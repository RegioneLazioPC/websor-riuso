/**
 * ag-grid-community - Advanced Data Grid / Data Table supporting Javascript / React / AngularJS / Web Components
 * @version v20.2.0
 * @link http://www.ag-grid.com/
 * @license MIT
 */
"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
Object.defineProperty(exports, "__esModule", { value: true });
var constants_1 = require("../constants");
var context_1 = require("../context/context");
var gridOptionsWrapper_1 = require("../gridOptionsWrapper");
var environment_1 = require("../environment");
var eventService_1 = require("../eventService");
var events_1 = require("../events");
var utils_1 = require("../utils");
var PopupService = /** @class */ (function () {
    function PopupService() {
        this.activePopupElements = [];
    }
    PopupService.prototype.registerGridCore = function (gridCore) {
        this.gridCore = gridCore;
    };
    PopupService.prototype.getDocument = function () {
        return this.gridOptionsWrapper.getDocument();
    };
    PopupService.prototype.getPopupParent = function () {
        var ePopupParent = this.gridOptionsWrapper.getPopupParent();
        if (ePopupParent) {
            // user provided popup parent, may not have the right theme applied
            return ePopupParent;
        }
        return this.gridCore.getRootGui();
    };
    PopupService.prototype.positionPopupForMenu = function (params) {
        var sourceRect = params.eventSource.getBoundingClientRect();
        var eDocument = this.getDocument();
        var popupParent = this.getPopupParent();
        var parentRect;
        if (popupParent === eDocument.body) {
            parentRect = eDocument.documentElement.getBoundingClientRect();
        }
        else {
            parentRect = popupParent.getBoundingClientRect();
        }
        var y = sourceRect.top - parentRect.top;
        y = this.keepYWithinBounds(params, y);
        var minWidth = (params.ePopup.clientWidth > 0) ? params.ePopup.clientWidth : 200;
        params.ePopup.style.minWidth = minWidth + "px";
        var widthOfParent = parentRect.right - parentRect.left;
        var maxX = widthOfParent - minWidth;
        // the x position of the popup depends on RTL or LTR. for normal cases, LTR, we put the child popup
        // to the right, unless it doesn't fit and we then put it to the left. for RTL it's the other way around,
        // we try place it first to the left, and then if not to the right.
        var x;
        if (this.gridOptionsWrapper.isEnableRtl()) {
            // for RTL, try left first
            x = xLeftPosition();
            if (x < 0) {
                x = xRightPosition();
            }
            if (x > maxX) {
                x = 0;
            }
        }
        else {
            // for LTR, try right first
            x = xRightPosition();
            if (x > maxX) {
                x = xLeftPosition();
            }
            if (x < 0) {
                x = 0;
            }
        }
        params.ePopup.style.left = x + "px";
        params.ePopup.style.top = y + "px";
        function xRightPosition() {
            return sourceRect.right - parentRect.left - 2;
        }
        function xLeftPosition() {
            return sourceRect.left - parentRect.left - minWidth;
        }
    };
    PopupService.prototype.positionPopupUnderMouseEvent = function (params) {
        var _a = this.calculatePointerAlign(params.mouseEvent), x = _a.x, y = _a.y;
        var ePopup = params.ePopup, nudgeX = params.nudgeX, nudgeY = params.nudgeY;
        this.positionPopup({
            ePopup: ePopup,
            x: x,
            y: y,
            nudgeX: nudgeX,
            nudgeY: nudgeY,
            keepWithinBounds: true
        });
        this.callPostProcessPopup(params.ePopup, null, params.mouseEvent, params.type, params.column, params.rowNode);
    };
    PopupService.prototype.calculatePointerAlign = function (e) {
        var eDocument = this.getDocument();
        var popupParent = this.getPopupParent();
        var parentRect = popupParent.getBoundingClientRect();
        var documentRect = eDocument.documentElement.getBoundingClientRect();
        return {
            x: e.clientX - (popupParent === eDocument.body ? documentRect.left : parentRect.left),
            y: e.clientY - (popupParent === eDocument.body ? documentRect.top : parentRect.top)
        };
    };
    PopupService.prototype.positionPopupUnderComponent = function (params) {
        var sourceRect = params.eventSource.getBoundingClientRect();
        var eDocument = this.getDocument();
        var popupParent = this.getPopupParent();
        var parentRect;
        if (popupParent === eDocument.body) {
            parentRect = eDocument.documentElement.getBoundingClientRect();
        }
        else {
            parentRect = popupParent.getBoundingClientRect();
        }
        this.positionPopup({
            ePopup: params.ePopup,
            minWidth: params.minWidth,
            minHeight: params.minHeight,
            nudgeX: params.nudgeX,
            nudgeY: params.nudgeY,
            x: sourceRect.left - parentRect.left,
            y: sourceRect.top - parentRect.top + sourceRect.height,
            keepWithinBounds: params.keepWithinBounds
        });
        this.callPostProcessPopup(params.ePopup, params.eventSource, null, params.type, params.column, params.rowNode);
    };
    PopupService.prototype.callPostProcessPopup = function (ePopup, eventSource, mouseEvent, type, column, rowNode) {
        var callback = this.gridOptionsWrapper.getPostProcessPopupFunc();
        if (callback) {
            var params = {
                column: column,
                rowNode: rowNode,
                ePopup: ePopup,
                type: type,
                eventSource: eventSource,
                mouseEvent: mouseEvent
            };
            callback(params);
        }
    };
    PopupService.prototype.positionPopupOverComponent = function (params) {
        var sourceRect = params.eventSource.getBoundingClientRect();
        var eDocument = this.getDocument();
        var popupParent = this.getPopupParent();
        var parentRect;
        if (popupParent === eDocument.body) {
            parentRect = eDocument.documentElement.getBoundingClientRect();
        }
        else {
            parentRect = popupParent.getBoundingClientRect();
        }
        this.positionPopup({
            ePopup: params.ePopup,
            minWidth: params.minWidth,
            nudgeX: params.nudgeX,
            nudgeY: params.nudgeY,
            x: sourceRect.left - parentRect.left,
            y: sourceRect.top - parentRect.top,
            keepWithinBounds: params.keepWithinBounds
        });
        this.callPostProcessPopup(params.ePopup, params.eventSource, null, params.type, params.column, params.rowNode);
    };
    PopupService.prototype.positionPopup = function (params) {
        var x = params.x;
        var y = params.y;
        if (params.nudgeX) {
            x += params.nudgeX;
        }
        if (params.nudgeY) {
            y += params.nudgeY;
        }
        // if popup is overflowing to the bottom, move it up
        if (params.keepWithinBounds) {
            x = this.keepXWithinBounds(params, x);
            y = this.keepYWithinBounds(params, y);
        }
        params.ePopup.style.left = x + "px";
        params.ePopup.style.top = y + "px";
    };
    PopupService.prototype.keepYWithinBounds = function (params, y) {
        var eDocument = this.gridOptionsWrapper.getDocument();
        var docElement = eDocument.documentElement;
        var popupParent = this.getPopupParent();
        var parentRect = popupParent.getBoundingClientRect();
        var documentRect = eDocument.documentElement.getBoundingClientRect();
        var isBody = popupParent === eDocument.body;
        var defaultPadding = 3;
        var minHeight = Math.min(200, parentRect.height);
        var diff = 0;
        if (params.minHeight && params.minHeight < minHeight) {
            minHeight = params.minHeight;
        }
        else if (params.ePopup.offsetHeight > 0) {
            minHeight = params.ePopup.clientHeight;
            diff = utils_1._.getAbsoluteHeight(params.ePopup) - minHeight;
        }
        var heightOfParent = isBody ? (utils_1._.getAbsoluteHeight(docElement) + docElement.scrollTop) : parentRect.height;
        if (isBody) {
            heightOfParent -= Math.abs(documentRect.top - parentRect.top);
        }
        var maxY = heightOfParent - minHeight - diff - defaultPadding;
        return Math.min(Math.max(y, 0), Math.abs(maxY));
    };
    PopupService.prototype.keepXWithinBounds = function (params, x) {
        var eDocument = this.gridOptionsWrapper.getDocument();
        var docElement = eDocument.documentElement;
        var popupParent = this.getPopupParent();
        var parentRect = popupParent.getBoundingClientRect();
        var documentRect = eDocument.documentElement.getBoundingClientRect();
        var isBody = popupParent === eDocument.body;
        var defaultPadding = 3;
        var minWidth = Math.min(200, parentRect.width);
        var diff = 0;
        if (params.minWidth && params.minWidth < minWidth) {
            minWidth = params.minWidth;
        }
        else if (params.ePopup.clientWidth > 0) {
            minWidth = params.ePopup.clientWidth;
            params.ePopup.style.minWidth = minWidth + "px";
            diff = utils_1._.getAbsoluteWidth(params.ePopup) - minWidth;
        }
        var widthOfParent = isBody ? (utils_1._.getAbsoluteWidth(docElement) + docElement.scrollLeft) : parentRect.width;
        if (isBody) {
            widthOfParent -= Math.abs(documentRect.left - parentRect.left);
        }
        var maxX = widthOfParent - minWidth - diff - defaultPadding;
        return Math.min(Math.max(x, 0), Math.abs(maxX));
    };
    //adds an element to a div, but also listens to background checking for clicks,
    //so that when the background is clicked, the child is removed again, giving
    //a model look to popups.
    PopupService.prototype.addAsModalPopup = function (eChild, closeOnEsc, closedCallback, click) {
        return this.addPopup(true, eChild, closeOnEsc, closedCallback, click);
    };
    PopupService.prototype.addPopup = function (modal, eChild, closeOnEsc, closedCallback, click) {
        var _this = this;
        var eDocument = this.gridOptionsWrapper.getDocument();
        if (!eDocument) {
            console.warn('ag-grid: could not find the document, document is empty');
            return function () {
            };
        }
        eChild.style.top = '0px';
        eChild.style.left = '0px';
        var ePopupParent = this.getPopupParent();
        var popupAlreadyShown = utils_1._.isVisible(eChild);
        if (popupAlreadyShown && ePopupParent.contains(eChild)) {
            return function () { };
        }
        // add env CSS class to child, in case user provided a popup parent, which means
        // theme class may be missing
        var eWrapper = document.createElement('div');
        var theme = this.environment.getTheme();
        if (theme) {
            utils_1._.addCssClass(eWrapper, theme);
        }
        eWrapper.appendChild(eChild);
        ePopupParent.appendChild(eWrapper);
        this.activePopupElements.push(eChild);
        var popupHidden = false;
        var hidePopupOnKeyboardEvent = function (event) {
            var key = event.which || event.keyCode;
            if (key === constants_1.Constants.KEY_ESCAPE) {
                hidePopup(null);
            }
        };
        var hidePopupOnMouseEvent = function (event) {
            hidePopup(event);
        };
        var hidePopupOnTouchEvent = function (event) {
            hidePopup(null, event);
        };
        var hidePopup = function (mouseEvent, touchEvent) {
            // we don't hide popup if the event was on the child, or any
            // children of this child
            if (_this.isEventFromCurrentPopup(mouseEvent, touchEvent, eChild)) {
                return;
            }
            // if the event to close is actually the open event, then ignore it
            if (_this.isEventSameChainAsOriginalEvent(click, mouseEvent, touchEvent)) {
                return;
            }
            // this method should only be called once. the client can have different
            // paths, each one wanting to close, so this method may be called multiple times.
            if (popupHidden) {
                return;
            }
            popupHidden = true;
            ePopupParent.removeChild(eWrapper);
            utils_1._.removeFromArray(_this.activePopupElements, eChild);
            eDocument.removeEventListener('keydown', hidePopupOnKeyboardEvent);
            eDocument.removeEventListener('click', hidePopupOnMouseEvent);
            eDocument.removeEventListener('touchstart', hidePopupOnTouchEvent);
            eDocument.removeEventListener('contextmenu', hidePopupOnMouseEvent);
            _this.eventService.removeEventListener(events_1.Events.EVENT_DRAG_STARTED, hidePopupOnMouseEvent);
            if (closedCallback) {
                closedCallback();
            }
        };
        // if we add these listeners now, then the current mouse
        // click will be included, which we don't want
        window.setTimeout(function () {
            if (closeOnEsc) {
                eDocument.addEventListener('keydown', hidePopupOnKeyboardEvent);
            }
            if (modal) {
                eDocument.addEventListener('click', hidePopupOnMouseEvent);
                _this.eventService.addEventListener(events_1.Events.EVENT_DRAG_STARTED, hidePopupOnMouseEvent);
                eDocument.addEventListener('touchstart', hidePopupOnTouchEvent);
                eDocument.addEventListener('contextmenu', hidePopupOnMouseEvent);
            }
        }, 0);
        return hidePopup;
    };
    PopupService.prototype.isEventFromCurrentPopup = function (mouseEvent, touchEvent, eChild) {
        var event = mouseEvent ? mouseEvent : touchEvent;
        if (event) {
            var indexOfThisChild = this.activePopupElements.indexOf(eChild);
            for (var i = indexOfThisChild; i < this.activePopupElements.length; i++) {
                var element = this.activePopupElements[i];
                if (utils_1._.isElementInEventPath(element, event)) {
                    return true;
                }
            }
            // if the user did not write their own Custom Element to be rendered as popup
            // and this component has additional popup element, they should have the
            // `ag-custom-component-popup` class to be detected as part of the Custom Component
            var el = event.target;
            while (el && el != document.body) {
                if (el.classList.contains('ag-custom-component-popup') || el.parentElement === null) {
                    return true;
                }
                el = el.parentElement;
            }
        }
        return false;
    };
    // in some browsers, the context menu event can be fired before the click event, which means
    // the context menu event could open the popup, but then the click event closes it straight away.
    PopupService.prototype.isEventSameChainAsOriginalEvent = function (originalClick, mouseEvent, touchEvent) {
        // we check the coordinates of the event, to see if it's the same event. there is a 1 / 1000 chance that
        // the event is a different event, however that is an edge case that is not very relevant (the user clicking
        // twice on the same location isn't a normal path).
        // event could be mouse event or touch event.
        var mouseEventOrTouch = null;
        if (mouseEvent) {
            // mouse event can be used direction, it has coordinates
            mouseEventOrTouch = mouseEvent;
        }
        else if (touchEvent) {
            // touch event doesn't have coordinates, need it's touch object
            mouseEventOrTouch = touchEvent.touches[0];
        }
        if (mouseEventOrTouch && originalClick) {
            // for x, allow 4px margin, to cover iPads, where touch (which opens menu) is followed
            // by browser click (when you life finger up, touch is interrupted as click in browser)
            var screenX_1 = mouseEvent ? mouseEvent.screenX : 0;
            var screenY_1 = mouseEvent ? mouseEvent.screenY : 0;
            var xMatch = Math.abs(originalClick.screenX - screenX_1) < 5;
            var yMatch = Math.abs(originalClick.screenY - screenY_1) < 5;
            if (xMatch && yMatch) {
                return true;
            }
        }
        return false;
    };
    __decorate([
        context_1.Autowired('gridOptionsWrapper'),
        __metadata("design:type", gridOptionsWrapper_1.GridOptionsWrapper)
    ], PopupService.prototype, "gridOptionsWrapper", void 0);
    __decorate([
        context_1.Autowired('environment'),
        __metadata("design:type", environment_1.Environment)
    ], PopupService.prototype, "environment", void 0);
    __decorate([
        context_1.Autowired('eventService'),
        __metadata("design:type", eventService_1.EventService)
    ], PopupService.prototype, "eventService", void 0);
    PopupService = __decorate([
        context_1.Bean('popupService')
    ], PopupService);
    return PopupService;
}());
exports.PopupService = PopupService;
