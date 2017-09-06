<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/8/30
 * Time: 下午1:54
 * 优呼app  这个对象主要用于和app端的业务交互
 * 这里的app禁止使用匿名函数
 */
namespace  common\services\appService\apps;

use common\services\ttsService\thirds\Sinch;
use frontend\models\Channel;
use frontend\models\Friends\Friends;
use frontend\models\User;
use common\services\ttsService\CallService;
use frontend\models\CallRecord\CallRecord;
use WebSocket\Client;
use yii\db\Exception;

class callu {
    /**
     * @var User  用户
     */
    public $from_user;

    /**
     * @var 用户好友对象
     *
     */
    public $to_user;
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
        "message"=>"",
        "status"=> 0,
        "code"=>"0000"

    ];

    public function sendText($string){

        $this->result['message'] = $string;
        $client = new WebSocket();
        $client->connect('127.0.0.1' , '9803');
        $data = [
            'action'=>2,
            'app_fd'=>$this->socket_fd,
            'text'=>json_encode($this->result , JSON_UNESCAPED_UNICODE)
        ];
        $client->send_data(json_encode($data ,JSON_UNESCAPED_UNICODE));
        $data = $client->recv_data();
        $json = json_decode($data);
        return $json->status;

    }



    public function call($data){
        $data = json_decode($data);
        $user =  User::findOne(['token'=>$data->token]);   //身份校验
        if(empty($user)){
            $this->sendText('token错误');
            return ;
        }

        $to_user = User::findOne(['account'=>$data->account]);
        if(empty($to_user)){
            $this->sendText('不存在被叫的用户');
            return ;
        }
        if(!key_exists($data->call_type , CallRecord::$type_map)){
            $this->sendText('呼叫类型错误');
            return ;
        }
        $channel = Channel::findOne($data->channel_id);
        if(empty($channel)){
            $this->sendText('呼叫渠道错误');
            return ;
        }
        $this->from_user   = $user;
        $this->to_user = $to_user;

        $service = new  CallService(Sinch::class);
        $service->from_user = $user;
        $service->to_user   = $to_user;

        $friend = Friends::findOne(['user_id'=>$this->to_user->id , 'friend_id'=>$this->from_user->id]); //这里取的是主叫在被叫好友列表中的名字
        $name = empty($friend->remark)?$this->from_user->nickname:$friend->remark;
        if($data->call_type == CallRecord::CALLRECORD_TYPE_UNURGENT){
            $service->text  = $name.' 呼叫您上线 '.$channel->name;
        }else{
            $service->text  = '请转告 '.$this->to_user->nickname.' 上线'.$channel->name;
        }
        $service->app       = $this;
        $service->call_type = $data->call_type;

        $service->start_call();

    }





}