<?php

use yii\helpers\Html;
use \yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Channel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="channel-form">

    <?php $form = ActiveForm::begin([
        'options'=>['class'=>'form-horizontal m-t'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-sm-3\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>",
            'labelOptions' => ['class' => 'col-sm-1 ','style'=>['width'=>'100px']],
        ],
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <div class="channel-image">
        <?= $form->field($model, 'img_url')->hiddenInput()->label(false);?>
        <?php
            if (!$model->isNewRecord) {
        ?>
            <div class="form-group">
                <label class="col-sm-1 " style="width: 100px;" for="channel-name"></label>
                <div class="col-sm-3" id="old-images" baseUrl="<?=\Yii::$app->params['fileBaseDomain']?>" >
        <?php
                    echo Html::img(\Yii::$app->params['fileBaseDomain'].$model->img_url, ["width"=> 100, "height"=>100, "tip" => 'img_url']);
        ?>
                </div>
            </div>
        <?php
            }
        ?>
    </div>
    <?= $form->field($upload , 'imageFile')->widget(\kartik\file\FileInput::className(),[
        'options'   => [
            'accept'  => 'images/*',
            'module'  => 'Channel',
            // 'multiple' => true,
        ],
        'pluginOptions' => [
            'uploadUrl' => Url::to(['upload']),
            'uploadExtraData' => [
                'model' => 'channel',
            ]
        ],
        //fileupload为上传成功后触发的，三个参数，主要是第二个，有formData，jqXHR以及response参数，上传成功后返回的ajax数据可以在response获取
        'pluginEvents'  => [
            'fileuploaded'  => "function (object, data){
                $('.channel-image').find('input').val(data.response.imageUrl);
                var tmp = $('#old-images').attr('baseUrl');
                if (typeof(tmp) != 'undefined') {
                    $('#old-images').find('img').attr('src', tmp+data.response.imageUrl);
                }
		    }",
            // 错误的冗余机制.
            'error' => "function (){
			    alert('图片上传失败');
            }"
        ]
    ])->label('渠道图片');?>
    <div class="channel-image-gray">
        <?= $form->field($model, 'gray_img_url')->hiddenInput()->label(false);?>
        <?php
        if (!$model->isNewRecord) {
            ?>
            <div class="form-group">
                <label class="col-sm-1 " style="width: 100px;" for="channel-name"></label>
                <div class="col-sm-3" id="old-images-gray" baseUrl="<?=\Yii::$app->params['fileBaseDomain']?>" >
                    <?php
                    echo Html::img(\Yii::$app->params['fileBaseDomain'].$model->gray_img_url, ["width"=> 100, "height"=>100, 'tip' => 'gray_img_url']);
                    ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <?= $form->field($upload , 'imageGrayFile')->widget(\kartik\file\FileInput::className(),[
        'options'   => [
            'accept'  => 'images/*',
            'module'  => 'Channel',
            // 'multiple' => true,
        ],
        'pluginOptions' => [
            'uploadUrl' => Url::to(['upload-gray']),
            'uploadExtraData' => [
                'model' => 'channel',
            ]
        ],
        //fileupload为上传成功后触发的，三个参数，主要是第二个，有formData，jqXHR以及response参数，上传成功后返回的ajax数据可以在response获取
        'pluginEvents'  => [
            'fileuploaded'  => "function (object, data){
                $('.channel-image-gray').find('input').val(data.response.imageUrl);
                var tmp = $('#old-images-gray').attr('baseUrl');
                if (typeof(tmp) != 'undefined') {
                    $('#old-images').find('img').attr('src', tmp+data.response.imageUrl);
                }
		    }",
            // 错误的冗余机制.
            'error' => "function (){
			    alert('图片上传失败');
            }"
        ]
    ])->label('灰色图片');?>


    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-1">
            <?= Html::submitButton($model->isNewRecord ? '新增渠道' : '修改渠道', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
