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
            'name' => '名称',
            'parent_id' => '上级ID',
            'time' => '时间',
            'code' => '编号',
        ];
    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }
}
