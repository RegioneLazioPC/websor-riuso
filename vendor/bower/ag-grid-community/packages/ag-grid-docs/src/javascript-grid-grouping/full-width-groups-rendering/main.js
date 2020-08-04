var columnDefs = [
    {headerName: 'Athlete', field: 'athlete', width: 200},
    {headerName: 'Age', field: 'age', width: 90},
    {headerName: 'Gold', field: 'gold', width: 100, aggFunc: 'sum'},
    {headerName: 'Silver', field: 'silver', width: 100, aggFunc: 'sum'},
    {headerName: 'Bronze', field: 'bronze', width: 100, aggFunc: 'sum'},
    {headerName: 'Total', field: 'total', width: 100, aggFunc: 'sum'},
    {headerName: 'Country', field: 'country', width: 120, rowGroup: true},
    {headerName: 'Year', field: 'year', width: 90},
    {headerName: 'Date', field: 'date', width: 110},
    {headerName: 'Sport', field: 'sport', width: 110}
];

var gridOptions = {
    columnDefs: columnDefs,
    rowData: null,
    groupUseEntireRow: true,
    components: {
        groupRowInnerRenderer: GroupRowInnerRenderer
    },
    groupRowInnerRenderer: 'groupRowInnerRenderer',
    groupRowRendererParams: {
        flagCodes: {
            Ireland: 'ie',
            'United States': 'us',
            Russia: 'ru',
            Australia: 'au',
            Canada: 'ca',
            Norway: 'no',
            China: 'cn',
            Zimbabwe: 'zw',
            Netherlands: 'nl',
            'South Korea': 'kr',
            Croatia: 'hr',
            France: 'fr'
        }
    }
};

// setup the grid after the page has finished loading
document.addEventListener('DOMContentLoaded', function() {
    var gridDiv = document.querySelector('#myGrid');
    new agGrid.Grid(gridDiv, gridOptions);

    // do http request to get our sample data - not using any framework to keep the example self contained.
    // you will probably use a framework like JQuery, Angular or something else to do your HTTP calls.
    var httpRequest = new XMLHttpRequest();
    httpRequest.open('GET', 'https://raw.githubusercontent.com/ag-grid/ag-grid/master/packages/ag-grid-docs/src/olympicWinnersSmall.json');
    httpRequest.send();
    httpRequest.onreadystatechange = function() {
        if (httpRequest.readyState === 4 && httpRequest.status === 200) {
            var httpResult = JSON.parse(httpRequest.responseText);
            gridOptions.api.setRowData(httpResult);
        }
    };
});