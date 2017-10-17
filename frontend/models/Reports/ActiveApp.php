<?php
namespace frontend\models\Reports;
use frontend\models\ErrCode;
use frontend\models\FActiveRecord;
class ActiveApp
{
    public $utma;
    public $imei;
    public $countryCode;
    public $countryName;
    public function writeLogs($data)
    {
        $this->utma = isset($data['utma'])?$data['utma'] : '';
        $this->imei = isset($data['mei'])?$data['mei'] : '';
        $ip = \Yii::$app->request->getUserIP();
        $db = new \IP2Location\Database(YII_BASE_PATH.'/../../vendor/ip2location/ip2location-php/databases/IP-COUNTRY-SAMPLE.BIN', \IP2Location\Database::FILE_IO);
        $records = $db->lookup($ip, \IP2Location\Database::ALL);

        $this->countryCode =  isset($records['countryCode']) ? $records['countryCode'] :'' ;
        $this->countryName =  isset($records['countryName']) ? $records['countryName'] :'' ;

        $_data = [
            $this->utma,
            $this->imei,
            $this->countryCode,
            $this->countryName,
        ];
        $fp = @fopen('/tmp/active-app.csv', 'w');
        @fputcsv($fp,$_data);
        @fclose($fp);
        return FActiveRecord::jsonResult([],'操作成功',0,ErrCode::SUCCESS);
    }
}