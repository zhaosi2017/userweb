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
    public function rules()
    {
        return [

            ['country_code', 'integer'],
            ['password','string'],
            [['country_code','phone_number','password','code'],'required'],
            ['password', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/','message'=>'密码至少包含8个字符，至少包括以下2种字符：大写字母、小写字母、数字、符号'],
            ['country_code','match','pattern'=>'/^[0-9]{2,6}$/','message'=>'{attribute}必须为2到6位纯数字'],
            ['phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'{attribute}必须为4到11位纯数字'],
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

        if(!empty($rows)){
            $this->addError('phone_number', '该手机已注册，请更换手机再试');
        }

    }

    public function RegisterUser()
    {

        $number = $this->country_code.$this->phone_number;
        if($this->validate([ 'country_code', 'phone_number','password','code']))
        {
            if(!$this->checkVeryCode($number,$this->code))
            {
                return $this->jsonResponse($this,'验证码错误',1,ErrCode::CODE_ERROR);
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
            $user->balance = '0.0000';

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