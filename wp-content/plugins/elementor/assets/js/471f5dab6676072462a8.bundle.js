(self["webpackChunkelementor"] = self["webpackChunkelementor"] || []).push([["modules_nested-elements_assets_js_editor_module_js"],{

/***/ "../modules/nested-elements/assets/js/editor/component.js":
/*!****************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/component.js ***!
  \****************************************************************/
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
var _component = _interopRequireDefault(__webpack_require__(/*! ./nested-repeater/component */ "../modules/nested-elements/assets/js/editor/nested-repeater/component.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var Component = exports["default"] = /*#__PURE__*/function (_$e$modules$Component) {
  function Component() {
    (0, _classCallCheck2.default)(this, Component);
    return _callSuper(this, Component, arguments);
  }
  (0, _inherits2.default)(Component, _$e$modules$Component);
  return (0, _createClass2.default)(Component, [{
    key: "getNamespace",
    value: function getNamespace() {
      return 'nested-elements';
    }
  }, {
    key: "registerAPI",
    value: function registerAPI() {
      $e.components.register(new _component.default());
      _superPropGet(Component, "registerAPI", this, 3)([]);
    }
  }]);
}($e.modules.ComponentBase);

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/module.js":
/*!*************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/module.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _component = _interopRequireDefault(__webpack_require__(/*! ./component */ "../modules/nested-elements/assets/js/editor/component.js"));
var NestedElementsModule = exports["default"] = /*#__PURE__*/(0, _createClass2.default)(function NestedElementsModule() {
  (0, _classCallCheck2.default)(this, NestedElementsModule);
  this.component = $e.components.register(new _component.default());
});

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/nested-repeater/component.js":
/*!********************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/nested-repeater/component.js ***!
  \********************************************************************************/
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
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _nestedModelBase = _interopRequireDefault(__webpack_require__(/*! ./models/nested-model-base */ "../modules/nested-elements/assets/js/editor/nested-repeater/models/nested-model-base.js"));
var _nestedViewBase = _interopRequireDefault(__webpack_require__(/*! ./views/nested-view-base */ "../modules/nested-elements/assets/js/editor/nested-repeater/views/nested-view-base.js"));
var _repeater = _interopRequireDefault(__webpack_require__(/*! ./controls/repeater */ "../modules/nested-elements/assets/js/editor/nested-repeater/controls/repeater.js"));
var hooks = _interopRequireWildcard(__webpack_require__(/*! ./hooks/ */ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/index.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var Component = exports["default"] = /*#__PURE__*/function (_$e$modules$Component) {
  function Component() {
    var _this;
    (0, _classCallCheck2.default)(this, Component);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, Component, [].concat(args));
    (0, _defineProperty2.default)(_this, "exports", {
      NestedModelBase: _nestedModelBase.default,
      NestedViewBase: _nestedViewBase.default
    });
    return _this;
  }
  (0, _inherits2.default)(Component, _$e$modules$Component);
  return (0, _createClass2.default)(Component, [{
    key: "registerAPI",
    value: function registerAPI() {
      _superPropGet(Component, "registerAPI", this, 3)([]);
      elementor.addControlView('nested-elements-repeater', _repeater.default);
    }
  }, {
    key: "getNamespace",
    value: function getNamespace() {
      return 'nested-elements/nested-repeater';
    }
  }, {
    key: "defaultHooks",
    value: function defaultHooks() {
      return this.importHooks(hooks);
    }
  }]);
}($e.modules.ComponentBase);

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/nested-repeater/controls/repeater.js":
/*!****************************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/nested-repeater/controls/repeater.js ***!
  \****************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _utils = __webpack_require__(/*! elementor/modules/nested-elements/assets/js/editor/utils */ "../modules/nested-elements/assets/js/editor/utils.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var Repeater = exports["default"] = /*#__PURE__*/function (_elementor$modules$co) {
  function Repeater() {
    (0, _classCallCheck2.default)(this, Repeater);
    return _callSuper(this, Repeater, arguments);
  }
  (0, _inherits2.default)(Repeater, _elementor$modules$co);
  return (0, _createClass2.default)(Repeater, [{
    key: "className",
    value: function className() {
      // Repeater Panel CSS, depends on 'elementor-control-type-repeater` control.
      // `elementor-control-type-nested-elements-repeater` to `elementor-control-type-repeater`
      return _superPropGet(Repeater, "className", this, 3)([]).replace('nested-elements-repeater', 'repeater');
    }

    /**
     * Override to avoid the default behavior to adjust the title of the row.
     *
     * @return {Object}
     */
  }, {
    key: "getDefaults",
    value: function getDefaults() {
      var widgetContainer = this.options.container,
        defaults = widgetContainer.model.config.defaults,
        index = widgetContainer.children.length + 1;
      return (0, _defineProperty2.default)({
        _id: ''
      }, defaults.repeater_title_setting, (0, _utils.extractNestedItemTitle)(widgetContainer, index));
    }
  }, {
    key: "onChildviewClickDuplicate",
    value: function onChildviewClickDuplicate(childView) {
      $e.run('document/repeater/duplicate', {
        container: this.options.container,
        name: this.model.get('name'),
        index: childView._index
      });
      this.toggleMinRowsClass();
    }
  }, {
    key: "updateActiveRow",
    value: function updateActiveRow() {
      if (!this.currentEditableChild) {
        return;
      }
      $e.run('document/repeater/select', {
        container: this.container,
        index: this.currentEditableChild.itemIndex,
        options: {
          useHistory: false
        }
      });
    }
  }]);
}(elementor.modules.controls.Repeater);

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/base.js":
/*!**************************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/base.js ***!
  \**************************************************************************************/
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
var _utils = __webpack_require__(/*! elementor/modules/nested-elements/assets/js/editor/utils */ "../modules/nested-elements/assets/js/editor/utils.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Base = exports["default"] = /*#__PURE__*/function (_$e$modules$hookData$) {
  function Base() {
    (0, _classCallCheck2.default)(this, Base);
    return _callSuper(this, Base, arguments);
  }
  (0, _inherits2.default)(Base, _$e$modules$hookData$);
  return (0, _createClass2.default)(Base, [{
    key: "getContainerType",
    value: function getContainerType() {
      return 'widget';
    }
  }, {
    key: "getConditions",
    value: function getConditions(args) {
      return (0, _utils.isWidgetSupportNesting)(args.container.model.get('widgetType'));
    }
  }]);
}($e.modules.hookData.After);

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/document/repeater/duplicate/nested-repeater-duplicate-container.js":
/*!*************************************************************************************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/document/repeater/duplicate/nested-repeater-duplicate-container.js ***!
  \*************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.NestedRepeaterDuplicateContainer = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ../../../base */ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/base.js"));
var _utils = __webpack_require__(/*! elementor/modules/nested-elements/assets/js/editor/utils */ "../modules/nested-elements/assets/js/editor/utils.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var NestedRepeaterDuplicateContainer = exports.NestedRepeaterDuplicateContainer = /*#__PURE__*/function (_Base) {
  function NestedRepeaterDuplicateContainer() {
    (0, _classCallCheck2.default)(this, NestedRepeaterDuplicateContainer);
    return _callSuper(this, NestedRepeaterDuplicateContainer, arguments);
  }
  (0, _inherits2.default)(NestedRepeaterDuplicateContainer, _Base);
  return (0, _createClass2.default)(NestedRepeaterDuplicateContainer, [{
    key: "getId",
    value: function getId() {
      return 'document/repeater/duplicate--nested-repeater-duplicate-container';
    }
  }, {
    key: "getCommand",
    value: function getCommand() {
      return 'document/repeater/duplicate';
    }
  }, {
    key: "apply",
    value: function apply(_ref) {
      var container = _ref.container,
        index = _ref.index;
      var result = $e.run('document/elements/duplicate', {
        container: (0, _utils.findChildContainerOrFail)(container, index),
        options: {
          edit: false // Not losing focus.
        }
      });
      var widgetType = container.settings.get('widgetType');
      if ((0, _utils.shouldUseAtomicRepeaters)(widgetType)) {
        container.view.children._views = (0, _utils.sortViewsByModels)(container);
        elementor.$preview[0].contentWindow.dispatchEvent(new CustomEvent('elementor/nested-container/atomic-repeater', {
          detail: {
            container: container,
            targetContainer: result,
            index: index,
            action: {
              type: 'duplicate'
            }
          }
        }));
      } else {
        container.render();
      }
    }
  }]);
}(_base.default);
var _default = exports["default"] = NestedRepeaterDuplicateContainer;

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/document/repeater/insert/nested-repeater-create-container.js":
/*!*******************************************************************************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/document/repeater/insert/nested-repeater-create-container.js ***!
  \*******************************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.NestedRepeaterCreateContainer = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ../../../base */ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/base.js"));
var _utils = __webpack_require__(/*! elementor/modules/nested-elements/assets/js/editor/utils */ "../modules/nested-elements/assets/js/editor/utils.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
/**
 * Hook responsible for:
 * a. Create container element for each created repeater item.
 * b. Set setting `_title` for the new container.
 * c. Since the core mechanism does not support nested by default,
 *    the hook take care of duplicating the children for the new container.
 */
var NestedRepeaterCreateContainer = exports.NestedRepeaterCreateContainer = /*#__PURE__*/function (_Base) {
  function NestedRepeaterCreateContainer() {
    (0, _classCallCheck2.default)(this, NestedRepeaterCreateContainer);
    return _callSuper(this, NestedRepeaterCreateContainer, arguments);
  }
  (0, _inherits2.default)(NestedRepeaterCreateContainer, _Base);
  return (0, _createClass2.default)(NestedRepeaterCreateContainer, [{
    key: "getId",
    value: function getId() {
      return 'document/repeater/insert--nested-repeater-create-container';
    }
  }, {
    key: "getCommand",
    value: function getCommand() {
      return 'document/repeater/insert';
    }
  }, {
    key: "getConditions",
    value: function getConditions(args) {
      // Will only handle when command called directly and not through another command like `duplicate` or `move`.
      var isCommandCalledDirectly = $e.commands.isCurrentFirstTrace(this.getCommand());
      return _superPropGet(NestedRepeaterCreateContainer, "getConditions", this, 3)([args]) && isCommandCalledDirectly;
    }
  }, {
    key: "apply",
    value: function apply(_ref) {
      var container = _ref.container,
        name = _ref.name;
      var index = container.repeaters[name].children.length;
      $e.run('document/elements/create', {
        container: container,
        model: {
          elType: 'container',
          isLocked: true,
          _title: (0, _utils.extractNestedItemTitle)(container, index)
        },
        options: {
          edit: false // Not losing focus.
        }
      });
      var widgetType = container.settings.get('widgetType');
      if ((0, _utils.shouldUseAtomicRepeaters)(widgetType)) {
        elementor.$preview[0].contentWindow.dispatchEvent(new CustomEvent('elementor/nested-container/atomic-repeater', {
          detail: {
            container: container,
            action: {
              type: 'create'
            }
          }
        }));
      }
    }
  }]);
}(_base.default);
var _default = exports["default"] = NestedRepeaterCreateContainer;

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/document/repeater/move/nested-repeater-move-container.js":
/*!***************************************************************************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/document/repeater/move/nested-repeater-move-container.js ***!
  \***************************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.NestedRepeaterMoveContainer = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ../../../base */ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/base.js"));
var _utils = __webpack_require__(/*! elementor/modules/nested-elements/assets/js/editor/utils */ "../modules/nested-elements/assets/js/editor/utils.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var NestedRepeaterMoveContainer = exports.NestedRepeaterMoveContainer = /*#__PURE__*/function (_Base) {
  function NestedRepeaterMoveContainer() {
    (0, _classCallCheck2.default)(this, NestedRepeaterMoveContainer);
    return _callSuper(this, NestedRepeaterMoveContainer, arguments);
  }
  (0, _inherits2.default)(NestedRepeaterMoveContainer, _Base);
  return (0, _createClass2.default)(NestedRepeaterMoveContainer, [{
    key: "getId",
    value: function getId() {
      return 'document/repeater/move--nested-repeater-move-container';
    }
  }, {
    key: "getCommand",
    value: function getCommand() {
      return 'document/repeater/move';
    }
  }, {
    key: "apply",
    value: function apply(_ref) {
      var container = _ref.container,
        sourceIndex = _ref.sourceIndex,
        targetIndex = _ref.targetIndex;
      var result = $e.run('document/elements/move', {
        container: (0, _utils.findChildContainerOrFail)(container, sourceIndex),
        target: container,
        options: {
          at: targetIndex,
          edit: false // Not losing focus.
        }
      });
      var widgetType = container.settings.get('widgetType');
      if ((0, _utils.shouldUseAtomicRepeaters)(widgetType)) {
        container.view.children._views = (0, _utils.sortViewsByModels)(container);
        elementor.$preview[0].contentWindow.dispatchEvent(new CustomEvent('elementor/nested-container/atomic-repeater', {
          detail: {
            container: container,
            targetContainer: result,
            index: targetIndex,
            action: {
              type: 'move'
            }
          }
        }));
      }
    }
  }]);
}(_base.default);
var _default = exports["default"] = NestedRepeaterMoveContainer;

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/document/repeater/remove/nested-repeater-remove-container.js":
/*!*******************************************************************************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/document/repeater/remove/nested-repeater-remove-container.js ***!
  \*******************************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.NestedRepeaterRemoveContainer = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ../../../base */ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/base.js"));
var _utils = __webpack_require__(/*! elementor/modules/nested-elements/assets/js/editor/utils */ "../modules/nested-elements/assets/js/editor/utils.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
/**
 * Hook responsible for removing container element for the removed repeater item.
 */
var NestedRepeaterRemoveContainer = exports.NestedRepeaterRemoveContainer = /*#__PURE__*/function (_Base) {
  function NestedRepeaterRemoveContainer() {
    (0, _classCallCheck2.default)(this, NestedRepeaterRemoveContainer);
    return _callSuper(this, NestedRepeaterRemoveContainer, arguments);
  }
  (0, _inherits2.default)(NestedRepeaterRemoveContainer, _Base);
  return (0, _createClass2.default)(NestedRepeaterRemoveContainer, [{
    key: "getId",
    value: function getId() {
      return 'document/repeater/remove--nested-elements-remove-container';
    }
  }, {
    key: "getCommand",
    value: function getCommand() {
      return 'document/repeater/remove';
    }
  }, {
    key: "getConditions",
    value: function getConditions(args) {
      // Will only handle when command called directly and not through another command like `duplicate` or `move`.
      var isCommandCalledDirectly = $e.commands.isCurrentFirstTrace(this.getCommand());
      return _superPropGet(NestedRepeaterRemoveContainer, "getConditions", this, 3)([args]) && isCommandCalledDirectly;
    }
  }, {
    key: "apply",
    value: function apply(_ref) {
      var container = _ref.container,
        index = _ref.index;
      $e.run('document/elements/delete', {
        container: (0, _utils.findChildContainerOrFail)(container, index),
        force: true
      });
      var widgetType = container.settings.get('widgetType');
      if ((0, _utils.shouldUseAtomicRepeaters)(widgetType)) {
        elementor.$preview[0].contentWindow.dispatchEvent(new CustomEvent('elementor/nested-container/atomic-repeater', {
          detail: {
            container: container,
            action: {
              type: 'remove'
            }
          }
        }));
      }
    }
  }]);
}(_base.default);
var _default = exports["default"] = NestedRepeaterRemoveContainer;

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/index.js":
/*!**********************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/nested-repeater/hooks/index.js ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "NestedRepeaterCreateContainer", ({
  enumerable: true,
  get: function get() {
    return _nestedRepeaterCreateContainer.NestedRepeaterCreateContainer;
  }
}));
Object.defineProperty(exports, "NestedRepeaterDuplicateContainer", ({
  enumerable: true,
  get: function get() {
    return _nestedRepeaterDuplicateContainer.NestedRepeaterDuplicateContainer;
  }
}));
Object.defineProperty(exports, "NestedRepeaterFocusCurrentEditedContainer", ({
  enumerable: true,
  get: function get() {
    return _nestedRepeaterFocusCurrentEditedContainer.NestedRepeaterFocusCurrentEditedContainer;
  }
}));
Object.defineProperty(exports, "NestedRepeaterMoveContainer", ({
  enumerable: true,
  get: function get() {
    return _nestedRepeaterMoveContainer.NestedRepeaterMoveContainer;
  }
}));
Object.defineProperty(exports, "NestedRepeaterRemoveContainer", ({
  enumerable: true,
  get: function get() {
    return _nestedRepeaterRemoveContainer.NestedRepeaterRemoveContainer;
  }
}));
var _nestedRepeaterCreateContainer = __webpack_require__(/*! ./data/document/repeater/insert/nested-repeater-create-container */ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/document/repeater/insert/nested-repeater-create-container.js");
var _nestedRepeaterRemoveContainer = __webpack_require__(/*! ./data/document/repeater/remove/nested-repeater-remove-container */ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/document/repeater/remove/nested-repeater-remove-container.js");
var _nestedRepeaterMoveContainer = __webpack_require__(/*! ./data/document/repeater/move/nested-repeater-move-container */ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/document/repeater/move/nested-repeater-move-container.js");
var _nestedRepeaterDuplicateContainer = __webpack_require__(/*! ./data/document/repeater/duplicate/nested-repeater-duplicate-container */ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/data/document/repeater/duplicate/nested-repeater-duplicate-container.js");
var _nestedRepeaterFocusCurrentEditedContainer = __webpack_require__(/*! ./ui/panel/editor/open/nested-repeater-focus-current-edited-container */ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/ui/panel/editor/open/nested-repeater-focus-current-edited-container.js");

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/nested-repeater/hooks/ui/panel/editor/open/nested-repeater-focus-current-edited-container.js":
/*!************************************************************************************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/nested-repeater/hooks/ui/panel/editor/open/nested-repeater-focus-current-edited-container.js ***!
  \************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.NestedRepeaterFocusCurrentEditedContainer = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _utils = __webpack_require__(/*! elementor/modules/nested-elements/assets/js/editor/utils */ "../modules/nested-elements/assets/js/editor/utils.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * Since the nested tabs can have different depths, it should focus the current edited container,
 * but the problem is, without timeout it will be so fast, that the USER will not be able to see it.
 * using `NAVIGATION_DEPTH_SENSITIVITY_TIMEOUT` it will be delayed. formula: `NAVIGATION_DEPTH_SENSITIVITY_TIMEOUT * depth`.
 */
var NAVIGATION_DEPTH_SENSITIVITY_TIMEOUT = 250;

/**
 * Used to open current selected container.
 * Will run 'document/repeater/select', over nested elements tree.
 * Will select all repeater nested item(s) till it reach current repeater of selected element.
 */
var NestedRepeaterFocusCurrentEditedContainer = exports.NestedRepeaterFocusCurrentEditedContainer = /*#__PURE__*/function (_$e$modules$hookUI$Af) {
  function NestedRepeaterFocusCurrentEditedContainer() {
    (0, _classCallCheck2.default)(this, NestedRepeaterFocusCurrentEditedContainer);
    return _callSuper(this, NestedRepeaterFocusCurrentEditedContainer, arguments);
  }
  (0, _inherits2.default)(NestedRepeaterFocusCurrentEditedContainer, _$e$modules$hookUI$Af);
  return (0, _createClass2.default)(NestedRepeaterFocusCurrentEditedContainer, [{
    key: "getCommand",
    value: function getCommand() {
      return 'panel/editor/open';
    }
  }, {
    key: "getId",
    value: function getId() {
      return 'nested-repeater-focus-current-edited-container';
    }
  }, {
    key: "getConditions",
    value: function getConditions(args) {
      var _this$navigationMap;
      // Do not select for element creation.
      if ($e.commands.isCurrentFirstTrace('document/elements/create')) {
        return false;
      }

      // If some of the parents are supporting nested elements, then return true.
      var allParents = args.view.container.getParentAncestry(),
        result = allParents.some(function (parent) {
          return (0, _utils.isWidgetSupportNesting)(parent.model.get('widgetType'));
        });
      if (result) {
        this.navigationMap = this.getNavigationMapForContainers(allParents.filter(function (container) {
          return 'container' === container.type && 'widget' === container.parent.type;
        })).filter(function (map) {
          // Filter out paths that are the same as current.
          return map.index !== map.current;
        });
      }
      return (_this$navigationMap = this.navigationMap) === null || _this$navigationMap === void 0 ? void 0 : _this$navigationMap.length;
    }
  }, {
    key: "apply",
    value: function apply() {
      var depth = 1;
      this.navigationMap.forEach(function (_ref) {
        var container = _ref.container,
          index = _ref.index;
        setTimeout(function () {
          // No history, for focusing on current container.
          $e.run('document/repeater/select', {
            container: container,
            index: index++,
            options: {
              useHistory: false
            }
          });
        }, NAVIGATION_DEPTH_SENSITIVITY_TIMEOUT * depth);
        ++depth;
      });
    }
  }, {
    key: "getNavigationMapForContainers",
    value: function getNavigationMapForContainers(containers) {
      return containers.map(function (container) {
        return {
          current: container.parent.model.get('editSettings').get('activeItemIndex'),
          container: container.parent,
          index: container.parent.children.indexOf(container) + 1
        };
      }).reverse();
    }
  }]);
}($e.modules.hookUI.After);
var _default = exports["default"] = NestedRepeaterFocusCurrentEditedContainer;

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/nested-repeater/models/nested-model-base.js":
/*!***********************************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/nested-repeater/models/nested-model-base.js ***!
  \***********************************************************************************************/
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
var _utils = __webpack_require__(/*! elementor/modules/nested-elements/assets/js/editor/utils */ "../modules/nested-elements/assets/js/editor/utils.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var NestedModelBase = exports["default"] = /*#__PURE__*/function (_elementor$modules$el) {
  function NestedModelBase() {
    (0, _classCallCheck2.default)(this, NestedModelBase);
    return _callSuper(this, NestedModelBase, arguments);
  }
  (0, _inherits2.default)(NestedModelBase, _elementor$modules$el);
  return (0, _createClass2.default)(NestedModelBase, [{
    key: "initialize",
    value: function initialize(options) {
      this.config = elementor.widgetsCache[options.widgetType];
      this.set('supportRepeaterChildren', true);
      var isNewElementCreate = 0 === this.get('elements').length && $e.commands.currentTrace.includes('document/elements/create');
      if (isNewElementCreate) {
        this.onElementCreate();
      }
      _superPropGet(NestedModelBase, "initialize", this, 3)([options]);
    }
  }, {
    key: "isValidChild",
    value: function isValidChild(childModel) {
      var parentElType = this.get('elType'),
        childElType = childModel.get('elType');
      return 'container' === childElType && 'widget' === parentElType && (0, _utils.isWidgetSupportNesting)(this.get('widgetType')) &&
      // When creating a container for the tabs widget specifically from the repeater, the container should be locked,
      // so only containers that are locked (created from the repeater) can be inside the tabs widget.
      childModel.get('isLocked');
    }
  }, {
    key: "getDefaultChildren",
    value: function getDefaultChildren() {
      var defaults = this.config.defaults,
        result = [];
      defaults.elements.forEach(function (element) {
        element.id = elementorCommon.helpers.getUniqueId();
        element.settings = element.settings || {};
        element.elements = element.elements || [];
        element.isLocked = true;
        result.push(element);
      });
      return result;
    }
  }, {
    key: "onElementCreate",
    value: function onElementCreate() {
      this.set('elements', this.getDefaultChildren());
    }
  }]);
}(elementor.modules.elements.models.Element);

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/nested-repeater/views/nested-view-base.js":
/*!*********************************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/nested-repeater/views/nested-view-base.js ***!
  \*********************************************************************************************/
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
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var NestedViewBase = exports["default"] = /*#__PURE__*/function (_elementor$modules$el) {
  function NestedViewBase() {
    (0, _classCallCheck2.default)(this, NestedViewBase);
    return _callSuper(this, NestedViewBase, arguments);
  }
  (0, _inherits2.default)(NestedViewBase, _elementor$modules$el);
  return (0, _createClass2.default)(NestedViewBase, [{
    key: "getChildViewContainer",
    value:
    // Sometimes the children placement is not in the end of the element, but somewhere else, eg: deep inside the element template.
    // If `defaults_placeholder_selector` is set, it will be used to find the correct place to insert the children.
    function getChildViewContainer(containerView, childView) {
      var _this$model$config$de = this.model.config.defaults,
        customSelector = _this$model$config$de.elements_placeholder_selector,
        childContainerSelector = _this$model$config$de.child_container_placeholder_selector;
      if (childView !== undefined && childView._index !== undefined && childContainerSelector) {
        return containerView.$el.find("".concat(childContainerSelector, ":nth-child(").concat(childView._index + 1, ")"));
      }
      if (customSelector) {
        return containerView.$el.find(this.model.config.defaults.elements_placeholder_selector);
      }
      return _superPropGet(NestedViewBase, "getChildViewContainer", this, 3)([containerView, childView]);
    }
  }, {
    key: "getChildType",
    value: function getChildType() {
      return ['container'];
    }
  }, {
    key: "onRender",
    value: function onRender() {
      _superPropGet(NestedViewBase, "onRender", this, 3)([]);
      this.normalizeAttributes();
    }
  }]);
}(elementor.modules.elements.views.BaseWidget);

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/utils.js":
/*!************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/utils.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var sprintf = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["sprintf"];


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.extractNestedItemTitle = extractNestedItemTitle;
exports.findChildContainerOrFail = findChildContainerOrFail;
exports.isWidgetSupportAtomicRepeaters = isWidgetSupportAtomicRepeaters;
exports.isWidgetSupportNesting = isWidgetSupportNesting;
exports.shouldUseAtomicRepeaters = shouldUseAtomicRepeaters;
exports.sortViewsByModels = sortViewsByModels;
exports.widgetNodes = widgetNodes;
function extractNestedItemTitle(container, index) {
  var title = container.view.model.config.defaults.elements_title;

  // Translations comes from server side.
  return sprintf(title, index);
}
function isWidgetSupportNesting(widgetType) {
  var widgetConfig = elementor.widgetsCache[widgetType];
  if (!widgetConfig) {
    return false;
  }
  return widgetConfig.support_nesting;
}
function isWidgetSupportAtomicRepeaters(widgetType) {
  var widgetConfig = elementor.widgetsCache[widgetType];
  if (!widgetConfig) {
    return false;
  }
  return widgetConfig.support_improved_repeaters;
}
function widgetNodes(widgetType) {
  var widgetConfig = elementor.widgetsCache[widgetType];
  if (!widgetConfig) {
    return false;
  }
  return {
    targetContainer: widgetConfig.target_container,
    node: widgetConfig.node
  };
}
function findChildContainerOrFail(container, index) {
  var childView = container.view.children.findByIndex(index);
  if (!childView) {
    throw new Error('Child container was not found for the current repeater item.');
  }
  return childView.getContainer();
}
function shouldUseAtomicRepeaters(widgetType) {
  return isWidgetSupportNesting(widgetType) && isWidgetSupportAtomicRepeaters(widgetType);
}
function sortViewsByModels(container) {
  var models = container.model.get('elements').models,
    children = container.view.children,
    updatedViews = {};
  models.forEach(function (model, index) {
    var view = children.findByModel(model);
    view._index = index;
    updatedViews[view.cid] = view;
  });
  return updatedViews;
}

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

/***/ })

}]);
//# sourceMappingURL=471f5dab6676072462a8.bundle.js.map