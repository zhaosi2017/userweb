<?php
namespace frontend\services\smsServers;


use yii\base\Component;
class smsConnection extends Component
{

    public $api_key ;
    public $api_secret;
    public $serverName;
    public $server;

    public static $smsArr = [
        'nexmo',
        'sinch',
        'twilioSms',
        'infobip',
    ];


    public function open()
    {


        if($this->server !==false)
        {
            return ;
        }
        if( in_array($this->serverName ,self::$smsArr))
        {
            throw new \Exception($this->serverName.'不存在');
        }

        $this->server = new $this->serverName($this->api_key,$this->api_secret);
    }


    protected function initConnection()
    {
        $this->trigger(self::EVENT_AFTER_OPEN);

    }

    public function sendSms($number,$msg)
    {
        return $this->server->sendSms($number,$msg);
    }
}