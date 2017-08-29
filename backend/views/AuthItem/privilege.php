<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '权限列表';
$this->params['breadcrumbs'][] = $this->title;
$actionId = Yii::$app->requestedAction->id;

?>

<div class="auth-item-index">

    <?php  echo $this->render('_privilegesearch', ['model' => $searchModel]); ?>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'layout' => "{items}\n  <div><ul class='pagination'><li style='display:inline;'><span>共".$dataProvider->getTotalCount(). "条数据 <span></li></ul>{pager}  </div>",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn','header' => '序号'],
            [
                'class' => 'yii\grid\DataColumn',
                'header' => '权限名称',
                'attribute'=>'name',
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'header' => '描述',
                'attribute'=>'description',
            ],
            // 'remark:ntext',
            [
                'class' => 'yii\grid\DataColumn', //由于是默认类型，可以省略
                'header' => '修改时间',
                'format' => 'raw',
                'value' => function ($data) {
                    return date('Y-m-d H:i:s',$data->updated_at);
                },
            ],
            [
                'class' => 'yii\grid\DataColumn', //由于是默认类型，可以省略
                'header' => '创建时间',
                'format' => 'raw',
                'value' => function ($data) {
                    return date('Y-m-d H:i:s',$data->created_at);
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                // 'template' => '{update} {auth} {delete}'
                'template' => '{update}',
                'buttons' => [
                    'update' => function($url, $model){
                        return Html::a('编辑',['/authitem/permission-create', 'id'=>$model->name]);
                    },
                    'auth' => function($url){
                        return Html::a('权限配置',$url);
                    },
                    'delete' => function($url){
                        if(Yii::$app->user->can('authitem/delete')){
                            return Html::a('删除',$url,[
                                'style' => 'color:red',
                                'data-method' => 'post',
                                'data' => ['confirm' => '你确定要删除吗?']
                            ]);
                        }else{
                            $url = 'index';
                            return Html::a('删除',$url,[
                                'style' => 'color:red',
                                'data-method' => 'get',
                                'data' => ['confirm' => '您没有该权限!']
                            ]);
                        }

                    },
                ],
            ],
        ],
    ]);?>
    <?php Pjax::end(); ?>
    <p class="text-right">
        <?= Html::a('新增权限', ['permission-create'], ['class' => 'btn btn-sm btn-primary']) ?>
    </p>
</div>
