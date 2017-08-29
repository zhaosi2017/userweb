<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Role */

$this->title = '权限授权: '.$model->name ;
$this->params['breadcrumbs'][] = ['label' => '角色列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '权限授权' ;
?>
<div class="posts-grid">

    <div class="table-responsive">
        <form action="" method="post" id="w0">
            <input type="hidden" name="AuthItem[name]" value="<?= $model->name ?>">
            <input type="hidden" name="_csrf" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">

            <?php foreach ($moduleArr as $k => $module){ ?>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th colspan="2"><?= $module ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="text-center" width="10%">序号</td>
                        <td>权限</td>
                    </tr>
                    <?php foreach ($allPrivileges[$k] as $key=>$val){ ?>
                        <tr>
                            <td class="text-center"><?= ++$key; ?></td>
                            <td class="text-left">
                                <label>
                                    <input type="checkbox" <?= in_array($val['route'], $alreadyAuth) ? 'checked="checked"' : ' ' ?> name="Auth[]" value="<?= $val['route'] ?>" />
                                </label>
                                <?= $val['description'] ?>
                            </td>
                        </tr>
                    <?php } ?>

                </table>
            <?php } ?>

            <div class="form-group">
                <div class="text-right">
                    <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>

        </form>
    </div>

</div>
