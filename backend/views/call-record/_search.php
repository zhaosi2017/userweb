<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ChannelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="channel-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class'=>'form-inline'],
    ]); ?>

    <div class="row">

        <div class="col-lg-12">
            <div class="text-left no-padding">
                <?= $form->field($model,'start_date')->input('date',['prompt'=>'请选择'])->label('登录时间：') ?>
                至
                <?= $form->field($model,'end_date')->input('date',['prompt'=>'请选择'])->label(false) ?>

                <a class="btn btn-xs btn-danger" onclick="
                $('#callrecordsearch-start_date').val('');
                $('#callrecordsearch-end_date').val('');
            ">清除时间</a>
                <?= $form->field($model, 'search_type')->dropDownList([
                    1 => '主叫优码',
                    2 => '被叫优码',

                ],['prompt' => '全部'])->label(false) ?>


                <?= $form->field($model, 'search_keywords')->textInput()->label(false) ?>
                <?= $form->field($model, 'search_status')->dropDownList(
                        \frontend\models\CallRecord\CallRecord::$status_map,
                        ['prompt' => '全部'])->label('呼叫状态:') ?>
                <?= $form->field($model, 'search_call_type')->dropDownList(
                        \frontend\models\CallRecord\CallRecord::$type_map
                ,['prompt' => '全部'])->label('呼叫类型:') ?>
                <div class="form-group">
                    <?= Html::submitButton('搜索', ['class' => 'btn btn-primary m-t-n-xs','id'=>'search']) ?>
                    &nbsp;
                    <?= Html::a('重置', ['index'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
