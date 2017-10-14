<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model  */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="manager-login-logs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['retained'],
        'method' => 'get',
        'options' => ['class'=>'form-inline'],
    ]); ?>

    <div class="row">
        <div class="col-lg-6">

            <?= $form->field($model,'start_date')->input('date',['prompt'=>'请选择'])->label('搜索日期：') ?>
            <a class="btn btn-xs btn-danger" onclick="
                $('#retainedreportsearch-start_date').val('');
            ">清除时间</a>


        </div>
        <div class="col-lg-6">

            <div class="form-group">
                <?= Html::submitButton('search', ['class' => 'hide','id'=>'search_hide']) ?>
                <button onclick = "return searchClick();" id="search" class = 'btn btn-primary m-t-n-xs'>搜索</button>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">


    function searchClick(){
        var start = $('#loginlogssearch-start_date').val();
        var end =  $('#loginlogssearch-end_date').val();
        if (start == "" && end != ""){
            alert('请同时选择开始时间和结束时间进行查询！');
            return false;
        }
        if(start != "" && end == ""){
            alert('请同时选择开始时间和结束时间进行查询！');
            return false;
        }
        return true;
    }

    function timeChange(){

        var start = $('#loginlogssearch-start_date').val();
        var end =  $('#loginlogssearch-end_date').val();
        if(start == '' || end ==''){
            alert('请同时选择开始时间和结束时间进行查询！');
            return false;
        }

    }

    function clearDate(){
        $('#loginlogssearch-start_date').val('');
        $('#loginlogssearch-end_date').val('');
    }
</script>
