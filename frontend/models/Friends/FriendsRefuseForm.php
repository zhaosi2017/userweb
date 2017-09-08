<?php
namespace frontend\models\Friends;

use frontend\models\BlackLists\BlackList;
use frontend\models\ErrCode;
use frontend\models\User;
use frontend\models\WhiteLists\WhiteList;
use yii\base\Model;
use frontend\models\Friends\Friends;
use frontend\models\Friends\FriendsRequest;
use yii\db\Transaction;
use yii;

class FriendsRefuseForm extends FriendsRequest
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

    /**
     * 拒绝优友的好友请求
     */
    public function refuseFriendsRequest()
    {
        if($this->validate('account')) {
            $identity = \Yii::$app->user->identity;
            if ($identity->account == $this->account) {
                return $this->jsonResponse([], '不能操作自己', '1', ErrCode::DO_NOT_YOURSELF);
            }
            $_friend = User::findOne(['account' => $this->account]);

            $userId = Yii::$app->user->id;
            $_friendRequest = FriendsRequest::findOne(['to_id' => $userId, 'from_id' => $_friend->id, 'status' => self::NORMAL_STATUS]);
            if (empty($_friendRequest)) {
                return $this->jsonResponse([], '无好友请求', 1, ErrCode::NO_USER_REQUEST);
            }

            $_friendRequest->status = self::REFUSE_STATUS;
            $_friendRequest->update_at = time();
            if ($_friendRequest->save()) {
                return $this->jsonResponse([], '操作成功', 0, ErrCode::SUCCESS);
            } else {
                return $this->jsonResponse([], $_friendRequest->getErrors(), 1, ErrCode::DATA_SAVE_ERROR);
            }
        }else{
            return $this->jsonResponse([], $this->getErrors(), 1, ErrCode::VALIDATION_NOT_PASS);

        }
    }

}