/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 7153:
/***/ (function(module, exports) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	Copyright (c) 2018 Jed Watson.
	Licensed under the MIT License (MIT), see
	http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames () {
		var classes = '';

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (arg) {
				classes = appendClass(classes, parseValue(arg));
			}
		}

		return classes;
	}

	function parseValue (arg) {
		if (typeof arg === 'string' || typeof arg === 'number') {
			return arg;
		}

		if (typeof arg !== 'object') {
			return '';
		}

		if (Array.isArray(arg)) {
			return classNames.apply(null, arg);
		}

		if (arg.toString !== Object.prototype.toString && !arg.toString.toString().includes('[native code]')) {
			return arg.toString();
		}

		var classes = '';

		for (var key in arg) {
			if (hasOwn.call(arg, key) && arg[key]) {
				classes = appendClass(classes, key);
			}
		}

		return classes;
	}

	function appendClass (value, newClass) {
		if (!newClass) {
			return value;
		}
	
		if (value) {
			return value + ' ' + newClass;
		}
	
		return value + newClass;
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


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
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
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "initialize": function() { return /* binding */ initialize; },
  "initializeEditor": function() { return /* binding */ initializeEditor; },
  "reinitializeEditor": function() { return /* binding */ reinitializeEditor; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/interface/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  "disableComplementaryArea": function() { return disableComplementaryArea; },
  "enableComplementaryArea": function() { return enableComplementaryArea; },
  "pinItem": function() { return pinItem; },
  "setDefaultComplementaryArea": function() { return setDefaultComplementaryArea; },
  "setFeatureDefaults": function() { return setFeatureDefaults; },
  "setFeatureValue": function() { return setFeatureValue; },
  "toggleFeature": function() { return toggleFeature; },
  "unpinItem": function() { return unpinItem; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/interface/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  "getActiveComplementaryArea": function() { return getActiveComplementaryArea; },
  "isFeatureActive": function() { return isFeatureActive; },
  "isItemPinned": function() { return isItemPinned; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-widgets/build-module/store/actions.js
var store_actions_namespaceObject = {};
__webpack_require__.r(store_actions_namespaceObject);
__webpack_require__.d(store_actions_namespaceObject, {
  "closeGeneralSidebar": function() { return closeGeneralSidebar; },
  "moveBlockToWidgetArea": function() { return moveBlockToWidgetArea; },
  "persistStubPost": function() { return persistStubPost; },
  "saveEditedWidgetAreas": function() { return saveEditedWidgetAreas; },
  "saveWidgetArea": function() { return saveWidgetArea; },
  "saveWidgetAreas": function() { return saveWidgetAreas; },
  "setIsInserterOpened": function() { return setIsInserterOpened; },
  "setIsListViewOpened": function() { return setIsListViewOpened; },
  "setIsWidgetAreaOpen": function() { return setIsWidgetAreaOpen; },
  "setWidgetAreasOpenState": function() { return setWidgetAreasOpenState; },
  "setWidgetIdForClientId": function() { return setWidgetIdForClientId; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-widgets/build-module/store/resolvers.js
var resolvers_namespaceObject = {};
__webpack_require__.r(resolvers_namespaceObject);
__webpack_require__.d(resolvers_namespaceObject, {
  "getWidgetAreas": function() { return getWidgetAreas; },
  "getWidgets": function() { return getWidgets; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-widgets/build-module/store/selectors.js
var store_selectors_namespaceObject = {};
__webpack_require__.r(store_selectors_namespaceObject);
__webpack_require__.d(store_selectors_namespaceObject, {
  "__experimentalGetInsertionPoint": function() { return __experimentalGetInsertionPoint; },
  "canInsertBlockInWidgetArea": function() { return canInsertBlockInWidgetArea; },
  "getEditedWidgetAreas": function() { return getEditedWidgetAreas; },
  "getIsWidgetAreaOpen": function() { return getIsWidgetAreaOpen; },
  "getParentWidgetAreaBlock": function() { return getParentWidgetAreaBlock; },
  "getReferenceWidgetBlocks": function() { return getReferenceWidgetBlocks; },
  "getWidget": function() { return getWidget; },
  "getWidgetAreaForWidgetId": function() { return getWidgetAreaForWidgetId; },
  "getWidgetAreas": function() { return selectors_getWidgetAreas; },
  "getWidgets": function() { return selectors_getWidgets; },
  "isInserterOpened": function() { return isInserterOpened; },
  "isListViewOpened": function() { return isListViewOpened; },
  "isSavingWidgetAreas": function() { return isSavingWidgetAreas; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-widgets/build-module/blocks/widget-area/index.js
var widget_area_namespaceObject = {};
__webpack_require__.r(widget_area_namespaceObject);
__webpack_require__.d(widget_area_namespaceObject, {
  "metadata": function() { return metadata; },
  "name": function() { return widget_area_name; },
  "settings": function() { return settings; }
});

;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","blocks"]
var external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","deprecated"]
var external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// CONCATENATED MODULE: external ["wp","blockLibrary"]
var external_wp_blockLibrary_namespaceObject = window["wp"]["blockLibrary"];
;// CONCATENATED MODULE: external ["wp","coreData"]
var external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// CONCATENATED MODULE: external ["wp","widgets"]
var external_wp_widgets_namespaceObject = window["wp"]["widgets"];
;// CONCATENATED MODULE: external ["wp","preferences"]
var external_wp_preferences_namespaceObject = window["wp"]["preferences"];
;// CONCATENATED MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/store/reducer.js
/**
 * WordPress dependencies
 */

/**
 * Controls the open state of the widget areas.
 *
 * @param {Object} state  Redux state.
 * @param {Object} action Redux action.
 *
 * @return {Array} Updated state.
 */

function widgetAreasOpenState() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;
  const {
    type
  } = action;

  switch (type) {
    case 'SET_WIDGET_AREAS_OPEN_STATE':
      {
        return action.widgetAreasOpenState;
      }

    case 'SET_IS_WIDGET_AREA_OPEN':
      {
        const {
          clientId,
          isOpen
        } = action;
        return { ...state,
          [clientId]: isOpen
        };
      }

    default:
      {
        return state;
      }
  }
}
/**
 * Reducer to set the block inserter panel open or closed.
 *
 * Note: this reducer interacts with the list view panel reducer
 * to make sure that only one of the two panels is open at the same time.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 */

function blockInserterPanel() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_IS_LIST_VIEW_OPENED':
      return action.isOpen ? false : state;

    case 'SET_IS_INSERTER_OPENED':
      return action.value;
  }

  return state;
}
/**
 * Reducer to set the list view panel open or closed.
 *
 * Note: this reducer interacts with the inserter panel reducer
 * to make sure that only one of the two panels is open at the same time.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 */

function listViewPanel() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_IS_INSERTER_OPENED':
      return action.value ? false : state;

    case 'SET_IS_LIST_VIEW_OPENED':
      return action.isOpen;
  }

  return state;
}
/* harmony default export */ var reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  blockInserterPanel,
  listViewPanel,
  widgetAreasOpenState
}));

;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","notices"]
var external_wp_notices_namespaceObject = window["wp"]["notices"];
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
function _extends() {
  _extends = Object.assign ? Object.assign.bind() : function (target) {
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
// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(7153);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);
;// CONCATENATED MODULE: external ["wp","components"]
var external_wp_components_namespaceObject = window["wp"]["components"];
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

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/star-filled.js


/**
 * WordPress dependencies
 */

const starFilled = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.071-.222L4.754 9.881a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z"
}));
/* harmony default export */ var star_filled = (starFilled);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/star-empty.js


/**
 * WordPress dependencies
 */

const starEmpty = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  d: "M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z",
  clipRule: "evenodd"
}));
/* harmony default export */ var star_empty = (starEmpty);

;// CONCATENATED MODULE: external ["wp","viewport"]
var external_wp_viewport_namespaceObject = window["wp"]["viewport"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/close-small.js


/**
 * WordPress dependencies
 */

const closeSmall = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"
}));
/* harmony default export */ var close_small = (closeSmall);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/actions.js
/**
 * WordPress dependencies
 */


/**
 * Set a default complementary area.
 *
 * @param {string} scope Complementary area scope.
 * @param {string} area  Area identifier.
 *
 * @return {Object} Action object.
 */

const setDefaultComplementaryArea = (scope, area) => ({
  type: 'SET_DEFAULT_COMPLEMENTARY_AREA',
  scope,
  area
});
/**
 * Enable the complementary area.
 *
 * @param {string} scope Complementary area scope.
 * @param {string} area  Area identifier.
 */

const enableComplementaryArea = (scope, area) => _ref => {
  let {
    registry,
    dispatch
  } = _ref;

  // Return early if there's no area.
  if (!area) {
    return;
  }

  const isComplementaryAreaVisible = registry.select(external_wp_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible');

  if (!isComplementaryAreaVisible) {
    registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, 'isComplementaryAreaVisible', true);
  }

  dispatch({
    type: 'ENABLE_COMPLEMENTARY_AREA',
    scope,
    area
  });
};
/**
 * Disable the complementary area.
 *
 * @param {string} scope Complementary area scope.
 */

const disableComplementaryArea = scope => _ref2 => {
  let {
    registry
  } = _ref2;
  const isComplementaryAreaVisible = registry.select(external_wp_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible');

  if (isComplementaryAreaVisible) {
    registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, 'isComplementaryAreaVisible', false);
  }
};
/**
 * Pins an item.
 *
 * @param {string} scope Item scope.
 * @param {string} item  Item identifier.
 *
 * @return {Object} Action object.
 */

const pinItem = (scope, item) => _ref3 => {
  let {
    registry
  } = _ref3;

  // Return early if there's no item.
  if (!item) {
    return;
  }

  const pinnedItems = registry.select(external_wp_preferences_namespaceObject.store).get(scope, 'pinnedItems'); // The item is already pinned, there's nothing to do.

  if ((pinnedItems === null || pinnedItems === void 0 ? void 0 : pinnedItems[item]) === true) {
    return;
  }

  registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, 'pinnedItems', { ...pinnedItems,
    [item]: true
  });
};
/**
 * Unpins an item.
 *
 * @param {string} scope Item scope.
 * @param {string} item  Item identifier.
 */

const unpinItem = (scope, item) => _ref4 => {
  let {
    registry
  } = _ref4;

  // Return early if there's no item.
  if (!item) {
    return;
  }

  const pinnedItems = registry.select(external_wp_preferences_namespaceObject.store).get(scope, 'pinnedItems');
  registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, 'pinnedItems', { ...pinnedItems,
    [item]: false
  });
};
/**
 * Returns an action object used in signalling that a feature should be toggled.
 *
 * @param {string} scope       The feature scope (e.g. core/edit-post).
 * @param {string} featureName The feature name.
 */

function toggleFeature(scope, featureName) {
  return function (_ref5) {
    let {
      registry
    } = _ref5;
    external_wp_deprecated_default()(`dispatch( 'core/interface' ).toggleFeature`, {
      since: '6.0',
      alternative: `dispatch( 'core/preferences' ).toggle`
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).toggle(scope, featureName);
  };
}
/**
 * Returns an action object used in signalling that a feature should be set to
 * a true or false value
 *
 * @param {string}  scope       The feature scope (e.g. core/edit-post).
 * @param {string}  featureName The feature name.
 * @param {boolean} value       The value to set.
 *
 * @return {Object} Action object.
 */

function setFeatureValue(scope, featureName, value) {
  return function (_ref6) {
    let {
      registry
    } = _ref6;
    external_wp_deprecated_default()(`dispatch( 'core/interface' ).setFeatureValue`, {
      since: '6.0',
      alternative: `dispatch( 'core/preferences' ).set`
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, featureName, !!value);
  };
}
/**
 * Returns an action object used in signalling that defaults should be set for features.
 *
 * @param {string}                  scope    The feature scope (e.g. core/edit-post).
 * @param {Object<string, boolean>} defaults A key/value map of feature names to values.
 *
 * @return {Object} Action object.
 */

function setFeatureDefaults(scope, defaults) {
  return function (_ref7) {
    let {
      registry
    } = _ref7;
    external_wp_deprecated_default()(`dispatch( 'core/interface' ).setFeatureDefaults`, {
      since: '6.0',
      alternative: `dispatch( 'core/preferences' ).setDefaults`
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).setDefaults(scope, defaults);
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/selectors.js
/**
 * WordPress dependencies
 */



/**
 * Returns the complementary area that is active in a given scope.
 *
 * @param {Object} state Global application state.
 * @param {string} scope Item scope.
 *
 * @return {string | null | undefined} The complementary area that is active in the given scope.
 */

const getActiveComplementaryArea = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, scope) => {
  var _state$complementaryA;

  const isComplementaryAreaVisible = select(external_wp_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible'); // Return `undefined` to indicate that the user has never toggled
  // visibility, this is the vanilla default. Other code relies on this
  // nuance in the return value.

  if (isComplementaryAreaVisible === undefined) {
    return undefined;
  } // Return `null` to indicate the user hid the complementary area.


  if (!isComplementaryAreaVisible) {
    return null;
  }

  return state === null || state === void 0 ? void 0 : (_state$complementaryA = state.complementaryAreas) === null || _state$complementaryA === void 0 ? void 0 : _state$complementaryA[scope];
});
/**
 * Returns a boolean indicating if an item is pinned or not.
 *
 * @param {Object} state Global application state.
 * @param {string} scope Scope.
 * @param {string} item  Item to check.
 *
 * @return {boolean} True if the item is pinned and false otherwise.
 */

const isItemPinned = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, scope, item) => {
  var _pinnedItems$item;

  const pinnedItems = select(external_wp_preferences_namespaceObject.store).get(scope, 'pinnedItems');
  return (_pinnedItems$item = pinnedItems === null || pinnedItems === void 0 ? void 0 : pinnedItems[item]) !== null && _pinnedItems$item !== void 0 ? _pinnedItems$item : true;
});
/**
 * Returns a boolean indicating whether a feature is active for a particular
 * scope.
 *
 * @param {Object} state       The store state.
 * @param {string} scope       The scope of the feature (e.g. core/edit-post).
 * @param {string} featureName The name of the feature.
 *
 * @return {boolean} Is the feature enabled?
 */

const isFeatureActive = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, scope, featureName) => {
  external_wp_deprecated_default()(`select( 'core/interface' ).isFeatureActive( scope, featureName )`, {
    since: '6.0',
    alternative: `select( 'core/preferences' ).get( scope, featureName )`
  });
  return !!select(external_wp_preferences_namespaceObject.store).get(scope, featureName);
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/reducer.js
/**
 * WordPress dependencies
 */

function complementaryAreas() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_DEFAULT_COMPLEMENTARY_AREA':
      {
        const {
          scope,
          area
        } = action; // If there's already an area, don't overwrite it.

        if (state[scope]) {
          return state;
        }

        return { ...state,
          [scope]: area
        };
      }

    case 'ENABLE_COMPLEMENTARY_AREA':
      {
        const {
          scope,
          area
        } = action;
        return { ...state,
          [scope]: area
        };
      }
  }

  return state;
}
/* harmony default export */ var store_reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  complementaryAreas
}));

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const STORE_NAME = 'core/interface';

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/index.js
/**
 * WordPress dependencies
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
  reducer: store_reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
}); // Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.

(0,external_wp_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: external ["wp","plugins"]
var external_wp_plugins_namespaceObject = window["wp"]["plugins"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-context/index.js
/**
 * WordPress dependencies
 */

/* harmony default export */ var complementary_area_context = ((0,external_wp_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
  return {
    icon: ownProps.icon || context.icon,
    identifier: ownProps.identifier || `${context.name}/${ownProps.name}`
  };
}));

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-toggle/index.js



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




function ComplementaryAreaToggle(_ref) {
  let {
    as = external_wp_components_namespaceObject.Button,
    scope,
    identifier,
    icon,
    selectedIcon,
    name,
    ...props
  } = _ref;
  const ComponentToUse = as;
  const isSelected = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).getActiveComplementaryArea(scope) === identifier, [identifier]);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  return (0,external_wp_element_namespaceObject.createElement)(ComponentToUse, _extends({
    icon: selectedIcon && isSelected ? selectedIcon : icon,
    onClick: () => {
      if (isSelected) {
        disableComplementaryArea(scope);
      } else {
        enableComplementaryArea(scope, identifier);
      }
    }
  }, props));
}

/* harmony default export */ var complementary_area_toggle = (complementary_area_context(ComplementaryAreaToggle));

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-header/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



const ComplementaryAreaHeader = _ref => {
  let {
    smallScreenTitle,
    children,
    className,
    toggleButtonProps
  } = _ref;
  const toggleButton = (0,external_wp_element_namespaceObject.createElement)(complementary_area_toggle, _extends({
    icon: close_small
  }, toggleButtonProps));
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "components-panel__header interface-complementary-area-header__small"
  }, smallScreenTitle && (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "interface-complementary-area-header__small-title"
  }, smallScreenTitle), toggleButton), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()('components-panel__header', 'interface-complementary-area-header', className),
    tabIndex: -1
  }, children, toggleButton));
};

/* harmony default export */ var complementary_area_header = (ComplementaryAreaHeader);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/action-item/index.js



/**
 * WordPress dependencies
 */



const noop = () => {};

function ActionItemSlot(_ref) {
  let {
    name,
    as: Component = external_wp_components_namespaceObject.ButtonGroup,
    fillProps = {},
    bubblesVirtually,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Slot, {
    name: name,
    bubblesVirtually: bubblesVirtually,
    fillProps: fillProps
  }, fills => {
    if (!external_wp_element_namespaceObject.Children.toArray(fills).length) {
      return null;
    } // Special handling exists for backward compatibility.
    // It ensures that menu items created by plugin authors aren't
    // duplicated with automatically injected menu items coming
    // from pinnable plugin sidebars.
    // @see https://github.com/WordPress/gutenberg/issues/14457


    const initializedByPlugins = [];
    external_wp_element_namespaceObject.Children.forEach(fills, _ref2 => {
      let {
        props: {
          __unstableExplicitMenuItem,
          __unstableTarget
        }
      } = _ref2;

      if (__unstableTarget && __unstableExplicitMenuItem) {
        initializedByPlugins.push(__unstableTarget);
      }
    });
    const children = external_wp_element_namespaceObject.Children.map(fills, child => {
      if (!child.props.__unstableExplicitMenuItem && initializedByPlugins.includes(child.props.__unstableTarget)) {
        return null;
      }

      return child;
    });
    return (0,external_wp_element_namespaceObject.createElement)(Component, props, children);
  });
}

function ActionItem(_ref3) {
  let {
    name,
    as: Component = external_wp_components_namespaceObject.Button,
    onClick,
    ...props
  } = _ref3;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Fill, {
    name: name
  }, _ref4 => {
    let {
      onClick: fpOnClick
    } = _ref4;
    return (0,external_wp_element_namespaceObject.createElement)(Component, _extends({
      onClick: onClick || fpOnClick ? function () {
        (onClick || noop)(...arguments);
        (fpOnClick || noop)(...arguments);
      } : undefined
    }, props));
  });
}

ActionItem.Slot = ActionItemSlot;
/* harmony default export */ var action_item = (ActionItem);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-more-menu-item/index.js



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




const PluginsMenuItem = _ref => {
  let {
    // Menu item is marked with unstable prop for backward compatibility.
    // They are removed so they don't leak to DOM elements.
    // @see https://github.com/WordPress/gutenberg/issues/14457
    __unstableExplicitMenuItem,
    __unstableTarget,
    ...restProps
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, restProps);
};

function ComplementaryAreaMoreMenuItem(_ref2) {
  let {
    scope,
    target,
    __unstableExplicitMenuItem,
    ...props
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)(complementary_area_toggle, _extends({
    as: toggleProps => {
      return (0,external_wp_element_namespaceObject.createElement)(action_item, _extends({
        __unstableExplicitMenuItem: __unstableExplicitMenuItem,
        __unstableTarget: `${scope}/${target}`,
        as: PluginsMenuItem,
        name: `${scope}/plugin-more-menu`
      }, toggleProps));
    },
    role: "menuitemcheckbox",
    selectedIcon: library_check,
    name: target,
    scope: scope
  }, props));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/pinned-items/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function PinnedItems(_ref) {
  let {
    scope,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Fill, _extends({
    name: `PinnedItems/${scope}`
  }, props));
}

function PinnedItemsSlot(_ref2) {
  let {
    scope,
    className,
    ...props
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Slot, _extends({
    name: `PinnedItems/${scope}`
  }, props), fills => (fills === null || fills === void 0 ? void 0 : fills.length) > 0 && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()(className, 'interface-pinned-items')
  }, fills));
}

PinnedItems.Slot = PinnedItemsSlot;
/* harmony default export */ var pinned_items = (PinnedItems);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */








function ComplementaryAreaSlot(_ref) {
  let {
    scope,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Slot, _extends({
    name: `ComplementaryArea/${scope}`
  }, props));
}

function ComplementaryAreaFill(_ref2) {
  let {
    scope,
    children,
    className
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Fill, {
    name: `ComplementaryArea/${scope}`
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: className
  }, children));
}

function useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall) {
  const previousIsSmall = (0,external_wp_element_namespaceObject.useRef)(false);
  const shouldOpenWhenNotSmall = (0,external_wp_element_namespaceObject.useRef)(false);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    // If the complementary area is active and the editor is switching from a big to a small window size.
    if (isActive && isSmall && !previousIsSmall.current) {
      // Disable the complementary area.
      disableComplementaryArea(scope); // Flag the complementary area to be reopened when the window size goes from small to big.

      shouldOpenWhenNotSmall.current = true;
    } else if ( // If there is a flag indicating the complementary area should be enabled when we go from small to big window size
    // and we are going from a small to big window size.
    shouldOpenWhenNotSmall.current && !isSmall && previousIsSmall.current) {
      // Remove the flag indicating the complementary area should be enabled.
      shouldOpenWhenNotSmall.current = false; // Enable the complementary area.

      enableComplementaryArea(scope, identifier);
    } else if ( // If the flag is indicating the current complementary should be reopened but another complementary area becomes active,
    // remove the flag.
    shouldOpenWhenNotSmall.current && activeArea && activeArea !== identifier) {
      shouldOpenWhenNotSmall.current = false;
    }

    if (isSmall !== previousIsSmall.current) {
      previousIsSmall.current = isSmall;
    }
  }, [isActive, isSmall, scope, identifier, activeArea]);
}

function ComplementaryArea(_ref3) {
  let {
    children,
    className,
    closeLabel = (0,external_wp_i18n_namespaceObject.__)('Close plugin'),
    identifier,
    header,
    headerClassName,
    icon,
    isPinnable = true,
    panelClassName,
    scope,
    name,
    smallScreenTitle,
    title,
    toggleShortcut,
    isActiveByDefault,
    showIconLabels = false
  } = _ref3;
  const {
    isActive,
    isPinned,
    activeArea,
    isSmall,
    isLarge
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getActiveComplementaryArea,
      isItemPinned
    } = select(store);

    const _activeArea = getActiveComplementaryArea(scope);

    return {
      isActive: _activeArea === identifier,
      isPinned: isItemPinned(scope, identifier),
      activeArea: _activeArea,
      isSmall: select(external_wp_viewport_namespaceObject.store).isViewportMatch('< medium'),
      isLarge: select(external_wp_viewport_namespaceObject.store).isViewportMatch('large')
    };
  }, [identifier, scope]);
  useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall);
  const {
    enableComplementaryArea,
    disableComplementaryArea,
    pinItem,
    unpinItem
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isActiveByDefault && activeArea === undefined && !isSmall) {
      enableComplementaryArea(scope, identifier);
    }
  }, [activeArea, isActiveByDefault, scope, identifier, isSmall]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, isPinnable && (0,external_wp_element_namespaceObject.createElement)(pinned_items, {
    scope: scope
  }, isPinned && (0,external_wp_element_namespaceObject.createElement)(complementary_area_toggle, {
    scope: scope,
    identifier: identifier,
    isPressed: isActive && (!showIconLabels || isLarge),
    "aria-expanded": isActive,
    label: title,
    icon: showIconLabels ? library_check : icon,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  })), name && isPinnable && (0,external_wp_element_namespaceObject.createElement)(ComplementaryAreaMoreMenuItem, {
    target: name,
    scope: scope,
    icon: icon
  }, title), isActive && (0,external_wp_element_namespaceObject.createElement)(ComplementaryAreaFill, {
    className: classnames_default()('interface-complementary-area', className),
    scope: scope
  }, (0,external_wp_element_namespaceObject.createElement)(complementary_area_header, {
    className: headerClassName,
    closeLabel: closeLabel,
    onClose: () => disableComplementaryArea(scope),
    smallScreenTitle: smallScreenTitle,
    toggleButtonProps: {
      label: closeLabel,
      shortcut: toggleShortcut,
      scope,
      identifier
    }
  }, header || (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("strong", null, title), isPinnable && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "interface-complementary-area__pin-unpin-item",
    icon: isPinned ? star_filled : star_empty,
    label: isPinned ? (0,external_wp_i18n_namespaceObject.__)('Unpin from toolbar') : (0,external_wp_i18n_namespaceObject.__)('Pin to toolbar'),
    onClick: () => (isPinned ? unpinItem : pinItem)(scope, identifier),
    isPressed: isPinned,
    "aria-expanded": isPinned
  }))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Panel, {
    className: panelClassName
  }, children)));
}

const ComplementaryAreaWrapped = complementary_area_context(ComplementaryArea);
ComplementaryAreaWrapped.Slot = ComplementaryAreaSlot;
/* harmony default export */ var complementary_area = (ComplementaryAreaWrapped);

;// CONCATENATED MODULE: external ["wp","compose"]
var external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/navigable-region/index.js



/**
 * External dependencies
 */

function NavigableRegion(_ref) {
  let {
    children,
    className,
    ariaLabel,
    as: Tag = 'div',
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(Tag, _extends({
    className: classnames_default()('interface-navigable-region', className),
    "aria-label": ariaLabel,
    role: "region",
    tabIndex: "-1"
  }, props), children);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/interface-skeleton/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



function useHTMLClass(className) {
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const element = document && document.querySelector(`html:not(.${className})`);

    if (!element) {
      return;
    }

    element.classList.toggle(className);
    return () => {
      element.classList.toggle(className);
    };
  }, [className]);
}

function InterfaceSkeleton(_ref, ref) {
  let {
    isDistractionFree,
    footer,
    header,
    editorNotices,
    sidebar,
    secondarySidebar,
    notices,
    content,
    actions,
    labels,
    className,
    enableRegionNavigation = true,
    // Todo: does this need to be a prop.
    // Can we use a dependency to keyboard-shortcuts directly?
    shortcuts
  } = _ref;
  const navigateRegionsProps = (0,external_wp_components_namespaceObject.__unstableUseNavigateRegions)(shortcuts);
  useHTMLClass('interface-interface-skeleton__html-container');
  const defaultLabels = {
    /* translators: accessibility text for the top bar landmark region. */
    header: (0,external_wp_i18n_namespaceObject.__)('Header'),

    /* translators: accessibility text for the content landmark region. */
    body: (0,external_wp_i18n_namespaceObject.__)('Content'),

    /* translators: accessibility text for the secondary sidebar landmark region. */
    secondarySidebar: (0,external_wp_i18n_namespaceObject.__)('Block Library'),

    /* translators: accessibility text for the settings landmark region. */
    sidebar: (0,external_wp_i18n_namespaceObject.__)('Settings'),

    /* translators: accessibility text for the publish landmark region. */
    actions: (0,external_wp_i18n_namespaceObject.__)('Publish'),

    /* translators: accessibility text for the footer landmark region. */
    footer: (0,external_wp_i18n_namespaceObject.__)('Footer')
  };
  const mergedLabels = { ...defaultLabels,
    ...labels
  };
  const headerVariants = {
    hidden: isDistractionFree ? {
      opacity: 0
    } : {
      opacity: 1
    },
    hover: {
      opacity: 1,
      transition: {
        type: 'tween',
        delay: 0.2,
        delayChildren: 0.2
      }
    }
  };
  return (0,external_wp_element_namespaceObject.createElement)("div", _extends({}, enableRegionNavigation ? navigateRegionsProps : {}, {
    ref: (0,external_wp_compose_namespaceObject.useMergeRefs)([ref, enableRegionNavigation ? navigateRegionsProps.ref : undefined]),
    className: classnames_default()(className, 'interface-interface-skeleton', navigateRegionsProps.className, !!footer && 'has-footer')
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__editor"
  }, !!header && isDistractionFree && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    as: external_wp_components_namespaceObject.__unstableMotion.div,
    className: "interface-interface-skeleton__header",
    "aria-label": mergedLabels.header,
    initial: isDistractionFree ? 'hidden' : 'hover',
    whileHover: "hover",
    variants: headerVariants,
    transition: {
      type: 'tween',
      delay: 0.8
    }
  }, header), !!header && !isDistractionFree && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__header",
    ariaLabel: mergedLabels.header
  }, header), isDistractionFree && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__header"
  }, editorNotices), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__body"
  }, !!secondarySidebar && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__secondary-sidebar",
    ariaLabel: mergedLabels.secondarySidebar
  }, secondarySidebar), !!notices && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__notices"
  }, notices), (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__content",
    ariaLabel: mergedLabels.body
  }, content), !!sidebar && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__sidebar",
    ariaLabel: mergedLabels.sidebar
  }, sidebar), !!actions && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__actions",
    ariaLabel: mergedLabels.actions
  }, actions))), !!footer && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__footer",
    ariaLabel: mergedLabels.footer
  }, footer));
}

/* harmony default export */ var interface_skeleton = ((0,external_wp_element_namespaceObject.forwardRef)(InterfaceSkeleton));

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/more-vertical.js


/**
 * WordPress dependencies
 */

const moreVertical = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z"
}));
/* harmony default export */ var more_vertical = (moreVertical);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/more-menu-dropdown/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function MoreMenuDropdown(_ref) {
  let {
    as: DropdownComponent = external_wp_components_namespaceObject.DropdownMenu,
    className,

    /* translators: button label text should, if possible, be under 16 characters. */
    label = (0,external_wp_i18n_namespaceObject.__)('Options'),
    popoverProps,
    toggleProps,
    children
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(DropdownComponent, {
    className: classnames_default()('interface-more-menu-dropdown', className),
    icon: more_vertical,
    label: label,
    popoverProps: {
      placement: 'bottom-end',
      ...popoverProps,
      className: classnames_default()('interface-more-menu-dropdown__content', popoverProps === null || popoverProps === void 0 ? void 0 : popoverProps.className)
    },
    toggleProps: {
      tooltipPosition: 'bottom',
      ...toggleProps
    }
  }, onClose => children(onClose));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/index.js














;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/index.js



;// CONCATENATED MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/store/transformers.js
/**
 * WordPress dependencies
 */


/**
 * Converts a widget entity record into a block.
 *
 * @param {Object} widget The widget entity record.
 * @return {Object} a block (converted from the entity record).
 */

function transformWidgetToBlock(widget) {
  if (widget.id_base === 'block') {
    const parsedBlocks = (0,external_wp_blocks_namespaceObject.parse)(widget.instance.raw.content, {
      __unstableSkipAutop: true
    });

    if (!parsedBlocks.length) {
      return (0,external_wp_widgets_namespaceObject.addWidgetIdToBlock)((0,external_wp_blocks_namespaceObject.createBlock)('core/paragraph', {}, []), widget.id);
    }

    return (0,external_wp_widgets_namespaceObject.addWidgetIdToBlock)(parsedBlocks[0], widget.id);
  }

  let attributes;

  if (widget._embedded.about[0].is_multi) {
    attributes = {
      idBase: widget.id_base,
      instance: widget.instance
    };
  } else {
    attributes = {
      id: widget.id
    };
  }

  return (0,external_wp_widgets_namespaceObject.addWidgetIdToBlock)((0,external_wp_blocks_namespaceObject.createBlock)('core/legacy-widget', attributes, []), widget.id);
}
/**
 * Converts a block to a widget entity record.
 *
 * @param {Object}  block         The block.
 * @param {Object?} relatedWidget A related widget entity record from the API (optional).
 * @return {Object} the widget object (converted from block).
 */

function transformBlockToWidget(block) {
  let relatedWidget = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  let widget;
  const isValidLegacyWidgetBlock = block.name === 'core/legacy-widget' && (block.attributes.id || block.attributes.instance);

  if (isValidLegacyWidgetBlock) {
    var _block$attributes$id, _block$attributes$idB, _block$attributes$ins;

    widget = { ...relatedWidget,
      id: (_block$attributes$id = block.attributes.id) !== null && _block$attributes$id !== void 0 ? _block$attributes$id : relatedWidget.id,
      id_base: (_block$attributes$idB = block.attributes.idBase) !== null && _block$attributes$idB !== void 0 ? _block$attributes$idB : relatedWidget.id_base,
      instance: (_block$attributes$ins = block.attributes.instance) !== null && _block$attributes$ins !== void 0 ? _block$attributes$ins : relatedWidget.instance
    };
  } else {
    widget = { ...relatedWidget,
      id_base: 'block',
      instance: {
        raw: {
          content: (0,external_wp_blocks_namespaceObject.serialize)(block)
        }
      }
    };
  } // Delete read-only properties.


  delete widget.rendered;
  delete widget.rendered_form;
  return widget;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/store/utils.js
/**
 * "Kind" of the navigation post.
 *
 * @type {string}
 */
const KIND = 'root';
/**
 * "post type" of the navigation post.
 *
 * @type {string}
 */

const WIDGET_AREA_ENTITY_TYPE = 'sidebar';
/**
 * "post type" of the widget area post.
 *
 * @type {string}
 */

const POST_TYPE = 'postType';
/**
 * Builds an ID for a new widget area post.
 *
 * @param {number} widgetAreaId Widget area id.
 * @return {string} An ID.
 */

const buildWidgetAreaPostId = widgetAreaId => `widget-area-${widgetAreaId}`;
/**
 * Builds an ID for a global widget areas post.
 *
 * @return {string} An ID.
 */

const buildWidgetAreasPostId = () => `widget-areas`;
/**
 * Builds a query to resolve sidebars.
 *
 * @return {Object} Query.
 */

function buildWidgetAreasQuery() {
  return {
    per_page: -1
  };
}
/**
 * Builds a query to resolve widgets.
 *
 * @return {Object} Query.
 */

function buildWidgetsQuery() {
  return {
    per_page: -1,
    _embed: 'about'
  };
}
/**
 * Creates a stub post with given id and set of blocks. Used as a governing entity records
 * for all widget areas.
 *
 * @param {string} id     Post ID.
 * @param {Array}  blocks The list of blocks.
 * @return {Object} A stub post object formatted in compliance with the data layer.
 */

const createStubPost = (id, blocks) => ({
  id,
  slug: id,
  status: 'draft',
  type: 'page',
  blocks,
  meta: {
    widgetAreaId: id
  }
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/store/constants.js
/**
 * Module Constants
 */
const constants_STORE_NAME = 'core/edit-widgets';

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/store/actions.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */




/**
 * Persists a stub post with given ID to core data store. The post is meant to be in-memory only and
 * shouldn't be saved via the API.
 *
 * @param {string} id     Post ID.
 * @param {Array}  blocks Blocks the post should consist of.
 * @return {Object} The post object.
 */

const persistStubPost = (id, blocks) => _ref => {
  let {
    registry
  } = _ref;
  const stubPost = createStubPost(id, blocks);
  registry.dispatch(external_wp_coreData_namespaceObject.store).receiveEntityRecords(KIND, POST_TYPE, stubPost, {
    id: stubPost.id
  }, false);
  return stubPost;
};
/**
 * Converts all the blocks from edited widget areas into widgets,
 * and submits a batch request to save everything at once.
 *
 * Creates a snackbar notice on either success or error.
 *
 * @return {Function} An action creator.
 */

const saveEditedWidgetAreas = () => async _ref2 => {
  let {
    select,
    dispatch,
    registry
  } = _ref2;
  const editedWidgetAreas = select.getEditedWidgetAreas();

  if (!(editedWidgetAreas !== null && editedWidgetAreas !== void 0 && editedWidgetAreas.length)) {
    return;
  }

  try {
    await dispatch.saveWidgetAreas(editedWidgetAreas);
    registry.dispatch(external_wp_notices_namespaceObject.store).createSuccessNotice((0,external_wp_i18n_namespaceObject.__)('Widgets saved.'), {
      type: 'snackbar'
    });
  } catch (e) {
    registry.dispatch(external_wp_notices_namespaceObject.store).createErrorNotice(
    /* translators: %s: The error message. */
    (0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)('There was an error. %s'), e.message), {
      type: 'snackbar'
    });
  }
};
/**
 * Converts all the blocks from specified widget areas into widgets,
 * and submits a batch request to save everything at once.
 *
 * @param {Object[]} widgetAreas Widget areas to save.
 * @return {Function} An action creator.
 */

const saveWidgetAreas = widgetAreas => async _ref3 => {
  let {
    dispatch,
    registry
  } = _ref3;

  try {
    for (const widgetArea of widgetAreas) {
      await dispatch.saveWidgetArea(widgetArea.id);
    }
  } finally {
    // saveEditedEntityRecord resets the resolution status, let's fix it manually.
    await registry.dispatch(external_wp_coreData_namespaceObject.store).finishResolution('getEntityRecord', KIND, WIDGET_AREA_ENTITY_TYPE, buildWidgetAreasQuery());
  }
};
/**
 * Converts all the blocks from a widget area specified by ID into widgets,
 * and submits a batch request to save everything at once.
 *
 * @param {string} widgetAreaId ID of the widget area to process.
 * @return {Function} An action creator.
 */

const saveWidgetArea = widgetAreaId => async _ref4 => {
  let {
    dispatch,
    select,
    registry
  } = _ref4;
  const widgets = select.getWidgets();
  const post = registry.select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord(KIND, POST_TYPE, buildWidgetAreaPostId(widgetAreaId)); // Get all widgets from this area

  const areaWidgets = Object.values(widgets).filter(_ref5 => {
    let {
      sidebar
    } = _ref5;
    return sidebar === widgetAreaId;
  }); // Remove all duplicate reference widget instances for legacy widgets.
  // Why? We filter out the widgets with duplicate IDs to prevent adding more than one instance of a widget
  // implemented using a function. WordPress doesn't support having more than one instance of these, if you try to
  // save multiple instances of these in different sidebars you will run into undefined behaviors.

  const usedReferenceWidgets = [];
  const widgetsBlocks = post.blocks.filter(block => {
    const {
      id
    } = block.attributes;

    if (block.name === 'core/legacy-widget' && id) {
      if (usedReferenceWidgets.includes(id)) {
        return false;
      }

      usedReferenceWidgets.push(id);
    }

    return true;
  }); // Determine which widgets have been deleted. We can tell if a widget is
  // deleted and not just moved to a different area by looking to see if
  // getWidgetAreaForWidgetId() finds something.

  const deletedWidgets = [];

  for (const widget of areaWidgets) {
    const widgetsNewArea = select.getWidgetAreaForWidgetId(widget.id);

    if (!widgetsNewArea) {
      deletedWidgets.push(widget);
    }
  }

  const batchMeta = [];
  const batchTasks = [];
  const sidebarWidgetsIds = [];

  for (let i = 0; i < widgetsBlocks.length; i++) {
    const block = widgetsBlocks[i];
    const widgetId = (0,external_wp_widgets_namespaceObject.getWidgetIdFromBlock)(block);
    const oldWidget = widgets[widgetId];
    const widget = transformBlockToWidget(block, oldWidget); // We'll replace the null widgetId after save, but we track it here
    // since order is important.

    sidebarWidgetsIds.push(widgetId); // Check oldWidget as widgetId might refer to an ID which has been
    // deleted, e.g. if a deleted block is restored via undo after saving.

    if (oldWidget) {
      // Update an existing widget.
      registry.dispatch(external_wp_coreData_namespaceObject.store).editEntityRecord('root', 'widget', widgetId, { ...widget,
        sidebar: widgetAreaId
      }, {
        undoIgnore: true
      });
      const hasEdits = registry.select(external_wp_coreData_namespaceObject.store).hasEditsForEntityRecord('root', 'widget', widgetId);

      if (!hasEdits) {
        continue;
      }

      batchTasks.push(_ref6 => {
        let {
          saveEditedEntityRecord
        } = _ref6;
        return saveEditedEntityRecord('root', 'widget', widgetId);
      });
    } else {
      // Create a new widget.
      batchTasks.push(_ref7 => {
        let {
          saveEntityRecord
        } = _ref7;
        return saveEntityRecord('root', 'widget', { ...widget,
          sidebar: widgetAreaId
        });
      });
    }

    batchMeta.push({
      block,
      position: i,
      clientId: block.clientId
    });
  }

  for (const widget of deletedWidgets) {
    batchTasks.push(_ref8 => {
      let {
        deleteEntityRecord
      } = _ref8;
      return deleteEntityRecord('root', 'widget', widget.id, {
        force: true
      });
    });
  }

  const records = await registry.dispatch(external_wp_coreData_namespaceObject.store).__experimentalBatch(batchTasks);
  const preservedRecords = records.filter(record => !record.hasOwnProperty('deleted'));
  const failedWidgetNames = [];

  for (let i = 0; i < preservedRecords.length; i++) {
    const widget = preservedRecords[i];
    const {
      block,
      position
    } = batchMeta[i]; // Set __internalWidgetId on the block. This will be persisted to the
    // store when we dispatch receiveEntityRecords( post ) below.

    post.blocks[position].attributes.__internalWidgetId = widget.id;
    const error = registry.select(external_wp_coreData_namespaceObject.store).getLastEntitySaveError('root', 'widget', widget.id);

    if (error) {
      var _block$attributes;

      failedWidgetNames.push(((_block$attributes = block.attributes) === null || _block$attributes === void 0 ? void 0 : _block$attributes.name) || (block === null || block === void 0 ? void 0 : block.name));
    }

    if (!sidebarWidgetsIds[position]) {
      sidebarWidgetsIds[position] = widget.id;
    }
  }

  if (failedWidgetNames.length) {
    throw new Error((0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: %s: List of widget names */
    (0,external_wp_i18n_namespaceObject.__)('Could not save the following widgets: %s.'), failedWidgetNames.join(', ')));
  }

  registry.dispatch(external_wp_coreData_namespaceObject.store).editEntityRecord(KIND, WIDGET_AREA_ENTITY_TYPE, widgetAreaId, {
    widgets: sidebarWidgetsIds
  }, {
    undoIgnore: true
  });
  dispatch(trySaveWidgetArea(widgetAreaId));
  registry.dispatch(external_wp_coreData_namespaceObject.store).receiveEntityRecords(KIND, POST_TYPE, post, undefined);
};

const trySaveWidgetArea = widgetAreaId => _ref9 => {
  let {
    registry
  } = _ref9;
  registry.dispatch(external_wp_coreData_namespaceObject.store).saveEditedEntityRecord(KIND, WIDGET_AREA_ENTITY_TYPE, widgetAreaId, {
    throwOnError: true
  });
};
/**
 * Sets the clientId stored for a particular widgetId.
 *
 * @param {number} clientId Client id.
 * @param {number} widgetId Widget id.
 *
 * @return {Object} Action.
 */


function setWidgetIdForClientId(clientId, widgetId) {
  return {
    type: 'SET_WIDGET_ID_FOR_CLIENT_ID',
    clientId,
    widgetId
  };
}
/**
 * Sets the open state of all the widget areas.
 *
 * @param {Object} widgetAreasOpenState The open states of all the widget areas.
 *
 * @return {Object} Action.
 */

function setWidgetAreasOpenState(widgetAreasOpenState) {
  return {
    type: 'SET_WIDGET_AREAS_OPEN_STATE',
    widgetAreasOpenState
  };
}
/**
 * Sets the open state of the widget area.
 *
 * @param {string}  clientId The clientId of the widget area.
 * @param {boolean} isOpen   Whether the widget area should be opened.
 *
 * @return {Object} Action.
 */

function setIsWidgetAreaOpen(clientId, isOpen) {
  return {
    type: 'SET_IS_WIDGET_AREA_OPEN',
    clientId,
    isOpen
  };
}
/**
 * Returns an action object used to open/close the inserter.
 *
 * @param {boolean|Object} value                Whether the inserter should be
 *                                              opened (true) or closed (false).
 *                                              To specify an insertion point,
 *                                              use an object.
 * @param {string}         value.rootClientId   The root client ID to insert at.
 * @param {number}         value.insertionIndex The index to insert at.
 *
 * @return {Object} Action object.
 */

function setIsInserterOpened(value) {
  return {
    type: 'SET_IS_INSERTER_OPENED',
    value
  };
}
/**
 * Returns an action object used to open/close the list view.
 *
 * @param {boolean} isOpen A boolean representing whether the list view should be opened or closed.
 * @return {Object} Action object.
 */

function setIsListViewOpened(isOpen) {
  return {
    type: 'SET_IS_LIST_VIEW_OPENED',
    isOpen
  };
}
/**
 * Returns an action object signalling that the user closed the sidebar.
 *
 * @return {Object} Action creator.
 */

const closeGeneralSidebar = () => _ref10 => {
  let {
    registry
  } = _ref10;
  registry.dispatch(store).disableComplementaryArea(constants_STORE_NAME);
};
/**
 * Action that handles moving a block between widget areas
 *
 * @param {string} clientId     The clientId of the block to move.
 * @param {string} widgetAreaId The id of the widget area to move the block to.
 */

const moveBlockToWidgetArea = (clientId, widgetAreaId) => async _ref11 => {
  let {
    dispatch,
    select,
    registry
  } = _ref11;
  const sourceRootClientId = registry.select(external_wp_blockEditor_namespaceObject.store).getBlockRootClientId(clientId); // Search the top level blocks (widget areas) for the one with the matching
  // id attribute. Makes the assumption that all top-level blocks are widget
  // areas.

  const widgetAreas = registry.select(external_wp_blockEditor_namespaceObject.store).getBlocks();
  const destinationWidgetAreaBlock = widgetAreas.find(_ref12 => {
    let {
      attributes
    } = _ref12;
    return attributes.id === widgetAreaId;
  });
  const destinationRootClientId = destinationWidgetAreaBlock.clientId; // Get the index for moving to the end of the destination widget area.

  const destinationInnerBlocksClientIds = registry.select(external_wp_blockEditor_namespaceObject.store).getBlockOrder(destinationRootClientId);
  const destinationIndex = destinationInnerBlocksClientIds.length; // Reveal the widget area, if it's not open.

  const isDestinationWidgetAreaOpen = select.getIsWidgetAreaOpen(destinationRootClientId);

  if (!isDestinationWidgetAreaOpen) {
    dispatch.setIsWidgetAreaOpen(destinationRootClientId, true);
  } // Move the block.


  registry.dispatch(external_wp_blockEditor_namespaceObject.store).moveBlocksToPosition([clientId], sourceRootClientId, destinationRootClientId, destinationIndex);
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/store/resolvers.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




/**
 * Creates a "stub" widgets post reflecting all available widget areas. The
 * post is meant as a convenient to only exists in runtime and should never be saved. It
 * enables a convenient way of editing the widgets by using a regular post editor.
 *
 * Fetches all widgets from all widgets aras, converts them into blocks, and hydrates a new post with them.
 *
 * @return {Function} An action creator.
 */

const getWidgetAreas = () => async _ref => {
  let {
    dispatch,
    registry
  } = _ref;
  const query = buildWidgetAreasQuery();
  const widgetAreas = await registry.resolveSelect(external_wp_coreData_namespaceObject.store).getEntityRecords(KIND, WIDGET_AREA_ENTITY_TYPE, query);
  const widgetAreaBlocks = [];
  const sortedWidgetAreas = widgetAreas.sort((a, b) => {
    if (a.id === 'wp_inactive_widgets') {
      return 1;
    }

    if (b.id === 'wp_inactive_widgets') {
      return -1;
    }

    return 0;
  });

  for (const widgetArea of sortedWidgetAreas) {
    widgetAreaBlocks.push((0,external_wp_blocks_namespaceObject.createBlock)('core/widget-area', {
      id: widgetArea.id,
      name: widgetArea.name
    }));

    if (!widgetArea.widgets.length) {
      // If this widget area has no widgets, it won't get a post setup by
      // the getWidgets resolver.
      dispatch(persistStubPost(buildWidgetAreaPostId(widgetArea.id), []));
    }
  }

  const widgetAreasOpenState = {};
  widgetAreaBlocks.forEach((widgetAreaBlock, index) => {
    // Defaults to open the first widget area.
    widgetAreasOpenState[widgetAreaBlock.clientId] = index === 0;
  });
  dispatch(setWidgetAreasOpenState(widgetAreasOpenState));
  dispatch(persistStubPost(buildWidgetAreasPostId(), widgetAreaBlocks));
};
/**
 * Fetches all widgets from all widgets ares, and groups them by widget area Id.
 *
 * @return {Function} An action creator.
 */

const getWidgets = () => async _ref2 => {
  let {
    dispatch,
    registry
  } = _ref2;
  const query = buildWidgetsQuery();
  const widgets = await registry.resolveSelect(external_wp_coreData_namespaceObject.store).getEntityRecords('root', 'widget', query);
  const groupedBySidebar = {};

  for (const widget of widgets) {
    const block = transformWidgetToBlock(widget);
    groupedBySidebar[widget.sidebar] = groupedBySidebar[widget.sidebar] || [];
    groupedBySidebar[widget.sidebar].push(block);
  }

  for (const sidebarId in groupedBySidebar) {
    if (groupedBySidebar.hasOwnProperty(sidebarId)) {
      // Persist the actual post containing the widget block
      dispatch(persistStubPost(buildWidgetAreaPostId(sidebarId), groupedBySidebar[sidebarId]));
    }
  }
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/store/selectors.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



/**
 * Returns all API widgets.
 *
 * @return {Object[]} API List of widgets.
 */

const selectors_getWidgets = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  const widgets = select(external_wp_coreData_namespaceObject.store).getEntityRecords('root', 'widget', buildWidgetsQuery());
  return (// Key widgets by their ID.
    (widgets === null || widgets === void 0 ? void 0 : widgets.reduce((allWidgets, widget) => ({ ...allWidgets,
      [widget.id]: widget
    }), {})) || {}
  );
});
/**
 * Returns API widget data for a particular widget ID.
 *
 * @param {number} id Widget ID.
 *
 * @return {Object} API widget data for a particular widget ID.
 */

const getWidget = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, id) => {
  const widgets = select(constants_STORE_NAME).getWidgets();
  return widgets[id];
});
/**
 * Returns all API widget areas.
 *
 * @return {Object[]} API List of widget areas.
 */

const selectors_getWidgetAreas = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  const query = buildWidgetAreasQuery();
  return select(external_wp_coreData_namespaceObject.store).getEntityRecords(KIND, WIDGET_AREA_ENTITY_TYPE, query);
});
/**
 * Returns widgetArea containing a block identify by given widgetId
 *
 * @param {string} widgetId The ID of the widget.
 * @return {Object} Containing widget area.
 */

const getWidgetAreaForWidgetId = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, widgetId) => {
  const widgetAreas = select(constants_STORE_NAME).getWidgetAreas();
  return widgetAreas.find(widgetArea => {
    const post = select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord(KIND, POST_TYPE, buildWidgetAreaPostId(widgetArea.id));
    const blockWidgetIds = post.blocks.map(block => (0,external_wp_widgets_namespaceObject.getWidgetIdFromBlock)(block));
    return blockWidgetIds.includes(widgetId);
  });
});
/**
 * Given a child client id, returns the parent widget area block.
 *
 * @param {string} clientId The client id of a block in a widget area.
 *
 * @return {WPBlock} The widget area block.
 */

const getParentWidgetAreaBlock = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, clientId) => {
  const {
    getBlock,
    getBlockName,
    getBlockParents
  } = select(external_wp_blockEditor_namespaceObject.store);
  const blockParents = getBlockParents(clientId);
  const widgetAreaClientId = blockParents.find(parentClientId => getBlockName(parentClientId) === 'core/widget-area');
  return getBlock(widgetAreaClientId);
});
/**
 * Returns all edited widget area entity records.
 *
 * @return {Object[]} List of edited widget area entity records.
 */

const getEditedWidgetAreas = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, ids) => {
  let widgetAreas = select(constants_STORE_NAME).getWidgetAreas();

  if (!widgetAreas) {
    return [];
  }

  if (ids) {
    widgetAreas = widgetAreas.filter(_ref => {
      let {
        id
      } = _ref;
      return ids.includes(id);
    });
  }

  return widgetAreas.filter(_ref2 => {
    let {
      id
    } = _ref2;
    return select(external_wp_coreData_namespaceObject.store).hasEditsForEntityRecord(KIND, POST_TYPE, buildWidgetAreaPostId(id));
  }).map(_ref3 => {
    let {
      id
    } = _ref3;
    return select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord(KIND, WIDGET_AREA_ENTITY_TYPE, id);
  });
});
/**
 * Returns all blocks representing reference widgets.
 *
 * @param {string} referenceWidgetName Optional. If given, only reference widgets with this name will be returned.
 * @return {Array}  List of all blocks representing reference widgets
 */

const getReferenceWidgetBlocks = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => function (state) {
  let referenceWidgetName = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  const results = [];
  const widgetAreas = select(constants_STORE_NAME).getWidgetAreas();

  for (const _widgetArea of widgetAreas) {
    const post = select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord(KIND, POST_TYPE, buildWidgetAreaPostId(_widgetArea.id));

    for (const block of post.blocks) {
      var _block$attributes;

      if (block.name === 'core/legacy-widget' && (!referenceWidgetName || ((_block$attributes = block.attributes) === null || _block$attributes === void 0 ? void 0 : _block$attributes.referenceWidgetName) === referenceWidgetName)) {
        results.push(block);
      }
    }
  }

  return results;
});
/**
 * Returns true if any widget area is currently being saved.
 *
 * @return {boolean} True if any widget area is currently being saved. False otherwise.
 */

const isSavingWidgetAreas = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  var _select$getWidgetArea;

  const widgetAreasIds = (_select$getWidgetArea = select(constants_STORE_NAME).getWidgetAreas()) === null || _select$getWidgetArea === void 0 ? void 0 : _select$getWidgetArea.map(_ref4 => {
    let {
      id
    } = _ref4;
    return id;
  });

  if (!widgetAreasIds) {
    return false;
  }

  for (const id of widgetAreasIds) {
    const isSaving = select(external_wp_coreData_namespaceObject.store).isSavingEntityRecord(KIND, WIDGET_AREA_ENTITY_TYPE, id);

    if (isSaving) {
      return true;
    }
  }

  const widgetIds = [...Object.keys(select(constants_STORE_NAME).getWidgets()), undefined // account for new widgets without an ID
  ];

  for (const id of widgetIds) {
    const isSaving = select(external_wp_coreData_namespaceObject.store).isSavingEntityRecord('root', 'widget', id);

    if (isSaving) {
      return true;
    }
  }

  return false;
});
/**
 * Gets whether the widget area is opened.
 *
 * @param {Array}  state    The open state of the widget areas.
 * @param {string} clientId The clientId of the widget area.
 *
 * @return {boolean} True if the widget area is open.
 */

const getIsWidgetAreaOpen = (state, clientId) => {
  const {
    widgetAreasOpenState
  } = state;
  return !!widgetAreasOpenState[clientId];
};
/**
 * Returns true if the inserter is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the inserter is opened.
 */

function isInserterOpened(state) {
  return !!state.blockInserterPanel;
}
/**
 * Get the insertion point for the inserter.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} The root client ID and index to insert at.
 */

function __experimentalGetInsertionPoint(state) {
  const {
    rootClientId,
    insertionIndex
  } = state.blockInserterPanel;
  return {
    rootClientId,
    insertionIndex
  };
}
/**
 * Returns true if a block can be inserted into a widget area.
 *
 * @param {Array}  state     The open state of the widget areas.
 * @param {string} blockName The name of the block being inserted.
 *
 * @return {boolean} True if the block can be inserted in a widget area.
 */

const canInsertBlockInWidgetArea = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, blockName) => {
  // Widget areas are always top-level blocks, which getBlocks will return.
  const widgetAreas = select(external_wp_blockEditor_namespaceObject.store).getBlocks(); // Makes an assumption that a block that can be inserted into one
  // widget area can be inserted into any widget area. Uses the first
  // widget area for testing whether the block can be inserted.

  const [firstWidgetArea] = widgetAreas;
  return select(external_wp_blockEditor_namespaceObject.store).canInsertBlockType(blockName, firstWidgetArea.clientId);
});
/**
 * Returns true if the list view is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the list view is opened.
 */

function isListViewOpened(state) {
  return state.listViewPanel;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/store/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */






/**
 * Block editor data store configuration.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#register
 *
 * @type {Object}
 */

const storeConfig = {
  reducer: reducer,
  selectors: store_selectors_namespaceObject,
  resolvers: resolvers_namespaceObject,
  actions: store_actions_namespaceObject
};
/**
 * Store definition for the edit widgets namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store_store = (0,external_wp_data_namespaceObject.createReduxStore)(constants_STORE_NAME, storeConfig);
(0,external_wp_data_namespaceObject.register)(store_store); // This package uses a few in-memory post types as wrappers for convenience.
// This middleware prevents any network requests related to these types as they are
// bound to fail anyway.

external_wp_apiFetch_default().use(function (options, next) {
  var _options$path;

  if (((_options$path = options.path) === null || _options$path === void 0 ? void 0 : _options$path.indexOf('/wp/v2/types/widget-area')) === 0) {
    return Promise.resolve({});
  }

  return next(options);
});

;// CONCATENATED MODULE: external ["wp","hooks"]
var external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/filters/move-to-widget-area.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


const withMoveToWidgetAreaToolbarItem = (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(BlockEdit => props => {
  const {
    clientId,
    name: blockName
  } = props;
  const {
    widgetAreas,
    currentWidgetAreaId,
    canInsertBlockInWidgetArea
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _widgetAreaBlock$attr;

    // Component won't display for a widget area, so don't run selectors.
    if (blockName === 'core/widget-area') {
      return {};
    }

    const selectors = select(store_store);
    const widgetAreaBlock = selectors.getParentWidgetAreaBlock(clientId);
    return {
      widgetAreas: selectors.getWidgetAreas(),
      currentWidgetAreaId: widgetAreaBlock === null || widgetAreaBlock === void 0 ? void 0 : (_widgetAreaBlock$attr = widgetAreaBlock.attributes) === null || _widgetAreaBlock$attr === void 0 ? void 0 : _widgetAreaBlock$attr.id,
      canInsertBlockInWidgetArea: selectors.canInsertBlockInWidgetArea(blockName)
    };
  }, [clientId, blockName]);
  const {
    moveBlockToWidgetArea
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const hasMultipleWidgetAreas = (widgetAreas === null || widgetAreas === void 0 ? void 0 : widgetAreas.length) > 1;
  const isMoveToWidgetAreaVisible = blockName !== 'core/widget-area' && hasMultipleWidgetAreas && canInsertBlockInWidgetArea;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(BlockEdit, props), isMoveToWidgetAreaVisible && (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockControls, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_widgets_namespaceObject.MoveToWidgetArea, {
    widgetAreas: widgetAreas,
    currentWidgetAreaId: currentWidgetAreaId,
    onSelect: widgetAreaId => {
      moveBlockToWidgetArea(props.clientId, widgetAreaId);
    }
  })));
}, 'withMoveToWidgetAreaToolbarItem');
(0,external_wp_hooks_namespaceObject.addFilter)('editor.BlockEdit', 'core/edit-widgets/block-edit', withMoveToWidgetAreaToolbarItem);

;// CONCATENATED MODULE: external ["wp","mediaUtils"]
var external_wp_mediaUtils_namespaceObject = window["wp"]["mediaUtils"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/filters/replace-media-upload.js
/**
 * WordPress dependencies
 */



const replaceMediaUpload = () => external_wp_mediaUtils_namespaceObject.MediaUpload;

(0,external_wp_hooks_namespaceObject.addFilter)('editor.MediaUpload', 'core/edit-widgets/replace-media-upload', replaceMediaUpload);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/filters/index.js
/**
 * Internal dependencies
 */



;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/blocks/widget-area/edit/use-is-dragging-within.js
/**
 * WordPress dependencies
 */

/** @typedef {import('@wordpress/element').RefObject} RefObject */

/**
 * A React hook to determine if it's dragging within the target element.
 *
 * @param {RefObject<HTMLElement>} elementRef The target elementRef object.
 *
 * @return {boolean} Is dragging within the target element.
 */

const useIsDraggingWithin = elementRef => {
  const [isDraggingWithin, setIsDraggingWithin] = (0,external_wp_element_namespaceObject.useState)(false);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const {
      ownerDocument
    } = elementRef.current;

    function handleDragStart(event) {
      // Check the first time when the dragging starts.
      handleDragEnter(event);
    } // Set to false whenever the user cancel the drag event by either releasing the mouse or press Escape.


    function handleDragEnd() {
      setIsDraggingWithin(false);
    }

    function handleDragEnter(event) {
      // Check if the current target is inside the item element.
      if (elementRef.current.contains(event.target)) {
        setIsDraggingWithin(true);
      } else {
        setIsDraggingWithin(false);
      }
    } // Bind these events to the document to catch all drag events.
    // Ideally, we can also use `event.relatedTarget`, but sadly that doesn't work in Safari.


    ownerDocument.addEventListener('dragstart', handleDragStart);
    ownerDocument.addEventListener('dragend', handleDragEnd);
    ownerDocument.addEventListener('dragenter', handleDragEnter);
    return () => {
      ownerDocument.removeEventListener('dragstart', handleDragStart);
      ownerDocument.removeEventListener('dragend', handleDragEnd);
      ownerDocument.removeEventListener('dragenter', handleDragEnter);
    };
  }, []);
  return isDraggingWithin;
};

/* harmony default export */ var use_is_dragging_within = (useIsDraggingWithin);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/blocks/widget-area/edit/inner-blocks.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function WidgetAreaInnerBlocks(_ref) {
  let {
    id
  } = _ref;
  const [blocks, onInput, onChange] = (0,external_wp_coreData_namespaceObject.useEntityBlockEditor)('root', 'postType');
  const innerBlocksRef = (0,external_wp_element_namespaceObject.useRef)();
  const isDraggingWithinInnerBlocks = use_is_dragging_within(innerBlocksRef);
  const shouldHighlightDropZone = isDraggingWithinInnerBlocks; // Using the experimental hook so that we can control the className of the element.

  const innerBlocksProps = (0,external_wp_blockEditor_namespaceObject.useInnerBlocksProps)({
    ref: innerBlocksRef
  }, {
    value: blocks,
    onInput,
    onChange,
    templateLock: false,
    renderAppender: external_wp_blockEditor_namespaceObject.InnerBlocks.ButtonBlockAppender
  });
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    "data-widget-area-id": id,
    className: classnames_default()('wp-block-widget-area__inner-blocks block-editor-inner-blocks editor-styles-wrapper', {
      'wp-block-widget-area__highlight-drop-zone': shouldHighlightDropZone
    })
  }, (0,external_wp_element_namespaceObject.createElement)("div", innerBlocksProps));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/blocks/widget-area/edit/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




/** @typedef {import('@wordpress/element').RefObject} RefObject */

function WidgetAreaEdit(_ref) {
  let {
    clientId,
    className,
    attributes: {
      id,
      name
    }
  } = _ref;
  const isOpen = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getIsWidgetAreaOpen(clientId), [clientId]);
  const {
    setIsWidgetAreaOpen
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const wrapper = (0,external_wp_element_namespaceObject.useRef)();
  const setOpen = (0,external_wp_element_namespaceObject.useCallback)(openState => setIsWidgetAreaOpen(clientId, openState), [clientId]);
  const isDragging = useIsDragging(wrapper);
  const isDraggingWithin = use_is_dragging_within(wrapper);
  const [openedWhileDragging, setOpenedWhileDragging] = (0,external_wp_element_namespaceObject.useState)(false);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!isDragging) {
      setOpenedWhileDragging(false);
      return;
    }

    if (isDraggingWithin && !isOpen) {
      setOpen(true);
      setOpenedWhileDragging(true);
    } else if (!isDraggingWithin && isOpen && openedWhileDragging) {
      setOpen(false);
    }
  }, [isOpen, isDragging, isDraggingWithin, openedWhileDragging]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Panel, {
    className: className,
    ref: wrapper
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: name,
    opened: isOpen,
    onToggle: () => {
      setIsWidgetAreaOpen(clientId, !isOpen);
    },
    scrollAfterOpen: !isDragging
  }, _ref2 => {
    let {
      opened
    } = _ref2;
    return (// This is required to ensure LegacyWidget blocks are not
      // unmounted when the panel is collapsed. Unmounting legacy
      // widgets may have unintended consequences (e.g.  TinyMCE
      // not being properly reinitialized)
      (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableDisclosureContent, {
        className: "wp-block-widget-area__panel-body-content",
        visible: opened
      }, (0,external_wp_element_namespaceObject.createElement)(external_wp_coreData_namespaceObject.EntityProvider, {
        kind: "root",
        type: "postType",
        id: `widget-area-${id}`
      }, (0,external_wp_element_namespaceObject.createElement)(WidgetAreaInnerBlocks, {
        id: id
      })))
    );
  }));
}
/**
 * A React hook to determine if dragging is active.
 *
 * @param {RefObject<HTMLElement>} elementRef The target elementRef object.
 *
 * @return {boolean} Is dragging within the entire document.
 */

const useIsDragging = elementRef => {
  const [isDragging, setIsDragging] = (0,external_wp_element_namespaceObject.useState)(false);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const {
      ownerDocument
    } = elementRef.current;

    function handleDragStart() {
      setIsDragging(true);
    }

    function handleDragEnd() {
      setIsDragging(false);
    }

    ownerDocument.addEventListener('dragstart', handleDragStart);
    ownerDocument.addEventListener('dragend', handleDragEnd);
    return () => {
      ownerDocument.removeEventListener('dragstart', handleDragStart);
      ownerDocument.removeEventListener('dragend', handleDragEnd);
    };
  }, []);
  return isDragging;
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/blocks/widget-area/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */

const metadata = {
  name: "core/widget-area",
  category: "widgets",
  attributes: {
    id: {
      type: "string"
    },
    name: {
      type: "string"
    }
  },
  supports: {
    html: false,
    inserter: false,
    customClassName: false,
    reusable: false,
    __experimentalToolbar: false,
    __experimentalParentSelector: false,
    __experimentalDisableBlockOverlay: true
  },
  editorStyle: "wp-block-widget-area-editor",
  style: "wp-block-widget-area"
};

const {
  name: widget_area_name
} = metadata;

const settings = {
  title: (0,external_wp_i18n_namespaceObject.__)('Widget Area'),
  description: (0,external_wp_i18n_namespaceObject.__)('A widget area container.'),
  __experimentalLabel: _ref => {
    let {
      name: label
    } = _ref;
    return label;
  },
  edit: WidgetAreaEdit
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/error-boundary/index.js


/**
 * WordPress dependencies
 */







function CopyButton(_ref) {
  let {
    text,
    children
  } = _ref;
  const ref = (0,external_wp_compose_namespaceObject.useCopyToClipboard)(text);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "secondary",
    ref: ref
  }, children);
}

function ErrorBoundaryWarning(_ref2) {
  let {
    message,
    error
  } = _ref2;
  const actions = [(0,external_wp_element_namespaceObject.createElement)(CopyButton, {
    key: "copy-error",
    text: error.stack
  }, (0,external_wp_i18n_namespaceObject.__)('Copy Error'))];
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.Warning, {
    className: "edit-widgets-error-boundary",
    actions: actions
  }, message);
}

class ErrorBoundary extends external_wp_element_namespaceObject.Component {
  constructor() {
    super(...arguments);
    this.state = {
      error: null
    };
  }

  componentDidCatch(error) {
    (0,external_wp_hooks_namespaceObject.doAction)('editor.ErrorBoundary.errorLogged', error);
  }

  static getDerivedStateFromError(error) {
    return {
      error
    };
  }

  render() {
    if (!this.state.error) {
      return this.props.children;
    }

    return (0,external_wp_element_namespaceObject.createElement)(ErrorBoundaryWarning, {
      message: (0,external_wp_i18n_namespaceObject.__)('The editor has encountered an unexpected error.'),
      error: this.state.error
    });
  }

}

;// CONCATENATED MODULE: external ["wp","reusableBlocks"]
var external_wp_reusableBlocks_namespaceObject = window["wp"]["reusableBlocks"];
;// CONCATENATED MODULE: external ["wp","keyboardShortcuts"]
var external_wp_keyboardShortcuts_namespaceObject = window["wp"]["keyboardShortcuts"];
;// CONCATENATED MODULE: external ["wp","keycodes"]
var external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/keyboard-shortcuts/index.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function KeyboardShortcuts() {
  const {
    redo,
    undo
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const {
    saveEditedWidgetAreas
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-widgets/undo', event => {
    undo();
    event.preventDefault();
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-widgets/redo', event => {
    redo();
    event.preventDefault();
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-widgets/save', event => {
    event.preventDefault();
    saveEditedWidgetAreas();
  });
  return null;
}

function KeyboardShortcutsRegister() {
  // Registering the shortcuts.
  const {
    registerShortcut
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_keyboardShortcuts_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    registerShortcut({
      name: 'core/edit-widgets/undo',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Undo your last changes.'),
      keyCombination: {
        modifier: 'primary',
        character: 'z'
      }
    });
    registerShortcut({
      name: 'core/edit-widgets/redo',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Redo your last undo.'),
      keyCombination: {
        modifier: 'primaryShift',
        character: 'z'
      },
      // Disable on Apple OS because it conflicts with the browser's
      // history shortcut. It's a fine alias for both Windows and Linux.
      // Since there's no conflict for Ctrl+Shift+Z on both Windows and
      // Linux, we keep it as the default for consistency.
      aliases: (0,external_wp_keycodes_namespaceObject.isAppleOS)() ? [] : [{
        modifier: 'primary',
        character: 'y'
      }]
    });
    registerShortcut({
      name: 'core/edit-widgets/save',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Save your changes.'),
      keyCombination: {
        modifier: 'primary',
        character: 's'
      }
    });
    registerShortcut({
      name: 'core/edit-widgets/keyboard-shortcuts',
      category: 'main',
      description: (0,external_wp_i18n_namespaceObject.__)('Display these keyboard shortcuts.'),
      keyCombination: {
        modifier: 'access',
        character: 'h'
      }
    });
    registerShortcut({
      name: 'core/edit-widgets/next-region',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Navigate to the next part of the editor.'),
      keyCombination: {
        modifier: 'ctrl',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'n'
      }]
    });
    registerShortcut({
      name: 'core/edit-widgets/previous-region',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Navigate to the previous part of the editor.'),
      keyCombination: {
        modifier: 'ctrlShift',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'p'
      }, {
        modifier: 'ctrlShift',
        character: '~'
      }]
    });
  }, [registerShortcut]);
  return null;
}

KeyboardShortcuts.Register = KeyboardShortcutsRegister;
/* harmony default export */ var keyboard_shortcuts = (KeyboardShortcuts);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/hooks/use-last-selected-widget-area.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



/**
 * A react hook that returns the client id of the last widget area to have
 * been selected, or to have a selected block within it.
 *
 * @return {string} clientId of the widget area last selected.
 */

const useLastSelectedWidgetArea = () => (0,external_wp_data_namespaceObject.useSelect)(select => {
  var _widgetAreasPost$bloc;

  const {
    getBlockSelectionEnd,
    getBlockName
  } = select(external_wp_blockEditor_namespaceObject.store);
  const selectionEndClientId = getBlockSelectionEnd(); // If the selected block is a widget area, return its clientId.

  if (getBlockName(selectionEndClientId) === 'core/widget-area') {
    return selectionEndClientId;
  }

  const {
    getParentWidgetAreaBlock
  } = select(store_store);
  const widgetAreaBlock = getParentWidgetAreaBlock(selectionEndClientId);
  const widgetAreaBlockClientId = widgetAreaBlock === null || widgetAreaBlock === void 0 ? void 0 : widgetAreaBlock.clientId;

  if (widgetAreaBlockClientId) {
    return widgetAreaBlockClientId;
  } // If no widget area has been selected, return the clientId of the first
  // area.


  const {
    getEntityRecord
  } = select(external_wp_coreData_namespaceObject.store);
  const widgetAreasPost = getEntityRecord(KIND, POST_TYPE, buildWidgetAreasPostId());
  return widgetAreasPost === null || widgetAreasPost === void 0 ? void 0 : (_widgetAreasPost$bloc = widgetAreasPost.blocks[0]) === null || _widgetAreasPost$bloc === void 0 ? void 0 : _widgetAreasPost$bloc.clientId;
}, []);

/* harmony default export */ var use_last_selected_widget_area = (useLastSelectedWidgetArea);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/constants.js
const ALLOW_REUSABLE_BLOCKS = false;
const ENABLE_EXPERIMENTAL_FSE_BLOCKS = false;

;// CONCATENATED MODULE: external ["wp","privateApis"]
var external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/private-apis.js
/**
 * WordPress dependencies
 */

const {
  lock,
  unlock
} = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I know using unstable features means my plugin or theme will inevitably break on the next WordPress release.', '@wordpress/edit-widgets');

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/widget-areas-block-editor-provider/index.js



/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */







const {
  ExperimentalBlockEditorProvider
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
function WidgetAreasBlockEditorProvider(_ref) {
  let {
    blockEditorSettings,
    children,
    ...props
  } = _ref;
  const mediaPermissions = (0,external_wp_coreData_namespaceObject.useResourcePermissions)('media');
  const {
    reusableBlocks,
    isFixedToolbarActive,
    keepCaretInsideBlock
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    widgetAreas: select(store_store).getWidgetAreas(),
    widgets: select(store_store).getWidgets(),
    reusableBlocks: ALLOW_REUSABLE_BLOCKS ? select(external_wp_coreData_namespaceObject.store).getEntityRecords('postType', 'wp_block') : [],
    isFixedToolbarActive: !!select(external_wp_preferences_namespaceObject.store).get('core/edit-widgets', 'fixedToolbar'),
    keepCaretInsideBlock: !!select(external_wp_preferences_namespaceObject.store).get('core/edit-widgets', 'keepCaretInsideBlock')
  }), []);
  const {
    setIsInserterOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const settings = (0,external_wp_element_namespaceObject.useMemo)(() => {
    let mediaUploadBlockEditor;

    if (mediaPermissions.canCreate) {
      mediaUploadBlockEditor = _ref2 => {
        let {
          onError,
          ...argumentsObject
        } = _ref2;
        (0,external_wp_mediaUtils_namespaceObject.uploadMedia)({
          wpAllowedMimeTypes: blockEditorSettings.allowedMimeTypes,
          onError: _ref3 => {
            let {
              message
            } = _ref3;
            return onError(message);
          },
          ...argumentsObject
        });
      };
    }

    return { ...blockEditorSettings,
      __experimentalReusableBlocks: reusableBlocks,
      hasFixedToolbar: isFixedToolbarActive,
      keepCaretInsideBlock,
      mediaUpload: mediaUploadBlockEditor,
      templateLock: 'all',
      __experimentalSetIsInserterOpened: setIsInserterOpened
    };
  }, [blockEditorSettings, isFixedToolbarActive, keepCaretInsideBlock, mediaPermissions.canCreate, reusableBlocks, setIsInserterOpened]);
  const widgetAreaId = use_last_selected_widget_area();
  const [blocks, onInput, onChange] = (0,external_wp_coreData_namespaceObject.useEntityBlockEditor)(KIND, POST_TYPE, {
    id: buildWidgetAreasPostId()
  });
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_keyboardShortcuts_namespaceObject.ShortcutProvider, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockEditorKeyboardShortcuts.Register, null), (0,external_wp_element_namespaceObject.createElement)(keyboard_shortcuts.Register, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SlotFillProvider, null, (0,external_wp_element_namespaceObject.createElement)(ExperimentalBlockEditorProvider, _extends({
    value: blocks,
    onInput: onInput,
    onChange: onChange,
    settings: settings,
    useSubRegistry: false
  }, props), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.CopyHandler, null, children), (0,external_wp_element_namespaceObject.createElement)(external_wp_reusableBlocks_namespaceObject.ReusableBlocksMenuItems, {
    rootClientId: widgetAreaId
  }))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/cog.js


/**
 * WordPress dependencies
 */

const cog = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  d: "M10.289 4.836A1 1 0 0111.275 4h1.306a1 1 0 01.987.836l.244 1.466c.787.26 1.503.679 2.108 1.218l1.393-.522a1 1 0 011.216.437l.653 1.13a1 1 0 01-.23 1.273l-1.148.944a6.025 6.025 0 010 2.435l1.149.946a1 1 0 01.23 1.272l-.653 1.13a1 1 0 01-1.216.437l-1.394-.522c-.605.54-1.32.958-2.108 1.218l-.244 1.466a1 1 0 01-.987.836h-1.306a1 1 0 01-.986-.836l-.244-1.466a5.995 5.995 0 01-2.108-1.218l-1.394.522a1 1 0 01-1.217-.436l-.653-1.131a1 1 0 01.23-1.272l1.149-.946a6.026 6.026 0 010-2.435l-1.148-.944a1 1 0 01-.23-1.272l.653-1.131a1 1 0 011.217-.437l1.393.522a5.994 5.994 0 012.108-1.218l.244-1.466zM14.929 12a3 3 0 11-6 0 3 3 0 016 0z",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_cog = (cog);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/block-default.js


/**
 * WordPress dependencies
 */

const blockDefault = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M19 8h-1V6h-5v2h-2V6H6v2H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-8c0-1.1-.9-2-2-2zm.5 10c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5v-8c0-.3.2-.5.5-.5h14c.3 0 .5.2.5.5v8z"
}));
/* harmony default export */ var block_default = (blockDefault);

;// CONCATENATED MODULE: external ["wp","url"]
var external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: external ["wp","dom"]
var external_wp_dom_namespaceObject = window["wp"]["dom"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/sidebar/widget-areas.js


/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */


function WidgetAreas(_ref) {
  let {
    selectedWidgetAreaId
  } = _ref;
  const widgetAreas = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getWidgetAreas(), []);
  const selectedWidgetArea = (0,external_wp_element_namespaceObject.useMemo)(() => selectedWidgetAreaId && (widgetAreas === null || widgetAreas === void 0 ? void 0 : widgetAreas.find(widgetArea => widgetArea.id === selectedWidgetAreaId)), [selectedWidgetAreaId, widgetAreas]);
  let description;

  if (!selectedWidgetArea) {
    description = (0,external_wp_i18n_namespaceObject.__)('Widget Areas are global parts in your site’s layout that can accept blocks. These vary by theme, but are typically parts like your Sidebar or Footer.');
  } else if (selectedWidgetAreaId === 'wp_inactive_widgets') {
    description = (0,external_wp_i18n_namespaceObject.__)('Blocks in this Widget Area will not be displayed in your site.');
  } else {
    description = selectedWidgetArea.description;
  }

  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-widgets-widget-areas"
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-widgets-widget-areas__top-container"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockIcon, {
    icon: block_default
  }), (0,external_wp_element_namespaceObject.createElement)("div", null, (0,external_wp_element_namespaceObject.createElement)("p", {
    // Use `dangerouslySetInnerHTML` to keep backwards
    // compatibility. Basic markup in the description is an
    // established feature of WordPress.
    // @see https://github.com/WordPress/gutenberg/issues/33106
    dangerouslySetInnerHTML: {
      __html: (0,external_wp_dom_namespaceObject.safeHTML)(description)
    }
  }), (widgetAreas === null || widgetAreas === void 0 ? void 0 : widgetAreas.length) === 0 && (0,external_wp_element_namespaceObject.createElement)("p", null, (0,external_wp_i18n_namespaceObject.__)('Your theme does not contain any Widget Areas.')), !selectedWidgetArea && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    href: (0,external_wp_url_namespaceObject.addQueryArgs)('customize.php', {
      'autofocus[panel]': 'widgets',
      return: window.location.pathname
    }),
    variant: "tertiary"
  }, (0,external_wp_i18n_namespaceObject.__)('Manage with live preview')))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/sidebar/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








const SIDEBAR_ACTIVE_BY_DEFAULT = external_wp_element_namespaceObject.Platform.select({
  web: true,
  native: false
});
const BLOCK_INSPECTOR_IDENTIFIER = 'edit-widgets/block-inspector'; // Widget areas were one called block areas, so use 'edit-widgets/block-areas'
// for backwards compatibility.

const WIDGET_AREAS_IDENTIFIER = 'edit-widgets/block-areas';
/**
 * Internal dependencies
 */




function ComplementaryAreaTab(_ref) {
  let {
    identifier,
    label,
    isActive
  } = _ref;
  const {
    enableComplementaryArea
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    onClick: () => enableComplementaryArea(store_store.name, identifier),
    className: classnames_default()('edit-widgets-sidebar__panel-tab', {
      'is-active': isActive
    }),
    "aria-label": isActive ? // translators: %s: sidebar label e.g: "Widget Areas".
    (0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)('%s (selected)'), label) : label,
    "data-label": label
  }, label);
}

function Sidebar() {
  const {
    enableComplementaryArea
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const {
    currentArea,
    hasSelectedNonAreaBlock,
    isGeneralSidebarOpen,
    selectedWidgetAreaBlock
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getSelectedBlock,
      getBlock,
      getBlockParentsByBlockName
    } = select(external_wp_blockEditor_namespaceObject.store);
    const {
      getActiveComplementaryArea
    } = select(store);
    const selectedBlock = getSelectedBlock();
    const activeArea = getActiveComplementaryArea(store_store.name);
    let currentSelection = activeArea;

    if (!currentSelection) {
      if (selectedBlock) {
        currentSelection = BLOCK_INSPECTOR_IDENTIFIER;
      } else {
        currentSelection = WIDGET_AREAS_IDENTIFIER;
      }
    }

    let widgetAreaBlock;

    if (selectedBlock) {
      if (selectedBlock.name === 'core/widget-area') {
        widgetAreaBlock = selectedBlock;
      } else {
        widgetAreaBlock = getBlock(getBlockParentsByBlockName(selectedBlock.clientId, 'core/widget-area')[0]);
      }
    }

    return {
      currentArea: currentSelection,
      hasSelectedNonAreaBlock: !!(selectedBlock && selectedBlock.name !== 'core/widget-area'),
      isGeneralSidebarOpen: !!activeArea,
      selectedWidgetAreaBlock: widgetAreaBlock
    };
  }, []); // currentArea, and isGeneralSidebarOpen are intentionally left out from the dependencies,
  // because we want to run the effect when a block is selected/unselected and not when the sidebar state changes.

  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (hasSelectedNonAreaBlock && currentArea === WIDGET_AREAS_IDENTIFIER && isGeneralSidebarOpen) {
      enableComplementaryArea('core/edit-widgets', BLOCK_INSPECTOR_IDENTIFIER);
    }

    if (!hasSelectedNonAreaBlock && currentArea === BLOCK_INSPECTOR_IDENTIFIER && isGeneralSidebarOpen) {
      enableComplementaryArea('core/edit-widgets', WIDGET_AREAS_IDENTIFIER);
    }
  }, [hasSelectedNonAreaBlock, enableComplementaryArea]);
  return (0,external_wp_element_namespaceObject.createElement)(complementary_area, {
    className: "edit-widgets-sidebar",
    header: (0,external_wp_element_namespaceObject.createElement)("ul", null, (0,external_wp_element_namespaceObject.createElement)("li", null, (0,external_wp_element_namespaceObject.createElement)(ComplementaryAreaTab, {
      identifier: WIDGET_AREAS_IDENTIFIER,
      label: selectedWidgetAreaBlock ? selectedWidgetAreaBlock.attributes.name : (0,external_wp_i18n_namespaceObject.__)('Widget Areas'),
      isActive: currentArea === WIDGET_AREAS_IDENTIFIER
    })), (0,external_wp_element_namespaceObject.createElement)("li", null, (0,external_wp_element_namespaceObject.createElement)(ComplementaryAreaTab, {
      identifier: BLOCK_INSPECTOR_IDENTIFIER,
      label: (0,external_wp_i18n_namespaceObject.__)('Block'),
      isActive: currentArea === BLOCK_INSPECTOR_IDENTIFIER
    }))),
    headerClassName: "edit-widgets-sidebar__panel-tabs"
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    title: (0,external_wp_i18n_namespaceObject.__)('Settings'),
    closeLabel: (0,external_wp_i18n_namespaceObject.__)('Close settings'),
    scope: "core/edit-widgets",
    identifier: currentArea,
    icon: library_cog,
    isActiveByDefault: SIDEBAR_ACTIVE_BY_DEFAULT
  }, currentArea === WIDGET_AREAS_IDENTIFIER && (0,external_wp_element_namespaceObject.createElement)(WidgetAreas, {
    selectedWidgetAreaId: selectedWidgetAreaBlock === null || selectedWidgetAreaBlock === void 0 ? void 0 : selectedWidgetAreaBlock.attributes.id
  }), currentArea === BLOCK_INSPECTOR_IDENTIFIER && (hasSelectedNonAreaBlock ? (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockInspector, null) : // Pretend that Widget Areas are part of the UI by not
  // showing the Block Inspector when one is selected.
  (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "block-editor-block-inspector__no-blocks"
  }, (0,external_wp_i18n_namespaceObject.__)('No block selected.'))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/plus.js


/**
 * WordPress dependencies
 */

const plus = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18 11.2h-5.2V6h-1.6v5.2H6v1.6h5.2V18h1.6v-5.2H18z"
}));
/* harmony default export */ var library_plus = (plus);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/list-view.js


/**
 * WordPress dependencies
 */

const listView = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13.8 5.2H3v1.5h10.8V5.2zm-3.6 12v1.5H21v-1.5H10.2zm7.2-6H6.6v1.5h10.8v-1.5z"
}));
/* harmony default export */ var list_view = (listView);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/save-button/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function SaveButton() {
  const {
    hasEditedWidgetAreaIds,
    isSaving
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _getEditedWidgetAreas;

    const {
      getEditedWidgetAreas,
      isSavingWidgetAreas
    } = select(store_store);
    return {
      hasEditedWidgetAreaIds: ((_getEditedWidgetAreas = getEditedWidgetAreas()) === null || _getEditedWidgetAreas === void 0 ? void 0 : _getEditedWidgetAreas.length) > 0,
      isSaving: isSavingWidgetAreas()
    };
  }, []);
  const {
    saveEditedWidgetAreas
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "primary",
    isBusy: isSaving,
    "aria-disabled": isSaving,
    onClick: isSaving ? undefined : saveEditedWidgetAreas,
    disabled: !hasEditedWidgetAreaIds
  }, isSaving ? (0,external_wp_i18n_namespaceObject.__)('Saving…') : (0,external_wp_i18n_namespaceObject.__)('Update'));
}

/* harmony default export */ var save_button = (SaveButton);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/undo.js


/**
 * WordPress dependencies
 */

const undo = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z"
}));
/* harmony default export */ var library_undo = (undo);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/redo.js


/**
 * WordPress dependencies
 */

const redo = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z"
}));
/* harmony default export */ var library_redo = (redo);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/header/undo-redo/undo.js


/**
 * WordPress dependencies
 */






function UndoButton() {
  const hasUndo = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).hasUndo(), []);
  const {
    undo
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarButton, {
    icon: !(0,external_wp_i18n_namespaceObject.isRTL)() ? library_undo : library_redo,
    label: (0,external_wp_i18n_namespaceObject.__)('Undo'),
    shortcut: external_wp_keycodes_namespaceObject.displayShortcut.primary('z') // If there are no undo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/WordPress/gutenberg/issues/3486
    ,
    "aria-disabled": !hasUndo,
    onClick: hasUndo ? undo : undefined
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/header/undo-redo/redo.js


/**
 * WordPress dependencies
 */






function RedoButton() {
  const shortcut = (0,external_wp_keycodes_namespaceObject.isAppleOS)() ? external_wp_keycodes_namespaceObject.displayShortcut.primaryShift('z') : external_wp_keycodes_namespaceObject.displayShortcut.primary('y');
  const hasRedo = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).hasRedo(), []);
  const {
    redo
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarButton, {
    icon: !(0,external_wp_i18n_namespaceObject.isRTL)() ? library_redo : library_undo,
    label: (0,external_wp_i18n_namespaceObject.__)('Redo'),
    shortcut: shortcut // If there are no undo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/WordPress/gutenberg/issues/3486
    ,
    "aria-disabled": !hasRedo,
    onClick: hasRedo ? redo : undefined
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/external.js


/**
 * WordPress dependencies
 */

const external = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"
}));
/* harmony default export */ var library_external = (external);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/keyboard-shortcut-help-modal/config.js
/**
 * WordPress dependencies
 */

const textFormattingShortcuts = [{
  keyCombination: {
    modifier: 'primary',
    character: 'b'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Make the selected text bold.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'i'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Make the selected text italic.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'k'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Convert the selected text into a link.')
}, {
  keyCombination: {
    modifier: 'primaryShift',
    character: 'k'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Remove a link.')
}, {
  keyCombination: {
    character: '[['
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Insert a link to a post or page.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'u'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Underline the selected text.')
}, {
  keyCombination: {
    modifier: 'access',
    character: 'd'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Strikethrough the selected text.')
}, {
  keyCombination: {
    modifier: 'access',
    character: 'x'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Make the selected text inline code.')
}];

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/keyboard-shortcut-help-modal/shortcut.js


/**
 * WordPress dependencies
 */



function KeyCombination(_ref) {
  let {
    keyCombination,
    forceAriaLabel
  } = _ref;
  const shortcut = keyCombination.modifier ? external_wp_keycodes_namespaceObject.displayShortcutList[keyCombination.modifier](keyCombination.character) : keyCombination.character;
  const ariaLabel = keyCombination.modifier ? external_wp_keycodes_namespaceObject.shortcutAriaLabel[keyCombination.modifier](keyCombination.character) : keyCombination.character;
  const shortcuts = Array.isArray(shortcut) ? shortcut : [shortcut];
  return (0,external_wp_element_namespaceObject.createElement)("kbd", {
    className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-key-combination",
    "aria-label": forceAriaLabel || ariaLabel
  }, shortcuts.map((character, index) => {
    if (character === '+') {
      return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, {
        key: index
      }, character);
    }

    return (0,external_wp_element_namespaceObject.createElement)("kbd", {
      key: index,
      className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-key"
    }, character);
  }));
}

function Shortcut(_ref2) {
  let {
    description,
    keyCombination,
    aliases = [],
    ariaLabel
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-description"
  }, description), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-term"
  }, (0,external_wp_element_namespaceObject.createElement)(KeyCombination, {
    keyCombination: keyCombination,
    forceAriaLabel: ariaLabel
  }), aliases.map((alias, index) => (0,external_wp_element_namespaceObject.createElement)(KeyCombination, {
    keyCombination: alias,
    forceAriaLabel: ariaLabel,
    key: index
  }))));
}

/* harmony default export */ var keyboard_shortcut_help_modal_shortcut = (Shortcut);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



function DynamicShortcut(_ref) {
  let {
    name
  } = _ref;
  const {
    keyCombination,
    description,
    aliases
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getShortcutKeyCombination,
      getShortcutDescription,
      getShortcutAliases
    } = select(external_wp_keyboardShortcuts_namespaceObject.store);
    return {
      keyCombination: getShortcutKeyCombination(name),
      aliases: getShortcutAliases(name),
      description: getShortcutDescription(name)
    };
  }, [name]);

  if (!keyCombination) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(keyboard_shortcut_help_modal_shortcut, {
    keyCombination: keyCombination,
    description: description,
    aliases: aliases
  });
}

/* harmony default export */ var dynamic_shortcut = (DynamicShortcut);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/keyboard-shortcut-help-modal/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */





const ShortcutList = _ref => {
  let {
    shortcuts
  } = _ref;
  return (
    /*
     * Disable reason: The `list` ARIA role is redundant but
     * Safari+VoiceOver won't announce the list otherwise.
     */

    /* eslint-disable jsx-a11y/no-redundant-roles */
    (0,external_wp_element_namespaceObject.createElement)("ul", {
      className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-list",
      role: "list"
    }, shortcuts.map((shortcut, index) => (0,external_wp_element_namespaceObject.createElement)("li", {
      className: "edit-widgets-keyboard-shortcut-help-modal__shortcut",
      key: index
    }, typeof shortcut === 'string' ? (0,external_wp_element_namespaceObject.createElement)(dynamic_shortcut, {
      name: shortcut
    }) : (0,external_wp_element_namespaceObject.createElement)(keyboard_shortcut_help_modal_shortcut, shortcut))))
    /* eslint-enable jsx-a11y/no-redundant-roles */

  );
};

const ShortcutSection = _ref2 => {
  let {
    title,
    shortcuts,
    className
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)("section", {
    className: classnames_default()('edit-widgets-keyboard-shortcut-help-modal__section', className)
  }, !!title && (0,external_wp_element_namespaceObject.createElement)("h2", {
    className: "edit-widgets-keyboard-shortcut-help-modal__section-title"
  }, title), (0,external_wp_element_namespaceObject.createElement)(ShortcutList, {
    shortcuts: shortcuts
  }));
};

const ShortcutCategorySection = _ref3 => {
  let {
    title,
    categoryName,
    additionalShortcuts = []
  } = _ref3;
  const categoryShortcuts = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return select(external_wp_keyboardShortcuts_namespaceObject.store).getCategoryShortcuts(categoryName);
  }, [categoryName]);
  return (0,external_wp_element_namespaceObject.createElement)(ShortcutSection, {
    title: title,
    shortcuts: categoryShortcuts.concat(additionalShortcuts)
  });
};

function KeyboardShortcutHelpModal(_ref4) {
  let {
    isModalActive,
    toggleModal
  } = _ref4;
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-widgets/keyboard-shortcuts', toggleModal, {
    bindGlobal: true
  });

  if (!isModalActive) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    className: "edit-widgets-keyboard-shortcut-help-modal",
    title: (0,external_wp_i18n_namespaceObject.__)('Keyboard shortcuts'),
    onRequestClose: toggleModal
  }, (0,external_wp_element_namespaceObject.createElement)(ShortcutSection, {
    className: "edit-widgets-keyboard-shortcut-help-modal__main-shortcuts",
    shortcuts: ['core/edit-widgets/keyboard-shortcuts']
  }), (0,external_wp_element_namespaceObject.createElement)(ShortcutCategorySection, {
    title: (0,external_wp_i18n_namespaceObject.__)('Global shortcuts'),
    categoryName: "global"
  }), (0,external_wp_element_namespaceObject.createElement)(ShortcutCategorySection, {
    title: (0,external_wp_i18n_namespaceObject.__)('Selection shortcuts'),
    categoryName: "selection"
  }), (0,external_wp_element_namespaceObject.createElement)(ShortcutCategorySection, {
    title: (0,external_wp_i18n_namespaceObject.__)('Block shortcuts'),
    categoryName: "block",
    additionalShortcuts: [{
      keyCombination: {
        character: '/'
      },
      description: (0,external_wp_i18n_namespaceObject.__)('Change the block type after adding a new paragraph.'),

      /* translators: The forward-slash character. e.g. '/'. */
      ariaLabel: (0,external_wp_i18n_namespaceObject.__)('Forward-slash')
    }]
  }), (0,external_wp_element_namespaceObject.createElement)(ShortcutSection, {
    title: (0,external_wp_i18n_namespaceObject.__)('Text formatting'),
    shortcuts: textFormattingShortcuts
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/more-menu/tools-more-menu-group.js


/**
 * WordPress dependencies
 */

const {
  Fill: ToolsMoreMenuGroup,
  Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('EditWidgetsToolsMoreMenuGroup');

ToolsMoreMenuGroup.Slot = _ref => {
  let {
    fillProps
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(Slot, {
    fillProps: fillProps
  }, fills => fills.length > 0 && fills);
};

/* harmony default export */ var tools_more_menu_group = (ToolsMoreMenuGroup);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/more-menu/index.js


/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */



function MoreMenu() {
  const [isKeyboardShortcutsModalActive, setIsKeyboardShortcutsModalVisible] = (0,external_wp_element_namespaceObject.useState)(false);

  const toggleKeyboardShortcutsModal = () => setIsKeyboardShortcutsModalVisible(!isKeyboardShortcutsModalActive);

  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-widgets/keyboard-shortcuts', toggleKeyboardShortcutsModal);
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium');
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(MoreMenuDropdown, null, onClose => (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, isLargeViewport && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
    label: (0,external_wp_i18n_namespaceObject._x)('View', 'noun')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-widgets",
    name: "fixedToolbar",
    label: (0,external_wp_i18n_namespaceObject.__)('Top toolbar'),
    info: (0,external_wp_i18n_namespaceObject.__)('Access all block and document tools in a single place'),
    messageActivated: (0,external_wp_i18n_namespaceObject.__)('Top toolbar activated'),
    messageDeactivated: (0,external_wp_i18n_namespaceObject.__)('Top toolbar deactivated')
  })), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
    label: (0,external_wp_i18n_namespaceObject.__)('Tools')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => {
      setIsKeyboardShortcutsModalVisible(true);
    },
    shortcut: external_wp_keycodes_namespaceObject.displayShortcut.access('h')
  }, (0,external_wp_i18n_namespaceObject.__)('Keyboard shortcuts')), (0,external_wp_element_namespaceObject.createElement)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-widgets",
    name: "welcomeGuide",
    label: (0,external_wp_i18n_namespaceObject.__)('Welcome Guide')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    role: "menuitem",
    icon: library_external,
    href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/support/article/block-based-widgets-editor/'),
    target: "_blank",
    rel: "noopener noreferrer"
  }, (0,external_wp_i18n_namespaceObject.__)('Help'), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
    as: "span"
  },
  /* translators: accessibility text */
  (0,external_wp_i18n_namespaceObject.__)('(opens in a new tab)'))), (0,external_wp_element_namespaceObject.createElement)(tools_more_menu_group.Slot, {
    fillProps: {
      onClose
    }
  })), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
    label: (0,external_wp_i18n_namespaceObject.__)('Preferences')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-widgets",
    name: "keepCaretInsideBlock",
    label: (0,external_wp_i18n_namespaceObject.__)('Contain text cursor inside block'),
    info: (0,external_wp_i18n_namespaceObject.__)('Aids screen readers by stopping text caret from leaving blocks.'),
    messageActivated: (0,external_wp_i18n_namespaceObject.__)('Contain text cursor inside block activated'),
    messageDeactivated: (0,external_wp_i18n_namespaceObject.__)('Contain text cursor inside block deactivated')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-widgets",
    name: "themeStyles",
    info: (0,external_wp_i18n_namespaceObject.__)('Make the editor look like your theme.'),
    label: (0,external_wp_i18n_namespaceObject.__)('Use theme styles')
  }), isLargeViewport && (0,external_wp_element_namespaceObject.createElement)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-widgets",
    name: "showBlockBreadcrumbs",
    label: (0,external_wp_i18n_namespaceObject.__)('Display block breadcrumbs'),
    info: (0,external_wp_i18n_namespaceObject.__)('Shows block breadcrumbs at the bottom of the editor.'),
    messageActivated: (0,external_wp_i18n_namespaceObject.__)('Display block breadcrumbs activated'),
    messageDeactivated: (0,external_wp_i18n_namespaceObject.__)('Display block breadcrumbs deactivated')
  })))), (0,external_wp_element_namespaceObject.createElement)(KeyboardShortcutHelpModal, {
    isModalActive: isKeyboardShortcutsModalActive,
    toggleModal: toggleKeyboardShortcutsModal
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/header/index.js


/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */








function Header() {
  const isMediumViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium');
  const inserterButton = (0,external_wp_element_namespaceObject.useRef)();
  const widgetAreaClientId = use_last_selected_widget_area();
  const isLastSelectedWidgetAreaOpen = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getIsWidgetAreaOpen(widgetAreaClientId), [widgetAreaClientId]);
  const {
    isInserterOpen,
    isListViewOpen
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isInserterOpened,
      isListViewOpened
    } = select(store_store);
    return {
      isInserterOpen: isInserterOpened(),
      isListViewOpen: isListViewOpened()
    };
  }, []);
  const {
    setIsWidgetAreaOpen,
    setIsInserterOpened,
    setIsListViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    selectBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);

  const handleClick = () => {
    if (isInserterOpen) {
      // Focusing the inserter button closes the inserter popover.
      setIsInserterOpened(false);
    } else {
      if (!isLastSelectedWidgetAreaOpen) {
        // Select the last selected block if hasn't already.
        selectBlock(widgetAreaClientId); // Open the last selected widget area when opening the inserter.

        setIsWidgetAreaOpen(widgetAreaClientId, true);
      } // The DOM updates resulting from selectBlock() and setIsInserterOpened() calls are applied the
      // same tick and pretty much in a random order. The inserter is closed if any other part of the
      // app receives focus. If selectBlock() happens to take effect after setIsInserterOpened() then
      // the inserter is visible for a brief moment and then gets auto-closed due to focus moving to
      // the selected block.


      window.requestAnimationFrame(() => setIsInserterOpened(true));
    }
  };

  const toggleListView = (0,external_wp_element_namespaceObject.useCallback)(() => setIsListViewOpened(!isListViewOpen), [setIsListViewOpened, isListViewOpen]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-widgets-header"
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-widgets-header__navigable-toolbar-wrapper"
  }, isMediumViewport && (0,external_wp_element_namespaceObject.createElement)("h1", {
    className: "edit-widgets-header__title"
  }, (0,external_wp_i18n_namespaceObject.__)('Widgets')), !isMediumViewport && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
    as: "h1",
    className: "edit-widgets-header__title"
  }, (0,external_wp_i18n_namespaceObject.__)('Widgets')), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.NavigableToolbar, {
    className: "edit-widgets-header-toolbar",
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Document tools')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    ref: inserterButton,
    as: external_wp_components_namespaceObject.Button,
    className: "edit-widgets-header-toolbar__inserter-toggle",
    variant: "primary",
    isPressed: isInserterOpen,
    onMouseDown: event => {
      event.preventDefault();
    },
    onClick: handleClick,
    icon: library_plus
    /* translators: button label text should, if possible, be under 16
    characters. */
    ,
    label: (0,external_wp_i18n_namespaceObject._x)('Toggle block inserter', 'Generic label for block inserter button')
  }), isMediumViewport && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(UndoButton, null), (0,external_wp_element_namespaceObject.createElement)(RedoButton, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    as: external_wp_components_namespaceObject.Button,
    className: "edit-widgets-header-toolbar__list-view-toggle",
    icon: list_view,
    isPressed: isListViewOpen
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    label: (0,external_wp_i18n_namespaceObject.__)('List View'),
    onClick: toggleListView
  })))), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-widgets-header__actions"
  }, (0,external_wp_element_namespaceObject.createElement)(save_button, null), (0,external_wp_element_namespaceObject.createElement)(pinned_items.Slot, {
    scope: "core/edit-widgets"
  }), (0,external_wp_element_namespaceObject.createElement)(MoreMenu, null))));
}

/* harmony default export */ var header = (Header);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/notices/index.js


/**
 * WordPress dependencies
 */




function Notices() {
  const {
    removeNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const {
    notices
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return {
      notices: select(external_wp_notices_namespaceObject.store).getNotices()
    };
  }, []);
  const dismissibleNotices = notices.filter(_ref => {
    let {
      isDismissible,
      type
    } = _ref;
    return isDismissible && type === 'default';
  });
  const nonDismissibleNotices = notices.filter(_ref2 => {
    let {
      isDismissible,
      type
    } = _ref2;
    return !isDismissible && type === 'default';
  });
  const snackbarNotices = notices.filter(_ref3 => {
    let {
      type
    } = _ref3;
    return type === 'snackbar';
  });
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.NoticeList, {
    notices: nonDismissibleNotices,
    className: "edit-widgets-notices__pinned"
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.NoticeList, {
    notices: dismissibleNotices,
    className: "edit-widgets-notices__dismissible",
    onRemove: removeNotice
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SnackbarList, {
    notices: snackbarNotices,
    className: "edit-widgets-notices__snackbar",
    onRemove: removeNotice
  }));
}

/* harmony default export */ var notices = (Notices);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/widget-areas-block-editor-content/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



function WidgetAreasBlockEditorContent(_ref) {
  let {
    blockEditorSettings
  } = _ref;
  const hasThemeStyles = (0,external_wp_data_namespaceObject.useSelect)(select => !!select(external_wp_preferences_namespaceObject.store).get('core/edit-widgets', 'themeStyles'), []);
  const styles = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return hasThemeStyles ? blockEditorSettings.styles : [];
  }, [blockEditorSettings, hasThemeStyles]);
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-widgets-block-editor"
  }, (0,external_wp_element_namespaceObject.createElement)(notices, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockTools, null, (0,external_wp_element_namespaceObject.createElement)(keyboard_shortcuts, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableEditorStyles, {
    styles: styles
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockSelectionClearer, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.WritingFlow, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.ObserveTyping, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockList, {
    className: "edit-widgets-main-block-list"
  }))))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/close.js


/**
 * WordPress dependencies
 */

const close_close = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"
}));
/* harmony default export */ var library_close = (close_close);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/hooks/use-widget-library-insertion-point.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




const useWidgetLibraryInsertionPoint = () => {
  const firstRootId = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _widgetAreasPost$bloc;

    // Default to the first widget area
    const {
      getEntityRecord
    } = select(external_wp_coreData_namespaceObject.store);
    const widgetAreasPost = getEntityRecord(KIND, POST_TYPE, buildWidgetAreasPostId());
    return widgetAreasPost === null || widgetAreasPost === void 0 ? void 0 : (_widgetAreasPost$bloc = widgetAreasPost.blocks[0]) === null || _widgetAreasPost$bloc === void 0 ? void 0 : _widgetAreasPost$bloc.clientId;
  }, []);
  return (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getBlockRootClientId,
      getBlockSelectionEnd,
      getBlockOrder,
      getBlockIndex
    } = select(external_wp_blockEditor_namespaceObject.store);

    const insertionPoint = select(store_store).__experimentalGetInsertionPoint(); // "Browse all" in the quick inserter will set the rootClientId to the current block.
    // Otherwise, it will just be undefined, and we'll have to handle it differently below.


    if (insertionPoint.rootClientId) {
      return insertionPoint;
    }

    const clientId = getBlockSelectionEnd() || firstRootId;
    const rootClientId = getBlockRootClientId(clientId); // If the selected block is at the root level, it's a widget area and
    // blocks can't be inserted here. Return this block as the root and the
    // last child clientId indicating insertion at the end.

    if (clientId && rootClientId === '') {
      return {
        rootClientId: clientId,
        insertionIndex: getBlockOrder(clientId).length
      };
    }

    return {
      rootClientId,
      insertionIndex: getBlockIndex(clientId) + 1
    };
  }, [firstRootId]);
};

/* harmony default export */ var use_widget_library_insertion_point = (useWidgetLibraryInsertionPoint);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/secondary-sidebar/inserter-sidebar.js



/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */



function InserterSidebar() {
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium', '<');
  const {
    rootClientId,
    insertionIndex
  } = use_widget_library_insertion_point();
  const {
    setIsInserterOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const closeInserter = (0,external_wp_element_namespaceObject.useCallback)(() => {
    return setIsInserterOpened(false);
  }, [setIsInserterOpened]);
  const TagName = !isMobileViewport ? external_wp_components_namespaceObject.VisuallyHidden : 'div';
  const [inserterDialogRef, inserterDialogProps] = (0,external_wp_compose_namespaceObject.__experimentalUseDialog)({
    onClose: closeInserter,
    focusOnMount: null
  });
  const libraryRef = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    libraryRef.current.focusSearch();
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)("div", _extends({
    ref: inserterDialogRef
  }, inserterDialogProps, {
    className: "edit-widgets-layout__inserter-panel"
  }), (0,external_wp_element_namespaceObject.createElement)(TagName, {
    className: "edit-widgets-layout__inserter-panel-header"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    icon: library_close,
    onClick: closeInserter,
    label: (0,external_wp_i18n_namespaceObject.__)('Close block inserter')
  })), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-widgets-layout__inserter-panel-content"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalLibrary, {
    showInserterHelpPanel: true,
    shouldFocusBlock: isMobileViewport,
    rootClientId: rootClientId,
    __experimentalInsertionIndex: insertionIndex,
    ref: libraryRef
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/secondary-sidebar/list-view-sidebar.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function ListViewSidebar() {
  const {
    setIsListViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const focusOnMountRef = (0,external_wp_compose_namespaceObject.useFocusOnMount)('firstElement');
  const headerFocusReturnRef = (0,external_wp_compose_namespaceObject.useFocusReturn)();
  const contentFocusReturnRef = (0,external_wp_compose_namespaceObject.useFocusReturn)();

  function closeOnEscape(event) {
    if (event.keyCode === external_wp_keycodes_namespaceObject.ESCAPE && !event.defaultPrevented) {
      event.preventDefault();
      setIsListViewOpened(false);
    }
  }

  const instanceId = (0,external_wp_compose_namespaceObject.useInstanceId)(ListViewSidebar);
  const labelId = `edit-widgets-editor__list-view-panel-label-${instanceId}`;
  return (// eslint-disable-next-line jsx-a11y/no-static-element-interactions
    (0,external_wp_element_namespaceObject.createElement)("div", {
      "aria-labelledby": labelId,
      className: "edit-widgets-editor__list-view-panel",
      onKeyDown: closeOnEscape
    }, (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-widgets-editor__list-view-panel-header",
      ref: headerFocusReturnRef
    }, (0,external_wp_element_namespaceObject.createElement)("strong", {
      id: labelId
    }, (0,external_wp_i18n_namespaceObject.__)('List View')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
      icon: close_small,
      label: (0,external_wp_i18n_namespaceObject.__)('Close List View Sidebar'),
      onClick: () => setIsListViewOpened(false)
    })), (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-widgets-editor__list-view-panel-content",
      ref: (0,external_wp_compose_namespaceObject.useMergeRefs)([contentFocusReturnRef, focusOnMountRef])
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalListView, null)))
  );
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/secondary-sidebar/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Internal dependencies
 */



function SecondarySidebar() {
  const {
    isInserterOpen,
    isListViewOpen
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isInserterOpened,
      isListViewOpened
    } = select(store_store);
    return {
      isInserterOpen: isInserterOpened(),
      isListViewOpen: isListViewOpened()
    };
  }, []);

  if (isInserterOpen) {
    return (0,external_wp_element_namespaceObject.createElement)(InserterSidebar, null);
  }

  if (isListViewOpen) {
    return (0,external_wp_element_namespaceObject.createElement)(ListViewSidebar, null);
  }

  return null;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/layout/interface.js


/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */





const interfaceLabels = {
  /* translators: accessibility text for the widgets screen top bar landmark region. */
  header: (0,external_wp_i18n_namespaceObject.__)('Widgets top bar'),

  /* translators: accessibility text for the widgets screen content landmark region. */
  body: (0,external_wp_i18n_namespaceObject.__)('Widgets and blocks'),

  /* translators: accessibility text for the widgets screen settings landmark region. */
  sidebar: (0,external_wp_i18n_namespaceObject.__)('Widgets settings'),

  /* translators: accessibility text for the widgets screen footer landmark region. */
  footer: (0,external_wp_i18n_namespaceObject.__)('Widgets footer')
};

function Interface(_ref) {
  let {
    blockEditorSettings
  } = _ref;
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium', '<');
  const isHugeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('huge', '>=');
  const {
    setIsInserterOpened,
    setIsListViewOpened,
    closeGeneralSidebar
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    hasBlockBreadCrumbsEnabled,
    hasSidebarEnabled,
    isInserterOpened,
    isListViewOpened,
    previousShortcut,
    nextShortcut
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    hasSidebarEnabled: !!select(store).getActiveComplementaryArea(store_store.name),
    isInserterOpened: !!select(store_store).isInserterOpened(),
    isListViewOpened: !!select(store_store).isListViewOpened(),
    hasBlockBreadCrumbsEnabled: !!select(external_wp_preferences_namespaceObject.store).get('core/edit-widgets', 'showBlockBreadcrumbs'),
    previousShortcut: select(external_wp_keyboardShortcuts_namespaceObject.store).getAllShortcutKeyCombinations('core/edit-widgets/previous-region'),
    nextShortcut: select(external_wp_keyboardShortcuts_namespaceObject.store).getAllShortcutKeyCombinations('core/edit-widgets/next-region')
  }), []); // Inserter and Sidebars are mutually exclusive

  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (hasSidebarEnabled && !isHugeViewport) {
      setIsInserterOpened(false);
      setIsListViewOpened(false);
    }
  }, [hasSidebarEnabled, isHugeViewport]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if ((isInserterOpened || isListViewOpened) && !isHugeViewport) {
      closeGeneralSidebar();
    }
  }, [isInserterOpened, isListViewOpened, isHugeViewport]);
  const secondarySidebarLabel = isListViewOpened ? (0,external_wp_i18n_namespaceObject.__)('List View') : (0,external_wp_i18n_namespaceObject.__)('Block Library');
  const hasSecondarySidebar = isListViewOpened || isInserterOpened;
  return (0,external_wp_element_namespaceObject.createElement)(interface_skeleton, {
    labels: { ...interfaceLabels,
      secondarySidebar: secondarySidebarLabel
    },
    header: (0,external_wp_element_namespaceObject.createElement)(header, null),
    secondarySidebar: hasSecondarySidebar && (0,external_wp_element_namespaceObject.createElement)(SecondarySidebar, null),
    sidebar: hasSidebarEnabled && (0,external_wp_element_namespaceObject.createElement)(complementary_area.Slot, {
      scope: "core/edit-widgets"
    }),
    content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(WidgetAreasBlockEditorContent, {
      blockEditorSettings: blockEditorSettings
    })),
    footer: hasBlockBreadCrumbsEnabled && !isMobileViewport && (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-widgets-layout__footer"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockBreadcrumb, {
      rootLabelText: (0,external_wp_i18n_namespaceObject.__)('Widgets')
    })),
    shortcuts: {
      previous: previousShortcut,
      next: nextShortcut
    }
  });
}

/* harmony default export */ var layout_interface = (Interface);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/layout/unsaved-changes-warning.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Warns the user if there are unsaved changes before leaving the editor.
 *
 * This is a duplicate of the component implemented in the editor package.
 * Duplicated here as edit-widgets doesn't depend on editor.
 *
 * @return {WPComponent} The component.
 */

function UnsavedChangesWarning() {
  const isDirty = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedWidgetAreas
    } = select(store_store);
    const editedWidgetAreas = getEditedWidgetAreas();
    return (editedWidgetAreas === null || editedWidgetAreas === void 0 ? void 0 : editedWidgetAreas.length) > 0;
  }, []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    /**
     * Warns the user if there are unsaved changes before leaving the editor.
     *
     * @param {Event} event `beforeunload` event.
     *
     * @return {string | undefined} Warning prompt message, if unsaved changes exist.
     */
    const warnIfUnsavedChanges = event => {
      if (isDirty) {
        event.returnValue = (0,external_wp_i18n_namespaceObject.__)('You have unsaved changes. If you proceed, they will be lost.');
        return event.returnValue;
      }
    };

    window.addEventListener('beforeunload', warnIfUnsavedChanges);
    return () => {
      window.removeEventListener('beforeunload', warnIfUnsavedChanges);
    };
  }, [isDirty]);
  return null;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/welcome-guide/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function WelcomeGuide() {
  var _widgetAreas$filter$l;

  const isActive = (0,external_wp_data_namespaceObject.useSelect)(select => !!select(external_wp_preferences_namespaceObject.store).get('core/edit-widgets', 'welcomeGuide'), []);
  const {
    toggle
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);
  const widgetAreas = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getWidgetAreas({
    per_page: -1
  }), []);

  if (!isActive) {
    return null;
  }

  const isEntirelyBlockWidgets = widgetAreas === null || widgetAreas === void 0 ? void 0 : widgetAreas.every(widgetArea => widgetArea.id === 'wp_inactive_widgets' || widgetArea.widgets.every(widgetId => widgetId.startsWith('block-')));
  const numWidgetAreas = (_widgetAreas$filter$l = widgetAreas === null || widgetAreas === void 0 ? void 0 : widgetAreas.filter(widgetArea => widgetArea.id !== 'wp_inactive_widgets').length) !== null && _widgetAreas$filter$l !== void 0 ? _widgetAreas$filter$l : 0;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Guide, {
    className: "edit-widgets-welcome-guide",
    contentLabel: (0,external_wp_i18n_namespaceObject.__)('Welcome to block Widgets'),
    finishButtonText: (0,external_wp_i18n_namespaceObject.__)('Get started'),
    onFinish: () => toggle('core/edit-widgets', 'welcomeGuide'),
    pages: [{
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.gif"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-widgets-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Welcome to block Widgets')), isEntirelyBlockWidgets ? (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-widgets-welcome-guide__text"
      }, (0,external_wp_i18n_namespaceObject.sprintf)( // Translators: %s: Number of block areas in the current theme.
      (0,external_wp_i18n_namespaceObject._n)('Your theme provides %s “block” area for you to add and edit content. Try adding a search bar, social icons, or other types of blocks here and see how they’ll look on your site.', 'Your theme provides %s different “block” areas for you to add and edit content. Try adding a search bar, social icons, or other types of blocks here and see how they’ll look on your site.', numWidgetAreas), numWidgetAreas))) : (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-widgets-welcome-guide__text"
      }, (0,external_wp_i18n_namespaceObject.__)('You can now add any block to your site’s widget areas. Don’t worry, all of your favorite widgets still work flawlessly.')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-widgets-welcome-guide__text"
      }, (0,external_wp_element_namespaceObject.createElement)("strong", null, (0,external_wp_i18n_namespaceObject.__)('Want to stick with the old widgets?')), ' ', (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ExternalLink, {
        href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/plugins/classic-widgets/')
      }, (0,external_wp_i18n_namespaceObject.__)('Get the Classic Widgets plugin.')))))
    }, {
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-editor.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-editor.gif"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-widgets-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Make each block your own')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-widgets-welcome-guide__text"
      }, (0,external_wp_i18n_namespaceObject.__)('Each block comes with its own set of controls for changing things like color, width, and alignment. These will show and hide automatically when you have a block selected.')))
    }, {
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-library.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-library.gif"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-widgets-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Get to know the block library')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-widgets-welcome-guide__text"
      }, (0,external_wp_element_namespaceObject.createInterpolateElement)((0,external_wp_i18n_namespaceObject.__)('All of the blocks available to you live in the block library. You’ll find it wherever you see the <InserterIconImage /> icon.'), {
        InserterIconImage: (0,external_wp_element_namespaceObject.createElement)("img", {
          className: "edit-widgets-welcome-guide__inserter-icon",
          alt: (0,external_wp_i18n_namespaceObject.__)('inserter'),
          src: "data:image/svg+xml,%3Csvg width='18' height='18' viewBox='0 0 18 18' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='18' height='18' rx='2' fill='%231E1E1E'/%3E%3Cpath d='M9.22727 4V14M4 8.77273H14' stroke='white' stroke-width='1.5'/%3E%3C/svg%3E%0A"
        })
      })))
    }, {
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.gif"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-widgets-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Learn how to use the block editor')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-widgets-welcome-guide__text"
      }, (0,external_wp_i18n_namespaceObject.__)('New to the block editor? Want to learn more about using it? '), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ExternalLink, {
        href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/support/article/wordpress-editor/')
      }, (0,external_wp_i18n_namespaceObject.__)("Here's a detailed guide."))))
    }]
  });
}

function WelcomeGuideImage(_ref) {
  let {
    nonAnimatedSrc,
    animatedSrc
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)("picture", {
    className: "edit-widgets-welcome-guide__image"
  }, (0,external_wp_element_namespaceObject.createElement)("source", {
    srcSet: nonAnimatedSrc,
    media: "(prefers-reduced-motion: reduce)"
  }), (0,external_wp_element_namespaceObject.createElement)("img", {
    src: animatedSrc,
    width: "312",
    height: "240",
    alt: ""
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/components/layout/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */








function Layout(_ref) {
  let {
    blockEditorSettings
  } = _ref;
  const {
    createErrorNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);

  function onPluginAreaError(name) {
    createErrorNotice((0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: %s: plugin name */
    (0,external_wp_i18n_namespaceObject.__)('The "%s" plugin has encountered an error and cannot be rendered.'), name));
  }

  return (0,external_wp_element_namespaceObject.createElement)(ErrorBoundary, null, (0,external_wp_element_namespaceObject.createElement)(WidgetAreasBlockEditorProvider, {
    blockEditorSettings: blockEditorSettings
  }, (0,external_wp_element_namespaceObject.createElement)(layout_interface, {
    blockEditorSettings: blockEditorSettings
  }), (0,external_wp_element_namespaceObject.createElement)(Sidebar, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Popover.Slot, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_plugins_namespaceObject.PluginArea, {
    onError: onPluginAreaError
  }), (0,external_wp_element_namespaceObject.createElement)(UnsavedChangesWarning, null), (0,external_wp_element_namespaceObject.createElement)(WelcomeGuide, null)));
}

/* harmony default export */ var layout = (Layout);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-widgets/build-module/index.js


/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */






const disabledBlocks = ['core/more', 'core/freeform', 'core/template-part', ...(ALLOW_REUSABLE_BLOCKS ? [] : ['core/block'])];
/**
 * Initializes the block editor in the widgets screen.
 *
 * @param {string} id       ID of the root element to render the screen in.
 * @param {Object} settings Block editor settings.
 */

function initializeEditor(id, settings) {
  const target = document.getElementById(id);
  const root = (0,external_wp_element_namespaceObject.createRoot)(target);

  const coreBlocks = (0,external_wp_blockLibrary_namespaceObject.__experimentalGetCoreBlocks)().filter(block => {
    return !(disabledBlocks.includes(block.name) || block.name.startsWith('core/post') || block.name.startsWith('core/query') || block.name.startsWith('core/site') || block.name.startsWith('core/navigation'));
  });

  (0,external_wp_data_namespaceObject.dispatch)(external_wp_preferences_namespaceObject.store).setDefaults('core/edit-widgets', {
    fixedToolbar: false,
    welcomeGuide: true,
    showBlockBreadcrumbs: true,
    themeStyles: true
  });

  (0,external_wp_data_namespaceObject.dispatch)(external_wp_blocks_namespaceObject.store).__experimentalReapplyBlockTypeFilters();

  (0,external_wp_blockLibrary_namespaceObject.registerCoreBlocks)(coreBlocks);
  (0,external_wp_widgets_namespaceObject.registerLegacyWidgetBlock)();

  if (false) {}

  (0,external_wp_widgets_namespaceObject.registerLegacyWidgetVariations)(settings);
  registerBlock(widget_area_namespaceObject);
  (0,external_wp_widgets_namespaceObject.registerWidgetGroupBlock)();

  settings.__experimentalFetchLinkSuggestions = (search, searchOptions) => (0,external_wp_coreData_namespaceObject.__experimentalFetchLinkSuggestions)(search, searchOptions, settings); // As we are unregistering `core/freeform` to avoid the Classic block, we must
  // replace it with something as the default freeform content handler. Failure to
  // do this will result in errors in the default block parser.
  // see: https://github.com/WordPress/gutenberg/issues/33097


  (0,external_wp_blocks_namespaceObject.setFreeformContentHandlerName)('core/html');
  root.render((0,external_wp_element_namespaceObject.createElement)(layout, {
    blockEditorSettings: settings
  }));
  return root;
}
/**
 * Compatibility export under the old `initialize` name.
 */

const initialize = initializeEditor;
function reinitializeEditor() {
  external_wp_deprecated_default()('wp.editWidgets.reinitializeEditor', {
    since: '6.2',
    version: '6.3'
  });
}
/**
 * Function to register an individual block.
 *
 * @param {Object} block The block to be registered.
 *
 */

const registerBlock = block => {
  if (!block) {
    return;
  }

  const {
    metadata,
    settings,
    name
  } = block;

  if (metadata) {
    (0,external_wp_blocks_namespaceObject.unstable__bootstrapServerSideBlockDefinitions)({
      [name]: metadata
    });
  }

  (0,external_wp_blocks_namespaceObject.registerBlockType)(name, settings);
};

}();
(window.wp = window.wp || {}).editWidgets = __webpack_exports__;
/******/ })()
;