<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;

/* @var $this yii\web\View */
/* @var $model backend\models\Admin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="manager-form">

    <?php $form = ActiveForm::begin([
        'options'=>['class'=>'form-horizontal m-t'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-sm-4\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>",
            'labelOptions' => [],
        ],
    ]); ?>


    <?php if(!$model->isNewRecord){ ?>
        <?= $form->field($model, 'account',
            [
                'template' => "<div><div style=\"display:inline-block;width:70px;\">{label}</div>\n<div  style=\"display:inline-block;\">{input}</div><div style=\"display:inline-block;\"><span style=\"margin-left:10px;\"></span></div>\n<span class=\"help-block m-b-none\" style=\"margin-left:70px;\" ></span></div>",
            ])->textInput(['placeholder'=>'请输入账号','readonly'=>true]) ?>
    <?php }else{?>

        <?= $form->field($model, 'account',
            [
                'template' => "<div><div style=\"display:inline-block;width:70px;\">{label}</div>\n<div  style=\"display:inline-block;\">{input}</div><div style=\"display:inline-block;\"><span style=\"margin-left:10px;\">*请输入管理员账号，账号至少包含8个字符，至少包括一下2种字符：\n
大写字母、小写字母、数字、符号</span></div>\n<span class=\"help-block m-b-none\" style=\"margin-left:70px;\" >{error}</span></div>",
            ])->textInput(['placeholder'=>'请输入账号',]) ?>
    <?php }?>

    <?= $form->field($model, 'password',[
        'template' => "<div><div style=\"display:inline-block;width:70px;\">{label}</div>\n<div  style=\"display:inline-block;\">{input}</div><div style=\"display:inline-block;\"><span style=\"margin-left:10px;\">
*请输入管理员密码 ,密码至少包含8个字符，至少包括以下2种字符：
 大写字母、小写字母、数字、符号
</span></div>\n<span class=\"help-block m-b-none\" style=\"margin-left:70px;\">{error}</span></div>",
    ])->passwordInput(['placeholder'=>'请输入账号']) ?>

    <?= $form->field($model, 'nickname',[

        'template' => "<div><div style=\"display:inline-block;width:70px;\">{label}</div>\n<div  style=\"display:inline-block;\">{input}</div><div style=\"display:inline-block;\"><span style=\"margin-left:10px;\">
*请输入管理员你昵称 ,昵称至少输入2～6个汉字
</span></div>\n<span class=\"help-block m-b-none\" style=\"margin-left:70px;\">{error}</span></div>",
    ])->textInput(['placeholder'=>'请输入管理员昵称']) ?>

    <?= $form->field($model, 'role_id',[
        'template' => "<div><div style=\"display:inline-block;width:70px;\">{label}</div>\n<div  style=\"display:inline-block;\">{input}</div><div style=\"display:inline-block;\"><span style=\"margin-left:10px;\">

</span></div>\n<span class=\"help-block m-b-none\" style=\"margin-left:70px;\">{error}</span></div>",
    ])->dropDownList($model['roles'],['prompt'=>'请选择']) ?>

    <?php if(!$model->isNewRecord){ ?>
        <?= $form->field($model, 'status',[
            'template' => "<div><div style=\"display:inline-block;width:70px;\">{label}</div>\n<div  style=\"display:inline-block;\">{input}</div><div style=\"display:inline-block;\"><span style=\"margin-left:10px;\">

</span></div>\n<span class=\"help-block m-b-none\" style=\"margin-left:70px;\">{error}</span></div>",
        ])->radioList([0 => '正常', 2 => '冻结']) ?>

        <?= $form->field($model, 'remark',[
            'template' => "<div><div style=\"display:inline-block;width:70px;\">{label}</div>\n<div  style=\"display:inline-block;\">{input}</div><div style=\"display:inline-block;\"><span style=\"margin-left:10px;\">

</span></div>\n<span class=\"help-block m-b-none\" style=\"margin-left:70px;\">{error}</span></div>",
        ])->widget(Redactor::className(),[
            'clientOptions' => [
                'lang' => 'zh_cn',
                'imageUpload' => false,
                'fileUpload' => false,
                'plugins' => [
                    'clips',
                    'fontcolor'
                ],
                'placeholder'=> '请填写原因',
                'maxlength'=>500,

            ],
            'options'=>[
                'value'=>'',
            ],
        ])->label('原因') ?>

        <?= $form->field($model, 'login_ip',[
            'template' => "<div><div style=\"display:inline-block;width:70px;\">{label}</div>\n<div  style=\"display:inline-block;\">{input}</div><div style=\"display:inline-block;\"><span style=\"margin-left:10px;\">

</span></div>\n<span class=\"help-block m-b-none\" style=\"margin-left:70px;\">{error}</span></div>",
        ])->textInput(['value'=>Yii::$app->request->userIP,'readonly'=>true]) ?>

    <?php } ?>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-1">
            <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class'=>'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
