import * as __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__ from "@wordpress/interactivity";
/******/ var __webpack_modules__ = ({

/***/ 438:
/***/ ((module) => {

module.exports = import("@wordpress/interactivity-router");;

/***/ })

/******/ });
/************************************************************************/
/******/ // The module cache
/******/ var __webpack_module_cache__ = {};
/******/ 
/******/ // The require function
/******/ function __webpack_require__(moduleId) {
/******/ 	// Check if module is in cache
/******/ 	var cachedModule = __webpack_module_cache__[moduleId];
/******/ 	if (cachedModule !== undefined) {
/******/ 		return cachedModule.exports;
/******/ 	}
/******/ 	// Create a new module (and put it into the cache)
/******/ 	var module = __webpack_module_cache__[moduleId] = {
/******/ 		// no module.id needed
/******/ 		// no module.loaded needed
/******/ 		exports: {}
/******/ 	};
/******/ 
/******/ 	// Execute the module function
/******/ 	__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 
/******/ 	// Return the exports of the module
/******/ 	return module.exports;
/******/ }
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/define property getters */
/******/ (() => {
/******/ 	// define getter functions for harmony exports
/******/ 	__webpack_require__.d = (exports, definition) => {
/******/ 		for(var key in definition) {
/******/ 			if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 				Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 			}
/******/ 		}
/******/ 	};
/******/ })();
/******/ 
/******/ /* webpack/runtime/hasOwnProperty shorthand */
/******/ (() => {
/******/ 	__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ })();
/******/ 
/************************************************************************/
var __webpack_exports__ = {};

;// external "@wordpress/interactivity"
var x = (y) => {
	var x = {}; __webpack_require__.d(x, y); return x
} 
var y = (x) => (() => (x))
const interactivity_namespaceObject = x({ ["getContext"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.getContext), ["getElement"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.getElement), ["store"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.store), ["withSyncEvent"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.withSyncEvent) });
;// ./node_modules/@wordpress/block-library/build-module/query/view.js

const isValidLink = (ref) => ref && ref instanceof window.HTMLAnchorElement && ref.href && (!ref.target || ref.target === "_self") && ref.origin === window.location.origin;
const isValidEvent = (event) => event.button === 0 && // Left clicks only.
!event.metaKey && // Open in new tab (Mac).
!event.ctrlKey && // Open in new tab (Windows).
!event.altKey && // Download.
!event.shiftKey && !event.defaultPrevented;
(0,interactivity_namespaceObject.store)(
  "core/query",
  {
    actions: {
      navigate: (0,interactivity_namespaceObject.withSyncEvent)(function* (event) {
        const ctx = (0,interactivity_namespaceObject.getContext)();
        const { ref } = (0,interactivity_namespaceObject.getElement)();
        const queryRef = ref.closest(
          ".wp-block-query[data-wp-router-region]"
        );
        if (isValidLink(ref) && isValidEvent(event)) {
          event.preventDefault();
          const { actions } = yield Promise.resolve(/* import() */).then(__webpack_require__.bind(__webpack_require__, 438));
          yield actions.navigate(ref.href);
          ctx.url = ref.href;
          const firstAnchor = `.wp-block-post-template a[href]`;
          queryRef.querySelector(firstAnchor)?.focus();
        }
      }),
      *prefetch() {
        const { ref } = (0,interactivity_namespaceObject.getElement)();
        if (isValidLink(ref)) {
          const { actions } = yield Promise.resolve(/* import() */).then(__webpack_require__.bind(__webpack_require__, 438));
          yield actions.prefetch(ref.href);
        }
      }
    },
    callbacks: {
      *prefetch() {
        const { url } = (0,interactivity_namespaceObject.getContext)();
        const { ref } = (0,interactivity_namespaceObject.getElement)();
        if (url && isValidLink(ref)) {
          const { actions } = yield Promise.resolve(/* import() */).then(__webpack_require__.bind(__webpack_require__, 438));
          yield actions.prefetch(ref.href);
        }
      }
    }
  },
  { lock: true }
);

