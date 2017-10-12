<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/4
 * Time: 上午10:10
 */
namespace common\services\socketService\Clerk;

use common\models\User;
use common\services\appService\apps\callu;
use common\services\socketService\AbstruactClerk;
use frontend\models\ErrCode;
use Yii;

class ClerkCallu extends  AbstruactClerk{
    /**
     * @var callu
     */
    public $app;

    public $server;     //当前服务
    public $fd;         //当前fd

    private function _call($server,  $frame){

        $this->app = new callu();
        $this->app->call($frame->data);

    }

    public function stratClerk($server,  $frame , $data){


            if($data->action == 1){                     //打电话
                $info = $server->connection_info($frame->fd);
                if(!isset($info['uid']) || empty($info['uid']) ){
                    $this->result['message'] = '请登陆！';
                    $server->push($frame->fd , json_encode( $this->result , JSON_UNESCAPED_UNICODE));
                    $server->close($frame->fd);
                    return false;
                }
                $this->_call($server,  $frame);

            }elseif($data->action == 6){

                $this->_stop_call($server,  $frame);
                return true;
            } else{                  //电话消息通知
               $this->sendMessage($server, $data->uCode , $data->text , self::TCP_MESSAGE_CATCH_NO);
            }
        return true;
    }


    private function _stop_call($server,  $frame){

        $this->app = new callu();
        $this->app->socket_fd = $frame->fd;
        $this->app->socket_server = $server;
        $this->app->call_stop($frame->data);
    }



}