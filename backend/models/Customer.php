<?php

namespace backend\models;

use Yii;
use backend\models\GActiveRecord;
use backend\models\AgencySearch;
use backend\models\Admin;
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

    const LEVEL_ONE = 1;
    const LEVEL_TWO = 2;
    const LEVEL_THREE = 3;
    const LEVEL_FOUR = 4;
    const LEVEL_FIVE = 5;
    const LEVEL_SIX = 6;
    const LEVEL_SEVEN = 7;
    const LEVEL_EIGHT= 8;
    const LEVEL_NINE = 9;

    const NORMAL_STATUS = 0;
    const INVALID_STATUS = 1;

    //客户编号的默认首字符
    private $initCode = 9999;
    private $codeC = 'C';

    public static $customerType = [
        self::GROUP_AGENCY     => '集团机构',
        self::COOPERATION_UNIT => '合作单位',
        self::SOCIAL_CLIENTS   => '社会客户',
    ];

    public $statusArr = [
        self::NORMAL_STATUS =>'正常状态',
        self::INVALID_STATUS =>'失效状态',
    ];

    public static $levelArr = [
        self::LEVEL_ONE => '一级',
        self::LEVEL_TWO => '二级',
        self::LEVEL_THREE => '三级',
        self::LEVEL_FOUR => '四级',
        self::LEVEL_FIVE => '五级',
        self::LEVEL_SIX => '六级',
        self::LEVEL_SEVEN => '七级',
        self::LEVEL_EIGHT => '八级',
        self::LEVEL_NINE => '九级',
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
            [['group_id', 'level','name', 'number', 'aide_name','type','company'], 'required'],
            [['group_id', 'level', 'type'], 'integer'],
            [[ 'name', 'number', 'aide_name', 'company'], 'string', 'max' => 32],
            [['code'],'unique','on'=>['insert_update_code']],
            [['status'],'number','on'=>['delete']],

        ];
    }


    public function scenarios()
    {
        $self = [
            'insert_update_code'=>['code'],
            'delete'=>['status'],
        ];

        return array_merge(parent::scenarios(),$self); // TODO: Change the autogenerated stub
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
            'company' => '集团',
            'admin_id' => '管理员ID',
            'update_id'=> '修改管理员ID',
            'create_at'=>'创建时间',
            'update_at'=>'修改时间'
        ];
    }



    public function create()
    {

        if($this->save())
        {
            $this->code = $this->codeC.($this->initCode + $this->id) ;
            $this->setScenario('insert_update_code');
            $this->update();
            return true;
        }else{
           return $this->addError('name','操作失败');
        }

    }

    public function beforeSave($insert)
    {
       if($this->isNewRecord)
       {
           $this->update_at = $this->create_at = time();
           $this->admin_id = Yii::$app->user->id ? Yii::$app->user->id: 0;
           $this->update_id = Yii::$app->user->id ? Yii::$app->user->id: 0;
       }else{
           $this->update_at = time();
           $this->update_id = Yii::$app->user->id ? Yii::$app->user->id: 0;
       }
       return true;
    }

    public function getAgency()
    {
        return $this->hasOne(Agency::className(),['id'=>'group_id']);
    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(),['id'=>'admin_id']);
    }
    public function getUpdate()
    {
        return $this->hasOne(Admin::className(),['id'=>'update_id']);
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
