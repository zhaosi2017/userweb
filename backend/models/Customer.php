<?php

namespace backend\models;

use Yii;
use backend\models\GActiveRecord;
use backend\models\AgencySearch;
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
class Customer extends GActiveRecord
{
    const GROUP_AGENCY = 1;
    const COOPERATION_UNIT = 2;
    const SOCIAL_CLIENTS = 3;
    public static $customerType = [
        self::GROUP_AGENCY     => '集团机构',
        self::COOPERATION_UNIT => '合作单位',
        self::SOCIAL_CLIENTS   => '社会客户',
    ];
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
            'code' => '客户编号',
            'name' => '客户主要名称',
            'number' => '客户代号',
            'aide_name' => '辅助名称',
            'group_id' => '客户上级单位',
            'level' => '级别',
            'type' => '类型',
            'company' => 'Company',
            'time' => '时间',
            'admin_id' => '管理员ID',
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
