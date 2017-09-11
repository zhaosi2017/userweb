<?php
namespace frontend\services\smsServers;
use frontend\services\smsServers\AbstractSmsServic;
use Twilio\Rest\Client;
class twilioSms extends AbstractSmsServic
{
    private $server;
    private  $api_number;
    public function __construct($api_key,$api_secret,$api_number)
    {

        if($this->server === null)
        {
            $this->server =new Client($api_key, $api_secret);
        }
        $this->api_number = $api_number;
        return $this->server;

    }

    public function sendSms($number, $msg)
    {
        $response = $this->server->messages->create(
                $number,
            array(
                // A Twilio phone number you purchased at twilio.com/console
                'from' => $this->api_number,
                // the body of the text message you'd like to send
                'body' => $msg
            )
        );
        if( $response->status == 'queued')
        {
            return true;
        }else{
            return false;
        }

    }
}
