<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '渠道列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channel-index">

    <?php  echo $this->render('user_number_search', ['model' => $searchModel]); ?>
    <?php var_dump($searchModel); ?>
    <p class="text-right">
        <?= Html::a('新增渠道', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
</div>
