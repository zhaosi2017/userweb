<?php
namespace frontend\models;


use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use frontend\models\FActiveRecord;
use frontend\services\SmsService;


/**
 * User model
 *
 * @property integer $id
 * @property string $account
 */
class UserPhone extends FActiveRecord
{

    const USER_PHONE_LIMIT_NUM = 10;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_phone';
    }

    public function rules()
    {
        return [
            [['phone_country_code','user_phone_number'],'integer'],
            ['phone_country_code','match','pattern'=>'/^[1-9]{1}[0-9]{0,5}$/','message'=>'{attribute}必须为1到6位纯数字'],
            ['user_phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'{attribute}必须为4到11位纯数字'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'phone_country_code'=>'国码',
            'user_phone_number'=>'手机号',
        ];
    }


    /**
     * 设置手机第一步 ，校验手机国码，并发送短信
     */
    public function checkUserPhone()
    {
        if(empty($this->phone_country_code)){return $this->jsonResponse([],'国码不能为空','1',ErrCode::COUNTRY_CODE_EMPTY);}
        if(empty($this->user_phone_number)){return $this->jsonResponse([],'手机号不能为空','1',ErrCode::PHONE_EMPTY);}
        $userId = Yii::$app->user->id;


        if(!$this->validate('phone_country_code','user_phone_number'))
        {
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::FAILURE);

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




    public function deleteUserPhone()
    {
        if(empty($this->phone_country_code)){return $this->jsonResponse([],'国码不能为空','1',ErrCode::COUNTRY_CODE_EMPTY);}
        if(empty($this->user_phone_number)){return $this->jsonResponse([],'手机号不能为空','1',ErrCode::PHONE_EMPTY);}
        if(!$this->validate('phone_country_code','user_phone_number'))
        {
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
        $userId = Yii::$app->user->id;
        $user = UserPhone ::find()->where(['user_id'=>$userId , 'phone_country_code'=>$this->phone_country_code,'user_phone_number'=>$this->user_phone_number])->one();

        if(empty($user))
        {
            return $this->jsonResponse([],'用户不存在','1',ErrCode::USER_NOT_EXIST);
        }else{
            if( $user->delete())
            {
                return $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);
            }else{
                return $this->jsonResponse([],$user->getErrors(),'1',ErrCode::DELETE_FAILURE);
            }
        }

    }

}
