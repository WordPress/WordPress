/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

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
  } // Android tablets are the noteable exception.


  if (window.navigator.userAgent.indexOf('Android') > -1) {
    return false;
  } // iPad pretends to be a Mac.


  if (window.navigator.userAgent.indexOf('Macintosh') > -1 && window.navigator.maxTouchPoints && window.navigator.maxTouchPoints > 2) {
    return false;
  } // IE only supports PDFs when there's an ActiveX object available for it.


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
/**
 * Hides all .wp-block-file__embed elements on the document. This function is only intended
 * to be run on the front-end, it may have weird side effects running in the block editor.
 */


const hidePdfEmbedsOnUnsupportedBrowsers = () => {
  if (!browserSupportsPdfs()) {
    const embeds = document.getElementsByClassName('wp-block-file__embed');
    Array.from(embeds).forEach(embed => {
      embed.style.display = 'none';
    });
  }
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/file/view.js
/**
 * Internal dependencies
 */

document.addEventListener('DOMContentLoaded', hidePdfEmbedsOnUnsupportedBrowsers);

/******/ })()
;