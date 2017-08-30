<?php

namespace backend\controllers;

use backend\models\Admin;
use backend\models\AuthAssignment;
use backend\models\AuthItemChild;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use Yii;
use backend\models\AuthItem;
use backend\models\AuthItemSearch;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 */
class AuthitemController extends PController
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
     * 角色列表.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch();
        $searchModel->type = Item::TYPE_ROLE;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 新增角色.
     *
     * @return mixed
     */
    public function actionRoleCreate($id = null)
    {
        if (empty($id)) {
            $model = new RoleForm();
        } else {
            $model = $this->findModel($id);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        } else {
            return $this->render('role-create', [
                'model' => $model,
            ]);
        }
        //权限列表( 添加角色的时候我们就可看到当前有没有权限来添加 )
        // $permissions = $this->loadPermission();
    }

    /**
     * 权限列表.
     */
    public function actionPrivilege()
    {
        $searchModel = new AuthItemSearch();
        $searchModel->type = Item::TYPE_PERMISSION;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('privilege', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 权限添加.
     */
    public function actionPermissionCreate($id = null) {

        if (empty($id)) {
            $model = new PermissionForm();
        } else {
            $model = $this->findModel($id);
        }

        if( $model->load( Yii::$app->request->post()) && $model->save() ) {
            return $this->redirect('privilege');
        } else {
            return $this->render('permission-create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param integer $id 角色id.
     *
     * @return string|\yii\web\Response
     */
    public function actionAuth($id)
    {
        $model = $this->findModel($id);

        $auth = Yii::$app->authManager;
        if ($model->load(Yii::$app->request->post())) {
            $posts = Yii::$app->request->post();

            if (isset($posts['Auth'])) {
                AuthItemChild::deleteAll('parent=:id', [':id' => $model->name]);
                $role = $auth->getRole($model->name);
                //重新分配许可
                foreach ($posts['Auth'] as $permission){
                    $permissionData = $auth->getPermission($permission);
                    $auth->addChild($role, $permissionData);
                }
            } else {
                AuthItemChild::deleteAll('parent=:id', [':id' => $model->name]);
            }

            return $this->redirect(['index', 'id' => $model->name]);
        } else {
            $alreadyAuth = $auth->getChildren($model->name);
            $alreadyAuth = array_keys($alreadyAuth);
            // 查出所以权限.
            $moduleArr = [
                'admin' => '用户模块',
                'authitem' => '角色模块'
            ];

            $allPrivilegesArray = [];
            $allPrivileges = AuthItem::find()
                ->select(['name', 'description'])
                ->where(['type' => 2])
                ->orderBy('description')
                ->all();
            foreach ($allPrivileges as $pri) {
                $tmp = [];
                $tmodule = explode('/', $pri->name);
                $tmp['route'] = $pri->name;
                $tmp['description'] = $pri->description;
                $module = array_shift($tmodule);
                $allPrivilegesArray[$module][] = $tmp;
            }

            // 角色已经拥有的权限.
            return $this->render(
                'auth',
                [
                    'model' => $model,
                    'moduleArr' => $moduleArr,
                    'allPrivileges' => $allPrivilegesArray,
                    'alreadyAuth' => $alreadyAuth,
                ]
            );
        }
    }

    /**
     * 给用户分配角色.
     *
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionUserRole($id)
    {
        $model = Admin::findOne($id);
        // 获取当前所以角色.
        $allRoles = Yii::$app->authManager->getRoles();
        $allRolesArray = ArrayHelper::map($allRoles, 'name', 'description');

        // 当前用户的权限.
        $userRoles =  Yii::$app->authManager->getRolesByUser($id);
        $userRolesArray = array_keys($userRoles);

        // 用表单提交过来的数据更新AuthAssignment, 从而用户角色发生变化.
        if (isset($_POST['newPri'])) {
            AuthAssignment::deleteAll('user_id=:id', [':id' => $id]);
            $newPri = $_POST['newPri'];
            $length = count($newPri);
            for($i=0; $i<$length; $i++) {
                $aPri = new AuthAssignment();
                $aPri->item_name = $newPri[$i];
                $aPri->user_id = $id;
                $aPri->created_at = time();

                $aPri->save();
            }

            return $this->redirect('/admin/index');
        }

        // 渲染checkboxlist表单.
        return $this->render('user-role', ['id' => $id, 'model' => $model, 'allRolesArray' => $allRolesArray, 'userRolesArray' => $userRolesArray]);
    }

    /**
     * Displays a single AuthItem model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->name]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
