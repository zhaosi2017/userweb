<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/10/2
 * Time: 下午3:45
 */

namespace  frontend\models\MessageCatch;
use frontend\models\FActiveRecord;

/**
 * Class MessageCatch
 * @package frontend\models\MessageCatch
 *
 * @property  int $id
 * @property  int $ucode
 * @property  string $message
 * @property  int $begin_time
 * @property  int $end_time
 * @property  int $status
 * @property  int $send_time
 */
class MessageCatch extends FActiveRecord
{
    const CONST_MESSAGE_CATCH_STATUS_Y = 1; //已经发送的消息
    const CONST_MESSAGE_CATCH_STATUS_N = 0; //未发送的消息

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message_catch';
    }

}
