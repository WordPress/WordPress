/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  ShortcutProvider: function() { return /* reexport */ ShortcutProvider; },
  __unstableUseShortcutEventMatch: function() { return /* reexport */ useShortcutEventMatch; },
  store: function() { return /* reexport */ store; },
  useShortcut: function() { return /* reexport */ useShortcut; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  registerShortcut: function() { return registerShortcut; },
  unregisterShortcut: function() { return unregisterShortcut; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  getAllShortcutKeyCombinations: function() { return getAllShortcutKeyCombinations; },
  getAllShortcutRawKeyCombinations: function() { return getAllShortcutRawKeyCombinations; },
  getCategoryShortcuts: function() { return getCategoryShortcuts; },
  getShortcutAliases: function() { return getShortcutAliases; },
  getShortcutDescription: function() { return getShortcutDescription; },
  getShortcutKeyCombination: function() { return getShortcutKeyCombination; },
  getShortcutRepresentation: function() { return getShortcutRepresentation; }
});

;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/reducer.js
/**
 * Reducer returning the registered shortcuts
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
function reducer(state = {}, action) {
  switch (action.type) {
    case 'REGISTER_SHORTCUT':
      return {
        ...state,
        [action.name]: {
          category: action.category,
          keyCombination: action.keyCombination,
          aliases: action.aliases,
          description: action.description
        }
      };
    case 'UNREGISTER_SHORTCUT':
      const {
        [action.name]: actionName,
        ...remainingState
      } = state;
      return remainingState;
  }
  return state;
}
/* harmony default export */ var store_reducer = (reducer);

;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/actions.js
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
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect, useDispatch } from '@wordpress/data';
 * import { useEffect } from '@wordpress/element';
 * import { __ } from '@wordpress/i18n';
 *
 * const ExampleComponent = () => {
 *     const { registerShortcut } = useDispatch( keyboardShortcutsStore );
 *
 *     useEffect( () => {
 *         registerShortcut( {
 *             name: 'custom/my-custom-shortcut',
 *             category: 'my-category',
 *             description: __( 'My custom shortcut' ),
 *             keyCombination: {
 *                 modifier: 'primary',
 *                 character: 'j',
 *             },
 *         } );
 *     }, [] );
 *
 *     const shortcut = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getShortcutKeyCombination(
 *                 'custom/my-custom-shortcut'
 *             ),
 *         []
 *     );
 *
 *     return shortcut ? (
 *         <p>{ __( 'Shortcut is registered.' ) }</p>
 *     ) : (
 *         <p>{ __( 'Shortcut is not registered.' ) }</p>
 *     );
 * };
 *```
 * @return {Object} action.
 */
function registerShortcut({
  name,
  category,
  description,
  keyCombination,
  aliases
}) {
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
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect, useDispatch } from '@wordpress/data';
 * import { useEffect } from '@wordpress/element';
 * import { __ } from '@wordpress/i18n';
 *
 * const ExampleComponent = () => {
 *     const { unregisterShortcut } = useDispatch( keyboardShortcutsStore );
 *
 *     useEffect( () => {
 *         unregisterShortcut( 'core/edit-post/next-region' );
 *     }, [] );
 *
 *     const shortcut = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getShortcutKeyCombination(
 *                 'core/edit-post/next-region'
 *             ),
 *         []
 *     );
 *
 *     return shortcut ? (
 *         <p>{ __( 'Shortcut is not unregistered.' ) }</p>
 *     ) : (
 *         <p>{ __( 'Shortcut is unregistered.' ) }</p>
 *     );
 * };
 *```
 * @return {Object} action.
 */
function unregisterShortcut(name) {
  return {
    type: 'UNREGISTER_SHORTCUT',
    name
  };
}

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

/**
 * Returns the first argument as the sole entry in an array.
 *
 * @template T
 *
 * @param {T} value Value to return.
 *
 * @return {[T]} Value returned as entry in array.
 */
function arrayOf(value) {
	return [value];
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
function isObjectLike(value) {
	return !!value && 'object' === typeof value;
}

/**
 * Creates and returns a new cache object.
 *
 * @return {Cache} Cache object.
 */
function createCache() {
	/** @type {Cache} */
	var cache = {
		clear: function () {
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
 * @param {*[]} a First array.
 * @param {*[]} b Second array.
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
function isShallowEqual(a, b, fromIndex) {
	var i;

	if (a.length !== b.length) {
		return false;
	}

	for (i = fromIndex; i < a.length; i++) {
		if (a[i] !== b[i]) {
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
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
			if (caches.has(dependant)) {
				// Traverse into nested WeakMap.
				caches = caches.get(dependant);
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set(dependant, map);
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
		if (!caches.has(LEAF_KEY)) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set(LEAF_KEY, cache);
		}

		return caches.get(LEAF_KEY);
	}

	/**
	 * Resets root memoization cache.
	 */
	function clear() {
		rootCache = new WeakMap();
	}

	/* eslint-disable jsdoc/check-param-names */
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
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
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
		while (node) {
			// Check whether node arguments match arguments
			if (!isShallowEqual(node.args, args, 1)) {
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if (node !== cache.head) {
				// Adjust siblings to point to each other.
				/** @type {CacheNode} */ (node.prev).next = node.next;
				if (node.next) {
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
				/** @type {CacheNode} */ (cache.head).prev = node;
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		node = /** @type {CacheNode} */ ({
			// Generate the result from original function
			val: selector.apply(null, args),
		});

		// Avoid including the source object in the cache.
		args[0] = null;
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if (cache.head) {
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

	callSelector.getDependants = normalizedGetDependants;
	callSelector.clear = clear;
	clear();

	return /** @type {S & EnhancedSelector} */ (callSelector);
}

;// CONCATENATED MODULE: external ["wp","keycodes"]
var external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/selectors.js
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
  display: external_wp_keycodes_namespaceObject.displayShortcut,
  raw: external_wp_keycodes_namespaceObject.rawShortcut,
  ariaLabel: external_wp_keycodes_namespaceObject.shortcutAriaLabel
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
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 * import { createInterpolateElement } from '@wordpress/element';
 * import { sprintf } from '@wordpress/i18n';
 * const ExampleComponent = () => {
 *     const {character, modifier} = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getShortcutKeyCombination(
 *                 'core/edit-post/next-region'
 *             ),
 *         []
 *     );
 *
 *     return (
 *         <div>
 *             { createInterpolateElement(
 *                 sprintf(
 *                     'Character: <code>%s</code> / Modifier: <code>%s</code>',
 *                     character,
 *                     modifier
 *                 ),
 *                 {
 *                     code: <code />,
 *                 }
 *             ) }
 *         </div>
 *     );
 * };
 *```
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
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 * import { sprintf } from '@wordpress/i18n';
 *
 * const ExampleComponent = () => {
 *     const {display, raw, ariaLabel} = useSelect(
 *         ( select ) =>{
 *             return {
 *                 display: select( keyboardShortcutsStore ).getShortcutRepresentation('core/edit-post/next-region' ),
 *                 raw: select( keyboardShortcutsStore ).getShortcutRepresentation('core/edit-post/next-region','raw' ),
 *                 ariaLabel: select( keyboardShortcutsStore ).getShortcutRepresentation('core/edit-post/next-region', 'ariaLabel')
 *             }
 *         },
 *         []
 *     );
 *
 *     return (
 *         <ul>
 *             <li>{ sprintf( 'display string: %s', display ) }</li>
 *             <li>{ sprintf( 'raw string: %s', raw ) }</li>
 *             <li>{ sprintf( 'ariaLabel string: %s', ariaLabel ) }</li>
 *         </ul>
 *     );
 * };
 *```
 *
 * @return {string?} Shortcut representation.
 */
function getShortcutRepresentation(state, name, representation = 'display') {
  const shortcut = getShortcutKeyCombination(state, name);
  return getKeyCombinationRepresentation(shortcut, representation);
}

/**
 * Returns the shortcut description given its name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Shortcut name.
 *
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 * import { __ } from '@wordpress/i18n';
 * const ExampleComponent = () => {
 *     const shortcutDescription = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getShortcutDescription( 'core/edit-post/next-region' ),
 *         []
 *     );
 *
 *     return shortcutDescription ? (
 *         <div>{ shortcutDescription }</div>
 *     ) : (
 *         <div>{ __( 'No description.' ) }</div>
 *     );
 * };
 *```
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
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 * import { createInterpolateElement } from '@wordpress/element';
 * import { sprintf } from '@wordpress/i18n';
 * const ExampleComponent = () => {
 *     const shortcutAliases = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getShortcutAliases(
 *                 'core/edit-post/next-region'
 *             ),
 *         []
 *     );
 *
 *     return (
 *         shortcutAliases.length > 0 && (
 *             <ul>
 *                 { shortcutAliases.map( ( { character, modifier }, index ) => (
 *                     <li key={ index }>
 *                         { createInterpolateElement(
 *                             sprintf(
 *                                 'Character: <code>%s</code> / Modifier: <code>%s</code>',
 *                                 character,
 *                                 modifier
 *                             ),
 *                             {
 *                                 code: <code />,
 *                             }
 *                         ) }
 *                     </li>
 *                 ) ) }
 *             </ul>
 *         )
 *     );
 * };
 *```
 *
 * @return {WPShortcutKeyCombination[]} Key combinations.
 */
function getShortcutAliases(state, name) {
  return state[name] && state[name].aliases ? state[name].aliases : EMPTY_ARRAY;
}

/**
 * Returns the shortcuts that include aliases for a given shortcut name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Shortcut name.
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 * import { createInterpolateElement } from '@wordpress/element';
 * import { sprintf } from '@wordpress/i18n';
 *
 * const ExampleComponent = () => {
 *     const allShortcutKeyCombinations = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getAllShortcutKeyCombinations(
 *                 'core/edit-post/next-region'
 *             ),
 *         []
 *     );
 *
 *     return (
 *         allShortcutKeyCombinations.length > 0 && (
 *             <ul>
 *                 { allShortcutKeyCombinations.map(
 *                     ( { character, modifier }, index ) => (
 *                         <li key={ index }>
 *                             { createInterpolateElement(
 *                                 sprintf(
 *                                     'Character: <code>%s</code> / Modifier: <code>%s</code>',
 *                                     character,
 *                                     modifier
 *                                 ),
 *                                 {
 *                                     code: <code />,
 *                                 }
 *                             ) }
 *                         </li>
 *                     )
 *                 ) }
 *             </ul>
 *         )
 *     );
 * };
 *```
 *
 * @return {WPShortcutKeyCombination[]} Key combinations.
 */
const getAllShortcutKeyCombinations = rememo((state, name) => {
  return [getShortcutKeyCombination(state, name), ...getShortcutAliases(state, name)].filter(Boolean);
}, (state, name) => [state[name]]);

/**
 * Returns the raw representation of all the keyboard combinations of a given shortcut name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Shortcut name.
 *
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 * import { createInterpolateElement } from '@wordpress/element';
 * import { sprintf } from '@wordpress/i18n';
 *
 * const ExampleComponent = () => {
 *     const allShortcutRawKeyCombinations = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getAllShortcutRawKeyCombinations(
 *                 'core/edit-post/next-region'
 *             ),
 *         []
 *     );
 *
 *     return (
 *         allShortcutRawKeyCombinations.length > 0 && (
 *             <ul>
 *                 { allShortcutRawKeyCombinations.map(
 *                     ( shortcutRawKeyCombination, index ) => (
 *                         <li key={ index }>
 *                             { createInterpolateElement(
 *                                 sprintf(
 *                                     ' <code>%s</code>',
 *                                     shortcutRawKeyCombination
 *                                 ),
 *                                 {
 *                                     code: <code />,
 *                                 }
 *                             ) }
 *                         </li>
 *                     )
 *                 ) }
 *             </ul>
 *         )
 *     );
 * };
 *```
 *
 * @return {string[]} Shortcuts.
 */
const getAllShortcutRawKeyCombinations = rememo((state, name) => {
  return getAllShortcutKeyCombinations(state, name).map(combination => getKeyCombinationRepresentation(combination, 'raw'));
}, (state, name) => [state[name]]);

/**
 * Returns the shortcut names list for a given category name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Category name.
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 *
 * const ExampleComponent = () => {
 *     const categoryShortcuts = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getCategoryShortcuts(
 *                 'block'
 *             ),
 *         []
 *     );
 *
 *     return (
 *         categoryShortcuts.length > 0 && (
 *             <ul>
 *                 { categoryShortcuts.map( ( categoryShortcut ) => (
 *                     <li key={ categoryShortcut }>{ categoryShortcut }</li>
 *                 ) ) }
 *             </ul>
 *         )
 *     );
 * };
 *```
 * @return {string[]} Shortcut names.
 */
const getCategoryShortcuts = rememo((state, categoryName) => {
  return Object.entries(state).filter(([, shortcut]) => shortcut.category === categoryName).map(([name]) => name);
}, state => [state]);

;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/index.js
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
const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: store_reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/hooks/use-shortcut-event-match.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Returns a function to check if a keyboard event matches a shortcut name.
 *
 * @return {Function} A function to check if a keyboard event matches a
 *                    predefined shortcut combination.
 */
function useShortcutEventMatch() {
  const {
    getAllShortcutKeyCombinations
  } = (0,external_wp_data_namespaceObject.useSelect)(store);

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
    return getAllShortcutKeyCombinations(name).some(({
      modifier,
      character
    }) => {
      return external_wp_keycodes_namespaceObject.isKeyboardEvent[modifier](event, character);
    });
  }
  return isMatch;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/context.js
/**
 * WordPress dependencies
 */

const globalShortcuts = new Set();
const globalListener = event => {
  for (const keyboardShortcut of globalShortcuts) {
    keyboardShortcut(event);
  }
};
const context = (0,external_wp_element_namespaceObject.createContext)({
  add: shortcut => {
    if (globalShortcuts.size === 0) {
      document.addEventListener('keydown', globalListener);
    }
    globalShortcuts.add(shortcut);
  },
  delete: shortcut => {
    globalShortcuts.delete(shortcut);
    if (globalShortcuts.size === 0) {
      document.removeEventListener('keydown', globalListener);
    }
  }
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/hooks/use-shortcut.js
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
function useShortcut(name, callback, {
  isDisabled = false
} = {}) {
  const shortcuts = (0,external_wp_element_namespaceObject.useContext)(context);
  const isMatch = useShortcutEventMatch();
  const callbackRef = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    callbackRef.current = callback;
  }, [callback]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isDisabled) {
      return;
    }
    function _callback(event) {
      if (isMatch(name, event)) {
        callbackRef.current(event);
      }
    }
    shortcuts.add(_callback);
    return () => {
      shortcuts.delete(_callback);
    };
  }, [name, isDisabled, shortcuts]);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/components/shortcut-provider.js

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
 * Adding a provider allows to register contextual shortcuts
 * that are only active when a certain part of the UI is focused.
 *
 * @param {Object} props Props to pass to `div`.
 *
 * @return {import('@wordpress/element').WPElement} Component.
 */
function ShortcutProvider(props) {
  const [keyboardShortcuts] = (0,external_wp_element_namespaceObject.useState)(() => new Set());
  function onKeyDown(event) {
    if (props.onKeyDown) props.onKeyDown(event);
    for (const keyboardShortcut of keyboardShortcuts) {
      keyboardShortcut(event);
    }
  }

  /* eslint-disable jsx-a11y/no-static-element-interactions */
  return (0,external_wp_element_namespaceObject.createElement)(Provider, {
    value: keyboardShortcuts
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    ...props,
    onKeyDown: onKeyDown
  }));
  /* eslint-enable jsx-a11y/no-static-element-interactions */
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/index.js





(window.wp = window.wp || {}).keyboardShortcuts = __webpack_exports__;
/******/ })()
;