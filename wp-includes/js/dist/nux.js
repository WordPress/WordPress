this["wp"] = this["wp"] || {}; this["wp"]["nux"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "51Wn");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "51Wn":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "store", function() { return /* reexport */ store; });
__webpack_require__.d(__webpack_exports__, "DotTip", function() { return /* reexport */ dot_tip; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/nux/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "triggerGuide", function() { return triggerGuide; });
__webpack_require__.d(actions_namespaceObject, "dismissTip", function() { return dismissTip; });
__webpack_require__.d(actions_namespaceObject, "disableTips", function() { return disableTips; });
__webpack_require__.d(actions_namespaceObject, "enableTips", function() { return enableTips; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/nux/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getAssociatedGuide", function() { return getAssociatedGuide; });
__webpack_require__.d(selectors_namespaceObject, "isTipVisible", function() { return isTipVisible; });
__webpack_require__.d(selectors_namespaceObject, "areTipsEnabled", function() { return selectors_areTipsEnabled; });

// EXTERNAL MODULE: external ["wp","deprecated"]
var external_wp_deprecated_ = __webpack_require__("NMb1");
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_);

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/store/reducer.js
/**
 * WordPress dependencies
 */

/**
 * Reducer that tracks which tips are in a guide. Each guide is represented by
 * an array which contains the tip identifiers contained within that guide.
 *
 * @param {Array} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Array} Updated state.
 */

function guides(state = [], action) {
  switch (action.type) {
    case 'TRIGGER_GUIDE':
      return [...state, action.tipIds];
  }

  return state;
}
/**
 * Reducer that tracks whether or not tips are globally enabled.
 *
 * @param {boolean} state Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {boolean} Updated state.
 */

function areTipsEnabled(state = true, action) {
  switch (action.type) {
    case 'DISABLE_TIPS':
      return false;

    case 'ENABLE_TIPS':
      return true;
  }

  return state;
}
/**
 * Reducer that tracks which tips have been dismissed. If the state object
 * contains a tip identifier, then that tip is dismissed.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function dismissedTips(state = {}, action) {
  switch (action.type) {
    case 'DISMISS_TIP':
      return { ...state,
        [action.id]: true
      };

    case 'ENABLE_TIPS':
      return {};
  }

  return state;
}
const preferences = Object(external_wp_data_["combineReducers"])({
  areTipsEnabled,
  dismissedTips
});
/* harmony default export */ var reducer = (Object(external_wp_data_["combineReducers"])({
  guides,
  preferences
}));

// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/store/actions.js
/**
 * Returns an action object that, when dispatched, presents a guide that takes
 * the user through a series of tips step by step.
 *
 * @param {string[]} tipIds Which tips to show in the guide.
 *
 * @return {Object} Action object.
 */
function triggerGuide(tipIds) {
  return {
    type: 'TRIGGER_GUIDE',
    tipIds
  };
}
/**
 * Returns an action object that, when dispatched, dismisses the given tip. A
 * dismissed tip will not show again.
 *
 * @param {string} id The tip to dismiss.
 *
 * @return {Object} Action object.
 */

function dismissTip(id) {
  return {
    type: 'DISMISS_TIP',
    id
  };
}
/**
 * Returns an action object that, when dispatched, prevents all tips from
 * showing again.
 *
 * @return {Object} Action object.
 */

function disableTips() {
  return {
    type: 'DISABLE_TIPS'
  };
}
/**
 * Returns an action object that, when dispatched, makes all tips show again.
 *
 * @return {Object} Action object.
 */

function enableTips() {
  return {
    type: 'ENABLE_TIPS'
  };
}

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__("pPDe");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/store/selectors.js
/**
 * External dependencies
 */


/**
 * An object containing information about a guide.
 *
 * @typedef {Object} NUXGuideInfo
 * @property {string[]} tipIds       Which tips the guide contains.
 * @property {?string}  currentTipId The guide's currently showing tip.
 * @property {?string}  nextTipId    The guide's next tip to show.
 */

/**
 * Returns an object describing the guide, if any, that the given tip is a part
 * of.
 *
 * @param {Object} state Global application state.
 * @param {string} tipId The tip to query.
 *
 * @return {?NUXGuideInfo} Information about the associated guide.
 */

const getAssociatedGuide = Object(rememo["a" /* default */])((state, tipId) => {
  for (const tipIds of state.guides) {
    if (Object(external_lodash_["includes"])(tipIds, tipId)) {
      const nonDismissedTips = Object(external_lodash_["difference"])(tipIds, Object(external_lodash_["keys"])(state.preferences.dismissedTips));
      const [currentTipId = null, nextTipId = null] = nonDismissedTips;
      return {
        tipIds,
        currentTipId,
        nextTipId
      };
    }
  }

  return null;
}, state => [state.guides, state.preferences.dismissedTips]);
/**
 * Determines whether or not the given tip is showing. Tips are hidden if they
 * are disabled, have been dismissed, or are not the current tip in any
 * guide that they have been added to.
 *
 * @param {Object} state Global application state.
 * @param {string} tipId The tip to query.
 *
 * @return {boolean} Whether or not the given tip is showing.
 */

function isTipVisible(state, tipId) {
  if (!state.preferences.areTipsEnabled) {
    return false;
  }

  if (Object(external_lodash_["has"])(state.preferences.dismissedTips, [tipId])) {
    return false;
  }

  const associatedGuide = getAssociatedGuide(state, tipId);

  if (associatedGuide && associatedGuide.currentTipId !== tipId) {
    return false;
  }

  return true;
}
/**
 * Returns whether or not tips are globally enabled.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether tips are globally enabled.
 */

function selectors_areTipsEnabled(state) {
  return state.preferences.areTipsEnabled;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




const STORE_NAME = 'core/nux';
/**
 * Store definition for the nux namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = Object(external_wp_data_["createReduxStore"])(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['preferences']
}); // Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.

Object(external_wp_data_["registerStore"])(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['preferences']
});

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external ["wp","compose"]
var external_wp_compose_ = __webpack_require__("K9lf");

// EXTERNAL MODULE: external ["wp","components"]
var external_wp_components_ = __webpack_require__("tI+e");

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/close.js
var library_close = __webpack_require__("w95h");

// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/components/dot-tip/index.js


/**
 * WordPress dependencies
 */







function onClick(event) {
  // Tips are often nested within buttons. We stop propagation so that clicking
  // on a tip doesn't result in the button being clicked.
  event.stopPropagation();
}

function DotTip({
  position = 'middle right',
  children,
  isVisible,
  hasNextTip,
  onDismiss,
  onDisable
}) {
  const anchorParent = Object(external_wp_element_["useRef"])(null);
  const onFocusOutsideCallback = Object(external_wp_element_["useCallback"])(event => {
    if (!anchorParent.current) {
      return;
    }

    if (anchorParent.current.contains(event.relatedTarget)) {
      return;
    }

    onDisable();
  }, [onDisable, anchorParent]);

  if (!isVisible) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["Popover"], {
    className: "nux-dot-tip",
    position: position,
    noArrow: true,
    focusOnMount: "container",
    shouldAnchorIncludePadding: true,
    role: "dialog",
    "aria-label": Object(external_wp_i18n_["__"])('Editor tips'),
    onClick: onClick,
    onFocusOutside: onFocusOutsideCallback
  }, Object(external_wp_element_["createElement"])("p", null, children), Object(external_wp_element_["createElement"])("p", null, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    isLink: true,
    onClick: onDismiss
  }, hasNextTip ? Object(external_wp_i18n_["__"])('See next tip') : Object(external_wp_i18n_["__"])('Got it'))), Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    className: "nux-dot-tip__disable",
    icon: library_close["a" /* default */],
    label: Object(external_wp_i18n_["__"])('Disable tips'),
    onClick: onDisable
  }));
}
/* harmony default export */ var dot_tip = (Object(external_wp_compose_["compose"])(Object(external_wp_data_["withSelect"])((select, {
  tipId
}) => {
  const {
    isTipVisible,
    getAssociatedGuide
  } = select('core/nux');
  const associatedGuide = getAssociatedGuide(tipId);
  return {
    isVisible: isTipVisible(tipId),
    hasNextTip: !!(associatedGuide && associatedGuide.nextTipId)
  };
}), Object(external_wp_data_["withDispatch"])((dispatch, {
  tipId
}) => {
  const {
    dismissTip,
    disableTips
  } = dispatch('core/nux');
  return {
    onDismiss() {
      dismissTip(tipId);
    },

    onDisable() {
      disableTips();
    }

  };
}))(DotTip));

// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/index.js
/**
 * WordPress dependencies
 */



external_wp_deprecated_default()('wp.nux', {
  since: '5.4',
  hint: 'wp.components.Guide can be used to show a user guide.'
});


/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "K9lf":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["compose"]; }());

/***/ }),

/***/ "NMb1":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["deprecated"]; }());

/***/ }),

/***/ "Tqx9":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["primitives"]; }());

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

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

/***/ "tI+e":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["components"]; }());

/***/ }),

/***/ "w95h":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const close = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (close);


/***/ })

/******/ });