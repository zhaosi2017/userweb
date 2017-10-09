<?php
namespace frontend\services\Email;
use function Couchbase\fastlzCompress;
use Yii;

class EmailCodeCheck
{
    public $email;
    public $code;
    const EMAIL_CODE_REDIS_PREFIX = 'email-key';
    public function __construct($email,$code)
    {
        $this->email = $email;
        $this->code = $code;

    }

    public function check()
    {
        $redis = Yii::$app->redis;
        $_code = $redis->get(self::EMAIL_CODE_REDIS_PREFIX.$this->email);

        if(empty($_code) ||  $_code != $_code)
        {
            return '验证码错误';
        }
        return false;
    }


    public static function delCode($email)
    {
        $redis = Yii::$app->redis;
        $redis->exists(self::EMAIL_CODE_REDIS_PREFIX.$email) && $redis->del(self::EMAIL_CODE_REDIS_PREFIX.$email);
    }
}