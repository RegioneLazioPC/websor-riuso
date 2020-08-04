var columnDefs = [
    {field: "athlete", rowDrag: true},
    {field: "country"},
    {field: "year"},
    {field: "date"},
    {field: "sport"},
    {field: "gold"},
    {field: "silver"},
    {field: "bronze"}
];

var gridOptions = {
    defaultColDef: {
        width: 150,
        sortable: true,
        filter: true
    },
    columnDefs: columnDefs,
    animateRows: true,
    onRowDragEnter: onRowDragEnter,
    onRowDragEnd: onRowDragEnd,
    onRowDragMove: onRowDragMove,
    onRowDragLeave: onRowDragLeave
};

function onRowDragEnter(e) {
    console.log('onRowDragEnter', e);
}

function onRowDragEnd(e) {
    console.log('onRowDragEnd', e);
}

function onRowDragMove(e) {
    console.log('onRowDragMove', e);
}

function onRowDragLeave(e) {
    console.log('onRowDragLeave', e);
}

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