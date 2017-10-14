<?php
namespace backend\models\Reports;


use frontend\models\UserLoginLogs\UserLoginLog;
use function GuzzleHttp\Psr7\str;
use yii\data\ActiveDataProvider;
use frontend\models\User;
use backend\models\Reports\CountryAddress;

class RetainedReportSearch extends UserLoginLog
{
    public $start_date;
    public $start_time;
    public $end_time;

    public $data;
    public function search($param)
    {




        $this->start_date = isset($param['RetainedReportSearch']['start_date'])  && $param['RetainedReportSearch']['start_date']? $param['RetainedReportSearch']['start_date']:'';
        $days=array();
        if($this->start_date)
        {
            $startTime  = strtotime($this->start_date);
            $endTime = $startTime  + 24*60*60 ;
            for ($i = 0; $i <= 10; $i++) {//这里数字根据需要变动
                $tmp = date('Y-m-d', strtotime('-' . $i . 'day',strtotime($this->start_date)));
                $startTime = strtotime($tmp);
                $endTime = $startTime + 24 * 60 * 60;
                $days[$tmp] = $this->getDatas($startTime, $endTime);

            }
            
            $days[$this->start_date] = $this->getDatas($startTime,$endTime);
        }
        if(empty($days)) {

            for ($i = 0; $i <= 10; $i++) {//这里数字根据需要变动
                 $tmp = date("Y-m-d", strtotime('-' . $i . 'day'));
                $startTime = strtotime($tmp);
                $endTime = $startTime + 24 * 60 * 60;
                $days[$tmp] = $this->getDatas($startTime, $endTime);

            }
        }


        $this->data = $days;

        return $this;




    }

    public function getDatas($start,$end)
    {
        $_data = User::find()->select('count(id) as id,country_code')->where(['>','reg_time',$start])
            ->andWhere(['<','reg_time',$end])
            ->andWhere(['not',['country_code'=>null]])
            ->andWhere(['not',['country_code'=>'']])
            ->indexBy('country_code')
            ->groupBy('country_code')
//            ->createCommand()
//            ->getRawSql();
            ->all();

        $tmp = [];
        if(!empty($_data)) {

            foreach ($_data as $k => $_d) {
                $startTime = $start;
                $endTime = $end;
                if ($_d->id) {

                    $_User = User::find()->select('id')->where(['>', 'reg_time', $startTime])
                        ->andWhere(['<', 'reg_time', $endTime])
                        ->andWhere(['country_code' => $_d['country_code']])
                        ->indexBy('id')
                        ->asArray()
                        ->all();


                    $_TodayLogin = UserLoginLog::find()->select('user_id as id')->where(['>', 'login_time', $startTime])
                        ->andWhere(['<', 'login_time', $endTime])
                        ->andWhere(['country_code' => $_d['country_code']])
                        ->distinct()
                        ->indexBy('id')
                        ->asArray()
                        ->all();

                    //次日
//                    $startTime = $startTime + 86400;
                    $endTime = $endTime + 86400;
                    $_SecondLogin = UserLoginLog::find()->select('user_id as id')->where(['>', 'login_time', $startTime])
                        ->andWhere(['<', 'login_time', $endTime])
                        ->andWhere(['country_code' => $_d['country_code']])
                        ->distinct()
                        ->indexBy('id')
//                    ->createCommand()->getRawSql();
                        ->asArray()
                        ->all();


//                    $startTime = $startTime + 86400 * 2;
                    $endTime = $endTime + 86400 * 2;
                    $_ThirdLogin = UserLoginLog::find()->select('user_id as id')->where(['>', 'login_time', $startTime])
                        ->andWhere(['<', 'login_time', $endTime])
                        ->andWhere(['country_code' => $_d['country_code']])
                        ->distinct()
                        ->indexBy('id')
                        ->asArray()
                        ->all();
                    //七日

//                    $startTime = $startTime + 86400 * 4;
                    $endTime = $endTime + 86400 * 4;
                    $_SevenLogin = UserLoginLog::find()->select('user_id as id')->where(['>', 'login_time', $startTime])
                        ->andWhere(['<', 'login_time', $endTime])
                        ->andWhere(['country_code' => $_d['country_code']])
                        ->distinct()
                        ->indexBy('id')
                        ->asArray()
                        ->all();

                    //14日
//                    $startTime = $startTime + 86400 * 7;
                    $endTime = $endTime + 86400 * 7;
                    $_FourteenLogin = UserLoginLog::find()->select('user_id as id')->where(['>', 'login_time', $startTime])
                        ->andWhere(['<', 'login_time', $endTime])
                        ->andWhere(['country_code' => $_d['country_code']])
                        ->distinct()
                        ->indexBy('id')
                        ->asArray()
                        ->all();

                    //30日
//                    $startTime = $startTime + 86400 * 16;
                    $endTime = $endTime + 86400 * 16;
                    $_ThirtyLogin = UserLoginLog::find()->select('user_id as id')->where(['>', 'login_time', $startTime])
                        ->andWhere(['<', 'login_time', $endTime])
                        ->andWhere(['country_code' => $_d['country_code']])
                        ->distinct()
                        ->indexBy('id')
                        ->asArray()
                        ->all();

//                $TodayIntersect =  count(array_intersect_key($_User,$_TodayLogin));

                    $SecondIntersect = count(array_intersect_key($_User, $_SecondLogin));
                    $ThirdIntersect = count(array_intersect_key($_User, $_ThirdLogin));
                    $SevenIntersect = count(array_intersect_key($_User, $_SevenLogin));
                    $FourteenIntersect = count(array_intersect_key($_User, $_FourteenLogin));
                    $ThirtyIntersect = count(array_intersect_key($_User, $_ThirtyLogin));


//                $tmp[$k]['today'] = $_d->id ? $TodayIntersect/$_d->id :'0';
                    $tmp[$k]['second'] = $_d->id ? ((($SecondIntersect / $_d->id) * 100) . '%') : '0';
                    $tmp[$k]['third'] = $_d->id ? ((($ThirdIntersect / $_d->id) * 100) . '%') : '0';
                    $tmp[$k]['seven'] = $_d->id ? ((($SevenIntersect / $_d->id) * 100) . '%') : '0';
                    $tmp[$k]['fourteen'] = $_d->id ? ((($FourteenIntersect / $_d->id) * 100) . '%') : '0';
                    $tmp[$k]['thirty'] = $_d->id ? ((($ThirtyIntersect / $_d->id) * 100) . '%') : '0';
                } else {
//                    $tmp[$k]['today'] = '0';
                    $tmp[$k]['second'] = '0';
                    $tmp[$k]['third'] = '0';
                    $tmp[$k]['seven'] = '0';
                    $tmp[$k]['fourteen'] = '0';
                    $tmp[$k]['thirty'] = '0';
                }


            }
        }
        return $tmp;
    }
}
