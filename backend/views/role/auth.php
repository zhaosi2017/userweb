<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Role */

$this->title = '权限设置角色: ' . $model->name ;


$this->title = '权限授权';
$this->params['breadcrumbs'][] = ['label' => '管理员角色', 'url' => ['index']];
$this->params['breadcrumbs'][] = '权限授权' ;

$actionId = Yii::$app->requestedAction->id;
$auth = Yii::$app->authManager;
$permissions = $auth->getPermissionsByRole($model->id);

$labels = [
    0 =>'进入',
    1 =>'查询',
    2 =>'创建',
    3 =>'修改',
    4 =>'恢复',
    5 =>'删除',
    6 =>'防骚扰',
    7 =>'修改密码',
];

$data = [
    [
        'module' => '首页模块',
        'items' => [
            [
                'page_name' => '首页',
                'permission' => ['default/index', 7=>'default/password'],//注意顺序
            ],
        ],

    ],


    [
        'module' => '后台用户模块',
        'items' => [
            [
                'page_name' => '管理员角色',
                'permission' => ['role/index',2=>'role/create',3=>'role/update',5=>'role/delete',],
            ],
            [
                'page_name'=>'权限授权',
                'permission'=>['role/auth'],
            ],
            [
                'page_name' => '管理员角色-垃圾桶',
                'permission' => ['role/trash',4=>'role/recover'],
            ],
            [
                'page_name' => '管理员列表',
                'permission' => ['admin/index',2=>'admin/create',3=>'admin/update',5=>'admin/delete'],
            ],
            [
                'page_name' => '管理员列表-垃圾桶',
                'permission' => ['admin/trash',4=>'admin/recover'],
            ],
            [
                'page_name' => '登录日志',
                'permission' => ['admin/login-logs',],
            ],
        ],

    ],
    [
        'module' => '客户管理',
        'items' => [
            [
                'page_name' => '客户管理',
                'permission' => ['customer/index',2=>'customer/create',3=>'customer/update',5=>'customer/delete',],
            ],
        ],

    ],

];
?>
<div class="posts-grid">

    <div class="table-responsive">
        <form action="" method="post" id="w0">
            <input type="hidden" name="Role[id]" value="<?= $model->id ?>">
            <input type="hidden" name="_csrf" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">

            <?php foreach ($data as $k=>$v){ ?>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th colspan="3"><?= $v['module'] ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="text-center" width="10%">序号</td>
                        <td class="text-center" width="20%">页面名称</td>
                        <td>权限</td>
                    </tr>
                    <?php foreach ($v['items'] as $key=>$val){ ?>
                        <tr>
                            <td class="text-center"><?= ++$key; ?></td>
                            <td class="text-center"><?= $val['page_name'] ?></td>
                            <td>
                                <?php foreach ($val['permission'] as $i=>$permission){ ?>
                                    <label for="<?= 'I-'.$k.$key.$i ?>">
                                        <input id="<?= 'I-'.$k.$key.$i ?>" <?= array_key_exists($permission, $permissions) ? 'checked="checked"' : ' ' ?> type="checkbox" name="Auth[<?= 'I-'.$k.$key.$i ?>]" value="<?= $permission ?>">
                                        <?php
                                        $label = $labels[$i];
                                        echo $label . '&nbsp;&nbsp;&nbsp;&nbsp;';
                                        ?>
                                    </label>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>

                </table>
            <?php } ?>

            <div class="form-group">
                <div class="text-right">
                    <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>

        </form>
    </div>


</div>
