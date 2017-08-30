<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/8/30
 * Time: 下午1:54
 * 优呼app  这个对象主要用于和app端的业务交互
 * 这里的app禁止使用匿名函数
 */
namespace  common\services\appService\apps;

use common\models\User;
use common\services\ttsService\CallService;
use frontend\models\CallRecord\CallRecord;

class callu {
    /**
     * @var User  用户
     */
    public $user;

    /**
     * @var 用户好友对象
     *
     */
    public $friend;
    /**
     * @var socket fd
     */
    public $socket_fd;
    /**
     * @var socket 服务
     */
    public $socket_server;


    private $result = [
        "data"=> [],
        "message"=>"修改昵称成功",
        "status"=> 0,
        "code"=>"0000"

    ];

    public function sendText($string){

        $this->result['message'] = $string;
        file_put_contents('/tmp/test-call.log' , $string , 8);
        return ;
        $this->socket_server->push($this->fd , json_encode($this->result , true));

    }



    public function call($data){

        $user = \frontend\models\User::findOne(['token'=>$data->token]);   //身份校验
        if(empty($user)){
            $this->socket_server->push($this->socket_fd , 'token错误');
            return ;
        }

        $to_user = \frontend\models\User::findOne(['account'=>$data->account]);
        if(empty($to_user)){
            $this->socket_server->push($this->socket_fd , '不存在被叫的用户');
            return ;
        }
        if(key_exists($data->call_type , CallRecord::$type_map)){
            $this->socket_server->push($this->socket_fd , '呼叫类型错误');
            return ;
        }
        $channel = Channel::findOne($data->channel_id);
        if(empty($channel)){
            $this->socket_server->push($this->socket_fd, '呼叫类型错误');
            return ;
        }


        $this->user = $user;
        $this->friend = $to_user;

        $service =new  CallService(Sinch::class);
        $service->from_user = $user;
        $service->to_user   = $to_user;
        if($data->call_type == CallRecord::CALLRECORD_TYPE_UNURGENT){
            $service->text  = $this->friend->nickname.' 呼叫您上线 '.$channel->name;
        }else{
            $service->text  = '请转告 '.$this->friend->nickname.' 上线'.$channel->name;
        }
        $service->app       = $this;
        $service->call_type = $data->call_type;

        $service->start_call();

    }





}