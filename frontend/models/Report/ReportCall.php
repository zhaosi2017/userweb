<?php
/**
 * Created by PhpStorm.
 * Users: zhangqing
 * Date: 2017/8/29
 * Time: 下午3:58
 * 通话记录表操作
 */
namespace frontend\models\Report;

use  frontend\models\FActiveRecord;

/**
 * Class ReportCall
 * @package frontend\models\CallRecord
 * @property int   $id
 * @property  int $type
 * @property  int $number
 * @property  date $day
 * 统计拨打电话统计 按照好友 非好友关系进行统计
 */
class ReportCall extends FActiveRecord{


    const CALL_TYPE_FRIEND   = 1; //好友
    const CALL_TYPE_NOFRIEND = 2; //非好友

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tmp_report_call';
    }






}