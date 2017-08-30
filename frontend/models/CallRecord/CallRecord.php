<?php
/**
 * Created by PhpStorm.
 * User: zhangqing
 * Date: 2017/8/29
 * Time: 下午3:58
 * 通话记录表操作
 */
  namespace frontend\models\CallRecord;

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
            self::CALLRECORD_STATUS_Filed   =>'呼叫错误',
            self::CALLRECORD_STATUS_BUSY    =>'用户忙',
            self::CALLRECORD_STATUS_NOANWSER=>'无人接听'
        ];


        const CALLRECORD_TYPE_UNURGENT  = 1;
        const CALLRECORD_TYPE_URGENT    = 2;

        static public $type_map = [
            self::CALLRECORD_TYPE_UNURGENT =>'联系电话呼叫',
            self::CALLRECORD_TYPE_URGENT   =>'紧急联系人呼叫'
        ];
      /**
       * @inheritdoc
       */
      public static function tableName()
      {
          return '{{call_record}}';
      }


  }