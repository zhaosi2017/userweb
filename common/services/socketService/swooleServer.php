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
use common\services\socketService\Clerk\CloseWebSocketMaster;
use common\services\socketService\Clerk\RefuseFriendNotice;
use Yii;

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
        8=>'重启websocket的worker进程',
        9=>'终止websocket的master进程和worker进程',
        10=>'拒绝好友',


    ];
    public function __construct(){
        if( $this->server == null){
            $this->server = new \swoole_websocket_server('0.0.0.0', 9803);
        }

        $this->server->set([
            'worker_num' => 4,
            'daemonize' => true,
            'max_request' => 10000,
            'dispatch_mode' => 5,
            'log_file'=>'/tmp/swooles.log',
            'debug_mode'=> 1,
            'heartbeat_check_interval' => 350,//每180秒 遍历所有连接
            'heartbeat_idle_time' => 360,//与heartbeat_check_interval配合使用。表示连接最大允许空闲的时间（6分钟）
            'task_worker_num'=>10,
        ]);


        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('close' ,[$this , 'onClose']);
        $this->server->on('open' ,[$this , 'onOpen']);
        $this->server->on('connect' ,[$this , 'onConnect']);
        $this->server->on('task' , [$this, 'onTask']);
        $this->server->on('finish' , [$this, 'onFinish']);
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
        file_put_contents('/tmp/test-call'.date('Y-m-d').'.log' , date('Y-m-d H:i:s').var_export($frame->data , true).PHP_EOL , 8);
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
            $temp  = ['frame'=>$frame ];
            if($data->action == 1){
                $task_id = $server->task($temp);  //投递任务
                return;
            }
            $clerk = new ClerkCallu();
            $clerk->fd = $frame->fd;
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
        }elseif (isset($data->action) && $data->action == 9)
        {
            $clerk = new CloseWebSocketMaster();
        }elseif (isset($data->action) && $data->action == 10)
        {
            $clerk = new RefuseFriendNotice();
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
        $result = [
            "data"=> [],
            "message"=>"请求时失败，请稍后重试！",
            "status"=> 1,
            "code"=>ErrCode::FAILURE
        ];
        try{
            $clerk->stratClerk($server,  $frame , $data);
        }catch (\yii\base\ErrorException $exception){
            Yii::$app->db->close();
            Yii::$app->db->open();
            $server->push($frame->fd , json_encode($result ,JSON_UNESCAPED_UNICODE));
        }catch(\Error $error){
            $server->push($frame->fd , json_encode($result ,JSON_UNESCAPED_UNICODE));
        }
        return true;
    }


    public function onClose( $server,  $fd){

    }


    public function onConnect( $server,  $fd,  $from_id){



    }



    public function onTask($server, $task_id, $from_id, $data){
        $frame = $data['frame'];
        $data = json_decode($frame->data);
        $clerk = new ClerkCallu();
        $clerk->fd = $frame->fd;
        return   $clerk->stratClerk($server,  $frame , $data);
    }

    public function onFinish($server, $task_id, $data){

        return true;
    }

}
