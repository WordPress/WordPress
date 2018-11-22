this["wp"] = this["wp"] || {}; this["wp"]["a11y"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/a11y/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@wordpress/a11y/build-module/addContainer.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@wordpress/a11y/build-module/addContainer.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/**\n * Build the live regions markup.\n *\n * @param {string} ariaLive Optional. Value for the 'aria-live' attribute, default 'polite'.\n *\n * @return {Object} $container The ARIA live region jQuery object.\n */\nvar addContainer = function addContainer(ariaLive) {\n  ariaLive = ariaLive || 'polite';\n  var container = document.createElement('div');\n  container.id = 'a11y-speak-' + ariaLive;\n  container.className = 'a11y-speak-region';\n  container.setAttribute('style', 'position: absolute;' + 'margin: -1px;' + 'padding: 0;' + 'height: 1px;' + 'width: 1px;' + 'overflow: hidden;' + 'clip: rect(1px, 1px, 1px, 1px);' + '-webkit-clip-path: inset(50%);' + 'clip-path: inset(50%);' + 'border: 0;' + 'word-wrap: normal !important;');\n  container.setAttribute('aria-live', ariaLive);\n  container.setAttribute('aria-relevant', 'additions text');\n  container.setAttribute('aria-atomic', 'true');\n  document.querySelector('body').appendChild(container);\n  return container;\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (addContainer);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/a11y/build-module/addContainer.js?");

/***/ }),

/***/ "./node_modules/@wordpress/a11y/build-module/clear.js":
/*!************************************************************!*\
  !*** ./node_modules/@wordpress/a11y/build-module/clear.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/**\n * Clear the a11y-speak-region elements.\n */\nvar clear = function clear() {\n  var regions = document.querySelectorAll('.a11y-speak-region');\n\n  for (var i = 0; i < regions.length; i++) {\n    regions[i].textContent = '';\n  }\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (clear);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/a11y/build-module/clear.js?");

/***/ }),

/***/ "./node_modules/@wordpress/a11y/build-module/filterMessage.js":
/*!********************************************************************!*\
  !*** ./node_modules/@wordpress/a11y/build-module/filterMessage.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\nvar previousMessage = '';\n/**\n * Filter the message to be announced to the screenreader.\n *\n * @param {string} message The message to be announced.\n *\n * @return {string} The filtered message.\n */\n\nvar filterMessage = function filterMessage(message) {\n  /*\n   * Strip HTML tags (if any) from the message string. Ideally, messages should\n   * be simple strings, carefully crafted for specific use with A11ySpeak.\n   * When re-using already existing strings this will ensure simple HTML to be\n   * stripped out and replaced with a space. Browsers will collapse multiple\n   * spaces natively.\n   */\n  message = message.replace(/<[^<>]+>/g, ' ');\n\n  if (previousMessage === message) {\n    message += \"\\xA0\";\n  }\n\n  previousMessage = message;\n  return message;\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (filterMessage);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/a11y/build-module/filterMessage.js?");

/***/ }),

/***/ "./node_modules/@wordpress/a11y/build-module/index.js":
/*!************************************************************!*\
  !*** ./node_modules/@wordpress/a11y/build-module/index.js ***!
  \************************************************************/
/*! exports provided: setup, speak */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"setup\", function() { return setup; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"speak\", function() { return speak; });\n/* harmony import */ var _addContainer__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./addContainer */ \"./node_modules/@wordpress/a11y/build-module/addContainer.js\");\n/* harmony import */ var _clear__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./clear */ \"./node_modules/@wordpress/a11y/build-module/clear.js\");\n/* harmony import */ var _wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/dom-ready */ \"@wordpress/dom-ready\");\n/* harmony import */ var _wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _filterMessage__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./filterMessage */ \"./node_modules/@wordpress/a11y/build-module/filterMessage.js\");\n\n\n\n\n/**\n * Create the live regions.\n */\n\nvar setup = function setup() {\n  var containerPolite = document.getElementById('a11y-speak-polite');\n  var containerAssertive = document.getElementById('a11y-speak-assertive');\n\n  if (containerPolite === null) {\n    containerPolite = Object(_addContainer__WEBPACK_IMPORTED_MODULE_0__[\"default\"])('polite');\n  }\n\n  if (containerAssertive === null) {\n    containerAssertive = Object(_addContainer__WEBPACK_IMPORTED_MODULE_0__[\"default\"])('assertive');\n  }\n};\n/**\n * Run setup on domReady.\n */\n\n_wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_2___default()(setup);\n/**\n * Update the ARIA live notification area text node.\n *\n * @param {string} message  The message to be announced by Assistive Technologies.\n * @param {string} ariaLive Optional. The politeness level for aria-live. Possible values:\n *                          polite or assertive. Default polite.\n */\n\nvar speak = function speak(message, ariaLive) {\n  // Clear previous messages to allow repeated strings being read out.\n  Object(_clear__WEBPACK_IMPORTED_MODULE_1__[\"default\"])();\n  message = Object(_filterMessage__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(message);\n  var containerPolite = document.getElementById('a11y-speak-polite');\n  var containerAssertive = document.getElementById('a11y-speak-assertive');\n\n  if (containerAssertive && 'assertive' === ariaLive) {\n    containerAssertive.textContent = message;\n  } else if (containerPolite) {\n    containerPolite.textContent = message;\n  }\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/a11y/build-module/index.js?");

/***/ }),

/***/ "@wordpress/dom-ready":
/*!*******************************************!*\
  !*** external {"this":["wp","domReady"]} ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"domReady\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22domReady%22%5D%7D?");

/***/ })

/******/ });