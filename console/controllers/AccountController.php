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
            $user = User::find()->select('account,id')->where(['account'=>null])->orWhere(['account'=>''])->limit(100)->all();
            if(!empty($user))
            {
                foreach ($user as $u)
                {
                    $code = UcodeService::makeCode();
                    if($code)
                    {
                        $u->account = $code;
                        $u->setScenario('update-account');
                        echo $u->id.'---'.$code.PHP_EOL;
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