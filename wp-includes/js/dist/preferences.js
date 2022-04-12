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
  "PreferenceToggleMenuItem": function() { return /* reexport */ PreferenceToggleMenuItem; },
  "store": function() { return /* reexport */ store; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/preferences/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  "set": function() { return set; },
  "setDefaults": function() { return setDefaults; },
  "toggle": function() { return toggle; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/preferences/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  "get": function() { return get; }
});

;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","components"]
var external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","primitives"]
var external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/check.js


/**
 * WordPress dependencies
 */

const check = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"
}));
/* harmony default export */ var library_check = (check);

;// CONCATENATED MODULE: external ["wp","a11y"]
var external_wp_a11y_namespaceObject = window["wp"]["a11y"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/store/reducer.js
/**
 * WordPress dependencies
 */

/**
 * Reducer returning the defaults for user preferences.
 *
 * This is kept intentionally separate from the preferences
 * themselves so that defaults are not persisted.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function defaults() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  if (action.type === 'SET_PREFERENCE_DEFAULTS') {
    const {
      scope,
      defaults: values
    } = action;
    return { ...state,
      [scope]: { ...state[scope],
        ...values
      }
    };
  }

  return state;
}
/**
 * Reducer returning the user preferences.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function preferences() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  if (action.type === 'SET_PREFERENCE_VALUE') {
    const {
      scope,
      name,
      value
    } = action;
    return { ...state,
      [scope]: { ...state[scope],
        [name]: value
      }
    };
  }

  return state;
}
/* harmony default export */ var reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  defaults,
  preferences
}));

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/store/actions.js
/**
 * Returns an action object used in signalling that a preference should be
 * toggled.
 *
 * @param {string} scope The preference scope (e.g. core/edit-post).
 * @param {string} name  The preference name.
 */
function toggle(scope, name) {
  return function (_ref) {
    let {
      select,
      dispatch
    } = _ref;
    const currentValue = select.get(scope, name);
    dispatch.set(scope, name, !currentValue);
  };
}
/**
 * Returns an action object used in signalling that a preference should be set
 * to a value
 *
 * @param {string} scope The preference scope (e.g. core/edit-post).
 * @param {string} name  The preference name.
 * @param {*}      value The value to set.
 *
 * @return {Object} Action object.
 */

function set(scope, name, value) {
  return {
    type: 'SET_PREFERENCE_VALUE',
    scope,
    name,
    value
  };
}
/**
 * Returns an action object used in signalling that preference defaults should
 * be set.
 *
 * @param {string}            scope    The preference scope (e.g. core/edit-post).
 * @param {Object<string, *>} defaults A key/value map of preference names to values.
 *
 * @return {Object} Action object.
 */

function setDefaults(scope, defaults) {
  return {
    type: 'SET_PREFERENCE_DEFAULTS',
    scope,
    defaults
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/store/selectors.js
/**
 * Returns a boolean indicating whether a prefer is active for a particular
 * scope.
 *
 * @param {Object} state The store state.
 * @param {string} scope The scope of the feature (e.g. core/edit-post).
 * @param {string} name  The name of the feature.
 *
 * @return {*} Is the feature enabled?
 */
function get(state, scope, name) {
  var _state$preferences$sc, _state$defaults$scope;

  const value = (_state$preferences$sc = state.preferences[scope]) === null || _state$preferences$sc === void 0 ? void 0 : _state$preferences$sc[name];
  return value !== undefined ? value : (_state$defaults$scope = state.defaults[scope]) === null || _state$defaults$scope === void 0 ? void 0 : _state$defaults$scope[name];
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const STORE_NAME = 'core/preferences';

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */

/**
 * Internal dependencies
 */





/**
 * Store definition for the interface namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['preferences']
}); // Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.

(0,external_wp_data_namespaceObject.registerStore)(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['preferences']
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/components/preference-toggle-menu-item/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function PreferenceToggleMenuItem(_ref) {
  let {
    scope,
    name,
    label,
    info,
    messageActivated,
    messageDeactivated,
    shortcut
  } = _ref;
  const isActive = (0,external_wp_data_namespaceObject.useSelect)(select => !!select(store).get(scope, name), [name]);
  const {
    toggle
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);

  const speakMessage = () => {
    if (isActive) {
      const message = messageDeactivated || (0,external_wp_i18n_namespaceObject.sprintf)(
      /* translators: %s: preference name, e.g. 'Fullscreen mode' */
      (0,external_wp_i18n_namespaceObject.__)('Preference deactivated - %s'), label);
      (0,external_wp_a11y_namespaceObject.speak)(message);
    } else {
      const message = messageActivated || (0,external_wp_i18n_namespaceObject.sprintf)(
      /* translators: %s: preference name, e.g. 'Fullscreen mode' */
      (0,external_wp_i18n_namespaceObject.__)('Preference activated - %s'), label);
      (0,external_wp_a11y_namespaceObject.speak)(message);
    }
  };

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    icon: isActive && library_check,
    isSelected: isActive,
    onClick: () => {
      toggle(scope, name);
      speakMessage();
    },
    role: "menuitemcheckbox",
    info: info,
    shortcut: shortcut
  }, label);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/components/index.js


;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences/build-module/index.js



(window.wp = window.wp || {}).preferences = __webpack_exports__;
/******/ })()
;