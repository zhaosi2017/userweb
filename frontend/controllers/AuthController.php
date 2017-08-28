<?php
namespace frontend\controllers;

use yii\web\Controller;

/**
 * 权限验证.
 *
 * @package frontend\controllers
 */
class AuthController extends Controller
{
    public function jsonResponse($data,$message,$status = 0)
    {
        return ['data'=>$data,'message'=>$message,'status'=>$status];
    }
}