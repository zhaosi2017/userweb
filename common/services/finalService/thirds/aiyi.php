<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/9/6
 * Time: 上午10:17
 */
namespace  common\services\finalService\thirds;


use app\modules\home\servers\FinalService\AbstractThird;

class aiyi extends  AbstractThird {
    /**
     * @var array 支付的付款渠道
     */
    public static $pay_map = [
        1=>'cibalipay', //兴业支付宝
        2=>'cibweixin',  //兴业微信支付
    ];
    public static $pay_name_map = [
        1=>'支付宝支付',
        2=>'微信支付'
    ];


    public  $pay_uri = 'https://vip.iyibank.com/cashier/Home';

    public $event_result = 'SUCCESS';

    public function submit(){

        $this->request_type = 'get';
        $result =  [
            'mch_id'=>$this->Merchant->merchant_id,
            'out_trade_no' =>$this->request_data['order_id'],
            'body'         =>'recharge',
            'callback_url' =>'https://test.callu.online/home/final/aiyi-event',
            'notify_url'   =>'https://test.callu.online/home/final/aiyi-event',
            'total_fee'    =>$this->request_data['order_amount'],
            'service'      =>self::$pay_map[$this->request_data['order_type']],
            'type'=>1
        ];
        $str = implode('',$result);
        $str.=$this->Merchant->certificate;
        $result['sign'] = md5($str);
        $this->pay_uri .='?'.http_build_query($result);
        try{
            $client = new \GuzzleHttp\Client();
            $request  = new \GuzzleHttp\Psr7\Request( $this->request_type , $this->pay_uri  );
            $response = $client->send($request,['timeout' => 30]);
        }catch (Exception $e){
            return false;
        }

        if($response->getStatusCode() !== 200){
            return false;
        }
        $data =  $response->getBody()->getContents();
        $data = json_decode($data ,true);
        $this->pay_uri = $data['token_id'];
        return [];
    }

    public static function checkType($type){

        foreach (Self::$service_map as $key=>$item) {
            if($key & $type){
                return true;
            }
        }
        return false;
    }

    public function getOrderid(Array $data){
        return $data['out_trade_no'];
    }


    public function event(Array $data){

        if($data['mch_id'] !== $this->Merchant->merchant_id){
            return false;
        }
        $str = $data['mch_id'].$data['out_trade_no'].$data['orderid'].$data['total_fee'].$data['service'].$data['result_code'];
        $str .=$this->Merchant->certificate;
        if(strtoupper(md5($str)) != $data['sign']){
            return false;
        }
        $this->event_data['order_id']     = $data['out_trade_no'];
        $this->event_data['order_status'] = $data['result_code'] == 0? FinalOrder::ORDER_STATUS_SUCCESS:FinalOrder::ORDER_STATUS_FAIL;
        $this->event_data['order_amount'] = $data['total_fee'];
        $this->event_data['order_time']   = time();
        return true;
    }






}
