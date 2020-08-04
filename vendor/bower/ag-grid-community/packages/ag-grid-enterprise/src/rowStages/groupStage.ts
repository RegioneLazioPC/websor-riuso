import {
    Autowired,
    Bean,
    ChangedPath,
    Column,
    ColumnController,
    Context,
    EventService,
    GetDataPath,
    GridOptionsWrapper,
    IRowNodeStage,
    NumberSequence,
    PostConstruct,
    RowNode,
    RowNodeTransaction,
    SelectableService,
    SelectionController,
    StageExecuteParams,
    ValueService,
    _
} from "ag-grid-community";

interface GroupInfo {
    key: string; // e.g. 'Ireland'
    field: string | null; // e.g. 'country'
    rowGroupColumn: Column | null;
}

interface GroupingDetails {
    pivotMode: boolean;
    includeParents: boolean;
    expandByDefault: number;
    changedPath: ChangedPath;
    rootNode: RowNode;
    groupedCols: Column[];
    groupedColCount: number;
    transaction: RowNodeTransaction;
    rowNodeOrder: { [id: string]: number };
    afterColumnsChanged: boolean;
}

@Bean('groupStage')
export class GroupStage implements IRowNodeStage {

    @Autowired('selectionController') private selectionController: SelectionController;
    @Autowired('gridOptionsWrapper') private gridOptionsWrapper: GridOptionsWrapper;
    @Autowired('columnController') private columnController: ColumnController;
    @Autowired('selectableService') private selectableService: SelectableService;
    @Autowired('valueService') private valueService: ValueService;
    @Autowired('eventService') private eventService: EventService;
    @Autowired('context') private context: Context;

    // if doing tree data, this is true. we set this at create time - as our code does not
    // cater for the scenario where this is switched on / off dynamically
    private usingTreeData: boolean;
    private getDataPath: GetDataPath | undefined;

    // we use a sequence variable so that each time we do a grouping, we don't
    // reuse the ids - otherwise the rowRenderer will confuse rowNodes between redraws
    // when it tries to animate between rows. we set to -1 as others row id 0 will be shared
    // with the other rows.
    private groupIdSequence = new NumberSequence(1);

    // when grouping, these items are of note:
    // rowNode.parent: RowNode: set to the parent
    // rowNode.childrenAfterGroup: RowNode[] = the direct children of this group
    // rowNode.childrenMapped: string=>RowNode = children mapped by group key (when groups) or an empty map if leaf group (this is then used by pivot)
    // for leaf groups, rowNode.childrenAfterGroup = rowNode.allLeafChildren;

    @PostConstruct
    private postConstruct(): void {
        this.usingTreeData = this.gridOptionsWrapper.isTreeData();
        if (this.usingTreeData) {
            this.getDataPath = this.gridOptionsWrapper.getDataPathFunc();
        }
    }

    public execute(params: StageExecuteParams): void {

        const details = this.createGroupingDetails(params);

        if (details.transaction) {
            this.handleTransaction(details);
        } else {
            const afterColsChanged = params.afterColumnsChanged === true;
            this.shotgunResetEverything(details, afterColsChanged);
        }

        this.sortGroupsWithComparator(details.rootNode);

        this.selectableService.updateSelectableAfterGrouping(details.rootNode);
    }

    private createGroupingDetails(params: StageExecuteParams): GroupingDetails {
        const {rowNode, changedPath, rowNodeTransaction, rowNodeOrder} = params;

        const groupedCols = this.usingTreeData ? null : this.columnController.getRowGroupColumns();
        const isGrouping = this.usingTreeData || (groupedCols && groupedCols.length > 0);
        const usingTransaction = isGrouping && _.exists(rowNodeTransaction);

        const details = {
            // someone complained that the parent attribute was causing some change detection
            // to break is some angular add-on - which i never used. taking the parent out breaks
            // a cyclic dependency, hence this flag got introduced.
            includeParents: !this.gridOptionsWrapper.isSuppressParentsInRowNodes(),
            expandByDefault: this.gridOptionsWrapper.isGroupSuppressRow() ?
                -1 : this.gridOptionsWrapper.getGroupDefaultExpanded(),
            groupedCols: groupedCols,
            rootNode: rowNode,
            pivotMode: this.columnController.isPivotMode(),
            groupedColCount: this.usingTreeData || !groupedCols ? 0 : groupedCols.length,
            rowNodeOrder: rowNodeOrder,

            // important not to do transaction if we are not grouping, as otherwise the 'insert index' is ignored.
            // ie, if not grouping, then we just want to shotgun so the rootNode.allLeafChildren gets copied
            // to rootNode.childrenAfterGroup and maintaining order (as delta transaction misses the order).
            transaction: usingTransaction ? rowNodeTransaction : null,

            // if no transaction, then it's shotgun, changed path would be 'not active' at this point anyway
            changedPath: changedPath
        } as GroupingDetails;

        return details;
    }

    private handleTransaction(details: GroupingDetails): void {
        const tran = details.transaction;
        if (tran.add) {
            this.insertNodes(tran.add, details);
        }
        if (tran.update) {
            this.moveNodesInWrongPath(tran.update, details);
        }
        if (tran.remove) {
            this.removeNodes(tran.remove, details);
        }
        if (details.rowNodeOrder) {
            this.sortChildren(details);
        }
    }

    // this is used when doing delta updates, eg Redux, keeps nodes in right order
    private sortChildren(details: GroupingDetails): void {
        details.changedPath.forEachChangedNodeDepthFirst( rowNode => {
            _.sortRowNodesByOrder(rowNode.childrenAfterGroup, details.rowNodeOrder);
        });
    }

    private sortGroupsWithComparator(rootNode: RowNode): void {
        // we don't do group sorting for tree data
        if (this.usingTreeData) {
            return;
        }

        const comparator = this.gridOptionsWrapper.getDefaultGroupSortComparator();
        if (_.exists(comparator)) {
            recursiveSort(rootNode);
        }

        function recursiveSort(rowNode: RowNode): void {
            const doSort = _.exists(rowNode.childrenAfterGroup) &&
                // we only want to sort groups, so we do not sort leafs (a leaf group has leafs as children)
                !rowNode.leafGroup;

            if (doSort) {
                rowNode.childrenAfterGroup.sort(comparator);
                rowNode.childrenAfterGroup.forEach((childNode: RowNode) => recursiveSort(childNode));
            }
        }
    }

    private getExistingPathForNode(node: RowNode, details: GroupingDetails): GroupInfo[] {
        const res: GroupInfo[] = [];

        // when doing tree data, the node is part of the path,
        // but when doing grid grouping, the node is not part of the path so we start with the parent.
        let pointer = this.usingTreeData ? node : node.parent;
        while (pointer && pointer !== details.rootNode) {
            res.push({
                key: pointer.key,
                rowGroupColumn: pointer.rowGroupColumn,
                field: pointer.field
            });
            pointer = pointer.parent;
        }
        res.reverse();
        return res;
    }

    private moveNodesInWrongPath(childNodes: RowNode[], details: GroupingDetails): void {

        childNodes.forEach(childNode => {

            // we add node, even if parent has not changed, as the data could have
            // changed, hence aggregations will be wrong
            if (details.changedPath.isActive()) {
                details.changedPath.addParentNode(childNode.parent);
            }

            const infoToKeyMapper = (item: GroupInfo) => item.key;
            const oldPath: string[] = this.getExistingPathForNode(childNode, details).map(infoToKeyMapper);
            const newPath: string[] = this.getGroupInfo(childNode, details).map(infoToKeyMapper);

            const nodeInCorrectPath = _.compareArrays(oldPath, newPath);

            if (!nodeInCorrectPath) {
                this.moveNode(childNode, details);
            }
        });
    }

    private moveNode(childNode: RowNode, details: GroupingDetails): void {

        this.removeOneNode(childNode, details);
        this.insertOneNode(childNode, details);

        // hack - if we didn't do this, then renaming a tree item (ie changing rowNode.key) wouldn't get
        // refreshed into the gui.
        // this is needed to kick off the event that rowComp listens to for refresh. this in turn
        // then will get each cell in the row to refresh - which is what we need as we don't know which
        // columns will be displaying the rowNode.key info.
        childNode.setData(childNode.data);

        // we add both old and new parents to changed path, as both will need to be refreshed.
        // we already added the old parent (in calling method), so just add the new parent here
        if (details.changedPath.isActive()) {
            const newParent = childNode.parent;
            details.changedPath.addParentNode(newParent);
        }
    }

    private removeNodes(leafRowNodes: RowNode[], details: GroupingDetails): void {
        leafRowNodes.forEach(leafToRemove => {
            this.removeOneNode(leafToRemove, details);
            if (details.changedPath.isActive()) {
                details.changedPath.addParentNode(leafToRemove.parent);
            }
        });
    }

    private removeOneNode(childNode: RowNode, details: GroupingDetails): void {

        // utility func to execute once on each parent node
        const forEachParentGroup = (callback: (parent: RowNode) => void) => {
            let pointer = childNode.parent;
            while (pointer && pointer !== details.rootNode) {
                callback(pointer);
                pointer = pointer.parent;
            }
        };

        // remove leaf from direct parent
        this.removeFromParent(childNode);

        // remove from allLeafChildren
        forEachParentGroup(parentNode => _.removeFromArray(parentNode.allLeafChildren, childNode));

        // if not group, and children are present, need to move children to a group.
        // otherwise if no children, we can just remove without replacing.
        const replaceWithGroup = childNode.hasChildren();
        if (replaceWithGroup) {
            const oldPath = this.getExistingPathForNode(childNode, details);
            // because we just removed the userGroup, this will always return new support group
            const newGroupNode = this.findParentForNode(childNode, oldPath, details);

            // these properties are the ones that will be incorrect in the newly created group,
            // so copy them form the old childNode
            newGroupNode.expanded = childNode.expanded;
            newGroupNode.allLeafChildren = childNode.allLeafChildren;
            newGroupNode.childrenAfterGroup = childNode.childrenAfterGroup;
            newGroupNode.childrenMapped = childNode.childrenMapped;

            newGroupNode.childrenAfterGroup.forEach((rowNode: RowNode) => rowNode.parent = newGroupNode);
        }

        // remove empty groups
        forEachParentGroup(node => {
            if (node.isEmptyFillerNode()) {
                this.removeFromParent(node);
                // we remove selection on filler nodes here, as the selection would not be removed
                // from the RowNodeManager, as filler nodes don't exist on teh RowNodeManager
                node.setSelected(false);
            }
        });
    }

    private removeFromParent(child: RowNode) {
        if (child.parent) {
            _.removeFromArray(child.parent.childrenAfterGroup, child);
        }
        const mapKey = this.getChildrenMappedKey(child.key, child.rowGroupColumn);
        if (child.parent && child.parent.childrenMapped) {
            child.parent.childrenMapped[mapKey] = undefined;
        }
        // this is important for transition, see rowComp removeFirstPassFuncs. when doing animation and
        // remove, if rowTop is still present, the rowComp thinks it's just moved position.
        child.setRowTop(null);
    }

    private addToParent(child: RowNode, parent: RowNode | null) {
        const mapKey = this.getChildrenMappedKey(child.key, child.rowGroupColumn);
        if (parent) {
            if (parent.childrenMapped) {
                parent.childrenMapped[mapKey] = child;
            }
            parent.childrenAfterGroup.push(child);
        }
    }

    private oldGroupingDetails: GroupingDetails;

    private areGroupColsEqual(d1: GroupingDetails, d2: GroupingDetails): boolean {

        if (d1 == null || d2 == null) { return false; }

        if (d1.pivotMode!==d2.pivotMode) { return false; }

        if (!_.compareArrays(d1.groupedCols, d2.groupedCols)) { return false; }

        return true;
    }

    private shotgunResetEverything(details: GroupingDetails, afterColumnsChanged: boolean): void {

        const skipStage = afterColumnsChanged ?
            this.usingTreeData || this.areGroupColsEqual(details, this.oldGroupingDetails)
            : false;
        this.oldGroupingDetails = details;
        if (skipStage) { return; }

        // because we are not creating the root node each time, we have the logic
        // here to change leafGroup once.
        // we set .leafGroup to false for tree data, as .leafGroup is only used when pivoting, and pivoting
        // isn't allowed with treeData, so the grid never actually use .leafGroup when doing treeData.
        details.rootNode.leafGroup = this.usingTreeData ? false : details.groupedCols.length === 0;

        // we are doing everything from scratch, so reset childrenAfterGroup and childrenMapped from the rootNode
        details.rootNode.childrenAfterGroup = [];
        details.rootNode.childrenMapped = {};

        this.insertNodes(details.rootNode.allLeafChildren, details);
    }

    private insertNodes(newRowNodes: RowNode[], details: GroupingDetails): void {
        newRowNodes.forEach(rowNode => {
            this.insertOneNode(rowNode, details);
            if (details.changedPath.isActive()) {
                details.changedPath.addParentNode(rowNode.parent);
            }
        });
    }

    private insertOneNode(childNode: RowNode, details: GroupingDetails): void {

        const path: GroupInfo[] = this.getGroupInfo(childNode, details);

        const parentGroup = this.findParentForNode(childNode, path, details);
        if (!parentGroup.group) {
            console.warn(`ag-Grid: duplicate group keys for row data, keys should be unique`,
                [parentGroup.data, childNode.data]);
        }

        if (this.usingTreeData) {
            this.swapGroupWithUserNode(parentGroup, childNode);
        } else {
            childNode.parent = parentGroup;
            childNode.level = path.length;
            parentGroup.childrenAfterGroup.push(childNode);
        }
    }

    private findParentForNode(childNode: RowNode, path: GroupInfo[], details: GroupingDetails): RowNode {
        let nextNode: RowNode = details.rootNode;

        path.forEach((groupInfo, level) => {
            nextNode = this.getOrCreateNextNode(nextNode, groupInfo, level, details);
            // node gets added to all group nodes.
            // note: we do not add to rootNode here, as the rootNode is the master list of rowNodes
            nextNode.allLeafChildren.push(childNode);
        });

        return nextNode;
    }

    private swapGroupWithUserNode(fillerGroup: RowNode, userGroup: RowNode) {
        userGroup.parent = fillerGroup.parent;
        userGroup.key = fillerGroup.key;
        userGroup.field = fillerGroup.field;
        userGroup.groupData = fillerGroup.groupData;
        userGroup.level = fillerGroup.level;
        userGroup.expanded = fillerGroup.expanded;

        // we set .leafGroup to false for tree data, as .leafGroup is only used when pivoting, and pivoting
        // isn't allowed with treeData, so the grid never actually use .leafGroup when doing treeData.
        userGroup.leafGroup = fillerGroup.leafGroup;

        // always null for userGroups, as row grouping is not allowed when doing tree data
        userGroup.rowGroupIndex = fillerGroup.rowGroupIndex;

        userGroup.allLeafChildren = fillerGroup.allLeafChildren;
        userGroup.childrenAfterGroup = fillerGroup.childrenAfterGroup;
        userGroup.childrenMapped = fillerGroup.childrenMapped;

        this.removeFromParent(fillerGroup);
        userGroup.childrenAfterGroup.forEach((rowNode: RowNode) => rowNode.parent = userGroup);
        this.addToParent(userGroup, fillerGroup.parent);
    }

    private getOrCreateNextNode(parentGroup: RowNode, groupInfo: GroupInfo, level: number,
                                details: GroupingDetails): RowNode {

        const mapKey = this.getChildrenMappedKey(groupInfo.key, groupInfo.rowGroupColumn);
        let nextNode = parentGroup.childrenMapped ? parentGroup.childrenMapped[mapKey] as RowNode : undefined;
        if (!nextNode) {
            nextNode = this.createGroup(groupInfo, parentGroup, level, details);
            // attach the new group to the parent
            this.addToParent(nextNode, parentGroup);
        }

        return nextNode;
    }

    private createGroup(groupInfo: GroupInfo, parent: RowNode, level: number, details: GroupingDetails): RowNode {
        const groupNode = new RowNode();
        this.context.wireBean(groupNode);

        groupNode.group = true;
        groupNode.field = groupInfo.field;
        groupNode.rowGroupColumn = groupInfo.rowGroupColumn;
        groupNode.groupData = {};

        const groupDisplayCols: Column[] = this.columnController.getGroupDisplayColumns();

        groupDisplayCols.forEach(col => {
            // newGroup.rowGroupColumn=null when working off GroupInfo, and we always display the group in the group column
            // if rowGroupColumn is present, then it's grid row grouping and we only include if configuration says so
            const displayGroupForCol = this.usingTreeData || (groupNode.rowGroupColumn ? col.isRowGroupDisplayed(groupNode.rowGroupColumn.getId()) : false);
            if (displayGroupForCol) {
                groupNode.groupData[col.getColId()] = groupInfo.key;
            }
        });

        // we use negative number for the ids of the groups, this makes sure we don't clash with the
        // id's of the leaf nodes.
        groupNode.id = (this.groupIdSequence.next() * -1).toString();
        groupNode.key = groupInfo.key;

        groupNode.level = level;
        groupNode.leafGroup = this.usingTreeData ? false : level === (details.groupedColCount - 1);

        // if doing pivoting, then the leaf group is never expanded,
        // as we do not show leaf rows
        if (details.pivotMode && groupNode.leafGroup) {
            groupNode.expanded = false;
        } else {
            groupNode.expanded = this.isExpanded(details.expandByDefault, level);
        }

        groupNode.allLeafChildren = [];
        // why is this done here? we are not updating the children could as we go,
        // i suspect this is updated in the filter stage

        groupNode.setAllChildrenCount(0);

        groupNode.rowGroupIndex = this.usingTreeData ? null : level;

        groupNode.childrenAfterGroup = [];
        groupNode.childrenMapped = {};

        groupNode.parent = details.includeParents ? parent : null;

        return groupNode;
    }

    private getChildrenMappedKey(key: string, rowGroupColumn: Column | null): string {
        if (rowGroupColumn) {
            // grouping by columns
            return rowGroupColumn.getId() + '-' + key;
        } else {
            // tree data - we don't have rowGroupColumns
            return key;
        }
    }

    private isExpanded(expandByDefault: number, level: number) {
        if (expandByDefault === -1) {
            return true;
        } else {
            return level < expandByDefault;
        }
    }

    private getGroupInfo(rowNode: RowNode, details: GroupingDetails): GroupInfo[] {
        if (this.usingTreeData) {
            return this.getGroupInfoFromCallback(rowNode);
        } else {
            return this.getGroupInfoFromGroupColumns(rowNode, details);
        }
    }

    private getGroupInfoFromCallback(rowNode: RowNode): GroupInfo[] {
        let keys: (string | null)[] = [];
        if (this.getDataPath) {
            let path = this.getDataPath(rowNode.data);
            if (path) {
                // sanitize
                keys = path.map(p => _.escape(p));
            }
        }

        if (keys === null || keys === undefined || keys.length === 0) {
            _.doOnce(
                () => console.warn(`getDataPath() should not return an empty path for data`, rowNode.data),
                'groupStage.getGroupInfoFromCallback'
            );
        }
        const groupInfoMapper = (key: string | null) => ({key, field: null, rowGroupColumn: null}) as GroupInfo;
        return keys ? keys.map(groupInfoMapper) : [];
    }

    private getGroupInfoFromGroupColumns(rowNode: RowNode, details: GroupingDetails) {
        const res: GroupInfo[] = [];
        details.groupedCols.forEach(groupCol => {
            let key: string = this.valueService.getKeyForNode(groupCol, rowNode);
            let keyExists = key !== null && key !== undefined;

            // unbalanced tree and pivot mode don't work together - not because of the grid, it doesn't make
            // mathematical sense as you are building up a cube. so if pivot mode, we put in a blank key where missing.
            // this keeps the tree balanced and hence can be represented as a group.
            if (details.pivotMode && !keyExists) {
                key = ' ';
                keyExists = true;
            }

            if (keyExists) {
                const item = {
                    key: key,
                    field: groupCol.getColDef().field,
                    rowGroupColumn: groupCol
                } as GroupInfo;
                res.push(item);
            }
        });
        return res;
    }
}
