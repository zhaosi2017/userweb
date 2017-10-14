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
use frontend\models\User;
use frontend\services\UcodeService;
use frontend\models\UserLoginLogs\UserLoginLog;

class TestController extends Controller{


    public function actionStart(){
        try{
            $user = UserLoginLog::find()->select('country_code,user_id,id')->where(['country_code'=>null])->orWhere(['country_code'=>''])->limit(500)->all();


            if(!empty($user))
            {
                foreach ($user as $u)
                {
                    $code = User::find()->select('country_code')->where(['id'=>$u->user_id])->one();

                    if(!empty($code))
                    {
                        $u->country_code = $code->country_code;

                        if(!$u->save())
                        {
                            echo json_encode($u->getErrors());
                            echo '操作失败'.PHP_EOL;
                            break;
                        }
                    }
                }
            }else{
                echo 'no data'.PHP_EOL;
            }
            echo '操作成功';

        } catch (Exception $e){
            echo $e->getMessage().PHP_EOL;
        }
        catch (\Exception $e){
            echo $e->getMessage().PHP_EOL;
        }


    }

    public function stop(){


    }




}