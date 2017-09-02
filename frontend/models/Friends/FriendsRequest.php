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
 * Class FriendsRequest
 * @package frontend\models\Friends
 * @property  integer $id
 * @property  integer $from_id
 * @property  integer $to_id
 * @property  integer $status
 * @property  integer $create_at
 * @property  string  $note
 */
class FriendsRequest extends FActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'friends_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['from_id','to_id','status','create_at'] ,'integer'],
            ['note','string','max'=>255],
        ];
    }



}