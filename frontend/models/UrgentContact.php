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
class UrgentContact extends FActiveRecord
{

    const URGENT_CONTACT_LIMIT_NUM = 5;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_urgent_contact';
    }

    public function rules()
    {
        return [
            [['contact_country_code','contact_phone_number'],'integer'],
            ['contact_country_code','match','pattern'=>'/^[0-9]{2,6}$/','message'=>'{attribute}必须为2到6位纯数字'],
            ['contact_phone_number','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'{attribute}必须为4到11位纯数字'],
            ['contact_nickname','string','max'=>20],
        ];

    }

    public function attributeLabels()
    {
        return [
            'contact_country_code'=>'国码',
            'contact_phone_number'=>'手机号',
            'contact_nickname'=>'紧急联系人' ,
        ];
    }


    public function deleteUrgentContact()
    {
        if(empty($this->contact_country_code)){return $this->jsonResponse([],'国码不能为空','1',ErrCode::COUNTRY_CODE_EMPTY);}
        if(empty($this->contact_phone_number)){return $this->jsonResponse([],'手机号不能为空','1',ErrCode::PHONE_EMPTY);}
        if(!$this->validate('contact_country_code','contact_phone_number'))
        {
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
        $userId = Yii::$app->user->id;
        $user = self::find()->where(['user_id'=>$userId , 'contact_country_code'=>$this->contact_country_code,'contact_phone_number'=>$this->contact_phone_number])->one();

        if(empty($user))
        {
            return $this->jsonResponse([],'紧急联系人不存在','1',ErrCode::USER_NOT_EXIST);
        }else{
            if( $user->delete())
            {
                return $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);
            }else{
                return $this->jsonResponse([],$user->getErrors(),'1',ErrCode::DELETE_FAILURE);
            }
        }

    }

    public function setUrgentContact()
    {
        if(empty($this->contact_country_code)){return $this->jsonResponse([],'国码不能为空','1',ErrCode::COUNTRY_CODE_EMPTY);}
        if(empty($this->contact_phone_number)){return $this->jsonResponse([],'手机号不能为空','1',ErrCode::PHONE_EMPTY);}
        if(empty($this->contact_nickname)){return $this->jsonResponse([],'紧急联系人昵称不能为空','1',ErrCode::PHONE_EMPTY);}

        if(!$this->validate('contact_country_code','contact_phone_number'))
        {
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
        $userId = Yii::$app->user->id;

        $count = self::find()->where(['user_id'=>$userId ])->count();
        if($count >= self::URGENT_CONTACT_LIMIT_NUM)
        {
            return $this->jsonResponse([],'紧急联系人不能超过'.self::URGENT_CONTACT_LIMIT_NUM.'个','1',ErrCode::DATA_SAVE_ERROR);
        }
        $user = self::find()->where(['user_id'=>$userId ,
            'contact_country_code'=>$this->contact_country_code,
            'contact_phone_number'=>$this->contact_phone_number])->one();
        if(empty($user))
        {
            $this->user_id = $userId;
            $this->reg_time = time();
            if($this->save())
            {
                return $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);
            }else{
                return $this->jsonResponse([],'操作失败','1',ErrCode::DATA_SAVE_ERROR);
            }
        }
        return $this->jsonResponse([],'紧急联系人已经添加了','1',ErrCode::URGENT_CONTACT_EXIST);

    }
}