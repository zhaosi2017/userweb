<?php

namespace backend\controllers;

use Yii;
use backend\models\Role;
use backend\models\RoleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\MyDbManager;
use backend\controllers\PController;
/**
 * RoleController implements the CRUD actions for Role model.
 */
class RoleController extends PController
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
     * Lists all Role models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTrash()
    {
        $searchModel = new RoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('trash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Role model.
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
     * Creates a new Role model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Role();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //创建角色
            $auth = Yii::$app->authManager;
            //添加角色[角色编号对应这类角色]
            $role = $auth->createRole($model->id);
            $role->description = '角色编号-' . $model->id;
            $auth->add($role) && $model->sendSuccess();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionAuth($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $posts = Yii::$app->request->post();

            if(!empty($posts['Auth'])){

                //创建许可，给角色分配许可，并创建它们的层次关系
                $auth = Yii::$app->authManager;
                $role = $auth->createRole($model->id);

                //如果获取不到角色就添加角色
                $role->description = '角色编号:' . $model->id;
                $auth->getRole($model->id) || $auth->add($role) ;

                //重新分配许可
                empty($auth->getChildren($model->id)) || $auth->removeChildren($role);

                foreach ($posts['Auth'] as $permission){
                    //添加权限
                    $permissionData = $auth->createPermission($permission);
                    $permissionData->description = 'permission: '.$permission;

                    //如果能获取到许可就不再添加许可
                    $auth->getPermission($permission) || $auth->add($permissionData);

                    $auth->addChild($role, $permissionData);
                }
            }
            $model->update_id =   Yii::$app->user->id ? Yii::$app->user->id : 0;
            $model->update_at = $_SERVER['REQUEST_TIME'];
            $model->save();
            $model->sendSuccess('权限设置成功');
            return $this->redirect(['auth', 'id' => $model->id]);
        } else {
            return $this->render('auth', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Role model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->save() ? $model->sendSuccess() : $model->sendSuccess();
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Role model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $auth = Yii::$app->authManager;
        $myDbmanger = new MyDbManager();
        $role = $myDbmanger->checkAssignment($model->id);
        switch ($role) {
            case 0:
                $model->status = 1;
                $model->update() && $model->sendSuccess();
                break;
            case false:
                $model->sendError('不存在该角色');
                break;
            default:
                $model->sendError('角色'.$model->name.'已被'.$role.'位管理员使用，请解除使用后再进行删除操作！', 5);
                break;
        }

        return $this->redirect(['index']);

    }

    public function actionRecover($id)
    {
        $model = $this->findModel($id);
        $model->status = 0;
        $model->update() && $model->sendSuccess();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Role model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Role the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Role::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
