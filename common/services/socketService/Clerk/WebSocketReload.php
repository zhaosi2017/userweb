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
use frontend\models\ErrCode;

class WebSocketReload extends  AbstruactClerk{


    public function stratClerk($server,  $frame , $data){
        if(isset($data->key) && !empty($data->key) && $data->key == Yii::$app->params['web_socket_reload'])
        {
            $server->reload();
            $this->result['status'] = 0;
            $this->result['code'] = ErrCode::SUCCESS;
            $this->result['message'] = '重启成功';

        }else{
            $this->result['message'] = '非法操作';
        }
        $server->push($frame->fd,json_encode($this->result));
        return ;
    }





}