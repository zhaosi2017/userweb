<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '留存报表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channel-index">

    <?php  echo $this->render('retained_search', ['model' => $searchModel]); ?>


    <div id="p0" class="row">
        <div  id="w1" class="grid-view">
            <?php if(empty($searchModel->data)){?>
                no data
            <?php }else{?>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center">国别</th>
                        <th class="text-center">前日24时累计用户数</th>
                        <th class="text-center">昨天新增上涨数</th>
                        <th class="text-center">前日呼叫数</th>
                        <th class="text-center">前日24时累计用户数</th>
                        <th class="text-center">昨天新增上涨数</th>
                        <th class="text-center">前日呼叫数</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($searchModel->data as $i => $v){?>
                        <?php if(isset(CountryAddress::$codeAddress[$i])  ){?>
                            <tr>
                                <td class="text-center"><?php echo CountryAddress::$codeAddress[$i];?></td>
                                <td class="text-center"><?php echo $v['today'];?></td>
                                <td class="text-center"><?php echo $v['second'];?></td>
                                <td class="text-center"><?php echo $v['third'];?></td>
                                <td class="text-center"><?php echo $v['seven'];?></td>
                                <td class="text-center"><?php echo $v['fourteen'];?></td>
                                <td class="text-center"><?php echo $v['thirty'];?></td>
                            </tr>
                        <?php }?>
                    <?php }?>
                    </tbody>
                </table>
            <?php }?>
        </div>
    </div>
</div>
