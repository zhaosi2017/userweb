<?php
namespace frontend\services;
use common\services\appService\apps\WebSocket;
use frontend\models\ErrCode;
use frontend\models\FActiveRecord;
use frontend\models\User;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
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

    /**
     * @param $account
     * @param $token
     *
     */
    public function _notice($account,$token){

        $user = User::findOne(['token'=>$token]);

        $data = ['account'=>$user->account,'type'=>1,'num'=>1];
        $message = $user->account .'向你发送了好友请求';
        $code = ErrCode::WEB_SOCKET_INVITE_FRIEND;
        $result = FActiveRecord::jsonResult($data , $message , 0 , $code);


        $text = json_encode( $result ,JSON_UNESCAPED_UNICODE);
        $json = ['uCode'=>$account , 'message'=>$text];
        $body =json_encode( $json , JSON_UNESCAPED_UNICODE);
        $request = new Request('GET' ,
            '127.0.0.1:9803?json='.$body);
        $client  = new \GuzzleHttp\Client();
        try{
            $response = $client->send($request , ['timeout'=>10]);
        }catch (\Exception $e){
            $response = new Response();
        }catch(\Error $e){
            $response = new Response();
        }
        if($response->getStatusCode() == 200){
            return true;
        }
        return false;


    }
}
