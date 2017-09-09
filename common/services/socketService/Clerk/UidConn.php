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

class UidConn extends  AbstruactClerk{
    /**
     * @var callu
     */
    public $app;
    public $server;     //当前服务
    public $fd;         //当前fd

    const UID_CONN_ACCOUNT = 'uid_conn_account';
    const UID_CONN_FD = 'uid_conn_fd';

    public function stratClerk($server,  $frame , $data){
        //用户连接  保存用户 account -- fd  对应关系
        $redis = Yii::$app->redis;


        if(isset($data->account) && $data->account && isset($data->token)) {
            $_data = User::find()->select(['id', 'token'])->where(['account' => $data->account])->one();
            if (!empty($_data) && $_data['token'] == $data->token) {
                $redis->set(self::UID_CONN_ACCOUNT . $data->account, $frame->fd);
                $redis->set(self::UID_CONN_FD . $frame->fd, $data->account);
                $server->push($frame->fd,json_encode(['data'=>'','status'=>0,'message'=>'连接成功','code'=>'0000','type'=>'0']));
            }

        }
        return ;
    }





}