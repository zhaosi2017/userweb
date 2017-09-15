<?php
namespace frontend\models\UserPhones;

use frontend\models\UserPhone;
use frontend\models\ErrCode;
use frontend\services\SmsService;
use Yii;
class UserPhoneForm extends UserPhone
{
    public function rules()
    {
        return [
            [['phone_country_code','user_phone_number'],'required'],
            [['phone_country_code','user_phone_number'],'integer'],
            ['phone_country_code','match','pattern'=>'/^[0-9]{2,6}$/','message'=>'{attribute}必须为2到6位纯数字'],
            ['user_phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'{attribute}必须为4到11位纯数字'],
        ];
    }
    public function checkUserPhone()
    {
        $userId = Yii::$app->user->id;
        if(!$this->validate('phone_country_code','user_phone_number'))
        {
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);

        }
        $count = UserPhone::find()->where(['user_id'=>$userId])->count();
        if($count >= SELF::USER_PHONE_LIMIT_NUM)
        {
            return $this->jsonResponse([],'手机号添加不能超过10个','1',ErrCode::PHONE_TOTAL_NOT_OVER_TEN);
        }
        $user = UserPhone ::find()->where(['user_id'=>$userId , 'phone_country_code'=>$this->phone_country_code,'user_phone_number'=>$this->user_phone_number])->one();
        if(!empty($user))
        {
            return $this->jsonResponse([],'该手机号已被添加，不能重复添加','1',ErrCode::COUNTRY_CODE_PHONE_EXIST);
        }
        $smsService = new SmsService();
        return $smsService->sendMessage($this->phone_country_code.$this->user_phone_number);
    }

}