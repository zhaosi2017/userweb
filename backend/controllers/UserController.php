<?php

namespace backend\controllers;

use backend\models\PasswordForm;
use Yii;
use backend\models\Admin;
use backend\models\AdminSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\controllers\PController;
/**
 * ManagerController implements the CRUD actions for Manager model.
 */
class UserController extends PController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}