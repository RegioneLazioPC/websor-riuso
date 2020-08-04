var columnDefs = [
    {headerName: "Country", field: "country", width: 120, rowGroup: true, enableRowGroup:true},
    {headerName: "Year", field: "year", width: 90, rowGroup: true, enableRowGroup:true, enablePivot:true},
    {headerName: "Date", field: "date", width: 110},
    {headerName: "Sport", field: "sport", width: 110},
    {headerName: "Gold", field: "gold", width: 100, aggFunc: 'sum'},
    {headerName: "Silver", field: "silver", width: 100, aggFunc: 'sum'},
    {headerName: "Bronze", field: "bronze", width: 100, aggFunc: 'sum'}
];

function onBtNormal() {
    gridOptions.columnApi.setPivotMode(false);
    gridOptions.columnApi.setPivotColumns([]);
    gridOptions.columnApi.setRowGroupColumns(['country','year']);
}

function onBtPivotMode() {
    gridOptions.columnApi.setPivotMode(true);
    gridOptions.columnApi.setPivotColumns([]);
    gridOptions.columnApi.setRowGroupColumns(['country','year']);
}

function onBtFullPivot() {
    gridOptions.columnApi.setPivotMode(true);
    gridOptions.columnApi.setPivotColumns(['year']);
    gridOptions.columnApi.setRowGroupColumns(['country']);
}

var gridOptions = {
    defaultColDef: {
        sortable: true,
        resizable: true
    },
    // set rowData to null or undefined to show loading panel by default
    columnDefs: columnDefs,
    sideBar: 'columns'
};

// setup the grid after the page has finished loading
document.addEventListener('DOMContentLoaded', function() {
    var gridDiv = document.querySelector('#myGrid');
    new agGrid.Grid(gridDiv, gridOptions);

    // do http request to get our sample data - not using any framework to keep the example self contained.
    // you will probably use a framework like JQuery, Angular or something else to do your HTTP calls.
    var httpRequest = new XMLHttpRequest();
    httpRequest.open('GET', 'https://raw.githubusercontent.com/ag-grid/ag-grid/master/packages/ag-grid-docs/src/olympicWinners.json');
    httpRequest.send();
    httpRequest.onreadystatechange = function() {
        if (httpRequest.readyState === 4 && httpRequest.status === 200) {
            var httpResult = JSON.parse(httpRequest.responseText);
            gridOptions.api.setRowData(httpResult);
        }
    };
});