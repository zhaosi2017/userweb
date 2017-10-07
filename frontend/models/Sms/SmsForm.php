<?php
namespace frontend\models\Sms;

use yii;
use frontend\services\SmsService;
use frontend\models\ErrCode;
class SmsForm extends yii\base\Model
{

    public $country_code;
    public $phone;

    public function rules()
    {
        return [

            ['country_code', 'integer'],
            [['country_code', 'phone'], 'required'],
            ['country_code', 'match', 'pattern' => '/^[1-9]{1}[0-9]{0,5}$/', 'message' => '{attribute}必须为1到6位纯数字'],
            ['phone', 'match', 'pattern' => '/^[0-9]{4,11}$/', 'message' => '{attribute}必须为4到11位纯数字'],

        ];
    }

    public function send()
    {
        if($this->validate())
        {
            $number = $this->country_code.$this->phone;
            if($number){
                $smsServer = new SmsService();
                return $smsServer->sendMessage($number);
            }else{
                return $this->jsonResponse([],'国码,号码不能为空',1,ErrCode::COUNTRY_CODE_OR_PHONE_EMPTY);
            }
        }else{
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }
    }

    /**
     * @return mixed
     */
    public function jsonResponse($data,$message,$status = 0,$code)
    {
        return ['data'=>$data, 'message'=>$message, 'status'=>$status, 'code'=>$code];
    }
}