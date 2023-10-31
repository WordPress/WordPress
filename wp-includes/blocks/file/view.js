"use strict";
(self["__WordPressPrivateInteractivityAPI__"] = self["__WordPressPrivateInteractivityAPI__"] || []).push([[81],{

/***/ 149:
/***/ (function(__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) {


// EXTERNAL MODULE: ./node_modules/@wordpress/interactivity/src/index.js + 15 modules
var src = __webpack_require__(754);
;// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/file/utils/index.js
/**
 * Uses a combination of user agent matching and feature detection to determine whether
 * the current browser supports rendering PDFs inline.
 *
 * @return {boolean} Whether or not the browser supports inline PDFs.
 */
const browserSupportsPdfs = () => {
  // Most mobile devices include "Mobi" in their UA.
  if (window.navigator.userAgent.indexOf('Mobi') > -1) {
    return false;
  }

  // Android tablets are the noteable exception.
  if (window.navigator.userAgent.indexOf('Android') > -1) {
    return false;
  }

  // iPad pretends to be a Mac.
  if (window.navigator.userAgent.indexOf('Macintosh') > -1 && window.navigator.maxTouchPoints && window.navigator.maxTouchPoints > 2) {
    return false;
  }

  // IE only supports PDFs when there's an ActiveX object available for it.
  if (!!(window.ActiveXObject || 'ActiveXObject' in window) && !(createActiveXObject('AcroPDF.PDF') || createActiveXObject('PDF.PdfCtrl'))) {
    return false;
  }
  return true;
};

/**
 * Helper function for creating ActiveX objects, catching any errors that are thrown
 * when it's generated.
 *
 * @param {string} type The name of the ActiveX object to create.
 * @return {window.ActiveXObject|undefined} The generated ActiveXObject, or null if it failed.
 */
const createActiveXObject = type => {
  let ax;
  try {
    ax = new window.ActiveXObject(type);
  } catch (e) {
    ax = undefined;
  }
  return ax;
};
;// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/file/view.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */

(0,src/* store */.h)({
  selectors: {
    core: {
      file: {
        hasPdfPreview: browserSupportsPdfs
      }
    }
  }
});

/***/ })

},
/******/ function(__webpack_require__) { // webpackRuntimeModules
/******/ var __webpack_exec__ = function(moduleId) { return __webpack_require__(__webpack_require__.s = moduleId); }
/******/ var __webpack_exports__ = (__webpack_exec__(149));
/******/ }
]);