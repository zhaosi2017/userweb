<?php
namespace frontend\services;
use common\services\appService\apps\WebSocket;
use Yii;

class AgreeFriendService
{

    /**
 * @param $account 被通知者
 * @param $token 请求者
 * @return mixed
 */
    public function _notice($account,$token)
    {
        $client = new WebSocket();
        $client->connect(Yii::$app->params['webSocket_host'] , Yii::$app->params['webSocket_port']);
        $data = [
            'account'=>$account,
            'token' =>$token,
            'action'=>5,
        ];
        file_put_contents('/tmp/myswoole.log',var_export($data,true).PHP_EOL,8);
        $client->send_data(json_encode($data ,JSON_UNESCAPED_UNICODE));
        $data = $client->recv_data();
        $json = json_decode($data);
        return isset($json->status) ? $json->status:'';
    }

    /**
     * @param $account
     * @param $token
     * 好友申请结果推送
     */
    public function notice($account,$token){
        $user = User::findOne(['token'=>$token]);

        $data = ['account'=>$user->account,'type'=>2,'num'=>1];
        $message = $user->account .'同意了你的好友请求';
        $code = ErrCode::WEB_SOCKET_AGREE_INVITE;
        $result = FActiveRecord::jsonResult($data , $message , 0 , $code);

        $text = json_encode( $result ,JSON_UNESCAPED_UNICODE);
        $json = ['uCode'=>$account , 'message'=>$text];
        $body =json_encode( $json);
        $request = new Request('GET' , '127.0.0.1:9803?json='.urldecode($body));
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
