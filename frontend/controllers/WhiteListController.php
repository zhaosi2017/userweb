<?php
namespace frontend\controllers;

use frontend\models\WhiteLists\WhiteListForm;
use frontend\models\UserForm\WhiteListSwitchForm;
use frontend\models\WhiteLists\WhiteList;
use frontend\models\WhiteLists\WhiteListSearchForm;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use frontend\models\ErrCode;

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
                        'actions' => ['remove', 'add', 'list','switch','status','new-list'],
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
                    'switch'=>['post'],
                    'status'=>['post'],
                    'new-list'=>['post'],

                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors, $self);
    }

    public function actionSwitch()
    {

        try {
            $data = $this->getRequestContent();
            $userModel = new WhiteListSwitchForm();
            $userModel->status = isset($data['status']) ? (int)$data['status'] : '';

            return $userModel->Switchs();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }




    /**
     * 添加某个好友到白名单列表
     */
    public function actionAdd()
    {
        try{
            $data = $this->getRequestContent();
            $whiteList = new WhiteListForm();
            $whiteList->account = isset($data['account']) ? $data['account'] : '';
            return $whiteList->addWhiteList();
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
            $whiteList = new WhiteListForm();
            $whiteList->account = isset($data['account']) ? $data['account'] : '';
            return $whiteList->remove();
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
            $whiteList = new WhiteListSearchForm();
            $p = isset($data['p']) ? (int)$data['p'] : 0;
            return $whiteList->lists($p);
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }


    public function actionStatus()
    {
        try {

            $whiteList = new WhiteList();
            return $whiteList->getWhiteStatus();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }


    public function actionNewList()
    {
        try {
            $data = $this->getRequestContent();
            $whiteList = new WhiteListSearchForm();
            $p = isset($data['p']) ? (int)$data['p'] : 0;
            return $whiteList->newLists($p);
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }



}