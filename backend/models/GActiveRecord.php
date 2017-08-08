<?php
namespace backend\models;
use  Yii;
use yii\db\ActiveRecord;

class GActiveRecord extends ActiveRecord
{



    public function ajaxResponse($response = ['code'=>0, 'msg'=>'操作成功', 'data'=>[]])
    {
        header('Content-Type: application/json');
        exit(json_encode($response, JSON_UNESCAPED_UNICODE));
    }

    public function changeStatus($status, $condition='')
    {
        $params = ['status' => $status];
        return $this::getDb()->createCommand()->update($this::tableName(), $params, $condition)->execute();
    }

    public function sendSuccess($str='操作成功', $min=3)
    {
        Yii::$app->getSession()->setFlash('pageMessTime', $min);
        Yii::$app->getSession()->setFlash('success', $str);
    }

    public function sendError($str='操作失败', $min=3)
    {
        Yii::$app->getSession()->setFlash('pageMessTime', $min);
        Yii::$app->getSession()->setFlash('error', $str);
    }


}