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
    public function search($params)
    {

        $this->start_date = isset($param['RetainedReportSearch']['start_date'])  && $param['RetainedReportSearch']['start_date']?$param['RetainedReportSearch']['start_date']:date('Y-m-d');

        $startTime = strtotime($this->start_date) ;
        $endTime =  $startTime + 24*60*60;


        $sql = User::find()->select('count(id) as id,country_code')->where(['>','reg_time',$startTime])
            ->andWhere(['<','reg_time',$endTime])
            ->andWhere(['not',['country_code'=>null]])
            ->andWhere(['not',['country_code'=>'']])
            ->indexBy('country_code')
            ->groupBy('country_code')
            ->createCommand()
            ->getRawSql();

        $_data = User::find()->select('count(id) as id,country_code')->where(['>','reg_time',$startTime])
            ->andWhere(['<','reg_time',$endTime])
            ->andWhere(['not',['country_code'=>null]])
            ->andWhere(['not',['country_code'=>'']])
            ->indexBy('country_code')
            ->groupBy('country_code')
//            ->createCommand()
//            ->getRawSql();
            ->all();

        var_dump($sql.PHP_EOL,$_data);die;

//        $secondDay = UserLoginLog::find()->where(['>','reg_time',$startTime])->andWhere(['<','reg_time',$endTime])->select('id')->



    }
}
