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
    /**
     * @var callu
     */
    public $app;
    public $server;     //当前服务
    public $fd;         //当前fd



    public function stratClerk($server,  $frame , $data){


        if(!isset($data->token) || empty($data->token) || !isset($data->account) || empty($data->account) )
        {
            $this->result['message'] = '参数非法';
            $server->push($frame->fd, json_encode($this->result));
            return ;
        }

        $_user = User::findOne(['account'=>$data->account]);
        if(empty($_user))
        {
            $this->result['message'] = '优码不存在';
            $server->push($frame->fd, json_encode($this->result));
            return;
        }




        if($_user->token != $data->token)
        {
            $this->result['message'] = 'token非法';
            $server->push($frame->fd, json_encode($this->result));
            return ;
        }
        $redis = Yii::$app->redis;
        $_fd = '';
        if($redis->EXISTS(UidConn::UID_CONN_ACCOUNT.$_user['account']))
        {
            $_fd = $redis->get(UidConn::UID_CONN_ACCOUNT.$_user['account']);

        }


        if($_fd)
        {
            $this->result['data'] = ['account'=>$_user->account,'type'=>3];
            $this->result['status'] = 0;
            $this->result['code'] = ErrCode::WEB_SOCKET_HEART_CHECK;
            $this->result['message'] = '心跳正常';
            if($server->exist($_fd)){
                $server->push($_fd, json_encode($this->result));
            }

        }else {
            $this->result['message'] = '心跳检测失败';
            $server->push($frame->fd, json_encode($this->result));
        }


        return ;
    }





}