<?php

namespace backend\controllers;

use backend\models\PasswordForm;
use Yii;
use backend\models\Admin;
use backend\models\AdminSearch;
use backend\controllers\PController;
use yii\web\NotFoundHttpException;
use backend\models\MyDbManager;
use backend\models\LoginLogsSearch;
/**
 * ManagerController implements the CRUD actions for Manager model.
 */
class AdminController extends PController
{

    public function actionPassword()
    {
        $model = new PasswordForm();
        if($model->load(Yii::$app->request->post()) ){
            if($res = $model->updateSave()){
                Yii::$app->getSession()->setFlash('success', '密码修改成功');
                return $this->redirect(['default/index'])->send();
            }else{
                Yii::$app->getSession()->setFlash('error', '密码修改失败');
                return $this->render('password',['model' => $model]);
            }

        }
        return $this->render('password',['model' => $model]);
    }

    /**
     * Lists all Manager models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTrash()
    {
        $searchModel = new AdminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('trash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Manager model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Manager model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Admin();
        $model->scenario = 'addadmin';
        if ($model->load(Yii::$app->request->post()) && $model->create()) {
            //权限逻辑
            $auth = Yii::$app->authManager;
            //获取角色
            $role = $auth->getRole($model->role_id);
            //给用户分配角色
            $auth->assign($role, $model->id);

            $model->sendSuccess();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Manager model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->scenario = 'updateadmin';
//            $model->deleteLoginNum();
            if($model->save()) {
                $auth = Yii::$app->authManager;
                $myDbmanger = new MyDbManager();
                $myDbmanger->updateAssignment($model->role_id,$model->id);
                $model->sendSuccess();
                return $this->redirect(['index']);
            } else{
                return $this->render('update', [
                    'model' => $model,
                ]);
            }

        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Manager model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->updateAttributes(['update_at'=>time(),'update_id'=>Yii::$app->user->id,'status'=>1]) ? $model->sendSuccess() : $model->sendError();
        return $this->redirect(['index']);
    }

    public function actionRecover($id)
    {
        $model = $this->findModel($id);
        $model->updateAttributes(['update_at'=>time(),'update_id'=>Yii::$app->user->id,'status'=>0]) ? $model->sendSuccess() : $model->sendError();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Manager model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Manager the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Admin::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionLoginLogs()
    {
        $searchModel = new LoginLogsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('login_logs', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

}
