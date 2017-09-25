<?php
namespace frontend\services;
use common\services\appService\apps\WebSocket;
use Yii;
use yii\base\Exception;

class FriendNoticeService
{

    /**
     * @param $account 被通知者
     * @param $token 请求者
     * @return mixed
     */
    public function notice($account,$token)
    {
        try {
            $client = new WebSocket();
            $client->connect(Yii::$app->params['webSocket_host'], Yii::$app->params['webSocket_port']);
            $data = [
                'account' => $account,
                'token' => $token,
                'action' => 3,
            ];
            file_put_contents('/tmp/myswoole.log',var_export($data,true).PHP_EOL,8);
            $client->send_data(json_encode($data, JSON_UNESCAPED_UNICODE));
            $data = $client->recv_data();
            $json = json_decode($data);
        }catch (Exception $e)
        {
            return '';
        }
        return isset($json->status) ? $json->status :'1';
    }
}
