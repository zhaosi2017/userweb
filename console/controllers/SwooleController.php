<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/8/31
 * Time: 上午9:34
 */


namespace console\controllers;

use common\services\socketService\swooleServer;
use yii\console\Controller;
use yii\db\Exception;

class SwooleController extends Controller{


    public function actionStart(){
        try{
            $server = new swooleServer();
        } catch (Exception $e){

        }


    }

    public function stop(){


    }




}