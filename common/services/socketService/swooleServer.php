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

class swooleServer{

    public $server;
    public $table;
    private static  $action_map =[
        1=>'打电话',
        2=>'电话回调通知',
        3=>'好友申请消息通知',
        0=>'连接',
        5=>'同意好友的添加请求',


    ];
    public function __construct(){
        if( $this->server == null){
            $this->server = new \swoole_websocket_server('0.0.0.0', 9803);
        }

        $this->server->set([
            'worker_num' => 2,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'log_file'=>'/tmp/swooles.log',
            'debug_mode'=> 1,
        ]);


        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('close' ,[$this , 'onClose']);
        $this->server->on('open' ,[$this , 'onOpen']);
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
        $data = json_decode($frame->data);
        if(empty($data) ){
            $server->push($frame->fd , json_encode(['json格式错误']));
            return;
        }
        if($data->action == 1 || $data->action == 2){    //电话相关
            $clerk = new ClerkCallu();
        }elseif (isset($data->action) && $data->action == 3){
            $clerk = new FriendRequetNotice();
        }elseif(isset($data->action) && $data->action === 0){
            $clerk = new UidConn();
        }elseif (isset($data->action) && $data->action == 5){
            $clerk = new AgreeFriendNotice();
        }else{
            $server->push($frame->fd , json_encode(['请求类型错误']));
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




}
