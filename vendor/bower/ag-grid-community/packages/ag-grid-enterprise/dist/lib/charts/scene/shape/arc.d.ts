// ag-grid-enterprise v20.2.0
import { Shape } from "./shape";
import { Path2D } from "../path2D";
import { BBox } from "../bbox";
export declare enum ArcType {
    Open = 0,
    Chord = 1,
    Round = 2
}
/**
 * Elliptical arc node.
 */
export declare class Arc extends Shape {
    protected static defaultStyles: {
        fillStyle: string | null;
        strokeStyle: string | null;
        lineWidth: number;
        lineDash: number[] | null;
        lineDashOffset: number;
        lineCap: import("./shape").ShapeLineCap;
        lineJoin: import("./shape").ShapeLineJoin;
        opacity: number;
        shadow: import("../dropShadow").DropShadow | null;
    } & {
        lineWidth: number;
        fillStyle: null;
    };
    constructor();
    static create(centerX: number, centerY: number, radiusX: number, radiusY?: number, startAngle?: number, endAngle?: number, counterClockwise?: boolean): Arc;
    protected path: Path2D;
    /**
     * It's not always that the path has to be updated.
     * For example, if transform attributes (such as `translationX`)
     * are changed, we don't have to update the path. The `dirtyFlag`
     * is how we keep track if the path has to be updated or not.
     */
    private _dirtyPath;
    dirtyPath: boolean;
    private _centerX;
    centerX: number;
    private _centerY;
    centerY: number;
    private _radiusX;
    radiusX: number;
    private _radiusY;
    radiusY: number;
    private _startAngle;
    startAngle: number;
    private _endAngle;
    endAngle: number;
    private readonly fullPie;
    private _counterClockwise;
    counterClockwise: boolean;
    /**
     * The type of arc to render:
     * - {@link ArcType.Open} - end points of the arc segment are not connected (default)
     * - {@link ArcType.Chord} - end points of the arc segment are connected by a line segment
     * - {@link ArcType.Round} - each of the end points of the arc segment are connected
     *                           to the center of the arc
     * Arcs with {@link ArcType.Open} do not support hit testing, even if they have their
     * {@link Shape.fillStyle} set, because they are not closed paths. Hit testing support
     * would require using two paths - one for rendering, another for hit testing - and there
     * doesn't seem to be a compelling reason to do that, when one can just use {@link ArcType.Chord}
     * to create a closed path.
     */
    private _type;
    type: ArcType;
    updatePath(): void;
    readonly getBBox: () => BBox;
    isPointInPath(x: number, y: number): boolean;
    isPointInStroke(x: number, y: number): boolean;
    render(ctx: CanvasRenderingContext2D): void;
}
