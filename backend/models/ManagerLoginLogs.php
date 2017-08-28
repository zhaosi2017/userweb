<?php

namespace backend\models;

//use Yii;
use backend\models\GActiveRecord;
use backend\models\Admin;
/**
 * This is the model class for table "manager_login_logs".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $status
 * @property string $login_time
 * @property string $login_ip
 */
class ManagerLoginLogs extends GActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manager_login_logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid'], 'required'],
            [['uid', 'status'], 'integer'],
            [['login_time'], 'safe'],
            [['login_ip'], 'string', 'max' => 15],
            [['address'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'status' => '登录状态',
            'login_time' => '登录时间',
            'login_ip' => 'Login Ip',
            'address' => '登陆地址',
        ];
    }

    public function getStatuses()
    {
        return [
            1 => '登录成功',
//            1 => '已解锁',
            2 => '密码错误',
//            3 => '验证错误',
//            4 => 'IP锁定中',
        ];
    }

    /**
     * @inheritdoc
     * @return ManagerLoginLogsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ManagerLoginLogsQuery(get_called_class());
    }

//    public function getManager()
//    {
//        return $this->hasOne(Admin::className(),['id' => 'uid']);
//    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(),['id' => 'uid']);
    }

}
