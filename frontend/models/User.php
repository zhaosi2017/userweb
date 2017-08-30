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
use frontend\models\ErrCode;
use frontend\models\Channel;



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
    /*********优码初始值*********/
    const INIT_YOUCODE = '999999';

    /*******渠道*******/
    const CHANNEL_TELEGRAM = 'Telegram';
    const CHANNEL_POTATO = 'Potato';
    const CHANNEL_SKYPE = 'Skype';
    const CHANNEL_WhatsApp = 'WhatsApp';
    const CHANNEL_QQ= 'QQ';
    const CHANNEL_Wechat = 'Wechat';
    const CHANNEL_FACEBOOK= 'Facebook';
    const CHANNEL_GMAIL = 'Gmail';

    /*******渠道数组*******/
    public static $ChannlArr =
        [
            self::CHANNEL_TELEGRAM =>'Telegram',
            self::CHANNEL_POTATO => 'Potato',
            self::CHANNEL_SKYPE => 'Skype',
            self::CHANNEL_WhatsApp => 'WhatsApp',
            self::CHANNEL_QQ=> 'QQ',
            self::CHANNEL_Wechat => 'Wechat',
            self::CHANNEL_FACEBOOK=> 'Facebook',
            self::CHANNEL_GMAIL => 'Gmail',
        ];




    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
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
            ['country_code','match','pattern'=>'/^[0-9]{2,6}$/','message'=>'{attribute}必须为2到6位纯数字'],
            ['phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'{attribute}必须为4到11位纯数字'],
            ['phone_number','validatePhone','on'=>'register'],
            ['nickname','match','pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{4,12}$/','message'=>'密码至少包含4-12个字符，至少包括以下2种字符：大写字母、小写字母、数字、符号'],
            ['channel','ValidateChannel'],

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

    public function ValidateChannel()
    {
        $tmp = explode(',',$this->channel);
        $channelArr = Channel::find()->select('id')->indexBy('id')->all();
        if(!empty($tmp))
        {
            foreach ($tmp as $c){
                if(array_key_exists($c,$channelArr))
                {
                    continue;
                }
                $this->addError('channel','渠道非法');
                break;
            }
        }
        return true;
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
        return static::findOne(['token' => $token]);
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

    private function makeYouCode()
    {
        $code =  self::INIT_YOUCODE;
        $code = $code + $this->id;
        return $code;
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
                if(isset($identity->password)){unset($identity->password);}
                if(isset($identity->auth_key)){unset($identity->auth_key);}
                return $this->jsonResponse($identity,'登录成功',0,ErrCode::SUCCESS);
            }else{
                return $this->jsonResponse([],'登录失败',1,ErrCode::FAILURE);
            }

        }else{
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

    }


    private function getUserIdentity()
    {


       $user =  User::find()->where([
           'country_code'=>$this->country_code,
           'phone_number'=>$this->phone_number,
       ])->one();
       if(empty($user))
       {
           return false;
       }else{
            if( Yii::$app->getSecurity()->validatePassword($this->password, $user->password))
           {
               return $user;
           }
           return false;
       }
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
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
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
            $response = json_decode($response, true);
            if(isset($response['messages'][0]['status'] ) && $response['messages'][0]['status'] ==0)
            {
                return $this->jsonResponse(['code'=>$verifyCode],'操作成功',0,ErrCode::SUCCESS);
            }else{
                return $this->jsonResponse([],'号码错误/网络错误',1,ErrCode::NETWORK_OR_PHONE_ERROR);
            }
        }else{
            return $this->jsonResponse([],'国码,号码不能为空',1,ErrCode::COUNTRY_CODE_OR_PHONE_EMPTY);
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
                return $this->jsonResponse($this,'验证码错误',1,ErrCode::CODE_ERROR);
            }

            $this->auth_key = Yii::$app->security->generateRandomString();
            $this->password && $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            $this->reg_time = time();
            $this->token = $this->makeToken();
            $this->status = 0;
            Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
            $transaction = Yii::$app->db->getTransaction();

            if($this->save())
            {
                $tmp = $this->updateYouCode();
                if($tmp !== true)
                {
                    $transaction->rollBack();
                    return $tmp;
                }
                if($res = $this->addUserPhone($this->id) === true)
                {
                    $transaction->commit();

                    if(isset($this->password)){ unset($this->password);}
                    if(isset($this->auth_key)){ unset($this->auth_key);}
                    $data = $this;
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

    private function updateYouCode()
    {
        try{

            $model = User::findOne($this->id);
            Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
            $transaction = Yii::$app->db->getTransaction();

            $model->account = $this->makeYouCode();
            if($model->save())
            {
                $transaction->commit();
                $this->account = $model->account;
                return true;
            }else{
                $transaction->rollBack();
                return $this->jsonResponse([],$model->getErrors(),1,ErrCode::DATA_SAVE_ERROR);
            }
        }catch (Exception $e)
        {
            $transaction->rollBack();
            return $this->jsonResponse([],$e->getMessage(),1,ErrCode::UNKNOWN_ERROR);
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
                return $this->jsonResponse([],$userPhone->getErrors(),1,ErrCode::DATA_SAVE_ERROR);
            }
        }catch (Exception $e)
        {
            $transaction->rollBack();
            return $this->jsonResponse([],$e->getErrors(),1,ErrCode::UNKNOWN_ERROR);

        }
    }


    public function updateNickname()
    {

        if($this->validate('nickname'))
        {
            if(empty($this->nickname))
            {
                return $this->jsonResponse([],'昵称不能为空',1,ErrCode::NICKNAME_EMPTY);
            }
            if($this->save())
            {
                return $this->jsonResponse([],'修改昵称成功',0,ErrCode::SUCCESS);
            }else{
                return $this->jsonResponse([],'修改昵称失败',1,ErrCode::DATA_SAVE_ERROR);
            }
        }else{
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

    }


    public function updateChannel()
    {
        if(empty($this->channel))
        {
            return $this->jsonResponse([],'渠道不能为空',1,ErrCode::CHANNEL_EMPTY);
        }
        if ($this->validate('channel') && $this->save())
        {
            return $this->jsonResponse([],'修改渠道成功',0,ErrCode::SUCCESS);
        }else{
            return $this->jsonResponse([],$this->getErrors(),0,ErrCode::SUCCESS);
        }
    }


}
