<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\Logins;

use frontend\models\ErrCode;
use frontend\models\FActiveRecord;
use frontend\models\Friends\Friends;
use frontend\models\User;
use frontend\models\UserLoginLogs\UserLoginLog;
use yii\base\Model;
use yii\db\Transaction;
use frontend\models\Friends\FriendsGroup;
use  frontend\services\SmsService;
use Yii;
use frontend\models\UserPhone;
use frontend\models\UrgentContact;
use frontend\models\SecurityQuestion;
use frontend\services\UcodeService;
use frontend\models\UserAppBind;
/**
 * Class Friends
 * @package frontend\models\Friends
 * @property integer $id
 * @property integer $user_id
 * @property integer $friend_id
 * @property integer $create_at
 * @property integer $group_id
 * @property string  $remark
 * @property string  $extsion
 *
 */
class LoginForm extends User
{


    public $country_code;
    public $phone_number;
    public $password;
    public $login_time;//登录时间
    public $address;//用户登录时所在地址
    public $latitude;//用户登录时所在纬度
    public $longitude;//用户登录时所在经度
    public function rules()
    {
        return [

            ['country_code', 'integer'],
            ['password','string'],
            [['country_code','password'],'required'],
            [['phone_number'],'required','message'=>'请输入4-11位手机号码'],
            ['password', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/','message'=>'请输入8位以上包括数字与字母的密码。'],
            ['country_code','match','pattern'=>'/^[1-9]{1}[0-9]{0,5}$/','message'=>'{attribute}必须为1到6位纯数字'],
            ['phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'请输入4-11位手机号码'],
            [['login_time','address','latitude','longitude'],'safe'],

        ];
    }



    public function login()
    {

        if($this->validate())
        {
            $_user =  $this->findUser();
            if(empty($_user))
            {
                return $this->jsonResponse([],'手机号还没注册，请先注册',1,ErrCode::USER_NOT_EXIST);
            }
            $identity = $this->getUserIdentity();

            if(empty($identity))
            {
                return $this->jsonResponse([],'密码错误，请重新输入',1,ErrCode::USER_NOT_EXIST);
            }
            if(Yii::$app->user->login($identity))
            {
                $user = User::findOne(['id'=>$identity->id]);
                $user->token = $user->makeToken($this->country_code.$this->phone_number);
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
                    $userLoginLog->user_id = $identity->id;
                    $userLoginLog->address = $this->address;
                    $userLoginLog->latitude = $this->latitude;
                    $userLoginLog->longitude = $this->longitude;
                    $userLoginLog->login_time = time();
                    $userLoginLog->login_ip= Yii::$app->request->getUserIP();
                    $userLoginLog->country_code = $user->country_code ?  $user->country_code : null;

                    if(!$userLoginLog->save())
                    {
                        return $this->jsonResponse([],$userLoginLog->getErrors(),1,ErrCode::LOGIN_UPDATE_TOKEN_ERROR);
                    }

                    $identity->token = $user->token;
                    if(isset($identity->password)){unset($identity->password);}
                    if(isset($identity->auth_key)){unset($identity->auth_key);}
                    $userPhoneNum = UserPhone::find()->where(['user_id'=>$identity->id])->count();
                    $urgentContactNum = UrgentContact::find()->where(['user_id'=>$identity->id])->count();
                    $data = [];
                    $data = $identity->toArray();
                    $data['header_url'] = '';
                    $isSetQuestion = SecurityQuestion::find()->where(['userid'=>$identity->id])->count();
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
                    return $this->jsonResponse($data,'登录成功',0,ErrCode::SUCCESS);
                }else{
                    return $this->jsonResponse($identity,'登录失败',1,ErrCode::LOGIN_UPDATE_TOKEN_ERROR);
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
        $_user =  User::find()->where(['country_code'=>$this->country_code, 'phone_number'=>$this->phone_number])->one();

        if(empty($_user))
        {
            $_userPhone =UserPhone::find()->select('user_id')->where(['phone_country_code'=>$this->country_code, 'user_phone_number'=>$this->phone_number])->one();
            if(!empty($_userPhone)){
                $_user = User::find()->where(['id'=>$_userPhone['user_id']])->one();

            }
        }
        return $_user;

    }

}