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
  BlockQuotation: () => (/* reexport */ BlockQuotation),
  Circle: () => (/* reexport */ Circle),
  Defs: () => (/* reexport */ Defs),
  G: () => (/* reexport */ G),
  HorizontalRule: () => (/* reexport */ HorizontalRule),
  Line: () => (/* reexport */ Line),
  LinearGradient: () => (/* reexport */ LinearGradient),
  Path: () => (/* reexport */ Path),
  Polygon: () => (/* reexport */ Polygon),
  RadialGradient: () => (/* reexport */ RadialGradient),
  Rect: () => (/* reexport */ Rect),
  SVG: () => (/* reexport */ SVG),
  Stop: () => (/* reexport */ Stop),
  View: () => (/* reexport */ View)
});

;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// ./node_modules/clsx/dist/clsx.mjs
function r(e){var t,f,n="";if("string"==typeof e||"number"==typeof e)n+=e;else if("object"==typeof e)if(Array.isArray(e)){var o=e.length;for(t=0;t<o;t++)e[t]&&(f=r(e[t]))&&(n&&(n+=" "),n+=f)}else for(f in e)e[f]&&(n&&(n+=" "),n+=f);return n}function clsx(){for(var e,t,f=0,n="",o=arguments.length;f<o;f++)(e=arguments[f])&&(t=r(e))&&(n&&(n+=" "),n+=t);return n}/* harmony default export */ const dist_clsx = (clsx);
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// ./node_modules/@wordpress/primitives/build-module/svg/index.js



const Circle = (props) => (0,external_wp_element_namespaceObject.createElement)("circle", props);
const G = (props) => (0,external_wp_element_namespaceObject.createElement)("g", props);
const Line = (props) => (0,external_wp_element_namespaceObject.createElement)("line", props);
const Path = (props) => (0,external_wp_element_namespaceObject.createElement)("path", props);
const Polygon = (props) => (0,external_wp_element_namespaceObject.createElement)("polygon", props);
const Rect = (props) => (0,external_wp_element_namespaceObject.createElement)("rect", props);
const Defs = (props) => (0,external_wp_element_namespaceObject.createElement)("defs", props);
const RadialGradient = (props) => (0,external_wp_element_namespaceObject.createElement)("radialGradient", props);
const LinearGradient = (props) => (0,external_wp_element_namespaceObject.createElement)("linearGradient", props);
const Stop = (props) => (0,external_wp_element_namespaceObject.createElement)("stop", props);
const SVG = (0,external_wp_element_namespaceObject.forwardRef)(
  /**
   * @param {SVGProps}                                    props isPressed indicates whether the SVG should appear as pressed.
   *                                                            Other props will be passed through to svg component.
   * @param {import('react').ForwardedRef<SVGSVGElement>} ref   The forwarded ref to the SVG element.
   *
   * @return {JSX.Element} Stop component
   */
  ({ className, isPressed, ...props }, ref) => {
    const appliedProps = {
      ...props,
      className: dist_clsx(className, { "is-pressed": isPressed }) || void 0,
      "aria-hidden": true,
      focusable: false
    };
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("svg", { ...appliedProps, ref });
  }
);
SVG.displayName = "SVG";


;// ./node_modules/@wordpress/primitives/build-module/horizontal-rule/index.js
const HorizontalRule = "hr";


;// ./node_modules/@wordpress/primitives/build-module/block-quotation/index.js
const BlockQuotation = "blockquote";


;// ./node_modules/@wordpress/primitives/build-module/view/index.js
const View = "div";


;// ./node_modules/@wordpress/primitives/build-module/index.js





(window.wp = window.wp || {}).primitives = __webpack_exports__;
/******/ })()
;