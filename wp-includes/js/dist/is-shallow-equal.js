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
  "default": () => (/* binding */ isShallowEqual),
  isShallowEqualArrays: () => (/* reexport */ isShallowEqualArrays),
  isShallowEqualObjects: () => (/* reexport */ isShallowEqualObjects)
});

;// ./node_modules/@wordpress/is-shallow-equal/build-module/objects.js
function isShallowEqualObjects(a, b) {
  if (a === b) {
    return true;
  }
  const aKeys = Object.keys(a);
  const bKeys = Object.keys(b);
  if (aKeys.length !== bKeys.length) {
    return false;
  }
  let i = 0;
  while (i < aKeys.length) {
    const key = aKeys[i];
    const aValue = a[key];
    if (
      // In iterating only the keys of the first object after verifying
      // equal lengths, account for the case that an explicit `undefined`
      // value in the first is implicitly undefined in the second.
      //
      // Example: isShallowEqualObjects( { a: undefined }, { b: 5 } )
      aValue === void 0 && !b.hasOwnProperty(key) || aValue !== b[key]
    ) {
      return false;
    }
    i++;
  }
  return true;
}


;// ./node_modules/@wordpress/is-shallow-equal/build-module/arrays.js
function isShallowEqualArrays(a, b) {
  if (a === b) {
    return true;
  }
  if (a.length !== b.length) {
    return false;
  }
  for (let i = 0, len = a.length; i < len; i++) {
    if (a[i] !== b[i]) {
      return false;
    }
  }
  return true;
}


;// ./node_modules/@wordpress/is-shallow-equal/build-module/index.js




function isShallowEqual(a, b) {
  if (a && b) {
    if (a.constructor === Object && b.constructor === Object) {
      return isShallowEqualObjects(a, b);
    } else if (Array.isArray(a) && Array.isArray(b)) {
      return isShallowEqualArrays(a, b);
    }
  }
  return a === b;
}


(window.wp = window.wp || {}).isShallowEqual = __webpack_exports__;
/******/ })()
;