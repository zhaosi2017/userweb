<?php
namespace backend\models\Reports;


use frontend\models\Reports\ActiveDay;
use frontend\models\UserLoginLogs\UserLoginLog;
use function GuzzleHttp\Psr7\str;
use yii\data\ActiveDataProvider;
use frontend\models\User;
use backend\models\Reports\CountryAddress;
use frontend\models\CallRecord\CallRecord;

class ActiveDaySearch extends UserLoginLog
{
    public $start_date;
    public $start_time;
    public $end_time;

    public $data;
    public function search($param)
    {

        $this->start_date = isset($param['ActiveDaySearch']['start_date'])  && $param['ActiveDaySearch']['start_date']? $param['ActiveDaySearch']['start_date']:'';


        if($this->start_date)
        {
//            $startTime  = strtotime($this->start_date);
//            $endTime = $startTime  + 24*60*60 ;
            for ($i = 0; $i <= 10; $i++) {//这里数字根据需要变动
                $_tmp = date('Y-m-d', strtotime('-' . $i . 'day',strtotime($this->start_date)));
                $startTime = strtotime($_tmp);
                $endTime = $startTime + 24 * 60 * 60;
                $days[$_tmp] = $this->getDatas($startTime, $endTime);
            }


        }
        if(empty($days)) {
            for ($i = 0; $i <= 10; $i++) {//这里数字根据需要变动
                $_tmp = date("Y-m-d", strtotime('-' . $i . 'day'));
                $startTime = strtotime($_tmp);
                $endTime = $startTime + 24 * 60 * 60;
                $days[$_tmp] = $this->getDatas($startTime, $endTime);

            }
        }


        $this->data = $days;

        return $this;




    }

    public function getDatas($start,$end)
    {
        $_activeNum = ActiveDay::find()->select('count(id) as id,country_code')->where(['>','create_at',$start])
            ->andWhere(['<','create_at',$end])
            ->andWhere(['not',['country_code'=>null]])
            ->andWhere(['not',['country_code'=>'']])
            ->indexBy('country_code')
            ->groupBy('country_code')
            ->all();


        $_callUserNum = CallRecord::find()->select('GROUP_CONCAT(active_call_uid) as id,active_code')->where(['>','call_time',$start])
            ->andWhere(['<','call_time',$end])
            ->andWhere(['not',['active_code'=>null]])
            ->andWhere(['not',['active_code'=>'']])
            ->indexBy('active_code')
            ->groupBy('active_code')
//            ->createCommand()->getRawSql();

            ->all();



        $_callNum  = CallRecord::find()->select('count("id") as id,active_code')
            ->where(['>','call_time',$start])
            ->andWhere(['<','call_time',$end])
            ->andWhere(['not',['active_code'=>null]])
            ->andWhere(['not',['active_code'=>'']])
            ->indexBy('active_code')
            ->groupBy('active_code')
//            ->createCommand()->getRawSql();
            ->all();

        // ->all();
        $key1 = !empty($_activeNum) ? array_keys($_activeNum) : [];

        $key2 =  !empty($_callUserNum) ? array_keys($_callUserNum) : [];


        $key3  =  !empty($_callNum) ? array_keys($_callNum) : [];
        $keys = [];
        $keys = array_merge($key1,$key3,$key2);
        $tmp = [];
        if(!empty($keys))
        {
            foreach ($keys as $i=> $k)
            {
                $tmp[$k]['active_num'] = isset($_activeNum[$k]->id) ? $_activeNum[$k]->id:0;
                if(isset($_callUserNum[$k]->id)){var_dump( $_callUserNum[$k]->id).PHP_EOL};
                $tmp[$k]['call_user_num'] = isset($_callUserNum[$k]->id) && $_callUserNum[$k]->id ? count(array_unique(explode(',',$_callUserNum[$k]->id))):0;
                $tmp[$k]['call_num'] = isset($_callNum[$k]->id) ? $_callNum[$k]->id:0;

            }
        }
        die;
        return $tmp;


    }
}
