<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/8/29
 * Time: 下午3:58
 * 通话记录表操作
 */
  namespace frontend\models\CallRecord;
use frontend\models\Friends\Friends;
use yii;
use frontend\models\ErrCode;
use frontend\models\User;
  /**
   * Class CallRecord
   * @package frontend\models\CallRecord
   */
  use  frontend\models\FActiveRecord;

  class CallRecord extends FActiveRecord{

        const CALLRECORD_STATUS_SUCCESS     = 1;
        const CALLRECORD_STATUS_FILED       = 2;
        const CALLRECORD_STATUS_BUSY        = 3;
        const CALLRECORD_STATUS_NOANWSER    = 4;

        static public  $status_map = [
            self::CALLRECORD_STATUS_SUCCESS =>'呼叫成功',
            self::CALLRECORD_STATUS_FILED   =>'呼叫错误',
            self::CALLRECORD_STATUS_BUSY    =>'用户忙',
            self::CALLRECORD_STATUS_NOANWSER=>'无人接听'
        ];


        const CALLRECORD_TYPE_UNURGENT  = 1;
        const CALLRECORD_TYPE_URGENT    = 2;

        static public $type_map = [
            self::CALLRECORD_TYPE_UNURGENT =>'联系电话呼叫',
            self::CALLRECORD_TYPE_URGENT   =>'紧急联系人呼叫'
        ];

        const FIRST_NUM = 20;//首次获取20个
        const OTHER_NUM = 10;//其他都10个
      /**
       * @inheritdoc
       */
      public static function tableName()
      {
          return 'call_record';
      }

      public function lists($p)
      {
          $userId = Yii::$app->user->id;
          $limit =  $p == 0 ?  self::FIRST_NUM : self::OTHER_NUM;
          $offset = $p == 0 ? 0: self::FIRST_NUM+self::OTHER_NUM*($p-1);

          $data  = self::find()->select('min(id) as id,group_id')->where(['from_user_id'=>$userId])->groupBy('group_id')
              ->offset($offset)->limit($limit)->orderBy('id desc') ->all();//createCommand()->getRawSql() ;
          $_res = [];
          if(!empty($data))
          {
              foreach ($data as $i => $r)
              {
                  $_res[] = CallRecord::findOne($r->id);
              }
          }

          $tmp = [];
          if(!empty($_res))
          {
              foreach ($_res as $k=>$v)
              {

                  $_v['id'] = $v['id'];
                  $_v['to_user_id'] = $v['to_user_id'];
                  $_v['time'] = date('Y-m-d H:i',$v['time']);
                  $_v['call_type']= $v['call_type'];
                  $_v['status'] = $v['status'];
                  $_v['group_id'] = $v['group_id'];
                  $_tmp =  User::find()->where(['id'=>$v['to_user_id']])->select(['nickname','account','header_img','channel'])->one();
                  $friend = Friends::find()->select('remark')->where(['friend_id'=>$v['to_user_id'],'user_id'=>$userId])->one();
                  $_name =isset($friend['remark']) && $friend['remark'] ? $friend['remark'] :'';
                  if(empty($_name)){
                      $_name = isset($_tmp['nickname']) ?$_tmp['nickname']:'';
                  }
                  $_v['channel'] = isset($_tmp['channel']) ? $_tmp['channel']: '';
                  $_v['nickname'] = $_name;
                  $_v['account'] = isset($_tmp['account']) ?$_tmp['account']:'';
                  $_v['header_url'] = isset($_tmp['header_img'])  && $_tmp['header_img'] ? Yii::$app->params['frontendBaseDomain'].$_tmp['header_img'].'?v='.time():'';
                  $tmp[$k]=$_v;
              }
          }
          return $this->jsonResponse($tmp,'操作成功',0,ErrCode::SUCCESS);
      }






  }