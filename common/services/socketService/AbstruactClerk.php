<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/4
 * Time: 上午10:07
 * 这里校验身份 ，验证登陆状态等等等
 */

namespace  common\services\socketService;
use frontend\models\ErrCode;

abstract  class AbstruactClerk{

  public $result =
      [
          "data"=> [],
          "message"=>"json格式错误",
          "status"=> 1,
          "code"=>ErrCode::FAILURE
      ];
  abstract public function stratClerk($server,  $frame , $data);

    /**
     * @param $server
     * @param $fd
     * @param $uCode
     * @return mixed
     *
     * 绑定fd对应的优码
     */
  public function bindUcode($server , $fd , $uCode){
     $old_fd =  $this->getFdByuCode($server , $uCode);
     if($old_fd == $fd){  //重复的发登陆请求
         return true;
     }
     if($old_fd ){
         $this->result['status'] = 0;
         $this->result['message'] = '您的账号在其他地方登录！';
         $this->result['code'] = ErrCode::YOU_ACCOUNT_LOGIN_IN_OTHER_DEVICE;
         $message = json_encode($this->result,JSON_UNESCAPED_UNICODE);
         $server->push($old_fd , $message);
         $server->close($old_fd);
     }
     return $server->bind($fd , (int)$uCode );
  }

    /**
     * @param $server
     * @param $uCode
     * @return bool
     * 根据优码获取 fd
     */
  public function getFdByuCode($server , $uCode){
        foreach($server->connection_list()  as $fd){
            $info = $server->connection_info($fd);
            if(empty($info) || !is_array($info)){
                continue;
            }
            if((int)$uCode == $info['uid']){
                return $fd;
            }
        }
        return false;
  }

    /**
     * @param $server
     * @param $uCode
     * @param $message
     * 给一个用户发送消息
     */
  public function sendMessage($server , $uCode , $message){

    $fd = $this->getFdByuCode($server , $uCode);
    if(!$fd){
        file_put_contents('/tmp/swoole_send.log' , 'fd不存在消息发送失败'.PHP_EOL , 8);
        return false;
    }
    if($server->exist($fd)){
       return  $server->push($fd , $message);
    }
    return false;
  }

}