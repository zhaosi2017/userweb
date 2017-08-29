<?php
namespace frontend\models;
class ErrCode
{
    CONST SUCCESS = '0000'; //操作成功
    CONST FAILURE = '0001'; //操作失败
    CONST VALIDATION_NOT_PASS = '1001'; //验证不通过
    CONST CODE_ERROR = '1000';//验证码错误
    CONST NETWORK_ERROR = '9000';//网络错误
    CONST USER_PHONE_EXIST ='2000';//用户手机已经存在
    CONST NETWORK_OR_PHONE_ERROR = '9001';//网络和手机号错误
    CONST COUNTRY_CODE_OR_PHONE_EMPTY = '2001';//国码或手机号为空
    CONST DATA_SAVE_ERROR = '3000';//数据保存错误
    const UNKNOWN_ERROR = '4000';//未知错误
    const NICKNAME_EMPTY = '2002';//昵称为空



}