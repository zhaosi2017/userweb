<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/8/29
 * Time: 上午11:27
 */
namespace common\services\ttsService\thirds;

use common\services\ttsService\AbstruactThird;
use frontend\models\CallRecord\CallRecord;
use \Nexmo\Client\Credentials\Basic;
use \Nexmo\Client\Credentials\Keypair;
use \Nexmo\Client;
use \Nexmo\Client\Credentials\Container;

class Nexmo extends  AbstruactThird{

    private $apiKey = '85704df7';
    private $apiScret = '755026fdd40f34c2';
    private $applicationId = '454eb4c4-1fdd-4b4b-9423-937c80f01bb8';

    private $_answerUrl;
    private $_eventUrl;


    public function CallStart(){

        $basic = new Basic($this->apiKey, $this->apiScret);
        $privatePath = Yii::getAlias('@common').'/config/'.'private.key';
        $keypair = new Keypair(file_get_contents($privatePath), $this->applicationId);
        $client = new Client(new Container($basic, $keypair));
        $this->_setAnwser();
        try{
            $call = $client->calls()->create([
                'to' => [[
                    'type' => 'phone',
                    'number' => $this->To
                ]],
                'from' => [
                    'type' => 'phone',
                    'number' => '12345678'
                ],
                'answer_url' => [
                    $this->_answerUrl,
                ],
                'event_url' => [
                    $this->_eventUrl,
                ]
            ]);
        }catch (Exception $e){
            $call = jsone_encode(['error'=>$e->getMessage()]);
        }
        $call = json_encode($call, JSON_UNESCAPED_UNICODE);
        $call = json_decode($call, true);
        if(isset($call['uuid']) && !empty($call['uuid'])){
            $this->callId = $call['uuid'];
            return true;
        }
        return false;

    }
    /**
     * 存储电话内容等待nexmo来领取
     */
    private function _setAnwser(){

        $cacheKey = $this->From.time();
        $tmp = [
            'action' => 'talk',
            'loop' => $this->Loop,
            'lg' => $this->Language,
            'voiceName' => $this->voice,
            'text' => $this->Text,
        ];
        $conference = [
            $tmp,
        ];
        $conferenceCacheKey = $cacheKey.'_pre';
        Yii::$app->redis->set($conferenceCacheKey, json_encode($conference, JSON_UNESCAPED_UNICODE));
        Yii::$app->redis->expire($conferenceCacheKey, 5*60);
    }

    public function Event(Array $event_data){

        parent::Event($event_data);
        $this->messageId = $event_data['uuid'];
        if($event_data['status'] == 'busy' || $event_data['status'] == 'rejected'){
            $this->Event_Status = $event_data['result'] = CallRecord::CALLRECORD_STATUS_BUSY;
        }elseif($event_data['status'] == 'answered'){
            $this->Event_Status = $event_data['result'] = CallRecord::CALLRECORD_STATUS_SUCCESS;
        }elseif($event_data['status'] == 'failed'){
            $this->Event_Status = $event_data['result'] = CallRecord::CALLRECORD_STATUS_Filed;
        }elseif($event_data['status'] == 'unanwsered'){
            $this->Event_Status = $event_data['result'] = CallRecord::CALLRECORD_STATUS_NOANWSER;
        }elseif($event_data['status'] == 'timeout'){
            $this->Event_Status = $event_data['result'] = CallRecord::CALLRECORD_STATUS_NOANWSER;
        }else{
            $this->DiscardEvent();                                      //其他的回调直接丢弃不处理
        }
        return true;
    }

}