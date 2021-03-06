<?php
namespace frontend\models;
use  Yii;
use yii\db\ActiveRecord;

class FActiveRecord extends ActiveRecord
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

    public function jsonResponse($data,$message,$status = 0,$code)
    {
        return ['data'=>$data, 'message'=>$message, 'status'=>$status, 'code'=>$code];
    }

    public static function jsonResult($data,$message,$status = 0,$code){
        return ['data'=>$data, 'message'=>$message, 'status'=>$status, 'code'=>$code];
    }




}