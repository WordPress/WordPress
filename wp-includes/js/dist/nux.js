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
  DotTip: () => (/* reexport */ dot_tip_default),
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

function guides(state = [], action) {
  switch (action.type) {
    case "TRIGGER_GUIDE":
      return [...state, action.tipIds];
  }
  return state;
}
function areTipsEnabled(state = true, action) {
  switch (action.type) {
    case "DISABLE_TIPS":
      return false;
    case "ENABLE_TIPS":
      return true;
  }
  return state;
}
function dismissedTips(state = {}, action) {
  switch (action.type) {
    case "DISMISS_TIP":
      return {
        ...state,
        [action.id]: true
      };
    case "ENABLE_TIPS":
      return {};
  }
  return state;
}
const preferences = (0,external_wp_data_namespaceObject.combineReducers)({ areTipsEnabled, dismissedTips });
var reducer_default = (0,external_wp_data_namespaceObject.combineReducers)({ guides, preferences });


;// ./node_modules/@wordpress/nux/build-module/store/actions.js
function triggerGuide(tipIds) {
  return {
    type: "TRIGGER_GUIDE",
    tipIds
  };
}
function dismissTip(id) {
  return {
    type: "DISMISS_TIP",
    id
  };
}
function disableTips() {
  return {
    type: "DISABLE_TIPS"
  };
}
function enableTips() {
  return {
    type: "ENABLE_TIPS"
  };
}


;// ./node_modules/@wordpress/nux/build-module/store/selectors.js

const getAssociatedGuide = (0,external_wp_data_namespaceObject.createSelector)(
  (state, tipId) => {
    for (const tipIds of state.guides) {
      if (tipIds.includes(tipId)) {
        const nonDismissedTips = tipIds.filter(
          (tId) => !Object.keys(
            state.preferences.dismissedTips
          ).includes(tId)
        );
        const [currentTipId = null, nextTipId = null] = nonDismissedTips;
        return { tipIds, currentTipId, nextTipId };
      }
    }
    return null;
  },
  (state) => [state.guides, state.preferences.dismissedTips]
);
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
function selectors_areTipsEnabled(state) {
  return state.preferences.areTipsEnabled;
}


;// ./node_modules/@wordpress/nux/build-module/store/index.js




const STORE_NAME = "core/nux";
const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer_default,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ["preferences"]
});
(0,external_wp_data_namespaceObject.registerStore)(STORE_NAME, {
  reducer: reducer_default,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ["preferences"]
});


;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
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
;// ./node_modules/@wordpress/icons/build-module/library/close.js


var close_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "m13.06 12 6.47-6.47-1.06-1.06L12 10.94 5.53 4.47 4.47 5.53 10.94 12l-6.47 6.47 1.06 1.06L12 13.06l6.47 6.47 1.06-1.06L13.06 12Z" }) });


;// ./node_modules/@wordpress/nux/build-module/components/dot-tip/index.js








function onClick(event) {
  event.stopPropagation();
}
function DotTip({
  position = "middle right",
  children,
  isVisible,
  hasNextTip,
  onDismiss,
  onDisable
}) {
  const anchorParent = (0,external_wp_element_namespaceObject.useRef)(null);
  const onFocusOutsideCallback = (0,external_wp_element_namespaceObject.useCallback)(
    (event) => {
      if (!anchorParent.current) {
        return;
      }
      if (anchorParent.current.contains(event.relatedTarget)) {
        return;
      }
      onDisable();
    },
    [onDisable, anchorParent]
  );
  if (!isVisible) {
    return null;
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
    external_wp_components_namespaceObject.Popover,
    {
      className: "nux-dot-tip",
      position,
      focusOnMount: true,
      role: "dialog",
      "aria-label": (0,external_wp_i18n_namespaceObject.__)("Editor tips"),
      onClick,
      onFocusOutside: onFocusOutsideCallback,
      children: [
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { children }),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          external_wp_components_namespaceObject.Button,
          {
            __next40pxDefaultSize: true,
            variant: "link",
            onClick: onDismiss,
            children: hasNextTip ? (0,external_wp_i18n_namespaceObject.__)("See next tip") : (0,external_wp_i18n_namespaceObject.__)("Got it")
          }
        ) }),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          external_wp_components_namespaceObject.Button,
          {
            size: "small",
            className: "nux-dot-tip__disable",
            icon: close_default,
            label: (0,external_wp_i18n_namespaceObject.__)("Disable tips"),
            onClick: onDisable
          }
        )
      ]
    }
  );
}
var dot_tip_default = (0,external_wp_compose_namespaceObject.compose)(
  (0,external_wp_data_namespaceObject.withSelect)((select, { tipId }) => {
    const { isTipVisible, getAssociatedGuide } = select(store);
    const associatedGuide = getAssociatedGuide(tipId);
    return {
      isVisible: isTipVisible(tipId),
      hasNextTip: !!(associatedGuide && associatedGuide.nextTipId)
    };
  }),
  (0,external_wp_data_namespaceObject.withDispatch)((dispatch, { tipId }) => {
    const { dismissTip, disableTips } = dispatch(store);
    return {
      onDismiss() {
        dismissTip(tipId);
      },
      onDisable() {
        disableTips();
      }
    };
  })
)(DotTip);


;// ./node_modules/@wordpress/nux/build-module/index.js



external_wp_deprecated_default()("wp.nux", {
  since: "5.4",
  hint: "wp.components.Guide can be used to show a user guide.",
  version: "6.2"
});


(window.wp = window.wp || {}).nux = __webpack_exports__;
/******/ })()
;