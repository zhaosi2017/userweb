<?php


/* @var $this yii\web\View */
/* @var $model backend\models\Admin */

$this->title = '控制面板';
$this->params['breadcrumbs'][] = ['label' => '控制面板'];
$days = 7;
// \backend\assets\DashboardAsset::register($this);
dmstr\web\AdminLteAsset::register($this);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="col-lg-2 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3><?=10;?><sup style="font-size: 20px">笔</sup></h3>
                    <p>今日银行卡汇款</p>
                </div>
                <div class="icon">
                    <i class="ion ion-card"></i>
                </div>
                <a href="<?= \yii\helpers\Url::to(['finance/recharge/card']) ?>" class="small-box-footer">更多 <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-2 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3><?=$days;?><sup style="font-size: 20px">笔</sup></h3>
                    <p>今日支付宝入款</p>
                </div>
                <div class="icon">
                    <i class="ion ion-cash"></i>
                </div>
                <a href="<?= \yii\helpers\Url::to(['finance/recharge/alipay']) ?>" class="small-box-footer">更多 <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-2 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3><?=10;?><sup style="font-size: 20px">笔</sup></h3>
                    <p>今日银行卡汇款</p>
                </div>
                <div class="icon">
                    <i class="ion ion-card"></i>
                </div>
                <a href="<?= \yii\helpers\Url::to(['finance/recharge/card']) ?>" class="small-box-footer">更多 <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-2 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3><?=$days;?><sup style="font-size: 20px">笔</sup></h3>
                    <p>今日支付宝入款</p>
                </div>
                <div class="icon">
                    <i class="ion ion-cash"></i>
                </div>
                <a href="<?= \yii\helpers\Url::to(['finance/recharge/alipay']) ?>" class="small-box-footer">更多 <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-2 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3><?=10;?><sup style="font-size: 20px">笔</sup></h3>
                    <p>今日银行卡汇款</p>
                </div>
                <div class="icon">
                    <i class="ion ion-card"></i>
                </div>
                <a href="<?= \yii\helpers\Url::to(['finance/recharge/card']) ?>" class="small-box-footer">更多 <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-2 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3><?=$days;?><sup style="font-size: 20px">笔</sup></h3>
                    <p>今日支付宝入款</p>
                </div>
                <div class="icon">
                    <i class="ion ion-cash"></i>
                </div>
                <a href="<?= \yii\helpers\Url::to(['finance/recharge/alipay']) ?>" class="small-box-footer">更多 <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <section class="col-lg-6 connectedSortable">
        <!-- solid sales graph -->
        <div class="box box-solid">
            <div class="box-header">
                <i class="fa fa-th"></i>
                <h3 class="box-title">充值提现成功金额<?=$days?>天走势图</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn bg-teal btn-sm" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="box-body border-radius-none">
                <?= \miloschuman\highcharts\Highcharts::widget([
                    'scripts' => [
                        'modules/exporting',
                        'themes/grid-light',
                    ],
                    'options' => [
                        'title' => ['text' => ''],
                        'xAxis' => [
                            'categories' => \common\utils\DateUtil::getLastDays($days)
                        ],
                        'yAxis' => [
                            'title' => ['text' => '金额']
                        ],
                        'series' => [
                            ['name' => '充值', 'data' => [0,0,0,0,0,0,0]],
                            ['name' => '提现', 'data' => [1,2,3,4,5,6,7]]
                        ]
                    ]
                ]);
                ?>
            </div>
        </div>
        <!-- /.box -->
    </section>

    <section class="col-lg-6 connectedSortable">
        <!-- solid sales graph -->
        <div class="box box-solid">
            <div class="box-header">
                <i class="fa fa-th"></i>
                <h3 class="box-title">最近<?=$days?>天各平台有效投注额</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn bg-teal btn-sm" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="box-body border-radius-none">
                <?= \miloschuman\highcharts\Highcharts::widget([
                    'scripts' => [
                        'modules/exporting',
                        'themes/grid-light',
                    ],
                    'options' => [
                        'chart' => ['type'=>'column'],
                        'title' => ['text' => ''],
                        'xAxis' => [
                            'categories' => \common\utils\DateUtil::getLastDays($days)
                        ],
                        'yAxis' => [
                            'title' => ['text' => '金额']
                        ],
                        'series' => [
                            ['name' => '提现', 'data' => [0,0,0,0,0,0,0]],
                            ['name' => '充值', 'data' => [1,2,3,4,5,0,8]]
                        ]
                    ]
                ]);
                ?>
            </div>
        </div>
        <!-- /.box -->
    </section>
</div>
