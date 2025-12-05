/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!*******************************************************!*\
  !*** ../core/editor/loader/v2/js/editor-loader-v2.js ***!
  \*******************************************************/


var _window$elementorV;
window.__elementorEditorV1LoadingPromise = new Promise(function (resolve) {
  window.addEventListener('elementor/init', function () {
    resolve();
  }, {
    once: true
  });
});
window.elementor.start();
if (!((_window$elementorV = window.elementorV2) !== null && _window$elementorV !== void 0 && _window$elementorV.editor)) {
  throw new Error('The "@elementor/editor" package was not loaded.');
}
window.elementorV2.editor.start(document.getElementById('elementor-editor-wrapper-v2'));
/******/ })()
;
//# sourceMappingURL=editor-loader-v2.js.map