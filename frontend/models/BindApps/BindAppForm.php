<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\BindApps;

use frontend\models\ErrCode;
use frontend\models\FActiveRecord;
use frontend\models\Friends\Friends;
use frontend\models\User;
use frontend\models\UserAppBind;
use frontend\models\UserPhone;
use yii\base\Model;
use yii\db\Transaction;
use frontend\models\Friends\FriendsGroup;
use  frontend\services\SmsService;

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
class BindAppForm extends UserAppBind
{

    const APP_BIND_LIMIT_NUM = 10;
    public $code;

    public function rules()
    {
        return [

            [['code'],'required'],
            ['code','validatePhone'],


        ];
    }

    public function ValidateType($attribute)
    {
        if(!array_key_exists($this->type,UserAppBind::$typeArr)){
            $this->addError('type', '绑定的类型错误');
        }

    }

    public function validatePhone($attribute)
    {

        $userId = \Yii::$app->user->id;
        $num = UserAppBind::find()->where(['user_id'=>$userId,'type'=>$this->type])->count();

        if($num >=self::APP_BIND_LIMIT_NUM)
        {
            $this->addError('phone_number', '最多绑定'.self::APP_BIND_LIMIT_NUM.'个账号');
        }



    }





    public function bindPotato()
    {
        if($this->validate())

        {

            $redis = \Yii::$app->redis;
            $redis->hostname = \Yii::$app->params['remote_web_redis_host'];
            $redis->password =  \Yii::$app->params['remote_web_reids_pass'];
            $redis->database =  \Yii::$app->params['redis_web_redis_db'];
            $redis->port =  \Yii::$app->params['redis_web_redis_port'];

            if (!$redis->exists($this->code) || !($potatoData= $redis->get($this->code))) {
                return $this->jsonResponse([],'验证码错误',1,ErrCode::CODE_ERROR);
            }
            $data = \Yii::$app->security->decryptByKey(base64_decode($potatoData), \Yii::$app->params['potato']);
            $dataArr = explode('-', $data);
            $_userAppBind = UserAppBind::find()->where(['type'=>UserAppBind::APP_TYPE_POTATO,'app_userid'=>$dataArr['1']])->one();
            if(!empty($_userAppBind))
            {
                return $this->jsonResponse([],'该potato已被占用',1,ErrCode::POTATO_ACCOUNT_EXISTS);
            }
            if ($dataArr[0] == \Yii::$app->params['potato_pre']) {

                $userBind = new UserAppBind ();
                $userBind->app_userid = $dataArr['1'];
                $userBind->user_id = \Yii::$app->user->id;
                $userBind->app_number = $dataArr['2'];
                $userBind->app_name   = $dataArr['3'];
                $userBind->type       = UserAppBind::APP_TYPE_POTATO;
                if ($userBind->save()) {
                    $redis->del($this->code);
                    return $this->jsonResponse([],'操作成功',0,ErrCode::SUCCESS);
                }else{
                    return  $this->jsonResponse([],$userBind->getErrors(),1,ErrCode::DATA_SAVE_ERROR);
                }

            } else {
                return $this->jsonResponse([],'验证码错误',1,ErrCode::CODE_ERROR);
            }

        }else{
            return  $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

    }


    public function bindTelegram()
    {
        if ($this->validate()) {
            $redis = \Yii::$app->redis;
            $redis->hostname = \Yii::$app->params['remote_web_redis_host'];
            $redis->password =  \Yii::$app->params['remote_web_reids_pass'];
            $redis->database =  \Yii::$app->params['redis_web_redis_db'];
            $redis->port =  \Yii::$app->params['redis_web_redis_port'];


            if (!($redis->exists($this->code)) || !($telegramData = $redis->get($this->code))) {
                return $this->jsonResponse([], '验证码错误', 1, ErrCode::CODE_ERROR);
            }
            $data = \Yii::$app->security->decryptByKey(base64_decode($telegramData), \Yii::$app->params['telegram']);
            $dataArr = explode('-', $data);
            if(!isset($dataArr['1']) || !isset($dataArr['2']) || !isset($dataArr['3']))
            {
                return $this->jsonResponse([], '验证码错误', 1, ErrCode::CODE_ERROR);
            }
            $_userAppBind = UserAppBind::find()->where(['type' => UserAppBind::APP_TYPE_TELEGRAM,  'app_userid' => $dataArr['1']])->one();
            if (!empty($_userAppBind)) {
                return $this->jsonResponse([], '该telegram已被占用', 1, ErrCode::POTATO_ACCOUNT_EXISTS);
            }

            if ($dataArr[0] == \Yii::$app->params['telegram_pre']) {
                $userBind = new UserAppBind ();
                $userBind->app_userid = $dataArr['1'];
                $userBind->user_id = \Yii::$app->user->id;
                $userBind->app_number = $dataArr['2'];
                $userBind->app_name = $dataArr['3'];
                $userBind->type = UserAppBind::APP_TYPE_TELEGRAM;
                if ($userBind->save()) {
                    \Yii::$app->redis->del($this->code);
                    return $this->jsonResponse([], '操作成功', 0, ErrCode::SUCCESS);
                } else {
                    return $this->jsonResponse([], $userBind->getErrors(), 1, ErrCode::DATA_SAVE_ERROR);
                }

            } else {
                return $this->jsonResponse([], '验证码错误', 1, ErrCode::CODE_ERROR);
            }

        } else {
            return $this->jsonResponse([], $this->getErrors(), 1, ErrCode::VALIDATION_NOT_PASS);
        }


    }

    public function sendMessage()
    {
        if($this->validate()) {


            $number = $this->country_code . $this->phone_number;
            if ($number) {
                $smsServer = new SmsService();
                return $smsServer->sendMessage($number);

            } else {
                return $this->jsonResponse([], '国码,号码不能为空', 1, ErrCode::COUNTRY_CODE_OR_PHONE_EMPTY);
            }
        }else{
            return  $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

    }


}