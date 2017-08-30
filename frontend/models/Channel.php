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
class Channel extends FActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel';
    }

}
