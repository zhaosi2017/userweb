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
use frontend\services\Email\EmailCodeCheck;

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
class RegisterUserByEmail extends User
{

    public $email;
    public $password;
    public $code;
    public $address;//用户登录时所在地址
    public $latitude;//用户登录时所在纬度
    public $longitude;//用户登录时所在经度
    public function rules()
    {
        return [

            ['email', 'required'],
            ['email','string'],
            ['email','email'],
            ['password','string'],
            ['address','string'],
            ['latitude','string'],
            ['longitude','string'],
            [['password'],'required','message'=>'请输入8位以上包括数字与字母的密码。'],
            [['code'],'required','message'=>'请输入4位数字验证码'],
            ['password', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/','message'=>'请输入8位以上包括数字与字母的密码。'],
            ['code','match','pattern'=>'/^[0-9]{4}$/','message'=>'请输入4位数字验证码'],
            ['email','validateEmail'],
            [['address','latitude','longitude'],'safe'],


        ];
    }




    public function validateEmail($attribute)
    {


        $rows = User::find()->where(['email'=>$this->email])->one();

        if(!empty($rows)){
            $this->addError('phone_number', '该邮箱已注册，请更换邮箱再试');
        }

    }

    public function RegisterUser()
    {


        if($this->validate([ 'email','password','code']))
        {
            $emailCheck = new EmailCodeCheck($this->email,$this->code);
            if($_sms = $emailCheck->check())
            {
                return $this->jsonResponse([],$_sms,1,ErrCode::CODE_ERROR);
            }
            file_put_contents('/tmp/swoole.log',$this->email.PHP_EOL,8);
            $user = new User();

            $user->auth_key = Yii::$app->security->generateRandomString();
            $this->password && $user->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            $user->reg_time = time();
            $user->token = $this->makeToken($this->email);
            $user->status = 0;
            $user->reg_ip = Yii::$app->request->getUserIP();
            $user->address = $this->address;
            $user->latitude = $this->latitude;
            $user->longitude = $this->longitude;
            $user->email = $this->email;

            Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
            $transaction = Yii::$app->db->getTransaction();

            if($user->save())
            {
                $tmp = $user->updateYouCode();
                if($tmp !== true)
                {
                    $transaction->rollBack();
                    return $this->jsonResponse([],$tmp,1,ErrCode::FAILURE);
                }

                $transaction->commit();
                if(isset($user->password)){ unset($user->password);}
                if(isset($user->auth_key)){ unset($user->auth_key);}
                $data = $user;
                EmailCodeCheck::delCode($this->email);
                return $this->jsonResponse($data,'注册成功',0,ErrCode::SUCCESS);


            }else{
                $transaction->rollBack();
                return $this->jsonResponse([],$user->getErrors(),1,ErrCode::DATA_SAVE_ERROR);
            }
        }else{
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

    }




}