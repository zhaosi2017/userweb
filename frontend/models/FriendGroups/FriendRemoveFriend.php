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
    public  $account;

    public function rules()
    {
        return [
            ['gid','integer'],
            ['account', 'required'],

        ];
    }
}