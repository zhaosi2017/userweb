<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\UserForm;

use frontend\models\UrgentContact;
use frontend\models\ErrCode;

class UrgentContactSortForm extends UrgentContact
{

    public $sort;

    public function rules()
    {
        return [
            [['sort','id'],'integer'],
            [['id','sort'],'required'],
            [['sort','id'],'integer'],

        ];
    }

    public function sort()
    {
        if(!$this->validate())
        {
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }

        $userId = \Yii::$app->user->id;
        $_urgentContact = UrgentContact::findOne(['user_id'=>$userId,'id'=>$this->id]);
        if(empty($_urgentContact))
        {
            return $this->jsonResponse([],'数据不存在','1',ErrCode::REQUEST_DATA_ERROR);
        }

        $_urgentContact->contact_sort = (int)$this->sort;

        if($_urgentContact->save())
        {
            return $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);
        }else{
            return $this->jsonResponse([],$_urgentContact->getErrors(),'1',ErrCode::DATA_SAVE_ERROR);
        }


    }



}