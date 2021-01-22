this["wp"] = this["wp"] || {}; this["wp"]["richText"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 440);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ 11:
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2017 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames () {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg) && arg.length) {
				var inner = classNames.apply(null, arg);
				if (inner) {
					classes.push(inner);
				}
			} else if (argType === 'object') {
				for (var key in arg) {
					if (hasOwn.call(arg, key) && arg[key]) {
						classes.push(key);
					}
				}
			}
		}

		return classes.join(' ');
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ }),

/***/ 14:
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
var unsupportedIterableToArray = __webpack_require__(29);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
var nonIterableRest = __webpack_require__(39);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js




function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(unsupportedIterableToArray["a" /* default */])(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ 15:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectWithoutProperties; });
/* harmony import */ var _objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(41);

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};
  var target = Object(_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(source, excluded);
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

/***/ 18:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _toConsumableArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js
var arrayLikeToArray = __webpack_require__(26);

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return Object(arrayLikeToArray["a" /* default */])(arr);
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
var iterableToArray = __webpack_require__(35);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
var unsupportedIterableToArray = __webpack_require__(29);

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

/***/ 21:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["keycodes"]; }());

/***/ }),

/***/ 26:
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

/***/ 29:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _unsupportedIterableToArray; });
/* harmony import */ var _arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(26);

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return Object(_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return Object(_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
}

/***/ }),

/***/ 35:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
}

/***/ }),

/***/ 37:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["deprecated"]; }());

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

/***/ 40:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _typeof; });
function _typeof(obj) {
  "@babel/helpers - typeof";

  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function _typeof(obj) {
      return typeof obj;
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

/***/ }),

/***/ 41:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectWithoutPropertiesLoose; });
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

/***/ 440:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "applyFormat", function() { return /* reexport */ applyFormat; });
__webpack_require__.d(__webpack_exports__, "concat", function() { return /* reexport */ concat; });
__webpack_require__.d(__webpack_exports__, "create", function() { return /* reexport */ create; });
__webpack_require__.d(__webpack_exports__, "getActiveFormat", function() { return /* reexport */ getActiveFormat; });
__webpack_require__.d(__webpack_exports__, "getActiveObject", function() { return /* reexport */ getActiveObject; });
__webpack_require__.d(__webpack_exports__, "getTextContent", function() { return /* reexport */ getTextContent; });
__webpack_require__.d(__webpack_exports__, "__unstableIsListRootSelected", function() { return /* reexport */ isListRootSelected; });
__webpack_require__.d(__webpack_exports__, "__unstableIsActiveListType", function() { return /* reexport */ isActiveListType; });
__webpack_require__.d(__webpack_exports__, "isCollapsed", function() { return /* reexport */ isCollapsed; });
__webpack_require__.d(__webpack_exports__, "isEmpty", function() { return /* reexport */ isEmpty; });
__webpack_require__.d(__webpack_exports__, "__unstableIsEmptyLine", function() { return /* reexport */ isEmptyLine; });
__webpack_require__.d(__webpack_exports__, "join", function() { return /* reexport */ join; });
__webpack_require__.d(__webpack_exports__, "registerFormatType", function() { return /* reexport */ registerFormatType; });
__webpack_require__.d(__webpack_exports__, "removeFormat", function() { return /* reexport */ removeFormat; });
__webpack_require__.d(__webpack_exports__, "remove", function() { return /* reexport */ remove_remove; });
__webpack_require__.d(__webpack_exports__, "replace", function() { return /* reexport */ replace_replace; });
__webpack_require__.d(__webpack_exports__, "insert", function() { return /* reexport */ insert; });
__webpack_require__.d(__webpack_exports__, "__unstableInsertLineSeparator", function() { return /* reexport */ insertLineSeparator; });
__webpack_require__.d(__webpack_exports__, "__unstableRemoveLineSeparator", function() { return /* reexport */ removeLineSeparator; });
__webpack_require__.d(__webpack_exports__, "insertObject", function() { return /* reexport */ insertObject; });
__webpack_require__.d(__webpack_exports__, "slice", function() { return /* reexport */ slice; });
__webpack_require__.d(__webpack_exports__, "split", function() { return /* reexport */ split; });
__webpack_require__.d(__webpack_exports__, "__unstableToDom", function() { return /* reexport */ toDom; });
__webpack_require__.d(__webpack_exports__, "toHTMLString", function() { return /* reexport */ toHTMLString; });
__webpack_require__.d(__webpack_exports__, "toggleFormat", function() { return /* reexport */ toggleFormat; });
__webpack_require__.d(__webpack_exports__, "__UNSTABLE_LINE_SEPARATOR", function() { return /* reexport */ LINE_SEPARATOR; });
__webpack_require__.d(__webpack_exports__, "unregisterFormatType", function() { return /* reexport */ unregisterFormatType; });
__webpack_require__.d(__webpack_exports__, "__unstableCanIndentListItems", function() { return /* reexport */ canIndentListItems; });
__webpack_require__.d(__webpack_exports__, "__unstableCanOutdentListItems", function() { return /* reexport */ canOutdentListItems; });
__webpack_require__.d(__webpack_exports__, "__unstableIndentListItems", function() { return /* reexport */ indentListItems; });
__webpack_require__.d(__webpack_exports__, "__unstableOutdentListItems", function() { return /* reexport */ outdentListItems; });
__webpack_require__.d(__webpack_exports__, "__unstableChangeListType", function() { return /* reexport */ changeListType; });
__webpack_require__.d(__webpack_exports__, "__unstableCreateElement", function() { return /* reexport */ createElement; });
__webpack_require__.d(__webpack_exports__, "__experimentalRichText", function() { return /* reexport */ component; });
__webpack_require__.d(__webpack_exports__, "__unstableFormatEdit", function() { return /* reexport */ FormatEdit; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/rich-text/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getFormatTypes", function() { return getFormatTypes; });
__webpack_require__.d(selectors_namespaceObject, "getFormatType", function() { return getFormatType; });
__webpack_require__.d(selectors_namespaceObject, "getFormatTypeForBareElement", function() { return getFormatTypeForBareElement; });
__webpack_require__.d(selectors_namespaceObject, "getFormatTypeForClassName", function() { return getFormatTypeForClassName; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/rich-text/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "addFormatTypes", function() { return addFormatTypes; });
__webpack_require__.d(actions_namespaceObject, "removeFormatTypes", function() { return removeFormatTypes; });

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(4);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__(5);

// EXTERNAL MODULE: external {"this":"lodash"}
var external_this_lodash_ = __webpack_require__(2);

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/reducer.js


function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Reducer managing the format types
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function reducer_formatTypes() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'ADD_FORMAT_TYPES':
      return _objectSpread({}, state, {}, Object(external_this_lodash_["keyBy"])(action.formatTypes, 'name'));

    case 'REMOVE_FORMAT_TYPES':
      return Object(external_this_lodash_["omit"])(state, action.names);
  }

  return state;
}
/* harmony default export */ var reducer = (Object(external_this_wp_data_["combineReducers"])({
  formatTypes: reducer_formatTypes
}));

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__(42);

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/selectors.js
/**
 * External dependencies
 */


/**
 * Returns all the available format types.
 *
 * @param {Object} state Data state.
 *
 * @return {Array} Format types.
 */

var getFormatTypes = Object(rememo["a" /* default */])(function (state) {
  return Object.values(state.formatTypes);
}, function (state) {
  return [state.formatTypes];
});
/**
 * Returns a format type by name.
 *
 * @param {Object} state Data state.
 * @param {string} name Format type name.
 *
 * @return {Object?} Format type.
 */

function getFormatType(state, name) {
  return state.formatTypes[name];
}
/**
 * Gets the format type, if any, that can handle a bare element (without a
 * data-format-type attribute), given the tag name of this element.
 *
 * @param {Object} state              Data state.
 * @param {string} bareElementTagName The tag name of the element to find a
 *                                    format type for.
 * @return {?Object} Format type.
 */

function getFormatTypeForBareElement(state, bareElementTagName) {
  return Object(external_this_lodash_["find"])(getFormatTypes(state), function (_ref) {
    var className = _ref.className,
        tagName = _ref.tagName;
    return className === null && bareElementTagName === tagName;
  });
}
/**
 * Gets the format type, if any, that can handle an element, given its classes.
 *
 * @param {Object} state            Data state.
 * @param {string} elementClassName The classes of the element to find a format
 *                                  type for.
 * @return {?Object} Format type.
 */

function getFormatTypeForClassName(state, elementClassName) {
  return Object(external_this_lodash_["find"])(getFormatTypes(state), function (_ref2) {
    var className = _ref2.className;

    if (className === null) {
      return false;
    }

    return " ".concat(elementClassName, " ").indexOf(" ".concat(className, " ")) >= 0;
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/actions.js
/**
 * External dependencies
 */

/**
 * Returns an action object used in signalling that format types have been
 * added.
 *
 * @param {Array|Object} formatTypes Format types received.
 *
 * @return {Object} Action object.
 */

function addFormatTypes(formatTypes) {
  return {
    type: 'ADD_FORMAT_TYPES',
    formatTypes: Object(external_this_lodash_["castArray"])(formatTypes)
  };
}
/**
 * Returns an action object used to remove a registered format type.
 *
 * @param {string|Array} names Format name.
 *
 * @return {Object} Action object.
 */

function removeFormatTypes(names) {
  return {
    type: 'REMOVE_FORMAT_TYPES',
    names: Object(external_this_lodash_["castArray"])(names)
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




Object(external_this_wp_data_["registerStore"])('core/rich-text', {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject
});

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__(18);

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-format-equal.js
/**
 * Optimised equality check for format objects.
 *
 * @param {?Object} format1 Format to compare.
 * @param {?Object} format2 Format to compare.
 *
 * @return {boolean} True if formats are equal, false if not.
 */
function isFormatEqual(format1, format2) {
  // Both not defined.
  if (format1 === format2) {
    return true;
  } // Either not defined.


  if (!format1 || !format2) {
    return false;
  }

  if (format1.type !== format2.type) {
    return false;
  }

  var attributes1 = format1.attributes;
  var attributes2 = format2.attributes; // Both not defined.

  if (attributes1 === attributes2) {
    return true;
  } // Either not defined.


  if (!attributes1 || !attributes2) {
    return false;
  }

  var keys1 = Object.keys(attributes1);
  var keys2 = Object.keys(attributes2);

  if (keys1.length !== keys2.length) {
    return false;
  }

  var length = keys1.length; // Optimise for speed.

  for (var i = 0; i < length; i++) {
    var name = keys1[i];

    if (attributes1[name] !== attributes2[name]) {
      return false;
    }
  }

  return true;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/normalise-formats.js


function normalise_formats_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function normalise_formats_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { normalise_formats_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { normalise_formats_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Internal dependencies
 */

/**
 * Normalises formats: ensures subsequent adjacent equal formats have the same
 * reference.
 *
 * @param {Object} value Value to normalise formats of.
 *
 * @return {Object} New value with normalised formats.
 */

function normaliseFormats(value) {
  var newFormats = value.formats.slice();
  newFormats.forEach(function (formatsAtIndex, index) {
    var formatsAtPreviousIndex = newFormats[index - 1];

    if (formatsAtPreviousIndex) {
      var newFormatsAtIndex = formatsAtIndex.slice();
      newFormatsAtIndex.forEach(function (format, formatIndex) {
        var previousFormat = formatsAtPreviousIndex[formatIndex];

        if (isFormatEqual(format, previousFormat)) {
          newFormatsAtIndex[formatIndex] = previousFormat;
        }
      });
      newFormats[index] = newFormatsAtIndex;
    }
  });
  return normalise_formats_objectSpread({}, value, {
    formats: newFormats
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/apply-format.js



function apply_format_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function apply_format_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { apply_format_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { apply_format_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */



function replace(array, index, value) {
  array = array.slice();
  array[index] = value;
  return array;
}
/**
 * Apply a format object to a Rich Text value from the given `startIndex` to the
 * given `endIndex`. Indices are retrieved from the selection if none are
 * provided.
 *
 * @param {Object} value        Value to modify.
 * @param {Object} format       Format to apply.
 * @param {number} [startIndex] Start index.
 * @param {number} [endIndex]   End index.
 *
 * @return {Object} A new value with the format applied.
 */


function applyFormat(value, format) {
  var startIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : value.start;
  var endIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : value.end;
  var formats = value.formats,
      activeFormats = value.activeFormats;
  var newFormats = formats.slice(); // The selection is collapsed.

  if (startIndex === endIndex) {
    var startFormat = Object(external_this_lodash_["find"])(newFormats[startIndex], {
      type: format.type
    }); // If the caret is at a format of the same type, expand start and end to
    // the edges of the format. This is useful to apply new attributes.

    if (startFormat) {
      var index = newFormats[startIndex].indexOf(startFormat);

      while (newFormats[startIndex] && newFormats[startIndex][index] === startFormat) {
        newFormats[startIndex] = replace(newFormats[startIndex], index, format);
        startIndex--;
      }

      endIndex++;

      while (newFormats[endIndex] && newFormats[endIndex][index] === startFormat) {
        newFormats[endIndex] = replace(newFormats[endIndex], index, format);
        endIndex++;
      }
    }
  } else {
    // Determine the highest position the new format can be inserted at.
    var position = +Infinity;

    for (var _index = startIndex; _index < endIndex; _index++) {
      if (newFormats[_index]) {
        newFormats[_index] = newFormats[_index].filter(function (_ref) {
          var type = _ref.type;
          return type !== format.type;
        });
        var length = newFormats[_index].length;

        if (length < position) {
          position = length;
        }
      } else {
        newFormats[_index] = [];
        position = 0;
      }
    }

    for (var _index2 = startIndex; _index2 < endIndex; _index2++) {
      newFormats[_index2].splice(position, 0, format);
    }
  }

  return normaliseFormats(apply_format_objectSpread({}, value, {
    formats: newFormats,
    // Always revise active formats. This serves as a placeholder for new
    // inputs with the format so new input appears with the format applied,
    // and ensures a format of the same type uses the latest values.
    activeFormats: [].concat(Object(toConsumableArray["a" /* default */])(Object(external_this_lodash_["reject"])(activeFormats, {
      type: format.type
    })), [format])
  }));
}

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/typeof.js
var esm_typeof = __webpack_require__(40);

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/create-element.js
/**
 * Parse the given HTML into a body element.
 *
 * Note: The current implementation will return a shared reference, reset on
 * each call to `createElement`. Therefore, you should not hold a reference to
 * the value to operate upon asynchronously, as it may have unexpected results.
 *
 * @param {HTMLDocument} document The HTML document to use to parse.
 * @param {string}       html     The HTML to parse.
 *
 * @return {HTMLBodyElement} Body element with parsed HTML.
 */
function createElement(_ref, html) {
  var implementation = _ref.implementation;

  // Because `createHTMLDocument` is an expensive operation, and with this
  // function being internal to `rich-text` (full control in avoiding a risk
  // of asynchronous operations on the shared reference), a single document
  // is reused and reset for each call to the function.
  if (!createElement.body) {
    createElement.body = implementation.createHTMLDocument('').body;
  }

  createElement.body.innerHTML = html;
  return createElement.body;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/special-characters.js
/**
 * Line separator character, used for multiline text.
 */
var LINE_SEPARATOR = "\u2028";
/**
 * Object replacement character, used as a placeholder for objects.
 */

var OBJECT_REPLACEMENT_CHARACTER = "\uFFFC";
/**
 * Zero width non-breaking space, used as padding in the editable DOM tree when
 * it is empty otherwise.
 */

var ZWNBSP = "\uFEFF";

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/create.js




function create_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function create_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { create_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { create_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */






function createEmptyValue() {
  return {
    formats: [],
    replacements: [],
    text: ''
  };
}

function simpleFindKey(object, value) {
  for (var key in object) {
    if (object[key] === value) {
      return key;
    }
  }
}

function toFormat(_ref) {
  var type = _ref.type,
      attributes = _ref.attributes;
  var formatType;

  if (attributes && attributes.class) {
    formatType = Object(external_this_wp_data_["select"])('core/rich-text').getFormatTypeForClassName(attributes.class);

    if (formatType) {
      // Preserve any additional classes.
      attributes.class = " ".concat(attributes.class, " ").replace(" ".concat(formatType.className, " "), ' ').trim();

      if (!attributes.class) {
        delete attributes.class;
      }
    }
  }

  if (!formatType) {
    formatType = Object(external_this_wp_data_["select"])('core/rich-text').getFormatTypeForBareElement(type);
  }

  if (!formatType) {
    return attributes ? {
      type: type,
      attributes: attributes
    } : {
      type: type
    };
  }

  if (formatType.__experimentalCreatePrepareEditableTree && !formatType.__experimentalCreateOnChangeEditableValue) {
    return null;
  }

  if (!attributes) {
    return {
      type: formatType.name
    };
  }

  var registeredAttributes = {};
  var unregisteredAttributes = {};

  for (var name in attributes) {
    var key = simpleFindKey(formatType.attributes, name);

    if (key) {
      registeredAttributes[key] = attributes[name];
    } else {
      unregisteredAttributes[name] = attributes[name];
    }
  }

  return {
    type: formatType.name,
    attributes: registeredAttributes,
    unregisteredAttributes: unregisteredAttributes
  };
}
/**
 * Create a RichText value from an `Element` tree (DOM), an HTML string or a
 * plain text string, with optionally a `Range` object to set the selection. If
 * called without any input, an empty value will be created. If
 * `multilineTag` is provided, any content of direct children whose type matches
 * `multilineTag` will be separated by two newlines. The optional functions can
 * be used to filter out content.
 *
 * A value will have the following shape, which you are strongly encouraged not
 * to modify without the use of helper functions:
 *
 * ```js
 * {
 *   text: string,
 *   formats: Array,
 *   replacements: Array,
 *   ?start: number,
 *   ?end: number,
 * }
 * ```
 *
 * As you can see, text and formatting are separated. `text` holds the text,
 * including any replacement characters for objects and lines. `formats`,
 * `objects` and `lines` are all sparse arrays of the same length as `text`. It
 * holds information about the formatting at the relevant text indices. Finally
 * `start` and `end` state which text indices are selected. They are only
 * provided if a `Range` was given.
 *
 * @param {Object}  [$1]                      Optional named arguments.
 * @param {Element} [$1.element]              Element to create value from.
 * @param {string}  [$1.text]                 Text to create value from.
 * @param {string}  [$1.html]                 HTML to create value from.
 * @param {Range}   [$1.range]                Range to create value from.
 * @param {string}  [$1.multilineTag]         Multiline tag if the structure is
 *                                            multiline.
 * @param {Array}   [$1.multilineWrapperTags] Tags where lines can be found if
 *                                            nesting is possible.
 * @param {?boolean} [$1.preserveWhiteSpace]  Whether or not to collapse white
 *                                            space characters.
 *
 * @return {Object} A rich text value.
 */


function create() {
  var _ref2 = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
      element = _ref2.element,
      text = _ref2.text,
      html = _ref2.html,
      range = _ref2.range,
      multilineTag = _ref2.multilineTag,
      multilineWrapperTags = _ref2.multilineWrapperTags,
      isEditableTree = _ref2.__unstableIsEditableTree,
      preserveWhiteSpace = _ref2.preserveWhiteSpace;

  if (typeof text === 'string' && text.length > 0) {
    return {
      formats: Array(text.length),
      replacements: Array(text.length),
      text: text
    };
  }

  if (typeof html === 'string' && html.length > 0) {
    // It does not matter which document this is, we're just using it to
    // parse.
    element = createElement(document, html);
  }

  if (Object(esm_typeof["a" /* default */])(element) !== 'object') {
    return createEmptyValue();
  }

  if (!multilineTag) {
    return createFromElement({
      element: element,
      range: range,
      isEditableTree: isEditableTree,
      preserveWhiteSpace: preserveWhiteSpace
    });
  }

  return createFromMultilineElement({
    element: element,
    range: range,
    multilineTag: multilineTag,
    multilineWrapperTags: multilineWrapperTags,
    isEditableTree: isEditableTree,
    preserveWhiteSpace: preserveWhiteSpace
  });
}
/**
 * Helper to accumulate the value's selection start and end from the current
 * node and range.
 *
 * @param {Object} accumulator Object to accumulate into.
 * @param {Node}   node        Node to create value with.
 * @param {Range}  range       Range to create value with.
 * @param {Object} value       Value that is being accumulated.
 */

function accumulateSelection(accumulator, node, range, value) {
  if (!range) {
    return;
  }

  var parentNode = node.parentNode;
  var startContainer = range.startContainer,
      startOffset = range.startOffset,
      endContainer = range.endContainer,
      endOffset = range.endOffset;
  var currentLength = accumulator.text.length; // Selection can be extracted from value.

  if (value.start !== undefined) {
    accumulator.start = currentLength + value.start; // Range indicates that the current node has selection.
  } else if (node === startContainer && node.nodeType === node.TEXT_NODE) {
    accumulator.start = currentLength + startOffset; // Range indicates that the current node is selected.
  } else if (parentNode === startContainer && node === startContainer.childNodes[startOffset]) {
    accumulator.start = currentLength; // Range indicates that the selection is after the current node.
  } else if (parentNode === startContainer && node === startContainer.childNodes[startOffset - 1]) {
    accumulator.start = currentLength + value.text.length; // Fallback if no child inside handled the selection.
  } else if (node === startContainer) {
    accumulator.start = currentLength;
  } // Selection can be extracted from value.


  if (value.end !== undefined) {
    accumulator.end = currentLength + value.end; // Range indicates that the current node has selection.
  } else if (node === endContainer && node.nodeType === node.TEXT_NODE) {
    accumulator.end = currentLength + endOffset; // Range indicates that the current node is selected.
  } else if (parentNode === endContainer && node === endContainer.childNodes[endOffset - 1]) {
    accumulator.end = currentLength + value.text.length; // Range indicates that the selection is before the current node.
  } else if (parentNode === endContainer && node === endContainer.childNodes[endOffset]) {
    accumulator.end = currentLength; // Fallback if no child inside handled the selection.
  } else if (node === endContainer) {
    accumulator.end = currentLength + endOffset;
  }
}
/**
 * Adjusts the start and end offsets from a range based on a text filter.
 *
 * @param {Node}     node   Node of which the text should be filtered.
 * @param {Range}    range  The range to filter.
 * @param {Function} filter Function to use to filter the text.
 *
 * @return {?Object} Object containing range properties.
 */


function filterRange(node, range, filter) {
  if (!range) {
    return;
  }

  var startContainer = range.startContainer,
      endContainer = range.endContainer;
  var startOffset = range.startOffset,
      endOffset = range.endOffset;

  if (node === startContainer) {
    startOffset = filter(node.nodeValue.slice(0, startOffset)).length;
  }

  if (node === endContainer) {
    endOffset = filter(node.nodeValue.slice(0, endOffset)).length;
  }

  return {
    startContainer: startContainer,
    startOffset: startOffset,
    endContainer: endContainer,
    endOffset: endOffset
  };
}
/**
 * Collapse any whitespace used for HTML formatting to one space character,
 * because it will also be displayed as such by the browser.
 *
 * @param {string} string
 */


function collapseWhiteSpace(string) {
  return string.replace(/[\n\r\t]+/g, ' ');
}

var ZWNBSPRegExp = new RegExp(ZWNBSP, 'g');
/**
 * Removes padding (zero width non breaking spaces) added by `toTree`.
 *
 * @param {string} string
 */

function removePadding(string) {
  return string.replace(ZWNBSPRegExp, '');
}
/**
 * Creates a Rich Text value from a DOM element and range.
 *
 * @param {Object}    $1                      Named argements.
 * @param {?Element}  $1.element              Element to create value from.
 * @param {?Range}    $1.range                Range to create value from.
 * @param {?string}   $1.multilineTag         Multiline tag if the structure is
 *                                            multiline.
 * @param {?Array}    $1.multilineWrapperTags Tags where lines can be found if
 *                                            nesting is possible.
 * @param {?boolean} $1.preserveWhiteSpace    Whether or not to collapse white
 *                                            space characters.
 *
 * @return {Object} A rich text value.
 */


function createFromElement(_ref3) {
  var element = _ref3.element,
      range = _ref3.range,
      multilineTag = _ref3.multilineTag,
      multilineWrapperTags = _ref3.multilineWrapperTags,
      _ref3$currentWrapperT = _ref3.currentWrapperTags,
      currentWrapperTags = _ref3$currentWrapperT === void 0 ? [] : _ref3$currentWrapperT,
      isEditableTree = _ref3.isEditableTree,
      preserveWhiteSpace = _ref3.preserveWhiteSpace;
  var accumulator = createEmptyValue();

  if (!element) {
    return accumulator;
  }

  if (!element.hasChildNodes()) {
    accumulateSelection(accumulator, element, range, createEmptyValue());
    return accumulator;
  }

  var length = element.childNodes.length; // Optimise for speed.

  var _loop = function _loop(index) {
    var node = element.childNodes[index];
    var type = node.nodeName.toLowerCase();

    if (node.nodeType === node.TEXT_NODE) {
      var filter = removePadding;

      if (!preserveWhiteSpace) {
        filter = function filter(string) {
          return removePadding(collapseWhiteSpace(string));
        };
      }

      var text = filter(node.nodeValue);
      range = filterRange(node, range, filter);
      accumulateSelection(accumulator, node, range, {
        text: text
      }); // Create a sparse array of the same length as `text`, in which
      // formats can be added.

      accumulator.formats.length += text.length;
      accumulator.replacements.length += text.length;
      accumulator.text += text;
      return "continue";
    }

    if (node.nodeType !== node.ELEMENT_NODE) {
      return "continue";
    }

    if (isEditableTree && ( // Ignore any placeholders.
    node.getAttribute('data-rich-text-placeholder') || // Ignore any line breaks that are not inserted by us.
    type === 'br' && !node.getAttribute('data-rich-text-line-break'))) {
      accumulateSelection(accumulator, node, range, createEmptyValue());
      return "continue";
    }

    if (type === 'br') {
      accumulateSelection(accumulator, node, range, createEmptyValue());
      mergePair(accumulator, create({
        text: '\n'
      }));
      return "continue";
    }

    var lastFormats = accumulator.formats[accumulator.formats.length - 1];
    var lastFormat = lastFormats && lastFormats[lastFormats.length - 1];
    var newFormat = toFormat({
      type: type,
      attributes: getAttributes({
        element: node
      })
    });
    var format = isFormatEqual(newFormat, lastFormat) ? lastFormat : newFormat;

    if (multilineWrapperTags && multilineWrapperTags.indexOf(type) !== -1) {
      var _value = createFromMultilineElement({
        element: node,
        range: range,
        multilineTag: multilineTag,
        multilineWrapperTags: multilineWrapperTags,
        currentWrapperTags: [].concat(Object(toConsumableArray["a" /* default */])(currentWrapperTags), [format]),
        isEditableTree: isEditableTree,
        preserveWhiteSpace: preserveWhiteSpace
      });

      accumulateSelection(accumulator, node, range, _value);
      mergePair(accumulator, _value);
      return "continue";
    }

    var value = createFromElement({
      element: node,
      range: range,
      multilineTag: multilineTag,
      multilineWrapperTags: multilineWrapperTags,
      isEditableTree: isEditableTree,
      preserveWhiteSpace: preserveWhiteSpace
    });
    accumulateSelection(accumulator, node, range, value);

    if (!format) {
      mergePair(accumulator, value);
    } else if (value.text.length === 0) {
      if (format.attributes) {
        mergePair(accumulator, {
          formats: [,],
          replacements: [format],
          text: OBJECT_REPLACEMENT_CHARACTER
        });
      }
    } else {
      // Indices should share a reference to the same formats array.
      // Only create a new reference if `formats` changes.
      function mergeFormats(formats) {
        if (mergeFormats.formats === formats) {
          return mergeFormats.newFormats;
        }

        var newFormats = formats ? [format].concat(Object(toConsumableArray["a" /* default */])(formats)) : [format];
        mergeFormats.formats = formats;
        mergeFormats.newFormats = newFormats;
        return newFormats;
      } // Since the formats parameter can be `undefined`, preset
      // `mergeFormats` with a new reference.


      mergeFormats.newFormats = [format];
      mergePair(accumulator, create_objectSpread({}, value, {
        formats: Array.from(value.formats, mergeFormats)
      }));
    }
  };

  for (var index = 0; index < length; index++) {
    var _ret = _loop(index);

    if (_ret === "continue") continue;
  }

  return accumulator;
}
/**
 * Creates a rich text value from a DOM element and range that should be
 * multiline.
 *
 * @param {Object}   $1                      Named argements.
 * @param {?Element} $1.element              Element to create value from.
 * @param {?Range}   $1.range                Range to create value from.
 * @param {?string}  $1.multilineTag         Multiline tag if the structure is
 *                                           multiline.
 * @param {?Array}   $1.multilineWrapperTags Tags where lines can be found if
 *                                           nesting is possible.
 * @param {boolean}  $1.currentWrapperTags   Whether to prepend a line
 *                                           separator.
 * @param {?boolean} $1.preserveWhiteSpace   Whether or not to collapse white
 *                                           space characters.
 *
 * @return {Object} A rich text value.
 */


function createFromMultilineElement(_ref4) {
  var element = _ref4.element,
      range = _ref4.range,
      multilineTag = _ref4.multilineTag,
      multilineWrapperTags = _ref4.multilineWrapperTags,
      _ref4$currentWrapperT = _ref4.currentWrapperTags,
      currentWrapperTags = _ref4$currentWrapperT === void 0 ? [] : _ref4$currentWrapperT,
      isEditableTree = _ref4.isEditableTree,
      preserveWhiteSpace = _ref4.preserveWhiteSpace;
  var accumulator = createEmptyValue();

  if (!element || !element.hasChildNodes()) {
    return accumulator;
  }

  var length = element.children.length; // Optimise for speed.

  for (var index = 0; index < length; index++) {
    var node = element.children[index];

    if (node.nodeName.toLowerCase() !== multilineTag) {
      continue;
    }

    var value = createFromElement({
      element: node,
      range: range,
      multilineTag: multilineTag,
      multilineWrapperTags: multilineWrapperTags,
      currentWrapperTags: currentWrapperTags,
      isEditableTree: isEditableTree,
      preserveWhiteSpace: preserveWhiteSpace
    }); // Multiline value text should be separated by a line separator.

    if (index !== 0 || currentWrapperTags.length > 0) {
      mergePair(accumulator, {
        formats: [,],
        replacements: currentWrapperTags.length > 0 ? [currentWrapperTags] : [,],
        text: LINE_SEPARATOR
      });
    }

    accumulateSelection(accumulator, node, range, value);
    mergePair(accumulator, value);
  }

  return accumulator;
}
/**
 * Gets the attributes of an element in object shape.
 *
 * @param {Object}    $1                 Named argements.
 * @param {Element}   $1.element         Element to get attributes from.
 *
 * @return {?Object} Attribute object or `undefined` if the element has no
 *                   attributes.
 */


function getAttributes(_ref5) {
  var element = _ref5.element;

  if (!element.hasAttributes()) {
    return;
  }

  var length = element.attributes.length;
  var accumulator; // Optimise for speed.

  for (var i = 0; i < length; i++) {
    var _element$attributes$i = element.attributes[i],
        name = _element$attributes$i.name,
        value = _element$attributes$i.value;

    if (name.indexOf('data-rich-text-') === 0) {
      continue;
    }

    accumulator = accumulator || {};
    accumulator[name] = value;
  }

  return accumulator;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/concat.js
/**
 * Internal dependencies
 */


/**
 * Concats a pair of rich text values. Not that this mutates `a` and does NOT
 * normalise formats!
 *
 * @param  {Object} a Value to mutate.
 * @param  {Object} b Value to add read from.
 *
 * @return {Object} `a`, mutated.
 */

function mergePair(a, b) {
  a.formats = a.formats.concat(b.formats);
  a.replacements = a.replacements.concat(b.replacements);
  a.text += b.text;
  return a;
}
/**
 * Combine all Rich Text values into one. This is similar to
 * `String.prototype.concat`.
 *
 * @param {...Object} values Objects to combine.
 *
 * @return {Object} A new value combining all given records.
 */

function concat() {
  for (var _len = arguments.length, values = new Array(_len), _key = 0; _key < _len; _key++) {
    values[_key] = arguments[_key];
  }

  return normaliseFormats(values.reduce(mergePair, create()));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-active-formats.js
/**
 * Gets the all format objects at the start of the selection.
 *
 * @param {Object}        value                Value to inspect.
 * @param {Array<Array>}  value.formats        Formats object data values.
 * @param {number}        value.start          Index to start from.
 * @param {number}        value.end            Index to end.
 * @param {Array}         value.activeFormats  Array to return if there are active formats.
 * @param {Array}         EMPTY_ACTIVE_FORMATS Array to return if there are no active
 *                                             formats.
 *
 * @return {?Object} Active format objects.
 */
function getActiveFormats(_ref) {
  var formats = _ref.formats,
      start = _ref.start,
      end = _ref.end,
      activeFormats = _ref.activeFormats;
  var EMPTY_ACTIVE_FORMATS = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];

  if (start === undefined) {
    return EMPTY_ACTIVE_FORMATS;
  }

  if (start === end) {
    // For a collapsed caret, it is possible to override the active formats.
    if (activeFormats) {
      return activeFormats;
    }

    var formatsBefore = formats[start - 1] || EMPTY_ACTIVE_FORMATS;
    var formatsAfter = formats[start] || EMPTY_ACTIVE_FORMATS; // By default, select the lowest amount of formats possible (which means
    // the caret is positioned outside the format boundary). The user can
    // then use arrow keys to define `activeFormats`.

    if (formatsBefore.length < formatsAfter.length) {
      return formatsBefore;
    }

    return formatsAfter;
  }

  return formats[start] || EMPTY_ACTIVE_FORMATS;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-active-format.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Gets the format object by type at the start of the selection. This can be
 * used to get e.g. the URL of a link format at the current selection, but also
 * to check if a format is active at the selection. Returns undefined if there
 * is no format at the selection.
 *
 * @param {Object} value      Value to inspect.
 * @param {string} formatType Format type to look for.
 *
 * @return {Object|undefined} Active format object of the specified type, or undefined.
 */

function getActiveFormat(value, formatType) {
  return Object(external_this_lodash_["find"])(getActiveFormats(value), {
    type: formatType
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-active-object.js
/**
 * Internal dependencies
 */

/**
 * Gets the active object, if there is any.
 *
 * @param {Object} value Value to inspect.
 *
 * @return {?Object} Active object, or undefined.
 */

function getActiveObject(_ref) {
  var start = _ref.start,
      end = _ref.end,
      replacements = _ref.replacements,
      text = _ref.text;

  if (start + 1 !== end || text[start] !== OBJECT_REPLACEMENT_CHARACTER) {
    return;
  }

  return replacements[start];
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-text-content.js
/**
 * Get the textual content of a Rich Text value. This is similar to
 * `Element.textContent`.
 *
 * @param {Object} value Value to use.
 *
 * @return {string} The text content.
 */
function getTextContent(_ref) {
  var text = _ref.text;
  return text;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-line-index.js
/**
 * Internal dependencies
 */

/**
 * Gets the currently selected line index, or the first line index if the
 * selection spans over multiple items.
 *
 * @param {Object}  value      Value to get the line index from.
 * @param {boolean} startIndex Optional index that should be contained by the
 *                             line. Defaults to the selection start of the
 *                             value.
 *
 * @return {?boolean} The line index. Undefined if not found.
 */

function getLineIndex(_ref) {
  var start = _ref.start,
      text = _ref.text;
  var startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : start;
  var index = startIndex;

  while (index--) {
    if (text[index] === LINE_SEPARATOR) {
      return index;
    }
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-list-root-selected.js
/**
 * Internal dependencies
 */

/**
 * Whether or not the root list is selected.
 *
 * @param {Object} value The value to check.
 *
 * @return {boolean} True if the root list or nothing is selected, false if an
 *                   inner list is selected.
 */

function isListRootSelected(value) {
  var replacements = value.replacements,
      start = value.start;
  var lineIndex = getLineIndex(value, start);
  var replacement = replacements[lineIndex];
  return !replacement || replacement.length < 1;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-active-list-type.js
/**
 * Internal dependencies
 */

/**
 * Wether or not the selected list has the given tag name.
 *
 * @param {Object}  value    The value to check.
 * @param {string}  type     The tag name the list should have.
 * @param {string}  rootType The current root tag name, to compare with in case
 *                           nothing is selected.
 *
 * @return {boolean} True if the current list type matches `type`, false if not.
 */

function isActiveListType(value, type, rootType) {
  var replacements = value.replacements,
      start = value.start;
  var lineIndex = getLineIndex(value, start);
  var replacement = replacements[lineIndex];

  if (!replacement || replacement.length === 0) {
    return type === rootType;
  }

  var lastFormat = replacement[replacement.length - 1];
  return lastFormat.type === type;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-collapsed.js
/**
 * Check if the selection of a Rich Text value is collapsed or not. Collapsed
 * means that no characters are selected, but there is a caret present. If there
 * is no selection, `undefined` will be returned. This is similar to
 * `window.getSelection().isCollapsed()`.
 *
 * @param {Object} value The rich text value to check.
 *
 * @return {boolean|undefined} True if the selection is collapsed, false if not,
 *                             undefined if there is no selection.
 */
function isCollapsed(_ref) {
  var start = _ref.start,
      end = _ref.end;

  if (start === undefined || end === undefined) {
    return;
  }

  return start === end;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-empty.js
/**
 * Internal dependencies
 */

/**
 * Check if a Rich Text value is Empty, meaning it contains no text or any
 * objects (such as images).
 *
 * @param {Object} value Value to use.
 *
 * @return {boolean} True if the value is empty, false if not.
 */

function isEmpty(_ref) {
  var text = _ref.text;
  return text.length === 0;
}
/**
 * Check if the current collapsed selection is on an empty line in case of a
 * multiline value.
 *
 * @param  {Object} value Value te check.
 *
 * @return {boolean} True if the line is empty, false if not.
 */

function isEmptyLine(_ref2) {
  var text = _ref2.text,
      start = _ref2.start,
      end = _ref2.end;

  if (start !== end) {
    return false;
  }

  if (text.length === 0) {
    return true;
  }

  if (start === 0 && text.slice(0, 1) === LINE_SEPARATOR) {
    return true;
  }

  if (start === text.length && text.slice(-1) === LINE_SEPARATOR) {
    return true;
  }

  return text.slice(start - 1, end + 1) === "".concat(LINE_SEPARATOR).concat(LINE_SEPARATOR);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/join.js
/**
 * Internal dependencies
 */


/**
 * Combine an array of Rich Text values into one, optionally separated by
 * `separator`, which can be a Rich Text value, HTML string, or plain text
 * string. This is similar to `Array.prototype.join`.
 *
 * @param {Array<Object>} values      An array of values to join.
 * @param {string|Object} [separator] Separator string or value.
 *
 * @return {Object} A new combined value.
 */

function join(values) {
  var separator = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

  if (typeof separator === 'string') {
    separator = create({
      text: separator
    });
  }

  return normaliseFormats(values.reduce(function (accumlator, _ref) {
    var formats = _ref.formats,
        replacements = _ref.replacements,
        text = _ref.text;
    return {
      formats: accumlator.formats.concat(separator.formats, formats),
      replacements: accumlator.replacements.concat(separator.replacements, replacements),
      text: accumlator.text + separator.text + text
    };
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/register-format-type.js


function register_format_type_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function register_format_type_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { register_format_type_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { register_format_type_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * WordPress dependencies
 */

/**
 * @typedef {Object} WPFormat
 *
 * @property {string}   name        A string identifying the format. Must be
 *                                  unique across all registered formats.
 * @property {string}   tagName     The HTML tag this format will wrap the
 *                                  selection with.
 * @property {string}   [className] A class to match the format.
 * @property {string}   title       Name of the format.
 * @property {Function} edit        Should return a component for the user to
 *                                  interact with the new registered format.
 */

/**
 * Registers a new format provided a unique name and an object defining its
 * behavior.
 *
 * @param {string}   name                 Format name.
 * @param {WPFormat} settings             Format settings.
 *
 * @return {WPFormat|undefined} The format, if it has been successfully registered;
 *                              otherwise `undefined`.
 */

function registerFormatType(name, settings) {
  settings = register_format_type_objectSpread({
    name: name
  }, settings);

  if (typeof settings.name !== 'string') {
    window.console.error('Format names must be strings.');
    return;
  }

  if (!/^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/.test(settings.name)) {
    window.console.error('Format names must contain a namespace prefix, include only lowercase alphanumeric characters or dashes, and start with a letter. Example: my-plugin/my-custom-format');
    return;
  }

  if (Object(external_this_wp_data_["select"])('core/rich-text').getFormatType(settings.name)) {
    window.console.error('Format "' + settings.name + '" is already registered.');
    return;
  }

  if (typeof settings.tagName !== 'string' || settings.tagName === '') {
    window.console.error('Format tag names must be a string.');
    return;
  }

  if ((typeof settings.className !== 'string' || settings.className === '') && settings.className !== null) {
    window.console.error('Format class names must be a string, or null to handle bare elements.');
    return;
  }

  if (!/^[_a-zA-Z]+[a-zA-Z0-9-]*$/.test(settings.className)) {
    window.console.error('A class name must begin with a letter, followed by any number of hyphens, letters, or numbers.');
    return;
  }

  if (settings.className === null) {
    var formatTypeForBareElement = Object(external_this_wp_data_["select"])('core/rich-text').getFormatTypeForBareElement(settings.tagName);

    if (formatTypeForBareElement) {
      window.console.error("Format \"".concat(formatTypeForBareElement.name, "\" is already registered to handle bare tag name \"").concat(settings.tagName, "\"."));
      return;
    }
  } else {
    var formatTypeForClassName = Object(external_this_wp_data_["select"])('core/rich-text').getFormatTypeForClassName(settings.className);

    if (formatTypeForClassName) {
      window.console.error("Format \"".concat(formatTypeForClassName.name, "\" is already registered to handle class name \"").concat(settings.className, "\"."));
      return;
    }
  }

  if (!('title' in settings) || settings.title === '') {
    window.console.error('The format "' + settings.name + '" must have a title.');
    return;
  }

  if ('keywords' in settings && settings.keywords.length > 3) {
    window.console.error('The format "' + settings.name + '" can have a maximum of 3 keywords.');
    return;
  }

  if (typeof settings.title !== 'string') {
    window.console.error('Format titles must be strings.');
    return;
  }

  Object(external_this_wp_data_["dispatch"])('core/rich-text').addFormatTypes(settings);
  return settings;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/remove-format.js


function remove_format_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function remove_format_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { remove_format_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { remove_format_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Remove any format object from a Rich Text value by type from the given
 * `startIndex` to the given `endIndex`. Indices are retrieved from the
 * selection if none are provided.
 *
 * @param {Object} value        Value to modify.
 * @param {string} formatType   Format type to remove.
 * @param {number} [startIndex] Start index.
 * @param {number} [endIndex]   End index.
 *
 * @return {Object} A new value with the format applied.
 */

function removeFormat(value, formatType) {
  var startIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : value.start;
  var endIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : value.end;
  var formats = value.formats,
      activeFormats = value.activeFormats;
  var newFormats = formats.slice(); // If the selection is collapsed, expand start and end to the edges of the
  // format.

  if (startIndex === endIndex) {
    var format = Object(external_this_lodash_["find"])(newFormats[startIndex], {
      type: formatType
    });

    if (format) {
      while (Object(external_this_lodash_["find"])(newFormats[startIndex], format)) {
        filterFormats(newFormats, startIndex, formatType);
        startIndex--;
      }

      endIndex++;

      while (Object(external_this_lodash_["find"])(newFormats[endIndex], format)) {
        filterFormats(newFormats, endIndex, formatType);
        endIndex++;
      }
    }
  } else {
    for (var i = startIndex; i < endIndex; i++) {
      if (newFormats[i]) {
        filterFormats(newFormats, i, formatType);
      }
    }
  }

  return normaliseFormats(remove_format_objectSpread({}, value, {
    formats: newFormats,
    activeFormats: Object(external_this_lodash_["reject"])(activeFormats, {
      type: formatType
    })
  }));
}

function filterFormats(formats, index, formatType) {
  var newFormats = formats[index].filter(function (_ref) {
    var type = _ref.type;
    return type !== formatType;
  });

  if (newFormats.length) {
    formats[index] = newFormats;
  } else {
    delete formats[index];
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/insert.js
/**
 * Internal dependencies
 */


/**
 * Insert a Rich Text value, an HTML string, or a plain text string, into a
 * Rich Text value at the given `startIndex`. Any content between `startIndex`
 * and `endIndex` will be removed. Indices are retrieved from the selection if
 * none are provided.
 *
 * @param {Object}        value         Value to modify.
 * @param {Object|string} valueToInsert Value to insert.
 * @param {number}        [startIndex]  Start index.
 * @param {number}        [endIndex]    End index.
 *
 * @return {Object} A new value with the value inserted.
 */

function insert(value, valueToInsert) {
  var startIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : value.start;
  var endIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : value.end;
  var formats = value.formats,
      replacements = value.replacements,
      text = value.text;

  if (typeof valueToInsert === 'string') {
    valueToInsert = create({
      text: valueToInsert
    });
  }

  var index = startIndex + valueToInsert.text.length;
  return normaliseFormats({
    formats: formats.slice(0, startIndex).concat(valueToInsert.formats, formats.slice(endIndex)),
    replacements: replacements.slice(0, startIndex).concat(valueToInsert.replacements, replacements.slice(endIndex)),
    text: text.slice(0, startIndex) + valueToInsert.text + text.slice(endIndex),
    start: index,
    end: index
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/remove.js
/**
 * Internal dependencies
 */


/**
 * Remove content from a Rich Text value between the given `startIndex` and
 * `endIndex`. Indices are retrieved from the selection if none are provided.
 *
 * @param {Object} value        Value to modify.
 * @param {number} [startIndex] Start index.
 * @param {number} [endIndex]   End index.
 *
 * @return {Object} A new value with the content removed.
 */

function remove_remove(value, startIndex, endIndex) {
  return insert(value, create(), startIndex, endIndex);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/replace.js


/**
 * Internal dependencies
 */

/**
 * Search a Rich Text value and replace the match(es) with `replacement`. This
 * is similar to `String.prototype.replace`.
 *
 * @param {Object}         value        The value to modify.
 * @param {RegExp|string}  pattern      A RegExp object or literal. Can also be
 *                                      a string. It is treated as a verbatim
 *                                      string and is not interpreted as a
 *                                      regular expression. Only the first
 *                                      occurrence will be replaced.
 * @param {Function|string} replacement The match or matches are replaced with
 *                                      the specified or the value returned by
 *                                      the specified function.
 *
 * @return {Object} A new value with replacements applied.
 */

function replace_replace(_ref, pattern, replacement) {
  var formats = _ref.formats,
      replacements = _ref.replacements,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end;
  text = text.replace(pattern, function (match) {
    for (var _len = arguments.length, rest = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      rest[_key - 1] = arguments[_key];
    }

    var offset = rest[rest.length - 2];
    var newText = replacement;
    var newFormats;
    var newReplacements;

    if (typeof newText === 'function') {
      newText = replacement.apply(void 0, [match].concat(rest));
    }

    if (Object(esm_typeof["a" /* default */])(newText) === 'object') {
      newFormats = newText.formats;
      newReplacements = newText.replacements;
      newText = newText.text;
    } else {
      newFormats = Array(newText.length);
      newReplacements = Array(newText.length);

      if (formats[offset]) {
        newFormats = newFormats.fill(formats[offset]);
      }
    }

    formats = formats.slice(0, offset).concat(newFormats, formats.slice(offset + match.length));
    replacements = replacements.slice(0, offset).concat(newReplacements, replacements.slice(offset + match.length));

    if (start) {
      start = end = offset + newText.length;
    }

    return newText;
  });
  return normaliseFormats({
    formats: formats,
    replacements: replacements,
    text: text,
    start: start,
    end: end
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/insert-line-separator.js
/**
 * Internal dependencies
 */



/**
 * Insert a line break character into a Rich Text value at the given
 * `startIndex`. Any content between `startIndex` and `endIndex` will be
 * removed. Indices are retrieved from the selection if none are provided.
 *
 * @param {Object} value        Value to modify.
 * @param {number} [startIndex] Start index.
 * @param {number} [endIndex]   End index.
 *
 * @return {Object} A new value with the value inserted.
 */

function insertLineSeparator(value) {
  var startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : value.start;
  var endIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : value.end;
  var beforeText = getTextContent(value).slice(0, startIndex);
  var previousLineSeparatorIndex = beforeText.lastIndexOf(LINE_SEPARATOR);
  var previousLineSeparatorFormats = value.replacements[previousLineSeparatorIndex];
  var replacements = [,];

  if (previousLineSeparatorFormats) {
    replacements = [previousLineSeparatorFormats];
  }

  var valueToInsert = {
    formats: [,],
    replacements: replacements,
    text: LINE_SEPARATOR
  };
  return insert(value, valueToInsert, startIndex, endIndex);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/remove-line-separator.js


function remove_line_separator_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function remove_line_separator_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { remove_line_separator_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { remove_line_separator_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Internal dependencies
 */



/**
 * Removes a line separator character, if existing, from a Rich Text value at the current
 * indices. If no line separator exists on the indices it will return undefined.
 *
 * @param {Object} value Value to modify.
 * @param {boolean} backward indicates if are removing from the start index or the end index.
 *
 * @return {Object|undefined} A new value with the line separator removed. Or undefined if no line separator is found on the position.
 */

function removeLineSeparator(value) {
  var backward = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
  var replacements = value.replacements,
      text = value.text,
      start = value.start,
      end = value.end;
  var collapsed = isCollapsed(value);
  var index = start - 1;
  var removeStart = collapsed ? start - 1 : start;
  var removeEnd = end;

  if (!backward) {
    index = end;
    removeStart = start;
    removeEnd = collapsed ? end + 1 : end;
  }

  if (text[index] !== LINE_SEPARATOR) {
    return;
  }

  var newValue; // If the line separator that is about te be removed
  // contains wrappers, remove the wrappers first.

  if (collapsed && replacements[index] && replacements[index].length) {
    var newReplacements = replacements.slice();
    newReplacements[index] = replacements[index].slice(0, -1);
    newValue = remove_line_separator_objectSpread({}, value, {
      replacements: newReplacements
    });
  } else {
    newValue = remove_remove(value, removeStart, removeEnd);
  }

  return newValue;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/insert-object.js
/**
 * Internal dependencies
 */

var insert_object_OBJECT_REPLACEMENT_CHARACTER = "\uFFFC";
/**
 * Insert a format as an object into a Rich Text value at the given
 * `startIndex`. Any content between `startIndex` and `endIndex` will be
 * removed. Indices are retrieved from the selection if none are provided.
 *
 * @param {Object} value          Value to modify.
 * @param {Object} formatToInsert Format to insert as object.
 * @param {number} [startIndex]   Start index.
 * @param {number} [endIndex]     End index.
 *
 * @return {Object} A new value with the object inserted.
 */

function insertObject(value, formatToInsert, startIndex, endIndex) {
  var valueToInsert = {
    formats: [,],
    replacements: [formatToInsert],
    text: insert_object_OBJECT_REPLACEMENT_CHARACTER
  };
  return insert(value, valueToInsert, startIndex, endIndex);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/slice.js


function slice_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function slice_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { slice_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { slice_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Slice a Rich Text value from `startIndex` to `endIndex`. Indices are
 * retrieved from the selection if none are provided. This is similar to
 * `String.prototype.slice`.
 *
 * @param {Object} value        Value to modify.
 * @param {number} [startIndex] Start index.
 * @param {number} [endIndex]   End index.
 *
 * @return {Object} A new extracted value.
 */
function slice(value) {
  var startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : value.start;
  var endIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : value.end;
  var formats = value.formats,
      replacements = value.replacements,
      text = value.text;

  if (startIndex === undefined || endIndex === undefined) {
    return slice_objectSpread({}, value);
  }

  return {
    formats: formats.slice(startIndex, endIndex),
    replacements: replacements.slice(startIndex, endIndex),
    text: text.slice(startIndex, endIndex)
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/split.js
/**
 * Internal dependencies
 */

/**
 * Split a Rich Text value in two at the given `startIndex` and `endIndex`, or
 * split at the given separator. This is similar to `String.prototype.split`.
 * Indices are retrieved from the selection if none are provided.
 *
 * @param {Object}        value
 * @param {Object[]}      value.formats
 * @param {Object[]}      value.replacements
 * @param {string}        value.text
 * @param {number}        value.start
 * @param {number}        value.end
 * @param {number|string} [string] Start index, or string at which to split.
 *
 * @return {Array} An array of new values.
 */

function split(_ref, string) {
  var formats = _ref.formats,
      replacements = _ref.replacements,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end;

  if (typeof string !== 'string') {
    return splitAtSelection.apply(void 0, arguments);
  }

  var nextStart = 0;
  return text.split(string).map(function (substring) {
    var startIndex = nextStart;
    var value = {
      formats: formats.slice(startIndex, startIndex + substring.length),
      replacements: replacements.slice(startIndex, startIndex + substring.length),
      text: substring
    };
    nextStart += string.length + substring.length;

    if (start !== undefined && end !== undefined) {
      if (start >= startIndex && start < nextStart) {
        value.start = start - startIndex;
      } else if (start < startIndex && end > startIndex) {
        value.start = 0;
      }

      if (end >= startIndex && end < nextStart) {
        value.end = end - startIndex;
      } else if (start < nextStart && end > nextStart) {
        value.end = substring.length;
      }
    }

    return value;
  });
}

function splitAtSelection(_ref2) {
  var formats = _ref2.formats,
      replacements = _ref2.replacements,
      text = _ref2.text,
      start = _ref2.start,
      end = _ref2.end;
  var startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : start;
  var endIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : end;
  var before = {
    formats: formats.slice(0, startIndex),
    replacements: replacements.slice(0, startIndex),
    text: text.slice(0, startIndex)
  };
  var after = {
    formats: formats.slice(endIndex),
    replacements: replacements.slice(endIndex),
    text: text.slice(endIndex),
    start: 0,
    end: 0
  };
  return [// Ensure newlines are trimmed.
  replace_replace(before, /\u2028+$/, ''), replace_replace(after, /^\u2028+/, '')];
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-format-type.js
/**
 * WordPress dependencies
 */

/**
 * Returns a registered format type.
 *
 * @param {string} name Format name.
 *
 * @return {?Object} Format type.
 */

function get_format_type_getFormatType(name) {
  return Object(external_this_wp_data_["select"])('core/rich-text').getFormatType(name);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/to-tree.js



function to_tree_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function to_tree_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { to_tree_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { to_tree_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Internal dependencies
 */



/**
 * Converts a format object to information that can be used to create an element
 * from (type, attributes and object).
 *
 * @param  {Object}  $1                        Named parameters.
 * @param  {string}  $1.type                   The format type.
 * @param  {Object}  $1.attributes             The format attributes.
 * @param  {Object}  $1.unregisteredAttributes The unregistered format
 *                                             attributes.
 * @param  {boolean} $1.object                 Wether or not it is an object
 *                                             format.
 * @param  {boolean} $1.boundaryClass          Wether or not to apply a boundary
 *                                             class.
 * @return {Object}                            Information to be used for
 *                                             element creation.
 */

function fromFormat(_ref) {
  var type = _ref.type,
      attributes = _ref.attributes,
      unregisteredAttributes = _ref.unregisteredAttributes,
      object = _ref.object,
      boundaryClass = _ref.boundaryClass;
  var formatType = get_format_type_getFormatType(type);
  var elementAttributes = {};

  if (boundaryClass) {
    elementAttributes['data-rich-text-format-boundary'] = 'true';
  }

  if (!formatType) {
    if (attributes) {
      elementAttributes = to_tree_objectSpread({}, attributes, {}, elementAttributes);
    }

    return {
      type: type,
      attributes: elementAttributes,
      object: object
    };
  }

  elementAttributes = to_tree_objectSpread({}, unregisteredAttributes, {}, elementAttributes);

  for (var name in attributes) {
    var key = formatType.attributes ? formatType.attributes[name] : false;

    if (key) {
      elementAttributes[key] = attributes[name];
    } else {
      elementAttributes[name] = attributes[name];
    }
  }

  if (formatType.className) {
    if (elementAttributes.class) {
      elementAttributes.class = "".concat(formatType.className, " ").concat(elementAttributes.class);
    } else {
      elementAttributes.class = formatType.className;
    }
  }

  return {
    type: formatType.tagName,
    object: formatType.object,
    attributes: elementAttributes
  };
}
/**
 * Checks if both arrays of formats up until a certain index are equal.
 *
 * @param {Array}  a     Array of formats to compare.
 * @param {Array}  b     Array of formats to compare.
 * @param {number} index Index to check until.
 */


function isEqualUntil(a, b, index) {
  do {
    if (a[index] !== b[index]) {
      return false;
    }
  } while (index--);

  return true;
}

function toTree(_ref2) {
  var value = _ref2.value,
      multilineTag = _ref2.multilineTag,
      preserveWhiteSpace = _ref2.preserveWhiteSpace,
      createEmpty = _ref2.createEmpty,
      append = _ref2.append,
      getLastChild = _ref2.getLastChild,
      getParent = _ref2.getParent,
      isText = _ref2.isText,
      getText = _ref2.getText,
      remove = _ref2.remove,
      appendText = _ref2.appendText,
      onStartIndex = _ref2.onStartIndex,
      onEndIndex = _ref2.onEndIndex,
      isEditableTree = _ref2.isEditableTree,
      placeholder = _ref2.placeholder;
  var formats = value.formats,
      replacements = value.replacements,
      text = value.text,
      start = value.start,
      end = value.end;
  var formatsLength = formats.length + 1;
  var tree = createEmpty();
  var multilineFormat = {
    type: multilineTag
  };
  var activeFormats = getActiveFormats(value);
  var deepestActiveFormat = activeFormats[activeFormats.length - 1];
  var lastSeparatorFormats;
  var lastCharacterFormats;
  var lastCharacter; // If we're building a multiline tree, start off with a multiline element.

  if (multilineTag) {
    append(append(tree, {
      type: multilineTag
    }), '');
    lastCharacterFormats = lastSeparatorFormats = [multilineFormat];
  } else {
    append(tree, '');
  }

  var _loop = function _loop(i) {
    var character = text.charAt(i);
    var shouldInsertPadding = isEditableTree && ( // Pad the line if the line is empty.
    !lastCharacter || lastCharacter === LINE_SEPARATOR || // Pad the line if the previous character is a line break, otherwise
    // the line break won't be visible.
    lastCharacter === '\n');
    var characterFormats = formats[i]; // Set multiline tags in queue for building the tree.

    if (multilineTag) {
      if (character === LINE_SEPARATOR) {
        characterFormats = lastSeparatorFormats = (replacements[i] || []).reduce(function (accumulator, format) {
          accumulator.push(format, multilineFormat);
          return accumulator;
        }, [multilineFormat]);
      } else {
        characterFormats = [].concat(Object(toConsumableArray["a" /* default */])(lastSeparatorFormats), Object(toConsumableArray["a" /* default */])(characterFormats || []));
      }
    }

    var pointer = getLastChild(tree);

    if (shouldInsertPadding && character === LINE_SEPARATOR) {
      var node = pointer;

      while (!isText(node)) {
        node = getLastChild(node);
      }

      append(getParent(node), ZWNBSP);
    } // Set selection for the start of line.


    if (lastCharacter === LINE_SEPARATOR) {
      var _node = pointer;

      while (!isText(_node)) {
        _node = getLastChild(_node);
      }

      if (onStartIndex && start === i) {
        onStartIndex(tree, _node);
      }

      if (onEndIndex && end === i) {
        onEndIndex(tree, _node);
      }
    }

    if (characterFormats) {
      characterFormats.forEach(function (format, formatIndex) {
        if (pointer && lastCharacterFormats && // Reuse the last element if all formats remain the same.
        isEqualUntil(characterFormats, lastCharacterFormats, formatIndex) && ( // Do not reuse the last element if the character is a
        // line separator.
        character !== LINE_SEPARATOR || characterFormats.length - 1 !== formatIndex)) {
          pointer = getLastChild(pointer);
          return;
        }

        var type = format.type,
            attributes = format.attributes,
            unregisteredAttributes = format.unregisteredAttributes;
        var boundaryClass = isEditableTree && character !== LINE_SEPARATOR && format === deepestActiveFormat;
        var parent = getParent(pointer);
        var newNode = append(parent, fromFormat({
          type: type,
          attributes: attributes,
          unregisteredAttributes: unregisteredAttributes,
          boundaryClass: boundaryClass
        }));

        if (isText(pointer) && getText(pointer).length === 0) {
          remove(pointer);
        }

        pointer = append(newNode, '');
      });
    } // No need for further processing if the character is a line separator.


    if (character === LINE_SEPARATOR) {
      lastCharacterFormats = characterFormats;
      lastCharacter = character;
      return "continue";
    } // If there is selection at 0, handle it before characters are inserted.


    if (i === 0) {
      if (onStartIndex && start === 0) {
        onStartIndex(tree, pointer);
      }

      if (onEndIndex && end === 0) {
        onEndIndex(tree, pointer);
      }
    }

    if (character === OBJECT_REPLACEMENT_CHARACTER) {
      pointer = append(getParent(pointer), fromFormat(to_tree_objectSpread({}, replacements[i], {
        object: true
      }))); // Ensure pointer is text node.

      pointer = append(getParent(pointer), '');
    } else if (!preserveWhiteSpace && character === '\n') {
      pointer = append(getParent(pointer), {
        type: 'br',
        attributes: isEditableTree ? {
          'data-rich-text-line-break': 'true'
        } : undefined,
        object: true
      }); // Ensure pointer is text node.

      pointer = append(getParent(pointer), '');
    } else if (!isText(pointer)) {
      pointer = append(getParent(pointer), character);
    } else {
      appendText(pointer, character);
    }

    if (onStartIndex && start === i + 1) {
      onStartIndex(tree, pointer);
    }

    if (onEndIndex && end === i + 1) {
      onEndIndex(tree, pointer);
    }

    if (shouldInsertPadding && i === text.length) {
      append(getParent(pointer), ZWNBSP);

      if (placeholder && text.length === 0) {
        append(getParent(pointer), {
          type: 'span',
          attributes: {
            'data-rich-text-placeholder': placeholder,
            // Necessary to prevent the placeholder from catching
            // selection. The placeholder is also not editable after
            // all.
            contenteditable: 'false'
          }
        });
      }
    }

    lastCharacterFormats = characterFormats;
    lastCharacter = character;
  };

  for (var i = 0; i < formatsLength; i++) {
    var _ret = _loop(i);

    if (_ret === "continue") continue;
  }

  return tree;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/to-dom.js



function to_dom_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function to_dom_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { to_dom_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { to_dom_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Internal dependencies
 */


/**
 * Creates a path as an array of indices from the given root node to the given
 * node.
 *
 * @param {Node}        node     Node to find the path of.
 * @param {HTMLElement} rootNode Root node to find the path from.
 * @param {Array}       path     Initial path to build on.
 *
 * @return {Array} The path from the root node to the node.
 */

function createPathToNode(node, rootNode, path) {
  var parentNode = node.parentNode;
  var i = 0;

  while (node = node.previousSibling) {
    i++;
  }

  path = [i].concat(Object(toConsumableArray["a" /* default */])(path));

  if (parentNode !== rootNode) {
    path = createPathToNode(parentNode, rootNode, path);
  }

  return path;
}
/**
 * Gets a node given a path (array of indices) from the given node.
 *
 * @param {HTMLElement} node Root node to find the wanted node in.
 * @param {Array}       path Path (indices) to the wanted node.
 *
 * @return {Object} Object with the found node and the remaining offset (if any).
 */


function getNodeByPath(node, path) {
  path = Object(toConsumableArray["a" /* default */])(path);

  while (node && path.length > 1) {
    node = node.childNodes[path.shift()];
  }

  return {
    node: node,
    offset: path[0]
  };
}

function to_dom_append(element, child) {
  if (typeof child === 'string') {
    child = element.ownerDocument.createTextNode(child);
  }

  var _child = child,
      type = _child.type,
      attributes = _child.attributes;

  if (type) {
    child = element.ownerDocument.createElement(type);

    for (var key in attributes) {
      child.setAttribute(key, attributes[key]);
    }
  }

  return element.appendChild(child);
}

function to_dom_appendText(node, text) {
  node.appendData(text);
}

function to_dom_getLastChild(_ref) {
  var lastChild = _ref.lastChild;
  return lastChild;
}

function to_dom_getParent(_ref2) {
  var parentNode = _ref2.parentNode;
  return parentNode;
}

function to_dom_isText(node) {
  return node.nodeType === node.TEXT_NODE;
}

function to_dom_getText(_ref3) {
  var nodeValue = _ref3.nodeValue;
  return nodeValue;
}

function to_dom_remove(node) {
  return node.parentNode.removeChild(node);
}

function toDom(_ref4) {
  var value = _ref4.value,
      multilineTag = _ref4.multilineTag,
      prepareEditableTree = _ref4.prepareEditableTree,
      _ref4$isEditableTree = _ref4.isEditableTree,
      isEditableTree = _ref4$isEditableTree === void 0 ? true : _ref4$isEditableTree,
      placeholder = _ref4.placeholder,
      _ref4$doc = _ref4.doc,
      doc = _ref4$doc === void 0 ? document : _ref4$doc;
  var startPath = [];
  var endPath = [];

  if (prepareEditableTree) {
    value = to_dom_objectSpread({}, value, {
      formats: prepareEditableTree(value)
    });
  }
  /**
   * Returns a new instance of a DOM tree upon which RichText operations can be
   * applied.
   *
   * Note: The current implementation will return a shared reference, reset on
   * each call to `createEmpty`. Therefore, you should not hold a reference to
   * the value to operate upon asynchronously, as it may have unexpected results.
   *
   * @return {Object} RichText tree.
   */


  var createEmpty = function createEmpty() {
    return createElement(doc, '');
  };

  var tree = toTree({
    value: value,
    multilineTag: multilineTag,
    createEmpty: createEmpty,
    append: to_dom_append,
    getLastChild: to_dom_getLastChild,
    getParent: to_dom_getParent,
    isText: to_dom_isText,
    getText: to_dom_getText,
    remove: to_dom_remove,
    appendText: to_dom_appendText,
    onStartIndex: function onStartIndex(body, pointer) {
      startPath = createPathToNode(pointer, body, [pointer.nodeValue.length]);
    },
    onEndIndex: function onEndIndex(body, pointer) {
      endPath = createPathToNode(pointer, body, [pointer.nodeValue.length]);
    },
    isEditableTree: isEditableTree,
    placeholder: placeholder
  });
  return {
    body: tree,
    selection: {
      startPath: startPath,
      endPath: endPath
    }
  };
}
/**
 * Create an `Element` tree from a Rich Text value and applies the difference to
 * the `Element` tree contained by `current`. If a `multilineTag` is provided,
 * text separated by two new lines will be wrapped in an `Element` of that type.
 *
 * @param {Object}      $1                        Named arguments.
 * @param {Object}      $1.value                  Value to apply.
 * @param {HTMLElement} $1.current                The live root node to apply the element tree to.
 * @param {string}      [$1.multilineTag]         Multiline tag.
 * @param {Array}       [$1.multilineWrapperTags] Tags where lines can be found if nesting is possible.
 */

function apply(_ref5) {
  var value = _ref5.value,
      current = _ref5.current,
      multilineTag = _ref5.multilineTag,
      prepareEditableTree = _ref5.prepareEditableTree,
      __unstableDomOnly = _ref5.__unstableDomOnly,
      placeholder = _ref5.placeholder;

  // Construct a new element tree in memory.
  var _toDom = toDom({
    value: value,
    multilineTag: multilineTag,
    prepareEditableTree: prepareEditableTree,
    placeholder: placeholder,
    doc: current.ownerDocument
  }),
      body = _toDom.body,
      selection = _toDom.selection;

  applyValue(body, current);

  if (value.start !== undefined && !__unstableDomOnly) {
    applySelection(selection, current);
  }
}
function applyValue(future, current) {
  var i = 0;
  var futureChild;

  while (futureChild = future.firstChild) {
    var currentChild = current.childNodes[i];

    if (!currentChild) {
      current.appendChild(futureChild);
    } else if (!currentChild.isEqualNode(futureChild)) {
      if (currentChild.nodeName !== futureChild.nodeName || currentChild.nodeType === currentChild.TEXT_NODE && currentChild.data !== futureChild.data) {
        current.replaceChild(futureChild, currentChild);
      } else {
        var currentAttributes = currentChild.attributes;
        var futureAttributes = futureChild.attributes;

        if (currentAttributes) {
          var ii = currentAttributes.length; // Reverse loop because `removeAttribute` on `currentChild`
          // changes `currentAttributes`.

          while (ii--) {
            var name = currentAttributes[ii].name;

            if (!futureChild.getAttribute(name)) {
              currentChild.removeAttribute(name);
            }
          }
        }

        if (futureAttributes) {
          for (var _ii = 0; _ii < futureAttributes.length; _ii++) {
            var _futureAttributes$_ii = futureAttributes[_ii],
                _name = _futureAttributes$_ii.name,
                value = _futureAttributes$_ii.value;

            if (currentChild.getAttribute(_name) !== value) {
              currentChild.setAttribute(_name, value);
            }
          }
        }

        applyValue(futureChild, currentChild);
        future.removeChild(futureChild);
      }
    } else {
      future.removeChild(futureChild);
    }

    i++;
  }

  while (current.childNodes[i]) {
    current.removeChild(current.childNodes[i]);
  }
}
/**
 * Returns true if two ranges are equal, or false otherwise. Ranges are
 * considered equal if their start and end occur in the same container and
 * offset.
 *
 * @param {Range} a First range object to test.
 * @param {Range} b First range object to test.
 *
 * @return {boolean} Whether the two ranges are equal.
 */

function isRangeEqual(a, b) {
  return a.startContainer === b.startContainer && a.startOffset === b.startOffset && a.endContainer === b.endContainer && a.endOffset === b.endOffset;
}

function applySelection(_ref6, current) {
  var startPath = _ref6.startPath,
      endPath = _ref6.endPath;

  var _getNodeByPath = getNodeByPath(current, startPath),
      startContainer = _getNodeByPath.node,
      startOffset = _getNodeByPath.offset;

  var _getNodeByPath2 = getNodeByPath(current, endPath),
      endContainer = _getNodeByPath2.node,
      endOffset = _getNodeByPath2.offset;

  var ownerDocument = current.ownerDocument;
  var defaultView = ownerDocument.defaultView;
  var selection = defaultView.getSelection();
  var range = ownerDocument.createRange();
  range.setStart(startContainer, startOffset);
  range.setEnd(endContainer, endOffset);
  var activeElement = ownerDocument.activeElement;

  if (selection.rangeCount > 0) {
    // If the to be added range and the live range are the same, there's no
    // need to remove the live range and add the equivalent range.
    if (isRangeEqual(range, selection.getRangeAt(0))) {
      return;
    }

    selection.removeAllRanges();
  }

  selection.addRange(range); // This function is not intended to cause a shift in focus. Since the above
  // selection manipulations may shift focus, ensure that focus is restored to
  // its previous state.

  if (activeElement !== ownerDocument.activeElement) {
    // The `instanceof` checks protect against edge cases where the focused
    // element is not of the interface HTMLElement (does not have a `focus`
    // or `blur` property).
    //
    // See: https://github.com/Microsoft/TypeScript/issues/5901#issuecomment-431649653
    if (activeElement instanceof defaultView.HTMLElement) {
      activeElement.focus();
    }
  }
}

// EXTERNAL MODULE: external {"this":["wp","escapeHtml"]}
var external_this_wp_escapeHtml_ = __webpack_require__(89);

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/to-html-string.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Create an HTML string from a Rich Text value. If a `multilineTag` is
 * provided, text separated by a line separator will be wrapped in it.
 *
 * @param {Object}   $1                      Named argements.
 * @param {Object}   $1.value                Rich text value.
 * @param {string}   [$1.multilineTag]       Multiline tag.
 * @param {?boolean} [$1.preserveWhiteSpace] Whether or not to use newline
 *                                           characters for line breaks.
 *
 * @return {string} HTML string.
 */

function toHTMLString(_ref) {
  var value = _ref.value,
      multilineTag = _ref.multilineTag,
      preserveWhiteSpace = _ref.preserveWhiteSpace;
  var tree = toTree({
    value: value,
    multilineTag: multilineTag,
    preserveWhiteSpace: preserveWhiteSpace,
    createEmpty: to_html_string_createEmpty,
    append: to_html_string_append,
    getLastChild: to_html_string_getLastChild,
    getParent: to_html_string_getParent,
    isText: to_html_string_isText,
    getText: to_html_string_getText,
    remove: to_html_string_remove,
    appendText: to_html_string_appendText
  });
  return createChildrenHTML(tree.children);
}

function to_html_string_createEmpty() {
  return {};
}

function to_html_string_getLastChild(_ref2) {
  var children = _ref2.children;
  return children && children[children.length - 1];
}

function to_html_string_append(parent, object) {
  if (typeof object === 'string') {
    object = {
      text: object
    };
  }

  object.parent = parent;
  parent.children = parent.children || [];
  parent.children.push(object);
  return object;
}

function to_html_string_appendText(object, text) {
  object.text += text;
}

function to_html_string_getParent(_ref3) {
  var parent = _ref3.parent;
  return parent;
}

function to_html_string_isText(_ref4) {
  var text = _ref4.text;
  return typeof text === 'string';
}

function to_html_string_getText(_ref5) {
  var text = _ref5.text;
  return text;
}

function to_html_string_remove(object) {
  var index = object.parent.children.indexOf(object);

  if (index !== -1) {
    object.parent.children.splice(index, 1);
  }

  return object;
}

function createElementHTML(_ref6) {
  var type = _ref6.type,
      attributes = _ref6.attributes,
      object = _ref6.object,
      children = _ref6.children;
  var attributeString = '';

  for (var key in attributes) {
    if (!Object(external_this_wp_escapeHtml_["isValidAttributeName"])(key)) {
      continue;
    }

    attributeString += " ".concat(key, "=\"").concat(Object(external_this_wp_escapeHtml_["escapeAttribute"])(attributes[key]), "\"");
  }

  if (object) {
    return "<".concat(type).concat(attributeString, ">");
  }

  return "<".concat(type).concat(attributeString, ">").concat(createChildrenHTML(children), "</").concat(type, ">");
}

function createChildrenHTML() {
  var children = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  return children.map(function (child) {
    return child.text === undefined ? createElementHTML(child) : Object(external_this_wp_escapeHtml_["escapeEditableHTML"])(child.text);
  }).join('');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/toggle-format.js
/**
 * Internal dependencies
 */



/**
 * Toggles a format object to a Rich Text value at the current selection.
 *
 * @param {Object} value  Value to modify.
 * @param {Object} format Format to apply or remove.
 *
 * @return {Object} A new value with the format applied or removed.
 */

function toggleFormat(value, format) {
  if (getActiveFormat(value, format.type)) {
    return removeFormat(value, format.type);
  }

  return applyFormat(value, format);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/unregister-format-type.js
/**
 * WordPress dependencies
 */

/** @typedef {import('./register-format-type').WPFormat} WPFormat */

/**
 * Unregisters a format.
 *
 * @param {string} name Format name.
 *
 * @return {WPFormat|undefined} The previous format value, if it has been successfully
 *                              unregistered; otherwise `undefined`.
 */

function unregisterFormatType(name) {
  var oldFormat = Object(external_this_wp_data_["select"])('core/rich-text').getFormatType(name);

  if (!oldFormat) {
    window.console.error("Format ".concat(name, " is not registered."));
    return;
  }

  Object(external_this_wp_data_["dispatch"])('core/rich-text').removeFormatTypes(name);
  return oldFormat;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/can-indent-list-items.js
/**
 * Internal dependencies
 */

/**
 * Checks if the selected list item can be indented.
 *
 * @param {Object} value Value to check.
 *
 * @return {boolean} Whether or not the selected list item can be indented.
 */

function canIndentListItems(value) {
  var lineIndex = getLineIndex(value); // There is only one line, so the line cannot be indented.

  if (lineIndex === undefined) {
    return false;
  }

  var replacements = value.replacements;
  var previousLineIndex = getLineIndex(value, lineIndex);
  var formatsAtLineIndex = replacements[lineIndex] || [];
  var formatsAtPreviousLineIndex = replacements[previousLineIndex] || []; // If the indentation of the current line is greater than previous line,
  // then the line cannot be furter indented.

  return formatsAtLineIndex.length <= formatsAtPreviousLineIndex.length;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/can-outdent-list-items.js
/**
 * Internal dependencies
 */

/**
 * Checks if the selected list item can be outdented.
 *
 * @param {Object} value Value to check.
 *
 * @return {boolean} Whether or not the selected list item can be outdented.
 */

function canOutdentListItems(value) {
  var replacements = value.replacements,
      start = value.start;
  var startingLineIndex = getLineIndex(value, start);
  return replacements[startingLineIndex] !== undefined;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/indent-list-items.js


function indent_list_items_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function indent_list_items_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { indent_list_items_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { indent_list_items_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Internal dependencies
 */



/**
 * Gets the line index of the first previous list item with higher indentation.
 *
 * @param {Object} value      Value to search.
 * @param {number} lineIndex  Line index of the list item to compare with.
 *
 * @return {boolean} The line index.
 */

function getTargetLevelLineIndex(_ref, lineIndex) {
  var text = _ref.text,
      replacements = _ref.replacements;
  var startFormats = replacements[lineIndex] || [];
  var index = lineIndex;

  while (index-- >= 0) {
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    }

    var formatsAtIndex = replacements[index] || []; // Return the first line index that is one level higher. If the level is
    // lower or equal, there is no result.

    if (formatsAtIndex.length === startFormats.length + 1) {
      return index;
    } else if (formatsAtIndex.length <= startFormats.length) {
      return;
    }
  }
}
/**
 * Indents any selected list items if possible.
 *
 * @param {Object} value      Value to change.
 * @param {Object} rootFormat Root format.
 *
 * @return {Object} The changed value.
 */


function indentListItems(value, rootFormat) {
  if (!canIndentListItems(value)) {
    return value;
  }

  var lineIndex = getLineIndex(value);
  var previousLineIndex = getLineIndex(value, lineIndex);
  var text = value.text,
      replacements = value.replacements,
      end = value.end;
  var newFormats = replacements.slice();
  var targetLevelLineIndex = getTargetLevelLineIndex(value, lineIndex);

  for (var index = lineIndex; index < end; index++) {
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    } // Get the previous list, and if there's a child list, take over the
    // formats. If not, duplicate the last level and create a new level.


    if (targetLevelLineIndex) {
      var targetFormats = replacements[targetLevelLineIndex] || [];
      newFormats[index] = targetFormats.concat((newFormats[index] || []).slice(targetFormats.length - 1));
    } else {
      var _targetFormats = replacements[previousLineIndex] || [];

      var lastformat = _targetFormats[_targetFormats.length - 1] || rootFormat;
      newFormats[index] = _targetFormats.concat([lastformat], (newFormats[index] || []).slice(_targetFormats.length));
    }
  }

  return indent_list_items_objectSpread({}, value, {
    replacements: newFormats
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-parent-line-index.js
/**
 * Internal dependencies
 */

/**
 * Gets the index of the first parent list. To get the parent list formats, we
 * go through every list item until we find one with exactly one format type
 * less.
 *
 * @param {Object} value     Value to search.
 * @param {number} lineIndex Line index of a child list item.
 *
 * @return {Array} The parent list line index.
 */

function getParentLineIndex(_ref, lineIndex) {
  var text = _ref.text,
      replacements = _ref.replacements;
  var startFormats = replacements[lineIndex] || [];
  var index = lineIndex;

  while (index-- >= 0) {
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    }

    var formatsAtIndex = replacements[index] || [];

    if (formatsAtIndex.length === startFormats.length - 1) {
      return index;
    }
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-last-child-index.js
/**
 * Internal dependencies
 */

/**
 * Gets the line index of the last child in the list.
 *
 * @param {Object} value     Value to search.
 * @param {number} lineIndex Line index of a list item in the list.
 *
 * @return {Array} The index of the last child.
 */

function getLastChildIndex(_ref, lineIndex) {
  var text = _ref.text,
      replacements = _ref.replacements;
  var lineFormats = replacements[lineIndex] || []; // Use the given line index in case there are no next children.

  var childIndex = lineIndex; // `lineIndex` could be `undefined` if it's the first line.

  for (var index = lineIndex || 0; index < text.length; index++) {
    // We're only interested in line indices.
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    }

    var formatsAtIndex = replacements[index] || []; // If the amout of formats is equal or more, store it, then return the
    // last one if the amount of formats is less.

    if (formatsAtIndex.length >= lineFormats.length) {
      childIndex = index;
    } else {
      return childIndex;
    }
  } // If the end of the text is reached, return the last child index.


  return childIndex;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/outdent-list-items.js


function outdent_list_items_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function outdent_list_items_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { outdent_list_items_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { outdent_list_items_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Internal dependencies
 */





/**
 * Outdents any selected list items if possible.
 *
 * @param {Object} value Value to change.
 *
 * @return {Object} The changed value.
 */

function outdentListItems(value) {
  if (!canOutdentListItems(value)) {
    return value;
  }

  var text = value.text,
      replacements = value.replacements,
      start = value.start,
      end = value.end;
  var startingLineIndex = getLineIndex(value, start);
  var newFormats = replacements.slice(0);
  var parentFormats = replacements[getParentLineIndex(value, startingLineIndex)] || [];
  var endingLineIndex = getLineIndex(value, end);
  var lastChildIndex = getLastChildIndex(value, endingLineIndex); // Outdent all list items from the starting line index until the last child
  // index of the ending list. All children of the ending list need to be
  // outdented, otherwise they'll be orphaned.

  for (var index = startingLineIndex; index <= lastChildIndex; index++) {
    // Skip indices that are not line separators.
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    } // In the case of level 0, the formats at the index are undefined.


    var currentFormats = newFormats[index] || []; // Omit the indentation level where the selection starts.

    newFormats[index] = parentFormats.concat(currentFormats.slice(parentFormats.length + 1));

    if (newFormats[index].length === 0) {
      delete newFormats[index];
    }
  }

  return outdent_list_items_objectSpread({}, value, {
    replacements: newFormats
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/change-list-type.js


function change_list_type_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function change_list_type_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { change_list_type_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { change_list_type_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Internal dependencies
 */



/**
 * Changes the list type of the selected indented list, if any. Looks at the
 * currently selected list item and takes the parent list, then changes the list
 * type of this list. When multiple lines are selected, the parent lists are
 * takes and changed.
 *
 * @param {Object} value     Value to change.
 * @param {Object} newFormat The new list format object. Choose between
 *                           `{ type: 'ol' }` and `{ type: 'ul' }`.
 *
 * @return {Object} The changed value.
 */

function changeListType(value, newFormat) {
  var text = value.text,
      replacements = value.replacements,
      start = value.start,
      end = value.end;
  var startingLineIndex = getLineIndex(value, start);
  var startLineFormats = replacements[startingLineIndex] || [];
  var endLineFormats = replacements[getLineIndex(value, end)] || [];
  var startIndex = getParentLineIndex(value, startingLineIndex);
  var newReplacements = replacements.slice();
  var startCount = startLineFormats.length - 1;
  var endCount = endLineFormats.length - 1;
  var changed;

  for (var index = startIndex + 1 || 0; index < text.length; index++) {
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    }

    if ((newReplacements[index] || []).length <= startCount) {
      break;
    }

    if (!newReplacements[index]) {
      continue;
    }

    changed = true;
    newReplacements[index] = newReplacements[index].map(function (format, i) {
      return i < startCount || i > endCount ? format : newFormat;
    });
  }

  if (!changed) {
    return value;
  }

  return change_list_type_objectSpread({}, value, {
    replacements: newReplacements
  });
}

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(8);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__(14);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js
var objectWithoutProperties = __webpack_require__(15);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(11);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__(21);

// EXTERNAL MODULE: external {"this":["wp","deprecated"]}
var external_this_wp_deprecated_ = __webpack_require__(37);
var external_this_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_deprecated_);

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/format-edit.js


/**
 * Internal dependencies
 */


/**
 * Set of all interactive content tags.
 *
 * @see https://html.spec.whatwg.org/multipage/dom.html#interactive-content
 */

var interactiveContentTags = new Set(['a', 'audio', 'button', 'details', 'embed', 'iframe', 'input', 'label', 'select', 'textarea', 'video']);
function FormatEdit(_ref) {
  var formatTypes = _ref.formatTypes,
      onChange = _ref.onChange,
      onFocus = _ref.onFocus,
      value = _ref.value,
      allowedFormats = _ref.allowedFormats,
      withoutInteractiveFormatting = _ref.withoutInteractiveFormatting;
  return formatTypes.map(function (_ref2) {
    var name = _ref2.name,
        Edit = _ref2.edit,
        tagName = _ref2.tagName;

    if (!Edit) {
      return null;
    }

    if (allowedFormats && allowedFormats.indexOf(name) === -1) {
      return null;
    }

    if (withoutInteractiveFormatting && interactiveContentTags.has(tagName)) {
      return null;
    }

    var activeFormat = getActiveFormat(value, name);
    var isActive = activeFormat !== undefined;
    var activeObject = getActiveObject(value);
    var isObjectActive = activeObject !== undefined && activeObject.type === name;
    return Object(external_this_wp_element_["createElement"])(Edit, {
      key: name,
      isActive: isActive,
      activeAttributes: isActive ? activeFormat.attributes || {} : {},
      isObjectActive: isObjectActive,
      activeObjectAttributes: isObjectActive ? activeObject.attributes || {} : {},
      value: value,
      onChange: onChange,
      onFocus: onFocus
    });
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/update-formats.js
/**
 * Internal dependencies
 */

/**
 * Efficiently updates all the formats from `start` (including) until `end`
 * (excluding) with the active formats. Mutates `value`.
 *
 * @param  {Object} $1         Named paramentes.
 * @param  {Object} $1.value   Value te update.
 * @param  {number} $1.start   Index to update from.
 * @param  {number} $1.end     Index to update until.
 * @param  {Array}  $1.formats Replacement formats.
 *
 * @return {Object} Mutated value.
 */

function updateFormats(_ref) {
  var value = _ref.value,
      start = _ref.start,
      end = _ref.end,
      formats = _ref.formats;
  var formatsBefore = value.formats[start - 1] || [];
  var formatsAfter = value.formats[end] || []; // First, fix the references. If any format right before or after are
  // equal, the replacement format should use the same reference.

  value.activeFormats = formats.map(function (format, index) {
    if (formatsBefore[index]) {
      if (isFormatEqual(format, formatsBefore[index])) {
        return formatsBefore[index];
      }
    } else if (formatsAfter[index]) {
      if (isFormatEqual(format, formatsAfter[index])) {
        return formatsAfter[index];
      }
    }

    return format;
  });

  while (--end >= start) {
    if (value.activeFormats.length > 0) {
      value.formats[end] = value.activeFormats;
    } else {
      delete value.formats[end];
    }
  }

  return value;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/with-format-types.js




function with_format_types_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function with_format_types_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { with_format_types_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { with_format_types_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function formatTypesSelector(select) {
  return select('core/rich-text').getFormatTypes();
}
/**
 * This higher-order component provides RichText with the `formatTypes` prop
 * and its derived props from experimental format type settings.
 *
 * @param {WPComponent} RichText The rich text component to add props for.
 *
 * @return {WPComponent} New enhanced component.
 */


function withFormatTypes(RichText) {
  return function WithFormatTypes(props) {
    var clientId = props.clientId,
        identifier = props.identifier;
    var formatTypes = Object(external_this_wp_data_["useSelect"])(formatTypesSelector, []);
    var selectProps = Object(external_this_wp_data_["useSelect"])(function (select) {
      return formatTypes.reduce(function (acc, settings) {
        if (!settings.__experimentalGetPropsForEditableTreePreparation) {
          return acc;
        }

        var selectPrefix = "format_prepare_props_(".concat(settings.name, ")_");
        return with_format_types_objectSpread({}, acc, {}, Object(external_this_lodash_["mapKeys"])(settings.__experimentalGetPropsForEditableTreePreparation(select, {
          richTextIdentifier: identifier,
          blockClientId: clientId
        }), function (value, key) {
          return selectPrefix + key;
        }));
      }, {});
    }, [formatTypes, clientId, identifier]);

    var dispatchProps = Object(external_this_wp_data_["__unstableUseDispatchWithMap"])(function (dispatch) {
      return formatTypes.reduce(function (acc, settings) {
        if (!settings.__experimentalGetPropsForEditableTreeChangeHandler) {
          return acc;
        }

        var dispatchPrefix = "format_on_change_props_(".concat(settings.name, ")_");
        return with_format_types_objectSpread({}, acc, {}, Object(external_this_lodash_["mapKeys"])(settings.__experimentalGetPropsForEditableTreeChangeHandler(dispatch, {
          richTextIdentifier: identifier,
          blockClientId: clientId
        }), function (value, key) {
          return dispatchPrefix + key;
        }));
      }, {});
    }, [formatTypes, clientId, identifier]);

    var newProps = Object(external_this_wp_element_["useMemo"])(function () {
      return formatTypes.reduce(function (acc, settings) {
        if (!settings.__experimentalCreatePrepareEditableTree) {
          return acc;
        }

        var args = {
          richTextIdentifier: identifier,
          blockClientId: clientId
        };

        var combined = with_format_types_objectSpread({}, selectProps, {}, dispatchProps);

        var name = settings.name;
        var selectPrefix = "format_prepare_props_(".concat(name, ")_");
        var dispatchPrefix = "format_on_change_props_(".concat(name, ")_");
        var propsByPrefix = Object.keys(combined).reduce(function (accumulator, key) {
          if (key.startsWith(selectPrefix)) {
            accumulator[key.slice(selectPrefix.length)] = combined[key];
          }

          if (key.startsWith(dispatchPrefix)) {
            accumulator[key.slice(dispatchPrefix.length)] = combined[key];
          }

          return accumulator;
        }, {});

        if (settings.__experimentalCreateOnChangeEditableValue) {
          var _objectSpread2;

          return with_format_types_objectSpread({}, acc, (_objectSpread2 = {}, Object(defineProperty["a" /* default */])(_objectSpread2, "format_value_functions_(".concat(name, ")"), settings.__experimentalCreatePrepareEditableTree(propsByPrefix, args)), Object(defineProperty["a" /* default */])(_objectSpread2, "format_on_change_functions_(".concat(name, ")"), settings.__experimentalCreateOnChangeEditableValue(propsByPrefix, args)), _objectSpread2));
        }

        return with_format_types_objectSpread({}, acc, Object(defineProperty["a" /* default */])({}, "format_prepare_functions_(".concat(name, ")"), settings.__experimentalCreatePrepareEditableTree(propsByPrefix, args)));
      }, {});
    }, [formatTypes, clientId, identifier, selectProps, dispatchProps]);
    return Object(external_this_wp_element_["createElement"])(RichText, Object(esm_extends["a" /* default */])({}, props, selectProps, newProps, {
      formatTypes: formatTypes
    }));
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-boundary-style.js
/**
 * WordPress dependencies
 */

/**
 * Calculates and renders the format boundary style when the active formats
 * change.
 */

function useBoundaryStyle(_ref) {
  var activeFormats = _ref.activeFormats,
      ref = _ref.ref;
  Object(external_this_wp_element_["useEffect"])(function () {
    // There's no need to recalculate the boundary styles if no formats are
    // active, because no boundary styles will be visible.
    if (!activeFormats || !activeFormats.length) {
      return;
    }

    var boundarySelector = '*[data-rich-text-format-boundary]';
    var element = ref.current.querySelector(boundarySelector);

    if (!element) {
      return;
    }

    var ownerDocument = element.ownerDocument;
    var defaultView = ownerDocument.defaultView;
    var computedStyle = defaultView.getComputedStyle(element);
    var newColor = computedStyle.color.replace(')', ', 0.2)').replace('rgb', 'rgba');
    var selector = ".rich-text:focus ".concat(boundarySelector);
    var rule = "background-color: ".concat(newColor);
    var style = "".concat(selector, " {").concat(rule, "}");
    var globalStyleId = 'rich-text-boundary-style';
    var globalStyle = ownerDocument.getElementById(globalStyleId);

    if (!globalStyle) {
      globalStyle = ownerDocument.createElement('style');
      globalStyle.id = globalStyleId;
      ownerDocument.head.appendChild(globalStyle);
    }

    if (globalStyle.innerHTML !== style) {
      globalStyle.innerHTML = style;
    }
  }, [activeFormats]);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-inline-warning.js
/**
 * WordPress dependencies
 */

function useInlineWarning(_ref) {
  var ref = _ref.ref;
  Object(external_this_wp_element_["useEffect"])(function () {
    if (false) { var computedStyle, defaultView, target; }
  }, []);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/index.js






function component_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function component_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { component_ownKeys(Object(source), true).forEach(function (key) { Object(defineProperty["a" /* default */])(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { component_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


















/** @typedef {import('@wordpress/element').WPSyntheticEvent} WPSyntheticEvent */

/**
 * All inserting input types that would insert HTML into the DOM.
 *
 * @see https://www.w3.org/TR/input-events-2/#interface-InputEvent-Attributes
 *
 * @type {Set}
 */

var INSERTION_INPUT_TYPES_TO_IGNORE = new Set(['insertParagraph', 'insertOrderedList', 'insertUnorderedList', 'insertHorizontalRule', 'insertLink']);
/**
 * In HTML, leading and trailing spaces are not visible, and multiple spaces
 * elsewhere are visually reduced to one space. This rule prevents spaces from
 * collapsing so all space is visible in the editor and can be removed. It also
 * prevents some browsers from inserting non-breaking spaces at the end of a
 * line to prevent the space from visually disappearing. Sometimes these non
 * breaking spaces can linger in the editor causing unwanted non breaking spaces
 * in between words. If also prevent Firefox from inserting a trailing `br` node
 * to visualise any trailing space, causing the element to be saved.
 *
 * > Authors are encouraged to set the 'white-space' property on editing hosts
 * > and on markup that was originally created through these editing mechanisms
 * > to the value 'pre-wrap'. Default HTML whitespace handling is not well
 * > suited to WYSIWYG editing, and line wrapping will not work correctly in
 * > some corner cases if 'white-space' is left at its default value.
 *
 * https://html.spec.whatwg.org/multipage/interaction.html#best-practices-for-in-page-editors
 *
 * @type {string}
 */

var whiteSpace = 'pre-wrap';
/**
 * Default style object for the editable element.
 *
 * @type {Object<string,string>}
 */

var defaultStyle = {
  whiteSpace: whiteSpace
};
var EMPTY_ACTIVE_FORMATS = [];

function createPrepareEditableTree(props, prefix) {
  var fns = Object.keys(props).reduce(function (accumulator, key) {
    if (key.startsWith(prefix)) {
      accumulator.push(props[key]);
    }

    return accumulator;
  }, []);
  return function (value) {
    return fns.reduce(function (accumulator, fn) {
      return fn(accumulator, value.text);
    }, value.formats);
  };
}
/**
 * If the selection is set on the placeholder element, collapse the selection to
 * the start (before the placeholder).
 *
 * @param {Window} defaultView
 */


function fixPlaceholderSelection(defaultView) {
  var selection = defaultView.getSelection();
  var anchorNode = selection.anchorNode,
      anchorOffset = selection.anchorOffset;

  if (anchorNode.nodeType !== anchorNode.ELEMENT_NODE) {
    return;
  }

  var targetNode = anchorNode.childNodes[anchorOffset];

  if (!targetNode || targetNode.nodeType !== targetNode.ELEMENT_NODE || !targetNode.getAttribute('data-rich-text-placeholder')) {
    return;
  }

  selection.collapseToStart();
}

function component_RichText(_ref) {
  var _ref$tagName = _ref.tagName,
      TagName = _ref$tagName === void 0 ? 'div' : _ref$tagName,
      _ref$value = _ref.value,
      value = _ref$value === void 0 ? '' : _ref$value,
      selectionStart = _ref.selectionStart,
      selectionEnd = _ref.selectionEnd,
      children = _ref.children,
      allowedFormats = _ref.allowedFormats,
      withoutInteractiveFormatting = _ref.withoutInteractiveFormatting,
      formatTypes = _ref.formatTypes,
      style = _ref.style,
      className = _ref.className,
      placeholder = _ref.placeholder,
      disabled = _ref.disabled,
      preserveWhiteSpace = _ref.preserveWhiteSpace,
      onPaste = _ref.onPaste,
      _ref$format = _ref.format,
      format = _ref$format === void 0 ? 'string' : _ref$format,
      onDelete = _ref.onDelete,
      onEnter = _ref.onEnter,
      onSelectionChange = _ref.onSelectionChange,
      onChange = _ref.onChange,
      onFocus = _ref.unstableOnFocus,
      setFocusedElement = _ref.setFocusedElement,
      instanceId = _ref.instanceId,
      multilineTag = _ref.__unstableMultilineTag,
      multilineRootTag = _ref.__unstableMultilineRootTag,
      disableFormats = _ref.__unstableDisableFormats,
      didAutomaticChange = _ref.__unstableDidAutomaticChange,
      inputRule = _ref.__unstableInputRule,
      markAutomaticChange = _ref.__unstableMarkAutomaticChange,
      allowPrefixTransformations = _ref.__unstableAllowPrefixTransformations,
      undo = _ref.__unstableUndo,
      isCaretWithinFormattedText = _ref.__unstableIsCaretWithinFormattedText,
      onEnterFormattedText = _ref.__unstableOnEnterFormattedText,
      onExitFormattedText = _ref.__unstableOnExitFormattedText,
      onCreateUndoLevel = _ref.__unstableOnCreateUndoLevel,
      isSelected = _ref.__unstableIsSelected,
      ref = _ref.forwardedRef,
      remainingProps = Object(objectWithoutProperties["a" /* default */])(_ref, ["tagName", "value", "selectionStart", "selectionEnd", "children", "allowedFormats", "withoutInteractiveFormatting", "formatTypes", "style", "className", "placeholder", "disabled", "preserveWhiteSpace", "onPaste", "format", "onDelete", "onEnter", "onSelectionChange", "onChange", "unstableOnFocus", "setFocusedElement", "instanceId", "__unstableMultilineTag", "__unstableMultilineRootTag", "__unstableDisableFormats", "__unstableDidAutomaticChange", "__unstableInputRule", "__unstableMarkAutomaticChange", "__unstableAllowPrefixTransformations", "__unstableUndo", "__unstableIsCaretWithinFormattedText", "__unstableOnEnterFormattedText", "__unstableOnExitFormattedText", "__unstableOnCreateUndoLevel", "__unstableIsSelected", "forwardedRef"]);

  var _useState = Object(external_this_wp_element_["useState"])(),
      _useState2 = Object(slicedToArray["a" /* default */])(_useState, 2),
      _useState2$ = _useState2[0],
      activeFormats = _useState2$ === void 0 ? [] : _useState2$,
      setActiveFormats = _useState2[1]; // For backward compatibility, fall back to tagName if it's a string.
  // tagName can now be a component for light blocks.


  if (!multilineRootTag && typeof TagName === 'string') {
    multilineRootTag = TagName;
  }

  function getDoc() {
    return ref.current.ownerDocument;
  }

  function getWin() {
    return getDoc().defaultView;
  }
  /**
   * Converts the outside data structure to our internal representation.
   *
   * @param {*} string The outside value, data type depends on props.
   *
   * @return {Object} An internal rich-text value.
   */


  function formatToValue(string) {
    if (disableFormats) {
      return {
        text: string,
        formats: Array(string.length),
        replacements: Array(string.length)
      };
    }

    if (format !== 'string') {
      return string;
    }

    var prepare = createPrepareEditableTree(remainingProps, 'format_value_functions');
    var result = create({
      html: string,
      multilineTag: multilineTag,
      multilineWrapperTags: multilineTag === 'li' ? ['ul', 'ol'] : undefined,
      preserveWhiteSpace: preserveWhiteSpace
    });
    result.formats = prepare(result);
    return result;
  }
  /**
   * Removes editor only formats from the value.
   *
   * Editor only formats are applied using `prepareEditableTree`, so we need to
   * remove them before converting the internal state
   *
   * @param {Object} val The internal rich-text value.
   *
   * @return {Object} A new rich-text value.
   */


  function removeEditorOnlyFormats(val) {
    formatTypes.forEach(function (formatType) {
      // Remove formats created by prepareEditableTree, because they are editor only.
      if (formatType.__experimentalCreatePrepareEditableTree) {
        val = removeFormat(val, formatType.name, 0, val.text.length);
      }
    });
    return val;
  }
  /**
   * Converts the internal value to the external data format.
   *
   * @param {Object} val The internal rich-text value.
   *
   * @return {*} The external data format, data type depends on props.
   */


  function valueToFormat(val) {
    if (disableFormats) {
      return val.text;
    }

    val = removeEditorOnlyFormats(val);

    if (format !== 'string') {
      return;
    }

    return toHTMLString({
      value: val,
      multilineTag: multilineTag,
      preserveWhiteSpace: preserveWhiteSpace
    });
  } // Internal values are updated synchronously, unlike props and state.


  var _value = Object(external_this_wp_element_["useRef"])(value);

  var record = Object(external_this_wp_element_["useRef"])(Object(external_this_wp_element_["useMemo"])(function () {
    var initialRecord = formatToValue(value);
    initialRecord.start = selectionStart;
    initialRecord.end = selectionEnd;
    return initialRecord;
  }, []));

  function createRecord() {
    var selection = getWin().getSelection();
    var range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
    return create({
      element: ref.current,
      range: range,
      multilineTag: multilineTag,
      multilineWrapperTags: multilineTag === 'li' ? ['ul', 'ol'] : undefined,
      __unstableIsEditableTree: true,
      preserveWhiteSpace: preserveWhiteSpace
    });
  }

  function applyRecord(newRecord) {
    var _ref2 = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {},
        domOnly = _ref2.domOnly;

    apply({
      value: newRecord,
      current: ref.current,
      multilineTag: multilineTag,
      multilineWrapperTags: multilineTag === 'li' ? ['ul', 'ol'] : undefined,
      prepareEditableTree: createPrepareEditableTree(remainingProps, 'format_prepare_functions'),
      __unstableDomOnly: domOnly,
      placeholder: placeholder
    });
  }
  /**
   * Handles a paste event.
   *
   * Saves the pasted data as plain text in `pastedPlainText`.
   *
   * @param {ClipboardEvent} event The paste event.
   */


  function handlePaste(event) {
    if (!isSelected) {
      event.preventDefault();
      return;
    }

    var clipboardData = event.clipboardData;
    var items = clipboardData.items,
        files = clipboardData.files; // In Edge these properties can be null instead of undefined, so a more
    // rigorous test is required over using default values.

    items = Object(external_this_lodash_["isNil"])(items) ? [] : items;
    files = Object(external_this_lodash_["isNil"])(files) ? [] : files;
    var plainText = '';
    var html = ''; // IE11 only supports `Text` as an argument for `getData` and will
    // otherwise throw an invalid argument error, so we try the standard
    // arguments first, then fallback to `Text` if they fail.

    try {
      plainText = clipboardData.getData('text/plain');
      html = clipboardData.getData('text/html');
    } catch (error1) {
      try {
        html = clipboardData.getData('Text');
      } catch (error2) {
        // Some browsers like UC Browser paste plain text by default and
        // don't support clipboardData at all, so allow default
        // behaviour.
        return;
      }
    }

    event.preventDefault(); // Allows us to ask for this information when we get a report.

    window.console.log('Received HTML:\n\n', html);
    window.console.log('Received plain text:\n\n', plainText);

    if (disableFormats) {
      handleChange(insert(record.current, plainText));
      return;
    }

    var transformed = formatTypes.reduce(function (accumlator, _ref3) {
      var __unstablePasteRule = _ref3.__unstablePasteRule;

      // Only allow one transform.
      if (__unstablePasteRule && accumlator === record.current) {
        accumlator = __unstablePasteRule(record.current, {
          html: html,
          plainText: plainText
        });
      }

      return accumlator;
    }, record.current);

    if (transformed !== record.current) {
      handleChange(transformed);
      return;
    }

    if (onPaste) {
      files = Array.from(files);
      Array.from(items).forEach(function (item) {
        if (!item.getAsFile) {
          return;
        }

        var file = item.getAsFile();

        if (!file) {
          return;
        }

        var name = file.name,
            type = file.type,
            size = file.size;

        if (!Object(external_this_lodash_["find"])(files, {
          name: name,
          type: type,
          size: size
        })) {
          files.push(file);
        }
      });
      onPaste({
        value: removeEditorOnlyFormats(record.current),
        onChange: handleChange,
        html: html,
        plainText: plainText,
        files: files,
        activeFormats: activeFormats
      });
    }
  }
  /**
   * Handles delete on keydown:
   * - outdent list items,
   * - delete content if everything is selected,
   * - trigger the onDelete prop when selection is uncollapsed and at an edge.
   *
   * @param {WPSyntheticEvent} event A synthetic keyboard event.
   */


  function handleDelete(event) {
    var keyCode = event.keyCode;

    if (keyCode !== external_this_wp_keycodes_["DELETE"] && keyCode !== external_this_wp_keycodes_["BACKSPACE"] && keyCode !== external_this_wp_keycodes_["ESCAPE"]) {
      return;
    }

    if (didAutomaticChange) {
      event.preventDefault();
      undo();
      return;
    }

    if (keyCode === external_this_wp_keycodes_["ESCAPE"]) {
      return;
    }

    var currentValue = createRecord();
    var start = currentValue.start,
        end = currentValue.end,
        text = currentValue.text;
    var isReverse = keyCode === external_this_wp_keycodes_["BACKSPACE"]; // Always handle full content deletion ourselves.

    if (start === 0 && end !== 0 && end === text.length) {
      handleChange(remove_remove(currentValue));
      event.preventDefault();
      return;
    }

    if (multilineTag) {
      var newValue; // Check to see if we should remove the first item if empty.

      if (isReverse && currentValue.start === 0 && currentValue.end === 0 && isEmptyLine(currentValue)) {
        newValue = removeLineSeparator(currentValue, !isReverse);
      } else {
        newValue = removeLineSeparator(currentValue, isReverse);
      }

      if (newValue) {
        handleChange(newValue);
        event.preventDefault();
        return;
      }
    } // Only process delete if the key press occurs at an uncollapsed edge.


    if (!onDelete || !isCollapsed(currentValue) || activeFormats.length || isReverse && start !== 0 || !isReverse && end !== text.length) {
      return;
    }

    onDelete({
      isReverse: isReverse,
      value: currentValue
    });
    event.preventDefault();
  }
  /**
   * Triggers the `onEnter` prop on keydown.
   *
   * @param {WPSyntheticEvent} event A synthetic keyboard event.
   */


  function handleEnter(event) {
    if (event.keyCode !== external_this_wp_keycodes_["ENTER"]) {
      return;
    }

    event.preventDefault();

    if (!onEnter) {
      return;
    }

    onEnter({
      value: removeEditorOnlyFormats(createRecord()),
      onChange: handleChange,
      shiftKey: event.shiftKey
    });
  }
  /**
   * Indents list items on space keydown.
   *
   * @param {WPSyntheticEvent} event A synthetic keyboard event.
   */


  function handleSpace(event) {
    var keyCode = event.keyCode,
        shiftKey = event.shiftKey,
        altKey = event.altKey,
        metaKey = event.metaKey,
        ctrlKey = event.ctrlKey;

    if ( // Only override when no modifiers are pressed.
    shiftKey || altKey || metaKey || ctrlKey || keyCode !== external_this_wp_keycodes_["SPACE"] || multilineTag !== 'li') {
      return;
    }

    var currentValue = createRecord();

    if (!isCollapsed(currentValue)) {
      return;
    }

    var text = currentValue.text,
        start = currentValue.start;
    var characterBefore = text[start - 1]; // The caret must be at the start of a line.

    if (characterBefore && characterBefore !== LINE_SEPARATOR) {
      return;
    }

    handleChange(indentListItems(currentValue, {
      type: multilineRootTag
    }));
    event.preventDefault();
  }
  /**
   * Handles horizontal keyboard navigation when no modifiers are pressed. The
   * navigation is handled separately to move correctly around format
   * boundaries.
   *
   * @param {WPSyntheticEvent} event A synthetic keyboard event.
   */


  function handleHorizontalNavigation(event) {
    var keyCode = event.keyCode,
        shiftKey = event.shiftKey,
        altKey = event.altKey,
        metaKey = event.metaKey,
        ctrlKey = event.ctrlKey;

    if ( // Only override left and right keys without modifiers pressed.
    shiftKey || altKey || metaKey || ctrlKey || keyCode !== external_this_wp_keycodes_["LEFT"] && keyCode !== external_this_wp_keycodes_["RIGHT"]) {
      return;
    }

    var _record$current = record.current,
        text = _record$current.text,
        formats = _record$current.formats,
        start = _record$current.start,
        end = _record$current.end,
        _record$current$activ = _record$current.activeFormats,
        currentActiveFormats = _record$current$activ === void 0 ? [] : _record$current$activ;
    var collapsed = isCollapsed(record.current); // To do: ideally, we should look at visual position instead.

    var _getWin$getComputedSt = getWin().getComputedStyle(ref.current),
        direction = _getWin$getComputedSt.direction;

    var reverseKey = direction === 'rtl' ? external_this_wp_keycodes_["RIGHT"] : external_this_wp_keycodes_["LEFT"];
    var isReverse = event.keyCode === reverseKey; // If the selection is collapsed and at the very start, do nothing if
    // navigating backward.
    // If the selection is collapsed and at the very end, do nothing if
    // navigating forward.

    if (collapsed && currentActiveFormats.length === 0) {
      if (start === 0 && isReverse) {
        return;
      }

      if (end === text.length && !isReverse) {
        return;
      }
    } // If the selection is not collapsed, let the browser handle collapsing
    // the selection for now. Later we could expand this logic to set
    // boundary positions if needed.


    if (!collapsed) {
      return;
    } // In all other cases, prevent default behaviour.


    event.preventDefault();
    var formatsBefore = formats[start - 1] || EMPTY_ACTIVE_FORMATS;
    var formatsAfter = formats[start] || EMPTY_ACTIVE_FORMATS;
    var newActiveFormatsLength = currentActiveFormats.length;
    var source = formatsAfter;

    if (formatsBefore.length > formatsAfter.length) {
      source = formatsBefore;
    } // If the amount of formats before the caret and after the caret is
    // different, the caret is at a format boundary.


    if (formatsBefore.length < formatsAfter.length) {
      if (!isReverse && currentActiveFormats.length < formatsAfter.length) {
        newActiveFormatsLength++;
      }

      if (isReverse && currentActiveFormats.length > formatsBefore.length) {
        newActiveFormatsLength--;
      }
    } else if (formatsBefore.length > formatsAfter.length) {
      if (!isReverse && currentActiveFormats.length > formatsAfter.length) {
        newActiveFormatsLength--;
      }

      if (isReverse && currentActiveFormats.length < formatsBefore.length) {
        newActiveFormatsLength++;
      }
    }

    if (newActiveFormatsLength !== currentActiveFormats.length) {
      var _newActiveFormats = source.slice(0, newActiveFormatsLength);

      var _newValue = component_objectSpread({}, record.current, {
        activeFormats: _newActiveFormats
      });

      record.current = _newValue;
      applyRecord(_newValue);
      setActiveFormats(_newActiveFormats);
      return;
    }

    var newPos = start + (isReverse ? -1 : 1);
    var newActiveFormats = isReverse ? formatsBefore : formatsAfter;

    var newValue = component_objectSpread({}, record.current, {
      start: newPos,
      end: newPos,
      activeFormats: newActiveFormats
    });

    record.current = newValue;
    applyRecord(newValue);
    onSelectionChange(newPos, newPos);
    setActiveFormats(newActiveFormats);
  }

  function handleKeyDown(event) {
    if (event.defaultPrevented) {
      return;
    }

    handleDelete(event);
    handleEnter(event);
    handleSpace(event);
    handleHorizontalNavigation(event);
  }

  var lastHistoryValue = Object(external_this_wp_element_["useRef"])(value);

  function createUndoLevel() {
    // If the content is the same, no level needs to be created.
    if (lastHistoryValue.current === _value.current) {
      return;
    }

    onCreateUndoLevel();
    lastHistoryValue.current = _value.current;
  }

  var isComposing = Object(external_this_wp_element_["useRef"])(false);
  var timeout = Object(external_this_wp_element_["useRef"])();
  /**
   * Handle input on the next selection change event.
   *
   * @param {WPSyntheticEvent} event Synthetic input event.
   */

  function handleInput(event) {
    // Do not trigger a change if characters are being composed. Browsers
    // will usually emit a final `input` event when the characters are
    // composed.
    // As of December 2019, Safari doesn't support nativeEvent.isComposing.
    if (isComposing.current) {
      return;
    }

    var inputType;

    if (event) {
      inputType = event.inputType;
    }

    if (!inputType && event && event.nativeEvent) {
      inputType = event.nativeEvent.inputType;
    } // The browser formatted something or tried to insert HTML.
    // Overwrite it. It will be handled later by the format library if
    // needed.


    if (inputType && (inputType.indexOf('format') === 0 || INSERTION_INPUT_TYPES_TO_IGNORE.has(inputType))) {
      applyRecord(record.current);
      return;
    }

    var currentValue = createRecord();
    var _record$current2 = record.current,
        start = _record$current2.start,
        _record$current2$acti = _record$current2.activeFormats,
        oldActiveFormats = _record$current2$acti === void 0 ? [] : _record$current2$acti; // Update the formats between the last and new caret position.

    var change = updateFormats({
      value: currentValue,
      start: start,
      end: currentValue.start,
      formats: oldActiveFormats
    });
    handleChange(change, {
      withoutHistory: true
    }); // Create an undo level when input stops for over a second.

    getWin().clearTimeout(timeout.current);
    timeout.current = getWin().setTimeout(createUndoLevel, 1000); // Only run input rules when inserting text.

    if (inputType !== 'insertText') {
      return;
    }

    if (allowPrefixTransformations && inputRule) {
      inputRule(change, valueToFormat);
    }

    var transformed = formatTypes.reduce(function (accumlator, _ref4) {
      var __unstableInputRule = _ref4.__unstableInputRule;

      if (__unstableInputRule) {
        accumlator = __unstableInputRule(accumlator);
      }

      return accumlator;
    }, change);

    if (transformed !== change) {
      createUndoLevel();
      handleChange(component_objectSpread({}, transformed, {
        activeFormats: oldActiveFormats
      }));
      markAutomaticChange();
    }
  }

  function handleCompositionStart() {
    isComposing.current = true; // Do not update the selection when characters are being composed as
    // this rerenders the component and might distroy internal browser
    // editing state.

    getDoc().removeEventListener('selectionchange', handleSelectionChange);
  }

  function handleCompositionEnd() {
    isComposing.current = false; // Ensure the value is up-to-date for browsers that don't emit a final
    // input event after composition.

    handleInput({
      inputType: 'insertText'
    }); // Tracking selection changes can be resumed.

    getDoc().addEventListener('selectionchange', handleSelectionChange);
  }

  var didMount = Object(external_this_wp_element_["useRef"])(false);
  /**
   * Syncs the selection to local state. A callback for the `selectionchange`
   * native events, `keyup`, `mouseup` and `touchend` synthetic events, and
   * animation frames after the `focus` event.
   *
   * @param {Event|WPSyntheticEvent|DOMHighResTimeStamp} event
   */

  function handleSelectionChange(event) {
    if (!ref.current) {
      return;
    }

    if (document.activeElement !== ref.current) {
      return;
    }

    if (event.type !== 'selectionchange' && !isSelected) {
      return;
    }

    if (disabled) {
      return;
    } // In case of a keyboard event, ignore selection changes during
    // composition.


    if (isComposing.current) {
      return;
    }

    var _createRecord = createRecord(),
        start = _createRecord.start,
        end = _createRecord.end,
        text = _createRecord.text;

    var oldRecord = record.current; // Fallback mechanism for IE11, which doesn't support the input event.
    // Any input results in a selection change.

    if (text !== oldRecord.text) {
      handleInput();
      return;
    }

    if (start === oldRecord.start && end === oldRecord.end) {
      // Sometimes the browser may set the selection on the placeholder
      // element, in which case the caret is not visible. We need to set
      // the caret before the placeholder if that's the case.
      if (oldRecord.text.length === 0 && start === 0) {
        fixPlaceholderSelection(getWin());
      }

      return;
    }

    var newValue = component_objectSpread({}, oldRecord, {
      start: start,
      end: end,
      // Allow `getActiveFormats` to get new `activeFormats`.
      activeFormats: undefined
    });

    var newActiveFormats = getActiveFormats(newValue, EMPTY_ACTIVE_FORMATS); // Update the value with the new active formats.

    newValue.activeFormats = newActiveFormats;

    if (!isCaretWithinFormattedText && newActiveFormats.length) {
      onEnterFormattedText();
    } else if (isCaretWithinFormattedText && !newActiveFormats.length) {
      onExitFormattedText();
    } // It is important that the internal value is updated first,
    // otherwise the value will be wrong on render!


    record.current = newValue;
    applyRecord(newValue, {
      domOnly: true
    });
    onSelectionChange(start, end);
    setActiveFormats(newActiveFormats);
  }
  /**
   * Sync the value to global state. The node tree and selection will also be
   * updated if differences are found.
   *
   * @param {Object}  newRecord         The record to sync and apply.
   * @param {Object}  $2                Named options.
   * @param {boolean} $2.withoutHistory If true, no undo level will be
   *                                    created.
   */


  function handleChange(newRecord) {
    var _ref5 = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {},
        withoutHistory = _ref5.withoutHistory;

    if (disableFormats) {
      newRecord.formats = Array(newRecord.text.length);
      newRecord.replacements = Array(newRecord.text.length);
    }

    applyRecord(newRecord);
    var start = newRecord.start,
        end = newRecord.end,
        _newRecord$activeForm = newRecord.activeFormats,
        newActiveFormats = _newRecord$activeForm === void 0 ? [] : _newRecord$activeForm;
    var changeHandlers = Object(external_this_lodash_["pickBy"])(remainingProps, function (v, key) {
      return key.startsWith('format_on_change_functions_');
    });
    Object.values(changeHandlers).forEach(function (changeHandler) {
      changeHandler(newRecord.formats, newRecord.text);
    });
    _value.current = valueToFormat(newRecord);
    record.current = newRecord; // Selection must be updated first, so it is recorded in history when
    // the content change happens.

    onSelectionChange(start, end);
    onChange(_value.current);
    setActiveFormats(newActiveFormats);

    if (!withoutHistory) {
      createUndoLevel();
    }
  }
  /**
   * Select object when they are clicked. The browser will not set any
   * selection when clicking e.g. an image.
   *
   * @param {WPSyntheticEvent} event Synthetic mousedown or touchstart event.
   */


  function handlePointerDown(event) {
    var target = event.target; // If the child element has no text content, it must be an object.

    if (target === ref.current || target.textContent) {
      return;
    }

    var parentNode = target.parentNode;
    var index = Array.from(parentNode.childNodes).indexOf(target);
    var range = getDoc().createRange();
    var selection = getWin().getSelection();
    range.setStart(target.parentNode, index);
    range.setEnd(target.parentNode, index + 1);
    selection.removeAllRanges();
    selection.addRange(range);
  }

  var rafId = Object(external_this_wp_element_["useRef"])();
  /**
   * Handles a focus event on the contenteditable field, calling the
   * `unstableOnFocus` prop callback if one is defined. The callback does not
   * receive any arguments.
   *
   * This is marked as a private API and the `unstableOnFocus` prop is not
   * documented, as the current requirements where it is used are subject to
   * future refactoring following `isSelected` handling.
   *
   * In contrast with `setFocusedElement`, this is only triggered in response
   * to focus within the contenteditable field, whereas `setFocusedElement`
   * is triggered on focus within any `RichText` descendent element.
   *
   * @see setFocusedElement
   *
   * @private
   */

  function handleFocus() {
    if (onFocus) {
      onFocus();
    }

    if (!isSelected) {
      // We know for certain that on focus, the old selection is invalid.
      // It will be recalculated on the next mouseup, keyup, or touchend
      // event.
      var index = undefined;
      record.current = component_objectSpread({}, record.current, {
        start: index,
        end: index,
        activeFormats: EMPTY_ACTIVE_FORMATS
      });
      onSelectionChange(index, index);
      setActiveFormats(EMPTY_ACTIVE_FORMATS);
    } else {
      onSelectionChange(record.current.start, record.current.end);
      setActiveFormats(getActiveFormats(component_objectSpread({}, record.current, {
        activeFormats: undefined
      }), EMPTY_ACTIVE_FORMATS));
    } // Update selection as soon as possible, which is at the next animation
    // frame. The event listener for selection changes may be added too late
    // at this point, but this focus event is still too early to calculate
    // the selection.


    rafId.current = getWin().requestAnimationFrame(handleSelectionChange);
    getDoc().addEventListener('selectionchange', handleSelectionChange);

    if (setFocusedElement) {
      external_this_wp_deprecated_default()('wp.blockEditor.RichText setFocusedElement prop', {
        alternative: 'selection state from the block editor store.'
      });
      setFocusedElement(instanceId);
    }
  }

  function handleBlur() {
    getDoc().removeEventListener('selectionchange', handleSelectionChange);
  }

  function applyFromProps() {
    _value.current = value;
    record.current = formatToValue(value);
    record.current.start = selectionStart;
    record.current.end = selectionEnd;
    applyRecord(record.current);
  }

  Object(external_this_wp_element_["useEffect"])(function () {
    if (didMount.current) {
      applyFromProps();
    }
  }, [TagName, placeholder]);
  Object(external_this_wp_element_["useEffect"])(function () {
    if (didMount.current && value !== _value.current) {
      applyFromProps();
    }
  }, [value]);
  Object(external_this_wp_element_["useEffect"])(function () {
    if (!didMount.current) {
      return;
    }

    if (isSelected && (selectionStart !== record.current.start || selectionEnd !== record.current.end)) {
      applyFromProps();
    } else {
      record.current = component_objectSpread({}, record.current, {
        start: selectionStart,
        end: selectionEnd
      });
    }
  }, [selectionStart, selectionEnd, isSelected]);
  var prefix = 'format_prepare_props_';

  var predicate = function predicate(v, key) {
    return key.startsWith(prefix);
  };

  var prepareProps = Object(external_this_lodash_["pickBy"])(remainingProps, predicate);
  Object(external_this_wp_element_["useEffect"])(function () {
    if (didMount.current) {
      applyFromProps();
    }
  }, Object.values(prepareProps));
  Object(external_this_wp_element_["useLayoutEffect"])(function () {
    applyRecord(record.current, {
      domOnly: true
    });
    didMount.current = true;
    return function () {
      getDoc().removeEventListener('selectionchange', handleSelectionChange);
      getWin().cancelAnimationFrame(rafId.current);
      getWin().clearTimeout(timeout.current);
    };
  }, []);

  function focus() {
    ref.current.focus();
    applyRecord(record.current);
  }

  var ariaProps = Object(external_this_lodash_["pickBy"])(remainingProps, function (v, key) {
    return Object(external_this_lodash_["startsWith"])(key, 'aria-');
  });

  var editableProps = component_objectSpread({
    // Overridable props.
    role: 'textbox',
    'aria-multiline': true,
    'aria-label': placeholder
  }, ariaProps, {
    ref: ref,
    style: style ? component_objectSpread({}, style, {
      whiteSpace: whiteSpace
    }) : defaultStyle,
    className: classnames_default()('rich-text', className),
    onPaste: handlePaste,
    onInput: handleInput,
    onCompositionStart: handleCompositionStart,
    onCompositionEnd: handleCompositionEnd,
    onKeyDown: handleKeyDown,
    onFocus: handleFocus,
    onBlur: handleBlur,
    onMouseDown: handlePointerDown,
    onTouchStart: handlePointerDown,
    // Selection updates must be done at these events as they
    // happen before the `selectionchange` event. In some cases,
    // the `selectionchange` event may not even fire, for
    // example when the window receives focus again on click.
    onKeyUp: handleSelectionChange,
    onMouseUp: handleSelectionChange,
    onTouchEnd: handleSelectionChange,
    // Do not set the attribute if disabled.
    contentEditable: disabled ? undefined : true,
    suppressContentEditableWarning: !disabled
  });

  useBoundaryStyle({
    ref: ref,
    activeFormats: activeFormats
  });
  useInlineWarning({
    ref: ref
  });
  return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, isSelected && Object(external_this_wp_element_["createElement"])(FormatEdit, {
    allowedFormats: allowedFormats,
    withoutInteractiveFormatting: withoutInteractiveFormatting,
    value: record.current,
    onChange: handleChange,
    onFocus: focus,
    formatTypes: formatTypes
  }), children && children({
    isSelected: isSelected,
    value: record.current,
    onChange: handleChange,
    onFocus: focus,
    editableProps: editableProps,
    editableTagName: TagName
  }), !children && Object(external_this_wp_element_["createElement"])(TagName, editableProps));
}

var RichTextWrapper = withFormatTypes(component_RichText);
/**
 * Renders a rich content input, providing users with the option to format the
 * content.
 */

/* harmony default export */ var component = (Object(external_this_wp_element_["forwardRef"])(function (props, ref) {
  return Object(external_this_wp_element_["createElement"])(RichTextWrapper, Object(esm_extends["a" /* default */])({}, props, {
    forwardedRef: ref
  }));
}));

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/index.js
/**
 * Internal dependencies
 */





































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

/***/ 8:
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

/***/ 89:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["escapeHtml"]; }());

/***/ })

/******/ });