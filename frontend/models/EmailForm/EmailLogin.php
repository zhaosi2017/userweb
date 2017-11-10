<?php
namespace frontend\models\EmailForm;

use frontend\models\User;
use frontend\models\UserAppBind;
use frontend\models\UserLoginLogs\UserLoginLog;
use frontend\models\UserPhone;
use frontend\models\UrgentContact;
use frontend\models\SecurityQuestion;
use frontend\models\ErrCode;
use frontend\services\UcodeService;
use Yii;


class EmailLogin extends User
{
    public $address;
    public $latitude;
    public $longitude;

    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email','email'],


        ];
    }






    public function login()
    {
        if($this->validate())
        {

            $_email = Yii::$app->security->decryptByKey(base64_decode($this->email), Yii::$app->params['inputKey']);
            $_user = $this->findUser();

            if(empty($_user))
            {
                return $this->jsonResponse([],'该邮箱还未注册，请先注册',1,ErrCode::USER_NOT_EXIST);
            }
            if( !Yii::$app->getSecurity()->validatePassword($this->password, $_user->password))
            {
                return $this->jsonResponse([],'密码错误，请重新输入',1,ErrCode::PASSWORD_ERROR);
            }


            if(Yii::$app->user->login($_user))
            {
                $user = User::findOne(['id'=>$_user->id]);
                $user->setScenario('token');
                $user->token = $user->makeToken($this->email);
                $user->login_ip = Yii::$app->request->getUserIP();
                $user->login_time = time();
                if( empty($user->account))
                {
                    $user->account = UcodeService::makeCode();
                    $_user->account = $user->account;
                }
                if($user->save())
                {


                    $userLoginLog = new UserLoginLog();
                    $userLoginLog->user_id = $_user->id;
                    $userLoginLog->address = $this->address;
                    $userLoginLog->latitude = $this->latitude;
                    $userLoginLog->longitude = $this->longitude;
                    $userLoginLog->login_time = time();
                    $userLoginLog->country_code = $user->country_code ?  $user->country_code : null;
                    $userLoginLog->login_ip= Yii::$app->request->getUserIP();

                    if(!$userLoginLog->save())
                    {
                        return $this->jsonResponse([],$userLoginLog->getErrors(),1,ErrCode::LOGIN_UPDATE_TOKEN_ERROR);
                    }

                    $_user->token = $user->token;
                    if(isset($_user->password)){unset($_user->password);}
                    if(isset($_user->auth_key)){unset($_user->auth_key);}
                    $userPhoneNum = UserPhone::find()->where(['user_id'=>$_user->id])->count();
                    $urgentContactNum = UrgentContact::find()->where(['user_id'=>$_user->id])->count();
                    $data = [];
                    $data = $_user->toArray();
                    $data['header_url'] = '';
                    $isSetQuestion = SecurityQuestion::find()->where(['userid'=>$_user->id])->count();
                    if(isset($data['header_img']))
                    {
                        if(  !empty($data['header_img'])) {
                            $data['header_url'] = Yii::$app->params['frontendBaseDomain'] . $data['header_img'];
                        }
                        unset($data['header_img']);
                    }
                    $data['userPotatoNum'] = UserAppBind::find()->where(['user_id'=>$user->id,'type'=>UserAppBind::APP_TYPE_POTATO])->count();
                    $data['userTelegramNum'] = UserAppBind::find()->where(['user_id'=>$user->id,'type'=>UserAppBind::APP_TYPE_TELEGRAM])->count();

                    $data['userPhoneNum'] = $userPhoneNum;
                    $data['urgentContactNum'] = $urgentContactNum;
                    $data['isSetQuestion'] = $isSetQuestion > 0 ? true :false;
                    $data['websocktUrl'] = Yii::$app->params['websocket_app_url'];
                    return $this->jsonResponse($data,'登录成功',0,ErrCode::SUCCESS);
                }else{
                    return $this->jsonResponse([],$user->getErrors(),1,ErrCode::LOGIN_UPDATE_TOKEN_ERROR);
                }

            }else{
                return $this->jsonResponse([],'登录失败',1,ErrCode::FAILURE);
            }

        }else{
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

    }

    public function findUser()
    {
        $users = User::find()->all();
        if(!empty($users))
        {

            foreach ($users as $v)
            {
                if($v->email == $this->email)
                {
                    return $v;
                }
            }
        }
        return [];

    }

}