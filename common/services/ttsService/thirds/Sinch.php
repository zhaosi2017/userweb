<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/8/29
 * Time: 上午10:57
 */

namespace  common\services\ttsService\third;
use common\services\ttsService\AbstruactThird;
use GuzzleHttp\Psr7\Request;

class Sinch extends  AbstruactThird{

    private $auth_id = '0221f92e-7fbf-4df2-9eb1-c4a965b14fc4';
    private $auth_key = 'D64MIM3RJ0ijv1r5K7fcsQ==';


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
        if(strpos($this->To , '+') !== false){
            $this->to = '+'.trim($this->To ,'+');
        }
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
        $data = json_decode($response->getBody()->getContents());

        if( isset($data->callId) && !empty($data->callId)){
            $this->callId = $data->callId;
            return true;
        }
        return false;
    }

    /**
     * 数据签名
     */
    private function _signature($body){
        $this->timestamp = date("c");
        $path                  = "/v1/callouts";
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
                $this->Event_Status =  CallRecord::Record_Status_Success;
                break;
            case 'FAILED':
                $this->Event_Status =  CallRecord::Record_Status_Fail;
                break;
            case 'NOANSWER':
                $this->Event_Status =  CallRecord::Record_Status_NoAnwser;
                break;
            case 'BUSY':
                $this->Event_Status =  CallRecord::Record_Status_Busy;
                break;
            default:
                $this->Event_Status =  CallRecord::Record_Status_Fail;
                break;
        }
        return 'OK';
    }

}