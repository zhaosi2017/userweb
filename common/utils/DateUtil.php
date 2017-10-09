<?php
namespace common\utils;


class DateUtil {

    public static function getCurrentDateTime()
    {
        return date("Y-m-d H:i:s",time());
    }

    public static function getCurrentDate()
    {
        return date("Y-m-d",time());
    }

    /** 把字符串日期 转换成指定样式
     * @param $dateTime
     * @param string $format
     * @return bool|string
     */
    public static function getCurrentDateByFormat($dateTime,$format='Y-m-d')
    {
        return date($format,strtotime($dateTime));
    }

    /** 时间戳格式转换字符格式
     * @param $dateTime
     * @param string $format
     * @return bool|string
     */
    public static function geetDateByTimestemp($dateTime,$format='Y-m-d')
    {
        return date($format,($dateTime));
    }

    /** 时间戳转换日期时间
     * @param $dateTime
     * @param string $format
     * @return bool|string
     */
    public static function getDateTimeByTimestamp($dateTime,$format='Y-m-d H:i:s')
    {
        return date($format,($dateTime));
    }

    /** 获得本月
     * @return bool|string
     */
    public static function getCurrentMonth()
    {
        return date("Y-m",time());
    }

    /** 获得 上月
     * @return bool|string
     */
    public static function getPreviousMonth()
    {
        return date('Y-m',strtotime('-1 month'));
    }

    /** 获得 当前 天
     * @return bool|string
     */
    public static function getCurrentDay()
    {
        return date("d",time());
    }

    /** 今天往前推几天的日期 Y-m-d
     * @param $num
     * @return bool|string
     */
    public static function getLastDay($num)
    {
        $time = date("Y-m-d",strtotime("-".$num." day"));
        return $time;
    }

    /** 今天往前推几天之后加上附加小时 确定小时分钟秒 Y-m-d H:i:s
     * @param int $num
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @return bool|string
     */
    public static function getLastDate($num,$hour,$minute,$second=0,$subHours = 0)
    {
        $timeStemp = mktime($hour,$minute,$second,date("m"),date("d")-$num,date("Y"));
        $timeStemp = $timeStemp + ($subHours * 3600);
        $time=date("Y-m-d H:i:s",$timeStemp);
        return $time;
    }

    /** 加减小时 字符串转时间戳
     * @param $dateTimeStr
     * @param int $hours
     * @return int
     */
    public static function getTimezoneTimestamp($dateTimeStr,$hours = 0)
    {
        $a_time = strtotime($dateTimeStr);
        $a_time = $a_time+$hours*3600;
        return $a_time;
    }

    /** 返回往前推几天的数组 包含一系列天数
     * @param int $days
     * @return array
     */
    public static function getLastDays($days = 7)
    {
        $result = array();
        for($i=0;$i<$days;$i++)
        {
            $lastDay =self::getLastDay($days-$i);
            $result[] = $lastDay;
        }
        return $result;
    }

    /** 获得日期间隔
     * @param $start 字符日期
     * @param $end 字符日期
     * @param string $format
     * @return array
     */
    public static function getBetweenDayList($start,$end,$format = 'Y-m-d')
    {
        $beginTimeStamp = strtotime($start);
        $endTimeStamp = strtotime($end);
        $tmp=array();

        for($i=$beginTimeStamp;$i<=$endTimeStamp;$i+=(24*3600)){
            $tmp[]=$i;
            $tmp['dayList'][]=date($format,$i);
        }
        return $tmp;
    }
}