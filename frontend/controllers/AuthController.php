<?php
namespace frontend\controllers;

use frontend\models\ErrCode;
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
//        $arr = ['login',
//            'register',
//            'register-user'];
//        $_action = \Yii::$app->controller->action->id;
//        if(in_array($_action,$arr))
//        {
//
//             if($this->checkRequest() !== true)
//             {
////                 return $this->jsonResponse([],'非法操作',1,ErrCode::ILLEGAL_OPERATION);
////                 return false;
//             }
//        }

        if(defined('YII_ENV') && YII_ENV == 'dev') {
            file_put_contents('/tmp/userweb.log', 'request---------'.PHP_EOL.var_export(\Yii::$app->request->getHeaders(), true) . PHP_EOL, 8);
            file_put_contents('/tmp/userweb.log', var_export(\Yii::$app->request->getRawBody(), true) . PHP_EOL, 8);
        }
        return parent::beforeAction($action);
    }


    public function afterAction($action, $result)
    {
        if(defined('YII_ENV') && YII_ENV == 'dev') {
            file_put_contents('/tmp/userweb.log', 'response ----------' .PHP_EOL. var_export($result, true) . PHP_EOL, 8);
        }
        return parent::afterAction($action, $result);
    }

    public function getRequestContent()
    {
        $postData = file_get_contents('php://input');
        $postData = json_decode($postData,true);
        return $postData;
    }

    private  function checkRequest()
    {
        $data = $this->getRequestContent();
        $time = isset($data['time'])? $data['time'] : '';
        $token = isset($data['token'])? $data['token'] : '';
        $privateKey = \Yii::$app->params['private_token_key'];
        if(md5($privateKey.$time) === $token)
        {
            return true;
        }
        return false;
    }
}