<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/4
 * Time: 上午10:10
 */
namespace common\services\socketService\Clerk;

use common\services\appService\apps\callu;
use common\services\socketService\AbstruactClerk;

class ClerkCallu extends  AbstruactClerk{
    /**
     * @var callu
     */
    public $app;

    public $server;     //当前服务
    public $fd;         //当前fd

    private function _call($server,  $frame){

        $this->app = new callu();
        $this->app->socket_fd = $frame->fd;
        $this->app->socket_server = $server;
        $this->app->call($frame->data);

    }

    public function stratClerk($server,  $frame , $data){

        if($data->action == 1){   //打电话

            $this->_call($server,  $frame);

        }else{                  //电话消息通知  需要通知另外一个fd所以这里做一个消息转发
            $resl = $server->push($data->app_fd , $data->text);
            $data_ = ['status'=>$resl];
            $server->push($frame->fd , json_encode($data_ , true));
        }


    }





}