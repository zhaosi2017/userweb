<?php
namespace frontend\services;

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

            $redis = Yii::$app->redis;
            $verifyCode = self::makeVerifyCode();
            $expire = isset(Yii::$app->params['redis_expire_time']) ? Yii::$app->params['redis_expire_time'] : 120;
            $redis->setex($number,$expire,$verifyCode);
            if(defined('YII_ENV') && YII_ENV =='dev'){
                return $this->jsonResponse(['code'=>$verifyCode],'操作成功',0,ErrCode::SUCCESS);
            }
            $url = 'https://rest.nexmo.com/sms/json?' . http_build_query(
                    [
                        'api_key' =>  Yii::$app->params['nexmo_api_key'],
                        'api_secret' => Yii::$app->params['nexmo_api_secret'],
                        'to' => $number,
                        'from' => Yii::$app->params['nexmo_account_number'],
                        'text' => 'Your Verification Code '.$verifyCode
                    ]
                );

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $response = json_decode($response, true);

            if(isset($response['messages'][0]['status'] ) && $response['messages'][0]['status'] ==0)
            {
                return $this->jsonResponse(['code'=>$verifyCode],'操作成功',0,ErrCode::SUCCESS);
            }else{
                return $this->jsonResponse([],'号码错误/网络错误',1,ErrCode::NETWORK_OR_PHONE_ERROR);
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
