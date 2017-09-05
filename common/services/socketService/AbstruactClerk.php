<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/4
 * Time: 上午10:07
 * 这里校验身份 ，验证登陆状态等等等
 */

namespace  common\services\socketService;

abstract  class AbstruactClerk{

  abstract public function stratClerk($server,  $frame , $data);


}