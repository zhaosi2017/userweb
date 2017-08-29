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
class DefaultController extends PController
{
    public function actionIndex()
    {
       return $this->render('index');
    }

    public function actionPassword()
    {
        $model = new PasswordForm();
        if($model->load(Yii::$app->request->post()) ){
            if($res = $model->updateSave()){
                Yii::$app->getSession()->setFlash('success', '密码修改成功');
                return $this->redirect(['index'])->send();
            }else{
                Yii::$app->getSession()->setFlash('error', '密码修改失败');
                return $this->render('password',['model' => $model]);
            }

        }
        return $this->render('password',['model' => $model]);
    }

    public function actionDeny()
    {
        return $this->render('deny');
    }
}