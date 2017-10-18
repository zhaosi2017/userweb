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
use frontend\models\MessageCatch\MessageCatch;
use frontend\models\Report\ReportCall;
use frontend\models\Channel;
use frontend\models\ErrCode;
use frontend\models\Friends\Friends;
use frontend\models\User;
use common\services\ttsService\CallService;
use frontend\models\CallRecord\CallRecord;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use WebSocket\Client;
use yii\db\Exception;
use Yii;

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
     * @var socket 服务
     */
    //public $socket_server;

    public $channel;


    private $socket;

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

    /***
     * @param $string
     * @param string $code
     * @return bool
     * 返回电话消息给app
     */
    public function _sendText($string , $code = "0000"){

        $this->result['message'] = $string;
        $this->result['code']    = $code;
        $this->_union_check($this->from_user->account , $this->from_user->token);
        if(empty($this->socket)){
            $this->socket = new WebSocket();
        }
        $b = $this->socket->connect('127.0.0.1' , '9803');
        if(!$b){ // 链接失败
            return false;
        }
        $data = [
            'action'=>2,
            'text'=>json_encode($this->result , JSON_UNESCAPED_UNICODE),
            'uCode'=>$this->from_user->account
        ];
        $b = $this->socket->send_data(json_encode($data ,JSON_UNESCAPED_UNICODE));
        if($b){ //发送消息失败
            return false;
        }
        $data = $this->socket->recv_data();
        $json = json_decode($data);
        return $json->status;
    }

    public function sendText($string , $code = "0000"){
        $this->result['message'] = $string;
        $this->result['code']    = $code;
        $text = json_encode( $this->result ,JSON_UNESCAPED_UNICODE);
        $json = ['uCode'=>$this->from_user->account , 'message'=>$text];
        $body =json_encode( $json );
        $this->_union_check($this->from_user->account , $this->from_user->token);
        $request = new Request('GET' ,
                                '127.0.0.1:9803?json='.$body);
        $client  = new \GuzzleHttp\Client();
        try{
            $response = $client->send($request , ['timeout'=>10]);
        }catch (\Exception $e){
            $response = new Response(500);
        }catch(\Error $e){
            $response = new Response(500);
        }
        file_put_contents('/tmp/test_123.log' ,$code.'******'.var_export($response , true).PHP_EOL  , 8);
        if($response->getStatusCode() == 200){
            return true;
        }
        if($code == ErrCode::CALL_END){   //如果是结束消息发送失败  则将消息缓存起来 等待下次登陆时发送回去 其他消息则不管
            $model = new MessageCatch();
            $model->status = 0;
            $model->message = $text;
            $model->begin_time = time();
            $model->ucode = $this->from_user->account;
            $model->end_time = time() + 30*24*60*60;
            $model->save();
        }
        return false;
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
        $this->from_user = $user;
        $this->result['status'] = 0;
        $service = new  CallService();
        $service->app = $this;
        $service->group_id = $data->group_id;
        $this->_union_check($this->from_user->account , $this->from_user->token , true);
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
        $this->from_user   = $user;
        $to_user = User::findOne(['account'=>$data->account]);
        if(empty($to_user)){
            $this->sendText('不存在被叫的用户' , ErrCode::CODE_ERROR);
            return false;
        }
        $user_channels = explode(',' , $to_user->channel);
        if(!key_exists($data->call_type , CallRecord::$type_map)){
            $this->sendText('呼叫类型错误' , ErrCode::CODE_ERROR);
            return false;
        }
        $channel = Channel::findOne($data->channel_id);
        if(empty($channel)){
            $this->sendText('呼叫渠道错误',ErrCode::CODE_ERROR);
            return false;
        }
        $this->to_user     = $to_user;
        $this->channel     = $channel;
        $this->result['status'] = 0;
        if(!$this->_union_call( $this->from_user->account , $this->from_user->token)){
            $this->sendText('只能发起一个电话',ErrCode::CODE_ERROR);
            return false;
        }
        return true;
    }

    /**
     * @param $ucode
     * @param $token
     * @return bool
     * 一个用户在同一时间只能发起一起次呼叫
     */
    private function _union_call($ucode , $token ){

        $key = $ucode.'-'.$token;
        if(Yii::$app->redis->exists($key)){
            return false;
        }
        Yii::$app->redis->hset($key , 'status' , 1);
        Yii::$app->redis->expire($key , 2*60);
        return true;
    }

    /**
     * @param $ucode
     * @param $token
     * 管理唯一呼叫标志
     */
    private function _union_check($ucode , $token , $flag = false){
        if(in_array($this->result['code'] ,[ErrCode::CALL_EXCEPTION , ErrCode::CALL_SUCCESS , ErrCode::CALL_END]) || $flag){
            $key = $ucode.'-'.$token;
            if(Yii::$app->redis->exists($key)){
                Yii::$app->redis->del($key);
            }
        }
        return true;
    }


    /**
     * 最近联系人记录
     */
    private function _linkFriend(){

        $friend_by_from = Friends::findOne(['friend_id'=>$this->from_user->id , 'user_id'=>$this->to_user->id]);   //被叫的好友（指主叫）
        $friend_by_to   = Friends::findOne(['friend_id'=>$this->to_user->id , 'user_id'=>$this->from_user->id]);      //主叫的好友（指被叫）
        if(empty($friend_by_to)){
            $type = ReportCall::CALL_TYPE_NOFRIEND;
        }else{
            $type = ReportCall::CALL_TYPE_FRIEND;
        }
        $model = ReportCall::findOne(['day'=>date('Y-m-d') , 'type'=>$type]);
        if(empty($model)){
            $model = new ReportCall();
            $model->day = date('Y-m-d');
            $model->number = 0;
            $model->type = $type;
        }
        $model->number++;
        $model->save();

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