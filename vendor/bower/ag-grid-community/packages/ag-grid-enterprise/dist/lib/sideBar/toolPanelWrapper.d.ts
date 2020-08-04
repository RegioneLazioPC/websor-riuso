// ag-grid-enterprise v20.2.0
import { Component, ToolPanelDef } from "ag-grid-community";
export declare class ToolPanelWrapper extends Component {
    private userComponentFactory;
    private gridOptionsWrapper;
    private static TEMPLATE;
    private toolPanelCompInstance;
    private toolPanelId;
    constructor();
    getToolPanelId(): string;
    setToolPanelDef(toolPanelDef: ToolPanelDef): void;
    private setupResize;
    private setToolPanelComponent;
    refresh(): void;
}
