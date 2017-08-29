<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AuthItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class'=>'form-inline'],
    ]); ?>

    <div class="row">
        <div class="col-lg-6">

        </div>
        <div class="col-lg-6">
            <div class="text-right no-padding">
                <?= $form->field($model, 'search_type')->dropDownList([
                    1 => '角色',
                    2 => '描述',
                ],['prompt' => '全部'])->label(false) ?>
                <?= $form->field($model, 'search_keywords')->textInput()->label(false) ?>
                <div class="form-group">
                    <?= Html::submitButton('搜索', ['class' => 'btn btn-primary m-t-n-xs','id'=>'search']) ?>
                    &nbsp;
                    <?= Html::resetButton('重置', ['class' => 'btn btn-primary m-t-n-xs']) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
