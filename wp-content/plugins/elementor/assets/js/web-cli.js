/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../assets/dev/js/editor/utils/is-instanceof.js":
/*!******************************************************!*\
  !*** ../assets/dev/js/editor/utils/is-instanceof.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t.return || t.return(); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
/**
 * Some FileAPI objects such as FileList, DataTransferItem and DataTransferItemList has inconsistency with the retrieved
 * object (from events, etc.) and the actual JavaScript object so a regular instanceof doesn't work. This function can
 * check whether it's instanceof by using the objects constructor and prototype names.
 *
 * @param  object
 * @param  constructors
 * @return {boolean}
 */
var _default = exports["default"] = function _default(object, constructors) {
  constructors = Array.isArray(constructors) ? constructors : [constructors];
  var _iterator = _createForOfIteratorHelper(constructors),
    _step;
  try {
    for (_iterator.s(); !(_step = _iterator.n()).done;) {
      var _constructor = _step.value;
      if (object.constructor.name === _constructor.prototype[Symbol.toStringTag]) {
        return true;
      }
    }
  } catch (err) {
    _iterator.e(err);
  } finally {
    _iterator.f();
  }
  return false;
};

/***/ }),

/***/ "../assets/dev/js/modules/imports/args-object.js":
/*!*******************************************************!*\
  !*** ../assets/dev/js/modules/imports/args-object.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _instanceType = _interopRequireDefault(__webpack_require__(/*! ./instance-type */ "../assets/dev/js/modules/imports/instance-type.js"));
var _isInstanceof = _interopRequireDefault(__webpack_require__(/*! ../../editor/utils/is-instanceof */ "../assets/dev/js/editor/utils/is-instanceof.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var ArgsObject = exports["default"] = /*#__PURE__*/function (_InstanceType) {
  /**
   * Function constructor().
   *
   * Create ArgsObject.
   *
   * @param {{}} args
   */
  function ArgsObject(args) {
    var _this;
    (0, _classCallCheck2.default)(this, ArgsObject);
    _this = _callSuper(this, ArgsObject);
    _this.args = args;
    return _this;
  }

  /**
   * Function requireArgument().
   *
   * Validate property in args.
   *
   * @param {string} property
   * @param {{}}     args
   *
   * @throws {Error}
   */
  (0, _inherits2.default)(ArgsObject, _InstanceType);
  return (0, _createClass2.default)(ArgsObject, [{
    key: "requireArgument",
    value: function requireArgument(property) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this.args;
      if (!Object.prototype.hasOwnProperty.call(args, property)) {
        throw Error("".concat(property, " is required."));
      }
    }

    /**
     * Function requireArgumentType().
     *
     * Validate property in args using `type === typeof(args.whatever)`.
     *
     * @param {string} property
     * @param {string} type
     * @param {{}}     args
     *
     * @throws {Error}
     */
  }, {
    key: "requireArgumentType",
    value: function requireArgumentType(property, type) {
      var args = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : this.args;
      this.requireArgument(property, args);
      if ((0, _typeof2.default)(args[property]) !== type) {
        throw Error("".concat(property, " invalid type: ").concat(type, "."));
      }
    }

    /**
     * Function requireArgumentInstance().
     *
     * Validate property in args using `args.whatever instanceof instance`.
     *
     * @param {string} property
     * @param {*}      instance
     * @param {{}}     args
     *
     * @throws {Error}
     */
  }, {
    key: "requireArgumentInstance",
    value: function requireArgumentInstance(property, instance) {
      var args = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : this.args;
      this.requireArgument(property, args);
      if (!(args[property] instanceof instance) && !(0, _isInstanceof.default)(args[property], instance)) {
        throw Error("".concat(property, " invalid instance."));
      }
    }

    /**
     * Function requireArgumentConstructor().
     *
     * Validate property in args using `type === args.whatever.constructor`.
     *
     * @param {string} property
     * @param {*}      type
     * @param {{}}     args
     *
     * @throws {Error}
     */
  }, {
    key: "requireArgumentConstructor",
    value: function requireArgumentConstructor(property, type) {
      var args = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : this.args;
      this.requireArgument(property, args);

      // Note: Converting the constructor to string in order to avoid equation issues
      // due to different memory addresses between iframes (window.Object !== window.top.Object).
      if (args[property].constructor.toString() !== type.prototype.constructor.toString()) {
        throw Error("".concat(property, " invalid constructor type."));
      }
    }
  }], [{
    key: "getInstanceType",
    value: function getInstanceType() {
      return 'ArgsObject';
    }
  }]);
}(_instanceType.default);

/***/ }),

/***/ "../assets/dev/js/modules/imports/instance-type.js":
/*!*********************************************************!*\
  !*** ../assets/dev/js/modules/imports/instance-type.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var InstanceType = exports["default"] = /*#__PURE__*/function () {
  function InstanceType() {
    var _this = this;
    (0, _classCallCheck2.default)(this, InstanceType);
    // Since anonymous classes sometimes do not get validated by babel, do it manually.
    var target = this instanceof InstanceType ? this.constructor : void 0;
    var prototypes = [];
    while (target.__proto__ && target.__proto__.name) {
      prototypes.push(target.__proto__);
      target = target.__proto__;
    }
    prototypes.reverse().forEach(function (proto) {
      return _this instanceof proto;
    });
  }
  return (0, _createClass2.default)(InstanceType, null, [{
    key: Symbol.hasInstance,
    value: function value(target) {
      /**
       * This is function extending being called each time JS uses instanceOf, since babel use it each time it create new class
       * its give's opportunity to mange capabilities of instanceOf operator.
       * saving current class each time will give option later to handle instanceOf manually.
       */
      var result = _superPropGet(InstanceType, Symbol.hasInstance, this, 2)([target]);

      // Act normal when validate a class, which does not have instance type.
      if (target && !target.constructor.getInstanceType) {
        return result;
      }
      if (target) {
        if (!target.instanceTypes) {
          target.instanceTypes = [];
        }
        if (!result) {
          if (this.getInstanceType() === target.constructor.getInstanceType()) {
            result = true;
          }
        }
        if (result) {
          var name = this.getInstanceType === InstanceType.getInstanceType ? 'BaseInstanceType' : this.getInstanceType();
          if (-1 === target.instanceTypes.indexOf(name)) {
            target.instanceTypes.push(name);
          }
        }
      }
      if (!result && target) {
        // Check if the given 'target', is instance of known types.
        result = target.instanceTypes && Array.isArray(target.instanceTypes) && -1 !== target.instanceTypes.indexOf(this.getInstanceType());
      }
      return result;
    }
  }, {
    key: "getInstanceType",
    value: function getInstanceType() {
      elementorModules.ForceMethodImplementation();
    }
  }]);
}();

/***/ }),

/***/ "../assets/dev/js/modules/imports/module.js":
/*!**************************************************!*\
  !*** ../assets/dev/js/modules/imports/module.js ***!
  \**************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js"));
var Module = function Module() {
  var $ = jQuery,
    instanceParams = arguments,
    self = this,
    events = {};
  var settings;
  var ensureClosureMethods = function ensureClosureMethods() {
    $.each(self, function (methodName) {
      var oldMethod = self[methodName];
      if ('function' !== typeof oldMethod) {
        return;
      }
      self[methodName] = function () {
        return oldMethod.apply(self, arguments);
      };
    });
  };
  var initSettings = function initSettings() {
    settings = self.getDefaultSettings();
    var instanceSettings = instanceParams[0];
    if (instanceSettings) {
      $.extend(true, settings, instanceSettings);
    }
  };
  var init = function init() {
    self.__construct.apply(self, instanceParams);
    ensureClosureMethods();
    initSettings();
    self.trigger('init');
  };
  this.getItems = function (items, itemKey) {
    if (itemKey) {
      var keyStack = itemKey.split('.'),
        currentKey = keyStack.splice(0, 1);
      if (!keyStack.length) {
        return items[currentKey];
      }
      if (!items[currentKey]) {
        return;
      }
      return this.getItems(items[currentKey], keyStack.join('.'));
    }
    return items;
  };
  this.getSettings = function (setting) {
    return this.getItems(settings, setting);
  };
  this.setSettings = function (settingKey, value, settingsContainer) {
    if (!settingsContainer) {
      settingsContainer = settings;
    }
    if ('object' === (0, _typeof2.default)(settingKey)) {
      $.extend(settingsContainer, settingKey);
      return self;
    }
    var keyStack = settingKey.split('.'),
      currentKey = keyStack.splice(0, 1);
    if (!keyStack.length) {
      settingsContainer[currentKey] = value;
      return self;
    }
    if (!settingsContainer[currentKey]) {
      settingsContainer[currentKey] = {};
    }
    return self.setSettings(keyStack.join('.'), value, settingsContainer[currentKey]);
  };
  this.getErrorMessage = function (type, functionName) {
    var message;
    switch (type) {
      case 'forceMethodImplementation':
        message = "The method '".concat(functionName, "' must to be implemented in the inheritor child.");
        break;
      default:
        message = 'An error occurs';
    }
    return message;
  };

  // TODO: This function should be deleted ?.
  this.forceMethodImplementation = function (functionName) {
    throw new Error(this.getErrorMessage('forceMethodImplementation', functionName));
  };
  this.on = function (eventName, callback) {
    if ('object' === (0, _typeof2.default)(eventName)) {
      $.each(eventName, function (singleEventName) {
        self.on(singleEventName, this);
      });
      return self;
    }
    var eventNames = eventName.split(' ');
    eventNames.forEach(function (singleEventName) {
      if (!events[singleEventName]) {
        events[singleEventName] = [];
      }
      events[singleEventName].push(callback);
    });
    return self;
  };
  this.off = function (eventName, callback) {
    if (!events[eventName]) {
      return self;
    }
    if (!callback) {
      delete events[eventName];
      return self;
    }
    var callbackIndex = events[eventName].indexOf(callback);
    if (-1 !== callbackIndex) {
      delete events[eventName][callbackIndex];

      // Reset array index (for next off on same event).
      events[eventName] = events[eventName].filter(function (val) {
        return val;
      });
    }
    return self;
  };
  this.trigger = function (eventName) {
    var methodName = 'on' + eventName[0].toUpperCase() + eventName.slice(1),
      params = Array.prototype.slice.call(arguments, 1);
    if (self[methodName]) {
      self[methodName].apply(self, params);
    }
    var callbacks = events[eventName];
    if (!callbacks) {
      return self;
    }
    $.each(callbacks, function (index, callback) {
      callback.apply(self, params);
    });
    return self;
  };
  init();
};
Module.prototype.__construct = function () {};
Module.prototype.getDefaultSettings = function () {
  return {};
};
Module.prototype.getConstructorID = function () {
  return this.constructor.name;
};
Module.extend = function (properties) {
  var $ = jQuery,
    parent = this;
  var child = function child() {
    return parent.apply(this, arguments);
  };
  $.extend(child, parent);
  child.prototype = Object.create($.extend({}, parent.prototype, properties));
  child.prototype.constructor = child;
  child.__super__ = parent.prototype;
  return child;
};
module.exports = Module;

/***/ }),

/***/ "../modules/web-cli/assets/js/api.js":
/*!*******************************************!*\
  !*** ../modules/web-cli/assets/js/api.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _backwardsCompatibility = _interopRequireDefault(__webpack_require__(/*! ./core/backwards-compatibility */ "../modules/web-cli/assets/js/core/backwards-compatibility.js"));
var _commandBase = _interopRequireDefault(__webpack_require__(/*! ./modules/command-base */ "../modules/web-cli/assets/js/modules/command-base.js"));
var _commandInternalBase = _interopRequireDefault(__webpack_require__(/*! ./modules/command-internal-base */ "../modules/web-cli/assets/js/modules/command-internal-base.js"));
var _commandData = _interopRequireDefault(__webpack_require__(/*! ./modules/command-data */ "../modules/web-cli/assets/js/modules/command-data.js"));
var _commands = _interopRequireDefault(__webpack_require__(/*! ./core/commands */ "../modules/web-cli/assets/js/core/commands.js"));
var _commandsInternal = _interopRequireDefault(__webpack_require__(/*! ./core/commands-internal */ "../modules/web-cli/assets/js/core/commands-internal.js"));
var _componentBase = _interopRequireDefault(__webpack_require__(/*! ./modules/component-base */ "../modules/web-cli/assets/js/modules/component-base.js"));
var _componentModalBase = _interopRequireDefault(__webpack_require__(/*! ./modules/component-modal-base */ "../modules/web-cli/assets/js/modules/component-modal-base.js"));
var _components = _interopRequireDefault(__webpack_require__(/*! ./core/components */ "../modules/web-cli/assets/js/core/components.js"));
var _data = _interopRequireDefault(__webpack_require__(/*! ./core/data.js */ "../modules/web-cli/assets/js/core/data.js"));
var _hashCommands = _interopRequireDefault(__webpack_require__(/*! ./extras/hash-commands */ "../modules/web-cli/assets/js/extras/hash-commands.js"));
var _hookBreak = _interopRequireDefault(__webpack_require__(/*! ./modules/hook-break */ "../modules/web-cli/assets/js/modules/hook-break.js"));
var _hooks = _interopRequireDefault(__webpack_require__(/*! ./core/hooks */ "../modules/web-cli/assets/js/core/hooks.js"));
var _routes = _interopRequireDefault(__webpack_require__(/*! ./core/routes */ "../modules/web-cli/assets/js/core/routes.js"));
var _shortcuts = _interopRequireDefault(__webpack_require__(/*! ./core/shortcuts */ "../modules/web-cli/assets/js/core/shortcuts.js"));
var _store = _interopRequireDefault(__webpack_require__(/*! ./core/store */ "../modules/web-cli/assets/js/core/store.js"));
var _uiStates = _interopRequireDefault(__webpack_require__(/*! ./core/ui-states */ "../modules/web-cli/assets/js/core/ui-states.js"));
var hookData = _interopRequireWildcard(__webpack_require__(/*! ./modules/hooks/data/ */ "../modules/web-cli/assets/js/modules/hooks/data/index.js"));
var hookUI = _interopRequireWildcard(__webpack_require__(/*! ./modules/hooks/ui */ "../modules/web-cli/assets/js/modules/hooks/ui/index.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
/* Alphabetical order */
var API = exports["default"] = /*#__PURE__*/function () {
  /**
   * Function constructor().
   *
   * Create's 'elementor' api.
   */
  function API() {
    (0, _classCallCheck2.default)(this, API);
    this.components = new _components.default();
    this.commands = new _commands.default();
    this.commandsInternal = new _commandsInternal.default();
    this.hooks = new _hooks.default();
    this.routes = new _routes.default();
    this.shortcuts = new _shortcuts.default(jQuery(window));
    this.data = new _data.default();
    this.store = new _store.default();
    this.uiStates = new _uiStates.default();
    this.modules = {
      CommandBase: _commandBase.default,
      CommandInternalBase: _commandInternalBase.default,
      CommandData: _commandData.default,
      ComponentBase: _componentBase.default,
      ComponentModalBase: _componentModalBase.default,
      HookBreak: _hookBreak.default,
      hookData: hookData,
      hookUI: hookUI
    };
    this.extras = {
      hashCommands: new _hashCommands.default()
    };

    // Backwards compatibility should be last, in order to handle others.
    this.bc = new _backwardsCompatibility.default();
  }

  /**
   * Function run().
   *
   * Alias of `$e.commands.run()`.
   *
   * @param {string} command
   * @param {*}      [args={}]
   *
   * @return {*} result
   */
  return (0, _createClass2.default)(API, [{
    key: "run",
    value: function run(command) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      return $e.commands.run(command, args);
    }

    /**
     * Function internal().
     *
     * Alias of `$e.commandsInternal.run()`.
     *
     * @param {string} command
     * @param {*}      [args={}]
     *
     * @return {boolean} result
     */
  }, {
    key: "internal",
    value: function internal(command) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      return $e.commandsInternal.run(command, args);
    }

    /**
     * Function route().
     *
     * Alias of `$e.routes.to()`.
     *
     * @param {string} route
     * @param {*}      [args={}]
     * @param {Object} [options]
     */
  }, {
    key: "route",
    value: function route(_route) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {
        history: true
      };
      return $e.routes.to(_route, args, options);
    }

    // TODO: shortcut();
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/core/backwards-compatibility.js":
/*!********************************************************************!*\
  !*** ../modules/web-cli/assets/js/core/backwards-compatibility.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _componentBase = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/component-base */ "../modules/web-cli/assets/js/modules/component-base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var BackwardsCompatibility = exports["default"] = /*#__PURE__*/function () {
  function BackwardsCompatibility() {
    (0, _classCallCheck2.default)(this, BackwardsCompatibility);
  }
  return (0, _createClass2.default)(BackwardsCompatibility, [{
    key: "ensureTab",
    value: function ensureTab(namespace, tabSlug) {
      var page = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
      var component = $e.components.get(namespace);
      if (!component) {
        var Component = /*#__PURE__*/function (_ComponentBase) {
          function Component() {
            (0, _classCallCheck2.default)(this, Component);
            return _callSuper(this, Component, arguments);
          }
          (0, _inherits2.default)(Component, _ComponentBase);
          return (0, _createClass2.default)(Component, [{
            key: "getNamespace",
            value: function getNamespace() {
              return namespace;
            }
          }, {
            key: "renderTab",
            value: function renderTab(tab) {
              elementor.getPanelView().setPage(page).activateTab(tab);
            }
          }]);
        }(_componentBase.default);
        component = $e.components.register(new Component());
      }
      if (!component.hasTab(tabSlug) && elementor.config.tabs[tabSlug]) {
        component.addTab(tabSlug, {
          title: elementor.config.tabs[tabSlug]
        });
      }
    }
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/core/backwards-compatibility/commands.js":
/*!*****************************************************************************!*\
  !*** ../modules/web-cli/assets/js/core/backwards-compatibility/commands.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _module = _interopRequireDefault(__webpack_require__(/*! elementor-assets-js/modules/imports/module.js */ "../assets/dev/js/modules/imports/module.js"));
var _deprecation = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/deprecation */ "../modules/web-cli/assets/js/utils/deprecation.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var CommandsBackwardsCompatibility = exports["default"] = /*#__PURE__*/function (_Module) {
  function CommandsBackwardsCompatibility() {
    var _this;
    (0, _classCallCheck2.default)(this, CommandsBackwardsCompatibility);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, CommandsBackwardsCompatibility, [].concat(args));
    (0, _defineProperty2.default)(_this, "on", function (eventName, callback) {
      if ('run' === eventName) {
        var componentName = _this.getConstructorID();

        // Regex takes the first letter and convert it to lower case.
        componentName = componentName.replace(/^./, function (val) {
          return val.toLowerCase();
        });
        _deprecation.default.deprecated("$e.".concat(componentName, ".on( 'run', ... )"), '3.0.0', "$e.".concat(componentName, ".on( 'run:before', ... )"));
        _this.onOrig('run:before', callback);
        return;
      }
      _this.onOrig(eventName, callback);
    });
    return _this;
  }
  (0, _inherits2.default)(CommandsBackwardsCompatibility, _Module);
  return (0, _createClass2.default)(CommandsBackwardsCompatibility, [{
    key: "__construct",
    value: function __construct() {
      this.onOrig = this.on;
    }
  }]);
}(_module.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/core/commands-internal.js":
/*!**************************************************************!*\
  !*** ../modules/web-cli/assets/js/core/commands-internal.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commands = _interopRequireDefault(__webpack_require__(/*! ./commands.js */ "../modules/web-cli/assets/js/core/commands.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var CommandsInternal = exports["default"] = /*#__PURE__*/function (_Commands) {
  function CommandsInternal() {
    (0, _classCallCheck2.default)(this, CommandsInternal);
    return _callSuper(this, CommandsInternal, arguments);
  }
  (0, _inherits2.default)(CommandsInternal, _Commands);
  return (0, _createClass2.default)(CommandsInternal, [{
    key: "error",
    value: function error(message) {
      throw Error('Commands internal: ' + message);
    }
  }]);
}(_commands.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/core/commands.js":
/*!*****************************************************!*\
  !*** ../modules/web-cli/assets/js/core/commands.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _commands = _interopRequireDefault(__webpack_require__(/*! ./backwards-compatibility/commands */ "../modules/web-cli/assets/js/core/backwards-compatibility/commands.js"));
var _commandBase = _interopRequireDefault(__webpack_require__(/*! ../modules/command-base */ "../modules/web-cli/assets/js/modules/command-base.js"));
var _console = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/console */ "../modules/web-cli/assets/js/utils/console.js"));
var _deprecation = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/deprecation */ "../modules/web-cli/assets/js/utils/deprecation.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @typedef {import('../modules/component-base')} ComponentBase
 */
/**
 * @typedef {import('../modules/command-base')} CommandBase
 */
/**
 * @typedef {{}} Component
 */
var Commands = exports["default"] = /*#__PURE__*/function (_CommandsBackwardsCom) {
  /**
   * Function constructor().
   *
   * Create `$e.commands` API.
   *
   * @param {{}} args
   */
  function Commands() {
    var _this;
    (0, _classCallCheck2.default)(this, Commands);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, Commands, [].concat(args));
    _this.current = {};
    _this.currentArgs = {};
    _this.currentTrace = [];
    _this.commands = {};
    _this.components = {};
    Object.defineProperty(_this, 'classes', {
      get: function get() {
        _deprecation.default.deprecated('$e.commands.classes', '3.7.0', '$e.commands.getCommandClass(), $e.commandsInternal.getCommandClass(), $e.data.getCommandClass(), $e.routes.getCommandClass() according to the requested command infra-structure,');
        return _objectSpread(_objectSpread(_objectSpread(_objectSpread({}, $e.commands.commands), $e.commandsInternal.commands), $e.data.commands), $e.routes.commands);
      }
    });
    return _this;
  }

  /**
   * @param {string} id
   * @return {CommandBase} command class
   */
  (0, _inherits2.default)(Commands, _CommandsBackwardsCom);
  return (0, _createClass2.default)(Commands, [{
    key: "getCommandClass",
    value: function getCommandClass(id) {
      return this.commands[id];
    }

    /**
     * Function getAll().
     *
     * Receive all loaded commands.
     *
     * @return {string[]} commands
     */
  }, {
    key: "getAll",
    value: function getAll() {
      return Object.keys(this.commands).sort();
    }

    /**
     * Function register().
     *
     * Register new command.
     *
     * @param {ComponentBase|string} component
     * @param {string}               command
     * @param {Function}             callback
     *
     * @return {Commands} commands
     */
  }, {
    key: "register",
    value: function register(component, command, callback) {
      var _this2 = this;
      var namespace;
      if ('string' === typeof component) {
        namespace = component;
        component = $e.components.get(namespace);
        if (!component) {
          this.error("'".concat(namespace, "' component is not exist."));
        }
      } else {
        namespace = component.getNamespace();
      }
      var fullCommand = namespace + (command ? '/' + command : '');
      if (this.commands[fullCommand]) {
        this.error("`".concat(fullCommand, "` is already registered."));
      }
      this.commands[fullCommand] = callback;
      this.components[fullCommand] = namespace;
      var shortcuts = component.getShortcuts(),
        shortcut = shortcuts[command];
      if (shortcut) {
        shortcut.command = fullCommand;
        shortcut.callback = function (event) {
          return _this2.runShortcut(fullCommand, event);
        };
        $e.shortcuts.register(shortcut.keys, shortcut);
      }
      return this;
    }
  }, {
    key: "unregister",
    value: function unregister(component, command) {
      var namespace;
      if ('string' === typeof component) {
        namespace = component;
        component = $e.components.get(namespace);
        if (!component) {
          this.error("'".concat(namespace, "' component is not exist."));
        }
      } else {
        namespace = component.getNamespace();
      }
      var fullCommand = namespace + (command ? '/' + command : '');
      if (!this.commands[fullCommand]) {
        this.error("`".concat(fullCommand, "` not exist."));
      }
      delete this.commands[fullCommand];
      delete this.components[fullCommand];
      var shortcuts = component.getShortcuts(),
        shortcut = shortcuts[command];
      if (shortcut) {
        $e.shortcuts.unregister(shortcut.keys, shortcut);
      }
      return this;
    }

    /**
     * Function getComponent().
     *
     * Receive Component of the command.
     *
     * @param {string} command
     *
     * @return {Component} component
     */
  }, {
    key: "getComponent",
    value: function getComponent(command) {
      var namespace = this.components[command];
      return $e.components.get(namespace);
    }

    /**
     * Function is().
     *
     * Checks if current running command is the same parameter command.
     *
     * @param {string} command
     *
     * @return {boolean} is this command the same as the one passed in the arguments
     */
  }, {
    key: "is",
    value: function is(command) {
      var component = this.getComponent(command);
      if (!component) {
        return false;
      }
      return command === this.current[component.getServiceName()];
    }

    /**
     * Function isCurrentFirstTrace().
     *
     * Checks if parameter command is the first command in trace that currently running.
     *
     * @param {string} command
     *
     * @return {boolean} is parameter command the first command in trace that currently running
     */
  }, {
    key: "isCurrentFirstTrace",
    value: function isCurrentFirstTrace(command) {
      return command === this.getCurrentFirstTrace();
    }

    /**
     * Function getCurrent().
     *
     * Receive currently running components and its commands.
     *
     * @param {string} container
     *
     * @return {{}|boolean|*} currently running components
     */
  }, {
    key: "getCurrent",
    value: function getCurrent() {
      var container = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      if (container) {
        if (!this.current[container]) {
          return false;
        }
        return this.current[container];
      }
      return this.current;
    }

    /**
     * Function getCurrentArgs().
     *
     * Receive currently running command args.
     *
     * @param {string} container
     *
     * @return {{}|boolean|*} current arguments
     */
  }, {
    key: "getCurrentArgs",
    value: function getCurrentArgs() {
      var container = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      if (container) {
        if (!this.currentArgs[container]) {
          return false;
        }
        return this.currentArgs[container];
      }
      return this.currentArgs;
    }

    /**
     * Function getCurrentFirst().
     *
     * Receive first command that currently running.
     *
     * @return {string} first running command
     */
  }, {
    key: "getCurrentFirst",
    value: function getCurrentFirst() {
      return Object.values(this.current)[0];
    }

    /**
     * Function getCurrentLast().
     *
     * Receive last command that currently running.
     *
     * @return {string} last running command
     */
  }, {
    key: "getCurrentLast",
    value: function getCurrentLast() {
      var current = Object.values(this.current);
      return current[current.length - 1];
    }

    /**
     * Function getCurrentFirstTrace().
     *
     * Receive first command in trace that currently running
     *
     * @return {string} first command in trace
     */
  }, {
    key: "getCurrentFirstTrace",
    value: function getCurrentFirstTrace() {
      return this.currentTrace[0];
    }

    /**
     * Function beforeRun().
     *
     * Responsible to add current command to trace and trigger 'run:before' event.
     * Run before command.
     *
     * @param {string}  command
     * @param {{}}      args
     * @param {boolean} [addTrace=true]
     */
  }, {
    key: "beforeRun",
    value: function beforeRun(command) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var addTrace = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
      var component = this.getComponent(command),
        container = component.getServiceName();
      if (addTrace) {
        this.addCurrentTrace(container, command, args);
      }
      if (args.onBefore) {
        args.onBefore.apply(component, [args]);
      }
      this.trigger('run:before', component, command, args);
      window.dispatchEvent(new CustomEvent('elementor/commands/run/before', {
        detail: {
          command: command,
          args: args
        }
      }));
    }

    /**
     * Function validateRun().
     *
     * Responsible to validate if the run is even possible.
     * Runs immediately after entering `run()`.
     *
     * @param {string} command
     * @param {*}      args
     *
     * @return {boolean} dependency result
     */
  }, {
    key: "validateRun",
    value: function validateRun(command) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      if (!this.commands[command]) {
        this.error("`".concat(command, "` not found."));
      }
      return this.getComponent(command).dependency(command, args);
    }

    /**
     * Function run().
     *
     * Runs a command.
     *
     * @param {string} command
     * @param {{}}     args
     *
     * @return {boolean|*} results
     */
  }, {
    key: "run",
    value: function run(command) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      if (!this.validateRun(command, args)) {
        return false;
      }
      this.beforeRun(command, args);

      // Get command class or callback.
      var context = this.commands[command];

      // Is it command-base based class?
      if (context.getInstanceType) {
        context = new context(args);
      }
      var currentComponent = this.getComponent(command);

      // Is simple callback? (e.g.  a route)
      if (!(context instanceof _commandBase.default)) {
        var results = context.apply(currentComponent, [args]);
        this.afterRun(command, args, results);
        return results;
      }
      if (!this.validateInstanceScope(context, currentComponent, command)) {
        return this.removeCurrentTrace(currentComponent);
      }
      return this.runInstance(context);
    }

    /**
     * Function runInstance().
     *
     * @param {CommandBase} instance
     *
     * @return {boolean|Promise<*>}
     */
  }, {
    key: "runInstance",
    value: function runInstance(instance) {
      var results = null;

      // For UI Hooks.
      instance.onBeforeRun(instance.args);
      try {
        // For data hooks.
        instance.onBeforeApply(instance.args);
        results = instance.run();
      } catch (e) {
        this.catchApply(e, instance);
        if (e instanceof $e.modules.HookBreak) {
          this.removeCurrentTrace(instance.component);
          return false;
        }
      }
      return this.applyRunAfter(instance, results);
    }

    /**
     * Function applyRunAfter().
     *
     * Responsible for applying everything that need to be run after each command runs.
     * Called on run() after runInstance(), to manipulate results & apply 'after' hooks.
     *
     * @param {CommandBase} instance
     * @param {*}           result
     *
     * @return {Promise<*>|*}
     */
  }, {
    key: "applyRunAfter",
    value: function applyRunAfter(instance, result) {
      var _this3 = this;
      // TODO: Temp code determine if it's a jQuery deferred object.
      if (result && 'object' === (0, _typeof2.default)(result) && result.promise && result.then && result.fail) {
        var handleJQueryDeferred = function handleJQueryDeferred(_result) {
          _result.fail(function (e) {
            _this3.catchApply(e, instance);
            _this3.afterRun(instance.command, instance.args, e);
          });
          return _result.done(function (__result) {
            return _this3.applyRunAfterAsyncResult(instance, __result);
          });
        };
        return handleJQueryDeferred(result);
      } else if (result instanceof Promise) {
        return this.applyRunAfterAsync(instance, result);
      }
      this.applyRunAfterSync(instance, result);
      return result;
    }

    /**
     * Function applyRunAfterSync().
     *
     * Responsible to handle simple(synchronous) 'run after' behavior.
     * Called on applyRunAfterSync() after runInstance(), to handle results.
     *
     * @param {CommandBase} instance
     * @param {*}           result
     */
  }, {
    key: "applyRunAfterSync",
    value: function applyRunAfterSync(instance, result) {
      // Run Data hooks.
      instance.onAfterApply(instance.args, result);

      // For UI hooks.
      instance.onAfterRun(instance.args, result);
      this.afterRun(instance.command, instance.args, result);
    }

    /**
     * Function applyRunAfterAsync().
     *
     * Await for promise result.
     * Called on applyRunAfter() after runInstance().
     *
     * @param {CommandBase} instance
     * @param {*}           result
     */
  }, {
    key: "applyRunAfterAsync",
    value: function applyRunAfterAsync(instance, result) {
      var _this4 = this;
      // Override initial result ( promise ) to await onAfter promises, first!.
      return (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
        return _regenerator.default.wrap(function (_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              _context.next = 1;
              return result.catch(function (e) {
                _this4.catchApply(e, instance);
                _this4.afterRun(instance.command, instance.args, e);
              });
            case 1:
              _context.next = 2;
              return result.then(function (_result) {
                return _this4.applyRunAfterAsyncResult(instance, _result);
              });
            case 2:
              return _context.abrupt("return", result);
            case 3:
            case "end":
              return _context.stop();
          }
        }, _callee);
      }))();
    }

    /**
     * Function applyRunAfterAsyncResult().
     *
     * Responsible to await all promises results.
     * Called on applyRunAfterAsync() after runInstance(), to handle async results.
     * Awaits all the promises, before releasing the command.
     *
     * @param {CommandBase} instance
     * @param {*}           result
     */
  }, {
    key: "applyRunAfterAsyncResult",
    value: (function () {
      var _applyRunAfterAsyncResult = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee2(instance, result) {
        var results, promises;
        return _regenerator.default.wrap(function (_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              // Run Data hooks.
              results = instance.onAfterApply(instance.args, result), promises = Array.isArray(results) ? results.flat().filter(function (filtered) {
                return filtered instanceof Promise;
              }) : [];
              if (!promises.length) {
                _context2.next = 1;
                break;
              }
              _context2.next = 1;
              return Promise.all(promises);
            case 1:
              // For UI hooks.
              instance.onAfterRun(instance.args, result);
              this.afterRun(instance.command, instance.args, result);
            case 2:
            case "end":
              return _context2.stop();
          }
        }, _callee2, this);
      }));
      function applyRunAfterAsyncResult(_x, _x2) {
        return _applyRunAfterAsyncResult.apply(this, arguments);
      }
      return applyRunAfterAsyncResult;
    }()
    /**
     * Function afterRun().
     *
     * Responsible to to clear command from trace, and run 'run:after' event.
     * Method fired after the command runs.
     *
     * @param {string}  command
     * @param {{}}      args
     * @param {*}       results
     * @param {boolean} [removeTrace=true]
     */
    )
  }, {
    key: "afterRun",
    value: function afterRun(command, args) {
      var results = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : undefined;
      var removeTrace = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : true;
      var component = this.getComponent(command);
      if (args.onAfter) {
        args.onAfter.apply(component, [args, results]);
      }
      this.trigger('run:after', component, command, args, results);
      window.dispatchEvent(new CustomEvent('elementor/commands/run/after', {
        detail: {
          command: command,
          args: args
        }
      }));
      if (removeTrace) {
        this.removeCurrentTrace(component);
      }
    }

    /**
     * @param {Error}       e
     * @param {CommandBase} instance
     */
  }, {
    key: "catchApply",
    value: function catchApply(e, instance) {
      instance.onCatchApply(e);
      _console.default.error(e);
    }

    /**
     * Function runShortcut().
     *
     * Run shortcut.
     *
     * It's separated in order to allow override.
     *
     * @param {string} command
     * @param {*}      event
     *
     * @return {boolean|*} result
     */
  }, {
    key: "runShortcut",
    value: function runShortcut(command, event) {
      return this.run(command, event);
    }
  }, {
    key: "validateInstanceScope",
    value: function validateInstanceScope(instance, currentComponent, command) {
      if (!(instance instanceof _commandBase.default)) {
        this.error("invalid instance, command: '".concat(command, "' "));
      }

      // In case of different scope.
      if (currentComponent !== instance.component) {
        if ($e.devTools) {
          $e.devTools.log.warn("Command: '".concat(command, "' registerArgs.component: '").concat(instance.component.getNamespace(), "' while current component is: '").concat(currentComponent.getNamespace(), "'"));
        }
        return false;
      }
      return true;
    }
  }, {
    key: "addCurrentTrace",
    value: function addCurrentTrace(container, command, args) {
      this.currentTrace.push(command);
      Commands.trace.push(command);
      this.attachCurrent(container, command, args);
    }
  }, {
    key: "removeCurrentTrace",
    value: function removeCurrentTrace(currentComponent) {
      var container = currentComponent.getServiceName();
      this.currentTrace.pop();
      Commands.trace.pop();
      this.detachCurrent(container);
    }
  }, {
    key: "attachCurrent",
    value: function attachCurrent(container, command, args) {
      this.current[container] = command;
      this.currentArgs[container] = args;
    }
  }, {
    key: "detachCurrent",
    value: function detachCurrent(container) {
      delete this.current[container];
      delete this.currentArgs[container];
    }

    /**
     * Function error().
     *
     * Throws error.
     *
     * @throws {Error}
     *
     * @param {string} message
     */
  }, {
    key: "error",
    value: function error(message) {
      throw Error("Commands: ".concat(message));
    }
  }]);
}(_commands.default);
(0, _defineProperty2.default)(Commands, "trace", []);

/***/ }),

/***/ "../modules/web-cli/assets/js/core/components.js":
/*!*******************************************************!*\
  !*** ../modules/web-cli/assets/js/core/components.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _module = _interopRequireDefault(__webpack_require__(/*! elementor-assets-js/modules/imports/module.js */ "../assets/dev/js/modules/imports/module.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @typedef {{}} Component
 */
var _default = exports["default"] = /*#__PURE__*/function (_Module) {
  function _default() {
    var _this;
    (0, _classCallCheck2.default)(this, _default);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, _default, [].concat(args));
    _this.components = {};
    _this.activeComponents = {};
    return _this;
  }
  (0, _inherits2.default)(_default, _Module);
  return (0, _createClass2.default)(_default, [{
    key: "getAll",
    value: function getAll() {
      return Object.keys(this.components).sort();
    }
  }, {
    key: "register",
    value: function register(component) {
      if (this.components[component.getNamespace()]) {
        return;
      }
      component.registerAPI();
      this.components[component.getNamespace()] = component;
      return component;
    }

    /**
     * @param {string} id
     * @return {Component} component
     */
  }, {
    key: "get",
    value: function get(id) {
      return this.components[id];
    }
  }, {
    key: "getActive",
    value: function getActive() {
      return this.activeComponents;
    }
  }, {
    key: "activate",
    value: function activate(namespace) {
      // Add as last.
      this.inactivate(namespace);
      this.activeComponents[namespace] = true;
    }
  }, {
    key: "inactivate",
    value: function inactivate(namespace) {
      delete this.activeComponents[namespace];
    }
  }, {
    key: "isActive",
    value: function isActive(namespace) {
      return !!this.activeComponents[namespace];
    }
  }]);
}(_module.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/core/data.js":
/*!*************************************************!*\
  !*** ../modules/web-cli/assets/js/core/data.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.READABLE = exports.EDITABLE = exports.DELETABLE = exports.CREATABLE = exports.ALLMETHODS = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _argsObject = _interopRequireDefault(__webpack_require__(/*! elementor-assets-js/modules/imports/args-object */ "../assets/dev/js/modules/imports/args-object.js"));
var _commands = _interopRequireDefault(__webpack_require__(/*! ./commands.js */ "../modules/web-cli/assets/js/core/commands.js"));
var _cache = _interopRequireDefault(__webpack_require__(/*! ./data/cache */ "../modules/web-cli/assets/js/core/data/cache.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
/**
 * @typedef {('create'|'delete'|'get'|'update'|'options')} DataTypes
 */
/**
 * @typedef {import('../modules/component-base')} ComponentBase
 */
/**
 * @typedef {{}} RequestInfo
 */

/**
 * @typedef {{}} RequestData
 * @property {ComponentBase}  component         component
 * @property {string}         command           command
 * @property {{}}             args              arguments
 * @property {DataTypes}      type              type
 * @property {number}         timestamp         timestamp
 * @property {string}         endpoint          endpoint
 *
 * @property {string}         [baseEndpointURL] baseEndpointURL
 * @property {string}         [namespace]       namespace
 * @property {string}         [version]         version
 * @property {('hit'|'miss')} [cache]           cache
 */

/**
 * @typedef {Object} ExtractedCommand
 * @property {string} command command
 * @property {Object} args    arguments
 */

// TODO: Return it from the server. Original at WP_REST_Server.
var READABLE = exports.READABLE = ['GET'],
  CREATABLE = exports.CREATABLE = ['POST'],
  EDITABLE = exports.EDITABLE = ['POST', 'PUT', 'PATCH'],
  DELETABLE = exports.DELETABLE = ['DELETE'],
  ALLMETHODS = exports.ALLMETHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
var Data = exports["default"] = /*#__PURE__*/function (_Commands) {
  function Data() {
    var _this;
    var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    (0, _classCallCheck2.default)(this, Data);
    _this = _callSuper(this, Data, [args]);
    _this.args = Object.assign(args, {
      namespace: 'elementor',
      version: '1',
      baseEndpointURL: elementorWebCliConfig.urls.rest
    });
    _this.cache = new _cache.default(_this);
    _this.validatedRequests = {};
    _this.commandFormats = {};
    return _this;
  }

  /**
   * Function getHTTPMethod().
   *
   * Returns HTTP Method by type.
   *
   * @param {DataTypes} type
   *
   * @return {string|boolean} HTTP Method
   */
  (0, _inherits2.default)(Data, _Commands);
  return (0, _createClass2.default)(Data, [{
    key: "getHTTPMethod",
    value: function getHTTPMethod(type) {
      switch (type) {
        case 'create':
          return 'POST';
        case 'delete':
          return 'DELETE';
        case 'get':
          return 'GET';
        case 'update':
          return 'PUT';
        case 'options':
          return 'OPTIONS';
      }
      return false;
    }

    /**
     * Function getAllowedMethods().
     *
     * Returns allowed HTTP methods by type.
     *
     * @param {DataTypes} type
     *
     * @return {[string]|boolean} allowed HTTP methods
     */
  }, {
    key: "getAllowedMethods",
    value: function getAllowedMethods(type) {
      switch (type) {
        case 'create':
          return CREATABLE;
        case 'delete':
          return DELETABLE;
        case 'get':
          return READABLE;
        case 'update':
          return EDITABLE;
        case 'options':
          return ['OPTIONS'];
      }
      return false;
    }

    /**
     * Function getEndpointURL().
     *
     * Get remote endpoint address.
     *
     * @param {RequestData} requestData
     * @param {string}      [endpoint=requestData.endpoint]
     *
     * @return {string} endpoint address
     */
  }, {
    key: "getEndpointURL",
    value: function getEndpointURL(requestData) {
      var endpoint = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : requestData.endpoint;
      // Allow to request data override default namespace and args.
      var _requestData$baseEndp = requestData.baseEndpointURL,
        baseEndpointURL = _requestData$baseEndp === void 0 ? this.args.baseEndpointURL : _requestData$baseEndp,
        _requestData$namespac = requestData.namespace,
        namespace = _requestData$namespac === void 0 ? this.args.namespace : _requestData$namespac,
        _requestData$version = requestData.version,
        version = _requestData$version === void 0 ? this.args.version : _requestData$version;
      return "".concat(baseEndpointURL).concat(namespace, "/v").concat(version, "/") + endpoint;
    }

    /**
     * Function commandToEndpoint().
     *
     * Convert command to endpoint.
     *
     * For example `component/command/{arg}` => `controller/endpoint/8`.
     *
     * TODO: Find a better solution.
     *
     * @param {string}      command
     * @param {{}}          args
     * @param {string|null} [format]
     *
     * @return {string} endpoint
     */
  }, {
    key: "commandToEndpoint",
    value: function commandToEndpoint(command, args) {
      var format = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      var endpoint = command;
      var argsQueryLength = args !== null && args !== void 0 && args.query ? Object.values(args.query).length : 0;
      if (argsQueryLength && format && format.includes('/{')) {
        // Means command includes magic query arguments ( controller/endpoint/{whatever} ).
        var magicParams = format.split('/').filter(function (str) {
          return '{' === str.charAt(0);
        });
        magicParams.forEach(function (param) {
          // Remove the '{', '}'.
          param = param.replace('{', '');
          param = param.replace('}', '');
          var formatted = Object.entries(args.query).find(function (_ref) {
            var _ref2 = (0, _slicedToArray2.default)(_ref, 1),
              key = _ref2[0];
            return key === param;
          });
          if (!formatted) {
            return;
          }
          var key = formatted[0],
            value = formatted[1].toString();

          // Replace magic params with values.
          format = format.replace(new RegExp('{' + param + '}', 'g'), value);
          delete args.query[key];
        });
      }
      if (format) {
        endpoint = format;
      }

      // If requested magic param does not exist in args, need to remove it to have fixed endpoint.
      // eg: 'documents/{documentId}/elements/{elementId}' and args { documentId: 4123 }.
      // result: 'documents/4123/elements'
      if (format && endpoint.includes('/{')) {
        endpoint = endpoint.substring(0, endpoint.indexOf('/{'));
      }
      if (args.query && Object.values(args.query).length) {
        // Sorting since the endpoint later will be used as key to store the cache.
        var queryEntries = Object.entries(args.query).sort(function (_ref3, _ref4) {
          var _ref5 = (0, _slicedToArray2.default)(_ref3, 1),
            aKey = _ref5[0];
          var _ref6 = (0, _slicedToArray2.default)(_ref4, 1),
            bKey = _ref6[0];
          return aKey - bKey;
        } // Sort by param name.
        );

        // `args.query` will become a part of GET params.
        if (queryEntries.length) {
          endpoint += '?';
          queryEntries.forEach(function (_ref7) {
            var _ref8 = (0, _slicedToArray2.default)(_ref7, 2),
              name = _ref8[0],
              value = _ref8[1];
            // Replace the character '/' with the encoded version,
            // mostly because when saving this endpoint value to the cache it splits the url base on the '/' character.
            value = "".concat(value).replace(/\//g, '%2F');
            endpoint += name + '=' + value + '&';
          });
        }

        // If last character is '&' remove it.
        endpoint = endpoint.replace(/&$/, '');
      }
      return endpoint;
    }

    /**
     * Function commandExtractArgs().
     *
     * If the command have query convert it to args.
     *
     * @param {string} command
     * @param {Object} args
     *
     * @return {ExtractedCommand} command
     */
  }, {
    key: "commandExtractArgs",
    value: function commandExtractArgs(command) {
      var _command;
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      if ((_command = command) !== null && _command !== void 0 && _command.includes('?')) {
        if (!args.query) {
          args.query = {};
        }
        var commandParts = command.split('?'),
          pureCommand = commandParts[0],
          queryString = commandParts[1],
          query = new URLSearchParams(queryString);
        Object.assign(args.query, Object.fromEntries(query));
        command = pureCommand;
      }
      return {
        command: command,
        args: args
      };
    }

    /**
     * Function validateRequestData().
     *
     * Validate request data requirements.
     *
     * @param {RequestData} requestData
     * @param {boolean}     [requireArgsData]
     */
  }, {
    key: "validateRequestData",
    value: function validateRequestData(requestData) {
      var requireArgsData = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      // Do not validate if its already valid.
      if (requestData.timestamp && this.validatedRequests[requestData.timestamp]) {
        return;
      }
      var argsObject = new _argsObject.default(requestData);
      argsObject.requireArgument('component');
      argsObject.requireArgumentType('command', 'string');
      argsObject.requireArgumentType('endpoint', 'string');
      if (requireArgsData) {
        argsObject.requireArgumentType('data', 'object', requestData.args);
      }

      // Ensure timestamp.
      if (!requestData.timestamp) {
        requestData.timestamp = new Date().getTime();
      }
      this.validatedRequests[requestData.timestamp] = true;
    }

    /**
     * Function prepareHeaders().
     *
     * Prepare the headers for each request.
     *
     * @param {RequestData} requestData
     *
     * @return {{}} params
     */
  }, {
    key: "prepareHeaders",
    value: function prepareHeaders(requestData) {
      var _requestData$args;
      var type = requestData.type,
        nonce = elementorWebCliConfig.nonce,
        params = {
          signal: (_requestData$args = requestData.args) === null || _requestData$args === void 0 || (_requestData$args = _requestData$args.options) === null || _requestData$args === void 0 ? void 0 : _requestData$args.signal,
          credentials: 'include' // Cookies is required for wp reset.
        },
        headers = {
          'X-WP-Nonce': nonce
        };

      /**
       * Translate:
       * 'create, delete, get, update' to HTTP Methods:
       * 'GET, POST, PUT, PATCH, DELETE'
       */
      var allowedMethods = this.getAllowedMethods(type),
        method = this.getHTTPMethod(type);
      if ('GET' === method) {
        Object.assign(params, {
          headers: headers
        });
      } else if (allowedMethods) {
        var _requestData$args2, _requestData$args3;
        if (['POST', 'PUT'].includes(method) && !((_requestData$args2 = requestData.args) !== null && _requestData$args2 !== void 0 && _requestData$args2.data)) {
          throw Error('Invalid requestData.args.data');
        }
        Object.assign(headers, {
          'Content-Type': 'application/json'
        });
        if ((_requestData$args3 = requestData.args) !== null && _requestData$args3 !== void 0 && _requestData$args3.headers) {
          Object.assign(headers, requestData.args.headers);
        }
        Object.assign(params, {
          method: method,
          headers: headers,
          body: 'application/json' === headers['Content-Type'] ? JSON.stringify(requestData.args.data) : requestData.args.data
        });
      } else {
        throw Error("Invalid type: '".concat(type, "'"));
      }
      return params;
    }

    /**
     * Function prepareEndpoint().
     *
     * This method response for building a final endpoint,
     * the main problem is with plain permalink mode + command with query params that creates a weird url,
     * the current method should fix it.
     *
     * @param {RequestData} requestData
     *
     * @return {string} Endpoint URL
     */
  }, {
    key: "prepareEndpoint",
    value: function prepareEndpoint(requestData) {
      var splitEndpoint = requestData.endpoint.split('?'),
        endpoint = splitEndpoint.shift();
      var endpointAddress = this.getEndpointURL(requestData, endpoint);
      if (splitEndpoint.length) {
        var separator = endpointAddress.includes('?') ? '&' : '?';
        endpointAddress += separator + splitEndpoint.pop();
      }
      return endpointAddress;
    }

    /**
     * Function fetch().
     *
     * @param {RequestData}                                  requestData
     * @param {function(RequestInfo,*) : Promise<Response> } [fetchAPI]
     *
     * @return {Promise<Response>} response
     */
  }, {
    key: "fetch",
    value: function fetch(requestData) {
      var _requestData$args$opt,
        _this2 = this;
      var fetchAPI = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : window.fetch;
      requestData.cache = 'miss';
      var refresh = (_requestData$args$opt = requestData.args.options) === null || _requestData$args$opt === void 0 ? void 0 : _requestData$args$opt.refresh,
        getCache = 'get' === requestData.type && !refresh,
        saveCache = ['create', 'get'].includes(requestData.type) && !refresh;
      if (getCache) {
        var cachePromise = this.cache.getAsync(requestData);
        if (cachePromise) {
          return cachePromise;
        }
      }
      var params = this.prepareHeaders(requestData);
      // eslint-disable-next-line no-async-promise-executor
      return new Promise(/*#__PURE__*/function () {
        var _ref9 = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee2(resolve, reject) {
          var endpoint, request, response, _t;
          return _regenerator.default.wrap(function (_context2) {
            while (1) switch (_context2.prev = _context2.next) {
              case 0:
                _context2.prev = 0;
                endpoint = _this2.prepareEndpoint(requestData);
                request = fetchAPI(endpoint, params);
                _context2.next = 1;
                return request.then(/*#__PURE__*/function () {
                  var _ref0 = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee(_response) {
                    return _regenerator.default.wrap(function (_context) {
                      while (1) switch (_context.prev = _context.next) {
                        case 0:
                          if (_response.ok) {
                            _context.next = 3;
                            break;
                          }
                          if (!_response.headers.get('content-type').includes('application/json')) {
                            _context.next = 2;
                            break;
                          }
                          _context.next = 1;
                          return _response.json();
                        case 1:
                          _response = _context.sent;
                        case 2:
                          throw _response;
                        case 3:
                          return _context.abrupt("return", _response.json());
                        case 4:
                        case "end":
                          return _context.stop();
                      }
                    }, _callee);
                  }));
                  return function (_x3) {
                    return _ref0.apply(this, arguments);
                  };
                }());
              case 1:
                response = _context2.sent;
                // At this point, it got the resolved response from remote.
                // So load cache, and resolve it.
                if (saveCache) {
                  _this2.cache.set(requestData, response);
                }
                resolve(response);
                _context2.next = 3;
                break;
              case 2:
                _context2.prev = 2;
                _t = _context2["catch"](0);
                reject(_t);
              case 3:
              case "end":
                return _context2.stop();
            }
          }, _callee2, null, [[0, 2]]);
        }));
        return function (_x, _x2) {
          return _ref9.apply(this, arguments);
        };
      }());
    }

    /**
     * Function getCache().
     *
     * @param {ComponentBase} component
     * @param {string}        command
     * @param {{}}            query
     *
     * @return {{}} cache object
     */
  }, {
    key: "getCache",
    value: function getCache(component, command) {
      var query = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      var args = {
        query: query
      };
      return this.cache.get({
        endpoint: this.commandToEndpoint(command, args, this.commandFormats[command]),
        component: component,
        command: command,
        args: args
      });
    }

    /**
     * Function setCache().
     *
     * @param {ComponentBase} component
     * @param {string}        command
     * @param {{}}            query
     * @param {*}             data
     */
  }, {
    key: "setCache",
    value: function setCache(component, command, query, data) {
      var args = {
        query: query
      };
      this.cache.set({
        endpoint: this.commandToEndpoint(command, args, this.commandFormats[command]),
        component: component,
        command: command,
        args: args
      }, data);
    }

    /**
     * Function updateCache().
     *
     * The difference between 'setCache' and 'updateCache' is update will only modify exist values.
     * and 'setCache' will create or update.
     *
     * @param {ComponentBase} component
     * @param {string}        command
     * @param {{}}            query
     * @param {*}             data
     */
  }, {
    key: "updateCache",
    value: function updateCache(component, command, query, data) {
      var args = {
        query: query,
        data: data
      };
      this.cache.update({
        endpoint: this.commandToEndpoint(command, args, this.commandFormats[command]),
        component: component,
        command: command,
        args: args
      });
    }

    /**
     * Function deleteCache().
     *
     * @param {ComponentBase} component
     * @param {string}        command
     * @param {{}}            query
     */
  }, {
    key: "deleteCache",
    value: function deleteCache(component, command) {
      var query = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      var args = {
        query: query
      };
      this.cache.delete({
        endpoint: this.commandToEndpoint(command, args, this.commandFormats[command]),
        component: component,
        command: command,
        args: args
      });
    }

    /**
     * Function registerFormat().
     *
     * Register's format for each command.
     *
     * @param {string} command
     * @param {string} format
     */
  }, {
    key: "registerFormat",
    value: function registerFormat(command, format) {
      this.commandFormats[command] = format;
    }

    /**
     * Function create().
     *
     * Run a command, that will be translated as endpoint for creating new data.
     *
     * @param {string} command
     * @param {*}      data
     * @param {{}}     query
     * @param {{}}     options
     *
     * @return {*} result
     */
  }, {
    key: "create",
    value: function create(command, data) {
      var query = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      var options = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
      return this.run('create', command, {
        query: query,
        options: options,
        data: data
      });
    }

    /**
     * Function delete().
     *
     * Run a command, that will be translated as endpoint for deleting data.
     *
     * @param {string} command
     * @param {{}}     query
     * @param {{}}     options
     *
     * @return {*} result
     */
  }, {
    key: "delete",
    value: function _delete(command) {
      var query = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      return this.run('delete', command, {
        query: query,
        options: options
      });
    }

    /**
     * Function get().
     *
     * Run a command, that will be translated as endpoint for getting data.
     *
     * @param {string} command
     * @param {{}}     query
     * @param {{}}     options
     *
     * @return {*} result
     */
  }, {
    key: "get",
    value: function get(command) {
      var query = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      return this.run('get', command, {
        query: query,
        options: options
      });
    }

    /**
     * Function update().
     *
     * Run a command, that will be translated as endpoint for updating data.
     *
     * @param {string} command
     * @param {*}      data
     * @param {{}}     query
     * @param {{}}     options
     *
     * @return {*} result
     */
  }, {
    key: "update",
    value: function update(command, data) {
      var query = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      var options = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
      return this.run('update', command, {
        query: query,
        options: options,
        data: data
      });
    }

    /**
     * Function options().
     *
     * Run a command, that will be translated as endpoint for requesting options/information about specific endpoint.
     *
     * @param {string} command
     * @param {{}}     query
     * @param {{}}     options
     *
     * @return {*} result
     */
  }, {
    key: "options",
    value: function options(command, query) {
      var _options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      return this.run('options', command, {
        query: query,
        options: _options
      });
    }
  }, {
    key: "register",
    value: function register(component, command, callback) {
      _superPropGet(Data, "register", this, 3)([component, command, callback]);
      var fullCommandName = component.getNamespace() + '/' + command,
        commandInstance = $e.data.getCommandClass(fullCommandName),
        format = commandInstance !== null && commandInstance !== void 0 && commandInstance.getEndpointFormat ? commandInstance.getEndpointFormat() : false;
      if (format) {
        $e.data.registerFormat(fullCommandName, format);
      }
      return this;
    }

    /**
     * TODO: Add JSDOC typedef for args ( query and options ).
     *
     * @param {DataTypes} type
     * @param {string}    command
     * @param {{}}        args
     *
     * @override
     */
  }, {
    key: "run",
    value: function run(type, command, args) {
      args.options.type = type;
      var _this$commandExtractA = this.commandExtractArgs(command, args);
      command = _this$commandExtractA.command;
      args = _this$commandExtractA.args;
      return _superPropGet(Data, "run", this, 3)([command, args]);
    }
  }, {
    key: "error",
    value: function error(message) {
      throw Error('Data commands: ' + message);
    }
  }]);
}(_commands.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/core/data/cache.js":
/*!*******************************************************!*\
  !*** ../modules/web-cli/assets/js/core/data/cache.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _localStorage = _interopRequireDefault(__webpack_require__(/*! ./storages/local-storage */ "../modules/web-cli/assets/js/core/data/storages/local-storage.js"));
/**
 * @typedef {{}} RequestData
 */
/**
 * @typedef {import('../data')} Data
 */
/**
 * TODO: Search common logic, create functions to reduce code size.
 */
var Cache = exports["default"] = /*#__PURE__*/function () {
  /**
   * Function constructor().
   *
   * Create cache.
   *
   * @param {Data} manager
   */
  function Cache(manager) {
    (0, _classCallCheck2.default)(this, Cache);
    this.manager = manager;
    this.storage = new _localStorage.default();
  }

  /**
   * Function getAsync().
   *
   * Receive from cache. the difference between getAsync() and get() is that receive return it as promise...
   * to fake fetch mechanism.
   *
   * @param {RequestData} requestData
   *
   * @return {(Promise|boolean)} request data
   */
  return (0, _createClass2.default)(Cache, [{
    key: "getAsync",
    value: function getAsync(requestData) {
      var data = this.get(requestData);
      if (null !== data) {
        // If data comes from cache, add 'cache = hit' to requestData.
        requestData.cache = 'hit';
        return new Promise(function (resolve) {
          resolve(data);
        });
      }

      // TODO: Check if possible, always return promise and reject it.
      return false;
    }

    /**
     * Function set().
     *
     * set data to cache.
     *
     * The difference between set() and update() is that set, will modify the data anyway...
     * when update() will only modify exist objects/values.
     *
     * @param {RequestData} requestData
     * @param {*}           data
     */
  }, {
    key: "set",
    value: function set(requestData, data) {
      $e.data.validateRequestData(requestData);
      var componentName = requestData.component.getNamespace(),
        pureEndpoint = requestData.endpoint.replace(componentName + '/', ''),
        pureEndpointParts = pureEndpoint.split('/');
      var newData = {};

      // Example of working with reaming endpoint part(s) can be found at 'cache.spec.js' test: 'load(): deep'.
      // Analyze reaming endpoint.
      if (pureEndpointParts.length && pureEndpoint !== componentName) {
        // Using reaming endpoint parts, to build new data object.
        var result = pureEndpointParts.reduce(function (accumulator, pureEndpointPart) {
          accumulator[pureEndpointPart] = {};
          return accumulator[pureEndpointPart];
        }, newData);

        // 'result' is equal to 'newData' with a deeper pointer, build based on 'pureEndpointParts' ( will effect newData ).
        Object.assign(result, data);
      } else {
        newData = data;
      }
      var oldData = this.storage.getItem(componentName);

      // When have old data, merge it recursively with newData using jQuery.extend().
      if (oldData !== null) {
        newData = jQuery.extend(true, oldData, newData);
      }
      this.storage.setItem(componentName, newData);
    }

    /**
     * Function get().
     *
     * Get from exist storage.
     *
     * @param {RequestData} requestData
     *
     * @return {{}} data
     */
  }, {
    key: "get",
    value: function get(requestData) {
      $e.data.validateRequestData(requestData);
      var componentName = requestData.component.getNamespace(),
        componentData = this.storage.getItem(componentName);
      if (componentData !== null) {
        if (componentName === requestData.endpoint) {
          return componentData;
        }

        // Example of working with reaming endpoint part(s) can be found at 'cache.spec.js' test: 'get(): complex'.
        // Analyze reaming endpoint (Using reduce over endpoint parts, build the right index).
        var pureEndpoint = requestData.endpoint.replace(requestData.component.getNamespace() + '/', ''),
          pureEndpointParts = pureEndpoint.split('/'),
          // eslint-disable-next-line array-callback-return
          result = pureEndpointParts.reduce(function (accumulator, endpointPart) {
            if (accumulator && accumulator[endpointPart]) {
              return accumulator[endpointPart];
            }
          }, componentData);

        // Since $e.data.cache.receive will reject only if null is the result.
        return result || null;
      }
      return null;
    }

    /**
     * Function update().
     *
     * Update only already exist storage, runs over all storage
     *
     * @param {RequestData} requestData
     *
     * @return {boolean} is updated
     */
  }, {
    key: "update",
    value: function update(requestData) {
      $e.data.validateRequestData(requestData, true);
      var endpoint = requestData.endpoint;
      var response = {};

      // Simulate response from cache.
      Object.entries(this.storage.getAll()).forEach(function (_ref) {
        var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
          endpointKey = _ref2[0],
          /* String*/endpointValue = _ref2[1];
        if (endpointValue && endpoint.includes(endpointKey)) {
          // Assuming it is a specific endpoint.
          var oldData = endpointValue,
            pureEndpoint = requestData.endpoint.replace(requestData.component.getNamespace() + '/', ''),
            pureEndpointParts = pureEndpoint.split('/'),
            isComponentUpdate = 1 === pureEndpointParts.length && endpointKey === requestData.endpoint && endpointKey === requestData.component.getNamespace();

          // Component update or specific update?
          if (isComponentUpdate) {
            response = jQuery.extend(true, oldData, requestData.args.data);
          } else {
            var oldSpecificData = pureEndpointParts.reduce(function (accumulator, pureEndpointPart) {
              return accumulator[pureEndpointPart];
            }, oldData);
            response = jQuery.extend(true, oldSpecificData, requestData.args.data);
          }
        }
      });

      // If response not found.
      if (0 === Object.values(response).length) {
        return false;
      }

      // Update cache.
      this.set(requestData, response);
      return true;
    }

    /**
     * Function delete().
     *
     * Delete endpoint from storage.
     *
     * @param {RequestData} requestData
     *
     * @return {boolean} is deleted
     */
  }, {
    key: "delete",
    value: function _delete(requestData) {
      $e.data.validateRequestData(requestData);
      var result = false;
      var componentName = requestData.component.getNamespace();
      if (componentName !== requestData.endpoint) {
        var oldData = this.storage.getItem(componentName),
          newData = {};
        if (null === oldData) {
          return false;
        }
        var pureEndpoint = requestData.endpoint.replace(componentName + '/', ''),
          pureEndpointParts = pureEndpoint.split('/'),
          lastEndpointPart = pureEndpointParts[pureEndpointParts.length - 1];
        pureEndpointParts.reduce(function (accumulator, pureEndpointPart) {
          if (pureEndpointPart === lastEndpointPart) {
            // Null, means delete.
            accumulator[pureEndpointPart] = null;
          } else {
            accumulator[pureEndpointPart] = {};
          }
          return accumulator[pureEndpointPart];
        }, newData);
        if (Object.keys(oldData).length) {
          var _deleteKeys = function deleteKeys(target, nullsObject) {
            if (nullsObject) {
              Object.keys(nullsObject).forEach(function (key) {
                if (nullsObject[key] && 'object' === (0, _typeof2.default)(nullsObject[key])) {
                  _deleteKeys(target[key], nullsObject[key]);
                } else if (null === nullsObject[key]) {
                  delete target[key];
                  result = true;
                }
              });
            } else {
              // Means need to clear all the object.
              Object.keys(target).forEach(function (key) {
                return delete target[key];
              });
            }
            return target;
          };
          this.storage.setItem(componentName, _deleteKeys(oldData, newData));
        }
      } else {
        for (var key in this.storage.getAll()) {
          if (key === requestData.endpoint) {
            this.storage.removeItem(requestData.endpoint);
            result = true;
            break;
          }
        }
      }
      return result;
    }
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/core/data/errors/base-error.js":
/*!*******************************************************************!*\
  !*** ../modules/web-cli/assets/js/core/data/errors/base-error.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _wrapNativeSuper2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/wrapNativeSuper */ "../node_modules/@babel/runtime/helpers/wrapNativeSuper.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _console = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/console */ "../modules/web-cli/assets/js/utils/console.js"));
var _forceMethodImplementation = _interopRequireDefault(__webpack_require__(/*! ../../../utils/force-method-implementation */ "../modules/web-cli/assets/js/utils/force-method-implementation.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var BaseError = exports["default"] = /*#__PURE__*/function (_Error) {
  /**
   * Error constructor.
   *
   * @param {string} message
   * @param {string} code
   * @param {*}      data
   */
  function BaseError() {
    var _this;
    var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
    var code = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
    var data = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
    (0, _classCallCheck2.default)(this, BaseError);
    _this = _callSuper(this, BaseError, [message]);
    /**
     * The server error code.
     *
     * @type {string}
     */
    (0, _defineProperty2.default)(_this, "code", '');
    /**
     * Additional data about the current error.
     *
     * @type {*[]}
     */
    (0, _defineProperty2.default)(_this, "data", []);
    _this.code = code;
    _this.data = data;
    return _this;
  }

  /**
   * Notify a message when the error occurs.
   */
  (0, _inherits2.default)(BaseError, _Error);
  return (0, _createClass2.default)(BaseError, [{
    key: "notify",
    value: function notify() {
      _console.default.error(_objectSpread({
        message: this.message
      }, this));
    }
  }], [{
    key: "create",
    value:
    /**
     * Static helper function to create the error.
     *
     * @param {string} message
     * @param {string} code
     * @param {*}      data
     * @return {BaseError} error
     */
    function create(message) {
      var code = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
      var data = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
      return new this(message, code, data);
    }

    /**
     * Returns the status code of the error.
     */
  }, {
    key: "getHTTPErrorCode",
    value: function getHTTPErrorCode() {
      (0, _forceMethodImplementation.default)();
    }
  }]);
}(/*#__PURE__*/(0, _wrapNativeSuper2.default)(Error));

/***/ }),

/***/ "../modules/web-cli/assets/js/core/data/errors/default-error.js":
/*!**********************************************************************!*\
  !*** ../modules/web-cli/assets/js/core/data/errors/default-error.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.DefaultError = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _baseError = _interopRequireDefault(__webpack_require__(/*! ./base-error */ "../modules/web-cli/assets/js/core/data/errors/base-error.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var DefaultError = exports.DefaultError = /*#__PURE__*/function (_BaseError) {
  function DefaultError() {
    (0, _classCallCheck2.default)(this, DefaultError);
    return _callSuper(this, DefaultError, arguments);
  }
  (0, _inherits2.default)(DefaultError, _BaseError);
  return (0, _createClass2.default)(DefaultError, null, [{
    key: "getHTTPErrorCode",
    value: function getHTTPErrorCode() {
      return 501;
    }
  }]);
}(_baseError.default);
var _default = exports["default"] = DefaultError;

/***/ }),

/***/ "../modules/web-cli/assets/js/core/data/errors/error-404.js":
/*!******************************************************************!*\
  !*** ../modules/web-cli/assets/js/core/data/errors/error-404.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Error404 = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _baseError = _interopRequireDefault(__webpack_require__(/*! ./base-error */ "../modules/web-cli/assets/js/core/data/errors/base-error.js"));
var _console = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/console */ "../modules/web-cli/assets/js/utils/console.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Error404 = exports.Error404 = /*#__PURE__*/function (_BaseError) {
  function Error404() {
    (0, _classCallCheck2.default)(this, Error404);
    return _callSuper(this, Error404, arguments);
  }
  (0, _inherits2.default)(Error404, _BaseError);
  return (0, _createClass2.default)(Error404, [{
    key: "notify",
    value: function notify() {
      _console.default.warn(this.message);
    }
  }], [{
    key: "getHTTPErrorCode",
    value: function getHTTPErrorCode() {
      return 404;
    }
  }]);
}(_baseError.default);
var _default = exports["default"] = Error404;

/***/ }),

/***/ "../modules/web-cli/assets/js/core/data/errors/index.js":
/*!**************************************************************!*\
  !*** ../modules/web-cli/assets/js/core/data/errors/index.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "DefaultError", ({
  enumerable: true,
  get: function get() {
    return _defaultError.DefaultError;
  }
}));
Object.defineProperty(exports, "Error404", ({
  enumerable: true,
  get: function get() {
    return _error.Error404;
  }
}));
var _defaultError = __webpack_require__(/*! ./default-error */ "../modules/web-cli/assets/js/core/data/errors/default-error.js");
var _error = __webpack_require__(/*! ./error-404 */ "../modules/web-cli/assets/js/core/data/errors/error-404.js");

/***/ }),

/***/ "../modules/web-cli/assets/js/core/data/storages/base-prefix-storage.js":
/*!******************************************************************************!*\
  !*** ../modules/web-cli/assets/js/core/data/storages/base-prefix-storage.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _baseStorage = _interopRequireDefault(__webpack_require__(/*! elementor-api/core/data/storages/base-storage */ "../modules/web-cli/assets/js/core/data/storages/base-storage.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var BasePrefixStorage = exports["default"] = /*#__PURE__*/function (_BaseStorage) {
  function BasePrefixStorage() {
    (0, _classCallCheck2.default)(this, BasePrefixStorage);
    return _callSuper(this, BasePrefixStorage, arguments);
  }
  (0, _inherits2.default)(BasePrefixStorage, _BaseStorage);
  return (0, _createClass2.default)(BasePrefixStorage, [{
    key: "clear",
    value: function clear() {
      var _this = this;
      Object.keys(this.getAll()).forEach(function (key) {
        return _this.removeItem(key);
      });
    }
  }, {
    key: "getItem",
    value: function getItem(key) {
      return _superPropGet(BasePrefixStorage, "getItem", this, 3)([BasePrefixStorage.DEFAULT_KEY_PREFIX + key]);
    }
  }, {
    key: "removeItem",
    value: function removeItem(key) {
      return _superPropGet(BasePrefixStorage, "removeItem", this, 3)([BasePrefixStorage.DEFAULT_KEY_PREFIX + key]);
    }
  }, {
    key: "setItem",
    value: function setItem(key, value) {
      return _superPropGet(BasePrefixStorage, "setItem", this, 3)([BasePrefixStorage.DEFAULT_KEY_PREFIX + key, value]);
    }
  }, {
    key: "getAll",
    value: function getAll() {
      var _this2 = this;
      var DEFAULT_KEY_PREFIX = BasePrefixStorage.DEFAULT_KEY_PREFIX,
        keys = Object.keys(this.provider),
        result = {};
      keys.forEach(function (key) {
        if (key.startsWith(DEFAULT_KEY_PREFIX)) {
          key = key.replace(DEFAULT_KEY_PREFIX, '');
          result[key] = _this2.getItem(key);
        }
      });
      return result;
    }
  }]);
}(_baseStorage.default);
(0, _defineProperty2.default)(BasePrefixStorage, "DEFAULT_KEY_PREFIX", 'e_');

/***/ }),

/***/ "../modules/web-cli/assets/js/core/data/storages/base-storage.js":
/*!***********************************************************************!*\
  !*** ../modules/web-cli/assets/js/core/data/storages/base-storage.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
/**
 * TODO: Merge all storage's to one.
 * Using this technique give's the ability to use JSDOC from 'window.storage'.
 *
 * @implements {Storage}
 */
var BaseStorage = exports["default"] = /*#__PURE__*/function () {
  /**
   * Create storage wrapper.
   *
   * @param {Storage} provider
   */
  function BaseStorage(provider) {
    (0, _classCallCheck2.default)(this, BaseStorage);
    if (BaseStorage === (this instanceof BaseStorage ? this.constructor : void 0)) {
      throw new TypeError('Cannot construct BaseStorage instances directly');
    }
    this.provider = provider;
  }
  return (0, _createClass2.default)(BaseStorage, [{
    key: "clear",
    value: function clear() {
      return this.provider.clear();
    }
  }, {
    key: "getItem",
    value: function getItem(key) {
      var result = this.provider.getItem(key);
      if (null !== result) {
        return JSON.parse(result);
      }
      return result;
    }
  }, {
    key: "key",
    value: function key(index) {
      return this.provider.key(index);
    }
  }, {
    key: "removeItem",
    value: function removeItem(key) {
      return this.provider.removeItem(key);
    }
  }, {
    key: "setItem",
    value: function setItem(key, value) {
      return this.provider.setItem(key, JSON.stringify(value));
    }
  }, {
    key: "getAll",
    value: function getAll() {
      var _this = this;
      var keys = Object.keys(this.provider),
        result = {};
      keys.forEach(function (key) {
        result[key] = _this.getItem(key);
      });
      return result;
    }
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/core/data/storages/local-storage.js":
/*!************************************************************************!*\
  !*** ../modules/web-cli/assets/js/core/data/storages/local-storage.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _basePrefixStorage = _interopRequireDefault(__webpack_require__(/*! ./base-prefix-storage */ "../modules/web-cli/assets/js/core/data/storages/base-prefix-storage.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var LocalStorage = exports["default"] = /*#__PURE__*/function (_BasePrefixStorage) {
  function LocalStorage() {
    (0, _classCallCheck2.default)(this, LocalStorage);
    return _callSuper(this, LocalStorage, [localStorage]);
  }
  (0, _inherits2.default)(LocalStorage, _BasePrefixStorage);
  return (0, _createClass2.default)(LocalStorage, [{
    key: "debug",
    value: function debug() {
      var entries = this.getAll(),
        ordered = {};
      Object.keys(entries).sort().forEach(function (key) {
        var value = entries[key];
        ordered[key] = value;
      });
      return ordered;
    }
  }]);
}(_basePrefixStorage.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/core/hooks.js":
/*!**************************************************!*\
  !*** ../modules/web-cli/assets/js/core/hooks.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _data = _interopRequireDefault(__webpack_require__(/*! ./hooks/data.js */ "../modules/web-cli/assets/js/core/hooks/data.js"));
var _ui = _interopRequireDefault(__webpack_require__(/*! ./hooks/ui.js */ "../modules/web-cli/assets/js/core/hooks/ui.js"));
/**
 * @typedef {import('../modules/hook-base')} HookBase
 */
var Hooks = exports["default"] = /*#__PURE__*/function () {
  function Hooks() {
    (0, _classCallCheck2.default)(this, Hooks);
    (0, _defineProperty2.default)(this, "data", new _data.default());
    (0, _defineProperty2.default)(this, "ui", new _ui.default());
  }
  return (0, _createClass2.default)(Hooks, [{
    key: "activate",
    value:
    /**
     * Function activate().
     *
     * Activate all hooks.
     */
    function activate() {
      this.getTypes().forEach(function (hooksType) {
        hooksType.activate();
      });
    }

    /**
     * Function deactivate().
     *
     * Deactivate all hooks.
     */
  }, {
    key: "deactivate",
    value: function deactivate() {
      this.getTypes().forEach(function (hooksType) {
        hooksType.deactivate();
      });
    }
  }, {
    key: "getAll",
    value: function getAll() {
      var flat = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      var result = {};
      this.getTypes().forEach(function (hooksType) {
        result[hooksType.getType()] = hooksType.getAll(flat);
      });
      return result;
    }
  }, {
    key: "getTypes",
    value: function getTypes() {
      return [this.data, this.ui];
    }
  }, {
    key: "getType",
    value: function getType(type) {
      return this.getTypes().find(function (hooks) {
        return type === hooks.getType();
      });
    }

    /**
     * Function register().
     *
     * Register hook.
     *
     * @param {string}   type
     * @param {string}   event
     * @param {HookBase} instance
     *
     * @return {{}} Created callback
     */
  }, {
    key: "register",
    value: function register(type, event, instance) {
      return this.getType(type).register(event, instance);
    }

    /**
     * Function run().
     *
     * Run's a hook.
     *
     * @param {string} type
     * @param {string} event
     * @param {string} command
     * @param {{}}     args
     * @param {*}      result
     *
     * @return {boolean} result
     */
  }, {
    key: "run",
    value: function run(type, event, command, args) {
      var result = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : undefined;
      return this.getType(type).run(event, command, args, result);
    }

    /**
     * Function registerDataAfter().
     *
     * Register data hook that's run after the command.
     *
     * @param {HookBase} instance
     *
     * @return {{}} callback
     */
  }, {
    key: "registerDataAfter",
    value: function registerDataAfter(instance) {
      return this.register('data', 'after', instance);
    }

    /**
     * Function registerDataCatch().
     *
     * Register data hook that's run when the command fails.
     *
     * @param {HookBase} instance
     *
     * @return {{}} callback
     */
  }, {
    key: "registerDataCatch",
    value: function registerDataCatch(instance) {
      return this.register('data', 'catch', instance);
    }

    /**
     * Function registerDataDependency().
     *
     * Register data hook that's run before the command as dependency.
     *
     * @param {HookBase} instance
     *
     * @return {{}} callback
     */
  }, {
    key: "registerDataDependency",
    value: function registerDataDependency(instance) {
      return this.register('data', 'dependency', instance);
    }

    /**
     * Function registerUIAfter().
     *
     * Register UI hook that's run after the commands run.
     *
     * @param {HookBase} instance
     *
     * @return {{}} callback
     */
  }, {
    key: "registerUIAfter",
    value: function registerUIAfter(instance) {
      return this.register('ui', 'after', instance);
    }

    /**
     * Function registerUICatch().
     *
     * Register UI hook that's run when the command fails.
     *
     * @param {HookBase} instance
     *
     * @return {{}} callback
     */
  }, {
    key: "registerUICatch",
    value: function registerUICatch(instance) {
      return this.register('ui', 'catch', instance);
    }

    /**
     * Function registerUIBefore().
     *
     * Register UI hook that's run before the command.
     *
     * @param {HookBase} instance
     *
     * @return {{}} callback
     */
  }, {
    key: "registerUIBefore",
    value: function registerUIBefore(instance) {
      return this.register('ui', 'before', instance);
    }

    /**
     * Function runDataAfter().
     *
     * Run data hook that's run after the command.
     *
     * @param {string} command
     * @param {{}}     args
     * @param {*}      result
     *
     * @return {boolean} result
     */
  }, {
    key: "runDataAfter",
    value: function runDataAfter(command, args, result) {
      return this.run('data', 'after', command, args, result);
    }

    /**
     * Function runDataCatch().
     *
     * Run data hook that's run when the command fails.
     *
     * @param {string} command
     * @param {{}}     args
     * @param {*}      error
     *
     * @return {boolean} result
     */
  }, {
    key: "runDataCatch",
    value: function runDataCatch(command, args, error) {
      return this.run('data', 'catch', command, args, error);
    }

    /**
     * Function runDataDependency().
     *
     * Run data hook that's run before the command as dependency.
     *
     * @param {string} command
     * @param {{}}     args
     *
     * @return {boolean} result
     */
  }, {
    key: "runDataDependency",
    value: function runDataDependency(command, args) {
      return this.run('data', 'dependency', command, args);
    }

    /**
     * Function runUIAfter().
     *
     * Run UI hook that's run after the commands run.
     *
     * @param {string} command
     * @param {{}}     args
     * @param {*}      result
     *
     * @return {boolean} result
     */
  }, {
    key: "runUIAfter",
    value: function runUIAfter(command, args, result) {
      return this.run('ui', 'after', command, args, result);
    }

    /**
     * Function runUICatch().
     *
     * Run UI hook that's run when the command fails.
     *
     * @param {string} command
     * @param {{}}     args
     * @param {*}      e
     *
     * @return {boolean} result
     */
  }, {
    key: "runUICatch",
    value: function runUICatch(command, args, e) {
      return this.run('ui', 'catch', command, args, e);
    }

    /**
     * Function runUIBefore().
     *
     * Run UI hook that's run before the command.
     *
     * @param {string} command
     * @param {{}}     args
     *
     * @return {boolean} result
     */
  }, {
    key: "runUIBefore",
    value: function runUIBefore(command, args) {
      return this.run('ui', 'before', command, args);
    }
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/core/hooks/base.js":
/*!*******************************************************!*\
  !*** ../modules/web-cli/assets/js/core/hooks/base.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _console = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/console */ "../modules/web-cli/assets/js/utils/console.js"));
var _module = _interopRequireDefault(__webpack_require__(/*! elementor-assets-js/modules/imports/module.js */ "../assets/dev/js/modules/imports/module.js"));
var _forceMethodImplementation = _interopRequireDefault(__webpack_require__(/*! ../../utils/force-method-implementation */ "../modules/web-cli/assets/js/utils/force-method-implementation.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @typedef {import('../../modules/hook-base')} HookBase
 */
var HooksBase = exports["default"] = /*#__PURE__*/function (_Module) {
  /**
   * Function constructor().
   *
   * Create hooks base.
   *
   * @param {{}} args
   */
  function HooksBase() {
    var _this;
    (0, _classCallCheck2.default)(this, HooksBase);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, HooksBase, [].concat(args));

    /**
     * Current command.
     *
     * @type {string}
     */
    _this.current = '';

    /**
     * Array of ids which in use.
     *
     * @type {Array}
     */
    _this.usedIds = [];

    /**
     * Object of callbacks that was bound by container type.
     *
     * @type {{}}
     */
    _this.callbacks = {
      after: {},
      catch: {}
    };

    /**
     * Object of depth.
     *
     * @type {{}}
     */
    _this.depth = {
      after: {},
      catch: {}
    };
    _this.callbacksFlatList = {};
    return _this;
  }
  (0, _inherits2.default)(HooksBase, _Module);
  return (0, _createClass2.default)(HooksBase, [{
    key: "activate",
    value: function activate() {
      Object.values(this.getAll(true)).forEach(function (callback) {
        callback.activate();
      });
    }
  }, {
    key: "deactivate",
    value: function deactivate() {
      Object.values(this.getAll(true)).forEach(function (callback) {
        callback.deactivate();
      });
    }

    // eslint-disable-next-line jsdoc/require-returns-check
    /**
     * Function getType().
     *
     * Returns type eg: ( event, hook, etc ... ).
     *
     * @return {string} type
     */
  }, {
    key: "getType",
    value: function getType() {
      (0, _forceMethodImplementation.default)();
    }
  }, {
    key: "get",
    value: function get(id) {
      return this.callbacksFlatList[id];
    }

    /**
     * Function getAll().
     *
     * Return all possible callbacks.
     *
     * @param {boolean} flat
     *
     * @return {{}} all callbacks
     */
  }, {
    key: "getAll",
    value: function getAll() {
      var _this2 = this;
      var flat = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      if (flat) {
        return this.callbacksFlatList;
      }
      var result = {};
      Object.keys(this.callbacks).forEach(function (event) {
        if (!result[event]) {
          result[event] = [];
        }
        Object.keys(_this2.callbacks[event]).forEach(function (command) {
          result[event].push({
            command: command,
            callbacks: _this2.callbacks[event][command]
          });
        });
      });
      return result;
    }

    /**
     * Function getCurrent();
     *
     * Return current command.
     *
     * @return {string} current command
     */
  }, {
    key: "getCurrent",
    value: function getCurrent() {
      return this.current;
    }

    /**
     * Function getUsedIds().
     *
     * Returns the current used ids.
     *
     * @return {Array} used IDs
     */
  }, {
    key: "getUsedIds",
    value: function getUsedIds() {
      return this.usedIds;
    }

    /**
     * Function getCallbacks().
     *
     * Get available callbacks for specific event and command.
     *
     * @param {string} event
     * @param {string} command
     * @param {*}      args
     * @return {(Array | boolean)} callbacks
     */
  }, {
    key: "getCallbacks",
    value: function getCallbacks(event, command, args) {
      var _args$containers = args.containers,
        containers = _args$containers === void 0 ? [args.container] : _args$containers,
        containerType = containers[0] ? containers[0].type : false;
      var callbacks = [];
      if (this.callbacks[event] && this.callbacks[event][command]) {
        if (containerType && this.callbacks[event][command][containerType]) {
          callbacks = callbacks.concat(this.callbacks[event][command][containerType]);
        }
        if (this.callbacks[event][command].all) {
          callbacks = callbacks.concat(this.callbacks[event][command].all);
        }
      }
      if (callbacks.length) {
        return callbacks;
      }
      return false;
    }

    /**
     * Function checkEvent().
     *
     * Validate if the event is available.
     *
     * @param {string} event
     */
  }, {
    key: "checkEvent",
    value: function checkEvent(event) {
      if (-1 === Object.keys(this.callbacks).indexOf(event)) {
        throw Error("".concat(this.getType(), ": '").concat(event, "' is not available."));
      }
    }

    /**
     * Function checkInstance().
     *
     * Validate given instance.
     *
     * @param {HookBase} instance
     */
  }, {
    key: "checkInstance",
    value: function checkInstance(instance) {
      if (instance.getType() !== this.getType()) {
        throw new Error("invalid instance, please use: 'elementor-api/modules/hook-base.js'. ");
      }
    }

    /**
     * Function checkId().
     *
     * Validate if the id is not used before.
     *
     * @param {string} id
     */
  }, {
    key: "checkId",
    value: function checkId(id) {
      if (-1 !== this.usedIds.indexOf(id)) {
        throw Error("id: '".concat(id, "' is already in use."));
      }
    }

    /**
     * Function shouldRun().
     *
     * Determine if the event should run.
     *
     * @param {Array} callbacks
     *
     * @return {boolean} true if there are callbacks, otherwise false
     *
     * @throws {Error}
     */
  }, {
    key: "shouldRun",
    value: function shouldRun(callbacks) {
      return !!callbacks && callbacks.length;
    }

    /**
     * Function register().
     *
     * Register the callback instance.
     *
     * @param {string}   event
     * @param {HookBase} instance
     *
     * @return {{}} Created callback
     */
  }, {
    key: "register",
    value: function register(event, instance) {
      var command = instance.getCommand(),
        id = instance.getId(),
        containerType = instance.getContainerType();
      this.checkEvent(event);
      this.checkInstance(instance);
      this.checkId(id);
      return this.registerCallback(id, event, command, instance, containerType);
    }

    /**
     * Function registerCallback().
     *
     * Register callback.
     *
     * @param {string}   id
     * @param {string}   event
     * @param {string}   command
     * @param {HookBase} instance
     * @param {string}   containerType
     *
     *                                 TODO: Consider replace with typedef.
     * @return {{callback: *, id: *, isActive: boolean}} callback
     */
  }, {
    key: "registerCallback",
    value: function registerCallback(id, event, command, instance, containerType) {
      if (!this.callbacks[event][command]) {
        this.callbacks[event][command] = [];
      }

      // Save used id(s).
      this.usedIds.push(id);
      if (!this.callbacks[event][command]) {
        this.callbacks[event][command] = {};
      }

      // TODO: Create HookCallback class/type.
      var callback = {
        id: id,
        callback: instance.run.bind(instance),
        isActive: true,
        activate: function activate() {
          this.isActive = true;
        },
        deactivate: function deactivate() {
          this.isActive = false;
        }
      };
      if (containerType) {
        if (!this.callbacks[event][command][containerType]) {
          this.callbacks[event][command][containerType] = [];
        }
        this.callbacks[event][command][containerType].push(callback);
      } else {
        if (!this.callbacks[event][command].all) {
          this.callbacks[event][command].all = [];
        }
        this.callbacks[event][command].all.push(callback);
      }
      this.callbacksFlatList[callback.id] = callback;
      return callback;
    }

    /**
     * Function run().
     *
     * Run the callbacks.
     *
     * @param {string} event
     * @param {string} command
     * @param {{}}     args
     * @param {*}      result
     *
     * @return {*} results
     */
  }, {
    key: "run",
    value: function run(event, command, args) {
      var result = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : undefined;
      var callbacks = this.getCallbacks(event, command, args);
      if (this.shouldRun(callbacks)) {
        this.current = command;
        this.onRun(command, args, event);
        return this.runCallbacks(event, command, callbacks, args, result);
      }
      return false;
    }

    /**
     * Function runCallbacks().
     *
     * Run's the given callbacks.
     *
     * @param {string} event
     * @param {string} command
     * @param {Array}  callbacks
     * @param {{}}     args
     * @param {[]}     result
     */
  }, {
    key: "runCallbacks",
    value: function runCallbacks(event, command, callbacks, args, result) {
      var callbacksResult = [];
      for (var i in callbacks) {
        var callback = callbacks[i];
        if (!callback.isActive) {
          continue;
        }

        // If not exist, set zero.
        if (undefined === this.depth[event][callback.id]) {
          this.depth[event][callback.id] = 0;
        }
        this.depth[event][callback.id]++;

        // Prevent recursive hooks.
        if (1 === this.depth[event][callback.id]) {
          this.onCallback(command, args, event, callback.id);
          try {
            var callbackResult = this.runCallback(event, callback, args, result);
            if (!callbackResult) {
              throw Error("Callback failed, event: '".concat(event, "'"));
            }
            callbacksResult.push(callbackResult);
          } catch (e) {
            // If its 'Hook-Break' then parent `try {}` will handle it.
            if (e instanceof $e.modules.HookBreak) {
              throw e;
            }
            _console.default.error(e);
          }
        }
        this.depth[event][callback.id]--;
      }
      return callbacksResult;
    }

    // eslint-disable-next-line jsdoc/require-returns-check
    /**
     * Function runCallback().
     *
     * Run's the given callback.
     *
     * @param {string} event
     * @param {{}}     callback
     * @param {{}}     args
     * @param {*}      result
     *
     * @return {*} results
     *
     * @throws {Error}
     */
  }, {
    key: "runCallback",
    value: function runCallback(event, callback, args, result) {
      // eslint-disable-line no-unused-vars
      (0, _forceMethodImplementation.default)();
    }

    /**
     * Function onRun().
     *
     * Called before run a set of callbacks.
     *
     * @param {string} command
     * @param {{}}     args
     * @param {string} event
     *
     * @throws {Error}
     */
  }, {
    key: "onRun",
    value: function onRun(command, args, event) {
      // eslint-disable-line no-unused-vars
      (0, _forceMethodImplementation.default)();
    }

    /**
     * Function onCallback().
     *
     * Called before a single callback.
     *
     * @param {string} command
     * @param {{}}     args
     * @param {string} event
     * @param {string} id
     *
     * @throws {Error}
     */
  }, {
    key: "onCallback",
    value: function onCallback(command, args, event, id) {
      // eslint-disable-line no-unused-vars
      (0, _forceMethodImplementation.default)();
    }
  }]);
}(_module.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/core/hooks/data.js":
/*!*******************************************************!*\
  !*** ../modules/web-cli/assets/js/core/hooks/data.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ./base.js */ "../modules/web-cli/assets/js/core/hooks/base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var Data = exports["default"] = /*#__PURE__*/function (_HooksBase) {
  function Data() {
    var _this;
    (0, _classCallCheck2.default)(this, Data);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, Data, [].concat(args));
    _this.callbacks.dependency = {};
    _this.depth.dependency = {};
    return _this;
  }
  (0, _inherits2.default)(Data, _HooksBase);
  return (0, _createClass2.default)(Data, [{
    key: "getType",
    value: function getType() {
      return 'data';
    }
  }, {
    key: "runCallback",
    value: function runCallback(event, callback, args, result) {
      switch (event) {
        case 'dependency':
          {
            // If callback returns false and its dependency, then 'Hook-Break'.
            if (!callback.callback(args)) {
              this.depth[event][callback.id]--;

              // Throw custom break to be catch by the base for 'Safe' exit.
              throw new $e.modules.HookBreak();
            }
            return true;
          }
        case 'catch':
        case 'after':
          {
            /**
             * When handling HOOK which is data after (not breakable),
             * even the result of the callback is negative, it is required to return positive,
             * since result of runCallback determine if the callback succeeded.
             */
            return callback.callback(args, result) || 'after' === event;
          }
      }
      return false;
    }
  }, {
    key: "shouldRun",
    value: function shouldRun(callbacks) {
      return _superPropGet(Data, "shouldRun", this, 3)([callbacks]) && elementor.documents.getCurrent().history.getActive();
    }
  }, {
    key: "onRun",
    value: function onRun(command, args, event) {
      if (!$e.devTools) {
        return;
      }
      $e.devTools.log.callbacks().run(this.getType(), command, args, event);
    }
  }, {
    key: "onCallback",
    value: function onCallback(command, args, event, id) {
      if (!$e.devTools) {
        return;
      }
      $e.devTools.log.callbacks().callback(this.getType(), command, args, event, id);
    }
  }]);
}(_base.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/core/hooks/ui.js":
/*!*****************************************************!*\
  !*** ../modules/web-cli/assets/js/core/hooks/ui.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ./base */ "../modules/web-cli/assets/js/core/hooks/base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Ui = exports["default"] = /*#__PURE__*/function (_HooksBase) {
  function Ui() {
    var _this;
    (0, _classCallCheck2.default)(this, Ui);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, Ui, [].concat(args));
    _this.callbacks.before = {};
    _this.depth.before = {};
    return _this;
  }
  (0, _inherits2.default)(Ui, _HooksBase);
  return (0, _createClass2.default)(Ui, [{
    key: "getType",
    value: function getType() {
      return 'ui';
    }
  }, {
    key: "runCallback",
    value: function runCallback(event, callback, args, result) {
      switch (event) {
        case 'before':
          callback.callback(args);
          break;
        case 'catch':
        case 'after':
          callback.callback(args, result);
          break;
        default:
          return false;
      }
      return true;
    }
  }, {
    key: "onRun",
    value: function onRun(command, args, event) {
      if (!$e.devTools) {
        return;
      }
      $e.devTools.log.callbacks().run(this.getType(), command, args, event);
    }
  }, {
    key: "onCallback",
    value: function onCallback(command, args, event, id) {
      if (!$e.devTools) {
        return;
      }
      $e.devTools.log.callbacks().callback(this.getType(), command, args, event, id);
    }
  }]);
}(_base.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/core/routes.js":
/*!***************************************************!*\
  !*** ../modules/web-cli/assets/js/core/routes.js ***!
  \***************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commands = _interopRequireDefault(__webpack_require__(/*! ./commands */ "../modules/web-cli/assets/js/core/commands.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var Routes = exports["default"] = /*#__PURE__*/function (_Commands) {
  function Routes() {
    var _this;
    (0, _classCallCheck2.default)(this, Routes);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, Routes, [].concat(args));
    _this.savedStates = {};
    _this.historyPerComponent = {};
    return _this;
  }
  (0, _inherits2.default)(Routes, _Commands);
  return (0, _createClass2.default)(Routes, [{
    key: "refreshContainer",
    value: function refreshContainer(container) {
      var currentRoute = this.getCurrent(container),
        currentArgs = this.getCurrentArgs(container);
      this.clearCurrent(container);
      this.to(currentRoute, currentArgs);
    }
  }, {
    key: "getHistory",
    value: function getHistory() {
      var namespaceRoot = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      if (namespaceRoot) {
        return this.historyPerComponent[namespaceRoot] || [];
      }
      return this.historyPerComponent;
    }
  }, {
    key: "clearHistory",
    value: function clearHistory(namespaceRoot) {
      delete this.historyPerComponent[namespaceRoot];
    }
  }, {
    key: "clearCurrent",
    value: function clearCurrent(container) {
      var route = this.current[container];
      if (!route) {
        return;
      }
      this.detachCurrent(container);
      this.getComponent(route).onCloseRoute(route);
      this.dispatchOnClose(route);
    }
  }, {
    key: "clear",
    value: function clear() {
      var _this2 = this;
      Object.keys(this.current).forEach(function (container) {
        return _this2.clearCurrent(container);
      });
    }
  }, {
    key: "saveState",
    value: function saveState(container) {
      this.savedStates[container] = {
        route: this.current[container],
        args: this.currentArgs[container]
      };
      return this;
    }
  }, {
    key: "restoreState",
    value: function restoreState(container) {
      if (!this.savedStates[container]) {
        return false;
      }
      this.to(this.savedStates[container].route, this.savedStates[container].args);
      return true;
    }
  }, {
    key: "validateRun",
    value: function validateRun(route) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      if (!_superPropGet(Routes, "validateRun", this, 3)([route, args])) {
        return false;
      }
      if (this.is(route, args) && !args.refresh) {
        return false;
      }
      var component = this.getComponent(route);
      if (!component.isOpen || args.reOpen) {
        component.isOpen = component.open(args);
      }
      return component.isOpen;
    }

    /**
     * @override
     */
  }, {
    key: "beforeRun",
    value: function beforeRun(route, args) {
      var component = this.getComponent(route),
        container = component.getServiceName(),
        oldRoute = this.current[container];
      if (oldRoute) {
        this.getComponent(oldRoute).onCloseRoute(oldRoute);
      }
      _commands.default.trace.push(route);
      _superPropGet(Routes, "beforeRun", this, 3)([route, args, false]);
      this.attachCurrent(container, route, args);

      // In the previous condition, `$e.routes.is()` resolves the old route as active (because the actual route
      // switching happens inside `this.attachCurrent()`), so we can't use it there.
      if (oldRoute) {
        this.dispatchOnClose(oldRoute);
      }
    }
  }, {
    key: "to",
    value: function to(route, args) {
      var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {
        history: true
      };
      this.run(route, args);
      var namespaceRoot = this.getComponent(route).getServiceName();
      if (options.history) {
        if (!this.historyPerComponent[namespaceRoot]) {
          this.historyPerComponent[namespaceRoot] = [];
        }
        this.historyPerComponent[namespaceRoot].push({
          route: route,
          args: args
        });
      }
    }
  }, {
    key: "back",
    value: function back(namespaceRoot) {
      var history = this.getHistory(namespaceRoot);

      // Remove current;
      history.pop();
      var last = history.pop();
      if (!last) {
        return;
      }
      this.to(last.route, last.args);
    }

    // Don't use the event object.
  }, {
    key: "runShortcut",
    value: function runShortcut(command) {
      this.to(command);
    }

    // Don't clear current route.
  }, {
    key: "afterRun",
    value: function afterRun(route, args) {
      var results = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : undefined;
      var component = this.getComponent(route);
      component.onRoute(route, args);
      this.dispatchOnOpen(route);
      _superPropGet(Routes, "afterRun", this, 3)([route, args, results, false]);
      _commands.default.trace.pop();
    }
  }, {
    key: "is",
    value: function is(route) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      if (!_superPropGet(Routes, "is", this, 3)([route])) {
        return false;
      }
      var container = this.getComponent(route).getServiceName();
      return _.isEqual(args, this.currentArgs[container]);
    }
  }, {
    key: "isPartOf",
    value: function isPartOf(route) {
      /**
       * Check against current command hierarchically.
       * For example `is( 'panel' )` will be true for `panel/elements`
       * `is( 'panel/editor' )` will be true for `panel/editor/style`
       */
      var parts = route.split('/'),
        container = parts[0],
        toCheck = [],
        currentParts = this.current[container] ? this.current[container].split('/') : [];
      var match = false;
      currentParts.forEach(function (part) {
        toCheck.push(part);
        if (toCheck.join('/') === route) {
          match = true;
        }
      });
      return match;
    }
  }, {
    key: "error",
    value: function error(message) {
      throw Error('Routes: ' + message);
    }
  }, {
    key: "dispatchOnOpen",
    value: function dispatchOnOpen(route) {
      window.dispatchEvent(new CustomEvent('elementor/routes/open', {
        detail: {
          route: route
        }
      }));
    }
  }, {
    key: "dispatchOnClose",
    value: function dispatchOnClose(route) {
      window.dispatchEvent(new CustomEvent('elementor/routes/close', {
        detail: {
          route: route
        }
      }));
    }
  }]);
}(_commands.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/core/shortcuts.js":
/*!******************************************************!*\
  !*** ../modules/web-cli/assets/js/core/shortcuts.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _environment = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/environment */ "../modules/web-cli/assets/js/utils/environment.js"));
var _console = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/console */ "../modules/web-cli/assets/js/utils/console.js"));
var Shortcuts = exports["default"] = /*#__PURE__*/function () {
  function Shortcuts($window) {
    (0, _classCallCheck2.default)(this, Shortcuts);
    this.specialKeys = {
      13: 'enter',
      27: 'esc',
      38: 'up',
      40: 'down',
      46: 'del',
      191: '?'
    };
    this.component = '';
    this.handlers = {};
    this.bindListener($window);
  }
  return (0, _createClass2.default)(Shortcuts, [{
    key: "bindListener",
    value: function bindListener($window) {
      var _this = this;
      $window.on('keydown', function (event) {
        return _this.handle(event);
      });
    }
  }, {
    key: "getAll",
    value: function getAll() {
      var shortcuts = {};
      jQuery.each(this.handlers, function (key, handler) {
        jQuery.each(handler, function (index, config) {
          shortcuts[config.command] = key;
        });
      });
      return shortcuts;
    }

    /**
     * @param {string}   shortcuts
     * @param {Object}   args
     * @param {Function} args.callback    Required
     * @param {string}   args.component   Optional
     * @param {Function} args.dependency  Optional
     * @param {Array}    args.exclude     Optional
     * @param {boolean}  args.allowAltKey Optional
     */
  }, {
    key: "register",
    value: function register(shortcuts, args) {
      var _this2 = this;
      shortcuts.replace(' ', '').split(',').forEach(function (shortcut) {
        if (!_this2.handlers[shortcut]) {
          _this2.handlers[shortcut] = [];
        }
        _this2.handlers[shortcut].push(args);
      });
    }
  }, {
    key: "unregister",
    value: function unregister(shortcuts, args) {
      var _this3 = this;
      shortcuts.replace(' ', '').split(',').forEach(function (shortcut) {
        _this3.handlers[shortcut].forEach(function (index, handler) {
          if (args === handler) {
            delete _this3.handlers[shortcut][index];
          }
        });
      });
    }
  }, {
    key: "handle",
    value: function handle(event) {
      var handlers = this.getHandlersByPriority(event);
      if (!handlers) {
        return;
      }
      var filteredHandlers = handlers.filter(function (handler) {
        if (handler.exclude && -1 !== handler.exclude.indexOf('input')) {
          var $target = jQuery(event.target);
          if ($target.is(':input, .elementor-input') || $target.closest('[contenteditable="true"]').length) {
            return false;
          }
        }
        if (handler.dependency && !handler.dependency(event)) {
          return false;
        }

        // Fix for some keyboard sources that consider alt key as ctrl key
        if (!handler.allowAltKey && event.altKey) {
          return false;
        }
        return true;
      });
      if (!filteredHandlers.length) {
        return;
      }
      if (1 < filteredHandlers.length && elementorWebCliConfig.isDebug) {
        _console.default.warn('Multiple handlers for shortcut.', filteredHandlers, event);
      }
      event.preventDefault();
      filteredHandlers[0].callback(event);
    }
  }, {
    key: "isControlEvent",
    value: function isControlEvent(event) {
      return event[_environment.default.mac ? 'metaKey' : 'ctrlKey'];
    }
  }, {
    key: "getEventShortcut",
    value: function getEventShortcut(event) {
      var shortcut = [];
      if (event.altKey) {
        shortcut.push('alt');
      }
      if (this.isControlEvent(event)) {
        shortcut.push('ctrl');
      }
      if (event.shiftKey) {
        shortcut.push('shift');
      }
      if (this.specialKeys[event.which]) {
        shortcut.push(this.specialKeys[event.which]);
      } else {
        shortcut.push(String.fromCharCode(event.which).toLowerCase());
      }
      return shortcut.join('+');
    }
  }, {
    key: "isActiveScope",
    value: function isActiveScope(scopes) {
      var activeComponents = Object.keys($e.components.activeComponents),
        activeComponent = activeComponents[activeComponents.length - 1],
        component = $e.components.get(activeComponent);
      if (!component) {
        return false;
      }
      var namespace = component.getNamespace();
      var filteredByNamespace = scopes.some(function (scope) {
        return namespace === scope;
      });
      if (filteredByNamespace) {
        return true;
      }

      // Else filter by namespaceRoot.
      var namespaceRoot = component.getServiceName();
      return scopes.some(function (scope) {
        return namespaceRoot === scope;
      });
    }
  }, {
    key: "getHandlersByPriority",
    value: function getHandlersByPriority(event) {
      var _this4 = this;
      var handlers = this.handlers[this.getEventShortcut(event)];
      if (!handlers) {
        return false;
      }

      // TODO: Prioritize current scope before roo scope.
      var inCurrentScope = handlers.filter(function (handler) {
        return handler.scopes && _this4.isActiveScope(handler.scopes);
      });
      if (inCurrentScope.length) {
        return inCurrentScope;
      }
      var noScope = handlers.filter(function (handler) {
        return !handler.scopes;
      });
      if (noScope.length) {
        return noScope;
      }
    }
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/core/store.js":
/*!**************************************************!*\
  !*** ../modules/web-cli/assets/js/core/store.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _toolkit = __webpack_require__(/*! @reduxjs/toolkit */ "../node_modules/@reduxjs/toolkit/dist/redux-toolkit.esm.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
/**
 * @typedef {import('@reduxjs/toolkit').Slice} Slice
 */
/**
 * @typedef {import('@reduxjs/toolkit').EnhancedStore} EnhancedStore
 */
/**
 * @typedef {import('@reduxjs/toolkit').AnyAction} AnyAction
 */
/**
 * @typedef {import('@reduxjs/toolkit').ThunkMiddlewareFor} ThunkMiddlewareFor
 */
var Store = exports["default"] = /*#__PURE__*/function () {
  /**
   * Initialize the Store.
   *
   * @return {void}
   */
  function Store() {
    (0, _classCallCheck2.default)(this, Store);
    /**
     * @type {Object}
     */
    (0, _defineProperty2.default)(this, "slices", {});
    /**
     * @type {EnhancedStore<{}, AnyAction, [ThunkMiddlewareFor<{}>]>}
     */
    (0, _defineProperty2.default)(this, "reduxStore", void 0);
    this.slices = {};
    this.reduxStore = this.createStore();
  }

  /**
   * Create a Redux Store object.
   *
   * @return {EnhancedStore<{}, AnyAction, [ThunkMiddlewareFor<{}>]>} store
   */
  return (0, _createClass2.default)(Store, [{
    key: "createStore",
    value: function createStore() {
      return (0, _toolkit.configureStore)({
        // Use an empty function instead of empty object since an empty object
        // isn't a valid reducer value.
        reducer: function reducer() {}
      });
    }

    /**
     * Inject a new reducer.
     *
     * See: https://redux.js.org/usage/code-splitting#defining-an-injectreducer-function
     *
     * @param {string}   id         - Reducer unique ID.
     * @param {Function} newReducer - New reducer to inject.
     *
     * @return {void}
     */
  }, {
    key: "injectReducer",
    value: function injectReducer(id, newReducer) {
      var prevReducers = this.getReducers();
      this.reduxStore.replaceReducer((0, _toolkit.combineReducers)(_objectSpread(_objectSpread({}, prevReducers), {}, (0, _defineProperty2.default)({}, id, newReducer))));
    }

    /**
     * Register a Redux Store slice.
     *
     * @param {string} sliceId  - Slice unique ID.
     * @param {Slice}  instance - Slice object to add.
     *
     * @return {void}
     */
  }, {
    key: "register",
    value: function register(sliceId, instance) {
      if (this.slices[sliceId]) {
        throw "Slice with ID '".concat(sliceId, "' already exists.");
      }
      this.slices[sliceId] = instance;
      this.injectReducer(sliceId, instance.reducer);
    }

    /**
     * Get a specific slice.
     *
     * @param {string|null} sliceId - Slice ID to get.
     *
     * @return {Slice} slice
     */
  }, {
    key: "get",
    value: function get(sliceId) {
      return this.slices[sliceId];
    }

    /**
     * Get all slices.
     *
     * @return {Object} slices
     */
  }, {
    key: "getAllSlices",
    value: function getAllSlices() {
      return this.slices;
    }

    /**
     * Get All slices.
     *
     * @return {Object} slices
     */
  }, {
    key: "getAll",
    value: function getAll() {
      return Object.keys(this.slices).sort();
    }

    /**
     * Return the current reducers.
     *
     * @return {Object} reducers
     */
  }, {
    key: "getReducers",
    value: function getReducers() {
      return Object.entries(this.slices).reduce(function (reducers, _ref) {
        var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
          key = _ref2[0],
          slice = _ref2[1];
        return _objectSpread(_objectSpread({}, reducers), {}, (0, _defineProperty2.default)({}, key, slice.reducer));
      }, {});
    }

    /**
     * Get the actual Redux store object.
     *
     * @return {Object} redux store object
     */
  }, {
    key: "getReduxStore",
    value: function getReduxStore() {
      return this.reduxStore;
    }

    /**
     * Proxy to Redux's `dispatch()` function.
     *
     * @return {*} the dispatched action
     */
  }, {
    key: "dispatch",
    value: function dispatch() {
      var _this$reduxStore;
      return (_this$reduxStore = this.reduxStore).dispatch.apply(_this$reduxStore, arguments);
    }

    /**
     * Proxy to Redux's `getState()` function, with the ability to get a specific slice.
     *
     * @param {string|null} sliceId
     *
     * @return {*} The current state tree of the application
     */
  }, {
    key: "getState",
    value: function getState() {
      var sliceId = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var state = this.reduxStore.getState();
      return sliceId ? state[sliceId] : state;
    }

    /**
     * Proxy to Redux's `replaceReducer()` function.
     * TODO: Do we want that?
     *
     * @return {*} undefined
     */
  }, {
    key: "replaceReducer",
    value: function replaceReducer() {
      var _this$reduxStore2;
      return (_this$reduxStore2 = this.reduxStore).replaceReducer.apply(_this$reduxStore2, arguments);
    }

    /**
     * Proxy to Redux's `subscribe()` function.
     *
     * @return {*} A function that unsubscribes the change listener
     */
  }, {
    key: "subscribe",
    value: function subscribe() {
      var _this$reduxStore3;
      return (_this$reduxStore3 = this.reduxStore).subscribe.apply(_this$reduxStore3, arguments);
    }
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/core/ui-states.js":
/*!******************************************************!*\
  !*** ../modules/web-cli/assets/js/core/ui-states.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
/**
 * @typedef {import('./states/ui-state-base')} UiStateBase
 */
var UiStates = exports["default"] = /*#__PURE__*/function () {
  /**
   * Initialize the State Manager.
   *
   * @return {void}
   */
  function UiStates() {
    (0, _classCallCheck2.default)(this, UiStates);
    this.states = {};
  }

  /**
   * Register a new state.
   *
   * @param {UiStateBase} instance - State instance.
   */
  return (0, _createClass2.default)(UiStates, [{
    key: "register",
    value: function register(instance) {
      var stateId = instance.getPrefixedId();
      if (this.states[stateId]) {
        throw "State '".concat(stateId, "' already exists.");
      }
      this.states[stateId] = instance;
    }

    /**
     * Get all existing states with their options:
     *
     * {
     *     'state-id': [
     *         'option-1',
     *         'option-2',
     *         'option-3',
     *     ],
     * }
     *
     * @return {Object} all existing states
     */
  }, {
    key: "getAll",
    value: function getAll() {
      var states = {};
      Object.entries(this.states).forEach(function (_ref) {
        var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
          id = _ref2[0],
          instance = _ref2[1];
        var options = instance.getOptions();
        states[id] = Object.keys(options);
      });
      return states;
    }

    /**
     * Get the state value, or return all of them if no `state` is set.
     *
     * @param {string} state - State ID.
     *
     * @return {UiStateBase} state value
     */
  }, {
    key: "get",
    value: function get(state) {
      if (state) {
        return this.states[state];
      }
      return this.states;
    }

    /**
     * Set the current state value and trigger its callbacks & events.
     * This function triggers a `e-ui-state:${ stateID }` event to the scope, with `oldValue` & `newValue`
     * under `e.detail`.
     * Additionally, it adds a `e-ui-state--${ stateID }__${ value }` class to the scope element.
     *
     * @param {string} state - State ID.
     * @param {string} value - New state value.
     *
     * @return {void}
     */
  }, {
    key: "set",
    value: function set(state, value) {
      // Invalid state or option.
      if (!this.get(state)) {
        throw "State '".concat(state, "' doesn't exist.");
      }
      var oldValue = this.getCurrent(state),
        classPrefix = "e-ui-state--".concat(state.replaceAll('/', '-')),
        oldStateClass = "".concat(classPrefix, "__").concat(oldValue),
        newStateClass = "".concat(classPrefix, "__").concat(value),
        scopes = this.get(state).getScopes();

      // Set the current state to the new value.
      this.get(state).set(value);
      scopes.forEach(function (scope) {
        scope.classList.remove(oldStateClass);

        // Set the new class only if there is a value (i.e. it's not a state removal action).
        if (value) {
          scope.classList.add(newStateClass);
        }

        // Dispatch a custom state-change event to the scope.
        var event = new CustomEvent("e-ui-state:".concat(state), {
          detail: {
            oldValue: oldValue,
            newValue: value
          }
        });
        scope.dispatchEvent(event);
      });
    }

    /**
     * Remove a state.
     *
     * @param {string} state - State ID.
     *
     * @return {void}
     */
  }, {
    key: "remove",
    value: function remove(state) {
      this.set(state, '');
    }

    /**
     * Get the current state value.
     *
     * @param {string} state - State ID.
     *
     * @return {string} current state value
     */
  }, {
    key: "getCurrent",
    value: function getCurrent(state) {
      var _this$get;
      return (_this$get = this.get(state)) === null || _this$get === void 0 ? void 0 : _this$get.getCurrent();
    }
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/extras/hash-commands.js":
/*!************************************************************!*\
  !*** ../modules/web-cli/assets/js/extras/hash-commands.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _console = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/console */ "../modules/web-cli/assets/js/utils/console.js"));
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t.return || t.return(); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
var HashCommands = exports["default"] = /*#__PURE__*/function () {
  function HashCommands() {
    (0, _classCallCheck2.default)(this, HashCommands);
    /**
     * Cannot be static since it uses callback(s) that are available only after '$e' is initialized.
     */
    (0, _defineProperty2.default)(this, "dispatchersList", {
      'e:run': {
        runner: function runner() {
          return $e.run;
        },
        isSafe: function isSafe(command) {
          var _$e$commands$getComma;
          return (_$e$commands$getComma = $e.commands.getCommandClass(command)) === null || _$e$commands$getComma === void 0 ? void 0 : _$e$commands$getComma.getInfo().isSafe;
        },
        isSafeWithArgs: function isSafeWithArgs(command) {
          var _$e$commands$getComma2;
          return (_$e$commands$getComma2 = $e.commands.getCommandClass(command)) === null || _$e$commands$getComma2 === void 0 ? void 0 : _$e$commands$getComma2.getInfo().isSafeWithArgs;
        }
      },
      'e:route': {
        runner: function runner() {
          return $e.route;
        },
        isSafe: function isSafe() {
          return true;
        },
        isSafeWithArgs: function isSafeWithArgs() {
          return false;
        }
      }
    });
    /**
     * @typedef HashCommand
     * @property {string} method  method
     * @property {string} command command
     * @property {Object} args    arguments
     */
    /**
     * List of current loaded hash commands.
     *
     * @type {Array.<HashCommand>}
     */
    (0, _defineProperty2.default)(this, "commands", []);
    this.commands = this.get();
  }

  /**
   * Function get().
   *
   * Get API requests that comes from hash ( eg #e:run ).
   *
   * @param {string} hash
   *
   * @return {Array.<HashCommand>} API requests
   */
  return (0, _createClass2.default)(HashCommands, [{
    key: "get",
    value: function get() {
      var _this = this;
      var hash = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : location.hash;
      var result = [];
      if (hash) {
        // Remove first '#' and split each '&'.
        var hashList = hash.substr(1).split('&');
        hashList.forEach(function (hashItem) {
          // eslint-disable-next-line @wordpress/no-unused-vars-before-return
          var _hashItem$split = hashItem.split('?'),
            _hashItem$split2 = (0, _slicedToArray2.default)(_hashItem$split, 2),
            rawCommand = _hashItem$split2[0],
            rawArgs = _hashItem$split2[1];
          var hashParts = rawCommand.split(':');
          if (3 !== hashParts.length) {
            return;
          }
          var method = hashParts[0] + ':' + hashParts[1],
            dispatcher = _this.dispatchersList[method];
          if (dispatcher) {
            var command = hashParts[2],
              args = _this.parseCommandArgs(rawArgs);
            result.push({
              method: method,
              command: command,
              args: args
            });
          }
        });
      }
      return result;
    }

    /**
     * Function run().
     *
     * Run API requests that comes from hash ( eg #e:run ).
     *
     * @param {Array.<HashCommand>} [commands=this.commands]
     */
  }, {
    key: "run",
    value: (function () {
      var _run = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
        var commands,
          _iterator,
          _step,
          hashCommand,
          dispatcher,
          _iterator2,
          _step2,
          _hashCommand,
          _dispatcher,
          _args = arguments,
          _t,
          _t2;
        return _regenerator.default.wrap(function (_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              commands = _args.length > 0 && _args[0] !== undefined ? _args[0] : this.commands;
              // To allow validate the run first.
              _iterator = _createForOfIteratorHelper(commands);
              _context.prev = 1;
              _iterator.s();
            case 2:
              if ((_step = _iterator.n()).done) {
                _context.next = 5;
                break;
              }
              hashCommand = _step.value;
              dispatcher = this.dispatchersList[hashCommand.method];
              if (dispatcher) {
                _context.next = 3;
                break;
              }
              return _context.abrupt("return", Promise.reject(new Error("No dispatcher found for the command: `".concat(hashCommand.command, "`."))));
            case 3:
              if (dispatcher.isSafe(hashCommand.command)) {
                _context.next = 4;
                break;
              }
              return _context.abrupt("return", Promise.reject(new Error("Attempting to run unsafe or non exist command: `".concat(hashCommand.command, "`."))));
            case 4:
              _context.next = 2;
              break;
            case 5:
              _context.next = 7;
              break;
            case 6:
              _context.prev = 6;
              _t = _context["catch"](1);
              _iterator.e(_t);
            case 7:
              _context.prev = 7;
              _iterator.f();
              return _context.finish(7);
            case 8:
              // This logic will run the promises by sequence (will wait for dispatcher to finish, before run again).
              _iterator2 = _createForOfIteratorHelper(commands);
              _context.prev = 9;
              _iterator2.s();
            case 10:
              if ((_step2 = _iterator2.n()).done) {
                _context.next = 12;
                break;
              }
              _hashCommand = _step2.value;
              _dispatcher = this.dispatchersList[_hashCommand.method];
              _context.next = 11;
              return _dispatcher.runner()(_hashCommand.command, _dispatcher.isSafeWithArgs(_hashCommand.command) ? _hashCommand.args : undefined);
            case 11:
              _context.next = 10;
              break;
            case 12:
              _context.next = 14;
              break;
            case 13:
              _context.prev = 13;
              _t2 = _context["catch"](9);
              _iterator2.e(_t2);
            case 14:
              _context.prev = 14;
              _iterator2.f();
              return _context.finish(14);
            case 15:
            case "end":
              return _context.stop();
          }
        }, _callee, this, [[1, 6, 7, 8], [9, 13, 14, 15]]);
      }));
      function run() {
        return _run.apply(this, arguments);
      }
      return run;
    }()
    /**
     * Function runOnce().
     *
     * Do same as `run` but clear `this.commands` before leaving.
     */
    )
  }, {
    key: "runOnce",
    value: function runOnce() {
      var _this2 = this;
      this.run(this.commands).then(function () {
        _this2.commands = [];
      });
    }

    /**
     * Takes a args in form of JSON and parse it.
     *
     * @param {string} rawArgs
     * @return {Object} args in as an object
     */
  }, {
    key: "parseCommandArgs",
    value: function parseCommandArgs(rawArgs) {
      try {
        return JSON.parse(decodeURI(rawArgs || '{}'));
      } catch (e) {
        _console.default.warn('Hash commands JSON args cannot be parsed. \n\n', e);
        return {};
      }
    }
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/command-base.js":
/*!************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/command-base.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commandInfra = _interopRequireDefault(__webpack_require__(/*! ./command-infra */ "../modules/web-cli/assets/js/modules/command-infra.js"));
var _deprecation = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/deprecation */ "../modules/web-cli/assets/js/utils/deprecation.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @name $e.modules.CommandBase
 */
var CommandBase = exports["default"] = /*#__PURE__*/function (_CommandInfra) {
  function CommandBase() {
    (0, _classCallCheck2.default)(this, CommandBase);
    return _callSuper(this, CommandBase, arguments);
  }
  (0, _inherits2.default)(CommandBase, _CommandInfra);
  return (0, _createClass2.default)(CommandBase, [{
    key: "onBeforeRun",
    value: function onBeforeRun() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      $e.hooks.runUIBefore(this.command, args);
    }
  }, {
    key: "onAfterRun",
    value: function onAfterRun() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var result = arguments.length > 1 ? arguments[1] : undefined;
      $e.hooks.runUIAfter(this.command, args, result);
    }
  }, {
    key: "onBeforeApply",
    value: function onBeforeApply() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      $e.hooks.runDataDependency(this.command, args);
    }
  }, {
    key: "onAfterApply",
    value: function onAfterApply() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var result = arguments.length > 1 ? arguments[1] : undefined;
      return $e.hooks.runDataAfter(this.command, args, result);
    }
  }, {
    key: "onCatchApply",
    value: function onCatchApply(e) {
      this.runCatchHooks(e);
    }

    /**
     * Run all the catch hooks.
     *
     * @param {Error} e
     */
  }, {
    key: "runCatchHooks",
    value: function runCatchHooks(e) {
      $e.hooks.runDataCatch(this.command, this.args, e);
      $e.hooks.runUICatch(this.command, this.args, e);
    }

    /**
     * TODO - Remove - Backwards compatibility.
     *
     * Function requireContainer().
     *
     * Validate `arg.container` & `arg.containers`.
     *
     * @param {{}} args
     * @deprecated since 3.7.0, extend `$e.modules.editor.CommandContainerBase` or `$e.modules.editor.CommandContainerInternalBase` instead.
     *
     * @throws {Error}
     */
  }, {
    key: "requireContainer",
    value: function requireContainer() {
      var _this = this;
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.args;
      _deprecation.default.deprecated('requireContainer()', '3.7.0', 'Extend `$e.modules.editor.CommandContainerBase` or `$e.modules.editor.CommandContainerInternalBase`');
      if (!args.container && !args.containers) {
        throw Error('container or containers are required.');
      }
      if (args.container && args.containers) {
        throw Error('container and containers cannot go together please select one of them.');
      }
      var containers = args.containers || [args.container];
      containers.forEach(function (container) {
        _this.requireArgumentInstance('container', elementorModules.editor.Container, {
          container: container
        });
      });
    }
  }], [{
    key: "getInstanceType",
    value: function getInstanceType() {
      return 'CommandBase';
    }
  }]);
}(_commandInfra.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/command-callback-base.js":
/*!*********************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/command-callback-base.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commandBase = _interopRequireDefault(__webpack_require__(/*! ./command-base */ "../modules/web-cli/assets/js/modules/command-base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * To support pure callbacks in the API(commands.js), to ensure they have registered with the proper context.
 */
var CommandCallbackBase = exports["default"] = /*#__PURE__*/function (_CommandBase) {
  function CommandCallbackBase() {
    (0, _classCallCheck2.default)(this, CommandCallbackBase);
    return _callSuper(this, CommandCallbackBase, arguments);
  }
  (0, _inherits2.default)(CommandCallbackBase, _CommandBase);
  return (0, _createClass2.default)(CommandCallbackBase, [{
    key: "apply",
    value: function apply() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      return this.constructor.getCallback()(args);
    }
  }], [{
    key: "getInstanceType",
    value: function getInstanceType() {
      return 'CommandCallbackBase';
    }

    /**
     * Get original callback of the command.
     *
     * Support pure callbacks ( Non command-base ).
     *
     * @return {()=>{}} Command Results.
     */
  }, {
    key: "getCallback",
    value: function getCallback() {
      return this.registerConfig.callback;
    }
  }]);
}(_commandBase.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/command-data.js":
/*!************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/command-data.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _commandBase = _interopRequireDefault(__webpack_require__(/*! ./command-base */ "../modules/web-cli/assets/js/modules/command-base.js"));
var errors = _interopRequireWildcard(__webpack_require__(/*! ../core/data/errors/ */ "../modules/web-cli/assets/js/core/data/errors/index.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @name $e.modules.CommandData
 */
/**
 * @typedef {('create'|'delete'|'get'|'update'|'options')} DataTypes
 */
/**
 * @typedef {{}} RequestData
 */
/**
 * @typedef {import('../core/data/errors/base-error')} BaseError
 */
var CommandData = exports["default"] = /*#__PURE__*/function (_CommandBase) {
  function CommandData(args) {
    var _this$args$options;
    var _this;
    var commandsAPI = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : $e.data;
    (0, _classCallCheck2.default)(this, CommandData);
    _this = _callSuper(this, CommandData, [args, commandsAPI]);
    /**
     * Data returned from remote.
     *
     * @type {*}
     */
    (0, _defineProperty2.default)(_this, "data", void 0);
    /**
     * Fetch type.
     *
     * @type {DataTypes}
     */
    (0, _defineProperty2.default)(_this, "type", void 0);
    if ((_this$args$options = _this.args.options) !== null && _this$args$options !== void 0 && _this$args$options.type) {
      _this.type = _this.args.options.type;
    }
    return _this;
  }

  /**
   * Function getEndpointFormat().
   *
   * @return {null|string} endpoint format
   */
  (0, _inherits2.default)(CommandData, _CommandBase);
  return (0, _createClass2.default)(CommandData, [{
    key: "getApplyMethods",
    value:
    /**
     * @param {DataTypes} type
     *
     * @return {boolean|{before: (function(*=): {}), after: (function({}, *=): {})}} apply methods
     */
    function getApplyMethods() {
      var type = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.type;
      var before, after;
      switch (type) {
        case 'create':
          before = this.applyBeforeCreate;
          after = this.applyAfterCreate;
          break;
        case 'delete':
          before = this.applyBeforeDelete;
          after = this.applyAfterDelete;
          break;
        case 'get':
          before = this.applyBeforeGet;
          after = this.applyAfterGet;
          break;
        case 'update':
          before = this.applyBeforeUpdate;
          after = this.applyAfterUpdate;
          break;
        case 'options':
          before = this.applyBeforeOptions;
          after = this.applyAfterOptions;
          break;
        default:
          return false;
      }
      return {
        before: before.bind(this),
        after: after.bind(this)
      };
    }

    /**
     * Function getRequestData().
     *
     * @return {RequestData} request data
     */
  }, {
    key: "getRequestData",
    value: function getRequestData() {
      return {
        type: this.type,
        args: this.args,
        timestamp: new Date().getTime(),
        component: this.component,
        command: this.command,
        endpoint: $e.data.commandToEndpoint(this.command, JSON.parse(JSON.stringify(this.args)), this.constructor.getEndpointFormat())
      };
    }
  }, {
    key: "apply",
    value: function apply() {
      var _this2 = this;
      var applyMethods = this.getApplyMethods();

      // Run 'before' method.
      this.args = applyMethods.before(this.args);
      var requestData = this.getRequestData();
      return $e.data.fetch(requestData).then(function (data) {
        _this2.data = data;

        // Run 'after' method.
        _this2.data = applyMethods.after(data, _this2.args);
        _this2.data = {
          data: _this2.data
        };

        // Append requestData.
        _this2.data = Object.assign({
          __requestData__: requestData
        }, _this2.data);
        return _this2.data;
      });
    }

    /**
     * @param {*} [args={}]
     * @return {{}} filtered args
     */
  }, {
    key: "applyBeforeCreate",
    value: function applyBeforeCreate() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      return args;
    }

    /**
     * @param {{}} data
     * @param {*}  [args={}]
     * @return {{}} filtered result
     */
  }, {
    key: "applyAfterCreate",
    value: function applyAfterCreate(data) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      // eslint-disable-line no-unused-vars
      return data;
    }

    /**
     * @param {*} [args={}]
     * @return {{}} filtered args
     */
  }, {
    key: "applyBeforeDelete",
    value: function applyBeforeDelete() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      return args;
    }

    /**
     * @param {{}} data
     * @param {*}  [args={}]
     * @return {{}} filtered result
     */
  }, {
    key: "applyAfterDelete",
    value: function applyAfterDelete(data) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      // eslint-disable-line no-unused-vars
      return data;
    }

    /**
     * @param {*} [args={}]
     * @return {{}} filtered args
     */
  }, {
    key: "applyBeforeGet",
    value: function applyBeforeGet() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      return args;
    }

    /**
     * @param {{}} data
     * @param {*}  [args={}]
     * @return {{}} filtered result
     */
  }, {
    key: "applyAfterGet",
    value: function applyAfterGet(data) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      // eslint-disable-line no-unused-vars
      return data;
    }

    /**
     * @param {*} [args={}]
     * @return {{}} filtered args
     */
  }, {
    key: "applyBeforeUpdate",
    value: function applyBeforeUpdate() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      return args;
    }

    /**
     * @param {{}} data
     * @param {*}  [args={}]
     * @return {{}} filtered result
     */
  }, {
    key: "applyAfterUpdate",
    value: function applyAfterUpdate(data) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      // eslint-disable-line no-unused-vars
      return data;
    }

    /**
     * @param {*} [args={}]
     * @return {{}} filtered args
     */
  }, {
    key: "applyBeforeOptions",
    value: function applyBeforeOptions() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      return args;
    }

    /**
     * @param {{}} data
     * @param {*}  [args={}]
     * @return {{}} filtered result
     */
  }, {
    key: "applyAfterOptions",
    value: function applyAfterOptions(data) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      // eslint-disable-line no-unused-vars
      return data;
    }

    /**
     * @param {BaseError} e
     */
  }, {
    key: "applyAfterCatch",
    value: function applyAfterCatch(e) {
      e.notify();
    }
  }, {
    key: "onCatchApply",
    value: function onCatchApply(e) {
      var _e;
      // TODO: If the errors that returns from the server is consistent remove the '?' from 'e'
      var httpErrorCode = ((_e = e) === null || _e === void 0 || (_e = _e.data) === null || _e === void 0 ? void 0 : _e.status) || 501;
      var dataError = Object.values(errors).find(function (error) {
        return error.getHTTPErrorCode() === httpErrorCode;
      });
      if (!dataError) {
        dataError = errors.DefaultError;
      }
      e = dataError.create(e.message, e.code, e.data || []);
      this.runCatchHooks(e);
      this.applyAfterCatch(e);
    }
  }], [{
    key: "getInstanceType",
    value: function getInstanceType() {
      return 'CommandData';
    }
  }, {
    key: "getEndpointFormat",
    value: function getEndpointFormat() {
      return null;
    }
  }]);
}(_commandBase.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/command-infra.js":
/*!*************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/command-infra.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _argsObject = _interopRequireDefault(__webpack_require__(/*! elementor-assets-js/modules/imports/args-object */ "../assets/dev/js/modules/imports/args-object.js"));
var _deprecation = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/deprecation */ "../modules/web-cli/assets/js/utils/deprecation.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @typedef {import('../modules/component-base')} ComponentBase
 */
var CommandInfra = exports["default"] = /*#__PURE__*/function (_ArgsObject) {
  /**
   * Function constructor().
   *
   * Create Commands Base.
   *
   * @param {{}} args
   */
  function CommandInfra() {
    var _this;
    var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    (0, _classCallCheck2.default)(this, CommandInfra);
    _this = _callSuper(this, CommandInfra, [args]);
    if (!_this.constructor.registerConfig) {
      throw RangeError('Doing it wrong: Each command type should have `registerConfig`.');
    }

    // Acknowledge self about which command it run.
    _this.command = _this.constructor.getCommand();

    // Assign instance of current component.
    _this.component = _this.constructor.getComponent();

    // Who ever need do something before without `super` the constructor can use `initialize` method.
    _this.initialize(args);

    // Refresh args, maybe the changed via `initialize`.
    args = _this.args;

    // Validate args before run.
    _this.validateArgs(args);
    return _this;
  }

  /**
   * Function initialize().
   *
   * Initialize command, called after construction.
   *
   * @param {{}} args
   */
  (0, _inherits2.default)(CommandInfra, _ArgsObject);
  return (0, _createClass2.default)(CommandInfra, [{
    key: "currentCommand",
    get:
    /**
     * @deprecated since 3.7.0, use `this.command` instead.
     */
    function get() {
      _deprecation.default.deprecated('this.currentCommand', '3.7.0', 'this.command');
      return this.command;
    }
  }, {
    key: "initialize",
    value: function initialize() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    } // eslint-disable-line no-unused-vars

    /**
     * Function validateArgs().
     *
     * Validate command arguments.
     *
     * @param {{}} args
     */
  }, {
    key: "validateArgs",
    value: function validateArgs() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    } // eslint-disable-line no-unused-vars

    // eslint-disable-next-line jsdoc/require-returns-check
    /**
     * Function apply().
     *
     * Do the actual command.
     *
     * @param {{}} args
     *
     * @return {*} Command results.
     */
  }, {
    key: "apply",
    value: function apply() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      // eslint-disable-line no-unused-vars
      elementorModules.ForceMethodImplementation();
    }

    /**
     * Function run().
     *
     * Run command with history & hooks.
     *
     * @return {*} Command results.
     */
  }, {
    key: "run",
    value: function run() {
      return this.apply(this.args);
    }

    /**
     * Function onBeforeRun.
     *
     * Called before run().
     *
     * @param {{}} args
     */
  }, {
    key: "onBeforeRun",
    value: function onBeforeRun() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    } // eslint-disable-line no-unused-vars

    /**
     * Function onAfterRun.
     *
     * Called after run().
     *
     * @param {{}} args
     * @param {*}  result
     */
  }, {
    key: "onAfterRun",
    value: function onAfterRun() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var result = arguments.length > 1 ? arguments[1] : undefined;
    } // eslint-disable-line no-unused-vars

    /**
     * Function onBeforeApply.
     *
     * Called before apply().
     *
     * @param {{}} args
     */
  }, {
    key: "onBeforeApply",
    value: function onBeforeApply() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    } // eslint-disable-line no-unused-vars

    /**
     * Function onAfterApply.
     *
     * Called after apply().
     *
     * @param {{}} args
     * @param {*}  result
     */
  }, {
    key: "onAfterApply",
    value: function onAfterApply() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var result = arguments.length > 1 ? arguments[1] : undefined;
    } // eslint-disable-line no-unused-vars

    /**
     * Function onCatchApply.
     *
     * Called after apply() failed.
     *
     * @param {Error} e
     */
  }, {
    key: "onCatchApply",
    value: function onCatchApply(e) {} // eslint-disable-line no-unused-vars
  }], [{
    key: "getInstanceType",
    value: function getInstanceType() {
      return 'CommandInfra';
    }

    /**
     * Get info of command.
     *
     * @return {Object} Extra information about the command.
     */
  }, {
    key: "getInfo",
    value: function getInfo() {
      return {};
    }

    /**
     * @return {string} Self command name.
     */
  }, {
    key: "getCommand",
    value: function getCommand() {
      return this.registerConfig.command;
    }

    /**
     * @return {ComponentBase} Self component
     */
  }, {
    key: "getComponent",
    value: function getComponent() {
      return this.registerConfig.component;
    }
  }, {
    key: "setRegisterConfig",
    value: function setRegisterConfig(config) {
      this.registerConfig = Object.freeze(config);
    }
  }]);
}(_argsObject.default);
/**
 * @type {Object}
 */
(0, _defineProperty2.default)(CommandInfra, "registerConfig", null);

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/command-internal-base.js":
/*!*********************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/command-internal-base.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commandBase = _interopRequireDefault(__webpack_require__(/*! ./command-base */ "../modules/web-cli/assets/js/modules/command-base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @name $e.modules.CommandInternalBase
 */
var CommandInternalBase = exports["default"] = /*#__PURE__*/function (_CommandBase) {
  function CommandInternalBase(args) {
    var commandsAPI = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : $e.commandsInternal;
    (0, _classCallCheck2.default)(this, CommandInternalBase);
    return _callSuper(this, CommandInternalBase, [args, commandsAPI]);
  }
  (0, _inherits2.default)(CommandInternalBase, _CommandBase);
  return (0, _createClass2.default)(CommandInternalBase, null, [{
    key: "getInstanceType",
    value: function getInstanceType() {
      return 'CommandInternalBase';
    }
  }]);
}(_commandBase.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/commands/close.js":
/*!**************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/commands/close.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Close = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commandBase = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/command-base */ "../modules/web-cli/assets/js/modules/command-base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Close = exports.Close = /*#__PURE__*/function (_CommandBase) {
  function Close() {
    (0, _classCallCheck2.default)(this, Close);
    return _callSuper(this, Close, arguments);
  }
  (0, _inherits2.default)(Close, _CommandBase);
  return (0, _createClass2.default)(Close, [{
    key: "apply",
    value: function apply() {
      this.component.close();
    }
  }]);
}(_commandBase.default);
var _default = exports["default"] = Close;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/commands/index.js":
/*!**************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/commands/index.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "Close", ({
  enumerable: true,
  get: function get() {
    return _close.Close;
  }
}));
Object.defineProperty(exports, "Open", ({
  enumerable: true,
  get: function get() {
    return _open.Open;
  }
}));
Object.defineProperty(exports, "Toggle", ({
  enumerable: true,
  get: function get() {
    return _toggle.Toggle;
  }
}));
var _close = __webpack_require__(/*! ./close */ "../modules/web-cli/assets/js/modules/commands/close.js");
var _open = __webpack_require__(/*! ./open */ "../modules/web-cli/assets/js/modules/commands/open.js");
var _toggle = __webpack_require__(/*! ./toggle */ "../modules/web-cli/assets/js/modules/commands/toggle.js");

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/commands/open.js":
/*!*************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/commands/open.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Open = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commandBase = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/command-base */ "../modules/web-cli/assets/js/modules/command-base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Open = exports.Open = /*#__PURE__*/function (_CommandBase) {
  function Open() {
    (0, _classCallCheck2.default)(this, Open);
    return _callSuper(this, Open, arguments);
  }
  (0, _inherits2.default)(Open, _CommandBase);
  return (0, _createClass2.default)(Open, [{
    key: "apply",
    value: function apply() {
      $e.route(this.component.getNamespace());
    }
  }]);
}(_commandBase.default);
var _default = exports["default"] = Open;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/commands/toggle.js":
/*!***************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/commands/toggle.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Toggle = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commandBase = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/command-base */ "../modules/web-cli/assets/js/modules/command-base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Toggle = exports.Toggle = /*#__PURE__*/function (_CommandBase) {
  function Toggle() {
    (0, _classCallCheck2.default)(this, Toggle);
    return _callSuper(this, Toggle, arguments);
  }
  (0, _inherits2.default)(Toggle, _CommandBase);
  return (0, _createClass2.default)(Toggle, [{
    key: "apply",
    value: function apply() {
      if (this.component.isOpen) {
        this.component.close();
      } else {
        $e.route(this.component.getNamespace());
      }
    }
  }]);
}(_commandBase.default);
var _default = exports["default"] = Toggle;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/component-base.js":
/*!**************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/component-base.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _commandCallbackBase = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/command-callback-base */ "../modules/web-cli/assets/js/modules/command-callback-base.js"));
var _toolkit = __webpack_require__(/*! @reduxjs/toolkit */ "../node_modules/@reduxjs/toolkit/dist/redux-toolkit.esm.js");
var _module = _interopRequireDefault(__webpack_require__(/*! elementor/assets/dev/js/modules/imports/module.js */ "../assets/dev/js/modules/imports/module.js"));
var _forceMethodImplementation = _interopRequireDefault(__webpack_require__(/*! ../utils/force-method-implementation */ "../modules/web-cli/assets/js/utils/force-method-implementation.js"));
var _deprecation = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/deprecation */ "../modules/web-cli/assets/js/utils/deprecation.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @typedef {import('./command-infra')} CommandInfra
 * @typedef {import('./hook-base')} HookBase
 * @typedef {import('../core/states/ui-state-base')} UiStateBase
 */
var ComponentBase = exports["default"] = /*#__PURE__*/function (_Module) {
  function ComponentBase() {
    (0, _classCallCheck2.default)(this, ComponentBase);
    return _callSuper(this, ComponentBase, arguments);
  }
  (0, _inherits2.default)(ComponentBase, _Module);
  return (0, _createClass2.default)(ComponentBase, [{
    key: "__construct",
    value: function __construct() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      if (args.manager) {
        this.manager = args.manager;
      }
      this.commands = this.defaultCommands();
      this.commandsInternal = this.defaultCommandsInternal();
      this.hooks = this.defaultHooks();
      this.routes = this.defaultRoutes();
      this.tabs = this.defaultTabs();
      this.shortcuts = this.defaultShortcuts();
      this.utils = this.defaultUtils();
      this.data = this.defaultData();
      this.uiStates = this.defaultUiStates();
      this.states = this.defaultStates();
      this.defaultRoute = '';
      this.currentTab = '';
    }
  }, {
    key: "registerAPI",
    value: function registerAPI() {
      var _this = this;
      Object.entries(this.getTabs()).forEach(function (tab) {
        return _this.registerTabRoute(tab[0]);
      });
      Object.entries(this.getRoutes()).forEach(function (_ref) {
        var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
          route = _ref2[0],
          callback = _ref2[1];
        return _this.registerRoute(route, callback);
      });
      Object.entries(this.getCommands()).forEach(function (_ref3) {
        var _ref4 = (0, _slicedToArray2.default)(_ref3, 2),
          command = _ref4[0],
          callback = _ref4[1];
        return _this.registerCommand(command, callback);
      });
      Object.entries(this.getCommandsInternal()).forEach(function (_ref5) {
        var _ref6 = (0, _slicedToArray2.default)(_ref5, 2),
          command = _ref6[0],
          callback = _ref6[1];
        return _this.registerCommandInternal(command, callback);
      });
      Object.values(this.getHooks()).forEach(function (instance) {
        return _this.registerHook(instance);
      });
      Object.entries(this.getData()).forEach(function (_ref7) {
        var _ref8 = (0, _slicedToArray2.default)(_ref7, 2),
          command = _ref8[0],
          callback = _ref8[1];
        return _this.registerData(command, callback);
      });
      Object.values(this.getUiStates()).forEach(function (instance) {
        return _this.registerUiState(instance);
      });
      Object.entries(this.getStates()).forEach(function (_ref9) {
        var _ref0 = (0, _slicedToArray2.default)(_ref9, 2),
          id = _ref0[0],
          state = _ref0[1];
        return _this.registerState(id, state);
      });
    }

    // eslint-disable-next-line jsdoc/require-returns-check
    /**
     * @return {string} namespace
     */
  }, {
    key: "getNamespace",
    value: function getNamespace() {
      (0, _forceMethodImplementation.default)();
    }

    /**
     * @deprecated since 3.7.0, use `getServiceName()` instead.
     */
  }, {
    key: "getRootContainer",
    value: function getRootContainer() {
      _deprecation.default.deprecated('getRootContainer()', '3.7.0', 'getServiceName()');
      return this.getServiceName();
    }
  }, {
    key: "getServiceName",
    value: function getServiceName() {
      return this.getNamespace().split('/')[0];
    }
  }, {
    key: "store",
    get: function get() {
      return $e.store.get(this.getNamespace());
    }
  }, {
    key: "defaultTabs",
    value: function defaultTabs() {
      return {};
    }
  }, {
    key: "defaultRoutes",
    value: function defaultRoutes() {
      return {};
    }
  }, {
    key: "defaultCommands",
    value: function defaultCommands() {
      return {};
    }
  }, {
    key: "defaultCommandsInternal",
    value: function defaultCommandsInternal() {
      return {};
    }
  }, {
    key: "defaultHooks",
    value: function defaultHooks() {
      return {};
    }

    /**
     * Get the component's default UI states.
     *
     * @return {Object} default UI states
     */
  }, {
    key: "defaultUiStates",
    value: function defaultUiStates() {
      return {};
    }

    /**
     * Get the component's Redux slice settings.
     *
     * @return {Object} Redux slice settings
     */
  }, {
    key: "defaultStates",
    value: function defaultStates() {
      return {};
    }
  }, {
    key: "defaultShortcuts",
    value: function defaultShortcuts() {
      return {};
    }
  }, {
    key: "defaultUtils",
    value: function defaultUtils() {
      return {};
    }
  }, {
    key: "defaultData",
    value: function defaultData() {
      return {};
    }
  }, {
    key: "getCommands",
    value: function getCommands() {
      return this.commands;
    }
  }, {
    key: "getCommandsInternal",
    value: function getCommandsInternal() {
      return this.commandsInternal;
    }
  }, {
    key: "getHooks",
    value: function getHooks() {
      return this.hooks;
    }

    /**
     * Retrieve the component's UI states.
     *
     * @return {Object} UI states
     */
  }, {
    key: "getUiStates",
    value: function getUiStates() {
      return this.uiStates;
    }

    /**
     * Retrieve the component's Redux Slice.
     *
     * @return {Object} Redux Slice
     */
  }, {
    key: "getStates",
    value: function getStates() {
      return this.states;
    }
  }, {
    key: "getRoutes",
    value: function getRoutes() {
      return this.routes;
    }
  }, {
    key: "getTabs",
    value: function getTabs() {
      return this.tabs;
    }
  }, {
    key: "getShortcuts",
    value: function getShortcuts() {
      return this.shortcuts;
    }
  }, {
    key: "getData",
    value: function getData() {
      return this.data;
    }

    /**
     * @param {string}                      command
     * @param {(()=>{}|CommandInfra)}       context
     * @param {'default'|'internal'|'data'} commandsType
     */
  }, {
    key: "registerCommand",
    value: function registerCommand(command, context) {
      var commandsType = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'default';
      var commandsManager;
      switch (commandsType) {
        case 'default':
          commandsManager = $e.commands;
          break;
        case 'internal':
          commandsManager = $e.commandsInternal;
          break;
        case 'data':
          commandsManager = $e.data;
          break;
        default:
          throw new Error("Invalid commands type: '".concat(command, "'"));
      }
      var fullCommand = this.getNamespace() + '/' + command,
        instanceType = context.getInstanceType ? context.getInstanceType() : false,
        registerConfig = {
          command: fullCommand,
          component: this
        };

      // Support pure callback.
      if (!instanceType) {
        if ($e.devTools) {
          $e.devTools.log.warn("Attach command-callback-base, on command: '".concat(fullCommand, "', context is unknown type."));
        }
        registerConfig.callback = context;

        // Unique class.
        context = /*#__PURE__*/function (_CommandCallbackBase) {
          function context() {
            (0, _classCallCheck2.default)(this, context);
            return _callSuper(this, context, arguments);
          }
          (0, _inherits2.default)(context, _CommandCallbackBase);
          return (0, _createClass2.default)(context);
        }(_commandCallbackBase.default);
      }
      context.setRegisterConfig(registerConfig);
      commandsManager.register(this, command, context);
    }

    /**
     * @param {HookBase} instance
     */
  }, {
    key: "registerHook",
    value: function registerHook(instance) {
      return instance.register();
    }
  }, {
    key: "registerCommandInternal",
    value: function registerCommandInternal(command, context) {
      this.registerCommand(command, context, 'internal');
    }

    /**
     * Register a UI state.
     *
     * @param {UiStateBase} instance - UI state instance.
     *
     * @return {void}
     */
  }, {
    key: "registerUiState",
    value: function registerUiState(instance) {
      $e.uiStates.register(instance);
    }

    /**
     * Register a Redux Slice.
     *
     * @param {string} id          - State id.
     * @param {Object} stateConfig - The state config.
     *
     * @return {void}
     */
  }, {
    key: "registerState",
    value: function registerState(id, stateConfig) {
      id = this.getNamespace() + (id ? "/".concat(id) : '');
      var slice = (0, _toolkit.createSlice)(_objectSpread(_objectSpread({}, stateConfig), {}, {
        name: id
      }));
      $e.store.register(id, slice);
    }
  }, {
    key: "registerRoute",
    value: function registerRoute(route, callback) {
      $e.routes.register(this, route, callback);
    }
  }, {
    key: "registerData",
    value: function registerData(command, context) {
      this.registerCommand(command, context, 'data');
    }
  }, {
    key: "unregisterRoute",
    value: function unregisterRoute(route) {
      $e.routes.unregister(this, route);
    }
  }, {
    key: "registerTabRoute",
    value: function registerTabRoute(tab) {
      var _this2 = this;
      this.registerRoute(tab, function (args) {
        return _this2.activateTab(tab, args);
      });
    }
  }, {
    key: "dependency",
    value: function dependency() {
      return true;
    }
  }, {
    key: "open",
    value: function open() {
      return true;
    }
  }, {
    key: "close",
    value: function close() {
      if (!this.isOpen) {
        return false;
      }
      this.isOpen = false;
      this.inactivate();
      $e.routes.clearCurrent(this.getNamespace());
      $e.routes.clearHistory(this.getServiceName());
      return true;
    }
  }, {
    key: "activate",
    value: function activate() {
      $e.components.activate(this.getNamespace());
    }
  }, {
    key: "inactivate",
    value: function inactivate() {
      $e.components.inactivate(this.getNamespace());
    }
  }, {
    key: "isActive",
    value: function isActive() {
      return $e.components.isActive(this.getNamespace());
    }
  }, {
    key: "onRoute",
    value: function onRoute(route) {
      this.toggleRouteClass(route, true);
      this.toggleHistoryClass();
      this.activate();
      this.trigger('route/open', route);
    }
  }, {
    key: "onCloseRoute",
    value: function onCloseRoute(route) {
      this.toggleRouteClass(route, false);
      this.inactivate();
      this.trigger('route/close', route);
    }
  }, {
    key: "setDefaultRoute",
    value: function setDefaultRoute(route) {
      this.defaultRoute = this.getNamespace() + '/' + route;
    }
  }, {
    key: "getDefaultRoute",
    value: function getDefaultRoute() {
      return this.defaultRoute;
    }
  }, {
    key: "removeTab",
    value: function removeTab(tab) {
      delete this.tabs[tab];
      this.unregisterRoute(tab);
    }
  }, {
    key: "hasTab",
    value: function hasTab(tab) {
      return !!this.tabs[tab];
    }
  }, {
    key: "addTab",
    value: function addTab(tab, args, position) {
      var _this3 = this;
      this.tabs[tab] = args;
      // It can be 0.
      if ('undefined' !== typeof position) {
        var newTabs = {};
        var ids = Object.keys(this.tabs);
        // Remove new tab
        ids.pop();

        // Add it to position.
        ids.splice(position, 0, tab);
        ids.forEach(function (id) {
          newTabs[id] = _this3.tabs[id];
        });
        this.tabs = newTabs;
      }
      this.registerTabRoute(tab);
    }
  }, {
    key: "getTabsWrapperSelector",
    value: function getTabsWrapperSelector() {
      return '';
    }
  }, {
    key: "getTabRoute",
    value: function getTabRoute(tab) {
      return this.getNamespace() + '/' + tab;
    }
  }, {
    key: "renderTab",
    value: function renderTab(tab) {} // eslint-disable-line
  }, {
    key: "activateTab",
    value: function activateTab(tab, args) {
      var _this4 = this;
      this.renderTab(tab, args);
      jQuery(this.getTabsWrapperSelector() + ' .elementor-component-tab').off('click').on('click', function (event) {
        $e.route(_this4.getTabRoute(event.currentTarget.dataset.tab), args);
      }).removeClass('elementor-active').filter('[data-tab="' + tab + '"]').addClass('elementor-active');
    }
  }, {
    key: "getActiveTabConfig",
    value: function getActiveTabConfig() {
      return this.tabs[this.currentTab] || {};
    }
  }, {
    key: "getBodyClass",
    value: function getBodyClass(route) {
      return 'e-route-' + route.replace(/\//g, '-');
    }

    /**
     * If command includes uppercase character convert it to lowercase and add `-`.
     * e.g: `CopyAll` is converted to `copy-all`.
     *
     * @param {string} commandName
     */
  }, {
    key: "normalizeCommandName",
    value: function normalizeCommandName(commandName) {
      return commandName.replace(/[A-Z]/g, function (match, offset) {
        return (offset > 0 ? '-' : '') + match.toLowerCase();
      });
    }

    /**
     * @param {{}} commandsFromImport
     * @return {{}} imported commands
     */
  }, {
    key: "importCommands",
    value: function importCommands(commandsFromImport) {
      var _this5 = this;
      var commands = {};

      // Convert `Commands` to `ComponentBase` workable format.
      Object.entries(commandsFromImport).forEach(function (_ref1) {
        var _ref10 = (0, _slicedToArray2.default)(_ref1, 2),
          className = _ref10[0],
          Class = _ref10[1];
        var command = _this5.normalizeCommandName(className);
        commands[command] = Class;
      });
      return commands;
    }
  }, {
    key: "importHooks",
    value: function importHooks(hooksFromImport) {
      var hooks = {};
      for (var key in hooksFromImport) {
        var hook = new hooksFromImport[key]();
        hooks[hook.getId()] = hook;
      }
      return hooks;
    }

    /**
     * Import & initialize the component's UI states.
     * Should be used inside `defaultUiState()`.
     *
     * @param {Object} statesFromImport - UI states from import.
     *
     * @return {Object} UI States
     */
  }, {
    key: "importUiStates",
    value: function importUiStates(statesFromImport) {
      var _this6 = this;
      var uiStates = {};
      Object.values(statesFromImport).forEach(function (className) {
        var uiState = new className(_this6);
        uiStates[uiState.getId()] = uiState;
      });
      return uiStates;
    }

    /**
     * Set a UI state value.
     * TODO: Should we provide such function? Maybe the developer should implicitly pass the full state ID?
     *
     * @param {string} state - Non-prefixed state ID.
     * @param {*}      value - New state value.
     *
     * @return {void}
     */
  }, {
    key: "setUiState",
    value: function setUiState(state, value) {
      $e.uiStates.set("".concat(this.getNamespace(), "/").concat(state), value);
    }
  }, {
    key: "toggleRouteClass",
    value: function toggleRouteClass(route, state) {
      document.body.classList.toggle(this.getBodyClass(route), state);
    }
  }, {
    key: "toggleHistoryClass",
    value: function toggleHistoryClass() {
      document.body.classList.toggle('e-routes-has-history', !!$e.routes.getHistory(this.getServiceName()).length);
    }
  }]);
}(_module.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/component-modal-base.js":
/*!********************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/component-modal-base.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _readOnlyError2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/readOnlyError */ "../node_modules/@babel/runtime/helpers/readOnlyError.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _componentBase = _interopRequireDefault(__webpack_require__(/*! ./component-base */ "../modules/web-cli/assets/js/modules/component-base.js"));
var commands = _interopRequireWildcard(__webpack_require__(/*! ./commands/ */ "../modules/web-cli/assets/js/modules/commands/index.js"));
var _forceMethodImplementation = _interopRequireDefault(__webpack_require__(/*! ../utils/force-method-implementation */ "../modules/web-cli/assets/js/utils/force-method-implementation.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var ComponentModalBase = exports["default"] = /*#__PURE__*/function (_ComponentBase) {
  function ComponentModalBase() {
    (0, _classCallCheck2.default)(this, ComponentModalBase);
    return _callSuper(this, ComponentModalBase, arguments);
  }
  (0, _inherits2.default)(ComponentModalBase, _ComponentBase);
  return (0, _createClass2.default)(ComponentModalBase, [{
    key: "registerAPI",
    value: function registerAPI() {
      var _this = this;
      _superPropGet(ComponentModalBase, "registerAPI", this, 3)([]);
      $e.shortcuts.register('esc', {
        scopes: [this.getNamespace()],
        callback: function callback() {
          return _this.close();
        }
      });
    }
  }, {
    key: "defaultCommands",
    value: function defaultCommands() {
      return this.importCommands(commands);
    }
  }, {
    key: "defaultRoutes",
    value: function defaultRoutes() {
      return {
        '': function _() {/* Nothing to do, it's already rendered. */}
      };
    }
  }, {
    key: "open",
    value: function open() {
      var _this2 = this;
      if (!this.layout) {
        var layout = this.getModalLayout();
        this.layout = new layout({
          component: this
        });
        this.layout.getModal().on('hide', function () {
          return _this2.close();
        });
      }
      this.layout.showModal();
      return true;
    }
  }, {
    key: "close",
    value: function close() {
      if (!_superPropGet(ComponentModalBase, "close", this, 3)([])) {
        return false;
      }
      var close = elementor.hooks.applyFilters('component/modal/close', this.layout.getModal().hide.bind(this.layout.getModal()), this);
      close();
      return true;
    }
  }, {
    key: "getModalLayout",
    value: function getModalLayout() {
      (0, _forceMethodImplementation.default)();
    }
  }]);
}(_componentBase.default);

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/hook-base.js":
/*!*********************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/hook-base.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _forceMethodImplementation = _interopRequireDefault(__webpack_require__(/*! ../utils/force-method-implementation */ "../modules/web-cli/assets/js/utils/force-method-implementation.js"));
var HookBase = exports["default"] = /*#__PURE__*/function () {
  /**
   * Function constructor().
   *
   * Create callback base.
   */
  function HookBase() {
    (0, _classCallCheck2.default)(this, HookBase);
    /**
     * Callback type, eg ( hook, event ).
     *
     * @type {string}
     */
    (0, _defineProperty2.default)(this, "type", void 0);
    /**
     * Full command address, that will hook the callback.
     *
     * @type {string}
     */
    (0, _defineProperty2.default)(this, "command", void 0);
    /**
     * Unique id of the callback.
     *
     * @type {string}
     */
    (0, _defineProperty2.default)(this, "id", void 0);
    this.initialize();
    this.type = this.getType();
    this.command = this.getCommand();
    this.id = this.getId();
  }

  /**
   * Function initialize().
   *
   * Called after creation of the base, used for initialize extras.
   * Without expending constructor.
   */
  return (0, _createClass2.default)(HookBase, [{
    key: "initialize",
    value: function initialize() {}

    /**
     * Function register().
     *
     * Used to register the callback.
     *
     * @throws {Error}
     */
  }, {
    key: "register",
    value: function register() {
      (0, _forceMethodImplementation.default)();
    }

    // eslint-disable-next-line jsdoc/require-returns-check
    /**
     * Function getType().
     *
     * Get type eg: ( hook, event, etc ... ).
     *
     * @return {string} type
     *
     * @throws {Error}
     */
  }, {
    key: "getType",
    value: function getType() {
      (0, _forceMethodImplementation.default)();
    }

    // eslint-disable-next-line jsdoc/require-returns-check
    /**
     * Function getCommand().
     *
     * Returns the full command path for callback binding.
     *
     * Supports array of strings ( commands ).
     *
     * @return {string} command
     *
     * @throws {Error}
     */
  }, {
    key: "getCommand",
    value: function getCommand() {
      (0, _forceMethodImplementation.default)();
    }

    // eslint-disable-next-line jsdoc/require-returns-check
    /**
     * Function getId().
     *
     * Returns command id for the hook (should be unique).
     *
     * @return {string} id
     *
     * @throws {Error}
     */
  }, {
    key: "getId",
    value: function getId() {
      (0, _forceMethodImplementation.default)();
    }

    // eslint-disable-next-line jsdoc/require-returns-check
    /**
     * Function getContainerType().
     *
     * Bind eContainer type to callback.
     *
     * Used to gain performance.
     *
     * @return {string} type
     */
  }, {
    key: "getContainerType",
    value: function getContainerType() {}

    /**
     * Function getConditions().
     *
     * Condition for running the callback, if true, call to apply().
     *
     * @param {*} [args={}]
     * @param {*} [result=*]
     *
     * @return {boolean} conditions
     */
  }, {
    key: "getConditions",
    value: function getConditions() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var result = arguments.length > 1 ? arguments[1] : undefined;
      // eslint-disable-line no-unused-vars
      return true;
    }

    // eslint-disable-next-line jsdoc/require-returns-check
    /**
     * Function apply().
     *
     * Apply the callback, ( The actual affect of the callback ).
     *
     * @param {*} [args={}]
     *
     * @return {*} results
     */
  }, {
    key: "apply",
    value: function apply(args) {
      // eslint-disable-line no-unused-vars
      (0, _forceMethodImplementation.default)();
    }

    /**
     * Function run().
     *
     * Run the callback.
     *
     * @param {*} args
     *
     * @return {*} results
     */
  }, {
    key: "run",
    value: function run() {
      var _ref = arguments.length <= 0 ? undefined : arguments[0],
        _ref$options = _ref.options,
        options = _ref$options === void 0 ? {} : _ref$options;

      // Disable callback if requested by args.options.
      if (options.callbacks && false === options.callbacks[this.id]) {
        return true;
      }
      if (this.getConditions.apply(this, arguments)) {
        if ($e.devTools) {
          $e.devTools.log.callbacks().active(this.type, this.command, this.id);
        }
        return this.apply.apply(this, arguments);
      }
      return true;
    }
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/hook-break.js":
/*!**********************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/hook-break.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _wrapNativeSuper2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/wrapNativeSuper */ "../node_modules/@babel/runtime/helpers/wrapNativeSuper.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var HookBreak = exports["default"] = /*#__PURE__*/function (_Error) {
  function HookBreak() {
    (0, _classCallCheck2.default)(this, HookBreak);
    return _callSuper(this, HookBreak, ['HookBreak']);
  }
  (0, _inherits2.default)(HookBreak, _Error);
  return (0, _createClass2.default)(HookBreak);
}(/*#__PURE__*/(0, _wrapNativeSuper2.default)(Error));

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/hooks/data/after.js":
/*!****************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/hooks/data/after.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.After = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ./base */ "../modules/web-cli/assets/js/modules/hooks/data/base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var After = exports.After = /*#__PURE__*/function (_Base) {
  function After() {
    (0, _classCallCheck2.default)(this, After);
    return _callSuper(this, After, arguments);
  }
  (0, _inherits2.default)(After, _Base);
  return (0, _createClass2.default)(After, [{
    key: "register",
    value: function register() {
      $e.hooks.registerDataAfter(this);
    }
  }]);
}(_base.default);
var _default = exports["default"] = After;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/hooks/data/base.js":
/*!***************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/hooks/data/base.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Base = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _hookBase = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/hook-base */ "../modules/web-cli/assets/js/modules/hook-base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Base = exports.Base = /*#__PURE__*/function (_HookBase) {
  function Base() {
    (0, _classCallCheck2.default)(this, Base);
    return _callSuper(this, Base, arguments);
  }
  (0, _inherits2.default)(Base, _HookBase);
  return (0, _createClass2.default)(Base, [{
    key: "getType",
    value: function getType() {
      return 'data';
    }
  }]);
}(_hookBase.default);
var _default = exports["default"] = Base;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/hooks/data/catch.js":
/*!****************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/hooks/data/catch.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Catch = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ./base */ "../modules/web-cli/assets/js/modules/hooks/data/base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Catch = exports.Catch = /*#__PURE__*/function (_Base) {
  function Catch() {
    (0, _classCallCheck2.default)(this, Catch);
    return _callSuper(this, Catch, arguments);
  }
  (0, _inherits2.default)(Catch, _Base);
  return (0, _createClass2.default)(Catch, [{
    key: "register",
    value: function register() {
      $e.hooks.registerDataCatch(this);
    }
  }]);
}(_base.default);
var _default = exports["default"] = Catch;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/hooks/data/dependency.js":
/*!*********************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/hooks/data/dependency.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Dependency = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ./base */ "../modules/web-cli/assets/js/modules/hooks/data/base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Dependency = exports.Dependency = /*#__PURE__*/function (_Base) {
  function Dependency() {
    (0, _classCallCheck2.default)(this, Dependency);
    return _callSuper(this, Dependency, arguments);
  }
  (0, _inherits2.default)(Dependency, _Base);
  return (0, _createClass2.default)(Dependency, [{
    key: "register",
    value: function register() {
      $e.hooks.registerDataDependency(this);
    }
  }]);
}(_base.default);
var _default = exports["default"] = Dependency;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/hooks/data/index.js":
/*!****************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/hooks/data/index.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "After", ({
  enumerable: true,
  get: function get() {
    return _after.After;
  }
}));
Object.defineProperty(exports, "Base", ({
  enumerable: true,
  get: function get() {
    return _base.Base;
  }
}));
Object.defineProperty(exports, "Catch", ({
  enumerable: true,
  get: function get() {
    return _catch.Catch;
  }
}));
Object.defineProperty(exports, "Dependency", ({
  enumerable: true,
  get: function get() {
    return _dependency.Dependency;
  }
}));
var _after = __webpack_require__(/*! ./after */ "../modules/web-cli/assets/js/modules/hooks/data/after.js");
var _base = __webpack_require__(/*! ./base */ "../modules/web-cli/assets/js/modules/hooks/data/base.js");
var _catch = __webpack_require__(/*! ./catch */ "../modules/web-cli/assets/js/modules/hooks/data/catch.js");
var _dependency = __webpack_require__(/*! ./dependency */ "../modules/web-cli/assets/js/modules/hooks/data/dependency.js");

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/hooks/ui/after.js":
/*!**************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/hooks/ui/after.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.After = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ./base */ "../modules/web-cli/assets/js/modules/hooks/ui/base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var After = exports.After = /*#__PURE__*/function (_Base) {
  function After() {
    (0, _classCallCheck2.default)(this, After);
    return _callSuper(this, After, arguments);
  }
  (0, _inherits2.default)(After, _Base);
  return (0, _createClass2.default)(After, [{
    key: "register",
    value: function register() {
      $e.hooks.registerUIAfter(this);
    }
  }]);
}(_base.default);
var _default = exports["default"] = After;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/hooks/ui/base.js":
/*!*************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/hooks/ui/base.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Base = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _hookBase = _interopRequireDefault(__webpack_require__(/*! elementor-api/modules/hook-base */ "../modules/web-cli/assets/js/modules/hook-base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Base = exports.Base = /*#__PURE__*/function (_HookBase) {
  function Base() {
    (0, _classCallCheck2.default)(this, Base);
    return _callSuper(this, Base, arguments);
  }
  (0, _inherits2.default)(Base, _HookBase);
  return (0, _createClass2.default)(Base, [{
    key: "getType",
    value: function getType() {
      return 'ui';
    }
  }]);
}(_hookBase.default);
var _default = exports["default"] = Base;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/hooks/ui/before.js":
/*!***************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/hooks/ui/before.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Before = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ./base */ "../modules/web-cli/assets/js/modules/hooks/ui/base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Before = exports.Before = /*#__PURE__*/function (_Base) {
  function Before() {
    (0, _classCallCheck2.default)(this, Before);
    return _callSuper(this, Before, arguments);
  }
  (0, _inherits2.default)(Before, _Base);
  return (0, _createClass2.default)(Before, [{
    key: "register",
    value: function register() {
      $e.hooks.registerUIBefore(this);
    }
  }]);
}(_base.default);
var _default = exports["default"] = Before;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/hooks/ui/catch.js":
/*!**************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/hooks/ui/catch.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Catch = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ./base */ "../modules/web-cli/assets/js/modules/hooks/ui/base.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Catch = exports.Catch = /*#__PURE__*/function (_Base) {
  function Catch() {
    (0, _classCallCheck2.default)(this, Catch);
    return _callSuper(this, Catch, arguments);
  }
  (0, _inherits2.default)(Catch, _Base);
  return (0, _createClass2.default)(Catch, [{
    key: "register",
    value: function register() {
      $e.hooks.registerUICatch(this);
    }
  }]);
}(_base.default);
var _default = exports["default"] = Catch;

/***/ }),

/***/ "../modules/web-cli/assets/js/modules/hooks/ui/index.js":
/*!**************************************************************!*\
  !*** ../modules/web-cli/assets/js/modules/hooks/ui/index.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "After", ({
  enumerable: true,
  get: function get() {
    return _after.After;
  }
}));
Object.defineProperty(exports, "Base", ({
  enumerable: true,
  get: function get() {
    return _base.Base;
  }
}));
Object.defineProperty(exports, "Before", ({
  enumerable: true,
  get: function get() {
    return _before.Before;
  }
}));
Object.defineProperty(exports, "Catch", ({
  enumerable: true,
  get: function get() {
    return _catch.Catch;
  }
}));
var _after = __webpack_require__(/*! ./after */ "../modules/web-cli/assets/js/modules/hooks/ui/after.js");
var _base = __webpack_require__(/*! ./base */ "../modules/web-cli/assets/js/modules/hooks/ui/base.js");
var _before = __webpack_require__(/*! ./before */ "../modules/web-cli/assets/js/modules/hooks/ui/before.js");
var _catch = __webpack_require__(/*! ./catch */ "../modules/web-cli/assets/js/modules/hooks/ui/catch.js");

/***/ }),

/***/ "../modules/web-cli/assets/js/utils/console.js":
/*!*****************************************************!*\
  !*** ../modules/web-cli/assets/js/utils/console.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var Console = exports["default"] = /*#__PURE__*/function () {
  function Console() {
    (0, _classCallCheck2.default)(this, Console);
  }
  return (0, _createClass2.default)(Console, null, [{
    key: "error",
    value: function error(message) {
      // Show an error if devTools is available.
      if ($e.devTools) {
        $e.devTools.log.error(message);
      }

      // If not a 'Hook-Break' then show error.
      if (!(message instanceof $e.modules.HookBreak)) {
        // eslint-disable-next-line no-console
        console.error(message);
      }
    }
  }, {
    key: "warn",
    value: function warn() {
      var _console;
      var style = "font-size: 12px; background-image: url(\"".concat(elementorWebCliConfig.urls.assets, "images/logo-icon.png\"); background-repeat: no-repeat; background-size: contain;");
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }
      args.unshift('%c  %c', style, '');
      (_console = console).warn.apply(_console, args); // eslint-disable-line no-console
    }
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/utils/deprecation.js":
/*!*********************************************************!*\
  !*** ../modules/web-cli/assets/js/utils/deprecation.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _console = _interopRequireDefault(__webpack_require__(/*! elementor-api/utils/console */ "../modules/web-cli/assets/js/utils/console.js"));
// Copied from `modules/dev-tools/assets/js/deprecation.js`
/**
 * @typedef {Object} Version
 * @property {number} major1 The first number
 * @property {number} major2 The second number
 * @property {number} minor  The third number
 * @property {string} build  The fourth number
 */

var softDeprecated = function softDeprecated(name, version, replacement) {
  if (elementorWebCliConfig.isDebug) {
    deprecatedMessage('soft', name, version, replacement);
  }
};
var hardDeprecated = function hardDeprecated(name, version, replacement) {
  deprecatedMessage('hard', name, version, replacement);
};
var deprecatedMessage = function deprecatedMessage(type, name, version, replacement) {
  var message = "`".concat(name, "` is ").concat(type, " deprecated since ").concat(version);
  if (replacement) {
    message += " - Use `".concat(replacement, "` instead");
  }
  _console.default.warn(message);
};
var Deprecation = exports["default"] = /*#__PURE__*/function () {
  function Deprecation() {
    (0, _classCallCheck2.default)(this, Deprecation);
  }
  return (0, _createClass2.default)(Deprecation, null, [{
    key: "deprecated",
    value: function deprecated(name, version, replacement) {
      if (this.isHardDeprecated(version)) {
        hardDeprecated(name, version, replacement);
      } else {
        softDeprecated(name, version, replacement);
      }
    }

    /**
     * @param {string} version
     *
     * @return {Version}
     */
  }, {
    key: "parseVersion",
    value: function parseVersion(version) {
      var versionParts = version.split('.');
      if (versionParts.length < 3 || versionParts.length > 4) {
        throw new RangeError('Invalid Semantic Version string provided');
      }
      var _versionParts = (0, _slicedToArray2.default)(versionParts, 4),
        major1 = _versionParts[0],
        major2 = _versionParts[1],
        minor = _versionParts[2],
        _versionParts$ = _versionParts[3],
        build = _versionParts$ === void 0 ? '' : _versionParts$;
      return {
        major1: parseInt(major1),
        major2: parseInt(major2),
        minor: parseInt(minor),
        build: build
      };
    }

    /**
     * Get total of major.
     *
     * Since `get_total_major` cannot determine how much really versions between 2.9.0 and 3.3.0 if there is 2.10.0 version for example,
     * versions with major2 more then 9 will be added to total.
     *
     * @param {Version} versionObj
     *
     * @return {number}
     */
  }, {
    key: "getTotalMajor",
    value: function getTotalMajor(versionObj) {
      var total = parseInt("".concat(versionObj.major1).concat(versionObj.major2, "0"));
      total = Number((total / 10).toFixed(0));
      if (versionObj.major2 > 9) {
        total = versionObj.major2 - 9;
      }
      return total;
    }

    /**
     * @param {string} version1
     * @param {string} version2
     *
     * @return {number}
     */
  }, {
    key: "compareVersion",
    value: function compareVersion(version1, version2) {
      var _this = this;
      return [this.parseVersion(version1), this.parseVersion(version2)].map(function (versionObj) {
        return _this.getTotalMajor(versionObj);
      }).reduce(function (acc, major) {
        return acc - major;
      });
    }

    /**
     * @param {string} version
     *
     * @return {boolean}
     */
  }, {
    key: "isSoftDeprecated",
    value: function isSoftDeprecated(version) {
      var total = this.compareVersion(version, elementorWebCliConfig.version);
      return total <= 4;
    }

    /**
     * @param {string} version
     * @return {boolean}
     */
  }, {
    key: "isHardDeprecated",
    value: function isHardDeprecated(version) {
      var total = this.compareVersion(version, elementorWebCliConfig.version);
      return total < 0 || total >= 8;
    }
  }]);
}();

/***/ }),

/***/ "../modules/web-cli/assets/js/utils/environment.js":
/*!*********************************************************!*\
  !*** ../modules/web-cli/assets/js/utils/environment.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
// TODO: Copied from `core/common/assets/js/utils/environment.js`.
var matchUserAgent = function matchUserAgent(UserAgentStr) {
    return userAgent.indexOf(UserAgentStr) >= 0;
  },
  userAgent = navigator.userAgent,
  // Solution influenced by https://stackoverflow.com/questions/9847580/how-to-detect-safari-chrome-ie-firefox-and-opera-browser

  // Opera 8.0+
  isOpera = !!window.opr && !!opr.addons || !!window.opera || matchUserAgent(' OPR/'),
  // Firefox 1.0+
  isFirefox = matchUserAgent('Firefox'),
  // Safari 3.0+ "[object HTMLElementConstructor]"
  isSafari = /^((?!chrome|android).)*safari/i.test(userAgent) || /constructor/i.test(window.HTMLElement) || function (p) {
    return '[object SafariRemoteNotification]' === p.toString();
  }(!window.safari || typeof safari !== 'undefined' && safari.pushNotification),
  // Internet Explorer 6-11
  isIE = /Trident|MSIE/.test(userAgent) && (/* @cc_on!@*/ false || !!document.documentMode),
  // Edge 20+
  isEdge = !isIE && !!window.StyleMedia || matchUserAgent('Edg'),
  // Google Chrome (Not accurate)
  isChrome = !!window.chrome && matchUserAgent('Chrome') && !(isEdge || isOpera),
  // Blink engine
  isBlink = matchUserAgent('Chrome') && !!window.CSS,
  // Apple Webkit engine
  isAppleWebkit = matchUserAgent('AppleWebKit') && !isBlink,
  environment = {
    appleWebkit: isAppleWebkit,
    blink: isBlink,
    chrome: isChrome,
    edge: isEdge,
    firefox: isFirefox,
    ie: isIE,
    mac: matchUserAgent('Macintosh'),
    opera: isOpera,
    safari: isSafari,
    webkit: matchUserAgent('AppleWebKit')
  };
var _default = exports["default"] = environment;

/***/ }),

/***/ "../modules/web-cli/assets/js/utils/force-method-implementation.js":
/*!*************************************************************************!*\
  !*** ../modules/web-cli/assets/js/utils/force-method-implementation.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.ForceMethodImplementation = void 0;
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _wrapNativeSuper2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/wrapNativeSuper */ "../node_modules/@babel/runtime/helpers/wrapNativeSuper.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
// TODO: Copied from `assets/dev/js/modules/imports/force-method-implementation.js`;
var ForceMethodImplementation = exports.ForceMethodImplementation = /*#__PURE__*/function (_Error) {
  function ForceMethodImplementation() {
    var _this;
    var info = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    (0, _classCallCheck2.default)(this, ForceMethodImplementation);
    _this = _callSuper(this, ForceMethodImplementation, ["".concat(info.isStatic ? 'static ' : '').concat(info.fullName, "() should be implemented, please provide '").concat(info.functionName || info.fullName, "' functionality.")]);
    Error.captureStackTrace(_this, ForceMethodImplementation);
    return _this;
  }
  (0, _inherits2.default)(ForceMethodImplementation, _Error);
  return (0, _createClass2.default)(ForceMethodImplementation);
}(/*#__PURE__*/(0, _wrapNativeSuper2.default)(Error));
var _default = exports["default"] = function _default() {
  var stack = Error().stack,
    caller = stack.split('\n')[2].trim(),
    callerName = caller.startsWith('at new') ? 'constructor' : caller.split(' ')[1],
    info = {};
  info.functionName = callerName;
  info.fullName = callerName;
  if (info.functionName.includes('.')) {
    var parts = info.functionName.split('.');
    info.className = parts[0];
    info.functionName = parts[1];
  } else {
    info.isStatic = true;
  }
  throw new ForceMethodImplementation(info);
};

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/OverloadYield.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/OverloadYield.js ***!
  \***************************************************************/
/***/ ((module) => {

function _OverloadYield(e, d) {
  this.v = e, this.k = d;
}
module.exports = _OverloadYield, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \******************************************************************/
/***/ ((module) => {

function _arrayLikeToArray(r, a) {
  (null == a || a > r.length) && (a = r.length);
  for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e];
  return n;
}
module.exports = _arrayLikeToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayWithHoles.js ***!
  \****************************************************************/
/***/ ((module) => {

function _arrayWithHoles(r) {
  if (Array.isArray(r)) return r;
}
module.exports = _arrayWithHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/assertThisInitialized.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/assertThisInitialized.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _assertThisInitialized(e) {
  if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  return e;
}
module.exports = _assertThisInitialized, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/asyncToGenerator.js ***!
  \******************************************************************/
/***/ ((module) => {

function asyncGeneratorStep(n, t, e, r, o, a, c) {
  try {
    var i = n[a](c),
      u = i.value;
  } catch (n) {
    return void e(n);
  }
  i.done ? t(u) : Promise.resolve(u).then(r, o);
}
function _asyncToGenerator(n) {
  return function () {
    var t = this,
      e = arguments;
    return new Promise(function (r, o) {
      var a = n.apply(t, e);
      function _next(n) {
        asyncGeneratorStep(a, r, o, _next, _throw, "next", n);
      }
      function _throw(n) {
        asyncGeneratorStep(a, r, o, _next, _throw, "throw", n);
      }
      _next(void 0);
    });
  };
}
module.exports = _asyncToGenerator, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/classCallCheck.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/classCallCheck.js ***!
  \****************************************************************/
/***/ ((module) => {

function _classCallCheck(a, n) {
  if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function");
}
module.exports = _classCallCheck, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/construct.js":
/*!***********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/construct.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var isNativeReflectConstruct = __webpack_require__(/*! ./isNativeReflectConstruct.js */ "../node_modules/@babel/runtime/helpers/isNativeReflectConstruct.js");
var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js");
function _construct(t, e, r) {
  if (isNativeReflectConstruct()) return Reflect.construct.apply(null, arguments);
  var o = [null];
  o.push.apply(o, e);
  var p = new (t.bind.apply(t, o))();
  return r && setPrototypeOf(p, r.prototype), p;
}
module.exports = _construct, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/createClass.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/createClass.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/toPropertyKey.js");
function _defineProperties(e, r) {
  for (var t = 0; t < r.length; t++) {
    var o = r[t];
    o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, toPropertyKey(o.key), o);
  }
}
function _createClass(e, r, t) {
  return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", {
    writable: !1
  }), e;
}
module.exports = _createClass, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/defineProperty.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/toPropertyKey.js");
function _defineProperty(e, r, t) {
  return (r = toPropertyKey(r)) in e ? Object.defineProperty(e, r, {
    value: t,
    enumerable: !0,
    configurable: !0,
    writable: !0
  }) : e[r] = t, e;
}
module.exports = _defineProperty, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \********************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _defineProperty)
/* harmony export */ });
/* harmony import */ var _toPropertyKey_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/esm/toPropertyKey.js");

function _defineProperty(e, r, t) {
  return (r = (0,_toPropertyKey_js__WEBPACK_IMPORTED_MODULE_0__["default"])(r)) in e ? Object.defineProperty(e, r, {
    value: t,
    enumerable: !0,
    configurable: !0,
    writable: !0
  }) : e[r] = t, e;
}


/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/esm/objectSpread2.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/esm/objectSpread2.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _objectSpread2)
/* harmony export */ });
/* harmony import */ var _defineProperty_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./defineProperty.js */ "../node_modules/@babel/runtime/helpers/esm/defineProperty.js");

function ownKeys(e, r) {
  var t = Object.keys(e);
  if (Object.getOwnPropertySymbols) {
    var o = Object.getOwnPropertySymbols(e);
    r && (o = o.filter(function (r) {
      return Object.getOwnPropertyDescriptor(e, r).enumerable;
    })), t.push.apply(t, o);
  }
  return t;
}
function _objectSpread2(e) {
  for (var r = 1; r < arguments.length; r++) {
    var t = null != arguments[r] ? arguments[r] : {};
    r % 2 ? ownKeys(Object(t), !0).forEach(function (r) {
      (0,_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__["default"])(e, r, t[r]);
    }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) {
      Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
    });
  }
  return e;
}


/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/esm/toPrimitive.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/esm/toPrimitive.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ toPrimitive)
/* harmony export */ });
/* harmony import */ var _typeof_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/esm/typeof.js");

function toPrimitive(t, r) {
  if ("object" != (0,_typeof_js__WEBPACK_IMPORTED_MODULE_0__["default"])(t) || !t) return t;
  var e = t[Symbol.toPrimitive];
  if (void 0 !== e) {
    var i = e.call(t, r || "default");
    if ("object" != (0,_typeof_js__WEBPACK_IMPORTED_MODULE_0__["default"])(i)) return i;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return ("string" === r ? String : Number)(t);
}


/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/esm/toPropertyKey.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/esm/toPropertyKey.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ toPropertyKey)
/* harmony export */ });
/* harmony import */ var _typeof_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/esm/typeof.js");
/* harmony import */ var _toPrimitive_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./toPrimitive.js */ "../node_modules/@babel/runtime/helpers/esm/toPrimitive.js");


function toPropertyKey(t) {
  var i = (0,_toPrimitive_js__WEBPACK_IMPORTED_MODULE_1__["default"])(t, "string");
  return "symbol" == (0,_typeof_js__WEBPACK_IMPORTED_MODULE_0__["default"])(i) ? i : i + "";
}


/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/esm/typeof.js":
/*!************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/esm/typeof.js ***!
  \************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _typeof)
/* harmony export */ });
function _typeof(o) {
  "@babel/helpers - typeof";

  return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
    return typeof o;
  } : function (o) {
    return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
  }, _typeof(o);
}


/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/get.js":
/*!*****************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/get.js ***!
  \*****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var superPropBase = __webpack_require__(/*! ./superPropBase.js */ "../node_modules/@babel/runtime/helpers/superPropBase.js");
function _get() {
  return module.exports = _get = "undefined" != typeof Reflect && Reflect.get ? Reflect.get.bind() : function (e, t, r) {
    var p = superPropBase(e, t);
    if (p) {
      var n = Object.getOwnPropertyDescriptor(p, t);
      return n.get ? n.get.call(arguments.length < 3 ? e : r) : n.value;
    }
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _get.apply(null, arguments);
}
module.exports = _get, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/getPrototypeOf.js ***!
  \****************************************************************/
/***/ ((module) => {

function _getPrototypeOf(t) {
  return module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) {
    return t.__proto__ || Object.getPrototypeOf(t);
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _getPrototypeOf(t);
}
module.exports = _getPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/inherits.js":
/*!**********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/inherits.js ***!
  \**********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js");
function _inherits(t, e) {
  if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
  t.prototype = Object.create(e && e.prototype, {
    constructor: {
      value: t,
      writable: !0,
      configurable: !0
    }
  }), Object.defineProperty(t, "prototype", {
    writable: !1
  }), e && setPrototypeOf(t, e);
}
module.exports = _inherits, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/interopRequireDefault.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _interopRequireDefault(e) {
  return e && e.__esModule ? e : {
    "default": e
  };
}
module.exports = _interopRequireDefault, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/isNativeFunction.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/isNativeFunction.js ***!
  \******************************************************************/
/***/ ((module) => {

function _isNativeFunction(t) {
  try {
    return -1 !== Function.toString.call(t).indexOf("[native code]");
  } catch (n) {
    return "function" == typeof t;
  }
}
module.exports = _isNativeFunction, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/isNativeReflectConstruct.js":
/*!**************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/isNativeReflectConstruct.js ***!
  \**************************************************************************/
/***/ ((module) => {

function _isNativeReflectConstruct() {
  try {
    var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
  } catch (t) {}
  return (module.exports = _isNativeReflectConstruct = function _isNativeReflectConstruct() {
    return !!t;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports)();
}
module.exports = _isNativeReflectConstruct, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js":
/*!**********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js ***!
  \**********************************************************************/
/***/ ((module) => {

function _iterableToArrayLimit(r, l) {
  var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
  if (null != t) {
    var e,
      n,
      i,
      u,
      a = [],
      f = !0,
      o = !1;
    try {
      if (i = (t = t.call(r)).next, 0 === l) {
        if (Object(t) !== t) return;
        f = !1;
      } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0);
    } catch (r) {
      o = !0, n = r;
    } finally {
      try {
        if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return;
      } finally {
        if (o) throw n;
      }
    }
    return a;
  }
}
module.exports = _iterableToArrayLimit, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/nonIterableRest.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/nonIterableRest.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableRest, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js":
/*!***************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js ***!
  \***************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
var assertThisInitialized = __webpack_require__(/*! ./assertThisInitialized.js */ "../node_modules/@babel/runtime/helpers/assertThisInitialized.js");
function _possibleConstructorReturn(t, e) {
  if (e && ("object" == _typeof(e) || "function" == typeof e)) return e;
  if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined");
  return assertThisInitialized(t);
}
module.exports = _possibleConstructorReturn, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/readOnlyError.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/readOnlyError.js ***!
  \***************************************************************/
/***/ ((module) => {

function _readOnlyError(r) {
  throw new TypeError('"' + r + '" is read-only');
}
module.exports = _readOnlyError, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regenerator.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regenerator.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var regeneratorDefine = __webpack_require__(/*! ./regeneratorDefine.js */ "../node_modules/@babel/runtime/helpers/regeneratorDefine.js");
function _regenerator() {
  /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */
  var e,
    t,
    r = "function" == typeof Symbol ? Symbol : {},
    n = r.iterator || "@@iterator",
    o = r.toStringTag || "@@toStringTag";
  function i(r, n, o, i) {
    var c = n && n.prototype instanceof Generator ? n : Generator,
      u = Object.create(c.prototype);
    return regeneratorDefine(u, "_invoke", function (r, n, o) {
      var i,
        c,
        u,
        f = 0,
        p = o || [],
        y = !1,
        G = {
          p: 0,
          n: 0,
          v: e,
          a: d,
          f: d.bind(e, 4),
          d: function d(t, r) {
            return i = t, c = 0, u = e, G.n = r, a;
          }
        };
      function d(r, n) {
        for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) {
          var o,
            i = p[t],
            d = G.p,
            l = i[2];
          r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0));
        }
        if (o || r > 1) return a;
        throw y = !0, n;
      }
      return function (o, p, l) {
        if (f > 1) throw TypeError("Generator is already running");
        for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) {
          i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u);
          try {
            if (f = 2, i) {
              if (c || (o = "next"), t = i[o]) {
                if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object");
                if (!t.done) return t;
                u = t.value, c < 2 && (c = 0);
              } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1);
              i = e;
            } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break;
          } catch (t) {
            i = e, c = 1, u = t;
          } finally {
            f = 1;
          }
        }
        return {
          value: t,
          done: y
        };
      };
    }(r, o, i), !0), u;
  }
  var a = {};
  function Generator() {}
  function GeneratorFunction() {}
  function GeneratorFunctionPrototype() {}
  t = Object.getPrototypeOf;
  var c = [][n] ? t(t([][n]())) : (regeneratorDefine(t = {}, n, function () {
      return this;
    }), t),
    u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c);
  function f(e) {
    return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, regeneratorDefine(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e;
  }
  return GeneratorFunction.prototype = GeneratorFunctionPrototype, regeneratorDefine(u, "constructor", GeneratorFunctionPrototype), regeneratorDefine(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", regeneratorDefine(GeneratorFunctionPrototype, o, "GeneratorFunction"), regeneratorDefine(u), regeneratorDefine(u, o, "Generator"), regeneratorDefine(u, n, function () {
    return this;
  }), regeneratorDefine(u, "toString", function () {
    return "[object Generator]";
  }), (module.exports = _regenerator = function _regenerator() {
    return {
      w: i,
      m: f
    };
  }, module.exports.__esModule = true, module.exports["default"] = module.exports)();
}
module.exports = _regenerator, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorAsync.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorAsync.js ***!
  \******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var regeneratorAsyncGen = __webpack_require__(/*! ./regeneratorAsyncGen.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsyncGen.js");
function _regeneratorAsync(n, e, r, t, o) {
  var a = regeneratorAsyncGen(n, e, r, t, o);
  return a.next().then(function (n) {
    return n.done ? n.value : a.next();
  });
}
module.exports = _regeneratorAsync, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorAsyncGen.js":
/*!*********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorAsyncGen.js ***!
  \*********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var regenerator = __webpack_require__(/*! ./regenerator.js */ "../node_modules/@babel/runtime/helpers/regenerator.js");
var regeneratorAsyncIterator = __webpack_require__(/*! ./regeneratorAsyncIterator.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsyncIterator.js");
function _regeneratorAsyncGen(r, e, t, o, n) {
  return new regeneratorAsyncIterator(regenerator().w(r, e, t, o), n || Promise);
}
module.exports = _regeneratorAsyncGen, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorAsyncIterator.js":
/*!**************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorAsyncIterator.js ***!
  \**************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var OverloadYield = __webpack_require__(/*! ./OverloadYield.js */ "../node_modules/@babel/runtime/helpers/OverloadYield.js");
var regeneratorDefine = __webpack_require__(/*! ./regeneratorDefine.js */ "../node_modules/@babel/runtime/helpers/regeneratorDefine.js");
function AsyncIterator(t, e) {
  function n(r, o, i, f) {
    try {
      var c = t[r](o),
        u = c.value;
      return u instanceof OverloadYield ? e.resolve(u.v).then(function (t) {
        n("next", t, i, f);
      }, function (t) {
        n("throw", t, i, f);
      }) : e.resolve(u).then(function (t) {
        c.value = t, i(c);
      }, function (t) {
        return n("throw", t, i, f);
      });
    } catch (t) {
      f(t);
    }
  }
  var r;
  this.next || (regeneratorDefine(AsyncIterator.prototype), regeneratorDefine(AsyncIterator.prototype, "function" == typeof Symbol && Symbol.asyncIterator || "@asyncIterator", function () {
    return this;
  })), regeneratorDefine(this, "_invoke", function (t, o, i) {
    function f() {
      return new e(function (e, r) {
        n(t, i, e, r);
      });
    }
    return r = r ? r.then(f, f) : f();
  }, !0);
}
module.exports = AsyncIterator, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorDefine.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorDefine.js ***!
  \*******************************************************************/
/***/ ((module) => {

function _regeneratorDefine(e, r, n, t) {
  var i = Object.defineProperty;
  try {
    i({}, "", {});
  } catch (e) {
    i = 0;
  }
  module.exports = _regeneratorDefine = function regeneratorDefine(e, r, n, t) {
    if (r) i ? i(e, r, {
      value: n,
      enumerable: !t,
      configurable: !t,
      writable: !t
    }) : e[r] = n;else {
      var o = function o(r, n) {
        _regeneratorDefine(e, r, function (e) {
          return this._invoke(r, n, e);
        });
      };
      o("next", 0), o("throw", 1), o("return", 2);
    }
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _regeneratorDefine(e, r, n, t);
}
module.exports = _regeneratorDefine, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorKeys.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorKeys.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _regeneratorKeys(e) {
  var n = Object(e),
    r = [];
  for (var t in n) r.unshift(t);
  return function e() {
    for (; r.length;) if ((t = r.pop()) in n) return e.value = t, e.done = !1, e;
    return e.done = !0, e;
  };
}
module.exports = _regeneratorKeys, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorRuntime.js":
/*!********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorRuntime.js ***!
  \********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var OverloadYield = __webpack_require__(/*! ./OverloadYield.js */ "../node_modules/@babel/runtime/helpers/OverloadYield.js");
var regenerator = __webpack_require__(/*! ./regenerator.js */ "../node_modules/@babel/runtime/helpers/regenerator.js");
var regeneratorAsync = __webpack_require__(/*! ./regeneratorAsync.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsync.js");
var regeneratorAsyncGen = __webpack_require__(/*! ./regeneratorAsyncGen.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsyncGen.js");
var regeneratorAsyncIterator = __webpack_require__(/*! ./regeneratorAsyncIterator.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsyncIterator.js");
var regeneratorKeys = __webpack_require__(/*! ./regeneratorKeys.js */ "../node_modules/@babel/runtime/helpers/regeneratorKeys.js");
var regeneratorValues = __webpack_require__(/*! ./regeneratorValues.js */ "../node_modules/@babel/runtime/helpers/regeneratorValues.js");
function _regeneratorRuntime() {
  "use strict";

  var r = regenerator(),
    e = r.m(_regeneratorRuntime),
    t = (Object.getPrototypeOf ? Object.getPrototypeOf(e) : e.__proto__).constructor;
  function n(r) {
    var e = "function" == typeof r && r.constructor;
    return !!e && (e === t || "GeneratorFunction" === (e.displayName || e.name));
  }
  var o = {
    "throw": 1,
    "return": 2,
    "break": 3,
    "continue": 3
  };
  function a(r) {
    var e, t;
    return function (n) {
      e || (e = {
        stop: function stop() {
          return t(n.a, 2);
        },
        "catch": function _catch() {
          return n.v;
        },
        abrupt: function abrupt(r, e) {
          return t(n.a, o[r], e);
        },
        delegateYield: function delegateYield(r, o, a) {
          return e.resultName = o, t(n.d, regeneratorValues(r), a);
        },
        finish: function finish(r) {
          return t(n.f, r);
        }
      }, t = function t(r, _t, o) {
        n.p = e.prev, n.n = e.next;
        try {
          return r(_t, o);
        } finally {
          e.next = n.n;
        }
      }), e.resultName && (e[e.resultName] = n.v, e.resultName = void 0), e.sent = n.v, e.next = n.n;
      try {
        return r.call(this, e);
      } finally {
        n.p = e.prev, n.n = e.next;
      }
    };
  }
  return (module.exports = _regeneratorRuntime = function _regeneratorRuntime() {
    return {
      wrap: function wrap(e, t, n, o) {
        return r.w(a(e), t, n, o && o.reverse());
      },
      isGeneratorFunction: n,
      mark: r.m,
      awrap: function awrap(r, e) {
        return new OverloadYield(r, e);
      },
      AsyncIterator: regeneratorAsyncIterator,
      async: function async(r, e, t, o, u) {
        return (n(e) ? regeneratorAsyncGen : regeneratorAsync)(a(r), e, t, o, u);
      },
      keys: regeneratorKeys,
      values: regeneratorValues
    };
  }, module.exports.__esModule = true, module.exports["default"] = module.exports)();
}
module.exports = _regeneratorRuntime, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorValues.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorValues.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
function _regeneratorValues(e) {
  if (null != e) {
    var t = e["function" == typeof Symbol && Symbol.iterator || "@@iterator"],
      r = 0;
    if (t) return t.call(e);
    if ("function" == typeof e.next) return e;
    if (!isNaN(e.length)) return {
      next: function next() {
        return e && r >= e.length && (e = void 0), {
          value: e && e[r++],
          done: !e
        };
      }
    };
  }
  throw new TypeError(_typeof(e) + " is not iterable");
}
module.exports = _regeneratorValues, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/setPrototypeOf.js ***!
  \****************************************************************/
/***/ ((module) => {

function _setPrototypeOf(t, e) {
  return module.exports = _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) {
    return t.__proto__ = e, t;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _setPrototypeOf(t, e);
}
module.exports = _setPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/slicedToArray.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/slicedToArray.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayWithHoles = __webpack_require__(/*! ./arrayWithHoles.js */ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js");
var iterableToArrayLimit = __webpack_require__(/*! ./iterableToArrayLimit.js */ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableRest = __webpack_require__(/*! ./nonIterableRest.js */ "../node_modules/@babel/runtime/helpers/nonIterableRest.js");
function _slicedToArray(r, e) {
  return arrayWithHoles(r) || iterableToArrayLimit(r, e) || unsupportedIterableToArray(r, e) || nonIterableRest();
}
module.exports = _slicedToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/superPropBase.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/superPropBase.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var getPrototypeOf = __webpack_require__(/*! ./getPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js");
function _superPropBase(t, o) {
  for (; !{}.hasOwnProperty.call(t, o) && null !== (t = getPrototypeOf(t)););
  return t;
}
module.exports = _superPropBase, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPrimitive.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPrimitive.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
function toPrimitive(t, r) {
  if ("object" != _typeof(t) || !t) return t;
  var e = t[Symbol.toPrimitive];
  if (void 0 !== e) {
    var i = e.call(t, r || "default");
    if ("object" != _typeof(i)) return i;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return ("string" === r ? String : Number)(t);
}
module.exports = toPrimitive, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPropertyKey.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPropertyKey.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
var toPrimitive = __webpack_require__(/*! ./toPrimitive.js */ "../node_modules/@babel/runtime/helpers/toPrimitive.js");
function toPropertyKey(t) {
  var i = toPrimitive(t, "string");
  return "symbol" == _typeof(i) ? i : i + "";
}
module.exports = toPropertyKey, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/typeof.js":
/*!********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/typeof.js ***!
  \********************************************************/
/***/ ((module) => {

function _typeof(o) {
  "@babel/helpers - typeof";

  return module.exports = _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
    return typeof o;
  } : function (o) {
    return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _typeof(o);
}
module.exports = _typeof, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
/*!****************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _unsupportedIterableToArray(r, a) {
  if (r) {
    if ("string" == typeof r) return arrayLikeToArray(r, a);
    var t = {}.toString.call(r).slice(8, -1);
    return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? arrayLikeToArray(r, a) : void 0;
  }
}
module.exports = _unsupportedIterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/wrapNativeSuper.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/wrapNativeSuper.js ***!
  \*****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var getPrototypeOf = __webpack_require__(/*! ./getPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js");
var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js");
var isNativeFunction = __webpack_require__(/*! ./isNativeFunction.js */ "../node_modules/@babel/runtime/helpers/isNativeFunction.js");
var construct = __webpack_require__(/*! ./construct.js */ "../node_modules/@babel/runtime/helpers/construct.js");
function _wrapNativeSuper(t) {
  var r = "function" == typeof Map ? new Map() : void 0;
  return module.exports = _wrapNativeSuper = function _wrapNativeSuper(t) {
    if (null === t || !isNativeFunction(t)) return t;
    if ("function" != typeof t) throw new TypeError("Super expression must either be null or a function");
    if (void 0 !== r) {
      if (r.has(t)) return r.get(t);
      r.set(t, Wrapper);
    }
    function Wrapper() {
      return construct(t, arguments, getPrototypeOf(this).constructor);
    }
    return Wrapper.prototype = Object.create(t.prototype, {
      constructor: {
        value: Wrapper,
        enumerable: !1,
        writable: !0,
        configurable: !0
      }
    }), setPrototypeOf(Wrapper, t);
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _wrapNativeSuper(t);
}
module.exports = _wrapNativeSuper, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/regenerator/index.js":
/*!***********************************************************!*\
  !*** ../node_modules/@babel/runtime/regenerator/index.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

// TODO(Babel 8): Remove this file.

var runtime = __webpack_require__(/*! ../helpers/regeneratorRuntime */ "../node_modules/@babel/runtime/helpers/regeneratorRuntime.js")();
module.exports = runtime;

// Copied from https://github.com/facebook/regenerator/blob/main/packages/runtime/runtime.js#L736=
try {
  regeneratorRuntime = runtime;
} catch (accidentalStrictMode) {
  if (typeof globalThis === "object") {
    globalThis.regeneratorRuntime = runtime;
  } else {
    Function("r", "regeneratorRuntime = r")(runtime);
  }
}


/***/ }),

/***/ "../node_modules/@reduxjs/toolkit/dist/redux-toolkit.esm.js":
/*!******************************************************************!*\
  !*** ../node_modules/@reduxjs/toolkit/dist/redux-toolkit.esm.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EnhancerArray: () => (/* binding */ EnhancerArray),
/* harmony export */   MiddlewareArray: () => (/* binding */ MiddlewareArray),
/* harmony export */   SHOULD_AUTOBATCH: () => (/* binding */ SHOULD_AUTOBATCH),
/* harmony export */   TaskAbortError: () => (/* binding */ TaskAbortError),
/* harmony export */   __DO_NOT_USE__ActionTypes: () => (/* reexport safe */ redux__WEBPACK_IMPORTED_MODULE_0__.__DO_NOT_USE__ActionTypes),
/* harmony export */   addListener: () => (/* binding */ addListener),
/* harmony export */   applyMiddleware: () => (/* reexport safe */ redux__WEBPACK_IMPORTED_MODULE_0__.applyMiddleware),
/* harmony export */   autoBatchEnhancer: () => (/* binding */ autoBatchEnhancer),
/* harmony export */   bindActionCreators: () => (/* reexport safe */ redux__WEBPACK_IMPORTED_MODULE_0__.bindActionCreators),
/* harmony export */   clearAllListeners: () => (/* binding */ clearAllListeners),
/* harmony export */   combineReducers: () => (/* reexport safe */ redux__WEBPACK_IMPORTED_MODULE_0__.combineReducers),
/* harmony export */   compose: () => (/* reexport safe */ redux__WEBPACK_IMPORTED_MODULE_0__.compose),
/* harmony export */   configureStore: () => (/* binding */ configureStore),
/* harmony export */   createAction: () => (/* binding */ createAction),
/* harmony export */   createActionCreatorInvariantMiddleware: () => (/* binding */ createActionCreatorInvariantMiddleware),
/* harmony export */   createAsyncThunk: () => (/* binding */ createAsyncThunk),
/* harmony export */   createDraftSafeSelector: () => (/* binding */ createDraftSafeSelector),
/* harmony export */   createEntityAdapter: () => (/* binding */ createEntityAdapter),
/* harmony export */   createImmutableStateInvariantMiddleware: () => (/* binding */ createImmutableStateInvariantMiddleware),
/* harmony export */   createListenerMiddleware: () => (/* binding */ createListenerMiddleware),
/* harmony export */   createNextState: () => (/* reexport safe */ immer__WEBPACK_IMPORTED_MODULE_2__["default"]),
/* harmony export */   createReducer: () => (/* binding */ createReducer),
/* harmony export */   createSelector: () => (/* reexport safe */ reselect__WEBPACK_IMPORTED_MODULE_1__.createSelector),
/* harmony export */   createSerializableStateInvariantMiddleware: () => (/* binding */ createSerializableStateInvariantMiddleware),
/* harmony export */   createSlice: () => (/* binding */ createSlice),
/* harmony export */   createStore: () => (/* reexport safe */ redux__WEBPACK_IMPORTED_MODULE_0__.createStore),
/* harmony export */   current: () => (/* reexport safe */ immer__WEBPACK_IMPORTED_MODULE_2__.current),
/* harmony export */   findNonSerializableValue: () => (/* binding */ findNonSerializableValue),
/* harmony export */   freeze: () => (/* reexport safe */ immer__WEBPACK_IMPORTED_MODULE_2__.freeze),
/* harmony export */   getDefaultMiddleware: () => (/* binding */ getDefaultMiddleware),
/* harmony export */   getType: () => (/* binding */ getType),
/* harmony export */   isAction: () => (/* binding */ isAction),
/* harmony export */   isActionCreator: () => (/* binding */ isActionCreator),
/* harmony export */   isAllOf: () => (/* binding */ isAllOf),
/* harmony export */   isAnyOf: () => (/* binding */ isAnyOf),
/* harmony export */   isAsyncThunkAction: () => (/* binding */ isAsyncThunkAction),
/* harmony export */   isDraft: () => (/* reexport safe */ immer__WEBPACK_IMPORTED_MODULE_2__.isDraft),
/* harmony export */   isFluxStandardAction: () => (/* binding */ isFSA),
/* harmony export */   isFulfilled: () => (/* binding */ isFulfilled),
/* harmony export */   isImmutableDefault: () => (/* binding */ isImmutableDefault),
/* harmony export */   isPending: () => (/* binding */ isPending),
/* harmony export */   isPlain: () => (/* binding */ isPlain),
/* harmony export */   isPlainObject: () => (/* binding */ isPlainObject),
/* harmony export */   isRejected: () => (/* binding */ isRejected),
/* harmony export */   isRejectedWithValue: () => (/* binding */ isRejectedWithValue),
/* harmony export */   legacy_createStore: () => (/* reexport safe */ redux__WEBPACK_IMPORTED_MODULE_0__.legacy_createStore),
/* harmony export */   miniSerializeError: () => (/* binding */ miniSerializeError),
/* harmony export */   nanoid: () => (/* binding */ nanoid),
/* harmony export */   original: () => (/* reexport safe */ immer__WEBPACK_IMPORTED_MODULE_2__.original),
/* harmony export */   prepareAutoBatched: () => (/* binding */ prepareAutoBatched),
/* harmony export */   removeListener: () => (/* binding */ removeListener),
/* harmony export */   unwrapResult: () => (/* binding */ unwrapResult)
/* harmony export */ });
/* harmony import */ var immer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! immer */ "../node_modules/immer/dist/immer.esm.mjs");
/* harmony import */ var redux__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! redux */ "../node_modules/redux/es/redux.js");
/* harmony import */ var reselect__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! reselect */ "../node_modules/reselect/es/index.js");
/* harmony import */ var redux_thunk__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! redux-thunk */ "../node_modules/redux-thunk/es/index.js");
var __extends = (undefined && undefined.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        if (typeof b !== "function" && b !== null)
            throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var __generator = (undefined && undefined.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
            if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [op[0] & 2, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
var __spreadArray = (undefined && undefined.__spreadArray) || function (to, from) {
    for (var i = 0, il = from.length, j = to.length; i < il; i++, j++)
        to[j] = from[i];
    return to;
};
var __defProp = Object.defineProperty;
var __defProps = Object.defineProperties;
var __getOwnPropDescs = Object.getOwnPropertyDescriptors;
var __getOwnPropSymbols = Object.getOwnPropertySymbols;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __propIsEnum = Object.prototype.propertyIsEnumerable;
var __defNormalProp = function (obj, key, value) { return key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value: value }) : obj[key] = value; };
var __spreadValues = function (a, b) {
    for (var prop in b || (b = {}))
        if (__hasOwnProp.call(b, prop))
            __defNormalProp(a, prop, b[prop]);
    if (__getOwnPropSymbols)
        for (var _i = 0, _c = __getOwnPropSymbols(b); _i < _c.length; _i++) {
            var prop = _c[_i];
            if (__propIsEnum.call(b, prop))
                __defNormalProp(a, prop, b[prop]);
        }
    return a;
};
var __spreadProps = function (a, b) { return __defProps(a, __getOwnPropDescs(b)); };
var __async = function (__this, __arguments, generator) {
    return new Promise(function (resolve, reject) {
        var fulfilled = function (value) {
            try {
                step(generator.next(value));
            }
            catch (e) {
                reject(e);
            }
        };
        var rejected = function (value) {
            try {
                step(generator.throw(value));
            }
            catch (e) {
                reject(e);
            }
        };
        var step = function (x) { return x.done ? resolve(x.value) : Promise.resolve(x.value).then(fulfilled, rejected); };
        step((generator = generator.apply(__this, __arguments)).next());
    });
};
// src/index.ts




// src/createDraftSafeSelector.ts


var createDraftSafeSelector = function () {
    var args = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        args[_i] = arguments[_i];
    }
    var selector = reselect__WEBPACK_IMPORTED_MODULE_1__.createSelector.apply(void 0, args);
    var wrappedSelector = function (value) {
        var rest = [];
        for (var _i = 1; _i < arguments.length; _i++) {
            rest[_i - 1] = arguments[_i];
        }
        return selector.apply(void 0, __spreadArray([(0,immer__WEBPACK_IMPORTED_MODULE_2__.isDraft)(value) ? (0,immer__WEBPACK_IMPORTED_MODULE_2__.current)(value) : value], rest));
    };
    return wrappedSelector;
};
// src/configureStore.ts

// src/devtoolsExtension.ts

var composeWithDevTools = typeof window !== "undefined" && window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ ? window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ : function () {
    if (arguments.length === 0)
        return void 0;
    if (typeof arguments[0] === "object")
        return redux__WEBPACK_IMPORTED_MODULE_0__.compose;
    return redux__WEBPACK_IMPORTED_MODULE_0__.compose.apply(null, arguments);
};
var devToolsEnhancer = typeof window !== "undefined" && window.__REDUX_DEVTOOLS_EXTENSION__ ? window.__REDUX_DEVTOOLS_EXTENSION__ : function () {
    return function (noop2) {
        return noop2;
    };
};
// src/isPlainObject.ts
function isPlainObject(value) {
    if (typeof value !== "object" || value === null)
        return false;
    var proto = Object.getPrototypeOf(value);
    if (proto === null)
        return true;
    var baseProto = proto;
    while (Object.getPrototypeOf(baseProto) !== null) {
        baseProto = Object.getPrototypeOf(baseProto);
    }
    return proto === baseProto;
}
// src/getDefaultMiddleware.ts

// src/tsHelpers.ts
var hasMatchFunction = function (v) {
    return v && typeof v.match === "function";
};
// src/createAction.ts
function createAction(type, prepareAction) {
    function actionCreator() {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        if (prepareAction) {
            var prepared = prepareAction.apply(void 0, args);
            if (!prepared) {
                throw new Error("prepareAction did not return an object");
            }
            return __spreadValues(__spreadValues({
                type: type,
                payload: prepared.payload
            }, "meta" in prepared && { meta: prepared.meta }), "error" in prepared && { error: prepared.error });
        }
        return { type: type, payload: args[0] };
    }
    actionCreator.toString = function () { return "" + type; };
    actionCreator.type = type;
    actionCreator.match = function (action) { return action.type === type; };
    return actionCreator;
}
function isAction(action) {
    return isPlainObject(action) && "type" in action;
}
function isActionCreator(action) {
    return typeof action === "function" && "type" in action && hasMatchFunction(action);
}
function isFSA(action) {
    return isAction(action) && typeof action.type === "string" && Object.keys(action).every(isValidKey);
}
function isValidKey(key) {
    return ["type", "payload", "error", "meta"].indexOf(key) > -1;
}
function getType(actionCreator) {
    return "" + actionCreator;
}
// src/actionCreatorInvariantMiddleware.ts
function getMessage(type) {
    var splitType = type ? ("" + type).split("/") : [];
    var actionName = splitType[splitType.length - 1] || "actionCreator";
    return "Detected an action creator with type \"" + (type || "unknown") + "\" being dispatched. \nMake sure you're calling the action creator before dispatching, i.e. `dispatch(" + actionName + "())` instead of `dispatch(" + actionName + ")`. This is necessary even if the action has no payload.";
}
function createActionCreatorInvariantMiddleware(options) {
    if (options === void 0) { options = {}; }
    if (false) // removed by dead control flow
{}
    var _c = options.isActionCreator, isActionCreator2 = _c === void 0 ? isActionCreator : _c;
    return function () { return function (next) { return function (action) {
        if (isActionCreator2(action)) {
            console.warn(getMessage(action.type));
        }
        return next(action);
    }; }; };
}
// src/utils.ts

function getTimeMeasureUtils(maxDelay, fnName) {
    var elapsed = 0;
    return {
        measureTime: function (fn) {
            var started = Date.now();
            try {
                return fn();
            }
            finally {
                var finished = Date.now();
                elapsed += finished - started;
            }
        },
        warnIfExceeded: function () {
            if (elapsed > maxDelay) {
                console.warn(fnName + " took " + elapsed + "ms, which is more than the warning threshold of " + maxDelay + "ms. \nIf your state or actions are very large, you may want to disable the middleware as it might cause too much of a slowdown in development mode. See https://redux-toolkit.js.org/api/getDefaultMiddleware for instructions.\nIt is disabled in production builds, so you don't need to worry about that.");
            }
        }
    };
}
var MiddlewareArray = /** @class */ (function (_super) {
    __extends(MiddlewareArray, _super);
    function MiddlewareArray() {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        var _this = _super.apply(this, args) || this;
        Object.setPrototypeOf(_this, MiddlewareArray.prototype);
        return _this;
    }
    Object.defineProperty(MiddlewareArray, Symbol.species, {
        get: function () {
            return MiddlewareArray;
        },
        enumerable: false,
        configurable: true
    });
    MiddlewareArray.prototype.concat = function () {
        var arr = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            arr[_i] = arguments[_i];
        }
        return _super.prototype.concat.apply(this, arr);
    };
    MiddlewareArray.prototype.prepend = function () {
        var arr = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            arr[_i] = arguments[_i];
        }
        if (arr.length === 1 && Array.isArray(arr[0])) {
            return new (MiddlewareArray.bind.apply(MiddlewareArray, __spreadArray([void 0], arr[0].concat(this))))();
        }
        return new (MiddlewareArray.bind.apply(MiddlewareArray, __spreadArray([void 0], arr.concat(this))))();
    };
    return MiddlewareArray;
}(Array));
var EnhancerArray = /** @class */ (function (_super) {
    __extends(EnhancerArray, _super);
    function EnhancerArray() {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        var _this = _super.apply(this, args) || this;
        Object.setPrototypeOf(_this, EnhancerArray.prototype);
        return _this;
    }
    Object.defineProperty(EnhancerArray, Symbol.species, {
        get: function () {
            return EnhancerArray;
        },
        enumerable: false,
        configurable: true
    });
    EnhancerArray.prototype.concat = function () {
        var arr = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            arr[_i] = arguments[_i];
        }
        return _super.prototype.concat.apply(this, arr);
    };
    EnhancerArray.prototype.prepend = function () {
        var arr = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            arr[_i] = arguments[_i];
        }
        if (arr.length === 1 && Array.isArray(arr[0])) {
            return new (EnhancerArray.bind.apply(EnhancerArray, __spreadArray([void 0], arr[0].concat(this))))();
        }
        return new (EnhancerArray.bind.apply(EnhancerArray, __spreadArray([void 0], arr.concat(this))))();
    };
    return EnhancerArray;
}(Array));
function freezeDraftable(val) {
    return (0,immer__WEBPACK_IMPORTED_MODULE_2__.isDraftable)(val) ? (0,immer__WEBPACK_IMPORTED_MODULE_2__["default"])(val, function () {
    }) : val;
}
// src/immutableStateInvariantMiddleware.ts
var isProduction = "development" === "production";
var prefix = "Invariant failed";
function invariant(condition, message) {
    if (condition) {
        return;
    }
    if (isProduction) {
        throw new Error(prefix);
    }
    throw new Error(prefix + ": " + (message || ""));
}
function stringify(obj, serializer, indent, decycler) {
    return JSON.stringify(obj, getSerialize(serializer, decycler), indent);
}
function getSerialize(serializer, decycler) {
    var stack = [], keys = [];
    if (!decycler)
        decycler = function (_, value) {
            if (stack[0] === value)
                return "[Circular ~]";
            return "[Circular ~." + keys.slice(0, stack.indexOf(value)).join(".") + "]";
        };
    return function (key, value) {
        if (stack.length > 0) {
            var thisPos = stack.indexOf(this);
            ~thisPos ? stack.splice(thisPos + 1) : stack.push(this);
            ~thisPos ? keys.splice(thisPos, Infinity, key) : keys.push(key);
            if (~stack.indexOf(value))
                value = decycler.call(this, key, value);
        }
        else
            stack.push(value);
        return serializer == null ? value : serializer.call(this, key, value);
    };
}
function isImmutableDefault(value) {
    return typeof value !== "object" || value == null || Object.isFrozen(value);
}
function trackForMutations(isImmutable, ignorePaths, obj) {
    var trackedProperties = trackProperties(isImmutable, ignorePaths, obj);
    return {
        detectMutations: function () {
            return detectMutations(isImmutable, ignorePaths, trackedProperties, obj);
        }
    };
}
function trackProperties(isImmutable, ignorePaths, obj, path, checkedObjects) {
    if (ignorePaths === void 0) { ignorePaths = []; }
    if (path === void 0) { path = ""; }
    if (checkedObjects === void 0) { checkedObjects = new Set(); }
    var tracked = { value: obj };
    if (!isImmutable(obj) && !checkedObjects.has(obj)) {
        checkedObjects.add(obj);
        tracked.children = {};
        for (var key in obj) {
            var childPath = path ? path + "." + key : key;
            if (ignorePaths.length && ignorePaths.indexOf(childPath) !== -1) {
                continue;
            }
            tracked.children[key] = trackProperties(isImmutable, ignorePaths, obj[key], childPath);
        }
    }
    return tracked;
}
function detectMutations(isImmutable, ignoredPaths, trackedProperty, obj, sameParentRef, path) {
    if (ignoredPaths === void 0) { ignoredPaths = []; }
    if (sameParentRef === void 0) { sameParentRef = false; }
    if (path === void 0) { path = ""; }
    var prevObj = trackedProperty ? trackedProperty.value : void 0;
    var sameRef = prevObj === obj;
    if (sameParentRef && !sameRef && !Number.isNaN(obj)) {
        return { wasMutated: true, path: path };
    }
    if (isImmutable(prevObj) || isImmutable(obj)) {
        return { wasMutated: false };
    }
    var keysToDetect = {};
    for (var key in trackedProperty.children) {
        keysToDetect[key] = true;
    }
    for (var key in obj) {
        keysToDetect[key] = true;
    }
    var hasIgnoredPaths = ignoredPaths.length > 0;
    var _loop_1 = function (key) {
        var nestedPath = path ? path + "." + key : key;
        if (hasIgnoredPaths) {
            var hasMatches = ignoredPaths.some(function (ignored) {
                if (ignored instanceof RegExp) {
                    return ignored.test(nestedPath);
                }
                return nestedPath === ignored;
            });
            if (hasMatches) {
                return "continue";
            }
        }
        var result = detectMutations(isImmutable, ignoredPaths, trackedProperty.children[key], obj[key], sameRef, nestedPath);
        if (result.wasMutated) {
            return { value: result };
        }
    };
    for (var key in keysToDetect) {
        var state_1 = _loop_1(key);
        if (typeof state_1 === "object")
            return state_1.value;
    }
    return { wasMutated: false };
}
function createImmutableStateInvariantMiddleware(options) {
    if (options === void 0) { options = {}; }
    if (false) // removed by dead control flow
{}
    var _c = options.isImmutable, isImmutable = _c === void 0 ? isImmutableDefault : _c, ignoredPaths = options.ignoredPaths, _d = options.warnAfter, warnAfter = _d === void 0 ? 32 : _d, ignore = options.ignore;
    ignoredPaths = ignoredPaths || ignore;
    var track = trackForMutations.bind(null, isImmutable, ignoredPaths);
    return function (_c) {
        var getState = _c.getState;
        var state = getState();
        var tracker = track(state);
        var result;
        return function (next) { return function (action) {
            var measureUtils = getTimeMeasureUtils(warnAfter, "ImmutableStateInvariantMiddleware");
            measureUtils.measureTime(function () {
                state = getState();
                result = tracker.detectMutations();
                tracker = track(state);
                invariant(!result.wasMutated, "A state mutation was detected between dispatches, in the path '" + (result.path || "") + "'.  This may cause incorrect behavior. (https://redux.js.org/style-guide/style-guide#do-not-mutate-state)");
            });
            var dispatchedAction = next(action);
            measureUtils.measureTime(function () {
                state = getState();
                result = tracker.detectMutations();
                tracker = track(state);
                result.wasMutated && invariant(!result.wasMutated, "A state mutation was detected inside a dispatch, in the path: " + (result.path || "") + ". Take a look at the reducer(s) handling the action " + stringify(action) + ". (https://redux.js.org/style-guide/style-guide#do-not-mutate-state)");
            });
            measureUtils.warnIfExceeded();
            return dispatchedAction;
        }; };
    };
}
// src/serializableStateInvariantMiddleware.ts
function isPlain(val) {
    var type = typeof val;
    return val == null || type === "string" || type === "boolean" || type === "number" || Array.isArray(val) || isPlainObject(val);
}
function findNonSerializableValue(value, path, isSerializable, getEntries, ignoredPaths, cache) {
    if (path === void 0) { path = ""; }
    if (isSerializable === void 0) { isSerializable = isPlain; }
    if (ignoredPaths === void 0) { ignoredPaths = []; }
    var foundNestedSerializable;
    if (!isSerializable(value)) {
        return {
            keyPath: path || "<root>",
            value: value
        };
    }
    if (typeof value !== "object" || value === null) {
        return false;
    }
    if (cache == null ? void 0 : cache.has(value))
        return false;
    var entries = getEntries != null ? getEntries(value) : Object.entries(value);
    var hasIgnoredPaths = ignoredPaths.length > 0;
    var _loop_2 = function (key, nestedValue) {
        var nestedPath = path ? path + "." + key : key;
        if (hasIgnoredPaths) {
            var hasMatches = ignoredPaths.some(function (ignored) {
                if (ignored instanceof RegExp) {
                    return ignored.test(nestedPath);
                }
                return nestedPath === ignored;
            });
            if (hasMatches) {
                return "continue";
            }
        }
        if (!isSerializable(nestedValue)) {
            return { value: {
                    keyPath: nestedPath,
                    value: nestedValue
                } };
        }
        if (typeof nestedValue === "object") {
            foundNestedSerializable = findNonSerializableValue(nestedValue, nestedPath, isSerializable, getEntries, ignoredPaths, cache);
            if (foundNestedSerializable) {
                return { value: foundNestedSerializable };
            }
        }
    };
    for (var _i = 0, entries_1 = entries; _i < entries_1.length; _i++) {
        var _c = entries_1[_i], key = _c[0], nestedValue = _c[1];
        var state_2 = _loop_2(key, nestedValue);
        if (typeof state_2 === "object")
            return state_2.value;
    }
    if (cache && isNestedFrozen(value))
        cache.add(value);
    return false;
}
function isNestedFrozen(value) {
    if (!Object.isFrozen(value))
        return false;
    for (var _i = 0, _c = Object.values(value); _i < _c.length; _i++) {
        var nestedValue = _c[_i];
        if (typeof nestedValue !== "object" || nestedValue === null)
            continue;
        if (!isNestedFrozen(nestedValue))
            return false;
    }
    return true;
}
function createSerializableStateInvariantMiddleware(options) {
    if (options === void 0) { options = {}; }
    if (false) // removed by dead control flow
{}
    var _c = options.isSerializable, isSerializable = _c === void 0 ? isPlain : _c, getEntries = options.getEntries, _d = options.ignoredActions, ignoredActions = _d === void 0 ? [] : _d, _e = options.ignoredActionPaths, ignoredActionPaths = _e === void 0 ? ["meta.arg", "meta.baseQueryMeta"] : _e, _f = options.ignoredPaths, ignoredPaths = _f === void 0 ? [] : _f, _g = options.warnAfter, warnAfter = _g === void 0 ? 32 : _g, _h = options.ignoreState, ignoreState = _h === void 0 ? false : _h, _j = options.ignoreActions, ignoreActions = _j === void 0 ? false : _j, _k = options.disableCache, disableCache = _k === void 0 ? false : _k;
    var cache = !disableCache && WeakSet ? new WeakSet() : void 0;
    return function (storeAPI) { return function (next) { return function (action) {
        var result = next(action);
        var measureUtils = getTimeMeasureUtils(warnAfter, "SerializableStateInvariantMiddleware");
        if (!ignoreActions && !(ignoredActions.length && ignoredActions.indexOf(action.type) !== -1)) {
            measureUtils.measureTime(function () {
                var foundActionNonSerializableValue = findNonSerializableValue(action, "", isSerializable, getEntries, ignoredActionPaths, cache);
                if (foundActionNonSerializableValue) {
                    var keyPath = foundActionNonSerializableValue.keyPath, value = foundActionNonSerializableValue.value;
                    console.error("A non-serializable value was detected in an action, in the path: `" + keyPath + "`. Value:", value, "\nTake a look at the logic that dispatched this action: ", action, "\n(See https://redux.js.org/faq/actions#why-should-type-be-a-string-or-at-least-serializable-why-should-my-action-types-be-constants)", "\n(To allow non-serializable values see: https://redux-toolkit.js.org/usage/usage-guide#working-with-non-serializable-data)");
                }
            });
        }
        if (!ignoreState) {
            measureUtils.measureTime(function () {
                var state = storeAPI.getState();
                var foundStateNonSerializableValue = findNonSerializableValue(state, "", isSerializable, getEntries, ignoredPaths, cache);
                if (foundStateNonSerializableValue) {
                    var keyPath = foundStateNonSerializableValue.keyPath, value = foundStateNonSerializableValue.value;
                    console.error("A non-serializable value was detected in the state, in the path: `" + keyPath + "`. Value:", value, "\nTake a look at the reducer(s) handling this action type: " + action.type + ".\n(See https://redux.js.org/faq/organizing-state#can-i-put-functions-promises-or-other-non-serializable-items-in-my-store-state)");
                }
            });
            measureUtils.warnIfExceeded();
        }
        return result;
    }; }; };
}
// src/getDefaultMiddleware.ts
function isBoolean(x) {
    return typeof x === "boolean";
}
function curryGetDefaultMiddleware() {
    return function curriedGetDefaultMiddleware(options) {
        return getDefaultMiddleware(options);
    };
}
function getDefaultMiddleware(options) {
    if (options === void 0) { options = {}; }
    var _c = options.thunk, thunk = _c === void 0 ? true : _c, _d = options.immutableCheck, immutableCheck = _d === void 0 ? true : _d, _e = options.serializableCheck, serializableCheck = _e === void 0 ? true : _e, _f = options.actionCreatorCheck, actionCreatorCheck = _f === void 0 ? true : _f;
    var middlewareArray = new MiddlewareArray();
    if (thunk) {
        if (isBoolean(thunk)) {
            middlewareArray.push(redux_thunk__WEBPACK_IMPORTED_MODULE_3__["default"]);
        }
        else {
            middlewareArray.push(redux_thunk__WEBPACK_IMPORTED_MODULE_3__["default"].withExtraArgument(thunk.extraArgument));
        }
    }
    if (true) {
        if (immutableCheck) {
            var immutableOptions = {};
            if (!isBoolean(immutableCheck)) {
                immutableOptions = immutableCheck;
            }
            middlewareArray.unshift(createImmutableStateInvariantMiddleware(immutableOptions));
        }
        if (serializableCheck) {
            var serializableOptions = {};
            if (!isBoolean(serializableCheck)) {
                serializableOptions = serializableCheck;
            }
            middlewareArray.push(createSerializableStateInvariantMiddleware(serializableOptions));
        }
        if (actionCreatorCheck) {
            var actionCreatorOptions = {};
            if (!isBoolean(actionCreatorCheck)) {
                actionCreatorOptions = actionCreatorCheck;
            }
            middlewareArray.unshift(createActionCreatorInvariantMiddleware(actionCreatorOptions));
        }
    }
    return middlewareArray;
}
// src/configureStore.ts
var IS_PRODUCTION = "development" === "production";
function configureStore(options) {
    var curriedGetDefaultMiddleware = curryGetDefaultMiddleware();
    var _c = options || {}, _d = _c.reducer, reducer = _d === void 0 ? void 0 : _d, _e = _c.middleware, middleware = _e === void 0 ? curriedGetDefaultMiddleware() : _e, _f = _c.devTools, devTools = _f === void 0 ? true : _f, _g = _c.preloadedState, preloadedState = _g === void 0 ? void 0 : _g, _h = _c.enhancers, enhancers = _h === void 0 ? void 0 : _h;
    var rootReducer;
    if (typeof reducer === "function") {
        rootReducer = reducer;
    }
    else if (isPlainObject(reducer)) {
        rootReducer = (0,redux__WEBPACK_IMPORTED_MODULE_0__.combineReducers)(reducer);
    }
    else {
        throw new Error('"reducer" is a required argument, and must be a function or an object of functions that can be passed to combineReducers');
    }
    var finalMiddleware = middleware;
    if (typeof finalMiddleware === "function") {
        finalMiddleware = finalMiddleware(curriedGetDefaultMiddleware);
        if (!IS_PRODUCTION && !Array.isArray(finalMiddleware)) {
            throw new Error("when using a middleware builder function, an array of middleware must be returned");
        }
    }
    if (!IS_PRODUCTION && finalMiddleware.some(function (item) { return typeof item !== "function"; })) {
        throw new Error("each middleware provided to configureStore must be a function");
    }
    var middlewareEnhancer = redux__WEBPACK_IMPORTED_MODULE_0__.applyMiddleware.apply(void 0, finalMiddleware);
    var finalCompose = redux__WEBPACK_IMPORTED_MODULE_0__.compose;
    if (devTools) {
        finalCompose = composeWithDevTools(__spreadValues({
            trace: !IS_PRODUCTION
        }, typeof devTools === "object" && devTools));
    }
    var defaultEnhancers = new EnhancerArray(middlewareEnhancer);
    var storeEnhancers = defaultEnhancers;
    if (Array.isArray(enhancers)) {
        storeEnhancers = __spreadArray([middlewareEnhancer], enhancers);
    }
    else if (typeof enhancers === "function") {
        storeEnhancers = enhancers(defaultEnhancers);
    }
    var composedEnhancer = finalCompose.apply(void 0, storeEnhancers);
    return (0,redux__WEBPACK_IMPORTED_MODULE_0__.createStore)(rootReducer, preloadedState, composedEnhancer);
}
// src/createReducer.ts

// src/mapBuilders.ts
function executeReducerBuilderCallback(builderCallback) {
    var actionsMap = {};
    var actionMatchers = [];
    var defaultCaseReducer;
    var builder = {
        addCase: function (typeOrActionCreator, reducer) {
            if (true) {
                if (actionMatchers.length > 0) {
                    throw new Error("`builder.addCase` should only be called before calling `builder.addMatcher`");
                }
                if (defaultCaseReducer) {
                    throw new Error("`builder.addCase` should only be called before calling `builder.addDefaultCase`");
                }
            }
            var type = typeof typeOrActionCreator === "string" ? typeOrActionCreator : typeOrActionCreator.type;
            if (!type) {
                throw new Error("`builder.addCase` cannot be called with an empty action type");
            }
            if (type in actionsMap) {
                throw new Error("`builder.addCase` cannot be called with two reducers for the same action type");
            }
            actionsMap[type] = reducer;
            return builder;
        },
        addMatcher: function (matcher, reducer) {
            if (true) {
                if (defaultCaseReducer) {
                    throw new Error("`builder.addMatcher` should only be called before calling `builder.addDefaultCase`");
                }
            }
            actionMatchers.push({ matcher: matcher, reducer: reducer });
            return builder;
        },
        addDefaultCase: function (reducer) {
            if (true) {
                if (defaultCaseReducer) {
                    throw new Error("`builder.addDefaultCase` can only be called once");
                }
            }
            defaultCaseReducer = reducer;
            return builder;
        }
    };
    builderCallback(builder);
    return [actionsMap, actionMatchers, defaultCaseReducer];
}
// src/createReducer.ts
function isStateFunction(x) {
    return typeof x === "function";
}
var hasWarnedAboutObjectNotation = false;
function createReducer(initialState, mapOrBuilderCallback, actionMatchers, defaultCaseReducer) {
    if (actionMatchers === void 0) { actionMatchers = []; }
    if (true) {
        if (typeof mapOrBuilderCallback === "object") {
            if (!hasWarnedAboutObjectNotation) {
                hasWarnedAboutObjectNotation = true;
                console.warn("The object notation for `createReducer` is deprecated, and will be removed in RTK 2.0. Please use the 'builder callback' notation instead: https://redux-toolkit.js.org/api/createReducer");
            }
        }
    }
    var _c = typeof mapOrBuilderCallback === "function" ? executeReducerBuilderCallback(mapOrBuilderCallback) : [mapOrBuilderCallback, actionMatchers, defaultCaseReducer], actionsMap = _c[0], finalActionMatchers = _c[1], finalDefaultCaseReducer = _c[2];
    var getInitialState;
    if (isStateFunction(initialState)) {
        getInitialState = function () { return freezeDraftable(initialState()); };
    }
    else {
        var frozenInitialState_1 = freezeDraftable(initialState);
        getInitialState = function () { return frozenInitialState_1; };
    }
    function reducer(state, action) {
        if (state === void 0) { state = getInitialState(); }
        var caseReducers = __spreadArray([
            actionsMap[action.type]
        ], finalActionMatchers.filter(function (_c) {
            var matcher = _c.matcher;
            return matcher(action);
        }).map(function (_c) {
            var reducer2 = _c.reducer;
            return reducer2;
        }));
        if (caseReducers.filter(function (cr) { return !!cr; }).length === 0) {
            caseReducers = [finalDefaultCaseReducer];
        }
        return caseReducers.reduce(function (previousState, caseReducer) {
            if (caseReducer) {
                if ((0,immer__WEBPACK_IMPORTED_MODULE_2__.isDraft)(previousState)) {
                    var draft = previousState;
                    var result = caseReducer(draft, action);
                    if (result === void 0) {
                        return previousState;
                    }
                    return result;
                }
                else if (!(0,immer__WEBPACK_IMPORTED_MODULE_2__.isDraftable)(previousState)) {
                    var result = caseReducer(previousState, action);
                    if (result === void 0) {
                        if (previousState === null) {
                            return previousState;
                        }
                        throw Error("A case reducer on a non-draftable value must not return undefined");
                    }
                    return result;
                }
                else {
                    return (0,immer__WEBPACK_IMPORTED_MODULE_2__["default"])(previousState, function (draft) {
                        return caseReducer(draft, action);
                    });
                }
            }
            return previousState;
        }, state);
    }
    reducer.getInitialState = getInitialState;
    return reducer;
}
// src/createSlice.ts
var hasWarnedAboutObjectNotation2 = false;
function getType2(slice, actionKey) {
    return slice + "/" + actionKey;
}
function createSlice(options) {
    var name = options.name;
    if (!name) {
        throw new Error("`name` is a required option for createSlice");
    }
    if (typeof process !== "undefined" && "development" === "development") {
        if (options.initialState === void 0) {
            console.error("You must provide an `initialState` value that is not `undefined`. You may have misspelled `initialState`");
        }
    }
    var initialState = typeof options.initialState == "function" ? options.initialState : freezeDraftable(options.initialState);
    var reducers = options.reducers || {};
    var reducerNames = Object.keys(reducers);
    var sliceCaseReducersByName = {};
    var sliceCaseReducersByType = {};
    var actionCreators = {};
    reducerNames.forEach(function (reducerName) {
        var maybeReducerWithPrepare = reducers[reducerName];
        var type = getType2(name, reducerName);
        var caseReducer;
        var prepareCallback;
        if ("reducer" in maybeReducerWithPrepare) {
            caseReducer = maybeReducerWithPrepare.reducer;
            prepareCallback = maybeReducerWithPrepare.prepare;
        }
        else {
            caseReducer = maybeReducerWithPrepare;
        }
        sliceCaseReducersByName[reducerName] = caseReducer;
        sliceCaseReducersByType[type] = caseReducer;
        actionCreators[reducerName] = prepareCallback ? createAction(type, prepareCallback) : createAction(type);
    });
    function buildReducer() {
        if (true) {
            if (typeof options.extraReducers === "object") {
                if (!hasWarnedAboutObjectNotation2) {
                    hasWarnedAboutObjectNotation2 = true;
                    console.warn("The object notation for `createSlice.extraReducers` is deprecated, and will be removed in RTK 2.0. Please use the 'builder callback' notation instead: https://redux-toolkit.js.org/api/createSlice");
                }
            }
        }
        var _c = typeof options.extraReducers === "function" ? executeReducerBuilderCallback(options.extraReducers) : [options.extraReducers], _d = _c[0], extraReducers = _d === void 0 ? {} : _d, _e = _c[1], actionMatchers = _e === void 0 ? [] : _e, _f = _c[2], defaultCaseReducer = _f === void 0 ? void 0 : _f;
        var finalCaseReducers = __spreadValues(__spreadValues({}, extraReducers), sliceCaseReducersByType);
        return createReducer(initialState, function (builder) {
            for (var key in finalCaseReducers) {
                builder.addCase(key, finalCaseReducers[key]);
            }
            for (var _i = 0, actionMatchers_1 = actionMatchers; _i < actionMatchers_1.length; _i++) {
                var m = actionMatchers_1[_i];
                builder.addMatcher(m.matcher, m.reducer);
            }
            if (defaultCaseReducer) {
                builder.addDefaultCase(defaultCaseReducer);
            }
        });
    }
    var _reducer;
    return {
        name: name,
        reducer: function (state, action) {
            if (!_reducer)
                _reducer = buildReducer();
            return _reducer(state, action);
        },
        actions: actionCreators,
        caseReducers: sliceCaseReducersByName,
        getInitialState: function () {
            if (!_reducer)
                _reducer = buildReducer();
            return _reducer.getInitialState();
        }
    };
}
// src/entities/entity_state.ts
function getInitialEntityState() {
    return {
        ids: [],
        entities: {}
    };
}
function createInitialStateFactory() {
    function getInitialState(additionalState) {
        if (additionalState === void 0) { additionalState = {}; }
        return Object.assign(getInitialEntityState(), additionalState);
    }
    return { getInitialState: getInitialState };
}
// src/entities/state_selectors.ts
function createSelectorsFactory() {
    function getSelectors(selectState) {
        var selectIds = function (state) { return state.ids; };
        var selectEntities = function (state) { return state.entities; };
        var selectAll = createDraftSafeSelector(selectIds, selectEntities, function (ids, entities) { return ids.map(function (id) { return entities[id]; }); });
        var selectId = function (_, id) { return id; };
        var selectById = function (entities, id) { return entities[id]; };
        var selectTotal = createDraftSafeSelector(selectIds, function (ids) { return ids.length; });
        if (!selectState) {
            return {
                selectIds: selectIds,
                selectEntities: selectEntities,
                selectAll: selectAll,
                selectTotal: selectTotal,
                selectById: createDraftSafeSelector(selectEntities, selectId, selectById)
            };
        }
        var selectGlobalizedEntities = createDraftSafeSelector(selectState, selectEntities);
        return {
            selectIds: createDraftSafeSelector(selectState, selectIds),
            selectEntities: selectGlobalizedEntities,
            selectAll: createDraftSafeSelector(selectState, selectAll),
            selectTotal: createDraftSafeSelector(selectState, selectTotal),
            selectById: createDraftSafeSelector(selectGlobalizedEntities, selectId, selectById)
        };
    }
    return { getSelectors: getSelectors };
}
// src/entities/state_adapter.ts

function createSingleArgumentStateOperator(mutator) {
    var operator = createStateOperator(function (_, state) { return mutator(state); });
    return function operation(state) {
        return operator(state, void 0);
    };
}
function createStateOperator(mutator) {
    return function operation(state, arg) {
        function isPayloadActionArgument(arg2) {
            return isFSA(arg2);
        }
        var runMutator = function (draft) {
            if (isPayloadActionArgument(arg)) {
                mutator(arg.payload, draft);
            }
            else {
                mutator(arg, draft);
            }
        };
        if ((0,immer__WEBPACK_IMPORTED_MODULE_2__.isDraft)(state)) {
            runMutator(state);
            return state;
        }
        else {
            return (0,immer__WEBPACK_IMPORTED_MODULE_2__["default"])(state, runMutator);
        }
    };
}
// src/entities/utils.ts
function selectIdValue(entity, selectId) {
    var key = selectId(entity);
    if ( true && key === void 0) {
        console.warn("The entity passed to the `selectId` implementation returned undefined.", "You should probably provide your own `selectId` implementation.", "The entity that was passed:", entity, "The `selectId` implementation:", selectId.toString());
    }
    return key;
}
function ensureEntitiesArray(entities) {
    if (!Array.isArray(entities)) {
        entities = Object.values(entities);
    }
    return entities;
}
function splitAddedUpdatedEntities(newEntities, selectId, state) {
    newEntities = ensureEntitiesArray(newEntities);
    var added = [];
    var updated = [];
    for (var _i = 0, newEntities_1 = newEntities; _i < newEntities_1.length; _i++) {
        var entity = newEntities_1[_i];
        var id = selectIdValue(entity, selectId);
        if (id in state.entities) {
            updated.push({ id: id, changes: entity });
        }
        else {
            added.push(entity);
        }
    }
    return [added, updated];
}
// src/entities/unsorted_state_adapter.ts
function createUnsortedStateAdapter(selectId) {
    function addOneMutably(entity, state) {
        var key = selectIdValue(entity, selectId);
        if (key in state.entities) {
            return;
        }
        state.ids.push(key);
        state.entities[key] = entity;
    }
    function addManyMutably(newEntities, state) {
        newEntities = ensureEntitiesArray(newEntities);
        for (var _i = 0, newEntities_2 = newEntities; _i < newEntities_2.length; _i++) {
            var entity = newEntities_2[_i];
            addOneMutably(entity, state);
        }
    }
    function setOneMutably(entity, state) {
        var key = selectIdValue(entity, selectId);
        if (!(key in state.entities)) {
            state.ids.push(key);
        }
        state.entities[key] = entity;
    }
    function setManyMutably(newEntities, state) {
        newEntities = ensureEntitiesArray(newEntities);
        for (var _i = 0, newEntities_3 = newEntities; _i < newEntities_3.length; _i++) {
            var entity = newEntities_3[_i];
            setOneMutably(entity, state);
        }
    }
    function setAllMutably(newEntities, state) {
        newEntities = ensureEntitiesArray(newEntities);
        state.ids = [];
        state.entities = {};
        addManyMutably(newEntities, state);
    }
    function removeOneMutably(key, state) {
        return removeManyMutably([key], state);
    }
    function removeManyMutably(keys, state) {
        var didMutate = false;
        keys.forEach(function (key) {
            if (key in state.entities) {
                delete state.entities[key];
                didMutate = true;
            }
        });
        if (didMutate) {
            state.ids = state.ids.filter(function (id) { return id in state.entities; });
        }
    }
    function removeAllMutably(state) {
        Object.assign(state, {
            ids: [],
            entities: {}
        });
    }
    function takeNewKey(keys, update, state) {
        var original2 = state.entities[update.id];
        var updated = Object.assign({}, original2, update.changes);
        var newKey = selectIdValue(updated, selectId);
        var hasNewKey = newKey !== update.id;
        if (hasNewKey) {
            keys[update.id] = newKey;
            delete state.entities[update.id];
        }
        state.entities[newKey] = updated;
        return hasNewKey;
    }
    function updateOneMutably(update, state) {
        return updateManyMutably([update], state);
    }
    function updateManyMutably(updates, state) {
        var newKeys = {};
        var updatesPerEntity = {};
        updates.forEach(function (update) {
            if (update.id in state.entities) {
                updatesPerEntity[update.id] = {
                    id: update.id,
                    changes: __spreadValues(__spreadValues({}, updatesPerEntity[update.id] ? updatesPerEntity[update.id].changes : null), update.changes)
                };
            }
        });
        updates = Object.values(updatesPerEntity);
        var didMutateEntities = updates.length > 0;
        if (didMutateEntities) {
            var didMutateIds = updates.filter(function (update) { return takeNewKey(newKeys, update, state); }).length > 0;
            if (didMutateIds) {
                state.ids = Object.keys(state.entities);
            }
        }
    }
    function upsertOneMutably(entity, state) {
        return upsertManyMutably([entity], state);
    }
    function upsertManyMutably(newEntities, state) {
        var _c = splitAddedUpdatedEntities(newEntities, selectId, state), added = _c[0], updated = _c[1];
        updateManyMutably(updated, state);
        addManyMutably(added, state);
    }
    return {
        removeAll: createSingleArgumentStateOperator(removeAllMutably),
        addOne: createStateOperator(addOneMutably),
        addMany: createStateOperator(addManyMutably),
        setOne: createStateOperator(setOneMutably),
        setMany: createStateOperator(setManyMutably),
        setAll: createStateOperator(setAllMutably),
        updateOne: createStateOperator(updateOneMutably),
        updateMany: createStateOperator(updateManyMutably),
        upsertOne: createStateOperator(upsertOneMutably),
        upsertMany: createStateOperator(upsertManyMutably),
        removeOne: createStateOperator(removeOneMutably),
        removeMany: createStateOperator(removeManyMutably)
    };
}
// src/entities/sorted_state_adapter.ts
function createSortedStateAdapter(selectId, sort) {
    var _c = createUnsortedStateAdapter(selectId), removeOne = _c.removeOne, removeMany = _c.removeMany, removeAll = _c.removeAll;
    function addOneMutably(entity, state) {
        return addManyMutably([entity], state);
    }
    function addManyMutably(newEntities, state) {
        newEntities = ensureEntitiesArray(newEntities);
        var models = newEntities.filter(function (model) { return !(selectIdValue(model, selectId) in state.entities); });
        if (models.length !== 0) {
            merge(models, state);
        }
    }
    function setOneMutably(entity, state) {
        return setManyMutably([entity], state);
    }
    function setManyMutably(newEntities, state) {
        newEntities = ensureEntitiesArray(newEntities);
        if (newEntities.length !== 0) {
            merge(newEntities, state);
        }
    }
    function setAllMutably(newEntities, state) {
        newEntities = ensureEntitiesArray(newEntities);
        state.entities = {};
        state.ids = [];
        addManyMutably(newEntities, state);
    }
    function updateOneMutably(update, state) {
        return updateManyMutably([update], state);
    }
    function updateManyMutably(updates, state) {
        var appliedUpdates = false;
        for (var _i = 0, updates_1 = updates; _i < updates_1.length; _i++) {
            var update = updates_1[_i];
            var entity = state.entities[update.id];
            if (!entity) {
                continue;
            }
            appliedUpdates = true;
            Object.assign(entity, update.changes);
            var newId = selectId(entity);
            if (update.id !== newId) {
                delete state.entities[update.id];
                state.entities[newId] = entity;
            }
        }
        if (appliedUpdates) {
            resortEntities(state);
        }
    }
    function upsertOneMutably(entity, state) {
        return upsertManyMutably([entity], state);
    }
    function upsertManyMutably(newEntities, state) {
        var _c = splitAddedUpdatedEntities(newEntities, selectId, state), added = _c[0], updated = _c[1];
        updateManyMutably(updated, state);
        addManyMutably(added, state);
    }
    function areArraysEqual(a, b) {
        if (a.length !== b.length) {
            return false;
        }
        for (var i = 0; i < a.length && i < b.length; i++) {
            if (a[i] === b[i]) {
                continue;
            }
            return false;
        }
        return true;
    }
    function merge(models, state) {
        models.forEach(function (model) {
            state.entities[selectId(model)] = model;
        });
        resortEntities(state);
    }
    function resortEntities(state) {
        var allEntities = Object.values(state.entities);
        allEntities.sort(sort);
        var newSortedIds = allEntities.map(selectId);
        var ids = state.ids;
        if (!areArraysEqual(ids, newSortedIds)) {
            state.ids = newSortedIds;
        }
    }
    return {
        removeOne: removeOne,
        removeMany: removeMany,
        removeAll: removeAll,
        addOne: createStateOperator(addOneMutably),
        updateOne: createStateOperator(updateOneMutably),
        upsertOne: createStateOperator(upsertOneMutably),
        setOne: createStateOperator(setOneMutably),
        setMany: createStateOperator(setManyMutably),
        setAll: createStateOperator(setAllMutably),
        addMany: createStateOperator(addManyMutably),
        updateMany: createStateOperator(updateManyMutably),
        upsertMany: createStateOperator(upsertManyMutably)
    };
}
// src/entities/create_adapter.ts
function createEntityAdapter(options) {
    if (options === void 0) { options = {}; }
    var _c = __spreadValues({
        sortComparer: false,
        selectId: function (instance) { return instance.id; }
    }, options), selectId = _c.selectId, sortComparer = _c.sortComparer;
    var stateFactory = createInitialStateFactory();
    var selectorsFactory = createSelectorsFactory();
    var stateAdapter = sortComparer ? createSortedStateAdapter(selectId, sortComparer) : createUnsortedStateAdapter(selectId);
    return __spreadValues(__spreadValues(__spreadValues({
        selectId: selectId,
        sortComparer: sortComparer
    }, stateFactory), selectorsFactory), stateAdapter);
}
// src/nanoid.ts
var urlAlphabet = "ModuleSymbhasOwnPr-0123456789ABCDEFGHNRVfgctiUvz_KqYTJkLxpZXIjQW";
var nanoid = function (size) {
    if (size === void 0) { size = 21; }
    var id = "";
    var i = size;
    while (i--) {
        id += urlAlphabet[Math.random() * 64 | 0];
    }
    return id;
};
// src/createAsyncThunk.ts
var commonProperties = [
    "name",
    "message",
    "stack",
    "code"
];
var RejectWithValue = /** @class */ (function () {
    function RejectWithValue(payload, meta) {
        this.payload = payload;
        this.meta = meta;
    }
    return RejectWithValue;
}());
var FulfillWithMeta = /** @class */ (function () {
    function FulfillWithMeta(payload, meta) {
        this.payload = payload;
        this.meta = meta;
    }
    return FulfillWithMeta;
}());
var miniSerializeError = function (value) {
    if (typeof value === "object" && value !== null) {
        var simpleError = {};
        for (var _i = 0, commonProperties_1 = commonProperties; _i < commonProperties_1.length; _i++) {
            var property = commonProperties_1[_i];
            if (typeof value[property] === "string") {
                simpleError[property] = value[property];
            }
        }
        return simpleError;
    }
    return { message: String(value) };
};
var createAsyncThunk = (function () {
    function createAsyncThunk2(typePrefix, payloadCreator, options) {
        var fulfilled = createAction(typePrefix + "/fulfilled", function (payload, requestId, arg, meta) { return ({
            payload: payload,
            meta: __spreadProps(__spreadValues({}, meta || {}), {
                arg: arg,
                requestId: requestId,
                requestStatus: "fulfilled"
            })
        }); });
        var pending = createAction(typePrefix + "/pending", function (requestId, arg, meta) { return ({
            payload: void 0,
            meta: __spreadProps(__spreadValues({}, meta || {}), {
                arg: arg,
                requestId: requestId,
                requestStatus: "pending"
            })
        }); });
        var rejected = createAction(typePrefix + "/rejected", function (error, requestId, arg, payload, meta) { return ({
            payload: payload,
            error: (options && options.serializeError || miniSerializeError)(error || "Rejected"),
            meta: __spreadProps(__spreadValues({}, meta || {}), {
                arg: arg,
                requestId: requestId,
                rejectedWithValue: !!payload,
                requestStatus: "rejected",
                aborted: (error == null ? void 0 : error.name) === "AbortError",
                condition: (error == null ? void 0 : error.name) === "ConditionError"
            })
        }); });
        var displayedWarning = false;
        var AC = typeof AbortController !== "undefined" ? AbortController : /** @class */ (function () {
            function class_1() {
                this.signal = {
                    aborted: false,
                    addEventListener: function () {
                    },
                    dispatchEvent: function () {
                        return false;
                    },
                    onabort: function () {
                    },
                    removeEventListener: function () {
                    },
                    reason: void 0,
                    throwIfAborted: function () {
                    }
                };
            }
            class_1.prototype.abort = function () {
                if (true) {
                    if (!displayedWarning) {
                        displayedWarning = true;
                        console.info("This platform does not implement AbortController. \nIf you want to use the AbortController to react to `abort` events, please consider importing a polyfill like 'abortcontroller-polyfill/dist/abortcontroller-polyfill-only'.");
                    }
                }
            };
            return class_1;
        }());
        function actionCreator(arg) {
            return function (dispatch, getState, extra) {
                var requestId = (options == null ? void 0 : options.idGenerator) ? options.idGenerator(arg) : nanoid();
                var abortController = new AC();
                var abortReason;
                var started = false;
                function abort(reason) {
                    abortReason = reason;
                    abortController.abort();
                }
                var promise2 = function () {
                    return __async(this, null, function () {
                        var _a, _b, finalAction, conditionResult, abortedPromise, err_1, skipDispatch;
                        return __generator(this, function (_c) {
                            switch (_c.label) {
                                case 0:
                                    _c.trys.push([0, 4, , 5]);
                                    conditionResult = (_a = options == null ? void 0 : options.condition) == null ? void 0 : _a.call(options, arg, { getState: getState, extra: extra });
                                    if (!isThenable(conditionResult)) return [3 /*break*/, 2];
                                    return [4 /*yield*/, conditionResult];
                                case 1:
                                    conditionResult = _c.sent();
                                    _c.label = 2;
                                case 2:
                                    if (conditionResult === false || abortController.signal.aborted) {
                                        throw {
                                            name: "ConditionError",
                                            message: "Aborted due to condition callback returning false."
                                        };
                                    }
                                    started = true;
                                    abortedPromise = new Promise(function (_, reject) { return abortController.signal.addEventListener("abort", function () { return reject({
                                        name: "AbortError",
                                        message: abortReason || "Aborted"
                                    }); }); });
                                    dispatch(pending(requestId, arg, (_b = options == null ? void 0 : options.getPendingMeta) == null ? void 0 : _b.call(options, { requestId: requestId, arg: arg }, { getState: getState, extra: extra })));
                                    return [4 /*yield*/, Promise.race([
                                            abortedPromise,
                                            Promise.resolve(payloadCreator(arg, {
                                                dispatch: dispatch,
                                                getState: getState,
                                                extra: extra,
                                                requestId: requestId,
                                                signal: abortController.signal,
                                                abort: abort,
                                                rejectWithValue: function (value, meta) {
                                                    return new RejectWithValue(value, meta);
                                                },
                                                fulfillWithValue: function (value, meta) {
                                                    return new FulfillWithMeta(value, meta);
                                                }
                                            })).then(function (result) {
                                                if (result instanceof RejectWithValue) {
                                                    throw result;
                                                }
                                                if (result instanceof FulfillWithMeta) {
                                                    return fulfilled(result.payload, requestId, arg, result.meta);
                                                }
                                                return fulfilled(result, requestId, arg);
                                            })
                                        ])];
                                case 3:
                                    finalAction = _c.sent();
                                    return [3 /*break*/, 5];
                                case 4:
                                    err_1 = _c.sent();
                                    finalAction = err_1 instanceof RejectWithValue ? rejected(null, requestId, arg, err_1.payload, err_1.meta) : rejected(err_1, requestId, arg);
                                    return [3 /*break*/, 5];
                                case 5:
                                    skipDispatch = options && !options.dispatchConditionRejection && rejected.match(finalAction) && finalAction.meta.condition;
                                    if (!skipDispatch) {
                                        dispatch(finalAction);
                                    }
                                    return [2 /*return*/, finalAction];
                            }
                        });
                    });
                }();
                return Object.assign(promise2, {
                    abort: abort,
                    requestId: requestId,
                    arg: arg,
                    unwrap: function () {
                        return promise2.then(unwrapResult);
                    }
                });
            };
        }
        return Object.assign(actionCreator, {
            pending: pending,
            rejected: rejected,
            fulfilled: fulfilled,
            typePrefix: typePrefix
        });
    }
    createAsyncThunk2.withTypes = function () { return createAsyncThunk2; };
    return createAsyncThunk2;
})();
function unwrapResult(action) {
    if (action.meta && action.meta.rejectedWithValue) {
        throw action.payload;
    }
    if (action.error) {
        throw action.error;
    }
    return action.payload;
}
function isThenable(value) {
    return value !== null && typeof value === "object" && typeof value.then === "function";
}
// src/matchers.ts
var matches = function (matcher, action) {
    if (hasMatchFunction(matcher)) {
        return matcher.match(action);
    }
    else {
        return matcher(action);
    }
};
function isAnyOf() {
    var matchers = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        matchers[_i] = arguments[_i];
    }
    return function (action) {
        return matchers.some(function (matcher) { return matches(matcher, action); });
    };
}
function isAllOf() {
    var matchers = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        matchers[_i] = arguments[_i];
    }
    return function (action) {
        return matchers.every(function (matcher) { return matches(matcher, action); });
    };
}
function hasExpectedRequestMetadata(action, validStatus) {
    if (!action || !action.meta)
        return false;
    var hasValidRequestId = typeof action.meta.requestId === "string";
    var hasValidRequestStatus = validStatus.indexOf(action.meta.requestStatus) > -1;
    return hasValidRequestId && hasValidRequestStatus;
}
function isAsyncThunkArray(a) {
    return typeof a[0] === "function" && "pending" in a[0] && "fulfilled" in a[0] && "rejected" in a[0];
}
function isPending() {
    var asyncThunks = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        asyncThunks[_i] = arguments[_i];
    }
    if (asyncThunks.length === 0) {
        return function (action) { return hasExpectedRequestMetadata(action, ["pending"]); };
    }
    if (!isAsyncThunkArray(asyncThunks)) {
        return isPending()(asyncThunks[0]);
    }
    return function (action) {
        var matchers = asyncThunks.map(function (asyncThunk) { return asyncThunk.pending; });
        var combinedMatcher = isAnyOf.apply(void 0, matchers);
        return combinedMatcher(action);
    };
}
function isRejected() {
    var asyncThunks = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        asyncThunks[_i] = arguments[_i];
    }
    if (asyncThunks.length === 0) {
        return function (action) { return hasExpectedRequestMetadata(action, ["rejected"]); };
    }
    if (!isAsyncThunkArray(asyncThunks)) {
        return isRejected()(asyncThunks[0]);
    }
    return function (action) {
        var matchers = asyncThunks.map(function (asyncThunk) { return asyncThunk.rejected; });
        var combinedMatcher = isAnyOf.apply(void 0, matchers);
        return combinedMatcher(action);
    };
}
function isRejectedWithValue() {
    var asyncThunks = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        asyncThunks[_i] = arguments[_i];
    }
    var hasFlag = function (action) {
        return action && action.meta && action.meta.rejectedWithValue;
    };
    if (asyncThunks.length === 0) {
        return function (action) {
            var combinedMatcher = isAllOf(isRejected.apply(void 0, asyncThunks), hasFlag);
            return combinedMatcher(action);
        };
    }
    if (!isAsyncThunkArray(asyncThunks)) {
        return isRejectedWithValue()(asyncThunks[0]);
    }
    return function (action) {
        var combinedMatcher = isAllOf(isRejected.apply(void 0, asyncThunks), hasFlag);
        return combinedMatcher(action);
    };
}
function isFulfilled() {
    var asyncThunks = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        asyncThunks[_i] = arguments[_i];
    }
    if (asyncThunks.length === 0) {
        return function (action) { return hasExpectedRequestMetadata(action, ["fulfilled"]); };
    }
    if (!isAsyncThunkArray(asyncThunks)) {
        return isFulfilled()(asyncThunks[0]);
    }
    return function (action) {
        var matchers = asyncThunks.map(function (asyncThunk) { return asyncThunk.fulfilled; });
        var combinedMatcher = isAnyOf.apply(void 0, matchers);
        return combinedMatcher(action);
    };
}
function isAsyncThunkAction() {
    var asyncThunks = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        asyncThunks[_i] = arguments[_i];
    }
    if (asyncThunks.length === 0) {
        return function (action) { return hasExpectedRequestMetadata(action, ["pending", "fulfilled", "rejected"]); };
    }
    if (!isAsyncThunkArray(asyncThunks)) {
        return isAsyncThunkAction()(asyncThunks[0]);
    }
    return function (action) {
        var matchers = [];
        for (var _i = 0, asyncThunks_1 = asyncThunks; _i < asyncThunks_1.length; _i++) {
            var asyncThunk = asyncThunks_1[_i];
            matchers.push(asyncThunk.pending, asyncThunk.rejected, asyncThunk.fulfilled);
        }
        var combinedMatcher = isAnyOf.apply(void 0, matchers);
        return combinedMatcher(action);
    };
}
// src/listenerMiddleware/utils.ts
var assertFunction = function (func, expected) {
    if (typeof func !== "function") {
        throw new TypeError(expected + " is not a function");
    }
};
var noop = function () {
};
var catchRejection = function (promise2, onError) {
    if (onError === void 0) { onError = noop; }
    promise2.catch(onError);
    return promise2;
};
var addAbortSignalListener = function (abortSignal, callback) {
    abortSignal.addEventListener("abort", callback, { once: true });
    return function () { return abortSignal.removeEventListener("abort", callback); };
};
var abortControllerWithReason = function (abortController, reason) {
    var signal = abortController.signal;
    if (signal.aborted) {
        return;
    }
    if (!("reason" in signal)) {
        Object.defineProperty(signal, "reason", {
            enumerable: true,
            value: reason,
            configurable: true,
            writable: true
        });
    }
    ;
    abortController.abort(reason);
};
// src/listenerMiddleware/exceptions.ts
var task = "task";
var listener = "listener";
var completed = "completed";
var cancelled = "cancelled";
var taskCancelled = "task-" + cancelled;
var taskCompleted = "task-" + completed;
var listenerCancelled = listener + "-" + cancelled;
var listenerCompleted = listener + "-" + completed;
var TaskAbortError = /** @class */ (function () {
    function TaskAbortError(code) {
        this.code = code;
        this.name = "TaskAbortError";
        this.message = task + " " + cancelled + " (reason: " + code + ")";
    }
    return TaskAbortError;
}());
// src/listenerMiddleware/task.ts
var validateActive = function (signal) {
    if (signal.aborted) {
        throw new TaskAbortError(signal.reason);
    }
};
function raceWithSignal(signal, promise2) {
    var cleanup = noop;
    return new Promise(function (resolve, reject) {
        var notifyRejection = function () { return reject(new TaskAbortError(signal.reason)); };
        if (signal.aborted) {
            notifyRejection();
            return;
        }
        cleanup = addAbortSignalListener(signal, notifyRejection);
        promise2.finally(function () { return cleanup(); }).then(resolve, reject);
    }).finally(function () {
        cleanup = noop;
    });
}
var runTask = function (task2, cleanUp) { return __async(void 0, null, function () {
    var value, error_1;
    return __generator(this, function (_c) {
        switch (_c.label) {
            case 0:
                _c.trys.push([0, 3, 4, 5]);
                return [4 /*yield*/, Promise.resolve()];
            case 1:
                _c.sent();
                return [4 /*yield*/, task2()];
            case 2:
                value = _c.sent();
                return [2 /*return*/, {
                        status: "ok",
                        value: value
                    }];
            case 3:
                error_1 = _c.sent();
                return [2 /*return*/, {
                        status: error_1 instanceof TaskAbortError ? "cancelled" : "rejected",
                        error: error_1
                    }];
            case 4:
                cleanUp == null ? void 0 : cleanUp();
                return [7 /*endfinally*/];
            case 5: return [2 /*return*/];
        }
    });
}); };
var createPause = function (signal) {
    return function (promise2) {
        return catchRejection(raceWithSignal(signal, promise2).then(function (output) {
            validateActive(signal);
            return output;
        }));
    };
};
var createDelay = function (signal) {
    var pause = createPause(signal);
    return function (timeoutMs) {
        return pause(new Promise(function (resolve) { return setTimeout(resolve, timeoutMs); }));
    };
};
// src/listenerMiddleware/index.ts
var assign = Object.assign;
var INTERNAL_NIL_TOKEN = {};
var alm = "listenerMiddleware";
var createFork = function (parentAbortSignal, parentBlockingPromises) {
    var linkControllers = function (controller) { return addAbortSignalListener(parentAbortSignal, function () { return abortControllerWithReason(controller, parentAbortSignal.reason); }); };
    return function (taskExecutor, opts) {
        assertFunction(taskExecutor, "taskExecutor");
        var childAbortController = new AbortController();
        linkControllers(childAbortController);
        var result = runTask(function () { return __async(void 0, null, function () {
            var result2;
            return __generator(this, function (_c) {
                switch (_c.label) {
                    case 0:
                        validateActive(parentAbortSignal);
                        validateActive(childAbortController.signal);
                        return [4 /*yield*/, taskExecutor({
                                pause: createPause(childAbortController.signal),
                                delay: createDelay(childAbortController.signal),
                                signal: childAbortController.signal
                            })];
                    case 1:
                        result2 = _c.sent();
                        validateActive(childAbortController.signal);
                        return [2 /*return*/, result2];
                }
            });
        }); }, function () { return abortControllerWithReason(childAbortController, taskCompleted); });
        if (opts == null ? void 0 : opts.autoJoin) {
            parentBlockingPromises.push(result);
        }
        return {
            result: createPause(parentAbortSignal)(result),
            cancel: function () {
                abortControllerWithReason(childAbortController, taskCancelled);
            }
        };
    };
};
var createTakePattern = function (startListening, signal) {
    var take = function (predicate, timeout) { return __async(void 0, null, function () {
        var unsubscribe, tuplePromise, promises, output;
        return __generator(this, function (_c) {
            switch (_c.label) {
                case 0:
                    validateActive(signal);
                    unsubscribe = function () {
                    };
                    tuplePromise = new Promise(function (resolve, reject) {
                        var stopListening = startListening({
                            predicate: predicate,
                            effect: function (action, listenerApi) {
                                listenerApi.unsubscribe();
                                resolve([
                                    action,
                                    listenerApi.getState(),
                                    listenerApi.getOriginalState()
                                ]);
                            }
                        });
                        unsubscribe = function () {
                            stopListening();
                            reject();
                        };
                    });
                    promises = [
                        tuplePromise
                    ];
                    if (timeout != null) {
                        promises.push(new Promise(function (resolve) { return setTimeout(resolve, timeout, null); }));
                    }
                    _c.label = 1;
                case 1:
                    _c.trys.push([1, , 3, 4]);
                    return [4 /*yield*/, raceWithSignal(signal, Promise.race(promises))];
                case 2:
                    output = _c.sent();
                    validateActive(signal);
                    return [2 /*return*/, output];
                case 3:
                    unsubscribe();
                    return [7 /*endfinally*/];
                case 4: return [2 /*return*/];
            }
        });
    }); };
    return function (predicate, timeout) { return catchRejection(take(predicate, timeout)); };
};
var getListenerEntryPropsFrom = function (options) {
    var type = options.type, actionCreator = options.actionCreator, matcher = options.matcher, predicate = options.predicate, effect = options.effect;
    if (type) {
        predicate = createAction(type).match;
    }
    else if (actionCreator) {
        type = actionCreator.type;
        predicate = actionCreator.match;
    }
    else if (matcher) {
        predicate = matcher;
    }
    else if (predicate) {
    }
    else {
        throw new Error("Creating or removing a listener requires one of the known fields for matching an action");
    }
    assertFunction(effect, "options.listener");
    return { predicate: predicate, type: type, effect: effect };
};
var createListenerEntry = function (options) {
    var _c = getListenerEntryPropsFrom(options), type = _c.type, predicate = _c.predicate, effect = _c.effect;
    var id = nanoid();
    var entry = {
        id: id,
        effect: effect,
        type: type,
        predicate: predicate,
        pending: new Set(),
        unsubscribe: function () {
            throw new Error("Unsubscribe not initialized");
        }
    };
    return entry;
};
var cancelActiveListeners = function (entry) {
    entry.pending.forEach(function (controller) {
        abortControllerWithReason(controller, listenerCancelled);
    });
};
var createClearListenerMiddleware = function (listenerMap) {
    return function () {
        listenerMap.forEach(cancelActiveListeners);
        listenerMap.clear();
    };
};
var safelyNotifyError = function (errorHandler, errorToNotify, errorInfo) {
    try {
        errorHandler(errorToNotify, errorInfo);
    }
    catch (errorHandlerError) {
        setTimeout(function () {
            throw errorHandlerError;
        }, 0);
    }
};
var addListener = createAction(alm + "/add");
var clearAllListeners = createAction(alm + "/removeAll");
var removeListener = createAction(alm + "/remove");
var defaultErrorHandler = function () {
    var args = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        args[_i] = arguments[_i];
    }
    console.error.apply(console, __spreadArray([alm + "/error"], args));
};
function createListenerMiddleware(middlewareOptions) {
    var _this = this;
    if (middlewareOptions === void 0) { middlewareOptions = {}; }
    var listenerMap = new Map();
    var extra = middlewareOptions.extra, _c = middlewareOptions.onError, onError = _c === void 0 ? defaultErrorHandler : _c;
    assertFunction(onError, "onError");
    var insertEntry = function (entry) {
        entry.unsubscribe = function () { return listenerMap.delete(entry.id); };
        listenerMap.set(entry.id, entry);
        return function (cancelOptions) {
            entry.unsubscribe();
            if (cancelOptions == null ? void 0 : cancelOptions.cancelActive) {
                cancelActiveListeners(entry);
            }
        };
    };
    var findListenerEntry = function (comparator) {
        for (var _i = 0, _c = Array.from(listenerMap.values()); _i < _c.length; _i++) {
            var entry = _c[_i];
            if (comparator(entry)) {
                return entry;
            }
        }
        return void 0;
    };
    var startListening = function (options) {
        var entry = findListenerEntry(function (existingEntry) { return existingEntry.effect === options.effect; });
        if (!entry) {
            entry = createListenerEntry(options);
        }
        return insertEntry(entry);
    };
    var stopListening = function (options) {
        var _c = getListenerEntryPropsFrom(options), type = _c.type, effect = _c.effect, predicate = _c.predicate;
        var entry = findListenerEntry(function (entry2) {
            var matchPredicateOrType = typeof type === "string" ? entry2.type === type : entry2.predicate === predicate;
            return matchPredicateOrType && entry2.effect === effect;
        });
        if (entry) {
            entry.unsubscribe();
            if (options.cancelActive) {
                cancelActiveListeners(entry);
            }
        }
        return !!entry;
    };
    var notifyListener = function (entry, action, api, getOriginalState) { return __async(_this, null, function () {
        var internalTaskController, take, autoJoinPromises, listenerError_1;
        return __generator(this, function (_c) {
            switch (_c.label) {
                case 0:
                    internalTaskController = new AbortController();
                    take = createTakePattern(startListening, internalTaskController.signal);
                    autoJoinPromises = [];
                    _c.label = 1;
                case 1:
                    _c.trys.push([1, 3, 4, 6]);
                    entry.pending.add(internalTaskController);
                    return [4 /*yield*/, Promise.resolve(entry.effect(action, assign({}, api, {
                            getOriginalState: getOriginalState,
                            condition: function (predicate, timeout) { return take(predicate, timeout).then(Boolean); },
                            take: take,
                            delay: createDelay(internalTaskController.signal),
                            pause: createPause(internalTaskController.signal),
                            extra: extra,
                            signal: internalTaskController.signal,
                            fork: createFork(internalTaskController.signal, autoJoinPromises),
                            unsubscribe: entry.unsubscribe,
                            subscribe: function () {
                                listenerMap.set(entry.id, entry);
                            },
                            cancelActiveListeners: function () {
                                entry.pending.forEach(function (controller, _, set) {
                                    if (controller !== internalTaskController) {
                                        abortControllerWithReason(controller, listenerCancelled);
                                        set.delete(controller);
                                    }
                                });
                            }
                        })))];
                case 2:
                    _c.sent();
                    return [3 /*break*/, 6];
                case 3:
                    listenerError_1 = _c.sent();
                    if (!(listenerError_1 instanceof TaskAbortError)) {
                        safelyNotifyError(onError, listenerError_1, {
                            raisedBy: "effect"
                        });
                    }
                    return [3 /*break*/, 6];
                case 4: return [4 /*yield*/, Promise.allSettled(autoJoinPromises)];
                case 5:
                    _c.sent();
                    abortControllerWithReason(internalTaskController, listenerCompleted);
                    entry.pending.delete(internalTaskController);
                    return [7 /*endfinally*/];
                case 6: return [2 /*return*/];
            }
        });
    }); };
    var clearListenerMiddleware = createClearListenerMiddleware(listenerMap);
    var middleware = function (api) { return function (next) { return function (action) {
        if (!isAction(action)) {
            return next(action);
        }
        if (addListener.match(action)) {
            return startListening(action.payload);
        }
        if (clearAllListeners.match(action)) {
            clearListenerMiddleware();
            return;
        }
        if (removeListener.match(action)) {
            return stopListening(action.payload);
        }
        var originalState = api.getState();
        var getOriginalState = function () {
            if (originalState === INTERNAL_NIL_TOKEN) {
                throw new Error(alm + ": getOriginalState can only be called synchronously");
            }
            return originalState;
        };
        var result;
        try {
            result = next(action);
            if (listenerMap.size > 0) {
                var currentState = api.getState();
                var listenerEntries = Array.from(listenerMap.values());
                for (var _i = 0, listenerEntries_1 = listenerEntries; _i < listenerEntries_1.length; _i++) {
                    var entry = listenerEntries_1[_i];
                    var runListener = false;
                    try {
                        runListener = entry.predicate(action, currentState, originalState);
                    }
                    catch (predicateError) {
                        runListener = false;
                        safelyNotifyError(onError, predicateError, {
                            raisedBy: "predicate"
                        });
                    }
                    if (!runListener) {
                        continue;
                    }
                    notifyListener(entry, action, api, getOriginalState);
                }
            }
        }
        finally {
            originalState = INTERNAL_NIL_TOKEN;
        }
        return result;
    }; }; };
    return {
        middleware: middleware,
        startListening: startListening,
        stopListening: stopListening,
        clearListeners: clearListenerMiddleware
    };
}
// src/autoBatchEnhancer.ts
var SHOULD_AUTOBATCH = "RTK_autoBatch";
var prepareAutoBatched = function () { return function (payload) {
    var _c;
    return ({
        payload: payload,
        meta: (_c = {}, _c[SHOULD_AUTOBATCH] = true, _c)
    });
}; };
var promise;
var queueMicrotaskShim = typeof queueMicrotask === "function" ? queueMicrotask.bind(typeof window !== "undefined" ? window : typeof __webpack_require__.g !== "undefined" ? __webpack_require__.g : globalThis) : function (cb) { return (promise || (promise = Promise.resolve())).then(cb).catch(function (err) { return setTimeout(function () {
    throw err;
}, 0); }); };
var createQueueWithTimer = function (timeout) {
    return function (notify) {
        setTimeout(notify, timeout);
    };
};
var rAF = typeof window !== "undefined" && window.requestAnimationFrame ? window.requestAnimationFrame : createQueueWithTimer(10);
var autoBatchEnhancer = function (options) {
    if (options === void 0) { options = { type: "raf" }; }
    return function (next) { return function () {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        var store = next.apply(void 0, args);
        var notifying = true;
        var shouldNotifyAtEndOfTick = false;
        var notificationQueued = false;
        var listeners = new Set();
        var queueCallback = options.type === "tick" ? queueMicrotaskShim : options.type === "raf" ? rAF : options.type === "callback" ? options.queueNotification : createQueueWithTimer(options.timeout);
        var notifyListeners = function () {
            notificationQueued = false;
            if (shouldNotifyAtEndOfTick) {
                shouldNotifyAtEndOfTick = false;
                listeners.forEach(function (l) { return l(); });
            }
        };
        return Object.assign({}, store, {
            subscribe: function (listener2) {
                var wrappedListener = function () { return notifying && listener2(); };
                var unsubscribe = store.subscribe(wrappedListener);
                listeners.add(listener2);
                return function () {
                    unsubscribe();
                    listeners.delete(listener2);
                };
            },
            dispatch: function (action) {
                var _a;
                try {
                    notifying = !((_a = action == null ? void 0 : action.meta) == null ? void 0 : _a[SHOULD_AUTOBATCH]);
                    shouldNotifyAtEndOfTick = !notifying;
                    if (shouldNotifyAtEndOfTick) {
                        if (!notificationQueued) {
                            notificationQueued = true;
                            queueCallback(notifyListeners);
                        }
                    }
                    return store.dispatch(action);
                }
                finally {
                    notifying = true;
                }
            }
        });
    }; };
};
// src/index.ts
(0,immer__WEBPACK_IMPORTED_MODULE_2__.enableES5)();

//# sourceMappingURL=redux-toolkit.esm.js.map

/***/ }),

/***/ "../node_modules/immer/dist/immer.esm.mjs":
/*!************************************************!*\
  !*** ../node_modules/immer/dist/immer.esm.mjs ***!
  \************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Immer: () => (/* binding */ un),
/* harmony export */   applyPatches: () => (/* binding */ pn),
/* harmony export */   castDraft: () => (/* binding */ K),
/* harmony export */   castImmutable: () => (/* binding */ $),
/* harmony export */   createDraft: () => (/* binding */ ln),
/* harmony export */   current: () => (/* binding */ R),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__),
/* harmony export */   enableAllPlugins: () => (/* binding */ J),
/* harmony export */   enableES5: () => (/* binding */ F),
/* harmony export */   enableMapSet: () => (/* binding */ C),
/* harmony export */   enablePatches: () => (/* binding */ T),
/* harmony export */   finishDraft: () => (/* binding */ dn),
/* harmony export */   freeze: () => (/* binding */ d),
/* harmony export */   immerable: () => (/* binding */ L),
/* harmony export */   isDraft: () => (/* binding */ r),
/* harmony export */   isDraftable: () => (/* binding */ t),
/* harmony export */   nothing: () => (/* binding */ H),
/* harmony export */   original: () => (/* binding */ e),
/* harmony export */   produce: () => (/* binding */ fn),
/* harmony export */   produceWithPatches: () => (/* binding */ cn),
/* harmony export */   setAutoFreeze: () => (/* binding */ sn),
/* harmony export */   setUseProxies: () => (/* binding */ vn)
/* harmony export */ });
function n(n){for(var r=arguments.length,t=Array(r>1?r-1:0),e=1;e<r;e++)t[e-1]=arguments[e];if(true){var i=Y[n],o=i?"function"==typeof i?i.apply(null,t):i:"unknown error nr: "+n;throw Error("[Immer] "+o)}// removed by dead control flow
{}}function r(n){return!!n&&!!n[Q]}function t(n){var r;return!!n&&(function(n){if(!n||"object"!=typeof n)return!1;var r=Object.getPrototypeOf(n);if(null===r)return!0;var t=Object.hasOwnProperty.call(r,"constructor")&&r.constructor;return t===Object||"function"==typeof t&&Function.toString.call(t)===Z}(n)||Array.isArray(n)||!!n[L]||!!(null===(r=n.constructor)||void 0===r?void 0:r[L])||s(n)||v(n))}function e(t){return r(t)||n(23,t),t[Q].t}function i(n,r,t){void 0===t&&(t=!1),0===o(n)?(t?Object.keys:nn)(n).forEach((function(e){t&&"symbol"==typeof e||r(e,n[e],n)})):n.forEach((function(t,e){return r(e,t,n)}))}function o(n){var r=n[Q];return r?r.i>3?r.i-4:r.i:Array.isArray(n)?1:s(n)?2:v(n)?3:0}function u(n,r){return 2===o(n)?n.has(r):Object.prototype.hasOwnProperty.call(n,r)}function a(n,r){return 2===o(n)?n.get(r):n[r]}function f(n,r,t){var e=o(n);2===e?n.set(r,t):3===e?n.add(t):n[r]=t}function c(n,r){return n===r?0!==n||1/n==1/r:n!=n&&r!=r}function s(n){return X&&n instanceof Map}function v(n){return q&&n instanceof Set}function p(n){return n.o||n.t}function l(n){if(Array.isArray(n))return Array.prototype.slice.call(n);var r=rn(n);delete r[Q];for(var t=nn(r),e=0;e<t.length;e++){var i=t[e],o=r[i];!1===o.writable&&(o.writable=!0,o.configurable=!0),(o.get||o.set)&&(r[i]={configurable:!0,writable:!0,enumerable:o.enumerable,value:n[i]})}return Object.create(Object.getPrototypeOf(n),r)}function d(n,e){return void 0===e&&(e=!1),y(n)||r(n)||!t(n)||(o(n)>1&&(n.set=n.add=n.clear=n.delete=h),Object.freeze(n),e&&i(n,(function(n,r){return d(r,!0)}),!0)),n}function h(){n(2)}function y(n){return null==n||"object"!=typeof n||Object.isFrozen(n)}function b(r){var t=tn[r];return t||n(18,r),t}function m(n,r){tn[n]||(tn[n]=r)}function _(){return false||U||n(0),U}function j(n,r){r&&(b("Patches"),n.u=[],n.s=[],n.v=r)}function g(n){O(n),n.p.forEach(S),n.p=null}function O(n){n===U&&(U=n.l)}function w(n){return U={p:[],l:U,h:n,m:!0,_:0}}function S(n){var r=n[Q];0===r.i||1===r.i?r.j():r.g=!0}function P(r,e){e._=e.p.length;var i=e.p[0],o=void 0!==r&&r!==i;return e.h.O||b("ES5").S(e,r,o),o?(i[Q].P&&(g(e),n(4)),t(r)&&(r=M(e,r),e.l||x(e,r)),e.u&&b("Patches").M(i[Q].t,r,e.u,e.s)):r=M(e,i,[]),g(e),e.u&&e.v(e.u,e.s),r!==H?r:void 0}function M(n,r,t){if(y(r))return r;var e=r[Q];if(!e)return i(r,(function(i,o){return A(n,e,r,i,o,t)}),!0),r;if(e.A!==n)return r;if(!e.P)return x(n,e.t,!0),e.t;if(!e.I){e.I=!0,e.A._--;var o=4===e.i||5===e.i?e.o=l(e.k):e.o,u=o,a=!1;3===e.i&&(u=new Set(o),o.clear(),a=!0),i(u,(function(r,i){return A(n,e,o,r,i,t,a)})),x(n,o,!1),t&&n.u&&b("Patches").N(e,t,n.u,n.s)}return e.o}function A(e,i,o,a,c,s,v){if( true&&c===o&&n(5),r(c)){var p=M(e,c,s&&i&&3!==i.i&&!u(i.R,a)?s.concat(a):void 0);if(f(o,a,p),!r(p))return;e.m=!1}else v&&o.add(c);if(t(c)&&!y(c)){if(!e.h.D&&e._<1)return;M(e,c),i&&i.A.l||x(e,c)}}function x(n,r,t){void 0===t&&(t=!1),!n.l&&n.h.D&&n.m&&d(r,t)}function z(n,r){var t=n[Q];return(t?p(t):n)[r]}function I(n,r){if(r in n)for(var t=Object.getPrototypeOf(n);t;){var e=Object.getOwnPropertyDescriptor(t,r);if(e)return e;t=Object.getPrototypeOf(t)}}function k(n){n.P||(n.P=!0,n.l&&k(n.l))}function E(n){n.o||(n.o=l(n.t))}function N(n,r,t){var e=s(r)?b("MapSet").F(r,t):v(r)?b("MapSet").T(r,t):n.O?function(n,r){var t=Array.isArray(n),e={i:t?1:0,A:r?r.A:_(),P:!1,I:!1,R:{},l:r,t:n,k:null,o:null,j:null,C:!1},i=e,o=en;t&&(i=[e],o=on);var u=Proxy.revocable(i,o),a=u.revoke,f=u.proxy;return e.k=f,e.j=a,f}(r,t):b("ES5").J(r,t);return(t?t.A:_()).p.push(e),e}function R(e){return r(e)||n(22,e),function n(r){if(!t(r))return r;var e,u=r[Q],c=o(r);if(u){if(!u.P&&(u.i<4||!b("ES5").K(u)))return u.t;u.I=!0,e=D(r,c),u.I=!1}else e=D(r,c);return i(e,(function(r,t){u&&a(u.t,r)===t||f(e,r,n(t))})),3===c?new Set(e):e}(e)}function D(n,r){switch(r){case 2:return new Map(n);case 3:return Array.from(n)}return l(n)}function F(){function t(n,r){var t=s[n];return t?t.enumerable=r:s[n]=t={configurable:!0,enumerable:r,get:function(){var r=this[Q];return true&&f(r),en.get(r,n)},set:function(r){var t=this[Q]; true&&f(t),en.set(t,n,r)}},t}function e(n){for(var r=n.length-1;r>=0;r--){var t=n[r][Q];if(!t.P)switch(t.i){case 5:a(t)&&k(t);break;case 4:o(t)&&k(t)}}}function o(n){for(var r=n.t,t=n.k,e=nn(t),i=e.length-1;i>=0;i--){var o=e[i];if(o!==Q){var a=r[o];if(void 0===a&&!u(r,o))return!0;var f=t[o],s=f&&f[Q];if(s?s.t!==a:!c(f,a))return!0}}var v=!!r[Q];return e.length!==nn(r).length+(v?0:1)}function a(n){var r=n.k;if(r.length!==n.t.length)return!0;var t=Object.getOwnPropertyDescriptor(r,r.length-1);if(t&&!t.get)return!0;for(var e=0;e<r.length;e++)if(!r.hasOwnProperty(e))return!0;return!1}function f(r){r.g&&n(3,JSON.stringify(p(r)))}var s={};m("ES5",{J:function(n,r){var e=Array.isArray(n),i=function(n,r){if(n){for(var e=Array(r.length),i=0;i<r.length;i++)Object.defineProperty(e,""+i,t(i,!0));return e}var o=rn(r);delete o[Q];for(var u=nn(o),a=0;a<u.length;a++){var f=u[a];o[f]=t(f,n||!!o[f].enumerable)}return Object.create(Object.getPrototypeOf(r),o)}(e,n),o={i:e?5:4,A:r?r.A:_(),P:!1,I:!1,R:{},l:r,t:n,k:i,o:null,g:!1,C:!1};return Object.defineProperty(i,Q,{value:o,writable:!0}),i},S:function(n,t,o){o?r(t)&&t[Q].A===n&&e(n.p):(n.u&&function n(r){if(r&&"object"==typeof r){var t=r[Q];if(t){var e=t.t,o=t.k,f=t.R,c=t.i;if(4===c)i(o,(function(r){r!==Q&&(void 0!==e[r]||u(e,r)?f[r]||n(o[r]):(f[r]=!0,k(t)))})),i(e,(function(n){void 0!==o[n]||u(o,n)||(f[n]=!1,k(t))}));else if(5===c){if(a(t)&&(k(t),f.length=!0),o.length<e.length)for(var s=o.length;s<e.length;s++)f[s]=!1;else for(var v=e.length;v<o.length;v++)f[v]=!0;for(var p=Math.min(o.length,e.length),l=0;l<p;l++)o.hasOwnProperty(l)||(f[l]=!0),void 0===f[l]&&n(o[l])}}}}(n.p[0]),e(n.p))},K:function(n){return 4===n.i?o(n):a(n)}})}function T(){function e(n){if(!t(n))return n;if(Array.isArray(n))return n.map(e);if(s(n))return new Map(Array.from(n.entries()).map((function(n){return[n[0],e(n[1])]})));if(v(n))return new Set(Array.from(n).map(e));var r=Object.create(Object.getPrototypeOf(n));for(var i in n)r[i]=e(n[i]);return u(n,L)&&(r[L]=n[L]),r}function f(n){return r(n)?e(n):n}var c="add";m("Patches",{$:function(r,t){return t.forEach((function(t){for(var i=t.path,u=t.op,f=r,s=0;s<i.length-1;s++){var v=o(f),p=i[s];"string"!=typeof p&&"number"!=typeof p&&(p=""+p),0!==v&&1!==v||"__proto__"!==p&&"constructor"!==p||n(24),"function"==typeof f&&"prototype"===p&&n(24),"object"!=typeof(f=a(f,p))&&n(15,i.join("/"))}var l=o(f),d=e(t.value),h=i[i.length-1];switch(u){case"replace":switch(l){case 2:return f.set(h,d);case 3:n(16);default:return f[h]=d}case c:switch(l){case 1:return"-"===h?f.push(d):f.splice(h,0,d);case 2:return f.set(h,d);case 3:return f.add(d);default:return f[h]=d}case"remove":switch(l){case 1:return f.splice(h,1);case 2:return f.delete(h);case 3:return f.delete(t.value);default:return delete f[h]}default:n(17,u)}})),r},N:function(n,r,t,e){switch(n.i){case 0:case 4:case 2:return function(n,r,t,e){var o=n.t,s=n.o;i(n.R,(function(n,i){var v=a(o,n),p=a(s,n),l=i?u(o,n)?"replace":c:"remove";if(v!==p||"replace"!==l){var d=r.concat(n);t.push("remove"===l?{op:l,path:d}:{op:l,path:d,value:p}),e.push(l===c?{op:"remove",path:d}:"remove"===l?{op:c,path:d,value:f(v)}:{op:"replace",path:d,value:f(v)})}}))}(n,r,t,e);case 5:case 1:return function(n,r,t,e){var i=n.t,o=n.R,u=n.o;if(u.length<i.length){var a=[u,i];i=a[0],u=a[1];var s=[e,t];t=s[0],e=s[1]}for(var v=0;v<i.length;v++)if(o[v]&&u[v]!==i[v]){var p=r.concat([v]);t.push({op:"replace",path:p,value:f(u[v])}),e.push({op:"replace",path:p,value:f(i[v])})}for(var l=i.length;l<u.length;l++){var d=r.concat([l]);t.push({op:c,path:d,value:f(u[l])})}i.length<u.length&&e.push({op:"replace",path:r.concat(["length"]),value:i.length})}(n,r,t,e);case 3:return function(n,r,t,e){var i=n.t,o=n.o,u=0;i.forEach((function(n){if(!o.has(n)){var i=r.concat([u]);t.push({op:"remove",path:i,value:n}),e.unshift({op:c,path:i,value:n})}u++})),u=0,o.forEach((function(n){if(!i.has(n)){var o=r.concat([u]);t.push({op:c,path:o,value:n}),e.unshift({op:"remove",path:o,value:n})}u++}))}(n,r,t,e)}},M:function(n,r,t,e){t.push({op:"replace",path:[],value:r===H?void 0:r}),e.push({op:"replace",path:[],value:n})}})}function C(){function r(n,r){function t(){this.constructor=n}a(n,r),n.prototype=(t.prototype=r.prototype,new t)}function e(n){n.o||(n.R=new Map,n.o=new Map(n.t))}function o(n){n.o||(n.o=new Set,n.t.forEach((function(r){if(t(r)){var e=N(n.A.h,r,n);n.p.set(r,e),n.o.add(e)}else n.o.add(r)})))}function u(r){r.g&&n(3,JSON.stringify(p(r)))}var a=function(n,r){return(a=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(n,r){n.__proto__=r}||function(n,r){for(var t in r)r.hasOwnProperty(t)&&(n[t]=r[t])})(n,r)},f=function(){function n(n,r){return this[Q]={i:2,l:r,A:r?r.A:_(),P:!1,I:!1,o:void 0,R:void 0,t:n,k:this,C:!1,g:!1},this}r(n,Map);var o=n.prototype;return Object.defineProperty(o,"size",{get:function(){return p(this[Q]).size}}),o.has=function(n){return p(this[Q]).has(n)},o.set=function(n,r){var t=this[Q];return u(t),p(t).has(n)&&p(t).get(n)===r||(e(t),k(t),t.R.set(n,!0),t.o.set(n,r),t.R.set(n,!0)),this},o.delete=function(n){if(!this.has(n))return!1;var r=this[Q];return u(r),e(r),k(r),r.t.has(n)?r.R.set(n,!1):r.R.delete(n),r.o.delete(n),!0},o.clear=function(){var n=this[Q];u(n),p(n).size&&(e(n),k(n),n.R=new Map,i(n.t,(function(r){n.R.set(r,!1)})),n.o.clear())},o.forEach=function(n,r){var t=this;p(this[Q]).forEach((function(e,i){n.call(r,t.get(i),i,t)}))},o.get=function(n){var r=this[Q];u(r);var i=p(r).get(n);if(r.I||!t(i))return i;if(i!==r.t.get(n))return i;var o=N(r.A.h,i,r);return e(r),r.o.set(n,o),o},o.keys=function(){return p(this[Q]).keys()},o.values=function(){var n,r=this,t=this.keys();return(n={})[V]=function(){return r.values()},n.next=function(){var n=t.next();return n.done?n:{done:!1,value:r.get(n.value)}},n},o.entries=function(){var n,r=this,t=this.keys();return(n={})[V]=function(){return r.entries()},n.next=function(){var n=t.next();if(n.done)return n;var e=r.get(n.value);return{done:!1,value:[n.value,e]}},n},o[V]=function(){return this.entries()},n}(),c=function(){function n(n,r){return this[Q]={i:3,l:r,A:r?r.A:_(),P:!1,I:!1,o:void 0,t:n,k:this,p:new Map,g:!1,C:!1},this}r(n,Set);var t=n.prototype;return Object.defineProperty(t,"size",{get:function(){return p(this[Q]).size}}),t.has=function(n){var r=this[Q];return u(r),r.o?!!r.o.has(n)||!(!r.p.has(n)||!r.o.has(r.p.get(n))):r.t.has(n)},t.add=function(n){var r=this[Q];return u(r),this.has(n)||(o(r),k(r),r.o.add(n)),this},t.delete=function(n){if(!this.has(n))return!1;var r=this[Q];return u(r),o(r),k(r),r.o.delete(n)||!!r.p.has(n)&&r.o.delete(r.p.get(n))},t.clear=function(){var n=this[Q];u(n),p(n).size&&(o(n),k(n),n.o.clear())},t.values=function(){var n=this[Q];return u(n),o(n),n.o.values()},t.entries=function(){var n=this[Q];return u(n),o(n),n.o.entries()},t.keys=function(){return this.values()},t[V]=function(){return this.values()},t.forEach=function(n,r){for(var t=this.values(),e=t.next();!e.done;)n.call(r,e.value,e.value,this),e=t.next()},n}();m("MapSet",{F:function(n,r){return new f(n,r)},T:function(n,r){return new c(n,r)}})}function J(){F(),C(),T()}function K(n){return n}function $(n){return n}var G,U,W="undefined"!=typeof Symbol&&"symbol"==typeof Symbol("x"),X="undefined"!=typeof Map,q="undefined"!=typeof Set,B="undefined"!=typeof Proxy&&void 0!==Proxy.revocable&&"undefined"!=typeof Reflect,H=W?Symbol.for("immer-nothing"):((G={})["immer-nothing"]=!0,G),L=W?Symbol.for("immer-draftable"):"__$immer_draftable",Q=W?Symbol.for("immer-state"):"__$immer_state",V="undefined"!=typeof Symbol&&Symbol.iterator||"@@iterator",Y={0:"Illegal state",1:"Immer drafts cannot have computed properties",2:"This object has been frozen and should not be mutated",3:function(n){return"Cannot use a proxy that has been revoked. Did you pass an object from inside an immer function to an async process? "+n},4:"An immer producer returned a new value *and* modified its draft. Either return a new value *or* modify the draft.",5:"Immer forbids circular references",6:"The first or second argument to `produce` must be a function",7:"The third argument to `produce` must be a function or undefined",8:"First argument to `createDraft` must be a plain object, an array, or an immerable object",9:"First argument to `finishDraft` must be a draft returned by `createDraft`",10:"The given draft is already finalized",11:"Object.defineProperty() cannot be used on an Immer draft",12:"Object.setPrototypeOf() cannot be used on an Immer draft",13:"Immer only supports deleting array indices",14:"Immer only supports setting array indices and the 'length' property",15:function(n){return"Cannot apply patch, path doesn't resolve: "+n},16:'Sets cannot have "replace" patches.',17:function(n){return"Unsupported patch operation: "+n},18:function(n){return"The plugin for '"+n+"' has not been loaded into Immer. To enable the plugin, import and call `enable"+n+"()` when initializing your application."},20:"Cannot use proxies if Proxy, Proxy.revocable or Reflect are not available",21:function(n){return"produce can only be called on things that are draftable: plain objects, arrays, Map, Set or classes that are marked with '[immerable]: true'. Got '"+n+"'"},22:function(n){return"'current' expects a draft, got: "+n},23:function(n){return"'original' expects a draft, got: "+n},24:"Patching reserved attributes like __proto__, prototype and constructor is not allowed"},Z=""+Object.prototype.constructor,nn="undefined"!=typeof Reflect&&Reflect.ownKeys?Reflect.ownKeys:void 0!==Object.getOwnPropertySymbols?function(n){return Object.getOwnPropertyNames(n).concat(Object.getOwnPropertySymbols(n))}:Object.getOwnPropertyNames,rn=Object.getOwnPropertyDescriptors||function(n){var r={};return nn(n).forEach((function(t){r[t]=Object.getOwnPropertyDescriptor(n,t)})),r},tn={},en={get:function(n,r){if(r===Q)return n;var e=p(n);if(!u(e,r))return function(n,r,t){var e,i=I(r,t);return i?"value"in i?i.value:null===(e=i.get)||void 0===e?void 0:e.call(n.k):void 0}(n,e,r);var i=e[r];return n.I||!t(i)?i:i===z(n.t,r)?(E(n),n.o[r]=N(n.A.h,i,n)):i},has:function(n,r){return r in p(n)},ownKeys:function(n){return Reflect.ownKeys(p(n))},set:function(n,r,t){var e=I(p(n),r);if(null==e?void 0:e.set)return e.set.call(n.k,t),!0;if(!n.P){var i=z(p(n),r),o=null==i?void 0:i[Q];if(o&&o.t===t)return n.o[r]=t,n.R[r]=!1,!0;if(c(t,i)&&(void 0!==t||u(n.t,r)))return!0;E(n),k(n)}return n.o[r]===t&&(void 0!==t||r in n.o)||Number.isNaN(t)&&Number.isNaN(n.o[r])||(n.o[r]=t,n.R[r]=!0),!0},deleteProperty:function(n,r){return void 0!==z(n.t,r)||r in n.t?(n.R[r]=!1,E(n),k(n)):delete n.R[r],n.o&&delete n.o[r],!0},getOwnPropertyDescriptor:function(n,r){var t=p(n),e=Reflect.getOwnPropertyDescriptor(t,r);return e?{writable:!0,configurable:1!==n.i||"length"!==r,enumerable:e.enumerable,value:t[r]}:e},defineProperty:function(){n(11)},getPrototypeOf:function(n){return Object.getPrototypeOf(n.t)},setPrototypeOf:function(){n(12)}},on={};i(en,(function(n,r){on[n]=function(){return arguments[0]=arguments[0][0],r.apply(this,arguments)}})),on.deleteProperty=function(r,t){return true&&isNaN(parseInt(t))&&n(13),on.set.call(this,r,t,void 0)},on.set=function(r,t,e){return true&&"length"!==t&&isNaN(parseInt(t))&&n(14),en.set.call(this,r[0],t,e,r[0])};var un=function(){function e(r){var e=this;this.O=B,this.D=!0,this.produce=function(r,i,o){if("function"==typeof r&&"function"!=typeof i){var u=i;i=r;var a=e;return function(n){var r=this;void 0===n&&(n=u);for(var t=arguments.length,e=Array(t>1?t-1:0),o=1;o<t;o++)e[o-1]=arguments[o];return a.produce(n,(function(n){var t;return(t=i).call.apply(t,[r,n].concat(e))}))}}var f;if("function"!=typeof i&&n(6),void 0!==o&&"function"!=typeof o&&n(7),t(r)){var c=w(e),s=N(e,r,void 0),v=!0;try{f=i(s),v=!1}finally{v?g(c):O(c)}return"undefined"!=typeof Promise&&f instanceof Promise?f.then((function(n){return j(c,o),P(n,c)}),(function(n){throw g(c),n})):(j(c,o),P(f,c))}if(!r||"object"!=typeof r){if(void 0===(f=i(r))&&(f=r),f===H&&(f=void 0),e.D&&d(f,!0),o){var p=[],l=[];b("Patches").M(r,f,p,l),o(p,l)}return f}n(21,r)},this.produceWithPatches=function(n,r){if("function"==typeof n)return function(r){for(var t=arguments.length,i=Array(t>1?t-1:0),o=1;o<t;o++)i[o-1]=arguments[o];return e.produceWithPatches(r,(function(r){return n.apply(void 0,[r].concat(i))}))};var t,i,o=e.produce(n,r,(function(n,r){t=n,i=r}));return"undefined"!=typeof Promise&&o instanceof Promise?o.then((function(n){return[n,t,i]})):[o,t,i]},"boolean"==typeof(null==r?void 0:r.useProxies)&&this.setUseProxies(r.useProxies),"boolean"==typeof(null==r?void 0:r.autoFreeze)&&this.setAutoFreeze(r.autoFreeze)}var i=e.prototype;return i.createDraft=function(e){t(e)||n(8),r(e)&&(e=R(e));var i=w(this),o=N(this,e,void 0);return o[Q].C=!0,O(i),o},i.finishDraft=function(r,t){var e=r&&r[Q]; true&&(e&&e.C||n(9),e.I&&n(10));var i=e.A;return j(i,t),P(void 0,i)},i.setAutoFreeze=function(n){this.D=n},i.setUseProxies=function(r){r&&!B&&n(20),this.O=r},i.applyPatches=function(n,t){var e;for(e=t.length-1;e>=0;e--){var i=t[e];if(0===i.path.length&&"replace"===i.op){n=i.value;break}}e>-1&&(t=t.slice(e+1));var o=b("Patches").$;return r(n)?o(n,t):this.produce(n,(function(n){return o(n,t)}))},e}(),an=new un,fn=an.produce,cn=an.produceWithPatches.bind(an),sn=an.setAutoFreeze.bind(an),vn=an.setUseProxies.bind(an),pn=an.applyPatches.bind(an),ln=an.createDraft.bind(an),dn=an.finishDraft.bind(an);/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (fn);
//# sourceMappingURL=immer.esm.js.map


/***/ }),

/***/ "../node_modules/redux-thunk/es/index.js":
/*!***********************************************!*\
  !*** ../node_modules/redux-thunk/es/index.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/** A function that accepts a potential "extra argument" value to be injected later,
 * and returns an instance of the thunk middleware that uses that value
 */
function createThunkMiddleware(extraArgument) {
  // Standard Redux middleware definition pattern:
  // See: https://redux.js.org/tutorials/fundamentals/part-4-store#writing-custom-middleware
  var middleware = function middleware(_ref) {
    var dispatch = _ref.dispatch,
        getState = _ref.getState;
    return function (next) {
      return function (action) {
        // The thunk middleware looks for any functions that were passed to `store.dispatch`.
        // If this "action" is really a function, call it and return the result.
        if (typeof action === 'function') {
          // Inject the store's `dispatch` and `getState` methods, as well as any "extra arg"
          return action(dispatch, getState, extraArgument);
        } // Otherwise, pass the action down the middleware chain as usual


        return next(action);
      };
    };
  };

  return middleware;
}

var thunk = createThunkMiddleware(); // Attach the factory function so users can create a customized version
// with whatever "extra arg" they want to inject into their thunks

thunk.withExtraArgument = createThunkMiddleware;
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (thunk);

/***/ }),

/***/ "../node_modules/redux/es/redux.js":
/*!*****************************************!*\
  !*** ../node_modules/redux/es/redux.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   __DO_NOT_USE__ActionTypes: () => (/* binding */ ActionTypes),
/* harmony export */   applyMiddleware: () => (/* binding */ applyMiddleware),
/* harmony export */   bindActionCreators: () => (/* binding */ bindActionCreators),
/* harmony export */   combineReducers: () => (/* binding */ combineReducers),
/* harmony export */   compose: () => (/* binding */ compose),
/* harmony export */   createStore: () => (/* binding */ createStore),
/* harmony export */   legacy_createStore: () => (/* binding */ legacy_createStore)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread2__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread2 */ "../node_modules/@babel/runtime/helpers/esm/objectSpread2.js");


/**
 * Adapted from React: https://github.com/facebook/react/blob/master/packages/shared/formatProdErrorMessage.js
 *
 * Do not require this module directly! Use normal throw error calls. These messages will be replaced with error codes
 * during build.
 * @param {number} code
 */
function formatProdErrorMessage(code) {
  return "Minified Redux error #" + code + "; visit https://redux.js.org/Errors?code=" + code + " for the full message or " + 'use the non-minified dev environment for full errors. ';
}

// Inlined version of the `symbol-observable` polyfill
var $$observable = (function () {
  return typeof Symbol === 'function' && Symbol.observable || '@@observable';
})();

/**
 * These are private action types reserved by Redux.
 * For any unknown actions, you must return the current state.
 * If the current state is undefined, you must return the initial state.
 * Do not reference these action types directly in your code.
 */
var randomString = function randomString() {
  return Math.random().toString(36).substring(7).split('').join('.');
};

var ActionTypes = {
  INIT: "@@redux/INIT" + randomString(),
  REPLACE: "@@redux/REPLACE" + randomString(),
  PROBE_UNKNOWN_ACTION: function PROBE_UNKNOWN_ACTION() {
    return "@@redux/PROBE_UNKNOWN_ACTION" + randomString();
  }
};

/**
 * @param {any} obj The object to inspect.
 * @returns {boolean} True if the argument appears to be a plain object.
 */
function isPlainObject(obj) {
  if (typeof obj !== 'object' || obj === null) return false;
  var proto = obj;

  while (Object.getPrototypeOf(proto) !== null) {
    proto = Object.getPrototypeOf(proto);
  }

  return Object.getPrototypeOf(obj) === proto;
}

// Inlined / shortened version of `kindOf` from https://github.com/jonschlinkert/kind-of
function miniKindOf(val) {
  if (val === void 0) return 'undefined';
  if (val === null) return 'null';
  var type = typeof val;

  switch (type) {
    case 'boolean':
    case 'string':
    case 'number':
    case 'symbol':
    case 'function':
      {
        return type;
      }
  }

  if (Array.isArray(val)) return 'array';
  if (isDate(val)) return 'date';
  if (isError(val)) return 'error';
  var constructorName = ctorName(val);

  switch (constructorName) {
    case 'Symbol':
    case 'Promise':
    case 'WeakMap':
    case 'WeakSet':
    case 'Map':
    case 'Set':
      return constructorName;
  } // other


  return type.slice(8, -1).toLowerCase().replace(/\s/g, '');
}

function ctorName(val) {
  return typeof val.constructor === 'function' ? val.constructor.name : null;
}

function isError(val) {
  return val instanceof Error || typeof val.message === 'string' && val.constructor && typeof val.constructor.stackTraceLimit === 'number';
}

function isDate(val) {
  if (val instanceof Date) return true;
  return typeof val.toDateString === 'function' && typeof val.getDate === 'function' && typeof val.setDate === 'function';
}

function kindOf(val) {
  var typeOfVal = typeof val;

  if (true) {
    typeOfVal = miniKindOf(val);
  }

  return typeOfVal;
}

/**
 * @deprecated
 *
 * **We recommend using the `configureStore` method
 * of the `@reduxjs/toolkit` package**, which replaces `createStore`.
 *
 * Redux Toolkit is our recommended approach for writing Redux logic today,
 * including store setup, reducers, data fetching, and more.
 *
 * **For more details, please read this Redux docs page:**
 * **https://redux.js.org/introduction/why-rtk-is-redux-today**
 *
 * `configureStore` from Redux Toolkit is an improved version of `createStore` that
 * simplifies setup and helps avoid common bugs.
 *
 * You should not be using the `redux` core package by itself today, except for learning purposes.
 * The `createStore` method from the core `redux` package will not be removed, but we encourage
 * all users to migrate to using Redux Toolkit for all Redux code.
 *
 * If you want to use `createStore` without this visual deprecation warning, use
 * the `legacy_createStore` import instead:
 *
 * `import { legacy_createStore as createStore} from 'redux'`
 *
 */

function createStore(reducer, preloadedState, enhancer) {
  var _ref2;

  if (typeof preloadedState === 'function' && typeof enhancer === 'function' || typeof enhancer === 'function' && typeof arguments[3] === 'function') {
    throw new Error( false ? 0 : 'It looks like you are passing several store enhancers to ' + 'createStore(). This is not supported. Instead, compose them ' + 'together to a single function. See https://redux.js.org/tutorials/fundamentals/part-4-store#creating-a-store-with-enhancers for an example.');
  }

  if (typeof preloadedState === 'function' && typeof enhancer === 'undefined') {
    enhancer = preloadedState;
    preloadedState = undefined;
  }

  if (typeof enhancer !== 'undefined') {
    if (typeof enhancer !== 'function') {
      throw new Error( false ? 0 : "Expected the enhancer to be a function. Instead, received: '" + kindOf(enhancer) + "'");
    }

    return enhancer(createStore)(reducer, preloadedState);
  }

  if (typeof reducer !== 'function') {
    throw new Error( false ? 0 : "Expected the root reducer to be a function. Instead, received: '" + kindOf(reducer) + "'");
  }

  var currentReducer = reducer;
  var currentState = preloadedState;
  var currentListeners = [];
  var nextListeners = currentListeners;
  var isDispatching = false;
  /**
   * This makes a shallow copy of currentListeners so we can use
   * nextListeners as a temporary list while dispatching.
   *
   * This prevents any bugs around consumers calling
   * subscribe/unsubscribe in the middle of a dispatch.
   */

  function ensureCanMutateNextListeners() {
    if (nextListeners === currentListeners) {
      nextListeners = currentListeners.slice();
    }
  }
  /**
   * Reads the state tree managed by the store.
   *
   * @returns {any} The current state tree of your application.
   */


  function getState() {
    if (isDispatching) {
      throw new Error( false ? 0 : 'You may not call store.getState() while the reducer is executing. ' + 'The reducer has already received the state as an argument. ' + 'Pass it down from the top reducer instead of reading it from the store.');
    }

    return currentState;
  }
  /**
   * Adds a change listener. It will be called any time an action is dispatched,
   * and some part of the state tree may potentially have changed. You may then
   * call `getState()` to read the current state tree inside the callback.
   *
   * You may call `dispatch()` from a change listener, with the following
   * caveats:
   *
   * 1. The subscriptions are snapshotted just before every `dispatch()` call.
   * If you subscribe or unsubscribe while the listeners are being invoked, this
   * will not have any effect on the `dispatch()` that is currently in progress.
   * However, the next `dispatch()` call, whether nested or not, will use a more
   * recent snapshot of the subscription list.
   *
   * 2. The listener should not expect to see all state changes, as the state
   * might have been updated multiple times during a nested `dispatch()` before
   * the listener is called. It is, however, guaranteed that all subscribers
   * registered before the `dispatch()` started will be called with the latest
   * state by the time it exits.
   *
   * @param {Function} listener A callback to be invoked on every dispatch.
   * @returns {Function} A function to remove this change listener.
   */


  function subscribe(listener) {
    if (typeof listener !== 'function') {
      throw new Error( false ? 0 : "Expected the listener to be a function. Instead, received: '" + kindOf(listener) + "'");
    }

    if (isDispatching) {
      throw new Error( false ? 0 : 'You may not call store.subscribe() while the reducer is executing. ' + 'If you would like to be notified after the store has been updated, subscribe from a ' + 'component and invoke store.getState() in the callback to access the latest state. ' + 'See https://redux.js.org/api/store#subscribelistener for more details.');
    }

    var isSubscribed = true;
    ensureCanMutateNextListeners();
    nextListeners.push(listener);
    return function unsubscribe() {
      if (!isSubscribed) {
        return;
      }

      if (isDispatching) {
        throw new Error( false ? 0 : 'You may not unsubscribe from a store listener while the reducer is executing. ' + 'See https://redux.js.org/api/store#subscribelistener for more details.');
      }

      isSubscribed = false;
      ensureCanMutateNextListeners();
      var index = nextListeners.indexOf(listener);
      nextListeners.splice(index, 1);
      currentListeners = null;
    };
  }
  /**
   * Dispatches an action. It is the only way to trigger a state change.
   *
   * The `reducer` function, used to create the store, will be called with the
   * current state tree and the given `action`. Its return value will
   * be considered the **next** state of the tree, and the change listeners
   * will be notified.
   *
   * The base implementation only supports plain object actions. If you want to
   * dispatch a Promise, an Observable, a thunk, or something else, you need to
   * wrap your store creating function into the corresponding middleware. For
   * example, see the documentation for the `redux-thunk` package. Even the
   * middleware will eventually dispatch plain object actions using this method.
   *
   * @param {Object} action A plain object representing “what changed”. It is
   * a good idea to keep actions serializable so you can record and replay user
   * sessions, or use the time travelling `redux-devtools`. An action must have
   * a `type` property which may not be `undefined`. It is a good idea to use
   * string constants for action types.
   *
   * @returns {Object} For convenience, the same action object you dispatched.
   *
   * Note that, if you use a custom middleware, it may wrap `dispatch()` to
   * return something else (for example, a Promise you can await).
   */


  function dispatch(action) {
    if (!isPlainObject(action)) {
      throw new Error( false ? 0 : "Actions must be plain objects. Instead, the actual type was: '" + kindOf(action) + "'. You may need to add middleware to your store setup to handle dispatching other values, such as 'redux-thunk' to handle dispatching functions. See https://redux.js.org/tutorials/fundamentals/part-4-store#middleware and https://redux.js.org/tutorials/fundamentals/part-6-async-logic#using-the-redux-thunk-middleware for examples.");
    }

    if (typeof action.type === 'undefined') {
      throw new Error( false ? 0 : 'Actions may not have an undefined "type" property. You may have misspelled an action type string constant.');
    }

    if (isDispatching) {
      throw new Error( false ? 0 : 'Reducers may not dispatch actions.');
    }

    try {
      isDispatching = true;
      currentState = currentReducer(currentState, action);
    } finally {
      isDispatching = false;
    }

    var listeners = currentListeners = nextListeners;

    for (var i = 0; i < listeners.length; i++) {
      var listener = listeners[i];
      listener();
    }

    return action;
  }
  /**
   * Replaces the reducer currently used by the store to calculate the state.
   *
   * You might need this if your app implements code splitting and you want to
   * load some of the reducers dynamically. You might also need this if you
   * implement a hot reloading mechanism for Redux.
   *
   * @param {Function} nextReducer The reducer for the store to use instead.
   * @returns {void}
   */


  function replaceReducer(nextReducer) {
    if (typeof nextReducer !== 'function') {
      throw new Error( false ? 0 : "Expected the nextReducer to be a function. Instead, received: '" + kindOf(nextReducer));
    }

    currentReducer = nextReducer; // This action has a similiar effect to ActionTypes.INIT.
    // Any reducers that existed in both the new and old rootReducer
    // will receive the previous state. This effectively populates
    // the new state tree with any relevant data from the old one.

    dispatch({
      type: ActionTypes.REPLACE
    });
  }
  /**
   * Interoperability point for observable/reactive libraries.
   * @returns {observable} A minimal observable of state changes.
   * For more information, see the observable proposal:
   * https://github.com/tc39/proposal-observable
   */


  function observable() {
    var _ref;

    var outerSubscribe = subscribe;
    return _ref = {
      /**
       * The minimal observable subscription method.
       * @param {Object} observer Any object that can be used as an observer.
       * The observer object should have a `next` method.
       * @returns {subscription} An object with an `unsubscribe` method that can
       * be used to unsubscribe the observable from the store, and prevent further
       * emission of values from the observable.
       */
      subscribe: function subscribe(observer) {
        if (typeof observer !== 'object' || observer === null) {
          throw new Error( false ? 0 : "Expected the observer to be an object. Instead, received: '" + kindOf(observer) + "'");
        }

        function observeState() {
          if (observer.next) {
            observer.next(getState());
          }
        }

        observeState();
        var unsubscribe = outerSubscribe(observeState);
        return {
          unsubscribe: unsubscribe
        };
      }
    }, _ref[$$observable] = function () {
      return this;
    }, _ref;
  } // When a store is created, an "INIT" action is dispatched so that every
  // reducer returns their initial state. This effectively populates
  // the initial state tree.


  dispatch({
    type: ActionTypes.INIT
  });
  return _ref2 = {
    dispatch: dispatch,
    subscribe: subscribe,
    getState: getState,
    replaceReducer: replaceReducer
  }, _ref2[$$observable] = observable, _ref2;
}
/**
 * Creates a Redux store that holds the state tree.
 *
 * **We recommend using `configureStore` from the
 * `@reduxjs/toolkit` package**, which replaces `createStore`:
 * **https://redux.js.org/introduction/why-rtk-is-redux-today**
 *
 * The only way to change the data in the store is to call `dispatch()` on it.
 *
 * There should only be a single store in your app. To specify how different
 * parts of the state tree respond to actions, you may combine several reducers
 * into a single reducer function by using `combineReducers`.
 *
 * @param {Function} reducer A function that returns the next state tree, given
 * the current state tree and the action to handle.
 *
 * @param {any} [preloadedState] The initial state. You may optionally specify it
 * to hydrate the state from the server in universal apps, or to restore a
 * previously serialized user session.
 * If you use `combineReducers` to produce the root reducer function, this must be
 * an object with the same shape as `combineReducers` keys.
 *
 * @param {Function} [enhancer] The store enhancer. You may optionally specify it
 * to enhance the store with third-party capabilities such as middleware,
 * time travel, persistence, etc. The only store enhancer that ships with Redux
 * is `applyMiddleware()`.
 *
 * @returns {Store} A Redux store that lets you read the state, dispatch actions
 * and subscribe to changes.
 */

var legacy_createStore = createStore;

/**
 * Prints a warning in the console if it exists.
 *
 * @param {String} message The warning message.
 * @returns {void}
 */
function warning(message) {
  /* eslint-disable no-console */
  if (typeof console !== 'undefined' && typeof console.error === 'function') {
    console.error(message);
  }
  /* eslint-enable no-console */


  try {
    // This error was thrown as a convenience so that if you enable
    // "break on all exceptions" in your console,
    // it would pause the execution at this line.
    throw new Error(message);
  } catch (e) {} // eslint-disable-line no-empty

}

function getUnexpectedStateShapeWarningMessage(inputState, reducers, action, unexpectedKeyCache) {
  var reducerKeys = Object.keys(reducers);
  var argumentName = action && action.type === ActionTypes.INIT ? 'preloadedState argument passed to createStore' : 'previous state received by the reducer';

  if (reducerKeys.length === 0) {
    return 'Store does not have a valid reducer. Make sure the argument passed ' + 'to combineReducers is an object whose values are reducers.';
  }

  if (!isPlainObject(inputState)) {
    return "The " + argumentName + " has unexpected type of \"" + kindOf(inputState) + "\". Expected argument to be an object with the following " + ("keys: \"" + reducerKeys.join('", "') + "\"");
  }

  var unexpectedKeys = Object.keys(inputState).filter(function (key) {
    return !reducers.hasOwnProperty(key) && !unexpectedKeyCache[key];
  });
  unexpectedKeys.forEach(function (key) {
    unexpectedKeyCache[key] = true;
  });
  if (action && action.type === ActionTypes.REPLACE) return;

  if (unexpectedKeys.length > 0) {
    return "Unexpected " + (unexpectedKeys.length > 1 ? 'keys' : 'key') + " " + ("\"" + unexpectedKeys.join('", "') + "\" found in " + argumentName + ". ") + "Expected to find one of the known reducer keys instead: " + ("\"" + reducerKeys.join('", "') + "\". Unexpected keys will be ignored.");
  }
}

function assertReducerShape(reducers) {
  Object.keys(reducers).forEach(function (key) {
    var reducer = reducers[key];
    var initialState = reducer(undefined, {
      type: ActionTypes.INIT
    });

    if (typeof initialState === 'undefined') {
      throw new Error( false ? 0 : "The slice reducer for key \"" + key + "\" returned undefined during initialization. " + "If the state passed to the reducer is undefined, you must " + "explicitly return the initial state. The initial state may " + "not be undefined. If you don't want to set a value for this reducer, " + "you can use null instead of undefined.");
    }

    if (typeof reducer(undefined, {
      type: ActionTypes.PROBE_UNKNOWN_ACTION()
    }) === 'undefined') {
      throw new Error( false ? 0 : "The slice reducer for key \"" + key + "\" returned undefined when probed with a random type. " + ("Don't try to handle '" + ActionTypes.INIT + "' or other actions in \"redux/*\" ") + "namespace. They are considered private. Instead, you must return the " + "current state for any unknown actions, unless it is undefined, " + "in which case you must return the initial state, regardless of the " + "action type. The initial state may not be undefined, but can be null.");
    }
  });
}
/**
 * Turns an object whose values are different reducer functions, into a single
 * reducer function. It will call every child reducer, and gather their results
 * into a single state object, whose keys correspond to the keys of the passed
 * reducer functions.
 *
 * @param {Object} reducers An object whose values correspond to different
 * reducer functions that need to be combined into one. One handy way to obtain
 * it is to use ES6 `import * as reducers` syntax. The reducers may never return
 * undefined for any action. Instead, they should return their initial state
 * if the state passed to them was undefined, and the current state for any
 * unrecognized action.
 *
 * @returns {Function} A reducer function that invokes every reducer inside the
 * passed object, and builds a state object with the same shape.
 */


function combineReducers(reducers) {
  var reducerKeys = Object.keys(reducers);
  var finalReducers = {};

  for (var i = 0; i < reducerKeys.length; i++) {
    var key = reducerKeys[i];

    if (true) {
      if (typeof reducers[key] === 'undefined') {
        warning("No reducer provided for key \"" + key + "\"");
      }
    }

    if (typeof reducers[key] === 'function') {
      finalReducers[key] = reducers[key];
    }
  }

  var finalReducerKeys = Object.keys(finalReducers); // This is used to make sure we don't warn about the same
  // keys multiple times.

  var unexpectedKeyCache;

  if (true) {
    unexpectedKeyCache = {};
  }

  var shapeAssertionError;

  try {
    assertReducerShape(finalReducers);
  } catch (e) {
    shapeAssertionError = e;
  }

  return function combination(state, action) {
    if (state === void 0) {
      state = {};
    }

    if (shapeAssertionError) {
      throw shapeAssertionError;
    }

    if (true) {
      var warningMessage = getUnexpectedStateShapeWarningMessage(state, finalReducers, action, unexpectedKeyCache);

      if (warningMessage) {
        warning(warningMessage);
      }
    }

    var hasChanged = false;
    var nextState = {};

    for (var _i = 0; _i < finalReducerKeys.length; _i++) {
      var _key = finalReducerKeys[_i];
      var reducer = finalReducers[_key];
      var previousStateForKey = state[_key];
      var nextStateForKey = reducer(previousStateForKey, action);

      if (typeof nextStateForKey === 'undefined') {
        var actionType = action && action.type;
        throw new Error( false ? 0 : "When called with an action of type " + (actionType ? "\"" + String(actionType) + "\"" : '(unknown type)') + ", the slice reducer for key \"" + _key + "\" returned undefined. " + "To ignore an action, you must explicitly return the previous state. " + "If you want this reducer to hold no value, you can return null instead of undefined.");
      }

      nextState[_key] = nextStateForKey;
      hasChanged = hasChanged || nextStateForKey !== previousStateForKey;
    }

    hasChanged = hasChanged || finalReducerKeys.length !== Object.keys(state).length;
    return hasChanged ? nextState : state;
  };
}

function bindActionCreator(actionCreator, dispatch) {
  return function () {
    return dispatch(actionCreator.apply(this, arguments));
  };
}
/**
 * Turns an object whose values are action creators, into an object with the
 * same keys, but with every function wrapped into a `dispatch` call so they
 * may be invoked directly. This is just a convenience method, as you can call
 * `store.dispatch(MyActionCreators.doSomething())` yourself just fine.
 *
 * For convenience, you can also pass an action creator as the first argument,
 * and get a dispatch wrapped function in return.
 *
 * @param {Function|Object} actionCreators An object whose values are action
 * creator functions. One handy way to obtain it is to use ES6 `import * as`
 * syntax. You may also pass a single function.
 *
 * @param {Function} dispatch The `dispatch` function available on your Redux
 * store.
 *
 * @returns {Function|Object} The object mimicking the original object, but with
 * every action creator wrapped into the `dispatch` call. If you passed a
 * function as `actionCreators`, the return value will also be a single
 * function.
 */


function bindActionCreators(actionCreators, dispatch) {
  if (typeof actionCreators === 'function') {
    return bindActionCreator(actionCreators, dispatch);
  }

  if (typeof actionCreators !== 'object' || actionCreators === null) {
    throw new Error( false ? 0 : "bindActionCreators expected an object or a function, but instead received: '" + kindOf(actionCreators) + "'. " + "Did you write \"import ActionCreators from\" instead of \"import * as ActionCreators from\"?");
  }

  var boundActionCreators = {};

  for (var key in actionCreators) {
    var actionCreator = actionCreators[key];

    if (typeof actionCreator === 'function') {
      boundActionCreators[key] = bindActionCreator(actionCreator, dispatch);
    }
  }

  return boundActionCreators;
}

/**
 * Composes single-argument functions from right to left. The rightmost
 * function can take multiple arguments as it provides the signature for
 * the resulting composite function.
 *
 * @param {...Function} funcs The functions to compose.
 * @returns {Function} A function obtained by composing the argument functions
 * from right to left. For example, compose(f, g, h) is identical to doing
 * (...args) => f(g(h(...args))).
 */
function compose() {
  for (var _len = arguments.length, funcs = new Array(_len), _key = 0; _key < _len; _key++) {
    funcs[_key] = arguments[_key];
  }

  if (funcs.length === 0) {
    return function (arg) {
      return arg;
    };
  }

  if (funcs.length === 1) {
    return funcs[0];
  }

  return funcs.reduce(function (a, b) {
    return function () {
      return a(b.apply(void 0, arguments));
    };
  });
}

/**
 * Creates a store enhancer that applies middleware to the dispatch method
 * of the Redux store. This is handy for a variety of tasks, such as expressing
 * asynchronous actions in a concise manner, or logging every action payload.
 *
 * See `redux-thunk` package as an example of the Redux middleware.
 *
 * Because middleware is potentially asynchronous, this should be the first
 * store enhancer in the composition chain.
 *
 * Note that each middleware will be given the `dispatch` and `getState` functions
 * as named arguments.
 *
 * @param {...Function} middlewares The middleware chain to be applied.
 * @returns {Function} A store enhancer applying the middleware.
 */

function applyMiddleware() {
  for (var _len = arguments.length, middlewares = new Array(_len), _key = 0; _key < _len; _key++) {
    middlewares[_key] = arguments[_key];
  }

  return function (createStore) {
    return function () {
      var store = createStore.apply(void 0, arguments);

      var _dispatch = function dispatch() {
        throw new Error( false ? 0 : 'Dispatching while constructing your middleware is not allowed. ' + 'Other middleware would not be applied to this dispatch.');
      };

      var middlewareAPI = {
        getState: store.getState,
        dispatch: function dispatch() {
          return _dispatch.apply(void 0, arguments);
        }
      };
      var chain = middlewares.map(function (middleware) {
        return middleware(middlewareAPI);
      });
      _dispatch = compose.apply(void 0, chain)(store.dispatch);
      return (0,_babel_runtime_helpers_esm_objectSpread2__WEBPACK_IMPORTED_MODULE_0__["default"])((0,_babel_runtime_helpers_esm_objectSpread2__WEBPACK_IMPORTED_MODULE_0__["default"])({}, store), {}, {
        dispatch: _dispatch
      });
    };
  };
}




/***/ }),

/***/ "../node_modules/reselect/es/defaultMemoize.js":
/*!*****************************************************!*\
  !*** ../node_modules/reselect/es/defaultMemoize.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createCacheKeyComparator: () => (/* binding */ createCacheKeyComparator),
/* harmony export */   defaultEqualityCheck: () => (/* binding */ defaultEqualityCheck),
/* harmony export */   defaultMemoize: () => (/* binding */ defaultMemoize)
/* harmony export */ });
// Cache implementation based on Erik Rasmussen's `lru-memoize`:
// https://github.com/erikras/lru-memoize
var NOT_FOUND = 'NOT_FOUND';

function createSingletonCache(equals) {
  var entry;
  return {
    get: function get(key) {
      if (entry && equals(entry.key, key)) {
        return entry.value;
      }

      return NOT_FOUND;
    },
    put: function put(key, value) {
      entry = {
        key: key,
        value: value
      };
    },
    getEntries: function getEntries() {
      return entry ? [entry] : [];
    },
    clear: function clear() {
      entry = undefined;
    }
  };
}

function createLruCache(maxSize, equals) {
  var entries = [];

  function get(key) {
    var cacheIndex = entries.findIndex(function (entry) {
      return equals(key, entry.key);
    }); // We found a cached entry

    if (cacheIndex > -1) {
      var entry = entries[cacheIndex]; // Cached entry not at top of cache, move it to the top

      if (cacheIndex > 0) {
        entries.splice(cacheIndex, 1);
        entries.unshift(entry);
      }

      return entry.value;
    } // No entry found in cache, return sentinel


    return NOT_FOUND;
  }

  function put(key, value) {
    if (get(key) === NOT_FOUND) {
      // TODO Is unshift slow?
      entries.unshift({
        key: key,
        value: value
      });

      if (entries.length > maxSize) {
        entries.pop();
      }
    }
  }

  function getEntries() {
    return entries;
  }

  function clear() {
    entries = [];
  }

  return {
    get: get,
    put: put,
    getEntries: getEntries,
    clear: clear
  };
}

var defaultEqualityCheck = function defaultEqualityCheck(a, b) {
  return a === b;
};
function createCacheKeyComparator(equalityCheck) {
  return function areArgumentsShallowlyEqual(prev, next) {
    if (prev === null || next === null || prev.length !== next.length) {
      return false;
    } // Do this in a for loop (and not a `forEach` or an `every`) so we can determine equality as fast as possible.


    var length = prev.length;

    for (var i = 0; i < length; i++) {
      if (!equalityCheck(prev[i], next[i])) {
        return false;
      }
    }

    return true;
  };
}
// defaultMemoize now supports a configurable cache size with LRU behavior,
// and optional comparison of the result value with existing values
function defaultMemoize(func, equalityCheckOrOptions) {
  var providedOptions = typeof equalityCheckOrOptions === 'object' ? equalityCheckOrOptions : {
    equalityCheck: equalityCheckOrOptions
  };
  var _providedOptions$equa = providedOptions.equalityCheck,
      equalityCheck = _providedOptions$equa === void 0 ? defaultEqualityCheck : _providedOptions$equa,
      _providedOptions$maxS = providedOptions.maxSize,
      maxSize = _providedOptions$maxS === void 0 ? 1 : _providedOptions$maxS,
      resultEqualityCheck = providedOptions.resultEqualityCheck;
  var comparator = createCacheKeyComparator(equalityCheck);
  var cache = maxSize === 1 ? createSingletonCache(comparator) : createLruCache(maxSize, comparator); // we reference arguments instead of spreading them for performance reasons

  function memoized() {
    var value = cache.get(arguments);

    if (value === NOT_FOUND) {
      // @ts-ignore
      value = func.apply(null, arguments);

      if (resultEqualityCheck) {
        var entries = cache.getEntries();
        var matchingEntry = entries.find(function (entry) {
          return resultEqualityCheck(entry.value, value);
        });

        if (matchingEntry) {
          value = matchingEntry.value;
        }
      }

      cache.put(arguments, value);
    }

    return value;
  }

  memoized.clearCache = function () {
    return cache.clear();
  };

  return memoized;
}

/***/ }),

/***/ "../node_modules/reselect/es/index.js":
/*!********************************************!*\
  !*** ../node_modules/reselect/es/index.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createSelector: () => (/* binding */ createSelector),
/* harmony export */   createSelectorCreator: () => (/* binding */ createSelectorCreator),
/* harmony export */   createStructuredSelector: () => (/* binding */ createStructuredSelector),
/* harmony export */   defaultEqualityCheck: () => (/* reexport safe */ _defaultMemoize__WEBPACK_IMPORTED_MODULE_0__.defaultEqualityCheck),
/* harmony export */   defaultMemoize: () => (/* reexport safe */ _defaultMemoize__WEBPACK_IMPORTED_MODULE_0__.defaultMemoize)
/* harmony export */ });
/* harmony import */ var _defaultMemoize__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./defaultMemoize */ "../node_modules/reselect/es/defaultMemoize.js");



function getDependencies(funcs) {
  var dependencies = Array.isArray(funcs[0]) ? funcs[0] : funcs;

  if (!dependencies.every(function (dep) {
    return typeof dep === 'function';
  })) {
    var dependencyTypes = dependencies.map(function (dep) {
      return typeof dep === 'function' ? "function " + (dep.name || 'unnamed') + "()" : typeof dep;
    }).join(', ');
    throw new Error("createSelector expects all input-selectors to be functions, but received the following types: [" + dependencyTypes + "]");
  }

  return dependencies;
}

function createSelectorCreator(memoize) {
  for (var _len = arguments.length, memoizeOptionsFromArgs = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
    memoizeOptionsFromArgs[_key - 1] = arguments[_key];
  }

  var createSelector = function createSelector() {
    for (var _len2 = arguments.length, funcs = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
      funcs[_key2] = arguments[_key2];
    }

    var _recomputations = 0;

    var _lastResult; // Due to the intricacies of rest params, we can't do an optional arg after `...funcs`.
    // So, start by declaring the default value here.
    // (And yes, the words 'memoize' and 'options' appear too many times in this next sequence.)


    var directlyPassedOptions = {
      memoizeOptions: undefined
    }; // Normally, the result func or "output selector" is the last arg

    var resultFunc = funcs.pop(); // If the result func is actually an _object_, assume it's our options object

    if (typeof resultFunc === 'object') {
      directlyPassedOptions = resultFunc; // and pop the real result func off

      resultFunc = funcs.pop();
    }

    if (typeof resultFunc !== 'function') {
      throw new Error("createSelector expects an output function after the inputs, but received: [" + typeof resultFunc + "]");
    } // Determine which set of options we're using. Prefer options passed directly,
    // but fall back to options given to createSelectorCreator.


    var _directlyPassedOption = directlyPassedOptions,
        _directlyPassedOption2 = _directlyPassedOption.memoizeOptions,
        memoizeOptions = _directlyPassedOption2 === void 0 ? memoizeOptionsFromArgs : _directlyPassedOption2; // Simplifying assumption: it's unlikely that the first options arg of the provided memoizer
    // is an array. In most libs I've looked at, it's an equality function or options object.
    // Based on that, if `memoizeOptions` _is_ an array, we assume it's a full
    // user-provided array of options. Otherwise, it must be just the _first_ arg, and so
    // we wrap it in an array so we can apply it.

    var finalMemoizeOptions = Array.isArray(memoizeOptions) ? memoizeOptions : [memoizeOptions];
    var dependencies = getDependencies(funcs);
    var memoizedResultFunc = memoize.apply(void 0, [function recomputationWrapper() {
      _recomputations++; // apply arguments instead of spreading for performance.

      return resultFunc.apply(null, arguments);
    }].concat(finalMemoizeOptions)); // If a selector is called with the exact same arguments we don't need to traverse our dependencies again.

    var selector = memoize(function dependenciesChecker() {
      var params = [];
      var length = dependencies.length;

      for (var i = 0; i < length; i++) {
        // apply arguments instead of spreading and mutate a local list of params for performance.
        // @ts-ignore
        params.push(dependencies[i].apply(null, arguments));
      } // apply arguments instead of spreading for performance.


      _lastResult = memoizedResultFunc.apply(null, params);
      return _lastResult;
    });
    Object.assign(selector, {
      resultFunc: resultFunc,
      memoizedResultFunc: memoizedResultFunc,
      dependencies: dependencies,
      lastResult: function lastResult() {
        return _lastResult;
      },
      recomputations: function recomputations() {
        return _recomputations;
      },
      resetRecomputations: function resetRecomputations() {
        return _recomputations = 0;
      }
    });
    return selector;
  }; // @ts-ignore


  return createSelector;
}
var createSelector = /* #__PURE__ */createSelectorCreator(_defaultMemoize__WEBPACK_IMPORTED_MODULE_0__.defaultMemoize);
// Manual definition of state and output arguments
var createStructuredSelector = function createStructuredSelector(selectors, selectorCreator) {
  if (selectorCreator === void 0) {
    selectorCreator = createSelector;
  }

  if (typeof selectors !== 'object') {
    throw new Error('createStructuredSelector expects first argument to be an object ' + ("where each property is a selector, instead received a " + typeof selectors));
  }

  var objectKeys = Object.keys(selectors);
  var resultSelector = selectorCreator( // @ts-ignore
  objectKeys.map(function (key) {
    return selectors[key];
  }), function () {
    for (var _len3 = arguments.length, values = new Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
      values[_key3] = arguments[_key3];
    }

    return values.reduce(function (composition, value, index) {
      composition[objectKeys[index]] = value;
      return composition;
    }, {});
  });
  return resultSelector;
};

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/global */
/******/ 	(() => {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!*********************************************!*\
  !*** ../modules/web-cli/assets/js/index.js ***!
  \*********************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _api = _interopRequireDefault(__webpack_require__(/*! ./api */ "../modules/web-cli/assets/js/api.js"));
window.$e = new _api.default();
})();

/******/ })()
;
//# sourceMappingURL=web-cli.js.map