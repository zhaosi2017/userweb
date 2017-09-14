<?php
namespace frontend\modules\v1\controllers;

use frontend\models\ErrCode;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\controllers\AuthController;
use frontend\modules\v1\models\Logins\LoginForm;
use yii\rest\ActiveController;
/**
 * Site controller
 */
class UserController extends AuthController
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {

        $self = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login','reset-message','register',],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['nickname','channel-list','update-channel','update-question',
                            'question-list','reset-message','set-user-phone','check-user-phone','user-phone-list',
                            'delete-user-phone','logout','update-image','urgent-contact-list','set-urgent-contact','delete-urgent-contact'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'register-user'=>['post'],
                    'login'=>['post'],
                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors,$self);
    }


    public function actionLogin()
    {
        $postData = file_get_contents('php://input');
        $postData = json_decode($postData,true);


        try {

            $model = new LoginForm();
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
}