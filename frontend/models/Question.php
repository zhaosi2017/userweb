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
class Question extends FActiveRecord
{
    const GROUP_ONE = 1;
    const GROUP_TWO = 2;
    const GROUP_THREE = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question';
    }

}
