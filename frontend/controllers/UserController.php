<?php
namespace frontend\controllers;

use frontend\models\Channel;
use frontend\models\EmailForm\UserEmail;
use frontend\models\EmailForm\UserUpdateEmail;
use frontend\models\Logins\LoginForm;
use frontend\models\Registers\RegisterForm;
use frontend\models\Registers\RegisterUserForm;
use frontend\models\ResetPasswords\ResetPasswordByPhoneForm;
use frontend\models\ResetPasswords\ResetPasswordByQuestionForm;
use frontend\models\ResetPasswords\ResetPasswordFristForm;
use frontend\models\ResetPasswords\ResetPasswordMessageForm;
use frontend\models\ResetPasswords\ResetPasswordForm;
use frontend\models\Question;
use frontend\models\SecurityQuestion;
use frontend\models\UserForm\PhoneSortForm;
use frontend\models\UserForm\UrgentContactSortForm;
use frontend\models\UserForm\UserImageForm;
use frontend\models\UserPhone;
use frontend\models\UserPhones\UserPhoneAddForm;
use frontend\models\UserPhones\UserPhoneForm;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;

use frontend\models\ErrCode;
use frontend\models\UrgentContact;
USE frontend\models\UserForm\ChannelForm;
USE frontend\models\UserForm\NicknameForm;
USE frontend\models\UserForm\WhiteListSwitchForm;
use frontend\models\User;
use yii\web\UploadedFile;

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
                        'actions' => ['register-user','user-question','login','reset-message','register',
                            'forget-password','reset-password','reset-pass-phone','reset-pass-question'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['nickname','send-email','urgent-contact-sort','phone-sort','channel-list','update-channel','update-question',
                            'question-list','reset-message','set-user-phone','check-user-phone','user-phone-list',
                            'delete-user-phone','update-email','user-question-list','logout','update-image','urgent-contact-list','set-urgent-contact','delete-urgent-contact'],
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
                    'logout'=>['post'],
                    'update-image'=>['post'],
                    'phone-sort'=>['post'],
                    'urgent-contact-sort'=>['post'],
                    'user-question-list'=>['get'],
                    'update-email'=>['post'],
                    'send-email'=>['post'],

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
       $postData = $this->getRequestContent();

        try{
            $model = new RegisterForm();
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
            $model = new RegisterUserForm();
            $model->country_code = isset($postData['country_code']) ? $postData['country_code'] : '';
            $model->phone_number = isset($postData['phone_number']) ? $postData['phone_number'] : '';
            $model->password = isset($postData['password']) ? $postData['password'] : '';
            $model->code = isset($postData['code']) ? $postData['code']:'';
            $model->address = isset($postData['address']) ? $postData['address']:'';
            $model->longitude = isset($postData['longitude'])?$postData['longitude']:'';
            $model->latitude = isset($postData['latitude'])?$postData['latitude']:'';
            return  $model->registerUser();

        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1,ErrCode::UNKNOWN_ERROR);
        }catch (Exception $e){
            return $this->jsonResponse('',$e->getMessage(),1,ErrCode::DATA_SAVE_ERROR);
        }
    }

    /**  登录
     * @return array
     */
    public function actionLogin()
    {
        $postData = $this->getRequestContent();

        try {

            $model = new LoginForm();
            $model->country_code = isset($postData['country_code'])?$postData['country_code']:'';
            $model->phone_number = isset($postData['phone_number'])?$postData['phone_number']:'';
            $model->password = isset($postData['password'])?$postData['password']:'';
            $model->address = isset($postData['address'])?$postData['address']:'';
            $model->longitude = isset($postData['longitude'])?$postData['longitude']:'';
            $model->latitude = isset($postData['latitude'])?$postData['latitude']:'';
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
            $model = new NicknameForm();
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

    /**邮箱发送消息（更新邮箱发送验证码）
     * @return array
     */
    public function actionSendEmail()
    {
        $postData =$this->getRequestContent();
        $email = isset($postData['email']) ? $postData['email'] :'';
        try{
            $model = new UserEmail();
            $model->email = $email;
            return $model->sendEmail();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);

        }
    }

    /**更新邮箱
     * @return array
     */
    public function actionUpdateEmail()
    {
        $postData =$this->getRequestContent();
        $email = isset($postData['email']) ? $postData['email'] :'';
        $code = isset($postData['code']) ? $postData['code'] :'';
        try{
            $model = new UserUpdateEmail();
            $model->email = $email;
            $model->code = $code;
            return $model->updateEmail();
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
        try {
            $data = Channel::find()->select(['id', 'name', 'img_url','gray_img_url'])->orderBy('sort asc')->all();
            if (!empty($data)) {
                foreach ($data as $k => $v) {
                    $v['img_url'] = $v['img_url']? Yii::$app->params['fileBaseDomain'] . $v['img_url']:'';
                    $v['gray_img_url'] = $v['gray_img_url']? Yii::$app->params['fileBaseDomain'] . $v['gray_img_url']: '';
                    $data[$k] = $v;
                }
            }
            return $this->jsonResponse($data,'操作成功',0,ErrCode::SUCCESS);
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);

        }

    }

    /**更新渠道
     * @return array
     */
    public function actionUpdateChannel()
    {
        try{
            $data = $this->getRequestContent();
            $channel = isset($data['chan']) ? $data['chan']: '';
            $model = new ChannelForm();
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

    /**
     *
     */
    public function actionUserQuestionList()
    {
        $data = Question::find()->select(['id','title'])->all();
        $userId = Yii::$app->user->id;
        $_qustion = SecurityQuestion::findOne(['userid'=>$userId]);
        $user_qustion = [];
        if(!empty($_qustion))
        {
            $user_qustion['q1']= $_qustion->q_one;
            $user_qustion['q2']= $_qustion->q_two;
            $user_qustion['q3']= $_qustion->q_three;
            $user_qustion['a1']= $_qustion->a_one;
            $user_qustion['a2']= $_qustion->a_two;
            $user_qustion['a3']= $_qustion->a_three;
        }
        if(empty($user_qustion))
        {
            $user_qustion = (object)$user_qustion;
        }
        return $this->jsonResponse(['questionList'=>$data,'userQuestion'=>$user_qustion] ,'操作成功',0,ErrCode::SUCCESS);

    }

    /**重置密码 第一步
     * @return array|bool
     */
    public function actionForgetPassword()
    {
        $data = $this->getRequestContent();
        $model = new ResetPasswordFristForm();
        $model->country_code = isset($data['country_code']) ? $data['country_code'] : '';
        $model->phone= isset($data['phone']) ? $data['phone'] : '';
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
        $model = new ResetPasswordMessageForm();
        $model->country_code = isset($data['country_code']) ? $data['country_code'] : '';
        $model->phone = isset($data['phone']) ? $data['phone'] : '';

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
        $model = new ResetPasswordByPhoneForm();
        $model->country_code = isset($data['country_code']) ? $data['country_code'] : '';
        $model->phone = isset($data['phone']) ? $data['phone'] : '';
        $model->code = isset($data['code']) ? $data['code'] : '';
        try{
            return $model->resetPasswordPhone();

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
        $model = new ResetPasswordByQuestionForm();
        $model->country_code = isset($data['country_code']) ? $data['country_code'] : '';
        $model->phone = isset($data['phone']) ? $data['phone'] : '';
        $model->q1 = isset($data['q1']) ? $data['q1'] : '';
        $model->q2 = isset($data['q2']) ? $data['q2'] : '';
        $model->q3 = isset($data['q3']) ? $data['q3'] : '';
        $model->a1 = isset($data['a1']) ? $data['a1'] : '';
        $model->a2 = isset($data['a2']) ? $data['a2'] : '';
        $model->a3 = isset($data['a3']) ? $data['a3'] : '';
        try{
            return $model->resetPasswordQuestion();

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
            $model = new ResetPasswordForm();
            $model->country_code = isset($data['country_code']) ? $data['country_code'] : '';
            $model->phone = isset($data['phone']) ? $data['phone'] : '';
            $model->pass = isset($data['pass']) ? $data['pass'] : '';
            $model->token = isset($data['token']) ? $data['token'] : '';
            return $model->resetPasswords();
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
            $model = new UserPhoneForm();
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
            $model = new UserPhoneAddForm();
            $model->phone_country_code = isset($data['country_code']) ? $data['country_code'] : '';
            $model->user_phone_number = isset($data['phone']) ? $data['phone'] : '';
            $model->code = isset($data['code']) ? $data['code'] : '';
            return $model->setUserPhone();
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
            $data = UserPhone::find()->where(['user_id' => Yii::$app->user->id])->orderBy('user_phone_sort desc,id asc')->all();
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
            $data = UrgentContact::find()->where(['user_id' => Yii::$app->user->id])->orderBy('contact_sort desc,id asc')->all();
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

    public function actionUpdateImage()
    {
        try {
            $uploadImage = new UserImageForm();
            $uploadImage->file = UploadedFile::getInstanceByName('file');
            return $uploadImage->upload();

        }catch (Exception $e)
        {
            return $this->jsonResponse([],$e->getMessage(),'1',ErrCode::FAILURE);
        }catch (\Exception $e)
        {
            return $this->jsonResponse([],$e->getMessage(),'1',ErrCode::FAILURE);
        }

    }

    public function actionLogout()
    {
        $data = $this->getRequestContent();
        try {
            $model = new User();
            return $model->logout();
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



    public function actionPhoneSort()
    {
        $data = $this->getRequestContent();

        try {
            $model = new PhoneSortForm();
//            $model->id = isset($data['id']) ? $data['id'] : '';
//            $model->sort = isset($data['sort']) ? $data['sort'] : '';
            $model->data = $data;
            return $model->sort();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

    public function actionUrgentContactSort()
    {
        $data = $this->getRequestContent();
        try {
            $model = new UrgentContactSortForm();
//            $model->id = isset($data['id']) ? $data['id'] : '';
//            $model->sort = isset($data['sort']) ? $data['sort'] : '';
            $model->data = $data;
            return $model->sort();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }






}