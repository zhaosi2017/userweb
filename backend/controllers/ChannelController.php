<?php

namespace backend\controllers;

use backend\models\UploadForm;
use Yii;
use backend\models\Channel;
use backend\models\ChannelSearch;
use backend\controllers\PController;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ChannelController implements the CRUD actions for Channel model.
 */
class ChannelController extends PController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Channel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ChannelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Channel model.
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
     * Creates a new Channel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Channel();
        $upload = new UploadForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'upload' => $upload,
            ]);
        }
    }

    /**
     * Updates an existing Channel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $upload = new UploadForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'upload' => $upload,
            ]);
        }
    }

    /**
     * Deletes an existing Channel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Channel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Channel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Channel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 上传图片.
     */
    public function actionUpload()
    {
        $uploadForm = new UploadForm();
        if(Yii::$app->request->isPost){
            $uploadForm->imageFile = UploadedFile::getInstance($uploadForm, 'imageFile');
            if($imageUrl = $uploadForm->upload()){
                file_put_contents('/tmp/aaa.txt', var_export($imageUrl, true).PHP_EOL, 8);
                echo Json::encode([
                    'imageUrl'    => $imageUrl,
                    // 上传的error字段，如果没有错误就返回空字符串，否则返回错误信息，客户端会自动判定该字段来认定是否有错.
                    'error'   => '',
                ]);
            }else{
                echo Json::encode([
                    'imageUrl'    => '',
                    'error'   => '文件上传失败'
                ]);
            }
        }
    }

    /**
     * 上传图片.
     */
    public function actionUploadGray()
    {
        $uploadForm = new UploadForm();
        if(Yii::$app->request->isPost){
            $uploadForm->imageFile = UploadedFile::getInstance($uploadForm, 'imageGrayFile');
            if($imageUrl = $uploadForm->upload()){
                file_put_contents('/tmp/aaa.txt', var_export($imageUrl, true).PHP_EOL, 8);
                echo Json::encode([
                    'imageUrl'    => $imageUrl,
                    // 上传的error字段，如果没有错误就返回空字符串，否则返回错误信息，客户端会自动判定该字段来认定是否有错.
                    'error'   => '',
                ]);
            }else{
                echo Json::encode([
                    'imageUrl'    => '',
                    'error'   => '文件上传失败'
                ]);
            }
        }
    }

}
