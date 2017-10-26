<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\CallRecord\CallRecord;
use backend\models\Reports\CountryAddress;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '呼叫记录列表';
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
            [
                'class' => 'yii\grid\DataColumn',
                'header' => '主叫',
                'value'  => function ($data)
                {
                    return $data['active']['account'];
                },
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'header' => '被叫',
                'value'  => function ($data)
                {
                    return $data['user']['account'];
                },
            ],
            [
                'header'=>'主叫国码',
                'attribute' => 'active_code',
                'value'=>function ($data)
                {
                    return isset(CountryAddress::$codeAddress[$data['active_code']]) ? CountryAddress::$codeAddress[$data['active_code']] :'未知';
                },
            ],
            [
                'header'=>'呼叫文本',
                'attribute' => 'text',
            ],
            [
                'header'=>'呼叫ID',
                'attribute' => 'call_id',
            ],
            [
                'header'=>'呼叫组ID',
                'attribute' => 'group_id',
            ],
//            'duration',
            [
                'header'=>'呼叫状态',
                'attribute' => 'status',
                'value'=>function ($data)
                {
                    return isset(CallRecord::$status_map[$data['status']]) ? CallRecord::$status_map[$data['status']] :'未知';
                },
            ],
            [
                    'header'=>'呼叫类型',
                'attribute' => 'type',
                'value'=>function ($data)
                {
                    return isset(CallRecord::$type_map[$data['type']]) ? CallRecord::$type_map[$data['type']] :'未知';
                },
            ],
            [
                'header'=>'主叫联系电话',
                'attribute' => 'contact_number',
            ],
            [
                'header'=>'被叫联系电话',
                'attribute' => 'unactive_contact_number',
            ],

            [
                'header'=>'呼叫时间',
                'attribute' => 'call_time',
                'format'=>['date', 'php:Y-m-d H:i:s'],
            ],


//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <p class="text-right">

    </p>
</div>
