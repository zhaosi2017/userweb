<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\UserForm;

use frontend\models\FActiveRecord;
use frontend\models\User;
USE frontend\models\ErrCode;
USE yii;
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
class ResetPasswordForm extends User
{

    const  REDIS_TOKEN ='token';
    public $pass;
    public $token;
    public $phone;

    public function rules()
    {
        return [
            ['country_code','required'],
            ['phone','required'],
            ['pass','required'],
            ['token','required'],
            [['country_code','phone'], 'integer'],
            ['pass','string'],
            ['country_code','match','pattern'=>'/^[0-9]{2,6}$/','message'=>'{attribute}必须为2到6位纯数字'],
            ['phone','match','pattern'=>'/^[0-9]{4,11}$/','message'=>'{attribute}必须为4到11位纯数字'],
            ['pass', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{8,}$/','message'=>'密码至少包含8个字符，至少包括以下2种字符：大写字母、小写字母、数字、符号'],

        ];
    }

    public function resetPasswords()
    {
        if($this->validate())
        {
            $redis = Yii::$app->redis;
            $key = $this->country_code.$this->phone.self::REDIS_TOKEN;
            $_token = $redis->get($key);
            $user =  User::findOne([
                         'country_code'=>$this->country_code,
                         'phone_number'=>$this->phone
                        ]
                    );
           if(empty($user))
           {
               return  $this->jsonResponse([],'用户不存在','1',ErrCode::USER_NOT_EXIST );
           }

            if(empty($this->token) || $this->token != $_token)
            {
                return  $this->jsonResponse([],'非法操作','0',ErrCode::ILLEGAL_OPERATION);
            }
            $user->password = Yii::$app->getSecurity()->generatePasswordHash($this->pass);
            if($user->validate('country_code','phone_number','password') && $user->save())
            {
                $redis->del($key);
                return  $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);

            }else{
                return  $this->jsonResponse([],$user->getErrors(),'1',ErrCode::DATA_SAVE_ERROR);
            }

        }else{
            return  $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }



}