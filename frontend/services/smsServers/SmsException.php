<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\services\smsServers;
use yii\web\HttpException;
/**
 * UnauthorizedHttpException represents an "Unauthorized" HTTP exception with status code 401
 *
 * Use this exception to indicate that a client needs to authenticate via WWW-Authenticate header
 * to perform the requested action.
 *
 * If the client is already authenticated and is simply not allowed to
 * perform the action, consider using a 403 [[ForbiddenHttpException]]
 * or 404 [[NotFoundHttpException]] instead.
 *
 * @link https://tools.ietf.org/html/rfc7235#section-3.1
 * @author Dan Schmidt <danschmidt5189@gmail.com>
 * @since 2.0
 */
class SmsException extends HttpException
{
    /**
     * Constructor.
     * @param string $message error message
     * @param int $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = null, $code = 0,$status, \Exception $previous = null)
    {
        parent::__construct($status, $message, $code, $previous);
    }
}