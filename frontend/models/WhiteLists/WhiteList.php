<?php
namespace frontend\model\WhiteLists;


use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use frontend\models\FActiveRecord;
use frontend\services\SmsService;


/**
 * User model
 *
 * @property integer $id
 * @property string $account
 */
class WhiteList extends FActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'white_list';
    }

    public function rules()
    {
        return [
            [['uid', 'white_uid'], 'integer'],
        ];

    }

    public function attributeLabels()
    {
        return [
            'uid' => '用户id',
            'white_uid' => '白名单用户',
        ];
    }

}