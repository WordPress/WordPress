<<<<<<< HEAD
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  __UNSTABLE_LINE_SEPARATOR: () => (/* reexport */ LINE_SEPARATOR),
  __experimentalRichText: () => (/* reexport */ __experimentalRichText),
  __unstableCreateElement: () => (/* reexport */ createElement),
  __unstableFormatEdit: () => (/* reexport */ FormatEdit),
  __unstableInsertLineSeparator: () => (/* reexport */ insertLineSeparator),
  __unstableIsEmptyLine: () => (/* reexport */ isEmptyLine),
  __unstableToDom: () => (/* reexport */ toDom),
  __unstableUseRichText: () => (/* reexport */ useRichText),
  applyFormat: () => (/* reexport */ applyFormat),
  concat: () => (/* reexport */ concat),
  create: () => (/* reexport */ create),
  getActiveFormat: () => (/* reexport */ getActiveFormat),
  getActiveFormats: () => (/* reexport */ getActiveFormats),
  getActiveObject: () => (/* reexport */ getActiveObject),
  getTextContent: () => (/* reexport */ getTextContent),
  insert: () => (/* reexport */ insert),
  insertObject: () => (/* reexport */ insertObject),
  isCollapsed: () => (/* reexport */ isCollapsed),
  isEmpty: () => (/* reexport */ isEmpty),
  join: () => (/* reexport */ join),
  registerFormatType: () => (/* reexport */ registerFormatType),
  remove: () => (/* reexport */ remove),
  removeFormat: () => (/* reexport */ removeFormat),
  replace: () => (/* reexport */ replace_replace),
  slice: () => (/* reexport */ slice),
  split: () => (/* reexport */ split),
  store: () => (/* reexport */ store),
  toHTMLString: () => (/* reexport */ toHTMLString),
  toggleFormat: () => (/* reexport */ toggleFormat),
  unregisterFormatType: () => (/* reexport */ unregisterFormatType),
  useAnchor: () => (/* reexport */ useAnchor),
  useAnchorRef: () => (/* reexport */ useAnchorRef)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/rich-text/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  getFormatType: () => (getFormatType),
  getFormatTypeForBareElement: () => (getFormatTypeForBareElement),
  getFormatTypeForClassName: () => (getFormatTypeForClassName),
  getFormatTypes: () => (getFormatTypes)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/rich-text/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  addFormatTypes: () => (addFormatTypes),
  removeFormatTypes: () => (removeFormatTypes)
});

;// CONCATENATED MODULE: external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/reducer.js
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

function formatTypes(state = {}, action) {
  switch (action.type) {
    case 'ADD_FORMAT_TYPES':
      return { ...state,
        // Key format types by their name.
        ...action.formatTypes.reduce((newFormatTypes, type) => ({ ...newFormatTypes,
          [type.name]: type
        }), {})
      };

    case 'REMOVE_FORMAT_TYPES':
      return Object.fromEntries(Object.entries(state).filter(([key]) => !action.names.includes(key)));
  }

  return state;
}
/* harmony default export */ const reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  formatTypes
}));

;// CONCATENATED MODULE: ./node_modules/rememo/rememo.js


/** @typedef {(...args: any[]) => *[]} GetDependants */

/** @typedef {() => void} Clear */

/**
 * @typedef {{
 *   getDependants: GetDependants,
 *   clear: Clear
 * }} EnhancedSelector
 */

/**
 * Internal cache entry.
 *
 * @typedef CacheNode
 *
 * @property {?CacheNode|undefined} [prev] Previous node.
 * @property {?CacheNode|undefined} [next] Next node.
 * @property {*[]} args Function arguments for cache entry.
 * @property {*} val Function result.
 */

/**
 * @typedef Cache
 *
 * @property {Clear} clear Function to clear cache.
 * @property {boolean} [isUniqueByDependants] Whether dependants are valid in
 * considering cache uniqueness. A cache is unique if dependents are all arrays
 * or objects.
 * @property {CacheNode?} [head] Cache head.
 * @property {*[]} [lastDependants] Dependants from previous invocation.
 */

/**
 * Arbitrary value used as key for referencing cache object in WeakMap tree.
 *
 * @type {{}}
 */
var LEAF_KEY = {};
=======
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
  if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
}

/***/ }),

/***/ "4eJC":
/***/ (function(module, exports, __webpack_require__) {

/**
 * Memize options object.
 *
 * @typedef MemizeOptions
 *
 * @property {number} [maxSize] Maximum size of the cache.
 */

/**
 * Internal cache entry.
 *
 * @typedef MemizeCacheNode
 *
 * @property {?MemizeCacheNode|undefined} [prev] Previous node.
 * @property {?MemizeCacheNode|undefined} [next] Next node.
 * @property {Array<*>}                   args   Function arguments for cache
 *                                               entry.
 * @property {*}                          val    Function result.
 */

/**
 * Properties of the enhanced function for controlling cache.
 *
 * @typedef MemizeMemoizedFunction
 *
 * @property {()=>void} clear Clear the cache.
 */

/**
 * Accepts a function to be memoized, and returns a new memoized function, with
 * optional options.
 *
 * @template {Function} F
 *
 * @param {F}             fn        Function to memoize.
 * @param {MemizeOptions} [options] Options object.
 *
 * @return {F & MemizeMemoizedFunction} Memoized function.
 */
function memize( fn, options ) {
	var size = 0;

	/** @type {?MemizeCacheNode|undefined} */
	var head;

	/** @type {?MemizeCacheNode|undefined} */
	var tail;

	options = options || {};

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
				/** @type {MemizeCacheNode} */ ( node.prev ).next = node.next;
				if ( node.next ) {
					node.next.prev = node.prev;
				}

				node.next = head;
				node.prev = null;
				/** @type {MemizeCacheNode} */ ( head ).prev = node;
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
			val: fn.apply( null, args ),
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
		if ( size === /** @type {MemizeOptions} */ ( options ).maxSize ) {
			tail = /** @type {MemizeCacheNode} */ ( tail ).prev;
			/** @type {MemizeCacheNode} */ ( tail ).next = null;
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

	// Ignore reason: There's not a clear solution to create an intersection of
	// the function with additional properties, where the goal is to retain the
	// function signature of the incoming argument and add control properties
	// on the return value.

	// @ts-ignore
	return memoized;
}

module.exports = memize;


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

/***/ "U8pU":
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

/***/ "Vx3V":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["escapeHtml"]; }());

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * Returns the first argument as the sole entry in an array.
 *
<<<<<<< HEAD
 * @template T
 *
 * @param {T} value Value to return.
 *
 * @return {[T]} Value returned as entry in array.
 */
function arrayOf(value) {
	return [value];
=======
 * @param {*} value Value to return.
 *
 * @return {Array} Value returned as entry in array.
 */
function arrayOf( value ) {
	return [ value ];
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
<<<<<<< HEAD
function isObjectLike(value) {
	return !!value && 'object' === typeof value;
=======
function isObjectLike( value ) {
	return !! value && 'object' === typeof value;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
}

/**
 * Creates and returns a new cache object.
 *
<<<<<<< HEAD
 * @return {Cache} Cache object.
 */
function createCache() {
	/** @type {Cache} */
	var cache = {
		clear: function () {
=======
 * @return {Object} Cache object.
 */
function createCache() {
	var cache = {
		clear: function() {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
<<<<<<< HEAD
 * @param {*[]} a First array.
 * @param {*[]} b Second array.
=======
 * @param {Array}  a         First array.
 * @param {Array}  b         Second array.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
<<<<<<< HEAD
function isShallowEqual(a, b, fromIndex) {
	var i;

	if (a.length !== b.length) {
		return false;
	}

	for (i = fromIndex; i < a.length; i++) {
		if (a[i] !== b[i]) {
=======
function isShallowEqual( a, b, fromIndex ) {
	var i;

	if ( a.length !== b.length ) {
		return false;
	}

	for ( i = fromIndex; i < a.length; i++ ) {
		if ( a[ i ] !== b[ i ] ) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
 * @template {(...args: *[]) => *} S
 *
 * @param {S} selector Selector function.
 * @param {GetDependants=} getDependants Dependant getter returning an array of
 * references used in cache bust consideration.
 */
/* harmony default export */ function rememo(selector, getDependants) {
	/** @type {WeakMap<*,*>} */
	var rootCache;

	/** @type {GetDependants} */
	var normalizedGetDependants = getDependants ? getDependants : arrayOf;
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

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
<<<<<<< HEAD
	 * @param {*[]} dependants Selector dependants.
	 *
	 * @return {Cache} Cache object.
	 */
	function getCache(dependants) {
		var caches = rootCache,
			isUniqueByDependants = true,
			i,
			dependant,
			map,
			cache;

		for (i = 0; i < dependants.length; i++) {
			dependant = dependants[i];

			// Can only compose WeakMap from object-like key.
			if (!isObjectLike(dependant)) {
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
<<<<<<< HEAD
			if (caches.has(dependant)) {
				// Traverse into nested WeakMap.
				caches = caches.get(dependant);
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set(dependant, map);
=======
			if ( caches.has( dependant ) ) {
				// Traverse into nested WeakMap.
				caches = caches.get( dependant );
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set( dependant, map );
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
<<<<<<< HEAD
		if (!caches.has(LEAF_KEY)) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set(LEAF_KEY, cache);
		}

		return caches.get(LEAF_KEY);
	}

=======
		if ( ! caches.has( LEAF_KEY ) ) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set( LEAF_KEY, cache );
		}

		return caches.get( LEAF_KEY );
	}

	// Assign cache handler by availability of WeakMap
	getCache = hasWeakMap ? getWeakMapCache : getRootCache;

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	/**
	 * Resets root memoization cache.
	 */
	function clear() {
<<<<<<< HEAD
		rootCache = new WeakMap();
	}

	/* eslint-disable jsdoc/check-param-names */
=======
		rootCache = hasWeakMap ? new WeakMap() : createCache();
	}

	// eslint-disable-next-line jsdoc/check-param-names
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
<<<<<<< HEAD
	 * @param {*}    source    Source object for derivation.
	 * @param {...*} extraArgs Additional arguments to pass to selector.
	 *
	 * @return {*} Selector result.
	 */
	/* eslint-enable jsdoc/check-param-names */
	function callSelector(/* source, ...extraArgs */) {
		var len = arguments.length,
			cache,
			node,
			i,
			args,
			dependants;

		// Create copy of arguments (avoid leaking deoptimization).
		args = new Array(len);
		for (i = 0; i < len; i++) {
			args[i] = arguments[i];
		}

		dependants = normalizedGetDependants.apply(null, args);
		cache = getCache(dependants);

		// If not guaranteed uniqueness by dependants (primitive type), shallow
		// compare against last dependants and, if references have changed,
		// destroy cache to recalculate result.
		if (!cache.isUniqueByDependants) {
			if (
				cache.lastDependants &&
				!isShallowEqual(dependants, cache.lastDependants, 0)
			) {
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
<<<<<<< HEAD
		while (node) {
			// Check whether node arguments match arguments
			if (!isShallowEqual(node.args, args, 1)) {
=======
		while ( node ) {
			// Check whether node arguments match arguments
			if ( ! isShallowEqual( node.args, args, 1 ) ) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
<<<<<<< HEAD
			if (node !== cache.head) {
				// Adjust siblings to point to each other.
				/** @type {CacheNode} */ (node.prev).next = node.next;
				if (node.next) {
=======
			if ( node !== cache.head ) {
				// Adjust siblings to point to each other.
				node.prev.next = node.next;
				if ( node.next ) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
<<<<<<< HEAD
				/** @type {CacheNode} */ (cache.head).prev = node;
=======
				cache.head.prev = node;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

<<<<<<< HEAD
		node = /** @type {CacheNode} */ ({
			// Generate the result from original function
			val: selector.apply(null, args),
		});

		// Avoid including the source object in the cache.
		args[0] = null;
=======
		node = {
			// Generate the result from original function
			val: selector.apply( null, args ),
		};

		// Avoid including the source object in the cache.
		args[ 0 ] = null;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
<<<<<<< HEAD
		if (cache.head) {
=======
		if ( cache.head ) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

<<<<<<< HEAD
	callSelector.getDependants = normalizedGetDependants;
	callSelector.clear = clear;
	clear();

	return /** @type {S & EnhancedSelector} */ (callSelector);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/selectors.js
=======
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
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("rePB");

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? Object(arguments[i]) : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(target, key, source[key]);
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * External dependencies
 */

<<<<<<< HEAD
=======

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Returns all the available format types.
 *
 * @param {Object} state Data state.
 *
 * @return {Array} Format types.
 */

<<<<<<< HEAD
const getFormatTypes = rememo(state => Object.values(state.formatTypes), state => [state.formatTypes]);
=======
var getFormatTypes = Object(rememo["a" /* default */])(function (state) {
  return Object.values(state.formatTypes);
}, function (state) {
  return [state.formatTypes];
});
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Returns a format type by name.
 *
 * @param {Object} state Data state.
<<<<<<< HEAD
 * @param {string} name  Format type name.
=======
 * @param {string} name Format type name.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
  const formatTypes = getFormatTypes(state);
  return formatTypes.find(({
    className,
    tagName
  }) => {
    return className === null && bareElementTagName === tagName;
  }) || formatTypes.find(({
    className,
    tagName
  }) => {
    return className === null && '*' === tagName;
=======
  return Object(external_lodash_["find"])(getFormatTypes(state), function (_ref) {
    var tagName = _ref.tagName;
    return bareElementTagName === tagName;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
  return getFormatTypes(state).find(({
    className
  }) => {
=======
  return Object(external_lodash_["find"])(getFormatTypes(state), function (_ref2) {
    var className = _ref2.className;

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    if (className === null) {
      return false;
    }

<<<<<<< HEAD
    return ` ${elementClassName} `.indexOf(` ${className} `) >= 0;
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/actions.js
=======
    return " ".concat(elementClassName, " ").indexOf(" ".concat(className, " ")) >= 0;
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/actions.js
/**
 * External dependencies
 */

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Returns an action object used in signalling that format types have been
 * added.
 *
 * @param {Array|Object} formatTypes Format types received.
 *
 * @return {Object} Action object.
 */
<<<<<<< HEAD
function addFormatTypes(formatTypes) {
  return {
    type: 'ADD_FORMAT_TYPES',
    formatTypes: Array.isArray(formatTypes) ? formatTypes : [formatTypes]
=======

function addFormatTypes(formatTypes) {
  return {
    type: 'ADD_FORMAT_TYPES',
    formatTypes: Object(external_lodash_["castArray"])(formatTypes)
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
    names: Array.isArray(names) ? names : [names]
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/index.js
=======
    names: Object(external_lodash_["castArray"])(names)
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




<<<<<<< HEAD
const STORE_NAME = 'core/rich-text';
/**
 * Store definition for the rich-text namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
=======
Object(external_this_wp_data_["registerStore"])('core/rich-text', {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject
});
<<<<<<< HEAD
(0,external_wp_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-format-equal.js
/** @typedef {import('./types').RichTextFormat} RichTextFormat */

/**
 * Optimised equality check for format objects.
 *
 * @param {?RichTextFormat} format1 Format to compare.
 * @param {?RichTextFormat} format2 Format to compare.
=======

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-format-equal.js
/**
 * Optimised equality check for format objects.
 *
 * @param {?Object} format1 Format to compare.
 * @param {?Object} format2 Format to compare.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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

<<<<<<< HEAD
  const attributes1 = format1.attributes;
  const attributes2 = format2.attributes; // Both not defined.
=======
  var attributes1 = format1.attributes;
  var attributes2 = format2.attributes; // Both not defined.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (attributes1 === attributes2) {
    return true;
  } // Either not defined.


  if (!attributes1 || !attributes2) {
    return false;
  }

<<<<<<< HEAD
  const keys1 = Object.keys(attributes1);
  const keys2 = Object.keys(attributes2);
=======
  var keys1 = Object.keys(attributes1);
  var keys2 = Object.keys(attributes2);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (keys1.length !== keys2.length) {
    return false;
  }

<<<<<<< HEAD
  const length = keys1.length; // Optimise for speed.

  for (let i = 0; i < length; i++) {
    const name = keys1[i];
=======
  var length = keys1.length; // Optimise for speed.

  for (var i = 0; i < length; i++) {
    var name = keys1[i];
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

    if (attributes1[name] !== attributes2[name]) {
      return false;
    }
  }

  return true;
}

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/normalise-formats.js
=======
// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/normalise-formats.js
/**
 * External dependencies
 */

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */

<<<<<<< HEAD
/** @typedef {import('./types').RichTextValue} RichTextValue */

/**
 * Normalises formats: ensures subsequent adjacent equal formats have the same
 * reference.
 *
 * @param {RichTextValue} value Value to normalise formats of.
 *
 * @return {RichTextValue} New value with normalised formats.
 */

function normaliseFormats(value) {
  const newFormats = value.formats.slice();
  newFormats.forEach((formatsAtIndex, index) => {
    const formatsAtPreviousIndex = newFormats[index - 1];

    if (formatsAtPreviousIndex) {
      const newFormatsAtIndex = formatsAtIndex.slice();
      newFormatsAtIndex.forEach((format, formatIndex) => {
        const previousFormat = formatsAtPreviousIndex[formatIndex];

        if (isFormatEqual(format, previousFormat)) {
          newFormatsAtIndex[formatIndex] = previousFormat;
        }
      });
      newFormats[index] = newFormatsAtIndex;
    }
  });
  return { ...value,
    formats: newFormats
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/apply-format.js
/**
 * Internal dependencies
 */

/** @typedef {import('./types').RichTextValue} RichTextValue */

/** @typedef {import('./types').RichTextFormat} RichTextFormat */

function replace(array, index, value) {
  array = array.slice();
  array[index] = value;
  return array;
}
=======

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


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Apply a format object to a Rich Text value from the given `startIndex` to the
 * given `endIndex`. Indices are retrieved from the selection if none are
 * provided.
 *
<<<<<<< HEAD
 * @param {RichTextValue}  value        Value to modify.
 * @param {RichTextFormat} format       Format to apply.
 * @param {number}         [startIndex] Start index.
 * @param {number}         [endIndex]   End index.
 *
 * @return {RichTextValue} A new value with the format applied.
 */


function applyFormat(value, format, startIndex = value.start, endIndex = value.end) {
  const {
    formats,
    activeFormats
  } = value;
  const newFormats = formats.slice(); // The selection is collapsed.

  if (startIndex === endIndex) {
    const startFormat = newFormats[startIndex]?.find(({
      type
    }) => type === format.type); // If the caret is at a format of the same type, expand start and end to
    // the edges of the format. This is useful to apply new attributes.

    if (startFormat) {
      const index = newFormats[startIndex].indexOf(startFormat);

      while (newFormats[startIndex] && newFormats[startIndex][index] === startFormat) {
        newFormats[startIndex] = replace(newFormats[startIndex], index, format);
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
        startIndex--;
      }

      endIndex++;

<<<<<<< HEAD
      while (newFormats[endIndex] && newFormats[endIndex][index] === startFormat) {
        newFormats[endIndex] = replace(newFormats[endIndex], index, format);
        endIndex++;
      }
    }
  } else {
    // Determine the highest position the new format can be inserted at.
    let position = +Infinity;

    for (let index = startIndex; index < endIndex; index++) {
      if (newFormats[index]) {
        newFormats[index] = newFormats[index].filter(({
          type
        }) => type !== format.type);
        const length = newFormats[index].length;

        if (length < position) {
          position = length;
        }
      } else {
        newFormats[index] = [];
        position = 0;
      }
    }

    for (let index = startIndex; index < endIndex; index++) {
      newFormats[index].splice(position, 0, format);
    }
  }

  return normaliseFormats({ ...value,
    formats: newFormats,
    // Always revise active formats. This serves as a placeholder for new
    // inputs with the format so new input appears with the format applied,
    // and ensures a format of the same type uses the latest values.
    activeFormats: [...(activeFormats?.filter(({
      type
    }) => type !== format.type) || []), format]
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/create-element.js
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
function createElement({
  implementation
}, html) {
=======
function createElement(_ref, html) {
  var implementation = _ref.implementation;

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/special-characters.js
/**
 * Line separator character, used for multiline text.
 */
const LINE_SEPARATOR = '\u2028';
/**
 * Object replacement character, used as a placeholder for objects.
 */

const OBJECT_REPLACEMENT_CHARACTER = '\ufffc';
/**
 * Zero width non-breaking space, used as padding in the editable DOM tree when
 * it is empty otherwise.
 */

const ZWNBSP = '\ufeff';

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/create.js
=======
// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/create.js



>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





<<<<<<< HEAD
/** @typedef {import('./types').RichTextValue} RichTextValue */
=======
/**
 * Browser dependencies
 */

var _window$Node = window.Node,
    TEXT_NODE = _window$Node.TEXT_NODE,
    ELEMENT_NODE = _window$Node.ELEMENT_NODE;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

function createEmptyValue() {
  return {
    formats: [],
    replacements: [],
    text: ''
  };
}

<<<<<<< HEAD
function toFormat({
  tagName,
  attributes
}) {
  let formatType;

  if (attributes && attributes.class) {
    formatType = (0,external_wp_data_namespaceObject.select)(store).getFormatTypeForClassName(attributes.class);

    if (formatType) {
      // Preserve any additional classes.
      attributes.class = ` ${attributes.class} `.replace(` ${formatType.className} `, ' ').trim();
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

      if (!attributes.class) {
        delete attributes.class;
      }
    }
  }

  if (!formatType) {
<<<<<<< HEAD
    formatType = (0,external_wp_data_namespaceObject.select)(store).getFormatTypeForBareElement(tagName);
=======
    formatType = Object(external_this_wp_data_["select"])('core/rich-text').getFormatTypeForBareElement(type);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  }

  if (!formatType) {
    return attributes ? {
<<<<<<< HEAD
      type: tagName,
      attributes
    } : {
      type: tagName
    };
  }

  if (formatType.__experimentalCreatePrepareEditableTree && !formatType.__experimentalCreateOnChangeEditableValue) {
    return null;
  }

  if (!attributes) {
    return {
      formatType,
      type: formatType.name,
      tagName
    };
  }

  const registeredAttributes = {};
  const unregisteredAttributes = {};
  const _attributes = { ...attributes
  };

  for (const key in formatType.attributes) {
    const name = formatType.attributes[key];
    registeredAttributes[key] = _attributes[name];

    if (formatType.__unstableFilterAttributeValue) {
      registeredAttributes[key] = formatType.__unstableFilterAttributeValue(key, registeredAttributes[key]);
    } // delete the attribute and what's left is considered
    // to be unregistered.


    delete _attributes[name];

    if (typeof registeredAttributes[key] === 'undefined') {
      delete registeredAttributes[key];
    }
  }

  for (const name in _attributes) {
    unregisteredAttributes[name] = attributes[name];
  }

  if (formatType.contentEditable === false) {
    delete unregisteredAttributes.contenteditable;
  }

  return {
    formatType,
    type: formatType.name,
    tagName,
    attributes: registeredAttributes,
    unregisteredAttributes
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
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
 * @param {Object}  [$1]                          Optional named arguments.
 * @param {Element} [$1.element]                  Element to create value from.
 * @param {string}  [$1.text]                     Text to create value from.
 * @param {string}  [$1.html]                     HTML to create value from.
 * @param {Range}   [$1.range]                    Range to create value from.
 * @param {string}  [$1.multilineTag]             Multiline tag if the structure is
 *                                                multiline.
 * @param {Array}   [$1.multilineWrapperTags]     Tags where lines can be found if
 *                                                nesting is possible.
 * @param {boolean} [$1.preserveWhiteSpace]       Whether or not to collapse white
 *                                                space characters.
 * @param {boolean} [$1.__unstableIsEditableTree]
 *
 * @return {RichTextValue} A rich text value.
 */


function create({
  element,
  text,
  html,
  range,
  multilineTag,
  multilineWrapperTags,
  __unstableIsEditableTree: isEditableTree,
  preserveWhiteSpace
} = {}) {
=======
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

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  if (typeof text === 'string' && text.length > 0) {
    return {
      formats: Array(text.length),
      replacements: Array(text.length),
<<<<<<< HEAD
      text
=======
      text: text
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    };
  }

  if (typeof html === 'string' && html.length > 0) {
<<<<<<< HEAD
    // It does not matter which document this is, we're just using it to
    // parse.
    element = createElement(document, html);
  }

  if (typeof element !== 'object') {
=======
    element = createElement(document, html);
  }

  if (Object(esm_typeof["a" /* default */])(element) !== 'object') {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    return createEmptyValue();
  }

  if (!multilineTag) {
    return createFromElement({
<<<<<<< HEAD
      element,
      range,
      isEditableTree,
      preserveWhiteSpace
=======
      element: element,
      range: range,
      removeNode: removeNode,
      unwrapNode: unwrapNode,
      filterString: filterString,
      removeAttribute: removeAttribute
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    });
  }

  return createFromMultilineElement({
<<<<<<< HEAD
    element,
    range,
    multilineTag,
    multilineWrapperTags,
    isEditableTree,
    preserveWhiteSpace
=======
    element: element,
    range: range,
    multilineTag: multilineTag,
    multilineWrapperTags: multilineWrapperTags,
    removeNode: removeNode,
    unwrapNode: unwrapNode,
    filterString: filterString,
    removeAttribute: removeAttribute
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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

<<<<<<< HEAD
  const {
    parentNode
  } = node;
  const {
    startContainer,
    startOffset,
    endContainer,
    endOffset
  } = range;
  const currentLength = accumulator.text.length; // Selection can be extracted from value.

  if (value.start !== undefined) {
    accumulator.start = currentLength + value.start; // Range indicates that the current node has selection.
  } else if (node === startContainer && node.nodeType === node.TEXT_NODE) {
=======
  var parentNode = node.parentNode;
  var startContainer = range.startContainer,
      startOffset = range.startOffset,
      endContainer = range.endContainer,
      endOffset = range.endOffset;
  var currentLength = accumulator.text.length; // Selection can be extracted from value.

  if (value.start !== undefined) {
    accumulator.start = currentLength + value.start; // Range indicates that the current node has selection.
  } else if (node === startContainer && node.nodeType === TEXT_NODE) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
  } else if (node === endContainer && node.nodeType === node.TEXT_NODE) {
=======
  } else if (node === endContainer && node.nodeType === TEXT_NODE) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
 * @return {Object|void} Object containing range properties.
=======
 * @return {?Object} Object containing range properties.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */


function filterRange(node, range, filter) {
  if (!range) {
    return;
  }

<<<<<<< HEAD
  const {
    startContainer,
    endContainer
  } = range;
  let {
    startOffset,
    endOffset
  } = range;
=======
  var startContainer = range.startContainer,
      endContainer = range.endContainer;
  var startOffset = range.startOffset,
      endOffset = range.endOffset;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (node === startContainer) {
    startOffset = filter(node.nodeValue.slice(0, startOffset)).length;
  }

  if (node === endContainer) {
    endOffset = filter(node.nodeValue.slice(0, endOffset)).length;
  }

  return {
<<<<<<< HEAD
    startContainer,
    startOffset,
    endContainer,
    endOffset
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
/**
 * Removes reserved characters used by rich-text (zero width non breaking spaces added by `toTree` and object replacement characters).
 *
 * @param {string} string
 */


function removeReservedCharacters(string) {
  // with the global flag, note that we should create a new regex each time OR reset lastIndex state.
  return string.replace(new RegExp(`[${ZWNBSP}${OBJECT_REPLACEMENT_CHARACTER}]`, 'gu'), '');
}
/**
 * Creates a Rich Text value from a DOM element and range.
 *
 * @param {Object}  $1                        Named argements.
 * @param {Element} [$1.element]              Element to create value from.
 * @param {Range}   [$1.range]                Range to create value from.
 * @param {string}  [$1.multilineTag]         Multiline tag if the structure is
 *                                            multiline.
 * @param {Array}   [$1.multilineWrapperTags] Tags where lines can be found if
 *                                            nesting is possible.
 * @param {boolean} [$1.preserveWhiteSpace]   Whether or not to collapse white
 *                                            space characters.
 * @param {Array}   [$1.currentWrapperTags]
 * @param {boolean} [$1.isEditableTree]
 *
 * @return {RichTextValue} A rich text value.
 */

function createFromElement({
  element,
  range,
  multilineTag,
  multilineWrapperTags,
  currentWrapperTags = [],
  isEditableTree,
  preserveWhiteSpace
}) {
  const accumulator = createEmptyValue();
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (!element) {
    return accumulator;
  }

  if (!element.hasChildNodes()) {
    accumulateSelection(accumulator, element, range, createEmptyValue());
    return accumulator;
  }

<<<<<<< HEAD
  const length = element.childNodes.length; // Optimise for speed.

  for (let index = 0; index < length; index++) {
    const node = element.childNodes[index];
    const tagName = node.nodeName.toLowerCase();

    if (node.nodeType === node.TEXT_NODE) {
      let filter = removeReservedCharacters;

      if (!preserveWhiteSpace) {
        filter = string => removeReservedCharacters(collapseWhiteSpace(string));
      }

      const text = filter(node.nodeValue);
      range = filterRange(node, range, filter);
      accumulateSelection(accumulator, node, range, {
        text
      }); // Create a sparse array of the same length as `text`, in which
      // formats can be added.

      accumulator.formats.length += text.length;
      accumulator.replacements.length += text.length;
      accumulator.text += text;
      continue;
    }

    if (node.nodeType !== node.ELEMENT_NODE) {
      continue;
    }

    if (isEditableTree && ( // Ignore any placeholders.
    node.getAttribute('data-rich-text-placeholder') || // Ignore any line breaks that are not inserted by us.
    tagName === 'br' && !node.getAttribute('data-rich-text-line-break'))) {
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      accumulateSelection(accumulator, node, range, createEmptyValue());
      continue;
    }

<<<<<<< HEAD
    if (tagName === 'script') {
      const value = {
        formats: [,],
        replacements: [{
          type: tagName,
=======
    if (type === 'script') {
      var _value = {
        formats: [,],
        replacements: [{
          type: type,
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
          attributes: {
            'data-rich-text-script': node.getAttribute('data-rich-text-script') || encodeURIComponent(node.innerHTML)
          }
        }],
        text: OBJECT_REPLACEMENT_CHARACTER
      };
<<<<<<< HEAD
      accumulateSelection(accumulator, node, range, value);
      mergePair(accumulator, value);
      continue;
    }

    if (tagName === 'br') {
      accumulateSelection(accumulator, node, range, createEmptyValue());
      mergePair(accumulator, create({
        text: '\n'
      }));
      continue;
    }

    const format = toFormat({
      tagName,
      attributes: getAttributes({
        element: node
      })
    }); // When a format type is declared as not editable, replace it with an
    // object replacement character and preserve the inner HTML.

    if (format?.formatType?.contentEditable === false) {
      delete format.formatType;
      accumulateSelection(accumulator, node, range, createEmptyValue());
      mergePair(accumulator, {
        formats: [,],
        replacements: [{ ...format,
          innerHTML: node.innerHTML
        }],
        text: OBJECT_REPLACEMENT_CHARACTER
      });
      continue;
    }

    if (format) delete format.formatType;

    if (multilineWrapperTags && multilineWrapperTags.indexOf(tagName) !== -1) {
      const value = createFromMultilineElement({
        element: node,
        range,
        multilineTag,
        multilineWrapperTags,
        currentWrapperTags: [...currentWrapperTags, format],
        isEditableTree,
        preserveWhiteSpace
      });
      accumulateSelection(accumulator, node, range, value);
      mergePair(accumulator, value);
      continue;
    }

    const value = createFromElement({
      element: node,
      range,
      multilineTag,
      multilineWrapperTags,
      isEditableTree,
      preserveWhiteSpace
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

        const newFormats = formats ? [format, ...formats] : [format];
        mergeFormats.formats = formats;
        mergeFormats.newFormats = newFormats;
        return newFormats;
      } // Since the formats parameter can be `undefined`, preset
      // `mergeFormats` with a new reference.


      mergeFormats.newFormats = [format];
      mergePair(accumulator, { ...value,
        formats: Array.from(value.formats, mergeFormats)
      });
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    }
  }

  return accumulator;
}
/**
 * Creates a rich text value from a DOM element and range that should be
 * multiline.
 *
<<<<<<< HEAD
 * @param {Object}  $1                        Named argements.
 * @param {Element} [$1.element]              Element to create value from.
 * @param {Range}   [$1.range]                Range to create value from.
 * @param {string}  [$1.multilineTag]         Multiline tag if the structure is
 *                                            multiline.
 * @param {Array}   [$1.multilineWrapperTags] Tags where lines can be found if
 *                                            nesting is possible.
 * @param {Array}   [$1.currentWrapperTags]   Whether to prepend a line
 *                                            separator.
 * @param {boolean} [$1.preserveWhiteSpace]   Whether or not to collapse white
 *                                            space characters.
 * @param {boolean} [$1.isEditableTree]
 *
 * @return {RichTextValue} A rich text value.
 */


function createFromMultilineElement({
  element,
  range,
  multilineTag,
  multilineWrapperTags,
  currentWrapperTags = [],
  isEditableTree,
  preserveWhiteSpace
}) {
  const accumulator = createEmptyValue();
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (!element || !element.hasChildNodes()) {
    return accumulator;
  }

<<<<<<< HEAD
  const length = element.children.length; // Optimise for speed.

  for (let index = 0; index < length; index++) {
    const node = element.children[index];
=======
  var length = element.children.length; // Optimise for speed.

  for (var index = 0; index < length; index++) {
    var node = element.children[index];
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

    if (node.nodeName.toLowerCase() !== multilineTag) {
      continue;
    }

<<<<<<< HEAD
    const value = createFromElement({
      element: node,
      range,
      multilineTag,
      multilineWrapperTags,
      currentWrapperTags,
      isEditableTree,
      preserveWhiteSpace
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
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  }

  return accumulator;
}
/**
 * Gets the attributes of an element in object shape.
 *
<<<<<<< HEAD
 * @param {Object}  $1         Named argements.
 * @param {Element} $1.element Element to get attributes from.
 *
 * @return {Object|void} Attribute object or `undefined` if the element has no
 *                       attributes.
 */


function getAttributes({
  element
}) {
=======
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

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  if (!element.hasAttributes()) {
    return;
  }

<<<<<<< HEAD
  const length = element.attributes.length;
  let accumulator; // Optimise for speed.

  for (let i = 0; i < length; i++) {
    const {
      name,
      value
    } = element.attributes[i];

    if (name.indexOf('data-rich-text-') === 0) {
      continue;
    }

    const safeName = /^on/i.test(name) ? 'data-disable-rich-text-' + name : name;
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    accumulator = accumulator || {};
    accumulator[safeName] = value;
  }

  return accumulator;
}

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/concat.js
/**
 * Internal dependencies
 */


/** @typedef {import('./types').RichTextValue} RichTextValue */

/**
 * Concats a pair of rich text values. Not that this mutates `a` and does NOT
 * normalise formats!
 *
 * @param {Object} a Value to mutate.
 * @param {Object} b Value to add read from.
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
 * @param {...RichTextValue} values Objects to combine.
 *
 * @return {RichTextValue} A new value combining all given records.
 */

function concat(...values) {
  return normaliseFormats(values.reduce(mergePair, create()));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-active-formats.js
/** @typedef {import('./types').RichTextValue} RichTextValue */

/** @typedef {import('./types').RichTextFormatList} RichTextFormatList */

/**
 * Internal dependencies
 */

/**
 * Gets the all format objects at the start of the selection.
 *
 * @param {RichTextValue} value                Value to inspect.
 * @param {Array}         EMPTY_ACTIVE_FORMATS Array to return if there are no
 *                                             active formats.
 *
 * @return {RichTextFormatList} Active format objects.
 */

function getActiveFormats(value, EMPTY_ACTIVE_FORMATS = []) {
  const {
    formats,
    start,
    end,
    activeFormats
  } = value;

  if (start === undefined) {
    return EMPTY_ACTIVE_FORMATS;
  }

  if (start === end) {
    // For a collapsed caret, it is possible to override the active formats.
    if (activeFormats) {
      return activeFormats;
    }

    const formatsBefore = formats[start - 1] || EMPTY_ACTIVE_FORMATS;
    const formatsAfter = formats[start] || EMPTY_ACTIVE_FORMATS; // By default, select the lowest amount of formats possible (which means
    // the caret is positioned outside the format boundary). The user can
    // then use arrow keys to define `activeFormats`.

    if (formatsBefore.length < formatsAfter.length) {
      return formatsBefore;
    }

    return formatsAfter;
  } // If there's no formats at the start index, there are not active formats.


  if (!formats[start]) {
    return EMPTY_ACTIVE_FORMATS;
  }

  const selectedFormats = formats.slice(start, end); // Clone the formats so we're not mutating the live value.

  const _activeFormats = [...selectedFormats[0]];
  let i = selectedFormats.length; // For performance reasons, start from the end where it's much quicker to
  // realise that there are no active formats.

  while (i--) {
    const formatsAtIndex = selectedFormats[i]; // If we run into any index without formats, we're sure that there's no
    // active formats.

    if (!formatsAtIndex) {
      return EMPTY_ACTIVE_FORMATS;
    }

    let ii = _activeFormats.length; // Loop over the active formats and remove any that are not present at
    // the current index.

    while (ii--) {
      const format = _activeFormats[ii];

      if (!formatsAtIndex.find(_format => isFormatEqual(format, _format))) {
        _activeFormats.splice(ii, 1);
      }
    } // If there are no active formats, we can stop.


    if (_activeFormats.length === 0) {
      return EMPTY_ACTIVE_FORMATS;
    }
  }

  return _activeFormats || EMPTY_ACTIVE_FORMATS;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-active-format.js
/**
 * Internal dependencies
 */

/** @typedef {import('./types').RichTextValue} RichTextValue */

/** @typedef {import('./types').RichTextFormat} RichTextFormat */

/**
 * Gets the format object by type at the start of the selection. This can be
 * used to get e.g. the URL of a link format at the current selection, but also
 * to check if a format is active at the selection. Returns undefined if there
 * is no format at the selection.
 *
 * @param {RichTextValue} value      Value to inspect.
 * @param {string}        formatType Format type to look for.
 *
 * @return {RichTextFormat|undefined} Active format object of the specified
 *                                    type, or undefined.
 */

function getActiveFormat(value, formatType) {
  return getActiveFormats(value).find(({
    type
  }) => type === formatType);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-active-object.js
/**
 * Internal dependencies
 */

/** @typedef {import('./types').RichTextValue} RichTextValue */

/** @typedef {import('./types').RichTextFormat} RichTextFormat */

/**
 * Gets the active object, if there is any.
 *
 * @param {RichTextValue} value Value to inspect.
 *
 * @return {RichTextFormat|void} Active object, or undefined.
 */

function getActiveObject({
  start,
  end,
  replacements,
  text
}) {
  if (start + 1 !== end || text[start] !== OBJECT_REPLACEMENT_CHARACTER) {
    return;
  }

  return replacements[start];
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-text-content.js
/**
 * Internal dependencies
 */

/** @typedef {import('./types').RichTextValue} RichTextValue */

const pattern = new RegExp(`[${OBJECT_REPLACEMENT_CHARACTER}${LINE_SEPARATOR}]`, 'g');
/**
 * Get the textual content of a Rich Text value. This is similar to
 * `Element.textContent`.
 *
 * @param {RichTextValue} value Value to use.
 *
 * @return {string} The text content.
 */

function getTextContent({
  text
}) {
  return text.replace(pattern, c => c === OBJECT_REPLACEMENT_CHARACTER ? '' : '\n');
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-collapsed.js
/**
 * Internal dependencies
 */

/**
 * Check if the selection of a Rich Text value is collapsed or not. Collapsed
 * means that no characters are selected, but there is a caret present. If there
 * is no selection, `undefined` will be returned. This is similar to
 * `window.getSelection().isCollapsed()`.
 *
 * @param props       The rich text value to check.
 * @param props.start
 * @param props.end
 * @return True if the selection is collapsed, false if not, undefined if there is no selection.
 */
function isCollapsed({
  start,
  end
}) {
  if (start === undefined || end === undefined) {
    return;
  }

  return start === end;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-empty.js
/**
 * Internal dependencies
 */

/** @typedef {import('./types').RichTextValue} RichTextValue */

/**
 * Check if a Rich Text value is Empty, meaning it contains no text or any
 * objects (such as images).
 *
 * @param {RichTextValue} value Value to use.
 *
 * @return {boolean} True if the value is empty, false if not.
 */

function isEmpty({
  text
}) {
  return text.length === 0;
}
/**
 * Check if the current collapsed selection is on an empty line in case of a
 * multiline value.
 *
 * @param {RichTextValue} value Value te check.
 *
 * @return {boolean} True if the line is empty, false if not.
 */

function isEmptyLine({
  text,
  start,
  end
}) {
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

  return text.slice(start - 1, end + 1) === `${LINE_SEPARATOR}${LINE_SEPARATOR}`;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/join.js
/**
 * Internal dependencies
 */


/** @typedef {import('./types').RichTextValue} RichTextValue */

/**
 * Combine an array of Rich Text values into one, optionally separated by
 * `separator`, which can be a Rich Text value, HTML string, or plain text
 * string. This is similar to `Array.prototype.join`.
 *
 * @param {Array<RichTextValue>} values      An array of values to join.
 * @param {string|RichTextValue} [separator] Separator string or value.
 *
 * @return {RichTextValue} A new combined value.
 */

function join(values, separator = '') {
  if (typeof separator === 'string') {
    separator = create({
      text: separator
    });
  }

  return normaliseFormats(values.reduce((accumlator, {
    formats,
    replacements,
    text
  }) => ({
    formats: accumlator.formats.concat(separator.formats, formats),
    replacements: accumlator.replacements.concat(separator.replacements, replacements),
    text: accumlator.text + separator.text + text
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/register-format-type.js
=======
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


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

<<<<<<< HEAD
/**
 * Internal dependencies
 */


/**
 * @typedef {Object} WPFormat
 *
 * @property {string}   name        A string identifying the format. Must be
 *                                  unique across all registered formats.
 * @property {string}   tagName     The HTML tag this format will wrap the
 *                                  selection with.
 * @property {boolean}  interactive Whether format makes content interactive or not.
 * @property {string}   [className] A class to match the format.
 * @property {string}   title       Name of the format.
 * @property {Function} edit        Should return a component for the user to
 *                                  interact with the new registered format.
 */

=======



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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Registers a new format provided a unique name and an object defining its
 * behavior.
 *
<<<<<<< HEAD
 * @param {string}   name     Format name.
 * @param {WPFormat} settings Format settings.
 *
 * @return {WPFormat|undefined} The format, if it has been successfully
 *                              registered; otherwise `undefined`.
 */

function registerFormatType(name, settings) {
  settings = {
    name,
    ...settings
  };
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (typeof settings.name !== 'string') {
    window.console.error('Format names must be strings.');
    return;
  }

  if (!/^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/.test(settings.name)) {
    window.console.error('Format names must contain a namespace prefix, include only lowercase alphanumeric characters or dashes, and start with a letter. Example: my-plugin/my-custom-format');
    return;
  }

<<<<<<< HEAD
  if ((0,external_wp_data_namespaceObject.select)(store).getFormatType(settings.name)) {
=======
  if (Object(external_this_wp_data_["select"])('core/rich-text').getFormatType(settings.name)) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
    const formatTypeForBareElement = (0,external_wp_data_namespaceObject.select)(store).getFormatTypeForBareElement(settings.tagName);

    if (formatTypeForBareElement && formatTypeForBareElement.name !== 'core/unknown') {
      window.console.error(`Format "${formatTypeForBareElement.name}" is already registered to handle bare tag name "${settings.tagName}".`);
      return;
    }
  } else {
    const formatTypeForClassName = (0,external_wp_data_namespaceObject.select)(store).getFormatTypeForClassName(settings.className);

    if (formatTypeForClassName) {
      window.console.error(`Format "${formatTypeForClassName.name}" is already registered to handle class name "${settings.className}".`);
=======
    var formatTypeForBareElement = Object(external_this_wp_data_["select"])('core/rich-text').getFormatTypeForBareElement(settings.tagName);

    if (formatTypeForBareElement) {
      window.console.error("Format \"".concat(formatTypeForBareElement.name, "\" is already registered to handle bare tag name \"").concat(settings.tagName, "\"."));
      return;
    }
  } else {
    var formatTypeForClassName = Object(external_this_wp_data_["select"])('core/rich-text').getFormatTypeForClassName(settings.className);

    if (formatTypeForClassName) {
      window.console.error("Format \"".concat(formatTypeForClassName.name, "\" is already registered to handle class name \"").concat(settings.className, "\"."));
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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

<<<<<<< HEAD
  (0,external_wp_data_namespaceObject.dispatch)(store).addFormatTypes(settings);
  return settings;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/remove-format.js
=======
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

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */

<<<<<<< HEAD
/** @typedef {import('./types').RichTextValue} RichTextValue */
=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * Remove any format object from a Rich Text value by type from the given
 * `startIndex` to the given `endIndex`. Indices are retrieved from the
 * selection if none are provided.
 *
<<<<<<< HEAD
 * @param {RichTextValue} value        Value to modify.
 * @param {string}        formatType   Format type to remove.
 * @param {number}        [startIndex] Start index.
 * @param {number}        [endIndex]   End index.
 *
 * @return {RichTextValue} A new value with the format applied.
 */

function removeFormat(value, formatType, startIndex = value.start, endIndex = value.end) {
  const {
    formats,
    activeFormats
  } = value;
  const newFormats = formats.slice(); // If the selection is collapsed, expand start and end to the edges of the
  // format.

  if (startIndex === endIndex) {
    const format = newFormats[startIndex]?.find(({
      type
    }) => type === formatType);

    if (format) {
      while (newFormats[startIndex]?.find(newFormat => newFormat === format)) {
        filterFormats(newFormats, startIndex, formatType);
        startIndex--;
      }

      endIndex++;

      while (newFormats[endIndex]?.find(newFormat => newFormat === format)) {
        filterFormats(newFormats, endIndex, formatType);
        endIndex++;
      }
    }
  } else {
    for (let i = startIndex; i < endIndex; i++) {
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      if (newFormats[i]) {
        filterFormats(newFormats, i, formatType);
      }
    }
  }

<<<<<<< HEAD
  return normaliseFormats({ ...value,
    formats: newFormats,
    activeFormats: activeFormats?.filter(({
      type
    }) => type !== formatType) || []
=======
  return normaliseFormats({
    formats: newFormats,
    text: text,
    start: start,
    end: end,
    replacements: replacements
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  });
}

function filterFormats(formats, index, formatType) {
<<<<<<< HEAD
  const newFormats = formats[index].filter(({
    type
  }) => type !== formatType);
=======
  var newFormats = formats[index].filter(function (_ref2) {
    var type = _ref2.type;
    return type !== formatType;
  });
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (newFormats.length) {
    formats[index] = newFormats;
  } else {
    delete formats[index];
  }
}

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/insert.js
=======
// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/insert.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD
/** @typedef {import('./types').RichTextValue} RichTextValue */

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Insert a Rich Text value, an HTML string, or a plain text string, into a
 * Rich Text value at the given `startIndex`. Any content between `startIndex`
 * and `endIndex` will be removed. Indices are retrieved from the selection if
 * none are provided.
 *
<<<<<<< HEAD
 * @param {RichTextValue}        value         Value to modify.
 * @param {RichTextValue|string} valueToInsert Value to insert.
 * @param {number}               [startIndex]  Start index.
 * @param {number}               [endIndex]    End index.
 *
 * @return {RichTextValue} A new value with the value inserted.
 */

function insert(value, valueToInsert, startIndex = value.start, endIndex = value.end) {
  const {
    formats,
    replacements,
    text
  } = value;
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (typeof valueToInsert === 'string') {
    valueToInsert = create({
      text: valueToInsert
    });
  }

<<<<<<< HEAD
  const index = startIndex + valueToInsert.text.length;
  return normaliseFormats({
    formats: formats.slice(0, startIndex).concat(valueToInsert.formats, formats.slice(endIndex)),
    replacements: replacements.slice(0, startIndex).concat(valueToInsert.replacements, replacements.slice(endIndex)),
    text: text.slice(0, startIndex) + valueToInsert.text + text.slice(endIndex),
=======
  var index = startIndex + valueToInsert.text.length;
  return normaliseFormats({
    formats: formats.slice(0, startIndex).concat(valueToInsert.formats, formats.slice(endIndex)),
    text: text.slice(0, startIndex) + valueToInsert.text + text.slice(endIndex),
    replacements: replacements.slice(0, startIndex).concat(valueToInsert.replacements, replacements.slice(endIndex)),
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    start: index,
    end: index
  });
}

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/remove.js
=======
// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/remove.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD
/** @typedef {import('./types').RichTextValue} RichTextValue */

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Remove content from a Rich Text value between the given `startIndex` and
 * `endIndex`. Indices are retrieved from the selection if none are provided.
 *
<<<<<<< HEAD
 * @param {RichTextValue} value        Value to modify.
 * @param {number}        [startIndex] Start index.
 * @param {number}        [endIndex]   End index.
 *
 * @return {RichTextValue} A new value with the content removed.
 */

function remove(value, startIndex, endIndex) {
  return insert(value, create(), startIndex, endIndex);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/replace.js
=======
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


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */

<<<<<<< HEAD
/** @typedef {import('./types').RichTextValue} RichTextValue */

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Search a Rich Text value and replace the match(es) with `replacement`. This
 * is similar to `String.prototype.replace`.
 *
<<<<<<< HEAD
 * @param {RichTextValue}   value       The value to modify.
 * @param {RegExp|string}   pattern     A RegExp object or literal. Can also be
=======
 * @param {Object}         value        The value to modify.
 * @param {RegExp|string}  pattern      A RegExp object or literal. Can also be
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *                                      a string. It is treated as a verbatim
 *                                      string and is not interpreted as a
 *                                      regular expression. Only the first
 *                                      occurrence will be replaced.
 * @param {Function|string} replacement The match or matches are replaced with
 *                                      the specified or the value returned by
 *                                      the specified function.
 *
<<<<<<< HEAD
 * @return {RichTextValue} A new value with replacements applied.
 */

function replace_replace({
  formats,
  replacements,
  text,
  start,
  end
}, pattern, replacement) {
  text = text.replace(pattern, (match, ...rest) => {
    const offset = rest[rest.length - 2];
    let newText = replacement;
    let newFormats;
    let newReplacements;

    if (typeof newText === 'function') {
      newText = replacement(match, ...rest);
    }

    if (typeof newText === 'object') {
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
    formats,
    replacements,
    text,
    start,
    end
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/insert-line-separator.js
=======
    formats: formats,
    replacements: replacements,
    text: text,
    start: start,
    end: end
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/insert-line-separator.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD
/** @typedef {import('./types').RichTextValue} RichTextValue */
=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * Insert a line break character into a Rich Text value at the given
 * `startIndex`. Any content between `startIndex` and `endIndex` will be
 * removed. Indices are retrieved from the selection if none are provided.
 *
<<<<<<< HEAD
 * @param {RichTextValue} value        Value to modify.
 * @param {number}        [startIndex] Start index.
 * @param {number}        [endIndex]   End index.
 *
 * @return {RichTextValue} A new value with the value inserted.
 */

function insertLineSeparator(value, startIndex = value.start, endIndex = value.end) {
  const beforeText = value.text.slice(0, startIndex);
  const previousLineSeparatorIndex = beforeText.lastIndexOf(LINE_SEPARATOR);
  const previousLineSeparatorFormats = value.replacements[previousLineSeparatorIndex];
  let replacements = [,];

  if (previousLineSeparatorFormats) {
    replacements = [previousLineSeparatorFormats];
  }

  const valueToInsert = {
    formats: [,],
    replacements,
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    text: LINE_SEPARATOR
  };
  return insert(value, valueToInsert, startIndex, endIndex);
}

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/insert-object.js
=======
// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/insert-object.js


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */

<<<<<<< HEAD

/** @typedef {import('./types').RichTextValue} RichTextValue */

/** @typedef {import('./types').RichTextFormat} RichTextFormat */

=======
var insert_object_OBJECT_REPLACEMENT_CHARACTER = "\uFFFC";
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Insert a format as an object into a Rich Text value at the given
 * `startIndex`. Any content between `startIndex` and `endIndex` will be
 * removed. Indices are retrieved from the selection if none are provided.
 *
<<<<<<< HEAD
 * @param {RichTextValue}  value          Value to modify.
 * @param {RichTextFormat} formatToInsert Format to insert as object.
 * @param {number}         [startIndex]   Start index.
 * @param {number}         [endIndex]     End index.
 *
 * @return {RichTextValue} A new value with the object inserted.
 */

function insertObject(value, formatToInsert, startIndex, endIndex) {
  const valueToInsert = {
    formats: [,],
    replacements: [formatToInsert],
    text: OBJECT_REPLACEMENT_CHARACTER
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  };
  return insert(value, valueToInsert, startIndex, endIndex);
}

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/slice.js
/** @typedef {import('./types').RichTextValue} RichTextValue */

=======
// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/slice.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Slice a Rich Text value from `startIndex` to `endIndex`. Indices are
 * retrieved from the selection if none are provided. This is similar to
 * `String.prototype.slice`.
 *
<<<<<<< HEAD
 * @param {RichTextValue} value        Value to modify.
 * @param {number}        [startIndex] Start index.
 * @param {number}        [endIndex]   End index.
 *
 * @return {RichTextValue} A new extracted value.
 */
function slice(value, startIndex = value.start, endIndex = value.end) {
  const {
    formats,
    replacements,
    text
  } = value;

  if (startIndex === undefined || endIndex === undefined) {
    return { ...value
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    };
  }

  return {
    formats: formats.slice(startIndex, endIndex),
    replacements: replacements.slice(startIndex, endIndex),
    text: text.slice(startIndex, endIndex)
  };
}

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/split.js
=======
// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/split.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */

<<<<<<< HEAD
/** @typedef {import('./types').RichTextValue} RichTextValue */

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Split a Rich Text value in two at the given `startIndex` and `endIndex`, or
 * split at the given separator. This is similar to `String.prototype.split`.
 * Indices are retrieved from the selection if none are provided.
 *
<<<<<<< HEAD
 * @param {RichTextValue} value
 * @param {number|string} [string] Start index, or string at which to split.
 *
 * @return {Array<RichTextValue>|undefined} An array of new values.
 */

function split({
  formats,
  replacements,
  text,
  start,
  end
}, string) {
  if (typeof string !== 'string') {
    return splitAtSelection(...arguments);
  }

  let nextStart = 0;
  return text.split(string).map(substring => {
    const startIndex = nextStart;
    const value = {
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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

<<<<<<< HEAD
function splitAtSelection({
  formats,
  replacements,
  text,
  start,
  end
}, startIndex = start, endIndex = end) {
  if (start === undefined || end === undefined) {
    return;
  }

  const before = {
=======
function splitAtSelection(_ref2) {
  var formats = _ref2.formats,
      text = _ref2.text,
      start = _ref2.start,
      end = _ref2.end,
      replacements = _ref2.replacements;
  var startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : start;
  var endIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : end;
  var before = {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    formats: formats.slice(0, startIndex),
    replacements: replacements.slice(0, startIndex),
    text: text.slice(0, startIndex)
  };
<<<<<<< HEAD
  const after = {
=======
  var after = {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    formats: formats.slice(endIndex),
    replacements: replacements.slice(endIndex),
    text: text.slice(endIndex),
    start: 0,
    end: 0
  };
  return [// Ensure newlines are trimmed.
<<<<<<< HEAD
  replace_replace(before, /\u2028+$/, ''), replace_replace(after, /^\u2028+/, '')];
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-format-type.js
=======
  replace(before, /\u2028+$/, ''), replace(after, /^\u2028+/, '')];
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-format-type.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

/**
<<<<<<< HEAD
 * Internal dependencies
 */


/** @typedef {import('./register-format-type').RichTextFormatType} RichTextFormatType */

/**
=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 * Returns a registered format type.
 *
 * @param {string} name Format name.
 *
<<<<<<< HEAD
 * @return {RichTextFormatType|undefined} Format type.
 */

function get_format_type_getFormatType(name) {
  return (0,external_wp_data_namespaceObject.select)(store).getFormatType(name);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/to-tree.js
=======
 * @return {?Object} Format type.
 */

function get_format_type_getFormatType(name) {
  return Object(external_this_wp_data_["select"])('core/rich-text').getFormatType(name);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/to-tree.js



>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */



<<<<<<< HEAD

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
function restoreOnAttributes(attributes, isEditableTree) {
  if (isEditableTree) {
    return attributes;
  }

<<<<<<< HEAD
  const newAttributes = {};

  for (const key in attributes) {
    let newKey = key;
=======
  var newAttributes = {};

  for (var key in attributes) {
    var newKey = key;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

    if (key.startsWith('data-disable-rich-text-')) {
      newKey = key.slice('data-disable-rich-text-'.length);
    }

    newAttributes[newKey] = attributes[key];
  }

  return newAttributes;
}
<<<<<<< HEAD
/**
 * Converts a format object to information that can be used to create an element
 * from (type, attributes and object).
 *
 * @param {Object}  $1                        Named parameters.
 * @param {string}  $1.type                   The format type.
 * @param {string}  $1.tagName                The tag name.
 * @param {Object}  $1.attributes             The format attributes.
 * @param {Object}  $1.unregisteredAttributes The unregistered format
 *                                            attributes.
 * @param {boolean} $1.object                 Whether or not it is an object
 *                                            format.
 * @param {boolean} $1.boundaryClass          Whether or not to apply a boundary
 *                                            class.
 * @param {boolean} $1.isEditableTree
 *
 * @return {Object} Information to be used for element creation.
 */


function fromFormat({
  type,
  tagName,
  attributes,
  unregisteredAttributes,
  object,
  boundaryClass,
  isEditableTree
}) {
  const formatType = get_format_type_getFormatType(type);
  let elementAttributes = {};

  if (boundaryClass && isEditableTree) {
    elementAttributes['data-rich-text-format-boundary'] = 'true';
  }

  if (!formatType) {
    if (attributes) {
      elementAttributes = { ...attributes,
        ...elementAttributes
      };
    }

    return {
      type,
      attributes: restoreOnAttributes(elementAttributes, isEditableTree),
      object
    };
  }

  elementAttributes = { ...unregisteredAttributes,
    ...elementAttributes
  };

  for (const name in attributes) {
    const key = formatType.attributes ? formatType.attributes[name] : false;
=======

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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

    if (key) {
      elementAttributes[key] = attributes[name];
    } else {
      elementAttributes[name] = attributes[name];
    }
  }

  if (formatType.className) {
    if (elementAttributes.class) {
<<<<<<< HEAD
      elementAttributes.class = `${formatType.className} ${elementAttributes.class}`;
    } else {
      elementAttributes.class = formatType.className;
    }
  } // When a format is declared as non editable, make it non editable in the
  // editor.


  if (isEditableTree && formatType.contentEditable === false) {
    elementAttributes.contenteditable = 'false';
  }

  return {
    type: tagName || formatType.tagName,
=======
      elementAttributes.class = "".concat(formatType.className, " ").concat(elementAttributes.class);
    } else {
      elementAttributes.class = formatType.className;
    }
  }

  return {
    type: formatType.tagName,
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    object: formatType.object,
    attributes: restoreOnAttributes(elementAttributes, isEditableTree)
  };
}
<<<<<<< HEAD
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

function toTree({
  value,
  multilineTag,
  preserveWhiteSpace,
  createEmpty,
  append,
  getLastChild,
  getParent,
  isText,
  getText,
  remove,
  appendText,
  onStartIndex,
  onEndIndex,
  isEditableTree,
  placeholder
}) {
  const {
    formats,
    replacements,
    text,
    start,
    end
  } = value;
  const formatsLength = formats.length + 1;
  const tree = createEmpty();
  const multilineFormat = {
    type: multilineTag
  };
  const activeFormats = getActiveFormats(value);
  const deepestActiveFormat = activeFormats[activeFormats.length - 1];
  let lastSeparatorFormats;
  let lastCharacterFormats;
  let lastCharacter; // If we're building a multiline tree, start off with a multiline element.
=======

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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (multilineTag) {
    append(append(tree, {
      type: multilineTag
    }), '');
    lastCharacterFormats = lastSeparatorFormats = [multilineFormat];
  } else {
    append(tree, '');
  }

<<<<<<< HEAD
  for (let i = 0; i < formatsLength; i++) {
    const character = text.charAt(i);
    const shouldInsertPadding = isEditableTree && ( // Pad the line if the line is empty.
    !lastCharacter || lastCharacter === LINE_SEPARATOR || // Pad the line if the previous character is a line break, otherwise
    // the line break won't be visible.
    lastCharacter === '\n');
    let characterFormats = formats[i]; // Set multiline tags in queue for building the tree.

    if (multilineTag) {
      if (character === LINE_SEPARATOR) {
        characterFormats = lastSeparatorFormats = (replacements[i] || []).reduce((accumulator, format) => {
          accumulator.push(format, multilineFormat);
          return accumulator;
        }, [multilineFormat]);
      } else {
        characterFormats = [...lastSeparatorFormats, ...(characterFormats || [])];
      }
    }

    let pointer = getLastChild(tree);

    if (shouldInsertPadding && character === LINE_SEPARATOR) {
      let node = pointer;

      while (!isText(node)) {
        node = getLastChild(node);
      }

      append(getParent(node), ZWNBSP);
    } // Set selection for the start of line.


    if (lastCharacter === LINE_SEPARATOR) {
      let node = pointer;
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

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
<<<<<<< HEAD
      characterFormats.forEach((format, formatIndex) => {
        if (pointer && lastCharacterFormats && // Reuse the last element if all formats remain the same.
        isEqualUntil(characterFormats, lastCharacterFormats, formatIndex) && ( // Do not reuse the last element if the character is a
=======
      characterFormats.forEach(function (format, formatIndex) {
        if (pointer && lastCharacterFormats && format === lastCharacterFormats[formatIndex] && ( // Do not reuse the last element if the character is a
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
        // line separator.
        character !== LINE_SEPARATOR || characterFormats.length - 1 !== formatIndex)) {
          pointer = getLastChild(pointer);
          return;
        }

<<<<<<< HEAD
        const {
          type,
          tagName,
          attributes,
          unregisteredAttributes
        } = format;
        const boundaryClass = isEditableTree && character !== LINE_SEPARATOR && format === deepestActiveFormat;
        const parent = getParent(pointer);
        const newNode = append(parent, fromFormat({
          type,
          tagName,
          attributes,
          unregisteredAttributes,
          boundaryClass,
          isEditableTree
=======
        var parent = getParent(pointer);
        var type = format.type,
            attributes = format.attributes,
            unregisteredAttributes = format.unregisteredAttributes;
        var newNode = append(parent, fromFormat({
          type: type,
          attributes: attributes,
          unregisteredAttributes: unregisteredAttributes,
          isEditableTree: isEditableTree
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
        }));

        if (isText(pointer) && getText(pointer).length === 0) {
          remove(pointer);
        }

<<<<<<< HEAD
        pointer = append(newNode, '');
=======
        pointer = append(format.object ? parent : newNode, '');
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      });
    } // No need for further processing if the character is a line separator.


    if (character === LINE_SEPARATOR) {
      lastCharacterFormats = characterFormats;
      lastCharacter = character;
<<<<<<< HEAD
      continue;
    } // If there is selection at 0, handle it before characters are inserted.

=======
      return "continue";
    }

    pointer = setFormatPlaceholder(pointer, 0); // If there is selection at 0, handle it before characters are inserted.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

    if (i === 0) {
      if (onStartIndex && start === 0) {
        onStartIndex(tree, pointer);
      }

      if (onEndIndex && end === 0) {
        onEndIndex(tree, pointer);
      }
    }

    if (character === OBJECT_REPLACEMENT_CHARACTER) {
<<<<<<< HEAD
      const replacement = replacements[i];
      if (!replacement) continue;
      const {
        type,
        attributes,
        innerHTML
      } = replacement;
      const formatType = get_format_type_getFormatType(type);

      if (!isEditableTree && type === 'script') {
        pointer = append(getParent(pointer), fromFormat({
          type: 'script',
          isEditableTree
        }));
        append(pointer, {
          html: decodeURIComponent(attributes['data-rich-text-script'])
        });
      } else if (formatType?.contentEditable === false) {
        // For non editable formats, render the stored inner HTML.
        pointer = append(getParent(pointer), fromFormat({ ...replacement,
          isEditableTree,
          boundaryClass: start === i && end === i + 1
        }));

        if (innerHTML) {
          append(pointer, {
            html: innerHTML
          });
        }
      } else {
        pointer = append(getParent(pointer), fromFormat({ ...replacement,
          object: true,
          isEditableTree
        }));
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      } // Ensure pointer is text node.


      pointer = append(getParent(pointer), '');
<<<<<<< HEAD
    } else if (!preserveWhiteSpace && character === '\n') {
      pointer = append(getParent(pointer), {
        type: 'br',
        attributes: isEditableTree ? {
          'data-rich-text-line-break': 'true'
        } : undefined,
=======
    } else if (character === '\n') {
      pointer = append(getParent(pointer), {
        type: 'br',
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
        object: true
      }); // Ensure pointer is text node.

      pointer = append(getParent(pointer), '');
    } else if (!isText(pointer)) {
      pointer = append(getParent(pointer), character);
    } else {
      appendText(pointer, character);
    }

<<<<<<< HEAD
=======
    pointer = setFormatPlaceholder(pointer, i + 1);

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    if (onStartIndex && start === i + 1) {
      onStartIndex(tree, pointer);
    }

    if (onEndIndex && end === i + 1) {
      onEndIndex(tree, pointer);
    }

<<<<<<< HEAD
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
            contenteditable: 'false',
            style: 'pointer-events:none;user-select:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;'
          }
        });
      }
    }

    lastCharacterFormats = characterFormats;
    lastCharacter = character;
=======
    lastCharacterFormats = characterFormats;
    lastCharacter = character;
  };

  for (var i = 0; i < formatsLength; i++) {
    var _ret = _loop(i);

    if (_ret === "continue") continue;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  }

  return tree;
}

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-range-equal.js
/**
 * Returns true if two ranges are equal, or false otherwise. Ranges are
 * considered equal if their start and end occur in the same container and
 * offset.
 *
 * @param {Range|null} a First range object to test.
 * @param {Range|null} b First range object to test.
 *
 * @return {boolean} Whether the two ranges are equal.
 */
function isRangeEqual(a, b) {
  return a === b || a && b && a.startContainer === b.startContainer && a.startOffset === b.startOffset && a.endContainer === b.endContainer && a.endOffset === b.endOffset;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/to-dom.js
=======
// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/to-dom.js



>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD

/** @typedef {import('./types').RichTextValue} RichTextValue */

=======
/**
 * Browser dependencies
 */

var to_dom_window$Node = window.Node,
    to_dom_TEXT_NODE = to_dom_window$Node.TEXT_NODE,
    to_dom_ELEMENT_NODE = to_dom_window$Node.ELEMENT_NODE;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
  const parentNode = node.parentNode;
  let i = 0;
=======
  var parentNode = node.parentNode;
  var i = 0;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  while (node = node.previousSibling) {
    i++;
  }

<<<<<<< HEAD
  path = [i, ...path];
=======
  path = [i].concat(Object(toConsumableArray["a" /* default */])(path));
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

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
<<<<<<< HEAD
  path = [...path];
=======
  path = Object(toConsumableArray["a" /* default */])(path);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  while (node && path.length > 1) {
    node = node.childNodes[path.shift()];
  }

  return {
<<<<<<< HEAD
    node,
    offset: path[0]
  };
}

function append(element, child) {
  if (child.html !== undefined) {
    return element.innerHTML += child.html;
  }

=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  if (typeof child === 'string') {
    child = element.ownerDocument.createTextNode(child);
  }

<<<<<<< HEAD
  const {
    type,
    attributes
  } = child;
=======
  var _child = child,
      type = _child.type,
      attributes = _child.attributes;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (type) {
    child = element.ownerDocument.createElement(type);

<<<<<<< HEAD
    for (const key in attributes) {
=======
    for (var key in attributes) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      child.setAttribute(key, attributes[key]);
    }
  }

  return element.appendChild(child);
}

<<<<<<< HEAD
function appendText(node, text) {
  node.appendData(text);
}

function getLastChild({
  lastChild
}) {
  return lastChild;
}

function getParent({
  parentNode
}) {
  return parentNode;
}

function isText(node) {
  return node.nodeType === node.TEXT_NODE;
}

function getText({
  nodeValue
}) {
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  return nodeValue;
}

function to_dom_remove(node) {
  return node.parentNode.removeChild(node);
}

<<<<<<< HEAD
function toDom({
  value,
  multilineTag,
  prepareEditableTree,
  isEditableTree = true,
  placeholder,
  doc = document
}) {
  let startPath = [];
  let endPath = [];

  if (prepareEditableTree) {
    value = { ...value,
      formats: prepareEditableTree(value)
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
   * @return {Object} RichText tree.
   */


  const createEmpty = () => createElement(doc, '');

  const tree = toTree({
    value,
    multilineTag,
    createEmpty,
    append,
    getLastChild,
    getParent,
    isText,
    getText,
    remove: to_dom_remove,
    appendText,

    onStartIndex(body, pointer) {
      startPath = createPathToNode(pointer, body, [pointer.nodeValue.length]);
    },

    onEndIndex(body, pointer) {
      endPath = createPathToNode(pointer, body, [pointer.nodeValue.length]);
    },

    isEditableTree,
    placeholder
  });
  return {
    body: tree,
    selection: {
      startPath,
      endPath
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    }
  };
}
/**
 * Create an `Element` tree from a Rich Text value and applies the difference to
 * the `Element` tree contained by `current`. If a `multilineTag` is provided,
 * text separated by two new lines will be wrapped in an `Element` of that type.
 *
<<<<<<< HEAD
 * @param {Object}        $1                       Named arguments.
 * @param {RichTextValue} $1.value                 Value to apply.
 * @param {HTMLElement}   $1.current               The live root node to apply the element tree to.
 * @param {string}        [$1.multilineTag]        Multiline tag.
 * @param {Function}      [$1.prepareEditableTree] Function to filter editorable formats.
 * @param {boolean}       [$1.__unstableDomOnly]   Only apply elements, no selection.
 * @param {string}        [$1.placeholder]         Placeholder text.
 */

function apply({
  value,
  current,
  multilineTag,
  prepareEditableTree,
  __unstableDomOnly,
  placeholder
}) {
  // Construct a new element tree in memory.
  const {
    body,
    selection
  } = toDom({
    value,
    multilineTag,
    prepareEditableTree,
    placeholder,
    doc: current.ownerDocument
  });
  applyValue(body, current);

  if (value.start !== undefined && !__unstableDomOnly) {
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    applySelection(selection, current);
  }
}
function applyValue(future, current) {
<<<<<<< HEAD
  let i = 0;
  let futureChild;

  while (futureChild = future.firstChild) {
    const currentChild = current.childNodes[i];
=======
  var i = 0;
  var futureChild;

  while (futureChild = future.firstChild) {
    var currentChild = current.childNodes[i];
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

    if (!currentChild) {
      current.appendChild(futureChild);
    } else if (!currentChild.isEqualNode(futureChild)) {
<<<<<<< HEAD
      if (currentChild.nodeName !== futureChild.nodeName || currentChild.nodeType === currentChild.TEXT_NODE && currentChild.data !== futureChild.data) {
        current.replaceChild(futureChild, currentChild);
      } else {
        const currentAttributes = currentChild.attributes;
        const futureAttributes = futureChild.attributes;

        if (currentAttributes) {
          let ii = currentAttributes.length; // Reverse loop because `removeAttribute` on `currentChild`
          // changes `currentAttributes`.

          while (ii--) {
            const {
              name
            } = currentAttributes[ii];

            if (!futureChild.getAttribute(name)) {
              currentChild.removeAttribute(name);
            }
          }
        }

        if (futureAttributes) {
          for (let ii = 0; ii < futureAttributes.length; ii++) {
            const {
              name,
              value
            } = futureAttributes[ii];

            if (currentChild.getAttribute(name) !== value) {
              currentChild.setAttribute(name, value);
            }
          }
        }

        applyValue(futureChild, currentChild);
        future.removeChild(futureChild);
      }
=======
      current.replaceChild(futureChild, currentChild);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    } else {
      future.removeChild(futureChild);
    }

    i++;
  }

  while (current.childNodes[i]) {
    current.removeChild(current.childNodes[i]);
  }
}
<<<<<<< HEAD
function applySelection({
  startPath,
  endPath
}, current) {
  const {
    node: startContainer,
    offset: startOffset
  } = getNodeByPath(current, startPath);
  const {
    node: endContainer,
    offset: endOffset
  } = getNodeByPath(current, endPath);
  const {
    ownerDocument
  } = current;
  const {
    defaultView
  } = ownerDocument;
  const selection = defaultView.getSelection();
  const range = ownerDocument.createRange();
  range.setStart(startContainer, startOffset);
  range.setEnd(endContainer, endOffset);
  const {
    activeElement
  } = ownerDocument;

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

;// CONCATENATED MODULE: external ["wp","escapeHtml"]
const external_wp_escapeHtml_namespaceObject = window["wp"]["escapeHtml"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/to-html-string.js
/**
 * WordPress dependencies
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */

/**
 * Internal dependencies
 */


<<<<<<< HEAD
/** @typedef {import('./types').RichTextValue} RichTextValue */

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Create an HTML string from a Rich Text value. If a `multilineTag` is
 * provided, text separated by a line separator will be wrapped in it.
 *
<<<<<<< HEAD
 * @param {Object}        $1                      Named argements.
 * @param {RichTextValue} $1.value                Rich text value.
 * @param {string}        [$1.multilineTag]       Multiline tag.
 * @param {boolean}       [$1.preserveWhiteSpace] Whether or not to use newline
 *                                                characters for line breaks.
=======
 * @param {Object} $1                      Named argements.
 * @param {Object} $1.value                Rich text value.
 * @param {string} $1.multilineTag         Multiline tag.
 * @param {Array}  $1.multilineWrapperTags Tags where lines can be found if
 *                                         nesting is possible.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {string} HTML string.
 */

<<<<<<< HEAD
function toHTMLString({
  value,
  multilineTag,
  preserveWhiteSpace
}) {
  const tree = toTree({
    value,
    multilineTag,
    preserveWhiteSpace,
    createEmpty,
=======
function toHTMLString(_ref) {
  var value = _ref.value,
      multilineTag = _ref.multilineTag,
      multilineWrapperTags = _ref.multilineWrapperTags;
  var tree = toTree({
    value: value,
    multilineTag: multilineTag,
    multilineWrapperTags: multilineWrapperTags,
    createEmpty: to_html_string_createEmpty,
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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

<<<<<<< HEAD
function createEmpty() {
  return {};
}

function to_html_string_getLastChild({
  children
}) {
=======
function to_html_string_createEmpty() {
  return {};
}

function to_html_string_getLastChild(_ref2) {
  var children = _ref2.children;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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

<<<<<<< HEAD
function to_html_string_getParent({
  parent
}) {
  return parent;
}

function to_html_string_isText({
  text
}) {
  return typeof text === 'string';
}

function to_html_string_getText({
  text
}) {
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  return text;
}

function to_html_string_remove(object) {
<<<<<<< HEAD
  const index = object.parent.children.indexOf(object);
=======
  var index = object.parent.children.indexOf(object);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (index !== -1) {
    object.parent.children.splice(index, 1);
  }

  return object;
}

<<<<<<< HEAD
function createElementHTML({
  type,
  attributes,
  object,
  children
}) {
  let attributeString = '';

  for (const key in attributes) {
    if (!(0,external_wp_escapeHtml_namespaceObject.isValidAttributeName)(key)) {
      continue;
    }

    attributeString += ` ${key}="${(0,external_wp_escapeHtml_namespaceObject.escapeAttribute)(attributes[key])}"`;
  }

  if (object) {
    return `<${type}${attributeString}>`;
  }

  return `<${type}${attributeString}>${createChildrenHTML(children)}</${type}>`;
}

function createChildrenHTML(children = []) {
  return children.map(child => {
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    if (child.html !== undefined) {
      return child.html;
    }

<<<<<<< HEAD
    return child.text === undefined ? createElementHTML(child) : (0,external_wp_escapeHtml_namespaceObject.escapeEditableHTML)(child.text);
  }).join('');
}

;// CONCATENATED MODULE: external ["wp","a11y"]
const external_wp_a11y_namespaceObject = window["wp"]["a11y"];
;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/toggle-format.js
/**
 * WordPress dependencies
 */


=======
    return child.text === undefined ? createElementHTML(child) : Object(external_this_wp_escapeHtml_["escapeHTML"])(child.text);
  }).join('');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/toggle-format.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */



<<<<<<< HEAD

/** @typedef {import('./types').RichTextValue} RichTextValue */

/** @typedef {import('./types').RichTextFormat} RichTextFormat */

/**
 * Toggles a format object to a Rich Text value at the current selection.
 *
 * @param {RichTextValue}  value  Value to modify.
 * @param {RichTextFormat} format Format to apply or remove.
 *
 * @return {RichTextValue} A new value with the format applied or removed.
=======
/**
 * Toggles a format object to a Rich Text value at the current selection.
 *
 * @param {Object} value      Value to modify.
 * @param {Object} format     Format to apply or remove.
 *
 * @return {Object} A new value with the format applied or removed.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */

function toggleFormat(value, format) {
  if (getActiveFormat(value, format.type)) {
<<<<<<< HEAD
    // For screen readers, will announce if formatting control is disabled.
    if (format.title) {
      // translators: %s: title of the formatting control
      (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)('%s removed.'), format.title), 'assertive');
    }

    return removeFormat(value, format.type);
  } // For screen readers, will announce if formatting control is enabled.


  if (format.title) {
    // translators: %s: title of the formatting control
    (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)('%s applied.'), format.title), 'assertive');
=======
    return removeFormat(value, format.type);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  }

  return applyFormat(value, format);
}

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/unregister-format-type.js
=======
// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/unregister-format-type.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

<<<<<<< HEAD
/**
 * Internal dependencies
 */


/** @typedef {import('./register-format-type').WPFormat} WPFormat */
=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * Unregisters a format.
 *
 * @param {string} name Format name.
 *
<<<<<<< HEAD
 * @return {WPFormat|undefined} The previous format value, if it has
 *                                        been successfully unregistered;
 *                                        otherwise `undefined`.
 */

function unregisterFormatType(name) {
  const oldFormat = (0,external_wp_data_namespaceObject.select)(store).getFormatType(name);

  if (!oldFormat) {
    window.console.error(`Format ${name} is not registered.`);
    return;
  }

  (0,external_wp_data_namespaceObject.dispatch)(store).removeFormatTypes(name);
  return oldFormat;
}

;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-anchor-ref.js
/**
 * WordPress dependencies
 */


=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */

<<<<<<< HEAD

/**
 * @template T
 * @typedef {import('@wordpress/element').RefObject<T>} RefObject<T>
 */

/** @typedef {import('../register-format-type').WPFormat} WPFormat */

/** @typedef {import('../types').RichTextValue} RichTextValue */

/**
 * This hook, to be used in a format type's Edit component, returns the active
 * element that is formatted, or the selection range if no format is active.
 * The returned value is meant to be used for positioning UI, e.g. by passing it
 * to the `Popover` component.
 *
 * @param {Object}                 $1          Named parameters.
 * @param {RefObject<HTMLElement>} $1.ref      React ref of the element
 *                                             containing  the editable content.
 * @param {RichTextValue}          $1.value    Value to check for selection.
 * @param {WPFormat}               $1.settings The format type's settings.
 *
 * @return {Element|Range} The active element or selection range.
 */

function useAnchorRef({
  ref,
  value,
  settings = {}
}) {
  external_wp_deprecated_default()('`useAnchorRef` hook', {
    since: '6.1',
    alternative: '`useAnchor` hook'
  });
  const {
    tagName,
    className,
    name
  } = settings;
  const activeFormat = name ? getActiveFormat(value, name) : undefined;
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!ref.current) return;
    const {
      ownerDocument: {
        defaultView
      }
    } = ref.current;
    const selection = defaultView.getSelection();

    if (!selection.rangeCount) {
      return;
    }

    const range = selection.getRangeAt(0);

    if (!activeFormat) {
      return range;
    }

    let element = range.startContainer; // If the caret is right before the element, select the next element.

    element = element.nextElementSibling || element;

    while (element.nodeType !== element.ELEMENT_NODE) {
      element = element.parentNode;
    }

    return element.closest(tagName + (className ? '.' + className : ''));
  }, [activeFormat, value.start, value.end, tagName, className]);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-anchor.js
/**
 * WordPress dependencies
 */

/** @typedef {import('../register-format-type').WPFormat} WPFormat */

/** @typedef {import('../types').RichTextValue} RichTextValue */

/**
 * Given a range and a format tag name and class name, returns the closest
 * format element.
 *
 * @param {Range}       range                  The Range to check.
 * @param {HTMLElement} editableContentElement The editable wrapper.
 * @param {string}      tagName                The tag name of the format element.
 * @param {string}      className              The class name of the format element.
 *
 * @return {HTMLElement|undefined} The format element, if found.
 */

function getFormatElement(range, editableContentElement, tagName, className) {
  let element = range.startContainer; // If the caret is right before the element, select the next element.

  element = element.nextElementSibling || element;

  if (element.nodeType !== element.ELEMENT_NODE) {
    element = element.parentElement;
  }

  if (!element) return;
  if (element === editableContentElement) return;
  if (!editableContentElement.contains(element)) return;
  const selector = tagName + (className ? '.' + className : ''); // .closest( selector ), but with a boundary. Check if the element matches
  // the selector. If it doesn't match, try the parent element if it's not the
  // editable wrapper. We don't want to try to match ancestors of the editable
  // wrapper, which is what .closest( selector ) would do. When the element is
  // the editable wrapper (which is most likely the case because most text is
  // unformatted), this never runs.

  while (element !== editableContentElement) {
    if (element.matches(selector)) {
      return element;
    }

    element = element.parentElement;
  }
}
/**
 * @typedef {Object} VirtualAnchorElement
 * @property {() => DOMRect} getBoundingClientRect A function returning a DOMRect
 * @property {Document}      ownerDocument         The element's ownerDocument
 */

/**
 * Creates a virtual anchor element for a range.
 *
 * @param {Range}       range                  The range to create a virtual anchor element for.
 * @param {HTMLElement} editableContentElement The editable wrapper.
 *
 * @return {VirtualAnchorElement} The virtual anchor element.
 */


function createVirtualAnchorElement(range, editableContentElement) {
  return {
    ownerDocument: range.startContainer.ownerDocument,

    getBoundingClientRect() {
      return editableContentElement.contains(range.startContainer) ? range.getBoundingClientRect() : editableContentElement.getBoundingClientRect();
    }

  };
}
/**
 * Get the anchor: a format element if there is a matching one based on the
 * tagName and className or a range otherwise.
 *
 * @param {HTMLElement} editableContentElement The editable wrapper.
 * @param {string}      tagName                The tag name of the format
 *                                             element.
 * @param {string}      className              The class name of the format
 *                                             element.
 *
 * @return {HTMLElement|VirtualAnchorElement|undefined} The anchor.
 */


function getAnchor(editableContentElement, tagName, className) {
  if (!editableContentElement) return;
  const {
    ownerDocument
  } = editableContentElement;
  const {
    defaultView
  } = ownerDocument;
  const selection = defaultView.getSelection();
  if (!selection) return;
  if (!selection.rangeCount) return;
  const range = selection.getRangeAt(0);
  if (!range || !range.startContainer) return;
  const formatElement = getFormatElement(range, editableContentElement, tagName, className);
  if (formatElement) return formatElement;
  return createVirtualAnchorElement(range, editableContentElement);
}
/**
 * This hook, to be used in a format type's Edit component, returns the active
 * element that is formatted, or a virtual element for the selection range if
 * no format is active. The returned value is meant to be used for positioning
 * UI, e.g. by passing it to the `Popover` component via the `anchor` prop.
 *
 * @param {Object}           $1                        Named parameters.
 * @param {HTMLElement|null} $1.editableContentElement The element containing
 *                                                     the editable content.
 * @param {WPFormat=}        $1.settings               The format type's settings.
 * @return {Element|VirtualAnchorElement|undefined|null} The active element or selection range.
 */


function useAnchor({
  editableContentElement,
  settings = {}
}) {
  const {
    tagName,
    className
  } = settings;
  const [anchor, setAnchor] = (0,external_wp_element_namespaceObject.useState)(() => getAnchor(editableContentElement, tagName, className));
  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    if (!editableContentElement) return;
    const {
      ownerDocument
    } = editableContentElement;

    function callback() {
      setAnchor(getAnchor(editableContentElement, tagName, className));
    }

    function attach() {
      ownerDocument.addEventListener('selectionchange', callback);
    }

    function detach() {
      ownerDocument.removeEventListener('selectionchange', callback);
    }

    if (editableContentElement === ownerDocument.activeElement) {
      attach();
    }

    editableContentElement.addEventListener('focusin', attach);
    editableContentElement.addEventListener('focusout', detach);
    return detach;
  }, [editableContentElement, tagName, className]);
  return anchor;
}

;// CONCATENATED MODULE: external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-default-style.js
/**
 * WordPress dependencies
 */

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

const whiteSpace = 'pre-wrap';
/**
 * A minimum width of 1px will prevent the rich text container from collapsing
 * to 0 width and hiding the caret. This is useful for inline containers.
 */

const minWidth = '1px';
function useDefaultStyle() {
  return (0,external_wp_element_namespaceObject.useCallback)(element => {
    if (!element) return;
    element.style.whiteSpace = whiteSpace;
    element.style.minWidth = minWidth;
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-boundary-style.js
/**
 * WordPress dependencies
 */

/*
 * Calculates and renders the format boundary style when the active formats
 * change.
 */

function useBoundaryStyle({
  record
}) {
  const ref = (0,external_wp_element_namespaceObject.useRef)();
  const {
    activeFormats = [],
    replacements,
    start
  } = record.current;
  const activeReplacement = replacements[start];
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    // There's no need to recalculate the boundary styles if no formats are
    // active, because no boundary styles will be visible.
    if ((!activeFormats || !activeFormats.length) && !activeReplacement) {
      return;
    }

    const boundarySelector = '*[data-rich-text-format-boundary]';
    const element = ref.current.querySelector(boundarySelector);

    if (!element) {
      return;
    }

    const {
      ownerDocument
    } = element;
    const {
      defaultView
    } = ownerDocument;
    const computedStyle = defaultView.getComputedStyle(element);
    const newColor = computedStyle.color.replace(')', ', 0.2)').replace('rgb', 'rgba');
    const selector = `.rich-text:focus ${boundarySelector}`;
    const rule = `background-color: ${newColor}`;
    const style = `${selector} {${rule}}`;
    const globalStyleId = 'rich-text-boundary-style';
    let globalStyle = ownerDocument.getElementById(globalStyleId);

    if (!globalStyle) {
      globalStyle = ownerDocument.createElement('style');
      globalStyle.id = globalStyleId;
      ownerDocument.head.appendChild(globalStyle);
    }

    if (globalStyle.innerHTML !== style) {
      globalStyle.innerHTML = style;
    }
  }, [activeFormats, activeReplacement]);
  return ref;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-copy-handler.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





function useCopyHandler(props) {
  const propsRef = (0,external_wp_element_namespaceObject.useRef)(props);
  propsRef.current = props;
  return (0,external_wp_compose_namespaceObject.useRefEffect)(element => {
    function onCopy(event) {
      const {
        record,
        multilineTag,
        preserveWhiteSpace
      } = propsRef.current;
      const {
        ownerDocument
      } = element;

      if (isCollapsed(record.current) || !element.contains(ownerDocument.activeElement)) {
        return;
      }

      const selectedRecord = slice(record.current);
      const plainText = getTextContent(selectedRecord);
      const html = toHTMLString({
        value: selectedRecord,
        multilineTag,
        preserveWhiteSpace
      });
      event.clipboardData.setData('text/plain', plainText);
      event.clipboardData.setData('text/html', html);
      event.clipboardData.setData('rich-text', 'true');
      event.clipboardData.setData('rich-text-multi-line-tag', multilineTag || '');
      event.preventDefault();

      if (event.type === 'cut') {
        ownerDocument.execCommand('delete');
      }
    }

    element.addEventListener('copy', onCopy);
    element.addEventListener('cut', onCopy);
    return () => {
      element.removeEventListener('copy', onCopy);
      element.removeEventListener('cut', onCopy);
    };
  }, []);
}

;// CONCATENATED MODULE: external ["wp","keycodes"]
const external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-format-boundaries.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


const EMPTY_ACTIVE_FORMATS = [];
function useFormatBoundaries(props) {
  const [, forceRender] = (0,external_wp_element_namespaceObject.useReducer)(() => ({}));
  const propsRef = (0,external_wp_element_namespaceObject.useRef)(props);
  propsRef.current = props;
  return (0,external_wp_compose_namespaceObject.useRefEffect)(element => {
    function onKeyDown(event) {
      const {
        keyCode,
        shiftKey,
        altKey,
        metaKey,
        ctrlKey
      } = event;

      if ( // Only override left and right keys without modifiers pressed.
      shiftKey || altKey || metaKey || ctrlKey || keyCode !== external_wp_keycodes_namespaceObject.LEFT && keyCode !== external_wp_keycodes_namespaceObject.RIGHT) {
        return;
      }

      const {
        record,
        applyRecord
      } = propsRef.current;
      const {
        text,
        formats,
        start,
        end,
        activeFormats: currentActiveFormats = []
      } = record.current;
      const collapsed = isCollapsed(record.current);
      const {
        ownerDocument
      } = element;
      const {
        defaultView
      } = ownerDocument; // To do: ideally, we should look at visual position instead.

      const {
        direction
      } = defaultView.getComputedStyle(element);
      const reverseKey = direction === 'rtl' ? external_wp_keycodes_namespaceObject.RIGHT : external_wp_keycodes_namespaceObject.LEFT;
      const isReverse = event.keyCode === reverseKey; // If the selection is collapsed and at the very start, do nothing if
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
      }

      const formatsBefore = formats[start - 1] || EMPTY_ACTIVE_FORMATS;
      const formatsAfter = formats[start] || EMPTY_ACTIVE_FORMATS;
      const destination = isReverse ? formatsBefore : formatsAfter;
      const isIncreasing = currentActiveFormats.every((format, index) => format === destination[index]);
      let newActiveFormatsLength = currentActiveFormats.length;

      if (!isIncreasing) {
        newActiveFormatsLength--;
      } else if (newActiveFormatsLength < destination.length) {
        newActiveFormatsLength++;
      }

      if (newActiveFormatsLength === currentActiveFormats.length) {
        record.current._newActiveFormats = destination;
        return;
      }

      event.preventDefault();
      const origin = isReverse ? formatsAfter : formatsBefore;
      const source = isIncreasing ? destination : origin;
      const newActiveFormats = source.slice(0, newActiveFormatsLength);
      const newValue = { ...record.current,
        activeFormats: newActiveFormats
      };
      record.current = newValue;
      applyRecord(newValue);
      forceRender();
    }

    element.addEventListener('keydown', onKeyDown);
    return () => {
      element.removeEventListener('keydown', onKeyDown);
    };
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-select-object.js
/**
 * WordPress dependencies
 */

function useSelectObject() {
  return (0,external_wp_compose_namespaceObject.useRefEffect)(element => {
    function onClick(event) {
      const {
        target
      } = event; // If the child element has no text content, it must be an object.

      if (target === element || target.textContent && target.isContentEditable) {
        return;
      }

      const {
        ownerDocument
      } = target;
      const {
        defaultView
      } = ownerDocument;
      const selection = defaultView.getSelection(); // If it's already selected, do nothing and let default behavior
      // happen. This means it's "click-through".

      if (selection.containsNode(target)) return;
      const range = ownerDocument.createRange(); // If the target is within a non editable element, select the non
      // editable element.

      const nodeToSelect = target.isContentEditable ? target : target.closest('[contenteditable]');
      range.selectNode(nodeToSelect);
      selection.removeAllRanges();
      selection.addRange(range);
      event.preventDefault();
    }

    function onFocusIn(event) {
      // When there is incoming focus from a link, select the object.
      if (event.relatedTarget && !element.contains(event.relatedTarget) && event.relatedTarget.tagName === 'A') {
        onClick(event);
      }
    }

    element.addEventListener('click', onClick);
    element.addEventListener('focusin', onFocusIn);
    return () => {
      element.removeEventListener('click', onClick);
      element.removeEventListener('focusin', onFocusIn);
    };
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/update-formats.js
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */

<<<<<<< HEAD
/** @typedef {import('./types').RichTextValue} RichTextValue */

/**
 * Efficiently updates all the formats from `start` (including) until `end`
 * (excluding) with the active formats. Mutates `value`.
 *
 * @param {Object}        $1         Named paramentes.
 * @param {RichTextValue} $1.value   Value te update.
 * @param {number}        $1.start   Index to update from.
 * @param {number}        $1.end     Index to update until.
 * @param {Array}         $1.formats Replacement formats.
 *
 * @return {RichTextValue} Mutated value.
 */

function updateFormats({
  value,
  start,
  end,
  formats
}) {
  // Start and end may be switched in case of delete.
  const min = Math.min(start, end);
  const max = Math.max(start, end);
  const formatsBefore = value.formats[min - 1] || [];
  const formatsAfter = value.formats[max] || []; // First, fix the references. If any format right before or after are
  // equal, the replacement format should use the same reference.

  value.activeFormats = formats.map((format, index) => {
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

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-input-and-selection.js
/**
 * WordPress dependencies
 */


=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */

<<<<<<< HEAD


/**
 * All inserting input types that would insert HTML into the DOM.
 *
 * @see https://www.w3.org/TR/input-events-2/#interface-InputEvent-Attributes
 *
 * @type {Set}
 */

const INSERTION_INPUT_TYPES_TO_IGNORE = new Set(['insertParagraph', 'insertOrderedList', 'insertUnorderedList', 'insertHorizontalRule', 'insertLink']);
const use_input_and_selection_EMPTY_ACTIVE_FORMATS = [];
const PLACEHOLDER_ATTR_NAME = 'data-rich-text-placeholder';
/**
 * If the selection is set on the placeholder element, collapse the selection to
 * the start (before the placeholder).
 *
 * @param {Window} defaultView
 */

function fixPlaceholderSelection(defaultView) {
  const selection = defaultView.getSelection();
  const {
    anchorNode,
    anchorOffset
  } = selection;

  if (anchorNode.nodeType !== anchorNode.ELEMENT_NODE) {
    return;
  }

  const targetNode = anchorNode.childNodes[anchorOffset];

  if (!targetNode || targetNode.nodeType !== targetNode.ELEMENT_NODE || !targetNode.hasAttribute(PLACEHOLDER_ATTR_NAME)) {
    return;
  }

  selection.collapseToStart();
}

function useInputAndSelection(props) {
  const propsRef = (0,external_wp_element_namespaceObject.useRef)(props);
  propsRef.current = props;
  return (0,external_wp_compose_namespaceObject.useRefEffect)(element => {
    const {
      ownerDocument
    } = element;
    const {
      defaultView
    } = ownerDocument;
    let isComposing = false;

    function onInput(event) {
      // Do not trigger a change if characters are being composed.
      // Browsers  will usually emit a final `input` event when the
      // characters are composed.
      // As of December 2019, Safari doesn't support
      // nativeEvent.isComposing.
      if (isComposing) {
        return;
      }

      let inputType;

      if (event) {
        inputType = event.inputType;
      }

      const {
        record,
        applyRecord,
        createRecord,
        handleChange
      } = propsRef.current; // The browser formatted something or tried to insert HTML.
      // Overwrite it. It will be handled later by the format library if
      // needed.

      if (inputType && (inputType.indexOf('format') === 0 || INSERTION_INPUT_TYPES_TO_IGNORE.has(inputType))) {
        applyRecord(record.current);
        return;
      }

      const currentValue = createRecord();
      const {
        start,
        activeFormats: oldActiveFormats = []
      } = record.current; // Update the formats between the last and new caret position.

      const change = updateFormats({
        value: currentValue,
        start,
        end: currentValue.start,
        formats: oldActiveFormats
      });
      handleChange(change);
    }
    /**
     * Syncs the selection to local state. A callback for the
     * `selectionchange` event.
     */


    function handleSelectionChange() {
      const {
        record,
        applyRecord,
        createRecord,
        onSelectionChange
      } = propsRef.current; // Check if the implementor disabled editing. `contentEditable`
      // does disable input, but not text selection, so we must ignore
      // selection changes.

      if (element.contentEditable !== 'true') {
        return;
      } // If the selection changes where the active element is a parent of
      // the rich text instance (writing flow), call `onSelectionChange`
      // for the rich text instance that contains the start or end of the
      // selection.


      if (ownerDocument.activeElement !== element) {
        // Only process if the active elment is contentEditable, either
        // this rich text instance or the writing flow parent. Fixes a
        // bug in Firefox where it strangely selects the closest
        // contentEditable element, even though the click was outside
        // any contentEditable element.
        if (ownerDocument.activeElement.contentEditable !== 'true') {
          return;
        }

        if (!ownerDocument.activeElement.contains(element)) {
          return;
        }

        const selection = defaultView.getSelection();
        const {
          anchorNode,
          focusNode
        } = selection;

        if (element.contains(anchorNode) && element !== anchorNode && element.contains(focusNode) && element !== focusNode) {
          const {
            start,
            end
          } = createRecord();
          record.current.activeFormats = use_input_and_selection_EMPTY_ACTIVE_FORMATS;
          onSelectionChange(start, end);
        } else if (element.contains(anchorNode) && element !== anchorNode) {
          const {
            start,
            end: offset = start
          } = createRecord();
          record.current.activeFormats = use_input_and_selection_EMPTY_ACTIVE_FORMATS;
          onSelectionChange(offset);
        } else if (element.contains(focusNode)) {
          const {
            start,
            end: offset = start
          } = createRecord();
          record.current.activeFormats = use_input_and_selection_EMPTY_ACTIVE_FORMATS;
          onSelectionChange(undefined, offset);
        }

        return;
      } // In case of a keyboard event, ignore selection changes during
      // composition.


      if (isComposing) {
        return;
      }

      const {
        start,
        end,
        text
      } = createRecord();
      const oldRecord = record.current; // Fallback mechanism for IE11, which doesn't support the input event.
      // Any input results in a selection change.

      if (text !== oldRecord.text) {
        onInput();
        return;
      }

      if (start === oldRecord.start && end === oldRecord.end) {
        // Sometimes the browser may set the selection on the placeholder
        // element, in which case the caret is not visible. We need to set
        // the caret before the placeholder if that's the case.
        if (oldRecord.text.length === 0 && start === 0) {
          fixPlaceholderSelection(defaultView);
        }

        return;
      }

      const newValue = { ...oldRecord,
        start,
        end,
        // _newActiveFormats may be set on arrow key navigation to control
        // the right boundary position. If undefined, getActiveFormats will
        // give the active formats according to the browser.
        activeFormats: oldRecord._newActiveFormats,
        _newActiveFormats: undefined
      };
      const newActiveFormats = getActiveFormats(newValue, use_input_and_selection_EMPTY_ACTIVE_FORMATS); // Update the value with the new active formats.

      newValue.activeFormats = newActiveFormats; // It is important that the internal value is updated first,
      // otherwise the value will be wrong on render!

      record.current = newValue;
      applyRecord(newValue, {
        domOnly: true
      });
      onSelectionChange(start, end);
    }

    function onCompositionStart() {
      isComposing = true; // Do not update the selection when characters are being composed as
      // this rerenders the component and might destroy internal browser
      // editing state.

      ownerDocument.removeEventListener('selectionchange', handleSelectionChange); // Remove the placeholder. Since the rich text value doesn't update
      // during composition, the placeholder doesn't get removed. There's
      // no need to re-add it, when the value is updated on compositionend
      // it will be re-added when the value is empty.

      element.querySelector(`[${PLACEHOLDER_ATTR_NAME}]`)?.remove();
    }

    function onCompositionEnd() {
      isComposing = false; // Ensure the value is up-to-date for browsers that don't emit a final
      // input event after composition.

      onInput({
        inputType: 'insertText'
      }); // Tracking selection changes can be resumed.

      ownerDocument.addEventListener('selectionchange', handleSelectionChange);
    }

    function onFocus() {
      const {
        record,
        isSelected,
        onSelectionChange,
        applyRecord
      } = propsRef.current; // When the whole editor is editable, let writing flow handle
      // selection.

      if (element.parentElement.closest('[contenteditable="true"]')) {
        return;
      }

      if (!isSelected) {
        // We know for certain that on focus, the old selection is invalid.
        // It will be recalculated on the next mouseup, keyup, or touchend
        // event.
        const index = undefined;
        record.current = { ...record.current,
          start: index,
          end: index,
          activeFormats: use_input_and_selection_EMPTY_ACTIVE_FORMATS
        };
      } else {
        applyRecord(record.current);
        onSelectionChange(record.current.start, record.current.end);
      }
    }

    element.addEventListener('input', onInput);
    element.addEventListener('compositionstart', onCompositionStart);
    element.addEventListener('compositionend', onCompositionEnd);
    element.addEventListener('focus', onFocus);
    ownerDocument.addEventListener('selectionchange', handleSelectionChange);
    return () => {
      element.removeEventListener('input', onInput);
      element.removeEventListener('compositionstart', onCompositionStart);
      element.removeEventListener('compositionend', onCompositionEnd);
      element.removeEventListener('focus', onFocus);
      ownerDocument.removeEventListener('selectionchange', handleSelectionChange);
    };
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-selection-change-compat.js
/**
 * WordPress dependencies
 */

=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD
/**
 * Sometimes some browsers are not firing a `selectionchange` event when
 * changing the selection by mouse or keyboard. This hook makes sure that, if we
 * detect no `selectionchange` or `input` event between the up and down events,
 * we fire a `selectionchange` event.
 *
 * @return {import('@wordpress/compose').RefEffect} A ref effect attaching the
 *                                                  listeners.
 */

function useSelectionChangeCompat() {
  return (0,external_wp_compose_namespaceObject.useRefEffect)(element => {
    const {
      ownerDocument
    } = element;
    const {
      defaultView
    } = ownerDocument;
    const selection = defaultView?.getSelection();
    let range;

    function getRange() {
      return selection.rangeCount ? selection.getRangeAt(0) : null;
    }

    function onDown(event) {
      const type = event.type === 'keydown' ? 'keyup' : 'pointerup';

      function onCancel() {
        ownerDocument.removeEventListener(type, onUp);
        ownerDocument.removeEventListener('selectionchange', onCancel);
        ownerDocument.removeEventListener('input', onCancel);
      }

      function onUp() {
        onCancel();
        if (isRangeEqual(range, getRange())) return;
        ownerDocument.dispatchEvent(new Event('selectionchange'));
      }

      ownerDocument.addEventListener(type, onUp);
      ownerDocument.addEventListener('selectionchange', onCancel);
      ownerDocument.addEventListener('input', onCancel);
      range = getRange();
    }

    element.addEventListener('pointerdown', onDown);
    element.addEventListener('keydown', onDown);
    return () => {
      element.removeEventListener('pointerdown', onDown);
      element.removeEventListener('keydown', onDown);
    };
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/remove-line-separator.js
/**
 * Internal dependencies
 */



/** @typedef {import('./types').RichTextValue} RichTextValue */

/**
 * Removes a line separator character, if existing, from a Rich Text value at
 * the current indices. If no line separator exists on the indices it will
 * return undefined.
 *
 * @param {RichTextValue} value    Value to modify.
 * @param {boolean}       backward Indicates if are removing from the start
 *                                 index or the end index.
 *
 * @return {RichTextValue|undefined} A new value with the line separator
 *                                   removed. Or undefined if no line separator
 *                                   is found on the position.
 */

function removeLineSeparator(value, backward = true) {
  const {
    replacements,
    text,
    start,
    end
  } = value;
  const collapsed = isCollapsed(value);
  let index = start - 1;
  let removeStart = collapsed ? start - 1 : start;
  let removeEnd = end;

  if (!backward) {
    index = end;
    removeStart = start;
    removeEnd = collapsed ? end + 1 : end;
  }

  if (text[index] !== LINE_SEPARATOR) {
    return;
  }

  let newValue; // If the line separator that is about te be removed
  // contains wrappers, remove the wrappers first.

  if (collapsed && replacements[index] && replacements[index].length) {
    const newReplacements = replacements.slice();
    newReplacements[index] = replacements[index].slice(0, -1);
    newValue = { ...value,
      replacements: newReplacements
    };
  } else {
    newValue = remove(value, removeStart, removeEnd);
  }

  return newValue;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-delete.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




function useDelete(props) {
  const propsRef = (0,external_wp_element_namespaceObject.useRef)(props);
  propsRef.current = props;
  return (0,external_wp_compose_namespaceObject.useRefEffect)(element => {
    function onKeyDown(event) {
      const {
        keyCode
      } = event;
      const {
        createRecord,
        handleChange,
        multilineTag
      } = propsRef.current;

      if (event.defaultPrevented) {
        return;
      }

      if (keyCode !== external_wp_keycodes_namespaceObject.DELETE && keyCode !== external_wp_keycodes_namespaceObject.BACKSPACE) {
        return;
      }

      const currentValue = createRecord();
      const {
        start,
        end,
        text
      } = currentValue;
      const isReverse = keyCode === external_wp_keycodes_namespaceObject.BACKSPACE; // Always handle full content deletion ourselves.

      if (start === 0 && end !== 0 && end === text.length) {
        handleChange(remove(currentValue));
        event.preventDefault();
        return;
      }

      if (multilineTag) {
        let newValue; // Check to see if we should remove the first item if empty.

        if (isReverse && currentValue.start === 0 && currentValue.end === 0 && isEmptyLine(currentValue)) {
          newValue = removeLineSeparator(currentValue, !isReverse);
        } else {
          newValue = removeLineSeparator(currentValue, isReverse);
        }

        if (newValue) {
          handleChange(newValue);
          event.preventDefault();
        }
      }
    }

    element.addEventListener('keydown', onKeyDown);
    return () => {
      element.removeEventListener('keydown', onKeyDown);
    };
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/index.js
/**
 * WordPress dependencies
 */



=======



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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */




<<<<<<< HEAD








function useRichText({
  value = '',
  selectionStart,
  selectionEnd,
  placeholder,
  preserveWhiteSpace,
  onSelectionChange,
  onChange,
  __unstableMultilineTag: multilineTag,
  __unstableDisableFormats: disableFormats,
  __unstableIsSelected: isSelected,
  __unstableDependencies = [],
  __unstableAfterParse,
  __unstableBeforeSerialize,
  __unstableAddInvisibleFormats
}) {
  const registry = (0,external_wp_data_namespaceObject.useRegistry)();
  const [, forceRender] = (0,external_wp_element_namespaceObject.useReducer)(() => ({}));
  const ref = (0,external_wp_element_namespaceObject.useRef)();

  function createRecord() {
    const {
      ownerDocument: {
        defaultView
      }
    } = ref.current;
    const selection = defaultView.getSelection();
    const range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
    return create({
      element: ref.current,
      range,
      multilineTag,
      multilineWrapperTags: multilineTag === 'li' ? ['ul', 'ol'] : undefined,
      __unstableIsEditableTree: true,
      preserveWhiteSpace
    });
  }

  function applyRecord(newRecord, {
    domOnly
  } = {}) {
    apply({
      value: newRecord,
      current: ref.current,
      multilineTag,
      multilineWrapperTags: multilineTag === 'li' ? ['ul', 'ol'] : undefined,
      prepareEditableTree: __unstableAddInvisibleFormats,
      __unstableDomOnly: domOnly,
      placeholder
    });
  } // Internal values are updated synchronously, unlike props and state.


  const _value = (0,external_wp_element_namespaceObject.useRef)(value);

  const record = (0,external_wp_element_namespaceObject.useRef)();

  function setRecordFromProps() {
    _value.current = value;
    record.current = create({
      html: value,
      multilineTag,
      multilineWrapperTags: multilineTag === 'li' ? ['ul', 'ol'] : undefined,
      preserveWhiteSpace
    });

    if (disableFormats) {
      record.current.formats = Array(value.length);
      record.current.replacements = Array(value.length);
    }

    if (__unstableAfterParse) {
      record.current.formats = __unstableAfterParse(record.current);
    }

    record.current.start = selectionStart;
    record.current.end = selectionEnd;
  }

  const hadSelectionUpdate = (0,external_wp_element_namespaceObject.useRef)(false);

  if (!record.current) {
    hadSelectionUpdate.current = isSelected;
    setRecordFromProps(); // Sometimes formats are added programmatically and we need to make
    // sure it's persisted to the block store / markup. If these formats
    // are not applied, they could cause inconsistencies between the data
    // in the visual editor and the frontend. Right now, it's only relevant
    // to the `core/text-color` format, which is applied at runtime in
    // certain circunstances. See the `__unstableFilterAttributeValue`
    // function in `packages/format-library/src/text-color/index.js`.
    // @todo find a less-hacky way of solving this.

    const hasRelevantInitFormat = record.current?.formats[0]?.[0]?.type === 'core/text-color';

    if (hasRelevantInitFormat) {
      handleChangesUponInit(record.current);
    }
  } else if (selectionStart !== record.current.start || selectionEnd !== record.current.end) {
    hadSelectionUpdate.current = isSelected;
    record.current = { ...record.current,
      start: selectionStart,
      end: selectionEnd
    };
  }
  /**
   * Sync the value to global state. The node tree and selection will also be
   * updated if differences are found.
   *
   * @param {Object} newRecord The record to sync and apply.
   */


  function handleChange(newRecord) {
    record.current = newRecord;
    applyRecord(newRecord);

    if (disableFormats) {
      _value.current = newRecord.text;
    } else {
      _value.current = toHTMLString({
        value: __unstableBeforeSerialize ? { ...newRecord,
          formats: __unstableBeforeSerialize(newRecord)
        } : newRecord,
        multilineTag,
        preserveWhiteSpace
      });
    }

    const {
      start,
      end,
      formats,
      text
    } = newRecord; // Selection must be updated first, so it is recorded in history when
    // the content change happens.
    // We batch both calls to only attempt to rerender once.

    registry.batch(() => {
      onSelectionChange(start, end);
      onChange(_value.current, {
        __unstableFormats: formats,
        __unstableText: text
      });
    });
    forceRender();
  }

  function handleChangesUponInit(newRecord) {
    record.current = newRecord;
    _value.current = toHTMLString({
      value: __unstableBeforeSerialize ? { ...newRecord,
        formats: __unstableBeforeSerialize(newRecord)
      } : newRecord,
      multilineTag,
      preserveWhiteSpace
    });
    const {
      formats,
      text
    } = newRecord;
    registry.batch(() => {
      onChange(_value.current, {
        __unstableFormats: formats,
        __unstableText: text
      });
    });
    forceRender();
  }

  function applyFromProps() {
    setRecordFromProps();
    applyRecord(record.current);
  }

  const didMount = (0,external_wp_element_namespaceObject.useRef)(false); // Value updates must happen synchonously to avoid overwriting newer values.

  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    if (didMount.current && value !== _value.current) {
      applyFromProps();
      forceRender();
    }
  }, [value]); // Value updates must happen synchonously to avoid overwriting newer values.

  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    if (!hadSelectionUpdate.current) {
      return;
    }

    if (ref.current.ownerDocument.activeElement !== ref.current) {
      ref.current.focus();
    }

    applyFromProps();
    hadSelectionUpdate.current = false;
  }, [hadSelectionUpdate.current]);
  const mergedRefs = (0,external_wp_compose_namespaceObject.useMergeRefs)([ref, useDefaultStyle(), useBoundaryStyle({
    record
  }), useCopyHandler({
    record,
    multilineTag,
    preserveWhiteSpace
  }), useSelectObject(), useFormatBoundaries({
    record,
    applyRecord
  }), useDelete({
    createRecord,
    handleChange,
    multilineTag
  }), useInputAndSelection({
    record,
    applyRecord,
    createRecord,
    handleChange,
    isSelected,
    onSelectionChange
  }), useSelectionChangeCompat(), (0,external_wp_compose_namespaceObject.useRefEffect)(() => {
    applyFromProps();
    didMount.current = true;
  }, [placeholder, ...__unstableDependencies])]);
  return {
    value: record.current,
    // A function to get the most recent value so event handlers in
    // useRichText implementations have access to it. For example when
    // listening to input events, we internally update the state, but this
    // state is not yet available to the input event handler because React
    // may re-render asynchronously.
    getValue: () => record.current,
    onChange: handleChange,
    ref: mergedRefs
  };
}
function __experimentalRichText() {}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/format-edit.js


/**
 * Internal dependencies
 */


function FormatEdit({
  formatTypes,
  onChange,
  onFocus,
  value,
  forwardedRef
}) {
  return formatTypes.map(settings => {
    const {
      name,
      edit: Edit
    } = settings;

    if (!Edit) {
      return null;
    }

    const activeFormat = getActiveFormat(value, name);
    const isActive = activeFormat !== undefined;
    const activeObject = getActiveObject(value);
    const isObjectActive = activeObject !== undefined && activeObject.type === name;
    return (0,external_wp_element_namespaceObject.createElement)(Edit, {
      key: name,
      isActive: isActive,
      activeAttributes: isActive ? activeFormat.attributes || {} : {},
      isObjectActive: isObjectActive,
      activeObjectAttributes: isObjectActive ? activeObject.attributes || {} : {},
      value: value,
      onChange: onChange,
      onFocus: onFocus,
      contentRef: forwardedRef
    });
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/index.js
=======
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9






























<<<<<<< HEAD
/**
 * An object which represents a formatted string. See main `@wordpress/rich-text`
 * documentation for more information.
 */

(window.wp = window.wp || {}).richText = __webpack_exports__;
/******/ })()
;
=======

/***/ })

/******/ });
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
