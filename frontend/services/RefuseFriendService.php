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
    public function notice_($account,$token)
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


    /**
     * @param $account
     * @param $token
     * 好友申请结果推送
     */
    public function notice($account,$token){
        $user = User::findOne(['token'=>$token]);

        $data = ['account'=>$user->account,'type'=>4,'num'=>1];
        $message = $user->account .'拒绝了你的好友请求';
        $code = ErrCode::WEB_SOCKET_REFUSE_INVITE;
        $result = FActiveRecord::jsonResult($data , $message , 0 , $code);

        $text = json_encode( $result ,JSON_UNESCAPED_UNICODE);
        $json = ['uCode'=>$account , 'message'=>$text];
        $body =json_encode( $json );
        $request = new Request('GET' , '127.0.0.1:9803?json='.urlencode($body));
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



