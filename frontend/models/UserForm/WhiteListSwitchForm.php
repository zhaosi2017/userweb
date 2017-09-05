<?php
namespace frontend\models\UserForm;

use frontend\models\FActiveRecord;
use frontend\models\User;
USE frontend\models\ErrCode;
USE yii;
use frontend\models\Channel;
/**
 * Class Friends
 * @package frontend\models\Friends
 * @property integer $id
 * @property integer $user_id
 * @property integer $friend_id
 * @property integer $create_at
 * @property integer $group_id
 * @property string  $remark
 * @property string  $extsion
 *
 */
class WhiteListSwitchForm extends User
{
    public function rules()
    {
        return [
            ['status','required'],
            ['status','ValidateWhite'],
        ];
    }

    public function ValidateWhite()
    {

        if(!in_array($this->status, [User::WHITE_SWITCH_OFF,User::WHITE_SWITCH_ON])){
            $this->addError('status', '参数非法');
        }
    }

    public function Switchs()
    {
        if($this->validate('status'))
        {
            $userId = Yii::$app->user->id;
            $user = User::findOne(['id'=>$userId]);

            if(empty($user))
            {
                return  $this->jsonResponse([],'用户不存在','1',ErrCode::USER_NOT_EXIST );
            }
            $user->whitelist_switch = $this->status;
            if($user->save())
            {
                return  $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);
            }else{
                return  $this->jsonResponse([],$user->getErrors(),'1',ErrCode::DATA_SAVE_ERROR);
            }
        }

        return  $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);

    }

}