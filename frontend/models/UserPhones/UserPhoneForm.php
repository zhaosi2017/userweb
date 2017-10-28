<?php
namespace frontend\models\UserPhones;

use frontend\models\UserPhone;
use frontend\models\ErrCode;
use frontend\services\SmsService;
use Yii;
use frontend\models\User;
class UserPhoneForm extends UserPhone
{
    public function rules()
    {
        return [
            [['phone_country_code','user_phone_number'],'required'],
            [['phone_country_code','user_phone_number'],'integer'],
            ['phone_country_code','match','pattern'=>'/^[1-9]{1}[0-9]{0,5}$/','message'=>'{attribute}必须为1到6位纯数字'],
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
        $user = UserPhone ::find()->where(['phone_country_code'=>$this->phone_country_code,'user_phone_number'=>$this->user_phone_number])->one();
        $_user = User ::find()->where(['country_code'=>$this->phone_country_code,'phone_number'=>$this->user_phone_number])->one();

        if(!empty($user) || !empty($_user))
        {
            return $this->jsonResponse([],'该手机号已被占用，不能添加','1',ErrCode::COUNTRY_CODE_PHONE_EXIST);
        }
        $smsService = new SmsService();
        return $smsService->sendMessage($this->phone_country_code.$this->user_phone_number);
    }

}