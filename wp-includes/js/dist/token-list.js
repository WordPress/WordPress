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
/******/ 	return __webpack_require__(__webpack_require__.s = 437);
/******/ })
/************************************************************************/
/******/ ({

/***/ 16:
/***/ (function(module, exports) {

(function() { module.exports = window["regeneratorRuntime"]; }());

/***/ }),

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ 25:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ 26:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _createClass; });
function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}

/***/ }),

/***/ 437:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TokenList; });
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(16);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(25);
/* harmony import */ var _babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(26);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(2);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);




/**
 * External dependencies
 */

/**
 * A set of tokens.
 *
 * @see https://dom.spec.whatwg.org/#domtokenlist
 */

var TokenList = /*#__PURE__*/function () {
  /**
   * Constructs a new instance of TokenList.
   *
   * @param {string} initialValue Initial value to assign.
   */
  function TokenList() {
    var initialValue = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

    Object(_babel_runtime_helpers_esm_classCallCheck__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(this, TokenList);

    this.value = initialValue; // Disable reason: These are type hints on the class.

    /* eslint-disable no-unused-expressions */

    /** @type {string} */

    this._currentValue;
    /** @type {string[]} */

    this._valueAsArray;
    /* eslint-enable no-unused-expressions */
  } // Disable reason: JSDoc lint doesn't understand TypeScript types

  /* eslint-disable jsdoc/valid-types */

  /**
   * @param {Parameters<Array<string>['entries']>} args
   */


  Object(_babel_runtime_helpers_esm_createClass__WEBPACK_IMPORTED_MODULE_2__[/* default */ "a"])(TokenList, [{
    key: "entries",
    value: function entries() {
      var _this$_valueAsArray;

      return (_this$_valueAsArray = this._valueAsArray).entries.apply(_this$_valueAsArray, arguments);
    }
    /**
     * @param {Parameters<Array<string>['forEach']>} args
     */

  }, {
    key: "forEach",
    value: function forEach() {
      var _this$_valueAsArray2;

      return (_this$_valueAsArray2 = this._valueAsArray).forEach.apply(_this$_valueAsArray2, arguments);
    }
    /**
     * @param {Parameters<Array<string>['keys']>} args
     */

  }, {
    key: "keys",
    value: function keys() {
      var _this$_valueAsArray3;

      return (_this$_valueAsArray3 = this._valueAsArray).keys.apply(_this$_valueAsArray3, arguments);
    }
    /**
     * @param {Parameters<Array<string>['values']>} args
     */

  }, {
    key: "values",
    value: function values() {
      var _this$_valueAsArray4;

      return (_this$_valueAsArray4 = this._valueAsArray).values.apply(_this$_valueAsArray4, arguments);
    }
    /* eslint-enable jsdoc/valid-types */

    /**
     * Returns the associated set as string.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-value
     *
     * @return {string} Token set as string.
     */

  }, {
    key: "toString",

    /**
     * Returns the stringified form of the TokenList.
     *
     * @see https://dom.spec.whatwg.org/#DOMTokenList-stringification-behavior
     * @see https://www.ecma-international.org/ecma-262/9.0/index.html#sec-tostring
     *
     * @return {string} Token set as string.
     */
    value: function toString() {
      return this.value;
    }
    /**
     * Returns an iterator for the TokenList, iterating items of the set.
     *
     * @see https://dom.spec.whatwg.org/#domtokenlist
     *
     * @return {IterableIterator<string>} TokenList iterator.
     */

  }, {
    key: Symbol.iterator,
    value: /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function value() {
      return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function value$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              return _context.delegateYield(this._valueAsArray, "t0", 1);

            case 1:
              return _context.abrupt("return", _context.t0);

            case 2:
            case "end":
              return _context.stop();
          }
        }
      }, value, this);
    })
    /**
     * Returns the token with index `index`.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-item
     *
     * @param {number} index Index at which to return token.
     *
     * @return {string|undefined} Token at index.
     */

  }, {
    key: "item",
    value: function item(index) {
      return this._valueAsArray[index];
    }
    /**
     * Returns true if `token` is present, and false otherwise.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-contains
     *
     * @param {string} item Token to test.
     *
     * @return {boolean} Whether token is present.
     */

  }, {
    key: "contains",
    value: function contains(item) {
      return this._valueAsArray.indexOf(item) !== -1;
    }
    /**
     * Adds all arguments passed, except those already present.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-add
     *
     * @param {...string} items Items to add.
     */

  }, {
    key: "add",
    value: function add() {
      for (var _len = arguments.length, items = new Array(_len), _key = 0; _key < _len; _key++) {
        items[_key] = arguments[_key];
      }

      this.value += ' ' + items.join(' ');
    }
    /**
     * Removes arguments passed, if they are present.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-remove
     *
     * @param {...string} items Items to remove.
     */

  }, {
    key: "remove",
    value: function remove() {
      for (var _len2 = arguments.length, items = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
        items[_key2] = arguments[_key2];
      }

      this.value = lodash__WEBPACK_IMPORTED_MODULE_3__["without"].apply(void 0, [this._valueAsArray].concat(items)).join(' ');
    }
    /**
     * If `force` is not given, "toggles" `token`, removing it if it’s present
     * and adding it if it’s not present. If `force` is true, adds token (same
     * as add()). If force is false, removes token (same as remove()). Returns
     * true if `token` is now present, and false otherwise.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-toggle
     *
     * @param {string}  token   Token to toggle.
     * @param {boolean} [force] Presence to force.
     *
     * @return {boolean} Whether token is present after toggle.
     */

  }, {
    key: "toggle",
    value: function toggle(token, force) {
      if (undefined === force) {
        force = !this.contains(token);
      }

      if (force) {
        this.add(token);
      } else {
        this.remove(token);
      }

      return force;
    }
    /**
     * Replaces `token` with `newToken`. Returns true if `token` was replaced
     * with `newToken`, and false otherwise.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-replace
     *
     * @param {string} token    Token to replace with `newToken`.
     * @param {string} newToken Token to use in place of `token`.
     *
     * @return {boolean} Whether replacement occurred.
     */

  }, {
    key: "replace",
    value: function replace(token, newToken) {
      if (!this.contains(token)) {
        return false;
      }

      this.remove(token);
      this.add(newToken);
      return true;
    }
    /**
     * Returns true if `token` is in the associated attribute’s supported
     * tokens. Returns false otherwise.
     *
     * Always returns `true` in this implementation.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-supports
     *
     * @return {boolean} Whether token is supported.
     */

  }, {
    key: "supports",
    value: function supports() {
      return true;
    }
  }, {
    key: "value",
    get: function get() {
      return this._currentValue;
    }
    /**
     * Replaces the associated set with a new string value.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-value
     *
     * @param {string} value New token set as string.
     */
    ,
    set: function set(value) {
      value = String(value);
      this._valueAsArray = Object(lodash__WEBPACK_IMPORTED_MODULE_3__["uniq"])(Object(lodash__WEBPACK_IMPORTED_MODULE_3__["compact"])(value.split(/\s+/g)));
      this._currentValue = this._valueAsArray.join(' ');
    }
    /**
     * Returns the number of tokens.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-length
     *
     * @return {number} Number of tokens.
     */

  }, {
    key: "length",
    get: function get() {
      return this._valueAsArray.length;
    }
  }]);

  return TokenList;
}();




/***/ })

/******/ })["default"];