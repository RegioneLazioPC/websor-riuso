
// this example uses Angular 1. cellTemplates doesn't make sense with any other framework
agGrid.initialiseAgGridWithAngular1(angular);
var module = angular.module('example', ['agGrid']);

module.controller('exampleCtrl', function($scope, $http) {

    var columnDefs = [
        {headerName: 'Useless', width: 100, template: '<span style="font-weight: bold;">BLAH</span>'},
        {headerName: 'Athlete', width: 150, template: '<span style="font-weight: bold;" ng-bind="data.athlete"></span>'},
        {headerName: 'Age', width: 90, template: '<span style="font-weight: bold;" ng-bind="data.age"></span>'},
        {headerName: 'Country', field: 'country', width: 120},
        {headerName: 'Year', field: 'year', width: 90},
        {headerName: 'Date', field: 'date', width: 110},
        {headerName: 'Sport', field: 'sport', width: 110},
        {headerName: 'Gold', field: 'gold', width: 100},
        {headerName: 'Silver', field: 'silver', width: 100},
        {headerName: 'Bronze', field: 'bronze', width: 100},
        {headerName: 'Total', field: 'total', width: 100}
    ];

    $scope.gridOptions = {
        // we are using angular in the templates
        angularCompileRows: true,
        columnDefs: columnDefs,
        rowData: null
    };

    $http.get('https://raw.githubusercontent.com/ag-grid/ag-grid/master/packages/ag-grid-docs/src/wideSpreadOfSports.json')
        .then(function(res){
            $scope.gridOptions.api.setRowData(res.data);
        });
});
