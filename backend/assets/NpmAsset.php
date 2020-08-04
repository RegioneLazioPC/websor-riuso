<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class NpmAsset extends AssetBundle
{
    public $basePath = '@npm';
    public $sourcePath = '@npm';

    public $css = [
        "viewerjs/dist/viewer.min.css"
    ];

    public $js = [
        "viewerjs/dist/viewer.min.js"
    ];

    public $depends = [];
}
