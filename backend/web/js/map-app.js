/**
 * Created by montes on 20/07/16.
 */
angular.module('mapAngular', ['uiGmapgoogle-maps', 'region.helpers'])
.config(function(uiGmapGoogleMapApiProvider, appConfig) {
    uiGmapGoogleMapApiProvider.configure({
        key: google_map_key,
        v: appConfig.MAP.version, 
        libraries: 'weather,geometry,visualization'
    });
})
.factory('MapService', ['$http', '$location', function( $http, $location) {

    
    var MapService = {
        
    };


    MapService.getEvents = function() {

        return $http({
            url: siteurl+'/evento/list-eventi-map?expand=tipologia',
            method: "GET"
        });
    };

    MapService.getSegnalazioni = function() {

        return $http({
            //url: apiurl+'/segnalazioni',
            url: siteurl+'/segnalazione/list-map',
            method: "GET"
        });

    };


    return MapService;

}])
.controller("mapEventoController", function($scope, uiGmapGoogleMapApi, MapService, appConfig) {

    $scope.markers = [];

    MapService.getEvents().then(function (res) {
        console.log('response', res)
        angular.forEach(res.data, function(item, key) {

            var statoEvento = '';
            switch (item.stato) {
              case 'Allarme':
                statoEvento = 'allarme';
                break;
              case 'Emergenza':
                statoEvento = 'emergenza';
                break;
              default:
                statoEvento = 'preallarme';
                break;
            }


            
            this.push({
                id: item.id,
                latitude: item.lat,
                longitude: item.lon,
                stato: item.stato,
                note: item.note,
                direzione: item.direzione,
                tipologia: item.tipologia,
                options: {
                    icon: {
                        url: siteurl+"/images/markers/"+item.tipologia.icon_name,
                        scaledSize: new google.maps.Size(40, 40),
                        origin: new google.maps.Point(0,0), // origin
                        anchor: new google.maps.Point(0, 0)
                    }   
                }
            });

        }, $scope.markers);
    });

    $scope.hasVal = function(marker, data) {
        console.log(marker, data)
        return marker[data] ? true : false
    }

    $scope.map = {
        center: {
            latitude: appConfig.MAP.lat,
            longitude: appConfig.MAP.lon
        },
        zoom: 9,
        bounds: {},
        markers: [],
        window: {
            visible : true
        }
    };

    
    uiGmapGoogleMapApi.then(function(maps) { });
})
.controller("mapSegnalazioneController", function($scope, uiGmapGoogleMapApi, MapService, $interval, appConfig) {
    
    $scope.markers = [];

    function call() {
        console.log('chiama')
        MapService.getSegnalazioni().then(function (res) {
            console.log('result', res);
            angular.forEach(res.data, function (item, key) {

                
                this.push({
                    id: item.id,
                    latitude: item.lat,
                    longitude: item.lon,
                    note: item.note,
                    direzione: item.direzione,
                    tipologia: item.tipologia,
                    comune: item.comune,
                    dataora: item.dataora_segnalazione,
                    options: {
                        icon: {
                        url: siteurl+"/images/markers/"+item.tipologia.icon_name,
                        scaledSize: new google.maps.Size(40, 40),
                        origin: new google.maps.Point(0,0), // origin
                        anchor: new google.maps.Point(0, 0)
                    }   
                   }
                });

            }, $scope.markers);
        });
    }


    call();

    setInterval( call, 60000);


        $scope.map = {
            center: {
                latitude: appConfig.MAP.lat,
                longitude: appConfig.MAP.lon
            },
            zoom: 8,
            bounds: {},
            markers: [],
            window: {
                visible : true
            }
        };

        // uiGmapGoogleMapApi is a promise.
        // The "then" callback function provides the google.maps object.
        uiGmapGoogleMapApi.then(function(maps) {});
    })
.filter('dateToISO', function() {
    return function(input) {
        input = new Date(input).toISOString();
        return input;
    };
});
