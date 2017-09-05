<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/8/30
 * Time: 上午10:07
 */


namespace common\tests\unit\models;

use common\services\ttsService\third\Nexmo;
use common\services\ttsService\third\Sinch;
use frontend\models\CallRecord\CallRecord;
use frontend\models\User;
use Yii;
use common\services\ttsService\CallService;
use common\fixtures\UserFixture as UserFixture;

/**
 * Login form test
 */
class CallTest extends \Codeception\Test\Unit{

    public function testCall(){

        $service =new  CallService(Sinch::class);
        $service->from_user = User::findOne(1);
        $service->to_user   = User::findOne(2);
        $service->text      ="双流老妈秃头呼叫你上线";
        $service->app       = $this;
        $service->call_type = CallRecord::CALLRECORD_TYPE_UNURGENT;

        $service->start_call();

    }


    public function sendText($string ){

        echo $string;

    }



}