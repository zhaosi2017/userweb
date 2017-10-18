<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '前台用户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channel-index">

    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'layout' => "{items}\n  <div><ul class='pagination'><li style='display:inline;'><span>共".$dataProvider->getTotalCount(). "条数据 <span></li></ul>{pager}  </div>",
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'id',
            'account',
            'token',
            'nickname',
            'username',
            'country_code',
            'phone_number',
//            ['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url){
                        return Html::a('查看',$url);
                    },


                ],
            ],
        ],
    ]); ?>


</div>
