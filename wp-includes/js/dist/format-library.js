(function() {
"use strict";
var wp;
(wp ||= {}).formatLibrary = (() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __require = /* @__PURE__ */ ((x) => typeof require !== "undefined" ? require : typeof Proxy !== "undefined" ? new Proxy(x, {
    get: (a, b) => (typeof require !== "undefined" ? require : a)[b]
  }) : x)(function(x) {
    if (typeof require !== "undefined") return require.apply(this, arguments);
    throw Error('Dynamic require of "' + x + '" is not supported');
  });
  var __commonJS = (cb, mod) => function __require2() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __export = (target, all) => {
    for (var name16 in all)
      __defProp(target, name16, { get: all[name16], enumerable: true });
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

  // package-external:@wordpress/rich-text
  var require_rich_text = __commonJS({
    "package-external:@wordpress/rich-text"(exports, module) {
      module.exports = window.wp.richText;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/block-editor
  var require_block_editor = __commonJS({
    "package-external:@wordpress/block-editor"(exports, module) {
      module.exports = window.wp.blockEditor;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

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

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
    }
  });

  // vendor-external:react
  var require_react = __commonJS({
    "vendor-external:react"(exports, module) {
      module.exports = window.React;
    }
  });

  // vendor-external:react-dom
  var require_react_dom = __commonJS({
    "vendor-external:react-dom"(exports, module) {
      module.exports = window.ReactDOM;
    }
  });

  // node_modules/use-sync-external-store/cjs/use-sync-external-store-shim.development.js
  var require_use_sync_external_store_shim_development = __commonJS({
    "node_modules/use-sync-external-store/cjs/use-sync-external-store-shim.development.js"(exports) {
      "use strict";
      (function() {
        function is(x, y) {
          return x === y && (0 !== x || 1 / x === 1 / y) || x !== x && y !== y;
        }
        function useSyncExternalStore$2(subscribe2, getSnapshot2) {
          didWarnOld18Alpha || void 0 === React51.startTransition || (didWarnOld18Alpha = true, console.error(
            "You are using an outdated, pre-release alpha of React 18 that does not support useSyncExternalStore. The use-sync-external-store shim will not work correctly. Upgrade to a newer pre-release."
          ));
          var value = getSnapshot2();
          if (!didWarnUncachedGetSnapshot) {
            var cachedValue = getSnapshot2();
            objectIs(value, cachedValue) || (console.error(
              "The result of getSnapshot should be cached to avoid an infinite loop"
            ), didWarnUncachedGetSnapshot = true);
          }
          cachedValue = useState21({
            inst: { value, getSnapshot: getSnapshot2 }
          });
          var inst = cachedValue[0].inst, forceUpdate = cachedValue[1];
          useLayoutEffect3(
            function() {
              inst.value = value;
              inst.getSnapshot = getSnapshot2;
              checkIfSnapshotChanged(inst) && forceUpdate({ inst });
            },
            [subscribe2, value, getSnapshot2]
          );
          useEffect14(
            function() {
              checkIfSnapshotChanged(inst) && forceUpdate({ inst });
              return subscribe2(function() {
                checkIfSnapshotChanged(inst) && forceUpdate({ inst });
              });
            },
            [subscribe2]
          );
          useDebugValue(value);
          return value;
        }
        function checkIfSnapshotChanged(inst) {
          var latestGetSnapshot = inst.getSnapshot;
          inst = inst.value;
          try {
            var nextValue = latestGetSnapshot();
            return !objectIs(inst, nextValue);
          } catch (error2) {
            return true;
          }
        }
        function useSyncExternalStore$1(subscribe2, getSnapshot2) {
          return getSnapshot2();
        }
        "undefined" !== typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ && "function" === typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());
        var React51 = require_react(), objectIs = "function" === typeof Object.is ? Object.is : is, useState21 = React51.useState, useEffect14 = React51.useEffect, useLayoutEffect3 = React51.useLayoutEffect, useDebugValue = React51.useDebugValue, didWarnOld18Alpha = false, didWarnUncachedGetSnapshot = false, shim = "undefined" === typeof window || "undefined" === typeof window.document || "undefined" === typeof window.document.createElement ? useSyncExternalStore$1 : useSyncExternalStore$2;
        exports.useSyncExternalStore = void 0 !== React51.useSyncExternalStore ? React51.useSyncExternalStore : shim;
        "undefined" !== typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ && "function" === typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error());
      })();
    }
  });

  // node_modules/use-sync-external-store/shim/index.js
  var require_shim = __commonJS({
    "node_modules/use-sync-external-store/shim/index.js"(exports, module) {
      "use strict";
      if (false) {
        module.exports = null;
      } else {
        module.exports = require_use_sync_external_store_shim_development();
      }
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/a11y
  var require_a11y = __commonJS({
    "package-external:@wordpress/a11y"(exports, module) {
      module.exports = window.wp.a11y;
    }
  });

  // package-external:@wordpress/url
  var require_url = __commonJS({
    "package-external:@wordpress/url"(exports, module) {
      module.exports = window.wp.url;
    }
  });

  // package-external:@wordpress/html-entities
  var require_html_entities = __commonJS({
    "package-external:@wordpress/html-entities"(exports, module) {
      module.exports = window.wp.htmlEntities;
    }
  });

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // packages/format-library/build-module/index.mjs
  var import_rich_text18 = __toESM(require_rich_text(), 1);

  // packages/format-library/build-module/bold/index.mjs
  var import_i18n = __toESM(require_i18n(), 1);
  var import_rich_text = __toESM(require_rich_text(), 1);
  var import_block_editor = __toESM(require_block_editor(), 1);

  // packages/icons/build-module/icon/index.mjs
  var import_element = __toESM(require_element(), 1);
  var icon_default = (0, import_element.forwardRef)(
    ({ icon, size = 24, style, ...props }, ref) => {
      const intrinsicStyle = icon.props.style;
      const mergedStyle = intrinsicStyle || style ? { ...intrinsicStyle, ...style } : void 0;
      return (0, import_element.cloneElement)(icon, {
        width: size,
        height: size,
        ...props,
        // Merge styles so the icon's intrinsic style (e.g. `fill: none` on
        // stroke-based icons) is preserved unless the consumer overrides
        // the same property explicitly.
        ...mergedStyle ? { style: mergedStyle } : {},
        ref
      });
    }
  );

  // packages/icons/build-module/library/button.mjs
  var import_primitives = __toESM(require_primitives(), 1);
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
  var button_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M8 11.75H16M5 16.25H19C19.6904 16.25 20.25 15.6904 20.25 15V8.5C20.25 7.80964 19.6904 7.25 19 7.25H5C4.30964 7.25 3.75 7.80964 3.75 8.5V15C3.75 15.6904 4.30964 16.25 5 16.25Z", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/chevron-right.mjs
  var import_primitives2 = __toESM(require_primitives(), 1);
  var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
  var chevron_right_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M10 6.5L15 12L10 17.5", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/code.mjs
  var import_primitives3 = __toESM(require_primitives(), 1);
  var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
  var code_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M7.99997 7L3.70708 11.2929C3.31655 11.6834 3.31655 12.3166 3.70708 12.7071L7.99997 17M16 17L20.2929 12.7071C20.6834 12.3166 20.6834 11.6834 20.2929 11.2929L16 7", strokeLinejoin: "round", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/color.mjs
  var import_primitives4 = __toESM(require_primitives(), 1);
  var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
  var color_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.Path, { d: "M17.25 14C17.25 16.8995 14.8995 19.25 12 19.25C9.10051 19.25 6.75 16.8995 6.75 14C6.75 11.1005 10 7.37 12 5.12C14 7.37 17.25 11.1005 17.25 14Z", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/error.mjs
  var import_primitives5 = __toESM(require_primitives(), 1);
  var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
  var error_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.Path, { d: "M12 9L12 14M12 15.5V17M11.1284 5.04947L3.83827 18.0097C3.46331 18.6763 3.94502 19.5 4.70985 19.5H19.2902C20.055 19.5 20.5367 18.6763 20.1617 18.0097L12.8716 5.04947C12.4893 4.36982 11.5107 4.36982 11.1284 5.04947Z", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/format-bold.mjs
  var import_primitives6 = __toESM(require_primitives(), 1);
  var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
  var format_bold_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.Path, { d: "M13 11.5H7.75M13 11.5L13.7412 11.6059C15.3242 11.832 16.5 13.1877 16.5 14.7868C16.5 16.5614 15.0614 18 13.2868 18H7.75C7.75 18 7.75 14.9338 7.75 11.5M13 11.5L13.2913 11.4417C14.5756 11.1849 15.5 10.0573 15.5 8.74755C15.5 7.23012 14.2699 6 12.7525 6H7.75C7.75 7.65199 7.75 9.63049 7.75 11.5", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/format-italic.mjs
  var import_primitives7 = __toESM(require_primitives(), 1);
  var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
  var format_italic_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.Path, { d: "M11 19L13.5 5", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/format-strikethrough.mjs
  var import_primitives8 = __toESM(require_primitives(), 1);
  var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
  var format_strikethrough_default = /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.Path, { d: "M9 16.1554C12 18.02 15 16.5283 15 14.2908C15 13.6314 14.6839 12.999 14.1213 12.5328C14.108 12.5217 14.0945 12.5108 14.081 12.5M9 9.31845C9 7.49773 11.4313 6.3247 13.9199 7.4278M5.5 12.5H14.081M18.5 12.5H14.081", strokeLinecap: "square", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/help.mjs
  var import_primitives9 = __toESM(require_primitives(), 1);
  var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
  var help_default = /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(import_primitives9.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives9.Path, { d: "M9.75 10.25C9.75 9.00736 10.7574 8 12 8C13.2426 8 14.25 9.00736 14.25 10.25C14.25 11.4083 13.3748 12.3621 12.2496 12.4863C12.1124 12.5015 12 12.6119 12 12.75V14M12 15V16.5", vectorEffect: "non-scaling-stroke" }),
    /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives9.Path, { d: "M19.25 12C19.25 16.0041 16.0041 19.25 12 19.25C7.99594 19.25 4.75 16.0041 4.75 12C4.75 7.99594 7.99594 4.75 12 4.75C16.0041 4.75 19.25 7.99594 19.25 12Z", strokeLinejoin: "round", vectorEffect: "non-scaling-stroke" })
  ] });

  // packages/icons/build-module/library/inline-image.mjs
  var import_primitives10 = __toESM(require_primitives(), 1);
  var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
  var inline_image_default = /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives10.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives10.Path, { d: "M13.25 11.3197L11.4155 9.48453C11.3765 9.44547 11.3132 9.44546 11.2741 9.48452L9.05837 11.7003C9.02448 11.7341 8.97132 11.7392 8.93163 11.7124L7.0648 10.4495C7.03003 10.426 6.98426 10.4267 6.95022 10.4513L4.75 12.0405M13.25 11.3197V7C13.25 6.30964 12.6904 5.75 12 5.75H6C5.30964 5.75 4.75 6.30965 4.75 7V12.0405M13.25 11.3197V13C13.25 13.6904 12.6904 14.25 12 14.25H6C5.30964 14.25 4.75 13.6904 4.75 13V12.0405M16 14H20M4 18H20", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/language.mjs
  var import_primitives11 = __toESM(require_primitives(), 1);
  var import_jsx_runtime11 = __toESM(require_jsx_runtime(), 1);
  var language_default = /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(import_primitives11.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives11.Path, { d: "M3 6H9.5M16 6H13.5M9.5 6V3M9.5 6H13.5M13.5 6C13.0477 7.24386 11.7361 9.79194 9.44037 12.5512M5.5 16.5C7.04336 15.2139 8.35238 13.8588 9.44037 12.5512M6.5 8C6.9232 8.9522 7.93743 10.7507 9.44037 12.5512M12.5 15.5C11.3314 14.6235 10.305 13.587 9.44037 12.5512", vectorEffect: "non-scaling-stroke" }),
    /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives11.Path, { d: "M13.75 20V19.75L14.6667 17M14.6667 17L16.75 10.75H17.25L19.3333 17L20.25 19.75V20M19.3333 17H14.6667", vectorEffect: "non-scaling-stroke" })
  ] });

  // packages/icons/build-module/library/link.mjs
  var import_primitives12 = __toESM(require_primitives(), 1);
  var import_jsx_runtime12 = __toESM(require_jsx_runtime(), 1);
  var link_default = /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives12.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives12.Path, { d: "M9.5 12L14.5 12M10 7.75H8.25C5.90279 7.75 4 9.65279 4 12C4 14.3472 5.90279 16.25 8.25 16.25H10M14 16.25H15.75C18.0972 16.25 20 14.3472 20 12C20 9.65279 18.0972 7.75 15.75 7.75L14 7.75", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/math.mjs
  var import_primitives13 = __toESM(require_primitives(), 1);
  var import_jsx_runtime13 = __toESM(require_jsx_runtime(), 1);
  var math_default = /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives13.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives13.Path, { d: "M3 14H5L6.8916 16.3645C6.93974 16.4247 7.03496 16.4101 7.06289 16.3383L10.2521 8.13755C10.4015 7.75318 10.7717 7.5 11.1841 7.5H19M17 11L14.5 13.5M12 16L14.5 13.5M14.5 13.5L17 16M14.5 13.5L12 11", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/published.mjs
  var import_primitives14 = __toESM(require_primitives(), 1);
  var import_jsx_runtime14 = __toESM(require_jsx_runtime(), 1);
  var published_default = /* @__PURE__ */ (0, import_jsx_runtime14.jsxs)(import_primitives14.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(import_primitives14.Path, { d: "M12 19.25C16.0041 19.25 19.25 16.0041 19.25 12C19.25 7.99594 16.0041 4.75 12 4.75C7.99594 4.75 4.75 7.99594 4.75 12C4.75 16.0041 7.99594 19.25 12 19.25Z", strokeLinejoin: "round", vectorEffect: "non-scaling-stroke" }),
    /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(import_primitives14.Path, { d: "M9 12L11 14L15 10", vectorEffect: "non-scaling-stroke" })
  ] });

  // packages/icons/build-module/library/subscript.mjs
  var import_primitives15 = __toESM(require_primitives(), 1);
  var import_jsx_runtime15 = __toESM(require_jsx_runtime(), 1);
  var subscript_default = /* @__PURE__ */ (0, import_jsx_runtime15.jsxs)(import_primitives15.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_primitives15.Path, { d: "M17.75 18.5988H15.75C15.75 16.5322 17.75 17.0489 17.75 15.4989C17.75 14.7239 16.75 14.0738 15.75 14.8488", strokeLinecap: "square", vectorEffect: "non-scaling-stroke" }),
    /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_primitives15.Path, { d: "M10 12L13 7.4V7M10 12L7 7.4L7.00004 7M10 12L7 16.6V17M10 12L13 16.6V17", vectorEffect: "non-scaling-stroke" })
  ] });

  // packages/icons/build-module/library/superscript.mjs
  var import_primitives16 = __toESM(require_primitives(), 1);
  var import_jsx_runtime16 = __toESM(require_jsx_runtime(), 1);
  var superscript_default = /* @__PURE__ */ (0, import_jsx_runtime16.jsxs)(import_primitives16.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_primitives16.Path, { d: "M17.75 8.59884H15.75C15.75 6.53219 17.75 7.04885 17.75 5.49887C17.75 4.72388 16.75 4.07384 15.75 4.84884", strokeLinecap: "square", vectorEffect: "non-scaling-stroke" }),
    /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_primitives16.Path, { d: "M10 12L13 7.4V7M10 12L7 7.4L7.00004 7M10 12L7 16.6V17M10 12L13 16.6V17", vectorEffect: "non-scaling-stroke" })
  ] });

  // packages/icons/build-module/library/text-color.mjs
  var import_primitives17 = __toESM(require_primitives(), 1);
  var import_jsx_runtime17 = __toESM(require_jsx_runtime(), 1);
  var text_color_default = /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_primitives17.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_primitives17.Path, { d: "M8 17.5V17.223L9.01577 14.1757M9.01577 14.1757L11.3243 7.25H11.8784L14.1869 14.1757L15.2027 17.223V17.5M14.1869 14.1757H9.01577", vectorEffect: "non-scaling-stroke" }) });

  // packages/format-library/build-module/bold/index.mjs
  var import_jsx_runtime18 = __toESM(require_jsx_runtime(), 1);
  var name = "core/bold";
  var title = (0, import_i18n.__)("Bold");
  var bold = {
    name,
    title,
    tagName: "strong",
    className: null,
    edit({
      isActive,
      value,
      onChange,
      onFocus,
      isVisible = true
    }) {
      function onToggle() {
        onChange((0, import_rich_text.toggleFormat)(value, { type: name, title }));
      }
      function onClick() {
        onChange((0, import_rich_text.toggleFormat)(value, { type: name, title }));
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime18.jsxs)(import_jsx_runtime18.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
          import_block_editor.RichTextShortcut,
          {
            type: "primary",
            character: "b",
            onUse: onToggle
          }
        ),
        isVisible && /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
          import_block_editor.RichTextToolbarButton,
          {
            name: "bold",
            icon: format_bold_default,
            title,
            onClick,
            isActive,
            shortcutType: "primary",
            shortcutCharacter: "b"
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
          import_block_editor.__unstableRichTextInputEvent,
          {
            inputType: "formatBold",
            onInput: onToggle
          }
        )
      ] });
    }
  };

  // packages/format-library/build-module/code/index.mjs
  var import_i18n2 = __toESM(require_i18n(), 1);
  var import_rich_text2 = __toESM(require_rich_text(), 1);
  var import_block_editor2 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime19 = __toESM(require_jsx_runtime(), 1);
  var name2 = "core/code";
  var title2 = (0, import_i18n2.__)("Inline code");
  var code = {
    name: name2,
    title: title2,
    tagName: "code",
    className: null,
    __unstableInputRule(value) {
      const BACKTICK = "`";
      const { start, text } = value;
      const characterBefore = text[start - 1];
      if (characterBefore !== BACKTICK) {
        return value;
      }
      if (start - 2 < 0) {
        return value;
      }
      const indexBefore = text.lastIndexOf(BACKTICK, start - 2);
      if (indexBefore === -1) {
        return value;
      }
      const startIndex = indexBefore;
      const endIndex = start - 2;
      if (startIndex === endIndex) {
        return value;
      }
      value = (0, import_rich_text2.remove)(value, startIndex, startIndex + 1);
      value = (0, import_rich_text2.remove)(value, endIndex, endIndex + 1);
      value = (0, import_rich_text2.applyFormat)(value, { type: name2 }, startIndex, endIndex);
      return value;
    },
    edit({
      value,
      onChange,
      onFocus,
      isActive
    }) {
      function onClick() {
        onChange((0, import_rich_text2.toggleFormat)(value, { type: name2, title: title2 }));
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime19.jsxs)(import_jsx_runtime19.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
          import_block_editor2.RichTextShortcut,
          {
            type: "access",
            character: "x",
            onUse: onClick
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
          import_block_editor2.RichTextToolbarButton,
          {
            icon: code_default,
            title: title2,
            onClick,
            isActive,
            role: "menuitemcheckbox"
          }
        )
      ] });
    }
  };

  // packages/format-library/build-module/image/index.mjs
  var import_components = __toESM(require_components(), 1);

  // node_modules/clsx/dist/clsx.mjs
  function r(e) {
    var t, f, n = "";
    if ("string" == typeof e || "number" == typeof e) n += e;
    else if ("object" == typeof e) if (Array.isArray(e)) {
      var o = e.length;
      for (t = 0; t < o; t++) e[t] && (f = r(e[t])) && (n && (n += " "), n += f);
    } else for (f in e) e[f] && (n && (n += " "), n += f);
    return n;
  }
  function clsx() {
    for (var e, t, f = 0, n = "", o = arguments.length; f < o; f++) (e = arguments[f]) && (t = r(e)) && (n && (n += " "), n += t);
    return n;
  }
  var clsx_default = clsx;

  // node_modules/@base-ui/utils/useControlled.mjs
  var React = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/createLogOnce.mjs
  var loggedMessages;
  if (true) {
    loggedMessages = /* @__PURE__ */ new Set();
  }
  function createLogOnce(severity, prefix) {
    return function logOnce(...messages) {
      if (true) {
        const message = messages.join(" ");
        const output = prefix ? `${prefix}: ${message}` : message;
        const key = `${severity}:${output}`;
        if (!loggedMessages.has(key)) {
          loggedMessages.add(key);
          if (severity === "warn") {
            console.warn(output);
          } else {
            console.error(output);
          }
        }
      }
    };
  }

  // node_modules/@base-ui/utils/error.mjs
  var error = createLogOnce("error", "Base UI");

  // node_modules/@base-ui/utils/useControlled.mjs
  function useControlled({
    controlled,
    default: defaultProp,
    name: name16,
    state = "value"
  }) {
    const {
      current: isControlled
    } = React.useRef(controlled !== void 0);
    const [valueState, setValue] = React.useState(defaultProp);
    const value = isControlled && controlled !== void 0 ? controlled : valueState;
    if (true) {
      React.useEffect(() => {
        if (isControlled !== (controlled !== void 0)) {
          error([`A component is changing the ${isControlled ? "" : "un"}controlled ${state} state of ${name16} to be ${isControlled ? "un" : ""}controlled.`, "Elements should not switch from uncontrolled to controlled (or vice versa).", `Decide between using a controlled or uncontrolled ${name16} element for the lifetime of the component.`, "The nature of the state is determined during the first render. It's considered controlled if the value is not `undefined`.", "More info: https://fb.me/react-controlled-components"].join("\n"));
        }
      }, [state, name16, controlled]);
      const {
        current: defaultValue
      } = React.useRef(defaultProp);
      React.useEffect(() => {
        if (!isControlled && serializeToDevModeString(defaultValue) !== serializeToDevModeString(defaultProp)) {
          error([`A component is changing the default ${state} state of an uncontrolled ${name16} after being initialized. To suppress this warning opt to use a controlled ${name16}.`].join("\n"));
        }
      }, [defaultProp]);
    }
    const setValueIfUncontrolled = React.useCallback((newValue) => {
      if (!isControlled) {
        setValue(newValue);
      }
    }, []);
    return [value, setValueIfUncontrolled];
  }
  function serializeToDevModeString(input) {
    let nextId = 0;
    const seen = /* @__PURE__ */ new WeakMap();
    try {
      const result = JSON.stringify(input, function replacer(key, value) {
        if (key === "_owner" && this != null && typeof this === "object" && "$$typeof" in this) {
          return void 0;
        }
        if (typeof value === "bigint") {
          return `__bigint__:${value}`;
        }
        if (value !== null && typeof value === "object") {
          const id = seen.get(value);
          if (id !== void 0) {
            return `__object__:${id}`;
          }
          seen.set(value, nextId);
          nextId += 1;
        }
        return value;
      });
      return result ?? `__top__:${typeof input}`;
    } catch {
      return "__unserializable__";
    }
  }

  // node_modules/@base-ui/utils/safeReact.mjs
  var React2 = __toESM(require_react(), 1);
  var SafeReact = {
    ...React2
  };

  // node_modules/@base-ui/utils/useRefWithInit.mjs
  var React3 = __toESM(require_react(), 1);
  var UNINITIALIZED = {};
  function useRefWithInit(init, initArg) {
    const ref = React3.useRef(UNINITIALIZED);
    if (ref.current === UNINITIALIZED) {
      ref.current = init(initArg);
    }
    return ref;
  }

  // node_modules/@base-ui/utils/useStableCallback.mjs
  var useInsertionEffect = SafeReact.useInsertionEffect;
  var useSafeInsertionEffect = (
    // React 17 doesn't have useInsertionEffect.
    useInsertionEffect && // Preact replaces useInsertionEffect with useLayoutEffect and fires too late.
    useInsertionEffect !== SafeReact.useLayoutEffect ? useInsertionEffect : (fn) => fn()
  );
  function useStableCallback(callback) {
    const stable = useRefWithInit(createStableCallback).current;
    stable.next = callback;
    useSafeInsertionEffect(stable.effect);
    return stable.trampoline;
  }
  function createStableCallback() {
    const stable = {
      next: void 0,
      callback: assertNotCalled,
      trampoline: (...args) => stable.callback?.(...args),
      effect: () => {
        stable.callback = stable.next;
      }
    };
    return stable;
  }
  function assertNotCalled() {
    if (true) {
      throw (
        /* minify-error-disabled */
        new Error("Base UI: Cannot call an event handler while rendering.")
      );
    }
  }

  // node_modules/@base-ui/utils/warn.mjs
  var warn = createLogOnce("warn", "Base UI");

  // node_modules/@base-ui/utils/empty.mjs
  function NOOP() {
  }
  var EMPTY_ARRAY = Object.freeze([]);
  var EMPTY_OBJECT = Object.freeze({});

  // node_modules/@base-ui/react/internals/composite/list/CompositeList.mjs
  var React6 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/useIsoLayoutEffect.mjs
  var React4 = __toESM(require_react(), 1);
  var noop = () => {
  };
  var useIsoLayoutEffect = typeof document !== "undefined" ? React4.useLayoutEffect : noop;

  // node_modules/@base-ui/react/internals/composite/list/CompositeListContext.mjs
  var React5 = __toESM(require_react(), 1);
  var CompositeListContext = /* @__PURE__ */ React5.createContext({
    register: () => {
    },
    unregister: () => {
    },
    subscribeMapChange: () => () => {
    },
    nextIndexRef: {
      current: 0
    }
  });
  if (true) CompositeListContext.displayName = "CompositeListContext";
  function useCompositeListContext() {
    return React5.useContext(CompositeListContext);
  }

  // node_modules/@base-ui/react/internals/composite/list/CompositeList.mjs
  var import_jsx_runtime20 = __toESM(require_jsx_runtime(), 1);
  function CompositeList(props) {
    const {
      children,
      elementsRef,
      labelsRef,
      onMapChange: onMapChangeProp
    } = props;
    const onMapChange = useStableCallback(onMapChangeProp);
    const [, setMapTick] = React6.useState(false);
    const listeners = useRefWithInit(createListeners).current;
    const map = useRefWithInit(createMap).current;
    const nextIndexRef = React6.useRef(0);
    const isDirtyRef = React6.useRef(true);
    const itemsRef = React6.useRef(null);
    const mutationObserverRef = React6.useRef(null);
    const scheduleMapUpdate = useStableCallback(() => {
      if (isDirtyRef.current) {
        return;
      }
      isDirtyRef.current = true;
      setMapTick((tick) => !tick);
    });
    const register = useStableCallback((node, registration) => {
      map.set(node, registration);
      scheduleMapUpdate();
    });
    const unregister = useStableCallback((node) => {
      map.delete(node);
      scheduleMapUpdate();
    });
    const syncRefs = useStableCallback((items) => {
      const nextMap = /* @__PURE__ */ new Map();
      elementsRef.current.length = 0;
      if (labelsRef) {
        labelsRef.current.length = 0;
      }
      items.forEach((item) => {
        nextMap.set(item.element, {
          ...item.registration.metadata ?? {},
          index: item.index
        });
        elementsRef.current[item.index] = item.element;
        if (labelsRef) {
          labelsRef.current[item.index] = item.registration.label !== void 0 ? item.registration.label : item.registration.textRef?.current?.textContent ?? item.element.textContent;
        }
      });
      nextIndexRef.current = elementsRef.current.length;
      return nextMap;
    });
    function observe(sortedNodes) {
      mutationObserverRef.current?.disconnect();
      mutationObserverRef.current = null;
      if (typeof MutationObserver !== "function" || sortedNodes.length < 2) {
        return;
      }
      const mutationObserver = new MutationObserver((entries) => {
        if (!hasMovedNode(entries)) {
          return;
        }
        let previousConnectedNode = null;
        for (const node of sortedNodes) {
          if (!node.isConnected) {
            continue;
          }
          if (previousConnectedNode && sortByDocumentPosition(previousConnectedNode, node) > 0) {
            mutationObserver.disconnect();
            scheduleMapUpdate();
            return;
          }
          previousConnectedNode = node;
        }
      });
      mutationObserverRef.current = mutationObserver;
      const roots = /* @__PURE__ */ new Set();
      for (let i = 1; i < sortedNodes.length; i += 1) {
        const root = getCommonAncestor(sortedNodes[i - 1], sortedNodes[i]);
        if (root) {
          roots.add(root);
        }
      }
      roots.forEach((root) => mutationObserver.observe(root, {
        childList: true
      }));
    }
    const flush = useStableCallback(() => {
      const [items, automaticNodes] = getCompositeListSnapshot(map);
      const nextMap = syncRefs(items);
      const previousItems = itemsRef.current;
      const changed = !previousItems || previousItems.length !== items.length || items.some((item, index2) => {
        const previousItem = previousItems[index2];
        return item.index !== previousItem.index || item.element !== previousItem.element || item.registration.index !== previousItem.registration.index || item.registration.metadata !== previousItem.registration.metadata;
      });
      observe(automaticNodes);
      itemsRef.current = items;
      isDirtyRef.current = false;
      if (!changed) {
        return;
      }
      listeners.forEach((listener) => listener(nextMap));
      onMapChange(nextMap);
    });
    useIsoLayoutEffect(() => {
      if (!isDirtyRef.current && itemsRef.current) {
        syncRefs(itemsRef.current);
      }
      return () => {
        elementsRef.current = [];
        if (labelsRef) {
          labelsRef.current = [];
        }
      };
    }, [elementsRef, labelsRef, syncRefs]);
    useIsoLayoutEffect(() => {
      if (isDirtyRef.current) {
        flush();
      }
    });
    useIsoLayoutEffect(() => {
      return () => {
        mutationObserverRef.current?.disconnect();
        isDirtyRef.current = true;
      };
    }, []);
    const subscribeMapChange = useStableCallback((fn) => {
      listeners.add(fn);
      return () => {
        listeners.delete(fn);
      };
    });
    const contextValue = React6.useMemo(() => ({
      register,
      unregister,
      subscribeMapChange,
      nextIndexRef
    }), [register, unregister, subscribeMapChange, nextIndexRef]);
    return /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(CompositeListContext.Provider, {
      value: contextValue,
      children
    });
  }
  function createMap() {
    return /* @__PURE__ */ new Map();
  }
  function createListeners() {
    return /* @__PURE__ */ new Set();
  }
  function getCompositeListSnapshot(map) {
    const reservedIndices = /* @__PURE__ */ new Set();
    const items = [];
    const automaticItems = [];
    map.forEach((registration, node) => {
      if (!node.isConnected) {
        return;
      }
      const index2 = registration.index;
      const item = {
        index: index2 ?? -1,
        element: node,
        registration
      };
      if (index2 === null) {
        automaticItems.push(item);
      } else if (index2 >= 0) {
        reservedIndices.add(index2);
        items.push(item);
      }
    });
    let nextAutomaticIndex = 0;
    automaticItems.sort((a, b) => sortByDocumentPosition(a.element, b.element));
    automaticItems.forEach((item) => {
      while (reservedIndices.has(nextAutomaticIndex)) {
        nextAutomaticIndex += 1;
      }
      item.index = nextAutomaticIndex;
      items.push(item);
      nextAutomaticIndex += 1;
    });
    if (reservedIndices.size > 0) {
      items.sort((a, b) => a.index - b.index);
    }
    return [items, automaticItems.map((item) => item.element)];
  }
  function getCommonAncestor(firstNode, lastNode) {
    let ancestor = firstNode.parentElement;
    while (ancestor && !ancestor.contains(lastNode)) {
      ancestor = ancestor.parentElement;
    }
    return ancestor;
  }
  function hasMovedNode(entries) {
    for (const entry of entries) {
      for (let i = 0; i < entry.removedNodes.length; i += 1) {
        if (entry.removedNodes[i].isConnected) {
          return true;
        }
      }
    }
    return false;
  }
  function sortByDocumentPosition(a, b) {
    return a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1;
  }

  // node_modules/@base-ui/react/internals/useRenderElement.mjs
  var React9 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/useMergedRefs.mjs
  function useMergedRefs(a, b, c, d) {
    const forkRef = useRefWithInit(createForkRef).current;
    if (didChange(forkRef, a, b, c, d)) {
      update(forkRef, [a, b, c, d]);
    }
    return forkRef.callback;
  }
  function useMergedRefsN(refs) {
    const forkRef = useRefWithInit(createForkRef).current;
    if (didChangeN(forkRef, refs)) {
      update(forkRef, refs);
    }
    return forkRef.callback;
  }
  function createForkRef() {
    return {
      callback: null,
      cleanup: null,
      refs: []
    };
  }
  function didChange(forkRef, a, b, c, d) {
    return forkRef.refs[0] !== a || forkRef.refs[1] !== b || forkRef.refs[2] !== c || forkRef.refs[3] !== d;
  }
  function didChangeN(forkRef, newRefs) {
    return forkRef.refs.length !== newRefs.length || forkRef.refs.some((ref, index2) => ref !== newRefs[index2]);
  }
  function update(forkRef, refs) {
    forkRef.refs = refs;
    if (refs.every((ref) => ref == null)) {
      forkRef.callback = null;
      return;
    }
    forkRef.callback = (instance) => {
      if (forkRef.cleanup) {
        forkRef.cleanup();
        forkRef.cleanup = null;
      }
      if (instance != null) {
        const cleanupCallbacks = Array(refs.length).fill(null);
        for (let i = 0; i < refs.length; i += 1) {
          const ref = refs[i];
          if (ref == null) {
            continue;
          }
          switch (typeof ref) {
            case "function": {
              const refCleanup = ref(instance);
              if (typeof refCleanup === "function") {
                cleanupCallbacks[i] = refCleanup;
              }
              break;
            }
            case "object": {
              ref.current = instance;
              break;
            }
            default:
          }
        }
        forkRef.cleanup = () => {
          for (let i = 0; i < refs.length; i += 1) {
            const ref = refs[i];
            if (ref == null) {
              continue;
            }
            switch (typeof ref) {
              case "function": {
                const cleanupCallback = cleanupCallbacks[i];
                if (typeof cleanupCallback === "function") {
                  cleanupCallback();
                } else {
                  void ref(null);
                }
                break;
              }
              case "object": {
                ref.current = null;
                break;
              }
              default:
            }
          }
        };
      }
    };
  }

  // node_modules/@base-ui/utils/getReactElementRef.mjs
  var React8 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/reactVersion.mjs
  var React7 = __toESM(require_react(), 1);
  var majorVersion = parseInt(React7.version, 10);
  function isReactVersionAtLeast(reactVersionToCheck) {
    return majorVersion >= reactVersionToCheck;
  }

  // node_modules/@base-ui/utils/getReactElementRef.mjs
  function getReactElementRef(element) {
    if (!/* @__PURE__ */ React8.isValidElement(element)) {
      return null;
    }
    const reactElement = element;
    const propsWithRef = reactElement.props;
    return (isReactVersionAtLeast(19) ? propsWithRef?.ref : reactElement.ref) ?? null;
  }

  // node_modules/@base-ui/utils/mergeObjects.mjs
  function mergeObjects(a, b) {
    if (a && !b) {
      return a;
    }
    if (!a && b) {
      return b;
    }
    if (a || b) {
      return {
        ...a,
        ...b
      };
    }
    return void 0;
  }

  // node_modules/@base-ui/react/internals/getStateAttributesProps.mjs
  function getStateAttributesProps(state, customMapping) {
    const props = {};
    for (const key in state) {
      const value = state[key];
      if (customMapping?.hasOwnProperty(key)) {
        const customProps = customMapping[key](value);
        if (customProps != null) {
          Object.assign(props, customProps);
        }
        continue;
      }
      if (value === true) {
        props[`data-${key.toLowerCase()}`] = "";
      } else if (value) {
        props[`data-${key.toLowerCase()}`] = value.toString();
      }
    }
    return props;
  }

  // node_modules/@base-ui/react/utils/resolveClassName.mjs
  function resolveClassName(className, state) {
    return typeof className === "function" ? className(state) : className;
  }

  // node_modules/@base-ui/react/utils/resolveStyle.mjs
  function resolveStyle(style, state) {
    return typeof style === "function" ? style(state) : style;
  }

  // node_modules/@base-ui/react/merge-props/mergeProps.mjs
  var EMPTY_PROPS = {};
  function mergeProps(a, b, c, d, e) {
    if (!c && !d && !e && !a) {
      return createInitialMergedProps(b);
    }
    let merged = createInitialMergedProps(a);
    if (b) {
      merged = mergeInto(merged, b);
    }
    if (c) {
      merged = mergeInto(merged, c);
    }
    if (d) {
      merged = mergeInto(merged, d);
    }
    if (e) {
      merged = mergeInto(merged, e);
    }
    return merged;
  }
  function mergePropsN(props) {
    if (props.length === 0) {
      return EMPTY_PROPS;
    }
    if (props.length === 1) {
      return createInitialMergedProps(props[0]);
    }
    let merged = createInitialMergedProps(props[0]);
    for (let i = 1; i < props.length; i += 1) {
      merged = mergeInto(merged, props[i]);
    }
    return merged;
  }
  function createInitialMergedProps(inputProps) {
    if (isPropsGetter(inputProps)) {
      return {
        ...resolvePropsGetter(inputProps, EMPTY_PROPS)
      };
    }
    return copyInitialProps(inputProps);
  }
  function mergeInto(merged, inputProps) {
    if (isPropsGetter(inputProps)) {
      return resolvePropsGetter(inputProps, merged);
    }
    return mutablyMergeInto(merged, inputProps);
  }
  function copyInitialProps(inputProps) {
    const copiedProps = {
      ...inputProps
    };
    for (const propName in copiedProps) {
      const propValue = copiedProps[propName];
      if (isEventHandler(propName, propValue)) {
        copiedProps[propName] = wrapEventHandler(propValue);
      }
    }
    return copiedProps;
  }
  function mutablyMergeInto(mergedProps, externalProps) {
    if (!externalProps) {
      return mergedProps;
    }
    for (const propName in externalProps) {
      const externalPropValue = externalProps[propName];
      switch (propName) {
        case "style": {
          mergedProps[propName] = mergeObjects(mergedProps.style, externalPropValue);
          break;
        }
        case "className": {
          mergedProps[propName] = mergeClassNames(mergedProps.className, externalPropValue);
          break;
        }
        default: {
          if (isEventHandler(propName, externalPropValue)) {
            mergedProps[propName] = mergeEventHandlers(mergedProps[propName], externalPropValue);
          } else {
            mergedProps[propName] = externalPropValue;
          }
        }
      }
    }
    return mergedProps;
  }
  function isEventHandler(key, value) {
    const code0 = key.charCodeAt(0);
    const code1 = key.charCodeAt(1);
    const code2 = key.charCodeAt(2);
    return code0 === 111 && code1 === 110 && code2 >= 65 && code2 <= 90 && (typeof value === "function" || typeof value === "undefined");
  }
  function isPropsGetter(inputProps) {
    return typeof inputProps === "function";
  }
  function resolvePropsGetter(inputProps, previousProps) {
    if (isPropsGetter(inputProps)) {
      return inputProps(previousProps);
    }
    return inputProps ?? EMPTY_PROPS;
  }
  function mergeEventHandlers(ourHandler, theirHandler) {
    if (!theirHandler) {
      return ourHandler;
    }
    if (!ourHandler) {
      return wrapEventHandler(theirHandler);
    }
    return (...args) => {
      const event = args[0];
      if (isSyntheticEvent(event)) {
        const baseUIEvent = event;
        makeEventPreventable(baseUIEvent);
        const result2 = theirHandler(...args);
        if (!baseUIEvent.baseUIHandlerPrevented) {
          ourHandler?.(...args);
        }
        return result2;
      }
      const result = theirHandler(...args);
      ourHandler?.(...args);
      return result;
    };
  }
  function wrapEventHandler(handler) {
    if (!handler) {
      return handler;
    }
    return (...args) => {
      const event = args[0];
      if (isSyntheticEvent(event)) {
        makeEventPreventable(event);
      }
      return handler(...args);
    };
  }
  function makeEventPreventable(event) {
    event.preventBaseUIHandler = () => {
      event.baseUIHandlerPrevented = true;
    };
    return event;
  }
  function mergeClassNames(ourClassName, theirClassName) {
    if (theirClassName) {
      if (ourClassName) {
        return theirClassName + " " + ourClassName;
      }
      return theirClassName;
    }
    return ourClassName;
  }
  function isSyntheticEvent(event) {
    return event != null && typeof event === "object" && "nativeEvent" in event;
  }

  // node_modules/@base-ui/react/internals/useRenderElement.mjs
  var import_react = __toESM(require_react(), 1);
  function useRenderElement(element, componentProps, params = {}) {
    let renderProp = componentProps.render;
    if (params.enabled !== false) {
      renderProp = unwrapLazyRenderProp(renderProp);
    }
    const outProps = useRenderElementProps(componentProps, params, renderProp);
    if (params.enabled === false) {
      return null;
    }
    const state = params.state ?? EMPTY_OBJECT;
    return evaluateRenderProp(element, renderProp, outProps, state);
  }
  function useRenderElementProps(componentProps, params, renderProp) {
    const {
      className: classNameProp,
      style: styleProp
    } = componentProps;
    const {
      state = EMPTY_OBJECT,
      ref,
      props,
      stateAttributesMapping: stateAttributesMapping4,
      enabled = true
    } = params;
    const className = enabled ? resolveClassName(classNameProp, state) : void 0;
    const style = enabled ? resolveStyle(styleProp, state) : void 0;
    const stateProps = enabled ? getStateAttributesProps(state, stateAttributesMapping4) : EMPTY_OBJECT;
    const resolvedProps = enabled && props ? resolveRenderFunctionProps(props) : void 0;
    const outProps = enabled ? mergeObjects(stateProps, resolvedProps) ?? {} : EMPTY_OBJECT;
    if (typeof document !== "undefined") {
      if (!enabled) {
        void useMergedRefs(null, null);
      } else if (Array.isArray(ref)) {
        outProps.ref = useMergedRefsN([outProps.ref, getReactElementRef(renderProp), ...ref]);
      } else {
        outProps.ref = useMergedRefs(outProps.ref, getReactElementRef(renderProp), ref);
      }
    }
    if (!enabled) {
      return EMPTY_OBJECT;
    }
    if (className !== void 0) {
      outProps.className = mergeClassNames(outProps.className, className);
    }
    if (style !== void 0) {
      outProps.style = mergeObjects(outProps.style, style);
    }
    return outProps;
  }
  function resolveRenderFunctionProps(props) {
    if (Array.isArray(props)) {
      return mergePropsN(props);
    }
    return mergeProps(void 0, props);
  }
  var REACT_LAZY_TYPE = /* @__PURE__ */ Symbol.for("react.lazy");
  var COMPONENT_IDENTIFIER_PATTERN = /^[A-Z][A-Za-z0-9$]*$/;
  var LOWERCASE_CHARACTER_PATTERN = /[a-z]/;
  function unwrapLazyRenderProp(render) {
    if (render?.$$typeof !== REACT_LAZY_TYPE) {
      return render;
    }
    const unwrapped = React9.Children.toArray(render)[0];
    return /* @__PURE__ */ React9.isValidElement(unwrapped) ? unwrapped : render;
  }
  function evaluateRenderProp(element, render, props, state) {
    if (render) {
      if (typeof render === "function") {
        if (true) {
          warnIfRenderPropLooksLikeComponent(render);
        }
        return render(props, state);
      }
      const mergedProps = mergeProps(props, render.props);
      mergedProps.ref = props.ref;
      if (true) {
        if (!/* @__PURE__ */ React9.isValidElement(render)) {
          throw new Error(["Base UI: The `render` prop was provided an invalid React element as `React.isValidElement(render)` is `false`.", "A valid React element must be provided to the `render` prop because it is cloned with props to replace the default element.", "https://base-ui.com/r/invalid-render-prop"].join("\n"));
        }
      }
      return /* @__PURE__ */ React9.cloneElement(render, mergedProps);
    }
    if (element) {
      if (typeof element === "string") {
        return renderTag(element, props);
      }
    }
    throw new Error(true ? "Base UI: Render element or function are not defined." : formatErrorMessage_default(8));
  }
  function warnIfRenderPropLooksLikeComponent(renderFn) {
    const functionName = renderFn.name;
    if (functionName.length === 0) {
      return;
    }
    if (!COMPONENT_IDENTIFIER_PATTERN.test(functionName)) {
      return;
    }
    if (!LOWERCASE_CHARACTER_PATTERN.test(functionName)) {
      return;
    }
    warn(`The \`render\` prop received a function named \`${functionName}\` that starts with an uppercase letter.`, "This usually means a React component was passed directly as `render={Component}`.", "Base UI calls `render` as a plain function, which can break the Rules of Hooks during reconciliation.", "If this is an intentional render callback, rename it to start with a lowercase letter.", "Use `render={<Component />}` or `render={(props) => <Component {...props} />}` instead.", "https://base-ui.com/r/invalid-render-prop");
  }
  function renderTag(Tag, props) {
    if (Tag === "button") {
      return /* @__PURE__ */ (0, import_react.createElement)("button", {
        type: "button",
        ...props,
        key: props.key
      });
    }
    if (Tag === "img") {
      return /* @__PURE__ */ (0, import_react.createElement)("img", {
        alt: "",
        ...props,
        key: props.key
      });
    }
    return /* @__PURE__ */ React9.createElement(Tag, props);
  }

  // node_modules/@base-ui/utils/useId.mjs
  var React10 = __toESM(require_react(), 1);
  var globalId = 0;
  function useGlobalId(idOverride, prefix = "mui") {
    const [defaultId, setDefaultId] = React10.useState(idOverride);
    const id = idOverride || defaultId;
    React10.useEffect(() => {
      if (defaultId == null) {
        globalId += 1;
        setDefaultId(`${prefix}-${globalId}`);
      }
    }, [defaultId, prefix]);
    return id;
  }
  var maybeReactUseId = SafeReact.useId;
  function useId(idOverride, prefix) {
    if (maybeReactUseId !== void 0) {
      const reactId = maybeReactUseId();
      return idOverride ?? (prefix ? `${prefix}-${reactId}` : reactId);
    }
    return useGlobalId(idOverride, prefix);
  }

  // node_modules/@base-ui/react/internals/useBaseUiId.mjs
  function useBaseUiId(idOverride) {
    return useId(idOverride, "base-ui");
  }

  // node_modules/@base-ui/react/internals/reason-parts.mjs
  var reason_parts_exports = {};
  __export(reason_parts_exports, {
    cancelOpen: () => cancelOpen,
    chipRemovePress: () => chipRemovePress,
    clearPress: () => clearPress,
    closePress: () => closePress,
    closeWatcher: () => closeWatcher,
    decrementPress: () => decrementPress,
    disabled: () => disabled,
    drag: () => drag,
    escapeKey: () => escapeKey,
    focusOut: () => focusOut,
    imperativeAction: () => imperativeAction,
    incrementPress: () => incrementPress,
    initial: () => initial,
    inputBlur: () => inputBlur,
    inputChange: () => inputChange,
    inputClear: () => inputClear,
    inputPaste: () => inputPaste,
    inputPress: () => inputPress,
    itemPress: () => itemPress,
    keyboard: () => keyboard,
    linkPress: () => linkPress,
    listNavigation: () => listNavigation,
    missing: () => missing,
    none: () => none,
    outsidePress: () => outsidePress,
    pointer: () => pointer,
    scrub: () => scrub,
    siblingOpen: () => siblingOpen,
    swipe: () => swipe,
    trackPress: () => trackPress,
    triggerFocus: () => triggerFocus,
    triggerHover: () => triggerHover,
    triggerPress: () => triggerPress,
    wheel: () => wheel,
    windowResize: () => windowResize
  });
  var none = "none";
  var triggerPress = "trigger-press";
  var triggerHover = "trigger-hover";
  var triggerFocus = "trigger-focus";
  var outsidePress = "outside-press";
  var itemPress = "item-press";
  var closePress = "close-press";
  var linkPress = "link-press";
  var clearPress = "clear-press";
  var chipRemovePress = "chip-remove-press";
  var trackPress = "track-press";
  var incrementPress = "increment-press";
  var decrementPress = "decrement-press";
  var inputChange = "input-change";
  var inputClear = "input-clear";
  var inputBlur = "input-blur";
  var inputPaste = "input-paste";
  var inputPress = "input-press";
  var focusOut = "focus-out";
  var escapeKey = "escape-key";
  var closeWatcher = "close-watcher";
  var listNavigation = "list-navigation";
  var keyboard = "keyboard";
  var pointer = "pointer";
  var drag = "drag";
  var wheel = "wheel";
  var scrub = "scrub";
  var cancelOpen = "cancel-open";
  var siblingOpen = "sibling-open";
  var disabled = "disabled";
  var missing = "missing";
  var initial = "initial";
  var imperativeAction = "imperative-action";
  var swipe = "swipe";
  var windowResize = "window-resize";

  // node_modules/@base-ui/react/internals/createBaseUIEventDetails.mjs
  function createChangeEventDetails(reason, event, trigger, customProperties) {
    let canceled = false;
    let allowPropagation = false;
    const custom = customProperties ?? EMPTY_OBJECT;
    const details = {
      reason,
      event: event ?? new Event("base-ui"),
      cancel() {
        canceled = true;
      },
      allowPropagation() {
        allowPropagation = true;
      },
      get isCanceled() {
        return canceled;
      },
      get isPropagationAllowed() {
        return allowPropagation;
      },
      trigger,
      ...custom
    };
    return details;
  }

  // node_modules/@base-ui/react/internals/useTransitionStatus.mjs
  var React12 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/useOnMount.mjs
  var React11 = __toESM(require_react(), 1);
  function useOnMount(fn) {
    React11.useEffect(fn, EMPTY_ARRAY);
  }

  // node_modules/@base-ui/utils/useAnimationFrame.mjs
  var EMPTY = null;
  var LAST_RAF = globalThis.requestAnimationFrame;
  var Scheduler = class {
    /* This implementation uses an array as a backing data-structure for frame callbacks.
     * It allows `O(1)` callback cancelling by inserting a `null` in the array, though it
     * never calls the native `cancelAnimationFrame` if there are no frames left. This can
     * be much more efficient if there is a call pattern that alterns as
     * "request-cancel-request-cancel-…".
     * But in the case of "request-request-…-cancel-cancel-…", it leaves the final animation
     * frame to run anyway. We turn that frame into a `O(1)` no-op via `callbacksCount`. */
    callbacks = [];
    callbacksCount = 0;
    nextId = 1;
    startId = 1;
    isScheduled = false;
    tick = (timestamp) => {
      this.isScheduled = false;
      const currentCallbacks = this.callbacks;
      const currentCallbacksCount = this.callbacksCount;
      this.callbacks = [];
      this.callbacksCount = 0;
      this.startId = this.nextId;
      if (currentCallbacksCount > 0) {
        for (let i = 0; i < currentCallbacks.length; i += 1) {
          currentCallbacks[i]?.(timestamp);
        }
      }
    };
    request(fn) {
      const id = this.nextId;
      this.nextId += 1;
      this.callbacks.push(fn);
      this.callbacksCount += 1;
      const didRAFChange = LAST_RAF !== requestAnimationFrame && (LAST_RAF = requestAnimationFrame, true);
      if (!this.isScheduled || didRAFChange) {
        requestAnimationFrame(this.tick);
        this.isScheduled = true;
      }
      return id;
    }
    cancel(id) {
      const index2 = id - this.startId;
      if (index2 < 0 || index2 >= this.callbacks.length) {
        return;
      }
      if (this.callbacks[index2] === null) {
        return;
      }
      this.callbacks[index2] = null;
      this.callbacksCount -= 1;
    }
  };
  var scheduler = new Scheduler();
  var AnimationFrame = class _AnimationFrame {
    static create() {
      return new _AnimationFrame();
    }
    static request(fn) {
      return scheduler.request(fn);
    }
    static cancel(id) {
      return scheduler.cancel(id);
    }
    currentId = EMPTY;
    /**
     * Executes `fn` after `delay`, clearing any previously scheduled call.
     */
    request(fn) {
      this.cancel();
      this.currentId = scheduler.request(() => {
        this.currentId = EMPTY;
        fn();
      });
    }
    cancel = () => {
      if (this.currentId !== EMPTY) {
        scheduler.cancel(this.currentId);
        this.currentId = EMPTY;
      }
    };
    disposeEffect = () => {
      return this.cancel;
    };
  };
  function useAnimationFrame() {
    const timeout = useRefWithInit(AnimationFrame.create).current;
    useOnMount(timeout.disposeEffect);
    return timeout;
  }

  // node_modules/@base-ui/react/internals/useTransitionStatus.mjs
  function useTransitionStatus(open, enableIdleState = false, deferEndingState = false, animateInitialOpen = false) {
    const [transitionStatus, setTransitionStatus] = React12.useState(open && enableIdleState ? "idle" : void 0);
    const [mounted, setMounted] = React12.useState(open && !animateInitialOpen);
    if (open && !mounted) {
      setMounted(true);
      setTransitionStatus("starting");
    }
    if (!open && mounted && transitionStatus !== "ending" && !deferEndingState) {
      setTransitionStatus("ending");
    }
    if (!open && !mounted && transitionStatus === "ending") {
      setTransitionStatus(void 0);
    }
    useIsoLayoutEffect(() => {
      if (!open && mounted && transitionStatus !== "ending" && deferEndingState) {
        const frame = AnimationFrame.request(() => {
          setTransitionStatus("ending");
        });
        return () => {
          AnimationFrame.cancel(frame);
        };
      }
      return void 0;
    }, [open, mounted, transitionStatus, deferEndingState]);
    useIsoLayoutEffect(() => {
      if (!open || enableIdleState) {
        return void 0;
      }
      const frame = AnimationFrame.request(() => {
        setTransitionStatus(void 0);
      });
      return () => {
        AnimationFrame.cancel(frame);
      };
    }, [enableIdleState, open]);
    useIsoLayoutEffect(() => {
      if (!open || !enableIdleState) {
        return void 0;
      }
      if (open && mounted && transitionStatus !== "idle") {
        setTransitionStatus("starting");
      }
      const frame = AnimationFrame.request(() => {
        setTransitionStatus("idle");
      });
      return () => {
        AnimationFrame.cancel(frame);
      };
    }, [enableIdleState, open, mounted, transitionStatus]);
    return {
      mounted,
      setMounted,
      transitionStatus
    };
  }

  // node_modules/@base-ui/react/internals/composite/list/useCompositeListItem.mjs
  var React13 = __toESM(require_react(), 1);
  function useCompositeListItem(params = {}) {
    const {
      guess,
      label,
      metadata,
      textRef,
      index: externalIndex
    } = params;
    const {
      register,
      unregister,
      subscribeMapChange,
      nextIndexRef
    } = useCompositeListContext();
    const indexRef = React13.useRef(-1);
    const [internalIndex, setInternalIndex] = React13.useState(externalIndex == null && guess ? () => {
      if (indexRef.current === -1) {
        const newIndex = nextIndexRef.current;
        nextIndexRef.current += 1;
        indexRef.current = newIndex;
      }
      return indexRef.current;
    } : -1);
    const index2 = externalIndex ?? internalIndex;
    const componentRef = React13.useRef(null);
    const ref = React13.useCallback((node) => {
      const previousNode = componentRef.current;
      if (previousNode) {
        unregister(previousNode);
      }
      componentRef.current = node;
      if (node) {
        register(node, {
          metadata: metadata ?? null,
          index: externalIndex ?? null,
          label,
          textRef
        });
      }
    }, [externalIndex, register, unregister, metadata, label, textRef]);
    useIsoLayoutEffect(() => {
      if (externalIndex != null) {
        return void 0;
      }
      return subscribeMapChange((map) => {
        const i = componentRef.current ? map.get(componentRef.current)?.index : null;
        if (i != null) {
          setInternalIndex(i);
        }
      });
    }, [externalIndex, subscribeMapChange]);
    return {
      ref,
      index: index2
    };
  }

  // node_modules/@base-ui/react/internals/TransitionStatusDataAttributes.mjs
  var TransitionStatusDataAttributes_exports = {};
  __export(TransitionStatusDataAttributes_exports, {
    endingStyle: () => endingStyle,
    startingStyle: () => startingStyle
  });
  var startingStyle = "data-starting-style";
  var endingStyle = "data-ending-style";

  // node_modules/@base-ui/react/internals/stateAttributesMapping.mjs
  var STARTING_HOOK = {
    [startingStyle]: ""
  };
  var ENDING_HOOK = {
    [endingStyle]: ""
  };
  var transitionStatusMapping = {
    transitionStatus(value) {
      if (value === "starting") {
        return STARTING_HOOK;
      }
      if (value === "ending") {
        return ENDING_HOOK;
      }
      return null;
    }
  };

  // node_modules/@base-ui/react/internals/use-button/useButton.mjs
  var React16 = __toESM(require_react(), 1);

  // node_modules/@floating-ui/utils/dist/floating-ui.utils.dom.mjs
  function hasWindow() {
    return typeof window !== "undefined";
  }
  function getNodeName(node) {
    if (isNode(node)) {
      return (node.nodeName || "").toLowerCase();
    }
    return "#document";
  }
  function getWindow(node) {
    var _node$ownerDocument;
    return (node == null || (_node$ownerDocument = node.ownerDocument) == null ? void 0 : _node$ownerDocument.defaultView) || window;
  }
  function getDocumentElement(node) {
    var _ref;
    return (_ref = (isNode(node) ? node.ownerDocument : node.document) || window.document) == null ? void 0 : _ref.documentElement;
  }
  function isNode(value) {
    if (!hasWindow()) {
      return false;
    }
    return value instanceof Node || value instanceof getWindow(value).Node;
  }
  function isHTMLElement(value) {
    if (!hasWindow()) {
      return false;
    }
    return value instanceof HTMLElement || value instanceof getWindow(value).HTMLElement;
  }
  function isShadowRoot(value) {
    if (!hasWindow() || typeof ShadowRoot === "undefined") {
      return false;
    }
    return value instanceof ShadowRoot || value instanceof getWindow(value).ShadowRoot;
  }
  function isLastTraversableNode(node) {
    return /^(html|body|#document)$/.test(getNodeName(node));
  }
  function getComputedStyle2(element) {
    return getWindow(element).getComputedStyle(element);
  }
  function getParentNode(node) {
    if (getNodeName(node) === "html") {
      return node;
    }
    const result = (
      // Step into the shadow DOM of the parent of a slotted node.
      node.assignedSlot || // DOM Element detected.
      node.parentNode || // ShadowRoot detected.
      isShadowRoot(node) && node.host || // Fallback.
      getDocumentElement(node)
    );
    return isShadowRoot(result) ? result.host : result;
  }

  // node_modules/@base-ui/react/internals/composite/root/CompositeRootContext.mjs
  var React14 = __toESM(require_react(), 1);
  var CompositeRootContext = /* @__PURE__ */ React14.createContext(void 0);
  if (true) CompositeRootContext.displayName = "CompositeRootContext";
  function useCompositeRootContext(optional = false) {
    const context = React14.useContext(CompositeRootContext);
    if (context === void 0 && !optional) {
      throw new Error(true ? "Base UI: CompositeRootContext is missing. Composite parts must be placed within <Composite.Root>." : formatErrorMessage_default(16));
    }
    return context;
  }

  // node_modules/@base-ui/react/utils/useFocusableWhenDisabled.mjs
  var React15 = __toESM(require_react(), 1);
  function useFocusableWhenDisabled(parameters) {
    const {
      focusableWhenDisabled,
      disabled: disabled2,
      composite = false,
      tabIndex: tabIndexProp = 0,
      isNativeButton
    } = parameters;
    const isFocusableComposite = composite && focusableWhenDisabled !== false;
    const isNonFocusableComposite = composite && focusableWhenDisabled === false;
    const props = React15.useMemo(() => {
      const additionalProps = {
        // allow Tabbing away from focusableWhenDisabled elements
        onKeyDown(event) {
          if (disabled2 && focusableWhenDisabled && event.key !== "Tab") {
            event.preventDefault();
          }
        }
      };
      if (!composite) {
        additionalProps.tabIndex = tabIndexProp;
        if (!isNativeButton && disabled2) {
          additionalProps.tabIndex = focusableWhenDisabled ? tabIndexProp : -1;
        }
      }
      if (isNativeButton && (focusableWhenDisabled || isFocusableComposite) || !isNativeButton && disabled2) {
        additionalProps["aria-disabled"] = disabled2;
      }
      if (isNativeButton && (!focusableWhenDisabled || isNonFocusableComposite)) {
        additionalProps.disabled = disabled2;
      }
      return additionalProps;
    }, [composite, disabled2, focusableWhenDisabled, isFocusableComposite, isNonFocusableComposite, isNativeButton, tabIndexProp]);
    return {
      props
    };
  }

  // node_modules/@base-ui/utils/owner.mjs
  function ownerDocument(node) {
    return node?.ownerDocument || document;
  }

  // node_modules/@base-ui/react/utils/dispatchClickWithModifiers.mjs
  function dispatchClickWithModifiers(target, sourceEvent, {
    detail = 0
  } = {}) {
    target.dispatchEvent(new (getWindow(target)).PointerEvent("click", {
      bubbles: true,
      cancelable: true,
      composed: true,
      detail,
      shiftKey: sourceEvent.shiftKey,
      ctrlKey: sourceEvent.ctrlKey,
      altKey: sourceEvent.altKey,
      metaKey: sourceEvent.metaKey
    }));
  }

  // node_modules/@base-ui/react/internals/use-button/useButton.mjs
  function useButton(parameters = {}) {
    const {
      disabled: disabled2 = false,
      focusableWhenDisabled,
      tabIndex = 0,
      native: isNativeButton = true,
      composite: compositeProp
    } = parameters;
    const elementRef = React16.useRef(null);
    const compositeRootContext = useCompositeRootContext(true);
    const isCompositeItem = compositeProp ?? compositeRootContext !== void 0;
    const {
      props: focusableWhenDisabledProps
    } = useFocusableWhenDisabled({
      focusableWhenDisabled,
      disabled: disabled2,
      composite: isCompositeItem,
      tabIndex,
      isNativeButton
    });
    if (true) {
      React16.useEffect(() => {
        if (!elementRef.current) {
          return;
        }
        const isButtonTag = isButtonElement(elementRef.current);
        if (isNativeButton) {
          if (!isButtonTag) {
            const ownerStackMessage = SafeReact.captureOwnerStack?.() || "";
            const message = "A component that acts as a button expected a native <button> because the `nativeButton` prop is true. Rendering a non-<button> removes native button semantics, which can impact forms and accessibility. Use a real <button> in the `render` prop, or set `nativeButton` to `false`.";
            error(`${message}${ownerStackMessage}`);
          }
        } else if (isButtonTag) {
          const ownerStackMessage = SafeReact.captureOwnerStack?.() || "";
          const message = "A component that acts as a button expected a non-<button> because the `nativeButton` prop is false. Rendering a <button> keeps native behavior while Base UI applies non-native attributes and handlers, which can add unintended extra attributes (such as `role` or `aria-disabled`). Use a non-<button> in the `render` prop, or set `nativeButton` to `true`.";
          error(`${message}${ownerStackMessage}`);
        }
      }, [isNativeButton]);
    }
    const updateDisabled = React16.useCallback(() => {
      const element = elementRef.current;
      if (!isButtonElement(element)) {
        return;
      }
      if (isCompositeItem && disabled2 && focusableWhenDisabledProps.disabled === void 0 && element.disabled) {
        element.disabled = false;
      }
    }, [disabled2, focusableWhenDisabledProps.disabled, isCompositeItem]);
    useIsoLayoutEffect(updateDisabled, [updateDisabled]);
    const getButtonProps = React16.useCallback((externalProps = {}) => {
      const {
        onClick: externalOnClick,
        onMouseDown: externalOnMouseDown,
        onKeyUp: externalOnKeyUp,
        onKeyDown: externalOnKeyDown,
        onPointerDown: externalOnPointerDown,
        ...otherExternalProps
      } = externalProps;
      return mergeProps({
        onClick(event) {
          if (disabled2) {
            event.preventDefault();
            return;
          }
          externalOnClick?.(event);
        },
        onMouseDown(event) {
          if (!disabled2) {
            externalOnMouseDown?.(event);
          }
        },
        onKeyDown(event) {
          if (disabled2) {
            return;
          }
          makeEventPreventable(event);
          externalOnKeyDown?.(event);
          if (event.baseUIHandlerPrevented) {
            return;
          }
          const isCurrentTarget = event.target === event.currentTarget;
          const currentTarget = event.currentTarget;
          const isButton = isButtonElement(currentTarget);
          const isLink = !isNativeButton && isValidLinkElement(currentTarget);
          const shouldClick = isCurrentTarget && (isNativeButton ? isButton : !isLink);
          const isEnterKey = event.key === "Enter";
          const isSpaceKey = event.key === " ";
          const role = currentTarget.getAttribute("role");
          const isTextNavigationRole = role?.startsWith("menuitem") || role === "option" || role === "gridcell";
          if (isCurrentTarget && isCompositeItem && isSpaceKey) {
            if (event.defaultPrevented && isTextNavigationRole) {
              return;
            }
            event.preventDefault();
            if (!isNativeButton || isButton) {
              event.preventBaseUIHandler();
              dispatchClickWithModifiers(currentTarget, event);
            }
            return;
          }
          if (!shouldClick || isNativeButton || !isSpaceKey && !isEnterKey) {
            if (isCurrentTarget && isLink && isSpaceKey) {
              event.preventDefault();
            }
            return;
          }
          if (event.defaultPrevented) {
            return;
          }
          event.preventDefault();
          if (isEnterKey) {
            event.preventBaseUIHandler();
            dispatchClickWithModifiers(currentTarget, event);
          }
        },
        onKeyUp(event) {
          if (disabled2) {
            return;
          }
          makeEventPreventable(event);
          externalOnKeyUp?.(event);
          if (event.target === event.currentTarget && isNativeButton && isCompositeItem && isButtonElement(event.currentTarget) && event.key === " ") {
            event.preventDefault();
            return;
          }
          if (event.baseUIHandlerPrevented) {
            return;
          }
          if (event.target === event.currentTarget && !isNativeButton && !isCompositeItem && !event.defaultPrevented && event.key === " ") {
            event.preventBaseUIHandler();
            dispatchClickWithModifiers(event.currentTarget, event);
          }
        },
        onPointerDown(event) {
          if (disabled2) {
            event.preventDefault();
            return;
          }
          externalOnPointerDown?.(event);
        }
      }, isNativeButton ? {
        type: "button"
      } : {
        role: "button"
      }, focusableWhenDisabledProps, otherExternalProps);
    }, [disabled2, focusableWhenDisabledProps, isCompositeItem, isNativeButton]);
    const buttonRef = useStableCallback((element) => {
      elementRef.current = element;
      updateDisabled();
    });
    return {
      getButtonProps,
      buttonRef
    };
  }
  function isButtonElement(elem) {
    return isHTMLElement(elem) && elem.tagName === "BUTTON";
  }
  function isValidLinkElement(elem) {
    return isHTMLElement(elem) && elem.tagName === "A" && Boolean(elem.href);
  }

  // node_modules/@base-ui/react/internals/useOpenChangeComplete.mjs
  var React17 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/internals/useAnimationsFinished.mjs
  var ReactDOM = __toESM(require_react_dom(), 1);

  // node_modules/@base-ui/react/utils/resolveRef.mjs
  function resolveRef(maybeRef) {
    if (maybeRef == null) {
      return maybeRef;
    }
    return "current" in maybeRef ? maybeRef.current : maybeRef;
  }

  // node_modules/@base-ui/react/internals/useAnimationsFinished.mjs
  var pendingCallbacks = null;
  function flushBeforePaint(fn) {
    if (!pendingCallbacks) {
      const callbacks = [];
      pendingCallbacks = callbacks;
      queueMicrotask(() => {
        pendingCallbacks = null;
        ReactDOM.flushSync(() => {
          for (const callback of callbacks) {
            callback();
          }
        });
      });
    }
    pendingCallbacks.push(fn);
  }
  function useAnimationsFinished(elementOrRef, waitForStartingStyleRemoved = false, batch = false) {
    const frame = useAnimationFrame();
    return useStableCallback((fnToExecute, signal = null) => {
      frame.cancel();
      const element = resolveRef(elementOrRef);
      if (element == null) {
        return;
      }
      const resolvedElement = element;
      const done = () => {
        if (!batch) {
          ReactDOM.flushSync(fnToExecute);
          return;
        }
        flushBeforePaint(() => {
          if (!signal?.aborted) {
            fnToExecute();
          }
        });
      };
      if (typeof resolvedElement.getAnimations !== "function" || globalThis.BASE_UI_ANIMATIONS_DISABLED) {
        fnToExecute();
        return;
      }
      function exec() {
        Promise.all(resolvedElement.getAnimations().map((animation) => animation.finished)).then(() => {
          if (!signal?.aborted) {
            done();
          }
        }, () => {
          if (signal?.aborted) {
            return;
          }
          const currentAnimations = resolvedElement.getAnimations();
          if (currentAnimations.some((animation) => animation.pending || animation.playState !== "finished")) {
            exec();
            return;
          }
          done();
        });
      }
      if (waitForStartingStyleRemoved) {
        const startingStyleAttribute = startingStyle;
        if (!resolvedElement.hasAttribute(startingStyleAttribute)) {
          frame.request(exec);
          return;
        }
        const attributeObserver = new MutationObserver(() => {
          if (!resolvedElement.hasAttribute(startingStyleAttribute)) {
            attributeObserver.disconnect();
            exec();
          }
        });
        attributeObserver.observe(resolvedElement, {
          attributes: true,
          attributeFilter: [startingStyleAttribute]
        });
        signal?.addEventListener("abort", () => attributeObserver.disconnect(), {
          once: true
        });
        return;
      }
      frame.request(exec);
    });
  }

  // node_modules/@base-ui/react/internals/useOpenChangeComplete.mjs
  function useOpenChangeComplete(parameters) {
    const {
      enabled = true,
      open,
      ref,
      batch = false,
      onComplete: onCompleteParam
    } = parameters;
    const onComplete = useStableCallback(onCompleteParam);
    const runOnceAnimationsFinish = useAnimationsFinished(ref, open, batch);
    React17.useEffect(() => {
      if (!enabled) {
        return void 0;
      }
      const abortController = new AbortController();
      runOnceAnimationsFinish(onComplete, abortController.signal);
      return () => {
        abortController.abort();
      };
    }, [enabled, open, onComplete, runOnceAnimationsFinish]);
  }

  // node_modules/@base-ui/utils/useTimeout.mjs
  var EMPTY2 = 0;
  var Timeout = class _Timeout {
    static create() {
      return new _Timeout();
    }
    currentId = EMPTY2;
    /**
     * Executes `fn` after `delay`, clearing any previously scheduled call.
     */
    start(delay, fn) {
      this.clear();
      this.currentId = setTimeout(() => {
        this.currentId = EMPTY2;
        fn();
      }, delay);
    }
    isStarted() {
      return this.currentId !== EMPTY2;
    }
    clear = () => {
      if (this.currentId !== EMPTY2) {
        clearTimeout(this.currentId);
        this.currentId = EMPTY2;
      }
    };
    disposeEffect = () => {
      return this.clear;
    };
  };
  function useTimeout() {
    const timeout = useRefWithInit(Timeout.create).current;
    useOnMount(timeout.disposeEffect);
    return timeout;
  }

  // node_modules/@base-ui/utils/shadowDom.mjs
  function activeElement(doc) {
    let element = doc.activeElement;
    while (element?.shadowRoot?.activeElement != null) {
      element = element.shadowRoot.activeElement;
    }
    return element;
  }
  function contains(parent, child) {
    if (!parent || !child) {
      return false;
    }
    const rootNode = child.getRootNode?.();
    if (parent.contains(child)) {
      return true;
    }
    if (rootNode && isShadowRoot(rootNode)) {
      let next = child;
      while (next) {
        if (parent === next) {
          return true;
        }
        next = next.parentNode || next.host;
      }
    }
    return false;
  }
  function getTarget(event) {
    if ("composedPath" in event) {
      return event.composedPath()[0] ?? event.target;
    }
    return event.target;
  }

  // node_modules/@floating-ui/utils/dist/floating-ui.utils.mjs
  var round = Math.round;

  // node_modules/@base-ui/react/floating-ui-react/utils/composite.mjs
  function isIndexOutOfListBounds(list, index2) {
    return index2 < 0 || index2 >= list.length;
  }
  function getMinListIndex(listRef, disabledIndices) {
    return findNonDisabledListIndex(listRef.current, {
      disabledIndices
    });
  }
  function getMaxListIndex(listRef, disabledIndices) {
    return findNonDisabledListIndex(listRef.current, {
      decrement: true,
      startingIndex: listRef.current.length,
      disabledIndices
    });
  }
  function findNonDisabledListIndex(list, {
    startingIndex = -1,
    decrement = false,
    disabledIndices,
    amount = 1
  } = {}) {
    let index2 = startingIndex;
    do {
      index2 += decrement ? -amount : amount;
    } while (index2 >= 0 && index2 <= list.length - 1 && isListIndexDisabled(list, index2, disabledIndices));
    return index2;
  }
  function isListIndexDisabled(list, index2, disabledIndices) {
    const isExplicitlyDisabled = typeof disabledIndices === "function" ? disabledIndices(index2) : disabledIndices?.includes(index2) ?? false;
    if (isExplicitlyDisabled) {
      return true;
    }
    const element = list[index2];
    if (!element) {
      return false;
    }
    if (!isElementVisible(element)) {
      return true;
    }
    if (element.matches(":disabled")) {
      return true;
    }
    return !disabledIndices && (element.hasAttribute("disabled") || element.getAttribute("aria-disabled") === "true");
  }
  function isHiddenByStyles(styles) {
    return styles.visibility === "hidden" || styles.visibility === "collapse";
  }
  function isElementVisible(element, styles = element ? getComputedStyle2(element) : null) {
    if (!element || !element.isConnected || !styles || isHiddenByStyles(styles)) {
      return false;
    }
    if (typeof element.checkVisibility === "function") {
      return element.checkVisibility();
    }
    return styles.display !== "none" && styles.display !== "contents";
  }

  // node_modules/@base-ui/utils/useForcedRerendering.mjs
  var React18 = __toESM(require_react(), 1);
  function useForcedRerendering() {
    const [, setState] = React18.useState({});
    return React18.useCallback(() => {
      setState({});
    }, []);
  }

  // node_modules/@base-ui/react/internals/composite/composite.mjs
  var ARROW_UP = "ArrowUp";
  var ARROW_DOWN = "ArrowDown";
  var ARROW_LEFT = "ArrowLeft";
  var ARROW_RIGHT = "ArrowRight";
  var HOME = "Home";
  var END = "End";
  var COMPOSITE_KEYS = /* @__PURE__ */ new Set([ARROW_UP, ARROW_DOWN, ARROW_LEFT, ARROW_RIGHT, HOME, END]);
  var SHIFT = "Shift";
  var MODIFIER_KEYS = [SHIFT, "Control", "Alt", "Meta"];
  function isInputElement(element) {
    return isHTMLElement(element) && element.tagName === "INPUT";
  }
  function isNativeInput(element) {
    if (isInputElement(element) && element.selectionStart != null) {
      return true;
    }
    if (isHTMLElement(element) && element.tagName === "TEXTAREA") {
      return true;
    }
    return false;
  }
  function scrollIntoViewIfNeeded(scrollContainer, element, direction, orientation) {
    if (!scrollContainer || !element || !element.scrollTo) {
      return;
    }
    let targetX = scrollContainer.scrollLeft;
    let targetY = scrollContainer.scrollTop;
    const isOverflowingX = scrollContainer.clientWidth < scrollContainer.scrollWidth;
    const isOverflowingY = scrollContainer.clientHeight < scrollContainer.scrollHeight;
    if (isOverflowingX && orientation !== "vertical") {
      const elementOffsetLeft = getOffset(scrollContainer, element, "left");
      const containerStyles = getStyles(scrollContainer);
      const elementStyles = getStyles(element);
      if (direction === "ltr") {
        if (elementOffsetLeft + element.offsetWidth + elementStyles.scrollMarginRight > scrollContainer.scrollLeft + scrollContainer.clientWidth - containerStyles.scrollPaddingRight) {
          targetX = elementOffsetLeft + element.offsetWidth + elementStyles.scrollMarginRight - scrollContainer.clientWidth + containerStyles.scrollPaddingRight;
        } else if (elementOffsetLeft - elementStyles.scrollMarginLeft < scrollContainer.scrollLeft + containerStyles.scrollPaddingLeft) {
          targetX = elementOffsetLeft - elementStyles.scrollMarginLeft - containerStyles.scrollPaddingLeft;
        }
      }
      if (direction === "rtl") {
        if (elementOffsetLeft - elementStyles.scrollMarginLeft < scrollContainer.scrollLeft + containerStyles.scrollPaddingLeft) {
          targetX = elementOffsetLeft - elementStyles.scrollMarginLeft - containerStyles.scrollPaddingLeft;
        } else if (elementOffsetLeft + element.offsetWidth + elementStyles.scrollMarginRight > scrollContainer.scrollLeft + scrollContainer.clientWidth - containerStyles.scrollPaddingRight) {
          targetX = elementOffsetLeft + element.offsetWidth + elementStyles.scrollMarginRight - scrollContainer.clientWidth + containerStyles.scrollPaddingRight;
        }
      }
    }
    if (isOverflowingY && orientation !== "horizontal") {
      const elementOffsetTop = getOffset(scrollContainer, element, "top");
      const containerStyles = getStyles(scrollContainer);
      const elementStyles = getStyles(element);
      if (elementOffsetTop - elementStyles.scrollMarginTop < scrollContainer.scrollTop + containerStyles.scrollPaddingTop) {
        targetY = elementOffsetTop - elementStyles.scrollMarginTop - containerStyles.scrollPaddingTop;
      } else if (elementOffsetTop + element.offsetHeight + elementStyles.scrollMarginBottom > scrollContainer.scrollTop + scrollContainer.clientHeight - containerStyles.scrollPaddingBottom) {
        targetY = elementOffsetTop + element.offsetHeight + elementStyles.scrollMarginBottom - scrollContainer.clientHeight + containerStyles.scrollPaddingBottom;
      }
    }
    scrollContainer.scrollTo({
      left: targetX,
      top: targetY,
      behavior: "auto"
    });
  }
  function getOffset(ancestor, element, side) {
    const propName = side === "left" ? "offsetLeft" : "offsetTop";
    let result = 0;
    while (element.offsetParent) {
      result += element[propName];
      if (element.offsetParent === ancestor) {
        break;
      }
      element = element.offsetParent;
    }
    return result;
  }
  function getStyles(element) {
    const styles = getComputedStyle(element);
    return {
      scrollMarginTop: parseFloat(styles.scrollMarginTop) || 0,
      scrollMarginRight: parseFloat(styles.scrollMarginRight) || 0,
      scrollMarginBottom: parseFloat(styles.scrollMarginBottom) || 0,
      scrollMarginLeft: parseFloat(styles.scrollMarginLeft) || 0,
      scrollPaddingTop: parseFloat(styles.scrollPaddingTop) || 0,
      scrollPaddingRight: parseFloat(styles.scrollPaddingRight) || 0,
      scrollPaddingBottom: parseFloat(styles.scrollPaddingBottom) || 0,
      scrollPaddingLeft: parseFloat(styles.scrollPaddingLeft) || 0
    };
  }

  // node_modules/@base-ui/utils/inertValue.mjs
  function inertValue(value) {
    if (isReactVersionAtLeast(19)) {
      return value;
    }
    return value ? "true" : void 0;
  }

  // node_modules/@base-ui/react/internals/useValueChanged.mjs
  var React19 = __toESM(require_react(), 1);
  function useValueChanged(value, onChange) {
    const valueRef = React19.useRef(value);
    const onChangeCallback = useStableCallback(onChange);
    useIsoLayoutEffect(() => {
      if (valueRef.current !== value) {
        onChangeCallback(valueRef.current);
      }
      valueRef.current = value;
    }, [value, onChangeCallback]);
  }

  // node_modules/@base-ui/react/internals/field-root-context/FieldRootContext.mjs
  var React20 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/field/control/FieldControlDataAttributes.mjs
  var valid = "data-valid";
  var invalid = "data-invalid";

  // node_modules/@base-ui/react/internals/field-constants/constants.mjs
  var DEFAULT_VALIDITY_STATE = {
    badInput: false,
    customError: false,
    patternMismatch: false,
    rangeOverflow: false,
    rangeUnderflow: false,
    stepMismatch: false,
    tooLong: false,
    tooShort: false,
    typeMismatch: false,
    valid: null,
    valueMissing: false
  };
  var DEFAULT_FIELD_STATE_ATTRIBUTES = {
    valid: null,
    touched: false,
    dirty: false,
    filled: false,
    focused: false
  };
  var DEFAULT_FIELD_ROOT_STATE = {
    disabled: false,
    ...DEFAULT_FIELD_STATE_ATTRIBUTES
  };
  var fieldValidityMapping = {
    valid(value) {
      if (value === null) {
        return null;
      }
      if (value) {
        return {
          [valid]: ""
        };
      }
      return {
        [invalid]: ""
      };
    }
  };

  // node_modules/@base-ui/react/internals/field-root-context/FieldRootContext.mjs
  var DEFAULT_FIELD_ROOT_CONTEXT = {
    invalid: void 0,
    name: void 0,
    validityData: {
      state: DEFAULT_VALIDITY_STATE,
      errors: [],
      error: "",
      value: "",
      initialValue: null
    },
    setValidityData: NOOP,
    disabled: void 0,
    setTouched: NOOP,
    setDirty: NOOP,
    setFilled: NOOP,
    setFocused: NOOP,
    validationMode: "onSubmit",
    shouldValidateOnChange: () => false,
    state: DEFAULT_FIELD_ROOT_STATE,
    registerFieldControl: NOOP,
    validation: {
      getValidationProps: (_disabled, props = EMPTY_OBJECT) => props,
      inputRef: {
        current: null
      },
      registeredInputs: /* @__PURE__ */ new Map(),
      registerInput: NOOP,
      getInputControl: () => null,
      commit: async () => {
      },
      change: NOOP
    }
  };
  var FieldRootContext = /* @__PURE__ */ React20.createContext(DEFAULT_FIELD_ROOT_CONTEXT);
  if (true) FieldRootContext.displayName = "FieldRootContext";
  function useFieldRootContext(optional = true) {
    const context = React20.useContext(FieldRootContext);
    if (context.setValidityData === NOOP && !optional) {
      throw new Error(true ? "Base UI: FieldRootContext is missing. Field parts must be placed within <Field.Root>." : formatErrorMessage_default(28));
    }
    return context;
  }

  // node_modules/@base-ui/react/internals/field-register-control/useRegisterFieldControl.mjs
  function useRegisterFieldControl(controlRef, id, value, getFormValueOverride, enabled = true, name16) {
    const {
      registerFieldControl
    } = useFieldRootContext();
    const sourceRef = useRefWithInit(() => /* @__PURE__ */ Symbol());
    useIsoLayoutEffect(() => {
      const source = sourceRef.current;
      if (!enabled) {
        registerFieldControl(source, void 0);
        return;
      }
      const registration = {
        controlRef,
        getValue: getFormValueOverride,
        id,
        name: name16,
        value
      };
      registerFieldControl(source, registration);
    }, [controlRef, enabled, getFormValueOverride, id, name16, registerFieldControl, sourceRef, value]);
    useIsoLayoutEffect(() => {
      const source = sourceRef.current;
      return () => {
        registerFieldControl(source, void 0);
      };
    }, [registerFieldControl, sourceRef]);
  }

  // node_modules/@base-ui/react/internals/form-context/FormContext.mjs
  var React21 = __toESM(require_react(), 1);
  var FormContext = /* @__PURE__ */ React21.createContext({
    elementRef: {
      current: null
    },
    formRef: {
      current: {
        fields: /* @__PURE__ */ new Map()
      }
    },
    errors: {},
    clearErrors: NOOP,
    validationMode: "onSubmit",
    submitCountRef: {
      current: 0
    }
  });
  if (true) FormContext.displayName = "FormContext";
  function useFormContext() {
    return React21.useContext(FormContext);
  }

  // node_modules/@base-ui/react/internals/labelable-provider/useLabelableId.mjs
  var React23 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/internals/labelable-provider/LabelableContext.mjs
  var React22 = __toESM(require_react(), 1);
  var LabelableContext = /* @__PURE__ */ React22.createContext({
    controlId: void 0,
    registerControlId: NOOP,
    resetControlId: NOOP,
    labelId: void 0,
    setLabelId: NOOP,
    messageIds: [],
    setMessageIds: NOOP,
    getDescriptionProps: (externalProps) => externalProps
  });
  if (true) LabelableContext.displayName = "LabelableContext";
  function useLabelableContext() {
    return React22.useContext(LabelableContext);
  }

  // node_modules/@base-ui/react/internals/labelable-provider/useLabelableId.mjs
  function useLabelableId(params = {}) {
    const {
      id,
      enabled = true
    } = params;
    const {
      controlId,
      registerControlId,
      resetControlId
    } = useLabelableContext();
    const defaultId = useBaseUiId();
    const controlSourceRef = useRefWithInit(() => /* @__PURE__ */ Symbol());
    const hasRegisteredRef = React23.useRef(false);
    const hadExplicitIdRef = React23.useRef(false);
    const unregisterControlId = useStableCallback(() => {
      if (!hasRegisteredRef.current || registerControlId === NOOP) {
        return;
      }
      hasRegisteredRef.current = false;
      registerControlId(controlSourceRef.current, void 0);
    });
    useIsoLayoutEffect(() => {
      if (!enabled || registerControlId === NOOP) {
        unregisterControlId();
        return void 0;
      }
      let nextId;
      if (id !== void 0) {
        hadExplicitIdRef.current = true;
        nextId = id;
      } else if (hadExplicitIdRef.current) {
        nextId = defaultId;
      } else {
        resetControlId();
        return void 0;
      }
      if (nextId === void 0) {
        unregisterControlId();
        return void 0;
      }
      hasRegisteredRef.current = true;
      registerControlId(controlSourceRef.current, nextId);
      return void 0;
    }, [id, enabled, registerControlId, resetControlId, defaultId, controlSourceRef, unregisterControlId]);
    useIsoLayoutEffect(() => {
      return unregisterControlId;
    }, [unregisterControlId]);
    return (enabled ? controlId : void 0) ?? id ?? defaultId;
  }

  // node_modules/@base-ui/react/internals/direction-context/DirectionContext.mjs
  var React24 = __toESM(require_react(), 1);
  var DirectionContext = /* @__PURE__ */ React24.createContext(void 0);
  if (true) DirectionContext.displayName = "DirectionContext";
  function useDirection() {
    const context = React24.useContext(DirectionContext);
    return context?.direction ?? "ltr";
  }

  // node_modules/@base-ui/react/field/item/FieldItemContext.mjs
  var React25 = __toESM(require_react(), 1);
  var FieldItemContext = /* @__PURE__ */ React25.createContext({
    disabled: false
  });
  if (true) FieldItemContext.displayName = "FieldItemContext";
  function useFieldItemContext() {
    const context = React25.useContext(FieldItemContext);
    return context;
  }

  // node_modules/@base-ui/react/field/root/useFieldValidation.mjs
  var React26 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/field/utils/getCombinedFieldValidityData.mjs
  function getCombinedFieldValidityData(validityData, invalid2) {
    return {
      ...validityData,
      state: {
        ...validityData.state,
        valid: !invalid2 && validityData.state.valid
      }
    };
  }

  // node_modules/@base-ui/react/field/root/useFieldValidation.mjs
  var validityKeys = Object.keys(DEFAULT_VALIDITY_STATE);
  function isEligibleInput(input, formElement) {
    if (input.matches(":disabled")) {
      return false;
    }
    if (!formElement || input.form === formElement) {
      return true;
    }
    return input.form === null && !input.hasAttribute("form");
  }
  function findRepresentativeInput(inputs, formElement) {
    let fallback = null;
    for (const input of inputs.keys()) {
      if (!isEligibleInput(input, formElement)) {
        continue;
      }
      if (!input.validity.valid) {
        return input;
      }
      fallback ??= input;
    }
    return fallback;
  }
  function makeState(customError) {
    return {
      ...DEFAULT_VALIDITY_STATE,
      valid: !customError,
      customError
    };
  }
  function getNativeErrors(element) {
    return element && element.validationMessage ? [element.validationMessage] : [];
  }
  function useFieldValidation(params) {
    const {
      elementRef,
      formRef
    } = useFormContext();
    const {
      setValidityData,
      validate,
      validityData,
      validationDebounceTime,
      invalid: invalid2,
      markedDirtyRef,
      state,
      shouldValidateOnChange,
      validationMode,
      registeredFieldIdRef
    } = params;
    const {
      controlId,
      getDescriptionProps
    } = useLabelableContext();
    const timeout = useTimeout();
    const inputRef = React26.useRef(null);
    const registeredInputs = useRefWithInit(() => /* @__PURE__ */ new Map()).current;
    const validationCommitIdRef = React26.useRef(0);
    const customValidityRef = React26.useRef(null);
    const registerInput = React26.useCallback((element, registration) => {
      registeredInputs.set(element, registration);
      return () => {
        registeredInputs.delete(element);
      };
    }, [registeredInputs]);
    const getInputControl = useStableCallback(() => {
      const element = findRepresentativeInput(registeredInputs, elementRef.current);
      return element && registeredInputs.get(element)?.controlRef.current || null;
    });
    const commit = useStableCallback(async (value, revalidate = false) => {
      validationCommitIdRef.current += 1;
      const validationCommitId = validationCommitIdRef.current;
      function updateRegisteredFieldValidity(nextValidityData, externalInvalid = invalid2) {
        const fieldId = registeredFieldIdRef.current ?? controlId;
        if (fieldId == null) {
          return;
        }
        const currentFieldData = formRef.current.fields.get(fieldId);
        if (!currentFieldData) {
          return;
        }
        const validityDataWithFormErrors = getCombinedFieldValidityData(nextValidityData, externalInvalid);
        formRef.current.fields.set(fieldId, {
          ...currentFieldData,
          validityData: validityDataWithFormErrors
        });
      }
      function makeValidityData(validityState, errorMessages) {
        const errors = validityState.valid === false ? errorMessages : [];
        return {
          value,
          state: validityState,
          error: errors[0] ?? "",
          errors,
          initialValue: validityData.initialValue
        };
      }
      function setCustomValidity(element2, message) {
        const displaced = element2.validity.customError ? element2.validationMessage : "";
        const ownedMessage = message.replace(/\r\n?/g, "\n");
        element2.setCustomValidity(ownedMessage);
        customValidityRef.current = [element2, ownedMessage, displaced];
      }
      function clearCustomValidity() {
        const record = customValidityRef.current;
        customValidityRef.current = null;
        if (record && (!record[0].willValidate || record[0].validationMessage === record[1])) {
          record[0].setCustomValidity(record[2]);
        }
      }
      function publish(validityState, errorMessages, externalInvalid) {
        const nextValidityData = makeValidityData(validityState, errorMessages);
        updateRegisteredFieldValidity(nextValidityData, externalInvalid);
        setValidityData(nextValidityData);
      }
      function getState(el) {
        const computedState = validityKeys.reduce((acc, key) => {
          acc[key] = el.validity[key];
          return acc;
        }, {});
        let hasOnlyValueMissingError = false;
        for (const key of validityKeys) {
          if (key === "valid") {
            continue;
          }
          if (key === "valueMissing" && computedState[key]) {
            hasOnlyValueMissingError = true;
          } else if (computedState[key]) {
            return computedState;
          }
        }
        if (hasOnlyValueMissingError && !markedDirtyRef.current) {
          computedState.valid = true;
          computedState.valueMissing = false;
        }
        return computedState;
      }
      function resolveRepresentativeInput() {
        return registeredInputs.size > 0 ? findRepresentativeInput(registeredInputs, elementRef.current) : inputRef.current;
      }
      let element = resolveRepresentativeInput();
      function refreshState() {
        element = resolveRepresentativeInput();
        return element?.willValidate ? getState(element) : makeState(false);
      }
      if (revalidate) {
        if (state.valid !== false || !element) {
          return;
        }
        if (!element.validity.valueMissing) {
          clearCustomValidity();
          const currentElement = resolveRepresentativeInput();
          const foreign = currentElement?.validity.customError ? getNativeErrors(currentElement) : [];
          publish(makeState(foreign.length > 0), foreign, false);
          return;
        }
        for (const key of validityKeys) {
          if (key !== "valid" && key !== "valueMissing" && key !== "customError" && element.validity[key]) {
            return;
          }
        }
      }
      timeout.clear();
      clearCustomValidity();
      let nextState = refreshState();
      let validationErrors = getNativeErrors(element);
      const isValidatingOnChange = shouldValidateOnChange();
      if (validationErrors.length === 0 || isValidatingOnChange) {
        const formValues = Array.from(formRef.current.fields.values()).reduce((acc, field) => {
          if (field.name) {
            acc[field.name] = field.getValue();
          }
          return acc;
        }, {});
        const resultOrPromise = validate(value, formValues);
        let result;
        if (typeof resultOrPromise === "object" && resultOrPromise !== null && "then" in resultOrPromise) {
          if (nextState.valid === false) {
            publish(nextState, validationErrors);
          } else if (validationMode === "onSubmit" || !validityData.state.customError) {
            nextState.valid = null;
            publish(nextState, validationErrors);
          }
          result = await resultOrPromise;
          if (validationCommitId !== validationCommitIdRef.current) {
            return;
          }
          nextState = refreshState();
        } else {
          result = resultOrPromise;
        }
        validationErrors = result ? [].concat(result).filter(Boolean) : [];
        if (validationErrors.length > 0) {
          nextState.valid = false;
          nextState.customError = true;
          if (element?.willValidate) {
            setCustomValidity(element, validationErrors.join("\n"));
          }
        } else {
          validationErrors = getNativeErrors(element);
        }
      }
      publish(nextState, validationErrors);
    });
    const change = useStableCallback((value, cancelPending = false) => {
      timeout.clear();
      validationCommitIdRef.current += 1;
      if (cancelPending) {
        return;
      }
      const validateOnChange = shouldValidateOnChange();
      if (validateOnChange && value !== "" && validationDebounceTime) {
        timeout.start(validationDebounceTime, () => {
          commit(value);
        });
      } else {
        commit(value, !validateOnChange);
      }
    });
    const getValidationProps = React26.useCallback((disabled2, externalProps = {}) => mergeProps(getDescriptionProps(externalProps), state.valid === false && !state.disabled && !disabled2 ? {
      "aria-invalid": true
    } : EMPTY_OBJECT), [getDescriptionProps, state.disabled, state.valid]);
    return React26.useMemo(() => ({
      getValidationProps,
      inputRef,
      registeredInputs,
      registerInput,
      getInputControl,
      commit,
      change
    }), [getValidationProps, registeredInputs, registerInput, getInputControl, commit, change]);
  }

  // node_modules/@base-ui/react/utils/useRegisteredLabelId.mjs
  function useRegisteredLabelId(idProp, setLabelId) {
    const id = useBaseUiId(idProp);
    useIsoLayoutEffect(() => {
      setLabelId(id);
      return () => {
        setLabelId((currentId) => currentId === id ? void 0 : currentId);
      };
    }, [id, setLabelId]);
    return id;
  }

  // node_modules/@base-ui/react/internals/labelable-provider/useLabel.mjs
  function useLabel(params = {}) {
    const {
      id: idProp,
      fallbackControlId,
      native = false,
      setLabelId: setLabelIdProp,
      focusControl: focusControlProp
    } = params;
    const {
      controlId: contextControlId,
      setLabelId: setContextLabelId
    } = useLabelableContext();
    const syncLabelId = useStableCallback((nextLabelId) => {
      setContextLabelId(nextLabelId);
      setLabelIdProp?.(nextLabelId);
    });
    const id = useRegisteredLabelId(idProp, syncLabelId);
    const resolvedControlId = contextControlId ?? fallbackControlId;
    function focusControl(event) {
      if (focusControlProp) {
        focusControlProp(event, resolvedControlId);
        return;
      }
      if (!resolvedControlId) {
        return;
      }
      const controlElement = ownerDocument(event.currentTarget).getElementById(resolvedControlId);
      if (isHTMLElement(controlElement)) {
        focusElementWithVisible(controlElement);
      }
    }
    function handleInteraction(event) {
      const target = getTarget(event.nativeEvent);
      if (target?.closest("button,input,select,textarea")) {
        return;
      }
      if (!event.defaultPrevented && event.detail > 1) {
        event.preventDefault();
      }
      if (native) {
        return;
      }
      focusControl(event);
    }
    return native ? {
      id,
      htmlFor: resolvedControlId,
      onMouseDown: handleInteraction
    } : {
      id,
      onClick: handleInteraction,
      onPointerDown(event) {
        event.preventDefault();
      }
    };
  }
  function focusElementWithVisible(element) {
    element.focus({
      // Available from Chrome 144+ (January 2026).
      // Safari and Firefox already support it.
      focusVisible: true
    });
  }

  // node_modules/@base-ui/react/internals/composite/item/useCompositeItem.mjs
  var React27 = __toESM(require_react(), 1);
  function useCompositeItem(params = {}) {
    const {
      highlightItemOnHover,
      highlightedIndex,
      onHighlightedIndexChange
    } = useCompositeRootContext();
    const {
      ref,
      index: index2
    } = useCompositeListItem(params);
    const isHighlighted = highlightedIndex === index2;
    const itemRef = React27.useRef(null);
    const mergedRef = useMergedRefs(ref, itemRef);
    const compositeProps = {
      tabIndex: isHighlighted ? 0 : -1,
      onFocus() {
        onHighlightedIndexChange(index2);
      },
      onMouseMove() {
        const item = itemRef.current;
        if (!highlightItemOnHover || !item) {
          return;
        }
        const disabled2 = item.hasAttribute("disabled") || item.ariaDisabled === "true";
        if (!isHighlighted && !disabled2) {
          item.focus();
        }
      }
    };
    return {
      compositeProps,
      compositeRef: mergedRef,
      index: index2
    };
  }

  // node_modules/@base-ui/react/utils/getCssDimensions.mjs
  function getCssDimensions(element) {
    const css = getComputedStyle2(element);
    let width = parseFloat(css.width) || 0;
    let height = parseFloat(css.height) || 0;
    const hasOffset = isHTMLElement(element);
    const offsetWidth = hasOffset ? element.offsetWidth : width;
    const offsetHeight = hasOffset ? element.offsetHeight : height;
    const shouldFallback = round(width) !== offsetWidth || round(height) !== offsetHeight;
    if (shouldFallback) {
      width = offsetWidth;
      height = offsetHeight;
    }
    return {
      width,
      height
    };
  }

  // node_modules/@base-ui/react/direction-provider/DirectionProvider.mjs
  var React28 = __toESM(require_react(), 1);
  var import_jsx_runtime21 = __toESM(require_jsx_runtime(), 1);
  var DirectionProvider = function DirectionProvider2(props) {
    const {
      direction = "ltr"
    } = props;
    const contextValue = React28.useMemo(() => ({
      direction
    }), [direction]);
    return /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(DirectionContext.Provider, {
      value: contextValue,
      children: props.children
    });
  };
  if (true) DirectionProvider.displayName = "DirectionProvider";

  // node_modules/@base-ui/utils/isElementDisabled.mjs
  function isElementDisabled(element) {
    return element == null || element.hasAttribute("disabled") || element.getAttribute("aria-disabled") === "true";
  }

  // node_modules/@base-ui/react/internals/csp-context/CSPContext.mjs
  var React29 = __toESM(require_react(), 1);
  var CSPContext = /* @__PURE__ */ React29.createContext(void 0);
  if (true) CSPContext.displayName = "CSPContext";
  var DEFAULT_CSP_CONTEXT_VALUE = {
    disableStyleElements: false
  };
  function useCSPContext() {
    return React29.useContext(CSPContext) ?? DEFAULT_CSP_CONTEXT_VALUE;
  }

  // node_modules/@base-ui/react/utils/getElementTransform.mjs
  function getElementTransform(element, computedStyle) {
    const transform = (computedStyle ?? getWindow(element).getComputedStyle(element)).transform;
    let translateX = 0;
    let translateY = 0;
    let scale = 1;
    if (transform && transform !== "none") {
      const matrix = transform.match(/matrix(?:3d)?\(([^)]+)\)/);
      if (matrix) {
        const values = matrix[1].split(", ").map(parseFloat);
        if (values.length === 6) {
          translateX = values[4];
          translateY = values[5];
          scale = Math.sqrt(values[0] * values[0] + values[1] * values[1]);
        } else if (values.length === 16) {
          translateX = values[12];
          translateY = values[13];
          scale = values[0];
        }
      }
    }
    return {
      x: translateX,
      y: translateY,
      scale
    };
  }

  // node_modules/@base-ui/react/field/index.parts.mjs
  var index_parts_exports = {};
  __export(index_parts_exports, {
    Control: () => FieldControl,
    Description: () => FieldDescription,
    Error: () => FieldError,
    Item: () => FieldItem,
    Label: () => FieldLabel,
    Root: () => FieldRoot,
    Validity: () => FieldValidity
  });

  // node_modules/@base-ui/react/field/root/FieldRoot.mjs
  var React33 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/fieldset/root/FieldsetRootContext.mjs
  var React30 = __toESM(require_react(), 1);
  var FieldsetRootContext = /* @__PURE__ */ React30.createContext(void 0);
  if (true) FieldsetRootContext.displayName = "FieldsetRootContext";
  function useFieldsetRootContext(optional = false) {
    const context = React30.useContext(FieldsetRootContext);
    if (!context && !optional) {
      throw new Error(true ? "Base UI: FieldsetRootContext is missing. Fieldset parts must be placed within <Fieldset.Root>." : formatErrorMessage_default(86));
    }
    return context;
  }

  // node_modules/@base-ui/react/internals/labelable-provider/LabelableProvider.mjs
  var React31 = __toESM(require_react(), 1);
  var import_jsx_runtime22 = __toESM(require_jsx_runtime(), 1);
  var LabelableProvider = function LabelableProvider2(props) {
    const defaultId = useBaseUiId();
    const [controlIdState, setControlIdState] = React31.useState(defaultId);
    const [labelId, setLabelId] = React31.useState();
    const [messageIds, setMessageIds] = React31.useState([]);
    const controlId = controlIdState === void 0 ? defaultId : controlIdState;
    const registrationsRef = useRefWithInit(() => /* @__PURE__ */ new Map());
    const {
      messageIds: parentMessageIds
    } = useLabelableContext();
    const registerControlId = useStableCallback((source, nextId) => {
      const registrations = registrationsRef.current;
      if (nextId === void 0) {
        registrations.delete(source);
      } else {
        registrations.set(source, nextId);
      }
      setControlIdState((prev) => {
        if (registrations.size === 0) {
          return prev;
        }
        let nextControlId;
        for (const id of registrations.values()) {
          if (id === prev) {
            return prev;
          }
          if (nextControlId === void 0) {
            nextControlId = id;
          }
        }
        return nextControlId;
      });
    });
    const resetControlId = useStableCallback(() => {
      if (registrationsRef.current.size === 0) {
        setControlIdState(defaultId);
      }
    });
    const getDescriptionProps = React31.useCallback((externalProps) => {
      const ids = externalProps["aria-describedby"] ? externalProps["aria-describedby"].split(" ") : [];
      ids.push(...parentMessageIds, ...messageIds);
      return {
        ...externalProps,
        "aria-describedby": Array.from(new Set(ids)).join(" ") || void 0
      };
    }, [parentMessageIds, messageIds]);
    const contextValue = React31.useMemo(() => ({
      controlId,
      registerControlId,
      resetControlId,
      labelId,
      setLabelId,
      messageIds,
      setMessageIds,
      getDescriptionProps
    }), [controlId, registerControlId, resetControlId, labelId, setLabelId, messageIds, setMessageIds, getDescriptionProps]);
    return /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(LabelableContext.Provider, {
      value: contextValue,
      children: props.children
    });
  };
  if (true) LabelableProvider.displayName = "LabelableProvider";

  // node_modules/@base-ui/react/internals/field-register-control/useFieldControlRegistration.mjs
  var React32 = __toESM(require_react(), 1);
  function useFieldControlRegistration(params) {
    const {
      change,
      commit,
      invalid: invalid2,
      markedDirtyRef,
      name: name16,
      setRegisteredFieldName,
      registeredFieldIdRef,
      setValidityData,
      validityData
    } = params;
    const {
      formRef
    } = useFormContext();
    const activeFieldControlSourceRef = React32.useRef(null);
    const registrationRef = React32.useRef(null);
    const initialValueCapturedRef = React32.useRef(false);
    const getValueForForm = useStableCallback(() => {
      const registration = registrationRef.current;
      if (!registration) {
        return void 0;
      }
      if (registration.getValue) {
        return registration.getValue();
      }
      return registration.value;
    });
    function getRegistrationValue(registration) {
      return registration.value === void 0 ? getValueForForm() : registration.value;
    }
    const validate = useStableCallback(() => {
      const registration = registrationRef.current;
      markedDirtyRef.current = true;
      if (!registration) {
        commit(validityData.value);
        return;
      }
      commit(getRegistrationValue(registration));
    });
    function refreshRegistration() {
      const registration = registrationRef.current;
      if (!registration || !registration.id) {
        return;
      }
      formRef.current.fields.set(registration.id, {
        getValue: getValueForForm,
        name: name16 ?? registration.name,
        controlRef: registration.controlRef,
        validityData: getCombinedFieldValidityData(validityData, invalid2),
        validate
      });
    }
    function deleteRegistration(id = registrationRef.current?.id) {
      if (id) {
        formRef.current.fields.delete(id);
      }
    }
    function captureInitialValue(registration) {
      if (initialValueCapturedRef.current) {
        return;
      }
      initialValueCapturedRef.current = true;
      const initialValue = getRegistrationValue(registration);
      setValidityData((prev) => prev.initialValue === initialValue ? prev : {
        ...prev,
        initialValue
      });
    }
    useIsoLayoutEffect(() => {
      const registration = registrationRef.current;
      if (!registration || !registration.id) {
        return;
      }
      setRegisteredFieldName(name16 ? void 0 : registration.name);
      formRef.current.fields.set(registration.id, {
        getValue: getValueForForm,
        name: name16 ?? registration.name,
        controlRef: registration.controlRef,
        validityData: getCombinedFieldValidityData(validityData, invalid2),
        validate
      });
    }, [formRef, getValueForForm, invalid2, name16, setRegisteredFieldName, validate, validityData]);
    useIsoLayoutEffect(() => {
      const fields = formRef.current.fields;
      return () => {
        const id = registrationRef.current?.id;
        if (id) {
          fields.delete(id);
        }
      };
    }, [formRef]);
    const register = useStableCallback((source, registration) => {
      if (!registration) {
        if (activeFieldControlSourceRef.current === source) {
          activeFieldControlSourceRef.current = null;
          change(void 0, true);
          deleteRegistration();
          registrationRef.current = null;
          setRegisteredFieldName(void 0);
          registeredFieldIdRef.current = void 0;
        }
        return;
      }
      const previousId = registrationRef.current?.id;
      const previousSource = activeFieldControlSourceRef.current;
      if (previousSource && previousSource !== source) {
        change(void 0, true);
      }
      activeFieldControlSourceRef.current = source;
      registrationRef.current = registration;
      if (!name16) {
        setRegisteredFieldName(registration.name);
      }
      registeredFieldIdRef.current = registration.id;
      if (previousId && previousId !== registration.id) {
        deleteRegistration(previousId);
      }
      captureInitialValue(registration);
      refreshRegistration();
    });
    return [validate, register];
  }

  // node_modules/@base-ui/react/field/root/FieldRoot.mjs
  var import_jsx_runtime23 = __toESM(require_jsx_runtime(), 1);
  var FieldRootInner = /* @__PURE__ */ React33.forwardRef(function FieldRootInner2(componentProps, forwardedRef) {
    const {
      errors,
      validationMode: formValidationMode,
      submitCountRef
    } = useFormContext();
    const {
      render,
      className,
      validate: validateProp,
      validationDebounceTime = 0,
      validationMode = formValidationMode,
      name: name16,
      disabled: disabledProp = false,
      invalid: invalidProp,
      dirty: dirtyProp,
      touched: touchedProp,
      actionsRef,
      style,
      ...elementProps
    } = componentProps;
    const disabledFieldset = useFieldsetRootContext(true)?.disabled;
    const validate = useStableCallback(validateProp || (() => null));
    const disabled2 = disabledFieldset || disabledProp;
    const [touchedState, setTouchedUnwrapped] = React33.useState(false);
    const [dirtyState, setDirtyUnwrapped] = React33.useState(false);
    const [filled, setFilled] = React33.useState(false);
    const [focused, setFocused] = React33.useState(false);
    const dirty = dirtyProp ?? dirtyState;
    const touched = touchedProp ?? touchedState;
    const markedDirtyRef = React33.useRef(dirty);
    const registeredFieldIdRef = React33.useRef(void 0);
    const [registeredFieldName, setRegisteredFieldName] = React33.useState();
    const effectiveName = name16 ?? registeredFieldName;
    useIsoLayoutEffect(() => {
      if (dirtyProp !== void 0) {
        markedDirtyRef.current = dirtyProp;
      }
    }, [dirtyProp]);
    const setDirty = useStableCallback((value) => {
      if (dirtyProp !== void 0) {
        return;
      }
      if (value) {
        markedDirtyRef.current = true;
      }
      setDirtyUnwrapped(value);
    });
    const setTouched = useStableCallback((value) => {
      if (touchedProp !== void 0) {
        return;
      }
      setTouchedUnwrapped(value);
    });
    const shouldValidateOnChange = useStableCallback(() => validationMode === "onChange" || validationMode === "onSubmit" && submitCountRef.current > 0);
    const formError = effectiveName && Object.hasOwn(errors, effectiveName) ? errors[effectiveName] : null;
    const hasFormError = !!(Array.isArray(formError) ? formError.length : formError);
    const invalid2 = invalidProp === true || hasFormError;
    const [validityData, setValidityData] = React33.useState({
      state: DEFAULT_VALIDITY_STATE,
      error: "",
      errors: [],
      value: null,
      initialValue: null
    });
    const valid2 = !invalid2 && (disabled2 ? null : validityData.state.valid);
    const state = React33.useMemo(() => ({
      disabled: disabled2,
      touched,
      dirty,
      valid: valid2,
      filled,
      focused
    }), [disabled2, touched, dirty, valid2, filled, focused]);
    const validation = useFieldValidation({
      setValidityData,
      validate,
      validityData,
      validationDebounceTime,
      invalid: invalid2,
      markedDirtyRef,
      state,
      shouldValidateOnChange,
      validationMode,
      registeredFieldIdRef
    });
    const [validateFieldControl, registerFieldControl] = useFieldControlRegistration({
      change: validation.change,
      commit: validation.commit,
      invalid: invalid2,
      markedDirtyRef,
      name: name16,
      setRegisteredFieldName,
      registeredFieldIdRef,
      setValidityData,
      validityData
    });
    React33.useImperativeHandle(actionsRef, () => ({
      validate: validateFieldControl
    }), [validateFieldControl]);
    const contextValue = React33.useMemo(() => ({
      invalid: invalid2,
      name: effectiveName,
      validityData,
      setValidityData,
      disabled: disabled2,
      setTouched,
      setDirty,
      setFilled,
      setFocused,
      validationMode,
      shouldValidateOnChange,
      state,
      registerFieldControl,
      validation
    }), [invalid2, effectiveName, validityData, disabled2, setTouched, setDirty, setFilled, setFocused, validationMode, shouldValidateOnChange, state, registerFieldControl, validation]);
    const element = useRenderElement("div", componentProps, {
      ref: forwardedRef,
      state,
      props: elementProps,
      stateAttributesMapping: fieldValidityMapping
    });
    return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(FieldRootContext.Provider, {
      value: contextValue,
      children: element
    });
  });
  if (true) FieldRootInner.displayName = "FieldRootInner";
  var FieldRoot = /* @__PURE__ */ React33.forwardRef(function FieldRoot2(componentProps, forwardedRef) {
    return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(LabelableProvider, {
      children: /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(FieldRootInner, {
        ...componentProps,
        ref: forwardedRef
      })
    });
  });
  if (true) FieldRoot.displayName = "FieldRoot";

  // node_modules/@base-ui/react/field/label/FieldLabel.mjs
  var React34 = __toESM(require_react(), 1);
  var FieldLabel = /* @__PURE__ */ React34.forwardRef(function FieldLabel2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      id: idProp,
      nativeLabel = true,
      ...elementProps
    } = componentProps;
    const fieldRootContext = useFieldRootContext(false);
    const fieldItemContext = useFieldItemContext();
    const {
      labelId
    } = useLabelableContext();
    const state = {
      ...fieldRootContext.state,
      disabled: fieldRootContext.disabled || fieldItemContext.disabled
    };
    const labelRef = React34.useRef(null);
    const labelProps = useLabel({
      id: labelId ?? idProp,
      native: nativeLabel
    });
    if (true) {
      React34.useEffect(() => {
        if (!labelRef.current) {
          return;
        }
        const isLabelTag = labelRef.current.tagName === "LABEL";
        if (nativeLabel) {
          if (!isLabelTag) {
            const ownerStackMessage = SafeReact.captureOwnerStack?.() || "";
            const message = "<Field.Label> expected a <label> element because the `nativeLabel` prop is true. Rendering a non-<label> disables native label association, so `htmlFor` will not work. Use a real <label> in the `render` prop, or set `nativeLabel` to `false`.";
            error(`${message}${ownerStackMessage}`);
          }
        } else if (isLabelTag) {
          const ownerStackMessage = SafeReact.captureOwnerStack?.() || "";
          const message = "<Field.Label> expected a non-<label> element because the `nativeLabel` prop is false. Rendering a <label> assumes native label behavior while Base UI treats it as non-native, which can cause unexpected pointer behavior. Use a non-<label> in the `render` prop, or set `nativeLabel` to `true`.";
          error(`${message}${ownerStackMessage}`);
        }
      }, [nativeLabel]);
    }
    const element = useRenderElement("label", componentProps, {
      ref: [forwardedRef, labelRef],
      state,
      props: [labelProps, elementProps],
      stateAttributesMapping: fieldValidityMapping
    });
    return element;
  });
  if (true) FieldLabel.displayName = "FieldLabel";

  // node_modules/@base-ui/react/field/error/FieldError.mjs
  var React35 = __toESM(require_react(), 1);
  var import_jsx_runtime24 = __toESM(require_jsx_runtime(), 1);
  var stateAttributesMapping = {
    ...fieldValidityMapping,
    ...transitionStatusMapping
  };
  var FieldError = /* @__PURE__ */ React35.forwardRef(function FieldError2(componentProps, forwardedRef) {
    const {
      render,
      id: idProp,
      className,
      match,
      style,
      ...elementProps
    } = componentProps;
    const id = useBaseUiId(idProp);
    const {
      validityData,
      state: fieldState,
      name: name16
    } = useFieldRootContext(false);
    const {
      setMessageIds
    } = useLabelableContext();
    const {
      errors
    } = useFormContext();
    const formError = name16 && Object.hasOwn(errors, name16) ? errors[name16] : null;
    const hasFormError = !!(Array.isArray(formError) ? formError.length : formError);
    const hasSpecificMatch = typeof match === "string";
    let rendered = false;
    if (match === true) {
      rendered = true;
    } else if (fieldState.disabled) {
      rendered = false;
    } else if (hasSpecificMatch) {
      rendered = Boolean(validityData.state[match]);
    } else {
      rendered = hasFormError || validityData.state.valid === false;
    }
    const {
      mounted,
      transitionStatus,
      setMounted
    } = useTransitionStatus(rendered);
    useIsoLayoutEffect(() => {
      if (!rendered || !id) {
        return void 0;
      }
      setMessageIds((v) => v.concat(id));
      return () => {
        setMessageIds((v) => v.filter((item) => item !== id));
      };
    }, [rendered, id, setMessageIds]);
    const errorRef = React35.useRef(null);
    const [lastRenderedMessage, setLastRenderedMessage] = React35.useState(null);
    const [lastRenderedMessageKey, setLastRenderedMessageKey] = React35.useState(null);
    let error2 = validityData.error;
    if (!hasSpecificMatch && hasFormError) {
      error2 = formError;
    } else if (validityData.errors.length > 1) {
      error2 = validityData.errors;
    }
    let errorMessage = error2;
    if (Array.isArray(error2)) {
      errorMessage = error2.length > 1 ? /* @__PURE__ */ (0, import_jsx_runtime24.jsx)("ul", {
        children: error2.map((message) => /* @__PURE__ */ (0, import_jsx_runtime24.jsx)("li", {
          children: message
        }, message))
      }) : error2[0];
    }
    const errorKey = Array.isArray(error2) ? JSON.stringify(error2) : error2;
    if (rendered && errorKey !== lastRenderedMessageKey) {
      setLastRenderedMessageKey(errorKey);
      setLastRenderedMessage(errorMessage);
    }
    useOpenChangeComplete({
      open: rendered,
      ref: errorRef,
      onComplete() {
        if (!rendered) {
          setMounted(false);
        }
      }
    });
    const state = {
      ...fieldState,
      transitionStatus
    };
    const element = useRenderElement("div", componentProps, {
      ref: [forwardedRef, errorRef],
      state,
      props: [{
        id,
        children: rendered ? errorMessage : lastRenderedMessage
      }, elementProps],
      stateAttributesMapping,
      enabled: mounted
    });
    if (!mounted) {
      return null;
    }
    return element;
  });
  if (true) FieldError.displayName = "FieldError";

  // node_modules/@base-ui/react/field/description/FieldDescription.mjs
  var React36 = __toESM(require_react(), 1);
  var FieldDescription = /* @__PURE__ */ React36.forwardRef(function FieldDescription2(componentProps, forwardedRef) {
    const {
      render,
      id: idProp,
      className,
      style,
      ...elementProps
    } = componentProps;
    const id = useBaseUiId(idProp);
    const fieldRootContext = useFieldRootContext(false);
    const fieldItemContext = useFieldItemContext();
    const {
      setMessageIds
    } = useLabelableContext();
    const state = {
      ...fieldRootContext.state,
      disabled: fieldRootContext.disabled || fieldItemContext.disabled
    };
    useIsoLayoutEffect(() => {
      if (!id) {
        return void 0;
      }
      setMessageIds((v) => v.concat(id));
      return () => {
        setMessageIds((v) => v.filter((item) => item !== id));
      };
    }, [id, setMessageIds]);
    const element = useRenderElement("p", componentProps, {
      ref: forwardedRef,
      state,
      props: [{
        id
      }, elementProps],
      stateAttributesMapping: fieldValidityMapping
    });
    return element;
  });
  if (true) FieldDescription.displayName = "FieldDescription";

  // node_modules/@base-ui/react/field/control/FieldControl.mjs
  var React37 = __toESM(require_react(), 1);
  var FieldControl = /* @__PURE__ */ React37.forwardRef(function FieldControl2(componentProps, forwardedRef) {
    const {
      render,
      className,
      id: idProp,
      name: nameProp,
      value: valueProp,
      disabled: disabledProp = false,
      onValueChange,
      defaultValue,
      autoFocus = false,
      style,
      ...elementProps
    } = componentProps;
    const {
      state: fieldState,
      name: fieldName,
      disabled: fieldDisabled,
      setTouched,
      setDirty,
      validityData,
      setFocused,
      setFilled,
      validationMode,
      validation
    } = useFieldRootContext();
    const {
      clearErrors,
      elementRef: formElementRef,
      submitCountRef
    } = useFormContext();
    const disabled2 = fieldDisabled || disabledProp;
    const name16 = fieldName ?? nameProp;
    const state = {
      ...fieldState,
      disabled: disabled2
    };
    const {
      labelId
    } = useLabelableContext();
    const id = useLabelableId({
      id: idProp
    });
    const [valueUnwrapped] = useControlled({
      controlled: valueProp,
      default: defaultValue,
      name: "FieldControl",
      state: "value"
    });
    const isControlled = valueProp !== void 0;
    const value = isControlled ? valueUnwrapped : void 0;
    const serializedValue = value == null ? void 0 : String(value);
    const getValueFromInput = useStableCallback(() => validation.inputRef.current?.value);
    useRegisterFieldControl(validation.inputRef, id, serializedValue, getValueFromInput, !disabled2, nameProp);
    useIsoLayoutEffect(() => {
      const currentValue = serializedValue ?? validation.inputRef.current?.value;
      if (currentValue !== void 0) {
        setFilled(currentValue !== "");
      }
    }, [serializedValue, validation.inputRef, setFilled]);
    useValueChanged(serializedValue, () => {
      if (serializedValue === void 0) {
        return;
      }
      clearErrors(name16);
      setDirty(serializedValue !== (validityData.initialValue ?? ""));
      validation.change(serializedValue);
    });
    const inputRef = React37.useRef(null);
    const enterValidationTimeout = useTimeout();
    useIsoLayoutEffect(() => {
      if (autoFocus && inputRef.current === activeElement(ownerDocument(inputRef.current))) {
        setFocused(true);
      }
    }, [autoFocus, setFocused]);
    const element = useRenderElement("input", componentProps, {
      ref: [forwardedRef, inputRef],
      state,
      props: [{
        id,
        disabled: disabled2,
        name: name16,
        ref: validation.inputRef,
        "aria-labelledby": labelId,
        autoFocus,
        ...isControlled ? {
          value
        } : {
          defaultValue
        },
        onChange(event) {
          const inputValue = event.currentTarget.value;
          const details = createChangeEventDetails(reason_parts_exports.none, event.nativeEvent);
          onValueChange?.(inputValue, details);
          if (isControlled) {
            return;
          }
          setDirty(inputValue !== (validityData.initialValue ?? ""));
          setFilled(inputValue !== "");
          if (!event.nativeEvent.defaultPrevented && !details.isCanceled) {
            clearErrors(name16);
            validation.change(inputValue);
          }
        },
        onFocus() {
          setFocused(true);
        },
        onBlur(event) {
          setTouched(true);
          setFocused(false);
          if (validationMode === "onBlur") {
            const inputValue = event.currentTarget.value;
            validation.commit(inputValue);
            if (isControlled) {
              queueMicrotask(() => {
                const nextValue = validation.inputRef.current?.value;
                if (nextValue !== void 0 && nextValue !== inputValue && nextValue !== (validityData.initialValue ?? "")) {
                  validation.commit(nextValue);
                }
              });
            }
          }
        },
        onKeyDown(event) {
          if (event.currentTarget.tagName === "INPUT" && event.key === "Enter") {
            setTouched(true);
            const value2 = event.currentTarget.value;
            const form = event.currentTarget.form;
            if (form && form === formElementRef.current && !event.defaultPrevented) {
              const input = event.currentTarget;
              const submitCount = submitCountRef.current;
              enterValidationTimeout.start(0, () => {
                if (submitCountRef.current === submitCount) {
                  validation.commit(input.value);
                }
              });
            } else {
              validation.commit(value2);
            }
          }
        }
      }, elementProps, (props) => validation.getValidationProps(disabled2, props)],
      stateAttributesMapping: fieldValidityMapping
    });
    return element;
  });
  if (true) FieldControl.displayName = "FieldControl";

  // node_modules/@base-ui/react/field/validity/FieldValidity.mjs
  var React38 = __toESM(require_react(), 1);
  var import_jsx_runtime25 = __toESM(require_jsx_runtime(), 1);
  var FieldValidity = function FieldValidity2(props) {
    const {
      children
    } = props;
    const {
      validityData,
      invalid: invalid2
    } = useFieldRootContext(false);
    const combinedFieldValidityData = React38.useMemo(() => getCombinedFieldValidityData(validityData, invalid2), [validityData, invalid2]);
    const isInvalid = combinedFieldValidityData.state.valid === false;
    const {
      transitionStatus
    } = useTransitionStatus(isInvalid);
    const fieldValidityState = React38.useMemo(() => {
      return {
        ...combinedFieldValidityData,
        validity: combinedFieldValidityData.state,
        transitionStatus
      };
    }, [combinedFieldValidityData, transitionStatus]);
    return /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(React38.Fragment, {
      children: children(fieldValidityState)
    });
  };
  if (true) FieldValidity.displayName = "FieldValidity";

  // node_modules/@base-ui/react/field/item/FieldItem.mjs
  var React39 = __toESM(require_react(), 1);
  var import_jsx_runtime26 = __toESM(require_jsx_runtime(), 1);
  var FieldItem = /* @__PURE__ */ React39.forwardRef(function FieldItem2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      disabled: disabledProp = false,
      ...elementProps
    } = componentProps;
    const {
      state: fieldState,
      disabled: rootDisabled
    } = useFieldRootContext(false);
    const disabled2 = rootDisabled || disabledProp;
    const state = {
      ...fieldState,
      disabled: disabled2
    };
    const fieldItemContext = React39.useMemo(() => ({
      disabled: disabled2
    }), [disabled2]);
    const element = useRenderElement("div", componentProps, {
      ref: forwardedRef,
      state,
      props: elementProps,
      stateAttributesMapping: fieldValidityMapping
    });
    return /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(LabelableProvider, {
      children: /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(FieldItemContext.Provider, {
        value: fieldItemContext,
        children: element
      })
    });
  });
  if (true) FieldItem.displayName = "FieldItem";

  // node_modules/@base-ui/react/input/Input.mjs
  var React40 = __toESM(require_react(), 1);
  var import_jsx_runtime27 = __toESM(require_jsx_runtime(), 1);
  var Input = /* @__PURE__ */ React40.forwardRef(function Input2(props, forwardedRef) {
    return /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(index_parts_exports.Control, {
      ref: forwardedRef,
      ...props
    });
  });
  if (true) Input.displayName = "Input";

  // node_modules/@base-ui/react/internals/composite/root/CompositeRoot.mjs
  var React42 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/internals/composite/root/useCompositeRoot.mjs
  var React41 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/internals/composite/constants.mjs
  var ACTIVE_COMPOSITE_ITEM = "data-composite-item-active";

  // node_modules/@base-ui/react/internals/composite/root/useCompositeRoot.mjs
  function useCompositeRoot(params) {
    const {
      loopFocus = true,
      orientation = "both",
      grid,
      onLoop,
      direction,
      highlightedIndex: externalHighlightedIndex,
      onHighlightedIndexChange: externalSetHighlightedIndex,
      rootRef: externalRef,
      enableHomeAndEndKeys = false,
      stopEventPropagation,
      disabledIndices,
      modifierKeys = EMPTY_ARRAY
    } = params;
    const [internalHighlightedIndex, internalSetHighlightedIndex] = React41.useState(0);
    const isGrid = grid != null;
    const rootRef = React41.useRef(null);
    const mergedRef = useMergedRefs(rootRef, externalRef);
    const elementsRef = React41.useRef([]);
    const hasSetDefaultIndexRef = React41.useRef(false);
    const highlightedElementRef = React41.useRef(null);
    const highlightedIndex = externalHighlightedIndex ?? internalHighlightedIndex;
    const onHighlightedIndexChange = useStableCallback((index2, shouldScrollIntoView = false) => {
      highlightedElementRef.current = elementsRef.current[index2] ?? null;
      (externalSetHighlightedIndex ?? internalSetHighlightedIndex)(index2);
      if (shouldScrollIntoView) {
        const newActiveItem = elementsRef.current[index2];
        scrollIntoViewIfNeeded(rootRef.current, newActiveItem, direction, orientation);
      }
    });
    const onMapChange = useStableCallback((map) => {
      if (map.size === 0) {
        return;
      }
      if (hasSetDefaultIndexRef.current) {
        const elements = elementsRef.current;
        const nextIndex = elements.indexOf(highlightedElementRef.current);
        if (nextIndex === -1) {
          const replacement = elements[highlightedIndex];
          if (!replacement || isListIndexDisabled(elements, highlightedIndex, disabledIndices)) {
            onHighlightedIndexChange(getFallbackIndex(elements, disabledIndices));
          } else {
            highlightedElementRef.current = replacement;
          }
        } else if (nextIndex !== highlightedIndex) {
          onHighlightedIndexChange(nextIndex);
        }
        return;
      }
      hasSetDefaultIndexRef.current = true;
      const sortedElements = Array.from(map.keys());
      const activeItem = sortedElements.find((compositeElement) => compositeElement?.hasAttribute(ACTIVE_COMPOSITE_ITEM)) ?? null;
      const activeIndex = activeItem ? map.get(activeItem)?.index ?? -1 : -1;
      if (activeIndex !== -1) {
        onHighlightedIndexChange(activeIndex);
      } else if (isListIndexDisabled(sortedElements, highlightedIndex, disabledIndices)) {
        const firstEnabledIndex = findNonDisabledListIndex(sortedElements, {
          disabledIndices
        });
        if (!isIndexOutOfListBounds(sortedElements, firstEnabledIndex)) {
          onHighlightedIndexChange(firstEnabledIndex);
        }
      }
      scrollIntoViewIfNeeded(rootRef.current, activeItem, direction, orientation);
    });
    useIsoLayoutEffect(() => {
      if (disabledIndices == null || externalHighlightedIndex != null || !hasSetDefaultIndexRef.current) {
        return;
      }
      const elements = elementsRef.current;
      if (isListIndexDisabled(elements, highlightedIndex, disabledIndices)) {
        const firstEnabledIndex = findNonDisabledListIndex(elements, {
          disabledIndices
        });
        if (!isIndexOutOfListBounds(elements, firstEnabledIndex)) {
          onHighlightedIndexChange(firstEnabledIndex);
        }
      }
    }, [disabledIndices, externalHighlightedIndex, highlightedIndex, elementsRef, onHighlightedIndexChange]);
    const wrappedOnLoop = useStableCallback((event, prevIndex, nextIndex) => {
      if (!onLoop) {
        return nextIndex;
      }
      return onLoop(event, prevIndex, nextIndex, elementsRef);
    });
    const onKeyDown = useStableCallback((event) => {
      const isHomeOrEnd = event.key === HOME || event.key === END;
      if (!COMPOSITE_KEYS.has(event.key) || !enableHomeAndEndKeys && isHomeOrEnd) {
        return;
      }
      if (isModifierKeySet(event, modifierKeys)) {
        return;
      }
      const element = rootRef.current;
      if (!element) {
        return;
      }
      const isRtl = direction === "rtl";
      const horizontalForwardKey = isRtl ? ARROW_LEFT : ARROW_RIGHT;
      const horizontalBackwardKey = isRtl ? ARROW_RIGHT : ARROW_LEFT;
      const forwardKey = orientation === "vertical" ? ARROW_DOWN : horizontalForwardKey;
      const backwardKey = orientation === "vertical" ? ARROW_UP : horizontalBackwardKey;
      const target = getTarget(event.nativeEvent);
      if (target != null && isNativeInput(target) && !isElementDisabled(target)) {
        const selectionStart = target.selectionStart;
        const selectionEnd = target.selectionEnd;
        const textContent = target.value;
        if (selectionStart == null || event.shiftKey || selectionStart !== selectionEnd) {
          return;
        }
        if (event.key !== backwardKey && selectionStart < textContent.length) {
          return;
        }
        if (event.key !== forwardKey && selectionStart > 0) {
          return;
        }
      }
      let nextIndex = highlightedIndex;
      const minIndex = getMinListIndex(elementsRef, disabledIndices);
      const maxIndex = getMaxListIndex(elementsRef, disabledIndices);
      if (grid != null) {
        nextIndex = grid({
          disabledIndices,
          elementsRef,
          event,
          highlightedIndex,
          loopFocus,
          maxIndex,
          minIndex,
          onLoop: wrappedOnLoop,
          orientation,
          rtl: isRtl
        });
      }
      const isForwardKey = orientation !== "vertical" && event.key === horizontalForwardKey || orientation !== "horizontal" && event.key === ARROW_DOWN;
      const isBackwardKey = orientation !== "vertical" && event.key === horizontalBackwardKey || orientation !== "horizontal" && event.key === ARROW_UP;
      if (enableHomeAndEndKeys) {
        if (event.key === HOME) {
          nextIndex = minIndex;
        } else if (event.key === END) {
          nextIndex = maxIndex;
        }
      }
      if (nextIndex === highlightedIndex && (isForwardKey || isBackwardKey)) {
        if (loopFocus && nextIndex === maxIndex && isForwardKey) {
          nextIndex = minIndex;
          if (onLoop) {
            nextIndex = onLoop(event, highlightedIndex, nextIndex, elementsRef);
          }
        } else if (loopFocus && nextIndex === minIndex && isBackwardKey) {
          nextIndex = maxIndex;
          if (onLoop) {
            nextIndex = onLoop(event, highlightedIndex, nextIndex, elementsRef);
          }
        } else {
          nextIndex = findNonDisabledListIndex(elementsRef.current, {
            startingIndex: nextIndex,
            decrement: isBackwardKey,
            disabledIndices
          });
        }
      }
      if (nextIndex !== highlightedIndex && !isIndexOutOfListBounds(elementsRef.current, nextIndex)) {
        if (stopEventPropagation) {
          event.stopPropagation();
        }
        if (isGrid || isHomeOrEnd || isForwardKey || isBackwardKey) {
          event.preventDefault();
        }
        onHighlightedIndexChange(nextIndex, true);
        queueMicrotask(() => {
          elementsRef.current[nextIndex]?.focus();
        });
      }
    });
    const props = {
      ref: mergedRef,
      onFocus(event) {
        const element = rootRef.current;
        const target = getTarget(event.nativeEvent);
        if (!element || target == null || !isNativeInput(target)) {
          return;
        }
        target.setSelectionRange(0, target.value.length);
      },
      onKeyDown
    };
    return {
      props,
      highlightedIndex,
      onHighlightedIndexChange,
      elementsRef,
      onMapChange,
      relayKeyboardEvent: onKeyDown
    };
  }
  function getFallbackIndex(elements, disabledIndices) {
    let fallbackIndex = -1;
    for (let index2 = 0; index2 < elements.length; index2 += 1) {
      const element = elements[index2];
      if (!element || isListIndexDisabled(elements, index2, disabledIndices)) {
        continue;
      }
      if (element.hasAttribute(ACTIVE_COMPOSITE_ITEM)) {
        return index2;
      }
      if (fallbackIndex === -1) {
        fallbackIndex = index2;
      }
    }
    return Math.max(fallbackIndex, 0);
  }
  function isModifierKeySet(event, ignoredModifierKeys) {
    for (const key of MODIFIER_KEYS) {
      if (ignoredModifierKeys.includes(key)) {
        continue;
      }
      if (event.getModifierState(key)) {
        return true;
      }
    }
    return false;
  }

  // node_modules/@base-ui/react/internals/composite/root/CompositeRoot.mjs
  var import_jsx_runtime28 = __toESM(require_jsx_runtime(), 1);
  function CompositeRoot(componentProps) {
    const {
      render,
      className,
      style,
      refs = EMPTY_ARRAY,
      props = EMPTY_ARRAY,
      state = EMPTY_OBJECT,
      stateAttributesMapping: stateAttributesMapping4,
      highlightedIndex: highlightedIndexProp,
      onHighlightedIndexChange: onHighlightedIndexChangeProp,
      orientation,
      grid,
      loopFocus,
      onLoop,
      enableHomeAndEndKeys,
      onMapChange: onMapChangeProp,
      stopEventPropagation = true,
      rootRef,
      disabledIndices,
      modifierKeys,
      highlightItemOnHover = false,
      tag = "div",
      ...elementProps
    } = componentProps;
    const direction = useDirection();
    const {
      props: defaultProps,
      highlightedIndex,
      onHighlightedIndexChange,
      elementsRef,
      onMapChange: onMapChangeUnwrapped,
      relayKeyboardEvent
    } = useCompositeRoot({
      grid,
      loopFocus,
      onLoop,
      orientation,
      highlightedIndex: highlightedIndexProp,
      onHighlightedIndexChange: onHighlightedIndexChangeProp,
      rootRef,
      stopEventPropagation,
      enableHomeAndEndKeys,
      direction,
      disabledIndices,
      modifierKeys
    });
    const element = useRenderElement(tag, componentProps, {
      state,
      ref: refs,
      props: [defaultProps, ...props, elementProps],
      stateAttributesMapping: stateAttributesMapping4
    });
    const contextValue = React42.useMemo(() => ({
      highlightedIndex,
      onHighlightedIndexChange,
      highlightItemOnHover,
      relayKeyboardEvent
    }), [highlightedIndex, onHighlightedIndexChange, highlightItemOnHover, relayKeyboardEvent]);
    return /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(CompositeRootContext.Provider, {
      value: contextValue,
      children: /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(CompositeList, {
        elementsRef,
        onMapChange: (newMap) => {
          onMapChangeProp?.(newMap);
          onMapChangeUnwrapped(newMap);
        },
        children: element
      })
    });
  }

  // node_modules/@base-ui/react/internals/prehydrationScript.stub.mjs
  var script = "";

  // node_modules/@base-ui/react/utils/useIsHydrating.mjs
  var import_shim = __toESM(require_shim(), 1);
  function subscribe() {
    return NOOP;
  }
  function getSnapshot() {
    return false;
  }
  function getServerSnapshot() {
    return true;
  }
  function useIsHydrating() {
    return (0, import_shim.useSyncExternalStore)(subscribe, getSnapshot, getServerSnapshot);
  }

  // node_modules/@base-ui/react/internals/PrehydrationScript.mjs
  var React43 = __toESM(require_react(), 1);
  var import_jsx_runtime29 = __toESM(require_jsx_runtime(), 1);
  function PrehydrationScript(props) {
    const {
      script: script2
    } = props;
    const {
      nonce
    } = useCSPContext();
    const isHydrating = useIsHydrating();
    if (!isHydrating) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)("script", {
      nonce,
      dangerouslySetInnerHTML: {
        __html: script2
      },
      suppressHydrationWarning: true
    });
  }

  // node_modules/@base-ui/react/tabs/index.parts.mjs
  var index_parts_exports2 = {};
  __export(index_parts_exports2, {
    Indicator: () => TabsIndicator,
    List: () => TabsList,
    Panel: () => TabsPanel,
    Root: () => TabsRoot,
    Tab: () => TabsTab
  });

  // node_modules/@base-ui/react/tabs/root/TabsRoot.mjs
  var React45 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/tabs/root/TabsRootContext.mjs
  var React44 = __toESM(require_react(), 1);
  var TabsRootContext = /* @__PURE__ */ React44.createContext(void 0);
  if (true) TabsRootContext.displayName = "TabsRootContext";
  function useTabsRootContext() {
    const context = React44.useContext(TabsRootContext);
    if (context === void 0) {
      throw new Error(true ? "Base UI: TabsRootContext is missing. Tabs parts must be placed within <Tabs.Root>." : formatErrorMessage_default(64));
    }
    return context;
  }

  // node_modules/@base-ui/react/tabs/root/TabsRootDataAttributes.mjs
  var activationDirection = "data-activation-direction";

  // node_modules/@base-ui/react/tabs/root/stateAttributesMapping.mjs
  var tabsStateAttributesMapping = {
    tabActivationDirection: (dir) => ({
      [activationDirection]: dir
    })
  };

  // node_modules/@base-ui/react/tabs/root/TabsRoot.mjs
  var import_jsx_runtime30 = __toESM(require_jsx_runtime(), 1);
  var TabsRoot = /* @__PURE__ */ React45.forwardRef(function TabsRoot2(componentProps, forwardedRef) {
    const {
      className,
      defaultValue: defaultValueProp = 0,
      onValueChange: onValueChangeProp,
      orientation = "horizontal",
      render,
      value: valueProp,
      style,
      ...elementProps
    } = componentProps;
    const hasExplicitDefaultValueProp = componentProps.defaultValue !== void 0;
    const tabPanelRefs = React45.useRef([]);
    const [mountedTabPanels, setMountedTabPanels] = React45.useState(() => /* @__PURE__ */ new Map());
    const [value, setValue] = useControlled({
      controlled: valueProp,
      default: defaultValueProp,
      name: "Tabs",
      state: "value"
    });
    const isControlled = valueProp !== void 0;
    const [tabMap, setTabMap] = React45.useState(() => /* @__PURE__ */ new Map());
    const lastKnownTabElementRef = React45.useRef(void 0);
    const getTabElementBySelectedValue = React45.useCallback((selectedValue) => findTabElement(tabMap, selectedValue), [tabMap]);
    const [activationDirectionState, setActivationDirectionState] = React45.useState(() => ({
      previousValue: value,
      tabActivationDirection: "none"
    }));
    const {
      previousValue,
      tabActivationDirection: committedTabActivationDirection
    } = activationDirectionState;
    let tabActivationDirection = committedTabActivationDirection;
    let directionComputationIncomplete = false;
    if (previousValue !== value) {
      tabActivationDirection = computeActivationDirection(previousValue, value, orientation, tabMap);
      directionComputationIncomplete = previousValue != null && value != null && getTabElementBySelectedValue(value) == null;
    }
    const nextPreviousValue = directionComputationIncomplete ? previousValue : value;
    const shouldSyncActivationDirectionState = previousValue !== nextPreviousValue || committedTabActivationDirection !== tabActivationDirection;
    useIsoLayoutEffect(() => {
      if (!shouldSyncActivationDirectionState) {
        return;
      }
      setActivationDirectionState({
        previousValue: nextPreviousValue,
        tabActivationDirection
      });
    }, [nextPreviousValue, shouldSyncActivationDirectionState, tabActivationDirection]);
    const onValueChange = useStableCallback((newValue, eventDetails) => {
      const activationDirection2 = computeActivationDirection(value, newValue, orientation, tabMap);
      eventDetails.activationDirection = activationDirection2;
      onValueChangeProp?.(newValue, eventDetails);
      if (eventDetails.isCanceled) {
        return;
      }
      setValue(newValue);
    });
    const notifyAutomaticValueChange = useStableCallback((nextValue, reason) => {
      onValueChangeProp?.(nextValue, createChangeEventDetails(reason, void 0, void 0, {
        activationDirection: "none"
      }));
    });
    const registerMountedTabPanel = useStableCallback((panelValue, panelId) => {
      setMountedTabPanels((prev) => {
        const next = new Map(prev);
        next.set(panelValue, panelId);
        return next;
      });
      return () => {
        setMountedTabPanels((prev) => {
          if (prev.get(panelValue) !== panelId) {
            return prev;
          }
          const next = new Map(prev);
          next.delete(panelValue);
          return next;
        });
      };
    });
    const getTabPanelIdByValue = React45.useCallback((tabValue) => {
      return mountedTabPanels.get(tabValue);
    }, [mountedTabPanels]);
    const getTabIdByPanelValue = React45.useCallback((tabPanelValue) => {
      for (const tabMetadata of tabMap.values()) {
        if (tabPanelValue === tabMetadata.value) {
          return tabMetadata.id;
        }
      }
      return void 0;
    }, [tabMap]);
    const tabsContextValue = React45.useMemo(() => ({
      getTabElementBySelectedValue,
      getTabIdByPanelValue,
      getTabPanelIdByValue,
      onValueChange,
      orientation,
      registerMountedTabPanel,
      setTabMap,
      tabActivationDirection,
      value
    }), [getTabElementBySelectedValue, getTabIdByPanelValue, getTabPanelIdByValue, onValueChange, orientation, registerMountedTabPanel, setTabMap, tabActivationDirection, value]);
    const selectedTabMetadata = React45.useMemo(() => {
      for (const tabMetadata of tabMap.values()) {
        if (tabMetadata.value === value) {
          return tabMetadata;
        }
      }
      return void 0;
    }, [tabMap, value]);
    const firstEnabledTabValue = React45.useMemo(() => {
      for (const tabMetadata of tabMap.values()) {
        if (!tabMetadata.disabled) {
          return tabMetadata.value;
        }
      }
      return void 0;
    }, [tabMap]);
    const shouldNotifyInitialValueChangeRef = React45.useRef(!hasExplicitDefaultValueProp);
    const initialDefaultValueRef = React45.useRef(defaultValueProp);
    const shouldHonorDisabledDefaultValueRef = React45.useRef(hasExplicitDefaultValueProp);
    const didRegisterTabsRef = React45.useRef(false);
    useIsoLayoutEffect(() => {
      if (isControlled) {
        return;
      }
      function commitAutomaticValueChange(fallbackValue, fallbackReason) {
        setValue(fallbackValue);
        setActivationDirectionState({
          previousValue: fallbackValue,
          tabActivationDirection: "none"
        });
        notifyAutomaticValueChange(fallbackValue, fallbackReason);
        shouldNotifyInitialValueChangeRef.current = false;
      }
      if (tabMap.size === 0) {
        if (didRegisterTabsRef.current && value !== null && !lastKnownTabElementRef.current?.isConnected) {
          commitAutomaticValueChange(null, reason_parts_exports.missing);
        }
        return;
      }
      didRegisterTabsRef.current = true;
      lastKnownTabElementRef.current = tabMap.keys().next().value;
      const selectionIsDisabled = selectedTabMetadata?.disabled;
      const selectionIsMissing = selectedTabMetadata == null && value !== null;
      if (!selectionIsDisabled && value === initialDefaultValueRef.current) {
        shouldHonorDisabledDefaultValueRef.current = false;
      }
      if (shouldHonorDisabledDefaultValueRef.current && selectionIsDisabled && value === initialDefaultValueRef.current) {
        return;
      }
      const shouldNotifyInitialValueChange = shouldNotifyInitialValueChangeRef.current;
      if (selectionIsDisabled || selectionIsMissing) {
        const fallbackValue = firstEnabledTabValue ?? null;
        if (value === fallbackValue) {
          shouldNotifyInitialValueChangeRef.current = false;
          return;
        }
        let fallbackReason = reason_parts_exports.missing;
        if (shouldNotifyInitialValueChange) {
          fallbackReason = reason_parts_exports.initial;
        } else if (selectionIsDisabled) {
          fallbackReason = reason_parts_exports.disabled;
        }
        commitAutomaticValueChange(fallbackValue, fallbackReason);
        return;
      }
      if (shouldNotifyInitialValueChange && selectedTabMetadata != null) {
        notifyAutomaticValueChange(value, reason_parts_exports.initial);
        shouldNotifyInitialValueChangeRef.current = false;
      }
    }, [firstEnabledTabValue, isControlled, notifyAutomaticValueChange, selectedTabMetadata, setValue, tabMap, value]);
    const state = {
      orientation,
      tabActivationDirection
    };
    const element = useRenderElement("div", componentProps, {
      state,
      ref: forwardedRef,
      props: elementProps,
      stateAttributesMapping: tabsStateAttributesMapping
    });
    return /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(TabsRootContext.Provider, {
      value: tabsContextValue,
      children: /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(CompositeList, {
        elementsRef: tabPanelRefs,
        children: element
      })
    });
  });
  if (true) TabsRoot.displayName = "TabsRoot";
  function findTabElement(tabMap, value) {
    for (const [tabElement, tabMetadata] of tabMap.entries()) {
      if (value === tabMetadata.value) {
        return tabElement;
      }
    }
    return null;
  }
  function computeActivationDirection(oldValue, newValue, orientation, tabMap) {
    if (oldValue == null || newValue == null) {
      return "none";
    }
    const [positionProp, backward, forward] = orientation === "horizontal" ? ["left", "left", "right"] : ["top", "up", "down"];
    const oldTab = findTabElement(tabMap, oldValue);
    const newTab = findTabElement(tabMap, newValue);
    if (oldTab == null || newTab == null) {
      if (oldTab !== newTab && (typeof oldValue === "number" || typeof oldValue === "string") && typeof oldValue === typeof newValue) {
        return newValue > oldValue ? forward : backward;
      }
      return "none";
    }
    const oldPosition = oldTab.getBoundingClientRect()[positionProp];
    const newPosition = newTab.getBoundingClientRect()[positionProp];
    if (newPosition < oldPosition) {
      return backward;
    }
    if (newPosition > oldPosition) {
      return forward;
    }
    return "none";
  }

  // node_modules/@base-ui/react/tabs/tab/TabsTab.mjs
  var React47 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/tabs/list/TabsListContext.mjs
  var React46 = __toESM(require_react(), 1);
  var TabsListContext = /* @__PURE__ */ React46.createContext(void 0);
  if (true) TabsListContext.displayName = "TabsListContext";
  function useTabsListContext() {
    const context = React46.useContext(TabsListContext);
    if (context === void 0) {
      throw new Error(true ? "Base UI: TabsListContext is missing. TabsList parts must be placed within <Tabs.List>." : formatErrorMessage_default(65));
    }
    return context;
  }

  // node_modules/@base-ui/react/tabs/tab/TabsTab.mjs
  var TabsTab = /* @__PURE__ */ React47.forwardRef(function TabsTab2(componentProps, forwardedRef) {
    const {
      className,
      disabled: disabled2 = false,
      render,
      value,
      id: idProp,
      nativeButton = true,
      style,
      ...elementProps
    } = componentProps;
    const {
      value: activeTabValue,
      getTabPanelIdByValue,
      onValueChange,
      orientation,
      tabActivationDirection
    } = useTabsRootContext();
    const {
      activateOnFocus,
      registerTabResizeObserverElement,
      tabsListElement
    } = useTabsListContext();
    const {
      highlightedIndex,
      onHighlightedIndexChange
    } = useCompositeRootContext();
    const id = useBaseUiId(idProp);
    const tabMetadata = React47.useMemo(() => ({
      disabled: disabled2,
      id,
      value
    }), [disabled2, id, value]);
    const {
      compositeProps,
      compositeRef,
      index: index2
      // hook is used instead of the CompositeItem component
      // because the index is needed for Tab internals
    } = useCompositeItem({
      metadata: tabMetadata
    });
    const active = value === activeTabValue;
    const isNavigatingRef = React47.useRef(false);
    const unobserveTabElementRef = React47.useRef(null);
    const observeTabElement = useStableCallback((element2) => {
      unobserveTabElementRef.current?.();
      unobserveTabElementRef.current = element2 ? registerTabResizeObserverElement(element2) : null;
    });
    useIsoLayoutEffect(() => {
      if (isNavigatingRef.current) {
        isNavigatingRef.current = false;
        return;
      }
      if (!(active && index2 > -1 && highlightedIndex !== index2)) {
        return;
      }
      const listElement = tabsListElement;
      if (listElement != null) {
        const activeEl = activeElement(ownerDocument(listElement));
        if (activeEl && contains(listElement, activeEl)) {
          return;
        }
      }
      if (!disabled2) {
        onHighlightedIndexChange(index2);
      }
    }, [active, index2, highlightedIndex, onHighlightedIndexChange, disabled2, tabsListElement]);
    const {
      getButtonProps,
      buttonRef
    } = useButton({
      disabled: disabled2,
      native: nativeButton,
      focusableWhenDisabled: true
    });
    const tabPanelId = getTabPanelIdByValue(value);
    const isPressingRef = React47.useRef(false);
    const isMainButtonRef = React47.useRef(false);
    function activate(event) {
      onValueChange(value, createChangeEventDetails(reason_parts_exports.none, event.nativeEvent, void 0, {
        activationDirection: "none"
      }));
    }
    function onClick(event) {
      if (active || disabled2) {
        return;
      }
      activate(event);
    }
    function onFocus(event) {
      if (active || disabled2) {
        return;
      }
      if (activateOnFocus && (!isPressingRef.current || // keyboard or touch focus
      isMainButtonRef.current)) {
        activate(event);
      }
    }
    function onPointerDown(event) {
      if (active || disabled2) {
        return;
      }
      isPressingRef.current = true;
      isMainButtonRef.current = event.button === 0;
      const doc = ownerDocument(event.currentTarget);
      function handlePointerEnd() {
        isPressingRef.current = false;
        isMainButtonRef.current = false;
        doc.removeEventListener("pointerup", handlePointerEnd);
        doc.removeEventListener("pointercancel", handlePointerEnd);
      }
      doc.addEventListener("pointerup", handlePointerEnd);
      doc.addEventListener("pointercancel", handlePointerEnd);
    }
    const state = {
      disabled: disabled2,
      active,
      orientation,
      tabActivationDirection
    };
    const element = useRenderElement("button", componentProps, {
      state,
      ref: [forwardedRef, buttonRef, compositeRef, observeTabElement],
      props: [compositeProps, {
        role: "tab",
        "aria-controls": tabPanelId,
        "aria-selected": active,
        id,
        onClick,
        onFocus,
        onPointerDown,
        [ACTIVE_COMPOSITE_ITEM]: active ? "" : void 0,
        onKeyDownCapture() {
          isNavigatingRef.current = true;
        }
      }, elementProps, getButtonProps],
      stateAttributesMapping: tabsStateAttributesMapping
    });
    return element;
  });
  if (true) TabsTab.displayName = "TabsTab";

  // node_modules/@base-ui/react/tabs/indicator/TabsIndicator.mjs
  var React48 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/tabs/indicator/TabsIndicatorCssVars.mjs
  var activeTabLeft = "--active-tab-left";
  var activeTabRight = "--active-tab-right";
  var activeTabTop = "--active-tab-top";
  var activeTabBottom = "--active-tab-bottom";
  var activeTabWidth = "--active-tab-width";
  var activeTabHeight = "--active-tab-height";

  // node_modules/@base-ui/react/tabs/indicator/TabsIndicator.mjs
  var import_jsx_runtime31 = __toESM(require_jsx_runtime(), 1);
  var _PrehydrationScript;
  var stateAttributesMapping2 = {
    ...tabsStateAttributesMapping,
    activeTabPosition: () => null,
    activeTabSize: () => null
  };
  var MAX_LAYOUT_ROUNDING_ERROR = 2;
  var TabsIndicator = /* @__PURE__ */ React48.forwardRef(function TabsIndicator2(componentProps, forwardedRef) {
    const {
      className,
      render,
      renderBeforeHydration = false,
      style: styleProp,
      ...elementProps
    } = componentProps;
    const {
      getTabElementBySelectedValue,
      orientation,
      tabActivationDirection,
      value
    } = useTabsRootContext();
    const {
      tabsListElement,
      registerIndicatorUpdateListener
    } = useTabsListContext();
    const rerender = useForcedRerendering();
    React48.useEffect(() => {
      return registerIndicatorUpdateListener(rerender);
    }, [registerIndicatorUpdateListener, rerender]);
    let left = 0;
    let right = 0;
    let top = 0;
    let bottom = 0;
    let width = 0;
    let height = 0;
    let isTabSelected = false;
    if (value != null && tabsListElement != null) {
      const activeTab = getTabElementBySelectedValue(value);
      if (activeTab != null) {
        isTabSelected = true;
        const {
          width: computedWidth,
          height: computedHeight
        } = getCssDimensions(activeTab);
        const {
          width: tabListWidth,
          height: tabListHeight
        } = getCssDimensions(tabsListElement);
        const tabRect = activeTab.getBoundingClientRect();
        const tabsListRect = tabsListElement.getBoundingClientRect();
        const scaleX = tabListWidth > 0 ? tabsListRect.width / tabListWidth : 1;
        const scaleY = tabListHeight > 0 ? tabsListRect.height / tabListHeight : 1;
        const layoutOffset = getLayoutOffset(activeTab, tabsListElement);
        left = layoutOffset.left;
        top = layoutOffset.top;
        const rectLeft = (tabRect.left - tabsListRect.left) / scaleX + tabsListElement.scrollLeft - tabsListElement.clientLeft;
        const rectTop = (tabRect.top - tabsListRect.top) / scaleY + tabsListElement.scrollTop - tabsListElement.clientTop;
        const tabTranslation = getActiveTabTranslation(activeTab);
        if (Math.abs(rectLeft - tabTranslation.x - left) <= MAX_LAYOUT_ROUNDING_ERROR && Math.abs(rectTop - tabTranslation.y - top) <= MAX_LAYOUT_ROUNDING_ERROR) {
          left = rectLeft;
          top = rectTop;
        }
        width = computedWidth;
        height = computedHeight;
        right = tabsListElement.scrollWidth - left - width;
        bottom = tabsListElement.scrollHeight - top - height;
      }
    }
    const activeTabPosition = isTabSelected ? {
      left,
      right,
      top,
      bottom
    } : null;
    const activeTabSize = isTabSelected ? {
      width,
      height
    } : null;
    const style = isTabSelected ? {
      [activeTabLeft]: `${left}px`,
      [activeTabRight]: `${right}px`,
      [activeTabTop]: `${top}px`,
      [activeTabBottom]: `${bottom}px`,
      [activeTabWidth]: `${width}px`,
      [activeTabHeight]: `${height}px`
    } : void 0;
    const displayIndicator = isTabSelected && width > 0 && height > 0;
    const state = {
      orientation,
      activeTabPosition,
      activeTabSize,
      tabActivationDirection
    };
    const element = useRenderElement("span", componentProps, {
      state,
      ref: forwardedRef,
      props: [{
        role: "presentation",
        style,
        hidden: !displayIndicator
        // do not display the indicator before the layout is settled
      }, elementProps, {
        suppressHydrationWarning: true
      }],
      stateAttributesMapping: stateAttributesMapping2
    });
    if (value == null) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(React48.Fragment, {
      children: [element, renderBeforeHydration && (_PrehydrationScript || (_PrehydrationScript = /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(PrehydrationScript, {
        script
      })))]
    });
  });
  if (true) TabsIndicator.displayName = "TabsIndicator";
  function getLayoutOffset(element, ancestor) {
    const elementOffset = getCumulativeOffset(element);
    const ancestorOffset = getCumulativeOffset(ancestor);
    let left = elementOffset.left - ancestorOffset.left - ancestor.clientLeft;
    let top = elementOffset.top - ancestorOffset.top - ancestor.clientTop;
    let node = getParentNode(element);
    while (isHTMLElement(node) && node !== ancestor && !isLastTraversableNode(node)) {
      left -= node.scrollLeft;
      top -= node.scrollTop;
      node = getParentNode(node);
    }
    return {
      left,
      top
    };
  }
  function getCumulativeOffset(element) {
    let left = 0;
    let top = 0;
    let currentElement = element;
    while (currentElement != null) {
      left += currentElement.offsetLeft;
      top += currentElement.offsetTop;
      const offsetParent = currentElement.offsetParent;
      if (offsetParent != null) {
        left += offsetParent.clientLeft;
        top += offsetParent.clientTop;
      }
      currentElement = offsetParent;
    }
    return {
      left,
      top
    };
  }
  function getActiveTabTranslation(element) {
    const computedStyle = getWindow(element).getComputedStyle(element);
    const {
      x,
      y
    } = getElementTransform(element, computedStyle);
    let translateX = x;
    let translateY = y;
    const {
      translate
    } = computedStyle;
    if (translate && translate !== "none") {
      const parts = translate.split(" ");
      translateX += resolveTranslateLength(parts[0], element.offsetWidth);
      translateY += resolveTranslateLength(parts[1], element.offsetHeight);
    }
    return {
      x: translateX,
      y: translateY
    };
  }
  function resolveTranslateLength(value, referenceSize) {
    if (!value) {
      return 0;
    }
    const numeric = parseFloat(value);
    if (!Number.isFinite(numeric)) {
      return 0;
    }
    return value.endsWith("%") ? numeric / 100 * referenceSize : numeric;
  }

  // node_modules/@base-ui/react/tabs/panel/TabsPanel.mjs
  var React49 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/tabs/panel/TabsPanelDataAttributes.mjs
  var index = "data-index";
  var startingStyle2 = TransitionStatusDataAttributes_exports.startingStyle;
  var endingStyle2 = TransitionStatusDataAttributes_exports.endingStyle;

  // node_modules/@base-ui/react/tabs/panel/TabsPanel.mjs
  var stateAttributesMapping3 = {
    ...tabsStateAttributesMapping,
    ...transitionStatusMapping
  };
  var TabsPanel = /* @__PURE__ */ React49.forwardRef(function TabsPanel2(componentProps, forwardedRef) {
    const {
      className,
      value,
      render,
      keepMounted = false,
      style,
      ...elementProps
    } = componentProps;
    const {
      value: selectedValue,
      getTabIdByPanelValue,
      orientation,
      tabActivationDirection,
      registerMountedTabPanel
    } = useTabsRootContext();
    const id = useBaseUiId();
    const {
      ref: listItemRef,
      index: index2
    } = useCompositeListItem();
    const open = value === selectedValue;
    const {
      mounted,
      transitionStatus,
      setMounted
    } = useTransitionStatus(open);
    const hidden = !mounted;
    const correspondingTabId = getTabIdByPanelValue(value);
    const state = {
      hidden,
      orientation,
      tabActivationDirection,
      transitionStatus
    };
    const panelRef = React49.useRef(null);
    const element = useRenderElement("div", componentProps, {
      state,
      ref: [forwardedRef, listItemRef, panelRef],
      props: [{
        "aria-labelledby": correspondingTabId,
        hidden,
        id,
        role: "tabpanel",
        tabIndex: open ? 0 : -1,
        inert: inertValue(!open),
        // Computed key: a plain literal key fails the DOM-props excess property check.
        [index]: index2
      }, elementProps],
      stateAttributesMapping: stateAttributesMapping3
    });
    useOpenChangeComplete({
      open,
      ref: panelRef,
      onComplete() {
        if (!open) {
          setMounted(false);
        }
      }
    });
    useIsoLayoutEffect(() => {
      if (id == null || hidden && !keepMounted) {
        return void 0;
      }
      return registerMountedTabPanel(value, id);
    }, [hidden, keepMounted, value, id, registerMountedTabPanel]);
    const shouldRender = keepMounted || mounted;
    if (!shouldRender) {
      return null;
    }
    return element;
  });
  if (true) TabsPanel.displayName = "TabsPanel";

  // node_modules/@base-ui/react/tabs/list/TabsList.mjs
  var React50 = __toESM(require_react(), 1);
  var import_jsx_runtime32 = __toESM(require_jsx_runtime(), 1);
  var TabsList = /* @__PURE__ */ React50.forwardRef(function TabsList2(componentProps, forwardedRef) {
    const {
      activateOnFocus = false,
      className,
      loopFocus = true,
      render,
      style,
      ...elementProps
    } = componentProps;
    const {
      orientation,
      setTabMap,
      tabActivationDirection
    } = useTabsRootContext();
    const [highlightedTabIndex, setHighlightedTabIndex] = React50.useState(0);
    const [tabsListElement, setTabsListElement] = React50.useState(null);
    const indicatorUpdateListenersRef = React50.useRef(/* @__PURE__ */ new Set());
    const tabResizeObserverElementsRef = React50.useRef(/* @__PURE__ */ new Set());
    const resizeObserverRef = React50.useRef(null);
    useIsoLayoutEffect(() => {
      if (typeof ResizeObserver === "undefined") {
        return void 0;
      }
      const resizeObserver = new ResizeObserver(() => {
        indicatorUpdateListenersRef.current.forEach((listener) => {
          listener();
        });
      });
      resizeObserverRef.current = resizeObserver;
      if (tabsListElement) {
        resizeObserver.observe(tabsListElement);
      }
      tabResizeObserverElementsRef.current.forEach((element) => {
        resizeObserver.observe(element);
      });
      return () => {
        resizeObserver.disconnect();
        resizeObserverRef.current = null;
      };
    }, [tabsListElement]);
    const registerIndicatorUpdateListener = useStableCallback((listener) => {
      indicatorUpdateListenersRef.current.add(listener);
      return () => {
        indicatorUpdateListenersRef.current.delete(listener);
      };
    });
    const registerTabResizeObserverElement = useStableCallback((element) => {
      tabResizeObserverElementsRef.current.add(element);
      resizeObserverRef.current?.observe(element);
      return () => {
        tabResizeObserverElementsRef.current.delete(element);
        resizeObserverRef.current?.unobserve(element);
      };
    });
    const state = {
      orientation,
      tabActivationDirection
    };
    const defaultProps = {
      "aria-orientation": orientation === "vertical" ? "vertical" : void 0,
      role: "tablist"
    };
    const tabsListContextValue = React50.useMemo(() => ({
      activateOnFocus,
      registerIndicatorUpdateListener,
      registerTabResizeObserverElement,
      tabsListElement
    }), [activateOnFocus, registerIndicatorUpdateListener, registerTabResizeObserverElement, tabsListElement]);
    return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(TabsListContext.Provider, {
      value: tabsListContextValue,
      children: /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(CompositeRoot, {
        render,
        className,
        style,
        state,
        refs: [forwardedRef, setTabsListElement],
        props: [defaultProps, elementProps],
        stateAttributesMapping: tabsStateAttributesMapping,
        highlightedIndex: highlightedTabIndex,
        enableHomeAndEndKeys: true,
        loopFocus,
        orientation,
        onHighlightedIndexChange: setHighlightedTabIndex,
        onMapChange: setTabMap,
        disabledIndices: EMPTY_ARRAY
      })
    });
  });
  if (true) TabsList.displayName = "TabsList";

  // node_modules/@base-ui/react/use-render/useRender.mjs
  function useRender(params) {
    return useRenderElement(params.defaultTagName ?? "div", params, params);
  }

  // packages/ui/build-module/utils/direction-provider.mjs
  var import_i18n3 = __toESM(require_i18n(), 1);
  var import_jsx_runtime33 = __toESM(require_jsx_runtime(), 1);
  function DirectionProvider3({ children }) {
    return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(DirectionProvider, { direction: (0, import_i18n3.isRTL)() ? "rtl" : "ltr", children });
  }

  // packages/ui/build-module/link/link.mjs
  var import_element2 = __toESM(require_element(), 1);
  var import_i18n4 = __toESM(require_i18n(), 1);
  var import_jsx_runtime34 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE = "data-wp-hash";
  function getRuntime() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument(targetDocument) {
    const runtime = getRuntime();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle(hash, css) {
    const runtime = getRuntime();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle("10f3806643", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
  }
  var resets_default = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
  if (typeof process === "undefined" || true) {
    registerStyle("08122b3d53", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{.af79fb116edb0dd7__outset-ring--focus:focus,.dfcfdc28396e5d98__outset-ring--focus-visible:focus-visible,.e5cd9ee879f6403a__outset-ring--focus-within:focus-within,:focus-visible ._81935a08e952f267__outset-ring--focus-parent-visible{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within,.abc777e9713fa711__outset-ring--focus-except-active:focus{outline:none}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.abc777e9713fa711__outset-ring--focus-except-active:focus:not(:active){--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}}");
  }
  var focus_module_default = { "outset-ring--focus": "af79fb116edb0dd7__outset-ring--focus", "outset-ring--focus-visible": "dfcfdc28396e5d98__outset-ring--focus-visible", "outset-ring--focus-within": "e5cd9ee879f6403a__outset-ring--focus-within", "outset-ring--focus-parent-visible": "_81935a08e952f267__outset-ring--focus-parent-visible", "outset-ring--focus-except-active": "abc777e9713fa711__outset-ring--focus-except-active", "outset-ring--focus-within-except-active": "_3c9f5ee9fc9c136d__outset-ring--focus-within-except-active" };
  if (typeof process === "undefined" || true) {
    registerStyle("e8e6a9be37", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.d4250949359b05ce__link{text-decoration-thickness:from-font;text-underline-offset:.2em}.c6055659b8e2cd2c__is-brand,.c6055659b8e2cd2c__is-brand:visited{--_gcd-a-color:var(--wpds-color-foreground-interactive-brand,var(--wp-admin-theme-color,#3858e9));color:var(--wpds-color-foreground-interactive-brand,var(--wp-admin-theme-color,#3858e9))}.c6055659b8e2cd2c__is-brand:active,.c6055659b8e2cd2c__is-brand:hover{--_gcd-a-color:var(--wpds-color-foreground-interactive-brand-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 52%,#000));color:var(--wpds-color-foreground-interactive-brand-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 52%,#000))}._92e0dfcaeee15b88__is-neutral,._92e0dfcaeee15b88__is-neutral:visited{--_gcd-a-color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);text-decoration-color:var(--wpds-color-stroke-interactive-neutral,#8d8d8d)}._92e0dfcaeee15b88__is-neutral:active,._92e0dfcaeee15b88__is-neutral:hover{--_gcd-a-color:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e);color:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e)}.cf122a9bf1035d42__is-unstyled{--_gcd-a-color:inherit;color:inherit;text-decoration:none}._0cb411afac4c86c7__link-icon{display:inline-block;font-weight:var(--wpds-typography-font-weight-default,400);line-height:1;margin-inline-start:var(--wpds-dimension-padding-xs,4px);text-decoration:none}._0cb411afac4c86c7__link-icon:after{content:"\\2197"}._0cb411afac4c86c7__link-icon:dir(rtl):after{content:"\\2196"}}}');
  }
  var style_default = { "link": "d4250949359b05ce__link", "is-brand": "c6055659b8e2cd2c__is-brand", "is-neutral": "_92e0dfcaeee15b88__is-neutral", "is-unstyled": "cf122a9bf1035d42__is-unstyled", "link-icon": "_0cb411afac4c86c7__link-icon" };
  if (typeof process === "undefined" || true) {
    registerStyle("e8e31009f5", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}");
  }
  var global_css_defense_default = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li" };
  var Link = (0, import_element2.forwardRef)(function Link2({
    children,
    variant = "default",
    tone = "brand",
    openInNewTab = false,
    target,
    render,
    className,
    ...props
  }, ref) {
    const shouldShowNewTabIndicator = openInNewTab || /^_blank$/i.test(target ?? "");
    const element = useRender({
      render,
      defaultTagName: "a",
      ref,
      props: mergeProps(props, {
        className: clsx_default(
          global_css_defense_default.a,
          resets_default["box-sizing"],
          focus_module_default["outset-ring--focus-except-active"],
          variant !== "unstyled" && style_default.link,
          variant !== "unstyled" && style_default[`is-${tone}`],
          variant === "unstyled" && style_default["is-unstyled"],
          className
        ),
        target: target ?? (openInNewTab ? "_blank" : void 0),
        children: /* @__PURE__ */ (0, import_jsx_runtime34.jsxs)(import_jsx_runtime34.Fragment, { children: [
          children,
          shouldShowNewTabIndicator && /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(
            "span",
            {
              className: style_default["link-icon"],
              role: "img",
              "aria-label": (
                /* translators: accessibility text appended to link text */
                (0, import_i18n4.__)("(opens in a new tab)")
              )
            }
          )
        ] })
      })
    });
    return element;
  });

  // packages/ui/build-module/icon/icon.mjs
  var import_element3 = __toESM(require_element(), 1);
  var import_primitives18 = __toESM(require_primitives(), 1);
  var import_jsx_runtime35 = __toESM(require_jsx_runtime(), 1);
  var Icon = (0, import_element3.forwardRef)(function Icon2({ icon, size = 24, style, ...restProps }, ref) {
    const mergedStyle = icon.props.style || style ? { ...icon.props.style, ...style } : void 0;
    return /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
      import_primitives18.SVG,
      {
        ref,
        ...icon.props,
        ...restProps,
        ...mergedStyle ? { style: mergedStyle } : {},
        width: size,
        height: size
      }
    );
  });

  // packages/ui/build-module/visually-hidden/visually-hidden.mjs
  var import_element4 = __toESM(require_element(), 1);
  var STYLE_HASH_ATTRIBUTE2 = "data-wp-hash";
  function getRuntime2() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument2(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash2(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE2}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE2) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle2(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime2();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash2(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE2, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument2(targetDocument) {
    const runtime = getRuntime2();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle2(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle2(hash, css) {
    const runtime = getRuntime2();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle2(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle2("f4df7461e9", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.f37b9e2e191ebd66__visually-hidden{border:0;clip-path:inset(50%);height:1px;margin:-1px;overflow:hidden;overflow-wrap:normal;padding:0;position:absolute;width:1px;word-break:normal}}}");
  }
  var style_default2 = { "visually-hidden": "f37b9e2e191ebd66__visually-hidden" };
  var VisuallyHidden = (0, import_element4.forwardRef)(
    function VisuallyHidden2({ render, ...restProps }, ref) {
      const element = useRender({
        render,
        ref,
        props: mergeProps(
          { className: style_default2["visually-hidden"] },
          restProps,
          {
            // @ts-expect-error Arbitrary data-* attributes aren't indexable on the typed div props. Kept hardcoded so consumers can't change or remove it.
            "data-visually-hidden": ""
          }
        )
      });
      return element;
    }
  );

  // packages/ui/build-module/stack/stack.mjs
  var import_element5 = __toESM(require_element(), 1);
  var STYLE_HASH_ATTRIBUTE3 = "data-wp-hash";
  function getRuntime3() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument3(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash3(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE3}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE3) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle3(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime3();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash3(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE3, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument3(targetDocument) {
    const runtime = getRuntime3();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle3(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle3(hash, css) {
    const runtime = getRuntime3();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle3(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle3("32aba35fe1", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._19ce0419607e1896__stack{display:flex}}}");
  }
  var style_default3 = { "stack": "_19ce0419607e1896__stack" };
  var gapTokens = {
    xs: "var(--wpds-dimension-gap-xs, 4px)",
    sm: "var(--wpds-dimension-gap-sm, 8px)",
    md: "var(--wpds-dimension-gap-md, 12px)",
    lg: "var(--wpds-dimension-gap-lg, 16px)",
    xl: "var(--wpds-dimension-gap-xl, 24px)",
    "2xl": "var(--wpds-dimension-gap-2xl, 32px)",
    "3xl": "var(--wpds-dimension-gap-3xl, 40px)"
  };
  var Stack = (0, import_element5.forwardRef)(function Stack2({ direction, gap, align, justify, wrap, render, ...props }, ref) {
    const style = {
      gap: gap && gapTokens[gap],
      alignItems: align,
      justifyContent: justify,
      flexDirection: direction,
      flexWrap: wrap
    };
    const element = useRender({
      render,
      ref,
      props: mergeProps(props, { style, className: style_default3.stack })
    });
    return element;
  });

  // packages/ui/build-module/utils/use-schedule-validation.mjs
  var import_compose = __toESM(require_compose(), 1);
  var import_element6 = __toESM(require_element(), 1);
  function useScheduleValidation(validate) {
    const validateEvent = (0, import_compose.useEvent)(validate);
    const timerRef = (0, import_element6.useRef)(null);
    const unmountedRef = (0, import_element6.useRef)(false);
    const scheduleValidation = (0, import_element6.useCallback)(() => {
      if (unmountedRef.current) {
        return;
      }
      if (timerRef.current) {
        clearTimeout(timerRef.current);
      }
      timerRef.current = setTimeout(() => {
        validateEvent();
        timerRef.current = null;
      }, 0);
    }, [validateEvent]);
    (0, import_element6.useEffect)(() => {
      unmountedRef.current = false;
      return () => {
        unmountedRef.current = true;
        if (timerRef.current) {
          clearTimeout(timerRef.current);
        }
      };
    }, []);
    return scheduleValidation;
  }

  // packages/ui/build-module/form/primitives/input/input.mjs
  var import_element9 = __toESM(require_element(), 1);

  // packages/ui/build-module/form/primitives/input-layout/input-layout.mjs
  var import_element7 = __toESM(require_element(), 1);
  var import_jsx_runtime36 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE4 = "data-wp-hash";
  function getRuntime4() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument4(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash4(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE4}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE4) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle4(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime4();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash4(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE4, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument4(targetDocument) {
    const runtime = getRuntime4();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle4(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle4(hash, css) {
    const runtime = getRuntime4();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle4(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle4("e8e31009f5", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}");
  }
  var global_css_defense_default2 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li" };
  if (typeof process === "undefined" || true) {
    registerStyle4("10f3806643", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
  }
  var resets_default2 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
  if (typeof process === "undefined" || true) {
    registerStyle4("e4f4f9600b", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.cb2baafdc08746bb__input-layout{--wp-ui-input-layout-padding-inline:var(--wpds-dimension-padding-md,12px);background-color:var(--wpds-color-background-interactive-neutral,var(--wpds-color-background-surface-neutral-strong,#fff));border-color:var(--wpds-color-stroke-interactive-neutral,#8d8d8d);border-radius:var(--wpds-border-radius-sm,2px);border-style:solid;border-width:var(--wpds-border-width-xs,1px);color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:max(var(--wpds-typography-font-size-md,13px),16px);height:var(--wpds-dimension-size-lg,40px);line-height:1;@media (min-width:600px){font-size:var(--wpds-typography-font-size-md,13px)}&._0c807a84cbb94e0c__is-size-compact{height:var(--wpds-dimension-size-md,32px)}&._0c807a84cbb94e0c__is-size-compact,&.ed67cda122dc1e7b__is-size-small{--wp-ui-input-layout-padding-inline:var(--wpds-dimension-padding-sm,8px)}&.ed67cda122dc1e7b__is-size-small{height:var(--wpds-dimension-size-sm,24px)}&._6fb7104732387680__is-disabled,&:has([data-can-disable-input-layout][data-disabled]){color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}&:not(._8097270636ca6100__is-borderless){background-color:var(--wpds-color-background-interactive-neutral-disabled,var(--wpds-color-background-surface-neutral-strong,#fff));border-color:var(--wpds-color-stroke-interactive-neutral-disabled,#dbdbdb);@media (forced-colors:active){border-bottom-color:GrayText;border-left-color:GrayText;border-right-color:GrayText;border-top-color:GrayText}}}&._8097270636ca6100__is-borderless{background-color:var(--wpds-color-background-interactive-neutral-weak,#0000);border-color:transparent}&:has(._0d7afad74a057888__input-layout-slot:focus-within){--_gcd-div-outline:none;outline:none}&:hover:not(._6fb7104732387680__is-disabled,:has([data-can-disable-input-layout][data-disabled]),._8097270636ca6100__is-borderless){background-color:var(--wpds-color-background-interactive-neutral-active,var(--wpds-color-background-surface-neutral-strong,#fff));border-color:var(--wpds-color-stroke-interactive-neutral-active,#6e6e6e)}&:has(:invalid[data-validity-visible]){--focus-color:var(--wpds-color-stroke-interactive-error,#cc1818);border-color:var(--wpds-color-stroke-interactive-error,#cc1818);&:hover{border-color:var(--wpds-color-stroke-interactive-error-active,#9d0000)}}}.c192b41a12b4387b__slot-wrapper{display:contents}._0d7afad74a057888__input-layout-slot{align-items:center;display:flex;&._0c952682762ca288__is-padding-minimal{--wp-ui-input-layout-prefix-padding-start:calc(var(--wp-ui-input-layout-padding-inline) - var(--wpds-dimension-padding-xs, 4px));--wp-ui-input-layout-suffix-padding-end:calc(var(--wp-ui-input-layout-padding-inline) - var(--wpds-dimension-padding-xs, 4px))}[data-slot-type=prefix] &{padding-inline-start:var(--wp-ui-input-layout-prefix-padding-start,var(--wp-ui-input-layout-padding-inline))}[data-slot-type=suffix] &{padding-inline-end:var(--wp-ui-input-layout-suffix-padding-end,var(--wp-ui-input-layout-padding-inline))}}}}');
  }
  var style_default4 = { "input-layout": "cb2baafdc08746bb__input-layout", "is-size-compact": "_0c807a84cbb94e0c__is-size-compact", "is-size-small": "ed67cda122dc1e7b__is-size-small", "is-disabled": "_6fb7104732387680__is-disabled", "is-borderless": "_8097270636ca6100__is-borderless", "input-layout-slot": "_0d7afad74a057888__input-layout-slot", "slot-wrapper": "c192b41a12b4387b__slot-wrapper", "is-padding-minimal": "_0c952682762ca288__is-padding-minimal" };
  var InputLayout = (0, import_element7.forwardRef)(
    function InputLayout2({
      className,
      children,
      visuallyDisabled,
      size = "default",
      isBorderless,
      prefix,
      suffix,
      ...restProps
    }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(
        "div",
        {
          ref,
          className: clsx_default(
            global_css_defense_default2.div,
            resets_default2["box-sizing"],
            style_default4["input-layout"],
            style_default4[`is-size-${size}`],
            visuallyDisabled && style_default4["is-disabled"],
            isBorderless && style_default4["is-borderless"],
            className
          ),
          ...restProps,
          children: [
            import_element7.Children.count(prefix) > 0 && /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
              "div",
              {
                className: style_default4["slot-wrapper"],
                "data-slot-type": "prefix",
                children: prefix
              }
            ),
            children,
            import_element7.Children.count(suffix) > 0 && /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
              "div",
              {
                className: style_default4["slot-wrapper"],
                "data-slot-type": "suffix",
                children: suffix
              }
            )
          ]
        }
      );
    }
  );

  // packages/ui/build-module/form/primitives/input-layout/slot.mjs
  var import_element8 = __toESM(require_element(), 1);
  var import_jsx_runtime37 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE5 = "data-wp-hash";
  function getRuntime5() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument5(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash5(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE5}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE5) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle5(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime5();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash5(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE5, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument5(targetDocument) {
    const runtime = getRuntime5();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle5(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle5(hash, css) {
    const runtime = getRuntime5();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle5(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle5("e4f4f9600b", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.cb2baafdc08746bb__input-layout{--wp-ui-input-layout-padding-inline:var(--wpds-dimension-padding-md,12px);background-color:var(--wpds-color-background-interactive-neutral,var(--wpds-color-background-surface-neutral-strong,#fff));border-color:var(--wpds-color-stroke-interactive-neutral,#8d8d8d);border-radius:var(--wpds-border-radius-sm,2px);border-style:solid;border-width:var(--wpds-border-width-xs,1px);color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:max(var(--wpds-typography-font-size-md,13px),16px);height:var(--wpds-dimension-size-lg,40px);line-height:1;@media (min-width:600px){font-size:var(--wpds-typography-font-size-md,13px)}&._0c807a84cbb94e0c__is-size-compact{height:var(--wpds-dimension-size-md,32px)}&._0c807a84cbb94e0c__is-size-compact,&.ed67cda122dc1e7b__is-size-small{--wp-ui-input-layout-padding-inline:var(--wpds-dimension-padding-sm,8px)}&.ed67cda122dc1e7b__is-size-small{height:var(--wpds-dimension-size-sm,24px)}&._6fb7104732387680__is-disabled,&:has([data-can-disable-input-layout][data-disabled]){color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}&:not(._8097270636ca6100__is-borderless){background-color:var(--wpds-color-background-interactive-neutral-disabled,var(--wpds-color-background-surface-neutral-strong,#fff));border-color:var(--wpds-color-stroke-interactive-neutral-disabled,#dbdbdb);@media (forced-colors:active){border-bottom-color:GrayText;border-left-color:GrayText;border-right-color:GrayText;border-top-color:GrayText}}}&._8097270636ca6100__is-borderless{background-color:var(--wpds-color-background-interactive-neutral-weak,#0000);border-color:transparent}&:has(._0d7afad74a057888__input-layout-slot:focus-within){--_gcd-div-outline:none;outline:none}&:hover:not(._6fb7104732387680__is-disabled,:has([data-can-disable-input-layout][data-disabled]),._8097270636ca6100__is-borderless){background-color:var(--wpds-color-background-interactive-neutral-active,var(--wpds-color-background-surface-neutral-strong,#fff));border-color:var(--wpds-color-stroke-interactive-neutral-active,#6e6e6e)}&:has(:invalid[data-validity-visible]){--focus-color:var(--wpds-color-stroke-interactive-error,#cc1818);border-color:var(--wpds-color-stroke-interactive-error,#cc1818);&:hover{border-color:var(--wpds-color-stroke-interactive-error-active,#9d0000)}}}.c192b41a12b4387b__slot-wrapper{display:contents}._0d7afad74a057888__input-layout-slot{align-items:center;display:flex;&._0c952682762ca288__is-padding-minimal{--wp-ui-input-layout-prefix-padding-start:calc(var(--wp-ui-input-layout-padding-inline) - var(--wpds-dimension-padding-xs, 4px));--wp-ui-input-layout-suffix-padding-end:calc(var(--wp-ui-input-layout-padding-inline) - var(--wpds-dimension-padding-xs, 4px))}[data-slot-type=prefix] &{padding-inline-start:var(--wp-ui-input-layout-prefix-padding-start,var(--wp-ui-input-layout-padding-inline))}[data-slot-type=suffix] &{padding-inline-end:var(--wp-ui-input-layout-suffix-padding-end,var(--wp-ui-input-layout-padding-inline))}}}}');
  }
  var style_default5 = { "input-layout": "cb2baafdc08746bb__input-layout", "is-size-compact": "_0c807a84cbb94e0c__is-size-compact", "is-size-small": "ed67cda122dc1e7b__is-size-small", "is-disabled": "_6fb7104732387680__is-disabled", "is-borderless": "_8097270636ca6100__is-borderless", "input-layout-slot": "_0d7afad74a057888__input-layout-slot", "slot-wrapper": "c192b41a12b4387b__slot-wrapper", "is-padding-minimal": "_0c952682762ca288__is-padding-minimal" };
  var InputLayoutSlot = (0, import_element8.forwardRef)(function InputLayoutSlot2({ padding = "default", className, ...restProps }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
      "div",
      {
        ref,
        className: clsx_default(
          style_default5["input-layout-slot"],
          style_default5[`is-padding-${padding}`],
          className
        ),
        ...restProps
      }
    );
  });
  InputLayoutSlot.displayName = "InputLayout.Slot";

  // packages/ui/build-module/form/primitives/input-layout/index.mjs
  var InputLayout3 = Object.assign(InputLayout, {
    Slot: InputLayoutSlot
  });

  // packages/ui/build-module/form/primitives/input/input.mjs
  var import_jsx_runtime38 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE6 = "data-wp-hash";
  function getRuntime6() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument6(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash6(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE6}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE6) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle6(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime6();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash6(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE6, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument6(targetDocument) {
    const runtime = getRuntime6();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle6(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle6(hash, css) {
    const runtime = getRuntime6();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle6(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle6("e8e31009f5", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}");
  }
  var global_css_defense_default3 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li" };
  if (typeof process === "undefined" || true) {
    registerStyle6("08122b3d53", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{.af79fb116edb0dd7__outset-ring--focus:focus,.dfcfdc28396e5d98__outset-ring--focus-visible:focus-visible,.e5cd9ee879f6403a__outset-ring--focus-within:focus-within,:focus-visible ._81935a08e952f267__outset-ring--focus-parent-visible{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within,.abc777e9713fa711__outset-ring--focus-except-active:focus{outline:none}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.abc777e9713fa711__outset-ring--focus-except-active:focus:not(:active){--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}}");
  }
  var focus_module_default2 = { "outset-ring--focus": "af79fb116edb0dd7__outset-ring--focus", "outset-ring--focus-visible": "dfcfdc28396e5d98__outset-ring--focus-visible", "outset-ring--focus-within": "e5cd9ee879f6403a__outset-ring--focus-within", "outset-ring--focus-parent-visible": "_81935a08e952f267__outset-ring--focus-parent-visible", "outset-ring--focus-except-active": "abc777e9713fa711__outset-ring--focus-except-active", "outset-ring--focus-within-except-active": "_3c9f5ee9fc9c136d__outset-ring--focus-within-except-active" };
  if (typeof process === "undefined" || true) {
    registerStyle6("1a25f6a232", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._2ae7be2fc1bb17a3__input{--_gcd-input-padding:var(--wp-ui-input-padding-block,0px) var(--wp-ui-input-layout-padding-inline,0px);background:transparent;border:none;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);font-family:inherit;font-size:inherit;line-height:inherit;outline:none;padding-block:var(--wp-ui-input-padding-block,0);padding-inline:var(--wp-ui-input-layout-padding-inline,0);width:100%;&::placeholder{color:var(--wpds-color-foreground-interactive-neutral-weak,#707070)}&:disabled,&[aria-disabled=true]{--_gcd-input-placeholder-color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);&::placeholder{color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d)}@media (forced-colors:active){color:GrayText}}&[type=email],&[type=url]{direction:ltr}&[type=number]{appearance:textfield;&::-webkit-inner-spin-button,&::-webkit-outer-spin-button{appearance:none;margin:0}}}}}");
  }
  var style_default6 = { "input": "_2ae7be2fc1bb17a3__input" };
  var Input3 = (0, import_element9.forwardRef)(function Input22({ className, size = "default", prefix, suffix, style, ...restProps }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
      InputLayout3,
      {
        className: clsx_default(
          focus_module_default2["outset-ring--focus-within"],
          className
        ),
        style,
        size,
        visuallyDisabled: restProps.disabled,
        prefix,
        suffix,
        children: /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
          Input,
          {
            ref,
            className: clsx_default(global_css_defense_default3.input, style_default6.input),
            ...restProps
          }
        )
      }
    );
  });

  // packages/ui/build-module/form/primitives/control-with-error/control-with-error.mjs
  var import_i18n5 = __toESM(require_i18n(), 1);
  var import_element11 = __toESM(require_element(), 1);

  // packages/ui/build-module/spinner/spinner.mjs
  var import_element10 = __toESM(require_element(), 1);
  var import_jsx_runtime39 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE7 = "data-wp-hash";
  function getRuntime7() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument7(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash7(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE7}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE7) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle7(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime7();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash7(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE7, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument7(targetDocument) {
    const runtime = getRuntime7();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle7(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle7(hash, css) {
    const runtime = getRuntime7();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle7(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle7("bbbecfe373", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.ab4d64c07c0ba587__spinner{background-color:transparent;display:inline-block;height:var(--wpds-dimension-size-2xs,16px);opacity:1;overflow:visible;position:relative;width:var(--wpds-dimension-size-2xs,16px)}.a7654e10245bb7d2__indicator,.dc51f80c84b35fe2__track{fill:transparent;stroke-width:1.5px}.dc51f80c84b35fe2__track{stroke:var(--wpds-color-background-track-neutral,#dbdbdb)}.a7654e10245bb7d2__indicator{stroke:var(--wpds-color-background-thumb-brand,var(--wp-admin-theme-color,#3858e9));stroke-linecap:round;animation:_02322d973909703c__spinner-spin 1.4s linear infinite both;transform-origin:50% 50%}@keyframes _02322d973909703c__spinner-spin{0%{transform:rotate(0deg)}to{transform:rotate(1turn)}}}}");
  }
  var style_default7 = { "spinner": "ab4d64c07c0ba587__spinner", "track": "dc51f80c84b35fe2__track", "indicator": "a7654e10245bb7d2__indicator", "spinner-spin": "_02322d973909703c__spinner-spin" };
  var Spinner = (0, import_element10.forwardRef)(
    function Spinner2({ className, ...props }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime39.jsxs)(
        "svg",
        {
          className: clsx_default(style_default7.spinner, className),
          viewBox: "0 0 100 100",
          xmlns: "http://www.w3.org/2000/svg",
          role: "presentation",
          focusable: "false",
          ...props,
          ref,
          children: [
            /* @__PURE__ */ (0, import_jsx_runtime39.jsx)(
              "circle",
              {
                className: style_default7.track,
                cx: "50",
                cy: "50",
                r: "50",
                vectorEffect: "non-scaling-stroke"
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime39.jsx)(
              "path",
              {
                className: style_default7.indicator,
                d: "m 50 0 a 50 50 0 0 1 50 50",
                vectorEffect: "non-scaling-stroke"
              }
            )
          ]
        }
      );
    }
  );

  // packages/ui/build-module/form/primitives/validity-indicator/validity-indicator.mjs
  var import_jsx_runtime40 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE8 = "data-wp-hash";
  function getRuntime8() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument8(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash8(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE8}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE8) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle8(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime8();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash8(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE8, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument8(targetDocument) {
    const runtime = getRuntime8();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle8(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle8(hash, css) {
    const runtime = getRuntime8();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle8(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle8("e8e31009f5", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}");
  }
  var global_css_defense_default4 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li" };
  if (typeof process === "undefined" || true) {
    registerStyle8("d11816833c", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._7a7ac88e306348e7__indicator{--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);align-items:flex-start;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);gap:var(--wpds-dimension-gap-xs,4px);line-height:var(--wpds-typography-line-height-xs,16px);@media not (prefers-reduced-motion){animation:_48722cc5dd090ce4__indicator-jump var(--wpds-motion-duration-md,.2s) cubic-bezier(.68,-.55,.27,1.55)}}._57a3bbbfbe38a5c6__is-invalid{color:var(--wpds-color-foreground-content-error-weak,#cc1818)}._9e944dc198aac10b__is-valid{color:var(--wpds-color-foreground-content-success-weak,#008030)}.e482806667437c6a__indicator-icon{flex-shrink:0}._6e46434cc23019d2__indicator-spinner{height:var(--wpds-dimension-size-3xs,12px);margin:2px;width:var(--wpds-dimension-size-3xs,12px)}@keyframes _48722cc5dd090ce4__indicator-jump{0%{opacity:0;transform:translateY(-4px)}to{opacity:1;transform:translateY(0)}}}}');
  }
  var style_default8 = { "indicator": "_7a7ac88e306348e7__indicator", "indicator-jump": "_48722cc5dd090ce4__indicator-jump", "is-invalid": "_57a3bbbfbe38a5c6__is-invalid", "is-valid": "_9e944dc198aac10b__is-valid", "indicator-icon": "e482806667437c6a__indicator-icon", "indicator-spinner": "_6e46434cc23019d2__indicator-spinner" };
  var ICON = {
    valid: published_default,
    invalid: error_default
  };
  function ValidityIndicator({
    id,
    type,
    message
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime40.jsxs)(
      "p",
      {
        id,
        className: clsx_default(
          global_css_defense_default4.p,
          style_default8.indicator,
          style_default8[`is-${type}`]
        ),
        children: [
          type === "validating" ? /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(Spinner, { className: style_default8["indicator-spinner"] }) : /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(
            Icon,
            {
              className: style_default8["indicator-icon"],
              icon: ICON[type],
              size: 16,
              fill: "currentColor"
            }
          ),
          message
        ]
      }
    );
  }

  // packages/ui/build-module/form/primitives/control-with-error/control-with-error.mjs
  var import_jsx_runtime41 = __toESM(require_jsx_runtime(), 1);
  var DEFAULT_RENDER = (props) => /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(Stack, { ...props, direction: "column", gap: "sm" });
  function appendRequiredIndicator(label, required, markWhenOptional) {
    let suffix;
    if (required && !markWhenOptional) {
      suffix = `(${(0, import_i18n5.__)("Required")})`;
    } else if (!required && markWhenOptional) {
      suffix = `(${(0, import_i18n5.__)("Optional")})`;
    }
    if (!suffix) {
      return label;
    }
    if (typeof label === "string") {
      return `${label} ${suffix}`;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime41.jsxs)(import_jsx_runtime41.Fragment, { children: [
      label,
      " ",
      suffix
    ] });
  }
  var VALIDITY_VISIBLE_ATTRIBUTE = "data-validity-visible";
  var ControlWithError = (0, import_element11.forwardRef)(function ControlWithError2({
    required,
    markWhenOptional,
    customValidity,
    getValidityTarget,
    children,
    render = DEFAULT_RENDER,
    ...restProps
  }, forwardedRef) {
    const [errorMessage, setErrorMessage] = (0, import_element11.useState)();
    const [statusMessage, setStatusMessage] = (0, import_element11.useState)();
    const [showMessage, setShowMessage] = (0, import_element11.useState)(false);
    const [isTouched, setIsTouched] = (0, import_element11.useState)(false);
    const wrapperRef = (0, import_element11.useRef)(null);
    (0, import_element11.useEffect)(() => {
      const validityTarget = getValidityTarget();
      const handler = () => {
        if (customValidity?.type !== "validating") {
          setErrorMessage(validityTarget?.validationMessage);
        }
        setShowMessage(true);
        validityTarget?.setAttribute(VALIDITY_VISIBLE_ATTRIBUTE, "");
      };
      validityTarget?.addEventListener("invalid", handler);
      return () => validityTarget?.removeEventListener("invalid", handler);
    }, [customValidity?.type, getValidityTarget]);
    (0, import_element11.useEffect)(() => {
      const validityTarget = getValidityTarget();
      const suppressNativePopover = (event) => {
        event.preventDefault();
        if (!event.isTrusted) {
          return;
        }
        const target = event.target;
        const firstErrorInForm = Array.from(
          target.form?.elements ?? []
        ).find((el) => !el.validity.valid);
        if (!target.form || firstErrorInForm === target) {
          target.focus();
        }
      };
      const radioSiblings = validityTarget?.type === "radio" && validityTarget?.name ? Array.from(
        wrapperRef.current?.querySelectorAll(
          `input[type="radio"][name="${validityTarget?.name}"]`
        ) ?? []
      ).filter((sibling) => sibling !== validityTarget) : [];
      validityTarget?.addEventListener("invalid", suppressNativePopover);
      radioSiblings.forEach(
        (sibling) => sibling.addEventListener("invalid", suppressNativePopover)
      );
      return () => {
        validityTarget?.removeEventListener(
          "invalid",
          suppressNativePopover
        );
        radioSiblings.forEach(
          (sibling) => sibling.removeEventListener("invalid", suppressNativePopover)
        );
      };
    }, [getValidityTarget]);
    (0, import_element11.useEffect)(() => {
      const validityTarget = getValidityTarget();
      if (!customValidity?.type) {
        validityTarget?.setCustomValidity("");
        setErrorMessage(validityTarget?.validationMessage);
        setStatusMessage(void 0);
        return;
      }
      switch (customValidity.type) {
        case "validating": {
          validityTarget?.setCustomValidity("");
          setErrorMessage(void 0);
          setStatusMessage({
            type: "validating",
            message: customValidity.message
          });
          break;
        }
        case "valid": {
          validityTarget?.setCustomValidity("");
          setErrorMessage(validityTarget?.validationMessage);
          setStatusMessage({
            type: "valid",
            message: customValidity.message
          });
          break;
        }
        case "invalid": {
          validityTarget?.setCustomValidity(
            customValidity.message ?? ""
          );
          setErrorMessage(validityTarget?.validationMessage);
          setStatusMessage(void 0);
          break;
        }
      }
    }, [customValidity, getValidityTarget]);
    (0, import_element11.useEffect)(() => {
      if (!isTouched || showMessage) {
        return;
      }
      if (customValidity?.type === "validating") {
        const timer = setTimeout(() => {
          setShowMessage(true);
        }, 1e3);
        return () => clearTimeout(timer);
      }
      setShowMessage(true);
    }, [isTouched, customValidity?.type, showMessage]);
    const onBlur = (event) => {
      if (event.relatedTarget && event.currentTarget.contains(event.relatedTarget)) {
        return;
      }
      if (!isTouched) {
        setIsTouched(true);
        getValidityTarget()?.setAttribute(VALIDITY_VISIBLE_ATTRIBUTE, "");
      }
      const validityTarget = getValidityTarget();
      const isValidating = customValidity?.type === "validating";
      window.queueMicrotask(() => {
        if (!isValidating) {
          setErrorMessage(validityTarget?.validationMessage);
        }
      });
    };
    const messageId = (0, import_element11.useId)();
    const message = (() => {
      if (errorMessage) {
        return /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(
          ValidityIndicator,
          {
            id: messageId,
            type: "invalid",
            message: errorMessage
          }
        );
      }
      if (statusMessage?.type) {
        return /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(
          ValidityIndicator,
          {
            id: messageId,
            type: statusMessage.type,
            message: statusMessage.message
          }
        );
      }
      return null;
    })();
    const visibleMessage = showMessage ? message : null;
    (0, import_element11.useEffect)(() => {
      const target = getValidityTarget();
      if (!target) {
        return;
      }
      function setDescribedBy(el, shouldAdd) {
        const ids = (el.getAttribute("aria-describedby") ?? "").split(" ").filter((id) => id && id !== messageId);
        if (shouldAdd) {
          ids.push(messageId);
        }
        if (ids.length) {
          el.setAttribute("aria-describedby", ids.join(" "));
        } else {
          el.removeAttribute("aria-describedby");
        }
      }
      setDescribedBy(target, !!visibleMessage);
      return () => setDescribedBy(target, false);
    }, [visibleMessage, messageId, getValidityTarget]);
    return useRender({
      render,
      ref: [forwardedRef, wrapperRef],
      props: mergeProps(restProps, {
        onBlur,
        children: /* @__PURE__ */ (0, import_jsx_runtime41.jsxs)(import_jsx_runtime41.Fragment, { children: [
          (0, import_element11.cloneElement)(children, {
            label: appendRequiredIndicator(
              children.props.label,
              required,
              markWhenOptional
            ),
            required
          }),
          visibleMessage
        ] })
      })
    });
  });

  // packages/ui/build-module/form/primitives/field/index.mjs
  var field_exports = {};
  __export(field_exports, {
    Control: () => Control,
    Description: () => Description,
    Details: () => Details,
    Item: () => Item,
    Label: () => Label,
    Root: () => Root,
    VisualLabel: () => VisualLabel
  });

  // packages/ui/build-module/form/primitives/field/root.mjs
  var import_element12 = __toESM(require_element(), 1);
  var import_jsx_runtime42 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE9 = "data-wp-hash";
  function getRuntime9() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument9(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash9(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE9}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE9) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle9(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime9();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash9(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE9, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument9(targetDocument) {
    const runtime = getRuntime9();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle9(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle9(hash, css) {
    const runtime = getRuntime9();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle9(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle9("10f3806643", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
  }
  var resets_default3 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
  var DEFAULT_RENDER2 = (props) => /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(Stack, { ...props, direction: "column", gap: "sm" });
  var Root = (0, import_element12.forwardRef)(function Root2({ className, render = DEFAULT_RENDER2, ...restProps }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(
      index_parts_exports.Root,
      {
        ref,
        className: clsx_default(resets_default3["box-sizing"], className),
        render,
        ...restProps
      }
    );
  });

  // packages/ui/build-module/form/primitives/field/item.mjs
  var import_element13 = __toESM(require_element(), 1);
  var import_jsx_runtime43 = __toESM(require_jsx_runtime(), 1);
  var Item = (0, import_element13.forwardRef)(function Item2(props, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(index_parts_exports.Item, { ref, ...props });
  });

  // packages/ui/build-module/form/primitives/field/label.mjs
  var import_element14 = __toESM(require_element(), 1);
  var import_jsx_runtime44 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE10 = "data-wp-hash";
  function getRuntime10() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument10(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash10(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE10}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE10) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle10(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime10();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash10(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE10, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument10(targetDocument) {
    const runtime = getRuntime10();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle10(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle10(hash, css) {
    const runtime = getRuntime10();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle10(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle10("df33c48b2d", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._2d5ad850b2f90964__label{--wp-ui-field-label-line-height:var(--wpds-typography-line-height-xs,16px);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-xs,11px);font-weight:var(--wpds-typography-font-weight-emphasis,600);line-height:var(--wp-ui-field-label-line-height);text-transform:uppercase;&._17c4214649230bea__is-plain{font-size:var(--wpds-typography-font-size-md,13px);font-weight:var(--wpds-typography-font-weight-default,400);text-transform:none}}._08a3750500e0233f__description{--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);--_gcd-p-margin:0;text-wrap:pretty;color:var(--wpds-color-foreground-content-neutral-weak,#707070);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);line-height:var(--wpds-typography-line-height-xs,16px)}}}');
  }
  var field_default = { "label": "_2d5ad850b2f90964__label", "is-plain": "_17c4214649230bea__is-plain", "description": "_08a3750500e0233f__description" };
  var Label = (0, import_element14.forwardRef)(
    function Label2({ className, hideFromVision, variant, ...restProps }, ref) {
      const label = /* @__PURE__ */ (0, import_jsx_runtime44.jsx)(
        index_parts_exports.Label,
        {
          ref,
          className: clsx_default(
            field_default.label,
            variant && field_default[`is-${variant}`],
            className
          ),
          ...restProps
        }
      );
      if (hideFromVision) {
        return /* @__PURE__ */ (0, import_jsx_runtime44.jsx)(VisuallyHidden, { render: label });
      }
      return label;
    }
  );

  // packages/ui/build-module/form/primitives/field/description.mjs
  var import_element15 = __toESM(require_element(), 1);
  var import_jsx_runtime45 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE11 = "data-wp-hash";
  function getRuntime11() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument11(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash11(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE11}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE11) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle11(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime11();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash11(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE11, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument11(targetDocument) {
    const runtime = getRuntime11();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle11(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle11(hash, css) {
    const runtime = getRuntime11();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle11(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle11("e8e31009f5", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}");
  }
  var global_css_defense_default5 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li" };
  if (typeof process === "undefined" || true) {
    registerStyle11("df33c48b2d", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._2d5ad850b2f90964__label{--wp-ui-field-label-line-height:var(--wpds-typography-line-height-xs,16px);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-xs,11px);font-weight:var(--wpds-typography-font-weight-emphasis,600);line-height:var(--wp-ui-field-label-line-height);text-transform:uppercase;&._17c4214649230bea__is-plain{font-size:var(--wpds-typography-font-size-md,13px);font-weight:var(--wpds-typography-font-weight-default,400);text-transform:none}}._08a3750500e0233f__description{--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);--_gcd-p-margin:0;text-wrap:pretty;color:var(--wpds-color-foreground-content-neutral-weak,#707070);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);line-height:var(--wpds-typography-line-height-xs,16px)}}}');
  }
  var field_default2 = { "label": "_2d5ad850b2f90964__label", "is-plain": "_17c4214649230bea__is-plain", "description": "_08a3750500e0233f__description" };
  var Description = (0, import_element15.forwardRef)(function Description2({ className, ...restProps }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(
      index_parts_exports.Description,
      {
        ref,
        className: clsx_default(
          global_css_defense_default5.p,
          field_default2.description,
          className
        ),
        ...restProps
      }
    );
  });

  // packages/ui/build-module/form/primitives/field/details.mjs
  var import_element16 = __toESM(require_element(), 1);
  var import_i18n6 = __toESM(require_i18n(), 1);
  var import_jsx_runtime46 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE12 = "data-wp-hash";
  function getRuntime12() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument12(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash12(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE12}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE12) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle12(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime12();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash12(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE12, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument12(targetDocument) {
    const runtime = getRuntime12();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle12(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle12(hash, css) {
    const runtime = getRuntime12();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle12(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle12("df33c48b2d", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._2d5ad850b2f90964__label{--wp-ui-field-label-line-height:var(--wpds-typography-line-height-xs,16px);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-xs,11px);font-weight:var(--wpds-typography-font-weight-emphasis,600);line-height:var(--wp-ui-field-label-line-height);text-transform:uppercase;&._17c4214649230bea__is-plain{font-size:var(--wpds-typography-font-size-md,13px);font-weight:var(--wpds-typography-font-weight-default,400);text-transform:none}}._08a3750500e0233f__description{--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);--_gcd-p-margin:0;text-wrap:pretty;color:var(--wpds-color-foreground-content-neutral-weak,#707070);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);line-height:var(--wpds-typography-line-height-xs,16px)}}}');
  }
  var field_default3 = { "label": "_2d5ad850b2f90964__label", "is-plain": "_17c4214649230bea__is-plain", "description": "_08a3750500e0233f__description" };
  var Details = (0, import_element16.forwardRef)(
    function Details2({ className, ...restProps }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime46.jsxs)(import_jsx_runtime46.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(VisuallyHidden, { render: /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(index_parts_exports.Description, {}), children: (0, import_i18n6.__)("More details follow the field.") }),
        /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(
          "div",
          {
            ref,
            className: clsx_default(field_default3.description, className),
            ...restProps
          }
        )
      ] });
    }
  );

  // packages/ui/build-module/form/primitives/field/control.mjs
  var import_element17 = __toESM(require_element(), 1);
  var import_jsx_runtime47 = __toESM(require_jsx_runtime(), 1);
  var Control = (0, import_element17.forwardRef)(
    function Control2(props, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime47.jsx)(index_parts_exports.Control, { ref, ...props });
    }
  );

  // packages/ui/build-module/form/primitives/field/visual-label.mjs
  var import_element18 = __toESM(require_element(), 1);
  var STYLE_HASH_ATTRIBUTE13 = "data-wp-hash";
  function getRuntime13() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument13(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash13(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE13}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE13) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle13(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime13();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash13(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE13, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument13(targetDocument) {
    const runtime = getRuntime13();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle13(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle13(hash, css) {
    const runtime = getRuntime13();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle13(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle13("df33c48b2d", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._2d5ad850b2f90964__label{--wp-ui-field-label-line-height:var(--wpds-typography-line-height-xs,16px);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-xs,11px);font-weight:var(--wpds-typography-font-weight-emphasis,600);line-height:var(--wp-ui-field-label-line-height);text-transform:uppercase;&._17c4214649230bea__is-plain{font-size:var(--wpds-typography-font-size-md,13px);font-weight:var(--wpds-typography-font-weight-default,400);text-transform:none}}._08a3750500e0233f__description{--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);--_gcd-p-margin:0;text-wrap:pretty;color:var(--wpds-color-foreground-content-neutral-weak,#707070);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);line-height:var(--wpds-typography-line-height-xs,16px)}}}');
  }
  var field_default4 = { "label": "_2d5ad850b2f90964__label", "is-plain": "_17c4214649230bea__is-plain", "description": "_08a3750500e0233f__description" };
  var VisualLabel = (0, import_element18.forwardRef)(
    function VisualLabel2({ className, render, variant, ...restProps }, ref) {
      return useRender({
        defaultTagName: "span",
        render,
        ref,
        props: mergeProps(restProps, {
          className: clsx_default(
            field_default4.label,
            variant && field_default4[`is-${variant}`],
            className
          )
        })
      });
    }
  );
  VisualLabel.displayName = "Field.VisualLabel";

  // packages/ui/build-module/form/input-control/input-control.mjs
  var import_element19 = __toESM(require_element(), 1);
  var import_jsx_runtime48 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE14 = "data-wp-hash";
  function getRuntime14() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument14(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash14(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE14}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE14) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle14(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime14();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash14(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE14, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument14(targetDocument) {
    const runtime = getRuntime14();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle14(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle14(hash, css) {
    const runtime = getRuntime14();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle14(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle14("0c752c3d08", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer compositions{@supports (-webkit-hyphens:none) and (not (-moz-appearance:none)){.a9211a7e6af24bed__root input[type=date]:not([data-filled]):not(:focus),.a9211a7e6af24bed__root input[type=datetime-local]:not([data-filled]):not(:focus),.a9211a7e6af24bed__root input[type=time]:not([data-filled]):not(:focus){--_gcd-input-color:transparent;--_gcd-input-color-disabled:transparent;color:transparent;&:disabled::-webkit-datetime-edit-text{color:transparent}}}}}");
  }
  var style_default9 = { "root": "a9211a7e6af24bed__root" };
  var InputControl = (0, import_element19.forwardRef)(
    function InputControl2({
      className,
      label,
      description,
      details,
      hideLabelFromVision,
      ...restProps
    }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime48.jsxs)(field_exports.Root, { className: clsx_default(style_default9.root, className), children: [
        /* @__PURE__ */ (0, import_jsx_runtime48.jsx)(field_exports.Label, { hideFromVision: hideLabelFromVision, children: label }),
        /* @__PURE__ */ (0, import_jsx_runtime48.jsx)(Input3, { ref, ...restProps }),
        description && /* @__PURE__ */ (0, import_jsx_runtime48.jsx)(field_exports.Description, { children: description }),
        details && /* @__PURE__ */ (0, import_jsx_runtime48.jsx)(field_exports.Details, { children: details })
      ] });
    }
  );

  // packages/ui/build-module/form/with-validation/validated-input-control/validated-input-control.mjs
  var import_element20 = __toESM(require_element(), 1);
  var import_compose2 = __toESM(require_compose(), 1);
  var import_jsx_runtime49 = __toESM(require_jsx_runtime(), 1);
  var ValidatedInputControl = (0, import_element20.forwardRef)(function ValidatedInputControl2({ required, markWhenOptional, customValidity, ...restProps }, forwardedRef) {
    const validityTargetRef = (0, import_element20.useRef)(null);
    const mergedRefs = (0, import_compose2.useMergeRefs)([forwardedRef, validityTargetRef]);
    return /* @__PURE__ */ (0, import_jsx_runtime49.jsx)(
      ControlWithError,
      {
        required,
        markWhenOptional,
        customValidity,
        getValidityTarget: () => validityTargetRef.current,
        children: /* @__PURE__ */ (0, import_jsx_runtime49.jsx)(InputControl, { ref: mergedRefs, ...restProps })
      }
    );
  });

  // packages/ui/build-module/tabs/index.mjs
  var tabs_exports = {};
  __export(tabs_exports, {
    List: () => List,
    Panel: () => Panel,
    Root: () => Root3,
    Tab: () => Tab
  });

  // packages/ui/build-module/tabs/list.mjs
  var import_element21 = __toESM(require_element(), 1);
  var import_compose3 = __toESM(require_compose(), 1);
  var import_jsx_runtime50 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE15 = "data-wp-hash";
  function getRuntime15() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument15(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash15(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE15}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE15) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle15(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime15();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash15(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE15, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument15(targetDocument) {
    const runtime = getRuntime15();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle15(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle15(hash, css) {
    const runtime = getRuntime15();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle15(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle15("54ae3f4680", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._7313adbc8a112e90__tablist{--direction-start:left;--direction-end:right;align-items:stretch;display:flex;overflow-inline:auto;overscroll-behavior-inline:none;position:relative;&:dir(rtl){--direction-start:right;--direction-end:left}&[data-orientation=horizontal]{--fade-width:4rem;--fade-gradient-base:transparent 0%,#000 var(--fade-width);--fade-gradient-composed:var(--fade-gradient-base),#000 60%,transparent 50%;width:fit-content;&._9f2ac729c68a735a__is-overflowing-first{mask-image:linear-gradient(to var(--direction-end),var(--fade-gradient-base))}&._81c799c1f3cdd261__is-overflowing-last{mask-image:linear-gradient(to var(--direction-start),var(--fade-gradient-base))}&._9f2ac729c68a735a__is-overflowing-first._81c799c1f3cdd261__is-overflowing-last{mask-image:linear-gradient(to right,var(--fade-gradient-composed)),linear-gradient(to left,var(--fade-gradient-composed))}&._59228b5227f38a99__is-minimal-variant{gap:1rem}}&[data-orientation=vertical]{flex-direction:column}}._1c37dcfaa1ad8cda__indicator{@media not (prefers-reduced-motion){transition-duration:.2s;transition-property:translate,width,height,border-radius,border-block;transition-timing-function:ease-out}outline:2px solid transparent;outline-offset:-1px;pointer-events:none;position:absolute;&[data-orientation=horizontal]{background-color:var(--wpds-color-stroke-interactive-neutral-strong,#6e6e6e);bottom:0;height:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px));left:0;translate:var(--active-tab-left) 0;width:var(--active-tab-width);z-index:1}&[data-orientation=vertical]{background-color:var(--wpds-color-background-interactive-neutral-weak-active,#ededed);border-radius:var(--wpds-border-radius-sm,2px);height:var(--active-tab-height);left:50%;top:0;translate:-50% var(--active-tab-top);width:100%;z-index:0}._7313adbc8a112e90__tablist[data-select-on-move=true]:has(:focus-visible)\n			&[data-orientation=vertical]{border:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9));box-sizing:border-box}}.a5fd8814f195aa5e__tab{align-items:center;background:transparent;border:none;border-radius:0;box-shadow:none;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);cursor:var(--wpds-cursor-control,pointer);display:flex;flex:1 0 auto;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);font-weight:400;line-height:1.2;outline:none;padding:0;position:relative;white-space:nowrap;z-index:1;&[data-disabled]{color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);cursor:default;@media (forced-colors:active){color:GrayText}}&:not([data-disabled]):is(:hover,:focus-visible){color:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e)}&:after{border-radius:var(--wpds-border-radius-sm,2px);opacity:0;outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9));pointer-events:none;position:absolute;z-index:-1;@media not (prefers-reduced-motion){transition:opacity .1s linear}}&:focus-visible:after{opacity:1}[data-orientation=horizontal] &{height:48px;padding-inline:var(--wpds-dimension-padding-lg,16px);scroll-margin:24px;&:after{content:"";inset:var(--wpds-dimension-padding-md,12px)}}._59228b5227f38a99__is-minimal-variant[data-orientation=horizontal] &{padding-inline:0;&:after{inset-inline:round(up,var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)),1px)}}[data-orientation=vertical] &{min-height:var(--wpds-dimension-size-lg,40px);padding:var(--wpds-dimension-padding-sm,8px) var(--wpds-dimension-padding-md,12px)}[data-orientation=vertical][data-select-on-move=false] &:after{content:"";inset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}._5dfc77e6edd345d4__tab-children{align-items:center;display:flex;flex-grow:1;[data-orientation=horizontal] &{justify-content:center}[data-orientation=vertical] &{justify-content:start}}._4a20e969d15e5ac1__tab-chevron{flex-shrink:0;margin-inline-end:calc(var(--wpds-dimension-gap-xs, 4px)*-1);opacity:0;[data-orientation=horizontal] &{display:none}[role=tab]:is([aria-selected=true],:focus-visible,:hover) &{opacity:1}@media not (prefers-reduced-motion){[data-select-on-move=true]\n				[role=tab]:is([aria-selected=true])\n				&{transition:opacity .15s linear .15s}}&:dir(rtl){rotate:180deg}}}}');
  }
  var style_default10 = { "tablist": "_7313adbc8a112e90__tablist", "is-overflowing-first": "_9f2ac729c68a735a__is-overflowing-first", "is-overflowing-last": "_81c799c1f3cdd261__is-overflowing-last", "is-minimal-variant": "_59228b5227f38a99__is-minimal-variant", "indicator": "_1c37dcfaa1ad8cda__indicator", "tab": "a5fd8814f195aa5e__tab", "tab-children": "_5dfc77e6edd345d4__tab-children", "tab-chevron": "_4a20e969d15e5ac1__tab-chevron" };
  var SCROLL_EPSILON = 1;
  var List = (0, import_element21.forwardRef)(
    function TabList({
      children,
      variant = "default",
      className,
      activateOnFocus,
      ...otherProps
    }, forwardedRef) {
      const [listEl, setListEl] = (0, import_element21.useState)(null);
      const [overflow, setOverflow] = (0, import_element21.useState)({
        first: false,
        last: false,
        isScrolling: false
      });
      (0, import_element21.useEffect)(() => {
        if (!listEl) {
          return;
        }
        const measureOverflow = () => {
          const { scrollWidth, clientWidth, scrollLeft } = listEl;
          const maxScroll = Math.max(scrollWidth - clientWidth, 0);
          const direction = listEl.dir || (typeof window !== "undefined" ? window.getComputedStyle(listEl).direction : "ltr");
          const scrollFromStart = direction === "rtl" && scrollLeft < 0 ? (
            // In RTL layouts, scrollLeft is typically 0 at the visual "start"
            // (right edge) and becomes negative toward the "end" (left edge).
            // Normalize value for correct first/last detection logic.
            -scrollLeft
          ) : scrollLeft;
          const next = {
            first: scrollFromStart > SCROLL_EPSILON,
            last: scrollFromStart < maxScroll - SCROLL_EPSILON,
            isScrolling: scrollWidth > clientWidth
          };
          setOverflow(
            (prev) => prev.first === next.first && prev.last === next.last && prev.isScrolling === next.isScrolling ? prev : next
          );
        };
        const resizeObserver = new ResizeObserver(measureOverflow);
        const observeTabs = () => {
          resizeObserver.disconnect();
          resizeObserver.observe(listEl);
          listEl.querySelectorAll('[role="tab"]').forEach((tab) => resizeObserver.observe(tab));
        };
        const mutationObserver = new MutationObserver(observeTabs);
        mutationObserver.observe(listEl, {
          childList: true,
          subtree: true
        });
        let scrollTick = false;
        const throttleMeasureOverflowOnScroll = () => {
          if (!scrollTick) {
            requestAnimationFrame(() => {
              measureOverflow();
              scrollTick = false;
            });
            scrollTick = true;
          }
        };
        listEl.addEventListener(
          "scroll",
          throttleMeasureOverflowOnScroll,
          { passive: true }
        );
        observeTabs();
        measureOverflow();
        return () => {
          listEl.removeEventListener(
            "scroll",
            throttleMeasureOverflowOnScroll
          );
          resizeObserver.disconnect();
          mutationObserver.disconnect();
        };
      }, [listEl]);
      const mergedListRef = (0, import_compose3.useMergeRefs)([
        forwardedRef,
        (el) => setListEl(el)
      ]);
      return /* @__PURE__ */ (0, import_jsx_runtime50.jsxs)(
        index_parts_exports2.List,
        {
          ref: mergedListRef,
          activateOnFocus,
          "data-select-on-move": activateOnFocus ? "true" : "false",
          className: clsx_default(
            style_default10.tablist,
            overflow.first && style_default10["is-overflowing-first"],
            overflow.last && style_default10["is-overflowing-last"],
            style_default10[`is-${variant}-variant`],
            className
          ),
          ...otherProps,
          tabIndex: otherProps.tabIndex ?? (overflow.isScrolling ? -1 : void 0),
          children: [
            children,
            /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(index_parts_exports2.Indicator, { className: style_default10.indicator })
          ]
        }
      );
    }
  );

  // packages/ui/build-module/tabs/panel.mjs
  var import_element23 = __toESM(require_element(), 1);

  // packages/ui/build-module/tabs/context.mjs
  var import_element22 = __toESM(require_element(), 1);
  var import_jsx_runtime51 = __toESM(require_jsx_runtime(), 1);
  var VALIDATION_ENABLED = true;
  var TabsValidationContext = VALIDATION_ENABLED ? (0, import_element22.createContext)(null) : null;
  function useRegisterTabDev() {
    const context = (0, import_element22.useContext)(TabsValidationContext);
    (0, import_element22.useEffect)(() => {
      if (context) {
        return context.registerTab();
      }
      return void 0;
    }, [context]);
  }
  function useRegisterTabProd() {
  }
  var useRegisterTab = VALIDATION_ENABLED ? useRegisterTabDev : useRegisterTabProd;
  function useRegisterPanelDev() {
    const context = (0, import_element22.useContext)(TabsValidationContext);
    (0, import_element22.useEffect)(() => {
      if (context) {
        return context.registerPanel();
      }
      return void 0;
    }, [context]);
  }
  function useRegisterPanelProd() {
  }
  var useRegisterPanel = VALIDATION_ENABLED ? useRegisterPanelDev : useRegisterPanelProd;
  function TabsValidationProviderDev({
    children
  }) {
    const tabCountRef = (0, import_element22.useRef)(0);
    const panelCountRef = (0, import_element22.useRef)(0);
    const scheduleValidation = useScheduleValidation(() => {
      const tabCount = tabCountRef.current;
      const panelCount = panelCountRef.current;
      if (tabCount !== panelCount) {
        throw new Error(
          `Tabs: Tab/Panel count mismatch (${tabCount} Tabs, ${panelCount} Panels). Each Tab must be associated with exactly one Panel. Mismatched or missing associations can break screen reader navigation and violate WAI-ARIA Tabs pattern requirements.`
        );
      }
    });
    const registerTab = (0, import_element22.useCallback)(() => {
      tabCountRef.current += 1;
      scheduleValidation();
      return () => {
        tabCountRef.current -= 1;
        scheduleValidation();
      };
    }, [scheduleValidation]);
    const registerPanel = (0, import_element22.useCallback)(() => {
      panelCountRef.current += 1;
      scheduleValidation();
      return () => {
        panelCountRef.current -= 1;
        scheduleValidation();
      };
    }, [scheduleValidation]);
    const contextValue = (0, import_element22.useMemo)(
      () => ({
        registerTab,
        registerPanel
      }),
      [registerTab, registerPanel]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime51.jsx)(TabsValidationContext.Provider, { value: contextValue, children });
  }
  function TabsValidationProviderProd({
    children
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime51.jsx)(import_jsx_runtime51.Fragment, { children });
  }
  var TabsValidationProvider = VALIDATION_ENABLED ? TabsValidationProviderDev : TabsValidationProviderProd;

  // packages/ui/build-module/tabs/panel.mjs
  var import_jsx_runtime52 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE16 = "data-wp-hash";
  function getRuntime16() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument16(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash16(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE16}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE16) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle16(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime16();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash16(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE16, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument16(targetDocument) {
    const runtime = getRuntime16();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle16(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle16(hash, css) {
    const runtime = getRuntime16();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle16(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle16("e8e31009f5", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}");
  }
  var global_css_defense_default6 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li" };
  if (typeof process === "undefined" || true) {
    registerStyle16("08122b3d53", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{.af79fb116edb0dd7__outset-ring--focus:focus,.dfcfdc28396e5d98__outset-ring--focus-visible:focus-visible,.e5cd9ee879f6403a__outset-ring--focus-within:focus-within,:focus-visible ._81935a08e952f267__outset-ring--focus-parent-visible{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within,.abc777e9713fa711__outset-ring--focus-except-active:focus{outline:none}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.abc777e9713fa711__outset-ring--focus-except-active:focus:not(:active){--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}}");
  }
  var focus_module_default3 = { "outset-ring--focus": "af79fb116edb0dd7__outset-ring--focus", "outset-ring--focus-visible": "dfcfdc28396e5d98__outset-ring--focus-visible", "outset-ring--focus-within": "e5cd9ee879f6403a__outset-ring--focus-within", "outset-ring--focus-parent-visible": "_81935a08e952f267__outset-ring--focus-parent-visible", "outset-ring--focus-except-active": "abc777e9713fa711__outset-ring--focus-except-active", "outset-ring--focus-within-except-active": "_3c9f5ee9fc9c136d__outset-ring--focus-within-except-active" };
  var Panel = (0, import_element23.forwardRef)(
    function TabPanel({ className, ...otherProps }, forwardedRef) {
      useRegisterPanel();
      return /* @__PURE__ */ (0, import_jsx_runtime52.jsx)(
        index_parts_exports2.Panel,
        {
          ref: forwardedRef,
          className: clsx_default(
            global_css_defense_default6.div,
            focus_module_default3["outset-ring--focus-visible"],
            className
          ),
          ...otherProps
        }
      );
    }
  );

  // packages/ui/build-module/tabs/root.mjs
  var import_element24 = __toESM(require_element(), 1);
  var import_jsx_runtime53 = __toESM(require_jsx_runtime(), 1);
  var Root3 = (0, import_element24.forwardRef)(
    function TabsRoot3({ ...otherProps }, forwardedRef) {
      return /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(DirectionProvider3, { children: /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(TabsValidationProvider, { children: /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(index_parts_exports2.Root, { ref: forwardedRef, ...otherProps }) }) });
    }
  );

  // packages/ui/build-module/tabs/tab.mjs
  var import_element25 = __toESM(require_element(), 1);
  var import_jsx_runtime54 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE17 = "data-wp-hash";
  function getRuntime17() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument17(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash17(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE17}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE17) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle17(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime17();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash17(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE17, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument17(targetDocument) {
    const runtime = getRuntime17();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle17(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle17(hash, css) {
    const runtime = getRuntime17();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle17(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle17("54ae3f4680", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._7313adbc8a112e90__tablist{--direction-start:left;--direction-end:right;align-items:stretch;display:flex;overflow-inline:auto;overscroll-behavior-inline:none;position:relative;&:dir(rtl){--direction-start:right;--direction-end:left}&[data-orientation=horizontal]{--fade-width:4rem;--fade-gradient-base:transparent 0%,#000 var(--fade-width);--fade-gradient-composed:var(--fade-gradient-base),#000 60%,transparent 50%;width:fit-content;&._9f2ac729c68a735a__is-overflowing-first{mask-image:linear-gradient(to var(--direction-end),var(--fade-gradient-base))}&._81c799c1f3cdd261__is-overflowing-last{mask-image:linear-gradient(to var(--direction-start),var(--fade-gradient-base))}&._9f2ac729c68a735a__is-overflowing-first._81c799c1f3cdd261__is-overflowing-last{mask-image:linear-gradient(to right,var(--fade-gradient-composed)),linear-gradient(to left,var(--fade-gradient-composed))}&._59228b5227f38a99__is-minimal-variant{gap:1rem}}&[data-orientation=vertical]{flex-direction:column}}._1c37dcfaa1ad8cda__indicator{@media not (prefers-reduced-motion){transition-duration:.2s;transition-property:translate,width,height,border-radius,border-block;transition-timing-function:ease-out}outline:2px solid transparent;outline-offset:-1px;pointer-events:none;position:absolute;&[data-orientation=horizontal]{background-color:var(--wpds-color-stroke-interactive-neutral-strong,#6e6e6e);bottom:0;height:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px));left:0;translate:var(--active-tab-left) 0;width:var(--active-tab-width);z-index:1}&[data-orientation=vertical]{background-color:var(--wpds-color-background-interactive-neutral-weak-active,#ededed);border-radius:var(--wpds-border-radius-sm,2px);height:var(--active-tab-height);left:50%;top:0;translate:-50% var(--active-tab-top);width:100%;z-index:0}._7313adbc8a112e90__tablist[data-select-on-move=true]:has(:focus-visible)\n			&[data-orientation=vertical]{border:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9));box-sizing:border-box}}.a5fd8814f195aa5e__tab{align-items:center;background:transparent;border:none;border-radius:0;box-shadow:none;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);cursor:var(--wpds-cursor-control,pointer);display:flex;flex:1 0 auto;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);font-weight:400;line-height:1.2;outline:none;padding:0;position:relative;white-space:nowrap;z-index:1;&[data-disabled]{color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);cursor:default;@media (forced-colors:active){color:GrayText}}&:not([data-disabled]):is(:hover,:focus-visible){color:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e)}&:after{border-radius:var(--wpds-border-radius-sm,2px);opacity:0;outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9));pointer-events:none;position:absolute;z-index:-1;@media not (prefers-reduced-motion){transition:opacity .1s linear}}&:focus-visible:after{opacity:1}[data-orientation=horizontal] &{height:48px;padding-inline:var(--wpds-dimension-padding-lg,16px);scroll-margin:24px;&:after{content:"";inset:var(--wpds-dimension-padding-md,12px)}}._59228b5227f38a99__is-minimal-variant[data-orientation=horizontal] &{padding-inline:0;&:after{inset-inline:round(up,var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)),1px)}}[data-orientation=vertical] &{min-height:var(--wpds-dimension-size-lg,40px);padding:var(--wpds-dimension-padding-sm,8px) var(--wpds-dimension-padding-md,12px)}[data-orientation=vertical][data-select-on-move=false] &:after{content:"";inset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}._5dfc77e6edd345d4__tab-children{align-items:center;display:flex;flex-grow:1;[data-orientation=horizontal] &{justify-content:center}[data-orientation=vertical] &{justify-content:start}}._4a20e969d15e5ac1__tab-chevron{flex-shrink:0;margin-inline-end:calc(var(--wpds-dimension-gap-xs, 4px)*-1);opacity:0;[data-orientation=horizontal] &{display:none}[role=tab]:is([aria-selected=true],:focus-visible,:hover) &{opacity:1}@media not (prefers-reduced-motion){[data-select-on-move=true]\n				[role=tab]:is([aria-selected=true])\n				&{transition:opacity .15s linear .15s}}&:dir(rtl){rotate:180deg}}}}');
  }
  var style_default11 = { "tablist": "_7313adbc8a112e90__tablist", "is-overflowing-first": "_9f2ac729c68a735a__is-overflowing-first", "is-overflowing-last": "_81c799c1f3cdd261__is-overflowing-last", "is-minimal-variant": "_59228b5227f38a99__is-minimal-variant", "indicator": "_1c37dcfaa1ad8cda__indicator", "tab": "a5fd8814f195aa5e__tab", "tab-children": "_5dfc77e6edd345d4__tab-children", "tab-chevron": "_4a20e969d15e5ac1__tab-chevron" };
  var Tab = (0, import_element25.forwardRef)(function Tab2({ className, children, ...otherProps }, forwardedRef) {
    useRegisterTab();
    return /* @__PURE__ */ (0, import_jsx_runtime54.jsxs)(
      index_parts_exports2.Tab,
      {
        ref: forwardedRef,
        className: clsx_default(style_default11.tab, className),
        ...otherProps,
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime54.jsx)("span", { className: style_default11["tab-children"], children }),
          /* @__PURE__ */ (0, import_jsx_runtime54.jsx)(Icon, { icon: chevron_right_default, className: style_default11["tab-chevron"] })
        ]
      }
    );
  });

  // packages/format-library/build-module/image/index.mjs
  var import_i18n7 = __toESM(require_i18n(), 1);
  var import_element26 = __toESM(require_element(), 1);
  var import_rich_text3 = __toESM(require_rich_text(), 1);
  var import_block_editor3 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime55 = __toESM(require_jsx_runtime(), 1);
  var ALLOWED_MEDIA_TYPES = ["image"];
  var name3 = "core/image";
  var title3 = (0, import_i18n7.__)("Inline image");
  function getCurrentImageId(activeObjectAttributes) {
    if (!activeObjectAttributes?.className) {
      return void 0;
    }
    const [, id] = activeObjectAttributes.className.match(/wp-image-(\d+)/) ?? [];
    return id ? parseInt(id, 10) : void 0;
  }
  var image = {
    name: name3,
    title: title3,
    keywords: [(0, import_i18n7.__)("photo"), (0, import_i18n7.__)("media")],
    object: true,
    tagName: "img",
    className: null,
    attributes: {
      className: "class",
      style: "style",
      url: "src",
      alt: "alt"
    },
    edit: Edit
  };
  function InlineUI({
    value,
    onChange,
    activeObjectAttributes,
    contentRef
  }) {
    const style = activeObjectAttributes?.style;
    const alt = activeObjectAttributes?.alt;
    const width = style?.replace(/\D/g, "");
    const [editedWidth, setEditedWidth] = (0, import_element26.useState)(width);
    const [editedAlt, setEditedAlt] = (0, import_element26.useState)(alt);
    const hasChanged = editedWidth !== width || editedAlt !== alt;
    const popoverAnchor = (0, import_rich_text3.useAnchor)({
      // eslint-disable-next-line react-hooks/refs
      editableContentElement: contentRef.current,
      settings: image
    });
    return /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
      import_components.Popover,
      {
        focusOnMount: false,
        anchor: popoverAnchor,
        className: "block-editor-format-toolbar__image-popover",
        children: /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
          "form",
          {
            className: "block-editor-format-toolbar__image-container-content",
            onSubmit: (event) => {
              const newReplacements = value.replacements.slice();
              newReplacements[value.start] = {
                type: name3,
                attributes: {
                  ...activeObjectAttributes,
                  style: editedWidth ? `width: ${editedWidth}px;` : "",
                  alt: editedAlt ?? ""
                }
              };
              onChange({
                ...value,
                replacements: newReplacements
              });
              event.preventDefault();
            },
            children: /* @__PURE__ */ (0, import_jsx_runtime55.jsxs)(Stack, { direction: "column", gap: "lg", children: [
              /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
                import_components.__experimentalNumberControl,
                {
                  label: (0, import_i18n7.__)("Width"),
                  value: editedWidth,
                  min: 1,
                  onChange: (newWidth) => {
                    setEditedWidth(newWidth);
                  }
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
                import_components.TextareaControl,
                {
                  label: (0, import_i18n7.__)("Alternative text"),
                  value: editedAlt ?? "",
                  onChange: (newAlt) => {
                    setEditedAlt(newAlt);
                  },
                  help: /* @__PURE__ */ (0, import_jsx_runtime55.jsxs)(import_jsx_runtime55.Fragment, { children: [
                    /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
                      Link,
                      {
                        openInNewTab: true,
                        href: (
                          // translators: Localized tutorial, if one exists. W3C Web Accessibility Initiative link has list of existing translations.
                          (0, import_i18n7.__)(
                            "https://www.w3.org/WAI/tutorials/images/decision-tree/"
                          )
                        ),
                        children: (0, import_i18n7.__)(
                          "Describe the purpose of the image."
                        )
                      }
                    ),
                    /* @__PURE__ */ (0, import_jsx_runtime55.jsx)("br", {}),
                    (0, import_i18n7.__)("Leave empty if decorative.")
                  ] })
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(Stack, { justify: "right", children: /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
                import_components.Button,
                {
                  disabled: !hasChanged,
                  accessibleWhenDisabled: true,
                  variant: "primary",
                  type: "submit",
                  size: "compact",
                  children: (0, import_i18n7.__)("Apply")
                }
              ) })
            ] })
          }
        )
      }
    );
  }
  function Edit({
    value,
    onChange,
    onFocus,
    isObjectActive,
    activeObjectAttributes,
    contentRef
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime55.jsxs)(import_block_editor3.MediaUploadCheck, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
        import_block_editor3.MediaUpload,
        {
          allowedTypes: ALLOWED_MEDIA_TYPES,
          value: getCurrentImageId(activeObjectAttributes),
          onSelect: ({
            id,
            url,
            alt,
            width: imgWidth
          }) => {
            onChange(
              (0, import_rich_text3.insertObject)(value, {
                type: name3,
                attributes: {
                  className: `wp-image-${id}`,
                  style: `width: ${Math.min(
                    imgWidth,
                    150
                  )}px;`,
                  url,
                  alt
                }
              })
            );
            onFocus();
          },
          render: ({ open }) => /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
            import_block_editor3.RichTextToolbarButton,
            {
              icon: inline_image_default,
              title: isObjectActive ? (0, import_i18n7.__)("Replace image") : title3,
              onClick: open,
              isActive: isObjectActive
            }
          )
        }
      ),
      isObjectActive && /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
        InlineUI,
        {
          value,
          onChange,
          activeObjectAttributes,
          contentRef
        }
      )
    ] });
  }

  // packages/format-library/build-module/italic/index.mjs
  var import_i18n8 = __toESM(require_i18n(), 1);
  var import_rich_text4 = __toESM(require_rich_text(), 1);
  var import_block_editor4 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime56 = __toESM(require_jsx_runtime(), 1);
  var name4 = "core/italic";
  var title4 = (0, import_i18n8.__)("Italic");
  var italic = {
    name: name4,
    title: title4,
    tagName: "em",
    className: null,
    edit({
      isActive,
      value,
      onChange,
      onFocus,
      isVisible = true
    }) {
      function onToggle() {
        onChange((0, import_rich_text4.toggleFormat)(value, { type: name4, title: title4 }));
      }
      function onClick() {
        onChange((0, import_rich_text4.toggleFormat)(value, { type: name4, title: title4 }));
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime56.jsxs)(import_jsx_runtime56.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
          import_block_editor4.RichTextShortcut,
          {
            type: "primary",
            character: "i",
            onUse: onToggle
          }
        ),
        isVisible && /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
          import_block_editor4.RichTextToolbarButton,
          {
            name: "italic",
            icon: format_italic_default,
            title: title4,
            onClick,
            isActive,
            shortcutType: "primary",
            shortcutCharacter: "i"
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
          import_block_editor4.__unstableRichTextInputEvent,
          {
            inputType: "formatItalic",
            onInput: onToggle
          }
        )
      ] });
    }
  };

  // packages/format-library/build-module/link/index.mjs
  var import_i18n11 = __toESM(require_i18n(), 1);
  var import_element29 = __toESM(require_element(), 1);
  var import_rich_text6 = __toESM(require_rich_text(), 1);
  var import_url3 = __toESM(require_url(), 1);
  var import_block_editor6 = __toESM(require_block_editor(), 1);
  var import_html_entities = __toESM(require_html_entities(), 1);
  var import_a11y2 = __toESM(require_a11y(), 1);

  // packages/format-library/build-module/link/inline.mjs
  var import_element28 = __toESM(require_element(), 1);
  var import_i18n10 = __toESM(require_i18n(), 1);
  var import_a11y = __toESM(require_a11y(), 1);
  var import_components3 = __toESM(require_components(), 1);
  var import_url2 = __toESM(require_url(), 1);
  var import_rich_text5 = __toESM(require_rich_text(), 1);
  var import_block_editor5 = __toESM(require_block_editor(), 1);
  var import_data = __toESM(require_data(), 1);

  // packages/format-library/build-module/link/utils.mjs
  var import_url = __toESM(require_url(), 1);
  function isValidHref(href) {
    if (!href) {
      return false;
    }
    const trimmedHref = href.trim();
    if (!trimmedHref) {
      return false;
    }
    if (/^\S+:/.test(trimmedHref)) {
      const protocol = (0, import_url.getProtocol)(trimmedHref);
      if (!protocol || !(0, import_url.isValidProtocol)(protocol)) {
        return false;
      }
      if (protocol.startsWith("http") && !/^https?:\/\/[^\/\s]/i.test(trimmedHref)) {
        return false;
      }
      const authority = (0, import_url.getAuthority)(trimmedHref);
      if (!authority || !(0, import_url.isValidAuthority)(authority)) {
        return false;
      }
      const path = (0, import_url.getPath)(trimmedHref);
      if (path && !(0, import_url.isValidPath)(path)) {
        return false;
      }
      const queryString = (0, import_url.getQueryString)(trimmedHref);
      if (queryString && !(0, import_url.isValidQueryString)(queryString)) {
        return false;
      }
      const fragment = (0, import_url.getFragment)(trimmedHref);
      if (fragment && !(0, import_url.isValidFragment)(fragment)) {
        return false;
      }
    }
    if (trimmedHref.startsWith("#") && !(0, import_url.isValidFragment)(trimmedHref)) {
      return false;
    }
    return true;
  }
  function createLinkFormat({
    url,
    type,
    id,
    opensInNewWindow,
    nofollow,
    cssClasses
  }) {
    const format = {
      type: "core/link",
      attributes: {
        url
      }
    };
    if (type) {
      format.attributes.type = type;
    }
    if (id) {
      format.attributes.id = id;
    }
    if (opensInNewWindow) {
      format.attributes.target = "_blank";
      format.attributes.rel = format.attributes.rel ? format.attributes.rel + " noopener" : "noopener";
    }
    if (nofollow) {
      format.attributes.rel = format.attributes.rel ? format.attributes.rel + " nofollow" : "nofollow";
    }
    const trimmedCssClasses = cssClasses?.trim();
    if (trimmedCssClasses?.length) {
      format.attributes.class = trimmedCssClasses;
    }
    return format;
  }
  function getFormatBoundary(value, format, startIndex = value.start, endIndex = value.end) {
    const EMPTY_BOUNDARIES = {
      start: void 0,
      end: void 0
    };
    const { formats } = value;
    let targetFormat;
    let initialIndex;
    if (!formats?.length) {
      return EMPTY_BOUNDARIES;
    }
    const newFormats = formats.slice();
    const formatAtStart = newFormats[startIndex]?.find(
      ({ type }) => type === format.type
    );
    const formatAtEnd = newFormats[endIndex]?.find(
      ({ type }) => type === format.type
    );
    const formatAtEndMinusOne = newFormats[endIndex - 1]?.find(
      ({ type }) => type === format.type
    );
    if (!!formatAtStart) {
      targetFormat = formatAtStart;
      initialIndex = startIndex;
    } else if (!!formatAtEnd) {
      targetFormat = formatAtEnd;
      initialIndex = endIndex;
    } else if (!!formatAtEndMinusOne) {
      targetFormat = formatAtEndMinusOne;
      initialIndex = endIndex - 1;
    } else {
      return EMPTY_BOUNDARIES;
    }
    const index2 = newFormats[initialIndex].indexOf(targetFormat);
    startIndex = walkToStart(newFormats, initialIndex, targetFormat, index2);
    endIndex = walkToEnd(newFormats, initialIndex, targetFormat, index2);
    startIndex = startIndex < 0 ? 0 : startIndex;
    return {
      start: startIndex,
      end: endIndex + 1
    };
  }
  function walkToBoundary(formats, initialIndex, targetFormatRef, formatIndex, direction) {
    let index2 = initialIndex;
    const directions = {
      forwards: 1,
      backwards: -1
    };
    const directionIncrement = directions[direction] || 1;
    const inverseDirectionIncrement = directionIncrement * -1;
    while (formats[index2] && formats[index2][formatIndex] === targetFormatRef) {
      index2 = index2 + directionIncrement;
    }
    index2 = index2 + inverseDirectionIncrement;
    return index2;
  }
  var walkToStart = (...args) => walkToBoundary(...args, "backwards");
  var walkToEnd = (...args) => walkToBoundary(...args, "forwards");

  // packages/format-library/build-module/link/css-classes-setting.mjs
  var import_element27 = __toESM(require_element(), 1);
  var import_compose4 = __toESM(require_compose(), 1);
  var import_i18n9 = __toESM(require_i18n(), 1);
  var import_components2 = __toESM(require_components(), 1);
  var import_jsx_runtime57 = __toESM(require_jsx_runtime(), 1);
  var CSSClassesSettingComponent = ({
    setting,
    value,
    onChange
  }) => {
    const hasValue = !!value?.cssClasses?.length;
    const [isSettingActive, setIsSettingActive] = (0, import_element27.useState)(hasValue);
    const instanceId = (0, import_compose4.useInstanceId)(CSSClassesSettingComponent);
    const controlledRegionId = `css-classes-setting-${instanceId}`;
    const handleSettingChange = (newValue) => {
      const sanitizedValue = typeof newValue === "string" ? newValue.replace(/,/g, " ").replace(/\s+/g, " ").trim() : newValue;
      onChange({
        ...value,
        [setting.id]: sanitizedValue
      });
    };
    const handleCheckboxChange = () => {
      if (isSettingActive) {
        if (hasValue) {
          handleSettingChange("");
        }
        setIsSettingActive(false);
      } else {
        setIsSettingActive(true);
      }
    };
    return /* @__PURE__ */ (0, import_jsx_runtime57.jsxs)("fieldset", { children: [
      /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(VisuallyHidden, { render: /* @__PURE__ */ (0, import_jsx_runtime57.jsx)("legend", {}), children: setting.title }),
      /* @__PURE__ */ (0, import_jsx_runtime57.jsxs)(Stack, { direction: "column", gap: "md", children: [
        /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
          import_components2.CheckboxControl,
          {
            label: setting.title,
            onChange: handleCheckboxChange,
            checked: isSettingActive || hasValue,
            "aria-expanded": isSettingActive,
            "aria-controls": isSettingActive ? controlledRegionId : void 0
          }
        ),
        isSettingActive && /* @__PURE__ */ (0, import_jsx_runtime57.jsx)("div", { id: controlledRegionId, children: /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
          import_components2.__experimentalInputControl,
          {
            label: (0, import_i18n9.__)("CSS classes"),
            value: value?.cssClasses,
            onChange: handleSettingChange,
            help: (0, import_i18n9.__)(
              "Separate multiple classes with spaces."
            ),
            __unstableInputWidth: "100%"
          }
        ) })
      ] })
    ] });
  };
  var css_classes_setting_default = CSSClassesSettingComponent;

  // packages/format-library/build-module/link/inline.mjs
  var import_jsx_runtime58 = __toESM(require_jsx_runtime(), 1);
  var LINK_SETTINGS = [
    ...import_block_editor5.LinkControl.DEFAULT_LINK_SETTINGS,
    {
      id: "nofollow",
      title: (0, import_i18n10.__)("Mark as nofollow")
    },
    {
      id: "cssClasses",
      title: (0, import_i18n10.__)("Additional CSS class(es)"),
      render: (setting, value, onChange) => /* @__PURE__ */ (0, import_jsx_runtime58.jsx)(
        css_classes_setting_default,
        {
          setting,
          value,
          onChange
        }
      )
    }
  ];
  function InlineLinkUI({
    isActive,
    activeAttributes,
    value,
    onChange,
    onFocusOutside,
    stopAddingLink,
    contentRef,
    focusOnMount
  }) {
    const richLinkTextValue = getRichTextValueFromSelection(value, isActive);
    const richTextText = richLinkTextValue.text;
    const { selectionChange } = (0, import_data.useDispatch)(import_block_editor5.store);
    const { createPageEntity, userCanCreatePages, selectionStart } = (0, import_data.useSelect)(
      (select) => {
        const { getSettings, getSelectionStart } = select(import_block_editor5.store);
        const _settings = getSettings();
        return {
          createPageEntity: _settings.__experimentalCreatePageEntity,
          userCanCreatePages: _settings.__experimentalUserCanCreatePages,
          selectionStart: getSelectionStart()
        };
      },
      []
    );
    const linkValue = (0, import_element28.useMemo)(
      () => ({
        url: activeAttributes.url,
        type: activeAttributes.type,
        id: activeAttributes.id,
        opensInNewTab: activeAttributes.target === "_blank",
        nofollow: activeAttributes.rel?.includes("nofollow"),
        title: richTextText,
        cssClasses: activeAttributes.class
      }),
      [
        activeAttributes.class,
        activeAttributes.id,
        activeAttributes.rel,
        activeAttributes.target,
        activeAttributes.type,
        activeAttributes.url,
        richTextText
      ]
    );
    function removeLink() {
      const newValue = (0, import_rich_text5.removeFormat)(value, "core/link");
      onChange(newValue);
      stopAddingLink();
      (0, import_a11y.speak)((0, import_i18n10.__)("Link removed."), "assertive");
    }
    function onChangeLink(nextValue) {
      const hasLink = linkValue?.url;
      const isNewLink = !hasLink;
      nextValue = {
        ...linkValue,
        ...nextValue
      };
      const newUrl = (0, import_url2.prependHTTPS)(nextValue?.url ?? "");
      const linkFormat = createLinkFormat({
        url: newUrl,
        type: nextValue.type,
        id: nextValue.id !== void 0 && nextValue.id !== null ? String(nextValue.id) : void 0,
        opensInNewWindow: nextValue.opensInNewTab,
        nofollow: nextValue.nofollow,
        cssClasses: nextValue.cssClasses
      });
      const newText = nextValue.title || newUrl;
      let newValue;
      if ((0, import_rich_text5.isCollapsed)(value) && !isActive) {
        const inserted = (0, import_rich_text5.insert)(value, newText);
        newValue = (0, import_rich_text5.applyFormat)(
          inserted,
          linkFormat,
          value.start,
          value.start + newText.length
        );
        onChange(newValue);
        stopAddingLink();
        selectionChange({
          clientId: selectionStart.clientId,
          identifier: selectionStart.attributeKey,
          start: value.start + newText.length + 1
        });
        return;
      } else if (newText === richTextText) {
        const boundary = getFormatBoundary(value, {
          type: "core/link"
        });
        newValue = (0, import_rich_text5.applyFormat)(
          value,
          linkFormat,
          boundary.start,
          boundary.end
        );
      } else {
        newValue = (0, import_rich_text5.create)({ text: newText });
        newValue = (0, import_rich_text5.applyFormat)(newValue, linkFormat, 0, newText.length);
        const boundary = getFormatBoundary(value, {
          type: "core/link"
        });
        const splitValue = (0, import_rich_text5.split)(value, boundary.start, boundary.start);
        if (!splitValue) {
          return;
        }
        const [valBefore, valAfter] = splitValue;
        const newValAfter = (0, import_rich_text5.replace)(valAfter, richTextText, newValue);
        newValue = (0, import_rich_text5.concat)(valBefore, newValAfter);
      }
      onChange(newValue);
      if (!isNewLink) {
        stopAddingLink();
      }
      if (!isValidHref(newUrl)) {
        (0, import_a11y.speak)(
          (0, import_i18n10.__)(
            "Warning: the link has been inserted but may have errors. Please test it."
          ),
          "assertive"
        );
      } else if (isActive) {
        (0, import_a11y.speak)((0, import_i18n10.__)("Link edited."), "assertive");
      } else {
        (0, import_a11y.speak)((0, import_i18n10.__)("Link inserted."), "assertive");
      }
    }
    const anchorSettings = { ...link, isActive };
    const popoverAnchor = (0, import_rich_text5.useAnchor)({
      // eslint-disable-next-line react-hooks/refs
      editableContentElement: contentRef.current,
      settings: anchorSettings
    });
    async function handleCreate(pageTitle) {
      const page = await createPageEntity({
        title: pageTitle,
        status: "draft"
      });
      return {
        id: page.id,
        type: page.type,
        title: page.title.rendered,
        url: page.link,
        kind: "post-type"
      };
    }
    function createButtonText(searchTerm) {
      return (0, import_element28.createInterpolateElement)(
        (0, import_i18n10.sprintf)(
          /* translators: %s: search term. */
          (0, import_i18n10.__)("Create page: <mark>%s</mark>"),
          searchTerm
        ),
        { mark: /* @__PURE__ */ (0, import_jsx_runtime58.jsx)("mark", {}) }
      );
    }
    return /* @__PURE__ */ (0, import_jsx_runtime58.jsx)(
      import_components3.Popover,
      {
        anchor: popoverAnchor,
        animate: false,
        onClose: stopAddingLink,
        onFocusOutside,
        placement: "bottom",
        offset: 8,
        shift: true,
        focusOnMount,
        constrainTabbing: true,
        children: /* @__PURE__ */ (0, import_jsx_runtime58.jsx)(
          import_block_editor5.LinkControl,
          {
            value: linkValue,
            onChange: onChangeLink,
            onRemove: removeLink,
            hasRichPreviews: true,
            createSuggestion: createPageEntity && handleCreate,
            withCreateSuggestion: userCanCreatePages,
            createSuggestionButtonText: createButtonText,
            hasTextControl: true,
            settings: LINK_SETTINGS,
            showInitialSuggestions: true,
            suggestionsQuery: {
              // always show Pages as initial suggestions
              initialSuggestionsSearchOptions: {
                type: "post",
                subtype: "page",
                perPage: 20
              }
            }
          }
        )
      }
    );
  }
  function getRichTextValueFromSelection(value, isActive) {
    let textStart = value.start;
    let textEnd = value.end;
    if (isActive) {
      const boundary = getFormatBoundary(value, {
        type: "core/link"
      });
      textStart = boundary.start ?? value.start;
      textEnd = boundary.end ?? value.end;
    }
    return (0, import_rich_text5.slice)(value, textStart, textEnd);
  }
  var inline_default = InlineLinkUI;

  // packages/format-library/build-module/link/index.mjs
  var import_jsx_runtime59 = __toESM(require_jsx_runtime(), 1);
  var name5 = "core/link";
  var title5 = (0, import_i18n11.__)("Link");
  function Edit2({
    isActive,
    activeAttributes,
    value,
    onChange,
    onFocus,
    contentRef,
    isVisible = true
  }) {
    const [addingLink, setAddingLink] = (0, import_element29.useState)(false);
    const [openedBy, setOpenedBy] = (0, import_element29.useState)(null);
    (0, import_element29.useEffect)(() => {
      if (!isActive) {
        setAddingLink(false);
      }
    }, [isActive]);
    (0, import_element29.useLayoutEffect)(() => {
      const editableContentElement = contentRef.current;
      if (!editableContentElement) {
        return;
      }
      function handleClick(event) {
        const link2 = event.target.closest(
          "[contenteditable] a"
        );
        if (!link2 || // other formats (e.g. bold) may be nested within the link.
        !isActive) {
          return;
        }
        setAddingLink(true);
        setOpenedBy({
          el: link2,
          action: "click"
        });
      }
      editableContentElement.addEventListener("click", handleClick);
      return () => {
        editableContentElement.removeEventListener("click", handleClick);
      };
    }, [contentRef, isActive]);
    function addLink(target) {
      const text = (0, import_rich_text6.getTextContent)((0, import_rich_text6.slice)(value));
      if (!isActive && text && (0, import_url3.isURL)(text) && isValidHref(text)) {
        onChange(
          (0, import_rich_text6.applyFormat)(value, {
            type: name5,
            attributes: { url: text }
          })
        );
      } else if (!isActive && text && (0, import_url3.isEmail)(text)) {
        onChange(
          (0, import_rich_text6.applyFormat)(value, {
            type: name5,
            attributes: { url: `mailto:${text}` }
          })
        );
      } else if (!isActive && text && (0, import_url3.isPhoneNumber)(text)) {
        onChange(
          (0, import_rich_text6.applyFormat)(value, {
            type: name5,
            attributes: { url: `tel:${text.replace(/\D/g, "")}` }
          })
        );
      } else {
        if (target) {
          setOpenedBy({
            el: target,
            action: null
            // We don't need to distinguish between click or keyboard here
          });
        }
        setAddingLink(true);
      }
    }
    function stopAddingLink() {
      setAddingLink(false);
      if (openedBy?.el?.tagName === "BUTTON") {
        openedBy.el.focus();
      } else {
        onFocus();
      }
      setOpenedBy(null);
    }
    function onFocusOutside() {
      setAddingLink(false);
      setOpenedBy(null);
    }
    function onRemoveFormat() {
      onChange((0, import_rich_text6.removeFormat)(value, name5));
      (0, import_a11y2.speak)((0, import_i18n11.__)("Link removed."), "assertive");
    }
    const shouldAutoFocus = !(openedBy?.el?.tagName === "A" && openedBy?.action === "click");
    const hasSelection = !(0, import_rich_text6.isCollapsed)(value);
    return /* @__PURE__ */ (0, import_jsx_runtime59.jsxs)(import_jsx_runtime59.Fragment, { children: [
      hasSelection && /* @__PURE__ */ (0, import_jsx_runtime59.jsx)(
        import_block_editor6.RichTextShortcut,
        {
          type: "primary",
          character: "k",
          onUse: addLink
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime59.jsx)(
        import_block_editor6.RichTextShortcut,
        {
          type: "primaryShift",
          character: "k",
          onUse: onRemoveFormat
        }
      ),
      isVisible && /* @__PURE__ */ (0, import_jsx_runtime59.jsx)(
        import_block_editor6.RichTextToolbarButton,
        {
          name: "link",
          icon: link_default,
          title: isActive ? (0, import_i18n11.__)("Link") : title5,
          onClick: (event) => {
            addLink(event.currentTarget);
          },
          isActive: isActive || addingLink,
          shortcutType: "primary",
          shortcutCharacter: "k",
          "aria-haspopup": "true",
          "aria-expanded": addingLink
        }
      ),
      isVisible && addingLink && /* @__PURE__ */ (0, import_jsx_runtime59.jsx)(
        inline_default,
        {
          stopAddingLink,
          onFocusOutside,
          isActive,
          activeAttributes,
          value,
          onChange,
          contentRef,
          focusOnMount: shouldAutoFocus ? "firstElement" : false
        }
      )
    ] });
  }
  var link = {
    name: name5,
    title: title5,
    tagName: "a",
    className: null,
    attributes: {
      url: "href",
      type: "data-type",
      id: "data-id",
      _id: "id",
      target: "target",
      rel: "rel",
      class: "class"
    },
    __unstablePasteRule(value, { html, plainText }) {
      const pastedText = (html || plainText).replace(/<[^>]+>/g, "").trim();
      if (!(0, import_url3.isURL)(pastedText) || !/^https?:/.test(pastedText)) {
        return value;
      }
      window.console.log("Created link:\n\n", pastedText);
      const format = {
        type: name5,
        attributes: {
          url: (0, import_html_entities.decodeEntities)(pastedText)
        }
      };
      if ((0, import_rich_text6.isCollapsed)(value)) {
        return (0, import_rich_text6.insert)(
          value,
          (0, import_rich_text6.applyFormat)(
            (0, import_rich_text6.create)({ text: plainText }),
            format,
            0,
            plainText.length
          )
        );
      }
      return (0, import_rich_text6.applyFormat)(value, format);
    },
    edit: Edit2
  };

  // packages/format-library/build-module/strikethrough/index.mjs
  var import_i18n12 = __toESM(require_i18n(), 1);
  var import_rich_text7 = __toESM(require_rich_text(), 1);
  var import_block_editor7 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime60 = __toESM(require_jsx_runtime(), 1);
  var name6 = "core/strikethrough";
  var title6 = (0, import_i18n12.__)("Strikethrough");
  var strikethrough = {
    name: name6,
    title: title6,
    tagName: "s",
    className: null,
    edit({ isActive, value, onChange, onFocus }) {
      function onClick() {
        onChange((0, import_rich_text7.toggleFormat)(value, { type: name6, title: title6 }));
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime60.jsxs)(import_jsx_runtime60.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(
          import_block_editor7.RichTextShortcut,
          {
            type: "access",
            character: "d",
            onUse: onClick
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(
          import_block_editor7.RichTextToolbarButton,
          {
            icon: format_strikethrough_default,
            title: title6,
            onClick,
            isActive,
            role: "menuitemcheckbox"
          }
        )
      ] });
    }
  };

  // packages/format-library/build-module/underline/index.mjs
  var import_i18n13 = __toESM(require_i18n(), 1);
  var import_rich_text8 = __toESM(require_rich_text(), 1);
  var import_block_editor8 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime61 = __toESM(require_jsx_runtime(), 1);
  var name7 = "core/underline";
  var title7 = (0, import_i18n13.__)("Underline");
  var underline = {
    name: name7,
    title: title7,
    tagName: "span",
    className: null,
    attributes: {
      style: "style"
    },
    edit({ value, onChange }) {
      const onToggle = () => {
        onChange(
          (0, import_rich_text8.toggleFormat)(value, {
            type: name7,
            attributes: {
              style: "text-decoration: underline;"
            },
            title: title7
          })
        );
      };
      return /* @__PURE__ */ (0, import_jsx_runtime61.jsxs)(import_jsx_runtime61.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(
          import_block_editor8.RichTextShortcut,
          {
            type: "primary",
            character: "u",
            onUse: onToggle
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(
          import_block_editor8.__unstableRichTextInputEvent,
          {
            inputType: "formatUnderline",
            onInput: onToggle
          }
        )
      ] });
    }
  };

  // packages/format-library/build-module/text-color/index.mjs
  var import_i18n15 = __toESM(require_i18n(), 1);
  var import_element31 = __toESM(require_element(), 1);
  var import_block_editor10 = __toESM(require_block_editor(), 1);
  var import_rich_text10 = __toESM(require_rich_text(), 1);

  // packages/format-library/build-module/text-color/inline.mjs
  var import_element30 = __toESM(require_element(), 1);
  var import_data2 = __toESM(require_data(), 1);
  var import_rich_text9 = __toESM(require_rich_text(), 1);
  var import_block_editor9 = __toESM(require_block_editor(), 1);
  var import_components4 = __toESM(require_components(), 1);
  var import_i18n14 = __toESM(require_i18n(), 1);
  var import_jsx_runtime62 = __toESM(require_jsx_runtime(), 1);
  var TABS = [
    { name: "color", title: (0, import_i18n14.__)("Text") },
    { name: "backgroundColor", title: (0, import_i18n14.__)("Background") }
  ];
  function parseCSS(css = "") {
    return css.split(";").reduce(
      (accumulator, rule) => {
        if (rule) {
          const [property, value] = rule.split(":");
          if (property === "color") {
            accumulator.color = value;
          }
          if (property === "background-color" && value !== transparentValue) {
            accumulator.backgroundColor = value;
          }
        }
        return accumulator;
      },
      {}
    );
  }
  function parseClassName(className = "", colorSettings) {
    return className.split(" ").reduce((accumulator, name16) => {
      if (name16.startsWith("has-") && name16.endsWith("-color")) {
        const colorSlug = name16.replace(/^has-/, "").replace(/-color$/, "");
        const colorObject = (0, import_block_editor9.getColorObjectByAttributeValues)(
          colorSettings,
          colorSlug
        );
        accumulator.color = colorObject.color;
      }
      return accumulator;
    }, {});
  }
  function getActiveColors(value, name16, colorSettings) {
    const activeColorFormat = (0, import_rich_text9.getActiveFormat)(value, name16);
    if (!activeColorFormat) {
      return {};
    }
    return {
      ...parseCSS(activeColorFormat.attributes?.style),
      ...parseClassName(activeColorFormat.attributes?.class, colorSettings)
    };
  }
  function setColors(value, name16, colorSettings, colors) {
    const { color, backgroundColor } = {
      ...getActiveColors(value, name16, colorSettings),
      ...colors
    };
    if (!color && !backgroundColor) {
      return (0, import_rich_text9.removeFormat)(value, name16);
    }
    const styles = [];
    const classNames = [];
    const attributes = {};
    if (backgroundColor) {
      styles.push(["background-color", backgroundColor].join(":"));
    } else {
      styles.push(["background-color", transparentValue].join(":"));
    }
    if (color) {
      const colorObject = (0, import_block_editor9.getColorObjectByColorValue)(colorSettings, color);
      if (colorObject && colorObject.slug) {
        const colorClassName = (0, import_block_editor9.getColorClassName)(
          "color",
          colorObject.slug
        );
        if (colorClassName) {
          classNames.push(colorClassName);
        }
      } else {
        styles.push(["color", color].join(":"));
      }
    }
    if (styles.length) {
      attributes.style = styles.join(";");
    }
    if (classNames.length) {
      attributes.class = classNames.join(" ");
    }
    return (0, import_rich_text9.applyFormat)(value, { type: name16, attributes });
  }
  function ColorPicker({ name: name16, property, value, onChange }) {
    const colors = (0, import_data2.useSelect)((select) => {
      const { getSettings } = select(import_block_editor9.store);
      return getSettings().colors ?? [];
    }, []);
    const activeColors = (0, import_element30.useMemo)(
      () => getActiveColors(value, name16, colors),
      [name16, value, colors]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(
      import_block_editor9.ColorPalette,
      {
        value: activeColors[property],
        onChange: (color) => {
          onChange(
            setColors(value, name16, colors, { [property]: color })
          );
        },
        enableAlpha: true,
        __experimentalIsRenderedInSidebar: true
      }
    );
  }
  function InlineColorUI({
    name: name16,
    value,
    onChange,
    onClose,
    contentRef,
    isActive
  }) {
    const anchorSettings = { ...textColor, isActive };
    const popoverAnchor = (0, import_rich_text9.useAnchor)({
      // eslint-disable-next-line react-hooks/refs
      editableContentElement: contentRef.current,
      settings: anchorSettings
    });
    return /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(
      import_components4.Popover,
      {
        onClose,
        className: "format-library__inline-color-popover",
        anchor: popoverAnchor,
        children: /* @__PURE__ */ (0, import_jsx_runtime62.jsxs)(tabs_exports.Root, { defaultValue: TABS[0].name, children: [
          /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(tabs_exports.List, { children: TABS.map((tab) => /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(tabs_exports.Tab, { value: tab.name, children: tab.title }, tab.name)) }),
          TABS.map((tab) => /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(
            tabs_exports.Panel,
            {
              value: tab.name,
              tabIndex: -1,
              children: /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(
                ColorPicker,
                {
                  name: name16,
                  property: tab.name,
                  value,
                  onChange
                }
              )
            },
            tab.name
          ))
        ] })
      }
    );
  }

  // packages/format-library/build-module/text-color/index.mjs
  var import_jsx_runtime63 = __toESM(require_jsx_runtime(), 1);
  var transparentValue = "rgba(0, 0, 0, 0)";
  var name8 = "core/text-color";
  var title8 = (0, import_i18n15.__)("Highlight");
  var EMPTY_ARRAY2 = [];
  function getComputedStyleProperty(element, property) {
    const { ownerDocument: ownerDocument2 } = element;
    const { defaultView } = ownerDocument2;
    const style = defaultView?.getComputedStyle(element);
    const value = style?.getPropertyValue(property);
    if (property === "background-color" && value === transparentValue && element.parentElement) {
      return getComputedStyleProperty(element.parentElement, property);
    }
    return value;
  }
  function fillComputedColors(element, { color, backgroundColor }) {
    if (!element || !color && !backgroundColor) {
      return;
    }
    return {
      color: color || getComputedStyleProperty(element, "color"),
      backgroundColor: backgroundColor === transparentValue ? getComputedStyleProperty(element, "background-color") : backgroundColor
    };
  }
  function TextColorEdit({
    value,
    onChange,
    isActive,
    activeAttributes,
    contentRef
  }) {
    const [allowCustomControl, colors = EMPTY_ARRAY2] = (0, import_block_editor10.useSettings)(
      "color.custom",
      "color.palette"
    );
    const [isAddingColor, setIsAddingColor] = (0, import_element31.useState)(false);
    const colorIndicatorStyle = (0, import_element31.useMemo)(
      () => fillComputedColors(
        contentRef.current,
        getActiveColors(value, name8, colors)
      ),
      [contentRef, value, colors]
    );
    const hasColorsToChoose = !!colors.length || allowCustomControl;
    if (!hasColorsToChoose && !isActive) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime63.jsxs)(import_jsx_runtime63.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(
        import_block_editor10.RichTextToolbarButton,
        {
          className: "format-library-text-color-button",
          isActive,
          icon: /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(
            icon_default,
            {
              icon: Object.keys(activeAttributes).length ? text_color_default : color_default,
              style: colorIndicatorStyle
            }
          ),
          title: title8,
          onClick: hasColorsToChoose ? () => setIsAddingColor(true) : () => onChange((0, import_rich_text10.removeFormat)(value, name8)),
          role: "menuitemcheckbox"
        }
      ),
      isAddingColor && /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(
        InlineColorUI,
        {
          name: name8,
          onClose: () => setIsAddingColor(false),
          value,
          onChange,
          contentRef,
          isActive
        }
      )
    ] });
  }
  var textColor = {
    name: name8,
    title: title8,
    tagName: "mark",
    className: "has-inline-color",
    attributes: {
      style: "style",
      class: "class"
    },
    edit: TextColorEdit
  };

  // packages/format-library/build-module/subscript/index.mjs
  var import_i18n16 = __toESM(require_i18n(), 1);
  var import_rich_text11 = __toESM(require_rich_text(), 1);
  var import_block_editor11 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime64 = __toESM(require_jsx_runtime(), 1);
  var name9 = "core/subscript";
  var title9 = (0, import_i18n16.__)("Subscript");
  var subscript = {
    name: name9,
    title: title9,
    tagName: "sub",
    className: null,
    edit({ isActive, value, onChange, onFocus }) {
      function onToggle() {
        onChange((0, import_rich_text11.toggleFormat)(value, { type: name9, title: title9 }));
      }
      function onClick() {
        onToggle();
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime64.jsx)(
        import_block_editor11.RichTextToolbarButton,
        {
          icon: subscript_default,
          title: title9,
          onClick,
          isActive,
          role: "menuitemcheckbox"
        }
      );
    }
  };

  // packages/format-library/build-module/superscript/index.mjs
  var import_i18n17 = __toESM(require_i18n(), 1);
  var import_rich_text12 = __toESM(require_rich_text(), 1);
  var import_block_editor12 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime65 = __toESM(require_jsx_runtime(), 1);
  var name10 = "core/superscript";
  var title10 = (0, import_i18n17.__)("Superscript");
  var superscript = {
    name: name10,
    title: title10,
    tagName: "sup",
    className: null,
    edit({ isActive, value, onChange, onFocus }) {
      function onToggle() {
        onChange((0, import_rich_text12.toggleFormat)(value, { type: name10, title: title10 }));
      }
      function onClick() {
        onToggle();
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime65.jsx)(
        import_block_editor12.RichTextToolbarButton,
        {
          icon: superscript_default,
          title: title10,
          onClick,
          isActive,
          role: "menuitemcheckbox"
        }
      );
    }
  };

  // packages/format-library/build-module/keyboard/index.mjs
  var import_i18n18 = __toESM(require_i18n(), 1);
  var import_rich_text13 = __toESM(require_rich_text(), 1);
  var import_block_editor13 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime66 = __toESM(require_jsx_runtime(), 1);
  var name11 = "core/keyboard";
  var title11 = (0, import_i18n18.__)("Keyboard input");
  var keyboard2 = {
    name: name11,
    title: title11,
    tagName: "kbd",
    className: null,
    edit({ isActive, value, onChange, onFocus }) {
      function onToggle() {
        onChange((0, import_rich_text13.toggleFormat)(value, { type: name11, title: title11 }));
      }
      function onClick() {
        onToggle();
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime66.jsx)(
        import_block_editor13.RichTextToolbarButton,
        {
          icon: button_default,
          title: title11,
          onClick,
          isActive,
          role: "menuitemcheckbox"
        }
      );
    }
  };

  // packages/format-library/build-module/unknown/index.mjs
  var import_i18n19 = __toESM(require_i18n(), 1);
  var import_rich_text14 = __toESM(require_rich_text(), 1);
  var import_block_editor14 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime67 = __toESM(require_jsx_runtime(), 1);
  var name12 = "core/unknown";
  var title12 = (0, import_i18n19.__)("Clear Unknown Formatting");
  function selectionContainsUnknownFormats(value) {
    if ((0, import_rich_text14.isCollapsed)(value)) {
      return false;
    }
    const selectedValue = (0, import_rich_text14.slice)(value);
    return selectedValue.formats.some((formats) => {
      return formats.some((format) => format.type === name12);
    });
  }
  var unknown = {
    name: name12,
    title: title12,
    tagName: "*",
    className: null,
    edit({ isActive, value, onChange, onFocus }) {
      if (!isActive && !selectionContainsUnknownFormats(value)) {
        return null;
      }
      function onClick() {
        onChange((0, import_rich_text14.removeFormat)(value, name12));
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime67.jsx)(
        import_block_editor14.RichTextToolbarButton,
        {
          name: "unknown",
          icon: help_default,
          title: title12,
          onClick,
          isActive: true
        }
      );
    }
  };

  // packages/format-library/build-module/language/index.mjs
  var import_i18n20 = __toESM(require_i18n(), 1);
  var import_block_editor15 = __toESM(require_block_editor(), 1);
  var import_components5 = __toESM(require_components(), 1);
  var import_element32 = __toESM(require_element(), 1);
  var import_rich_text15 = __toESM(require_rich_text(), 1);
  var import_jsx_runtime68 = __toESM(require_jsx_runtime(), 1);
  var name13 = "core/language";
  var title13 = (0, import_i18n20.__)("Language");
  var language = {
    name: name13,
    title: title13,
    tagName: "bdo",
    className: null,
    attributes: {
      lang: "lang",
      dir: "dir"
    },
    edit: Edit3
  };
  function Edit3({ isActive, value, onChange, contentRef }) {
    const [isPopoverVisible, setIsPopoverVisible] = (0, import_element32.useState)(false);
    const togglePopover = () => {
      setIsPopoverVisible((state) => !state);
    };
    return /* @__PURE__ */ (0, import_jsx_runtime68.jsxs)(import_jsx_runtime68.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime68.jsx)(
        import_block_editor15.RichTextToolbarButton,
        {
          icon: language_default,
          label: title13,
          title: title13,
          onClick: () => {
            if (isActive) {
              onChange((0, import_rich_text15.removeFormat)(value, name13));
            } else {
              togglePopover();
            }
          },
          isActive,
          role: "menuitemcheckbox"
        }
      ),
      isPopoverVisible && /* @__PURE__ */ (0, import_jsx_runtime68.jsx)(
        InlineLanguageUI,
        {
          value,
          onChange,
          onClose: togglePopover,
          contentRef
        }
      )
    ] });
  }
  function InlineLanguageUI({
    value,
    contentRef,
    onChange,
    onClose
  }) {
    const popoverAnchor = (0, import_rich_text15.useAnchor)({
      // eslint-disable-next-line react-hooks/refs
      editableContentElement: contentRef.current,
      settings: language
    });
    const [lang, setLang] = (0, import_element32.useState)("");
    const [dir, setDir] = (0, import_element32.useState)("ltr");
    return /* @__PURE__ */ (0, import_jsx_runtime68.jsx)(
      import_components5.Popover,
      {
        className: "block-editor-format-toolbar__language-popover",
        anchor: popoverAnchor,
        onClose,
        children: /* @__PURE__ */ (0, import_jsx_runtime68.jsxs)(
          Stack,
          {
            render: /* @__PURE__ */ (0, import_jsx_runtime68.jsx)("form", {}),
            direction: "column",
            gap: "lg",
            className: "block-editor-format-toolbar__language-container-content",
            onSubmit: (event) => {
              event.preventDefault();
              onChange(
                (0, import_rich_text15.applyFormat)(value, {
                  type: name13,
                  attributes: {
                    lang,
                    dir
                  }
                })
              );
              onClose();
            },
            children: [
              /* @__PURE__ */ (0, import_jsx_runtime68.jsx)(
                import_components5.TextControl,
                {
                  label: title13,
                  value: lang,
                  onChange: (val) => setLang(val),
                  help: (0, import_i18n20.__)(
                    'A valid language attribute, like "en" or "fr".'
                  )
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime68.jsx)(
                import_components5.SelectControl,
                {
                  label: (0, import_i18n20.__)("Text direction"),
                  value: dir,
                  options: [
                    {
                      label: (0, import_i18n20.__)("Left to right"),
                      value: "ltr"
                    },
                    {
                      label: (0, import_i18n20.__)("Right to left"),
                      value: "rtl"
                    }
                  ],
                  onChange: (val) => setDir(val)
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime68.jsx)(Stack, { justify: "right", children: /* @__PURE__ */ (0, import_jsx_runtime68.jsx)(
                import_components5.Button,
                {
                  __next40pxDefaultSize: true,
                  variant: "primary",
                  type: "submit",
                  text: (0, import_i18n20.__)("Apply")
                }
              ) })
            ]
          }
        )
      }
    );
  }

  // packages/format-library/build-module/math/index.mjs
  var import_i18n21 = __toESM(require_i18n(), 1);
  var import_element33 = __toESM(require_element(), 1);
  var import_rich_text16 = __toESM(require_rich_text(), 1);
  var import_block_editor16 = __toESM(require_block_editor(), 1);
  var import_components6 = __toESM(require_components(), 1);
  var import_jsx_runtime69 = __toESM(require_jsx_runtime(), 1);
  var name14 = "core/math";
  var title14 = (0, import_i18n21.__)("Math");
  function InlineUI2({
    value,
    onChange,
    activeAttributes,
    contentRef,
    latexToMathML
  }) {
    const [latex, setLatex] = (0, import_element33.useState)(
      activeAttributes?.["data-latex"] || ""
    );
    const [error2, setError] = (0, import_element33.useState)(null);
    const formRef = (0, import_element33.useRef)(null);
    const popoverAnchor = (0, import_rich_text16.useAnchor)({
      // eslint-disable-next-line react-hooks/refs
      editableContentElement: contentRef.current,
      settings: math
    });
    const handleLatexChange = (newLatex) => {
      setLatex(newLatex);
      let mathML = "";
      if (newLatex && latexToMathML) {
        try {
          mathML = latexToMathML(newLatex, { displayMode: false });
          setError(null);
        } catch (err) {
          setError(
            err instanceof Error ? err.message : (0, import_i18n21.__)("Could not parse the LaTeX math syntax.")
          );
        }
      } else {
        setError(null);
      }
      const newReplacements = value.replacements.slice();
      newReplacements[value.start] = {
        type: name14,
        attributes: {
          "data-latex": newLatex
        },
        innerHTML: mathML
      };
      onChange({
        ...value,
        replacements: newReplacements
      });
    };
    return /* @__PURE__ */ (0, import_jsx_runtime69.jsx)(
      import_components6.Popover,
      {
        placement: "bottom-start",
        offset: 8,
        focusOnMount: false,
        anchor: popoverAnchor,
        onFocusOutside: () => formRef.current?.reportValidity(),
        className: "block-editor-format-toolbar__math-popover",
        children: /* @__PURE__ */ (0, import_jsx_runtime69.jsxs)(
          "form",
          {
            ref: formRef,
            style: { minWidth: "300px", padding: "16px" },
            onSubmit: (event) => event.preventDefault(),
            children: [
              /* @__PURE__ */ (0, import_jsx_runtime69.jsx)(
                ValidatedInputControl,
                {
                  label: (0, import_i18n21.__)("LaTeX math syntax"),
                  value: latex,
                  customValidity: error2 ? { type: "invalid", message: error2 } : void 0,
                  onValueChange: handleLatexChange,
                  placeholder: (0, import_i18n21.__)("e.g., x^2, \\frac{a}{b}, \\sqrt{x}"),
                  autoComplete: "off",
                  className: "block-editor-format-toolbar__math-input"
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime69.jsx)(
                Link,
                {
                  openInNewTab: true,
                  className: "block-editor-format-toolbar__math-learn-more",
                  href: (0, import_i18n21.__)(
                    "https://wordpress.org/documentation/article/math-block/"
                  ),
                  children: (0, import_i18n21.__)("Learn more about LaTeX syntax")
                }
              )
            ]
          }
        )
      }
    );
  }
  function Edit4({
    value,
    onChange,
    onFocus,
    isObjectActive,
    activeObjectAttributes,
    contentRef
  }) {
    const [latexToMathML, setLatexToMathML] = (0, import_element33.useState)();
    (0, import_element33.useEffect)(() => {
      import("@wordpress/latex-to-mathml").then((module) => {
        setLatexToMathML(() => module.default);
      });
    }, []);
    function onClick() {
      let newValue;
      if (isObjectActive) {
        const latex = activeObjectAttributes?.["data-latex"] || "";
        newValue = (0, import_rich_text16.insert)(value, latex);
        newValue.start = newValue.end - latex.length;
      } else {
        const selectedText = (0, import_rich_text16.getTextContent)((0, import_rich_text16.slice)(value));
        let innerHTML = "";
        if (selectedText && latexToMathML) {
          try {
            innerHTML = latexToMathML(selectedText, {
              displayMode: false
            });
          } catch {
          }
        }
        newValue = (0, import_rich_text16.insertObject)(value, {
          type: name14,
          attributes: { "data-latex": selectedText },
          innerHTML
        });
        newValue.start = newValue.end - 1;
      }
      onChange(newValue);
      onFocus();
    }
    return /* @__PURE__ */ (0, import_jsx_runtime69.jsxs)(import_jsx_runtime69.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime69.jsx)(
        import_block_editor16.RichTextToolbarButton,
        {
          icon: math_default,
          title: title14,
          onClick,
          isActive: isObjectActive
        }
      ),
      isObjectActive && /* @__PURE__ */ (0, import_jsx_runtime69.jsx)(
        InlineUI2,
        {
          value,
          onChange,
          activeAttributes: activeObjectAttributes,
          contentRef,
          latexToMathML
        }
      )
    ] });
  }
  var math = {
    name: name14,
    title: title14,
    tagName: "math",
    className: null,
    attributes: {
      "data-latex": "data-latex"
    },
    contentEditable: false,
    edit: Edit4
  };

  // packages/format-library/build-module/non-breaking-space/index.mjs
  var import_i18n22 = __toESM(require_i18n(), 1);
  var import_rich_text17 = __toESM(require_rich_text(), 1);
  var import_block_editor17 = __toESM(require_block_editor(), 1);
  var import_components7 = __toESM(require_components(), 1);
  var import_jsx_runtime70 = __toESM(require_jsx_runtime(), 1);
  var name15 = "core/non-breaking-space";
  var title15 = (0, import_i18n22.__)("Non breaking space");
  function PopoverAnchor({ contentRef }) {
    const popoverAnchor = (0, import_rich_text17.useAnchor)({
      // eslint-disable-next-line react-hooks/refs
      editableContentElement: contentRef.current,
      settings: nonBreakingSpace
    });
    return /* @__PURE__ */ (0, import_jsx_runtime70.jsx)(import_components7.Popover, { anchor: popoverAnchor, children: /* @__PURE__ */ (0, import_jsx_runtime70.jsx)("div", { style: { whiteSpace: "nowrap", padding: "4px" }, children: (0, import_i18n22.__)("Non-breaking space") }) });
  }
  var nonBreakingSpace = {
    name: name15,
    title: title15,
    tagName: "span",
    className: "non-breaking-space",
    edit({ value, onChange, contentRef }) {
      function addNonBreakingSpace() {
        onChange((0, import_rich_text17.insert)(value, "\xA0"));
      }
      const selectedValue = value.start && value.end ? value.text.slice(value.start, value.end) : null;
      return /* @__PURE__ */ (0, import_jsx_runtime70.jsxs)(import_jsx_runtime70.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime70.jsx)(
          import_block_editor17.RichTextShortcut,
          {
            type: "primaryShift",
            character: " ",
            onUse: addNonBreakingSpace
          }
        ),
        selectedValue === "\xA0" && /* @__PURE__ */ (0, import_jsx_runtime70.jsx)(PopoverAnchor, { contentRef })
      ] });
    },
    __experimentalCreatePrepareEditableTree() {
      return (formats, text) => {
        const NBSP = "\xA0";
        let record = { formats, text };
        let index2 = -1;
        do {
          index2 = text.indexOf(NBSP, index2 + 1);
          if (index2 !== -1) {
            record = (0, import_rich_text17.applyFormat)(
              record,
              { type: name15 },
              index2,
              index2 + 1
            );
          }
        } while (index2 !== -1);
        return record.formats;
      };
    }
  };

  // packages/format-library/build-module/default-formats.mjs
  var default_formats_default = [
    bold,
    code,
    image,
    italic,
    link,
    strikethrough,
    underline,
    textColor,
    subscript,
    superscript,
    keyboard2,
    unknown,
    language,
    math,
    nonBreakingSpace
  ];

  // packages/format-library/build-module/index.mjs
  default_formats_default.forEach(
    ({ name: name16, ...settings }) => (0, import_rich_text18.registerFormatType)(name16, settings)
  );
})();
/*! Bundled license information:

use-sync-external-store/cjs/use-sync-external-store-shim.development.js:
  (**
   * @license React
   * use-sync-external-store-shim.development.js
   *
   * Copyright (c) Meta Platforms, Inc. and affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   *)
*/
(window.wp ||= {}).formatLibrary = wp.formatLibrary;
})();
