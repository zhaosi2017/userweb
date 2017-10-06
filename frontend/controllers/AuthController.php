<?php
namespace frontend\controllers;

use frontend\models\ErrCode;
use frontend\services\LimitRate\LimitRateService;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use yii\web\UnauthorizedHttpException;

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
                        'register-email',
                        'test',
                        'event-sinch',
                        'reset-password',
                        'forget-password',
                        'reset-pass-phone',
                        'reset-pass-question',
                        'reset-message',
                        'user-question'
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
        if(defined('YII_ENV') && YII_ENV == 'dev') {
            file_put_contents('/tmp/userweb.log', 'request-url---------'.PHP_EOL.var_export(\Yii::$app->request->getPathInfo(), true) . PHP_EOL, 8);
            file_put_contents('/tmp/userweb.log', 'request---------'.PHP_EOL.var_export(\Yii::$app->request->getHeaders(), true) . PHP_EOL, 8);
            file_put_contents('/tmp/userweb.log', var_export(\Yii::$app->request->getRawBody(), true) . PHP_EOL, 8);
        }
        $arr = ['login', 'register', 'register-user','user-question','reset-message','forget-password','reset-password','reset-pass-phone','reset-pass-question','register-email'];
        $_action = \Yii::$app->controller->action->id;
        if(in_array($_action , ['event-sinch' , 'test'])){    //三方的回调数据校验不再这里
              return true;
        }
        if(in_array($_action,$arr))
        {

             if($this->checkRequest() !== true)
             {
                 throw new UnauthorizedHttpException('Your request was made with invalid credentials.');
                 return false;
             }
        }



        return parent::beforeAction($action);
    }


    public function afterAction($action, $result)
    {
//        if(!\Yii::$app->user->isGuest)
//        {
//            $limitRateService = new LimitRateService();
//            if ($ms = $limitRateService->limit(\Yii::$app->request->getPathInfo()))
//            {
//                return $this->jsonResponse([],$ms,'1',ErrCode::LIMIT_RATE_ERROR);
//            }
//        }

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

        $authHeader = \Yii::$app->request->getHeaders();
        $matches = [];
        if ($authHeader->get('Authorization') !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader->get('Authorization'), $matches)) {

            $token = strtolower($matches[1]);
            $time = $authHeader->get('time') !== null ? $authHeader->get('time') : '';
            $privateKey = \Yii::$app->params['private_token_key'];
            if(md5($privateKey.$time) === $token)
            {
                return true;
            }
            return false;
        }else{
            return false;
        }

    }
}