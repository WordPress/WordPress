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
  DotTip: () => (/* reexport */ dot_tip),
  store: () => (/* reexport */ store)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/nux/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  disableTips: () => (disableTips),
  dismissTip: () => (dismissTip),
  enableTips: () => (enableTips),
  triggerGuide: () => (triggerGuide)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/nux/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  areTipsEnabled: () => (selectors_areTipsEnabled),
  getAssociatedGuide: () => (getAssociatedGuide),
  isTipVisible: () => (isTipVisible)
});

;// external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// ./node_modules/@wordpress/nux/build-module/store/reducer.js
/**
 * WordPress dependencies
 */


/**
 * Reducer that tracks which tips are in a guide. Each guide is represented by
 * an array which contains the tip identifiers contained within that guide.
 *
 * @param {Array}  state  Current state.
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
 * @param {boolean} state  Current state.
 * @param {Object}  action Dispatched action.
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
      return {
        ...state,
        [action.id]: true
      };
    case 'ENABLE_TIPS':
      return {};
  }
  return state;
}
const preferences = (0,external_wp_data_namespaceObject.combineReducers)({
  areTipsEnabled,
  dismissedTips
});
/* harmony default export */ const reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  guides,
  preferences
}));

;// ./node_modules/@wordpress/nux/build-module/store/actions.js
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

;// ./node_modules/@wordpress/nux/build-module/store/selectors.js
/**
 * WordPress dependencies
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
const getAssociatedGuide = (0,external_wp_data_namespaceObject.createSelector)((state, tipId) => {
  for (const tipIds of state.guides) {
    if (tipIds.includes(tipId)) {
      const nonDismissedTips = tipIds.filter(tId => !Object.keys(state.preferences.dismissedTips).includes(tId));
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
  if (state.preferences.dismissedTips?.hasOwnProperty(tipId)) {
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

;// ./node_modules/@wordpress/nux/build-module/store/index.js
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
const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['preferences']
});

// Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.
(0,external_wp_data_namespaceObject.registerStore)(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['preferences']
});

;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// ./node_modules/@wordpress/icons/build-module/library/close.js
/**
 * WordPress dependencies
 */


const close_close = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "m13.06 12 6.47-6.47-1.06-1.06L12 10.94 5.53 4.47 4.47 5.53 10.94 12l-6.47 6.47 1.06 1.06L12 13.06l6.47 6.47 1.06-1.06L13.06 12Z"
  })
});
/* harmony default export */ const library_close = (close_close);

;// ./node_modules/@wordpress/nux/build-module/components/dot-tip/index.js
/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
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
  const anchorParent = (0,external_wp_element_namespaceObject.useRef)(null);
  const onFocusOutsideCallback = (0,external_wp_element_namespaceObject.useCallback)(event => {
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
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.Popover, {
    className: "nux-dot-tip",
    position: position,
    focusOnMount: true,
    role: "dialog",
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Editor tips'),
    onClick: onClick,
    onFocusOutside: onFocusOutsideCallback,
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
      children: children
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Button, {
        __next40pxDefaultSize: true,
        variant: "link",
        onClick: onDismiss,
        children: hasNextTip ? (0,external_wp_i18n_namespaceObject.__)('See next tip') : (0,external_wp_i18n_namespaceObject.__)('Got it')
      })
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Button, {
      size: "small",
      className: "nux-dot-tip__disable",
      icon: library_close,
      label: (0,external_wp_i18n_namespaceObject.__)('Disable tips'),
      onClick: onDisable
    })]
  });
}
/* harmony default export */ const dot_tip = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, {
  tipId
}) => {
  const {
    isTipVisible,
    getAssociatedGuide
  } = select(store);
  const associatedGuide = getAssociatedGuide(tipId);
  return {
    isVisible: isTipVisible(tipId),
    hasNextTip: !!(associatedGuide && associatedGuide.nextTipId)
  };
}), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, {
  tipId
}) => {
  const {
    dismissTip,
    disableTips
  } = dispatch(store);
  return {
    onDismiss() {
      dismissTip(tipId);
    },
    onDisable() {
      disableTips();
    }
  };
}))(DotTip));

;// ./node_modules/@wordpress/nux/build-module/index.js
/**
 * WordPress dependencies
 */



external_wp_deprecated_default()('wp.nux', {
  since: '5.4',
  hint: 'wp.components.Guide can be used to show a user guide.',
  version: '6.2'
});

(window.wp = window.wp || {}).nux = __webpack_exports__;
/******/ })()
;