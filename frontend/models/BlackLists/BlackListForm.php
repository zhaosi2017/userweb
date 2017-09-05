<?php
namespace frontend\models\BlackLists;

use frontend\models\BlackLists\BlackList;
use frontend\models\ErrCode;
use frontend\models\User;
use frontend\models\WhiteLists\WhiteList;
use yii\base\Model;
use frontend\models\Friends\Friends;

class BlackListForm extends BlackList
{
    public $account;

    public function rules()
    {
        return [
            ['account', 'required'],
            ['account', 'integer'],
            ['account', 'ValidateAccount'],
        ];
    }

    public function ValidateAccount()
    {
        $user = User::findOne(['account' => $this->account]);
        if (empty($user)) {
            $this->addError('account', '优码不存在');
        }
    }


    public function addBlackList()
    {
        if($this->validate('account'))
        {
            $userId = \Yii::$app->user->id;
            $user = User::findOne(['account' => $this->account]);
            $identy = \Yii::$app->user->identity;
            if($identy->account == $this->account)
            {
                return $this->jsonResponse([],'不能添加自己到黑名单',1, ErrCode::DO_NOT_YOURSELF);
            }
            $_friend = Friends::findOne(['user_id'=>$userId,'friend_id'=>$user->id]);
            if(empty($_friend))
            {
                return $this->jsonResponse([],'你们不是好友',1, ErrCode::YOU_ARE_NOT_FRIENDS);
            }
            $white = WhiteList::findOne(['uid'=>$userId,'white_uid'=>$user->id]);
            if(!empty($white))
            {
                return $this->jsonResponse([],'该好友在你的白名单中，不能添加到黑名单',1, ErrCode::THE_FRIEND_IN_YOU_WHITELIST);
            }
            $_black = BlackList::findOne(['uid'=>$userId,'black_uid'=>$user->id]);
            if(!empty($_black))
            {
                return $this->jsonResponse([],'该好友在你的黑名单中，不能重复添加黑名单',1, ErrCode::THE_FRIEND_ALREADY_IN_YOU_BLACKLIST);
            }else{
                $black = new BlackList();
                $black->uid = $userId;
                $black->black_uid = $user->id;
                if($black->save())
                {
                    return $this->jsonResponse([],'操作成功',0,ErrCode::SUCCESS);
                }else{
                    return $this->jsonResponse([],$black->getErrors(),0,ErrCode::DATA_SAVE_ERROR);
                }

            }


        }else{
            return $this->jsonResponse([],$this->getErrors(),1, ErrCode::VALIDATION_NOT_PASS);
        }
    }

    public function remove()
    {
        if($this->validate('account'))
        {
            $userId = \Yii::$app->user->id;
            $user = User::findOne(['account' => $this->account]);
            $identy = \Yii::$app->user->identity;
            if($identy->account == $this->account)
            {
                return $this->jsonResponse([],'不能操作自己',1, ErrCode::DO_NOT_YOURSELF);
            }

            $_black = BlackList::findOne(['uid'=>$userId,'black_uid'=>$user->id]);
            if(!empty($_black))
            {
                if($_black->delete())
                {
                    return $this->jsonResponse([],'操作成功',0, ErrCode::SUCCESS);
                }else{
                    return $this->jsonResponse([],$_black->getErrors(),1, ErrCode::FAILURE);
                }
            }else{
                return $this->jsonResponse([],'对方不在你的黑名单列表里',1,ErrCode::THE_FRIEND_NOT_IN_YOU_BLACKLIST);

            }


        }else{
            return $this->jsonResponse([],$this->getErrors(),1, ErrCode::VALIDATION_NOT_PASS);
        }
    }



}