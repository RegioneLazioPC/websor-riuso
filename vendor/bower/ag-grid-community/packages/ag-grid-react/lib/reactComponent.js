// ag-grid-react v20.2.0
"use strict";
var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var React = require("react");
var ReactDOM = require("react-dom");
var AgGrid = require("ag-grid-community");
var ag_grid_community_1 = require("ag-grid-community");
var baseReactComponent_1 = require("./baseReactComponent");
var ReactComponent = /** @class */ (function (_super) {
    __extends(ReactComponent, _super);
    function ReactComponent(reactComponent, parentComponent) {
        var _this = _super.call(this) || this;
        _this.portal = null;
        _this.componentWrappingElement = 'div';
        _this.reactComponent = reactComponent;
        _this.parentComponent = parentComponent;
        _this.statelessComponent = ReactComponent.isStateless(_this.reactComponent);
        return _this;
    }
    ReactComponent.prototype.getFrameworkComponentInstance = function () {
        return this.componentInstance;
    };
    ReactComponent.prototype.isStatelesComponent = function () {
        return this.statelessComponent;
    };
    ReactComponent.prototype.getReactComponentName = function () {
        return this.reactComponent.name;
    };
    ReactComponent.prototype.init = function (params) {
        var _this = this;
        return new ag_grid_community_1.Promise(function (resolve) {
            _this.eParentElement = _this.createParentElement(params);
            _this.createReactComponent(params, resolve);
        });
    };
    ReactComponent.prototype.getGui = function () {
        return this.eParentElement;
    };
    ReactComponent.prototype.destroy = function () {
        return this.parentComponent.destroyPortal(this.portal);
    };
    ReactComponent.prototype.createReactComponent = function (params, resolve) {
        var _this = this;
        if (!this.statelessComponent) {
            // grab hold of the actual instance created - we use a react ref for this as there is no other mechanism to
            // retrieve the created instance from either createPortal or render
            params.ref = function (element) {
                _this.componentInstance = element;
            };
        }
        var ReactComponent = React.createElement(this.reactComponent, params);
        var portal = ReactDOM.createPortal(ReactComponent, this.eParentElement);
        this.portal = portal;
        this.parentComponent.mountReactPortal(portal, this, resolve);
    };
    ReactComponent.prototype.createParentElement = function (params) {
        var eParentElement = document.createElement(this.parentComponent.props.componentWrappingElement || 'div');
        AgGrid.Utils.addCssClass(eParentElement, 'ag-react-container');
        // so user can have access to the react container,
        // to add css class or style
        params.reactContainer = this.eParentElement;
        return eParentElement;
    };
    ReactComponent.isStateless = function (Component) {
        return (typeof Component === 'function' &&
            !(Component.prototype && Component.prototype.isReactComponent));
    };
    return ReactComponent;
}(baseReactComponent_1.BaseReactComponent));
exports.ReactComponent = ReactComponent;
