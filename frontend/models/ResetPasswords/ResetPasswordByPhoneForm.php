<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\ResetPasswords;

use frontend\models\ErrCode;
use frontend\models\FActiveRecord;
use frontend\models\Friends\Friends;
use frontend\models\User;
use SebastianBergmann\CodeCoverage\Report\PHP;
use yii\base\Model;
use yii\db\Transaction;
use frontend\models\Friends\FriendsGroup;
use  frontend\services\SmsService;
use Yii;
use frontend\models\UserPhone;
use frontend\models\UrgentContact;
use frontend\models\SecurityQuestion;

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
class ResetPasswordByPhoneForm extends User
{


    public $country_code;
    public $phone;
    public $code;

    public function rules()
    {
        return [

            ['country_code', 'integer'],
            [['country_code', 'phone','code'], 'required'],
            ['country_code', 'match', 'pattern' => '/^[1-9]{1}[0-9]{0,5}$/', 'message' => '{attribute}必须为1到6位纯数字'],
            ['phone', 'match', 'pattern' => '/^[0-9]{4,11}$/', 'message' => '{attribute}必须为4到11位纯数字'],

        ];
    }



    public function resetPasswordPhone()
    {

        if($this->validate(['country_code','phone','code'])) {
            $redis = Yii::$app->redis;
            $key = $this->country_code.$this->phone;
//            $_code = $redis->get($key);
            $res = self::find()->where(['country_code' => $this->country_code, 'phone_number' => $this->phone])->one();
            if (empty($res)) {
                return $this->jsonResponse([],'手机号还没注册，请先注册','1',ErrCode::USER_NOT_EXIST);
            }
//            if(empty($_code) ||  $_code != $this->code)
//            {
//                return $this->jsonResponse([],'验证码过期/验证码错误','1',ErrCode::CODE_ERROR);
//            }
            $smsService = new SmsService();
            if($_sms = $smsService->checkSms($key,$this->code))
            {
                return $this->jsonResponse([],$_sms,1,ErrCode::CODE_ERROR);
            }
            $smsService->delCode($key);
//            $redis->exists($key) && $redis->del($key);
            $_tmp = md5($key.time());
            $_key = $key.self::REDIS_TOKEN;
            $expire = isset(Yii::$app->params['redis_expire_time']) ? Yii::$app->params['redis_expire_time'] : 120;
            $redis->setex($_key, $expire , $_tmp);
            return $this->jsonResponse(['token'=>$_tmp],'操作成功','0',ErrCode::SUCCESS);

        }else{
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }

}