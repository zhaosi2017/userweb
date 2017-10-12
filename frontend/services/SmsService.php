<?php
namespace frontend\services;

use frontend\services\Translates\TranslateService;
use yii;
use frontend\models\FActiveRecord;
use frontend\models\ErrCode;

/**短信类
 * Class SmsService
 * @package frontend\services
 */
class SmsService
{

    const EXPIRE_NUMBER = '_expire_number';
    const EXPIRE_TIME_LIMIT = 300;//5分钟
    const LIMIT_NUM = 5;//300秒内 最多请求5次


    public  function sendMessage($number)
    {
        if($number){


            $switch = Yii::$app->params['sms_send_enable'];
            $redis = Yii::$app->redis;
            $verifyCode = $this->getVerifyCode($number);
            if($this->rateLimit($number))
            {
                return $this->jsonResponse([],'操作太频繁，每5分钟最多发送5次',1,ErrCode::SUCCESS);
            }

//            if($switch === false){
//                return $this->jsonResponse(['code'=>$verifyCode],'操作成功',0,ErrCode::SUCCESS);
//            }
//            $msg =  '您注册客优的验证码为:'.$verifyCode.',有效期5分钟.';
            $msg ='You are registering callu for the verification code:'.$verifyCode.',Valid for 5 minutes.';

            try {
                $_sms = Yii::$app->sms;
                $res = $_sms->sendSms($number, $msg);
                if ($res === true) {
                    return $this->jsonResponse(['code' => $verifyCode], '操作成功', 0, ErrCode::SUCCESS);
                } else {
                    $this->delCode($number);
                    return $this->jsonResponse([], '网络错误', 1, ErrCode::NETWORK_OR_PHONE_ERROR);
                }
            }catch (\Exception $e)
            {
                $text = '';
                if($e->getCode() == '21211' || $e->getCode() =='21614'){
                    $text = $number.'不是有效的电话号码';
                }else{
                    $text = $e->getMessage();
                }
                $target =  ($res = Yii::$app->user->identity) ? $res->language : 'zh-CN';
                $translate = new  TranslateService($text,$target);
                $_message = $translate->translate();
                $this->delCode($number);
                return $this->jsonResponse([], $_message, 1, ErrCode::NETWORK_OR_PHONE_ERROR);
            }
        }else{
            return $this->jsonResponse([],'国码,号码不能为空',1,ErrCode::COUNTRY_CODE_OR_PHONE_EMPTY);
        }
    }

    /**获取验证码，并记录到redis
     * @param $number
     * @return int|string
     */
    public function getVerifyCode($number)
    {

        $redis = Yii::$app->redis;
        $verifyCode = '';
        $expire = isset(Yii::$app->params['redis_expire_time']) ? Yii::$app->params['redis_expire_time'] : 120;

        if($redis->HEXISTS($number,'code'))
        {
            $verifyCode = $redis->hget($number,'code');
            if($verifyCode)
            {
                $redis->HINCRBY($number,'num','1');
                if(!$redis->exists($number.self::EXPIRE_NUMBER)) //如果设置的过期验证码 不存在了，则重新设置过期验证码
                {
                    $_time = $redis->ttl($number);
                    $redis->SETEX($number.self::EXPIRE_NUMBER,self::EXPIRE_TIME_LIMIT - $_time, $verifyCode); ;
                }
            }

        }

        if(empty($verifyCode))
        {
            $verifyCode = self::makeVerifyCode();
            $expire = isset(Yii::$app->params['redis_expire_time']) ? Yii::$app->params['redis_expire_time'] : 120;
            $redis->HSETNX($number,'code',$verifyCode);//验证码
            $redis->HINCRBY($number,'num','1');//请求次数加一
            $redis->expire($number,$expire);//五分钟过期时间
            if(!$redis->exists($number.self::EXPIRE_NUMBER)) //如果过期验证码没有设置，则设置过期验证码 （5+5）=10分钟
            {
                $redis->SETEX($number.self::EXPIRE_NUMBER,$expire + self::EXPIRE_TIME_LIMIT, $verifyCode);//设置过期验证码（有效期10）
            }

        }
        return $verifyCode;

    }

    /**
     * 速率现在5分钟内最多发送5次
     */
    public function rateLimit($number)
    {
        $redis = Yii::$app->redis;
        if($redis->HEXISTS($number,'num'))
        {
            $num = $redis->hget($number,'num');
            if($num > self::LIMIT_NUM)
            {
                return true;
            }
            return false;
        }else{
            return false;
        }

    }

    /**检查验证码是否有效
     * @param $number
     * @param $code
     */
    public function checkSms($number,$code)
    {
        $redis = Yii::$app->redis;
        $_code = $redis->hget($number,'code');//验证码
        $_expire_code = $redis->get($number.self::EXPIRE_NUMBER);//过期验证码


        if($_expire_code && $code == $_expire_code && $_expire_code != $_code)
        {
            return '验证码已过有效期，请重新获取。';
        }
        if(empty($_code) || $_code != $code)
        {
            return '验证码错误';
        }

        return false;


    }

    /**删除redids 当中的验证码,并设置过期验证码
     * @param $number
     * @return bool
     */
    public function delCode($number)
    {
        $redis = Yii::$app->redis;
        $_code = $redis->hget($number,'code');//验证码
        $_expire_code = $redis->get($number.self::EXPIRE_NUMBER);//过期验证码

        $redis->exists($number) && $redis->del($number);
        if($_code  && $_expire_code != $_code)
        {
            $expire = isset(Yii::$app->params['redis_expire_time']) ? Yii::$app->params['redis_expire_time'] : 120;
            $redis->SETEX($number.self::EXPIRE_NUMBER, $expire, $_code);//设置过期验证码（有效期5分钟）
        }
        return true;
    }

    public function jsonResponse($data,$message,$status = 0,$code)
    {
        return ['data'=>$data, 'message'=>$message, 'status'=>$status, 'code'=>$code];
    }

    public static function makeVerifyCode()
    {
        return rand(1000,9999);
    }
}
