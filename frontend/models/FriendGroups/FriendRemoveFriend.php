<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\FriendGroups;

use frontend\models\ErrCode;
use frontend\models\FActiveRecord;
use frontend\models\User;
use yii\base\Model;
use yii\db\Transaction;
use frontend\models\Friends\FriendsGroup;

/**
 * Class Friends
 * @package frontend\models\Friends
 * @property integer $id
 * @property integer $user_id
 * @property integer $friend_id
 * @property integer $create_at
 * @property integer $group_id
 * @property string  $remark
 * @property string  $extsion
 *
 */
class FriendRemoveFriend extends FriendsGroup
{


    public $gid;
    public $account;

    public function rules()
    {
        return [
            ['gid','integer'],
            [['gid','account'], 'required'],
            ['account','ValidateAccount'],

        ];
    }

    public function ValidateAccount()
    {
        $_user = User::findOne(['account'=>$this->account]);
        if(empty($_user))
        {
            $this->addError('account','该用户不存在');
        }
    }

    public function ValidateGid()
    {
        $userId = \Yii::$app->user->id;
        $_group = FriendsGroup::findOne(['id'=>$this->gid, 'user_id'=>$userId]);

        if(empty($_group))
        {
            $this->addError('account','该分组不存在');
        }
    }

    public function removeFriend()
    {
        if($this->validate(['gid','account']))
        {
            $userId = \Yii::$app->user->id;
            $_user = User::findOne(['account' => $this->account]);
            $_friend = Friends::findOne(['user_id' => $userId, 'friend_id' => $_user->id]);
            if (empty($_friend)) {
                return $this->jsonResponse([], '你们还不是好友，请先添加好友', '1', ErrCode::YOU_ARE_NOT_FRIENDS);
            }
            if($_friend->gid != $this->gid)
            {
                return $this->jsonResponse([], '该好友不在这个分组下面', '1', ErrCode::THE_FRIEND_NOT_IN_THE_GROUP);
            }
            $_friend->group_id = 0;
            if($_friend->save())
            {
                return $this->jsonResponse([],'操作成功',0,ErrCode::SUCCESS);
            }else{
                return $this->jsonResponse([],$_friend->getErrors(),1,ErrCode::DATA_SAVE_ERROR);
            }
        }else{
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

    }
}