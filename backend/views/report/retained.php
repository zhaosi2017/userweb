<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '留存报表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channel-index">

    <?php  echo $this->render('retained_search', ['model' => $searchModel]); ?>


   
</div>
