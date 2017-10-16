<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/8/31
 * Time: 上午9:34
 */


namespace console\controllers;

use common\services\appService\apps\WebSocket;
use common\services\socketService\swooleServer;
use common\services\socketService\test;
use yii\console\Controller;
use yii\db\Exception;
use Yii;

class SwooleController extends Controller{


    public function actionStart(){
        try{
            $server = new swooleServer();
        } catch (Exception $e){

        }


    }

    /**
     * 重启socket服务
     */
    public function actionRestart(){
        $json = ['action'=>8 , 'key'=>Yii::$app->params['web_socket_reload']];
        $socket = new  WebSocket();
        try{
            $b = $socket->connect('127.0.0.1' , '9803');
            if(!$b) exit("重启失败");
            $b = $socket->send_data(json_encode($json ,JSON_UNESCAPED_UNICODE));
            if(!$b) exit("重启失败");

            echo  "ok";
        }catch (\Exception $exception){
            echo $exception->getMessage();
        }catch (\Error $error){
            echo $error->getMessage();
        }

    }


    /**
     * 停止socket服务
     */
    public function actionStop(){
        $json = ['action'=>9 , 'key'=>Yii::$app->params['web_socket_reload']];
        $socket = new  WebSocket();
        try{
            $b = $socket->connect('127.0.0.1' , '9803');
            if(!$b) exit("stop ");
            $b = $socket->send_data(json_encode($json ,JSON_UNESCAPED_UNICODE));
            if(!$b) exit("stop");
            echo  "ok";
        }catch (\Exception $exception){
            echo $exception->getMessage();
        }catch (\Error $error){
            echo $error->getMessage();
        }

    }




}