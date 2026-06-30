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
          didWarnOld18Alpha || void 0 === React31.startTransition || (didWarnOld18Alpha = true, console.error(
            "You are using an outdated, pre-release alpha of React 18 that does not support useSyncExternalStore. The use-sync-external-store shim will not work correctly. Upgrade to a newer pre-release."
          ));
          var value = getSnapshot2();
          if (!didWarnUncachedGetSnapshot) {
            var cachedValue = getSnapshot2();
            objectIs(value, cachedValue) || (console.error(
              "The result of getSnapshot should be cached to avoid an infinite loop"
            ), didWarnUncachedGetSnapshot = true);
          }
          cachedValue = useState17({
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
        var React31 = require_react(), objectIs = "function" === typeof Object.is ? Object.is : is, useState17 = React31.useState, useEffect14 = React31.useEffect, useLayoutEffect3 = React31.useLayoutEffect, useDebugValue = React31.useDebugValue, didWarnOld18Alpha = false, didWarnUncachedGetSnapshot = false, shim = "undefined" === typeof window || "undefined" === typeof window.document || "undefined" === typeof window.document.createElement ? useSyncExternalStore$1 : useSyncExternalStore$2;
        exports.useSyncExternalStore = void 0 !== React31.useSyncExternalStore ? React31.useSyncExternalStore : shim;
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

  // package-external:@wordpress/a11y
  var require_a11y = __commonJS({
    "package-external:@wordpress/a11y"(exports, module) {
      module.exports = window.wp.a11y;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
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
    ({ icon, size = 24, ...props }, ref) => {
      return (0, import_element.cloneElement)(icon, {
        width: size,
        height: size,
        ...props,
        ref
      });
    }
  );

  // packages/icons/build-module/library/button.mjs
  var import_primitives = __toESM(require_primitives(), 1);
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
  var button_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M8 12.5h8V11H8v1.5Z M19 6.5H5a2 2 0 0 0-2 2V15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.5a2 2 0 0 0-2-2ZM5 8h14a.5.5 0 0 1 .5.5V15a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V8.5A.5.5 0 0 1 5 8Z" }) });

  // packages/icons/build-module/library/chevron-right.mjs
  var import_primitives2 = __toESM(require_primitives(), 1);
  var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
  var chevron_right_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z" }) });

  // packages/icons/build-module/library/code.mjs
  var import_primitives3 = __toESM(require_primitives(), 1);
  var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
  var code_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M20.8 10.7l-4.3-4.3-1.1 1.1 4.3 4.3c.1.1.1.3 0 .4l-4.3 4.3 1.1 1.1 4.3-4.3c.7-.8.7-1.9 0-2.6zM4.2 11.8l4.3-4.3-1-1-4.3 4.3c-.7.7-.7 1.8 0 2.5l4.3 4.3 1.1-1.1-4.3-4.3c-.2-.1-.2-.3-.1-.4z" }) });

  // packages/icons/build-module/library/color.mjs
  var import_primitives4 = __toESM(require_primitives(), 1);
  var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
  var color_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.Path, { d: "M17.2 10.9c-.5-1-1.2-2.1-2.1-3.2-.6-.9-1.3-1.7-2.1-2.6L12 4l-1 1.1c-.6.9-1.3 1.7-2 2.6-.8 1.2-1.5 2.3-2 3.2-.6 1.2-1 2.2-1 3 0 3.4 2.7 6.1 6.1 6.1s6.1-2.7 6.1-6.1c0-.8-.3-1.8-1-3zm-5.1 7.6c-2.5 0-4.6-2.1-4.6-4.6 0-.3.1-1 .8-2.3.5-.9 1.1-1.9 2-3.1.7-.9 1.3-1.7 1.8-2.3.7.8 1.3 1.6 1.8 2.3.8 1.1 1.5 2.2 2 3.1.7 1.3.8 2 .8 2.3 0 2.5-2.1 4.6-4.6 4.6z" }) });

  // packages/icons/build-module/library/format-bold.mjs
  var import_primitives5 = __toESM(require_primitives(), 1);
  var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
  var format_bold_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.Path, { d: "M14.7 11.3c1-.6 1.5-1.6 1.5-3 0-2.3-1.3-3.4-4-3.4H7v14h5.8c1.4 0 2.5-.3 3.3-1 .8-.7 1.2-1.7 1.2-2.9.1-1.9-.8-3.1-2.6-3.7zm-5.1-4h2.3c.6 0 1.1.1 1.4.4.3.3.5.7.5 1.2s-.2 1-.5 1.2c-.3.3-.8.4-1.4.4H9.6V7.3zm4.6 9c-.4.3-1 .4-1.7.4H9.6v-3.9h2.9c.7 0 1.3.2 1.7.5.4.3.6.8.6 1.5s-.2 1.2-.6 1.5z" }) });

  // packages/icons/build-module/library/format-italic.mjs
  var import_primitives6 = __toESM(require_primitives(), 1);
  var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
  var format_italic_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.Path, { d: "M12.5 5L10 19h1.9l2.5-14z" }) });

  // packages/icons/build-module/library/format-strikethrough.mjs
  var import_primitives7 = __toESM(require_primitives(), 1);
  var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
  var format_strikethrough_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.Path, { d: "M9.1 9v-.5c0-.6.2-1.1.7-1.4.5-.3 1.2-.5 2-.5.7 0 1.4.1 2.1.3.7.2 1.4.5 2.1.9l.2-1.9c-.6-.3-1.2-.5-1.9-.7-.8-.1-1.6-.2-2.4-.2-1.5 0-2.7.3-3.6 1-.8.7-1.2 1.5-1.2 2.6V9h2zM20 12H4v1h8.3c.3.1.6.2.8.3.5.2.9.5 1.1.8.3.3.4.7.4 1.2 0 .7-.2 1.1-.8 1.5-.5.3-1.2.5-2.1.5-.8 0-1.6-.1-2.4-.3-.8-.2-1.5-.5-2.2-.8L7 18.1c.5.2 1.2.4 2 .6.8.2 1.6.3 2.4.3 1.7 0 3-.3 3.9-1 .9-.7 1.3-1.6 1.3-2.8 0-.9-.2-1.7-.7-2.2H20v-1z" }) });

  // packages/icons/build-module/library/help.mjs
  var import_primitives8 = __toESM(require_primitives(), 1);
  var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
  var help_default = /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.Path, { d: "M12 4a8 8 0 1 1 .001 16.001A8 8 0 0 1 12 4Zm0 1.5a6.5 6.5 0 1 0-.001 13.001A6.5 6.5 0 0 0 12 5.5Zm.75 11h-1.5V15h1.5v1.5Zm-.445-9.234a3 3 0 0 1 .445 5.89V14h-1.5v-1.25c0-.57.452-.958.917-1.01A1.5 1.5 0 0 0 12 8.75a1.5 1.5 0 0 0-1.5 1.5H9a3 3 0 0 1 3.305-2.984Z" }) });

  // packages/icons/build-module/library/language.mjs
  var import_primitives9 = __toESM(require_primitives(), 1);
  var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
  var language_default = /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives9.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives9.Path, { d: "M17.5 10h-1.7l-3.7 10.5h1.7l.9-2.6h3.9l.9 2.6h1.7L17.5 10zm-2.2 6.3 1.4-4 1.4 4h-2.8zm-4.8-3.8c1.6-1.8 2.9-3.6 3.7-5.7H16V5.2h-5.8V3H8.8v2.2H3v1.5h9.6c-.7 1.6-1.8 3.1-3.1 4.6C8.6 10.2 7.8 9 7.2 8H5.6c.6 1.4 1.7 2.9 2.9 4.4l-2.4 2.4c-.3.4-.7.8-1.1 1.2l1 1 1.2-1.2c.8-.8 1.6-1.5 2.3-2.3.8.9 1.7 1.7 2.5 2.5l.6-1.5c-.7-.6-1.4-1.3-2.1-2z" }) });

  // packages/icons/build-module/library/link.mjs
  var import_primitives10 = __toESM(require_primitives(), 1);
  var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
  var link_default = /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives10.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives10.Path, { d: "M10 17.389H8.444A5.194 5.194 0 1 1 8.444 7H10v1.5H8.444a3.694 3.694 0 0 0 0 7.389H10v1.5ZM14 7h1.556a5.194 5.194 0 0 1 0 10.39H14v-1.5h1.556a3.694 3.694 0 0 0 0-7.39H14V7Zm-4.5 6h5v-1.5h-5V13Z" }) });

  // packages/icons/build-module/library/math.mjs
  var import_primitives11 = __toESM(require_primitives(), 1);
  var import_jsx_runtime11 = __toESM(require_jsx_runtime(), 1);
  var math_default = /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives11.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives11.Path, { d: "M11.2 6.8c-.7 0-1.4.5-1.6 1.1l-2.8 7.5-1.2-1.8c-.1-.2-.4-.3-.6-.3H3v1.5h1.6l1.2 1.8c.6.9 1.9.7 2.2-.3l2.9-7.9s.1-.2.2-.2h7.8V6.7h-7.8Zm5.3 3.4-1.9 1.9-1.9-1.9-1.1 1.1 1.9 1.9-1.9 1.9 1.1 1.1 1.9-1.9 1.9 1.9 1.1-1.1-1.9-1.9 1.9-1.9-1.1-1.1Z" }) });

  // packages/icons/build-module/library/subscript.mjs
  var import_primitives12 = __toESM(require_primitives(), 1);
  var import_jsx_runtime12 = __toESM(require_jsx_runtime(), 1);
  var subscript_default = /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives12.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives12.Path, { d: "M16.9 18.3l.8-1.2c.4-.6.7-1.2.9-1.6.2-.4.3-.8.3-1.2 0-.3-.1-.7-.2-1-.1-.3-.4-.5-.6-.7-.3-.2-.6-.3-1-.3s-.8.1-1.1.2c-.3.1-.7.3-1 .6l.2 1.3c.3-.3.5-.5.8-.6s.6-.2.9-.2c.3 0 .5.1.7.2.2.2.2.4.2.7 0 .3-.1.5-.2.8-.1.3-.4.7-.8 1.3L15 19.4h4.3v-1.2h-2.4zM14.1 7.2h-2L9.5 11 6.9 7.2h-2l3.6 5.3L4.7 18h2l2.7-4 2.7 4h2l-3.8-5.5 3.8-5.3z" }) });

  // packages/icons/build-module/library/superscript.mjs
  var import_primitives13 = __toESM(require_primitives(), 1);
  var import_jsx_runtime13 = __toESM(require_jsx_runtime(), 1);
  var superscript_default = /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives13.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives13.Path, { d: "M16.9 10.3l.8-1.3c.4-.6.7-1.2.9-1.6.2-.4.3-.8.3-1.2 0-.3-.1-.7-.2-1-.2-.2-.4-.4-.7-.6-.3-.2-.6-.3-1-.3s-.8.1-1.1.2c-.3.1-.7.3-1 .6l.1 1.3c.3-.3.5-.5.8-.6s.6-.2.9-.2c.3 0 .5.1.7.2.2.2.2.4.2.7 0 .3-.1.5-.2.8-.1.3-.4.7-.8 1.3l-1.8 2.8h4.3v-1.2h-2.2zm-2.8-3.1h-2L9.5 11 6.9 7.2h-2l3.6 5.3L4.7 18h2l2.7-4 2.7 4h2l-3.8-5.5 3.8-5.3z" }) });

  // packages/icons/build-module/library/text-color.mjs
  var import_primitives14 = __toESM(require_primitives(), 1);
  var import_jsx_runtime14 = __toESM(require_jsx_runtime(), 1);
  var text_color_default = /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(import_primitives14.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(import_primitives14.Path, { d: "M12.9 6h-2l-4 11h1.9l1.1-3h4.2l1.1 3h1.9L12.9 6zm-2.5 6.5l1.5-4.9 1.7 4.9h-3.2z" }) });

  // packages/format-library/build-module/bold/index.mjs
  var import_jsx_runtime15 = __toESM(require_jsx_runtime(), 1);
  var name = "core/bold";
  var title = (0, import_i18n.__)("Bold");
  var bold = {
    name,
    title,
    tagName: "strong",
    className: null,
    edit({ isActive, value, onChange, onFocus, isVisible = true }) {
      function onToggle() {
        onChange((0, import_rich_text.toggleFormat)(value, { type: name, title }));
      }
      function onClick() {
        onChange((0, import_rich_text.toggleFormat)(value, { type: name }));
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime15.jsxs)(import_jsx_runtime15.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(
          import_block_editor.RichTextShortcut,
          {
            type: "primary",
            character: "b",
            onUse: onToggle
          }
        ),
        isVisible && /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(
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
        /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(
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
  var import_jsx_runtime16 = __toESM(require_jsx_runtime(), 1);
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
    edit({ value, onChange, onFocus, isActive }) {
      function onClick() {
        onChange((0, import_rich_text2.toggleFormat)(value, { type: name2, title: title2 }));
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime16.jsxs)(import_jsx_runtime16.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
          import_block_editor2.RichTextShortcut,
          {
            type: "access",
            character: "x",
            onUse: onClick
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
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

  // node_modules/@base-ui/utils/esm/useControlled.js
  var React = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/esm/error.js
  var set;
  if (true) {
    set = /* @__PURE__ */ new Set();
  }
  function error(...messages) {
    if (true) {
      const messageKey = messages.join(" ");
      if (!set.has(messageKey)) {
        set.add(messageKey);
        console.error(`Base UI: ${messageKey}`);
      }
    }
  }

  // node_modules/@base-ui/utils/esm/useControlled.js
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
    const value = isControlled ? controlled : valueState;
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

  // node_modules/@base-ui/utils/esm/safeReact.js
  var React2 = __toESM(require_react(), 1);
  var SafeReact = {
    ...React2
  };

  // node_modules/@base-ui/utils/esm/useRefWithInit.js
  var React3 = __toESM(require_react(), 1);
  var UNINITIALIZED = {};
  function useRefWithInit(init, initArg) {
    const ref = React3.useRef(UNINITIALIZED);
    if (ref.current === UNINITIALIZED) {
      ref.current = init(initArg);
    }
    return ref;
  }

  // node_modules/@base-ui/utils/esm/useStableCallback.js
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

  // node_modules/@base-ui/utils/esm/useIsoLayoutEffect.js
  var React4 = __toESM(require_react(), 1);
  var noop = () => {
  };
  var useIsoLayoutEffect = typeof document !== "undefined" ? React4.useLayoutEffect : noop;

  // node_modules/@base-ui/utils/esm/warn.js
  var set2;
  if (true) {
    set2 = /* @__PURE__ */ new Set();
  }
  function warn(...messages) {
    if (true) {
      const messageKey = messages.join(" ");
      if (!set2.has(messageKey)) {
        set2.add(messageKey);
        console.warn(`Base UI: ${messageKey}`);
      }
    }
  }

  // node_modules/@base-ui/react/esm/internals/composite/list/CompositeList.js
  var React6 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/esm/internals/composite/list/CompositeListContext.js
  var React5 = __toESM(require_react(), 1);
  var CompositeListContext = /* @__PURE__ */ React5.createContext({
    register: () => {
    },
    unregister: () => {
    },
    subscribeMapChange: () => {
      return () => {
      };
    },
    elementsRef: {
      current: []
    },
    nextIndexRef: {
      current: 0
    }
  });
  if (true) CompositeListContext.displayName = "CompositeListContext";
  function useCompositeListContext() {
    return React5.useContext(CompositeListContext);
  }

  // node_modules/@base-ui/react/esm/internals/composite/list/CompositeList.js
  var import_jsx_runtime17 = __toESM(require_jsx_runtime(), 1);
  function CompositeList(props) {
    const {
      children,
      elementsRef,
      labelsRef,
      onMapChange: onMapChangeProp
    } = props;
    const onMapChange = useStableCallback(onMapChangeProp);
    const nextIndexRef = React6.useRef(0);
    const listeners = useRefWithInit(createListeners).current;
    const map = useRefWithInit(createMap).current;
    const [mapTick, setMapTick] = React6.useState(0);
    const lastTickRef = React6.useRef(mapTick);
    const register = useStableCallback((node, metadata) => {
      map.set(node, metadata ?? null);
      lastTickRef.current += 1;
      setMapTick(lastTickRef.current);
    });
    const unregister = useStableCallback((node) => {
      map.delete(node);
      lastTickRef.current += 1;
      setMapTick(lastTickRef.current);
    });
    const sortedMap = React6.useMemo(() => {
      disableEslintWarning(mapTick);
      const newMap = /* @__PURE__ */ new Map();
      const sortedNodes = Array.from(map.keys()).filter((node) => node.isConnected).sort(sortByDocumentPosition);
      sortedNodes.forEach((node, index) => {
        const metadata = map.get(node) ?? {};
        newMap.set(node, {
          ...metadata,
          index
        });
      });
      return newMap;
    }, [map, mapTick]);
    useIsoLayoutEffect(() => {
      if (typeof MutationObserver !== "function" || sortedMap.size === 0) {
        return void 0;
      }
      const mutationObserver = new MutationObserver((entries) => {
        const diff = /* @__PURE__ */ new Set();
        const updateDiff = (node) => diff.has(node) ? diff.delete(node) : diff.add(node);
        entries.forEach((entry) => {
          entry.removedNodes.forEach(updateDiff);
          entry.addedNodes.forEach(updateDiff);
        });
        if (diff.size === 0) {
          lastTickRef.current += 1;
          setMapTick(lastTickRef.current);
        }
      });
      sortedMap.forEach((_, node) => {
        if (node.parentElement) {
          mutationObserver.observe(node.parentElement, {
            childList: true
          });
        }
      });
      return () => {
        mutationObserver.disconnect();
      };
    }, [sortedMap]);
    useIsoLayoutEffect(() => {
      const shouldUpdateLengths = lastTickRef.current === mapTick;
      if (shouldUpdateLengths) {
        if (elementsRef.current.length !== sortedMap.size) {
          elementsRef.current.length = sortedMap.size;
        }
        if (labelsRef && labelsRef.current.length !== sortedMap.size) {
          labelsRef.current.length = sortedMap.size;
        }
        nextIndexRef.current = sortedMap.size;
      }
      onMapChange(sortedMap);
    }, [onMapChange, sortedMap, elementsRef, labelsRef, mapTick]);
    useIsoLayoutEffect(() => {
      return () => {
        elementsRef.current = [];
      };
    }, [elementsRef]);
    useIsoLayoutEffect(() => {
      return () => {
        if (labelsRef) {
          labelsRef.current = [];
        }
      };
    }, [labelsRef]);
    const subscribeMapChange = useStableCallback((fn) => {
      listeners.add(fn);
      return () => {
        listeners.delete(fn);
      };
    });
    useIsoLayoutEffect(() => {
      listeners.forEach((l) => l(sortedMap));
    }, [listeners, sortedMap]);
    const contextValue = React6.useMemo(() => ({
      register,
      unregister,
      subscribeMapChange,
      elementsRef,
      labelsRef,
      nextIndexRef
    }), [register, unregister, subscribeMapChange, elementsRef, labelsRef, nextIndexRef]);
    return /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(CompositeListContext.Provider, {
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
  function sortByDocumentPosition(a, b) {
    const position = a.compareDocumentPosition(b);
    if (position & Node.DOCUMENT_POSITION_FOLLOWING || position & Node.DOCUMENT_POSITION_CONTAINED_BY) {
      return -1;
    }
    if (position & Node.DOCUMENT_POSITION_PRECEDING || position & Node.DOCUMENT_POSITION_CONTAINS) {
      return 1;
    }
    return 0;
  }
  function disableEslintWarning(_) {
  }

  // node_modules/@base-ui/react/esm/internals/direction-context/DirectionContext.js
  var React7 = __toESM(require_react(), 1);
  var DirectionContext = /* @__PURE__ */ React7.createContext(void 0);
  if (true) DirectionContext.displayName = "DirectionContext";
  function useDirection() {
    const context = React7.useContext(DirectionContext);
    return context?.direction ?? "ltr";
  }

  // node_modules/@base-ui/react/esm/internals/useRenderElement.js
  var React10 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/esm/useMergedRefs.js
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
    return forkRef.refs.length !== newRefs.length || forkRef.refs.some((ref, index) => ref !== newRefs[index]);
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
                  ref(null);
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

  // node_modules/@base-ui/utils/esm/getReactElementRef.js
  var React9 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/esm/reactVersion.js
  var React8 = __toESM(require_react(), 1);
  var majorVersion = parseInt(React8.version, 10);
  function isReactVersionAtLeast(reactVersionToCheck) {
    return majorVersion >= reactVersionToCheck;
  }

  // node_modules/@base-ui/utils/esm/getReactElementRef.js
  function getReactElementRef(element) {
    if (!/* @__PURE__ */ React9.isValidElement(element)) {
      return null;
    }
    const reactElement = element;
    const propsWithRef = reactElement.props;
    return (isReactVersionAtLeast(19) ? propsWithRef?.ref : reactElement.ref) ?? null;
  }

  // node_modules/@base-ui/utils/esm/mergeObjects.js
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

  // node_modules/@base-ui/utils/esm/empty.js
  function NOOP() {
  }
  var EMPTY_ARRAY = Object.freeze([]);
  var EMPTY_OBJECT = Object.freeze({});

  // node_modules/@base-ui/react/esm/internals/getStateAttributesProps.js
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

  // node_modules/@base-ui/react/esm/utils/resolveClassName.js
  function resolveClassName(className, state) {
    return typeof className === "function" ? className(state) : className;
  }

  // node_modules/@base-ui/react/esm/utils/resolveStyle.js
  function resolveStyle(style, state) {
    return typeof style === "function" ? style(state) : style;
  }

  // node_modules/@base-ui/react/esm/merge-props/mergeProps.js
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

  // node_modules/@base-ui/react/esm/internals/useRenderElement.js
  var import_react = __toESM(require_react(), 1);
  function useRenderElement(element, componentProps, params = {}) {
    const renderProp = componentProps.render;
    const outProps = useRenderElementProps(componentProps, params);
    if (params.enabled === false) {
      return null;
    }
    const state = params.state ?? EMPTY_OBJECT;
    return evaluateRenderProp(element, renderProp, outProps, state);
  }
  function useRenderElementProps(componentProps, params = {}) {
    const {
      className: classNameProp,
      style: styleProp,
      render: renderProp
    } = componentProps;
    const {
      state = EMPTY_OBJECT,
      ref,
      props,
      stateAttributesMapping: stateAttributesMapping3,
      enabled = true
    } = params;
    const className = enabled ? resolveClassName(classNameProp, state) : void 0;
    const style = enabled ? resolveStyle(styleProp, state) : void 0;
    const stateProps = enabled ? getStateAttributesProps(state, stateAttributesMapping3) : EMPTY_OBJECT;
    const resolvedProps = enabled && props ? resolveRenderFunctionProps(props) : void 0;
    const outProps = enabled ? mergeObjects(stateProps, resolvedProps) ?? {} : EMPTY_OBJECT;
    if (typeof document !== "undefined") {
      if (!enabled) {
        useMergedRefs(null, null);
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
      let newElement = render;
      if (newElement?.$$typeof === REACT_LAZY_TYPE) {
        const children = React10.Children.toArray(render);
        newElement = children[0];
      }
      if (true) {
        if (!/* @__PURE__ */ React10.isValidElement(newElement)) {
          throw new Error(["Base UI: The `render` prop was provided an invalid React element as `React.isValidElement(render)` is `false`.", "A valid React element must be provided to the `render` prop because it is cloned with props to replace the default element.", "https://base-ui.com/r/invalid-render-prop"].join("\n"));
        }
      }
      return /* @__PURE__ */ React10.cloneElement(newElement, mergedProps);
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
    return /* @__PURE__ */ React10.createElement(Tag, props);
  }

  // node_modules/@base-ui/react/esm/internals/reason-parts.js
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

  // node_modules/@base-ui/react/esm/internals/createBaseUIEventDetails.js
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

  // node_modules/@base-ui/utils/esm/useId.js
  var React11 = __toESM(require_react(), 1);
  var globalId = 0;
  function useGlobalId(idOverride, prefix = "mui") {
    const [defaultId, setDefaultId] = React11.useState(idOverride);
    const id = idOverride || defaultId;
    React11.useEffect(() => {
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

  // node_modules/@base-ui/react/esm/internals/useBaseUiId.js
  function useBaseUiId(idOverride) {
    return useId(idOverride, "base-ui");
  }

  // node_modules/@base-ui/react/esm/internals/useTransitionStatus.js
  var React13 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/esm/useOnMount.js
  var React12 = __toESM(require_react(), 1);
  var EMPTY = [];
  function useOnMount(fn) {
    React12.useEffect(fn, EMPTY);
  }

  // node_modules/@base-ui/utils/esm/useAnimationFrame.js
  var EMPTY2 = null;
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
      const index = id - this.startId;
      if (index < 0 || index >= this.callbacks.length) {
        return;
      }
      this.callbacks[index] = null;
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
    currentId = EMPTY2;
    /**
     * Executes `fn` after `delay`, clearing any previously scheduled call.
     */
    request(fn) {
      this.cancel();
      this.currentId = scheduler.request(() => {
        this.currentId = EMPTY2;
        fn();
      });
    }
    cancel = () => {
      if (this.currentId !== EMPTY2) {
        scheduler.cancel(this.currentId);
        this.currentId = EMPTY2;
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

  // node_modules/@base-ui/react/esm/internals/useTransitionStatus.js
  function useTransitionStatus(open, enableIdleState = false, deferEndingState = false) {
    const [transitionStatus, setTransitionStatus] = React13.useState(open && enableIdleState ? "idle" : void 0);
    const [mounted, setMounted] = React13.useState(open);
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

  // node_modules/@base-ui/react/esm/internals/composite/list/useCompositeListItem.js
  var React14 = __toESM(require_react(), 1);
  var IndexGuessBehavior = /* @__PURE__ */ (function(IndexGuessBehavior2) {
    IndexGuessBehavior2[IndexGuessBehavior2["None"] = 0] = "None";
    IndexGuessBehavior2[IndexGuessBehavior2["GuessFromOrder"] = 1] = "GuessFromOrder";
    return IndexGuessBehavior2;
  })({});
  function useCompositeListItem(params = {}) {
    const {
      label,
      metadata,
      textRef,
      indexGuessBehavior,
      index: externalIndex
    } = params;
    const {
      register,
      unregister,
      subscribeMapChange,
      elementsRef,
      labelsRef,
      nextIndexRef
    } = useCompositeListContext();
    const indexRef = React14.useRef(-1);
    const [index, setIndex] = React14.useState(externalIndex ?? (indexGuessBehavior === IndexGuessBehavior.GuessFromOrder ? () => {
      if (indexRef.current === -1) {
        const newIndex = nextIndexRef.current;
        nextIndexRef.current += 1;
        indexRef.current = newIndex;
      }
      return indexRef.current;
    } : -1));
    const componentRef = React14.useRef(null);
    const ref = React14.useCallback((node) => {
      componentRef.current = node;
      if (index !== -1 && node !== null) {
        elementsRef.current[index] = node;
        if (labelsRef) {
          const isLabelDefined = label !== void 0;
          labelsRef.current[index] = isLabelDefined ? label : textRef?.current?.textContent ?? node.textContent;
        }
      }
    }, [index, elementsRef, labelsRef, label, textRef]);
    useIsoLayoutEffect(() => {
      if (externalIndex != null) {
        return void 0;
      }
      const node = componentRef.current;
      if (node) {
        register(node, metadata);
        return () => {
          unregister(node);
        };
      }
      return void 0;
    }, [externalIndex, register, unregister, metadata]);
    useIsoLayoutEffect(() => {
      if (externalIndex != null) {
        return void 0;
      }
      return subscribeMapChange((map) => {
        const i = componentRef.current ? map.get(componentRef.current)?.index : null;
        if (i != null) {
          setIndex(i);
        }
      });
    }, [externalIndex, subscribeMapChange, setIndex]);
    return React14.useMemo(() => ({
      ref,
      index
    }), [index, ref]);
  }

  // node_modules/@base-ui/react/esm/internals/stateAttributesMapping.js
  var TransitionStatusDataAttributes = /* @__PURE__ */ (function(TransitionStatusDataAttributes2) {
    TransitionStatusDataAttributes2["startingStyle"] = "data-starting-style";
    TransitionStatusDataAttributes2["endingStyle"] = "data-ending-style";
    return TransitionStatusDataAttributes2;
  })({});
  var STARTING_HOOK = {
    [TransitionStatusDataAttributes.startingStyle]: ""
  };
  var ENDING_HOOK = {
    [TransitionStatusDataAttributes.endingStyle]: ""
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

  // node_modules/@base-ui/utils/esm/isElementDisabled.js
  function isElementDisabled(element) {
    return element == null || element.hasAttribute("disabled") || element.getAttribute("aria-disabled") === "true";
  }

  // node_modules/@base-ui/react/esm/internals/use-button/useButton.js
  var React17 = __toESM(require_react(), 1);

  // node_modules/@floating-ui/utils/dist/floating-ui.utils.dom.mjs
  function hasWindow() {
    return typeof window !== "undefined";
  }
  function getWindow(node) {
    var _node$ownerDocument;
    return (node == null || (_node$ownerDocument = node.ownerDocument) == null ? void 0 : _node$ownerDocument.defaultView) || window;
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
  function getComputedStyle2(element) {
    return getWindow(element).getComputedStyle(element);
  }

  // node_modules/@base-ui/react/esm/internals/composite/root/CompositeRootContext.js
  var React15 = __toESM(require_react(), 1);
  var CompositeRootContext = /* @__PURE__ */ React15.createContext(void 0);
  if (true) CompositeRootContext.displayName = "CompositeRootContext";
  function useCompositeRootContext(optional = false) {
    const context = React15.useContext(CompositeRootContext);
    if (context === void 0 && !optional) {
      throw new Error(true ? "Base UI: CompositeRootContext is missing. Composite parts must be placed within <Composite.Root>." : formatErrorMessage_default(16));
    }
    return context;
  }

  // node_modules/@base-ui/react/esm/utils/useFocusableWhenDisabled.js
  var React16 = __toESM(require_react(), 1);
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
    const props = React16.useMemo(() => {
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

  // node_modules/@base-ui/react/esm/internals/use-button/useButton.js
  function useButton(parameters = {}) {
    const {
      disabled: disabled2 = false,
      focusableWhenDisabled,
      tabIndex = 0,
      native: isNativeButton = true,
      composite: compositeProp
    } = parameters;
    const elementRef = React17.useRef(null);
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
      React17.useEffect(() => {
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
    const updateDisabled = React17.useCallback(() => {
      const element = elementRef.current;
      if (!isButtonElement(element)) {
        return;
      }
      if (isCompositeItem && disabled2 && focusableWhenDisabledProps.disabled === void 0 && element.disabled) {
        element.disabled = false;
      }
    }, [disabled2, focusableWhenDisabledProps.disabled, isCompositeItem]);
    useIsoLayoutEffect(updateDisabled, [updateDisabled]);
    const getButtonProps = React17.useCallback((externalProps = {}) => {
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
            if (isLink || isNativeButton && isButton) {
              currentTarget.click();
              event.preventBaseUIHandler();
            } else if (shouldClick) {
              externalOnClick?.(event);
              event.preventBaseUIHandler();
            }
            return;
          }
          if (shouldClick) {
            if (!isNativeButton && (isSpaceKey || isEnterKey)) {
              event.preventDefault();
            }
            if (!isNativeButton && isEnterKey) {
              externalOnClick?.(event);
            }
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
          if (event.target === event.currentTarget && !isNativeButton && !isCompositeItem && event.key === " ") {
            externalOnClick?.(event);
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
    return Boolean(elem?.tagName === "A" && elem?.href);
  }

  // node_modules/@base-ui/react/esm/floating-ui-react/utils/constants.js
  var ARROW_LEFT = "ArrowLeft";
  var ARROW_RIGHT = "ArrowRight";
  var ARROW_UP = "ArrowUp";
  var ARROW_DOWN = "ArrowDown";

  // node_modules/@base-ui/react/esm/internals/shadowDom.js
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
      return event.composedPath()[0];
    }
    return event.target;
  }

  // node_modules/@base-ui/react/esm/floating-ui-react/utils/event.js
  function stopEvent(event) {
    event.preventDefault();
    event.stopPropagation();
  }

  // node_modules/@floating-ui/utils/dist/floating-ui.utils.mjs
  var round = Math.round;
  var floor = Math.floor;

  // node_modules/@base-ui/react/esm/floating-ui-react/utils/composite.js
  function isDifferentGridRow(index, cols, prevRow) {
    return Math.floor(index / cols) !== prevRow;
  }
  function isIndexOutOfListBounds(list, index) {
    return index < 0 || index >= list.length;
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
    let index = startingIndex;
    do {
      index += decrement ? -amount : amount;
    } while (index >= 0 && index <= list.length - 1 && isListIndexDisabled(list, index, disabledIndices));
    return index;
  }
  function getGridNavigatedIndex(list, {
    event,
    orientation,
    loopFocus,
    onLoop,
    rtl,
    cols,
    disabledIndices,
    minIndex,
    maxIndex,
    prevIndex,
    stopEvent: stop = false
  }) {
    let nextIndex = prevIndex;
    let verticalDirection;
    if (event.key === ARROW_UP) {
      verticalDirection = "up";
    } else if (event.key === ARROW_DOWN) {
      verticalDirection = "down";
    }
    if (verticalDirection) {
      const rows = [];
      const rowIndexMap = [];
      let hasRoleRow = false;
      let visibleItemCount = 0;
      {
        let currentRowEl = null;
        let currentRowIndex = -1;
        list.forEach((el, idx) => {
          if (el == null) {
            return;
          }
          visibleItemCount += 1;
          const rowEl = el.closest('[role="row"]');
          if (rowEl) {
            hasRoleRow = true;
          }
          if (rowEl !== currentRowEl || currentRowIndex === -1) {
            currentRowEl = rowEl;
            currentRowIndex += 1;
            rows[currentRowIndex] = [];
          }
          rows[currentRowIndex].push(idx);
          rowIndexMap[idx] = currentRowIndex;
        });
      }
      let hasDomRows = false;
      let inferredDomCols = 0;
      if (hasRoleRow) {
        for (const row of rows) {
          const rowLength = row.length;
          if (rowLength > inferredDomCols) {
            inferredDomCols = rowLength;
          }
          if (rowLength !== cols) {
            hasDomRows = true;
          }
        }
      }
      const hasVirtualizedGaps = hasDomRows && visibleItemCount < list.length;
      const verticalCols = inferredDomCols || cols;
      const navigateVertically = (direction) => {
        if (!hasDomRows || prevIndex === -1) {
          return void 0;
        }
        const currentRow = rowIndexMap[prevIndex];
        if (currentRow == null) {
          return void 0;
        }
        const colInRow = rows[currentRow].indexOf(prevIndex);
        const step = direction === "up" ? -1 : 1;
        for (let nextRow = currentRow + step, i = 0; i < rows.length; i += 1, nextRow += step) {
          if (nextRow < 0 || nextRow >= rows.length) {
            if (!loopFocus || hasVirtualizedGaps) {
              return void 0;
            }
            nextRow = nextRow < 0 ? rows.length - 1 : 0;
            if (onLoop) {
              const clampedCol = Math.min(colInRow, rows[nextRow].length - 1);
              const targetItemIndex = rows[nextRow][clampedCol] ?? rows[nextRow][0];
              const returnedItemIndex = onLoop(event, prevIndex, targetItemIndex);
              nextRow = rowIndexMap[returnedItemIndex] ?? nextRow;
            }
          }
          const targetRow = rows[nextRow];
          for (let col = Math.min(colInRow, targetRow.length - 1); col >= 0; col -= 1) {
            const candidate = targetRow[col];
            if (!isListIndexDisabled(list, candidate, disabledIndices)) {
              return candidate;
            }
          }
        }
        return void 0;
      };
      const navigateVerticallyWithInferredRows = (direction) => {
        if (!hasVirtualizedGaps || prevIndex === -1) {
          return void 0;
        }
        const colInRow = prevIndex % verticalCols;
        const rowStep = direction === "up" ? -verticalCols : verticalCols;
        const lastRowStart = maxIndex - maxIndex % verticalCols;
        const rowCount = floor(maxIndex / verticalCols) + 1;
        for (let rowStart = prevIndex - colInRow + rowStep, i = 0; i < rowCount; i += 1, rowStart += rowStep) {
          if (rowStart < 0 || rowStart > maxIndex) {
            if (!loopFocus) {
              return void 0;
            }
            rowStart = rowStart < 0 ? lastRowStart : 0;
          }
          const rowEnd = Math.min(rowStart + verticalCols - 1, maxIndex);
          for (let candidate = Math.min(rowStart + colInRow, rowEnd); candidate >= rowStart; candidate -= 1) {
            if (!isListIndexDisabled(list, candidate, disabledIndices)) {
              return candidate;
            }
          }
        }
        return void 0;
      };
      if (stop) {
        stopEvent(event);
      }
      const verticalCandidate = navigateVertically(verticalDirection) ?? navigateVerticallyWithInferredRows(verticalDirection);
      if (verticalCandidate !== void 0) {
        nextIndex = verticalCandidate;
      } else if (prevIndex === -1) {
        nextIndex = verticalDirection === "up" ? maxIndex : minIndex;
      } else {
        nextIndex = findNonDisabledListIndex(list, {
          startingIndex: prevIndex,
          amount: verticalCols,
          decrement: verticalDirection === "up",
          disabledIndices
        });
        if (loopFocus) {
          if (verticalDirection === "up" && (prevIndex - verticalCols < minIndex || nextIndex < 0)) {
            const col = prevIndex % verticalCols;
            const maxCol = maxIndex % verticalCols;
            const offset = maxIndex - (maxCol - col);
            if (maxCol === col) {
              nextIndex = maxIndex;
            } else {
              nextIndex = maxCol > col ? offset : offset - verticalCols;
            }
            if (onLoop) {
              nextIndex = onLoop(event, prevIndex, nextIndex);
            }
          }
          if (verticalDirection === "down" && prevIndex + verticalCols > maxIndex) {
            nextIndex = findNonDisabledListIndex(list, {
              startingIndex: prevIndex % verticalCols - verticalCols,
              amount: verticalCols,
              disabledIndices
            });
            if (onLoop) {
              nextIndex = onLoop(event, prevIndex, nextIndex);
            }
          }
        }
      }
      if (isIndexOutOfListBounds(list, nextIndex)) {
        nextIndex = prevIndex;
      }
    }
    if (orientation === "both") {
      const prevRow = floor(prevIndex / cols);
      if (event.key === (rtl ? ARROW_LEFT : ARROW_RIGHT)) {
        if (stop) {
          stopEvent(event);
        }
        if (prevIndex % cols !== cols - 1) {
          nextIndex = findNonDisabledListIndex(list, {
            startingIndex: prevIndex,
            disabledIndices
          });
          if (loopFocus && isDifferentGridRow(nextIndex, cols, prevRow)) {
            nextIndex = findNonDisabledListIndex(list, {
              startingIndex: prevIndex - prevIndex % cols - 1,
              disabledIndices
            });
            if (onLoop) {
              nextIndex = onLoop(event, prevIndex, nextIndex);
            }
          }
        } else if (loopFocus) {
          nextIndex = findNonDisabledListIndex(list, {
            startingIndex: prevIndex - prevIndex % cols - 1,
            disabledIndices
          });
          if (onLoop) {
            nextIndex = onLoop(event, prevIndex, nextIndex);
          }
        }
        if (isDifferentGridRow(nextIndex, cols, prevRow)) {
          nextIndex = prevIndex;
        }
      }
      if (event.key === (rtl ? ARROW_RIGHT : ARROW_LEFT)) {
        if (stop) {
          stopEvent(event);
        }
        if (prevIndex % cols !== 0) {
          nextIndex = findNonDisabledListIndex(list, {
            startingIndex: prevIndex,
            decrement: true,
            disabledIndices
          });
          if (loopFocus && isDifferentGridRow(nextIndex, cols, prevRow)) {
            nextIndex = findNonDisabledListIndex(list, {
              startingIndex: prevIndex + (cols - prevIndex % cols),
              decrement: true,
              disabledIndices
            });
            if (onLoop) {
              nextIndex = onLoop(event, prevIndex, nextIndex);
            }
          }
        } else if (loopFocus) {
          nextIndex = findNonDisabledListIndex(list, {
            startingIndex: prevIndex + (cols - prevIndex % cols),
            decrement: true,
            disabledIndices
          });
          if (onLoop) {
            nextIndex = onLoop(event, prevIndex, nextIndex);
          }
        }
        if (isDifferentGridRow(nextIndex, cols, prevRow)) {
          nextIndex = prevIndex;
        }
      }
      const lastRow = floor(maxIndex / cols) === prevRow;
      if (isIndexOutOfListBounds(list, nextIndex)) {
        if (loopFocus && lastRow) {
          nextIndex = event.key === (rtl ? ARROW_RIGHT : ARROW_LEFT) ? maxIndex : findNonDisabledListIndex(list, {
            startingIndex: prevIndex - prevIndex % cols - 1,
            disabledIndices
          });
          if (onLoop) {
            nextIndex = onLoop(event, prevIndex, nextIndex);
          }
        } else {
          nextIndex = prevIndex;
        }
      }
    }
    return nextIndex;
  }
  function createGridCellMap(sizes, cols, dense) {
    const cellMap = [];
    let startIndex = 0;
    sizes.forEach(({
      width,
      height
    }, index) => {
      if (width > cols) {
        if (true) {
          throw new Error(`[Floating UI]: Invalid grid - item width at index ${index} is greater than grid columns`);
        }
      }
      let itemPlaced = false;
      if (dense) {
        startIndex = 0;
      }
      while (!itemPlaced) {
        const targetCells = [];
        for (let i = 0; i < width; i += 1) {
          for (let j = 0; j < height; j += 1) {
            targetCells.push(startIndex + i + j * cols);
          }
        }
        if (startIndex % cols + width <= cols && targetCells.every((cell) => cellMap[cell] == null)) {
          targetCells.forEach((cell) => {
            cellMap[cell] = index;
          });
          itemPlaced = true;
        } else {
          startIndex += 1;
        }
      }
    });
    return [...cellMap];
  }
  function getGridCellIndexOfCorner(index, sizes, cellMap, cols, corner) {
    if (index === -1) {
      return -1;
    }
    const firstCellIndex = cellMap.indexOf(index);
    const sizeItem = sizes[index];
    switch (corner) {
      case "tl":
        return firstCellIndex;
      case "tr":
        if (!sizeItem) {
          return firstCellIndex;
        }
        return firstCellIndex + sizeItem.width - 1;
      case "bl":
        if (!sizeItem) {
          return firstCellIndex;
        }
        return firstCellIndex + (sizeItem.height - 1) * cols;
      case "br":
        return cellMap.lastIndexOf(index);
      default:
        return -1;
    }
  }
  function getGridCellIndices(indices, cellMap) {
    return cellMap.flatMap((index, cellIndex) => indices.includes(index) ? [cellIndex] : []);
  }
  function isListIndexDisabled(list, index, disabledIndices) {
    const isExplicitlyDisabled = typeof disabledIndices === "function" ? disabledIndices(index) : disabledIndices?.includes(index) ?? false;
    if (isExplicitlyDisabled) {
      return true;
    }
    const element = list[index];
    if (!element) {
      return false;
    }
    if (!isElementVisible(element)) {
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

  // node_modules/@base-ui/utils/esm/owner.js
  function ownerDocument(node) {
    return node?.ownerDocument || document;
  }

  // node_modules/@base-ui/react/esm/internals/composite/composite.js
  var ARROW_UP2 = "ArrowUp";
  var ARROW_DOWN2 = "ArrowDown";
  var ARROW_LEFT2 = "ArrowLeft";
  var ARROW_RIGHT2 = "ArrowRight";
  var HOME = "Home";
  var END = "End";
  var HORIZONTAL_KEYS = /* @__PURE__ */ new Set([ARROW_LEFT2, ARROW_RIGHT2]);
  var HORIZONTAL_KEYS_WITH_EXTRA_KEYS = /* @__PURE__ */ new Set([ARROW_LEFT2, ARROW_RIGHT2, HOME, END]);
  var VERTICAL_KEYS = /* @__PURE__ */ new Set([ARROW_UP2, ARROW_DOWN2]);
  var VERTICAL_KEYS_WITH_EXTRA_KEYS = /* @__PURE__ */ new Set([ARROW_UP2, ARROW_DOWN2, HOME, END]);
  var ARROW_KEYS = /* @__PURE__ */ new Set([...HORIZONTAL_KEYS, ...VERTICAL_KEYS]);
  var COMPOSITE_KEYS = /* @__PURE__ */ new Set([...ARROW_KEYS, HOME, END]);
  var SHIFT = "Shift";
  var CONTROL = "Control";
  var ALT = "Alt";
  var META = "Meta";
  var MODIFIER_KEYS = /* @__PURE__ */ new Set([SHIFT, CONTROL, ALT, META]);
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
        if (elementOffsetLeft - elementStyles.scrollMarginRight < scrollContainer.scrollLeft + containerStyles.scrollPaddingLeft) {
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

  // node_modules/@base-ui/react/esm/internals/useOpenChangeComplete.js
  var React18 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/esm/internals/useAnimationsFinished.js
  var ReactDOM = __toESM(require_react_dom(), 1);

  // node_modules/@base-ui/react/esm/utils/resolveRef.js
  function resolveRef(maybeRef) {
    if (maybeRef == null) {
      return maybeRef;
    }
    return "current" in maybeRef ? maybeRef.current : maybeRef;
  }

  // node_modules/@base-ui/react/esm/internals/useAnimationsFinished.js
  function useAnimationsFinished(elementOrRef, waitForStartingStyleRemoved = false, treatAbortedAsFinished = true) {
    const frame = useAnimationFrame();
    return useStableCallback((fnToExecute, signal = null) => {
      frame.cancel();
      const element = resolveRef(elementOrRef);
      if (element == null) {
        return;
      }
      const resolvedElement = element;
      const done = () => {
        ReactDOM.flushSync(fnToExecute);
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
        }).catch(() => {
          if (treatAbortedAsFinished) {
            if (!signal?.aborted) {
              done();
            }
            return;
          }
          const currentAnimations = resolvedElement.getAnimations();
          if (!signal?.aborted && currentAnimations.length > 0 && currentAnimations.some((animation) => animation.pending || animation.playState !== "finished")) {
            exec();
          }
        });
      }
      if (waitForStartingStyleRemoved) {
        const startingStyleAttribute = TransitionStatusDataAttributes.startingStyle;
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

  // node_modules/@base-ui/react/esm/internals/useOpenChangeComplete.js
  function useOpenChangeComplete(parameters) {
    const {
      enabled = true,
      open,
      ref,
      onComplete: onCompleteParam
    } = parameters;
    const onComplete = useStableCallback(onCompleteParam);
    const runOnceAnimationsFinish = useAnimationsFinished(ref, open, false);
    React18.useEffect(() => {
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

  // node_modules/@base-ui/utils/esm/useForcedRerendering.js
  var React19 = __toESM(require_react(), 1);
  function useForcedRerendering() {
    const [, setState] = React19.useState({});
    return React19.useCallback(() => {
      setState({});
    }, []);
  }

  // node_modules/@base-ui/utils/esm/inertValue.js
  function inertValue(value) {
    if (isReactVersionAtLeast(19)) {
      return value;
    }
    return value ? "true" : void 0;
  }

  // node_modules/@base-ui/react/esm/internals/composite/item/useCompositeItem.js
  var React20 = __toESM(require_react(), 1);
  function useCompositeItem(params = {}) {
    const {
      highlightItemOnHover,
      highlightedIndex,
      onHighlightedIndexChange
    } = useCompositeRootContext();
    const {
      ref,
      index
    } = useCompositeListItem(params);
    const isHighlighted = highlightedIndex === index;
    const itemRef = React20.useRef(null);
    const mergedRef = useMergedRefs(ref, itemRef);
    const compositeProps = React20.useMemo(() => ({
      tabIndex: isHighlighted ? 0 : -1,
      onFocus() {
        onHighlightedIndexChange(index);
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
    }), [isHighlighted, onHighlightedIndexChange, index, highlightItemOnHover]);
    return {
      compositeProps,
      compositeRef: mergedRef,
      index
    };
  }

  // node_modules/@base-ui/react/esm/utils/getCssDimensions.js
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

  // node_modules/@base-ui/react/esm/internals/csp-context/CSPContext.js
  var React21 = __toESM(require_react(), 1);
  var CSPContext = /* @__PURE__ */ React21.createContext(void 0);
  if (true) CSPContext.displayName = "CSPContext";
  var DEFAULT_CSP_CONTEXT_VALUE = {
    disableStyleElements: false
  };
  function useCSPContext() {
    return React21.useContext(CSPContext) ?? DEFAULT_CSP_CONTEXT_VALUE;
  }

  // node_modules/@base-ui/react/esm/internals/composite/root/CompositeRoot.js
  var React23 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/esm/internals/composite/root/useCompositeRoot.js
  var React22 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/esm/internals/composite/constants.js
  var ACTIVE_COMPOSITE_ITEM = "data-composite-item-active";

  // node_modules/@base-ui/react/esm/internals/composite/root/useCompositeRoot.js
  var EMPTY_ARRAY2 = [];
  function useCompositeRoot(params) {
    const {
      itemSizes,
      cols = 1,
      loopFocus = true,
      onLoop,
      dense = false,
      orientation = "both",
      direction,
      highlightedIndex: externalHighlightedIndex,
      onHighlightedIndexChange: externalSetHighlightedIndex,
      rootRef: externalRef,
      enableHomeAndEndKeys = false,
      stopEventPropagation = false,
      disabledIndices,
      modifierKeys = EMPTY_ARRAY2
    } = params;
    const [internalHighlightedIndex, internalSetHighlightedIndex] = React22.useState(0);
    const isGrid = cols > 1;
    const rootRef = React22.useRef(null);
    const mergedRef = useMergedRefs(rootRef, externalRef);
    const elementsRef = React22.useRef([]);
    const hasSetDefaultIndexRef = React22.useRef(false);
    const highlightedIndex = externalHighlightedIndex ?? internalHighlightedIndex;
    const onHighlightedIndexChange = useStableCallback((index, shouldScrollIntoView = false) => {
      (externalSetHighlightedIndex ?? internalSetHighlightedIndex)(index);
      if (shouldScrollIntoView) {
        const newActiveItem = elementsRef.current[index];
        scrollIntoViewIfNeeded(rootRef.current, newActiveItem, direction, orientation);
      }
    });
    const onMapChange = useStableCallback((map) => {
      if (map.size === 0 || hasSetDefaultIndexRef.current) {
        return;
      }
      hasSetDefaultIndexRef.current = true;
      const sortedElements = Array.from(map.keys());
      const activeItem = sortedElements.find((compositeElement) => compositeElement?.hasAttribute(ACTIVE_COMPOSITE_ITEM)) ?? null;
      const activeIndex = activeItem ? sortedElements.indexOf(activeItem) : -1;
      if (activeIndex !== -1) {
        onHighlightedIndexChange(activeIndex);
      }
      scrollIntoViewIfNeeded(rootRef.current, activeItem, direction, orientation);
    });
    const wrappedOnLoop = useStableCallback((event, prevIndex, nextIndex) => {
      if (!onLoop) {
        return nextIndex;
      }
      return onLoop?.(event, prevIndex, nextIndex, elementsRef);
    });
    const props = React22.useMemo(() => ({
      "aria-orientation": orientation === "both" ? void 0 : orientation,
      ref: mergedRef,
      onFocus(event) {
        const element = rootRef.current;
        const target = getTarget(event.nativeEvent);
        if (!element || target == null || !isNativeInput(target)) {
          return;
        }
        target.setSelectionRange(0, target.value.length ?? 0);
      },
      onKeyDown(event) {
        const RELEVANT_KEYS = enableHomeAndEndKeys ? COMPOSITE_KEYS : ARROW_KEYS;
        if (!RELEVANT_KEYS.has(event.key)) {
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
        const horizontalForwardKey = isRtl ? ARROW_LEFT2 : ARROW_RIGHT2;
        const forwardKey = {
          horizontal: horizontalForwardKey,
          vertical: ARROW_DOWN2,
          both: horizontalForwardKey
        }[orientation];
        const horizontalBackwardKey = isRtl ? ARROW_RIGHT2 : ARROW_LEFT2;
        const backwardKey = {
          horizontal: horizontalBackwardKey,
          vertical: ARROW_UP2,
          both: horizontalBackwardKey
        }[orientation];
        const target = getTarget(event.nativeEvent);
        if (target != null && isNativeInput(target) && !isElementDisabled(target)) {
          const selectionStart = target.selectionStart;
          const selectionEnd = target.selectionEnd;
          const textContent = target.value ?? "";
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
        if (isGrid) {
          const sizes = itemSizes || Array.from({
            length: elementsRef.current.length
          }, () => ({
            width: 1,
            height: 1
          }));
          const cellMap = createGridCellMap(sizes, cols, dense);
          const minGridIndex = cellMap.findIndex((index) => index != null && !isListIndexDisabled(elementsRef.current, index, disabledIndices));
          const maxGridIndex = cellMap.reduce((foundIndex, index, cellIndex) => index != null && !isListIndexDisabled(elementsRef.current, index, disabledIndices) ? cellIndex : foundIndex, -1);
          nextIndex = cellMap[getGridNavigatedIndex(cellMap.map((itemIndex) => itemIndex != null ? elementsRef.current[itemIndex] : null), {
            event,
            orientation,
            loopFocus,
            onLoop: wrappedOnLoop,
            cols,
            // treat undefined (empty grid spaces) as disabled indices so we
            // don't end up in them
            disabledIndices: getGridCellIndices([...disabledIndices || elementsRef.current.map((_, index) => isListIndexDisabled(elementsRef.current, index) ? index : void 0), void 0], cellMap),
            minIndex: minGridIndex,
            maxIndex: maxGridIndex,
            prevIndex: getGridCellIndexOfCorner(
              highlightedIndex > maxIndex ? minIndex : highlightedIndex,
              sizes,
              cellMap,
              cols,
              // use a corner matching the edge closest to the direction we're
              // moving in so we don't end up in the same item. Prefer
              // top/left over bottom/right.
              // eslint-disable-next-line no-nested-ternary
              event.key === ARROW_DOWN2 ? "bl" : event.key === ARROW_RIGHT2 ? "tr" : "tl"
            ),
            rtl: isRtl
          })];
        }
        const forwardKeys = {
          horizontal: [horizontalForwardKey],
          vertical: [ARROW_DOWN2],
          both: [horizontalForwardKey, ARROW_DOWN2]
        }[orientation];
        const backwardKeys = {
          horizontal: [horizontalBackwardKey],
          vertical: [ARROW_UP2],
          both: [horizontalBackwardKey, ARROW_UP2]
        }[orientation];
        const preventedKeys = isGrid ? RELEVANT_KEYS : {
          horizontal: enableHomeAndEndKeys ? HORIZONTAL_KEYS_WITH_EXTRA_KEYS : HORIZONTAL_KEYS,
          vertical: enableHomeAndEndKeys ? VERTICAL_KEYS_WITH_EXTRA_KEYS : VERTICAL_KEYS,
          both: RELEVANT_KEYS
        }[orientation];
        if (enableHomeAndEndKeys) {
          if (event.key === HOME) {
            nextIndex = minIndex;
          } else if (event.key === END) {
            nextIndex = maxIndex;
          }
        }
        if (nextIndex === highlightedIndex && (forwardKeys.includes(event.key) || backwardKeys.includes(event.key))) {
          if (loopFocus && nextIndex === maxIndex && forwardKeys.includes(event.key)) {
            nextIndex = minIndex;
            if (onLoop) {
              nextIndex = onLoop(event, highlightedIndex, nextIndex, elementsRef);
            }
          } else if (loopFocus && nextIndex === minIndex && backwardKeys.includes(event.key)) {
            nextIndex = maxIndex;
            if (onLoop) {
              nextIndex = onLoop(event, highlightedIndex, nextIndex, elementsRef);
            }
          } else {
            nextIndex = findNonDisabledListIndex(elementsRef.current, {
              startingIndex: nextIndex,
              decrement: backwardKeys.includes(event.key),
              disabledIndices
            });
          }
        }
        if (nextIndex !== highlightedIndex && !isIndexOutOfListBounds(elementsRef.current, nextIndex)) {
          if (stopEventPropagation) {
            event.stopPropagation();
          }
          if (preventedKeys.has(event.key)) {
            event.preventDefault();
          }
          onHighlightedIndexChange(nextIndex, true);
          queueMicrotask(() => {
            elementsRef.current[nextIndex]?.focus();
          });
        }
      }
    }), [cols, dense, direction, disabledIndices, elementsRef, enableHomeAndEndKeys, highlightedIndex, isGrid, itemSizes, loopFocus, onLoop, wrappedOnLoop, mergedRef, modifierKeys, onHighlightedIndexChange, orientation, stopEventPropagation]);
    return React22.useMemo(() => ({
      props,
      highlightedIndex,
      onHighlightedIndexChange,
      elementsRef,
      disabledIndices,
      onMapChange,
      relayKeyboardEvent: props.onKeyDown
    }), [props, highlightedIndex, onHighlightedIndexChange, elementsRef, disabledIndices, onMapChange]);
  }
  function isModifierKeySet(event, ignoredModifierKeys) {
    for (const key of MODIFIER_KEYS.values()) {
      if (ignoredModifierKeys.includes(key)) {
        continue;
      }
      if (event.getModifierState(key)) {
        return true;
      }
    }
    return false;
  }

  // node_modules/@base-ui/react/esm/internals/composite/root/CompositeRoot.js
  var import_jsx_runtime18 = __toESM(require_jsx_runtime(), 1);
  function CompositeRoot(componentProps) {
    const {
      render,
      className,
      style,
      refs = EMPTY_ARRAY,
      props = EMPTY_ARRAY,
      state = EMPTY_OBJECT,
      stateAttributesMapping: stateAttributesMapping3,
      highlightedIndex: highlightedIndexProp,
      onHighlightedIndexChange: onHighlightedIndexChangeProp,
      orientation,
      dense,
      itemSizes,
      loopFocus,
      onLoop,
      cols,
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
      itemSizes,
      cols,
      loopFocus,
      onLoop,
      dense,
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
      stateAttributesMapping: stateAttributesMapping3
    });
    const contextValue = React23.useMemo(() => ({
      highlightedIndex,
      onHighlightedIndexChange,
      highlightItemOnHover,
      relayKeyboardEvent
    }), [highlightedIndex, onHighlightedIndexChange, highlightItemOnHover, relayKeyboardEvent]);
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(CompositeRootContext.Provider, {
      value: contextValue,
      children: /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(CompositeList, {
        elementsRef,
        onMapChange: (newMap) => {
          onMapChangeProp?.(newMap);
          onMapChangeUnwrapped(newMap);
        },
        children: element
      })
    });
  }

  // node_modules/@base-ui/react/esm/utils/useIsHydrating.js
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

  // node_modules/@base-ui/react/esm/tabs/index.parts.js
  var index_parts_exports = {};
  __export(index_parts_exports, {
    Indicator: () => TabsIndicator,
    List: () => TabsList,
    Panel: () => TabsPanel,
    Root: () => TabsRoot,
    Tab: () => TabsTab
  });

  // node_modules/@base-ui/react/esm/tabs/root/TabsRoot.js
  var React25 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/esm/tabs/root/TabsRootContext.js
  var React24 = __toESM(require_react(), 1);
  var TabsRootContext = /* @__PURE__ */ React24.createContext(void 0);
  if (true) TabsRootContext.displayName = "TabsRootContext";
  function useTabsRootContext() {
    const context = React24.useContext(TabsRootContext);
    if (context === void 0) {
      throw new Error(true ? "Base UI: TabsRootContext is missing. Tabs parts must be placed within <Tabs.Root>." : formatErrorMessage_default(64));
    }
    return context;
  }

  // node_modules/@base-ui/react/esm/tabs/root/TabsRootDataAttributes.js
  var TabsRootDataAttributes = /* @__PURE__ */ (function(TabsRootDataAttributes2) {
    TabsRootDataAttributes2["activationDirection"] = "data-activation-direction";
    TabsRootDataAttributes2["orientation"] = "data-orientation";
    return TabsRootDataAttributes2;
  })({});

  // node_modules/@base-ui/react/esm/tabs/root/stateAttributesMapping.js
  var tabsStateAttributesMapping = {
    tabActivationDirection: (dir) => ({
      [TabsRootDataAttributes.activationDirection]: dir
    })
  };

  // node_modules/@base-ui/react/esm/tabs/root/TabsRoot.js
  var import_jsx_runtime19 = __toESM(require_jsx_runtime(), 1);
  var TabsRoot = /* @__PURE__ */ React25.forwardRef(function TabsRoot2(componentProps, forwardedRef) {
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
    const tabPanelRefs = React25.useRef([]);
    const [mountedTabPanels, setMountedTabPanels] = React25.useState(() => /* @__PURE__ */ new Map());
    const [value, setValue] = useControlled({
      controlled: valueProp,
      default: defaultValueProp,
      name: "Tabs",
      state: "value"
    });
    const isControlled = valueProp !== void 0;
    const [tabMap, setTabMap] = React25.useState(() => /* @__PURE__ */ new Map());
    const getTabElementBySelectedValue = React25.useCallback((selectedValue) => {
      if (selectedValue === void 0) {
        return null;
      }
      for (const [tabElement, tabMetadata] of tabMap.entries()) {
        if (tabMetadata != null && selectedValue === (tabMetadata.value ?? tabMetadata.index)) {
          return tabElement;
        }
      }
      return null;
    }, [tabMap]);
    const [activationDirectionState, setActivationDirectionState] = React25.useState(() => ({
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
      const activationDirection = computeActivationDirection(value, newValue, orientation, tabMap);
      eventDetails.activationDirection = activationDirection;
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
        if (prev.get(panelValue) === panelId) {
          return prev;
        }
        const next = new Map(prev);
        next.set(panelValue, panelId);
        return next;
      });
    });
    const unregisterMountedTabPanel = useStableCallback((panelValue, panelId) => {
      setMountedTabPanels((prev) => {
        if (!prev.has(panelValue) || prev.get(panelValue) !== panelId) {
          return prev;
        }
        const next = new Map(prev);
        next.delete(panelValue);
        return next;
      });
    });
    const getTabPanelIdByValue = React25.useCallback((tabValue) => {
      return mountedTabPanels.get(tabValue);
    }, [mountedTabPanels]);
    const getTabIdByPanelValue = React25.useCallback((tabPanelValue) => {
      for (const tabMetadata of tabMap.values()) {
        if (tabPanelValue === tabMetadata?.value) {
          return tabMetadata?.id;
        }
      }
      return void 0;
    }, [tabMap]);
    const tabsContextValue = React25.useMemo(() => ({
      getTabElementBySelectedValue,
      getTabIdByPanelValue,
      getTabPanelIdByValue,
      onValueChange,
      orientation,
      registerMountedTabPanel,
      setTabMap,
      unregisterMountedTabPanel,
      tabActivationDirection,
      value
    }), [getTabElementBySelectedValue, getTabIdByPanelValue, getTabPanelIdByValue, onValueChange, orientation, registerMountedTabPanel, setTabMap, unregisterMountedTabPanel, tabActivationDirection, value]);
    const selectedTabMetadata = React25.useMemo(() => {
      for (const tabMetadata of tabMap.values()) {
        if (tabMetadata != null && tabMetadata.value === value) {
          return tabMetadata;
        }
      }
      return void 0;
    }, [tabMap, value]);
    const firstEnabledTabValue = React25.useMemo(() => {
      for (const tabMetadata of tabMap.values()) {
        if (tabMetadata != null && !tabMetadata.disabled) {
          return tabMetadata.value;
        }
      }
      return void 0;
    }, [tabMap]);
    const shouldNotifyInitialValueChangeRef = React25.useRef(!hasExplicitDefaultValueProp);
    const shouldHonorDisabledDefaultValueRef = React25.useRef(hasExplicitDefaultValueProp);
    const didRegisterTabsRef = React25.useRef(false);
    useIsoLayoutEffect(() => {
      if (isControlled) {
        return;
      }
      function commitAutomaticValueChange(fallbackValue, fallbackReason) {
        setValue(fallbackValue);
        setActivationDirectionState((prev) => {
          if (prev.previousValue === fallbackValue && prev.tabActivationDirection === "none") {
            return prev;
          }
          return {
            previousValue: fallbackValue,
            tabActivationDirection: "none"
          };
        });
        notifyAutomaticValueChange(fallbackValue, fallbackReason);
        shouldNotifyInitialValueChangeRef.current = false;
      }
      if (tabMap.size === 0) {
        if (!didRegisterTabsRef.current || value === null) {
          return;
        }
        commitAutomaticValueChange(null, reason_parts_exports.missing);
        return;
      }
      didRegisterTabsRef.current = true;
      const selectionIsDisabled = selectedTabMetadata?.disabled;
      const selectionIsMissing = selectedTabMetadata == null && value !== null;
      if (!selectionIsDisabled && value === defaultValueProp) {
        shouldHonorDisabledDefaultValueRef.current = false;
      }
      if (shouldHonorDisabledDefaultValueRef.current && selectionIsDisabled && value === defaultValueProp) {
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
    }, [defaultValueProp, firstEnabledTabValue, isControlled, notifyAutomaticValueChange, selectedTabMetadata, setValue, tabMap, value]);
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
    return /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(TabsRootContext.Provider, {
      value: tabsContextValue,
      children: /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(CompositeList, {
        elementsRef: tabPanelRefs,
        children: element
      })
    });
  });
  if (true) TabsRoot.displayName = "TabsRoot";
  function computeActivationDirection(oldValue, newValue, orientation, tabMap) {
    if (oldValue == null || newValue == null) {
      return "none";
    }
    let oldTab = null;
    let newTab = null;
    for (const [tabElement, tabMetadata] of tabMap.entries()) {
      if (tabMetadata == null) {
        continue;
      }
      const tabValue = tabMetadata.value ?? tabMetadata.index;
      if (oldValue === tabValue) {
        oldTab = tabElement;
      }
      if (newValue === tabValue) {
        newTab = tabElement;
      }
      if (oldTab != null && newTab != null) {
        break;
      }
    }
    if (oldTab == null || newTab == null) {
      if (oldTab !== newTab && (typeof oldValue === "number" || typeof oldValue === "string") && typeof oldValue === typeof newValue) {
        if (orientation === "horizontal") {
          return newValue > oldValue ? "right" : "left";
        }
        return newValue > oldValue ? "down" : "up";
      }
      return "none";
    }
    const oldRect = oldTab.getBoundingClientRect();
    const newRect = newTab.getBoundingClientRect();
    if (orientation === "horizontal") {
      if (newRect.left < oldRect.left) {
        return "left";
      }
      if (newRect.left > oldRect.left) {
        return "right";
      }
    } else {
      if (newRect.top < oldRect.top) {
        return "up";
      }
      if (newRect.top > oldRect.top) {
        return "down";
      }
    }
    return "none";
  }

  // node_modules/@base-ui/react/esm/tabs/tab/TabsTab.js
  var React27 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/esm/tabs/list/TabsListContext.js
  var React26 = __toESM(require_react(), 1);
  var TabsListContext = /* @__PURE__ */ React26.createContext(void 0);
  if (true) TabsListContext.displayName = "TabsListContext";
  function useTabsListContext() {
    const context = React26.useContext(TabsListContext);
    if (context === void 0) {
      throw new Error(true ? "Base UI: TabsListContext is missing. TabsList parts must be placed within <Tabs.List>." : formatErrorMessage_default(65));
    }
    return context;
  }

  // node_modules/@base-ui/react/esm/tabs/tab/TabsTab.js
  var TabsTab = /* @__PURE__ */ React27.forwardRef(function TabsTab2(componentProps, forwardedRef) {
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
      orientation
    } = useTabsRootContext();
    const {
      activateOnFocus,
      highlightedTabIndex,
      onTabActivation,
      registerTabResizeObserverElement,
      setHighlightedTabIndex,
      tabsListElement
    } = useTabsListContext();
    const id = useBaseUiId(idProp);
    const tabMetadata = React27.useMemo(() => ({
      disabled: disabled2,
      id,
      value
    }), [disabled2, id, value]);
    const {
      compositeProps,
      compositeRef,
      index
      // hook is used instead of the CompositeItem component
      // because the index is needed for Tab internals
    } = useCompositeItem({
      metadata: tabMetadata
    });
    const active = value === activeTabValue;
    const isNavigatingRef = React27.useRef(false);
    const tabElementRef = React27.useRef(null);
    React27.useEffect(() => {
      const tabElement = tabElementRef.current;
      if (!tabElement) {
        return void 0;
      }
      return registerTabResizeObserverElement(tabElement);
    }, [registerTabResizeObserverElement]);
    useIsoLayoutEffect(() => {
      if (isNavigatingRef.current) {
        isNavigatingRef.current = false;
        return;
      }
      if (!(active && index > -1 && highlightedTabIndex !== index)) {
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
        setHighlightedTabIndex(index);
      }
    }, [active, index, highlightedTabIndex, setHighlightedTabIndex, disabled2, tabsListElement]);
    const {
      getButtonProps,
      buttonRef
    } = useButton({
      disabled: disabled2,
      native: nativeButton,
      focusableWhenDisabled: true
    });
    const tabPanelId = getTabPanelIdByValue(value);
    const isPressingRef = React27.useRef(false);
    const isMainButtonRef = React27.useRef(false);
    function onClick(event) {
      if (active || disabled2) {
        return;
      }
      onTabActivation(value, createChangeEventDetails(reason_parts_exports.none, event.nativeEvent, void 0, {
        activationDirection: "none"
      }));
    }
    function onFocus(event) {
      if (active) {
        return;
      }
      if (index > -1 && !disabled2) {
        setHighlightedTabIndex(index);
      }
      if (disabled2) {
        return;
      }
      if (activateOnFocus && (!isPressingRef.current || // keyboard or touch focus
      isPressingRef.current && isMainButtonRef.current)) {
        onTabActivation(value, createChangeEventDetails(reason_parts_exports.none, event.nativeEvent, void 0, {
          activationDirection: "none"
        }));
      }
    }
    function onPointerDown(event) {
      if (active || disabled2) {
        return;
      }
      isPressingRef.current = true;
      function handlePointerUp() {
        isPressingRef.current = false;
        isMainButtonRef.current = false;
      }
      if (!event.button || event.button === 0) {
        isMainButtonRef.current = true;
        const doc = ownerDocument(event.currentTarget);
        doc.addEventListener("pointerup", handlePointerUp, {
          once: true
        });
      }
    }
    const state = {
      disabled: disabled2,
      active,
      orientation
    };
    const element = useRenderElement("button", componentProps, {
      state,
      ref: [forwardedRef, buttonRef, compositeRef, tabElementRef],
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
      }, elementProps, getButtonProps]
    });
    return element;
  });
  if (true) TabsTab.displayName = "TabsTab";

  // node_modules/@base-ui/react/esm/tabs/indicator/TabsIndicator.js
  var React28 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/esm/tabs/indicator/prehydrationScript.min.js
  var script = '!function(){const t=document.currentScript.previousElementSibling;if(!t)return;const e=t.closest(\'[role="tablist"]\');if(!e)return;const i=e.querySelector("[data-active]");if(!i)return;if(0===i.offsetWidth||0===e.offsetWidth)return;let o=0,n=0,h=0,l=0,r=0,f=0;function s(t){const e=getComputedStyle(t);let i=parseFloat(e.width)||0,o=parseFloat(e.height)||0;return(Math.round(i)!==t.offsetWidth||Math.round(o)!==t.offsetHeight)&&(i=t.offsetWidth,o=t.offsetHeight),{width:i,height:o}}if(null!=i&&null!=e){const{width:t,height:c}=s(i),{width:u,height:d}=s(e),a=i.getBoundingClientRect(),g=e.getBoundingClientRect(),p=u>0?g.width/u:1,b=d>0?g.height/d:1;if(Math.abs(p)>Number.EPSILON&&Math.abs(b)>Number.EPSILON){const t=a.left-g.left,i=a.top-g.top;o=t/p+e.scrollLeft-e.clientLeft,h=i/b+e.scrollTop-e.clientTop}else o=i.offsetLeft,h=i.offsetTop;r=t,f=c,n=e.scrollWidth-o-r,l=e.scrollHeight-h-f}function c(e,i){t.style.setProperty(`--active-tab-${e}`,`${i}px`)}c("left",o),c("right",n),c("top",h),c("bottom",l),c("width",r),c("height",f),r>0&&f>0&&t.removeAttribute("hidden")}();';

  // node_modules/@base-ui/react/esm/tabs/indicator/TabsIndicatorCssVars.js
  var TabsIndicatorCssVars = /* @__PURE__ */ (function(TabsIndicatorCssVars2) {
    TabsIndicatorCssVars2["activeTabLeft"] = "--active-tab-left";
    TabsIndicatorCssVars2["activeTabRight"] = "--active-tab-right";
    TabsIndicatorCssVars2["activeTabTop"] = "--active-tab-top";
    TabsIndicatorCssVars2["activeTabBottom"] = "--active-tab-bottom";
    TabsIndicatorCssVars2["activeTabWidth"] = "--active-tab-width";
    TabsIndicatorCssVars2["activeTabHeight"] = "--active-tab-height";
    return TabsIndicatorCssVars2;
  })({});

  // node_modules/@base-ui/react/esm/tabs/indicator/TabsIndicator.js
  var import_jsx_runtime20 = __toESM(require_jsx_runtime(), 1);
  var stateAttributesMapping = {
    ...tabsStateAttributesMapping,
    activeTabPosition: () => null,
    activeTabSize: () => null
  };
  var TabsIndicator = /* @__PURE__ */ React28.forwardRef(function TabsIndicator2(componentProps, forwardedRef) {
    const {
      className,
      render,
      renderBeforeHydration = false,
      style: styleProp,
      ...elementProps
    } = componentProps;
    const {
      nonce
    } = useCSPContext();
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
    const isHydrating = useIsHydrating();
    const rerender = useForcedRerendering();
    React28.useEffect(() => {
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
      isTabSelected = true;
      if (activeTab != null) {
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
        const hasNonZeroScale = Math.abs(scaleX) > Number.EPSILON && Math.abs(scaleY) > Number.EPSILON;
        if (hasNonZeroScale) {
          const tabLeftDelta = tabRect.left - tabsListRect.left;
          const tabTopDelta = tabRect.top - tabsListRect.top;
          left = tabLeftDelta / scaleX + tabsListElement.scrollLeft - tabsListElement.clientLeft;
          top = tabTopDelta / scaleY + tabsListElement.scrollTop - tabsListElement.clientTop;
        } else {
          left = activeTab.offsetLeft;
          top = activeTab.offsetTop;
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
      [TabsIndicatorCssVars.activeTabLeft]: `${left}px`,
      [TabsIndicatorCssVars.activeTabRight]: `${right}px`,
      [TabsIndicatorCssVars.activeTabTop]: `${top}px`,
      [TabsIndicatorCssVars.activeTabBottom]: `${bottom}px`,
      [TabsIndicatorCssVars.activeTabWidth]: `${width}px`,
      [TabsIndicatorCssVars.activeTabHeight]: `${height}px`
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
      stateAttributesMapping
    });
    if (value == null) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime20.jsxs)(React28.Fragment, {
      children: [element, isHydrating && renderBeforeHydration && /* @__PURE__ */ (0, import_jsx_runtime20.jsx)("script", {
        nonce,
        dangerouslySetInnerHTML: {
          __html: script
        },
        suppressHydrationWarning: true
      })]
    });
  });
  if (true) TabsIndicator.displayName = "TabsIndicator";

  // node_modules/@base-ui/react/esm/tabs/panel/TabsPanel.js
  var React29 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/esm/tabs/panel/TabsPanelDataAttributes.js
  var TabsPanelDataAttributes = (function(TabsPanelDataAttributes2) {
    TabsPanelDataAttributes2["index"] = "data-index";
    TabsPanelDataAttributes2["activationDirection"] = "data-activation-direction";
    TabsPanelDataAttributes2["orientation"] = "data-orientation";
    TabsPanelDataAttributes2["hidden"] = "data-hidden";
    TabsPanelDataAttributes2[TabsPanelDataAttributes2["startingStyle"] = TransitionStatusDataAttributes.startingStyle] = "startingStyle";
    TabsPanelDataAttributes2[TabsPanelDataAttributes2["endingStyle"] = TransitionStatusDataAttributes.endingStyle] = "endingStyle";
    return TabsPanelDataAttributes2;
  })({});

  // node_modules/@base-ui/react/esm/tabs/panel/TabsPanel.js
  var stateAttributesMapping2 = {
    ...tabsStateAttributesMapping,
    ...transitionStatusMapping
  };
  var TabsPanel = /* @__PURE__ */ React29.forwardRef(function TabsPanel2(componentProps, forwardedRef) {
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
      registerMountedTabPanel,
      unregisterMountedTabPanel
    } = useTabsRootContext();
    const id = useBaseUiId();
    const metadata = React29.useMemo(() => ({
      id,
      value
    }), [id, value]);
    const {
      ref: listItemRef,
      index
    } = useCompositeListItem({
      metadata
    });
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
    const panelRef = React29.useRef(null);
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
        [TabsPanelDataAttributes.index]: index
      }, elementProps],
      stateAttributesMapping: stateAttributesMapping2
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
      if (hidden && !keepMounted) {
        return void 0;
      }
      if (id == null) {
        return void 0;
      }
      registerMountedTabPanel(value, id);
      return () => {
        unregisterMountedTabPanel(value, id);
      };
    }, [hidden, keepMounted, value, id, registerMountedTabPanel, unregisterMountedTabPanel]);
    const shouldRender = keepMounted || mounted;
    if (!shouldRender) {
      return null;
    }
    return element;
  });
  if (true) TabsPanel.displayName = "TabsPanel";

  // node_modules/@base-ui/react/esm/tabs/list/TabsList.js
  var React30 = __toESM(require_react(), 1);
  var import_jsx_runtime21 = __toESM(require_jsx_runtime(), 1);
  var TabsList = /* @__PURE__ */ React30.forwardRef(function TabsList2(componentProps, forwardedRef) {
    const {
      activateOnFocus = false,
      className,
      loopFocus = true,
      render,
      style,
      ...elementProps
    } = componentProps;
    const {
      onValueChange,
      orientation,
      value,
      setTabMap,
      tabActivationDirection
    } = useTabsRootContext();
    const [highlightedTabIndex, setHighlightedTabIndex] = React30.useState(0);
    const [tabsListElement, setTabsListElement] = React30.useState(null);
    const indicatorUpdateListenersRef = React30.useRef(/* @__PURE__ */ new Set());
    const tabResizeObserverElementsRef = React30.useRef(/* @__PURE__ */ new Set());
    const resizeObserverRef = React30.useRef(null);
    React30.useEffect(() => {
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
    const onTabActivation = useStableCallback((newValue, eventDetails) => {
      if (newValue !== value) {
        onValueChange(newValue, eventDetails);
      }
    });
    const state = {
      orientation,
      tabActivationDirection
    };
    const defaultProps = {
      "aria-orientation": orientation === "vertical" ? "vertical" : void 0,
      role: "tablist"
    };
    const tabsListContextValue = React30.useMemo(() => ({
      activateOnFocus,
      highlightedTabIndex,
      registerIndicatorUpdateListener,
      registerTabResizeObserverElement,
      onTabActivation,
      setHighlightedTabIndex,
      tabsListElement
    }), [activateOnFocus, highlightedTabIndex, registerIndicatorUpdateListener, registerTabResizeObserverElement, onTabActivation, setHighlightedTabIndex, tabsListElement]);
    return /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(TabsListContext.Provider, {
      value: tabsListContextValue,
      children: /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(CompositeRoot, {
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

  // node_modules/@base-ui/react/esm/use-render/useRender.js
  function useRender(params) {
    return useRenderElement(params.defaultTagName ?? "div", params, params);
  }

  // packages/ui/build-module/icon/icon.mjs
  var import_element2 = __toESM(require_element(), 1);
  var import_primitives15 = __toESM(require_primitives(), 1);
  var import_jsx_runtime22 = __toESM(require_jsx_runtime(), 1);
  var Icon = (0, import_element2.forwardRef)(function Icon2({ icon, size = 24, ...restProps }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
      import_primitives15.SVG,
      {
        ref,
        fill: "currentColor",
        ...icon.props,
        ...restProps,
        width: size,
        height: size
      }
    );
  });

  // packages/ui/build-module/stack/stack.mjs
  var import_element3 = __toESM(require_element(), 1);
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
    registerStyle("32aba35fe1", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._19ce0419607e1896__stack{display:flex}}}");
  }
  var style_default = { "stack": "_19ce0419607e1896__stack" };
  var gapTokens = {
    xs: "var(--wpds-dimension-gap-xs, 4px)",
    sm: "var(--wpds-dimension-gap-sm, 8px)",
    md: "var(--wpds-dimension-gap-md, 12px)",
    lg: "var(--wpds-dimension-gap-lg, 16px)",
    xl: "var(--wpds-dimension-gap-xl, 24px)",
    "2xl": "var(--wpds-dimension-gap-2xl, 32px)",
    "3xl": "var(--wpds-dimension-gap-3xl, 40px)"
  };
  var Stack = (0, import_element3.forwardRef)(function Stack2({ direction, gap, align, justify, wrap, render, ...props }, ref) {
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
      props: mergeProps(props, { style, className: style_default.stack })
    });
    return element;
  });

  // packages/ui/build-module/utils/use-schedule-validation.mjs
  var import_element4 = __toESM(require_element(), 1);
  function useScheduleValidation(validate) {
    const validateRef = (0, import_element4.useRef)(validate);
    validateRef.current = validate;
    const timerRef = (0, import_element4.useRef)(null);
    const unmountedRef = (0, import_element4.useRef)(false);
    const scheduleValidation = (0, import_element4.useCallback)(() => {
      if (unmountedRef.current) {
        return;
      }
      if (timerRef.current) {
        clearTimeout(timerRef.current);
      }
      timerRef.current = setTimeout(() => {
        validateRef.current();
        timerRef.current = null;
      }, 0);
    }, []);
    (0, import_element4.useEffect)(() => {
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

  // packages/ui/build-module/visually-hidden/visually-hidden.mjs
  var import_element5 = __toESM(require_element(), 1);
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
    registerStyle2("fa606a57ae", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.f37b9e2e191ebd66__visually-hidden{word-wrap:normal;border:0;clip-path:inset(50%);height:1px;margin:-1px;overflow:hidden;padding:0;position:absolute;width:1px;word-break:normal}}}");
  }
  var style_default2 = { "visually-hidden": "f37b9e2e191ebd66__visually-hidden" };
  var VisuallyHidden = (0, import_element5.forwardRef)(
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

  // packages/ui/build-module/link/link.mjs
  var import_element6 = __toESM(require_element(), 1);
  var import_i18n3 = __toESM(require_i18n(), 1);
  var import_jsx_runtime23 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle3("10f3806643", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
  }
  var resets_default = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
  if (typeof process === "undefined" || true) {
    registerStyle3("693cd16544", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._08e8a2e44959f892__outset-ring--focus,._970d04df7376df67__outset-ring--focus-within-except-active,.c5cb3ee4bddaa8e4__outset-ring--focus-within-visible,.cd83dfc2126a0846__outset-ring--focus-within,.d0541bc9dd9dc7b6__outset-ring--focus-visible,.e25b2bdd7aa21721__outset-ring--focus-except-active,.ecadb9e080e2dfa5__outset-ring--focus-parent-visible{@media not (prefers-reduced-motion){--_gcd-a-transition:outline 0.1s ease-out;transition:outline .1s ease-out}outline:0 solid transparent;outline-offset:1px}._08e8a2e44959f892__outset-ring--focus:focus,._970d04df7376df67__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.c5cb3ee4bddaa8e4__outset-ring--focus-within-visible:focus-within:has(:focus-visible),.cd83dfc2126a0846__outset-ring--focus-within:focus-within,.d0541bc9dd9dc7b6__outset-ring--focus-visible:focus-visible,.e25b2bdd7aa21721__outset-ring--focus-except-active:focus:not(:active),:focus-visible .ecadb9e080e2dfa5__outset-ring--focus-parent-visible{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9))}}}");
  }
  var focus_default = { "outset-ring--focus": "_08e8a2e44959f892__outset-ring--focus", "outset-ring--focus-except-active": "e25b2bdd7aa21721__outset-ring--focus-except-active", "outset-ring--focus-visible": "d0541bc9dd9dc7b6__outset-ring--focus-visible", "outset-ring--focus-within": "cd83dfc2126a0846__outset-ring--focus-within", "outset-ring--focus-within-except-active": "_970d04df7376df67__outset-ring--focus-within-except-active", "outset-ring--focus-within-visible": "c5cb3ee4bddaa8e4__outset-ring--focus-within-visible", "outset-ring--focus-parent-visible": "ecadb9e080e2dfa5__outset-ring--focus-parent-visible" };
  if (typeof process === "undefined" || true) {
    registerStyle3("9f01019e30", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.d4250949359b05ce__link{text-decoration-thickness:from-font;text-underline-offset:.2em}.c6055659b8e2cd2c__is-brand,.c6055659b8e2cd2c__is-brand:visited{--_gcd-a-color:var(--wpds-color-fg-interactive-brand,var(--wp-admin-theme-color,#3858e9));color:var(--wpds-color-fg-interactive-brand,var(--wp-admin-theme-color,#3858e9))}.c6055659b8e2cd2c__is-brand:active,.c6055659b8e2cd2c__is-brand:hover{--_gcd-a-color:var(--wpds-color-fg-interactive-brand-active,var(--wp-admin-theme-color,#3858e9));color:var(--wpds-color-fg-interactive-brand-active,var(--wp-admin-theme-color,#3858e9))}._92e0dfcaeee15b88__is-neutral,._92e0dfcaeee15b88__is-neutral:visited{--_gcd-a-color:var(--wpds-color-fg-interactive-neutral,#1e1e1e);color:var(--wpds-color-fg-interactive-neutral,#1e1e1e);text-decoration-color:var(--wpds-color-stroke-interactive-neutral,#8d8d8d)}._92e0dfcaeee15b88__is-neutral:active,._92e0dfcaeee15b88__is-neutral:hover{--_gcd-a-color:var(--wpds-color-fg-interactive-neutral-active,#1e1e1e);color:var(--wpds-color-fg-interactive-neutral-active,#1e1e1e)}.cf122a9bf1035d42__is-unstyled{--_gcd-a-color:inherit;color:inherit;text-decoration:none}._0cb411afac4c86c7__link-icon{display:inline-block;font-weight:var(--wpds-typography-font-weight-regular,400);line-height:1;margin-inline-start:var(--wpds-dimension-padding-xs,4px);text-decoration:none}._0cb411afac4c86c7__link-icon:after{content:"\\2197"}._0cb411afac4c86c7__link-icon:dir(rtl):after{content:"\\2196"}}}');
  }
  var style_default3 = { "link": "d4250949359b05ce__link", "is-brand": "c6055659b8e2cd2c__is-brand", "is-neutral": "_92e0dfcaeee15b88__is-neutral", "is-unstyled": "cf122a9bf1035d42__is-unstyled", "link-icon": "_0cb411afac4c86c7__link-icon" };
  if (typeof process === "undefined" || true) {
    registerStyle3("d5c1b736fd", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-fg-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-fg-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-medium,499));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}");
  }
  var global_css_defense_default = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a" };
  var Link = (0, import_element6.forwardRef)(function Link2({
    children,
    variant = "default",
    tone = "brand",
    openInNewTab = false,
    render,
    className,
    ...props
  }, ref) {
    const element = useRender({
      render,
      defaultTagName: "a",
      ref,
      props: mergeProps(props, {
        className: clsx_default(
          global_css_defense_default.a,
          resets_default["box-sizing"],
          focus_default["outset-ring--focus"],
          variant !== "unstyled" && style_default3.link,
          variant !== "unstyled" && style_default3[`is-${tone}`],
          variant === "unstyled" && style_default3["is-unstyled"],
          className
        ),
        target: openInNewTab ? "_blank" : void 0,
        children: /* @__PURE__ */ (0, import_jsx_runtime23.jsxs)(import_jsx_runtime23.Fragment, { children: [
          children,
          openInNewTab && /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
            "span",
            {
              className: style_default3["link-icon"],
              role: "img",
              "aria-label": (
                /* translators: accessibility text appended to link text */
                (0, import_i18n3.__)("(opens in a new tab)")
              )
            }
          )
        ] })
      })
    });
    return element;
  });

  // packages/ui/build-module/tabs/index.mjs
  var tabs_exports = {};
  __export(tabs_exports, {
    List: () => List,
    Panel: () => Panel,
    Root: () => Root,
    Tab: () => Tab
  });

  // packages/ui/build-module/tabs/list.mjs
  var import_element7 = __toESM(require_element(), 1);
  var import_compose = __toESM(require_compose(), 1);
  var import_jsx_runtime24 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle4("8e0be0c342", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._7313adbc8a112e90__tablist{--direction-factor:1;--direction-start:left;--direction-end:right;align-items:stretch;display:flex;overflow-inline:auto;overscroll-behavior-inline:none;position:relative;&:dir(rtl){--direction-factor:-1;--direction-start:right;--direction-end:left}&[data-orientation=horizontal]{--fade-width:4rem;--fade-gradient-base:transparent 0%,#000 var(--fade-width);--fade-gradient-composed:var(--fade-gradient-base),#000 60%,transparent 50%;width:fit-content;&._9f2ac729c68a735a__is-overflowing-first{mask-image:linear-gradient(to var(--direction-end),var(--fade-gradient-base))}&._81c799c1f3cdd261__is-overflowing-last{mask-image:linear-gradient(to var(--direction-start),var(--fade-gradient-base))}&._9f2ac729c68a735a__is-overflowing-first._81c799c1f3cdd261__is-overflowing-last{mask-image:linear-gradient(to right,var(--fade-gradient-composed)),linear-gradient(to left,var(--fade-gradient-composed))}&._59228b5227f38a99__is-minimal-variant{gap:1rem}}&[data-orientation=vertical]{flex-direction:column}}._1c37dcfaa1ad8cda__indicator{@media not (prefers-reduced-motion){transition-duration:.2s;transition-property:translate,width,height,border-radius,border-block;transition-timing-function:ease-out}outline:2px solid transparent;outline-offset:-1px;pointer-events:none;position:absolute;&[data-orientation=horizontal]{background-color:var(--wpds-color-stroke-interactive-neutral-strong,#6e6e6e);bottom:0;height:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px));left:0;translate:var(--active-tab-left) 0;width:var(--active-tab-width);z-index:1}&[data-orientation=vertical]{background-color:var(--wpds-color-bg-interactive-neutral-weak-active,#ededed);border-radius:var(--wpds-border-radius-sm,2px);height:var(--active-tab-height);left:50%;top:0;translate:-50% var(--active-tab-top);width:100%;z-index:0}._7313adbc8a112e90__tablist[data-select-on-move=true]:has(:focus-visible)\n			&[data-orientation=vertical]{border:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));box-sizing:border-box}}.a5fd8814f195aa5e__tab{align-items:center;background:transparent;border:none;border-radius:0;box-shadow:none;color:var(--wpds-color-fg-interactive-neutral,#1e1e1e);cursor:var(--wpds-cursor-control,pointer);display:flex;flex:1 0 auto;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);font-weight:400;line-height:1.2;outline:none;padding:0;position:relative;white-space:nowrap;z-index:1;&[data-disabled]{color:var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d);cursor:default;@media (forced-colors:active){color:GrayText}}&:not([data-disabled]):is(:hover,:focus-visible){color:var(--wpds-color-fg-interactive-neutral-active,#1e1e1e)}&:after{border-radius:var(--wpds-border-radius-sm,2px);opacity:0;outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));pointer-events:none;position:absolute;z-index:-1;@media not (prefers-reduced-motion){transition:opacity .1s linear}}&:focus-visible:after{opacity:1}[data-orientation=horizontal] &{height:48px;padding-inline:var(--wpds-dimension-padding-lg,16px);scroll-margin:24px;&:after{content:"";inset:var(--wpds-dimension-padding-md,12px)}}._59228b5227f38a99__is-minimal-variant[data-orientation=horizontal] &{padding-inline:0;&:after{inset-inline:round(up,var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)),1px)}}[data-orientation=vertical] &{min-height:40px;padding:var(--wpds-dimension-padding-sm,8px) var(--wpds-dimension-padding-md,12px)}[data-orientation=vertical][data-select-on-move=false] &:after{content:"";inset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}._5dfc77e6edd345d4__tab-children{align-items:center;display:flex;flex-grow:1;[data-orientation=horizontal] &{justify-content:center}[data-orientation=vertical] &{justify-content:start}}._4a20e969d15e5ac1__tab-chevron{flex-shrink:0;margin-inline-end:calc(var(--wpds-dimension-gap-xs, 4px)*-1);opacity:0;[data-orientation=horizontal] &{display:none}[role=tab]:is([aria-selected=true],:focus-visible,:hover) &{opacity:1}@media not (prefers-reduced-motion){[data-select-on-move=true]\n				[role=tab]:is([aria-selected=true])\n				&{transition:opacity .15s linear .15s}}&:dir(rtl){rotate:180deg}}}}');
  }
  var style_default4 = { "tablist": "_7313adbc8a112e90__tablist", "is-overflowing-first": "_9f2ac729c68a735a__is-overflowing-first", "is-overflowing-last": "_81c799c1f3cdd261__is-overflowing-last", "is-minimal-variant": "_59228b5227f38a99__is-minimal-variant", "indicator": "_1c37dcfaa1ad8cda__indicator", "tab": "a5fd8814f195aa5e__tab", "tab-children": "_5dfc77e6edd345d4__tab-children", "tab-chevron": "_4a20e969d15e5ac1__tab-chevron" };
  var SCROLL_EPSILON = 1;
  var List = (0, import_element7.forwardRef)(
    function TabList({
      children,
      variant = "default",
      className,
      activateOnFocus,
      ...otherProps
    }, forwardedRef) {
      const [listEl, setListEl] = (0, import_element7.useState)(null);
      const [overflow, setOverflow] = (0, import_element7.useState)({
        first: false,
        last: false,
        isScrolling: false
      });
      (0, import_element7.useEffect)(() => {
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
          setOverflow({
            first: scrollFromStart > SCROLL_EPSILON,
            last: scrollFromStart < maxScroll - SCROLL_EPSILON,
            isScrolling: scrollWidth > clientWidth
          });
        };
        const resizeObserver = new ResizeObserver(measureOverflow);
        resizeObserver.observe(listEl);
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
        measureOverflow();
        return () => {
          listEl.removeEventListener(
            "scroll",
            throttleMeasureOverflowOnScroll
          );
          resizeObserver.disconnect();
        };
      }, [listEl]);
      const mergedListRef = (0, import_compose.useMergeRefs)([
        forwardedRef,
        (el) => setListEl(el)
      ]);
      return /* @__PURE__ */ (0, import_jsx_runtime24.jsxs)(
        index_parts_exports.List,
        {
          ref: mergedListRef,
          activateOnFocus,
          "data-select-on-move": activateOnFocus ? "true" : "false",
          className: clsx_default(
            style_default4.tablist,
            overflow.first && style_default4["is-overflowing-first"],
            overflow.last && style_default4["is-overflowing-last"],
            style_default4[`is-${variant}-variant`],
            className
          ),
          ...otherProps,
          tabIndex: otherProps.tabIndex ?? (overflow.isScrolling ? -1 : void 0),
          children: [
            children,
            /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(index_parts_exports.Indicator, { className: style_default4.indicator })
          ]
        }
      );
    }
  );

  // packages/ui/build-module/tabs/panel.mjs
  var import_element9 = __toESM(require_element(), 1);

  // packages/ui/build-module/tabs/context.mjs
  var import_element8 = __toESM(require_element(), 1);
  var import_jsx_runtime25 = __toESM(require_jsx_runtime(), 1);
  var VALIDATION_ENABLED = true;
  var TabsValidationContext = VALIDATION_ENABLED ? (0, import_element8.createContext)(null) : null;
  function useRegisterTabDev() {
    const context = (0, import_element8.useContext)(TabsValidationContext);
    (0, import_element8.useEffect)(() => {
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
    const context = (0, import_element8.useContext)(TabsValidationContext);
    (0, import_element8.useEffect)(() => {
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
    const tabCountRef = (0, import_element8.useRef)(0);
    const panelCountRef = (0, import_element8.useRef)(0);
    const scheduleValidation = useScheduleValidation(() => {
      const tabCount = tabCountRef.current;
      const panelCount = panelCountRef.current;
      if (tabCount !== panelCount) {
        throw new Error(
          `Tabs: Tab/Panel count mismatch (${tabCount} Tabs, ${panelCount} Panels). Each Tab must be associated with exactly one Panel. Mismatched or missing associations can break screen reader navigation and violate WAI-ARIA Tabs pattern requirements.`
        );
      }
    });
    const registerTab = (0, import_element8.useCallback)(() => {
      tabCountRef.current += 1;
      scheduleValidation();
      return () => {
        tabCountRef.current -= 1;
        scheduleValidation();
      };
    }, [scheduleValidation]);
    const registerPanel = (0, import_element8.useCallback)(() => {
      panelCountRef.current += 1;
      scheduleValidation();
      return () => {
        panelCountRef.current -= 1;
        scheduleValidation();
      };
    }, [scheduleValidation]);
    const contextValue = (0, import_element8.useMemo)(
      () => ({
        registerTab,
        registerPanel
      }),
      [registerTab, registerPanel]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(TabsValidationContext.Provider, { value: contextValue, children });
  }
  function TabsValidationProviderProd({
    children
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(import_jsx_runtime25.Fragment, { children });
  }
  var TabsValidationProvider = VALIDATION_ENABLED ? TabsValidationProviderDev : TabsValidationProviderProd;

  // packages/ui/build-module/tabs/panel.mjs
  var import_jsx_runtime26 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle5("d5c1b736fd", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-fg-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-fg-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-medium,499));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}");
  }
  var global_css_defense_default2 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a" };
  if (typeof process === "undefined" || true) {
    registerStyle5("693cd16544", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._08e8a2e44959f892__outset-ring--focus,._970d04df7376df67__outset-ring--focus-within-except-active,.c5cb3ee4bddaa8e4__outset-ring--focus-within-visible,.cd83dfc2126a0846__outset-ring--focus-within,.d0541bc9dd9dc7b6__outset-ring--focus-visible,.e25b2bdd7aa21721__outset-ring--focus-except-active,.ecadb9e080e2dfa5__outset-ring--focus-parent-visible{@media not (prefers-reduced-motion){--_gcd-a-transition:outline 0.1s ease-out;transition:outline .1s ease-out}outline:0 solid transparent;outline-offset:1px}._08e8a2e44959f892__outset-ring--focus:focus,._970d04df7376df67__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.c5cb3ee4bddaa8e4__outset-ring--focus-within-visible:focus-within:has(:focus-visible),.cd83dfc2126a0846__outset-ring--focus-within:focus-within,.d0541bc9dd9dc7b6__outset-ring--focus-visible:focus-visible,.e25b2bdd7aa21721__outset-ring--focus-except-active:focus:not(:active),:focus-visible .ecadb9e080e2dfa5__outset-ring--focus-parent-visible{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9))}}}");
  }
  var focus_default2 = { "outset-ring--focus": "_08e8a2e44959f892__outset-ring--focus", "outset-ring--focus-except-active": "e25b2bdd7aa21721__outset-ring--focus-except-active", "outset-ring--focus-visible": "d0541bc9dd9dc7b6__outset-ring--focus-visible", "outset-ring--focus-within": "cd83dfc2126a0846__outset-ring--focus-within", "outset-ring--focus-within-except-active": "_970d04df7376df67__outset-ring--focus-within-except-active", "outset-ring--focus-within-visible": "c5cb3ee4bddaa8e4__outset-ring--focus-within-visible", "outset-ring--focus-parent-visible": "ecadb9e080e2dfa5__outset-ring--focus-parent-visible" };
  var Panel = (0, import_element9.forwardRef)(
    function TabPanel({ className, ...otherProps }, forwardedRef) {
      useRegisterPanel();
      return /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
        index_parts_exports.Panel,
        {
          ref: forwardedRef,
          className: clsx_default(
            global_css_defense_default2.div,
            focus_default2["outset-ring--focus-visible"],
            className
          ),
          ...otherProps
        }
      );
    }
  );

  // packages/ui/build-module/tabs/root.mjs
  var import_element10 = __toESM(require_element(), 1);
  var import_jsx_runtime27 = __toESM(require_jsx_runtime(), 1);
  var Root = (0, import_element10.forwardRef)(
    function TabsRoot3({ ...otherProps }, forwardedRef) {
      return /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(TabsValidationProvider, { children: /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(index_parts_exports.Root, { ref: forwardedRef, ...otherProps }) });
    }
  );

  // packages/ui/build-module/tabs/tab.mjs
  var import_element11 = __toESM(require_element(), 1);
  var import_jsx_runtime28 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle6("8e0be0c342", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._7313adbc8a112e90__tablist{--direction-factor:1;--direction-start:left;--direction-end:right;align-items:stretch;display:flex;overflow-inline:auto;overscroll-behavior-inline:none;position:relative;&:dir(rtl){--direction-factor:-1;--direction-start:right;--direction-end:left}&[data-orientation=horizontal]{--fade-width:4rem;--fade-gradient-base:transparent 0%,#000 var(--fade-width);--fade-gradient-composed:var(--fade-gradient-base),#000 60%,transparent 50%;width:fit-content;&._9f2ac729c68a735a__is-overflowing-first{mask-image:linear-gradient(to var(--direction-end),var(--fade-gradient-base))}&._81c799c1f3cdd261__is-overflowing-last{mask-image:linear-gradient(to var(--direction-start),var(--fade-gradient-base))}&._9f2ac729c68a735a__is-overflowing-first._81c799c1f3cdd261__is-overflowing-last{mask-image:linear-gradient(to right,var(--fade-gradient-composed)),linear-gradient(to left,var(--fade-gradient-composed))}&._59228b5227f38a99__is-minimal-variant{gap:1rem}}&[data-orientation=vertical]{flex-direction:column}}._1c37dcfaa1ad8cda__indicator{@media not (prefers-reduced-motion){transition-duration:.2s;transition-property:translate,width,height,border-radius,border-block;transition-timing-function:ease-out}outline:2px solid transparent;outline-offset:-1px;pointer-events:none;position:absolute;&[data-orientation=horizontal]{background-color:var(--wpds-color-stroke-interactive-neutral-strong,#6e6e6e);bottom:0;height:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px));left:0;translate:var(--active-tab-left) 0;width:var(--active-tab-width);z-index:1}&[data-orientation=vertical]{background-color:var(--wpds-color-bg-interactive-neutral-weak-active,#ededed);border-radius:var(--wpds-border-radius-sm,2px);height:var(--active-tab-height);left:50%;top:0;translate:-50% var(--active-tab-top);width:100%;z-index:0}._7313adbc8a112e90__tablist[data-select-on-move=true]:has(:focus-visible)\n			&[data-orientation=vertical]{border:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));box-sizing:border-box}}.a5fd8814f195aa5e__tab{align-items:center;background:transparent;border:none;border-radius:0;box-shadow:none;color:var(--wpds-color-fg-interactive-neutral,#1e1e1e);cursor:var(--wpds-cursor-control,pointer);display:flex;flex:1 0 auto;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);font-weight:400;line-height:1.2;outline:none;padding:0;position:relative;white-space:nowrap;z-index:1;&[data-disabled]{color:var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d);cursor:default;@media (forced-colors:active){color:GrayText}}&:not([data-disabled]):is(:hover,:focus-visible){color:var(--wpds-color-fg-interactive-neutral-active,#1e1e1e)}&:after{border-radius:var(--wpds-border-radius-sm,2px);opacity:0;outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));pointer-events:none;position:absolute;z-index:-1;@media not (prefers-reduced-motion){transition:opacity .1s linear}}&:focus-visible:after{opacity:1}[data-orientation=horizontal] &{height:48px;padding-inline:var(--wpds-dimension-padding-lg,16px);scroll-margin:24px;&:after{content:"";inset:var(--wpds-dimension-padding-md,12px)}}._59228b5227f38a99__is-minimal-variant[data-orientation=horizontal] &{padding-inline:0;&:after{inset-inline:round(up,var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)),1px)}}[data-orientation=vertical] &{min-height:40px;padding:var(--wpds-dimension-padding-sm,8px) var(--wpds-dimension-padding-md,12px)}[data-orientation=vertical][data-select-on-move=false] &:after{content:"";inset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}._5dfc77e6edd345d4__tab-children{align-items:center;display:flex;flex-grow:1;[data-orientation=horizontal] &{justify-content:center}[data-orientation=vertical] &{justify-content:start}}._4a20e969d15e5ac1__tab-chevron{flex-shrink:0;margin-inline-end:calc(var(--wpds-dimension-gap-xs, 4px)*-1);opacity:0;[data-orientation=horizontal] &{display:none}[role=tab]:is([aria-selected=true],:focus-visible,:hover) &{opacity:1}@media not (prefers-reduced-motion){[data-select-on-move=true]\n				[role=tab]:is([aria-selected=true])\n				&{transition:opacity .15s linear .15s}}&:dir(rtl){rotate:180deg}}}}');
  }
  var style_default5 = { "tablist": "_7313adbc8a112e90__tablist", "is-overflowing-first": "_9f2ac729c68a735a__is-overflowing-first", "is-overflowing-last": "_81c799c1f3cdd261__is-overflowing-last", "is-minimal-variant": "_59228b5227f38a99__is-minimal-variant", "indicator": "_1c37dcfaa1ad8cda__indicator", "tab": "a5fd8814f195aa5e__tab", "tab-children": "_5dfc77e6edd345d4__tab-children", "tab-chevron": "_4a20e969d15e5ac1__tab-chevron" };
  var Tab = (0, import_element11.forwardRef)(function Tab2({ className, children, ...otherProps }, forwardedRef) {
    useRegisterTab();
    return /* @__PURE__ */ (0, import_jsx_runtime28.jsxs)(
      index_parts_exports.Tab,
      {
        ref: forwardedRef,
        className: clsx_default(style_default5.tab, className),
        ...otherProps,
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime28.jsx)("span", { className: style_default5["tab-children"], children }),
          /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(Icon, { icon: chevron_right_default, className: style_default5["tab-chevron"] })
        ]
      }
    );
  });

  // packages/format-library/build-module/image/index.mjs
  var import_i18n4 = __toESM(require_i18n(), 1);
  var import_element12 = __toESM(require_element(), 1);
  var import_rich_text3 = __toESM(require_rich_text(), 1);
  var import_block_editor3 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime29 = __toESM(require_jsx_runtime(), 1);
  var ALLOWED_MEDIA_TYPES = ["image"];
  var name3 = "core/image";
  var title3 = (0, import_i18n4.__)("Inline image");
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
    keywords: [(0, import_i18n4.__)("photo"), (0, import_i18n4.__)("media")],
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
  function InlineUI({ value, onChange, activeObjectAttributes, contentRef }) {
    const { style, alt } = activeObjectAttributes;
    const width = style?.replace(/\D/g, "");
    const [editedWidth, setEditedWidth] = (0, import_element12.useState)(width);
    const [editedAlt, setEditedAlt] = (0, import_element12.useState)(alt);
    const hasChanged = editedWidth !== width || editedAlt !== alt;
    const popoverAnchor = (0, import_rich_text3.useAnchor)({
      editableContentElement: contentRef.current,
      settings: image
    });
    return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
      import_components.Popover,
      {
        focusOnMount: false,
        anchor: popoverAnchor,
        className: "block-editor-format-toolbar__image-popover",
        children: /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
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
                  alt: editedAlt
                }
              };
              onChange({
                ...value,
                replacements: newReplacements
              });
              event.preventDefault();
            },
            children: /* @__PURE__ */ (0, import_jsx_runtime29.jsxs)(Stack, { direction: "column", gap: "lg", children: [
              /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
                import_components.__experimentalNumberControl,
                {
                  __next40pxDefaultSize: true,
                  label: (0, import_i18n4.__)("Width"),
                  value: editedWidth,
                  min: 1,
                  onChange: (newWidth) => {
                    setEditedWidth(newWidth);
                  }
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
                import_components.TextareaControl,
                {
                  label: (0, import_i18n4.__)("Alternative text"),
                  value: editedAlt,
                  onChange: (newAlt) => {
                    setEditedAlt(newAlt);
                  },
                  help: /* @__PURE__ */ (0, import_jsx_runtime29.jsxs)(import_jsx_runtime29.Fragment, { children: [
                    /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
                      Link,
                      {
                        openInNewTab: true,
                        href: (
                          // translators: Localized tutorial, if one exists. W3C Web Accessibility Initiative link has list of existing translations.
                          (0, import_i18n4.__)(
                            "https://www.w3.org/WAI/tutorials/images/decision-tree/"
                          )
                        ),
                        children: (0, import_i18n4.__)(
                          "Describe the purpose of the image."
                        )
                      }
                    ),
                    /* @__PURE__ */ (0, import_jsx_runtime29.jsx)("br", {}),
                    (0, import_i18n4.__)("Leave empty if decorative.")
                  ] })
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(Stack, { justify: "right", children: /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
                import_components.Button,
                {
                  disabled: !hasChanged,
                  accessibleWhenDisabled: true,
                  variant: "primary",
                  type: "submit",
                  size: "compact",
                  children: (0, import_i18n4.__)("Apply")
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
    return /* @__PURE__ */ (0, import_jsx_runtime29.jsxs)(import_block_editor3.MediaUploadCheck, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
        import_block_editor3.MediaUpload,
        {
          allowedTypes: ALLOWED_MEDIA_TYPES,
          value: getCurrentImageId(activeObjectAttributes),
          onSelect: ({ id, url, alt, width: imgWidth }) => {
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
          render: ({ open }) => /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
            import_block_editor3.RichTextToolbarButton,
            {
              icon: /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
                import_components.SVG,
                {
                  xmlns: "http://www.w3.org/2000/svg",
                  viewBox: "0 0 24 24",
                  children: /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(import_components.Path, { d: "M4 18.5h16V17H4v1.5zM16 13v1.5h4V13h-4zM5.1 15h7.8c.6 0 1.1-.5 1.1-1.1V6.1c0-.6-.5-1.1-1.1-1.1H5.1C4.5 5 4 5.5 4 6.1v7.8c0 .6.5 1.1 1.1 1.1zm.4-8.5h7V10l-1-1c-.3-.3-.8-.3-1 0l-1.6 1.5-1.2-.7c-.3-.2-.6-.2-.9 0l-1.3 1V6.5zm0 6.1l1.8-1.3 1.3.8c.3.2.7.2.9-.1l1.5-1.4 1.5 1.4v1.5h-7v-.9z" })
                }
              ),
              title: isObjectActive ? (0, import_i18n4.__)("Replace image") : title3,
              onClick: open,
              isActive: isObjectActive
            }
          )
        }
      ),
      isObjectActive && /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
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
  var import_i18n5 = __toESM(require_i18n(), 1);
  var import_rich_text4 = __toESM(require_rich_text(), 1);
  var import_block_editor4 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime30 = __toESM(require_jsx_runtime(), 1);
  var name4 = "core/italic";
  var title4 = (0, import_i18n5.__)("Italic");
  var italic = {
    name: name4,
    title: title4,
    tagName: "em",
    className: null,
    edit({ isActive, value, onChange, onFocus, isVisible = true }) {
      function onToggle() {
        onChange((0, import_rich_text4.toggleFormat)(value, { type: name4, title: title4 }));
      }
      function onClick() {
        onChange((0, import_rich_text4.toggleFormat)(value, { type: name4 }));
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime30.jsxs)(import_jsx_runtime30.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
          import_block_editor4.RichTextShortcut,
          {
            type: "primary",
            character: "i",
            onUse: onToggle
          }
        ),
        isVisible && /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
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
        /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
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
  var import_i18n8 = __toESM(require_i18n(), 1);
  var import_element15 = __toESM(require_element(), 1);
  var import_rich_text6 = __toESM(require_rich_text(), 1);
  var import_url3 = __toESM(require_url(), 1);
  var import_block_editor6 = __toESM(require_block_editor(), 1);
  var import_html_entities = __toESM(require_html_entities(), 1);
  var import_a11y2 = __toESM(require_a11y(), 1);

  // packages/format-library/build-module/link/inline.mjs
  var import_element14 = __toESM(require_element(), 1);
  var import_i18n7 = __toESM(require_i18n(), 1);
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
      if (!(0, import_url.isValidProtocol)(protocol)) {
        return false;
      }
      if (protocol.startsWith("http") && !/^https?:\/\/[^\/\s]/i.test(trimmedHref)) {
        return false;
      }
      const authority = (0, import_url.getAuthority)(trimmedHref);
      if (!(0, import_url.isValidAuthority)(authority)) {
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
    const index = newFormats[initialIndex].indexOf(targetFormat);
    const walkingArgs = [newFormats, initialIndex, targetFormat, index];
    startIndex = walkToStart(...walkingArgs);
    endIndex = walkToEnd(...walkingArgs);
    startIndex = startIndex < 0 ? 0 : startIndex;
    return {
      start: startIndex,
      end: endIndex + 1
    };
  }
  function walkToBoundary(formats, initialIndex, targetFormatRef, formatIndex, direction) {
    let index = initialIndex;
    const directions = {
      forwards: 1,
      backwards: -1
    };
    const directionIncrement = directions[direction] || 1;
    const inverseDirectionIncrement = directionIncrement * -1;
    while (formats[index] && formats[index][formatIndex] === targetFormatRef) {
      index = index + directionIncrement;
    }
    index = index + inverseDirectionIncrement;
    return index;
  }
  var partialRight = (fn, ...partialArgs) => (...args) => fn(...args, ...partialArgs);
  var walkToStart = partialRight(walkToBoundary, "backwards");
  var walkToEnd = partialRight(walkToBoundary, "forwards");

  // packages/format-library/build-module/link/css-classes-setting.mjs
  var import_element13 = __toESM(require_element(), 1);
  var import_compose2 = __toESM(require_compose(), 1);
  var import_i18n6 = __toESM(require_i18n(), 1);
  var import_components2 = __toESM(require_components(), 1);
  var import_jsx_runtime31 = __toESM(require_jsx_runtime(), 1);
  var CSSClassesSettingComponent = ({ setting, value, onChange }) => {
    const hasValue = value ? value?.cssClasses?.length > 0 : false;
    const [isSettingActive, setIsSettingActive] = (0, import_element13.useState)(hasValue);
    const instanceId = (0, import_compose2.useInstanceId)(CSSClassesSettingComponent);
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
    return /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)("fieldset", { children: [
      /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(VisuallyHidden, { render: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)("legend", {}), children: setting.title }),
      /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(Stack, { direction: "column", gap: "md", children: [
        /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
          import_components2.CheckboxControl,
          {
            label: setting.title,
            onChange: handleCheckboxChange,
            checked: isSettingActive || hasValue,
            "aria-expanded": isSettingActive,
            "aria-controls": isSettingActive ? controlledRegionId : void 0
          }
        ),
        isSettingActive && /* @__PURE__ */ (0, import_jsx_runtime31.jsx)("div", { id: controlledRegionId, children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
          import_components2.__experimentalInputControl,
          {
            label: (0, import_i18n6.__)("CSS classes"),
            value: value?.cssClasses,
            onChange: handleSettingChange,
            help: (0, import_i18n6.__)(
              "Separate multiple classes with spaces."
            ),
            __unstableInputWidth: "100%",
            __next40pxDefaultSize: true
          }
        ) })
      ] })
    ] });
  };
  var css_classes_setting_default = CSSClassesSettingComponent;

  // packages/format-library/build-module/link/inline.mjs
  var import_jsx_runtime32 = __toESM(require_jsx_runtime(), 1);
  var LINK_SETTINGS = [
    ...import_block_editor5.LinkControl.DEFAULT_LINK_SETTINGS,
    {
      id: "nofollow",
      title: (0, import_i18n7.__)("Mark as nofollow")
    },
    {
      id: "cssClasses",
      title: (0, import_i18n7.__)("Additional CSS class(es)"),
      render: (setting, value, onChange) => {
        return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(
          css_classes_setting_default,
          {
            setting,
            value,
            onChange
          }
        );
      }
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
    const linkValue = (0, import_element14.useMemo)(
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
      (0, import_a11y.speak)((0, import_i18n7.__)("Link removed."), "assertive");
    }
    function onChangeLink(nextValue) {
      const hasLink = linkValue?.url;
      const isNewLink = !hasLink;
      nextValue = {
        ...linkValue,
        ...nextValue
      };
      const newUrl = (0, import_url2.prependHTTPS)(nextValue.url);
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
        const [valBefore, valAfter] = (0, import_rich_text5.split)(
          value,
          boundary.start,
          boundary.start
        );
        const newValAfter = (0, import_rich_text5.replace)(valAfter, richTextText, newValue);
        newValue = (0, import_rich_text5.concat)(valBefore, newValAfter);
      }
      onChange(newValue);
      if (!isNewLink) {
        stopAddingLink();
      }
      if (!isValidHref(newUrl)) {
        (0, import_a11y.speak)(
          (0, import_i18n7.__)(
            "Warning: the link has been inserted but may have errors. Please test it."
          ),
          "assertive"
        );
      } else if (isActive) {
        (0, import_a11y.speak)((0, import_i18n7.__)("Link edited."), "assertive");
      } else {
        (0, import_a11y.speak)((0, import_i18n7.__)("Link inserted."), "assertive");
      }
    }
    const popoverAnchor = (0, import_rich_text5.useAnchor)({
      editableContentElement: contentRef.current,
      settings: {
        ...link,
        isActive
      }
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
      return (0, import_element14.createInterpolateElement)(
        (0, import_i18n7.sprintf)(
          /* translators: %s: search term. */
          (0, import_i18n7.__)("Create page: <mark>%s</mark>"),
          searchTerm
        ),
        { mark: /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("mark", {}) }
      );
    }
    return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(
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
        children: /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(
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
      textStart = boundary.start;
      textEnd = boundary.end;
    }
    return (0, import_rich_text5.slice)(value, textStart, textEnd);
  }
  var inline_default = InlineLinkUI;

  // packages/format-library/build-module/link/index.mjs
  var import_jsx_runtime33 = __toESM(require_jsx_runtime(), 1);
  var name5 = "core/link";
  var title5 = (0, import_i18n8.__)("Link");
  function Edit2({
    isActive,
    activeAttributes,
    value,
    onChange,
    onFocus,
    contentRef,
    isVisible = true
  }) {
    const [addingLink, setAddingLink] = (0, import_element15.useState)(false);
    const [openedBy, setOpenedBy] = (0, import_element15.useState)(null);
    (0, import_element15.useEffect)(() => {
      if (!isActive) {
        setAddingLink(false);
      }
    }, [isActive]);
    (0, import_element15.useLayoutEffect)(() => {
      const editableContentElement = contentRef.current;
      if (!editableContentElement) {
        return;
      }
      function handleClick(event) {
        const link2 = event.target.closest("[contenteditable] a");
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
      (0, import_a11y2.speak)((0, import_i18n8.__)("Link removed."), "assertive");
    }
    const shouldAutoFocus = !(openedBy?.el?.tagName === "A" && openedBy?.action === "click");
    const hasSelection = !(0, import_rich_text6.isCollapsed)(value);
    return /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(import_jsx_runtime33.Fragment, { children: [
      hasSelection && /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
        import_block_editor6.RichTextShortcut,
        {
          type: "primary",
          character: "k",
          onUse: addLink
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
        import_block_editor6.RichTextShortcut,
        {
          type: "primaryShift",
          character: "k",
          onUse: onRemoveFormat
        }
      ),
      isVisible && /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
        import_block_editor6.RichTextToolbarButton,
        {
          name: "link",
          icon: link_default,
          title: isActive ? (0, import_i18n8.__)("Link") : title5,
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
      isVisible && addingLink && /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
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
  var import_i18n9 = __toESM(require_i18n(), 1);
  var import_rich_text7 = __toESM(require_rich_text(), 1);
  var import_block_editor7 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime34 = __toESM(require_jsx_runtime(), 1);
  var name6 = "core/strikethrough";
  var title6 = (0, import_i18n9.__)("Strikethrough");
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
      return /* @__PURE__ */ (0, import_jsx_runtime34.jsxs)(import_jsx_runtime34.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(
          import_block_editor7.RichTextShortcut,
          {
            type: "access",
            character: "d",
            onUse: onClick
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(
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
  var import_i18n10 = __toESM(require_i18n(), 1);
  var import_rich_text8 = __toESM(require_rich_text(), 1);
  var import_block_editor8 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime35 = __toESM(require_jsx_runtime(), 1);
  var name7 = "core/underline";
  var title7 = (0, import_i18n10.__)("Underline");
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
      return /* @__PURE__ */ (0, import_jsx_runtime35.jsxs)(import_jsx_runtime35.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
          import_block_editor8.RichTextShortcut,
          {
            type: "primary",
            character: "u",
            onUse: onToggle
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
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
  var import_i18n12 = __toESM(require_i18n(), 1);
  var import_element17 = __toESM(require_element(), 1);
  var import_block_editor10 = __toESM(require_block_editor(), 1);
  var import_rich_text10 = __toESM(require_rich_text(), 1);

  // packages/format-library/build-module/text-color/inline.mjs
  var import_element16 = __toESM(require_element(), 1);
  var import_data2 = __toESM(require_data(), 1);
  var import_rich_text9 = __toESM(require_rich_text(), 1);
  var import_block_editor9 = __toESM(require_block_editor(), 1);
  var import_components4 = __toESM(require_components(), 1);
  var import_i18n11 = __toESM(require_i18n(), 1);
  var import_jsx_runtime36 = __toESM(require_jsx_runtime(), 1);
  var TABS = [
    { name: "color", title: (0, import_i18n11.__)("Text") },
    { name: "backgroundColor", title: (0, import_i18n11.__)("Background") }
  ];
  function parseCSS(css = "") {
    return css.split(";").reduce((accumulator, rule) => {
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
    }, {});
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
      ...parseCSS(activeColorFormat.attributes.style),
      ...parseClassName(activeColorFormat.attributes.class, colorSettings)
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
      if (colorObject) {
        classNames.push((0, import_block_editor9.getColorClassName)("color", colorObject.slug));
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
    const activeColors = (0, import_element16.useMemo)(
      () => getActiveColors(value, name16, colors),
      [name16, value, colors]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
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
    const popoverAnchor = (0, import_rich_text9.useAnchor)({
      editableContentElement: contentRef.current,
      settings: { ...textColor, isActive }
    });
    return /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
      import_components4.Popover,
      {
        onClose,
        className: "format-library__inline-color-popover",
        anchor: popoverAnchor,
        children: /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(tabs_exports.Root, { defaultValue: TABS[0].name, children: [
          /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(tabs_exports.List, { children: TABS.map((tab) => /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(tabs_exports.Tab, { value: tab.name, children: tab.title }, tab.name)) }),
          TABS.map((tab) => /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
            tabs_exports.Panel,
            {
              value: tab.name,
              tabIndex: -1,
              children: /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
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
  var import_jsx_runtime37 = __toESM(require_jsx_runtime(), 1);
  var transparentValue = "rgba(0, 0, 0, 0)";
  var name8 = "core/text-color";
  var title8 = (0, import_i18n12.__)("Highlight");
  var EMPTY_ARRAY3 = [];
  function getComputedStyleProperty(element, property) {
    const { ownerDocument: ownerDocument2 } = element;
    const { defaultView } = ownerDocument2;
    const style = defaultView.getComputedStyle(element);
    const value = style.getPropertyValue(property);
    if (property === "background-color" && value === transparentValue && element.parentElement) {
      return getComputedStyleProperty(element.parentElement, property);
    }
    return value;
  }
  function fillComputedColors(element, { color, backgroundColor }) {
    if (!color && !backgroundColor) {
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
    const [allowCustomControl, colors = EMPTY_ARRAY3] = (0, import_block_editor10.useSettings)(
      "color.custom",
      "color.palette"
    );
    const [isAddingColor, setIsAddingColor] = (0, import_element17.useState)(false);
    const colorIndicatorStyle = (0, import_element17.useMemo)(
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
    return /* @__PURE__ */ (0, import_jsx_runtime37.jsxs)(import_jsx_runtime37.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
        import_block_editor10.RichTextToolbarButton,
        {
          className: "format-library-text-color-button",
          isActive,
          icon: /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
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
      isAddingColor && /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
        InlineColorUI,
        {
          name: name8,
          onClose: () => setIsAddingColor(false),
          activeAttributes,
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
  var import_i18n13 = __toESM(require_i18n(), 1);
  var import_rich_text11 = __toESM(require_rich_text(), 1);
  var import_block_editor11 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime38 = __toESM(require_jsx_runtime(), 1);
  var name9 = "core/subscript";
  var title9 = (0, import_i18n13.__)("Subscript");
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
      return /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
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
  var import_i18n14 = __toESM(require_i18n(), 1);
  var import_rich_text12 = __toESM(require_rich_text(), 1);
  var import_block_editor12 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime39 = __toESM(require_jsx_runtime(), 1);
  var name10 = "core/superscript";
  var title10 = (0, import_i18n14.__)("Superscript");
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
      return /* @__PURE__ */ (0, import_jsx_runtime39.jsx)(
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
  var import_i18n15 = __toESM(require_i18n(), 1);
  var import_rich_text13 = __toESM(require_rich_text(), 1);
  var import_block_editor13 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime40 = __toESM(require_jsx_runtime(), 1);
  var name11 = "core/keyboard";
  var title11 = (0, import_i18n15.__)("Keyboard input");
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
      return /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(
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
  var import_i18n16 = __toESM(require_i18n(), 1);
  var import_rich_text14 = __toESM(require_rich_text(), 1);
  var import_block_editor14 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime41 = __toESM(require_jsx_runtime(), 1);
  var name12 = "core/unknown";
  var title12 = (0, import_i18n16.__)("Clear Unknown Formatting");
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
      return /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(
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
  var import_i18n17 = __toESM(require_i18n(), 1);
  var import_block_editor15 = __toESM(require_block_editor(), 1);
  var import_components5 = __toESM(require_components(), 1);
  var import_element18 = __toESM(require_element(), 1);
  var import_rich_text15 = __toESM(require_rich_text(), 1);
  var import_jsx_runtime42 = __toESM(require_jsx_runtime(), 1);
  var name13 = "core/language";
  var title13 = (0, import_i18n17.__)("Language");
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
    const [isPopoverVisible, setIsPopoverVisible] = (0, import_element18.useState)(false);
    const togglePopover = () => {
      setIsPopoverVisible((state) => !state);
    };
    return /* @__PURE__ */ (0, import_jsx_runtime42.jsxs)(import_jsx_runtime42.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(
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
      isPopoverVisible && /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(
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
  function InlineLanguageUI({ value, contentRef, onChange, onClose }) {
    const popoverAnchor = (0, import_rich_text15.useAnchor)({
      editableContentElement: contentRef.current,
      settings: language
    });
    const [lang, setLang] = (0, import_element18.useState)("");
    const [dir, setDir] = (0, import_element18.useState)("ltr");
    return /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(
      import_components5.Popover,
      {
        className: "block-editor-format-toolbar__language-popover",
        anchor: popoverAnchor,
        onClose,
        children: /* @__PURE__ */ (0, import_jsx_runtime42.jsxs)(
          Stack,
          {
            render: /* @__PURE__ */ (0, import_jsx_runtime42.jsx)("form", {}),
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
              /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(
                import_components5.TextControl,
                {
                  __next40pxDefaultSize: true,
                  label: title13,
                  value: lang,
                  onChange: (val) => setLang(val),
                  help: (0, import_i18n17.__)(
                    'A valid language attribute, like "en" or "fr".'
                  )
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(
                import_components5.SelectControl,
                {
                  __next40pxDefaultSize: true,
                  label: (0, import_i18n17.__)("Text direction"),
                  value: dir,
                  options: [
                    {
                      label: (0, import_i18n17.__)("Left to right"),
                      value: "ltr"
                    },
                    {
                      label: (0, import_i18n17.__)("Right to left"),
                      value: "rtl"
                    }
                  ],
                  onChange: (val) => setDir(val)
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(Stack, { justify: "right", children: /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(
                import_components5.Button,
                {
                  __next40pxDefaultSize: true,
                  variant: "primary",
                  type: "submit",
                  text: (0, import_i18n17.__)("Apply")
                }
              ) })
            ]
          }
        )
      }
    );
  }

  // packages/format-library/build-module/math/index.mjs
  var import_i18n18 = __toESM(require_i18n(), 1);
  var import_element19 = __toESM(require_element(), 1);
  var import_rich_text16 = __toESM(require_rich_text(), 1);
  var import_block_editor16 = __toESM(require_block_editor(), 1);
  var import_components6 = __toESM(require_components(), 1);
  var import_a11y3 = __toESM(require_a11y(), 1);

  // packages/format-library/build-module/lock-unlock.mjs
  var import_private_apis = __toESM(require_private_apis(), 1);
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/format-library"
  );

  // packages/format-library/build-module/math/index.mjs
  var import_jsx_runtime43 = __toESM(require_jsx_runtime(), 1);
  var { Badge: WCBadge } = unlock(import_components6.privateApis);
  var name14 = "core/math";
  var title14 = (0, import_i18n18.__)("Math");
  function InlineUI2({
    value,
    onChange,
    activeAttributes,
    contentRef,
    latexToMathML
  }) {
    const [latex, setLatex] = (0, import_element19.useState)(
      activeAttributes?.["data-latex"] || ""
    );
    const [error2, setError] = (0, import_element19.useState)(null);
    const popoverAnchor = (0, import_rich_text16.useAnchor)({
      editableContentElement: contentRef.current,
      settings: math
    });
    const handleLatexChange = (newLatex) => {
      let mathML = "";
      setLatex(newLatex);
      if (newLatex) {
        try {
          mathML = latexToMathML(newLatex, { displayMode: false });
          setError(null);
        } catch (err) {
          setError(err.message);
          (0, import_a11y3.speak)(
            (0, import_i18n18.sprintf)(
              /* translators: %s: error message returned when parsing LaTeX. */
              (0, import_i18n18.__)("Error parsing mathematical expression: %s"),
              err.message
            )
          );
          return;
        }
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
    return /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(
      import_components6.Popover,
      {
        placement: "bottom-start",
        offset: 8,
        focusOnMount: false,
        anchor: popoverAnchor,
        className: "block-editor-format-toolbar__math-popover",
        children: /* @__PURE__ */ (0, import_jsx_runtime43.jsx)("div", { style: { minWidth: "300px", padding: "4px" }, children: /* @__PURE__ */ (0, import_jsx_runtime43.jsxs)(Stack, { direction: "column", gap: "xs", children: [
          /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(
            import_components6.TextControl,
            {
              __next40pxDefaultSize: true,
              hideLabelFromVision: true,
              label: (0, import_i18n18.__)("LaTeX math syntax"),
              value: latex,
              onChange: handleLatexChange,
              placeholder: (0, import_i18n18.__)("e.g., x^2, \\frac{a}{b}"),
              autoComplete: "off",
              className: "block-editor-format-toolbar__math-input"
            }
          ),
          error2 && /* @__PURE__ */ (0, import_jsx_runtime43.jsxs)(import_jsx_runtime43.Fragment, { children: [
            /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(
              WCBadge,
              {
                intent: "error",
                className: "wp-block-math__error",
                children: (0, import_i18n18.sprintf)(
                  /* translators: %s: error message returned when parsing LaTeX. */
                  (0, import_i18n18.__)("Error: %s"),
                  error2
                )
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime43.jsx)("style", { children: ".wp-block-math__error .components-badge__content{white-space:normal}" })
          ] })
        ] }) })
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
    const [latexToMathML, setLatexToMathML] = (0, import_element19.useState)();
    (0, import_element19.useEffect)(() => {
      import("@wordpress/latex-to-mathml").then((module) => {
        setLatexToMathML(() => module.default);
      });
    }, []);
    return /* @__PURE__ */ (0, import_jsx_runtime43.jsxs)(import_jsx_runtime43.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(
        import_block_editor16.RichTextToolbarButton,
        {
          icon: math_default,
          title: title14,
          onClick: () => {
            const newValue = (0, import_rich_text16.insertObject)(value, {
              type: name14,
              attributes: {
                "data-latex": ""
              },
              innerHTML: ""
            });
            newValue.start = newValue.end - 1;
            onChange(newValue);
            onFocus();
          },
          isActive: isObjectActive
        }
      ),
      isObjectActive && /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(
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
  var import_i18n19 = __toESM(require_i18n(), 1);
  var import_rich_text17 = __toESM(require_rich_text(), 1);
  var import_block_editor17 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime44 = __toESM(require_jsx_runtime(), 1);
  var name15 = "core/non-breaking-space";
  var title15 = (0, import_i18n19.__)("Non breaking space");
  var nonBreakingSpace = {
    name: name15,
    title: title15,
    tagName: "nbsp",
    className: null,
    edit({ value, onChange }) {
      function addNonBreakingSpace() {
        onChange((0, import_rich_text17.insert)(value, "\xA0"));
      }
      return /* @__PURE__ */ (0, import_jsx_runtime44.jsx)(
        import_block_editor17.RichTextShortcut,
        {
          type: "primaryShift",
          character: " ",
          onUse: addNonBreakingSpace
        }
      );
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
