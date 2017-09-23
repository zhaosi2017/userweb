<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\Friends;

use frontend\services\FriendNoticeService;
use Yii;
use frontend\models\User;
use frontend\models\ErrCode;
use frontend\models\Friends\FriendsRequest;
use common\services\appService\apps\WebSocket;


class FriendsRequestForm extends FriendsRequest
{
    public $account;

    public function rules()
    {
        return [
            ['account','required'],
            ['account', 'match', 'pattern' => '/^[0-9]{7}$/', 'message' => '{attribute}必须为7位纯数字'],
            ['account', 'safe'],
            ['note','required'],
      ];
    }


    public function addFriendsRequest()
    {
        if($this->validate('account','note'))
        {

            $user = User::findOne(['account'=>$this->account]);
            if(empty($user))
            {
                return $this->jsonResponse([],'请求的好友不存在','1',ErrCode::USER_NOT_EXIST);
            }
            $identity= Yii::$app->user->identity;
            $from_id = $identity->id;
            if($from_id == $user->id)
            {
                return $this->jsonResponse([],'用户不能添加自己为好友','1',ErrCode::USER_NO_ADD_SELF);
            }

            $_f = Friends::find()->where(['user_id'=>$identity->id,'friend_id'=>$user->id])->one();
            if(!empty($_f))
            {
                return $this->jsonResponse([],'你们已经是好友了，不能重复邀请','1',ErrCode::YOU_ARE_ALREADY_FRIENDS);
            }
            $_friends = FriendsRequest::findOne(['from_id'=>$from_id,'to_id'=>$user->id]);
            if(!empty($_friends))
            {
                return $this->jsonResponse([],'已发送添加请求，不能重复发送','1',ErrCode::USER_ADD_FRIEND_REQUEST_EXIST);
            }
            $friendsRequest = new FriendsRequest();
            $friendsRequest->from_id = $from_id;
            $friendsRequest->to_id = $user->id;
            $friendsRequest->create_at = time();
            $friendsRequest->note = $this->note;

            if($friendsRequest->save())
            {
                $noticeService = new FriendNoticeService();
                $data = @$noticeService->notice($this->account, $identity->token);
                return $this->jsonResponse([],'操作成功','0',ErrCode::SUCCESS);
            }else{
                return $this->jsonResponse([],$friendsRequest->getErrors(),'1',ErrCode::DATA_SAVE_ERROR);
            }

        }else{
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }

}