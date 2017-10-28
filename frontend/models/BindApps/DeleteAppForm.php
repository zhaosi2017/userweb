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

            ['country_code', 'integer'],
            ['phone_number','string'],
            [['country_code'],'required'],
            [['phone_number'],'required','message'=>'请输入4-11位手机号码'],
            ['type','integer'],
            [['type'],'required'],
            ['country_code','match','pattern'=>'/^[1-9]{1}[0-9]{0,5}$/','message'=>'{attribute}必须为1到6位纯数字'],
            ['phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'请输入4-11位手机号码'],
            ['type','ValidateType'],
            ['phone_number','validatePhone'],


        ];
    }


    public function ValidateType($attribute)
    {
        if(!array_key_exists($this->type,BindAppForm::$typeArr)){
            $this->addError('type', '不能删除该类型错误');
        }

    }

    public function validatePhone($attribute)
    {
        $rows = UserAppBind::find()->where(['type'=>$this->type,'app_country_code'=>$this->country_code, 'app_phone'=>$this->phone_number])->one();
        if(empty($rows)) {
            $this->addError('phone_number', '该数据不存在');
        }

    }


    public function deleteApp()
    {
        if($this->validate())
        {
           $userAppBind =  UserAppBind::findOne(['type'=>$this->type,'app_country_code'=>$this->country_code, 'app_phone'=>$this->phone_number]);
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