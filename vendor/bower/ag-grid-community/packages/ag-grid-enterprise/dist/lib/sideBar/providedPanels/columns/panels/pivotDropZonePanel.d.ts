// ag-grid-enterprise v20.2.0
import { Column } from "ag-grid-community/main";
import { BaseDropZonePanel } from "../dropZone/baseDropZonePanel";
export declare class PivotDropZonePanel extends BaseDropZonePanel {
    private columnController;
    private eventService;
    private gridOptionsWrapper;
    private loggerFactory;
    private dragAndDropService;
    private columnApi;
    private gridApi;
    constructor(horizontal: boolean);
    private passBeansUp;
    private refresh;
    private checkVisibility;
    protected isColumnDroppable(column: Column): boolean;
    protected updateColumns(columns: Column[]): void;
    protected getIconName(): string;
    protected getExistingColumns(): Column[];
}
