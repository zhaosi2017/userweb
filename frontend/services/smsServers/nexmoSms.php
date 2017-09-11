<?php
namespace frontend\services\smsServers;
use frontend\services\smsServers\AbstractSmsServic;
use Yii;
class nexmoSms extends AbstractSmsServic
{
    private $api_key;
    private $api_secret;
//    private $server;
    private $api_number ;

    public function __construct($api_key,$api_secret, $api_number)
    {

        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->api_number = $api_number;

    }


    public function sendSms($number, $msg)
    {
        $url = 'https://rest.nexmo.com/sms/json?' . http_build_query(
                [
                    'api_key' => $this->api_key, // Yii::$app->params['nexmo_api_key'],
                    'api_secret' => $this->api_secret , // Yii::$app->params['nexmo_api_secret'],
                    'to' => $number,
                    'from' => $this->api_number,//Yii::$app->params['nexmo_account_number'],
                    'text' => $msg
                ]
            );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $response = json_decode($response, true);

        if(isset($response['messages'][0]['status'] ) && $response['messages'][0]['status'] ==0)
        {
            return true;
        }
        if(isset($response['messages'][0]['status'] ) && $response['messages'][0]['status'] ==9)
        {
            //nexmo费用或余额不足,发送消息到邮箱提示
            $mail= Yii::$app->mailer->compose();
            $mail->setTo(Yii::$app->params['adminEmail']);
            $mail->setSubject("nexmo平台余额不足");
            $mail->setTextBody('nexmo平台余额不足,影响短信发送，请到nexmo平台充值或者切换到其他短信平台 ');   //发布纯文字文本
            $mail->send();
        }
        return false;
    }
}
