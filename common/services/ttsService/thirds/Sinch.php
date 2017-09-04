<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/8/29
 * Time: 上午10:57
 */

namespace  common\services\ttsService\thirds;
use common\services\ttsService\AbstruactThird;
use frontend\models\CallRecord\CallRecord;
use GuzzleHttp\Psr7\Request;

class Sinch extends  AbstruactThird{

    private $auth_id = '610491fc-2af1-4c16-9a2f-8ab50b7ffc93';
    private $auth_key = 'Y/9Mx71MbUaYrfJMreCthQ==';

    private $uri = 'https://callingapi.sinch.com/v1/callouts';

    private $authorization;
    private $timestamp;


    /**
     * @var array
     * 需要处理的返回状态
     */
    private $status_map = [
        'timeout',   //超时
        'ANSWERED',   //成功
        'FAILED',       //错误
        'NOANSWER', //无法接通
        'BUSY',     //用户忙
    ];

    /**
     * @return bool 呼叫开始
     */
    public function CallStart(){

        $this->To = '+'.trim($this->To ,'+');
        $text = '';
        for($i=1; $i <= $this->Loop ; $i++){
            $text .=' '.$this->Text;
        }
        $body = json_encode(
            ['method'=>'ttsCallout',
                "ttsCallout"=>[
                    "cli" => $this->From,
                    "destination" =>[ "type" => "number", "endpoint" =>$this->To ],
                    "domain" => "pstn",
                    "custom" =>"customData",
                    "locale" => $this->Language,
                    "prompts" =>'#tts['.$text.'];myprerecordedfile',
                    'enabledice' => true,
                ],
            ]);
        $this->_signature($body);
        $header = ['x-timestamp'=>$this->timestamp , 'Content-type'=>'application/json' ,'Authorization'=>$this->authorization];
        $request  = new Request('POST' , $this->uri , $header , $body);
        $response =  $this->HttpSend($request);
        if(empty($response)){
            return false;
        }
        $data = json_decode($response->getBody()->getContents() );
        if( isset($data->callId) && !empty($data->callId)){
            $this->callId = $data->callId;
            return true;
        }
        return false;
    }

    /**
     * 附属产品发短信
     */
    public function SmsStart(){

        $this->uri = 'https://messagingapi.sinch.com/v1/Sms/85586564836';
        $body = json_encode([
            'message'=>$this->Text
        ]);
        $this->_signature($body , '/v1/Sms/85586564836');
        $header = ['x-timestamp'=>$this->timestamp , 'Content-type'=>'application/json' ,'Authorization'=>$this->authorization];
        $request  = new Request('POST' , $this->uri , $header , $body);
        $response =  $this->HttpSend($request);
        if($response->getStatusCode() !== 200){
            return false;
        }
        return true;
    }

    /**
     * 数据签名
     */
    private function _signature($body , $path = null){
        $this->timestamp = date("c");
        $path                  = empty($path)?"/v1/callouts":$path;
        $content_type          = "application/json";
        $canonicalized_headers = "x-timestamp:" . $this->timestamp;

        $content_md5 = base64_encode( md5( utf8_encode($body), true ));
        $string_to_sign =
            "POST\n".
            $content_md5."\n".
            $content_type."\n".
            $canonicalized_headers."\n".
            $path;
        $signature = base64_encode(hash_hmac("sha256", utf8_encode($string_to_sign), base64_decode($this->auth_key), true));
        $this->authorization = "Application " . $this->auth_id . ":" . $signature;
    }

    /**
     * @param array $Event_data
     * @return string
     *
     */
    public  function Event(Array $Event_data){

        parent::Event($Event_data);

        if($Event_data['event'] != 'dice'){
            $this->DiscardEvent('OK');
        }

        $this->callId = $Event_data['callid'];           //通话id
        switch ($Event_data['result']){
            case 'ANSWERED':
                $this->Event_Status =  CallRecord::CALLRECORD_STATUS_SUCCESS;
                break;
            case 'FAILED':
                $this->Event_Status =  CallRecord::CALLRECORD_STATUS_FILED;
                break;
            case 'NOANSWER':
                $this->Event_Status =  CallRecord::CALLRECORD_STATUS_NOANWSER;
                break;
            case 'BUSY':
                $this->Event_Status =  CallRecord::CALLRECORD_STATUS_BUSY;
                break;
            default:
                $this->Event_Status =  CallRecord::CALLRECORD_STATUS_FILED;
                break;
        }
        return 'OK';
    }

}