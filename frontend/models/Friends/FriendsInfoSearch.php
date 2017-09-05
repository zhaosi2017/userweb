<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/9/2
 * Time: 上午10:33
 */
namespace frontend\models\Friends;

use frontend\models\BlackLists\BlackList;
use frontend\models\WhiteLists\WhiteList;
use frontend\models\ErrCode;
use frontend\models\FActiveRecord;

use frontend\models\Friends\Friends;
use frontend\models\User;
use frontend\models\UserPhone;
use frontend\models\UrgentContact;


class FriendsInfoSearch extends Friends {

    public $account;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ 'account','required'],
            [ 'account' ,'integer'],
            ['account','ValidateAccount'],
        ];
    }

    public function ValidateAccount()
    {
        $user = User::findOne(['account'=>$this->account]);
        if(empty($user))
        {
            $this->addError('account','优码不存在');
        }
    }

    /**查看好友的基本信息
     * @return array
     */
    public function getFriendInfo()
    {

        if($this->validate('account'))
        {
           $user =  User::find()->select(['account','nickname','channel'])->where(['account'=>$this->account])->one();
            if(empty($user))
            {
                return $this->jsonResponse([],'用户不存在','1',ErrCode::USER_NOT_EXIST);
            }
            $userPhoneNum =  UserPhone::find()->where(['user_id'=>$user->id])->count();
            $urgentContactNum =  UrgentContact::find()->where(['user_id'=>$user->id])->count();

            $white =   WhiteList::findOne(['white_uid'=>$user->id,'uid'=>\Yii::$app->user->id]);

            $black =  BlackList::findOne(['black_uid'=>$user->id,'uid'=>\Yii::$app->user->id]);
            $data = [
                $user,
                'userPhoneNum'=>$userPhoneNum,
                'urgentContactNum'=>$urgentContactNum,
                'white_status'=>empty($white)? 0 : 1,
                'black_status'=>empty($black)? 0 :1,
            ];

            return $this->jsonResponse($data,'操作成功',0,ErrCode::SUCCESS);
        }else{
            return $this->jsonResponse([],$this->getErrors(),1,ErrCode::VALIDATION_NOT_PASS);
        }
    }




}