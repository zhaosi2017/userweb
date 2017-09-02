<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:34
 */
namespace frontend\models\Friends;

use frontend\models\FActiveRecord;

/**
 * Class FriendsGroup
 * @package frontend\models\Friends
 * @property  integer $id
 * @property  integer $user_id
 * @property  integer $create_at
 * @property  integer $group_name
 */
class FriendsGroup extends FActiveRecord {


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'friends_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['user_id','create_at'] ,'integer'],
            ['group_name','string','max'=>128],
        ];
    }

}