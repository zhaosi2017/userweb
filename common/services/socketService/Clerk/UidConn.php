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
                /**********************************************************/
                    $this->bindUcode($server, $frame->fd , $data->account); //绑定优码和fd的对应关系
                /**********************************************************/
                if($redis->exists(self::UID_CONN_ACCOUNT . $data->account))
                {
                   $_fd = $redis->get(self::UID_CONN_ACCOUNT . $data->account);
                   if($_fd)
                   {
                       $this->result['status'] = 0;
                       $this->result['message'] = '您的账号在其他地方登录！';
                       $this->result['code'] = ErrCode::YOU_ACCOUNT_LOGIN_IN_OTHER_DEVICE;
                       if($server->exist($_fd))
                       {
                           $server->push($_fd,json_encode($this->result));
                           $server->close($_fd);
                       }
                       $redis->del(self::UID_CONN_ACCOUNT . $data->account);
                       $redis->del(self::UID_CONN_FD.$_fd);
                   }
                }
                $redis->set(self::UID_CONN_ACCOUNT . $data->account, $frame->fd);
                $redis->set(self::UID_CONN_FD . $frame->fd, $data->account);
                $this->result['status'] = 0;
                $this->result['message'] = '连接成功';
                $this->result['code']= ErrCode::WEB_SOCKET_LOGIN;
                $this->result['data'] = ['type'=>0];
                $server->push($frame->fd,json_encode($this->result));
                return ;
            }

        }
        $this->result['message'] = '连接失败';
        $server->push($frame->fd,json_encode($this->result));

        return ;
    }





}