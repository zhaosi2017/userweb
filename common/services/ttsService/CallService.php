<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/8/29
 * Time: 下午4:24
 *
 * 这里完成呼叫链的业务逻辑
 */
namespace  common\services\ttsService;

use frontend\models\CallRecord\CallRecord;
use frontend\models\User;
use frontend\models\UserPhone;
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


    public function __construct($className = null){

        if(empty($className))  $className = __NAMESPACE__.'\\'.Yii::$app->params['tts_third'];
        if(is_subclass_of($className , AbstruactThird::class) ){
            $this->third = new $className();
        }
    }

    /**
     * 发起呼叫
     */
    public function start_call(){

        if(empty($this->from_user) || empty($this->to_user) || empty( $this->third)){
             throw  new \Exception('参数错误');
        }
        if(empty($this->call_type)) $this->call_type = CallRecord::CALLRECORD_TYPE_UNURGENT;  //默认为正常呼叫

        if(!$this->_check()){
            $this->app->sendtext($this->message);
            return false;
        }

        $numbers = $this->_getToUserNumber();
        if(empty($numbers)){
            $this->app->sendtext("被叫没有可用的联系电话！");
            return false;
        }
        $this->app->sendtext("开始操作，请稍后！");
        $number = array_shift($numbers);
        $this->third->To        = $number;
        $this->third->From      = $this->_getFromUserNumber();
        $this->third->Text      = $this->text;
        $this->third->Language  = $this->to_user->language;
        $this->third->Loop      = 2;//Yii::$apps->params['tts_loop'];
        $this->app->sendtext("正在尝试呼叫，请稍后");
        if(!$this->third->CallStart()){
            $this->app->sendtext("呼叫异常，请稍后再试！");
            return false;
        }
        $this->_catch($numbers);                  //呼叫开始就不能受发起方控制 直至呼叫完成
        return true;
    }

    /**
     * @param array $event_data
     * @return mixed
     * 回调事件
     */
    public function Event(Array $event_data){

        $result = $this->third->Event($event_data);
        $catch_key =  get_class($this->third).$this->third->callId;
        $catch = $this->_redisGetVByK($catch_key);
        $this->app = unserialize($catch['apps']);
        $this->to_user      = unserialize($catch['to_user']);
        $this->from_user    = unserialize($catch['from_user']);
        $this->call_type    = $catch['call_type'];

        if(empty($catch)){
            $this->app->sendtext("呼叫异常，请稍后再试！");
        }

        if(!$this->_Event_ActionResult()){
            if(!$this->_call($catch)){
                $this->app->sendtext("呼叫异常，请稍后再试！");
            }
        }
        return $result;
    }



    private function _redisGetVByK($cacheKey){

        $cache_keys = Yii::$app->redis->hkeys($cacheKey);
        $catch_vals = Yii::$app->redis->hvals($cacheKey);
        //Yii::$app->redis->del($cacheKey);
        return array_combine($cache_keys , $catch_vals);

    }
    /**
     * 处理回调 结果
     */
    private function _Event_ActionResult(){
        $this->_saveRecord();                   //保存通话记录
        switch ($this->third->Event_Status){
            case CallRecord::CALLRECORD_STATUS_SUCCESS:
                $this->app->sendText('呼叫成功！');
                return true;
                break;
            case CallRecord::CALLRECORD_STATUS_FILED:
                $this->app->sendText('呼叫失败，请稍后再试！');
                break;
            case CallRecord::CALLRECORD_STATUS_BUSY:
                $this->app->sendText('呼叫的用户忙！');
                break;
            case CallRecord::CALLRECORD_STATUS_NOANWSER:
                $this->app->sendText('呼叫用户暂时无人接听！');
                break;
            default:
                $this->app->sendText('呼叫失败，请稍后再试！');
                break;
        }
        return false;
    }

    /**
     * 开始呼叫
     */
    private function _call(Array $catch){


        $numbers = json_decode($catch['numbers']);
        if(empty($numbers)){
            $this->app->sendtext("呼叫完成！");
        }
        $this->third = unserialize($catch['third']);   //恢复为原始的呼叫状态
        $number = array_shift($numbers);
        $this->third->To   =  $number;
        $this->app->sendtext("正在尝试呼叫，请稍后");
        if(!$this->third->CallStart()){
            $this->app->sendtext("呼叫异常，请稍后再试！");
            return false;
        }
        $this->_catch($numbers);                  //呼叫开始就不能受发起方控制 直至呼叫完成

        return true;

    }

    /**
     * @param $numbers 号码集合
     * 创建呼叫队列
     */
    private function _catch($numbers){

        $call_key = get_class($this->third).$this->third->callId;

        Yii::$app->redis->hset($call_key , 'time', time());  //呼叫开始时间
        Yii::$app->redis->hset($call_key , 'numbers' , json_encode($numbers , true));
        Yii::$app->redis->hset($call_key , 'to_user' , serialize($this->to_user ));
        Yii::$app->redis->hset($call_key , 'from_user' , serialize($this->from_user));
        Yii::$app->redis->hset($call_key , 'third' , serialize($this->third));
        Yii::$app->redis->hset($call_key , 'apps' ,serialize($this->app) );
        Yii::$app->redis->hset($call_key , 'call_type' , $this->call_type);
        Yii::$app->redis->expire($call_key , 60*60 );

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
            $numbers = UserGrentPhone::findAll(['user_id'=>$this->to_user->id]);
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
     * 检测呼叫条件  设置相应的提示
     * 黑名单 1
     * 白名单 2
     * 呼叫限制设置 3
     * 主叫金额  4
     */
    private function _check(){


        return true;
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
        $model->save();

    }

}