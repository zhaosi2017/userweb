<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Role */

$this->title = '创建角色';
$this->params['breadcrumbs'][] = ['label' => '角色', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
