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
class BindAppForm extends UserAppBind
{

    const APP_BIND_LIMIT_NUM = 10;
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
            $this->addError('type', '绑定的类型错误');
        }

    }

    public function validatePhone($attribute)
    {

        $userId = \Yii::$app->user->id;
        $num = UserAppBind::find()->where(['user_id'=>$userId,'type'=>$this->type])->count();

        if($num >=self::APP_BIND_LIMIT_NUM)
        {
            $this->addError('phone_number', '最多绑定'.self::APP_BIND_LIMIT_NUM.'个'.UserAppBind::$typeArr[$this->type]);
        }

        $rows = UserAppBind::find()->where(['type'=>$this->type,'app_country_code'=>$this->country_code, 'app_phone'=>$this->phone_number])->one();
        if(!empty($rows)){
            $this->addError('phone_number', '该'.UserAppBind::$typeArr[$this->type].'已绑定，请更换再试');
        }


    }





    public function bindPotato()
    {
        if($this->validate())
        {
            $userbind = new UserAppBind();
            $userbind->create_at = time();
            $userbind->app_number = $this->country_code.$this->phone_number;
            $userbind->user_id = \Yii::$app->user->id;
            $userbind->app_country_code = $this->country_code;
            $userbind->app_phone = $this->phone_number;
            $userbind->type = $this->type;
            $sms = new SmsService();
            if($res = $sms->checkSms($userbind->app_number,$this->code))
            {
                return $this->jsonResponse([],$res,1,ErrCode::CODE_ERROR);
            }
            if($userbind->save())
            {
                return $this->jsonResponse([],'操作成功',0,ErrCode::SUCCESS);
            }else{
                return  $this->jsonResponse([],$userbind->getErrors(),1,ErrCode::DATA_SAVE_ERROR);
            }

        }else{
            return  $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

    }


    public function bindTelegram()
    {
        if($this->validate())
        {
            $userbind = new UserAppBind();
            $userbind->create_at = time();
            $userbind->app_number = $this->country_code.$this->phone_number;
            $userbind->user_id = \Yii::$app->user->id;
            $userbind->app_country_code = $this->country_code;
            $userbind->app_phone = $this->phone_number;
            $userbind->type = $this->type;
            $sms = new SmsService();
            if($res = $sms->checkSms($userbind->app_number,$this->code))
            {
                return $this->jsonResponse([],$res,1,ErrCode::CODE_ERROR);
            }
            if($userbind->save())
            {
                return $this->jsonResponse([],'操作成功',0,ErrCode::SUCCESS);
            }else{
                return  $this->jsonResponse([],$userbind->getErrors(),1,ErrCode::DATA_SAVE_ERROR);
            }

        }else{
            return  $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

    }

    public function sendMessage()
    {
        if($this->validate()) {


            $number = $this->country_code . $this->phone_number;
            if ($number) {
                $smsServer = new SmsService();
                return $smsServer->sendMessage($number);

            } else {
                return $this->jsonResponse([], '国码,号码不能为空', 1, ErrCode::COUNTRY_CODE_OR_PHONE_EMPTY);
            }
        }else{
            return  $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

    }


}