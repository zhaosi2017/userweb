<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use  frontend\models\Versions\Version;

/* @var $this yii\web\View */
/* @var $model backend\models\ChannelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="channel-search">

    <?php $form = ActiveForm::begin([
        'action' => ['platform-index'],
        'method' => 'get',
        'options' => ['class'=>'form-inline'],
    ]); ?>

    <div class="row">

        <div class="col-lg-6">
            <div class="text-left no-padding">

                <?= $form->field($model, 'search_type')->dropDownList([
                1 => Version::PLATFORM_IOS,
                2 => Version::PLATFORM_ANDROID,
                ],['prompt' => '全部'])->label('类型：') ?>

                <?= $form->field($model, 'search_keywords')->textInput()->label('版本号：') ?>
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
