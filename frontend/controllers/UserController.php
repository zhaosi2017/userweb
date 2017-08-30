<?php
namespace frontend\controllers;

use frontend\models\Channel;
use frontend\models\SecurityQuestion;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use frontend\models\User;
use frontend\models\ErrCode;
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
                        'actions' => ['register-user','login','register'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['nickname','channel-list','update-channel','update-question','question-list'],
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
            return $model->login($postData);


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
        return $this->jsonResponse(SecurityQuestion::getQuestions() ,'操作成功',0,ErrCode::SUCCESS);
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