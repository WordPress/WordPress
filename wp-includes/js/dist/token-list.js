/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ TokenList; }
/* harmony export */ });
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
    this._valueAsArray = [...new Set(value.split(/\s+/g).filter(Boolean))];
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

    this.value = this._valueAsArray.filter(val => !items.includes(val)).join(' ');
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

(window.wp = window.wp || {}).tokenList = __webpack_exports__["default"];
/******/ })()
;