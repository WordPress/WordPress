this["wp"] = this["wp"] || {}; this["wp"]["isShallowEqual"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/is-shallow-equal/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@wordpress/is-shallow-equal/arrays.js":
/*!************************************************************!*\
  !*** ./node_modules/@wordpress/is-shallow-equal/arrays.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\n/**\n * Returns true if the two arrays are shallow equal, or false otherwise.\n *\n * @param {Array} a First array to compare.\n * @param {Array} b Second array to compare.\n *\n * @return {boolean} Whether the two arrays are shallow equal.\n */\nfunction isShallowEqualArrays( a, b ) {\n\tvar i;\n\n\tif ( a === b ) {\n\t\treturn true;\n\t}\n\n\tif ( a.length !== b.length ) {\n\t\treturn false;\n\t}\n\n\tfor ( i = 0; i < a.length; i++ ) {\n\t\tif ( a[ i ] !== b[ i ] ) {\n\t\t\treturn false;\n\t\t}\n\t}\n\n\treturn true;\n}\n\nmodule.exports = isShallowEqualArrays;\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/is-shallow-equal/arrays.js?");

/***/ }),

/***/ "./node_modules/@wordpress/is-shallow-equal/index.js":
/*!***********************************************************!*\
  !*** ./node_modules/@wordpress/is-shallow-equal/index.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\n/**\n * Internal dependencies;\n */\nvar isShallowEqualObjects = __webpack_require__( /*! ./objects */ \"./node_modules/@wordpress/is-shallow-equal/objects.js\" );\nvar isShallowEqualArrays = __webpack_require__( /*! ./arrays */ \"./node_modules/@wordpress/is-shallow-equal/arrays.js\" );\n\nvar isArray = Array.isArray;\n\n/**\n * Returns true if the two arrays or objects are shallow equal, or false\n * otherwise.\n *\n * @param {(Array|Object)} a First object or array to compare.\n * @param {(Array|Object)} b Second object or array to compare.\n *\n * @return {boolean} Whether the two values are shallow equal.\n */\nfunction isShallowEqual( a, b ) {\n\tif ( a && b ) {\n\t\tif ( a.constructor === Object && b.constructor === Object ) {\n\t\t\treturn isShallowEqualObjects( a, b );\n\t\t} else if ( isArray( a ) && isArray( b ) ) {\n\t\t\treturn isShallowEqualArrays( a, b );\n\t\t}\n\t}\n\n\treturn a === b;\n}\n\nmodule.exports = isShallowEqual;\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/is-shallow-equal/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/is-shallow-equal/objects.js":
/*!*************************************************************!*\
  !*** ./node_modules/@wordpress/is-shallow-equal/objects.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nvar keys = Object.keys;\n\n/**\n * Returns true if the two objects are shallow equal, or false otherwise.\n *\n * @param {Object} a First object to compare.\n * @param {Object} b Second object to compare.\n *\n * @return {boolean} Whether the two objects are shallow equal.\n */\nfunction isShallowEqualObjects( a, b ) {\n\tvar aKeys, bKeys, i, key;\n\n\tif ( a === b ) {\n\t\treturn true;\n\t}\n\n\taKeys = keys( a );\n\tbKeys = keys( b );\n\n\tif ( aKeys.length !== bKeys.length ) {\n\t\treturn false;\n\t}\n\n\ti = 0;\n\n\twhile ( i < aKeys.length ) {\n\t\tkey = aKeys[ i ];\n\t\tif ( a[ key ] !== b[ key ] ) {\n\t\t\treturn false;\n\t\t}\n\n\t\ti++;\n\t}\n\n\treturn true;\n}\n\nmodule.exports = isShallowEqualObjects;\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/is-shallow-equal/objects.js?");

/***/ })

/******/ });