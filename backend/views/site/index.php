<?php

/* @var $this yii\web\View */

$this->title = 'Eventi sul territorio';
?>
<div id="map-canvas" class="site-index" ng-app="mapAngular">

    <div ng-controller="mapController">
        <ui-gmap-google-map center='map.center' zoom='map.zoom'>

            <ui-gmap-circle
                ng-repeat="c in circles track by c.id"
                center="c.center"
                stroke="c.stroke"
                fill="c.fill"
                radius="c.radius"
                visible="c.visible"
                geodesic="c.geodesic"
                editable="c.editable"
                draggable="c.draggable"
                clickable="c.clickable"
                control="c.control"></ui-gmap-circle>

            <ui-gmap-marker ng-repeat="marker in markers" coords="{latitude: marker.latitude, longitude: marker.longitude}" options="marker.options" events="map.markersEvents" idkey="marker.id"> </ui-gmap-marker>

        </ui-gmap-google-map>
    </div>

</div>
