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

                <li class="<?php if(Yii::$app->controller->id == 'user'){ echo 'active';}?>">
                    <a href="#"><span class="nav-label">用户管理</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="<?php if(Yii::$app->controller->id == 'user' && Yii::$app->controller->action->id != 'login-logs'){ echo 'active';}?>"><a class="J_menuItem" href="<?= Url::to(['/admin/index']) ?>">用户列表</a>
                        <li class="<?php if(Yii::$app->controller->id == 'user' && Yii::$app->controller->action->id == 'login-logs' ){ echo 'active';}?>"><a class="J_menuItem" href="<?= Url::to(['/admin/login-logs']) ?>">用户登陆日志</a>
                    </ul>
                </li>

                <li class="<?php if(Yii::$app->controller->id == 'role' || Yii::$app->controller->id =='manager' || Yii::$app->controller->id == 'login-logs' ){ echo 'active';}?>">
                    <a href="#"><span class="nav-label">后台用户管理</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="<?php if(Yii::$app->controller->id == 'role'){ echo 'active';}?>"><a class="J_menuItem" href="<?= Url::to(['/role/index']) ?>">管理员角色</a>
                        <li class="<?php if(Yii::$app->controller->id == 'manager'){ echo 'active';}?>"><a class="J_menuItem" href="<?= Url::to(['/admin/index']) ?>">管理员列表</a>
                        <li class="<?php if(Yii::$app->controller->id == 'login-logs'){ echo 'active';}?>"><a class="J_menuItem" href="<?= Url::to(['/admin/login-logs']) ?>">登录日志</a>
                    </ul>
                </li>
                <li class="<?php if(Yii::$app->controller->id == 'customer'  ){ echo 'active';}?>">
                    <a href="#"><span class="nav-label">客服管理</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="<?php if(Yii::$app->controller->id == 'customer'){ echo 'active';}?>"><a class="J_menuItem" href="<?= Url::to(['/customer/index']) ?>">客服管理</a>
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

