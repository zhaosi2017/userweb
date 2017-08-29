<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;

?>
<?php $this->beginContent('@app/views/layouts/public_admin.php'); ?>
<?= $this->render('admin_common') ?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>
                        <?= Html::encode($this->title) ?>
                    </h5>
                </div>

                <div class="ibox-content">
                    <?= isset($content) ? $content : '' ?>
                </div>

            </div>
        </div>
    </div>
</div>

<?php $this->endContent(); ?>
