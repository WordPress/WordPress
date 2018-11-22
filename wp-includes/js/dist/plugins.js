this["wp"] = this["wp"] || {}; this["wp"]["plugins"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/plugins/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _assertThisInitialized; });\nfunction _assertThisInitialized(self) {\n  if (self === void 0) {\n    throw new ReferenceError(\"this hasn't been initialised - super() hasn't been called\");\n  }\n\n  return self;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _classCallCheck; });\nfunction _classCallCheck(instance, Constructor) {\n  if (!(instance instanceof Constructor)) {\n    throw new TypeError(\"Cannot call a class as a function\");\n  }\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/classCallCheck.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/createClass.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/createClass.js ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _createClass; });\nfunction _defineProperties(target, props) {\n  for (var i = 0; i < props.length; i++) {\n    var descriptor = props[i];\n    descriptor.enumerable = descriptor.enumerable || false;\n    descriptor.configurable = true;\n    if (\"value\" in descriptor) descriptor.writable = true;\n    Object.defineProperty(target, descriptor.key, descriptor);\n  }\n}\n\nfunction _createClass(Constructor, protoProps, staticProps) {\n  if (protoProps) _defineProperties(Constructor.prototype, protoProps);\n  if (staticProps) _defineProperties(Constructor, staticProps);\n  return Constructor;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/createClass.js?");

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

/***/ "./node_modules/@babel/runtime/helpers/esm/extends.js":
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/extends.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _extends; });\nfunction _extends() {\n  _extends = Object.assign || function (target) {\n    for (var i = 1; i < arguments.length; i++) {\n      var source = arguments[i];\n\n      for (var key in source) {\n        if (Object.prototype.hasOwnProperty.call(source, key)) {\n          target[key] = source[key];\n        }\n      }\n    }\n\n    return target;\n  };\n\n  return _extends.apply(this, arguments);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/extends.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _getPrototypeOf; });\nfunction _getPrototypeOf(o) {\n  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {\n    return o.__proto__ || Object.getPrototypeOf(o);\n  };\n  return _getPrototypeOf(o);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/inherits.js":
/*!*************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/inherits.js ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _inherits; });\n/* harmony import */ var _setPrototypeOf__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./setPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js\");\n\nfunction _inherits(subClass, superClass) {\n  if (typeof superClass !== \"function\" && superClass !== null) {\n    throw new TypeError(\"Super expression must either be null or a function\");\n  }\n\n  subClass.prototype = Object.create(superClass && superClass.prototype, {\n    constructor: {\n      value: subClass,\n      writable: true,\n      configurable: true\n    }\n  });\n  if (superClass) Object(_setPrototypeOf__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(subClass, superClass);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/inherits.js?");

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

/***/ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _possibleConstructorReturn; });\n/* harmony import */ var _helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../helpers/esm/typeof */ \"./node_modules/@babel/runtime/helpers/esm/typeof.js\");\n/* harmony import */ var _assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./assertThisInitialized */ \"./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js\");\n\n\nfunction _possibleConstructorReturn(self, call) {\n  if (call && (Object(_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(call) === \"object\" || typeof call === \"function\")) {\n    return call;\n  }\n\n  return Object(_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(self);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _setPrototypeOf; });\nfunction _setPrototypeOf(o, p) {\n  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {\n    o.__proto__ = p;\n    return o;\n  };\n\n  return _setPrototypeOf(o, p);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/typeof.js":
/*!***********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/typeof.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _typeof; });\nfunction _typeof2(obj) { if (typeof Symbol === \"function\" && typeof Symbol.iterator === \"symbol\") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === \"function\" && obj.constructor === Symbol && obj !== Symbol.prototype ? \"symbol\" : typeof obj; }; } return _typeof2(obj); }\n\nfunction _typeof(obj) {\n  if (typeof Symbol === \"function\" && _typeof2(Symbol.iterator) === \"symbol\") {\n    _typeof = function _typeof(obj) {\n      return _typeof2(obj);\n    };\n  } else {\n    _typeof = function _typeof(obj) {\n      return obj && typeof Symbol === \"function\" && obj.constructor === Symbol && obj !== Symbol.prototype ? \"symbol\" : _typeof2(obj);\n    };\n  }\n\n  return _typeof(obj);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/typeof.js?");

/***/ }),

/***/ "./node_modules/@wordpress/plugins/build-module/api/index.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@wordpress/plugins/build-module/api/index.js ***!
  \*******************************************************************/
/*! exports provided: registerPlugin, unregisterPlugin, getPlugin, getPlugins */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"registerPlugin\", function() { return registerPlugin; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"unregisterPlugin\", function() { return unregisterPlugin; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"getPlugin\", function() { return getPlugin; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"getPlugins\", function() { return getPlugins; });\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/typeof */ \"./node_modules/@babel/runtime/helpers/esm/typeof.js\");\n/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/hooks */ \"@wordpress/hooks\");\n/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);\n\n\n\n/* eslint no-console: [ 'error', { allow: [ 'error' ] } ] */\n\n/**\n * WordPress dependencies\n */\n\n/**\n * External dependencies\n */\n\n\n/**\n * Plugin definitions keyed by plugin name.\n *\n * @type {Object.<string,WPPlugin>}\n */\n\nvar plugins = {};\n/**\n * Registers a plugin to the editor.\n *\n * @param {string}                    name            The name of the plugin.\n * @param {Object}                    settings        The settings for this plugin.\n * @param {Function}                  settings.render The function that renders the plugin.\n * @param {string|WPElement|Function} settings.icon   An icon to be shown in the UI.\n *\n * @return {Object} The final plugin settings object.\n */\n\nfunction registerPlugin(name, settings) {\n  if (Object(_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(settings) !== 'object') {\n    console.error('No settings object provided!');\n    return null;\n  }\n\n  if (typeof name !== 'string') {\n    console.error('Plugin names must be strings.');\n    return null;\n  }\n\n  if (!/^[a-z][a-z0-9-]*$/.test(name)) {\n    console.error('Plugin names must include only lowercase alphanumeric characters or dashes, and start with a letter. Example: \"my-plugin\".');\n    return null;\n  }\n\n  if (plugins[name]) {\n    console.error(\"Plugin \\\"\".concat(name, \"\\\" is already registered.\"));\n  }\n\n  settings = Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__[\"applyFilters\"])('plugins.registerPlugin', settings, name);\n\n  if (!Object(lodash__WEBPACK_IMPORTED_MODULE_3__[\"isFunction\"])(settings.render)) {\n    console.error('The \"render\" property must be specified and must be a valid function.');\n    return null;\n  }\n\n  plugins[name] = Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({\n    name: name,\n    icon: 'admin-plugins'\n  }, settings);\n  Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__[\"doAction\"])('plugins.pluginRegistered', settings, name);\n  return settings;\n}\n/**\n * Unregisters a plugin by name.\n *\n * @param {string} name Plugin name.\n *\n * @return {?WPPlugin} The previous plugin settings object, if it has been\n *                     successfully unregistered; otherwise `undefined`.\n */\n\nfunction unregisterPlugin(name) {\n  if (!plugins[name]) {\n    console.error('Plugin \"' + name + '\" is not registered.');\n    return;\n  }\n\n  var oldPlugin = plugins[name];\n  delete plugins[name];\n  Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__[\"doAction\"])('plugins.pluginUnregistered', oldPlugin, name);\n  return oldPlugin;\n}\n/**\n * Returns a registered plugin settings.\n *\n * @param {string} name Plugin name.\n *\n * @return {?Object} Plugin setting.\n */\n\nfunction getPlugin(name) {\n  return plugins[name];\n}\n/**\n * Returns all registered plugins.\n *\n * @return {Array} Plugin settings.\n */\n\nfunction getPlugins() {\n  return Object.values(plugins);\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/plugins/build-module/api/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/plugins/build-module/components/index.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/plugins/build-module/components/index.js ***!
  \**************************************************************************/
/*! exports provided: PluginArea, withPluginContext */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _plugin_area__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./plugin-area */ \"./node_modules/@wordpress/plugins/build-module/components/plugin-area/index.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"PluginArea\", function() { return _plugin_area__WEBPACK_IMPORTED_MODULE_0__[\"default\"]; });\n\n/* harmony import */ var _plugin_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./plugin-context */ \"./node_modules/@wordpress/plugins/build-module/components/plugin-context/index.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"withPluginContext\", function() { return _plugin_context__WEBPACK_IMPORTED_MODULE_1__[\"withPluginContext\"]; });\n\n\n\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/plugins/build-module/components/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/plugins/build-module/components/plugin-area/index.js":
/*!**************************************************************************************!*\
  !*** ./node_modules/@wordpress/plugins/build-module/components/plugin-area/index.js ***!
  \**************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/possibleConstructorReturn */ \"./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/getPrototypeOf */ \"./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inherits */ \"./node_modules/@babel/runtime/helpers/esm/inherits.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ \"./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/hooks */ \"@wordpress/hooks\");\n/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var _plugin_context__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../plugin-context */ \"./node_modules/@wordpress/plugins/build-module/components/plugin-context/index.js\");\n/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../api */ \"./node_modules/@wordpress/plugins/build-module/api/index.js\");\n\n\n\n\n\n\n\n\n/**\n * External dependencies\n */\n\n/**\n * WordPress dependencies\n */\n\n\n\n/**\n * Internal dependencies\n */\n\n\n\n/**\n * A component that renders all plugin fills in a hidden div.\n *\n * @return {WPElement} Plugin area.\n */\n\nvar PluginArea =\n/*#__PURE__*/\nfunction (_Component) {\n  Object(_babel_runtime_helpers_esm_inherits__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(PluginArea, _Component);\n\n  function PluginArea() {\n    var _this;\n\n    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(this, PluginArea);\n\n    _this = Object(_babel_runtime_helpers_esm_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(this, Object(_babel_runtime_helpers_esm_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(PluginArea).apply(this, arguments));\n    _this.setPlugins = _this.setPlugins.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__[\"default\"])(_this)));\n    _this.state = _this.getCurrentPluginsState();\n    return _this;\n  }\n\n  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(PluginArea, [{\n    key: \"getCurrentPluginsState\",\n    value: function getCurrentPluginsState() {\n      return {\n        plugins: Object(lodash__WEBPACK_IMPORTED_MODULE_7__[\"map\"])(Object(_api__WEBPACK_IMPORTED_MODULE_10__[\"getPlugins\"])(), function (_ref) {\n          var icon = _ref.icon,\n              name = _ref.name,\n              render = _ref.render;\n          return {\n            Plugin: render,\n            context: {\n              name: name,\n              icon: icon\n            }\n          };\n        })\n      };\n    }\n  }, {\n    key: \"componentDidMount\",\n    value: function componentDidMount() {\n      Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_8__[\"addAction\"])('plugins.pluginRegistered', 'core/plugins/plugin-area/plugins-registered', this.setPlugins);\n      Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_8__[\"addAction\"])('plugins.pluginUnregistered', 'core/plugins/plugin-area/plugins-unregistered', this.setPlugins);\n    }\n  }, {\n    key: \"componentWillUnmount\",\n    value: function componentWillUnmount() {\n      Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_8__[\"removeAction\"])('plugins.pluginRegistered', 'core/plugins/plugin-area/plugins-registered');\n      Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_8__[\"removeAction\"])('plugins.pluginUnregistered', 'core/plugins/plugin-area/plugins-unregistered');\n    }\n  }, {\n    key: \"setPlugins\",\n    value: function setPlugins() {\n      this.setState(this.getCurrentPluginsState);\n    }\n  }, {\n    key: \"render\",\n    value: function render() {\n      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(\"div\", {\n        style: {\n          display: 'none'\n        }\n      }, Object(lodash__WEBPACK_IMPORTED_MODULE_7__[\"map\"])(this.state.plugins, function (_ref2) {\n        var context = _ref2.context,\n            Plugin = _ref2.Plugin;\n        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(_plugin_context__WEBPACK_IMPORTED_MODULE_9__[\"PluginContextProvider\"], {\n          key: context.name,\n          value: context\n        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"createElement\"])(Plugin, null));\n      }));\n    }\n  }]);\n\n  return PluginArea;\n}(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__[\"Component\"]);\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (PluginArea);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/plugins/build-module/components/plugin-area/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/plugins/build-module/components/plugin-context/index.js":
/*!*****************************************************************************************!*\
  !*** ./node_modules/@wordpress/plugins/build-module/components/plugin-context/index.js ***!
  \*****************************************************************************************/
/*! exports provided: PluginContextProvider, withPluginContext */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"PluginContextProvider\", function() { return Provider; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"withPluginContext\", function() { return withPluginContext; });\n/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ \"./node_modules/@babel/runtime/helpers/esm/extends.js\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/compose */ \"@wordpress/compose\");\n/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__);\n\n\n\n/**\n * WordPress dependencies\n */\n\n\n\nvar _createContext = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__[\"createContext\"])({\n  name: null,\n  icon: null\n}),\n    Consumer = _createContext.Consumer,\n    Provider = _createContext.Provider;\n\n\n/**\n * A Higher Order Component used to inject Plugin context to the\n * wrapped component.\n *\n * @param {Function} mapContextToProps Function called on every context change,\n *                                     expected to return object of props to\n *                                     merge with the component's own props.\n *\n * @return {Component} Enhanced component with injected context as props.\n */\n\nvar withPluginContext = function withPluginContext(mapContextToProps) {\n  return Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__[\"createHigherOrderComponent\"])(function (OriginalComponent) {\n    return function (props) {\n      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__[\"createElement\"])(Consumer, null, function (context) {\n        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__[\"createElement\"])(OriginalComponent, Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, props, mapContextToProps(context, props)));\n      });\n    };\n  }, 'withPluginContext');\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/plugins/build-module/components/plugin-context/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/plugins/build-module/index.js":
/*!***************************************************************!*\
  !*** ./node_modules/@wordpress/plugins/build-module/index.js ***!
  \***************************************************************/
/*! exports provided: PluginArea, withPluginContext, registerPlugin, unregisterPlugin, getPlugin, getPlugins */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components */ \"./node_modules/@wordpress/plugins/build-module/components/index.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"PluginArea\", function() { return _components__WEBPACK_IMPORTED_MODULE_0__[\"PluginArea\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"withPluginContext\", function() { return _components__WEBPACK_IMPORTED_MODULE_0__[\"withPluginContext\"]; });\n\n/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./api */ \"./node_modules/@wordpress/plugins/build-module/api/index.js\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"registerPlugin\", function() { return _api__WEBPACK_IMPORTED_MODULE_1__[\"registerPlugin\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"unregisterPlugin\", function() { return _api__WEBPACK_IMPORTED_MODULE_1__[\"unregisterPlugin\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"getPlugin\", function() { return _api__WEBPACK_IMPORTED_MODULE_1__[\"getPlugin\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"getPlugins\", function() { return _api__WEBPACK_IMPORTED_MODULE_1__[\"getPlugins\"]; });\n\n\n\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/plugins/build-module/index.js?");

/***/ }),

/***/ "@wordpress/compose":
/*!******************************************!*\
  !*** external {"this":["wp","compose"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"compose\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22compose%22%5D%7D?");

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"element\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22element%22%5D%7D?");

/***/ }),

/***/ "@wordpress/hooks":
/*!****************************************!*\
  !*** external {"this":["wp","hooks"]} ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"hooks\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22hooks%22%5D%7D?");

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