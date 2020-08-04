<?php

namespace backend\assets;

use yii\web\AssetBundle;
use Yii;


/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $css = [
        'css/site.css',
        'css/autocomplete.css',
        'css/fontawesome-all.min.css',
        'css/ui-grid.css',
        'css/monitoraggio.css'
    ];

    public $js = [
        'js/helpers-module.js',
        'js/map-app.js',
        'js/yii-override.js',
        'js/ingaggio-ctrl.js',
        'js/evento-ctrl.js',
        'js/segnalazione-ctrl.js',
        'js/allerta-ctrl.js',
        'js/autocomplete-ctrl.js',
        'js/rubrica.js',
        'js/app.js',
        'js/mapper.js'
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'backend\assets\BowerAsset',
        'backend\assets\NpmAsset'
    ];
}
