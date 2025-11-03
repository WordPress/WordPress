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
  __unstableAwaitPromise: () => (/* binding */ __unstableAwaitPromise),
  apiFetch: () => (/* binding */ apiFetch),
  controls: () => (/* binding */ controls),
  dispatch: () => (/* binding */ dispatch),
  select: () => (/* binding */ build_module_select),
  syncSelect: () => (/* binding */ syncSelect)
});

;// external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// ./node_modules/@wordpress/data-controls/build-module/index.js



function apiFetch(request) {
  return {
    type: "API_FETCH",
    request
  };
}
function build_module_select(storeNameOrDescriptor, selectorName, ...args) {
  external_wp_deprecated_default()("`select` control in `@wordpress/data-controls`", {
    since: "5.7",
    alternative: "built-in `resolveSelect` control in `@wordpress/data`"
  });
  return external_wp_data_namespaceObject.controls.resolveSelect(
    storeNameOrDescriptor,
    selectorName,
    ...args
  );
}
function syncSelect(storeNameOrDescriptor, selectorName, ...args) {
  external_wp_deprecated_default()("`syncSelect` control in `@wordpress/data-controls`", {
    since: "5.7",
    alternative: "built-in `select` control in `@wordpress/data`"
  });
  return external_wp_data_namespaceObject.controls.select(storeNameOrDescriptor, selectorName, ...args);
}
function dispatch(storeNameOrDescriptor, actionName, ...args) {
  external_wp_deprecated_default()("`dispatch` control in `@wordpress/data-controls`", {
    since: "5.7",
    alternative: "built-in `dispatch` control in `@wordpress/data`"
  });
  return external_wp_data_namespaceObject.controls.dispatch(storeNameOrDescriptor, actionName, ...args);
}
const __unstableAwaitPromise = function(promise) {
  return {
    type: "AWAIT_PROMISE",
    promise
  };
};
const controls = {
  AWAIT_PROMISE({ promise }) {
    return promise;
  },
  API_FETCH({ request }) {
    return external_wp_apiFetch_default()(request);
  }
};


(window.wp = window.wp || {}).dataControls = __webpack_exports__;
/******/ })()
;