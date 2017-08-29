<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin([
        'options'=>['class'=>'form-horizontal m-t'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-sm-3\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>",
            'labelOptions' => ['class' => 'col-sm-1 ','style'=>['width'=>'100px']],
        ],
    ]); ?>

    <?= $form->field($model, 'type')->hiddenInput()->label(false)?>
    <?php
    if ($model->isNewRecord) {
        echo $form->field($model, 'name')->textInput();
    }
    ?>
    <?= $form->field($model, 'description')->textInput() ?>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-1">
            <?= Html::submitButton($model->isNewRecord ? '创建角色' : '保存修改', ['class'=>'btn btn-primary']) ?>

        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
