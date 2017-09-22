<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/8/30
 * Time: 下午2:24
 */

namespace common\services\socketService;

error_reporting(E_ALL);
ini_set("display_errors" , "on");

use common\models\User;
use common\services\appService\apps\callu;
use common\services\socketService\Clerk\ClerkCallu;
use common\services\socketService\Clerk\FriendRequetNotice;
use common\services\socketService\Clerk\AgreeFriendNotice;
use common\services\ttsService\CallService;
use frontend\models\CallRecord\CallRecord;
use frontend\models\Channel;
use common\services\socketService\Clerk\UidConn;
use frontend\models\ErrCode;
use common\services\socketService\Clerk\HeartCheckNotice;
use common\services\socketService\Clerk\WebSocketReload;

class swooleServer{

    public $server;
    public $table;
    private static  $action_map =[
        1=>'打电话',
        2=>'电话回调通知',
        3=>'好友申请消息通知',
        0=>'连接',
        5=>'同意好友的添加请求',
        6=>'中断电话呼叫',
        7=>'心跳检查',
        8=>'重启worker进程',


    ];
    public function __construct(){
        if( $this->server == null){
            $this->server = new \swoole_websocket_server('0.0.0.0', 9803);
        }

        $this->server->set([
            'worker_num' => 3,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'log_file'=>'/tmp/swooles.log',
            'debug_mode'=> 1,
            'heartbeat_check_interval' => 60,//每60秒 遍历所有连接
            'heartbeat_idle_time' => 360,//与heartbeat_check_interval配合使用。表示连接最大允许空闲的时间（6分钟）
        ]);


        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('close' ,[$this , 'onClose']);
        $this->server->on('open' ,[$this , 'onOpen']);
        $this->server->on('connect' ,[$this , 'onConnect']);
        $this->server->start();
    }


    public function onOpen(\swoole_websocket_server $server)
    {

    }

    /**
     * @param swoole_server $server
     * @param swoole_websocket_frame $frame
     * 只是呼叫消息处理  如果需要增加其他业务 请将业务层封装
     */
    public function onMessage($server,  $frame){
        file_put_contents('/tmp/test-call.log' , var_export($frame->data , true).PHP_EOL , 8);
        $data = json_decode($frame->data);
        if(empty($data)  || !isset($data->action)){

            $result = [
                "data"=> [],
                "message"=>"json格式错误",
                "status"=> 1,
                "code"=>ErrCode::FAILURE
            ];

            $server->push($frame->fd , json_encode($result , JSON_UNESCAPED_UNICODE));
            return;
        }
        if($data->action == 1 || $data->action == 2 || $data->action == 6){    //电话相关
            $clerk = new ClerkCallu();
        }elseif (isset($data->action) && $data->action == 3){
            $clerk = new FriendRequetNotice();
        }elseif(isset($data->action) && $data->action === 0){
            $clerk = new UidConn();
        }elseif (isset($data->action) && $data->action == 5){
            $clerk = new AgreeFriendNotice();
        }elseif (isset($data->action) && $data->action == 7){
            $clerk = new HeartCheckNotice();
        }elseif (isset($data->action) && $data->action == 8)
        {
            $clerk = new WebSocketReload();
        }else{
            $result = [
                "data"=> [],
                "message"=>"请求类型错误",
                "status"=> 1,
                "code"=>ErrCode::FAILURE
            ];
            $server->push($frame->fd , json_encode($result ,JSON_UNESCAPED_UNICODE));
            return ;
        }
        $clerk->stratClerk($server,  $frame , $data);
    }


    public function onClose( $server,  $fd){

        $redis = \Yii::$app->redis;
        if($redis->exists(UidConn::UID_CONN_FD.$fd))
        {
            $uid = $redis->get(UidConn::UID_CONN_FD.$fd);
            $redis->del(UidConn::UID_CONN_ACCOUNT.$uid);
            $redis->del(UidConn::UID_CONN_FD.$fd);
        }
    }


    public function onConnect( $server,  $fd,  $from_id){



    }


}
