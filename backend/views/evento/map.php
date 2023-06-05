<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlEventoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mappa eventi calamitosi in corso';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_partial_elicotteri_volo', []); ?>
<div id="map-canvas" class="site-index" ng-app="mapAngular">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if(Yii::$app->FilteredActions->showCartografico): ?>
        <p class="carto-link"><?php echo Html::a('Cartografia', ['/sistema-cartografico?can_add=1'], ['class' => 'btn btn-default']) ?></p>
    <?php endif; ?>
    <div ng-controller="mapEventoController">
        <ui-gmap-google-map center='{latitude: <?php echo Yii::$app->params['lat']; ?>, longitude: <?php echo Yii::$app->params['lng']; ?>}' zoom='map.zoom'>
            <ui-gmap-marker ng-repeat="marker in markers" coords="{latitude: marker.latitude, longitude: marker.longitude}" options="marker.options" events="marker.events" idkey="marker.id">
                <ui-gmap-window>
                    <div class="popup">
                        <h2><a href="view?id={{marker.id}}">{{marker.tipologia.tipologia}}</a></h2>
                        <p ng-show="{{hasVal(marker, 'stato')}}"><strong>Stato:</strong> {{marker.stato}}</p>
                        <p ng-show="{{hasVal(marker, 'latitude') || hasVal(marker, 'longitude')}}"><strong>Lat:</strong> {{marker.latitude}} - <strong>Lon:</strong> {{marker.longitude}}</p>
                        <p ng-show="{{hasVal(marker, 'note')}}"><strong>Note:</strong> {{marker.note}}</p>
                        <p ng-show="{{hasVal(marker, 'direzione')}}"><strong>Direzione:</strong> {{marker.direzione}}</p>
                    </div>
                </ui-gmap-window>
            </ui-gmap-marker>

        </ui-gmap-google-map>
    </div>

</div>
