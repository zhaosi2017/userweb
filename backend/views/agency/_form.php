<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Agency*/
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-form">

    <?php $form = ActiveForm::begin([
        'options'=>['class'=>'form-horizontal m-t'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-sm-9\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label'],
        ],
    ]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 20]) ?>

    <?php

    $supList = [0=>'无'];
    $sup_level = [0 => 0];

//    $condition = $model->isNewRecord ? ['status'=>0] : ['and','status=0',['<', 'level', $model->level],['not in', 'id', $model->id]];
    $company_list = backend\models\Agency::find()->select(['name','id'])
//        ->where($condition)
        ->orderBy(['id' => SORT_DESC])->all();

    if(!empty($company_list)){
        foreach ($company_list as $item){
            $sup_level[$item->id] = $item->level;
            $supList[$item->id] = $item->name;
        }
    }

    $sup_level_json = json_encode($sup_level);

    ?>

<!--    --><?//= $form->field($model, 'sup_id')->dropDownList($supList,
//        ['onchange' => '
//            var sup_id = $(this).val();
//            var sup_level_json = '.$sup_level_json.';
//            $("#sup_level").val(sup_level_json[sup_id] + "级");
//        '])
//    ?>

<!--    <div class="form-group">-->
<!--        <label class="col-sm-3 control-label" for="sup_level">层级</label>-->
<!--        <div class="col-sm-9">-->
<!--            <input class="form-control" id="sup_level" type="text" readonly="readonly" value="--><?//= $model->level ? $model->level-1 : 0 ?><!--级">-->
<!--        </div>-->
<!--    </div>-->

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
