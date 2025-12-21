<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'theme/plugins/bootstrap-select/css/bootstrap-select.min.css',
        'theme/plugins/select2/css/select2.min.css',
        'theme/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css',
        'theme/assets/css/bootstrap.min.css',
        'theme/assets/css/icons.css',
        'theme/assets/css/metismenu.min.css',
        'theme/assets/css/style.css',
        'theme/assets/js/modernizr.min.js',
    ];
    public $js = [
        'theme/assets/js/bootstrap.bundle.min.js',
        'theme/assets/js/metisMenu.min.js',
        'theme/assets/js/waves.js',
        'theme/assets/js/jquery.slimscroll.js',
        'theme/plugins/select2/js/select2.min.js',
        'theme/plugins/bootstrap-select/js/bootstrap-select.js',
        'theme/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
        'theme/assets/pages/jquery.form-advanced.init.js',
        'theme/assets/pages/jquery.form-pickers.init.js',
        'theme/assets/js/jquery.core.js',
        'theme/assets/js/jquery.app.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];
}
