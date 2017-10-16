<?php

namespace backend\controllers;

use backend\models\PasswordForm;
use backend\models\Reports\ActiveDaySearch;
use backend\models\Reports\RetainedReportSearch;
use backend\models\Reports\UserNumberSearch;
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

    /**
     * 活跃日报表
     */
    public function actionActiveDay()
    {
        $activeDaySearch = new ActiveDaySearch();
        $searchModel = $activeDaySearch->search(Yii::$app->request->queryParams);
        return $this->render('active_day', [
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 用户数日报表
     */
    public function actionUserNumber()
    {
        $userNumberSearchModel = new UserNumberSearch();
        $searchModel = $userNumberSearchModel->search(Yii::$app->request->queryParams);


        return $this->render('user_number', [
            'searchModel' => $searchModel,
        ]);
    }
}