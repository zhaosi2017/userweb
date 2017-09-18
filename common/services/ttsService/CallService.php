<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/8/29
 * Time: 下午4:24
 *
 * 这里完成呼叫链的业务逻辑
 */
namespace  common\services\ttsService;

use frontend\models\BlackLists\BlackList;
use frontend\models\CallRecord\CallRecord;
use frontend\models\ErrCode;
use frontend\models\UrgentContact;
use frontend\models\User;
use frontend\models\UserPhone;
use frontend\models\WhiteLists\WhiteList;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use yii\base\Object;
use Yii;

class CallService {

    /**
     * @var  AbstruactThird  呼叫的渠道
     */
    private $third;

    /**
     * @var App    发起呼叫的app对象
     */
    public $app;
    /**
     * @var User 呼叫发起者
     */
    public $from_user;

    /**
     * @var User 被叫
     */
    public $to_user;

    /**
     * @var   CallRecord::$type_map -> each 呼叫类型
     */
    public $call_type;

    /**
     * @var string 反馈给用户的消息
     */
    public $message;
    /**
     * @var string 电话的内容
     */
    public $text;

    /**
     * @var int 每次呼叫的id 一次呼叫多条记录拥有统一id
     */
    public $group_id;

    private static $call_type_map = [
        CallRecord::CALLRECORD_TYPE_URGENT =>'个紧急联系人',
        CallRecord::CALLRECORD_TYPE_UNURGENT=>'部联系电话'
    ];


    public function __construct($className = null){

        if(empty($className))  $className = __NAMESPACE__.'\\'.Yii::$app->params['tts_third'];
        if(is_subclass_of($className , AbstruactThird::class) ){
            $this->third = new $className();
        }
    }

    /**
     * 检测呼叫条件  设置相应的提示
     * 1 话费
     * 2 黑名单
     * 3 白名单
     */
    public function check(){

        if($this->from_user->balance <= 0){   //话费检测  暂时无用
           // return false;
        }
        $black  = BlackList::findOne(['uid'=>$this->to_user->id , 'black_uid'=>$this->from_user->id]);
        if(!empty($black)){
            return '对方不方便接听，无法呼叫';
        }
        if($this->to_user->whitelist_switch  == User::WHITE_SWITCH_OFF){
            $white = WhiteList::findOne(['uid'=>$this->to_user->id , 'white_uid'=>$this->from_user->id]);
            if(empty($white)){
                return '对方开启白名单模式，您不再其中';
            }
        }
        return true;
    }

    /**
     * 发起呼叫
     */
    public function start_call(){

        if(empty($this->from_user) || empty($this->to_user) || empty( $this->third)){
             throw  new \Exception('参数错误');
        }
        if(empty($this->call_type)) $this->call_type = CallRecord::CALLRECORD_TYPE_UNURGENT;  //默认为正常呼叫

        $numbers = $this->_getToUserNumber();
        if(empty($numbers)){
            $this->app->sendtext("被叫没有可用的".self::$call_type_map[$this->call_type]."！", ErrCode::CALL_EXCEPTION );
            return false;
        }
        $count = count($numbers);
        $number = array_shift($numbers);
        $this->third->To        = $number;
        $this->third->From      = $this->_getFromUserNumber();
        $this->third->Text      = $this->text;
        $this->third->Language  = $this->to_user->language;
        $this->third->Loop      = Yii::$app->params['tts_loop'];
        if($this->call_type == CallRecord::CALLRECORD_TYPE_URGENT ){
            $tmp = $this->_redisGetVByK($this->group_id , false);
            if(empty($this->group_id) || empty($tmp)){
                $this->app->sendtext("不能单独呼叫紧急联系人" , ErrCode::CALL_EXCEPTION);
                return false;
            }
        }else{
            $this->group_id = $this->from_user->id.'-'.time().'-'.rand(1000, 9999);
            $this->app->result['data']['group_id'] = $this->group_id;
            $this->app->result['data']['call_type'] = $this->call_type;
            $this->app->sendtext($this->group_id , ErrCode::CALL_MESSAGE_GROUP);   //给客户端一个打电话的唯一标志 用于中断呼叫
        }
        $this->app->result['data']['group_id'] = $this->group_id;
        $this->app->result['data']['call_type'] = $this->call_type;
        $this->app->sendtext('正在拨打第1'.self::$call_type_map[$this->call_type].'（共'.$count.'部)' , ErrCode::CALL_MESSAGE);
        if(!$this->third->CallStart()){
            $this->app->sendtext("呼叫异常，请稍后再试！" , ErrCode::CALL_EXCEPTION);
            return false;
        }

        $this->_catch($numbers , 1);                  //呼叫开始就不能受发起方控制 直至呼叫完成
        return true;
    }

    /**
     * @param array $event_data
     * @return mixed
     * 回调事件
     */
    public function Event(Array $event_data){

        $result = $this->third->Event($event_data);   //反馈第三方的消息 和业务处理无关 用于输出
        $catch_key =  get_class($this->third).$this->third->callId;
        $catch = $this->_redisGetVByK($catch_key);
        $this->app = unserialize($catch['apps']);
        $this->to_user      = unserialize($catch['to_user']);
        $this->from_user    = unserialize($catch['from_user']);
        $this->call_type    = $catch['call_type'];
        $this->group_id     = $catch['group_id'];
        if(empty($catch)){
            $this->app->sendtext("呼叫异常，请稍后再试！" , ErrCode::CALL_EXCEPTION);
            $this->_redisGetVByK($this->group_id);
            return $result;
        }

        if(!$this->_Event_ActionResult()){
            $numbers = json_decode($catch['numbers']);
            if(empty($numbers)){
                $this->app->sendtext("呼叫结束" , ErrCode::CALL_END);
                if($this->call_type == CallRecord::CALLRECORD_TYPE_URGENT){  //紧急联系人呼叫结束时 删除这个group呼叫id
                    $this->_redisGetVByK($this->group_id ,true);
                }
                return  $result;
            }
            $tmp = $this->_redisGetVByK($this->group_id , false);
            if(!empty($tmp) && !$this->_call($catch)){                  //前提是呼叫流程没有被用户强制中断
                $this->app->sendtext("呼叫异常，请稍后再试！",ErrCode::CALL_EXCEPTION);
                $this->_redisGetVByK($this->group_id);
            }
        }
        return $result;
    }

    /**
     * 放弃呼叫
     */
    public function stop_call(){
        if(!empty($this->group_id)){
            Yii::$app->redis->del($this->group_id);
        }
        $this->app->sendtext("呼叫放弃成功!",ErrCode::CALL_MESSAGE);
    }


    private function _redisGetVByK($cacheKey , $flag = true){

        $cache_keys = Yii::$app->redis->hkeys($cacheKey);
        $catch_vals = Yii::$app->redis->hvals($cacheKey);
        if($flag){
            Yii::$app->redis->del($cacheKey);
        }

        return array_combine($cache_keys , $catch_vals);

    }
    /**
     * 处理回调 结果
     */
    private function _Event_ActionResult(){
        $this->_saveRecord();                   //保存通话记录
        switch ($this->third->Event_Status){
            case CallRecord::CALLRECORD_STATUS_SUCCESS:
                $this->app->sendText('呼叫成功！' , ErrCode::CALL_SUCCESS);
                $this->_redisGetVByK($this->group_id ,true);
                return true;
                break;
            case CallRecord::CALLRECORD_STATUS_FILED:
                $this->app->sendText('呼叫失败，请稍后再试！', ErrCode::CALL_FAIL);
                break;
            case CallRecord::CALLRECORD_STATUS_BUSY:
                $this->app->sendText('呼叫的用户忙！',ErrCode::CALL_FAIL);
                break;
            case CallRecord::CALLRECORD_STATUS_NOANWSER:
                $this->app->sendText('呼叫用户暂时无人接听！',ErrCode::CALL_FAIL);
                break;
            default:
                $this->app->sendText('呼叫失败，请稍后再试！',ErrCode::CALL_FAIL);
                break;
        }
        return false;
    }

    /**
     * 开始呼叫
     */
    private function _call(Array $catch){
        $numbers = json_decode($catch['numbers']);
        $this->third = unserialize($catch['third']);   //恢复为原始的呼叫状态
        $number = array_shift($numbers);
        $this->third->To   =  $number;
        $this->app->sendtext('正在拨打第'.($catch['serial']+1).self::$call_type_map[$this->call_type].'（共'.$catch['count'].'部)' , ErrCode::CALL_MESSAGE);
        if(!$this->third->CallStart()){
            $this->app->sendtext("呼叫异常，请稍后再试！" , ErrCode::CALL_EXCEPTION);
            $this->_redisGetVByK($this->group_id);
            return false;
        }
        $this->_catch($numbers , ($catch['serial']+1));                  //呼叫开始就不能受发起方控制 直至呼叫完成
        return true;

    }

    /**
     * @param $numbers 号码集合
     * 创建呼叫队列
     */
    private function _catch($numbers, $Serial = 1){

        $call_key = get_class($this->third).$this->third->callId;

        Yii::$app->redis->hset($call_key , 'time', time());  //呼叫开始时间
        Yii::$app->redis->hset($call_key , 'numbers' , json_encode($numbers , true));
        Yii::$app->redis->hset($call_key , 'to_user' , serialize($this->to_user ));
        Yii::$app->redis->hset($call_key , 'from_user' , serialize($this->from_user));
        Yii::$app->redis->hset($call_key , 'third' , serialize($this->third));
        Yii::$app->redis->hset($call_key , 'apps' ,serialize($this->app) );
        Yii::$app->redis->hset($call_key , 'call_type' , $this->call_type);
        Yii::$app->redis->hset($call_key , 'serial' , $Serial);
        Yii::$app->redis->hset($call_key , 'count' ,(count($numbers) + $Serial) );
        Yii::$app->redis->hset($call_key , 'group_id' ,$this->group_id );

        Yii::$app->redis->expire($call_key , 60*60 );

        Yii::$app->redis->hset($this->group_id , 'call_type' ,$this->call_type );   //这个记录的是同一次呼叫 的呼叫类型
        Yii::$app->redis->expire($this->group_id , 60*60 );
    }
    /**
     * 获取被叫的电话号码
     */
    private function _getToUserNumber(){
        $result = [];
        if($this->call_type == CallRecord::CALLRECORD_TYPE_UNURGENT ){
            $numbers = UserPhone::findAll(['user_id'=>$this->to_user->id]);
            foreach($numbers as $number){
                array_push($result ,$number->phone_country_code.$number->user_phone_number);
            }
        }else{
            $numbers = UrgentContact::findAll(['user_id'=>$this->to_user->id]);
            foreach($numbers as $number){
                array_push($result ,$number->contact_country_code.$number->contact_phone_number);
            }
        }
        return $result;
    }

    /**
     * 获取主叫的电话号码
     */
    private  function _getFromUserNumber(){
        return "12345678";
    }


    /**
     * 保存通话记录
     */
    private function _saveRecord(){

        $model = new CallRecord();
        $model->from_user_id = $this->from_user->id;
        $model->call_id      = $this->third->callId;
        $model->to_user_id   = $this->to_user->id;
        $model->time         = time();
        $model->text         = $this->third->Text;
        $model->duration     = 0;                               //通话时间 暂时为0
        $model->amount       = 0;                               //通话费用
        $model->status       = $this->third->Event_Status;
        $model->call_type    = $this->call_type;
        $model->from_number  = $this->third->From;
        $model->to_number    = $this->third->To;
        $model->third        = get_class($this->third);
        $model->group_id     = $this->group_id;
        $model->save();

    }

}