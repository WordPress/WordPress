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

// packages/icons/build-module/library/home.mjs
var import_primitives = __toESM(require_primitives(), 1);
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
var home_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M12 4L4 7.9V20h16V7.9L12 4zm6.5 14.5H14V13h-4v5.5H5.5V8.8L12 5.7l6.5 3.1v9.7z" }) });

// packages/icons/build-module/library/layout.mjs
var import_primitives2 = __toESM(require_primitives(), 1);
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
var layout_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M18 5.5H6a.5.5 0 00-.5.5v3h13V6a.5.5 0 00-.5-.5zm.5 5H10v8h8a.5.5 0 00.5-.5v-7.5zm-10 0h-3V18a.5.5 0 00.5.5h2.5v-8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z" }) });

// packages/icons/build-module/library/navigation.mjs
var import_primitives3 = __toESM(require_primitives(), 1);
var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
var navigation_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M12 4c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 14.5c-3.6 0-6.5-2.9-6.5-6.5S8.4 5.5 12 5.5s6.5 2.9 6.5 6.5-2.9 6.5-6.5 6.5zM9 16l4.5-3L15 8.4l-4.5 3L9 16z" }) });

// packages/icons/build-module/library/page.mjs
var import_primitives4 = __toESM(require_primitives(), 1);
var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
var page_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsxs)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: [
  /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.Path, { d: "M15.5 7.5h-7V9h7V7.5Zm-7 3.5h7v1.5h-7V11Zm7 3.5h-7V16h7v-1.5Z" }),
  /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.Path, { d: "M17 4H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2ZM7 5.5h10a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5H7a.5.5 0 0 1-.5-.5V6a.5.5 0 0 1 .5-.5Z" })
] });

// packages/icons/build-module/library/styles.mjs
var import_primitives5 = __toESM(require_primitives(), 1);
var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
var styles_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.Path, { fillRule: "evenodd", clipRule: "evenodd", d: "M20 12a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-1.5 0a6.5 6.5 0 0 1-6.5 6.5v-13a6.5 6.5 0 0 1 6.5 6.5Z" }) });

// packages/icons/build-module/library/symbol-filled.mjs
var import_primitives6 = __toESM(require_primitives(), 1);
var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
var symbol_filled_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.Path, { d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-17.6 1L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z" }) });

// packages/icons/build-module/library/symbol.mjs
var import_primitives7 = __toESM(require_primitives(), 1);
var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
var symbol_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.Path, { d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-1 1.4l-5.6 5.6c-.1.1-.3.1-.4 0l-5.6-5.6c-.1-.1-.1-.3 0-.4l5.6-5.6s.1-.1.2-.1.1 0 .2.1l5.6 5.6c.1.1.1.3 0 .4zm-16.6-.4L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z" }) });

// packages/edit-site-init/build-module/index.mjs
var import_data = __toESM(require_data(), 1);
import { store as bootStore } from "@wordpress/boot";
async function init() {
  const menuIcons = {
    home: { icon: home_default },
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
}
export {
  init
};
