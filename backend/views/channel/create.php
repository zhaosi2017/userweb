<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Channel */

$this->title = '新建渠道';
$this->params['breadcrumbs'][] = ['label' => '渠道列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channel-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
