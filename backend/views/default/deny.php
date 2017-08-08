<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

//use yii\helpers\Html;

$this->title = '拒绝访问';
?>
<div class="site-error">

    <!--    <h1>--><?php //echo Html::encode($this->title) ?><!--</h1>-->

    <div class="alert alert-danger">
        <p style="font-size: x-large;">您无此权限,请联系超级管理员！</p>

    </div>

    <!-- <p>
         The above error occurred while the Web server was processing your request.
     </p>
     <p>
         Please contact us if you think this is a server error. Thank you.111111
     </p>-->

</div>
