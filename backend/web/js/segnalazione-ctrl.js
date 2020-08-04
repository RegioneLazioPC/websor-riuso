angular.module('segnalazione', ['region.helpers','mapAngular'])
    .config(function($compileProvider){
        'ngInject'; $compileProvider.preAssignBindingsEnabled(true);
    })
    .factory('MapService', ['$http', '$location', function( $http, $location) {

        //var SERVICE_URL = appConfig.SERVICES_URL;
        var MapService = {
            
        };

        MapService.getLatLonToAddress = function(address){

            var endpoint = 'https://maps.googleapis.com/maps/api/geocode/json?address=' + address + '&key='+google_map_key;
            return $http({
                url: endpoint,
                method: "GET"
            });

        };

        MapService.getAddressToLatLon = function(latLon){

            var endpoint = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='+latLon+'&key='+google_map_key;
            return $http({
                url: endpoint,
                method: "GET"
            });

        };

        return MapService;

    }])
    .controller("segnalazioneViewController", function($scope, MapService) {

        var selfScope = this;

        selfScope.myButtonLabels = {
            rotateLeft: '<i class="fa fa-rotate-left" data-toggle="tooltip" title="Ruota a sinistra"></i>',
            rotateRight: '<i class="fa fa-rotate-right" data-toggle="tooltip" title="Ruota a destra"></i>',
            zoomIn: '<i class="fa fa-plus" data-toggle="tooltip" title="Zoom In"></i>',
            zoomOut: '<i class="fa fa-minus" data-toggle="tooltip" title="Zoom Out"></i>',
            fit: '<i class="fa fa-arrows" data-toggle="tooltip" title="Dimensioni iniziali"></i>',
            crop: false // You can pass html too.
        };

        //angular.bootstrap(document.getElementById("segnalazioni-map-canvas"), ['mapAngular']);

        selfScope.listEventi = false;
    })
    .controller("segnalazioneFormController", function($scope, MapService, uiGmapGoogleMapApi) {

        var selfScope = this;

        selfScope.map = null;
        selfScope.indirizzo = "";
        selfScope.comune = "";
        selfScope.luogo = "";
        selfScope.lat = "";
        selfScope.lon = "";
        selfScope.id_tipo_ente_pubblico = null;

        selfScope.checkboxesTipo = [];
        selfScope.tipoSegnalatore = '1';
        selfScope.ruoloSegnalatore = null;

        selfScope.checkBoxes = [];

        selfScope.setLatLon = function( lat, lon ) {
        
            selfScope.lat = parseFloat(""+lat);
            selfScope.lon = parseFloat(""+lon);
            
            if($scope.mapInstance) $scope.mapInstance.setCenter({lat:parseFloat(""+lat), lng:parseFloat(""+lon)});
        }

        $scope.mapInstance = null;
        $scope.map = {
            events: {
                tilesloaded: function (map) {
                    $scope.$apply(function () {
                        $scope.mapInstance = map;
                    });                
                }
            }
        }

        // uiGmapGoogleMapApi is a promise.
        // The "then" callback function provides the google.maps object.
        uiGmapGoogleMapApi.then(function(maps) { 
            selfScope.map = maps;
        });


    })
    .factory('AutocompleteService', ['$http', '$location', '$httpParamSerializer', function( $http, $location, $httpParamSerializer ) {

        var AutocompleteService = {
        };

        AutocompleteService.search = function ( comune, provincia, string, csrf ){
            var formData = new FormData;
            formData.append('_csrf-backend', csrf);
            formData.append('comune', comune);
            formData.append('provincia', provincia);
            formData.append('string', string);

            return $http({
                url: siteurl+'/evento/search-indirizzo',
                data: formData,
                headers: { 'Content-Type': undefined},
                transformRequest: angular.identity,
                method: "POST"
            });
        };

        AutocompleteService.searchToponimo = function ( comune, provincia, string, csrf ){
            var formData = new FormData;
            formData.append('_csrf-backend', csrf);
            formData.append('comune', comune);
            //formData.append('provincia', provincia);
            formData.append('string', string);

            return $http({
                url: siteurl+'/evento/search-toponimo',
                data: formData,
                headers: { 'Content-Type': undefined},
                transformRequest: angular.identity,
                method: "POST"
            });
        };

        AutocompleteService.getComune = function ( comune ){
            

            return $http({
                url: siteurl+'/evento/get-comune?id_comune='+comune,
                method: "GET"
            });
        };

        return AutocompleteService;

    }])
    .controller("AutocompleteController", function($scope, appConfig, AutocompleteService) {

        var selfScope = this;
        selfScope.comune = "";

        selfScope.comune = null;
        selfScope.address = '';
        selfScope.toponimo = '';
        $scope.csrf = null;

        $scope.results = [];
        $scope.toponimo_results = [];

        $scope.init = function(token, parentCtrl = null) {
            $scope.csrf = token;

            if(parentCtrl) $scope.parentCtrl = parentCtrl;
        }

        $scope.avaible_civici = [];


        $scope.loadInitAddress = function(address) {
            var a = address.split(" ");
            var cap = a[a.length -1 ];
            var civico = a[a.length -2 ];
            selfScope.address = address.replace( " " + civico + " " + cap, "");
            selfScope.civico = civico;
            selfScope.cap = cap;
            $scope.avaible_civici = [{civico: civico}]

        }

        $scope.loadInitComune = function(id) {
            selfScope.comune = id;
            $scope.loadComune()
        }

        $scope.loadComune = function() {
            
            //selfScope.comune
            AutocompleteService.getComune( selfScope.comune )
            .then(function(res){
                console.log('comune', res)
                if(res.data) {
                    $scope.comune_name = res.data.comune
                    $scope.provincia_sigla = res.data.provincia_sigla
                }
            }).catch(function(err){
                console.warn(err);
            })
        }

        $scope.comune_name = '';
        $scope.provincia_sigla = '';


        $scope.loadResults = function() {
            
            if(selfScope.address.length > 3) {
                AutocompleteService.search($scope.comune_name, $scope.provincia_sigla, selfScope.address, $scope.csrf)
                .then(function(res){
                    
                    $scope.results = res.data;

                }).catch(function(err){
                    console.warn(err)
                })
            }
        }

        $scope.loadToponimoResults = function() {
            
            if(selfScope.toponimo.length > 3) {
                AutocompleteService.searchToponimo($scope.comune_name, $scope.provincia_sigla, selfScope.toponimo, $scope.csrf)
                .then(function(res){
                    
                    $scope.toponimo_results = res.data;

                }).catch(function(err){
                    console.warn(err)
                })
            }
        }

        $scope.selectToponimo = function(toponimo) {

            selfScope.luogo = toponimo.toponimo;
            selfScope.toponimo = toponimo.toponimo;
            selfScope.lat = toponimo.lat
            selfScope.lon = toponimo.lon;
            $scope.toponimo_results = [];     
            selfScope.address = null;
            selfScope.civico = null;
            selfScope.cap = null;

            $scope.updateMarker(selfScope.lat, selfScope.lon);        
        }

        $scope.selectAddress = function(addr) {
            selfScope.address = addr.via;
             var cc = JSON.parse(addr.civici).sort( function( a, b ) {
                var regex_n = /^[0-9]+/gi;
                var regex_n_ = /^[0-9]+/gi;
                var regex_s = /^[a-z]+/gi;
                var regex_s_ = /^[a-z]+/gi;    

                
                var ar = parseInt(regex_n.exec(a.civico));
                var br = parseInt(regex_n_.exec(b.civico));
                var astr = regex_s.exec(a.civico);
                var bstr = regex_s_.exec(b.civico);

                
                
                if( ar < br ) {
                    return -1;
                } else if( ar > br ) {
                    return 1;
                } else {
                    return (astr > bstr) ? -1: 1; 
                }

            } );

            $scope.avaible_civici = cc;//JSON.parse(addr.civici);
            $scope.results = [];       
            selfScope.civico = null
            selfScope.cap = null     
        }

        selfScope.civico_obj = {};

        $scope.selectCivico = function() {
            
            selfScope.civico_obj = $scope.avaible_civici.find(c => c.civico === selfScope.civico);
            if(selfScope.civico_obj) {
                
                selfScope.lat = parseFloat(selfScope.civico_obj.lat)
                selfScope.lon = parseFloat(selfScope.civico_obj.lon)
                selfScope.cap = selfScope.civico_obj.cap;

                $scope.updateMarker(selfScope.lat, selfScope.lon);                

            } else {
                console.warn('no civico')
            }
        }


        $scope.updateMarker = function(lat, lon) {
            lat = lat.toString().replace(/[^0-9\.]+/g, "");
            lon = lon.toString().replace(/[^0-9\.]+/g, "");
            selfScope.lat = lat; 
            selfScope.lon = lon;
            if($scope.parentCtrl) $scope.parentCtrl.setLatLon( parseFloat(lat), parseFloat(lon) )
        }

        $scope.updateManualMarker = function(lat, lon) {
            
            selfScope.address = ""
            $scope.updateMarker(lat, lon);
        }

        window.addEventListener('changedLatLng', function(e) {                   
            $scope.updateManualMarker( e.detail.lat, e.detail.lon );
        });

    });
