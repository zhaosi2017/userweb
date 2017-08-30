<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/8/29
 * Time: 下午2:23
 */
namespace frontend\controllers;

use common\services\appService\apps\callu;
use frontend\models\CallRecord\CallRecord;
use Yii;
use common\services\ttsService\CallService;
use frontend\models\User;
use common\services\ttsService\thirds\Sinch;
use yii\filters\AccessControl;

class EventController extends  AuthController {

    public $enableCsrfValidation = false;


    public function behaviors()
    {
        $self = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['test' , 'event-sinch'],
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors,$self);

        return $behaviors;
    }


    public function actionEventSinch(){

        $postData = @file_get_contents('php://input');
        $callback_data = json_decode($postData ,true);
        $service = new CallService(Sinch::class);
        $rest = $service->Event($callback_data);
        echo $rest;
    }


    public function actionEventNexmo(){

        $postData = @file_get_contents('php://input');
        $postData = json_decode($postData, true);
        $service = new CallService(Nexmo::class);
        $result =  $service->Event($postData);
        echo $result;
    }

    public function actionAnwserNexmo(){

        $cachKey = Yii::$app->request->get('key');
        header("content-type:application/json;charset=utf-8");
        $data = Yii::$app->redis->get($cachKey);
        if(empty($data)){
            $data   = '[]';
        }
        exit($data);
    }


    public function actionTest(){


        $app =  new callu();

        $service =new  CallService(Sinch::class);
        $service->from_user = User::findOne(1);
        $service->to_user   = User::findOne(2);
        $service->text      ="双流老妈秃头呼叫你上线";
        $service->app       = $app;
        $service->call_type = CallRecord::CALLRECORD_TYPE_UNURGENT;

        $service->start_call();
    }



}