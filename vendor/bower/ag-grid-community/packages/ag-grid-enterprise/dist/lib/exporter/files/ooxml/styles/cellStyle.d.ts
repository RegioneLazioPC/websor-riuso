// ag-grid-enterprise v20.2.0
import { ExcelOOXMLTemplate } from 'ag-grid-community';
declare const borderFactory: ExcelOOXMLTemplate;
export default borderFactory;
export interface CellStyle {
    builtinId: number;
    name: string;
    xfId: number;
}
