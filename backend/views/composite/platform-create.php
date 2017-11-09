<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Channel */

$this->title = '新建平台版本';
$this->params['breadcrumbs'][] = ['label' => '平台版本列表', 'url' => ['platform-index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channel-create">


    <?= $this->render('platform_form', [
        'model' => $model,
        'upload' => $upload,
    ]) ?>

</div>
