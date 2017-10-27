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
        //保存分组查询的，勿删
        public $sqlText;

        const CALLRECORD_STATUS_SUCCESS     = 0;
        const CALLRECORD_STATUS_FILED       = 1;
        const CALLRECORD_STATUS_BUSY        = 3;
        const CALLRECORD_STATUS_NOANWSER    = 5;

        static public  $status_map = [
            self::CALLRECORD_STATUS_SUCCESS =>'呼叫成功',
            self::CALLRECORD_STATUS_FILED   =>'呼叫错误',
            self::CALLRECORD_STATUS_BUSY    =>'用户忙',
            self::CALLRECORD_STATUS_NOANWSER=>'无人接听'
        ];


        const CALLRECORD_TYPE_UNURGENT  = 0;
        const CALLRECORD_TYPE_URGENT    = 1;

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

          $data  = self::find()->select('max(id) as id')->where(['active_call_uid'=>$userId])->groupBy('unactive_call_uid')
              ->offset($offset)->limit($limit)->orderBy('id desc') ->all();//createCommand()->getRawSql() ;



          $_res = [];
          if(!empty($data))
          {    $ids = [];
              foreach ($data as $i => $r)
              {
                  $ids[] = $r->id;
              }
              if(!empty($ids)) {
                  $_res = CallRecord::find()->where(['in', 'id', $ids])->orderBy('id desc')->all();
              }
          }


          $tmp = [];
          if(!empty($_res))
          {
              $_friends = Friends::find()->select('remark,friend_id')->where(['user_id'=>$userId])->indexBy('friend_id')->all();
              foreach ($_res as $k=>$v)
              {

                  if(isset($v->user->account) &&  $v->user->account) {
                      $_vs['id'] = $v['id'];
                      $_vs['to_user_id'] = $v['unactive_call_uid'];
                      $_vs['time'] = date('y-m-d H:i', $v['call_time']);
                      $_vs['call_type'] = $v['type'];
                      $_vs['status'] = $v['status'];
                      $_vs['group_id'] = $v['group_id'];

                      $_name = isset($_friends[$_vs['to_user_id']]['remark']) && $_friends[$_vs['to_user_id']]['remark'] ? $_friends[$_vs['to_user_id']]['remark'] : '';
                      if (empty($_name)) {
                          $_name = isset($v->user->nickname) ? $v->user->nickname : '';
                      }
                      $_vs['channel'] = isset($v->user->channel) ? $v->user->channel : '';
                      $_vs['nickname'] = $_name;
                      $_vs['account'] = isset($v->user->account) ? $v->user->account : '';
                      $_vs['header_url'] = isset($v->user->header_img) && $v->user->header_img ? Yii::$app->params['frontendBaseDomain'] . $v->user->header_img : '';
                      $tmp[] = $_vs;

                  }
              }
          }
          return $this->jsonResponse($tmp,'操作成功',0,ErrCode::SUCCESS);
      }


      public function getUser()
      {
          return $this->hasOne(User::className(), ['id' => 'unactive_call_uid']);
      }


      public function getActive()
      {
          return $this->hasOne(User::className(), ['id' => 'active_call_uid']);
      }









  }