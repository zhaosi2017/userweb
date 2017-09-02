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
    const USER_NOT_EXIST = '2003';//用户不存在
    const CHANNEL_EMPTY = '2004';//渠道为空
    const ILLEGAL_OPERATION = '4001';//非法操作
    const PASSWORD_EMPTY = '2005'; //密码不能为空
    const SECURITY_QUESTION_NOT_SET = '2006';//安全密保问题没有设置
    const COUNTRY_CODE_EMPTY = '2007';//国码为空
    const PHONE_EMPTY = '2008';//手机为空
    const QUESTIONS_EMPTY = '2009';//问题表为空
    const CHANNEL_ILLEGAL ='2010';//渠道非法
    const COUNTRY_CODE_PHONE_EXIST = '2011';//国码手机号已存在
    const PHONE_TOTAL_NOT_OVER_TEN ='2012';//用户手机号不能超过十个
    const DELETE_FAILURE = '2013';//删除失败
    const URGENT_CONTACT_EXIST = '2014';//紧急联系人已经添加过拉
    const WHITE_SWITCH_STATUS_EMPTY = '2015';//白名单开关状态为空
    const SEARCH_WORDS_EMPTY = '2016';//搜索的关键字为空
    const ACCOUNT_EMPTY ='2017';//优码不能为空
    const USER_NO_ADD_SELF = '2018';//用户不能添加自己为好友
    const USER_ADD_FRIEND_REQUEST_EXIST = '2019';//用户已经向某优客发送拉请求，不能重复发送



}