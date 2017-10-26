<?php

namespace backend\controllers;

use backend\models\PasswordForm;
use backend\models\Users\UserSearch;
use Yii;
use backend\models\Admin;
use backend\models\AdminSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\controllers\PController;
use frontend\models\User;
use backend\models\CallRecords\CallRecordSearch;
/**
 * ManagerController implements the CRUD actions for Manager model.
 */
class CallRecordController extends PController
{

    /**
     * Lists all Channel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CallRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
