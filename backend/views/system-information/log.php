<?php
use yii\bootstrap\Modal;
$this->title = '系统日志';
?>
<?php
foreach ($files as $file)
{
    echo $this->render('_file', ['model'=>$file]);
}

Modal::begin([
    'header' => '<h2>系统日志</h2>',
    'id'=>'modal',
    'size'=>Modal::SIZE_LARGE,
]);

Modal::end();