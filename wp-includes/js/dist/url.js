this["wp"] = this["wp"] || {}; this["wp"]["url"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 449);
/******/ })
/************************************************************************/
/******/ ({

/***/ 11:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _slicedToArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
var arrayWithHoles = __webpack_require__(38);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js
function _iterableToArrayLimit(arr, i) {
  if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return;
  var _arr = [];
  var _n = true;
  var _d = false;
  var _e = undefined;

  try {
    for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
      _arr.push(_s.value);

      if (i && _arr.length === i) break;
    }
  } catch (err) {
    _d = true;
    _e = err;
  } finally {
    try {
      if (!_n && _i["return"] != null) _i["return"]();
    } finally {
      if (_d) throw _e;
    }
  }

  return _arr;
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
var unsupportedIterableToArray = __webpack_require__(31);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
var nonIterableRest = __webpack_require__(39);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js




function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(unsupportedIterableToArray["a" /* default */])(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ 24:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayLikeToArray; });
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}

/***/ }),

/***/ 31:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _unsupportedIterableToArray; });
/* harmony import */ var _babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(24);

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return Object(_babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return Object(_babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
}

/***/ }),

/***/ 38:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayWithHoles; });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),

/***/ 39:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _nonIterableRest; });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

/***/ }),

/***/ 449:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "isURL", function() { return /* reexport */ isURL; });
__webpack_require__.d(__webpack_exports__, "isEmail", function() { return /* reexport */ isEmail; });
__webpack_require__.d(__webpack_exports__, "getProtocol", function() { return /* reexport */ getProtocol; });
__webpack_require__.d(__webpack_exports__, "isValidProtocol", function() { return /* reexport */ isValidProtocol; });
__webpack_require__.d(__webpack_exports__, "getAuthority", function() { return /* reexport */ getAuthority; });
__webpack_require__.d(__webpack_exports__, "isValidAuthority", function() { return /* reexport */ isValidAuthority; });
__webpack_require__.d(__webpack_exports__, "getPath", function() { return /* reexport */ getPath; });
__webpack_require__.d(__webpack_exports__, "isValidPath", function() { return /* reexport */ isValidPath; });
__webpack_require__.d(__webpack_exports__, "getQueryString", function() { return /* reexport */ getQueryString; });
__webpack_require__.d(__webpack_exports__, "buildQueryString", function() { return /* reexport */ buildQueryString; });
__webpack_require__.d(__webpack_exports__, "isValidQueryString", function() { return /* reexport */ isValidQueryString; });
__webpack_require__.d(__webpack_exports__, "getPathAndQueryString", function() { return /* reexport */ getPathAndQueryString; });
__webpack_require__.d(__webpack_exports__, "getFragment", function() { return /* reexport */ getFragment; });
__webpack_require__.d(__webpack_exports__, "isValidFragment", function() { return /* reexport */ isValidFragment; });
__webpack_require__.d(__webpack_exports__, "addQueryArgs", function() { return /* reexport */ addQueryArgs; });
__webpack_require__.d(__webpack_exports__, "getQueryArg", function() { return /* reexport */ getQueryArg; });
__webpack_require__.d(__webpack_exports__, "getQueryArgs", function() { return /* reexport */ getQueryArgs; });
__webpack_require__.d(__webpack_exports__, "hasQueryArg", function() { return /* reexport */ hasQueryArg; });
__webpack_require__.d(__webpack_exports__, "removeQueryArgs", function() { return /* reexport */ removeQueryArgs; });
__webpack_require__.d(__webpack_exports__, "prependHTTP", function() { return /* reexport */ prependHTTP; });
__webpack_require__.d(__webpack_exports__, "safeDecodeURI", function() { return /* reexport */ safeDecodeURI; });
__webpack_require__.d(__webpack_exports__, "safeDecodeURIComponent", function() { return /* reexport */ safeDecodeURIComponent; });
__webpack_require__.d(__webpack_exports__, "filterURLForDisplay", function() { return /* reexport */ filterURLForDisplay; });
__webpack_require__.d(__webpack_exports__, "cleanForSlug", function() { return /* reexport */ cleanForSlug; });

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-url.js
/**
 * Determines whether the given string looks like a URL.
 *
 * @param {string} url The string to scrutinise.
 *
 * @example
 * ```js
 * const isURL = isURL( 'https://wordpress.org' ); // true
 * ```
 *
 * @see https://url.spec.whatwg.org/
 * @see https://url.spec.whatwg.org/#valid-url-string
 *
 * @return {boolean} Whether or not it looks like a URL.
 */
function isURL(url) {
  // A URL can be considered value if the `URL` constructor is able to parse
  // it. The constructor throws an error for an invalid URL.
  try {
    new URL(url);
    return true;
  } catch (_unused) {
    return false;
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-email.js
var EMAIL_REGEXP = /^(mailto:)?[a-z0-9._%+-]+@[a-z0-9][a-z0-9.-]*\.[a-z]{2,63}$/i;
/**
 * Determines whether the given string looks like an email.
 *
 * @param {string} email The string to scrutinise.
 *
 * @example
 * ```js
 * const isEmail = isEmail( 'hello@wordpress.org' ); // true
 * ```
 *
 * @return {boolean} Whether or not it looks like an email.
 */

function isEmail(email) {
  return EMAIL_REGEXP.test(email);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-protocol.js
/**
 * Returns the protocol part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @example
 * ```js
 * const protocol1 = getProtocol( 'tel:012345678' ); // 'tel:'
 * const protocol2 = getProtocol( 'https://wordpress.org' ); // 'https:'
 * ```
 *
 * @return {string|void} The protocol part of the URL.
 */
function getProtocol(url) {
  var matches = /^([^\s:]+:)/.exec(url);

  if (matches) {
    return matches[1];
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-valid-protocol.js
/**
 * Tests if a url protocol is valid.
 *
 * @param {string} protocol The url protocol.
 *
 * @example
 * ```js
 * const isValid = isValidProtocol( 'https:' ); // true
 * const isNotValid = isValidProtocol( 'https :' ); // false
 * ```
 *
 * @return {boolean} True if the argument is a valid protocol (e.g. http:, tel:).
 */
function isValidProtocol(protocol) {
  if (!protocol) {
    return false;
  }

  return /^[a-z\-.\+]+[0-9]*:$/i.test(protocol);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-authority.js
/**
 * Returns the authority part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @example
 * ```js
 * const authority1 = getAuthority( 'https://wordpress.org/help/' ); // 'wordpress.org'
 * const authority2 = getAuthority( 'https://localhost:8080/test/' ); // 'localhost:8080'
 * ```
 *
 * @return {string|void} The authority part of the URL.
 */
function getAuthority(url) {
  var matches = /^[^\/\s:]+:(?:\/\/)?\/?([^\/\s#?]+)[\/#?]{0,1}\S*$/.exec(url);

  if (matches) {
    return matches[1];
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-valid-authority.js
/**
 * Checks for invalid characters within the provided authority.
 *
 * @param {string} authority A string containing the URL authority.
 *
 * @example
 * ```js
 * const isValid = isValidAuthority( 'wordpress.org' ); // true
 * const isNotValid = isValidAuthority( 'wordpress#org' ); // false
 * ```
 *
 * @return {boolean} True if the argument contains a valid authority.
 */
function isValidAuthority(authority) {
  if (!authority) {
    return false;
  }

  return /^[^\s#?]+$/.test(authority);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-path.js
/**
 * Returns the path part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @example
 * ```js
 * const path1 = getPath( 'http://localhost:8080/this/is/a/test?query=true' ); // 'this/is/a/test'
 * const path2 = getPath( 'https://wordpress.org/help/faq/' ); // 'help/faq'
 * ```
 *
 * @return {string|void} The path part of the URL.
 */
function getPath(url) {
  var matches = /^[^\/\s:]+:(?:\/\/)?[^\/\s#?]+[\/]([^\s#?]+)[#?]{0,1}\S*$/.exec(url);

  if (matches) {
    return matches[1];
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-valid-path.js
/**
 * Checks for invalid characters within the provided path.
 *
 * @param {string} path The URL path.
 *
 * @example
 * ```js
 * const isValid = isValidPath( 'test/path/' ); // true
 * const isNotValid = isValidPath( '/invalid?test/path/' ); // false
 * ```
 *
 * @return {boolean} True if the argument contains a valid path
 */
function isValidPath(path) {
  if (!path) {
    return false;
  }

  return /^[^\s#?]+$/.test(path);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-query-string.js
/**
 * Returns the query string part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @example
 * ```js
 * const queryString = getQueryString( 'http://localhost:8080/this/is/a/test?query=true#fragment' ); // 'query=true'
 * ```
 *
 * @return {string|void} The query string part of the URL.
 */
function getQueryString(url) {
  var query;

  try {
    query = new URL(url, 'http://example.com').search.substring(1);
  } catch (error) {}

  if (query) {
    return query;
  }
}

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(11);

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/build-query-string.js


function _createForOfIteratorHelper(o, allowArrayLike) { var it; if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

/**
 * Generates URL-encoded query string using input query data.
 *
 * It is intended to behave equivalent as PHP's `http_build_query`, configured
 * with encoding type PHP_QUERY_RFC3986 (spaces as `%20`).
 *
 * @example
 * ```js
 * const queryString = buildQueryString( {
 *    simple: 'is ok',
 *    arrays: [ 'are', 'fine', 'too' ],
 *    objects: {
 *       evenNested: {
 *          ok: 'yes',
 *       },
 *    },
 * } );
 * // "simple=is%20ok&arrays%5B0%5D=are&arrays%5B1%5D=fine&arrays%5B2%5D=too&objects%5BevenNested%5D%5Bok%5D=yes"
 * ```
 *
 * @param {Record<string,*>} data Data to encode.
 *
 * @return {string} Query string.
 */
function buildQueryString(data) {
  var string = '';
  var stack = Object.entries(data);
  var pair;

  while (pair = stack.shift()) {
    var _pair = pair,
        _pair2 = Object(slicedToArray["a" /* default */])(_pair, 2),
        key = _pair2[0],
        value = _pair2[1]; // Support building deeply nested data, from array or object values.


    var hasNestedData = Array.isArray(value) || value && value.constructor === Object;

    if (hasNestedData) {
      // Push array or object values onto the stack as composed of their
      // original key and nested index or key, retaining order by a
      // combination of Array#reverse and Array#unshift onto the stack.
      var valuePairs = Object.entries(value).reverse();

      var _iterator = _createForOfIteratorHelper(valuePairs),
          _step;

      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var _step$value = Object(slicedToArray["a" /* default */])(_step.value, 2),
              member = _step$value[0],
              memberValue = _step$value[1];

          stack.unshift(["".concat(key, "[").concat(member, "]"), memberValue]);
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }
    } else if (value !== undefined) {
      // Null is treated as special case, equivalent to empty string.
      if (value === null) {
        value = '';
      }

      string += '&' + [key, value].map(encodeURIComponent).join('=');
    }
  } // Loop will concatenate with leading `&`, but it's only expected for all
  // but the first query parameter. This strips the leading `&`, while still
  // accounting for the case that the string may in-fact be empty.


  return string.substr(1);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-valid-query-string.js
/**
 * Checks for invalid characters within the provided query string.
 *
 * @param {string} queryString The query string.
 *
 * @example
 * ```js
 * const isValid = isValidQueryString( 'query=true&another=false' ); // true
 * const isNotValid = isValidQueryString( 'query=true?another=false' ); // false
 * ```
 *
 * @return {boolean} True if the argument contains a valid query string.
 */
function isValidQueryString(queryString) {
  if (!queryString) {
    return false;
  }

  return /^[^\s#?\/]+$/.test(queryString);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-path-and-query-string.js
/**
 * Internal dependencies
 */

/**
 * Returns the path part and query string part of the URL.
 *
 * @param {string} url The full URL.
 *
 * @example
 * ```js
 * const pathAndQueryString1 = getPathAndQueryString( 'http://localhost:8080/this/is/a/test?query=true' ); // '/this/is/a/test?query=true'
 * const pathAndQueryString2 = getPathAndQueryString( 'https://wordpress.org/help/faq/' ); // '/help/faq'
 * ```
 *
 * @return {string} The path part and query string part of the URL.
 */

function getPathAndQueryString(url) {
  var path = getPath(url);
  var queryString = getQueryString(url);
  var value = '/';
  if (path) value += path;
  if (queryString) value += "?".concat(queryString);
  return value;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-fragment.js
/**
 * Returns the fragment part of the URL.
 *
 * @param {string} url The full URL
 *
 * @example
 * ```js
 * const fragment1 = getFragment( 'http://localhost:8080/this/is/a/test?query=true#fragment' ); // '#fragment'
 * const fragment2 = getFragment( 'https://wordpress.org#another-fragment?query=true' ); // '#another-fragment'
 * ```
 *
 * @return {string|void} The fragment part of the URL.
 */
function getFragment(url) {
  var matches = /^\S+?(#[^\s\?]*)/.exec(url);

  if (matches) {
    return matches[1];
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/is-valid-fragment.js
/**
 * Checks for invalid characters within the provided fragment.
 *
 * @param {string} fragment The url fragment.
 *
 * @example
 * ```js
 * const isValid = isValidFragment( '#valid-fragment' ); // true
 * const isNotValid = isValidFragment( '#invalid-#fragment' ); // false
 * ```
 *
 * @return {boolean} True if the argument contains a valid fragment.
 */
function isValidFragment(fragment) {
  if (!fragment) {
    return false;
  }

  return /^#[^\s#?\/]*$/.test(fragment);
}

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(5);

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-query-args.js



function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Internal dependencies
 */

/** @typedef {import('./get-query-arg').QueryArgParsed} QueryArgParsed */

/**
 * @typedef {Record<string,QueryArgParsed>} QueryArgs
 */

/**
 * Sets a value in object deeply by a given array of path segments. Mutates the
 * object reference.
 *
 * @param {Record<string,*>} object Object in which to assign.
 * @param {string[]}         path   Path segment at which to set value.
 * @param {*}                value  Value to set.
 */

function setPath(object, path, value) {
  var length = path.length;
  var lastIndex = length - 1;

  for (var i = 0; i < length; i++) {
    var key = path[i];

    if (!key && Array.isArray(object)) {
      // If key is empty string and next value is array, derive key from
      // the current length of the array.
      key = object.length.toString();
    } // If the next key in the path is numeric (or empty string), it will be
    // created as an array. Otherwise, it will be created as an object.


    var isNextKeyArrayIndex = !isNaN(Number(path[i + 1]));
    object[key] = i === lastIndex ? // If at end of path, assign the intended value.
    value : // Otherwise, advance to the next object in the path, creating
    // it if it does not yet exist.
    object[key] || (isNextKeyArrayIndex ? [] : {});

    if (Array.isArray(object[key]) && !isNextKeyArrayIndex) {
      // If we current key is non-numeric, but the next value is an
      // array, coerce the value to an object.
      object[key] = _objectSpread({}, object[key]);
    } // Update working reference object to the next in the path.


    object = object[key];
  }
}
/**
 * Returns an object of query arguments of the given URL. If the given URL is
 * invalid or has no querystring, an empty object is returned.
 *
 * @param {string} url URL.
 *
 * @example
 * ```js
 * const foo = getQueryArgs( 'https://wordpress.org?foo=bar&bar=baz' );
 * // { "foo": "bar", "bar": "baz" }
 * ```
 *
 * @return {QueryArgs} Query args object.
 */


function getQueryArgs(url) {
  return (getQueryString(url) || ''). // Normalize space encoding, accounting for PHP URL encoding
  // corresponding to `application/x-www-form-urlencoded`.
  //
  // See: https://tools.ietf.org/html/rfc1866#section-8.2.1
  replace(/\+/g, '%20').split('&').reduce(function (accumulator, keyValue) {
    var _keyValue$split$filte = keyValue.split('=') // Filtering avoids decoding as `undefined` for value, where
    // default is restored in destructuring assignment.
    .filter(Boolean).map(decodeURIComponent),
        _keyValue$split$filte2 = Object(slicedToArray["a" /* default */])(_keyValue$split$filte, 2),
        key = _keyValue$split$filte2[0],
        _keyValue$split$filte3 = _keyValue$split$filte2[1],
        value = _keyValue$split$filte3 === void 0 ? '' : _keyValue$split$filte3;

    if (key) {
      var segments = key.replace(/\]/g, '').split('[');
      setPath(accumulator, segments, value);
    }

    return accumulator;
  }, {});
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/add-query-args.js
/**
 * Internal dependencies
 */


/**
 * Appends arguments as querystring to the provided URL. If the URL already
 * includes query arguments, the arguments are merged with (and take precedent
 * over) the existing set.
 *
 * @param {string} [url='']  URL to which arguments should be appended. If omitted,
 *                           only the resulting querystring is returned.
 * @param {Object} [args]    Query arguments to apply to URL.
 *
 * @example
 * ```js
 * const newURL = addQueryArgs( 'https://google.com', { q: 'test' } ); // https://google.com/?q=test
 * ```
 *
 * @return {string} URL with arguments applied.
 */

function addQueryArgs() {
  var url = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var args = arguments.length > 1 ? arguments[1] : undefined;

  // If no arguments are to be appended, return original URL.
  if (!args || !Object.keys(args).length) {
    return url;
  }

  var baseUrl = url; // Determine whether URL already had query arguments.

  var queryStringIndex = url.indexOf('?');

  if (queryStringIndex !== -1) {
    // Merge into existing query arguments.
    args = Object.assign(getQueryArgs(url), args); // Change working base URL to omit previous query arguments.

    baseUrl = baseUrl.substr(0, queryStringIndex);
  }

  return baseUrl + '?' + buildQueryString(args);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/get-query-arg.js
/**
 * Internal dependencies
 */

/* eslint-disable jsdoc/valid-types */

/**
 * @typedef {{[key: string]: QueryArgParsed}} QueryArgObject
 */

/* eslint-enable */

/**
 * @typedef {string|string[]|QueryArgObject} QueryArgParsed
 */

/**
 * Returns a single query argument of the url
 *
 * @param {string} url URL.
 * @param {string} arg Query arg name.
 *
 * @example
 * ```js
 * const foo = getQueryArg( 'https://wordpress.org?foo=bar&bar=baz', 'foo' ); // bar
 * ```
 *
 * @return {QueryArgParsed|void} Query arg value.
 */

function getQueryArg(url, arg) {
  return getQueryArgs(url)[arg];
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/has-query-arg.js
/**
 * Internal dependencies
 */

/**
 * Determines whether the URL contains a given query arg.
 *
 * @param {string} url URL.
 * @param {string} arg Query arg name.
 *
 * @example
 * ```js
 * const hasBar = hasQueryArg( 'https://wordpress.org?foo=bar&bar=baz', 'bar' ); // true
 * ```
 *
 * @return {boolean} Whether or not the URL contains the query arg.
 */

function hasQueryArg(url, arg) {
  return getQueryArg(url, arg) !== undefined;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/remove-query-args.js
/**
 * Internal dependencies
 */


/**
 * Removes arguments from the query string of the url
 *
 * @param {string}    url  URL.
 * @param {...string} args Query Args.
 *
 * @example
 * ```js
 * const newUrl = removeQueryArgs( 'https://wordpress.org?foo=bar&bar=baz&baz=foobar', 'foo', 'bar' ); // https://wordpress.org?baz=foobar
 * ```
 *
 * @return {string} Updated URL.
 */

function removeQueryArgs(url) {
  var queryStringIndex = url.indexOf('?');

  if (queryStringIndex === -1) {
    return url;
  }

  var query = getQueryArgs(url);
  var baseURL = url.substr(0, queryStringIndex);

  for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
    args[_key - 1] = arguments[_key];
  }

  args.forEach(function (arg) {
    return delete query[arg];
  });
  var queryString = buildQueryString(query);
  return queryString ? baseURL + '?' + queryString : baseURL;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/prepend-http.js
/**
 * Internal dependencies
 */

var USABLE_HREF_REGEXP = /^(?:[a-z]+:|#|\?|\.|\/)/i;
/**
 * Prepends "http://" to a url, if it looks like something that is meant to be a TLD.
 *
 * @param {string} url The URL to test.
 *
 * @example
 * ```js
 * const actualURL = prependHTTP( 'wordpress.org' ); // http://wordpress.org
 * ```
 *
 * @return {string} The updated URL.
 */

function prependHTTP(url) {
  if (!url) {
    return url;
  }

  url = url.trim();

  if (!USABLE_HREF_REGEXP.test(url) && !isEmail(url)) {
    return 'http://' + url;
  }

  return url;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/safe-decode-uri.js
/**
 * Safely decodes a URI with `decodeURI`. Returns the URI unmodified if
 * `decodeURI` throws an error.
 *
 * @param {string} uri URI to decode.
 *
 * @example
 * ```js
 * const badUri = safeDecodeURI( '%z' ); // does not throw an Error, simply returns '%z'
 * ```
 *
 * @return {string} Decoded URI if possible.
 */
function safeDecodeURI(uri) {
  try {
    return decodeURI(uri);
  } catch (uriError) {
    return uri;
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/safe-decode-uri-component.js
/**
 * Safely decodes a URI component with `decodeURIComponent`. Returns the URI component unmodified if
 * `decodeURIComponent` throws an error.
 *
 * @param {string} uriComponent URI component to decode.
 *
 * @return {string} Decoded URI component if possible.
 */
function safeDecodeURIComponent(uriComponent) {
  try {
    return decodeURIComponent(uriComponent);
  } catch (uriComponentError) {
    return uriComponent;
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/filter-url-for-display.js
/**
 * Returns a URL for display.
 *
 * @param {string} url Original URL.
 * @param {number|null} maxLength URL length.
 *
 * @example
 * ```js
 * const displayUrl = filterURLForDisplay( 'https://www.wordpress.org/gutenberg/' ); // wordpress.org/gutenberg
 * const imageUrl = filterURLForDisplay( 'https://www.wordpress.org/wp-content/uploads/img.png', 20 ); // …ent/uploads/img.png
 * ```
 *
 * @return {string} Displayed URL.
 */
function filterURLForDisplay(url) {
  var maxLength = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  // Remove protocol and www prefixes.
  var filteredURL = url.replace(/^(?:https?:)\/\/(?:www\.)?/, ''); // Ends with / and only has that single slash, strip it.

  if (filteredURL.match(/^[^\/]+\/$/)) {
    filteredURL = filteredURL.replace('/', '');
  }

  var mediaRegexp = /([\w|:])*\.(?:jpg|jpeg|gif|png|svg)/;

  if (!maxLength || filteredURL.length <= maxLength || !filteredURL.match(mediaRegexp)) {
    return filteredURL;
  } // If the file is not greater than max length, return last portion of URL.


  filteredURL = filteredURL.split('?')[0];
  var urlPieces = filteredURL.split('/');
  var file = urlPieces[urlPieces.length - 1];

  if (file.length <= maxLength) {
    return '…' + filteredURL.slice(-maxLength);
  } // If the file is greater than max length, truncate the file.


  var index = file.lastIndexOf('.');
  var _ref = [file.slice(0, index), file.slice(index + 1)],
      fileName = _ref[0],
      extension = _ref[1];
  var truncatedFile = fileName.slice(-3) + '.' + extension;
  return file.slice(0, maxLength - truncatedFile.length - 1) + '…' + truncatedFile;
}

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/clean-for-slug.js
/**
 * External dependencies
 */

/**
 * Performs some basic cleanup of a string for use as a post slug.
 *
 * This replicates some of what `sanitize_title()` does in WordPress core, but
 * is only designed to approximate what the slug will be.
 *
 * Converts Latin-1 Supplement and Latin Extended-A letters to basic Latin
 * letters. Removes combining diacritical marks. Converts whitespace, periods,
 * and forward slashes to hyphens. Removes any remaining non-word characters
 * except hyphens. Converts remaining string to lowercase. It does not account
 * for octets, HTML entities, or other encoded characters.
 *
 * @param {string} string Title or slug to be processed.
 *
 * @return {string} Processed string.
 */

function cleanForSlug(string) {
  if (!string) {
    return '';
  }

  return Object(external_lodash_["trim"])(Object(external_lodash_["deburr"])(string).replace(/[\s\./]+/g, '-').replace(/[^\w-]+/g, '').toLowerCase(), '-');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/url/build-module/index.js


























/***/ }),

/***/ 5:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _defineProperty; });
function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

/***/ })

/******/ });