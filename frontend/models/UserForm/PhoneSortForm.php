<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\UserForm;

use frontend\models\ErrCode;
use frontend\models\UserPhone;
use yii\db\Transaction;

class PhoneSortForm extends UserPhone
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
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::REQUEST_DATA_ERROR);
        }

        $userId = \Yii::$app->user->id;
        \Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
        $transaction = \Yii::$app->db->getTransaction();
        foreach ($this->data as $k => $v)
        {
            $_userPhone = UserPhone::findOne(['user_id'=>$userId,'id'=>$k]);
            if(!empty($_userPhone)){
                $_userPhone->user_phone_sort = (int) $v;
                if(!$_userPhone->save())
                {
                    $transaction->rollBack();
                    return $this->jsonResponse([],$_userPhone->getErrors(),'1',ErrCode::DATA_SAVE_ERROR);
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
