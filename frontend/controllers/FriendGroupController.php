<?php
namespace frontend\controllers;

use frontend\models\FriendGroups\FriendAddFriendForm;
use frontend\models\FriendGroups\FriendRemoveFriend;
use frontend\models\FriendGroups\FriendAddGroupForm;
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
use frontend\models\FriendGroups\FriendUpdateGroupNameForm;

class FriendGroupController extends AuthController
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
                        'actions' => ['add-group', 'update-group-name','group-list' ,'add-friend', 'remove-friend'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'add-group' => ['post'],
                    'update-group-name'=>['post'],
                    'add-friend'=>['post'],
                    'remove-friend'=>['post'],
                    'group-list'=>['post'],
                ],
            ],
        ];
        $behaviors = parent::behaviors();
        return array_merge($behaviors, $self);
    }

    public function actionAddGroup()
    {
        try {
            $data = $this->getRequestContent();
            $group = new FriendAddGroupForm();
            $group->name = isset($data['name']) ? $data['name'] : '';
            return $group->addGroup();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }

    }


    public function actionUpdateGroupName()
    {
        try {
            $data = $this->getRequestContent();
            $group = new FriendUpdateGroupNameForm();
            $group->gid = isset($data['gid']) ? $data['gid'] : '';
            $group->name = isset($data['name']) ? $data['name'] : '';
            return $group->updateGroupName();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }


    public function actionAddFriend()
    {
        try {
            $data = $this->getRequestContent();
            $group = new FriendAddFriendForm();
            $group->gid = isset($data['gid']) ? $data['gid'] : '';
            $group->account = isset($data['account']) ? $data['account'] : '';
            return $group->addFriend();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }

    }

    public function actionRemoveFriend()
    {
        try {
            $data = $this->getRequestContent();
            $group = new FriendRemoveFriend();
            $group->gid = isset($data['gid']) ? $data['gid'] : '';
            $group->account = isset($data['account']) ? $data['account'] : '';
            return $group->removeFriend();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

    public function actionGroupList()
    {
        try {
            $_friend = new Friends();
            return $_friend->getGroupList();
        }catch (Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::UNKNOWN_ERROR);
        }catch (\Exception $e)
        {
            return $this->jsonResponse('',$e->getMessage(),1, ErrCode::NETWORK_ERROR);
        }
    }

}