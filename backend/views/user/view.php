<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Channel */

$this->title = $model->account;
$this->params['breadcrumbs'][] = ['label' => '渠道列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channel-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'account',
            'email',
            'token',
            'username',
            'nickname',
            'channel',
            'country_code',
            'phone_number',
            'whitelist_switch',
            'language',
            'balance',
            'address',
            'longitude',
            'latitude',
            'header_img',
            'reg_ip',
            'reg_time',
            'login_ip',

            [
                "format" => [
                    "image",
                    [
                        "width"=>"100",
                        "height"=>"100"
                    ]
                ],
                'attribute' => 'header_img',
                'value' => function($data) {
                    return  Yii::$app->params['frontendBaseDomain'].$data->header_img;
                }
            ],
            [
                "attribute" => 'reg_time',
                'format'=> ['date', 'php:Y-m-d H:i:s'],
            ],
            [
                "attribute" => 'login_time',
                'format'=> ['date', 'php:Y-m-d H:i:s'],
            ],
        ],
    ]) ?>

    <p class="text-right">
        <?= Html::a('返回', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

</div>
