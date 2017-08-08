<?php

namespace backend\models;

use Yii;
use backend\models\GActiveRecord;
/**
 * This is the model class for table "agency".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property integer $time
 * @property string $code
 */
class Agency extends GActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'time'], 'integer'],
            [['time'], 'required'],
            [['name', 'code'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'parent_id' => 'Parent ID',
            'time' => 'Time',
            'code' => 'Code',
        ];
    }
}
