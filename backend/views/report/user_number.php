<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Reports\CountryAddress;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户数报表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channel-index">

    <?php  echo $this->render('user_number_search', ['model' => $searchModel]); ?>
    <div id="p0" class="row">
   <div  id="w1" class="grid-view">
    <?php if(empty($searchModel->data)){?>
        no data
    <?php }else{?>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
            <th class="text-center">国别</th>
            <th class="text-center">前天</th>
            <th class="text-center">昨天</th>
            <th class="text-center">呼叫数</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($searchModel->data as $i => $v){?>
                <?php if(isset(CountryAddress::$codeAddress[$i])  ){?>
                <tr>
                    <td><?php echo CountryAddress::$codeAddress[$i];?></td>
                    <td><?php echo $v['before'];?></td>
                    <td><?php echo $v['yesterday'];?></td>
                    <td><?php echo $v['call_num'];?></td>
                </tr>
                    <?php }?>
            <?php }?>
            </tbody>
        </table>
    <?php }?>
   </div>
    </div>

</div>
