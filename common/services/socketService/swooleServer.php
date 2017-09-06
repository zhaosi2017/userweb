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
use common\services\ttsService\CallService;
use frontend\models\CallRecord\CallRecord;
use frontend\models\Channel;

class swooleServer{

    public $server;

    private static  $action_map =[
        1=>'打电话',
        2=>'电话回调通知',
        3=>'好友申请消息通知'

    ];
    public function __construct(){
        if( $this->server == null){
            $this->server = new \swoole_websocket_server('0.0.0.0', 9803);
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
        file_put_contents('/tmp/test-call.log' , var_export($frame->data).PHP_EOL , 8);
        $data = json_decode($frame->data);
        if(empty($data) ){
            $server->push($frame->fd , json_encode(['json格式错误']));
            return;
        }
        if($data->action == 1 || $data->action == 2){    //电话相关
            $clerk = new ClerkCallu();
        }elseif ($data->action == 3){

        }else{
            $server->push($frame->fd , json_encode(['请求类型错误']));
            return ;
        }
        $clerk->stratClerk($server,  $frame , $data);
    }


    public function onClose( $server,  $fd){



    }




}
