<?php
namespace backend\models\Reports;


use frontend\models\UserLoginLogs\UserLoginLog;
use yii\data\ActiveDataProvider;
use frontend\models\User;
use backend\models\Reports\CountryAddress;

class RetainedReportSearch extends UserLoginLog
{
    public $start_date;

    public function search($params)
    {

        $startTime =  strtotime(date('Y-m-d'));
        $endTime = $startTime + 24*60*60;

        if($this->start_date){
            $startTime = strtotime($this->start_date) ;
            $endTime =  $startTime + 24*60*60;
        }


        $count = User::find()->where(['>','reg_time',$startTime])
            ->andWhere(['<','reg_time',$endTime])
            ->createCommand()
            ->getRawSql();
//            ->count();
        var_dump($count);die;
//        $secondDay = UserLoginLog::find()->where(['>','reg_time',$startTime])->andWhere(['<','reg_time',$endTime])->select('id')->



    }
}
