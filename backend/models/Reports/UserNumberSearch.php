<?php
namespace backend\models\Reports;


use frontend\models\UserLoginLogs\UserLoginLog;
use yii\data\ActiveDataProvider;
use frontend\models\User;
use backend\models\Reports\CountryAddress;

/**用户数日报表
 * Class UserNumberSearch
 * @package backend\models\Reports
 */
class UserNumberSearch
{
    //日期
    public $search_keywords;

    public function search()
    {
        $days=array();

        for($i=0;$i<=7;$i++){//这里数字根据需要变动

        $days[]=date("Y-m-d",strtotime('-'.$i.'day'));

        }

        echo '<pre>';print_r($days);

    }
}