<?php
namespace frontend\models\UserLoginLogs;


use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use frontend\models\FActiveRecord;



/**
 * User model
 *
 * @property integer $id
 * @property string $account
 */
class UserLoginLog extends FActiveRecord
{

    public function rules()
    {
        return [
            [['user_id','login_time'], 'integer'],
            [['address'],'string'],
            [['longitude','latitude','login_ip'],'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_login_log';
    }

}
