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
class ChannelForm extends User
{
    public function rules()
    {
        return
        [
            ['channel','required'],
            ['channel','ValidateChannel'],
        ];
    }


    public function ValidateChannel()
    {
        $tmp = explode(',',$this->channel);
        $channelArr = Channel::find()->select('id')->indexBy('id')->all();
        if(!empty($tmp))
        {
            foreach ($tmp as $c){
                if(array_key_exists($c,$channelArr))
                {
                    continue;
                }
                $this->addError('channel','渠道非法');
                break;
            }
        }
        return true;
    }

    public function updateCHannel()
    {
        if(empty($this->channel))
        {
            return $this->jsonResponse([],'渠道不能为空',1,ErrCode::CHANNEL_EMPTY);
        }
        if ($this->validate('channel'))
        {
            $userId = Yii::$app->user->id;
            $user = User::findOne(['id'=>$userId]);
            if(empty($user))
            {
                return  $this->jsonResponse([],'用户不存在','1',ErrCode::USER_NOT_EXIST );
            }
            $user->channel = $this->channel;
            if($user->save())
            {
                return  $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS );
            }else{
                return  $this->jsonResponse([],$user->getErrors(),'1',ErrCode::USER_NOT_EXIST );
            }
        }else{
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::SUCCESS);
        }
    }
}