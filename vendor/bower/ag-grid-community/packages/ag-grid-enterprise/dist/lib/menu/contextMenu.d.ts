// ag-grid-enterprise v20.2.0
import { Column, IContextMenuFactory, RowNode } from "ag-grid-community";
export declare class ContextMenuFactory implements IContextMenuFactory {
    private context;
    private popupService;
    private gridOptionsWrapper;
    private rowModel;
    private rangeController;
    private activeMenu;
    private init;
    hideActiveMenu(): void;
    private getMenuItems;
    showMenu(node: RowNode, column: Column, value: any, mouseEvent: MouseEvent | Touch): void;
}
