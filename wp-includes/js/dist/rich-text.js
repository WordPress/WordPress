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
/******/ 	return __webpack_require__(__webpack_require__.s = "yyEc");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ "25BE":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

/***/ }),

/***/ "4eJC":
/***/ (function(module, exports, __webpack_require__) {

module.exports = function memize( fn, options ) {
	var size = 0,
		maxSize, head, tail;

	if ( options && options.maxSize ) {
		maxSize = options.maxSize;
	}

	function memoized( /* ...args */ ) {
		var node = head,
			len = arguments.length,
			args, i;

		searchCache: while ( node ) {
			// Perform a shallow equality test to confirm that whether the node
			// under test is a candidate for the arguments passed. Two arrays
			// are shallowly equal if their length matches and each entry is
			// strictly equal between the two sets. Avoid abstracting to a
			// function which could incur an arguments leaking deoptimization.

			// Check whether node arguments match arguments length
			if ( node.args.length !== arguments.length ) {
				node = node.next;
				continue;
			}

			// Check whether node arguments match arguments values
			for ( i = 0; i < len; i++ ) {
				if ( node.args[ i ] !== arguments[ i ] ) {
					node = node.next;
					continue searchCache;
				}
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if ( node !== head ) {
				// As tail, shift to previous. Must only shift if not also
				// head, since if both head and tail, there is no previous.
				if ( node === tail ) {
					tail = node.prev;
				}

				// Adjust siblings to point to each other. If node was tail,
				// this also handles new tail's empty `next` assignment.
				node.prev.next = node.next;
				if ( node.next ) {
					node.next.prev = node.prev;
				}

				node.next = head;
				node.prev = null;
				head.prev = node;
				head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		// Create a copy of arguments (avoid leaking deoptimization)
		args = new Array( len );
		for ( i = 0; i < len; i++ ) {
			args[ i ] = arguments[ i ];
		}

		node = {
			args: args,

			// Generate the result from original function
			val: fn.apply( null, args )
		};

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if ( head ) {
			head.prev = node;
			node.next = head;
		} else {
			// If no head, follows that there's no tail (at initial or reset)
			tail = node;
		}

		// Trim tail if we're reached max size and are pending cache insertion
		if ( size === maxSize ) {
			tail = tail.prev;
			tail.next = null;
		} else {
			size++;
		}

		head = node;

		return node.val;
	}

	memoized.clear = function() {
		head = null;
		tail = null;
		size = 0;
	};

	if ( false ) {}

	return memoized;
};


/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "K9lf":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ "KQm4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _toConsumableArray; });

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
var iterableToArray = __webpack_require__("25BE");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js



function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || Object(iterableToArray["a" /* default */])(arr) || _nonIterableSpread();
}

/***/ }),

/***/ "U8pU":
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

/***/ "Vx3V":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["escapeHtml"]; }());

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ "g56x":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["hooks"]; }());

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

/***/ "vpQ4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectSpread; });
/* harmony import */ var _defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("rePB");

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

/***/ "wx14":
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

/***/ "yyEc":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "applyFormat", function() { return /* reexport */ applyFormat; });
__webpack_require__.d(__webpack_exports__, "charAt", function() { return /* reexport */ charAt; });
__webpack_require__.d(__webpack_exports__, "concat", function() { return /* reexport */ concat; });
__webpack_require__.d(__webpack_exports__, "create", function() { return /* reexport */ create; });
__webpack_require__.d(__webpack_exports__, "getActiveFormat", function() { return /* reexport */ getActiveFormat; });
__webpack_require__.d(__webpack_exports__, "getSelectionEnd", function() { return /* reexport */ getSelectionEnd; });
__webpack_require__.d(__webpack_exports__, "getSelectionStart", function() { return /* reexport */ getSelectionStart; });
__webpack_require__.d(__webpack_exports__, "getTextContent", function() { return /* reexport */ getTextContent; });
__webpack_require__.d(__webpack_exports__, "isCollapsed", function() { return /* reexport */ isCollapsed; });
__webpack_require__.d(__webpack_exports__, "isEmpty", function() { return /* reexport */ isEmpty; });
__webpack_require__.d(__webpack_exports__, "isEmptyLine", function() { return /* reexport */ isEmptyLine; });
__webpack_require__.d(__webpack_exports__, "join", function() { return /* reexport */ join; });
__webpack_require__.d(__webpack_exports__, "registerFormatType", function() { return /* reexport */ registerFormatType; });
__webpack_require__.d(__webpack_exports__, "removeFormat", function() { return /* reexport */ removeFormat; });
__webpack_require__.d(__webpack_exports__, "remove", function() { return /* reexport */ remove_remove; });
__webpack_require__.d(__webpack_exports__, "replace", function() { return /* reexport */ replace; });
__webpack_require__.d(__webpack_exports__, "insert", function() { return /* reexport */ insert; });
__webpack_require__.d(__webpack_exports__, "insertLineSeparator", function() { return /* reexport */ insertLineSeparator; });
__webpack_require__.d(__webpack_exports__, "insertObject", function() { return /* reexport */ insertObject; });
__webpack_require__.d(__webpack_exports__, "slice", function() { return /* reexport */ slice; });
__webpack_require__.d(__webpack_exports__, "split", function() { return /* reexport */ split; });
__webpack_require__.d(__webpack_exports__, "apply", function() { return /* reexport */ apply; });
__webpack_require__.d(__webpack_exports__, "unstableToDom", function() { return /* reexport */ toDom; });
__webpack_require__.d(__webpack_exports__, "toHTMLString", function() { return /* reexport */ toHTMLString; });
__webpack_require__.d(__webpack_exports__, "toggleFormat", function() { return /* reexport */ toggleFormat; });
__webpack_require__.d(__webpack_exports__, "LINE_SEPARATOR", function() { return /* reexport */ LINE_SEPARATOR; });
__webpack_require__.d(__webpack_exports__, "unregisterFormatType", function() { return /* reexport */ unregisterFormatType; });
__webpack_require__.d(__webpack_exports__, "indentListItems", function() { return /* reexport */ indentListItems; });
__webpack_require__.d(__webpack_exports__, "outdentListItems", function() { return /* reexport */ outdentListItems; });
__webpack_require__.d(__webpack_exports__, "changeListType", function() { return /* reexport */ changeListType; });

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
var external_this_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__("vpQ4");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/reducer.js


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
      return Object(objectSpread["a" /* default */])({}, state, Object(external_lodash_["keyBy"])(action.formatTypes, 'name'));

    case 'REMOVE_FORMAT_TYPES':
      return Object(external_lodash_["omit"])(state, action.names);
  }

  return state;
}
/* harmony default export */ var reducer = (Object(external_this_wp_data_["combineReducers"])({
  formatTypes: reducer_formatTypes
}));

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__("pPDe");

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
  return Object(external_lodash_["find"])(getFormatTypes(state), function (_ref) {
    var tagName = _ref.tagName;
    return bareElementTagName === tagName;
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
  return Object(external_lodash_["find"])(getFormatTypes(state), function (_ref2) {
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
    formatTypes: Object(external_lodash_["castArray"])(formatTypes)
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
    names: Object(external_lodash_["castArray"])(names)
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
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Normalises formats: ensures subsequent equal formats have the same reference.
 *
 * @param  {Object} value Value to normalise formats of.
 *
 * @return {Object} New value with normalised formats.
 */

function normaliseFormats(_ref) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end,
      replacements = _ref.replacements;
  var refs = [];
  var newFormats = formats.map(function (formatsAtIndex) {
    return formatsAtIndex.map(function (format) {
      var equalRef = Object(external_lodash_["find"])(refs, function (ref) {
        return isFormatEqual(ref, format);
      });

      if (equalRef) {
        return equalRef;
      }

      refs.push(format);
      return format;
    });
  });
  return {
    formats: newFormats,
    text: text,
    start: start,
    end: end,
    replacements: replacements
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/apply-format.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Apply a format object to a Rich Text value from the given `startIndex` to the
 * given `endIndex`. Indices are retrieved from the selection if none are
 * provided.
 *
 * @param {Object} value      Value to modify.
 * @param {Object} format     Format to apply.
 * @param {number} startIndex Start index.
 * @param {number} endIndex   End index.
 *
 * @return {Object} A new value with the format applied.
 */

function applyFormat(_ref, format) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end,
      replacements = _ref.replacements;
  var startIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : start;
  var endIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : end;
  var newFormats = formats.slice(0); // The selection is collpased.

  if (startIndex === endIndex) {
    var startFormat = Object(external_lodash_["find"])(newFormats[startIndex], {
      type: format.type
    }); // If the caret is at a format of the same type, expand start and end to
    // the edges of the format. This is useful to apply new attributes.

    if (startFormat) {
      while (Object(external_lodash_["find"])(newFormats[startIndex], startFormat)) {
        applyFormats(newFormats, startIndex, format);
        startIndex--;
      }

      endIndex++;

      while (Object(external_lodash_["find"])(newFormats[endIndex], startFormat)) {
        applyFormats(newFormats, endIndex, format);
        endIndex++;
      } // Otherwise, insert a placeholder with the format so new input appears
      // with the format applied.

    } else {
      var previousFormat = newFormats[startIndex - 1] || [];
      var hasType = Object(external_lodash_["find"])(previousFormat, {
        type: format.type
      });
      return {
        formats: formats,
        text: text,
        start: start,
        end: end,
        replacements: replacements,
        formatPlaceholder: {
          index: startIndex,
          format: hasType ? undefined : format
        }
      };
    }
  } else {
    for (var index = startIndex; index < endIndex; index++) {
      applyFormats(newFormats, index, format);
    }
  }

  return normaliseFormats({
    formats: newFormats,
    text: text,
    start: start,
    end: end,
    replacements: replacements
  });
}

function applyFormats(formats, index, format) {
  if (formats[index]) {
    var newFormatsAtIndex = formats[index].filter(function (_ref2) {
      var type = _ref2.type;
      return type !== format.type;
    });
    newFormatsAtIndex.push(format);
    formats[index] = newFormatsAtIndex;
  } else {
    formats[index] = [format];
  }
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/char-at.js
/**
 * Gets the character at the specified index, or returns `undefined` if no
 * character was found.
 *
 * @param {Object} value Value to get the character from.
 * @param {string} index Index to use.
 *
 * @return {?string} A one character long string, or undefined.
 */
function charAt(_ref, index) {
  var text = _ref.text;
  return text[index];
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/concat.js
/**
 * Internal dependencies
 */

/**
 * Combine all Rich Text values into one. This is similar to
 * `String.prototype.concat`.
 *
 * @param {...[object]} values An array of all values to combine.
 *
 * @return {Object} A new value combining all given records.
 */

function concat() {
  for (var _len = arguments.length, values = new Array(_len), _key = 0; _key < _len; _key++) {
    values[_key] = arguments[_key];
  }

  return normaliseFormats(values.reduce(function (accumlator, _ref) {
    var formats = _ref.formats,
        text = _ref.text;
    return {
      text: accumlator.text + text,
      formats: accumlator.formats.concat(formats)
    };
  }));
}

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__("KQm4");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/typeof.js
var esm_typeof = __webpack_require__("U8pU");

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/special-characters.js
var LINE_SEPARATOR = "\u2028";
var OBJECT_REPLACEMENT_CHARACTER = "\uFFFC";
var ZERO_WIDTH_NO_BREAK_SPACE = "\uFEFF";

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-empty.js

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

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/create.js



/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





/**
 * Browser dependencies
 */

var _window$Node = window.Node,
    TEXT_NODE = _window$Node.TEXT_NODE,
    ELEMENT_NODE = _window$Node.ELEMENT_NODE;

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
 * @param {?Object}   $1                      Optional named argements.
 * @param {?Element}  $1.element              Element to create value from.
 * @param {?string}   $1.text                 Text to create value from.
 * @param {?string}   $1.html                 HTML to create value from.
 * @param {?Range}    $1.range                Range to create value from.
 * @param {?string}   $1.multilineTag         Multiline tag if the structure is
 *                                            multiline.
 * @param {?Array}    $1.multilineWrapperTags Tags where lines can be found if
 *                                            nesting is possible.
 * @param {?Function} $1.removeNode           Function to declare whether the
 *                                            given node should be removed.
 * @param {?Function} $1.unwrapNode           Function to declare whether the
 *                                            given node should be unwrapped.
 * @param {?Function} $1.filterString         Function to filter the given
 *                                            string.
 * @param {?Function} $1.removeAttribute      Wether to remove an attribute
 *                                            based on the name.
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
      removeNode = _ref2.removeNode,
      unwrapNode = _ref2.unwrapNode,
      filterString = _ref2.filterString,
      removeAttribute = _ref2.removeAttribute;

  if (typeof text === 'string' && text.length > 0) {
    return {
      formats: Array(text.length),
      replacements: Array(text.length),
      text: text
    };
  }

  if (typeof html === 'string' && html.length > 0) {
    element = createElement(document, html);
  }

  if (Object(esm_typeof["a" /* default */])(element) !== 'object') {
    return createEmptyValue();
  }

  if (!multilineTag) {
    return createFromElement({
      element: element,
      range: range,
      removeNode: removeNode,
      unwrapNode: unwrapNode,
      filterString: filterString,
      removeAttribute: removeAttribute
    });
  }

  return createFromMultilineElement({
    element: element,
    range: range,
    multilineTag: multilineTag,
    multilineWrapperTags: multilineWrapperTags,
    removeNode: removeNode,
    unwrapNode: unwrapNode,
    filterString: filterString,
    removeAttribute: removeAttribute
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
  } else if (node === startContainer && node.nodeType === TEXT_NODE) {
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
  } else if (node === endContainer && node.nodeType === TEXT_NODE) {
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
 * Creates a Rich Text value from a DOM element and range.
 *
 * @param {Object}    $1                      Named argements.
 * @param {?Element}  $1.element              Element to create value from.
 * @param {?Range}    $1.range                Range to create value from.
 * @param {?string}   $1.multilineTag         Multiline tag if the structure is
 *                                            multiline.
 * @param {?Array}    $1.multilineWrapperTags Tags where lines can be found if
 *                                            nesting is possible.
 * @param {?Function} $1.removeNode           Function to declare whether the
 *                                            given node should be removed.
 * @param {?Function} $1.unwrapNode           Function to declare whether the
 *                                            given node should be unwrapped.
 * @param {?Function} $1.filterString         Function to filter the given
 *                                            string.
 * @param {?Function} $1.removeAttribute      Wether to remove an attribute
 *                                            based on the name.
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
      removeNode = _ref3.removeNode,
      unwrapNode = _ref3.unwrapNode,
      filterString = _ref3.filterString,
      removeAttribute = _ref3.removeAttribute;
  var accumulator = createEmptyValue();

  if (!element) {
    return accumulator;
  }

  if (!element.hasChildNodes()) {
    accumulateSelection(accumulator, element, range, createEmptyValue());
    return accumulator;
  }

  var length = element.childNodes.length;

  var filterStringComplete = function filterStringComplete(string) {
    // Reduce any whitespace used for HTML formatting to one space
    // character, because it will also be displayed as such by the browser.
    string = string.replace(/[\n\r\t]+/g, ' ');

    if (filterString) {
      string = filterString(string);
    }

    return string;
  }; // Optimise for speed.


  for (var index = 0; index < length; index++) {
    var node = element.childNodes[index];
    var type = node.nodeName.toLowerCase();

    if (node.nodeType === TEXT_NODE) {
      var _text = filterStringComplete(node.nodeValue);

      range = filterRange(node, range, filterStringComplete);
      accumulateSelection(accumulator, node, range, {
        text: _text
      });
      accumulator.text += _text; // Create a sparse array of the same length as `text`, in which
      // formats can be added.

      accumulator.formats.length += _text.length;
      accumulator.replacements.length += _text.length;
      continue;
    }

    if (node.nodeType !== ELEMENT_NODE) {
      continue;
    }

    if (removeNode && removeNode(node) || unwrapNode && unwrapNode(node) && !node.hasChildNodes()) {
      accumulateSelection(accumulator, node, range, createEmptyValue());
      continue;
    }

    if (type === 'script') {
      var _value = {
        formats: [,],
        replacements: [{
          type: type,
          attributes: {
            'data-rich-text-script': node.getAttribute('data-rich-text-script') || encodeURIComponent(node.innerHTML)
          }
        }],
        text: OBJECT_REPLACEMENT_CHARACTER
      };
      accumulateSelection(accumulator, node, range, _value);
      accumulator.formats.length += 1;
      accumulator.replacements = accumulator.replacements.concat(_value.replacements);
      accumulator.text += OBJECT_REPLACEMENT_CHARACTER;
      continue;
    }

    if (type === 'br') {
      accumulateSelection(accumulator, node, range, createEmptyValue());
      accumulator.text += '\n';
      accumulator.formats.length += 1;
      accumulator.replacements.length += 1;
      continue;
    }

    var lastFormats = accumulator.formats[accumulator.formats.length - 1];
    var lastFormat = lastFormats && lastFormats[lastFormats.length - 1];
    var format = void 0;
    var value = void 0;

    if (!unwrapNode || !unwrapNode(node)) {
      var newFormat = toFormat({
        type: type,
        attributes: getAttributes({
          element: node,
          removeAttribute: removeAttribute
        })
      });

      if (newFormat) {
        // Reuse the last format if it's equal.
        if (isFormatEqual(newFormat, lastFormat)) {
          format = lastFormat;
        } else {
          format = newFormat;
        }
      }
    }

    if (multilineWrapperTags && multilineWrapperTags.indexOf(type) !== -1) {
      value = createFromMultilineElement({
        element: node,
        range: range,
        multilineTag: multilineTag,
        multilineWrapperTags: multilineWrapperTags,
        removeNode: removeNode,
        unwrapNode: unwrapNode,
        filterString: filterString,
        removeAttribute: removeAttribute,
        currentWrapperTags: Object(toConsumableArray["a" /* default */])(currentWrapperTags).concat([format])
      });
      format = undefined;
    } else {
      value = createFromElement({
        element: node,
        range: range,
        multilineTag: multilineTag,
        multilineWrapperTags: multilineWrapperTags,
        removeNode: removeNode,
        unwrapNode: unwrapNode,
        filterString: filterString,
        removeAttribute: removeAttribute
      });
    }

    var text = value.text;
    var start = accumulator.text.length;
    accumulateSelection(accumulator, node, range, value); // Don't apply the element as formatting if it has no content.

    if (isEmpty(value) && format && !format.attributes) {
      continue;
    }

    var formats = accumulator.formats;

    if (format && format.attributes && text.length === 0) {
      var lastReplacement = accumulator.replacements[accumulator.replacements.length - 1];
      format.object = true;

      if (isFormatEqual(lastReplacement, format)) {
        return accumulator;
      }

      accumulator.text += OBJECT_REPLACEMENT_CHARACTER;
      accumulator.replacements.push(format);
      accumulator.formats.length += 1;
    } else {
      accumulator.text += text;
      accumulator.formats.length += text.length;
      accumulator.replacements.length += text.length;
      var i = value.formats.length; // Optimise for speed.

      while (i--) {
        var formatIndex = start + i;

        if (format) {
          if (formats[formatIndex]) {
            formats[formatIndex].push(format);
          } else {
            formats[formatIndex] = [format];
          }
        }

        if (value.formats[i]) {
          if (formats[formatIndex]) {
            var _formats$formatIndex;

            (_formats$formatIndex = formats[formatIndex]).push.apply(_formats$formatIndex, Object(toConsumableArray["a" /* default */])(value.formats[i]));
          } else {
            formats[formatIndex] = value.formats[i];
          }
        }

        if (value.replacements[i]) {
          accumulator.replacements[formatIndex] = value.replacements[i];
        }
      }
    }
  }

  return accumulator;
}
/**
 * Creates a rich text value from a DOM element and range that should be
 * multiline.
 *
 * @param {Object}    $1                      Named argements.
 * @param {?Element}  $1.element              Element to create value from.
 * @param {?Range}    $1.range                Range to create value from.
 * @param {?string}   $1.multilineTag         Multiline tag if the structure is
 *                                            multiline.
 * @param {?Array}    $1.multilineWrapperTags Tags where lines can be found if
 *                                            nesting is possible.
 * @param {?Function} $1.removeNode           Function to declare whether the
 *                                            given node should be removed.
 * @param {?Function} $1.unwrapNode           Function to declare whether the
 *                                            given node should be unwrapped.
 * @param {?Function} $1.filterString         Function to filter the given
 *                                            string.
 * @param {?Function} $1.removeAttribute      Wether to remove an attribute
 *                                            based on the name.
 * @param {boolean}   $1.currentWrapperTags   Whether to prepend a line
 *                                            separator.
 *
 * @return {Object} A rich text value.
 */


function createFromMultilineElement(_ref4) {
  var element = _ref4.element,
      range = _ref4.range,
      multilineTag = _ref4.multilineTag,
      multilineWrapperTags = _ref4.multilineWrapperTags,
      removeNode = _ref4.removeNode,
      unwrapNode = _ref4.unwrapNode,
      filterString = _ref4.filterString,
      removeAttribute = _ref4.removeAttribute,
      _ref4$currentWrapperT = _ref4.currentWrapperTags,
      currentWrapperTags = _ref4$currentWrapperT === void 0 ? [] : _ref4$currentWrapperT;
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
      removeNode: removeNode,
      unwrapNode: unwrapNode,
      filterString: filterString,
      removeAttribute: removeAttribute
    }); // If a line consists of one single line break (invisible), consider the
    // line empty, wether this is the browser's doing or not.

    if (value.text === '\n') {
      var start = value.start;
      var end = value.end;
      value = createEmptyValue();

      if (start !== undefined) {
        value.start = 0;
      }

      if (end !== undefined) {
        value.end = 0;
      }
    } // Multiline value text should be separated by a double line break.


    if (index !== 0 || currentWrapperTags.length > 0) {
      var formats = currentWrapperTags.length > 0 ? [currentWrapperTags] : [,];
      accumulator.formats = accumulator.formats.concat(formats);
      accumulator.replacements.length += 1;
      accumulator.text += LINE_SEPARATOR;
    }

    accumulateSelection(accumulator, node, range, value);
    accumulator.formats = accumulator.formats.concat(value.formats);
    accumulator.replacements = accumulator.replacements.concat(value.replacements);
    accumulator.text += value.text;
  }

  return accumulator;
}
/**
 * Gets the attributes of an element in object shape.
 *
 * @param {Object}    $1                 Named argements.
 * @param {Element}   $1.element         Element to get attributes from.
 * @param {?Function} $1.removeAttribute Wether to remove an attribute based on
 *                                       the name.
 *
 * @return {?Object} Attribute object or `undefined` if the element has no
 *                   attributes.
 */


function getAttributes(_ref5) {
  var element = _ref5.element,
      removeAttribute = _ref5.removeAttribute;

  if (!element.hasAttributes()) {
    return;
  }

  var length = element.attributes.length;
  var accumulator; // Optimise for speed.

  for (var i = 0; i < length; i++) {
    var _element$attributes$i = element.attributes[i],
        name = _element$attributes$i.name,
        value = _element$attributes$i.value;

    if (removeAttribute && removeAttribute(name)) {
      continue;
    }

    var safeName = /^on/i.test(name) ? 'data-disable-rich-text-' + name : name;
    accumulator = accumulator || {};
    accumulator[safeName] = value;
  }

  return accumulator;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-active-format.js
/**
 * External dependencies
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
 * @return {?Object} Active format object of the specified type, or undefined.
 */

function getActiveFormat(_ref, formatType) {
  var formats = _ref.formats,
      start = _ref.start;

  if (start === undefined) {
    return;
  }

  return Object(external_lodash_["find"])(formats[start], {
    type: formatType
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-selection-end.js
/**
 * Gets the end index of the current selection, or returns `undefined` if no
 * selection exists. The selection ends right before the character at this
 * index.
 *
 * @param {Object} value Value to get the selection from.
 *
 * @return {?number} Index where the selection ends.
 */
function getSelectionEnd(_ref) {
  var end = _ref.end;
  return end;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-selection-start.js
/**
 * Gets the start index of the current selection, or returns `undefined` if no
 * selection exists. The selection starts right before the character at this
 * index.
 *
 * @param {Object} value Value to get the selection from.
 *
 * @return {?number} Index where the selection starts.
 */
function getSelectionStart(_ref) {
  var start = _ref.start;
  return start;
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

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-collapsed.js
/**
 * Check if the selection of a Rich Text value is collapsed or not. Collapsed
 * means that no characters are selected, but there is a caret present. If there
 * is no selection, `undefined` will be returned. This is similar to
 * `window.getSelection().isCollapsed()`.
 *
 * @param {Object} value The rich text value to check.
 *
 * @return {?boolean} True if the selection is collapsed, false if not,
 *                    undefined if there is no selection.
 */
function isCollapsed(_ref) {
  var start = _ref.start,
      end = _ref.end;

  if (start === undefined || end === undefined) {
    return;
  }

  return start === end;
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
 * @param {Array}         values    An array of values to join.
 * @param {string|Object} separator Separator string or value.
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
        text = _ref.text;
    return {
      text: accumlator.text + separator.text + text,
      formats: accumlator.formats.concat(separator.formats, formats)
    };
  }));
}

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__("rePB");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: ./node_modules/memize/index.js
var memize = __webpack_require__("4eJC");
var memize_default = /*#__PURE__*/__webpack_require__.n(memize);

// EXTERNAL MODULE: external {"this":["wp","hooks"]}
var external_this_wp_hooks_ = __webpack_require__("g56x");

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__("K9lf");

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/register-format-type.js






/**
 * External dependencies
 */


/**
 * WordPress dependencies
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
 * Registers a new format provided a unique name and an object defining its
 * behavior.
 *
 * @param {string} name     Format name.
 * @param {Object} settings Format settings.
 *
 * @return {?WPFormat} The format, if it has been successfully registered;
 *                     otherwise `undefined`.
 */

function registerFormatType(name, settings) {
  settings = Object(objectSpread["a" /* default */])({
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
  var getFunctionStackMemoized = memize_default()(function () {
    var previousStack = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : EMPTY_ARRAY;
    var newFunction = arguments.length > 1 ? arguments[1] : undefined;
    return Object(toConsumableArray["a" /* default */])(previousStack).concat([newFunction]);
  });

  if (settings.__experimentalGetPropsForEditableTreePreparation) {
    Object(external_this_wp_hooks_["addFilter"])('experimentalRichText', name, function (OriginalComponent) {
      var Component = OriginalComponent;

      if (settings.__experimentalCreatePrepareEditableTree || settings.__experimentalCreateFormatToValue || settings.__experimentalCreateValueToFormat) {
        Component = function Component(props) {
          var additionalProps = {};

          if (settings.__experimentalCreatePrepareEditableTree) {
            additionalProps.prepareEditableTree = getFunctionStackMemoized(props.prepareEditableTree, settings.__experimentalCreatePrepareEditableTree(props["format_".concat(name)], {
              richTextIdentifier: props.identifier,
              blockClientId: props.clientId
            }));
          }

          if (settings.__experimentalCreateOnChangeEditableValue) {
            var dispatchProps = Object.keys(props).reduce(function (accumulator, propKey) {
              var propValue = props[propKey];
              var keyPrefix = "format_".concat(name, "_dispatch_");

              if (propKey.startsWith(keyPrefix)) {
                var realKey = propKey.replace(keyPrefix, '');
                accumulator[realKey] = propValue;
              }

              return accumulator;
            }, {});
            additionalProps.onChangeEditableValue = getFunctionStackMemoized(props.onChangeEditableValue, settings.__experimentalCreateOnChangeEditableValue(Object(objectSpread["a" /* default */])({}, props["format_".concat(name)], dispatchProps), {
              richTextIdentifier: props.identifier,
              blockClientId: props.clientId
            }));
          }

          return Object(external_this_wp_element_["createElement"])(OriginalComponent, Object(esm_extends["a" /* default */])({}, props, additionalProps));
        };
      }

      var hocs = [Object(external_this_wp_data_["withSelect"])(function (sel, _ref) {
        var clientId = _ref.clientId,
            identifier = _ref.identifier;
        return Object(defineProperty["a" /* default */])({}, "format_".concat(name), settings.__experimentalGetPropsForEditableTreePreparation(sel, {
          richTextIdentifier: identifier,
          blockClientId: clientId
        }));
      })];

      if (settings.__experimentalGetPropsForEditableTreeChangeHandler) {
        hocs.push(Object(external_this_wp_data_["withDispatch"])(function (disp, _ref3) {
          var clientId = _ref3.clientId,
              identifier = _ref3.identifier;

          var dispatchProps = settings.__experimentalGetPropsForEditableTreeChangeHandler(disp, {
            richTextIdentifier: identifier,
            blockClientId: clientId
          });

          return Object(external_lodash_["mapKeys"])(dispatchProps, function (value, key) {
            return "format_".concat(name, "_dispatch_").concat(key);
          });
        }));
      }

      return Object(external_this_wp_compose_["compose"])(hocs)(Component);
    });
  }

  return settings;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/remove-format.js
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
 * @param {Object} value      Value to modify.
 * @param {string} formatType Format type to remove.
 * @param {number} startIndex Start index.
 * @param {number} endIndex   End index.
 *
 * @return {Object} A new value with the format applied.
 */

function removeFormat(_ref, formatType) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end,
      replacements = _ref.replacements;
  var startIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : start;
  var endIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : end;
  var newFormats = formats.slice(0); // If the selection is collapsed, expand start and end to the edges of the
  // format.

  if (startIndex === endIndex) {
    var format = Object(external_lodash_["find"])(newFormats[startIndex], {
      type: formatType
    });

    while (Object(external_lodash_["find"])(newFormats[startIndex], format)) {
      filterFormats(newFormats, startIndex, formatType);
      startIndex--;
    }

    endIndex++;

    while (Object(external_lodash_["find"])(newFormats[endIndex], format)) {
      filterFormats(newFormats, endIndex, formatType);
      endIndex++;
    }
  } else {
    for (var i = startIndex; i < endIndex; i++) {
      if (newFormats[i]) {
        filterFormats(newFormats, i, formatType);
      }
    }
  }

  return normaliseFormats({
    formats: newFormats,
    text: text,
    start: start,
    end: end,
    replacements: replacements
  });
}

function filterFormats(formats, index, formatType) {
  var newFormats = formats[index].filter(function (_ref2) {
    var type = _ref2.type;
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
 * @param {Object} value         Value to modify.
 * @param {string} valueToInsert Value to insert.
 * @param {number} startIndex    Start index.
 * @param {number} endIndex      End index.
 *
 * @return {Object} A new value with the value inserted.
 */

function insert(_ref, valueToInsert) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end,
      replacements = _ref.replacements;
  var startIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : start;
  var endIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : end;

  if (typeof valueToInsert === 'string') {
    valueToInsert = create({
      text: valueToInsert
    });
  }

  var index = startIndex + valueToInsert.text.length;
  return normaliseFormats({
    formats: formats.slice(0, startIndex).concat(valueToInsert.formats, formats.slice(endIndex)),
    text: text.slice(0, startIndex) + valueToInsert.text + text.slice(endIndex),
    replacements: replacements.slice(0, startIndex).concat(valueToInsert.replacements, replacements.slice(endIndex)),
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
 * @param {Object} value      Value to modify.
 * @param {number} startIndex Start index.
 * @param {number} endIndex   End index.
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

function replace(_ref, pattern, replacement) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end,
      replacements = _ref.replacements;
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
 * @param {Object} value         Value to modify.
 * @param {number} startIndex    Start index.
 * @param {number} endIndex      End index.
 *
 * @return {Object} A new value with the value inserted.
 */

function insertLineSeparator(value) {
  var startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : value.start;
  var endIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : value.end;
  var beforeText = getTextContent(value).slice(0, startIndex);
  var previousLineSeparatorIndex = beforeText.lastIndexOf(LINE_SEPARATOR);
  var previousLineSeparatorFormats = value.formats[previousLineSeparatorIndex];
  var formats = [,];

  if (previousLineSeparatorFormats) {
    formats = [previousLineSeparatorFormats];
  }

  var valueToInsert = {
    formats: formats,
    replacements: [,],
    text: LINE_SEPARATOR
  };
  return insert(value, valueToInsert, startIndex, endIndex);
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
 * @param {number} startIndex     Start index.
 * @param {number} endIndex       End index.
 *
 * @return {Object} A new value with the object inserted.
 */

function insertObject(value, formatToInsert, startIndex, endIndex) {
  var valueToInsert = {
    text: insert_object_OBJECT_REPLACEMENT_CHARACTER,
    replacements: [Object(objectSpread["a" /* default */])({}, formatToInsert, {
      object: true
    })],
    formats: [,]
  };
  return insert(value, valueToInsert, startIndex, endIndex);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/slice.js
/**
 * Slice a Rich Text value from `startIndex` to `endIndex`. Indices are
 * retrieved from the selection if none are provided. This is similar to
 * `String.prototype.slice`.
 *
 * @param {Object} value       Value to modify.
 * @param {number} startIndex  Start index.
 * @param {number} endIndex    End index.
 *
 * @return {Object} A new extracted value.
 */
function slice(_ref) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end,
      replacements = _ref.replacements;
  var startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : start;
  var endIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : end;

  if (startIndex === undefined || endIndex === undefined) {
    return {
      formats: formats,
      text: text,
      replacements: replacements
    };
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
 * @param {Object}        value   Value to modify.
 * @param {number|string} string  Start index, or string at which to split.
 * @param {number}        end     End index.
 *
 * @return {Array} An array of new values.
 */

function split(_ref, string) {
  var formats = _ref.formats,
      text = _ref.text,
      start = _ref.start,
      end = _ref.end,
      replacements = _ref.replacements;

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
      text = _ref2.text,
      start = _ref2.start,
      end = _ref2.end,
      replacements = _ref2.replacements;
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
  replace(before, /\u2028+$/, ''), replace(after, /^\u2028+/, '')];
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



/**
 * Internal dependencies
 */



function restoreOnAttributes(attributes, isEditableTree) {
  if (isEditableTree) {
    return attributes;
  }

  var newAttributes = {};

  for (var key in attributes) {
    var newKey = key;

    if (key.startsWith('data-disable-rich-text-')) {
      newKey = key.slice('data-disable-rich-text-'.length);
    }

    newAttributes[newKey] = attributes[key];
  }

  return newAttributes;
}

function fromFormat(_ref) {
  var type = _ref.type,
      attributes = _ref.attributes,
      unregisteredAttributes = _ref.unregisteredAttributes,
      object = _ref.object,
      isEditableTree = _ref.isEditableTree;
  var formatType = get_format_type_getFormatType(type);

  if (!formatType) {
    return {
      type: type,
      attributes: restoreOnAttributes(attributes, isEditableTree),
      object: object
    };
  }

  var elementAttributes = Object(objectSpread["a" /* default */])({}, unregisteredAttributes);

  for (var name in attributes) {
    var key = formatType.attributes[name];

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
    attributes: restoreOnAttributes(elementAttributes, isEditableTree)
  };
}

function toTree(_ref2) {
  var value = _ref2.value,
      multilineTag = _ref2.multilineTag,
      _ref2$multilineWrappe = _ref2.multilineWrapperTags,
      multilineWrapperTags = _ref2$multilineWrappe === void 0 ? [] : _ref2$multilineWrappe,
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
      isEditableTree = _ref2.isEditableTree;
  var formats = value.formats,
      text = value.text,
      start = value.start,
      end = value.end,
      formatPlaceholder = value.formatPlaceholder,
      replacements = value.replacements;
  var formatsLength = formats.length + 1;
  var tree = createEmpty();
  var multilineFormat = {
    type: multilineTag
  };
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

  function setFormatPlaceholder(pointer, index) {
    if (isEditableTree && formatPlaceholder && formatPlaceholder.index === index) {
      var parent = getParent(pointer);

      if (formatPlaceholder.format === undefined) {
        pointer = getParent(parent);
      } else {
        pointer = append(parent, fromFormat(formatPlaceholder.format));
      }

      pointer = append(pointer, ZERO_WIDTH_NO_BREAK_SPACE);
    }

    return pointer;
  }

  var _loop = function _loop(i) {
    var character = text.charAt(i);
    var characterFormats = formats[i]; // Set multiline tags in queue for building the tree.

    if (multilineTag) {
      if (character === LINE_SEPARATOR) {
        characterFormats = lastSeparatorFormats = (characterFormats || []).reduce(function (accumulator, format) {
          if (character === LINE_SEPARATOR && multilineWrapperTags.indexOf(format.type) !== -1) {
            accumulator.push(format);
            accumulator.push(multilineFormat);
          }

          return accumulator;
        }, [multilineFormat]);
      } else {
        characterFormats = Object(toConsumableArray["a" /* default */])(lastSeparatorFormats).concat(Object(toConsumableArray["a" /* default */])(characterFormats || []));
      }
    }

    var pointer = getLastChild(tree); // Set selection for the start of line.

    if (lastCharacter === LINE_SEPARATOR) {
      var node = pointer;

      while (!isText(node)) {
        node = getLastChild(node);
      }

      if (onStartIndex && start === i) {
        onStartIndex(tree, node);
      }

      if (onEndIndex && end === i) {
        onEndIndex(tree, node);
      }
    }

    if (characterFormats) {
      characterFormats.forEach(function (format, formatIndex) {
        if (pointer && lastCharacterFormats && format === lastCharacterFormats[formatIndex] && ( // Do not reuse the last element if the character is a
        // line separator.
        character !== LINE_SEPARATOR || characterFormats.length - 1 !== formatIndex)) {
          pointer = getLastChild(pointer);
          return;
        }

        var parent = getParent(pointer);
        var type = format.type,
            attributes = format.attributes,
            unregisteredAttributes = format.unregisteredAttributes;
        var newNode = append(parent, fromFormat({
          type: type,
          attributes: attributes,
          unregisteredAttributes: unregisteredAttributes,
          isEditableTree: isEditableTree
        }));

        if (isText(pointer) && getText(pointer).length === 0) {
          remove(pointer);
        }

        pointer = append(format.object ? parent : newNode, '');
      });
    } // No need for further processing if the character is a line separator.


    if (character === LINE_SEPARATOR) {
      lastCharacterFormats = characterFormats;
      lastCharacter = character;
      return "continue";
    }

    pointer = setFormatPlaceholder(pointer, 0); // If there is selection at 0, handle it before characters are inserted.

    if (i === 0) {
      if (onStartIndex && start === 0) {
        onStartIndex(tree, pointer);
      }

      if (onEndIndex && end === 0) {
        onEndIndex(tree, pointer);
      }
    }

    if (character === OBJECT_REPLACEMENT_CHARACTER) {
      if (!isEditableTree && replacements[i].type === 'script') {
        pointer = append(getParent(pointer), fromFormat({
          type: 'script',
          isEditableTree: isEditableTree
        }));
        append(pointer, {
          html: decodeURIComponent(replacements[i].attributes['data-rich-text-script'])
        });
      } else {
        pointer = append(getParent(pointer), fromFormat(Object(objectSpread["a" /* default */])({}, replacements[i], {
          object: true,
          isEditableTree: isEditableTree
        })));
      } // Ensure pointer is text node.


      pointer = append(getParent(pointer), '');
    } else if (character === '\n') {
      pointer = append(getParent(pointer), {
        type: 'br',
        object: true
      }); // Ensure pointer is text node.

      pointer = append(getParent(pointer), '');
    } else if (!isText(pointer)) {
      pointer = append(getParent(pointer), character);
    } else {
      appendText(pointer, character);
    }

    pointer = setFormatPlaceholder(pointer, i + 1);

    if (onStartIndex && start === i + 1) {
      onStartIndex(tree, pointer);
    }

    if (onEndIndex && end === i + 1) {
      onEndIndex(tree, pointer);
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



/**
 * Internal dependencies
 */


/**
 * Browser dependencies
 */

var to_dom_window$Node = window.Node,
    to_dom_TEXT_NODE = to_dom_window$Node.TEXT_NODE,
    to_dom_ELEMENT_NODE = to_dom_window$Node.ELEMENT_NODE;
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
/**
 * Returns a new instance of a DOM tree upon which RichText operations can be
 * applied.
 *
 * Note: The current implementation will return a shared reference, reset on
 * each call to `createEmpty`. Therefore, you should not hold a reference to
 * the value to operate upon asynchronously, as it may have unexpected results.
 *
 * @return {WPRichTextTree} RichText tree.
 */


var to_dom_createEmpty = function createEmpty() {
  return createElement(document, '');
};

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

function to_dom_isText(_ref3) {
  var nodeType = _ref3.nodeType;
  return nodeType === to_dom_TEXT_NODE;
}

function to_dom_getText(_ref4) {
  var nodeValue = _ref4.nodeValue;
  return nodeValue;
}

function to_dom_remove(node) {
  return node.parentNode.removeChild(node);
}

function padEmptyLines(_ref5) {
  var element = _ref5.element,
      createLinePadding = _ref5.createLinePadding,
      multilineWrapperTags = _ref5.multilineWrapperTags;
  var length = element.childNodes.length;
  var doc = element.ownerDocument;

  for (var index = 0; index < length; index++) {
    var child = element.childNodes[index];

    if (child.nodeType === to_dom_TEXT_NODE) {
      if (length === 1 && !child.nodeValue) {
        // Pad if the only child is an empty text node.
        element.appendChild(createLinePadding(doc));
      }
    } else {
      if (multilineWrapperTags && !child.previousSibling && multilineWrapperTags.indexOf(child.nodeName.toLowerCase()) !== -1) {
        // Pad the line if there is no content before a nested wrapper.
        element.insertBefore(createLinePadding(doc), child);
      }

      padEmptyLines({
        element: child,
        createLinePadding: createLinePadding,
        multilineWrapperTags: multilineWrapperTags
      });
    }
  }
}

function prepareFormats() {
  var prepareEditableTree = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var value = arguments.length > 1 ? arguments[1] : undefined;
  return prepareEditableTree.reduce(function (accumlator, fn) {
    return fn(accumlator, value.text);
  }, value.formats);
}

function toDom(_ref6) {
  var value = _ref6.value,
      multilineTag = _ref6.multilineTag,
      multilineWrapperTags = _ref6.multilineWrapperTags,
      createLinePadding = _ref6.createLinePadding,
      prepareEditableTree = _ref6.prepareEditableTree;
  var startPath = [];
  var endPath = [];
  var tree = toTree({
    value: Object(objectSpread["a" /* default */])({}, value, {
      formats: prepareFormats(prepareEditableTree, value)
    }),
    multilineTag: multilineTag,
    multilineWrapperTags: multilineWrapperTags,
    createEmpty: to_dom_createEmpty,
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
    isEditableTree: true
  });

  if (createLinePadding) {
    padEmptyLines({
      element: tree,
      createLinePadding: createLinePadding,
      multilineWrapperTags: multilineWrapperTags
    });
  }

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
 * @param {Object}      value        Value to apply.
 * @param {HTMLElement} current      The live root node to apply the element
 *                                   tree to.
 * @param {string}      multilineTag Multiline tag.
 */

function apply(_ref7) {
  var value = _ref7.value,
      current = _ref7.current,
      multilineTag = _ref7.multilineTag,
      multilineWrapperTags = _ref7.multilineWrapperTags,
      createLinePadding = _ref7.createLinePadding,
      prepareEditableTree = _ref7.prepareEditableTree;

  // Construct a new element tree in memory.
  var _toDom = toDom({
    value: value,
    multilineTag: multilineTag,
    multilineWrapperTags: multilineWrapperTags,
    createLinePadding: createLinePadding,
    prepareEditableTree: prepareEditableTree
  }),
      body = _toDom.body,
      selection = _toDom.selection;

  applyValue(body, current);

  if (value.start !== undefined) {
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
      current.replaceChild(futureChild, currentChild);
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

function applySelection(selection, current) {
  var _getNodeByPath = getNodeByPath(current, selection.startPath),
      startContainer = _getNodeByPath.node,
      startOffset = _getNodeByPath.offset;

  var _getNodeByPath2 = getNodeByPath(current, selection.endPath),
      endContainer = _getNodeByPath2.node,
      endOffset = _getNodeByPath2.offset;

  var windowSelection = window.getSelection();
  var range = current.ownerDocument.createRange();
  var collapsed = startContainer === endContainer && startOffset === endOffset;

  if (collapsed && startOffset === 0 && startContainer.previousSibling && startContainer.previousSibling.nodeType === to_dom_ELEMENT_NODE && startContainer.previousSibling.nodeName !== 'BR') {
    startContainer.insertData(0, "\uFEFF");
    range.setStart(startContainer, 1);
    range.setEnd(endContainer, 1);
  } else if (collapsed && startOffset === 0 && startContainer === to_dom_TEXT_NODE && startContainer.nodeValue.length === 0) {
    startContainer.insertData(0, "\uFEFF");
    range.setStart(startContainer, 1);
    range.setEnd(endContainer, 1);
  } else {
    range.setStart(startContainer, startOffset);
    range.setEnd(endContainer, endOffset);
  }

  if (windowSelection.rangeCount > 0) {
    // If the to be added range and the live range are the same, there's no
    // need to remove the live range and add the equivalent range.
    if (isRangeEqual(range, windowSelection.getRangeAt(0))) {
      return;
    }

    windowSelection.removeAllRanges();
  }

  windowSelection.addRange(range);
}

// EXTERNAL MODULE: external {"this":["wp","escapeHtml"]}
var external_this_wp_escapeHtml_ = __webpack_require__("Vx3V");

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/to-html-string.js
/**
 * Internal dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Create an HTML string from a Rich Text value. If a `multilineTag` is
 * provided, text separated by a line separator will be wrapped in it.
 *
 * @param {Object} $1                      Named argements.
 * @param {Object} $1.value                Rich text value.
 * @param {string} $1.multilineTag         Multiline tag.
 * @param {Array}  $1.multilineWrapperTags Tags where lines can be found if
 *                                         nesting is possible.
 *
 * @return {string} HTML string.
 */

function toHTMLString(_ref) {
  var value = _ref.value,
      multilineTag = _ref.multilineTag,
      multilineWrapperTags = _ref.multilineWrapperTags;
  var tree = toTree({
    value: value,
    multilineTag: multilineTag,
    multilineWrapperTags: multilineWrapperTags,
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
    if (child.html !== undefined) {
      return child.html;
    }

    return child.text === undefined ? createElementHTML(child) : Object(external_this_wp_escapeHtml_["escapeHTML"])(child.text);
  }).join('');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/toggle-format.js
/**
 * Internal dependencies
 */



/**
 * Toggles a format object to a Rich Text value at the current selection.
 *
 * @param {Object} value      Value to modify.
 * @param {Object} format     Format to apply or remove.
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


/**
 * Unregisters a format.
 *
 * @param {string} name Format name.
 *
 * @return {?WPFormat} The previous format value, if it has been successfully
 *                     unregistered; otherwise `undefined`.
 */

function unregisterFormatType(name) {
  var oldFormat = Object(external_this_wp_data_["select"])('core/rich-text').getFormatType(name);

  if (!oldFormat) {
    window.console.error("Format ".concat(name, " is not registered."));
    return;
  }

  if (oldFormat.__experimentalCreatePrepareEditableTree && oldFormat.__experimentalGetPropsForEditableTreePreparation) {
    Object(external_this_wp_hooks_["removeFilter"])('experimentalRichText', name);
  }

  Object(external_this_wp_data_["dispatch"])('core/rich-text').removeFormatTypes(name);
  return oldFormat;
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

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/indent-list-items.js
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
      formats = _ref.formats;
  var startFormats = formats[lineIndex] || [];
  var index = lineIndex;

  while (index-- >= 0) {
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    }

    var formatsAtIndex = formats[index] || []; // Return the first line index that is one level higher. If the level is
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
 * @param {Object} rootFormat
 *
 * @return {Object} The changed value.
 */


function indentListItems(value, rootFormat) {
  var lineIndex = getLineIndex(value); // There is only one line, so the line cannot be indented.

  if (lineIndex === undefined) {
    return value;
  }

  var text = value.text,
      formats = value.formats,
      start = value.start,
      end = value.end;
  var previousLineIndex = getLineIndex(value, lineIndex);
  var formatsAtLineIndex = formats[lineIndex] || [];
  var formatsAtPreviousLineIndex = formats[previousLineIndex] || []; // The the indentation of the current line is greater than previous line,
  // then the line cannot be furter indented.

  if (formatsAtLineIndex.length > formatsAtPreviousLineIndex.length) {
    return value;
  }

  var newFormats = formats.slice();
  var targetLevelLineIndex = getTargetLevelLineIndex(value, lineIndex);

  for (var index = lineIndex; index < end; index++) {
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    } // Get the previous list, and if there's a child list, take over the
    // formats. If not, duplicate the last level and create a new level.


    if (targetLevelLineIndex) {
      var targetFormats = formats[targetLevelLineIndex] || [];
      newFormats[index] = targetFormats.concat((newFormats[index] || []).slice(targetFormats.length - 1));
    } else {
      var _targetFormats = formats[previousLineIndex] || [];

      var lastformat = _targetFormats[_targetFormats.length - 1] || rootFormat;
      newFormats[index] = _targetFormats.concat([lastformat], (newFormats[index] || []).slice(_targetFormats.length));
    }
  }

  return normaliseFormats({
    text: text,
    formats: newFormats,
    start: start,
    end: end
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
      formats = _ref.formats;
  var startFormats = formats[lineIndex] || [];
  var index = lineIndex;

  while (index-- >= 0) {
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    }

    var formatsAtIndex = formats[index] || [];

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
      formats = _ref.formats;
  var lineFormats = formats[lineIndex] || []; // Use the given line index in case there are no next children.

  var childIndex = lineIndex; // `lineIndex` could be `undefined` if it's the first line.

  for (var index = lineIndex || 0; index < text.length; index++) {
    // We're only interested in line indices.
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    }

    var formatsAtIndex = formats[index] || []; // If the amout of formats is equal or more, store it, then return the
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
  var text = value.text,
      formats = value.formats,
      start = value.start,
      end = value.end;
  var startingLineIndex = getLineIndex(value, start); // Return early if the starting line index cannot be further outdented.

  if (formats[startingLineIndex] === undefined) {
    return value;
  }

  var newFormats = formats.slice(0);
  var parentFormats = formats[getParentLineIndex(value, startingLineIndex)] || [];
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

  return normaliseFormats({
    text: text,
    formats: newFormats,
    start: start,
    end: end
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/change-list-type.js
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
      formats = value.formats,
      start = value.start,
      end = value.end;
  var startingLineIndex = getLineIndex(value, start);
  var startLineFormats = formats[startingLineIndex] || [];
  var endLineFormats = formats[getLineIndex(value, end)] || [];
  var startIndex = getParentLineIndex(value, startingLineIndex);
  var newFormats = formats.slice(0);
  var startCount = startLineFormats.length - 1;
  var endCount = endLineFormats.length - 1;
  var changed;

  for (var index = startIndex + 1 || 0; index < text.length; index++) {
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    }

    if ((newFormats[index] || []).length <= startCount) {
      break;
    }

    if (!newFormats[index]) {
      continue;
    }

    changed = true;
    newFormats[index] = newFormats[index].map(function (format, i) {
      return i < startCount || i > endCount ? format : newFormat;
    });
  }

  if (!changed) {
    return value;
  }

  return normaliseFormats({
    text: text,
    formats: newFormats,
    start: start,
    end: end
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/index.js































/***/ })

/******/ });