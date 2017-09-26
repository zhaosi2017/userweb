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
use frontend\models\ErrCode;
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

    public $channel;

    /**
     * @var array
     * 返回对象
     */
    public $result = [
        "data"=> [],
        "message"=>"",
        "status"=> 0,
        "code"=>"0000"

    ];

    public function sendText($string , $code = "0000"){

        $this->result['message'] = $string;
        $this->result['code']    = $code;
        $client = new WebSocket();
        $client->connect('103.235.171.146' , '9803');
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

        if(!$this->_checkData($data)){
            return false;
        }
        $data = json_decode($data);
        $service = new  CallService(Sinch::class);
        $service->from_user = $this->from_user;
        $service->to_user   = $this->to_user;
        $this->_linkFriend();
        $friend = Friends::findOne(['user_id'=>$this->to_user->id , 'friend_id'=>$this->from_user->id]); //这里取的是主叫在被叫好友列表中的名字
        $name = empty($friend->remark)?$this->from_user->nickname:$friend->remark;
        if($data->call_type == CallRecord::CALLRECORD_TYPE_UNURGENT){
            $service->text  = $name.' 呼叫您上线 '.$this->channel->name;
        }else{
            $service->text  = '请转告 '.$this->to_user->nickname.' 上线'.$this->channel->name;
        }
        $service->app       = $this;
        $service->call_type = $data->call_type;
        $service->group_id  = isset($data->group_id)?$data->group_id:'';
        $service->start_call();
    }

    /**
     * @param $data
     * 放弃呼叫
     */
    public function call_stop($data){
        $data = json_decode($data);
        $user =  User::findOne(['token'=>$data->token]);   //身份校验
        $this->result['status'] = 1;
        if(empty($user)){
            $this->sendText('token错误' , ErrCode::CODE_ERROR);
            return false;
        }
        $this->result['status'] = 0;
        $service = new  CallService();
        $service->app = $this;
        $service->group_id = $data->group_id;
        $service->stop_call();

    }
    /**
     * @param $data
     * 只有参数错误的时候 返回的状态是1 其他通知消息状体都是0
     * @return bool
     */
    private function _checkData($data){
        $data = json_decode($data);
        $user =  User::findOne(['token'=>$data->token]);   //身份校验
        $this->result['status'] = 1;
        if(empty($user)){
            $this->sendText('token错误' , ErrCode::CODE_ERROR);
            return false;
        }

        $to_user = User::findOne(['account'=>$data->account]);
        if(empty($to_user)){
            $this->sendText('不存在被叫的用户' , ErrCode::CODE_ERROR);
            return false;
        }
        if(!key_exists($data->call_type , CallRecord::$type_map)){
            $this->sendText('呼叫类型错误' , ErrCode::CODE_ERROR);
            return false;
        }
        $channel = Channel::findOne($data->channel_id);
        if(empty($channel)){
            $this->sendText('呼叫渠道错误',ErrCode::CODE_ERROR);
            return false;
        }
        $this->from_user   = $user;
        $this->to_user     = $to_user;
        $this->channel     = $channel;
        $this->result['status'] = 0;
        return true;
    }

    /**
     * 最近联系人记录
     */
    private function _linkFriend(){

        $friend_by_from = Friends::findOne(['friend_id'=>$this->from_user->id , 'user_id'=>$this->to_user->id]);   //被叫的好友（指主叫）
        $friend_by_to   = Friends::findOne(['friend_id'=>$this->to_user->id , 'user_id'=>$this->from_user->id]);      //主叫的好友（指被叫）

        if(!empty($friend_by_from)){
            $friend_by_from->link_time = time();
            $friend_by_from->save();
        }

        if(!empty($friend_by_to)){
            $friend_by_to->link_time = time();
            $friend_by_to->save();
        }

    }





}