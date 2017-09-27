<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Channel */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '渠道列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channel-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'typeName',
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
                "attribute" => 'create_at',
                'format'=> ['date', 'php:Y-m-d H:i:s'],
            ],
            [
                "attribute" => 'update_at',
                'format'=> ['date', 'php:Y-m-d H:i:s'],
            ],
        ],
    ]) ?>

    <p class="text-right">
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

</div>
