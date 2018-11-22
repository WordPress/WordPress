this["wp"] = this["wp"] || {}; this["wp"]["nux"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/nux/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _arrayWithHoles; });\nfunction _arrayWithHoles(arr) {\n  if (Array.isArray(arr)) return arr;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _arrayWithoutHoles; });\nfunction _arrayWithoutHoles(arr) {\n  if (Array.isArray(arr)) {\n    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {\n      arr2[i] = arr[i];\n    }\n\n    return arr2;\n  }\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _defineProperty; });\nfunction _defineProperty(obj, key, value) {\n  if (key in obj) {\n    Object.defineProperty(obj, key, {\n      value: value,\n      enumerable: true,\n      configurable: true,\n      writable: true\n    });\n  } else {\n    obj[key] = value;\n  }\n\n  return obj;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/defineProperty.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/iterableToArray.js":
/*!********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _iterableToArray; });\nfunction _iterableToArray(iter) {\n  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === \"[object Arguments]\") return Array.from(iter);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/iterableToArray.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _iterableToArrayLimit; });\nfunction _iterableToArrayLimit(arr, i) {\n  var _arr = [];\n  var _n = true;\n  var _d = false;\n  var _e = undefined;\n\n  try {\n    for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {\n      _arr.push(_s.value);\n\n      if (i && _arr.length === i) break;\n    }\n  } catch (err) {\n    _d = true;\n    _e = err;\n  } finally {\n    try {\n      if (!_n && _i[\"return\"] != null) _i[\"return\"]();\n    } finally {\n      if (_d) throw _e;\n    }\n  }\n\n  return _arr;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js":
/*!********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _nonIterableRest; });\nfunction _nonIterableRest() {\n  throw new TypeError(\"Invalid attempt to destructure non-iterable instance\");\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _nonIterableSpread; });\nfunction _nonIterableSpread() {\n  throw new TypeError(\"Invalid attempt to spread non-iterable instance\");\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectSpread.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _objectSpread; });\n/* harmony import */ var _defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./defineProperty */ \"./node_modules/@babel/runtime/helpers/esm/defineProperty.js\");\n\nfunction _objectSpread(target) {\n  for (var i = 1; i < arguments.length; i++) {\n    var source = arguments[i] != null ? arguments[i] : {};\n    var ownKeys = Object.keys(source);\n\n    if (typeof Object.getOwnPropertySymbols === 'function') {\n      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {\n        return Object.getOwnPropertyDescriptor(source, sym).enumerable;\n      }));\n    }\n\n    ownKeys.forEach(function (key) {\n      Object(_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(target, key, source[key]);\n    });\n  }\n\n  return target;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/objectSpread.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _slicedToArray; });\n/* harmony import */ var _arrayWithHoles__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayWithHoles */ \"./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js\");\n/* harmony import */ var _iterableToArrayLimit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./iterableToArrayLimit */ \"./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js\");\n/* harmony import */ var _nonIterableRest__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./nonIterableRest */ \"./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js\");\n\n\n\nfunction _slicedToArray(arr, i) {\n  return Object(_arrayWithHoles__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(arr) || Object(_iterableToArrayLimit__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(arr, i) || Object(_nonIterableRest__WEBPACK_IMPORTED_MODULE_2__[\"default\"])();\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/slicedToArray.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _toConsumableArray; });\n/* harmony import */ var _arrayWithoutHoles__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayWithoutHoles */ \"./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js\");\n/* harmony import */ var _iterableToArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./iterableToArray */ \"./node_modules/@babel/runtime/helpers/esm/iterableToArray.js\");\n/* harmony import */ var _nonIterableSpread__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./nonIterableSpread */ \"./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js\");\n\n\n\nfunction _toConsumableArray(arr) {\n  return Object(_arrayWithoutHoles__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(arr) || Object(_iterableToArray__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(arr) || Object(_nonIterableSpread__WEBPACK_IMPORTED_MODULE_2__[\"default\"])();\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js?");

/***/ }),

/***/ "./node_modules/@wordpress/nux/build-module/components/dot-tip/index.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@wordpress/nux/build-module/components/dot-tip/index.js ***!
  \******************************************************************************/
/*! exports provided: DotTip, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"DotTip\", function() { return DotTip; });\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/compose */ \"@wordpress/compose\");\n/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ \"@wordpress/components\");\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ \"@wordpress/data\");\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);\n\n\n/**\n * WordPress dependencies\n */\n\n\n\n\n\nfunction getAnchorRect(anchor) {\n  // The default getAnchorRect() excludes an element's top and bottom padding\n  // from its calculation. We want tips to point to the outer margin of an\n  // element, so we override getAnchorRect() to include all padding.\n  return anchor.parentNode.getBoundingClientRect();\n}\n\nfunction onClick(event) {\n  // Tips are often nested within buttons. We stop propagation so that clicking\n  // on a tip doesn't result in the button being clicked.\n  event.stopPropagation();\n}\n\nfunction DotTip(_ref) {\n  var children = _ref.children,\n      isVisible = _ref.isVisible,\n      hasNextTip = _ref.hasNextTip,\n      onDismiss = _ref.onDismiss,\n      onDisable = _ref.onDisable;\n\n  if (!isVisible) {\n    return null;\n  }\n\n  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__[\"Popover\"], {\n    className: \"nux-dot-tip\",\n    position: \"middle right\",\n    noArrow: true,\n    focusOnMount: \"container\",\n    getAnchorRect: getAnchorRect,\n    role: \"dialog\",\n    \"aria-label\": Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__[\"__\"])('Gutenberg tips'),\n    onClick: onClick\n  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"p\", null, children), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(\"p\", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__[\"Button\"], {\n    isLink: true,\n    onClick: onDismiss\n  }, hasNextTip ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__[\"__\"])('See next tip') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__[\"__\"])('Got it'))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__[\"createElement\"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__[\"IconButton\"], {\n    className: \"nux-dot-tip__disable\",\n    icon: \"no-alt\",\n    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__[\"__\"])('Disable tips'),\n    onClick: onDisable\n  }));\n}\n/* harmony default export */ __webpack_exports__[\"default\"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_1__[\"compose\"])(Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__[\"withSelect\"])(function (select, _ref2) {\n  var tipId = _ref2.tipId;\n\n  var _select = select('core/nux'),\n      isTipVisible = _select.isTipVisible,\n      getAssociatedGuide = _select.getAssociatedGuide;\n\n  var associatedGuide = getAssociatedGuide(tipId);\n  return {\n    isVisible: isTipVisible(tipId),\n    hasNextTip: !!(associatedGuide && associatedGuide.nextTipId)\n  };\n}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__[\"withDispatch\"])(function (dispatch, _ref3) {\n  var tipId = _ref3.tipId;\n\n  var _dispatch = dispatch('core/nux'),\n      dismissTip = _dispatch.dismissTip,\n      disableTips = _dispatch.disableTips;\n\n  return {\n    onDismiss: function onDismiss() {\n      dismissTip(tipId);\n    },\n    onDisable: function onDisable() {\n      disableTips();\n    }\n  };\n}))(DotTip));\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/nux/build-module/components/dot-tip/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/nux/build-module/index.js":
/*!***********************************************************!*\
  !*** ./node_modules/@wordpress/nux/build-module/index.js ***!
  \***********************************************************/
/*! exports provided: DotTip */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./store */ \"./node_modules/@wordpress/nux/build-module/store/index.js\");\n/* harmony import */ var _components_dot_tip__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/dot-tip */ \"./node_modules/@wordpress/nux/build-module/components/dot-tip/index.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"DotTip\", function() { return _components_dot_tip__WEBPACK_IMPORTED_MODULE_1__[\"default\"]; });\n\n/**\n * Internal dependencies\n */\n\n\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/nux/build-module/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/nux/build-module/store/actions.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@wordpress/nux/build-module/store/actions.js ***!
  \*******************************************************************/
/*! exports provided: triggerGuide, dismissTip, disableTips, enableTips */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"triggerGuide\", function() { return triggerGuide; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"dismissTip\", function() { return dismissTip; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"disableTips\", function() { return disableTips; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"enableTips\", function() { return enableTips; });\n/**\n * Returns an action object that, when dispatched, presents a guide that takes\n * the user through a series of tips step by step.\n *\n * @param {string[]} tipIds Which tips to show in the guide.\n *\n * @return {Object} Action object.\n */\nfunction triggerGuide(tipIds) {\n  return {\n    type: 'TRIGGER_GUIDE',\n    tipIds: tipIds\n  };\n}\n/**\n * Returns an action object that, when dispatched, dismisses the given tip. A\n * dismissed tip will not show again.\n *\n * @param {string} id The tip to dismiss.\n *\n * @return {Object} Action object.\n */\n\nfunction dismissTip(id) {\n  return {\n    type: 'DISMISS_TIP',\n    id: id\n  };\n}\n/**\n * Returns an action object that, when dispatched, prevents all tips from\n * showing again.\n *\n * @return {Object} Action object.\n */\n\nfunction disableTips() {\n  return {\n    type: 'DISABLE_TIPS'\n  };\n}\n/**\n * Returns an action object that, when dispatched, makes all tips show again.\n *\n * @return {Object} Action object.\n */\n\nfunction enableTips() {\n  return {\n    type: 'ENABLE_TIPS'\n  };\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/nux/build-module/store/actions.js?");

/***/ }),

/***/ "./node_modules/@wordpress/nux/build-module/store/index.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@wordpress/nux/build-module/store/index.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ \"@wordpress/data\");\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _reducer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./reducer */ \"./node_modules/@wordpress/nux/build-module/store/reducer.js\");\n/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./actions */ \"./node_modules/@wordpress/nux/build-module/store/actions.js\");\n/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./selectors */ \"./node_modules/@wordpress/nux/build-module/store/selectors.js\");\n/**\n * WordPress dependencies\n */\n\n/**\n * Internal dependencies\n */\n\n\n\n\nvar store = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__[\"registerStore\"])('core/nux', {\n  reducer: _reducer__WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  actions: _actions__WEBPACK_IMPORTED_MODULE_2__,\n  selectors: _selectors__WEBPACK_IMPORTED_MODULE_3__,\n  persist: ['preferences']\n});\n/* harmony default export */ __webpack_exports__[\"default\"] = (store);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/nux/build-module/store/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/nux/build-module/store/reducer.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@wordpress/nux/build-module/store/reducer.js ***!
  \*******************************************************************/
/*! exports provided: guides, areTipsEnabled, dismissedTips, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"guides\", function() { return guides; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"areTipsEnabled\", function() { return areTipsEnabled; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"dismissedTips\", function() { return dismissedTips; });\n/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ \"./node_modules/@babel/runtime/helpers/esm/defineProperty.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ \"./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js\");\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ \"@wordpress/data\");\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);\n\n\n\n\n/**\n * WordPress dependencies\n */\n\n/**\n * Reducer that tracks which tips are in a guide. Each guide is represented by\n * an array which contains the tip identifiers contained within that guide.\n *\n * @param {Array} state  Current state.\n * @param {Object} action Dispatched action.\n *\n * @return {Array} Updated state.\n */\n\nfunction guides() {\n  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];\n  var action = arguments.length > 1 ? arguments[1] : undefined;\n\n  switch (action.type) {\n    case 'TRIGGER_GUIDE':\n      return Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(state).concat([action.tipIds]);\n  }\n\n  return state;\n}\n/**\n * Reducer that tracks whether or not tips are globally enabled.\n *\n * @param {boolean} state Current state.\n * @param {Object} action Dispatched action.\n *\n * @return {boolean} Updated state.\n */\n\nfunction areTipsEnabled() {\n  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;\n  var action = arguments.length > 1 ? arguments[1] : undefined;\n\n  switch (action.type) {\n    case 'DISABLE_TIPS':\n      return false;\n\n    case 'ENABLE_TIPS':\n      return true;\n  }\n\n  return state;\n}\n/**\n * Reducer that tracks which tips have been dismissed. If the state object\n * contains a tip identifier, then that tip is dismissed.\n *\n * @param {Object} state  Current state.\n * @param {Object} action Dispatched action.\n *\n * @return {Object} Updated state.\n */\n\nfunction dismissedTips() {\n  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};\n  var action = arguments.length > 1 ? arguments[1] : undefined;\n\n  switch (action.type) {\n    case 'DISMISS_TIP':\n      return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__[\"default\"])({}, state, Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, action.id, true));\n\n    case 'ENABLE_TIPS':\n      return {};\n  }\n\n  return state;\n}\nvar preferences = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__[\"combineReducers\"])({\n  areTipsEnabled: areTipsEnabled,\n  dismissedTips: dismissedTips\n});\n/* harmony default export */ __webpack_exports__[\"default\"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__[\"combineReducers\"])({\n  guides: guides,\n  preferences: preferences\n}));\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/nux/build-module/store/reducer.js?");

/***/ }),

/***/ "./node_modules/@wordpress/nux/build-module/store/selectors.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@wordpress/nux/build-module/store/selectors.js ***!
  \*********************************************************************/
/*! exports provided: getAssociatedGuide, isTipVisible, areTipsEnabled */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"getAssociatedGuide\", function() { return getAssociatedGuide; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"isTipVisible\", function() { return isTipVisible; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"areTipsEnabled\", function() { return areTipsEnabled; });\n/* harmony import */ var _babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/slicedToArray */ \"./node_modules/@babel/runtime/helpers/esm/slicedToArray.js\");\n/* harmony import */ var rememo__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! rememo */ \"./node_modules/rememo/es/rememo.js\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);\n\n\n/**\n * External dependencies\n */\n\n\n/**\n * An object containing information about a guide.\n *\n * @typedef {Object} NUX.GuideInfo\n * @property {string[]} tipIds       Which tips the guide contains.\n * @property {?string}  currentTipId The guide's currently showing tip.\n * @property {?string}  nextTipId    The guide's next tip to show.\n */\n\n/**\n * Returns an object describing the guide, if any, that the given tip is a part\n * of.\n *\n * @param {Object} state Global application state.\n * @param {string} tipId The tip to query.\n *\n * @return {?NUX.GuideInfo} Information about the associated guide.\n */\n\nvar getAssociatedGuide = Object(rememo__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(function (state, tipId) {\n  var _iteratorNormalCompletion = true;\n  var _didIteratorError = false;\n  var _iteratorError = undefined;\n\n  try {\n    for (var _iterator = state.guides[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {\n      var tipIds = _step.value;\n\n      if (Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"includes\"])(tipIds, tipId)) {\n        var nonDismissedTips = Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"difference\"])(tipIds, Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"keys\"])(state.preferences.dismissedTips));\n\n        var _nonDismissedTips = Object(_babel_runtime_helpers_esm_slicedToArray__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(nonDismissedTips, 2),\n            _nonDismissedTips$ = _nonDismissedTips[0],\n            currentTipId = _nonDismissedTips$ === void 0 ? null : _nonDismissedTips$,\n            _nonDismissedTips$2 = _nonDismissedTips[1],\n            nextTipId = _nonDismissedTips$2 === void 0 ? null : _nonDismissedTips$2;\n\n        return {\n          tipIds: tipIds,\n          currentTipId: currentTipId,\n          nextTipId: nextTipId\n        };\n      }\n    }\n  } catch (err) {\n    _didIteratorError = true;\n    _iteratorError = err;\n  } finally {\n    try {\n      if (!_iteratorNormalCompletion && _iterator.return != null) {\n        _iterator.return();\n      }\n    } finally {\n      if (_didIteratorError) {\n        throw _iteratorError;\n      }\n    }\n  }\n\n  return null;\n}, function (state) {\n  return [state.guides, state.preferences.dismissedTips];\n});\n/**\n * Determines whether or not the given tip is showing. Tips are hidden if they\n * are disabled, have been dismissed, or are not the current tip in any\n * guide that they have been added to.\n *\n * @param {Object} state Global application state.\n * @param {string} tipId The tip to query.\n *\n * @return {boolean} Whether or not the given tip is showing.\n */\n\nfunction isTipVisible(state, tipId) {\n  if (!state.preferences.areTipsEnabled) {\n    return false;\n  }\n\n  if (state.preferences.dismissedTips[tipId]) {\n    return false;\n  }\n\n  var associatedGuide = getAssociatedGuide(state, tipId);\n\n  if (associatedGuide && associatedGuide.currentTipId !== tipId) {\n    return false;\n  }\n\n  return true;\n}\n/**\n * Returns whether or not tips are globally enabled.\n *\n * @param {Object} state Global application state.\n *\n * @return {boolean} Whether tips are globally enabled.\n */\n\nfunction areTipsEnabled(state) {\n  return state.preferences.areTipsEnabled;\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/nux/build-module/store/selectors.js?");

/***/ }),

/***/ "./node_modules/rememo/es/rememo.js":
/*!******************************************!*\
  !*** ./node_modules/rememo/es/rememo.js ***!
  \******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n\nvar LEAF_KEY, hasWeakMap;\n\n/**\n * Arbitrary value used as key for referencing cache object in WeakMap tree.\n *\n * @type {Object}\n */\nLEAF_KEY = {};\n\n/**\n * Whether environment supports WeakMap.\n *\n * @type {boolean}\n */\nhasWeakMap = typeof WeakMap !== 'undefined';\n\n/**\n * Returns the first argument as the sole entry in an array.\n *\n * @param {*} value Value to return.\n *\n * @return {Array} Value returned as entry in array.\n */\nfunction arrayOf( value ) {\n\treturn [ value ];\n}\n\n/**\n * Returns true if the value passed is object-like, or false otherwise. A value\n * is object-like if it can support property assignment, e.g. object or array.\n *\n * @param {*} value Value to test.\n *\n * @return {boolean} Whether value is object-like.\n */\nfunction isObjectLike( value ) {\n\treturn !! value && 'object' === typeof value;\n}\n\n/**\n * Creates and returns a new cache object.\n *\n * @return {Object} Cache object.\n */\nfunction createCache() {\n\tvar cache = {\n\t\tclear: function() {\n\t\t\tcache.head = null;\n\t\t},\n\t};\n\n\treturn cache;\n}\n\n/**\n * Returns true if entries within the two arrays are strictly equal by\n * reference from a starting index.\n *\n * @param {Array}  a         First array.\n * @param {Array}  b         Second array.\n * @param {number} fromIndex Index from which to start comparison.\n *\n * @return {boolean} Whether arrays are shallowly equal.\n */\nfunction isShallowEqual( a, b, fromIndex ) {\n\tvar i;\n\n\tif ( a.length !== b.length ) {\n\t\treturn false;\n\t}\n\n\tfor ( i = fromIndex; i < a.length; i++ ) {\n\t\tif ( a[ i ] !== b[ i ] ) {\n\t\t\treturn false;\n\t\t}\n\t}\n\n\treturn true;\n}\n\n/**\n * Returns a memoized selector function. The getDependants function argument is\n * called before the memoized selector and is expected to return an immutable\n * reference or array of references on which the selector depends for computing\n * its own return value. The memoize cache is preserved only as long as those\n * dependant references remain the same. If getDependants returns a different\n * reference(s), the cache is cleared and the selector value regenerated.\n *\n * @param {Function} selector      Selector function.\n * @param {Function} getDependants Dependant getter returning an immutable\n *                                 reference or array of reference used in\n *                                 cache bust consideration.\n *\n * @return {Function} Memoized selector.\n */\n/* harmony default export */ __webpack_exports__[\"default\"] = (function( selector, getDependants ) {\n\tvar rootCache, getCache;\n\n\t// Use object source as dependant if getter not provided\n\tif ( ! getDependants ) {\n\t\tgetDependants = arrayOf;\n\t}\n\n\t/**\n\t * Returns the root cache. If WeakMap is supported, this is assigned to the\n\t * root WeakMap cache set, otherwise it is a shared instance of the default\n\t * cache object.\n\t *\n\t * @return {(WeakMap|Object)} Root cache object.\n\t */\n\tfunction getRootCache() {\n\t\treturn rootCache;\n\t}\n\n\t/**\n\t * Returns the cache for a given dependants array. When possible, a WeakMap\n\t * will be used to create a unique cache for each set of dependants. This\n\t * is feasible due to the nature of WeakMap in allowing garbage collection\n\t * to occur on entries where the key object is no longer referenced. Since\n\t * WeakMap requires the key to be an object, this is only possible when the\n\t * dependant is object-like. The root cache is created as a hierarchy where\n\t * each top-level key is the first entry in a dependants set, the value a\n\t * WeakMap where each key is the next dependant, and so on. This continues\n\t * so long as the dependants are object-like. If no dependants are object-\n\t * like, then the cache is shared across all invocations.\n\t *\n\t * @see isObjectLike\n\t *\n\t * @param {Array} dependants Selector dependants.\n\t *\n\t * @return {Object} Cache object.\n\t */\n\tfunction getWeakMapCache( dependants ) {\n\t\tvar caches = rootCache,\n\t\t\tisUniqueByDependants = true,\n\t\t\ti, dependant, map, cache;\n\n\t\tfor ( i = 0; i < dependants.length; i++ ) {\n\t\t\tdependant = dependants[ i ];\n\n\t\t\t// Can only compose WeakMap from object-like key.\n\t\t\tif ( ! isObjectLike( dependant ) ) {\n\t\t\t\tisUniqueByDependants = false;\n\t\t\t\tbreak;\n\t\t\t}\n\n\t\t\t// Does current segment of cache already have a WeakMap?\n\t\t\tif ( caches.has( dependant ) ) {\n\t\t\t\t// Traverse into nested WeakMap.\n\t\t\t\tcaches = caches.get( dependant );\n\t\t\t} else {\n\t\t\t\t// Create, set, and traverse into a new one.\n\t\t\t\tmap = new WeakMap();\n\t\t\t\tcaches.set( dependant, map );\n\t\t\t\tcaches = map;\n\t\t\t}\n\t\t}\n\n\t\t// We use an arbitrary (but consistent) object as key for the last item\n\t\t// in the WeakMap to serve as our running cache.\n\t\tif ( ! caches.has( LEAF_KEY ) ) {\n\t\t\tcache = createCache();\n\t\t\tcache.isUniqueByDependants = isUniqueByDependants;\n\t\t\tcaches.set( LEAF_KEY, cache );\n\t\t}\n\n\t\treturn caches.get( LEAF_KEY );\n\t}\n\n\t// Assign cache handler by availability of WeakMap\n\tgetCache = hasWeakMap ? getWeakMapCache : getRootCache;\n\n\t/**\n\t * Resets root memoization cache.\n\t */\n\tfunction clear() {\n\t\trootCache = hasWeakMap ? new WeakMap() : createCache();\n\t}\n\n\t// eslint-disable-next-line jsdoc/check-param-names\n\t/**\n\t * The augmented selector call, considering first whether dependants have\n\t * changed before passing it to underlying memoize function.\n\t *\n\t * @param {Object} source    Source object for derivation.\n\t * @param {...*}   extraArgs Additional arguments to pass to selector.\n\t *\n\t * @return {*} Selector result.\n\t */\n\tfunction callSelector( /* source, ...extraArgs */ ) {\n\t\tvar len = arguments.length,\n\t\t\tcache, node, i, args, dependants;\n\n\t\t// Create copy of arguments (avoid leaking deoptimization).\n\t\targs = new Array( len );\n\t\tfor ( i = 0; i < len; i++ ) {\n\t\t\targs[ i ] = arguments[ i ];\n\t\t}\n\n\t\tdependants = getDependants.apply( null, args );\n\t\tcache = getCache( dependants );\n\n\t\t// If not guaranteed uniqueness by dependants (primitive type or lack\n\t\t// of WeakMap support), shallow compare against last dependants and, if\n\t\t// references have changed, destroy cache to recalculate result.\n\t\tif ( ! cache.isUniqueByDependants ) {\n\t\t\tif ( cache.lastDependants && ! isShallowEqual( dependants, cache.lastDependants, 0 ) ) {\n\t\t\t\tcache.clear();\n\t\t\t}\n\n\t\t\tcache.lastDependants = dependants;\n\t\t}\n\n\t\tnode = cache.head;\n\t\twhile ( node ) {\n\t\t\t// Check whether node arguments match arguments\n\t\t\tif ( ! isShallowEqual( node.args, args, 1 ) ) {\n\t\t\t\tnode = node.next;\n\t\t\t\tcontinue;\n\t\t\t}\n\n\t\t\t// At this point we can assume we've found a match\n\n\t\t\t// Surface matched node to head if not already\n\t\t\tif ( node !== cache.head ) {\n\t\t\t\t// Adjust siblings to point to each other.\n\t\t\t\tnode.prev.next = node.next;\n\t\t\t\tif ( node.next ) {\n\t\t\t\t\tnode.next.prev = node.prev;\n\t\t\t\t}\n\n\t\t\t\tnode.next = cache.head;\n\t\t\t\tnode.prev = null;\n\t\t\t\tcache.head.prev = node;\n\t\t\t\tcache.head = node;\n\t\t\t}\n\n\t\t\t// Return immediately\n\t\t\treturn node.val;\n\t\t}\n\n\t\t// No cached value found. Continue to insertion phase:\n\n\t\tnode = {\n\t\t\t// Generate the result from original function\n\t\t\tval: selector.apply( null, args ),\n\t\t};\n\n\t\t// Avoid including the source object in the cache.\n\t\targs[ 0 ] = null;\n\t\tnode.args = args;\n\n\t\t// Don't need to check whether node is already head, since it would\n\t\t// have been returned above already if it was\n\n\t\t// Shift existing head down list\n\t\tif ( cache.head ) {\n\t\t\tcache.head.prev = node;\n\t\t\tnode.next = cache.head;\n\t\t}\n\n\t\tcache.head = node;\n\n\t\treturn node.val;\n\t}\n\n\tcallSelector.getDependants = getDependants;\n\tcallSelector.clear = clear;\n\tclear();\n\n\treturn callSelector;\n});\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/rememo/es/rememo.js?");

/***/ }),

/***/ "@wordpress/components":
/*!*********************************************!*\
  !*** external {"this":["wp","components"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"components\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22components%22%5D%7D?");

/***/ }),

/***/ "@wordpress/compose":
/*!******************************************!*\
  !*** external {"this":["wp","compose"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"compose\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22compose%22%5D%7D?");

/***/ }),

/***/ "@wordpress/data":
/*!***************************************!*\
  !*** external {"this":["wp","data"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"data\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22data%22%5D%7D?");

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"element\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22element%22%5D%7D?");

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"i18n\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22i18n%22%5D%7D?");

/***/ }),

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"lodash\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%22lodash%22?");

/***/ })

/******/ });