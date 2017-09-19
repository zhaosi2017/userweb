<?php
namespace frontend\services\smsServers;
use frontend\services\smsServers\AbstractSmsServic;

class infobipSms extends AbstractSmsServic
{
    private $api_key;
    private $api_secret;
    private $api_number;
    private $authorization;
    private $url = 'http://api.infobip.com/sms/1/text/single';


    public function __construct($api_key, $api_secret, $api_number)
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->api_number = $api_number;
        //$this->authorization = base64_encode( 'Basic '.$api_key.':'.$api_secret);
        $this->authorization = 'Basic Y2FsbHVvbmxpbmU6dHhjLC4vMTIz';

    }

    public function sendSms($number, $msg)
    {

        $data = ["from"=>$this->api_number,"to"=> $number, "text"=>$msg];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: ".$this->authorization,
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            $response = json_decode($response,true);
            return $response['messages']['0']['status']['groupName'] === 'PENDING' ? true : false;
        }
    }
}