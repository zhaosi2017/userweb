<?php
namespace frontend\services\smsServers;
use frontend\services\smsServers\AbstractSmsServic;
use Twilio\Rest\Client;
class twilioSms extends AbstractSmsServic
{
    private $sid;
    private $token;
    private $server;
    public function __construct($sid,$token)
    {

        if($this->server === null)
        {
            $this->server =new Client($sid, $token);
        }
        return $this->server;

    }

    public function sendSms($number, $msg)
    {



            $response = $this->server->messages->create(
                        $number,
                    array(
                        // A Twilio phone number you purchased at twilio.com/console
                        'from' => '+19472224852',
                        // the body of the text message you'd like to send
                        'body' => $msg
                    )
                    );


            if(isset($response->status) &&  $response->status == 'queued')
            {
                return true;
            }
            return false;
    }
}
