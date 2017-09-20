<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/4
 * Time: 上午10:10
 */
namespace common\services\socketService\Clerk;

use frontend\models\Friends\FriendsRequest;
use frontend\models\User;
use Yii;
use common\services\socketService\AbstruactClerk;
use common\services\socketService\Clerk\UidConn;
use frontend\models\ErrCode;

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

        if(!isset($data->token)  || empty($data->token) || !isset($data->account) || empty($data->account))
        {
            $this->result['message'] = '参数非法';
            $server->push($frame->fd, json_encode($this->result));
            return ;
        }

        $_user = User::findOne(['token'=>$data->token]);
        if(empty($_user))
        {
            $this->result['message'] = '非法用户';
            $server->push($frame->fd, json_encode($this->result));
            return;
        }


        $redis = Yii::$app->redis;
        $_data = User::find()->select(['id','account'])->where(['account'=>$data->account])->one();

        $_friendRequest = FriendsRequest::findOne(['from_id'=>$_user->id,'to_id'=>$_data->id]);
        if(empty($_friendRequest) || ($_friendRequest && $_friendRequest['status'] !=0 ))
        {
            $this->result['message'] = '参数非法';
            $server->push($frame->fd, json_encode($this->result));
            return ;
        }

        if(!empty($_data))
        {
            if($_user->account == $data->account)
            {
                $this->result['message'] = '参数非法';
                $server->push($frame->fd, json_encode($this->result));
                return ;
            }
            $redis->EXISTS(UidConn::UID_CONN_ACCOUNT.$_data['account']);
            $_fd = $redis->get(UidConn::UID_CONN_ACCOUNT.$_data['account']);


            if($_fd)
            {
                $this->result['data'] = ['account'=>$_user->account,'type'=>1,'num'=>1];
                $this->result['code'] = ErrCode::WEB_SOCKET_INVITE_FRIEND;
                $this->result['status'] = 0;
                $this->result['message'] = $_user->account .'向你发送了好友请求';

                $server->push($_fd,  json_encode($this->result));
            }else {
                $this->result['message'] = $_data['account'] . '通知的好友不在线' ;
                $server->push($frame->fd, json_encode($this->result));

            }

        }else{
            $this->result['message'] = '通知的好友不存在' ;
            $server->push($frame->fd, json_encode($this->result));
        }
        return ;
    }







}