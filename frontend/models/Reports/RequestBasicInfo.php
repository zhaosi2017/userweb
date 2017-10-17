<?php
namespace frontend\models\Reports;
use Yii;
use frontend\models\FActiveRecord;
use frontend\models\ErrCode;

class RequestBasicInfo extends FActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request_basic_info';
    }

    /**
     * 写入日志
     */
    public function createLogs($data)
    {
        $this->user_id = Yii::$app->user->id ?   Yii::$app->user->id  : 0;
        $this->from_id = isset($data['from_id']) ?  (int)$data['from_id'] : 0;
        $this->utma = isset($data['from_id']) ?  $data['from_id'] : '';
        $this->imei = isset($data['mei']) ?  $data['mei'] : '';
        $this->model = isset($data['model']) ?  $data['model'] : '';
        $this->mac = isset($data['mac']) ?  $data['mac'] : '';
        $this->os = isset($data['os']) ?  $data['os'] : '';
        $this->screen = isset($data['screen']) ?  $data['screen'] : '';
        $this->network = isset($data['network']) ?  $data['network'] : '';
        $this->operator = isset($data['operator']) ?  $data['operator'] : '';
        $this->longitude = isset($data['longitude']) ?  $data['longitude'] : '';
        $this->latitude = isset($data['latitude']) ?  $data['latitude'] : '';
        $ip = Yii::$app->request->getUserIP();
        $db = new \IP2Location\Database(YII_BASE_PATH.'/../../vendor/ip2location/ip2location-php/databases/IP-COUNTRY-SAMPLE.BIN', \IP2Location\Database::FILE_IO);

        $records = $db->lookup($ip, \IP2Location\Database::ALL);

        $this->country_code = isset($records['countryCode']) ?  $records['countryCode'] : '';
        $this->country = isset($records['countryName']) ?  $records['countryName'] : '';
        $this->osversion = isset($data['osversion']) ?  $data['osversion'] : '';
        $this->version = isset($data['version']) ?  $data['version'] : '';
        $this->create_at = time();
        file_put_contents('/tmp/statistics.log',var_export($this,true).PHP_EOL,8);
    }


    /**
     * 入库
     */

    public function insertData($data)
    {
        $ip = Yii::$app->request->getUserIP();
        $this->user_id = Yii::$app->user->id ?   Yii::$app->user->id  : 0;
        $this->from_id = isset($data['from_id']) ?  (int)$data['from_id'] : 0;
        $this->utma = isset($data['from_id']) ?  $data['from_id'] : '';
        $this->imei = isset($data['imei']) ?  $data['imei'] : '';
        $this->model = isset($data['model']) ?  $data['model'] : '';
        $this->mac = isset($data['mac']) ?  $data['mac'] : '';
        $this->os = isset($data['os']) ?  $data['os'] : '';
        $this->screen = isset($data['screen']) ?  $data['screen'] : '';
        $this->network = isset($data['network']) ?  $data['network'] : '';
        $this->operator = isset($data['operator']) ?  $data['operator'] : '';
        $this->longitude = isset($data['longitude']) ?  $data['longitude'] : '';
        $this->latitude = isset($data['latitude']) ?  $data['latitude'] : '';
        $db = new \IP2Location\Database(YII_BASE_PATH.'/../../vendor/ip2location/ip2location-php/databases/IP-COUNTRY-SAMPLE.BIN', \IP2Location\Database::FILE_IO);

        $records = $db->lookup($ip, \IP2Location\Database::ALL);
        $this->country_code = isset($records['countryCode']) ?  $records['countryCode'] : '';
        $this->country = isset($records['countryName']) ?  $records['countryName'] : '';
        $this->osversion = isset($data['osversion']) ?  $data['osversion'] : '';
        $this->version = isset($data['version']) ?  $data['version'] : '';
        $this->create_at = time();
        @$this->save();
        return true;
    }




}

