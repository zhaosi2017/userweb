<?php
namespace frontend\controllers;

use Yii;
class UserController extends AuthController
{

    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['registerUser'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['@'],
                    ],
                ],
                /*
                'denyCallback' => function($rule, $action) {
                    echo 'You are not allowed to access this page!';
                }
                */
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 注册用户的入口.
     */
    public function actionRegisterUser()
    {
        $postData = Yii::$app->request->post();
        try{
            $result = \PHPClient\Text::instance('userCenter')->setClass('UserCenter_Userweb_Write_User')->resgisterUser($postData);
            if ($result) {

            }
        } catch (\Exception $e) {
            return '网络异常, 请稍后再试!';
        }
    }

}