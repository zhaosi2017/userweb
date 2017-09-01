<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/8/30
 * Time: 下午2:24
 */

namespace common\services\socketService;

error_reporting(E_ALL);
ini_set("display_errors" , "on");

use common\models\User;
use common\services\appService\apps\callu;
use common\services\ttsService\CallService;
use frontend\models\CallRecord\CallRecord;
use frontend\models\Channel;

class swooleCallu{

    public $server;

    public function __construct(){
        if( $this->server == null){
            $this->server = new \swoole_websocket_server('127.0.0.1', 9803);
        }
        $this->server->set([
            'worker_num' => 2,
            'daemonize' => true,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1,
        ]);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('close' ,[$this , 'onClose']);
        $this->server->start();
    }

    /**
     * @param swoole_server $server
     * @param swoole_websocket_frame $frame
     * 只是呼叫消息处理  如果需要增加其他业务 请将业务层封装
     */
    public function onMessage($server,  $frame){

        $app = new callu();
        $app->socket_fd = $frame->fd;
        $app->socket_server = $server;
        $app->call($frame->data);


    }


    public function onClose( $server,  $fd){



    }




}
