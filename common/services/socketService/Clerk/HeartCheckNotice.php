<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/4
 * Time: 上午10:10
 */
namespace common\services\socketService\Clerk;

use frontend\models\User;
use Yii;
use common\services\socketService\AbstruactClerk;
use common\services\socketService\Clerk\UidConn;
use frontend\models\ErrCode;


/** 心跳检测
 *
 * Class AgreeFriendNotice
 * @package common\services\socketService\Clerk
 */
class HeartCheckNotice extends  AbstruactClerk{


    public function stratClerk($server,  $frame , $data){

        $this->result['data'] = ['account'=>$data->account,'type'=>3];
        $this->result['status'] = 0;
        $this->result['code'] = ErrCode::WEB_SOCKET_HEART_CHECK;
        $this->result['message'] = '心跳正常';
        return $server->push($frame->fd, json_encode($this->result , JSON_UNESCAPED_UNICODE));

    }





}