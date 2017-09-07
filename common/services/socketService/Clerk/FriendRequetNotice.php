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

class FriendRequetNotice extends  AbstruactClerk{
    /**
     * @var callu
     */
    public $app;
    public $server;     //当前服务
    public $fd;         //当前fd

    const UID_CONN_UID = 'uid_conn_uid';
    const UID_CONN_CONN = 'uid_conn_conn';

    public function stratClerk($server,  $frame , $data){

        if(isset($data->token))
        {
            $_user = User::findOne(['token'=>$data->token]);
            if(empty($_user))
            {
                $server->push($server->fd, json_encode(['status'=>1,'msg'=>'非法用户']));
            }

        }
        $redis = Yii::$app->redis;
        $_data = User::find()->select(['id','account'])->where(['account'=>$data->account])->one();

        if(!empty($_data))
        {
            $redis->EXISTS(UidConn::UID_CONN_ACCOUNT.$_data['account']);
            $_fd = $redis->get(UidConn::UID_CONN_ACCOUNT.$_data['account']);
            $server->push($_fd, $_data['account'].'向你发送了好友请求');
        }else{
            $server->push($server->fd, json_encode(['status'=>1,'msg'=>'通知的好友不存在']));
        }
        return ;
    }





}