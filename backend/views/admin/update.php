<?php

/* @var $this yii\web\View */
/* @var $model backend\models\Admin */

$this->title = '修改管理员: ' . $model->account;
$this->params['breadcrumbs'][] = ['label' => '管理员列表', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改管理员';
?>
<div class="manager-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
