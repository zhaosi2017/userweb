<?php
namespace frontend\services\smsServers;
use frontend\services\smsServers\AbstractSmsServic;

class infobipSms extends AbstractSmsServic
{
    private $api_key;
    private $api_secret;
    private $api_number;
    private $uri = 'https://api.infobip.com/tts/3/advanced';
    public function __construct($api_key, $api_secret, $api_number)
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->api_number = $api_number;

    }

    public function sendSms($number, $msg)
    {

    }
}