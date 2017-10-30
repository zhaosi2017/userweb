<?php
namespace frontend\models\UserPhones;

use frontend\models\User;
use frontend\models\UserPhone;
use frontend\models\ErrCode;
use frontend\services\SmsService;
use Yii;
class UserPhoneAddForm extends UserPhone
{
    public $code;
    public function rules()
    {
        return [
            [['phone_country_code','user_phone_number','code'],'required'],
            [['phone_country_code','user_phone_number'],'integer'],
            ['phone_country_code','match','pattern'=>'/^[1-9]{1}[0-9]{0,5}$/','message'=>'{attribute}必须为1到6位纯数字'],
            ['user_phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'{attribute}必须为4到11位纯数字'],
        ];
    }
    public function setUserPhone()
    {
        if(!$this->validate(['phone_country_code','user_phone_number','code']))
        {
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }


        $userId = Yii::$app->user->id;
        $count = UserPhone::find()->where(['user_id'=>$userId])->count();
        if($count >= self::USER_PHONE_LIMIT_NUM)
        {
            return $this->jsonResponse([],'手机号添加不能超过'.self::USER_PHONE_LIMIT_NUM.'个','1',ErrCode::PHONE_TOTAL_NOT_OVER_TEN);
        }
        //$user = UserPhone ::find()->where(['user_id'=>$userId , 'phone_country_code'=>$this->phone_country_code,'user_phone_number'=>$this->user_phone_number])->one();
        $user = UserPhone ::find()->where(['phone_country_code'=>$this->phone_country_code,'user_phone_number'=>$this->user_phone_number])->one();
        //手机号去重
        $_User =  User::find()->where(['country_code'=>$this->phone_country_code,'phone_number'=>$this->user_phone_number])->one();


        if(!empty($user) || !empty($_User))
        {
            return $this->jsonResponse([],'手机号已被占用，不能添加','1',ErrCode::COUNTRY_CODE_PHONE_EXIST);
        }else{
            $key = $this->phone_country_code.$this->user_phone_number;
            $smsService = new SmsService();
            if($res = $smsService->checkSms($key,$this->code))
            {
                return $this->jsonResponse([],$res,'1',ErrCode::CODE_ERROR);
            }
            $this->user_id = $userId;
            $this->reg_time = time();
            if($this->save())
            {
                $smsService->delCode($key);
                return $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);
            }else{
                return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::DATA_SAVE_ERROR);
            }
        }


    }

}