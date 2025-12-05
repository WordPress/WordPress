/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../assets/dev/js/admin/floating-elements/layout.js":
/*!**********************************************************!*\
  !*** ../assets/dev/js/admin/floating-elements/layout.js ***!
  \**********************************************************/
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
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _view = _interopRequireDefault(__webpack_require__(/*! elementor-admin/floating-elements/view */ "../assets/dev/js/admin/floating-elements/view.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! elementor-common/views/modal/layout */ "../core/common/assets/js/views/modal/layout.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var _default = exports["default"] = /*#__PURE__*/function (_ModalLayout) {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _callSuper(this, _default, arguments);
  }
  (0, _inherits2.default)(_default, _ModalLayout);
  return (0, _createClass2.default)(_default, [{
    key: "getModalOptions",
    value: function getModalOptions() {
      return {
        id: 'elementor-new-floating-elements-modal'
      };
    }
  }, {
    key: "getLogoOptions",
    value: function getLogoOptions() {
      return {
        title: __('New Floating Elements', 'elementor')
      };
    }
  }, {
    key: "initialize",
    value: function initialize() {
      _superPropGet(_default, "initialize", this, 3)([]);
      this.showLogo();
      this.showContentView();
    }
  }, {
    key: "showContentView",
    value: function showContentView() {
      this.modalContent.show(new _view.default());
    }
  }]);
}(_layout.default);

/***/ }),

/***/ "../assets/dev/js/admin/floating-elements/view.js":
/*!********************************************************!*\
  !*** ../assets/dev/js/admin/floating-elements/view.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _default = exports["default"] = Marionette.ItemView.extend({
  id: 'elementor-new-floating-elements-dialog-content',
  template: '#tmpl-elementor-new-floating-elements',
  ui: {},
  events: {},
  onRender: function onRender() {}
});

/***/ }),

/***/ "../core/common/assets/js/views/modal/header.js":
/*!******************************************************!*\
  !*** ../core/common/assets/js/views/modal/header.js ***!
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
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var _default = exports["default"] = /*#__PURE__*/function (_Marionette$LayoutVie) {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _callSuper(this, _default, arguments);
  }
  (0, _inherits2.default)(_default, _Marionette$LayoutVie);
  return (0, _createClass2.default)(_default, [{
    key: "className",
    value: function className() {
      return 'elementor-templates-modal__header';
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return '#tmpl-elementor-templates-modal__header';
    }
  }, {
    key: "regions",
    value: function regions() {
      return {
        logoArea: '.elementor-templates-modal__header__logo-area',
        tools: '#elementor-template-library-header-tools',
        menuArea: '.elementor-templates-modal__header__menu-area'
      };
    }
  }, {
    key: "ui",
    value: function ui() {
      return {
        closeModal: '.elementor-templates-modal__header__close'
      };
    }
  }, {
    key: "events",
    value: function events() {
      return {
        'click @ui.closeModal': 'onCloseModalClick'
      };
    }
  }, {
    key: "onRender",
    value: function onRender() {
      this.bindEscapeKey();
    }
  }, {
    key: "bindEscapeKey",
    value: function bindEscapeKey() {
      var _this = this;
      this.onDocumentKeyDown = function (event) {
        if ('Escape' === event.key) {
          _this.onCloseModalClick();
        }
      };
      document.addEventListener('keydown', this.onDocumentKeyDown);
    }
  }, {
    key: "onDestroy",
    value: function onDestroy() {
      if (this.onDocumentKeyDown) {
        document.removeEventListener('keydown', this.onDocumentKeyDown);
      }
    }
  }, {
    key: "templateHelpers",
    value: function templateHelpers() {
      return {
        closeType: this.getOption('closeType')
      };
    }
  }, {
    key: "onCloseModalClick",
    value: function onCloseModalClick() {
      this._parent._parent._parent.hideModal();
      var documentType = this.getDocumentType();
      var customEvent = new CustomEvent("core/modal/close/".concat(documentType));
      window.dispatchEvent(customEvent);
      if (this.isFloatingButtonLibraryClose()) {
        $e.internal('document/save/set-is-modified', {
          status: false
        });
        window.location.href = elementor.config.admin_floating_button_admin_url;
      }
    }
  }, {
    key: "getDocumentType",
    value: function getDocumentType() {
      var _elementor$config$doc, _elementor;
      var DEFAULT_TYPE = 'default';
      if ('undefined' === typeof window.elementor) {
        return DEFAULT_TYPE;
      }
      return (_elementor$config$doc = (_elementor = elementor) === null || _elementor === void 0 || (_elementor = _elementor.config) === null || _elementor === void 0 || (_elementor = _elementor.document) === null || _elementor === void 0 ? void 0 : _elementor.type) !== null && _elementor$config$doc !== void 0 ? _elementor$config$doc : DEFAULT_TYPE;
    }
  }, {
    key: "isFloatingButtonLibraryClose",
    value: function isFloatingButtonLibraryClose() {
      var _elementor$config, _elementor$config2;
      return window.elementor && ((_elementor$config = elementor.config) === null || _elementor$config === void 0 ? void 0 : _elementor$config.admin_floating_button_admin_url) && 'floating-buttons' === ((_elementor$config2 = elementor.config) === null || _elementor$config2 === void 0 || (_elementor$config2 = _elementor$config2.document) === null || _elementor$config2 === void 0 ? void 0 : _elementor$config2.type) && (this.$el.closest('.dialog-lightbox-widget-content').find('.elementor-template-library-template-floating_button').length || this.$el.closest('.dialog-lightbox-widget-content').find('#elementor-template-library-preview').length || this.$el.closest('.dialog-lightbox-widget-content').find('#elementor-template-library-templates-empty').length);
    }
  }]);
}(Marionette.LayoutView);

/***/ }),

/***/ "../core/common/assets/js/views/modal/layout.js":
/*!******************************************************!*\
  !*** ../core/common/assets/js/views/modal/layout.js ***!
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
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _header = _interopRequireDefault(__webpack_require__(/*! ./header */ "../core/common/assets/js/views/modal/header.js"));
var _logo = _interopRequireDefault(__webpack_require__(/*! ./logo */ "../core/common/assets/js/views/modal/logo.js"));
var _loading = _interopRequireDefault(__webpack_require__(/*! ./loading */ "../core/common/assets/js/views/modal/loading.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var _default = exports["default"] = /*#__PURE__*/function (_Marionette$LayoutVie) {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _callSuper(this, _default, arguments);
  }
  (0, _inherits2.default)(_default, _Marionette$LayoutVie);
  return (0, _createClass2.default)(_default, [{
    key: "el",
    value: function el() {
      return this.getModal().getElements('widget');
    }
  }, {
    key: "regions",
    value: function regions() {
      return {
        modalHeader: '.dialog-header',
        modalContent: '.dialog-lightbox-content',
        modalLoading: '.dialog-lightbox-loading'
      };
    }
  }, {
    key: "initialize",
    value: function initialize() {
      this.modalHeader.show(new _header.default(this.getHeaderOptions()));
    }
  }, {
    key: "getModal",
    value: function getModal() {
      if (!this.modal) {
        this.initModal();
      }
      return this.modal;
    }
  }, {
    key: "initModal",
    value: function initModal() {
      var modalOptions = {
        className: 'elementor-templates-modal',
        closeButton: false,
        draggable: false,
        hide: {
          onOutsideClick: false,
          onEscKeyPress: false
        }
      };
      jQuery.extend(true, modalOptions, this.getModalOptions());
      this.modal = elementorCommon.dialogsManager.createWidget('lightbox', modalOptions);
      this.modal.getElements('message').append(this.modal.addElement('content'), this.modal.addElement('loading'));
      if (modalOptions.draggable) {
        this.draggableModal();
      }
    }
  }, {
    key: "showModal",
    value: function showModal() {
      this.getModal().show();
    }
  }, {
    key: "hideModal",
    value: function hideModal() {
      this.getModal().hide();
    }
  }, {
    key: "draggableModal",
    value: function draggableModal() {
      var $modalWidgetContent = this.getModal().getElements('widgetContent');
      $modalWidgetContent.draggable({
        containment: 'parent',
        stop: function stop() {
          $modalWidgetContent.height('');
        }
      });
      $modalWidgetContent.css('position', 'absolute');
    }
  }, {
    key: "getModalOptions",
    value: function getModalOptions() {
      return {};
    }
  }, {
    key: "getLogoOptions",
    value: function getLogoOptions() {
      return {};
    }
  }, {
    key: "getHeaderOptions",
    value: function getHeaderOptions() {
      return {
        closeType: 'normal'
      };
    }
  }, {
    key: "getHeaderView",
    value: function getHeaderView() {
      return this.modalHeader.currentView;
    }
  }, {
    key: "showLoadingView",
    value: function showLoadingView() {
      this.modalLoading.show(new _loading.default());
      this.modalLoading.$el.show();
      this.modalContent.$el.hide();
    }
  }, {
    key: "hideLoadingView",
    value: function hideLoadingView() {
      this.modalContent.$el.show();
      this.modalLoading.$el.hide();
    }
  }, {
    key: "showLogo",
    value: function showLogo() {
      this.getHeaderView().logoArea.show(new _logo.default(this.getLogoOptions()));
    }
  }]);
}(Marionette.LayoutView);

/***/ }),

/***/ "../core/common/assets/js/views/modal/loading.js":
/*!*******************************************************!*\
  !*** ../core/common/assets/js/views/modal/loading.js ***!
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
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var _default = exports["default"] = /*#__PURE__*/function (_Marionette$ItemView) {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _callSuper(this, _default, arguments);
  }
  (0, _inherits2.default)(_default, _Marionette$ItemView);
  return (0, _createClass2.default)(_default, [{
    key: "id",
    value: function id() {
      return 'elementor-template-library-loading';
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return '#tmpl-elementor-template-library-loading';
    }
  }]);
}(Marionette.ItemView);

/***/ }),

/***/ "../core/common/assets/js/views/modal/logo.js":
/*!****************************************************!*\
  !*** ../core/common/assets/js/views/modal/logo.js ***!
  \****************************************************/
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
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var _default = exports["default"] = /*#__PURE__*/function (_Marionette$ItemView) {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _callSuper(this, _default, arguments);
  }
  (0, _inherits2.default)(_default, _Marionette$ItemView);
  return (0, _createClass2.default)(_default, [{
    key: "getTemplate",
    value: function getTemplate() {
      return '#tmpl-elementor-templates-modal__header__logo';
    }
  }, {
    key: "className",
    value: function className() {
      return 'elementor-templates-modal__header__logo';
    }
  }, {
    key: "events",
    value: function events() {
      return {
        click: 'onClick'
      };
    }
  }, {
    key: "templateHelpers",
    value: function templateHelpers() {
      return {
        title: this.getOption('title')
      };
    }
  }, {
    key: "onClick",
    value: function onClick() {
      var clickCallback = this.getOption('click');
      if (clickCallback) {
        clickCallback();
      }
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
/*!*************************************************************************!*\
  !*** ../assets/dev/js/admin/floating-elements/new-floating-elements.js ***!
  \*************************************************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _layout = _interopRequireDefault(__webpack_require__(/*! elementor-admin/floating-elements/layout */ "../assets/dev/js/admin/floating-elements/layout.js"));
var NewFloatingElementsModule = elementorModules.ViewModule.extend({
  getDefaultSettings: function getDefaultSettings() {
    return {
      selectors: {
        addButtonTopBar: '.page-title-action',
        addButtonAdminBar: '#wp-admin-bar-new-e-floating-buttons a',
        addButtonEmptyTemplate: '#elementor-template-library-add-new'
      }
    };
  },
  getDefaultElements: function getDefaultElements() {
    var selectors = this.getSettings('selectors');
    return {
      addButtonTopBar: document.querySelector(selectors.addButtonTopBar),
      addButtonAdminBar: document.querySelector(selectors.addButtonAdminBar),
      addButtonEmptyTemplate: document.querySelector(selectors.addButtonEmptyTemplate)
    };
  },
  bindEvents: function bindEvents() {
    if (this.elements.addButtonTopBar !== null) {
      this.elements.addButtonTopBar.addEventListener('click', this.onAddButtonClick);
    }
    if (this.elements.addButtonAdminBar !== null) {
      this.elements.addButtonAdminBar.addEventListener('click', this.onAddButtonClick);
    }
    if (this.elements.addButtonEmptyTemplate !== null) {
      this.elements.addButtonEmptyTemplate.addEventListener('click', this.onAddButtonClick);
    }
  },
  onInit: function onInit() {
    elementorModules.ViewModule.prototype.onInit.apply(this, arguments);
    this.layout = new _layout.default();
  },
  onAddButtonClick: function onAddButtonClick(event) {
    event.preventDefault();
    this.layout.showModal();
  }
});
document.addEventListener('DOMContentLoaded', function () {
  window.elementorNewFloatingElements = new NewFloatingElementsModule();
});
})();

/******/ })()
;
//# sourceMappingURL=floating-elements-modal.js.map