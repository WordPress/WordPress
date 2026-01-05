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

// packages/icons/build-module/library/home.js
var import_primitives = __toESM(require_primitives());
var import_jsx_runtime = __toESM(require_jsx_runtime());
var home_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M12 4L4 7.9V20h16V7.9L12 4zm6.5 14.5H14V13h-4v5.5H5.5V8.8L12 5.7l6.5 3.1v9.7z" }) });

// packages/icons/build-module/library/layout.js
var import_primitives2 = __toESM(require_primitives());
var import_jsx_runtime2 = __toESM(require_jsx_runtime());
var layout_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M18 5.5H6a.5.5 0 00-.5.5v3h13V6a.5.5 0 00-.5-.5zm.5 5H10v8h8a.5.5 0 00.5-.5v-7.5zm-10 0h-3V18a.5.5 0 00.5.5h2.5v-8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z" }) });

// packages/icons/build-module/library/navigation.js
var import_primitives3 = __toESM(require_primitives());
var import_jsx_runtime3 = __toESM(require_jsx_runtime());
var navigation_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M12 4c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 14.5c-3.6 0-6.5-2.9-6.5-6.5S8.4 5.5 12 5.5s6.5 2.9 6.5 6.5-2.9 6.5-6.5 6.5zM9 16l4.5-3L15 8.4l-4.5 3L9 16z" }) });

// packages/icons/build-module/library/page.js
var import_primitives4 = __toESM(require_primitives());
var import_jsx_runtime4 = __toESM(require_jsx_runtime());
var page_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsxs)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: [
  /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.Path, { d: "M15.5 7.5h-7V9h7V7.5Zm-7 3.5h7v1.5h-7V11Zm7 3.5h-7V16h7v-1.5Z" }),
  /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.Path, { d: "M17 4H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2ZM7 5.5h10a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5H7a.5.5 0 0 1-.5-.5V6a.5.5 0 0 1 .5-.5Z" })
] });

// packages/icons/build-module/library/styles.js
var import_primitives5 = __toESM(require_primitives());
var import_jsx_runtime5 = __toESM(require_jsx_runtime());
var styles_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
  import_primitives5.Path,
  {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M20 12a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-1.5 0a6.5 6.5 0 0 1-6.5 6.5v-13a6.5 6.5 0 0 1 6.5 6.5Z"
  }
) });

// packages/icons/build-module/library/symbol-filled.js
var import_primitives6 = __toESM(require_primitives());
var import_jsx_runtime6 = __toESM(require_jsx_runtime());
var symbol_filled_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.Path, { d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-17.6 1L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z" }) });

// packages/icons/build-module/library/symbol.js
var import_primitives7 = __toESM(require_primitives());
var import_jsx_runtime7 = __toESM(require_jsx_runtime());
var symbol_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.Path, { d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-1 1.4l-5.6 5.6c-.1.1-.3.1-.4 0l-5.6-5.6c-.1-.1-.1-.3 0-.4l5.6-5.6s.1-.1.2-.1.1 0 .2.1l5.6 5.6c.1.1.1.3 0 .4zm-16.6-.4L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z" }) });

// packages/icons/build-module/library/typography.js
var import_primitives8 = __toESM(require_primitives());
var import_jsx_runtime8 = __toESM(require_jsx_runtime());
var typography_default = /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.Path, { d: "m8.6 7 3.9 10.8h-1.7l-1-2.8H5.7l-1 2.8H3L6.9 7h1.7Zm-2.4 6.6h3L7.7 9.3l-1.5 4.3ZM17.691 8.879c.473 0 .88.055 1.221.165.352.1.643.264.875.495.274.253.456.572.544.957.088.374.132.83.132 1.37v4.554c0 .274.033.472.099.593.077.11.198.166.363.166.11 0 .215-.028.313-.083.11-.055.237-.137.38-.247l.165.28a3.304 3.304 0 0 1-.71.446c-.23.11-.527.165-.89.165-.352 0-.639-.055-.858-.165-.22-.11-.386-.27-.495-.479-.1-.209-.149-.468-.149-.775-.286.462-.627.814-1.023 1.056-.396.242-.858.363-1.386.363-.462 0-.858-.088-1.188-.264a1.752 1.752 0 0 1-.742-.726 2.201 2.201 0 0 1-.248-1.056c0-.484.11-.875.33-1.172.22-.308.5-.556.841-.742.352-.187.721-.341 1.106-.462.396-.132.765-.253 1.106-.363.351-.121.637-.259.857-.413.232-.154.347-.357.347-.61V10.81c0-.396-.066-.71-.198-.941a1.05 1.05 0 0 0-.511-.511 1.763 1.763 0 0 0-.76-.149c-.253 0-.522.039-.808.116a1.165 1.165 0 0 0-.677.412 1.1 1.1 0 0 1 .595.396c.165.187.247.424.247.71 0 .307-.104.55-.313.726-.198.176-.451.263-.76.263-.34 0-.594-.104-.758-.313a1.231 1.231 0 0 1-.248-.759c0-.297.072-.539.214-.726.154-.187.352-.363.595-.528.264-.176.6-.324 1.006-.445.418-.121.88-.182 1.386-.182Zm.99 3.729a1.57 1.57 0 0 1-.528.462c-.231.121-.479.248-.742.38a5.377 5.377 0 0 0-.76.462c-.23.165-.423.38-.577.643-.154.264-.231.6-.231 1.007 0 .429.11.77.33 1.023.22.242.517.363.891.363.308 0 .594-.088.858-.264.275-.176.528-.44.759-.792v-3.284Z" }) });

// packages/edit-site-init/build-module/index.js
var import_data = __toESM(require_data());
import { store as bootStore } from "@wordpress/boot";
async function init() {
  const menuIcons = {
    home: { icon: home_default },
    styles: { icon: styles_default },
    navigation: { icon: navigation_default },
    pages: { icon: page_default },
    templateParts: { icon: symbol_filled_default },
    patterns: { icon: symbol_default },
    templates: { icon: layout_default },
    fontList: { icon: typography_default }
  };
  Object.entries(menuIcons).forEach(([id, { icon }]) => {
    (0, import_data.dispatch)(bootStore).updateMenuItem(id, { icon });
  });
}
export {
  init
};