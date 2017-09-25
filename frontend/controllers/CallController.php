<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/9/9
 * Time: 下午3:30
 */
namespace frontend\controllers;


use common\services\ttsService\CallService;
use frontend\models\ErrCode;
use frontend\models\User;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use Yii;

class CallController extends  AuthController{


    public $enableCsrfValidation = false;


    public function behaviors()
    {

        $self = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['verification'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'remove' => ['post'],
                    'add' => ['post'],
                    'list' => ['post'],

                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors, $self);
    }



    public function actionVerification(){

        try{
            $data = $this->getRequestContent();
            $from_user = Yii::$app->user->identity;
            $to_user_account = $data['account'];   //优码
            $to_user = User::findOne(['account'=>$to_user_account]);
            if(empty($to_user_account) || empty($to_user)){
                return $this->jsonResponse('','检测的用户不存在',1, ErrCode::USER_NOT_EXIST);
            }
            $service = new CallService();
            $service->from_user = $from_user;
            $service->to_user   = $to_user;
            $ret  = $service->check();
            if($ret !== true){
                return $this->jsonResponse('',$ret,1, ErrCode::CODE_ERROR);
            }

            return $this->jsonResponse('','检测成功',0, ErrCode::SUCCESS);
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }



    }










}