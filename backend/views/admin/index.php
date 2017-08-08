<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\ManagerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '管理员';
$this->params['breadcrumbs'][] = $this->title;
$actionId = Yii::$app->requestedAction->id;
?>
<div class="manager-index">

    <p class="btn-group hidden-xs">
        <?= Html::a('管理员列表', ['index'], ['class' => $actionId=='trash' ? 'btn btn-outline btn-default' : 'btn btn-primary']) ?>
        <?= Html::a('垃圾筒', ['trash'], ['class' => $actionId=='index' ? 'btn btn-outline btn-default' : 'btn btn-primary']) ?>
    </p>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
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

            'account:ntext',
            'nickname:ntext',
            [
                'header' =>'角色',
                'value' => function($data){
                    return $data['role']['name'];
                },
            ],
            [
                'header' =>'角色备注',
                'value' => function($data){
                    return $data['role']['remark'];
                },
            ],
            [
                'header' =>'账号状态',
                'value' => function($data){
                    return $data['statuses'][$data->status];
                },
            ],
            [
                'header' =>'冻结／解冻备注',
                'value' => function($data){
                    return strip_tags($data['remark'])?strip_tags($data['remark']):'*';
                },
            ],
            [
                'header' =>'最后登陆IP',
                'value' => function($data){
                    return $data['login_ip'];
                },
            ],

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
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function($url){
                        return Html::a('编辑',$url);
                    },
                    'delete' => function($url){
                        if(Yii::$app->user->can('admin/delete')){

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
                                'data' => ['confirm' => '您没有该权限！']
                            ]);

                        }


                    },
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    <p class="text-right">
        <?= Html::a('创建管理员', ['create'], ['class' => 'btn btn-sm btn-primary']) ?>
    </p>
</div>
