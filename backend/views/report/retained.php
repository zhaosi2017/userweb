<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Reports\CountryAddress;

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
                        <th class="text-center">日期</th>
                        <th class="text-center">国别</th>
                        <th class="text-center">次留</th>
                        <th class="text-center">三日留存</th>
                        <th class="text-center">七日留存</th>
                        <th class="text-center">14日流存</th>
                        <th class="text-center">30天流存</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $y = 0;?>
                    <?php foreach ($searchModel->data as $i => $v){?>
                        <?php $y = $y + 1;?>
                        <?php if(!empty($v)){ ?>
                            <?php foreach ($v as $k=> $m){?>
                                <?php if(isset(CountryAddress::$codeAddress[$k])){?>
                                    <tr style="<?php if($y%2 == 0){ echo 'color:red;';}?>">
                                        <td class="text-center"> <?php echo $i;?> </td>
                                        <td class="text-center"><?php echo CountryAddress::$codeAddress[$k];?></td>
                                        <td class="text-center"><?php echo $m['second'];?></td>
                                        <td class="text-center"><?php echo $m['third'];?></td>
                                        <td class="text-center"><?php echo $m['seven'];?></td>
                                        <td class="text-center"><?php echo $m['fourteen'];?></td>
                                        <td class="text-center"><?php echo $m['thirty'];?></td>
                                    </tr>
                                <?php }?>
                            <?php }?>
                        <?php }else{ ?>
                            <tr style="<?php if($y%2 == 0){ echo 'color:red;';}?>">
                                <td class="text-center"> <?php echo $i;?> </td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
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
