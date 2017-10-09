<?php
/**
 * @var $this \yii\web\View
 * @var $provider \probe\provider\ProviderInterface
 */

$this->title = '系统信息';
$this->registerJs("window.paceOptions = { ajax: false }", \yii\web\View::POS_HEAD);
$this->registerJsFile(
    Yii::$app->request->baseUrl . 'js/system-information/index.js',
    ['depends' => ['\yii\web\JqueryAsset', '\common\assets\Flot', '\yii\bootstrap\BootstrapPluginAsset']]
) ?>
<div id="system-information-index">
<div class="row connectedSortable">
    <div class="col-lg-4 col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <i class="fa fa-hdd-o"></i>
                <h3 class="box-title"><?php echo '处理器' ?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo '处理器' ?></dt>
                    <dd><?php echo $provider->getCpuModel() ?></dd>

                    <dt><?php echo '服务器架构' ?></dt>
                    <dd><?php echo $provider->getArchitecture() ?></dd>

                    <dt><?php echo '核心数' ?></dt>
                    <dd><?php echo $provider->getCpuCores() ?></dd>
                </dl>
            </div><!-- /.box-body -->
        </div>
    </div>
    <div class="col-lg-4 col-sm-12">
        <div class="box box-primary">
            <div class="box-header">
                <i class="fa fa-hdd-o"></i>
                <h3 class="box-title"><?php echo '操作系统' ?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo '操作系统' ?></dt>
                    <dd><?php echo $provider->getOsType() ?></dd>

                    <dt><?php echo '系统版本' ?></dt>
                    <dd><?php echo $provider->getOsRelease() ?></dd>

                    <dt><?php echo '核心版本' ?></dt>
                    <dd><?php echo $provider->getOsKernelVersion() ?></dd>
                </dl>
            </div><!-- /.box-body -->
        </div>
    </div>
    <div class="col-lg-4 col-sm-12">
        <div class="box box-primary">
            <div class="box-header">
                <i class="fa fa-hdd-o"></i>
                <h3 class="box-title"><?php echo '时间' ?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo '系统日期' ?></dt>
                    <dd><?php echo Yii::$app->formatter->asDate(time()) ?></dd>

                    <dt><?php echo '系统时间' ?></dt>
                    <dd><?php echo Yii::$app->formatter->asTime(time()) ?></dd>

                    <dt><?php echo '时区' ?></dt>
                    <dd><?php echo date_default_timezone_get() ?></dd>
                </dl>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>
<div class="row connectedSortable">
    <div class="col-lg-4 col-sm-12">
        <div class="box box-primary">
            <div class="box-header">
                <i class="fa fa-hdd-o"></i>
                <h3 class="box-title"><?php echo '软件' ?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo 'Web 服务器' ?></dt>
                    <dd><?php echo $provider->getServerSoftware() ?></dd>

                    <dt><?php echo 'PHP 版本' ?></dt>
                    <dd><?php echo $provider->getPhpVersion() ?></dd>

                    <dt><?php echo '数据库类型' ?></dt>
                    <dd><?php echo $provider->getDbType(Yii::$app->db->pdo) ?></dd>

                    <dt><?php echo '数据库版本' ?></dt>
                    <dd><?php echo $provider->getDbVersion(Yii::$app->db->pdo) ?></dd>
                </dl>
            </div><!-- /.box-body -->
        </div>
    </div>
    <div class="col-lg-4 col-sm-12">
        <div class="box box-primary">
            <div class="box-header">
                <i class="fa fa-hdd-o"></i>
                <h3 class="box-title"><?php echo '内存' ?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo '总内存' ?></dt>
                    <dd><?php echo Yii::$app->formatter->asSize($provider->getTotalMem()) ?></dd>

                    <dt><?php echo '空闲内存' ?></dt>
                    <dd><?php echo Yii::$app->formatter->asSize($provider->getFreeMem()) ?></dd>

                    <dt><?php echo '总Swap内存' ?></dt>
                    <dd><?php echo Yii::$app->formatter->asSize($provider->getTotalSwap()) ?></dd>

                    <dt><?php echo '空闲Swap内存' ?></dt>
                    <dd><?php echo Yii::$app->formatter->asSize($provider->getFreeSwap()) ?></dd>
                </dl>
            </div><!-- /.box-body -->
        </div>
    </div>
    <div class="col-lg-4 col-sm-12">
        <div class="box box-primary">
            <div class="box-header">
                <i class="fa fa-hdd-o"></i>
                <h3 class="box-title"><?php echo '网络' ?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo '主机名' ?></dt>
                    <dd><?php echo $provider->getHostname() ?></dd>

                    <dt><?php echo '内部IP' ?></dt>
                    <dd><?php echo $provider->getServerIP() ?></dd>

                    <dt><?php echo '外部IP' ?></dt>
                    <dd><?php echo $provider->getExternalIP() ?></dd>

                    <dt><?php echo '端口' ?></dt>
                    <dd><?php echo $provider->getServerVariable('SERVER_PORT') ?></dd>
                </dl>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div id="cpu-usage" class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">
                    <?php echo 'CPU 使用' ?>
                </h3>
                <div class="box-tools pull-right">
                    <?php echo '实时' ?>
                    <div class="realtime btn-group" data-toggle="btn-toggle">
                        <button type="button" class="btn btn-default btn-xs active" data-toggle="on">
                            <?php echo '开' ?>
                        </button>
                        <button type="button" class="btn btn-default btn-xs" data-toggle="off">
                            <?php echo '关' ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="chart" style="height: 300px;">
                </div>
            </div><!-- /.box-body-->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div id="memory-usage" class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">
                    <?php echo '内存 使用' ?>
                </h3>
                <div class="box-tools pull-right">
                    <?php echo '实时' ?>
                    <div class="btn-group realtime" data-toggle="btn-toggle">
                        <button type="button" class="btn btn-default btn-xs active" data-toggle="on">
                            <?php echo '开' ?>
                        </button>
                        <button type="button" class="btn btn-default btn-xs" data-toggle="off">
                            <?php echo '关' ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="chart" style="height: 300px;">
                </div>
            </div><!-- /.box-body-->
        </div>
    </div>
</div>
</div>
