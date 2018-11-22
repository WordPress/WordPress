this["wp"] = this["wp"] || {}; this["wp"]["viewport"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/viewport/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _arrayWithoutHoles; });\nfunction _arrayWithoutHoles(arr) {\n  if (Array.isArray(arr)) {\n    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {\n      arr2[i] = arr[i];\n    }\n\n    return arr2;\n  }\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js?");

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

/***/ "./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _nonIterableSpread; });\nfunction _nonIterableSpread() {\n  throw new TypeError(\"Invalid attempt to spread non-iterable instance\");\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js?");

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

/***/ "./node_modules/@wordpress/viewport/build-module/if-viewport-matches.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@wordpress/viewport/build-module/if-viewport-matches.js ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/compose */ \"@wordpress/compose\");\n/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _with_viewport_match__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./with-viewport-match */ \"./node_modules/@wordpress/viewport/build-module/with-viewport-match.js\");\n/**\n * WordPress dependencies\n */\n\n/**\n * Internal dependencies\n */\n\n\n/**\n * Higher-order component creator, creating a new component which renders if\n * the viewport query is satisfied.\n *\n * @param {string} query Viewport query.\n *\n * @see withViewportMatches\n *\n * @return {Function} Higher-order component.\n */\n\nvar ifViewportMatches = function ifViewportMatches(query) {\n  return Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_0__[\"createHigherOrderComponent\"])(Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_0__[\"compose\"])([Object(_with_viewport_match__WEBPACK_IMPORTED_MODULE_1__[\"default\"])({\n    isViewportMatch: query\n  }), Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_0__[\"ifCondition\"])(function (props) {\n    return props.isViewportMatch;\n  })]), 'ifViewportMatches');\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (ifViewportMatches);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/viewport/build-module/if-viewport-matches.js?");

/***/ }),

/***/ "./node_modules/@wordpress/viewport/build-module/index.js":
/*!****************************************************************!*\
  !*** ./node_modules/@wordpress/viewport/build-module/index.js ***!
  \****************************************************************/
/*! exports provided: ifViewportMatches, withViewportMatch */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ \"@wordpress/data\");\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./store */ \"./node_modules/@wordpress/viewport/build-module/store/index.js\");\n/* harmony import */ var _if_viewport_matches__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./if-viewport-matches */ \"./node_modules/@wordpress/viewport/build-module/if-viewport-matches.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"ifViewportMatches\", function() { return _if_viewport_matches__WEBPACK_IMPORTED_MODULE_3__[\"default\"]; });\n\n/* harmony import */ var _with_viewport_match__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./with-viewport-match */ \"./node_modules/@wordpress/viewport/build-module/with-viewport-match.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"withViewportMatch\", function() { return _with_viewport_match__WEBPACK_IMPORTED_MODULE_4__[\"default\"]; });\n\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n/**\n * Internal dependencies\n */\n\n\n\n\n/**\n * Hash of breakpoint names with pixel width at which it becomes effective.\n *\n * @see _breakpoints.scss\n *\n * @type {Object}\n */\n\nvar BREAKPOINTS = {\n  huge: 1440,\n  wide: 1280,\n  large: 960,\n  medium: 782,\n  small: 600,\n  mobile: 480\n};\n/**\n * Hash of query operators with corresponding condition for media query.\n *\n * @type {Object}\n */\n\nvar OPERATORS = {\n  '<': 'max-width',\n  '>=': 'min-width'\n};\n/**\n * Callback invoked when media query state should be updated. Is invoked a\n * maximum of one time per call stack.\n */\n\nvar setIsMatching = Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"debounce\"])(function () {\n  var values = Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"mapValues\"])(queries, function (query) {\n    return query.matches;\n  });\n  Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__[\"dispatch\"])('core/viewport').setIsMatching(values);\n}, {\n  leading: true\n});\n/**\n * Hash of breakpoint names with generated MediaQueryList for corresponding\n * media query.\n *\n * @see https://developer.mozilla.org/en-US/docs/Web/API/Window/matchMedia\n * @see https://developer.mozilla.org/en-US/docs/Web/API/MediaQueryList\n *\n * @type {Object<string,MediaQueryList>}\n */\n\nvar queries = Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"reduce\"])(BREAKPOINTS, function (result, width, name) {\n  Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"forEach\"])(OPERATORS, function (condition, operator) {\n    var list = window.matchMedia(\"(\".concat(condition, \": \").concat(width, \"px)\"));\n    list.addListener(setIsMatching);\n    var key = [operator, name].join(' ');\n    result[key] = list;\n  });\n  return result;\n}, {});\nwindow.addEventListener('orientationchange', setIsMatching); // Set initial values\n\nsetIsMatching();\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/viewport/build-module/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/viewport/build-module/store/actions.js":
/*!************************************************************************!*\
  !*** ./node_modules/@wordpress/viewport/build-module/store/actions.js ***!
  \************************************************************************/
/*! exports provided: setIsMatching */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"setIsMatching\", function() { return setIsMatching; });\n/**\n * Returns an action object used in signalling that viewport queries have been\n * updated. Values are specified as an object of breakpoint query keys where\n * value represents whether query matches.\n *\n * @param {Object} values Breakpoint query matches.\n *\n * @return {Object} Action object.\n */\nfunction setIsMatching(values) {\n  return {\n    type: 'SET_IS_MATCHING',\n    values: values\n  };\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/viewport/build-module/store/actions.js?");

/***/ }),

/***/ "./node_modules/@wordpress/viewport/build-module/store/index.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@wordpress/viewport/build-module/store/index.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ \"@wordpress/data\");\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _reducer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./reducer */ \"./node_modules/@wordpress/viewport/build-module/store/reducer.js\");\n/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./actions */ \"./node_modules/@wordpress/viewport/build-module/store/actions.js\");\n/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./selectors */ \"./node_modules/@wordpress/viewport/build-module/store/selectors.js\");\n/**\n * WordPress dependencies\n */\n\n/**\n * Internal dependencies\n */\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__[\"registerStore\"])('core/viewport', {\n  reducer: _reducer__WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  actions: _actions__WEBPACK_IMPORTED_MODULE_2__,\n  selectors: _selectors__WEBPACK_IMPORTED_MODULE_3__\n}));\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/viewport/build-module/store/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/viewport/build-module/store/reducer.js":
/*!************************************************************************!*\
  !*** ./node_modules/@wordpress/viewport/build-module/store/reducer.js ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/**\n * Reducer returning the viewport state, as keys of breakpoint queries with\n * boolean value representing whether query is matched.\n *\n * @param {Object} state  Current state.\n * @param {Object} action Dispatched action.\n *\n * @return {Object} Updated state.\n */\nfunction reducer() {\n  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};\n  var action = arguments.length > 1 ? arguments[1] : undefined;\n\n  switch (action.type) {\n    case 'SET_IS_MATCHING':\n      return action.values;\n  }\n\n  return state;\n}\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (reducer);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/viewport/build-module/store/reducer.js?");

/***/ }),

/***/ "./node_modules/@wordpress/viewport/build-module/store/selectors.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/viewport/build-module/store/selectors.js ***!
  \**************************************************************************/
/*! exports provided: isViewportMatch */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"isViewportMatch\", function() { return isViewportMatch; });\n/* harmony import */ var _babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/toConsumableArray */ \"./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);\n\n\n/**\n * External dependencies\n */\n\n/**\n * Returns true if the viewport matches the given query, or false otherwise.\n *\n * @param {Object} state Viewport state object.\n * @param {string} query Query string. Includes operator and breakpoint name,\n *                       space separated. Operator defaults to >=.\n *\n * @example\n *\n * ```js\n * isViewportMatch( state, '< huge' );\n * isViewPortMatch( state, 'medium' );\n * ```\n *\n * @return {boolean} Whether viewport matches query.\n */\n\nfunction isViewportMatch(state, query) {\n  // Pad to _at least_ two elements to take from the right, effectively\n  // defaulting the left-most value.\n  var key = Object(lodash__WEBPACK_IMPORTED_MODULE_1__[\"takeRight\"])(['>='].concat(Object(_babel_runtime_helpers_esm_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(query.split(' '))), 2).join(' ');\n  return !!state[key];\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/viewport/build-module/store/selectors.js?");

/***/ }),

/***/ "./node_modules/@wordpress/viewport/build-module/with-viewport-match.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@wordpress/viewport/build-module/with-viewport-match.js ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/compose */ \"@wordpress/compose\");\n/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ \"@wordpress/data\");\n/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n\n/**\n * Higher-order component creator, creating a new component which renders with\n * the given prop names, where the value passed to the underlying component is\n * the result of the query assigned as the object's value.\n *\n * @param {Object} queries  Object of prop name to viewport query.\n *\n * @see isViewportMatch\n *\n * @return {Function} Higher-order component.\n */\n\nvar withViewportMatch = function withViewportMatch(queries) {\n  return Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_1__[\"createHigherOrderComponent\"])(Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__[\"withSelect\"])(function (select) {\n    return Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"mapValues\"])(queries, function (query) {\n      return select('core/viewport').isViewportMatch(query);\n    });\n  }), 'withViewportMatch');\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (withViewportMatch);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/viewport/build-module/with-viewport-match.js?");

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

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"lodash\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%22lodash%22?");

/***/ })

/******/ });