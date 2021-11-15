this["wp"] = this["wp"] || {}; this["wp"]["keyboardShortcuts"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "cFS5");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "RxS6":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["keycodes"]; }());

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "cFS5":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "store", function() { return /* reexport */ store; });
__webpack_require__.d(__webpack_exports__, "useShortcut", function() { return /* reexport */ useShortcut; });
__webpack_require__.d(__webpack_exports__, "ShortcutProvider", function() { return /* reexport */ ShortcutProvider; });
__webpack_require__.d(__webpack_exports__, "__unstableUseShortcutEventMatch", function() { return /* reexport */ useShortcutEventMatch; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "registerShortcut", function() { return registerShortcut; });
__webpack_require__.d(actions_namespaceObject, "unregisterShortcut", function() { return unregisterShortcut; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getShortcutKeyCombination", function() { return getShortcutKeyCombination; });
__webpack_require__.d(selectors_namespaceObject, "getShortcutRepresentation", function() { return getShortcutRepresentation; });
__webpack_require__.d(selectors_namespaceObject, "getShortcutDescription", function() { return getShortcutDescription; });
__webpack_require__.d(selectors_namespaceObject, "getShortcutAliases", function() { return getShortcutAliases; });
__webpack_require__.d(selectors_namespaceObject, "getAllShortcutKeyCombinations", function() { return selectors_getAllShortcutKeyCombinations; });
__webpack_require__.d(selectors_namespaceObject, "getAllShortcutRawKeyCombinations", function() { return getAllShortcutRawKeyCombinations; });
__webpack_require__.d(selectors_namespaceObject, "getCategoryShortcuts", function() { return getCategoryShortcuts; });

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/reducer.js
/**
 * External dependencies
 */

/**
 * Reducer returning the registered shortcuts
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function reducer() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'REGISTER_SHORTCUT':
      return { ...state,
        [action.name]: {
          category: action.category,
          keyCombination: action.keyCombination,
          aliases: action.aliases,
          description: action.description
        }
      };

    case 'UNREGISTER_SHORTCUT':
      return Object(external_lodash_["omit"])(state, action.name);
  }

  return state;
}

/* harmony default export */ var store_reducer = (reducer);

// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/actions.js
/** @typedef {import('@wordpress/keycodes').WPKeycodeModifier} WPKeycodeModifier */

/**
 * Keyboard key combination.
 *
 * @typedef {Object} WPShortcutKeyCombination
 *
 * @property {string}                      character Character.
 * @property {WPKeycodeModifier|undefined} modifier  Modifier.
 */

/**
 * Configuration of a registered keyboard shortcut.
 *
 * @typedef {Object} WPShortcutConfig
 *
 * @property {string}                     name           Shortcut name.
 * @property {string}                     category       Shortcut category.
 * @property {string}                     description    Shortcut description.
 * @property {WPShortcutKeyCombination}   keyCombination Shortcut key combination.
 * @property {WPShortcutKeyCombination[]} [aliases]      Shortcut aliases.
 */

/**
 * Returns an action object used to register a new keyboard shortcut.
 *
 * @param {WPShortcutConfig} config Shortcut config.
 *
 * @return {Object} action.
 */
function registerShortcut(_ref) {
  let {
    name,
    category,
    description,
    keyCombination,
    aliases
  } = _ref;
  return {
    type: 'REGISTER_SHORTCUT',
    name,
    category,
    keyCombination,
    aliases,
    description
  };
}
/**
 * Returns an action object used to unregister a keyboard shortcut.
 *
 * @param {string} name Shortcut name.
 *
 * @return {Object} action.
 */

function unregisterShortcut(name) {
  return {
    type: 'UNREGISTER_SHORTCUT',
    name
  };
}

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__("pPDe");

// EXTERNAL MODULE: external ["wp","keycodes"]
var external_wp_keycodes_ = __webpack_require__("RxS6");

// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/selectors.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/** @typedef {import('./actions').WPShortcutKeyCombination} WPShortcutKeyCombination */

/** @typedef {import('@wordpress/keycodes').WPKeycodeHandlerByModifier} WPKeycodeHandlerByModifier */

/**
 * Shared reference to an empty array for cases where it is important to avoid
 * returning a new array reference on every invocation.
 *
 * @type {Array<any>}
 */

const EMPTY_ARRAY = [];
/**
 * Shortcut formatting methods.
 *
 * @property {WPKeycodeHandlerByModifier} display     Display formatting.
 * @property {WPKeycodeHandlerByModifier} rawShortcut Raw shortcut formatting.
 * @property {WPKeycodeHandlerByModifier} ariaLabel   ARIA label formatting.
 */

const FORMATTING_METHODS = {
  display: external_wp_keycodes_["displayShortcut"],
  raw: external_wp_keycodes_["rawShortcut"],
  ariaLabel: external_wp_keycodes_["shortcutAriaLabel"]
};
/**
 * Returns a string representing the key combination.
 *
 * @param {?WPShortcutKeyCombination} shortcut       Key combination.
 * @param {keyof FORMATTING_METHODS}  representation Type of representation
 *                                                   (display, raw, ariaLabel).
 *
 * @return {string?} Shortcut representation.
 */

function getKeyCombinationRepresentation(shortcut, representation) {
  if (!shortcut) {
    return null;
  }

  return shortcut.modifier ? FORMATTING_METHODS[representation][shortcut.modifier](shortcut.character) : shortcut.character;
}
/**
 * Returns the main key combination for a given shortcut name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Shortcut name.
 *
 * @return {WPShortcutKeyCombination?} Key combination.
 */


function getShortcutKeyCombination(state, name) {
  return state[name] ? state[name].keyCombination : null;
}
/**
 * Returns a string representing the main key combination for a given shortcut name.
 *
 * @param {Object}                   state          Global state.
 * @param {string}                   name           Shortcut name.
 * @param {keyof FORMATTING_METHODS} representation Type of representation
 *                                                  (display, raw, ariaLabel).
 *
 * @return {string?} Shortcut representation.
 */

function getShortcutRepresentation(state, name) {
  let representation = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'display';
  const shortcut = getShortcutKeyCombination(state, name);
  return getKeyCombinationRepresentation(shortcut, representation);
}
/**
 * Returns the shortcut description given its name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Shortcut name.
 *
 * @return {string?} Shortcut description.
 */

function getShortcutDescription(state, name) {
  return state[name] ? state[name].description : null;
}
/**
 * Returns the aliases for a given shortcut name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Shortcut name.
 *
 * @return {WPShortcutKeyCombination[]} Key combinations.
 */

function getShortcutAliases(state, name) {
  return state[name] && state[name].aliases ? state[name].aliases : EMPTY_ARRAY;
}
const selectors_getAllShortcutKeyCombinations = Object(rememo["a" /* default */])((state, name) => {
  return Object(external_lodash_["compact"])([getShortcutKeyCombination(state, name), ...getShortcutAliases(state, name)]);
}, (state, name) => [state[name]]);
/**
 * Returns the raw representation of all the keyboard combinations of a given shortcut name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Shortcut name.
 *
 * @return {string[]} Shortcuts.
 */

const getAllShortcutRawKeyCombinations = Object(rememo["a" /* default */])((state, name) => {
  return selectors_getAllShortcutKeyCombinations(state, name).map(combination => getKeyCombinationRepresentation(combination, 'raw'));
}, (state, name) => [state[name]]);
/**
 * Returns the shortcut names list for a given category name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Category name.
 *
 * @return {string[]} Shortcut names.
 */

const getCategoryShortcuts = Object(rememo["a" /* default */])((state, categoryName) => {
  return Object.entries(state).filter(_ref => {
    let [, shortcut] = _ref;
    return shortcut.category === categoryName;
  }).map(_ref2 => {
    let [name] = _ref2;
    return name;
  });
}, state => [state]);

// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




const STORE_NAME = 'core/keyboard-shortcuts';
/**
 * Store definition for the keyboard shortcuts namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = Object(external_wp_data_["createReduxStore"])(STORE_NAME, {
  reducer: store_reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});
Object(external_wp_data_["register"])(store);

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/hooks/use-shortcut-event-match.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Returns a function to check if a keyboard event matches a shortcut name.
 *
 * @return {Function} A function to to check if a keyboard event matches a
 *                    predefined shortcut combination.
 */

function useShortcutEventMatch() {
  const {
    getAllShortcutKeyCombinations
  } = Object(external_wp_data_["useSelect"])(store);
  /**
   * A function to check if a keyboard event matches a predefined shortcut
   * combination.
   *
   * @param {string}        name  Shortcut name.
   * @param {KeyboardEvent} event Event to check.
   *
   * @return {boolean} True if the event matches any shortcuts, false if not.
   */

  function isMatch(name, event) {
    return getAllShortcutKeyCombinations(name).some(_ref => {
      let {
        modifier,
        character
      } = _ref;
      return external_wp_keycodes_["isKeyboardEvent"][modifier](event, character);
    });
  }

  return isMatch;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/context.js
/**
 * WordPress dependencies
 */

const context = Object(external_wp_element_["createContext"])();

// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/hooks/use-shortcut.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



/**
 * Attach a keyboard shortcut handler.
 *
 * @param {string}   name               Shortcut name.
 * @param {Function} callback           Shortcut callback.
 * @param {Object}   options            Shortcut options.
 * @param {boolean}  options.isDisabled Whether to disable to shortut.
 */

function useShortcut(name, callback) {
  let {
    isDisabled
  } = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  const shortcuts = Object(external_wp_element_["useContext"])(context);
  const isMatch = useShortcutEventMatch();
  const callbackRef = Object(external_wp_element_["useRef"])();
  callbackRef.current = callback;
  Object(external_wp_element_["useEffect"])(() => {
    if (isDisabled) {
      return;
    }

    function _callback(event) {
      if (isMatch(name, event)) {
        callbackRef.current(event);
      }
    }

    shortcuts.current.add(_callback);
    return () => {
      shortcuts.current.delete(_callback);
    };
  }, [name, isDisabled]);
}

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/components/shortcut-provider.js



/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


const {
  Provider
} = context;
/**
 * Handles callbacks added to context by `useShortcut`.
 *
 * @param {Object} props Props to pass to `div`.
 *
 * @return {import('@wordpress/element').WPElement} Component.
 */

function ShortcutProvider(props) {
  const keyboardShortcuts = Object(external_wp_element_["useRef"])(new Set());

  function onKeyDown(event) {
    if (props.onKeyDown) props.onKeyDown(event);

    for (const keyboardShortcut of keyboardShortcuts.current) {
      keyboardShortcut(event);
    }
  }
  /* eslint-disable jsx-a11y/no-static-element-interactions */


  return Object(external_wp_element_["createElement"])(Provider, {
    value: keyboardShortcuts
  }, Object(external_wp_element_["createElement"])("div", Object(esm_extends["a" /* default */])({}, props, {
    onKeyDown: onKeyDown
  })));
  /* eslint-enable jsx-a11y/no-static-element-interactions */
}

// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/index.js






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

/***/ })

/******/ });