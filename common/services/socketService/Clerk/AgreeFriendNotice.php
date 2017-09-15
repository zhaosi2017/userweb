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
            $server->push($frame->fd, json_encode(['status'=>1,'code'=>ErrCode::FAILURE,'message'=>'参数非法']));
            return ;
        }

        $_user = User::findOne(['token'=>$data->token]);
        if(empty($_user))
        {
            $server->push($frame->fd, json_encode(['status'=>1,'code'=>ErrCode::FAILURE,'message'=>'非法用户']));
            return;
        }

        $_data = User::find()->select(['id','account'])->where(['account'=>$data->account])->one();


        if(!empty($_data))
        {
            if($_user->account == $data->account)
            {
                $server->push($frame->fd, json_encode(['status'=>1,'code'=>ErrCode::FAILURE,'message'=>'参数非法']));
                return ;
            }
            $redis = Yii::$app->redis;
            $redis->EXISTS(UidConn::UID_CONN_ACCOUNT.$_data['account']);
            $_fd = $redis->get(UidConn::UID_CONN_ACCOUNT.$_data['account']);

            if($_fd)
            {
                $server->push($_fd, json_encode(['data'=>['account'=>$_user->account],'type'=>2,'code'=>ErrCode::SUCCESS,'message'=>$_user->account .'同意了你的好友请求']));
            }else {
                $server->push($frame->fd, json_encode(['status'=>1,'code'=>ErrCode::FAILURE,'message'=>$_data['account'] . '通知的好友不在线']));
            }

        }else{
            $server->push($frame->fd, json_encode(['status'=>1,'code'=>ErrCode::FAILURE, 'message'=>'通知的好友不存在']));
        }
        return ;
    }





}