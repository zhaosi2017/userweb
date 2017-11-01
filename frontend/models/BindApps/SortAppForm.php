<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\BindApps;

use frontend\models\ErrCode;
use frontend\models\UserAppBind;
use frontend\models\UserPhone;
use yii\db\Transaction;
use frontend\models\BindApps\BindAppForm;

class SortAppForm extends UserAppBind
{
    public $data;

    public function rules()
    {
        return [
            ['data','required'],


        ];
    }



    public function sort()
    {
        if(!$this->validate())
        {
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

        if(!is_array($this->data))
        {
            return $this->jsonResponse([],'参数错误',1,ErrCode::REQUEST_DATA_ERROR);
        }

        $userId = \Yii::$app->user->id;
        \Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
        $transaction = \Yii::$app->db->getTransaction();
        foreach ($this->data as $k => $v)
        {
            $userAppBind = UserAppBind::findOne(['user_id'=>$userId,'type'=>UserAppBind::APP_TYPE_POTATO,'id'=>$k]);
            if(!empty($userAppBind)){
                $userAppBind->sort = (int) $v;
                $userAppBind->update_at = time();
                if(!$userAppBind->save())
                {
                    $transaction->rollBack();
                    return $this->jsonResponse([],$userAppBind->getErrors(),'1',ErrCode::DATA_SAVE_ERROR);
                }
            }else{
                $transaction->rollBack();
                return $this->jsonResponse([],'参数有误','1',ErrCode::REQUEST_DATA_ERROR);

            }

        }
        $transaction->commit();
        return $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);



    }



}
