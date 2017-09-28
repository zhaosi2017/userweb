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
class RefuseFriendNotice extends  AbstruactClerk{
    /**
     * @var callu
     */
    public $app;
    public $server;     //当前服务
    public $fd;         //当前fd

    const UID_CONN_UID = 'uid_conn_uid';
    const UID_CONN_CONN = 'uid_conn_conn';

    public function stratClerk($server,  $frame , $data){


        file_put_contents('/tmp/myswoole.log','000'.PHP_EOL,8);
        if(!isset($data->token) || empty($data->token) || !isset($data->account) || empty($data->account) )
        {
            $this->result['message'] = '参数非法';
            $server->push($frame->fd, json_encode($this->result));
            return ;
        }

        file_put_contents('/tmp/myswoole.log','111'.PHP_EOL,8);
        $_user = User::findOne(['token'=>$data->token]);//拒绝者
        if(empty($_user))
        {
            $this->result['message'] = '非法用户';
            $server->push($frame->fd, json_encode($this->result));
            return;
        }
        file_put_contents('/tmp/myswoole.log','222'.PHP_EOL,8);

        $_data = User::find()->select(['id','account'])->where(['account'=>$data->account])->one();//被拒绝者


        if(!empty($_data))
        {
            file_put_contents('/tmp/myswoole.log','333'.PHP_EOL,8);
            if($_user->account == $data->account)
            {
                $this->result['message'] = '参数非法';
                $server->push($frame->fd, json_encode($this->result));
                return ;
            }
            $redis = Yii::$app->redis;
            $redis->EXISTS(UidConn::UID_CONN_ACCOUNT.$_data['account']);
            $_fd = $redis->get(UidConn::UID_CONN_ACCOUNT.$_data['account']);
            file_put_contents('/tmp/myswoole.log','444'.PHP_EOL,8);
            if($_fd)
            {
                $this->result['data'] = ['account'=>$_user->account,'type'=>4,'num'=>1];
                $this->result['status'] = 0;
                $this->result['code'] = ErrCode::WEB_SOCKET_REFUSE_INVITE;
                $this->result['message'] = $_user->account .'拒绝了你的好友请求';
                file_put_contents('/tmp/myswoole.log','拒绝'.var_export($this->result,true).PHP_EOL,8);
                if($server->exist($_fd))
                {
                    $server->push($_fd, json_encode($this->result));
                }

            } else {
                file_put_contents('/tmp/myswoole.log','666'.PHP_EOL,8);
                $this->result['message'] = $_data['account'] . '通知的好友不在线';
                $server->push($frame->fd, json_encode($this->result));
            }

        }else{
            file_put_contents('/tmp/myswoole.log','777'.PHP_EOL,8);
            $this->result['message'] = '通知的好友不存在';
            $server->push($frame->fd, json_encode($this->result));
        }
        return ;
    }





}