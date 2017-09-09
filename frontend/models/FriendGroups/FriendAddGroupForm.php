<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\Friends;

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
class FriendAddGroupForm extends FriendsGroup {


    public $name;

    public function rules()
    {
        return [
            ['name','required'],
            ['name','string'],
        ];
    }

    public function addGroup()
    {
        $userId= \Yii::$app->user->id;
        $_data = FriendsGroup::find()->where(['user_id'=>$userId,'group_name'=>$this->name])->one();
        if(!empty($_data))
        {
            return $this->jsonResponse('','该组名已存在！',1,ErrCode::GROUP_NAME_EXIST);
        }
        $friendGroup = new FriendsGroup();
        $friendGroup->user_id = $userId;
        $friendGroup->group_name = $this->name;
        if($friendGroup->save())
        {
            return $this->jsonResponse('','操作成功',0,ErrCode::SUCCESS);
        }else{
            return $this->jsonResponse('',$friendGroup->getErrors(),1,ErrCode::GROUP_NAME_EXIST);
        }
    }

}
