<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Role */

$this->title = '修改角色: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '管理员角色', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更改角色';
?>
<div class="role-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
