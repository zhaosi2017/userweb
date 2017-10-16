<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Reports\CountryAddress;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '日活跃报表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channel-index">

    <?php  echo $this->render('active_day_search', ['model' => $searchModel]); ?>
    <div id="p0" class="row">
        <div  id="w1" class="grid-view">
            <?php if(empty($searchModel->data)){?>
                no data
            <?php }else{?>
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th class="text-center">日期</th>
                    <th class="text-center">国别</th>
                    <th class="text-center">活跃用户数</th>
                    <th class="text-center">呼叫用户数</th>
                    <th class="text-center">呼叫数</th>
                </tr>
                </thead>
                <tbody>
                <?php $y = 0;?>
                <?php foreach ($searchModel->data as $i => $v){?>
                    <?php $y = $y + 1;?>
                    <?php if(!empty($v)){ ?>
                        <?php foreach ($v as $k=> $m){?>
                            <?php if(isset(CountryAddress::$codeAddress[$k])){?>
                                <tr style="<?php if($y%2 == 0){ /*echo 'border-top: 2px solid green;';*/ }?>">
                                    <td class="text-center"> <?php echo $i;?> </td>
                                    <td class="text-center"><?php echo CountryAddress::$codeAddress[$k];?></td>
                                    <td class="text-center"><?php echo $m['active_num'];?></td>
                                    <td class="text-center"><?php echo $m['call_user_num'];?></td>
                                    <td class="text-center"><?php echo $m['call_num'];?></td>
                                </tr>
                            <?php }?>
                        <?php }?>
                    <?php }else{ ?>
                        <tr style="<?php if($y%2 == 0){ /*echo 'border-top: 2px solid green;';*/ }?>">
                            <td class="text-center"> <?php echo $i;?> </td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        </tr>
                    <?php }?>
                <?php }?>
                <?php }?>
                </tbody>
            </table>

        </div>
    </div>

</div>
