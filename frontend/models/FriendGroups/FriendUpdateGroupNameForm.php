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
class FriendUpdateGroupNameForm extends FriendsGroup {

    public $gid;
    public $name;

    public function rules()
    {
        return [
            ['gid','integer'],
            [['gid','name'],'required'],
            ['name','string'],
            ['gid','ValidateId'],
        ];
    }
    public function ValidateId()
    {
        $data = FriendsGroup::findOne($this->gid);
        if(empty($data))
        {
            $this->addError('gid','不存在该分组');
        }

    }



    public function updateGroupName()
    {
        if($this->validate(['gid','name']))
        {
            $userId= \Yii::$app->user->id;
            $_data = FriendsGroup::find()->where(['user_id'=>$userId,'group_name'=>$this->name])->one();

            if(!empty($_data))
            {
                if($this->gid != $_data['id'])
                {
                    return $this->jsonResponse('','该组名已存在！',1,ErrCode::GROUP_NAME_EXIST);
                }
            }
            $_group  = FriendsGroup::findOne($this->gid);
            $_group->group_name = $this->name;

            if($_group->save())
            {
                return $this->jsonResponse('','操作成功',0,ErrCode::SUCCESS);
            }else{
                return $this->jsonResponse('',$_group->getErrors(),1,ErrCode::DATA_SAVE_ERROR);
            }
        }else{
            return $this->jsonResponse('',$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);

        }
    }




}
