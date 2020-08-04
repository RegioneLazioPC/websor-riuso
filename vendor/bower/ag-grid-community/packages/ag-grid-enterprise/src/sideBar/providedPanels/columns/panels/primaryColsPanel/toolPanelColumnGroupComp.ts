import {
    AgCheckbox,
    Autowired,
    Column,
    ColumnController,
    Component,
    Context,
    CssClassApplier,
    DragAndDropService,
    DragSource,
    DragSourceType,
    Events,
    EventService,
    GridOptionsWrapper,
    OriginalColumnGroup,
    PostConstruct,
    RefSelector,
    TouchListener,
    _
} from "ag-grid-community/main";
import { BaseColumnItem } from "./primaryColsPanel";

export class ToolPanelColumnGroupComp extends Component implements BaseColumnItem {

    private static TEMPLATE =
        `<div class="ag-column-tool-panel-column-group">
            <span class="ag-column-group-icons" ref="eColumnGroupIcons" >
                <span class="ag-column-group-closed-icon" ref="eGroupOpenedIcon"></span>
                <span class="ag-column-group-opened-icon" ref="eGroupClosedIcon"></span>
            </span>
            <ag-checkbox ref="cbSelect" class="ag-column-select-checkbox"></ag-checkbox>
            <span class="ag-column-drag" ref="eDragHandle"></span>
            <span class="ag-column-tool-panel-column-group" ref="eLabel"></span>
        </div>`;

    @Autowired('gridOptionsWrapper') private gridOptionsWrapper: GridOptionsWrapper;
    @Autowired('columnController') private columnController: ColumnController;
    @Autowired('dragAndDropService') private dragAndDropService: DragAndDropService;
    @Autowired('eventService') private eventService: EventService;

    @RefSelector('cbSelect') private cbSelect: AgCheckbox;
    @RefSelector('eDragHandle') private eDragHandle: HTMLElement;
    @RefSelector('eLabel') private eLabel: HTMLElement;

    @RefSelector('eGroupOpenedIcon') private eGroupOpenedIcon: HTMLElement;
    @RefSelector('eGroupClosedIcon') private eGroupClosedIcon: HTMLElement;
    @RefSelector('eColumnGroupIcons') private eColumnGroupIcons: HTMLElement;

    private columnGroup: OriginalColumnGroup;
    private expanded: boolean;
    private columnDept: number;

    private expandedCallback: () => void;

    private allowDragging: boolean;

    private displayName: string | null;

    private processingColumnStateChange = false;
    private selectionCallback: (selected: boolean) => void;

    constructor(columnGroup: OriginalColumnGroup, columnDept: number, expandedCallback: () => void, allowDragging: boolean, expandByDefault: boolean) {
        super();
        this.columnGroup = columnGroup;
        this.columnDept = columnDept;
        this.expandedCallback = expandedCallback;
        this.allowDragging = allowDragging;
        this.expanded = expandByDefault;
    }

    @PostConstruct
    public init(): void {
        this.setTemplate(ToolPanelColumnGroupComp.TEMPLATE);

        // this.displayName = this.columnGroup.getColGroupDef() ? this.columnGroup.getColGroupDef().headerName : null;
        this.displayName = this.columnController.getDisplayNameForOriginalColumnGroup(null, this.columnGroup, 'toolPanel');

        if (_.missing(this.displayName)) {
            this.displayName = '>>';
        }

        this.eLabel.innerHTML = this.displayName ? this.displayName : '';
        this.setupExpandContract();

        this.addCssClass('ag-toolpanel-indent-' + this.columnDept);

        this.addDestroyableEventListener(this.eventService, Events.EVENT_COLUMN_PIVOT_MODE_CHANGED, this.onColumnStateChanged.bind(this));

        this.addDestroyableEventListener(this.eLabel, 'click', this.onLabelClicked.bind(this));
        this.addDestroyableEventListener(this.cbSelect, 'change', this.onCheckboxChanged.bind(this));

        this.setOpenClosedIcons();

        this.setupDragging();

        this.onColumnStateChanged();
        this.addVisibilityListenersToAllChildren();

        CssClassApplier.addToolPanelClassesFromColDef(this.columnGroup.getColGroupDef(), this.getGui(), this.gridOptionsWrapper, null, this.columnGroup);
    }

    private addVisibilityListenersToAllChildren(): void {
        this.columnGroup.getLeafColumns().forEach(column => {
            this.addDestroyableEventListener(column, Column.EVENT_VISIBLE_CHANGED, this.onColumnStateChanged.bind(this));
            this.addDestroyableEventListener(column, Column.EVENT_VALUE_CHANGED, this.onColumnStateChanged.bind(this));
            this.addDestroyableEventListener(column, Column.EVENT_PIVOT_CHANGED, this.onColumnStateChanged.bind(this));
            this.addDestroyableEventListener(column, Column.EVENT_ROW_GROUP_CHANGED, this.onColumnStateChanged.bind(this));
        });
    }

    private setupDragging(): void {

        if (!this.allowDragging) {
            _.setVisible(this.eDragHandle, false);
            return;
        }

        const dragSource: DragSource = {
            type: DragSourceType.ToolPanel,
            eElement: this.eDragHandle,
            dragItemName: this.displayName,
            dragItemCallback: () => this.createDragItem()
        };
        this.dragAndDropService.addDragSource(dragSource, true);
        this.addDestroyFunc(() => this.dragAndDropService.removeDragSource(dragSource));
    }

    private createDragItem() {
        const visibleState: { [key: string]: boolean } = {};
        this.columnGroup.getLeafColumns().forEach(col => {
            visibleState[col.getId()] = col.isVisible();
        });

        return {
            columns: this.columnGroup.getLeafColumns(),
            visibleState: visibleState
        };
    }

    private setupExpandContract(): void {
        this.eGroupClosedIcon.appendChild(_.createIcon('columnSelectClosed', this.gridOptionsWrapper, null));
        this.eGroupOpenedIcon.appendChild(_.createIcon('columnSelectOpen', this.gridOptionsWrapper, null));

        this.addDestroyableEventListener(this.eGroupClosedIcon, 'click', this.onExpandOrContractClicked.bind(this));
        this.addDestroyableEventListener(this.eGroupOpenedIcon, 'click', this.onExpandOrContractClicked.bind(this));

        const touchListener = new TouchListener(this.eColumnGroupIcons, true);
        this.addDestroyableEventListener(touchListener, TouchListener.EVENT_TAP, this.onExpandOrContractClicked.bind(this));
        this.addDestroyFunc(touchListener.destroy.bind(touchListener));
    }

    private onLabelClicked(): void {
        const nextState = !this.cbSelect.isSelected();
        this.onChangeCommon(nextState);
    }

    private onCheckboxChanged(event: any): void {
        this.onChangeCommon(event.selected);
    }

    private onChangeCommon(nextState: boolean): void {
        if (this.processingColumnStateChange) {
            return;
        }

        const childColumns = this.columnGroup.getLeafColumns();

        if (this.columnController.isPivotMode()) {
            if (nextState) {
                this.actionCheckedReduce(childColumns);
            } else {
                this.actionUnCheckedReduce(childColumns);
            }
        } else {
            const isAllowedColumn = (c: Column) => !c.isLockVisible() && !c.getColDef().suppressToolPanel;
            const allowedColumns = childColumns.filter(isAllowedColumn);
            this.columnController.setColumnsVisible(allowedColumns, nextState, "toolPanelUi");
        }

        if (this.selectionCallback) {
            this.selectionCallback(this.isSelected());
        }
    }

    private actionUnCheckedReduce(columns: Column[]): void {

        const columnsToUnPivot: Column[] = [];
        const columnsToUnValue: Column[] = [];
        const columnsToUnGroup: Column[] = [];

        columns.forEach(column => {
            if (column.isPivotActive()) {
                columnsToUnPivot.push(column);
            }
            if (column.isRowGroupActive()) {
                columnsToUnGroup.push(column);
            }
            if (column.isValueActive()) {
                columnsToUnValue.push(column);
            }
        });

        if (columnsToUnPivot.length > 0) {
            this.columnController.removePivotColumns(columnsToUnPivot, "toolPanelUi");
        }
        if (columnsToUnGroup.length > 0) {
            this.columnController.removeRowGroupColumns(columnsToUnGroup, "toolPanelUi");
        }
        if (columnsToUnValue.length > 0) {
            this.columnController.removeValueColumns(columnsToUnValue, "toolPanelUi");
        }
    }

    private actionCheckedReduce(columns: Column[]): void {

        const columnsToAggregate: Column[] = [];
        const columnsToGroup: Column[] = [];
        const columnsToPivot: Column[] = [];

        columns.forEach(column => {
            // don't change any column that's already got a function active
            if (column.isAnyFunctionActive()) {
                return;
            }

            if (column.isAllowValue()) {
                columnsToAggregate.push(column);
            } else if (column.isAllowRowGroup()) {
                columnsToGroup.push(column);
            } else if (column.isAllowRowGroup()) {
                columnsToPivot.push(column);
            }

        });

        if (columnsToAggregate.length > 0) {
            this.columnController.addValueColumns(columnsToAggregate, "toolPanelUi");
        }
        if (columnsToGroup.length > 0) {
            this.columnController.addRowGroupColumns(columnsToGroup, "toolPanelUi");
        }
        if (columnsToPivot.length > 0) {
            this.columnController.addPivotColumns(columnsToPivot, "toolPanelUi");
        }

    }

    private onColumnStateChanged(): void {
        const selectedValue = this.workOutSelectedValue();
        const readOnlyValue = this.workOutReadOnlyValue();
        this.processingColumnStateChange = true;
        this.cbSelect.setSelected(selectedValue);
        if (this.selectionCallback) {
            this.selectionCallback(this.isSelected());
        }
        this.cbSelect.setReadOnly(readOnlyValue);
        this.processingColumnStateChange = false;
    }

    private workOutReadOnlyValue(): boolean {
        const pivotMode = this.columnController.isPivotMode();

        let colsThatCanAction = 0;

        this.columnGroup.getLeafColumns().forEach(col => {
            if (pivotMode) {
                if (col.isAnyFunctionAllowed()) {
                    colsThatCanAction++;
                }
            } else {
                if (!col.isLockVisible()) {
                    colsThatCanAction++;
                }
            }
        });

        return colsThatCanAction === 0;
    }

    private workOutSelectedValue(): boolean | null {
        const pivotMode = this.columnController.isPivotMode();

        let visibleChildCount = 0;
        let hiddenChildCount = 0;

        this.columnGroup.getLeafColumns().forEach((column: Column) => {

            // ignore lock visible columns and columns set to 'suppressToolPanel'
            const ignoredColumn = column.isLockVisible() || column.getColDef().suppressToolPanel;
            if (ignoredColumn) return null;

            if (this.isColumnVisible(column, pivotMode)) {
                visibleChildCount++;
            } else {
                hiddenChildCount++;
            }
        });

        let selectedValue: boolean | null;
        if (visibleChildCount > 0 && hiddenChildCount > 0) {
            selectedValue = null;
        } else if (visibleChildCount > 0) {
            selectedValue = true;
        } else {
            selectedValue = false;
        }

        return selectedValue;
    }

    private isColumnVisible(column: Column, pivotMode: boolean): boolean {
        if (pivotMode) {
            const pivoted = column.isPivotActive();
            const grouped = column.isRowGroupActive();
            const aggregated = column.isValueActive();
            return pivoted || grouped || aggregated;
        } else {
            return column.isVisible();
        }
    }

    private onExpandOrContractClicked(): void {
        this.expanded = !this.expanded;
        this.setOpenClosedIcons();
        this.expandedCallback();
    }

    private setOpenClosedIcons(): void {
        const folderOpen = this.expanded;
        _.setVisible(this.eGroupClosedIcon, !folderOpen);
        _.setVisible(this.eGroupOpenedIcon, folderOpen);
    }

    public isExpanded(): boolean {
        return this.expanded;
    }

    public getDisplayName(): string | null {
        return this.displayName;
    }

    public onSelectAllChanged(value: boolean): void {
        if (
            (value && !this.cbSelect.isSelected()) ||
            (!value && this.cbSelect.isSelected())
        ) {
            if (!this.cbSelect.isReadOnly()) {
                this.cbSelect.toggle();
            }
        }
    }

    public isSelected(): boolean {
        return this.cbSelect.isSelected();
    }

    public isSelectable(): boolean {
        return !this.cbSelect.isReadOnly();
    }

    public isExpandable(): boolean {
        return true;
    }

    public setExpanded(value: boolean): void {
        if (this.expanded !== value) {
            this.onExpandOrContractClicked();
        }
    }
}
