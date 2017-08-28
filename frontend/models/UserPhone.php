<?php
namespace frontend\models;


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
class UserPhone extends FActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{user_phone}}';
    }

}
