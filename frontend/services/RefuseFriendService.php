<?php
namespace frontend\services;
use common\services\appService\apps\WebSocket;
use Yii;

class RefuseFriendService
{

    /**
     * @param $account 被通知者
     * @param $token 请求者
     * @return mixed
     */
    public function notice($account,$token)
    {
        $client = new WebSocket();
        $client->connect(Yii::$app->params['webSocket_host'] , Yii::$app->params['webSocket_port']);
        $data = [
            'account'=>$account,//被拒绝者
            'token' =>$token,//拒绝者
            'action'=>10,
        ];
        file_put_contents('/tmp/myswoole.log',var_export($data,true).PHP_EOL,8);
        $client->send_data(json_encode($data ,JSON_UNESCAPED_UNICODE));
        $data = $client->recv_data();
        $json = json_decode($data);
        return isset($json->status) ? $json->status:'';
    }
}
