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
/************************************************************************/
var __webpack_exports__ = {};

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "default": () => (/* binding */ warning)
});

;// ./node_modules/@wordpress/warning/build-module/utils.js
const logged = /* @__PURE__ */ new Set();


;// ./node_modules/@wordpress/warning/build-module/index.js

function isDev() {
  return true === true;
}
function warning(message) {
  if (!isDev()) {
    return;
  }
  if (logged.has(message)) {
    return;
  }
  console.warn(message);
  try {
    throw Error(message);
  } catch (x) {
  }
  logged.add(message);
}


(window.wp = window.wp || {}).warning = __webpack_exports__["default"];
/******/ })()
;