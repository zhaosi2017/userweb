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
    public $p;
    const PAGE_NUM = 10;// 每次获取10个
    public function rules()
    {
        return [
            ['cid', 'required','message'=>'优码不能为空'],//优码
            ['cid', 'integer','message'=>'优码必须是整数'],
            ['p','integer'],
        ];
    }



    public function detail()
    {
        if($this->validate('cid'))
        {
            $user = User::findOne(['account'=>$this->cid]);
            if(empty($user))
            {
                return $this->jsonResponse([],'用户不存在','1',ErrCode::USER_NOT_EXIST);
            }

            $userId = Yii::$app->user->id;
            if($user->id == $userId)
            {
                return $this->jsonResponse([],'数据非法','1',ErrCode::USER_NOT_EXIST);
            }
            $offset = $this->p == 0 ? 0: self::PAGE_NUM*($this->p-1);

            $callRecord = CallRecord::find()->select(['id','to_user_id','time','status','call_type'])
                ->where(['to_user_id'=>$user->id,'from_user_id'=>$userId])->orderBy('time desc')
                ->limit(self::PAGE_NUM)
                ->offset($offset)
                ->all();

            $data = [];
            if(!empty($callRecord))
            {
                foreach ($callRecord as $key => $call) {
                    $data[$key]['time'] = date('y-m-d H:i', $call['time']);
                    $data[$key]['msg'] = isset(self::$status_map[$call['status']]) ? self::$status_map[$call['status']] : '未知错误';
                    $data[$key]['status'] = $call['status'];
                    $_user = User::find()->select('account,header_img')->where(['id' => $call['to_user_id']])->one();
                    $data[$key]['account'] = isset($_user['account']) ? $_user['account'] : '';
                    $data[$key]['header_url'] = isset($_user['header_img']) &&  $_user['header_img'] ? Yii::$app->params['frontendBaseDomain'].$_user['header_img'] : '';
                    $data[$key]['id'] = $call['id'];
                }
            }

            return $this->jsonResponse($data,'操作成功','0',ErrCode::SUCCESS);
        }else{
            return $this->jsonResponse([],$this->getErrors(),'1',ErrCode::VALIDATION_NOT_PASS);
        }
    }




}