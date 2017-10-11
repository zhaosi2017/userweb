<?php
namespace backend\models\Reports;


use frontend\models\UserLoginLogs\UserLoginLog;
use yii\data\ActiveDataProvider;
use frontend\models\User;

class RetainedReportSearch extends UserLoginLog
{
    public $search_time;
    public function search($params)
    {
        $query = UserLoginLog::find();
        if($this->search_time){
            $startTime = strtotime($this->search_time) ;
            $endTime =  strtotime($this->search_time) + 24*60*60;
        }
        $count = User::find()->where(['>','reg_time',$startTime])->andWhere(['<','reg_time',$endTime])->count();



    }
}
