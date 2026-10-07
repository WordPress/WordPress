var __create = Object.create;
var __defProp = Object.defineProperty;
var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __getProtoOf = Object.getPrototypeOf;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __commonJS = (cb, mod) => function __require() {
  return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
};
var __copyProps = (to, from, except, desc) => {
  if (from && typeof from === "object" || typeof from === "function") {
    for (let key of __getOwnPropNames(from))
      if (!__hasOwnProp.call(to, key) && key !== except)
        __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
  }
  return to;
};
var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(
  // If the importer is in node compatibility mode or this is not an ESM
  // file that has been converted to a CommonJS file using a Babel-
  // compatible transform (i.e. "__esModule" has not been set), then set
  // "default" to the CommonJS "module.exports" for node compatibility.
  isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", { value: mod, enumerable: true }) : target,
  mod
));

// package-external:@wordpress/primitives
var require_primitives = __commonJS({
  "package-external:@wordpress/primitives"(exports, module) {
    module.exports = window.wp.primitives;
  }
});

// vendor-external:react/jsx-runtime
var require_jsx_runtime = __commonJS({
  "vendor-external:react/jsx-runtime"(exports, module) {
    module.exports = window.ReactJSXRuntime;
  }
});

// package-external:@wordpress/data
var require_data = __commonJS({
  "package-external:@wordpress/data"(exports, module) {
    module.exports = window.wp.data;
  }
});

// package-external:@wordpress/preferences
var require_preferences = __commonJS({
  "package-external:@wordpress/preferences"(exports, module) {
    module.exports = window.wp.preferences;
  }
});

// packages/icons/build-module/library/home.mjs
var import_primitives = __toESM(require_primitives(), 1);
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
var home_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M13.75 19.25H19.25V8.5L12 5L4.75 8.5V19.25H10.25M13.75 19.25V13.7H10.25V19.25M13.75 19.25H10.25", vectorEffect: "non-scaling-stroke" }) });

// packages/icons/build-module/library/layout.mjs
var import_primitives2 = __toESM(require_primitives(), 1);
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
var layout_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M9.75 19.25H18C18.6904 19.25 19.25 18.6904 19.25 18V9.75M9.75 19.25H6C5.30964 19.25 4.75 18.6904 4.75 18V9.75M9.75 19.25V9.75M19.25 9.75V6C19.25 5.30964 18.6904 4.75 18 4.75H6C5.30964 4.75 4.75 5.30964 4.75 6V9.75M19.25 9.75H9.75M9.75 9.75H4.75", vectorEffect: "non-scaling-stroke" }) });

// packages/icons/build-module/library/navigation.mjs
var import_primitives3 = __toESM(require_primitives(), 1);
var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
var navigation_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsxs)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
  /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M9 16L13.5 12.8421L15 8L10.5 11.1579L9 16Z", fill: "currentColor", stroke: "none" }),
  /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M4.75 12C4.75 7.99593 7.99594 4.75 12 4.75C16.0041 4.75 19.25 7.99594 19.25 12C19.25 16.0041 16.0041 19.25 12 19.25C7.99594 19.25 4.75 16.0041 4.75 12Z", strokeLinejoin: "round", vectorEffect: "non-scaling-stroke" })
] });

// packages/icons/build-module/library/page.mjs
var import_primitives4 = __toESM(require_primitives(), 1);
var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
var page_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.Path, { d: "M8.5 8.25H15.5M8.5 11.75H15.5M8.5 15.25H15.5M18.25 18V6C18.25 5.30964 17.6904 4.75 17 4.75H7C6.30964 4.75 5.75 5.30964 5.75 6L5.75 18C5.75 18.6904 6.30964 19.25 7 19.25H17C17.6904 19.25 18.25 18.6904 18.25 18Z", vectorEffect: "non-scaling-stroke" }) });

// packages/icons/build-module/library/site-logo.mjs
var import_primitives5 = __toESM(require_primitives(), 1);
var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
var site_logo_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.Path, { d: "M5.07325 14.1472L8.71429 12L12.5 13.3333L15.75 11C15.75 11 17.934 11.9169 19.2032 12.8281M19.25 12C19.25 16.0041 16.0041 19.25 12 19.25C7.99594 19.25 4.75 16.0041 4.75 12C4.75 7.99594 7.99594 4.75 12 4.75C16.0041 4.75 19.25 7.99594 19.25 12Z", strokeLinejoin: "round", vectorEffect: "non-scaling-stroke" }) });

// packages/icons/build-module/library/styles.mjs
var import_primitives6 = __toESM(require_primitives(), 1);
var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
var styles_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsxs)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
  /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.Path, { d: "M12 4.75C7.99594 4.75 4.75 7.99593 4.75 12C4.75 16.0041 7.99594 19.25 12 19.25L12 4.75Z", fill: "currentColor", stroke: "none" }),
  /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.Path, { d: "M12 19.25C16.0041 19.25 19.25 16.0041 19.25 12C19.25 7.99594 16.0041 4.75 12 4.75C7.99594 4.75 4.75 7.99594 4.75 12C4.75 16.0041 7.99594 19.25 12 19.25Z", strokeLinejoin: "round", vectorEffect: "non-scaling-stroke" })
] });

// packages/icons/build-module/library/symbol-filled.mjs
var import_primitives7 = __toESM(require_primitives(), 1);
var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
var symbol_filled_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsxs)(import_primitives7.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
  /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.Path, { d: "M15.2071 6.20711L20.2929 11.2929C20.6834 11.6834 20.6834 12.3166 20.2929 12.7071L15.2071 17.7929C14.8166 18.1834 14.1834 18.1834 13.7929 17.7929L8.70711 12.7071C8.31658 12.3166 8.31658 11.6834 8.70711 11.2929L13.7929 6.20711C14.1834 5.81658 14.8166 5.81658 15.2071 6.20711Z", fill: "currentColor", vectorEffect: "non-scaling-stroke" }),
  /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.Path, { d: "M9.5 5.5L3.70711 11.2929C3.31658 11.6834 3.31658 12.3166 3.70711 12.7071L9.5 18.5", vectorEffect: "non-scaling-stroke" })
] });

// packages/icons/build-module/library/symbol.mjs
var import_primitives8 = __toESM(require_primitives(), 1);
var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
var symbol_default = /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)(import_primitives8.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
  /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.Path, { d: "M15.2071 6.20711L20.2929 11.2929C20.6834 11.6834 20.6834 12.3166 20.2929 12.7071L15.2071 17.7929C14.8166 18.1834 14.1834 18.1834 13.7929 17.7929L8.70711 12.7071C8.31658 12.3166 8.31658 11.6834 8.70711 11.2929L13.7929 6.20711C14.1834 5.81658 14.8166 5.81658 15.2071 6.20711Z", vectorEffect: "non-scaling-stroke" }),
  /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.Path, { d: "M9.5 5.5L3.70711 11.2929C3.31658 11.6834 3.31658 12.3166 3.70711 12.7071L9.5 18.5", vectorEffect: "non-scaling-stroke" })
] });

// packages/edit-site-init/build-module/index.mjs
var import_data = __toESM(require_data(), 1);
var import_preferences = __toESM(require_preferences(), 1);
import { store as bootStore } from "@wordpress/boot";
async function init() {
  const menuIcons = {
    home: { icon: home_default },
    identity: { icon: site_logo_default },
    styles: { icon: styles_default },
    navigation: { icon: navigation_default },
    pages: { icon: page_default },
    templateParts: { icon: symbol_filled_default },
    patterns: { icon: symbol_default },
    templates: { icon: layout_default }
  };
  Object.entries(menuIcons).forEach(([id, { icon }]) => {
    (0, import_data.dispatch)(bootStore).updateMenuItem(id, { icon });
  });
  const entityLinks = {
    default: { list: "/types/{type}", edit: "/types/{type}/edit/{id}" },
    wp_template: { list: "/templates" },
    wp_template_part: { list: "/template-parts" },
    wp_block: { list: "/patterns" },
    wp_navigation: { list: "/navigation" }
  };
  Object.entries(entityLinks).forEach(([postType, links]) => {
    (0, import_data.dispatch)(bootStore).registerEntityLinks(postType, {
      ...entityLinks.default,
      ...links
    });
  });
  (0, import_data.dispatch)(import_preferences.store).setDefaults("core", {
    allowRightClickOverrides: true,
    enableChoosePatternModal: true,
    showBlockBreadcrumbs: true
  });
}
export {
  init
};
