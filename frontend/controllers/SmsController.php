<?php
namespace frontend\controllers;

use frontend\controllers\AuthController;
use frontend\models\EmailForm\EmailLogin;
use frontend\models\Sms\SmsForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\db\Exception;
use frontend\models\ErrCode;
class SmsController extends AuthController
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
                        'actions' => ['send-sms'],
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
                    'send-sms' => ['post'],

                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors, $self);
    }


    public function actionSendSms()
    {
        $data = $this->getRequestContent();
        $model = new SmsForm();
        $model->country_code = isset($data['country_code']) ? $data['country_code'] : '';
        $model->phone = isset($data['phone']) ? $data['phone'] : '';

        try {
            return $model->send();

        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }
}