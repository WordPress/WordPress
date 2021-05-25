this["wp"] = this["wp"] || {}; this["wp"]["coreData"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "dsJ0");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1OyB":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "25BE":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
}

/***/ }),

/***/ "51Zz":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["dataControls"]; }());

/***/ }),

/***/ "7Cbv":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/rng.js
// Unique ID creation requires a high quality random # generator. In the browser we therefore
// require the crypto API and do not support built-in fallback to lower quality random number
// generators (like Math.random()).
var getRandomValues;
var rnds8 = new Uint8Array(16);
function rng() {
  // lazy load so that environments that need to polyfill have a chance to do so
  if (!getRandomValues) {
    // getRandomValues needs to be invoked in a context where "this" is a Crypto implementation. Also,
    // find the complete implementation of crypto (msCrypto) on IE11.
    getRandomValues = typeof crypto !== 'undefined' && crypto.getRandomValues && crypto.getRandomValues.bind(crypto) || typeof msCrypto !== 'undefined' && typeof msCrypto.getRandomValues === 'function' && msCrypto.getRandomValues.bind(msCrypto);

    if (!getRandomValues) {
      throw new Error('crypto.getRandomValues() not supported. See https://github.com/uuidjs/uuid#getrandomvalues-not-supported');
    }
  }

  return getRandomValues(rnds8);
}
// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/regex.js
/* harmony default export */ var regex = (/^(?:[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}|00000000-0000-0000-0000-000000000000)$/i);
// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/validate.js


function validate(uuid) {
  return typeof uuid === 'string' && regex.test(uuid);
}

/* harmony default export */ var esm_browser_validate = (validate);
// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/stringify.js

/**
 * Convert array of 16 byte values to UUID string format of the form:
 * XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
 */

var byteToHex = [];

for (var stringify_i = 0; stringify_i < 256; ++stringify_i) {
  byteToHex.push((stringify_i + 0x100).toString(16).substr(1));
}

function stringify(arr) {
  var offset = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
  // Note: Be careful editing this code!  It's been tuned for performance
  // and works in ways you may not expect. See https://github.com/uuidjs/uuid/pull/434
  var uuid = (byteToHex[arr[offset + 0]] + byteToHex[arr[offset + 1]] + byteToHex[arr[offset + 2]] + byteToHex[arr[offset + 3]] + '-' + byteToHex[arr[offset + 4]] + byteToHex[arr[offset + 5]] + '-' + byteToHex[arr[offset + 6]] + byteToHex[arr[offset + 7]] + '-' + byteToHex[arr[offset + 8]] + byteToHex[arr[offset + 9]] + '-' + byteToHex[arr[offset + 10]] + byteToHex[arr[offset + 11]] + byteToHex[arr[offset + 12]] + byteToHex[arr[offset + 13]] + byteToHex[arr[offset + 14]] + byteToHex[arr[offset + 15]]).toLowerCase(); // Consistency check for valid UUID.  If this throws, it's likely due to one
  // of the following:
  // - One or more input array values don't map to a hex octet (leading to
  // "undefined" in the uuid)
  // - Invalid input values for the RFC `version` or `variant` fields

  if (!esm_browser_validate(uuid)) {
    throw TypeError('Stringified UUID is invalid');
  }

  return uuid;
}

/* harmony default export */ var esm_browser_stringify = (stringify);
// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/v4.js



function v4(options, buf, offset) {
  options = options || {};
  var rnds = options.random || (options.rng || rng)(); // Per 4.4, set bits for version and `clock_seq_hi_and_reserved`

  rnds[6] = rnds[6] & 0x0f | 0x40;
  rnds[8] = rnds[8] & 0x3f | 0x80; // Copy bytes to buffer, if provided

  if (buf) {
    offset = offset || 0;

    for (var i = 0; i < 16; ++i) {
      buf[offset + i] = rnds[i];
    }

    return buf;
  }

  return esm_browser_stringify(rnds);
}

/* harmony default export */ var esm_browser_v4 = __webpack_exports__["a"] = (v4);

/***/ }),

/***/ "BsWD":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _unsupportedIterableToArray; });
/* harmony import */ var _babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("a3WO");

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return Object(_babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return Object(_babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
}

/***/ }),

/***/ "DSFK":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayWithHoles; });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),

/***/ "FtRg":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


function _typeof(obj) {
  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function (obj) {
      return typeof obj;
    };
  } else {
    _typeof = function (obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

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

/**
 * Given an instance of EquivalentKeyMap, returns its internal value pair tuple
 * for a key, if one exists. The tuple members consist of the last reference
 * value for the key (used in efficient subsequent lookups) and the value
 * assigned for the key at the leaf node.
 *
 * @param {EquivalentKeyMap} instance EquivalentKeyMap instance.
 * @param {*} key                     The key for which to return value pair.
 *
 * @return {?Array} Value pair, if exists.
 */
function getValuePair(instance, key) {
  var _map = instance._map,
      _arrayTreeMap = instance._arrayTreeMap,
      _objectTreeMap = instance._objectTreeMap; // Map keeps a reference to the last object-like key used to set the
  // value, which can be used to shortcut immediately to the value.

  if (_map.has(key)) {
    return _map.get(key);
  } // Sort keys to ensure stable retrieval from tree.


  var properties = Object.keys(key).sort(); // Tree by type to avoid conflicts on numeric object keys, empty value.

  var map = Array.isArray(key) ? _arrayTreeMap : _objectTreeMap;

  for (var i = 0; i < properties.length; i++) {
    var property = properties[i];
    map = map.get(property);

    if (map === undefined) {
      return;
    }

    var propertyValue = key[property];
    map = map.get(propertyValue);

    if (map === undefined) {
      return;
    }
  }

  var valuePair = map.get('_ekm_value');

  if (!valuePair) {
    return;
  } // If reached, it implies that an object-like key was set with another
  // reference, so delete the reference and replace with the current.


  _map.delete(valuePair[0]);

  valuePair[0] = key;
  map.set('_ekm_value', valuePair);

  _map.set(key, valuePair);

  return valuePair;
}
/**
 * Variant of a Map object which enables lookup by equivalent (deeply equal)
 * object and array keys.
 */


var EquivalentKeyMap =
/*#__PURE__*/
function () {
  /**
   * Constructs a new instance of EquivalentKeyMap.
   *
   * @param {Iterable.<*>} iterable Initial pair of key, value for map.
   */
  function EquivalentKeyMap(iterable) {
    _classCallCheck(this, EquivalentKeyMap);

    this.clear();

    if (iterable instanceof EquivalentKeyMap) {
      // Map#forEach is only means of iterating with support for IE11.
      var iterablePairs = [];
      iterable.forEach(function (value, key) {
        iterablePairs.push([key, value]);
      });
      iterable = iterablePairs;
    }

    if (iterable != null) {
      for (var i = 0; i < iterable.length; i++) {
        this.set(iterable[i][0], iterable[i][1]);
      }
    }
  }
  /**
   * Accessor property returning the number of elements.
   *
   * @return {number} Number of elements.
   */


  _createClass(EquivalentKeyMap, [{
    key: "set",

    /**
     * Add or update an element with a specified key and value.
     *
     * @param {*} key   The key of the element to add.
     * @param {*} value The value of the element to add.
     *
     * @return {EquivalentKeyMap} Map instance.
     */
    value: function set(key, value) {
      // Shortcut non-object-like to set on internal Map.
      if (key === null || _typeof(key) !== 'object') {
        this._map.set(key, value);

        return this;
      } // Sort keys to ensure stable assignment into tree.


      var properties = Object.keys(key).sort();
      var valuePair = [key, value]; // Tree by type to avoid conflicts on numeric object keys, empty value.

      var map = Array.isArray(key) ? this._arrayTreeMap : this._objectTreeMap;

      for (var i = 0; i < properties.length; i++) {
        var property = properties[i];

        if (!map.has(property)) {
          map.set(property, new EquivalentKeyMap());
        }

        map = map.get(property);
        var propertyValue = key[property];

        if (!map.has(propertyValue)) {
          map.set(propertyValue, new EquivalentKeyMap());
        }

        map = map.get(propertyValue);
      } // If an _ekm_value exists, there was already an equivalent key. Before
      // overriding, ensure that the old key reference is removed from map to
      // avoid memory leak of accumulating equivalent keys. This is, in a
      // sense, a poor man's WeakMap, while still enabling iterability.


      var previousValuePair = map.get('_ekm_value');

      if (previousValuePair) {
        this._map.delete(previousValuePair[0]);
      }

      map.set('_ekm_value', valuePair);

      this._map.set(key, valuePair);

      return this;
    }
    /**
     * Returns a specified element.
     *
     * @param {*} key The key of the element to return.
     *
     * @return {?*} The element associated with the specified key or undefined
     *              if the key can't be found.
     */

  }, {
    key: "get",
    value: function get(key) {
      // Shortcut non-object-like to get from internal Map.
      if (key === null || _typeof(key) !== 'object') {
        return this._map.get(key);
      }

      var valuePair = getValuePair(this, key);

      if (valuePair) {
        return valuePair[1];
      }
    }
    /**
     * Returns a boolean indicating whether an element with the specified key
     * exists or not.
     *
     * @param {*} key The key of the element to test for presence.
     *
     * @return {boolean} Whether an element with the specified key exists.
     */

  }, {
    key: "has",
    value: function has(key) {
      if (key === null || _typeof(key) !== 'object') {
        return this._map.has(key);
      } // Test on the _presence_ of the pair, not its value, as even undefined
      // can be a valid member value for a key.


      return getValuePair(this, key) !== undefined;
    }
    /**
     * Removes the specified element.
     *
     * @param {*} key The key of the element to remove.
     *
     * @return {boolean} Returns true if an element existed and has been
     *                   removed, or false if the element does not exist.
     */

  }, {
    key: "delete",
    value: function _delete(key) {
      if (!this.has(key)) {
        return false;
      } // This naive implementation will leave orphaned child trees. A better
      // implementation should traverse and remove orphans.


      this.set(key, undefined);
      return true;
    }
    /**
     * Executes a provided function once per each key/value pair, in insertion
     * order.
     *
     * @param {Function} callback Function to execute for each element.
     * @param {*}        thisArg  Value to use as `this` when executing
     *                            `callback`.
     */

  }, {
    key: "forEach",
    value: function forEach(callback) {
      var _this = this;

      var thisArg = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this;

      this._map.forEach(function (value, key) {
        // Unwrap value from object-like value pair.
        if (key !== null && _typeof(key) === 'object') {
          value = value[1];
        }

        callback.call(thisArg, value, key, _this);
      });
    }
    /**
     * Removes all elements.
     */

  }, {
    key: "clear",
    value: function clear() {
      this._map = new Map();
      this._arrayTreeMap = new Map();
      this._objectTreeMap = new Map();
    }
  }, {
    key: "size",
    get: function get() {
      return this._map.size;
    }
  }]);

  return EquivalentKeyMap;
}();

module.exports = EquivalentKeyMap;


/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "HSyU":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blocks"]; }());

/***/ }),

/***/ "HaE+":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _asyncToGenerator; });
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
  try {
    var info = gen[key](arg);
    var value = info.value;
  } catch (error) {
    reject(error);
    return;
  }

  if (info.done) {
    resolve(value);
  } else {
    Promise.resolve(value).then(_next, _throw);
  }
}

function _asyncToGenerator(fn) {
  return function () {
    var self = this,
        args = arguments;
    return new Promise(function (resolve, reject) {
      var gen = fn.apply(self, args);

      function _next(value) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
      }

      function _throw(err) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
      }

      _next(undefined);
    });
  };
}

/***/ }),

/***/ "KQm4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _toConsumableArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js
var arrayLikeToArray = __webpack_require__("a3WO");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return Object(arrayLikeToArray["a" /* default */])(arr);
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
var iterableToArray = __webpack_require__("25BE");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
var unsupportedIterableToArray = __webpack_require__("BsWD");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js




function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || Object(iterableToArray["a" /* default */])(arr) || Object(unsupportedIterableToArray["a" /* default */])(arr) || _nonIterableSpread();
}

/***/ }),

/***/ "Mmq9":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["url"]; }());

/***/ }),

/***/ "NMb1":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["deprecated"]; }());

/***/ }),

/***/ "ODXe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _slicedToArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
var arrayWithHoles = __webpack_require__("DSFK");

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
var unsupportedIterableToArray = __webpack_require__("BsWD");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
var nonIterableRest = __webpack_require__("PYwp");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js




function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(unsupportedIterableToArray["a" /* default */])(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ "PYwp":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _nonIterableRest; });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

/***/ }),

/***/ "T5bk":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _toArray; });
/* harmony import */ var _babel_runtime_helpers_esm_arrayWithHoles__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("DSFK");
/* harmony import */ var _babel_runtime_helpers_esm_iterableToArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("25BE");
/* harmony import */ var _babel_runtime_helpers_esm_unsupportedIterableToArray__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__("BsWD");
/* harmony import */ var _babel_runtime_helpers_esm_nonIterableRest__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__("PYwp");




function _toArray(arr) {
  return Object(_babel_runtime_helpers_esm_arrayWithHoles__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(arr) || Object(_babel_runtime_helpers_esm_iterableToArray__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(arr) || Object(_babel_runtime_helpers_esm_unsupportedIterableToArray__WEBPACK_IMPORTED_MODULE_2__[/* default */ "a"])(arr) || Object(_babel_runtime_helpers_esm_nonIterableRest__WEBPACK_IMPORTED_MODULE_3__[/* default */ "a"])();
}

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "a3WO":
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

/***/ "dsJ0":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "store", function() { return /* binding */ build_module_store; });
__webpack_require__.d(__webpack_exports__, "EntityProvider", function() { return /* reexport */ EntityProvider; });
__webpack_require__.d(__webpack_exports__, "useEntityId", function() { return /* reexport */ useEntityId; });
__webpack_require__.d(__webpack_exports__, "useEntityProp", function() { return /* reexport */ useEntityProp; });
__webpack_require__.d(__webpack_exports__, "useEntityBlockEditor", function() { return /* reexport */ useEntityBlockEditor; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/core-data/build-module/locks/actions.js
var locks_actions_namespaceObject = {};
__webpack_require__.r(locks_actions_namespaceObject);
__webpack_require__.d(locks_actions_namespaceObject, "__unstableAcquireStoreLock", function() { return __unstableAcquireStoreLock; });
__webpack_require__.d(locks_actions_namespaceObject, "__unstableEnqueueLockRequest", function() { return __unstableEnqueueLockRequest; });
__webpack_require__.d(locks_actions_namespaceObject, "__unstableReleaseStoreLock", function() { return __unstableReleaseStoreLock; });
__webpack_require__.d(locks_actions_namespaceObject, "__unstableProcessPendingLockRequests", function() { return __unstableProcessPendingLockRequests; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/core-data/build-module/actions.js
var build_module_actions_namespaceObject = {};
__webpack_require__.r(build_module_actions_namespaceObject);
__webpack_require__.d(build_module_actions_namespaceObject, "receiveUserQuery", function() { return receiveUserQuery; });
__webpack_require__.d(build_module_actions_namespaceObject, "receiveCurrentUser", function() { return receiveCurrentUser; });
__webpack_require__.d(build_module_actions_namespaceObject, "addEntities", function() { return addEntities; });
__webpack_require__.d(build_module_actions_namespaceObject, "receiveEntityRecords", function() { return receiveEntityRecords; });
__webpack_require__.d(build_module_actions_namespaceObject, "receiveCurrentTheme", function() { return receiveCurrentTheme; });
__webpack_require__.d(build_module_actions_namespaceObject, "receiveThemeSupports", function() { return receiveThemeSupports; });
__webpack_require__.d(build_module_actions_namespaceObject, "receiveEmbedPreview", function() { return receiveEmbedPreview; });
__webpack_require__.d(build_module_actions_namespaceObject, "deleteEntityRecord", function() { return deleteEntityRecord; });
__webpack_require__.d(build_module_actions_namespaceObject, "editEntityRecord", function() { return actions_editEntityRecord; });
__webpack_require__.d(build_module_actions_namespaceObject, "undo", function() { return undo; });
__webpack_require__.d(build_module_actions_namespaceObject, "redo", function() { return redo; });
__webpack_require__.d(build_module_actions_namespaceObject, "__unstableCreateUndoLevel", function() { return actions_unstableCreateUndoLevel; });
__webpack_require__.d(build_module_actions_namespaceObject, "saveEntityRecord", function() { return saveEntityRecord; });
__webpack_require__.d(build_module_actions_namespaceObject, "__experimentalBatch", function() { return __experimentalBatch; });
__webpack_require__.d(build_module_actions_namespaceObject, "saveEditedEntityRecord", function() { return saveEditedEntityRecord; });
__webpack_require__.d(build_module_actions_namespaceObject, "receiveUploadPermissions", function() { return receiveUploadPermissions; });
__webpack_require__.d(build_module_actions_namespaceObject, "receiveUserPermission", function() { return receiveUserPermission; });
__webpack_require__.d(build_module_actions_namespaceObject, "receiveAutosaves", function() { return receiveAutosaves; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/core-data/build-module/selectors.js
var build_module_selectors_namespaceObject = {};
__webpack_require__.r(build_module_selectors_namespaceObject);
__webpack_require__.d(build_module_selectors_namespaceObject, "isRequestingEmbedPreview", function() { return isRequestingEmbedPreview; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getAuthors", function() { return getAuthors; });
__webpack_require__.d(build_module_selectors_namespaceObject, "__unstableGetAuthor", function() { return __unstableGetAuthor; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getCurrentUser", function() { return getCurrentUser; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getUserQueryResults", function() { return getUserQueryResults; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getEntitiesByKind", function() { return getEntitiesByKind; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getEntity", function() { return selectors_getEntity; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getEntityRecord", function() { return getEntityRecord; });
__webpack_require__.d(build_module_selectors_namespaceObject, "__experimentalGetEntityRecordNoResolver", function() { return __experimentalGetEntityRecordNoResolver; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getRawEntityRecord", function() { return getRawEntityRecord; });
__webpack_require__.d(build_module_selectors_namespaceObject, "hasEntityRecords", function() { return hasEntityRecords; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getEntityRecords", function() { return getEntityRecords; });
__webpack_require__.d(build_module_selectors_namespaceObject, "__experimentalGetDirtyEntityRecords", function() { return __experimentalGetDirtyEntityRecords; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getEntityRecordEdits", function() { return getEntityRecordEdits; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getEntityRecordNonTransientEdits", function() { return getEntityRecordNonTransientEdits; });
__webpack_require__.d(build_module_selectors_namespaceObject, "hasEditsForEntityRecord", function() { return hasEditsForEntityRecord; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getEditedEntityRecord", function() { return getEditedEntityRecord; });
__webpack_require__.d(build_module_selectors_namespaceObject, "isAutosavingEntityRecord", function() { return isAutosavingEntityRecord; });
__webpack_require__.d(build_module_selectors_namespaceObject, "isSavingEntityRecord", function() { return isSavingEntityRecord; });
__webpack_require__.d(build_module_selectors_namespaceObject, "isDeletingEntityRecord", function() { return isDeletingEntityRecord; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getLastEntitySaveError", function() { return getLastEntitySaveError; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getLastEntityDeleteError", function() { return getLastEntityDeleteError; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getUndoEdit", function() { return getUndoEdit; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getRedoEdit", function() { return getRedoEdit; });
__webpack_require__.d(build_module_selectors_namespaceObject, "hasUndo", function() { return hasUndo; });
__webpack_require__.d(build_module_selectors_namespaceObject, "hasRedo", function() { return hasRedo; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getCurrentTheme", function() { return getCurrentTheme; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getThemeSupports", function() { return getThemeSupports; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getEmbedPreview", function() { return getEmbedPreview; });
__webpack_require__.d(build_module_selectors_namespaceObject, "isPreviewEmbedFallback", function() { return isPreviewEmbedFallback; });
__webpack_require__.d(build_module_selectors_namespaceObject, "hasUploadPermissions", function() { return hasUploadPermissions; });
__webpack_require__.d(build_module_selectors_namespaceObject, "canUser", function() { return canUser; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getAutosaves", function() { return getAutosaves; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getAutosave", function() { return getAutosave; });
__webpack_require__.d(build_module_selectors_namespaceObject, "hasFetchedAutosaves", function() { return hasFetchedAutosaves; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getReferenceByDistinctEdits", function() { return getReferenceByDistinctEdits; });
__webpack_require__.d(build_module_selectors_namespaceObject, "__experimentalGetTemplateForLink", function() { return __experimentalGetTemplateForLink; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/core-data/build-module/resolvers.js
var resolvers_namespaceObject = {};
__webpack_require__.r(resolvers_namespaceObject);
__webpack_require__.d(resolvers_namespaceObject, "getAuthors", function() { return resolvers_getAuthors; });
__webpack_require__.d(resolvers_namespaceObject, "__unstableGetAuthor", function() { return resolvers_unstableGetAuthor; });
__webpack_require__.d(resolvers_namespaceObject, "getCurrentUser", function() { return resolvers_getCurrentUser; });
__webpack_require__.d(resolvers_namespaceObject, "getEntityRecord", function() { return resolvers_getEntityRecord; });
__webpack_require__.d(resolvers_namespaceObject, "getRawEntityRecord", function() { return resolvers_getRawEntityRecord; });
__webpack_require__.d(resolvers_namespaceObject, "getEditedEntityRecord", function() { return resolvers_getEditedEntityRecord; });
__webpack_require__.d(resolvers_namespaceObject, "getEntityRecords", function() { return resolvers_getEntityRecords; });
__webpack_require__.d(resolvers_namespaceObject, "getCurrentTheme", function() { return resolvers_getCurrentTheme; });
__webpack_require__.d(resolvers_namespaceObject, "getThemeSupports", function() { return resolvers_getThemeSupports; });
__webpack_require__.d(resolvers_namespaceObject, "getEmbedPreview", function() { return resolvers_getEmbedPreview; });
__webpack_require__.d(resolvers_namespaceObject, "hasUploadPermissions", function() { return resolvers_hasUploadPermissions; });
__webpack_require__.d(resolvers_namespaceObject, "canUser", function() { return resolvers_canUser; });
__webpack_require__.d(resolvers_namespaceObject, "getAutosaves", function() { return resolvers_getAutosaves; });
__webpack_require__.d(resolvers_namespaceObject, "getAutosave", function() { return resolvers_getAutosave; });
__webpack_require__.d(resolvers_namespaceObject, "__experimentalGetTemplateForLink", function() { return resolvers_experimentalGetTemplateForLink; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/core-data/build-module/locks/selectors.js
var locks_selectors_namespaceObject = {};
__webpack_require__.r(locks_selectors_namespaceObject);
__webpack_require__.d(locks_selectors_namespaceObject, "__unstableGetPendingLockRequests", function() { return __unstableGetPendingLockRequests; });
__webpack_require__.d(locks_selectors_namespaceObject, "__unstableIsLockAvailable", function() { return __unstableIsLockAvailable; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__("rePB");

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external ["wp","dataControls"]
var external_wp_dataControls_ = __webpack_require__("51Zz");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__("ODXe");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__("KQm4");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external ["wp","isShallowEqual"]
var external_wp_isShallowEqual_ = __webpack_require__("rl8x");
var external_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_wp_isShallowEqual_);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/utils/if-matching-action.js
/**
 * A higher-order reducer creator which invokes the original reducer only if
 * the dispatching action matches the given predicate, **OR** if state is
 * initializing (undefined).
 *
 * @param {Function} isMatch Function predicate for allowing reducer call.
 *
 * @return {Function} Higher-order reducer.
 */
var ifMatchingAction = function ifMatchingAction(isMatch) {
  return function (reducer) {
    return function (state, action) {
      if (state === undefined || isMatch(action)) {
        return reducer(state, action);
      }

      return state;
    };
  };
};

/* harmony default export */ var if_matching_action = (ifMatchingAction);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/utils/replace-action.js
/**
 * Higher-order reducer creator which substitutes the action object before
 * passing to the original reducer.
 *
 * @param {Function} replacer Function mapping original action to replacement.
 *
 * @return {Function} Higher-order reducer.
 */
var replaceAction = function replaceAction(replacer) {
  return function (reducer) {
    return function (state, action) {
      return reducer(state, replacer(action));
    };
  };
};

/* harmony default export */ var replace_action = (replaceAction);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/utils/conservative-map-item.js
/**
 * External dependencies
 */

/**
 * Given the current and next item entity, returns the minimally "modified"
 * result of the next item, preferring value references from the original item
 * if equal. If all values match, the original item is returned.
 *
 * @param {Object} item     Original item.
 * @param {Object} nextItem Next item.
 *
 * @return {Object} Minimally modified merged item.
 */

function conservativeMapItem(item, nextItem) {
  // Return next item in its entirety if there is no original item.
  if (!item) {
    return nextItem;
  }

  var hasChanges = false;
  var result = {};

  for (var key in nextItem) {
    if (Object(external_lodash_["isEqual"])(item[key], nextItem[key])) {
      result[key] = item[key];
    } else {
      hasChanges = true;
      result[key] = nextItem[key];
    }
  }

  if (!hasChanges) {
    return item;
  } // Only at this point, backfill properties from the original item which
  // weren't explicitly set into the result above. This is an optimization
  // to allow `hasChanges` to return early.


  for (var _key in item) {
    if (!result.hasOwnProperty(_key)) {
      result[_key] = item[_key];
    }
  }

  return result;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/utils/on-sub-key.js


function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Higher-order reducer creator which creates a combined reducer object, keyed
 * by a property on the action object.
 *
 * @param {string} actionProperty Action property by which to key object.
 *
 * @return {Function} Higher-order reducer.
 */
var on_sub_key_onSubKey = function onSubKey(actionProperty) {
  return function (reducer) {
    return function () {
      var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var action = arguments.length > 1 ? arguments[1] : undefined;
      // Retrieve subkey from action. Do not track if undefined; useful for cases
      // where reducer is scoped by action shape.
      var key = action[actionProperty];

      if (key === undefined) {
        return state;
      } // Avoid updating state if unchanged. Note that this also accounts for a
      // reducer which returns undefined on a key which is not yet tracked.


      var nextKeyState = reducer(state[key], action);

      if (nextKeyState === state[key]) {
        return state;
      }

      return _objectSpread(_objectSpread({}, state), {}, Object(defineProperty["a" /* default */])({}, key, nextKeyState));
    };
  };
};
/* harmony default export */ var on_sub_key = (on_sub_key_onSubKey);

// EXTERNAL MODULE: external "regeneratorRuntime"
var external_regeneratorRuntime_ = __webpack_require__("dvlR");
var external_regeneratorRuntime_default = /*#__PURE__*/__webpack_require__.n(external_regeneratorRuntime_);

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toArray.js
var toArray = __webpack_require__("T5bk");

// EXTERNAL MODULE: ./node_modules/uuid/dist/esm-browser/v4.js + 4 modules
var v4 = __webpack_require__("7Cbv");

// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__("Mmq9");

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/queried-data/actions.js


function actions_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function actions_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { actions_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { actions_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */

/**
 * Returns an action object used in signalling that items have been received.
 *
 * @param {Array}   items Items received.
 * @param {?Object} edits Optional edits to reset.
 *
 * @return {Object} Action object.
 */

function receiveItems(items, edits) {
  return {
    type: 'RECEIVE_ITEMS',
    items: Object(external_lodash_["castArray"])(items),
    persistedEdits: edits
  };
}
/**
 * Returns an action object used in signalling that entity records have been
 * deleted and they need to be removed from entities state.
 *
 * @param {string}       kind             Kind of the removed entities.
 * @param {string}       name             Name of the removed entities.
 * @param {Array|number} records          Record IDs of the removed entities.
 * @param {boolean}      invalidateCache  Controls whether we want to invalidate the cache.
 * @return {Object} Action object.
 */

function removeItems(kind, name, records) {
  var invalidateCache = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
  return {
    type: 'REMOVE_ITEMS',
    itemIds: Object(external_lodash_["castArray"])(records),
    kind: kind,
    name: name,
    invalidateCache: invalidateCache
  };
}
/**
 * Returns an action object used in signalling that queried data has been
 * received.
 *
 * @param {Array}   items Queried items received.
 * @param {?Object} query Optional query object.
 * @param {?Object} edits Optional edits to reset.
 *
 * @return {Object} Action object.
 */

function receiveQueriedItems(items) {
  var query = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var edits = arguments.length > 2 ? arguments[2] : undefined;
  return actions_objectSpread(actions_objectSpread({}, receiveItems(items, edits)), {}, {
    query: query
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/locks/actions.js


function _createForOfIteratorHelper(o, allowArrayLike) { var it; if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

var _marked = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(__unstableAcquireStoreLock),
    _marked2 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(__unstableEnqueueLockRequest),
    _marked3 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(__unstableReleaseStoreLock),
    _marked4 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(__unstableProcessPendingLockRequests);

/**
 * WordPress dependencies
 */


function __unstableAcquireStoreLock(store, path, _ref) {
  var exclusive, promise;
  return external_regeneratorRuntime_default.a.wrap(function __unstableAcquireStoreLock$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          exclusive = _ref.exclusive;
          return _context.delegateYield(__unstableEnqueueLockRequest(store, path, {
            exclusive: exclusive
          }), "t0", 2);

        case 2:
          promise = _context.t0;
          return _context.delegateYield(__unstableProcessPendingLockRequests(), "t1", 4);

        case 4:
          _context.next = 6;
          return Object(external_wp_dataControls_["__unstableAwaitPromise"])(promise);

        case 6:
          return _context.abrupt("return", _context.sent);

        case 7:
        case "end":
          return _context.stop();
      }
    }
  }, _marked);
}
function __unstableEnqueueLockRequest(store, path, _ref2) {
  var exclusive, notifyAcquired, promise;
  return external_regeneratorRuntime_default.a.wrap(function __unstableEnqueueLockRequest$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          exclusive = _ref2.exclusive;
          promise = new Promise(function (resolve) {
            notifyAcquired = resolve;
          });
          _context2.next = 4;
          return {
            type: 'ENQUEUE_LOCK_REQUEST',
            request: {
              store: store,
              path: path,
              exclusive: exclusive,
              notifyAcquired: notifyAcquired
            }
          };

        case 4:
          return _context2.abrupt("return", promise);

        case 5:
        case "end":
          return _context2.stop();
      }
    }
  }, _marked2);
}
function __unstableReleaseStoreLock(lock) {
  return external_regeneratorRuntime_default.a.wrap(function __unstableReleaseStoreLock$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          _context3.next = 2;
          return {
            type: 'RELEASE_LOCK',
            lock: lock
          };

        case 2:
          return _context3.delegateYield(__unstableProcessPendingLockRequests(), "t0", 3);

        case 3:
        case "end":
          return _context3.stop();
      }
    }
  }, _marked3);
}
function __unstableProcessPendingLockRequests() {
  var lockRequests, _iterator, _step, request, store, path, exclusive, notifyAcquired, isAvailable, lock;

  return external_regeneratorRuntime_default.a.wrap(function __unstableProcessPendingLockRequests$(_context4) {
    while (1) {
      switch (_context4.prev = _context4.next) {
        case 0:
          _context4.next = 2;
          return {
            type: 'PROCESS_PENDING_LOCK_REQUESTS'
          };

        case 2:
          _context4.next = 4;
          return external_wp_data_["controls"].select('core', '__unstableGetPendingLockRequests');

        case 4:
          lockRequests = _context4.sent;
          _iterator = _createForOfIteratorHelper(lockRequests);
          _context4.prev = 6;

          _iterator.s();

        case 8:
          if ((_step = _iterator.n()).done) {
            _context4.next = 21;
            break;
          }

          request = _step.value;
          store = request.store, path = request.path, exclusive = request.exclusive, notifyAcquired = request.notifyAcquired;
          _context4.next = 13;
          return external_wp_data_["controls"].select('core', '__unstableIsLockAvailable', store, path, {
            exclusive: exclusive
          });

        case 13:
          isAvailable = _context4.sent;

          if (!isAvailable) {
            _context4.next = 19;
            break;
          }

          lock = {
            store: store,
            path: path,
            exclusive: exclusive
          };
          _context4.next = 18;
          return {
            type: 'GRANT_LOCK_REQUEST',
            lock: lock,
            request: request
          };

        case 18:
          notifyAcquired(lock);

        case 19:
          _context4.next = 8;
          break;

        case 21:
          _context4.next = 26;
          break;

        case 23:
          _context4.prev = 23;
          _context4.t0 = _context4["catch"](6);

          _iterator.e(_context4.t0);

        case 26:
          _context4.prev = 26;

          _iterator.f();

          return _context4.finish(26);

        case 29:
        case "end":
          return _context4.stop();
      }
    }
  }, _marked4, null, [[6, 23, 26, 29]]);
}

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js
var setPrototypeOf = __webpack_require__("s4An");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/isNativeReflectConstruct.js
function _isNativeReflectConstruct() {
  if (typeof Reflect === "undefined" || !Reflect.construct) return false;
  if (Reflect.construct.sham) return false;
  if (typeof Proxy === "function") return true;

  try {
    Date.prototype.toString.call(Reflect.construct(Date, [], function () {}));
    return true;
  } catch (e) {
    return false;
  }
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/construct.js


function construct_construct(Parent, args, Class) {
  if (_isNativeReflectConstruct()) {
    construct_construct = Reflect.construct;
  } else {
    construct_construct = function _construct(Parent, args, Class) {
      var a = [null];
      a.push.apply(a, args);
      var Constructor = Function.bind.apply(Parent, a);
      var instance = new Constructor();
      if (Class) Object(setPrototypeOf["a" /* default */])(instance, Class.prototype);
      return instance;
    };
  }

  return construct_construct.apply(null, arguments);
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__("1OyB");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__("vuIU");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js
var asyncToGenerator = __webpack_require__("HaE+");

// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__("ywyh");
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/batch/default-processor.js



/**
 * WordPress dependencies
 */

/**
 * Default batch processor. Sends its input requests to /v1/batch.
 *
 * @param {Array} requests List of API requests to perform at once.
 *
 * @return {Promise} Promise that resolves to a list of objects containing
 *                   either `output` (if that request was succesful) or `error`
 *                   (if not ).
 */

function defaultProcessor(_x) {
  return _defaultProcessor.apply(this, arguments);
}

function _defaultProcessor() {
  _defaultProcessor = Object(asyncToGenerator["a" /* default */])( /*#__PURE__*/external_regeneratorRuntime_default.a.mark(function _callee(requests) {
    var batchResponse;
    return external_regeneratorRuntime_default.a.wrap(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            _context.next = 2;
            return external_wp_apiFetch_default()({
              path: '/v1/batch',
              method: 'POST',
              data: {
                validation: 'require-all-validate',
                requests: requests.map(function (request) {
                  return {
                    path: request.path,
                    body: request.data,
                    // Rename 'data' to 'body'.
                    method: request.method,
                    headers: request.headers
                  };
                })
              }
            });

          case 2:
            batchResponse = _context.sent;

            if (!batchResponse.failed) {
              _context.next = 5;
              break;
            }

            return _context.abrupt("return", batchResponse.responses.map(function (response) {
              return {
                error: response === null || response === void 0 ? void 0 : response.body
              };
            }));

          case 5:
            return _context.abrupt("return", batchResponse.responses.map(function (response) {
              var result = {};

              if (response.status >= 200 && response.status < 300) {
                result.output = response.body;
              } else {
                result.error = response.body;
              }

              return result;
            }));

          case 6:
          case "end":
            return _context.stop();
        }
      }
    }, _callee);
  }));
  return _defaultProcessor.apply(this, arguments);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/batch/create-batch.js







function create_batch_createForOfIteratorHelper(o, allowArrayLike) { var it; if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (it = create_batch_unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }

function create_batch_unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return create_batch_arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return create_batch_arrayLikeToArray(o, minLen); }

function create_batch_arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Creates a batch, which can be used to combine multiple API requests into one
 * API request using the WordPress batch processing API (/v1/batch).
 *
 * ```
 * const batch = createBatch();
 * const dunePromise = batch.add( {
 *   path: '/v1/books',
 *   method: 'POST',
 *   data: { title: 'Dune' }
 * } );
 * const lotrPromise = batch.add( {
 *   path: '/v1/books',
 *   method: 'POST',
 *   data: { title: 'Lord of the Rings' }
 * } );
 * const isSuccess = await batch.run(); // Sends one POST to /v1/batch.
 * if ( isSuccess ) {
 *   console.log(
 *     'Saved two books:',
 *     await dunePromise,
 *     await lotrPromise
 *   );
 * }
 * ```
 *
 * @param {Function} [processor] Processor function. Can be used to replace the
 *                               default functionality which is to send an API
 *                               request to /v1/batch. Is given an array of
 *                               inputs and must return a promise that
 *                               resolves to an array of objects containing
 *                               either `output` or `error`.
 */

function createBatch() {
  var processor = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : defaultProcessor;
  var lastId = 0;
  var queue = [];
  var pending = new create_batch_ObservableSet();
  return {
    /**
     * Adds an input to the batch and returns a promise that is resolved or
     * rejected when the input is processed by `batch.run()`.
     *
     * You may also pass a thunk which allows inputs to be added
     * asychronously.
     *
     * ```
     * // Both are allowed:
     * batch.add( { path: '/v1/books', ... } );
     * batch.add( ( add ) => add( { path: '/v1/books', ... } ) );
     * ```
     *
     * If a thunk is passed, `batch.run()` will pause until either:
     *
     * - The thunk calls its `add` argument, or;
     * - The thunk returns a promise and that promise resolves, or;
     * - The thunk returns a non-promise.
     *
     * @param {any|Function} inputOrThunk Input to add or thunk to execute.
     
     * @return {Promise|any} If given an input, returns a promise that
     *                       is resolved or rejected when the batch is
     *                       processed. If given a thunk, returns the return
     *                       value of that thunk.
     */
    add: function add(inputOrThunk) {
      var id = ++lastId;
      pending.add(id);

      var add = function add(input) {
        return new Promise(function (resolve, reject) {
          queue.push({
            input: input,
            resolve: resolve,
            reject: reject
          });
          pending.delete(id);
        });
      };

      if (Object(external_lodash_["isFunction"])(inputOrThunk)) {
        return Promise.resolve(inputOrThunk(add)).finally(function () {
          pending.delete(id);
        });
      }

      return add(inputOrThunk);
    },

    /**
     * Runs the batch. This calls `batchProcessor` and resolves or rejects
     * all promises returned by `add()`.
     *
     * @return {Promise} A promise that resolves to a boolean that is true
     *                   if the processor returned no errors.
     */
    run: function run() {
      return Object(asyncToGenerator["a" /* default */])( /*#__PURE__*/external_regeneratorRuntime_default.a.mark(function _callee() {
        var results, _iterator, _step, reject, isSuccess, _iterator2, _step2, _step2$value, result, _step2$value$, resolve, _reject, _result$output;

        return external_regeneratorRuntime_default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                if (!pending.size) {
                  _context.next = 3;
                  break;
                }

                _context.next = 3;
                return new Promise(function (resolve) {
                  var unsubscribe = pending.subscribe(function () {
                    if (!pending.size) {
                      unsubscribe();
                      resolve();
                    }
                  });
                });

              case 3:
                _context.prev = 3;
                _context.next = 6;
                return processor(queue.map(function (_ref) {
                  var input = _ref.input;
                  return input;
                }));

              case 6:
                results = _context.sent;

                if (!(results.length !== queue.length)) {
                  _context.next = 9;
                  break;
                }

                throw new Error('run: Array returned by processor must be same size as input array.');

              case 9:
                _context.next = 16;
                break;

              case 11:
                _context.prev = 11;
                _context.t0 = _context["catch"](3);
                _iterator = create_batch_createForOfIteratorHelper(queue);

                try {
                  for (_iterator.s(); !(_step = _iterator.n()).done;) {
                    reject = _step.value.reject;
                    reject(_context.t0);
                  }
                } catch (err) {
                  _iterator.e(err);
                } finally {
                  _iterator.f();
                }

                throw _context.t0;

              case 16:
                isSuccess = true;
                _iterator2 = create_batch_createForOfIteratorHelper(Object(external_lodash_["zip"])(results, queue));

                try {
                  for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
                    _step2$value = Object(slicedToArray["a" /* default */])(_step2.value, 2), result = _step2$value[0], _step2$value$ = _step2$value[1], resolve = _step2$value$.resolve, _reject = _step2$value$.reject;

                    if (result !== null && result !== void 0 && result.error) {
                      _reject(result.error);

                      isSuccess = false;
                    } else {
                      resolve((_result$output = result === null || result === void 0 ? void 0 : result.output) !== null && _result$output !== void 0 ? _result$output : result);
                    }
                  }
                } catch (err) {
                  _iterator2.e(err);
                } finally {
                  _iterator2.f();
                }

                queue = [];
                return _context.abrupt("return", isSuccess);

              case 21:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, null, [[3, 11]]);
      }))();
    }
  };
}

var create_batch_ObservableSet = /*#__PURE__*/function () {
  function ObservableSet() {
    Object(classCallCheck["a" /* default */])(this, ObservableSet);

    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    this.set = construct_construct(Set, args);
    this.subscribers = new Set();
  }

  Object(createClass["a" /* default */])(ObservableSet, [{
    key: "add",
    value: function add() {
      var _this$set;

      (_this$set = this.set).add.apply(_this$set, arguments);

      this.subscribers.forEach(function (subscriber) {
        return subscriber();
      });
      return this;
    }
  }, {
    key: "delete",
    value: function _delete() {
      var _this$set2;

      var isSuccess = (_this$set2 = this.set).delete.apply(_this$set2, arguments);

      this.subscribers.forEach(function (subscriber) {
        return subscriber();
      });
      return isSuccess;
    }
  }, {
    key: "subscribe",
    value: function subscribe(subscriber) {
      var _this = this;

      this.subscribers.add(subscriber);
      return function () {
        _this.subscribers.delete(subscriber);
      };
    }
  }, {
    key: "size",
    get: function get() {
      return this.set.size;
    }
  }]);

  return ObservableSet;
}();

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/controls.js



/**
 * WordPress dependencies
 */

function regularFetch(url) {
  return {
    type: 'REGULAR_FETCH',
    url: url
  };
}
function getDispatch() {
  return {
    type: 'GET_DISPATCH'
  };
}
var controls = {
  REGULAR_FETCH: function REGULAR_FETCH(_ref) {
    return Object(asyncToGenerator["a" /* default */])( /*#__PURE__*/external_regeneratorRuntime_default.a.mark(function _callee() {
      var url, _yield$window$fetch$t, data;

      return external_regeneratorRuntime_default.a.wrap(function _callee$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              url = _ref.url;
              _context.next = 3;
              return window.fetch(url).then(function (res) {
                return res.json();
              });

            case 3:
              _yield$window$fetch$t = _context.sent;
              data = _yield$window$fetch$t.data;
              return _context.abrupt("return", data);

            case 6:
            case "end":
              return _context.stop();
          }
        }
      }, _callee);
    }))();
  },
  GET_DISPATCH: Object(external_wp_data_["createRegistryControl"])(function (_ref2) {
    var dispatch = _ref2.dispatch;
    return function () {
      return dispatch;
    };
  })
};
/* harmony default export */ var build_module_controls = (controls);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/actions.js






var actions_marked = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(deleteEntityRecord),
    actions_marked2 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(actions_editEntityRecord),
    actions_marked3 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(undo),
    actions_marked4 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(redo),
    _marked5 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(saveEntityRecord),
    _marked6 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(__experimentalBatch),
    _marked7 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(saveEditedEntityRecord);

function build_module_actions_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function build_module_actions_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { build_module_actions_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { build_module_actions_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */






/**
 * Returns an action object used in signalling that authors have been received.
 *
 * @param {string}       queryID Query ID.
 * @param {Array|Object} users   Users received.
 *
 * @return {Object} Action object.
 */

function receiveUserQuery(queryID, users) {
  return {
    type: 'RECEIVE_USER_QUERY',
    users: Object(external_lodash_["castArray"])(users),
    queryID: queryID
  };
}
/**
 * Returns an action used in signalling that the current user has been received.
 *
 * @param {Object} currentUser Current user object.
 *
 * @return {Object} Action object.
 */

function receiveCurrentUser(currentUser) {
  return {
    type: 'RECEIVE_CURRENT_USER',
    currentUser: currentUser
  };
}
/**
 * Returns an action object used in adding new entities.
 *
 * @param {Array} entities  Entities received.
 *
 * @return {Object} Action object.
 */

function addEntities(entities) {
  return {
    type: 'ADD_ENTITIES',
    entities: entities
  };
}
/**
 * Returns an action object used in signalling that entity records have been received.
 *
 * @param {string}       kind            Kind of the received entity.
 * @param {string}       name            Name of the received entity.
 * @param {Array|Object} records         Records received.
 * @param {?Object}      query           Query Object.
 * @param {?boolean}     invalidateCache Should invalidate query caches.
 * @param {?Object}      edits           Edits to reset.
 * @return {Object} Action object.
 */

function receiveEntityRecords(kind, name, records, query) {
  var invalidateCache = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : false;
  var edits = arguments.length > 5 ? arguments[5] : undefined;

  // Auto drafts should not have titles, but some plugins rely on them so we can't filter this
  // on the server.
  if (kind === 'postType') {
    records = Object(external_lodash_["castArray"])(records).map(function (record) {
      return record.status === 'auto-draft' ? build_module_actions_objectSpread(build_module_actions_objectSpread({}, record), {}, {
        title: ''
      }) : record;
    });
  }

  var action;

  if (query) {
    action = receiveQueriedItems(records, query, edits);
  } else {
    action = receiveItems(records, edits);
  }

  return build_module_actions_objectSpread(build_module_actions_objectSpread({}, action), {}, {
    kind: kind,
    name: name,
    invalidateCache: invalidateCache
  });
}
/**
 * Returns an action object used in signalling that the current theme has been received.
 *
 * @param {Object} currentTheme The current theme.
 *
 * @return {Object} Action object.
 */

function receiveCurrentTheme(currentTheme) {
  return {
    type: 'RECEIVE_CURRENT_THEME',
    currentTheme: currentTheme
  };
}
/**
 * Returns an action object used in signalling that the index has been received.
 *
 * @param {Object} themeSupports Theme support for the current theme.
 *
 * @return {Object} Action object.
 */

function receiveThemeSupports(themeSupports) {
  return {
    type: 'RECEIVE_THEME_SUPPORTS',
    themeSupports: themeSupports
  };
}
/**
 * Returns an action object used in signalling that the preview data for
 * a given URl has been received.
 *
 * @param {string}  url     URL to preview the embed for.
 * @param {*}       preview Preview data.
 *
 * @return {Object} Action object.
 */

function receiveEmbedPreview(url, preview) {
  return {
    type: 'RECEIVE_EMBED_PREVIEW',
    url: url,
    preview: preview
  };
}
/**
 * Action triggered to delete an entity record.
 *
 * @param {string}   kind                      Kind of the deleted entity.
 * @param {string}   name                      Name of the deleted entity.
 * @param {string}   recordId                  Record ID of the deleted entity.
 * @param {?Object}  query                     Special query parameters for the
 *                                             DELETE API call.
 * @param {Object}   [options]                 Delete options.
 * @param {Function} [options.__unstableFetch] Internal use only. Function to
 *                                             call instead of `apiFetch()`.
 *                                             Must return a control descriptor.
 */

function deleteEntityRecord(kind, name, recordId, query) {
  var _ref,
      _ref$__unstableFetch,
      __unstableFetch,
      entities,
      entity,
      error,
      deletedRecord,
      lock,
      path,
      options,
      _args = arguments;

  return external_regeneratorRuntime_default.a.wrap(function deleteEntityRecord$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _ref = _args.length > 4 && _args[4] !== undefined ? _args[4] : {}, _ref$__unstableFetch = _ref.__unstableFetch, __unstableFetch = _ref$__unstableFetch === void 0 ? null : _ref$__unstableFetch;
          _context.next = 3;
          return getKindEntities(kind);

        case 3:
          entities = _context.sent;
          entity = Object(external_lodash_["find"])(entities, {
            kind: kind,
            name: name
          });
          deletedRecord = false;

          if (entity) {
            _context.next = 8;
            break;
          }

          return _context.abrupt("return");

        case 8:
          return _context.delegateYield(__unstableAcquireStoreLock('core', ['entities', 'data', kind, name, recordId], {
            exclusive: true
          }), "t0", 9);

        case 9:
          lock = _context.t0;
          _context.prev = 10;
          _context.next = 13;
          return {
            type: 'DELETE_ENTITY_RECORD_START',
            kind: kind,
            name: name,
            recordId: recordId
          };

        case 13:
          _context.prev = 13;
          path = "".concat(entity.baseURL, "/").concat(recordId);

          if (query) {
            path = Object(external_wp_url_["addQueryArgs"])(path, query);
          }

          options = {
            path: path,
            method: 'DELETE'
          };

          if (!__unstableFetch) {
            _context.next = 23;
            break;
          }

          _context.next = 20;
          return Object(external_wp_dataControls_["__unstableAwaitPromise"])(__unstableFetch(options));

        case 20:
          deletedRecord = _context.sent;
          _context.next = 26;
          break;

        case 23:
          _context.next = 25;
          return Object(external_wp_dataControls_["apiFetch"])(options);

        case 25:
          deletedRecord = _context.sent;

        case 26:
          _context.next = 28;
          return removeItems(kind, name, recordId, true);

        case 28:
          _context.next = 33;
          break;

        case 30:
          _context.prev = 30;
          _context.t1 = _context["catch"](13);
          error = _context.t1;

        case 33:
          _context.next = 35;
          return {
            type: 'DELETE_ENTITY_RECORD_FINISH',
            kind: kind,
            name: name,
            recordId: recordId,
            error: error
          };

        case 35:
          return _context.abrupt("return", deletedRecord);

        case 36:
          _context.prev = 36;
          return _context.delegateYield(__unstableReleaseStoreLock(lock), "t2", 38);

        case 38:
          return _context.finish(36);

        case 39:
        case "end":
          return _context.stop();
      }
    }
  }, actions_marked, null, [[10,, 36, 39], [13, 30]]);
}
/**
 * Returns an action object that triggers an
 * edit to an entity record.
 *
 * @param {string} kind     Kind of the edited entity record.
 * @param {string} name     Name of the edited entity record.
 * @param {number} recordId Record ID of the edited entity record.
 * @param {Object} edits    The edits.
 * @param {Object} options  Options for the edit.
 * @param {boolean} options.undoIgnore Whether to ignore the edit in undo history or not.
 *
 * @return {Object} Action object.
 */

function actions_editEntityRecord(kind, name, recordId, edits) {
  var options,
      entity,
      _entity$transientEdit,
      transientEdits,
      _entity$mergedEdits,
      mergedEdits,
      record,
      editedRecord,
      edit,
      _args2 = arguments;

  return external_regeneratorRuntime_default.a.wrap(function editEntityRecord$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          options = _args2.length > 4 && _args2[4] !== undefined ? _args2[4] : {};
          _context2.next = 3;
          return external_wp_data_["controls"].select('core', 'getEntity', kind, name);

        case 3:
          entity = _context2.sent;

          if (entity) {
            _context2.next = 6;
            break;
          }

          throw new Error("The entity being edited (".concat(kind, ", ").concat(name, ") does not have a loaded config."));

        case 6:
          _entity$transientEdit = entity.transientEdits, transientEdits = _entity$transientEdit === void 0 ? {} : _entity$transientEdit, _entity$mergedEdits = entity.mergedEdits, mergedEdits = _entity$mergedEdits === void 0 ? {} : _entity$mergedEdits;
          _context2.next = 9;
          return external_wp_data_["controls"].select('core', 'getRawEntityRecord', kind, name, recordId);

        case 9:
          record = _context2.sent;
          _context2.next = 12;
          return external_wp_data_["controls"].select('core', 'getEditedEntityRecord', kind, name, recordId);

        case 12:
          editedRecord = _context2.sent;
          edit = {
            kind: kind,
            name: name,
            recordId: recordId,
            // Clear edits when they are equal to their persisted counterparts
            // so that the property is not considered dirty.
            edits: Object.keys(edits).reduce(function (acc, key) {
              var recordValue = record[key];
              var editedRecordValue = editedRecord[key];
              var value = mergedEdits[key] ? build_module_actions_objectSpread(build_module_actions_objectSpread({}, editedRecordValue), edits[key]) : edits[key];
              acc[key] = Object(external_lodash_["isEqual"])(recordValue, value) ? undefined : value;
              return acc;
            }, {}),
            transientEdits: transientEdits
          };
          return _context2.abrupt("return", build_module_actions_objectSpread(build_module_actions_objectSpread({
            type: 'EDIT_ENTITY_RECORD'
          }, edit), {}, {
            meta: {
              undo: !options.undoIgnore && build_module_actions_objectSpread(build_module_actions_objectSpread({}, edit), {}, {
                // Send the current values for things like the first undo stack entry.
                edits: Object.keys(edits).reduce(function (acc, key) {
                  acc[key] = editedRecord[key];
                  return acc;
                }, {})
              })
            }
          }));

        case 15:
        case "end":
          return _context2.stop();
      }
    }
  }, actions_marked2);
}
/**
 * Action triggered to undo the last edit to
 * an entity record, if any.
 */

function undo() {
  var undoEdit;
  return external_regeneratorRuntime_default.a.wrap(function undo$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          _context3.next = 2;
          return external_wp_data_["controls"].select('core', 'getUndoEdit');

        case 2:
          undoEdit = _context3.sent;

          if (undoEdit) {
            _context3.next = 5;
            break;
          }

          return _context3.abrupt("return");

        case 5:
          _context3.next = 7;
          return build_module_actions_objectSpread(build_module_actions_objectSpread({
            type: 'EDIT_ENTITY_RECORD'
          }, undoEdit), {}, {
            meta: {
              isUndo: true
            }
          });

        case 7:
        case "end":
          return _context3.stop();
      }
    }
  }, actions_marked3);
}
/**
 * Action triggered to redo the last undoed
 * edit to an entity record, if any.
 */

function redo() {
  var redoEdit;
  return external_regeneratorRuntime_default.a.wrap(function redo$(_context4) {
    while (1) {
      switch (_context4.prev = _context4.next) {
        case 0:
          _context4.next = 2;
          return external_wp_data_["controls"].select('core', 'getRedoEdit');

        case 2:
          redoEdit = _context4.sent;

          if (redoEdit) {
            _context4.next = 5;
            break;
          }

          return _context4.abrupt("return");

        case 5:
          _context4.next = 7;
          return build_module_actions_objectSpread(build_module_actions_objectSpread({
            type: 'EDIT_ENTITY_RECORD'
          }, redoEdit), {}, {
            meta: {
              isRedo: true
            }
          });

        case 7:
        case "end":
          return _context4.stop();
      }
    }
  }, actions_marked4);
}
/**
 * Forces the creation of a new undo level.
 *
 * @return {Object} Action object.
 */

function actions_unstableCreateUndoLevel() {
  return {
    type: 'CREATE_UNDO_LEVEL'
  };
}
/**
 * Action triggered to save an entity record.
 *
 * @param {string}   kind                       Kind of the received entity.
 * @param {string}   name                       Name of the received entity.
 * @param {Object}   record                     Record to be saved.
 * @param {Object}   options                    Saving options.
 * @param {boolean}  [options.isAutosave=false] Whether this is an autosave.
 * @param {Function} [options.__unstableFetch]  Internal use only. Function to
 *                                              call instead of `apiFetch()`.
 *                                              Must return a control
 *                                              descriptor.
 */

function saveEntityRecord(kind, name, record) {
  var _ref2,
      _ref2$isAutosave,
      isAutosave,
      _ref2$__unstableFetch,
      __unstableFetch,
      entities,
      entity,
      entityIdKey,
      recordId,
      lock,
      _i,
      _Object$entries,
      _Object$entries$_i,
      key,
      value,
      evaluatedValue,
      updatedRecord,
      error,
      path,
      persistedRecord,
      currentUser,
      currentUserId,
      autosavePost,
      data,
      options,
      newRecord,
      edits,
      _options,
      _args5 = arguments;

  return external_regeneratorRuntime_default.a.wrap(function saveEntityRecord$(_context5) {
    while (1) {
      switch (_context5.prev = _context5.next) {
        case 0:
          _ref2 = _args5.length > 3 && _args5[3] !== undefined ? _args5[3] : {}, _ref2$isAutosave = _ref2.isAutosave, isAutosave = _ref2$isAutosave === void 0 ? false : _ref2$isAutosave, _ref2$__unstableFetch = _ref2.__unstableFetch, __unstableFetch = _ref2$__unstableFetch === void 0 ? null : _ref2$__unstableFetch;
          _context5.next = 3;
          return getKindEntities(kind);

        case 3:
          entities = _context5.sent;
          entity = Object(external_lodash_["find"])(entities, {
            kind: kind,
            name: name
          });

          if (entity) {
            _context5.next = 7;
            break;
          }

          return _context5.abrupt("return");

        case 7:
          entityIdKey = entity.key || DEFAULT_ENTITY_KEY;
          recordId = record[entityIdKey];
          return _context5.delegateYield(__unstableAcquireStoreLock('core', ['entities', 'data', kind, name, recordId || Object(v4["a" /* default */])()], {
            exclusive: true
          }), "t0", 10);

        case 10:
          lock = _context5.t0;
          _context5.prev = 11;
          _i = 0, _Object$entries = Object.entries(record);

        case 13:
          if (!(_i < _Object$entries.length)) {
            _context5.next = 27;
            break;
          }

          _Object$entries$_i = Object(slicedToArray["a" /* default */])(_Object$entries[_i], 2), key = _Object$entries$_i[0], value = _Object$entries$_i[1];

          if (!(typeof value === 'function')) {
            _context5.next = 24;
            break;
          }

          _context5.t1 = value;
          _context5.next = 19;
          return external_wp_data_["controls"].select('core', 'getEditedEntityRecord', kind, name, recordId);

        case 19:
          _context5.t2 = _context5.sent;
          evaluatedValue = (0, _context5.t1)(_context5.t2);
          _context5.next = 23;
          return actions_editEntityRecord(kind, name, recordId, Object(defineProperty["a" /* default */])({}, key, evaluatedValue), {
            undoIgnore: true
          });

        case 23:
          record[key] = evaluatedValue;

        case 24:
          _i++;
          _context5.next = 13;
          break;

        case 27:
          _context5.next = 29;
          return {
            type: 'SAVE_ENTITY_RECORD_START',
            kind: kind,
            name: name,
            recordId: recordId,
            isAutosave: isAutosave
          };

        case 29:
          _context5.prev = 29;
          path = "".concat(entity.baseURL).concat(recordId ? '/' + recordId : '');
          _context5.next = 33;
          return external_wp_data_["controls"].select('core', 'getRawEntityRecord', kind, name, recordId);

        case 33:
          persistedRecord = _context5.sent;

          if (!isAutosave) {
            _context5.next = 65;
            break;
          }

          _context5.next = 37;
          return external_wp_data_["controls"].select('core', 'getCurrentUser');

        case 37:
          currentUser = _context5.sent;
          currentUserId = currentUser ? currentUser.id : undefined;
          _context5.next = 41;
          return external_wp_data_["controls"].select('core', 'getAutosave', persistedRecord.type, persistedRecord.id, currentUserId);

        case 41:
          autosavePost = _context5.sent;
          // Autosaves need all expected fields to be present.
          // So we fallback to the previous autosave and then
          // to the actual persisted entity if the edits don't
          // have a value.
          data = build_module_actions_objectSpread(build_module_actions_objectSpread(build_module_actions_objectSpread({}, persistedRecord), autosavePost), record);
          data = Object.keys(data).reduce(function (acc, key) {
            if (['title', 'excerpt', 'content'].includes(key)) {
              // Edits should be the "raw" attribute values.
              acc[key] = Object(external_lodash_["get"])(data[key], 'raw', data[key]);
            }

            return acc;
          }, {
            status: data.status === 'auto-draft' ? 'draft' : data.status
          });
          options = {
            path: "".concat(path, "/autosaves"),
            method: 'POST',
            data: data
          };

          if (!__unstableFetch) {
            _context5.next = 51;
            break;
          }

          _context5.next = 48;
          return Object(external_wp_dataControls_["__unstableAwaitPromise"])(__unstableFetch(options));

        case 48:
          updatedRecord = _context5.sent;
          _context5.next = 54;
          break;

        case 51:
          _context5.next = 53;
          return Object(external_wp_dataControls_["apiFetch"])(options);

        case 53:
          updatedRecord = _context5.sent;

        case 54:
          if (!(persistedRecord.id === updatedRecord.id)) {
            _context5.next = 61;
            break;
          }

          newRecord = build_module_actions_objectSpread(build_module_actions_objectSpread(build_module_actions_objectSpread({}, persistedRecord), data), updatedRecord);
          newRecord = Object.keys(newRecord).reduce(function (acc, key) {
            // These properties are persisted in autosaves.
            if (['title', 'excerpt', 'content'].includes(key)) {
              // Edits should be the "raw" attribute values.
              acc[key] = Object(external_lodash_["get"])(newRecord[key], 'raw', newRecord[key]);
            } else if (key === 'status') {
              // Status is only persisted in autosaves when going from
              // "auto-draft" to "draft".
              acc[key] = persistedRecord.status === 'auto-draft' && newRecord.status === 'draft' ? newRecord.status : persistedRecord.status;
            } else {
              // These properties are not persisted in autosaves.
              acc[key] = Object(external_lodash_["get"])(persistedRecord[key], 'raw', persistedRecord[key]);
            }

            return acc;
          }, {});
          _context5.next = 59;
          return receiveEntityRecords(kind, name, newRecord, undefined, true);

        case 59:
          _context5.next = 63;
          break;

        case 61:
          _context5.next = 63;
          return receiveAutosaves(persistedRecord.id, updatedRecord);

        case 63:
          _context5.next = 79;
          break;

        case 65:
          edits = record;

          if (entity.__unstablePrePersist) {
            edits = build_module_actions_objectSpread(build_module_actions_objectSpread({}, edits), entity.__unstablePrePersist(persistedRecord, edits));
          }

          _options = {
            path: path,
            method: recordId ? 'PUT' : 'POST',
            data: edits
          };

          if (!__unstableFetch) {
            _context5.next = 74;
            break;
          }

          _context5.next = 71;
          return Object(external_wp_dataControls_["__unstableAwaitPromise"])(__unstableFetch(_options));

        case 71:
          updatedRecord = _context5.sent;
          _context5.next = 77;
          break;

        case 74:
          _context5.next = 76;
          return Object(external_wp_dataControls_["apiFetch"])(_options);

        case 76:
          updatedRecord = _context5.sent;

        case 77:
          _context5.next = 79;
          return receiveEntityRecords(kind, name, updatedRecord, undefined, true, edits);

        case 79:
          _context5.next = 84;
          break;

        case 81:
          _context5.prev = 81;
          _context5.t3 = _context5["catch"](29);
          error = _context5.t3;

        case 84:
          _context5.next = 86;
          return {
            type: 'SAVE_ENTITY_RECORD_FINISH',
            kind: kind,
            name: name,
            recordId: recordId,
            error: error,
            isAutosave: isAutosave
          };

        case 86:
          return _context5.abrupt("return", updatedRecord);

        case 87:
          _context5.prev = 87;
          return _context5.delegateYield(__unstableReleaseStoreLock(lock), "t4", 89);

        case 89:
          return _context5.finish(87);

        case 90:
        case "end":
          return _context5.stop();
      }
    }
  }, _marked5, null, [[11,, 87, 90], [29, 81]]);
}
/**
 * Runs multiple core-data actions at the same time using one API request.
 *
 * Example:
 *
 * ```
 * const [ savedRecord, updatedRecord, deletedRecord ] =
 *   await dispatch( 'core' ).__experimentalBatch( [
 *     ( { saveEntityRecord } ) => saveEntityRecord( 'root', 'widget', widget ),
 *     ( { saveEditedEntityRecord } ) => saveEntityRecord( 'root', 'widget', 123 ),
 *     ( { deleteEntityRecord } ) => deleteEntityRecord( 'root', 'widget', 123, null ),
 *   ] );
 * ```
 *
 * @param {Array} requests Array of functions which are invoked simultaneously.
 *                         Each function is passed an object containing
 *                         `saveEntityRecord`, `saveEditedEntityRecord`, and
 *                         `deleteEntityRecord`.
 *
 * @return {Promise} A promise that resolves to an array containing the return
 *                   values of each function given in `requests`.
 */

function __experimentalBatch(requests) {
  var batch, dispatch, api, resultPromises, _yield$__unstableAwai, _yield$__unstableAwai2, results;

  return external_regeneratorRuntime_default.a.wrap(function __experimentalBatch$(_context6) {
    while (1) {
      switch (_context6.prev = _context6.next) {
        case 0:
          batch = createBatch();
          _context6.next = 3;
          return getDispatch();

        case 3:
          dispatch = _context6.sent;
          api = {
            saveEntityRecord: function saveEntityRecord(kind, name, record, options) {
              return batch.add(function (add) {
                return dispatch('core').saveEntityRecord(kind, name, record, build_module_actions_objectSpread(build_module_actions_objectSpread({}, options), {}, {
                  __unstableFetch: add
                }));
              });
            },
            saveEditedEntityRecord: function saveEditedEntityRecord(kind, name, recordId, options) {
              return batch.add(function (add) {
                return dispatch('core').saveEditedEntityRecord(kind, name, recordId, build_module_actions_objectSpread(build_module_actions_objectSpread({}, options), {}, {
                  __unstableFetch: add
                }));
              });
            },
            deleteEntityRecord: function deleteEntityRecord(kind, name, recordId, query, options) {
              return batch.add(function (add) {
                return dispatch('core').deleteEntityRecord(kind, name, recordId, query, build_module_actions_objectSpread(build_module_actions_objectSpread({}, options), {}, {
                  __unstableFetch: add
                }));
              });
            }
          };
          resultPromises = requests.map(function (request) {
            return request(api);
          });
          _context6.next = 8;
          return Object(external_wp_dataControls_["__unstableAwaitPromise"])(Promise.all([batch.run()].concat(Object(toConsumableArray["a" /* default */])(resultPromises))));

        case 8:
          _yield$__unstableAwai = _context6.sent;
          _yield$__unstableAwai2 = Object(toArray["a" /* default */])(_yield$__unstableAwai);
          results = _yield$__unstableAwai2.slice(1);
          return _context6.abrupt("return", results);

        case 12:
        case "end":
          return _context6.stop();
      }
    }
  }, _marked6);
}
/**
 * Action triggered to save an entity record's edits.
 *
 * @param {string} kind     Kind of the entity.
 * @param {string} name     Name of the entity.
 * @param {Object} recordId ID of the record.
 * @param {Object} options  Saving options.
 */

function saveEditedEntityRecord(kind, name, recordId, options) {
  var edits, record;
  return external_regeneratorRuntime_default.a.wrap(function saveEditedEntityRecord$(_context7) {
    while (1) {
      switch (_context7.prev = _context7.next) {
        case 0:
          _context7.next = 2;
          return external_wp_data_["controls"].select('core', 'hasEditsForEntityRecord', kind, name, recordId);

        case 2:
          if (_context7.sent) {
            _context7.next = 4;
            break;
          }

          return _context7.abrupt("return");

        case 4:
          _context7.next = 6;
          return external_wp_data_["controls"].select('core', 'getEntityRecordNonTransientEdits', kind, name, recordId);

        case 6:
          edits = _context7.sent;
          record = build_module_actions_objectSpread({
            id: recordId
          }, edits);
          return _context7.delegateYield(saveEntityRecord(kind, name, record, options), "t0", 9);

        case 9:
          return _context7.abrupt("return", _context7.t0);

        case 10:
        case "end":
          return _context7.stop();
      }
    }
  }, _marked7);
}
/**
 * Returns an action object used in signalling that Upload permissions have been received.
 *
 * @param {boolean} hasUploadPermissions Does the user have permission to upload files?
 *
 * @return {Object} Action object.
 */

function receiveUploadPermissions(hasUploadPermissions) {
  return {
    type: 'RECEIVE_USER_PERMISSION',
    key: 'create/media',
    isAllowed: hasUploadPermissions
  };
}
/**
 * Returns an action object used in signalling that the current user has
 * permission to perform an action on a REST resource.
 *
 * @param {string}  key       A key that represents the action and REST resource.
 * @param {boolean} isAllowed Whether or not the user can perform the action.
 *
 * @return {Object} Action object.
 */

function receiveUserPermission(key, isAllowed) {
  return {
    type: 'RECEIVE_USER_PERMISSION',
    key: key,
    isAllowed: isAllowed
  };
}
/**
 * Returns an action object used in signalling that the autosaves for a
 * post have been received.
 *
 * @param {number}       postId    The id of the post that is parent to the autosave.
 * @param {Array|Object} autosaves An array of autosaves or singular autosave object.
 *
 * @return {Object} Action object.
 */

function receiveAutosaves(postId, autosaves) {
  return {
    type: 'RECEIVE_AUTOSAVES',
    postId: postId,
    autosaves: Object(external_lodash_["castArray"])(autosaves)
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/entities.js


var entities_marked = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(loadPostTypeEntities),
    entities_marked2 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(loadTaxonomyEntities),
    entities_marked3 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(getKindEntities);

/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


var DEFAULT_ENTITY_KEY = 'id';
var defaultEntities = [{
  label: Object(external_wp_i18n_["__"])('Base'),
  name: '__unstableBase',
  kind: 'root',
  baseURL: ''
}, {
  label: Object(external_wp_i18n_["__"])('Site'),
  name: 'site',
  kind: 'root',
  baseURL: '/wp/v2/settings',
  getTitle: function getTitle(record) {
    return Object(external_lodash_["get"])(record, ['title'], Object(external_wp_i18n_["__"])('Site Title'));
  }
}, {
  label: Object(external_wp_i18n_["__"])('Post Type'),
  name: 'postType',
  kind: 'root',
  key: 'slug',
  baseURL: '/wp/v2/types'
}, {
  name: 'media',
  kind: 'root',
  baseURL: '/wp/v2/media',
  plural: 'mediaItems',
  label: Object(external_wp_i18n_["__"])('Media')
}, {
  name: 'taxonomy',
  kind: 'root',
  key: 'slug',
  baseURL: '/wp/v2/taxonomies',
  plural: 'taxonomies',
  label: Object(external_wp_i18n_["__"])('Taxonomy')
}, {
  name: 'sidebar',
  kind: 'root',
  baseURL: '/wp/v2/sidebars',
  plural: 'sidebars',
  transientEdits: {
    blocks: true
  },
  label: Object(external_wp_i18n_["__"])('Widget areas')
}, {
  name: 'widget',
  kind: 'root',
  baseURL: '/wp/v2/widgets',
  plural: 'widgets',
  transientEdits: {
    blocks: true
  },
  label: Object(external_wp_i18n_["__"])('Widgets')
}, {
  label: Object(external_wp_i18n_["__"])('User'),
  name: 'user',
  kind: 'root',
  baseURL: '/wp/v2/users',
  plural: 'users'
}, {
  name: 'comment',
  kind: 'root',
  baseURL: '/wp/v2/comments',
  plural: 'comments',
  label: Object(external_wp_i18n_["__"])('Comment')
}, {
  name: 'menu',
  kind: 'root',
  baseURL: '/__experimental/menus',
  plural: 'menus',
  label: Object(external_wp_i18n_["__"])('Menu')
}, {
  name: 'menuItem',
  kind: 'root',
  baseURL: '/__experimental/menu-items',
  plural: 'menuItems',
  label: Object(external_wp_i18n_["__"])('Menu Item')
}, {
  name: 'menuLocation',
  kind: 'root',
  baseURL: '/__experimental/menu-locations',
  plural: 'menuLocations',
  label: Object(external_wp_i18n_["__"])('Menu Location'),
  key: 'name'
}];
var kinds = [{
  name: 'postType',
  loadEntities: loadPostTypeEntities
}, {
  name: 'taxonomy',
  loadEntities: loadTaxonomyEntities
}];
/**
 * Returns a function to be used to retrieve extra edits to apply before persisting a post type.
 *
 * @param {Object} persistedRecord Already persisted Post
 * @param {Object} edits Edits.
 * @return {Object} Updated edits.
 */

var prePersistPostType = function prePersistPostType(persistedRecord, edits) {
  var newEdits = {};

  if ((persistedRecord === null || persistedRecord === void 0 ? void 0 : persistedRecord.status) === 'auto-draft') {
    // Saving an auto-draft should create a draft by default.
    if (!edits.status && !newEdits.status) {
      newEdits.status = 'draft';
    } // Fix the auto-draft default title.


    if ((!edits.title || edits.title === 'Auto Draft') && !newEdits.title && (!(persistedRecord !== null && persistedRecord !== void 0 && persistedRecord.title) || (persistedRecord === null || persistedRecord === void 0 ? void 0 : persistedRecord.title) === 'Auto Draft')) {
      newEdits.title = '';
    }
  }

  return newEdits;
};
/**
 * Returns the list of post type entities.
 *
 * @return {Promise} Entities promise
 */

function loadPostTypeEntities() {
  var postTypes;
  return external_regeneratorRuntime_default.a.wrap(function loadPostTypeEntities$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.next = 2;
          return Object(external_wp_dataControls_["apiFetch"])({
            path: '/wp/v2/types?context=edit'
          });

        case 2:
          postTypes = _context.sent;
          return _context.abrupt("return", Object(external_lodash_["map"])(postTypes, function (postType, name) {
            var isTemplate = ['wp_template', 'wp_template_part'].includes(name);
            return {
              kind: 'postType',
              baseURL: '/wp/v2/' + postType.rest_base,
              name: name,
              label: postType.labels.singular_name,
              transientEdits: {
                blocks: true,
                selectionStart: true,
                selectionEnd: true
              },
              mergedEdits: {
                meta: true
              },
              getTitle: function getTitle(record) {
                var _record$title;

                return (record === null || record === void 0 ? void 0 : (_record$title = record.title) === null || _record$title === void 0 ? void 0 : _record$title.rendered) || (record === null || record === void 0 ? void 0 : record.title) || (isTemplate ? Object(external_lodash_["startCase"])(record.slug) : String(record.id));
              },
              __unstablePrePersist: isTemplate ? undefined : prePersistPostType
            };
          }));

        case 4:
        case "end":
          return _context.stop();
      }
    }
  }, entities_marked);
}
/**
 * Returns the list of the taxonomies entities.
 *
 * @return {Promise} Entities promise
 */


function loadTaxonomyEntities() {
  var taxonomies;
  return external_regeneratorRuntime_default.a.wrap(function loadTaxonomyEntities$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          _context2.next = 2;
          return Object(external_wp_dataControls_["apiFetch"])({
            path: '/wp/v2/taxonomies?context=edit'
          });

        case 2:
          taxonomies = _context2.sent;
          return _context2.abrupt("return", Object(external_lodash_["map"])(taxonomies, function (taxonomy, name) {
            return {
              kind: 'taxonomy',
              baseURL: '/wp/v2/' + taxonomy.rest_base,
              name: name,
              label: taxonomy.labels.singular_name
            };
          }));

        case 4:
        case "end":
          return _context2.stop();
      }
    }
  }, entities_marked2);
}
/**
 * Returns the entity's getter method name given its kind and name.
 *
 * @param {string}  kind      Entity kind.
 * @param {string}  name      Entity name.
 * @param {string}  prefix    Function prefix.
 * @param {boolean} usePlural Whether to use the plural form or not.
 *
 * @return {string} Method name
 */


var entities_getMethodName = function getMethodName(kind, name) {
  var prefix = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'get';
  var usePlural = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
  var entity = Object(external_lodash_["find"])(defaultEntities, {
    kind: kind,
    name: name
  });
  var kindPrefix = kind === 'root' ? '' : Object(external_lodash_["upperFirst"])(Object(external_lodash_["camelCase"])(kind));
  var nameSuffix = Object(external_lodash_["upperFirst"])(Object(external_lodash_["camelCase"])(name)) + (usePlural ? 's' : '');
  var suffix = usePlural && entity.plural ? Object(external_lodash_["upperFirst"])(Object(external_lodash_["camelCase"])(entity.plural)) : nameSuffix;
  return "".concat(prefix).concat(kindPrefix).concat(suffix);
};
/**
 * Loads the kind entities into the store.
 *
 * @param {string} kind  Kind
 *
 * @return {Array} Entities
 */

function getKindEntities(kind) {
  var entities, kindConfig;
  return external_regeneratorRuntime_default.a.wrap(function getKindEntities$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          _context3.next = 2;
          return external_wp_data_["controls"].select('core', 'getEntitiesByKind', kind);

        case 2:
          entities = _context3.sent;

          if (!(entities && entities.length !== 0)) {
            _context3.next = 5;
            break;
          }

          return _context3.abrupt("return", entities);

        case 5:
          kindConfig = Object(external_lodash_["find"])(kinds, {
            name: kind
          });

          if (kindConfig) {
            _context3.next = 8;
            break;
          }

          return _context3.abrupt("return", []);

        case 8:
          _context3.next = 10;
          return kindConfig.loadEntities();

        case 10:
          entities = _context3.sent;
          _context3.next = 13;
          return addEntities(entities);

        case 13:
          return _context3.abrupt("return", entities);

        case 14:
        case "end":
          return _context3.stop();
      }
    }
  }, entities_marked3);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/utils/get-normalized-comma-separable.js
/**
 * Given a value which can be specified as one or the other of a comma-separated
 * string or an array, returns a value normalized to an array of strings, or
 * null if the value cannot be interpreted as either.
 *
 * @param {string|string[]|*} value
 *
 * @return {?(string[])} Normalized field value.
 */
function getNormalizedCommaSeparable(value) {
  if (typeof value === 'string') {
    return value.split(',');
  } else if (Array.isArray(value)) {
    return value;
  }

  return null;
}

/* harmony default export */ var get_normalized_comma_separable = (getNormalizedCommaSeparable);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/utils/with-weak-map-cache.js
/**
 * External dependencies
 */

/**
 * Given a function, returns an enhanced function which caches the result and
 * tracks in WeakMap. The result is only cached if the original function is
 * passed a valid object-like argument (requirement for WeakMap key).
 *
 * @param {Function} fn Original function.
 *
 * @return {Function} Enhanced caching function.
 */

function withWeakMapCache(fn) {
  var cache = new WeakMap();
  return function (key) {
    var value;

    if (cache.has(key)) {
      value = cache.get(key);
    } else {
      value = fn(key); // Can reach here if key is not valid for WeakMap, since `has`
      // will return false for invalid key. Since `set` will throw,
      // ensure that key is valid before setting into cache.

      if (Object(external_lodash_["isObjectLike"])(key)) {
        cache.set(key, value);
      }
    }

    return value;
  };
}

/* harmony default export */ var with_weak_map_cache = (withWeakMapCache);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/queried-data/get-query-parts.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * An object of properties describing a specific query.
 *
 * @typedef {Object} WPQueriedDataQueryParts
 *
 * @property {number}      page      The query page (1-based index, default 1).
 * @property {number}      perPage   Items per page for query (default 10).
 * @property {string}      stableKey An encoded stable string of all non-
 *                                   pagination, non-fields query parameters.
 * @property {?(string[])} fields    Target subset of fields to derive from
 *                                   item objects.
 * @property {?(number[])} include   Specific item IDs to include.
 */

/**
 * Given a query object, returns an object of parts, including pagination
 * details (`page` and `perPage`, or default values). All other properties are
 * encoded into a stable (idempotent) `stableKey` value.
 *
 * @param {Object} query Optional query object.
 *
 * @return {WPQueriedDataQueryParts} Query parts.
 */

function getQueryParts(query) {
  /**
   * @type {WPQueriedDataQueryParts}
   */
  var parts = {
    stableKey: '',
    page: 1,
    perPage: 10,
    fields: null,
    include: null
  }; // Ensure stable key by sorting keys. Also more efficient for iterating.

  var keys = Object.keys(query).sort();

  for (var i = 0; i < keys.length; i++) {
    var key = keys[i];
    var value = query[key];

    switch (key) {
      case 'page':
        parts[key] = Number(value);
        break;

      case 'per_page':
        parts.perPage = Number(value);
        break;

      case 'include':
        parts.include = get_normalized_comma_separable(value).map(Number);
        break;

      default:
        // While in theory, we could exclude "_fields" from the stableKey
        // because two request with different fields have the same results
        // We're not able to ensure that because the server can decide to omit
        // fields from the response even if we explicitely asked for it.
        // Example: Asking for titles in posts without title support.
        if (key === '_fields') {
          parts.fields = get_normalized_comma_separable(value); // Make sure to normalize value for `stableKey`

          value = parts.fields.join();
        } // While it could be any deterministic string, for simplicity's
        // sake mimic querystring encoding for stable key.
        //
        // TODO: For consistency with PHP implementation, addQueryArgs
        // should accept a key value pair, which may optimize its
        // implementation for our use here, vs. iterating an object
        // with only a single key.


        parts.stableKey += (parts.stableKey ? '&' : '') + Object(external_wp_url_["addQueryArgs"])('', Object(defineProperty["a" /* default */])({}, key, value)).slice(1);
    }
  }

  return parts;
}
/* harmony default export */ var get_query_parts = (with_weak_map_cache(getQueryParts));

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/queried-data/reducer.js


function reducer_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function reducer_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { reducer_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { reducer_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




/**
 * Returns a merged array of item IDs, given details of the received paginated
 * items. The array is sparse-like with `undefined` entries where holes exist.
 *
 * @param {?Array<number>} itemIds     Original item IDs (default empty array).
 * @param {number[]}       nextItemIds Item IDs to merge.
 * @param {number}         page        Page of items merged.
 * @param {number}         perPage     Number of items per page.
 *
 * @return {number[]} Merged array of item IDs.
 */

function getMergedItemIds(itemIds, nextItemIds, page, perPage) {
  var receivedAllIds = page === 1 && perPage === -1;

  if (receivedAllIds) {
    return nextItemIds;
  }

  var nextItemIdsStartIndex = (page - 1) * perPage; // If later page has already been received, default to the larger known
  // size of the existing array, else calculate as extending the existing.

  var size = Math.max(itemIds.length, nextItemIdsStartIndex + nextItemIds.length); // Preallocate array since size is known.

  var mergedItemIds = new Array(size);

  for (var i = 0; i < size; i++) {
    // Preserve existing item ID except for subset of range of next items.
    var isInNextItemsRange = i >= nextItemIdsStartIndex && i < nextItemIdsStartIndex + nextItemIds.length;
    mergedItemIds[i] = isInNextItemsRange ? nextItemIds[i - nextItemIdsStartIndex] : itemIds[i];
  }

  return mergedItemIds;
}
/**
 * Reducer tracking items state, keyed by ID. Items are assumed to be normal,
 * where identifiers are common across all queries.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Next state.
 */

function reducer_items() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RECEIVE_ITEMS':
      var key = action.key || DEFAULT_ENTITY_KEY;
      return reducer_objectSpread(reducer_objectSpread({}, state), action.items.reduce(function (accumulator, value) {
        var itemId = value[key];
        accumulator[itemId] = conservativeMapItem(state[itemId], value);
        return accumulator;
      }, {}));

    case 'REMOVE_ITEMS':
      var newState = Object(external_lodash_["omit"])(state, action.itemIds);
      return newState;
  }

  return state;
}
/**
 * Reducer tracking item completeness, keyed by ID. A complete item is one for
 * which all fields are known. This is used in supporting `_fields` queries,
 * where not all properties associated with an entity are necessarily returned.
 * In such cases, completeness is used as an indication of whether it would be
 * safe to use queried data for a non-`_fields`-limited request.
 *
 * @param {Object<string,boolean>} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object<string,boolean>} Next state.
 */


function itemIsComplete() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;
  var type = action.type,
      query = action.query,
      _action$key = action.key,
      key = _action$key === void 0 ? DEFAULT_ENTITY_KEY : _action$key;

  if (type !== 'RECEIVE_ITEMS') {
    return state;
  } // An item is considered complete if it is received without an associated
  // fields query. Ideally, this would be implemented in such a way where the
  // complete aggregate of all fields would satisfy completeness. Since the
  // fields are not consistent across all entity types, this would require
  // introspection on the REST schema for each entity to know which fields
  // compose a complete item for that entity.


  var isCompleteQuery = !query || !Array.isArray(get_query_parts(query).fields);
  return reducer_objectSpread(reducer_objectSpread({}, state), action.items.reduce(function (result, item) {
    var itemId = item[key]; // Defer to completeness if already assigned. Technically the
    // data may be outdated if receiving items for a field subset.

    result[itemId] = state[itemId] || isCompleteQuery;
    return result;
  }, {}));
}
/**
 * Reducer tracking queries state, keyed by stable query key. Each reducer
 * query object includes `itemIds` and `requestingPageByPerPage`.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Next state.
 */

var receiveQueries = Object(external_lodash_["flowRight"])([// Limit to matching action type so we don't attempt to replace action on
// an unhandled action.
if_matching_action(function (action) {
  return 'query' in action;
}), // Inject query parts into action for use both in `onSubKey` and reducer.
replace_action(function (action) {
  // `ifMatchingAction` still passes on initialization, where state is
  // undefined and a query is not assigned. Avoid attempting to parse
  // parts. `onSubKey` will omit by lack of `stableKey`.
  if (action.query) {
    return reducer_objectSpread(reducer_objectSpread({}, action), get_query_parts(action.query));
  }

  return action;
}), // Queries shape is shared, but keyed by query `stableKey` part. Original
// reducer tracks only a single query object.
on_sub_key('stableKey')])(function () {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var action = arguments.length > 1 ? arguments[1] : undefined;
  var type = action.type,
      page = action.page,
      perPage = action.perPage,
      _action$key2 = action.key,
      key = _action$key2 === void 0 ? DEFAULT_ENTITY_KEY : _action$key2;

  if (type !== 'RECEIVE_ITEMS') {
    return state;
  }

  return getMergedItemIds(state || [], Object(external_lodash_["map"])(action.items, key), page, perPage);
});
/**
 * Reducer tracking queries state.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Next state.
 */

var reducer_queries = function queries() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RECEIVE_ITEMS':
      return receiveQueries(state, action);

    case 'REMOVE_ITEMS':
      var newState = reducer_objectSpread({}, state);

      var removedItems = action.itemIds.reduce(function (result, itemId) {
        result[itemId] = true;
        return result;
      }, {});
      Object(external_lodash_["forEach"])(newState, function (queryItems, key) {
        newState[key] = Object(external_lodash_["filter"])(queryItems, function (queryId) {
          return !removedItems[queryId];
        });
      });
      return newState;

    default:
      return state;
  }
};

/* harmony default export */ var queried_data_reducer = (Object(external_wp_data_["combineReducers"])({
  items: reducer_items,
  itemIsComplete: itemIsComplete,
  queries: reducer_queries
}));

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/locks/utils.js




var utils_marked = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(iteratePath),
    utils_marked2 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(iterateDescendants);

function utils_createForOfIteratorHelper(o, allowArrayLike) { var it; if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (it = utils_unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }

function utils_unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return utils_arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return utils_arrayLikeToArray(o, minLen); }

function utils_arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function utils_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function utils_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { utils_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { utils_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function deepCopyLocksTreePath(tree, path) {
  var newTree = utils_objectSpread({}, tree);

  var currentNode = newTree;

  var _iterator = utils_createForOfIteratorHelper(path),
      _step;

  try {
    for (_iterator.s(); !(_step = _iterator.n()).done;) {
      var branchName = _step.value;
      currentNode.children = utils_objectSpread(utils_objectSpread({}, currentNode.children), {}, Object(defineProperty["a" /* default */])({}, branchName, utils_objectSpread({
        locks: [],
        children: {}
      }, currentNode.children[branchName])));
      currentNode = currentNode.children[branchName];
    }
  } catch (err) {
    _iterator.e(err);
  } finally {
    _iterator.f();
  }

  return newTree;
}
function getNode(tree, path) {
  var currentNode = tree;

  var _iterator2 = utils_createForOfIteratorHelper(path),
      _step2;

  try {
    for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
      var branchName = _step2.value;
      var nextNode = currentNode.children[branchName];

      if (!nextNode) {
        return null;
      }

      currentNode = nextNode;
    }
  } catch (err) {
    _iterator2.e(err);
  } finally {
    _iterator2.f();
  }

  return currentNode;
}
function iteratePath(tree, path) {
  var currentNode, _iterator3, _step3, branchName, nextNode;

  return external_regeneratorRuntime_default.a.wrap(function iteratePath$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          currentNode = tree;
          _context.next = 3;
          return currentNode;

        case 3:
          _iterator3 = utils_createForOfIteratorHelper(path);
          _context.prev = 4;

          _iterator3.s();

        case 6:
          if ((_step3 = _iterator3.n()).done) {
            _context.next = 16;
            break;
          }

          branchName = _step3.value;
          nextNode = currentNode.children[branchName];

          if (nextNode) {
            _context.next = 11;
            break;
          }

          return _context.abrupt("break", 16);

        case 11:
          _context.next = 13;
          return nextNode;

        case 13:
          currentNode = nextNode;

        case 14:
          _context.next = 6;
          break;

        case 16:
          _context.next = 21;
          break;

        case 18:
          _context.prev = 18;
          _context.t0 = _context["catch"](4);

          _iterator3.e(_context.t0);

        case 21:
          _context.prev = 21;

          _iterator3.f();

          return _context.finish(21);

        case 24:
        case "end":
          return _context.stop();
      }
    }
  }, utils_marked, null, [[4, 18, 21, 24]]);
}
function iterateDescendants(node) {
  var stack, childNode;
  return external_regeneratorRuntime_default.a.wrap(function iterateDescendants$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          stack = Object.values(node.children);

        case 1:
          if (!stack.length) {
            _context2.next = 8;
            break;
          }

          childNode = stack.pop();
          _context2.next = 5;
          return childNode;

        case 5:
          stack.push.apply(stack, Object(toConsumableArray["a" /* default */])(Object.values(childNode.children)));
          _context2.next = 1;
          break;

        case 8:
        case "end":
          return _context2.stop();
      }
    }
  }, utils_marked2);
}
function hasConflictingLock(_ref, locks) {
  var exclusive = _ref.exclusive;

  if (exclusive && locks.length) {
    return true;
  }

  if (!exclusive && locks.filter(function (lock) {
    return lock.exclusive;
  }).length) {
    return true;
  }

  return false;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/locks/reducer.js



function locks_reducer_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function locks_reducer_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { locks_reducer_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { locks_reducer_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Internal dependencies
 */

var DEFAULT_STATE = {
  requests: [],
  tree: {
    locks: [],
    children: {}
  }
};
/**
 * Reducer returning locks.
 *
 * @param  {Object} state  Current state.
 * @param  {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function reducer_locks() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_STATE;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'ENQUEUE_LOCK_REQUEST':
      {
        var request = action.request;
        return locks_reducer_objectSpread(locks_reducer_objectSpread({}, state), {}, {
          requests: [request].concat(Object(toConsumableArray["a" /* default */])(state.requests))
        });
      }

    case 'GRANT_LOCK_REQUEST':
      {
        var lock = action.lock,
            _request = action.request;
        var store = _request.store,
            path = _request.path;
        var storePath = [store].concat(Object(toConsumableArray["a" /* default */])(path));
        var newTree = deepCopyLocksTreePath(state.tree, storePath);
        var node = getNode(newTree, storePath);
        node.locks = [].concat(Object(toConsumableArray["a" /* default */])(node.locks), [lock]);
        return locks_reducer_objectSpread(locks_reducer_objectSpread({}, state), {}, {
          requests: state.requests.filter(function (r) {
            return r !== _request;
          }),
          tree: newTree
        });
      }

    case 'RELEASE_LOCK':
      {
        var _lock = action.lock;

        var _storePath = [_lock.store].concat(Object(toConsumableArray["a" /* default */])(_lock.path));

        var _newTree = deepCopyLocksTreePath(state.tree, _storePath);

        var _node = getNode(_newTree, _storePath);

        _node.locks = _node.locks.filter(function (l) {
          return l !== _lock;
        });
        return locks_reducer_objectSpread(locks_reducer_objectSpread({}, state), {}, {
          tree: _newTree
        });
      }
  }

  return state;
}
/* harmony default export */ var locks_reducer = (reducer_locks);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/reducer.js




function reducer_createForOfIteratorHelper(o, allowArrayLike) { var it; if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (it = reducer_unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }

function reducer_unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return reducer_arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return reducer_arrayLikeToArray(o, minLen); }

function reducer_arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function build_module_reducer_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function build_module_reducer_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { build_module_reducer_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { build_module_reducer_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */





/**
 * Reducer managing terms state. Keyed by taxonomy slug, the value is either
 * undefined (if no request has been made for given taxonomy), null (if a
 * request is in-flight for given taxonomy), or the array of terms for the
 * taxonomy.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function terms() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RECEIVE_TERMS':
      return build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state), {}, Object(defineProperty["a" /* default */])({}, action.taxonomy, action.terms));
  }

  return state;
}
/**
 * Reducer managing authors state. Keyed by id.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function reducer_users() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
    byId: {},
    queries: {}
  };
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RECEIVE_USER_QUERY':
      return {
        byId: build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state.byId), Object(external_lodash_["keyBy"])(action.users, 'id')),
        queries: build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state.queries), {}, Object(defineProperty["a" /* default */])({}, action.queryID, Object(external_lodash_["map"])(action.users, function (user) {
          return user.id;
        })))
      };
  }

  return state;
}
/**
 * Reducer managing current user state.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function reducer_currentUser() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RECEIVE_CURRENT_USER':
      return action.currentUser;
  }

  return state;
}
/**
 * Reducer managing taxonomies.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function reducer_taxonomies() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RECEIVE_TAXONOMIES':
      return action.taxonomies;
  }

  return state;
}
/**
 * Reducer managing the current theme.
 *
 * @param {string} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {string} Updated state.
 */

function currentTheme() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : undefined;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RECEIVE_CURRENT_THEME':
      return action.currentTheme.stylesheet;
  }

  return state;
}
/**
 * Reducer managing installed themes.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function themes() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RECEIVE_CURRENT_THEME':
      return build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state), {}, Object(defineProperty["a" /* default */])({}, action.currentTheme.stylesheet, action.currentTheme));
  }

  return state;
}
/**
 * Reducer managing theme supports data.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function themeSupports() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RECEIVE_THEME_SUPPORTS':
      return build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state), action.themeSupports);
  }

  return state;
}
/**
 * Higher Order Reducer for a given entity config. It supports:
 *
 *  - Fetching
 *  - Editing
 *  - Saving
 *
 * @param {Object} entityConfig  Entity config.
 *
 * @return {Function} Reducer.
 */

function reducer_entity(entityConfig) {
  return Object(external_lodash_["flowRight"])([// Limit to matching action type so we don't attempt to replace action on
  // an unhandled action.
  if_matching_action(function (action) {
    return action.name && action.kind && action.name === entityConfig.name && action.kind === entityConfig.kind;
  }), // Inject the entity config into the action.
  replace_action(function (action) {
    return build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, action), {}, {
      key: entityConfig.key || DEFAULT_ENTITY_KEY
    });
  })])(Object(external_wp_data_["combineReducers"])({
    queriedData: queried_data_reducer,
    edits: function edits() {
      var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var action = arguments.length > 1 ? arguments[1] : undefined;

      switch (action.type) {
        case 'RECEIVE_ITEMS':
          var nextState = build_module_reducer_objectSpread({}, state);

          var _iterator = reducer_createForOfIteratorHelper(action.items),
              _step;

          try {
            var _loop = function _loop() {
              var record = _step.value;
              var recordId = record[action.key];
              var edits = nextState[recordId];

              if (!edits) {
                return "continue";
              }

              var nextEdits = Object.keys(edits).reduce(function (acc, key) {
                // If the edited value is still different to the persisted value,
                // keep the edited value in edits.
                if ( // Edits are the "raw" attribute values, but records may have
                // objects with more properties, so we use `get` here for the
                // comparison.
                !Object(external_lodash_["isEqual"])(edits[key], Object(external_lodash_["get"])(record[key], 'raw', record[key])) && ( // Sometimes the server alters the sent value which means
                // we need to also remove the edits before the api request.
                !action.persistedEdits || !Object(external_lodash_["isEqual"])(edits[key], action.persistedEdits[key]))) {
                  acc[key] = edits[key];
                }

                return acc;
              }, {});

              if (Object.keys(nextEdits).length) {
                nextState[recordId] = nextEdits;
              } else {
                delete nextState[recordId];
              }
            };

            for (_iterator.s(); !(_step = _iterator.n()).done;) {
              var _ret = _loop();

              if (_ret === "continue") continue;
            }
          } catch (err) {
            _iterator.e(err);
          } finally {
            _iterator.f();
          }

          return nextState;

        case 'EDIT_ENTITY_RECORD':
          var nextEdits = build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state[action.recordId]), action.edits);

          Object.keys(nextEdits).forEach(function (key) {
            // Delete cleared edits so that the properties
            // are not considered dirty.
            if (nextEdits[key] === undefined) {
              delete nextEdits[key];
            }
          });
          return build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state), {}, Object(defineProperty["a" /* default */])({}, action.recordId, nextEdits));
      }

      return state;
    },
    saving: function saving() {
      var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var action = arguments.length > 1 ? arguments[1] : undefined;

      switch (action.type) {
        case 'SAVE_ENTITY_RECORD_START':
        case 'SAVE_ENTITY_RECORD_FINISH':
          return build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state), {}, Object(defineProperty["a" /* default */])({}, action.recordId, {
            pending: action.type === 'SAVE_ENTITY_RECORD_START',
            error: action.error,
            isAutosave: action.isAutosave
          }));
      }

      return state;
    },
    deleting: function deleting() {
      var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var action = arguments.length > 1 ? arguments[1] : undefined;

      switch (action.type) {
        case 'DELETE_ENTITY_RECORD_START':
        case 'DELETE_ENTITY_RECORD_FINISH':
          return build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state), {}, Object(defineProperty["a" /* default */])({}, action.recordId, {
            pending: action.type === 'DELETE_ENTITY_RECORD_START',
            error: action.error
          }));
      }

      return state;
    }
  }));
}
/**
 * Reducer keeping track of the registered entities.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */


function entitiesConfig() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : defaultEntities;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'ADD_ENTITIES':
      return [].concat(Object(toConsumableArray["a" /* default */])(state), Object(toConsumableArray["a" /* default */])(action.entities));
  }

  return state;
}
/**
 * Reducer keeping track of the registered entities config and data.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

var reducer_entities = function entities() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;
  var newConfig = entitiesConfig(state.config, action); // Generates a dynamic reducer for the entities

  var entitiesDataReducer = state.reducer;

  if (!entitiesDataReducer || newConfig !== state.config) {
    var entitiesByKind = Object(external_lodash_["groupBy"])(newConfig, 'kind');
    entitiesDataReducer = Object(external_wp_data_["combineReducers"])(Object.entries(entitiesByKind).reduce(function (memo, _ref) {
      var _ref2 = Object(slicedToArray["a" /* default */])(_ref, 2),
          kind = _ref2[0],
          subEntities = _ref2[1];

      var kindReducer = Object(external_wp_data_["combineReducers"])(subEntities.reduce(function (kindMemo, entityConfig) {
        return build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, kindMemo), {}, Object(defineProperty["a" /* default */])({}, entityConfig.name, reducer_entity(entityConfig)));
      }, {}));
      memo[kind] = kindReducer;
      return memo;
    }, {}));
  }

  var newData = entitiesDataReducer(state.data, action);

  if (newData === state.data && newConfig === state.config && entitiesDataReducer === state.reducer) {
    return state;
  }

  return {
    reducer: entitiesDataReducer,
    data: newData,
    config: newConfig
  };
};
/**
 * Reducer keeping track of entity edit undo history.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

var UNDO_INITIAL_STATE = [];
UNDO_INITIAL_STATE.offset = 0;
var lastEditAction;
function reducer_undo() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : UNDO_INITIAL_STATE;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'EDIT_ENTITY_RECORD':
    case 'CREATE_UNDO_LEVEL':
      var isCreateUndoLevel = action.type === 'CREATE_UNDO_LEVEL';
      var isUndoOrRedo = !isCreateUndoLevel && (action.meta.isUndo || action.meta.isRedo);

      if (isCreateUndoLevel) {
        action = lastEditAction;
      } else if (!isUndoOrRedo) {
        // Don't lose the last edit cache if the new one only has transient edits.
        // Transient edits don't create new levels so updating the cache would make
        // us skip an edit later when creating levels explicitly.
        if (Object.keys(action.edits).some(function (key) {
          return !action.transientEdits[key];
        })) {
          lastEditAction = action;
        } else {
          lastEditAction = build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, action), {}, {
            edits: build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, lastEditAction && lastEditAction.edits), action.edits)
          });
        }
      }

      var nextState;

      if (isUndoOrRedo) {
        nextState = Object(toConsumableArray["a" /* default */])(state);
        nextState.offset = state.offset + (action.meta.isUndo ? -1 : 1);

        if (state.flattenedUndo) {
          // The first undo in a sequence of undos might happen while we have
          // flattened undos in state. If this is the case, we want execution
          // to continue as if we were creating an explicit undo level. This
          // will result in an extra undo level being appended with the flattened
          // undo values.
          isCreateUndoLevel = true;
          action = lastEditAction;
        } else {
          return nextState;
        }
      }

      if (!action.meta.undo) {
        return state;
      } // Transient edits don't create an undo level, but are
      // reachable in the next meaningful edit to which they
      // are merged. They are defined in the entity's config.


      if (!isCreateUndoLevel && !Object.keys(action.edits).some(function (key) {
        return !action.transientEdits[key];
      })) {
        nextState = Object(toConsumableArray["a" /* default */])(state);
        nextState.flattenedUndo = build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state.flattenedUndo), action.edits);
        nextState.offset = state.offset;
        return nextState;
      } // Clear potential redos, because this only supports linear history.


      nextState = nextState || state.slice(0, state.offset || undefined);
      nextState.offset = nextState.offset || 0;
      nextState.pop();

      if (!isCreateUndoLevel) {
        nextState.push({
          kind: action.meta.undo.kind,
          name: action.meta.undo.name,
          recordId: action.meta.undo.recordId,
          edits: build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state.flattenedUndo), action.meta.undo.edits)
        });
      } // When an edit is a function it's an optimization to avoid running some expensive operation.
      // We can't rely on the function references being the same so we opt out of comparing them here.


      var comparisonUndoEdits = Object.values(action.meta.undo.edits).filter(function (edit) {
        return typeof edit !== 'function';
      });
      var comparisonEdits = Object.values(action.edits).filter(function (edit) {
        return typeof edit !== 'function';
      });

      if (!external_wp_isShallowEqual_default()(comparisonUndoEdits, comparisonEdits)) {
        nextState.push({
          kind: action.kind,
          name: action.name,
          recordId: action.recordId,
          edits: isCreateUndoLevel ? build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state.flattenedUndo), action.edits) : action.edits
        });
      }

      return nextState;
  }

  return state;
}
/**
 * Reducer managing embed preview data.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function embedPreviews() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RECEIVE_EMBED_PREVIEW':
      var url = action.url,
          preview = action.preview;
      return build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state), {}, Object(defineProperty["a" /* default */])({}, url, preview));
  }

  return state;
}
/**
 * State which tracks whether the user can perform an action on a REST
 * resource.
 *
 * @param  {Object} state  Current state.
 * @param  {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function userPermissions() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RECEIVE_USER_PERMISSION':
      return build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state), {}, Object(defineProperty["a" /* default */])({}, action.key, action.isAllowed));
  }

  return state;
}
/**
 * Reducer returning autosaves keyed by their parent's post id.
 *
 * @param  {Object} state  Current state.
 * @param  {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function reducer_autosaves() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'RECEIVE_AUTOSAVES':
      var postId = action.postId,
          autosavesData = action.autosaves;
      return build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state), {}, Object(defineProperty["a" /* default */])({}, postId, autosavesData));
  }

  return state;
}
/* harmony default export */ var build_module_reducer = (Object(external_wp_data_["combineReducers"])({
  terms: terms,
  users: reducer_users,
  currentTheme: currentTheme,
  currentUser: reducer_currentUser,
  taxonomies: reducer_taxonomies,
  themes: themes,
  themeSupports: themeSupports,
  entities: reducer_entities,
  undo: reducer_undo,
  embedPreviews: embedPreviews,
  userPermissions: userPermissions,
  autosaves: reducer_autosaves,
  locks: locks_reducer
}));

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__("pPDe");

// EXTERNAL MODULE: external ["wp","deprecated"]
var external_wp_deprecated_ = __webpack_require__("NMb1");
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/name.js
/**
 * The reducer key used by core data in store registration.
 * This is defined in a separate file to avoid cycle-dependency
 *
 * @type {string}
 */
var STORE_NAME = 'core';

// EXTERNAL MODULE: ./node_modules/equivalent-key-map/equivalent-key-map.js
var equivalent_key_map = __webpack_require__("FtRg");
var equivalent_key_map_default = /*#__PURE__*/__webpack_require__.n(equivalent_key_map);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/queried-data/selectors.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Cache of state keys to EquivalentKeyMap where the inner map tracks queries
 * to their resulting items set. WeakMap allows garbage collection on expired
 * state references.
 *
 * @type {WeakMap<Object,EquivalentKeyMap>}
 */

var queriedItemsCacheByState = new WeakMap();
/**
 * Returns items for a given query, or null if the items are not known.
 *
 * @param {Object}  state State object.
 * @param {?Object} query Optional query.
 *
 * @return {?Array} Query items.
 */

function getQueriedItemsUncached(state, query) {
  var _getQueryParts = get_query_parts(query),
      stableKey = _getQueryParts.stableKey,
      page = _getQueryParts.page,
      perPage = _getQueryParts.perPage,
      include = _getQueryParts.include,
      fields = _getQueryParts.fields;

  var itemIds;

  if (Array.isArray(include) && !stableKey) {
    // If the parsed query yields a set of IDs, but otherwise no filtering,
    // it's safe to consider targeted item IDs as the include set. This
    // doesn't guarantee that those objects have been queried, which is
    // accounted for below in the loop `null` return.
    itemIds = include; // TODO: Avoid storing the empty stable string in reducer, since it
    // can be computed dynamically here always.
  } else if (state.queries[stableKey]) {
    itemIds = state.queries[stableKey];
  }

  if (!itemIds) {
    return null;
  }

  var startOffset = perPage === -1 ? 0 : (page - 1) * perPage;
  var endOffset = perPage === -1 ? itemIds.length : Math.min(startOffset + perPage, itemIds.length);
  var items = [];

  for (var i = startOffset; i < endOffset; i++) {
    var itemId = itemIds[i];

    if (Array.isArray(include) && !include.includes(itemId)) {
      continue;
    }

    if (!state.items.hasOwnProperty(itemId)) {
      return null;
    }

    var item = state.items[itemId];
    var filteredItem = void 0;

    if (Array.isArray(fields)) {
      filteredItem = {};

      for (var f = 0; f < fields.length; f++) {
        var field = fields[f].split('.');
        var value = Object(external_lodash_["get"])(item, field);
        Object(external_lodash_["set"])(filteredItem, field, value);
      }
    } else {
      // If expecting a complete item, validate that completeness, or
      // otherwise abort.
      if (!state.itemIsComplete[itemId]) {
        return null;
      }

      filteredItem = item;
    }

    items.push(filteredItem);
  }

  return items;
}
/**
 * Returns items for a given query, or null if the items are not known. Caches
 * result both per state (by reference) and per query (by deep equality).
 * The caching approach is intended to be durable to query objects which are
 * deeply but not referentially equal, since otherwise:
 *
 * `getQueriedItems( state, {} ) !== getQueriedItems( state, {} )`
 *
 * @param {Object}  state State object.
 * @param {?Object} query Optional query.
 *
 * @return {?Array} Query items.
 */


var getQueriedItems = Object(rememo["a" /* default */])(function (state) {
  var query = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var queriedItemsCache = queriedItemsCacheByState.get(state);

  if (queriedItemsCache) {
    var queriedItems = queriedItemsCache.get(query);

    if (queriedItems !== undefined) {
      return queriedItems;
    }
  } else {
    queriedItemsCache = new equivalent_key_map_default.a();
    queriedItemsCacheByState.set(state, queriedItemsCache);
  }

  var items = getQueriedItemsUncached(state, query);
  queriedItemsCache.set(query, items);
  return items;
});

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/selectors.js


function selectors_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function selectors_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { selectors_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { selectors_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */





/**
 * Shared reference to an empty array for cases where it is important to avoid
 * returning a new array reference on every invocation, as in a connected or
 * other pure component which performs `shouldComponentUpdate` check on props.
 * This should be used as a last resort, since the normalized data should be
 * maintained by the reducer result in state.
 *
 * @type {Array}
 */

var EMPTY_ARRAY = [];
/**
 * Returns true if a request is in progress for embed preview data, or false
 * otherwise.
 *
 * @param {Object} state Data state.
 * @param {string} url   URL the preview would be for.
 *
 * @return {boolean} Whether a request is in progress for an embed preview.
 */

var isRequestingEmbedPreview = Object(external_wp_data_["createRegistrySelector"])(function (select) {
  return function (state, url) {
    return select('core/data').isResolving(STORE_NAME, 'getEmbedPreview', [url]);
  };
});
/**
 * Returns all available authors.
 *
 * @param {Object}           state Data state.
 * @param {Object|undefined} query Optional object of query parameters to
 *                                 include with request.
 * @return {Array} Authors list.
 */

function getAuthors(state, query) {
  var path = Object(external_wp_url_["addQueryArgs"])('/wp/v2/users/?who=authors&per_page=100', query);
  return getUserQueryResults(state, path);
}
/**
 * Returns all available authors.
 *
 * @param {Object} state Data state.
 * @param {number} id The author id.
 *
 * @return {Array} Authors list.
 */

function __unstableGetAuthor(state, id) {
  return Object(external_lodash_["get"])(state, ['users', 'byId', id], null);
}
/**
 * Returns the current user.
 *
 * @param {Object} state Data state.
 *
 * @return {Object} Current user object.
 */

function getCurrentUser(state) {
  return state.currentUser;
}
/**
 * Returns all the users returned by a query ID.
 *
 * @param {Object} state   Data state.
 * @param {string} queryID Query ID.
 *
 * @return {Array} Users list.
 */

var getUserQueryResults = Object(rememo["a" /* default */])(function (state, queryID) {
  var queryResults = state.users.queries[queryID];
  return Object(external_lodash_["map"])(queryResults, function (id) {
    return state.users.byId[id];
  });
}, function (state, queryID) {
  return [state.users.queries[queryID], state.users.byId];
});
/**
 * Returns whether the entities for the give kind are loaded.
 *
 * @param {Object} state   Data state.
 * @param {string} kind  Entity kind.
 *
 * @return {boolean} Whether the entities are loaded
 */

function getEntitiesByKind(state, kind) {
  return Object(external_lodash_["filter"])(state.entities.config, {
    kind: kind
  });
}
/**
 * Returns the entity object given its kind and name.
 *
 * @param {Object} state   Data state.
 * @param {string} kind  Entity kind.
 * @param {string} name  Entity name.
 *
 * @return {Object} Entity
 */

function selectors_getEntity(state, kind, name) {
  return Object(external_lodash_["find"])(state.entities.config, {
    kind: kind,
    name: name
  });
}
/**
 * Returns the Entity's record object by key. Returns `null` if the value is not
 * yet received, undefined if the value entity is known to not exist, or the
 * entity object if it exists and is received.
 *
 * @param {Object}  state State tree
 * @param {string}  kind  Entity kind.
 * @param {string}  name  Entity name.
 * @param {number}  key   Record's key
 * @param {?Object} query Optional query.
 *
 * @return {Object?} Record.
 */

function getEntityRecord(state, kind, name, key, query) {
  var queriedState = Object(external_lodash_["get"])(state.entities.data, [kind, name, 'queriedData']);

  if (!queriedState) {
    return undefined;
  }

  if (query === undefined) {
    // If expecting a complete item, validate that completeness.
    if (!queriedState.itemIsComplete[key]) {
      return undefined;
    }

    return queriedState.items[key];
  }

  var item = queriedState.items[key];

  if (item && query._fields) {
    var filteredItem = {};
    var fields = get_normalized_comma_separable(query._fields);

    for (var f = 0; f < fields.length; f++) {
      var field = fields[f].split('.');
      var value = Object(external_lodash_["get"])(item, field);
      Object(external_lodash_["set"])(filteredItem, field, value);
    }

    return filteredItem;
  }

  return item;
}
/**
 * Returns the Entity's record object by key. Doesn't trigger a resolver nor requests the entity from the API if the entity record isn't available in the local state.
 *
 * @param {Object} state  State tree
 * @param {string} kind   Entity kind.
 * @param {string} name   Entity name.
 * @param {number} key    Record's key
 *
 * @return {Object|null} Record.
 */

function __experimentalGetEntityRecordNoResolver(state, kind, name, key) {
  return getEntityRecord(state, kind, name, key);
}
/**
 * Returns the entity's record object by key,
 * with its attributes mapped to their raw values.
 *
 * @param {Object} state  State tree.
 * @param {string} kind   Entity kind.
 * @param {string} name   Entity name.
 * @param {number} key    Record's key.
 *
 * @return {Object?} Object with the entity's raw attributes.
 */

var getRawEntityRecord = Object(rememo["a" /* default */])(function (state, kind, name, key) {
  var record = getEntityRecord(state, kind, name, key);
  return record && Object.keys(record).reduce(function (accumulator, _key) {
    // Because edits are the "raw" attribute values,
    // we return those from record selectors to make rendering,
    // comparisons, and joins with edits easier.
    accumulator[_key] = Object(external_lodash_["get"])(record[_key], 'raw', record[_key]);
    return accumulator;
  }, {});
}, function (state) {
  return [state.entities.data];
});
/**
 * Returns true if records have been received for the given set of parameters,
 * or false otherwise.
 *
 * @param {Object}  state State tree
 * @param {string}  kind  Entity kind.
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @return {boolean} Whether entity records have been received.
 */

function hasEntityRecords(state, kind, name, query) {
  return Array.isArray(getEntityRecords(state, kind, name, query));
}
/**
 * Returns the Entity's records.
 *
 * @param {Object}  state State tree
 * @param {string}  kind  Entity kind.
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @return {?Array} Records.
 */

function getEntityRecords(state, kind, name, query) {
  // Queried data state is prepopulated for all known entities. If this is not
  // assigned for the given parameters, then it is known to not exist. Thus, a
  // return value of an empty array is used instead of `null` (where `null` is
  // otherwise used to represent an unknown state).
  var queriedState = Object(external_lodash_["get"])(state.entities.data, [kind, name, 'queriedData']);

  if (!queriedState) {
    return EMPTY_ARRAY;
  }

  return getQueriedItems(queriedState, query);
}
/**
 * Returns the  list of dirty entity records.
 *
 * @param {Object} state State tree.
 *
 * @return {[{ title: string, key: string, name: string, kind: string }]} The list of updated records
 */

var __experimentalGetDirtyEntityRecords = Object(rememo["a" /* default */])(function (state) {
  var data = state.entities.data;
  var dirtyRecords = [];
  Object.keys(data).forEach(function (kind) {
    Object.keys(data[kind]).forEach(function (name) {
      var primaryKeys = Object.keys(data[kind][name].edits).filter(function (primaryKey) {
        return hasEditsForEntityRecord(state, kind, name, primaryKey);
      });

      if (primaryKeys.length) {
        var entity = selectors_getEntity(state, kind, name);
        primaryKeys.forEach(function (primaryKey) {
          var _entity$getTitle;

          var entityRecord = getEditedEntityRecord(state, kind, name, primaryKey);
          dirtyRecords.push({
            // We avoid using primaryKey because it's transformed into a string
            // when it's used as an object key.
            key: entityRecord[entity.key || DEFAULT_ENTITY_KEY],
            title: (entity === null || entity === void 0 ? void 0 : (_entity$getTitle = entity.getTitle) === null || _entity$getTitle === void 0 ? void 0 : _entity$getTitle.call(entity, entityRecord)) || '',
            name: name,
            kind: kind
          });
        });
      }
    });
  });
  return dirtyRecords;
}, function (state) {
  return [state.entities.data];
});
/**
 * Returns the specified entity record's edits.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's edits.
 */

function getEntityRecordEdits(state, kind, name, recordId) {
  return Object(external_lodash_["get"])(state.entities.data, [kind, name, 'edits', recordId]);
}
/**
 * Returns the specified entity record's non transient edits.
 *
 * Transient edits don't create an undo level, and
 * are not considered for change detection.
 * They are defined in the entity's config.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's non transient edits.
 */

var getEntityRecordNonTransientEdits = Object(rememo["a" /* default */])(function (state, kind, name, recordId) {
  var _ref = selectors_getEntity(state, kind, name) || {},
      transientEdits = _ref.transientEdits;

  var edits = getEntityRecordEdits(state, kind, name, recordId) || {};

  if (!transientEdits) {
    return edits;
  }

  return Object.keys(edits).reduce(function (acc, key) {
    if (!transientEdits[key]) {
      acc[key] = edits[key];
    }

    return acc;
  }, {});
}, function (state) {
  return [state.entities.config, state.entities.data];
});
/**
 * Returns true if the specified entity record has edits,
 * and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record has edits or not.
 */

function hasEditsForEntityRecord(state, kind, name, recordId) {
  return isSavingEntityRecord(state, kind, name, recordId) || Object.keys(getEntityRecordNonTransientEdits(state, kind, name, recordId)).length > 0;
}
/**
 * Returns the specified entity record, merged with its edits.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record, merged with its edits.
 */

var getEditedEntityRecord = Object(rememo["a" /* default */])(function (state, kind, name, recordId) {
  return selectors_objectSpread(selectors_objectSpread({}, getRawEntityRecord(state, kind, name, recordId)), getEntityRecordEdits(state, kind, name, recordId));
}, function (state) {
  return [state.entities.data];
});
/**
 * Returns true if the specified entity record is autosaving, and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is autosaving or not.
 */

function isAutosavingEntityRecord(state, kind, name, recordId) {
  var _get = Object(external_lodash_["get"])(state.entities.data, [kind, name, 'saving', recordId], {}),
      pending = _get.pending,
      isAutosave = _get.isAutosave;

  return Boolean(pending && isAutosave);
}
/**
 * Returns true if the specified entity record is saving, and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is saving or not.
 */

function isSavingEntityRecord(state, kind, name, recordId) {
  return Object(external_lodash_["get"])(state.entities.data, [kind, name, 'saving', recordId, 'pending'], false);
}
/**
 * Returns true if the specified entity record is deleting, and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is deleting or not.
 */

function isDeletingEntityRecord(state, kind, name, recordId) {
  return Object(external_lodash_["get"])(state.entities.data, [kind, name, 'deleting', recordId, 'pending'], false);
}
/**
 * Returns the specified entity record's last save error.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's save error.
 */

function getLastEntitySaveError(state, kind, name, recordId) {
  return Object(external_lodash_["get"])(state.entities.data, [kind, name, 'saving', recordId, 'error']);
}
/**
 * Returns the specified entity record's last delete error.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's save error.
 */

function getLastEntityDeleteError(state, kind, name, recordId) {
  return Object(external_lodash_["get"])(state.entities.data, [kind, name, 'deleting', recordId, 'error']);
}
/**
 * Returns the current undo offset for the
 * entity records edits history. The offset
 * represents how many items from the end
 * of the history stack we are at. 0 is the
 * last edit, -1 is the second last, and so on.
 *
 * @param {Object} state State tree.
 *
 * @return {number} The current undo offset.
 */

function getCurrentUndoOffset(state) {
  return state.undo.offset;
}
/**
 * Returns the previous edit from the current undo offset
 * for the entity records edits history, if any.
 *
 * @param {Object} state State tree.
 *
 * @return {Object?} The edit.
 */


function getUndoEdit(state) {
  return state.undo[state.undo.length - 2 + getCurrentUndoOffset(state)];
}
/**
 * Returns the next edit from the current undo offset
 * for the entity records edits history, if any.
 *
 * @param {Object} state State tree.
 *
 * @return {Object?} The edit.
 */

function getRedoEdit(state) {
  return state.undo[state.undo.length + getCurrentUndoOffset(state)];
}
/**
 * Returns true if there is a previous edit from the current undo offset
 * for the entity records edits history, and false otherwise.
 *
 * @param {Object} state State tree.
 *
 * @return {boolean} Whether there is a previous edit or not.
 */

function hasUndo(state) {
  return Boolean(getUndoEdit(state));
}
/**
 * Returns true if there is a next edit from the current undo offset
 * for the entity records edits history, and false otherwise.
 *
 * @param {Object} state State tree.
 *
 * @return {boolean} Whether there is a next edit or not.
 */

function hasRedo(state) {
  return Boolean(getRedoEdit(state));
}
/**
 * Return the current theme.
 *
 * @param {Object} state Data state.
 *
 * @return {Object}      The current theme.
 */

function getCurrentTheme(state) {
  return state.themes[state.currentTheme];
}
/**
 * Return theme supports data in the index.
 *
 * @param {Object} state Data state.
 *
 * @return {*}           Index data.
 */

function getThemeSupports(state) {
  return state.themeSupports;
}
/**
 * Returns the embed preview for the given URL.
 *
 * @param {Object} state    Data state.
 * @param {string} url      Embedded URL.
 *
 * @return {*} Undefined if the preview has not been fetched, otherwise, the preview fetched from the embed preview API.
 */

function getEmbedPreview(state, url) {
  return state.embedPreviews[url];
}
/**
 * Determines if the returned preview is an oEmbed link fallback.
 *
 * WordPress can be configured to return a simple link to a URL if it is not embeddable.
 * We need to be able to determine if a URL is embeddable or not, based on what we
 * get back from the oEmbed preview API.
 *
 * @param {Object} state    Data state.
 * @param {string} url      Embedded URL.
 *
 * @return {boolean} Is the preview for the URL an oEmbed link fallback.
 */

function isPreviewEmbedFallback(state, url) {
  var preview = state.embedPreviews[url];
  var oEmbedLinkCheck = '<a href="' + url + '">' + url + '</a>';

  if (!preview) {
    return false;
  }

  return preview.html === oEmbedLinkCheck;
}
/**
 * Returns whether the current user can upload media.
 *
 * Calling this may trigger an OPTIONS request to the REST API via the
 * `canUser()` resolver.
 *
 * https://developer.wordpress.org/rest-api/reference/
 *
 * @deprecated since 5.0. Callers should use the more generic `canUser()` selector instead of
 *             `hasUploadPermissions()`, e.g. `canUser( 'create', 'media' )`.
 *
 * @param {Object} state Data state.
 *
 * @return {boolean} Whether or not the user can upload media. Defaults to `true` if the OPTIONS
 *                   request is being made.
 */

function hasUploadPermissions(state) {
  external_wp_deprecated_default()("select( 'core' ).hasUploadPermissions()", {
    alternative: "select( 'core' ).canUser( 'create', 'media' )"
  });
  return Object(external_lodash_["defaultTo"])(canUser(state, 'create', 'media'), true);
}
/**
 * Returns whether the current user can perform the given action on the given
 * REST resource.
 *
 * Calling this may trigger an OPTIONS request to the REST API via the
 * `canUser()` resolver.
 *
 * https://developer.wordpress.org/rest-api/reference/
 *
 * @param {Object}   state            Data state.
 * @param {string}   action           Action to check. One of: 'create', 'read', 'update', 'delete'.
 * @param {string}   resource         REST resource to check, e.g. 'media' or 'posts'.
 * @param {string=}  id               Optional ID of the rest resource to check.
 *
 * @return {boolean|undefined} Whether or not the user can perform the action,
 *                             or `undefined` if the OPTIONS request is still being made.
 */

function canUser(state, action, resource, id) {
  var key = Object(external_lodash_["compact"])([action, resource, id]).join('/');
  return Object(external_lodash_["get"])(state, ['userPermissions', key]);
}
/**
 * Returns the latest autosaves for the post.
 *
 * May return multiple autosaves since the backend stores one autosave per
 * author for each post.
 *
 * @param {Object} state    State tree.
 * @param {string} postType The type of the parent post.
 * @param {number} postId   The id of the parent post.
 *
 * @return {?Array} An array of autosaves for the post, or undefined if there is none.
 */

function getAutosaves(state, postType, postId) {
  return state.autosaves[postId];
}
/**
 * Returns the autosave for the post and author.
 *
 * @param {Object} state    State tree.
 * @param {string} postType The type of the parent post.
 * @param {number} postId   The id of the parent post.
 * @param {number} authorId The id of the author.
 *
 * @return {?Object} The autosave for the post and author.
 */

function getAutosave(state, postType, postId, authorId) {
  if (authorId === undefined) {
    return;
  }

  var autosaves = state.autosaves[postId];
  return Object(external_lodash_["find"])(autosaves, {
    author: authorId
  });
}
/**
 * Returns true if the REST request for autosaves has completed.
 *
 * @param {Object} state State tree.
 * @param {string} postType The type of the parent post.
 * @param {number} postId   The id of the parent post.
 *
 * @return {boolean} True if the REST request was completed. False otherwise.
 */

var hasFetchedAutosaves = Object(external_wp_data_["createRegistrySelector"])(function (select) {
  return function (state, postType, postId) {
    return select(STORE_NAME).hasFinishedResolution('getAutosaves', [postType, postId]);
  };
});
/**
 * Returns a new reference when edited values have changed. This is useful in
 * inferring where an edit has been made between states by comparison of the
 * return values using strict equality.
 *
 * @example
 *
 * ```
 * const hasEditOccurred = (
 *    getReferenceByDistinctEdits( beforeState ) !==
 *    getReferenceByDistinctEdits( afterState )
 * );
 * ```
 *
 * @param {Object} state Editor state.
 *
 * @return {*} A value whose reference will change only when an edit occurs.
 */

var getReferenceByDistinctEdits = Object(rememo["a" /* default */])(function () {
  return [];
}, function (state) {
  return [state.undo.length, state.undo.offset, state.undo.flattenedUndo];
});
/**
 * Retrieve the frontend template used for a given link.
 *
 * @param {Object} state Editor state.
 * @param {string} link  Link.
 *
 * @return {Object?} The template record.
 */

function __experimentalGetTemplateForLink(state, link) {
  var records = getEntityRecords(state, 'postType', 'wp_template', {
    'find-template': link
  });
  return records !== null && records !== void 0 && records.length ? records[0] : null;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/utils/if-not-resolved.js


/**
 * WordPress dependencies
 */

/**
 * Higher-order function which invokes the given resolver only if it has not
 * already been resolved with the arguments passed to the enhanced function.
 *
 * This only considers resolution state, and notably does not support resolver
 * custom `isFulfilled` behavior.
 *
 * @param {Function} resolver     Original resolver.
 * @param {string}   selectorName Selector name associated with resolver.
 *
 * @return {Function} Enhanced resolver.
 */

var if_not_resolved_ifNotResolved = function ifNotResolved(resolver, selectorName) {
  return (
    /*#__PURE__*/

    /**
     * @param {...any} args Original resolver arguments.
     */
    external_regeneratorRuntime_default.a.mark(function resolveIfNotResolved() {
      var _len,
          args,
          _key,
          hasStartedResolution,
          _args = arguments;

      return external_regeneratorRuntime_default.a.wrap(function resolveIfNotResolved$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              for (_len = _args.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
                args[_key] = _args[_key];
              }

              _context.next = 3;
              return external_wp_data_["controls"].select('core', 'hasStartedResolution', selectorName, args);

            case 3:
              hasStartedResolution = _context.sent;

              if (hasStartedResolution) {
                _context.next = 6;
                break;
              }

              return _context.delegateYield(resolver.apply(void 0, args), "t0", 6);

            case 6:
            case "end":
              return _context.stop();
          }
        }
      }, resolveIfNotResolved);
    })
  );
};

/* harmony default export */ var if_not_resolved = (if_not_resolved_ifNotResolved);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/resolvers.js




function resolvers_createForOfIteratorHelper(o, allowArrayLike) { var it; if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (it = resolvers_unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }

function resolvers_unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return resolvers_arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return resolvers_arrayLikeToArray(o, minLen); }

function resolvers_arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function resolvers_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function resolvers_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { resolvers_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { resolvers_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var resolvers_marked = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(resolvers_getAuthors),
    resolvers_marked2 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(resolvers_unstableGetAuthor),
    resolvers_marked3 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(resolvers_getCurrentUser),
    resolvers_marked4 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(resolvers_getEntityRecord),
    resolvers_marked5 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(resolvers_getEntityRecords),
    resolvers_marked6 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(resolvers_getCurrentTheme),
    resolvers_marked7 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(resolvers_getThemeSupports),
    _marked8 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(resolvers_getEmbedPreview),
    _marked9 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(resolvers_hasUploadPermissions),
    _marked10 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(resolvers_canUser),
    _marked11 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(resolvers_getAutosaves),
    _marked12 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(resolvers_getAutosave),
    _marked13 = /*#__PURE__*/external_regeneratorRuntime_default.a.mark(resolvers_experimentalGetTemplateForLink);

/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Internal dependencies
 */





/**
 * Requests authors from the REST API.
 *
 * @param {Object|undefined} query Optional object of query parameters to
 *                                 include with request.
 */

function resolvers_getAuthors(query) {
  var path, users;
  return external_regeneratorRuntime_default.a.wrap(function getAuthors$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          path = Object(external_wp_url_["addQueryArgs"])('/wp/v2/users/?who=authors&per_page=100', query);
          _context.next = 3;
          return Object(external_wp_dataControls_["apiFetch"])({
            path: path
          });

        case 3:
          users = _context.sent;
          _context.next = 6;
          return receiveUserQuery(path, users);

        case 6:
        case "end":
          return _context.stop();
      }
    }
  }, resolvers_marked);
}
/**
 * Temporary approach to resolving editor access to author queries.
 *
 * @param {number} id The author id.
 */

function resolvers_unstableGetAuthor(id) {
  var path, users;
  return external_regeneratorRuntime_default.a.wrap(function __unstableGetAuthor$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          path = "/wp/v2/users?who=authors&include=".concat(id);
          _context2.next = 3;
          return Object(external_wp_dataControls_["apiFetch"])({
            path: path
          });

        case 3:
          users = _context2.sent;
          _context2.next = 6;
          return receiveUserQuery('author', users);

        case 6:
        case "end":
          return _context2.stop();
      }
    }
  }, resolvers_marked2);
}
/**
 * Requests the current user from the REST API.
 */

function resolvers_getCurrentUser() {
  var currentUser;
  return external_regeneratorRuntime_default.a.wrap(function getCurrentUser$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          _context3.next = 2;
          return Object(external_wp_dataControls_["apiFetch"])({
            path: '/wp/v2/users/me'
          });

        case 2:
          currentUser = _context3.sent;
          _context3.next = 5;
          return receiveCurrentUser(currentUser);

        case 5:
        case "end":
          return _context3.stop();
      }
    }
  }, resolvers_marked3);
}
/**
 * Requests an entity's record from the REST API.
 *
 * @param {string}           kind  Entity kind.
 * @param {string}           name  Entity name.
 * @param {number|string}    key   Record's key
 * @param {Object|undefined} query Optional object of query parameters to
 *                                 include with request.
 */

function resolvers_getEntityRecord(kind, name) {
  var key,
      query,
      entities,
      entity,
      lock,
      path,
      hasRecords,
      record,
      _args4 = arguments;
  return external_regeneratorRuntime_default.a.wrap(function getEntityRecord$(_context4) {
    while (1) {
      switch (_context4.prev = _context4.next) {
        case 0:
          key = _args4.length > 2 && _args4[2] !== undefined ? _args4[2] : '';
          query = _args4.length > 3 ? _args4[3] : undefined;
          _context4.next = 4;
          return getKindEntities(kind);

        case 4:
          entities = _context4.sent;
          entity = Object(external_lodash_["find"])(entities, {
            kind: kind,
            name: name
          });

          if (entity) {
            _context4.next = 8;
            break;
          }

          return _context4.abrupt("return");

        case 8:
          return _context4.delegateYield(__unstableAcquireStoreLock('core', ['entities', 'data', kind, name, key], {
            exclusive: false
          }), "t0", 9);

        case 9:
          lock = _context4.t0;
          _context4.prev = 10;

          if (query !== undefined && query._fields) {
            // If requesting specific fields, items and query assocation to said
            // records are stored by ID reference. Thus, fields must always include
            // the ID.
            query = resolvers_objectSpread(resolvers_objectSpread({}, query), {}, {
              _fields: Object(external_lodash_["uniq"])([].concat(Object(toConsumableArray["a" /* default */])(get_normalized_comma_separable(query._fields) || []), [entity.key || DEFAULT_ENTITY_KEY])).join()
            });
          } // Disable reason: While true that an early return could leave `path`
          // unused, it's important that path is derived using the query prior to
          // additional query modifications in the condition below, since those
          // modifications are relevant to how the data is tracked in state, and not
          // for how the request is made to the REST API.
          // eslint-disable-next-line @wordpress/no-unused-vars-before-return


          path = Object(external_wp_url_["addQueryArgs"])(entity.baseURL + '/' + key, resolvers_objectSpread(resolvers_objectSpread({}, query), {}, {
            context: 'edit'
          }));

          if (!(query !== undefined)) {
            _context4.next = 20;
            break;
          }

          query = resolvers_objectSpread(resolvers_objectSpread({}, query), {}, {
            include: [key]
          }); // The resolution cache won't consider query as reusable based on the
          // fields, so it's tested here, prior to initiating the REST request,
          // and without causing `getEntityRecords` resolution to occur.

          _context4.next = 17;
          return external_wp_data_["controls"].select('core', 'hasEntityRecords', kind, name, query);

        case 17:
          hasRecords = _context4.sent;

          if (!hasRecords) {
            _context4.next = 20;
            break;
          }

          return _context4.abrupt("return");

        case 20:
          _context4.next = 22;
          return Object(external_wp_dataControls_["apiFetch"])({
            path: path
          });

        case 22:
          record = _context4.sent;
          _context4.next = 25;
          return receiveEntityRecords(kind, name, record, query);

        case 25:
          _context4.next = 29;
          break;

        case 27:
          _context4.prev = 27;
          _context4.t1 = _context4["catch"](10);

        case 29:
          _context4.prev = 29;
          return _context4.delegateYield(__unstableReleaseStoreLock(lock), "t2", 31);

        case 31:
          return _context4.finish(29);

        case 32:
        case "end":
          return _context4.stop();
      }
    }
  }, resolvers_marked4, null, [[10, 27, 29, 32]]);
}
/**
 * Requests an entity's record from the REST API.
 */

var resolvers_getRawEntityRecord = if_not_resolved(resolvers_getEntityRecord, 'getEntityRecord');
/**
 * Requests an entity's record from the REST API.
 */

var resolvers_getEditedEntityRecord = if_not_resolved(resolvers_getRawEntityRecord, 'getRawEntityRecord');
/**
 * Requests the entity's records from the REST API.
 *
 * @param {string}  kind   Entity kind.
 * @param {string}  name   Entity name.
 * @param {Object?} query  Query Object.
 */

function resolvers_getEntityRecords(kind, name) {
  var query,
      entities,
      entity,
      lock,
      _query,
      path,
      records,
      key,
      _iterator,
      _step,
      record,
      _args5 = arguments;

  return external_regeneratorRuntime_default.a.wrap(function getEntityRecords$(_context5) {
    while (1) {
      switch (_context5.prev = _context5.next) {
        case 0:
          query = _args5.length > 2 && _args5[2] !== undefined ? _args5[2] : {};
          _context5.next = 3;
          return getKindEntities(kind);

        case 3:
          entities = _context5.sent;
          entity = Object(external_lodash_["find"])(entities, {
            kind: kind,
            name: name
          });

          if (entity) {
            _context5.next = 7;
            break;
          }

          return _context5.abrupt("return");

        case 7:
          return _context5.delegateYield(__unstableAcquireStoreLock('core', ['entities', 'data', kind, name], {
            exclusive: false
          }), "t0", 8);

        case 8:
          lock = _context5.t0;
          _context5.prev = 9;

          if (query._fields) {
            // If requesting specific fields, items and query assocation to said
            // records are stored by ID reference. Thus, fields must always include
            // the ID.
            query = resolvers_objectSpread(resolvers_objectSpread({}, query), {}, {
              _fields: Object(external_lodash_["uniq"])([].concat(Object(toConsumableArray["a" /* default */])(get_normalized_comma_separable(query._fields) || []), [entity.key || DEFAULT_ENTITY_KEY])).join()
            });
          }

          path = Object(external_wp_url_["addQueryArgs"])(entity.baseURL, resolvers_objectSpread(resolvers_objectSpread({}, query), {}, {
            context: 'edit'
          }));
          _context5.t1 = Object;
          _context5.next = 15;
          return Object(external_wp_dataControls_["apiFetch"])({
            path: path
          });

        case 15:
          _context5.t2 = _context5.sent;
          records = _context5.t1.values.call(_context5.t1, _context5.t2);

          // If we request fields but the result doesn't contain the fields,
          // explicitely set these fields as "undefined"
          // that way we consider the query "fullfilled".
          if (query._fields) {
            records = records.map(function (record) {
              query._fields.split(',').forEach(function (field) {
                if (!record.hasOwnProperty(field)) {
                  record[field] = undefined;
                }
              });

              return record;
            });
          }

          _context5.next = 20;
          return receiveEntityRecords(kind, name, records, query);

        case 20:
          if ((_query = query) !== null && _query !== void 0 && _query._fields) {
            _context5.next = 42;
            break;
          }

          key = entity.key || DEFAULT_ENTITY_KEY;
          _iterator = resolvers_createForOfIteratorHelper(records);
          _context5.prev = 23;

          _iterator.s();

        case 25:
          if ((_step = _iterator.n()).done) {
            _context5.next = 34;
            break;
          }

          record = _step.value;

          if (!record[key]) {
            _context5.next = 32;
            break;
          }

          _context5.next = 30;
          return {
            type: 'START_RESOLUTION',
            selectorName: 'getEntityRecord',
            args: [kind, name, record[key]]
          };

        case 30:
          _context5.next = 32;
          return {
            type: 'FINISH_RESOLUTION',
            selectorName: 'getEntityRecord',
            args: [kind, name, record[key]]
          };

        case 32:
          _context5.next = 25;
          break;

        case 34:
          _context5.next = 39;
          break;

        case 36:
          _context5.prev = 36;
          _context5.t3 = _context5["catch"](23);

          _iterator.e(_context5.t3);

        case 39:
          _context5.prev = 39;

          _iterator.f();

          return _context5.finish(39);

        case 42:
          _context5.prev = 42;
          return _context5.delegateYield(__unstableReleaseStoreLock(lock), "t4", 44);

        case 44:
          return _context5.finish(42);

        case 45:
        case "end":
          return _context5.stop();
      }
    }
  }, resolvers_marked5, null, [[9,, 42, 45], [23, 36, 39, 42]]);
}

resolvers_getEntityRecords.shouldInvalidate = function (action, kind, name) {
  return (action.type === 'RECEIVE_ITEMS' || action.type === 'REMOVE_ITEMS') && action.invalidateCache && kind === action.kind && name === action.name;
};
/**
 * Requests the current theme.
 */


function resolvers_getCurrentTheme() {
  var activeThemes;
  return external_regeneratorRuntime_default.a.wrap(function getCurrentTheme$(_context6) {
    while (1) {
      switch (_context6.prev = _context6.next) {
        case 0:
          _context6.next = 2;
          return Object(external_wp_dataControls_["apiFetch"])({
            path: '/wp/v2/themes?status=active'
          });

        case 2:
          activeThemes = _context6.sent;
          _context6.next = 5;
          return receiveCurrentTheme(activeThemes[0]);

        case 5:
        case "end":
          return _context6.stop();
      }
    }
  }, resolvers_marked6);
}
/**
 * Requests theme supports data from the index.
 */

function resolvers_getThemeSupports() {
  var activeThemes;
  return external_regeneratorRuntime_default.a.wrap(function getThemeSupports$(_context7) {
    while (1) {
      switch (_context7.prev = _context7.next) {
        case 0:
          _context7.next = 2;
          return Object(external_wp_dataControls_["apiFetch"])({
            path: '/wp/v2/themes?status=active'
          });

        case 2:
          activeThemes = _context7.sent;
          _context7.next = 5;
          return receiveThemeSupports(activeThemes[0].theme_supports);

        case 5:
        case "end":
          return _context7.stop();
      }
    }
  }, resolvers_marked7);
}
/**
 * Requests a preview from the from the Embed API.
 *
 * @param {string} url   URL to get the preview for.
 */

function resolvers_getEmbedPreview(url) {
  var embedProxyResponse;
  return external_regeneratorRuntime_default.a.wrap(function getEmbedPreview$(_context8) {
    while (1) {
      switch (_context8.prev = _context8.next) {
        case 0:
          _context8.prev = 0;
          _context8.next = 3;
          return Object(external_wp_dataControls_["apiFetch"])({
            path: Object(external_wp_url_["addQueryArgs"])('/oembed/1.0/proxy', {
              url: url
            })
          });

        case 3:
          embedProxyResponse = _context8.sent;
          _context8.next = 6;
          return receiveEmbedPreview(url, embedProxyResponse);

        case 6:
          _context8.next = 12;
          break;

        case 8:
          _context8.prev = 8;
          _context8.t0 = _context8["catch"](0);
          _context8.next = 12;
          return receiveEmbedPreview(url, false);

        case 12:
        case "end":
          return _context8.stop();
      }
    }
  }, _marked8, null, [[0, 8]]);
}
/**
 * Requests Upload Permissions from the REST API.
 *
 * @deprecated since 5.0. Callers should use the more generic `canUser()` selector instead of
 *            `hasUploadPermissions()`, e.g. `canUser( 'create', 'media' )`.
 */

function resolvers_hasUploadPermissions() {
  return external_regeneratorRuntime_default.a.wrap(function hasUploadPermissions$(_context9) {
    while (1) {
      switch (_context9.prev = _context9.next) {
        case 0:
          external_wp_deprecated_default()("select( 'core' ).hasUploadPermissions()", {
            alternative: "select( 'core' ).canUser( 'create', 'media' )"
          });
          return _context9.delegateYield(resolvers_canUser('create', 'media'), "t0", 2);

        case 2:
        case "end":
          return _context9.stop();
      }
    }
  }, _marked9);
}
/**
 * Checks whether the current user can perform the given action on the given
 * REST resource.
 *
 * @param {string}  action   Action to check. One of: 'create', 'read', 'update',
 *                           'delete'.
 * @param {string}  resource REST resource to check, e.g. 'media' or 'posts'.
 * @param {?string} id       ID of the rest resource to check.
 */

function resolvers_canUser(action, resource, id) {
  var methods, method, path, response, allowHeader, key, isAllowed;
  return external_regeneratorRuntime_default.a.wrap(function canUser$(_context10) {
    while (1) {
      switch (_context10.prev = _context10.next) {
        case 0:
          methods = {
            create: 'POST',
            read: 'GET',
            update: 'PUT',
            delete: 'DELETE'
          };
          method = methods[action];

          if (method) {
            _context10.next = 4;
            break;
          }

          throw new Error("'".concat(action, "' is not a valid action."));

        case 4:
          path = id ? "/wp/v2/".concat(resource, "/").concat(id) : "/wp/v2/".concat(resource);
          _context10.prev = 5;
          _context10.next = 8;
          return Object(external_wp_dataControls_["apiFetch"])({
            path: path,
            // Ideally this would always be an OPTIONS request, but unfortunately there's
            // a bug in the REST API which causes the Allow header to not be sent on
            // OPTIONS requests to /posts/:id routes.
            // https://core.trac.wordpress.org/ticket/45753
            method: id ? 'GET' : 'OPTIONS',
            parse: false
          });

        case 8:
          response = _context10.sent;
          _context10.next = 14;
          break;

        case 11:
          _context10.prev = 11;
          _context10.t0 = _context10["catch"](5);
          return _context10.abrupt("return");

        case 14:
          if (Object(external_lodash_["hasIn"])(response, ['headers', 'get'])) {
            // If the request is fetched using the fetch api, the header can be
            // retrieved using the 'get' method.
            allowHeader = response.headers.get('allow');
          } else {
            // If the request was preloaded server-side and is returned by the
            // preloading middleware, the header will be a simple property.
            allowHeader = Object(external_lodash_["get"])(response, ['headers', 'Allow'], '');
          }

          key = Object(external_lodash_["compact"])([action, resource, id]).join('/');
          isAllowed = Object(external_lodash_["includes"])(allowHeader, method);
          _context10.next = 19;
          return receiveUserPermission(key, isAllowed);

        case 19:
        case "end":
          return _context10.stop();
      }
    }
  }, _marked10, null, [[5, 11]]);
}
/**
 * Request autosave data from the REST API.
 *
 * @param {string} postType The type of the parent post.
 * @param {number} postId   The id of the parent post.
 */

function resolvers_getAutosaves(postType, postId) {
  var _yield$controls$resol, restBase, autosaves;

  return external_regeneratorRuntime_default.a.wrap(function getAutosaves$(_context11) {
    while (1) {
      switch (_context11.prev = _context11.next) {
        case 0:
          _context11.next = 2;
          return external_wp_data_["controls"].resolveSelect('core', 'getPostType', postType);

        case 2:
          _yield$controls$resol = _context11.sent;
          restBase = _yield$controls$resol.rest_base;
          _context11.next = 6;
          return Object(external_wp_dataControls_["apiFetch"])({
            path: "/wp/v2/".concat(restBase, "/").concat(postId, "/autosaves?context=edit")
          });

        case 6:
          autosaves = _context11.sent;

          if (!(autosaves && autosaves.length)) {
            _context11.next = 10;
            break;
          }

          _context11.next = 10;
          return receiveAutosaves(postId, autosaves);

        case 10:
        case "end":
          return _context11.stop();
      }
    }
  }, _marked11);
}
/**
 * Request autosave data from the REST API.
 *
 * This resolver exists to ensure the underlying autosaves are fetched via
 * `getAutosaves` when a call to the `getAutosave` selector is made.
 *
 * @param {string} postType The type of the parent post.
 * @param {number} postId   The id of the parent post.
 */

function resolvers_getAutosave(postType, postId) {
  return external_regeneratorRuntime_default.a.wrap(function getAutosave$(_context12) {
    while (1) {
      switch (_context12.prev = _context12.next) {
        case 0:
          _context12.next = 2;
          return external_wp_data_["controls"].resolveSelect('core', 'getAutosaves', postType, postId);

        case 2:
        case "end":
          return _context12.stop();
      }
    }
  }, _marked12);
}
/**
 * Retrieve the frontend template used for a given link.
 *
 * @param {string} link  Link.
 */

function resolvers_experimentalGetTemplateForLink(link) {
  var template, record;
  return external_regeneratorRuntime_default.a.wrap(function __experimentalGetTemplateForLink$(_context13) {
    while (1) {
      switch (_context13.prev = _context13.next) {
        case 0:
          _context13.next = 2;
          return regularFetch(Object(external_wp_url_["addQueryArgs"])(link, {
            '_wp-find-template': true
          }));

        case 2:
          template = _context13.sent;

          if (!(template === null)) {
            _context13.next = 5;
            break;
          }

          return _context13.abrupt("return");

        case 5:
          _context13.next = 7;
          return resolvers_getEntityRecord('postType', 'wp_template', template.id);

        case 7:
          _context13.next = 9;
          return external_wp_data_["controls"].select('core', 'getEntityRecord', 'postType', 'wp_template', template.id);

        case 9:
          record = _context13.sent;

          if (!record) {
            _context13.next = 13;
            break;
          }

          _context13.next = 13;
          return receiveEntityRecords('postType', 'wp_template', [record], {
            'find-template': link
          });

        case 13:
        case "end":
          return _context13.stop();
      }
    }
  }, _marked13);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/locks/selectors.js


function selectors_createForOfIteratorHelper(o, allowArrayLike) { var it; if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (it = selectors_unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }

function selectors_unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return selectors_arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return selectors_arrayLikeToArray(o, minLen); }

function selectors_arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

/**
 * Internal dependencies
 */

function __unstableGetPendingLockRequests(state) {
  return state.locks.requests;
}
function __unstableIsLockAvailable(state, store, path, _ref) {
  var exclusive = _ref.exclusive;
  var storePath = [store].concat(Object(toConsumableArray["a" /* default */])(path));
  var locks = state.locks.tree; // Validate all parents and the node itself

  var _iterator = selectors_createForOfIteratorHelper(iteratePath(locks, storePath)),
      _step;

  try {
    for (_iterator.s(); !(_step = _iterator.n()).done;) {
      var _node = _step.value;

      if (hasConflictingLock({
        exclusive: exclusive
      }, _node.locks)) {
        return false;
      }
    } // iteratePath terminates early if path is unreachable, let's
    // re-fetch the node and check it exists in the tree.

  } catch (err) {
    _iterator.e(err);
  } finally {
    _iterator.f();
  }

  var node = getNode(locks, storePath);

  if (!node) {
    return true;
  } // Validate all nested nodes


  var _iterator2 = selectors_createForOfIteratorHelper(iterateDescendants(node)),
      _step2;

  try {
    for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
      var descendant = _step2.value;

      if (hasConflictingLock({
        exclusive: exclusive
      }, descendant.locks)) {
        return false;
      }
    }
  } catch (err) {
    _iterator2.e(err);
  } finally {
    _iterator2.f();
  }

  return true;
}

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__("HSyU");

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/entity-provider.js



function entity_provider_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function entity_provider_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { entity_provider_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { entity_provider_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * WordPress dependencies
 */



var entity_provider_EMPTY_ARRAY = [];
/**
 * Internal dependencies
 */



var entity_provider_entities = entity_provider_objectSpread(entity_provider_objectSpread({}, defaultEntities.reduce(function (acc, entity) {
  if (!acc[entity.kind]) {
    acc[entity.kind] = {};
  }

  acc[entity.kind][entity.name] = {
    context: Object(external_wp_element_["createContext"])()
  };
  return acc;
}, {})), kinds.reduce(function (acc, kind) {
  acc[kind.name] = {};
  return acc;
}, {}));

var entity_provider_getEntity = function getEntity(kind, type) {
  if (!entity_provider_entities[kind]) {
    throw new Error("Missing entity config for kind: ".concat(kind, "."));
  }

  if (!entity_provider_entities[kind][type]) {
    entity_provider_entities[kind][type] = {
      context: Object(external_wp_element_["createContext"])()
    };
  }

  return entity_provider_entities[kind][type];
};
/**
 * Context provider component for providing
 * an entity for a specific entity type.
 *
 * @param {Object} props          The component's props.
 * @param {string} props.kind     The entity kind.
 * @param {string} props.type     The entity type.
 * @param {number} props.id       The entity ID.
 * @param {*}      props.children The children to wrap.
 *
 * @return {Object} The provided children, wrapped with
 *                   the entity's context provider.
 */


function EntityProvider(_ref) {
  var kind = _ref.kind,
      type = _ref.type,
      id = _ref.id,
      children = _ref.children;
  var Provider = entity_provider_getEntity(kind, type).context.Provider;
  return Object(external_wp_element_["createElement"])(Provider, {
    value: id
  }, children);
}
/**
 * Hook that returns the ID for the nearest
 * provided entity of the specified type.
 *
 * @param {string} kind The entity kind.
 * @param {string} type The entity type.
 */

function useEntityId(kind, type) {
  return Object(external_wp_element_["useContext"])(entity_provider_getEntity(kind, type).context);
}
/**
 * Hook that returns the value and a setter for the
 * specified property of the nearest provided
 * entity of the specified type.
 *
 * @param {string} kind  The entity kind.
 * @param {string} type  The entity type.
 * @param {string} prop  The property name.
 * @param {string} [_id] An entity ID to use instead of the context-provided one.
 *
 * @return {[*, Function]} A tuple where the first item is the
 *                          property value and the second is the
 *                          setter.
 */

function useEntityProp(kind, type, prop, _id) {
  var providerId = useEntityId(kind, type);
  var id = _id !== null && _id !== void 0 ? _id : providerId;

  var _useSelect = Object(external_wp_data_["useSelect"])(function (select) {
    var _select = select('core'),
        getEntityRecord = _select.getEntityRecord,
        getEditedEntityRecord = _select.getEditedEntityRecord;

    var entity = getEntityRecord(kind, type, id); // Trigger resolver.

    var editedEntity = getEditedEntityRecord(kind, type, id);
    return entity && editedEntity ? {
      value: editedEntity[prop],
      fullValue: entity[prop]
    } : {};
  }, [kind, type, id, prop]),
      value = _useSelect.value,
      fullValue = _useSelect.fullValue;

  var _useDispatch = Object(external_wp_data_["useDispatch"])('core'),
      editEntityRecord = _useDispatch.editEntityRecord;

  var setValue = Object(external_wp_element_["useCallback"])(function (newValue) {
    editEntityRecord(kind, type, id, Object(defineProperty["a" /* default */])({}, prop, newValue));
  }, [kind, type, id, prop]);
  return [value, setValue, fullValue];
}
/**
 * Hook that returns block content getters and setters for
 * the nearest provided entity of the specified type.
 *
 * The return value has the shape `[ blocks, onInput, onChange ]`.
 * `onInput` is for block changes that don't create undo levels
 * or dirty the post, non-persistent changes, and `onChange` is for
 * peristent changes. They map directly to the props of a
 * `BlockEditorProvider` and are intended to be used with it,
 * or similar components or hooks.
 *
 * @param {string} kind                            The entity kind.
 * @param {string} type                            The entity type.
 * @param {Object} options
 * @param {string} [options.id]                    An entity ID to use instead of the context-provided one.
 *
 * @return {[WPBlock[], Function, Function]} The block array and setters.
 */

function useEntityBlockEditor(kind, type) {
  var _ref2 = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {},
      _id = _ref2.id;

  var providerId = useEntityId(kind, type);
  var id = _id !== null && _id !== void 0 ? _id : providerId;

  var _useSelect2 = Object(external_wp_data_["useSelect"])(function (select) {
    var _select2 = select('core'),
        getEditedEntityRecord = _select2.getEditedEntityRecord;

    var editedEntity = getEditedEntityRecord(kind, type, id);
    return {
      blocks: editedEntity.blocks,
      content: editedEntity.content
    };
  }, [kind, type, id]),
      content = _useSelect2.content,
      blocks = _useSelect2.blocks;

  var _useDispatch2 = Object(external_wp_data_["useDispatch"])('core'),
      __unstableCreateUndoLevel = _useDispatch2.__unstableCreateUndoLevel,
      editEntityRecord = _useDispatch2.editEntityRecord;

  Object(external_wp_element_["useEffect"])(function () {
    // Load the blocks from the content if not already in state
    // Guard against other instances that might have
    // set content to a function already or the blocks are already in state.
    if (content && typeof content !== 'function' && !blocks) {
      var parsedContent = Object(external_wp_blocks_["parse"])(content);
      editEntityRecord(kind, type, id, {
        blocks: parsedContent
      }, {
        undoIgnore: true
      });
    }
  }, [content]);
  var onChange = Object(external_wp_element_["useCallback"])(function (newBlocks, options) {
    var selectionStart = options.selectionStart,
        selectionEnd = options.selectionEnd;
    var edits = {
      blocks: newBlocks,
      selectionStart: selectionStart,
      selectionEnd: selectionEnd
    };
    var noChange = blocks === edits.blocks;

    if (noChange) {
      return __unstableCreateUndoLevel(kind, type, id);
    } // We create a new function here on every persistent edit
    // to make sure the edit makes the post dirty and creates
    // a new undo level.


    edits.content = function (_ref3) {
      var _ref3$blocks = _ref3.blocks,
          blocksForSerialization = _ref3$blocks === void 0 ? [] : _ref3$blocks;
      return Object(external_wp_blocks_["__unstableSerializeAndClean"])(blocksForSerialization);
    };

    editEntityRecord(kind, type, id, edits);
  }, [kind, type, id, blocks]);
  var onInput = Object(external_wp_element_["useCallback"])(function (newBlocks, options) {
    var selectionStart = options.selectionStart,
        selectionEnd = options.selectionEnd;
    var edits = {
      blocks: newBlocks,
      selectionStart: selectionStart,
      selectionEnd: selectionEnd
    };
    editEntityRecord(kind, type, id, edits);
  }, [kind, type, id]);
  return [blocks !== null && blocks !== void 0 ? blocks : entity_provider_EMPTY_ARRAY, onInput, onChange];
}

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/index.js


function build_module_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function build_module_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { build_module_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { build_module_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */









 // The entity selectors/resolvers and actions are shortcuts to their generic equivalents
// (getEntityRecord, getEntityRecords, updateEntityRecord, updateEntityRecordss)
// Instead of getEntityRecord, the consumer could use more user-frieldly named selector: getPostType, getTaxonomy...
// The "kind" and the "name" of the entity are combined to generate these shortcuts.

var entitySelectors = defaultEntities.reduce(function (result, entity) {
  var kind = entity.kind,
      name = entity.name;

  result[entities_getMethodName(kind, name)] = function (state, key) {
    return getEntityRecord(state, kind, name, key);
  };

  result[entities_getMethodName(kind, name, 'get', true)] = function (state) {
    for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      args[_key - 1] = arguments[_key];
    }

    return getEntityRecords.apply(build_module_selectors_namespaceObject, [state, kind, name].concat(args));
  };

  return result;
}, {});
var entityResolvers = defaultEntities.reduce(function (result, entity) {
  var kind = entity.kind,
      name = entity.name;

  result[entities_getMethodName(kind, name)] = function (key) {
    return resolvers_getEntityRecord(kind, name, key);
  };

  var pluralMethodName = entities_getMethodName(kind, name, 'get', true);

  result[pluralMethodName] = function () {
    for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
      args[_key2] = arguments[_key2];
    }

    return resolvers_getEntityRecords.apply(resolvers_namespaceObject, [kind, name].concat(args));
  };

  result[pluralMethodName].shouldInvalidate = function (action) {
    var _resolvers$getEntityR;

    for (var _len3 = arguments.length, args = new Array(_len3 > 1 ? _len3 - 1 : 0), _key3 = 1; _key3 < _len3; _key3++) {
      args[_key3 - 1] = arguments[_key3];
    }

    return (_resolvers$getEntityR = resolvers_getEntityRecords).shouldInvalidate.apply(_resolvers$getEntityR, [action, kind, name].concat(args));
  };

  return result;
}, {});
var entityActions = defaultEntities.reduce(function (result, entity) {
  var kind = entity.kind,
      name = entity.name;

  result[entities_getMethodName(kind, name, 'save')] = function (key) {
    return saveEntityRecord(kind, name, key);
  };

  result[entities_getMethodName(kind, name, 'delete')] = function (key, query) {
    return deleteEntityRecord(kind, name, key, query);
  };

  return result;
}, {});
var storeConfig = {
  reducer: build_module_reducer,
  controls: build_module_objectSpread(build_module_objectSpread({}, build_module_controls), external_wp_dataControls_["controls"]),
  actions: build_module_objectSpread(build_module_objectSpread(build_module_objectSpread({}, build_module_actions_namespaceObject), entityActions), locks_actions_namespaceObject),
  selectors: build_module_objectSpread(build_module_objectSpread(build_module_objectSpread({}, build_module_selectors_namespaceObject), entitySelectors), locks_selectors_namespaceObject),
  resolvers: build_module_objectSpread(build_module_objectSpread({}, resolvers_namespaceObject), entityResolvers)
};
/**
 * Store definition for the code data namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

var build_module_store = Object(external_wp_data_["createReduxStore"])(STORE_NAME, storeConfig);
Object(external_wp_data_["register"])(build_module_store);




/***/ }),

/***/ "dvlR":
/***/ (function(module, exports) {

(function() { module.exports = window["regeneratorRuntime"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ "pPDe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";


var LEAF_KEY, hasWeakMap;

/**
 * Arbitrary value used as key for referencing cache object in WeakMap tree.
 *
 * @type {Object}
 */
LEAF_KEY = {};

/**
 * Whether environment supports WeakMap.
 *
 * @type {boolean}
 */
hasWeakMap = typeof WeakMap !== 'undefined';

/**
 * Returns the first argument as the sole entry in an array.
 *
 * @param {*} value Value to return.
 *
 * @return {Array} Value returned as entry in array.
 */
function arrayOf( value ) {
	return [ value ];
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
function isObjectLike( value ) {
	return !! value && 'object' === typeof value;
}

/**
 * Creates and returns a new cache object.
 *
 * @return {Object} Cache object.
 */
function createCache() {
	var cache = {
		clear: function() {
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
 * @param {Array}  a         First array.
 * @param {Array}  b         Second array.
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
function isShallowEqual( a, b, fromIndex ) {
	var i;

	if ( a.length !== b.length ) {
		return false;
	}

	for ( i = fromIndex; i < a.length; i++ ) {
		if ( a[ i ] !== b[ i ] ) {
			return false;
		}
	}

	return true;
}

/**
 * Returns a memoized selector function. The getDependants function argument is
 * called before the memoized selector and is expected to return an immutable
 * reference or array of references on which the selector depends for computing
 * its own return value. The memoize cache is preserved only as long as those
 * dependant references remain the same. If getDependants returns a different
 * reference(s), the cache is cleared and the selector value regenerated.
 *
 * @param {Function} selector      Selector function.
 * @param {Function} getDependants Dependant getter returning an immutable
 *                                 reference or array of reference used in
 *                                 cache bust consideration.
 *
 * @return {Function} Memoized selector.
 */
/* harmony default export */ __webpack_exports__["a"] = (function( selector, getDependants ) {
	var rootCache, getCache;

	// Use object source as dependant if getter not provided
	if ( ! getDependants ) {
		getDependants = arrayOf;
	}

	/**
	 * Returns the root cache. If WeakMap is supported, this is assigned to the
	 * root WeakMap cache set, otherwise it is a shared instance of the default
	 * cache object.
	 *
	 * @return {(WeakMap|Object)} Root cache object.
	 */
	function getRootCache() {
		return rootCache;
	}

	/**
	 * Returns the cache for a given dependants array. When possible, a WeakMap
	 * will be used to create a unique cache for each set of dependants. This
	 * is feasible due to the nature of WeakMap in allowing garbage collection
	 * to occur on entries where the key object is no longer referenced. Since
	 * WeakMap requires the key to be an object, this is only possible when the
	 * dependant is object-like. The root cache is created as a hierarchy where
	 * each top-level key is the first entry in a dependants set, the value a
	 * WeakMap where each key is the next dependant, and so on. This continues
	 * so long as the dependants are object-like. If no dependants are object-
	 * like, then the cache is shared across all invocations.
	 *
	 * @see isObjectLike
	 *
	 * @param {Array} dependants Selector dependants.
	 *
	 * @return {Object} Cache object.
	 */
	function getWeakMapCache( dependants ) {
		var caches = rootCache,
			isUniqueByDependants = true,
			i, dependant, map, cache;

		for ( i = 0; i < dependants.length; i++ ) {
			dependant = dependants[ i ];

			// Can only compose WeakMap from object-like key.
			if ( ! isObjectLike( dependant ) ) {
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
			if ( caches.has( dependant ) ) {
				// Traverse into nested WeakMap.
				caches = caches.get( dependant );
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set( dependant, map );
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
		if ( ! caches.has( LEAF_KEY ) ) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set( LEAF_KEY, cache );
		}

		return caches.get( LEAF_KEY );
	}

	// Assign cache handler by availability of WeakMap
	getCache = hasWeakMap ? getWeakMapCache : getRootCache;

	/**
	 * Resets root memoization cache.
	 */
	function clear() {
		rootCache = hasWeakMap ? new WeakMap() : createCache();
	}

	// eslint-disable-next-line jsdoc/check-param-names
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
	 * @param {Object} source    Source object for derivation.
	 * @param {...*}   extraArgs Additional arguments to pass to selector.
	 *
	 * @return {*} Selector result.
	 */
	function callSelector( /* source, ...extraArgs */ ) {
		var len = arguments.length,
			cache, node, i, args, dependants;

		// Create copy of arguments (avoid leaking deoptimization).
		args = new Array( len );
		for ( i = 0; i < len; i++ ) {
			args[ i ] = arguments[ i ];
		}

		dependants = getDependants.apply( null, args );
		cache = getCache( dependants );

		// If not guaranteed uniqueness by dependants (primitive type or lack
		// of WeakMap support), shallow compare against last dependants and, if
		// references have changed, destroy cache to recalculate result.
		if ( ! cache.isUniqueByDependants ) {
			if ( cache.lastDependants && ! isShallowEqual( dependants, cache.lastDependants, 0 ) ) {
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
		while ( node ) {
			// Check whether node arguments match arguments
			if ( ! isShallowEqual( node.args, args, 1 ) ) {
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if ( node !== cache.head ) {
				// Adjust siblings to point to each other.
				node.prev.next = node.next;
				if ( node.next ) {
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
				cache.head.prev = node;
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		node = {
			// Generate the result from original function
			val: selector.apply( null, args ),
		};

		// Avoid including the source object in the cache.
		args[ 0 ] = null;
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if ( cache.head ) {
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

	callSelector.getDependants = getDependants;
	callSelector.clear = clear;
	clear();

	return callSelector;
});


/***/ }),

/***/ "rePB":
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

/***/ }),

/***/ "rl8x":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["isShallowEqual"]; }());

/***/ }),

/***/ "s4An":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _setPrototypeOf; });
function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}

/***/ }),

/***/ "vuIU":
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

/***/ "ywyh":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["apiFetch"]; }());

/***/ })

/******/ });