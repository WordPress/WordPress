import * as __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__ from "@wordpress/interactivity";
/******/ // The require scope
/******/ var __webpack_require__ = {};
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
const interactivity_namespaceObject = x({ ["store"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.store) });
;// ./node_modules/@wordpress/block-library/build-module/file/utils/index.js
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

;// ./node_modules/@wordpress/block-library/build-module/file/view.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */

(0,interactivity_namespaceObject.store)('core/file', {
  state: {
    get hasPdfPreview() {
      return browserSupportsPdfs();
    }
  }
}, {
  lock: true
});

