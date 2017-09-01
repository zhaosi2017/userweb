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
            ['phone_country_code','match','pattern'=>'/^[0-9]{2,6}$/','message'=>'{attribute}必须为2到6位纯数字'],
            ['user_phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'{attribute}必须为4到11位纯数字'],
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
        $user = UserPhone ::find()->where(['user_id'=>$userId , 'phone_country_code'=>$this->phone_country_code,'user_phone_number'=>$this->user_phone_number])->one();
        if(!empty($user))
        {
            return $this->jsonResponse([],'该手机号已被添加，不能重复添加','1',ErrCode::COUNTRY_CODE_PHONE_EXIST);
        }
        $smsService = new SmsService();
        return $smsService->sendMessage($this->phone_country_code.$this->user_phone_number);
    }


    public function setUserPhone($code)
    {

        if(empty($this->phone_country_code)){return $this->jsonResponse([],'国码不能为空','1',ErrCode::COUNTRY_CODE_EMPTY);}
        if(empty($this->user_phone_number)){return $this->jsonResponse([],'手机号不能为空','1',ErrCode::PHONE_EMPTY);}
        if(!$this->validate('phone_country_code','user_phone_number'))
        {
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::FAILURE);
        }
        $key = $this->phone_country_code.$this->user_phone_number;
        $redis = Yii::$app->redis;
        $_code = $redis->get($key);
        if($_code && $_code == $code)
        {
            $userId = Yii::$app->user->id;
            $user = UserPhone ::find()->where(['user_id'=>$userId , 'phone_country_code'=>$this->phone_country_code,'user_phone_number'=>$this->user_phone_number])->one();
            if(!empty($user))
            {
                return $this->jsonResponse([],'该手机号已被添加，不能重复添加','1',ErrCode::COUNTRY_CODE_PHONE_EXIST);
            }else{
                $this->user_id = $userId;
                $this->reg_time = time();
                if($this->save())
                {
                    $redis->del($key);
                    return $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);
                }else{
                    return $this->jsonResponse([],'操作失败','1',ErrCode::DATA_SAVE_ERROR);
                }
            }

        }else{
            return $this->jsonResponse([],'验证码错误','1',ErrCode::CODE_ERROR);
        }



    }

}
