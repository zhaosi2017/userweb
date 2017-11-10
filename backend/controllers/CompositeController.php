<?php

namespace backend\controllers;

use backend\models\PasswordForm;
use backend\models\Composites\CompositePlatformSearch;
use backend\models\Users\UserSearch;
use backend\models\Composites\VersionForm;
use frontend\models\Versions\Version;
use Yii;
use backend\models\Admin;
use backend\models\AdminSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\controllers\PController;
use frontend\models\User;
use backend\models\Composites\PlatformUploadForm;
use yii\web\UploadedFile;
use yii\helpers\Json;

/**
 * ManagerController implements the CRUD actions for Manager model.
 */
class CompositeController extends PController
{
    public function actionPlatformIndex()
    {
        $searchModel = new CompositePlatformSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('platform-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionPlatformCreate()
    {
        $model = new VersionForm();
        $upload = new PlatformUploadForm();

        if ($model->load(Yii::$app->request->post()) ) {
            $model->save();
            return $this->redirect(['platform-index', 'id' => $model->id]);
        } else {
            return $this->render('platform-create', [
                'model' => $model,
                'upload' => $upload,
            ]);
        }
    }


    public function actionPlatformUpload()
    {
        $uploadForm = new PlatformUploadForm();
        if(Yii::$app->request->isPost){
            $uploadForm->url = UploadedFile::getInstance($uploadForm, 'url');

            if($imageUrl = $uploadForm->upload()){
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


    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}