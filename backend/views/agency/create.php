<?php

/* @var $this yii\web\View */
/* @var $model backend\models\Agency */

$this->title = '新增单位';
$this->params['breadcrumbs'][] = ['label' => '用户', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '单位管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-create">

    <?= $this->render('_form', [
        'model' => $model,
        'list'=>$list
    ]) ?>

</div>
