<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\ManagerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="manager-search">

    <?php $form = ActiveForm::begin([
        'action' =>  Yii::$app->requestedAction->id == 'index' ?['index']:['trash'],
        'method' => 'get',
        'options' => ['class'=>'form-inline'],
    ]); ?>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model,'role_id')->dropDownList($model['roles'],['prompt'=>'全部','onchange'=>'
                $("#search_hide").click();
            '])->label('角色：') ?>

            <?= $form->field($model,'status')->dropDownList( Yii::$app->requestedAction->id == 'index' ?$model['statuses']:['1'=>'作废'],['prompt'=>'全部','onchange'=>'
                $("#search_hide").click();
            '])->label('&nbsp;&nbsp;账号状态：') ?>
        </div>
        <div class="col-lg-6">
            <div class="text-right no-padding">
                <?= $form->field($model, 'search_type')->dropDownList([
                    1 => '账号',
                    2 => '昵称',
                    3 => '最后登录IP',
                ],['prompt' => '全部'])->label(false) ?>
                <?= $form->field($model, 'search_keywords')->textInput()->label(false) ?>
                <div class="form-group">
                    <?= Html::submitButton('search', ['class' => 'hide','id'=>'search_hide']) ?>
                    <?= Html::submitButton('搜索', ['class' => 'btn btn-primary m-t-n-xs','id'=>'search','onclick'=>'
                        $("#managersearch-role_id").val("");
                        $("#managersearch-status").val("");
                    ']) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
