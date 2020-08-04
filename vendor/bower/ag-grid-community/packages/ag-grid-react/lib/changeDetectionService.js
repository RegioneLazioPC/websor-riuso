// ag-grid-react v20.2.0
"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var ChangeDetectionStrategyType;
(function (ChangeDetectionStrategyType) {
    ChangeDetectionStrategyType["IdentityCheck"] = "IdentityCheck";
    ChangeDetectionStrategyType["DeepValueCheck"] = "DeepValueCheck";
    ChangeDetectionStrategyType["NoCheck"] = "NoCheck";
})(ChangeDetectionStrategyType = exports.ChangeDetectionStrategyType || (exports.ChangeDetectionStrategyType = {}));
var ChangeDetectionService = /** @class */ (function () {
    function ChangeDetectionService() {
        var _a;
        this.strategyMap = (_a = {},
            _a[ChangeDetectionStrategyType.DeepValueCheck] = new DeepValueStrategy(),
            _a[ChangeDetectionStrategyType.IdentityCheck] = new SimpleFunctionalStrategy(function (a, b) { return a === b; }),
            _a[ChangeDetectionStrategyType.NoCheck] = new SimpleFunctionalStrategy(function (a, b) { return true; }),
            _a);
    }
    ChangeDetectionService.prototype.getStrategy = function (changeDetectionStrategy) {
        return this.strategyMap[changeDetectionStrategy];
    };
    return ChangeDetectionService;
}());
exports.ChangeDetectionService = ChangeDetectionService;
var SimpleFunctionalStrategy = /** @class */ (function () {
    function SimpleFunctionalStrategy(strategy) {
        this.strategy = strategy;
    }
    SimpleFunctionalStrategy.prototype.areEqual = function (a, b) {
        return this.strategy(a, b);
    };
    return SimpleFunctionalStrategy;
}());
var DeepValueStrategy = /** @class */ (function () {
    function DeepValueStrategy() {
    }
    DeepValueStrategy.prototype.areEqual = function (a, b) {
        return DeepValueStrategy.areEquivalent(DeepValueStrategy.copy(a), DeepValueStrategy.copy(b));
    };
    /*
     * deeper object comparison - taken from https://stackoverflow.com/questions/1068834/object-comparison-in-javascript
     */
    DeepValueStrategy.unwrapStringOrNumber = function (obj) {
        return obj instanceof Number || obj instanceof String ? obj.valueOf() : obj;
    };
    // sigh, here for ie compatibility
    DeepValueStrategy.copy = function (value) {
        if (!value) {
            return value;
        }
        if (Array.isArray(value)) {
            // shallow copy the array - this will typically be either rowData or columnDefs
            var arrayCopy = [];
            for (var i = 0; i < value.length; i++) {
                arrayCopy.push(this.copy(value[i]));
            }
            return arrayCopy;
        }
        // for anything without keys (boolean, string etc).
        // Object.keys - chrome will swallow them, IE will fail (correctly, imho)
        if (typeof value !== "object") {
            return value;
        }
        return [{}, value].reduce(function (r, o) {
            Object.keys(o).forEach(function (k) {
                r[k] = o[k];
            });
            return r;
        }, {});
    };
    /*
     * slightly modified, but taken from https://stackoverflow.com/questions/1068834/object-comparison-in-javascript
     *
     * What we're trying to do here is determine if the property being checked has changed in _value_, not just in reference
     *
     * For eg, if a user updates the columnDefs via property binding, but the actual columns defs are the same before and
     * after, then we don't want the grid to re-render
     */
    DeepValueStrategy.areEquivalent = function (a, b) {
        a = DeepValueStrategy.unwrapStringOrNumber(a);
        b = DeepValueStrategy.unwrapStringOrNumber(b);
        if (a === b)
            return true; //e.g. a and b both null
        if (a === null || b === null || typeof a !== typeof b)
            return false;
        if (a instanceof Date) {
            return b instanceof Date && a.valueOf() === b.valueOf();
        }
        if (typeof a === "function") {
            return a.toString() === b.toString();
        }
        if (typeof a !== "object") {
            return a == b; //for boolean, number, string, function, xml
        }
        var newA = a.areEquivPropertyTracking === undefined, newB = b.areEquivPropertyTracking === undefined;
        try {
            var prop = void 0;
            if (newA) {
                a.areEquivPropertyTracking = [];
            }
            else if (a.areEquivPropertyTracking.some(function (other) {
                return other === b;
            }))
                return true;
            if (newB) {
                b.areEquivPropertyTracking = [];
            }
            else if (b.areEquivPropertyTracking.some(function (other) { return other === a; })) {
                return true;
            }
            a.areEquivPropertyTracking.push(b);
            b.areEquivPropertyTracking.push(a);
            var tmp = {};
            for (prop in a)
                if (prop != "areEquivPropertyTracking") {
                    tmp[prop] = null;
                }
            for (prop in b)
                if (prop != "areEquivPropertyTracking") {
                    tmp[prop] = null;
                }
            for (prop in tmp) {
                if (!this.areEquivalent(a[prop], b[prop])) {
                    return false;
                }
            }
            return true;
        }
        finally {
            if (newA)
                delete a.areEquivPropertyTracking;
            if (newB)
                delete b.areEquivPropertyTracking;
        }
    };
    return DeepValueStrategy;
}());
