/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../assets/dev/js/admin/new-template/behaviors/lock-pro.js":
/*!*****************************************************************!*\
  !*** ../assets/dev/js/admin/new-template/behaviors/lock-pro.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var LockPro = exports["default"] = /*#__PURE__*/function () {
  function LockPro(elements) {
    (0, _classCallCheck2.default)(this, LockPro);
    this.elements = elements;
  }
  return (0, _createClass2.default)(LockPro, [{
    key: "bindEvents",
    value: function bindEvents() {
      var _this$elements = this.elements,
        form = _this$elements.form,
        templateType = _this$elements.templateType;
      form.addEventListener('submit', this.onFormSubmit.bind(this));
      templateType.addEventListener('change', this.onTemplateTypeChange.bind(this));

      // Force checking on render, to make sure that default values are also checked.
      this.onTemplateTypeChange();
    }
  }, {
    key: "onFormSubmit",
    value: function onFormSubmit(e) {
      var lockOptions = this.getCurrentLockOptions();
      if (lockOptions.is_locked) {
        e.preventDefault();
      }
    }
  }, {
    key: "onTemplateTypeChange",
    value: function onTemplateTypeChange() {
      var lockOptions = this.getCurrentLockOptions();
      if (lockOptions.is_locked) {
        this.lock(lockOptions);
      } else {
        this.unlock();
      }
    }
  }, {
    key: "getCurrentLockOptions",
    value: function getCurrentLockOptions() {
      var templateType = this.elements.templateType,
        currentOption = templateType.options[templateType.selectedIndex];
      return JSON.parse(currentOption.dataset.lock || '{}');
    }
  }, {
    key: "lock",
    value: function lock(lockOptions) {
      this.showLockBadge(lockOptions.badge);
      this.showLockButton(lockOptions.button);
      this.hideSubmitButton();
    }
  }, {
    key: "unlock",
    value: function unlock() {
      this.hideLockBadge();
      this.hideLockButton();
      this.showSubmitButton();
    }
  }, {
    key: "showLockBadge",
    value: function showLockBadge(badgeConfig) {
      var _this$elements2 = this.elements,
        lockBadge = _this$elements2.lockBadge,
        lockBadgeText = _this$elements2.lockBadgeText,
        lockBadgeIcon = _this$elements2.lockBadgeIcon;
      lockBadgeText.innerText = badgeConfig.text;
      lockBadgeIcon.className = badgeConfig.icon;
      lockBadge.classList.remove('e-hidden');
    }
  }, {
    key: "hideLockBadge",
    value: function hideLockBadge() {
      this.elements.lockBadge.classList.add('e-hidden');
    }
  }, {
    key: "showLockButton",
    value: function showLockButton(buttonConfig) {
      var lockButton = this.elements.lockButton;
      lockButton.href = this.replaceLockLinkPlaceholders(buttonConfig.url);
      lockButton.innerText = buttonConfig.text;
      lockButton.classList.remove('e-hidden');
    }
  }, {
    key: "hideLockButton",
    value: function hideLockButton() {
      this.elements.lockButton.classList.add('e-hidden');
    }
  }, {
    key: "showSubmitButton",
    value: function showSubmitButton() {
      this.elements.submitButton.classList.remove('e-hidden');
    }
  }, {
    key: "hideSubmitButton",
    value: function hideSubmitButton() {
      this.elements.submitButton.classList.add('e-hidden');
    }
  }, {
    key: "replaceLockLinkPlaceholders",
    value: function replaceLockLinkPlaceholders(link) {
      return link.replace(/%%utm_source%%/g, 'wp-add-new').replace(/%%utm_medium%%/g, 'wp-dash');
    }
  }]);
}();

/***/ }),

/***/ "../assets/dev/js/admin/new-template/layout.js":
/*!*****************************************************!*\
  !*** ../assets/dev/js/admin/new-template/layout.js ***!
  \*****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _lockPro = _interopRequireDefault(__webpack_require__(/*! ./behaviors/lock-pro */ "../assets/dev/js/admin/new-template/behaviors/lock-pro.js"));
var NewTemplateView = __webpack_require__(/*! elementor-admin/new-template/view */ "../assets/dev/js/admin/new-template/view.js");
module.exports = elementorModules.common.views.modal.Layout.extend({
  getModalOptions: function getModalOptions() {
    return {
      id: 'elementor-new-template-modal'
    };
  },
  getLogoOptions: function getLogoOptions() {
    return {
      title: __('New Template', 'elementor')
    };
  },
  initialize: function initialize() {
    elementorModules.common.views.modal.Layout.prototype.initialize.apply(this, arguments);
    this.showLogo();
    this.showContentView();
    this.initElements();
    this.lockProBehavior = new _lockPro.default(this.elements);
    this.lockProBehavior.bindEvents();
    this.setupDynamicControlsVisibility();
  },
  setupDynamicControlsVisibility: function setupDynamicControlsVisibility() {
    // eslint-disable-next-line camelcase
    var isFormControlsDefined = 'undefined' !== typeof elementor_new_template_form_controls;
    if (!isFormControlsDefined) {
      return;
    }
    var CONTROL_ID_PREFIX = 'elementor-new-template__form__';
    var templateTypeSelectId = "".concat(CONTROL_ID_PREFIX, "template-type");
    var dynamicControlsVisibilityListener = function dynamicControlsVisibilityListener() {
      // eslint-disable-next-line camelcase
      elementorAdmin.templateControls.setDynamicControlsVisibility(CONTROL_ID_PREFIX, elementor_new_template_form_controls);
    };
    this.getModal().onShow = function () {
      dynamicControlsVisibilityListener();
      document.getElementById(templateTypeSelectId).addEventListener('change', dynamicControlsVisibilityListener);
    };
    this.getModal().onHide = function () {
      document.getElementById(templateTypeSelectId).removeEventListener('change', dynamicControlsVisibilityListener);
    };
  },
  initElements: function initElements() {
    var container = this.$el[0],
      root = '#elementor-new-template__form';
    this.elements = {
      form: container.querySelector(root),
      submitButton: container.querySelector("".concat(root, "__submit")),
      lockButton: container.querySelector("".concat(root, "__lock_button")),
      templateType: container.querySelector("".concat(root, "__template-type")),
      lockBadge: container.querySelector("".concat(root, "__template-type-badge")),
      lockBadgeText: container.querySelector("".concat(root, "__template-type-badge__text")),
      lockBadgeIcon: container.querySelector("".concat(root, "__template-type-badge__icon"))
    };
  },
  showContentView: function showContentView() {
    this.modalContent.show(new NewTemplateView());
  }
});

/***/ }),

/***/ "../assets/dev/js/admin/new-template/view.js":
/*!***************************************************!*\
  !*** ../assets/dev/js/admin/new-template/view.js ***!
  \***************************************************/
/***/ ((module) => {

"use strict";


module.exports = Marionette.ItemView.extend({
  id: 'elementor-new-template-dialog-content',
  template: '#tmpl-elementor-new-template',
  ui: {},
  events: {},
  onRender: function onRender() {}
});

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
/*!***********************************************************!*\
  !*** ../assets/dev/js/admin/new-template/new-template.js ***!
  \***********************************************************/


var NewTemplateLayout = __webpack_require__(/*! elementor-admin/new-template/layout */ "../assets/dev/js/admin/new-template/layout.js");
var NewTemplateModule = elementorModules.ViewModule.extend({
  getDefaultSettings: function getDefaultSettings() {
    return {
      selectors: {
        addButton: '.page-title-action:first, #elementor-template-library-add-new'
      }
    };
  },
  getDefaultElements: function getDefaultElements() {
    var selectors = this.getSettings('selectors');
    return {
      $addButton: jQuery(selectors.addButton)
    };
  },
  bindEvents: function bindEvents() {
    this.elements.$addButton.on('click', this.onAddButtonClick);
    elementorCommon.elements.$window.on('hashchange', this.showModalByHash.bind(this));
  },
  showModalByHash: function showModalByHash() {
    if ('#add_new' === location.hash) {
      var _this$layout;
      (_this$layout = this.layout) === null || _this$layout === void 0 || _this$layout.showModal();
      location.hash = '';
    }
  },
  onInit: function onInit() {
    elementorModules.ViewModule.prototype.onInit.apply(this, arguments);
    this.layout = new NewTemplateLayout();
    this.showModalByHash();
  },
  onAddButtonClick: function onAddButtonClick(event) {
    var _this$layout2;
    event.preventDefault();
    (_this$layout2 = this.layout) === null || _this$layout2 === void 0 || _this$layout2.showModal();
  }
});
jQuery(function () {
  window.elementorNewTemplate = new NewTemplateModule();
});
})();

/******/ })()
;
//# sourceMappingURL=new-template.js.map