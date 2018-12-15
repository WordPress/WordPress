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
/******/ 	return __webpack_require__(__webpack_require__.s = 316);
/******/ })
/************************************************************************/
/******/ ({

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ 316:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "setIsMatching", function() { return setIsMatching; });
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "isViewportMatch", function() { return isViewportMatch; });

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(5);

// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/store/reducer.js
/**
 * Reducer returning the viewport state, as keys of breakpoint queries with
 * boolean value representing whether query is matched.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
function reducer() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_IS_MATCHING':
      return action.values;
  }

  return state;
}

/* harmony default export */ var store_reducer = (reducer);

// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/store/actions.js
/**
 * Returns an action object used in signalling that viewport queries have been
 * updated. Values are specified as an object of breakpoint query keys where
 * value represents whether query matches.
 *
 * @param {Object} values Breakpoint query matches.
 *
 * @return {Object} Action object.
 */
function setIsMatching(values) {
  return {
    type: 'SET_IS_MATCHING',
    values: values
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/store/selectors.js
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
 * isViewportMatch( state, '< huge' );
 * isViewPortMatch( state, 'medium' );
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

// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




/* harmony default export */ var store = (Object(external_this_wp_data_["registerStore"])('core/viewport', {
  reducer: store_reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
}));

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(7);

// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/with-viewport-match.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Higher-order component creator, creating a new component which renders with
 * the given prop names, where the value passed to the underlying component is
 * the result of the query assigned as the object's value.
 *
 * @param {Object} queries  Object of prop name to viewport query.
 *
 * @see isViewportMatch
 *
 * @return {Function} Higher-order component.
 */

var with_viewport_match_withViewportMatch = function withViewportMatch(queries) {
  return Object(external_this_wp_compose_["createHigherOrderComponent"])(Object(external_this_wp_data_["withSelect"])(function (select) {
    return Object(external_lodash_["mapValues"])(queries, function (query) {
      return select('core/viewport').isViewportMatch(query);
    });
  }), 'withViewportMatch');
};

/* harmony default export */ var with_viewport_match = (with_viewport_match_withViewportMatch);

// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/if-viewport-matches.js
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
 * @param {string} query Viewport query.
 *
 * @see withViewportMatches
 *
 * @return {Function} Higher-order component.
 */

var if_viewport_matches_ifViewportMatches = function ifViewportMatches(query) {
  return Object(external_this_wp_compose_["createHigherOrderComponent"])(Object(external_this_wp_compose_["compose"])([with_viewport_match({
    isViewportMatch: query
  }), Object(external_this_wp_compose_["ifCondition"])(function (props) {
    return props.isViewportMatch;
  })]), 'ifViewportMatches');
};

/* harmony default export */ var if_viewport_matches = (if_viewport_matches_ifViewportMatches);

// CONCATENATED MODULE: ./node_modules/@wordpress/viewport/build-module/index.js
/* concated harmony reexport ifViewportMatches */__webpack_require__.d(__webpack_exports__, "ifViewportMatches", function() { return if_viewport_matches; });
/* concated harmony reexport withViewportMatch */__webpack_require__.d(__webpack_exports__, "withViewportMatch", function() { return with_viewport_match; });
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
 * Hash of breakpoint names with pixel width at which it becomes effective.
 *
 * @see _breakpoints.scss
 *
 * @type {Object}
 */

var BREAKPOINTS = {
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


/***/ }),

/***/ 5:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ 7:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ })

/******/ });