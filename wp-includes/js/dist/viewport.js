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
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  ifViewportMatches: () => (/* reexport */ if_viewport_matches_default),
  store: () => (/* reexport */ store),
  withViewportMatch: () => (/* reexport */ with_viewport_match_default)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/viewport/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  setIsMatching: () => (setIsMatching)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/viewport/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  isViewportMatch: () => (isViewportMatch)
});

;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// ./node_modules/@wordpress/viewport/build-module/store/reducer.js
function reducer(state = {}, action) {
  switch (action.type) {
    case "SET_IS_MATCHING":
      return action.values;
  }
  return state;
}
var reducer_default = reducer;


;// ./node_modules/@wordpress/viewport/build-module/store/actions.js
function setIsMatching(values) {
  return {
    type: "SET_IS_MATCHING",
    values
  };
}


;// ./node_modules/@wordpress/viewport/build-module/store/selectors.js
function isViewportMatch(state, query) {
  if (query.indexOf(" ") === -1) {
    query = ">= " + query;
  }
  return !!state[query];
}


;// ./node_modules/@wordpress/viewport/build-module/store/index.js




const STORE_NAME = "core/viewport";
const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer_default,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);


;// ./node_modules/@wordpress/viewport/build-module/listener.js



const addDimensionsEventListener = (breakpoints, operators) => {
  const setIsMatching = (0,external_wp_compose_namespaceObject.debounce)(
    () => {
      const values = Object.fromEntries(
        queries.map(([key, query]) => [key, query.matches])
      );
      (0,external_wp_data_namespaceObject.dispatch)(store).setIsMatching(values);
    },
    0,
    { leading: true }
  );
  const operatorEntries = Object.entries(operators);
  const queries = Object.entries(breakpoints).flatMap(
    ([name, width]) => {
      return operatorEntries.map(([operator, condition]) => {
        const list = window.matchMedia(
          `(${condition}: ${width}px)`
        );
        list.addEventListener("change", setIsMatching);
        return [`${operator} ${name}`, list];
      });
    }
  );
  window.addEventListener("orientationchange", setIsMatching);
  setIsMatching();
  setIsMatching.flush();
};
var listener_default = addDimensionsEventListener;


;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// ./node_modules/@wordpress/viewport/build-module/with-viewport-match.js


const withViewportMatch = (queries) => {
  const queryEntries = Object.entries(queries);
  const useViewPortQueriesResult = () => Object.fromEntries(
    queryEntries.map(([key, query]) => {
      let [operator, breakpointName] = query.split(" ");
      if (breakpointName === void 0) {
        breakpointName = operator;
        operator = ">=";
      }
      return [key, (0,external_wp_compose_namespaceObject.useViewportMatch)(breakpointName, operator)];
    })
  );
  return (0,external_wp_compose_namespaceObject.createHigherOrderComponent)((WrappedComponent) => {
    return (0,external_wp_compose_namespaceObject.pure)((props) => {
      const queriesResult = useViewPortQueriesResult();
      return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(WrappedComponent, { ...props, ...queriesResult });
    });
  }, "withViewportMatch");
};
var with_viewport_match_default = withViewportMatch;


;// ./node_modules/@wordpress/viewport/build-module/if-viewport-matches.js


const ifViewportMatches = (query) => (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(
  (0,external_wp_compose_namespaceObject.compose)([
    with_viewport_match_default({
      isViewportMatch: query
    }),
    (0,external_wp_compose_namespaceObject.ifCondition)((props) => props.isViewportMatch)
  ]),
  "ifViewportMatches"
);
var if_viewport_matches_default = ifViewportMatches;


;// ./node_modules/@wordpress/viewport/build-module/index.js




const BREAKPOINTS = {
  huge: 1440,
  wide: 1280,
  large: 960,
  medium: 782,
  small: 600,
  mobile: 480
};
const OPERATORS = {
  "<": "max-width",
  ">=": "min-width"
};
listener_default(BREAKPOINTS, OPERATORS);


(window.wp = window.wp || {}).viewport = __webpack_exports__;
/******/ })()
;