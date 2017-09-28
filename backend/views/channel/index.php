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

    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'layout' => "{items}\n  <div><ul class='pagination'><li style='display:inline;'><span>共".$dataProvider->getTotalCount(). "条数据 <span></li></ul>{pager}  </div>",
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            [
                "format" => [
                    "image",
                    [
                        "width"=>"100",
                        "height"=>"100"
                    ]
                ],
                'attribute' => 'img_url',
                'value' => function($data) {
                    return  Yii::$app->params['fileBaseDomain'].$data->img_url;
                }
            ],
            [
                "format" => [
                    "image",
                    [
                        "width"=>"100",
                        "height"=>"100"
                    ]
                ],
                'attribute' => 'gray_img_url',
                'value' => function($data) {
                    return  Yii::$app->params['fileBaseDomain'].$data->gray_img_url;
                }
            ],
            [
                'attribute' => 'create_at',
                'format'=>['date', 'php:Y-m-d H:i:s'],
            ],
            [
                'attribute' => 'update_at',
                'format'=>['date', 'php:Y-m-d H:i:s'],
            ],
            'sort',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <p class="text-right">
        <?= Html::a('新增渠道', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
</div>
