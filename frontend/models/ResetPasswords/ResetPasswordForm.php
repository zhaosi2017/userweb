<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\ResetPasswords;

use frontend\models\ErrCode;
use frontend\models\FActiveRecord;
use frontend\models\Friends\Friends;
use frontend\models\User;
use yii\base\Model;
use yii\db\Transaction;
use frontend\models\Friends\FriendsGroup;
use  frontend\services\SmsService;
use Yii;
use frontend\models\UserPhone;
use frontend\models\UrgentContact;
use frontend\models\SecurityQuestion;
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
class ResetPasswordForm extends User
{

    const  REDIS_TOKEN ='token';
    public $pass;
    public $token;
    public $phone;

    public function rules()
    {
        return [
            ['country_code','required'],
            ['phone','required'],
            ['pass','required'],
            ['token','required'],
            [['country_code','phone'], 'integer'],
            ['pass','string'],
            ['country_code','match','pattern'=>'/^[1-9]{1}[0-9]{0,5}$/','message'=>'{attribute}必须为1到6位纯数字'],
            ['phone','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'{attribute}必须为4到11位纯数字'],
            //['pass', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/','message'=>'密码至少包含8个字符，至少包括以下2种字符：大写字母、小写字母、数字、符号'],
            ['pass', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/','message'=>'请输入8位以上包括数字与字母的密码。'],
        ];
    }

    public function resetPasswords()
    {
        if($this->validate())
        {

            $user =  User::findOne([
                    'country_code'=>$this->country_code,
                    'phone_number'=>$this->phone
                ]
            );
            if(empty($user))
            {
                return  $this->jsonResponse([],'手机号还没注册，请先注册','1',ErrCode::USER_NOT_EXIST );
            }
            $redis = Yii::$app->redis;
            $key = $this->country_code.$this->phone.self::REDIS_TOKEN;
            $_token = $redis->get($key);
            if(empty($this->token) || $this->token != $_token)
            {
                return  $this->jsonResponse([],'非法操作','1',ErrCode::ILLEGAL_OPERATION);
            }
            $user->password = Yii::$app->getSecurity()->generatePasswordHash($this->pass);
            if($user->validate('country_code','phone_number','password') && $user->save())
            {
                $redis->del($key);
                return  $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);

            }else{
                return  $this->jsonResponse([],$user->getErrors(),'1',ErrCode::DATA_SAVE_ERROR);
            }

        }else{
            return  $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }
}