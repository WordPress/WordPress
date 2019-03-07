this["wp"] = this["wp"] || {}; this["wp"]["blockEditor"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 312);
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

/***/ 10:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ 11:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _possibleConstructorReturn; });
/* harmony import */ var _helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(29);
/* harmony import */ var _assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(3);


function _possibleConstructorReturn(self, call) {
  if (call && (Object(_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(call) === "object" || typeof call === "function")) {
    return call;
  }

  return Object(_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(self);
}

/***/ }),

/***/ 12:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _getPrototypeOf; });
function _getPrototypeOf(o) {
  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

/***/ }),

/***/ 13:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js
function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _inherits; });

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  if (superClass) _setPrototypeOf(subClass, superClass);
}

/***/ }),

/***/ 14:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),

/***/ 15:
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

/***/ 18:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js
function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  }
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
var iterableToArray = __webpack_require__(33);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _toConsumableArray; });



function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || Object(iterableToArray["a" /* default */])(arr) || _nonIterableSpread();
}

/***/ }),

/***/ 19:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _extends; });
function _extends() {
  _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };

  return _extends.apply(this, arguments);
}

/***/ }),

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ 21:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js
function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectWithoutProperties; });

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};
  var target = _objectWithoutPropertiesLoose(source, excluded);
  var key, i;

  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);

    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }

  return target;
}

/***/ }),

/***/ 25:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
var arrayWithHoles = __webpack_require__(34);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js
function _iterableToArrayLimit(arr, i) {
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
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
var nonIterableRest = __webpack_require__(35);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _slicedToArray; });



function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ 28:
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

/***/ 29:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _typeof; });
function _typeof2(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

function _typeof(obj) {
  if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
    _typeof = function _typeof(obj) {
      return _typeof2(obj);
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
    };
  }

  return _typeof(obj);
}

/***/ }),

/***/ 3:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _assertThisInitialized; });
function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

/***/ }),

/***/ 312:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "resetBlocks", function() { return resetBlocks; });
__webpack_require__.d(actions_namespaceObject, "receiveBlocks", function() { return receiveBlocks; });
__webpack_require__.d(actions_namespaceObject, "updateBlockAttributes", function() { return updateBlockAttributes; });
__webpack_require__.d(actions_namespaceObject, "updateBlock", function() { return updateBlock; });
__webpack_require__.d(actions_namespaceObject, "selectBlock", function() { return selectBlock; });
__webpack_require__.d(actions_namespaceObject, "selectPreviousBlock", function() { return selectPreviousBlock; });
__webpack_require__.d(actions_namespaceObject, "selectNextBlock", function() { return selectNextBlock; });
__webpack_require__.d(actions_namespaceObject, "startMultiSelect", function() { return startMultiSelect; });
__webpack_require__.d(actions_namespaceObject, "stopMultiSelect", function() { return stopMultiSelect; });
__webpack_require__.d(actions_namespaceObject, "multiSelect", function() { return multiSelect; });
__webpack_require__.d(actions_namespaceObject, "clearSelectedBlock", function() { return clearSelectedBlock; });
__webpack_require__.d(actions_namespaceObject, "toggleSelection", function() { return toggleSelection; });
__webpack_require__.d(actions_namespaceObject, "replaceBlocks", function() { return replaceBlocks; });
__webpack_require__.d(actions_namespaceObject, "replaceBlock", function() { return replaceBlock; });
__webpack_require__.d(actions_namespaceObject, "moveBlocksDown", function() { return moveBlocksDown; });
__webpack_require__.d(actions_namespaceObject, "moveBlocksUp", function() { return moveBlocksUp; });
__webpack_require__.d(actions_namespaceObject, "moveBlockToPosition", function() { return moveBlockToPosition; });
__webpack_require__.d(actions_namespaceObject, "insertBlock", function() { return insertBlock; });
__webpack_require__.d(actions_namespaceObject, "insertBlocks", function() { return insertBlocks; });
__webpack_require__.d(actions_namespaceObject, "showInsertionPoint", function() { return showInsertionPoint; });
__webpack_require__.d(actions_namespaceObject, "hideInsertionPoint", function() { return hideInsertionPoint; });
__webpack_require__.d(actions_namespaceObject, "setTemplateValidity", function() { return setTemplateValidity; });
__webpack_require__.d(actions_namespaceObject, "synchronizeTemplate", function() { return synchronizeTemplate; });
__webpack_require__.d(actions_namespaceObject, "mergeBlocks", function() { return mergeBlocks; });
__webpack_require__.d(actions_namespaceObject, "removeBlocks", function() { return removeBlocks; });
__webpack_require__.d(actions_namespaceObject, "removeBlock", function() { return removeBlock; });
__webpack_require__.d(actions_namespaceObject, "toggleBlockMode", function() { return toggleBlockMode; });
__webpack_require__.d(actions_namespaceObject, "startTyping", function() { return startTyping; });
__webpack_require__.d(actions_namespaceObject, "stopTyping", function() { return stopTyping; });
__webpack_require__.d(actions_namespaceObject, "enterFormattedText", function() { return enterFormattedText; });
__webpack_require__.d(actions_namespaceObject, "exitFormattedText", function() { return exitFormattedText; });
__webpack_require__.d(actions_namespaceObject, "insertDefaultBlock", function() { return insertDefaultBlock; });
__webpack_require__.d(actions_namespaceObject, "updateBlockListSettings", function() { return updateBlockListSettings; });
__webpack_require__.d(actions_namespaceObject, "updateSettings", function() { return updateSettings; });
__webpack_require__.d(actions_namespaceObject, "__unstableSaveReusableBlock", function() { return __unstableSaveReusableBlock; });
__webpack_require__.d(actions_namespaceObject, "__unstableMarkLastChangeAsPersistent", function() { return __unstableMarkLastChangeAsPersistent; });
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "INSERTER_UTILITY_HIGH", function() { return INSERTER_UTILITY_HIGH; });
__webpack_require__.d(selectors_namespaceObject, "INSERTER_UTILITY_MEDIUM", function() { return INSERTER_UTILITY_MEDIUM; });
__webpack_require__.d(selectors_namespaceObject, "INSERTER_UTILITY_LOW", function() { return INSERTER_UTILITY_LOW; });
__webpack_require__.d(selectors_namespaceObject, "INSERTER_UTILITY_NONE", function() { return INSERTER_UTILITY_NONE; });
__webpack_require__.d(selectors_namespaceObject, "getBlockDependantsCacheBust", function() { return getBlockDependantsCacheBust; });
__webpack_require__.d(selectors_namespaceObject, "getBlockName", function() { return getBlockName; });
__webpack_require__.d(selectors_namespaceObject, "isBlockValid", function() { return isBlockValid; });
__webpack_require__.d(selectors_namespaceObject, "getBlockAttributes", function() { return getBlockAttributes; });
__webpack_require__.d(selectors_namespaceObject, "getBlock", function() { return getBlock; });
__webpack_require__.d(selectors_namespaceObject, "__unstableGetBlockWithoutInnerBlocks", function() { return __unstableGetBlockWithoutInnerBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getBlocks", function() { return getBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getClientIdsOfDescendants", function() { return selectors_getClientIdsOfDescendants; });
__webpack_require__.d(selectors_namespaceObject, "getClientIdsWithDescendants", function() { return getClientIdsWithDescendants; });
__webpack_require__.d(selectors_namespaceObject, "getGlobalBlockCount", function() { return getGlobalBlockCount; });
__webpack_require__.d(selectors_namespaceObject, "getBlocksByClientId", function() { return getBlocksByClientId; });
__webpack_require__.d(selectors_namespaceObject, "getBlockCount", function() { return getBlockCount; });
__webpack_require__.d(selectors_namespaceObject, "getBlockSelectionStart", function() { return getBlockSelectionStart; });
__webpack_require__.d(selectors_namespaceObject, "getBlockSelectionEnd", function() { return getBlockSelectionEnd; });
__webpack_require__.d(selectors_namespaceObject, "getSelectedBlockCount", function() { return getSelectedBlockCount; });
__webpack_require__.d(selectors_namespaceObject, "hasSelectedBlock", function() { return hasSelectedBlock; });
__webpack_require__.d(selectors_namespaceObject, "getSelectedBlockClientId", function() { return getSelectedBlockClientId; });
__webpack_require__.d(selectors_namespaceObject, "getSelectedBlock", function() { return getSelectedBlock; });
__webpack_require__.d(selectors_namespaceObject, "getBlockRootClientId", function() { return getBlockRootClientId; });
__webpack_require__.d(selectors_namespaceObject, "getBlockHierarchyRootClientId", function() { return getBlockHierarchyRootClientId; });
__webpack_require__.d(selectors_namespaceObject, "getAdjacentBlockClientId", function() { return getAdjacentBlockClientId; });
__webpack_require__.d(selectors_namespaceObject, "getPreviousBlockClientId", function() { return getPreviousBlockClientId; });
__webpack_require__.d(selectors_namespaceObject, "getNextBlockClientId", function() { return getNextBlockClientId; });
__webpack_require__.d(selectors_namespaceObject, "getSelectedBlocksInitialCaretPosition", function() { return getSelectedBlocksInitialCaretPosition; });
__webpack_require__.d(selectors_namespaceObject, "getMultiSelectedBlockClientIds", function() { return getMultiSelectedBlockClientIds; });
__webpack_require__.d(selectors_namespaceObject, "getMultiSelectedBlocks", function() { return getMultiSelectedBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getFirstMultiSelectedBlockClientId", function() { return getFirstMultiSelectedBlockClientId; });
__webpack_require__.d(selectors_namespaceObject, "getLastMultiSelectedBlockClientId", function() { return getLastMultiSelectedBlockClientId; });
__webpack_require__.d(selectors_namespaceObject, "isFirstMultiSelectedBlock", function() { return isFirstMultiSelectedBlock; });
__webpack_require__.d(selectors_namespaceObject, "isBlockMultiSelected", function() { return isBlockMultiSelected; });
__webpack_require__.d(selectors_namespaceObject, "isAncestorMultiSelected", function() { return isAncestorMultiSelected; });
__webpack_require__.d(selectors_namespaceObject, "getMultiSelectedBlocksStartClientId", function() { return getMultiSelectedBlocksStartClientId; });
__webpack_require__.d(selectors_namespaceObject, "getMultiSelectedBlocksEndClientId", function() { return getMultiSelectedBlocksEndClientId; });
__webpack_require__.d(selectors_namespaceObject, "getBlockOrder", function() { return getBlockOrder; });
__webpack_require__.d(selectors_namespaceObject, "getBlockIndex", function() { return getBlockIndex; });
__webpack_require__.d(selectors_namespaceObject, "isBlockSelected", function() { return isBlockSelected; });
__webpack_require__.d(selectors_namespaceObject, "hasSelectedInnerBlock", function() { return hasSelectedInnerBlock; });
__webpack_require__.d(selectors_namespaceObject, "isBlockWithinSelection", function() { return isBlockWithinSelection; });
__webpack_require__.d(selectors_namespaceObject, "hasMultiSelection", function() { return hasMultiSelection; });
__webpack_require__.d(selectors_namespaceObject, "isMultiSelecting", function() { return isMultiSelecting; });
__webpack_require__.d(selectors_namespaceObject, "isSelectionEnabled", function() { return isSelectionEnabled; });
__webpack_require__.d(selectors_namespaceObject, "getBlockMode", function() { return getBlockMode; });
__webpack_require__.d(selectors_namespaceObject, "isTyping", function() { return selectors_isTyping; });
__webpack_require__.d(selectors_namespaceObject, "isCaretWithinFormattedText", function() { return selectors_isCaretWithinFormattedText; });
__webpack_require__.d(selectors_namespaceObject, "getBlockInsertionPoint", function() { return getBlockInsertionPoint; });
__webpack_require__.d(selectors_namespaceObject, "isBlockInsertionPointVisible", function() { return isBlockInsertionPointVisible; });
__webpack_require__.d(selectors_namespaceObject, "isValidTemplate", function() { return isValidTemplate; });
__webpack_require__.d(selectors_namespaceObject, "getTemplate", function() { return getTemplate; });
__webpack_require__.d(selectors_namespaceObject, "getTemplateLock", function() { return getTemplateLock; });
__webpack_require__.d(selectors_namespaceObject, "canInsertBlockType", function() { return canInsertBlockType; });
__webpack_require__.d(selectors_namespaceObject, "getInserterItems", function() { return getInserterItems; });
__webpack_require__.d(selectors_namespaceObject, "hasInserterItems", function() { return hasInserterItems; });
__webpack_require__.d(selectors_namespaceObject, "getBlockListSettings", function() { return getBlockListSettings; });
__webpack_require__.d(selectors_namespaceObject, "getSettings", function() { return getSettings; });
__webpack_require__.d(selectors_namespaceObject, "isLastBlockChangePersistent", function() { return isLastBlockChangePersistent; });

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__(8);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js + 1 modules
var objectWithoutProperties = __webpack_require__(21);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__(18);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(15);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-editor/build-module/store/defaults.js
/**
 * WordPress dependencies
 */

var PREFERENCES_DEFAULTS = {
  insertUsage: {}
};
/**
 * The default editor settings
 *
 *  alignWide              boolean        Enable/Disable Wide/Full Alignments
 *  colors                 Array          Palette colors
 *  disableCustomColors    boolean        Whether or not the custom colors are disabled
 *  fontSizes              Array          Available font sizes
 *  disableCustomFontSizes boolean        Whether or not the custom font sizes are disabled
 *  imageSizes             Array          Available image sizes
 *  maxWidth               number         Max width to constraint resizing
 *  allowedBlockTypes      boolean|Array  Allowed block types
 *  hasFixedToolbar        boolean        Whether or not the editor toolbar is fixed
 *  focusMode              boolean        Whether the focus mode is enabled or not
 *  styles                 Array          Editor Styles
 *  isRTL                  boolean        Whether the editor is in RTL mode
 *  bodyPlaceholder        string         Empty post placeholder
 *  titlePlaceholder       string         Empty title placeholder
 */

var SETTINGS_DEFAULTS = {
  alignWide: false,
  colors: [{
    name: Object(external_this_wp_i18n_["__"])('Pale pink'),
    slug: 'pale-pink',
    color: '#f78da7'
  }, {
    name: Object(external_this_wp_i18n_["__"])('Vivid red'),
    slug: 'vivid-red',
    color: '#cf2e2e'
  }, {
    name: Object(external_this_wp_i18n_["__"])('Luminous vivid orange'),
    slug: 'luminous-vivid-orange',
    color: '#ff6900'
  }, {
    name: Object(external_this_wp_i18n_["__"])('Luminous vivid amber'),
    slug: 'luminous-vivid-amber',
    color: '#fcb900'
  }, {
    name: Object(external_this_wp_i18n_["__"])('Light green cyan'),
    slug: 'light-green-cyan',
    color: '#7bdcb5'
  }, {
    name: Object(external_this_wp_i18n_["__"])('Vivid green cyan'),
    slug: 'vivid-green-cyan',
    color: '#00d084'
  }, {
    name: Object(external_this_wp_i18n_["__"])('Pale cyan blue'),
    slug: 'pale-cyan-blue',
    color: '#8ed1fc'
  }, {
    name: Object(external_this_wp_i18n_["__"])('Vivid cyan blue'),
    slug: 'vivid-cyan-blue',
    color: '#0693e3'
  }, {
    name: Object(external_this_wp_i18n_["__"])('Very light gray'),
    slug: 'very-light-gray',
    color: '#eeeeee'
  }, {
    name: Object(external_this_wp_i18n_["__"])('Cyan bluish gray'),
    slug: 'cyan-bluish-gray',
    color: '#abb8c3'
  }, {
    name: Object(external_this_wp_i18n_["__"])('Very dark gray'),
    slug: 'very-dark-gray',
    color: '#313131'
  }],
  fontSizes: [{
    name: Object(external_this_wp_i18n_["_x"])('Small', 'font size name'),
    size: 13,
    slug: 'small'
  }, {
    name: Object(external_this_wp_i18n_["_x"])('Normal', 'font size name'),
    size: 16,
    slug: 'normal'
  }, {
    name: Object(external_this_wp_i18n_["_x"])('Medium', 'font size name'),
    size: 20,
    slug: 'medium'
  }, {
    name: Object(external_this_wp_i18n_["_x"])('Large', 'font size name'),
    size: 36,
    slug: 'large'
  }, {
    name: Object(external_this_wp_i18n_["_x"])('Huge', 'font size name'),
    size: 48,
    slug: 'huge'
  }],
  imageSizes: [{
    slug: 'thumbnail',
    label: Object(external_this_wp_i18n_["__"])('Thumbnail')
  }, {
    slug: 'medium',
    label: Object(external_this_wp_i18n_["__"])('Medium')
  }, {
    slug: 'large',
    label: Object(external_this_wp_i18n_["__"])('Large')
  }, {
    slug: 'full',
    label: Object(external_this_wp_i18n_["__"])('Full Size')
  }],
  // This is current max width of the block inner area
  // It's used to constraint image resizing and this value could be overridden later by themes
  maxWidth: 580,
  // Allowed block types for the editor, defaulting to true (all supported).
  allowedBlockTypes: true,
  // Maximum upload size in bytes allowed for the site.
  maxUploadFileSize: 0,
  // List of allowed mime types and file extensions.
  allowedMimeTypes: null
};

// CONCATENATED MODULE: ./node_modules/@wordpress/block-editor/build-module/store/array.js


/**
 * External dependencies
 */

/**
 * Insert one or multiple elements into a given position of an array.
 *
 * @param {Array}  array    Source array.
 * @param {*}      elements Elements to insert.
 * @param {number} index    Insert Position.
 *
 * @return {Array}          Result.
 */

function insertAt(array, elements, index) {
  return [].concat(Object(toConsumableArray["a" /* default */])(array.slice(0, index)), Object(toConsumableArray["a" /* default */])(Object(external_lodash_["castArray"])(elements)), Object(toConsumableArray["a" /* default */])(array.slice(index)));
}
/**
 * Moves an element in an array.
 *
 * @param {Array}  array Source array.
 * @param {number} from  Source index.
 * @param {number} to    Destination index.
 * @param {number} count Number of elements to move.
 *
 * @return {Array}       Result.
 */

function moveTo(array, from, to) {
  var count = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 1;

  var withoutMovedElements = Object(toConsumableArray["a" /* default */])(array);

  withoutMovedElements.splice(from, count);
  return insertAt(withoutMovedElements, array.slice(from, from + count), to);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-editor/build-module/store/reducer.js





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
 * Given an array of blocks, returns an object where each key is a nesting
 * context, the value of which is an array of block client IDs existing within
 * that nesting context.
 *
 * @param {Array}   blocks       Blocks to map.
 * @param {?string} rootClientId Assumed root client ID.
 *
 * @return {Object} Block order map object.
 */

function mapBlockOrder(blocks) {
  var rootClientId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

  var result = Object(defineProperty["a" /* default */])({}, rootClientId, []);

  blocks.forEach(function (block) {
    var clientId = block.clientId,
        innerBlocks = block.innerBlocks;
    result[rootClientId].push(clientId);
    Object.assign(result, mapBlockOrder(innerBlocks, clientId));
  });
  return result;
}
/**
 * Helper method to iterate through all blocks, recursing into inner blocks,
 * applying a transformation function to each one.
 * Returns a flattened object with the transformed blocks.
 *
 * @param {Array} blocks Blocks to flatten.
 * @param {Function} transform Transforming function to be applied to each block.
 *
 * @return {Object} Flattened object.
 */


function flattenBlocks(blocks, transform) {
  var result = {};

  var stack = Object(toConsumableArray["a" /* default */])(blocks);

  while (stack.length) {
    var _stack$shift = stack.shift(),
        innerBlocks = _stack$shift.innerBlocks,
        block = Object(objectWithoutProperties["a" /* default */])(_stack$shift, ["innerBlocks"]);

    stack.push.apply(stack, Object(toConsumableArray["a" /* default */])(innerBlocks));
    result[block.clientId] = transform(block);
  }

  return result;
}
/**
 * Given an array of blocks, returns an object containing all blocks, without
 * attributes, recursing into inner blocks. Keys correspond to the block client
 * ID, the value of which is the attributes object.
 *
 * @param {Array} blocks Blocks to flatten.
 *
 * @return {Object} Flattened block attributes object.
 */


function getFlattenedBlocksWithoutAttributes(blocks) {
  return flattenBlocks(blocks, function (block) {
    return Object(external_lodash_["omit"])(block, 'attributes');
  });
}
/**
 * Given an array of blocks, returns an object containing all block attributes,
 * recursing into inner blocks. Keys correspond to the block client ID, the
 * value of which is the attributes object.
 *
 * @param {Array} blocks Blocks to flatten.
 *
 * @return {Object} Flattened block attributes object.
 */


function getFlattenedBlockAttributes(blocks) {
  return flattenBlocks(blocks, function (block) {
    return block.attributes;
  });
}
/**
 * Given a block order map object, returns *all* of the block client IDs that are
 * a descendant of the given root client ID.
 *
 * Calling this with `rootClientId` set to `''` results in a list of client IDs
 * that are in the post. That is, it excludes blocks like fetched reusable
 * blocks which are stored into state but not visible.
 *
 * @param {Object}  blocksOrder  Object that maps block client IDs to a list of
 *                               nested block client IDs.
 * @param {?string} rootClientId The root client ID to search. Defaults to ''.
 *
 * @return {Array} List of descendant client IDs.
 */


function getNestedBlockClientIds(blocksOrder) {
  var rootClientId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  return Object(external_lodash_["reduce"])(blocksOrder[rootClientId], function (result, clientId) {
    return [].concat(Object(toConsumableArray["a" /* default */])(result), [clientId], Object(toConsumableArray["a" /* default */])(getNestedBlockClientIds(blocksOrder, clientId)));
  }, []);
}
/**
 * Returns an object against which it is safe to perform mutating operations,
 * given the original object and its current working copy.
 *
 * @param {Object} original Original object.
 * @param {Object} working  Working object.
 *
 * @return {Object} Mutation-safe object.
 */


function getMutateSafeObject(original, working) {
  if (original === working) {
    return Object(objectSpread["a" /* default */])({}, original);
  }

  return working;
}
/**
 * Returns true if the two object arguments have the same keys, or false
 * otherwise.
 *
 * @param {Object} a First object.
 * @param {Object} b Second object.
 *
 * @return {boolean} Whether the two objects have the same keys.
 */


function hasSameKeys(a, b) {
  return Object(external_lodash_["isEqual"])(Object(external_lodash_["keys"])(a), Object(external_lodash_["keys"])(b));
}
/**
 * Returns true if, given the currently dispatching action and the previously
 * dispatched action, the two actions are updating the same block attribute, or
 * false otherwise.
 *
 * @param {Object} action     Currently dispatching action.
 * @param {Object} lastAction Previously dispatched action.
 *
 * @return {boolean} Whether actions are updating the same block attribute.
 */

function isUpdatingSameBlockAttribute(action, lastAction) {
  return action.type === 'UPDATE_BLOCK_ATTRIBUTES' && lastAction !== undefined && lastAction.type === 'UPDATE_BLOCK_ATTRIBUTES' && action.clientId === lastAction.clientId && hasSameKeys(action.attributes, lastAction.attributes);
}
/**
 * Higher-order reducer intended to augment the blocks reducer, assigning an
 * `isPersistentChange` property value corresponding to whether a change in
 * state can be considered as persistent. All changes are considered persistent
 * except when updating the same block attribute as in the previous action.
 *
 * @param {Function} reducer Original reducer function.
 *
 * @return {Function} Enhanced reducer function.
 */

function withPersistentBlockChange(reducer) {
  var lastAction;
  /**
   * Set of action types for which a blocks state change should be considered
   * non-persistent.
   *
   * @type {Set}
   */

  var IGNORED_ACTION_TYPES = new Set(['RECEIVE_BLOCKS']);
  return function (state, action) {
    var nextState = reducer(state, action);
    var isExplicitPersistentChange = action.type === 'MARK_LAST_CHANGE_AS_PERSISTENT'; // Defer to previous state value (or default) unless changing or
    // explicitly marking as persistent.

    if (state === nextState && !isExplicitPersistentChange) {
      return Object(objectSpread["a" /* default */])({}, nextState, {
        isPersistentChange: Object(external_lodash_["get"])(state, ['isPersistentChange'], true)
      });
    } // Some state changes should not be considered persistent, namely those
    // which are not a direct result of user interaction.


    var isIgnoredActionType = IGNORED_ACTION_TYPES.has(action.type);

    if (isIgnoredActionType) {
      return Object(objectSpread["a" /* default */])({}, nextState, {
        isPersistentChange: false
      });
    }

    nextState = Object(objectSpread["a" /* default */])({}, nextState, {
      isPersistentChange: isExplicitPersistentChange || !isUpdatingSameBlockAttribute(action, lastAction)
    }); // In comparing against the previous action, consider only those which
    // would have qualified as one which would have been ignored or not
    // have resulted in a changed state.

    lastAction = action;
    return nextState;
  };
}
/**
 * Higher-order reducer targeting the combined blocks reducer, augmenting
 * block client IDs in remove action to include cascade of inner blocks.
 *
 * @param {Function} reducer Original reducer function.
 *
 * @return {Function} Enhanced reducer function.
 */


var reducer_withInnerBlocksRemoveCascade = function withInnerBlocksRemoveCascade(reducer) {
  return function (state, action) {
    if (state && action.type === 'REMOVE_BLOCKS') {
      var clientIds = Object(toConsumableArray["a" /* default */])(action.clientIds); // For each removed client ID, include its inner blocks to remove,
      // recursing into those so long as inner blocks exist.


      for (var i = 0; i < clientIds.length; i++) {
        clientIds.push.apply(clientIds, Object(toConsumableArray["a" /* default */])(state.order[clientIds[i]]));
      }

      action = Object(objectSpread["a" /* default */])({}, action, {
        clientIds: clientIds
      });
    }

    return reducer(state, action);
  };
};
/**
 * Higher-order reducer which targets the combined blocks reducer and handles
 * the `RESET_BLOCKS` action. When dispatched, this action will replace all
 * blocks that exist in the post, leaving blocks that exist only in state (e.g.
 * reusable blocks) alone.
 *
 * @param {Function} reducer Original reducer function.
 *
 * @return {Function} Enhanced reducer function.
 */


var reducer_withBlockReset = function withBlockReset(reducer) {
  return function (state, action) {
    if (state && action.type === 'RESET_BLOCKS') {
      var visibleClientIds = getNestedBlockClientIds(state.order);
      return Object(objectSpread["a" /* default */])({}, state, {
        byClientId: Object(objectSpread["a" /* default */])({}, Object(external_lodash_["omit"])(state.byClientId, visibleClientIds), getFlattenedBlocksWithoutAttributes(action.blocks)),
        attributes: Object(objectSpread["a" /* default */])({}, Object(external_lodash_["omit"])(state.attributes, visibleClientIds), getFlattenedBlockAttributes(action.blocks)),
        order: Object(objectSpread["a" /* default */])({}, Object(external_lodash_["omit"])(state.order, visibleClientIds), mapBlockOrder(action.blocks))
      });
    }

    return reducer(state, action);
  };
};
/**
 * Higher-order reducer which targets the combined blocks reducer and handles
 * the `SAVE_REUSABLE_BLOCK_SUCCESS` action. This action can't be handled by
 * regular reducers and needs a higher-order reducer since it needs access to
 * both `byClientId` and `attributes` simultaneously.
 *
 * @param {Function} reducer Original reducer function.
 *
 * @return {Function} Enhanced reducer function.
 */


var reducer_withSaveReusableBlock = function withSaveReusableBlock(reducer) {
  return function (state, action) {
    if (state && action.type === 'SAVE_REUSABLE_BLOCK_SUCCESS') {
      var id = action.id,
          updatedId = action.updatedId; // If a temporary reusable block is saved, we swap the temporary id with the final one

      if (id === updatedId) {
        return state;
      }

      state = Object(objectSpread["a" /* default */])({}, state);
      state.attributes = Object(external_lodash_["mapValues"])(state.attributes, function (attributes, clientId) {
        var name = state.byClientId[clientId].name;

        if (name === 'core/block' && attributes.ref === id) {
          return Object(objectSpread["a" /* default */])({}, attributes, {
            ref: updatedId
          });
        }

        return attributes;
      });
    }

    return reducer(state, action);
  };
};
/**
 * Reducer returning the blocks state.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @returns {Object} Updated state.
 */


var reducer_blocks = Object(external_lodash_["flow"])(external_this_wp_data_["combineReducers"], reducer_withInnerBlocksRemoveCascade, reducer_withBlockReset, reducer_withSaveReusableBlock, withPersistentBlockChange)({
  byClientId: function byClientId() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    var action = arguments.length > 1 ? arguments[1] : undefined;

    switch (action.type) {
      case 'RESET_BLOCKS':
        return getFlattenedBlocksWithoutAttributes(action.blocks);

      case 'RECEIVE_BLOCKS':
        return Object(objectSpread["a" /* default */])({}, state, getFlattenedBlocksWithoutAttributes(action.blocks));

      case 'UPDATE_BLOCK':
        // Ignore updates if block isn't known
        if (!state[action.clientId]) {
          return state;
        } // Do nothing if only attributes change.


        var changes = Object(external_lodash_["omit"])(action.updates, 'attributes');

        if (Object(external_lodash_["isEmpty"])(changes)) {
          return state;
        }

        return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, action.clientId, Object(objectSpread["a" /* default */])({}, state[action.clientId], changes)));

      case 'INSERT_BLOCKS':
        return Object(objectSpread["a" /* default */])({}, state, getFlattenedBlocksWithoutAttributes(action.blocks));

      case 'REPLACE_BLOCKS':
        if (!action.blocks) {
          return state;
        }

        return Object(objectSpread["a" /* default */])({}, Object(external_lodash_["omit"])(state, action.clientIds), getFlattenedBlocksWithoutAttributes(action.blocks));

      case 'REMOVE_BLOCKS':
        return Object(external_lodash_["omit"])(state, action.clientIds);
    }

    return state;
  },
  attributes: function attributes() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    var action = arguments.length > 1 ? arguments[1] : undefined;

    switch (action.type) {
      case 'RESET_BLOCKS':
        return getFlattenedBlockAttributes(action.blocks);

      case 'RECEIVE_BLOCKS':
        return Object(objectSpread["a" /* default */])({}, state, getFlattenedBlockAttributes(action.blocks));

      case 'UPDATE_BLOCK':
        // Ignore updates if block isn't known or there are no attribute changes.
        if (!state[action.clientId] || !action.updates.attributes) {
          return state;
        }

        return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, action.clientId, Object(objectSpread["a" /* default */])({}, state[action.clientId], action.updates.attributes)));

      case 'UPDATE_BLOCK_ATTRIBUTES':
        // Ignore updates if block isn't known
        if (!state[action.clientId]) {
          return state;
        } // Consider as updates only changed values


        var nextAttributes = Object(external_lodash_["reduce"])(action.attributes, function (result, value, key) {
          if (value !== result[key]) {
            result = getMutateSafeObject(state[action.clientId], result);
            result[key] = value;
          }

          return result;
        }, state[action.clientId]); // Skip update if nothing has been changed. The reference will
        // match the original block if `reduce` had no changed values.

        if (nextAttributes === state[action.clientId]) {
          return state;
        } // Otherwise replace attributes in state


        return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, action.clientId, nextAttributes));

      case 'INSERT_BLOCKS':
        return Object(objectSpread["a" /* default */])({}, state, getFlattenedBlockAttributes(action.blocks));

      case 'REPLACE_BLOCKS':
        if (!action.blocks) {
          return state;
        }

        return Object(objectSpread["a" /* default */])({}, Object(external_lodash_["omit"])(state, action.clientIds), getFlattenedBlockAttributes(action.blocks));

      case 'REMOVE_BLOCKS':
        return Object(external_lodash_["omit"])(state, action.clientIds);
    }

    return state;
  },
  order: function order() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    var action = arguments.length > 1 ? arguments[1] : undefined;

    switch (action.type) {
      case 'RESET_BLOCKS':
        return mapBlockOrder(action.blocks);

      case 'RECEIVE_BLOCKS':
        return Object(objectSpread["a" /* default */])({}, state, Object(external_lodash_["omit"])(mapBlockOrder(action.blocks), ''));

      case 'INSERT_BLOCKS':
        {
          var _action$rootClientId = action.rootClientId,
              rootClientId = _action$rootClientId === void 0 ? '' : _action$rootClientId;
          var subState = state[rootClientId] || [];
          var mappedBlocks = mapBlockOrder(action.blocks, rootClientId);
          var _action$index = action.index,
              index = _action$index === void 0 ? subState.length : _action$index;
          return Object(objectSpread["a" /* default */])({}, state, mappedBlocks, Object(defineProperty["a" /* default */])({}, rootClientId, insertAt(subState, mappedBlocks[rootClientId], index)));
        }

      case 'MOVE_BLOCK_TO_POSITION':
        {
          var _objectSpread7;

          var _action$fromRootClien = action.fromRootClientId,
              fromRootClientId = _action$fromRootClien === void 0 ? '' : _action$fromRootClien,
              _action$toRootClientI = action.toRootClientId,
              toRootClientId = _action$toRootClientI === void 0 ? '' : _action$toRootClientI,
              clientId = action.clientId;

          var _action$index2 = action.index,
              _index = _action$index2 === void 0 ? state[toRootClientId].length : _action$index2; // Moving inside the same parent block


          if (fromRootClientId === toRootClientId) {
            var _subState = state[toRootClientId];

            var fromIndex = _subState.indexOf(clientId);

            return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, toRootClientId, moveTo(state[toRootClientId], fromIndex, _index)));
          } // Moving from a parent block to another


          return Object(objectSpread["a" /* default */])({}, state, (_objectSpread7 = {}, Object(defineProperty["a" /* default */])(_objectSpread7, fromRootClientId, Object(external_lodash_["without"])(state[fromRootClientId], clientId)), Object(defineProperty["a" /* default */])(_objectSpread7, toRootClientId, insertAt(state[toRootClientId], clientId, _index)), _objectSpread7));
        }

      case 'MOVE_BLOCKS_UP':
        {
          var clientIds = action.clientIds,
              _action$rootClientId2 = action.rootClientId,
              _rootClientId = _action$rootClientId2 === void 0 ? '' : _action$rootClientId2;

          var firstClientId = Object(external_lodash_["first"])(clientIds);
          var _subState2 = state[_rootClientId];

          if (!_subState2.length || firstClientId === Object(external_lodash_["first"])(_subState2)) {
            return state;
          }

          var firstIndex = _subState2.indexOf(firstClientId);

          return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, _rootClientId, moveTo(_subState2, firstIndex, firstIndex - 1, clientIds.length)));
        }

      case 'MOVE_BLOCKS_DOWN':
        {
          var _clientIds = action.clientIds,
              _action$rootClientId3 = action.rootClientId,
              _rootClientId2 = _action$rootClientId3 === void 0 ? '' : _action$rootClientId3;

          var _firstClientId = Object(external_lodash_["first"])(_clientIds);

          var lastClientId = Object(external_lodash_["last"])(_clientIds);
          var _subState3 = state[_rootClientId2];

          if (!_subState3.length || lastClientId === Object(external_lodash_["last"])(_subState3)) {
            return state;
          }

          var _firstIndex = _subState3.indexOf(_firstClientId);

          return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, _rootClientId2, moveTo(_subState3, _firstIndex, _firstIndex + 1, _clientIds.length)));
        }

      case 'REPLACE_BLOCKS':
        {
          var _clientIds2 = action.clientIds;

          if (!action.blocks) {
            return state;
          }

          var _mappedBlocks = mapBlockOrder(action.blocks);

          return Object(external_lodash_["flow"])([function (nextState) {
            return Object(external_lodash_["omit"])(nextState, _clientIds2);
          }, function (nextState) {
            return Object(objectSpread["a" /* default */])({}, nextState, Object(external_lodash_["omit"])(_mappedBlocks, ''));
          }, function (nextState) {
            return Object(external_lodash_["mapValues"])(nextState, function (subState) {
              return Object(external_lodash_["reduce"])(subState, function (result, clientId) {
                if (clientId === _clientIds2[0]) {
                  return [].concat(Object(toConsumableArray["a" /* default */])(result), Object(toConsumableArray["a" /* default */])(_mappedBlocks['']));
                }

                if (_clientIds2.indexOf(clientId) === -1) {
                  result.push(clientId);
                }

                return result;
              }, []);
            });
          }])(state);
        }

      case 'REMOVE_BLOCKS':
        return Object(external_lodash_["flow"])([// Remove inner block ordering for removed blocks
        function (nextState) {
          return Object(external_lodash_["omit"])(nextState, action.clientIds);
        }, // Remove deleted blocks from other blocks' orderings
        function (nextState) {
          return Object(external_lodash_["mapValues"])(nextState, function (subState) {
            return external_lodash_["without"].apply(void 0, [subState].concat(Object(toConsumableArray["a" /* default */])(action.clientIds)));
          });
        }])(state);
    }

    return state;
  }
});
/**
 * Reducer returning typing state.
 *
 * @param {boolean} state  Current state.
 * @param {Object}  action Dispatched action.
 *
 * @return {boolean} Updated state.
 */

function isTyping() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'START_TYPING':
      return true;

    case 'STOP_TYPING':
      return false;
  }

  return state;
}
/**
 * Reducer returning whether the caret is within formatted text.
 *
 * @param {boolean} state  Current state.
 * @param {Object}  action Dispatched action.
 *
 * @return {boolean} Updated state.
 */

function isCaretWithinFormattedText() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'ENTER_FORMATTED_TEXT':
      return true;

    case 'EXIT_FORMATTED_TEXT':
      return false;
  }

  return state;
}
/**
 * Reducer returning the block selection's state.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function blockSelection() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
    start: null,
    end: null,
    isMultiSelecting: false,
    isEnabled: true,
    initialPosition: null
  };
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'CLEAR_SELECTED_BLOCK':
      if (state.start === null && state.end === null && !state.isMultiSelecting) {
        return state;
      }

      return Object(objectSpread["a" /* default */])({}, state, {
        start: null,
        end: null,
        isMultiSelecting: false,
        initialPosition: null
      });

    case 'START_MULTI_SELECT':
      if (state.isMultiSelecting) {
        return state;
      }

      return Object(objectSpread["a" /* default */])({}, state, {
        isMultiSelecting: true,
        initialPosition: null
      });

    case 'STOP_MULTI_SELECT':
      if (!state.isMultiSelecting) {
        return state;
      }

      return Object(objectSpread["a" /* default */])({}, state, {
        isMultiSelecting: false,
        initialPosition: null
      });

    case 'MULTI_SELECT':
      return Object(objectSpread["a" /* default */])({}, state, {
        start: action.start,
        end: action.end,
        initialPosition: null
      });

    case 'SELECT_BLOCK':
      if (action.clientId === state.start && action.clientId === state.end) {
        return state;
      }

      return Object(objectSpread["a" /* default */])({}, state, {
        start: action.clientId,
        end: action.clientId,
        initialPosition: action.initialPosition
      });

    case 'INSERT_BLOCKS':
      {
        if (action.updateSelection) {
          return Object(objectSpread["a" /* default */])({}, state, {
            start: action.blocks[0].clientId,
            end: action.blocks[0].clientId,
            initialPosition: null,
            isMultiSelecting: false
          });
        }

        return state;
      }

    case 'REMOVE_BLOCKS':
      if (!action.clientIds || !action.clientIds.length || action.clientIds.indexOf(state.start) === -1) {
        return state;
      }

      return Object(objectSpread["a" /* default */])({}, state, {
        start: null,
        end: null,
        initialPosition: null,
        isMultiSelecting: false
      });

    case 'REPLACE_BLOCKS':
      if (action.clientIds.indexOf(state.start) === -1) {
        return state;
      } // If there are replacement blocks, assign last block as the next
      // selected block, otherwise set to null.


      var lastBlock = Object(external_lodash_["last"])(action.blocks);
      var nextSelectedBlockClientId = lastBlock ? lastBlock.clientId : null;

      if (nextSelectedBlockClientId === state.start && nextSelectedBlockClientId === state.end) {
        return state;
      }

      return Object(objectSpread["a" /* default */])({}, state, {
        start: nextSelectedBlockClientId,
        end: nextSelectedBlockClientId,
        initialPosition: null,
        isMultiSelecting: false
      });

    case 'TOGGLE_SELECTION':
      return Object(objectSpread["a" /* default */])({}, state, {
        isEnabled: action.isSelectionEnabled
      });
  }

  return state;
}
function blocksMode() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  if (action.type === 'TOGGLE_BLOCK_MODE') {
    var clientId = action.clientId;
    return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, clientId, state[clientId] && state[clientId] === 'html' ? 'visual' : 'html'));
  }

  return state;
}
/**
 * Reducer returning the block insertion point visibility, either null if there
 * is not an explicit insertion point assigned, or an object of its `index` and
 * `rootClientId`.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function insertionPoint() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SHOW_INSERTION_POINT':
      var rootClientId = action.rootClientId,
          index = action.index;
      return {
        rootClientId: rootClientId,
        index: index
      };

    case 'HIDE_INSERTION_POINT':
      return null;
  }

  return state;
}
/**
 * Reducer returning whether the post blocks match the defined template or not.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {boolean} Updated state.
 */

function reducer_template() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
    isValid: true
  };
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_TEMPLATE_VALIDITY':
      return Object(objectSpread["a" /* default */])({}, state, {
        isValid: action.isValid
      });
  }

  return state;
}
/**
 * Reducer returning the editor setting.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function settings() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : SETTINGS_DEFAULTS;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'UPDATE_SETTINGS':
      return Object(objectSpread["a" /* default */])({}, state, action.settings);
  }

  return state;
}
/**
 * Reducer returning the user preferences.
 *
 * @param {Object}  state                 Current state.
 * @param {Object}  action                Dispatched action.
 *
 * @return {string} Updated state.
 */

function preferences() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : PREFERENCES_DEFAULTS;
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'INSERT_BLOCKS':
    case 'REPLACE_BLOCKS':
      return action.blocks.reduce(function (prevState, block) {
        var id = block.name;
        var insert = {
          name: block.name
        };

        if (Object(external_this_wp_blocks_["isReusableBlock"])(block)) {
          insert.ref = block.attributes.ref;
          id += '/' + block.attributes.ref;
        }

        return Object(objectSpread["a" /* default */])({}, prevState, {
          insertUsage: Object(objectSpread["a" /* default */])({}, prevState.insertUsage, Object(defineProperty["a" /* default */])({}, id, {
            time: action.time,
            count: prevState.insertUsage[id] ? prevState.insertUsage[id].count + 1 : 1,
            insert: insert
          }))
        });
      }, state);
  }

  return state;
}
/**
 * Reducer returning an object where each key is a block client ID, its value
 * representing the settings for its nested blocks.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

var reducer_blockListSettings = function blockListSettings() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    // Even if the replaced blocks have the same client ID, our logic
    // should correct the state.
    case 'REPLACE_BLOCKS':
    case 'REMOVE_BLOCKS':
      {
        return Object(external_lodash_["omit"])(state, action.clientIds);
      }

    case 'UPDATE_BLOCK_LIST_SETTINGS':
      {
        var clientId = action.clientId;

        if (!action.settings) {
          if (state.hasOwnProperty(clientId)) {
            return Object(external_lodash_["omit"])(state, clientId);
          }

          return state;
        }

        if (Object(external_lodash_["isEqual"])(state[clientId], action.settings)) {
          return state;
        }

        return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, clientId, action.settings));
      }
  }

  return state;
};
/* harmony default export */ var store_reducer = (Object(external_this_wp_data_["combineReducers"])({
  blocks: reducer_blocks,
  isTyping: isTyping,
  isCaretWithinFormattedText: isCaretWithinFormattedText,
  blockSelection: blockSelection,
  blocksMode: blocksMode,
  blockListSettings: reducer_blockListSettings,
  insertionPoint: insertionPoint,
  template: reducer_template,
  settings: settings,
  preferences: preferences
}));

// EXTERNAL MODULE: ./node_modules/refx/refx.js
var refx = __webpack_require__(64);
var refx_default = /*#__PURE__*/__webpack_require__.n(refx);

// EXTERNAL MODULE: ./node_modules/redux-multi/lib/index.js
var lib = __webpack_require__(87);
var lib_default = /*#__PURE__*/__webpack_require__.n(lib);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(25);

// EXTERNAL MODULE: external {"this":["wp","a11y"]}
var external_this_wp_a11y_ = __webpack_require__(44);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-editor/build-module/store/controls.js


/**
 * WordPress dependencies
 */

/**
 * Calls a selector using the current state.
 *
 * @param {string} storeName    Store name.
 * @param {string} selectorName Selector name.
 * @param  {Array} args         Selector arguments.
 *
 * @return {Object} control descriptor.
 */

function controls_select(storeName, selectorName) {
  for (var _len = arguments.length, args = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
    args[_key - 2] = arguments[_key];
  }

  return {
    type: 'SELECT',
    storeName: storeName,
    selectorName: selectorName,
    args: args
  };
}
var controls = {
  SELECT: Object(external_this_wp_data_["createRegistryControl"])(function (registry) {
    return function (_ref) {
      var _registry$select;

      var storeName = _ref.storeName,
          selectorName = _ref.selectorName,
          args = _ref.args;
      return (_registry$select = registry.select(storeName))[selectorName].apply(_registry$select, Object(toConsumableArray["a" /* default */])(args));
    };
  })
};
/* harmony default export */ var store_controls = (controls);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-editor/build-module/store/actions.js
var _marked =
/*#__PURE__*/
regeneratorRuntime.mark(selectPreviousBlock),
    _marked2 =
/*#__PURE__*/
regeneratorRuntime.mark(selectNextBlock),
    _marked3 =
/*#__PURE__*/
regeneratorRuntime.mark(removeBlocks);

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
 * Returns an action object used in signalling that blocks state should be
 * reset to the specified array of blocks, taking precedence over any other
 * content reflected as an edit in state.
 *
 * @param {Array} blocks Array of blocks.
 *
 * @return {Object} Action object.
 */

function resetBlocks(blocks) {
  return {
    type: 'RESET_BLOCKS',
    blocks: blocks
  };
}
/**
 * Returns an action object used in signalling that blocks have been received.
 * Unlike resetBlocks, these should be appended to the existing known set, not
 * replacing.
 *
 * @param {Object[]} blocks Array of block objects.
 *
 * @return {Object} Action object.
 */

function receiveBlocks(blocks) {
  return {
    type: 'RECEIVE_BLOCKS',
    blocks: blocks
  };
}
/**
 * Returns an action object used in signalling that the block attributes with
 * the specified client ID has been updated.
 *
 * @param {string} clientId   Block client ID.
 * @param {Object} attributes Block attributes to be merged.
 *
 * @return {Object} Action object.
 */

function updateBlockAttributes(clientId, attributes) {
  return {
    type: 'UPDATE_BLOCK_ATTRIBUTES',
    clientId: clientId,
    attributes: attributes
  };
}
/**
 * Returns an action object used in signalling that the block with the
 * specified client ID has been updated.
 *
 * @param {string} clientId Block client ID.
 * @param {Object} updates  Block attributes to be merged.
 *
 * @return {Object} Action object.
 */

function updateBlock(clientId, updates) {
  return {
    type: 'UPDATE_BLOCK',
    clientId: clientId,
    updates: updates
  };
}
/**
 * Returns an action object used in signalling that the block with the
 * specified client ID has been selected, optionally accepting a position
 * value reflecting its selection directionality. An initialPosition of -1
 * reflects a reverse selection.
 *
 * @param {string}  clientId        Block client ID.
 * @param {?number} initialPosition Optional initial position. Pass as -1 to
 *                                  reflect reverse selection.
 *
 * @return {Object} Action object.
 */

function selectBlock(clientId) {
  var initialPosition = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  return {
    type: 'SELECT_BLOCK',
    initialPosition: initialPosition,
    clientId: clientId
  };
}
/**
 * Yields action objects used in signalling that the block preceding the given
 * clientId should be selected.
 *
 * @param {string} clientId Block client ID.
 */

function selectPreviousBlock(clientId) {
  var previousBlockClientId;
  return regeneratorRuntime.wrap(function selectPreviousBlock$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.next = 2;
          return controls_select('core/block-editor', 'getPreviousBlockClientId', clientId);

        case 2:
          previousBlockClientId = _context.sent;
          _context.next = 5;
          return selectBlock(previousBlockClientId, -1);

        case 5:
        case "end":
          return _context.stop();
      }
    }
  }, _marked, this);
}
/**
 * Yields action objects used in signalling that the block following the given
 * clientId should be selected.
 *
 * @param {string} clientId Block client ID.
 */

function selectNextBlock(clientId) {
  var nextBlockClientId;
  return regeneratorRuntime.wrap(function selectNextBlock$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          _context2.next = 2;
          return controls_select('core/block-editor', 'getNextBlockClientId', clientId);

        case 2:
          nextBlockClientId = _context2.sent;
          _context2.next = 5;
          return selectBlock(nextBlockClientId);

        case 5:
        case "end":
          return _context2.stop();
      }
    }
  }, _marked2, this);
}
/**
 * Returns an action object used in signalling that a block multi-selection has started.
 *
 * @return {Object} Action object.
 */

function startMultiSelect() {
  return {
    type: 'START_MULTI_SELECT'
  };
}
/**
 * Returns an action object used in signalling that block multi-selection stopped.
 *
 * @return {Object} Action object.
 */

function stopMultiSelect() {
  return {
    type: 'STOP_MULTI_SELECT'
  };
}
/**
 * Returns an action object used in signalling that block multi-selection changed.
 *
 * @param {string} start First block of the multi selection.
 * @param {string} end   Last block of the multiselection.
 *
 * @return {Object} Action object.
 */

function multiSelect(start, end) {
  return {
    type: 'MULTI_SELECT',
    start: start,
    end: end
  };
}
/**
 * Returns an action object used in signalling that the block selection is cleared.
 *
 * @return {Object} Action object.
 */

function clearSelectedBlock() {
  return {
    type: 'CLEAR_SELECTED_BLOCK'
  };
}
/**
 * Returns an action object that enables or disables block selection.
 *
 * @param {boolean} [isSelectionEnabled=true] Whether block selection should
 *                                            be enabled.

 * @return {Object} Action object.
 */

function toggleSelection() {
  var isSelectionEnabled = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
  return {
    type: 'TOGGLE_SELECTION',
    isSelectionEnabled: isSelectionEnabled
  };
}
/**
 * Returns an action object signalling that a blocks should be replaced with
 * one or more replacement blocks.
 *
 * @param {(string|string[])} clientIds Block client ID(s) to replace.
 * @param {(Object|Object[])} blocks    Replacement block(s).
 *
 * @return {Object} Action object.
 */

function replaceBlocks(clientIds, blocks) {
  return {
    type: 'REPLACE_BLOCKS',
    clientIds: Object(external_lodash_["castArray"])(clientIds),
    blocks: Object(external_lodash_["castArray"])(blocks),
    time: Date.now()
  };
}
/**
 * Returns an action object signalling that a single block should be replaced
 * with one or more replacement blocks.
 *
 * @param {(string|string[])} clientId Block client ID to replace.
 * @param {(Object|Object[])} block    Replacement block(s).
 *
 * @return {Object} Action object.
 */

function replaceBlock(clientId, block) {
  return replaceBlocks(clientId, block);
}
/**
 * Higher-order action creator which, given the action type to dispatch creates
 * an action creator for managing block movement.
 *
 * @param {string} type Action type to dispatch.
 *
 * @return {Function} Action creator.
 */

function createOnMove(type) {
  return function (clientIds, rootClientId) {
    return {
      clientIds: Object(external_lodash_["castArray"])(clientIds),
      type: type,
      rootClientId: rootClientId
    };
  };
}

var moveBlocksDown = createOnMove('MOVE_BLOCKS_DOWN');
var moveBlocksUp = createOnMove('MOVE_BLOCKS_UP');
/**
 * Returns an action object signalling that an indexed block should be moved
 * to a new index.
 *
 * @param  {?string} clientId         The client ID of the block.
 * @param  {?string} fromRootClientId Root client ID source.
 * @param  {?string} toRootClientId   Root client ID destination.
 * @param  {number}  index            The index to move the block into.
 *
 * @return {Object} Action object.
 */

function moveBlockToPosition(clientId, fromRootClientId, toRootClientId, index) {
  return {
    type: 'MOVE_BLOCK_TO_POSITION',
    fromRootClientId: fromRootClientId,
    toRootClientId: toRootClientId,
    clientId: clientId,
    index: index
  };
}
/**
 * Returns an action object used in signalling that a single block should be
 * inserted, optionally at a specific index respective a root block list.
 *
 * @param {Object}  block            Block object to insert.
 * @param {?number} index            Index at which block should be inserted.
 * @param {?string} rootClientId     Optional root client ID of block list on which to insert.
 * @param {?boolean} updateSelection If true block selection will be updated. If false, block selection will not change. Defaults to true.
 *
 * @return {Object} Action object.
 */

function insertBlock(block, index, rootClientId) {
  var updateSelection = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : true;
  return insertBlocks([block], index, rootClientId, updateSelection);
}
/**
 * Returns an action object used in signalling that an array of blocks should
 * be inserted, optionally at a specific index respective a root block list.
 *
 * @param {Object[]} blocks          Block objects to insert.
 * @param {?number}  index           Index at which block should be inserted.
 * @param {?string}  rootClientId    Optional root client ID of block list on which to insert.
 * @param {?boolean} updateSelection If true block selection will be updated.  If false, block selection will not change. Defaults to true.
 *
 * @return {Object} Action object.
 */

function insertBlocks(blocks, index, rootClientId) {
  var updateSelection = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : true;
  return {
    type: 'INSERT_BLOCKS',
    blocks: Object(external_lodash_["castArray"])(blocks),
    index: index,
    rootClientId: rootClientId,
    time: Date.now(),
    updateSelection: updateSelection
  };
}
/**
 * Returns an action object used in signalling that the insertion point should
 * be shown.
 *
 * @param {?string} rootClientId Optional root client ID of block list on
 *                               which to insert.
 * @param {?number} index        Index at which block should be inserted.
 *
 * @return {Object} Action object.
 */

function showInsertionPoint(rootClientId, index) {
  return {
    type: 'SHOW_INSERTION_POINT',
    rootClientId: rootClientId,
    index: index
  };
}
/**
 * Returns an action object hiding the insertion point.
 *
 * @return {Object} Action object.
 */

function hideInsertionPoint() {
  return {
    type: 'HIDE_INSERTION_POINT'
  };
}
/**
 * Returns an action object resetting the template validity.
 *
 * @param {boolean}  isValid  template validity flag.
 *
 * @return {Object} Action object.
 */

function setTemplateValidity(isValid) {
  return {
    type: 'SET_TEMPLATE_VALIDITY',
    isValid: isValid
  };
}
/**
 * Returns an action object synchronize the template with the list of blocks
 *
 * @return {Object} Action object.
 */

function synchronizeTemplate() {
  return {
    type: 'SYNCHRONIZE_TEMPLATE'
  };
}
/**
 * Returns an action object used in signalling that two blocks should be merged
 *
 * @param {string} firstBlockClientId  Client ID of the first block to merge.
 * @param {string} secondBlockClientId Client ID of the second block to merge.
 *
 * @return {Object} Action object.
 */

function mergeBlocks(firstBlockClientId, secondBlockClientId) {
  return {
    type: 'MERGE_BLOCKS',
    blocks: [firstBlockClientId, secondBlockClientId]
  };
}
/**
 * Yields action objects used in signalling that the blocks corresponding to
 * the set of specified client IDs are to be removed.
 *
 * @param {string|string[]} clientIds      Client IDs of blocks to remove.
 * @param {boolean}         selectPrevious True if the previous block should be
 *                                         selected when a block is removed.
 */

function removeBlocks(clientIds) {
  var selectPrevious,
      count,
      _args3 = arguments;
  return regeneratorRuntime.wrap(function removeBlocks$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          selectPrevious = _args3.length > 1 && _args3[1] !== undefined ? _args3[1] : true;
          clientIds = Object(external_lodash_["castArray"])(clientIds);

          if (!selectPrevious) {
            _context3.next = 5;
            break;
          }

          _context3.next = 5;
          return selectPreviousBlock(clientIds[0]);

        case 5:
          _context3.next = 7;
          return {
            type: 'REMOVE_BLOCKS',
            clientIds: clientIds
          };

        case 7:
          _context3.next = 9;
          return controls_select('core/block-editor', 'getBlockCount');

        case 9:
          count = _context3.sent;

          if (!(count === 0)) {
            _context3.next = 13;
            break;
          }

          _context3.next = 13;
          return insertDefaultBlock();

        case 13:
        case "end":
          return _context3.stop();
      }
    }
  }, _marked3, this);
}
/**
 * Returns an action object used in signalling that the block with the
 * specified client ID is to be removed.
 *
 * @param {string}  clientId       Client ID of block to remove.
 * @param {boolean} selectPrevious True if the previous block should be
 *                                 selected when a block is removed.
 *
 * @return {Object} Action object.
 */

function removeBlock(clientId, selectPrevious) {
  return removeBlocks([clientId], selectPrevious);
}
/**
 * Returns an action object used to toggle the block editing mode between
 * visual and HTML modes.
 *
 * @param {string} clientId Block client ID.
 *
 * @return {Object} Action object.
 */

function toggleBlockMode(clientId) {
  return {
    type: 'TOGGLE_BLOCK_MODE',
    clientId: clientId
  };
}
/**
 * Returns an action object used in signalling that the user has begun to type.
 *
 * @return {Object} Action object.
 */

function startTyping() {
  return {
    type: 'START_TYPING'
  };
}
/**
 * Returns an action object used in signalling that the user has stopped typing.
 *
 * @return {Object} Action object.
 */

function stopTyping() {
  return {
    type: 'STOP_TYPING'
  };
}
/**
 * Returns an action object used in signalling that the caret has entered formatted text.
 *
 * @return {Object} Action object.
 */

function enterFormattedText() {
  return {
    type: 'ENTER_FORMATTED_TEXT'
  };
}
/**
 * Returns an action object used in signalling that the user caret has exited formatted text.
 *
 * @return {Object} Action object.
 */

function exitFormattedText() {
  return {
    type: 'EXIT_FORMATTED_TEXT'
  };
}
/**
 * Returns an action object used in signalling that a new block of the default
 * type should be added to the block list.
 *
 * @param {?Object} attributes   Optional attributes of the block to assign.
 * @param {?string} rootClientId Optional root client ID of block list on which
 *                               to append.
 * @param {?number} index        Optional index where to insert the default block
 *
 * @return {Object} Action object
 */

function insertDefaultBlock(attributes, rootClientId, index) {
  var block = Object(external_this_wp_blocks_["createBlock"])(Object(external_this_wp_blocks_["getDefaultBlockName"])(), attributes);
  return insertBlock(block, index, rootClientId);
}
/**
 * Returns an action object that changes the nested settings of a given block.
 *
 * @param {string} clientId Client ID of the block whose nested setting are
 *                          being received.
 * @param {Object} settings Object with the new settings for the nested block.
 *
 * @return {Object} Action object
 */

function updateBlockListSettings(clientId, settings) {
  return {
    type: 'UPDATE_BLOCK_LIST_SETTINGS',
    clientId: clientId,
    settings: settings
  };
}
/*
 * Returns an action object used in signalling that the block editor settings have been updated.
 *
 * @param {Object} settings Updated settings
 *
 * @return {Object} Action object
 */

function updateSettings(settings) {
  return {
    type: 'UPDATE_SETTINGS',
    settings: settings
  };
}
/**
 * Returns an action object used in signalling that a temporary reusable blocks have been saved
 * in order to switch its temporary id with the real id.
 *
 * @param {string} id        Reusable block's id.
 * @param {string} updatedId Updated block's id.
 *
 * @return {Object} Action object.
 */

function __unstableSaveReusableBlock(id, updatedId) {
  return {
    type: 'SAVE_REUSABLE_BLOCK_SUCCESS',
    id: id,
    updatedId: updatedId
  };
}
/**
 * Returns an action object used in signalling that the last block change should be marked explicitely as persistent.
 *
 * @return {Object} Action object.
 */

function __unstableMarkLastChangeAsPersistent() {
  return {
    type: 'MARK_LAST_CHANGE_AS_PERSISTENT'
  };
}

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__(28);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-editor/build-module/store/selectors.js



/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/***
 * Module constants
 */

var INSERTER_UTILITY_HIGH = 3;
var INSERTER_UTILITY_MEDIUM = 2;
var INSERTER_UTILITY_LOW = 1;
var INSERTER_UTILITY_NONE = 0;
var MILLISECONDS_PER_HOUR = 3600 * 1000;
var MILLISECONDS_PER_DAY = 24 * 3600 * 1000;
var MILLISECONDS_PER_WEEK = 7 * 24 * 3600 * 1000;
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
 * Shared reference to an empty object for cases where it is important to avoid
 * returning a new object reference on every invocation.
 *
 * @type {Object}
 */

var EMPTY_OBJECT = {};
/**
 * Returns a new reference when the inner blocks of a given block client ID
 * change. This is used exclusively as a memoized selector dependant, relying
 * on this selector's shared return value and recursively those of its inner
 * blocks defined as dependencies. This abuses mechanics of the selector
 * memoization to return from the original selector function only when
 * dependants change.
 *
 * @param {Object} state    Editor state.
 * @param {string} clientId Block client ID.
 *
 * @return {*} A value whose reference will change only when inner blocks of
 *             the given block client ID change.
 */

var getBlockDependantsCacheBust = Object(rememo["a" /* default */])(function () {
  return [];
}, function (state, clientId) {
  return Object(external_lodash_["map"])(getBlockOrder(state, clientId), function (innerBlockClientId) {
    return getBlock(state, innerBlockClientId);
  });
});
/**
 * Returns a block's name given its client ID, or null if no block exists with
 * the client ID.
 *
 * @param {Object} state    Editor state.
 * @param {string} clientId Block client ID.
 *
 * @return {string} Block name.
 */

function getBlockName(state, clientId) {
  var block = state.blocks.byClientId[clientId];
  return block ? block.name : null;
}
/**
 * Returns whether a block is valid or not.
 *
 * @param {Object} state    Editor state.
 * @param {string} clientId Block client ID.
 *
 * @return {boolean} Is Valid.
 */

function isBlockValid(state, clientId) {
  var block = state.blocks.byClientId[clientId];
  return !!block && block.isValid;
}
/**
 * Returns a block's attributes given its client ID, or null if no block exists with
 * the client ID.
 *
 * @param {Object} state    Editor state.
 * @param {string} clientId Block client ID.
 *
 * @return {Object?} Block attributes.
 */

var getBlockAttributes = Object(rememo["a" /* default */])(function (state, clientId) {
  var block = state.blocks.byClientId[clientId];

  if (!block) {
    return null;
  }

  var attributes = state.blocks.attributes[clientId]; // Inject custom source attribute values.
  //
  // TODO: Create generic external sourcing pattern, not explicitly
  // targeting meta attributes.

  var type = Object(external_this_wp_blocks_["getBlockType"])(block.name);

  if (type) {
    attributes = Object(external_lodash_["reduce"])(type.attributes, function (result, value, key) {
      if (value.source === 'meta') {
        if (result === attributes) {
          result = Object(objectSpread["a" /* default */])({}, result);
        }

        result[key] = getPostMeta(state, value.meta);
      }

      return result;
    }, attributes);
  }

  return attributes;
}, function (state, clientId) {
  return [state.blocks.byClientId[clientId], state.blocks.attributes[clientId], getPostMeta(state)];
});
/**
 * Returns a block given its client ID. This is a parsed copy of the block,
 * containing its `blockName`, `clientId`, and current `attributes` state. This
 * is not the block's registration settings, which must be retrieved from the
 * blocks module registration store.
 *
 * @param {Object} state    Editor state.
 * @param {string} clientId Block client ID.
 *
 * @return {Object} Parsed block object.
 */

var getBlock = Object(rememo["a" /* default */])(function (state, clientId) {
  var block = state.blocks.byClientId[clientId];

  if (!block) {
    return null;
  }

  return Object(objectSpread["a" /* default */])({}, block, {
    attributes: getBlockAttributes(state, clientId),
    innerBlocks: getBlocks(state, clientId)
  });
}, function (state, clientId) {
  return [].concat(Object(toConsumableArray["a" /* default */])(getBlockAttributes.getDependants(state, clientId)), [getBlockDependantsCacheBust(state, clientId)]);
});
var __unstableGetBlockWithoutInnerBlocks = Object(rememo["a" /* default */])(function (state, clientId) {
  var block = state.blocks.byClientId[clientId];

  if (!block) {
    return null;
  }

  return Object(objectSpread["a" /* default */])({}, block, {
    attributes: getBlockAttributes(state, clientId)
  });
}, function (state, clientId) {
  return [state.blocks.byClientId[clientId]].concat(Object(toConsumableArray["a" /* default */])(getBlockAttributes.getDependants(state, clientId)));
});
/**
 * Returns all block objects for the current post being edited as an array in
 * the order they appear in the post.
 *
 * Note: It's important to memoize this selector to avoid return a new instance
 * on each call
 *
 * @param {Object}  state        Editor state.
 * @param {?String} rootClientId Optional root client ID of block list.
 *
 * @return {Object[]} Post blocks.
 */

var getBlocks = Object(rememo["a" /* default */])(function (state, rootClientId) {
  return Object(external_lodash_["map"])(getBlockOrder(state, rootClientId), function (clientId) {
    return getBlock(state, clientId);
  });
}, function (state) {
  return [state.blocks.byClientId, state.blocks.order, state.blocks.attributes];
});
/**
 * Returns an array containing the clientIds of all descendants
 * of the blocks given.
 *
 * @param {Object} state Global application state.
 * @param {Array} clientIds Array of blocks to inspect.
 *
 * @return {Array} ids of descendants.
 */

var selectors_getClientIdsOfDescendants = function getClientIdsOfDescendants(state, clientIds) {
  return Object(external_lodash_["flatMap"])(clientIds, function (clientId) {
    var descendants = getBlockOrder(state, clientId);
    return [].concat(Object(toConsumableArray["a" /* default */])(descendants), Object(toConsumableArray["a" /* default */])(getClientIdsOfDescendants(state, descendants)));
  });
};
/**
 * Returns an array containing the clientIds of the top-level blocks
 * and their descendants of any depth (for nested blocks).
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} ids of top-level and descendant blocks.
 */

var getClientIdsWithDescendants = Object(rememo["a" /* default */])(function (state) {
  var topLevelIds = getBlockOrder(state);
  return [].concat(Object(toConsumableArray["a" /* default */])(topLevelIds), Object(toConsumableArray["a" /* default */])(selectors_getClientIdsOfDescendants(state, topLevelIds)));
}, function (state) {
  return [state.blocks.order];
});
/**
 * Returns the total number of blocks, or the total number of blocks with a specific name in a post.
 * The number returned includes nested blocks.
 *
 * @param {Object}  state     Global application state.
 * @param {?String} blockName Optional block name, if specified only blocks of that type will be counted.
 *
 * @return {number} Number of blocks in the post, or number of blocks with name equal to blockName.
 */

var getGlobalBlockCount = Object(rememo["a" /* default */])(function (state, blockName) {
  var clientIds = getClientIdsWithDescendants(state);

  if (!blockName) {
    return clientIds.length;
  }

  return Object(external_lodash_["reduce"])(clientIds, function (count, clientId) {
    var block = state.blocks.byClientId[clientId];
    return block.name === blockName ? count + 1 : count;
  }, 0);
}, function (state) {
  return [state.blocks.order, state.blocks.byClientId];
});
/**
 * Given an array of block client IDs, returns the corresponding array of block
 * objects.
 *
 * @param {Object}   state     Editor state.
 * @param {string[]} clientIds Client IDs for which blocks are to be returned.
 *
 * @return {WPBlock[]} Block objects.
 */

var getBlocksByClientId = Object(rememo["a" /* default */])(function (state, clientIds) {
  return Object(external_lodash_["map"])(Object(external_lodash_["castArray"])(clientIds), function (clientId) {
    return getBlock(state, clientId);
  });
}, function (state) {
  return [getPostMeta(state), state.blocks.byClientId, state.blocks.order, state.blocks.attributes];
});
/**
 * Returns the number of blocks currently present in the post.
 *
 * @param {Object}  state        Editor state.
 * @param {?string} rootClientId Optional root client ID of block list.
 *
 * @return {number} Number of blocks in the post.
 */

function getBlockCount(state, rootClientId) {
  return getBlockOrder(state, rootClientId).length;
}
/**
 * Returns the current block selection start. This value may be null, and it
 * may represent either a singular block selection or multi-selection start.
 * A selection is singular if its start and end match.
 *
 * @param {Object} state Global application state.
 *
 * @return {?string} Client ID of block selection start.
 */

function getBlockSelectionStart(state) {
  return state.blockSelection.start;
}
/**
 * Returns the current block selection end. This value may be null, and it
 * may represent either a singular block selection or multi-selection end.
 * A selection is singular if its start and end match.
 *
 * @param {Object} state Global application state.
 *
 * @return {?string} Client ID of block selection end.
 */

function getBlockSelectionEnd(state) {
  return state.blockSelection.end;
}
/**
 * Returns the number of blocks currently selected in the post.
 *
 * @param {Object} state Global application state.
 *
 * @return {number} Number of blocks selected in the post.
 */

function getSelectedBlockCount(state) {
  var multiSelectedBlockCount = getMultiSelectedBlockClientIds(state).length;

  if (multiSelectedBlockCount) {
    return multiSelectedBlockCount;
  }

  return state.blockSelection.start ? 1 : 0;
}
/**
 * Returns true if there is a single selected block, or false otherwise.
 *
 * @param {Object} state Editor state.
 *
 * @return {boolean} Whether a single block is selected.
 */

function hasSelectedBlock(state) {
  var _state$blockSelection = state.blockSelection,
      start = _state$blockSelection.start,
      end = _state$blockSelection.end;
  return !!start && start === end;
}
/**
 * Returns the currently selected block client ID, or null if there is no
 * selected block.
 *
 * @param {Object} state Editor state.
 *
 * @return {?string} Selected block client ID.
 */

function getSelectedBlockClientId(state) {
  var _state$blockSelection2 = state.blockSelection,
      start = _state$blockSelection2.start,
      end = _state$blockSelection2.end; // We need to check the block exists because the current blockSelection
  // reducer doesn't take into account when blocks are reset via undo. To be
  // removed when that's fixed.

  return start && start === end && !!state.blocks.byClientId[start] ? start : null;
}
/**
 * Returns the currently selected block, or null if there is no selected block.
 *
 * @param {Object} state Global application state.
 *
 * @return {?Object} Selected block.
 */

function getSelectedBlock(state) {
  var clientId = getSelectedBlockClientId(state);
  return clientId ? getBlock(state, clientId) : null;
}
/**
 * Given a block client ID, returns the root block from which the block is
 * nested, an empty string for top-level blocks, or null if the block does not
 * exist.
 *
 * @param {Object} state    Editor state.
 * @param {string} clientId Block from which to find root client ID.
 *
 * @return {?string} Root client ID, if exists
 */

var getBlockRootClientId = Object(rememo["a" /* default */])(function (state, clientId) {
  var order = state.blocks.order;

  for (var rootClientId in order) {
    if (Object(external_lodash_["includes"])(order[rootClientId], clientId)) {
      return rootClientId;
    }
  }

  return null;
}, function (state) {
  return [state.blocks.order];
});
/**
 * Given a block client ID, returns the root of the hierarchy from which the block is nested, return the block itself for root level blocks.
 *
 * @param {Object} state    Editor state.
 * @param {string} clientId Block from which to find root client ID.
 *
 * @return {string} Root client ID
 */

var getBlockHierarchyRootClientId = Object(rememo["a" /* default */])(function (state, clientId) {
  var rootClientId = clientId;
  var current = clientId;

  while (rootClientId) {
    current = rootClientId;
    rootClientId = getBlockRootClientId(state, current);
  }

  return current;
}, function (state) {
  return [state.blocks.order];
});
/**
 * Returns the client ID of the block adjacent one at the given reference
 * startClientId and modifier directionality. Defaults start startClientId to
 * the selected block, and direction as next block. Returns null if there is no
 * adjacent block.
 *
 * @param {Object}  state         Editor state.
 * @param {?string} startClientId Optional client ID of block from which to
 *                                search.
 * @param {?number} modifier      Directionality multiplier (1 next, -1
 *                                previous).
 *
 * @return {?string} Return the client ID of the block, or null if none exists.
 */

function getAdjacentBlockClientId(state, startClientId) {
  var modifier = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 1;

  // Default to selected block.
  if (startClientId === undefined) {
    startClientId = getSelectedBlockClientId(state);
  } // Try multi-selection starting at extent based on modifier.


  if (startClientId === undefined) {
    if (modifier < 0) {
      startClientId = getFirstMultiSelectedBlockClientId(state);
    } else {
      startClientId = getLastMultiSelectedBlockClientId(state);
    }
  } // Validate working start client ID.


  if (!startClientId) {
    return null;
  } // Retrieve start block root client ID, being careful to allow the falsey
  // empty string top-level root by explicitly testing against null.


  var rootClientId = getBlockRootClientId(state, startClientId);

  if (rootClientId === null) {
    return null;
  }

  var order = state.blocks.order;
  var orderSet = order[rootClientId];
  var index = orderSet.indexOf(startClientId);
  var nextIndex = index + 1 * modifier; // Block was first in set and we're attempting to get previous.

  if (nextIndex < 0) {
    return null;
  } // Block was last in set and we're attempting to get next.


  if (nextIndex === orderSet.length) {
    return null;
  } // Assume incremented index is within the set.


  return orderSet[nextIndex];
}
/**
 * Returns the previous block's client ID from the given reference start ID.
 * Defaults start to the selected block. Returns null if there is no previous
 * block.
 *
 * @param {Object}  state         Editor state.
 * @param {?string} startClientId Optional client ID of block from which to
 *                                search.
 *
 * @return {?string} Adjacent block's client ID, or null if none exists.
 */

function getPreviousBlockClientId(state, startClientId) {
  return getAdjacentBlockClientId(state, startClientId, -1);
}
/**
 * Returns the next block's client ID from the given reference start ID.
 * Defaults start to the selected block. Returns null if there is no next
 * block.
 *
 * @param {Object}  state         Editor state.
 * @param {?string} startClientId Optional client ID of block from which to
 *                                search.
 *
 * @return {?string} Adjacent block's client ID, or null if none exists.
 */

function getNextBlockClientId(state, startClientId) {
  return getAdjacentBlockClientId(state, startClientId, 1);
}
/**
 * Returns the initial caret position for the selected block.
 * This position is to used to position the caret properly when the selected block changes.
 *
 * @param {Object} state Global application state.
 *
 * @return {?Object} Selected block.
 */

function getSelectedBlocksInitialCaretPosition(state) {
  var _state$blockSelection3 = state.blockSelection,
      start = _state$blockSelection3.start,
      end = _state$blockSelection3.end;

  if (start !== end || !start) {
    return null;
  }

  return state.blockSelection.initialPosition;
}
/**
 * Returns the current multi-selection set of block client IDs, or an empty
 * array if there is no multi-selection.
 *
 * @param {Object} state Editor state.
 *
 * @return {Array} Multi-selected block client IDs.
 */

var getMultiSelectedBlockClientIds = Object(rememo["a" /* default */])(function (state) {
  var _state$blockSelection4 = state.blockSelection,
      start = _state$blockSelection4.start,
      end = _state$blockSelection4.end;

  if (start === end) {
    return [];
  } // Retrieve root client ID to aid in retrieving relevant nested block
  // order, being careful to allow the falsey empty string top-level root
  // by explicitly testing against null.


  var rootClientId = getBlockRootClientId(state, start);

  if (rootClientId === null) {
    return [];
  }

  var blockOrder = getBlockOrder(state, rootClientId);
  var startIndex = blockOrder.indexOf(start);
  var endIndex = blockOrder.indexOf(end);

  if (startIndex > endIndex) {
    return blockOrder.slice(endIndex, startIndex + 1);
  }

  return blockOrder.slice(startIndex, endIndex + 1);
}, function (state) {
  return [state.blocks.order, state.blockSelection.start, state.blockSelection.end];
});
/**
 * Returns the current multi-selection set of blocks, or an empty array if
 * there is no multi-selection.
 *
 * @param {Object} state Editor state.
 *
 * @return {Array} Multi-selected block objects.
 */

var getMultiSelectedBlocks = Object(rememo["a" /* default */])(function (state) {
  var multiSelectedBlockClientIds = getMultiSelectedBlockClientIds(state);

  if (!multiSelectedBlockClientIds.length) {
    return EMPTY_ARRAY;
  }

  return multiSelectedBlockClientIds.map(function (clientId) {
    return getBlock(state, clientId);
  });
}, function (state) {
  return [].concat(Object(toConsumableArray["a" /* default */])(getMultiSelectedBlockClientIds.getDependants(state)), [state.blocks.byClientId, state.blocks.order, state.blocks.attributes, getPostMeta(state)]);
});
/**
 * Returns the client ID of the first block in the multi-selection set, or null
 * if there is no multi-selection.
 *
 * @param {Object} state Editor state.
 *
 * @return {?string} First block client ID in the multi-selection set.
 */

function getFirstMultiSelectedBlockClientId(state) {
  return Object(external_lodash_["first"])(getMultiSelectedBlockClientIds(state)) || null;
}
/**
 * Returns the client ID of the last block in the multi-selection set, or null
 * if there is no multi-selection.
 *
 * @param {Object} state Editor state.
 *
 * @return {?string} Last block client ID in the multi-selection set.
 */

function getLastMultiSelectedBlockClientId(state) {
  return Object(external_lodash_["last"])(getMultiSelectedBlockClientIds(state)) || null;
}
/**
 * Checks if possibleAncestorId is an ancestor of possibleDescendentId.
 *
 * @param {Object} state                Editor state.
 * @param {string} possibleAncestorId   Possible ancestor client ID.
 * @param {string} possibleDescendentId Possible descent client ID.
 *
 * @return {boolean} True if possibleAncestorId is an ancestor
 *                   of possibleDescendentId, and false otherwise.
 */

var isAncestorOf = Object(rememo["a" /* default */])(function (state, possibleAncestorId, possibleDescendentId) {
  var idToCheck = possibleDescendentId;

  while (possibleAncestorId !== idToCheck && idToCheck) {
    idToCheck = getBlockRootClientId(state, idToCheck);
  }

  return possibleAncestorId === idToCheck;
}, function (state) {
  return [state.blocks.order];
});
/**
 * Returns true if a multi-selection exists, and the block corresponding to the
 * specified client ID is the first block of the multi-selection set, or false
 * otherwise.
 *
 * @param {Object} state    Editor state.
 * @param {string} clientId Block client ID.
 *
 * @return {boolean} Whether block is first in multi-selection.
 */

function isFirstMultiSelectedBlock(state, clientId) {
  return getFirstMultiSelectedBlockClientId(state) === clientId;
}
/**
 * Returns true if the client ID occurs within the block multi-selection, or
 * false otherwise.
 *
 * @param {Object} state    Editor state.
 * @param {string} clientId Block client ID.
 *
 * @return {boolean} Whether block is in multi-selection set.
 */

function isBlockMultiSelected(state, clientId) {
  return getMultiSelectedBlockClientIds(state).indexOf(clientId) !== -1;
}
/**
 * Returns true if an ancestor of the block is multi-selected, or false
 * otherwise.
 *
 * @param {Object} state    Editor state.
 * @param {string} clientId Block client ID.
 *
 * @return {boolean} Whether an ancestor of the block is in multi-selection
 *                   set.
 */

var isAncestorMultiSelected = Object(rememo["a" /* default */])(function (state, clientId) {
  var ancestorClientId = clientId;
  var isMultiSelected = false;

  while (ancestorClientId && !isMultiSelected) {
    ancestorClientId = getBlockRootClientId(state, ancestorClientId);
    isMultiSelected = isBlockMultiSelected(state, ancestorClientId);
  }

  return isMultiSelected;
}, function (state) {
  return [state.blocks.order, state.blockSelection.start, state.blockSelection.end];
});
/**
 * Returns the client ID of the block which begins the multi-selection set, or
 * null if there is no multi-selection.
 *
 * This is not necessarily the first client ID in the selection.
 *
 * @see getFirstMultiSelectedBlockClientId
 *
 * @param {Object} state Editor state.
 *
 * @return {?string} Client ID of block beginning multi-selection.
 */

function getMultiSelectedBlocksStartClientId(state) {
  var _state$blockSelection5 = state.blockSelection,
      start = _state$blockSelection5.start,
      end = _state$blockSelection5.end;

  if (start === end) {
    return null;
  }

  return start || null;
}
/**
 * Returns the client ID of the block which ends the multi-selection set, or
 * null if there is no multi-selection.
 *
 * This is not necessarily the last client ID in the selection.
 *
 * @see getLastMultiSelectedBlockClientId
 *
 * @param {Object} state Editor state.
 *
 * @return {?string} Client ID of block ending multi-selection.
 */

function getMultiSelectedBlocksEndClientId(state) {
  var _state$blockSelection6 = state.blockSelection,
      start = _state$blockSelection6.start,
      end = _state$blockSelection6.end;

  if (start === end) {
    return null;
  }

  return end || null;
}
/**
 * Returns an array containing all block client IDs in the editor in the order
 * they appear. Optionally accepts a root client ID of the block list for which
 * the order should be returned, defaulting to the top-level block order.
 *
 * @param {Object}  state        Editor state.
 * @param {?string} rootClientId Optional root client ID of block list.
 *
 * @return {Array} Ordered client IDs of editor blocks.
 */

function getBlockOrder(state, rootClientId) {
  return state.blocks.order[rootClientId || ''] || EMPTY_ARRAY;
}
/**
 * Returns the index at which the block corresponding to the specified client
 * ID occurs within the block order, or `-1` if the block does not exist.
 *
 * @param {Object}  state        Editor state.
 * @param {string}  clientId     Block client ID.
 * @param {?string} rootClientId Optional root client ID of block list.
 *
 * @return {number} Index at which block exists in order.
 */

function getBlockIndex(state, clientId, rootClientId) {
  return getBlockOrder(state, rootClientId).indexOf(clientId);
}
/**
 * Returns true if the block corresponding to the specified client ID is
 * currently selected and no multi-selection exists, or false otherwise.
 *
 * @param {Object} state    Editor state.
 * @param {string} clientId Block client ID.
 *
 * @return {boolean} Whether block is selected and multi-selection exists.
 */

function isBlockSelected(state, clientId) {
  var _state$blockSelection7 = state.blockSelection,
      start = _state$blockSelection7.start,
      end = _state$blockSelection7.end;

  if (start !== end) {
    return false;
  }

  return start === clientId;
}
/**
 * Returns true if one of the block's inner blocks is selected.
 *
 * @param {Object}  state    Editor state.
 * @param {string}  clientId Block client ID.
 * @param {boolean} deep     Perform a deep check.
 *
 * @return {boolean} Whether the block as an inner block selected
 */

function hasSelectedInnerBlock(state, clientId) {
  var deep = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  return Object(external_lodash_["some"])(getBlockOrder(state, clientId), function (innerClientId) {
    return isBlockSelected(state, innerClientId) || isBlockMultiSelected(state, innerClientId) || deep && hasSelectedInnerBlock(state, innerClientId, deep);
  });
}
/**
 * Returns true if the block corresponding to the specified client ID is
 * currently selected but isn't the last of the selected blocks. Here "last"
 * refers to the block sequence in the document, _not_ the sequence of
 * multi-selection, which is why `state.blockSelection.end` isn't used.
 *
 * @param {Object} state    Editor state.
 * @param {string} clientId Block client ID.
 *
 * @return {boolean} Whether block is selected and not the last in the
 *                   selection.
 */

function isBlockWithinSelection(state, clientId) {
  if (!clientId) {
    return false;
  }

  var clientIds = getMultiSelectedBlockClientIds(state);
  var index = clientIds.indexOf(clientId);
  return index > -1 && index < clientIds.length - 1;
}
/**
 * Returns true if a multi-selection has been made, or false otherwise.
 *
 * @param {Object} state Editor state.
 *
 * @return {boolean} Whether multi-selection has been made.
 */

function hasMultiSelection(state) {
  var _state$blockSelection8 = state.blockSelection,
      start = _state$blockSelection8.start,
      end = _state$blockSelection8.end;
  return start !== end;
}
/**
 * Whether in the process of multi-selecting or not. This flag is only true
 * while the multi-selection is being selected (by mouse move), and is false
 * once the multi-selection has been settled.
 *
 * @see hasMultiSelection
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} True if multi-selecting, false if not.
 */

function isMultiSelecting(state) {
  return state.blockSelection.isMultiSelecting;
}
/**
 * Selector that returns if multi-selection is enabled or not.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} True if it should be possible to multi-select blocks, false if multi-selection is disabled.
 */

function isSelectionEnabled(state) {
  return state.blockSelection.isEnabled;
}
/**
 * Returns the block's editing mode, defaulting to "visual" if not explicitly
 * assigned.
 *
 * @param {Object} state    Editor state.
 * @param {string} clientId Block client ID.
 *
 * @return {Object} Block editing mode.
 */

function getBlockMode(state, clientId) {
  return state.blocksMode[clientId] || 'visual';
}
/**
 * Returns true if the user is typing, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether user is typing.
 */

function selectors_isTyping(state) {
  return state.isTyping;
}
/**
 * Returns true if the caret is within formatted text, or false otherwise.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the caret is within formatted text.
 */

function selectors_isCaretWithinFormattedText(state) {
  return state.isCaretWithinFormattedText;
}
/**
 * Returns the insertion point, the index at which the new inserted block would
 * be placed. Defaults to the last index.
 *
 * @param {Object} state Editor state.
 *
 * @return {Object} Insertion point object with `rootClientId`, `index`.
 */

function getBlockInsertionPoint(state) {
  var rootClientId, index;
  var insertionPoint = state.insertionPoint,
      blockSelection = state.blockSelection;

  if (insertionPoint !== null) {
    return insertionPoint;
  }

  var end = blockSelection.end;

  if (end) {
    rootClientId = getBlockRootClientId(state, end) || undefined;
    index = getBlockIndex(state, end, rootClientId) + 1;
  } else {
    index = getBlockOrder(state).length;
  }

  return {
    rootClientId: rootClientId,
    index: index
  };
}
/**
 * Returns true if we should show the block insertion point.
 *
 * @param {Object} state Global application state.
 *
 * @return {?boolean} Whether the insertion point is visible or not.
 */

function isBlockInsertionPointVisible(state) {
  return state.insertionPoint !== null;
}
/**
 * Returns whether the blocks matches the template or not.
 *
 * @param {boolean} state
 * @return {?boolean} Whether the template is valid or not.
 */

function isValidTemplate(state) {
  return state.template.isValid;
}
/**
 * Returns the defined block template
 *
 * @param {boolean} state
 * @return {?Array}        Block Template
 */

function getTemplate(state) {
  return state.settings.template;
}
/**
 * Returns the defined block template lock. Optionally accepts a root block
 * client ID as context, otherwise defaulting to the global context.
 *
 * @param {Object}  state        Editor state.
 * @param {?string} rootClientId Optional block root client ID.
 *
 * @return {?string} Block Template Lock
 */

function getTemplateLock(state, rootClientId) {
  if (!rootClientId) {
    return state.settings.templateLock;
  }

  var blockListSettings = getBlockListSettings(state, rootClientId);

  if (!blockListSettings) {
    return null;
  }

  return blockListSettings.templateLock;
}
/**
 * Determines if the given block type is allowed to be inserted into the block list.
 * This function is not exported and not memoized because using a memoized selector
 * inside another memoized selector is just a waste of time.
 *
 * @param {Object}  state        Editor state.
 * @param {string}  blockName    The name of the block type, e.g.' core/paragraph'.
 * @param {?string} rootClientId Optional root client ID of block list.
 *
 * @return {boolean} Whether the given block type is allowed to be inserted.
 */

var selectors_canInsertBlockTypeUnmemoized = function canInsertBlockTypeUnmemoized(state, blockName) {
  var rootClientId = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;

  var checkAllowList = function checkAllowList(list, item) {
    var defaultResult = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;

    if (Object(external_lodash_["isBoolean"])(list)) {
      return list;
    }

    if (Object(external_lodash_["isArray"])(list)) {
      return Object(external_lodash_["includes"])(list, item);
    }

    return defaultResult;
  };

  var blockType = Object(external_this_wp_blocks_["getBlockType"])(blockName);

  if (!blockType) {
    return false;
  }

  var _getSettings = getSettings(state),
      allowedBlockTypes = _getSettings.allowedBlockTypes;

  var isBlockAllowedInEditor = checkAllowList(allowedBlockTypes, blockName, true);

  if (!isBlockAllowedInEditor) {
    return false;
  }

  var isLocked = !!getTemplateLock(state, rootClientId);

  if (isLocked) {
    return false;
  }

  var parentBlockListSettings = getBlockListSettings(state, rootClientId);
  var parentAllowedBlocks = Object(external_lodash_["get"])(parentBlockListSettings, ['allowedBlocks']);
  var hasParentAllowedBlock = checkAllowList(parentAllowedBlocks, blockName);
  var blockAllowedParentBlocks = blockType.parent;
  var parentName = getBlockName(state, rootClientId);
  var hasBlockAllowedParent = checkAllowList(blockAllowedParentBlocks, parentName);

  if (hasParentAllowedBlock !== null && hasBlockAllowedParent !== null) {
    return hasParentAllowedBlock || hasBlockAllowedParent;
  } else if (hasParentAllowedBlock !== null) {
    return hasParentAllowedBlock;
  } else if (hasBlockAllowedParent !== null) {
    return hasBlockAllowedParent;
  }

  return true;
};
/**
 * Determines if the given block type is allowed to be inserted into the block list.
 *
 * @param {Object}  state        Editor state.
 * @param {string}  blockName    The name of the block type, e.g.' core/paragraph'.
 * @param {?string} rootClientId Optional root client ID of block list.
 *
 * @return {boolean} Whether the given block type is allowed to be inserted.
 */


var canInsertBlockType = Object(rememo["a" /* default */])(selectors_canInsertBlockTypeUnmemoized, function (state, blockName, rootClientId) {
  return [state.blockListSettings[rootClientId], state.blocks.byClientId[rootClientId], state.settings.allowedBlockTypes, state.settings.templateLock];
});
/**
 * Returns information about how recently and frequently a block has been inserted.
 *
 * @param {Object} state Global application state.
 * @param {string} id    A string which identifies the insert, e.g. 'core/block/12'
 *
 * @return {?{ time: number, count: number }} An object containing `time` which is when the last
 *                                            insert occurred as a UNIX epoch, and `count` which is
 *                                            the number of inserts that have occurred.
 */

function getInsertUsage(state, id) {
  return state.preferences.insertUsage[id] || null;
}
/**
 * Returns whether we can show a block type in the inserter
 *
 * @param {Object} state Global State
 * @param {Object} blockType BlockType
 * @param {?string} rootClientId Optional root client ID of block list.
 *
 * @return {boolean} Whether the given block type is allowed to be shown in the inserter.
 */


var selectors_canIncludeBlockTypeInInserter = function canIncludeBlockTypeInInserter(state, blockType, rootClientId) {
  if (!Object(external_this_wp_blocks_["hasBlockSupport"])(blockType, 'inserter', true)) {
    return false;
  }

  return selectors_canInsertBlockTypeUnmemoized(state, blockType.name, rootClientId);
};
/**
 * Returns whether we can show a reusable block in the inserter
 *
 * @param {Object} state Global State
 * @param {Object} reusableBlock Reusable block object
 * @param {?string} rootClientId Optional root client ID of block list.
 *
 * @return {boolean} Whether the given block type is allowed to be shown in the inserter.
 */


var selectors_canIncludeReusableBlockInInserter = function canIncludeReusableBlockInInserter(state, reusableBlock, rootClientId) {
  if (!selectors_canInsertBlockTypeUnmemoized(state, 'core/block', rootClientId)) {
    return false;
  }

  var referencedBlockName = getBlockName(state, reusableBlock.clientId);

  if (!referencedBlockName) {
    return false;
  }

  var referencedBlockType = Object(external_this_wp_blocks_["getBlockType"])(referencedBlockName);

  if (!referencedBlockType) {
    return false;
  }

  if (!selectors_canInsertBlockTypeUnmemoized(state, referencedBlockName, rootClientId)) {
    return false;
  }

  if (isAncestorOf(state, reusableBlock.clientId, rootClientId)) {
    return false;
  }

  return true;
};
/**
 * Determines the items that appear in the inserter. Includes both static
 * items (e.g. a regular block type) and dynamic items (e.g. a reusable block).
 *
 * Each item object contains what's necessary to display a button in the
 * inserter and handle its selection.
 *
 * The 'utility' property indicates how useful we think an item will be to the
 * user. There are 4 levels of utility:
 *
 * 1. Blocks that are contextually useful (utility = 3)
 * 2. Blocks that have been previously inserted (utility = 2)
 * 3. Blocks that are in the common category (utility = 1)
 * 4. All other blocks (utility = 0)
 *
 * The 'frecency' property is a heuristic (https://en.wikipedia.org/wiki/Frecency)
 * that combines block usage frequenty and recency.
 *
 * Items are returned ordered descendingly by their 'utility' and 'frecency'.
 *
 * @param {Object}  state        Editor state.
 * @param {?string} rootClientId Optional root client ID of block list.
 *
 * @return {Editor.InserterItem[]} Items that appear in inserter.
 *
 * @typedef {Object} Editor.InserterItem
 * @property {string}   id                Unique identifier for the item.
 * @property {string}   name              The type of block to create.
 * @property {Object}   initialAttributes Attributes to pass to the newly created block.
 * @property {string}   title             Title of the item, as it appears in the inserter.
 * @property {string}   icon              Dashicon for the item, as it appears in the inserter.
 * @property {string}   category          Block category that the item is associated with.
 * @property {string[]} keywords          Keywords that can be searched to find this item.
 * @property {boolean}  isDisabled        Whether or not the user should be prevented from inserting
 *                                        this item.
 * @property {number}   utility           How useful we think this item is, between 0 and 3.
 * @property {number}   frecency          Hueristic that combines frequency and recency.
 */


var getInserterItems = Object(rememo["a" /* default */])(function (state) {
  var rootClientId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

  var calculateUtility = function calculateUtility(category, count, isContextual) {
    if (isContextual) {
      return INSERTER_UTILITY_HIGH;
    } else if (count > 0) {
      return INSERTER_UTILITY_MEDIUM;
    } else if (category === 'common') {
      return INSERTER_UTILITY_LOW;
    }

    return INSERTER_UTILITY_NONE;
  };

  var calculateFrecency = function calculateFrecency(time, count) {
    if (!time) {
      return count;
    } // The selector is cached, which means Date.now() is the last time that the
    // relevant state changed. This suits our needs.


    var duration = Date.now() - time;

    switch (true) {
      case duration < MILLISECONDS_PER_HOUR:
        return count * 4;

      case duration < MILLISECONDS_PER_DAY:
        return count * 2;

      case duration < MILLISECONDS_PER_WEEK:
        return count / 2;

      default:
        return count / 4;
    }
  };

  var buildBlockTypeInserterItem = function buildBlockTypeInserterItem(blockType) {
    var id = blockType.name;
    var isDisabled = false;

    if (!Object(external_this_wp_blocks_["hasBlockSupport"])(blockType.name, 'multiple', true)) {
      isDisabled = Object(external_lodash_["some"])(getBlocksByClientId(state, getClientIdsWithDescendants(state)), {
        name: blockType.name
      });
    }

    var isContextual = Object(external_lodash_["isArray"])(blockType.parent);

    var _ref = getInsertUsage(state, id) || {},
        time = _ref.time,
        _ref$count = _ref.count,
        count = _ref$count === void 0 ? 0 : _ref$count;

    return {
      id: id,
      name: blockType.name,
      initialAttributes: {},
      title: blockType.title,
      icon: blockType.icon,
      category: blockType.category,
      keywords: blockType.keywords,
      isDisabled: isDisabled,
      utility: calculateUtility(blockType.category, count, isContextual),
      frecency: calculateFrecency(time, count),
      hasChildBlocksWithInserterSupport: Object(external_this_wp_blocks_["hasChildBlocksWithInserterSupport"])(blockType.name)
    };
  };

  var buildReusableBlockInserterItem = function buildReusableBlockInserterItem(reusableBlock) {
    var id = "core/block/".concat(reusableBlock.id);
    var referencedBlockName = getBlockName(state, reusableBlock.clientId);
    var referencedBlockType = Object(external_this_wp_blocks_["getBlockType"])(referencedBlockName);

    var _ref2 = getInsertUsage(state, id) || {},
        time = _ref2.time,
        _ref2$count = _ref2.count,
        count = _ref2$count === void 0 ? 0 : _ref2$count;

    var utility = calculateUtility('reusable', count, false);
    var frecency = calculateFrecency(time, count);
    return {
      id: id,
      name: 'core/block',
      initialAttributes: {
        ref: reusableBlock.id
      },
      title: reusableBlock.title,
      icon: referencedBlockType.icon,
      category: 'reusable',
      keywords: [],
      isDisabled: false,
      utility: utility,
      frecency: frecency
    };
  };

  var blockTypeInserterItems = Object(external_this_wp_blocks_["getBlockTypes"])().filter(function (blockType) {
    return selectors_canIncludeBlockTypeInInserter(state, blockType, rootClientId);
  }).map(buildBlockTypeInserterItem);
  var reusableBlockInserterItems = getReusableBlocks(state).filter(function (block) {
    return selectors_canIncludeReusableBlockInInserter(state, block, rootClientId);
  }).map(buildReusableBlockInserterItem);
  return Object(external_lodash_["orderBy"])([].concat(Object(toConsumableArray["a" /* default */])(blockTypeInserterItems), Object(toConsumableArray["a" /* default */])(reusableBlockInserterItems)), ['utility', 'frecency'], ['desc', 'desc']);
}, function (state, rootClientId) {
  return [state.blockListSettings[rootClientId], state.blocks.byClientId, state.blocks.order, state.preferences.insertUsage, state.settings.allowedBlockTypes, state.settings.templateLock, getReusableBlocks(state), Object(external_this_wp_blocks_["getBlockTypes"])()];
});
/**
 * Determines whether there are items to show in the inserter.
 * @param {Object}  state        Editor state.
 * @param {?string} rootClientId Optional root client ID of block list.
 *
 * @return {boolean} Items that appear in inserter.
 */

var hasInserterItems = Object(rememo["a" /* default */])(function (state) {
  var rootClientId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  var hasBlockType = Object(external_lodash_["some"])(Object(external_this_wp_blocks_["getBlockTypes"])(), function (blockType) {
    return selectors_canIncludeBlockTypeInInserter(state, blockType, rootClientId);
  });

  if (hasBlockType) {
    return true;
  }

  var hasReusableBlock = Object(external_lodash_["some"])(getReusableBlocks(state), function (block) {
    return selectors_canIncludeReusableBlockInInserter(state, block, rootClientId);
  });
  return hasReusableBlock;
}, function (state, rootClientId) {
  return [state.blockListSettings[rootClientId], state.blocks.byClientId, state.settings.allowedBlockTypes, state.settings.templateLock, getReusableBlocks(state), Object(external_this_wp_blocks_["getBlockTypes"])()];
});
/**
 * Returns the Block List settings of a block, if any exist.
 *
 * @param {Object}  state    Editor state.
 * @param {?string} clientId Block client ID.
 *
 * @return {?Object} Block settings of the block if set.
 */

function getBlockListSettings(state, clientId) {
  return state.blockListSettings[clientId];
}
/**
 * Returns the editor settings.
 *
 * @param {Object} state Editor state.
 *
 * @return {Object} The editor settings object.
 */

function getSettings(state) {
  return state.settings;
}
/**
 * Returns true if the most recent block change is be considered persistent, or
 * false otherwise. A persistent change is one committed by BlockEditorProvider
 * via its `onChange` callback, in addition to `onInput`.
 *
 * @param {Object} state Block editor state.
 *
 * @return {boolean} Whether the most recent block change was persistent.
 */

function isLastBlockChangePersistent(state) {
  return state.blocks.isPersistentChange;
}
/**
 * Returns the value of a post meta from the editor settings.
 *
 * @param {Object} state Global application state.
 * @param {string} key   Meta Key to retrieve
 *
 * @return {*} Meta value
 */

function getPostMeta(state, key) {
  if (key === undefined) {
    return Object(external_lodash_["get"])(state, ['settings', '__experimentalMetaSource', 'value'], EMPTY_OBJECT);
  }

  return Object(external_lodash_["get"])(state, ['settings', '__experimentalMetaSource', 'value', key]);
}
/**
 * Returns the available reusable blocks
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} Reusable blocks
 */


function getReusableBlocks(state) {
  return Object(external_lodash_["get"])(state, ['settings', '__experimentalReusableBlocks'], EMPTY_ARRAY);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/block-editor/build-module/store/effects.js




/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



/**
 * Block validity is a function of blocks state (at the point of a
 * reset) and the template setting. As a compromise to its placement
 * across distinct parts of state, it is implemented here as a side-
 * effect of the block reset action.
 *
 * @param {Object} action RESET_BLOCKS action.
 * @param {Object} store  Store instance.
 *
 * @return {?Object} New validity set action if validity has changed.
 */

function validateBlocksToTemplate(action, store) {
  var state = store.getState();
  var template = getTemplate(state);
  var templateLock = getTemplateLock(state); // Unlocked templates are considered always valid because they act
  // as default values only.

  var isBlocksValidToTemplate = !template || templateLock !== 'all' || Object(external_this_wp_blocks_["doBlocksMatchTemplate"])(action.blocks, template); // Update if validity has changed.

  if (isBlocksValidToTemplate !== isValidTemplate(state)) {
    return setTemplateValidity(isBlocksValidToTemplate);
  }
}
/**
 * Effect handler which will return a default block insertion action if there
 * are no other blocks at the root of the editor. This is expected to be used
 * in actions which may result in no blocks remaining in the editor (removal,
 * replacement, etc).
 *
 * @param {Object} action Action which had initiated the effect handler.
 * @param {Object} store  Store instance.
 *
 * @return {?Object} Default block insert action, if no other blocks exist.
 */

function ensureDefaultBlock(action, store) {
  if (!getBlockCount(store.getState())) {
    return insertDefaultBlock();
  }
}
/* harmony default export */ var effects = ({
  MERGE_BLOCKS: function MERGE_BLOCKS(action, store) {
    var dispatch = store.dispatch;
    var state = store.getState();

    var _action$blocks = Object(slicedToArray["a" /* default */])(action.blocks, 2),
        firstBlockClientId = _action$blocks[0],
        secondBlockClientId = _action$blocks[1];

    var blockA = getBlock(state, firstBlockClientId);
    var blockType = Object(external_this_wp_blocks_["getBlockType"])(blockA.name); // Only focus the previous block if it's not mergeable

    if (!blockType.merge) {
      dispatch(selectBlock(blockA.clientId));
      return;
    } // We can only merge blocks with similar types
    // thus, we transform the block to merge first


    var blockB = getBlock(state, secondBlockClientId);
    var blocksWithTheSameType = blockA.name === blockB.name ? [blockB] : Object(external_this_wp_blocks_["switchToBlockType"])(blockB, blockA.name); // If the block types can not match, do nothing

    if (!blocksWithTheSameType || !blocksWithTheSameType.length) {
      return;
    } // Calling the merge to update the attributes and remove the block to be merged


    var updatedAttributes = blockType.merge(blockA.attributes, blocksWithTheSameType[0].attributes);
    dispatch(selectBlock(blockA.clientId, -1));
    dispatch(replaceBlocks([blockA.clientId, blockB.clientId], [Object(objectSpread["a" /* default */])({}, blockA, {
      attributes: Object(objectSpread["a" /* default */])({}, blockA.attributes, updatedAttributes)
    })].concat(Object(toConsumableArray["a" /* default */])(blocksWithTheSameType.slice(1)))));
  },
  RESET_BLOCKS: [validateBlocksToTemplate],
  REPLACE_BLOCKS: [ensureDefaultBlock],
  MULTI_SELECT: function MULTI_SELECT(action, _ref) {
    var getState = _ref.getState;
    var blockCount = getSelectedBlockCount(getState());
    /* translators: %s: number of selected blocks */

    Object(external_this_wp_a11y_["speak"])(Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["_n"])('%s block selected.', '%s blocks selected.', blockCount), blockCount), 'assertive');
  },
  SYNCHRONIZE_TEMPLATE: function SYNCHRONIZE_TEMPLATE(action, _ref2) {
    var getState = _ref2.getState;
    var state = getState();
    var blocks = getBlocks(state);
    var template = getTemplate(state);
    var updatedBlockList = Object(external_this_wp_blocks_["synchronizeBlocksWithTemplate"])(blocks, template);
    return resetBlocks(updatedBlockList);
  }
});

// CONCATENATED MODULE: ./node_modules/@wordpress/block-editor/build-module/store/middlewares.js


/**
 * External dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Applies the custom middlewares used specifically in the editor module.
 *
 * @param {Object} store Store Object.
 *
 * @return {Object} Update Store Object.
 */

function applyMiddlewares(store) {
  var middlewares = [refx_default()(effects), lib_default.a];

  var enhancedDispatch = function enhancedDispatch() {
    throw new Error('Dispatching while constructing your middleware is not allowed. ' + 'Other middleware would not be applied to this dispatch.');
  };

  var chain = [];
  var middlewareAPI = {
    getState: store.getState,
    dispatch: function dispatch() {
      return enhancedDispatch.apply(void 0, arguments);
    }
  };
  chain = middlewares.map(function (middleware) {
    return middleware(middlewareAPI);
  });
  enhancedDispatch = external_lodash_["flowRight"].apply(void 0, Object(toConsumableArray["a" /* default */])(chain))(store.dispatch);
  store.dispatch = enhancedDispatch;
  return store;
}

/* harmony default export */ var store_middlewares = (applyMiddlewares);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-editor/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */






/**
 * Module Constants
 */

var MODULE_KEY = 'core/block-editor';
var store_store = Object(external_this_wp_data_["registerStore"])(MODULE_KEY, {
  reducer: store_reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject,
  controls: store_controls,
  persist: ['preferences']
});
store_middlewares(store_store);
/* harmony default export */ var build_module_store = (store_store);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(11);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(19);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(4);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(7);

// CONCATENATED MODULE: ./node_modules/@wordpress/block-editor/build-module/components/provider/index.js








/**
 * WordPress dependencies
 */




/**
 * Higher-order component which renders the original component with the current
 * registry context passed as its `registry` prop.
 *
 * @param {WPComponent} OriginalComponent Original component.
 *
 * @return {WPComponent} Enhanced component.
 */

var withRegistry = Object(external_this_wp_compose_["createHigherOrderComponent"])(function (OriginalComponent) {
  return function (props) {
    return Object(external_this_wp_element_["createElement"])(external_this_wp_data_["RegistryConsumer"], null, function (registry) {
      return Object(external_this_wp_element_["createElement"])(OriginalComponent, Object(esm_extends["a" /* default */])({}, props, {
        registry: registry
      }));
    });
  };
}, 'withRegistry');

var provider_BlockEditorProvider =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(BlockEditorProvider, _Component);

  function BlockEditorProvider() {
    Object(classCallCheck["a" /* default */])(this, BlockEditorProvider);

    return Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(BlockEditorProvider).apply(this, arguments));
  }

  Object(createClass["a" /* default */])(BlockEditorProvider, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.props.updateSettings(this.props.settings);
      this.props.resetBlocks(this.props.value);
      this.attachChangeObserver(this.props.registry);
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      var _this$props = this.props,
          settings = _this$props.settings,
          updateSettings = _this$props.updateSettings,
          value = _this$props.value,
          resetBlocks = _this$props.resetBlocks,
          registry = _this$props.registry;

      if (settings !== prevProps.settings) {
        updateSettings(settings);
      }

      if (registry !== prevProps.registry) {
        this.attachChangeObserver(registry);
      }

      if (this.isSyncingOutcomingValue) {
        this.isSyncingOutcomingValue = false;
      } else if (value !== prevProps.value) {
        this.isSyncingIncomingValue = true;
        resetBlocks(value);
      }
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      if (this.unsubscribe) {
        this.unsubscribe();
      }
    }
    /**
     * Given a registry object, overrides the default dispatch behavior for the
     * `core/block-editor` store to interpret a state change and decide whether
     * we should call `onChange` or `onInput` depending on whether the change
     * is persistent or not.
     *
     * This needs to be done synchronously after state changes (instead of using
     * `componentDidUpdate`) in order to avoid batching these changes.
     *
     * @param {WPDataRegistry} registry     Registry from which block editor
     *                                      dispatch is to be overriden.
     */

  }, {
    key: "attachChangeObserver",
    value: function attachChangeObserver(registry) {
      var _this = this;

      if (this.unsubscribe) {
        this.unsubscribe();
      }

      var _registry$select = registry.select('core/block-editor'),
          getBlocks = _registry$select.getBlocks,
          isLastBlockChangePersistent = _registry$select.isLastBlockChangePersistent;

      var blocks = getBlocks();
      var isPersistent = isLastBlockChangePersistent();
      this.unsubscribe = registry.subscribe(function () {
        var _this$props2 = _this.props,
            onChange = _this$props2.onChange,
            onInput = _this$props2.onInput;
        var newBlocks = getBlocks();
        var newIsPersistent = isLastBlockChangePersistent();

        if (newBlocks !== blocks && _this.isSyncingIncomingValue) {
          _this.isSyncingIncomingValue = false;
          blocks = newBlocks;
          isPersistent = newIsPersistent;
          return;
        }

        if (newBlocks !== blocks || // This happens when a previous input is explicitely marked as persistent.
        newIsPersistent && !isPersistent) {
          blocks = newBlocks;
          isPersistent = newIsPersistent;
          _this.isSyncingOutcomingValue = true;

          if (isPersistent) {
            onChange(blocks);
          } else {
            onInput(blocks);
          }
        }
      });
    }
  }, {
    key: "render",
    value: function render() {
      var children = this.props.children;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SlotFillProvider"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["DropZoneProvider"], null, children));
    }
  }]);

  return BlockEditorProvider;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var provider = (Object(external_this_wp_compose_["compose"])([Object(external_this_wp_data_["withDispatch"])(function (dispatch) {
  var _dispatch = dispatch('core/block-editor'),
      updateSettings = _dispatch.updateSettings,
      resetBlocks = _dispatch.resetBlocks;

  return {
    updateSettings: updateSettings,
    resetBlocks: resetBlocks
  };
}), withRegistry])(provider_BlockEditorProvider));

// CONCATENATED MODULE: ./node_modules/@wordpress/block-editor/build-module/components/index.js


// CONCATENATED MODULE: ./node_modules/@wordpress/block-editor/build-module/index.js
/* concated harmony reexport BlockEditorProvider */__webpack_require__.d(__webpack_exports__, "BlockEditorProvider", function() { return provider; });
/* concated harmony reexport SETTINGS_DEFAULTS */__webpack_require__.d(__webpack_exports__, "SETTINGS_DEFAULTS", function() { return SETTINGS_DEFAULTS; });
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */






/***/ }),

/***/ 33:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

/***/ }),

/***/ 34:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayWithHoles; });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),

/***/ 35:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _nonIterableRest; });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance");
}

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ 44:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["a11y"]; }());

/***/ }),

/***/ 5:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ 64:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


function flattenIntoMap( map, effects ) {
	var i;
	if ( Array.isArray( effects ) ) {
		for ( i = 0; i < effects.length; i++ ) {
			flattenIntoMap( map, effects[ i ] );
		}
	} else {
		for ( i in effects ) {
			map[ i ] = ( map[ i ] || [] ).concat( effects[ i ] );
		}
	}
}

function refx( effects ) {
	var map = {},
		middleware;

	flattenIntoMap( map, effects );

	middleware = function( store ) {
		return function( next ) {
			return function( action ) {
				var handlers = map[ action.type ],
					result = next( action ),
					i, handlerAction;

				if ( handlers ) {
					for ( i = 0; i < handlers.length; i++ ) {
						handlerAction = handlers[ i ]( action, store );
						if ( handlerAction ) {
							store.dispatch( handlerAction );
						}
					}
				}

				return result;
			};
		};
	};

	middleware.effects = map;

	return middleware;
}

module.exports = refx;


/***/ }),

/***/ 7:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ 8:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectSpread; });
/* harmony import */ var _defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(15);

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      Object(_defineProperty__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(target, key, source[key]);
    });
  }

  return target;
}

/***/ }),

/***/ 87:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
/**
 * Redux dispatch multiple actions
 */

function multi(_ref) {
  var dispatch = _ref.dispatch;

  return function (next) {
    return function (action) {
      return Array.isArray(action) ? action.filter(Boolean).map(dispatch) : next(action);
    };
  };
}

/**
 * Exports
 */

exports.default = multi;

/***/ }),

/***/ 9:
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

/***/ })

/******/ });