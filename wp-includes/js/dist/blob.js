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
/**
 * @type {Record<string, File|undefined>}
 */
const cache = {};

/**
 * Create a blob URL from a file.
 *
 * @param {File} file The file to create a blob URL for.
 *
 * @return {string} The blob URL.
 */
function createBlobURL(file) {
  const url = window.URL.createObjectURL(file);
  cache[url] = file;
  return url;
}

/**
 * Retrieve a file based on a blob URL. The file must have been created by
 * `createBlobURL` and not removed by `revokeBlobURL`, otherwise it will return
 * `undefined`.
 *
 * @param {string} url The blob URL.
 *
 * @return {File|undefined} The file for the blob URL.
 */
function getBlobByURL(url) {
  return cache[url];
}

/**
 * Retrieve a blob type based on URL. The file must have been created by
 * `createBlobURL` and not removed by `revokeBlobURL`, otherwise it will return
 * `undefined`.
 *
 * @param {string} url The blob URL.
 *
 * @return {string|undefined} The blob type.
 */
function getBlobTypeByURL(url) {
  return getBlobByURL(url)?.type.split('/')[0]; // 0: media type , 1: file extension eg ( type: 'image/jpeg' ).
}

/**
 * Remove the resource and file cache from memory.
 *
 * @param {string} url The blob URL.
 */
function revokeBlobURL(url) {
  if (cache[url]) {
    window.URL.revokeObjectURL(url);
  }
  delete cache[url];
}

/**
 * Check whether a url is a blob url.
 *
 * @param {string|undefined} url The URL.
 *
 * @return {boolean} Is the url a blob url?
 */
function isBlobURL(url) {
  if (!url || !url.indexOf) {
    return false;
  }
  return url.indexOf('blob:') === 0;
}

/**
 * Downloads a file, e.g., a text or readable stream, in the browser.
 * Appropriate for downloading smaller file sizes, e.g., < 5 MB.
 *
 * Example usage:
 *
 * ```js
 * 	const fileContent = JSON.stringify(
 * 		{
 * 			"title": "My Post",
 * 		},
 * 		null,
 * 		2
 * 	);
 * 	const filename = 'file.json';
 *
 * 	downloadBlob( filename, fileContent, 'application/json' );
 * ```
 *
 * @param {string}   filename    File name.
 * @param {BlobPart} content     File content (BufferSource | Blob | string).
 * @param {string}   contentType (Optional) File mime type. Default is `''`.
 */
function downloadBlob(filename, content, contentType = '') {
  if (!filename || !content) {
    return;
  }
  const file = new window.Blob([content], {
    type: contentType
  });
  const url = window.URL.createObjectURL(file);
  const anchorElement = document.createElement('a');
  anchorElement.href = url;
  anchorElement.download = filename;
  anchorElement.style.display = 'none';
  document.body.appendChild(anchorElement);
  anchorElement.click();
  document.body.removeChild(anchorElement);
  window.URL.revokeObjectURL(url);
}

(window.wp = window.wp || {}).blob = __webpack_exports__;
/******/ })()
;