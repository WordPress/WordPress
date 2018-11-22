this["wp"] = this["wp"] || {}; this["wp"]["deprecated"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/deprecated/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@wordpress/deprecated/build-module/index.js":
/*!******************************************************************!*\
  !*** ./node_modules/@wordpress/deprecated/build-module/index.js ***!
  \******************************************************************/
/*! exports provided: logged, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"logged\", function() { return logged; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return deprecated; });\n/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/hooks */ \"@wordpress/hooks\");\n/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__);\n/**\n * WordPress dependencies\n */\n\n/**\n * Object map tracking messages which have been logged, for use in ensuring a\n * message is only logged once.\n *\n * @type {Object}\n */\n\nvar logged = Object.create(null);\n/**\n * Logs a message to notify developers about a deprecated feature.\n *\n * @param {string}  feature             Name of the deprecated feature.\n * @param {?Object} options             Personalisation options\n * @param {?string} options.version     Version in which the feature will be removed.\n * @param {?string} options.alternative Feature to use instead\n * @param {?string} options.plugin      Plugin name if it's a plugin feature\n * @param {?string} options.link        Link to documentation\n * @param {?string} options.hint        Additional message to help transition away from the deprecated feature.\n */\n\nfunction deprecated(feature) {\n  var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};\n  var version = options.version,\n      alternative = options.alternative,\n      plugin = options.plugin,\n      link = options.link,\n      hint = options.hint;\n  var pluginMessage = plugin ? \" from \".concat(plugin) : '';\n  var versionMessage = version ? \"\".concat(pluginMessage, \" in \").concat(version) : '';\n  var useInsteadMessage = alternative ? \" Please use \".concat(alternative, \" instead.\") : '';\n  var linkMessage = link ? \" See: \".concat(link) : '';\n  var hintMessage = hint ? \" Note: \".concat(hint) : '';\n  var message = \"\".concat(feature, \" is deprecated and will be removed\").concat(versionMessage, \".\").concat(useInsteadMessage).concat(linkMessage).concat(hintMessage); // Skip if already logged.\n\n  if (message in logged) {\n    return;\n  }\n  /**\n   * Fires whenever a deprecated feature is encountered\n   *\n   * @param {string}  feature             Name of the deprecated feature.\n   * @param {?Object} options             Personalisation options\n   * @param {?string} options.version     Version in which the feature will be removed.\n   * @param {?string} options.alternative Feature to use instead\n   * @param {?string} options.plugin      Plugin name if it's a plugin feature\n   * @param {?string} options.link        Link to documentation\n   * @param {?string} options.hint        Additional message to help transition away from the deprecated feature.\n   * @param {?string} message             Message sent to console.warn\n   */\n\n\n  Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__[\"doAction\"])('deprecated', feature, options, message); // eslint-disable-next-line no-console\n\n  console.warn(message);\n  logged[message] = true;\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/deprecated/build-module/index.js?");

/***/ }),

/***/ "@wordpress/hooks":
/*!****************************************!*\
  !*** external {"this":["wp","hooks"]} ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"hooks\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22hooks%22%5D%7D?");

/***/ })

/******/ })["default"];