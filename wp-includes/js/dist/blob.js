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
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createBlobURL: () => (/* binding */ createBlobURL),
/* harmony export */   downloadBlob: () => (/* binding */ downloadBlob),
/* harmony export */   getBlobByURL: () => (/* binding */ getBlobByURL),
/* harmony export */   getBlobTypeByURL: () => (/* binding */ getBlobTypeByURL),
/* harmony export */   isBlobURL: () => (/* binding */ isBlobURL),
/* harmony export */   revokeBlobURL: () => (/* binding */ revokeBlobURL)
/* harmony export */ });
const cache = {};
function createBlobURL(file) {
  const url = window.URL.createObjectURL(file);
  cache[url] = file;
  return url;
}
function getBlobByURL(url) {
  return cache[url];
}
function getBlobTypeByURL(url) {
  return getBlobByURL(url)?.type.split("/")[0];
}
function revokeBlobURL(url) {
  if (cache[url]) {
    window.URL.revokeObjectURL(url);
  }
  delete cache[url];
}
function isBlobURL(url) {
  if (!url || !url.indexOf) {
    return false;
  }
  return url.indexOf("blob:") === 0;
}
function downloadBlob(filename, content, contentType = "") {
  if (!filename || !content) {
    return;
  }
  const file = new window.Blob([content], { type: contentType });
  const url = window.URL.createObjectURL(file);
  const anchorElement = document.createElement("a");
  anchorElement.href = url;
  anchorElement.download = filename;
  anchorElement.style.display = "none";
  document.body.appendChild(anchorElement);
  anchorElement.click();
  document.body.removeChild(anchorElement);
  window.URL.revokeObjectURL(url);
}


(window.wp = window.wp || {}).blob = __webpack_exports__;
/******/ })()
;