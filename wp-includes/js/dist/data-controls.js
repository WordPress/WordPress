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
/**
 * WordPress dependencies
 */



/**
 * Dispatches a control action for triggering an api fetch call.
 *
 * @param {Object} request Arguments for the fetch request.
 *
 * @example
 * ```js
 * import { apiFetch } from '@wordpress/data-controls';
 *
 * // Action generator using apiFetch
 * export function* myAction() {
 * 	const path = '/v2/my-api/items';
 * 	const items = yield apiFetch( { path } );
 * 	// do something with the items.
 * }
 * ```
 *
 * @return {Object} The control descriptor.
 */
function apiFetch(request) {
  return {
    type: 'API_FETCH',
    request
  };
}

/**
 * Control for resolving a selector in a registered data store.
 * Alias for the `resolveSelect` built-in control in the `@wordpress/data` package.
 *
 * @param storeNameOrDescriptor The store object or identifier.
 * @param selectorName          The selector name.
 * @param args                  Arguments passed without change to the `@wordpress/data` control.
 */
function build_module_select(storeNameOrDescriptor, selectorName, ...args) {
  external_wp_deprecated_default()('`select` control in `@wordpress/data-controls`', {
    since: '5.7',
    alternative: 'built-in `resolveSelect` control in `@wordpress/data`'
  });
  return external_wp_data_namespaceObject.controls.resolveSelect(storeNameOrDescriptor, selectorName, ...args);
}

/**
 * Control for calling a selector in a registered data store.
 * Alias for the `select` built-in control in the `@wordpress/data` package.
 *
 * @param storeNameOrDescriptor The store object or identifier.
 * @param selectorName          The selector name.
 * @param args                  Arguments passed without change to the `@wordpress/data` control.
 */
function syncSelect(storeNameOrDescriptor, selectorName, ...args) {
  external_wp_deprecated_default()('`syncSelect` control in `@wordpress/data-controls`', {
    since: '5.7',
    alternative: 'built-in `select` control in `@wordpress/data`'
  });
  return external_wp_data_namespaceObject.controls.select(storeNameOrDescriptor, selectorName, ...args);
}

/**
 * Control for dispatching an action in a registered data store.
 * Alias for the `dispatch` control in the `@wordpress/data` package.
 *
 * @param storeNameOrDescriptor The store object or identifier.
 * @param actionName            The action name.
 * @param args                  Arguments passed without change to the `@wordpress/data` control.
 */
function dispatch(storeNameOrDescriptor, actionName, ...args) {
  external_wp_deprecated_default()('`dispatch` control in `@wordpress/data-controls`', {
    since: '5.7',
    alternative: 'built-in `dispatch` control in `@wordpress/data`'
  });
  return external_wp_data_namespaceObject.controls.dispatch(storeNameOrDescriptor, actionName, ...args);
}

/**
 * Dispatches a control action for awaiting on a promise to be resolved.
 *
 * @param {Object} promise Promise to wait for.
 *
 * @example
 * ```js
 * import { __unstableAwaitPromise } from '@wordpress/data-controls';
 *
 * // Action generator using apiFetch
 * export function* myAction() {
 * 	const promise = getItemsAsync();
 * 	const items = yield __unstableAwaitPromise( promise );
 * 	// do something with the items.
 * }
 * ```
 *
 * @return {Object} The control descriptor.
 */
const __unstableAwaitPromise = function (promise) {
  return {
    type: 'AWAIT_PROMISE',
    promise
  };
};

/**
 * The default export is what you use to register the controls with your custom
 * store.
 *
 * @example
 * ```js
 * // WordPress dependencies
 * import { controls } from '@wordpress/data-controls';
 * import { registerStore } from '@wordpress/data';
 *
 * // Internal dependencies
 * import reducer from './reducer';
 * import * as selectors from './selectors';
 * import * as actions from './actions';
 * import * as resolvers from './resolvers';
 *
 * registerStore( 'my-custom-store', {
 * reducer,
 * controls,
 * actions,
 * selectors,
 * resolvers,
 * } );
 * ```
 * @return {Object} An object for registering the default controls with the
 * store.
 */
const controls = {
  AWAIT_PROMISE: ({
    promise
  }) => promise,
  API_FETCH({
    request
  }) {
    return external_wp_apiFetch_default()(request);
  }
};

(window.wp = window.wp || {}).dataControls = __webpack_exports__;
/******/ })()
;