<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\Registers;

use frontend\models\ErrCode;
use frontend\models\FActiveRecord;
use frontend\models\Friends\Friends;
use frontend\models\User;
use frontend\models\UserPhone;
use yii\base\Model;
use yii\db\Transaction;
use frontend\models\Friends\FriendsGroup;
use  frontend\services\SmsService;

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
class RegisterForm extends User
{


    public $country_code;
    public $phone_number;
    public $password;
    public function rules()
    {
        return [

            ['country_code', 'integer'],
            ['password','string'],
            [['country_code'],'required'],
            [['phone_number'],'required','message'=>'请输入4-11位手机号码'],
            [['password'],'required','message'=>'请输入8位以上包括数字与字母的密码。'],
            ['password', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/','message'=>'请输入8位以上包括数字与字母的密码。'],
            ['country_code','match','pattern'=>'/^[1-9]{1}[0-9]{0,5}$/','message'=>'{attribute}必须为1到6位纯数字'],
            ['phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'请输入4-11位手机号码'],
            ['phone_number','validatePhone'],

        ];
    }




    public function validatePhone($attribute)
    {
        if(empty($this->phone_number))
        {
            $this->addError('phone_number', '该手机号不能为空');
        }

        $rows = User::find()->where(['country_code'=>$this->country_code, 'phone_number'=>$this->phone_number])->one();
        $_userPhone = UserPhone::find()->where(['phone_country_code'=>$this->country_code, 'user_phone_number'=>$this->phone_number])->one();
        if(!empty($rows) || !empty($_userPhone)){
            $this->addError('phone_number', '该手机已注册，请更换手机再试');
        }

    }

    public function  Register()
    {
        if($this->validate())
        {
            return $this->sendMessage();
        }else{
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

    }

    public function sendMessage()
    {
        $number = $this->country_code.$this->phone_number;
        if($number){
            $smsServer = new SmsService();
            return $smsServer->sendMessage($number);

        }else{
            return $this->jsonResponse([],'国码,号码不能为空',1,ErrCode::COUNTRY_CODE_OR_PHONE_EMPTY);
        }
    }


}