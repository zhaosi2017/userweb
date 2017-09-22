<?php
namespace frontend\models;

use frontend\services\SmsService;
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
    const STATUS_DELETED = 10;
    const STATUS_ACTIVE = 0;
    /*********优码初始值*********/
    const INIT_YOUCODE = '999999';

    //token
    const REDIS_TOKEN = 'token';
    //sms
    const REDIS_MESSAGE = 'sms';

    const WHITE_SWITCH_ON = 0;

    const WHITE_SWITCH_OFF = 1;




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
            ['latitude','string'],
            ['longitude','string'],
            ['phone_number','required','on'=>['register']],
            ['country_code','required','on'=>['register']],
            ['password','required','on'=>['register']],
            ['account','match','pattern'=>'/^[0-9]{7}$/','message'=>'{attribute}必须为7位纯数字'],
            ['password', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/','message'=>'密码至少包含8个字符，至少包括以下2种字符：大写字母、小写字母、数字、符号'],
            ['country_code','integer'],
            ['country_code','match','pattern'=>'/^[0-9]{2,6}$/','message'=>'{attribute}必须为2到6位纯数字'],
            ['phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'{attribute}必须为4到11位纯数字'],
            ['phone_number','validatePhone','on'=>'register'],
            [['header_img','latitude','longitude'],'safe'],

        ];
    }



    public function attributeLabels()
    {
        return [
            'status'=>'状态',
            'country_code'=>'国码',
            'phone_number'=>'手机号',
            'account'=>'优码',
            'nickname'=>'昵称',
            'channel'=>'渠道',
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

    public function ValidateWhite()
    {

        if(!in_array($this->whitelist_switch, [self::WHITE_SWITCH_OFF,self::WHITE_SWITCH_ON])){
            $this->addError('whitelist_switch', '参数非法');
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

    protected function makeYouCode()
    {
        $code =  self::INIT_YOUCODE;
        $code = $code + $this->id;
        return $code;
    }





    public function logout()
    {
        //客户端用户-手动关闭websocket 如果不手动关闭，swoole 会定期检查不活动的fd 会触发关闭
        Yii::$app->user->logout();
        return  $this->jsonResponse([],'操作成功',0,ErrCode::SUCCESS);
    }


    protected function getUserIdentity()
    {


       $user =  User::find()->where(['country_code'=>$this->country_code, 'phone_number'=>$this->phone_number])->one();
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




    public static function makeVerifyCode()
    {
        return rand(1000,9999);
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

    protected function updateYouCode()
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

    protected function makeToken()
    {
        return md5($this->phone_number.time().$this->country_code.$this->makeCode(7) );
    }

    protected function makeCode($len = 4)
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
        }catch (\Exception $e)
        {
            $transaction->rollBack();
            return $this->jsonResponse([],$e->getMessage(),1,ErrCode::UNKNOWN_ERROR);

        }
    }


    public function updateNickname()
    {
        if(empty($this->nickname))
        {
            return $this->jsonResponse([],'昵称不能为空',1,ErrCode::NICKNAME_EMPTY);
        }
        $this->setScenario('update_nickname');
        if($this->validate('nickname'))
        {
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
        $this->setScenario('update_channel');
        if ($this->validate('channel') && $this->save())
        {
            return $this->jsonResponse([],'修改渠道成功',0,ErrCode::SUCCESS);
        }else{
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::SUCCESS);
        }
    }

    //重置密码-第一步

    /**shur
     * @return array|bool
     */

    public function checkResetPassword()
    {
        if($this->validate('country_code','phone_number'))
        {
            $res = self::find()->where(['country_code'=>$this->country_code,'phone_number'=>$this->phone_number])->one();
            if($res)
            {
                $question = SecurityQuestion::find()->select(['id'])->where(['userid'=>$res->id])->one();
                $bool = empty($question) ? false: true;
                return $this->jsonResponse(['set-question'=> $bool],'操作成功','0',ErrCode::SUCCESS);

            }else{
                return $this->jsonResponse([],'用户不存在','1',ErrCode::USER_NOT_EXIST);
            }
        }else{
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }





    /**获取用户的安全问题
     * @return array
     */
    public function getUserQuestion()
    {
        if(empty($this->country_code)){return $this->jsonResponse([],'国码不能为空','1',ErrCode::COUNTRY_CODE_EMPTY);}
        if(empty($this->phone_number)){return $this->jsonResponse([],'手机号不能为空','1',ErrCode::PHONE_EMPTY);}

        $user = User::find()->where(['country_code'=>$this->country_code,'phone_number'=>$this->phone_number])->one();
        if(empty($user))
        {
            return $this->jsonResponse([],'用户不存在','1',ErrCode::USER_NOT_EXIST);
        }
        $model = SecurityQuestion::find()->where(['userid'=>$user->id])->one();
        if(empty($model)){
            return $this->jsonResponse([],'用户没有设置密保问题','1',ErrCode::SECURITY_QUESTION_NOT_SET);
        }
        $question = Question::find()->select(['id','title','type'])->indexBy('id')->all();
        if(empty($question))
        {
            return $this->jsonResponse([],'问题表数据为空','1',ErrCode::QUESTIONS_EMPTY);
        }

        $data = [
            'q1'=>isset($question[$model->q_one])? $question[$model->q_one]:'',
            'q2'=>isset($question[$model->q_two])? $question[$model->q_two] :'' ,
            'q3'=>isset($question[$model->q_three])?  $question[$model->q_three] :'',
        ];

        return  $this->jsonResponse($data,'操作成功','0',ErrCode::SUCCESS);

    }






}
