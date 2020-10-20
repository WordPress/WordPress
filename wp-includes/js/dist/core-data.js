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
/******/ 	return __webpack_require__(__webpack_require__.s = 465);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ 1:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ }),

/***/ 106:
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

/***/ 11:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),

/***/ 12:
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
var unsupportedIterableToArray = __webpack_require__(30);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
var nonIterableRest = __webpack_require__(39);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js




function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(unsupportedIterableToArray["a" /* default */])(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ 17:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _toConsumableArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js
var arrayLikeToArray = __webpack_require__(27);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return Object(arrayLikeToArray["a" /* default */])(arr);
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
var iterableToArray = __webpack_require__(37);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
var unsupportedIterableToArray = __webpack_require__(30);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js




function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || Object(iterableToArray["a" /* default */])(arr) || Object(unsupportedIterableToArray["a" /* default */])(arr) || _nonIterableSpread();
}

/***/ }),

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ 20:
/***/ (function(module, exports) {

(function() { module.exports = this["regeneratorRuntime"]; }());

/***/ }),

/***/ 27:
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

/***/ 30:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _unsupportedIterableToArray; });
/* harmony import */ var _arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(27);

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return Object(_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return Object(_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
}

/***/ }),

/***/ 31:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["url"]; }());

/***/ }),

/***/ 32:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["dataControls"]; }());

/***/ }),

/***/ 36:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["deprecated"]; }());

/***/ }),

/***/ 37:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
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

/***/ 4:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ 42:
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

/***/ 465:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "EntityProvider", function() { return /* reexport */ EntityProvider; });
__webpack_require__.d(__webpack_exports__, "useEntityId", function() { return /* reexport */ useEntityId; });
__webpack_require__.d(__webpack_exports__, "useEntityProp", function() { return /* reexport */ useEntityProp; });
__webpack_require__.d(__webpack_exports__, "useEntityBlockEditor", function() { return /* reexport */ useEntityBlockEditor; });

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
__webpack_require__.d(build_module_actions_namespaceObject, "__unstableCreateUndoLevel", function() { return __unstableCreateUndoLevel; });
__webpack_require__.d(build_module_actions_namespaceObject, "saveEntityRecord", function() { return saveEntityRecord; });
__webpack_require__.d(build_module_actions_namespaceObject, "saveEditedEntityRecord", function() { return saveEditedEntityRecord; });
__webpack_require__.d(build_module_actions_namespaceObject, "receiveUploadPermissions", function() { return receiveUploadPermissions; });
__webpack_require__.d(build_module_actions_namespaceObject, "receiveUserPermission", function() { return receiveUserPermission; });
__webpack_require__.d(build_module_actions_namespaceObject, "receiveAutosaves", function() { return receiveAutosaves; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/core-data/build-module/selectors.js
var build_module_selectors_namespaceObject = {};
__webpack_require__.r(build_module_selectors_namespaceObject);
__webpack_require__.d(build_module_selectors_namespaceObject, "isRequestingEmbedPreview", function() { return isRequestingEmbedPreview; });
__webpack_require__.d(build_module_selectors_namespaceObject, "getAuthors", function() { return getAuthors; });
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

// NAMESPACE OBJECT: ./node_modules/@wordpress/core-data/build-module/resolvers.js
var resolvers_namespaceObject = {};
__webpack_require__.r(resolvers_namespaceObject);
__webpack_require__.d(resolvers_namespaceObject, "getAuthors", function() { return resolvers_getAuthors; });
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

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(5);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","dataControls"]}
var external_this_wp_dataControls_ = __webpack_require__(32);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__(17);

// EXTERNAL MODULE: external {"this":"lodash"}
var external_this_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","isShallowEqual"]}
var external_this_wp_isShallowEqual_ = __webpack_require__(64);
var external_this_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_isShallowEqual_);

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
    if (Object(external_this_lodash_["isEqual"])(item[key], nextItem[key])) {
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

// EXTERNAL MODULE: external {"this":"regeneratorRuntime"}
var external_this_regeneratorRuntime_ = __webpack_require__(20);
var external_this_regeneratorRuntime_default = /*#__PURE__*/__webpack_require__.n(external_this_regeneratorRuntime_);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__(31);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/queried-data/actions.js


function actions_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function actions_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { actions_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { actions_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */

/**
 * Returns an action object used in signalling that items have been received.
 *
 * @param {Array} items Items received.
 *
 * @return {Object} Action object.
 */

function receiveItems(items) {
  return {
    type: 'RECEIVE_ITEMS',
    items: Object(external_this_lodash_["castArray"])(items)
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
    itemIds: Object(external_this_lodash_["castArray"])(records),
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
 *
 * @return {Object} Action object.
 */

function receiveQueriedItems(items) {
  var query = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  return actions_objectSpread(actions_objectSpread({}, receiveItems(items)), {}, {
    query: query
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/actions.js




var _marked = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(deleteEntityRecord),
    _marked2 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(actions_editEntityRecord),
    _marked3 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(undo),
    _marked4 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(redo),
    _marked5 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(saveEntityRecord),
    _marked6 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(saveEditedEntityRecord);

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
    users: Object(external_this_lodash_["castArray"])(users),
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
 * @param {?boolean}     invalidateCache Should invalidate query caches
 *
 * @return {Object} Action object.
 */

function receiveEntityRecords(kind, name, records, query) {
  var invalidateCache = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : false;

  // Auto drafts should not have titles, but some plugins rely on them so we can't filter this
  // on the server.
  if (kind === 'postType') {
    records = Object(external_this_lodash_["castArray"])(records).map(function (record) {
      return record.status === 'auto-draft' ? build_module_actions_objectSpread(build_module_actions_objectSpread({}, record), {}, {
        title: ''
      }) : record;
    });
  }

  var action;

  if (query) {
    action = receiveQueriedItems(records, query);
  } else {
    action = receiveItems(records);
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
 * @param {string}  kind              Kind of the deleted entity.
 * @param {string}  name              Name of the deleted entity.
 * @param {string}  recordId          Record ID of the deleted entity.
 * @param {?Object} query             Special query parameters for the DELETE API call.
 */

function deleteEntityRecord(kind, name, recordId, query) {
  var entities, entity, error, deletedRecord, path;
  return external_this_regeneratorRuntime_default.a.wrap(function deleteEntityRecord$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.next = 2;
          return getKindEntities(kind);

        case 2:
          entities = _context.sent;
          entity = Object(external_this_lodash_["find"])(entities, {
            kind: kind,
            name: name
          });
          deletedRecord = false;

          if (entity) {
            _context.next = 7;
            break;
          }

          return _context.abrupt("return");

        case 7:
          _context.next = 9;
          return {
            type: 'DELETE_ENTITY_RECORD_START',
            kind: kind,
            name: name,
            recordId: recordId
          };

        case 9:
          _context.prev = 9;
          path = "".concat(entity.baseURL, "/").concat(recordId);

          if (query) {
            path = Object(external_this_wp_url_["addQueryArgs"])(path, query);
          }

          _context.next = 14;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: path,
            method: 'DELETE'
          });

        case 14:
          deletedRecord = _context.sent;
          _context.next = 17;
          return removeItems(kind, name, recordId, true);

        case 17:
          _context.next = 22;
          break;

        case 19:
          _context.prev = 19;
          _context.t0 = _context["catch"](9);
          error = _context.t0;

        case 22:
          _context.next = 24;
          return {
            type: 'DELETE_ENTITY_RECORD_FINISH',
            kind: kind,
            name: name,
            recordId: recordId,
            error: error
          };

        case 24:
          return _context.abrupt("return", deletedRecord);

        case 25:
        case "end":
          return _context.stop();
      }
    }
  }, _marked, null, [[9, 19]]);
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

  return external_this_regeneratorRuntime_default.a.wrap(function editEntityRecord$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          options = _args2.length > 4 && _args2[4] !== undefined ? _args2[4] : {};
          _context2.next = 3;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'getEntity', kind, name);

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
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'getRawEntityRecord', kind, name, recordId);

        case 9:
          record = _context2.sent;
          _context2.next = 12;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'getEditedEntityRecord', kind, name, recordId);

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
              acc[key] = Object(external_this_lodash_["isEqual"])(recordValue, value) ? undefined : value;
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
  }, _marked2);
}
/**
 * Action triggered to undo the last edit to
 * an entity record, if any.
 */

function undo() {
  var undoEdit;
  return external_this_regeneratorRuntime_default.a.wrap(function undo$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          _context3.next = 2;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'getUndoEdit');

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
  }, _marked3);
}
/**
 * Action triggered to redo the last undoed
 * edit to an entity record, if any.
 */

function redo() {
  var redoEdit;
  return external_this_regeneratorRuntime_default.a.wrap(function redo$(_context4) {
    while (1) {
      switch (_context4.prev = _context4.next) {
        case 0:
          _context4.next = 2;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'getRedoEdit');

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
  }, _marked4);
}
/**
 * Forces the creation of a new undo level.
 *
 * @return {Object} Action object.
 */

function __unstableCreateUndoLevel() {
  return {
    type: 'CREATE_UNDO_LEVEL'
  };
}
/**
 * Action triggered to save an entity record.
 *
 * @param {string}  kind                       Kind of the received entity.
 * @param {string}  name                       Name of the received entity.
 * @param {Object}  record                     Record to be saved.
 * @param {Object}  options                    Saving options.
 * @param {boolean} [options.isAutosave=false] Whether this is an autosave.
 */

function saveEntityRecord(kind, name, record) {
  var _ref,
      _ref$isAutosave,
      isAutosave,
      entities,
      entity,
      entityIdKey,
      recordId,
      _i,
      _Object$entries,
      _Object$entries$_i,
      key,
      value,
      evaluatedValue,
      updatedRecord,
      error,
      persistedEntity,
      currentEdits,
      path,
      persistedRecord,
      currentUser,
      currentUserId,
      autosavePost,
      data,
      newRecord,
      _data,
      _args5 = arguments;

  return external_this_regeneratorRuntime_default.a.wrap(function saveEntityRecord$(_context5) {
    while (1) {
      switch (_context5.prev = _context5.next) {
        case 0:
          _ref = _args5.length > 3 && _args5[3] !== undefined ? _args5[3] : {
            isAutosave: false
          }, _ref$isAutosave = _ref.isAutosave, isAutosave = _ref$isAutosave === void 0 ? false : _ref$isAutosave;
          _context5.next = 3;
          return getKindEntities(kind);

        case 3:
          entities = _context5.sent;
          entity = Object(external_this_lodash_["find"])(entities, {
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
          recordId = record[entityIdKey]; // Evaluate optimized edits.
          // (Function edits that should be evaluated on save to avoid expensive computations on every edit.)

          _i = 0, _Object$entries = Object.entries(record);

        case 10:
          if (!(_i < _Object$entries.length)) {
            _context5.next = 24;
            break;
          }

          _Object$entries$_i = Object(slicedToArray["a" /* default */])(_Object$entries[_i], 2), key = _Object$entries$_i[0], value = _Object$entries$_i[1];

          if (!(typeof value === 'function')) {
            _context5.next = 21;
            break;
          }

          _context5.t0 = value;
          _context5.next = 16;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'getEditedEntityRecord', kind, name, recordId);

        case 16:
          _context5.t1 = _context5.sent;
          evaluatedValue = (0, _context5.t0)(_context5.t1);
          _context5.next = 20;
          return actions_editEntityRecord(kind, name, recordId, Object(defineProperty["a" /* default */])({}, key, evaluatedValue), {
            undoIgnore: true
          });

        case 20:
          record[key] = evaluatedValue;

        case 21:
          _i++;
          _context5.next = 10;
          break;

        case 24:
          _context5.next = 26;
          return {
            type: 'SAVE_ENTITY_RECORD_START',
            kind: kind,
            name: name,
            recordId: recordId,
            isAutosave: isAutosave
          };

        case 26:
          _context5.prev = 26;
          path = "".concat(entity.baseURL).concat(recordId ? '/' + recordId : '');
          _context5.next = 30;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'getRawEntityRecord', kind, name, recordId);

        case 30:
          persistedRecord = _context5.sent;

          if (!isAutosave) {
            _context5.next = 55;
            break;
          }

          _context5.next = 34;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'getCurrentUser');

        case 34:
          currentUser = _context5.sent;
          currentUserId = currentUser ? currentUser.id : undefined;
          _context5.next = 38;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'getAutosave', persistedRecord.type, persistedRecord.id, currentUserId);

        case 38:
          autosavePost = _context5.sent;
          // Autosaves need all expected fields to be present.
          // So we fallback to the previous autosave and then
          // to the actual persisted entity if the edits don't
          // have a value.
          data = build_module_actions_objectSpread(build_module_actions_objectSpread(build_module_actions_objectSpread({}, persistedRecord), autosavePost), record);
          data = Object.keys(data).reduce(function (acc, key) {
            if (['title', 'excerpt', 'content'].includes(key)) {
              // Edits should be the "raw" attribute values.
              acc[key] = Object(external_this_lodash_["get"])(data[key], 'raw', data[key]);
            }

            return acc;
          }, {
            status: data.status === 'auto-draft' ? 'draft' : data.status
          });
          _context5.next = 43;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: "".concat(path, "/autosaves"),
            method: 'POST',
            data: data
          });

        case 43:
          updatedRecord = _context5.sent;

          if (!(persistedRecord.id === updatedRecord.id)) {
            _context5.next = 51;
            break;
          }

          newRecord = build_module_actions_objectSpread(build_module_actions_objectSpread(build_module_actions_objectSpread({}, persistedRecord), data), updatedRecord);
          newRecord = Object.keys(newRecord).reduce(function (acc, key) {
            // These properties are persisted in autosaves.
            if (['title', 'excerpt', 'content'].includes(key)) {
              // Edits should be the "raw" attribute values.
              acc[key] = Object(external_this_lodash_["get"])(newRecord[key], 'raw', newRecord[key]);
            } else if (key === 'status') {
              // Status is only persisted in autosaves when going from
              // "auto-draft" to "draft".
              acc[key] = persistedRecord.status === 'auto-draft' && newRecord.status === 'draft' ? newRecord.status : persistedRecord.status;
            } else {
              // These properties are not persisted in autosaves.
              acc[key] = Object(external_this_lodash_["get"])(persistedRecord[key], 'raw', persistedRecord[key]);
            }

            return acc;
          }, {});
          _context5.next = 49;
          return receiveEntityRecords(kind, name, newRecord, undefined, true);

        case 49:
          _context5.next = 53;
          break;

        case 51:
          _context5.next = 53;
          return receiveAutosaves(persistedRecord.id, updatedRecord);

        case 53:
          _context5.next = 70;
          break;

        case 55:
          // Auto drafts should be converted to drafts on explicit saves and we should not respect their default title,
          // but some plugins break with this behavior so we can't filter it on the server.
          _data = record;

          if (kind === 'postType' && persistedRecord && persistedRecord.status === 'auto-draft') {
            if (!_data.status) {
              _data = build_module_actions_objectSpread(build_module_actions_objectSpread({}, _data), {}, {
                status: 'draft'
              });
            }

            if (!_data.title || _data.title === 'Auto Draft') {
              _data = build_module_actions_objectSpread(build_module_actions_objectSpread({}, _data), {}, {
                title: ''
              });
            }
          } // Get the full local version of the record before the update,
          // to merge it with the edits and then propagate it to subscribers


          _context5.next = 59;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', '__experimentalGetEntityRecordNoResolver', kind, name, recordId);

        case 59:
          persistedEntity = _context5.sent;
          _context5.next = 62;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'getEntityRecordEdits', kind, name, recordId);

        case 62:
          currentEdits = _context5.sent;
          _context5.next = 65;
          return receiveEntityRecords(kind, name, build_module_actions_objectSpread(build_module_actions_objectSpread({}, persistedEntity), _data), undefined, true);

        case 65:
          _context5.next = 67;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: path,
            method: recordId ? 'PUT' : 'POST',
            data: _data
          });

        case 67:
          updatedRecord = _context5.sent;
          _context5.next = 70;
          return receiveEntityRecords(kind, name, updatedRecord, undefined, true);

        case 70:
          _context5.next = 91;
          break;

        case 72:
          _context5.prev = 72;
          _context5.t2 = _context5["catch"](26);
          error = _context5.t2; // If we got to the point in the try block where we made an optimistic update,
          // we need to roll it back here.

          if (!(persistedEntity && currentEdits)) {
            _context5.next = 91;
            break;
          }

          _context5.next = 78;
          return receiveEntityRecords(kind, name, persistedEntity, undefined, true);

        case 78:
          _context5.t3 = actions_editEntityRecord;
          _context5.t4 = kind;
          _context5.t5 = name;
          _context5.t6 = recordId;
          _context5.t7 = build_module_actions_objectSpread;
          _context5.t8 = build_module_actions_objectSpread({}, currentEdits);
          _context5.next = 86;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'getEntityRecordEdits', kind, name, recordId);

        case 86:
          _context5.t9 = _context5.sent;
          _context5.t10 = (0, _context5.t7)(_context5.t8, _context5.t9);
          _context5.t11 = {
            undoIgnore: true
          };
          _context5.next = 91;
          return (0, _context5.t3)(_context5.t4, _context5.t5, _context5.t6, _context5.t10, _context5.t11);

        case 91:
          _context5.next = 93;
          return {
            type: 'SAVE_ENTITY_RECORD_FINISH',
            kind: kind,
            name: name,
            recordId: recordId,
            error: error,
            isAutosave: isAutosave
          };

        case 93:
          return _context5.abrupt("return", updatedRecord);

        case 94:
        case "end":
          return _context5.stop();
      }
    }
  }, _marked5, null, [[26, 72]]);
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
  return external_this_regeneratorRuntime_default.a.wrap(function saveEditedEntityRecord$(_context6) {
    while (1) {
      switch (_context6.prev = _context6.next) {
        case 0:
          _context6.next = 2;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'hasEditsForEntityRecord', kind, name, recordId);

        case 2:
          if (_context6.sent) {
            _context6.next = 4;
            break;
          }

          return _context6.abrupt("return");

        case 4:
          _context6.next = 6;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'getEntityRecordNonTransientEdits', kind, name, recordId);

        case 6:
          edits = _context6.sent;
          record = build_module_actions_objectSpread({
            id: recordId
          }, edits);
          return _context6.delegateYield(saveEntityRecord(kind, name, record, options), "t0", 9);

        case 9:
        case "end":
          return _context6.stop();
      }
    }
  }, _marked6);
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
    autosaves: Object(external_this_lodash_["castArray"])(autosaves)
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/entities.js


var entities_marked = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(loadPostTypeEntities),
    entities_marked2 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(loadTaxonomyEntities),
    entities_marked3 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(getKindEntities);

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
  label: Object(external_this_wp_i18n_["__"])('Base'),
  name: '__unstableBase',
  kind: 'root',
  baseURL: ''
}, {
  label: Object(external_this_wp_i18n_["__"])('Site'),
  name: 'site',
  kind: 'root',
  baseURL: '/wp/v2/settings',
  getTitle: function getTitle(record) {
    return Object(external_this_lodash_["get"])(record, ['title'], Object(external_this_wp_i18n_["__"])('Site Title'));
  }
}, {
  label: Object(external_this_wp_i18n_["__"])('Post Type'),
  name: 'postType',
  kind: 'root',
  key: 'slug',
  baseURL: '/wp/v2/types'
}, {
  name: 'media',
  kind: 'root',
  baseURL: '/wp/v2/media',
  plural: 'mediaItems',
  label: Object(external_this_wp_i18n_["__"])('Media')
}, {
  name: 'taxonomy',
  kind: 'root',
  key: 'slug',
  baseURL: '/wp/v2/taxonomies',
  plural: 'taxonomies',
  label: Object(external_this_wp_i18n_["__"])('Taxonomy')
}, {
  name: 'sidebar',
  kind: 'root',
  baseURL: '/wp/v2/sidebars',
  plural: 'sidebars',
  transientEdits: {
    blocks: true
  },
  label: Object(external_this_wp_i18n_["__"])('Widget areas')
}, {
  name: 'widget',
  kind: 'root',
  baseURL: '/wp/v2/widgets',
  plural: 'widgets',
  transientEdits: {
    blocks: true
  },
  label: Object(external_this_wp_i18n_["__"])('Widgets')
}, {
  label: Object(external_this_wp_i18n_["__"])('User'),
  name: 'user',
  kind: 'root',
  baseURL: '/wp/v2/users',
  plural: 'users'
}, {
  name: 'comment',
  kind: 'root',
  baseURL: '/wp/v2/comments',
  plural: 'comments',
  label: Object(external_this_wp_i18n_["__"])('Comment')
}, {
  name: 'menu',
  kind: 'root',
  baseURL: '/__experimental/menus',
  plural: 'menus',
  label: Object(external_this_wp_i18n_["__"])('Menu')
}, {
  name: 'menuItem',
  kind: 'root',
  baseURL: '/__experimental/menu-items',
  plural: 'menuItems',
  label: Object(external_this_wp_i18n_["__"])('Menu Item')
}, {
  name: 'menuLocation',
  kind: 'root',
  baseURL: '/__experimental/menu-locations',
  plural: 'menuLocations',
  label: Object(external_this_wp_i18n_["__"])('Menu Location'),
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
 * Returns the list of post type entities.
 *
 * @return {Promise} Entities promise
 */

function loadPostTypeEntities() {
  var postTypes;
  return external_this_regeneratorRuntime_default.a.wrap(function loadPostTypeEntities$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.next = 2;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: '/wp/v2/types?context=edit'
          });

        case 2:
          postTypes = _context.sent;
          return _context.abrupt("return", Object(external_this_lodash_["map"])(postTypes, function (postType, name) {
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
                if (name === 'wp_template_part' || name === 'wp_template') {
                  return Object(external_this_lodash_["startCase"])(record.slug);
                }

                return Object(external_this_lodash_["get"])(record, ['title', 'rendered'], record.id);
              }
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
  return external_this_regeneratorRuntime_default.a.wrap(function loadTaxonomyEntities$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          _context2.next = 2;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: '/wp/v2/taxonomies?context=edit'
          });

        case 2:
          taxonomies = _context2.sent;
          return _context2.abrupt("return", Object(external_this_lodash_["map"])(taxonomies, function (taxonomy, name) {
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
  var entity = Object(external_this_lodash_["find"])(defaultEntities, {
    kind: kind,
    name: name
  });
  var kindPrefix = kind === 'root' ? '' : Object(external_this_lodash_["upperFirst"])(Object(external_this_lodash_["camelCase"])(kind));
  var nameSuffix = Object(external_this_lodash_["upperFirst"])(Object(external_this_lodash_["camelCase"])(name)) + (usePlural ? 's' : '');
  var suffix = usePlural && entity.plural ? Object(external_this_lodash_["upperFirst"])(Object(external_this_lodash_["camelCase"])(entity.plural)) : nameSuffix;
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
  return external_this_regeneratorRuntime_default.a.wrap(function getKindEntities$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          _context3.next = 2;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'getEntitiesByKind', kind);

        case 2:
          entities = _context3.sent;

          if (!(entities && entities.length !== 0)) {
            _context3.next = 5;
            break;
          }

          return _context3.abrupt("return", entities);

        case 5:
          kindConfig = Object(external_this_lodash_["find"])(kinds, {
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

      if (Object(external_this_lodash_["isObjectLike"])(key)) {
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
          parts.fields = get_normalized_comma_separable(value);
        } // While it could be any deterministic string, for simplicity's
        // sake mimic querystring encoding for stable key.
        //
        // TODO: For consistency with PHP implementation, addQueryArgs
        // should accept a key value pair, which may optimize its
        // implementation for our use here, vs. iterating an object
        // with only a single key.


        parts.stableKey += (parts.stableKey ? '&' : '') + Object(external_this_wp_url_["addQueryArgs"])('', Object(defineProperty["a" /* default */])({}, key, value)).slice(1);
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
      var newState = Object(external_this_lodash_["omit"])(state, action.itemIds);
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

var receiveQueries = Object(external_this_lodash_["flowRight"])([// Limit to matching action type so we don't attempt to replace action on
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

  return getMergedItemIds(state || [], Object(external_this_lodash_["map"])(action.items, key), page, perPage);
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
      Object(external_this_lodash_["forEach"])(newState, function (queryItems, key) {
        newState[key] = Object(external_this_lodash_["filter"])(queryItems, function (queryId) {
          return !removedItems[queryId];
        });
      });
      return newState;

    default:
      return state;
  }
};

/* harmony default export */ var queried_data_reducer = (Object(external_this_wp_data_["combineReducers"])({
  items: reducer_items,
  itemIsComplete: itemIsComplete,
  queries: reducer_queries
}));

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/reducer.js




function _createForOfIteratorHelper(o, allowArrayLike) { var it; if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

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
        byId: build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state.byId), Object(external_this_lodash_["keyBy"])(action.users, 'id')),
        queries: build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, state.queries), {}, Object(defineProperty["a" /* default */])({}, action.queryID, Object(external_this_lodash_["map"])(action.users, function (user) {
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
  return Object(external_this_lodash_["flowRight"])([// Limit to matching action type so we don't attempt to replace action on
  // an unhandled action.
  if_matching_action(function (action) {
    return action.name && action.kind && action.name === entityConfig.name && action.kind === entityConfig.kind;
  }), // Inject the entity config into the action.
  replace_action(function (action) {
    return build_module_reducer_objectSpread(build_module_reducer_objectSpread({}, action), {}, {
      key: entityConfig.key || DEFAULT_ENTITY_KEY
    });
  })])(Object(external_this_wp_data_["combineReducers"])({
    queriedData: queried_data_reducer,
    edits: function edits() {
      var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var action = arguments.length > 1 ? arguments[1] : undefined;

      switch (action.type) {
        case 'RECEIVE_ITEMS':
          var nextState = build_module_reducer_objectSpread({}, state);

          var _iterator = _createForOfIteratorHelper(action.items),
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
                !Object(external_this_lodash_["isEqual"])(edits[key], Object(external_this_lodash_["get"])(record[key], 'raw', record[key]))) {
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
    var entitiesByKind = Object(external_this_lodash_["groupBy"])(newConfig, 'kind');
    entitiesDataReducer = Object(external_this_wp_data_["combineReducers"])(Object.entries(entitiesByKind).reduce(function (memo, _ref) {
      var _ref2 = Object(slicedToArray["a" /* default */])(_ref, 2),
          kind = _ref2[0],
          subEntities = _ref2[1];

      var kindReducer = Object(external_this_wp_data_["combineReducers"])(subEntities.reduce(function (kindMemo, entityConfig) {
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

      if (!external_this_wp_isShallowEqual_default()(comparisonUndoEdits, comparisonEdits)) {
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
/* harmony default export */ var build_module_reducer = (Object(external_this_wp_data_["combineReducers"])({
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
  autosaves: reducer_autosaves
}));

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__(42);

// EXTERNAL MODULE: external {"this":["wp","deprecated"]}
var external_this_wp_deprecated_ = __webpack_require__(36);
var external_this_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_deprecated_);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/name.js
/**
 * The reducer key used by core data in store registration.
 * This is defined in a separate file to avoid cycle-dependency
 *
 * @type {string}
 */
var REDUCER_KEY = 'core';

// EXTERNAL MODULE: ./node_modules/equivalent-key-map/equivalent-key-map.js
var equivalent_key_map = __webpack_require__(106);
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
        var value = Object(external_this_lodash_["get"])(item, field);
        Object(external_this_lodash_["set"])(filteredItem, field, value);
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
 * Returns true if a request is in progress for embed preview data, or false
 * otherwise.
 *
 * @param {Object} state Data state.
 * @param {string} url   URL the preview would be for.
 *
 * @return {boolean} Whether a request is in progress for an embed preview.
 */

var isRequestingEmbedPreview = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function (state, url) {
    return select('core/data').isResolving(REDUCER_KEY, 'getEmbedPreview', [url]);
  };
});
/**
 * Returns all available authors.
 *
 * @param {Object} state Data state.
 *
 * @return {Array} Authors list.
 */

function getAuthors(state) {
  external_this_wp_deprecated_default()("select( 'core' ).getAuthors()", {
    alternative: "select( 'core' ).getUsers({ who: 'authors' })"
  });
  return getUserQueryResults(state, 'authors');
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
  return Object(external_this_lodash_["map"])(queryResults, function (id) {
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
  return Object(external_this_lodash_["filter"])(state.entities.config, {
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
  return Object(external_this_lodash_["find"])(state.entities.config, {
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
  var queriedState = Object(external_this_lodash_["get"])(state.entities.data, [kind, name, 'queriedData']);

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
      var value = Object(external_this_lodash_["get"])(item, field);
      Object(external_this_lodash_["set"])(filteredItem, field, value);
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
    accumulator[_key] = Object(external_this_lodash_["get"])(record[_key], 'raw', record[_key]);
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
  var queriedState = Object(external_this_lodash_["get"])(state.entities.data, [kind, name, 'queriedData']);

  if (!queriedState) {
    return [];
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
          var entityRecord = getEditedEntityRecord(state, kind, name, primaryKey);
          dirtyRecords.push({
            // We avoid using primaryKey because it's transformed into a string
            // when it's used as an object key.
            key: entityRecord[entity.key || DEFAULT_ENTITY_KEY],
            title: !entity.getTitle ? '' : entity.getTitle(entityRecord),
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
  return Object(external_this_lodash_["get"])(state.entities.data, [kind, name, 'edits', recordId]);
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
  var _get = Object(external_this_lodash_["get"])(state.entities.data, [kind, name, 'saving', recordId], {}),
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
  return Object(external_this_lodash_["get"])(state.entities.data, [kind, name, 'saving', recordId, 'pending'], false);
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
  return Object(external_this_lodash_["get"])(state.entities.data, [kind, name, 'deleting', recordId, 'pending'], false);
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
  return Object(external_this_lodash_["get"])(state.entities.data, [kind, name, 'saving', recordId, 'error']);
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
  return Object(external_this_lodash_["get"])(state.entities.data, [kind, name, 'deleting', recordId, 'error']);
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
  external_this_wp_deprecated_default()("select( 'core' ).hasUploadPermissions()", {
    alternative: "select( 'core' ).canUser( 'create', 'media' )"
  });
  return Object(external_this_lodash_["defaultTo"])(canUser(state, 'create', 'media'), true);
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
  var key = Object(external_this_lodash_["compact"])([action, resource, id]).join('/');
  return Object(external_this_lodash_["get"])(state, ['userPermissions', key]);
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
  return Object(external_this_lodash_["find"])(autosaves, {
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

var hasFetchedAutosaves = Object(external_this_wp_data_["createRegistrySelector"])(function (select) {
  return function (state, postType, postId) {
    return select(REDUCER_KEY).hasFinishedResolution('getAutosaves', [postType, postId]);
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
    external_this_regeneratorRuntime_default.a.mark(function resolveIfNotResolved() {
      var _len,
          args,
          _key,
          hasStartedResolution,
          _args = arguments;

      return external_this_regeneratorRuntime_default.a.wrap(function resolveIfNotResolved$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              for (_len = _args.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
                args[_key] = _args[_key];
              }

              _context.next = 3;
              return Object(external_this_wp_dataControls_["select"])('core', 'hasStartedResolution', selectorName, args);

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




function resolvers_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function resolvers_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { resolvers_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { resolvers_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var resolvers_marked = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(resolvers_getAuthors),
    resolvers_marked2 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(resolvers_getCurrentUser),
    resolvers_marked3 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(resolvers_getEntityRecord),
    resolvers_marked4 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(resolvers_getEntityRecords),
    resolvers_marked5 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(resolvers_getCurrentTheme),
    resolvers_marked6 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(resolvers_getThemeSupports),
    _marked7 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(resolvers_getEmbedPreview),
    _marked8 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(resolvers_hasUploadPermissions),
    _marked9 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(resolvers_canUser),
    _marked10 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(resolvers_getAutosaves),
    _marked11 = /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(resolvers_getAutosave);

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
 * Requests authors from the REST API.
 */

function resolvers_getAuthors() {
  var users;
  return external_this_regeneratorRuntime_default.a.wrap(function getAuthors$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.next = 2;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: '/wp/v2/users/?who=authors&per_page=-1'
          });

        case 2:
          users = _context.sent;
          _context.next = 5;
          return receiveUserQuery('authors', users);

        case 5:
        case "end":
          return _context.stop();
      }
    }
  }, resolvers_marked);
}
/**
 * Requests the current user from the REST API.
 */

function resolvers_getCurrentUser() {
  var currentUser;
  return external_this_regeneratorRuntime_default.a.wrap(function getCurrentUser$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          _context2.next = 2;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: '/wp/v2/users/me'
          });

        case 2:
          currentUser = _context2.sent;
          _context2.next = 5;
          return receiveCurrentUser(currentUser);

        case 5:
        case "end":
          return _context2.stop();
      }
    }
  }, resolvers_marked2);
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
      path,
      hasRecords,
      record,
      _args3 = arguments;
  return external_this_regeneratorRuntime_default.a.wrap(function getEntityRecord$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          key = _args3.length > 2 && _args3[2] !== undefined ? _args3[2] : '';
          query = _args3.length > 3 ? _args3[3] : undefined;
          _context3.next = 4;
          return getKindEntities(kind);

        case 4:
          entities = _context3.sent;
          entity = Object(external_this_lodash_["find"])(entities, {
            kind: kind,
            name: name
          });

          if (entity) {
            _context3.next = 8;
            break;
          }

          return _context3.abrupt("return");

        case 8:
          if (query !== undefined && query._fields) {
            // If requesting specific fields, items and query assocation to said
            // records are stored by ID reference. Thus, fields must always include
            // the ID.
            query = resolvers_objectSpread(resolvers_objectSpread({}, query), {}, {
              _fields: Object(external_this_lodash_["uniq"])([].concat(Object(toConsumableArray["a" /* default */])(get_normalized_comma_separable(query._fields) || []), [entity.key || DEFAULT_ENTITY_KEY])).join()
            });
          } // Disable reason: While true that an early return could leave `path`
          // unused, it's important that path is derived using the query prior to
          // additional query modifications in the condition below, since those
          // modifications are relevant to how the data is tracked in state, and not
          // for how the request is made to the REST API.
          // eslint-disable-next-line @wordpress/no-unused-vars-before-return


          path = Object(external_this_wp_url_["addQueryArgs"])(entity.baseURL + '/' + key, resolvers_objectSpread(resolvers_objectSpread({}, query), {}, {
            context: 'edit'
          }));

          if (!(query !== undefined)) {
            _context3.next = 17;
            break;
          }

          query = resolvers_objectSpread(resolvers_objectSpread({}, query), {}, {
            include: [key]
          }); // The resolution cache won't consider query as reusable based on the
          // fields, so it's tested here, prior to initiating the REST request,
          // and without causing `getEntityRecords` resolution to occur.

          _context3.next = 14;
          return Object(external_this_wp_dataControls_["syncSelect"])('core', 'hasEntityRecords', kind, name, query);

        case 14:
          hasRecords = _context3.sent;

          if (!hasRecords) {
            _context3.next = 17;
            break;
          }

          return _context3.abrupt("return");

        case 17:
          _context3.next = 19;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: path
          });

        case 19:
          record = _context3.sent;
          _context3.next = 22;
          return receiveEntityRecords(kind, name, record, query);

        case 22:
        case "end":
          return _context3.stop();
      }
    }
  }, resolvers_marked3);
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
      path,
      records,
      _args4 = arguments;
  return external_this_regeneratorRuntime_default.a.wrap(function getEntityRecords$(_context4) {
    while (1) {
      switch (_context4.prev = _context4.next) {
        case 0:
          query = _args4.length > 2 && _args4[2] !== undefined ? _args4[2] : {};
          _context4.next = 3;
          return getKindEntities(kind);

        case 3:
          entities = _context4.sent;
          entity = Object(external_this_lodash_["find"])(entities, {
            kind: kind,
            name: name
          });

          if (entity) {
            _context4.next = 7;
            break;
          }

          return _context4.abrupt("return");

        case 7:
          if (query._fields) {
            // If requesting specific fields, items and query assocation to said
            // records are stored by ID reference. Thus, fields must always include
            // the ID.
            query = resolvers_objectSpread(resolvers_objectSpread({}, query), {}, {
              _fields: Object(external_this_lodash_["uniq"])([].concat(Object(toConsumableArray["a" /* default */])(get_normalized_comma_separable(query._fields) || []), [entity.key || DEFAULT_ENTITY_KEY])).join()
            });
          }

          path = Object(external_this_wp_url_["addQueryArgs"])(entity.baseURL, resolvers_objectSpread(resolvers_objectSpread({}, query), {}, {
            context: 'edit'
          }));
          _context4.t0 = Object;
          _context4.next = 12;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: path
          });

        case 12:
          _context4.t1 = _context4.sent;
          records = _context4.t0.values.call(_context4.t0, _context4.t1);

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

          _context4.next = 17;
          return receiveEntityRecords(kind, name, records, query);

        case 17:
        case "end":
          return _context4.stop();
      }
    }
  }, resolvers_marked4);
}

resolvers_getEntityRecords.shouldInvalidate = function (action, kind, name) {
  return (action.type === 'RECEIVE_ITEMS' || action.type === 'REMOVE_ITEMS') && action.invalidateCache && kind === action.kind && name === action.name;
};
/**
 * Requests the current theme.
 */


function resolvers_getCurrentTheme() {
  var activeThemes;
  return external_this_regeneratorRuntime_default.a.wrap(function getCurrentTheme$(_context5) {
    while (1) {
      switch (_context5.prev = _context5.next) {
        case 0:
          _context5.next = 2;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: '/wp/v2/themes?status=active'
          });

        case 2:
          activeThemes = _context5.sent;
          _context5.next = 5;
          return receiveCurrentTheme(activeThemes[0]);

        case 5:
        case "end":
          return _context5.stop();
      }
    }
  }, resolvers_marked5);
}
/**
 * Requests theme supports data from the index.
 */

function resolvers_getThemeSupports() {
  var activeThemes;
  return external_this_regeneratorRuntime_default.a.wrap(function getThemeSupports$(_context6) {
    while (1) {
      switch (_context6.prev = _context6.next) {
        case 0:
          _context6.next = 2;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: '/wp/v2/themes?status=active'
          });

        case 2:
          activeThemes = _context6.sent;
          _context6.next = 5;
          return receiveThemeSupports(activeThemes[0].theme_supports);

        case 5:
        case "end":
          return _context6.stop();
      }
    }
  }, resolvers_marked6);
}
/**
 * Requests a preview from the from the Embed API.
 *
 * @param {string} url   URL to get the preview for.
 */

function resolvers_getEmbedPreview(url) {
  var embedProxyResponse;
  return external_this_regeneratorRuntime_default.a.wrap(function getEmbedPreview$(_context7) {
    while (1) {
      switch (_context7.prev = _context7.next) {
        case 0:
          _context7.prev = 0;
          _context7.next = 3;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: Object(external_this_wp_url_["addQueryArgs"])('/oembed/1.0/proxy', {
              url: url
            })
          });

        case 3:
          embedProxyResponse = _context7.sent;
          _context7.next = 6;
          return receiveEmbedPreview(url, embedProxyResponse);

        case 6:
          _context7.next = 12;
          break;

        case 8:
          _context7.prev = 8;
          _context7.t0 = _context7["catch"](0);
          _context7.next = 12;
          return receiveEmbedPreview(url, false);

        case 12:
        case "end":
          return _context7.stop();
      }
    }
  }, _marked7, null, [[0, 8]]);
}
/**
 * Requests Upload Permissions from the REST API.
 *
 * @deprecated since 5.0. Callers should use the more generic `canUser()` selector instead of
 *            `hasUploadPermissions()`, e.g. `canUser( 'create', 'media' )`.
 */

function resolvers_hasUploadPermissions() {
  return external_this_regeneratorRuntime_default.a.wrap(function hasUploadPermissions$(_context8) {
    while (1) {
      switch (_context8.prev = _context8.next) {
        case 0:
          external_this_wp_deprecated_default()("select( 'core' ).hasUploadPermissions()", {
            alternative: "select( 'core' ).canUser( 'create', 'media' )"
          });
          return _context8.delegateYield(resolvers_canUser('create', 'media'), "t0", 2);

        case 2:
        case "end":
          return _context8.stop();
      }
    }
  }, _marked8);
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
  return external_this_regeneratorRuntime_default.a.wrap(function canUser$(_context9) {
    while (1) {
      switch (_context9.prev = _context9.next) {
        case 0:
          methods = {
            create: 'POST',
            read: 'GET',
            update: 'PUT',
            delete: 'DELETE'
          };
          method = methods[action];

          if (method) {
            _context9.next = 4;
            break;
          }

          throw new Error("'".concat(action, "' is not a valid action."));

        case 4:
          path = id ? "/wp/v2/".concat(resource, "/").concat(id) : "/wp/v2/".concat(resource);
          _context9.prev = 5;
          _context9.next = 8;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: path,
            // Ideally this would always be an OPTIONS request, but unfortunately there's
            // a bug in the REST API which causes the Allow header to not be sent on
            // OPTIONS requests to /posts/:id routes.
            // https://core.trac.wordpress.org/ticket/45753
            method: id ? 'GET' : 'OPTIONS',
            parse: false
          });

        case 8:
          response = _context9.sent;
          _context9.next = 14;
          break;

        case 11:
          _context9.prev = 11;
          _context9.t0 = _context9["catch"](5);
          return _context9.abrupt("return");

        case 14:
          if (Object(external_this_lodash_["hasIn"])(response, ['headers', 'get'])) {
            // If the request is fetched using the fetch api, the header can be
            // retrieved using the 'get' method.
            allowHeader = response.headers.get('allow');
          } else {
            // If the request was preloaded server-side and is returned by the
            // preloading middleware, the header will be a simple property.
            allowHeader = Object(external_this_lodash_["get"])(response, ['headers', 'Allow'], '');
          }

          key = Object(external_this_lodash_["compact"])([action, resource, id]).join('/');
          isAllowed = Object(external_this_lodash_["includes"])(allowHeader, method);
          _context9.next = 19;
          return receiveUserPermission(key, isAllowed);

        case 19:
        case "end":
          return _context9.stop();
      }
    }
  }, _marked9, null, [[5, 11]]);
}
/**
 * Request autosave data from the REST API.
 *
 * @param {string} postType The type of the parent post.
 * @param {number} postId   The id of the parent post.
 */

function resolvers_getAutosaves(postType, postId) {
  var _yield$select, restBase, autosaves;

  return external_this_regeneratorRuntime_default.a.wrap(function getAutosaves$(_context10) {
    while (1) {
      switch (_context10.prev = _context10.next) {
        case 0:
          _context10.next = 2;
          return Object(external_this_wp_dataControls_["select"])('core', 'getPostType', postType);

        case 2:
          _yield$select = _context10.sent;
          restBase = _yield$select.rest_base;
          _context10.next = 6;
          return Object(external_this_wp_dataControls_["apiFetch"])({
            path: "/wp/v2/".concat(restBase, "/").concat(postId, "/autosaves?context=edit")
          });

        case 6:
          autosaves = _context10.sent;

          if (!(autosaves && autosaves.length)) {
            _context10.next = 10;
            break;
          }

          _context10.next = 10;
          return receiveAutosaves(postId, autosaves);

        case 10:
        case "end":
          return _context10.stop();
      }
    }
  }, _marked10);
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
  return external_this_regeneratorRuntime_default.a.wrap(function getAutosave$(_context11) {
    while (1) {
      switch (_context11.prev = _context11.next) {
        case 0:
          _context11.next = 2;
          return Object(external_this_wp_dataControls_["select"])('core', 'getAutosaves', postType, postId);

        case 2:
        case "end":
          return _context11.stop();
      }
    }
  }, _marked11);
}

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(11);

// CONCATENATED MODULE: ./node_modules/@wordpress/core-data/build-module/entity-provider.js




function entity_provider_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function entity_provider_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { entity_provider_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { entity_provider_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



var entity_provider_entities = entity_provider_objectSpread(entity_provider_objectSpread({}, defaultEntities.reduce(function (acc, entity) {
  if (!acc[entity.kind]) {
    acc[entity.kind] = {};
  }

  acc[entity.kind][entity.name] = {
    context: Object(external_this_wp_element_["createContext"])()
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
      context: Object(external_this_wp_element_["createContext"])()
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
  return Object(external_this_wp_element_["createElement"])(Provider, {
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
  return Object(external_this_wp_element_["useContext"])(entity_provider_getEntity(kind, type).context);
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

  var _useSelect = Object(external_this_wp_data_["useSelect"])(function (select) {
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

  var _useDispatch = Object(external_this_wp_data_["useDispatch"])('core'),
      editEntityRecord = _useDispatch.editEntityRecord;

  var setValue = Object(external_this_wp_element_["useCallback"])(function (newValue) {
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
 * @param {Object} [options.initialEdits]          Initial edits object for the entity record.
 * @param {string} [options.blocksProp='blocks']   The name of the entity prop that holds the blocks array.
 * @param {string} [options.contentProp='content'] The name of the entity prop that holds the serialized blocks.
 * @param {string} [options.id]                    An entity ID to use instead of the context-provided one.
 *
 * @return {[WPBlock[], Function, Function]} The block array and setters.
 */

function useEntityBlockEditor(kind, type) {
  var _ref2 = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {},
      initialEdits = _ref2.initialEdits,
      _ref2$blocksProp = _ref2.blocksProp,
      blocksProp = _ref2$blocksProp === void 0 ? 'blocks' : _ref2$blocksProp,
      _ref2$contentProp = _ref2.contentProp,
      contentProp = _ref2$contentProp === void 0 ? 'content' : _ref2$contentProp,
      _id = _ref2.id;

  var providerId = useEntityId(kind, type);
  var id = _id !== null && _id !== void 0 ? _id : providerId;

  var _useEntityProp = useEntityProp(kind, type, contentProp, id),
      _useEntityProp2 = Object(slicedToArray["a" /* default */])(_useEntityProp, 2),
      content = _useEntityProp2[0],
      setContent = _useEntityProp2[1];

  var _useDispatch2 = Object(external_this_wp_data_["useDispatch"])('core'),
      editEntityRecord = _useDispatch2.editEntityRecord;

  Object(external_this_wp_element_["useEffect"])(function () {
    if (initialEdits) {
      editEntityRecord(kind, type, id, initialEdits, {
        undoIgnore: true
      });
    }
  }, [id]);
  var initialBlocks = Object(external_this_wp_element_["useMemo"])(function () {
    // Guard against other instances that might have
    // set content to a function already.
    if (content && typeof content !== 'function') {
      var parsedContent = Object(external_this_wp_blocks_["parse"])(content);
      return parsedContent.length ? parsedContent : [];
    }

    return [];
  }, [content]);

  var _useEntityProp3 = useEntityProp(kind, type, blocksProp, id),
      _useEntityProp4 = Object(slicedToArray["a" /* default */])(_useEntityProp3, 2),
      _useEntityProp4$ = _useEntityProp4[0],
      blocks = _useEntityProp4$ === void 0 ? initialBlocks : _useEntityProp4$,
      onInput = _useEntityProp4[1];

  var onChange = Object(external_this_wp_element_["useCallback"])(function (nextBlocks) {
    onInput(nextBlocks); // Use a function edit to avoid serializing often.

    setContent(function (_ref3) {
      var blocksToSerialize = _ref3.blocks;
      return Object(external_this_wp_blocks_["serialize"])(blocksToSerialize);
    });
  }, [onInput, setContent]);
  return [blocks, onInput, onChange];
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
Object(external_this_wp_data_["registerStore"])(REDUCER_KEY, {
  reducer: build_module_reducer,
  controls: external_this_wp_dataControls_["controls"],
  actions: build_module_objectSpread(build_module_objectSpread({}, build_module_actions_namespaceObject), entityActions),
  selectors: build_module_objectSpread(build_module_objectSpread({}, build_module_selectors_namespaceObject), entitySelectors),
  resolvers: build_module_objectSpread(build_module_objectSpread({}, resolvers_namespaceObject), entityResolvers)
});




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

/***/ }),

/***/ 64:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["isShallowEqual"]; }());

/***/ })

/******/ });