<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/8/31
 * Time: 上午9:34
 */


namespace console\controllers;

use common\services\socketService\swooleCallu;
use yii\console\Controller;
use yii\db\Exception;

class SwooleController extends Controller{


    public function actionStart(){
        try{
            $server = new swooleCallu();
        } catch (Exception $e){
          echo   $e->getMessage();
        }


    }

    public function stop(){


    }




}