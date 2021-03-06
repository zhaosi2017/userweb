<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\UserForm;

use frontend\models\FActiveRecord;
use frontend\models\User;
USE frontend\models\ErrCode;
USE yii;
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
class NicknameForm extends User
{
    public function rules()
    {
        return [
            ['nickname','required'],
            ['nickname','match','pattern' => '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]{1,12}$/u','message'=>'昵称至少包含1-12个字符，仅中英文、数字、下划线'],
//            ['nickname','ValidateNickname'],
        ];
    }

    public function ValidateNickname()
    {

        $res = self::find()->where(['nickname'=>$this->nickname])->one();

        if(!empty($res) &&  $res->id != Yii::$app->user->id){
            $this->addError('nickname', '该昵称已被占用！');
        }else{
            return true;
        }

    }


    public function updateNickname()
    {
        if($this->validate('nickname'))
        {
            $userId = Yii::$app->user->id;
            $user = User::findOne(['id'=>$userId]);
            if(empty($user))
            {
                return  $this->jsonResponse([],'用户不存在','1',ErrCode::USER_NOT_EXIST );
            }
            $user->nickname = $this->nickname;
            if($user->save())
            {
                return $this->jsonResponse([],'修改昵称成功',0,ErrCode::SUCCESS);
            }else{
                return $this->jsonResponse([],$user->getErrors(),1,ErrCode::DATA_SAVE_ERROR);
            }
        }else{
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }
    }


}