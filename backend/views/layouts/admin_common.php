<?php
use yii\helpers\Html;
use yii\bootstrap\Alert;

if( Yii::$app->getSession()->hasFlash('success') ) {
    echo Alert::widget([
        'options' => [
            // 'class' => 'alert-success no-margins', //这里是提示框的class
            // 'style' => 'z-index:9999;position:fixed;width:100%',
            'style' => '
                position: relative;
                color: #3c763d;
                background-color: #dff0d8;
                border-color: #d6e9c6;
                z-index: 99999999;
                ',
        ],
        'body' => Yii::$app->getSession()->getFlash('success'), //消息体
    ]);
    $res = Yii::$app->getSession()->getFlash('pageMessTime');
    if(!$res){
        $res = 3000;
    }else{
        $res = $res*1000;
    }
    echo '<script type="text/javascript"  ?>
            function closeSuccess(){
                $(".alert").hide();
            }    
            t = setTimeout(closeSuccess,'.$res.');
    </script>';

}
if( Yii::$app->getSession()->hasFlash('error') ) {
    echo Alert::widget([
        'options' => [
            // 'class' => 'alert-warning no-margins',
            'style' => '
                position: relative;
                color: #3c763d;
                background-color: #dff0d8;
                border-color: #d6e9c6;
                z-index: 99999999;
                ',
        ],
        'body' => Yii::$app->getSession()->getFlash('error'),
    ]);
    $res = Yii::$app->getSession()->getFlash('pageMessTime');
    if(!$res){
        $res = 3000;
    }else{
        $res = $res*1000;
    }

    echo '<script type="text/javascript"  ?>
            function closeSuccess(){
                $(".alert").hide();
            }    
            t = setTimeout(closeSuccess,'.$res.');
    </script>';
}
if( Yii::$app->getSession()->hasFlash('info') ) {
    echo Alert::widget([
        'options' => [
            // 'class' => 'alert-info no-margins',
            'style' => '
                position: relative;
                color: #3c763d;
                background-color: #dff0d8;
                border-color: #d6e9c6;
                z-index: 99999999;
                ',
        ],
        'body' => Yii::$app->getSession()->getFlash('info'),
    ]);
    $res = Yii::$app->getSession()->getFlash('pageMessTime');
    if(!$res){
        $res = 3000;
    }else{
        $res = $res*1000;
    }
    echo '<script type="text/javascript"  ?>
            function closeSuccess(){
                $(".alert").hide();
            }    
            t = setTimeout(closeSuccess,'.$res.');
    </script>';
}
?>