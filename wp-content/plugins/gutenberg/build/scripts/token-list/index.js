"use strict";
var wp;
(wp ||= {}).tokenList = (() => {
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // packages/token-list/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    default: () => TokenList
  });
  var TokenList = class {
    _currentValue;
    _valueAsArray;
    /**
     * Constructs a new instance of TokenList.
     *
     * @param initialValue Initial value to assign.
     */
    constructor(initialValue = "") {
      this._currentValue = "";
      this._valueAsArray = [];
      this.value = initialValue;
    }
    entries(...args) {
      return this._valueAsArray.entries(...args);
    }
    forEach(...args) {
      return this._valueAsArray.forEach(...args);
    }
    keys(...args) {
      return this._valueAsArray.keys(...args);
    }
    values(...args) {
      return this._valueAsArray.values(...args);
    }
    /**
     * Returns the associated set as string.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-value
     *
     * @return Token set as string.
     */
    get value() {
      return this._currentValue;
    }
    /**
     * Replaces the associated set with a new string value.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-value
     *
     * @param value New token set as string.
     */
    set value(value) {
      value = String(value);
      this._valueAsArray = [
        ...new Set(value.split(/\s+/g).filter(Boolean))
      ];
      this._currentValue = this._valueAsArray.join(" ");
    }
    /**
     * Returns the number of tokens.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-length
     *
     * @return Number of tokens.
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
     * @return Token set as string.
     */
    toString() {
      return this.value;
    }
    /**
     * Returns an iterator for the TokenList, iterating items of the set.
     *
     * @see https://dom.spec.whatwg.org/#domtokenlist
     *
     * @return TokenList iterator.
     */
    *[Symbol.iterator]() {
      return yield* this._valueAsArray;
    }
    /**
     * Returns the token with index `index`.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-item
     *
     * @param index Index at which to return token.
     *
     * @return Token at index.
     */
    item(index) {
      return this._valueAsArray[index];
    }
    /**
     * Returns true if `token` is present, and false otherwise.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-contains
     *
     * @param item Token to test.
     *
     * @return Whether token is present.
     */
    contains(item) {
      return this._valueAsArray.indexOf(item) !== -1;
    }
    /**
     * Adds all arguments passed, except those already present.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-add
     *
     * @param items Items to add.
     */
    add(...items) {
      this.value += " " + items.join(" ");
    }
    /**
     * Removes arguments passed, if they are present.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-remove
     *
     * @param items Items to remove.
     */
    remove(...items) {
      this.value = this._valueAsArray.filter((val) => !items.includes(val)).join(" ");
    }
    /**
     * If `force` is not given, "toggles" `token`, removing it if it’s present
     * and adding it if it’s not present. If `force` is true, adds token (same
     * as add()). If force is false, removes token (same as remove()). Returns
     * true if `token` is now present, and false otherwise.
     *
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-toggle
     *
     * @param token   Token to toggle.
     * @param [force] Presence to force.
     *
     * @return Whether token is present after toggle.
     */
    toggle(token, force) {
      if (void 0 === force) {
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
     * @param token    Token to replace with `newToken`.
     * @param newToken Token to use in place of `token`.
     *
     * @return Whether replacement occurred.
     */
    replace(token, newToken) {
      if (!this.contains(token)) {
        return false;
      }
      this.remove(token);
      this.add(newToken);
      return true;
    }
    /* eslint-disable @typescript-eslint/no-unused-vars */
    /**
     * Returns true if `token` is in the associated attribute’s supported
     * tokens. Returns false otherwise.
     *
     * Always returns `true` in this implementation.
     *
     * @param _token
     * @see https://dom.spec.whatwg.org/#dom-domtokenlist-supports
     *
     * @return Whether token is supported.
     */
    supports(_token) {
      return true;
    }
    /* eslint-enable @typescript-eslint/no-unused-vars */
  };
  return __toCommonJS(index_exports);
})();
if (typeof wp.tokenList === 'object' && wp.tokenList.default) { wp.tokenList = wp.tokenList.default; }
//# sourceMappingURL=index.js.map
