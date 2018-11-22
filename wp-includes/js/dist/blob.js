this["wp"] = this["wp"] || {}; this["wp"]["blob"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/blob/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@wordpress/blob/build-module/index.js":
/*!************************************************************!*\
  !*** ./node_modules/@wordpress/blob/build-module/index.js ***!
  \************************************************************/
/*! exports provided: createBlobURL, getBlobByURL, revokeBlobURL, isBlobURL */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"createBlobURL\", function() { return createBlobURL; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"getBlobByURL\", function() { return getBlobByURL; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"revokeBlobURL\", function() { return revokeBlobURL; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"isBlobURL\", function() { return isBlobURL; });\n/**\n * Browser dependencies\n */\nvar _window$URL = window.URL,\n    createObjectURL = _window$URL.createObjectURL,\n    revokeObjectURL = _window$URL.revokeObjectURL;\nvar cache = {};\n/**\n * Create a blob URL from a file.\n *\n * @param {File} file The file to create a blob URL for.\n *\n * @return {string} The blob URL.\n */\n\nfunction createBlobURL(file) {\n  var url = createObjectURL(file);\n  cache[url] = file;\n  return url;\n}\n/**\n * Retrieve a file based on a blob URL. The file must have been created by\n * `createBlobURL` and not removed by `revokeBlobURL`, otherwise it will return\n * `undefined`.\n *\n * @param {string} url The blob URL.\n *\n * @return {?File} The file for the blob URL.\n */\n\nfunction getBlobByURL(url) {\n  return cache[url];\n}\n/**\n * Remove the resource and file cache from memory.\n *\n * @param {string} url The blob URL.\n */\n\nfunction revokeBlobURL(url) {\n  if (cache[url]) {\n    revokeObjectURL(url);\n  }\n\n  delete cache[url];\n}\n/**\n * Check whether a url is a blob url.\n *\n * @param {string} url The URL.\n *\n * @return {boolean} Is the url a blob url?\n */\n\nfunction isBlobURL(url) {\n  if (!url || !url.indexOf) {\n    return false;\n  }\n\n  return url.indexOf('blob:') === 0;\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/blob/build-module/index.js?");

/***/ })

/******/ });