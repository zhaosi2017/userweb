<?php
namespace backend\models\Reports;


use frontend\models\UserLoginLogs\UserLoginLog;
use yii\data\ActiveDataProvider;
use frontend\models\User;
use backend\models\Reports\CountryAddress;

class RetainedReportSearch extends UserLoginLog
{
    public $search_time;
    public function search($params)
    {
        $query = UserLoginLog::find();
        $startTime =  strtotime(date('Y-m-d'));
        $endTime = $startTime + 24*60*60;
        if($this->search_time){
            $startTime = strtotime($this->search_time) ;
            $endTime =  $startTime + 24*60*60;
        }


        $count = User::find()->where(['>','reg_time',$startTime])->andWhere(['<','reg_time',$endTime])->count();

//        $secondDay = UserLoginLog::find()->where(['>','reg_time',$startTime])->andWhere(['<','reg_time',$endTime])->select('id')->



    }
}
