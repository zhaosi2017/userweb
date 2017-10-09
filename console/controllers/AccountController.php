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

class AccountController extends Controller{


    public function actionStart(){
        try{
            $user = User::find()->select('account')->where(['account'=>''])->limit(100)->all();
            if(!empty($user))
            {
                foreach ($user as $u)
                {
                    $code = UcodeService::makeCode();
                    if($code)
                    {
                        $u->account = $code;
                        $u->save();
                    }
                }
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