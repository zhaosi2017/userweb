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


}