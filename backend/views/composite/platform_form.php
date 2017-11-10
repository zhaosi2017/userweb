<?php

use yii\helpers\Html;
use \yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\models\Versions\Version;

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

    <?= $form->field($model, 'platform')->dropDownList([

        Version::PLATFORM_ANDROID => Version::PLATFORM_ANDROID,
        Version::PLATFORM_IOS => Version::PLATFORM_IOS,
    ])->label('类型：') ?>

    <?= $form->field($model, 'version')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'info')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
    <label   style="width: 100px;display: none;" for="channel-name"></label>
    <label style="display: none;"  id="old-images" baseUrl="<?=\Yii::$app->params['fileBaseDomain']?>" ></label>
    <?= $form->field($upload , 'url')->widget(\kartik\file\FileInput::className(),[
        'options'   => [
            'accept'  => 'images/*',
            'module'  => 'Channel',
            // 'multiple' => true,
        ],
        'pluginOptions' => [
            'uploadUrl' => Url::to(['platform-upload']),
            'uploadExtraData' => [
                'model' => 'version',
            ]
        ],
        //fileupload为上传成功后触发的，三个参数，主要是第二个，有formData，jqXHR以及response参数，上传成功后返回的ajax数据可以在response获取
        'pluginEvents'  => [
            'fileuploaded'  => "function (object, data){
             console.log(data.response.imageUrl);
                $('.channel-image').find('input').val(data.response.imageUrl);
               
                var tmp = $('#old-images').attr('baseUrl');
                if (typeof(tmp) != 'undefined') {
                    $('#version-url').val( tmp+data.response.imageUrl);
                }
		    }",
            // 错误的冗余机制.
            'error' => "function (){
			    alert('图片上传失败');
            }"
        ]
    ])->label('平台安装包');?>



    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-1">
            <?= Html::submitButton($model->isNewRecord ? '新增平台版本' : '修改平台版本', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>


<?php
$this->registerJs(
    '$("#versionform-platform").change(function () {
        console.log($("#versionform-platform").val());
        if($("#versionform-platform").val() =="android")
        {
               $(".field-platformuploadform-url").show();
        }else{
            $(".field-platformuploadform-url").hide();
        }
    })'
);
?>

   
