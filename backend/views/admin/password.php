<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\PasswordForm */
/* @var $form yii\widgets\ActiveForm */

$this->title = '修改密码';
$this->params['breadcrumbs'][] = ['label'=>'首页','url'=>['/admin/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'options'=>['class'=>'form-horizontal m-t'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-sm-3\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>",
            'labelOptions' => ['class' => 'col-sm-2 ','style'=>['width'=>'10%']],
        ],
    ]) ?>

    <?= $form->field($model, 'password',[
        'template' => "{label}\n<div class=\"col-sm-3\">{input}</div> <span class=\"col-sm-3\">*请输入管理员原密码</span>\n<br/><div><span class=\"help-block m-b-none \" style=\" margin-left:11.4%;   margin-top: 16px;\">{error}</span></div>",
    ])->passwordInput()->label('原密码：') ?>

    <?= $form->field($model, 'newPassword',[
        'template' => "{label}\n<div class=\"col-sm-3\">{input}</div> <span class=\"col-sm-5\">*请输入账户新密码 ,密码至少包含8个字符，至少包括以下2种字符： 大写字母，小写字母，数字，符号</span>\n<br/><div><span class=\"help-block m-b-none \" style=\" margin-left:11.4%;   margin-top: 18px;\">{error}</span></div>",
    ])->passwordInput()->label('新密码：') ?>

    <?= $form->field($model, 'rePassword',[
        'template' => "{label}\n<div class=\"col-sm-3\">{input}</div> <span class=\"col-sm-5\">请再次输入账户新密码 ,密码至少包含8个字符，至少包括以下2种 字符：大写字母，小写字母，数字，符号</span>\n<br/><div><span class=\"help-block m-b-none \" style=\" margin-left:11.4%;   margin-top: 18px;\">{error}</span></div>",
    ])->passwordInput()->label('重复新密码：') ?>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <?= Html::submitButton('修改', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
