<?php

use yii\helpers\Url;

$identity = Yii::$app->user->identity;
$identity = (Object) $identity;

$username = isset($identity->account) ? $identity->account : 'Guest';
$module = $this->context->module->id;
?>
<?php $this->beginContent('@app/views/layouts/global.php'); ?>

<?php $srcDataPrefix = 'data:image/jpg;base64,'; ?>
<?php $imgUrl = Url::home(true) .'img/'; ?>
<div id="wrapper" data-url="<?= $_SERVER['REQUEST_URI'] ?>">
    <!--左侧导航开始-->
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="nav-close"><i class="fa fa-times-circle"></i>
        </div>
        <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <span><img alt="image" class="img-circle" src="<?= $srcDataPrefix . (base64_encode(file_get_contents($imgUrl.'profile_small.jpg'))) ?>" /></span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                                    <input type="hidden" title="" id="login-user-id" value="<?= isset($identity->id) ? $identity->id : '' ?>">
                                <span class="block m-t-xs"><strong class="font-bold"><?= $username ?></strong></span>
                                </span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a class="J_menuItem" href="<?= Url::to(['/default/password']) ?>">修改密码</a></li>
                            <li class="divider"></li>
                            <li><a data-method="post" href="<?= Url::to(['/login/logout']) ?>">安全退出</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">H+
                    </div>
                </li>

                <li class="<?php if( Yii::$app->controller->id == 'default'){ echo 'active';}?>">
                    <a class="J_menuItem" href="<?= Url::to(['/default/index']) ?>"><i class="fa fa-home"></i> <span class="nav-label">首页</span></a>
                </li>

                <li class="<?php if(Yii::$app->controller->id == 'system-information'  ){ echo 'active';}else{ echo '';}?>">
                    <a href="#">
                        <i class="fa fa-gears"></i>
                        <span class="nav-label">系统模块</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level">
                        <li class="">
                            <a href="<?= Url::to(['/system-information/log']) ?>">
                                <i class="fa fa-bug"></i>
                                <span>系统日志</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="<?= Url::to(['/system-information/index']) ?>">
                                <i class="fa fa-line-chart"></i>
                                <span>系统监控</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="<?php if(Yii::$app->controller->id == 'authitem' || Yii::$app->controller->id =='admin'){ echo 'active';}else{ echo '';}?>">
                    <a href="#">
                        <i class="fa fa-user-secret"></i>
                        <span class="nav-label">用户模块</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level" test="<?=Yii::$app->controller->action->id;?>">
                        <li class="<?php if(Yii::$app->controller->id =='admin' && Yii::$app->controller->action->id == 'index'){ echo 'active';}?>">
                            <a class="J_menuItem" href="<?= Url::to(['/admin/index']) ?>">
                                <i class="fa fa-group"></i>
                                <span>用户列表</span>
                            </a>
                        </li>
                        <li class="<?php if(Yii::$app->controller->id =='authItem' && Yii::$app->controller->action->id == 'index'){ echo 'active';}?>">
                            <a class="J_menuItem" href="<?= Url::to(['/authitem/index']) ?>">
                                <i class="fa fa-group"></i>
                                <span>角色列表</span>
                            </a>
                        </li>
                        <li class="<?php if(Yii::$app->controller->id =='authItem' && Yii::$app->controller->action->id == 'privilege'){ echo 'active';}?>">
                            <a class="J_menuItem" href="<?= Url::to(['/authitem/privilege']) ?>">
                                <i class="fa fa-sitemap"></i>
                                <span>权限列表</span>
                            </a>
                        </li>
                        <li class="<?php if(Yii::$app->controller->id =='admin' && Yii::$app->controller->action->id == 'login-logs'){ echo 'active';}?>">
                            <a class="J_menuItem" href="<?= Url::to(['/admin/login-logs']) ?>">
                                <i class="fa fa-file-text"></i>
                                <span>登录日志</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="<?php if(Yii::$app->controller->id == 'channel'  ){ echo 'active';}else{ echo '';}?>">
                    <a href="#">
                        <i class="fa fa-cny"></i>
                        <span class="nav-label">财务模块</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level">
                        <li class="">
                            <a href="">
                                <i class="fa fa-mail-forward"></i>
                                <span>充值</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="">
                                <i class="fa fa-mail-reply"></i>
                                <span>提款</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="<?php if(Yii::$app->controller->id == 'customer'  || Yii::$app->controller->id == 'agency' ){ echo 'active';}else{ echo '';}?>">
                    <a href="#">
                        <i class="fa fa-user"></i>
                        <span class="nav-label">客户管理</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level">
                        <li class="<?php if(Yii::$app->controller->id == 'customer'){ echo 'active';}?>">
                            <a href="<?= Url::to(['/customer/index']) ?>">
                                <i class="fa fa-sun-o"></i>
                                <span>客服管理</span>
                            </a>
                        <li class="<?php if(Yii::$app->controller->id == 'agency'){ echo 'active';}?>">
                            <a href="<?= Url::to(['/agency/index']) ?>">
                                <i class="fa fa-gear"></i>
                                <span>单位管理</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="<?php if(Yii::$app->controller->id == 'channel'   ){ echo 'active';}else{ echo '';}?>">
                    <a href="#">
                        <i class="fa fa-tasks"></i>
                        <span class="nav-label">callu管理</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level">
                        <li class="<?php if(Yii::$app->controller->action->id == 'index'){ echo 'active';}?>">
                            <a href="<?= Url::to(['/channel/index']) ?>">
                                <i class="fa fa-bars"></i>
                                <span>渠道管理</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="<?php if(Yii::$app->controller->id == 'user'  ){ echo 'active';}else{ echo '';}?>">
                    <a href="#">
                        <i class="fa fa-tasks"></i>
                        <span class="nav-label">前台用户管理</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level">
                        <li class="<?php if(Yii::$app->controller->action->id == 'user'){ echo 'active';}?>">
                            <a href="<?= Url::to(['/user/index']) ?>">
                                <i class="fa fa-bars"></i>
                                <span>前台用户管理</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="<?php if(Yii::$app->controller->id == 'report'  ){ echo 'active';}else{ echo '';}?>">
                    <a href="#">
                        <i class="fa fa-tasks"></i>
                        <span class="nav-label">报表管理</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level">
                        <li class="<?php if(Yii::$app->controller->action->id == 'user-number'){ echo 'active';}?>">
                            <a href="<?= Url::to(['/report/user-number']) ?>">
                                <i class="fa fa-bars"></i>
                                <span>用户数报表</span>
                            </a>
                        </li>
                        <li class="<?php if(Yii::$app->controller->action->id == 'retained'){ echo 'active';}?>">
                            <a href="<?= Url::to(['/report/retained']) ?>">
                                <i class="fa fa-bars"></i>
                                <span>流存率报表</span>
                            </a>
                        </li>
                        <li class="<?php if(Yii::$app->controller->action->id == 'active-day'){ echo 'active';}?>">
                            <a href="<?= Url::to(['/report/active-day']) ?>">
                                <i class="fa fa-bars"></i>
                                <span>活跃日报表</span>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </nav>
    <!--左侧导航结束-->
    <!--右侧部分开始-->
    <div id="page-wrapper" class="gray-bg">
        <div class="row content-tabs">
            <div class="pull-right m-r-md">
                <span><?= isset($identity->account) ? $identity->account : ''; ?></span>
                <span>|</span>
                <a href="<?= Url::to(['/default/password']) ?>">修改密码</a>
                <span>|</span>
                <a data-method="post" href="<?= Url::to(['/login/logout']) ?>">退出</a>
            </div>
            <span class="pull-left m-l-md">当前位置：</span>
            <a>
                <?php
                echo \yii\widgets\Breadcrumbs::widget([
                    //'tag'=>'h2',
                    // 'homeLink'=>[
                    //    'label'=>'后台首页>>', 修改默认的Home
                    //    'url'=>Url::to(['index/index']), 修改默认的Home指向的url地址
                    // ],
                    'homeLink'=>false, // 若设置false 则 可以隐藏Home按钮
                    //'homeLink'=>['label' => '主 页','url' => Yii::$app->homeUrl.'home/'], // 若设置false 则 可以隐藏Home按钮
                    'itemTemplate'=>"<span>{link} > </span>",
                    'activeItemTemplate'=>"<span>{link}</span>",
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ])
                ?>
            </a>
        </div>
        <div class="row" id="content-main" style="overflow: auto; height: calc(100% - 50px)">
            <?= isset($content) ? $content : '' ?>
        </div>
        <!--<div class="footer">
            <div class="text-left">
                <a href="#">V 1.0.0</a>
            </div>
        </div>-->
    </div>
</div>
<?php $this->endContent(); ?>

