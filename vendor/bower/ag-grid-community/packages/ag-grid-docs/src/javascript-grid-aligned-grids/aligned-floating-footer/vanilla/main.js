var columnDefs = [
    {headerName: 'Athlete', field: 'athlete', width: 200},
    {headerName: 'Age', field: 'age', width: 100},
    {headerName: 'Country', field: 'country', width: 150},
    {headerName: 'Year', field: 'year', width: 120},
    {headerName: 'Sport', field: 'sport', width: 200},
    // in the total col, we have a value getter, which usually means we don't need to provide a field
    // however the master/slave depends on the column id (which is derived from the field if provided) in
    // order ot match up the columns
    {
        headerName: 'Total',
        field: 'total',
        valueGetter: 'data.gold + data.silver + data.bronze',
        width: 200
    },
    {headerName: 'Gold', field: 'gold', width: 100},
    {headerName: 'Silver', field: 'silver', width: 100},
    {headerName: 'Bronze', field: 'bronze', width: 100}
];

var dataForBottomGrid = [
    {
        athlete: 'Total',
        age: '15 - 61',
        country: 'Ireland',
        year: '2020',
        date: '26/11/1970',
        sport: 'Synchronised Riding',
        gold: 55,
        silver: 65,
        bronze: 12
    }
];

// this is the grid options for the top grid
var gridOptionsTop = {
    defaultColDef: {
        sortable: true,
        resizable: true
    },
    columnDefs: columnDefs,
    rowData: null,
    debug: true,
    // don't show the horizontal scrollbar on the top grid
    suppressHorizontalScroll: true,
    alignedGrids: []
};

// this is the grid options for the bottom grid
var gridOptionsBottom = {
    defaultColDef: {
        resizable: true
    },
    columnDefs: columnDefs,
    // we are hard coding the data here, it's just for demo purposes
    rowData: dataForBottomGrid,
    debug: true,
    rowClass: 'bold-row',
    // hide the header on the bottom grid
    headerHeight: 0,
    alignedGrids: []
};

gridOptionsTop.alignedGrids.push(gridOptionsBottom);
gridOptionsBottom.alignedGrids.push(gridOptionsTop);

function btSizeColsToFix() {
    gridOptionsTop.api.sizeColumnsToFit();
    console.log('btSizeColsToFix ');
}

// setup the grid after the page has finished loading
document.addEventListener('DOMContentLoaded', function() {
    var gridDivTop = document.querySelector('#myGridTop');
    new agGrid.Grid(gridDivTop, gridOptionsTop);
    var gridDivBottom = document.querySelector('#myGridBottom');
    new agGrid.Grid(gridDivBottom, gridOptionsBottom);

    // do http request to get our sample data - not using any framework to keep the example self contained.
    // you will probably use a framework like JQuery, Angular or something else to do your HTTP calls.
    var httpRequest = new XMLHttpRequest();
    httpRequest.open('GET', 'https://raw.githubusercontent.com/ag-grid/ag-grid/master/packages/ag-grid-docs/src/olympicWinnersSmall.json');
    httpRequest.send();
    httpRequest.onreadystatechange = function() {
        if (httpRequest.readyState === 4 && httpRequest.status === 200) {
            var httpResult = JSON.parse(httpRequest.responseText);
            gridOptionsTop.api.setRowData(httpResult);
        }
    };
});