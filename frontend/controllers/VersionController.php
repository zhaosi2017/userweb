<?php
namespace frontend\controllers;

use frontend\models\Versions\VersionForm;
use frontend\models\WhiteLists\WhiteListForm;
use frontend\models\UserForm\WhiteListSwitchForm;
use frontend\models\WhiteLists\WhiteList;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use frontend\models\ErrCode;

class VersionController extends AuthController
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
                        'actions' => ['check'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'check' => ['post'],

                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors, $self);
    }

    public function actionCheck()
    {

        try {
            $data = $this->getRequestContent();
            $versionForm = new VersionForm();
            $versionForm->platform = isset($data['platform']) ? $data['platform'] : '';
            return $versionForm->checkUpdate();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }



}