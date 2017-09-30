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
use yii\base\Model;
use yii\db\Transaction;
use frontend\models\Friends\FriendsGroup;
use  frontend\services\SmsService;
use Yii;

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
class RegisterUserForm extends User
{


    public $country_code;
    public $phone_number;
    public $password;
    public $code;
    public $address;//用户登录时所在地址
    public $latitude;//用户登录时所在纬度
    public $longitude;//用户登录时所在经度
    public function rules()
    {
        return [

            ['country_code', 'integer'],
            ['password','string'],
            ['address','string'],
            ['latitude','string'],
            ['longitude','string'],
            [['country_code'],'required'],
            [['phone_number'],'required','message'=>'请输入4-11位手机号码'],
            [['password'],'required','message'=>'请输入8位以上包括数字与字母的密码。'],
            [['code'],'required','message'=>'请输入4位数字验证码'],

            ['password', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/','message'=>'请输入8位以上包括数字与字母的密码。'],
            ['country_code','match','pattern'=>'/^[1-9]{1}[0-9]{0,5}$/','message'=>'{attribute}必须为1到6位纯数字'],
            ['phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'请输入4-11位手机号码'],
            ['code','match','pattern'=>'/^[0-9]{4}$/','message'=>'请输入4位数字验证码'],
            ['phone_number','validatePhone'],
            [['address','latitude','longitude'],'safe'],


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

    public function RegisterUser()
    {

        $number = $this->country_code.$this->phone_number;
        if($this->validate([ 'country_code', 'phone_number','password','code']))
        {
//            if(!$this->checkVeryCode($number,$this->code))
//            {
//                return $this->jsonResponse($this,'验证码错误',1,ErrCode::CODE_ERROR);
//            }
            $smsService = new SmsService();
            if($_sms = $smsService->checkSms($number,$this->code))
            {
                return $this->jsonResponse([],$_sms,1,ErrCode::CODE_ERROR);
            }

            $user = new User();

            $user->auth_key = Yii::$app->security->generateRandomString();
            $this->password && $user->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            $user->reg_time = time();
            $user->token = $this->makeToken();
            $user->status = 0;
            $user->reg_ip = Yii::$app->request->getUserIP();
            $user->country_code = $this->country_code;
            $user->phone_number = $this->phone_number;
            $user->address = $this->address;
            $user->latitude = $this->latitude;
            $user->longitude = $this->longitude;

            Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
            $transaction = Yii::$app->db->getTransaction();

            if($user->save())
            {
                $tmp = $user->updateYouCode();
                if($tmp !== true)
                {
                    $transaction->rollBack();
                    return $tmp;
                }
                $res = $user->addUserPhone($user->id);
                if($res  === true)
                {
                    $transaction->commit();
                    if(isset($user->password)){ unset($user->password);}
                    if(isset($user->auth_key)){ unset($user->auth_key);}
                    $data = $user;
                    $smsService->delCode($number);
                    return $this->jsonResponse($data,'注册成功',0,ErrCode::SUCCESS);

                }else{
                    $transaction->rollBack();
                    return $res;
                }

            }else{
                $transaction->rollBack();
                return $this->jsonResponse([],$this->getErrors(),1,ErrCode::DATA_SAVE_ERROR);
            }
        }else{
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

    }


}