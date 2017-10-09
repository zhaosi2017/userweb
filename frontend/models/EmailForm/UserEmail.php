<?php
namespace frontend\models\EmailForm;

use frontend\models\User;
use frontend\services\Email\EmailClient;
use frontend\models\ErrCode;

class UserEmail extends User
{
    //public $email;

    public function rules()
    {
        return [
            ['email','required'],
            ['email','email'],
            ['email','validatorEmail'],
        ];
    }

    public function validatorEmail()
    {
        $userId = \Yii::$app->user->id;
        $_user = User::find()->select('id')->where(['email'=>$this->email])->one();
        if(!empty($_user) && $_user->id != $userId)
        {
            $this->addError('email','邮箱已被占用，请更换重试！');
        }

    }

    public function sendEmail()
    {
        try {
            file_put_contents('/tmp/swoole.log','1111'.PHP_EOL,8);
            $emailClient = new EmailClient();
            $emailClient->send($this->email, '邮箱验证码');
            return $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);
        }catch (\Error $e)
        {
            return $this->jsonResponse([],$e->getMessage(),'1',ErrCode::EMAIL_SEND_FAILURE);
        }



    }
}