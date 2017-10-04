<?php
namespace frontend\controllers;

use frontend\controllers\AuthController;
use frontend\models\EmailForm\EmailLogin;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\db\Exception;
class LoginController extends AuthController
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
                        'actions' => ['login'],
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
                    'login' => ['post'],

                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors, $self);
    }

    public function actionLogin()
    {
        try {
            $data = $this->getRequestContent();
            $emailLogin = new EmailLogin();
            $emailLogin->email = isset($data['email']) ? $data['email'] : '';
            $emailLogin->password = isset($data['password']) ? $data['password'] : '';
            $emailLogin->address = isset($data['address']) ? $data['address'] : '';
            $emailLogin->latitude = isset($data['latitude']) ? $data['latitude'] : '';
            $emailLogin->longitude = isset($data['longitude']) ? $data['longitude'] : '';
            return $emailLogin->login();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

}