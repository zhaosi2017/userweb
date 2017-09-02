<?php
namespace frontend\controllers;

use frontend\models\Friends\FriendsRequestForm;
use frontend\models\Friends\FriendsSearch;
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
                        'actions' => ['search','friend-detail','add-friend-request'],
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




}