<?php
namespace frontend\services\Email;

use Yii;
use frontend\services\Email\EmailCodeCheck;
class EmailService
{
    const EMAIL_SERVER_PORT = 9509;
    public $server;
    public function __construct()
    {
        if($this->server == null) {
            $this->server = new \swoole_server("127.0.0.1", self::EMAIL_SERVER_PORT);
            $this->server->set(['task_worker_num' => 2]);
            $this->server->on('receive', [$this, 'onReceive']);
            $this->server->on('task', [$this, 'onTask']);
            $this->server->on('finish', [$this, 'onFinish']);
        }


    }

    public function run()
    {
        $this->server->start();
    }

    public function onReceive($server,$fd,$from_id, $data)
    {
        $task_id = $this->server->task($data);
    }

    public function onTask($server, $task_id, $from_id, $data)
    {

        $this->sendEmail($data);
        $this->onFinish($server,$task_id,$data);
    }


    public function onFinish($server, $task_id, $data)
    {
        echo $task_id.PHP_EOL;
    }

    public function sendEmail($data)
    {
        try {
            $data = json_decode($data,true);
            $email = isset($data['email']) ? $data['email'] : '';
            $message = isset($data['message']) ? $data['message'] : '';
            if (empty($email) || empty($message)) {
                return true;
            }
            $redis = Yii::$app->redis;
            $veryCode = $this->makeCode();
            $redis->setex(EmailCodeCheck::EMAIL_CODE_REDIS_PREFIX.$email, 300, $veryCode);

            $mail = Yii::$app->mailer->compose()
            ->setTo($email)
            ->setSubject('callu邮箱验证码')
            ->setTextBody('你的callu的邮箱验证码为'.$veryCode)
            ->send();
            return $mail;
        }catch (\Exception $e)
        {
            echo 'error-'.date('Y-m-d H:i:s').$e->getMessage().var_dump($data).PHP_EOL;
        }catch (\Error $error)
        {
            echo 'error-1'.date('Y-m-d H:i:s').$error->getMessage().var_dump($data).PHP_EOL;
        }

    }


    private function makeCode()
    {
        return rand(1000,9999);
    }
}


