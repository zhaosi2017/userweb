<?php
namespace frontend\services\Email;
use frontend\services\Email\EmailService;

/**异步邮箱发送
 * Class EmailClient
 * @package frontend\services\Email
 */
class EmailClient
{
    public function send($email,$message){
        $client = new \swoole_client(SWOOLE_SOCK_TCP);
        //连接到服务器
        if (!$client->connect('127.0.0.1', EmailService::EMAIL_SERVER_PORT, 0.5))
        {
            file_put_contents('/tmp/myswoole.log','email client connect fail'.PHP_EOL, 8);
        }
        $data = json_encode(['email'=>$email,'message'=>$message]);
        //向服务器发送数据
        if (!$client->send($data))
        {
            file_put_contents('/tmp/myswoole.log','send email fail'.$data.PHP_EOL, 8);
        }
        //关闭连接
        $client->close();
    }
}