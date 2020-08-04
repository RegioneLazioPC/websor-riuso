var latinText =
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum';

var columnDefs = [
    {
        headerName: 'Latin Text',
        field: 'latinText',
        width: 350,
        cellStyle: {
            'white-space': 'normal'
        }
    },
    {headerName: 'Athlete', field: 'athlete', width: 180},
    {headerName: 'Age', field: 'age', width: 90},
    {headerName: 'Country', field: 'country', width: 120},
    {headerName: 'Year', field: 'year', width: 90},
    {headerName: 'Date', field: 'date', width: 110},
    {headerName: 'Sport', field: 'sport', width: 110},
    {headerName: 'Gold', field: 'gold', width: 100},
    {headerName: 'Silver', field: 'silver', width: 100},
    {headerName: 'Bronze', field: 'bronze', width: 100},
    {headerName: 'Total', field: 'total', width: 100}
];

var gridOptions = {
    defaultColDef: {
        sortable: true,
        resizable: true,
        filter: true
    },
    columnDefs: columnDefs,
    rowData: null,
    // call back function, to tell the grid what height
    // each row should be
    getRowHeight: function(params) {
        // assuming 50 characters per line, working how how many lines we need
        return 28 * (Math.floor(params.data.latinText.length / 60) + 1);
    }
};

// setup the grid after the page has finished loading
document.addEventListener('DOMContentLoaded', function() {
    var gridDiv = document.querySelector('#myGrid');
    new agGrid.Grid(gridDiv, gridOptions);

    agGrid.simpleHttpRequest({url: 'https://raw.githubusercontent.com/ag-grid/ag-grid/master/packages/ag-grid-docs/src/olympicWinnersSmall.json'}).then(function(data) {
        data.forEach(function(dataItem) {
            var start = Math.floor(Math.random() * (latinText.length / 2));
            var end = Math.floor(Math.random() * (latinText.length / 2) + latinText.length / 2);
            dataItem.latinText = latinText.substring(start, end);
        });

        // now set the height into the grid
        gridOptions.api.setRowData(data);
    });
});