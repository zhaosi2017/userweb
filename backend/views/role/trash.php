<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\RoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '角色';
$this->params['breadcrumbs'][] = $this->title;
$actionId = Yii::$app->requestedAction->id;
?>
<div class="role-index">
    <p class="btn-group hidden-xs">
        <?= Html::a('角色列表', ['index'], ['class' => $actionId=='trash' ? 'btn btn-outline btn-default' : 'btn btn-primary']) ?>
        <?= Html::a('垃圾筒', ['trash'], ['class' => $actionId=='index' ? 'btn btn-outline btn-default' : 'btn btn-primary']) ?>
    </p>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'layout' => "{items}\n  <div><ul class='pagination'><li style='display:inline;'><span>共".$dataProvider->getTotalCount(). "条数据 <span></li></ul>{pager}  </div>",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn','header' => '序号'],
            'name:ntext',
            'remark:ntext',
            [
                'class' => 'yii\grid\DataColumn', //由于是默认类型，可以省略
                'header' => '最后修改人／时间',
                'format' => 'raw',
                'value' => function ($data) {
                    $account = $data['updater']['account'] ? $data['updater']['account'] : '系统';
                    return $account .'<br>'. date('Y-m-d H:i:s',$data->update_at);
                },
            ],
            [
                'class' => 'yii\grid\DataColumn', //由于是默认类型，可以省略
                'header' => '创建人／时间',
                'format' => 'raw',
                'value' => function ($data) {
                    $account = $data['creator']['account'] ? $data['creator']['account'] : '系统';
                    return $account.'<br>'.date('Y-m-d H:i:s',$data->create_at);
                },
            ],



            [

                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{recover}',
                'buttons' => [
                    'recover' => function($url){
                        if(Yii::$app->user->can('admin/role/recover')){
                            return Html::a('恢复',$url,[
                                'data-method' => 'post',
                                'data' => ['confirm' => '你确定要恢复吗?']
                            ]);
                        }else{
                            $url = 'trash';
                            return Html::a('恢复',$url,[
                                'data-method' => 'get',
                                'data' => ['confirm' => '你没有该权限']
                            ]);
                        }

                    },
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    <p class="text-right">
        <?= Html::a('新增角色', ['create'], ['class' => 'btn btn-sm btn-primary']) ?>
    </p>
</div>
