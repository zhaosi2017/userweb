<?php
namespace frontend\services\LimitRate;
use Yii;


class LimitRateService
{
    const LIMIT_TIME = 600 ; //10分钟
    const LIMIT_NUM = 10; //10分钟内限制10次
    const LIMIT_REDIS_PREFIX_KEY = 'limit-rate-key';
    const LIMIT_REDIS_PREFIX_KEY_TOTAL = 'limit-rate-total';//限制用户在规定时间内请求的总次数（所有的url）
    const LIMIT_NO_LOGIN_REDIS_KEY = 'limit-no-login';

    /**针对每个url 请求的速率限制（用户已经登录）
     * @param $action 请求的url
     */
    public function limit($action)
    {
        $id = Yii::$app->user->id;
        if(!$id)
        {
            return false;
        }
        $key = self::LIMIT_REDIS_PREFIX_KEY.$id.$action;
        return $this->insertRedis($key);

    }

    /**限制用户总的返回次数（不限定url）
     * @return bool|string
     */
    public function limitTotal()
    {
        $id = Yii::$app->user->id;
        if(!$id)
        {
            return false;
        }
        $key = self::LIMIT_REDIS_PREFIX_KEY_TOTAL.$id;
        return $this->insertRedis($key);
    }


    /**没有登录限制（针对限定的url）
     * @param $key
     * @param $action
     * @return bool|string
     */
    public function noLoginLimit($key, $action)
    {
        $redisKey = self::LIMIT_NO_LOGIN_REDIS_KEY.$key.$action;
        return $this->insertRedis($redisKey);

    }

    public function insertRedis($key)
    {
        if(empty($key))
        {
            return false;
        }
        $redis = Yii::$app->redis;
        if($redis->HEXISTS($key,'num'))
        {

            $num =  $redis->hget($key,'num');
            if($num > self::LIMIT_NUM)
            {
                $expire_time = $redis->ttl($key);
                $m = intval($expire_time/60);

                $s = $expire_time%60;
                if($m == 0 && $s == 0)
                {
                    return false;
                }
                $_m = $m ? $m.'分':'';
                return '操作过于频繁，请'.$_m.$s.'秒后再试。';
            }
            $redis->HINCRBY($key,'num','1');
        } else{
            $redis->HINCRBY($key,'num','1');
            $redis->expire($key, self::LIMIT_TIME);
        }
        return false;
    }



}