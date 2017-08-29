<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model backend\models\LoginForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\captcha\Captcha;

$this->title = '登录';
?>
    <div class="middle-box text-center loginscreen  animated fadeInDown">
        <div>
            <div>
                <h1 class="logo-name">&nbsp;</h1>
            </div>
            <h3>登录</h3>

            <?php $form = ActiveForm::begin([
                'id' => 'register-form',
                'options'=>['class'=>'m-t text-left'],
                'fieldConfig' => [
                    'template' => "\n<div> <div style=\"display:inline-block\">{label}</div><div style=\"width: 210px;display:inline-block;margin-left:10px;\">{input}</div>\n<span class=\"help-block m-b-none\" style=\"margin-left:77px;\">{error}</span></div>",
                    'labelOptions' => [],
                ],
            ]); ?>

            <?= $form->field($model, 'username')->textInput([
                'autofocus' => true,
                'placeholder'=>'邮箱账号',
            ])->label('管理员账号: ') ?>

            <?= $form->field($model, 'pwd')->passwordInput(['placeholder'=>'密码'])->label('管理员密码: ') ?>

            <?= $form->field($model, 'code')
                ->label(false)
                ->widget(Captcha::className(), [
                    'captchaAction'=>'/login/captcha',
                    'template' => '<div class="row" style="width:300px;"><div style="display:inline-block;"><label style="width:60px;padding-left:7px;" for="loginform-code">验证码:</label></div><div style="height:30px;line-height:33px;display: inline-block;width: 77px;margin-left: 22px;">{input}</div><div style="display: inline-block;margin-left:52px;">{image}</div></div>',
                    'options' => ['placeholder'=>'验证码']
                ]) ?>
            <?= Html::submitButton('登 录', ['class' => 'btn btn-primary block full-width m-b']) ?>

            <?php ActiveForm::end(); ?>
            <p class="text-muted text-center">
                <a><small>若忘记密码请直接联系管理员</small></a>
            </p>
        </div>
    </div>


<?php echo '<style type="text/css">
    #loginform-code{
        padding-left: 10px;
        width:100px;
    }
</style>'; ?>