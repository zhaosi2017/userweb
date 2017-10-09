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
class DashboardAsset extends AssetBundle
{

    public $css = [
        'https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css'
    ];
    public $js = [
        'js/plugins/bootstrap-toastr/toastr.min.js',
    ];
}
