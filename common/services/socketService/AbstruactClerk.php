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
use frontend\models\MessageCatch\MessageCatch;
use Yii;

abstract  class AbstruactClerk{

    const TCP_MESSAGE_CATCH_NO     = 0;  //发送失败的消息不缓存
    const TCP_MESSAGE_CATCH_SHORT  = 1;  //发送失败的消息缓存短时间  时间5分钟
    const TCP_MESSAGE_CATCH_LONG   = 2;  //发送失败消息 长时间缓存直到发送成功



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
     $messages = $this->getCatch($uCode);
     if(!empty($messages)){
         foreach ($messages as $m){
            $server->push($fd , $m->message);
            $this->updateCatch($m->id);
         }
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
     * @param  $catch 缓存标志
     * 给一个用户发送消息
     */
  public function sendMessage($server , $uCode , $message , $catch = self::TCP_MESSAGE_CATCH_NO){

    $fd = $this->getFdByuCode($server , $uCode);
    if(!$fd){
        $this->setCatch($uCode , $message , $catch);
        return false;
    }
    if($server->exist($fd)){
       return  $server->push($fd , $message);
    }
    return false;
  }




  private function setCatch($uCode , $message , $catch){
        if($catch == self::TCP_MESSAGE_CATCH_NO){
            return true;
        }
         $model = new MessageCatch();
         $model->status = 0;
         $model->message = $message;
         $model->begin_time = time();
         $model->ucode = $uCode;
        if($catch == self::TCP_MESSAGE_CATCH_LONG){
            $model->end_time = time() + 30*24*60*60;

        }elseif($catch == self::TCP_MESSAGE_CATCH_SHORT){
            $model->end_time = time() + 5*60;
        }
        $model->save();

        return true;
  }

    /**
     * @param $uCode
     * 根据优码获取用户的消息队列
     */
  private function getCatch($uCode){
        $time = time();
        $messages = MessageCatch::find()->select('id , message')
                                        ->where(['ucode'=>$uCode , 'status'=>0])
                                        ->andWhere(['>' , 'end_time' ,$time ])
                                        ->orderBy('begin_time ASC')
                                        ->all();
       return $messages;
  }

    /**
     * @param $id
     * @return bool
     * 更新缓存的状态
     */
  private function updateCatch($id){

      $model = MessageCatch::findOne($id);
      if(empty($model)){
          return true;
      }
      $model->status = 1;
      $model->send_time = time();
      $model->save();
      return true;
  }


}