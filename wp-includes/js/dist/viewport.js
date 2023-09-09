<<<<<<< HEAD
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
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
=======
this["wp"] = this["wp"] || {}; this["wp"]["viewport"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "PR0u");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ "K9lf":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ "PR0u":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
<<<<<<< HEAD
__webpack_require__.d(__webpack_exports__, {
  ifViewportMatches: () => (/* reexport */ if_viewport_matches),
  store: () => (/* reexport */ store),
  withViewportMatch: () => (/* reexport */ with_viewport_match)
});
=======
__webpack_require__.d(__webpack_exports__, "ifViewportMatches", function() { return /* reexport */ if_viewport_matches; });
__webpack_require__.d(__webpack_exports__, "withViewportMatch", function() { return /* reexport */ with_viewport_match; });
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

// NAMESPACE OBJECT: ./node_modules/@wordpress/viewport/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
<<<<<<< HEAD
__webpack_require__.d(actions_namespaceObject, {
  setIsMatching: () => (setIsMatching)
});
=======
__webpack_require__.d(actions_namespaceObject, "setIsMatching", function() { return setIsMatching; });
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

// NAMESPACE OBJECT: ./node_modules/@wordpress/viewport/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
<<<<<<< HEAD
__webpack_require__.d(selectors_namespaceObject, {
  isViewportMatch: () => (isViewportMatch)
});

;// CONCATENATED MODULE: external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/store/reducer.js
=======
__webpack_require__.d(selectors_namespaceObject, "isViewportMatch", function() { return isViewportMatch; });

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__("1ZqX");

// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/store/reducer.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Reducer returning the viewport state, as keys of breakpoint queries with
 * boolean value representing whether query is matched.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
<<<<<<< HEAD
function reducer(state = {}, action) {
=======
function reducer() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  switch (action.type) {
    case 'SET_IS_MATCHING':
      return action.values;
  }

  return state;
}

<<<<<<< HEAD
/* harmony default export */ const store_reducer = (reducer);

;// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/store/actions.js
=======
/* harmony default export */ var store_reducer = (reducer);

// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/store/actions.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Returns an action object used in signalling that viewport queries have been
 * updated. Values are specified as an object of breakpoint query keys where
 * value represents whether query matches.
<<<<<<< HEAD
 * Ignored from documentation as it is for internal use only.
 *
 * @ignore
=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @param {Object} values Breakpoint query matches.
 *
 * @return {Object} Action object.
 */
function setIsMatching(values) {
  return {
    type: 'SET_IS_MATCHING',
<<<<<<< HEAD
    values
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/store/selectors.js
=======
    values: values
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/store/selectors.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Returns true if the viewport matches the given query, or false otherwise.
 *
 * @param {Object} state Viewport state object.
 * @param {string} query Query string. Includes operator and breakpoint name,
 *                       space separated. Operator defaults to >=.
 *
 * @example
 *
 * ```js
<<<<<<< HEAD
 * import { store as viewportStore } from '@wordpress/viewport';
 * import { useSelect } from '@wordpress/data';
 * import { __ } from '@wordpress/i18n';
 * const ExampleComponent = () => {
 *     const isMobile = useSelect(
 *         ( select ) => select( viewportStore ).isViewportMatch( '< small' ),
 *         []
 *     );
 *
 *     return isMobile ? (
 *         <div>{ __( 'Mobile' ) }</div>
 *     ) : (
 *         <div>{ __( 'Not Mobile' ) }</div>
 *     );
 * };
=======
 * isViewportMatch( state, '< huge' );
 * isViewPortMatch( state, 'medium' );
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 * ```
 *
 * @return {boolean} Whether viewport matches query.
 */
function isViewportMatch(state, query) {
  // Default to `>=` if no operator is present.
  if (query.indexOf(' ') === -1) {
    query = '>= ' + query;
  }

  return !!state[query];
}

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/store/index.js
=======
// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/store/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




<<<<<<< HEAD
const STORE_NAME = 'core/viewport';
/**
 * Store definition for the viewport namespace.
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

;// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/listener.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



const addDimensionsEventListener = (breakpoints, operators) => {
  /**
   * Callback invoked when media query state should be updated. Is invoked a
   * maximum of one time per call stack.
   */
  const setIsMatching = (0,external_wp_compose_namespaceObject.debounce)(() => {
    const values = Object.fromEntries(queries.map(([key, query]) => [key, query.matches]));
    (0,external_wp_data_namespaceObject.dispatch)(store).setIsMatching(values);
  }, 0, {
    leading: true
  });
  /**
   * Hash of breakpoint names with generated MediaQueryList for corresponding
   * media query.
   *
   * @see https://developer.mozilla.org/en-US/docs/Web/API/Window/matchMedia
   * @see https://developer.mozilla.org/en-US/docs/Web/API/MediaQueryList
   *
   * @type {Object<string,MediaQueryList>}
   */

  const operatorEntries = Object.entries(operators);
  const queries = Object.entries(breakpoints).flatMap(([name, width]) => {
    return operatorEntries.map(([operator, condition]) => {
      const list = window.matchMedia(`(${condition}: ${width}px)`);
      list.addEventListener('change', setIsMatching);
      return [`${operator} ${name}`, list];
    });
  });
  window.addEventListener('orientationchange', setIsMatching); // Set initial values.

  setIsMatching();
  setIsMatching.flush();
};

/* harmony default export */ const listener = (addDimensionsEventListener);

;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/with-viewport-match.js


=======
/* harmony default export */ var store = (Object(external_this_wp_data_["registerStore"])('core/viewport', {
  reducer: store_reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
}));

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__("K9lf");

// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/with-viewport-match.js
/**
 * External dependencies
 */

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

<<<<<<< HEAD
=======


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Higher-order component creator, creating a new component which renders with
 * the given prop names, where the value passed to the underlying component is
 * the result of the query assigned as the object's value.
 *
<<<<<<< HEAD
 * @see isViewportMatch
 *
 * @param {Object} queries Object of prop name to viewport query.
 *
 * @example
 *
 * ```jsx
 * function MyComponent( { isMobile } ) {
 * 	return (
 * 		<div>Currently: { isMobile ? 'Mobile' : 'Not Mobile' }</div>
 * 	);
 * }
 *
 * MyComponent = withViewportMatch( { isMobile: '< small' } )( MyComponent );
 * ```
=======
 * @param {Object} queries  Object of prop name to viewport query.
 *
 * @see isViewportMatch
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {Function} Higher-order component.
 */

<<<<<<< HEAD
const withViewportMatch = queries => {
  const queryEntries = Object.entries(queries);

  const useViewPortQueriesResult = () => Object.fromEntries(queryEntries.map(([key, query]) => {
    let [operator, breakpointName] = query.split(' ');

    if (breakpointName === undefined) {
      breakpointName = operator;
      operator = '>=';
    } // Hooks should unconditionally execute in the same order,
    // we are respecting that as from the static query of the HOC we generate
    // a hook that calls other hooks always in the same order (because the query never changes).
    // eslint-disable-next-line react-hooks/rules-of-hooks


    return [key, (0,external_wp_compose_namespaceObject.useViewportMatch)(breakpointName, operator)];
  }));

  return (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(WrappedComponent => {
    return (0,external_wp_compose_namespaceObject.pure)(props => {
      const queriesResult = useViewPortQueriesResult();
      return (0,external_wp_element_namespaceObject.createElement)(WrappedComponent, { ...props,
        ...queriesResult
      });
    });
  }, 'withViewportMatch');
};

/* harmony default export */ const with_viewport_match = (withViewportMatch);

;// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/if-viewport-matches.js
=======
var with_viewport_match_withViewportMatch = function withViewportMatch(queries) {
  return Object(external_this_wp_compose_["createHigherOrderComponent"])(Object(external_this_wp_data_["withSelect"])(function (select) {
    return Object(external_lodash_["mapValues"])(queries, function (query) {
      return select('core/viewport').isViewportMatch(query);
    });
  }), 'withViewportMatch');
};

/* harmony default export */ var with_viewport_match = (with_viewport_match_withViewportMatch);

// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/if-viewport-matches.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Higher-order component creator, creating a new component which renders if
 * the viewport query is satisfied.
 *
<<<<<<< HEAD
 * @see withViewportMatches
 *
 * @param {string} query Viewport query.
 *
 * @example
 *
 * ```jsx
 * function MyMobileComponent() {
 * 	return <div>I'm only rendered on mobile viewports!</div>;
 * }
 *
 * MyMobileComponent = ifViewportMatches( '< small' )( MyMobileComponent );
 * ```
=======
 * @param {string} query Viewport query.
 *
 * @see withViewportMatches
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {Function} Higher-order component.
 */

<<<<<<< HEAD
const ifViewportMatches = query => (0,external_wp_compose_namespaceObject.createHigherOrderComponent)((0,external_wp_compose_namespaceObject.compose)([with_viewport_match({
  isViewportMatch: query
}), (0,external_wp_compose_namespaceObject.ifCondition)(props => props.isViewportMatch)]), 'ifViewportMatches');

/* harmony default export */ const if_viewport_matches = (ifViewportMatches);

;// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/index.js
=======
var if_viewport_matches_ifViewportMatches = function ifViewportMatches(query) {
  return Object(external_this_wp_compose_["createHigherOrderComponent"])(Object(external_this_wp_compose_["compose"])([with_viewport_match({
    isViewportMatch: query
  }), Object(external_this_wp_compose_["ifCondition"])(function (props) {
    return props.isViewportMatch;
  })]), 'ifViewportMatches');
};

/* harmony default export */ var if_viewport_matches = (if_viewport_matches_ifViewportMatches);

// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/index.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */




/**
 * Hash of breakpoint names with pixel width at which it becomes effective.
 *
 * @see _breakpoints.scss
 *
 * @type {Object}
 */

<<<<<<< HEAD
const BREAKPOINTS = {
=======
var BREAKPOINTS = {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  huge: 1440,
  wide: 1280,
  large: 960,
  medium: 782,
  small: 600,
  mobile: 480
};
/**
 * Hash of query operators with corresponding condition for media query.
 *
 * @type {Object}
 */

<<<<<<< HEAD
const OPERATORS = {
  '<': 'max-width',
  '>=': 'min-width'
};
listener(BREAKPOINTS, OPERATORS);

(window.wp = window.wp || {}).viewport = __webpack_exports__;
/******/ })()
;
=======
var OPERATORS = {
  '<': 'max-width',
  '>=': 'min-width'
};
/**
 * Callback invoked when media query state should be updated. Is invoked a
 * maximum of one time per call stack.
 */

var build_module_setIsMatching = Object(external_lodash_["debounce"])(function () {
  var values = Object(external_lodash_["mapValues"])(build_module_queries, function (query) {
    return query.matches;
  });
  Object(external_this_wp_data_["dispatch"])('core/viewport').setIsMatching(values);
}, {
  leading: true
});
/**
 * Hash of breakpoint names with generated MediaQueryList for corresponding
 * media query.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/API/Window/matchMedia
 * @see https://developer.mozilla.org/en-US/docs/Web/API/MediaQueryList
 *
 * @type {Object<string,MediaQueryList>}
 */

var build_module_queries = Object(external_lodash_["reduce"])(BREAKPOINTS, function (result, width, name) {
  Object(external_lodash_["forEach"])(OPERATORS, function (condition, operator) {
    var list = window.matchMedia("(".concat(condition, ": ").concat(width, "px)"));
    list.addListener(build_module_setIsMatching);
    var key = [operator, name].join(' ');
    result[key] = list;
  });
  return result;
}, {});
window.addEventListener('orientationchange', build_module_setIsMatching); // Set initial values

build_module_setIsMatching();
build_module_setIsMatching.flush();


/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ })

/******/ });
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
