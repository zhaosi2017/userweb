<?php
namespace frontend\services\smsServers;
use frontend\models\ErrCode;
use yii\base\Component;
use frontend\services\smsServers\SmsException;
class smsConnection extends Component
{

    public $api_key ;
    public $api_secret;
    public $api_number;
    public $server_name;
    public $server = null;

    public static $smsArr = [
        'nexmo'=>'frontend\services\smsServers\nexmoSms',
        'sinch',
        'twilio'=>'frontend\services\smsServers\twilioSms',
        'infobip',
    ];




    public function sendSms($number,$msg)
    {
        if( !array_key_exists($this->server_name ,self::$smsArr))
        {
            throw new SmsException('Your config param has wrong',ErrCode::FAILURE,409);
            return false;
        }

        if(substr($number,0,1) != '+')
        {
            $number = '+'.$number;
        }
        if(substr($this->api_number,0,1) != '+')
        {
            $this->api_number = '+'.$this->api_number;
        }
        $this->server = new self::$smsArr[$this->server_name]($this->api_key,$this->api_secret,$this->api_number);
        return $this->server->sendSms($number,$msg);



    }
}