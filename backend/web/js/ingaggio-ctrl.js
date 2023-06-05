
angular.module('ingaggi', ['uiGmapgoogle-maps', 'region.helpers', 'mapAngular','google.places'])

.factory('SearchService', ['$http', '$location', '$httpParamSerializer', function( $http, $location, $httpParamSerializer) {

    //var SERVICE_URL = appConfig.SERVICES_URL;
    var SearchService = {
        
    };

    SearchService.find = function(data) {
        
        var obj = {};
        angular.copy(data, obj);
        obj.id_utl_automezzo_tipo = (typeof data.id_utl_automezzo_tipo == 'object') ? data.id_utl_automezzo_tipo.join(",") : '';
        obj.id_utl_attrezzatura_tipo = (typeof data.id_utl_attrezzatura_tipo == 'object') ? data.id_utl_attrezzatura_tipo.join(",") : '';
        obj.id_categoria = (typeof data.id_categoria == 'object') ? data.id_categoria.join(",") : '';
        obj.id_tipologia = (typeof data.id_tipologia == 'object') ? data.id_tipologia.join(",") : '';
        obj.specializzazioni = (typeof data.specializzazioni == 'object') ? data.specializzazioni.join(",") : '';
        
        let q_string = $httpParamSerializer(obj);

        //console.log('q_string',siteurl+'/ingaggio/search-organizzazione?'+q_string);
        return $http({
            url: siteurl+'/ingaggio/search-organizzazione?'+q_string,
            method: "GET"
        });
    }

    SearchService.engage = function(obj, event) {

        let q_string = $httpParamSerializer({
            ref_id: obj.ref_id,
            ref_type: obj.tipologia_risorsa,
            event_id: event
        });
        return $http({
            url: siteurl+'/ingaggio/ingaggia?'+q_string,
            method: "GET"
        })
    }

    SearchService.calculateDistance = function(coords, center) {
        let q_string = $httpParamSerializer({
            from_lat: coords[1],
            from_lon: coords[0],
            to_lat: center[1],
            to_lon: center[0]
        });
        return $http({
            url: siteurl+'/ingaggio/distance?'+q_string,
            method: "GET"
        });
    }

    return SearchService;

}])
.controller("ingaggioSearchController", function($scope, uiGmapGoogleMapApi, SearchService, appConfig) {

    $scope.markers = [];
    
    var selfScope = this;

    selfScope.id_utl_automezzo_tipo = [];
    selfScope.id_utl_attrezzatura_tipo = [];
    selfScope.id_categoria = [];
    selfScope.id_tipologia = [];
    selfScope.id_comune = "";
    selfScope.id_provincia = "";
    selfScope.distance = "";
    selfScope.id_evento = "";
    selfScope.id_organizzazione = "";
    selfScope.num_comunale = "";
    selfScope.base_lat = "";
    selfScope.base_lon = "";
    selfScope.lat = "";
    selfScope.lon = "";
    selfScope.id_specializzazione_sede = [];
    selfScope.page = 1;

    $scope.place = null;

    $scope.gotopage = "";

    
    $scope.autocompleteOptions = {
        //bounds: new google.maps.LatLngBounds(new google.maps.LatLng(41.177572,11.323039), new google.maps.LatLng(42.950794,13.401760)),
        //strictBounds: true,
        //types: ['address'],
        //componentRestrictions: {country: 'it'}
    }
    
    $scope.initialized = false;
    $scope.results = [];

    
    $scope.total_pages = 1;
    $scope.show_pages = [];

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

    $scope.meta_keys = [];
    
    $scope.inizializza = function(lat, lng, meta_keys) {
        
        selfScope.lat = lat;
        selfScope.lon = lng;
        selfScope.base_lat = lat;
        selfScope.base_lon = lng;
        $scope.map.center = {
            latitude: lat,
            longitude: lng
        }

        setMetaKeys(meta_keys);

    }

    setMetaKeys = function(meta_keys) {
        selfScope.meta_keys = meta_keys;
    }
    /**
     * inserisci categoria iniziale in base a tipo evento
     * @param {[type]} cat [description]
     */
    $scope.setCategoriaDefault = function(cat) {
        
        if(cat.length > 0) cat.map(c => {
            selfScope.id_categoria.push(""+c)
        });


        
    }

    $scope.changedCategory = function() {
        refresh_select2_cat_options();        
    }

    $scope.changedType = function() {
        refresh_select2_type_options();        
    }

    $scope.isDisabledTipologiaOption = function(type, category) {
        return selfScope.id_categoria.indexOf(""+category) == -1
    }

    /**
     * 
     * @param  {[type]}  id          [description]
     * @param  {[type]}  categorie   [description]
     * @param  {[type]}  aggregatori [description]
     * @return {Boolean}             [description]
     */
    $scope.isDisabledTipoMezzoAttrezzatura = function(id, categorie, aggregatori, map) {
        let disabled = true;
        map = JSON.parse(map);
        //console.log('map',map)
        
        // se seleziona tutti o il resto è vuoto tornali tutti
        if(selfScope.id_tipologia.length == 0 && selfScope.id_categoria.length == 0 || selfScope.id_categoria.indexOf("0") != -1) return false;

        
        // se viene inserita una tipologia per una categoria, successivamente non devo verificare la categoria (il filtro è sulla tipologia)
        // uso l'oggetto della mappatura per farlo
        let categories_to_disable = [];

        if(selfScope.id_tipologia.length > 0) {
            selfScope.id_tipologia.map(selected_type => {
                
                if(aggregatori.indexOf(parseInt(selected_type)) != -1) {
                    // quello selezionato è fra quelli dell'elemento
                    disabled = false; 
                }               
                categories_to_disable.push(map[""+selected_type]);
            })
            
        } 
        
        categorie = categorie.filter(c => categories_to_disable.indexOf(parseInt(c)) == -1);
        
        
        categorie.map(cat => {
            if(selfScope.id_categoria.indexOf(""+cat) != -1) {
                
                disabled = false; 
            }
        })

        return disabled;
    }

    $scope.setSpecializzazioni = function(list) {
        var sp = []
        list.map(el => {
            sp.push(el.toString())
        })
        return sp
    }


    $scope.submitForm = function() {       
        if(!$scope.initialized) $scope.initialized = true;
        
        // inserisco lat e lon se indirizzo selezionato
        if($scope.place && $scope.place.geometry && $scope.place.geometry.location){
            selfScope.lat = $scope.place.geometry.location.lat();
            selfScope.lon = $scope.place.geometry.location.lng();
        } else {
            selfScope.lat = selfScope.base_lat;
            selfScope.lon = selfScope.base_lon;
        }
        
        selfScope.page = 1;
        search();
    }

    search = function() {
        $scope.markers = [{
            id: "0_event",
            lat: selfScope.lat,
            lon: selfScope.lon
        }];

        SearchService.find(selfScope).then(res => {
            
            $scope.results = res.data.data.slice(0, 50);
            $scope.total_pages = res.data.pages;
            selfScope.page = res.data.page;
            setPages();
            $scope.gotopage = "";
            angular.forEach(res.data.data, function(item, key, index) {
                
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
                        id: item.ref_id+"_"+item.tipologia_risorsa,
                        lat: item.lat,
                        lon: item.lon,
                        organizzazione: item.denominazione_organizzazione,
                        options: {
                          icon: siteurl+"/images/icons/32/sede.png"
                        }                   
                    });
                

            }, $scope.markers);
        })
    }

    $scope.nextPage = function() {
        if($scope.total_pages > selfScope.page) {
            selfScope.page += 1;
            search();
        }
    }

    $scope.prevPage = function() {
        if(selfScope.page > 0) {
            selfScope.page -= 1;
            search();
        }
    }

    $scope.loadPage = function(p) {
        if($scope.total_pages >= p && p > 0) {
            selfScope.page = p;
            search();
        }
    }

    setPages = function() {
        var pages = [];
        if(selfScope.page > 0) {
            for(n = (selfScope.page - 2); n < selfScope.page; n++) {
                
                if(pages.indexOf(n) == -1 && n > 0) pages.push(n);}
        }

        if($scope.total_pages >= selfScope.page) {
            for(n = (selfScope.page); n < selfScope.page + 3 && n <= $scope.total_pages; n++) {
                
                if(pages.indexOf(n) == -1) pages.push(n);}
        }

        $scope.show_pages = pages;
    }


    $scope.distanceFormat = function(d) {
        return d;
        //var dist = "" + (parseInt(d) / 1000);
        //return dist.replace(/\./g, ",") + " km"
    }

    $scope.engaging = false;
    $scope.engaging_ids = [];

    $scope.engage = function(obj, event) {
        
        if(!$scope.engaging && $scope.engaging_ids.indexOf(obj.riferimento)) {
            $scope.engaging = true;
            $scope.engaging_ids.push(obj.riferimento);
            SearchService.engage(obj, event)
            .then(function(res){
                if(!res.data.error) {
                    window.reload_ingaggi();
                    search();
                }
                $scope.engaging = false;
            }, function(err){
                $scope.engaging = false;
                $scope.engaging_ids = $scope.engaging_ids.filter(function(e) { return e != obj.riferimento });
            })
        }
    }

    $scope.hasEngagedInSession = function(obj) {
        return $scope.engaging_ids.indexOf(obj.riferimento) != -1 ? true : false;
    }

    $scope.calculate = function(coords, obj) {
        
        SearchService.calculateDistance(coords, [selfScope.lon, selfScope.lat]).then(function(res){
            
            if(res.data && res.data.duration) {
                
                angular.forEach($scope.results, function(item, key, index) {
                    if(item.ref_id == obj.ref_id && 
                        item.tipologia_risorsa == obj.tipologia_risorsa) 
                    {
                        
                        obj.time = res.data.duration;
                        obj.dist = res.data.distance;
                        
                        $scope.results[index] = obj;

                    }
                })
            }
        })
    }

    $scope.getContacts = function( string ) {
        try {
            //console.log(string)
            //console.log(string.split(", "));
            return string.split(", ");
        }catch(e) {
            return ""
        }
    }

    $scope.getContact = function ( contact ) {

        
        try{
            var label = '';
            label += contact.contatto.contatto;
            label += contact.note && contact.note != '' ? " (" + contact.note + ")" : ""
            return label
        } catch(e) {
            console.error(e);
            return ""
        }
    }

    $scope.getMeta = function ( metas ) {
        
        var els = [];
        if(metas) {
            var json_metas = JSON.parse(metas);
            for(var n = 0; n < selfScope.meta_keys.length; n++){

                var chiave = Object.keys(selfScope.meta_keys[n])[0];
                if(json_metas[chiave]) {
                    els.push([selfScope.meta_keys[n][chiave]] + ": "+json_metas[chiave]);
                }
            }
        }
        
        return els;
    }

    $scope.getSpecializzazioni = function ( specializzazioni ) {
        let s = []; 
        angular.forEach(specializzazioni, function(value, key) {
          s.push(value.descrizione);
        });
        return s.join("; ");
    }
    
    
    uiGmapGoogleMapApi.then(function(maps) { 
        //console.log(maps);        
    });

});
