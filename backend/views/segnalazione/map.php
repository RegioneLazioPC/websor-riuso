<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlEventoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mappa segnalazioni';
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="map-canvas" class="site-index" ng-app="mapAngular">

    <h1><?= Html::encode($this->title) ?></h1>
    <p class="carto-link"><?php echo Html::a('Cartografia', ['/sistema-cartografico?hide_events=1&visible_reports=1'], ['class' => 'btn btn-default']) ?></p>
    <div ng-controller="mapSegnalazioneController">
        <ui-gmap-google-map center='{latitude: <?php echo Yii::$app->params['lat']; ?>, longitude: <?php echo Yii::$app->params['lng']; ?>}' zoom='map.zoom'>


            <ui-gmap-marker ng-repeat="marker in markers" coords="{latitude: marker.latitude, longitude: marker.longitude}" options="marker.options" events="marker.events" idkey="marker.id">

                <ui-gmap-window>
                    <div class="popup">
                        <h3><a href="view?id={{marker.id}}">{{marker.tipologia.tipologia}}</a></h3>
                        <p ng-show="{{marker.dataora != null}}"><strong>Data/ora:</strong> {{marker.dataora | dateToISO | date:'dd-MM-yyyy HH:mm'}}</p>
                        <p ng-show="{{marker.comune.comune != null}}"><strong>Comune:</strong> {{marker.comune.comune}} {{marker.comune.provincia_sigla}}</p>
                        <p ng-show="{{marker.latitude != null}} || {{marker.longitude != null}}"><strong>Lat:</strong> {{marker.latitude}} - <strong>Lon:</strong> {{marker.longitude}}</p>

                        <p ng-show="{{(marker.direzione) ? true : false}}"><strong>Direzione:</strong> {{marker.direzione}}</p>

                    </div>
                </ui-gmap-window>
            </ui-gmap-marker>


        </ui-gmap-google-map>
    </div>

</div>
