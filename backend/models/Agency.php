<?php

namespace backend\models;

use Yii;
use backend\models\GActiveRecord;
use yii\helpers\ArrayHelper;
use backend\models\Admin;
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
    const TOP_AGENCY = '顶级单位';
    const NORMAL_STATUS = 0; //正常状态
    const INVALID_STATUS = 1;//失效状态

    public static $AGENCY_STATUS = [
        SELF::NORMAL_STATUS  => '正常状态',
        SELF::INVALID_STATUS => '失效状态',
    ];

    private $codeA = 'A';

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
            [['parent_id','status','update_id'], 'integer'],
            [['name'], 'string', 'max' => 32],
            ['name','checkName','on'=>['create']],
            ['name','validateName','on'=>['update']],
            [['code'],'unique','on'=>['insert_update_code']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $self = [
            'create'=>['parent_id','name','status'],
            'udpate'=>['parent_id','name','code','status','udpate_id'],
            'insert_update_code'=>['code'],
        ];
        return array_merge($scenarios,$self);
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
            'create_at' => '创建时间',
            'udpate_at' => '更新时间',
            'code' => '编号',
            'status'=>'状态',
            'update_id'=>'更新者ID',
        ];
    }

    public function checkName($attribute)
    {
        $res = self::findOne(['name'=>$this->name]);
        if($res){
            $this->addError('name','名称已经存在，请重新输入');
        }
    }

    public function validateName($attribute)
    {
        $res = self::find()->select(['id'])->where(['name'=>$this->name])->all();
        if(!empty($res)){
            foreach ($res as $k => $v)
            {
                if($v->id != $this->id)
                {
                    return $this->addError('name','名称已经存在，请重新输入');
                    break;
                }
            }
        }

    }

    public function create()
    {

        $this->setScenario('create');
        $this->admin_id = Yii::$app->user->id? Yii::$app->user->id:0;
        if($this->save()){
            $this->setScenario('insert_update_code');
            $this->code = $this->makeCode();
            $this->update();
            return true;
        }
        return false;

    }

    public function beforeSave($insert)
    {
        if($this->isNewRecord)
        {
            $this->admin_id  =  $this->update_id  = Yii::$app->user->id? Yii::$app->user->id:0;
            $this->update_at =  $this->create_at = time();
        }else{
            $this->update_id = Yii::$app->user->id? Yii::$app->user->id:0;
            $this->update_at = time();
        }
        return true;
    }

    //机构编号生成函数
    private function makeCode()
    {
       return $this->codeA.(10000 + $this->id);
    }
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    public function getHeader()
    {
        return $this->hasOne(Agency::className(),['id'=>'parent_id']);
    }

    public function getUpdater()
    {
        return $this->hasOne(Admin::className(),['id'=>'update_id']);
    }



    public function getCategories($param = [])
    {
        $con = [];
        if(!empty($param) && is_array($param))
        {
            $con = $param;
        }
        $data = self::find()->where($con)->all();
        $data = ArrayHelper::toArray($data);
        return $data;
    }

    public static function getTree($data,$pid = 0,$lev = 1)
    {
        $tree = [];
        foreach($data as $value){
            if($value['parent_id'] == $pid){
                $value['name'] = str_repeat('|___',$lev).$value['name'];
                $tree[] = $value;
                $tree = array_merge($tree,self::getTree($data,$value['id'],$lev+1));
            }
        }
        return $tree;
    }

    public function getOptions($param = [])
    {
        $data = $this->getCategories($param);
        $tree = $this->getTree($data);
        $list = [Agency::TOP_AGENCY];
        foreach($tree as $value){
            $list[$value['id']] = $value['name'];
        }
        return $list;
    }
}
