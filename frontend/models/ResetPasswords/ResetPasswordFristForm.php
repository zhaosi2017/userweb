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
class ResetPasswordFristForm extends User
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

    public function checkResetPassword()
    {
        if($this->validate(['country_code','phone']))
        {
            $res = self::find()->where(['country_code'=>$this->country_code,'phone_number'=>$this->phone ])->one();
            if(!empty($res))
            {
                $question = SecurityQuestion::find()->select(['id'])->where(['userid'=>$res->id])->one();
                $bool = empty($question) ? false: true;
                return $this->jsonResponse(['set-question'=> $bool],'操作成功','0',ErrCode::SUCCESS);

            }else{
                return $this->jsonResponse([],'手机号还没注册，请先注册','1',ErrCode::USER_NOT_EXIST);
            }
        }else{
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }
}