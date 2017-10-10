<?php

namespace common\models\system;

use common\ActiveRecord;
use Yii;

/**
 * This is the model class for table "Logs".
 *
 * @property integer $id
 * @property integer $adminid
 * @property string $url
 * @property string $table
 * @property string $content
 * @property integer $ctime
 */
class Logs extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['adminid'], 'integer'],
            [['adminid'], 'required'],
            [['table'], 'string', 'max' => 45],
            [['url'], 'string', 'max' => 100],
            [['content'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'adminid' => '管理员',
            'table' => '操作表',
            'ctime' => '操作时间',
            'url'=> '操作路由',
            'content' => '操作内容',
        ];
    }
}
