<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '渠道列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channel-index">

    <?php  echo $this->render('user_number_search', ['model' => $searchModel]); ?>
    <?php if(empty($searchModel->data)){?>
        no data
    <?php }else{?>
        <tabel>
            <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            </tr>
            <?php foreach ($searchModel->data as $i => $v){?>
                <tr>
                 <td><?php echo $i;?></td>
                <td><?php echo $v['before']?></td>
                <td><?php echo $v['yesterday']?></td>
                <td><?php echo $v['call_num']?></td>
                </tr>
            <?php }?>
        </tabel>
    <?php }?>

</div>
