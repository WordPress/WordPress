/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../app/assets/js/event-track/dashboard/action-controls.js":
/*!*****************************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/action-controls.js ***!
  \*****************************************************************/
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
var _wpDashboardTracking = _interopRequireWildcard(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _utils = __webpack_require__(/*! ./utils */ "../app/assets/js/event-track/dashboard/utils.js");
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t.return || t.return(); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var EXCLUDED_SELECTORS = {
  ADMIN_MENU: '#adminmenu',
  TOP_BAR: '.e-admin-top-bar',
  WP_ADMIN_BAR: '#wpadminbar',
  SUBMENU: '.wp-submenu',
  PROMO_PAGE: '.e-feature-promotion',
  PROMO_BLANK_STATE: '.elementor-blank_state',
  APP: '.e-app'
};
var ActionControlTracking = /*#__PURE__*/function (_BaseTracking) {
  function ActionControlTracking() {
    (0, _classCallCheck2.default)(this, ActionControlTracking);
    return _callSuper(this, ActionControlTracking, arguments);
  }
  (0, _inherits2.default)(ActionControlTracking, _BaseTracking);
  return (0, _createClass2.default)(ActionControlTracking, null, [{
    key: "init",
    value: function init() {
      if (!_utils.DashboardUtils.isElementorPage()) {
        return;
      }
      this.attachDelegatedHandlers();
      this.addTrackingAttributesToFilterButtons();
      this.initializeLinkDataIds();
    }
  }, {
    key: "initializeLinkDataIds",
    value: function initializeLinkDataIds() {
      var _this = this;
      var initializeLinks = function initializeLinks() {
        var links = document.querySelectorAll('a[href]');
        links.forEach(function (link) {
          if (_this.isExcludedElement(link) || _this.isNavigationLink(link) || link.hasAttribute('data-id')) {
            return;
          }
          var href = link.getAttribute('href');
          if (!href) {
            return;
          }
          var cleanedHref = _this.removeNonceFromUrl(href);
          if (cleanedHref) {
            link.setAttribute('data-id', cleanedHref);
          }
        });
      };
      if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', initializeLinks);
      } else {
        initializeLinks();
      }
    }
  }, {
    key: "addTrackingAttributesToFilterButtons",
    value: function addTrackingAttributesToFilterButtons() {
      var body = document.body;
      if (!body) {
        return;
      }
      var screenPrefix = '';
      switch (true) {
        case body.classList.contains('post-type-elementor_library'):
          screenPrefix = 'elementor_library-library';
          break;
        case body.classList.contains('post-type-e-floating-buttons'):
          screenPrefix = 'e-floating-buttons';
          break;
        default:
          return;
      }
      var addDataIdToListTableButtons = function addDataIdToListTableButtons() {
        var buttonConfigs = [{
          id: 'post-query-submit',
          suffix: 'filter'
        }, {
          id: 'search-submit',
          suffix: 'search'
        }, {
          id: 'doaction',
          suffix: 'apply'
        }, {
          id: 'doaction2',
          suffix: 'apply-bottom'
        }];
        buttonConfigs.forEach(function (config) {
          var button = document.getElementById(config.id);
          if (!button || button.hasAttribute('data-id')) {
            return;
          }
          button.setAttribute('data-id', "".concat(screenPrefix, "-button-").concat(config.suffix));
        });
      };
      if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', addDataIdToListTableButtons);
      } else {
        addDataIdToListTableButtons();
      }
    }
  }, {
    key: "isExcludedElement",
    value: function isExcludedElement(element) {
      for (var _i = 0, _Object$values = Object.values(EXCLUDED_SELECTORS); _i < _Object$values.length; _i++) {
        var selector = _Object$values[_i];
        if (element.closest(selector)) {
          return true;
        }
      }
      if (element.classList.contains('go-pro')) {
        return true;
      }
      return false;
    }
  }, {
    key: "attachDelegatedHandlers",
    value: function attachDelegatedHandlers() {
      var _this2 = this;
      var FILTER_BUTTON_IDS = ['search-submit', 'post-query-submit'];
      this.addEventListenerTracked(document, 'click', function (event) {
        var _event$target;
        var base = event.target && 1 === event.target.nodeType ? event.target : (_event$target = event.target) === null || _event$target === void 0 ? void 0 : _event$target.parentElement;
        if (!base) {
          return;
        }
        var button = base.closest('button, input[type="submit"], input[type="button"], .button, .e-btn');
        if (button && !_this2.isExcludedElement(button)) {
          if (FILTER_BUTTON_IDS.includes(button.id)) {
            _this2.trackControl(button, _wpDashboardTracking.CONTROL_TYPES.FILTER);
            return;
          }
          _this2.trackControl(button, _wpDashboardTracking.CONTROL_TYPES.BUTTON);
          return;
        }
        var link = base.closest('a');
        if (link && !_this2.isExcludedElement(link) && !_this2.isNavigationLink(link)) {
          _this2.trackControl(link, _wpDashboardTracking.CONTROL_TYPES.LINK);
        }
      }, {
        capture: false
      });
      this.addEventListenerTracked(document, 'change', function (event) {
        var _event$target2;
        var base = event.target && 1 === event.target.nodeType ? event.target : (_event$target2 = event.target) === null || _event$target2 === void 0 ? void 0 : _event$target2.parentElement;
        if (!base) {
          return;
        }
        var toggle = base.closest('.components-toggle-control');
        if (toggle && !_this2.isExcludedElement(toggle)) {
          _this2.trackControl(toggle, _wpDashboardTracking.CONTROL_TYPES.TOGGLE);
          return;
        }
        var checkbox = base.closest('input[type="checkbox"]');
        if (checkbox && !_this2.isExcludedElement(checkbox)) {
          _this2.trackControl(checkbox, _wpDashboardTracking.CONTROL_TYPES.CHECKBOX);
          return;
        }
        var radio = base.closest('input[type="radio"]');
        if (radio && !_this2.isExcludedElement(radio)) {
          _this2.trackControl(radio, _wpDashboardTracking.CONTROL_TYPES.RADIO);
          return;
        }
        var select = base.closest('select');
        if (select && !_this2.isExcludedElement(select)) {
          _this2.trackControl(select, _wpDashboardTracking.CONTROL_TYPES.SELECT);
        }
      });
    }
  }, {
    key: "isNavigationLink",
    value: function isNavigationLink(link) {
      var href = link.getAttribute('href');
      if (!href) {
        return false;
      }
      if (href.startsWith('#') && href.includes('tab')) {
        return true;
      }
      if (link.classList.contains('nav-tab')) {
        return true;
      }
      var isInNavigation = link.closest('.wp-submenu, #adminmenu, .e-admin-top-bar, #wpadminbar');
      return !!isInNavigation;
    }
  }, {
    key: "trackControl",
    value: function trackControl(element, controlType) {
      var controlIdentifier = this.extractControlIdentifier(element, controlType);
      if (!controlIdentifier) {
        return;
      }
      _wpDashboardTracking.default.trackActionControl(controlIdentifier, controlType);
    }
  }, {
    key: "extractControlIdentifier",
    value: function extractControlIdentifier(element, controlType) {
      if (_wpDashboardTracking.CONTROL_TYPES.RADIO === controlType) {
        var name = element.getAttribute('name');
        var value = element.value || element.getAttribute('value');
        if (name && value) {
          return "".concat(name, "-").concat(value);
        }
        if (name) {
          return name;
        }
      }
      if (_wpDashboardTracking.CONTROL_TYPES.SELECT === controlType) {
        var _name = element.getAttribute('name');
        if (_name) {
          return _name;
        }
      }
      if (_wpDashboardTracking.CONTROL_TYPES.CHECKBOX === controlType) {
        var _name2 = element.getAttribute('name');
        if (_name2) {
          var checkboxesWithSameName = document.querySelectorAll("input[type=\"checkbox\"][name=\"".concat(CSS.escape(_name2), "\"]"));
          if (checkboxesWithSameName.length > 1) {
            var _value = element.value || element.getAttribute('value');
            if (_value) {
              return "".concat(_name2, "-").concat(_value);
            }
          }
          return _name2;
        }
      }
      if (_wpDashboardTracking.CONTROL_TYPES.LINK === controlType) {
        var dataId = element.getAttribute('data-id');
        if (dataId) {
          return dataId;
        }
        var href = element.getAttribute('href');
        if (href) {
          return this.removeNonceFromUrl(href);
        }
      }
      if (_wpDashboardTracking.CONTROL_TYPES.BUTTON === controlType || _wpDashboardTracking.CONTROL_TYPES.TOGGLE === controlType || _wpDashboardTracking.CONTROL_TYPES.FILTER === controlType) {
        var _dataId = element.getAttribute('data-id');
        if (_dataId) {
          return _dataId;
        }
        var classIdMatch = this.extractClassId(element);
        if (classIdMatch) {
          return classIdMatch;
        }
      }
      return '';
    }
  }, {
    key: "extractClassId",
    value: function extractClassId(element) {
      var classes = element.className;
      if (!classes || 'string' !== typeof classes) {
        return '';
      }
      var classList = classes.split(' ');
      var _iterator = _createForOfIteratorHelper(classList),
        _step;
      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var cls = _step.value;
          if (cls.startsWith('e-id-')) {
            return cls.substring(5);
          }
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }
      return '';
    }
  }, {
    key: "removeNonceFromUrl",
    value: function removeNonceFromUrl(url) {
      try {
        var urlObj = new URL(url, window.location.origin);
        urlObj.searchParams.delete('_wpnonce');
        var postParam = urlObj.searchParams.get('post');
        if (postParam !== null && /^[0-9]+$/.test(postParam)) {
          urlObj.searchParams.delete('post');
        }
        return urlObj.pathname + urlObj.search + urlObj.hash;
      } catch (e) {
        return url;
      }
    }
  }]);
}(_baseTracking.default);
var _default = exports["default"] = ActionControlTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/base-tracking.js":
/*!***************************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/base-tracking.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var BaseTracking = /*#__PURE__*/function () {
  function BaseTracking() {
    (0, _classCallCheck2.default)(this, BaseTracking);
  }
  return (0, _createClass2.default)(BaseTracking, null, [{
    key: "ensureOwnArrays",
    value: function ensureOwnArrays() {
      if (!Object.prototype.hasOwnProperty.call(this, 'observers')) {
        this.observers = [];
      }
      if (!Object.prototype.hasOwnProperty.call(this, 'eventListeners')) {
        this.eventListeners = [];
      }
    }
  }, {
    key: "destroy",
    value: function destroy() {
      this.ensureOwnArrays();
      this.observers.forEach(function (observer) {
        return observer.disconnect();
      });
      this.observers = [];
      this.eventListeners.forEach(function (_ref) {
        var target = _ref.target,
          type = _ref.type,
          handler = _ref.handler,
          options = _ref.options;
        target.removeEventListener(type, handler, options);
      });
      this.eventListeners = [];
    }
  }, {
    key: "addObserver",
    value: function addObserver(target, options, callback) {
      this.ensureOwnArrays();
      var observer = new MutationObserver(callback);
      observer.observe(target, options);
      this.observers.push(observer);
      return observer;
    }
  }, {
    key: "addEventListenerTracked",
    value: function addEventListenerTracked(target, type, handler) {
      var options = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
      this.ensureOwnArrays();
      target.addEventListener(type, handler, options);
      this.eventListeners.push({
        target: target,
        type: type,
        handler: handler,
        options: options
      });
    }
  }]);
}();
var _default = exports["default"] = BaseTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/menu-promotion.js":
/*!****************************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/menu-promotion.js ***!
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
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _wpDashboardTracking = _interopRequireDefault(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var PROMO_MENU_ITEMS = {
  go_elementor_pro: 'Upgrade'
};
var MenuPromotionTracking = /*#__PURE__*/function (_BaseTracking) {
  function MenuPromotionTracking() {
    (0, _classCallCheck2.default)(this, MenuPromotionTracking);
    return _callSuper(this, MenuPromotionTracking, arguments);
  }
  (0, _inherits2.default)(MenuPromotionTracking, _BaseTracking);
  return (0, _createClass2.default)(MenuPromotionTracking, null, [{
    key: "init",
    value: function init() {
      this.attachDelegatedTracking();
    }
  }, {
    key: "attachDelegatedTracking",
    value: function attachDelegatedTracking() {
      var _this = this;
      this.addEventListenerTracked(document, 'click', function (event) {
        var target = event.target;
        if (!target) {
          return;
        }
        var link = target.closest('a');
        if (!link) {
          return;
        }
        var href = link.getAttribute('href');
        if (!href) {
          return;
        }
        var menuItemKey = _this.extractPromoMenuKey(href);
        if (!menuItemKey) {
          return;
        }
        _this.handleMenuPromoClick(link, menuItemKey);
      }, {
        capture: true
      });
    }
  }, {
    key: "extractPromoMenuKey",
    value: function extractPromoMenuKey(href) {
      for (var _i = 0, _Object$keys = Object.keys(PROMO_MENU_ITEMS); _i < _Object$keys.length; _i++) {
        var menuItemKey = _Object$keys[_i];
        if (href.includes("page=".concat(menuItemKey))) {
          return menuItemKey;
        }
      }
      return null;
    }
  }, {
    key: "handleMenuPromoClick",
    value: function handleMenuPromoClick(menuItem, menuItemKey) {
      var destination = menuItem.getAttribute('href');
      var promoName = PROMO_MENU_ITEMS[menuItemKey];
      var path = menuItemKey.replace('elementor_', '').replace(/_/g, '/');
      _wpDashboardTracking.default.trackPromoClicked(promoName, destination, path);
    }
  }]);
}(_baseTracking.default);
var _default = exports["default"] = MenuPromotionTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/navigation.js":
/*!************************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/navigation.js ***!
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
var _wpDashboardTracking = _interopRequireWildcard(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var ELEMENTOR_MENU_SELECTORS = {
  ELEMENTOR_TOP_LEVEL: 'li#toplevel_page_elementor',
  TEMPLATES_TOP_LEVEL: 'li#menu-posts-elementor_library',
  ADMIN_MENU: '#adminmenu',
  TOP_LEVEL_LINK: '.wp-menu-name',
  SUBMENU_CONTAINER: '.wp-submenu',
  SUBMENU_ITEM: '.wp-submenu li a',
  SUBMENU_ITEM_TOP_LEVEL: '.wp-has-submenu'
};
var NavigationTracking = /*#__PURE__*/function (_BaseTracking) {
  function NavigationTracking() {
    (0, _classCallCheck2.default)(this, NavigationTracking);
    return _callSuper(this, NavigationTracking, arguments);
  }
  (0, _inherits2.default)(NavigationTracking, _BaseTracking);
  return (0, _createClass2.default)(NavigationTracking, null, [{
    key: "init",
    value: function init() {
      this.attachElementorMenuTracking();
      this.attachTemplatesMenuTracking();
    }
  }, {
    key: "attachElementorMenuTracking",
    value: function attachElementorMenuTracking() {
      var elementorMenu = document.querySelector(ELEMENTOR_MENU_SELECTORS.ELEMENTOR_TOP_LEVEL);
      if (!elementorMenu) {
        return;
      }
      this.attachMenuTracking(elementorMenu, 'Elementor');
    }
  }, {
    key: "attachTemplatesMenuTracking",
    value: function attachTemplatesMenuTracking() {
      var templatesMenu = document.querySelector(ELEMENTOR_MENU_SELECTORS.TEMPLATES_TOP_LEVEL);
      if (!templatesMenu) {
        return;
      }
      this.attachMenuTracking(templatesMenu, 'Templates');
    }
  }, {
    key: "attachMenuTracking",
    value: function attachMenuTracking(menuElement, menuName) {
      var _this = this;
      this.addEventListenerTracked(menuElement, 'click', function (event) {
        _this.handleMenuClick(event, menuName);
      });
    }
  }, {
    key: "handleMenuClick",
    value: function handleMenuClick(event, menuName) {
      var link = event.target.closest('a');
      if (!link) {
        return;
      }
      var isTopLevel = link.classList.contains('menu-top');
      var itemId = this.extractItemId(link);
      var area = this.determineNavArea(link);
      _wpDashboardTracking.default.trackNavClicked(itemId, isTopLevel ? null : menuName, area);
    }
  }, {
    key: "extractItemId",
    value: function extractItemId(link) {
      var textContent = link.textContent.trim();
      if (textContent) {
        return textContent;
      }
      var href = link.getAttribute('href');
      if (href) {
        var urlParams = new URLSearchParams(href.split('?')[1] || '');
        var page = urlParams.get('page');
        var postType = urlParams.get('post_type');
        if (page) {
          return page;
        }
        if (postType) {
          return postType;
        }
      }
      var id = link.getAttribute('id');
      if (id) {
        return id;
      }
      return 'unknown';
    }
  }, {
    key: "determineNavArea",
    value: function determineNavArea(link) {
      var parentMenu = link.closest('li.menu-top');
      if (parentMenu) {
        var isSubmenuItem = link.closest(ELEMENTOR_MENU_SELECTORS.SUBMENU_CONTAINER);
        if (isSubmenuItem) {
          var submenuElement = link.closest(ELEMENTOR_MENU_SELECTORS.SUBMENU_ITEM_TOP_LEVEL);
          if (submenuElement.classList.contains('wp-not-current-submenu')) {
            return _wpDashboardTracking.NAV_AREAS.HOVER_MENU;
          }
          return _wpDashboardTracking.NAV_AREAS.SUBMENU;
        }
        return _wpDashboardTracking.NAV_AREAS.LEFT_MENU;
      }
      return _wpDashboardTracking.NAV_AREAS.LEFT_MENU;
    }
  }]);
}(_baseTracking.default);
var _default = exports["default"] = NavigationTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/plugin-actions.js":
/*!****************************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/plugin-actions.js ***!
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
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _wpDashboardTracking = _interopRequireDefault(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var PLUGIN_TYPE = {
  ELEMENTOR: 'core',
  ELEMENTOR_PRO: 'pro'
};
var PluginActions = /*#__PURE__*/function (_BaseTracking) {
  function PluginActions() {
    (0, _classCallCheck2.default)(this, PluginActions);
    return _callSuper(this, PluginActions, arguments);
  }
  (0, _inherits2.default)(PluginActions, _BaseTracking);
  return (0, _createClass2.default)(PluginActions, null, [{
    key: "init",
    value: function init() {
      this.attachCoreDeactivationTracking();
      this.attachProDeactivationTracking();
      this.attachProDeletionTracking();
    }
  }, {
    key: "attachCoreDeactivationTracking",
    value: function attachCoreDeactivationTracking() {
      var _this = this;
      var dialogForm = document.querySelector('#elementor-deactivate-feedback-dialog-form');
      if (!dialogForm) {
        return;
      }
      this.addEventListenerTracked(dialogForm, 'change', function (event) {
        var target = event.target;
        if (target.classList.contains('elementor-deactivate-feedback-dialog-input')) {
          _this.selectedReason = target.value;
        }
      });
      this.observeModalButtons();
    }
  }, {
    key: "attachProDeactivationTracking",
    value: function attachProDeactivationTracking() {
      var _this2 = this;
      var pluginsTable = document.querySelector('.plugins');
      if (!pluginsTable) {
        return;
      }
      this.addEventListenerTracked(pluginsTable, 'click', function (event) {
        var link = event.target.closest('a');
        if (link && 'deactivate-elementor-pro' === link.id) {
          _this2.trackProDeactivation();
        }
      }, {
        capture: true
      });
    }
  }, {
    key: "observeModalButtons",
    value: function observeModalButtons() {
      var _this3 = this;
      var checkAndAttachDelegation = function checkAndAttachDelegation() {
        var modal = document.querySelector('#elementor-deactivate-feedback-modal');
        if (!modal) {
          return false;
        }
        _this3.addEventListenerTracked(modal, 'click', function (event) {
          var submitButton = event.target.closest('.dialog-submit');
          var skipButton = event.target.closest('.dialog-skip');
          if (submitButton) {
            _this3.trackCoreDeactivation('submit&deactivate');
          } else if (skipButton) {
            _this3.trackCoreDeactivation('skip&deactivate');
          }
        }, {
          capture: true
        });
        return true;
      };
      if (checkAndAttachDelegation()) {
        return;
      }
      this.addObserver(document.body, {
        childList: true,
        subtree: true
      }, function (mutations, observer) {
        if (checkAndAttachDelegation()) {
          observer.disconnect();
        }
      });
    }
  }, {
    key: "getUserInput",
    value: function getUserInput() {
      var reasonsWithInput = ['found_a_better_plugin', 'other'];
      if (!this.selectedReason || !reasonsWithInput.includes(this.selectedReason)) {
        return null;
      }
      var inputField = document.querySelector("input[name=\"reason_".concat(this.selectedReason, "\"]"));
      if (inputField && inputField.value) {
        return inputField.value;
      }
      return null;
    }
  }, {
    key: "trackCoreDeactivation",
    value: function trackCoreDeactivation(action) {
      var properties = {
        deactivate_form_submit: action,
        deactivate_plugin_type: PLUGIN_TYPE.ELEMENTOR
      };
      if (this.selectedReason) {
        properties.deactivate_feedback_reason = this.selectedReason;
      }
      var userInput = this.getUserInput();
      if (userInput) {
        properties.deactivate_feedback_reason += "/".concat(userInput);
      }
      _wpDashboardTracking.default.dispatchEvent('wpdash_deactivate_plugin', properties, {
        send_immediately: true
      });
    }
  }, {
    key: "trackProDeactivation",
    value: function trackProDeactivation() {
      this.trackProAction('deactivate');
    }
  }, {
    key: "attachProDeletionTracking",
    value: function attachProDeletionTracking() {
      var _this4 = this;
      if ('undefined' === typeof jQuery) {
        return;
      }
      jQuery(document).on('wp-plugin-deleting', function (event, args) {
        if ('elementor-pro' === (args === null || args === void 0 ? void 0 : args.slug)) {
          _this4.trackProAction('delete');
        }
      });
    }
  }, {
    key: "destroy",
    value: function destroy() {
      if ('undefined' !== typeof jQuery) {
        jQuery(document).off('wp-plugin-deleting');
      }
      _baseTracking.default.destroy.call(this);
    }
  }, {
    key: "trackProAction",
    value: function trackProAction(action) {
      var eventMap = {
        deactivate: {
          eventName: 'wpdash_deactivate_plugin',
          propertyKey: 'deactivate_plugin_type'
        },
        delete: {
          eventName: 'wpdash_delete_plugin',
          propertyKey: 'plugin_delete'
        }
      };
      var config = eventMap[action];
      if (!config) {
        return;
      }
      var properties = (0, _defineProperty2.default)({}, config.propertyKey, PLUGIN_TYPE.ELEMENTOR_PRO);
      _wpDashboardTracking.default.dispatchEvent(config.eventName, properties, {
        send_immediately: true
      });
    }
  }]);
}(_baseTracking.default);
(0, _defineProperty2.default)(PluginActions, "selectedReason", null);
var _default = exports["default"] = PluginActions;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/promotion.js":
/*!***********************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/promotion.js ***!
  \***********************************************************/
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
var _wpDashboardTracking = _interopRequireDefault(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var PROMO_SELECTORS = {
  PROMO_PAGE: '.e-feature-promotion, .elementor-settings-form-page, #elementor-element-manager-wrap',
  PROMO_BLANK_STATE: '.elementor-blank_state',
  CTA_BUTTON: '.go-pro',
  TITLE: 'h3'
};
var PromotionTracking = /*#__PURE__*/function (_BaseTracking) {
  function PromotionTracking() {
    (0, _classCallCheck2.default)(this, PromotionTracking);
    return _callSuper(this, PromotionTracking, arguments);
  }
  (0, _inherits2.default)(PromotionTracking, _BaseTracking);
  return (0, _createClass2.default)(PromotionTracking, null, [{
    key: "init",
    value: function init() {
      this.attachDelegatedTracking();
    }
  }, {
    key: "attachDelegatedTracking",
    value: function attachDelegatedTracking() {
      var _this = this;
      this.addEventListenerTracked(document, 'click', function (event) {
        var target = event.target;
        if (!target) {
          return;
        }
        var button = target.closest("a".concat(PROMO_SELECTORS.CTA_BUTTON));
        if (!button) {
          return;
        }
        var promoPage = button.closest("".concat(PROMO_SELECTORS.PROMO_PAGE, ", ").concat(PROMO_SELECTORS.PROMO_BLANK_STATE));
        if (!promoPage) {
          return;
        }
        _this.handlePromoClick(button, promoPage);
      }, {
        capture: true
      });
    }
  }, {
    key: "handlePromoClick",
    value: function handlePromoClick(button, promoPage) {
      var promoTitle = this.extractPromoTitle(promoPage, button);
      var destination = button.getAttribute('href');
      var path = this.extractPromoPath();
      _wpDashboardTracking.default.trackPromoClicked(promoTitle, destination, path);
    }
  }, {
    key: "extractPromoTitle",
    value: function extractPromoTitle(promoPage, button) {
      var titleElement = promoPage.querySelector(PROMO_SELECTORS.TITLE);
      return titleElement ? titleElement.textContent.trim() : button.textContent.trim();
    }
  }, {
    key: "extractPromoPath",
    value: function extractPromoPath() {
      var urlParams = new URLSearchParams(window.location.search);
      var page = urlParams.get('page');
      if (!page) {
        return 'elementor';
      }
      return page.replace('elementor_', '').replace(/_/g, '/');
    }
  }]);
}(_baseTracking.default);
var _default = exports["default"] = PromotionTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/screen-view.js":
/*!*************************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/screen-view.js ***!
  \*************************************************************/
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
var _wpDashboardTracking = _interopRequireWildcard(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _utils = __webpack_require__(/*! ./utils */ "../app/assets/js/event-track/dashboard/utils.js");
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t.return || t.return(); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var SCREEN_SELECTORS = {
  NAV_TAB_WRAPPER: '.nav-tab-wrapper',
  NAV_TAB: '.nav-tab',
  NAV_TAB_ACTIVE: '.nav-tab-active',
  SETTINGS_FORM_PAGE: '.elementor-settings-form-page',
  SETTINGS_FORM_PAGE_ACTIVE: '.elementor-settings-form-page.elementor-active',
  FLOATING_ELEMENTS_MODAL: '#elementor-new-floating-elements-modal',
  TEMPLATE_DIALOG_MODAL: '#elementor-new-template-dialog-content'
};
var TRACKED_MODALS = [SCREEN_SELECTORS.FLOATING_ELEMENTS_MODAL, SCREEN_SELECTORS.TEMPLATE_DIALOG_MODAL];
var ScreenViewTracking = /*#__PURE__*/function (_BaseTracking) {
  function ScreenViewTracking() {
    (0, _classCallCheck2.default)(this, ScreenViewTracking);
    return _callSuper(this, ScreenViewTracking, arguments);
  }
  (0, _inherits2.default)(ScreenViewTracking, _BaseTracking);
  return (0, _createClass2.default)(ScreenViewTracking, null, [{
    key: "init",
    value: function init() {
      if (!_utils.DashboardUtils.isElementorPage()) {
        return;
      }
      this.attachTabChangeTracking();
    }
  }, {
    key: "destroy",
    value: function destroy() {
      _superPropGet(ScreenViewTracking, "destroy", this, 2)([]);
      this.trackedScreens.clear();
    }
  }, {
    key: "getScreenData",
    value: function getScreenData() {
      var urlParams = new URLSearchParams(window.location.search);
      var page = urlParams.get('page');
      var postType = urlParams.get('post_type');
      var hash = window.location.hash;
      var screenId = '';
      var screenType = '';
      if (page) {
        screenId = page;
      } else if (postType) {
        screenId = postType;
      } else {
        screenId = this.getScreenIdFromBody();
      }
      if (this.isElementorAppPage()) {
        var appScreenData = this.getAppScreenData(hash);
        if (appScreenData) {
          return appScreenData;
        }
      }
      var hasNavTabs = document.querySelector(SCREEN_SELECTORS.NAV_TAB_WRAPPER);
      var hasSettingsTabs = document.querySelectorAll(SCREEN_SELECTORS.SETTINGS_FORM_PAGE).length > 1;
      if (hasNavTabs || hasSettingsTabs || hash && !this.isElementorAppPage()) {
        screenType = _wpDashboardTracking.SCREEN_TYPES.TAB;
        if (hash) {
          var tabId = hash.replace(/^#(tab-)?/, '');
          screenId = "".concat(screenId, "-").concat(tabId);
        } else if (hasNavTabs) {
          var activeTab = document.querySelector(SCREEN_SELECTORS.NAV_TAB_ACTIVE);
          if (activeTab) {
            var tabText = activeTab.textContent.trim();
            var tabHref = activeTab.getAttribute('href');
            if (tabText) {
              screenId = "".concat(screenId, "-").concat(this.sanitizeScreenId(tabText));
            } else if (tabHref && tabHref.includes('#')) {
              var _tabId = tabHref.split('#')[1];
              screenId = "".concat(screenId, "-").concat(_tabId);
            }
          }
        } else if (hasSettingsTabs) {
          var activeSettingsTab = document.querySelector(SCREEN_SELECTORS.SETTINGS_FORM_PAGE_ACTIVE);
          if (activeSettingsTab) {
            var _tabId2 = activeSettingsTab.id;
            if (_tabId2) {
              screenId = "".concat(screenId, "-").concat(_tabId2);
            }
          }
        }
      }
      return {
        screenId: screenId,
        screenType: screenType
      };
    }
  }, {
    key: "isElementorAppPage",
    value: function isElementorAppPage() {
      var urlParams = new URLSearchParams(window.location.search);
      return 'elementor-app' === urlParams.get('page');
    }
  }, {
    key: "getAppScreenData",
    value: function getAppScreenData(hash) {
      if (!hash) {
        return null;
      }
      var cleanHash = hash.replace(/^#/, '');
      if (!cleanHash.startsWith('/')) {
        return null;
      }
      var pathParts = cleanHash.split('/').filter(Boolean);
      if (0 === pathParts.length) {
        return null;
      }
      var screenId = pathParts.join('/');
      var screenType = _wpDashboardTracking.SCREEN_TYPES.APP_SCREEN;
      return {
        screenId: screenId,
        screenType: screenType
      };
    }
  }, {
    key: "getScreenIdFromBody",
    value: function getScreenIdFromBody() {
      var body = document.body;
      var bodyClasses = body.className.split(' ');
      var _iterator = _createForOfIteratorHelper(bodyClasses),
        _step;
      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var cls = _step.value;
          if (cls.startsWith('elementor') && (cls.includes('page') || cls.includes('post-type'))) {
            return cls;
          }
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }
      return 'elementor-unknown';
    }
  }, {
    key: "sanitizeScreenId",
    value: function sanitizeScreenId(text) {
      return text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    }
  }, {
    key: "attachTabChangeTracking",
    value: function attachTabChangeTracking() {
      this.attachNavTabTracking();
      this.attachHashChangeTracking();
      this.attachSettingsTabTracking();
      this.attachModalTracking();
    }
  }, {
    key: "attachNavTabTracking",
    value: function attachNavTabTracking() {
      var _this = this;
      var wrapper = document.querySelector(SCREEN_SELECTORS.NAV_TAB_WRAPPER);
      if (!wrapper) {
        return;
      }
      this.addEventListenerTracked(wrapper, 'click', function (event) {
        var navTab = event.target.closest(SCREEN_SELECTORS.NAV_TAB);
        if (navTab && !navTab.classList.contains('nav-tab-active')) {
          var screenData = _this.getScreenData();
          if (screenData) {
            _this.trackScreen(screenData.screenId, screenData.screenType);
          }
        }
      });
    }
  }, {
    key: "attachHashChangeTracking",
    value: function attachHashChangeTracking() {
      var _this2 = this;
      this.addEventListenerTracked(window, 'hashchange', function () {
        var screenData = _this2.getScreenData();
        if (screenData) {
          _this2.trackScreen(screenData.screenId, screenData.screenType);
        }
      });
    }
  }, {
    key: "attachSettingsTabTracking",
    value: function attachSettingsTabTracking() {
      var _this3 = this;
      var settingsPages = document.querySelectorAll(SCREEN_SELECTORS.SETTINGS_FORM_PAGE);
      if (0 === settingsPages.length) {
        return;
      }
      settingsPages.forEach(function (page) {
        _this3.addObserver(page, {
          attributes: true,
          attributeFilter: ['class']
        }, function () {
          var screenData = _this3.getScreenData();
          if (screenData) {
            _this3.trackScreen(screenData.screenId, screenData.screenType);
          }
        });
      });
    }
  }, {
    key: "attachModalTracking",
    value: function attachModalTracking() {
      var _this4 = this;
      this.addObserver(document.body, {
        childList: true,
        subtree: true
      }, function (mutations) {
        var _iterator2 = _createForOfIteratorHelper(mutations),
          _step2;
        try {
          for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
            var mutation = _step2.value;
            if ('childList' === mutation.type) {
              TRACKED_MODALS.forEach(function (modalSelector) {
                var modal = document.querySelector(modalSelector);
                if (modal && _this4.isModalVisible(modal)) {
                  var modalId = modalSelector.replace('#', '');
                  _this4.trackScreen(modalId, _wpDashboardTracking.SCREEN_TYPES.POPUP);
                }
              });
            }
          }
        } catch (err) {
          _iterator2.e(err);
        } finally {
          _iterator2.f();
        }
      });
    }
  }, {
    key: "isModalVisible",
    value: function isModalVisible(element) {
      if (!element) {
        return false;
      }
      var style = window.getComputedStyle(element);
      return 'none' !== style.display && 0 !== parseFloat(style.opacity);
    }
  }, {
    key: "trackScreen",
    value: function trackScreen(screenId) {
      var screenType = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : _wpDashboardTracking.SCREEN_TYPES.TOP_LEVEL_PAGE;
      var trackingKey = "".concat(screenId, "-").concat(screenType);
      if (this.trackedScreens.has(trackingKey)) {
        return;
      }
      this.trackedScreens.add(trackingKey);
      _wpDashboardTracking.default.trackScreenViewed(screenId, screenType);
    }
  }]);
}(_baseTracking.default);
(0, _defineProperty2.default)(ScreenViewTracking, "trackedScreens", new Set());
var _default = exports["default"] = ScreenViewTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/top-bar.js":
/*!*********************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/top-bar.js ***!
  \*********************************************************/
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
var _wpDashboardTracking = _interopRequireWildcard(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var TOP_BAR_SELECTORS = {
  TOP_BAR_ROOT: '.e-admin-top-bar',
  BAR_BUTTON: '.e-admin-top-bar__bar-button',
  BUTTON_TITLE: '.e-admin-top-bar__bar-button-title',
  MAIN_AREA: '.e-admin-top-bar__main-area',
  SECONDARY_AREA: '.e-admin-top-bar__secondary-area'
};
var TopBarTracking = /*#__PURE__*/function (_BaseTracking) {
  function TopBarTracking() {
    (0, _classCallCheck2.default)(this, TopBarTracking);
    return _callSuper(this, TopBarTracking, arguments);
  }
  (0, _inherits2.default)(TopBarTracking, _BaseTracking);
  return (0, _createClass2.default)(TopBarTracking, null, [{
    key: "init",
    value: function init() {
      this.waitForTopBar();
    }
  }, {
    key: "waitForTopBar",
    value: function waitForTopBar() {
      var _this = this;
      var topBar = document.querySelector(TOP_BAR_SELECTORS.TOP_BAR_ROOT);
      if (topBar) {
        this.attachTopBarTracking(topBar);
        return;
      }
      var observer = this.addObserver(document.body, {
        childList: true,
        subtree: true
      }, function () {
        var foundTopBar = document.querySelector(TOP_BAR_SELECTORS.TOP_BAR_ROOT);
        if (foundTopBar) {
          _this.attachTopBarTracking(foundTopBar);
          observer.disconnect();
          clearTimeout(timeoutId);
        }
      });
      var timeoutId = setTimeout(function () {
        observer.disconnect();
      }, 10000);
    }
  }, {
    key: "attachTopBarTracking",
    value: function attachTopBarTracking(topBar) {
      var _this2 = this;
      var buttons = topBar.querySelectorAll(TOP_BAR_SELECTORS.BAR_BUTTON);
      buttons.forEach(function (button) {
        _this2.addEventListenerTracked(button, 'click', function (event) {
          _this2.handleTopBarClick(event);
        });
      });
      this.observeTopBarChanges(topBar);
    }
  }, {
    key: "observeTopBarChanges",
    value: function observeTopBarChanges(topBar) {
      var _this3 = this;
      this.addObserver(topBar, {
        childList: true,
        subtree: true
      }, function (mutations) {
        mutations.forEach(function (mutation) {
          if ('childList' === mutation.type) {
            mutation.addedNodes.forEach(function (node) {
              if (1 === node.nodeType) {
                if (node.matches && node.matches(TOP_BAR_SELECTORS.BAR_BUTTON)) {
                  _this3.addEventListenerTracked(node, 'click', function (event) {
                    _this3.handleTopBarClick(event);
                  });
                } else {
                  var buttons = node.querySelectorAll ? node.querySelectorAll(TOP_BAR_SELECTORS.BAR_BUTTON) : [];
                  buttons.forEach(function (button) {
                    _this3.addEventListenerTracked(button, 'click', function (event) {
                      _this3.handleTopBarClick(event);
                    });
                  });
                }
              }
            });
          }
        });
      });
    }
  }, {
    key: "handleTopBarClick",
    value: function handleTopBarClick(event) {
      var button = event.currentTarget;
      var itemId = this.extractItemId(button);
      _wpDashboardTracking.default.trackNavClicked(itemId, null, _wpDashboardTracking.NAV_AREAS.TOP_BAR);
    }
  }, {
    key: "extractItemId",
    value: function extractItemId(button) {
      var titleElement = button.querySelector(TOP_BAR_SELECTORS.BUTTON_TITLE);
      if (titleElement && titleElement.textContent.trim()) {
        return titleElement.textContent.trim();
      }
      var textContent = button.textContent.trim();
      if (textContent) {
        return textContent;
      }
      var href = button.getAttribute('href');
      if (href) {
        var urlParams = new URLSearchParams(href.split('?')[1] || '');
        var page = urlParams.get('page');
        if (page) {
          return page;
        }
        if (href.includes('/wp-admin/')) {
          var pathParts = href.split('/wp-admin/')[1];
          if (pathParts) {
            return pathParts.split('?')[0];
          }
        }
        try {
          var url = new URL(href, window.location.origin);
          return url.pathname.split('/').filter(Boolean).pop() || url.hostname;
        } catch (error) {
          return href;
        }
      }
      var dataInfo = button.getAttribute('data-info');
      if (dataInfo) {
        return dataInfo;
      }
      var classes = button.className.split(' ').filter(function (cls) {
        return cls && 'e-admin-top-bar__bar-button' !== cls;
      });
      if (classes.length > 0) {
        return classes.join('-');
      }
      return 'unknown-top-bar-button';
    }
  }]);
}(_baseTracking.default);
var _default = exports["default"] = TopBarTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/utils.js":
/*!*******************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/utils.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.DashboardUtils = void 0;
var DashboardUtils = exports.DashboardUtils = {
  isElementorPage: function isElementorPage() {
    var urlParams = new URLSearchParams(window.location.search);
    var page = urlParams.get('page');
    if (page && (page.startsWith('elementor') || page.includes('elementor'))) {
      return true;
    }
    var postType = urlParams.get('post_type');
    if ('elementor_library' === postType || 'e-floating-buttons' === postType) {
      return true;
    }
    var body = document.body;
    var bodyClasses = body.className.split(' ');
    return bodyClasses.some(function (cls) {
      return cls.includes('elementor') && (cls.includes('page') || cls.includes('post-type'));
    });
  }
};

/***/ }),

/***/ "../app/assets/js/event-track/wp-dashboard-tracking.js":
/*!*************************************************************!*\
  !*** ../app/assets/js/event-track/wp-dashboard-tracking.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.SCREEN_TYPES = exports.NAV_AREAS = exports.CONTROL_TYPES = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _navigation = _interopRequireDefault(__webpack_require__(/*! ./dashboard/navigation */ "../app/assets/js/event-track/dashboard/navigation.js"));
var _pluginActions = _interopRequireDefault(__webpack_require__(/*! ./dashboard/plugin-actions */ "../app/assets/js/event-track/dashboard/plugin-actions.js"));
var _promotion = _interopRequireDefault(__webpack_require__(/*! ./dashboard/promotion */ "../app/assets/js/event-track/dashboard/promotion.js"));
var _screenView = _interopRequireDefault(__webpack_require__(/*! ./dashboard/screen-view */ "../app/assets/js/event-track/dashboard/screen-view.js"));
var _topBar = _interopRequireDefault(__webpack_require__(/*! ./dashboard/top-bar */ "../app/assets/js/event-track/dashboard/top-bar.js"));
var _menuPromotion = _interopRequireDefault(__webpack_require__(/*! ./dashboard/menu-promotion */ "../app/assets/js/event-track/dashboard/menu-promotion.js"));
var _actionControls = _interopRequireDefault(__webpack_require__(/*! ./dashboard/action-controls */ "../app/assets/js/event-track/dashboard/action-controls.js"));
var SESSION_TIMEOUT_MINUTES = 30;
var MINUTE_MS = 60 * 1000;
var SESSION_TIMEOUT = SESSION_TIMEOUT_MINUTES * MINUTE_MS;
var ACTIVITY_CHECK_INTERVAL = 1 * MINUTE_MS;
var SESSION_STORAGE_KEY = 'elementor_wpdash_session';
var PENDING_NAV_CLICK_KEY = 'elementor_wpdash_pending_nav';
var CONTROL_TYPES = exports.CONTROL_TYPES = {
  BUTTON: 'button',
  CHECKBOX: 'checkbox',
  RADIO: 'radio',
  LINK: 'link',
  SELECT: 'select',
  TOGGLE: 'toggle',
  FILTER: 'filter'
};
var NAV_AREAS = exports.NAV_AREAS = {
  LEFT_MENU: 'left_menu',
  SUBMENU: 'submenu',
  HOVER_MENU: 'hover_menu',
  TOP_BAR: 'top_bar'
};
var SCREEN_TYPES = exports.SCREEN_TYPES = {
  TAB: 'tab',
  POPUP: 'popup',
  APP_SCREEN: 'app_screen'
};
var WpDashboardTracking = exports["default"] = /*#__PURE__*/function () {
  function WpDashboardTracking() {
    (0, _classCallCheck2.default)(this, WpDashboardTracking);
  }
  return (0, _createClass2.default)(WpDashboardTracking, null, [{
    key: "init",
    value: function init() {
      if (this.initialized) {
        return;
      }
      this.restoreOrCreateSession();
      if (this.isEventsManagerAvailable()) {
        this.startSessionMonitoring();
        this.attachActivityListeners();
        this.attachNavigationListener();
        this.initialized = true;
      }
    }
  }, {
    key: "restoreOrCreateSession",
    value: function restoreOrCreateSession() {
      var storedSession = this.getStoredSession();
      if (storedSession) {
        this.sessionStartTime = storedSession.sessionStartTime;
        this.navItemsVisited = new Set(storedSession.navItemsVisited);
        this.lastActivityTime = Date.now();
        this.sessionEnded = false;
      } else {
        this.sessionStartTime = Date.now();
        this.lastActivityTime = Date.now();
        this.sessionEnded = false;
        this.navItemsVisited = new Set();
      }
      this.processPendingNavClick();
      this.saveSessionToStorage();
    }
  }, {
    key: "processPendingNavClick",
    value: function processPendingNavClick() {
      try {
        var pendingNav = sessionStorage.getItem(PENDING_NAV_CLICK_KEY);
        if (pendingNav) {
          var _JSON$parse = JSON.parse(pendingNav),
            itemId = _JSON$parse.itemId,
            rootItem = _JSON$parse.rootItem,
            area = _JSON$parse.area;
          this.navItemsVisited.add(itemId);
          var properties = {
            wpdash_nav_item_id: itemId,
            wpdash_nav_area: area
          };
          if (rootItem) {
            properties.wpdash_nav_item_root = rootItem;
          }
          this.dispatchEvent('wpdash_nav_clicked', properties, {
            send_immediately: true
          });
          sessionStorage.removeItem(PENDING_NAV_CLICK_KEY);
        }
      } catch (error) {
        sessionStorage.removeItem(PENDING_NAV_CLICK_KEY);
      }
    }
  }, {
    key: "getStoredSession",
    value: function getStoredSession() {
      try {
        var stored = sessionStorage.getItem(SESSION_STORAGE_KEY);
        return stored ? JSON.parse(stored) : null;
      } catch (error) {
        return null;
      }
    }
  }, {
    key: "saveSessionToStorage",
    value: function saveSessionToStorage() {
      var sessionData = {
        sessionStartTime: this.sessionStartTime,
        navItemsVisited: Array.from(this.navItemsVisited)
      };
      sessionStorage.setItem(SESSION_STORAGE_KEY, JSON.stringify(sessionData));
    }
  }, {
    key: "clearStoredSession",
    value: function clearStoredSession() {
      sessionStorage.removeItem(SESSION_STORAGE_KEY);
    }
  }, {
    key: "isEventsManagerAvailable",
    value: function isEventsManagerAvailable() {
      var _elementorCommon;
      return ((_elementorCommon = elementorCommon) === null || _elementorCommon === void 0 ? void 0 : _elementorCommon.eventsManager) && 'function' === typeof elementorCommon.eventsManager.dispatchEvent;
    }
  }, {
    key: "canSendEvents",
    value: function canSendEvents() {
      var _elementorCommon2;
      return ((_elementorCommon2 = elementorCommon) === null || _elementorCommon2 === void 0 || (_elementorCommon2 = _elementorCommon2.config) === null || _elementorCommon2 === void 0 || (_elementorCommon2 = _elementorCommon2.editor_events) === null || _elementorCommon2 === void 0 ? void 0 : _elementorCommon2.can_send_events) || false;
    }
  }, {
    key: "dispatchEvent",
    value: function dispatchEvent(eventName) {
      var properties = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      if (!this.isEventsManagerAvailable() || !this.canSendEvents()) {
        return;
      }
      elementorCommon.eventsManager.dispatchEvent(eventName, properties, options);
    }
  }, {
    key: "updateActivity",
    value: function updateActivity() {
      this.lastActivityTime = Date.now();
    }
  }, {
    key: "startSessionMonitoring",
    value: function startSessionMonitoring() {
      var _this = this;
      this.activityCheckInterval = setInterval(function () {
        _this.checkSessionTimeout();
      }, ACTIVITY_CHECK_INTERVAL);
      window.addEventListener('beforeunload', function () {
        if (!_this.sessionEnded && !_this.isNavigatingToElementor) {
          _this.trackSessionEnd('tab_closed');
        }
      });
      document.addEventListener('visibilitychange', function () {
        if (!_this.sessionEnded && document.hidden) {
          var timeSinceLastActivity = Date.now() - _this.lastActivityTime;
          if (timeSinceLastActivity > SESSION_TIMEOUT) {
            _this.trackSessionEnd('tab_inactive');
          }
        }
      });
    }
  }, {
    key: "isElementorPage",
    value: function isElementorPage(url) {
      try {
        var urlObj = new URL(url, window.location.origin);
        var params = urlObj.searchParams;
        var page = params.get('page');
        var postType = params.get('post_type');
        var action = params.get('action');
        var elementorPages = ['elementor', 'go_knowledge_base_site', 'e-form-submissions'];
        var elementorPostTypes = ['elementor_library', 'e-floating-buttons'];
        return page && elementorPages.some(function (p) {
          return page.includes(p);
        }) || postType && elementorPostTypes.includes(postType) || action && action.includes('elementor');
      } catch (error) {
        return false;
      }
    }
  }, {
    key: "isPluginsPage",
    value: function isPluginsPage(url) {
      try {
        var urlObj = new URL(url, window.location.origin);
        return urlObj.pathname.includes('plugins.php');
      } catch (error) {
        return false;
      }
    }
  }, {
    key: "isNavigatingAwayFromElementor",
    value: function isNavigatingAwayFromElementor(targetUrl) {
      if (!targetUrl) {
        return false;
      }
      if (targetUrl.startsWith('#')) {
        return false;
      }
      return !this.isElementorPage(targetUrl);
    }
  }, {
    key: "isLinkOpeningInNewTab",
    value: function isLinkOpeningInNewTab(link) {
      var target = link.getAttribute('target');
      return '_blank' === target || '_new' === target;
    }
  }, {
    key: "attachNavigationListener",
    value: function attachNavigationListener() {
      var _this2 = this;
      var handleLinkClick = function handleLinkClick(event) {
        var link = event.target.closest('a');
        if (link && link.href) {
          if (_this2.isLinkOpeningInNewTab(link)) {
            return;
          }
          if (!_this2.sessionEnded && _this2.isNavigatingAwayFromElementor(link.href)) {
            _this2.trackSessionEnd('navigate_away');
          } else if (_this2.isElementorPage(link.href)) {
            _this2.isNavigatingToElementor = true;
          }
        }
      };
      var handleFormSubmit = function handleFormSubmit(event) {
        var form = event.target;
        if (form.action) {
          if (!_this2.sessionEnded && _this2.isNavigatingAwayFromElementor(form.action)) {
            _this2.trackSessionEnd('navigate_away');
          } else if (_this2.isElementorPage(form.action)) {
            _this2.isNavigatingToElementor = true;
          }
        }
      };
      document.addEventListener('click', handleLinkClick, true);
      document.addEventListener('submit', handleFormSubmit, true);
      this.navigationListeners.push({
        type: 'click',
        handler: handleLinkClick
      }, {
        type: 'submit',
        handler: handleFormSubmit
      });
    }
  }, {
    key: "checkSessionTimeout",
    value: function checkSessionTimeout() {
      var timeSinceLastActivity = Date.now() - this.lastActivityTime;
      if (timeSinceLastActivity > SESSION_TIMEOUT && !this.sessionEnded) {
        this.trackSessionEnd('timeout');
      }
    }
  }, {
    key: "attachActivityListeners",
    value: function attachActivityListeners() {
      var _this3 = this;
      var events = ['mousedown', 'keydown', 'scroll', 'touchstart', 'click'];
      events.forEach(function (event) {
        document.addEventListener(event, function () {
          _this3.updateActivity();
        }, {
          capture: true,
          passive: true
        });
      });
    }
  }, {
    key: "formatDuration",
    value: function formatDuration(milliseconds) {
      var totalSeconds = Math.floor(milliseconds / 1000);
      return Number(totalSeconds.toFixed(2));
    }
  }, {
    key: "trackNavClicked",
    value: function trackNavClicked(itemId) {
      var rootItem = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      var area = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : NAV_AREAS.LEFT_MENU;
      if (!this.initialized) {
        var pendingNav = {
          itemId: itemId,
          rootItem: rootItem,
          area: area
        };
        sessionStorage.setItem(PENDING_NAV_CLICK_KEY, JSON.stringify(pendingNav));
        return;
      }
      this.updateActivity();
      this.navItemsVisited.add(itemId);
      this.saveSessionToStorage();
      var properties = {
        wpdash_nav_item_id: itemId,
        wpdash_nav_area: area
      };
      if (rootItem) {
        properties.wpdash_nav_item_root = rootItem;
      }
      this.dispatchEvent('wpdash_nav_clicked', properties);
    }
  }, {
    key: "trackScreenViewed",
    value: function trackScreenViewed(screenId) {
      var screenType = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : SCREEN_TYPES.TAB;
      this.updateActivity();
      var properties = {
        wpdash_screen_id: screenId,
        wpdash_screen_type: screenType
      };
      this.dispatchEvent('wpdash_screen_viewed', properties);
    }
  }, {
    key: "trackActionControl",
    value: function trackActionControl(controlIdentifier, controlType) {
      this.updateActivity();
      var properties = {
        wpdash_action_control_interacted: controlIdentifier,
        wpdash_control_type: controlType
      };
      this.dispatchEvent('wpdash_action_control', properties);
    }
  }, {
    key: "trackPromoClicked",
    value: function trackPromoClicked(promoName, destination, clickPath) {
      this.updateActivity();
      var properties = {
        wpdash_promo_name: promoName,
        wpdash_promo_destination: destination,
        wpdash_promo_clicked_path: clickPath
      };
      this.dispatchEvent('wpdash_promo_clicked', properties);
    }
  }, {
    key: "trackSessionEnd",
    value: function trackSessionEnd() {
      var reason = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'timeout';
      if (this.sessionEnded) {
        return;
      }
      this.sessionEnded = true;
      if (this.activityCheckInterval) {
        clearInterval(this.activityCheckInterval);
        this.activityCheckInterval = null;
      }
      var duration = Date.now() - this.sessionStartTime;
      var properties = {
        wpdash_endstate_nav_summary: Array.from(this.navItemsVisited),
        wpdash_endstate_nav_count: this.navItemsVisited.size,
        wpdash_endstate_duration: this.formatDuration(duration),
        reason: reason
      };
      this.dispatchEvent('wpdash_session_end_state', properties);
      this.clearStoredSession();
    }
  }, {
    key: "destroy",
    value: function destroy() {
      if (this.activityCheckInterval) {
        clearInterval(this.activityCheckInterval);
      }
      this.navigationListeners.forEach(function (_ref) {
        var type = _ref.type,
          handler = _ref.handler;
        document.removeEventListener(type, handler, true);
      });
      this.navigationListeners = [];
      _topBar.default.destroy();
      _screenView.default.destroy();
      _promotion.default.destroy();
      _menuPromotion.default.destroy();
      _actionControls.default.destroy();
      this.initialized = false;
    }
  }]);
}();
(0, _defineProperty2.default)(WpDashboardTracking, "sessionStartTime", Date.now());
(0, _defineProperty2.default)(WpDashboardTracking, "lastActivityTime", Date.now());
(0, _defineProperty2.default)(WpDashboardTracking, "sessionEnded", false);
(0, _defineProperty2.default)(WpDashboardTracking, "navItemsVisited", new Set());
(0, _defineProperty2.default)(WpDashboardTracking, "activityCheckInterval", null);
(0, _defineProperty2.default)(WpDashboardTracking, "initialized", false);
(0, _defineProperty2.default)(WpDashboardTracking, "navigationListeners", []);
(0, _defineProperty2.default)(WpDashboardTracking, "isNavigatingToElementor", false);
window.addEventListener('elementor/admin/init', function () {
  var currentUrl = window.location.href;
  var isPluginsPage = WpDashboardTracking.isPluginsPage(currentUrl);
  var isElementorPage = WpDashboardTracking.isElementorPage(currentUrl);
  if (isPluginsPage) {
    _pluginActions.default.init();
  }
  _navigation.default.init();
  if (isElementorPage) {
    WpDashboardTracking.init();
    _topBar.default.init();
    _screenView.default.init();
    _promotion.default.init();
    _menuPromotion.default.init();
    _actionControls.default.init();
  }
});
window.addEventListener('beforeunload', function () {
  _navigation.default.destroy();
  _pluginActions.default.destroy();
  WpDashboardTracking.destroy();
});

/***/ }),

/***/ "../assets/dev/js/utils/react.js":
/*!***************************************!*\
  !*** ../assets/dev/js/utils/react.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var React = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var ReactDOM = _interopRequireWildcard(__webpack_require__(/*! react-dom */ "react-dom"));
var _client = __webpack_require__(/*! react-dom/client */ "../node_modules/react-dom/client.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
/**
 * Support conditional rendering of a React App to the DOM, based on the React version.
 * We use `createRoot` when available, but fallback to `ReactDOM.render` for older versions.
 *
 * @param { React.ReactElement } app        The app to render.
 * @param { HTMLElement }        domElement The DOM element to render the app into.
 *
 * @return {{ unmount: () => void }} The unmount function.
 */
function render(app, domElement) {
  var unmountFunction;
  try {
    var root = (0, _client.createRoot)(domElement);
    root.render(app);
    unmountFunction = function unmountFunction() {
      root.unmount();
    };
  } catch (e) {
    // eslint-disable-next-line react/no-deprecated
    ReactDOM.render(app, domElement);
    unmountFunction = function unmountFunction() {
      // eslint-disable-next-line react/no-deprecated
      ReactDOM.unmountComponentAtNode(domElement);
    };
  }
  return {
    unmount: unmountFunction
  };
}
var _default = exports["default"] = {
  render: render
};

/***/ }),

/***/ "../modules/home/assets/js/components/addons-section.js":
/*!**************************************************************!*\
  !*** ../modules/home/assets/js/components/addons-section.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _objectDestructuringEmpty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectDestructuringEmpty */ "../node_modules/@babel/runtime/helpers/objectDestructuringEmpty.js"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _List = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/List */ "@elementor/ui/List"));
var _Link = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Link */ "@elementor/ui/Link"));
var _Button = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Button */ "@elementor/ui/Button"));
var _Card = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Card */ "@elementor/ui/Card"));
var _CardActions = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/CardActions */ "@elementor/ui/CardActions"));
var _CardContent = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/CardContent */ "@elementor/ui/CardContent"));
var _CardMedia = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/CardMedia */ "@elementor/ui/CardMedia"));
var _promoTracking = __webpack_require__(/*! ../utils/promo-tracking */ "../modules/home/assets/js/utils/promo-tracking.js");
var Addons = function Addons(_ref) {
  var props = (0, _extends2.default)({}, ((0, _objectDestructuringEmpty2.default)(_ref), _ref));
  var domain = props.adminUrl.replace('wp-admin/', '');
  var addonsArray = props.addonsData.repeater;
  var cardsPerRow = 3 === addonsArray.length ? 3 : 2;
  var handleAddonClick = function handleAddonClick(title, url) {
    (0, _promoTracking.trackPromoClick)(title, url, (0, _promoTracking.getHomeScreenPath)('addons'));
  };
  return /*#__PURE__*/_react.default.createElement(_ui.Paper, {
    elevation: 0,
    sx: {
      p: 3,
      display: 'flex',
      flexDirection: 'column',
      gap: 2
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Box, null, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "h6"
  }, props.addonsData.header.title), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2",
    color: "text.secondary"
  }, props.addonsData.header.description)), /*#__PURE__*/_react.default.createElement(_List.default, {
    sx: {
      display: 'grid',
      gridTemplateColumns: {
        md: "repeat(".concat(cardsPerRow, ", 1fr)"),
        xs: 'repeat(1, 1fr)'
      },
      gap: 2
    }
  }, addonsArray.map(function (item) {
    var linkTarget = item.hasOwnProperty('target') ? item.target : '_blank';
    return /*#__PURE__*/_react.default.createElement(_Card.default, {
      key: item.title,
      elevation: 0,
      sx: {
        display: 'flex',
        border: 1,
        borderRadius: 1,
        borderColor: 'action.focus'
      }
    }, /*#__PURE__*/_react.default.createElement(_CardContent.default, {
      sx: {
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'space-between',
        gap: 3,
        p: 3
      }
    }, /*#__PURE__*/_react.default.createElement(_ui.Box, null, /*#__PURE__*/_react.default.createElement(_CardMedia.default, {
      image: item.image,
      sx: {
        height: '58px',
        width: '58px',
        mb: 2
      }
    }), /*#__PURE__*/_react.default.createElement(_ui.Box, null, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
      variant: "subtitle2"
    }, item.title), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
      variant: "body2",
      color: "text.secondary"
    }, item.description))), /*#__PURE__*/_react.default.createElement(_CardActions.default, {
      sx: {
        p: 0
      }
    }, /*#__PURE__*/_react.default.createElement(_Button.default, {
      variant: "outlined",
      size: "small",
      color: "promotion",
      href: item.url,
      target: linkTarget,
      onClick: function onClick() {
        return handleAddonClick(item.title, item.url);
      }
    }, item.button_label))));
  })), /*#__PURE__*/_react.default.createElement(_Link.default, {
    variant: "body2",
    color: "info.main",
    underline: "none",
    href: "".concat(domain).concat(props.addonsData.footer.file_path)
  }, props.addonsData.footer.label));
};
var _default = exports["default"] = Addons;
Addons.propTypes = {
  addonsData: PropTypes.object.isRequired,
  adminUrl: PropTypes.string.isRequired
};

/***/ }),

/***/ "../modules/home/assets/js/components/create-new-page-dialog.js":
/*!**********************************************************************!*\
  !*** ../modules/home/assets/js/components/create-new-page-dialog.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _DialogHeader = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/DialogHeader */ "@elementor/ui/DialogHeader"));
var _DialogHeaderGroup = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/DialogHeaderGroup */ "@elementor/ui/DialogHeaderGroup"));
var _DialogTitle = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/DialogTitle */ "@elementor/ui/DialogTitle"));
var _DialogContent = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/DialogContent */ "@elementor/ui/DialogContent"));
var _DialogContentText = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/DialogContentText */ "@elementor/ui/DialogContentText"));
var _TextField = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/TextField */ "@elementor/ui/TextField"));
var _DialogActions = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/DialogActions */ "@elementor/ui/DialogActions"));
var _Button = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Button */ "@elementor/ui/Button"));
var _Dialog = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Dialog */ "@elementor/ui/Dialog"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var CreateNewPageDialog = function CreateNewPageDialog(_ref) {
  var url = _ref.url,
    isOpen = _ref.isOpen,
    closedDialogCallback = _ref.closedDialogCallback;
  var _React$useState = _react.default.useState(false),
    _React$useState2 = (0, _slicedToArray2.default)(_React$useState, 2),
    open = _React$useState2[0],
    setOpen = _React$useState2[1];
  var _React$useState3 = _react.default.useState(''),
    _React$useState4 = (0, _slicedToArray2.default)(_React$useState3, 2),
    pageName = _React$useState4[0],
    setPageName = _React$useState4[1];
  (0, _react.useEffect)(function () {
    setOpen(isOpen);
  }, [isOpen]);
  var handleDialogClose = function handleDialogClose() {
    setOpen(false);
    closedDialogCallback();
  };
  var handleChange = function handleChange(event) {
    var urlParams = new URLSearchParams();
    urlParams.append('post_data[post_title]', event.target.value);
    setPageName(urlParams.toString());
  };
  return /*#__PURE__*/_react.default.createElement(_Dialog.default, {
    open: open,
    onClose: handleDialogClose,
    maxWidth: "xs",
    width: "xs",
    fullWidth: true
  }, /*#__PURE__*/_react.default.createElement(_DialogHeader.default, null, /*#__PURE__*/_react.default.createElement(_DialogHeaderGroup.default, null, /*#__PURE__*/_react.default.createElement(_DialogTitle.default, null, __('Name your page', 'elementor')))), /*#__PURE__*/_react.default.createElement(_DialogContent.default, {
    dividers: true
  }, /*#__PURE__*/_react.default.createElement(_DialogContentText.default, {
    sx: {
      mb: 2
    }
  }, __('To proceed, please name your first page,', 'elementor'), /*#__PURE__*/_react.default.createElement("br", null), __('or rename it later.', 'elementor')), /*#__PURE__*/_react.default.createElement(_TextField.default, {
    onChange: handleChange,
    fullWidth: true,
    placeholder: __('New Page', 'elementor')
  })), /*#__PURE__*/_react.default.createElement(_DialogActions.default, null, /*#__PURE__*/_react.default.createElement(_Button.default, {
    onClick: handleDialogClose,
    color: "secondary"
  }, __('Cancel', 'elementor')), /*#__PURE__*/_react.default.createElement(_Button.default, {
    variant: "contained",
    href: pageName ? url + '&' + pageName : url,
    target: "_blank"
  }, __('Save', 'elementor'))));
};
var _default = exports["default"] = CreateNewPageDialog;
CreateNewPageDialog.propTypes = {
  url: PropTypes.string.isRequired,
  isOpen: PropTypes.bool.isRequired,
  closedDialogCallback: PropTypes.func.isRequired
};

/***/ }),

/***/ "../modules/home/assets/js/components/create-with-ai-banner.js":
/*!*********************************************************************!*\
  !*** ../modules/home/assets/js/components/create-with-ai-banner.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _objectDestructuringEmpty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectDestructuringEmpty */ "../node_modules/@babel/runtime/helpers/objectDestructuringEmpty.js"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _Typography = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Typography */ "@elementor/ui/Typography"));
var _Button = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Button */ "@elementor/ui/Button"));
var _promoTracking = __webpack_require__(/*! ../utils/promo-tracking */ "../modules/home/assets/js/utils/promo-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var CreateWithAIBanner = function CreateWithAIBanner(_ref) {
  var props = (0, _extends2.default)({}, ((0, _objectDestructuringEmpty2.default)(_ref), _ref));
  var createWithAIData = props.createWithAIData;
  var _useState = (0, _react.useState)(''),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    inputValue = _useState2[0],
    setInputValue = _useState2[1];
  if (!createWithAIData) {
    return null;
  }
  var title = createWithAIData.title,
    description = createWithAIData.description,
    inputPlaceholder = createWithAIData.input_placeholder,
    buttonTitle = createWithAIData.button_title,
    buttonCtaUrl = createWithAIData.button_cta_url,
    backgroundImage = createWithAIData.background_image,
    utmSource = createWithAIData.utm_source,
    utmMedium = createWithAIData.utm_medium,
    utmCampaign = createWithAIData.utm_campaign;
  var handleInputChange = function handleInputChange(event) {
    setInputValue(event.target.value);
  };
  var getButtonHref = function getButtonHref() {
    if (!inputValue) {
      return buttonCtaUrl;
    }
    var url = new URL(buttonCtaUrl);
    url.searchParams.append('prompt', inputValue);
    url.searchParams.append('utm_source', utmSource);
    url.searchParams.append('utm_medium', utmMedium);
    url.searchParams.append('utm_campaign', utmCampaign);
    return url.toString();
  };
  var handleNavigation = function handleNavigation() {
    if (!inputValue) {
      return;
    }
    var destination = getButtonHref();
    (0, _promoTracking.trackPromoClick)(title, destination, (0, _promoTracking.getHomeScreenPath)('ai_banner'));
    window.open(destination, '_blank');
    setInputValue('');
  };
  var handleKeyDown = function handleKeyDown(event) {
    if ('Enter' === event.key) {
      event.preventDefault();
      handleNavigation();
    }
  };
  return /*#__PURE__*/_react.default.createElement(_ui.Paper, {
    elevation: 0,
    sx: {
      display: 'flex',
      flexDirection: 'column',
      py: 3,
      px: 4,
      gap: 2,
      backgroundImage: "url(".concat(backgroundImage, ")"),
      backgroundSize: 'cover',
      backgroundPosition: 'right center',
      backgroundRepeat: 'no-repeat'
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    gap: 1,
    justifyContent: "center"
  }, /*#__PURE__*/_react.default.createElement(_Typography.default, {
    variant: "h6"
  }, title), /*#__PURE__*/_react.default.createElement(_Typography.default, {
    variant: "body2",
    color: "secondary"
  }, description)), /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      display: 'flex',
      flexDirection: {
        xs: 'column',
        sm: 'row'
      },
      gap: 2,
      mt: 1
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.TextField, {
    fullWidth: true,
    placeholder: inputPlaceholder,
    variant: "outlined",
    color: "secondary",
    size: "small",
    sx: {
      flex: 1
    },
    value: inputValue,
    onChange: handleInputChange,
    onKeyDown: handleKeyDown
  }), /*#__PURE__*/_react.default.createElement(_Button.default, {
    variant: "outlined",
    size: "small",
    color: "secondary",
    startIcon: /*#__PURE__*/_react.default.createElement("span", {
      className: "eicon-ai"
    }),
    onClick: handleNavigation
  }, buttonTitle)));
};
CreateWithAIBanner.propTypes = {
  createWithAIData: PropTypes.object
};
var _default = exports["default"] = CreateWithAIBanner;

/***/ }),

/***/ "../modules/home/assets/js/components/external-links-section.js":
/*!**********************************************************************!*\
  !*** ../modules/home/assets/js/components/external-links-section.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _objectDestructuringEmpty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectDestructuringEmpty */ "../node_modules/@babel/runtime/helpers/objectDestructuringEmpty.js"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _List = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/List */ "@elementor/ui/List"));
var _ListItemButton = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/ListItemButton */ "@elementor/ui/ListItemButton"));
var _ListItemText = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/ListItemText */ "@elementor/ui/ListItemText"));
var _Divider = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Divider */ "@elementor/ui/Divider"));
var ExternalLinksSection = function ExternalLinksSection(_ref) {
  var props = (0, _extends2.default)({}, ((0, _objectDestructuringEmpty2.default)(_ref), _ref));
  return /*#__PURE__*/_react.default.createElement(_ui.Paper, {
    elevation: 0,
    sx: {
      px: 3
    }
  }, /*#__PURE__*/_react.default.createElement(_List.default, null, props.externalLinksData.map(function (item, index) {
    return /*#__PURE__*/_react.default.createElement(_ui.Box, {
      key: item.label
    }, /*#__PURE__*/_react.default.createElement(_ListItemButton.default, {
      href: item.url,
      target: "_blank",
      sx: {
        '&:hover': {
          backgroundColor: 'initial'
        },
        gap: 2,
        px: 0,
        py: 2
      }
    }, /*#__PURE__*/_react.default.createElement(_ui.Box, {
      component: "img",
      src: item.image,
      sx: {
        width: '38px'
      }
    }), /*#__PURE__*/_react.default.createElement(_ListItemText.default, {
      sx: {
        color: 'text.secondary'
      },
      primary: item.label
    })), index < props.externalLinksData.length - 1 && /*#__PURE__*/_react.default.createElement(_Divider.default, null));
  })));
};
var _default = exports["default"] = ExternalLinksSection;
ExternalLinksSection.propTypes = {
  externalLinksData: PropTypes.array.isRequired
};

/***/ }),

/***/ "../modules/home/assets/js/components/get-started-list-item.js":
/*!*********************************************************************!*\
  !*** ../modules/home/assets/js/components/get-started-list-item.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _ListItem = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/ListItem */ "@elementor/ui/ListItem"));
var _ListItemText = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/ListItemText */ "@elementor/ui/ListItemText"));
var _Link = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Link */ "@elementor/ui/Link"));
var _Box = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Box */ "@elementor/ui/Box"));
var _createNewPageDialog = _interopRequireDefault(__webpack_require__(/*! ./create-new-page-dialog */ "../modules/home/assets/js/components/create-new-page-dialog.js"));
var GetStartedListItem = function GetStartedListItem(_ref) {
  var item = _ref.item,
    image = _ref.image,
    adminUrl = _ref.adminUrl;
  var url = item.is_relative_url ? adminUrl + item.url : item.url;
  var _React$useState = _react.default.useState(false),
    _React$useState2 = (0, _slicedToArray2.default)(_React$useState, 2),
    isOpen = _React$useState2[0],
    openDialog = _React$useState2[1];
  var handleLinkClick = function handleLinkClick(event) {
    if (!item.new_page) {
      return;
    }
    event.preventDefault();
    openDialog(true);
  };
  return /*#__PURE__*/_react.default.createElement(_ListItem.default, {
    alignItems: "flex-start",
    sx: {
      gap: 1,
      p: 0,
      maxWidth: '150px'
    }
  }, /*#__PURE__*/_react.default.createElement(_Box.default, {
    component: "img",
    src: image
  }), /*#__PURE__*/_react.default.createElement(_Box.default, null, /*#__PURE__*/_react.default.createElement(_ListItemText.default, {
    primary: item.title,
    primaryTypographyProps: {
      variant: 'subtitle1'
    },
    sx: {
      my: 0
    }
  }), /*#__PURE__*/_react.default.createElement(_Link.default, {
    variant: "body2",
    color: item.title_small_color ? item.title_small_color : 'text.tertiary',
    underline: "hover",
    href: url,
    target: "_blank",
    onClick: handleLinkClick
  }, item.title_small)), item.new_page && /*#__PURE__*/_react.default.createElement(_createNewPageDialog.default, {
    url: url,
    isOpen: isOpen,
    closedDialogCallback: function closedDialogCallback() {
      return openDialog(false);
    }
  }));
};
var _default = exports["default"] = GetStartedListItem;
GetStartedListItem.propTypes = {
  item: PropTypes.shape({
    title: PropTypes.string.isRequired,
    title_small: PropTypes.string.isRequired,
    url: PropTypes.string.isRequired,
    new_page: PropTypes.bool,
    is_relative_url: PropTypes.bool,
    title_small_color: PropTypes.string
  }).isRequired,
  adminUrl: PropTypes.string.isRequired,
  image: PropTypes.string
};

/***/ }),

/***/ "../modules/home/assets/js/components/get-started-section.js":
/*!*******************************************************************!*\
  !*** ../modules/home/assets/js/components/get-started-section.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _objectDestructuringEmpty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectDestructuringEmpty */ "../node_modules/@babel/runtime/helpers/objectDestructuringEmpty.js"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _List = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/List */ "@elementor/ui/List"));
var _getStartedListItem = _interopRequireDefault(__webpack_require__(/*! ./get-started-list-item */ "../modules/home/assets/js/components/get-started-list-item.js"));
var GetStarted = function GetStarted(_ref) {
  var props = (0, _extends2.default)({}, ((0, _objectDestructuringEmpty2.default)(_ref), _ref));
  return /*#__PURE__*/_react.default.createElement(_ui.Paper, {
    elevation: 0,
    sx: {
      p: 3,
      display: 'flex',
      flexDirection: 'column',
      gap: 2
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Box, null, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "h6"
  }, props.getStartedData.header.title), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2",
    color: "text.secondary"
  }, props.getStartedData.header.description)), /*#__PURE__*/_react.default.createElement(_List.default, {
    sx: {
      display: 'grid',
      gridTemplateColumns: {
        md: 'repeat(4, 1fr)',
        xs: 'repeat(2, 1fr)'
      },
      columnGap: {
        md: 9,
        xs: 7
      },
      rowGap: 3
    }
  }, props.getStartedData.repeater.map(function (item) {
    return /*#__PURE__*/_react.default.createElement(_getStartedListItem.default, {
      key: item.title,
      item: item,
      image: item.image,
      adminUrl: props.adminUrl
    });
  })));
};
var _default = exports["default"] = GetStarted;
GetStarted.propTypes = {
  getStartedData: PropTypes.object.isRequired,
  adminUrl: PropTypes.string.isRequired
};

/***/ }),

/***/ "../modules/home/assets/js/components/home-screen.js":
/*!***********************************************************!*\
  !*** ../modules/home/assets/js/components/home-screen.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _topSection = _interopRequireDefault(__webpack_require__(/*! ./top-section */ "../modules/home/assets/js/components/top-section.js"));
var _sidebarPromotion = _interopRequireDefault(__webpack_require__(/*! ./sidebar-promotion */ "../modules/home/assets/js/components/sidebar-promotion.js"));
var _addonsSection = _interopRequireDefault(__webpack_require__(/*! ./addons-section */ "../modules/home/assets/js/components/addons-section.js"));
var _externalLinksSection = _interopRequireDefault(__webpack_require__(/*! ./external-links-section */ "../modules/home/assets/js/components/external-links-section.js"));
var _getStartedSection = _interopRequireDefault(__webpack_require__(/*! ./get-started-section */ "../modules/home/assets/js/components/get-started-section.js"));
var _createWithAiBanner = _interopRequireDefault(__webpack_require__(/*! ./create-with-ai-banner */ "../modules/home/assets/js/components/create-with-ai-banner.js"));
var HomeScreen = function HomeScreen(props) {
  var hasSidebarPromotion = props.homeScreenData.hasOwnProperty('sidebar_promotion_variants');
  return /*#__PURE__*/ /*  Box wrapper around the Container is needed to neutralize wp-content area left-padding */_react.default.createElement(_ui.Box, {
    sx: {
      pr: 1
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Container, {
    disableGutters: true,
    maxWidth: "lg",
    sx: {
      display: 'flex',
      flexDirection: 'column',
      gap: {
        xs: 1,
        md: 3
      },
      pt: {
        xs: 2,
        md: 6
      },
      pb: 2
    }
  }, props.homeScreenData.top_with_licences && /*#__PURE__*/_react.default.createElement(_topSection.default, {
    topData: props.homeScreenData.top_with_licences,
    buttonCtaUrl: props.homeScreenData.button_cta_url
  }), /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      display: 'flex',
      flexDirection: {
        xs: 'column',
        sm: 'row'
      },
      justifyContent: 'space-between',
      gap: 3
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    sx: {
      flex: 1,
      gap: 3
    }
  }, props.homeScreenData.create_with_ai && /*#__PURE__*/_react.default.createElement(_createWithAiBanner.default, {
    createWithAIData: props.homeScreenData.create_with_ai
  }), /*#__PURE__*/_react.default.createElement(_getStartedSection.default, {
    getStartedData: props.homeScreenData.get_started,
    adminUrl: props.adminUrl
  }), /*#__PURE__*/_react.default.createElement(_addonsSection.default, {
    addonsData: props.homeScreenData.add_ons,
    adminUrl: props.adminUrl
  })), /*#__PURE__*/_react.default.createElement(_ui.Container, {
    maxWidth: "xs",
    disableGutters: true,
    sx: {
      width: {
        sm: '305px'
      },
      display: 'flex',
      flexDirection: 'column',
      gap: 3
    }
  }, hasSidebarPromotion && /*#__PURE__*/_react.default.createElement(_sidebarPromotion.default, {
    sideData: props.homeScreenData.sidebar_promotion_variants
  }), /*#__PURE__*/_react.default.createElement(_externalLinksSection.default, {
    externalLinksData: props.homeScreenData.external_links
  })))));
};
HomeScreen.propTypes = {
  homeScreenData: PropTypes.object,
  adminUrl: PropTypes.string
};
var _default = exports["default"] = HomeScreen;

/***/ }),

/***/ "../modules/home/assets/js/components/promotions/sidebar-banner.js":
/*!*************************************************************************!*\
  !*** ../modules/home/assets/js/components/promotions/sidebar-banner.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _Link = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Link */ "@elementor/ui/Link"));
var _promoTracking = __webpack_require__(/*! ../../utils/promo-tracking */ "../modules/home/assets/js/utils/promo-tracking.js");
var SidebarBanner = function SidebarBanner(_ref) {
  var image = _ref.image,
    link = _ref.link;
  var handleClick = function handleClick() {
    (0, _promoTracking.trackPromoClick)('Sidebar Banner', link, (0, _promoTracking.getHomeScreenPath)('sidebar'));
  };
  return /*#__PURE__*/_react.default.createElement(_ui.Paper, {
    elevation: 0,
    sx: {
      overflow: 'hidden'
    }
  }, /*#__PURE__*/_react.default.createElement(_Link.default, {
    target: "_blank",
    href: link,
    onClick: handleClick,
    sx: {
      lineHeight: 0,
      display: 'block',
      width: '100%',
      height: '100%',
      boxShadow: 'none',
      '&:focus': {
        boxShadow: 'none'
      },
      '&:active': {
        boxShadow: 'none'
      }
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Box, {
    component: 'img',
    src: image,
    sx: {
      width: '100%',
      height: '100%'
    }
  })));
};
var _default = exports["default"] = SidebarBanner;
SidebarBanner.propTypes = {
  image: PropTypes.string.isRequired,
  link: PropTypes.string.isRequired
};

/***/ }),

/***/ "../modules/home/assets/js/components/promotions/sidebar-default.js":
/*!**************************************************************************!*\
  !*** ../modules/home/assets/js/components/promotions/sidebar-default.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _Button = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Button */ "@elementor/ui/Button"));
var _List = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/List */ "@elementor/ui/List"));
var _ListItem = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/ListItem */ "@elementor/ui/ListItem"));
var _sideBarCheckIcon = _interopRequireDefault(__webpack_require__(/*! ../../icons/side-bar-check-icon */ "../modules/home/assets/js/icons/side-bar-check-icon.js"));
var _ListItemText = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/ListItemText */ "@elementor/ui/ListItemText"));
var _promoTracking = __webpack_require__(/*! ../../utils/promo-tracking */ "../modules/home/assets/js/utils/promo-tracking.js");
var SidebarDefault = function SidebarDefault(_ref) {
  var header = _ref.header,
    cta = _ref.cta,
    repeater = _ref.repeater;
  var handleCtaClick = function handleCtaClick() {
    (0, _promoTracking.trackPromoClick)(header.title, cta.url, (0, _promoTracking.getHomeScreenPath)('sidebar'));
  };
  return /*#__PURE__*/_react.default.createElement(_ui.Paper, {
    elevation: 0,
    sx: {
      p: 3
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    gap: 1.5,
    alignItems: "center",
    textAlign: "center",
    sx: {
      pb: 4
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Box, {
    component: "img",
    src: header.image
  }), /*#__PURE__*/_react.default.createElement(_ui.Box, null, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "h6"
  }, header.title), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2",
    color: "text.secondary"
  }, header.description)), /*#__PURE__*/_react.default.createElement(_Button.default, {
    variant: "contained",
    size: "medium",
    color: "promotion",
    href: cta.url,
    onClick: handleCtaClick,
    startIcon: /*#__PURE__*/_react.default.createElement(_ui.Box, {
      component: "img",
      src: cta.image,
      sx: {
        width: '16px'
      }
    }),
    target: "_blank",
    sx: {
      maxWidth: 'fit-content'
    }
  }, cta.label)), /*#__PURE__*/_react.default.createElement(_List.default, {
    sx: {
      p: 0
    }
  }, repeater.map(function (item, index) {
    return /*#__PURE__*/_react.default.createElement(_ListItem.default, {
      key: index,
      sx: {
        p: 0,
        gap: 1
      }
    }, /*#__PURE__*/_react.default.createElement(_sideBarCheckIcon.default, null), /*#__PURE__*/_react.default.createElement(_ListItemText.default, {
      primaryTypographyProps: {
        variant: 'body2'
      },
      primary: item.title
    }));
  })));
};
var _default = exports["default"] = SidebarDefault;
SidebarDefault.propTypes = {
  header: PropTypes.object.isRequired,
  cta: PropTypes.object.isRequired,
  repeater: PropTypes.array
};

/***/ }),

/***/ "../modules/home/assets/js/components/sidebar-promotion.js":
/*!*****************************************************************!*\
  !*** ../modules/home/assets/js/components/sidebar-promotion.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _sidebarBanner = _interopRequireDefault(__webpack_require__(/*! ./promotions/sidebar-banner */ "../modules/home/assets/js/components/promotions/sidebar-banner.js"));
var _sidebarDefault = _interopRequireDefault(__webpack_require__(/*! ./promotions/sidebar-default */ "../modules/home/assets/js/components/promotions/sidebar-default.js"));
var SideBarPromotion = function SideBarPromotion(_ref) {
  var sideData = _ref.sideData;
  if ('banner' === sideData.type) {
    return /*#__PURE__*/_react.default.createElement(_sidebarBanner.default, sideData.data);
  }
  return /*#__PURE__*/_react.default.createElement(_sidebarDefault.default, sideData.data);
};
var _default = exports["default"] = SideBarPromotion;
SideBarPromotion.propTypes = {
  sideData: PropTypes.object.isRequired
};

/***/ }),

/***/ "../modules/home/assets/js/components/top-section.js":
/*!***********************************************************!*\
  !*** ../modules/home/assets/js/components/top-section.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _objectDestructuringEmpty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectDestructuringEmpty */ "../node_modules/@babel/runtime/helpers/objectDestructuringEmpty.js"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _Typography = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Typography */ "@elementor/ui/Typography"));
var _Button = _interopRequireDefault(__webpack_require__(/*! @elementor/ui/Button */ "@elementor/ui/Button"));
var _youtubeIcon = _interopRequireDefault(__webpack_require__(/*! ../icons/youtube-icon */ "../modules/home/assets/js/icons/youtube-icon.js"));
var _promoTracking = __webpack_require__(/*! ../utils/promo-tracking */ "../modules/home/assets/js/utils/promo-tracking.js");
var TopSection = function TopSection(_ref) {
  var props = (0, _extends2.default)({}, ((0, _objectDestructuringEmpty2.default)(_ref), _ref));
  var topData = props.topData,
    buttonCtaUrl = props.buttonCtaUrl;
  if (!topData) {
    return null;
  }
  var title = topData.title,
    description = topData.description,
    buttonCtaTitle = topData.button_cta_text,
    buttonCreatePageTitle = topData.button_create_page_title,
    youtubeEmbeddedId = topData.youtube_embed_id,
    buttonWatchURL = topData.button_watch_url,
    buttonWatchTitle = topData.button_watch_title;
  var ctaButtonTitle = buttonCtaTitle !== null && buttonCtaTitle !== void 0 ? buttonCtaTitle : buttonCreatePageTitle;
  var handleCtaClick = function handleCtaClick() {
    (0, _promoTracking.trackPromoClick)(ctaButtonTitle, buttonCtaUrl, (0, _promoTracking.getHomeScreenPath)('top_section'));
  };
  var handleWatchClick = function handleWatchClick() {
    (0, _promoTracking.trackPromoClick)(buttonWatchTitle, buttonWatchURL, (0, _promoTracking.getHomeScreenPath)('top_section'));
  };
  return /*#__PURE__*/_react.default.createElement(_ui.Paper, {
    elevation: 0,
    sx: {
      display: 'flex',
      flexDirection: {
        xs: 'column',
        sm: 'row'
      },
      justifyContent: 'space-between',
      py: {
        xs: 3,
        md: 3
      },
      px: {
        xs: 3,
        md: 4
      },
      gap: {
        xs: 2,
        sm: 3,
        lg: 22
      }
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    gap: 3,
    justifyContent: "center"
  }, /*#__PURE__*/_react.default.createElement(_ui.Box, null, /*#__PURE__*/_react.default.createElement(_Typography.default, {
    variant: "h6"
  }, title), /*#__PURE__*/_react.default.createElement(_Typography.default, {
    variant: "body2",
    color: "secondary"
  }, description)), /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      display: 'flex',
      gap: 1
    }
  }, /*#__PURE__*/_react.default.createElement(_Button.default, {
    "data-testid": "e-create-button",
    variant: "contained",
    size: "small",
    href: buttonCtaUrl,
    target: "_blank",
    onClick: handleCtaClick
  }, ctaButtonTitle), /*#__PURE__*/_react.default.createElement(_Button.default, {
    variant: "outlined",
    color: "secondary",
    size: "small",
    startIcon: /*#__PURE__*/_react.default.createElement(_youtubeIcon.default, null),
    href: buttonWatchURL,
    target: "_blank",
    onClick: handleWatchClick
  }, buttonWatchTitle))), /*#__PURE__*/_react.default.createElement(_ui.Box, {
    component: "iframe",
    src: "https://www.youtube.com/embed/".concat(youtubeEmbeddedId),
    title: "YouTube video player",
    frameBorder: "0",
    allow: "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share",
    allowFullScreen: true,
    sx: {
      aspectRatio: '16/9',
      borderRadius: 1,
      display: 'flex',
      width: '100%',
      maxWidth: '365px'
    }
  }));
};
TopSection.propTypes = {
  topData: PropTypes.object.isRequired,
  buttonCtaUrl: PropTypes.string.isRequired
};
var _default = exports["default"] = TopSection;

/***/ }),

/***/ "../modules/home/assets/js/icons/side-bar-check-icon.js":
/*!**************************************************************!*\
  !*** ../modules/home/assets/js/icons/side-bar-check-icon.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var React = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var SideBarCheckIcon = function SideBarCheckIcon(props) {
  return /*#__PURE__*/React.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 24 24"
  }, props), /*#__PURE__*/React.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M9.09013 3.69078C10.273 3.2008 11.5409 2.94861 12.8213 2.94861C14.1017 2.94861 15.3695 3.2008 16.5525 3.69078C17.7354 4.18077 18.8102 4.89895 19.7156 5.80432C20.621 6.70969 21.3391 7.78452 21.8291 8.96744C22.3191 10.1504 22.5713 11.4182 22.5713 12.6986C22.5713 13.979 22.3191 15.2468 21.8291 16.4298C21.3391 17.6127 20.621 18.6875 19.7156 19.5929C18.8102 20.4983 17.7354 21.2165 16.5525 21.7064C15.3695 22.1964 14.1017 22.4486 12.8213 22.4486C11.5409 22.4486 10.2731 22.1964 9.09013 21.7064C7.9072 21.2165 6.83237 20.4983 5.927 19.5929C5.02163 18.6875 4.30345 17.6127 3.81346 16.4298C3.32348 15.2468 3.07129 13.979 3.07129 12.6986C3.07129 11.4182 3.32348 10.1504 3.81346 8.96744C4.30345 7.78452 5.02163 6.70969 5.927 5.80432C6.83237 4.89895 7.9072 4.18077 9.09013 3.69078ZM12.8213 4.44861C11.7379 4.44861 10.6651 4.662 9.66415 5.0766C8.66321 5.4912 7.75374 6.09889 6.98766 6.86498C6.22157 7.63106 5.61388 8.54053 5.19928 9.54147C4.78468 10.5424 4.57129 11.6152 4.57129 12.6986C4.57129 13.782 4.78468 14.8548 5.19928 15.8557C5.61388 16.8567 6.22157 17.7662 6.98766 18.5322C7.75374 19.2983 8.66322 19.906 9.66415 20.3206C10.6651 20.7352 11.7379 20.9486 12.8213 20.9486C13.9047 20.9486 14.9775 20.7352 15.9784 20.3206C16.9794 19.906 17.8888 19.2983 18.6549 18.5322C19.421 17.7662 20.0287 16.8567 20.4433 15.8557C20.8579 14.8548 21.0713 13.782 21.0713 12.6986C21.0713 11.6152 20.8579 10.5424 20.4433 9.54147C20.0287 8.54053 19.421 7.63106 18.6549 6.86498C17.8888 6.09889 16.9794 5.4912 15.9784 5.0766C14.9775 4.662 13.9047 4.44861 12.8213 4.44861Z",
    fill: "#93003F"
  }), /*#__PURE__*/React.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M17.3213 9.69424C17.6142 9.98713 17.6142 10.462 17.3213 10.7549L12.3732 15.703C12.0803 15.9959 11.6054 15.9959 11.3125 15.703L8.83851 13.2289C8.54562 12.936 8.54562 12.4612 8.83851 12.1683C9.1314 11.8754 9.60628 11.8754 9.89917 12.1683L11.8429 14.112L16.2606 9.69424C16.5535 9.40135 17.0284 9.40135 17.3213 9.69424Z",
    fill: "#93003F"
  }));
};
var _default = exports["default"] = SideBarCheckIcon;

/***/ }),

/***/ "../modules/home/assets/js/icons/youtube-icon.js":
/*!*******************************************************!*\
  !*** ../modules/home/assets/js/icons/youtube-icon.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var React = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var YoutubeIcon = function YoutubeIcon(props) {
  return /*#__PURE__*/React.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 24 24"
  }, props), /*#__PURE__*/React.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M7 5.75C5.20507 5.75 3.75 7.20507 3.75 9V15C3.75 16.7949 5.20507 18.25 7 18.25H17C18.7949 18.25 20.25 16.7949 20.25 15V9C20.25 7.20507 18.7949 5.75 17 5.75H7ZM2.25 9C2.25 6.37665 4.37665 4.25 7 4.25H17C19.6234 4.25 21.75 6.37665 21.75 9V15C21.75 17.6234 19.6234 19.75 17 19.75H7C4.37665 19.75 2.25 17.6234 2.25 15V9ZM9.63048 8.34735C9.86561 8.21422 10.1542 8.21786 10.3859 8.35688L15.3859 11.3569C15.6118 11.4924 15.75 11.7366 15.75 12C15.75 12.2634 15.6118 12.5076 15.3859 12.6431L10.3859 15.6431C10.1542 15.7821 9.86561 15.7858 9.63048 15.6526C9.39534 15.5195 9.25 15.2702 9.25 15V9C9.25 8.7298 9.39534 8.48048 9.63048 8.34735ZM10.75 10.3246V13.6754L13.5423 12L10.75 10.3246Z"
  }));
};
var _default = exports["default"] = YoutubeIcon;

/***/ }),

/***/ "../modules/home/assets/js/utils/promo-tracking.js":
/*!*********************************************************!*\
  !*** ../modules/home/assets/js/utils/promo-tracking.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.trackPromoClick = exports.getHomeScreenPath = void 0;
var _wpDashboardTracking = _interopRequireDefault(__webpack_require__(/*! ../../../../../app/assets/js/event-track/wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var trackPromoClick = exports.trackPromoClick = function trackPromoClick(promoName, destination, path) {
  if (_wpDashboardTracking.default && 'function' === typeof _wpDashboardTracking.default.trackPromoClicked) {
    _wpDashboardTracking.default.trackPromoClicked(promoName, destination, path);
  }
};
var getHomeScreenPath = exports.getHomeScreenPath = function getHomeScreenPath(section) {
  return ['home', section];
};

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

/***/ "../node_modules/@babel/runtime/helpers/extends.js":
/*!*********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/extends.js ***!
  \*********************************************************/
/***/ ((module) => {

function _extends() {
  return module.exports = _extends = Object.assign ? Object.assign.bind() : function (n) {
    for (var e = 1; e < arguments.length; e++) {
      var t = arguments[e];
      for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]);
    }
    return n;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _extends.apply(null, arguments);
}
module.exports = _extends, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/objectDestructuringEmpty.js":
/*!**************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/objectDestructuringEmpty.js ***!
  \**************************************************************************/
/***/ ((module) => {

function _objectDestructuringEmpty(t) {
  if (null == t) throw new TypeError("Cannot destructure " + t);
}
module.exports = _objectDestructuringEmpty, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/object-assign/index.js":
/*!**********************************************!*\
  !*** ../node_modules/object-assign/index.js ***!
  \**********************************************/
/***/ ((module) => {

"use strict";
/*
object-assign
(c) Sindre Sorhus
@license MIT
*/


/* eslint-disable no-unused-vars */
var getOwnPropertySymbols = Object.getOwnPropertySymbols;
var hasOwnProperty = Object.prototype.hasOwnProperty;
var propIsEnumerable = Object.prototype.propertyIsEnumerable;

function toObject(val) {
	if (val === null || val === undefined) {
		throw new TypeError('Object.assign cannot be called with null or undefined');
	}

	return Object(val);
}

function shouldUseNative() {
	try {
		if (!Object.assign) {
			return false;
		}

		// Detect buggy property enumeration order in older V8 versions.

		// https://bugs.chromium.org/p/v8/issues/detail?id=4118
		var test1 = new String('abc');  // eslint-disable-line no-new-wrappers
		test1[5] = 'de';
		if (Object.getOwnPropertyNames(test1)[0] === '5') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test2 = {};
		for (var i = 0; i < 10; i++) {
			test2['_' + String.fromCharCode(i)] = i;
		}
		var order2 = Object.getOwnPropertyNames(test2).map(function (n) {
			return test2[n];
		});
		if (order2.join('') !== '0123456789') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test3 = {};
		'abcdefghijklmnopqrst'.split('').forEach(function (letter) {
			test3[letter] = letter;
		});
		if (Object.keys(Object.assign({}, test3)).join('') !==
				'abcdefghijklmnopqrst') {
			return false;
		}

		return true;
	} catch (err) {
		// We don't expect any of the above to throw, but better to be safe.
		return false;
	}
}

module.exports = shouldUseNative() ? Object.assign : function (target, source) {
	var from;
	var to = toObject(target);
	var symbols;

	for (var s = 1; s < arguments.length; s++) {
		from = Object(arguments[s]);

		for (var key in from) {
			if (hasOwnProperty.call(from, key)) {
				to[key] = from[key];
			}
		}

		if (getOwnPropertySymbols) {
			symbols = getOwnPropertySymbols(from);
			for (var i = 0; i < symbols.length; i++) {
				if (propIsEnumerable.call(from, symbols[i])) {
					to[symbols[i]] = from[symbols[i]];
				}
			}
		}
	}

	return to;
};


/***/ }),

/***/ "../node_modules/prop-types/checkPropTypes.js":
/*!****************************************************!*\
  !*** ../node_modules/prop-types/checkPropTypes.js ***!
  \****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var printWarning = function() {};

if (true) {
  var ReactPropTypesSecret = __webpack_require__(/*! ./lib/ReactPropTypesSecret */ "../node_modules/prop-types/lib/ReactPropTypesSecret.js");
  var loggedTypeFailures = {};
  var has = __webpack_require__(/*! ./lib/has */ "../node_modules/prop-types/lib/has.js");

  printWarning = function(text) {
    var message = 'Warning: ' + text;
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) { /**/ }
  };
}

/**
 * Assert that the values match with the type specs.
 * Error messages are memorized and will only be shown once.
 *
 * @param {object} typeSpecs Map of name to a ReactPropType
 * @param {object} values Runtime values that need to be type-checked
 * @param {string} location e.g. "prop", "context", "child context"
 * @param {string} componentName Name of the component for error messages.
 * @param {?Function} getStack Returns the component stack.
 * @private
 */
function checkPropTypes(typeSpecs, values, location, componentName, getStack) {
  if (true) {
    for (var typeSpecName in typeSpecs) {
      if (has(typeSpecs, typeSpecName)) {
        var error;
        // Prop type validation may throw. In case they do, we don't want to
        // fail the render phase where it didn't fail before. So we log it.
        // After these have been cleaned up, we'll let them throw.
        try {
          // This is intentionally an invariant that gets caught. It's the same
          // behavior as without this statement except with a better message.
          if (typeof typeSpecs[typeSpecName] !== 'function') {
            var err = Error(
              (componentName || 'React class') + ': ' + location + ' type `' + typeSpecName + '` is invalid; ' +
              'it must be a function, usually from the `prop-types` package, but received `' + typeof typeSpecs[typeSpecName] + '`.' +
              'This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.'
            );
            err.name = 'Invariant Violation';
            throw err;
          }
          error = typeSpecs[typeSpecName](values, typeSpecName, componentName, location, null, ReactPropTypesSecret);
        } catch (ex) {
          error = ex;
        }
        if (error && !(error instanceof Error)) {
          printWarning(
            (componentName || 'React class') + ': type specification of ' +
            location + ' `' + typeSpecName + '` is invalid; the type checker ' +
            'function must return `null` or an `Error` but returned a ' + typeof error + '. ' +
            'You may have forgotten to pass an argument to the type checker ' +
            'creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and ' +
            'shape all require an argument).'
          );
        }
        if (error instanceof Error && !(error.message in loggedTypeFailures)) {
          // Only monitor this failure once because there tends to be a lot of the
          // same error.
          loggedTypeFailures[error.message] = true;

          var stack = getStack ? getStack() : '';

          printWarning(
            'Failed ' + location + ' type: ' + error.message + (stack != null ? stack : '')
          );
        }
      }
    }
  }
}

/**
 * Resets warning cache when testing.
 *
 * @private
 */
checkPropTypes.resetWarningCache = function() {
  if (true) {
    loggedTypeFailures = {};
  }
}

module.exports = checkPropTypes;


/***/ }),

/***/ "../node_modules/prop-types/factoryWithTypeCheckers.js":
/*!*************************************************************!*\
  !*** ../node_modules/prop-types/factoryWithTypeCheckers.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactIs = __webpack_require__(/*! react-is */ "../node_modules/prop-types/node_modules/react-is/index.js");
var assign = __webpack_require__(/*! object-assign */ "../node_modules/object-assign/index.js");

var ReactPropTypesSecret = __webpack_require__(/*! ./lib/ReactPropTypesSecret */ "../node_modules/prop-types/lib/ReactPropTypesSecret.js");
var has = __webpack_require__(/*! ./lib/has */ "../node_modules/prop-types/lib/has.js");
var checkPropTypes = __webpack_require__(/*! ./checkPropTypes */ "../node_modules/prop-types/checkPropTypes.js");

var printWarning = function() {};

if (true) {
  printWarning = function(text) {
    var message = 'Warning: ' + text;
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) {}
  };
}

function emptyFunctionThatReturnsNull() {
  return null;
}

module.exports = function(isValidElement, throwOnDirectAccess) {
  /* global Symbol */
  var ITERATOR_SYMBOL = typeof Symbol === 'function' && Symbol.iterator;
  var FAUX_ITERATOR_SYMBOL = '@@iterator'; // Before Symbol spec.

  /**
   * Returns the iterator method function contained on the iterable object.
   *
   * Be sure to invoke the function with the iterable as context:
   *
   *     var iteratorFn = getIteratorFn(myIterable);
   *     if (iteratorFn) {
   *       var iterator = iteratorFn.call(myIterable);
   *       ...
   *     }
   *
   * @param {?object} maybeIterable
   * @return {?function}
   */
  function getIteratorFn(maybeIterable) {
    var iteratorFn = maybeIterable && (ITERATOR_SYMBOL && maybeIterable[ITERATOR_SYMBOL] || maybeIterable[FAUX_ITERATOR_SYMBOL]);
    if (typeof iteratorFn === 'function') {
      return iteratorFn;
    }
  }

  /**
   * Collection of methods that allow declaration and validation of props that are
   * supplied to React components. Example usage:
   *
   *   var Props = require('ReactPropTypes');
   *   var MyArticle = React.createClass({
   *     propTypes: {
   *       // An optional string prop named "description".
   *       description: Props.string,
   *
   *       // A required enum prop named "category".
   *       category: Props.oneOf(['News','Photos']).isRequired,
   *
   *       // A prop named "dialog" that requires an instance of Dialog.
   *       dialog: Props.instanceOf(Dialog).isRequired
   *     },
   *     render: function() { ... }
   *   });
   *
   * A more formal specification of how these methods are used:
   *
   *   type := array|bool|func|object|number|string|oneOf([...])|instanceOf(...)
   *   decl := ReactPropTypes.{type}(.isRequired)?
   *
   * Each and every declaration produces a function with the same signature. This
   * allows the creation of custom validation functions. For example:
   *
   *  var MyLink = React.createClass({
   *    propTypes: {
   *      // An optional string or URI prop named "href".
   *      href: function(props, propName, componentName) {
   *        var propValue = props[propName];
   *        if (propValue != null && typeof propValue !== 'string' &&
   *            !(propValue instanceof URI)) {
   *          return new Error(
   *            'Expected a string or an URI for ' + propName + ' in ' +
   *            componentName
   *          );
   *        }
   *      }
   *    },
   *    render: function() {...}
   *  });
   *
   * @internal
   */

  var ANONYMOUS = '<<anonymous>>';

  // Important!
  // Keep this list in sync with production version in `./factoryWithThrowingShims.js`.
  var ReactPropTypes = {
    array: createPrimitiveTypeChecker('array'),
    bigint: createPrimitiveTypeChecker('bigint'),
    bool: createPrimitiveTypeChecker('boolean'),
    func: createPrimitiveTypeChecker('function'),
    number: createPrimitiveTypeChecker('number'),
    object: createPrimitiveTypeChecker('object'),
    string: createPrimitiveTypeChecker('string'),
    symbol: createPrimitiveTypeChecker('symbol'),

    any: createAnyTypeChecker(),
    arrayOf: createArrayOfTypeChecker,
    element: createElementTypeChecker(),
    elementType: createElementTypeTypeChecker(),
    instanceOf: createInstanceTypeChecker,
    node: createNodeChecker(),
    objectOf: createObjectOfTypeChecker,
    oneOf: createEnumTypeChecker,
    oneOfType: createUnionTypeChecker,
    shape: createShapeTypeChecker,
    exact: createStrictShapeTypeChecker,
  };

  /**
   * inlined Object.is polyfill to avoid requiring consumers ship their own
   * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/is
   */
  /*eslint-disable no-self-compare*/
  function is(x, y) {
    // SameValue algorithm
    if (x === y) {
      // Steps 1-5, 7-10
      // Steps 6.b-6.e: +0 != -0
      return x !== 0 || 1 / x === 1 / y;
    } else {
      // Step 6.a: NaN == NaN
      return x !== x && y !== y;
    }
  }
  /*eslint-enable no-self-compare*/

  /**
   * We use an Error-like object for backward compatibility as people may call
   * PropTypes directly and inspect their output. However, we don't use real
   * Errors anymore. We don't inspect their stack anyway, and creating them
   * is prohibitively expensive if they are created too often, such as what
   * happens in oneOfType() for any type before the one that matched.
   */
  function PropTypeError(message, data) {
    this.message = message;
    this.data = data && typeof data === 'object' ? data: {};
    this.stack = '';
  }
  // Make `instanceof Error` still work for returned errors.
  PropTypeError.prototype = Error.prototype;

  function createChainableTypeChecker(validate) {
    if (true) {
      var manualPropTypeCallCache = {};
      var manualPropTypeWarningCount = 0;
    }
    function checkType(isRequired, props, propName, componentName, location, propFullName, secret) {
      componentName = componentName || ANONYMOUS;
      propFullName = propFullName || propName;

      if (secret !== ReactPropTypesSecret) {
        if (throwOnDirectAccess) {
          // New behavior only for users of `prop-types` package
          var err = new Error(
            'Calling PropTypes validators directly is not supported by the `prop-types` package. ' +
            'Use `PropTypes.checkPropTypes()` to call them. ' +
            'Read more at http://fb.me/use-check-prop-types'
          );
          err.name = 'Invariant Violation';
          throw err;
        } else if ( true && typeof console !== 'undefined') {
          // Old behavior for people using React.PropTypes
          var cacheKey = componentName + ':' + propName;
          if (
            !manualPropTypeCallCache[cacheKey] &&
            // Avoid spamming the console because they are often not actionable except for lib authors
            manualPropTypeWarningCount < 3
          ) {
            printWarning(
              'You are manually calling a React.PropTypes validation ' +
              'function for the `' + propFullName + '` prop on `' + componentName + '`. This is deprecated ' +
              'and will throw in the standalone `prop-types` package. ' +
              'You may be seeing this warning due to a third-party PropTypes ' +
              'library. See https://fb.me/react-warning-dont-call-proptypes ' + 'for details.'
            );
            manualPropTypeCallCache[cacheKey] = true;
            manualPropTypeWarningCount++;
          }
        }
      }
      if (props[propName] == null) {
        if (isRequired) {
          if (props[propName] === null) {
            return new PropTypeError('The ' + location + ' `' + propFullName + '` is marked as required ' + ('in `' + componentName + '`, but its value is `null`.'));
          }
          return new PropTypeError('The ' + location + ' `' + propFullName + '` is marked as required in ' + ('`' + componentName + '`, but its value is `undefined`.'));
        }
        return null;
      } else {
        return validate(props, propName, componentName, location, propFullName);
      }
    }

    var chainedCheckType = checkType.bind(null, false);
    chainedCheckType.isRequired = checkType.bind(null, true);

    return chainedCheckType;
  }

  function createPrimitiveTypeChecker(expectedType) {
    function validate(props, propName, componentName, location, propFullName, secret) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== expectedType) {
        // `propValue` being instance of, say, date/regexp, pass the 'object'
        // check, but we can offer a more precise error message here rather than
        // 'of type `object`'.
        var preciseType = getPreciseType(propValue);

        return new PropTypeError(
          'Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + preciseType + '` supplied to `' + componentName + '`, expected ') + ('`' + expectedType + '`.'),
          {expectedType: expectedType}
        );
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createAnyTypeChecker() {
    return createChainableTypeChecker(emptyFunctionThatReturnsNull);
  }

  function createArrayOfTypeChecker(typeChecker) {
    function validate(props, propName, componentName, location, propFullName) {
      if (typeof typeChecker !== 'function') {
        return new PropTypeError('Property `' + propFullName + '` of component `' + componentName + '` has invalid PropType notation inside arrayOf.');
      }
      var propValue = props[propName];
      if (!Array.isArray(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected an array.'));
      }
      for (var i = 0; i < propValue.length; i++) {
        var error = typeChecker(propValue, i, componentName, location, propFullName + '[' + i + ']', ReactPropTypesSecret);
        if (error instanceof Error) {
          return error;
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createElementTypeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      if (!isValidElement(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected a single ReactElement.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createElementTypeTypeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      if (!ReactIs.isValidElementType(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected a single ReactElement type.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createInstanceTypeChecker(expectedClass) {
    function validate(props, propName, componentName, location, propFullName) {
      if (!(props[propName] instanceof expectedClass)) {
        var expectedClassName = expectedClass.name || ANONYMOUS;
        var actualClassName = getClassName(props[propName]);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + actualClassName + '` supplied to `' + componentName + '`, expected ') + ('instance of `' + expectedClassName + '`.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createEnumTypeChecker(expectedValues) {
    if (!Array.isArray(expectedValues)) {
      if (true) {
        if (arguments.length > 1) {
          printWarning(
            'Invalid arguments supplied to oneOf, expected an array, got ' + arguments.length + ' arguments. ' +
            'A common mistake is to write oneOf(x, y, z) instead of oneOf([x, y, z]).'
          );
        } else {
          printWarning('Invalid argument supplied to oneOf, expected an array.');
        }
      }
      return emptyFunctionThatReturnsNull;
    }

    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      for (var i = 0; i < expectedValues.length; i++) {
        if (is(propValue, expectedValues[i])) {
          return null;
        }
      }

      var valuesString = JSON.stringify(expectedValues, function replacer(key, value) {
        var type = getPreciseType(value);
        if (type === 'symbol') {
          return String(value);
        }
        return value;
      });
      return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of value `' + String(propValue) + '` ' + ('supplied to `' + componentName + '`, expected one of ' + valuesString + '.'));
    }
    return createChainableTypeChecker(validate);
  }

  function createObjectOfTypeChecker(typeChecker) {
    function validate(props, propName, componentName, location, propFullName) {
      if (typeof typeChecker !== 'function') {
        return new PropTypeError('Property `' + propFullName + '` of component `' + componentName + '` has invalid PropType notation inside objectOf.');
      }
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected an object.'));
      }
      for (var key in propValue) {
        if (has(propValue, key)) {
          var error = typeChecker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
          if (error instanceof Error) {
            return error;
          }
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createUnionTypeChecker(arrayOfTypeCheckers) {
    if (!Array.isArray(arrayOfTypeCheckers)) {
       true ? printWarning('Invalid argument supplied to oneOfType, expected an instance of array.') : 0;
      return emptyFunctionThatReturnsNull;
    }

    for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
      var checker = arrayOfTypeCheckers[i];
      if (typeof checker !== 'function') {
        printWarning(
          'Invalid argument supplied to oneOfType. Expected an array of check functions, but ' +
          'received ' + getPostfixForTypeWarning(checker) + ' at index ' + i + '.'
        );
        return emptyFunctionThatReturnsNull;
      }
    }

    function validate(props, propName, componentName, location, propFullName) {
      var expectedTypes = [];
      for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
        var checker = arrayOfTypeCheckers[i];
        var checkerResult = checker(props, propName, componentName, location, propFullName, ReactPropTypesSecret);
        if (checkerResult == null) {
          return null;
        }
        if (checkerResult.data && has(checkerResult.data, 'expectedType')) {
          expectedTypes.push(checkerResult.data.expectedType);
        }
      }
      var expectedTypesMessage = (expectedTypes.length > 0) ? ', expected one of type [' + expectedTypes.join(', ') + ']': '';
      return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` supplied to ' + ('`' + componentName + '`' + expectedTypesMessage + '.'));
    }
    return createChainableTypeChecker(validate);
  }

  function createNodeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      if (!isNode(props[propName])) {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` supplied to ' + ('`' + componentName + '`, expected a ReactNode.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function invalidValidatorError(componentName, location, propFullName, key, type) {
    return new PropTypeError(
      (componentName || 'React class') + ': ' + location + ' type `' + propFullName + '.' + key + '` is invalid; ' +
      'it must be a function, usually from the `prop-types` package, but received `' + type + '`.'
    );
  }

  function createShapeTypeChecker(shapeTypes) {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type `' + propType + '` ' + ('supplied to `' + componentName + '`, expected `object`.'));
      }
      for (var key in shapeTypes) {
        var checker = shapeTypes[key];
        if (typeof checker !== 'function') {
          return invalidValidatorError(componentName, location, propFullName, key, getPreciseType(checker));
        }
        var error = checker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
        if (error) {
          return error;
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createStrictShapeTypeChecker(shapeTypes) {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type `' + propType + '` ' + ('supplied to `' + componentName + '`, expected `object`.'));
      }
      // We need to check all keys in case some are required but missing from props.
      var allKeys = assign({}, props[propName], shapeTypes);
      for (var key in allKeys) {
        var checker = shapeTypes[key];
        if (has(shapeTypes, key) && typeof checker !== 'function') {
          return invalidValidatorError(componentName, location, propFullName, key, getPreciseType(checker));
        }
        if (!checker) {
          return new PropTypeError(
            'Invalid ' + location + ' `' + propFullName + '` key `' + key + '` supplied to `' + componentName + '`.' +
            '\nBad object: ' + JSON.stringify(props[propName], null, '  ') +
            '\nValid keys: ' + JSON.stringify(Object.keys(shapeTypes), null, '  ')
          );
        }
        var error = checker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
        if (error) {
          return error;
        }
      }
      return null;
    }

    return createChainableTypeChecker(validate);
  }

  function isNode(propValue) {
    switch (typeof propValue) {
      case 'number':
      case 'string':
      case 'undefined':
        return true;
      case 'boolean':
        return !propValue;
      case 'object':
        if (Array.isArray(propValue)) {
          return propValue.every(isNode);
        }
        if (propValue === null || isValidElement(propValue)) {
          return true;
        }

        var iteratorFn = getIteratorFn(propValue);
        if (iteratorFn) {
          var iterator = iteratorFn.call(propValue);
          var step;
          if (iteratorFn !== propValue.entries) {
            while (!(step = iterator.next()).done) {
              if (!isNode(step.value)) {
                return false;
              }
            }
          } else {
            // Iterator will provide entry [k,v] tuples rather than values.
            while (!(step = iterator.next()).done) {
              var entry = step.value;
              if (entry) {
                if (!isNode(entry[1])) {
                  return false;
                }
              }
            }
          }
        } else {
          return false;
        }

        return true;
      default:
        return false;
    }
  }

  function isSymbol(propType, propValue) {
    // Native Symbol.
    if (propType === 'symbol') {
      return true;
    }

    // falsy value can't be a Symbol
    if (!propValue) {
      return false;
    }

    // 19.4.3.5 Symbol.prototype[@@toStringTag] === 'Symbol'
    if (propValue['@@toStringTag'] === 'Symbol') {
      return true;
    }

    // Fallback for non-spec compliant Symbols which are polyfilled.
    if (typeof Symbol === 'function' && propValue instanceof Symbol) {
      return true;
    }

    return false;
  }

  // Equivalent of `typeof` but with special handling for array and regexp.
  function getPropType(propValue) {
    var propType = typeof propValue;
    if (Array.isArray(propValue)) {
      return 'array';
    }
    if (propValue instanceof RegExp) {
      // Old webkits (at least until Android 4.0) return 'function' rather than
      // 'object' for typeof a RegExp. We'll normalize this here so that /bla/
      // passes PropTypes.object.
      return 'object';
    }
    if (isSymbol(propType, propValue)) {
      return 'symbol';
    }
    return propType;
  }

  // This handles more types than `getPropType`. Only used for error messages.
  // See `createPrimitiveTypeChecker`.
  function getPreciseType(propValue) {
    if (typeof propValue === 'undefined' || propValue === null) {
      return '' + propValue;
    }
    var propType = getPropType(propValue);
    if (propType === 'object') {
      if (propValue instanceof Date) {
        return 'date';
      } else if (propValue instanceof RegExp) {
        return 'regexp';
      }
    }
    return propType;
  }

  // Returns a string that is postfixed to a warning about an invalid type.
  // For example, "undefined" or "of type array"
  function getPostfixForTypeWarning(value) {
    var type = getPreciseType(value);
    switch (type) {
      case 'array':
      case 'object':
        return 'an ' + type;
      case 'boolean':
      case 'date':
      case 'regexp':
        return 'a ' + type;
      default:
        return type;
    }
  }

  // Returns class name of the object, if any.
  function getClassName(propValue) {
    if (!propValue.constructor || !propValue.constructor.name) {
      return ANONYMOUS;
    }
    return propValue.constructor.name;
  }

  ReactPropTypes.checkPropTypes = checkPropTypes;
  ReactPropTypes.resetWarningCache = checkPropTypes.resetWarningCache;
  ReactPropTypes.PropTypes = ReactPropTypes;

  return ReactPropTypes;
};


/***/ }),

/***/ "../node_modules/prop-types/index.js":
/*!*******************************************!*\
  !*** ../node_modules/prop-types/index.js ***!
  \*******************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (true) {
  var ReactIs = __webpack_require__(/*! react-is */ "../node_modules/prop-types/node_modules/react-is/index.js");

  // By explicitly using `prop-types` you are opting into new development behavior.
  // http://fb.me/prop-types-in-prod
  var throwOnDirectAccess = true;
  module.exports = __webpack_require__(/*! ./factoryWithTypeCheckers */ "../node_modules/prop-types/factoryWithTypeCheckers.js")(ReactIs.isElement, throwOnDirectAccess);
} else // removed by dead control flow
{}


/***/ }),

/***/ "../node_modules/prop-types/lib/ReactPropTypesSecret.js":
/*!**************************************************************!*\
  !*** ../node_modules/prop-types/lib/ReactPropTypesSecret.js ***!
  \**************************************************************/
/***/ ((module) => {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED';

module.exports = ReactPropTypesSecret;


/***/ }),

/***/ "../node_modules/prop-types/lib/has.js":
/*!*********************************************!*\
  !*** ../node_modules/prop-types/lib/has.js ***!
  \*********************************************/
/***/ ((module) => {

module.exports = Function.call.bind(Object.prototype.hasOwnProperty);


/***/ }),

/***/ "../node_modules/prop-types/node_modules/react-is/cjs/react-is.development.js":
/*!************************************************************************************!*\
  !*** ../node_modules/prop-types/node_modules/react-is/cjs/react-is.development.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";
/** @license React v16.13.1
 * react-is.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */





if (true) {
  (function() {
'use strict';

// The Symbol used to tag the ReactElement-like types. If there is no native Symbol
// nor polyfill, then a plain number is used for performance.
var hasSymbol = typeof Symbol === 'function' && Symbol.for;
var REACT_ELEMENT_TYPE = hasSymbol ? Symbol.for('react.element') : 0xeac7;
var REACT_PORTAL_TYPE = hasSymbol ? Symbol.for('react.portal') : 0xeaca;
var REACT_FRAGMENT_TYPE = hasSymbol ? Symbol.for('react.fragment') : 0xeacb;
var REACT_STRICT_MODE_TYPE = hasSymbol ? Symbol.for('react.strict_mode') : 0xeacc;
var REACT_PROFILER_TYPE = hasSymbol ? Symbol.for('react.profiler') : 0xead2;
var REACT_PROVIDER_TYPE = hasSymbol ? Symbol.for('react.provider') : 0xeacd;
var REACT_CONTEXT_TYPE = hasSymbol ? Symbol.for('react.context') : 0xeace; // TODO: We don't use AsyncMode or ConcurrentMode anymore. They were temporary
// (unstable) APIs that have been removed. Can we remove the symbols?

var REACT_ASYNC_MODE_TYPE = hasSymbol ? Symbol.for('react.async_mode') : 0xeacf;
var REACT_CONCURRENT_MODE_TYPE = hasSymbol ? Symbol.for('react.concurrent_mode') : 0xeacf;
var REACT_FORWARD_REF_TYPE = hasSymbol ? Symbol.for('react.forward_ref') : 0xead0;
var REACT_SUSPENSE_TYPE = hasSymbol ? Symbol.for('react.suspense') : 0xead1;
var REACT_SUSPENSE_LIST_TYPE = hasSymbol ? Symbol.for('react.suspense_list') : 0xead8;
var REACT_MEMO_TYPE = hasSymbol ? Symbol.for('react.memo') : 0xead3;
var REACT_LAZY_TYPE = hasSymbol ? Symbol.for('react.lazy') : 0xead4;
var REACT_BLOCK_TYPE = hasSymbol ? Symbol.for('react.block') : 0xead9;
var REACT_FUNDAMENTAL_TYPE = hasSymbol ? Symbol.for('react.fundamental') : 0xead5;
var REACT_RESPONDER_TYPE = hasSymbol ? Symbol.for('react.responder') : 0xead6;
var REACT_SCOPE_TYPE = hasSymbol ? Symbol.for('react.scope') : 0xead7;

function isValidElementType(type) {
  return typeof type === 'string' || typeof type === 'function' || // Note: its typeof might be other than 'symbol' or 'number' if it's a polyfill.
  type === REACT_FRAGMENT_TYPE || type === REACT_CONCURRENT_MODE_TYPE || type === REACT_PROFILER_TYPE || type === REACT_STRICT_MODE_TYPE || type === REACT_SUSPENSE_TYPE || type === REACT_SUSPENSE_LIST_TYPE || typeof type === 'object' && type !== null && (type.$$typeof === REACT_LAZY_TYPE || type.$$typeof === REACT_MEMO_TYPE || type.$$typeof === REACT_PROVIDER_TYPE || type.$$typeof === REACT_CONTEXT_TYPE || type.$$typeof === REACT_FORWARD_REF_TYPE || type.$$typeof === REACT_FUNDAMENTAL_TYPE || type.$$typeof === REACT_RESPONDER_TYPE || type.$$typeof === REACT_SCOPE_TYPE || type.$$typeof === REACT_BLOCK_TYPE);
}

function typeOf(object) {
  if (typeof object === 'object' && object !== null) {
    var $$typeof = object.$$typeof;

    switch ($$typeof) {
      case REACT_ELEMENT_TYPE:
        var type = object.type;

        switch (type) {
          case REACT_ASYNC_MODE_TYPE:
          case REACT_CONCURRENT_MODE_TYPE:
          case REACT_FRAGMENT_TYPE:
          case REACT_PROFILER_TYPE:
          case REACT_STRICT_MODE_TYPE:
          case REACT_SUSPENSE_TYPE:
            return type;

          default:
            var $$typeofType = type && type.$$typeof;

            switch ($$typeofType) {
              case REACT_CONTEXT_TYPE:
              case REACT_FORWARD_REF_TYPE:
              case REACT_LAZY_TYPE:
              case REACT_MEMO_TYPE:
              case REACT_PROVIDER_TYPE:
                return $$typeofType;

              default:
                return $$typeof;
            }

        }

      case REACT_PORTAL_TYPE:
        return $$typeof;
    }
  }

  return undefined;
} // AsyncMode is deprecated along with isAsyncMode

var AsyncMode = REACT_ASYNC_MODE_TYPE;
var ConcurrentMode = REACT_CONCURRENT_MODE_TYPE;
var ContextConsumer = REACT_CONTEXT_TYPE;
var ContextProvider = REACT_PROVIDER_TYPE;
var Element = REACT_ELEMENT_TYPE;
var ForwardRef = REACT_FORWARD_REF_TYPE;
var Fragment = REACT_FRAGMENT_TYPE;
var Lazy = REACT_LAZY_TYPE;
var Memo = REACT_MEMO_TYPE;
var Portal = REACT_PORTAL_TYPE;
var Profiler = REACT_PROFILER_TYPE;
var StrictMode = REACT_STRICT_MODE_TYPE;
var Suspense = REACT_SUSPENSE_TYPE;
var hasWarnedAboutDeprecatedIsAsyncMode = false; // AsyncMode should be deprecated

function isAsyncMode(object) {
  {
    if (!hasWarnedAboutDeprecatedIsAsyncMode) {
      hasWarnedAboutDeprecatedIsAsyncMode = true; // Using console['warn'] to evade Babel and ESLint

      console['warn']('The ReactIs.isAsyncMode() alias has been deprecated, ' + 'and will be removed in React 17+. Update your code to use ' + 'ReactIs.isConcurrentMode() instead. It has the exact same API.');
    }
  }

  return isConcurrentMode(object) || typeOf(object) === REACT_ASYNC_MODE_TYPE;
}
function isConcurrentMode(object) {
  return typeOf(object) === REACT_CONCURRENT_MODE_TYPE;
}
function isContextConsumer(object) {
  return typeOf(object) === REACT_CONTEXT_TYPE;
}
function isContextProvider(object) {
  return typeOf(object) === REACT_PROVIDER_TYPE;
}
function isElement(object) {
  return typeof object === 'object' && object !== null && object.$$typeof === REACT_ELEMENT_TYPE;
}
function isForwardRef(object) {
  return typeOf(object) === REACT_FORWARD_REF_TYPE;
}
function isFragment(object) {
  return typeOf(object) === REACT_FRAGMENT_TYPE;
}
function isLazy(object) {
  return typeOf(object) === REACT_LAZY_TYPE;
}
function isMemo(object) {
  return typeOf(object) === REACT_MEMO_TYPE;
}
function isPortal(object) {
  return typeOf(object) === REACT_PORTAL_TYPE;
}
function isProfiler(object) {
  return typeOf(object) === REACT_PROFILER_TYPE;
}
function isStrictMode(object) {
  return typeOf(object) === REACT_STRICT_MODE_TYPE;
}
function isSuspense(object) {
  return typeOf(object) === REACT_SUSPENSE_TYPE;
}

exports.AsyncMode = AsyncMode;
exports.ConcurrentMode = ConcurrentMode;
exports.ContextConsumer = ContextConsumer;
exports.ContextProvider = ContextProvider;
exports.Element = Element;
exports.ForwardRef = ForwardRef;
exports.Fragment = Fragment;
exports.Lazy = Lazy;
exports.Memo = Memo;
exports.Portal = Portal;
exports.Profiler = Profiler;
exports.StrictMode = StrictMode;
exports.Suspense = Suspense;
exports.isAsyncMode = isAsyncMode;
exports.isConcurrentMode = isConcurrentMode;
exports.isContextConsumer = isContextConsumer;
exports.isContextProvider = isContextProvider;
exports.isElement = isElement;
exports.isForwardRef = isForwardRef;
exports.isFragment = isFragment;
exports.isLazy = isLazy;
exports.isMemo = isMemo;
exports.isPortal = isPortal;
exports.isProfiler = isProfiler;
exports.isStrictMode = isStrictMode;
exports.isSuspense = isSuspense;
exports.isValidElementType = isValidElementType;
exports.typeOf = typeOf;
  })();
}


/***/ }),

/***/ "../node_modules/prop-types/node_modules/react-is/index.js":
/*!*****************************************************************!*\
  !*** ../node_modules/prop-types/node_modules/react-is/index.js ***!
  \*****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


if (false) // removed by dead control flow
{} else {
  module.exports = __webpack_require__(/*! ./cjs/react-is.development.js */ "../node_modules/prop-types/node_modules/react-is/cjs/react-is.development.js");
}


/***/ }),

/***/ "../node_modules/react-dom/client.js":
/*!*******************************************!*\
  !*** ../node_modules/react-dom/client.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var m = __webpack_require__(/*! react-dom */ "react-dom");
if (false) // removed by dead control flow
{} else {
  var i = m.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;
  exports.createRoot = function(c, o) {
    i.usingClientEntryPoint = true;
    try {
      return m.createRoot(c, o);
    } finally {
      i.usingClientEntryPoint = false;
    }
  };
  exports.hydrateRoot = function(c, h, o) {
    i.usingClientEntryPoint = true;
    try {
      return m.hydrateRoot(c, h, o);
    } finally {
      i.usingClientEntryPoint = false;
    }
  };
}


/***/ }),

/***/ "@elementor/ui":
/*!*********************************!*\
  !*** external "elementorV2.ui" ***!
  \*********************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui;

/***/ }),

/***/ "@elementor/ui/Box":
/*!****************************************!*\
  !*** external "elementorV2.ui['Box']" ***!
  \****************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['Box'];

/***/ }),

/***/ "@elementor/ui/Button":
/*!*******************************************!*\
  !*** external "elementorV2.ui['Button']" ***!
  \*******************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['Button'];

/***/ }),

/***/ "@elementor/ui/Card":
/*!*****************************************!*\
  !*** external "elementorV2.ui['Card']" ***!
  \*****************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['Card'];

/***/ }),

/***/ "@elementor/ui/CardActions":
/*!************************************************!*\
  !*** external "elementorV2.ui['CardActions']" ***!
  \************************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['CardActions'];

/***/ }),

/***/ "@elementor/ui/CardContent":
/*!************************************************!*\
  !*** external "elementorV2.ui['CardContent']" ***!
  \************************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['CardContent'];

/***/ }),

/***/ "@elementor/ui/CardMedia":
/*!**********************************************!*\
  !*** external "elementorV2.ui['CardMedia']" ***!
  \**********************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['CardMedia'];

/***/ }),

/***/ "@elementor/ui/Dialog":
/*!*******************************************!*\
  !*** external "elementorV2.ui['Dialog']" ***!
  \*******************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['Dialog'];

/***/ }),

/***/ "@elementor/ui/DialogActions":
/*!**************************************************!*\
  !*** external "elementorV2.ui['DialogActions']" ***!
  \**************************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['DialogActions'];

/***/ }),

/***/ "@elementor/ui/DialogContent":
/*!**************************************************!*\
  !*** external "elementorV2.ui['DialogContent']" ***!
  \**************************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['DialogContent'];

/***/ }),

/***/ "@elementor/ui/DialogContentText":
/*!******************************************************!*\
  !*** external "elementorV2.ui['DialogContentText']" ***!
  \******************************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['DialogContentText'];

/***/ }),

/***/ "@elementor/ui/DialogHeader":
/*!*************************************************!*\
  !*** external "elementorV2.ui['DialogHeader']" ***!
  \*************************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['DialogHeader'];

/***/ }),

/***/ "@elementor/ui/DialogHeaderGroup":
/*!******************************************************!*\
  !*** external "elementorV2.ui['DialogHeaderGroup']" ***!
  \******************************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['DialogHeaderGroup'];

/***/ }),

/***/ "@elementor/ui/DialogTitle":
/*!************************************************!*\
  !*** external "elementorV2.ui['DialogTitle']" ***!
  \************************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['DialogTitle'];

/***/ }),

/***/ "@elementor/ui/Divider":
/*!********************************************!*\
  !*** external "elementorV2.ui['Divider']" ***!
  \********************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['Divider'];

/***/ }),

/***/ "@elementor/ui/Link":
/*!*****************************************!*\
  !*** external "elementorV2.ui['Link']" ***!
  \*****************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['Link'];

/***/ }),

/***/ "@elementor/ui/List":
/*!*****************************************!*\
  !*** external "elementorV2.ui['List']" ***!
  \*****************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['List'];

/***/ }),

/***/ "@elementor/ui/ListItem":
/*!*********************************************!*\
  !*** external "elementorV2.ui['ListItem']" ***!
  \*********************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['ListItem'];

/***/ }),

/***/ "@elementor/ui/ListItemButton":
/*!***************************************************!*\
  !*** external "elementorV2.ui['ListItemButton']" ***!
  \***************************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['ListItemButton'];

/***/ }),

/***/ "@elementor/ui/ListItemText":
/*!*************************************************!*\
  !*** external "elementorV2.ui['ListItemText']" ***!
  \*************************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['ListItemText'];

/***/ }),

/***/ "@elementor/ui/TextField":
/*!**********************************************!*\
  !*** external "elementorV2.ui['TextField']" ***!
  \**********************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['TextField'];

/***/ }),

/***/ "@elementor/ui/Typography":
/*!***********************************************!*\
  !*** external "elementorV2.ui['Typography']" ***!
  \***********************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui['Typography'];

/***/ }),

/***/ "@wordpress/i18n":
/*!**************************!*\
  !*** external "wp.i18n" ***!
  \**************************/
/***/ ((module) => {

"use strict";
module.exports = wp.i18n;

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

"use strict";
module.exports = React;

/***/ }),

/***/ "react-dom":
/*!***************************!*\
  !*** external "ReactDOM" ***!
  \***************************/
/***/ ((module) => {

"use strict";
module.exports = ReactDOM;

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
/*!****************************************!*\
  !*** ../modules/home/assets/js/app.js ***!
  \****************************************/
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _react2 = _interopRequireDefault(__webpack_require__(/*! elementor-utils/react */ "../assets/dev/js/utils/react.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _homeScreen = _interopRequireDefault(__webpack_require__(/*! ./components/home-screen */ "../modules/home/assets/js/components/home-screen.js"));
var App = function App(props) {
  return /*#__PURE__*/_react.default.createElement(_ui.DirectionProvider, {
    rtl: props.isRTL
  }, /*#__PURE__*/_react.default.createElement(_ui.LocalizationProvider, null, /*#__PURE__*/_react.default.createElement(_ui.ThemeProvider, {
    colorScheme: 'light'
  }, /*#__PURE__*/_react.default.createElement(_homeScreen.default, {
    homeScreenData: props.homeScreenData,
    adminUrl: props.adminUrl
  }))));
};
var isRTL = elementorCommon.config.isRTL,
  adminUrl = elementorAppConfig.admin_url,
  rootElement = document.querySelector('#e-home-screen');
App.propTypes = {
  isRTL: PropTypes.bool,
  adminUrl: PropTypes.string,
  homeScreenData: PropTypes.object
};
_react2.default.render(/*#__PURE__*/_react.default.createElement(App, {
  isRTL: isRTL,
  homeScreenData: elementorHomeScreenData,
  adminUrl: adminUrl
}), rootElement);
})();

/******/ })()
;
//# sourceMappingURL=e-home-screen.js.map