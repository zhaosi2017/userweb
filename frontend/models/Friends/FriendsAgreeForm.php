<?php
namespace frontend\models\Friends;

use common\services\appService\apps\WebSocket;
use common\services\socketService\Clerk\AgreeFriendNotice;
use frontend\models\BlackLists\BlackList;
use frontend\models\ErrCode;
use frontend\models\User;
use frontend\models\WhiteLists\WhiteList;
use yii\base\Model;
use frontend\models\Friends\Friends;
use frontend\models\Friends\FriendsRequest;
use yii\db\Transaction;
use yii;
use frontend\services\AgreeFriendService;

class FriendsAgreeForm extends FriendsRequest
{
    public $account;

    public function rules()
    {
        return [
            ['account', 'required'],
            ['account', 'integer'],
            ['account', 'ValidateAccount'],
        ];
    }

    public function ValidateAccount()
    {
        $user = User::findOne(['account' => $this->account]);
        if (empty($user)) {
            $this->addError('account', '优码不存在');
        }

    }


    public function agreeFriendsRequest($data)
    {
        if($this->validate('account')) {
            $_friend = User::findOne(['account' => $this->account]);

            $userId = Yii::$app->user->id;
            $_friendRequest = FriendsRequest::findOne(['to_id' => $userId, 'from_id' => $_friend->id, 'status' => self::NORMAL_STATUS]);
            if (empty($_friendRequest)) {
                return $this->jsonResponse([], '无好友请求', 1, ErrCode::NO_USER_REQUEST);
            }

            $_friendRequest->status = self::AGREE_STATUS;
            $time = time();
            $_friendRequest->update_at = $time;
            Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
            $transaction = Yii::$app->db->getTransaction();

            if ($_friendRequest->save()) {
                $_from = Friends::findOne(['user_id' => $userId, 'friend_id' => $_friend->id]);
                $_to = Friends::findOne(['user_id' => $_friend->id, 'friend_id' => $userId]);
                if (!empty($_from) && !empty($_to)) {
                    $transaction->commit();
                    return $this->jsonResponse([], '你们已经是好友了', 0, ErrCode::SUCCESS);
                }


                if (empty($_from)) {
                    $fromFriend = new Friends();
                    $fromFriend->user_id = $userId;
                    $fromFriend->friend_id = $_friend->id;
                    $fromFriend->create_at = $time;
                    $fromFriend->direction = 0;//被邀请
                    $fromFriend->link_time = 0;
                    $fromFriend->is_new_friend = Friends::NOT_IS_NEW_FRIEND;


                    if (!$fromFriend->save()) {
                        $transaction->rollBack();
                        return $this->jsonResponse([], $fromFriend->getErrors(), 1, ErrCode::DATA_SAVE_ERROR);
                    }
                }

                if (empty($_to)) {

                    $toFriend = new Friends();
                    $toFriend->user_id = $_friend->id;
                    $toFriend->friend_id = $userId;
                    $toFriend->create_at = $time;
                    $toFriend->direction = 1;//邀请者
                    $toFriend->link_time = 0;

                    if (!$toFriend->save()) {
                        $transaction->rollBack();
                        return $this->jsonResponse([], $toFriend->getErrors(), 1, ErrCode::DATA_SAVE_ERROR);
                    }
                }



                $transaction->commit();
                //同意好友请求,主动推送
                $agreeFriend = new AgreeFriendService();
                $identity = Yii::$app->user->identity;
                $agreeFriend->notice($this->account,$identity->token);
                return $this->jsonResponse([], '操作成功', 0, ErrCode::SUCCESS);
            } else {
                $transaction->rollBack();
                return $this->jsonResponse([], $_friendRequest->getErrors(), 1, ErrCode::DATA_SAVE_ERROR);
            }
        }else{
            return $this->jsonResponse([], $this->getErrors(), 1, ErrCode::VALIDATION_NOT_PASS);
        }
    }
}