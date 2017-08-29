<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AuthItem */

if ($model->isNewRecord) {
    $this->title = '新增权限';
} else {
    $this->title = '编辑权限: '.$model->name;
}
$this->params['breadcrumbs'][] = ['label' => '权限列表', 'url' => ['privilege']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
