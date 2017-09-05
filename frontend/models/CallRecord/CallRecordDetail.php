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
            ['cid', 'integer'],
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
                ->where(['id'=>$this->cid,'from_user_id'=>$userId])
                ->one();
            $data = [];
            if($callRecord['to_user_id'] || !empty($callRecord))
            {
                $data['time'] = date('Y-m-d H:i',$callRecord['time']);
                $data['status'] = isset(self::$status_map[$callRecord['status']]) ? self::$status_map[$callRecord['status']] :'未知错误';
                $_user = User::find()->select('account')->where(['id'=>$callRecord['to_user_id']])->one();
                $data['account'] = isset($_user['account'])? $_user['account']:'';
            }

            return $this->jsonResponse($data,'操作成功','0',ErrCode::SUCCESS);
        }else{
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }




}