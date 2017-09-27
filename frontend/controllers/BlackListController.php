<?php
namespace frontend\controllers;

use frontend\models\BlackLists\BlackList;
use frontend\models\BlackLists\BlackListForm;
use frontend\models\Friends\FriendsInfoSearch;
use frontend\models\Friends\FriendsRemarkForm;
use frontend\models\Friends\FriendsRequestForm;
use frontend\models\Friends\FriendsSearch;
use Yii;
use frontend\models\WhiteLists\WhiteList;
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
use frontend\models\BlackLists\BlackListSearchForm;

class BlackListController extends AuthController
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
                        'actions' => ['remove', 'add', 'list'],
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

    /**
     * 添加某个好友到黑名单列表
     */
    public function actionAdd()
    {
        try{
            $data = $this->getRequestContent();
            $blackList = new BlackListForm();
            $blackList->account = isset($data['account']) ? $data['account'] : '';
            return $blackList->addBlackList();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

    public function actionRemove()
    {
        try{
            $data = $this->getRequestContent();
            $blackList = new BlackListForm();
            $blackList->account = isset($data['account']) ? $data['account'] : '';
            return $blackList->remove();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

    public function actionList()
    {
        try {
            $data = $this->getRequestContent();
            $p = isset($data['p']) ? (int)$data['p'] : '0';
            $blackList = new BlackListSearchForm();

            return $blackList->lists($p);
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }





}