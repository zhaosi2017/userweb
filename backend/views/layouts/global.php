<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use backend\assets\GlobalAsset;

GlobalAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">

    <title><?= $this->title ?></title>

    <meta name="keywords" content="客服管理系统">
    <meta name="description" content="客服管理系统">

    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->

    <!--    <link rel="shortcut icon" href="favicon.ico">-->
    <?= Html::csrfMetaTags() ?>

    <?php $this->head() ?>
</head>

<body class="fixed-sidebar full-height-layout gray-bg pace-done" style="">
<?php $this->beginBody() ?>
<?= isset($content) ? $content : ''  ?>
</body>

<?php $this->endBody() ?>
</html>
<?php $this->endPage() ?>
