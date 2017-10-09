<?php
namespace frontend\controllers;
use frontend\controllers\AuthController;
use frontend\models\Registers\RegisterEmail;
use frontend\models\Registers\RegisterUserByEmail;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\db\Exception;
use frontend\models\ErrCode;

class RegisterController extends AuthController
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
                        'actions' => ['register-email', 'register-user'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => [],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'register-email' => ['post'],
                    'register-user' => ['post'],

                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors, $self);
    }


    /**
     * 注册邮箱的入口.
     * @return array
     */
    public function actionRegisterEmail()
    {
        try {

            $data = $this->getRequestContent();
            $registerEmail = new RegisterEmail();
            $registerEmail->email = isset($data['email']) ? $data['email'] : '';
            return $registerEmail->sendEmail();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }

    }


    /**
     * 注册邮箱-保存用户的入口.
     * @return array
     */
    public function actionRegisterUser()
    {
        try {
            $data = $this->getRequestContent();
            $registerEmail = new RegisterUserByEmail();
            $registerEmail->email = isset($data['email']) ? $data['email'] : '';
            $registerEmail->password = isset($data['password']) ? $data['password'] : '';
            $registerEmail->code = isset($data['code']) ? $data['code'] : '';
            $registerEmail->address = isset($data['address']) ? $data['address']:'';
            $registerEmail->longitude = isset($data['longitude'])?$data['longitude']:'';
            $registerEmail->latitude = isset($data['latitude'])?$data['latitude']:'';
            return $registerEmail->RegisterUser();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }
}