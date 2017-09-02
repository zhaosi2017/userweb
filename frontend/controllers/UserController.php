<?php
namespace frontend\controllers;

use frontend\models\Channel;
use frontend\models\Question;
use frontend\models\SecurityQuestion;
use frontend\models\UserPhone;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use frontend\models\User;
use frontend\models\ErrCode;
use frontend\models\UrgentContact;

class UserController extends AuthController
{

    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        $self = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['register-user','user-question','login','reset-message','register','forget-password','reset-password','reset-pass-phone','reset-pass-question'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['nickname','channel-list','update-channel','update-question',
                            'question-list','reset-message','set-user-phone','check-user-phone','user-phone-list',
                            'delete-user-phone','urgent-contact-list','set-urgent-contact','delete-urgent-contact'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post'],
                    'register-user'=>['post'],
                    'login'=>['post'],
                    'register'=>['post'],
                    'nickname'=>['post'],
                    'channel-list'=>['get'],
                    'update-channel'=>['post'],
                    'update-question'=>['post'],
                    'question-list'=>['get'],
                    'reset-password'=>['post'],
                    'forget-password'=>['post'],
                    'reset-pass-phone'=>['post'],
                    'reset-pass-question'=>['post'],
                    'reset-message'=>['post'],
                    'user-question'=>['post'],
                    'set-user-phone'=>['post'],
                    'check-user-phone'=>['post'],
                    'user-phone-list'=>['get'],
                    'delete-user-phone'=>['post'],
                    'urgent-contact-list'=>['get'],
                    'delete-urgent-contact'=>['post'],
                    'set-urgent-contact'=>['post'],
                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors,$self);
    }


    /**
     * 注册用户的入口.
     * @return array
     */
    public function actionRegister()
    {
        $postData = file_get_contents('php://input');
        $postData = json_decode($postData,true);

        try{
            $model = new User();
            $model->country_code = isset($postData['country_code'])?$postData['country_code']:'';
            $model->phone_number = isset($postData['phone_number'])?$postData['phone_number']:'';
            $model->password = isset($postData['password'])?$postData['password']:'';
            return $model->Register();

        }catch (Exception $e){
            return $this->jsonResponse('',$e->getMessage(),1,ErrCode::DATA_SAVE_ERROR);
        }
        catch (\Exception $e) {
             return $this->jsonResponse('',$e->getMessage(),1,ErrCode::UNKNOWN_ERROR);
        }
    }


    /**
     * 注册用户第二步.
     * @return array
     */
    public function actionRegisterUser()
    {

        $postData = $this->getRequestContent();

        try {
            $model = new User();
            $model->country_code = isset($postData['country_code']) ? $postData['country_code'] : '';
            $model->phone_number = isset($postData['phone_number']) ? $postData['phone_number'] : '';
            $model->password = isset($postData['password']) ? $postData['password'] : '';
            $veryCode = isset($postData['code']) ? $postData['code']:'';
            return  $model->registerUser($veryCode);

        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1,ErrCode::UNKNOWN_ERROR);
        }
    }

    /**  登录
     * @return array
     */
    public function actionLogin()
    {
        $postData = file_get_contents('php://input');
        $postData = json_decode($postData,true);


        try {

            $model = new User();
            $model->country_code = isset($postData['country_code'])?$postData['country_code']:'';
            $model->phone_number = isset($postData['phone_number'])?$postData['phone_number']:'';
            $model->password = isset($postData['password'])?$postData['password']:'';
            return $model->login();


        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::DATA_SAVE_ERROR);
        }
        catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }
    }

    /**更新昵称
     * @return array
     */
    public function actionNickname()
    {
        $postData =$this->getRequestContent();
        $nickname = isset($postData['nickname']) ? $postData['nickname'] :'';
        $userId = Yii::$app->user->id;
        try{
            $model = User::findOne($userId);
            $model->nickname = $nickname;
            return $model->updateNickname();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);

        }
    }

    /** 渠道列表
     * @return array
     */
    public function actionChannelList()
    {
        $data =  Channel::find()->select(['id','name','img_url'])->all();
        return $this->jsonResponse($data,'操作成功',0,ErrCode::SUCCESS);
    }

    /**更新渠道
     * @return array
     */
    public function actionUpdateChannel()
    {
        try{
            $data = $this->getRequestContent();
            $channel = isset($data['chan']) ? $data['chan']: '';
            $model = $this->findModel();
            if($model === false)
            {
                return $this->jsonResponse([],'用户不存在',1, ErrCode::USER_NOT_EXIST);
            }
            $model->channel = $channel;
            return $model->updateChannel();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }


    }

    public function actionQuestionList()
    {
        $data = Question::find()->select(['id','title','type'])->all();
        return $this->jsonResponse($data ,'操作成功',0,ErrCode::SUCCESS);

//        return $this->jsonResponse(SecurityQuestion::getQuestions() ,'操作成功',0,ErrCode::SUCCESS);
    }

    /** 密保问题修改
     *
     */
    public function actionUpdateQuestion()
    {
        try{
            $data = $this->getRequestContent();
            $model  = new SecurityQuestion();
            return $model->updateSecurityQuestion($data);

        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }

    }

    /**重置密码 第一步
     * @return array|bool
     */
    public function actionForgetPassword()
    {
        $data = $this->getRequestContent();
        $model = new User();
        $model->country_code = isset($data['country_code']) ? $data['country_code'] : '';
        $model->phone_number = isset($data['phone']) ? $data['phone'] : '';
        try{
           return $model->checkResetPassword();

        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }

    }



    /**重置密码（第二步  当用户选择短信验证--发送短信）
     * @return array|bool
     */
    public function actionResetMessage()
    {
        $data = $this->getRequestContent();
        $model = new User();
        $model->country_code = isset($data['country_code']) ? $data['country_code'] : '';
        $model->phone_number = isset($data['phone']) ? $data['phone'] : '';

        try {

            $res = $model->checkResetPassword();
            if (isset($res['status']) && $res['status'] == 0) {
                return $model->sendMessage();
            }
            return $res;
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

    /**重置密码（第二步  当用户选择安全问题验证--返回该用户设置的安全问题）
     *
     */
    public function actionUserQuestion()
    {
        $data = $this->getRequestContent();
        $model = new User();
        $model->country_code = isset($data['country_code']) ? $data['country_code'] : '';
        $model->phone_number = isset($data['phone']) ? $data['phone'] : '';

        try {
            return $model->getUserQuestion();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }




    /**重置密码验证短信（用户选择短信验证--第二步）
     * @return array
     */
    public function actionResetPassPhone()
    {
        $data = $this->getRequestContent();
        $model = new User();
        $model->country_code = isset($data['country_code']) ? $data['country_code'] : '';
        $model->phone_number = isset($data['phone']) ? $data['phone'] : '';
        $code = isset($data['code']) ? $data['code'] : '';
        try{
            return $model->resetPasswordPhone($code);

        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }


    /**重置密码验证安全问题 （用户选择安全问题验证--第二步）
     * @return array
     */
    public function actionResetPassQuestion()
    {
        $data = $this->getRequestContent();
        $model = new User();
        $model->country_code = isset($data['country_code']) ? $data['country_code'] : '';
        $model->phone_number = isset($data['phone']) ? $data['phone'] : '';
        try{
            return $model->resetPasswordQuestion($data);

        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }



    /**重置密码第三步
     *
     */

    public function actionResetPassword()
    {
        $data = $this->getRequestContent();
        try {
            $model = new User();
            $model->country_code = isset($data['country_code']) ? $data['country_code'] : '';
            $model->phone_number = isset($data['phone']) ? $data['phone'] : '';
            $model->password = isset($data['pass']) ? $data['pass'] : '';
            $token = isset($data['token']) ? $data['token'] : '';
            return $model->resetPassword($token);
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }

    }

    /**
     * 设置手机号，检验手机，并发送短信
     */
    public function actionCheckUserPhone()
    {
        $data = $this->getRequestContent();
        try {
            $model = new UserPhone();
            $model->phone_country_code = isset($data['country_code']) ? $data['country_code'] : '';
            $model->user_phone_number = isset($data['phone']) ? $data['phone'] : '';
            return $model->checkUserPhone();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }
    /**
     * 设置手机号，检验验证码，添加手机
     */
    public function actionSetUserPhone()
    {
        $data = $this->getRequestContent();
        try {
            $model = new UserPhone();
            $model->phone_country_code = isset($data['country_code']) ? $data['country_code'] : '';
            $model->user_phone_number = isset($data['phone']) ? $data['phone'] : '';
            $code = isset($data['code']) ? $data['code'] : '';
            return $model->setUserPhone($code);
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

    /**获取用户自己的手机列表
     * @return array
     */
    public function actionUserPhoneList()
    {
        try {
            $data = UserPhone::find()->where(['user_id' => Yii::$app->user->id])->all();
            return $this->jsonResponse($data, '操作成功', 0, ErrCode::SUCCESS);
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }

    }

    /**
     * 删除用户的手机号
     */
    public function actionDeleteUserPhone()
    {
        $data = $this->getRequestContent();
        try {
            $model = new UserPhone();
            $model->phone_country_code = isset($data['country_code']) ? $data['country_code'] : '';
            $model->user_phone_number = isset($data['phone']) ? $data['phone'] : '';
            return $model->deleteUserPhone();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }

    }



    /**获取用户自己添加的紧急联系人的列表
     * @return array
     */
    public function actionUrgentContactList()
    {
        try {
            $data = UrgentContact::find()->where(['user_id' => Yii::$app->user->id])->all();
            return $this->jsonResponse($data, '操作成功', 0, ErrCode::SUCCESS);
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }

    }

    /**获取用户自己添加的紧急联系人的列表
     * @return array
     */
    public function actionDeleteUrgentContact()
    {
        $data = $this->getRequestContent();
        try {
            $model = new UrgentContact();
            $model->contact_country_code = isset($data['country_code']) ? $data['country_code'] : '';
            $model->contact_phone_number = isset($data['phone']) ? $data['phone'] : '';
            return $model->deleteUrgentContact();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }

    }

    public function actionSetUrgentContact()
    {
        $data = $this->getRequestContent();
        try {
            $model = new UrgentContact();
            $model->contact_country_code = isset($data['country_code']) ? $data['country_code'] : '';
            $model->contact_phone_number = isset($data['phone']) ? $data['phone'] : '';
            $model->contact_nickname = isset($data['nickname']) ? $data['nickname'] : '';
            return $model->setUrgentContact();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }




    private function findModel()
    {
        $userId = Yii::$app->user->id;
        $model = User::findOne($userId);
        if(empty($model))
        {
            return false;
        }
        return $model;
    }







}