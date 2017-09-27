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

    public  function sendMessage($number)
    {
        if($number){

            $switch = Yii::$app->params['sms_send_enable'];
            $redis = Yii::$app->redis;
            $verifyCode = '';
            if($redis->exists($number))
            {
                $verifyCode = $redis->get($number);

                if($verifyCode && $switch===false)
                {
                    return $this->jsonResponse(['code'=>$verifyCode],'操作成功',0,ErrCode::SUCCESS);
                }
                if($verifyCode)
                {
                    $expire = isset(Yii::$app->params['redis_expire_time']) ? Yii::$app->params['redis_expire_time'] : 120;
                    $redis->EXPIRE($number,$expire);
                }

            }

            if(empty($verifyCode))
            {
                $verifyCode = self::makeVerifyCode();
                $expire = isset(Yii::$app->params['redis_expire_time']) ? Yii::$app->params['redis_expire_time'] : 120;
                $redis->setex($number,$expire,$verifyCode);
            }


            if($switch === false){
                return $this->jsonResponse(['code'=>$verifyCode],'操作成功',0,ErrCode::SUCCESS);
            }
            $msg = '您注册客优的验证码为：'.$verifyCode.'，有效期5分钟。';
            try {
                $_sms = Yii::$app->sms;
                $res = $_sms->sendSms($number, $msg);
                if ($res === true) {
                    return $this->jsonResponse(['code' => $verifyCode], '操作成功', 0, ErrCode::SUCCESS);
                } else {
                    $redis->exists($number) && $redis->del($number);
                    return $this->jsonResponse([], '网络错误', 1, ErrCode::NETWORK_OR_PHONE_ERROR);
                }
            }catch (\Exception $e)
            {
                $text = '';
                if($e->getCode() == '21211'){
                    $text = $number.'不是有效的电话号码';
                }else{
                    $text = $e->getMessage();
                }
                $target =  ($res = Yii::$app->user->identity) ? $res->language : 'zh-CN';
                $translate = new  TranslateService($text,$target);
                $_message = $translate->translate();
                $redis->del($number);
                return $this->jsonResponse([], $_message, 1, ErrCode::NETWORK_OR_PHONE_ERROR);
            }
        }else{
            return $this->jsonResponse([],'国码,号码不能为空',1,ErrCode::COUNTRY_CODE_OR_PHONE_EMPTY);
        }
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
