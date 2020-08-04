//const STATUS_DUPLICATED = -1;
//const STATUS_ADDED = 0;
//const STATUS_READY = 1;
//const STATUS_SENT = 2;
//const STATUS_RECEIVED = 3;
//const STATUS_REFUSED = 4;
//const INVALID_CONTACT = 5;
//const STATUS_NOT_SENT = 6;
//const STATUS_NO_FEEDBACK = 7;

var filterMultiSelect = function (searchTerm, cellValue){
    
    if(searchTerm.length == 0) return true;
      var terms = [];
  for(var n = 0; n < searchTerm.length; n++){
      terms.push(searchTerm[n].value);
  }
  return terms.indexOf(cellValue) != -1 ? true : false;
};

var filterMultiSelectSpecializzazioni = function (searchTerm, cellValue){
    //console.log('s', searchTerm, cellValue)
    if(searchTerm.length == 0) return true;
    // se sta filtrando e non ne ha torna falso
    if(cellValue.length == 0) return false;
    
    
    var valid = true;
    var terms = searchTerm.map(function(t){ return t.value});
    
    var values = cellValue.map(function(v){ return v.descrizione});
    
    terms.map(function(t){
        if(values.indexOf(t) == -1) valid = false;
    })

    return valid;
};

agGrid.initialiseAgGridWithAngular1(angular);

angular.module('AppRubrica', ['uiGmapgoogle-maps', 'region.helpers','agGrid',
    'ui.grid','ui.grid.resizeColumns', 'ui.grid.moveColumns', 'ui.grid.selection', 'ui.grid.exporter', 'ui.grid.expandable', /*'ui.grid.grouping', 'ui.grid.treeView'*/])
.config(function(uiGmapGoogleMapApiProvider, appConfig) {
    uiGmapGoogleMapApiProvider.configure({
        key: google_map_key,
        v: appConfig.MAP.version,
        libraries: 'weather,geometry,visualization'
    });
})
.directive('file', function () {
    return {
        scope: {
            file: '='
        },
        link: function (scope, el, attrs) {
            el.bind('change', function (event) {
                var file = event.target.files[0];
                scope.file = file ? file : undefined;
                scope.$apply();
            });
        }
    };
})
.factory('MasService', ['$http', '$location', '$httpParamSerializer', function( $http, $location, $httpParamSerializer ) {

    var MasService = {
        
    };

    MasService.sendInvioToMas = function ( data = {} ){
        
        var formData = new FormData();
        Object.keys(data).map( function(key)  {
            //console.log('appendo', key, data[key])
            formData.append(key, data[key]);
            return key;
        });


        return $http({
            url: siteurl+'/mas/send-to-mas',
            data: formData,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity,
            method: "POST"
        });
        
    };

    MasService.updateMessageChannels = function ( data = {} ){
        
        var formData = new FormData();
        Object.keys(data).map( function(key)  {

            formData.append(key, data[key]);
            return key;
        });

        return $http({
            url: siteurl+'/mas/update-message-channels',
            data: formData,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity,
            method: "POST"
        });
        
    };

    MasService.resend = function ( message ){
        
        return $http({
            url: siteurl+'/mas/resend?id_message='+message,
            method: "GET"
        });
        
    };

    MasService.resetInvio = function ( id_invio ){
        
        return $http({
            url: siteurl+'/mas/reset-invio?id_invio='+id_invio,
            method: "GET"
        });
        
    };

    MasService.send = function ( data = {} ){


        var formData = new FormData();
        Object.keys(data).map( function(key) {
            
            if(Array.isArray(data[key])) {
                for(var n = 0; n < data[key].length; n++) {
                    formData.append(""+key+"["+n+"]", data[key][n]);
                }
            } else {
                formData.append(key, data[key]);
            }

            return key;
        });
        

        return $http({
            url: siteurl+'/mas/send',
            data: formData,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity,
            method: "POST"
        })
        
    };

    return MasService;

}])
.factory('AllertaMeteoService', ['$http', '$location', '$httpParamSerializer', function( $http, $location, $httpParamSerializer ) {

    var AllertaMeteoService = {
        
    };

    AllertaMeteoService.getRubrica = function ( obj = {} ){

        let q_string = $httpParamSerializer(obj);

        return $http({
            url: siteurl+'/mas/rubrica-list-service?'+q_string,
            method: "GET"
        });
        
    };

    AllertaMeteoService.getGruppi = function ( obj = {} ){

        let q_string = $httpParamSerializer(obj);

        return $http({
            url: siteurl+'/mas/gruppi-list-service?'+q_string,
            method: "GET"
        });
        
    };


    AllertaMeteoService.send = function ( data = {} ){


        var formData = new FormData();
        Object.keys(data).map( function(key) {
            if(Array.isArray(data[key])) {
                for(var n = 0; n < data[key].length; n++) {
                    formData.append(""+key+"["+n+"]", data[key][n]);
                }
            } else {
                formData.append(key, data[key]);
            }
            return key;
        });



        return $http({
            url: siteurl+'/allerta-meteo/send-allerta',
            data: formData,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity,
            method: "POST"
        })
        
    };

    AllertaMeteoService.addContactsToInvio = function ( data = {} ){
        var formData = new FormData();
        Object.keys(data).map( function(key) {
            
            formData.append(key, data[key]);
            return key;
        });

        
        return $http({
            url: siteurl+'/mas/add-destinatari-to-invio',
            data: formData,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity,
            method: "POST"
        })
        
    };

    AllertaMeteoService.addGroupsToInvio = function ( data = {} ){
        
        var formData = new FormData();
        Object.keys(data).map( function(key)  {
            
            formData.append(key, data[key]);
            return key;
        });
        
        
        return $http({
            url: siteurl+'/mas/add-gruppi-to-invio',
            data: formData,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity,
            method: "POST"
        })
        
    };

    return AllertaMeteoService;

}])
.factory('TemplateService', ['$http', '$location', '$httpParamSerializer', function( $http, $location, $httpParamSerializer ) {

    var TemplateService = {
    };

    TemplateService.getTemplate = function ( id ){
        return $http({
            url: siteurl+'/mas/template-preview?id='+id,
            method: "GET"
        });
    };

    return TemplateService;

}])
.factory('RubricaGroupService', ['$http', '$location', '$httpParamSerializer', function( $http, $location, $httpParamSerializer ) {

    var RubricaGroupService = {
    };

    RubricaGroupService.getContacts = function ( id ){
        return $http({
            url: siteurl+'/rubrica-group/load-rubrica-group?id='+id,
            method: "GET"
        });
    };

    RubricaGroupService.addContact = function ( id_group, contact, csrf ){

        
        var formData = new FormData();

        formData.append('id_gruppo', id_group);
        formData.append('id_univoco', contact);
        formData.append('_csrf-backend', csrf);
        

        return $http({
            url: siteurl+'/rubrica-group/add-single-contact',
            data: formData,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity,
            method: "POST"
        });

    };

    RubricaGroupService.removeContact = function ( id_group, contact, csrf ){
        var formData = new FormData();

        formData.append('id_gruppo', id_group);
        formData.append('id_univoco', contact);
        formData.append('_csrf-backend', csrf);
        

        return $http({
            url: siteurl+'/rubrica-group/remove-single-contact',
            data: formData,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity,
            method: "POST"
        });
    };

    RubricaGroupService.editMultiple = function ( id_group, action, contacts, csrf ){
        
        var formData = new FormData();
        var c = (contacts == 'all') ? 'all' :  JSON.stringify(contacts)
        formData.append('action', action);
        formData.append('id_gruppo', id_group);
        formData.append('contacts', c);
        formData.append('_csrf-backend', csrf);
        

        return $http({
            url: siteurl+'/rubrica-group/edit-multiple-contacts',
            data: formData,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity,
            method: "POST"
        });
    };

    return RubricaGroupService;

}])
.controller("RubricaController", function($scope, 
    uiGridConstants, uiGridExporterConstants,/* uiGridTreeBaseConstants, uiGridGroupingConstants,*/
    AllertaMeteoService) {

    // se c'è un parente glielo metto come figlio in modo da accedere alle proprietò
    var parentScope = $scope.$parent;
    if(parentScope && parentScope.child && parentScope.child.RubricaController) parentScope.child.RubricaController = $scope;
    
    $scope.exportRubrica = function() {
        //console.log($scope.gridContactsApi.grid)
        var to_exp = ($scope.gridContactsApi.selection.getSelectedGridRows().length > 0) ? uiGridExporterConstants.SELECTED : uiGridExporterConstants.ALL;
        $scope.gridContactsApi.exporter.csvExport( to_exp, uiGridExporterConstants.ALL )
        //$scope.gridRubricaOptions.api.exportDataAsCsv();
    }

    $scope.exportGruppi = function() {
        var to_exp = ($scope.gridGroupsApi.selection.getSelectedGridRows().length > 0) ? uiGridExporterConstants.SELECTED : uiGridExporterConstants.ALL;
        $scope.gridGroupsApi.exporter.csvExport( to_exp, uiGridExporterConstants.ALL )
        //$scope.gridGruppiOptions.api.exportDataAsCsv();
    }

    function onlyUnique(value, index, self) { 
        return self.indexOf(value) === index;
    }

    $scope.getAllContacts = function() {
        return $scope.gridContactsApi.core.getVisibleRows($scope.gridContactsApi.grid)
    }

    $scope.getAllGroups = function() {
        return $scope.gridGroupsApi.core.getVisibleRows($scope.gridGroupsApi.grid)
    }

    $scope.selectRow = function(rowEntity) {
        $scope.gridContactsApi.selection.selectRow(rowEntity)
    }

    $scope.unSelectRow = function(rowEntity) {
        $scope.gridContactsApi.selection.unSelectRow(rowEntity)
    }

    $scope.unselectAll = function() {
        $scope.gridContactsApi.selection.clearSelectedRows()
    }

    $scope.getSelectedContacts = function() {
        if($scope.gridContactsApi.selection.getSelectAllState()&&
            $scope.gridContactsApi.selection.getSelectedGridRows().length == $scope.uiContactsGrid.data.length
            ) return "all";

        var ret_array = [];
        $scope.gridContactsApi.selection.getSelectedGridRows().map( function(selected) {
            ret_array.push( selected.entity.id_riferimento + "|" + selected.entity.tipo_riferimento );
        } )

        return ret_array.filter( onlyUnique );
        
    }


    $scope.getSelectedGroups = function() {
        if($scope.gridGroupsApi.selection.getSelectAllState() &&
            $scope.gridGroupsApi.selection.getSelectedGridRows().length == $scope.uiGroupsGrid.data.length
            ) return "all";

        var ret_array = [];
        $scope.gridGroupsApi.selection.getSelectedGridRows().map( function(selected) {
            ret_array.push( selected.entity.id );
        } )

        return ret_array;
    }

    var customAggregationDisplayValue = function( aggregation, fieldValue, numValue, row ) {
        aggregation.value = fieldValue
    }

    function selectChildren(gridRows, selected) {
        if (gridRows && gridRows.length > 0) {
          gridRows.forEach(function(child) {
            if (selected) {
              //console.log(child.entity);
              $scope.gridContactsApi.selection.selectRow(child.entity);
            } else {
              $scope.gridContactsApi.selection.unSelectRow(child.entity);
            }

            var children = $scope.gridContactsApi.treeBase.getRowChildren(child);
            selectChildren(children, selected); //recursively select/de-select children
          });
        }
      }


    $scope.setNoSelectionAvaible = function() {
        //$scope.uiContactsGrid.enableRowHeaderSelection = false;
    }

    $scope.lang = 'it'
    $scope.uiContactsGrid = {
        enableFiltering: true,
        enableSorting: true,
        //enableGridMenu: true,
        exporterMenuExcel: false,
        exporterMenuPdf: false,
        lang: 'it',
        multiSelect: true,
        enableRowSelection: false,
        enableSelectAll: true,
        enableRowHeaderSelection: true,
        selectionRowHeaderWidth: 35,
        rowHeight: 35,
        showGridFooter:true,
        columnDefs: [
            {name: 'Num. regionale', field: 'num_elenco_territoriale', defaultSort: { direction: uiGridConstants.ASC } },
            {name: 'Riferimento', field: 'valore_riferimento'},
            {name: 'Indirizzo', field: 'indirizzo'},
            {name: 'Comune', field: 'comune'},
            {name: 'PR', field: 'provincia'},
            {name: 'Tipo riferimento', field: 'tipologia_riferimento'},
            {name: 'Gruppi', field: 'gruppi', cellTemplate: '<div class="ui-grid-cell-contents">{{row.entity.gruppi.join(", ")}}</div>'},
            {name: 'Zone allerta', field: 'zone_allerta', cellTemplate: '<div class="ui-grid-cell-contents">{{row.entity.zone_allerta.join(", ")}}</div>'}
        ],
        data: {},
        onRegisterApi: function( gridApi ) {
            $scope.gridContactsApi = gridApi;
            // evento alla selezione di un contatto
            // preve che il parent abbia una funzione onContactSelectionChange nello scope
            $scope.gridContactsApi.selection.on.rowSelectionChanged($scope, function() {
                if($scope.$parent.onContactSelectionChange) $scope.$parent.onContactSelectionChange()
            });
        }
    }


    $scope.uiGroupsGrid = {
        enableFiltering: true,
        enableSorting: true,
        //enableGridMenu: true,
        exporterMenuExcel: false,
        exporterMenuPdf: false,
        lang: 'it',
        multiSelect: true,
        enableRowSelection: true,
        enableSelectAll: false,
        selectionRowHeaderWidth: 35,
        rowHeight: 35,
        showGridFooter:true,
        columnDefs: [
            {name: 'ID', field: 'id'},
            {name: 'Nome gruppo', field: 'name', defaultSort: { direction: uiGridConstants.ASC }}            
        ],
        data: {},
        onRegisterApi: function( gridApi ) {
            $scope.gridGroupsApi = gridApi;
            // evento alla selezione di un gruppo
            // preve che il parent abbia una funzione onGroupSelectionChange nello scope
            $scope.gridGroupsApi.selection.on.rowSelectionChanged($scope, function( scope, row ) {
                if($scope.$parent.onGroupSelectionChange) $scope.$parent.onGroupSelectionChange(scope, row)
            });
        }
    }

    

    AllertaMeteoService.getRubrica().then(function(res)  {
        // Trasformo zone di allerta e gruppi come array
        var formatted_data = res.data.map(function (contact) {
            try {
                contact.gruppi = (contact.gruppi) ? JSON.parse(contact.gruppi) : [];
                contact.zone_allerta = contact.zone_allerta.split(",");
            } catch(e) {
                contact.gruppi = [];
            }
            return contact;
        }); 
        $scope.uiContactsGrid.data = formatted_data;

        // evento del genitore
        // se presente gli notifico che i contatti sono stati caricati
        // utile per la selezione con le zone di allerta al primo caricamento
        setTimeout(function(){
            if($scope.$parent.onContactsLoaded) $scope.$parent.onContactsLoaded(); 
        }, 10);
        
    });    

    AllertaMeteoService.getGruppi().then(function(res)  {
        $scope.uiGroupsGrid.data = res.data;
        // evento del genitore
        // se presente gli notifico che i gruppi sono stati caricati
        setTimeout(function(){
            if($scope.$parent.onGroupsLoaded) $scope.$parent.onGroupsLoaded(); 
        }, 10);
    });    

    $scope.current_view = 'contatti';
    $scope.setView = function(v) {
        $scope.current_view = v;
    }

})
.controller("GruppoRubricaController", function($scope, 
    uiGridConstants, uiGridExporterConstants,/* uiGridTreeBaseConstants, uiGridGroupingConstants,*/
    RubricaGroupService) {

    // se c'è un parente glielo metto come figlio in modo da accedere alle proprietò
    var parentScope = $scope.$parent;
    if(parentScope && parentScope.child && parentScope.child.RubricaController) parentScope.child.RubricaController = $scope;
    
    $scope.exportRubrica = function() {
        
        var to_exp = ($scope.gridContactsApi.selection.getSelectedGridRows().length > 0) ? uiGridExporterConstants.SELECTED : uiGridExporterConstants.ALL;
        $scope.gridContactsApi.exporter.csvExport( to_exp, uiGridExporterConstants.ALL )
        
    }



    function onlyUnique(value, index, self) { 
        return self.indexOf(value) === index;
    }


    $scope.getSelectedContacts = function(inserted = false) {
        
        var ret_array = [];
        $scope.gridContactsApi.selection.getSelectedGridRows().map( function(selected) {
            if(selected.entity.inserted == inserted) ret_array.push( selected.entity.id_univoco );
        } )

        if($scope.uiContactsGrid.data.length == ret_array.length) {
            return "all";
        }

        return ret_array.filter( onlyUnique );
        
    }

    var customAggregationDisplayValue = function( aggregation, fieldValue, numValue, row ) {
        aggregation.value = fieldValue
    }

    function selectChildren(gridRows, selected) {
        if (gridRows && gridRows.length > 0) {
          gridRows.forEach(function(child) {
            if (selected) {
              //console.log(child.entity);
              $scope.gridContactsApi.selection.selectRow(child.entity);
            } else {
              $scope.gridContactsApi.selection.unSelectRow(child.entity);
            }

            var children = $scope.gridContactsApi.treeBase.getRowChildren(child);
            selectChildren(children, selected); //recursively select/de-select children
          });
        }
      }

    $scope.lang = 'it'
    $scope.uiContactsGrid = {
        enableFiltering: true,
        minRowsToShow: 23,
        enableSorting: true,
        exporterMenuExcel: false,
        exporterMenuPdf: false,
        lang: 'it',
        multiSelect: true,
        enableRowSelection: false,
        enableSelectAll: true,
        enableRowHeaderSelection: true,
        selectionRowHeaderWidth: 35,
        rowHeight: 35,
        showGridFooter:true,
        columnDefs: [
            {name: 'Inserito', field: 'inserted', cellTemplate: 'presentTemplate.html', width: 100, 
                filter: {
                  type: uiGridConstants.filter.SELECT,
                  condition: uiGridConstants.filter.CONTAINS,
                  selectOptions: [ { value: true, label: 'Si' }, { value: false, label: 'No' } ]
                }
            },
            {name: 'Num. regionale', field: 'num_elenco_territoriale', defaultSort: { direction: uiGridConstants.ASC }},
            {name: 'Riferimento', field: 'valore_riferimento' ,/*customTreeAggregationFn: customAggregationDisplayValue*/},
            {name: 'Indirizzo', field: 'indirizzo', /*customTreeAggregationFn: customAggregationDisplayValue,*/},
            {name: 'Comune', field: 'comune', 
            filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div multiple-select-dropdown></div></div>',
            filter: {
              type: uiGridConstants.filter.SELECT,
              condition: filterMultiSelect,
              selectOptions: []
            }},
            {name: 'Provincia', field: 'provincia', 
            filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div multiple-select-dropdown></div></div>',
            filter: {
              type: uiGridConstants.filter.SELECT,
              condition: filterMultiSelect,
              selectOptions: []
            }},
            {name: 'Tipo riferimento', field: 'tipologia_riferimento', 
            filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div multiple-select-dropdown></div></div>',
            filter: {
              type: uiGridConstants.filter.SELECT,
              condition: filterMultiSelect,
              selectOptions: [

              ]
            }},
            {name: 'Specializzazioni', field: 'specializzazioni', 
            cellFilter: 'specializzazioni',
            filterHeaderTemplate: '<div class="ui-grid-filter-container" ng-repeat="colFilter in col.filters"><div multiple-select-dropdown></div></div>',
            filter: {
              type: uiGridConstants.filter.SELECT,
              condition: filterMultiSelectSpecializzazioni,
              selectOptions: [

              ]
            }},
            {name: 'Azioni', field: 'id_univoco', cellTemplate: 'actionsTemplate.html'}
        ],
        data: {},
        onRegisterApi: function( gridApi ) {
            $scope.gridContactsApi = gridApi;
        }
    }

    $scope.id_gruppo = null;
    $scope.csrf = null;
    $scope.loadContacts = function(group, csrf, tipi_riferimento) {
        $scope.id_gruppo = group;
        $scope.csrf = csrf;

        
        $scope.uiContactsGrid.columnDefs[6].filter.selectOptions = tipi_riferimento.sort(function(a,b){
            return a>b ? 1 : -1;
        }).map(function(obj) { 
            var rObj = {'value': obj, 'label': obj};
            return rObj;
          });


        $scope.loadData();
    }

    $scope.calling = false;

    $scope.comuni = [];
    $scope.province = [];
    $scope.specializzazioni = [];

    $scope.loadData = function() {
        if($scope.calling) return;
        $scope.calling = true;
        var comuni = [];
        var province = [];
        var specializzazioni = [];
        var tipologia_riferimento = [];
        RubricaGroupService.getContacts($scope.id_gruppo).then(function(res){
            $scope.uiContactsGrid.data = res.data; 
            
            for(var i = 0; i < res.data.length; i++) {
                if(comuni.indexOf(res.data[i].comune) == -1 && res.data[i].comune && res.data[i].comune != '') comuni.push(res.data[i].comune);
                if(province.indexOf(res.data[i].provincia) == -1 && res.data[i].provincia && res.data[i].provincia != '') province.push(res.data[i].provincia);
                if(tipologia_riferimento.indexOf(res.data[i].tipologia_riferimento) == -1 && res.data[i].tipologia_riferimento && res.data[i].tipologia_riferimento != '') tipologia_riferimento.push(res.data[i].tipologia_riferimento);
                for(var n = 0; n < res.data[i].specializzazioni.length; n++) {
                    if(specializzazioni.indexOf(res.data[i].specializzazioni[n].descrizione) == -1) specializzazioni.push(res.data[i].specializzazioni[n].descrizione);
                }
            }
            comuni = comuni.sort(function(a,b){ return a > b ? 1 : -1; });
            province = province.sort(function(a,b){ return a > b ? 1 : -1; });
            specializzazioni = specializzazioni.sort(function(a,b){ return a > b ? 1 : -1; });

            
            $scope.uiContactsGrid.columnDefs[4].filter.selectOptions = comuni.map(function(obj) { 
                var rObj = {'value': obj, 'label': obj};
                return rObj;
              });

            $scope.uiContactsGrid.columnDefs[5].filter.selectOptions = province.map(function(obj) { 
                var rObj = {'value': obj, 'label': obj};
                return rObj;
              });

            $scope.uiContactsGrid.columnDefs[6].filter.selectOptions = tipologia_riferimento.map(function(obj) { 
                var rObj = {'value': obj, 'label': obj};
                return rObj;
              });

            $scope.uiContactsGrid.columnDefs[7].filter.selectOptions = specializzazioni.map(function(obj) { 
                var rObj = {'value': obj, 'label': obj};
                return rObj;
              });
            
            $scope.calling = false;
        }).catch(function(err){
            //console.log('err', err)
            $scope.calling = false;
        })
    }

    $scope.addContact = function(rowEntity, id) {

        if($scope.calling) return;
        $scope.calling = true;
        RubricaGroupService.addContact( $scope.id_gruppo, id, $scope.csrf ).then(function(res){
            rowEntity.inserted = true;
            $scope.calling = false;
        }).catch(function(err){

            $scope.calling = false;
        })
    }

    $scope.removeContact = function(rowEntity, id) {

        if($scope.calling) return;
        $scope.calling = true;
        RubricaGroupService.addContact( $scope.id_gruppo, id, $scope.csrf ).then(function(res){
            rowEntity.inserted = false;
            $scope.calling = false;
        }).catch(function(err){

            $scope.calling = false;
        })
    }
    

    $scope.addMultiple = function () {
        if($scope.calling) return;
        $scope.calling = true;
        RubricaGroupService.editMultiple( $scope.id_gruppo, 'add', $scope.getSelectedContacts(false), $scope.csrf ).then(function(res){
            $scope.calling = false;
            $scope.loadData();
            $scope.gridContactsApi.selection.clearSelectedRows();
            
        }).catch(function(err){
            //console.log('err',err)
            $scope.calling = false;
        })
    }

    $scope.removeMultiple = function () {
        if($scope.calling) return;
        $scope.calling = true;
        RubricaGroupService.editMultiple( $scope.id_gruppo, 'remove', $scope.getSelectedContacts(true), $scope.csrf ).then(function(res){
            $scope.calling = false; 
            $scope.loadData();
            $scope.gridContactsApi.selection.clearSelectedRows();
            
        }).catch(function(err){
            //console.log('err',err)
            $scope.calling = false;
        })
    }

})

.directive('multipleSelectDropdown', function() {
  return {
    template: '<select multiple="true" class="ui-grid-filter-select ui-grid-filter-input-0" ng-model="colFilter.term" ng-options="option as option.value for option in colFilter.selectOptions"></select>'
  };
})


.controller("TemplatePreviewController", function($scope, $sce, $location, TemplateService) {
    // necessario per prendere i contatti dalla rubrica
    
    var selfScope = this;

    var parentScope = $scope.$parent;
    if(parentScope.child.TemplatePreviewController) parentScope.child.TemplatePreviewController = $scope;

    var replaceForBody = function (channel) {
        switch(channel) {
            case 'mail_text': return 'mail_body'; break;
            case 'fax_text': return 'fax_body'; break;
            case 'push_text': return 'push_body'; break;
            case 'sms_text': return 'sms_body'; break;
        }
    }

    $scope.template_content = {};
    $scope.preview = '';
    $scope.channel = 'mail_text';
    $scope.data = {
        message: '',
        data_allerta: ''
    }

    $scope.loadTemplate = function( template ){
        TemplateService.getTemplate(template).then(function(res) {
            //console.log('template', res);
            $scope.template_content = res.data;
            $scope.updatePreview( ) 
        });    
    }    

    $scope.replaceChannelName = function() {
        switch($scope.channel) {
            case 'mail_text': return 'Mail/Pec'; break;
            case 'fax_text': return 'Fax'; break;
            case 'push_text': return 'Push notification'; break;
            case 'sms_text': return 'Sms'; break;
        }
    }

    $scope.updatePreview = function ( ) {
        var content = $scope.template_content[replaceForBody($scope.channel)];
        var data_allerta = $scope.$parent.getDataAllerta();
        var message = selfScope[$scope.channel];
        
        if(content) {
            content = content.replace(/{{message}}/g, message);
            content = content.replace(/{{data_allerta}}/g, data_allerta);
        } else {
            content = (message) ? message : '<p></p>'
        }
        
        $scope.preview = $sce.trustAsHtml( content );
    }

    $scope.validMessage = function( channels ) {

        var _return = {
            valid: true,
            message: ''
        }

        if ( channels.mail === 1 || channels.pec === 1 ) {
            $scope.channel = 'mail_text';
            var content = $scope.template_content[replaceForBody($scope.channel)];
            var data_allerta = $scope.$parent.getDataAllerta();
            var message = selfScope[$scope.channel];
            
            if(content) {
                content = content.replace(/{{message}}/g, message);
                content = content.replace(/{{data_allerta}}/g, data_allerta);
            } else {
                content = (message) ? message : null
            }
        
            if(!content) return {
                valid: false,
                message: 'Inserisci il testo per il canale mail'
            }
        }

        content = null;

        if ( channels.sms === 1 ) {
            $scope.channel = 'sms_text';
            var content = $scope.template_content[replaceForBody($scope.channel)];
            var data_allerta = $scope.$parent.getDataAllerta();
            var message = selfScope[$scope.channel];
            
            if(content) {
                content = content.replace(/{{message}}/g, message);
                content = content.replace(/{{data_allerta}}/g, data_allerta);
            } else {
                content = (message) ? message : null
            }
            
            if(!content) return {
                valid: false,
                message: 'Inserisci il testo per il canale sms'
            }
        }

        content = null;
        if ( channels.fax === 1 ) {
            $scope.channel = 'fax_text';
            var content = $scope.template_content[replaceForBody($scope.channel)];
            var data_allerta = $scope.$parent.getDataAllerta();
            var message = selfScope[$scope.channel];
            
            if(content) {
                content = content.replace(/{{message}}/g, message);
                content = content.replace(/{{data_allerta}}/g, data_allerta);
            } else {
                content = (message) ? message : null
            }
            
            if(!content) return {
                valid: false,
                message: 'Inserisci il testo per il canale fax'
            }
        }

        content = null;
        if ( channels.push === 1 ) {
            $scope.channel = 'push_text';
            var content = $scope.template_content[replaceForBody($scope.channel)];
            var data_allerta = $scope.$parent.getDataAllerta();
            var message = selfScope[$scope.channel];
            
            if(content) {
                content = content.replace(/{{message}}/g, message);
                content = content.replace(/{{data_allerta}}/g, data_allerta);
            } else {
                content = (message) ? message : null
            }
            
            if(!content) return {
                valid: false,
                message: 'Inserisci il testo per il canale push'
            }
        }

        return _return;
    }

    $scope.changeData = function(data) {
        $scope.data = data
    }

    $scope.setChannel = function($channel) {
        
        setTimeout(function(){

            var el = $location.hash();
            $scope.channel = el.replace("#","");
            $scope.updatePreview()
        }, 10)
        
    }

    

})
.controller("CreaAllertaMeteoController", function($scope, AllertaMeteoService, MasService, uiGmapGoogleMapApi, appConfig) {
    // necessario per prendere i contatti dalla rubrica
    $scope.child = {
        RubricaController: {},
        TemplatePreviewController: {}
    }
    var selfScope = this;

    $scope.contacts = ""
    $scope.groups = ""

    $scope.getDataAllerta = function () {
        return selfScope.data_allerta;
    }

    //$scope.template = null;

    $scope.changedTamplate = function() {
        $scope.child.TemplatePreviewController.loadTemplate( selfScope.template );
    }

    $scope.submitForm = function() {
        //console.log('form', $scope.allertaMeteoForm)
    }

    $scope.send_valid = false;


    addFormContacts = function() {
        //console.log('cc',$scope.child.RubricaController.getSelectedContacts())
        return $scope.child.RubricaController.getSelectedContacts()
    }

    addFormGroups = function() {
        return $scope.child.RubricaController.getSelectedGroups()
    }

    $scope.redirect_url = null;
    $scope.id_invio = null;

    /**
     * Verifica eventi
     * @type {Number}
     */
    $scope.to_add_contacts = 0;
    $scope.to_add_groups = 0;

    /**
     * Numero massimo di contatti da inviare per volta
     * @type {Number}
     */
    var max_n_c = 200;
    
    $scope.block_form = false;
    $scope.csrf = null;

    $scope.spin_message = 'Salvo il messaggio'

    
    $scope.sendForm = {
        submit : function(form, e) {
            
            e.preventDefault();
            try {
                if(!$scope.block_form) {
                    $scope.block_form = true;

                    var valid_template = $scope.child.TemplatePreviewController.validMessage({
                        mail: selfScope.channel_mail,
                        pec: selfScope.channel_pec,
                        fax: selfScope.channel_fax,
                        sms: selfScope.channel_sms,
                        push: selfScope.channel_push
                    });
                    if(!valid_template.valid) {
                        alert(valid_template.message/*'Inserisci un messaggio, anche un semplice carattere, per ogni canale'*/);
                        $scope.block_form = false;
                        return;
                    }


                    //console.log('submit del form', $scope.sendAllertaForm);
                    var data = {};
                    angular.element(form.$$element[0]).serializeArray().forEach( function(pair) {
                        //console.log(pair);
                        data[pair.name] = pair.value                    
                    });

                    $scope.csrf = data['_csrf-backend'];
                    
                    data['AlmAllertaMeteo[mediaFile]'] = $scope.mediaFile;
                    

                    var contacts = addFormContacts();
                    // i gruppi non ci servono più
                    var groups = [];//addFormGroups();

                    
                    if(Array.isArray(contacts) && contacts.length == 0 &&
                        Array.isArray(groups) && groups.length == 0) {
                        $scope.block_form = false;
                        alert('Seleziona contatti');
                        return;
                    }

                    AllertaMeteoService.send( data ).then( (res) => {
                        //console.log('res', res)
                        if(res.data.id_invio && res.data.redirect_url){

                            $scope.id_invio = res.data.id_invio;
                            $scope.redirect_url = res.data.redirect_url;

                            if(Array.isArray(contacts)) $scope.to_add_contacts = contacts.length;
                            if(Array.isArray(groups)) $scope.to_add_groups = groups.length;
                            
                            linkContacts(contacts);
                            // usando solo i check non abbiamo più bisogno dei gruppi
                            //linkGroups(groups)


                        } else {
                            $scope.block_form = false;
                            
                            if(res.data.error) alert(res.data.error)
                                else alert('Errore creazione invio')
                        }
                    }).catch(function(err) {
                        console.log(err);
                        resetAll()
                    })
                    
                }
            } catch(e) {
                //console.log(e)
                $scope.block_form = false;
            }
            
        }
    }

    $scope.added_contacts = 0;
    $scope.real_added_contacts = 0;
    $scope.added_groups = 0;

    $scope.errors = [];

    $scope.zone_allerta = [];
    $scope.groups = [];

    $scope.addInitialZone = function(z) {
        $scope.zone_allerta.push(z);
    }

    /**
     * Zone cambiate modifica la selezione
     * @param  {[type]} zone  [description]
     * @param  {[type]} value [description]
     * @return {[type]}       [description]
     */
    $scope.changedZone = function(zone, value) {
        $scope.updatedSelection();
    }

    $scope.onContactsLoaded = function() {
        $scope.updatedSelection();
    }

    // se presente questa funzione viene chiamata ogni volta che viene selezionato un gruppo
    $scope.onGroupSelectionChange = function(scope, row) {
        
        var selected = $scope.child.RubricaController.getSelectedGroups();
        if(selected == 'all') {
            var groups = $scope.child.RubricaController.getAllGroups();
            var to_add_groups = [];
            groups.map(function(g) {
                to_add_groups.push(g.entity.id);
            })
            $scope.groups = to_add_groups;
        } else {
            $scope.groups = selected;
        }

        $scope.updatedSelection();
    }

    $scope.updatedSelection = function() {
        
        if($scope.child.RubricaController && $scope.child.RubricaController.getAllContacts) {
        
            let rows = $scope.child.RubricaController.getAllContacts();
        
            // deseleziona tutte le righe prima
            $scope.child.RubricaController.unselectAll();

            // ora in base a zone e gruppi selezionale
            rows.map(function(row) {
                var ok = false;
                row.entity.zone_allerta.map(function(z){
                    if(selfScope.zona_allerta[z] == 1) ok = true;
                });

                if( row.entity.zone_allerta.length == 0 || 
                    !row.entity.zone_allerta ||
                    (row.entity.zone_allerta.length == 1 && row.entity.zone_allerta[0] == '' )
                        ) ok = true;

                if(!ok) {
                    row.entity.gruppi.map(function(g){
                        if($scope.groups.indexOf(g) != -1) ok = true;
                    });
                } 

                if(ok) $scope.child.RubricaController.selectRow(row.entity);
            });
        } else {
            console.log('no update selezione')
        }
    }

    linkContacts = function(contacts) {
        
        if(Array.isArray(contacts)) {

            
            if($scope.to_add_contacts > 0) {
                $scope.spin_message = 'Aggiungo ' + $scope.to_add_contacts + ' contatti';
                var arrays = [];
                for(n = 0; n < $scope.to_add_contacts; n+=max_n_c) {
                    arrays.push(contacts.slice(n, (n + max_n_c) ));
                }

                arrays.map( function(to_send, i, a) {
                    let n_ = to_send.length;
                    let add = to_send.join(',');                    
                    if(add && n_ > 0) {
                        setTimeout(function(){
                            AllertaMeteoService.addContactsToInvio({
                                id_invio: $scope.id_invio,
                                contacts: add,
                                '_csrf-backend': $scope.csrf
                            }).then((res)=>{
                                $scope.added_contacts += n_;
                                
                                if(res.data.message && res.data.message == 'ok') {
                                    //console.log('aggiunti contatti', n_);
                                    $scope.real_added_contacts += n_;
                                    $scope.spin_message = 'Aggiunti ' + ($scope.to_add_contacts - $scope.real_added_contacts) + ' contatti';
                                    $scope.$broadcast('update_send')  
                                } else {
                                    errors.push("Errore nell'inserimento di " + n_ + " contatti");
                                    $scope.$broadcast('update_send')  
                                }

                            }).catch((e)=>{
                                $scope.can_reset = true;
                            })
                        }, i*1000)
                        
                    }
                    return to_send;
                })
                    
            }
        } else {
            AllertaMeteoService.addContactsToInvio({
                id_invio: $scope.id_invio,
                contacts: "all",
                '_csrf-backend': $scope.csrf
            }).then(function(res){

                if(res.data.message && res.data.message == 'ok') {
                    //console.log('aggiunti tutti i contatti', res);
                    $scope.added_contacts = $scope.to_add_contacts;
                    $scope.spin_message = 'Aggiunti tutti i contatti';
                    $scope.$broadcast('update_send')     
                } else {
                    errors.push("Errore nell'inserimento dei contatti");
                    $scope.$broadcast('update_send')  
                }                   
            }).catch(function(e){
                $scope.can_reset = true;
            })
        }
    }

    // @deprecated
    linkGroups = function(groups) {
        
        if(Array.isArray(groups)) {
            //console.log('inserisco gruppi', groups);
            $scope.to_add_groups = groups.length;

            if($scope.to_add_groups > 0) {                
                AllertaMeteoService.addGroupsToInvio({
                    id_invio: $scope.id_invio,
                    groups: groups.join(','),
                    '_csrf-backend': $scope.csrf
                }).then(function(res) {
                    $scope.added_groups = $scope.to_add_groups;
                    if(res.data.message && res.data.message == 'ok') {
                        //console.log('added groups', res);
                        $scope.spin_message = 'Aggiunti ' + groups.length + ' gruppi';
                        $scope.$broadcast('update_send')  
                    } else {
                        errors.push("Errore nell'inserimento dei gruppi");
                        $scope.$broadcast('update_send')  
                    }  
                }).catch(function(e){
                    $scope.can_reset = true;
                })
            }
        } else {
            
            AllertaMeteoService.addGroupsToInvio({
                id_invio: $scope.id_invio,
                groups: "all",
                '_csrf-backend': $scope.csrf
            }).then(function(res) {
                $scope.added_groups = $scope.to_add_groups;
                if(res.data.message && res.data.message == 'ok') {
                    $scope.spin_message = 'Aggiunti tutti i gruppi';
                    $scope.$broadcast('update_send')
                } else {
                    errors.push("Errore nell'inserimento dei gruppi");
                    $scope.$broadcast('update_send')  
                } 
            }).catch(function(e) {
                $scope.can_reset = true;
            })
            
        }
    }

    

    verifySend = function() {
        if ( 
            $scope.to_add_contacts == $scope.added_contacts && 
            $scope.to_add_groups == $scope.added_groups 
        ) {
            createInvio();
        } 
    }


    createInvio = function() {
        $scope.spin_message = 'Invio al mas';  
        MasService.sendInvioToMas({
            id_invio: $scope.id_invio,
            '_csrf-backend': $scope.csrf
        }).then((res)=>{
            if(res.data.message && res.data.message == 'ok'){
                window.location = $scope.redirect_url;
            } else {
                if(res.data.error) alert(res.data.error);
                    else alert('errore nell\'invio, verrai comunque redirezionato alla lista degli invii')

                window.location = $scope.redirect_url;
            }
        });
    }

    $scope.$on( "update_send", verifySend );

    $scope.can_reset = false;

    $scope.resetAll = function() {
        if($scope.id_invio) {
            MasService.resetInvio( $scope.id_invio ).then(function(res){
                $scope.resetFinal();
            }).catch(function(err){
                console.log('err', err)
            })
        } else {
            $scope.resetFinal();
        }
        
    }

    $scope.resetFinal = function() {
        $scope.id_invio = null;
        /**
         * Verifica eventi
         * @type {Number}
         */
        $scope.to_add_contacts = 0;
        $scope.to_add_groups = 0;

        /**
         * Numero massimo di contatti da inviare per volta
         * @type {Number}
         */
        var max_n_c = 200;
        
        $scope.block_form = false;
        
        $scope.spin_message = 'Salvo il messaggio'
        $scope.added_contacts = 0;
        $scope.real_added_contacts = 0;
        $scope.added_groups = 0;

        $scope.can_reset = false;

        $scope.errors = [];
    }


    $scope.fileNameChanged = function (ele) {
      

        $scope.mediaFile = [];
        Object.keys(ele.files).map(function(k){
            $scope.mediaFile.push(ele.files[k]);
            return k;
        });
    }


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

    
    uiGmapGoogleMapApi.then(function(maps) { });

})

.controller("CreaMessaggioController", function($scope, AllertaMeteoService, MasService) {
    // necessario per prendere i contatti dalla rubrica
    $scope.child = {
        RubricaController: {},
        TemplatePreviewController: {}
    }
    var selfScope = this;

    $scope.contacts = ""
    $scope.groups = ""

    $scope.getDataAllerta = function () {
        return selfScope.data_allerta;
    }

    //$scope.template = null;

    $scope.changedTamplate = function() {
        
        $scope.child.TemplatePreviewController.loadTemplate( selfScope.template );
    }

    $scope.submitForm = function() {
        //console.log('form', $scope.allertaMeteoForm)
    }

    $scope.send_valid = false;
    $scope.groups = [];


    addFormContacts = function() {
        
        return $scope.child.RubricaController.getSelectedContacts()
    }

    addFormGroups = function() {
        return $scope.child.RubricaController.getSelectedGroups()
    }

    $scope.redirect_url = null;
    $scope.id_invio = null;

    /**
     * Verifica eventi
     * @type {Number}
     */
    $scope.to_add_contacts = 0;
    $scope.to_add_groups = 0;

    /**
     * Numero massimo di contatti da inviare per volta
     * @type {Number}
     */
    var max_n_c = 200;
    
    $scope.block_form = false;
    $scope.csrf = null;

    $scope.spin_message = 'Salvo il messaggio'

    // più complesso della selezione per le allerte meteo
    // se viene aggiunto un gruppo seleziona tutti i membri dello stesso
    // se viene tolto
    //     verifica che la riga non appartenga a altri gruppi selezionati
    //     in caso non appartenga a altri gruppi la deseleziona
    $scope.onGroupSelectionChange = function(scope, row) {
        
        let rows = $scope.child.RubricaController.getAllContacts();

        if($scope.groups.indexOf(scope.entity.id) == -1) {
            
            $scope.groups.push(scope.entity.id);
            
            // se l'entità appartiene al gruppo la seleziono
            rows.map(function(row_contact) {
                if( row_contact.entity.gruppi.indexOf(scope.entity.id) != -1 ) {
                    $scope.child.RubricaController.selectRow(row_contact.entity);
                }
            });

        } else {
            
            $scope.groups = $scope.groups.filter(function(g){
                return g != scope.entity.id;
            });
            
            rows.map(function(row_contact) {
                if( row_contact.entity.gruppi.indexOf(scope.entity.id) != -1 ) {
                    // apparteneva al gruppo deselezionato
                    // se non appartiene a altri gruppi selezionati la deseleziono (se era selezionata)
                    if(row_contact.isSelected) {
                        
                        var to_deselect = true;
                        $scope.groups.map(function(gr){
                            if(row_contact.entity.gruppi.indexOf(gr) != -1) to_deselect = false;
                        });

                        if(to_deselect) $scope.child.RubricaController.unSelectRow(row_contact.entity);
                        
                    }
                }
            });
        }
    }

    $scope.sendForm = {
        submit : function(form, e) {
            
            e.preventDefault();
            try {
                if(!$scope.block_form) {
                    $scope.block_form = true;
                    
                    var valid_template = $scope.child.TemplatePreviewController.validMessage({
                        mail: selfScope.channel_mail,
                        pec: selfScope.channel_pec,
                        fax: selfScope.channel_fax,
                        sms: selfScope.channel_sms,
                        push: selfScope.channel_push
                    });
                    if(!valid_template.valid) {
                        alert(valid_template.message/*'Inserisci un messaggio, anche un semplice carattere, per ogni canale'*/);
                        $scope.block_form = false;
                        return;
                    }


                    var data = {};
                    angular.element(form.$$element[0]).serializeArray().forEach( function(pair) {
                        data[pair.name] = pair.value                    
                    });

                    $scope.csrf = data['_csrf-backend'];
                    //console.log('csrf', $scope.csrf);
                    //console.log('data', data);
                    
                    if($scope.mediaFile && $scope.mediaFile.length > 0) {
                        data['MasMessage[mediaFile]'] = $scope.mediaFile;
                    }
                    
                    var contacts = addFormContacts();
                    // non usiamo più i gruppi
                    var groups = [];//addFormGroups();

                    
                    if(Array.isArray(contacts) && contacts.length == 0 &&
                        Array.isArray(groups) && groups.length == 0) {
                        $scope.block_form = false;
                        alert('Seleziona contatti');
                        return;
                    }

                    
                    MasService.send( data ).then( (res) => {
                        //console.log('res', res)
                        if(res.data.id_invio && res.data.redirect_url){

                            $scope.id_invio = res.data.id_invio;
                            $scope.redirect_url = res.data.redirect_url;

                            if(Array.isArray(contacts)) $scope.to_add_contacts = contacts.length;
                            if(Array.isArray(groups)) $scope.to_add_groups = groups.length;
                            
                            linkContacts(contacts)
                            // linkandoli direttamente non ne abbiamo più bisogno
                            //linkGroups(groups)


                        } else {
                            $scope.block_form = false;
                            if(res.data.error) alert(res.data.error)
                                else alert('Errore creazione invio')
                        }
                    }).catch(function(err) {
                        console.log(err);
                        $scope.resetAll()
                    })
                    
                }
            } catch(e) {
                console.log(e)
                $scope.block_form = false;
            }
            
        }
    }

    $scope.added_contacts = 0;
    $scope.real_added_contacts = 0;
    $scope.added_groups = 0;

    $scope.errors = [];
    

    linkContacts = function(contacts) {
        
        if(Array.isArray(contacts)) {

            
            if($scope.to_add_contacts > 0) {
                $scope.spin_message = 'Aggiungo ' + $scope.to_add_contacts + ' contatti';
                var arrays = [];
                for(n = 0; n < $scope.to_add_contacts; n+=max_n_c) {
                    arrays.push(contacts.slice(n, (n + max_n_c) ));
                }

                arrays.map( function(to_send, i, a) {
                    let n_ = to_send.length;
                    let add = to_send.join(',');                    
                    if(add && n_ > 0) {
                        setTimeout(function(){
                            AllertaMeteoService.addContactsToInvio({
                                id_invio: $scope.id_invio,
                                contacts: add,
                                '_csrf-backend': $scope.csrf
                            }).then((res)=>{
                                $scope.added_contacts += n_;
                                
                                if(res.data.message && res.data.message == 'ok') {
                                    //console.log('aggiunti contatti', n_);
                                    $scope.real_added_contacts += n_;
                                    $scope.spin_message = 'Aggiunti ' + ($scope.to_add_contacts - $scope.real_added_contacts) + ' contatti';
                                    $scope.$broadcast('update_send')  
                                } else {
                                    errors.push("Errore nell'inserimento di " + n_ + " contatti");
                                    $scope.$broadcast('update_send')  
                                }

                            }).catch((e)=>{
                                $scope.can_reset = true;
                            })
                        }, i*1000)
                        
                    }
                    return to_send;
                })
                    
            }
        } else {
            AllertaMeteoService.addContactsToInvio({
                id_invio: $scope.id_invio,
                contacts: "all",
                '_csrf-backend': $scope.csrf
            }).then(function(res){

                if(res.data.message && res.data.message == 'ok') {
                    //console.log('aggiunti tutti i contatti', res);
                    $scope.added_contacts = $scope.to_add_contacts;
                    $scope.spin_message = 'Aggiunti tutti i contatti';
                    $scope.$broadcast('update_send')     
                } else {
                    errors.push("Errore nell'inserimento dei contatti");
                    $scope.$broadcast('update_send')  
                }                   
            }).catch(function(e){
                $scope.can_reset = true;
            })
        }
    }

    // @deprecated
    linkGroups = function(groups) {
        
        if(Array.isArray(groups)) {
            //console.log('inserisco gruppi', groups);
            $scope.to_add_groups = groups.length;

            if($scope.to_add_groups > 0) {                
                AllertaMeteoService.addGroupsToInvio({
                    id_invio: $scope.id_invio,
                    groups: groups.join(','),
                    '_csrf-backend': $scope.csrf
                }).then(function(res) {
                    $scope.added_groups = $scope.to_add_groups;
                    if(res.data.message && res.data.message == 'ok') {
                        //console.log('added groups', res);
                        $scope.spin_message = 'Aggiunti ' + groups.length + ' gruppi';
                        $scope.$broadcast('update_send')  
                    } else {
                        errors.push("Errore nell'inserimento dei gruppi");
                        $scope.$broadcast('update_send')  
                    }  
                }).catch(function(e){
                    $scope.can_reset = true;
                })
            }
        } else {
            
            AllertaMeteoService.addGroupsToInvio({
                id_invio: $scope.id_invio,
                groups: "all",
                '_csrf-backend': $scope.csrf
            }).then(function(res) {
                $scope.added_groups = $scope.to_add_groups;
                if(res.data.message && res.data.message == 'ok') {
                    $scope.spin_message = 'Aggiunti tutti i gruppi';
                    $scope.$broadcast('update_send')
                } else {
                    errors.push("Errore nell'inserimento dei gruppi");
                    $scope.$broadcast('update_send')  
                } 
            }).catch(function(e) {
                $scope.can_reset = true;
            })
            
        }
    }

    

    verifySend = function() {
        if ( 
            $scope.to_add_contacts == $scope.added_contacts && 
            $scope.to_add_groups == $scope.added_groups 
        ) {
            createInvio();
        } else {
            
        }
    }


    createInvio = function() {
        $scope.spin_message = 'Invio al mas';  
        MasService.sendInvioToMas({
            id_invio: $scope.id_invio,
            '_csrf-backend': $scope.csrf
        }).then((res)=>{
            if(res.data.message && res.data.message == 'ok'){
                window.location = $scope.redirect_url;
            } else {
                if(res.data.error) alert(res.data.error);
                    else alert('errore nell\'invio, verrai comunque redirezionato alla lista degli invii')

                window.location = $scope.redirect_url;
            }
        });
    }

    $scope.$on( "update_send", verifySend );

    $scope.can_reset = false;

    $scope.resetAll = function() {
        if($scope.id_invio) {
            MasService.resetInvio( $scope.id_invio ).then(function(res){
                $scope.resetFinal();
            }).catch(function(err){
                console.log('err', err)
            })
        } else {
            $scope.resetFinal();
        }
        
    }

    $scope.resetFinal = function() {
        $scope.id_invio = null;
        /**
         * Verifica eventi
         * @type {Number}
         */
        $scope.to_add_contacts = 0;
        $scope.to_add_groups = 0;

        /**
         * Numero massimo di contatti da inviare per volta
         * @type {Number}
         */
        var max_n_c = 200;
        
        $scope.block_form = false;
        
        $scope.spin_message = 'Salvo il messaggio'
        $scope.added_contacts = 0;
        $scope.real_added_contacts = 0;
        $scope.added_groups = 0;

        $scope.can_reset = false;

        $scope.errors = [];
    }


    $scope.fileNameChanged = function (ele) {

      $scope.mediaFile = [];
      Object.keys(ele.files).map(function(k){
          $scope.mediaFile.push(ele.files[k]);
          return k;
      });

    }

})

.controller("MessageUpdateController", function( $scope, MasService) {
    
    $scope.channels = {
        channel_mail: false,
        channel_pec: false,
        channel_fax: false,
        channel_sms: false,
        channel_push: false,
    };

    initialState = {
        channel_mail: false,
        channel_pec: false,
        channel_fax: false,
        channel_sms: false,
        channel_push: false,
    }

    $scope.changed = false;

    $scope.initController = function( channels ) {
       
        // email
        // pec
        // fax
        // sms
        // push
        var n = 0;
        Object.keys($scope.channels).map( function(key) {
            $scope.channels[key] = channels[n] == 1 ? true : false;
            n++;
            return key;
        });       
        initialState = angular.copy($scope.channels)
        
        
    }

    $scope.logChannels = function() {
        var same = true;
        Object.keys($scope.channels).map( function(ch) {
            
            if($scope.channels[ch] && !initialState[ch]) same = false;

            if(!$scope.channels[ch] && initialState[ch]) same = false;

            return ch;
        })
        if(!same) {
            $scope.changed = true
        } else {
            $scope.changed = false
        }
    }

    $scope.calling = false;
    $scope.id_message = null;
    $scope.csrf = null;

    $scope.setDef = function(message, token) {
        $scope.id_message = message;
        $scope.csrf = token;
    }

    $scope.updateMessage = function() {
        if(!$scope.calling){
            $scope.calling = true;
            //console.log('aggiorno');
            var to_send = angular.copy($scope.channels);
            Object.keys(to_send).map(function(el){
                to_send[el] = to_send[el] ? "1" : "0";
                return el;
            })
            to_send.id_message = $scope.id_message;
            to_send['_csrf-backend'] = $scope.csrf;
            MasService.updateMessageChannels( to_send ).then( function(res){
                //console.log('res', res)
                initialState = angular.copy($scope.channels);
                $scope.changed = false;
                $scope.calling = false;
            }).catch(function(err) {
                console.log('err',err);
                $scope.calling = false;
            })
        }        
    }
    
    $scope.changeChannel = function(channel) {

    }

})
.controller("ResendMessageController", function($scope, AllertaMeteoService, MasService){
    $scope.child = {
        RubricaController: {},
    }
    var selfScope = this;

    $scope.contacts = "";
    $scope.groups = "";
    $scope.block_form = false;

    $scope.id_message = null;
    $scope.id_invio = null;
    $scope.csrf = null;

    addFormContacts = function() {
        //console.log('cc',$scope.child.RubricaController.getSelectedContacts())
        return $scope.child.RubricaController.getSelectedContacts()
    }

    addFormGroups = function() {
        return $scope.child.RubricaController.getSelectedGroups()
    }

    $scope.redirect_url = null;

    $scope.init = function(message, token) {
        $scope.id_message = message;
        $scope.csrf = token;
    }

    $scope.resend = function() {
        try {
                if(!$scope.block_form) {
                    $scope.block_form = true;
                   

                    var contacts = addFormContacts();
                    var groups = addFormGroups();

                    
                    if(Array.isArray(contacts) && contacts.length == 0 &&
                        Array.isArray(groups) && groups.length == 0) {
                        $scope.block_form = false;
                        alert('Seleziona contatti');
                        return;
                    }

                    
                    MasService.resend( $scope.id_message ).then( (res) => {
                    
                        if(res.data.id_invio && res.data.redirect_url){

                            $scope.id_invio = res.data.id_invio;
                            $scope.redirect_url = res.data.redirect_url;

                            if(Array.isArray(contacts)) $scope.to_add_contacts = contacts.length;
                            if(Array.isArray(groups)) $scope.to_add_groups = groups.length;
                            
                            linkContacts(contacts)
                            linkGroups(groups)


                        } else {
                            $scope.block_form = false;
                            if(res.data.error) alert(res.data.error)
                                else alert('Errore creazione invio')
                        }
                    }).catch(function(err) {
                        console.log(err);
                        $scope.resetAll()
                    })
                    
                }
            } catch(e) {
                console.log(e)
                $scope.block_form = false;
            }

    }

    /**
     * Verifica eventi
     * @type {Number}
     */
    $scope.to_add_contacts = 0;
    $scope.to_add_groups = 0;

    /**
     * Numero massimo di contatti da inviare per volta
     * @type {Number}
     */
    var max_n_c = 200;
    
    $scope.resetAll = function() {
            
        /**
         * Verifica eventi
         * @type {Number}
         */
        $scope.to_add_contacts = 0;
        $scope.to_add_groups = 0;

        /**
         * Numero massimo di contatti da inviare per volta
         * @type {Number}
         */
        var max_n_c = 200;
        
        $scope.block_form = false;
        
        $scope.spin_message = 'Salvo il messaggio'
        $scope.added_contacts = 0;
        $scope.real_added_contacts = 0;
        $scope.added_groups = 0;

        $scope.can_reset = false;

        $scope.errors = [];
    }


    $scope.spin_message = 'Salvo il messaggio'
    $scope.added_contacts = 0;
    $scope.real_added_contacts = 0;
    $scope.added_groups = 0;
    $scope.errors = [];


    linkContacts = function(contacts) {
        
        if(Array.isArray(contacts)) {

            
            if($scope.to_add_contacts > 0) {
                $scope.spin_message = 'Aggiungo ' + $scope.to_add_contacts + ' contatti';
                var arrays = [];
                for(n = 0; n < $scope.to_add_contacts; n+=max_n_c) {
                    arrays.push(contacts.slice(n, (n + max_n_c) ));
                }

                arrays.map( function(to_send, i, a) {
                    let n_ = to_send.length;
                    let add = to_send.join(',');                    
                    if(add && n_ > 0) {
                        setTimeout(function(){
                            AllertaMeteoService.addContactsToInvio({
                                id_invio: $scope.id_invio,
                                contacts: add,
                                '_csrf-backend': $scope.csrf
                            }).then((res)=>{
                                $scope.added_contacts += n_;
                                
                                if(res.data.message && res.data.message == 'ok') {
                                    
                                    $scope.real_added_contacts += n_;
                                    $scope.spin_message = 'Aggiunti ' + ($scope.to_add_contacts - $scope.real_added_contacts) + ' contatti';
                                    $scope.$broadcast('update_send')  
                                } else {
                                    errors.push("Errore nell'inserimento di " + n_ + " contatti");
                                    $scope.$broadcast('update_send')  
                                }

                            }).catch((e)=>{
                                $scope.can_reset = true;
                            })
                        }, i*1000)
                        
                    }
                    return to_send;
                })
                    
            }
        } else {
            AllertaMeteoService.addContactsToInvio({
                id_invio: $scope.id_invio,
                contacts: "all",
                '_csrf-backend': $scope.csrf
            }).then(function(res){

                if(res.data.message && res.data.message == 'ok') {
                    
                    $scope.added_contacts = $scope.to_add_contacts;
                    $scope.spin_message = 'Aggiunti tutti i contatti';
                    $scope.$broadcast('update_send')     
                } else {
                    errors.push("Errore nell'inserimento dei contatti");
                    $scope.$broadcast('update_send')  
                }                   
            }).catch(function(e){
                $scope.can_reset = true;
            })
        }
    }

    linkGroups = function(groups) {
        
        if(Array.isArray(groups)) {
            
            $scope.to_add_groups = groups.length;

            if($scope.to_add_groups > 0) {                
                AllertaMeteoService.addGroupsToInvio({
                    id_invio: $scope.id_invio,
                    groups: groups.join(','),
                    '_csrf-backend': $scope.csrf
                }).then(function(res) {
                    $scope.added_groups = $scope.to_add_groups;
                    if(res.data.message && res.data.message == 'ok') {
                        
                        $scope.spin_message = 'Aggiunti ' + groups.length + ' gruppi';
                        $scope.$broadcast('update_send')  
                    } else {
                        errors.push("Errore nell'inserimento dei gruppi");
                        $scope.$broadcast('update_send')  
                    }  
                }).catch(function(e){
                    $scope.can_reset = true;
                })
            }
        } else {
            
            AllertaMeteoService.addGroupsToInvio({
                id_invio: $scope.id_invio,
                groups: "all",
                '_csrf-backend': $scope.csrf
            }).then(function(res) {
                $scope.added_groups = $scope.to_add_groups;
                if(res.data.message && res.data.message == 'ok') {
                    $scope.spin_message = 'Aggiunti tutti i gruppi';
                    $scope.$broadcast('update_send')
                } else {
                    errors.push("Errore nell'inserimento dei gruppi");
                    $scope.$broadcast('update_send')  
                } 
            }).catch(function(e) {
                $scope.can_reset = true;
            })
            
        }
    }

    

    verifySend = function() {
        if ( 
            $scope.to_add_contacts == $scope.added_contacts && 
            $scope.to_add_groups == $scope.added_groups 
        ) {
            createInvio();
        } else {
            
        }
    }


    createInvio = function() {
        $scope.spin_message = 'Invio al mas';  
        MasService.sendInvioToMas({
            id_invio: $scope.id_invio,
            '_csrf-backend': $scope.csrf
        }).then((res)=>{
            if(res.data.message && res.data.message == 'ok'){
                window.location = $scope.redirect_url;
            } else {
                if(res.data.error) alert(res.data.error);
                    else alert('errore nell\'invio, verrai comunque redirezionato alla lista degli invii')

                window.location = $scope.redirect_url;
            }
        });
    }

    $scope.$on( "update_send", verifySend );

    $scope.can_reset = false;

    $scope.resetAll = function() {
        
        /**
         * Verifica eventi
         * @type {Number}
         */
        $scope.to_add_contacts = 0;
        $scope.to_add_groups = 0;

        /**
         * Numero massimo di contatti da inviare per volta
         * @type {Number}
         */
        var max_n_c = 200;
        
        $scope.block_form = false;
        
        $scope.spin_message = 'Salvo il messaggio'
        $scope.added_contacts = 0;
        $scope.real_added_contacts = 0;
        $scope.added_groups = 0;

        $scope.can_reset = false;

        $scope.errors = [];
    }
})



.factory('MonitoraggioService', ['$http', '$location', '$httpParamSerializer', function( $http, $location, $httpParamSerializer ) {

    var MonitoraggioService = {
        
    };

    MonitoraggioService.getContacts = function ( obj = {} ){

        let q_string = $httpParamSerializer(obj);

        return $http({
            url: siteurl+'/monitoraggio/contacts?'+q_string,
            method: "GET"
        });
        
    };

    MonitoraggioService.getGroupedContacts = function ( obj = {} ){

        let q_string = $httpParamSerializer(obj);

        return $http({
            url: siteurl+'/monitoraggio/grouped-contacts?'+q_string,
            method: "GET"
        });
        
    };

    MonitoraggioService.getLogs = function ( obj = {} ){

        let q_string = $httpParamSerializer(obj);

        return $http({
            url: siteurl+'/monitoraggio/mas-log?'+q_string,
            method: "GET"
        });
        
    };

    MonitoraggioService.getAttempts = function ( obj = {} ){

        let q_string = $httpParamSerializer(obj);

        return $http({
            url: siteurl+'/monitoraggio/mas-attempt?'+q_string,
            method: "GET"
        });
        
    };

    MonitoraggioService.getRunning = function ( obj = {} ){

        let q_string = $httpParamSerializer(obj);

        return $http({
            url: siteurl+'/monitoraggio/mas-running?'+q_string,
            method: "GET"
        });
        
    };

    MonitoraggioService.getInvioRunning = function ( obj = {} ){

        let q_string = $httpParamSerializer(obj);

        return $http({
            url: siteurl+'/monitoraggio/mas-invio-running?'+q_string,
            method: "GET"
        });
        
    };

    MonitoraggioService.getInvioMessages = function ( obj = {} ){

        let q_string = $httpParamSerializer(obj);

        return $http({
            url: siteurl+'/monitoraggio/mas-invio-messages?'+q_string,
            method: "GET"
        });
        
    };

    MonitoraggioService.processManually = function ( message ){

        return $http({
            url: siteurl+'/monitoraggio/mas-process-message?id_message='+message,
            method: "GET"
        });
        
    };

    MonitoraggioService.reverify = function ( message ){

        return $http({
            url: siteurl+'/monitoraggio/mas-process-reverify?id_message='+message,
            method: "GET"
        });
        
    };

    MonitoraggioService.stopProcess = function ( message ){
        
        return $http({
            url: siteurl+'/monitoraggio/mas-stop-process-message?id_message='+message,
            method: "GET"
        });
        
    };

    MonitoraggioService.restartProcess = function ( message ){
        
        return $http({
            url: siteurl+'/monitoraggio/mas-restart-process-message?id_message='+message,
            method: "GET"
        });
        
    };

    MonitoraggioService.resend = function ( obj ){
        
        var formData = new FormData();
        Object.keys(obj).map( function(key) {
            formData.append(key, obj[key]);
            return key;
        });

        return $http({
            url: siteurl+'/monitoraggio/resend',
            data: formData,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity,
            method: "POST"
        })
        
    };

    return MonitoraggioService;

}])

.controller("InvioController", function($scope, uiGridConstants, uiGridExporterConstants, /* uiGridTreeBaseConstants, uiGridGroupingConstants,*/ MonitoraggioService) {

    // se c'è un parente glielo metto come figlio in modo da accedere alle proprietò
    var parentScope = $scope.$parent;
    if(parentScope && parentScope.child && parentScope.child.InvioController) parentScope.child.InvioController = $scope;

    


    function onlyUnique(value, index, self) { 
        return self.indexOf(value) === index;
    }


    $scope.getSelectedContacts = function() {
        if($scope.gridContactsApi.selection.getSelectAllState()) return "all";

        var ret_array = [];
        $scope.gridContactsApi.selection.getSelectedGridRows().map( function(selected) {
            ret_array.push( selected.entity.id_riferimento + "|" + selected.entity.tipo_riferimento );
        } )

        return ret_array.filter( onlyUnique );
        
    }


    $scope.getSelectedGroups = function() {
        if($scope.gridGroupsApi.selection.getSelectAllState()) return "all";

        var ret_array = [];
        $scope.gridGroupsApi.selection.getSelectedGridRows().map( function(selected) {
            ret_array.push( selected.entity.id );
        } )

        return ret_array;
       
    }

    var customAggregationDisplayValue = function( aggregation, fieldValue, numValue, row ) {
        aggregation.value = fieldValue
    }

    $scope.exportContacts = function() {
        var to_exp = ($scope.gridExpandableContactsApi.selection.getSelectedGridRows().length > 0) ? uiGridExporterConstants.SELECTED : uiGridExporterConstants.ALL;
        $scope.gridExpandableContactsApi.exporter.csvExport( to_exp, uiGridExporterConstants.ALL )
        //$scope.gridGruppiOptions.api.exportDataAsCsv();
    }

    $scope.exportGrouped = function() {
        $scope.gridContactsApi.exporter.csvExport( uiGridExporterConstants.ALL, uiGridExporterConstants.ALL )
        
    }


    $scope.lang = 'it'
    $scope.uiGrid = {
        enableFiltering: true,
        enableSorting: true,
        //enableGridMenu: true,
        exporterMenuExcel: false,
        exporterMenuPdf: false,
        lang: 'it',
        multiSelect: true,
        enableRowSelection: false,
        enableSelectAll: true,
        enableRowHeaderSelection: true,
        selectionRowHeaderWidth: 35,
        rowHeight: 35,
        showGridFooter:true,
        columnDefs: [],
        data: [],
        onRegisterApi: function( gridApi ) {
            $scope.gridContactsApi = gridApi;
            
        }
    }

    $scope.uiGridExpandable = {
        enableFiltering: true,
        enableSorting: true,
        //enableGridMenu: true,
        exporterMenuExcel: false,
        exporterMenuPdf: false,
        lang: 'it',
        multiSelect: true,
        enableRowSelection: false,
        enableSelectAll: true,
        enableRowHeaderSelection: true,
        selectionRowHeaderWidth: 35,
        rowHeight: 35,
        showGridFooter:true,
        expandableRowTemplate: '<div ui-grid="row.entity.subGridOptions" style="height:150px;"></div>',
        columnDefs: [],
        data: [],
        onRegisterApi: function( gridApi ) {
            $scope.gridExpandableContactsApi = gridApi;
            
        }
    }

    $scope.current_action = 'contacts';

    var cols = {
        logs: [
            {name: 'Livello', field: 'level_name'},
            {name: 'Messaggio', field: 'message'},
            {name: 'Data', field: 'datetime', cellFilter: 'logDate', defaultSort: { direction: uiGridConstants.DESC } },
            {name: 'Memoria', field: 'extra', cellFilter: 'memoryFilter',
            sortingAlgorithm: function(a, b, rowA, rowB, direction) {
                  
                  var f = parseInt(a.memory_usage.replace(/MB/ig, ""));
                  var s = parseInt(b.memory_usage.replace(/MB/ig, ""));

                  return (f === s) ? 0 : ((f > s) ? 1 : -1);
                }
             }
        ],
        messages: [
            {name: 'ID', field: '_id'},
            {name: 'Canale', field: 'channel'},
            {name: 'Stato', field: 'stato'},
            {name: 'Ultimo aggiornamento', field: 'last_processing', cellFilter: 'logDateField', defaultSort: { direction: uiGridConstants.DESC }},
            {name: 'Azioni', field: '_id', cellTemplate: '<div class="ui-grid-cell-contents">'+
                '<button ng-if="row.entity.stopped == 1 && row.entity.process_status != 6" title="Elabora manualmente" class="btn btn-default btn-xs" type="button" ng-class="{\'disabled\': grid.appScope.calling}" ng-click="grid.appScope.processManually(COL_FIELD)">'+
                '<span class="fa fa-arrow-right"></span>'+
                '</button>'+
                '<button ng-if="row.entity.stopped == 0 && row.entity.process_status != 6" style="margin-left: 5px" title="Interrompi processo" class="btn btn-danger btn-xs" type="button" ng-class="{\'disabled\': grid.appScope.calling}" ng-click="grid.appScope.stopProcess(COL_FIELD)">'+
                '<span class="fa fa-ban"></span>'+
                '</button>'+
                '<button ng-if="row.entity.stopped == 1 && row.entity.process_status != 6" style="margin-left: 5px" title="Rimetti in coda il processo" class="btn btn-success btn-xs" type="button" ng-class="{\'disabled\': grid.appScope.calling}" ng-click="grid.appScope.restartProcess(COL_FIELD)">'+
                '<span class="fa fa-paper-plane"></span>'+
                '</button>'+
                '<button ng-if="row.entity.process_status == 6" style="margin-left: 5px" title="Rielabora il feedback" class="btn btn-info btn-xs" type="button" ng-class="{\'disabled\': grid.appScope.calling}" ng-click="grid.appScope.reverify(COL_FIELD)">'+
                '<span class="fa fa fa-refresh"></span>'+
                '</button>'+
              '</div>'},
        ],
        attempt: [
            {name: 'ID msg', field: '_id_message'},
            {name: 'Stato', field: 'stato'},
            {name: 'Contatto', field: 'contatto', /*cellFilter: 'refContattoFilter'*/},
            {name: 'Riferimento', field: 'ref', /*cellFilter: 'refRiferimentoFilter',*/ defaultSort: { direction: uiGridConstants.ASC }},
            {name: 'ID provider (es. Everbridge)', field: 'verification_identifier'},
            {name: 'Inserimento', field: 'add_time', /*cellFilter: 'logDateField'*/},
            {name: 'Invio', field: 'sent_time', /*cellFilter: 'logDateField'*/},
            {name: 'Feedback', field: 'feedback_time', /*cellFilter: 'logDateField'*/},
        ],
        contacts: [
            {name: 'Contatto', field: 'valore_rubrica_contatto'},
            {name: 'Riferimento', field: 'valore_riferimento', defaultSort: { direction: uiGridConstants.ASC }},
            {name: 'Tipo riferimento', field: 'contatto', cellFilter: 'tipoRiferimento'},
            {name: 'Canale', field: 'channel'},
            {name: 'Inviato', field: 'inviato'},
        ],
        groupedContacts: [
            {name: 'Riferimento', field: 'valore_riferimento', defaultSort: { direction: uiGridConstants.ASC }},
            {name: 'Tipo riferimento', field: 'tipologia_riferimento'},
            {name: 'Raggiunto', field: 'raggiunto', cellTemplate: 'checkTemplate.html',filter: {
              type: uiGridConstants.filter.SELECT,
              selectOptions: [ { value: 'Si', label: 'Si' }, { value: 'No', label: 'No' }]
            }},
            {name: 'Mail', field: 'sent_Email', cellTemplate: 'checkTemplate.html',filter: {
              type: uiGridConstants.filter.SELECT,
              selectOptions: [ { value: 'Si', label: 'Si' }, { value: 'No', label: 'No' }]
            } },
            {name: 'Pec', field: 'sent_Pec', cellTemplate: 'checkTemplate.html',filter: {
              type: uiGridConstants.filter.SELECT,
              selectOptions: [ { value: 'Si', label: 'Si' }, { value: 'No', label: 'No' }]
            } },
            {name: 'Fax', field: 'sent_Fax', cellTemplate: 'checkTemplate.html',filter: {
              type: uiGridConstants.filter.SELECT,
              selectOptions: [ { value: 'Si', label: 'Si' }, { value: 'No', label: 'No' }]
            } },
            {name: 'Sms', field: 'sent_Sms', cellTemplate: 'checkTemplate.html',filter: {
              type: uiGridConstants.filter.SELECT,
              selectOptions: [ { value: 'Si', label: 'Si' }, { value: 'No', label: 'No' }]
            } },
            {name: 'Push', field: 'sent_Push', cellTemplate: 'checkTemplate.html',filter: {
              type: uiGridConstants.filter.SELECT,
              selectOptions: [ { value: 'Si', label: 'Si' }, { value: 'No', label: 'No' }]
            } },
            //{name: 'Dettagli', field: 'masSingleSendsAggregated', cellFilter: 'tipoRiferimento'},
        ],
    }

    $scope.isYes = function(col, row) {        
        return row.entity[col.field] == 'Si' ? true : false;
    }

    $scope.isActiveMas = function() {
        $scope.calling = true;
        MonitoraggioService.getRunning().then( function(res) {
            //console.log('running', res);
            if(res.data.work && res.data.verify) {
                $scope.is_running = true;
            } else {
                $scope.is_running = false;
            }
            $scope.calling = false;
        }).catch(function(err) {
            $scope.is_running = false;
            $scope.calling = false;
        })
    }

    $scope.is_running = false;
    $scope.id_invio = null;
    $scope.log_channel = 'Email';
    $scope.csrf = null

    $scope.initController = function(invio, csrf_token) {
        $scope.id_invio = invio;
        $scope.csrf = csrf_token;
        // verifica se girano i consumer
        $scope.isActiveMas();

        setTimeout(function() {
            $scope.updateData();
        }, 1000)
    }


    serviceData = function() {
        switch($scope.current_action) {
            case 'logs':
            return 'getLogs';
            break;
            case 'messages':
            return 'getInvioMessages';
            break;
            case 'attempt':
            return 'getAttempts';
            break;
            case 'contacts':
            return 'getContacts';
            break;
            case 'groupedContacts':
            return 'getGroupedContacts';
            break;
        }
    }

    $scope.updateData = function ( reload_columns = true ) {
        
        $scope.uiGrid.data = [];
        $scope.uiGrid.columnDefs = [];

        $scope.uiGridExpandable.data = [];
        $scope.uiGridExpandable.columnDefs = [];

        MonitoraggioService[serviceData()]({id_invio: $scope.id_invio, channel: $scope.log_channel }).then( function(res) {
            //console.log('risultato', res);
            var data = res.data.data;

            
            if( $scope.current_action == 'contacts') {
                $scope.uiGridExpandable.expandableRowHeight = 140;
                
                // stati per cui l'invio è a buon fine
                var sent_statuses = [2,3];
                
                for(i = 0; i < data.length; i++){

                    var sent_statuses = (data[i].channel == 'Fax' || data[i].channel == 'Pec' || data[i].channel == 'Sms') ? [3] : [2,3];

                    var sent = false;
                    data[i].masSingleSendsWithoutDuplicates.map(function(el) {
                        if(sent_statuses.indexOf(el.status) != -1) sent = true
                    })

                    data[i].inviato = sent ? "Si" : "No";
                    //console.log(data[i].masSingleSendsWithoutDuplicates)
                    data[i].subGridOptions = {
                      columnDefs: [
                            {name: 'Status', field: 'status', cellFilter: 'replaceStatus'},
                            {name: 'Invio', field: 'sent_time', cellFilter: 'logDateField'},
                            {name: 'Feedback', field: 'feedback_time', cellFilter: 'logDateField'},
                      ],
                      data: data[i].masSingleSendsWithoutDuplicates
                    };
                }

                $scope.uiGridExpandable.data = data;
                $scope.uiGridExpandable.columnDefs = cols[$scope.current_action];

            } else 
            if( $scope.current_action == 'messages')  {
                $scope.uiGridExpandable.expandableRowHeight = 140;
                
                for(i = 0; i < data.length; i++){

                    data[i].subGridOptions = {
                      columnDefs: [
                            {name: 'Contatto', field: 'valore_rubrica_contatto'}
                      ],
                      data: data[i].contacts
                    };
                }

                $scope.uiGridExpandable.data = data;
                $scope.uiGridExpandable.columnDefs = cols[$scope.current_action];

            } else {
                $scope.uiGrid.data = data;
                $scope.uiGrid.columnDefs = cols[$scope.current_action];
            }

                
        })

    }

    $scope.changeChannel = function(channel) {
        $scope.log_channel = channel;
        $scope.updateData();
    }

    $scope.loadMasInvioMessages = function() {
        $scope.current_action = 'messages';
        $scope.updateData()
    }

    $scope.loadMasLog = function() {
        $scope.current_action = 'logs';
        $scope.updateData()
    }

    $scope.loadMasAttempt = function() {
        $scope.current_action = 'attempt';
        $scope.updateData()
    }

    $scope.loadContacts = function() {
        $scope.current_action = 'contacts';
        $scope.updateData()
    }

    $scope.loadGroupedContacts = function() {
        $scope.current_action = 'groupedContacts';
        $scope.updateData()
    }

    $scope.calling = false;
    $scope.processManually = function(field) {
        if(!$scope.calling) {
            $scope.calling = true;
            
            MonitoraggioService.processManually(field).then(function(res){
                
                $scope.calling = false;
                $scope.updateData();
            }).catch(function(err){
                $scope.calling = false
                
            })
        }
    }

    $scope.stopProcess = function(field) {
        if(!$scope.calling) {
            $scope.calling = true;
            
            MonitoraggioService.stopProcess(field).then(function(res){
                
                $scope.calling = false;
                $scope.updateData();
            }).catch(function(err){
                $scope.calling = false
                
            })
        }
    }

    $scope.restartProcess = function(field) {
        if(!$scope.calling) {
            $scope.calling = true;
            
            MonitoraggioService.restartProcess(field).then(function(res){
                $scope.calling = false;
                $scope.updateData();
            }).catch(function(err){
                $scope.calling = false
            })
        }
    }

    $scope.reverify = function(field) {
        if(!$scope.calling) {
            $scope.calling = true;
            
            MonitoraggioService.reverify(field).then(function(res){
                $scope.calling = false;
                $scope.updateData();
            }).catch(function(err){
                $scope.calling = false
            })
        }
    }

    /**
     * Reinvia il messaggio a tutti
     * @return {[type]} [description]
     */
    $scope.resend = function() {
        if($scope.calling) return;
        $scope.calling = true;
        
        MonitoraggioService.resend({
            action: 'all',
            '_csrf-backend': $scope.csrf,
            id_invio: $scope.id_invio 
        }).then(function(res) {
            $scope.updateData(false);
            $scope.calling = false;
        }).catch(function(err){
            console.log(err);
            $scope.calling = false;
        })
    }
    $scope.resendNotSent = function() {
        if($scope.calling) return;
        $scope.calling = true;
        
        MonitoraggioService.resend({
            action: 'not_sent',
            '_csrf-backend': $scope.csrf,
            id_invio: $scope.id_invio 
        }).then(function(res) {
            $scope.updateData(false);
            $scope.calling = false;
        }).catch(function(err){
            console.log(err);
            $scope.calling = false;
        })
    }
    $scope.resendSelected = function() {
        if($scope.calling) return;
        if($scope.gridExpandableContactsApi.selection.getSelectAllState()
            && $scope.gridExpandableContactsApi.selection.getSelectedGridRows().length == $scope.uiGridExpandable.data.length
            ) {
            $scope.resend();
        } else {
            var ret_array = [];
            $scope.gridExpandableContactsApi.selection.getSelectedGridRows().map( function(selected) {
                ret_array.push( selected.entity.id );
            } );
            //console.log(ret_array);
            if(ret_array.length > 0) {
                $scope.calling = true;
                MonitoraggioService.resend({
                    action: 'selected',
                    '_csrf-backend': $scope.csrf,
                    id_invio: $scope.id_invio,
                    contacts: JSON.stringify(ret_array)
                }).then(function(res) {
                    $scope.updateData(false);
                    $scope.calling = false;
                }).catch(function(err){
                    console.log(err);
                    $scope.calling = false;
                })
            } else {
                alert("Seleziona contatti");
            }
        }

    }

})
.filter('logDate', function() {
  return function(input) {
    var val = moment(input.date)
    return val.format('DD/MM/YYYY HH:mm:ss')
  };
})
.filter('logDateField', function() {
  return function(input) {
    var v = moment(input,"X");
    return v.isValid() ? v.format('DD/MM/YYYY HH:mm:ss') : "";
  };
})
.filter('memoryFilter', function() {
  return function(input) {
    return input.memory_usage
  };
})
.filter('refContattoFilter', function() {
  return function(input) {
    return input.valore_rubrica_contatto
  };
})
.filter('refRiferimentoFilter', function() {
  return function(input) {
    return input.valore_riferimento
  };
})
.filter('tipoRiferimento', function() {
  return function(input) {      
    return input.tipologia_riferimento
  };
})
.filter('destinatarioRaggiunto', function() {
    var sent_statuses = [2,3];
    return function(input) {
        var sent = false;
        input.map(function(el) {
            if(sent_statuses.indexOf(el.status) != -1) sent = true
        })
        return sent ? "Si" : "No";
    };
})
.filter('sentMail', function() {
    var sent_statuses = [2,3];
    return function(input) {
        var sent = false;
        input.filter(function(el) { return el.channel == 'Email'}).map(function(el) {
            if(sent_statuses.indexOf(el.status) != -1) sent = true
        })
        return sent ? "Si" : "No";
    };
})
.filter('sentPec', function() {
    var sent_statuses = [3];
    return function(input) {
        var sent = false;
        input.filter(function(el) { return el.channel == 'Pec'}).map(function(el) {
            if(sent_statuses.indexOf(el.status) != -1) sent = true
        })
        return sent ? "Si" : "No";
    };
})
.filter('sentSms', function() {
    var sent_statuses = [2,3];
    return function(input) {
        var sent = false;
        input.filter(function(el) { return el.channel == 'Sms'}).map(function(el) {
            if(sent_statuses.indexOf(el.status) != -1) sent = true
        })
        return sent ? "Si" : "No";
    };
})
.filter('sentFax', function() {
    var sent_statuses = [3];
    return function(input) {
        var sent = false;
        input.filter(function(el) { return el.channel == 'Fax'}).map(function(el) {
            if(sent_statuses.indexOf(el.status) != -1) sent = true
        })
        return sent ? "Si" : "No";
    };
})
.filter('sentPush', function() {
    var sent_statuses = [2,3];
    return function(input) {
        var sent = false;
        input.filter(function(el) { return el.channel == 'Push'}).map(function(el) {
            if(sent_statuses.indexOf(el.status) != -1) sent = true
        })
        return sent ? "Si" : "No";
    };
})
.filter('specializzazioni', function() {
    
    return function(input) {
        return input.map(function(a){ return a.descrizione }).join(", ");
    };
})
//const STATUS_DUPLICATED = -1;
//const STATUS_ADDED = 0;
//const STATUS_READY = 1;
//const STATUS_SENT = 2;
//const STATUS_RECEIVED = 3;
//const STATUS_REFUSED = 4;
//const INVALID_CONTACT = 5;
//const STATUS_NOT_SENT = 6;
//const STATUS_NO_FEEDBACK = 7;
.filter('replaceStatus', function() {
  return function(input) {
    switch(input) {
        case -1:
        return "Contatto duplicato"
        break;
        case 0:
        return "Inserito in database"
        break;
        case 1:
        return "Pronto per l'invio"
        break;
        case 2:
        return "Messaggio inviato"
        break;
        case 3:
        return "Messaggio ricevuto"
        break;
        case 4:
        return "Messaggio rifiutato"
        break;
        case 5:
        return "Contatto non valido"
        break;
        case 6:
        return "Messaggio non inviato"
        break;
        case 7:
        return "Feedback non ricevuto"
        break;
    }
  };
})

