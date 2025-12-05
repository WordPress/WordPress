/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../modules/container-converter/assets/js/editor/commands/convert-all.js":
/*!*******************************************************************************!*\
  !*** ../modules/container-converter/assets/js/editor/commands/convert-all.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.ConvertAll = void 0;
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var ConvertAll = exports.ConvertAll = /*#__PURE__*/function (_$e$modules$editor$do) {
  function ConvertAll() {
    (0, _classCallCheck2.default)(this, ConvertAll);
    return _callSuper(this, ConvertAll, arguments);
  }
  (0, _inherits2.default)(ConvertAll, _$e$modules$editor$do);
  return (0, _createClass2.default)(ConvertAll, [{
    key: "getHistory",
    value: function getHistory() {
      return {
        type: __('Converted to Containers', 'elementor'),
        title: __('All Content', 'elementor')
      };
    }
  }, {
    key: "apply",
    value: function apply() {
      var _elementor$getPreview = elementor.getPreviewContainer(),
        children = _elementor$getPreview.children;
      (0, _toConsumableArray2.default)(children).forEach(function (container) {
        $e.run('container-converter/convert', {
          container: container
        });
      });
    }
  }]);
}($e.modules.editor.document.CommandHistoryBase);

/***/ }),

/***/ "../modules/container-converter/assets/js/editor/commands/convert.js":
/*!***************************************************************************!*\
  !*** ../modules/container-converter/assets/js/editor/commands/convert.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.Convert = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _migrator = _interopRequireDefault(__webpack_require__(/*! ../migrator */ "../modules/container-converter/assets/js/editor/migrator.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @typedef {import('../../../../../../assets/dev/js/editor/container/container')} Container
 */
var Convert = exports.Convert = /*#__PURE__*/function (_$e$modules$editor$do) {
  function Convert() {
    (0, _classCallCheck2.default)(this, Convert);
    return _callSuper(this, Convert, arguments);
  }
  (0, _inherits2.default)(Convert, _$e$modules$editor$do);
  return (0, _createClass2.default)(Convert, [{
    key: "getHistory",
    value: function getHistory() {
      return {
        type: __('Converted to Container', 'elementor'),
        title: __('Section', 'elementor')
      };
    }
  }, {
    key: "validateArgs",
    value: function validateArgs() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      this.requireContainer(args);
    }
  }, {
    key: "apply",
    value: function apply(args) {
      this.constructor.convert(args);
    }

    /**
     * Convert an element to Container.
     *
     * TODO: It's static in order to be able to test it without initializing the whole editor in a browser.
     *  Should be moved to `apply()` when there is a proper way to test commands using jest.
     *
     * @param {Object}    root0
     * @param {Container} root0.container     - Element to convert.
     * @param {Container} root0.rootContainer - Root element to migrate the `container` into (used for recursion).
     *
     * @return {void}
     */
  }], [{
    key: "convert",
    value: function convert(_ref) {
      var container = _ref.container,
        _ref$rootContainer = _ref.rootContainer,
        rootContainer = _ref$rootContainer === void 0 ? container.parent : _ref$rootContainer;
      var view = container.view,
        elType = container.type,
        isFirst = rootContainer === container.parent;

      // TODO: Maybe use `view._parent.collection.indexOf( this.model )`.
      // Get the converted element index. The first converted element should be put after the original one.
      var at = isFirst ? view._index + 1 : view._index;

      // Copy the element as is without converting.
      if (!_migrator.default.canConvertToContainer(elType)) {
        $e.run('document/elements/create', {
          model: {
            elType: container.model.get('elType'),
            widgetType: container.model.get('widgetType'),
            settings: container.settings.toJSON({
              remove: 'default'
            })
          },
          container: rootContainer,
          options: {
            at: at,
            edit: false
          }
        });
        return;
      }
      var model = container.model.toJSON();
      var controlsMapping = _migrator.default.getLegacyControlsMapping(model);
      var settings = container.settings.toJSON({
        remove: 'default'
      });
      settings = _migrator.default.migrate(settings, controlsMapping);
      settings = _migrator.default.normalizeSettings(model, settings);
      var newContainer = $e.run('document/elements/create', {
        model: {
          elType: 'container',
          settings: settings
        },
        container: rootContainer,
        options: {
          at: at,
          edit: false
        }
      });

      // Recursively convert children to Containers.
      container.children.forEach(function (child) {
        $e.run('container-converter/convert', {
          container: child,
          rootContainer: newContainer
        });
      });
    }
  }]);
}($e.modules.editor.document.CommandHistoryBase);

/***/ }),

/***/ "../modules/container-converter/assets/js/editor/commands/index.js":
/*!*************************************************************************!*\
  !*** ../modules/container-converter/assets/js/editor/commands/index.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "Convert", ({
  enumerable: true,
  get: function get() {
    return _convert.Convert;
  }
}));
Object.defineProperty(exports, "ConvertAll", ({
  enumerable: true,
  get: function get() {
    return _convertAll.ConvertAll;
  }
}));
var _convert = __webpack_require__(/*! ./convert */ "../modules/container-converter/assets/js/editor/commands/convert.js");
var _convertAll = __webpack_require__(/*! ./convert-all */ "../modules/container-converter/assets/js/editor/commands/convert-all.js");

/***/ }),

/***/ "../modules/container-converter/assets/js/editor/component.js":
/*!********************************************************************!*\
  !*** ../modules/container-converter/assets/js/editor/component.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


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
var commands = _interopRequireWildcard(__webpack_require__(/*! ./commands/ */ "../modules/container-converter/assets/js/editor/commands/index.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var _default = exports["default"] = /*#__PURE__*/function (_$e$modules$Component) {
  function _default() {
    var _this;
    (0, _classCallCheck2.default)(this, _default);
    _this = _callSuper(this, _default);
    _this.bindEvents();
    return _this;
  }

  /**
   * Listen to click event in the panel.
   *
   * @return {void}
   */
  (0, _inherits2.default)(_default, _$e$modules$Component);
  return (0, _createClass2.default)(_default, [{
    key: "bindEvents",
    value: function bindEvents() {
      elementor.channels.editor.on('elementorContainerConverter:convert', function (_ref) {
        var container = _ref.container,
          el = _ref.el;
        var button = el.querySelector('.elementor-button');
        var loadingClass = 'e-loading';
        button.classList.add(loadingClass);

        // Defer the conversion process in order to force a re-render of the button, since the conversion is
        // synchronous and blocks the main thread from re-rendering.
        setTimeout(function () {
          if ('document' === container.type) {
            $e.run('container-converter/convert-all');
          } else {
            $e.run('container-converter/convert', {
              container: container
            });
          }
          button.classList.remove(loadingClass);
          button.setAttribute('disabled', true);
          elementor.notifications.showToast({
            message: __('Your changes have been updated.', 'elementor')
          });
        });
      });
    }

    /**
     * Get the component namespace.
     *
     * @return {string} component namespace
     */
  }, {
    key: "getNamespace",
    value: function getNamespace() {
      return 'container-converter';
    }

    /**
     * Get the component default commands.
     *
     * @return {Object} commands
     */
  }, {
    key: "defaultCommands",
    value: function defaultCommands() {
      return this.importCommands(commands);
    }
  }]);
}($e.modules.ComponentBase);

/***/ }),

/***/ "../modules/container-converter/assets/js/editor/maps/column.js":
/*!**********************************************************************!*\
  !*** ../modules/container-converter/assets/js/editor/maps/column.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _utils = __webpack_require__(/*! ./utils */ "../modules/container-converter/assets/js/editor/maps/utils.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var map = function map() {
  return _objectSpread(_objectSpread(_objectSpread({}, (0, _utils.responsive)('_inline_size', function (_ref) {
    var deviceValue = _ref.deviceValue,
      breakpoint = _ref.breakpoint;
    var deviceKey = (0, _utils.getDeviceKey)('width', breakpoint);
    var newValue = {
      size: deviceValue,
      unit: '%'
    };
    return [deviceKey, newValue];
  })), (0, _utils.responsive)('content_position', function (_ref2) {
    var deviceValue = _ref2.deviceValue,
      breakpoint = _ref2.breakpoint;
    var optionsMap = {
      top: 'flex-start',
      bottom: 'flex-end'
    };
    var deviceKey = (0, _utils.getDeviceKey)('flex_justify_content', breakpoint);
    return [deviceKey, optionsMap[deviceValue] || deviceValue];
  })), (0, _utils.responsive)('space_between_widgets', function (_ref3) {
    var deviceValue = _ref3.deviceValue,
      breakpoint = _ref3.breakpoint;
    var deviceKey = (0, _utils.getDeviceKey)('flex_gap', breakpoint);
    var newValue = {
      size: deviceValue,
      column: '' + deviceValue,
      row: '' + deviceValue,
      unit: 'px'
    };
    return [deviceKey, newValue];
  }));
};
var _default = exports["default"] = map;

/***/ }),

/***/ "../modules/container-converter/assets/js/editor/maps/section.js":
/*!***********************************************************************!*\
  !*** ../modules/container-converter/assets/js/editor/maps/section.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _utils = __webpack_require__(/*! ./utils */ "../modules/container-converter/assets/js/editor/maps/utils.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var map = function map(_ref) {
  var isInner = _ref.isInner,
    _ref$settings = _ref.settings,
    settings = _ref$settings === void 0 ? {} : _ref$settings;
  var widthKey = isInner ? 'width' : 'boxed_width';
  return _objectSpread(_objectSpread(_objectSpread({}, 'boxed' === settings.layout ? (0, _utils.responsive)('content_width', widthKey) : {
    content_width: null
  }), 'min-height' === settings.height && (0, _utils.responsive)('custom_height', 'min_height')), {}, {
    layout: function layout(_ref2) {
      var value = _ref2.value;
      var optionsMap = {
        boxed: 'boxed',
        full_width: 'full'
      };
      return ['content_width', optionsMap[value] || value];
    },
    height: function height(_ref3) {
      var value = _ref3.value,
        sectionSettings = _ref3.settings;
      switch (value) {
        case 'full':
          value = {
            size: 100,
            unit: 'vh'
          };
          break;
        case 'min-height':
          value = sectionSettings.custom_height || {
            size: 400,
            unit: 'px'
          }; // Default section's height.
          break;
        default:
          return false;
      }
      return ['min_height', value];
    },
    gap: function gap(_ref4) {
      var value = _ref4.value,
        sectionSettings = _ref4.settings;
      var sizesMap = {
        no: 0,
        narrow: 5,
        extended: 15,
        wide: 20,
        wider: 30
      };
      value = 'custom' === value ? sectionSettings.gap_columns_custom : {
        size: sizesMap[value],
        column: '' + sizesMap[value],
        row: '' + sizesMap[value],
        unit: 'px'
      };
      return ['flex_gap', value];
    },
    gap_columns_custom: null,
    column_position: function column_position(_ref5) {
      var value = _ref5.value;
      var optionsMap = {
        top: 'flex-start',
        middle: 'center',
        bottom: 'flex-end'
      };
      return ['flex_align_items', optionsMap[value] || value];
    }
  });
};
var _default = exports["default"] = map;

/***/ }),

/***/ "../modules/container-converter/assets/js/editor/maps/utils.js":
/*!*********************************************************************!*\
  !*** ../modules/container-converter/assets/js/editor/maps/utils.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.getDeviceKey = getDeviceKey;
exports.responsive = responsive;
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
/**
 * Generate a mapping object for responsive controls.
 *
 * Usage:
 *  1. responsive( 'old_key', 'new_key' );
 *  2. responsive( 'old_key', ( { key, value, deviceValue, settings, breakpoint } ) => { return [ key, value ] } );
 *
 * @param {string}            key   - Control name without device suffix.
 * @param {string | Function} value - New control name without device suffix, or a callback.
 *
 * @return {Object} mapping object
 */
function responsive(key, value) {
  var breakpoints = [''].concat((0, _toConsumableArray2.default)(Object.keys(elementorFrontend.config.responsive.activeBreakpoints)));
  return Object.fromEntries(breakpoints.map(function (breakpoint) {
    var deviceKey = getDeviceKey(key, breakpoint);

    // Simple responsive rename with string:
    if ('string' === typeof value) {
      var newDeviceKey = getDeviceKey(value, breakpoint);
      return [deviceKey, function (_ref) {
        var settings = _ref.settings;
        return [newDeviceKey, settings[deviceKey]];
      }];
    }

    // Advanced responsive rename with callback:
    return [deviceKey, function (_ref2) {
      var settings = _ref2.settings,
        desktopValue = _ref2.value;
      return value({
        key: key,
        deviceKey: deviceKey,
        value: desktopValue,
        deviceValue: settings[deviceKey],
        settings: settings,
        breakpoint: breakpoint
      });
    }];
  }));
}

/**
 * Get a setting key for a device.
 *
 * Examples:
 *  1. getDeviceKey( 'some_control', 'mobile' ) => 'some_control_mobile'.
 *  2. getDeviceKey( 'some_control', '' ) => 'some_control'.
 *
 * @param {string} key        - Setting key.
 * @param {string} breakpoint - Breakpoint name.
 *
 * @return {string} device key
 */
function getDeviceKey(key, breakpoint) {
  return [key, breakpoint].filter(function (v) {
    return !!v;
  }).join('_');
}

/***/ }),

/***/ "../modules/container-converter/assets/js/editor/migrator.js":
/*!*******************************************************************!*\
  !*** ../modules/container-converter/assets/js/editor/migrator.js ***!
  \*******************************************************************/
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
var _section = _interopRequireDefault(__webpack_require__(/*! ./maps/section */ "../modules/container-converter/assets/js/editor/maps/section.js"));
var _column = _interopRequireDefault(__webpack_require__(/*! ./maps/column */ "../modules/container-converter/assets/js/editor/maps/column.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var Migrator = exports["default"] = /*#__PURE__*/function () {
  function Migrator() {
    (0, _classCallCheck2.default)(this, Migrator);
  }
  return (0, _createClass2.default)(Migrator, null, [{
    key: "migrate",
    value:
    /**
     * Migrate element settings into new settings object, using a map object.
     *
     * @param {Object} settings - Settings to migrate.
     * @param {Object} map      - Mapping object.
     *
     * @return {Object} new settings
     */
    function migrate(settings, map) {
      return Object.fromEntries(Object.entries(_objectSpread({}, settings)).map(function (_ref) {
        var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
          key = _ref2[0],
          value = _ref2[1];
        var mapped = map[key];

        // Remove setting.
        if (null === mapped) {
          return null;
        }

        // Simple key conversion:
        // { old_setting: 'new_setting' }
        if ('string' === typeof mapped) {
          return [mapped, value];
        }

        // Advanced conversion using a callback:
        // { old_setting: ( { key, value, settings } ) => [ 'new_setting', value ] }
        if ('function' === typeof mapped) {
          return mapped({
            key: key,
            value: value,
            settings: settings
          });
        }
        return [key, value];
      }).filter(Boolean));
    }

    /**
     * Determine if an element can be converted to a Container.
     *
     * @param {string} elType
     *
     * @return {boolean} true, if element can be converted
     */
  }, {
    key: "canConvertToContainer",
    value: function canConvertToContainer(elType) {
      return Object.keys(this.config).includes(elType);
    }

    /**
     * Get a mapping object of Legacy-to-Container controls mapping.
     *
     * @param {Object} model - Element model.
     *
     * @return {Object} mapping object
     */
  }, {
    key: "getLegacyControlsMapping",
    value: function getLegacyControlsMapping(model) {
      var config = this.config[model.elType];
      if (!config) {
        return {};
      }
      var mapping = config.legacyControlsMapping;
      return 'function' === typeof mapping ? mapping(model) : mapping;
    }

    /**
     * Normalize element settings (adding defaults, etc.) by elType,
     *
     * @param {Object} model    - Element model.
     * @param {Object} settings - Settings object after migration.
     *
     * @return {Object} - normalized settings.
     */
  }, {
    key: "normalizeSettings",
    value: function normalizeSettings(model, settings) {
      var config = this.config[model.elType];
      if (!config.normalizeSettings) {
        return settings;
      }
      return config.normalizeSettings(settings, model);
    }
  }]);
}();
/**
 * Migrations configuration by `elType`.
 *
 * @type {Object}
 */
(0, _defineProperty2.default)(Migrator, "config", {
  section: {
    legacyControlsMapping: _section.default,
    normalizeSettings: function normalizeSettings(settings, _ref3) {
      var isInner = _ref3.isInner;
      return _objectSpread(_objectSpread({}, settings), {}, {
        flex_direction: 'row',
        // Force it to be row.
        // Defaults (since default settings are removed):
        flex_align_items: settings.flex_align_items || 'stretch',
        flex_gap: settings.flex_gap || {
          size: 10,
          column: '10',
          row: '10',
          unit: 'px'
        }
      }, isInner ? {
        content_width: 'full'
      } : {});
    }
  },
  column: {
    legacyControlsMapping: _column.default,
    normalizeSettings: function normalizeSettings(settings) {
      return _objectSpread(_objectSpread({}, settings), {}, {
        content_width: 'full'
      });
    }
  }
});

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

/***/ "../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _arrayWithoutHoles(r) {
  if (Array.isArray(r)) return arrayLikeToArray(r);
}
module.exports = _arrayWithoutHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/iterableToArray.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/iterableToArray.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _iterableToArray(r) {
  if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r);
}
module.exports = _iterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/nonIterableSpread.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/nonIterableSpread.js ***!
  \*******************************************************************/
/***/ ((module) => {

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableSpread, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/toConsumableArray.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toConsumableArray.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayWithoutHoles = __webpack_require__(/*! ./arrayWithoutHoles.js */ "../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js");
var iterableToArray = __webpack_require__(/*! ./iterableToArray.js */ "../node_modules/@babel/runtime/helpers/iterableToArray.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableSpread = __webpack_require__(/*! ./nonIterableSpread.js */ "../node_modules/@babel/runtime/helpers/nonIterableSpread.js");
function _toConsumableArray(r) {
  return arrayWithoutHoles(r) || iterableToArray(r) || unsupportedIterableToArray(r) || nonIterableSpread();
}
module.exports = _toConsumableArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

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
/*!*****************************************************************!*\
  !*** ../modules/container-converter/assets/js/editor/module.js ***!
  \*****************************************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _component = _interopRequireDefault(__webpack_require__(/*! ./component */ "../modules/container-converter/assets/js/editor/component.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Module = /*#__PURE__*/function (_elementorModules$edi) {
  function Module() {
    (0, _classCallCheck2.default)(this, Module);
    return _callSuper(this, Module, arguments);
  }
  (0, _inherits2.default)(Module, _elementorModules$edi);
  return (0, _createClass2.default)(Module, [{
    key: "onInit",
    value: function onInit() {
      $e.components.register(new _component.default());
    }
  }]);
}(elementorModules.editor.utils.Module);
new Module();
})();

/******/ })()
;
//# sourceMappingURL=container-converter.js.map