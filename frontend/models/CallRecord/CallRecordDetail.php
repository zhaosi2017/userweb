<?php
namespace frontend\models\CallRecord;

use frontend\models\BlackLists\BlackList;
use frontend\models\CallRecord\CallRecord;
use frontend\models\ErrCode;
use frontend\models\User;
use frontend\models\WhiteLists\WhiteList;
use yii\base\Model;
use frontend\models\Friends\Friends;
use frontend\models\Friends\FriendsRequest;
use yii\db\Transaction;
use yii;

class CallRecordDetail extends CallRecord
{
    public $cid;

    public function rules()
    {
        return [
            ['cid', 'required'],
            ['cid', 'string'],
            ['cid', 'ValidateCall'],
        ];
    }

    public function ValidateCall()
    {
        $userId = Yii::$app->user->id;
        $callRecord = CallRecord::findOne(['id'=>$this->cid,'from_user_id'=>$userId]);
        if(empty($callRecord))
        {
            $this->addError('cid','没有对应的呼叫记录');
        }
    }

    public function detail()
    {
        if($this->validate('cid'))
        {
            $userId = Yii::$app->user->id;
            $callRecord = CallRecord::find()->select(['id','to_user_id','time','status','call_type'])
                ->where(['group_id'=>$this->cid,'from_user_id'=>$userId])
                ->all();
            $data = [];
            if(!empty($callRecord))
            {
                foreach ($callRecord as $key => $call) {
                    $data['time'] = date('Y-m-d H:i', $call['time']);
                    $data['status'] = isset(self::$status_map[$call['status']]) ? self::$status_map[$call['status']] : '未知错误';
                    $_user = User::find()->select('account')->where(['id' => $call['to_user_id']])->one();
                    $data['account'] = isset($_user['account']) ? $_user['account'] : '';
                }
            }

            return $this->jsonResponse($data,'操作成功','0',ErrCode::SUCCESS);
        }else{
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }




}