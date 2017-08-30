<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = '权限设置: ' . $model->account;
$this->params['breadcrumbs'][] = ['label' => '管理员列表', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '权限设置';
?>
<div class="manager-update">

    <?php $form = ActiveForm::begin(); ?>
    <table class="table table-bordered">
        <?= Html::checkboxList('newPri', $userRolesArray, $allRolesArray)?>
        <div class="form-group">
            <div class="text-left">
                <?= Html::submitButton($model->isNewRecord ? '设置' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </table>

    <?php ActiveForm::end(); ?>
</div>
