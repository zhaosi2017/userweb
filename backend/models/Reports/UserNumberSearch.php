<?php
namespace backend\models\Reports;


use frontend\models\CallRecord\CallRecord;
use frontend\models\UserLoginLogs\UserLoginLog;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\User;
use backend\models\Reports\CountryAddress;

/**用户数日报表
 * Class UserNumberSearch
 * @package backend\models\Reports
 */
class UserNumberSearch extends Model
{
    //日期
    public $start_date;
    public $end_date;
    public $data;

    public function search($param)
    {

        $this->start_date = isset($param['UserNumberSearch']['start_date'])  && $param['UserNumberSearch']['start_date']?$param['UserNumberSearch']['start_date']:date('Y-m-d');
//        var_dump($this->start_date);

//        $days=array();
//
//        for($i=0;$i<=7;$i++){//这里数字根据需要变动
//
//        $days[]=date("Y-m-d",strtotime('-'.$i.'day'));
//
//        }
//        return $this;
//        echo '<pre>';print_r($days);
        $start = strtotime($this->start_date)-86400;
        $end = strtotime($this->start_date) ;
        //当天
//        $_Today = User::find()->select('count("id") as id,country_code')->where(['>','reg_time',$start])
//            ->andWhere(['<','reg_time',$end])
//            ->andWhere(['not',['country_code'=>null]])
//            ->andWhere(['not',['country_code'=>'']])
//            ->indexBy('country_code')
//            ->groupBy('country_code')
//            ->all();
        //昨天


        $_Yesterday = User::find()->select('count("id") as id,country_code')->where(['>','reg_time',$start])
            ->andWhere(['<','reg_time',$end])
            ->andWhere(['not',['country_code'=>null]])
            ->andWhere(['not',['country_code'=>'']])
            ->indexBy('country_code')
            ->groupBy('country_code')
            ->all();

//            ->createCommand()->getRawSql();

        $end = $start;
        $start = $start -86400;
        //前天
        $_Before = User::find()->select('count("id") as id,country_code')
            ->where(['>','reg_time',$start])
            ->andWhere(['<','reg_time',$end])
            ->andWhere(['not',['country_code'=>null]])
            ->andWhere(['not',['country_code'=>'']])
            ->indexBy('country_code')
            ->groupBy('country_code')
            ->all();
        $_tmp = [];


        $_callRecord  = CallRecord::find()->select('count("id") as id,active_code')
            ->where(['>','call_time',$start])
            ->andWhere(['<','call_time',$end])
            ->andWhere(['not',['active_code'=>null]])
            ->andWhere(['not',['active_code'=>'']])
            ->indexBy('active_code')
            ->groupBy('active_code')
            ->all();
        $keys1 =  array_keys($_Yesterday);
        var_dump($keys1);

        $keys2 =  array_keys($_Before);
        var_dump($keys2);

        var_dump(array_keys($_callRecord));

        $this->data = [$_Yesterday,$_Before];
        echo '<pre>'; print_r($this->data);;
        return $this;


    }
}