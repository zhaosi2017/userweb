<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\LoginLogsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '登录日志';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manager-login-logs-index">

    <?php  echo $this->render('_login_log_search', ['model' => $searchModel]); ?>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'layout' => "{items}\n  <div><ul class='pagination'><li style='display:inline;'><span>共".$dataProvider->getTotalCount(). "条数据 <span></li></ul>{pager}  </div>",

        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'header' => '序号'],

            ['header' => '管理员账号', 'value' => function($model){
                return $model['admin']['account'];
            }],

            ['header' => '管理员昵称', 'value' => function($model){
                return $model['admin']['nickname'];
            }],

            ['header' => '登录状态', 'value' => function($model){
                return $model->status ? $model['statuses'][$model->status] : '';
            }],

            ['header' => '登录IP', 'value' => function($model){
                return $model->login_ip;
            }],
            ['header' => '登录地址', 'value' => function($model){
                return $model->address;
            }],

            'login_time',

        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>
