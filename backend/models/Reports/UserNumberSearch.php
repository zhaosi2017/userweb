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
//            ->createCommand()->getRawSql();
            ->all();

           // ->all();
        $key1 = !empty($_Yesterday) ? array_keys($_Yesterday) : [];

        $key2 =  !empty($_Before) ? array_keys($_Before) : [];


        $key3  =  !empty($_callRecord) ? array_keys($_callRecord) : [];
        $keys = [];
        $keys = array_merge($key1,$key3,$key2);
        $tmp = [];
       // var_dump($_Before);
        if(!empty($keys))
        {

            foreach ($keys as $i=> $k)
            {

                $tmp[$k]['before'] = isset($_Before[$k]->id) ? $_Before[$k]->id:0;
                $tmp[$k]['yesterday'] = isset($_Yesterday[$k]->id) ? $_Yesterday[$k]->id:0;
                $tmp[$k]['call_num'] = isset($_callRecord[$k]->id) ? $_callRecord[$k]->id:0;

            }
        }

        $this->data = $tmp;

        return $this;


    }
}