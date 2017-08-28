<?php
namespace frontend\models;


use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\Exception;
use yii\web\IdentityInterface;
use frontend\models\FActiveRecord;
use frontend\models\UserPhone;
use yii\db\Transaction;



/**
 * User model
 *
 * @property integer $id
 * @property string $account
 */
class User extends FActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const INIT_YOUCODE = '0000';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{user}}';
    }
    

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['country_code','account'], 'integer'],
            ['password','string'],
            ['phone_number','required','on'=>['register']],
            ['country_code','required','on'=>['register']],
            ['password','required','on'=>['register']],
            ['account','match','pattern'=>'/^[0-9]{7}$/','message'=>'{attribute}必须为7位纯数字'],
            ['password', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/','message'=>'密码至少包含8个字符，至少包括以下2种字符：大写字母、小写字母、数字、符号'],
            ['country_code','integer'],
            ['phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'{attribute}必须为4到11位纯数字'],
            ['phone_number','validatePhone','on'=>'register'],

        ];
    }




    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $res = [
            'register' => [ 'password','country_code', 'phone_number'],
        ];
        return array_merge($scenarios,$res);
    }



    public function validatePhone($attribute)
    {
        if(empty($this->phone_number))
        {
            $this->addError('phone_number', '该手机号不能为空');
        }
        $rows = self::find()->where(['country_code'=>$this->country_code, 'phone_number'=>$this->phone_number])->one();

        if(!empty($rows)){
            $this->addError('phone_number', '该手机已注册，请更换手机再试');
        }

    }
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public function makeYouCode()
    {
        $res = self::find()->select('id')->orderBy('id desc')->one();
        if(empty($res))
        {
            $_tmp = 0;
        }else{
            $_tmp = $res['id'];
        }
        $code =  rand(1,9).rand(0,9).rand(0,9).self::INIT_YOUCODE;
        $code = $code + $_tmp;
        return $code;
    }



    public function login($data)
    {
        if($this->validate())
        {
            $identity = $this->getUserIdentity();
            if(Yii::$app->user->login($identity))
            {
                return ['status'=>0,'message'=>'登录成功','data'=>['token'=>$identity->token]];
            }

        }else{
            return ['status'=>1,'message'=>$this->getErrors(),1];
        }

    }

    private function getUserIdentity()
    {
       return User::find()->where(['country_code'=>$this->country_code,'phone_number'=>$this->phone_number])->one();
    }
    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function  Register()
    {

        $this->setScenario('register');
        if($this->validate())
        {
            return $this->sendMessage();
        }else{
            return ['status'=>1,'message'=>$this->getErrors(),'data'=>[]];
        }

    }

    private function sendMessage()
    {

        $number = $this->country_code.$this->phone_number;

        if($number){

            $redis = Yii::$app->redis;
            $verifyCode = self::makeVerifyCode();
           // $redis->setex($number,60*2,$verifyCode);
            $redis->set($number,$verifyCode);

            $url = 'https://rest.nexmo.com/sms/json?' . http_build_query(
                    [
                        'api_key' =>  Yii::$app->params['nexmo_api_key'],
                        'api_secret' => Yii::$app->params['nexmo_api_secret'],
                        'to' => $number,
                        'from' => Yii::$app->params['nexmo_account_number'],
                        'text' => 'Your Verification Code '.$verifyCode
                    ]
                );

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            file_put_contents('/tmp/userweb.log',$response.PHP_EOL,8);
            $response = json_decode($response, true);


            if(isset($response['messages'][0]['status'] ) && $response['messages'][0]['status'] ==0)
            {
                $response['code'] = $verifyCode;
                return ['status'=>1,'message'=>$this->getErrors(),'data'=>$response];
            }else{
                return ['status'=>1,'message'=>'号码错误/网络错误','data'=>[]];
            }

        }else{
            return ['status'=>1,'message'=>'国码,号码不能为空','data'=>[]];
        }


    }

    public static function makeVerifyCode()
    {
        return rand(1000,9999);
    }

    public function RegisterUser($veryCode)
    {

        $number = $this->country_code.$this->phone_number;
        $this->setScenario('register');
        if($this->validate())
        {


            if(!$this->checkVeryCode($number,$veryCode))
            {
                return  $this->jsonResponse(['status'=>1,'message'=>'验证码错误','data'=>$this]);
            }

            $this->auth_key = Yii::$app->security->generateRandomString();
            $this->account = $this->makeYouCode();
            $this->password && $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            $this->reg_time = time();
            $this->token = $this->makeToken();
            $this->status = 0;
            Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
            $transaction = Yii::$app->db->getTransaction();

            if($this->save())
            {
                if($res = $this->addUserPhone($this->id) === true)
                {
                    $transaction->commit();
                    return ['status'=>0,'message'=>'注册成功','data'=>$this];
                }else{
                    $transaction->rollBack();
                    return $res;
                }

            }else{
                $transaction->rollBack();
                return ['status'=>1,'message'=>$this->getErrors(),'data'=>[]];
            }
        }else{
            return ['status'=>1,'message'=>$this->getErrors(),'data'=>[]];
        }

    }

    public function checkVeryCode($number,$veryCode)
    {
        $redis = Yii::$app->redis;
        $_veryCode = $redis->get($number);
        if($_veryCode && $_veryCode == $veryCode)
        {
            $redis->del($number);
            return true;
        }else{
            return false;
        }

    }

    private function makeToken()
    {
        return md5($this->phone_number.time().$this->country_code.$this->makeCode() );
    }

    private function makeCode($len = 4)
    {
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz
                   ABCDEFGHIJKLOMNOPQRSTUVWXYZ,./&amp;l
                  t;&gt;?;#:@~[]{}-_=+)(*&amp;^%$£!';    //字符池
        $key = '';
        for($i=0; $i<$len; $i++)
        {
            $key .= $pattern{mt_rand(0,35)};    //生成php随机数
        }
        return $key;
    }

    public function addUserPhone($userId)
    {

        $res = UserPhone::find()->where(['user_id'=>$userId,'phone_country_code'=>$this->country_code,'user_phone_number'=>$this->phone_number])->one();
        if(!empty($res)){
            return true;
        }
        try {

            $userPhone = new UserPhone();
            $userPhone->user_id = $userId;
            $userPhone->phone_country_code = $this->country_code;
            $userPhone->user_phone_number = $this->phone_number;
            $userPhone->reg_time = time();
            Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
            $transaction = Yii::$app->db->getTransaction();
            if ($userPhone->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return ['status'=>1,'message'=>$userPhone->getErrors(),'data'=>[]];
            }
        }catch (Exception $e)
        {
            $transaction->rollBack();
            return ['status'=>1,'message'=> $e->getMessage(),'data'=>[]];
        }
    }


}
