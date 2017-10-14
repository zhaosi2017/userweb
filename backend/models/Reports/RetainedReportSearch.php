<?php
namespace backend\models\Reports;


use frontend\models\UserLoginLogs\UserLoginLog;
use yii\data\ActiveDataProvider;
use frontend\models\User;
use backend\models\Reports\CountryAddress;

class RetainedReportSearch extends UserLoginLog
{
    public $start_date;

    public $data;
    public function search($param)
    {


        $this->start_date = isset($param['RetainedReportSearch']['start_date'])  && $param['RetainedReportSearch']['start_date']? $param['RetainedReportSearch']['start_date']:date('Y-m-d');

        $startTime = strtotime($this->start_date) ;
        $endTime =  $startTime + 24*60*60;





        $_data = User::find()->select('count(id) as id,country_code')->where(['>','reg_time',$startTime])
            ->andWhere(['<','reg_time',$endTime])
            ->andWhere(['not',['country_code'=>null]])
            ->andWhere(['not',['country_code'=>'']])
            ->indexBy('country_code')
            ->groupBy('country_code')
//            ->createCommand()
//            ->getRawSql();
            ->all();

        $tmp = [];
        if(!empty($_data))
        {

            foreach ($_data as $k => $_d)
            {

                if($_d->id)
                {
                    
                $_User =User::find()->select('id')->where(['>','reg_time',$startTime])
                    ->andWhere(['<','reg_time',$endTime])
                    ->andWhere(['country_code'=>$_d['country_code']])
                    ->indexBy('id')
                    ->asArray()
                    ->all();


                $_TodayLogin =  UserLoginLog::find()->select('user_id as id')->where(['>','login_time',$startTime])
                    ->andWhere(['<','login_time',$endTime])
                    ->andWhere(['country_code'=>$_d['country_code']])
                    ->distinct()
                    ->indexBy('id')
                    ->asArray()
                    ->all();
                $startTime = $startTime + 86400;
                $endTime = $endTime + 86400;
                $_SecondLogin = UserLoginLog::find()->select('user_id as id')->where(['>','login_time',$startTime])
                    ->andWhere(['<','login_time',$endTime])
                    ->andWhere(['country_code'=>$_d['country_code']])
                    ->distinct()
                    ->indexBy('id')
                    ->asArray()
                    ->all();


                $startTime = $startTime + 86400*3;
                $endTime = $endTime + 86400*3;
                $_ThirdLogin = UserLoginLog::find()->select('user_id as id')->where(['>','login_time',$startTime])
                    ->andWhere(['<','login_time',$endTime])
                    ->andWhere(['country_code'=>$_d['country_code']])
                    ->distinct()
                    ->indexBy('id')
                    ->asArray()
                    ->all();


                $startTime = $startTime + 86400*7;
                $endTime = $endTime + 86400*7;
                $_SevenLogin = UserLoginLog::find()->select('user_id as id')->where(['>','login_time',$startTime])
                    ->andWhere(['<','login_time',$endTime])
                    ->andWhere(['country_code'=>$_d['country_code']])
                    ->distinct()
                    ->indexBy('id')
                    ->asArray()
                    ->all();


                $startTime = $startTime + 86400*14;
                $endTime = $endTime + 86400*14;
                $_FourteenLogin = UserLoginLog::find()->select('user_id as id')->where(['>','login_time',$startTime])
                    ->andWhere(['<','login_time',$endTime])
                    ->andWhere(['country_code'=>$_d['country_code']])
                    ->distinct()
                    ->indexBy('id')
                    ->asArray()
                    ->all();



                $startTime = $startTime + 86400*30;
                $endTime = $endTime + 86400*30;
                $_ThirtyLogin = UserLoginLog::find()->select('user_id as id')->where(['>','login_time',$startTime])
                    ->andWhere(['<','login_time',$endTime])
                    ->andWhere(['country_code'=>$_d['country_code']])
                    ->distinct()
                    ->indexBy('id')
                    ->asArray()
                    ->all();

                $TodayIntersect =  count(array_intersect_key($_User,$_TodayLogin));
                $SecondIntersect =  count(array_intersect_key($_User,$_SecondLogin));
                $ThirdIntersect =  count(array_intersect_key($_User,$_ThirdLogin));
                $SevenIntersect =  count(array_intersect_key($_User,$_SevenLogin));
                $FourteenIntersect =  count(array_intersect_key($_User,$_FourteenLogin));
                $ThirtyIntersect =  count(array_intersect_key($_User,$_ThirtyLogin));

                $tmp[$k]['today'] = $_d->id ? $TodayIntersect/$_d->id :'0';
                $tmp[$k]['second'] = $_d->id ? $SecondIntersect/$_d->id :'0';
                $tmp[$k]['third'] = $_d->id ? $ThirdIntersect/$_d->id :'0';
                $tmp[$k]['seven'] = $_d->id ? $SevenIntersect/$_d->id :'0';
                $tmp[$k]['fourteen'] = $_d->id ? $FourteenIntersect/$_d->id :'0';
                $tmp[$k]['thirty'] = $_d->id ? $ThirtyIntersect/$_d->id :'0';
                }else{
                    $tmp[$k]['today'] = '0';
                    $tmp[$k]['second'] = '0';
                    $tmp[$k]['third'] = '0';
                    $tmp[$k]['seven'] = '0';
                    $tmp[$k]['fourteen'] = '0';
                    $tmp[$k]['thirty'] = '0';
                }


            }
        }

        var_dump($tmp);die;




    }
}
