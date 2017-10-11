<?php

namespace backend\controllers;

use backend\models\PasswordForm;
use backend\models\Reports\RetainedReportSearch;
use frontend\models\UserLoginLogs\UserLoginLog;
use Yii;
use backend\models\Admin;
use backend\models\AdminSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\controllers\PController;
/**
 * ManagerController implements the CRUD actions for Manager model.
 */
class ReportController extends PController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 流存
     */
    public function actionRetained()
    {
        $searchModel = new RetainedReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('retained', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    public function ActionActiveDay()
    {
        UserLoginLog::find()->select('user_id')->where(['>','login_time',strtotime(date('Y-m-d'))])->andWhere(['>','login_time',strtotime(date('Y-m-d'))])->distinct()->count();
    }
}