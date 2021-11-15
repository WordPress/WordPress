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
/******/ 	return __webpack_require__(__webpack_require__.s = "hwXU");
/******/ })
/************************************************************************/
/******/ ({

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "hwXU":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TokenList; });
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("YLtl");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);
/**
 * External dependencies
 */

/**
 * A set of tokens.
 *
 * @see https://dom.spec.whatwg.org/#domtokenlist
 */

class TokenList {
  /**
   * Constructs a new instance of TokenList.
   *
   * @param {string} initialValue Initial value to assign.
   */
  constructor() {
    let initialValue = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
    this.value = initialValue; // Disable reason: These are type hints on the class.

    /* eslint-disable no-unused-expressions */

    /** @type {string} */

    this._currentValue;
    /** @type {string[]} */

    this._valueAsArray;
    /* eslint-enable no-unused-expressions */
  }
  /**
   * @param {Parameters<Array<string>['entries']>} args
   */


  entries() {
    return this._valueAsArray.entries(...arguments);
  }
  /**
   * @param {Parameters<Array<string>['forEach']>} args
   */


  forEach() {
    return this._valueAsArray.forEach(...arguments);
  }
  /**
   * @param {Parameters<Array<string>['keys']>} args
   */


  keys() {
    return this._valueAsArray.keys(...arguments);
  }
  /**
   * @param {Parameters<Array<string>['values']>} args
   */


  values() {
    return this._valueAsArray.values(...arguments);
  }
  /**
   * Returns the associated set as string.
   *
   * @see https://dom.spec.whatwg.org/#dom-domtokenlist-value
   *
   * @return {string} Token set as string.
   */


  get value() {
    return this._currentValue;
  }
  /**
   * Replaces the associated set with a new string value.
   *
   * @see https://dom.spec.whatwg.org/#dom-domtokenlist-value
   *
   * @param {string} value New token set as string.
   */


  set value(value) {
    value = String(value);
    this._valueAsArray = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["uniq"])(Object(lodash__WEBPACK_IMPORTED_MODULE_0__["compact"])(value.split(/\s+/g)));
    this._currentValue = this._valueAsArray.join(' ');
  }
  /**
   * Returns the number of tokens.
   *
   * @see https://dom.spec.whatwg.org/#dom-domtokenlist-length
   *
   * @return {number} Number of tokens.
   */


  get length() {
    return this._valueAsArray.length;
  }
  /**
   * Returns the stringified form of the TokenList.
   *
   * @see https://dom.spec.whatwg.org/#DOMTokenList-stringification-behavior
   * @see https://www.ecma-international.org/ecma-262/9.0/index.html#sec-tostring
   *
   * @return {string} Token set as string.
   */


  toString() {
    return this.value;
  }
  /**
   * Returns an iterator for the TokenList, iterating items of the set.
   *
   * @see https://dom.spec.whatwg.org/#domtokenlist
   *
   * @return {IterableIterator<string>} TokenList iterator.
   */


  *[Symbol.iterator]() {
    return yield* this._valueAsArray;
  }
  /**
   * Returns the token with index `index`.
   *
   * @see https://dom.spec.whatwg.org/#dom-domtokenlist-item
   *
   * @param {number} index Index at which to return token.
   *
   * @return {string|undefined} Token at index.
   */


  item(index) {
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


  contains(item) {
    return this._valueAsArray.indexOf(item) !== -1;
  }
  /**
   * Adds all arguments passed, except those already present.
   *
   * @see https://dom.spec.whatwg.org/#dom-domtokenlist-add
   *
   * @param {...string} items Items to add.
   */


  add() {
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


  remove() {
    for (var _len2 = arguments.length, items = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
      items[_key2] = arguments[_key2];
    }

    this.value = Object(lodash__WEBPACK_IMPORTED_MODULE_0__["without"])(this._valueAsArray, ...items).join(' ');
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


  toggle(token, force) {
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


  replace(token, newToken) {
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


  supports() {
    return true;
  }

}


/***/ })

/******/ })["default"];