<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property string $number
 * @property string $aide_name
 * @property integer $group_id
 * @property integer $level
 * @property integer $type
 * @property string $company
 * @property integer $time
 * @property integer $admin_id
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'level', 'type', 'time', 'admin_id'], 'required'],
            [['group_id', 'level', 'type', 'time', 'admin_id'], 'integer'],
            [['code', 'name', 'number', 'aide_name', 'company'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'number' => 'Number',
            'aide_name' => 'Aide Name',
            'group_id' => 'Group ID',
            'level' => 'Level',
            'type' => 'Type',
            'company' => 'Company',
            'time' => 'Time',
            'admin_id' => 'Admin ID',
        ];
    }

    /**
     * @inheritdoc
     * @return CustomerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CustomerQuery(get_called_class());
    }
}
