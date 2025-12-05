(self["webpackChunkelementor"] = self["webpackChunkelementor"] || []).push([["modules_nested-elements_assets_js_editor_nested-element-types-base_js"],{

/***/ "../modules/nested-elements/assets/js/editor/nested-element-types-base.js":
/*!********************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/nested-element-types-base.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.NestedElementTypesBase = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _view = _interopRequireDefault(__webpack_require__(/*! ./views/view */ "../modules/nested-elements/assets/js/editor/views/view.js"));
var _empty = _interopRequireDefault(__webpack_require__(/*! ./views/empty */ "../modules/nested-elements/assets/js/editor/views/empty.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @typedef {import('../../../../../assets/dev/js/editor/elements/types/base/element-base')} ElementBase
 */
var NestedElementTypesBase = exports.NestedElementTypesBase = /*#__PURE__*/function (_elementor$modules$el) {
  function NestedElementTypesBase() {
    (0, _classCallCheck2.default)(this, NestedElementTypesBase);
    return _callSuper(this, NestedElementTypesBase, arguments);
  }
  (0, _inherits2.default)(NestedElementTypesBase, _elementor$modules$el);
  return (0, _createClass2.default)(NestedElementTypesBase, [{
    key: "getType",
    value: function getType() {
      elementorModules.ForceMethodImplementation();
    }
  }, {
    key: "getView",
    value: function getView() {
      return _view.default;
    }
  }, {
    key: "getEmptyView",
    value: function getEmptyView() {
      return _empty.default;
    }
  }, {
    key: "getModel",
    value: function getModel() {
      return $e.components.get('nested-elements/nested-repeater').exports.NestedModelBase;
    }
  }]);
}(elementor.modules.elements.types.Base);
var _default = exports["default"] = NestedElementTypesBase;

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/views/add-section-area.js":
/*!*****************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/views/add-section-area.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = AddSectionArea;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
/* eslint-disable jsx-a11y/click-events-have-key-events */

function AddSectionArea(props) {
  var addAreaElementRef = (0, _react.useRef)(),
    containerHelper = elementor.helpers.container;

  // Make droppable area.
  (0, _react.useEffect)(function () {
    var $addAreaElementRef = jQuery(addAreaElementRef.current),
      defaultDroppableOptions = props.container.view.getDroppableOptions();

    // Make some adjustments to behave like 'AddSectionArea', use default droppable options from container element.
    defaultDroppableOptions.placeholder = false;
    defaultDroppableOptions.items = '> .elementor-add-section-inner';
    defaultDroppableOptions.hasDraggingOnChildClass = 'elementor-dragging-on-child';

    // Make element drop-able.
    $addAreaElementRef.html5Droppable(defaultDroppableOptions);

    // Cleanup.
    return function () {
      $addAreaElementRef.html5Droppable('destroy');
    };
  }, []);
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "elementor-add-section",
    onClick: function onClick() {
      return containerHelper.openEditMode(props.container);
    },
    ref: addAreaElementRef,
    role: "button",
    tabIndex: "0"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "elementor-add-section-inner"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-view elementor-add-new-section"
  }, /*#__PURE__*/_react.default.createElement("button", {
    type: "button",
    className: "elementor-add-section-area-button elementor-add-section-button",
    "aria-label": __('Add new container', 'elementor'),
    onClick: function onClick() {
      return props.setIsRenderPresets(true);
    }
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-plus",
    "aria-hidden": "true"
  })), /*#__PURE__*/_react.default.createElement("div", {
    className: "elementor-add-section-drag-title"
  }, __('Drag widget here', 'elementor')))));
}
AddSectionArea.propTypes = {
  container: PropTypes.object.isRequired,
  setIsRenderPresets: PropTypes.func.isRequired
};

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/views/empty.js":
/*!******************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/views/empty.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Empty;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _addSectionArea = _interopRequireDefault(__webpack_require__(/*! ./add-section-area */ "../modules/nested-elements/assets/js/editor/views/add-section-area.js"));
var _selectPreset = _interopRequireDefault(__webpack_require__(/*! ./select-preset */ "../modules/nested-elements/assets/js/editor/views/select-preset.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function Empty(props) {
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isRenderPresets = _useState2[0],
    setIsRenderPresets = _useState2[1];
  props = _objectSpread(_objectSpread({}, props), {}, {
    setIsRenderPresets: setIsRenderPresets
  });
  return isRenderPresets ? /*#__PURE__*/_react.default.createElement(_selectPreset.default, props) : /*#__PURE__*/_react.default.createElement(_addSectionArea.default, props);
}
Empty.propTypes = {
  container: PropTypes.object.isRequired
};

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/views/select-preset.js":
/*!**************************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/views/select-preset.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = SelectPreset;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
/* eslint-disable jsx-a11y/click-events-have-key-events */
function SelectPreset(props) {
  var containerHelper = elementor.helpers.container,
    onPresetSelected = function onPresetSelected(preset, container) {
      var options = {
        createWrapper: false
      };

      // Create new one by selected preset.
      containerHelper.createContainerFromPreset(preset, container, options);
    };
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("button", {
    type: "button",
    className: "elementor-add-section-close",
    "aria-label": __('Close', 'elementor'),
    onClick: function onClick() {
      return props.setIsRenderPresets(false);
    }
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-close",
    "aria-hidden": "true"
  })), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-view e-con-select-preset"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-con-select-preset__title"
  }, __('Select your Structure', 'elementor')), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-con-select-preset__list"
  }, elementor.presetsFactory.getContainerPresets().map(function (preset) {
    return /*#__PURE__*/_react.default.createElement("button", {
      type: "button",
      className: "e-con-preset",
      "data-preset": preset,
      key: preset,
      onClick: function onClick() {
        return onPresetSelected(preset, props.container);
      },
      dangerouslySetInnerHTML: {
        __html: elementor.presetsFactory.generateContainerPreset(preset)
      }
    });
  }))));
}
SelectPreset.propTypes = {
  container: PropTypes.object.isRequired,
  setIsRenderPresets: PropTypes.func.isRequired
};

/***/ }),

/***/ "../modules/nested-elements/assets/js/editor/views/view.js":
/*!*****************************************************************!*\
  !*** ../modules/nested-elements/assets/js/editor/views/view.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.View = void 0;
var _readOnlyError2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/readOnlyError */ "../node_modules/@babel/runtime/helpers/readOnlyError.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var View = exports.View = /*#__PURE__*/function (_$e$components$get$ex) {
  function View() {
    (0, _classCallCheck2.default)(this, View);
    return _callSuper(this, View, arguments);
  }
  (0, _inherits2.default)(View, _$e$components$get$ex);
  return (0, _createClass2.default)(View, [{
    key: "events",
    value: function events() {
      var _this = this;
      var events = _superPropGet(View, "events", this, 3)([]);
      events.click = function (e) {
        // If the clicked Nested Element is not within the currently edited document, don't do anything with it.
        if (elementor.documents.currentDocument.id.toString() !== e.target.closest('.elementor').dataset.elementorId) {
          return;
        }
        var closest = e.target.closest('.elementor-element');
        var targetContainer = null;

        // For clicks on container/widget.
        if (['container', 'widget'].includes(closest === null || closest === void 0 ? void 0 : closest.dataset.element_type)) {
          // eslint-disable-line camelcase
          // In case the container empty, click should be handled by the EmptyView.
          var container = elementor.getContainer(closest.dataset.id);
          if (container.view.isEmpty()) {
            return true;
          }

          // If not empty, open it.
          targetContainer = container;
        }
        e.stopPropagation();
        $e.run('document/elements/select', {
          container: targetContainer || _this.getContainer()
        });
      };
      return events;
    }

    /**
     * Function renderHTML().
     *
     * The `renderHTML()` method is overridden as it causes redundant renders when removing focus from any nested element.
     * This is because the original `renderHTML()` method sets `editModel.renderOnLeave = true;`.
     */
  }, {
    key: "renderHTML",
    value: function renderHTML() {
      var templateType = this.getTemplateType(),
        editModel = this.getEditModel();
      if ('js' === templateType) {
        editModel.setHtmlCache();
        this.render();
      } else {
        editModel.renderRemoteServer();
      }
    }
  }]);
}($e.components.get('nested-elements/nested-repeater').exports.NestedViewBase);
var _default = exports["default"] = View;

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
//# sourceMappingURL=152f977e0c1304a3b0db.bundle.js.map