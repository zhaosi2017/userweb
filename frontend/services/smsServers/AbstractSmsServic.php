<?php

namespace frontend\services\smsServers;

abstract class  AbstractSmsServic{

    abstract public function sendSms($number,$msg);
}