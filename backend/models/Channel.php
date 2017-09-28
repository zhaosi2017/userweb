<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "channel".
 *
 * @property integer $id
 * @property string $name
 * @property string $img_url
 * @property string $gray_img_url
 * @property integer $sort
 * @property integer $create_at
 * @property integer $update_at
 */
class Channel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'img_url', 'gray_img_url', 'sort'], 'required'],
            [['create_at', 'update_at', 'sort'], 'integer'],
            ['sort','unique'],
            [['name', 'img_url', 'gray_img_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * 更新时间自动更新设置.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_at',
                'updatedAtAttribute' => 'update_at',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '渠道名称',
            'img_url' => '渠道图片',
            'gray_img_url' => '灰色图片',
            'sort' => '排序',
            'create_at' => '创建时间',
            'update_at' => '修改时间',
        ];
    }

}
