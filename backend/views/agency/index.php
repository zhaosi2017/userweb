<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\AgencySearch;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\AgencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '单位管理 ';
$this->params['breadcrumbs'][] = ['label'=>'用户','url'=>''];
$this->params['breadcrumbs'][] = $this->title;
$actionId = Yii::$app->requestedAction->id;
?>
<div class="company-index">

    <p class="btn-group hidden-xs">
        <?= Html::a('单位列表', ['index'], ['class' => $actionId=='trash' ? 'btn btn-outline btn-default' : 'btn btn-primary']) ?>
        <?= Html::a('垃圾筒', ['trash'], ['class' => $actionId=='index' ? 'btn btn-outline btn-default' : 'btn btn-primary']) ?>
    </p>

    <div class="help-block m-t"></div>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin(); ?>
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
            ['class' => 'yii\grid\SerialColumn','header' => '序号'],

            ['label' => '单位名称', 'attribute'=>'name', 'value' => function($model){
                return $model->name;
            }],
            ['label' => '单位编号', 'attribute'=>'code', 'value' => function($model){
                return $model->code;
            }],





            [
                'class' => 'yii\grid\DataColumn',
                'header' => '上级单位',
                'value'  => function ($data)
                {
                    return $data['header']['name']?$data['header']['name']:AgencySearch::TOP_AGENCY;
                },
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'header' => '状态',
                'value'  => function ($data)
                {
                    $status = '';
                    switch ($data->status){
                        case \backend\models\Agency::NORMAL_STATUS:
                            $status = '正常';
                            break;
                        case \backend\models\Agency::INVALID_STATUS:
                            $status = '已作废';
                    }
                    return $status;
                },
            ],

            [
                'class' => 'yii\grid\DataColumn',
                'header' => '创建人／时间',
                'format' => 'html',
                'value'  => function ($data)
                {
                    return $data['admin']['nickname'] . '<br>' . $data->create_at;
                },
            ],

            [
                'class' => 'yii\grid\DataColumn',
                'header' => '最后修改人／时间',
                'format' => 'html',
                'value'  => function ($data)
                {
                    return $data['updater']['nickname'] . '<br>' . $data->update_at;
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function($url){
                        return Html::a('编辑',$url);
                    },
                    'delete' => function($url, $model){
                        $btn_link = '';
                        switch ($model->status){
                            case \backend\models\Agency::NORMAL_STATUS:
                                $btn_link = Html::a('作废',
                                    $url . '&status=1',
                                    [
//                                        'class' => 'btn btn-xs',
                                        'style' => 'color:red',
                                        'data-method' => 'post',
                                        'data' => ['confirm' => '你确定要作废吗?']
                                    ]);
                                break;
                            case \backend\models\Agency::INVALID_STATUS:
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
        <?= Html::a('新增单位', ['create'], ['class'=>'btn btn-primary btn-sm']) ?>
    </p>
</div>