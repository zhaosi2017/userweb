<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/8/30
 * Time: 下午1:54
 * 优呼app  这个对象主要用于和app端的业务交互
 * 这里的app禁止使用匿名函数
 */
namespace  common\services\appService\apps;

use common\models\User;

class callu {
    /**
     * @var User  用户
     */
    public $user;

    /**
     * @var 用户好友对象
     *
     */
    public $friend;
    /**
     * @var socket fd
     */
    public $socket_fd;
    /**
     * @var socket 服务
     */
    public $socket_server;


    private $result = [
        "data"=> [],
        "message"=>"修改昵称成功",
        "status"=> 0,
        "code"=>"0000"

    ];

    public function sendText($string){

        $this->result['message'] = $string;

        $this->socket_server->push($this->fd , json_encode($this->result , true));

    }







}