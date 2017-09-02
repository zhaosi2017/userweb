<?php
namespace frontend\controllers;

use frontend\model\WhiteLists\WhiteList;
use frontend\models\Channel;
use frontend\models\Question;
use frontend\models\SecurityQuestion;
use frontend\models\UserPhone;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use frontend\models\User;
use frontend\models\ErrCode;
use frontend\models\UrgentContact;

class WhiteListController extends AuthController
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
                        'actions' => ['D'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['switch'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'switch' => ['post'],

                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors,$self);
    }

    public function actionSwitch()
    {
        try {
            $data = $this->getRequestContent();
            $userId = Yii::$app->user->id;
            $userModel = User::findOne(['id' => $userId]);
            $userModel->whitelist_switch = isset($data['status']) ? (int)$data['status'] : 0;

            return $userModel->Switch();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }
}