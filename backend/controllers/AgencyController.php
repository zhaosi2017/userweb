<?php

namespace backend\controllers;

use Yii;
use backend\models\Agency;
use backend\models\AgencySearch;
use backend\controllers\PController;
use yii\web\NotFoundHttpException;

/**
 * CompanyController implements the CRUD actions for Company model.
 */
class AgencyController extends PController
{

    /**
     * Lists all Company models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AgencySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Company models.
     * @return mixed
     */
    public function actionTrash()
    {
        $searchModel = new AgencySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('trash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Company model.
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
     * Creates a new Company model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Agency();
        $list = $model->getOptions();
        if ($model->load(Yii::$app->request->post()) ) {
            if($model->create()){
                $model->sendSuccess();
                return $this->redirect(['index', 'id' => $model->id]);
            }else{
                return $this->render('create', [
                    'model' => $model,
                    'list'=>$list,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'list'=>$list,
            ]);
        }
    }

    /**
     * Updates an existing Company model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);
        $model->setScenario('update');
        $list = $model->getOptions(['not in','id',$id]);
        if ($model->load(Yii::$app->request->post())) {
            if($model->update()){
                $model->sendSuccess();
                return $this->redirect(['index', 'id' => $model->id]);
            }else{
                return $this->render('update', [
                    'model' => $model,
                    'list'=>$list,
                ]);
            }

        } else {
            return $this->render('update', [
                'model' => $model,
                'list'=>$list,
            ]);
        }
    }

    /**
     * Deletes an existing Company model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$status)
    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
        $model = $this->findModel($id);

        if(!array_key_exists($status,Agency::$AGENCY_STATUS))
        {
            $model->sendError('您操作的状态有误！');
            return $this->redirect(['index']);
        }
        if($status == Agency::INVALID_STATUS){
            if(Agency::findOne(['parent_id' => $id])){
                $model->sendError('当前公司下具有状态为正常的公司，不能作废！');
                return $this->redirect(['index']);
            }
        }
        if($status == Agency::NORMAL_STATUS && $model->parent_id != 0)
        {

            $res = Agency::findOne(['id' => $model->parent_id]);
            if(empty($res) || (!empty($res) && $res->status == Agency::INVALID_STATUS))
            {
                $model->sendError('该单位的上级单位为空或者上级单位已作废');
                return $this->redirect(['trash']);
            }
        }

        $model->status = $status;
        if($model->save()){
            Yii::$app->getSession()->setFlash('success', '操作成功');
        }else{
            Yii::$app->getSession()->setFlash('error', '操作失败');
        }
        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @param $status
     * @return \yii\web\Response
     */
    public function actionSwitch($id, $status)
    {

        $model = $this->findModel($id);

        if($status == Agency::INVALID_STATUS){
            if(Agency::findOne(['parent_id' => $id])){
                $model->sendError('当前单位下具有状态为正常的公司，不能作废！');
                return $this->redirect(['index']);
            }
        }

        $model->status = $status;
        if($model->save()){
            Yii::$app->getSession()->setFlash('success', '操作成功');
        }else{
            Yii::$app->getSession()->setFlash('error', '操作失败');
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Company model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Company the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Agency::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
