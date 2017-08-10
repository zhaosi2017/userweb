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

    <?php $form = ActiveForm::begin([
//        'options'=>['class'=>'form-horizontal m-t', 'enctype'=>'multipart/form-data'],
//        'fieldConfig' => [
//            'template' => "{label}\n<div class=\"col-sm-10\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>",
//            'labelOptions' => ['class' => 'col-sm-2 '],
//        ],
        'options'=>['class'=>'form-horizontal m-t'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-sm-4\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>",
            'labelOptions' => ['class' => 'col-sm-2 '],
        ],
    ]); ?>

    <?php if (!$model->isNewRecord){?>
    <?= $form->field($model, 'code')->textInput(['maxlength' => true,'readonly'=>'readonly']) ?>
    <?php }?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'aide_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'group_id')->hiddenInput()->label(false) ?>
    <div class="form-group field-task-product-btn">
        <label class="col-sm-2 " for="task-product-btn">上级单位</label>
        <div class="col-sm-4"><input type="button" id="task-product-btn" class="btn btn-block btn-outline btn-primary" value="选择">
            <span class="help-block m-b-none" id="product-name-number">单位名称(编号)</span>
        </div>
    </div>

    <?= $form->field($model, 'level')->dropDownList(\backend\models\Customer::$levelArr) ?>

    <?= $form->field($model, 'type')->dropDownList(\backend\models\Customer::$customerType) ?>

    <?= $form->field($model, 'company')->textInput(['maxlength' => true]) ?>

    <div class="col-sm-12"></div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('返回', ['index'], ['class' => 'btn btn-primary']) ?>

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