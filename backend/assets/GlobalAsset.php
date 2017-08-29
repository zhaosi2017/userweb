<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class GlobalAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/global/bootstrap.min.css?v=3.3.6',
        'css/global/font-awesome.min.css?v=4.4.0',
        'css/global/animate.css',
        'css/global/style.css?v=4.1.0',

    ];
    public $js = [
        'js/global/bootstrap.min.js?v=3.3.6',
//        'js/global/layer.min.js',
        'js/global/layer.js',
        //public
        'js/public/jquery.metisMenu.js',
        'js/public/jquery.slimscroll.min.js',
        'js/public/pace.min.js',
        'js/home/hplus.js?v=4.1.0',

        'js/home/contabs.js',


    ];
    public $depends = [
        'yii\web\YiiAsset',
//        'app\assets\JqueryAsset',
    ];
}
