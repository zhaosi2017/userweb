<?php
namespace frontend\models\EmailForm;

use frontend\models\ErrCode;
use frontend\models\User;
use frontend\services\Email\EmailCodeCheck;

class UserUpdateEmail extends User
{
    public $code;

    public function rules()
    {
        return [
            ['code','required'],
            ['email','required'],
            ['email','email'],
            ['code','number'],
            ['code','validateCode'],
        ];
    }

    public function validateCode()
    {
        $emailCheck = new EmailCodeCheck($this->email,$this->code);
        if($emailCheck->check())
        {
            $this->addError('code','验证码错误');
        }
    }

    public function updateEmail()
    {

        if($this->validate(['email','code']))
        {
            $userId = \Yii::$app->user->id;
            $_user = User::find()->select('id')->where(['email'=>$this->email])->one();
            if(!empty($_user) &&  $userId != $_user->id)
            {
                return $this->jsonResponse([],'该邮箱已被使用，请更换重试','1',ErrCode::EMAIL_HAD_ALREADY_EXISTS);
            }
            $user = User::findOne($userId);
            $user->setScenario('email');
            $user->email = $this->email;

            if($user->save())
            {
                return $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);
            }else{
                return $this->jsonResponse([],$user->getErrors(),'1',ErrCode::DATA_SAVE_ERROR);
            }


        }else{
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }


}