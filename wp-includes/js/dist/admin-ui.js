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
  NavigableRegion: () => (/* reexport */ navigable_region_default),
  Page: () => (/* reexport */ page_default)
});

;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// ./node_modules/clsx/dist/clsx.mjs
function r(e){var t,f,n="";if("string"==typeof e||"number"==typeof e)n+=e;else if("object"==typeof e)if(Array.isArray(e)){var o=e.length;for(t=0;t<o;t++)e[t]&&(f=r(e[t]))&&(n&&(n+=" "),n+=f)}else for(f in e)e[f]&&(n&&(n+=" "),n+=f);return n}function clsx(){for(var e,t,f=0,n="",o=arguments.length;f<o;f++)(e=arguments[f])&&(t=r(e))&&(n&&(n+=" "),n+=t);return n}/* harmony default export */ const dist_clsx = (clsx);
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// ./node_modules/@wordpress/admin-ui/build-module/navigable-region/index.js



const NavigableRegion = (0,external_wp_element_namespaceObject.forwardRef)(
  ({ children, className, ariaLabel, as: Tag = "div", ...props }, ref) => {
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      Tag,
      {
        ref,
        className: dist_clsx("admin-ui-navigable-region", className),
        "aria-label": ariaLabel,
        role: "region",
        tabIndex: "-1",
        ...props,
        children
      }
    );
  }
);
NavigableRegion.displayName = "NavigableRegion";
var navigable_region_default = NavigableRegion;


;// external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// ./node_modules/@wordpress/admin-ui/build-module/page/header.js


function Header({
  breadcrumbs,
  badges,
  title,
  subTitle,
  actions
}) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalVStack, { className: "admin-ui-page__header", as: "header", children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
      external_wp_components_namespaceObject.__experimentalHStack,
      {
        className: "admin-ui-page__header-title",
        justify: "space-between",
        spacing: 2,
        children: [
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalHStack, { spacing: 2, children: [
            title && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalHeading, { as: "h2", level: 3, weight: 500, truncate: true, children: title }),
            breadcrumbs,
            badges
          ] }),
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            external_wp_components_namespaceObject.__experimentalHStack,
            {
              style: { width: "auto", flexShrink: 0 },
              spacing: 2,
              className: "admin-ui-page__header-actions",
              children: actions
            }
          )
        ]
      }
    ),
    subTitle && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { className: "admin-ui-page__header-subtitle", children: subTitle })
  ] });
}


;// ./node_modules/@wordpress/admin-ui/build-module/page/index.js




function Page({
  breadcrumbs,
  badges,
  title,
  subTitle,
  children,
  className,
  actions,
  hasPadding = false
}) {
  const classes = dist_clsx("admin-ui-page", className);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(navigable_region_default, { className: classes, ariaLabel: title, children: [
    (title || breadcrumbs || badges) && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      Header,
      {
        breadcrumbs,
        badges,
        title,
        subTitle,
        actions
      }
    ),
    hasPadding ? /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "admin-ui-page__content has-padding", children }) : children
  ] });
}
var page_default = Page;


;// ./node_modules/@wordpress/admin-ui/build-module/index.js




(window.wp = window.wp || {}).adminUi = __webpack_exports__;
/******/ })()
;