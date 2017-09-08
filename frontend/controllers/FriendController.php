<?php
namespace frontend\controllers;

use frontend\models\Friends\FriendsAgreeForm;
use frontend\models\Friends\FriendsDelFrom;
use frontend\models\Friends\FriendsInfoSearch;
use frontend\models\Friends\FriendsRefuseForm;
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
                        'actions' => ['search','new-friend-num','friend-list','update-friend-remark','get-friend-info','friend-detail','delete-friend',
                            'add-friend-request','get-friend-request','refuse-friend-request','agree-friend-request'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'search' => ['post'],
                    'friend-detail'=>['post'],
                    'add-friend-request'=>['post'],
                    'get-friend-request'=>['post'],
                    'refuse-friend-request'=>['post'],
                    'get-friend-info'=>['post'],
                    'update-friend-remark'=>['post'],
                    'agree-friend-request'=>['post'],
                    'delete-friend'=>['post'],
                    'friend-list'=>['post'],
                    'new-friend-num'=>['post'],

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
        try{
        $data = $this->getRequestContent();
        $friendsSearch = new FriendsSearch();
        $friendsSearch->search_word = isset($data['search']) ? $data['search'] : '';
        return $friendsSearch->searchUser();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

    /**查看某优客的基本信息（还不是好友）
     * @return array
     */
    public function actionFriendDetail()
    {
        try{
            $data = $this->getRequestContent();
            $friendsSearch = new FriendsSearch();
            $friendsSearch->account = isset($data['account']) ? $data['account'] : '';
            return $friendsSearch->friendDetail();
        }catch (Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }

    }


    /**向某优客发送添加好友的申请
     * @return array
     */
    public function actionAddFriendRequest()
    {
         try{
             $data = $this->getRequestContent();
             $friendsSearch = new FriendsRequestForm();
             $friendsSearch->account = isset($data['account']) ? $data['account'] : '';
             $friendsSearch->note = isset($data['remark']) ? $data['remark'] : '';
             return $friendsSearch->addFriendsRequest();
         }catch (Exception $e) {
             return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
         }catch (\Exception $e) {
             return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
         }
    }

    /**
     * 手动获取新好友添加申请
     */
    public function actionGetFriendRequest()
    {
        try{
            $data = $this->getRequestContent();
            $friendsSearch = new FriendsRequest();
            return $friendsSearch->getFriendsRequest($data);
        }catch (Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

    /**
     * 同意 好友请求
     */
    public function actionAgreeFriendRequest()
    {

        try{
            $data = $this->getRequestContent();
            $friends = new FriendsAgreeForm();
            $friends->account = isset($data['account']) ? $data['account'] :'';
            return $friends->agreeFriendsRequest($data);
        }catch (Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

    public function actionRefuseFriendRequest()
    {
        try{
            $data = $this->getRequestContent();
            $friendsSearch = new FriendsRefuseForm();
            return $friendsSearch->refuseFriendsRequest($data);
        }catch (Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

    /**
     * 查看好友信息（已经是好友）
     */
    public function actionGetFriendInfo()
    {
        try{
            $data = $this->getRequestContent();
            $friends = new FriendsInfoSearch();
            $friends->account = isset($data['account']) ? $data['account'] : '';
            return $friends->getFriendInfo();
        }catch (Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

    public function actionUpdateFriendRemark()
    {
        try{
            $data = $this->getRequestContent();
            $friends = new FriendsRemarkForm();
            $friends->account = isset($data['account']) ? $data['account'] : '';
            $friends->remark = isset($data['remark']) ? $data['remark'] : '';
            return $friends->updateFriendRemark();
        }catch (Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

    public function actionDeleteFriend()
    {
        try{
            $data = $this->getRequestContent();
            $friends = new FriendsDelFrom();
            $friends->account = isset($data['account']) ? $data['account'] : '';
            return $friends->deleteFriend();
        }catch (Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }

    }

    //好友列表
    public function actionFriendList()
    {
        try{
        $fiends = new Friends();
        return $fiends->lists();
        }catch (Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }
    //新朋友列表
    public function actionNewFriendNum()
    {
        try{
            $fiends = new Friends();
            return $fiends->newFriendNum();
        }catch (Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e) {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }




}