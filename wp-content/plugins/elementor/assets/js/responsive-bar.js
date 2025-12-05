/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../assets/dev/js/editor/regions/responsive-bar/responsive-bar.js":
/*!************************************************************************!*\
  !*** ../assets/dev/js/editor/regions/responsive-bar/responsive-bar.js ***!
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
var _view = _interopRequireDefault(__webpack_require__(/*! ./view */ "../assets/dev/js/editor/regions/responsive-bar/view.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var _default = exports["default"] = /*#__PURE__*/function (_Marionette$Region) {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _callSuper(this, _default, arguments);
  }
  (0, _inherits2.default)(_default, _Marionette$Region);
  return (0, _createClass2.default)(_default, [{
    key: "initialize",
    value: function initialize() {
      var _this = this;
      this.show(new _view.default());
      elementor.panel.$el.on({
        resizestart: function resizestart() {
          return _this.onPanelResizeStart();
        },
        resizestop: function resizestop() {
          return _this.onPanelResizeStop();
        }
      });
    }
  }, {
    key: "onPanelResizeStart",
    value: function onPanelResizeStart() {
      this.$el.addClass('ui-resizable-resizing');
    }
  }, {
    key: "onPanelResizeStop",
    value: function onPanelResizeStop() {
      this.$el.removeClass('ui-resizable-resizing');
    }
  }]);
}(Marionette.Region);

/***/ }),

/***/ "../assets/dev/js/editor/regions/responsive-bar/view.js":
/*!**************************************************************!*\
  !*** ../assets/dev/js/editor/regions/responsive-bar/view.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


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
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var View = exports["default"] = /*#__PURE__*/function (_Marionette$ItemView) {
  function View() {
    (0, _classCallCheck2.default)(this, View);
    return _callSuper(this, View, arguments);
  }
  (0, _inherits2.default)(View, _Marionette$ItemView);
  return (0, _createClass2.default)(View, [{
    key: "getTemplate",
    value: function getTemplate() {
      return '#tmpl-elementor-templates-responsive-bar';
    }
  }, {
    key: "id",
    value: function id() {
      return 'e-responsive-bar';
    }
  }, {
    key: "ui",
    value: function ui() {
      var prefix = '#' + this.id();
      return {
        switcherInput: '.e-responsive-bar-switcher__option input',
        switcherLabel: '.e-responsive-bar-switcher__option',
        switcher: prefix + '-switcher',
        sizeInputWidth: prefix + '__input-width',
        sizeInputHeight: prefix + '__input-height',
        scaleValue: prefix + '-scale__value',
        scalePlusButton: prefix + '-scale__plus',
        scaleMinusButton: prefix + '-scale__minus',
        scaleResetButton: prefix + '-scale__reset',
        closeButton: prefix + '__close-button',
        breakpointSettingsButton: prefix + '__settings-button'
      };
    }
  }, {
    key: "events",
    value: function events() {
      return {
        'change @ui.switcherInput': 'onBreakpointSelected',
        'input @ui.sizeInputWidth': 'onSizeInputChange',
        'input @ui.sizeInputHeight': 'onSizeInputChange',
        'click @ui.scalePlusButton': 'onScalePlusButtonClick',
        'click @ui.scaleMinusButton': 'onScaleMinusButtonClick',
        'click @ui.scaleResetButton': 'onScaleResetButtonClick',
        'click @ui.closeButton': 'onCloseButtonClick',
        'click @ui.breakpointSettingsButton': 'onBreakpointSettingsOpen'
      };
    }
  }, {
    key: "initialize",
    value: function initialize() {
      this.listenTo(elementor.channels.deviceMode, 'change', this.onDeviceModeChange);
      this.listenTo(elementor.channels.responsivePreview, 'resize', this.onPreviewResize);
      this.listenTo(elementor.channels.responsivePreview, 'open', this.onPreviewOpen);
      this.listenTo(elementor.channels.deviceMode, 'close', this.resetScale);
    }
  }, {
    key: "addTipsyToIconButtons",
    value: function addTipsyToIconButtons() {
      this.ui.switcherLabel.add(this.ui.closeButton).add(this.ui.breakpointSettingsButton).tipsy({
        html: true,
        gravity: 'n',
        title: function title() {
          return jQuery(this).data('tooltip');
        }
      });
    }
  }, {
    key: "restoreLastValidPreviewSize",
    value: function restoreLastValidPreviewSize() {
      var lastSize = elementor.channels.responsivePreview.request('size');
      this.ui.sizeInputWidth.val(lastSize.width).tipsy({
        html: true,
        trigger: 'manual',
        gravity: 'n',
        title: function title() {
          return __('The value inserted isn\'t in the breakpoint boundaries', 'elementor');
        }
      });
      var tipsy = this.ui.sizeInputWidth.data('tipsy');
      tipsy.show();
      setTimeout(function () {
        return tipsy.hide();
      }, 3000);
    }
  }, {
    key: "autoScale",
    value: function autoScale() {
      var handlesWidth = 40 * this.scalePercentage / 100,
        previewWidth = elementor.$previewWrapper.width() - handlesWidth,
        iframeWidth = parseInt(elementor.$preview.css('--e-editor-preview-width')),
        iframeScaleWidth = iframeWidth * this.scalePercentage / 100;
      if (iframeScaleWidth > previewWidth) {
        var scalePercentage = previewWidth / iframeWidth * 100;
        this.setScalePercentage(scalePercentage);
      } else {
        this.setScalePercentage();
      }
      this.scalePreview();
    }
  }, {
    key: "scalePreview",
    value: function scalePreview() {
      var scale = this.scalePercentage / 100;
      elementor.$previewWrapper.css('--e-preview-scale', scale);
    }
  }, {
    key: "resetScale",
    value: function resetScale() {
      this.setScalePercentage();
      this.scalePreview();
    }
  }, {
    key: "setScalePercentage",
    value: function setScalePercentage() {
      var scalePercentage = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 100;
      this.scalePercentage = scalePercentage;
      this.ui.scaleValue.text(parseInt(this.scalePercentage));
    }
  }, {
    key: "onRender",
    value: function onRender() {
      this.addTipsyToIconButtons();
      this.setScalePercentage();
    }
  }, {
    key: "onDeviceModeChange",
    value: function onDeviceModeChange() {
      var currentDeviceMode = elementor.channels.deviceMode.request('currentMode'),
        $currentDeviceSwitcherInput = this.ui.switcherInput.filter('[value=' + currentDeviceMode + ']');
      this.setWidthHeightInputsEditableState();
      this.ui.switcherLabel.attr('aria-selected', false);
      $currentDeviceSwitcherInput.closest('label').attr('aria-selected', true);
      if (!$currentDeviceSwitcherInput.prop('checked')) {
        $currentDeviceSwitcherInput.prop('checked', true);
      }
    }
  }, {
    key: "onBreakpointSelected",
    value: function onBreakpointSelected(e) {
      var selectedDeviceMode = e.target.value;
      elementor.changeDeviceMode(selectedDeviceMode, false);
      this.autoScale();
    }
  }, {
    key: "onBreakpointSettingsOpen",
    value: function onBreakpointSettingsOpen() {
      var isWPPreviewMode = elementorCommon.elements.$body.hasClass('elementor-editor-preview');
      if (isWPPreviewMode) {
        elementor.exitPreviewMode();
      }
      var isInSettingsPanelActive = 'panel/global/menu' === elementor.documents.currentDocument.config.panel.default_route;
      if (isInSettingsPanelActive) {
        $e.run('panel/global/close');
        return;
      }

      //  Open Settings Panel for Global/Layout/Breakpoints Settings
      $e.run('editor/documents/switch', {
        id: elementor.config.kit_id,
        mode: 'autosave'
      }).then(function () {
        return $e.route('panel/global/settings-layout');
      })
      // TODO: Replace with a standard routing solution once one is available
      .then(function () {
        return jQuery('.elementor-control-section_breakpoints').trigger('click');
      });
    }
  }, {
    key: "onPreviewResize",
    value: function onPreviewResize() {
      if (this.updatingPreviewSize) {
        return;
      }
      var size = elementor.channels.responsivePreview.request('size');
      this.ui.sizeInputWidth.val(Math.round(size.width));
      this.ui.sizeInputHeight.val(Math.round(size.height));
    }
  }, {
    key: "onPreviewOpen",
    value: function onPreviewOpen() {
      this.setWidthHeightInputsEditableState();
    }
  }, {
    key: "setWidthHeightInputsEditableState",
    value: function setWidthHeightInputsEditableState() {
      var currentDeviceMode = elementor.channels.deviceMode.request('currentMode');
      // TODO: disable inputs
      if ('desktop' === currentDeviceMode) {
        this.ui.sizeInputWidth.attr('disabled', 'disabled');
        this.ui.sizeInputHeight.attr('disabled', 'disabled');
      } else {
        this.ui.sizeInputWidth.removeAttr('disabled');
        this.ui.sizeInputHeight.removeAttr('disabled');
      }
    }
  }, {
    key: "onCloseButtonClick",
    value: function onCloseButtonClick() {
      elementor.changeDeviceMode('desktop');
      // Force exit if device mode is already desktop
      elementor.exitDeviceMode();
    }
  }, {
    key: "onSizeInputChange",
    value: function onSizeInputChange() {
      var _this = this;
      clearTimeout(this.restorePreviewSizeTimeout);
      var size = {
        width: this.ui.sizeInputWidth.val(),
        height: this.ui.sizeInputHeight.val()
      };
      var currentDeviceConstrains = elementor.getCurrentDeviceConstrains();
      if (size.width < currentDeviceConstrains.minWidth || size.width > currentDeviceConstrains.maxWidth) {
        this.restorePreviewSizeTimeout = setTimeout(function () {
          return _this.restoreLastValidPreviewSize();
        }, 1500);
        return;
      }
      this.updatingPreviewSize = true;
      setTimeout(function () {
        return _this.updatingPreviewSize = false;
      }, 300);
      elementor.updatePreviewSize(size);
      this.autoScale();
    }
  }, {
    key: "onScalePlusButtonClick",
    value: function onScalePlusButtonClick() {
      var scaleUp = 0 === this.scalePercentage % 10 ? this.scalePercentage + 10 : Math.ceil(this.scalePercentage / 10) * 10;
      if (scaleUp > 200) {
        return;
      }
      this.setScalePercentage(scaleUp);
      this.scalePreview();
    }
  }, {
    key: "onScaleMinusButtonClick",
    value: function onScaleMinusButtonClick() {
      var scaleDown = 0 === this.scalePercentage % 10 ? this.scalePercentage - 10 : Math.floor(this.scalePercentage / 10) * 10;
      if (scaleDown < 50) {
        return;
      }
      this.setScalePercentage(scaleDown);
      this.scalePreview();
    }
  }, {
    key: "onScaleResetButtonClick",
    value: function onScaleResetButtonClick() {
      this.resetScale();
    }
  }]);
}(Marionette.ItemView);

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

/***/ "@wordpress/i18n":
/*!**************************!*\
  !*** external "wp.i18n" ***!
  \**************************/
/***/ ((module) => {

"use strict";
module.exports = wp.i18n;

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
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!***************************************************************!*\
  !*** ../assets/dev/js/editor/regions/responsive-bar/index.js ***!
  \***************************************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _responsiveBar = _interopRequireDefault(__webpack_require__(/*! elementor-regions/responsive-bar/responsive-bar */ "../assets/dev/js/editor/regions/responsive-bar/responsive-bar.js"));
elementor.on('preview:loaded', function (isFirstLoad) {
  if (!isFirstLoad) {
    return;
  }
  elementor.addRegions({
    responsiveBar: {
      el: '#elementor-responsive-bar',
      regionClass: _responsiveBar.default
    }
  });
  elementor.trigger('responsiveBar:init');
});
})();

/******/ })()
;
//# sourceMappingURL=responsive-bar.js.map