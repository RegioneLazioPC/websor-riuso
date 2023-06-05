angular.module('evento', ['region.helpers', 'mapAngular'])
.controller("eventoController", function($scope, appConfig, uiGmapGoogleMapApi) {

    var selfScope = this;
    selfScope.map = null;
    selfScope.comune = "";

    selfScope.stato = "Non gestito";
    selfScope.sottotipologia_evento = null;

    selfScope.checkBoxes = [];

    selfScope.centerMap = {
        latitude: appConfig.MAP.lat,
        longitude: appConfig.MAP.lon
    };

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


    selfScope.id_tipo_colonna = null;
    selfScope.n_elicotteri = 0;


    $scope.loadInitSottotipologia = function(id, id_colonna_fumo, n_elicotteri) {
        selfScope.sottotipologia_evento = id
        selfScope.id_tipo_colonna = id_colonna_fumo.toString()
        selfScope.n_elicotteri = parseInt(n_elicotteri)
    }

    $scope.changedSottotipo = function() {
        
    }

    $scope.needPopupEvt = function() {
        return selfScope.stato == 'Chiuso' && 
        parseInt(selfScope.sottotipologia_evento) == selfScope.id_tipo_colonna && 
        selfScope.n_elicotteri > 0
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

        AutocompleteService.searchToponimo = function ( comune, provincia, string, csrf ){
            var formData = new FormData;
            formData.append('_csrf-backend', csrf);
            //formData.append('comune', comune);
            //formData.append('provincia', provincia);
            formData.append('comune', comune);
            formData.append('string', string);

            return $http({
                url: siteurl+'/evento/search-toponimo',
                data: formData,
                headers: { 'Content-Type': undefined},
                transformRequest: angular.identity,
                method: "POST"
            });
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
        selfScope.toponimo = '';
        selfScope.address = ''
        $scope.csrf = null;

        $scope.results = [];
        $scope.toponimo_results = [];

        $scope.init = function(token, parentCtrl = null) {
            $scope.csrf = token;

            if(parentCtrl) $scope.parentCtrl = parentCtrl
        }

        $scope.updateMarker = function(lat, lon) {
            selfScope.lat = parseFloat(lat); selfScope.lon = parseFloat(lon);
            if($scope.parentCtrl) $scope.parentCtrl.setLatLon(lat, lon)
        }

        $scope.updateManualMarker = function(lat, lon) {
            selfScope.address = ""
            $scope.updateMarker(lat, lon);
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

        selfScope.civico_obj = {};

        $scope.selectCivico = function() {
            
            selfScope.civico_obj = $scope.avaible_civici.find(c => c.civico === selfScope.civico);
            if(selfScope.civico_obj) {
                
                selfScope.lat = parseFloat(selfScope.civico_obj.lat)
                selfScope.lon = parseFloat(selfScope.civico_obj.lon)
                selfScope.cap = selfScope.civico_obj.cap

                

                $scope.updateMarker(selfScope.lat, selfScope.lon);

            } else {
                console.warn('no civico')
            }
        }

        window.addEventListener('changedLatLng', function(e) {
            
            $scope.updateManualMarker( e.detail.lat, e.detail.lon );

        });

    });

//angular.bootstrap(document.getElementById("map-canvas-mod"), ['mapAngular']);