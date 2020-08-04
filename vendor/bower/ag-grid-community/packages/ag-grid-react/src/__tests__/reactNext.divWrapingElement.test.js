import React, {Component} from 'react';
import {AgGridReact} from '../agGridReact';

import {ensureGridApiHasBeenSet} from "./utils"

import {mount} from 'enzyme';

let component = null;
let agGridReact = null;

beforeEach((done) => {
    component = mount((<GridWithNoComponentContainerSpecified/>));
    agGridReact = component.find(AgGridReact).instance();
    // don't start our tests until the grid is ready
    ensureGridApiHasBeenSet(component).then(() => done());

});

afterEach(() => {
    component.unmount();
    agGridReact = null;
});

it('reactNext with wrapping element set to div renders as expected', () => {
    expect(component.render().find('.ag-cell-value').html()).toEqual(`<div class=\"ag-react-container\"><div>Blerp</div></div>`);
});

class CellRenderer extends Component {
    render() {
        return(
            <div>Blerp</div>
        )
    }
}

class GridWithNoComponentContainerSpecified extends Component {
    constructor(props) {
        super(props);

        this.state = {
            columnDefs: [{
                field: "age",
                cellRendererFramework: CellRenderer
            }],
            rowData: [{age: 24}]
        };
    }

    onGridReady(params) {
        this.api = params.api;
    }

    render() {
        return (
            <div
                className="ag-theme-balham">
                <AgGridReact
                    columnDefs={this.state.columnDefs}
                    onGridReady={this.onGridReady.bind(this)}
                    rowData={this.state.rowData}
                    reactNext={true}
                    componentWrappingElement="div"
                />
            </div>
        );
    }
}
