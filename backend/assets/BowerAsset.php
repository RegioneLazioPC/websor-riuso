<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class BowerAsset extends AssetBundle
{
    public $basePath = '@bower';
    public $sourcePath = '@bower';

    public $css = [
        "sweetalert/dist/sweetalert.css",
        "angular-google-places-autocomplete/src/autocomplete.css",
        //"ag-grid-community/dist/styles/ag-grid.css",
        "angular-ui-grid/ui-grid.min.css"
        //"ag-grid-community/dist/styles/compiled-icons.css",
    ];

    public $js = [
        "sweetalert/dist/sweetalert.min.js",
        "lodash/dist/lodash.min.js",
        "angular/angular.min.js",
        "moment/min/moment.min.js",
        "angular-simple-logger/dist/angular-simple-logger.min.js",
        "angular-google-maps/dist/angular-google-maps.min.js",
        "angular-google-places-autocomplete/src/autocomplete.js",
        "ag-grid-community/dist/ag-grid-community.min.js",
        "angular-ui-grid/ui-grid.js",
        "angular-ui-grid/i18n/ui-grid.language.it.js",
        //"angular-auto-complete/angular-auto-complete.js"
    ];

    public $depends = [];
}
