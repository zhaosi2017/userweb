<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/8/30
 * Time: 下午2:24
 */

namespace common\services\socketService;


use common\models\User;
use common\services\appService\apps\callu;
use common\services\ttsService\CallService;
use frontend\models\CallRecord\CallRecord;

class swoole{

    public $server;

    public function __construct(){

        $this->server = new Swoole_websocket_server('127.0.0.1', 9803);

        $this->server->set([
            'worker_num' => 2,
            'daemonize' => true,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1,
        ]);
        $this->server->on('Message', [$this, 'onMessage']);
        $this->server->on('colse' ,[$this , 'onColse']);

        $this->server->start();
    }

    /**
     * @param swoole_server $server
     * @param swoole_websocket_frame $frame
     * 只是呼叫消息处理  如果需要增加其他业务 请将业务层封装
     */
    public function onMessage(swoole_server $server, swoole_websocket_frame $frame){

        $data = $frame->data;
        $data = json_decode($data , true);
        if(empty($data)){
            $this->server->push($frame->fd , '数据错误');
        }

        $user = \frontend\models\User::findOne(['token'=>$data->token]);   //身份校验
        if(empty($user)){
            $this->server->push($frame->fd , 'token错误');
        }

        $to_user = \frontend\models\User::findOne(['account'=>$data->account]);
        if(empty($to_user)){
            $this->server->push($frame->fd , '不存在被叫的用户');
        }
        if(key_exists($data->call_type , CallRecord::$type_map)){
            $this->server->push($frame->fd , '呼叫类型错误');
        }


        $app = new callu();
        $app->user   = $user;
        $app->friend = $to_user;
        $app->socket_fd = $frame->fd;
        $app->socket_server = $server;

        $service =new  CallService(Sinch::class);
        $service->from_user = $user;
        $service->to_user   = $to_user;
        $service->text      ="双流老妈秃头呼叫你上线";
        $service->app       = $app;
        $service->call_type = $data->call_type;

        $service->start_call();
    }


    public function onColse( swoole_server $server,  $fd){



    }




}
