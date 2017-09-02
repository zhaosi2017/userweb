<?php
namespace frontend\controllers;

use Yii;
use frontend\model\WhiteLists\WhiteList;
use frontend\models\Channel;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use frontend\models\User;
use frontend\models\ErrCode;
use frontend\models\UrgentContact;
use frontend\models\Friends\Friends;
use frontend\models\Friends\FriendsRequest;
use frontend\models\Friends\FriendsGroup;

class FriendController extends AuthController
{

    public $enableCsrfValidation = false;


    public function behaviors()
    {

        $self = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['search'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'search' => ['post'],

                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors,$self);
    }

    /**
     * 根据account 和nickname 搜索
     */
    public function actionSearch()
    {

    }


}