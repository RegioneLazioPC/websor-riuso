// Type definitions for ag-grid-community v20.2.0
// Project: http://www.ag-grid.com/
// Definitions by: Niall Crosby <https://github.com/ag-grid/>
import { Component } from "../../widgets/component";
import { ICellRenderer, ICellRendererParams } from "./iCellRenderer";
export interface GroupCellRendererParams extends ICellRendererParams {
    pinned: string;
    padding: number;
    suppressPadding: boolean;
    suppressDoubleClickExpand: boolean;
    footerValueGetter: any;
    suppressCount: boolean;
    fullWidth: boolean;
    checkbox: any;
    scope: any;
    actualValue: string;
}
export declare class GroupCellRenderer extends Component implements ICellRenderer {
    private static TEMPLATE;
    private gridOptionsWrapper;
    private expressionService;
    private eventService;
    private valueFormatterService;
    private columnController;
    private mouseEventService;
    private userComponentFactory;
    private eExpanded;
    private eContracted;
    private eCheckbox;
    private eValue;
    private eChildCount;
    private params;
    private draggedFromHideOpenParents;
    private displayedGroup;
    private cellIsBlank;
    private indentClass;
    private innerCellRenderer;
    constructor();
    init(params: GroupCellRendererParams): void;
    private assignBlankValueToGroupFooterCell;
    private isEmbeddedRowMismatch;
    private setIndent;
    private setPaddingDeprecatedWay;
    private setupIndent;
    private addValueElement;
    private createFooterCell;
    private createGroupCell;
    private useInnerRenderer;
    private useFullWidth;
    private addChildCount;
    private updateChildCount;
    private createLeafCell;
    private isUserWantsSelected;
    private addCheckboxIfNeeded;
    private addExpandAndContract;
    private onAllChildrenCountChanged;
    private onKeyDown;
    private setupDragOpenParents;
    onExpandClicked(mouseEvent: MouseEvent): void;
    onCellDblClicked(mouseEvent: MouseEvent): void;
    onExpandOrContract(): void;
    private isExpandable;
    private showExpandAndContractIcons;
    destroy(): void;
    refresh(): boolean;
}
