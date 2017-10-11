<?php

namespace backend\components;
use common\models\system\Logs;
use Yii;

class SystemLogger extends \yii\base\Component
{

    public function addLog(array $data)
    {
        $sysLogsModel = new Logs();
        $sysLogsModel->adminid = array_key_exists('aid',$data) ? $data['aid'] : \Yii::$app->user->getId();
        $sysLogsModel->content = $data['c'];
        $sysLogsModel->ctime = time();
        $sysLogsModel->table = array_key_exists('b',$data) ? $data['b'] : '';
        $sysLogsModel->url = array_key_exists('u',$data) ? $data['u'] : \Yii::$app->request->getUrl();

        $sysLogsModel->save();
    }

}
