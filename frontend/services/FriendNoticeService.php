<?php
namespace frontend\services;
use common\services\appService\apps\WebSocket;
use Yii;

class FriendNoticeService
{

    public function notice($account,$token)
    {
        $client = new WebSocket();
        $client->connect(Yii::$app->params['webSocket_host'] , Yii::$app->params['webSocket_port']);
        $data = [
            'account'=>$account,
            'token' =>$token,
            'action'=>3,
        ];
        $client->send_data(json_encode($data ,JSON_UNESCAPED_UNICODE));
        $data = $client->recv_data();
        $json = json_decode($data);
        file_put_contents('/tmp/swooles',var_export($json,true).PHP_EOL,8);
        return $json->status;
    }
}
