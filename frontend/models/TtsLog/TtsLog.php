<?php
namespace frontend\models\TtsLog;


use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use frontend\models\FActiveRecord;
use frontend\services\SmsService;
use frontend\models\Friends\Friends;
use frontend\models\User;
use frontend\models\ErrCode;


/**
 * User model
 *
 * @property integer $id
 * @property string $type 交互类型
 * @property string $url  交互地址
 * @property string $data 交互数据
 * @property string $object 交互发生时 对象的状态
 * @property string $time  交互时间
 * @property string $number 拨打的电话号码
 * @property string $call_id 呼叫的id
 *
 */
class TtsLog extends FActiveRecord
{
    const LOG_TTS_TYPE_CALL_REQUEST   = 1; //呼叫请求
    const LOG_TTS_TYPE_CALL_RESPONSE  = 2; //呼叫回应
    const LOG_TTS_TYPE_EVENT_REQUEST  = 3; //回调请求
    const LOG_TTS_TYPE_EVENT_RESPONSE = 4; //回调回应


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tts_log';
    }

    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [['url' , 'data', 'object' , 'time' , 'number' , 'call_id'] , 'string']
        ];

    }




}