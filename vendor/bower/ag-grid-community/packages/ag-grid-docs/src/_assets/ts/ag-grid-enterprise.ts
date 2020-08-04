import "../../../../ag-grid-community/src/styles/ag-grid.scss";

import "../../../../ag-grid-community/src/styles/ag-theme-material/sass/ag-theme-material.scss";
import "../../../../ag-grid-community/src/styles/ag-theme-fresh/sass/ag-theme-fresh.scss";
import "../../../../ag-grid-community/src/styles/ag-theme-dark/sass/ag-theme-dark.scss";
import "../../../../ag-grid-community/src/styles/ag-theme-blue/sass/ag-theme-blue.scss";
import "../../../../ag-grid-community/src/styles/ag-theme-bootstrap/sass/ag-theme-bootstrap.scss";
import "../../../../ag-grid-community/src/styles/ag-theme-balham/sass/ag-theme-balham.scss";
import "../../../../ag-grid-community/src/styles/ag-theme-balham-dark/sass/ag-theme-balham-dark.scss";

declare var HMR: boolean;

if (HMR) {
    (<any>global).hot = true;
    require("webpack-hot-middleware/client?path=/dev/ag-grid-enterprise-bundle/__webpack_hmr&reload=true");
}

export * from "../../../../ag-grid-community/src/ts/main";

import "../../../../ag-grid-enterprise/src/main.ts";
