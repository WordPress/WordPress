this["wp"] = this["wp"] || {}; this["wp"]["escapeHtml"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/escape-html/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@wordpress/escape-html/build-module/index.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@wordpress/escape-html/build-module/index.js ***!
  \*******************************************************************/
/*! exports provided: escapeAmpersand, escapeQuotationMark, escapeLessThan, escapeAttribute, escapeHTML, isValidAttributeName */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"escapeAmpersand\", function() { return escapeAmpersand; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"escapeQuotationMark\", function() { return escapeQuotationMark; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"escapeLessThan\", function() { return escapeLessThan; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"escapeAttribute\", function() { return escapeAttribute; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"escapeHTML\", function() { return escapeHTML; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"isValidAttributeName\", function() { return isValidAttributeName; });\n/**\n * Regular expression matching invalid attribute names.\n *\n * \"Attribute names must consist of one or more characters other than controls,\n * U+0020 SPACE, U+0022 (\"), U+0027 ('), U+003E (>), U+002F (/), U+003D (=),\n * and noncharacters.\"\n *\n * @link https://html.spec.whatwg.org/multipage/syntax.html#attributes-2\n *\n * @type {RegExp}\n */\nvar REGEXP_INVALID_ATTRIBUTE_NAME = /[\\u007F-\\u009F \"'>/=\"\\uFDD0-\\uFDEF]/;\n/**\n * Returns a string with ampersands escaped. Note that this is an imperfect\n * implementation, where only ampersands which do not appear as a pattern of\n * named, decimal, or hexadecimal character references are escaped. Invalid\n * named references (i.e. ambiguous ampersand) are are still permitted.\n *\n * @link https://w3c.github.io/html/syntax.html#character-references\n * @link https://w3c.github.io/html/syntax.html#ambiguous-ampersand\n * @link https://w3c.github.io/html/syntax.html#named-character-references\n *\n * @param {string} value Original string.\n *\n * @return {string} Escaped string.\n */\n\nfunction escapeAmpersand(value) {\n  return value.replace(/&(?!([a-z0-9]+|#[0-9]+|#x[a-f0-9]+);)/gi, '&amp;');\n}\n/**\n * Returns a string with quotation marks replaced.\n *\n * @param {string} value Original string.\n *\n * @return {string} Escaped string.\n */\n\nfunction escapeQuotationMark(value) {\n  return value.replace(/\"/g, '&quot;');\n}\n/**\n * Returns a string with less-than sign replaced.\n *\n * @param {string} value Original string.\n *\n * @return {string} Escaped string.\n */\n\nfunction escapeLessThan(value) {\n  return value.replace(/</g, '&lt;');\n}\n/**\n * Returns an escaped attribute value.\n *\n * @link https://w3c.github.io/html/syntax.html#elements-attributes\n *\n * \"[...] the text cannot contain an ambiguous ampersand [...] must not contain\n * any literal U+0022 QUOTATION MARK characters (\")\"\n *\n * @param {string} value Attribute value.\n *\n * @return {string} Escaped attribute value.\n */\n\nfunction escapeAttribute(value) {\n  return escapeQuotationMark(escapeAmpersand(value));\n}\n/**\n * Returns an escaped HTML element value.\n *\n * @link https://w3c.github.io/html/syntax.html#writing-html-documents-elements\n *\n * \"the text must not contain the character U+003C LESS-THAN SIGN (<) or an\n * ambiguous ampersand.\"\n *\n * @param {string} value Element value.\n *\n * @return {string} Escaped HTML element value.\n */\n\nfunction escapeHTML(value) {\n  return escapeLessThan(escapeAmpersand(value));\n}\n/**\n * Returns true if the given attribute name is valid, or false otherwise.\n *\n * @param {string} name Attribute name to test.\n *\n * @return {boolean} Whether attribute is valid.\n */\n\nfunction isValidAttributeName(name) {\n  return !REGEXP_INVALID_ATTRIBUTE_NAME.test(name);\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/escape-html/build-module/index.js?");

/***/ })

/******/ });