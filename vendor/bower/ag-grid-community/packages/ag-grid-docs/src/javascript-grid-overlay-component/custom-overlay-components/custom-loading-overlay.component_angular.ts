import { Component, Input } from '@angular/core';
import { ILoadingOverlayAngularComp } from "ag-grid-angular";

@Component({
    selector: 'app-loading-overlay',
    template: `<div class="ag-overlay-loading-center" style="background-color: lightsteelblue;">` +
              `   <i class="fas fa-hourglass-half"> {{this.params.loadingMessage}} </i>` +
              `</div>`
})
export class CustomLoadingOverlay implements ILoadingOverlayAngularComp {

    private params: any;

    agInit(params): void {
        this.params = params;
    }
}