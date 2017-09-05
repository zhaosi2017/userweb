<?php
namespace frontend\models\Friends;

use frontend\models\ErrCode;
use frontend\models\User;
use yii\base\Model;
use frontend\models\Friends\Friends;

class FriendsRemarkForm extends Friends
{
    public $account;
    public  $remark;
    public function rules()
    {
          return [
                [ 'account','required'],
                [ 'remark','required'],
                [ 'account' ,'integer'],
                ['account','ValidateAccount'],
                ['remark','string','max'=>20],
              ];
    }

    public function ValidateAccount()
    {
        $user = User::findOne(['account'=>$this->account]);
        if(empty($user))
        {
            $this->addError('account','优码不存在');
        }
    }
    /**
     * 修改联系好友的备注
     */
    public function updateFriendRemark()
    {
        if($this->validate(['account','remark']))
        {
            $identity = \Yii::$app->user->identity;
            if($identity->account == $this->account)
            {
                return $this->jsonResponse([],'不能给自己修改备注','1',ErrCode::FAILURE);
            }
            $user = User::findOne(['account'=>$this->account]);
           $friend =  Friends::findOne(['user_id'=>\Yii::$app->user->id,'friend_id'=>$user->id]);
           if(empty($friend))
           {
               return $this->jsonResponse([],'你和该优客不是好友','1',ErrCode::FAILURE);
           }
           $friend->remark = $this->remark;
           if($friend->save())
           {
               return $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);
           }else{
               return $this->jsonResponse([],$friend->getErrors(),'1',ErrCode::FAILURE);
           }

        }else{
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }

    }
}