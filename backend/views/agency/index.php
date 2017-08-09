<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\AgencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '公司管理 ';
$this->params['breadcrumbs'][] = ['label'=>'用户','url'=>''];
$this->params['breadcrumbs'][] = $this->title;
$actionId = Yii::$app->requestedAction->id;
?>
<div class="company-index">

    <p class="btn-group hidden-xs">
        <?= Html::a('公司列表', ['index'], ['class' => $actionId=='trash' ? 'btn btn-outline btn-default' : 'btn btn-primary']) ?>
        <?= Html::a('垃圾筒', ['trash'], ['class' => $actionId=='index' ? 'btn btn-outline btn-default' : 'btn btn-primary']) ?>
    </p>

    <div class="help-block m-t"></div>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
//        'layout'=> '{items}<div class="text-right tooltip-demo">{pager}</div>',
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

            ['label' => '公司名称', 'attribute'=>'name', 'value' => function($model){
                return $model->name;
            }],

            ['label' => '上级公司', 'value' => function($model){
                $superior = $model['superior']['name'];
                if(!$superior){
                    return '无';
                }
                return $superior;
            }],

            [
                'class' => 'yii\grid\DataColumn',
                'header' => '状态',
                'value'  => function ($data)
                {
                    $status = '';
                    switch ($data->status){
                        case 0:
                            $status = '正常';
                            break;
                        case 1:
                            $status = '已作废';
                    }
                    return $status;
                },
            ],

            [
                'class' => 'yii\grid\DataColumn',
                'header' => '层级',
                'value'  => function ($data)
                {
                    return $data->level . '级';
                },
            ],

            [
                'class' => 'yii\grid\DataColumn',
                'header' => '创建人／时间',
                'format' => 'html',
                'value'  => function ($data)
                {
                    return $data['creator']['account'] . '<br>' . $data->create_time;
                },
            ],

            [
                'class' => 'yii\grid\DataColumn',
                'header' => '最后修改人／时间',
                'format' => 'html',
                'value'  => function ($data)
                {
                    return $data['updater']['account'] . '<br>' . $data->update_time;
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{update} {switch}',
                'buttons' => [
                    'update' => function($url){
                        return Html::a('编辑',$url);
                    },
                    'switch' => function($url, $model){
                        $btn_link = '';
                        switch ($model->status){
                            case 0:
                                $btn_link = Html::a('作废',
                                    $url . '&status=1',
                                    [
//                                        'class' => 'btn btn-xs',
                                        'style' => 'color:red',
                                        'data-method' => 'post',
                                        'data' => ['confirm' => '你确定要作废吗?']
                                    ]);
                                break;
                            case 1:
                                $btn_link = Html::a('恢复',
                                    $url . '&status=0',
                                    [
//                                        'class' => 'btn btn-xs',
                                        'data-method' => 'post',
                                        'data' => ['confirm' => '你确定要恢复吗?']
                                    ]);
                                break;
                        }
                        return $btn_link;
                    },

                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    <p class="text-right ">
        <?= Html::a('新增公司', ['create'], ['class'=>'btn btn-primary btn-sm']) ?>
    </p>
</div>