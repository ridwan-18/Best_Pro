<?php

namespace app\assets;

use yii\web\AssetBundle;

class PrintAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'theme/assets/css/normalize.min.css',
        'theme/assets/css/paper.css',
    ];
    public $js = [];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
