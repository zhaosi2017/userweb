<?php
namespace frontend\models\Registers;

use frontend\models\ErrCode;
use frontend\services\Email\EmailClient;
use Yii;
use  frontend\models\User;


class RegisterEmail extends User
{
    const EMAIL_REDIS_PREFIX = 'email-';
    const EMAIL_REDIS_EXPIRE_TIME = '300';//六分钟有效期
    public function rules()
    {
        return [
            ['email','required'],
            ['email','email'],
            ['email','validateEmail'],
        ];
    }

    public function validateEmail()
    {

        $_email = Yii::$app->security->decryptByKey(base64_decode($this->email), Yii::$app->params['inputKey']);
        $_user = User::findOne(['email'=>$_email]);
        if(!empty($_user))
        {
            $this->addError('email','该邮箱已注册，请更换邮箱再试');
        }
    }


    public function sendEmail()
    {
        if($this->validate())
        {
            $veryCode = self::makeCode();
            $redis = Yii::$app->redis;
            $redis->SETEX(self::EMAIL_REDIS_PREFIX.$this->email, self::EMAIL_REDIS_EXPIRE_TIME, $veryCode);
            $emailClient = new EmailClient();
            $emailClient->send($this->email,'你的callu的邮箱验证码为'.$veryCode);
            return $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);

        }else {
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }

    public function makeCode($len = 4)
    {
        return rand(1000,9999);
    }


}