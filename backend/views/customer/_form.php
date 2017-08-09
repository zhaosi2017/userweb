<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\redactor\widgets\Redactor;

/* @var $this yii\web\View */
/* @var $model backend\models\Customer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'aide_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'group_id')->textInput() ?>
    <div class="form-group field-task-product-btn">
        <label class="col-sm-2 control-label" for="task-product-btn">产品</label>
        <div class="col-sm-10"><input type="button" id="task-product-btn" class="btn btn-block btn-outline btn-primary" value="选择">
            <span class="help-block m-b-none" id="product-name-number">产品名称(编号)</span>
        </div>
    </div>

    <?= $form->field($model, 'level')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList(\backend\models\Customer::$customerType) ?>

    <?= $form->field($model, 'company')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php

$url = Url::to(["/customer/agency-search"]);

$this->registerJs('

        $("#task-product-btn").click(function(){
            
                var url = \''.$url.'\';
                layer.open({
                    type: 2,
                    title: \'产品选择\',
                    shade: 0.5,
                    area: [\'80%\', \'90%\'],
                    fixed: false,
                    shadeClose: true,
                    content: url,
                });
           
        });
    
');
?>