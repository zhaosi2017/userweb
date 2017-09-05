<?php
namespace frontend\models\Friends;

use frontend\models\BlackLists\BlackList;
use frontend\models\ErrCode;
use frontend\models\User;
use frontend\models\WhiteLists\WhiteList;
use yii\base\Model;
use frontend\models\Friends\Friends;
use yii\db\Transaction;
use yii;

class FriendsDelFrom extends Friends
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

    public function deleteFriend()
    {
        if($this->validate('account'))
        {
            $userId = \Yii::$app->user->id;
            $user = User::findOne(['account' => $this->account]);
            //要删除相关的信息
            $_friend = Friends::findOne(['user_id'=>$userId,'friend_id'=>$user->id]);
            $friend = Friends::findOne(['friend_id'=>$userId,'user_id'=>$user->id]);
            $white = WhiteList::findOne(['uid'=>$userId,'white_uid'=>$user->id]);
            $blacklist = BlackList::findOne(['uid'=>$userId,'black_uid'=>$user->id]);
            $_white = WhiteList::findOne(['uid'=>$user->id,'white_uid'=>$userId]);
            $_blacklist = BlackList::findOne(['uid'=>$user->id,'black_uid'=>$userId]);


            if(empty($_friend))
            {
                return $this->jsonResponse([],'你们还不是好友','1',ErrCode::YOU_ARE_NOT_FRIENDS);
            }else{
                Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
                $transaction = Yii::$app->db->getTransaction();
                if(!$friend->delete()){
                    $transaction->rollBack();
                    return $this->jsonResponse([],$friend->getErrors(),'1',ErrCode::FAILURE);
                }

                if(!empty($_friend))
                {
                    if(!$_friend->delete())
                    {
                        $transaction->rollBack();
                        return $this->jsonResponse([],$_friend->getErrors(),'1',ErrCode::FAILURE);
                    }
                }
                if(!empty($white))
                {
                    if(!$white->delete())
                    {
                        $transaction->rollBack();
                        return $this->jsonResponse([],$white->getErrors(),'1',ErrCode::FAILURE);
                    }
                }
                if(!empty($blacklist))
                {
                    if(!$blacklist->delete())
                    {
                        $transaction->rollBack();
                        return $this->jsonResponse([],$blacklist->getErrors(),'1',ErrCode::FAILURE);
                    }
                }
                if(!empty($_white))
                {
                    if(!$_white->delete())
                    {
                        $transaction->rollBack();
                        return $this->jsonResponse([],$_white->getErrors(),'1',ErrCode::FAILURE);
                    }
                }
                if(!empty($_blacklist))
                {
                    if(!$_blacklist->delete())
                    {
                        $transaction->rollBack();
                        return $this->jsonResponse([],$_blacklist->getErrors(),'1',ErrCode::FAILURE);
                    }
                }
                $transaction->commit();
                return   $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);

            }

        }else{
           return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }
}