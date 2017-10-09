<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\modules\v1\models\Logins;

use frontend\models\ErrCode;
use frontend\models\FActiveRecord;
use frontend\models\Friends\Friends;
use frontend\modules\v1\models\Users\User;
use yii\base\Model;
use yii\db\Transaction;
use frontend\models\Friends\FriendsGroup;
use  frontend\services\SmsService;
use Yii;
use frontend\models\UserPhone;
use frontend\models\UrgentContact;
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
    public function rules()
    {
        return [

            ['country_code', 'integer'],
            ['password','string'],
            [['country_code','phone_number','password'],'required'],
            ['password', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/','message'=>'密码至少包含8个字符，至少包括以下2种字符：大写字母、小写字母、数字、符号'],
            ['country_code','match','pattern'=>'/^[0-9]{2,6}$/','message'=>'{attribute}必须为2到6位纯数字'],
            ['phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'{attribute}必须为4到11位纯数字'],

        ];
    }




    public function validatePhone($attribute)
    {
        if(empty($this->phone_number))
        {
            $this->addError('phone_number', '该手机号不能为空');
        }

        $rows = User::find()->where(['country_code'=>$this->country_code, 'phone_number'=>$this->phone_number])->one();

        if(!empty($rows)){
            $this->addError('phone_number', '该手机已注册，请更换手机再试');
        }

    }

    public function login()
    {


        if($this->validate())
        {
            $identity = $this->getUserIdentity();

            if(empty($identity))
            {
                return $this->jsonResponse([],'用户不存在/密码错误',1,ErrCode::USER_NOT_EXIST);
            }
            if(Yii::$app->user->login($identity))
            {
                $user = User::findOne(['id'=>$identity->id]);
                $user->token = $user->makeToken($this->country_code.$this->phone_number);
                if($user->save())
                {
                    $identity->token = $user->token;
                    if(isset($identity->password)){unset($identity->password);}
                    if(isset($identity->auth_key)){unset($identity->auth_key);}
                    $userPhoneNum = UserPhone::find()->where(['user_id'=>$identity->id])->count();
                    $urgentContactNum = UrgentContact::find()->where(['user_id'=>$identity->id])->count();
                    $data = [];
                    $data = $identity->toArray();
                    $data['userPhoneNum'] = $userPhoneNum;
                    $data['urgentContactNum'] = $urgentContactNum;
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

}