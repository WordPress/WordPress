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

  // node_modules/.store/use-sync-external-store@1.7.0-jHNF5n8408rkWhZ5NHKclA/node_modules/use-sync-external-store/cjs/use-sync-external-store-shim.development.js
  var require_use_sync_external_store_shim_development = __commonJS({
    "node_modules/.store/use-sync-external-store@1.7.0-jHNF5n8408rkWhZ5NHKclA/node_modules/use-sync-external-store/cjs/use-sync-external-store-shim.development.js"(exports) {
      "use strict";
      (function() {
        function is(x, y) {
          return x === y && (0 !== x || 1 / x === 1 / y) || x !== x && y !== y;
        }
        function useSyncExternalStore$2(subscribe, getSnapshot) {
          didWarnOld18Alpha || void 0 === React101.startTransition || (didWarnOld18Alpha = true, console.error(
            "You are using an outdated, pre-release alpha of React 18 that does not support useSyncExternalStore. The use-sync-external-store shim will not work correctly. Upgrade to a newer pre-release."
          ));
          var value = getSnapshot();
          if (!didWarnUncachedGetSnapshot) {
            var cachedValue = getSnapshot();
            objectIs(value, cachedValue) || (console.error(
              "The result of getSnapshot should be cached to avoid an infinite loop"
            ), didWarnUncachedGetSnapshot = true);
          }
          cachedValue = useState32({
            inst: { value, getSnapshot }
          });
          var inst = cachedValue[0].inst, forceUpdate = cachedValue[1];
          useLayoutEffect4(
            function() {
              inst.value = value;
              inst.getSnapshot = getSnapshot;
              checkIfSnapshotChanged(inst) && forceUpdate({ inst });
            },
            [subscribe, value, getSnapshot]
          );
          useEffect21(
            function() {
              checkIfSnapshotChanged(inst) && forceUpdate({ inst });
              return subscribe(function() {
                checkIfSnapshotChanged(inst) && forceUpdate({ inst });
              });
            },
            [subscribe]
          );
          useDebugValue2(value);
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
        function useSyncExternalStore$1(subscribe, getSnapshot) {
          return getSnapshot();
        }
        "undefined" !== typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ && "function" === typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());
        var React101 = require_react(), objectIs = "function" === typeof Object.is ? Object.is : is, useState32 = React101.useState, useEffect21 = React101.useEffect, useLayoutEffect4 = React101.useLayoutEffect, useDebugValue2 = React101.useDebugValue, didWarnOld18Alpha = false, didWarnUncachedGetSnapshot = false, shim = "undefined" === typeof window || "undefined" === typeof window.document || "undefined" === typeof window.document.createElement ? useSyncExternalStore$1 : useSyncExternalStore$2;
        exports.useSyncExternalStore = void 0 !== React101.useSyncExternalStore ? React101.useSyncExternalStore : shim;
        "undefined" !== typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ && "function" === typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error());
      })();
    }
  });

  // node_modules/.store/use-sync-external-store@1.7.0-jHNF5n8408rkWhZ5NHKclA/node_modules/use-sync-external-store/shim/index.js
  var require_shim = __commonJS({
    "node_modules/.store/use-sync-external-store@1.7.0-jHNF5n8408rkWhZ5NHKclA/node_modules/use-sync-external-store/shim/index.js"(exports, module) {
      "use strict";
      if (false) {
        module.exports = null;
      } else {
        module.exports = require_use_sync_external_store_shim_development();
      }
    }
  });

  // node_modules/.store/use-sync-external-store@1.7.0-jHNF5n8408rkWhZ5NHKclA/node_modules/use-sync-external-store/cjs/use-sync-external-store-shim/with-selector.development.js
  var require_with_selector_development = __commonJS({
    "node_modules/.store/use-sync-external-store@1.7.0-jHNF5n8408rkWhZ5NHKclA/node_modules/use-sync-external-store/cjs/use-sync-external-store-shim/with-selector.development.js"(exports) {
      "use strict";
      (function() {
        function is(x, y) {
          return x === y && (0 !== x || 1 / x === 1 / y) || x !== x && y !== y;
        }
        "undefined" !== typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ && "function" === typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());
        var React101 = require_react(), shim = require_shim(), objectIs = "function" === typeof Object.is ? Object.is : is, useSyncExternalStore3 = shim.useSyncExternalStore, useRef46 = React101.useRef, useEffect21 = React101.useEffect, useMemo31 = React101.useMemo, useDebugValue2 = React101.useDebugValue;
        exports.useSyncExternalStoreWithSelector = function(subscribe, getSnapshot, getServerSnapshot, selector, isEqual) {
          var instRef = useRef46(null);
          if (null === instRef.current) {
            var inst = { hasValue: false, value: null };
            instRef.current = inst;
          } else inst = instRef.current;
          instRef = useMemo31(
            function() {
              function memoizedSelector(nextSnapshot) {
                if (!hasMemo) {
                  hasMemo = true;
                  memoizedSnapshot = nextSnapshot;
                  nextSnapshot = selector(nextSnapshot);
                  if (void 0 !== isEqual && inst.hasValue) {
                    var currentSelection = inst.value;
                    if (isEqual(currentSelection, nextSnapshot))
                      return memoizedSelection = currentSelection;
                  }
                  return memoizedSelection = nextSnapshot;
                }
                currentSelection = memoizedSelection;
                if (objectIs(memoizedSnapshot, nextSnapshot))
                  return currentSelection;
                var nextSelection = selector(nextSnapshot);
                if (void 0 !== isEqual && isEqual(currentSelection, nextSelection))
                  return memoizedSnapshot = nextSnapshot, currentSelection;
                memoizedSnapshot = nextSnapshot;
                return memoizedSelection = nextSelection;
              }
              var hasMemo = false, memoizedSnapshot, memoizedSelection, maybeGetServerSnapshot = void 0 === getServerSnapshot ? null : getServerSnapshot;
              return [
                function() {
                  return memoizedSelector(getSnapshot());
                },
                null === maybeGetServerSnapshot ? void 0 : function() {
                  return memoizedSelector(maybeGetServerSnapshot());
                }
              ];
            },
            [getSnapshot, getServerSnapshot, selector, isEqual]
          );
          var value = useSyncExternalStore3(subscribe, instRef[0], instRef[1]);
          useEffect21(
            function() {
              inst.hasValue = true;
              inst.value = value;
            },
            [value]
          );
          useDebugValue2(value);
          return value;
        };
        "undefined" !== typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ && "function" === typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error());
      })();
    }
  });

  // node_modules/.store/use-sync-external-store@1.7.0-jHNF5n8408rkWhZ5NHKclA/node_modules/use-sync-external-store/shim/with-selector.js
  var require_with_selector = __commonJS({
    "node_modules/.store/use-sync-external-store@1.7.0-jHNF5n8408rkWhZ5NHKclA/node_modules/use-sync-external-store/shim/with-selector.js"(exports, module) {
      "use strict";
      if (false) {
        module.exports = null;
      } else {
        module.exports = require_with_selector_development();
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
    ({ icon: icon2, size: size4 = 24, style, ...props }, ref) => {
      const intrinsicStyle = icon2.props.style;
      const mergedStyle = intrinsicStyle || style ? { ...intrinsicStyle, ...style } : void 0;
      return (0, import_element.cloneElement)(icon2, {
        width: size4,
        height: size4,
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
  var button_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M8 11.75H16M5 16.25H19C19.6904 16.25 20.25 15.6904 20.25 15V8.5C20.25 7.80964 19.6904 7.25 19 7.25H5C4.30964 7.25 3.75 7.80964 3.75 8.5V15C3.75 15.6904 4.30964 16.25 5 16.25Z" }) });

  // packages/icons/build-module/library/check.mjs
  var import_primitives2 = __toESM(require_primitives(), 1);
  var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
  var check_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M7 12L10 15L17 8" }) });

  // packages/icons/build-module/library/chevron-down.mjs
  var import_primitives3 = __toESM(require_primitives(), 1);
  var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
  var chevron_down_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M7 11L12 15L17 11" }) });

  // packages/icons/build-module/library/code.mjs
  var import_primitives4 = __toESM(require_primitives(), 1);
  var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
  var code_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.Path, { d: "M7.99997 7L3.70708 11.2929C3.31655 11.6834 3.31655 12.3166 3.70708 12.7071L7.99997 17M16 17L20.2929 12.7071C20.6834 12.3166 20.6834 11.6834 20.2929 11.2929L16 7", strokeLinejoin: "round" }) });

  // packages/icons/build-module/library/color.mjs
  var import_primitives5 = __toESM(require_primitives(), 1);
  var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
  var color_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.Path, { d: "M17.25 14C17.25 16.8995 14.8995 19.25 12 19.25C9.10051 19.25 6.75 16.8995 6.75 14C6.75 11.1005 10 7.37 12 5.12C14 7.37 17.25 11.1005 17.25 14Z" }) });

  // packages/icons/build-module/library/error.mjs
  var import_primitives6 = __toESM(require_primitives(), 1);
  var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
  var error_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.Path, { d: "M12 9L12 14M12 15.5V17M11.1284 5.04947L3.83827 18.0097C3.46331 18.6763 3.94502 19.5 4.70985 19.5H19.2902C20.055 19.5 20.5367 18.6763 20.1617 18.0097L12.8716 5.04947C12.4893 4.36982 11.5107 4.36982 11.1284 5.04947Z" }) });

  // packages/icons/build-module/library/format-bold.mjs
  var import_primitives7 = __toESM(require_primitives(), 1);
  var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
  var format_bold_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsxs)(import_primitives7.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.Path, { d: "M7.75 11.5V18H13.25C15.0449 18 16.5 16.5449 16.5 14.75C16.5 12.9551 15.0449 11.5 13.25 11.5H13M7.75 11.5V6H12.7441C14.2661 6 15.5 7.23387 15.5 8.75592C15.5 10.1718 14.4088 11.3591 13 11.5M7.75 11.5H13" }),
    /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.Path, { d: "M8.25 11.75V17.5H12.8642C14.5503 17.5 15.8744 16.0557 15.7283 14.3759C15.5992 12.8903 14.3554 11.75 12.8642 11.75H8.25ZM8.25 11.75V11.25M8.25 11.25V6.5H12.625C13.9367 6.5 15 7.56332 15 8.875C15 10.1867 13.9367 11.25 12.625 11.25H8.25Z" })
  ] });

  // packages/icons/build-module/library/format-italic.mjs
  var import_primitives8 = __toESM(require_primitives(), 1);
  var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
  var format_italic_default = /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.Path, { d: "M11 19L13.5 5" }) });

  // packages/icons/build-module/library/format-strikethrough.mjs
  var import_primitives9 = __toESM(require_primitives(), 1);
  var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
  var format_strikethrough_default = /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives9.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives9.Path, { d: "M9 16.1554C12 18.02 15 16.5283 15 14.2908C15 13.6314 14.6839 12.999 14.1213 12.5328C14.108 12.5217 14.0945 12.5108 14.081 12.5M9 9.31845C9 7.49773 11.4313 6.3247 13.9199 7.4278M5.5 12.5H14.081M18.5 12.5H14.081", strokeLinecap: "square" }) });

  // packages/icons/build-module/library/help.mjs
  var import_primitives10 = __toESM(require_primitives(), 1);
  var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
  var help_default = /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)(import_primitives10.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives10.Path, { d: "M9.75 10.25C9.75 9.00736 10.7574 8 12 8C13.2426 8 14.25 9.00736 14.25 10.25C14.25 11.4083 13.3748 12.3621 12.2496 12.4863C12.1124 12.5015 12 12.6119 12 12.75V14M12 15V16.5" }),
    /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives10.Path, { d: "M19.25 12C19.25 16.0041 16.0041 19.25 12 19.25C7.99594 19.25 4.75 16.0041 4.75 12C4.75 7.99594 7.99594 4.75 12 4.75C16.0041 4.75 19.25 7.99594 19.25 12Z", strokeLinejoin: "round" })
  ] });

  // packages/icons/build-module/library/inline-image.mjs
  var import_primitives11 = __toESM(require_primitives(), 1);
  var import_jsx_runtime11 = __toESM(require_jsx_runtime(), 1);
  var inline_image_default = /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives11.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives11.Path, { d: "M13.25 11.3197L11.4155 9.48453C11.3765 9.44547 11.3132 9.44546 11.2741 9.48452L9.05837 11.7003C9.02448 11.7341 8.97132 11.7392 8.93163 11.7124L7.0648 10.4495C7.03003 10.426 6.98426 10.4267 6.95022 10.4513L4.75 12.0405M13.25 11.3197V7C13.25 6.30964 12.6904 5.75 12 5.75H6C5.30964 5.75 4.75 6.30965 4.75 7V12.0405M13.25 11.3197V13C13.25 13.6904 12.6904 14.25 12 14.25H6C5.30964 14.25 4.75 13.6904 4.75 13V12.0405M16 14H20M4 18H20" }) });

  // packages/icons/build-module/library/language.mjs
  var import_primitives12 = __toESM(require_primitives(), 1);
  var import_jsx_runtime12 = __toESM(require_jsx_runtime(), 1);
  var language_default = /* @__PURE__ */ (0, import_jsx_runtime12.jsxs)(import_primitives12.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives12.Path, { d: "M3 6H9.5M16 6H13.5M9.5 6V3M9.5 6H13.5M13.5 6C13.0477 7.24386 11.7361 9.79194 9.44037 12.5512M5.5 16.5C7.04336 15.2139 8.35238 13.8588 9.44037 12.5512M6.5 8C6.9232 8.9522 7.93743 10.7507 9.44037 12.5512M12.5 15.5C11.3314 14.6235 10.305 13.587 9.44037 12.5512" }),
    /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives12.Path, { d: "M13.75 20V19.75L14.6667 17M14.6667 17L16.75 10.75H17.25L19.3333 17L20.25 19.75V20M19.3333 17H14.6667" })
  ] });

  // packages/icons/build-module/library/link.mjs
  var import_primitives13 = __toESM(require_primitives(), 1);
  var import_jsx_runtime13 = __toESM(require_jsx_runtime(), 1);
  var link_default = /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives13.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives13.Path, { d: "M9.5 12L14.5 12M10 7.75H8.25C5.90279 7.75 4 9.65279 4 12C4 14.3472 5.90279 16.25 8.25 16.25H10M14 16.25H15.75C18.0972 16.25 20 14.3472 20 12C20 9.65279 18.0972 7.75 15.75 7.75L14 7.75" }) });

  // packages/icons/build-module/library/math.mjs
  var import_primitives14 = __toESM(require_primitives(), 1);
  var import_jsx_runtime14 = __toESM(require_jsx_runtime(), 1);
  var math_default = /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(import_primitives14.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(import_primitives14.Path, { d: "M3 14H5L6.8916 16.3645C6.93974 16.4247 7.03496 16.4101 7.06289 16.3383L10.2521 8.13755C10.4015 7.75318 10.7717 7.5 11.1841 7.5H19M17 11L14.5 13.5M12 16L14.5 13.5M14.5 13.5L17 16M14.5 13.5L12 11" }) });

  // packages/icons/build-module/library/published.mjs
  var import_primitives15 = __toESM(require_primitives(), 1);
  var import_jsx_runtime15 = __toESM(require_jsx_runtime(), 1);
  var published_default = /* @__PURE__ */ (0, import_jsx_runtime15.jsxs)(import_primitives15.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_primitives15.Path, { d: "M12 19.25C16.0041 19.25 19.25 16.0041 19.25 12C19.25 7.99594 16.0041 4.75 12 4.75C7.99594 4.75 4.75 7.99594 4.75 12C4.75 16.0041 7.99594 19.25 12 19.25Z", strokeLinejoin: "round" }),
    /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_primitives15.Path, { d: "M9 12L11 14L15 10" })
  ] });

  // packages/icons/build-module/library/subscript.mjs
  var import_primitives16 = __toESM(require_primitives(), 1);
  var import_jsx_runtime16 = __toESM(require_jsx_runtime(), 1);
  var subscript_default = /* @__PURE__ */ (0, import_jsx_runtime16.jsxs)(import_primitives16.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_primitives16.Path, { d: "M17.75 18.5988H15.75C15.75 16.5322 17.75 17.0489 17.75 15.4989C17.75 14.7239 16.75 14.0738 15.75 14.8488", strokeLinecap: "square" }),
    /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_primitives16.Path, { d: "M10 12L13 7.4V7M10 12L7 7.4L7.00004 7M10 12L7 16.6V17M10 12L13 16.6V17" })
  ] });

  // packages/icons/build-module/library/superscript.mjs
  var import_primitives17 = __toESM(require_primitives(), 1);
  var import_jsx_runtime17 = __toESM(require_jsx_runtime(), 1);
  var superscript_default = /* @__PURE__ */ (0, import_jsx_runtime17.jsxs)(import_primitives17.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_primitives17.Path, { d: "M17.75 8.59884H15.75C15.75 6.53219 17.75 7.04885 17.75 5.49887C17.75 4.72388 16.75 4.07384 15.75 4.84884", strokeLinecap: "square" }),
    /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_primitives17.Path, { d: "M10 12L13 7.4V7M10 12L7 7.4L7.00004 7M10 12L7 16.6V17M10 12L13 16.6V17" })
  ] });

  // packages/icons/build-module/library/text-color.mjs
  var import_primitives18 = __toESM(require_primitives(), 1);
  var import_jsx_runtime18 = __toESM(require_jsx_runtime(), 1);
  var text_color_default = /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(import_primitives18.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(import_primitives18.Path, { d: "M8 17.5V17.223L9.01577 14.1757M9.01577 14.1757L11.3243 7.25H11.8784L14.1869 14.1757L15.2027 17.223V17.5M14.1869 14.1757H9.01577" }) });

  // packages/format-library/build-module/bold/index.mjs
  var import_jsx_runtime19 = __toESM(require_jsx_runtime(), 1);
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
      return /* @__PURE__ */ (0, import_jsx_runtime19.jsxs)(import_jsx_runtime19.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
          import_block_editor.RichTextShortcut,
          {
            type: "primary",
            character: "b",
            onUse: onToggle
          }
        ),
        isVisible && /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
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
        /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
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
  var import_jsx_runtime20 = __toESM(require_jsx_runtime(), 1);
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
      if (start === void 0) {
        return value;
      }
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
      return /* @__PURE__ */ (0, import_jsx_runtime20.jsxs)(import_jsx_runtime20.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
          import_block_editor2.RichTextShortcut,
          {
            type: "access",
            character: "x",
            onUse: onClick
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useRenderElement.mjs
  var React4 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useRefWithInit.mjs
  var React = __toESM(require_react(), 1);
  var UNINITIALIZED = {};
  function useRefWithInit(init, initArg) {
    const ref = React.useRef(UNINITIALIZED);
    if (ref.current === UNINITIALIZED) {
      ref.current = init(initArg);
    }
    return ref;
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useMergedRefs.mjs
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
    return forkRef.refs.length !== newRefs.length || forkRef.refs.some((ref, index3) => ref !== newRefs[index3]);
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

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/getReactElementRef.mjs
  var React3 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/reactVersion.mjs
  var React2 = __toESM(require_react(), 1);
  var majorVersion = parseInt(React2.version, 10);
  function isReactVersionAtLeast(reactVersionToCheck) {
    return majorVersion >= reactVersionToCheck;
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/getReactElementRef.mjs
  function getReactElementRef(element) {
    if (!/* @__PURE__ */ React3.isValidElement(element)) {
      return null;
    }
    const reactElement = element;
    const propsWithRef = reactElement.props;
    return (isReactVersionAtLeast(19) ? propsWithRef?.ref : reactElement.ref) ?? null;
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/mergeObjects.mjs
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

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/createLogOnce.mjs
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

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/warn.mjs
  var warn = createLogOnce("warn", "Base UI");

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/empty.mjs
  function NOOP() {
  }
  var EMPTY_ARRAY = Object.freeze([]);
  var EMPTY_OBJECT = Object.freeze({});

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/getStateAttributesProps.mjs
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/resolveClassName.mjs
  function resolveClassName(className, state) {
    return typeof className === "function" ? className(state) : className;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/resolveStyle.mjs
  function resolveStyle(style, state) {
    return typeof style === "function" ? style(state) : style;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/merge-props/mergeProps.mjs
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
        ...inputProps(EMPTY_PROPS)
      };
    }
    return copyInitialProps(inputProps);
  }
  function mergeInto(merged, inputProps) {
    if (isPropsGetter(inputProps)) {
      return inputProps(merged);
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useRenderElement.mjs
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
      stateAttributesMapping: stateAttributesMapping8,
      enabled = true
    } = params;
    const className = enabled ? resolveClassName(classNameProp, state) : void 0;
    const style = enabled ? resolveStyle(styleProp, state) : void 0;
    const stateProps = enabled ? getStateAttributesProps(state, stateAttributesMapping8) : EMPTY_OBJECT;
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
    const unwrapped = React4.Children.toArray(render)[0];
    return /* @__PURE__ */ React4.isValidElement(unwrapped) ? unwrapped : render;
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
        if (!/* @__PURE__ */ React4.isValidElement(render)) {
          throw new Error(["Base UI: The `render` prop was provided an invalid React element as `React.isValidElement(render)` is `false`.", "A valid React element must be provided to the `render` prop because it is cloned with props to replace the default element.", "https://base-ui.com/r/invalid-render-prop"].join("\n"));
        }
      }
      return /* @__PURE__ */ React4.cloneElement(render, mergedProps);
    }
    return renderTag(element, props);
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
    return /* @__PURE__ */ React4.createElement(Tag, props);
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/safeReact.mjs
  var React5 = __toESM(require_react(), 1);
  var SafeReact = {
    ...React5
  };

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useStableCallback.mjs
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

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useIsoLayoutEffect.mjs
  var React6 = __toESM(require_react(), 1);
  var noop = () => {
  };
  var useIsoLayoutEffect = typeof document !== "undefined" ? React6.useLayoutEffect : noop;

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useOpenChangeComplete.mjs
  var React8 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useAnimationsFinished.mjs
  var ReactDOM = __toESM(require_react_dom(), 1);

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useOnMount.mjs
  var React7 = __toESM(require_react(), 1);
  function useOnMount(fn) {
    React7.useEffect(fn, EMPTY_ARRAY);
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useAnimationFrame.mjs
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
      const index3 = id - this.startId;
      if (index3 < 0 || index3 >= this.callbacks.length) {
        return;
      }
      if (this.callbacks[index3] === null) {
        return;
      }
      this.callbacks[index3] = null;
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/resolveRef.mjs
  function resolveRef(maybeRef) {
    if (maybeRef == null) {
      return maybeRef;
    }
    return "current" in maybeRef ? maybeRef.current : maybeRef;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/getFiniteAnimations.mjs
  function getFiniteAnimations(element, options) {
    return element.getAnimations(options).filter((animation) => {
      const timing = animation.effect?.getTiming();
      return timing?.duration !== Infinity && timing?.iterations !== Infinity;
    });
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/TransitionStatusDataAttributes.mjs
  var TransitionStatusDataAttributes_exports = {};
  __export(TransitionStatusDataAttributes_exports, {
    endingStyle: () => endingStyle,
    startingStyle: () => startingStyle
  });
  var startingStyle = "data-starting-style";
  var endingStyle = "data-ending-style";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useAnimationsFinished.mjs
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
        Promise.all(getFiniteAnimations(resolvedElement).map((animation) => animation.finished.then(NOOP))).then(() => {
          if (!signal?.aborted) {
            done();
          }
        }, () => {
          if (signal?.aborted) {
            return;
          }
          const currentAnimations = getFiniteAnimations(resolvedElement);
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useOpenChangeComplete.mjs
  function useOpenChangeComplete(parameters) {
    const {
      enabled = true,
      open: open2,
      ref,
      batch = false,
      onComplete: onCompleteParam
    } = parameters;
    const onComplete = useStableCallback(onCompleteParam);
    const runOnceAnimationsFinish = useAnimationsFinished(ref, open2, batch);
    React8.useEffect(() => {
      if (!enabled) {
        return void 0;
      }
      const abortController = new AbortController();
      runOnceAnimationsFinish(onComplete, abortController.signal);
      return () => {
        abortController.abort();
      };
    }, [enabled, open2, onComplete, runOnceAnimationsFinish]);
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/stateAttributesMapping.mjs
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useTransitionStatus.mjs
  var React9 = __toESM(require_react(), 1);
  function useTransitionStatus(open2, enableIdleState = false, deferEndingState = false, animateInitialOpen = false) {
    const [transitionStatus, setTransitionStatus] = React9.useState(open2 && enableIdleState ? "idle" : void 0);
    const [mounted, setMounted] = React9.useState(open2 && !animateInitialOpen);
    if (open2 && !mounted) {
      setMounted(true);
      setTransitionStatus("starting");
    }
    if (!open2 && mounted && transitionStatus !== "ending" && !deferEndingState) {
      setTransitionStatus("ending");
    }
    if (!open2 && !mounted && transitionStatus === "ending") {
      setTransitionStatus(void 0);
    }
    useIsoLayoutEffect(() => {
      if (!open2 && mounted && transitionStatus !== "ending" && deferEndingState) {
        const frame = AnimationFrame.request(() => {
          setTransitionStatus("ending");
        });
        return () => {
          AnimationFrame.cancel(frame);
        };
      }
      return void 0;
    }, [open2, mounted, transitionStatus, deferEndingState]);
    useIsoLayoutEffect(() => {
      if (!open2 || enableIdleState || transitionStatus === void 0) {
        return void 0;
      }
      const frame = AnimationFrame.request(() => {
        setTransitionStatus(void 0);
      });
      return () => {
        AnimationFrame.cancel(frame);
      };
    }, [enableIdleState, open2, transitionStatus]);
    useIsoLayoutEffect(() => {
      if (!open2 || !enableIdleState) {
        return void 0;
      }
      if (open2 && mounted && transitionStatus !== "idle") {
        setTransitionStatus("starting");
      }
      const frame = AnimationFrame.request(() => {
        setTransitionStatus("idle");
      });
      return () => {
        AnimationFrame.cancel(frame);
      };
    }, [enableIdleState, open2, mounted, transitionStatus]);
    return {
      mounted,
      setMounted,
      transitionStatus
    };
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useTimeout.mjs
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

  // node_modules/.store/clsx@2.1.1-XOLP1vUL9vEGfpiXm37fAw/node_modules/clsx/dist/clsx.mjs
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

  // packages/ui/build-module/icon/icon.mjs
  var import_element2 = __toESM(require_element(), 1);
  var import_primitives19 = __toESM(require_primitives(), 1);
  var import_jsx_runtime21 = __toESM(require_jsx_runtime(), 1);
  var Icon = (0, import_element2.forwardRef)(
    function UnforwardedIcon({ icon: icon2, size: size4 = 24, style, ...restProps }, ref) {
      const mergedStyle = icon2.props.style || style ? { ...icon2.props.style, ...style } : void 0;
      return /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
        import_primitives19.SVG,
        {
          ref,
          ...icon2.props,
          ...restProps,
          ...mergedStyle ? { style: mergedStyle } : {},
          width: size4,
          height: size4
        }
      );
    }
  );

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useControlled.mjs
  var React10 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/error.mjs
  var error = createLogOnce("error", "Base UI");

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useControlled.mjs
  function useControlled({
    controlled,
    default: defaultProp,
    name: name16,
    state = "value"
  }) {
    const {
      current: isControlled
    } = React10.useRef(controlled !== void 0);
    const [valueState, setValue] = React10.useState(() => defaultProp);
    const value = isControlled && controlled !== void 0 ? controlled : valueState;
    if (true) {
      React10.useEffect(() => {
        if (isControlled !== (controlled !== void 0)) {
          error([`A component is changing the ${isControlled ? "" : "un"}controlled ${state} state of ${name16} to be ${isControlled ? "un" : ""}controlled.`, "Elements should not switch from uncontrolled to controlled (or vice versa).", `Decide between using a controlled or uncontrolled ${name16} element for the lifetime of the component.`, "The nature of the state is determined during the first render. It's considered controlled if the value is not `undefined`.", "More info: https://fb.me/react-controlled-components"].join("\n"));
        }
      }, [state, name16, controlled]);
      const {
        current: defaultValue
      } = React10.useRef(defaultProp);
      React10.useEffect(() => {
        if (!isControlled && serializeToDevModeString(defaultValue) !== serializeToDevModeString(defaultProp)) {
          error([`A component is changing the default ${state} state of an uncontrolled ${name16} after being initialized. To suppress this warning opt to use a controlled ${name16}.`].join("\n"));
        }
      }, [defaultProp]);
    }
    const setValueIfUncontrolled = React10.useCallback((newValue) => {
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/composite/list/CompositeList.mjs
  var React12 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/composite/list/CompositeListContext.mjs
  var React11 = __toESM(require_react(), 1);
  var CompositeListContext = /* @__PURE__ */ React11.createContext({
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
    return React11.useContext(CompositeListContext);
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/composite/list/CompositeList.mjs
  var import_jsx_runtime22 = __toESM(require_jsx_runtime(), 1);
  function CompositeList(props) {
    const {
      children,
      elementsRef,
      labelsRef,
      onMapChange: onMapChangeProp
    } = props;
    const onMapChange = useStableCallback(onMapChangeProp);
    const [, setMapTick] = React12.useState(0);
    const listeners = useRefWithInit(createListeners).current;
    const map = useRefWithInit(createMap).current;
    const nextIndexRef = React12.useRef(0);
    const isDirtyRef = React12.useRef(true);
    const itemsRef = React12.useRef(null);
    const mutationObserverRef = React12.useRef(null);
    const scheduleMapUpdate = useStableCallback(() => {
      if (isDirtyRef.current) {
        return;
      }
      isDirtyRef.current = true;
      setMapTick((tick) => tick + 1);
    });
    const register2 = useStableCallback((node, registration) => {
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
      const changed = !previousItems || previousItems.length !== items.length || items.some((item, index3) => {
        const previousItem = previousItems[index3];
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
    const contextValue = React12.useMemo(() => ({
      register: register2,
      unregister,
      subscribeMapChange,
      nextIndexRef
    }), [register2, unregister, subscribeMapChange, nextIndexRef]);
    return /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(CompositeListContext.Provider, {
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
      const index3 = registration.index;
      const item = {
        index: index3 ?? -1,
        element: node,
        registration
      };
      if (index3 === null) {
        automaticItems.push(item);
      } else if (index3 >= 0) {
        reservedIndices.add(index3);
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
    if (a.nextElementSibling === b) {
      return -1;
    }
    if (b.nextElementSibling === a) {
      return 1;
    }
    return a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1;
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useId.mjs
  var React14 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useForcedRerendering.mjs
  var React13 = __toESM(require_react(), 1);
  function useForcedRerendering() {
    const [, setState] = React13.useState({});
    return React13.useCallback(() => {
      setState({});
    }, []);
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useId.mjs
  var globalId = 0;
  function useGlobalId(idOverride, prefix = "mui") {
    const defaultId = React14.useRef(void 0);
    const forceRerender = useForcedRerendering();
    const id = idOverride ?? defaultId.current;
    React14.useEffect(() => {
      if (defaultId.current == null) {
        globalId += 1;
        defaultId.current = `${prefix}-${globalId}`;
        if (idOverride == null) {
          forceRerender();
        }
      }
    }, [idOverride, prefix, forceRerender]);
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useBaseUiId.mjs
  function useBaseUiId(idOverride) {
    return useId(idOverride, "base-ui");
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/createBaseUIEventDetails.mjs
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/reason-parts.mjs
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
    popupClose: () => popupClose,
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
  var popupClose = "popup-close";
  var cancelOpen = "cancel-open";
  var siblingOpen = "sibling-open";
  var disabled = "disabled";
  var missing = "missing";
  var initial = "initial";
  var imperativeAction = "imperative-action";
  var swipe = "swipe";
  var windowResize = "window-resize";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/composite/list/useCompositeListItem.mjs
  var React15 = __toESM(require_react(), 1);
  function useCompositeListItem(params = {}) {
    const {
      guess,
      label,
      metadata,
      textRef,
      index: externalIndex
    } = params;
    const {
      register: register2,
      unregister,
      subscribeMapChange,
      nextIndexRef
    } = useCompositeListContext();
    const indexRef = React15.useRef(-1);
    const [internalIndex, setInternalIndex] = React15.useState(externalIndex == null && guess ? () => {
      if (indexRef.current === -1) {
        const newIndex = nextIndexRef.current;
        nextIndexRef.current += 1;
        indexRef.current = newIndex;
      }
      return indexRef.current;
    } : -1);
    const index3 = externalIndex ?? internalIndex;
    const componentRef = React15.useRef(null);
    const ref = React15.useCallback((node) => {
      const previousNode = componentRef.current;
      if (previousNode) {
        unregister(previousNode);
      }
      componentRef.current = node;
      if (node) {
        register2(node, {
          metadata: metadata ?? null,
          index: externalIndex ?? null,
          label,
          textRef
        });
      }
    }, [externalIndex, register2, unregister, metadata, label, textRef]);
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
      index: index3
    };
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/use-button/useButton.mjs
  var React18 = __toESM(require_react(), 1);

  // node_modules/.store/@floating-ui/utils@0.2.12-gOxTzMf36nCrbeHpEew-rw/node_modules/@floating-ui/utils/dist/floating-ui.utils.dom.mjs
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
  function isElement(value) {
    if (!hasWindow()) {
      return false;
    }
    return value instanceof Element || value instanceof getWindow(value).Element;
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
  function isOverflowElement(element) {
    const {
      overflow,
      overflowX,
      overflowY,
      display
    } = getComputedStyle2(element);
    return /auto|scroll|overlay|hidden|clip/.test(overflow + overflowY + overflowX) && display !== "inline" && display !== "contents";
  }
  function isTableElement(element) {
    return /^(table|td|th)$/.test(getNodeName(element));
  }
  function isTopLayer(element) {
    try {
      if (element.matches(":popover-open")) {
        return true;
      }
    } catch (_e) {
    }
    try {
      return element.matches(":modal");
    } catch (_e) {
      return false;
    }
  }
  var willChangeRe = /transform|translate|scale|rotate|perspective|filter/;
  var containRe = /paint|layout|strict|content/;
  var isNotNone = (value) => !!value && value !== "none";
  var isWebKitValue;
  function isContainingBlock(elementOrCss) {
    const css = isElement(elementOrCss) ? getComputedStyle2(elementOrCss) : elementOrCss;
    return isNotNone(css.transform) || isNotNone(css.translate) || isNotNone(css.scale) || isNotNone(css.rotate) || isNotNone(css.perspective) || !isWebKit() && (isNotNone(css.backdropFilter) || isNotNone(css.filter)) || willChangeRe.test(css.willChange || "") || containRe.test(css.contain || "");
  }
  function getContainingBlock(element) {
    let currentNode = getParentNode(element);
    while (isHTMLElement(currentNode) && !isLastTraversableNode(currentNode)) {
      if (isContainingBlock(currentNode)) {
        return currentNode;
      } else if (isTopLayer(currentNode)) {
        return null;
      }
      currentNode = getParentNode(currentNode);
    }
    return null;
  }
  function isWebKit() {
    if (isWebKitValue == null) {
      isWebKitValue = typeof CSS !== "undefined" && CSS.supports && CSS.supports("-webkit-backdrop-filter", "none");
    }
    return isWebKitValue;
  }
  function isLastTraversableNode(node) {
    return /^(html|body|#document)$/.test(getNodeName(node));
  }
  function getComputedStyle2(element) {
    return getWindow(element).getComputedStyle(element);
  }
  function getNodeScroll(element) {
    if (isElement(element)) {
      return {
        scrollLeft: element.scrollLeft,
        scrollTop: element.scrollTop
      };
    }
    return {
      scrollLeft: element.scrollX,
      scrollTop: element.scrollY
    };
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
  function getNearestOverflowAncestor(node) {
    const parentNode = getParentNode(node);
    if (isLastTraversableNode(parentNode)) {
      return (node.ownerDocument || node).body;
    }
    if (isHTMLElement(parentNode) && isOverflowElement(parentNode)) {
      return parentNode;
    }
    return getNearestOverflowAncestor(parentNode);
  }
  function getOverflowAncestors(node, list, traverseIframes) {
    var _node$ownerDocument2;
    if (list === void 0) {
      list = [];
    }
    if (traverseIframes === void 0) {
      traverseIframes = true;
    }
    const scrollableAncestor = getNearestOverflowAncestor(node);
    const isBody = scrollableAncestor === ((_node$ownerDocument2 = node.ownerDocument) == null ? void 0 : _node$ownerDocument2.body);
    const win = getWindow(scrollableAncestor);
    if (isBody) {
      const frameElement = getFrameElement(win);
      return list.concat(win, win.visualViewport || [], isOverflowElement(scrollableAncestor) ? scrollableAncestor : [], frameElement && traverseIframes ? getOverflowAncestors(frameElement) : []);
    } else {
      return list.concat(scrollableAncestor, getOverflowAncestors(scrollableAncestor, [], traverseIframes));
    }
  }
  function getFrameElement(win) {
    return win.parent && Object.getPrototypeOf(win.parent) ? win.frameElement : null;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/composite/root/CompositeRootContext.mjs
  var React16 = __toESM(require_react(), 1);
  var CompositeRootContext = /* @__PURE__ */ React16.createContext(void 0);
  if (true) CompositeRootContext.displayName = "CompositeRootContext";
  function useCompositeRootContext(optional = false) {
    const context = React16.useContext(CompositeRootContext);
    if (context === void 0 && !optional) {
      throw new Error(true ? "Base UI: CompositeRootContext is missing. Composite parts must be placed within <Composite.Root>." : formatErrorMessage_default(16));
    }
    return context;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/useFocusableWhenDisabled.mjs
  var React17 = __toESM(require_react(), 1);
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
    const props = React17.useMemo(() => {
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

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/owner.mjs
  function ownerDocument(node) {
    return node?.ownerDocument || document;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/dispatchClickWithModifiers.mjs
  function dispatchClickWithModifiers(target, sourceEvent, {
    detail = 0,
    pointerType = ""
  } = {}) {
    target.dispatchEvent(new (getWindow(target)).PointerEvent("click", {
      bubbles: true,
      cancelable: true,
      composed: true,
      detail,
      pointerType,
      shiftKey: sourceEvent.shiftKey,
      ctrlKey: sourceEvent.ctrlKey,
      altKey: sourceEvent.altKey,
      metaKey: sourceEvent.metaKey
    }));
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/use-button/useButton.mjs
  function useButton(parameters = {}) {
    const {
      disabled: disabled2 = false,
      focusableWhenDisabled,
      tabIndex = 0,
      native: isNativeButton = true,
      composite: compositeProp
    } = parameters;
    const elementRef = React18.useRef(null);
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
      React18.useEffect(() => {
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
    const updateDisabled = React18.useCallback(() => {
      const element = elementRef.current;
      if (!isButtonElement(element)) {
        return;
      }
      if (isCompositeItem && disabled2 && focusableWhenDisabledProps.disabled === void 0 && element.disabled) {
        element.disabled = false;
      }
    }, [disabled2, focusableWhenDisabledProps.disabled, isCompositeItem]);
    useIsoLayoutEffect(updateDisabled, [updateDisabled]);
    const getButtonProps = React18.useCallback((externalProps = {}) => {
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

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/addEventListener.mjs
  function addEventListener(target, type, listener, options) {
    target.addEventListener(type, listener, options);
    return () => {
      target.removeEventListener(type, listener, options);
    };
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useValueAsRef.mjs
  function useValueAsRef(value) {
    const latest = useRefWithInit(createLatestRef, value).current;
    latest.next = value;
    useIsoLayoutEffect(latest.effect);
    return latest;
  }
  function createLatestRef(value) {
    const latest = {
      current: value,
      next: value,
      effect: () => {
        latest.current = latest.next;
      }
    };
    return latest;
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/platform/parts.mjs
  var parts_exports = {};
  __export(parts_exports, {
    engine: () => engine_exports,
    env: () => env_exports,
    mediaQuery: () => media_query_exports,
    os: () => os_exports,
    screenReader: () => screen_reader_exports
  });

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/platform/os.mjs
  var os_exports = {};
  __export(os_exports, {
    android: () => android,
    apple: () => apple,
    ios: () => ios,
    linux: () => linux,
    mac: () => mac,
    windows: () => windows
  });

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/platform/shared.mjs
  function readRawData() {
    if (typeof navigator === "undefined") {
      return {
        userAgent: "",
        platform: "",
        maxTouchPoints: 0
      };
    }
    if (true) {
      const uaData = navigator.userAgentData;
      if (uaData && Array.isArray(uaData.brands)) {
        return {
          userAgent: uaData.brands.map(({
            brand,
            version: version2
          }) => `${brand}/${version2}`).join(" "),
          platform: uaData.platform ?? navigator.platform ?? "",
          maxTouchPoints: navigator.maxTouchPoints ?? 0
        };
      }
    }
    return {
      userAgent: navigator.userAgent,
      platform: navigator.platform ?? "",
      maxTouchPoints: navigator.maxTouchPoints ?? 0
    };
  }
  var {
    userAgent,
    platform,
    maxTouchPoints
  } = readRawData();
  var lowerUserAgent = userAgent.toLowerCase();
  var lowerPlatform = platform.toLowerCase();

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/platform/os.mjs
  var ios = /^i(os$|p)/.test(lowerPlatform) || lowerPlatform === "macintel" && maxTouchPoints > 1;
  var ANDROID_STRING = "android";
  var android = lowerPlatform === ANDROID_STRING || lowerUserAgent.includes(ANDROID_STRING);
  var mac = !ios && lowerPlatform.startsWith("mac");
  var windows = lowerPlatform.startsWith("win");
  var linux = !android && /^(linux|chrome os)/.test(lowerPlatform);
  var apple = mac || ios;

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/platform/engine.mjs
  var engine_exports = {};
  __export(engine_exports, {
    blink: () => blink,
    gecko: () => gecko,
    webkit: () => webkit
  });
  var webkit = typeof CSS !== "undefined" && !!CSS.supports?.("-webkit-backdrop-filter:none");
  var gecko = !webkit && lowerUserAgent.includes("firefox");
  var blink = !webkit && lowerUserAgent.includes("chrom");

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/platform/screen-reader.mjs
  var screen_reader_exports = {};
  __export(screen_reader_exports, {
    voiceOver: () => voiceOver
  });
  var voiceOver = apple;

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/platform/env.mjs
  var env_exports = {};
  __export(env_exports, {
    jsdom: () => jsdom
  });
  var jsdom = /jsdom|happydom/.test(lowerUserAgent);

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/platform/media-query.mjs
  var media_query_exports = {};
  __export(media_query_exports, {
    iOS: () => iOS
  });
  var iOS = "@supports (-webkit-touch-callout: none)";

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useScrollLock.mjs
  var originalHtmlStyles = {};
  var originalBodyStyles = {};
  var originalHtmlScrollBehavior = "";
  function getViewportScroller(html, body) {
    return isOverflowElement(html) ? html : body;
  }
  function isPageScrollLocked(win, html, body) {
    return /hidden|clip/.test(win.getComputedStyle(getViewportScroller(html, body)).overflowY);
  }
  function hasInsetScrollbars(referenceElement) {
    if (typeof document === "undefined") {
      return false;
    }
    const doc = ownerDocument(referenceElement);
    const win = getWindow(doc);
    return win.innerWidth - doc.documentElement.clientWidth > 0;
  }
  function supportsStableScrollbarGutter(referenceElement) {
    const supported = typeof CSS !== "undefined" && CSS.supports && CSS.supports("scrollbar-gutter", "stable");
    if (!supported || typeof document === "undefined") {
      return false;
    }
    const doc = ownerDocument(referenceElement);
    const html = doc.documentElement;
    const body = doc.body;
    const scrollContainer = getViewportScroller(html, body);
    const originalScrollContainerOverflowY = scrollContainer.style.overflowY;
    const originalHtmlStyleGutter = html.style.scrollbarGutter;
    html.style.scrollbarGutter = "stable";
    scrollContainer.style.overflowY = "scroll";
    const before = scrollContainer.offsetWidth;
    scrollContainer.style.overflowY = "hidden";
    const after = scrollContainer.offsetWidth;
    scrollContainer.style.overflowY = originalScrollContainerOverflowY;
    html.style.scrollbarGutter = originalHtmlStyleGutter;
    return before === after;
  }
  function preventScrollOverlayScrollbars(referenceElement) {
    const doc = ownerDocument(referenceElement);
    const html = doc.documentElement;
    const body = doc.body;
    const elementToLock = getViewportScroller(html, body);
    const originalElementToLockStyles = {
      overflowY: elementToLock.style.overflowY,
      overflowX: elementToLock.style.overflowX
    };
    Object.assign(elementToLock.style, {
      overflowY: "hidden",
      overflowX: "hidden"
    });
    return () => {
      Object.assign(elementToLock.style, originalElementToLockStyles);
    };
  }
  function preventScrollInsetScrollbars(referenceElement) {
    const doc = ownerDocument(referenceElement);
    const html = doc.documentElement;
    const body = doc.body;
    const win = getWindow(html);
    let scrollTop = 0;
    let scrollLeft = 0;
    let updateGutterOnly = false;
    const resizeFrame = AnimationFrame.create();
    if (parts_exports.engine.webkit && (win.visualViewport?.scale ?? 1) !== 1) {
      return () => {
      };
    }
    function lockScroll() {
      const htmlStyles = win.getComputedStyle(html);
      const bodyStyles = win.getComputedStyle(body);
      const htmlScrollbarGutterValue = htmlStyles.scrollbarGutter || "";
      const hasBothEdges = htmlScrollbarGutterValue.includes("both-edges");
      const scrollbarGutterValue = hasBothEdges ? "stable both-edges" : "stable";
      scrollTop = html.scrollTop;
      scrollLeft = html.scrollLeft;
      originalHtmlStyles = {
        scrollbarGutter: html.style.scrollbarGutter,
        overflowY: html.style.overflowY,
        overflowX: html.style.overflowX
      };
      originalHtmlScrollBehavior = html.style.scrollBehavior;
      originalBodyStyles = {
        position: body.style.position,
        height: body.style.height,
        width: body.style.width,
        boxSizing: body.style.boxSizing,
        overflowY: body.style.overflowY,
        overflowX: body.style.overflowX,
        scrollBehavior: body.style.scrollBehavior
      };
      const isScrollableY = html.scrollHeight > html.clientHeight;
      const isScrollableX = html.scrollWidth > html.clientWidth;
      const hasConstantOverflowY = htmlStyles.overflowY === "scroll" || bodyStyles.overflowY === "scroll";
      const hasConstantOverflowX = htmlStyles.overflowX === "scroll" || bodyStyles.overflowX === "scroll";
      const scrollbarWidth = Math.max(0, win.innerWidth - body.clientWidth);
      const scrollbarHeight = Math.max(0, win.innerHeight - body.clientHeight);
      const marginY = parseFloat(bodyStyles.marginTop) + parseFloat(bodyStyles.marginBottom);
      const marginX = parseFloat(bodyStyles.marginLeft) + parseFloat(bodyStyles.marginRight);
      const elementToLock = getViewportScroller(html, body);
      updateGutterOnly = supportsStableScrollbarGutter(referenceElement);
      if (updateGutterOnly) {
        html.style.scrollbarGutter = scrollbarGutterValue;
        elementToLock.style.overflowY = "hidden";
        elementToLock.style.overflowX = "hidden";
        return;
      }
      Object.assign(html.style, {
        scrollbarGutter: scrollbarGutterValue,
        overflowY: "hidden",
        overflowX: "hidden"
      });
      if (isScrollableY || hasConstantOverflowY) {
        html.style.overflowY = "scroll";
      }
      if (isScrollableX || hasConstantOverflowX) {
        html.style.overflowX = "scroll";
      }
      Object.assign(body.style, {
        position: "relative",
        height: marginY || scrollbarHeight ? `calc(100dvh - ${marginY + scrollbarHeight}px)` : "100dvh",
        width: marginX || scrollbarWidth ? `calc(100vw - ${marginX + scrollbarWidth}px)` : "100vw",
        boxSizing: "border-box",
        // Assign the longhands that `cleanup` restores, so nothing is left behind.
        overflowY: "hidden",
        overflowX: "hidden",
        scrollBehavior: "unset"
      });
      body.scrollTop = scrollTop;
      body.scrollLeft = scrollLeft;
      html.setAttribute("data-base-ui-scroll-locked", "");
      html.style.scrollBehavior = "unset";
    }
    function cleanup() {
      Object.assign(html.style, originalHtmlStyles);
      Object.assign(body.style, originalBodyStyles);
      if (!updateGutterOnly) {
        html.scrollTop = scrollTop;
        html.scrollLeft = scrollLeft;
        html.removeAttribute("data-base-ui-scroll-locked");
        html.style.scrollBehavior = originalHtmlScrollBehavior;
      }
    }
    function handleResize() {
      cleanup();
      resizeFrame.request(lockScroll);
    }
    lockScroll();
    const unsubscribeResize = addEventListener(win, "resize", handleResize);
    return () => {
      resizeFrame.cancel();
      cleanup();
      if (typeof win.removeEventListener === "function") {
        unsubscribeResize();
      }
    };
  }
  var ScrollLocker = class {
    lockCount = 0;
    restore = null;
    timeoutLock = Timeout.create();
    timeoutUnlock = Timeout.create();
    acquire(referenceElement) {
      this.lockCount += 1;
      if (this.lockCount === 1 && this.restore === null) {
        this.timeoutLock.start(0, () => this.lock(referenceElement));
      }
      return this.release;
    }
    release = () => {
      this.lockCount -= 1;
      if (this.lockCount === 0 && this.restore) {
        this.timeoutUnlock.start(0, this.unlock);
      }
    };
    unlock = () => {
      if (this.lockCount === 0 && this.restore) {
        this.restore?.();
        this.restore = null;
      }
    };
    lock(referenceElement) {
      if (this.lockCount === 0 || this.restore !== null) {
        return;
      }
      const doc = ownerDocument(referenceElement);
      const html = doc.documentElement;
      const body = doc.body;
      const win = getWindow(html);
      if (isPageScrollLocked(win, html, body)) {
        const observer = new win.MutationObserver(() => {
          if (isPageScrollLocked(win, html, body)) {
            return;
          }
          observer.disconnect();
          this.restore = null;
          this.lock(referenceElement);
        });
        const options = {
          attributes: true
        };
        observer.observe(html, options);
        observer.observe(body, options);
        this.restore = () => observer.disconnect();
        return;
      }
      const hasOverlayScrollbars = parts_exports.os.ios || !hasInsetScrollbars(referenceElement);
      this.restore = hasOverlayScrollbars ? preventScrollOverlayScrollbars(referenceElement) : preventScrollInsetScrollbars(referenceElement);
    }
  };
  var SCROLL_LOCKER = new ScrollLocker();
  function useScrollLock(enabled = true, referenceElement = null) {
    useIsoLayoutEffect(() => {
      if (!enabled) {
        return void 0;
      }
      return SCROLL_LOCKER.acquire(referenceElement);
    }, [enabled, referenceElement]);
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/utils/event.mjs
  function stopEvent(event) {
    event.preventDefault();
    event.stopPropagation();
  }
  function isReactEvent(event) {
    return "nativeEvent" in event;
  }
  function isVirtualClick(event) {
    if (event.pointerType === "" && event.isTrusted) {
      return true;
    }
    if (parts_exports.os.android && event.pointerType) {
      return event.type === "click" && event.buttons === 1;
    }
    return event.detail === 0 && !event.pointerType;
  }
  function isVirtualPointerEvent(event) {
    if (parts_exports.env.jsdom) {
      return false;
    }
    return !parts_exports.os.android && event.width === 0 && event.height === 0 || // Chrome synthesizes a screen reader press (TalkBack, VoiceOver, NVDA) as a 1x1 mouse
    // `pointerdown` with no pressure. TalkBack can report a pressed button, while desktop
    // mouse presses can report no pressure, so only require no pressed button off Android.
    event.type === "pointerdown" && event.width === 1 && event.height === 1 && event.pressure === 0 && event.detail === 0 && event.pointerType === "mouse" && (parts_exports.os.android || event.buttons === 0) || // iOS VoiceOver returns 0.333• for width/height.
    event.width < 1 && event.height < 1 && event.pressure === 0 && event.detail === 0 && event.pointerType === "touch";
  }
  function isMouseLikePointerType(pointerType, strict) {
    const values = ["mouse", "pen"];
    if (!strict) {
      values.push("", void 0);
    }
    return values.includes(pointerType);
  }
  function isClickLikeEvent(event) {
    const type = event.type;
    return type === "click" || type === "mousedown" || type === "keydown" || type === "keyup";
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/shadowDom.mjs
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
  function closest(node, selector) {
    let current = node;
    while (current) {
      if (isElement(current) && current.matches(selector)) {
        return current;
      }
      current = current.assignedSlot ?? current.parentNode ?? (isShadowRoot(current) ? current.host : null);
    }
    return null;
  }
  function getTarget(event) {
    if ("composedPath" in event) {
      return event.composedPath()[0] ?? event.target;
    }
    return event.target;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/utils/constants.mjs
  var FOCUSABLE_ATTRIBUTE = "data-base-ui-focusable";
  var TYPEABLE_SELECTOR = "input:not([type='hidden']):not([disabled]),[contenteditable]:not([contenteditable='false']),textarea:not([disabled])";
  var ARROW_LEFT = "ArrowLeft";
  var ARROW_RIGHT = "ArrowRight";
  var ARROW_UP = "ArrowUp";
  var ARROW_DOWN = "ArrowDown";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/CommonPopupDataAttributes.mjs
  var open = "data-open";
  var closed = "data-closed";
  var anchorHidden = "data-anchor-hidden";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/CommonTriggerDataAttributes.mjs
  var CommonTriggerDataAttributes_exports = {};
  __export(CommonTriggerDataAttributes_exports, {
    popupOpen: () => popupOpen,
    pressed: () => pressed
  });
  var popupOpen = "data-popup-open";
  var pressed = "data-pressed";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/popupStateMapping.mjs
  var TRIGGER_HOOK = {
    [popupOpen]: ""
  };
  var PRESSABLE_TRIGGER_HOOK = {
    [popupOpen]: "",
    [pressed]: ""
  };
  var POPUP_OPEN_HOOK = {
    [open]: ""
  };
  var POPUP_CLOSED_HOOK = {
    [closed]: ""
  };
  var ANCHOR_HIDDEN_HOOK = {
    [anchorHidden]: ""
  };
  var triggerOpenStateMapping = {
    open(value) {
      if (value) {
        return TRIGGER_HOOK;
      }
      return null;
    }
  };
  var pressableTriggerOpenStateMapping = {
    open(value) {
      if (value) {
        return PRESSABLE_TRIGGER_HOOK;
      }
      return null;
    }
  };
  var popupStateMapping = {
    open(value) {
      if (value) {
        return POPUP_OPEN_HOOK;
      }
      return POPUP_CLOSED_HOOK;
    },
    anchorHidden(value) {
      if (value) {
        return ANCHOR_HIDDEN_HOOK;
      }
      return null;
    }
  };
  var popupTransitionStateMapping = {
    ...popupStateMapping,
    ...transitionStatusMapping
  };

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/utils/element.mjs
  function isEventTargetWithin(event, node) {
    if (node == null) {
      return false;
    }
    if ("composedPath" in event) {
      return event.composedPath().includes(node);
    }
    const eventAgain = event;
    return eventAgain.target != null && node.contains(eventAgain.target);
  }
  function isRootElement(element) {
    return element.matches("html,body");
  }
  function isTypeableElement(element) {
    return isHTMLElement(element) && element.matches(TYPEABLE_SELECTOR);
  }
  function isTypeableCombobox(element) {
    if (!element) {
      return false;
    }
    return element.getAttribute("role") === "combobox" && isTypeableElement(element);
  }
  function getFloatingFocusElement(floatingElement) {
    if (!floatingElement) {
      return null;
    }
    return floatingElement.hasAttribute(FOCUSABLE_ATTRIBUTE) ? floatingElement : floatingElement.querySelector(`[${FOCUSABLE_ATTRIBUTE}]`) || floatingElement;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/components/FloatingFocusManager.mjs
  var React22 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/mergeCleanups.mjs
  function mergeCleanups(...cleanups) {
    return () => {
      for (let i = 0; i < cleanups.length; i += 1) {
        const cleanup = cleanups[i];
        if (cleanup) {
          cleanup();
        }
      }
    };
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/FocusGuard.mjs
  var React19 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/visuallyHidden.mjs
  var visuallyHiddenBase = {
    clipPath: "inset(50%)",
    overflow: "hidden",
    whiteSpace: "nowrap",
    border: 0,
    padding: 0,
    width: 1,
    height: 1,
    margin: -1
  };
  var visuallyHidden = {
    ...visuallyHiddenBase,
    position: "fixed",
    margin: 0,
    top: 0,
    left: 0
  };
  var visuallyHiddenInput = {
    ...visuallyHiddenBase,
    position: "absolute"
  };

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/FocusGuard.mjs
  var import_jsx_runtime23 = __toESM(require_jsx_runtime(), 1);
  var FocusGuard = /* @__PURE__ */ React19.forwardRef(function FocusGuard2(props, ref) {
    const [role, setRole] = React19.useState();
    useIsoLayoutEffect(() => {
      if (parts_exports.screenReader.voiceOver && parts_exports.engine.webkit) {
        setRole("button");
      }
    }, []);
    const restProps = {
      tabIndex: 0,
      // Role is only for VoiceOver
      role
    };
    return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)("span", {
      ...props,
      ref,
      style: visuallyHidden,
      "aria-hidden": role ? void 0 : true,
      ...restProps,
      "data-base-ui-focus-guard": ""
    });
  });
  if (true) FocusGuard.displayName = "FocusGuard";

  // node_modules/.store/@floating-ui/utils@0.2.12-gOxTzMf36nCrbeHpEew-rw/node_modules/@floating-ui/utils/dist/floating-ui.utils.mjs
  var min = Math.min;
  var max = Math.max;
  var round = Math.round;
  var floor = Math.floor;
  var createCoords = (v) => ({
    x: v,
    y: v
  });
  var oppositeSideMap = {
    left: "right",
    right: "left",
    bottom: "top",
    top: "bottom"
  };
  function clamp(start, value, end) {
    return max(start, min(value, end));
  }
  function evaluate(value, param) {
    return typeof value === "function" ? value(param) : value;
  }
  function getSide(placement) {
    return placement.split("-")[0];
  }
  function getAlignment(placement) {
    return placement.split("-")[1];
  }
  function getOppositeAxis(axis) {
    return axis === "x" ? "y" : "x";
  }
  function getAxisLength(axis) {
    return axis === "y" ? "height" : "width";
  }
  function getSideAxis(placement) {
    const firstChar = placement[0];
    return firstChar === "t" || firstChar === "b" ? "y" : "x";
  }
  function getAlignmentAxis(placement) {
    return getOppositeAxis(getSideAxis(placement));
  }
  function getAlignmentSides(placement, rects, rtl) {
    if (rtl === void 0) {
      rtl = false;
    }
    const alignment = getAlignment(placement);
    const alignmentAxis = getAlignmentAxis(placement);
    const length = getAxisLength(alignmentAxis);
    let mainAlignmentSide = alignmentAxis === "x" ? alignment === (rtl ? "end" : "start") ? "right" : "left" : alignment === "start" ? "bottom" : "top";
    if (rects.reference[length] > rects.floating[length]) {
      mainAlignmentSide = getOppositePlacement(mainAlignmentSide);
    }
    return [mainAlignmentSide, getOppositePlacement(mainAlignmentSide)];
  }
  function getExpandedPlacements(placement) {
    const oppositePlacement = getOppositePlacement(placement);
    return [getOppositeAlignmentPlacement(placement), oppositePlacement, getOppositeAlignmentPlacement(oppositePlacement)];
  }
  function getOppositeAlignmentPlacement(placement) {
    return placement.includes("start") ? placement.replace("start", "end") : placement.replace("end", "start");
  }
  var lrPlacement = ["left", "right"];
  var rlPlacement = ["right", "left"];
  var tbPlacement = ["top", "bottom"];
  var btPlacement = ["bottom", "top"];
  function getSideList(side, isStart, rtl) {
    switch (side) {
      case "top":
      case "bottom":
        if (rtl) return isStart ? rlPlacement : lrPlacement;
        return isStart ? lrPlacement : rlPlacement;
      case "left":
      case "right":
        return isStart ? tbPlacement : btPlacement;
      default:
        return [];
    }
  }
  function getOppositeAxisPlacements(placement, flipAlignment, direction, rtl) {
    const alignment = getAlignment(placement);
    let list = getSideList(getSide(placement), direction === "start", rtl);
    if (alignment) {
      list = list.map((side) => side + "-" + alignment);
      if (flipAlignment) {
        list = list.concat(list.map(getOppositeAlignmentPlacement));
      }
    }
    return list;
  }
  function getOppositePlacement(placement) {
    const side = getSide(placement);
    return oppositeSideMap[side] + placement.slice(side.length);
  }
  function expandPaddingObject(padding) {
    var _padding$top, _padding$right, _padding$bottom, _padding$left;
    return {
      top: (_padding$top = padding.top) != null ? _padding$top : 0,
      right: (_padding$right = padding.right) != null ? _padding$right : 0,
      bottom: (_padding$bottom = padding.bottom) != null ? _padding$bottom : 0,
      left: (_padding$left = padding.left) != null ? _padding$left : 0
    };
  }
  function getPaddingObject(padding) {
    return typeof padding !== "number" ? expandPaddingObject(padding) : {
      top: padding,
      right: padding,
      bottom: padding,
      left: padding
    };
  }
  function rectToClientRect(rect) {
    const {
      x,
      y,
      width,
      height
    } = rect;
    return {
      width,
      height,
      top: y,
      left: x,
      right: x + width,
      bottom: y + height,
      x,
      y
    };
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/utils/composite.mjs
  function isIndexOutOfListBounds(list, index3) {
    return index3 < 0 || index3 >= list.length;
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
  function getNextListIndex(list, currentIndex, options) {
    const {
      decrement,
      loopFocus,
      allowEscape,
      disabledIndices,
      minIndex,
      maxIndex
    } = options;
    const step = () => findNonDisabledListIndex(list, {
      startingIndex: currentIndex,
      decrement,
      disabledIndices
    });
    let index3;
    let wrapped = false;
    if (!loopFocus) {
      index3 = decrement ? Math.max(minIndex, step()) : Math.min(maxIndex, step());
    } else if (decrement ? currentIndex <= minIndex : currentIndex >= maxIndex) {
      const outside = decrement ? -1 : list.length;
      if (allowEscape && currentIndex !== outside) {
        index3 = -1;
      } else {
        index3 = decrement ? maxIndex : minIndex;
        wrapped = true;
      }
    } else {
      index3 = step();
    }
    return {
      index: isIndexOutOfListBounds(list, index3) ? -1 : index3,
      wrapped
    };
  }
  function findNonDisabledListIndex(list, {
    startingIndex = -1,
    decrement = false,
    disabledIndices,
    amount = 1
  } = {}) {
    let index3 = startingIndex;
    do {
      index3 += decrement ? -amount : amount;
    } while (index3 >= 0 && index3 <= list.length - 1 && isListIndexDisabled(list, index3, disabledIndices));
    return index3;
  }
  function isListIndexDisabled(list, index3, disabledIndices) {
    const isExplicitlyDisabled = typeof disabledIndices === "function" ? disabledIndices(index3) : disabledIndices?.includes(index3) ?? false;
    if (isExplicitlyDisabled) {
      return true;
    }
    const element = list[index3];
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/utils/tabbable.mjs
  var CANDIDATE_SELECTOR = 'a[href],button,input,select,textarea,summary,details,iframe,object,embed,[tabindex],[contenteditable]:not([contenteditable="false"]),audio[controls],video[controls]';
  function getParentElement(element) {
    const assignedSlot = element.assignedSlot;
    if (assignedSlot) {
      return assignedSlot;
    }
    if (element.parentElement) {
      return element.parentElement;
    }
    const rootNode = element.getRootNode();
    return isShadowRoot(rootNode) ? rootNode.host : null;
  }
  function getDetailsSummary(details) {
    for (const child of Array.from(details.children)) {
      if (getNodeName(child) === "summary") {
        return child;
      }
    }
    return null;
  }
  function isWithinOpenDetailsSummary(element, details) {
    const summary = getDetailsSummary(details);
    return !!summary && (element === summary || contains(summary, element));
  }
  function isFocusableCandidate(element) {
    const nodeName = element ? getNodeName(element) : "";
    return element != null && element.matches(CANDIDATE_SELECTOR) && (nodeName !== "summary" || element.parentElement != null && getNodeName(element.parentElement) === "details" && getDetailsSummary(element.parentElement) === element) && (nodeName !== "details" || getDetailsSummary(element) == null) && (nodeName !== "input" || element.type !== "hidden");
  }
  function isFocusableElement(element) {
    if (!isFocusableCandidate(element) || !element.isConnected || element.matches(":disabled")) {
      return false;
    }
    for (let current = element; current; current = getParentElement(current)) {
      const isAncestor = current !== element;
      const isSlot = getNodeName(current) === "slot";
      if (current.hasAttribute("inert")) {
        return false;
      }
      if (isAncestor && getNodeName(current) === "details" && !current.open && !isWithinOpenDetailsSummary(element, current) || current.hasAttribute("hidden") || !isSlot && !isVisibleInTabbableTree(current, isAncestor)) {
        return false;
      }
    }
    return true;
  }
  function isVisibleInTabbableTree(element, isAncestor) {
    const styles = getComputedStyle2(element);
    if (!isAncestor) {
      return isElementVisible(element, styles);
    }
    return styles.display !== "none";
  }
  function getTabIndex(element) {
    const tabIndex = element.tabIndex;
    if (tabIndex < 0) {
      const nodeName = getNodeName(element);
      if (nodeName === "details" || nodeName === "audio" || nodeName === "video" || isHTMLElement(element) && element.isContentEditable) {
        return 0;
      }
    }
    return tabIndex;
  }
  function getNamedRadioInput(element) {
    if (getNodeName(element) !== "input") {
      return null;
    }
    const input = element;
    return input.type === "radio" && input.name !== "" ? input : null;
  }
  function isTabbableRadio(element, candidates) {
    const input = getNamedRadioInput(element);
    if (!input) {
      return true;
    }
    const checkedRadio = candidates.find((candidate) => {
      const radio = getNamedRadioInput(candidate);
      return radio?.name === input.name && radio.form === input.form && radio.checked;
    });
    if (checkedRadio) {
      return checkedRadio === input;
    }
    return candidates.find((candidate) => {
      const radio = getNamedRadioInput(candidate);
      return radio?.name === input.name && radio.form === input.form;
    }) === input;
  }
  function getComposedChildren(container) {
    if (isHTMLElement(container) && getNodeName(container) === "slot") {
      const assignedElements = container.assignedElements({
        flatten: true
      });
      if (assignedElements.length > 0) {
        return assignedElements;
      }
    }
    if (isHTMLElement(container) && container.shadowRoot) {
      return Array.from(container.shadowRoot.children);
    }
    return Array.from(container.children);
  }
  function appendCandidates(container, list) {
    getComposedChildren(container).forEach((child) => {
      if (isFocusableCandidate(child)) {
        list.push(child);
      }
      appendCandidates(child, list);
    });
  }
  function appendMatchingElements(container, selector, list) {
    getComposedChildren(container).forEach((child) => {
      if (isHTMLElement(child) && child.matches(selector)) {
        list.push(child);
      }
      appendMatchingElements(child, selector, list);
    });
  }
  function isTabbable(element) {
    return isFocusableElement(element) && getTabIndex(element) >= 0;
  }
  function focusable(container) {
    const candidates = [];
    appendCandidates(container, candidates);
    return candidates.filter(isFocusableElement);
  }
  function tabbable(container) {
    const candidates = focusable(container);
    return candidates.filter((element) => getTabIndex(element) >= 0 && isTabbableRadio(element, candidates));
  }
  function getTabbableIn(container, dir) {
    const list = tabbable(container);
    const len = list.length;
    if (len === 0) {
      return void 0;
    }
    const active = activeElement(ownerDocument(container));
    const index3 = list.indexOf(active);
    const nextIndex = index3 === -1 ? dir === 1 ? 0 : len - 1 : index3 + dir;
    return list[nextIndex];
  }
  function getNextTabbable(referenceElement) {
    return getTabbableIn(ownerDocument(referenceElement).body, 1) || referenceElement;
  }
  function getPreviousTabbable(referenceElement) {
    return getTabbableIn(ownerDocument(referenceElement).body, -1) || referenceElement;
  }
  function isOutsideEvent(event, container) {
    const containerElement = container || event.currentTarget;
    const relatedTarget = event.relatedTarget;
    return !relatedTarget || !contains(containerElement, relatedTarget);
  }
  function disableFocusInside(container) {
    const tabbableElements = tabbable(container);
    tabbableElements.forEach((element) => {
      element.dataset.tabindex = element.getAttribute("tabindex") || "";
      element.setAttribute("tabindex", "-1");
    });
  }
  function enableFocusInside(container) {
    const elements = [];
    appendMatchingElements(container, "[data-tabindex]", elements);
    elements.forEach((element) => {
      const tabindex = element.dataset.tabindex;
      delete element.dataset.tabindex;
      if (tabindex) {
        element.setAttribute("tabindex", tabindex);
      } else {
        element.removeAttribute("tabindex");
      }
    });
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/utils/nodes.mjs
  function getNodeChildren(nodes, id, onlyOpenChildren = true) {
    const directChildren = nodes.filter((node) => node.parentId === id);
    return directChildren.flatMap((child) => [...!onlyOpenChildren || child.context?.open ? [child] : [], ...getNodeChildren(nodes, child.id, onlyOpenChildren)]);
  }
  function getNodeAncestors(nodes, id) {
    let allAncestors = [];
    let currentParentId = nodes.find((node) => node.id === id)?.parentId;
    while (currentParentId) {
      const currentNode = nodes.find((node) => node.id === currentParentId);
      currentParentId = currentNode?.parentId;
      if (currentNode) {
        allAncestors = allAncestors.concat(currentNode);
      }
    }
    return allAncestors;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/utils/createAttribute.mjs
  function createAttribute(name16) {
    return `data-base-ui-${name16}`;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/utils/enqueueFocus.mjs
  var rafId = 0;
  function enqueueFocus(el, options = {}) {
    const {
      preventScroll = false,
      sync = false,
      shouldFocus
    } = options;
    cancelAnimationFrame(rafId);
    function exec() {
      if (shouldFocus && !shouldFocus()) {
        return;
      }
      el?.focus({
        preventScroll
      });
    }
    if (sync) {
      exec();
      return NOOP;
    }
    const currentRafId = requestAnimationFrame(exec);
    rafId = currentRafId;
    return () => {
      if (rafId === currentRafId) {
        cancelAnimationFrame(currentRafId);
        rafId = 0;
      }
    };
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/utils/markOthers.mjs
  var markerName = "data-base-ui-inert";
  var ariaHiddenCounterMap = /* @__PURE__ */ new WeakMap();
  var uncontrolledElementsSet = /* @__PURE__ */ new WeakSet();
  var markerCounterMap = /* @__PURE__ */ new WeakMap();
  var lockCount = 0;
  function unwrapHost(node) {
    if (!node) {
      return null;
    }
    return isShadowRoot(node) ? node.host : unwrapHost(node.parentNode);
  }
  var correctElements = (parent, targets) => targets.map((target) => {
    if (parent.contains(target)) {
      return target;
    }
    const correctedTarget = unwrapHost(target);
    if (parent.contains(correctedTarget)) {
      return correctedTarget;
    }
    return null;
  }).filter((x) => x != null);
  var buildKeepSet = (targets) => {
    const keep = /* @__PURE__ */ new Set();
    targets.forEach((target) => {
      let node = target;
      while (node && !keep.has(node)) {
        keep.add(node);
        node = node.parentNode;
      }
    });
    return keep;
  };
  var collectOutsideElements = (root, keepElements, stopElements) => {
    const outside = [];
    const walk = (parent) => {
      if (!parent || stopElements.has(parent)) {
        return;
      }
      Array.from(parent.children).forEach((node) => {
        if (getNodeName(node) === "script") {
          return;
        }
        if (keepElements.has(node)) {
          walk(node);
        } else {
          outside.push(node);
        }
      });
    };
    walk(root);
    return outside;
  };
  function applyAttributeToOthers(uncorrectedAvoidElements, body, ariaHidden, {
    mark = true
  }) {
    const avoidElements = correctElements(body, uncorrectedAvoidElements);
    const markerTargets = mark ? collectOutsideElements(body, buildKeepSet(avoidElements), new Set(avoidElements)) : [];
    const hiddenElements = [];
    const markedElements = [];
    if (ariaHidden) {
      const ariaLiveElements = correctElements(body, Array.from(body.querySelectorAll("[aria-live]")));
      const controlElements = avoidElements.concat(ariaLiveElements);
      const controlTargets = collectOutsideElements(body, buildKeepSet(controlElements), new Set(controlElements));
      controlTargets.forEach((node) => {
        const attr2 = node.getAttribute("aria-hidden");
        const alreadyHidden = attr2 !== null && attr2 !== "false";
        const counterValue = (ariaHiddenCounterMap.get(node) || 0) + 1;
        ariaHiddenCounterMap.set(node, counterValue);
        hiddenElements.push(node);
        if (counterValue === 1 && alreadyHidden) {
          uncontrolledElementsSet.add(node);
        }
        if (!alreadyHidden) {
          node.setAttribute("aria-hidden", "true");
        }
      });
    }
    if (mark) {
      markerTargets.forEach((node) => {
        const markerValue = (markerCounterMap.get(node) || 0) + 1;
        markerCounterMap.set(node, markerValue);
        markedElements.push(node);
        if (markerValue === 1) {
          node.setAttribute(markerName, "");
        }
      });
    }
    lockCount += 1;
    return () => {
      hiddenElements.forEach((element) => {
        const counterValue = (ariaHiddenCounterMap.get(element) || 0) - 1;
        ariaHiddenCounterMap.set(element, counterValue);
        if (!counterValue) {
          if (!uncontrolledElementsSet.has(element)) {
            element.removeAttribute("aria-hidden");
          }
          uncontrolledElementsSet.delete(element);
        }
      });
      if (mark) {
        markedElements.forEach((element) => {
          const markerValue = (markerCounterMap.get(element) || 0) - 1;
          markerCounterMap.set(element, markerValue);
          if (!markerValue) {
            element.removeAttribute(markerName);
          }
        });
      }
      lockCount -= 1;
      if (!lockCount) {
        ariaHiddenCounterMap = /* @__PURE__ */ new WeakMap();
        uncontrolledElementsSet = /* @__PURE__ */ new WeakSet();
        markerCounterMap = /* @__PURE__ */ new WeakMap();
      }
    };
  }
  function markOthers(avoidElements, options = {}) {
    const {
      ariaHidden = false,
      mark = true
    } = options;
    const body = ownerDocument(avoidElements[0]).body;
    return applyAttributeToOthers(avoidElements, body, ariaHidden, {
      mark
    });
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/components/FloatingPortal.mjs
  var React20 = __toESM(require_react(), 1);
  var ReactDOM2 = __toESM(require_react_dom(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/constants.mjs
  var DISABLED_TRANSITIONS_STYLE = {
    style: {
      transition: "none"
    }
  };
  var CLICK_TRIGGER_IDENTIFIER = "data-base-ui-click-trigger";
  var BASE_UI_SWIPE_IGNORE_ATTRIBUTE = "data-base-ui-swipe-ignore";
  var LEGACY_SWIPE_IGNORE_ATTRIBUTE = "data-swipe-ignore";
  var BASE_UI_SWIPE_IGNORE_SELECTOR = `[${BASE_UI_SWIPE_IGNORE_ATTRIBUTE}]`;
  var LEGACY_SWIPE_IGNORE_SELECTOR = `[${LEGACY_SWIPE_IGNORE_ATTRIBUTE}]`;
  var DROPDOWN_COLLISION_AVOIDANCE = {
    fallbackAxisSide: "none"
  };
  var ownerVisuallyHidden = {
    clipPath: "inset(50%)",
    position: "fixed",
    top: 0,
    left: 0
  };

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/components/FloatingPortal.mjs
  var import_jsx_runtime24 = __toESM(require_jsx_runtime(), 1);
  var PortalContext = /* @__PURE__ */ React20.createContext(null);
  if (true) PortalContext.displayName = "PortalContext";
  var usePortalContext = () => React20.useContext(PortalContext);
  var attr = createAttribute("portal");
  function useFloatingPortalNode(props = {}) {
    const {
      ref,
      container: containerProp,
      componentProps = EMPTY_OBJECT,
      elementProps
    } = props;
    const uniqueId = useId();
    const portalContext = usePortalContext();
    const parentPortalNode = portalContext?.portalNode;
    const [containerElement, setContainerElement] = React20.useState(null);
    const [portalNode, setPortalNode] = React20.useState(null);
    const setPortalNodeRef = useStableCallback((node) => {
      if (node !== null) {
        setPortalNode(node);
      }
    });
    const containerRef = React20.useRef(null);
    useIsoLayoutEffect(() => {
      if (containerProp === null) {
        if (containerRef.current) {
          containerRef.current = null;
          setPortalNode(null);
          setContainerElement(null);
        }
        return;
      }
      const resolvedContainer = (containerProp && (isNode(containerProp) ? containerProp : containerProp.current)) ?? parentPortalNode ?? document.body;
      if (resolvedContainer == null) {
        if (containerRef.current) {
          containerRef.current = null;
          setPortalNode(null);
          setContainerElement(null);
        }
        return;
      }
      if (containerRef.current !== resolvedContainer) {
        containerRef.current = resolvedContainer;
        setPortalNode(null);
        setContainerElement(resolvedContainer);
      }
    }, [containerProp, parentPortalNode]);
    const portalElement = useRenderElement("div", componentProps, {
      ref: [ref, setPortalNodeRef],
      props: [{
        id: uniqueId,
        [attr]: ""
      }, elementProps]
    });
    const portalSubtree = containerElement && portalElement ? /* @__PURE__ */ ReactDOM2.createPortal(portalElement, containerElement) : null;
    return {
      node: portalNode,
      // `id` and `render` props can override or remove the generated ID. Use the exact
      // rendered value so `aria-owns` never points at an ID absent from the DOM.
      nodeId: /* @__PURE__ */ React20.isValidElement(portalElement) ? portalElement.props.id : void 0,
      subtree: portalSubtree
    };
  }
  var FloatingPortal = /* @__PURE__ */ React20.forwardRef(function FloatingPortal2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      children,
      container,
      portalOwnerRole,
      ...elementProps
    } = componentProps;
    const {
      node: portalNode,
      nodeId: portalNodeId,
      subtree: portalSubtree
    } = useFloatingPortalNode({
      container,
      ref: forwardedRef,
      componentProps,
      elementProps
    });
    const beforeOutsideRef = React20.useRef(null);
    const afterOutsideRef = React20.useRef(null);
    const beforeInsideRef = React20.useRef(null);
    const afterInsideRef = React20.useRef(null);
    const [focusManagerState, setFocusManagerState] = React20.useState(null);
    const focusInsideDisabledRef = React20.useRef(false);
    const modal = focusManagerState?.modal;
    const open2 = focusManagerState?.open;
    const shouldRenderGuards = !!focusManagerState && !focusManagerState.modal && focusManagerState.open && !!portalNode;
    React20.useEffect(() => {
      if (!portalNode || modal) {
        return void 0;
      }
      function onFocus(event) {
        if (portalNode && event.relatedTarget && isOutsideEvent(event)) {
          if (event.type === "focusin") {
            if (focusInsideDisabledRef.current) {
              enableFocusInside(portalNode);
              focusInsideDisabledRef.current = false;
            }
          } else {
            disableFocusInside(portalNode);
            focusInsideDisabledRef.current = true;
          }
        }
      }
      return mergeCleanups(addEventListener(portalNode, "focusin", onFocus, true), addEventListener(portalNode, "focusout", onFocus, true));
    }, [portalNode, modal]);
    useIsoLayoutEffect(() => {
      if (!portalNode || open2 !== true || !focusInsideDisabledRef.current) {
        return;
      }
      enableFocusInside(portalNode);
      focusInsideDisabledRef.current = false;
    }, [open2, portalNode]);
    const portalContextValue = React20.useMemo(() => ({
      beforeOutsideRef,
      afterOutsideRef,
      beforeInsideRef,
      afterInsideRef,
      portalNode,
      setFocusManagerState
    }), [portalNode]);
    return /* @__PURE__ */ (0, import_jsx_runtime24.jsxs)(React20.Fragment, {
      children: [portalSubtree, /* @__PURE__ */ (0, import_jsx_runtime24.jsxs)(PortalContext.Provider, {
        value: portalContextValue,
        children: [shouldRenderGuards && portalNode && /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(FocusGuard, {
          "data-type": "outside",
          ref: beforeOutsideRef,
          onFocus: (event) => {
            if (isOutsideEvent(event, portalNode)) {
              beforeInsideRef.current?.focus();
            } else {
              const domReference = focusManagerState ? focusManagerState.domReference : null;
              const prevTabbable = getPreviousTabbable(domReference);
              prevTabbable?.focus();
            }
          }
        }), shouldRenderGuards && portalNode && /* @__PURE__ */ (0, import_jsx_runtime24.jsx)("span", {
          role: portalOwnerRole,
          "aria-owns": portalNodeId,
          style: ownerVisuallyHidden
        }), portalNode && /* @__PURE__ */ ReactDOM2.createPortal(children, portalNode), shouldRenderGuards && portalNode && /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(FocusGuard, {
          "data-type": "outside",
          ref: afterOutsideRef,
          onFocus: (event) => {
            if (isOutsideEvent(event, portalNode)) {
              afterInsideRef.current?.focus();
            } else {
              const domReference = focusManagerState ? focusManagerState.domReference : null;
              const nextTabbable = getNextTabbable(domReference);
              nextTabbable?.focus();
              if (focusManagerState?.closeOnFocusOut) {
                focusManagerState?.onOpenChange(false, createChangeEventDetails(reason_parts_exports.focusOut, event.nativeEvent));
              }
            }
          }
        })]
      })]
    });
  });
  if (true) FloatingPortal.displayName = "FloatingPortal";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/components/FloatingTree.mjs
  var React21 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/utils/createEventEmitter.mjs
  function createEventEmitter() {
    const map = /* @__PURE__ */ new Map();
    return {
      emit(event, data) {
        map.get(event)?.forEach((listener) => listener(data));
      },
      on(event, listener) {
        if (!map.has(event)) {
          map.set(event, /* @__PURE__ */ new Set());
        }
        map.get(event).add(listener);
      },
      off(event, listener) {
        map.get(event)?.delete(listener);
      }
    };
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/components/FloatingTree.mjs
  var import_jsx_runtime25 = __toESM(require_jsx_runtime(), 1);
  var FloatingNodeContext = /* @__PURE__ */ React21.createContext(null);
  if (true) FloatingNodeContext.displayName = "FloatingNodeContext";
  var FloatingTreeContext = /* @__PURE__ */ React21.createContext(null);
  if (true) FloatingTreeContext.displayName = "FloatingTreeContext";
  var useFloatingParentNodeId = () => React21.useContext(FloatingNodeContext)?.id || null;
  var useFloatingTree = (externalTree) => {
    const contextTree = React21.useContext(FloatingTreeContext);
    return externalTree ?? contextTree;
  };

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/components/FloatingFocusManager.mjs
  var import_jsx_runtime26 = __toESM(require_jsx_runtime(), 1);
  function getEventType(event, lastInteractionType) {
    const win = getWindow(getTarget(event));
    if (event instanceof win.KeyboardEvent) {
      return "keyboard";
    }
    if (event instanceof win.FocusEvent) {
      return lastInteractionType || "keyboard";
    }
    if ("pointerType" in event) {
      return event.pointerType || (isVirtualClick(event) ? "keyboard" : lastInteractionType || "mouse");
    }
    if ("touches" in event) {
      return "touch";
    }
    if (event instanceof win.MouseEvent) {
      return lastInteractionType || (event.detail === 0 ? "keyboard" : "mouse");
    }
    return "";
  }
  var LIST_LIMIT = 20;
  var previouslyFocusedElements = [];
  function clearDisconnectedPreviouslyFocusedElements() {
    previouslyFocusedElements = previouslyFocusedElements.filter((entry) => {
      return entry.deref()?.isConnected;
    });
  }
  function addPreviouslyFocusedElement(element) {
    clearDisconnectedPreviouslyFocusedElements();
    if (element && getNodeName(element) !== "body") {
      previouslyFocusedElements.push(new WeakRef(element));
      if (previouslyFocusedElements.length > LIST_LIMIT) {
        previouslyFocusedElements = previouslyFocusedElements.slice(-LIST_LIMIT);
      }
    }
  }
  function getPreviouslyFocusedElement() {
    clearDisconnectedPreviouslyFocusedElements();
    return previouslyFocusedElements[previouslyFocusedElements.length - 1]?.deref();
  }
  function getFirstTabbableElement(container) {
    if (!container) {
      return null;
    }
    if (isTabbable(container)) {
      return container;
    }
    return tabbable(container)[0] || container;
  }
  function handleTabIndex(floatingFocusElement) {
    if (floatingFocusElement.hasAttribute("tabindex") && !floatingFocusElement.hasAttribute("data-tabindex")) {
      return;
    }
    if (!floatingFocusElement.getAttribute("role")?.includes("dialog")) {
      return;
    }
    const focusableElements = focusable(floatingFocusElement);
    const tabbableContent = focusableElements.filter((element) => {
      const dataTabIndex = element.getAttribute("data-tabindex") || "";
      return isTabbable(element) || element.hasAttribute("data-tabindex") && !dataTabIndex.startsWith("-");
    });
    const tabIndex = floatingFocusElement.getAttribute("tabindex");
    if (tabbableContent.length === 0) {
      if (tabIndex !== "0") {
        floatingFocusElement.setAttribute("tabindex", "0");
        floatingFocusElement.setAttribute("data-tabindex", "0");
      }
    } else if (tabIndex !== "-1" || floatingFocusElement.hasAttribute("data-tabindex") && floatingFocusElement.getAttribute("data-tabindex") !== "-1") {
      floatingFocusElement.setAttribute("tabindex", "-1");
      floatingFocusElement.setAttribute("data-tabindex", "-1");
    }
  }
  function FloatingFocusManager(props) {
    const {
      context: store,
      children,
      disabled: disabled2 = false,
      initialFocus = true,
      returnFocus = true,
      explicitReturnFocus,
      restoreFocus = false,
      modal = true,
      closeOnFocusOut = true,
      openInteractionType = "",
      nextFocusableElement,
      previousFocusableElement,
      beforeContentFocusGuardRef,
      externalTree,
      getInsideElements
    } = props;
    const open2 = store.useState("open");
    const domReference = store.useState("domReferenceElement");
    const floating = store.useState("floatingElement");
    const {
      events,
      dataRef
    } = store.context;
    const getNodeId = useStableCallback(() => dataRef.current.floatingContext?.nodeId);
    const ignoreInitialFocus = initialFocus === false;
    const isUntrappedTypeableCombobox = isTypeableCombobox(domReference) && ignoreInitialFocus;
    const initialFocusRef = useValueAsRef(initialFocus);
    const returnFocusRef = useValueAsRef(returnFocus);
    const explicitReturnFocusRef = useValueAsRef(explicitReturnFocus);
    const openInteractionTypeRef = useValueAsRef(openInteractionType);
    const openRef = useValueAsRef(open2);
    const tree = useFloatingTree(externalTree);
    const portalContext = usePortalContext();
    const preventReturnFocusRef = React22.useRef(false);
    const isPointerDownRef = React22.useRef(false);
    const pointerDownOutsideRef = React22.useRef(false);
    const lastFocusedTabbableRef = React22.useRef(null);
    const closeTypeRef = React22.useRef("");
    const lastInteractionTypeRef = React22.useRef("");
    const beforeGuardRef = React22.useRef(null);
    const afterGuardRef = React22.useRef(null);
    const mergedBeforeGuardRef = useMergedRefs(beforeGuardRef, beforeContentFocusGuardRef, portalContext?.beforeInsideRef);
    const mergedAfterGuardRef = useMergedRefs(afterGuardRef, portalContext?.afterInsideRef);
    const blurTimeout = useTimeout();
    const pointerDownTimeout = useTimeout();
    const restoreFocusFrame = useAnimationFrame();
    const isInsidePortal = portalContext != null;
    const floatingFocusElement = getFloatingFocusElement(floating);
    const getTabbableContent = useStableCallback((container = floatingFocusElement) => {
      return container ? tabbable(container) : [];
    });
    const getResolvedInsideElements = useStableCallback(() => getInsideElements?.().filter((element) => element != null) ?? []);
    React22.useEffect(() => {
      if (disabled2 || !modal) {
        return void 0;
      }
      function onKeyDown(event) {
        if (event.key === "Tab") {
          if (contains(floatingFocusElement, activeElement(ownerDocument(floatingFocusElement))) && getTabbableContent().length === 0 && !isUntrappedTypeableCombobox) {
            stopEvent(event);
          }
        }
      }
      const doc = ownerDocument(floatingFocusElement);
      return addEventListener(doc, "keydown", onKeyDown);
    }, [disabled2, floatingFocusElement, modal, isUntrappedTypeableCombobox, getTabbableContent]);
    React22.useEffect(() => {
      if (disabled2 || !open2) {
        return void 0;
      }
      const doc = ownerDocument(floatingFocusElement);
      function clearPointerDownOutside() {
        pointerDownOutsideRef.current = false;
      }
      function onPointerDown(event) {
        const target = getTarget(event);
        const insideElements = getResolvedInsideElements();
        const pointerTargetInside = contains(floating, target) || contains(domReference, target) || contains(portalContext?.portalNode, target) || insideElements.some((element) => element === target || contains(element, target));
        pointerDownOutsideRef.current = !pointerTargetInside;
        lastInteractionTypeRef.current = event.pointerType || "keyboard";
        if (closest(target, `[${CLICK_TRIGGER_IDENTIFIER}]`)) {
          isPointerDownRef.current = true;
          pointerDownTimeout.start(0, () => {
            isPointerDownRef.current = false;
          });
        }
      }
      function onKeyDown() {
        lastInteractionTypeRef.current = "keyboard";
      }
      return mergeCleanups(
        addEventListener(doc, "pointerdown", onPointerDown, true),
        addEventListener(doc, "pointerup", clearPointerDownOutside, true),
        addEventListener(doc, "pointercancel", clearPointerDownOutside, true),
        addEventListener(doc, "keydown", onKeyDown, true),
        // Avoid a stale `true` leaking into the next open (e.g. keep-mounted popups)
        // if the popup dismissed between pointerdown and pointerup.
        clearPointerDownOutside
      );
    }, [disabled2, floating, domReference, floatingFocusElement, open2, portalContext, pointerDownTimeout, getResolvedInsideElements]);
    React22.useEffect(() => {
      if (disabled2 || !closeOnFocusOut) {
        return void 0;
      }
      const doc = ownerDocument(floatingFocusElement);
      function handlePointerDown() {
        isPointerDownRef.current = true;
        pointerDownTimeout.start(0, () => {
          isPointerDownRef.current = false;
        });
      }
      function handleFocusIn(event) {
        const target = getTarget(event);
        if (isTabbable(target)) {
          lastFocusedTabbableRef.current = target;
        }
      }
      function handleFocusOutside(event) {
        const relatedTarget = event.relatedTarget;
        const currentTarget = event.currentTarget;
        const target = getTarget(event);
        if (modal && relatedTarget == null && target != null && contains(floating, target)) {
          addPreviouslyFocusedElement(target);
        }
        queueMicrotask(() => {
          const nodeId = getNodeId();
          const triggers = store.context.triggerElements;
          const insideElements = getResolvedInsideElements();
          const isRelatedFocusGuard = relatedTarget?.hasAttribute(createAttribute("focus-guard")) && [beforeGuardRef.current, afterGuardRef.current, portalContext?.beforeInsideRef.current, portalContext?.afterInsideRef.current, portalContext?.beforeOutsideRef.current, portalContext?.afterOutsideRef.current, resolveRef(previousFocusableElement), resolveRef(nextFocusableElement)].includes(relatedTarget);
          const movedToUnrelatedNode = !(contains(domReference, relatedTarget) || contains(floating, relatedTarget) || contains(relatedTarget, floating) || contains(portalContext?.portalNode, relatedTarget) || insideElements.some((element) => element === relatedTarget || contains(element, relatedTarget)) || triggers.hasMatchingElement((trigger) => contains(trigger, relatedTarget)) || isRelatedFocusGuard || tree && (getNodeChildren(tree.nodesRef.current, nodeId).find((node) => contains(node.context?.elements.floating, relatedTarget) || contains(node.context?.elements.domReference, relatedTarget)) || getNodeAncestors(tree.nodesRef.current, nodeId).find((node) => [node.context?.elements.floating, getFloatingFocusElement(node.context?.elements.floating)].includes(relatedTarget) || node.context?.elements.domReference === relatedTarget)));
          if (currentTarget === domReference && floatingFocusElement) {
            handleTabIndex(floatingFocusElement);
          }
          if (restoreFocus && currentTarget !== domReference && !isElementVisible(target) && activeElement(doc) === doc.body) {
            if (isHTMLElement(floatingFocusElement)) {
              floatingFocusElement.focus();
              if (restoreFocus === "popup") {
                restoreFocusFrame.request(() => {
                  floatingFocusElement.focus();
                });
                return;
              }
            }
            const tabbableContent = getTabbableContent();
            const prevTabbable = lastFocusedTabbableRef.current;
            const nodeToFocus = (prevTabbable && tabbableContent.includes(prevTabbable) ? prevTabbable : null) || tabbableContent[tabbableContent.length - 1] || floatingFocusElement;
            if (isHTMLElement(nodeToFocus)) {
              nodeToFocus.focus();
            }
          }
          if (dataRef.current.insideReactTree) {
            dataRef.current.insideReactTree = false;
            return;
          }
          if ((isUntrappedTypeableCombobox ? true : !modal) && relatedTarget && movedToUnrelatedNode && !isPointerDownRef.current && // Fix React 18 Strict Mode returnFocus due to double rendering.
          // For an "untrapped" typeable combobox (input role=combobox with
          // initialFocus=false), re-opening the popup and tabbing out should still close it even
          // when the previously focused element (e.g. the next tabbable outside the popup) is
          // focused again. Otherwise, the popup remains open on the second Tab sequence:
          // click input -> Tab (closes) -> click input -> Tab.
          // Allow closing when `isUntrappedTypeableCombobox` regardless of the previously focused element.
          (isUntrappedTypeableCombobox || relatedTarget !== getPreviouslyFocusedElement())) {
            preventReturnFocusRef.current = true;
            store.setOpen(false, createChangeEventDetails(reason_parts_exports.focusOut, event));
          }
        });
      }
      function markInsideReactTree() {
        if (pointerDownOutsideRef.current) {
          return;
        }
        dataRef.current.insideReactTree = true;
        blurTimeout.start(0, () => {
          dataRef.current.insideReactTree = false;
        });
      }
      const domReferenceElement = isHTMLElement(domReference) ? domReference : null;
      if (!floating && !domReferenceElement) {
        return void 0;
      }
      return mergeCleanups(domReferenceElement && addEventListener(domReferenceElement, "focusout", handleFocusOutside), domReferenceElement && addEventListener(domReferenceElement, "pointerdown", handlePointerDown), floating && addEventListener(floating, "focusin", handleFocusIn), floating && addEventListener(floating, "focusout", handleFocusOutside), floating && portalContext && addEventListener(floating, "focusout", markInsideReactTree, true));
    }, [disabled2, domReference, floating, floatingFocusElement, modal, tree, portalContext, store, closeOnFocusOut, restoreFocus, getTabbableContent, isUntrappedTypeableCombobox, getNodeId, dataRef, blurTimeout, pointerDownTimeout, restoreFocusFrame, nextFocusableElement, previousFocusableElement, getResolvedInsideElements]);
    React22.useEffect(() => {
      if (disabled2 || !floating || !open2) {
        return void 0;
      }
      const portalNodes = Array.from(portalContext?.portalNode?.querySelectorAll(`[${createAttribute("portal")}]`) || []);
      const ancestors = tree ? getNodeAncestors(tree.nodesRef.current, getNodeId()) : [];
      const rootAncestorComboboxDomReference = ancestors.find((node) => isTypeableCombobox(node.context?.elements.domReference || null))?.context?.elements.domReference;
      const controlInsideElements = [floating, ...portalNodes, beforeGuardRef.current, afterGuardRef.current, portalContext?.beforeOutsideRef.current, portalContext?.afterOutsideRef.current, ...getResolvedInsideElements()];
      const insideElements = [...controlInsideElements, rootAncestorComboboxDomReference, resolveRef(previousFocusableElement), resolveRef(nextFocusableElement), isUntrappedTypeableCombobox ? domReference : null].filter((x) => x != null);
      const ariaHiddenCleanup = markOthers(insideElements, {
        ariaHidden: modal || isUntrappedTypeableCombobox,
        mark: false
      });
      const markerInsideElements = [floating, ...portalNodes].filter((x) => x != null);
      const markerCleanup = markOthers(markerInsideElements);
      return () => {
        markerCleanup();
        ariaHiddenCleanup();
      };
    }, [open2, disabled2, domReference, floating, modal, portalContext, isUntrappedTypeableCombobox, tree, getNodeId, nextFocusableElement, previousFocusableElement, getResolvedInsideElements]);
    useIsoLayoutEffect(() => {
      if (!open2 || disabled2 || !isHTMLElement(floatingFocusElement)) {
        return;
      }
      closeTypeRef.current = "";
      lastInteractionTypeRef.current = "";
      const doc = ownerDocument(floatingFocusElement);
      const previouslyFocusedElement = activeElement(doc);
      queueMicrotask(() => {
        const initialFocusValueOrFn = initialFocusRef.current;
        const resolvedInitialFocus = typeof initialFocusValueOrFn === "function" ? initialFocusValueOrFn(openInteractionTypeRef.current || "") : initialFocusValueOrFn;
        if (resolvedInitialFocus === void 0 || resolvedInitialFocus === false) {
          return;
        }
        const focusAlreadyInsideFloatingEl = contains(floatingFocusElement, previouslyFocusedElement);
        if (focusAlreadyInsideFloatingEl) {
          return;
        }
        let focusableElements = null;
        const getDefaultFocusElement = () => {
          if (focusableElements == null) {
            focusableElements = getTabbableContent(floatingFocusElement);
          }
          return focusableElements[0] || floatingFocusElement;
        };
        let elToFocus;
        if (resolvedInitialFocus === true || resolvedInitialFocus === null) {
          elToFocus = getDefaultFocusElement();
        } else {
          elToFocus = resolveRef(resolvedInitialFocus);
        }
        elToFocus = elToFocus || getDefaultFocusElement();
        const hadFocusInside = contains(floatingFocusElement, activeElement(doc));
        const openEvent = dataRef.current.openEvent;
        const openedByVirtualPress = openEvent?.type === "mousedown" && isVirtualClick(openEvent);
        void enqueueFocus(elToFocus, {
          sync: openedByVirtualPress,
          preventScroll: elToFocus === floatingFocusElement,
          shouldFocus() {
            if (!openRef.current) {
              return false;
            }
            if (hadFocusInside) {
              return true;
            }
            const currentActiveElement = activeElement(doc);
            const focusMovedInside = currentActiveElement !== elToFocus && contains(floatingFocusElement, currentActiveElement);
            return !focusMovedInside;
          }
        });
      });
    }, [disabled2, open2, floatingFocusElement, getTabbableContent, initialFocusRef, openInteractionTypeRef, openRef, dataRef]);
    const pendingReturnFocusRef = React22.useRef(null);
    useIsoLayoutEffect(() => {
      if (disabled2 || !floatingFocusElement) {
        pendingReturnFocusRef.current = null;
        return void 0;
      }
      if (pendingReturnFocusRef.current) {
        pendingReturnFocusRef.current.cancelled = true;
        pendingReturnFocusRef.current = null;
      }
      const doc = ownerDocument(floatingFocusElement);
      const elementFocusedBeforeOpen = activeElement(doc);
      const preferPreviousFocus = openInteractionTypeRef.current == null;
      addPreviouslyFocusedElement(elementFocusedBeforeOpen);
      function onOpenChangeLocal(details) {
        if (!details.open) {
          closeTypeRef.current = getEventType(details.nativeEvent, lastInteractionTypeRef.current);
        }
        if (details.reason === reason_parts_exports.focusOut && details.triggerElement?.hasAttribute(createAttribute("focus-guard")) || details.reason === reason_parts_exports.triggerHover && details.nativeEvent.type === "mouseleave") {
          preventReturnFocusRef.current = true;
        }
        if (details.reason !== reason_parts_exports.outsidePress) {
          return;
        }
        if (details.nested) {
          preventReturnFocusRef.current = false;
        } else if (isVirtualClick(details.nativeEvent) || isVirtualPointerEvent(details.nativeEvent)) {
          preventReturnFocusRef.current = false;
        } else {
          let isPreventScrollSupported = false;
          ownerDocument(floatingFocusElement).createElement("div").focus({
            get preventScroll() {
              isPreventScrollSupported = true;
              return false;
            }
          });
          if (isPreventScrollSupported) {
            preventReturnFocusRef.current = false;
          } else {
            preventReturnFocusRef.current = true;
          }
        }
      }
      events.on("openchange", onOpenChangeLocal);
      function getReturnElement(closeType) {
        const returnFocusValueOrFn = returnFocusRef.current;
        let resolvedReturnFocusValue = typeof returnFocusValueOrFn === "function" ? returnFocusValueOrFn(closeType) : returnFocusValueOrFn;
        if (resolvedReturnFocusValue === void 0 || resolvedReturnFocusValue === false) {
          return null;
        }
        if (resolvedReturnFocusValue === null) {
          resolvedReturnFocusValue = true;
        }
        const referenceReturnElement = domReference?.isConnected ? domReference : null;
        const previousReturnElement = elementFocusedBeforeOpen?.isConnected && getNodeName(elementFocusedBeforeOpen) !== "body" ? elementFocusedBeforeOpen : null;
        let defaultReturnElement = preferPreviousFocus ? previousReturnElement || referenceReturnElement : referenceReturnElement || previousReturnElement;
        if (!defaultReturnElement) {
          defaultReturnElement = getPreviouslyFocusedElement() || null;
        }
        if (typeof resolvedReturnFocusValue === "boolean") {
          return defaultReturnElement;
        }
        return resolveRef(resolvedReturnFocusValue) || defaultReturnElement || null;
      }
      return () => {
        events.off("openchange", onOpenChangeLocal);
        const activeEl = activeElement(doc);
        const insideElements = getResolvedInsideElements();
        const isFocusInsideFloatingTree = contains(floating, activeEl) || insideElements.some((element) => element === activeEl || contains(element, activeEl)) || tree && getNodeChildren(tree.nodesRef.current, getNodeId(), false).some((node) => contains(node.context?.elements.floating, activeEl));
        const returnFocusValueOrFn = returnFocusRef.current;
        const closeType = closeTypeRef.current;
        const returnElement = getReturnElement(closeType);
        const job = {
          cancelled: false
        };
        pendingReturnFocusRef.current = job;
        queueMicrotask(() => {
          if (pendingReturnFocusRef.current === job) {
            pendingReturnFocusRef.current = null;
          }
          const tabbableReturnElement = getFirstTabbableElement(returnElement);
          const hasExplicitReturnFocus = (
            // eslint-disable-next-line react-hooks/exhaustive-deps
            explicitReturnFocusRef.current ?? typeof returnFocusValueOrFn !== "boolean"
          );
          if (!job.cancelled && returnFocusValueOrFn && !preventReturnFocusRef.current && isHTMLElement(tabbableReturnElement) && // If the focus moved somewhere else after mount, avoid returning focus
          // since it likely entered a different element which should be
          // respected: https://github.com/floating-ui/floating-ui/issues/2607
          (!hasExplicitReturnFocus && tabbableReturnElement !== activeEl && activeEl !== doc.body ? isFocusInsideFloatingTree : true)) {
            const focusOptions = {
              preventScroll: true
            };
            if (closeType === "keyboard") {
              focusOptions.focusVisible = true;
            }
            tabbableReturnElement.focus(focusOptions);
          }
          preventReturnFocusRef.current = false;
        });
      };
    }, [disabled2, floating, floatingFocusElement, returnFocusRef, explicitReturnFocusRef, openInteractionTypeRef, events, tree, domReference, getNodeId, getResolvedInsideElements]);
    useIsoLayoutEffect(() => {
      if (!parts_exports.engine.webkit || open2 || !floating) {
        return;
      }
      const activeEl = activeElement(ownerDocument(floating));
      if (!isHTMLElement(activeEl) || !isTypeableElement(activeEl)) {
        return;
      }
      if (contains(floating, activeEl)) {
        activeEl.blur();
      }
    }, [open2, floating]);
    useIsoLayoutEffect(() => {
      if (disabled2 || !portalContext) {
        return void 0;
      }
      portalContext.setFocusManagerState({
        modal,
        closeOnFocusOut,
        open: open2,
        onOpenChange: store.setOpen,
        domReference
      });
      return () => {
        portalContext.setFocusManagerState(null);
      };
    }, [disabled2, portalContext, modal, open2, store, closeOnFocusOut, domReference]);
    useIsoLayoutEffect(() => {
      if (disabled2 || !floatingFocusElement) {
        return void 0;
      }
      handleTabIndex(floatingFocusElement);
      return () => {
        queueMicrotask(clearDisconnectedPreviouslyFocusedElements);
      };
    }, [disabled2, floatingFocusElement]);
    const shouldRenderGuards = !disabled2 && (modal ? !isUntrappedTypeableCombobox : true) && (isInsidePortal || modal);
    return /* @__PURE__ */ (0, import_jsx_runtime26.jsxs)(React22.Fragment, {
      children: [shouldRenderGuards && /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(FocusGuard, {
        "data-type": "inside",
        ref: mergedBeforeGuardRef,
        onFocus: (event) => {
          if (modal) {
            const els = getTabbableContent();
            void enqueueFocus(els[els.length - 1]);
          } else if (portalContext?.portalNode) {
            preventReturnFocusRef.current = false;
            if (isOutsideEvent(event, portalContext.portalNode)) {
              const nextTabbable = getNextTabbable(domReference);
              nextTabbable?.focus();
            } else {
              resolveRef(previousFocusableElement ?? portalContext.beforeOutsideRef)?.focus();
            }
          }
        }
      }), children, shouldRenderGuards && /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(FocusGuard, {
        "data-type": "inside",
        ref: mergedAfterGuardRef,
        onFocus: (event) => {
          if (modal) {
            void enqueueFocus(getTabbableContent()[0]);
          } else if (portalContext?.portalNode) {
            if (closeOnFocusOut) {
              preventReturnFocusRef.current = true;
            }
            if (isOutsideEvent(event, portalContext.portalNode)) {
              const prevTabbable = getPreviousTabbable(domReference);
              prevTabbable?.focus();
            } else {
              resolveRef(nextFocusableElement ?? portalContext.afterOutsideRef)?.focus();
            }
          }
        }
      })]
    });
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/hooks/useClick.mjs
  var React23 = __toESM(require_react(), 1);
  function useClick(store, props = {}) {
    const {
      enabled = true,
      event: eventOption = "click",
      toggle = true,
      ignoreMouse = false,
      stickIfOpen = true,
      touchOpenDelay = 0,
      reason = reason_parts_exports.triggerPress
    } = props;
    const dataRef = store.context.dataRef;
    const pointerTypeRef = React23.useRef(void 0);
    const frame = useAnimationFrame();
    const touchOpenTimeout = useTimeout();
    const reference = React23.useMemo(() => {
      function setOpenWithTouchDelay(nextOpen, nativeEvent, target, pointerType) {
        const details = createChangeEventDetails(reason, nativeEvent, target);
        if (nextOpen && pointerType === "touch" && touchOpenDelay > 0) {
          touchOpenTimeout.start(touchOpenDelay, () => {
            store.setOpen(true, details);
          });
        } else {
          store.setOpen(nextOpen, details);
        }
      }
      function getNextOpen(open2, currentTarget, isClickLikeOpenEvent) {
        const openEvent = dataRef.current.openEvent;
        const hasClickedOnInactiveTrigger = store.select("domReferenceElement") !== currentTarget;
        if (open2 && hasClickedOnInactiveTrigger) {
          return true;
        }
        if (!open2) {
          return true;
        }
        if (!toggle) {
          return true;
        }
        if (openEvent && (typeof stickIfOpen === "function" ? stickIfOpen() : stickIfOpen)) {
          return !isClickLikeOpenEvent(openEvent.type);
        }
        return false;
      }
      return {
        onPointerDown(event) {
          pointerTypeRef.current = isMouseLikePointerType(event.pointerType, true) && isVirtualPointerEvent(event.nativeEvent) ? "virtual" : event.pointerType;
        },
        onMouseDown(event) {
          const pointerType = pointerTypeRef.current;
          const nativeEvent = event.nativeEvent;
          const open2 = store.select("open");
          if (event.button !== 0 || eventOption === "click" || isMouseLikePointerType(pointerType, true) && ignoreMouse) {
            return;
          }
          const nextOpen = getNextOpen(open2, event.currentTarget, (openEventType) => openEventType === "click" || openEventType === "mousedown");
          const target = getTarget(nativeEvent);
          const isTypeable = isTypeableElement(target);
          if (isTypeable || pointerType === "virtual") {
            setOpenWithTouchDelay(nextOpen, nativeEvent, isTypeable ? target : event.currentTarget, pointerType);
            return;
          }
          const eventCurrentTarget = event.currentTarget;
          frame.request(() => {
            setOpenWithTouchDelay(nextOpen, nativeEvent, eventCurrentTarget, pointerType);
          });
        },
        onClick(event) {
          if (eventOption === "mousedown-only") {
            return;
          }
          const pointerType = pointerTypeRef.current;
          if (eventOption === "mousedown" && pointerType) {
            pointerTypeRef.current = void 0;
            return;
          }
          if (isMouseLikePointerType(pointerType, true) && ignoreMouse) {
            return;
          }
          const open2 = store.select("open");
          const nextOpen = getNextOpen(open2, event.currentTarget, (openEventType) => openEventType === "click" || openEventType === "mousedown" || openEventType === "keydown" || openEventType === "keyup");
          setOpenWithTouchDelay(nextOpen, event.nativeEvent, event.currentTarget, pointerType);
        },
        onKeyDown() {
          pointerTypeRef.current = void 0;
        }
      };
    }, [dataRef, eventOption, ignoreMouse, reason, store, stickIfOpen, toggle, frame, touchOpenTimeout, touchOpenDelay]);
    return React23.useMemo(() => enabled ? {
      reference
    } : EMPTY_OBJECT, [enabled, reference]);
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/hooks/useDismiss.mjs
  var React24 = __toESM(require_react(), 1);
  function alwaysFalse() {
    return false;
  }
  function normalizeProp(normalizable) {
    return {
      escapeKey: typeof normalizable === "boolean" ? normalizable : normalizable?.escapeKey ?? false,
      outsidePress: typeof normalizable === "boolean" ? normalizable : normalizable?.outsidePress ?? true
    };
  }
  function useDismiss(store, props = {}) {
    const {
      enabled = true,
      escapeKey: escapeKey2 = true,
      outsidePress: outsidePressProp = true,
      outsidePressEvent = "sloppy",
      referencePress = alwaysFalse,
      bubbles,
      externalTree
    } = props;
    const open2 = store.useState("open");
    const floatingElement = store.useState("floatingElement");
    const {
      dataRef,
      events
    } = store.context;
    const tree = useFloatingTree(externalTree);
    const outsidePressFn = useStableCallback(typeof outsidePressProp === "function" ? outsidePressProp : () => false);
    const outsidePress2 = typeof outsidePressProp === "function" ? outsidePressFn : outsidePressProp;
    const outsidePressEnabled = outsidePress2 !== false;
    const getOutsidePressEventProp = useStableCallback(() => outsidePressEvent);
    const {
      escapeKey: escapeKeyBubbles,
      outsidePress: outsidePressBubbles
    } = normalizeProp(bubbles);
    const pressStartedInsideRef = React24.useRef(false);
    const pressStartPreventedRef = React24.useRef(false);
    const suppressNextOutsideClickRef = React24.useRef(false);
    const sawPressWhileOpenRef = React24.useRef(false);
    const isComposingRef = React24.useRef(false);
    const currentPointerTypeRef = React24.useRef("");
    const touchStateRef = React24.useRef(null);
    const cancelDismissOnEndTimeout = useTimeout();
    const clearInsideReactTreeTimeout = useTimeout();
    const compositionTimeout = useTimeout();
    const clearInsideReactTree = useStableCallback(() => {
      clearInsideReactTreeTimeout.clear();
      dataRef.current.insideReactTree = false;
    });
    const hasBlockingChild = useStableCallback((bubbleKey) => {
      const nodeId = dataRef.current.floatingContext?.nodeId;
      const children = tree ? getNodeChildren(tree.nodesRef.current, nodeId) : [];
      return children.some((child) => child.context?.open && !child.context.dataRef.current[bubbleKey]);
    });
    const isEventWithinOwnElements = useStableCallback((event) => {
      return isEventTargetWithin(event, store.select("floatingElement")) || isEventTargetWithin(event, store.select("domReferenceElement"));
    });
    const closeOnReferencePress = useStableCallback((event) => {
      if (!referencePress()) {
        return;
      }
      store.setOpen(false, createChangeEventDetails(reason_parts_exports.triggerPress, event.nativeEvent));
    });
    const closeOnEscapeKeyDown = useStableCallback((event) => {
      if (!open2 || !enabled || !escapeKey2 || event.key !== "Escape") {
        return;
      }
      const native = isReactEvent(event) ? event.nativeEvent : event;
      if (isComposingRef.current || native.isComposing) {
        return;
      }
      if (!escapeKeyBubbles && hasBlockingChild("__escapeKeyBubbles")) {
        return;
      }
      const eventDetails = createChangeEventDetails(reason_parts_exports.escapeKey, native);
      store.setOpen(false, eventDetails);
      if (!eventDetails.isCanceled) {
        event.preventDefault();
      }
      if (!escapeKeyBubbles && !eventDetails.isPropagationAllowed) {
        event.stopPropagation();
      }
    });
    const markInsideReactTree = useStableCallback(() => {
      dataRef.current.insideReactTree = true;
      clearInsideReactTreeTimeout.start(0, clearInsideReactTree);
    });
    const markPressStartedInsideReactTree = useStableCallback((event) => {
      if (!open2 || !enabled || event.button !== 0) {
        return;
      }
      const target = getTarget(event.nativeEvent);
      if (!contains(store.select("floatingElement"), target)) {
        return;
      }
      if (!pressStartedInsideRef.current) {
        pressStartedInsideRef.current = true;
        pressStartPreventedRef.current = false;
      }
    });
    const markInsidePressStartPrevented = useStableCallback((event) => {
      if (!open2 || !enabled) {
        return;
      }
      if (!(event.defaultPrevented || event.nativeEvent.defaultPrevented)) {
        return;
      }
      if (pressStartedInsideRef.current) {
        pressStartPreventedRef.current = true;
      }
    });
    React24.useEffect(() => {
      function handleOpenChange(details) {
        if (!details.open) {
          sawPressWhileOpenRef.current = false;
        }
      }
      events.on("openchange", handleOpenChange);
      return () => {
        events.off("openchange", handleOpenChange);
      };
    }, [events]);
    React24.useEffect(() => clearInsideReactTree, [clearInsideReactTree]);
    React24.useEffect(() => {
      if (!open2 || !enabled) {
        if (!open2) {
          sawPressWhileOpenRef.current = false;
          isComposingRef.current = false;
          currentPointerTypeRef.current = "";
          touchStateRef.current = null;
        }
        return clearInsideReactTree;
      }
      dataRef.current.__escapeKeyBubbles = escapeKeyBubbles;
      dataRef.current.__outsidePressBubbles = outsidePressBubbles;
      const preventedPressSuppressionTimeout = new Timeout();
      const doc = ownerDocument(floatingElement);
      function handleCompositionStart() {
        compositionTimeout.clear();
        isComposingRef.current = true;
      }
      function handleCompositionEnd() {
        isComposingRef.current = true;
        compositionTimeout.start(
          // 0ms or 1ms don't work in Safari. 5ms appears to consistently work.
          // Only apply to WebKit for the test to remain 0ms.
          parts_exports.engine.webkit ? 5 : 0,
          () => {
            isComposingRef.current = false;
          }
        );
      }
      function suppressImmediateOutsideClickAfterPreventedStart() {
        suppressNextOutsideClickRef.current = true;
        preventedPressSuppressionTimeout.start(0, () => {
          suppressNextOutsideClickRef.current = false;
        });
      }
      function resetPressStartState() {
        pressStartedInsideRef.current = false;
        pressStartPreventedRef.current = false;
      }
      function getOutsidePressEvent() {
        const type = currentPointerTypeRef.current;
        const computedType = type === "pen" || !type ? "mouse" : type;
        const outsidePressEventValue = getOutsidePressEventProp();
        const resolved = typeof outsidePressEventValue === "function" ? outsidePressEventValue() : outsidePressEventValue;
        if (typeof resolved === "string") {
          return resolved;
        }
        return resolved[computedType];
      }
      function shouldIgnoreEvent(event) {
        const computedOutsidePressEvent = getOutsidePressEvent();
        return computedOutsidePressEvent === "intentional" && event.type !== "click" || computedOutsidePressEvent === "sloppy" && event.type === "click";
      }
      function isEventWithinFloatingTree(event) {
        const nodeId = dataRef.current.floatingContext?.nodeId;
        const targetIsInsideChildren = tree && getNodeChildren(tree.nodesRef.current, nodeId).some((node) => isEventTargetWithin(event, node.context?.elements.floating));
        return isEventWithinOwnElements(event) || targetIsInsideChildren;
      }
      function closeOnPressOutside(event) {
        if (shouldIgnoreEvent(event)) {
          if (event.type !== "click" && !isEventWithinOwnElements(event)) {
            preventedPressSuppressionTimeout.clear();
            suppressNextOutsideClickRef.current = false;
          }
          clearInsideReactTree();
          return;
        }
        if (dataRef.current.insideReactTree) {
          clearInsideReactTree();
          return;
        }
        const target = getTarget(event);
        const inertSelector = `[${createAttribute("inert")}]`;
        const targetRoot = isElement(target) ? target.getRootNode() : null;
        const markers = Array.from((isShadowRoot(targetRoot) ? targetRoot : ownerDocument(store.select("floatingElement"))).querySelectorAll(inertSelector));
        const triggers = store.context.triggerElements;
        if (target && (triggers.hasElement(target) || triggers.hasMatchingElement((trigger) => contains(trigger, target)))) {
          return;
        }
        let targetRootAncestor = isElement(target) ? target : null;
        while (targetRootAncestor && !isLastTraversableNode(targetRootAncestor)) {
          const nextParent = getParentNode(targetRootAncestor);
          if (isLastTraversableNode(nextParent) || !isElement(nextParent)) {
            break;
          }
          targetRootAncestor = nextParent;
        }
        if (markers.length && isElement(target) && !isRootElement(target) && // Clicked on a direct ancestor (e.g. FloatingOverlay).
        !contains(target, store.select("floatingElement")) && // If the target root element contains none of the markers, then the
        // element was injected after the floating element rendered.
        markers.every((marker) => !contains(targetRootAncestor, marker))) {
          return;
        }
        if (isHTMLElement(target) && !("touches" in event)) {
          const lastTraversableNode = isLastTraversableNode(target);
          const style = getComputedStyle2(target);
          const scrollRe = /auto|scroll/;
          const isScrollableX = lastTraversableNode || scrollRe.test(style.overflowX);
          const isScrollableY = lastTraversableNode || scrollRe.test(style.overflowY);
          const canScrollX = isScrollableX && target.clientWidth > 0 && target.scrollWidth > target.clientWidth;
          const canScrollY = isScrollableY && target.clientHeight > 0 && target.scrollHeight > target.clientHeight;
          const isRTL3 = style.direction === "rtl";
          const pressedVerticalScrollbar = canScrollY && (isRTL3 ? event.offsetX <= target.offsetWidth - target.clientWidth : event.offsetX > target.clientWidth);
          const pressedHorizontalScrollbar = canScrollX && event.offsetY > target.clientHeight;
          if (pressedVerticalScrollbar || pressedHorizontalScrollbar) {
            return;
          }
        }
        if (isEventWithinFloatingTree(event)) {
          return;
        }
        if (getOutsidePressEvent() === "intentional") {
          if (event.detail !== 0 && !isVirtualClick(event) && !sawPressWhileOpenRef.current) {
            return;
          }
          if (suppressNextOutsideClickRef.current) {
            preventedPressSuppressionTimeout.clear();
            suppressNextOutsideClickRef.current = false;
            return;
          }
        }
        if (typeof outsidePress2 === "function" && !outsidePress2(event)) {
          return;
        }
        if (hasBlockingChild("__outsidePressBubbles")) {
          return;
        }
        store.setOpen(false, createChangeEventDetails(reason_parts_exports.outsidePress, event));
        clearInsideReactTree();
      }
      function handlePointerDown(event) {
        if (getOutsidePressEvent() !== "sloppy" || event.pointerType === "touch" || !store.select("open") || !enabled || isEventWithinOwnElements(event)) {
          return;
        }
        closeOnPressOutside(event);
      }
      function handleTouchStart(event) {
        if (getOutsidePressEvent() !== "sloppy" || !store.select("open") || !enabled || isEventWithinOwnElements(event)) {
          return;
        }
        const touch = event.touches[0];
        if (touch) {
          touchStateRef.current = {
            startTime: Date.now(),
            startX: touch.clientX,
            startY: touch.clientY,
            dismissOnTouchEnd: false,
            dismissOnMouseDown: true
          };
          cancelDismissOnEndTimeout.start(1e3, () => {
            if (touchStateRef.current) {
              touchStateRef.current.dismissOnTouchEnd = false;
              touchStateRef.current.dismissOnMouseDown = false;
            }
          });
        }
      }
      function addTargetEventListenerOnce(event, listener) {
        const target = getTarget(event);
        if (!target) {
          return;
        }
        const unsubscribe2 = addEventListener(target, event.type, () => {
          listener(event);
          unsubscribe2();
        });
      }
      function handleTouchStartCapture(event) {
        currentPointerTypeRef.current = "touch";
        addTargetEventListenerOnce(event, handleTouchStart);
      }
      function closeOnPressOutsideCapture(event) {
        cancelDismissOnEndTimeout.clear();
        if (event.type === "pointerdown") {
          if (event.button === 0) {
            sawPressWhileOpenRef.current = true;
          }
          currentPointerTypeRef.current = event.pointerType;
        }
        if (event.type === "mousedown" && touchStateRef.current && !touchStateRef.current.dismissOnMouseDown) {
          return;
        }
        addTargetEventListenerOnce(event, (targetEvent) => {
          if (targetEvent.type === "pointerdown") {
            handlePointerDown(targetEvent);
          } else {
            closeOnPressOutside(targetEvent);
          }
        });
      }
      function handlePressEndCapture(event) {
        if (event.type === "pointercancel") {
          sawPressWhileOpenRef.current = false;
        }
        if (!pressStartedInsideRef.current) {
          return;
        }
        const pressStartedInsideDefaultPrevented = pressStartPreventedRef.current;
        resetPressStartState();
        if (getOutsidePressEvent() !== "intentional") {
          return;
        }
        if (event.type === "pointercancel") {
          if (pressStartedInsideDefaultPrevented) {
            suppressImmediateOutsideClickAfterPreventedStart();
          }
          return;
        }
        if (isEventWithinFloatingTree(event)) {
          return;
        }
        if (pressStartedInsideDefaultPrevented) {
          suppressImmediateOutsideClickAfterPreventedStart();
          return;
        }
        if (typeof outsidePress2 === "function" && !outsidePress2(event)) {
          return;
        }
        preventedPressSuppressionTimeout.clear();
        suppressNextOutsideClickRef.current = true;
        clearInsideReactTree();
      }
      function handleTouchMove(event) {
        if (getOutsidePressEvent() !== "sloppy" || !touchStateRef.current || isEventWithinOwnElements(event)) {
          return;
        }
        const touch = event.touches[0];
        if (!touch) {
          return;
        }
        const deltaX = Math.abs(touch.clientX - touchStateRef.current.startX);
        const deltaY = Math.abs(touch.clientY - touchStateRef.current.startY);
        const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
        if (distance > 5) {
          touchStateRef.current.dismissOnTouchEnd = true;
        }
        if (distance > 10) {
          closeOnPressOutside(event);
          cancelDismissOnEndTimeout.clear();
          touchStateRef.current = null;
        }
      }
      function handleTouchMoveCapture(event) {
        addTargetEventListenerOnce(event, handleTouchMove);
      }
      function handleTouchEnd(event) {
        if (getOutsidePressEvent() !== "sloppy" || !touchStateRef.current || isEventWithinOwnElements(event)) {
          return;
        }
        if (touchStateRef.current.dismissOnTouchEnd) {
          closeOnPressOutside(event);
        }
        cancelDismissOnEndTimeout.clear();
        touchStateRef.current = null;
      }
      function handleTouchEndCapture(event) {
        addTargetEventListenerOnce(event, handleTouchEnd);
      }
      const unsubscribe = mergeCleanups(escapeKey2 && mergeCleanups(addEventListener(doc, "keydown", closeOnEscapeKeyDown), addEventListener(doc, "compositionstart", handleCompositionStart), addEventListener(doc, "compositionend", handleCompositionEnd)), outsidePressEnabled && mergeCleanups(addEventListener(doc, "click", closeOnPressOutsideCapture, true), addEventListener(doc, "pointerdown", closeOnPressOutsideCapture, true), addEventListener(doc, "pointerup", handlePressEndCapture, true), addEventListener(doc, "pointercancel", handlePressEndCapture, true), addEventListener(doc, "mousedown", closeOnPressOutsideCapture, true), addEventListener(doc, "mouseup", handlePressEndCapture, true), addEventListener(doc, "touchstart", handleTouchStartCapture, {
        capture: true,
        passive: true
      }), addEventListener(doc, "touchmove", handleTouchMoveCapture, {
        capture: true,
        passive: true
      }), addEventListener(doc, "touchend", handleTouchEndCapture, {
        capture: true,
        passive: true
      })));
      return () => {
        unsubscribe();
        preventedPressSuppressionTimeout.clear();
        resetPressStartState();
        suppressNextOutsideClickRef.current = false;
      };
    }, [dataRef, floatingElement, escapeKey2, outsidePressEnabled, outsidePress2, open2, enabled, escapeKeyBubbles, outsidePressBubbles, closeOnEscapeKeyDown, clearInsideReactTree, getOutsidePressEventProp, hasBlockingChild, isEventWithinOwnElements, tree, store, cancelDismissOnEndTimeout, compositionTimeout]);
    const reference = React24.useMemo(() => ({
      onKeyDown: closeOnEscapeKeyDown,
      onPointerDown: closeOnReferencePress,
      onClick: closeOnReferencePress
    }), [closeOnEscapeKeyDown, closeOnReferencePress]);
    const floating = React24.useMemo(() => ({
      onKeyDown: closeOnEscapeKeyDown,
      // `onMouseDown` may be blocked if `event.preventDefault()` is called in
      // `onPointerDown`, such as with <NumberField.ScrubArea>.
      // See https://github.com/mui/base-ui/pull/3379
      onPointerDown: markInsidePressStartPrevented,
      onMouseDown: markInsidePressStartPrevented,
      onClickCapture: markInsideReactTree,
      onMouseDownCapture(event) {
        markInsideReactTree();
        markPressStartedInsideReactTree(event);
      },
      onPointerDownCapture(event) {
        markInsideReactTree();
        markPressStartedInsideReactTree(event);
      },
      onMouseUpCapture: markInsideReactTree,
      onTouchEndCapture: markInsideReactTree,
      onTouchMoveCapture: markInsideReactTree
    }), [closeOnEscapeKeyDown, markInsideReactTree, markPressStartedInsideReactTree, markInsidePressStartPrevented]);
    return React24.useMemo(() => enabled ? {
      reference,
      floating,
      trigger: reference
    } : {}, [enabled, reference, floating]);
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/popups/popupStoreUtils.mjs
  var React26 = __toESM(require_react(), 1);
  var ReactDOM3 = __toESM(require_react_dom(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useUnmountAfterClose.mjs
  var React25 = __toESM(require_react(), 1);
  function useUnmountAfterClose(parameters) {
    const {
      open: open2,
      ref,
      preventUnmountOnClose,
      setPreventUnmountOnClose: setPreventUnmountOnCloseParam,
      onUnmount,
      animateInitialOpen
    } = parameters;
    const setPreventUnmountOnClose = useStableCallback(setPreventUnmountOnCloseParam);
    const {
      mounted,
      setMounted,
      transitionStatus
    } = useTransitionStatus(open2, false, false, animateInitialOpen);
    const preventUnmountingOnClose = open2 ? false : preventUnmountOnClose;
    useIsoLayoutEffect(() => {
      if (open2) {
        setPreventUnmountOnClose(false);
      }
    }, [open2, setPreventUnmountOnClose]);
    const mountedRef = React25.useRef(mounted);
    const pendingUnmountRef = React25.useRef(false);
    const rerender = useForcedRerendering();
    const unmount = () => {
      mountedRef.current = false;
      setMounted(false);
      onUnmount();
    };
    useIsoLayoutEffect(() => {
      mountedRef.current = mounted;
      if (pendingUnmountRef.current) {
        pendingUnmountRef.current = false;
        if (!open2 && mounted) {
          unmount();
        }
      }
    });
    const forceUnmount = useStableCallback(() => {
      if (!mountedRef.current) {
        return;
      }
      if (open2) {
        pendingUnmountRef.current = true;
        rerender();
        return;
      }
      unmount();
    });
    useOpenChangeComplete({
      enabled: mounted && !open2 && !preventUnmountingOnClose,
      open: open2,
      ref,
      onComplete() {
        if (!open2) {
          forceUnmount();
        }
      }
    });
    return {
      mounted,
      transitionStatus,
      preventUnmountingOnClose,
      forceUnmount
    };
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/popups/popupStoreUtils.mjs
  var FOCUSABLE_POPUP_PROPS = {
    tabIndex: -1,
    [FOCUSABLE_ATTRIBUTE]: ""
  };
  function attachPreventUnmountOnClose(eventDetails) {
    let preventUnmountOnClose = false;
    eventDetails.preventUnmountOnClose = () => {
      preventUnmountOnClose = true;
    };
    return () => preventUnmountOnClose;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/popups/popupTriggerMap.mjs
  var devElementIdsByMap;
  function getDevElementIds(map) {
    devElementIdsByMap ??= /* @__PURE__ */ new WeakMap();
    let elementIds = devElementIdsByMap.get(map);
    if (!elementIds) {
      elementIds = /* @__PURE__ */ new WeakMap();
      devElementIdsByMap.set(map, elementIds);
    }
    return elementIds;
  }
  var PopupTriggerMap = class {
    idMap = /* @__PURE__ */ new Map();
    /**
     * Adds a trigger element with the given ID.
     *
     * Note: The provided element is assumed to not be registered under multiple IDs.
     */
    add(id, element) {
      if (true) {
        const elementIds = getDevElementIds(this);
        const existingId = elementIds.get(element);
        if (existingId !== void 0 && existingId !== id) {
          throw new Error("Base UI: A trigger element cannot be registered under multiple IDs in PopupTriggerMap.");
        }
        const previousElement = this.idMap.get(id);
        if (previousElement !== void 0 && previousElement !== element) {
          elementIds.delete(previousElement);
        }
        elementIds.set(element, id);
      }
      this.idMap.set(id, element);
    }
    /**
     * Removes the trigger element with the given ID.
     */
    delete(id) {
      if (true) {
        const element = this.idMap.get(id);
        if (element !== void 0) {
          devElementIdsByMap?.get(this)?.delete(element);
        }
      }
      this.idMap.delete(id);
    }
    /**
     * Whether the given element is registered as a trigger.
     */
    hasElement(element) {
      for (const registered of this.idMap.values()) {
        if (registered === element) {
          return true;
        }
      }
      return false;
    }
    /**
     * Whether there is a registered trigger element matching the given predicate.
     */
    hasMatchingElement(predicate) {
      for (const element of this.idMap.values()) {
        if (predicate(element)) {
          return true;
        }
      }
      return false;
    }
    /**
     * Returns the trigger element associated with the given ID, or undefined if no such element exists.
     */
    getById(id) {
      return this.idMap.get(id);
    }
    /**
     * Returns an iterable of all registered trigger entries, where each entry is a tuple of [id, element].
     */
    entries() {
      return this.idMap.entries();
    }
    /**
     * Returns an iterable of all registered trigger elements.
     */
    elements() {
      return this.idMap.values();
    }
    /**
     * Returns the number of registered trigger elements.
     */
    get size() {
      return this.idMap.size;
    }
  };

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/store/useStore.mjs
  var React28 = __toESM(require_react(), 1);
  var import_shim = __toESM(require_shim(), 1);
  var import_with_selector = __toESM(require_with_selector(), 1);

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/fastHooks.mjs
  var React27 = __toESM(require_react(), 1);
  var hooks = [];
  var currentInstance = void 0;
  function getInstance() {
    return currentInstance;
  }
  function register(hook) {
    hooks.push(hook);
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/store/useStore.mjs
  var canUseRawUseSyncExternalStore = isReactVersionAtLeast(19);
  var useStoreImplementation = canUseRawUseSyncExternalStore ? useStoreFast : useStoreLegacy;
  function useStore(store, selector, a1, a2, a3) {
    return useStoreImplementation(store, selector, a1, a2, a3);
  }
  function useStoreR19(store, selector, a1, a2, a3) {
    const getSelection = React28.useCallback(() => selector(store.getSnapshot(), a1, a2, a3), [store, selector, a1, a2, a3]);
    return (0, import_shim.useSyncExternalStore)(store.subscribe, getSelection, getSelection);
  }
  register({
    before(instance) {
      instance.syncIndex = 0;
      if (!instance.didInitialize) {
        instance.syncTick = 1;
        instance.syncHooks = [];
        instance.didChangeStore = true;
        instance.getSnapshot = () => {
          let didChange2 = false;
          for (let i = 0; i < instance.syncHooks.length; i += 1) {
            const hook = instance.syncHooks[i];
            const value = hook.selector(hook.store.state, hook.a1, hook.a2, hook.a3);
            if (!Object.is(hook.value, value)) {
              didChange2 = true;
              hook.value = value;
            }
          }
          if (didChange2) {
            instance.syncTick += 1;
          }
          return instance.syncTick;
        };
      }
    },
    after(instance) {
      if (instance.syncHooks.length > 0) {
        if (instance.didChangeStore) {
          instance.didChangeStore = false;
          instance.subscribe = (onStoreChange) => {
            const stores = /* @__PURE__ */ new Set();
            for (const hook of instance.syncHooks) {
              stores.add(hook.store);
            }
            const unsubscribes = [];
            for (const store of stores) {
              unsubscribes.push(store.subscribe(onStoreChange));
            }
            return () => {
              for (const unsubscribe of unsubscribes) {
                unsubscribe();
              }
            };
          };
        }
        (0, import_shim.useSyncExternalStore)(instance.subscribe, instance.getSnapshot, instance.getSnapshot);
      }
    }
  });
  function useStoreFast(store, selector, a1, a2, a3) {
    const instance = getInstance();
    if (!instance) {
      return useStoreR19(store, selector, a1, a2, a3);
    }
    const index3 = instance.syncIndex;
    instance.syncIndex += 1;
    let hook;
    if (!instance.didInitialize) {
      hook = {
        store,
        selector,
        a1,
        a2,
        a3,
        value: selector(store.getSnapshot(), a1, a2, a3)
      };
      instance.syncHooks.push(hook);
    } else {
      hook = instance.syncHooks[index3];
      if (hook.store !== store || hook.selector !== selector || !Object.is(hook.a1, a1) || !Object.is(hook.a2, a2) || !Object.is(hook.a3, a3)) {
        if (hook.store !== store) {
          instance.didChangeStore = true;
        }
        hook.store = store;
        hook.selector = selector;
        hook.a1 = a1;
        hook.a2 = a2;
        hook.a3 = a3;
        hook.value = selector(store.getSnapshot(), a1, a2, a3);
      }
    }
    return hook.value;
  }
  function useStoreLegacy(store, selector, a1, a2, a3) {
    return (0, import_with_selector.useSyncExternalStoreWithSelector)(store.subscribe, store.getSnapshot, store.getSnapshot, (state) => selector(state, a1, a2, a3));
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/store/Store.mjs
  var Store = class {
    /**
     * Creates a store with the given initial state, constructing the class it is called on.
     * Calling it on a generic base class (e.g. `ReactStore.create(...)`) constructs that
     * class but degrades the inferred instance type to `Store`; use `new` there instead.
     */
    static create(state) {
      return new this(state);
    }
    /**
     * The current state of the store.
     * This property is updated immediately when the state changes as a result of calling {@link setState}, {@link update}, or {@link set}.
     * To subscribe to state changes, use the {@link useState} method. The value returned by {@link useState} is updated after the component renders (similarly to React's useState).
     * The values can be used directly (to avoid subscribing to the store) in effects or event handlers.
     *
     * Do not modify properties in state directly. Instead, use the provided methods to ensure proper state management and listener notification.
     */
    // Internal state to handle recursive `setState()` calls
    constructor(state) {
      this.state = state;
      this.listeners = /* @__PURE__ */ new Set();
      this.updateTick = 0;
    }
    /**
     * Registers a listener that will be called whenever the store's state changes.
     *
     * @param fn The listener function to be called on state changes.
     * @returns A function to unsubscribe the listener.
     */
    subscribe = (fn) => {
      this.listeners.add(fn);
      return () => {
        this.listeners.delete(fn);
      };
    };
    /**
     * Returns the current state of the store.
     */
    getSnapshot = () => {
      return this.state;
    };
    /**
     * Updates the entire store's state and notifies all registered listeners.
     *
     * @param newState The new state to set for the store.
     */
    setState(newState) {
      if (this.state === newState) {
        return;
      }
      this.state = newState;
      this.updateTick += 1;
      const currentTick = this.updateTick;
      for (const listener of this.listeners) {
        if (currentTick !== this.updateTick) {
          return;
        }
        listener(newState);
      }
    }
    /**
     * Merges the provided changes into the current state and notifies listeners if there are changes.
     * Each value must match its state key. Pass an exact known subset rather than a broad
     * `Partial<State>`, which may contain `undefined` for required state fields.
     *
     * @param changes An object containing the changes to apply to the current state.
     */
    update(changes) {
      for (const key in changes) {
        if (!Object.is(this.state[key], changes[key])) {
          this.setState({
            ...this.state,
            ...changes
          });
          return;
        }
      }
    }
    /**
     * Sets a specific key in the store's state to a new value and notifies listeners if the value has changed.
     *
     * @param key The key in the store's state to update.
     * @param value The new value to set for the specified key.
     */
    set(key, value) {
      if (!Object.is(this.state[key], value)) {
        this.setState({
          ...this.state,
          [key]: value
        });
      }
    }
    /**
     * Gives the state a new reference and updates all registered listeners.
     */
    notifyAll() {
      const newState = {
        ...this.state
      };
      this.setState(newState);
    }
    use(selector, a1, a2, a3) {
      return useStore(this, selector, a1, a2, a3);
    }
  };

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/store/ReactStore.mjs
  var React29 = __toESM(require_react(), 1);
  var ReactStore = class extends Store {
    /**
     * Creates a new ReactStore instance.
     *
     * @param state Initial state of the store.
     * @param context Non-reactive context values.
     * @param selectors Optional selectors for use with `useState`.
     */
    constructor(state, context = {}, selectors3) {
      super(state);
      this.context = context;
      this.selectors = selectors3;
    }
    /**
     * Non-reactive values such as refs, callbacks, etc.
     */
    /**
     * Synchronizes a single external value into the store.
     *
     * Note that the while the value in `state` is updated immediately, the value returned
     * by `useState` is updated before the next render (similarly to React's `useState`).
     */
    useSyncedValue(key, value) {
      React29.useDebugValue(key);
      const store = this;
      useIsoLayoutEffect(() => {
        if (store.state[key] !== value) {
          store.set(key, value);
        }
      }, [store, key, value]);
    }
    /**
     * Synchronizes a single external value into the store and
     * cleans it up (sets to `undefined`) on unmount.
     *
     * Note that the while the value in `state` is updated immediately, the value returned
     * by `useState` is updated before the next render (similarly to React's `useState`).
     */
    useSyncedValueWithCleanup(key, value) {
      const store = this;
      useIsoLayoutEffect(() => {
        if (store.state[key] !== value) {
          store.set(key, value);
        }
        return () => {
          store.set(key, void 0);
        };
      }, [store, key, value]);
    }
    /**
     * Synchronizes multiple external values into the store.
     * Each value must match its state key. Pass an exact known subset rather than a broad
     * `Partial<State>`, which may contain `undefined` for required state fields.
     *
     * Note that the while the values in `state` are updated immediately, the values returned
     * by `useState` are updated before the next render (similarly to React's `useState`).
     *
     * @param statePart An exact subset of state fields to synchronize. Unknown keys are not accepted.
     */
    useSyncedValues(statePart) {
      const store = this;
      if (true) {
        React29.useDebugValue(statePart, (p) => Object.keys(p));
        const keys = React29.useRef(Object.keys(statePart)).current;
        const nextKeys = Object.keys(statePart);
        if (keys.length !== nextKeys.length || keys.some((key, index3) => key !== nextKeys[index3])) {
          console.error("ReactStore.useSyncedValues expects the same prop keys on every render. Keys should be stable.");
        }
      }
      const dependencies = Object.values(statePart);
      useIsoLayoutEffect(() => {
        store.update(statePart);
      }, [store, ...dependencies]);
    }
    /**
     * Registers a controllable prop pair (`controlled`, `defaultValue`) for a specific key. If `controlled`
     * is non-undefined, the store's state at `key` is updated to match `controlled`.
     */
    useControlledProp(key, controlled) {
      React29.useDebugValue(key);
      const store = this;
      const isControlled = controlled !== void 0;
      useIsoLayoutEffect(() => {
        if (isControlled && !Object.is(store.state[key], controlled)) {
          store.setState({
            ...store.state,
            [key]: controlled
          });
        }
      }, [store, key, controlled, isControlled]);
      if (true) {
        const cache = this.controlledValues ??= /* @__PURE__ */ new Map();
        if (!cache.has(key)) {
          cache.set(key, isControlled);
        }
        const previouslyControlled = cache.get(key);
        if (previouslyControlled !== void 0 && previouslyControlled !== isControlled) {
          console.error(`A component is changing the ${previouslyControlled ? "" : "un"}controlled state of ${key.toString()} to be ${previouslyControlled ? "un" : ""}controlled. Elements should not switch from uncontrolled to controlled (or vice versa).`);
        }
      }
    }
    /** Gets the current value from the store using a selector with the provided key.
     *
     * @param key Key of the selector to use.
     */
    select(key, a1, a2, a3) {
      const selector = this.selectors[key];
      return selector(this.state, a1, a2, a3);
    }
    /**
     * Returns a value from the store's state using a selector function.
     * Used to subscribe to specific parts of the state.
     * This methods causes a rerender whenever the selected state changes.
     *
     * @param key Key of the selector to use.
     */
    useState(key, a1, a2, a3) {
      React29.useDebugValue(key);
      return useStore(this, this.selectors[key], a1, a2, a3);
    }
    /**
     * Wraps a function with `useStableCallback` to ensure it has a stable reference
     * and assigns it to the context.
     *
     * @param key Key of the event callback. Must be a function in the context.
     * @param fn Function to assign.
     */
    useContextCallback(key, fn) {
      React29.useDebugValue(key);
      const stableFunction = useStableCallback(fn ?? NOOP);
      this.context[key] = stableFunction;
    }
    /**
     * Returns a stable setter function for a specific key in the store's state.
     * It's commonly used to pass as a ref callback to React elements.
     *
     * @param key Key of the state to set.
     */
    useStateSetter(key) {
      const ref = React29.useRef(void 0);
      if (ref.current === void 0) {
        ref.current = (value) => {
          this.set(key, value);
        };
      }
      return ref.current;
    }
    /**
     * Observes changes derived from the store's selectors and calls the listener when the selected value changes.
     *
     * @param key Key of the selector to observe.
     * @param listener Listener function called when the selector result changes.
     */
    observe(selector, listener) {
      let selectFn;
      if (typeof selector === "function") {
        selectFn = selector;
      } else {
        selectFn = this.selectors[selector];
      }
      let prevValue = selectFn(this.state);
      listener(prevValue, prevValue, this);
      return this.subscribe((nextState) => {
        const nextValue = selectFn(nextState);
        if (!Object.is(prevValue, nextValue)) {
          const oldValue = prevValue;
          prevValue = nextValue;
          listener(nextValue, oldValue, this);
        }
      });
    }
  };

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/components/FloatingRootStore.mjs
  var selectors = {
    open: (state) => state.open,
    transitionStatus: (state) => state.transitionStatus,
    domReferenceElement: (state) => state.domReferenceElement,
    referenceElement: (state) => state.positionReference ?? state.referenceElement,
    floatingElement: (state) => state.floatingElement,
    floatingId: (state) => state.floatingId
  };
  var FloatingRootStore = class extends ReactStore {
    constructor(options) {
      const {
        syncOnly,
        nested,
        onOpenChange,
        triggerElements,
        ...initialState
      } = options;
      super({
        ...initialState,
        positionReference: initialState.referenceElement,
        domReferenceElement: initialState.referenceElement
      }, {
        onOpenChange,
        dataRef: {
          current: {}
        },
        events: createEventEmitter(),
        nested,
        triggerElements
      }, selectors);
      this.syncOnly = syncOnly;
    }
    /**
     * Syncs the event used by hover logic to distinguish hover-open from click-like interaction.
     */
    syncOpenEvent = (newOpen, event) => {
      if (!newOpen || !this.state.open || // Prevent a pending hover-open from overwriting a click-open event, while allowing
      // click events to upgrade a hover-open.
      event != null && isClickLikeEvent(event)) {
        this.context.dataRef.current.openEvent = newOpen ? event : void 0;
      }
    };
    /**
     * Runs the root-owned side effects for an open state change.
     */
    dispatchOpenChange = (newOpen, eventDetails) => {
      this.syncOpenEvent(newOpen, eventDetails.event);
      const details = {
        open: newOpen,
        reason: eventDetails.reason,
        nativeEvent: eventDetails.event,
        nested: this.context.nested,
        triggerElement: eventDetails.trigger
      };
      this.context.events.emit("openchange", details);
    };
    /**
     * Emits the `openchange` event through the internal event emitter and calls the `onOpenChange` handler with the provided arguments.
     *
     * @param newOpen The new open state.
     * @param eventDetails Details about the event that triggered the open state change.
     */
    setOpen = (newOpen, eventDetails) => {
      if (this.syncOnly) {
        this.context.onOpenChange?.(newOpen, eventDetails);
        return;
      }
      this.dispatchOpenChange(newOpen, eventDetails);
      this.context.onOpenChange?.(newOpen, eventDetails);
    };
  };

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/hooks/useFloatingRootContext.mjs
  function useFloatingRootContext(options) {
    const {
      open: open2 = false,
      onOpenChange,
      elements = {}
    } = options;
    const floatingId = useId();
    const nested = useFloatingParentNodeId() != null;
    if (true) {
      const optionDomReference = elements.reference;
      if (optionDomReference && !isElement(optionDomReference)) {
        console.error("Cannot pass a virtual element to the `elements.reference` option,", "as it must be a real DOM element. Use `context.setPositionReference()`", "instead.");
      }
    }
    const store = useRefWithInit(() => new FloatingRootStore({
      open: open2,
      transitionStatus: void 0,
      onOpenChange,
      referenceElement: elements.reference ?? null,
      floatingElement: elements.floating ?? null,
      triggerElements: new PopupTriggerMap(),
      floatingId,
      syncOnly: false,
      nested
    })).current;
    useIsoLayoutEffect(() => {
      const valuesToSync = {
        open: open2,
        floatingId
      };
      if (elements.reference !== void 0) {
        valuesToSync.referenceElement = elements.reference;
        valuesToSync.domReferenceElement = isElement(elements.reference) ? elements.reference : null;
      }
      if (elements.floating !== void 0) {
        valuesToSync.floatingElement = elements.floating;
      }
      store.update(valuesToSync);
    }, [open2, floatingId, elements.reference, elements.floating, store]);
    store.context.onOpenChange = onOpenChange;
    store.context.nested = nested;
    return store;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/hooks/useListNavigation.mjs
  var React30 = __toESM(require_react(), 1);
  function isStationaryWebKitPointer(event) {
    return parts_exports.engine.webkit && event.movementX === 0 && event.movementY === 0;
  }
  function doSwitch(orientation, vertical, horizontal) {
    switch (orientation) {
      case "vertical":
        return vertical;
      case "horizontal":
        return horizontal;
      default:
        return vertical || horizontal;
    }
  }
  function isMainOrientationKey(key, orientation) {
    const vertical = key === ARROW_UP || key === ARROW_DOWN;
    const horizontal = key === ARROW_LEFT || key === ARROW_RIGHT;
    return doSwitch(orientation, vertical, horizontal);
  }
  function isMainOrientationToEndKey(key, orientation, rtl) {
    const vertical = key === ARROW_DOWN;
    const horizontal = rtl ? key === ARROW_LEFT : key === ARROW_RIGHT;
    return doSwitch(orientation, vertical, horizontal) || key === "Enter" || key === " " || key === "";
  }
  function isCrossOrientationOpenKey(key, orientation, rtl) {
    const vertical = rtl ? key === ARROW_LEFT : key === ARROW_RIGHT;
    const horizontal = key === ARROW_DOWN;
    return doSwitch(orientation, vertical, horizontal);
  }
  function isCrossOrientationCloseKey(key, orientation, rtl, grid) {
    const vertical = rtl ? key === ARROW_RIGHT : key === ARROW_LEFT;
    const horizontal = key === ARROW_UP;
    if (orientation === "both" || orientation === "horizontal" && grid) {
      return key === "Escape";
    }
    return doSwitch(orientation, vertical, horizontal);
  }
  function useListNavigation(store, props) {
    const {
      listRef,
      activeIndex,
      onNavigate: onNavigateProp = () => {
      },
      enabled = true,
      selectedIndex = null,
      allowEscape = false,
      loopFocus = false,
      nested = false,
      rtl = false,
      virtual = false,
      focusItemOnOpen = "auto",
      focusItemOnHover = true,
      openOnArrowKeyDown = true,
      disabledIndices = void 0,
      orientation = "vertical",
      triggerOrientation = orientation,
      parentOrientation,
      id,
      resetOnPointerLeave = true,
      externalTree,
      nestedReturnFocusRef,
      grid: navigateGrid
    } = props;
    const isGrid = navigateGrid != null;
    if (true) {
      if (allowEscape) {
        if (!loopFocus) {
          console.warn("`useListNavigation` looping must be enabled to allow escaping.");
        }
        if (!virtual) {
          console.warn("`useListNavigation` must be virtual to allow escaping.");
        }
      }
      if (orientation === "vertical" && isGrid) {
        console.warn("In grid list navigation mode, the `orientation` should", 'be either "horizontal" or "both".');
      }
    }
    const open2 = store.useState("open");
    const floatingElement = store.useState("floatingElement");
    const domReferenceElement = store.useState("domReferenceElement");
    const dataRef = store.context.dataRef;
    const floatingFocusElement = getFloatingFocusElement(floatingElement);
    const typeableComboboxReference = isTypeableCombobox(domReferenceElement);
    const floatingFocusElementRef = useValueAsRef(floatingFocusElement);
    const parentId = useFloatingParentNodeId();
    const tree = useFloatingTree(externalTree);
    const focusItemOnOpenRef = React30.useRef(focusItemOnOpen);
    const indexRef = React30.useRef(selectedIndex ?? -1);
    const keyRef = React30.useRef(null);
    const isPointerModalityRef = React30.useRef(true);
    const onNavigate = useStableCallback((event) => {
      onNavigateProp(indexRef.current === -1 ? null : indexRef.current, event);
    });
    const previousMountedRef = React30.useRef(!!floatingElement);
    const previousOpenRef = React30.useRef(open2);
    const forceSyncFocusRef = React30.useRef(false);
    const forceScrollIntoViewRef = React30.useRef(false);
    const cancelQueuedFocusRef = React30.useRef(null);
    const disabledIndicesRef = useValueAsRef(disabledIndices);
    const latestOpenRef = useValueAsRef(open2);
    const selectedIndexRef = useValueAsRef(selectedIndex);
    const resetOnPointerLeaveRef = useValueAsRef(resetOnPointerLeave);
    const focusFrame = useAnimationFrame();
    const waitForListPopulatedFrame = useAnimationFrame();
    const focusItem = useStableCallback(() => {
      focusFrame.cancel();
      function runFocus(item2) {
        if (!virtual) {
          cancelQueuedFocusRef.current = enqueueFocus(item2, {
            sync: forceSyncFocusRef.current,
            preventScroll: true
          });
        }
      }
      const initialItem = listRef.current[indexRef.current];
      const forceScrollIntoView = forceScrollIntoViewRef.current;
      if (initialItem) {
        runFocus(initialItem);
      }
      const scheduler2 = forceSyncFocusRef.current ? (callback) => callback() : (callback) => focusFrame.request(callback);
      scheduler2(() => {
        const waitedItem = listRef.current[indexRef.current] || initialItem;
        if (!waitedItem) {
          return;
        }
        if (!initialItem) {
          runFocus(waitedItem);
        }
        const shouldScrollIntoView = (
          // eslint-disable-next-line @typescript-eslint/no-use-before-define
          item && (forceScrollIntoView || !isPointerModalityRef.current)
        );
        if (shouldScrollIntoView) {
          waitedItem.scrollIntoView?.({
            block: "nearest",
            inline: "nearest"
          });
        }
      });
    });
    useIsoLayoutEffect(() => {
      dataRef.current.orientation = orientation;
    }, [dataRef, orientation]);
    useIsoLayoutEffect(() => {
      if (!open2) {
        keyRef.current = null;
      }
      if (!open2 || focusItemOnOpen !== "auto") {
        focusItemOnOpenRef.current = focusItemOnOpen;
      }
    }, [open2, focusItemOnOpen]);
    useIsoLayoutEffect(() => {
      if (!enabled) {
        return;
      }
      if (open2 && floatingElement) {
        indexRef.current = selectedIndex ?? -1;
        if (focusItemOnOpenRef.current && selectedIndex != null) {
          forceScrollIntoViewRef.current = true;
          onNavigate();
        }
      } else if (previousMountedRef.current) {
        indexRef.current = -1;
        onNavigate();
      }
    }, [enabled, open2, floatingElement, selectedIndex, onNavigate]);
    useIsoLayoutEffect(() => {
      if (!enabled) {
        return;
      }
      if (!open2) {
        forceSyncFocusRef.current = false;
        return;
      }
      if (!floatingElement) {
        return;
      }
      if (activeIndex == null) {
        forceSyncFocusRef.current = false;
        if (selectedIndexRef.current != null) {
          return;
        }
        if (previousMountedRef.current) {
          indexRef.current = -1;
          focusItem();
        }
        if ((!previousOpenRef.current || !previousMountedRef.current) && focusItemOnOpenRef.current && (keyRef.current != null || focusItemOnOpenRef.current === true && keyRef.current == null)) {
          let runs = 0;
          const waitForListPopulated = () => {
            if (listRef.current[0] == null) {
              if (runs < 2) {
                const scheduler2 = runs ? (callback) => waitForListPopulatedFrame.request(callback) : queueMicrotask;
                scheduler2(waitForListPopulated);
              }
              runs += 1;
            } else {
              indexRef.current = keyRef.current == null || isMainOrientationToEndKey(keyRef.current, triggerOrientation, rtl) || nested ? getMinListIndex(listRef) : getMaxListIndex(listRef);
              keyRef.current = null;
              onNavigate();
            }
          };
          waitForListPopulated();
        }
      } else if (!isIndexOutOfListBounds(listRef.current, activeIndex)) {
        indexRef.current = activeIndex;
        focusItem();
        forceScrollIntoViewRef.current = false;
      }
    }, [enabled, open2, floatingElement, activeIndex, selectedIndexRef, nested, listRef, triggerOrientation, rtl, onNavigate, focusItem, waitForListPopulatedFrame]);
    useIsoLayoutEffect(() => {
      if (!enabled || floatingElement || !tree || virtual || !previousMountedRef.current) {
        return;
      }
      const nodes = tree.nodesRef.current;
      const parent = nodes.find((node) => node.id === parentId)?.context?.elements.floating;
      const activeEl = activeElement(ownerDocument(domReferenceElement ?? parent ?? null));
      const treeContainsActiveEl = nodes.some((node) => node.context && contains(node.context.elements.floating, activeEl));
      if (parent && !treeContainsActiveEl && isPointerModalityRef.current) {
        parent.focus({
          preventScroll: true
        });
      }
    }, [enabled, floatingElement, domReferenceElement, tree, parentId, virtual]);
    useIsoLayoutEffect(() => {
      previousOpenRef.current = open2;
      previousMountedRef.current = !!floatingElement;
    });
    const hasActiveIndex = activeIndex != null;
    const syncCurrentTarget = useStableCallback((event) => {
      if (!latestOpenRef.current) {
        return;
      }
      const index3 = listRef.current.indexOf(event.currentTarget);
      if (index3 !== -1 && (indexRef.current !== index3 || activeIndex !== index3)) {
        indexRef.current = index3;
        onNavigate(event);
      }
    });
    const getParentOrientation = useStableCallback(() => {
      return parentOrientation ?? tree?.nodesRef.current.find((node) => node.id === parentId)?.context?.dataRef?.current.orientation;
    });
    const getMinEnabledIndex = useStableCallback(() => {
      return getMinListIndex(listRef, disabledIndicesRef.current);
    });
    const commonOnKeyDown = useStableCallback((event) => {
      isPointerModalityRef.current = false;
      forceSyncFocusRef.current = true;
      if (event.which === 229) {
        return;
      }
      if (!latestOpenRef.current && event.currentTarget === floatingFocusElementRef.current) {
        return;
      }
      if (nested && isCrossOrientationCloseKey(event.key, orientation, rtl, isGrid)) {
        if (!isMainOrientationKey(event.key, getParentOrientation())) {
          stopEvent(event);
        }
        store.setOpen(false, createChangeEventDetails(reason_parts_exports.listNavigation, event.nativeEvent));
        const returnElement = nestedReturnFocusRef?.current ?? domReferenceElement;
        if (isHTMLElement(returnElement)) {
          returnElement.focus();
        }
        return;
      }
      if (activeIndex != null && activeIndex !== indexRef.current && !isIndexOutOfListBounds(listRef.current, activeIndex)) {
        indexRef.current = activeIndex;
      }
      const currentIndex = indexRef.current;
      const minIndex = getMinListIndex(listRef, disabledIndices);
      const maxIndex = getMaxListIndex(listRef, disabledIndices);
      if (!typeableComboboxReference) {
        if (event.key === "Home") {
          stopEvent(event);
          indexRef.current = minIndex;
          onNavigate(event);
        }
        if (event.key === "End") {
          stopEvent(event);
          indexRef.current = maxIndex;
          onNavigate(event);
        }
      }
      if (navigateGrid != null) {
        const index3 = navigateGrid(event, indexRef.current, listRef, orientation, loopFocus, rtl, disabledIndices, minIndex, maxIndex);
        if (index3 != null && index3 !== indexRef.current) {
          indexRef.current = index3;
          onNavigate(event);
        }
        if (orientation === "both") {
          return;
        }
      }
      if (isMainOrientationKey(event.key, orientation)) {
        stopEvent(event);
        const focusedElement = activeElement(event.currentTarget.ownerDocument);
        if (open2 && !virtual && contains(event.currentTarget, focusedElement) && !listRef.current.some((item2) => item2 != null && contains(item2, focusedElement))) {
          indexRef.current = isMainOrientationToEndKey(event.key, orientation, rtl) ? minIndex : maxIndex;
          onNavigate(event);
          if (activeIndex === indexRef.current) {
            focusItem();
          }
          return;
        }
        const {
          index: index3,
          wrapped
        } = getNextListIndex(listRef.current, currentIndex, {
          decrement: !isMainOrientationToEndKey(event.key, orientation, rtl),
          loopFocus,
          allowEscape,
          disabledIndices,
          minIndex,
          maxIndex
        });
        if (wrapped) {
          forceSyncFocusRef.current = false;
        }
        indexRef.current = index3;
        onNavigate(event);
      }
    });
    const item = React30.useMemo(() => {
      const itemProps = {
        onFocus(event) {
          forceSyncFocusRef.current = true;
          syncCurrentTarget(event);
        },
        onClick({
          currentTarget
        }) {
          if (!virtual) {
            currentTarget.focus({
              preventScroll: true
            });
          }
        },
        onMouseMove(event) {
          if (isStationaryWebKitPointer(event)) {
            return;
          }
          forceSyncFocusRef.current = true;
          forceScrollIntoViewRef.current = false;
          if (focusItemOnHover) {
            syncCurrentTarget(event);
          }
        },
        onPointerLeave(event) {
          if (!latestOpenRef.current || !isPointerModalityRef.current || event.pointerType === "touch") {
            return;
          }
          forceSyncFocusRef.current = true;
          const relatedTarget = event.relatedTarget;
          if (!focusItemOnHover || listRef.current.includes(relatedTarget)) {
            return;
          }
          if (!resetOnPointerLeaveRef.current) {
            return;
          }
          cancelQueuedFocusRef.current?.();
          cancelQueuedFocusRef.current = null;
          indexRef.current = -1;
          onNavigate(event);
          if (!virtual) {
            const floatingFocusEl = floatingFocusElementRef.current;
            const activeEl = activeElement(ownerDocument(floatingFocusEl));
            if (floatingFocusEl && contains(floatingFocusEl, activeEl)) {
              floatingFocusEl.focus({
                preventScroll: true
              });
            }
          }
        }
      };
      return itemProps;
    }, [syncCurrentTarget, latestOpenRef, floatingFocusElementRef, focusItemOnHover, listRef, onNavigate, resetOnPointerLeaveRef, virtual]);
    const ariaActiveDescendantProp = React30.useMemo(() => {
      return virtual && open2 && hasActiveIndex && {
        "aria-activedescendant": `${id}-${activeIndex}`
      };
    }, [virtual, open2, hasActiveIndex, id, activeIndex]);
    const floating = React30.useMemo(() => {
      return {
        ...!typeableComboboxReference ? ariaActiveDescendantProp : {},
        onKeyDown(event) {
          if (event.key === "Tab" && event.shiftKey && open2 && !virtual) {
            const target = getTarget(event.nativeEvent);
            if (target && !contains(floatingFocusElementRef.current, target)) {
              return;
            }
            stopEvent(event);
            const details = createChangeEventDetails(reason_parts_exports.focusOut, event.nativeEvent);
            store.setOpen(false, details);
            const returnElement = nestedReturnFocusRef?.current ?? domReferenceElement;
            if (!details.isCanceled && isHTMLElement(returnElement)) {
              returnElement.focus();
            }
            return;
          }
          commonOnKeyDown(event);
        },
        onPointerMove(event) {
          if (isStationaryWebKitPointer(event)) {
            return;
          }
          isPointerModalityRef.current = true;
        }
      };
    }, [ariaActiveDescendantProp, commonOnKeyDown, floatingFocusElementRef, typeableComboboxReference, store, open2, virtual, domReferenceElement, nestedReturnFocusRef]);
    const trigger = React30.useMemo(() => {
      function openOnNavigationKeyDown(event) {
        store.setOpen(true, createChangeEventDetails(reason_parts_exports.listNavigation, event.nativeEvent, event.currentTarget));
      }
      function checkVirtualMouse(event) {
        if (focusItemOnOpen === "auto" && isVirtualClick(event.nativeEvent)) {
          focusItemOnOpenRef.current = !virtual;
        }
      }
      function checkVirtualPointer(event) {
        focusItemOnOpenRef.current = focusItemOnOpen;
        if (focusItemOnOpen === "auto" && isVirtualPointerEvent(event.nativeEvent)) {
          focusItemOnOpenRef.current = true;
        }
      }
      return {
        onKeyDown(event) {
          const currentOpen = store.select("open");
          isPointerModalityRef.current = false;
          const isArrowKey = event.key.startsWith("Arrow");
          const isParentCrossOpenKey = isCrossOrientationOpenKey(event.key, getParentOrientation(), rtl);
          const isMainKey = isMainOrientationKey(event.key, currentOpen ? orientation : triggerOrientation);
          const isNavigationKey = (nested ? isParentCrossOpenKey : isMainKey) || event.key === "Enter" || event.key.trim() === "";
          if (virtual && currentOpen && (!nested || isTypeableElement(event.currentTarget))) {
            return commonOnKeyDown(event);
          }
          if (!currentOpen && !openOnArrowKeyDown && isArrowKey) {
            return void 0;
          }
          if (isNavigationKey) {
            const isParentMainKey = isMainOrientationKey(event.key, getParentOrientation());
            keyRef.current = nested && isParentMainKey ? null : event.key;
          }
          if (nested) {
            if (isParentCrossOpenKey) {
              stopEvent(event);
              if (currentOpen) {
                indexRef.current = getMinEnabledIndex();
                onNavigate(event);
                if (virtual) {
                  floatingFocusElementRef.current?.focus();
                }
              } else {
                openOnNavigationKeyDown(event);
              }
            }
            return void 0;
          }
          if (isMainKey) {
            if (selectedIndexRef.current != null) {
              indexRef.current = selectedIndexRef.current;
            }
            stopEvent(event);
            if (!currentOpen && openOnArrowKeyDown) {
              openOnNavigationKeyDown(event);
            } else {
              commonOnKeyDown(event);
            }
            if (currentOpen) {
              onNavigate(event);
            }
          }
          return void 0;
        },
        onFocus(event) {
          if (event.target !== event.currentTarget) {
            return;
          }
          if (store.select("open") && !virtual) {
            indexRef.current = -1;
            onNavigate(event);
          }
        },
        onPointerDown: checkVirtualPointer,
        onPointerEnter: checkVirtualPointer,
        onMouseDown: checkVirtualMouse,
        onClick: checkVirtualMouse
      };
    }, [commonOnKeyDown, focusItemOnOpen, getMinEnabledIndex, nested, onNavigate, store, openOnArrowKeyDown, orientation, triggerOrientation, getParentOrientation, rtl, selectedIndexRef, virtual, floatingFocusElementRef]);
    const reference = React30.useMemo(() => {
      return {
        ...ariaActiveDescendantProp,
        ...trigger
      };
    }, [ariaActiveDescendantProp, trigger]);
    return React30.useMemo(() => enabled ? {
      reference,
      floating,
      item,
      trigger
    } : {}, [enabled, reference, floating, trigger, item]);
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/hooks/useTypeahead.mjs
  var React31 = __toESM(require_react(), 1);
  function useTypeahead(store, props) {
    const {
      listRef,
      elementsRef,
      activeIndex,
      onMatch: onMatchProp,
      disabledIndices,
      onTyping: onTypingProp,
      enabled = true,
      resetMs = 750,
      selectedIndex = null
    } = props;
    const onTyping = useStableCallback(onTypingProp);
    const open2 = store.useState("open");
    const timeout = useTimeout();
    const stringRef = React31.useRef("");
    const prevIndexRef = React31.useRef(selectedIndex ?? activeIndex ?? -1);
    const matchIndexRef = React31.useRef(null);
    const onKeyDown = useStableCallback((event) => {
      function getElement(index4) {
        return elementsRef?.current[index4];
      }
      function isItemAvailable(index4) {
        const element = getElement(index4);
        if (element && !isElementVisible(element) || element?.matches(":disabled")) {
          return false;
        }
        return disabledIndices == null || !isListIndexDisabled(EMPTY_ARRAY, index4, disabledIndices);
      }
      function getMatchingIndex(list, string, startIndex2 = 0) {
        if (list.length === 0) {
          return -1;
        }
        const normalizedStartIndex = (startIndex2 % list.length + list.length) % list.length;
        const lowerString = string.toLowerCase();
        for (let offset4 = 0; offset4 < list.length; offset4 += 1) {
          const index4 = (normalizedStartIndex + offset4) % list.length;
          const text = list[index4];
          if (!text?.toLowerCase().startsWith(lowerString) || !isItemAvailable(index4)) {
            continue;
          }
          return index4;
        }
        return -1;
      }
      const listContent = listRef.current;
      if (stringRef.current.length > 0 && event.key === " ") {
        stopEvent(event);
        onTyping?.(true);
      }
      if (stringRef.current.length > 0 && stringRef.current[0] !== " ") {
        if (getMatchingIndex(listContent, stringRef.current) === -1 && event.key !== " ") {
          onTyping?.(false);
        }
      }
      if (listContent == null || // Character key.
      event.key.length !== 1 || // Modifier key.
      event.ctrlKey || event.metaKey || event.altKey) {
        return;
      }
      if (open2 && event.key !== " ") {
        stopEvent(event);
        onTyping?.(true);
      }
      const isNewSession = stringRef.current === "";
      if (isNewSession) {
        prevIndexRef.current = selectedIndex ?? activeIndex ?? -1;
      }
      const allowRapidSuccessionOfFirstLetter = listContent.every((text, index4) => text && isItemAvailable(index4) ? text[0]?.toLowerCase() !== text[1]?.toLowerCase() : true);
      if (allowRapidSuccessionOfFirstLetter && stringRef.current === event.key) {
        stringRef.current = "";
        prevIndexRef.current = matchIndexRef.current;
      }
      stringRef.current += event.key;
      timeout.start(resetMs, () => {
        stringRef.current = "";
        prevIndexRef.current = matchIndexRef.current;
        onTyping?.(false);
      });
      const prevIndex = isNewSession ? selectedIndex ?? activeIndex ?? -1 : prevIndexRef.current;
      const startIndex = (prevIndex ?? 0) + 1;
      const index3 = getMatchingIndex(listContent, stringRef.current, startIndex);
      if (index3 !== -1) {
        onMatchProp?.(index3, event);
        matchIndexRef.current = index3;
      } else if (event.key !== " ") {
        stringRef.current = "";
        onTyping?.(false);
      }
    });
    const onBlur = useStableCallback((event) => {
      const next = event.relatedTarget;
      const currentDomReferenceElement = store.select("domReferenceElement");
      const currentFloatingElement = store.select("floatingElement");
      const withinComposite = contains(currentDomReferenceElement, next) || contains(currentFloatingElement, next);
      if (withinComposite) {
        return;
      }
      timeout.clear();
      stringRef.current = "";
      prevIndexRef.current = matchIndexRef.current;
      onTyping?.(false);
    });
    useIsoLayoutEffect(() => {
      if (!open2 && selectedIndex !== null) {
        return;
      }
      timeout.clear();
      matchIndexRef.current = null;
      if (stringRef.current !== "") {
        stringRef.current = "";
        onTyping?.(false);
      }
    }, [open2, selectedIndex, timeout, onTyping]);
    const sharedProps = React31.useMemo(() => ({
      onKeyDown,
      onBlur
    }), [onKeyDown, onBlur]);
    return React31.useMemo(() => enabled ? {
      reference: sharedProps,
      floating: sharedProps
    } : {}, [enabled, sharedProps]);
  }

  // node_modules/.store/@floating-ui/core@1.8.0-3V8aS7jJHHGcsCINjP9-5A/node_modules/@floating-ui/core/dist/floating-ui.core.mjs
  function computeCoordsFromPlacement(_ref, placement, rtl) {
    let {
      reference,
      floating
    } = _ref;
    const sideAxis = getSideAxis(placement);
    const alignmentAxis = getAlignmentAxis(placement);
    const alignLength = getAxisLength(alignmentAxis);
    const side = getSide(placement);
    const isVertical = sideAxis === "y";
    const commonX = reference.x + reference.width / 2 - floating.width / 2;
    const commonY = reference.y + reference.height / 2 - floating.height / 2;
    const commonAlign = reference[alignLength] / 2 - floating[alignLength] / 2;
    let coords;
    switch (side) {
      case "top":
        coords = {
          x: commonX,
          y: reference.y - floating.height
        };
        break;
      case "bottom":
        coords = {
          x: commonX,
          y: reference.y + reference.height
        };
        break;
      case "right":
        coords = {
          x: reference.x + reference.width,
          y: commonY
        };
        break;
      case "left":
        coords = {
          x: reference.x - floating.width,
          y: commonY
        };
        break;
      default:
        coords = {
          x: reference.x,
          y: reference.y
        };
    }
    const alignment = getAlignment(placement);
    if (alignment) {
      coords[alignmentAxis] += commonAlign * (alignment === "end" ? 1 : -1) * (rtl && isVertical ? -1 : 1);
    }
    return coords;
  }
  async function detectOverflow(state, options) {
    var _await$platform$isEle;
    if (options === void 0) {
      options = {};
    }
    const {
      x,
      y,
      platform: platform3,
      rects,
      elements,
      strategy
    } = state;
    const {
      boundary = "clippingAncestors",
      rootBoundary = "viewport",
      elementContext = "floating",
      altBoundary = false,
      padding = 0
    } = evaluate(options, state);
    const paddingObject = getPaddingObject(padding);
    const altContext = elementContext === "floating" ? "reference" : "floating";
    const element = elements[altBoundary ? altContext : elementContext];
    const clippingClientRect = rectToClientRect(await platform3.getClippingRect({
      element: ((_await$platform$isEle = await (platform3.isElement == null ? void 0 : platform3.isElement(element))) != null ? _await$platform$isEle : true) ? element : element.contextElement || await (platform3.getDocumentElement == null ? void 0 : platform3.getDocumentElement(elements.floating)),
      boundary,
      rootBoundary,
      strategy
    }));
    const rect = elementContext === "floating" ? {
      x,
      y,
      width: rects.floating.width,
      height: rects.floating.height
    } : rects.reference;
    const offsetParent = await (platform3.getOffsetParent == null ? void 0 : platform3.getOffsetParent(elements.floating));
    const offsetScale = await (platform3.isElement == null ? void 0 : platform3.isElement(offsetParent)) && await (platform3.getScale == null ? void 0 : platform3.getScale(offsetParent)) || {
      x: 1,
      y: 1
    };
    const elementClientRect = rectToClientRect(platform3.convertOffsetParentRelativeRectToViewportRelativeRect ? await platform3.convertOffsetParentRelativeRectToViewportRelativeRect({
      elements,
      rect,
      offsetParent,
      strategy
    }) : rect);
    return {
      top: (clippingClientRect.top - elementClientRect.top + paddingObject.top) / offsetScale.y,
      bottom: (elementClientRect.bottom - clippingClientRect.bottom + paddingObject.bottom) / offsetScale.y,
      left: (clippingClientRect.left - elementClientRect.left + paddingObject.left) / offsetScale.x,
      right: (elementClientRect.right - clippingClientRect.right + paddingObject.right) / offsetScale.x
    };
  }
  var MAX_RESET_COUNT = 50;
  var computePosition = async (reference, floating, config) => {
    const {
      placement = "bottom",
      strategy = "absolute",
      middleware = [],
      platform: platform3
    } = config;
    const platformWithDetectOverflow = platform3.detectOverflow ? platform3 : {
      ...platform3,
      detectOverflow
    };
    const rtl = await (platform3.isRTL == null ? void 0 : platform3.isRTL(floating));
    let rects = await platform3.getElementRects({
      reference,
      floating,
      strategy
    });
    let {
      x,
      y
    } = computeCoordsFromPlacement(rects, placement, rtl);
    let statefulPlacement = placement;
    let resetCount = 0;
    const middlewareData = {};
    for (let i = 0; i < middleware.length; i++) {
      const currentMiddleware = middleware[i];
      if (!currentMiddleware) {
        continue;
      }
      const {
        name: name16,
        fn
      } = currentMiddleware;
      const {
        x: nextX,
        y: nextY,
        data,
        reset
      } = await fn({
        x,
        y,
        initialPlacement: placement,
        placement: statefulPlacement,
        strategy,
        middlewareData,
        rects,
        platform: platformWithDetectOverflow,
        elements: {
          reference,
          floating
        }
      });
      x = nextX != null ? nextX : x;
      y = nextY != null ? nextY : y;
      middlewareData[name16] = {
        ...middlewareData[name16],
        ...data
      };
      if (reset && resetCount < MAX_RESET_COUNT) {
        resetCount++;
        if (typeof reset === "object") {
          if (reset.placement) {
            statefulPlacement = reset.placement;
          }
          if (reset.rects) {
            rects = reset.rects === true ? await platform3.getElementRects({
              reference,
              floating,
              strategy
            }) : reset.rects;
          }
          ({
            x,
            y
          } = computeCoordsFromPlacement(rects, statefulPlacement, rtl));
        }
        i = -1;
      }
    }
    return {
      x,
      y,
      placement: statefulPlacement,
      strategy,
      middlewareData
    };
  };
  var flip = function(options) {
    if (options === void 0) {
      options = {};
    }
    return {
      name: "flip",
      options,
      async fn(state) {
        var _middlewareData$arrow, _middlewareData$flip;
        const {
          placement,
          middlewareData,
          rects,
          initialPlacement,
          platform: platform3,
          elements
        } = state;
        const {
          mainAxis: checkMainAxis = true,
          crossAxis: checkCrossAxis = true,
          fallbackPlacements: specifiedFallbackPlacements,
          fallbackStrategy = "bestFit",
          fallbackAxisSideDirection = "none",
          flipAlignment = true,
          ...detectOverflowOptions
        } = evaluate(options, state);
        if ((_middlewareData$arrow = middlewareData.arrow) != null && _middlewareData$arrow.alignmentOffset) {
          return {};
        }
        const side = getSide(placement);
        const initialSideAxis = getSideAxis(initialPlacement);
        const isBasePlacement = getSide(initialPlacement) === initialPlacement;
        const rtl = await (platform3.isRTL == null ? void 0 : platform3.isRTL(elements.floating));
        const fallbackPlacements = specifiedFallbackPlacements || (isBasePlacement || !flipAlignment ? [getOppositePlacement(initialPlacement)] : getExpandedPlacements(initialPlacement));
        const hasFallbackAxisSideDirection = fallbackAxisSideDirection !== "none";
        if (!specifiedFallbackPlacements && hasFallbackAxisSideDirection) {
          fallbackPlacements.push(...getOppositeAxisPlacements(initialPlacement, flipAlignment, fallbackAxisSideDirection, rtl));
        }
        const placements2 = [initialPlacement, ...fallbackPlacements];
        const overflow = await platform3.detectOverflow(state, detectOverflowOptions);
        const overflows = [];
        let overflowsData = ((_middlewareData$flip = middlewareData.flip) == null ? void 0 : _middlewareData$flip.overflows) || [];
        if (checkMainAxis) {
          overflows.push(overflow[side]);
        }
        if (checkCrossAxis) {
          const sides2 = getAlignmentSides(placement, rects, rtl);
          overflows.push(overflow[sides2[0]], overflow[sides2[1]]);
        }
        overflowsData = [...overflowsData, {
          placement,
          overflows
        }];
        if (!overflows.every((side2) => side2 <= 0)) {
          var _middlewareData$flip2, _overflowsData$filter;
          const nextIndex = (((_middlewareData$flip2 = middlewareData.flip) == null ? void 0 : _middlewareData$flip2.index) || 0) + 1;
          const nextPlacement = placements2[nextIndex];
          if (nextPlacement) {
            const ignoreCrossAxisOverflow = checkCrossAxis === "alignment" ? initialSideAxis !== getSideAxis(nextPlacement) : false;
            if (!ignoreCrossAxisOverflow || // We leave the current main axis only if every placement on that axis
            // overflows the main axis.
            overflowsData.every((d) => getSideAxis(d.placement) === initialSideAxis ? d.overflows[0] > 0 : true)) {
              return {
                data: {
                  index: nextIndex,
                  overflows: overflowsData
                },
                reset: {
                  placement: nextPlacement
                }
              };
            }
          }
          let resetPlacement = (_overflowsData$filter = overflowsData.filter((d) => d.overflows[0] <= 0).sort((a, b) => a.overflows[1] - b.overflows[1])[0]) == null ? void 0 : _overflowsData$filter.placement;
          if (!resetPlacement) {
            switch (fallbackStrategy) {
              case "bestFit": {
                var _overflowsData$filter2;
                const placement2 = (_overflowsData$filter2 = overflowsData.filter((d) => {
                  if (hasFallbackAxisSideDirection) {
                    const currentSideAxis = getSideAxis(d.placement);
                    return currentSideAxis === initialSideAxis || // Create a bias to the `y` side axis due to horizontal
                    // reading directions favoring greater width.
                    currentSideAxis === "y";
                  }
                  return true;
                }).map((d) => [d.placement, d.overflows.filter((overflow2) => overflow2 > 0).reduce((acc, overflow2) => acc + overflow2, 0)]).sort((a, b) => a[1] - b[1])[0]) == null ? void 0 : _overflowsData$filter2[0];
                if (placement2) {
                  resetPlacement = placement2;
                }
                break;
              }
              case "initialPlacement":
                resetPlacement = initialPlacement;
                break;
            }
          }
          if (placement !== resetPlacement) {
            return {
              reset: {
                placement: resetPlacement
              }
            };
          }
        }
        return {};
      }
    };
  };
  var originSides = /* @__PURE__ */ new Set(["left", "top"]);
  async function convertValueToCoords(state, options) {
    const {
      placement,
      platform: platform3,
      elements
    } = state;
    const rtl = await (platform3.isRTL == null ? void 0 : platform3.isRTL(elements.floating));
    const side = getSide(placement);
    const alignment = getAlignment(placement);
    const isVertical = getSideAxis(placement) === "y";
    const mainAxisMulti = originSides.has(side) ? -1 : 1;
    const crossAxisMulti = rtl && isVertical ? -1 : 1;
    const rawValue = evaluate(options, state);
    let {
      mainAxis,
      crossAxis,
      alignmentAxis
    } = typeof rawValue === "number" ? {
      mainAxis: rawValue,
      crossAxis: 0,
      alignmentAxis: null
    } : {
      mainAxis: rawValue.mainAxis || 0,
      crossAxis: rawValue.crossAxis || 0,
      alignmentAxis: rawValue.alignmentAxis
    };
    if (alignment && typeof alignmentAxis === "number") {
      crossAxis = alignment === "end" ? alignmentAxis * -1 : alignmentAxis;
    }
    return isVertical ? {
      x: crossAxis * crossAxisMulti,
      y: mainAxis * mainAxisMulti
    } : {
      x: mainAxis * mainAxisMulti,
      y: crossAxis * crossAxisMulti
    };
  }
  var offset = function(options) {
    if (options === void 0) {
      options = 0;
    }
    return {
      name: "offset",
      options,
      async fn(state) {
        var _middlewareData$offse, _middlewareData$arrow;
        const {
          x,
          y,
          placement,
          middlewareData
        } = state;
        const diffCoords = await convertValueToCoords(state, options);
        if (placement === ((_middlewareData$offse = middlewareData.offset) == null ? void 0 : _middlewareData$offse.placement) && (_middlewareData$arrow = middlewareData.arrow) != null && _middlewareData$arrow.alignmentOffset) {
          return {};
        }
        return {
          x: x + diffCoords.x,
          y: y + diffCoords.y,
          data: {
            ...diffCoords,
            placement
          }
        };
      }
    };
  };
  var shift = function(options) {
    if (options === void 0) {
      options = {};
    }
    return {
      name: "shift",
      options,
      async fn(state) {
        const {
          x,
          y,
          placement,
          platform: platform3
        } = state;
        const {
          mainAxis: checkMainAxis = true,
          crossAxis: checkCrossAxis = false,
          limiter = {
            fn: (_ref) => {
              let {
                x: x2,
                y: y2
              } = _ref;
              return {
                x: x2,
                y: y2
              };
            }
          },
          ...detectOverflowOptions
        } = evaluate(options, state);
        const coords = {
          x,
          y
        };
        const overflow = await platform3.detectOverflow(state, detectOverflowOptions);
        const crossAxis = getSideAxis(placement);
        const mainAxis = getOppositeAxis(crossAxis);
        let mainAxisCoord = coords[mainAxis];
        let crossAxisCoord = coords[crossAxis];
        const clampCoord = (axis, coord) => clamp(coord + overflow[axis === "y" ? "top" : "left"], coord, coord - overflow[axis === "y" ? "bottom" : "right"]);
        if (checkMainAxis) {
          mainAxisCoord = clampCoord(mainAxis, mainAxisCoord);
        }
        if (checkCrossAxis) {
          crossAxisCoord = clampCoord(crossAxis, crossAxisCoord);
        }
        const limitedCoords = limiter.fn({
          ...state,
          [mainAxis]: mainAxisCoord,
          [crossAxis]: crossAxisCoord
        });
        return {
          ...limitedCoords,
          data: {
            x: limitedCoords.x - x,
            y: limitedCoords.y - y,
            enabled: {
              [mainAxis]: checkMainAxis,
              [crossAxis]: checkCrossAxis
            }
          }
        };
      }
    };
  };
  var limitShift = function(options) {
    if (options === void 0) {
      options = {};
    }
    return {
      options,
      fn(state) {
        var _rawOffset$mainAxis, _rawOffset$crossAxis;
        const {
          x,
          y,
          placement,
          rects,
          middlewareData
        } = state;
        const {
          offset: offset4 = 0,
          mainAxis: checkMainAxis = true,
          crossAxis: checkCrossAxis = true
        } = evaluate(options, state);
        const coords = {
          x,
          y
        };
        const crossAxis = getSideAxis(placement);
        const mainAxis = getOppositeAxis(crossAxis);
        let mainAxisCoord = coords[mainAxis];
        let crossAxisCoord = coords[crossAxis];
        const rawOffset = evaluate(offset4, state);
        const computedOffset = typeof rawOffset === "number" ? {
          mainAxis: rawOffset,
          crossAxis: 0
        } : {
          mainAxis: (_rawOffset$mainAxis = rawOffset.mainAxis) != null ? _rawOffset$mainAxis : 0,
          crossAxis: (_rawOffset$crossAxis = rawOffset.crossAxis) != null ? _rawOffset$crossAxis : 0
        };
        if (checkMainAxis) {
          const len = mainAxis === "y" ? "height" : "width";
          const limitMin = rects.reference[mainAxis] - rects.floating[len] + computedOffset.mainAxis;
          const limitMax = rects.reference[mainAxis] + rects.reference[len] - computedOffset.mainAxis;
          if (mainAxisCoord < limitMin) {
            mainAxisCoord = limitMin;
          } else if (mainAxisCoord > limitMax) {
            mainAxisCoord = limitMax;
          }
        }
        if (checkCrossAxis) {
          var _middlewareData$offse, _middlewareData$offse2;
          const len = mainAxis === "y" ? "width" : "height";
          const isOriginSide = originSides.has(getSide(placement));
          const limitMin = rects.reference[crossAxis] - rects.floating[len] + (isOriginSide ? ((_middlewareData$offse = middlewareData.offset) == null ? void 0 : _middlewareData$offse[crossAxis]) || 0 : 0) + (isOriginSide ? 0 : computedOffset.crossAxis);
          const limitMax = rects.reference[crossAxis] + rects.reference[len] + (isOriginSide ? 0 : ((_middlewareData$offse2 = middlewareData.offset) == null ? void 0 : _middlewareData$offse2[crossAxis]) || 0) - (isOriginSide ? computedOffset.crossAxis : 0);
          if (crossAxisCoord < limitMin) {
            crossAxisCoord = limitMin;
          } else if (crossAxisCoord > limitMax) {
            crossAxisCoord = limitMax;
          }
        }
        return {
          [mainAxis]: mainAxisCoord,
          [crossAxis]: crossAxisCoord
        };
      }
    };
  };
  var size = function(options) {
    if (options === void 0) {
      options = {};
    }
    return {
      name: "size",
      options,
      async fn(state) {
        const {
          placement,
          rects,
          platform: platform3,
          elements
        } = state;
        const {
          apply = () => {
          },
          ...detectOverflowOptions
        } = evaluate(options, state);
        const overflow = await platform3.detectOverflow(state, detectOverflowOptions);
        const side = getSide(placement);
        const alignment = getAlignment(placement);
        const isYAxis = getSideAxis(placement) === "y";
        const {
          width,
          height
        } = rects.floating;
        let heightSide;
        let widthSide;
        if (side === "top" || side === "bottom") {
          heightSide = side;
          widthSide = alignment === (await (platform3.isRTL == null ? void 0 : platform3.isRTL(elements.floating)) ? "start" : "end") ? "left" : "right";
        } else {
          widthSide = side;
          heightSide = alignment === "end" ? "top" : "bottom";
        }
        const maximumClippingHeight = height - overflow.top - overflow.bottom;
        const maximumClippingWidth = width - overflow.left - overflow.right;
        const overflowAvailableHeight = min(height - overflow[heightSide], maximumClippingHeight);
        const overflowAvailableWidth = min(width - overflow[widthSide], maximumClippingWidth);
        const shiftData = state.middlewareData.shift;
        const noShift = !shiftData;
        let availableHeight2 = overflowAvailableHeight;
        let availableWidth2 = overflowAvailableWidth;
        if (shiftData != null && shiftData.enabled.x) {
          availableWidth2 = maximumClippingWidth;
        }
        if (shiftData != null && shiftData.enabled.y) {
          availableHeight2 = maximumClippingHeight;
        }
        if (noShift && !alignment) {
          if (isYAxis) {
            availableWidth2 = width - 2 * max(overflow.left, overflow.right);
          } else {
            availableHeight2 = height - 2 * max(overflow.top, overflow.bottom);
          }
        }
        await apply({
          ...state,
          availableWidth: availableWidth2,
          availableHeight: availableHeight2
        });
        const nextDimensions = await platform3.getDimensions(elements.floating);
        if (width !== nextDimensions.width || height !== nextDimensions.height) {
          return {
            reset: {
              rects: true
            }
          };
        }
        return {};
      }
    };
  };

  // node_modules/.store/@floating-ui/dom@1.8.0-vHXcr4FgWneV0cgwFw-d7A/node_modules/@floating-ui/dom/dist/floating-ui.dom.mjs
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
      height,
      $: shouldFallback
    };
  }
  function unwrapElement(element) {
    return !isElement(element) ? element.contextElement : element;
  }
  function getScale(element) {
    const domElement = unwrapElement(element);
    if (!isHTMLElement(domElement)) {
      return createCoords(1);
    }
    const rect = domElement.getBoundingClientRect();
    const {
      width,
      height,
      $
    } = getCssDimensions(domElement);
    let x = ($ ? round(rect.width) : rect.width) / width;
    let y = ($ ? round(rect.height) : rect.height) / height;
    if (!x || !Number.isFinite(x)) {
      x = 1;
    }
    if (!y || !Number.isFinite(y)) {
      y = 1;
    }
    return {
      x,
      y
    };
  }
  var noOffsets = /* @__PURE__ */ createCoords(0);
  function getVisualOffsets(element) {
    const win = getWindow(element);
    if (!isWebKit() || !win.visualViewport) {
      return noOffsets;
    }
    return {
      x: win.visualViewport.offsetLeft,
      y: win.visualViewport.offsetTop
    };
  }
  function shouldAddVisualOffsets(element, isFixed, floatingOffsetParent) {
    if (isFixed === void 0) {
      isFixed = false;
    }
    return !!floatingOffsetParent && isFixed && floatingOffsetParent === getWindow(element);
  }
  function getBoundingClientRect(element, includeScale, isFixedStrategy, offsetParent) {
    if (includeScale === void 0) {
      includeScale = false;
    }
    if (isFixedStrategy === void 0) {
      isFixedStrategy = false;
    }
    const clientRect = element.getBoundingClientRect();
    const domElement = unwrapElement(element);
    let scale = createCoords(1);
    if (includeScale) {
      if (offsetParent) {
        if (isElement(offsetParent)) {
          scale = getScale(offsetParent);
        }
      } else {
        scale = getScale(element);
      }
    }
    const visualOffsets = shouldAddVisualOffsets(domElement, isFixedStrategy, offsetParent) ? getVisualOffsets(domElement) : createCoords(0);
    let x = (clientRect.left + visualOffsets.x) / scale.x;
    let y = (clientRect.top + visualOffsets.y) / scale.y;
    let width = clientRect.width / scale.x;
    let height = clientRect.height / scale.y;
    if (domElement && offsetParent) {
      const win = getWindow(domElement);
      const offsetWin = isElement(offsetParent) ? getWindow(offsetParent) : offsetParent;
      let currentWin = win;
      let currentIFrame = getFrameElement(currentWin);
      while (currentIFrame && offsetWin !== currentWin) {
        const iframeScale = getScale(currentIFrame);
        const iframeRect = currentIFrame.getBoundingClientRect();
        const css = getComputedStyle2(currentIFrame);
        const left = iframeRect.left + (currentIFrame.clientLeft + parseFloat(css.paddingLeft)) * iframeScale.x;
        const top = iframeRect.top + (currentIFrame.clientTop + parseFloat(css.paddingTop)) * iframeScale.y;
        x *= iframeScale.x;
        y *= iframeScale.y;
        width *= iframeScale.x;
        height *= iframeScale.y;
        x += left;
        y += top;
        currentWin = getWindow(currentIFrame);
        currentIFrame = getFrameElement(currentWin);
      }
    }
    return rectToClientRect({
      width,
      height,
      x,
      y
    });
  }
  function getWindowScrollBarX(element, rect) {
    const leftScroll = getNodeScroll(element).scrollLeft;
    if (!rect) {
      return getBoundingClientRect(getDocumentElement(element)).left + leftScroll;
    }
    return rect.left + leftScroll;
  }
  function getHTMLOffset(documentElement, scroll) {
    const htmlRect = documentElement.getBoundingClientRect();
    const x = htmlRect.left + scroll.scrollLeft - getWindowScrollBarX(documentElement, htmlRect);
    const y = htmlRect.top + scroll.scrollTop;
    return {
      x,
      y
    };
  }
  function convertOffsetParentRelativeRectToViewportRelativeRect(_ref) {
    let {
      elements,
      rect,
      offsetParent,
      strategy
    } = _ref;
    const isFixed = strategy === "fixed";
    const documentElement = getDocumentElement(offsetParent);
    const topLayer = elements ? isTopLayer(elements.floating) : false;
    if (offsetParent === documentElement || topLayer && isFixed) {
      return rect;
    }
    let scroll = {
      scrollLeft: 0,
      scrollTop: 0
    };
    let scale = createCoords(1);
    const offsets = createCoords(0);
    const isOffsetParentAnElement = isHTMLElement(offsetParent);
    if (isOffsetParentAnElement || !isFixed) {
      if (getNodeName(offsetParent) !== "body" || isOverflowElement(documentElement)) {
        scroll = getNodeScroll(offsetParent);
      }
      if (isOffsetParentAnElement) {
        const offsetRect = getBoundingClientRect(offsetParent);
        scale = getScale(offsetParent);
        offsets.x = offsetRect.x + offsetParent.clientLeft;
        offsets.y = offsetRect.y + offsetParent.clientTop;
      }
    }
    const htmlOffset = documentElement && !isOffsetParentAnElement && !isFixed ? getHTMLOffset(documentElement, scroll) : createCoords(0);
    return {
      width: rect.width * scale.x,
      height: rect.height * scale.y,
      x: rect.x * scale.x - scroll.scrollLeft * scale.x + offsets.x + htmlOffset.x,
      y: rect.y * scale.y - scroll.scrollTop * scale.y + offsets.y + htmlOffset.y
    };
  }
  function getClientRects(element) {
    return element.getClientRects ? Array.from(element.getClientRects()) : [];
  }
  function getDocumentRect(html) {
    const scroll = getNodeScroll(html);
    const body = html.ownerDocument.body;
    const width = max(html.scrollWidth, html.clientWidth, body.scrollWidth, body.clientWidth);
    const height = max(html.scrollHeight, html.clientHeight, body.scrollHeight, body.clientHeight);
    let x = -scroll.scrollLeft + getWindowScrollBarX(html);
    const y = -scroll.scrollTop;
    if (getComputedStyle2(body).direction === "rtl") {
      x += max(html.clientWidth, body.clientWidth) - width;
    }
    return {
      width,
      height,
      x,
      y
    };
  }
  var SCROLLBAR_MAX = 25;
  function getViewportRect(element, strategy, rootBoundary) {
    if (rootBoundary === void 0) {
      rootBoundary = "viewport";
    }
    const isLayoutViewport = rootBoundary === "layoutViewport";
    const win = getWindow(element);
    const html = getDocumentElement(element);
    const visualViewport = win.visualViewport;
    let width = html.clientWidth;
    let height = html.clientHeight;
    let x = 0;
    let y = 0;
    if (visualViewport) {
      const layoutRelativeClientCoords = !isWebKit() || strategy === "fixed";
      if (isLayoutViewport) {
        if (!layoutRelativeClientCoords) {
          x = -visualViewport.offsetLeft;
          y = -visualViewport.offsetTop;
        }
      } else {
        width = visualViewport.width;
        height = visualViewport.height;
        if (layoutRelativeClientCoords) {
          x = visualViewport.offsetLeft;
          y = visualViewport.offsetTop;
        }
      }
    }
    const windowScrollbarX = getWindowScrollBarX(html);
    if (windowScrollbarX <= 0) {
      const doc = html.ownerDocument;
      const body = doc.body;
      const bodyStyles = getComputedStyle(body);
      const bodyMarginInline = doc.compatMode === "CSS1Compat" ? parseFloat(bodyStyles.marginLeft) + parseFloat(bodyStyles.marginRight) || 0 : 0;
      const reservedWidth = Math.abs(html.clientWidth - body.clientWidth - bodyMarginInline);
      const gutter = getComputedStyle(html).scrollbarGutter === "stable both-edges" ? reservedWidth / 2 : reservedWidth;
      if (gutter <= SCROLLBAR_MAX) {
        width -= gutter;
      }
    }
    return {
      width,
      height,
      x,
      y
    };
  }
  function getInnerBoundingClientRect(element, strategy) {
    const clientRect = getBoundingClientRect(element, true, strategy === "fixed");
    const top = clientRect.top + element.clientTop;
    const left = clientRect.left + element.clientLeft;
    const scale = getScale(element);
    const width = element.clientWidth * scale.x;
    const height = element.clientHeight * scale.y;
    const x = left * scale.x;
    const y = top * scale.y;
    return {
      width,
      height,
      x,
      y
    };
  }
  function getClientRectFromClippingAncestor(element, clippingAncestor, strategy) {
    let rect;
    if (clippingAncestor === "viewport" || clippingAncestor === "layoutViewport") {
      rect = getViewportRect(element, strategy, clippingAncestor);
    } else if (clippingAncestor === "document") {
      rect = getDocumentRect(getDocumentElement(element));
    } else if (isElement(clippingAncestor)) {
      rect = getInnerBoundingClientRect(clippingAncestor, strategy);
    } else {
      const visualOffsets = getVisualOffsets(element);
      rect = {
        x: clippingAncestor.x - visualOffsets.x,
        y: clippingAncestor.y - visualOffsets.y,
        width: clippingAncestor.width,
        height: clippingAncestor.height
      };
    }
    return rectToClientRect(rect);
  }
  function getClippingElementAncestors(element, cache) {
    const cachedResult = cache.get(element);
    if (cachedResult) {
      return cachedResult;
    }
    let result = getOverflowAncestors(element, [], false).filter((el) => isElement(el) && getNodeName(el) !== "body");
    let lastKeptComputedStyle = null;
    const elementIsFixed = getComputedStyle2(element).position === "fixed";
    let currentNode = elementIsFixed ? getParentNode(element) : element;
    while (isElement(currentNode) && !isLastTraversableNode(currentNode)) {
      const computedStyle = getComputedStyle2(currentNode);
      const currentNodeIsContaining = isContainingBlock(currentNode);
      const lastPosition = lastKeptComputedStyle ? lastKeptComputedStyle.position : elementIsFixed ? "fixed" : "";
      const shouldDropCurrentNode = !currentNodeIsContaining && (lastPosition === "fixed" || lastPosition === "absolute" && computedStyle.position === "static");
      if (shouldDropCurrentNode) {
        result = result.filter((ancestor) => ancestor !== currentNode);
      } else {
        lastKeptComputedStyle = computedStyle;
      }
      currentNode = getParentNode(currentNode);
    }
    cache.set(element, result);
    return result;
  }
  function getClippingRect(_ref) {
    let {
      element,
      boundary,
      rootBoundary,
      strategy
    } = _ref;
    const elementClippingAncestors = boundary === "clippingAncestors" ? isTopLayer(element) ? [] : getClippingElementAncestors(element, this._c) : [].concat(boundary);
    const clippingAncestors = [...elementClippingAncestors, rootBoundary];
    const firstRect = getClientRectFromClippingAncestor(element, clippingAncestors[0], strategy);
    let top = firstRect.top;
    let right = firstRect.right;
    let bottom = firstRect.bottom;
    let left = firstRect.left;
    for (let i = 1; i < clippingAncestors.length; i++) {
      const rect = getClientRectFromClippingAncestor(element, clippingAncestors[i], strategy);
      top = max(rect.top, top);
      right = min(rect.right, right);
      bottom = min(rect.bottom, bottom);
      left = max(rect.left, left);
    }
    return {
      width: right - left,
      height: bottom - top,
      x: left,
      y: top
    };
  }
  function getDimensions(element) {
    const {
      width,
      height
    } = getCssDimensions(element);
    return {
      width,
      height
    };
  }
  function getRectRelativeToOffsetParent(element, offsetParent, strategy) {
    const isOffsetParentAnElement = isHTMLElement(offsetParent);
    const documentElement = getDocumentElement(offsetParent);
    const isFixed = strategy === "fixed";
    const rect = getBoundingClientRect(element, true, isFixed, offsetParent);
    let scroll = {
      scrollLeft: 0,
      scrollTop: 0
    };
    const offsets = createCoords(0);
    if (isOffsetParentAnElement || !isFixed) {
      if (getNodeName(offsetParent) !== "body" || isOverflowElement(documentElement)) {
        scroll = getNodeScroll(offsetParent);
      }
      if (isOffsetParentAnElement) {
        const offsetRect = getBoundingClientRect(offsetParent, true, isFixed, offsetParent);
        offsets.x = offsetRect.x + offsetParent.clientLeft;
        offsets.y = offsetRect.y + offsetParent.clientTop;
      }
    }
    if (!isOffsetParentAnElement && documentElement) {
      offsets.x = getWindowScrollBarX(documentElement);
    }
    const htmlOffset = documentElement && !isOffsetParentAnElement && !isFixed ? getHTMLOffset(documentElement, scroll) : createCoords(0);
    const x = rect.left + scroll.scrollLeft - offsets.x - htmlOffset.x;
    const y = rect.top + scroll.scrollTop - offsets.y - htmlOffset.y;
    return {
      x,
      y,
      width: rect.width,
      height: rect.height
    };
  }
  function isStaticPositioned(element) {
    return getComputedStyle2(element).position === "static";
  }
  function getTrueOffsetParent(element, polyfill) {
    if (!isHTMLElement(element) || getComputedStyle2(element).position === "fixed") {
      return null;
    }
    if (polyfill) {
      return polyfill(element);
    }
    let rawOffsetParent = element.offsetParent;
    if (getDocumentElement(element) === rawOffsetParent) {
      rawOffsetParent = rawOffsetParent.ownerDocument.body;
    }
    return rawOffsetParent;
  }
  function getOffsetParent(element, polyfill) {
    const win = getWindow(element);
    if (isTopLayer(element)) {
      return win;
    }
    if (!isHTMLElement(element)) {
      let svgOffsetParent = getParentNode(element);
      while (svgOffsetParent && !isLastTraversableNode(svgOffsetParent)) {
        if (isElement(svgOffsetParent) && !isStaticPositioned(svgOffsetParent)) {
          return svgOffsetParent;
        }
        svgOffsetParent = getParentNode(svgOffsetParent);
      }
      return win;
    }
    let offsetParent = getTrueOffsetParent(element, polyfill);
    while (offsetParent && isTableElement(offsetParent) && isStaticPositioned(offsetParent)) {
      offsetParent = getTrueOffsetParent(offsetParent, polyfill);
    }
    if (offsetParent && isLastTraversableNode(offsetParent) && isStaticPositioned(offsetParent) && !isContainingBlock(offsetParent)) {
      return win;
    }
    return offsetParent || getContainingBlock(element) || win;
  }
  var getElementRects = async function(data) {
    const getOffsetParentFn = this.getOffsetParent || getOffsetParent;
    const getDimensionsFn = this.getDimensions;
    const floatingDimensions = await getDimensionsFn(data.floating);
    return {
      reference: getRectRelativeToOffsetParent(data.reference, await getOffsetParentFn(data.floating), data.strategy),
      floating: {
        x: 0,
        y: 0,
        width: floatingDimensions.width,
        height: floatingDimensions.height
      }
    };
  };
  function isRTL(element) {
    return getComputedStyle2(element).direction === "rtl";
  }
  var platform2 = {
    convertOffsetParentRelativeRectToViewportRelativeRect,
    getDocumentElement,
    getClippingRect,
    getOffsetParent,
    getElementRects,
    getClientRects,
    getDimensions,
    getScale,
    isElement,
    isRTL
  };
  function rectsAreEqual(a, b) {
    return a.x === b.x && a.y === b.y && a.width === b.width && a.height === b.height;
  }
  function observeMove(element, onMove, ancestorResize) {
    let io = null;
    let timeoutId;
    const root = getDocumentElement(element);
    function cleanup() {
      var _io;
      clearTimeout(timeoutId);
      (_io = io) == null || _io.disconnect();
      io = null;
    }
    function refresh(skip, threshold) {
      if (skip === void 0) {
        skip = false;
      }
      if (threshold === void 0) {
        threshold = 1;
      }
      cleanup();
      const elementRectForRootMargin = element.getBoundingClientRect();
      const {
        left,
        top,
        width,
        height
      } = elementRectForRootMargin;
      if (!skip) {
        onMove();
      }
      if (!width || !height) {
        return;
      }
      const insetTop = floor(top);
      const insetRight = floor(root.clientWidth - (left + width));
      const insetBottom = floor(root.clientHeight - (top + height));
      const insetLeft = floor(left);
      const rootMargin = -insetTop + "px " + -insetRight + "px " + -insetBottom + "px " + -insetLeft + "px";
      const options = {
        rootMargin,
        threshold: max(0, min(1, threshold)) || 1
      };
      let isFirstUpdate = true;
      function handleObserve(entries) {
        const ratio = entries[0].intersectionRatio;
        if (!rectsAreEqual(elementRectForRootMargin, element.getBoundingClientRect())) {
          return refresh();
        }
        if (ratio !== threshold) {
          if (!isFirstUpdate) {
            return refresh();
          }
          if (!ratio) {
            timeoutId = setTimeout(() => {
              refresh(false, 1e-7);
            }, 1e3);
          } else {
            refresh(false, ratio);
          }
        }
        isFirstUpdate = false;
      }
      try {
        io = new IntersectionObserver(handleObserve, {
          ...options,
          // Handle <iframe>s
          root: root.ownerDocument
        });
      } catch (_e) {
        io = new IntersectionObserver(handleObserve, options);
      }
      io.observe(element);
    }
    const win = getWindow(element);
    const handleResize = () => refresh(ancestorResize);
    win.addEventListener("resize", handleResize);
    refresh(true);
    return () => {
      win.removeEventListener("resize", handleResize);
      cleanup();
    };
  }
  function autoUpdate(reference, floating, update2, options) {
    if (options === void 0) {
      options = {};
    }
    const {
      ancestorScroll = true,
      ancestorResize = true,
      elementResize = typeof ResizeObserver === "function",
      layoutShift = typeof IntersectionObserver === "function",
      animationFrame = false
    } = options;
    const referenceEl = unwrapElement(reference);
    const ancestors = ancestorScroll || ancestorResize ? [...referenceEl ? getOverflowAncestors(referenceEl) : [], ...floating ? getOverflowAncestors(floating) : []] : [];
    ancestors.forEach((ancestor) => {
      ancestorScroll && ancestor.addEventListener("scroll", update2);
      ancestorResize && ancestor.addEventListener("resize", update2);
    });
    const cleanupIo = referenceEl && layoutShift ? observeMove(referenceEl, update2, ancestorResize) : null;
    let reobserveFrame = -1;
    let resizeObserver = null;
    if (elementResize) {
      resizeObserver = new ResizeObserver((_ref) => {
        let [firstEntry] = _ref;
        if (firstEntry && firstEntry.target === referenceEl && resizeObserver && floating) {
          resizeObserver.unobserve(floating);
          cancelAnimationFrame(reobserveFrame);
          reobserveFrame = requestAnimationFrame(() => {
            var _resizeObserver;
            (_resizeObserver = resizeObserver) == null || _resizeObserver.observe(floating);
          });
        }
        update2();
      });
      if (referenceEl && !animationFrame) {
        resizeObserver.observe(referenceEl);
      }
      if (floating) {
        resizeObserver.observe(floating);
      }
    }
    let frameId;
    let prevRefRect = animationFrame ? getBoundingClientRect(reference) : null;
    if (animationFrame) {
      frameLoop();
    }
    function frameLoop() {
      const nextRefRect = getBoundingClientRect(reference);
      if (prevRefRect && !rectsAreEqual(prevRefRect, nextRefRect)) {
        update2();
      }
      prevRefRect = nextRefRect;
      frameId = requestAnimationFrame(frameLoop);
    }
    update2();
    return () => {
      var _resizeObserver2;
      ancestors.forEach((ancestor) => {
        ancestorScroll && ancestor.removeEventListener("scroll", update2);
        ancestorResize && ancestor.removeEventListener("resize", update2);
      });
      cleanupIo == null || cleanupIo();
      (_resizeObserver2 = resizeObserver) == null || _resizeObserver2.disconnect();
      resizeObserver = null;
      if (animationFrame) {
        cancelAnimationFrame(frameId);
      }
    };
  }
  var offset2 = offset;
  var shift2 = shift;
  var flip2 = flip;
  var size2 = size;
  var limitShift2 = limitShift;
  var computePosition2 = (reference, floating, options) => {
    const cache = /* @__PURE__ */ new Map();
    const mergedOptions = options != null ? options : {};
    const platformWithCache = {
      ...platform2,
      ...mergedOptions.platform,
      _c: cache
    };
    return computePosition(reference, floating, {
      ...mergedOptions,
      platform: platformWithCache
    });
  };

  // node_modules/.store/@floating-ui/react-dom@2.1.9-AxWcAwqlMfOrAFOinFKSVg/node_modules/@floating-ui/react-dom/dist/floating-ui.react-dom.mjs
  var React32 = __toESM(require_react(), 1);
  var import_react2 = __toESM(require_react(), 1);
  var ReactDOM4 = __toESM(require_react_dom(), 1);
  var isClient = typeof document !== "undefined";
  var noop2 = function noop3() {
  };
  var index = isClient ? import_react2.useLayoutEffect : noop2;
  function deepEqual(a, b) {
    if (a === b) {
      return true;
    }
    if (typeof a !== typeof b) {
      return false;
    }
    if (typeof a === "function" && a.toString() === b.toString()) {
      return true;
    }
    let length;
    let i;
    let keys;
    if (a && b && typeof a === "object") {
      if (Array.isArray(a)) {
        length = a.length;
        if (length !== b.length) return false;
        for (i = length; i-- !== 0; ) {
          if (!deepEqual(a[i], b[i])) {
            return false;
          }
        }
        return true;
      }
      keys = Object.keys(a);
      length = keys.length;
      if (length !== Object.keys(b).length) {
        return false;
      }
      for (i = length; i-- !== 0; ) {
        if (!{}.hasOwnProperty.call(b, keys[i])) {
          return false;
        }
      }
      for (i = length; i-- !== 0; ) {
        const key = keys[i];
        if (key === "_owner" && a.$$typeof) {
          continue;
        }
        if (!deepEqual(a[key], b[key])) {
          return false;
        }
      }
      return true;
    }
    return a !== a && b !== b;
  }
  function getDPR(element) {
    if (typeof window === "undefined") {
      return 1;
    }
    const win = element.ownerDocument.defaultView || window;
    return win.devicePixelRatio || 1;
  }
  function roundByDPR(element, value) {
    const dpr = getDPR(element);
    return Math.round(value * dpr) / dpr;
  }
  function useLatestRef(value) {
    const ref = React32.useRef(value);
    index(() => {
      ref.current = value;
    });
    return ref;
  }
  function useFloating(options) {
    if (options === void 0) {
      options = {};
    }
    const {
      placement = "bottom",
      strategy = "absolute",
      middleware = [],
      platform: platform3,
      elements: {
        reference: externalReference,
        floating: externalFloating
      } = {},
      transform = true,
      whileElementsMounted,
      open: open2
    } = options;
    const [data, setData] = React32.useState({
      x: 0,
      y: 0,
      strategy,
      placement,
      middlewareData: {},
      isPositioned: false
    });
    const [latestMiddleware, setLatestMiddleware] = React32.useState(middleware);
    if (!deepEqual(latestMiddleware, middleware)) {
      setLatestMiddleware(middleware);
    }
    const [_reference, _setReference] = React32.useState(null);
    const [_floating, _setFloating] = React32.useState(null);
    const setReference = React32.useCallback((node) => {
      if (node !== referenceRef.current) {
        referenceRef.current = node;
        _setReference(node);
      }
    }, []);
    const setFloating = React32.useCallback((node) => {
      if (node !== floatingRef.current) {
        floatingRef.current = node;
        _setFloating(node);
      }
    }, []);
    const referenceEl = externalReference || _reference;
    const floatingEl = externalFloating || _floating;
    const referenceRef = React32.useRef(null);
    const floatingRef = React32.useRef(null);
    const dataRef = React32.useRef(data);
    const hasWhileElementsMounted = whileElementsMounted != null;
    const whileElementsMountedRef = useLatestRef(whileElementsMounted);
    const platformRef = useLatestRef(platform3);
    const openRef = useLatestRef(open2);
    const update2 = React32.useCallback(() => {
      if (!referenceRef.current || !floatingRef.current) {
        return;
      }
      const config = {
        placement,
        strategy,
        middleware: latestMiddleware
      };
      if (platformRef.current) {
        config.platform = platformRef.current;
      }
      computePosition2(referenceRef.current, floatingRef.current, config).then((data2) => {
        const fullData = {
          ...data2,
          // The floating element's position may be recomputed while it's closed
          // but still mounted (such as when transitioning out). To ensure
          // `isPositioned` will be `false` initially on the next open, avoid
          // setting it to `true` when `open === false` (must be specified).
          isPositioned: openRef.current !== false
        };
        if (isMountedRef.current && !deepEqual(dataRef.current, fullData)) {
          dataRef.current = fullData;
          ReactDOM4.flushSync(() => {
            setData(fullData);
          });
        }
      });
    }, [latestMiddleware, placement, strategy, platformRef, openRef]);
    index(() => {
      if (open2 === false && dataRef.current.isPositioned) {
        dataRef.current.isPositioned = false;
        setData((data2) => ({
          ...data2,
          isPositioned: false
        }));
      }
    }, [open2]);
    const isMountedRef = React32.useRef(false);
    index(() => {
      isMountedRef.current = true;
      return () => {
        isMountedRef.current = false;
      };
    }, []);
    index(() => {
      if (referenceEl) referenceRef.current = referenceEl;
      if (floatingEl) floatingRef.current = floatingEl;
      if (referenceEl && floatingEl) {
        if (whileElementsMountedRef.current) {
          return whileElementsMountedRef.current(referenceEl, floatingEl, update2);
        }
        update2();
      }
    }, [referenceEl, floatingEl, update2, whileElementsMountedRef, hasWhileElementsMounted]);
    const refs = React32.useMemo(() => ({
      reference: referenceRef,
      floating: floatingRef,
      setReference,
      setFloating
    }), [setReference, setFloating]);
    const elements = React32.useMemo(() => ({
      reference: referenceEl,
      floating: floatingEl
    }), [referenceEl, floatingEl]);
    const floatingStyles = React32.useMemo(() => {
      const initialStyles = {
        position: strategy,
        left: 0,
        top: 0
      };
      if (!elements.floating) {
        return initialStyles;
      }
      const x = roundByDPR(elements.floating, data.x);
      const y = roundByDPR(elements.floating, data.y);
      if (transform) {
        return {
          ...initialStyles,
          transform: "translate(" + x + "px, " + y + "px)",
          ...getDPR(elements.floating) >= 1.5 && {
            willChange: "transform"
          }
        };
      }
      return {
        position: strategy,
        left: x,
        top: y
      };
    }, [strategy, transform, elements.floating, data.x, data.y]);
    return React32.useMemo(() => ({
      ...data,
      update: update2,
      refs,
      elements,
      floatingStyles
    }), [data, update2, refs, elements, floatingStyles]);
  }
  var offset3 = (options, deps) => {
    const result = offset2(options);
    return {
      name: result.name,
      fn: result.fn,
      options: [options, deps]
    };
  };
  var shift3 = (options, deps) => {
    const result = shift2(options);
    return {
      name: result.name,
      fn: result.fn,
      options: [options, deps]
    };
  };
  var limitShift3 = (options, deps) => {
    const result = limitShift2(options);
    return {
      fn: result.fn,
      options: [options, deps]
    };
  };
  var flip3 = (options, deps) => {
    const result = flip2(options);
    return {
      name: result.name,
      fn: result.fn,
      options: [options, deps]
    };
  };
  var size3 = (options, deps) => {
    const result = size2(options);
    return {
      name: result.name,
      fn: result.fn,
      options: [options, deps]
    };
  };

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/composite/composite.mjs
  var ARROW_UP2 = "ArrowUp";
  var ARROW_DOWN2 = "ArrowDown";
  var ARROW_LEFT2 = "ArrowLeft";
  var ARROW_RIGHT2 = "ArrowRight";
  var HOME = "Home";
  var END = "End";
  var COMPOSITE_KEYS = /* @__PURE__ */ new Set([ARROW_UP2, ARROW_DOWN2, ARROW_LEFT2, ARROW_RIGHT2, HOME, END]);
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

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/inertValue.mjs
  function inertValue(value) {
    if (isReactVersionAtLeast(19)) {
      return value;
    }
    return value ? "true" : void 0;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/InternalBackdrop.mjs
  var React33 = __toESM(require_react(), 1);
  var import_jsx_runtime27 = __toESM(require_jsx_runtime(), 1);
  var InternalBackdrop = /* @__PURE__ */ React33.forwardRef(function InternalBackdrop2(props, ref) {
    const {
      cutout,
      ...otherProps
    } = props;
    let clipPath;
    if (cutout) {
      const rect = cutout.getBoundingClientRect();
      clipPath = `polygon(0% 0%,100% 0%,100% 100%,0% 100%,0% 0%,${rect.left}px ${rect.top}px,${rect.left}px ${rect.bottom}px,${rect.right}px ${rect.bottom}px,${rect.right}px ${rect.top}px,${rect.left}px ${rect.top}px)`;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime27.jsx)("div", {
      ref,
      role: "presentation",
      "data-base-ui-inert": "",
      ...otherProps,
      style: {
        position: "fixed",
        inset: 0,
        userSelect: "none",
        WebkitUserSelect: "none",
        clipPath
      }
    });
  });
  if (true) InternalBackdrop.displayName = "InternalBackdrop";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/useOpenInteractionType.mjs
  var React36 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useEnhancedClickHandler.mjs
  var React34 = __toESM(require_react(), 1);
  function useEnhancedClickHandler(handler) {
    const lastClickInteractionTypeRef = React34.useRef("");
    const handlePointerDown = React34.useCallback((event) => {
      if (event.defaultPrevented) {
        return;
      }
      lastClickInteractionTypeRef.current = event.pointerType;
      handler(event, event.pointerType);
    }, [handler]);
    const handleClick = React34.useCallback((event) => {
      if (event.detail === 0) {
        handler(event, "keyboard");
        return;
      }
      if ("pointerType" in event) {
        handler(event, event.pointerType);
      } else {
        handler(event, lastClickInteractionTypeRef.current);
      }
      lastClickInteractionTypeRef.current = "";
    }, [handler]);
    return {
      onClick: handleClick,
      onPointerDown: handlePointerDown
    };
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useValueChanged.mjs
  var React35 = __toESM(require_react(), 1);
  function useValueChanged(value, onChange) {
    const valueRef = React35.useRef(value);
    const onChangeCallback = useStableCallback(onChange);
    useIsoLayoutEffect(() => {
      if (valueRef.current !== value) {
        onChangeCallback(valueRef.current);
      }
      valueRef.current = value;
    }, [value, onChangeCallback]);
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/useOpenInteractionType.mjs
  function useOpenMethodTriggerProps(open2, setOpenMethod) {
    const handleTriggerClick = useStableCallback((_, interactionType) => {
      const isOpen = typeof open2 === "function" ? open2() : open2;
      if (!isOpen) {
        setOpenMethod(interactionType || // On iOS Safari, the hitslop around touch targets means tapping outside an element's
        // bounds does not fire `pointerdown` but does fire `mousedown`. The `interactionType`
        // will be "" in that case.
        (parts_exports.os.ios ? "touch" : ""));
      }
    });
    const {
      onClick,
      onPointerDown
    } = useEnhancedClickHandler(handleTriggerClick);
    return React36.useMemo(() => ({
      onClick,
      onPointerDown
    }), [onClick, onPointerDown]);
  }
  function useOpenInteractionType(open2) {
    const [openMethod, setOpenMethod] = React36.useState(null);
    const triggerProps = useOpenMethodTriggerProps(open2, setOpenMethod);
    useValueChanged(open2, (previousOpen) => {
      if (previousOpen && !open2) {
        setOpenMethod(null);
      }
    });
    return React36.useMemo(() => ({
      openMethod,
      triggerProps
    }), [openMethod, triggerProps]);
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/useOnFirstRender.mjs
  var React37 = __toESM(require_react(), 1);
  function useOnFirstRender(fn) {
    const ref = React37.useRef(true);
    if (ref.current) {
      ref.current = false;
      fn();
    }
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/areArraysEqual.mjs
  function areArraysEqual(array1, array2, itemComparer = Object.is) {
    const {
      length
    } = array1;
    if (length !== array2.length) {
      return false;
    }
    for (let i = 0; i < length; i += 1) {
      if (!itemComparer(array1[i], array2[i])) {
        return false;
      }
    }
    return true;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/itemEquality.mjs
  var defaultItemEquality = (itemValue, selectedValue) => Object.is(itemValue, selectedValue);
  function compareItemEquality(itemValue, selectedValue, comparer) {
    if (itemValue == null || selectedValue == null) {
      return Object.is(itemValue, selectedValue);
    }
    return comparer(itemValue, selectedValue);
  }
  function isSelectedValueDirty(currentValue, initialValue, comparer) {
    if (Array.isArray(currentValue) && Array.isArray(initialValue)) {
      return !areArraysEqual(currentValue, initialValue, (itemValue, initialItemValue) => compareItemEquality(itemValue, initialItemValue, comparer));
    }
    return currentValue !== initialValue;
  }
  function selectedValueIncludes(selectedValues, itemValue, comparer) {
    if (!selectedValues) {
      return false;
    }
    return selectedValues.some((selectedValue) => {
      if (selectedValue === void 0) {
        return false;
      }
      return compareItemEquality(itemValue, selectedValue, comparer);
    });
  }
  function findItemIndex(itemValues, selectedValue, comparer) {
    if (!itemValues) {
      return -1;
    }
    return itemValues.findIndex((itemValue) => {
      if (itemValue === void 0) {
        return false;
      }
      return compareItemEquality(itemValue, selectedValue, comparer);
    });
  }
  function createSelectionMatcher(selectedValues, comparer) {
    if (comparer !== defaultItemEquality) {
      return (itemValue) => selectedValueIncludes(selectedValues, itemValue, comparer);
    }
    const index3 = new Set(selectedValues);
    index3.delete(void 0);
    return (itemValue) => index3.has(itemValue) && (itemValue !== 0 || selectedValues.some((v) => Object.is(itemValue, v)));
  }
  function findSelectionIndex(itemValues, selectedValue, comparer, multiple) {
    const index3 = multiple && Array.isArray(selectedValue) ? (
      // Anchor to the first selected item in rendered order so the index does not depend
      // on the order in which the values were added to the array.
      itemValues.findIndex(createSelectionMatcher(selectedValue, comparer))
    ) : findItemIndex(itemValues, selectedValue, comparer);
    return index3 === -1 ? null : index3;
  }
  function resolveSelectedIndex(index3, itemValue, registry, selectedValues, comparer, currentIndex) {
    if (selectedValueIncludes(selectedValues, itemValue, comparer)) {
      return currentIndex != null && index3 > currentIndex && selectedValueIncludes(selectedValues, registry[currentIndex], comparer) ? currentIndex : index3;
    }
    return index3 === currentIndex ? findSelectionIndex(registry, selectedValues, comparer, true) : currentIndex;
  }
  function removeItem(selectedValues, itemValue, comparer) {
    return selectedValues.filter((selectedValue) => !compareItemEquality(itemValue, selectedValue, comparer));
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/resolveValueLabel.mjs
  var React38 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/serializeValue.mjs
  function serializeValue(value) {
    if (value == null) {
      return "";
    }
    if (typeof value === "string") {
      return value;
    }
    try {
      return JSON.stringify(value);
    } catch {
      return String(value);
    }
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/resolveValueLabel.mjs
  var import_jsx_runtime28 = __toESM(require_jsx_runtime(), 1);
  function isGroup(item) {
    return typeof item === "object" && item != null && Array.isArray(item.items);
  }
  function isGroupedItems(items) {
    return isGroup(items?.[0]);
  }
  function flattenLeafItems(items) {
    return isGroupedItems(items) ? items.flatMap((group) => group.items) : items;
  }
  function hasNullItemLabel(items) {
    if (!Array.isArray(items)) {
      return items != null && "null" in items;
    }
    const arrayItems = items;
    if (isGroupedItems(arrayItems)) {
      for (const group of arrayItems) {
        for (const item of group.items) {
          if (item && item.value == null && item.label != null) {
            return true;
          }
        }
      }
      return false;
    }
    for (const item of arrayItems) {
      if (item && item.value == null && item.label != null) {
        return true;
      }
    }
    return false;
  }
  function stringifyAsLabel(item, itemToStringLabel) {
    if (itemToStringLabel && item != null) {
      return itemToStringLabel(item) ?? "";
    }
    if (item && typeof item === "object") {
      if ("label" in item && item.label != null) {
        return String(item.label);
      }
      if ("value" in item) {
        return String(item.value);
      }
    }
    return serializeValue(item);
  }
  function stringifyAsValue(item, itemToStringValue) {
    if (itemToStringValue && item != null) {
      return itemToStringValue(item) ?? "";
    }
    if (item && typeof item === "object" && "value" in item && "label" in item) {
      return serializeValue(item.value);
    }
    return serializeValue(item);
  }
  function resolveSelectedLabel(value, items, itemToStringLabel) {
    function fallback() {
      return stringifyAsLabel(value, itemToStringLabel);
    }
    if (itemToStringLabel && value != null) {
      return itemToStringLabel(value);
    }
    if (value && typeof value === "object" && "label" in value && value.label != null) {
      return value.label;
    }
    if (items && !Array.isArray(items)) {
      const label = Object.hasOwn(items, value) ? items[value] : void 0;
      return label ?? fallback();
    }
    if (Array.isArray(items)) {
      const arrayItems = items;
      const flatItems = flattenLeafItems(arrayItems);
      if (value == null || typeof value !== "object") {
        const match = flatItems.find((item) => item.value === value);
        if (match && match.label != null) {
          return match.label;
        }
        return fallback();
      }
      if ("value" in value) {
        const match = flatItems.find((item) => item && item.value === value.value);
        if (match && match.label != null) {
          return match.label;
        }
      }
    }
    return fallback();
  }
  function resolveMultipleLabels(values, items, itemToStringLabel) {
    return values.reduce((acc, value, index3) => {
      if (index3 > 0) {
        acc.push(", ");
      }
      acc.push(/* @__PURE__ */ (0, import_jsx_runtime28.jsx)(React38.Fragment, {
        children: resolveSelectedLabel(value, items, itemToStringLabel)
      }, index3));
      return acc;
    }, []);
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/field-root-context/FieldRootContext.mjs
  var React39 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/control/FieldControlDataAttributes.mjs
  var valid = "data-valid";
  var invalid = "data-invalid";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/field-constants/constants.mjs
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/field-root-context/FieldRootContext.mjs
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
    focusOwnerRef: {
      current: void 0
    },
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
  var FieldRootContext = /* @__PURE__ */ React39.createContext(DEFAULT_FIELD_ROOT_CONTEXT);
  if (true) FieldRootContext.displayName = "FieldRootContext";
  function useFieldRootContext(optional = true) {
    const context = React39.useContext(FieldRootContext);
    if (context.setValidityData === NOOP && !optional) {
      throw new Error(true ? "Base UI: FieldRootContext is missing. Field parts must be placed within <Field.Root>." : formatErrorMessage_default(28));
    }
    return context;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/field-register-control/useRegisterFieldControl.mjs
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/form-context/FormContext.mjs
  var React40 = __toESM(require_react(), 1);
  var FormContext = /* @__PURE__ */ React40.createContext({
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
    return React40.useContext(FormContext);
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/labelable-provider/useLabelableId.mjs
  var React42 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/labelable-provider/LabelableContext.mjs
  var React41 = __toESM(require_react(), 1);
  var LabelableContext = /* @__PURE__ */ React41.createContext({
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
    return React41.useContext(LabelableContext);
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/labelable-provider/useLabelableId.mjs
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
    const hasRegisteredRef = React42.useRef(false);
    const hadExplicitIdRef = React42.useRef(false);
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/direction-context/DirectionContext.mjs
  var React43 = __toESM(require_react(), 1);
  var DirectionContext = /* @__PURE__ */ React43.createContext(void 0);
  if (true) DirectionContext.displayName = "DirectionContext";
  function useDirection() {
    const context = React43.useContext(DirectionContext);
    return context?.direction ?? "ltr";
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/field-root-context/useSetFieldFocused.mjs
  function useSetFieldFocused(disabled2, focusTargetRef, onFocusedChange) {
    const {
      setFocused,
      focusOwnerRef
    } = useFieldRootContext();
    const setFieldFocused = useStableCallback((focused) => {
      if (focused ? !disabled2 : focusOwnerRef.current === setFieldFocused) {
        focusOwnerRef.current = focused && setFieldFocused;
        onFocusedChange?.(focused);
        setFocused(focused);
      }
    });
    useIsoLayoutEffect(() => {
      const el = focusTargetRef.current;
      const root = el?.getRootNode();
      if (root?.activeElement === el) {
        setFieldFocused(true);
      }
      return () => setFieldFocused(false);
    }, [disabled2, focusTargetRef, setFieldFocused]);
    return setFieldFocused;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/getPseudoElementBounds.mjs
  var BOUNDARY_OFFSET = 5;
  function isMouseWithinBounds(event, element) {
    const bounds = getPseudoElementBounds(element);
    return event.clientX >= bounds.left - BOUNDARY_OFFSET && event.clientX <= bounds.right + BOUNDARY_OFFSET && event.clientY >= bounds.top - BOUNDARY_OFFSET && event.clientY <= bounds.bottom + BOUNDARY_OFFSET;
  }
  function getPseudoElementBounds(element) {
    const elementRect = element.getBoundingClientRect();
    const win = getWindow(element);
    if (parts_exports.env.jsdom) {
      return elementRect;
    }
    const beforeStyles = win.getComputedStyle(element, "::before");
    const afterStyles = win.getComputedStyle(element, "::after");
    const hasPseudoElements = beforeStyles.content !== "none" || afterStyles.content !== "none";
    if (!hasPseudoElements) {
      return elementRect;
    }
    const beforeWidth = parseFloat(beforeStyles.width) || 0;
    const beforeHeight = parseFloat(beforeStyles.height) || 0;
    const afterWidth = parseFloat(afterStyles.width) || 0;
    const afterHeight = parseFloat(afterStyles.height) || 0;
    const totalWidth = Math.max(elementRect.width, beforeWidth, afterWidth);
    const totalHeight = Math.max(elementRect.height, beforeHeight, afterHeight);
    const widthDiff = totalWidth - elementRect.width;
    const heightDiff = totalHeight - elementRect.height;
    return {
      left: elementRect.left - widthDiff / 2,
      right: elementRect.right + widthDiff / 2,
      top: elementRect.top - heightDiff / 2,
      bottom: elementRect.bottom + heightDiff / 2
    };
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/resolveAriaLabelledBy.mjs
  function getDefaultLabelId(id) {
    return id == null ? void 0 : `${id}-label`;
  }
  function resolveAriaLabelledBy(fieldLabelId, localLabelId) {
    return fieldLabelId ?? localLabelId;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useAnchorPositioning.mjs
  var React45 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/hooks/useFloating.mjs
  var React44 = __toESM(require_react(), 1);
  function useBaseUIFloating(options) {
    const {
      nodeId,
      externalTree,
      rootContext: store
    } = options;
    const referenceElement = store.useState("referenceElement");
    const floatingElement = store.useState("floatingElement");
    const domReferenceElement = store.useState("domReferenceElement");
    const open2 = store.useState("open");
    const floatingId = store.useState("floatingId");
    const [positionReference, setPositionReferenceRaw] = React44.useState(null);
    const domReferenceRef = React44.useRef(null);
    const tree = useFloatingTree(externalTree);
    const storeElements = React44.useMemo(() => ({
      reference: referenceElement,
      floating: floatingElement,
      domReference: domReferenceElement
    }), [referenceElement, floatingElement, domReferenceElement]);
    const position = useFloating({
      ...options,
      elements: {
        ...storeElements,
        ...positionReference && {
          reference: positionReference
        }
      }
    });
    const setPositionReference = React44.useCallback((node) => {
      const computedPositionReference = isElement(node) ? {
        getBoundingClientRect: () => node.getBoundingClientRect(),
        getClientRects: () => node.getClientRects(),
        contextElement: node
      } : node;
      setPositionReferenceRaw(computedPositionReference);
      position.refs.setReference(computedPositionReference);
    }, [position.refs]);
    const refs = React44.useMemo(() => ({
      ...position.refs,
      setPositionReference,
      domReference: domReferenceRef
    }), [position.refs, setPositionReference]);
    const elements = React44.useMemo(() => ({
      ...position.elements,
      domReference: domReferenceElement
    }), [position.elements, domReferenceElement]);
    const context = React44.useMemo(() => ({
      ...position,
      dataRef: store.context.dataRef,
      open: open2,
      onOpenChange: store.setOpen,
      events: store.context.events,
      floatingId,
      refs,
      elements,
      nodeId,
      rootStore: store
    }), [position, refs, elements, nodeId, store, open2, floatingId]);
    useIsoLayoutEffect(() => {
      if (domReferenceElement) {
        domReferenceRef.current = domReferenceElement;
      }
    }, [domReferenceElement]);
    useIsoLayoutEffect(() => {
      store.context.dataRef.current.floatingContext = context;
      const node = tree?.nodesRef.current.find((n) => n.id === nodeId);
      if (node) {
        node.context = context;
      }
    });
    return React44.useMemo(() => ({
      ...position,
      context,
      refs,
      elements,
      rootStore: store
    }), [position, refs, elements, context, store]);
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/floating-ui-react/middleware/arrow.mjs
  var baseArrow = (options) => ({
    name: "arrow",
    options,
    async fn(state) {
      const {
        x,
        y,
        placement,
        rects,
        platform: platform3,
        elements,
        middlewareData
      } = state;
      const {
        element,
        padding = 0
      } = evaluate(options, state) || {};
      if (element == null) {
        return {};
      }
      const paddingObject = getPaddingObject(padding);
      const coords = {
        x,
        y
      };
      const axis = getAlignmentAxis(placement);
      const length = getAxisLength(axis);
      const arrowDimensions = await platform3.getDimensions(element);
      const isYAxis = axis === "y";
      const minProp = isYAxis ? "top" : "left";
      const maxProp = isYAxis ? "bottom" : "right";
      const clientProp = isYAxis ? "clientHeight" : "clientWidth";
      const endDiff = rects.reference[length] + rects.reference[axis] - coords[axis] - rects.floating[length];
      const startDiff = coords[axis] - rects.reference[axis];
      const clientSize = elements.floating[clientProp] || rects.floating[length];
      const centerToReference = endDiff / 2 - startDiff / 2;
      const largestPossiblePadding = clientSize / 2 - arrowDimensions[length] / 2 - 1;
      const minPadding = Math.min(paddingObject[minProp], largestPossiblePadding);
      const maxPadding = Math.min(paddingObject[maxProp], largestPossiblePadding);
      const min2 = minPadding;
      const max2 = clientSize - arrowDimensions[length] - maxPadding;
      const center = clientSize / 2 - arrowDimensions[length] / 2 + centerToReference;
      const offset4 = clamp(min2, center, max2);
      const shouldAddOffset = !middlewareData.arrow && getAlignment(placement) != null && center !== offset4 && rects.reference[length] / 2 - (center < min2 ? minPadding : maxPadding) - arrowDimensions[length] / 2 < 0;
      const alignmentOffset = shouldAddOffset ? center < min2 ? center - min2 : center - max2 : 0;
      return {
        [axis]: coords[axis] + alignmentOffset,
        data: {
          [axis]: offset4,
          centerOffset: center - offset4 - alignmentOffset,
          ...shouldAddOffset && {
            alignmentOffset
          }
        },
        reset: shouldAddOffset
      };
    }
  });
  var arrow4 = (options, deps) => {
    const {
      name: name16,
      fn
    } = baseArrow(options);
    return {
      name: name16,
      fn,
      options: [options, deps]
    };
  };

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/hideMiddleware.mjs
  var hide4 = {
    name: "hide",
    async fn(state) {
      const {
        width,
        height,
        x,
        y
      } = state.rects.reference;
      const anchorHidden2 = width === 0 && height === 0 && x === 0 && y === 0;
      const overflow = await state.platform.detectOverflow(state, {
        elementContext: "reference"
      });
      const referenceHidden = overflow.top - height >= 0 || overflow.right - width >= 0 || overflow.bottom - height >= 0 || overflow.left - width >= 0;
      return {
        data: {
          referenceHidden: referenceHidden || anchorHidden2
        }
      };
    }
  };

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/adaptiveOriginConstants.mjs
  var DEFAULT_SIDES = {
    sideX: "left",
    sideY: "top"
  };

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/CommonPositionerCssVars.mjs
  var availableWidth = "--available-width";
  var availableHeight = "--available-height";
  var anchorWidth = "--anchor-width";
  var anchorHeight = "--anchor-height";
  var transformOrigin = "--transform-origin";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/useAnchorPositioning.mjs
  var AVAILABLE_WIDTH_VAR = availableWidth;
  var AVAILABLE_HEIGHT_VAR = availableHeight;
  function getLogicalSide(sideParam, renderedSide, isRtl) {
    const isLogicalSideParam = sideParam === "inline-start" || sideParam === "inline-end";
    const logicalRight = isRtl ? "inline-start" : "inline-end";
    const logicalLeft = isRtl ? "inline-end" : "inline-start";
    return {
      top: "top",
      right: isLogicalSideParam ? logicalRight : "right",
      bottom: "bottom",
      left: isLogicalSideParam ? logicalLeft : "left"
    }[renderedSide];
  }
  function getOffsetData(state, sideParam, isRtl) {
    const {
      rects,
      placement
    } = state;
    const data = {
      side: getLogicalSide(sideParam, getSide(placement), isRtl),
      align: getAlignment(placement) || "center",
      anchor: {
        width: rects.reference.width,
        height: rects.reference.height
      },
      positioner: {
        width: rects.floating.width,
        height: rects.floating.height
      }
    };
    return data;
  }
  function useAnchorPositioning(params) {
    return useAnchorPositioningWithHook(params, useBaseUIFloating);
  }
  function useAnchorPositioningWithHook(params, useFloatingHook) {
    const {
      // Public parameters
      anchor,
      positionMethod = "absolute",
      side: sideParam = "bottom",
      sideOffset = 0,
      align = "center",
      alignOffset = 0,
      collisionBoundary,
      collisionPadding: collisionPaddingParam = 5,
      sticky = false,
      arrowPadding = 5,
      disableAnchorTracking = false,
      inline: inlineMiddleware,
      // Private parameters
      keepMounted = false,
      floatingRootContext,
      mounted,
      collisionAvoidance,
      shift: shift4,
      nodeId,
      adaptiveOrigin,
      lazyFlip = false,
      externalTree
    } = params;
    const collisionAvoidanceSide = collisionAvoidance.side || "flip";
    const collisionAvoidanceAlign = collisionAvoidance.align || "flip";
    const collisionAvoidanceFallbackAxisSide = collisionAvoidance.fallbackAxisSide || "end";
    const shiftCrossAxis = shift4?.crossAxis ?? false;
    const shiftRootBoundary = shift4?.rootBoundary;
    const anchorFn = typeof anchor === "function" ? anchor : void 0;
    const anchorFnCallback = useStableCallback(anchorFn);
    const anchorDep = anchorFn ? anchorFnCallback : anchor;
    const anchorValueRef = useValueAsRef(anchor);
    const mountedRef = useValueAsRef(mounted);
    const direction = useDirection();
    const isRtl = direction === "rtl";
    const [mountPlacement, setMountPlacement] = React45.useState(null);
    if (!mounted && mountPlacement !== null) {
      setMountPlacement(null);
    }
    const lockAlign = lazyFlip === "placement";
    const mountSide = mountPlacement ? getSide(mountPlacement) : null;
    const mountAlign = mountPlacement && lockAlign ? getAlignment(mountPlacement) || "center" : null;
    const side = mountSide || {
      top: "top",
      right: "right",
      bottom: "bottom",
      left: "left",
      "inline-end": isRtl ? "left" : "right",
      "inline-start": isRtl ? "right" : "left"
    }[sideParam];
    const placementAlign = mountAlign || align;
    const placement = placementAlign === "center" ? side : `${side}-${placementAlign}`;
    let collisionPadding = collisionPaddingParam;
    if (typeof collisionPadding === "number") {
      collisionPadding = {
        top: collisionPadding,
        right: collisionPadding,
        bottom: collisionPadding,
        left: collisionPadding
      };
    } else if (collisionPadding) {
      collisionPadding = {
        top: collisionPadding.top || 0,
        right: collisionPadding.right || 0,
        bottom: collisionPadding.bottom || 0,
        left: collisionPadding.left || 0
      };
    }
    const bias = 1;
    const biasTop = sideParam === "bottom" ? bias : 0;
    const biasBottom = sideParam === "top" ? bias : 0;
    const biasLeft = sideParam === "right" ? bias : 0;
    const biasRight = sideParam === "left" ? bias : 0;
    const commonCollisionProps = {
      boundary: collisionBoundary === "clipping-ancestors" ? "clippingAncestors" : collisionBoundary,
      padding: collisionPadding
    };
    const arrowRef = React45.useRef(null);
    const sideOffsetRef = useValueAsRef(sideOffset);
    const alignOffsetRef = useValueAsRef(alignOffset);
    const sideOffsetDep = typeof sideOffset !== "function" ? sideOffset : 0;
    const alignOffsetDep = typeof alignOffset !== "function" ? alignOffset : 0;
    const middleware = [];
    if (inlineMiddleware) {
      middleware.push(inlineMiddleware);
    }
    middleware.push(offset3((state) => {
      const data = getOffsetData(state, sideParam, isRtl);
      const sideAxis = typeof sideOffsetRef.current === "function" ? sideOffsetRef.current(data) : sideOffsetRef.current;
      const alignAxis = typeof alignOffsetRef.current === "function" ? alignOffsetRef.current(data) : alignOffsetRef.current;
      return {
        mainAxis: sideAxis,
        crossAxis: alignAxis,
        alignmentAxis: alignAxis
      };
    }, [sideOffsetDep, alignOffsetDep, isRtl, sideParam]));
    const shiftDisabled = collisionAvoidanceAlign === "none" && collisionAvoidanceSide !== "shift";
    const crossAxisShiftEnabled = !shiftDisabled && (sticky || shiftCrossAxis || collisionAvoidanceSide === "shift");
    const flipMiddleware = collisionAvoidanceSide === "none" ? null : flip3({
      ...commonCollisionProps,
      // Ensure the popup flips if it's been limited by its --available-height and it resizes.
      // Since the size() padding is smaller than the flip() padding, flip() will take precedence.
      padding: {
        top: collisionPadding.top + bias + biasTop,
        right: collisionPadding.right + bias + biasRight,
        bottom: collisionPadding.bottom + bias + biasBottom,
        left: collisionPadding.left + bias + biasLeft
      },
      mainAxis: !shiftCrossAxis && collisionAvoidanceSide === "flip",
      crossAxis: collisionAvoidanceAlign === "flip" ? "alignment" : false,
      fallbackAxisSideDirection: collisionAvoidanceFallbackAxisSide
    });
    const shiftMiddleware = shiftDisabled ? null : shift3({
      ...commonCollisionProps,
      // Use the Layout Viewport to avoid shifting around when pinch-zooming.
      rootBoundary: shiftRootBoundary,
      mainAxis: collisionAvoidanceAlign !== "none",
      crossAxis: crossAxisShiftEnabled,
      limiter: sticky || shiftCrossAxis ? void 0 : limitShift3((limitData) => {
        if (!arrowRef.current) {
          return {};
        }
        const {
          width,
          height
        } = arrowRef.current.getBoundingClientRect();
        const sideAxis = getSideAxis(getSide(limitData.placement));
        const arrowSize = sideAxis === "y" ? width : height;
        const offsetAmount = sideAxis === "y" ? collisionPadding.left + collisionPadding.right : collisionPadding.top + collisionPadding.bottom;
        return {
          offset: arrowSize / 2 + offsetAmount / 2
        };
      })
    }, [commonCollisionProps, sticky, shiftCrossAxis, shiftRootBoundary, collisionPadding, collisionAvoidanceAlign]);
    if (collisionAvoidanceSide === "shift" || collisionAvoidanceAlign === "shift" || placementAlign === "center") {
      middleware.push(shiftMiddleware, flipMiddleware);
    } else {
      middleware.push(flipMiddleware, shiftMiddleware);
    }
    middleware.push(size3({
      ...commonCollisionProps,
      apply({
        elements: {
          floating
        },
        availableWidth: availableWidth2,
        availableHeight: availableHeight2,
        rects
      }) {
        if (!mountedRef.current) {
          return;
        }
        const floatingStyle = floating.style;
        floatingStyle.setProperty(AVAILABLE_WIDTH_VAR, `${availableWidth2}px`);
        floatingStyle.setProperty(AVAILABLE_HEIGHT_VAR, `${availableHeight2}px`);
        const dpr = getWindow(floating).devicePixelRatio || 1;
        const {
          x: x2,
          y: y2,
          width,
          height
        } = rects.reference;
        const anchorWidth2 = (Math.round((x2 + width) * dpr) - Math.round(x2 * dpr)) / dpr;
        const anchorHeight2 = (Math.round((y2 + height) * dpr) - Math.round(y2 * dpr)) / dpr;
        floatingStyle.setProperty(anchorWidth, `${anchorWidth2}px`);
        floatingStyle.setProperty(anchorHeight, `${anchorHeight2}px`);
      }
    }), arrow4((state) => ({
      // `transform-origin` calculations rely on an element existing. If the arrow hasn't been set,
      // we'll create a fake element.
      element: arrowRef.current || ownerDocument(state.elements.floating).createElement("div"),
      // No padding for the fake arrow: it would displace aligned popups on narrow anchors.
      padding: arrowRef.current ? arrowPadding : 0
    }), [arrowPadding]), {
      name: "transformOrigin",
      fn(state) {
        const {
          elements: {
            floating
          },
          middlewareData: middlewareData2,
          placement: renderedPlacement2,
          platform: platform3,
          rects,
          y: y2
        } = state;
        const renderedSide2 = getSide(renderedPlacement2);
        const renderedAlign2 = getAlignment(renderedPlacement2);
        const isVertical = getSideAxis(renderedSide2) === "y";
        const arrowEl = arrowRef.current;
        const sideOffsetValue = typeof sideOffset === "function" ? sideOffset(getOffsetData(state, sideParam, isRtl)) : sideOffset;
        let crossOrigin;
        if (!arrowEl && renderedAlign2 && Math.abs(isVertical ? middlewareData2.shift?.x || 0 : middlewareData2.shift?.y || 0) <= 1) {
          crossOrigin = renderedAlign2 === "start" === (isVertical && platform3.isRTL?.(floating) === true) ? "100%" : "0%";
        } else {
          const arrowOffset = isVertical ? middlewareData2.arrow?.x || 0 : middlewareData2.arrow?.y || 0;
          const arrowSize = isVertical ? arrowEl?.clientWidth || 0 : arrowEl?.clientHeight || 0;
          crossOrigin = `${arrowOffset + arrowSize / 2}px`;
        }
        let sideOrigin = renderedSide2 === "top" || renderedSide2 === "left" ? `calc(100% + ${sideOffsetValue}px)` : `${-sideOffsetValue}px`;
        if (crossAxisShiftEnabled && isVertical && Math.abs(middlewareData2.shift?.y || 0) > sideOffsetValue) {
          sideOrigin = `${rects.reference.y + rects.reference.height / 2 - y2}px`;
        }
        floating.style.setProperty(transformOrigin, isVertical ? `${crossOrigin} ${sideOrigin}` : `${sideOrigin} ${crossOrigin}`);
        return {};
      }
    }, hide4, adaptiveOrigin);
    useIsoLayoutEffect(() => {
      if (!mounted && floatingRootContext) {
        floatingRootContext.update({
          referenceElement: null,
          floatingElement: null,
          domReferenceElement: null,
          positionReference: null
        });
      }
    }, [mounted, floatingRootContext]);
    const autoUpdateOptions = React45.useMemo(() => ({
      ancestorScroll: !disableAnchorTracking,
      elementResize: !disableAnchorTracking && typeof ResizeObserver !== "undefined",
      layoutShift: !disableAnchorTracking && typeof IntersectionObserver !== "undefined"
    }), [disableAnchorTracking]);
    const {
      refs,
      elements,
      x,
      y,
      middlewareData,
      update: update2,
      placement: renderedPlacement,
      context,
      isPositioned,
      floatingStyles: originalFloatingStyles
    } = useFloatingHook({
      rootContext: floatingRootContext,
      open: keepMounted ? mounted : void 0,
      placement,
      middleware,
      strategy: positionMethod,
      whileElementsMounted: keepMounted ? void 0 : (...args) => autoUpdate(...args, autoUpdateOptions),
      nodeId,
      externalTree
    });
    const {
      sideX,
      sideY
    } = middlewareData.adaptiveOrigin || DEFAULT_SIDES;
    const resolvedPosition = isPositioned ? positionMethod : "fixed";
    const floatingStyles = React45.useMemo(() => {
      let base;
      if (!isPositioned) {
        base = {
          position: resolvedPosition,
          top: 0,
          left: 0
        };
      } else if (adaptiveOrigin) {
        base = {
          position: resolvedPosition,
          [sideX]: x,
          [sideY]: y
        };
      } else {
        base = {
          ...originalFloatingStyles,
          position: resolvedPosition
        };
      }
      base[AVAILABLE_WIDTH_VAR] = "100vw";
      base[AVAILABLE_HEIGHT_VAR] = "100vh";
      if (!isPositioned) {
        base.opacity = 0;
      }
      return base;
    }, [adaptiveOrigin, resolvedPosition, sideX, x, sideY, y, originalFloatingStyles, isPositioned]);
    const registeredPositionReferenceRef = React45.useRef(null);
    useIsoLayoutEffect(() => {
      if (!mounted) {
        return;
      }
      const anchorValue = anchorValueRef.current;
      const resolvedAnchor = typeof anchorValue === "function" ? anchorValue() : anchorValue;
      const unwrappedElement = (isRef(resolvedAnchor) ? resolvedAnchor.current : resolvedAnchor) || null;
      const finalAnchor = unwrappedElement || null;
      if (finalAnchor !== registeredPositionReferenceRef.current) {
        refs.setPositionReference(finalAnchor);
        registeredPositionReferenceRef.current = finalAnchor;
      }
    }, [mounted, refs, anchorDep, anchorValueRef]);
    React45.useEffect(() => {
      if (!mounted) {
        return;
      }
      const anchorValue = anchorValueRef.current;
      if (typeof anchorValue === "function") {
        return;
      }
      if (isRef(anchorValue) && anchorValue.current !== registeredPositionReferenceRef.current) {
        refs.setPositionReference(anchorValue.current);
        registeredPositionReferenceRef.current = anchorValue.current;
      }
    }, [mounted, refs, anchorDep, anchorValueRef]);
    React45.useEffect(() => {
      if (keepMounted && mounted && elements.reference && elements.floating) {
        return autoUpdate(elements.reference, elements.floating, update2, autoUpdateOptions);
      }
      return void 0;
    }, [keepMounted, mounted, elements, update2, autoUpdateOptions]);
    const renderedSide = getSide(renderedPlacement);
    const logicalRenderedSide = getLogicalSide(sideParam, renderedSide, isRtl);
    const renderedAlign = getAlignment(renderedPlacement) || "center";
    const anchorHidden2 = Boolean(middlewareData.hide?.referenceHidden);
    useIsoLayoutEffect(() => {
      if (lazyFlip && mounted && isPositioned && (renderedSide !== side || lockAlign && renderedAlign !== placementAlign)) {
        setMountPlacement(renderedPlacement);
      }
    }, [lazyFlip, lockAlign, mounted, isPositioned, renderedPlacement, renderedSide, renderedAlign, side, placementAlign]);
    const arrowStyles = React45.useMemo(() => ({
      position: "absolute",
      top: middlewareData.arrow?.y,
      left: middlewareData.arrow?.x
    }), [middlewareData.arrow]);
    const arrowUncentered = middlewareData.arrow?.centerOffset !== 0;
    return React45.useMemo(() => ({
      positionerStyles: floatingStyles,
      arrowStyles,
      arrowRef,
      arrowUncentered,
      side: logicalRenderedSide,
      align: renderedAlign,
      physicalSide: renderedSide,
      anchorHidden: anchorHidden2,
      refs,
      context,
      isPositioned,
      update: update2
    }), [floatingStyles, arrowStyles, arrowRef, arrowUncentered, logicalRenderedSide, renderedAlign, renderedSide, anchorHidden2, refs, context, isPositioned, update2]);
  }
  function isRef(param) {
    return param != null && "current" in param;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/getDisabledMountTransitionStyles.mjs
  function getDisabledMountTransitionStyles(transitionStatus) {
    return transitionStatus === "starting" ? DISABLED_TRANSITIONS_STYLE : EMPTY_OBJECT;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/usePositioner.mjs
  function usePositioner(componentProps, state, {
    styles,
    transitionStatus,
    props,
    refs,
    hidden,
    inert = false
  }) {
    const style = {
      ...styles
    };
    if (inert) {
      style.pointerEvents = "none";
    }
    return useRenderElement("div", componentProps, {
      state,
      ref: refs,
      props: [{
        role: "presentation",
        hidden,
        style
      }, getDisabledMountTransitionStyles(transitionStatus), props],
      stateAttributesMapping: popupStateMapping
    });
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/useAnchoredPopupScrollLock.mjs
  var React46 = __toESM(require_react(), 1);
  var VIEWPORT_WIDTH_TOLERANCE_PX = 20;
  function useAnchoredPopupScrollLock(enabled, touchOpen, positionerElement, referenceElement) {
    const [touchOpenShouldLockScroll, setTouchOpenShouldLockScroll] = React46.useState(false);
    useIsoLayoutEffect(() => {
      if (!enabled || !touchOpen || positionerElement == null) {
        setTouchOpenShouldLockScroll(false);
        return;
      }
      const viewportWidth = ownerDocument(positionerElement).documentElement.clientWidth;
      const popupWidth = positionerElement.offsetWidth;
      setTouchOpenShouldLockScroll(viewportWidth > 0 && popupWidth > 0 && popupWidth >= viewportWidth - VIEWPORT_WIDTH_TOLERANCE_PX);
    }, [enabled, touchOpen, positionerElement]);
    useScrollLock(enabled && (!touchOpen || touchOpenShouldLockScroll), referenceElement);
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/listbox-separator/ListboxSeparator.mjs
  var React47 = __toESM(require_react(), 1);
  var ListboxSeparator = /* @__PURE__ */ React47.forwardRef(function ListboxSeparator2(componentProps, forwardedRef) {
    const {
      className,
      render,
      orientation = "horizontal",
      style,
      ...elementProps
    } = componentProps;
    const state = {
      orientation
    };
    return useRenderElement("div", componentProps, {
      state,
      ref: forwardedRef,
      props: [{
        role: "presentation"
      }, elementProps]
    });
  });
  if (true) ListboxSeparator.displayName = "ListboxSeparator";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/item/FieldItemContext.mjs
  var React48 = __toESM(require_react(), 1);
  var FieldItemContext = /* @__PURE__ */ React48.createContext({
    disabled: false
  });
  if (true) FieldItemContext.displayName = "FieldItemContext";
  function useFieldItemContext() {
    const context = React48.useContext(FieldItemContext);
    return context;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/root/useFieldValidation.mjs
  var React49 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/utils/getCombinedFieldValidityData.mjs
  function getCombinedFieldValidityData(validityData, invalid2) {
    return {
      ...validityData,
      state: {
        ...validityData.state,
        valid: !invalid2 && validityData.state.valid
      }
    };
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/root/useFieldValidation.mjs
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
    const inputRef = React49.useRef(null);
    const registeredInputs = useRefWithInit(() => /* @__PURE__ */ new Map()).current;
    const validationCommitIdRef = React49.useRef(0);
    const customValidityRef = React49.useRef(null);
    const registerInput = React49.useCallback((element, registration) => {
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
    const getValidationProps = React49.useCallback((disabled2, externalProps = {}) => mergeProps(getDescriptionProps(externalProps), state.valid === false && !state.disabled && !disabled2 ? {
      "aria-invalid": true
    } : EMPTY_OBJECT), [getDescriptionProps, state.disabled, state.valid]);
    return React49.useMemo(() => ({
      getValidationProps,
      inputRef,
      registeredInputs,
      registerInput,
      getInputControl,
      commit,
      change
    }), [getValidationProps, registeredInputs, registerInput, getInputControl, commit, change]);
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/useRegisteredLabelId.mjs
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/labelable-provider/useLabel.mjs
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
      const controlElement = getControlById(event.currentTarget, resolvedControlId);
      if (isHTMLElement(controlElement)) {
        focusElementWithVisible(controlElement);
      }
    }
    function handleInteraction(event) {
      const target = getTarget(event.nativeEvent);
      if (closest(target, "button,input,select,textarea")) {
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
  function getControlById(label, id) {
    let root = label.getRootNode();
    while (isShadowRoot(root)) {
      const element = root.getElementById(id);
      if (element) {
        return element;
      }
      root = root.host.getRootNode();
    }
    return ownerDocument(label).getElementById(id);
  }
  function focusElementWithVisible(element) {
    element.focus({
      // Available from Chrome 144+ (January 2026).
      // Safari and Firefox already support it.
      focusVisible: true
    });
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/ItemIndicator.mjs
  var React50 = __toESM(require_react(), 1);
  var ItemIndicator = /* @__PURE__ */ React50.memo(/* @__PURE__ */ React50.forwardRef(function ItemIndicator2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      keepMounted,
      selected,
      ...elementProps
    } = componentProps;
    const indicatorRef = React50.useRef(null);
    const {
      transitionStatus,
      setMounted
    } = useTransitionStatus(selected);
    const state = {
      selected,
      transitionStatus
    };
    const element = useRenderElement("span", componentProps, {
      ref: [forwardedRef, indicatorRef],
      state,
      props: [{
        "aria-hidden": true,
        children: "\u2714\uFE0F"
      }, elementProps],
      stateAttributesMapping: transitionStatusMapping
    });
    useOpenChangeComplete({
      batch: true,
      enabled: !selected,
      open: selected,
      ref: indicatorRef,
      onComplete() {
        if (!selected) {
          setMounted(false);
        }
      }
    });
    return element;
  }));
  if (true) ItemIndicator.displayName = "ItemIndicator";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/toolbar/root/ToolbarRootContext.mjs
  var React51 = __toESM(require_react(), 1);
  var ToolbarRootContext = /* @__PURE__ */ React51.createContext(void 0);
  if (true) ToolbarRootContext.displayName = "ToolbarRootContext";
  function useToolbarRootContext(optional) {
    const context = React51.useContext(ToolbarRootContext);
    if (context === void 0 && !optional) {
      throw new Error(true ? "Base UI: ToolbarRootContext is missing. Toolbar parts must be placed within <Toolbar.Root>." : formatErrorMessage_default(69));
    }
    return context;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/composite/item/useCompositeItem.mjs
  var React52 = __toESM(require_react(), 1);
  function useCompositeItem(params = {}) {
    const {
      highlightItemOnHover,
      highlightedIndex,
      onHighlightedIndexChange
    } = useCompositeRootContext();
    const {
      ref,
      index: index3
    } = useCompositeListItem(params);
    const isHighlighted = highlightedIndex === index3;
    const itemRef = React52.useRef(null);
    const mergedRef = useMergedRefs(ref, itemRef);
    const compositeProps = {
      tabIndex: isHighlighted ? 0 : -1,
      onFocus() {
        onHighlightedIndexChange(index3);
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
      index: index3
    };
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/usePreviousValue.mjs
  var React53 = __toESM(require_react(), 1);
  function usePreviousValue(value) {
    const [state, setState] = React53.useState({
      current: value,
      previous: null
    });
    if (!Object.is(value, state.current)) {
      setState({
        current: value,
        previous: state.current
      });
    }
    return state.previous;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/getCssDimensions.mjs
  function getCssDimensions2(element) {
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/direction-provider/DirectionProvider.mjs
  var React54 = __toESM(require_react(), 1);
  var import_jsx_runtime29 = __toESM(require_jsx_runtime(), 1);
  var DirectionProvider = function DirectionProvider2(props) {
    const {
      direction = "ltr"
    } = props;
    const contextValue = React54.useMemo(() => ({
      direction
    }), [direction]);
    return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(DirectionContext.Provider, {
      value: contextValue,
      children: props.children
    });
  };
  if (true) DirectionProvider.displayName = "DirectionProvider";

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/isElementDisabled.mjs
  function isElementDisabled(element) {
    return element == null || element.hasAttribute("disabled") || element.getAttribute("aria-disabled") === "true";
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/csp-context/CSPContext.mjs
  var React55 = __toESM(require_react(), 1);
  var CSPContext = /* @__PURE__ */ React55.createContext(void 0);
  if (true) CSPContext.displayName = "CSPContext";
  var DEFAULT_CSP_CONTEXT_VALUE = {
    disableStyleElements: false
  };
  function useCSPContext() {
    return React55.useContext(CSPContext) ?? DEFAULT_CSP_CONTEXT_VALUE;
  }

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/clamp.mjs
  function clamp2(val, min2 = Number.MIN_SAFE_INTEGER, max2 = Number.MAX_SAFE_INTEGER) {
    return Math.max(min2, Math.min(val, max2));
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/getElementTransform.mjs
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/index.parts.mjs
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/root/FieldRoot.mjs
  var React59 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/fieldset/root/FieldsetRootContext.mjs
  var React56 = __toESM(require_react(), 1);
  var FieldsetRootContext = /* @__PURE__ */ React56.createContext(void 0);
  if (true) FieldsetRootContext.displayName = "FieldsetRootContext";
  function useFieldsetRootContext(optional = false) {
    const context = React56.useContext(FieldsetRootContext);
    if (!context && !optional) {
      throw new Error(true ? "Base UI: FieldsetRootContext is missing. Fieldset parts must be placed within <Fieldset.Root>." : formatErrorMessage_default(86));
    }
    return context;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/labelable-provider/LabelableProvider.mjs
  var React57 = __toESM(require_react(), 1);
  var import_jsx_runtime30 = __toESM(require_jsx_runtime(), 1);
  var LabelableProvider = function LabelableProvider2(props) {
    const defaultId = useBaseUiId();
    const [controlIdState, setControlIdState] = React57.useState(defaultId);
    const [labelId, setLabelId] = React57.useState();
    const [messageIds, setMessageIds] = React57.useState([]);
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
    const getDescriptionProps = React57.useCallback((externalProps) => {
      const ids = externalProps["aria-describedby"] ? externalProps["aria-describedby"].split(" ") : [];
      ids.push(...parentMessageIds, ...messageIds);
      return {
        ...externalProps,
        "aria-describedby": Array.from(new Set(ids)).join(" ") || void 0
      };
    }, [parentMessageIds, messageIds]);
    const contextValue = React57.useMemo(() => ({
      controlId,
      registerControlId,
      resetControlId,
      labelId,
      setLabelId,
      messageIds,
      setMessageIds,
      getDescriptionProps
    }), [controlId, registerControlId, resetControlId, labelId, setLabelId, messageIds, setMessageIds, getDescriptionProps]);
    return /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(LabelableContext.Provider, {
      value: contextValue,
      children: props.children
    });
  };
  if (true) LabelableProvider.displayName = "LabelableProvider";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/field-register-control/useFieldControlRegistration.mjs
  var React58 = __toESM(require_react(), 1);
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
    const activeFieldControlSourceRef = React58.useRef(null);
    const registrationRef = React58.useRef(null);
    const initialValueCapturedRef = React58.useRef(false);
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
    const register2 = useStableCallback((source, registration) => {
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
    return [validate, register2];
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/root/FieldRoot.mjs
  var import_jsx_runtime31 = __toESM(require_jsx_runtime(), 1);
  var FieldRootInner = /* @__PURE__ */ React59.forwardRef(function FieldRootInner2(componentProps, forwardedRef) {
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
    const [touchedState, setTouchedUnwrapped] = React59.useState(false);
    const [dirtyState, setDirtyUnwrapped] = React59.useState(false);
    const [filled, setFilled] = React59.useState(false);
    const [focused, setFocused] = React59.useState(false);
    const focusOwnerRef = React59.useRef(void 0);
    const dirty = dirtyProp ?? dirtyState;
    const touched = touchedProp ?? touchedState;
    const markedDirtyRef = React59.useRef(dirty);
    const registeredFieldIdRef = React59.useRef(void 0);
    const [registeredFieldName, setRegisteredFieldName] = React59.useState();
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
    const [validityData, setValidityData] = React59.useState({
      state: DEFAULT_VALIDITY_STATE,
      error: "",
      errors: [],
      value: null,
      initialValue: null
    });
    const valid2 = !invalid2 && (disabled2 ? null : validityData.state.valid);
    const state = React59.useMemo(() => ({
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
    React59.useImperativeHandle(actionsRef, () => ({
      validate: validateFieldControl
    }), [validateFieldControl]);
    const contextValue = React59.useMemo(() => ({
      invalid: invalid2,
      name: effectiveName,
      validityData,
      setValidityData,
      disabled: disabled2,
      setTouched,
      setDirty,
      setFilled,
      setFocused,
      focusOwnerRef,
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
    return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(FieldRootContext.Provider, {
      value: contextValue,
      children: element
    });
  });
  if (true) FieldRootInner.displayName = "FieldRootInner";
  var FieldRoot = /* @__PURE__ */ React59.forwardRef(function FieldRoot2(componentProps, forwardedRef) {
    return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(LabelableProvider, {
      children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(FieldRootInner, {
        ...componentProps,
        ref: forwardedRef
      })
    });
  });
  if (true) FieldRoot.displayName = "FieldRoot";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/label/FieldLabel.mjs
  var React60 = __toESM(require_react(), 1);
  var FieldLabel = /* @__PURE__ */ React60.forwardRef(function FieldLabel2(componentProps, forwardedRef) {
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
    const labelRef = React60.useRef(null);
    const labelProps = useLabel({
      id: labelId ?? idProp,
      native: nativeLabel
    });
    if (true) {
      React60.useEffect(() => {
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/error/FieldError.mjs
  var React61 = __toESM(require_react(), 1);
  var import_jsx_runtime32 = __toESM(require_jsx_runtime(), 1);
  var stateAttributesMapping = {
    ...fieldValidityMapping,
    ...transitionStatusMapping
  };
  var FieldError = /* @__PURE__ */ React61.forwardRef(function FieldError2(componentProps, forwardedRef) {
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
    const errorRef = React61.useRef(null);
    const [lastRenderedMessage, setLastRenderedMessage] = React61.useState(null);
    const [lastRenderedMessageKey, setLastRenderedMessageKey] = React61.useState(null);
    let error2 = validityData.error;
    if (!hasSpecificMatch && hasFormError) {
      error2 = formError;
    } else if (validityData.errors.length > 1) {
      error2 = validityData.errors;
    }
    let errorMessage = error2;
    if (Array.isArray(error2)) {
      errorMessage = error2.length > 1 ? /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("ul", {
        children: error2.map((message) => /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("li", {
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/description/FieldDescription.mjs
  var React62 = __toESM(require_react(), 1);
  var FieldDescription = /* @__PURE__ */ React62.forwardRef(function FieldDescription2(componentProps, forwardedRef) {
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/control/FieldControl.mjs
  var React63 = __toESM(require_react(), 1);
  var FieldControl = /* @__PURE__ */ React63.forwardRef(function FieldControl2(componentProps, forwardedRef) {
    const {
      render,
      className,
      id: idProp,
      name: nameProp,
      value,
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
    const isControlled = value !== void 0;
    const serializedValue = value == null ? void 0 : String(value);
    const getValueFromInput = useStableCallback(() => validation.inputRef.current?.value);
    useRegisterFieldControl(validation.inputRef, id, serializedValue, getValueFromInput, !disabled2, nameProp);
    useIsoLayoutEffect(() => {
      const currentValue = serializedValue ?? validation.inputRef.current?.value;
      if (currentValue !== void 0) {
        setFilled(currentValue !== "");
      }
    }, [serializedValue, defaultValue, validation.inputRef, setFilled]);
    useValueChanged(serializedValue, () => {
      if (serializedValue === void 0) {
        return;
      }
      clearErrors(name16);
      setDirty(serializedValue !== (validityData.initialValue ?? ""));
      validation.change(serializedValue);
    });
    const inputRef = React63.useRef(null);
    const setFocused = useSetFieldFocused(disabled2, inputRef);
    const enterValidationTimeout = useTimeout();
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/validity/FieldValidity.mjs
  var React64 = __toESM(require_react(), 1);
  var import_jsx_runtime33 = __toESM(require_jsx_runtime(), 1);
  var FieldValidity = function FieldValidity2(props) {
    const {
      children
    } = props;
    const {
      validityData,
      invalid: invalid2
    } = useFieldRootContext(false);
    const combinedFieldValidityData = React64.useMemo(() => getCombinedFieldValidityData(validityData, invalid2), [validityData, invalid2]);
    const isInvalid = combinedFieldValidityData.state.valid === false;
    const {
      transitionStatus
    } = useTransitionStatus(isInvalid);
    const fieldValidityState = React64.useMemo(() => {
      return {
        ...combinedFieldValidityData,
        validity: combinedFieldValidityData.state,
        transitionStatus
      };
    }, [combinedFieldValidityData, transitionStatus]);
    return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(React64.Fragment, {
      children: children(fieldValidityState)
    });
  };
  if (true) FieldValidity.displayName = "FieldValidity";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/field/item/FieldItem.mjs
  var React65 = __toESM(require_react(), 1);
  var import_jsx_runtime34 = __toESM(require_jsx_runtime(), 1);
  var FieldItem = /* @__PURE__ */ React65.forwardRef(function FieldItem2(componentProps, forwardedRef) {
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
    const fieldItemContext = React65.useMemo(() => ({
      disabled: disabled2
    }), [disabled2]);
    const element = useRenderElement("div", componentProps, {
      ref: forwardedRef,
      state,
      props: elementProps,
      stateAttributesMapping: fieldValidityMapping
    });
    return /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(LabelableProvider, {
      children: /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(FieldItemContext.Provider, {
        value: fieldItemContext,
        children: element
      })
    });
  });
  if (true) FieldItem.displayName = "FieldItem";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/input/Input.mjs
  var React66 = __toESM(require_react(), 1);
  var import_jsx_runtime35 = __toESM(require_jsx_runtime(), 1);
  var Input = /* @__PURE__ */ React66.forwardRef(function Input2(props, forwardedRef) {
    return /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(index_parts_exports.Control, {
      ref: forwardedRef,
      ...props
    });
  });
  if (true) Input.displayName = "Input";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/composite/root/CompositeRoot.mjs
  var React68 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/composite/root/useCompositeRoot.mjs
  var React67 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/composite/constants.mjs
  var ACTIVE_COMPOSITE_ITEM = "data-composite-item-active";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/composite/root/useCompositeRoot.mjs
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
    const [internalHighlightedIndex, internalSetHighlightedIndex] = React67.useState(0);
    const isGrid = grid != null;
    const rootRef = React67.useRef(null);
    const mergedRef = useMergedRefs(rootRef, externalRef);
    const elementsRef = React67.useRef([]);
    const hasSetDefaultIndexRef = React67.useRef(false);
    const highlightedElementRef = React67.useRef(null);
    const highlightedIndex = externalHighlightedIndex ?? internalHighlightedIndex;
    const onHighlightedIndexChange = useStableCallback((index3, shouldScrollIntoView = false) => {
      highlightedElementRef.current = elementsRef.current[index3] ?? null;
      (externalSetHighlightedIndex ?? internalSetHighlightedIndex)(index3);
      if (shouldScrollIntoView) {
        const newActiveItem = elementsRef.current[index3];
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
      const horizontalForwardKey = isRtl ? ARROW_LEFT2 : ARROW_RIGHT2;
      const horizontalBackwardKey = isRtl ? ARROW_RIGHT2 : ARROW_LEFT2;
      const forwardKey = orientation === "vertical" ? ARROW_DOWN2 : horizontalForwardKey;
      const backwardKey = orientation === "vertical" ? ARROW_UP2 : horizontalBackwardKey;
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
      const isForwardKey = orientation !== "vertical" && event.key === horizontalForwardKey || orientation !== "horizontal" && event.key === ARROW_DOWN2;
      const isBackwardKey = orientation !== "vertical" && event.key === horizontalBackwardKey || orientation !== "horizontal" && event.key === ARROW_UP2;
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
    for (let index3 = 0; index3 < elements.length; index3 += 1) {
      const element = elements[index3];
      if (!element || isListIndexDisabled(elements, index3, disabledIndices)) {
        continue;
      }
      if (element.hasAttribute(ACTIVE_COMPOSITE_ITEM)) {
        return index3;
      }
      if (fallbackIndex === -1) {
        fallbackIndex = index3;
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/composite/root/CompositeRoot.mjs
  var import_jsx_runtime36 = __toESM(require_jsx_runtime(), 1);
  function CompositeRoot(componentProps) {
    const {
      render,
      className,
      style,
      refs = EMPTY_ARRAY,
      props = EMPTY_ARRAY,
      state = EMPTY_OBJECT,
      stateAttributesMapping: stateAttributesMapping8,
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
      stateAttributesMapping: stateAttributesMapping8
    });
    const contextValue = React68.useMemo(() => ({
      highlightedIndex,
      onHighlightedIndexChange,
      highlightItemOnHover,
      relayKeyboardEvent
    }), [highlightedIndex, onHighlightedIndexChange, highlightItemOnHover, relayKeyboardEvent]);
    return /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(CompositeRootContext.Provider, {
      value: contextValue,
      children: /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(CompositeList, {
        elementsRef,
        onMapChange: (newMap) => {
          onMapChangeProp?.(newMap);
          onMapChangeUnwrapped(newMap);
        },
        children: element
      })
    });
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/styles.mjs
  var import_jsx_runtime37 = __toESM(require_jsx_runtime(), 1);
  var DISABLE_SCROLLBAR_CLASS_NAME = "base-ui-disable-scrollbar";
  var styleDisableScrollbar = {
    className: DISABLE_SCROLLBAR_CLASS_NAME,
    getElement(nonce) {
      return /* @__PURE__ */ (0, import_jsx_runtime37.jsx)("style", {
        nonce,
        href: DISABLE_SCROLLBAR_CLASS_NAME,
        precedence: "base-ui:low",
        children: `.${DISABLE_SCROLLBAR_CLASS_NAME}{scrollbar-width:none}.${DISABLE_SCROLLBAR_CLASS_NAME}::-webkit-scrollbar{display:none}`
      });
    }
  };
  if (true) styleDisableScrollbar.getElement.displayName = "styleDisableScrollbar.getElement";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/utils/scrollEdges.mjs
  var SCROLL_EDGE_TOLERANCE_PX = 1;
  function getMaxScrollOffset(scrollSize, clientSize) {
    return Math.max(0, scrollSize - clientSize);
  }
  function normalizeScrollOffset(value, max2) {
    if (max2 <= 0) {
      return 0;
    }
    const clamped = clamp2(value, 0, max2);
    const startDistance = clamped;
    const endDistance = max2 - clamped;
    const withinStartTolerance = startDistance <= SCROLL_EDGE_TOLERANCE_PX;
    const withinEndTolerance = endDistance <= SCROLL_EDGE_TOLERANCE_PX;
    if (withinStartTolerance && withinEndTolerance) {
      return startDistance <= endDistance ? 0 : max2;
    }
    if (withinStartTolerance) {
      return 0;
    }
    if (withinEndTolerance) {
      return max2;
    }
    return clamped;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/index.parts.mjs
  var index_parts_exports2 = {};
  __export(index_parts_exports2, {
    Arrow: () => SelectArrow,
    Backdrop: () => SelectBackdrop,
    Group: () => SelectGroup,
    GroupLabel: () => SelectGroupLabel,
    Icon: () => SelectIcon,
    Item: () => SelectItem,
    ItemIndicator: () => SelectItemIndicator,
    ItemText: () => SelectItemText,
    Label: () => SelectLabel,
    List: () => SelectList,
    Popup: () => SelectPopup,
    Portal: () => SelectPortal,
    Positioner: () => SelectPositioner,
    Root: () => SelectRoot,
    ScrollDownArrow: () => SelectScrollDownArrow,
    ScrollUpArrow: () => SelectScrollUpArrow,
    Separator: () => SelectSeparator,
    Trigger: () => SelectTrigger,
    Value: () => SelectValue
  });

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/root/SelectRoot.mjs
  var React70 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/root/SelectRootContext.mjs
  var React69 = __toESM(require_react(), 1);
  var SelectRootContext = /* @__PURE__ */ React69.createContext(void 0);
  if (true) SelectRootContext.displayName = "SelectRootContext";
  var SelectFloatingContext = /* @__PURE__ */ React69.createContext(void 0);
  if (true) SelectFloatingContext.displayName = "SelectFloatingContext";
  function useSelectRootContext() {
    const store = React69.useContext(SelectRootContext);
    if (store === void 0) {
      throw new Error(true ? "Base UI: SelectRootContext is missing. Select parts must be placed within <Select.Root>." : formatErrorMessage_default(60));
    }
    return store;
  }
  function useSelectFloatingContext() {
    const context = React69.useContext(SelectFloatingContext);
    if (context === void 0) {
      throw new Error(true ? "Base UI: SelectFloatingContext is missing. Select parts must be placed within <Select.Root>." : formatErrorMessage_default(61));
    }
    return context;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/store.mjs
  var selectors2 = {
    id: (state) => state.id,
    labelId: (state) => state.labelId,
    modal: (state) => state.modal,
    multiple: (state) => state.multiple,
    disabled: (state) => state.disabled,
    readOnly: (state) => state.readOnly,
    required: (state) => state.required,
    highlightItemOnHover: (state) => state.highlightItemOnHover,
    items: (state) => state.items,
    itemToStringLabel: (state) => state.itemToStringLabel,
    isItemEqualToValue: (state) => state.isItemEqualToValue,
    value: (state) => state.value,
    hasSelectedValue: (state) => {
      const {
        value,
        multiple,
        itemToStringValue
      } = state;
      if (value == null) {
        return false;
      }
      if (multiple && Array.isArray(value)) {
        return value.length > 0;
      }
      return stringifyAsValue(value, itemToStringValue) !== "";
    },
    hasNullItemLabel: (state, enabled) => {
      return enabled ? hasNullItemLabel(state.items) : false;
    },
    open: (state) => state.open,
    mounted: (state) => state.mounted,
    forceMount: (state) => state.forceMount,
    transitionStatus: (state) => state.transitionStatus,
    openMethod: (state) => state.openMethod,
    activeIndex: (state) => state.activeIndex,
    selectedIndex: (state) => state.selectedIndex,
    isActive: (state, index3) => state.activeIndex === index3,
    isSelected: (state, itemValue) => {
      const comparer = state.isItemEqualToValue;
      const storeValue = state.value;
      if (state.multiple) {
        return Array.isArray(storeValue) && storeValue.some((selectedItem) => compareItemEquality(itemValue, selectedItem, comparer));
      }
      return compareItemEquality(itemValue, storeValue, comparer);
    },
    isSelectedByFocus: (state, index3) => {
      return state.selectedIndex === index3;
    },
    popupProps: (state) => state.popupProps,
    triggerProps: (state) => state.triggerProps,
    itemRoot: (state) => state.itemRoot,
    triggerElement: (state) => state.triggerElement,
    positionerElement: (state) => state.positionerElement,
    listElement: (state) => state.listElement,
    popupSide: (state) => state.popupSide,
    scrollUpArrowVisible: (state) => state.scrollUpArrowVisible,
    scrollDownArrowVisible: (state) => state.scrollDownArrowVisible,
    hasScrollArrows: (state) => state.hasScrollArrows
  };

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/root/SelectRoot.mjs
  var import_jsx_runtime38 = __toESM(require_jsx_runtime(), 1);
  function SelectRoot(props) {
    const {
      id,
      value: valueProp,
      defaultValue = null,
      onValueChange,
      open: openProp,
      defaultOpen = false,
      onOpenChange,
      name: nameProp,
      form,
      autoComplete,
      disabled: disabledProp = false,
      readOnly = false,
      required = false,
      modal = true,
      actionsRef,
      inputRef,
      onOpenChangeComplete,
      items,
      multiple = false,
      itemToStringLabel,
      itemToStringValue,
      isItemEqualToValue = defaultItemEquality,
      highlightItemOnHover = true,
      children
    } = props;
    const {
      clearErrors
    } = useFormContext();
    const {
      setDirty,
      setTouched,
      setFocused,
      validityData,
      setFilled,
      name: fieldName,
      disabled: fieldDisabled,
      validation,
      validationMode
    } = useFieldRootContext();
    const generatedId = useLabelableId({
      id
    });
    const disabled2 = fieldDisabled || disabledProp;
    const name16 = fieldName ?? nameProp;
    const [value, setValueUnwrapped] = useControlled({
      controlled: valueProp,
      default: multiple ? defaultValue ?? EMPTY_ARRAY : defaultValue,
      name: "Select",
      state: "value"
    });
    const [open2, setOpenUnwrapped] = useControlled({
      controlled: openProp,
      default: defaultOpen,
      name: "Select",
      state: "open"
    });
    const listRef = React70.useRef([]);
    const labelsRef = React70.useRef([]);
    const popupRef = React70.useRef(null);
    const scrollHandlerRef = React70.useRef(null);
    const scrollArrowsMountedCountRef = React70.useRef(0);
    const valueRef = React70.useRef(null);
    const valuesRef = React70.useRef([]);
    const typingRef = React70.useRef(false);
    const firstItemTextRef = React70.useRef(null);
    const selectedItemTextRef = React70.useRef(null);
    const selectionRef = React70.useRef({
      allowSelectedMouseUp: false,
      allowUnselectedMouseUp: false,
      dragY: 0
    });
    const alignItemWithTriggerActiveRef = React70.useRef(false);
    const initialValueRef = React70.useRef(value);
    const {
      openMethod,
      triggerProps: interactionTypeProps
    } = useOpenInteractionType(open2);
    const store = useRefWithInit(() => new ReactStore({
      id: generatedId,
      labelId: void 0,
      modal,
      multiple,
      disabled: disabled2,
      readOnly,
      required,
      highlightItemOnHover,
      itemToStringLabel,
      itemToStringValue,
      isItemEqualToValue,
      value,
      open: open2,
      // Seeded with the initial values of `useUnmountAfterClose`, which is called after the
      // store because its unmount cleanup writes to it. `useSyncedValues` keeps them in sync.
      mounted: open2,
      transitionStatus: void 0,
      items,
      forceMount: false,
      openMethod: null,
      activeIndex: null,
      selectedIndex: null,
      popupProps: EMPTY_OBJECT,
      triggerProps: EMPTY_OBJECT,
      itemRoot: {
        props: EMPTY_OBJECT,
        multiple,
        disabled: disabled2,
        readOnly,
        isItemEqualToValue
      },
      triggerElement: null,
      positionerElement: null,
      listElement: null,
      popupSide: null,
      scrollUpArrowVisible: false,
      scrollDownArrowVisible: false,
      hasScrollArrows: false
    }, {
      setValue: NOOP,
      setOpen: NOOP,
      handleScrollArrowVisibility: NOOP,
      onOpenChangeComplete: NOOP,
      listRef,
      popupRef,
      scrollHandlerRef,
      scrollArrowsMountedCountRef,
      valueRef,
      valuesRef,
      labelsRef,
      typingRef,
      selectionRef,
      firstItemTextRef,
      selectedItemTextRef,
      alignItemWithTriggerActiveRef,
      initialValueRef
    }, selectors2)).current;
    const [preventUnmountOnClose, setPreventUnmountOnClose] = React70.useState(false);
    const {
      mounted,
      transitionStatus,
      forceUnmount: handleUnmount
    } = useUnmountAfterClose({
      open: open2,
      ref: popupRef,
      preventUnmountOnClose,
      setPreventUnmountOnClose,
      onUnmount() {
        store.update({
          activeIndex: null,
          openMethod: null,
          scrollUpArrowVisible: false,
          scrollDownArrowVisible: false
        });
        onOpenChangeComplete?.(false);
      }
    });
    const activeIndex = store.useState("activeIndex");
    const selectedIndex = store.useState("selectedIndex");
    const triggerElement = store.useState("triggerElement");
    const positionerElement = store.useState("positionerElement");
    const previousOpenMethod = usePreviousValue(openMethod);
    const renderedOpenMethod = openMethod ?? previousOpenMethod;
    const serializedValue = React70.useMemo(() => {
      if (multiple) {
        return "";
      }
      return stringifyAsValue(value, itemToStringValue);
    }, [multiple, value, itemToStringValue]);
    const fieldStringValue = React70.useMemo(() => {
      if (multiple && Array.isArray(value)) {
        return value.map((currentValue) => stringifyAsValue(currentValue, itemToStringValue));
      }
      return stringifyAsValue(value, itemToStringValue);
    }, [multiple, value, itemToStringValue]);
    const controlRef = useValueAsRef(triggerElement);
    const getStringifiedValueForForm = useStableCallback(() => fieldStringValue);
    useRegisterFieldControl(controlRef, generatedId, value, getStringifiedValueForForm, !disabled2, nameProp);
    const hasSelectedValue = multiple ? Array.isArray(value) && value.length > 0 : value != null && serializedValue !== "";
    useIsoLayoutEffect(() => {
      setFilled(hasSelectedValue);
    }, [hasSelectedValue, setFilled]);
    useIsoLayoutEffect(function syncSelectedIndex() {
      const nextIndex = findSelectionIndex(valuesRef.current, value, isItemEqualToValue, multiple);
      if (nextIndex === null) {
        selectedItemTextRef.current = null;
      }
      if (open2) {
        return;
      }
      store.set("selectedIndex", nextIndex);
    }, [multiple, open2, value, isItemEqualToValue, store]);
    useValueChanged(value, () => {
      clearErrors(name16);
      setDirty(isSelectedValueDirty(value, validityData.initialValue, isItemEqualToValue));
      validation.change(value);
    });
    const setOpen = useStableCallback((nextOpen, eventDetails) => {
      const openEventDetails = eventDetails;
      const shouldPreventUnmountOnClose = attachPreventUnmountOnClose(openEventDetails);
      onOpenChange?.(nextOpen, openEventDetails);
      if (eventDetails.isCanceled) {
        return;
      }
      if (!nextOpen) {
        setPreventUnmountOnClose(shouldPreventUnmountOnClose());
      }
      setOpenUnwrapped(nextOpen);
      if (!nextOpen && (eventDetails.reason === reason_parts_exports.focusOut || eventDetails.reason === reason_parts_exports.outsidePress)) {
        setTouched(true);
        setFocused(false);
        if (validationMode === "onBlur") {
          validation.commit(value);
        }
      }
    });
    React70.useImperativeHandle(actionsRef, () => ({
      unmount: handleUnmount,
      close: () => {
        if (store.state.open) {
          setOpen(false, createChangeEventDetails(reason_parts_exports.imperativeAction));
        }
      }
    }), [handleUnmount, setOpen, store]);
    const setValue = useStableCallback((nextValue, eventDetails) => {
      onValueChange?.(nextValue, eventDetails);
      if (eventDetails.isCanceled) {
        return;
      }
      setValueUnwrapped(nextValue);
    });
    const handleScrollArrowVisibility = useStableCallback((scroller) => {
      const maxScrollTop = getMaxScrollOffset(scroller.scrollHeight, scroller.clientHeight);
      const scrollTop = normalizeScrollOffset(scroller.scrollTop, maxScrollTop);
      const shouldShowUp = scrollTop > 0;
      const shouldShowDown = scrollTop < maxScrollTop;
      store.set("scrollUpArrowVisible", shouldShowUp);
      store.set("scrollDownArrowVisible", shouldShowDown);
    });
    const floatingContext = useFloatingRootContext({
      open: open2,
      onOpenChange: setOpen,
      elements: {
        reference: triggerElement,
        floating: positionerElement
      }
    });
    const click = useClick(floatingContext, {
      enabled: !disabled2,
      event: "mousedown"
    });
    const dismiss = useDismiss(floatingContext);
    const listNavigation2 = useListNavigation(floatingContext, {
      enabled: !disabled2,
      listRef,
      activeIndex,
      selectedIndex,
      disabledIndices: EMPTY_ARRAY,
      onNavigate(nextActiveIndex) {
        if (nextActiveIndex === null && !open2) {
          return;
        }
        store.set("activeIndex", nextActiveIndex);
      },
      focusItemOnHover: highlightItemOnHover
    });
    const typeahead = useTypeahead(floatingContext, {
      // Typeahead on an open popup only moves the highlight, so it remains available while
      // `readOnly`. The closed-trigger variant commits a value instead, so it doesn't.
      enabled: !disabled2 && (open2 || !readOnly && !multiple),
      listRef: labelsRef,
      activeIndex,
      selectedIndex,
      // Skip disabled items while matching so typeahead advances to the next selectable item
      // (a click can never select a disabled item and native `<select>` skips them too). Resolve
      // the disabled state from the element via the attribute-only `isElementDisabled` so the
      // hidden, force-mounted items used for closed-trigger typeahead aren't dropped by the
      // `elementsRef`/visibility filter that `disabledIndices` deliberately sidesteps.
      disabledIndices: (index3) => isElementDisabled(listRef.current[index3]),
      onMatch(index3) {
        if (open2) {
          store.set("activeIndex", index3);
        } else {
          setValue(valuesRef.current[index3], createChangeEventDetails(reason_parts_exports.none));
        }
      },
      onTyping(typing) {
        typingRef.current = typing;
      }
    });
    const mergedTriggerProps = React70.useMemo(() => mergeProps(typeahead.reference, listNavigation2.reference, dismiss.reference, click.reference, interactionTypeProps), [click.reference, typeahead.reference, listNavigation2.reference, dismiss.reference, interactionTypeProps]);
    const popupProps = React70.useMemo(() => mergeProps(FOCUSABLE_POPUP_PROPS, typeahead.floating, listNavigation2.floating, dismiss.floating), [typeahead.floating, listNavigation2.floating, dismiss.floating]);
    const itemProps = listNavigation2.item ?? EMPTY_OBJECT;
    const itemRoot = React70.useMemo(() => ({
      props: itemProps,
      multiple,
      disabled: disabled2,
      readOnly,
      isItemEqualToValue
    }), [itemProps, multiple, disabled2, readOnly, isItemEqualToValue]);
    store.useContextCallback("setValue", setValue);
    store.useContextCallback("setOpen", setOpen);
    store.useContextCallback("handleScrollArrowVisibility", handleScrollArrowVisibility);
    store.useContextCallback("onOpenChangeComplete", onOpenChangeComplete);
    useOnFirstRender(() => {
      store.update({
        popupProps,
        triggerProps: mergedTriggerProps,
        itemRoot
      });
    });
    store.useSyncedValues({
      id: generatedId,
      modal,
      multiple,
      disabled: disabled2,
      readOnly,
      required,
      highlightItemOnHover,
      value,
      open: open2,
      mounted,
      transitionStatus,
      popupProps,
      triggerProps: mergedTriggerProps,
      itemRoot,
      items,
      itemToStringLabel,
      itemToStringValue,
      isItemEqualToValue,
      openMethod: renderedOpenMethod
    });
    const ref = useMergedRefs(inputRef, validation.inputRef);
    const hiddenInputName = multiple ? void 0 : name16;
    const hiddenInputs = React70.useMemo(() => {
      if (!multiple || !Array.isArray(value) || !name16) {
        return null;
      }
      return value.map((v) => {
        const currentSerializedValue = stringifyAsValue(v, itemToStringValue);
        return /* @__PURE__ */ (0, import_jsx_runtime38.jsx)("input", {
          type: "hidden",
          form,
          name: name16,
          value: currentSerializedValue,
          disabled: disabled2
        }, currentSerializedValue);
      });
    }, [multiple, value, form, name16, itemToStringValue, disabled2]);
    return /* @__PURE__ */ (0, import_jsx_runtime38.jsxs)(SelectRootContext.Provider, {
      value: store,
      children: [/* @__PURE__ */ (0, import_jsx_runtime38.jsx)(SelectFloatingContext.Provider, {
        value: floatingContext,
        children
      }), /* @__PURE__ */ (0, import_jsx_runtime38.jsx)("input", {
        ...validation.getValidationProps(disabled2, {
          onFocus() {
            store.state.triggerElement?.focus({
              // Supported in Chrome from 144 (January 2026)
              focusVisible: true
            });
          },
          // Handle browser autofill.
          onChange(event) {
            if (event.nativeEvent.defaultPrevented || disabled2 || readOnly) {
              return;
            }
            const nextValue = event.currentTarget.value;
            const details = createChangeEventDetails(reason_parts_exports.none, event.nativeEvent);
            function handleChange() {
              if (multiple) {
                return;
              }
              const nextValueLower = nextValue.toLowerCase();
              let matchingIndex = valuesRef.current.findIndex((candidate) => stringifyAsValue(candidate, itemToStringValue).toLowerCase() === nextValueLower || stringifyAsLabel(candidate, itemToStringLabel).toLowerCase() === nextValueLower);
              if (matchingIndex === -1) {
                matchingIndex = valuesRef.current.findIndex((_, index3) => {
                  const renderedLabel = labelsRef.current[index3];
                  return renderedLabel != null && renderedLabel.toLowerCase() === nextValueLower;
                });
              }
              const matchingValue = valuesRef.current[matchingIndex];
              if (matchingValue != null) {
                setValue(matchingValue, details);
              }
            }
            store.set("forceMount", true);
            queueMicrotask(handleChange);
          }
        }),
        id: generatedId && hiddenInputName == null ? `${generatedId}-hidden-input` : void 0,
        form,
        name: hiddenInputName,
        autoComplete,
        value: serializedValue,
        disabled: disabled2,
        required: required && !(multiple && hasSelectedValue),
        readOnly,
        ref,
        style: name16 ? visuallyHiddenInput : visuallyHidden,
        tabIndex: -1,
        "aria-hidden": true,
        suppressHydrationWarning: true
      }), hiddenInputs]
    });
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/label/SelectLabel.mjs
  var React71 = __toESM(require_react(), 1);
  var SelectLabel = /* @__PURE__ */ React71.forwardRef(function SelectLabel2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      ...elementProps
    } = componentProps;
    const elementPropsWithoutId = elementProps;
    delete elementPropsWithoutId.id;
    const fieldRootContext = useFieldRootContext();
    const store = useSelectRootContext();
    const triggerElement = store.useState("triggerElement");
    const rootId = store.useState("id");
    const defaultLabelId = getDefaultLabelId(rootId);
    const labelProps = useLabel({
      id: defaultLabelId,
      fallbackControlId: triggerElement?.id ?? rootId,
      setLabelId(nextLabelId) {
        const resolvedLabelId = typeof nextLabelId === "function" ? nextLabelId(store.state.labelId) : nextLabelId;
        store.set("labelId", resolvedLabelId);
      }
    });
    return useRenderElement("div", componentProps, {
      ref: forwardedRef,
      state: fieldRootContext.state,
      props: [labelProps, elementProps],
      stateAttributesMapping: fieldValidityMapping
    });
  });
  if (true) SelectLabel.displayName = "SelectLabel";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/trigger/SelectTrigger.mjs
  var React72 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/trigger/SelectTriggerDataAttributes.mjs
  var popupOpen2 = CommonTriggerDataAttributes_exports.popupOpen;
  var pressed2 = CommonTriggerDataAttributes_exports.pressed;
  var popupSide = "data-popup-side";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/trigger/SelectTrigger.mjs
  var SELECTED_DELAY = 400;
  var stateAttributesMapping2 = {
    ...pressableTriggerOpenStateMapping,
    ...fieldValidityMapping,
    popupSide: (side) => side ? {
      [popupSide]: side
    } : null,
    value: () => null
  };
  var SelectTrigger = /* @__PURE__ */ React72.forwardRef(function SelectTrigger2(componentProps, forwardedRef) {
    const {
      render,
      className,
      id: idProp,
      disabled: disabledProp = false,
      nativeButton = true,
      style,
      ...elementProps
    } = componentProps;
    const {
      setTouched,
      validationMode,
      validation,
      state: fieldState,
      disabled: fieldDisabled
    } = useFieldRootContext();
    const {
      labelId: fieldLabelId
    } = useLabelableContext();
    const store = useSelectRootContext();
    const readOnly = store.useState("readOnly");
    const required = store.useState("required");
    const selectDisabled = store.useState("disabled");
    const disabled2 = fieldDisabled || selectDisabled || disabledProp;
    const open2 = store.useState("open");
    const mounted = store.useState("mounted");
    const value = store.useState("value");
    const triggerProps = store.useState("triggerProps");
    const positionerElement = store.useState("positionerElement");
    const listElement = store.useState("listElement");
    const popupSideValue = store.useState("popupSide");
    const rootId = store.useState("id");
    const selectLabelId = store.useState("labelId");
    const hasSelectedValue = store.useState("hasSelectedValue");
    const popupSide2 = mounted && positionerElement ? popupSideValue : null;
    const id = idProp ?? rootId;
    const ariaLabelledBy = resolveAriaLabelledBy(fieldLabelId, selectLabelId);
    useLabelableId({
      id: idProp
    });
    const positionerRef = useValueAsRef(positionerElement);
    const triggerRef = React72.useRef(null);
    const setFocused = useSetFieldFocused(disabled2, triggerRef);
    const {
      getButtonProps,
      buttonRef
    } = useButton({
      disabled: disabled2,
      native: nativeButton
    });
    const setTriggerElement = store.useStateSetter("triggerElement");
    const timeoutFocus = useTimeout();
    const timeoutMouseDown = useTimeout();
    const selectedDelayTimeout = useTimeout();
    React72.useEffect(() => {
      if (open2) {
        selectedDelayTimeout.start(SELECTED_DELAY, () => {
          store.context.selectionRef.current.allowUnselectedMouseUp = true;
          store.context.selectionRef.current.allowSelectedMouseUp = true;
        });
        return () => {
          selectedDelayTimeout.clear();
        };
      }
      store.context.selectionRef.current = {
        allowSelectedMouseUp: false,
        allowUnselectedMouseUp: false,
        dragY: 0
      };
      timeoutMouseDown.clear();
      return void 0;
    }, [open2, store, timeoutMouseDown, selectedDelayTimeout]);
    const mergedProps = mergeProps(triggerProps, {
      id,
      role: "combobox",
      "aria-expanded": open2,
      "aria-haspopup": "listbox",
      "aria-controls": open2 ? listElement?.id ?? getFloatingFocusElement(positionerElement)?.id : void 0,
      "aria-labelledby": ariaLabelledBy,
      "aria-readonly": readOnly || void 0,
      "aria-required": required || void 0,
      tabIndex: disabled2 ? -1 : 0,
      onFocus(event) {
        setFocused(true);
        if (open2 && store.context.alignItemWithTriggerActiveRef.current) {
          store.context.setOpen(false, createChangeEventDetails(reason_parts_exports.none, event.nativeEvent));
        }
        timeoutFocus.start(0, () => {
          store.set("forceMount", true);
        });
      },
      onBlur(event) {
        if (contains(positionerElement, event.relatedTarget)) {
          return;
        }
        setTouched(true);
        setFocused(false);
        if (validationMode === "onBlur") {
          validation.commit(value);
        }
      },
      onMouseDown(event) {
        if (open2) {
          return;
        }
        const doc = ownerDocument(event.currentTarget);
        function handleMouseUp(mouseEvent) {
          if (!triggerRef.current) {
            return;
          }
          const mouseUpTarget = mouseEvent.target;
          if (contains(triggerRef.current, mouseUpTarget) || contains(positionerRef.current, mouseUpTarget)) {
            return;
          }
          if (isMouseWithinBounds(mouseEvent, triggerRef.current)) {
            return;
          }
          store.context.setOpen(false, createChangeEventDetails(reason_parts_exports.cancelOpen, mouseEvent));
        }
        timeoutMouseDown.start(0, () => {
          doc.addEventListener("mouseup", handleMouseUp, {
            once: true
          });
        });
      }
    }, elementProps, getButtonProps);
    const props = validation.getValidationProps(disabled2, mergedProps);
    props.role = "combobox";
    const state = {
      ...fieldState,
      open: open2,
      disabled: disabled2,
      value,
      readOnly,
      popupSide: popupSide2,
      placeholder: !hasSelectedValue
    };
    return useRenderElement("button", componentProps, {
      ref: [forwardedRef, triggerRef, buttonRef, setTriggerElement],
      state,
      stateAttributesMapping: stateAttributesMapping2,
      props
    });
  });
  if (true) SelectTrigger.displayName = "SelectTrigger";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/value/SelectValue.mjs
  var React73 = __toESM(require_react(), 1);
  var stateAttributesMapping3 = {
    value: () => null
  };
  var SelectValue = /* @__PURE__ */ React73.forwardRef(function SelectValue2(componentProps, forwardedRef) {
    const {
      className,
      render,
      children: childrenProp,
      placeholder,
      style,
      ...elementProps
    } = componentProps;
    const store = useSelectRootContext();
    const value = store.useState("value");
    const items = store.useState("items");
    const itemToStringLabel = store.useState("itemToStringLabel");
    const hasSelectedValue = store.useState("hasSelectedValue");
    const shouldCheckNullItemLabel = !hasSelectedValue && placeholder != null && childrenProp == null;
    const hasNullLabel = store.useState("hasNullItemLabel", shouldCheckNullItemLabel);
    const state = {
      value,
      placeholder: !hasSelectedValue
    };
    let children = null;
    if (typeof childrenProp === "function") {
      children = childrenProp(value);
    } else if (childrenProp != null) {
      children = childrenProp;
    } else if (shouldCheckNullItemLabel && !hasNullLabel) {
      children = placeholder;
    } else if (Array.isArray(value)) {
      children = resolveMultipleLabels(value, items, itemToStringLabel);
    } else {
      children = resolveSelectedLabel(value, items, itemToStringLabel);
    }
    const element = useRenderElement("span", componentProps, {
      state,
      ref: [forwardedRef, store.context.valueRef],
      props: [{
        children
      }, elementProps],
      stateAttributesMapping: stateAttributesMapping3
    });
    return element;
  });
  if (true) SelectValue.displayName = "SelectValue";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/icon/SelectIcon.mjs
  var React74 = __toESM(require_react(), 1);
  var SelectIcon = /* @__PURE__ */ React74.forwardRef(function SelectIcon2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      ...elementProps
    } = componentProps;
    const store = useSelectRootContext();
    const open2 = store.useState("open");
    const state = {
      open: open2
    };
    const element = useRenderElement("span", componentProps, {
      state,
      ref: forwardedRef,
      props: [{
        "aria-hidden": true,
        children: "\u25BC"
      }, elementProps],
      stateAttributesMapping: triggerOpenStateMapping
    });
    return element;
  });
  if (true) SelectIcon.displayName = "SelectIcon";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/portal/SelectPortal.mjs
  var React75 = __toESM(require_react(), 1);
  var import_jsx_runtime39 = __toESM(require_jsx_runtime(), 1);
  var SelectPortal = /* @__PURE__ */ React75.forwardRef(function SelectPortal2(portalProps, forwardedRef) {
    const store = useSelectRootContext();
    const mounted = store.useState("mounted");
    const forceMount = store.useState("forceMount");
    const shouldRender = mounted || forceMount;
    if (!shouldRender) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime39.jsx)(FloatingPortal, {
      ref: forwardedRef,
      ...portalProps
    });
  });
  if (true) SelectPortal.displayName = "SelectPortal";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/backdrop/SelectBackdrop.mjs
  var React76 = __toESM(require_react(), 1);
  var stateAttributesMapping4 = {
    ...popupStateMapping,
    ...transitionStatusMapping
  };
  var SelectBackdrop = /* @__PURE__ */ React76.forwardRef(function SelectBackdrop2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      ...elementProps
    } = componentProps;
    const store = useSelectRootContext();
    const open2 = store.useState("open");
    const mounted = store.useState("mounted");
    const transitionStatus = store.useState("transitionStatus");
    const state = {
      open: open2,
      transitionStatus
    };
    const element = useRenderElement("div", componentProps, {
      state,
      ref: forwardedRef,
      props: [{
        role: "presentation",
        hidden: !mounted,
        style: {
          userSelect: "none",
          WebkitUserSelect: "none"
        }
      }, elementProps],
      stateAttributesMapping: stateAttributesMapping4
    });
    return element;
  });
  if (true) SelectBackdrop.displayName = "SelectBackdrop";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/positioner/SelectPositioner.mjs
  var React78 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/positioner/SelectPositionerContext.mjs
  var React77 = __toESM(require_react(), 1);
  var SelectPositionerContext = /* @__PURE__ */ React77.createContext(void 0);
  if (true) SelectPositionerContext.displayName = "SelectPositionerContext";
  function useSelectPositionerContext() {
    const context = React77.useContext(SelectPositionerContext);
    if (!context) {
      throw new Error(true ? "Base UI: SelectPositionerContext is missing. SelectPositioner parts must be placed within <Select.Positioner>." : formatErrorMessage_default(59));
    }
    return context;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/popup/utils.mjs
  function clearStyles(element, originalStyles) {
    if (element) {
      Object.assign(element.style, originalStyles);
    }
  }
  var LIST_FUNCTIONAL_STYLES = {
    position: "relative",
    maxHeight: "100%",
    overflowX: "hidden",
    overflowY: "auto"
  };

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/positioner/SelectPositioner.mjs
  var import_jsx_runtime40 = __toESM(require_jsx_runtime(), 1);
  var FIXED = {
    position: "fixed"
  };
  var SelectPositioner = /* @__PURE__ */ React78.forwardRef(function SelectPositioner2(componentProps, forwardedRef) {
    const {
      anchor,
      className,
      render,
      // `useAnchorPositioning` applies the same defaults to the undefined values; the names
      // remain destructured to exclude the props from `elementProps`.
      positionMethod,
      side,
      align,
      sideOffset,
      alignOffset,
      collisionBoundary = "clipping-ancestors",
      collisionPadding,
      arrowPadding,
      sticky,
      disableAnchorTracking,
      alignItemWithTrigger = true,
      collisionAvoidance = DROPDOWN_COLLISION_AVOIDANCE,
      style,
      ...elementProps
    } = componentProps;
    const store = useSelectRootContext();
    const floatingRootContext = useSelectFloatingContext();
    const open2 = store.useState("open");
    const mounted = store.useState("mounted");
    const modal = store.useState("modal");
    const value = store.useState("value");
    const openMethod = store.useState("openMethod");
    const positionerElement = store.useState("positionerElement");
    const triggerElement = store.useState("triggerElement");
    const isItemEqualToValue = store.useState("isItemEqualToValue");
    const transitionStatus = store.useState("transitionStatus");
    const scrollUpArrowRef = React78.useRef(null);
    const scrollDownArrowRef = React78.useRef(null);
    const [controlledAlignItemWithTrigger, setControlledAlignItemWithTrigger] = React78.useState(alignItemWithTrigger);
    const alignItemWithTriggerActive = mounted && controlledAlignItemWithTrigger && openMethod !== "touch";
    if (!mounted && controlledAlignItemWithTrigger !== alignItemWithTrigger) {
      setControlledAlignItemWithTrigger(alignItemWithTrigger);
    }
    React78.useImperativeHandle(store.context.alignItemWithTriggerActiveRef, () => alignItemWithTriggerActive);
    useAnchoredPopupScrollLock((alignItemWithTriggerActive || modal) && open2, openMethod === "touch", positionerElement, triggerElement);
    const positioning = useAnchorPositioning({
      anchor,
      floatingRootContext,
      positionMethod,
      mounted,
      side,
      sideOffset,
      align,
      alignOffset,
      arrowPadding,
      collisionBoundary,
      collisionPadding,
      sticky,
      disableAnchorTracking: disableAnchorTracking ?? alignItemWithTriggerActive,
      collisionAvoidance,
      keepMounted: true
    });
    const renderedSide = alignItemWithTriggerActive ? "none" : positioning.side;
    const positionerStyles = alignItemWithTriggerActive ? FIXED : positioning.positionerStyles;
    const state = {
      open: open2,
      side: renderedSide,
      align: positioning.align,
      anchorHidden: positioning.anchorHidden
    };
    useIsoLayoutEffect(() => {
      store.set("popupSide", positioning.side);
    }, [store, positioning.side]);
    const setPositionerElement = store.useStateSetter("positionerElement");
    const element = usePositioner(componentProps, state, {
      styles: positionerStyles,
      transitionStatus,
      props: elementProps,
      refs: [forwardedRef, setPositionerElement],
      hidden: !mounted,
      inert: !open2
    });
    const prevMapSizeRef = React78.useRef(0);
    const onMapChange = useStableCallback((map) => {
      if (store.context.valuesRef.current.length === 0) {
        return;
      }
      const prevSize = prevMapSizeRef.current;
      prevMapSizeRef.current = map.size;
      const eventDetails = createChangeEventDetails(reason_parts_exports.none);
      if (prevSize !== 0 && !store.state.multiple && value !== null) {
        const selectedValueIndex = findItemIndex(store.context.valuesRef.current, value, isItemEqualToValue);
        if (selectedValueIndex === -1) {
          const initialSelectedValue = store.context.initialValueRef.current;
          const hasInitial = initialSelectedValue != null && findItemIndex(store.context.valuesRef.current, initialSelectedValue, isItemEqualToValue) !== -1;
          const nextValue = hasInitial ? initialSelectedValue : null;
          store.context.setValue(nextValue, eventDetails);
          if (nextValue === null) {
            store.set("selectedIndex", null);
            store.context.selectedItemTextRef.current = null;
          }
        }
      }
      if (prevSize !== 0 && store.state.multiple && Array.isArray(value)) {
        const nextValue = value.filter((selectedItemValue) => findItemIndex(store.context.valuesRef.current, selectedItemValue, isItemEqualToValue) !== -1);
        if (nextValue.length !== value.length) {
          store.context.setValue(nextValue, eventDetails);
          if (nextValue.length === 0) {
            store.set("selectedIndex", null);
            store.context.selectedItemTextRef.current = null;
          }
        }
      }
      if (open2 && alignItemWithTriggerActive) {
        store.update({
          scrollUpArrowVisible: false,
          scrollDownArrowVisible: false
        });
        const stylesToClear = {
          height: ""
        };
        clearStyles(positionerElement, stylesToClear);
        clearStyles(store.context.popupRef.current, stylesToClear);
      }
    });
    const contextValue = React78.useMemo(() => ({
      ...positioning,
      side: renderedSide,
      alignItemWithTriggerActive,
      setControlledAlignItemWithTrigger,
      scrollUpArrowRef,
      scrollDownArrowRef
    }), [positioning, renderedSide, alignItemWithTriggerActive, setControlledAlignItemWithTrigger]);
    return /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(CompositeList, {
      elementsRef: store.context.listRef,
      labelsRef: store.context.labelsRef,
      onMapChange,
      children: /* @__PURE__ */ (0, import_jsx_runtime40.jsxs)(SelectPositionerContext.Provider, {
        value: contextValue,
        children: [mounted && modal && /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(InternalBackdrop, {
          inert: inertValue(!open2),
          cutout: triggerElement
        }), element]
      })
    });
  });
  if (true) SelectPositioner.displayName = "SelectPositioner";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/popup/SelectPopup.mjs
  var React79 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/positioner/SelectPositionerCssVars.mjs
  var transformOrigin2 = "--transform-origin";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/popup/SelectPopup.mjs
  var import_jsx_runtime41 = __toESM(require_jsx_runtime(), 1);
  var stateAttributesMapping5 = {
    ...popupStateMapping,
    ...transitionStatusMapping
  };
  var SelectPopup = /* @__PURE__ */ React79.forwardRef(function SelectPopup2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      finalFocus,
      ...elementProps
    } = componentProps;
    const store = useSelectRootContext();
    const multiple = store.useState("multiple");
    const readOnly = store.useState("readOnly");
    const highlightItemOnHover = store.useState("highlightItemOnHover");
    const floatingRootContext = useSelectFloatingContext();
    const {
      side,
      align,
      alignItemWithTriggerActive,
      isPositioned,
      setControlledAlignItemWithTrigger
    } = useSelectPositionerContext();
    const insideToolbar = useToolbarRootContext(true) != null;
    const direction = useDirection();
    const {
      nonce,
      disableStyleElements
    } = useCSPContext();
    const id = store.useState("id");
    const open2 = store.useState("open");
    const openMethod = store.useState("openMethod");
    const mounted = store.useState("mounted");
    const popupProps = store.useState("popupProps");
    const transitionStatus = store.useState("transitionStatus");
    const triggerElement = store.useState("triggerElement");
    const positionerElement = store.useState("positionerElement");
    const listElement = store.useState("listElement");
    const reachedMaxHeightRef = React79.useRef(false);
    const initialPlacedRef = React79.useRef(false);
    const originalPositionerStylesRef = React79.useRef({});
    const scrollArrowFrame = useAnimationFrame();
    const handleScroll = useStableCallback((scroller) => {
      if (!positionerElement || !store.context.popupRef.current || !initialPlacedRef.current) {
        return;
      }
      const isTopPositioned = positionerElement.style.top === "0px";
      const isBottomPositioned = positionerElement.style.bottom === "0px";
      if (reachedMaxHeightRef.current || !alignItemWithTriggerActive || !isTopPositioned && !isBottomPositioned) {
        store.context.handleScrollArrowVisibility(scroller);
        return;
      }
      const scale = getScale2(positionerElement);
      const currentHeight = normalizeSize(positionerElement.getBoundingClientRect().height, "y", scale);
      const doc = ownerDocument(positionerElement);
      const win = getWindow(positionerElement);
      const positionerStyles = win.getComputedStyle(positionerElement);
      const marginTop = parseFloat(positionerStyles.marginTop);
      const marginBottom = parseFloat(positionerStyles.marginBottom);
      const maxPopupHeight = getMaxPopupHeight(win.getComputedStyle(store.context.popupRef.current));
      const maxAvailableHeight = Math.min(doc.documentElement.clientHeight - marginTop - marginBottom, maxPopupHeight);
      const scrollTop = scroller.scrollTop;
      const maxScrollTop = getMaxScrollTop(scroller);
      let nextScrollTop = null;
      const setHeight = (height) => {
        positionerElement.style.height = `${height}px`;
      };
      const diff = isTopPositioned ? maxScrollTop - scrollTop : scrollTop;
      const nextHeight = Math.min(currentHeight + diff, maxAvailableHeight);
      if (diff <= SCROLL_EDGE_TOLERANCE_PX) {
        const heightDelta = clamp2(diff, 0, maxAvailableHeight - currentHeight);
        if (heightDelta > 0) {
          setHeight(currentHeight + heightDelta);
        }
        scroller.scrollTop = isTopPositioned ? maxScrollTop : 0;
        if (maxAvailableHeight - (currentHeight + heightDelta) <= SCROLL_EDGE_TOLERANCE_PX) {
          reachedMaxHeightRef.current = true;
        }
        store.context.handleScrollArrowVisibility(scroller);
        return;
      }
      if (maxAvailableHeight - nextHeight > SCROLL_EDGE_TOLERANCE_PX) {
        nextScrollTop = isTopPositioned ? Infinity : 0;
      } else if (isBottomPositioned && scrollTop < maxScrollTop) {
        const overshoot = currentHeight + diff - maxAvailableHeight;
        nextScrollTop = scrollTop - (diff - overshoot);
      }
      const nextPositionerHeight = Math.ceil(nextHeight);
      if (nextPositionerHeight !== 0) {
        setHeight(nextPositionerHeight);
      }
      if (nextScrollTop != null) {
        const target = clamp2(nextScrollTop, 0, getMaxScrollTop(scroller));
        if (Math.abs(scroller.scrollTop - target) > SCROLL_EDGE_TOLERANCE_PX) {
          scroller.scrollTop = target;
        }
      }
      if (nextPositionerHeight >= maxAvailableHeight - SCROLL_EDGE_TOLERANCE_PX) {
        reachedMaxHeightRef.current = true;
      }
      store.context.handleScrollArrowVisibility(scroller);
    });
    React79.useImperativeHandle(store.context.scrollHandlerRef, () => handleScroll, [handleScroll]);
    useOpenChangeComplete({
      open: open2,
      ref: store.context.popupRef,
      onComplete() {
        if (open2) {
          store.context.onOpenChangeComplete(true);
        }
      }
    });
    const state = {
      open: open2,
      transitionStatus,
      side,
      align
    };
    useIsoLayoutEffect(() => {
      if (!positionerElement || !store.context.popupRef.current || Object.keys(originalPositionerStylesRef.current).length) {
        return;
      }
      originalPositionerStylesRef.current = {
        top: positionerElement.style.top || "0",
        left: positionerElement.style.left || "0",
        right: positionerElement.style.right,
        height: positionerElement.style.height,
        bottom: positionerElement.style.bottom,
        minHeight: positionerElement.style.minHeight,
        maxHeight: positionerElement.style.maxHeight,
        marginTop: positionerElement.style.marginTop,
        marginBottom: positionerElement.style.marginBottom
      };
    }, [store, positionerElement]);
    useIsoLayoutEffect(() => {
      if (open2 || alignItemWithTriggerActive) {
        return;
      }
      initialPlacedRef.current = false;
      reachedMaxHeightRef.current = false;
      clearStyles(positionerElement, originalPositionerStylesRef.current);
    }, [open2, alignItemWithTriggerActive, positionerElement]);
    useIsoLayoutEffect(() => {
      const popupElement = store.context.popupRef.current;
      if (!open2 || !triggerElement || !positionerElement || !popupElement || alignItemWithTriggerActive && !isPositioned || store.state.transitionStatus === "ending") {
        return;
      }
      initialPlacedRef.current = true;
      popupElement.style.removeProperty(transformOrigin2);
      if (!alignItemWithTriggerActive) {
        scrollArrowFrame.request(() => store.context.handleScrollArrowVisibility(listElement || popupElement));
        return;
      }
      const restoreTransformStyles = unsetTransformStyles(popupElement);
      try {
        let textElement = store.context.selectedItemTextRef.current;
        if (!textElement?.isConnected) {
          const hasSelectedValue = store.select("hasSelectedValue");
          textElement = !hasSelectedValue && store.context.firstItemTextRef.current?.isConnected ? store.context.firstItemTextRef.current : null;
        }
        const valueElement = store.context.valueRef.current;
        const win = getWindow(positionerElement);
        const positionerStyles = win.getComputedStyle(positionerElement);
        const popupStyles = win.getComputedStyle(popupElement);
        const doc = ownerDocument(triggerElement);
        const scale = getScale2(triggerElement);
        const triggerRect = normalizeRect(triggerElement.getBoundingClientRect(), scale);
        const positionerRect = normalizeRect(positionerElement.getBoundingClientRect(), scale);
        const triggerHeight = triggerRect.height;
        const scroller = listElement || popupElement;
        const scrollHeight = scroller.scrollHeight;
        const borderBottom = parseFloat(popupStyles.borderBottomWidth);
        const marginTop = parseFloat(positionerStyles.marginTop) || 10;
        const marginBottom = parseFloat(positionerStyles.marginBottom) || 10;
        const minHeight = parseFloat(positionerStyles.minHeight) || 100;
        const maxPopupHeight = getMaxPopupHeight(popupStyles);
        const paddingLeft = 5;
        const paddingRight = 5;
        const triggerCollisionThreshold = 20;
        const viewportHeight = doc.documentElement.clientHeight - marginTop - marginBottom;
        const viewportWidth = doc.documentElement.clientWidth;
        const availableSpaceBeneathTrigger = viewportHeight - triggerRect.bottom + triggerHeight;
        let textRect;
        let alignedLeft = direction === "rtl" ? triggerRect.right - positionerRect.width : triggerRect.left;
        let offsetY = 0;
        if (textElement && valueElement) {
          const valueRect = normalizeRect(valueElement.getBoundingClientRect(), scale);
          textRect = normalizeRect(textElement.getBoundingClientRect(), scale);
          alignedLeft = positionerRect.left + (direction === "rtl" ? valueRect.right - textRect.right : valueRect.left - textRect.left);
          const valueCenterFromTriggerTop = valueRect.top - triggerRect.top + valueRect.height / 2;
          const textCenterFromPositionerTop = textRect.top - positionerRect.top + textRect.height / 2;
          offsetY = textCenterFromPositionerTop - valueCenterFromTriggerTop;
        }
        const idealHeight = availableSpaceBeneathTrigger + offsetY + marginBottom + borderBottom;
        let height = Math.min(viewportHeight, idealHeight);
        const maxHeight = viewportHeight - marginTop - marginBottom;
        const scrollTop = idealHeight - height;
        const maxRight = viewportWidth - paddingRight;
        positionerElement.style.left = `${clamp2(alignedLeft, paddingLeft, maxRight - positionerRect.width)}px`;
        positionerElement.style.height = `${height}px`;
        positionerElement.style.maxHeight = "none";
        positionerElement.style.marginTop = `${marginTop}px`;
        positionerElement.style.marginBottom = `${marginBottom}px`;
        popupElement.style.height = "100%";
        const maxScrollTop = getMaxScrollTop(scroller);
        const isTopPositioned = scrollTop >= maxScrollTop - SCROLL_EDGE_TOLERANCE_PX;
        if (isTopPositioned) {
          height = Math.min(viewportHeight, positionerRect.height) - (scrollTop - maxScrollTop);
        }
        const fallbackToAlignPopupToTrigger = triggerRect.top < triggerCollisionThreshold || triggerRect.bottom > viewportHeight - triggerCollisionThreshold || Math.ceil(height) + SCROLL_EDGE_TOLERANCE_PX < Math.min(scrollHeight, minHeight);
        const isPinchZoomed = (win.visualViewport?.scale ?? 1) !== 1 && parts_exports.engine.webkit;
        if (fallbackToAlignPopupToTrigger || isPinchZoomed) {
          clearStyles(positionerElement, originalPositionerStylesRef.current);
          setControlledAlignItemWithTrigger(false);
          return;
        }
        const initialHeight = Math.max(minHeight, height);
        if (isTopPositioned) {
          const topOffset = Math.max(0, viewportHeight - idealHeight);
          positionerElement.style.top = positionerRect.height >= maxHeight ? "0" : `${topOffset}px`;
          positionerElement.style.height = `${height}px`;
          scroller.scrollTop = getMaxScrollTop(scroller);
        } else {
          positionerElement.style.bottom = "0";
          scroller.scrollTop = scrollTop;
        }
        if (textRect) {
          const popupTop = positionerRect.top;
          const popupHeight = positionerRect.height;
          const textCenterY = textRect.top + textRect.height / 2;
          const clampedY = clamp2(popupHeight > 0 ? (textCenterY - popupTop) / popupHeight * 100 : 50, 0, 100);
          popupElement.style.setProperty(transformOrigin2, `50% ${clampedY}%`);
        }
        if (initialHeight === viewportHeight || height >= maxPopupHeight) {
          reachedMaxHeightRef.current = true;
        }
        store.context.handleScrollArrowVisibility(scroller);
        if (highlightItemOnHover && store.state.selectedIndex === null && store.state.activeIndex === null && store.context.listRef.current[0] != null) {
          store.set("activeIndex", 0);
        }
      } finally {
        restoreTransformStyles();
      }
    }, [store, open2, positionerElement, triggerElement, alignItemWithTriggerActive, setControlledAlignItemWithTrigger, scrollArrowFrame, listElement, highlightItemOnHover, direction, isPositioned]);
    React79.useEffect(() => {
      if (!alignItemWithTriggerActive || !positionerElement || !open2) {
        return void 0;
      }
      const win = getWindow(positionerElement);
      function handleResize(event) {
        store.context.setOpen(false, createChangeEventDetails(reason_parts_exports.windowResize, event));
      }
      return addEventListener(win, "resize", handleResize);
    }, [store, alignItemWithTriggerActive, positionerElement, open2]);
    const defaultProps = {
      ...listElement ? {
        role: "presentation"
      } : {
        role: "listbox",
        "aria-multiselectable": multiple || void 0,
        "aria-readonly": readOnly || void 0,
        id: `${id}-list`
      },
      onKeyDown(event) {
        if (insideToolbar && COMPOSITE_KEYS.has(event.key)) {
          event.stopPropagation();
        }
      },
      onScroll(event) {
        if (listElement) {
          return;
        }
        handleScroll(event.currentTarget);
      },
      ...alignItemWithTriggerActive && {
        style: listElement ? {
          height: "100%"
        } : LIST_FUNCTIONAL_STYLES
      },
      className: !listElement && alignItemWithTriggerActive ? styleDisableScrollbar.className : void 0
    };
    const element = useRenderElement("div", componentProps, {
      ref: [forwardedRef, store.context.popupRef],
      state,
      stateAttributesMapping: stateAttributesMapping5,
      props: [popupProps, defaultProps, getDisabledMountTransitionStyles(transitionStatus), elementProps]
    });
    return /* @__PURE__ */ (0, import_jsx_runtime41.jsxs)(React79.Fragment, {
      children: [!disableStyleElements && styleDisableScrollbar.getElement(nonce), /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(FloatingFocusManager, {
        context: floatingRootContext,
        modal: false,
        disabled: !mounted,
        openInteractionType: openMethod,
        returnFocus: finalFocus,
        restoreFocus: true,
        children: element
      })]
    });
  });
  if (true) SelectPopup.displayName = "SelectPopup";
  function getMaxPopupHeight(popupStyles) {
    const maxHeightStyle = popupStyles.maxHeight;
    return maxHeightStyle.endsWith("px") ? parseFloat(maxHeightStyle) || Infinity : Infinity;
  }
  function getMaxScrollTop(scroller) {
    return getMaxScrollOffset(scroller.scrollHeight, scroller.clientHeight);
  }
  function getScale2(element) {
    return platform2.getScale(element);
  }
  function normalizeSize(size4, axis, scale) {
    return size4 / scale[axis];
  }
  function normalizeRect(rect, scale) {
    return rectToClientRect({
      x: normalizeSize(rect.x, "x", scale),
      y: normalizeSize(rect.y, "y", scale),
      width: normalizeSize(rect.width, "x", scale),
      height: normalizeSize(rect.height, "y", scale)
    });
  }
  var TRANSFORM_STYLE_RESETS = [["transform", "none"], ["scale", "1"], ["translate", "0 0"]];
  function unsetTransformStyles(popupElement) {
    const {
      style
    } = popupElement;
    const originalStyles = {};
    for (const [property, value] of TRANSFORM_STYLE_RESETS) {
      originalStyles[property] = style.getPropertyValue(property);
      style.setProperty(property, value, "important");
    }
    return () => {
      for (const [property] of TRANSFORM_STYLE_RESETS) {
        const originalValue = originalStyles[property];
        if (originalValue) {
          style.setProperty(property, originalValue);
        } else {
          style.removeProperty(property);
        }
      }
    };
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/list/SelectList.mjs
  var React80 = __toESM(require_react(), 1);
  var SelectList = /* @__PURE__ */ React80.forwardRef(function SelectList2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      ...elementProps
    } = componentProps;
    const store = useSelectRootContext();
    const multiple = store.useState("multiple");
    const readOnly = store.useState("readOnly");
    const {
      alignItemWithTriggerActive
    } = useSelectPositionerContext();
    const hasScrollArrows = store.useState("hasScrollArrows");
    const openMethod = store.useState("openMethod");
    const id = store.useState("id");
    const defaultProps = {
      id: `${id}-list`,
      role: "listbox",
      "aria-multiselectable": multiple || void 0,
      "aria-readonly": readOnly || void 0,
      onScroll(event) {
        store.context.scrollHandlerRef.current?.(event.currentTarget);
      },
      ...alignItemWithTriggerActive && {
        style: LIST_FUNCTIONAL_STYLES
      },
      className: hasScrollArrows && openMethod !== "touch" ? styleDisableScrollbar.className : void 0
    };
    const setListElement = store.useStateSetter("listElement");
    return useRenderElement("div", componentProps, {
      ref: [forwardedRef, setListElement],
      props: [defaultProps, elementProps]
    });
  });
  if (true) SelectList.displayName = "SelectList";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/item/SelectItem.mjs
  var React82 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/item/SelectItemContext.mjs
  var React81 = __toESM(require_react(), 1);
  var SelectItemContext = /* @__PURE__ */ React81.createContext(void 0);
  if (true) SelectItemContext.displayName = "SelectItemContext";
  function useSelectItemContext() {
    const context = React81.useContext(SelectItemContext);
    if (!context) {
      throw new Error(true ? "Base UI: SelectItemContext is missing. SelectItem parts must be placed within <Select.Item>." : formatErrorMessage_default(57));
    }
    return context;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/item/SelectItem.mjs
  var import_jsx_runtime42 = __toESM(require_jsx_runtime(), 1);
  var SelectItem = /* @__PURE__ */ React82.memo(/* @__PURE__ */ React82.forwardRef(function SelectItem2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      value: itemValue = null,
      label,
      disabled: disabledProp = false,
      nativeButton = false,
      ...elementProps
    } = componentProps;
    const textRef = React82.useRef(null);
    const listItem = useCompositeListItem({
      guess: true,
      label,
      textRef
    });
    const store = useSelectRootContext();
    const {
      props: itemProps,
      multiple,
      disabled: selectDisabled,
      readOnly,
      isItemEqualToValue
    } = store.useState("itemRoot");
    const disabled2 = selectDisabled || disabledProp;
    const highlighted = store.useState("isActive", listItem.index);
    const open2 = store.useState("open");
    const selected = store.useState("isSelected", itemValue);
    const selectedByFocus = store.useState("isSelectedByFocus", listItem.index);
    const index3 = listItem.index;
    const itemRef = React82.useRef(null);
    useIsoLayoutEffect(() => {
      const values = store.context.valuesRef.current;
      values[index3] = itemValue;
      return () => {
        delete values[index3];
      };
    }, [index3, itemValue, store]);
    useIsoLayoutEffect(() => {
      const selectedValue = store.state.value;
      const currentIndex = store.state.selectedIndex;
      let nextIndex = currentIndex;
      let claims;
      if (multiple && Array.isArray(selectedValue)) {
        nextIndex = resolveSelectedIndex(index3, itemValue, store.context.valuesRef.current, selectedValue, isItemEqualToValue, currentIndex);
        claims = nextIndex === index3;
        if (index3 === currentIndex && !claims) {
          store.context.selectedItemTextRef.current = null;
        }
      } else {
        claims = selectedValue !== void 0 && compareItemEquality(itemValue, selectedValue, isItemEqualToValue);
        if (claims) {
          nextIndex = index3;
        }
      }
      store.set("selectedIndex", nextIndex);
      if (claims && textRef.current) {
        store.context.selectedItemTextRef.current = textRef.current;
      }
    }, [index3, multiple, isItemEqualToValue, store, itemValue]);
    const pointerTypeRef = React82.useRef("mouse");
    const allowMouseSelectionRef = React82.useRef(false);
    const {
      getButtonProps,
      buttonRef
    } = useButton({
      disabled: disabled2,
      focusableWhenDisabled: true,
      native: nativeButton,
      composite: true
    });
    const state = {
      disabled: disabled2,
      selected,
      highlighted
    };
    function commitSelection(event) {
      if (selectDisabled || readOnly) {
        return;
      }
      const selectedValue = store.state.value;
      if (multiple) {
        const currentValue = Array.isArray(selectedValue) ? selectedValue : [];
        const nextValue = selected ? removeItem(currentValue, itemValue, isItemEqualToValue) : [...currentValue, itemValue];
        store.context.setValue(nextValue, createChangeEventDetails(reason_parts_exports.itemPress, event));
      } else {
        store.context.setValue(itemValue, createChangeEventDetails(reason_parts_exports.itemPress, event));
        store.context.setOpen(false, createChangeEventDetails(reason_parts_exports.itemPress, event));
      }
    }
    function resetDragMovement() {
      store.context.selectionRef.current.dragY = 0;
    }
    const defaultProps = {
      role: "option",
      "aria-selected": selected,
      tabIndex: open2 && highlighted ? 0 : -1,
      onKeyDown(event) {
        store.set("activeIndex", index3);
        if (event.key === " " && store.context.typingRef.current) {
          event.preventDefault();
        }
      },
      onClick(event) {
        const isMouseClick = pointerTypeRef.current !== "touch";
        const clickPointerType = event.nativeEvent.pointerType;
        const isVirtualMouseClick = isMouseClick && isVirtualClick(event.nativeEvent) && // Generic no-pointer `detail === 0` clicks stay tied to highlight state. Virtual
        // clicks that carry browser pointer data, including an empty string from assistive
        // technology, can activate unhighlighted items.
        (clickPointerType !== void 0 || highlighted);
        const isInvalidMouseClick = isMouseClick && !isVirtualMouseClick && !allowMouseSelectionRef.current;
        allowMouseSelectionRef.current = false;
        if (disabled2 || isInvalidMouseClick) {
          return;
        }
        commitSelection(event.nativeEvent);
      },
      onPointerEnter(event) {
        pointerTypeRef.current = event.pointerType;
      },
      onPointerMove(event) {
        if (event.pointerType === "mouse" && event.buttons === 1) {
          const selection = store.context.selectionRef.current;
          selection.dragY += event.movementY;
          if (selection.dragY ** 2 >= 64) {
            selection.allowUnselectedMouseUp = true;
          }
        }
      },
      onPointerDown(event) {
        pointerTypeRef.current = event.pointerType;
        allowMouseSelectionRef.current = true;
        resetDragMovement();
      },
      onMouseUp() {
        resetDragMovement();
        if (disabled2 || pointerTypeRef.current === "touch") {
          return;
        }
        if (allowMouseSelectionRef.current) {
          return;
        }
        const disallowSelectedMouseUp = !store.context.selectionRef.current.allowSelectedMouseUp && selected;
        const disallowUnselectedMouseUp = !store.context.selectionRef.current.allowUnselectedMouseUp && !selected;
        if (disallowSelectedMouseUp || disallowUnselectedMouseUp) {
          return;
        }
        allowMouseSelectionRef.current = true;
        itemRef.current?.click();
        allowMouseSelectionRef.current = false;
      }
    };
    const element = useRenderElement("div", componentProps, {
      ref: [buttonRef, forwardedRef, listItem.ref, itemRef],
      state,
      props: [itemProps, defaultProps, elementProps, getButtonProps]
    });
    const contextValue = React82.useMemo(() => ({
      selected,
      index: index3,
      textRef,
      selectedByFocus
    }), [selected, index3, textRef, selectedByFocus]);
    return /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(SelectItemContext.Provider, {
      value: contextValue,
      children: element
    });
  }));
  if (true) SelectItem.displayName = "SelectItem";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/item-indicator/SelectItemIndicator.mjs
  var React83 = __toESM(require_react(), 1);
  var import_jsx_runtime43 = __toESM(require_jsx_runtime(), 1);
  var SelectItemIndicator = /* @__PURE__ */ React83.forwardRef(function SelectItemIndicator2(componentProps, forwardedRef) {
    const {
      selected
    } = useSelectItemContext();
    const shouldRender = componentProps.keepMounted || selected;
    if (!shouldRender) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(ItemIndicator, {
      ...componentProps,
      selected,
      ref: forwardedRef
    });
  });
  if (true) SelectItemIndicator.displayName = "SelectItemIndicator";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/item-text/SelectItemText.mjs
  var React84 = __toESM(require_react(), 1);
  var SelectItemText = /* @__PURE__ */ React84.memo(/* @__PURE__ */ React84.forwardRef(function SelectItemText2(componentProps, forwardedRef) {
    const {
      index: index3,
      textRef,
      selectedByFocus
    } = useSelectItemContext();
    const store = useSelectRootContext();
    const {
      render,
      className,
      style,
      ...elementProps
    } = componentProps;
    const localRef = React84.useCallback((node) => {
      if (!node) {
        return;
      }
      if (index3 === 0) {
        store.context.firstItemTextRef.current = node;
      }
      if (selectedByFocus) {
        store.context.selectedItemTextRef.current = node;
      }
    }, [store, index3, selectedByFocus]);
    const element = useRenderElement("div", componentProps, {
      ref: [localRef, forwardedRef, textRef],
      props: elementProps
    });
    return element;
  }));
  if (true) SelectItemText.displayName = "SelectItemText";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/arrow/SelectArrow.mjs
  var React85 = __toESM(require_react(), 1);
  var SelectArrow = /* @__PURE__ */ React85.forwardRef(function SelectArrow2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      ...elementProps
    } = componentProps;
    const store = useSelectRootContext();
    const {
      side,
      align,
      arrowRef,
      arrowStyles,
      arrowUncentered,
      alignItemWithTriggerActive
    } = useSelectPositionerContext();
    const open2 = store.useState("open");
    const state = {
      open: open2,
      side,
      align,
      uncentered: arrowUncentered
    };
    const element = useRenderElement("div", componentProps, {
      state,
      ref: [arrowRef, forwardedRef],
      props: [{
        style: arrowStyles,
        "aria-hidden": true
      }, elementProps],
      stateAttributesMapping: popupTransitionStateMapping
    });
    if (alignItemWithTriggerActive) {
      return null;
    }
    return element;
  });
  if (true) SelectArrow.displayName = "SelectArrow";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/scroll-down-arrow/SelectScrollDownArrow.mjs
  var React87 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/scroll-arrow/SelectScrollArrow.mjs
  var React86 = __toESM(require_react(), 1);
  var SelectScrollArrow = /* @__PURE__ */ React86.forwardRef(function SelectScrollArrow2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      direction,
      keepMounted,
      ...elementProps
    } = componentProps;
    const isUp = direction === "up";
    const store = useSelectRootContext();
    const {
      side,
      scrollDownArrowRef,
      scrollUpArrowRef
    } = useSelectPositionerContext();
    const visibleSelector = isUp ? "scrollUpArrowVisible" : "scrollDownArrowVisible";
    const stateVisible = store.useState(visibleSelector);
    const openMethod = store.useState("openMethod");
    const visible = stateVisible && openMethod !== "touch";
    const timeout = useTimeout();
    const scrollArrowRef = isUp ? scrollUpArrowRef : scrollDownArrowRef;
    const {
      mounted,
      transitionStatus,
      setMounted
    } = useTransitionStatus(visible);
    useIsoLayoutEffect(() => {
      store.context.scrollArrowsMountedCountRef.current += 1;
      store.set("hasScrollArrows", true);
      return () => {
        store.context.scrollArrowsMountedCountRef.current = Math.max(0, store.context.scrollArrowsMountedCountRef.current - 1);
        if (store.context.scrollArrowsMountedCountRef.current === 0) {
          store.set("hasScrollArrows", false);
        }
      };
    }, [store]);
    useOpenChangeComplete({
      open: visible,
      ref: scrollArrowRef,
      onComplete() {
        if (!visible) {
          setMounted(false);
        }
      }
    });
    const state = {
      direction,
      visible,
      side,
      transitionStatus
    };
    const defaultProps = {
      "aria-hidden": true,
      children: isUp ? "\u25B2" : "\u25BC",
      style: {
        position: "absolute"
      },
      onMouseMove(event) {
        if (event.movementX === 0 && event.movementY === 0 || timeout.isStarted()) {
          return;
        }
        store.set("activeIndex", null);
        function scrollNextItem() {
          const scroller = store.state.listElement ?? store.context.popupRef.current;
          if (!scroller) {
            return;
          }
          store.set("activeIndex", null);
          store.context.handleScrollArrowVisibility(scroller);
          const maxScrollTop = getMaxScrollOffset(scroller.scrollHeight, scroller.clientHeight);
          const scrollTop = normalizeScrollOffset(scroller.scrollTop, maxScrollTop);
          const isScrolledToEdge = scrollTop === (isUp ? 0 : maxScrollTop);
          const items = store.context.listRef.current;
          if (scrollTop !== scroller.scrollTop) {
            scroller.scrollTop = scrollTop;
          }
          if (isScrolledToEdge) {
            timeout.clear();
            return;
          }
          if (items.length > 0) {
            const scrollArrowHeight = scrollArrowRef.current?.offsetHeight || 0;
            scroller.scrollTop = getTargetScrollTop(items, isUp, scrollTop, scroller.clientHeight, scrollArrowHeight, maxScrollTop);
          }
          timeout.start(40, scrollNextItem);
        }
        timeout.start(40, scrollNextItem);
      },
      onMouseLeave() {
        timeout.clear();
      }
    };
    const element = useRenderElement("div", componentProps, {
      ref: [forwardedRef, scrollArrowRef],
      state,
      props: [defaultProps, elementProps],
      stateAttributesMapping: transitionStatusMapping
    });
    const shouldRender = mounted || keepMounted;
    if (!shouldRender) {
      return null;
    }
    return element;
  });
  if (true) SelectScrollArrow.displayName = "SelectScrollArrow";
  function getTargetScrollTop(items, isUp, scrollTop, clientHeight, scrollArrowHeight, maxScrollTop) {
    if (isUp) {
      let firstVisibleIndex = 0;
      const visibleTop = scrollTop + scrollArrowHeight - SCROLL_EDGE_TOLERANCE_PX;
      for (let i = 0; i < items.length; i += 1) {
        const item = items[i];
        if (item && item.offsetTop >= visibleTop) {
          firstVisibleIndex = i;
          break;
        }
      }
      const targetIndex2 = Math.max(0, firstVisibleIndex - 1);
      const targetItem2 = items[targetIndex2];
      return targetIndex2 < firstVisibleIndex && targetItem2 ? normalizeScrollOffset(targetItem2.offsetTop - scrollArrowHeight, maxScrollTop) : 0;
    }
    let lastVisibleIndex = items.length - 1;
    const visibleBottom = scrollTop + clientHeight - scrollArrowHeight + SCROLL_EDGE_TOLERANCE_PX;
    for (let i = 0; i < items.length; i += 1) {
      const item = items[i];
      if (item && item.offsetTop + item.offsetHeight > visibleBottom) {
        lastVisibleIndex = Math.max(0, i - 1);
        break;
      }
    }
    const targetIndex = Math.min(items.length - 1, lastVisibleIndex + 1);
    const targetItem = items[targetIndex];
    return targetIndex > lastVisibleIndex && targetItem ? normalizeScrollOffset(targetItem.offsetTop + targetItem.offsetHeight - clientHeight + scrollArrowHeight, maxScrollTop) : maxScrollTop;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/scroll-down-arrow/SelectScrollDownArrow.mjs
  var import_jsx_runtime44 = __toESM(require_jsx_runtime(), 1);
  var SelectScrollDownArrow = /* @__PURE__ */ React87.forwardRef(function SelectScrollDownArrow2(props, forwardedRef) {
    return /* @__PURE__ */ (0, import_jsx_runtime44.jsx)(SelectScrollArrow, {
      ...props,
      ref: forwardedRef,
      direction: "down"
    });
  });
  if (true) SelectScrollDownArrow.displayName = "SelectScrollDownArrow";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/scroll-up-arrow/SelectScrollUpArrow.mjs
  var React88 = __toESM(require_react(), 1);
  var import_jsx_runtime45 = __toESM(require_jsx_runtime(), 1);
  var SelectScrollUpArrow = /* @__PURE__ */ React88.forwardRef(function SelectScrollUpArrow2(props, forwardedRef) {
    return /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(SelectScrollArrow, {
      ...props,
      ref: forwardedRef,
      direction: "up"
    });
  });
  if (true) SelectScrollUpArrow.displayName = "SelectScrollUpArrow";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/group/SelectGroup.mjs
  var React90 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/group/SelectGroupContext.mjs
  var React89 = __toESM(require_react(), 1);
  var SelectGroupContext = /* @__PURE__ */ React89.createContext(void 0);
  if (true) SelectGroupContext.displayName = "SelectGroupContext";
  function useSelectGroupContext() {
    const context = React89.useContext(SelectGroupContext);
    if (context === void 0) {
      throw new Error(true ? "Base UI: SelectGroupContext is missing. SelectGroup parts must be placed within <Select.Group>." : formatErrorMessage_default(56));
    }
    return context;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/group/SelectGroup.mjs
  var import_jsx_runtime46 = __toESM(require_jsx_runtime(), 1);
  var SelectGroup = /* @__PURE__ */ React90.forwardRef(function SelectGroup2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      ...elementProps
    } = componentProps;
    const [labelId, setLabelId] = React90.useState();
    const contextValue = React90.useMemo(() => ({
      labelId,
      setLabelId
    }), [labelId, setLabelId]);
    const element = useRenderElement("div", componentProps, {
      ref: forwardedRef,
      props: [{
        role: "group",
        "aria-labelledby": labelId
      }, elementProps]
    });
    return /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(SelectGroupContext.Provider, {
      value: contextValue,
      children: element
    });
  });
  if (true) SelectGroup.displayName = "SelectGroup";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/group-label/SelectGroupLabel.mjs
  var React91 = __toESM(require_react(), 1);
  var SelectGroupLabel = /* @__PURE__ */ React91.forwardRef(function SelectGroupLabel2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      id: idProp,
      ...elementProps
    } = componentProps;
    const {
      setLabelId
    } = useSelectGroupContext();
    const id = useBaseUiId(idProp);
    useIsoLayoutEffect(() => {
      setLabelId(id);
      return () => {
        setLabelId((currentId) => currentId === id ? void 0 : currentId);
      };
    }, [id, setLabelId]);
    const element = useRenderElement("div", componentProps, {
      ref: forwardedRef,
      props: [{
        id,
        "aria-hidden": true
      }, elementProps]
    });
    return element;
  });
  if (true) SelectGroupLabel.displayName = "SelectGroupLabel";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/select/separator/SelectSeparator.mjs
  var SelectSeparator = ListboxSeparator;

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/prehydrationScript.stub.mjs
  var script = "";

  // node_modules/.store/@base-ui/utils@0.5.0--7My7Lubseca8je9LSEweA/node_modules/@base-ui/utils/hydration.mjs
  var React92 = __toESM(require_react(), 1);
  var import_shim2 = __toESM(require_shim(), 1);
  var emptySubscribe = () => () => {
  };
  var getTrue = () => true;
  var getFalse = () => false;
  function useIsHydratedModern() {
    return (0, import_shim2.useSyncExternalStore)(emptySubscribe, getTrue, getFalse);
  }
  function useIsHydratedLegacy() {
    const [hydrated, setHydrated] = React92.useState(false);
    React92.useEffect(() => {
      setHydrated(true);
    }, []);
    return hydrated;
  }
  var useIsHydrated = isReactVersionAtLeast(18) ? useIsHydratedModern : useIsHydratedLegacy;
  function useIsHydrating() {
    return !useIsHydrated();
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/internals/PrehydrationScript.mjs
  var React93 = __toESM(require_react(), 1);
  var import_jsx_runtime47 = __toESM(require_jsx_runtime(), 1);
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
    return /* @__PURE__ */ (0, import_jsx_runtime47.jsx)("script", {
      nonce,
      dangerouslySetInnerHTML: {
        __html: script2
      },
      suppressHydrationWarning: true
    });
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/index.parts.mjs
  var index_parts_exports3 = {};
  __export(index_parts_exports3, {
    Indicator: () => TabsIndicator,
    List: () => TabsList,
    Panel: () => TabsPanel,
    Root: () => TabsRoot,
    Tab: () => TabsTab
  });

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/root/TabsRoot.mjs
  var React95 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/root/TabsRootContext.mjs
  var React94 = __toESM(require_react(), 1);
  var TabsRootContext = /* @__PURE__ */ React94.createContext(void 0);
  if (true) TabsRootContext.displayName = "TabsRootContext";
  function useTabsRootContext() {
    const context = React94.useContext(TabsRootContext);
    if (context === void 0) {
      throw new Error(true ? "Base UI: TabsRootContext is missing. Tabs parts must be placed within <Tabs.Root>." : formatErrorMessage_default(64));
    }
    return context;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/root/TabsRootDataAttributes.mjs
  var activationDirection = "data-activation-direction";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/root/stateAttributesMapping.mjs
  var tabsStateAttributesMapping = {
    tabActivationDirection: (dir) => ({
      [activationDirection]: dir
    })
  };

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/root/TabsRoot.mjs
  var import_jsx_runtime48 = __toESM(require_jsx_runtime(), 1);
  var TabsRoot = /* @__PURE__ */ React95.forwardRef(function TabsRoot2(componentProps, forwardedRef) {
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
    const tabPanelRefs = React95.useRef([]);
    const [mountedTabPanels, setMountedTabPanels] = React95.useState(() => /* @__PURE__ */ new Map());
    const [value, setValue] = useControlled({
      controlled: valueProp,
      default: defaultValueProp,
      name: "Tabs",
      state: "value"
    });
    const isControlled = valueProp !== void 0;
    const [tabMap, setTabMap] = React95.useState(() => /* @__PURE__ */ new Map());
    const lastKnownTabElementRef = React95.useRef(void 0);
    const getTabElementBySelectedValue = React95.useCallback((selectedValue) => findTabElement(tabMap, selectedValue), [tabMap]);
    const [activationDirectionState, setActivationDirectionState] = React95.useState(() => ({
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
      setValue(() => newValue);
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
    const getTabPanelIdByValue = React95.useCallback((tabValue) => {
      return mountedTabPanels.get(tabValue);
    }, [mountedTabPanels]);
    const getTabIdByPanelValue = React95.useCallback((tabPanelValue) => {
      for (const tabMetadata of tabMap.values()) {
        if (tabPanelValue === tabMetadata.value) {
          return tabMetadata.id;
        }
      }
      return void 0;
    }, [tabMap]);
    const tabsContextValue = React95.useMemo(() => ({
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
    const selectedTabMetadata = React95.useMemo(() => {
      for (const tabMetadata of tabMap.values()) {
        if (tabMetadata.value === value) {
          return tabMetadata;
        }
      }
      return void 0;
    }, [tabMap, value]);
    const firstEnabledTabValue = React95.useMemo(() => {
      for (const tabMetadata of tabMap.values()) {
        if (!tabMetadata.disabled) {
          return tabMetadata.value;
        }
      }
      return void 0;
    }, [tabMap]);
    const shouldNotifyInitialValueChangeRef = React95.useRef(!hasExplicitDefaultValueProp);
    const initialDefaultValueRef = React95.useRef(defaultValueProp);
    const shouldHonorDisabledDefaultValueRef = React95.useRef(hasExplicitDefaultValueProp);
    const didRegisterTabsRef = React95.useRef(false);
    useIsoLayoutEffect(() => {
      if (isControlled) {
        return;
      }
      function commitAutomaticValueChange(fallbackValue, fallbackReason) {
        setValue(() => fallbackValue);
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
    return /* @__PURE__ */ (0, import_jsx_runtime48.jsx)(TabsRootContext.Provider, {
      value: tabsContextValue,
      children: /* @__PURE__ */ (0, import_jsx_runtime48.jsx)(CompositeList, {
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/tab/TabsTab.mjs
  var React97 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/list/TabsListContext.mjs
  var React96 = __toESM(require_react(), 1);
  var TabsListContext = /* @__PURE__ */ React96.createContext(void 0);
  if (true) TabsListContext.displayName = "TabsListContext";
  function useTabsListContext() {
    const context = React96.useContext(TabsListContext);
    if (context === void 0) {
      throw new Error(true ? "Base UI: TabsListContext is missing. TabsList parts must be placed within <Tabs.List>." : formatErrorMessage_default(65));
    }
    return context;
  }

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/tab/TabsTab.mjs
  var TabsTab = /* @__PURE__ */ React97.forwardRef(function TabsTab2(componentProps, forwardedRef) {
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
    const tabMetadata = React97.useMemo(() => ({
      disabled: disabled2,
      id,
      value
    }), [disabled2, id, value]);
    const {
      compositeProps,
      compositeRef,
      index: index3
      // hook is used instead of the CompositeItem component
      // because the index is needed for Tab internals
    } = useCompositeItem({
      metadata: tabMetadata
    });
    const active = value === activeTabValue;
    const isNavigatingRef = React97.useRef(false);
    const unobserveTabElementRef = React97.useRef(null);
    const observeTabElement = useStableCallback((element2) => {
      unobserveTabElementRef.current?.();
      unobserveTabElementRef.current = element2 ? registerTabResizeObserverElement(element2) : null;
    });
    useIsoLayoutEffect(() => {
      if (isNavigatingRef.current) {
        isNavigatingRef.current = false;
        return;
      }
      if (!(active && index3 > -1 && highlightedIndex !== index3)) {
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
        onHighlightedIndexChange(index3);
      }
    }, [active, index3, highlightedIndex, onHighlightedIndexChange, disabled2, tabsListElement]);
    const {
      getButtonProps,
      buttonRef
    } = useButton({
      disabled: disabled2,
      native: nativeButton,
      focusableWhenDisabled: true
    });
    const tabPanelId = getTabPanelIdByValue(value);
    const isPressingRef = React97.useRef(false);
    const isMainButtonRef = React97.useRef(false);
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/indicator/TabsIndicator.mjs
  var React98 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/indicator/TabsIndicatorCssVars.mjs
  var activeTabLeft = "--active-tab-left";
  var activeTabRight = "--active-tab-right";
  var activeTabTop = "--active-tab-top";
  var activeTabBottom = "--active-tab-bottom";
  var activeTabWidth = "--active-tab-width";
  var activeTabHeight = "--active-tab-height";

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/indicator/TabsIndicator.mjs
  var import_jsx_runtime49 = __toESM(require_jsx_runtime(), 1);
  var _PrehydrationScript;
  var stateAttributesMapping6 = {
    ...tabsStateAttributesMapping,
    activeTabPosition: () => null,
    activeTabSize: () => null
  };
  var MAX_LAYOUT_ROUNDING_ERROR = 2;
  var TabsIndicator = /* @__PURE__ */ React98.forwardRef(function TabsIndicator2(componentProps, forwardedRef) {
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
    React98.useEffect(() => {
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
        } = getCssDimensions2(activeTab);
        const {
          width: tabListWidth,
          height: tabListHeight
        } = getCssDimensions2(tabsListElement);
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
      stateAttributesMapping: stateAttributesMapping6
    });
    if (value == null) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime49.jsxs)(React98.Fragment, {
      children: [element, renderBeforeHydration && (_PrehydrationScript || (_PrehydrationScript = /* @__PURE__ */ (0, import_jsx_runtime49.jsx)(PrehydrationScript, {
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/panel/TabsPanel.mjs
  var React99 = __toESM(require_react(), 1);

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/panel/TabsPanelDataAttributes.mjs
  var index2 = "data-index";
  var startingStyle2 = TransitionStatusDataAttributes_exports.startingStyle;
  var endingStyle2 = TransitionStatusDataAttributes_exports.endingStyle;

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/panel/TabsPanel.mjs
  var stateAttributesMapping7 = {
    ...tabsStateAttributesMapping,
    ...transitionStatusMapping
  };
  var TabsPanel = /* @__PURE__ */ React99.forwardRef(function TabsPanel2(componentProps, forwardedRef) {
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
      index: index3
    } = useCompositeListItem();
    const open2 = value === selectedValue;
    const {
      mounted,
      transitionStatus,
      setMounted
    } = useTransitionStatus(open2);
    const hidden = !mounted;
    const correspondingTabId = getTabIdByPanelValue(value);
    const state = {
      hidden,
      orientation,
      tabActivationDirection,
      transitionStatus
    };
    const panelRef = React99.useRef(null);
    const element = useRenderElement("div", componentProps, {
      state,
      ref: [forwardedRef, listItemRef, panelRef],
      props: [{
        "aria-labelledby": correspondingTabId,
        hidden,
        id,
        role: "tabpanel",
        tabIndex: open2 ? 0 : -1,
        inert: inertValue(!open2),
        // Computed key: a plain literal key fails the DOM-props excess property check.
        [index2]: index3
      }, elementProps],
      stateAttributesMapping: stateAttributesMapping7
    });
    useOpenChangeComplete({
      open: open2,
      ref: panelRef,
      onComplete() {
        if (!open2) {
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/tabs/list/TabsList.mjs
  var React100 = __toESM(require_react(), 1);
  var import_jsx_runtime50 = __toESM(require_jsx_runtime(), 1);
  var TabsList = /* @__PURE__ */ React100.forwardRef(function TabsList2(componentProps, forwardedRef) {
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
    const [highlightedTabIndex, setHighlightedTabIndex] = React100.useState(0);
    const [tabsListElement, setTabsListElement] = React100.useState(null);
    const indicatorUpdateListenersRef = React100.useRef(/* @__PURE__ */ new Set());
    const tabResizeObserverElementsRef = React100.useRef(/* @__PURE__ */ new Set());
    const resizeObserverRef = React100.useRef(null);
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
    const tabsListContextValue = React100.useMemo(() => ({
      activateOnFocus,
      registerIndicatorUpdateListener,
      registerTabResizeObserverElement,
      tabsListElement
    }), [activateOnFocus, registerIndicatorUpdateListener, registerTabResizeObserverElement, tabsListElement]);
    return /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(TabsListContext.Provider, {
      value: tabsListContextValue,
      children: /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(CompositeRoot, {
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

  // node_modules/.store/@base-ui/react@1.9.0-uHNeM94OdjIeOtpyYGvL0A/node_modules/@base-ui/react/use-render/useRender.mjs
  function useRender(params) {
    return useRenderElement(params.defaultTagName ?? "div", params, params);
  }

  // packages/ui/build-module/text/text.mjs
  var import_element9 = __toESM(require_element(), 1);
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
    registerStyle("3167e7d116", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._83ed8a8da5dd50ea__text{text-wrap:pretty;margin:0}._14437cfb77831647__heading-2xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-2xl,32px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-2xl,32px);--_gcd-p-line-height:var(--wpds-typography-line-height-2xl,40px);font-size:var(--wpds-typography-font-size-2xl,32px);line-height:var(--wpds-typography-line-height-2xl,40px)}._14437cfb77831647__heading-2xl,._3c78b7fa9b4072dd__heading-xl{font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-emphasis,600)}._3c78b7fa9b4072dd__heading-xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-p-line-height:var(--wpds-typography-line-height-md,24px);font-size:var(--wpds-typography-font-size-xl,20px);line-height:var(--wpds-typography-line-height-md,24px)}.aa58f227716bcde2__heading-lg{--_gcd-heading-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-p-line-height:var(--wpds-typography-line-height-sm,20px);font-size:var(--wpds-typography-font-size-lg,15px)}.aa58f227716bcde2__heading-lg,.fc4da56d8dfe52c4__heading-md{font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-emphasis,600);line-height:var(--wpds-typography-line-height-sm,20px)}.fc4da56d8dfe52c4__heading-md{--_gcd-heading-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-p-line-height:var(--wpds-typography-line-height-sm,20px);font-size:var(--wpds-typography-font-size-md,13px)}.a9b78c7c82e8dff7__heading-sm{--_gcd-heading-font-size:var(--wpds-typography-font-size-xs,11px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-xs,11px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-xs,11px);font-weight:var(--wpds-typography-font-weight-emphasis,600);line-height:var(--wpds-typography-line-height-xs,16px);text-transform:uppercase}._305ff559e52180d5__body-xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-default,400);--_gcd-p-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-p-line-height:var(--wpds-typography-line-height-xl,32px);font-size:var(--wpds-typography-font-size-xl,20px);line-height:var(--wpds-typography-line-height-xl,32px)}._305ff559e52180d5__body-xl,.ca1aa3fc2029e958__body-lg{font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-default,400)}.ca1aa3fc2029e958__body-lg{--_gcd-heading-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-default,400);--_gcd-p-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-p-line-height:var(--wpds-typography-line-height-md,24px);font-size:var(--wpds-typography-font-size-lg,15px);line-height:var(--wpds-typography-line-height-md,24px)}._131101940be12424__body-md{--_gcd-heading-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-default,400);--_gcd-p-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-p-line-height:var(--wpds-typography-line-height-sm,20px);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px)}._0e8d87a42c1f75fa__body-sm,._131101940be12424__body-md{font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-default,400)}._0e8d87a42c1f75fa__body-sm{--_gcd-heading-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-default,400);--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);font-size:var(--wpds-typography-font-size-sm,12px);line-height:var(--wpds-typography-line-height-xs,16px)}}}');
  }
  var style_default = { "text": "_83ed8a8da5dd50ea__text", "heading-2xl": "_14437cfb77831647__heading-2xl", "heading-xl": "_3c78b7fa9b4072dd__heading-xl", "heading-lg": "aa58f227716bcde2__heading-lg", "heading-md": "fc4da56d8dfe52c4__heading-md", "heading-sm": "a9b78c7c82e8dff7__heading-sm", "body-xl": "_305ff559e52180d5__body-xl", "body-lg": "ca1aa3fc2029e958__body-lg", "body-md": "_131101940be12424__body-md", "body-sm": "_0e8d87a42c1f75fa__body-sm" };
  if (typeof process === "undefined" || true) {
    registerStyle("abb19c3171", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}[role=treeitem] span._400bf7ee0c6061a9__span[aria-hidden]{display:var(--_gcd-span-display,inline);font-family:var(--_gcd-span-font-family,inherit);font-size:var(--_gcd-span-font-size,inherit);line-height:var(--_gcd-span-line-height,1);pointer-events:var(--_gcd-span-pointer-events,auto);position:var(--_gcd-span-position,static)}");
  }
  var global_css_defense_default = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li", "span": "_400bf7ee0c6061a9__span" };
  var Text = (0, import_element9.forwardRef)(
    function UnforwardedText({ variant = "body-md", render, className, ...props }, ref) {
      const element = useRender({
        render,
        defaultTagName: "span",
        ref,
        props: mergeProps(props, {
          className: clsx_default(
            style_default.text,
            global_css_defense_default.heading,
            global_css_defense_default.p,
            style_default[variant],
            className
          )
        })
      });
      return element;
    }
  );

  // packages/ui/build-module/spinner/spinner.mjs
  var import_element10 = __toESM(require_element(), 1);
  var import_jsx_runtime51 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle2("bc09fb824f", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.ab4d64c07c0ba587__spinner{background-color:transparent;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:inline-block;height:var(--wpds-dimension-size-2xs,16px);opacity:1;overflow:visible;position:relative;width:var(--wpds-dimension-size-2xs,16px)}.a7654e10245bb7d2__indicator{fill:transparent;stroke:currentColor;stroke-width:var(--wpds-border-width-sm,2px);stroke-linecap:round;transform-origin:50% 50%;@media (forced-colors:active){stroke:CanvasText}animation:_02322d973909703c__spinner-spin 1.4s linear infinite both}@keyframes _02322d973909703c__spinner-spin{0%{transform:rotate(0deg)}to{transform:rotate(1turn)}}}}");
  }
  var style_default2 = { "spinner": "ab4d64c07c0ba587__spinner", "indicator": "a7654e10245bb7d2__indicator", "spinner-spin": "_02322d973909703c__spinner-spin" };
  var Spinner = (0, import_element10.forwardRef)(
    function UnforwardedSpinner({ className, color, style, ...props }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime51.jsx)(
        "svg",
        {
          className: clsx_default(style_default2.spinner, className),
          style: color === void 0 ? style : { ...style, color },
          viewBox: "0 0 100 100",
          xmlns: "http://www.w3.org/2000/svg",
          role: "presentation",
          focusable: "false",
          ...props,
          ref,
          children: /* @__PURE__ */ (0, import_jsx_runtime51.jsx)(
            "path",
            {
              className: style_default2.indicator,
              d: "m 50 0 a 50 50 0 0 1 0 100",
              vectorEffect: "non-scaling-stroke"
            }
          )
        }
      );
    }
  );

  // packages/ui/build-module/visually-hidden/visually-hidden.mjs
  var import_element11 = __toESM(require_element(), 1);
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
    registerStyle3("15abcab4d0", ".f37b9e2e191ebd66__visually-hidden{border:0;clip-path:inset(50%);height:1px;margin:-1px;overflow:hidden;overflow-wrap:normal;padding:0;position:absolute;width:1px;word-break:normal}");
  }
  var style_default3 = { "visually-hidden": "f37b9e2e191ebd66__visually-hidden" };
  var VisuallyHidden = (0, import_element11.forwardRef)(
    function UnforwardedVisuallyHidden({ render, ...restProps }, ref) {
      const element = useRender({
        render,
        ref,
        props: mergeProps(
          { className: style_default3["visually-hidden"] },
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

  // packages/ui/build-module/utils/item-popup/item-content.mjs
  var import_element12 = __toESM(require_element(), 1);
  var VALIDATION_ENABLED = true;
  function parseItemContent(children, { Label: Label2, Description: Description2, validationMessage }) {
    const childArray = import_element12.Children.toArray(children);
    const [label, ...descriptions] = childArray;
    const hasLabel = (0, import_element12.isValidElement)(label) && label.type === Label2;
    const descriptionElements = descriptions.filter(
      (description) => (0, import_element12.isValidElement)(description) && description.type === Description2
    );
    if (VALIDATION_ENABLED && (!hasLabel || descriptionElements.length !== descriptions.length)) {
      throw new Error(validationMessage);
    }
    return {
      descriptionIds: descriptionElements.map(
        (description) => description.props.id
      ),
      hasLabel,
      labelId: hasLabel ? label.props.id : void 0
    };
  }
  function useItemContent(children, components, {
    "aria-describedby": ariaDescribedBy,
    "aria-label": ariaLabel,
    "aria-labelledby": ariaLabelledBy
  } = {}) {
    const generatedLabelId = (0, import_element12.useId)();
    const generatedDescriptionId = (0, import_element12.useId)();
    const { descriptionIds, hasLabel, labelId } = parseItemContent(
      children,
      components
    );
    const resolvedLabelId = hasLabel ? labelId ?? generatedLabelId : void 0;
    const resolvedDescriptionIds = descriptionIds.map(
      (descriptionId, index3) => descriptionId ?? `${generatedDescriptionId}-${index3}`
    );
    const itemDescribedBy = Array.from(
      /* @__PURE__ */ new Set([
        ...ariaDescribedBy?.split(/\s+/).filter(Boolean) ?? [],
        ...resolvedDescriptionIds
      ])
    ).join(" ");
    let descriptionIndex = 0;
    const { Label: Label2, Description: Description2, descriptionValidationToken } = components;
    const contentChildren = import_element12.Children.map(children, (child) => {
      if (!(0, import_element12.isValidElement)(child)) {
        return child;
      }
      if (child.type === Label2) {
        return child.props.id === resolvedLabelId ? child : (0, import_element12.cloneElement)(child, { id: resolvedLabelId });
      }
      if (child.type !== Description2) {
        return child;
      }
      const descriptionId = resolvedDescriptionIds[descriptionIndex++];
      if (!descriptionValidationToken && child.props.id === descriptionId) {
        return child;
      }
      return (0, import_element12.cloneElement)(child, {
        id: descriptionId,
        ...descriptionValidationToken && {
          validationToken: descriptionValidationToken
        }
      });
    });
    const labelledBy = ariaLabelledBy ?? (ariaLabel ? void 0 : resolvedLabelId);
    return {
      contentChildren,
      resolvedLabelId,
      itemAriaProps: {
        "aria-describedby": itemDescribedBy || void 0,
        "aria-label": ariaLabel,
        "aria-labelledby": labelledBy
      }
    };
  }

  // packages/ui/build-module/utils/render-slot-with-children.mjs
  var import_element13 = __toESM(require_element(), 1);
  function renderSlotWithChildren(slot, defaultSlot, children) {
    return (0, import_element13.cloneElement)(
      slot ?? defaultSlot,
      { children }
    );
  }

  // packages/ui/build-module/utils/wp-compat-overlay-slot.mjs
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
    registerStyle4("be37f31c1e", "._11fc52b637ff8a7e__slot{inset:0;isolation:isolate;pointer-events:none;position:fixed;z-index:1000000003}@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._11fc52b637ff8a7e__slot>*{pointer-events:auto}}}");
  }
  var wp_compat_overlay_slot_default = { "slot": "_11fc52b637ff8a7e__slot" };
  var WP_COMPAT_OVERLAY_SLOT_ATTRIBUTE = "data-wp-compat-overlay-slot";
  function resolveOwnerDocument() {
    return typeof document === "undefined" ? null : document;
  }
  function isInWordPressEnvironment() {
    let topWp;
    try {
      topWp = window.top?.wp;
    } catch {
    }
    const wp = topWp ?? window.wp;
    return typeof wp?.components === "object" && wp.components !== null;
  }
  var cachedSlot = null;
  function ensureSlotIsAccessible(element) {
    element.setAttribute("aria-hidden", "false");
    return element;
  }
  function createSlot(ownerDocument2) {
    const element = ownerDocument2.createElement("div");
    element.setAttribute(WP_COMPAT_OVERLAY_SLOT_ATTRIBUTE, "");
    if (wp_compat_overlay_slot_default.slot) {
      element.classList.add(wp_compat_overlay_slot_default.slot);
    }
    ownerDocument2.body.appendChild(element);
    return element;
  }
  function getWpCompatOverlaySlot() {
    if (typeof window === "undefined") {
      return void 0;
    }
    if (!isInWordPressEnvironment() && window.__wpUiCompatOverlaySlotEnabled !== true) {
      return void 0;
    }
    const ownerDocument2 = resolveOwnerDocument();
    if (!ownerDocument2 || !ownerDocument2.body) {
      return void 0;
    }
    if (cachedSlot && cachedSlot.ownerDocument === ownerDocument2 && cachedSlot.isConnected) {
      return ensureSlotIsAccessible(cachedSlot);
    }
    const existing = ownerDocument2.querySelector(
      `[${WP_COMPAT_OVERLAY_SLOT_ATTRIBUTE}]`
    );
    if (existing instanceof HTMLDivElement) {
      cachedSlot = ensureSlotIsAccessible(existing);
      return cachedSlot;
    }
    if (cachedSlot?.isConnected) {
      cachedSlot.remove();
    }
    cachedSlot = ensureSlotIsAccessible(createSlot(ownerDocument2));
    return cachedSlot;
  }

  // packages/ui/build-module/form/primitives/constants.mjs
  var ITEM_POPUP_POSITIONER_PROPS = {
    align: "start",
    sideOffset: 8,
    collisionPadding: 12
  };

  // packages/ui/build-module/utils/direction-provider.mjs
  var import_i18n3 = __toESM(require_i18n(), 1);
  var import_jsx_runtime52 = __toESM(require_jsx_runtime(), 1);
  function DirectionProvider3({ children }) {
    return /* @__PURE__ */ (0, import_jsx_runtime52.jsx)(DirectionProvider, { direction: (0, import_i18n3.isRTL)() ? "rtl" : "ltr", children });
  }

  // packages/ui/build-module/link/link.mjs
  var import_element14 = __toESM(require_element(), 1);
  var import_i18n4 = __toESM(require_i18n(), 1);
  var import_jsx_runtime53 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle5("10f3806643", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
  }
  var resets_default = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
  if (typeof process === "undefined" || true) {
    registerStyle5("7c3267f794", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{.af79fb116edb0dd7__outset-ring--focus:focus,.dfcfdc28396e5d98__outset-ring--focus-visible:focus-visible,.e5cd9ee879f6403a__outset-ring--focus-within:focus-within{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within,.abc777e9713fa711__outset-ring--focus-except-active:focus{outline:none}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.abc777e9713fa711__outset-ring--focus-except-active:focus:not(:active){--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}}");
  }
  var focus_module_default = { "outset-ring--focus": "af79fb116edb0dd7__outset-ring--focus", "outset-ring--focus-visible": "dfcfdc28396e5d98__outset-ring--focus-visible", "outset-ring--focus-within": "e5cd9ee879f6403a__outset-ring--focus-within", "outset-ring--focus-except-active": "abc777e9713fa711__outset-ring--focus-except-active", "outset-ring--focus-within-except-active": "_3c9f5ee9fc9c136d__outset-ring--focus-within-except-active" };
  if (typeof process === "undefined" || true) {
    registerStyle5("888e86d20a", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.d4250949359b05ce__link{--_gcd-a-border-radius:var(--wpds-border-radius-sm,2px);border-radius:var(--wpds-border-radius-sm,2px);text-decoration-thickness:from-font;text-underline-offset:.2em}.f037c632480d4e11__link-with-icon{text-decoration-line:none}._8dc7567f9ff415bf__link-contents{text-decoration:inherit;text-decoration-line:underline}.c6055659b8e2cd2c__is-brand,.c6055659b8e2cd2c__is-brand:visited{--_gcd-a-color:var(--wpds-color-foreground-interactive-brand,var(--wp-admin-theme-color,#3858e9));color:var(--wpds-color-foreground-interactive-brand,var(--wp-admin-theme-color,#3858e9))}.c6055659b8e2cd2c__is-brand:active,.c6055659b8e2cd2c__is-brand:hover{--_gcd-a-color:var(--wpds-color-foreground-interactive-brand-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 52%,#000));color:var(--wpds-color-foreground-interactive-brand-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 52%,#000))}._92e0dfcaeee15b88__is-neutral,._92e0dfcaeee15b88__is-neutral:visited{--_gcd-a-color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);text-decoration-color:var(--wpds-color-stroke-interactive-neutral,#8d8d8d)}._92e0dfcaeee15b88__is-neutral:active,._92e0dfcaeee15b88__is-neutral:hover{--_gcd-a-color:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e);color:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e)}.cf122a9bf1035d42__is-unstyled{--_gcd-a-border-radius:0;--_gcd-a-color:inherit;color:inherit;text-decoration:none}._0cb411afac4c86c7__link-icon{font-weight:var(--wpds-typography-font-weight-default,400);line-height:1;margin-inline-start:var(--wpds-dimension-padding-xs,4px);text-decoration:none}._0cb411afac4c86c7__link-icon:after{content:"\\2060\\2197"}._0cb411afac4c86c7__link-icon:dir(rtl):after{content:"\\2060\\2196"}}}');
  }
  var style_default4 = { "link": "d4250949359b05ce__link", "link-with-icon": "f037c632480d4e11__link-with-icon", "link-contents": "_8dc7567f9ff415bf__link-contents", "is-brand": "c6055659b8e2cd2c__is-brand", "is-neutral": "_92e0dfcaeee15b88__is-neutral", "is-unstyled": "cf122a9bf1035d42__is-unstyled", "link-icon": "_0cb411afac4c86c7__link-icon" };
  if (typeof process === "undefined" || true) {
    registerStyle5("abb19c3171", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}[role=treeitem] span._400bf7ee0c6061a9__span[aria-hidden]{display:var(--_gcd-span-display,inline);font-family:var(--_gcd-span-font-family,inherit);font-size:var(--_gcd-span-font-size,inherit);line-height:var(--_gcd-span-line-height,1);pointer-events:var(--_gcd-span-pointer-events,auto);position:var(--_gcd-span-position,static)}");
  }
  var global_css_defense_default2 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li", "span": "_400bf7ee0c6061a9__span" };
  var Link = (0, import_element14.forwardRef)(
    function UnforwardedLink({
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
            global_css_defense_default2.a,
            resets_default["box-sizing"],
            focus_module_default["outset-ring--focus-except-active"],
            variant !== "unstyled" && style_default4.link,
            variant !== "unstyled" && shouldShowNewTabIndicator && style_default4["link-with-icon"],
            variant !== "unstyled" && style_default4[`is-${tone}`],
            variant === "unstyled" && style_default4["is-unstyled"],
            className
          ),
          target: target ?? (openInNewTab ? "_blank" : void 0),
          children: /* @__PURE__ */ (0, import_jsx_runtime53.jsxs)(import_jsx_runtime53.Fragment, { children: [
            shouldShowNewTabIndicator && variant !== "unstyled" ? /* @__PURE__ */ (0, import_jsx_runtime53.jsx)("span", { className: style_default4["link-contents"], children }) : children,
            shouldShowNewTabIndicator && /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(
              "span",
              {
                className: style_default4["link-icon"],
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
    }
  );

  // packages/ui/build-module/stack/stack.mjs
  var import_element15 = __toESM(require_element(), 1);
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
    registerStyle6("32aba35fe1", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._19ce0419607e1896__stack{display:flex}}}");
  }
  var style_default5 = { "stack": "_19ce0419607e1896__stack" };
  var gapTokens = {
    xs: "var(--wpds-dimension-gap-xs, 4px)",
    sm: "var(--wpds-dimension-gap-sm, 8px)",
    md: "var(--wpds-dimension-gap-md, 12px)",
    lg: "var(--wpds-dimension-gap-lg, 16px)",
    xl: "var(--wpds-dimension-gap-xl, 24px)",
    "2xl": "var(--wpds-dimension-gap-2xl, 32px)",
    "3xl": "var(--wpds-dimension-gap-3xl, 40px)"
  };
  var Stack = (0, import_element15.forwardRef)(
    function UnforwardedStack({ direction, gap, align, justify, wrap, render, ...props }, ref) {
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
        props: mergeProps(props, {
          style,
          className: style_default5.stack
        })
      });
      return element;
    }
  );

  // packages/ui/build-module/utils/use-schedule-validation.mjs
  var import_compose = __toESM(require_compose(), 1);
  var import_element16 = __toESM(require_element(), 1);
  function useScheduleValidation(validate) {
    const validateEvent = (0, import_compose.useEvent)(validate);
    const timerRef = (0, import_element16.useRef)(null);
    const unmountedRef = (0, import_element16.useRef)(false);
    const scheduleValidation = (0, import_element16.useCallback)(() => {
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
    (0, import_element16.useEffect)(() => {
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
  var import_element19 = __toESM(require_element(), 1);

  // packages/ui/build-module/form/primitives/input-layout/input-layout.mjs
  var import_element17 = __toESM(require_element(), 1);
  var import_jsx_runtime54 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle7("abb19c3171", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}[role=treeitem] span._400bf7ee0c6061a9__span[aria-hidden]{display:var(--_gcd-span-display,inline);font-family:var(--_gcd-span-font-family,inherit);font-size:var(--_gcd-span-font-size,inherit);line-height:var(--_gcd-span-line-height,1);pointer-events:var(--_gcd-span-pointer-events,auto);position:var(--_gcd-span-position,static)}");
  }
  var global_css_defense_default3 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li", "span": "_400bf7ee0c6061a9__span" };
  if (typeof process === "undefined" || true) {
    registerStyle7("10f3806643", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
  }
  var resets_default2 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
  if (typeof process === "undefined" || true) {
    registerStyle7("fae9ae14d3", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.cb2baafdc08746bb__input-layout{--wp-ui-input-layout-padding-inline:var(--wpds-dimension-padding-md,12px);background-color:var(--wpds-color-background-interactive-neutral,var(--wpds-color-background-surface-neutral-strong,#fff));border-color:var(--wpds-color-stroke-interactive-neutral,#8d8d8d);border-radius:var(--wpds-border-radius-sm,2px);border-style:solid;border-width:var(--wpds-border-width-xs,1px);color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);height:var(--wpds-dimension-size-lg,40px);line-height:1;&._0c807a84cbb94e0c__is-size-compact{height:var(--wpds-dimension-size-md,32px)}&._0c807a84cbb94e0c__is-size-compact,&.ed67cda122dc1e7b__is-size-small{--wp-ui-input-layout-padding-inline:var(--wpds-dimension-padding-sm,8px)}&.ed67cda122dc1e7b__is-size-small{height:var(--wpds-dimension-size-sm,24px)}&._6fb7104732387680__is-disabled,&:has([data-can-disable-input-layout][data-disabled]){color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}&:not(._8097270636ca6100__is-borderless){background-color:var(--wpds-color-background-interactive-neutral-disabled,var(--wpds-color-background-surface-neutral-strong,#fff));border-color:var(--wpds-color-stroke-interactive-neutral-disabled,#dbdbdb);@media (forced-colors:active){border-bottom-color:GrayText;border-left-color:GrayText;border-right-color:GrayText;border-top-color:GrayText}}}&._8097270636ca6100__is-borderless{background-color:var(--wpds-color-background-interactive-neutral-weak,#0000);border-color:transparent}&:has(._0d7afad74a057888__input-layout-slot:focus-within){--_gcd-div-outline:none;outline:none}&:hover:not(._6fb7104732387680__is-disabled,:has([data-can-disable-input-layout][data-disabled]),._8097270636ca6100__is-borderless){background-color:var(--wpds-color-background-interactive-neutral-active,var(--wpds-color-background-surface-neutral-strong,#fff));border-color:var(--wpds-color-stroke-interactive-neutral-active,#6e6e6e)}&:has(:invalid[data-validity-visible]){--focus-color:var(--wpds-color-stroke-interactive-error,#cc1818);border-color:var(--wpds-color-stroke-interactive-error,#cc1818);&:hover{border-color:var(--wpds-color-stroke-interactive-error-active,#9d0000)}}}.c192b41a12b4387b__slot-wrapper{display:contents}._0d7afad74a057888__input-layout-slot{align-items:center;display:flex;&._0c952682762ca288__is-padding-minimal{--wp-ui-input-layout-prefix-padding-start:calc(var(--wp-ui-input-layout-padding-inline) - var(--wpds-dimension-padding-xs, 4px));--wp-ui-input-layout-suffix-padding-end:calc(var(--wp-ui-input-layout-padding-inline) - var(--wpds-dimension-padding-xs, 4px))}[data-slot-type=prefix] &{padding-inline-start:var(--wp-ui-input-layout-prefix-padding-start,var(--wp-ui-input-layout-padding-inline))}[data-slot-type=suffix] &{padding-inline-end:var(--wp-ui-input-layout-suffix-padding-end,var(--wp-ui-input-layout-padding-inline))}}}}');
  }
  var style_default6 = { "input-layout": "cb2baafdc08746bb__input-layout", "is-size-compact": "_0c807a84cbb94e0c__is-size-compact", "is-size-small": "ed67cda122dc1e7b__is-size-small", "is-disabled": "_6fb7104732387680__is-disabled", "is-borderless": "_8097270636ca6100__is-borderless", "input-layout-slot": "_0d7afad74a057888__input-layout-slot", "slot-wrapper": "c192b41a12b4387b__slot-wrapper", "is-padding-minimal": "_0c952682762ca288__is-padding-minimal" };
  var InputLayout = (0, import_element17.forwardRef)(
    function UnforwardedInputLayout({
      className,
      children,
      visuallyDisabled,
      size: size4 = "default",
      isBorderless,
      prefix,
      suffix,
      ...restProps
    }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime54.jsxs)(
        "div",
        {
          ref,
          className: clsx_default(
            global_css_defense_default3.div,
            resets_default2["box-sizing"],
            style_default6["input-layout"],
            style_default6[`is-size-${size4}`],
            visuallyDisabled && style_default6["is-disabled"],
            isBorderless && style_default6["is-borderless"],
            className
          ),
          ...restProps,
          children: [
            import_element17.Children.count(prefix) > 0 && /* @__PURE__ */ (0, import_jsx_runtime54.jsx)(
              "div",
              {
                className: style_default6["slot-wrapper"],
                "data-slot-type": "prefix",
                children: prefix
              }
            ),
            children,
            import_element17.Children.count(suffix) > 0 && /* @__PURE__ */ (0, import_jsx_runtime54.jsx)(
              "div",
              {
                className: style_default6["slot-wrapper"],
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
  var import_element18 = __toESM(require_element(), 1);
  var import_jsx_runtime55 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle8("fae9ae14d3", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.cb2baafdc08746bb__input-layout{--wp-ui-input-layout-padding-inline:var(--wpds-dimension-padding-md,12px);background-color:var(--wpds-color-background-interactive-neutral,var(--wpds-color-background-surface-neutral-strong,#fff));border-color:var(--wpds-color-stroke-interactive-neutral,#8d8d8d);border-radius:var(--wpds-border-radius-sm,2px);border-style:solid;border-width:var(--wpds-border-width-xs,1px);color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);height:var(--wpds-dimension-size-lg,40px);line-height:1;&._0c807a84cbb94e0c__is-size-compact{height:var(--wpds-dimension-size-md,32px)}&._0c807a84cbb94e0c__is-size-compact,&.ed67cda122dc1e7b__is-size-small{--wp-ui-input-layout-padding-inline:var(--wpds-dimension-padding-sm,8px)}&.ed67cda122dc1e7b__is-size-small{height:var(--wpds-dimension-size-sm,24px)}&._6fb7104732387680__is-disabled,&:has([data-can-disable-input-layout][data-disabled]){color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}&:not(._8097270636ca6100__is-borderless){background-color:var(--wpds-color-background-interactive-neutral-disabled,var(--wpds-color-background-surface-neutral-strong,#fff));border-color:var(--wpds-color-stroke-interactive-neutral-disabled,#dbdbdb);@media (forced-colors:active){border-bottom-color:GrayText;border-left-color:GrayText;border-right-color:GrayText;border-top-color:GrayText}}}&._8097270636ca6100__is-borderless{background-color:var(--wpds-color-background-interactive-neutral-weak,#0000);border-color:transparent}&:has(._0d7afad74a057888__input-layout-slot:focus-within){--_gcd-div-outline:none;outline:none}&:hover:not(._6fb7104732387680__is-disabled,:has([data-can-disable-input-layout][data-disabled]),._8097270636ca6100__is-borderless){background-color:var(--wpds-color-background-interactive-neutral-active,var(--wpds-color-background-surface-neutral-strong,#fff));border-color:var(--wpds-color-stroke-interactive-neutral-active,#6e6e6e)}&:has(:invalid[data-validity-visible]){--focus-color:var(--wpds-color-stroke-interactive-error,#cc1818);border-color:var(--wpds-color-stroke-interactive-error,#cc1818);&:hover{border-color:var(--wpds-color-stroke-interactive-error-active,#9d0000)}}}.c192b41a12b4387b__slot-wrapper{display:contents}._0d7afad74a057888__input-layout-slot{align-items:center;display:flex;&._0c952682762ca288__is-padding-minimal{--wp-ui-input-layout-prefix-padding-start:calc(var(--wp-ui-input-layout-padding-inline) - var(--wpds-dimension-padding-xs, 4px));--wp-ui-input-layout-suffix-padding-end:calc(var(--wp-ui-input-layout-padding-inline) - var(--wpds-dimension-padding-xs, 4px))}[data-slot-type=prefix] &{padding-inline-start:var(--wp-ui-input-layout-prefix-padding-start,var(--wp-ui-input-layout-padding-inline))}[data-slot-type=suffix] &{padding-inline-end:var(--wp-ui-input-layout-suffix-padding-end,var(--wp-ui-input-layout-padding-inline))}}}}');
  }
  var style_default7 = { "input-layout": "cb2baafdc08746bb__input-layout", "is-size-compact": "_0c807a84cbb94e0c__is-size-compact", "is-size-small": "ed67cda122dc1e7b__is-size-small", "is-disabled": "_6fb7104732387680__is-disabled", "is-borderless": "_8097270636ca6100__is-borderless", "input-layout-slot": "_0d7afad74a057888__input-layout-slot", "slot-wrapper": "c192b41a12b4387b__slot-wrapper", "is-padding-minimal": "_0c952682762ca288__is-padding-minimal" };
  var InputLayoutSlot = (0, import_element18.forwardRef)(function UnforwardedInputLayoutSlot({ padding = "default", className, ...restProps }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
      "div",
      {
        ref,
        className: clsx_default(
          style_default7["input-layout-slot"],
          style_default7[`is-padding-${padding}`],
          className
        ),
        ...restProps
      }
    );
  });
  InputLayoutSlot.displayName = "InputLayout.Slot";

  // packages/ui/build-module/form/primitives/input-layout/index.mjs
  var InputLayout2 = Object.assign(InputLayout, {
    Slot: InputLayoutSlot
  });

  // packages/ui/build-module/form/primitives/input/input.mjs
  var import_jsx_runtime56 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle9("abb19c3171", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}[role=treeitem] span._400bf7ee0c6061a9__span[aria-hidden]{display:var(--_gcd-span-display,inline);font-family:var(--_gcd-span-font-family,inherit);font-size:var(--_gcd-span-font-size,inherit);line-height:var(--_gcd-span-line-height,1);pointer-events:var(--_gcd-span-pointer-events,auto);position:var(--_gcd-span-position,static)}");
  }
  var global_css_defense_default4 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li", "span": "_400bf7ee0c6061a9__span" };
  if (typeof process === "undefined" || true) {
    registerStyle9("7c3267f794", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{.af79fb116edb0dd7__outset-ring--focus:focus,.dfcfdc28396e5d98__outset-ring--focus-visible:focus-visible,.e5cd9ee879f6403a__outset-ring--focus-within:focus-within{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within,.abc777e9713fa711__outset-ring--focus-except-active:focus{outline:none}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.abc777e9713fa711__outset-ring--focus-except-active:focus:not(:active){--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}}");
  }
  var focus_module_default2 = { "outset-ring--focus": "af79fb116edb0dd7__outset-ring--focus", "outset-ring--focus-visible": "dfcfdc28396e5d98__outset-ring--focus-visible", "outset-ring--focus-within": "e5cd9ee879f6403a__outset-ring--focus-within", "outset-ring--focus-except-active": "abc777e9713fa711__outset-ring--focus-except-active", "outset-ring--focus-within-except-active": "_3c9f5ee9fc9c136d__outset-ring--focus-within-except-active" };
  if (typeof process === "undefined" || true) {
    registerStyle9("afb8caf1cd", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._2ae7be2fc1bb17a3__input{--_gcd-input-padding:var(--wp-ui-input-padding-block,0px) var(--wp-ui-input-layout-padding-inline,0px);--_gcd-input-font-size:max(var(--wpds-typography-font-size-md,13px),16px);background:transparent;border:none;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);font-family:inherit;font-size:inherit;line-height:inherit;outline:none;padding-block:var(--wp-ui-input-padding-block,0);padding-inline:var(--wp-ui-input-layout-padding-inline,0);width:100%;@media (min-width:600px){--_gcd-input-font-size:inherit}&::placeholder{color:var(--wpds-color-foreground-interactive-neutral-weak,#707070)}&:disabled,&[aria-disabled=true]{--_gcd-input-placeholder-color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);&::placeholder{color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d)}@media (forced-colors:active){color:GrayText}}&[type=email],&[type=url]{direction:ltr}&[type=number]{appearance:textfield;&::-webkit-inner-spin-button,&::-webkit-outer-spin-button{appearance:none;margin:0}}}}@layer compositions{.beecada4b38856ab__layout{font-size:max(var(--wpds-typography-font-size-md,13px),16px);@media (min-width:600px){font-size:var(--wpds-typography-font-size-md,13px)}}}}");
  }
  var style_default8 = { "input": "_2ae7be2fc1bb17a3__input", "layout": "beecada4b38856ab__layout" };
  var Input3 = (0, import_element19.forwardRef)(
    function UnforwardedInput({ className, size: size4 = "default", prefix, suffix, style, ...restProps }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
        InputLayout2,
        {
          className: clsx_default(
            style_default8.layout,
            focus_module_default2["outset-ring--focus-within"],
            className
          ),
          style,
          size: size4,
          visuallyDisabled: restProps.disabled,
          prefix,
          suffix,
          children: /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
            Input,
            {
              ref,
              className: clsx_default(global_css_defense_default4.input, style_default8.input),
              ...restProps
            }
          )
        }
      );
    }
  );

  // packages/ui/build-module/utils/css/item-popup.mjs
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
    registerStyle10("e25d67a230", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._380b81b8f79fb10f__dropdown-motion,._7f344b94e270e039__dropdown-motion--fade-only{--wp-ui-dropdown-slide-distance:4px;--wp-ui-dropdown-slide-duration:var(--wpds-motion-duration-md,200ms);--wp-ui-dropdown-slide-easing:var(--wpds-motion-easing-expressive,cubic-bezier(0.25,0,0,1));--wp-ui-dropdown-fade-duration:var(--wpds-motion-duration-sm,100ms);--wp-ui-dropdown-fade-easing:linear;@media not (prefers-reduced-motion){transition-duration:var(--wp-ui-dropdown-slide-duration),var(--wp-ui-dropdown-fade-duration);transition-property:transform,opacity;transition-timing-function:var(--wp-ui-dropdown-slide-easing),var(--wp-ui-dropdown-fade-easing);will-change:transform,opacity}opacity:1;&[data-instant]{transition:none}&[data-ending-style],&[data-starting-style]{opacity:0}}._380b81b8f79fb10f__dropdown-motion{transform:translate(0);&[data-side=bottom][data-ending-style],&[data-side=bottom][data-starting-style]{transform:translateY(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=top][data-ending-style],&[data-side=top][data-starting-style]{transform:translateY(var(--wp-ui-dropdown-slide-distance))}&[data-side=left][data-ending-style],&[data-side=left][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=right][data-ending-style],&[data-side=right][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=inline-start][data-ending-style],&[data-side=inline-start][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=inline-end][data-ending-style],&[data-side=inline-end][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-start][data-ending-style],&:dir(rtl)[data-side=inline-start][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-end][data-ending-style],&:dir(rtl)[data-side=inline-end][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}}}}@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._234b520016b4e56f__popup{--wp-ui-popup-padding:var(--wpds-dimension-padding-xs,4px);--_wp-ui-elevation-md:0 2px 3px rgba(0,0,0,.05),0 4px 5px rgba(0,0,0,.04),0 12px 12px rgba(0,0,0,.03),0 16px 16px rgba(0,0,0,.02);background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:var(--wpds-border-radius-md,4px);box-shadow:var(--_wp-ui-elevation-md);display:grid;grid-template-areas:"header" "status" "main";grid-template-rows:auto auto minmax(0,1fr);max-height:min(var(--available-height),480px,60dvh);max-width:var(--available-width);min-width:var(--anchor-width);&.b9a9946a395ccad8__is-width-anchor{width:var(--anchor-width)}&._7c9f1b268b013f02__is-width-content{width:auto}&._6f31db51d79ec899__is-width-sm{width:min(var(--available-width),var(--wpds-dimension-surface-width-sm,320px))}&.fa45cdb5f45e57fb__is-width-md{width:min(var(--available-width),var(--wpds-dimension-surface-width-md,400px))}&._46a909337f6be21c__is-width-lg{width:min(var(--available-width),var(--wpds-dimension-surface-width-lg,560px))}&._6083938dff06df34__is-width-available{width:var(--available-width)}}._101852fa256bb935__is-align-item-with-trigger[data-side=none] ._234b520016b4e56f__popup{max-height:none}._2fff4e9defe85de5__list-chrome{--_wp-ui-popup-selection-offset:0px;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);grid-area:main;line-height:var(--wpds-typography-line-height-sm,20px);&:has(.a3adcfd0b73ffd40__item-icon){--_wp-ui-popup-selection-offset:calc(var(--wpds-dimension-size-2xs, 16px) + var(--wpds-dimension-gap-xs, 4px) - var(--wpds-dimension-padding-xs, 4px))}}.f43dc7c768d7b622__list{display:grid;grid-template-areas:"scrollable" "footer";grid-template-rows:minmax(0,1fr) auto}._233cd60cdb84a2ef__list-scrollable-container{grid-area:scrollable;overflow-block:auto;overscroll-behavior:contain;scroll-padding-block:var(--wp-ui-popup-padding);&:not(:empty){padding-block:var(--wp-ui-popup-padding)}}.ec4db6f0122263e7__list-footer{grid-area:footer;padding-block:var(--wp-ui-popup-padding);._233cd60cdb84a2ef__list-scrollable-container:not(:empty)+&{border-block-start:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb)}}.b3c0d7f103fb10a2__group:not(:first-child){margin-block-start:var(--wpds-dimension-gap-sm,8px)}._21b59380477c306c__group-label{align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;min-height:var(--wpds-dimension-size-sm,24px);padding-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));padding-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset))}.be89a1df0fe77bd2__separator{background-color:var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);height:var(--wpds-border-width-xs,1px);margin-block:var(--wpds-dimension-gap-xs,4px);margin-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));margin-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset));@media (forced-colors:active){background-color:CanvasText}}._684ccb7988365b4f__item{--wp-ui-popup-item-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:var(--wpds-dimension-padding-xs,4px);align-items:center;border-radius:var(--wpds-border-radius-sm,2px);display:flex;gap:var(--wpds-dimension-gap-xs,4px);justify-content:flex-start;margin-inline:var(--wp-ui-popup-padding);min-height:var(--wp-ui-popup-item-height);min-width:0;overflow-wrap:anywhere;padding-block:var(--wp-ui-popup-item-padding-block);padding-inline:var(--wp-ui-popup-item-padding-inline);user-select:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}&:has(>.a3adcfd0b73ffd40__item-icon){align-content:center;align-items:start;display:grid;grid-template-columns:auto minmax(0,1fr);padding-inline-start:calc(var(--wp-ui-popup-item-padding-inline) - var(--wpds-dimension-padding-xs, 4px))}&._38f7faff93c61958__is-size-small{--wp-ui-popup-item-height:var(--wpds-dimension-size-sm,24px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:2px}&:not([data-selected]){._92fbe4765dfad5ee__item-indicator-icon{opacity:0}}&[data-highlighted]:not([aria-disabled=true]){background-color:var(--wpds-color-background-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);outline:none;@media (forced-colors:active){--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid Highlight}}&[aria-disabled=true]{background-color:var(--wpds-color-background-interactive-brand-weak-disabled,#0000);color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}}}.a3adcfd0b73ffd40__item-icon{align-items:center;display:flex;height:var(--wpds-typography-line-height-sm,20px);pointer-events:none}._92fbe4765dfad5ee__item-indicator-icon{translate:0 1px}._74465fc7e8ecea1a__item-content{align-items:center;display:flex;gap:var(--wpds-dimension-gap-xs,4px);min-width:0}._41f0dd875c005a48__item-text{display:flex;flex-direction:column;min-width:0}._12335b76ada0b1f5__item-label{min-width:0}._160635254c702623__item-description{color:var(--wpds-color-foreground-content-neutral-weak,#707070)}._684ccb7988365b4f__item[aria-disabled=true] ._160635254c702623__item-description,._684ccb7988365b4f__item[data-highlighted]:not([aria-disabled=true]) ._160635254c702623__item-description{color:inherit}._6eb78bc92f8d7795__status{grid-area:status}._06c7ff39d2f685b9__empty:not(:empty){grid-area:main}._06c7ff39d2f685b9__empty:not(:empty),._6eb78bc92f8d7795__status:not(:empty):not(:has([data-visually-hidden])){--wp-ui-popup-empty-min-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-empty-padding-inline:var(--wpds-dimension-padding-md,12px);align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px);min-height:var(--wp-ui-popup-empty-min-height);padding-inline:var(--wp-ui-popup-empty-padding-inline)}}}');
  }
  var item_popup_default = { "popup": "_234b520016b4e56f__popup _380b81b8f79fb10f__dropdown-motion", "is-width-anchor": "b9a9946a395ccad8__is-width-anchor", "is-width-content": "_7c9f1b268b013f02__is-width-content", "is-width-sm": "_6f31db51d79ec899__is-width-sm", "is-width-md": "fa45cdb5f45e57fb__is-width-md", "is-width-lg": "_46a909337f6be21c__is-width-lg", "is-width-available": "_6083938dff06df34__is-width-available", "is-align-item-with-trigger": "_101852fa256bb935__is-align-item-with-trigger", "list-chrome": "_2fff4e9defe85de5__list-chrome", "item-icon": "a3adcfd0b73ffd40__item-icon", "list": "f43dc7c768d7b622__list _2fff4e9defe85de5__list-chrome", "list-scrollable-container": "_233cd60cdb84a2ef__list-scrollable-container", "list-footer": "ec4db6f0122263e7__list-footer", "group": "b3c0d7f103fb10a2__group", "group-label": "_21b59380477c306c__group-label", "separator": "be89a1df0fe77bd2__separator", "item": "_684ccb7988365b4f__item", "is-size-small": "_38f7faff93c61958__is-size-small", "item-indicator-icon": "_92fbe4765dfad5ee__item-indicator-icon", "item-content": "_74465fc7e8ecea1a__item-content", "item-text": "_41f0dd875c005a48__item-text", "item-label": "_12335b76ada0b1f5__item-label", "item-description": "_160635254c702623__item-description", "status": "_6eb78bc92f8d7795__status", "empty": "_06c7ff39d2f685b9__empty" };
  function getItemPopupWidthClassName(width = "anchor") {
    switch (width) {
      case "available":
        return item_popup_default["is-width-available"];
      case "content":
        return item_popup_default["is-width-content"];
      case "lg":
        return item_popup_default["is-width-lg"];
      case "md":
        return item_popup_default["is-width-md"];
      case "sm":
        return item_popup_default["is-width-sm"];
      case "anchor":
        return item_popup_default["is-width-anchor"];
      default:
        return false;
    }
  }

  // packages/ui/build-module/form/primitives/control-with-error/control-with-error.mjs
  var import_i18n5 = __toESM(require_i18n(), 1);
  var import_element20 = __toESM(require_element(), 1);

  // packages/ui/build-module/form/primitives/validity-indicator/validity-indicator.mjs
  var import_jsx_runtime57 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle11("abb19c3171", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}[role=treeitem] span._400bf7ee0c6061a9__span[aria-hidden]{display:var(--_gcd-span-display,inline);font-family:var(--_gcd-span-font-family,inherit);font-size:var(--_gcd-span-font-size,inherit);line-height:var(--_gcd-span-line-height,1);pointer-events:var(--_gcd-span-pointer-events,auto);position:var(--_gcd-span-position,static)}");
  }
  var global_css_defense_default5 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li", "span": "_400bf7ee0c6061a9__span" };
  if (typeof process === "undefined" || true) {
    registerStyle11("d11816833c", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._7a7ac88e306348e7__indicator{--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);align-items:flex-start;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);gap:var(--wpds-dimension-gap-xs,4px);line-height:var(--wpds-typography-line-height-xs,16px);@media not (prefers-reduced-motion){animation:_48722cc5dd090ce4__indicator-jump var(--wpds-motion-duration-md,.2s) cubic-bezier(.68,-.55,.27,1.55)}}._57a3bbbfbe38a5c6__is-invalid{color:var(--wpds-color-foreground-content-error-weak,#cc1818)}._9e944dc198aac10b__is-valid{color:var(--wpds-color-foreground-content-success-weak,#008030)}.e482806667437c6a__indicator-icon{flex-shrink:0}._6e46434cc23019d2__indicator-spinner{height:var(--wpds-dimension-size-3xs,12px);margin:2px;width:var(--wpds-dimension-size-3xs,12px)}@keyframes _48722cc5dd090ce4__indicator-jump{0%{opacity:0;transform:translateY(-4px)}to{opacity:1;transform:translateY(0)}}}}');
  }
  var style_default9 = { "indicator": "_7a7ac88e306348e7__indicator", "indicator-jump": "_48722cc5dd090ce4__indicator-jump", "is-invalid": "_57a3bbbfbe38a5c6__is-invalid", "is-valid": "_9e944dc198aac10b__is-valid", "indicator-icon": "e482806667437c6a__indicator-icon", "indicator-spinner": "_6e46434cc23019d2__indicator-spinner" };
  var ICON = {
    valid: published_default,
    invalid: error_default
  };
  function ValidityIndicator({
    id,
    type,
    message
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime57.jsxs)(
      "p",
      {
        id,
        className: clsx_default(
          global_css_defense_default5.p,
          style_default9.indicator,
          style_default9[`is-${type}`]
        ),
        children: [
          type === "validating" ? /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(Spinner, { className: style_default9["indicator-spinner"] }) : /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
            Icon,
            {
              className: style_default9["indicator-icon"],
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
  var import_jsx_runtime58 = __toESM(require_jsx_runtime(), 1);
  var DEFAULT_RENDER = (props) => /* @__PURE__ */ (0, import_jsx_runtime58.jsx)(Stack, { ...props, direction: "column", gap: "sm" });
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
    return /* @__PURE__ */ (0, import_jsx_runtime58.jsxs)(import_jsx_runtime58.Fragment, { children: [
      label,
      " ",
      suffix
    ] });
  }
  var VALIDITY_VISIBLE_ATTRIBUTE = "data-validity-visible";
  var ControlWithError = (0, import_element20.forwardRef)(function UnforwardedControlWithError({
    required,
    markWhenOptional,
    customValidity,
    getValidityTarget,
    children,
    render = DEFAULT_RENDER,
    ...restProps
  }, forwardedRef) {
    const [errorMessage, setErrorMessage] = (0, import_element20.useState)();
    const [statusMessage, setStatusMessage] = (0, import_element20.useState)();
    const [showMessage, setShowMessage] = (0, import_element20.useState)(false);
    const [isTouched, setIsTouched] = (0, import_element20.useState)(false);
    const wrapperRef = (0, import_element20.useRef)(null);
    (0, import_element20.useEffect)(() => {
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
    (0, import_element20.useEffect)(() => {
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
    (0, import_element20.useEffect)(() => {
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
    (0, import_element20.useEffect)(() => {
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
    const messageId = (0, import_element20.useId)();
    const message = (() => {
      if (errorMessage) {
        return /* @__PURE__ */ (0, import_jsx_runtime58.jsx)(
          ValidityIndicator,
          {
            id: messageId,
            type: "invalid",
            message: errorMessage
          }
        );
      }
      if (statusMessage?.type) {
        return /* @__PURE__ */ (0, import_jsx_runtime58.jsx)(
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
    (0, import_element20.useEffect)(() => {
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
        children: /* @__PURE__ */ (0, import_jsx_runtime58.jsxs)(import_jsx_runtime58.Fragment, { children: [
          (0, import_element20.cloneElement)(children, {
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
  var import_element21 = __toESM(require_element(), 1);
  var import_jsx_runtime59 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle12("10f3806643", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
  }
  var resets_default3 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
  var DEFAULT_RENDER2 = (props) => /* @__PURE__ */ (0, import_jsx_runtime59.jsx)(Stack, { ...props, direction: "column", gap: "sm" });
  var Root = (0, import_element21.forwardRef)(
    function UnforwardedRoot({ className, render = DEFAULT_RENDER2, ...restProps }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime59.jsx)(
        index_parts_exports.Root,
        {
          ref,
          className: clsx_default(resets_default3["box-sizing"], className),
          render,
          ...restProps
        }
      );
    }
  );

  // packages/ui/build-module/form/primitives/field/item.mjs
  var import_element22 = __toESM(require_element(), 1);
  var import_jsx_runtime60 = __toESM(require_jsx_runtime(), 1);
  var Item = (0, import_element22.forwardRef)(function UnforwardedItem(props, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(index_parts_exports.Item, { ref, ...props });
  });

  // packages/ui/build-module/form/primitives/field/label.mjs
  var import_element23 = __toESM(require_element(), 1);
  var import_jsx_runtime61 = __toESM(require_jsx_runtime(), 1);
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
  var field_default = { "label": "_2d5ad850b2f90964__label", "is-plain": "_17c4214649230bea__is-plain", "description": "_08a3750500e0233f__description" };
  var Label = (0, import_element23.forwardRef)(
    function UnforwardedLabel({ className, hideFromVision, variant, ...restProps }, ref) {
      const label = /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(
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
        return /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(VisuallyHidden, { render: label });
      }
      return label;
    }
  );

  // packages/ui/build-module/form/primitives/field/description.mjs
  var import_element24 = __toESM(require_element(), 1);
  var import_jsx_runtime62 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle14("abb19c3171", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}[role=treeitem] span._400bf7ee0c6061a9__span[aria-hidden]{display:var(--_gcd-span-display,inline);font-family:var(--_gcd-span-font-family,inherit);font-size:var(--_gcd-span-font-size,inherit);line-height:var(--_gcd-span-line-height,1);pointer-events:var(--_gcd-span-pointer-events,auto);position:var(--_gcd-span-position,static)}");
  }
  var global_css_defense_default6 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li", "span": "_400bf7ee0c6061a9__span" };
  if (typeof process === "undefined" || true) {
    registerStyle14("df33c48b2d", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._2d5ad850b2f90964__label{--wp-ui-field-label-line-height:var(--wpds-typography-line-height-xs,16px);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-xs,11px);font-weight:var(--wpds-typography-font-weight-emphasis,600);line-height:var(--wp-ui-field-label-line-height);text-transform:uppercase;&._17c4214649230bea__is-plain{font-size:var(--wpds-typography-font-size-md,13px);font-weight:var(--wpds-typography-font-weight-default,400);text-transform:none}}._08a3750500e0233f__description{--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);--_gcd-p-margin:0;text-wrap:pretty;color:var(--wpds-color-foreground-content-neutral-weak,#707070);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);line-height:var(--wpds-typography-line-height-xs,16px)}}}');
  }
  var field_default2 = { "label": "_2d5ad850b2f90964__label", "is-plain": "_17c4214649230bea__is-plain", "description": "_08a3750500e0233f__description" };
  var Description = (0, import_element24.forwardRef)(function UnforwardedDescription({ className, ...restProps }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(
      index_parts_exports.Description,
      {
        ref,
        className: clsx_default(
          global_css_defense_default6.p,
          field_default2.description,
          className
        ),
        ...restProps
      }
    );
  });

  // packages/ui/build-module/form/primitives/field/details.mjs
  var import_element25 = __toESM(require_element(), 1);
  var import_i18n6 = __toESM(require_i18n(), 1);
  var import_jsx_runtime63 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle15("df33c48b2d", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._2d5ad850b2f90964__label{--wp-ui-field-label-line-height:var(--wpds-typography-line-height-xs,16px);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-xs,11px);font-weight:var(--wpds-typography-font-weight-emphasis,600);line-height:var(--wp-ui-field-label-line-height);text-transform:uppercase;&._17c4214649230bea__is-plain{font-size:var(--wpds-typography-font-size-md,13px);font-weight:var(--wpds-typography-font-weight-default,400);text-transform:none}}._08a3750500e0233f__description{--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);--_gcd-p-margin:0;text-wrap:pretty;color:var(--wpds-color-foreground-content-neutral-weak,#707070);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);line-height:var(--wpds-typography-line-height-xs,16px)}}}');
  }
  var field_default3 = { "label": "_2d5ad850b2f90964__label", "is-plain": "_17c4214649230bea__is-plain", "description": "_08a3750500e0233f__description" };
  var Details = (0, import_element25.forwardRef)(
    function UnforwardedDetails({ className, ...restProps }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime63.jsxs)(import_jsx_runtime63.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(VisuallyHidden, { render: /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(index_parts_exports.Description, {}), children: (0, import_i18n6.__)("More details follow the field.") }),
        /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(
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
  var import_element26 = __toESM(require_element(), 1);
  var import_jsx_runtime64 = __toESM(require_jsx_runtime(), 1);
  var Control = (0, import_element26.forwardRef)(
    function UnforwardedControl(props, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime64.jsx)(index_parts_exports.Control, { ref, ...props });
    }
  );

  // packages/ui/build-module/form/primitives/field/visual-label.mjs
  var import_element27 = __toESM(require_element(), 1);
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
    registerStyle16("df33c48b2d", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._2d5ad850b2f90964__label{--wp-ui-field-label-line-height:var(--wpds-typography-line-height-xs,16px);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-xs,11px);font-weight:var(--wpds-typography-font-weight-emphasis,600);line-height:var(--wp-ui-field-label-line-height);text-transform:uppercase;&._17c4214649230bea__is-plain{font-size:var(--wpds-typography-font-size-md,13px);font-weight:var(--wpds-typography-font-weight-default,400);text-transform:none}}._08a3750500e0233f__description{--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);--_gcd-p-margin:0;text-wrap:pretty;color:var(--wpds-color-foreground-content-neutral-weak,#707070);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);line-height:var(--wpds-typography-line-height-xs,16px)}}}');
  }
  var field_default4 = { "label": "_2d5ad850b2f90964__label", "is-plain": "_17c4214649230bea__is-plain", "description": "_08a3750500e0233f__description" };
  var VisualLabel = (0, import_element27.forwardRef)(
    function UnforwardedVisualLabel({ className, render, variant, ...restProps }, ref) {
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

  // packages/ui/build-module/form/primitives/select/index.mjs
  var select_exports = {};
  __export(select_exports, {
    Group: () => Group,
    GroupLabel: () => GroupLabel,
    Item: () => Item2,
    ItemDescription: () => ItemDescription,
    ItemLabel: () => ItemLabel,
    Popup: () => Popup,
    Portal: () => Portal,
    Positioner: () => Positioner,
    Root: () => Root2,
    Separator: () => Separator,
    Trigger: () => Trigger
  });

  // packages/ui/build-module/form/primitives/select/group.mjs
  var import_element28 = __toESM(require_element(), 1);
  var import_jsx_runtime65 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle17("e25d67a230", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._380b81b8f79fb10f__dropdown-motion,._7f344b94e270e039__dropdown-motion--fade-only{--wp-ui-dropdown-slide-distance:4px;--wp-ui-dropdown-slide-duration:var(--wpds-motion-duration-md,200ms);--wp-ui-dropdown-slide-easing:var(--wpds-motion-easing-expressive,cubic-bezier(0.25,0,0,1));--wp-ui-dropdown-fade-duration:var(--wpds-motion-duration-sm,100ms);--wp-ui-dropdown-fade-easing:linear;@media not (prefers-reduced-motion){transition-duration:var(--wp-ui-dropdown-slide-duration),var(--wp-ui-dropdown-fade-duration);transition-property:transform,opacity;transition-timing-function:var(--wp-ui-dropdown-slide-easing),var(--wp-ui-dropdown-fade-easing);will-change:transform,opacity}opacity:1;&[data-instant]{transition:none}&[data-ending-style],&[data-starting-style]{opacity:0}}._380b81b8f79fb10f__dropdown-motion{transform:translate(0);&[data-side=bottom][data-ending-style],&[data-side=bottom][data-starting-style]{transform:translateY(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=top][data-ending-style],&[data-side=top][data-starting-style]{transform:translateY(var(--wp-ui-dropdown-slide-distance))}&[data-side=left][data-ending-style],&[data-side=left][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=right][data-ending-style],&[data-side=right][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=inline-start][data-ending-style],&[data-side=inline-start][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=inline-end][data-ending-style],&[data-side=inline-end][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-start][data-ending-style],&:dir(rtl)[data-side=inline-start][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-end][data-ending-style],&:dir(rtl)[data-side=inline-end][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}}}}@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._234b520016b4e56f__popup{--wp-ui-popup-padding:var(--wpds-dimension-padding-xs,4px);--_wp-ui-elevation-md:0 2px 3px rgba(0,0,0,.05),0 4px 5px rgba(0,0,0,.04),0 12px 12px rgba(0,0,0,.03),0 16px 16px rgba(0,0,0,.02);background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:var(--wpds-border-radius-md,4px);box-shadow:var(--_wp-ui-elevation-md);display:grid;grid-template-areas:"header" "status" "main";grid-template-rows:auto auto minmax(0,1fr);max-height:min(var(--available-height),480px,60dvh);max-width:var(--available-width);min-width:var(--anchor-width);&.b9a9946a395ccad8__is-width-anchor{width:var(--anchor-width)}&._7c9f1b268b013f02__is-width-content{width:auto}&._6f31db51d79ec899__is-width-sm{width:min(var(--available-width),var(--wpds-dimension-surface-width-sm,320px))}&.fa45cdb5f45e57fb__is-width-md{width:min(var(--available-width),var(--wpds-dimension-surface-width-md,400px))}&._46a909337f6be21c__is-width-lg{width:min(var(--available-width),var(--wpds-dimension-surface-width-lg,560px))}&._6083938dff06df34__is-width-available{width:var(--available-width)}}._101852fa256bb935__is-align-item-with-trigger[data-side=none] ._234b520016b4e56f__popup{max-height:none}._2fff4e9defe85de5__list-chrome{--_wp-ui-popup-selection-offset:0px;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);grid-area:main;line-height:var(--wpds-typography-line-height-sm,20px);&:has(.a3adcfd0b73ffd40__item-icon){--_wp-ui-popup-selection-offset:calc(var(--wpds-dimension-size-2xs, 16px) + var(--wpds-dimension-gap-xs, 4px) - var(--wpds-dimension-padding-xs, 4px))}}.f43dc7c768d7b622__list{display:grid;grid-template-areas:"scrollable" "footer";grid-template-rows:minmax(0,1fr) auto}._233cd60cdb84a2ef__list-scrollable-container{grid-area:scrollable;overflow-block:auto;overscroll-behavior:contain;scroll-padding-block:var(--wp-ui-popup-padding);&:not(:empty){padding-block:var(--wp-ui-popup-padding)}}.ec4db6f0122263e7__list-footer{grid-area:footer;padding-block:var(--wp-ui-popup-padding);._233cd60cdb84a2ef__list-scrollable-container:not(:empty)+&{border-block-start:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb)}}.b3c0d7f103fb10a2__group:not(:first-child){margin-block-start:var(--wpds-dimension-gap-sm,8px)}._21b59380477c306c__group-label{align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;min-height:var(--wpds-dimension-size-sm,24px);padding-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));padding-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset))}.be89a1df0fe77bd2__separator{background-color:var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);height:var(--wpds-border-width-xs,1px);margin-block:var(--wpds-dimension-gap-xs,4px);margin-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));margin-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset));@media (forced-colors:active){background-color:CanvasText}}._684ccb7988365b4f__item{--wp-ui-popup-item-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:var(--wpds-dimension-padding-xs,4px);align-items:center;border-radius:var(--wpds-border-radius-sm,2px);display:flex;gap:var(--wpds-dimension-gap-xs,4px);justify-content:flex-start;margin-inline:var(--wp-ui-popup-padding);min-height:var(--wp-ui-popup-item-height);min-width:0;overflow-wrap:anywhere;padding-block:var(--wp-ui-popup-item-padding-block);padding-inline:var(--wp-ui-popup-item-padding-inline);user-select:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}&:has(>.a3adcfd0b73ffd40__item-icon){align-content:center;align-items:start;display:grid;grid-template-columns:auto minmax(0,1fr);padding-inline-start:calc(var(--wp-ui-popup-item-padding-inline) - var(--wpds-dimension-padding-xs, 4px))}&._38f7faff93c61958__is-size-small{--wp-ui-popup-item-height:var(--wpds-dimension-size-sm,24px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:2px}&:not([data-selected]){._92fbe4765dfad5ee__item-indicator-icon{opacity:0}}&[data-highlighted]:not([aria-disabled=true]){background-color:var(--wpds-color-background-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);outline:none;@media (forced-colors:active){--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid Highlight}}&[aria-disabled=true]{background-color:var(--wpds-color-background-interactive-brand-weak-disabled,#0000);color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}}}.a3adcfd0b73ffd40__item-icon{align-items:center;display:flex;height:var(--wpds-typography-line-height-sm,20px);pointer-events:none}._92fbe4765dfad5ee__item-indicator-icon{translate:0 1px}._74465fc7e8ecea1a__item-content{align-items:center;display:flex;gap:var(--wpds-dimension-gap-xs,4px);min-width:0}._41f0dd875c005a48__item-text{display:flex;flex-direction:column;min-width:0}._12335b76ada0b1f5__item-label{min-width:0}._160635254c702623__item-description{color:var(--wpds-color-foreground-content-neutral-weak,#707070)}._684ccb7988365b4f__item[aria-disabled=true] ._160635254c702623__item-description,._684ccb7988365b4f__item[data-highlighted]:not([aria-disabled=true]) ._160635254c702623__item-description{color:inherit}._6eb78bc92f8d7795__status{grid-area:status}._06c7ff39d2f685b9__empty:not(:empty){grid-area:main}._06c7ff39d2f685b9__empty:not(:empty),._6eb78bc92f8d7795__status:not(:empty):not(:has([data-visually-hidden])){--wp-ui-popup-empty-min-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-empty-padding-inline:var(--wpds-dimension-padding-md,12px);align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px);min-height:var(--wp-ui-popup-empty-min-height);padding-inline:var(--wp-ui-popup-empty-padding-inline)}}}');
  }
  var item_popup_default2 = { "popup": "_234b520016b4e56f__popup _380b81b8f79fb10f__dropdown-motion", "is-width-anchor": "b9a9946a395ccad8__is-width-anchor", "is-width-content": "_7c9f1b268b013f02__is-width-content", "is-width-sm": "_6f31db51d79ec899__is-width-sm", "is-width-md": "fa45cdb5f45e57fb__is-width-md", "is-width-lg": "_46a909337f6be21c__is-width-lg", "is-width-available": "_6083938dff06df34__is-width-available", "is-align-item-with-trigger": "_101852fa256bb935__is-align-item-with-trigger", "list-chrome": "_2fff4e9defe85de5__list-chrome", "item-icon": "a3adcfd0b73ffd40__item-icon", "list": "f43dc7c768d7b622__list _2fff4e9defe85de5__list-chrome", "list-scrollable-container": "_233cd60cdb84a2ef__list-scrollable-container", "list-footer": "ec4db6f0122263e7__list-footer", "group": "b3c0d7f103fb10a2__group", "group-label": "_21b59380477c306c__group-label", "separator": "be89a1df0fe77bd2__separator", "item": "_684ccb7988365b4f__item", "is-size-small": "_38f7faff93c61958__is-size-small", "item-indicator-icon": "_92fbe4765dfad5ee__item-indicator-icon", "item-content": "_74465fc7e8ecea1a__item-content", "item-text": "_41f0dd875c005a48__item-text", "item-label": "_12335b76ada0b1f5__item-label", "item-description": "_160635254c702623__item-description", "status": "_6eb78bc92f8d7795__status", "empty": "_06c7ff39d2f685b9__empty" };
  var Group = (0, import_element28.forwardRef)(
    function UnforwardedGroup({ className, children, ...restProps }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime65.jsx)(
        index_parts_exports2.Group,
        {
          className: clsx_default(item_popup_default2.group, className),
          ref,
          ...restProps,
          children
        }
      );
    }
  );

  // packages/ui/build-module/form/primitives/select/group-label.mjs
  var import_element29 = __toESM(require_element(), 1);
  var import_jsx_runtime66 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE18 = "data-wp-hash";
  function getRuntime18() {
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
      registerDocument18(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash18(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE18}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE18) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle18(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime18();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash18(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE18, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument18(targetDocument) {
    const runtime = getRuntime18();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle18(targetDocument, hash, css);
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
  function registerStyle18(hash, css) {
    const runtime = getRuntime18();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle18(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle18("e25d67a230", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._380b81b8f79fb10f__dropdown-motion,._7f344b94e270e039__dropdown-motion--fade-only{--wp-ui-dropdown-slide-distance:4px;--wp-ui-dropdown-slide-duration:var(--wpds-motion-duration-md,200ms);--wp-ui-dropdown-slide-easing:var(--wpds-motion-easing-expressive,cubic-bezier(0.25,0,0,1));--wp-ui-dropdown-fade-duration:var(--wpds-motion-duration-sm,100ms);--wp-ui-dropdown-fade-easing:linear;@media not (prefers-reduced-motion){transition-duration:var(--wp-ui-dropdown-slide-duration),var(--wp-ui-dropdown-fade-duration);transition-property:transform,opacity;transition-timing-function:var(--wp-ui-dropdown-slide-easing),var(--wp-ui-dropdown-fade-easing);will-change:transform,opacity}opacity:1;&[data-instant]{transition:none}&[data-ending-style],&[data-starting-style]{opacity:0}}._380b81b8f79fb10f__dropdown-motion{transform:translate(0);&[data-side=bottom][data-ending-style],&[data-side=bottom][data-starting-style]{transform:translateY(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=top][data-ending-style],&[data-side=top][data-starting-style]{transform:translateY(var(--wp-ui-dropdown-slide-distance))}&[data-side=left][data-ending-style],&[data-side=left][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=right][data-ending-style],&[data-side=right][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=inline-start][data-ending-style],&[data-side=inline-start][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=inline-end][data-ending-style],&[data-side=inline-end][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-start][data-ending-style],&:dir(rtl)[data-side=inline-start][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-end][data-ending-style],&:dir(rtl)[data-side=inline-end][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}}}}@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._234b520016b4e56f__popup{--wp-ui-popup-padding:var(--wpds-dimension-padding-xs,4px);--_wp-ui-elevation-md:0 2px 3px rgba(0,0,0,.05),0 4px 5px rgba(0,0,0,.04),0 12px 12px rgba(0,0,0,.03),0 16px 16px rgba(0,0,0,.02);background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:var(--wpds-border-radius-md,4px);box-shadow:var(--_wp-ui-elevation-md);display:grid;grid-template-areas:"header" "status" "main";grid-template-rows:auto auto minmax(0,1fr);max-height:min(var(--available-height),480px,60dvh);max-width:var(--available-width);min-width:var(--anchor-width);&.b9a9946a395ccad8__is-width-anchor{width:var(--anchor-width)}&._7c9f1b268b013f02__is-width-content{width:auto}&._6f31db51d79ec899__is-width-sm{width:min(var(--available-width),var(--wpds-dimension-surface-width-sm,320px))}&.fa45cdb5f45e57fb__is-width-md{width:min(var(--available-width),var(--wpds-dimension-surface-width-md,400px))}&._46a909337f6be21c__is-width-lg{width:min(var(--available-width),var(--wpds-dimension-surface-width-lg,560px))}&._6083938dff06df34__is-width-available{width:var(--available-width)}}._101852fa256bb935__is-align-item-with-trigger[data-side=none] ._234b520016b4e56f__popup{max-height:none}._2fff4e9defe85de5__list-chrome{--_wp-ui-popup-selection-offset:0px;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);grid-area:main;line-height:var(--wpds-typography-line-height-sm,20px);&:has(.a3adcfd0b73ffd40__item-icon){--_wp-ui-popup-selection-offset:calc(var(--wpds-dimension-size-2xs, 16px) + var(--wpds-dimension-gap-xs, 4px) - var(--wpds-dimension-padding-xs, 4px))}}.f43dc7c768d7b622__list{display:grid;grid-template-areas:"scrollable" "footer";grid-template-rows:minmax(0,1fr) auto}._233cd60cdb84a2ef__list-scrollable-container{grid-area:scrollable;overflow-block:auto;overscroll-behavior:contain;scroll-padding-block:var(--wp-ui-popup-padding);&:not(:empty){padding-block:var(--wp-ui-popup-padding)}}.ec4db6f0122263e7__list-footer{grid-area:footer;padding-block:var(--wp-ui-popup-padding);._233cd60cdb84a2ef__list-scrollable-container:not(:empty)+&{border-block-start:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb)}}.b3c0d7f103fb10a2__group:not(:first-child){margin-block-start:var(--wpds-dimension-gap-sm,8px)}._21b59380477c306c__group-label{align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;min-height:var(--wpds-dimension-size-sm,24px);padding-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));padding-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset))}.be89a1df0fe77bd2__separator{background-color:var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);height:var(--wpds-border-width-xs,1px);margin-block:var(--wpds-dimension-gap-xs,4px);margin-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));margin-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset));@media (forced-colors:active){background-color:CanvasText}}._684ccb7988365b4f__item{--wp-ui-popup-item-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:var(--wpds-dimension-padding-xs,4px);align-items:center;border-radius:var(--wpds-border-radius-sm,2px);display:flex;gap:var(--wpds-dimension-gap-xs,4px);justify-content:flex-start;margin-inline:var(--wp-ui-popup-padding);min-height:var(--wp-ui-popup-item-height);min-width:0;overflow-wrap:anywhere;padding-block:var(--wp-ui-popup-item-padding-block);padding-inline:var(--wp-ui-popup-item-padding-inline);user-select:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}&:has(>.a3adcfd0b73ffd40__item-icon){align-content:center;align-items:start;display:grid;grid-template-columns:auto minmax(0,1fr);padding-inline-start:calc(var(--wp-ui-popup-item-padding-inline) - var(--wpds-dimension-padding-xs, 4px))}&._38f7faff93c61958__is-size-small{--wp-ui-popup-item-height:var(--wpds-dimension-size-sm,24px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:2px}&:not([data-selected]){._92fbe4765dfad5ee__item-indicator-icon{opacity:0}}&[data-highlighted]:not([aria-disabled=true]){background-color:var(--wpds-color-background-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);outline:none;@media (forced-colors:active){--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid Highlight}}&[aria-disabled=true]{background-color:var(--wpds-color-background-interactive-brand-weak-disabled,#0000);color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}}}.a3adcfd0b73ffd40__item-icon{align-items:center;display:flex;height:var(--wpds-typography-line-height-sm,20px);pointer-events:none}._92fbe4765dfad5ee__item-indicator-icon{translate:0 1px}._74465fc7e8ecea1a__item-content{align-items:center;display:flex;gap:var(--wpds-dimension-gap-xs,4px);min-width:0}._41f0dd875c005a48__item-text{display:flex;flex-direction:column;min-width:0}._12335b76ada0b1f5__item-label{min-width:0}._160635254c702623__item-description{color:var(--wpds-color-foreground-content-neutral-weak,#707070)}._684ccb7988365b4f__item[aria-disabled=true] ._160635254c702623__item-description,._684ccb7988365b4f__item[data-highlighted]:not([aria-disabled=true]) ._160635254c702623__item-description{color:inherit}._6eb78bc92f8d7795__status{grid-area:status}._06c7ff39d2f685b9__empty:not(:empty){grid-area:main}._06c7ff39d2f685b9__empty:not(:empty),._6eb78bc92f8d7795__status:not(:empty):not(:has([data-visually-hidden])){--wp-ui-popup-empty-min-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-empty-padding-inline:var(--wpds-dimension-padding-md,12px);align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px);min-height:var(--wp-ui-popup-empty-min-height);padding-inline:var(--wp-ui-popup-empty-padding-inline)}}}');
  }
  var item_popup_default3 = { "popup": "_234b520016b4e56f__popup _380b81b8f79fb10f__dropdown-motion", "is-width-anchor": "b9a9946a395ccad8__is-width-anchor", "is-width-content": "_7c9f1b268b013f02__is-width-content", "is-width-sm": "_6f31db51d79ec899__is-width-sm", "is-width-md": "fa45cdb5f45e57fb__is-width-md", "is-width-lg": "_46a909337f6be21c__is-width-lg", "is-width-available": "_6083938dff06df34__is-width-available", "is-align-item-with-trigger": "_101852fa256bb935__is-align-item-with-trigger", "list-chrome": "_2fff4e9defe85de5__list-chrome", "item-icon": "a3adcfd0b73ffd40__item-icon", "list": "f43dc7c768d7b622__list _2fff4e9defe85de5__list-chrome", "list-scrollable-container": "_233cd60cdb84a2ef__list-scrollable-container", "list-footer": "ec4db6f0122263e7__list-footer", "group": "b3c0d7f103fb10a2__group", "group-label": "_21b59380477c306c__group-label", "separator": "be89a1df0fe77bd2__separator", "item": "_684ccb7988365b4f__item", "is-size-small": "_38f7faff93c61958__is-size-small", "item-indicator-icon": "_92fbe4765dfad5ee__item-indicator-icon", "item-content": "_74465fc7e8ecea1a__item-content", "item-text": "_41f0dd875c005a48__item-text", "item-label": "_12335b76ada0b1f5__item-label", "item-description": "_160635254c702623__item-description", "status": "_6eb78bc92f8d7795__status", "empty": "_06c7ff39d2f685b9__empty" };
  var GroupLabel = (0, import_element29.forwardRef)(
    function UnforwardedGroupLabel({ className, children, ...restProps }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime66.jsx)(
        Text,
        {
          variant: "heading-sm",
          className: clsx_default(
            item_popup_default3["group-label"],
            className
          ),
          render: /* @__PURE__ */ (0, import_jsx_runtime66.jsx)(index_parts_exports2.GroupLabel, { ref, ...restProps }),
          children
        }
      );
    }
  );

  // packages/ui/build-module/form/primitives/select/item.mjs
  var import_element32 = __toESM(require_element(), 1);

  // packages/ui/build-module/form/primitives/select/item-description.mjs
  var import_element30 = __toESM(require_element(), 1);
  var import_jsx_runtime67 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE19 = "data-wp-hash";
  function getRuntime19() {
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
      registerDocument19(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash19(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE19}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE19) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle19(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime19();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash19(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE19, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument19(targetDocument) {
    const runtime = getRuntime19();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle19(targetDocument, hash, css);
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
  function registerStyle19(hash, css) {
    const runtime = getRuntime19();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle19(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle19("e25d67a230", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._380b81b8f79fb10f__dropdown-motion,._7f344b94e270e039__dropdown-motion--fade-only{--wp-ui-dropdown-slide-distance:4px;--wp-ui-dropdown-slide-duration:var(--wpds-motion-duration-md,200ms);--wp-ui-dropdown-slide-easing:var(--wpds-motion-easing-expressive,cubic-bezier(0.25,0,0,1));--wp-ui-dropdown-fade-duration:var(--wpds-motion-duration-sm,100ms);--wp-ui-dropdown-fade-easing:linear;@media not (prefers-reduced-motion){transition-duration:var(--wp-ui-dropdown-slide-duration),var(--wp-ui-dropdown-fade-duration);transition-property:transform,opacity;transition-timing-function:var(--wp-ui-dropdown-slide-easing),var(--wp-ui-dropdown-fade-easing);will-change:transform,opacity}opacity:1;&[data-instant]{transition:none}&[data-ending-style],&[data-starting-style]{opacity:0}}._380b81b8f79fb10f__dropdown-motion{transform:translate(0);&[data-side=bottom][data-ending-style],&[data-side=bottom][data-starting-style]{transform:translateY(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=top][data-ending-style],&[data-side=top][data-starting-style]{transform:translateY(var(--wp-ui-dropdown-slide-distance))}&[data-side=left][data-ending-style],&[data-side=left][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=right][data-ending-style],&[data-side=right][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=inline-start][data-ending-style],&[data-side=inline-start][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=inline-end][data-ending-style],&[data-side=inline-end][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-start][data-ending-style],&:dir(rtl)[data-side=inline-start][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-end][data-ending-style],&:dir(rtl)[data-side=inline-end][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}}}}@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._234b520016b4e56f__popup{--wp-ui-popup-padding:var(--wpds-dimension-padding-xs,4px);--_wp-ui-elevation-md:0 2px 3px rgba(0,0,0,.05),0 4px 5px rgba(0,0,0,.04),0 12px 12px rgba(0,0,0,.03),0 16px 16px rgba(0,0,0,.02);background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:var(--wpds-border-radius-md,4px);box-shadow:var(--_wp-ui-elevation-md);display:grid;grid-template-areas:"header" "status" "main";grid-template-rows:auto auto minmax(0,1fr);max-height:min(var(--available-height),480px,60dvh);max-width:var(--available-width);min-width:var(--anchor-width);&.b9a9946a395ccad8__is-width-anchor{width:var(--anchor-width)}&._7c9f1b268b013f02__is-width-content{width:auto}&._6f31db51d79ec899__is-width-sm{width:min(var(--available-width),var(--wpds-dimension-surface-width-sm,320px))}&.fa45cdb5f45e57fb__is-width-md{width:min(var(--available-width),var(--wpds-dimension-surface-width-md,400px))}&._46a909337f6be21c__is-width-lg{width:min(var(--available-width),var(--wpds-dimension-surface-width-lg,560px))}&._6083938dff06df34__is-width-available{width:var(--available-width)}}._101852fa256bb935__is-align-item-with-trigger[data-side=none] ._234b520016b4e56f__popup{max-height:none}._2fff4e9defe85de5__list-chrome{--_wp-ui-popup-selection-offset:0px;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);grid-area:main;line-height:var(--wpds-typography-line-height-sm,20px);&:has(.a3adcfd0b73ffd40__item-icon){--_wp-ui-popup-selection-offset:calc(var(--wpds-dimension-size-2xs, 16px) + var(--wpds-dimension-gap-xs, 4px) - var(--wpds-dimension-padding-xs, 4px))}}.f43dc7c768d7b622__list{display:grid;grid-template-areas:"scrollable" "footer";grid-template-rows:minmax(0,1fr) auto}._233cd60cdb84a2ef__list-scrollable-container{grid-area:scrollable;overflow-block:auto;overscroll-behavior:contain;scroll-padding-block:var(--wp-ui-popup-padding);&:not(:empty){padding-block:var(--wp-ui-popup-padding)}}.ec4db6f0122263e7__list-footer{grid-area:footer;padding-block:var(--wp-ui-popup-padding);._233cd60cdb84a2ef__list-scrollable-container:not(:empty)+&{border-block-start:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb)}}.b3c0d7f103fb10a2__group:not(:first-child){margin-block-start:var(--wpds-dimension-gap-sm,8px)}._21b59380477c306c__group-label{align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;min-height:var(--wpds-dimension-size-sm,24px);padding-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));padding-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset))}.be89a1df0fe77bd2__separator{background-color:var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);height:var(--wpds-border-width-xs,1px);margin-block:var(--wpds-dimension-gap-xs,4px);margin-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));margin-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset));@media (forced-colors:active){background-color:CanvasText}}._684ccb7988365b4f__item{--wp-ui-popup-item-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:var(--wpds-dimension-padding-xs,4px);align-items:center;border-radius:var(--wpds-border-radius-sm,2px);display:flex;gap:var(--wpds-dimension-gap-xs,4px);justify-content:flex-start;margin-inline:var(--wp-ui-popup-padding);min-height:var(--wp-ui-popup-item-height);min-width:0;overflow-wrap:anywhere;padding-block:var(--wp-ui-popup-item-padding-block);padding-inline:var(--wp-ui-popup-item-padding-inline);user-select:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}&:has(>.a3adcfd0b73ffd40__item-icon){align-content:center;align-items:start;display:grid;grid-template-columns:auto minmax(0,1fr);padding-inline-start:calc(var(--wp-ui-popup-item-padding-inline) - var(--wpds-dimension-padding-xs, 4px))}&._38f7faff93c61958__is-size-small{--wp-ui-popup-item-height:var(--wpds-dimension-size-sm,24px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:2px}&:not([data-selected]){._92fbe4765dfad5ee__item-indicator-icon{opacity:0}}&[data-highlighted]:not([aria-disabled=true]){background-color:var(--wpds-color-background-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);outline:none;@media (forced-colors:active){--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid Highlight}}&[aria-disabled=true]{background-color:var(--wpds-color-background-interactive-brand-weak-disabled,#0000);color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}}}.a3adcfd0b73ffd40__item-icon{align-items:center;display:flex;height:var(--wpds-typography-line-height-sm,20px);pointer-events:none}._92fbe4765dfad5ee__item-indicator-icon{translate:0 1px}._74465fc7e8ecea1a__item-content{align-items:center;display:flex;gap:var(--wpds-dimension-gap-xs,4px);min-width:0}._41f0dd875c005a48__item-text{display:flex;flex-direction:column;min-width:0}._12335b76ada0b1f5__item-label{min-width:0}._160635254c702623__item-description{color:var(--wpds-color-foreground-content-neutral-weak,#707070)}._684ccb7988365b4f__item[aria-disabled=true] ._160635254c702623__item-description,._684ccb7988365b4f__item[data-highlighted]:not([aria-disabled=true]) ._160635254c702623__item-description{color:inherit}._6eb78bc92f8d7795__status{grid-area:status}._06c7ff39d2f685b9__empty:not(:empty){grid-area:main}._06c7ff39d2f685b9__empty:not(:empty),._6eb78bc92f8d7795__status:not(:empty):not(:has([data-visually-hidden])){--wp-ui-popup-empty-min-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-empty-padding-inline:var(--wpds-dimension-padding-md,12px);align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px);min-height:var(--wp-ui-popup-empty-min-height);padding-inline:var(--wp-ui-popup-empty-padding-inline)}}}');
  }
  var item_popup_default4 = { "popup": "_234b520016b4e56f__popup _380b81b8f79fb10f__dropdown-motion", "is-width-anchor": "b9a9946a395ccad8__is-width-anchor", "is-width-content": "_7c9f1b268b013f02__is-width-content", "is-width-sm": "_6f31db51d79ec899__is-width-sm", "is-width-md": "fa45cdb5f45e57fb__is-width-md", "is-width-lg": "_46a909337f6be21c__is-width-lg", "is-width-available": "_6083938dff06df34__is-width-available", "is-align-item-with-trigger": "_101852fa256bb935__is-align-item-with-trigger", "list-chrome": "_2fff4e9defe85de5__list-chrome", "item-icon": "a3adcfd0b73ffd40__item-icon", "list": "f43dc7c768d7b622__list _2fff4e9defe85de5__list-chrome", "list-scrollable-container": "_233cd60cdb84a2ef__list-scrollable-container", "list-footer": "ec4db6f0122263e7__list-footer", "group": "b3c0d7f103fb10a2__group", "group-label": "_21b59380477c306c__group-label", "separator": "be89a1df0fe77bd2__separator", "item": "_684ccb7988365b4f__item", "is-size-small": "_38f7faff93c61958__is-size-small", "item-indicator-icon": "_92fbe4765dfad5ee__item-indicator-icon", "item-content": "_74465fc7e8ecea1a__item-content", "item-text": "_41f0dd875c005a48__item-text", "item-label": "_12335b76ada0b1f5__item-label", "item-description": "_160635254c702623__item-description", "status": "_6eb78bc92f8d7795__status", "empty": "_06c7ff39d2f685b9__empty" };
  var ITEM_DESCRIPTION_DIRECT_CHILD = /* @__PURE__ */ Symbol();
  var ItemDescription = (0, import_element30.forwardRef)(function UnforwardedItemDescription(props, ref) {
    const { className, validationToken, ...restProps } = props;
    if (validationToken !== ITEM_DESCRIPTION_DIRECT_CHILD) {
      throw new Error(
        "Select.ItemDescription: Missing direct select item parent. Render <Select.ItemDescription> as a direct child of <Select.Item>."
      );
    }
    return /* @__PURE__ */ (0, import_jsx_runtime67.jsx)(
      Text,
      {
        ref,
        variant: "body-sm",
        className: clsx_default(
          item_popup_default4["item-description"],
          className
        ),
        ...restProps
      }
    );
  });

  // packages/ui/build-module/form/primitives/select/item-label.mjs
  var import_element31 = __toESM(require_element(), 1);
  var import_jsx_runtime68 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE20 = "data-wp-hash";
  function getRuntime20() {
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
      registerDocument20(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash20(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE20}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE20) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle20(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime20();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash20(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE20, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument20(targetDocument) {
    const runtime = getRuntime20();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle20(targetDocument, hash, css);
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
  function registerStyle20(hash, css) {
    const runtime = getRuntime20();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle20(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle20("e25d67a230", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._380b81b8f79fb10f__dropdown-motion,._7f344b94e270e039__dropdown-motion--fade-only{--wp-ui-dropdown-slide-distance:4px;--wp-ui-dropdown-slide-duration:var(--wpds-motion-duration-md,200ms);--wp-ui-dropdown-slide-easing:var(--wpds-motion-easing-expressive,cubic-bezier(0.25,0,0,1));--wp-ui-dropdown-fade-duration:var(--wpds-motion-duration-sm,100ms);--wp-ui-dropdown-fade-easing:linear;@media not (prefers-reduced-motion){transition-duration:var(--wp-ui-dropdown-slide-duration),var(--wp-ui-dropdown-fade-duration);transition-property:transform,opacity;transition-timing-function:var(--wp-ui-dropdown-slide-easing),var(--wp-ui-dropdown-fade-easing);will-change:transform,opacity}opacity:1;&[data-instant]{transition:none}&[data-ending-style],&[data-starting-style]{opacity:0}}._380b81b8f79fb10f__dropdown-motion{transform:translate(0);&[data-side=bottom][data-ending-style],&[data-side=bottom][data-starting-style]{transform:translateY(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=top][data-ending-style],&[data-side=top][data-starting-style]{transform:translateY(var(--wp-ui-dropdown-slide-distance))}&[data-side=left][data-ending-style],&[data-side=left][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=right][data-ending-style],&[data-side=right][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=inline-start][data-ending-style],&[data-side=inline-start][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=inline-end][data-ending-style],&[data-side=inline-end][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-start][data-ending-style],&:dir(rtl)[data-side=inline-start][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-end][data-ending-style],&:dir(rtl)[data-side=inline-end][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}}}}@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._234b520016b4e56f__popup{--wp-ui-popup-padding:var(--wpds-dimension-padding-xs,4px);--_wp-ui-elevation-md:0 2px 3px rgba(0,0,0,.05),0 4px 5px rgba(0,0,0,.04),0 12px 12px rgba(0,0,0,.03),0 16px 16px rgba(0,0,0,.02);background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:var(--wpds-border-radius-md,4px);box-shadow:var(--_wp-ui-elevation-md);display:grid;grid-template-areas:"header" "status" "main";grid-template-rows:auto auto minmax(0,1fr);max-height:min(var(--available-height),480px,60dvh);max-width:var(--available-width);min-width:var(--anchor-width);&.b9a9946a395ccad8__is-width-anchor{width:var(--anchor-width)}&._7c9f1b268b013f02__is-width-content{width:auto}&._6f31db51d79ec899__is-width-sm{width:min(var(--available-width),var(--wpds-dimension-surface-width-sm,320px))}&.fa45cdb5f45e57fb__is-width-md{width:min(var(--available-width),var(--wpds-dimension-surface-width-md,400px))}&._46a909337f6be21c__is-width-lg{width:min(var(--available-width),var(--wpds-dimension-surface-width-lg,560px))}&._6083938dff06df34__is-width-available{width:var(--available-width)}}._101852fa256bb935__is-align-item-with-trigger[data-side=none] ._234b520016b4e56f__popup{max-height:none}._2fff4e9defe85de5__list-chrome{--_wp-ui-popup-selection-offset:0px;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);grid-area:main;line-height:var(--wpds-typography-line-height-sm,20px);&:has(.a3adcfd0b73ffd40__item-icon){--_wp-ui-popup-selection-offset:calc(var(--wpds-dimension-size-2xs, 16px) + var(--wpds-dimension-gap-xs, 4px) - var(--wpds-dimension-padding-xs, 4px))}}.f43dc7c768d7b622__list{display:grid;grid-template-areas:"scrollable" "footer";grid-template-rows:minmax(0,1fr) auto}._233cd60cdb84a2ef__list-scrollable-container{grid-area:scrollable;overflow-block:auto;overscroll-behavior:contain;scroll-padding-block:var(--wp-ui-popup-padding);&:not(:empty){padding-block:var(--wp-ui-popup-padding)}}.ec4db6f0122263e7__list-footer{grid-area:footer;padding-block:var(--wp-ui-popup-padding);._233cd60cdb84a2ef__list-scrollable-container:not(:empty)+&{border-block-start:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb)}}.b3c0d7f103fb10a2__group:not(:first-child){margin-block-start:var(--wpds-dimension-gap-sm,8px)}._21b59380477c306c__group-label{align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;min-height:var(--wpds-dimension-size-sm,24px);padding-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));padding-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset))}.be89a1df0fe77bd2__separator{background-color:var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);height:var(--wpds-border-width-xs,1px);margin-block:var(--wpds-dimension-gap-xs,4px);margin-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));margin-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset));@media (forced-colors:active){background-color:CanvasText}}._684ccb7988365b4f__item{--wp-ui-popup-item-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:var(--wpds-dimension-padding-xs,4px);align-items:center;border-radius:var(--wpds-border-radius-sm,2px);display:flex;gap:var(--wpds-dimension-gap-xs,4px);justify-content:flex-start;margin-inline:var(--wp-ui-popup-padding);min-height:var(--wp-ui-popup-item-height);min-width:0;overflow-wrap:anywhere;padding-block:var(--wp-ui-popup-item-padding-block);padding-inline:var(--wp-ui-popup-item-padding-inline);user-select:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}&:has(>.a3adcfd0b73ffd40__item-icon){align-content:center;align-items:start;display:grid;grid-template-columns:auto minmax(0,1fr);padding-inline-start:calc(var(--wp-ui-popup-item-padding-inline) - var(--wpds-dimension-padding-xs, 4px))}&._38f7faff93c61958__is-size-small{--wp-ui-popup-item-height:var(--wpds-dimension-size-sm,24px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:2px}&:not([data-selected]){._92fbe4765dfad5ee__item-indicator-icon{opacity:0}}&[data-highlighted]:not([aria-disabled=true]){background-color:var(--wpds-color-background-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);outline:none;@media (forced-colors:active){--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid Highlight}}&[aria-disabled=true]{background-color:var(--wpds-color-background-interactive-brand-weak-disabled,#0000);color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}}}.a3adcfd0b73ffd40__item-icon{align-items:center;display:flex;height:var(--wpds-typography-line-height-sm,20px);pointer-events:none}._92fbe4765dfad5ee__item-indicator-icon{translate:0 1px}._74465fc7e8ecea1a__item-content{align-items:center;display:flex;gap:var(--wpds-dimension-gap-xs,4px);min-width:0}._41f0dd875c005a48__item-text{display:flex;flex-direction:column;min-width:0}._12335b76ada0b1f5__item-label{min-width:0}._160635254c702623__item-description{color:var(--wpds-color-foreground-content-neutral-weak,#707070)}._684ccb7988365b4f__item[aria-disabled=true] ._160635254c702623__item-description,._684ccb7988365b4f__item[data-highlighted]:not([aria-disabled=true]) ._160635254c702623__item-description{color:inherit}._6eb78bc92f8d7795__status{grid-area:status}._06c7ff39d2f685b9__empty:not(:empty){grid-area:main}._06c7ff39d2f685b9__empty:not(:empty),._6eb78bc92f8d7795__status:not(:empty):not(:has([data-visually-hidden])){--wp-ui-popup-empty-min-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-empty-padding-inline:var(--wpds-dimension-padding-md,12px);align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px);min-height:var(--wp-ui-popup-empty-min-height);padding-inline:var(--wp-ui-popup-empty-padding-inline)}}}');
  }
  var item_popup_default5 = { "popup": "_234b520016b4e56f__popup _380b81b8f79fb10f__dropdown-motion", "is-width-anchor": "b9a9946a395ccad8__is-width-anchor", "is-width-content": "_7c9f1b268b013f02__is-width-content", "is-width-sm": "_6f31db51d79ec899__is-width-sm", "is-width-md": "fa45cdb5f45e57fb__is-width-md", "is-width-lg": "_46a909337f6be21c__is-width-lg", "is-width-available": "_6083938dff06df34__is-width-available", "is-align-item-with-trigger": "_101852fa256bb935__is-align-item-with-trigger", "list-chrome": "_2fff4e9defe85de5__list-chrome", "item-icon": "a3adcfd0b73ffd40__item-icon", "list": "f43dc7c768d7b622__list _2fff4e9defe85de5__list-chrome", "list-scrollable-container": "_233cd60cdb84a2ef__list-scrollable-container", "list-footer": "ec4db6f0122263e7__list-footer", "group": "b3c0d7f103fb10a2__group", "group-label": "_21b59380477c306c__group-label", "separator": "be89a1df0fe77bd2__separator", "item": "_684ccb7988365b4f__item", "is-size-small": "_38f7faff93c61958__is-size-small", "item-indicator-icon": "_92fbe4765dfad5ee__item-indicator-icon", "item-content": "_74465fc7e8ecea1a__item-content", "item-text": "_41f0dd875c005a48__item-text", "item-label": "_12335b76ada0b1f5__item-label", "item-description": "_160635254c702623__item-description", "status": "_6eb78bc92f8d7795__status", "empty": "_06c7ff39d2f685b9__empty" };
  var ItemLabel = (0, import_element31.forwardRef)(
    function UnforwardedItemLabel({ children, className, render, ...restProps }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime68.jsx)(
        Text,
        {
          ref,
          variant: "body-md",
          render: /* @__PURE__ */ (0, import_jsx_runtime68.jsx)(index_parts_exports2.ItemText, { render }),
          className: clsx_default(item_popup_default5["item-label"], className),
          ...restProps,
          children
        }
      );
    }
  );

  // packages/ui/build-module/form/primitives/select/item.mjs
  var import_jsx_runtime69 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE21 = "data-wp-hash";
  function getRuntime21() {
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
      registerDocument21(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash21(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE21}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE21) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle21(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime21();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash21(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE21, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument21(targetDocument) {
    const runtime = getRuntime21();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle21(targetDocument, hash, css);
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
  function registerStyle21(hash, css) {
    const runtime = getRuntime21();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle21(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle21("abb19c3171", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}[role=treeitem] span._400bf7ee0c6061a9__span[aria-hidden]{display:var(--_gcd-span-display,inline);font-family:var(--_gcd-span-font-family,inherit);font-size:var(--_gcd-span-font-size,inherit);line-height:var(--_gcd-span-line-height,1);pointer-events:var(--_gcd-span-pointer-events,auto);position:var(--_gcd-span-position,static)}");
  }
  var global_css_defense_default7 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li", "span": "_400bf7ee0c6061a9__span" };
  if (typeof process === "undefined" || true) {
    registerStyle21("e25d67a230", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._380b81b8f79fb10f__dropdown-motion,._7f344b94e270e039__dropdown-motion--fade-only{--wp-ui-dropdown-slide-distance:4px;--wp-ui-dropdown-slide-duration:var(--wpds-motion-duration-md,200ms);--wp-ui-dropdown-slide-easing:var(--wpds-motion-easing-expressive,cubic-bezier(0.25,0,0,1));--wp-ui-dropdown-fade-duration:var(--wpds-motion-duration-sm,100ms);--wp-ui-dropdown-fade-easing:linear;@media not (prefers-reduced-motion){transition-duration:var(--wp-ui-dropdown-slide-duration),var(--wp-ui-dropdown-fade-duration);transition-property:transform,opacity;transition-timing-function:var(--wp-ui-dropdown-slide-easing),var(--wp-ui-dropdown-fade-easing);will-change:transform,opacity}opacity:1;&[data-instant]{transition:none}&[data-ending-style],&[data-starting-style]{opacity:0}}._380b81b8f79fb10f__dropdown-motion{transform:translate(0);&[data-side=bottom][data-ending-style],&[data-side=bottom][data-starting-style]{transform:translateY(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=top][data-ending-style],&[data-side=top][data-starting-style]{transform:translateY(var(--wp-ui-dropdown-slide-distance))}&[data-side=left][data-ending-style],&[data-side=left][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=right][data-ending-style],&[data-side=right][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=inline-start][data-ending-style],&[data-side=inline-start][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=inline-end][data-ending-style],&[data-side=inline-end][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-start][data-ending-style],&:dir(rtl)[data-side=inline-start][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-end][data-ending-style],&:dir(rtl)[data-side=inline-end][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}}}}@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._234b520016b4e56f__popup{--wp-ui-popup-padding:var(--wpds-dimension-padding-xs,4px);--_wp-ui-elevation-md:0 2px 3px rgba(0,0,0,.05),0 4px 5px rgba(0,0,0,.04),0 12px 12px rgba(0,0,0,.03),0 16px 16px rgba(0,0,0,.02);background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:var(--wpds-border-radius-md,4px);box-shadow:var(--_wp-ui-elevation-md);display:grid;grid-template-areas:"header" "status" "main";grid-template-rows:auto auto minmax(0,1fr);max-height:min(var(--available-height),480px,60dvh);max-width:var(--available-width);min-width:var(--anchor-width);&.b9a9946a395ccad8__is-width-anchor{width:var(--anchor-width)}&._7c9f1b268b013f02__is-width-content{width:auto}&._6f31db51d79ec899__is-width-sm{width:min(var(--available-width),var(--wpds-dimension-surface-width-sm,320px))}&.fa45cdb5f45e57fb__is-width-md{width:min(var(--available-width),var(--wpds-dimension-surface-width-md,400px))}&._46a909337f6be21c__is-width-lg{width:min(var(--available-width),var(--wpds-dimension-surface-width-lg,560px))}&._6083938dff06df34__is-width-available{width:var(--available-width)}}._101852fa256bb935__is-align-item-with-trigger[data-side=none] ._234b520016b4e56f__popup{max-height:none}._2fff4e9defe85de5__list-chrome{--_wp-ui-popup-selection-offset:0px;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);grid-area:main;line-height:var(--wpds-typography-line-height-sm,20px);&:has(.a3adcfd0b73ffd40__item-icon){--_wp-ui-popup-selection-offset:calc(var(--wpds-dimension-size-2xs, 16px) + var(--wpds-dimension-gap-xs, 4px) - var(--wpds-dimension-padding-xs, 4px))}}.f43dc7c768d7b622__list{display:grid;grid-template-areas:"scrollable" "footer";grid-template-rows:minmax(0,1fr) auto}._233cd60cdb84a2ef__list-scrollable-container{grid-area:scrollable;overflow-block:auto;overscroll-behavior:contain;scroll-padding-block:var(--wp-ui-popup-padding);&:not(:empty){padding-block:var(--wp-ui-popup-padding)}}.ec4db6f0122263e7__list-footer{grid-area:footer;padding-block:var(--wp-ui-popup-padding);._233cd60cdb84a2ef__list-scrollable-container:not(:empty)+&{border-block-start:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb)}}.b3c0d7f103fb10a2__group:not(:first-child){margin-block-start:var(--wpds-dimension-gap-sm,8px)}._21b59380477c306c__group-label{align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;min-height:var(--wpds-dimension-size-sm,24px);padding-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));padding-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset))}.be89a1df0fe77bd2__separator{background-color:var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);height:var(--wpds-border-width-xs,1px);margin-block:var(--wpds-dimension-gap-xs,4px);margin-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));margin-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset));@media (forced-colors:active){background-color:CanvasText}}._684ccb7988365b4f__item{--wp-ui-popup-item-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:var(--wpds-dimension-padding-xs,4px);align-items:center;border-radius:var(--wpds-border-radius-sm,2px);display:flex;gap:var(--wpds-dimension-gap-xs,4px);justify-content:flex-start;margin-inline:var(--wp-ui-popup-padding);min-height:var(--wp-ui-popup-item-height);min-width:0;overflow-wrap:anywhere;padding-block:var(--wp-ui-popup-item-padding-block);padding-inline:var(--wp-ui-popup-item-padding-inline);user-select:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}&:has(>.a3adcfd0b73ffd40__item-icon){align-content:center;align-items:start;display:grid;grid-template-columns:auto minmax(0,1fr);padding-inline-start:calc(var(--wp-ui-popup-item-padding-inline) - var(--wpds-dimension-padding-xs, 4px))}&._38f7faff93c61958__is-size-small{--wp-ui-popup-item-height:var(--wpds-dimension-size-sm,24px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:2px}&:not([data-selected]){._92fbe4765dfad5ee__item-indicator-icon{opacity:0}}&[data-highlighted]:not([aria-disabled=true]){background-color:var(--wpds-color-background-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);outline:none;@media (forced-colors:active){--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid Highlight}}&[aria-disabled=true]{background-color:var(--wpds-color-background-interactive-brand-weak-disabled,#0000);color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}}}.a3adcfd0b73ffd40__item-icon{align-items:center;display:flex;height:var(--wpds-typography-line-height-sm,20px);pointer-events:none}._92fbe4765dfad5ee__item-indicator-icon{translate:0 1px}._74465fc7e8ecea1a__item-content{align-items:center;display:flex;gap:var(--wpds-dimension-gap-xs,4px);min-width:0}._41f0dd875c005a48__item-text{display:flex;flex-direction:column;min-width:0}._12335b76ada0b1f5__item-label{min-width:0}._160635254c702623__item-description{color:var(--wpds-color-foreground-content-neutral-weak,#707070)}._684ccb7988365b4f__item[aria-disabled=true] ._160635254c702623__item-description,._684ccb7988365b4f__item[data-highlighted]:not([aria-disabled=true]) ._160635254c702623__item-description{color:inherit}._6eb78bc92f8d7795__status{grid-area:status}._06c7ff39d2f685b9__empty:not(:empty){grid-area:main}._06c7ff39d2f685b9__empty:not(:empty),._6eb78bc92f8d7795__status:not(:empty):not(:has([data-visually-hidden])){--wp-ui-popup-empty-min-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-empty-padding-inline:var(--wpds-dimension-padding-md,12px);align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px);min-height:var(--wp-ui-popup-empty-min-height);padding-inline:var(--wp-ui-popup-empty-padding-inline)}}}');
  }
  var item_popup_default6 = { "popup": "_234b520016b4e56f__popup _380b81b8f79fb10f__dropdown-motion", "is-width-anchor": "b9a9946a395ccad8__is-width-anchor", "is-width-content": "_7c9f1b268b013f02__is-width-content", "is-width-sm": "_6f31db51d79ec899__is-width-sm", "is-width-md": "fa45cdb5f45e57fb__is-width-md", "is-width-lg": "_46a909337f6be21c__is-width-lg", "is-width-available": "_6083938dff06df34__is-width-available", "is-align-item-with-trigger": "_101852fa256bb935__is-align-item-with-trigger", "list-chrome": "_2fff4e9defe85de5__list-chrome", "item-icon": "a3adcfd0b73ffd40__item-icon", "list": "f43dc7c768d7b622__list _2fff4e9defe85de5__list-chrome", "list-scrollable-container": "_233cd60cdb84a2ef__list-scrollable-container", "list-footer": "ec4db6f0122263e7__list-footer", "group": "b3c0d7f103fb10a2__group", "group-label": "_21b59380477c306c__group-label", "separator": "be89a1df0fe77bd2__separator", "item": "_684ccb7988365b4f__item", "is-size-small": "_38f7faff93c61958__is-size-small", "item-indicator-icon": "_92fbe4765dfad5ee__item-indicator-icon", "item-content": "_74465fc7e8ecea1a__item-content", "item-text": "_41f0dd875c005a48__item-text", "item-label": "_12335b76ada0b1f5__item-label", "item-description": "_160635254c702623__item-description", "status": "_6eb78bc92f8d7795__status", "empty": "_06c7ff39d2f685b9__empty" };
  if (typeof process === "undefined" || true) {
    registerStyle21("10f3806643", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
  }
  var resets_default4 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
  var ITEM_CONTENT_COMPONENTS = {
    Label: ItemLabel,
    Description: ItemDescription,
    descriptionValidationToken: ITEM_DESCRIPTION_DIRECT_CHILD,
    validationMessage: "Select.ItemLabel must be the first direct child of every select item, followed only by Select.ItemDescription components."
  };
  var Item2 = (0, import_element32.forwardRef)(
    function UnforwardedItem2({
      className,
      value,
      size: size4 = "default",
      children,
      "aria-describedby": ariaDescribedBy,
      "aria-label": ariaLabel,
      "aria-labelledby": ariaLabelledBy,
      ...restProps
    }, ref) {
      const { contentChildren, itemAriaProps } = useItemContent(
        children,
        ITEM_CONTENT_COMPONENTS,
        {
          "aria-describedby": ariaDescribedBy,
          "aria-label": ariaLabel,
          "aria-labelledby": ariaLabelledBy
        }
      );
      return /* @__PURE__ */ (0, import_jsx_runtime69.jsxs)(
        index_parts_exports2.Item,
        {
          className: clsx_default(
            global_css_defense_default7.div,
            resets_default4["box-sizing"],
            item_popup_default6.item,
            size4 === "small" && item_popup_default6["is-size-small"],
            className
          ),
          value,
          ref,
          ...itemAriaProps,
          ...restProps,
          children: [
            /* @__PURE__ */ (0, import_jsx_runtime69.jsx)("span", { className: item_popup_default6["item-icon"], children: /* @__PURE__ */ (0, import_jsx_runtime69.jsx)(
              Icon,
              {
                icon: check_default,
                className: item_popup_default6["item-indicator-icon"],
                size: 16
              }
            ) }),
            /* @__PURE__ */ (0, import_jsx_runtime69.jsx)("div", { className: item_popup_default6["item-text"], children: contentChildren })
          ]
        }
      );
    }
  );

  // packages/ui/build-module/form/primitives/select/popup.mjs
  var import_element35 = __toESM(require_element(), 1);

  // packages/ui/build-module/form/primitives/select/portal.mjs
  var import_element33 = __toESM(require_element(), 1);
  var import_jsx_runtime70 = __toESM(require_jsx_runtime(), 1);
  var Portal = (0, import_element33.forwardRef)(function SelectPortal3({ container, ...restProps }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime70.jsx)(
      index_parts_exports2.Portal,
      {
        container: container === void 0 ? getWpCompatOverlaySlot() : container,
        ...restProps,
        ref
      }
    );
  });

  // packages/ui/build-module/form/primitives/select/positioner.mjs
  var import_element34 = __toESM(require_element(), 1);
  var import_jsx_runtime71 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE22 = "data-wp-hash";
  function getRuntime22() {
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
      registerDocument22(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash22(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE22}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE22) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle22(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime22();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash22(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE22, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument22(targetDocument) {
    const runtime = getRuntime22();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle22(targetDocument, hash, css);
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
  function registerStyle22(hash, css) {
    const runtime = getRuntime22();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle22(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle22("10f3806643", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
  }
  var resets_default5 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
  if (typeof process === "undefined" || true) {
    registerStyle22("e25d67a230", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._380b81b8f79fb10f__dropdown-motion,._7f344b94e270e039__dropdown-motion--fade-only{--wp-ui-dropdown-slide-distance:4px;--wp-ui-dropdown-slide-duration:var(--wpds-motion-duration-md,200ms);--wp-ui-dropdown-slide-easing:var(--wpds-motion-easing-expressive,cubic-bezier(0.25,0,0,1));--wp-ui-dropdown-fade-duration:var(--wpds-motion-duration-sm,100ms);--wp-ui-dropdown-fade-easing:linear;@media not (prefers-reduced-motion){transition-duration:var(--wp-ui-dropdown-slide-duration),var(--wp-ui-dropdown-fade-duration);transition-property:transform,opacity;transition-timing-function:var(--wp-ui-dropdown-slide-easing),var(--wp-ui-dropdown-fade-easing);will-change:transform,opacity}opacity:1;&[data-instant]{transition:none}&[data-ending-style],&[data-starting-style]{opacity:0}}._380b81b8f79fb10f__dropdown-motion{transform:translate(0);&[data-side=bottom][data-ending-style],&[data-side=bottom][data-starting-style]{transform:translateY(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=top][data-ending-style],&[data-side=top][data-starting-style]{transform:translateY(var(--wp-ui-dropdown-slide-distance))}&[data-side=left][data-ending-style],&[data-side=left][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=right][data-ending-style],&[data-side=right][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=inline-start][data-ending-style],&[data-side=inline-start][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=inline-end][data-ending-style],&[data-side=inline-end][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-start][data-ending-style],&:dir(rtl)[data-side=inline-start][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-end][data-ending-style],&:dir(rtl)[data-side=inline-end][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}}}}@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._234b520016b4e56f__popup{--wp-ui-popup-padding:var(--wpds-dimension-padding-xs,4px);--_wp-ui-elevation-md:0 2px 3px rgba(0,0,0,.05),0 4px 5px rgba(0,0,0,.04),0 12px 12px rgba(0,0,0,.03),0 16px 16px rgba(0,0,0,.02);background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:var(--wpds-border-radius-md,4px);box-shadow:var(--_wp-ui-elevation-md);display:grid;grid-template-areas:"header" "status" "main";grid-template-rows:auto auto minmax(0,1fr);max-height:min(var(--available-height),480px,60dvh);max-width:var(--available-width);min-width:var(--anchor-width);&.b9a9946a395ccad8__is-width-anchor{width:var(--anchor-width)}&._7c9f1b268b013f02__is-width-content{width:auto}&._6f31db51d79ec899__is-width-sm{width:min(var(--available-width),var(--wpds-dimension-surface-width-sm,320px))}&.fa45cdb5f45e57fb__is-width-md{width:min(var(--available-width),var(--wpds-dimension-surface-width-md,400px))}&._46a909337f6be21c__is-width-lg{width:min(var(--available-width),var(--wpds-dimension-surface-width-lg,560px))}&._6083938dff06df34__is-width-available{width:var(--available-width)}}._101852fa256bb935__is-align-item-with-trigger[data-side=none] ._234b520016b4e56f__popup{max-height:none}._2fff4e9defe85de5__list-chrome{--_wp-ui-popup-selection-offset:0px;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);grid-area:main;line-height:var(--wpds-typography-line-height-sm,20px);&:has(.a3adcfd0b73ffd40__item-icon){--_wp-ui-popup-selection-offset:calc(var(--wpds-dimension-size-2xs, 16px) + var(--wpds-dimension-gap-xs, 4px) - var(--wpds-dimension-padding-xs, 4px))}}.f43dc7c768d7b622__list{display:grid;grid-template-areas:"scrollable" "footer";grid-template-rows:minmax(0,1fr) auto}._233cd60cdb84a2ef__list-scrollable-container{grid-area:scrollable;overflow-block:auto;overscroll-behavior:contain;scroll-padding-block:var(--wp-ui-popup-padding);&:not(:empty){padding-block:var(--wp-ui-popup-padding)}}.ec4db6f0122263e7__list-footer{grid-area:footer;padding-block:var(--wp-ui-popup-padding);._233cd60cdb84a2ef__list-scrollable-container:not(:empty)+&{border-block-start:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb)}}.b3c0d7f103fb10a2__group:not(:first-child){margin-block-start:var(--wpds-dimension-gap-sm,8px)}._21b59380477c306c__group-label{align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;min-height:var(--wpds-dimension-size-sm,24px);padding-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));padding-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset))}.be89a1df0fe77bd2__separator{background-color:var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);height:var(--wpds-border-width-xs,1px);margin-block:var(--wpds-dimension-gap-xs,4px);margin-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));margin-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset));@media (forced-colors:active){background-color:CanvasText}}._684ccb7988365b4f__item{--wp-ui-popup-item-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:var(--wpds-dimension-padding-xs,4px);align-items:center;border-radius:var(--wpds-border-radius-sm,2px);display:flex;gap:var(--wpds-dimension-gap-xs,4px);justify-content:flex-start;margin-inline:var(--wp-ui-popup-padding);min-height:var(--wp-ui-popup-item-height);min-width:0;overflow-wrap:anywhere;padding-block:var(--wp-ui-popup-item-padding-block);padding-inline:var(--wp-ui-popup-item-padding-inline);user-select:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}&:has(>.a3adcfd0b73ffd40__item-icon){align-content:center;align-items:start;display:grid;grid-template-columns:auto minmax(0,1fr);padding-inline-start:calc(var(--wp-ui-popup-item-padding-inline) - var(--wpds-dimension-padding-xs, 4px))}&._38f7faff93c61958__is-size-small{--wp-ui-popup-item-height:var(--wpds-dimension-size-sm,24px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:2px}&:not([data-selected]){._92fbe4765dfad5ee__item-indicator-icon{opacity:0}}&[data-highlighted]:not([aria-disabled=true]){background-color:var(--wpds-color-background-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);outline:none;@media (forced-colors:active){--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid Highlight}}&[aria-disabled=true]{background-color:var(--wpds-color-background-interactive-brand-weak-disabled,#0000);color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}}}.a3adcfd0b73ffd40__item-icon{align-items:center;display:flex;height:var(--wpds-typography-line-height-sm,20px);pointer-events:none}._92fbe4765dfad5ee__item-indicator-icon{translate:0 1px}._74465fc7e8ecea1a__item-content{align-items:center;display:flex;gap:var(--wpds-dimension-gap-xs,4px);min-width:0}._41f0dd875c005a48__item-text{display:flex;flex-direction:column;min-width:0}._12335b76ada0b1f5__item-label{min-width:0}._160635254c702623__item-description{color:var(--wpds-color-foreground-content-neutral-weak,#707070)}._684ccb7988365b4f__item[aria-disabled=true] ._160635254c702623__item-description,._684ccb7988365b4f__item[data-highlighted]:not([aria-disabled=true]) ._160635254c702623__item-description{color:inherit}._6eb78bc92f8d7795__status{grid-area:status}._06c7ff39d2f685b9__empty:not(:empty){grid-area:main}._06c7ff39d2f685b9__empty:not(:empty),._6eb78bc92f8d7795__status:not(:empty):not(:has([data-visually-hidden])){--wp-ui-popup-empty-min-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-empty-padding-inline:var(--wpds-dimension-padding-md,12px);align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px);min-height:var(--wp-ui-popup-empty-min-height);padding-inline:var(--wp-ui-popup-empty-padding-inline)}}}');
  }
  var item_popup_default7 = { "popup": "_234b520016b4e56f__popup _380b81b8f79fb10f__dropdown-motion", "is-width-anchor": "b9a9946a395ccad8__is-width-anchor", "is-width-content": "_7c9f1b268b013f02__is-width-content", "is-width-sm": "_6f31db51d79ec899__is-width-sm", "is-width-md": "fa45cdb5f45e57fb__is-width-md", "is-width-lg": "_46a909337f6be21c__is-width-lg", "is-width-available": "_6083938dff06df34__is-width-available", "is-align-item-with-trigger": "_101852fa256bb935__is-align-item-with-trigger", "list-chrome": "_2fff4e9defe85de5__list-chrome", "item-icon": "a3adcfd0b73ffd40__item-icon", "list": "f43dc7c768d7b622__list _2fff4e9defe85de5__list-chrome", "list-scrollable-container": "_233cd60cdb84a2ef__list-scrollable-container", "list-footer": "ec4db6f0122263e7__list-footer", "group": "b3c0d7f103fb10a2__group", "group-label": "_21b59380477c306c__group-label", "separator": "be89a1df0fe77bd2__separator", "item": "_684ccb7988365b4f__item", "is-size-small": "_38f7faff93c61958__is-size-small", "item-indicator-icon": "_92fbe4765dfad5ee__item-indicator-icon", "item-content": "_74465fc7e8ecea1a__item-content", "item-text": "_41f0dd875c005a48__item-text", "item-label": "_12335b76ada0b1f5__item-label", "item-description": "_160635254c702623__item-description", "status": "_6eb78bc92f8d7795__status", "empty": "_06c7ff39d2f685b9__empty" };
  if (typeof process === "undefined" || true) {
    registerStyle22("aaac8c315e", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._0ab863136fb95530__positioner{z-index:var(--wp-ui-select-z-index,initial)}.a9ab07efb9ef413f__list{display:block;min-block-size:0;overflow-block:auto;overscroll-behavior:contain;scroll-padding-block:var(--wp-ui-popup-padding);&:not(:empty){padding-block:var(--wp-ui-popup-padding)}}}}");
  }
  var style_default10 = { "positioner": "_0ab863136fb95530__positioner", "list": "a9ab07efb9ef413f__list" };
  var Positioner = (0, import_element34.forwardRef)(
    function SelectPositioner3({ className, alignItemWithTrigger = true, ...props }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime71.jsx)(
        index_parts_exports2.Positioner,
        {
          ...ITEM_POPUP_POSITIONER_PROPS,
          ...props,
          alignItemWithTrigger,
          ref,
          className: clsx_default(
            resets_default5["box-sizing"],
            style_default10.positioner,
            alignItemWithTrigger && item_popup_default7["is-align-item-with-trigger"],
            className
          )
        }
      );
    }
  );

  // packages/ui/build-module/form/primitives/select/popup.mjs
  var import_jsx_runtime72 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE23 = "data-wp-hash";
  function getRuntime23() {
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
      registerDocument23(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash23(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE23}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE23) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle23(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime23();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash23(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE23, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument23(targetDocument) {
    const runtime = getRuntime23();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle23(targetDocument, hash, css);
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
  function registerStyle23(hash, css) {
    const runtime = getRuntime23();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle23(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle23("e25d67a230", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._380b81b8f79fb10f__dropdown-motion,._7f344b94e270e039__dropdown-motion--fade-only{--wp-ui-dropdown-slide-distance:4px;--wp-ui-dropdown-slide-duration:var(--wpds-motion-duration-md,200ms);--wp-ui-dropdown-slide-easing:var(--wpds-motion-easing-expressive,cubic-bezier(0.25,0,0,1));--wp-ui-dropdown-fade-duration:var(--wpds-motion-duration-sm,100ms);--wp-ui-dropdown-fade-easing:linear;@media not (prefers-reduced-motion){transition-duration:var(--wp-ui-dropdown-slide-duration),var(--wp-ui-dropdown-fade-duration);transition-property:transform,opacity;transition-timing-function:var(--wp-ui-dropdown-slide-easing),var(--wp-ui-dropdown-fade-easing);will-change:transform,opacity}opacity:1;&[data-instant]{transition:none}&[data-ending-style],&[data-starting-style]{opacity:0}}._380b81b8f79fb10f__dropdown-motion{transform:translate(0);&[data-side=bottom][data-ending-style],&[data-side=bottom][data-starting-style]{transform:translateY(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=top][data-ending-style],&[data-side=top][data-starting-style]{transform:translateY(var(--wp-ui-dropdown-slide-distance))}&[data-side=left][data-ending-style],&[data-side=left][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=right][data-ending-style],&[data-side=right][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=inline-start][data-ending-style],&[data-side=inline-start][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=inline-end][data-ending-style],&[data-side=inline-end][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-start][data-ending-style],&:dir(rtl)[data-side=inline-start][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-end][data-ending-style],&:dir(rtl)[data-side=inline-end][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}}}}@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._234b520016b4e56f__popup{--wp-ui-popup-padding:var(--wpds-dimension-padding-xs,4px);--_wp-ui-elevation-md:0 2px 3px rgba(0,0,0,.05),0 4px 5px rgba(0,0,0,.04),0 12px 12px rgba(0,0,0,.03),0 16px 16px rgba(0,0,0,.02);background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:var(--wpds-border-radius-md,4px);box-shadow:var(--_wp-ui-elevation-md);display:grid;grid-template-areas:"header" "status" "main";grid-template-rows:auto auto minmax(0,1fr);max-height:min(var(--available-height),480px,60dvh);max-width:var(--available-width);min-width:var(--anchor-width);&.b9a9946a395ccad8__is-width-anchor{width:var(--anchor-width)}&._7c9f1b268b013f02__is-width-content{width:auto}&._6f31db51d79ec899__is-width-sm{width:min(var(--available-width),var(--wpds-dimension-surface-width-sm,320px))}&.fa45cdb5f45e57fb__is-width-md{width:min(var(--available-width),var(--wpds-dimension-surface-width-md,400px))}&._46a909337f6be21c__is-width-lg{width:min(var(--available-width),var(--wpds-dimension-surface-width-lg,560px))}&._6083938dff06df34__is-width-available{width:var(--available-width)}}._101852fa256bb935__is-align-item-with-trigger[data-side=none] ._234b520016b4e56f__popup{max-height:none}._2fff4e9defe85de5__list-chrome{--_wp-ui-popup-selection-offset:0px;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);grid-area:main;line-height:var(--wpds-typography-line-height-sm,20px);&:has(.a3adcfd0b73ffd40__item-icon){--_wp-ui-popup-selection-offset:calc(var(--wpds-dimension-size-2xs, 16px) + var(--wpds-dimension-gap-xs, 4px) - var(--wpds-dimension-padding-xs, 4px))}}.f43dc7c768d7b622__list{display:grid;grid-template-areas:"scrollable" "footer";grid-template-rows:minmax(0,1fr) auto}._233cd60cdb84a2ef__list-scrollable-container{grid-area:scrollable;overflow-block:auto;overscroll-behavior:contain;scroll-padding-block:var(--wp-ui-popup-padding);&:not(:empty){padding-block:var(--wp-ui-popup-padding)}}.ec4db6f0122263e7__list-footer{grid-area:footer;padding-block:var(--wp-ui-popup-padding);._233cd60cdb84a2ef__list-scrollable-container:not(:empty)+&{border-block-start:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb)}}.b3c0d7f103fb10a2__group:not(:first-child){margin-block-start:var(--wpds-dimension-gap-sm,8px)}._21b59380477c306c__group-label{align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;min-height:var(--wpds-dimension-size-sm,24px);padding-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));padding-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset))}.be89a1df0fe77bd2__separator{background-color:var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);height:var(--wpds-border-width-xs,1px);margin-block:var(--wpds-dimension-gap-xs,4px);margin-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));margin-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset));@media (forced-colors:active){background-color:CanvasText}}._684ccb7988365b4f__item{--wp-ui-popup-item-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:var(--wpds-dimension-padding-xs,4px);align-items:center;border-radius:var(--wpds-border-radius-sm,2px);display:flex;gap:var(--wpds-dimension-gap-xs,4px);justify-content:flex-start;margin-inline:var(--wp-ui-popup-padding);min-height:var(--wp-ui-popup-item-height);min-width:0;overflow-wrap:anywhere;padding-block:var(--wp-ui-popup-item-padding-block);padding-inline:var(--wp-ui-popup-item-padding-inline);user-select:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}&:has(>.a3adcfd0b73ffd40__item-icon){align-content:center;align-items:start;display:grid;grid-template-columns:auto minmax(0,1fr);padding-inline-start:calc(var(--wp-ui-popup-item-padding-inline) - var(--wpds-dimension-padding-xs, 4px))}&._38f7faff93c61958__is-size-small{--wp-ui-popup-item-height:var(--wpds-dimension-size-sm,24px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:2px}&:not([data-selected]){._92fbe4765dfad5ee__item-indicator-icon{opacity:0}}&[data-highlighted]:not([aria-disabled=true]){background-color:var(--wpds-color-background-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);outline:none;@media (forced-colors:active){--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid Highlight}}&[aria-disabled=true]{background-color:var(--wpds-color-background-interactive-brand-weak-disabled,#0000);color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}}}.a3adcfd0b73ffd40__item-icon{align-items:center;display:flex;height:var(--wpds-typography-line-height-sm,20px);pointer-events:none}._92fbe4765dfad5ee__item-indicator-icon{translate:0 1px}._74465fc7e8ecea1a__item-content{align-items:center;display:flex;gap:var(--wpds-dimension-gap-xs,4px);min-width:0}._41f0dd875c005a48__item-text{display:flex;flex-direction:column;min-width:0}._12335b76ada0b1f5__item-label{min-width:0}._160635254c702623__item-description{color:var(--wpds-color-foreground-content-neutral-weak,#707070)}._684ccb7988365b4f__item[aria-disabled=true] ._160635254c702623__item-description,._684ccb7988365b4f__item[data-highlighted]:not([aria-disabled=true]) ._160635254c702623__item-description{color:inherit}._6eb78bc92f8d7795__status{grid-area:status}._06c7ff39d2f685b9__empty:not(:empty){grid-area:main}._06c7ff39d2f685b9__empty:not(:empty),._6eb78bc92f8d7795__status:not(:empty):not(:has([data-visually-hidden])){--wp-ui-popup-empty-min-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-empty-padding-inline:var(--wpds-dimension-padding-md,12px);align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px);min-height:var(--wp-ui-popup-empty-min-height);padding-inline:var(--wp-ui-popup-empty-padding-inline)}}}');
  }
  var item_popup_default8 = { "popup": "_234b520016b4e56f__popup _380b81b8f79fb10f__dropdown-motion", "is-width-anchor": "b9a9946a395ccad8__is-width-anchor", "is-width-content": "_7c9f1b268b013f02__is-width-content", "is-width-sm": "_6f31db51d79ec899__is-width-sm", "is-width-md": "fa45cdb5f45e57fb__is-width-md", "is-width-lg": "_46a909337f6be21c__is-width-lg", "is-width-available": "_6083938dff06df34__is-width-available", "is-align-item-with-trigger": "_101852fa256bb935__is-align-item-with-trigger", "list-chrome": "_2fff4e9defe85de5__list-chrome", "item-icon": "a3adcfd0b73ffd40__item-icon", "list": "f43dc7c768d7b622__list _2fff4e9defe85de5__list-chrome", "list-scrollable-container": "_233cd60cdb84a2ef__list-scrollable-container", "list-footer": "ec4db6f0122263e7__list-footer", "group": "b3c0d7f103fb10a2__group", "group-label": "_21b59380477c306c__group-label", "separator": "be89a1df0fe77bd2__separator", "item": "_684ccb7988365b4f__item", "is-size-small": "_38f7faff93c61958__is-size-small", "item-indicator-icon": "_92fbe4765dfad5ee__item-indicator-icon", "item-content": "_74465fc7e8ecea1a__item-content", "item-text": "_41f0dd875c005a48__item-text", "item-label": "_12335b76ada0b1f5__item-label", "item-description": "_160635254c702623__item-description", "status": "_6eb78bc92f8d7795__status", "empty": "_06c7ff39d2f685b9__empty" };
  if (typeof process === "undefined" || true) {
    registerStyle23("aaac8c315e", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._0ab863136fb95530__positioner{z-index:var(--wp-ui-select-z-index,initial)}.a9ab07efb9ef413f__list{display:block;min-block-size:0;overflow-block:auto;overscroll-behavior:contain;scroll-padding-block:var(--wp-ui-popup-padding);&:not(:empty){padding-block:var(--wp-ui-popup-padding)}}}}");
  }
  var style_default11 = { "positioner": "_0ab863136fb95530__positioner", "list": "a9ab07efb9ef413f__list" };
  var Popup = (0, import_element35.forwardRef)(
    function UnforwardedPopup({ className, portal, positioner, width, children, ...restProps }, ref) {
      const popupContent = /* @__PURE__ */ (0, import_jsx_runtime72.jsx)(
        index_parts_exports2.Popup,
        {
          ref,
          className: clsx_default(
            item_popup_default8.popup,
            getItemPopupWidthClassName(width),
            className
          ),
          ...restProps,
          children: /* @__PURE__ */ (0, import_jsx_runtime72.jsx)(
            index_parts_exports2.List,
            {
              className: clsx_default(
                item_popup_default8["list-chrome"],
                style_default11.list
              ),
              children
            }
          )
        }
      );
      const positionedPopup = renderSlotWithChildren(
        positioner,
        /* @__PURE__ */ (0, import_jsx_runtime72.jsx)(Positioner, {}),
        popupContent
      );
      return renderSlotWithChildren(portal, /* @__PURE__ */ (0, import_jsx_runtime72.jsx)(Portal, {}), positionedPopup);
    }
  );

  // packages/ui/build-module/form/primitives/select/root.mjs
  var import_jsx_runtime73 = __toESM(require_jsx_runtime(), 1);
  function Root2(props) {
    return /* @__PURE__ */ (0, import_jsx_runtime73.jsx)(DirectionProvider3, { children: /* @__PURE__ */ (0, import_jsx_runtime73.jsx)(index_parts_exports2.Root, { ...props }) });
  }

  // packages/ui/build-module/form/primitives/select/separator.mjs
  var import_element36 = __toESM(require_element(), 1);
  var import_jsx_runtime74 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE24 = "data-wp-hash";
  function getRuntime24() {
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
      registerDocument24(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash24(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE24}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE24) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle24(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime24();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash24(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE24, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument24(targetDocument) {
    const runtime = getRuntime24();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle24(targetDocument, hash, css);
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
  function registerStyle24(hash, css) {
    const runtime = getRuntime24();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle24(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle24("e25d67a230", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._380b81b8f79fb10f__dropdown-motion,._7f344b94e270e039__dropdown-motion--fade-only{--wp-ui-dropdown-slide-distance:4px;--wp-ui-dropdown-slide-duration:var(--wpds-motion-duration-md,200ms);--wp-ui-dropdown-slide-easing:var(--wpds-motion-easing-expressive,cubic-bezier(0.25,0,0,1));--wp-ui-dropdown-fade-duration:var(--wpds-motion-duration-sm,100ms);--wp-ui-dropdown-fade-easing:linear;@media not (prefers-reduced-motion){transition-duration:var(--wp-ui-dropdown-slide-duration),var(--wp-ui-dropdown-fade-duration);transition-property:transform,opacity;transition-timing-function:var(--wp-ui-dropdown-slide-easing),var(--wp-ui-dropdown-fade-easing);will-change:transform,opacity}opacity:1;&[data-instant]{transition:none}&[data-ending-style],&[data-starting-style]{opacity:0}}._380b81b8f79fb10f__dropdown-motion{transform:translate(0);&[data-side=bottom][data-ending-style],&[data-side=bottom][data-starting-style]{transform:translateY(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=top][data-ending-style],&[data-side=top][data-starting-style]{transform:translateY(var(--wp-ui-dropdown-slide-distance))}&[data-side=left][data-ending-style],&[data-side=left][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=right][data-ending-style],&[data-side=right][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&[data-side=inline-start][data-ending-style],&[data-side=inline-start][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}&[data-side=inline-end][data-ending-style],&[data-side=inline-end][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-start][data-ending-style],&:dir(rtl)[data-side=inline-start][data-starting-style]{transform:translateX(calc(var(--wp-ui-dropdown-slide-distance)*-1))}&:dir(rtl)[data-side=inline-end][data-ending-style],&:dir(rtl)[data-side=inline-end][data-starting-style]{transform:translateX(var(--wp-ui-dropdown-slide-distance))}}}}@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._234b520016b4e56f__popup{--wp-ui-popup-padding:var(--wpds-dimension-padding-xs,4px);--_wp-ui-elevation-md:0 2px 3px rgba(0,0,0,.05),0 4px 5px rgba(0,0,0,.04),0 12px 12px rgba(0,0,0,.03),0 16px 16px rgba(0,0,0,.02);background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:var(--wpds-border-radius-md,4px);box-shadow:var(--_wp-ui-elevation-md);display:grid;grid-template-areas:"header" "status" "main";grid-template-rows:auto auto minmax(0,1fr);max-height:min(var(--available-height),480px,60dvh);max-width:var(--available-width);min-width:var(--anchor-width);&.b9a9946a395ccad8__is-width-anchor{width:var(--anchor-width)}&._7c9f1b268b013f02__is-width-content{width:auto}&._6f31db51d79ec899__is-width-sm{width:min(var(--available-width),var(--wpds-dimension-surface-width-sm,320px))}&.fa45cdb5f45e57fb__is-width-md{width:min(var(--available-width),var(--wpds-dimension-surface-width-md,400px))}&._46a909337f6be21c__is-width-lg{width:min(var(--available-width),var(--wpds-dimension-surface-width-lg,560px))}&._6083938dff06df34__is-width-available{width:var(--available-width)}}._101852fa256bb935__is-align-item-with-trigger[data-side=none] ._234b520016b4e56f__popup{max-height:none}._2fff4e9defe85de5__list-chrome{--_wp-ui-popup-selection-offset:0px;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);grid-area:main;line-height:var(--wpds-typography-line-height-sm,20px);&:has(.a3adcfd0b73ffd40__item-icon){--_wp-ui-popup-selection-offset:calc(var(--wpds-dimension-size-2xs, 16px) + var(--wpds-dimension-gap-xs, 4px) - var(--wpds-dimension-padding-xs, 4px))}}.f43dc7c768d7b622__list{display:grid;grid-template-areas:"scrollable" "footer";grid-template-rows:minmax(0,1fr) auto}._233cd60cdb84a2ef__list-scrollable-container{grid-area:scrollable;overflow-block:auto;overscroll-behavior:contain;scroll-padding-block:var(--wp-ui-popup-padding);&:not(:empty){padding-block:var(--wp-ui-popup-padding)}}.ec4db6f0122263e7__list-footer{grid-area:footer;padding-block:var(--wp-ui-popup-padding);._233cd60cdb84a2ef__list-scrollable-container:not(:empty)+&{border-block-start:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb)}}.b3c0d7f103fb10a2__group:not(:first-child){margin-block-start:var(--wpds-dimension-gap-sm,8px)}._21b59380477c306c__group-label{align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;min-height:var(--wpds-dimension-size-sm,24px);padding-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));padding-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset))}.be89a1df0fe77bd2__separator{background-color:var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);height:var(--wpds-border-width-xs,1px);margin-block:var(--wpds-dimension-gap-xs,4px);margin-inline-end:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px));margin-inline-start:calc(var(--wp-ui-popup-padding) + var(--wpds-dimension-padding-sm, 8px) + var(--_wp-ui-popup-selection-offset));@media (forced-colors:active){background-color:CanvasText}}._684ccb7988365b4f__item{--wp-ui-popup-item-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:var(--wpds-dimension-padding-xs,4px);align-items:center;border-radius:var(--wpds-border-radius-sm,2px);display:flex;gap:var(--wpds-dimension-gap-xs,4px);justify-content:flex-start;margin-inline:var(--wp-ui-popup-padding);min-height:var(--wp-ui-popup-item-height);min-width:0;overflow-wrap:anywhere;padding-block:var(--wp-ui-popup-item-padding-block);padding-inline:var(--wp-ui-popup-item-padding-inline);user-select:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}&:has(>.a3adcfd0b73ffd40__item-icon){align-content:center;align-items:start;display:grid;grid-template-columns:auto minmax(0,1fr);padding-inline-start:calc(var(--wp-ui-popup-item-padding-inline) - var(--wpds-dimension-padding-xs, 4px))}&._38f7faff93c61958__is-size-small{--wp-ui-popup-item-height:var(--wpds-dimension-size-sm,24px);--wp-ui-popup-item-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-popup-item-padding-block:2px}&:not([data-selected]){._92fbe4765dfad5ee__item-indicator-icon{opacity:0}}&[data-highlighted]:not([aria-disabled=true]){background-color:var(--wpds-color-background-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);outline:none;@media (forced-colors:active){--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid Highlight}}&[aria-disabled=true]{background-color:var(--wpds-color-background-interactive-brand-weak-disabled,#0000);color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}}}.a3adcfd0b73ffd40__item-icon{align-items:center;display:flex;height:var(--wpds-typography-line-height-sm,20px);pointer-events:none}._92fbe4765dfad5ee__item-indicator-icon{translate:0 1px}._74465fc7e8ecea1a__item-content{align-items:center;display:flex;gap:var(--wpds-dimension-gap-xs,4px);min-width:0}._41f0dd875c005a48__item-text{display:flex;flex-direction:column;min-width:0}._12335b76ada0b1f5__item-label{min-width:0}._160635254c702623__item-description{color:var(--wpds-color-foreground-content-neutral-weak,#707070)}._684ccb7988365b4f__item[aria-disabled=true] ._160635254c702623__item-description,._684ccb7988365b4f__item[data-highlighted]:not([aria-disabled=true]) ._160635254c702623__item-description{color:inherit}._6eb78bc92f8d7795__status{grid-area:status}._06c7ff39d2f685b9__empty:not(:empty){grid-area:main}._06c7ff39d2f685b9__empty:not(:empty),._6eb78bc92f8d7795__status:not(:empty):not(:has([data-visually-hidden])){--wp-ui-popup-empty-min-height:var(--wpds-dimension-size-md,32px);--wp-ui-popup-empty-padding-inline:var(--wpds-dimension-padding-md,12px);align-items:center;color:var(--wpds-color-foreground-content-neutral-weak,#707070);display:flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px);min-height:var(--wp-ui-popup-empty-min-height);padding-inline:var(--wp-ui-popup-empty-padding-inline)}}}');
  }
  var item_popup_default9 = { "popup": "_234b520016b4e56f__popup _380b81b8f79fb10f__dropdown-motion", "is-width-anchor": "b9a9946a395ccad8__is-width-anchor", "is-width-content": "_7c9f1b268b013f02__is-width-content", "is-width-sm": "_6f31db51d79ec899__is-width-sm", "is-width-md": "fa45cdb5f45e57fb__is-width-md", "is-width-lg": "_46a909337f6be21c__is-width-lg", "is-width-available": "_6083938dff06df34__is-width-available", "is-align-item-with-trigger": "_101852fa256bb935__is-align-item-with-trigger", "list-chrome": "_2fff4e9defe85de5__list-chrome", "item-icon": "a3adcfd0b73ffd40__item-icon", "list": "f43dc7c768d7b622__list _2fff4e9defe85de5__list-chrome", "list-scrollable-container": "_233cd60cdb84a2ef__list-scrollable-container", "list-footer": "ec4db6f0122263e7__list-footer", "group": "b3c0d7f103fb10a2__group", "group-label": "_21b59380477c306c__group-label", "separator": "be89a1df0fe77bd2__separator", "item": "_684ccb7988365b4f__item", "is-size-small": "_38f7faff93c61958__is-size-small", "item-indicator-icon": "_92fbe4765dfad5ee__item-indicator-icon", "item-content": "_74465fc7e8ecea1a__item-content", "item-text": "_41f0dd875c005a48__item-text", "item-label": "_12335b76ada0b1f5__item-label", "item-description": "_160635254c702623__item-description", "status": "_6eb78bc92f8d7795__status", "empty": "_06c7ff39d2f685b9__empty" };
  var Separator = (0, import_element36.forwardRef)(
    function SelectSeparator2({
      className,
      orientation: _orientation,
      ...props
    }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime74.jsx)(
        index_parts_exports2.Separator,
        {
          ref,
          className: clsx_default(item_popup_default9.separator, className),
          ...props
        }
      );
    }
  );

  // packages/ui/build-module/form/primitives/select/trigger.mjs
  var import_element37 = __toESM(require_element(), 1);
  var import_i18n7 = __toESM(require_i18n(), 1);
  var import_jsx_runtime75 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE25 = "data-wp-hash";
  function getRuntime25() {
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
      registerDocument25(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash25(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE25}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE25) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle25(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime25();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash25(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE25, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument25(targetDocument) {
    const runtime = getRuntime25();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle25(targetDocument, hash, css);
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
  function registerStyle25(hash, css) {
    const runtime = getRuntime25();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle25(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle25("7c3267f794", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{.af79fb116edb0dd7__outset-ring--focus:focus,.dfcfdc28396e5d98__outset-ring--focus-visible:focus-visible,.e5cd9ee879f6403a__outset-ring--focus-within:focus-within{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within,.abc777e9713fa711__outset-ring--focus-except-active:focus{outline:none}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.abc777e9713fa711__outset-ring--focus-except-active:focus:not(:active){--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}}");
  }
  var focus_module_default3 = { "outset-ring--focus": "af79fb116edb0dd7__outset-ring--focus", "outset-ring--focus-visible": "dfcfdc28396e5d98__outset-ring--focus-visible", "outset-ring--focus-within": "e5cd9ee879f6403a__outset-ring--focus-within", "outset-ring--focus-except-active": "abc777e9713fa711__outset-ring--focus-except-active", "outset-ring--focus-within-except-active": "_3c9f5ee9fc9c136d__outset-ring--focus-within-except-active" };
  if (typeof process === "undefined" || true) {
    registerStyle25("8fc4b25e27", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._6e4e0445ef20426b__trigger-wrapper{&.fa98c865fb77e675__is-minimal{width:fit-content}}._7893295e3f6d1af7__trigger{align-items:center;background-color:transparent;border:none;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);display:flex;font-family:inherit;font-size:inherit;gap:var(--wpds-dimension-gap-xs,4px);justify-content:space-between;line-height:1.4;padding-block:4px;padding-inline-end:calc(var(--wp-ui-input-layout-padding-inline) - 4px);padding-inline-start:var(--wp-ui-input-layout-padding-inline);text-align:start;user-select:none;width:100%;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}&.fa98c865fb77e675__is-minimal{width:auto}&:focus{outline:none}&[data-disabled]{background-color:var(--wpds-color-background-interactive-neutral-weak-disabled,#0000);color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d)}}.e760c9339965ce84__trigger-value{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;._7893295e3f6d1af7__trigger[data-placeholder]:not([data-disabled]) &{color:var(--wpds-color-foreground-interactive-neutral-weak,#707070)}}._308bc63e43681f99__trigger-caret{flex:0 0 auto}}}");
  }
  var select_trigger_default = { "trigger-wrapper": "_6e4e0445ef20426b__trigger-wrapper", "is-minimal": "fa98c865fb77e675__is-minimal", "trigger": "_7893295e3f6d1af7__trigger", "trigger-value": "e760c9339965ce84__trigger-value", "trigger-caret": "_308bc63e43681f99__trigger-caret" };
  var Trigger = (0, import_element37.forwardRef)(
    function UnforwardedTrigger({
      className,
      size: size4,
      variant,
      children,
      placeholder = (0, import_i18n7.__)("Select"),
      ...restProps
    }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime75.jsx)(
        InputLayout2,
        {
          className: clsx_default(
            focus_module_default3["outset-ring--focus-within-except-active"],
            select_trigger_default["trigger-wrapper"],
            variant === "minimal" && select_trigger_default["is-minimal"],
            className
          ),
          size: size4,
          isBorderless: variant === "minimal",
          children: /* @__PURE__ */ (0, import_jsx_runtime75.jsxs)(
            index_parts_exports2.Trigger,
            {
              ...restProps,
              className: clsx_default(
                select_trigger_default.trigger,
                variant === "minimal" && select_trigger_default["is-minimal"]
              ),
              "data-can-disable-input-layout": true,
              ref,
              children: [
                /* @__PURE__ */ (0, import_jsx_runtime75.jsx)(
                  index_parts_exports2.Value,
                  {
                    placeholder,
                    className: select_trigger_default["trigger-value"],
                    children
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime75.jsx)(
                  Icon,
                  {
                    className: select_trigger_default["trigger-caret"],
                    icon: chevron_down_default,
                    size: 18
                  }
                )
              ]
            }
          )
        }
      );
    }
  );

  // packages/ui/build-module/form/input-control/input-control.mjs
  var import_element38 = __toESM(require_element(), 1);
  var import_jsx_runtime76 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE26 = "data-wp-hash";
  function getRuntime26() {
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
      registerDocument26(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash26(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE26}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE26) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle26(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime26();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash26(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE26, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument26(targetDocument) {
    const runtime = getRuntime26();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle26(targetDocument, hash, css);
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
  function registerStyle26(hash, css) {
    const runtime = getRuntime26();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle26(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle26("0c752c3d08", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer compositions{@supports (-webkit-hyphens:none) and (not (-moz-appearance:none)){.a9211a7e6af24bed__root input[type=date]:not([data-filled]):not(:focus),.a9211a7e6af24bed__root input[type=datetime-local]:not([data-filled]):not(:focus),.a9211a7e6af24bed__root input[type=time]:not([data-filled]):not(:focus){--_gcd-input-color:transparent;--_gcd-input-color-disabled:transparent;color:transparent;&:disabled::-webkit-datetime-edit-text{color:transparent}}}}}");
  }
  var style_default12 = { "root": "a9211a7e6af24bed__root" };
  var InputControl = (0, import_element38.forwardRef)(
    function UnforwardedInputControl({
      className,
      label,
      description,
      details,
      hideLabelFromVision,
      ...restProps
    }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime76.jsxs)(field_exports.Root, { className: clsx_default(style_default12.root, className), children: [
        /* @__PURE__ */ (0, import_jsx_runtime76.jsx)(field_exports.Label, { hideFromVision: hideLabelFromVision, children: label }),
        /* @__PURE__ */ (0, import_jsx_runtime76.jsx)(Input3, { ref, ...restProps }),
        description && /* @__PURE__ */ (0, import_jsx_runtime76.jsx)(field_exports.Description, { children: description }),
        details && /* @__PURE__ */ (0, import_jsx_runtime76.jsx)(field_exports.Details, { children: details })
      ] });
    }
  );

  // packages/ui/build-module/form/select-control/select-control.mjs
  var import_element41 = __toESM(require_element(), 1);

  // packages/ui/build-module/form/select-control/item.mjs
  var import_element39 = __toESM(require_element(), 1);
  var import_jsx_runtime77 = __toESM(require_jsx_runtime(), 1);
  var Item3 = (0, import_element39.forwardRef)(
    function UnforwardedItem3(props, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime77.jsx)(select_exports.Item, { ref, ...props });
    }
  );

  // packages/ui/build-module/form/select-control/trigger.mjs
  var import_element40 = __toESM(require_element(), 1);
  var import_jsx_runtime78 = __toESM(require_jsx_runtime(), 1);
  var Trigger2 = (0, import_element40.forwardRef)(function UnforwardedTrigger2(props, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime78.jsx)(select_exports.Trigger, { ref, ...props });
  });

  // packages/ui/build-module/form/select-control/select-control.mjs
  var import_jsx_runtime79 = __toESM(require_jsx_runtime(), 1);
  var DEFAULT_TRIGGER = /* @__PURE__ */ (0, import_jsx_runtime79.jsx)(Trigger2, {});
  var SelectControl = (0, import_element41.forwardRef)(function UnforwardedSelectControl({
    className,
    children,
    items,
    label,
    description,
    details,
    hideLabelFromVision,
    placeholder,
    popupWidth = "content",
    size: size4 = "default",
    trigger,
    ...restProps
  }, ref) {
    const triggerElement = useRender({
      render: trigger ?? DEFAULT_TRIGGER,
      ref,
      props: { placeholder, size: size4 }
    });
    return /* @__PURE__ */ (0, import_jsx_runtime79.jsxs)(field_exports.Root, { className, children: [
      /* @__PURE__ */ (0, import_jsx_runtime79.jsx)(field_exports.Label, { hideFromVision: hideLabelFromVision, children: label }),
      /* @__PURE__ */ (0, import_jsx_runtime79.jsxs)(select_exports.Root, { items, ...restProps, children: [
        triggerElement,
        /* @__PURE__ */ (0, import_jsx_runtime79.jsx)(select_exports.Popup, { width: popupWidth, children: children !== void 0 ? children : items?.map((item) => /* @__PURE__ */ (0, import_jsx_runtime79.jsxs)(
          Item3,
          {
            value: item,
            label: item.label,
            disabled: item.disabled,
            children: [
              /* @__PURE__ */ (0, import_jsx_runtime79.jsx)(select_exports.ItemLabel, { children: item.label }),
              item.description ? /* @__PURE__ */ (0, import_jsx_runtime79.jsx)(select_exports.ItemDescription, { children: item.description }) : null
            ]
          },
          item.value ?? "null"
        )) })
      ] }),
      description && /* @__PURE__ */ (0, import_jsx_runtime79.jsx)(field_exports.Description, { children: description }),
      details && /* @__PURE__ */ (0, import_jsx_runtime79.jsx)(field_exports.Details, { children: details })
    ] });
  });

  // packages/ui/build-module/form/select-control/index.mjs
  Trigger2.displayName = "SelectControl.Trigger";
  Group.displayName = "SelectControl.Group";
  GroupLabel.displayName = "SelectControl.GroupLabel";
  Item3.displayName = "SelectControl.Item";
  ItemLabel.displayName = "SelectControl.ItemLabel";
  ItemDescription.displayName = "SelectControl.ItemDescription";
  var SelectControl2 = Object.assign(SelectControl, {
    /**
     * The trigger button, passed through the `trigger` prop to customize
     * its props while retaining the control's label and selected content.
     */
    Trigger: Trigger2,
    /**
     * Groups related items together with an associated label rendered by
     * `SelectControl.GroupLabel`.
     */
    Group,
    /**
     * Renders a label for a `SelectControl.Group`.
     */
    GroupLabel,
    /**
     * An item rendered inside a `SelectControl` popup.
     */
    Item: Item3,
    /**
     * The primary label of a select item.
     */
    ItemLabel,
    /**
     * Supplementary content that describes a select item via
     * `aria-describedby`.
     */
    ItemDescription,
    /**
     * Renders a visual separator between items or groups.
     */
    Separator
  });

  // packages/ui/build-module/form/with-validation/validated-input-control/validated-input-control.mjs
  var import_element42 = __toESM(require_element(), 1);
  var import_compose2 = __toESM(require_compose(), 1);
  var import_jsx_runtime80 = __toESM(require_jsx_runtime(), 1);
  var ValidatedInputControl = (0, import_element42.forwardRef)(function UnforwardedValidatedInputControl({ required, markWhenOptional, customValidity, ...restProps }, forwardedRef) {
    const validityTargetRef = (0, import_element42.useRef)(null);
    const mergedRefs = (0, import_compose2.useMergeRefs)([forwardedRef, validityTargetRef]);
    return /* @__PURE__ */ (0, import_jsx_runtime80.jsx)(
      ControlWithError,
      {
        required,
        markWhenOptional,
        customValidity,
        getValidityTarget: () => validityTargetRef.current,
        children: /* @__PURE__ */ (0, import_jsx_runtime80.jsx)(InputControl, { ref: mergedRefs, ...restProps })
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
  var import_element43 = __toESM(require_element(), 1);
  var import_compose3 = __toESM(require_compose(), 1);
  var import_jsx_runtime81 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE27 = "data-wp-hash";
  function getRuntime27() {
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
      registerDocument27(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash27(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE27}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE27) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle27(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime27();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash27(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE27, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument27(targetDocument) {
    const runtime = getRuntime27();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle27(targetDocument, hash, css);
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
  function registerStyle27(hash, css) {
    const runtime = getRuntime27();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle27(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle27("f3c08da521", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._7313adbc8a112e90__tablist{--direction-start:left;--direction-end:right;align-items:stretch;display:flex;overflow-inline:auto;overscroll-behavior-inline:none;position:relative;&:dir(rtl){--direction-start:right;--direction-end:left}&[data-orientation=horizontal]{--fade-width:4rem;--fade-gradient-base:transparent 0%,#000 var(--fade-width);--fade-gradient-composed:var(--fade-gradient-base),#000 60%,transparent 50%;width:fit-content;&:not(.eb7aaeb14de04857__is-scrolling){overflow:visible}&._9f2ac729c68a735a__is-overflowing-first{mask-image:linear-gradient(to var(--direction-end),var(--fade-gradient-base))}&._81c799c1f3cdd261__is-overflowing-last{mask-image:linear-gradient(to var(--direction-start),var(--fade-gradient-base))}&._9f2ac729c68a735a__is-overflowing-first._81c799c1f3cdd261__is-overflowing-last{mask-image:linear-gradient(to right,var(--fade-gradient-composed)),linear-gradient(to left,var(--fade-gradient-composed))}&._59228b5227f38a99__is-minimal-variant{gap:1rem}}&[data-orientation=vertical]{flex-direction:column}}._1c37dcfaa1ad8cda__indicator{@media not (prefers-reduced-motion){transition-duration:.2s;transition-property:translate,width,height;transition-timing-function:ease-out}background-color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);outline:2px solid transparent;outline-offset:-1px;pointer-events:none;position:absolute;z-index:1;._7313adbc8a112e90__tablist:has(>.a5fd8814f195aa5e__tab[aria-selected=true]:not([data-disabled]):focus-visible)\n				&{background-color:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e)}&[data-orientation=horizontal]{bottom:0;height:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px));left:0;translate:var(--active-tab-left) 0;width:var(--active-tab-width)}&[data-orientation=vertical]{height:max(0px,calc(var(--active-tab-height) - var(--wpds-dimension-padding-md, 12px)*2));inset-inline-start:0;top:var(--wpds-dimension-padding-md,12px);translate:0 var(--active-tab-top);width:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}.a5fd8814f195aa5e__tab{align-items:center;background:transparent;border:none;border-radius:0;box-shadow:none;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);cursor:var(--wpds-cursor-control,pointer);display:flex;flex:1 0 auto;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);font-weight:400;line-height:1.2;outline:none;padding:0;position:relative;white-space:nowrap;z-index:1;&:is([aria-selected=true],[data-disabled]){cursor:default}&[data-disabled]{color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}}&:not([data-disabled]):is(:not([aria-selected=true]):hover,:not([aria-selected=true]):active,:focus-visible){color:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e);&:after{background-color:var(--wpds-color-background-interactive-neutral-weak-active,#ededed);opacity:1}}&:after,&:before{border-radius:var(--wpds-border-radius-sm,2px);opacity:0;outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid transparent;pointer-events:none;position:absolute;z-index:-1;@media not (prefers-reduced-motion){transition:opacity .1s linear}}&:focus-visible:after{opacity:1;outline-color:var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9))}[data-orientation=horizontal] &{height:48px;padding-inline:var(--wpds-dimension-padding-lg,16px);scroll-margin:24px;&:after{content:"";inset-block:calc(var(--wpds-dimension-padding-md, 12px) + var(--wpds-dimension-padding-xs, 4px));inset-inline:var(--wpds-dimension-padding-lg,16px);outline-offset:var(--wpds-dimension-padding-xs,4px)}&:not([data-disabled]):is(:not([aria-selected=true]):hover,:not([aria-selected=true]):active,:focus-visible):after{box-shadow:0 0 0 var(--wpds-dimension-padding-xs,4px) var(--wpds-color-background-interactive-neutral-weak-active,#ededed)}}._59228b5227f38a99__is-minimal-variant[data-orientation=horizontal] &{padding-inline:0;&:after{inset-inline:0}}._59228b5227f38a99__is-minimal-variant.eb7aaeb14de04857__is-scrolling[data-orientation=horizontal] &{&:before{content:"";inset-block:var(--wpds-dimension-padding-md,12px);inset-inline:round(up,var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)),1px);z-index:0}&:focus-visible{&:before{opacity:1;outline-color:var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9))}&:after{outline:none}}}[data-orientation=vertical] &{min-height:var(--wpds-dimension-size-lg,40px);padding:var(--wpds-dimension-padding-md,12px);&:after{content:"";inset:0;outline-offset:calc(var(--wpds-border-width-focus, var(--wp-admin-border-width-focus, 2px))*-1)}}}._5dfc77e6edd345d4__tab-children{align-items:center;display:flex;flex-grow:1;[data-orientation=horizontal] &{justify-content:center}[data-orientation=vertical] &{justify-content:start}}}}');
  }
  var style_default13 = { "tablist": "_7313adbc8a112e90__tablist", "is-scrolling": "eb7aaeb14de04857__is-scrolling", "is-overflowing-first": "_9f2ac729c68a735a__is-overflowing-first", "is-overflowing-last": "_81c799c1f3cdd261__is-overflowing-last", "is-minimal-variant": "_59228b5227f38a99__is-minimal-variant", "indicator": "_1c37dcfaa1ad8cda__indicator", "tab": "a5fd8814f195aa5e__tab", "tab-children": "_5dfc77e6edd345d4__tab-children" };
  var SCROLL_EPSILON = 1;
  var List = (0, import_element43.forwardRef)(
    function TabList({
      children,
      variant = "default",
      className,
      activateOnFocus,
      ...otherProps
    }, forwardedRef) {
      const [listEl, setListEl] = (0, import_element43.useState)(null);
      const [overflow, setOverflow] = (0, import_element43.useState)({
        first: false,
        last: false,
        isScrolling: false
      });
      (0, import_element43.useEffect)(() => {
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
      return /* @__PURE__ */ (0, import_jsx_runtime81.jsxs)(
        index_parts_exports3.List,
        {
          ref: mergedListRef,
          activateOnFocus,
          "data-select-on-move": activateOnFocus ? "true" : "false",
          className: clsx_default(
            style_default13.tablist,
            overflow.isScrolling && style_default13["is-scrolling"],
            overflow.first && style_default13["is-overflowing-first"],
            overflow.last && style_default13["is-overflowing-last"],
            style_default13[`is-${variant}-variant`],
            className
          ),
          ...otherProps,
          tabIndex: otherProps.tabIndex ?? (overflow.isScrolling ? -1 : void 0),
          children: [
            children,
            /* @__PURE__ */ (0, import_jsx_runtime81.jsx)(index_parts_exports3.Indicator, { className: style_default13.indicator })
          ]
        }
      );
    }
  );

  // packages/ui/build-module/tabs/panel.mjs
  var import_element45 = __toESM(require_element(), 1);

  // packages/ui/build-module/tabs/context.mjs
  var import_element44 = __toESM(require_element(), 1);
  var import_jsx_runtime82 = __toESM(require_jsx_runtime(), 1);
  var VALIDATION_ENABLED2 = true;
  var TabsValidationContext = VALIDATION_ENABLED2 ? (0, import_element44.createContext)(null) : null;
  function useRegisterTabDev() {
    const context = (0, import_element44.useContext)(TabsValidationContext);
    (0, import_element44.useEffect)(() => {
      if (context) {
        return context.registerTab();
      }
      return void 0;
    }, [context]);
  }
  function useRegisterTabProd() {
  }
  var useRegisterTab = VALIDATION_ENABLED2 ? useRegisterTabDev : useRegisterTabProd;
  function useRegisterPanelDev() {
    const context = (0, import_element44.useContext)(TabsValidationContext);
    (0, import_element44.useEffect)(() => {
      if (context) {
        return context.registerPanel();
      }
      return void 0;
    }, [context]);
  }
  function useRegisterPanelProd() {
  }
  var useRegisterPanel = VALIDATION_ENABLED2 ? useRegisterPanelDev : useRegisterPanelProd;
  function TabsValidationProviderDev({
    children
  }) {
    const tabCountRef = (0, import_element44.useRef)(0);
    const panelCountRef = (0, import_element44.useRef)(0);
    const scheduleValidation = useScheduleValidation(() => {
      const tabCount = tabCountRef.current;
      const panelCount = panelCountRef.current;
      if (tabCount !== panelCount) {
        throw new Error(
          `Tabs: Tab/Panel count mismatch (${tabCount} Tabs, ${panelCount} Panels). Each Tab must be associated with exactly one Panel. Mismatched or missing associations can break screen reader navigation and violate WAI-ARIA Tabs pattern requirements.`
        );
      }
    });
    const registerTab = (0, import_element44.useCallback)(() => {
      tabCountRef.current += 1;
      scheduleValidation();
      return () => {
        tabCountRef.current -= 1;
        scheduleValidation();
      };
    }, [scheduleValidation]);
    const registerPanel = (0, import_element44.useCallback)(() => {
      panelCountRef.current += 1;
      scheduleValidation();
      return () => {
        panelCountRef.current -= 1;
        scheduleValidation();
      };
    }, [scheduleValidation]);
    const contextValue = (0, import_element44.useMemo)(
      () => ({
        registerTab,
        registerPanel
      }),
      [registerTab, registerPanel]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime82.jsx)(TabsValidationContext.Provider, { value: contextValue, children });
  }
  function TabsValidationProviderProd({
    children
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime82.jsx)(import_jsx_runtime82.Fragment, { children });
  }
  var TabsValidationProvider = VALIDATION_ENABLED2 ? TabsValidationProviderDev : TabsValidationProviderProd;

  // packages/ui/build-module/tabs/panel.mjs
  var import_jsx_runtime83 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE28 = "data-wp-hash";
  function getRuntime28() {
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
      registerDocument28(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash28(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE28}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE28) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle28(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime28();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash28(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE28, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument28(targetDocument) {
    const runtime = getRuntime28();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle28(targetDocument, hash, css);
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
  function registerStyle28(hash, css) {
    const runtime = getRuntime28();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle28(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle28("abb19c3171", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}[role=treeitem] span._400bf7ee0c6061a9__span[aria-hidden]{display:var(--_gcd-span-display,inline);font-family:var(--_gcd-span-font-family,inherit);font-size:var(--_gcd-span-font-size,inherit);line-height:var(--_gcd-span-line-height,1);pointer-events:var(--_gcd-span-pointer-events,auto);position:var(--_gcd-span-position,static)}");
  }
  var global_css_defense_default8 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li", "span": "_400bf7ee0c6061a9__span" };
  if (typeof process === "undefined" || true) {
    registerStyle28("7c3267f794", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{.af79fb116edb0dd7__outset-ring--focus:focus,.dfcfdc28396e5d98__outset-ring--focus-visible:focus-visible,.e5cd9ee879f6403a__outset-ring--focus-within:focus-within{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within,.abc777e9713fa711__outset-ring--focus-except-active:focus{outline:none}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.abc777e9713fa711__outset-ring--focus-except-active:focus:not(:active){--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}}");
  }
  var focus_module_default4 = { "outset-ring--focus": "af79fb116edb0dd7__outset-ring--focus", "outset-ring--focus-visible": "dfcfdc28396e5d98__outset-ring--focus-visible", "outset-ring--focus-within": "e5cd9ee879f6403a__outset-ring--focus-within", "outset-ring--focus-except-active": "abc777e9713fa711__outset-ring--focus-except-active", "outset-ring--focus-within-except-active": "_3c9f5ee9fc9c136d__outset-ring--focus-within-except-active" };
  var Panel = (0, import_element45.forwardRef)(
    function TabPanel({ className, ...otherProps }, forwardedRef) {
      useRegisterPanel();
      return /* @__PURE__ */ (0, import_jsx_runtime83.jsx)(
        index_parts_exports3.Panel,
        {
          ref: forwardedRef,
          className: clsx_default(
            global_css_defense_default8.div,
            focus_module_default4["outset-ring--focus-visible"],
            className
          ),
          ...otherProps
        }
      );
    }
  );

  // packages/ui/build-module/tabs/root.mjs
  var import_element46 = __toESM(require_element(), 1);
  var import_jsx_runtime84 = __toESM(require_jsx_runtime(), 1);
  var Root3 = (0, import_element46.forwardRef)(
    function TabsRoot3({ ...otherProps }, forwardedRef) {
      return /* @__PURE__ */ (0, import_jsx_runtime84.jsx)(DirectionProvider3, { children: /* @__PURE__ */ (0, import_jsx_runtime84.jsx)(TabsValidationProvider, { children: /* @__PURE__ */ (0, import_jsx_runtime84.jsx)(index_parts_exports3.Root, { ref: forwardedRef, ...otherProps }) }) });
    }
  );

  // packages/ui/build-module/tabs/tab.mjs
  var import_element47 = __toESM(require_element(), 1);
  var import_jsx_runtime85 = __toESM(require_jsx_runtime(), 1);
  var STYLE_HASH_ATTRIBUTE29 = "data-wp-hash";
  function getRuntime29() {
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
      registerDocument29(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash29(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE29}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE29) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle29(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime29();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash29(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE29, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument29(targetDocument) {
    const runtime = getRuntime29();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle29(targetDocument, hash, css);
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
  function registerStyle29(hash, css) {
    const runtime = getRuntime29();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle29(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle29("f3c08da521", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._7313adbc8a112e90__tablist{--direction-start:left;--direction-end:right;align-items:stretch;display:flex;overflow-inline:auto;overscroll-behavior-inline:none;position:relative;&:dir(rtl){--direction-start:right;--direction-end:left}&[data-orientation=horizontal]{--fade-width:4rem;--fade-gradient-base:transparent 0%,#000 var(--fade-width);--fade-gradient-composed:var(--fade-gradient-base),#000 60%,transparent 50%;width:fit-content;&:not(.eb7aaeb14de04857__is-scrolling){overflow:visible}&._9f2ac729c68a735a__is-overflowing-first{mask-image:linear-gradient(to var(--direction-end),var(--fade-gradient-base))}&._81c799c1f3cdd261__is-overflowing-last{mask-image:linear-gradient(to var(--direction-start),var(--fade-gradient-base))}&._9f2ac729c68a735a__is-overflowing-first._81c799c1f3cdd261__is-overflowing-last{mask-image:linear-gradient(to right,var(--fade-gradient-composed)),linear-gradient(to left,var(--fade-gradient-composed))}&._59228b5227f38a99__is-minimal-variant{gap:1rem}}&[data-orientation=vertical]{flex-direction:column}}._1c37dcfaa1ad8cda__indicator{@media not (prefers-reduced-motion){transition-duration:.2s;transition-property:translate,width,height;transition-timing-function:ease-out}background-color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);outline:2px solid transparent;outline-offset:-1px;pointer-events:none;position:absolute;z-index:1;._7313adbc8a112e90__tablist:has(>.a5fd8814f195aa5e__tab[aria-selected=true]:not([data-disabled]):focus-visible)\n				&{background-color:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e)}&[data-orientation=horizontal]{bottom:0;height:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px));left:0;translate:var(--active-tab-left) 0;width:var(--active-tab-width)}&[data-orientation=vertical]{height:max(0px,calc(var(--active-tab-height) - var(--wpds-dimension-padding-md, 12px)*2));inset-inline-start:0;top:var(--wpds-dimension-padding-md,12px);translate:0 var(--active-tab-top);width:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}.a5fd8814f195aa5e__tab{align-items:center;background:transparent;border:none;border-radius:0;box-shadow:none;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);cursor:var(--wpds-cursor-control,pointer);display:flex;flex:1 0 auto;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-md,13px);font-weight:400;line-height:1.2;outline:none;padding:0;position:relative;white-space:nowrap;z-index:1;&:is([aria-selected=true],[data-disabled]){cursor:default}&[data-disabled]{color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d);@media (forced-colors:active){color:GrayText}}&:not([data-disabled]):is(:not([aria-selected=true]):hover,:not([aria-selected=true]):active,:focus-visible){color:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e);&:after{background-color:var(--wpds-color-background-interactive-neutral-weak-active,#ededed);opacity:1}}&:after,&:before{border-radius:var(--wpds-border-radius-sm,2px);opacity:0;outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid transparent;pointer-events:none;position:absolute;z-index:-1;@media not (prefers-reduced-motion){transition:opacity .1s linear}}&:focus-visible:after{opacity:1;outline-color:var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9))}[data-orientation=horizontal] &{height:48px;padding-inline:var(--wpds-dimension-padding-lg,16px);scroll-margin:24px;&:after{content:"";inset-block:calc(var(--wpds-dimension-padding-md, 12px) + var(--wpds-dimension-padding-xs, 4px));inset-inline:var(--wpds-dimension-padding-lg,16px);outline-offset:var(--wpds-dimension-padding-xs,4px)}&:not([data-disabled]):is(:not([aria-selected=true]):hover,:not([aria-selected=true]):active,:focus-visible):after{box-shadow:0 0 0 var(--wpds-dimension-padding-xs,4px) var(--wpds-color-background-interactive-neutral-weak-active,#ededed)}}._59228b5227f38a99__is-minimal-variant[data-orientation=horizontal] &{padding-inline:0;&:after{inset-inline:0}}._59228b5227f38a99__is-minimal-variant.eb7aaeb14de04857__is-scrolling[data-orientation=horizontal] &{&:before{content:"";inset-block:var(--wpds-dimension-padding-md,12px);inset-inline:round(up,var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)),1px);z-index:0}&:focus-visible{&:before{opacity:1;outline-color:var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9))}&:after{outline:none}}}[data-orientation=vertical] &{min-height:var(--wpds-dimension-size-lg,40px);padding:var(--wpds-dimension-padding-md,12px);&:after{content:"";inset:0;outline-offset:calc(var(--wpds-border-width-focus, var(--wp-admin-border-width-focus, 2px))*-1)}}}._5dfc77e6edd345d4__tab-children{align-items:center;display:flex;flex-grow:1;[data-orientation=horizontal] &{justify-content:center}[data-orientation=vertical] &{justify-content:start}}}}');
  }
  var style_default14 = { "tablist": "_7313adbc8a112e90__tablist", "is-scrolling": "eb7aaeb14de04857__is-scrolling", "is-overflowing-first": "_9f2ac729c68a735a__is-overflowing-first", "is-overflowing-last": "_81c799c1f3cdd261__is-overflowing-last", "is-minimal-variant": "_59228b5227f38a99__is-minimal-variant", "indicator": "_1c37dcfaa1ad8cda__indicator", "tab": "a5fd8814f195aa5e__tab", "tab-children": "_5dfc77e6edd345d4__tab-children" };
  var Tab = (0, import_element47.forwardRef)(
    function UnforwardedTab({ className, children, ...otherProps }, forwardedRef) {
      useRegisterTab();
      return /* @__PURE__ */ (0, import_jsx_runtime85.jsx)(
        index_parts_exports3.Tab,
        {
          ref: forwardedRef,
          className: clsx_default(style_default14.tab, className),
          ...otherProps,
          children: /* @__PURE__ */ (0, import_jsx_runtime85.jsx)("span", { className: style_default14["tab-children"], children })
        }
      );
    }
  );

  // packages/format-library/build-module/image/index.mjs
  var import_i18n8 = __toESM(require_i18n(), 1);
  var import_element48 = __toESM(require_element(), 1);
  var import_rich_text3 = __toESM(require_rich_text(), 1);
  var import_block_editor3 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime86 = __toESM(require_jsx_runtime(), 1);
  var ALLOWED_MEDIA_TYPES = ["image"];
  var name3 = "core/image";
  var title3 = (0, import_i18n8.__)("Inline image");
  function getCurrentImageId(activeObjectAttributes) {
    if (!activeObjectAttributes.className) {
      return void 0;
    }
    const [, id] = activeObjectAttributes.className.match(/wp-image-(\d+)/) ?? [];
    return id ? parseInt(id, 10) : void 0;
  }
  var image = {
    name: name3,
    title: title3,
    keywords: [(0, import_i18n8.__)("photo"), (0, import_i18n8.__)("media")],
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
    const style = activeObjectAttributes.style;
    const alt = activeObjectAttributes.alt;
    const width = style?.replace(/\D/g, "");
    const [editedWidth, setEditedWidth] = (0, import_element48.useState)(width);
    const [editedAlt, setEditedAlt] = (0, import_element48.useState)(alt);
    const hasChanged = editedWidth !== width || editedAlt !== alt;
    const popoverAnchor = (0, import_rich_text3.useAnchor)({
      // eslint-disable-next-line react-hooks/refs
      editableContentElement: contentRef.current,
      settings: image
    });
    return /* @__PURE__ */ (0, import_jsx_runtime86.jsx)(
      import_components.Popover,
      {
        focusOnMount: false,
        anchor: popoverAnchor,
        className: "block-editor-format-toolbar__image-popover",
        children: /* @__PURE__ */ (0, import_jsx_runtime86.jsx)(
          "form",
          {
            className: "block-editor-format-toolbar__image-container-content",
            onSubmit: (event) => {
              event.preventDefault();
              if (value.start === void 0) {
                return;
              }
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
            },
            children: /* @__PURE__ */ (0, import_jsx_runtime86.jsxs)(Stack, { direction: "column", gap: "lg", children: [
              /* @__PURE__ */ (0, import_jsx_runtime86.jsx)(
                import_components.__experimentalNumberControl,
                {
                  label: (0, import_i18n8.__)("Width"),
                  value: editedWidth,
                  min: 1,
                  onChange: (newWidth) => {
                    setEditedWidth(newWidth);
                  }
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime86.jsx)(
                import_components.TextareaControl,
                {
                  label: (0, import_i18n8.__)("Alternative text"),
                  value: editedAlt ?? "",
                  onChange: (newAlt) => {
                    setEditedAlt(newAlt);
                  },
                  help: /* @__PURE__ */ (0, import_jsx_runtime86.jsxs)(import_jsx_runtime86.Fragment, { children: [
                    /* @__PURE__ */ (0, import_jsx_runtime86.jsx)(
                      Link,
                      {
                        openInNewTab: true,
                        href: (
                          // translators: Localized tutorial, if one exists. W3C Web Accessibility Initiative link has list of existing translations.
                          (0, import_i18n8.__)(
                            "https://www.w3.org/WAI/tutorials/images/decision-tree/"
                          )
                        ),
                        children: (0, import_i18n8.__)(
                          "Describe the purpose of the image."
                        )
                      }
                    ),
                    /* @__PURE__ */ (0, import_jsx_runtime86.jsx)("br", {}),
                    (0, import_i18n8.__)("Leave empty if decorative.")
                  ] })
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime86.jsx)(Stack, { justify: "right", children: /* @__PURE__ */ (0, import_jsx_runtime86.jsx)(
                import_components.Button,
                {
                  disabled: !hasChanged,
                  accessibleWhenDisabled: true,
                  variant: "primary",
                  type: "submit",
                  size: "compact",
                  children: (0, import_i18n8.__)("Apply")
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
    return /* @__PURE__ */ (0, import_jsx_runtime86.jsxs)(import_block_editor3.MediaUploadCheck, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime86.jsx)(
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
          render: ({ open: open2 }) => /* @__PURE__ */ (0, import_jsx_runtime86.jsx)(
            import_block_editor3.RichTextToolbarButton,
            {
              icon: inline_image_default,
              title: isObjectActive ? (0, import_i18n8.__)("Replace image") : title3,
              onClick: open2,
              isActive: isObjectActive
            }
          )
        }
      ),
      isObjectActive && /* @__PURE__ */ (0, import_jsx_runtime86.jsx)(
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
  var import_i18n9 = __toESM(require_i18n(), 1);
  var import_rich_text4 = __toESM(require_rich_text(), 1);
  var import_block_editor4 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime87 = __toESM(require_jsx_runtime(), 1);
  var name4 = "core/italic";
  var title4 = (0, import_i18n9.__)("Italic");
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
      return /* @__PURE__ */ (0, import_jsx_runtime87.jsxs)(import_jsx_runtime87.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime87.jsx)(
          import_block_editor4.RichTextShortcut,
          {
            type: "primary",
            character: "i",
            onUse: onToggle
          }
        ),
        isVisible && /* @__PURE__ */ (0, import_jsx_runtime87.jsx)(
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
        /* @__PURE__ */ (0, import_jsx_runtime87.jsx)(
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
  var import_i18n12 = __toESM(require_i18n(), 1);
  var import_element51 = __toESM(require_element(), 1);
  var import_rich_text6 = __toESM(require_rich_text(), 1);
  var import_url3 = __toESM(require_url(), 1);
  var import_block_editor6 = __toESM(require_block_editor(), 1);
  var import_html_entities = __toESM(require_html_entities(), 1);
  var import_a11y2 = __toESM(require_a11y(), 1);

  // packages/format-library/build-module/link/inline.mjs
  var import_element50 = __toESM(require_element(), 1);
  var import_i18n11 = __toESM(require_i18n(), 1);
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
    if (!formats?.length || startIndex === void 0 || endIndex === void 0) {
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
    const index3 = newFormats[initialIndex].indexOf(targetFormat);
    startIndex = walkToStart(newFormats, initialIndex, targetFormat, index3);
    endIndex = walkToEnd(newFormats, initialIndex, targetFormat, index3);
    startIndex = startIndex < 0 ? 0 : startIndex;
    return {
      start: startIndex,
      end: endIndex + 1
    };
  }
  function walkToBoundary(formats, initialIndex, targetFormatRef, formatIndex, direction) {
    let index3 = initialIndex;
    const directions = {
      forwards: 1,
      backwards: -1
    };
    const directionIncrement = directions[direction] || 1;
    const inverseDirectionIncrement = directionIncrement * -1;
    while (formats[index3] && formats[index3][formatIndex] === targetFormatRef) {
      index3 = index3 + directionIncrement;
    }
    index3 = index3 + inverseDirectionIncrement;
    return index3;
  }
  var walkToStart = (...args) => walkToBoundary(...args, "backwards");
  var walkToEnd = (...args) => walkToBoundary(...args, "forwards");

  // packages/format-library/build-module/link/css-classes-setting.mjs
  var import_element49 = __toESM(require_element(), 1);
  var import_compose4 = __toESM(require_compose(), 1);
  var import_i18n10 = __toESM(require_i18n(), 1);
  var import_components2 = __toESM(require_components(), 1);
  var import_jsx_runtime88 = __toESM(require_jsx_runtime(), 1);
  var CSSClassesSettingComponent = ({
    setting,
    value,
    onChange
  }) => {
    const hasValue = !!value?.cssClasses?.length;
    const [isSettingActive, setIsSettingActive] = (0, import_element49.useState)(hasValue);
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
    return /* @__PURE__ */ (0, import_jsx_runtime88.jsxs)("fieldset", { children: [
      /* @__PURE__ */ (0, import_jsx_runtime88.jsx)(VisuallyHidden, { render: /* @__PURE__ */ (0, import_jsx_runtime88.jsx)("legend", {}), children: setting.title }),
      /* @__PURE__ */ (0, import_jsx_runtime88.jsxs)(Stack, { direction: "column", gap: "md", children: [
        /* @__PURE__ */ (0, import_jsx_runtime88.jsx)(
          import_components2.CheckboxControl,
          {
            label: setting.title,
            onChange: handleCheckboxChange,
            checked: isSettingActive || hasValue,
            "aria-expanded": isSettingActive,
            "aria-controls": isSettingActive ? controlledRegionId : void 0
          }
        ),
        isSettingActive && /* @__PURE__ */ (0, import_jsx_runtime88.jsx)("div", { id: controlledRegionId, children: /* @__PURE__ */ (0, import_jsx_runtime88.jsx)(
          import_components2.__experimentalInputControl,
          {
            label: (0, import_i18n10.__)("CSS classes"),
            value: value?.cssClasses,
            onChange: handleSettingChange,
            help: (0, import_i18n10.__)(
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
  var import_jsx_runtime89 = __toESM(require_jsx_runtime(), 1);
  var LINK_SETTINGS = [
    ...import_block_editor5.LinkControl.DEFAULT_LINK_SETTINGS,
    {
      id: "nofollow",
      title: (0, import_i18n11.__)("Mark as nofollow")
    },
    {
      id: "cssClasses",
      title: (0, import_i18n11.__)("Additional CSS class(es)"),
      render: (setting, value, onChange) => /* @__PURE__ */ (0, import_jsx_runtime89.jsx)(
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
    const linkValue = (0, import_element50.useMemo)(
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
      (0, import_a11y.speak)((0, import_i18n11.__)("Link removed."), "assertive");
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
      if ((0, import_rich_text5.isCollapsed)(value) && value.start !== void 0 && !isActive) {
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
          (0, import_i18n11.__)(
            "Warning: the link has been inserted but may have errors. Please test it."
          ),
          "assertive"
        );
      } else if (isActive) {
        (0, import_a11y.speak)((0, import_i18n11.__)("Link edited."), "assertive");
      } else {
        (0, import_a11y.speak)((0, import_i18n11.__)("Link inserted."), "assertive");
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
      return (0, import_element50.createInterpolateElement)(
        (0, import_i18n11.sprintf)(
          /* translators: %s: search term. */
          (0, import_i18n11.__)("Create page: <mark>%s</mark>"),
          searchTerm
        ),
        { mark: /* @__PURE__ */ (0, import_jsx_runtime89.jsx)("mark", {}) }
      );
    }
    return /* @__PURE__ */ (0, import_jsx_runtime89.jsx)(
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
        children: /* @__PURE__ */ (0, import_jsx_runtime89.jsx)(
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
  var import_jsx_runtime90 = __toESM(require_jsx_runtime(), 1);
  var name5 = "core/link";
  var title5 = (0, import_i18n12.__)("Link");
  function Edit2({
    isActive,
    activeAttributes,
    value,
    onChange,
    onFocus,
    contentRef,
    isVisible = true
  }) {
    const [addingLink, setAddingLink] = (0, import_element51.useState)(false);
    const [openedBy, setOpenedBy] = (0, import_element51.useState)(null);
    (0, import_element51.useEffect)(() => {
      if (!isActive) {
        setAddingLink(false);
      }
    }, [isActive]);
    (0, import_element51.useLayoutEffect)(() => {
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
      (0, import_a11y2.speak)((0, import_i18n12.__)("Link removed."), "assertive");
    }
    const shouldAutoFocus = !(openedBy?.el?.tagName === "A" && openedBy?.action === "click");
    const hasSelection = !(0, import_rich_text6.isCollapsed)(value);
    return /* @__PURE__ */ (0, import_jsx_runtime90.jsxs)(import_jsx_runtime90.Fragment, { children: [
      hasSelection && /* @__PURE__ */ (0, import_jsx_runtime90.jsx)(
        import_block_editor6.RichTextShortcut,
        {
          type: "primary",
          character: "k",
          onUse: addLink
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime90.jsx)(
        import_block_editor6.RichTextShortcut,
        {
          type: "primaryShift",
          character: "k",
          onUse: onRemoveFormat
        }
      ),
      isVisible && /* @__PURE__ */ (0, import_jsx_runtime90.jsx)(
        import_block_editor6.RichTextToolbarButton,
        {
          name: "link",
          icon: link_default,
          title: isActive ? (0, import_i18n12.__)("Link") : title5,
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
      isVisible && addingLink && /* @__PURE__ */ (0, import_jsx_runtime90.jsx)(
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
  var import_i18n13 = __toESM(require_i18n(), 1);
  var import_rich_text7 = __toESM(require_rich_text(), 1);
  var import_block_editor7 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime91 = __toESM(require_jsx_runtime(), 1);
  var name6 = "core/strikethrough";
  var title6 = (0, import_i18n13.__)("Strikethrough");
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
      return /* @__PURE__ */ (0, import_jsx_runtime91.jsxs)(import_jsx_runtime91.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime91.jsx)(
          import_block_editor7.RichTextShortcut,
          {
            type: "access",
            character: "d",
            onUse: onClick
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime91.jsx)(
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
  var import_i18n14 = __toESM(require_i18n(), 1);
  var import_rich_text8 = __toESM(require_rich_text(), 1);
  var import_block_editor8 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime92 = __toESM(require_jsx_runtime(), 1);
  var name7 = "core/underline";
  var title7 = (0, import_i18n14.__)("Underline");
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
      return /* @__PURE__ */ (0, import_jsx_runtime92.jsxs)(import_jsx_runtime92.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime92.jsx)(
          import_block_editor8.RichTextShortcut,
          {
            type: "primary",
            character: "u",
            onUse: onToggle
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime92.jsx)(
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
  var import_i18n16 = __toESM(require_i18n(), 1);
  var import_element53 = __toESM(require_element(), 1);
  var import_block_editor10 = __toESM(require_block_editor(), 1);
  var import_rich_text10 = __toESM(require_rich_text(), 1);

  // packages/format-library/build-module/text-color/inline.mjs
  var import_element52 = __toESM(require_element(), 1);
  var import_data2 = __toESM(require_data(), 1);
  var import_rich_text9 = __toESM(require_rich_text(), 1);
  var import_block_editor9 = __toESM(require_block_editor(), 1);
  var import_components4 = __toESM(require_components(), 1);
  var import_i18n15 = __toESM(require_i18n(), 1);
  var import_jsx_runtime93 = __toESM(require_jsx_runtime(), 1);
  var TABS = [
    { name: "color", title: (0, import_i18n15.__)("Text") },
    { name: "backgroundColor", title: (0, import_i18n15.__)("Background") }
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
    const activeColors = (0, import_element52.useMemo)(
      () => getActiveColors(value, name16, colors),
      [name16, value, colors]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime93.jsx)(
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
    return /* @__PURE__ */ (0, import_jsx_runtime93.jsx)(
      import_components4.Popover,
      {
        onClose,
        className: "format-library__inline-color-popover",
        anchor: popoverAnchor,
        children: /* @__PURE__ */ (0, import_jsx_runtime93.jsxs)(tabs_exports.Root, { defaultValue: TABS[0].name, children: [
          /* @__PURE__ */ (0, import_jsx_runtime93.jsx)(tabs_exports.List, { children: TABS.map((tab) => /* @__PURE__ */ (0, import_jsx_runtime93.jsx)(tabs_exports.Tab, { value: tab.name, children: tab.title }, tab.name)) }),
          TABS.map((tab) => /* @__PURE__ */ (0, import_jsx_runtime93.jsx)(
            tabs_exports.Panel,
            {
              value: tab.name,
              tabIndex: -1,
              children: /* @__PURE__ */ (0, import_jsx_runtime93.jsx)(
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
  var import_jsx_runtime94 = __toESM(require_jsx_runtime(), 1);
  var transparentValue = "rgba(0, 0, 0, 0)";
  var name8 = "core/text-color";
  var title8 = (0, import_i18n16.__)("Highlight");
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
    const [isAddingColor, setIsAddingColor] = (0, import_element53.useState)(false);
    const colorIndicatorStyle = (0, import_element53.useMemo)(
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
    return /* @__PURE__ */ (0, import_jsx_runtime94.jsxs)(import_jsx_runtime94.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime94.jsx)(
        import_block_editor10.RichTextToolbarButton,
        {
          className: "format-library-text-color-button",
          isActive,
          icon: /* @__PURE__ */ (0, import_jsx_runtime94.jsx)(
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
      isAddingColor && /* @__PURE__ */ (0, import_jsx_runtime94.jsx)(
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
  var import_i18n17 = __toESM(require_i18n(), 1);
  var import_rich_text11 = __toESM(require_rich_text(), 1);
  var import_block_editor11 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime95 = __toESM(require_jsx_runtime(), 1);
  var name9 = "core/subscript";
  var title9 = (0, import_i18n17.__)("Subscript");
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
      return /* @__PURE__ */ (0, import_jsx_runtime95.jsx)(
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
  var import_i18n18 = __toESM(require_i18n(), 1);
  var import_rich_text12 = __toESM(require_rich_text(), 1);
  var import_block_editor12 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime96 = __toESM(require_jsx_runtime(), 1);
  var name10 = "core/superscript";
  var title10 = (0, import_i18n18.__)("Superscript");
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
      return /* @__PURE__ */ (0, import_jsx_runtime96.jsx)(
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
  var import_i18n19 = __toESM(require_i18n(), 1);
  var import_rich_text13 = __toESM(require_rich_text(), 1);
  var import_block_editor13 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime97 = __toESM(require_jsx_runtime(), 1);
  var name11 = "core/keyboard";
  var title11 = (0, import_i18n19.__)("Keyboard input");
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
      return /* @__PURE__ */ (0, import_jsx_runtime97.jsx)(
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
  var import_i18n20 = __toESM(require_i18n(), 1);
  var import_rich_text14 = __toESM(require_rich_text(), 1);
  var import_block_editor14 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime98 = __toESM(require_jsx_runtime(), 1);
  var name12 = "core/unknown";
  var title12 = (0, import_i18n20.__)("Clear Unknown Formatting");
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
      return /* @__PURE__ */ (0, import_jsx_runtime98.jsx)(
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
  var import_i18n21 = __toESM(require_i18n(), 1);
  var import_block_editor15 = __toESM(require_block_editor(), 1);
  var import_components5 = __toESM(require_components(), 1);
  var import_element54 = __toESM(require_element(), 1);
  var import_rich_text15 = __toESM(require_rich_text(), 1);
  var import_jsx_runtime99 = __toESM(require_jsx_runtime(), 1);
  var name13 = "core/language";
  var title13 = (0, import_i18n21.__)("Language");
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
    const [isPopoverVisible, setIsPopoverVisible] = (0, import_element54.useState)(false);
    const togglePopover = () => {
      setIsPopoverVisible((state) => !state);
    };
    return /* @__PURE__ */ (0, import_jsx_runtime99.jsxs)(import_jsx_runtime99.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime99.jsx)(
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
      isPopoverVisible && /* @__PURE__ */ (0, import_jsx_runtime99.jsx)(
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
    const [lang, setLang] = (0, import_element54.useState)("");
    const [dir, setDir] = (0, import_element54.useState)("ltr");
    const directionItems = [
      { label: (0, import_i18n21.__)("Left to right"), value: "ltr" },
      { label: (0, import_i18n21.__)("Right to left"), value: "rtl" }
    ];
    return /* @__PURE__ */ (0, import_jsx_runtime99.jsx)(
      import_components5.Popover,
      {
        className: "block-editor-format-toolbar__language-popover",
        anchor: popoverAnchor,
        onClose,
        children: /* @__PURE__ */ (0, import_jsx_runtime99.jsxs)(
          Stack,
          {
            render: /* @__PURE__ */ (0, import_jsx_runtime99.jsx)("form", {}),
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
              /* @__PURE__ */ (0, import_jsx_runtime99.jsx)(
                import_components5.TextControl,
                {
                  label: title13,
                  value: lang,
                  onChange: (val) => setLang(val),
                  help: (0, import_i18n21.__)(
                    'A valid language attribute, like "en" or "fr".'
                  )
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime99.jsx)(
                SelectControl2,
                {
                  label: (0, import_i18n21.__)("Text direction"),
                  value: directionItems.find((item) => item.value === dir) ?? null,
                  items: directionItems,
                  onValueChange: (item) => {
                    if (item?.value === "ltr" || item?.value === "rtl") {
                      setDir(item.value);
                    }
                  }
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime99.jsx)(Stack, { justify: "right", children: /* @__PURE__ */ (0, import_jsx_runtime99.jsx)(
                import_components5.Button,
                {
                  __next40pxDefaultSize: true,
                  variant: "primary",
                  type: "submit",
                  text: (0, import_i18n21.__)("Apply")
                }
              ) })
            ]
          }
        )
      }
    );
  }

  // packages/format-library/build-module/math/index.mjs
  var import_i18n22 = __toESM(require_i18n(), 1);
  var import_element55 = __toESM(require_element(), 1);
  var import_rich_text16 = __toESM(require_rich_text(), 1);
  var import_block_editor16 = __toESM(require_block_editor(), 1);
  var import_components6 = __toESM(require_components(), 1);
  var import_jsx_runtime100 = __toESM(require_jsx_runtime(), 1);
  var name14 = "core/math";
  var title14 = (0, import_i18n22.__)("Math");
  function InlineUI2({
    value,
    onChange,
    activeObjectAttributes,
    contentRef,
    latexToMathML
  }) {
    const [latex, setLatex] = (0, import_element55.useState)(
      activeObjectAttributes["data-latex"] || ""
    );
    const [error2, setError] = (0, import_element55.useState)(null);
    const formRef = (0, import_element55.useRef)(null);
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
            err instanceof Error ? err.message : (0, import_i18n22.__)("Could not parse the LaTeX math syntax.")
          );
        }
      } else {
        setError(null);
      }
      if (value.start === void 0) {
        return;
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
    return /* @__PURE__ */ (0, import_jsx_runtime100.jsx)(
      import_components6.Popover,
      {
        placement: "bottom-start",
        offset: 8,
        focusOnMount: false,
        anchor: popoverAnchor,
        onFocusOutside: () => formRef.current?.reportValidity(),
        className: "block-editor-format-toolbar__math-popover",
        children: /* @__PURE__ */ (0, import_jsx_runtime100.jsxs)(
          "form",
          {
            ref: formRef,
            style: { minWidth: "300px", padding: "16px" },
            onSubmit: (event) => event.preventDefault(),
            children: [
              /* @__PURE__ */ (0, import_jsx_runtime100.jsx)(
                ValidatedInputControl,
                {
                  label: (0, import_i18n22.__)("LaTeX math syntax"),
                  value: latex,
                  customValidity: error2 ? { type: "invalid", message: error2 } : void 0,
                  onValueChange: handleLatexChange,
                  placeholder: (0, import_i18n22.__)("e.g., x^2, \\frac{a}{b}, \\sqrt{x}"),
                  autoComplete: "off",
                  className: "block-editor-format-toolbar__math-input"
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime100.jsx)(
                Link,
                {
                  openInNewTab: true,
                  className: "block-editor-format-toolbar__math-learn-more",
                  href: (0, import_i18n22.__)(
                    "https://wordpress.org/documentation/article/math-block/"
                  ),
                  children: (0, import_i18n22.__)("Learn more about LaTeX syntax")
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
    const [latexToMathML, setLatexToMathML] = (0, import_element55.useState)();
    (0, import_element55.useEffect)(() => {
      import("@wordpress/latex-to-mathml").then((module) => {
        setLatexToMathML(() => module.default);
      });
    }, []);
    function onClick() {
      let newValue;
      if (isObjectActive) {
        const latex = activeObjectAttributes["data-latex"] || "";
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
    return /* @__PURE__ */ (0, import_jsx_runtime100.jsxs)(import_jsx_runtime100.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime100.jsx)(
        import_block_editor16.RichTextToolbarButton,
        {
          icon: math_default,
          title: title14,
          onClick,
          isActive: isObjectActive
        }
      ),
      isObjectActive && /* @__PURE__ */ (0, import_jsx_runtime100.jsx)(
        InlineUI2,
        {
          value,
          onChange,
          activeObjectAttributes,
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
  var import_i18n23 = __toESM(require_i18n(), 1);
  var import_rich_text17 = __toESM(require_rich_text(), 1);
  var import_block_editor17 = __toESM(require_block_editor(), 1);
  var import_components7 = __toESM(require_components(), 1);
  var import_jsx_runtime101 = __toESM(require_jsx_runtime(), 1);
  var name15 = "core/non-breaking-space";
  var title15 = (0, import_i18n23.__)("Non breaking space");
  var NBSP = "\xA0";
  var icon = /* @__PURE__ */ (0, import_jsx_runtime101.jsx)(import_components7.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime101.jsx)(import_components7.Path, { d: "M4.5 9.5H6V14h12V9.5h1.5v6h-15z" }) });
  function getNonBreakingSpaceRange({ text, start, end }) {
    if (start === void 0 || end === void 0) {
      return null;
    }
    const from = start === end ? Math.max(start - 1, 0) : start;
    const to = start === end ? end + 1 : end;
    return text.slice(from, to).includes(NBSP) ? [from, to] : null;
  }
  function PopoverAnchor({
    contentRef
  }) {
    const popoverAnchor = (0, import_rich_text17.useAnchor)({
      // eslint-disable-next-line react-hooks/refs
      editableContentElement: contentRef.current,
      settings: nonBreakingSpace
    });
    return /* @__PURE__ */ (0, import_jsx_runtime101.jsx)(import_components7.Popover, { anchor: popoverAnchor, children: /* @__PURE__ */ (0, import_jsx_runtime101.jsx)("div", { style: { whiteSpace: "nowrap", padding: "4px" }, children: (0, import_i18n23.__)("Non-breaking space") }) });
  }
  var nonBreakingSpace = {
    name: name15,
    title: title15,
    tagName: "span",
    className: "non-breaking-space",
    edit({ value, onChange, onFocus, contentRef }) {
      function addNonBreakingSpace() {
        onChange((0, import_rich_text17.insert)(value, NBSP));
      }
      const range = getNonBreakingSpaceRange(value);
      function replaceNonBreakingSpaces() {
        if (!range) {
          return;
        }
        const [from, to] = range;
        const { text } = value;
        onChange({
          ...value,
          text: text.slice(0, from) + text.slice(from, to).replaceAll(NBSP, " ") + text.slice(to)
        });
        onFocus();
      }
      const selectedValue = value.start && value.end ? value.text.slice(value.start, value.end) : null;
      return /* @__PURE__ */ (0, import_jsx_runtime101.jsxs)(import_jsx_runtime101.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime101.jsx)(
          import_block_editor17.RichTextShortcut,
          {
            type: "primaryShift",
            character: " ",
            onUse: addNonBreakingSpace
          }
        ),
        range && /* @__PURE__ */ (0, import_jsx_runtime101.jsx)(
          import_block_editor17.RichTextToolbarButton,
          {
            name: "non-breaking-space",
            icon,
            title: (0, import_i18n23.__)("Replace non-breaking spaces"),
            onClick: replaceNonBreakingSpaces,
            isActive: true
          }
        ),
        selectedValue === NBSP && /* @__PURE__ */ (0, import_jsx_runtime101.jsx)(PopoverAnchor, { contentRef })
      ] });
    },
    __experimentalCreatePrepareEditableTree() {
      return (formats, text) => {
        let record = { formats, text };
        let index3 = -1;
        do {
          index3 = text.indexOf(NBSP, index3 + 1);
          if (index3 !== -1) {
            record = (0, import_rich_text17.applyFormat)(
              record,
              { type: name15 },
              index3,
              index3 + 1
            );
          }
        } while (index3 !== -1);
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

use-sync-external-store/cjs/use-sync-external-store-shim/with-selector.development.js:
  (**
   * @license React
   * use-sync-external-store-shim/with-selector.development.js
   *
   * Copyright (c) Meta Platforms, Inc. and affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   *)
*/
(window.wp ||= {}).formatLibrary = wp.formatLibrary;
})();
