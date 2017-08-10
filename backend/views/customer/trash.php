<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '客户';
$this->params['breadcrumbs'][] = $this->title;
$actionId = Yii::$app->requestedAction->id;
?>
<div class="customer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a('单位列表', ['index'], ['class' => $actionId=='trash' ? 'btn btn-outline btn-default' : 'btn btn-primary']) ?>
        <?= Html::a('垃圾筒', ['trash'], ['class' => $actionId=='index' ? 'btn btn-outline btn-default' : 'btn btn-primary']) ?>

    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'layout' => "{items}\n  <div><ul class='pagination'><li style='display:inline;'><span>共".$dataProvider->getTotalCount(). "条数据 <span></li></ul>{pager}  </div>",
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭自带分页
            'firstPageLabel'=>"首页",
            'prevPageLabel'=>'上一页',
            'nextPageLabel'=>'下一页',
            'lastPageLabel'=>'末页',
            'maxButtonCount' => 9,
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'code',
            'name',
            'number',
            'aide_name',
             'group_id',
             'level',
             'type',
             'company',
             'create_at:datetime',
             'update_at:datetime',
             'admin_id',
             'update_id',

            ['class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{recover}',
                'buttons' => [
                    'recover' => function($url){
                        $btn_link = Html::a('恢复',
                            $url ,
                            [
//                                        'class' => 'btn btn-xs',
                                'style' => 'color:red',
                                'data-method' => 'post',
                                'data' => ['confirm' => '你确定要恢复吗?']
                            ]);

                        return $btn_link;
                    },
                    ]
            ],
        ],
    ]); ?>
</div>
