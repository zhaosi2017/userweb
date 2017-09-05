<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/8/29
 * Time: 上午9:35
 *
 * 规范电话服务商 的接口
 *
 */

namespace  common\services\ttsService;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use yii\db\Exception;

/**
 * Class AbstruactThird
 * @property string $callId
 */

abstract  class AbstruactThird{
    /**
     * @var string  供应商返回的呼叫id
     */
    public $callId;
    /**
     * @var string 主叫电话
     */
    public $From;
    /**
     * @var string 被叫电话
     */
    public $To;

    /**
     * @var string 呼出内容
     */
    public $Text;
    /**
     * @var int  语音播报次数
     */
    public $Loop;

    /**
     * @var string 播报语言
     */
    public $Language;

    /**
     * @var int 呼叫状态
     */
    public $Event_Status;



    /**
     * @return mixed
     *开始呼叫 发送呼叫请求给电话服务商
     */
    abstract  public function CallStart();

    /**
     * @param array $data 回调数据
     * @return mixed
     * 回调事件处理
     */
      public function Event(Array $data){
            $log = [
                'url'=>$_SERVER["REMOTE_ADDR"],
                'data'=>json_encode($data , true),
            ];
            $this->_event_data_log($log);
      }



    /**
     * 丢弃一些回调事件
     */
    public function DiscardEvent($string =null ){

        exit($string);
    }

    /**
     * @param Request $request
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *
     * 这里统一发送http请求方便记录日志
     */
    public function HttpSend(Request $request){
        $log = [
            'url'=>$request->getUri(),                   //交互的地址
            'data'=>$request->getBody()->getContents(),  //请求的内容
        ];
        $this->_send_data_log($log);
        $client =  new Client();
        try{
              $response = $client->send($request , ['timeout' => 30]);
        }catch ( Exception $e){
              $response = new Response();
        }
       //$this->_send_data_log(array($copy->getBody()->getContents()));
        return $response;

    }

    /**
     * 写发送日志
     */
    private function _send_data_log(Array $log){
        $log['interface'] = get_class($this);
        $log['object']    = serialize($this);
        $log['time']      = time();
        file_put_contents('/tmp/log_tts.send.log' , var_export($log , true).PHP_EOL,8);

    }

    /**
     * 写回调日志
     */
    private function _event_data_log(Array $log){

        $log['interface'] = get_class($this);
        $log['object']    = serialize($this);
        $log['time']      = time();
        file_put_contents('/tmp/log_tts.event.log' , var_export($log , true).PHP_EOL,8);
    }

}