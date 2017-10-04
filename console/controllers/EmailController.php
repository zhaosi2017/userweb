<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/8/31
 * Time: 上午9:34
 */


namespace console\controllers;

use yii\console\Controller;
use yii\db\Exception;
use frontend\services\Email\EmailService;

class EmailController extends Controller{


    public function actionStart(){
        try{
            $server = new EmailService();
            $server->run();
        } catch (Exception $e){
            echo $e->getMessage().PHP_EOL;
        }


    }

    public function stop(){


    }




}