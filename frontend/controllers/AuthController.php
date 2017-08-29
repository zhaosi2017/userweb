<?php
namespace frontend\controllers;

use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;

/**
 * 权限验证.
 *
 * @package frontend\controllers
 */
class AuthController extends Controller
{
    public function behaviors()
    {


        $behaviors =  ArrayHelper::merge(
            parent::behaviors(),
            ['authenticator'=>
                [
                    // 'class'=>QueryParamAuth::className(),
                    'class'=>HttpBearerAuth::className(),
                    'optional' => [
                        'login',
                        'register',
                        'register-user',
                    ],
                ]
            ]

        );

        return $behaviors;
    }

    public function jsonResponse($data,$message,$status = 0,$code)
    {
        return ['data'=>$data, 'message'=>$message, 'status'=>$status, 'code'=>$code];
    }

    public function beforeAction($action)
    {

       file_put_contents('/tmp/userweb.log',var_export(\Yii::$app->request->getHeaders(),true).PHP_EOL,8);
        file_put_contents('/tmp/userweb.log',var_export(\Yii::$app->request->getRawBody(),true).PHP_EOL,8);
       return true;
    }

    public function getRequestContent()
    {
        $postData = file_get_contents('php://input');
        $postData = json_decode($postData,true);
        return $postData;
    }
}