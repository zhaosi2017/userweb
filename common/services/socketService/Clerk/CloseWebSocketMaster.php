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

class CloseWebSocketMaster extends  AbstruactClerk{
    /**
     * @var callu
     */
    public $app;
    public $server;     //当前服务
    public $fd;         //当前fd

    const UID_CONN_ACCOUNT = 'uid_conn_account';
    const UID_CONN_FD = 'uid_conn_fd';

    public function stratClerk($server,  $frame , $data){
        if(isset($data->key) && !empty($data->key) && $data->key == Yii::$app->params['web_socket_reload'])
        {
            foreach ($server->connections as $conn)
            {
               // $server->pause($conn);
                $server->close($conn);
            }
            $server->shutdown();
            return ;

        }else{
            $this->result['message'] = '非法操作';
        }
        $server->push($frame->fd,json_encode($this->result));
        return ;
    }





}