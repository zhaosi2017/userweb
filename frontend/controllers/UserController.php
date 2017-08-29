<?php
namespace frontend\controllers;

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
                        'actions' => ['nickname'],
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
                    'nickname'=>['post']
                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors,$self);
    }

    /**
     * 注册用户的入口.
     */
    public function actionRegisterUser()
    {
        $postData = file_get_contents('php://input');
        $postData = json_decode($postData, true);

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
    /**
     * 注册用户的入口.
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

}