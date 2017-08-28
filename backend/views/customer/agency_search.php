<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\task\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '上级单位选择';
$this->params['breadcrumbs'][] = $this->title;
$actionId = Yii::$app->requestedAction->id;

?>
<div class="product-index">

    <p class="btn-group hidden-xs">
          </p>

    <?php  echo $this->render('_agency', ['model' => $searchModel]); ?>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
            ['class' => 'yii\grid\SerialColumn','header' => '序号'],
            [
                'class' => 'yii\grid\DataColumn',
                'header' => '单位名称',
//                'headerOptions' => ['width' => '120'] ,
                'attribute' => 'name',
                'value'  => function ($data)
                {
                    return $data->name ;
                },
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'header' => '编号',
//                'headerOptions' => ['width' => '120'] ,
                'attribute' => 'name',
                'value'  => function ($data)
                {
                    return $data->code ;
                },
            ],



            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{choose}',
                'buttons' => [
                    'choose' => function($url, $model)
                    {
                        $btn_link = Html::button('选择',
                            [
                                'class' => 'btn btn-primary',
                                'onclick' => '
                                    var url = \''.$url.'\';
                                    var $body = parent.$("body");

                                    $body.find("#task-product_id").val(\''.$model->id.'\');
                                    $body.find("#product-name-number").html(\''.$model->name.'\' + \'('.$model->code.')\');
                                    $body.find("#customer-group_id").val(\''.$model->id.'\');
                                    var index = parent.layer.getFrameIndex(window.name); 
                                    parent.layer.close(index); 
                                ',
                            ]);
                        return $btn_link;
                    },
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?></div>
