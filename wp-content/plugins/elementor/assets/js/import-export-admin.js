/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

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
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!*******************************************************!*\
  !*** ../app/modules/import-export/assets/js/admin.js ***!
  \*******************************************************/
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var Admin = /*#__PURE__*/function () {
  function Admin() {
    (0, _classCallCheck2.default)(this, Admin);
    /**
     * Session Storage Key
     *
     * @type {string}
     */
    (0, _defineProperty2.default)(this, "KIT_DATA_KEY", 'elementor-kit-data');
    /**
     * Contains the ID of the referrer Kit and the name of the Kit to remove. Stored in session storage.
     *
     * @type {Object}
     */
    (0, _defineProperty2.default)(this, "cachedKitData", void 0);
    /**
     * The 'Remove Kit' revert button
     *
     * @type {Element}
     */
    (0, _defineProperty2.default)(this, "revertButton", void 0);
    /**
     * Name of the kit currently active (last imported)
     *
     * @type {string}
     */
    (0, _defineProperty2.default)(this, "activeKitName", void 0);
    this.activeKitName = this.getActiveKitName();
    this.revertButton = document.getElementById('elementor-import-export__revert_kit');
    if (this.revertButton) {
      this.revertButton.addEventListener('click', this.onRevertButtonClick.bind(this));
      this.maybeAddRevertBtnMargin();
    }
    this.maybeShowReferrerKitDialog();
  }

  /**
   * Add bottom margin to revert btn if referred from Kit library
   */
  return (0, _createClass2.default)(Admin, [{
    key: "maybeAddRevertBtnMargin",
    value: function maybeAddRevertBtnMargin() {
      var referrerKitId = new URLSearchParams(this.revertButton.href).get('referrer_kit');
      if (!referrerKitId) {
        return;
      }
      this.revertButton.style.marginBottom = this.calculateMargin();
      this.scrollToBottom();
    }

    /**
     * CalculateMargin
     *
     * @return {string}
     */
  }, {
    key: "calculateMargin",
    value: function calculateMargin() {
      var adminBar = document.getElementById('wpadminbar');
      var adminBarHeight = adminBar ? adminBar.offsetHeight : 0;
      var revertKitHeight = this.revertButton.parentElement.offsetHeight;
      return document.body.clientHeight - adminBarHeight - revertKitHeight - document.getElementById('wpfooter').offsetHeight - 15 // Extra margin at the top
      + 'px';
    }

    /**
     * Scroll to the bottom of the page
     */
  }, {
    key: "scrollToBottom",
    value: function scrollToBottom() {
      setTimeout(function () {
        window.scrollTo(0, document.body.scrollHeight);
      });
    }

    /**
     * RevertBtnOnClick
     *
     * @param {Event} event
     */
  }, {
    key: "onRevertButtonClick",
    value: function onRevertButtonClick(event) {
      var _this = this;
      event.preventDefault();
      elementorCommon.dialogsManager.createWidget('confirm', {
        headerMessage: __('Are you sure?', 'elementor'),
        // Translators: %s is the name of the active Kit
        message: __('Removing %s will permanently delete changes made to the Websites Template\'s content and site settings', 'elementor').replace('%s', this.activeKitName),
        strings: {
          confirm: __('Delete', 'elementor'),
          cancel: __('Cancel', 'elementor')
        },
        onConfirm: function onConfirm() {
          return _this.onRevertConfirm();
        }
      }).show();
    }
  }, {
    key: "onRevertConfirm",
    value: function onRevertConfirm() {
      var referrerKitId = new URLSearchParams(this.revertButton.href).get('referrer_kit');
      this.saveToCache(referrerKitId !== null && referrerKitId !== void 0 ? referrerKitId : '');
      location.href = this.revertButton.href;
    }
  }, {
    key: "maybeShowReferrerKitDialog",
    value: function maybeShowReferrerKitDialog() {
      var _this$getDataFromCach = this.getDataFromCache(),
        referrerKitId = _this$getDataFromCach.referrerKitId;
      if (undefined === referrerKitId) {
        return;
      }
      if (0 === referrerKitId.length) {
        this.createKitDeletedWidget({
          message: __('Try a different Website Template or build your site from scratch.', 'elementor'),
          strings: {
            confirm: __('OK', 'elementor'),
            cancel: __('Library', 'elementor')
          },
          onCancel: function onCancel() {
            location.href = elementorImportExport.appUrl;
          }
        });
        this.clearCache();
        return;
      }
      this.createKitDeletedWidget({
        message: __('You\'re ready to apply a new Kit!', 'elementor'),
        strings: {
          confirm: __('Continue to new Kit', 'elementor'),
          cancel: __('Close', 'elementor')
        },
        onConfirm: function onConfirm() {
          location.href = elementorImportExport.appUrl + '/preview/' + referrerKitId;
        }
      });
      this.clearCache();
    }

    /**
     * CreateKitDeletedWidget
     *
     * @param {Object} options
     */
  }, {
    key: "createKitDeletedWidget",
    value: function createKitDeletedWidget(options) {
      var _this$getDataFromCach2 = this.getDataFromCache(),
        activeKitName = _this$getDataFromCach2.activeKitName;
      elementorCommon.dialogsManager.createWidget('confirm', {
        id: 'e-revert-kit-deleted-dialog',
        // Translators: %s is the name of the active Kit
        headerMessage: __('%s was successfully deleted', 'elementor').replace('%s', activeKitName),
        message: options.message,
        strings: {
          confirm: options.strings.confirm,
          cancel: options.strings.cancel
        },
        onConfirm: options.onConfirm,
        onCancel: options.onCancel
      }).show();
    }

    /**
     * Retrieving the last imported kit from the elementorAppConfig global
     *
     * @return {string}
     */
  }, {
    key: "getActiveKitName",
    value: function getActiveKitName() {
      var lastKit = elementorImportExport.lastImportedSession;
      if (lastKit.kit_title) {
        return lastKit.kit_title;
      }
      if (lastKit.kit_name) {
        return this.convertNameToTitle(lastKit.kit_name);
      }
      return __('Your Kit', 'elementor');
    }

    /**
     * ConvertNameToTitle
     *
     * @param {string} name
     *
     * @return {string}
     */
  }, {
    key: "convertNameToTitle",
    value: function convertNameToTitle(name) {
      return name.split(/[-_]+/).map(function (word) {
        return word[0].toUpperCase() + word.substring(1);
      }).join(' ');
    }
  }, {
    key: "saveToCache",
    value: function saveToCache(referrerKitId) {
      sessionStorage.setItem(this.KIT_DATA_KEY, JSON.stringify({
        referrerKitId: referrerKitId,
        activeKitName: this.activeKitName
      }));
    }
  }, {
    key: "getDataFromCache",
    value: function getDataFromCache() {
      var _this$cachedKitData;
      if (this.cachedKitData) {
        return this.cachedKitData;
      }
      try {
        this.cachedKitData = JSON.parse(sessionStorage.getItem(this.KIT_DATA_KEY));
      } catch (e) {
        return {};
      }
      return (_this$cachedKitData = this.cachedKitData) !== null && _this$cachedKitData !== void 0 ? _this$cachedKitData : {};
    }
  }, {
    key: "clearCache",
    value: function clearCache() {
      sessionStorage.removeItem(this.KIT_DATA_KEY);
    }
  }]);
}();
window.addEventListener('load', function () {
  new Admin();
});
})();

/******/ })()
;
//# sourceMappingURL=import-export-admin.js.map