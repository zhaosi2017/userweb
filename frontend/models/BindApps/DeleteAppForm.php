<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\BindApps;

use frontend\models\ErrCode;
use frontend\models\FActiveRecord;
use frontend\models\Friends\Friends;
use frontend\models\User;
use frontend\models\UserAppBind;
use frontend\models\UserPhone;
use yii\base\Model;
use yii\db\Transaction;
use frontend\models\Friends\FriendsGroup;
use  frontend\services\SmsService;

/**
 * Class Friends
 * @package frontend\models\Friends
 * @property integer $id
 * @property integer $user_id
 * @property integer $friend_id
 * @property integer $create_at
 * @property integer $group_id
 * @property string  $remark
 * @property string  $extsion
 *
 */
class DeleteAppForm extends UserAppBind
{

    public $country_code;
    public $phone_number;
    public $type;
    public $code;

    public function rules()
    {
        return [

            ['id','integer'],
            ['type','integer'],
            [['id'],'required'],
            [['type'],'required'],
            ['type','ValidateType'],
            ['id','checkId'],

        ];
    }


    public function ValidateType($attribute)
    {
        if(!array_key_exists($this->type,BindAppForm::$typeArr)){
            $this->addError('type', '不能删除该类型错误');
        }

    }



    public function checkId($attribute)
    {
        $userId = \Yii::$app->user->id;
        $rows = UserAppBind::find()->where(['user_id'=>$userId,'type'=>$this->type,'id'=>$this->id])->one();
        if(empty($rows)) {
            $this->addError('phone_number', '该数据不存在');
        }

    }


    public function deleteApp()
    {
        if($this->validate())
        {
            $userId = \Yii::$app->user->id;
           $userAppBind =  UserAppBind::findOne(['user_id'=>$userId,'type'=>$this->type,'id'=>$this->id]);
           if($userAppBind->delete())
           {
               return  $this->jsonResponse([],'操作成功',0,ErrCode::SUCCESS);
           }else{
               return  $this->jsonResponse([],$this->getErrors(),1,ErrCode::DELETE_FAILURE);
           }

        }else{
            return  $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }
    }
}