this["wp"] = this["wp"] || {}; this["wp"]["tokenList"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/token-list/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

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

/***/ "./node_modules/@wordpress/token-list/build-module/index.js":
/*!******************************************************************!*\
  !*** ./node_modules/@wordpress/token-list/build-module/index.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return TokenList; });\n/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/classCallCheck */ \"./node_modules/@babel/runtime/helpers/esm/classCallCheck.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/createClass */ \"./node_modules/@babel/runtime/helpers/esm/createClass.js\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);\n\n\n\n/**\n * External dependencies\n */\n\n/**\n * A set of tokens.\n *\n * @link https://dom.spec.whatwg.org/#domtokenlist\n */\n\nvar TokenList =\n/*#__PURE__*/\nfunction () {\n  /**\n   * Constructs a new instance of TokenList.\n   *\n   * @param {string} initialValue Initial value to assign.\n   */\n  function TokenList() {\n    var _this = this;\n\n    var initialValue = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';\n\n    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(this, TokenList);\n\n    this.value = initialValue;\n    ['entries', 'forEach', 'keys', 'values'].forEach(function (fn) {\n      _this[fn] = function () {\n        var _this$_valueAsArray;\n\n        return (_this$_valueAsArray = this._valueAsArray)[fn].apply(_this$_valueAsArray, arguments);\n      }.bind(_this);\n    });\n  }\n  /**\n   * Returns the associated set as string.\n   *\n   * @link https://dom.spec.whatwg.org/#dom-domtokenlist-value\n   *\n   * @return {string} Token set as string.\n   */\n\n\n  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(TokenList, [{\n    key: \"toString\",\n\n    /**\n     * Returns the stringified form of the TokenList.\n     *\n     * @link https://dom.spec.whatwg.org/#DOMTokenList-stringification-behavior\n     * @link https://www.ecma-international.org/ecma-262/9.0/index.html#sec-tostring\n     *\n     * @return {string} Token set as string.\n     */\n    value: function toString() {\n      return this.value;\n    }\n    /**\n     * Returns an iterator for the TokenList, iterating items of the set.\n     *\n     * @link https://dom.spec.whatwg.org/#domtokenlist\n     *\n     * @return {Generator} TokenList iterator.\n     */\n\n  }, {\n    key: Symbol.iterator,\n    value:\n    /*#__PURE__*/\n    regeneratorRuntime.mark(function value() {\n      return regeneratorRuntime.wrap(function value$(_context) {\n        while (1) {\n          switch (_context.prev = _context.next) {\n            case 0:\n              return _context.delegateYield(this._valueAsArray, \"t0\", 1);\n\n            case 1:\n              return _context.abrupt(\"return\", _context.t0);\n\n            case 2:\n            case \"end\":\n              return _context.stop();\n          }\n        }\n      }, value, this);\n    })\n    /**\n     * Returns the token with index `index`.\n     *\n     * @link https://dom.spec.whatwg.org/#dom-domtokenlist-item\n     *\n     * @param {number} index Index at which to return token.\n     *\n     * @return {?string} Token at index.\n     */\n\n  }, {\n    key: \"item\",\n    value: function item(index) {\n      return this._valueAsArray[index];\n    }\n    /**\n     * Returns true if `token` is present, and false otherwise.\n     *\n     * @link https://dom.spec.whatwg.org/#dom-domtokenlist-contains\n     *\n     * @param {string} item Token to test.\n     *\n     * @return {boolean} Whether token is present.\n     */\n\n  }, {\n    key: \"contains\",\n    value: function contains(item) {\n      return this._valueAsArray.indexOf(item) !== -1;\n    }\n    /**\n     * Adds all arguments passed, except those already present.\n     *\n     * @link https://dom.spec.whatwg.org/#dom-domtokenlist-add\n     *\n     * @param {...string} items Items to add.\n     */\n\n  }, {\n    key: \"add\",\n    value: function add() {\n      for (var _len = arguments.length, items = new Array(_len), _key = 0; _key < _len; _key++) {\n        items[_key] = arguments[_key];\n      }\n\n      this.value += ' ' + items.join(' ');\n    }\n    /**\n     * Removes arguments passed, if they are present.\n     *\n     * @link https://dom.spec.whatwg.org/#dom-domtokenlist-remove\n     *\n     * @param {...string} items Items to remove.\n     */\n\n  }, {\n    key: \"remove\",\n    value: function remove() {\n      for (var _len2 = arguments.length, items = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {\n        items[_key2] = arguments[_key2];\n      }\n\n      this.value = lodash__WEBPACK_IMPORTED_MODULE_2__[\"without\"].apply(void 0, [this._valueAsArray].concat(items)).join(' ');\n    }\n    /**\n     * If `force` is not given, \"toggles\" `token`, removing it if it’s present\n     * and adding it if it’s not present. If `force` is true, adds token (same\n     * as add()). If force is false, removes token (same as remove()). Returns\n     * true if `token` is now present, and false otherwise.\n     *\n     * @link https://dom.spec.whatwg.org/#dom-domtokenlist-toggle\n     *\n     * @param {string}   token Token to toggle.\n     * @param {?boolean} force Presence to force.\n     *\n     * @return {boolean} Whether token is present after toggle.\n     */\n\n  }, {\n    key: \"toggle\",\n    value: function toggle(token, force) {\n      if (undefined === force) {\n        force = !this.contains(token);\n      }\n\n      if (force) {\n        this.add(token);\n      } else {\n        this.remove(token);\n      }\n\n      return force;\n    }\n    /**\n     * Replaces `token` with `newToken`. Returns true if `token` was replaced\n     * with `newToken`, and false otherwise.\n     *\n     * @link https://dom.spec.whatwg.org/#dom-domtokenlist-replace\n     *\n     * @param {string} token    Token to replace with `newToken`.\n     * @param {string} newToken Token to use in place of `token`.\n     *\n     * @return {boolean} Whether replacement occurred.\n     */\n\n  }, {\n    key: \"replace\",\n    value: function replace(token, newToken) {\n      if (!this.contains(token)) {\n        return false;\n      }\n\n      this.remove(token);\n      this.add(newToken);\n      return true;\n    }\n    /**\n     * Returns true if `token` is in the associated attribute’s supported\n     * tokens. Returns false otherwise.\n     *\n     * Always returns `true` in this implementation.\n     *\n     * @link https://dom.spec.whatwg.org/#dom-domtokenlist-supports\n     *\n     * @return {boolean} Whether token is supported.\n     */\n\n  }, {\n    key: \"supports\",\n    value: function supports() {\n      return true;\n    }\n  }, {\n    key: \"value\",\n    get: function get() {\n      return this._currentValue;\n    }\n    /**\n     * Replaces the associated set with a new string value.\n     *\n     * @link https://dom.spec.whatwg.org/#dom-domtokenlist-value\n     *\n     * @param {string} value New token set as string.\n     */\n    ,\n    set: function set(value) {\n      value = String(value);\n      this._valueAsArray = Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"uniq\"])(Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"compact\"])(value.split(/\\s+/g)));\n      this._currentValue = this._valueAsArray.join(' ');\n    }\n    /**\n     * Returns the number of tokens.\n     *\n     * @link https://dom.spec.whatwg.org/#dom-domtokenlist-length\n     *\n     * @return {number} Number of tokens.\n     */\n\n  }, {\n    key: \"length\",\n    get: function get() {\n      return this._valueAsArray.length;\n    }\n  }]);\n\n  return TokenList;\n}();\n\n\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/token-list/build-module/index.js?");

/***/ }),

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"lodash\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%22lodash%22?");

/***/ })

/******/ })["default"];