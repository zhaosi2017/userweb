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

/** 同意好友的请求
 *
 * Class AgreeFriendNotice
 * @package common\services\socketService\Clerk
 */
class AgreeFriendNotice extends  AbstruactClerk{
    /**
     * @var callu
     */
    public $app;
    public $server;     //当前服务
    public $fd;         //当前fd

    const UID_CONN_UID = 'uid_conn_uid';
    const UID_CONN_CONN = 'uid_conn_conn';

    public function stratClerk($server,  $frame , $data){


        if(!isset($data->token) || empty($data->token) || !isset($data->account) || empty($data->account) )
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

        $_data = User::find()->select(['id','account'])->where(['account'=>$data->account])->one();


        if(!empty($_data))
        {
            if($_user->account == $data->account)
            {
                $this->result['message'] = '参数非法';
                $server->push($frame->fd, json_encode($this->result));
                return ;
            }
            $this->result['data'] = ['account'=>$_user->account,'type'=>2,'num'=>1];
            $this->result['status'] = 0;
            $this->result['code'] = ErrCode::WEB_SOCKET_AGREE_INVITE;
            $this->result['message'] = $_user->account .'同意了你的好友请求';
            $this->sendMessage($server , $data->account , json_encode($this->result, JSON_UNESCAPED_UNICODE) );


        }else{
            $this->result['message'] = '通知的好友不存在';
            $server->push($frame->fd, json_encode($this->result));
        }
        return ;
    }





}