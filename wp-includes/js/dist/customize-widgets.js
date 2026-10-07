(function() {
var wp;
(wp ||= {}).customizeWidgets = (() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __commonJS = (cb, mod) => function __require() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
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
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/block-library
  var require_block_library = __commonJS({
    "package-external:@wordpress/block-library"(exports, module) {
      module.exports = window.wp.blockLibrary;
    }
  });

  // package-external:@wordpress/widgets
  var require_widgets = __commonJS({
    "package-external:@wordpress/widgets"(exports, module) {
      module.exports = window.wp.widgets;
    }
  });

  // package-external:@wordpress/blocks
  var require_blocks = __commonJS({
    "package-external:@wordpress/blocks"(exports, module) {
      module.exports = window.wp.blocks;
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

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // vendor-external:react
  var require_react = __commonJS({
    "vendor-external:react"(exports, module) {
      module.exports = window.React;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
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
        function useSyncExternalStore$2(subscribe, getSnapshot) {
          didWarnOld18Alpha || void 0 === React59.startTransition || (didWarnOld18Alpha = true, console.error(
            "You are using an outdated, pre-release alpha of React 18 that does not support useSyncExternalStore. The use-sync-external-store shim will not work correctly. Upgrade to a newer pre-release."
          ));
          var value = getSnapshot();
          if (!didWarnUncachedGetSnapshot) {
            var cachedValue = getSnapshot();
            objectIs(value, cachedValue) || (console.error(
              "The result of getSnapshot should be cached to avoid an infinite loop"
            ), didWarnUncachedGetSnapshot = true);
          }
          cachedValue = useState21({
            inst: { value, getSnapshot }
          });
          var inst = cachedValue[0].inst, forceUpdate = cachedValue[1];
          useLayoutEffect3(
            function() {
              inst.value = value;
              inst.getSnapshot = getSnapshot;
              checkIfSnapshotChanged(inst) && forceUpdate({ inst });
            },
            [subscribe, value, getSnapshot]
          );
          useEffect28(
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
        var React59 = require_react(), objectIs = "function" === typeof Object.is ? Object.is : is, useState21 = React59.useState, useEffect28 = React59.useEffect, useLayoutEffect3 = React59.useLayoutEffect, useDebugValue2 = React59.useDebugValue, didWarnOld18Alpha = false, didWarnUncachedGetSnapshot = false, shim = "undefined" === typeof window || "undefined" === typeof window.document || "undefined" === typeof window.document.createElement ? useSyncExternalStore$1 : useSyncExternalStore$2;
        exports.useSyncExternalStore = void 0 !== React59.useSyncExternalStore ? React59.useSyncExternalStore : shim;
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

  // node_modules/use-sync-external-store/cjs/use-sync-external-store-shim/with-selector.development.js
  var require_with_selector_development = __commonJS({
    "node_modules/use-sync-external-store/cjs/use-sync-external-store-shim/with-selector.development.js"(exports) {
      "use strict";
      (function() {
        function is(x, y) {
          return x === y && (0 !== x || 1 / x === 1 / y) || x !== x && y !== y;
        }
        "undefined" !== typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ && "function" === typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());
        var React59 = require_react(), shim = require_shim(), objectIs = "function" === typeof Object.is ? Object.is : is, useSyncExternalStore3 = shim.useSyncExternalStore, useRef25 = React59.useRef, useEffect28 = React59.useEffect, useMemo25 = React59.useMemo, useDebugValue2 = React59.useDebugValue;
        exports.useSyncExternalStoreWithSelector = function(subscribe, getSnapshot, getServerSnapshot, selector, isEqual) {
          var instRef = useRef25(null);
          if (null === instRef.current) {
            var inst = { hasValue: false, value: null };
            instRef.current = inst;
          } else inst = instRef.current;
          instRef = useMemo25(
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
          useEffect28(
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

  // node_modules/use-sync-external-store/shim/with-selector.js
  var require_with_selector = __commonJS({
    "node_modules/use-sync-external-store/shim/with-selector.js"(exports, module) {
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

  // package-external:@wordpress/theme
  var require_theme = __commonJS({
    "package-external:@wordpress/theme"(exports, module) {
      module.exports = window.wp.theme;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // package-external:@wordpress/primitives
  var require_primitives = __commonJS({
    "package-external:@wordpress/primitives"(exports, module) {
      module.exports = window.wp.primitives;
    }
  });

  // package-external:@wordpress/a11y
  var require_a11y = __commonJS({
    "package-external:@wordpress/a11y"(exports, module) {
      module.exports = window.wp.a11y;
    }
  });

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
    }
  });

  // package-external:@wordpress/hooks
  var require_hooks = __commonJS({
    "package-external:@wordpress/hooks"(exports, module) {
      module.exports = window.wp.hooks;
    }
  });

  // package-external:@wordpress/core-data
  var require_core_data = __commonJS({
    "package-external:@wordpress/core-data"(exports, module) {
      module.exports = window.wp.coreData;
    }
  });

  // package-external:@wordpress/block-editor
  var require_block_editor = __commonJS({
    "package-external:@wordpress/block-editor"(exports, module) {
      module.exports = window.wp.blockEditor;
    }
  });

  // package-external:@wordpress/media-utils
  var require_media_utils = __commonJS({
    "package-external:@wordpress/media-utils"(exports, module) {
      module.exports = window.wp.mediaUtils;
    }
  });

  // package-external:@wordpress/keycodes
  var require_keycodes = __commonJS({
    "package-external:@wordpress/keycodes"(exports, module) {
      module.exports = window.wp.keycodes;
    }
  });

  // package-external:@wordpress/keyboard-shortcuts
  var require_keyboard_shortcuts = __commonJS({
    "package-external:@wordpress/keyboard-shortcuts"(exports, module) {
      module.exports = window.wp.keyboardShortcuts;
    }
  });

  // node_modules/fast-deep-equal/es6/index.js
  var require_es6 = __commonJS({
    "node_modules/fast-deep-equal/es6/index.js"(exports, module) {
      "use strict";
      module.exports = function equal(a, b) {
        if (a === b) return true;
        if (a && b && typeof a == "object" && typeof b == "object") {
          if (a.constructor !== b.constructor) return false;
          var length, i, keys;
          if (Array.isArray(a)) {
            length = a.length;
            if (length != b.length) return false;
            for (i = length; i-- !== 0; )
              if (!equal(a[i], b[i])) return false;
            return true;
          }
          if (a instanceof Map && b instanceof Map) {
            if (a.size !== b.size) return false;
            for (i of a.entries())
              if (!b.has(i[0])) return false;
            for (i of a.entries())
              if (!equal(i[1], b.get(i[0]))) return false;
            return true;
          }
          if (a instanceof Set && b instanceof Set) {
            if (a.size !== b.size) return false;
            for (i of a.entries())
              if (!b.has(i[0])) return false;
            return true;
          }
          if (ArrayBuffer.isView(a) && ArrayBuffer.isView(b)) {
            length = a.length;
            if (length != b.length) return false;
            for (i = length; i-- !== 0; )
              if (a[i] !== b[i]) return false;
            return true;
          }
          if (a.constructor === RegExp) return a.source === b.source && a.flags === b.flags;
          if (a.valueOf !== Object.prototype.valueOf) return a.valueOf() === b.valueOf();
          if (a.toString !== Object.prototype.toString) return a.toString() === b.toString();
          keys = Object.keys(a);
          length = keys.length;
          if (length !== Object.keys(b).length) return false;
          for (i = length; i-- !== 0; )
            if (!Object.prototype.hasOwnProperty.call(b, keys[i])) return false;
          for (i = length; i-- !== 0; ) {
            var key = keys[i];
            if (!equal(a[key], b[key])) return false;
          }
          return true;
        }
        return a !== a && b !== b;
      };
    }
  });

  // package-external:@wordpress/is-shallow-equal
  var require_is_shallow_equal = __commonJS({
    "package-external:@wordpress/is-shallow-equal"(exports, module) {
      module.exports = window.wp.isShallowEqual;
    }
  });

  // package-external:@wordpress/dom
  var require_dom = __commonJS({
    "package-external:@wordpress/dom"(exports, module) {
      module.exports = window.wp.dom;
    }
  });

  // packages/customize-widgets/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    initialize: () => initialize,
    store: () => store
  });
  var import_element60 = __toESM(require_element(), 1);
  var import_block_library = __toESM(require_block_library(), 1);
  var import_widgets5 = __toESM(require_widgets(), 1);
  var import_blocks2 = __toESM(require_blocks(), 1);
  var import_data18 = __toESM(require_data(), 1);
  var import_preferences4 = __toESM(require_preferences(), 1);

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
    name,
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
          error([`A component is changing the ${isControlled ? "" : "un"}controlled ${state} state of ${name} to be ${isControlled ? "un" : ""}controlled.`, "Elements should not switch from uncontrolled to controlled (or vice versa).", `Decide between using a controlled or uncontrolled ${name} element for the lifetime of the component.`, "The nature of the state is determined during the first render. It's considered controlled if the value is not `undefined`.", "More info: https://fb.me/react-controlled-components"].join("\n"));
        }
      }, [state, name, controlled]);
      const {
        current: defaultValue
      } = React.useRef(defaultProp);
      React.useEffect(() => {
        if (!isControlled && serializeToDevModeString(defaultValue) !== serializeToDevModeString(defaultProp)) {
          error([`A component is changing the default ${state} state of an uncontrolled ${name} after being initialized. To suppress this warning opt to use a controlled ${name}.`].join("\n"));
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

  // node_modules/@base-ui/utils/useIsoLayoutEffect.mjs
  var React4 = __toESM(require_react(), 1);
  var noop = () => {
  };
  var useIsoLayoutEffect = typeof document !== "undefined" ? React4.useLayoutEffect : noop;

  // node_modules/@base-ui/react/internals/useRenderElement.mjs
  var React7 = __toESM(require_react(), 1);

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
  var React6 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/reactVersion.mjs
  var React5 = __toESM(require_react(), 1);
  var majorVersion = parseInt(React5.version, 10);
  function isReactVersionAtLeast(reactVersionToCheck) {
    return majorVersion >= reactVersionToCheck;
  }

  // node_modules/@base-ui/utils/getReactElementRef.mjs
  function getReactElementRef(element) {
    if (!/* @__PURE__ */ React6.isValidElement(element)) {
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
      stateAttributesMapping: stateAttributesMapping2,
      enabled = true
    } = params;
    const className = enabled ? resolveClassName(classNameProp, state) : void 0;
    const style = enabled ? resolveStyle(styleProp, state) : void 0;
    const stateProps = enabled ? getStateAttributesProps(state, stateAttributesMapping2) : EMPTY_OBJECT;
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
    const unwrapped = React7.Children.toArray(render)[0];
    return /* @__PURE__ */ React7.isValidElement(unwrapped) ? unwrapped : render;
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
        if (!/* @__PURE__ */ React7.isValidElement(render)) {
          throw new Error(["Base UI: The `render` prop was provided an invalid React element as `React.isValidElement(render)` is `false`.", "A valid React element must be provided to the `render` prop because it is cloned with props to replace the default element.", "https://base-ui.com/r/invalid-render-prop"].join("\n"));
        }
      }
      return /* @__PURE__ */ React7.cloneElement(render, mergedProps);
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
    return /* @__PURE__ */ React7.createElement(Tag, props);
  }

  // node_modules/@base-ui/utils/useId.mjs
  var React8 = __toESM(require_react(), 1);
  var globalId = 0;
  function useGlobalId(idOverride, prefix = "mui") {
    const [defaultId, setDefaultId] = React8.useState(idOverride);
    const id = idOverride || defaultId;
    React8.useEffect(() => {
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

  // node_modules/@base-ui/react/collapsible/root/useCollapsibleRoot.mjs
  var React11 = __toESM(require_react(), 1);

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
  var React10 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/useOnMount.mjs
  var React9 = __toESM(require_react(), 1);
  function useOnMount(fn) {
    React9.useEffect(fn, EMPTY_ARRAY);
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
  function useTransitionStatus(open3, enableIdleState = false, deferEndingState = false, animateInitialOpen = false) {
    const [transitionStatus, setTransitionStatus] = React10.useState(open3 && enableIdleState ? "idle" : void 0);
    const [mounted, setMounted] = React10.useState(open3 && !animateInitialOpen);
    if (open3 && !mounted) {
      setMounted(true);
      setTransitionStatus("starting");
    }
    if (!open3 && mounted && transitionStatus !== "ending" && !deferEndingState) {
      setTransitionStatus("ending");
    }
    if (!open3 && !mounted && transitionStatus === "ending") {
      setTransitionStatus(void 0);
    }
    useIsoLayoutEffect(() => {
      if (!open3 && mounted && transitionStatus !== "ending" && deferEndingState) {
        const frame = AnimationFrame.request(() => {
          setTransitionStatus("ending");
        });
        return () => {
          AnimationFrame.cancel(frame);
        };
      }
      return void 0;
    }, [open3, mounted, transitionStatus, deferEndingState]);
    useIsoLayoutEffect(() => {
      if (!open3 || enableIdleState) {
        return void 0;
      }
      const frame = AnimationFrame.request(() => {
        setTransitionStatus(void 0);
      });
      return () => {
        AnimationFrame.cancel(frame);
      };
    }, [enableIdleState, open3]);
    useIsoLayoutEffect(() => {
      if (!open3 || !enableIdleState) {
        return void 0;
      }
      if (open3 && mounted && transitionStatus !== "idle") {
        setTransitionStatus("starting");
      }
      const frame = AnimationFrame.request(() => {
        setTransitionStatus("idle");
      });
      return () => {
        AnimationFrame.cancel(frame);
      };
    }, [enableIdleState, open3, mounted, transitionStatus]);
    return {
      mounted,
      setMounted,
      transitionStatus
    };
  }

  // node_modules/@base-ui/react/collapsible/root/useCollapsibleRoot.mjs
  function useCollapsibleRoot(parameters) {
    const {
      open: openParam,
      defaultOpen = false,
      onOpenChange,
      disabled: disabled2
    } = parameters;
    const [open3, setOpen] = useControlled({
      controlled: openParam,
      default: defaultOpen,
      name: "Collapsible",
      state: "open"
    });
    const {
      mounted,
      setMounted,
      transitionStatus
    } = useTransitionStatus(open3, true, true);
    const defaultPanelId = useBaseUiId();
    const [registeredPanelId, setPanelIdState] = React11.useState();
    const panelId = registeredPanelId === null ? void 0 : registeredPanelId ?? defaultPanelId;
    const handleTrigger = useStableCallback((event) => {
      const nextOpen = !open3;
      const eventDetails = createChangeEventDetails(reason_parts_exports.triggerPress, event.nativeEvent);
      onOpenChange(nextOpen, eventDetails);
      if (eventDetails.isCanceled) {
        return;
      }
      setOpen(nextOpen);
    });
    return React11.useMemo(() => ({
      defaultPanelId,
      disabled: disabled2,
      handleTrigger,
      mounted,
      open: open3,
      panelId,
      setMounted,
      setOpen,
      setPanelIdState,
      transitionStatus
    }), [defaultPanelId, disabled2, handleTrigger, mounted, open3, panelId, setMounted, setOpen, setPanelIdState, transitionStatus]);
  }

  // node_modules/@base-ui/react/collapsible/root/CollapsibleRootContext.mjs
  var React12 = __toESM(require_react(), 1);
  var CollapsibleRootContext = /* @__PURE__ */ React12.createContext(void 0);
  if (true) CollapsibleRootContext.displayName = "CollapsibleRootContext";
  function useCollapsibleRootContext() {
    const context = React12.useContext(CollapsibleRootContext);
    if (context === void 0) {
      throw new Error(true ? "Base UI: CollapsibleRootContext is missing. Collapsible parts must be placed within <Collapsible.Root>." : formatErrorMessage_default(15));
    }
    return context;
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

  // node_modules/@base-ui/react/collapsible/panel/CollapsiblePanelDataAttributes.mjs
  var open = "data-open";
  var closed = "data-closed";
  var startingStyle2 = TransitionStatusDataAttributes_exports.startingStyle;
  var endingStyle2 = TransitionStatusDataAttributes_exports.endingStyle;

  // node_modules/@base-ui/react/collapsible/trigger/CollapsibleTriggerDataAttributes.mjs
  var panelOpen = "data-panel-open";

  // node_modules/@base-ui/react/utils/collapsibleOpenStateMapping.mjs
  var PANEL_OPEN_HOOK = {
    [open]: ""
  };
  var PANEL_CLOSED_HOOK = {
    [closed]: ""
  };
  var triggerOpenStateMapping = {
    open(value) {
      if (value) {
        return {
          [panelOpen]: ""
        };
      }
      return null;
    }
  };
  var collapsibleOpenStateMapping = {
    open(value) {
      if (value) {
        return PANEL_OPEN_HOOK;
      }
      return PANEL_CLOSED_HOOK;
    }
  };

  // node_modules/@base-ui/react/internals/use-button/useButton.mjs
  var React15 = __toESM(require_react(), 1);

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

  // node_modules/@base-ui/react/internals/composite/root/CompositeRootContext.mjs
  var React13 = __toESM(require_react(), 1);
  var CompositeRootContext = /* @__PURE__ */ React13.createContext(void 0);
  if (true) CompositeRootContext.displayName = "CompositeRootContext";
  function useCompositeRootContext(optional = false) {
    const context = React13.useContext(CompositeRootContext);
    if (context === void 0 && !optional) {
      throw new Error(true ? "Base UI: CompositeRootContext is missing. Composite parts must be placed within <Composite.Root>." : formatErrorMessage_default(16));
    }
    return context;
  }

  // node_modules/@base-ui/react/utils/useFocusableWhenDisabled.mjs
  var React14 = __toESM(require_react(), 1);
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
    const props = React14.useMemo(() => {
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
    const elementRef = React15.useRef(null);
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
      React15.useEffect(() => {
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
    const updateDisabled = React15.useCallback(() => {
      const element = elementRef.current;
      if (!isButtonElement(element)) {
        return;
      }
      if (isCompositeItem && disabled2 && focusableWhenDisabledProps.disabled === void 0 && element.disabled) {
        element.disabled = false;
      }
    }, [disabled2, focusableWhenDisabledProps.disabled, isCompositeItem]);
    useIsoLayoutEffect(updateDisabled, [updateDisabled]);
    const getButtonProps = React15.useCallback((externalProps = {}) => {
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

  // node_modules/@base-ui/react/collapsible/panel/useCollapsiblePanel.mjs
  var React17 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/addEventListener.mjs
  function addEventListener(target, type, listener, options) {
    target.addEventListener(type, listener, options);
    return () => {
      target.removeEventListener(type, listener, options);
    };
  }

  // node_modules/@base-ui/utils/useValueAsRef.mjs
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

  // node_modules/@base-ui/react/internals/useOpenChangeComplete.mjs
  var React16 = __toESM(require_react(), 1);

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
      open: open3,
      ref,
      batch = false,
      onComplete: onCompleteParam
    } = parameters;
    const onComplete = useStableCallback(onCompleteParam);
    const runOnceAnimationsFinish = useAnimationsFinished(ref, open3, batch);
    React16.useEffect(() => {
      if (!enabled) {
        return void 0;
      }
      const abortController = new AbortController();
      runOnceAnimationsFinish(onComplete, abortController.signal);
      return () => {
        abortController.abort();
      };
    }, [enabled, open3, onComplete, runOnceAnimationsFinish]);
  }

  // node_modules/@base-ui/react/collapsible/panel/useCollapsiblePanel.mjs
  var EMPTY_DIMENSIONS = {
    height: void 0,
    width: void 0
  };
  function useCollapsiblePanel(parameters) {
    const {
      externalRef,
      hiddenUntilFound,
      id: idParam,
      keepMounted,
      mounted,
      onOpenChange,
      open: open3,
      setMounted,
      setOpen,
      transitionStatus
    } = parameters;
    const panelRef = React17.useRef(null);
    const animationTypeRef = React17.useRef(null);
    const [dimensions, setDimensionsUnwrapped] = React17.useState(EMPTY_DIMENSIONS);
    const lastMeasuredDimensionsRef = React17.useRef(EMPTY_DIMENSIONS);
    const shouldSkipNextOpenRef = React17.useRef(false);
    const shouldPreventMountAnimationRef = React17.useRef(open3);
    const shouldPreventActivityResumeAnimationRef = React17.useRef(false);
    const [forcePanelIdle, setForcePanelIdle] = React17.useState(false);
    const pendingTemporaryStyleRestoreRef = React17.useRef(null);
    const mergedPanelRef = useMergedRefs(externalRef, panelRef);
    const latestOpenRef = useValueAsRef(open3);
    const runOnceCloseAnimationsFinish = useAnimationsFinished(panelRef);
    const hidden = !open3 && !mounted;
    const panelTransitionStatus = forcePanelIdle ? "idle" : transitionStatus;
    const shouldPreventOpenAnimation = open3 && // These 2 refs are safe to read in render, they are only written from committed
    // layout/effect paths and gate one-shot motion suppression for the next open
    // lifecycle. They intentionally expose the last committed motion snapshot.
    (shouldPreventMountAnimationRef.current || shouldPreventActivityResumeAnimationRef.current);
    const renderedDimensions = !open3 && mounted && // These 2 refs are also safe to read in render, both hold the last committed
    // animation mode and measurement. This fallback only restores a previously
    // measured pixel size after the live dimensions state has been reset back to `auto`.
    animationTypeRef.current === "css-animation" && dimensions.height === void 0 && dimensions.width === void 0 ? lastMeasuredDimensionsRef.current : dimensions;
    const shouldPersistHiddenTransitionStyles = hiddenUntilFound && hidden && animationTypeRef.current !== "css-animation";
    const setDimensions = useStableCallback((nextDimensions, shouldCacheMeasurement = true) => {
      if (shouldCacheMeasurement) {
        lastMeasuredDimensionsRef.current = nextDimensions;
      }
      setDimensionsUnwrapped(nextDimensions);
    });
    const restorePendingTemporaryStyle = useStableCallback(() => {
      pendingTemporaryStyleRestoreRef.current?.();
      pendingTemporaryStyleRestoreRef.current = null;
    });
    const setPendingTemporaryStyleRestore = useStableCallback((restore) => {
      restorePendingTemporaryStyle();
      pendingTemporaryStyleRestoreRef.current = () => {
        pendingTemporaryStyleRestoreRef.current = null;
        restore();
      };
    });
    const markActivityResumeAnimationSuppressed = useStableCallback(() => {
      if (open3 && mounted && animationTypeRef.current === "css-animation") {
        shouldPreventActivityResumeAnimationRef.current = true;
      }
    });
    useIsoLayoutEffect(() => {
      if (!forcePanelIdle || transitionStatus === "starting") {
        return;
      }
      setForcePanelIdle(false);
    }, [forcePanelIdle, transitionStatus]);
    React17.useEffect(() => {
      return () => {
        markActivityResumeAnimationSuppressed();
        restorePendingTemporaryStyle();
      };
    }, [markActivityResumeAnimationSuppressed, restorePendingTemporaryStyle]);
    useIsoLayoutEffect(() => {
      const panel = panelRef.current;
      if (!panel) {
        return void 0;
      }
      if (!open3 && pendingTemporaryStyleRestoreRef.current) {
        restorePendingTemporaryStyle();
      }
      const animationType = getAnimationType(panel, shouldPreventOpenAnimation);
      animationTypeRef.current = animationType;
      if (open3 && transitionStatus === "idle" && shouldPreventMountAnimationRef.current && animationType === "css-animation") {
        lastMeasuredDimensionsRef.current = getDimensions(panel);
        return void 0;
      }
      if (open3 && transitionStatus === "starting") {
        const skipNextOpen = shouldSkipNextOpenRef.current;
        shouldSkipNextOpenRef.current = false;
        if (animationType === "none") {
          setDimensions(getDimensions(panel));
          setForcePanelIdle(true);
          return void 0;
        }
        if (animationType === "css-transition") {
          const restoreLayoutStyles = resetLayoutStyles(panel);
          setDimensions(getDimensions(panel));
          if (!skipNextOpen) {
            return restoreLayoutStyles;
          }
          const restoreTransitionDuration = setTemporaryStyle(panel, "transition-duration", "0s");
          setPendingTemporaryStyleRestore(restoreTransitionDuration);
          setForcePanelIdle(true);
          return restoreLayoutStyles;
        }
        setDimensions(getDimensions(panel));
        const restoreAnimationName = setTemporaryStyle(panel, "animation-name", "none");
        if (!skipNextOpen) {
          restoreAnimationName();
          return void 0;
        }
        const restoreAnimationDuration = setTemporaryStyle(panel, "animation-duration", "0s");
        restoreAnimationName();
        setPendingTemporaryStyleRestore(restoreAnimationDuration);
        setForcePanelIdle(true);
        return void 0;
      }
      if (!open3 && mounted && (transitionStatus === "idle" || transitionStatus === "starting")) {
        shouldPreventMountAnimationRef.current = false;
        shouldPreventActivityResumeAnimationRef.current = false;
        if (animationType === "none") {
          setDimensions(EMPTY_DIMENSIONS, false);
          setMounted(false);
          return void 0;
        }
        setDimensions(getDimensions(panel));
        return void 0;
      }
      if (transitionStatus !== "ending") {
        return void 0;
      }
      if (animationType === "none") {
        setMounted(false);
        return void 0;
      }
      const nextDimensions = getDimensions(panel);
      const hasMeasuredSize = nextDimensions.height > 0 || nextDimensions.width > 0;
      if (!hasMeasuredSize) {
        setMounted(false);
        return void 0;
      }
      setDimensions(nextDimensions);
      if (animationType === "css-animation") {
        const restoreAnimationName = setTemporaryStyle(panel, "animation-name", "none");
        restoreAnimationName();
      }
      return void 0;
    }, [mounted, open3, restorePendingTemporaryStyle, setDimensions, setMounted, setPendingTemporaryStyleRestore, shouldPreventOpenAnimation, transitionStatus]);
    useOpenChangeComplete({
      enabled: open3 && mounted && panelTransitionStatus === "idle",
      open: true,
      ref: panelRef,
      onComplete() {
        if (!open3) {
          return;
        }
        setDimensions(EMPTY_DIMENSIONS, false);
      }
    });
    React17.useEffect(() => {
      if (open3 || !mounted || panelTransitionStatus !== "ending") {
        return void 0;
      }
      const panel = panelRef.current;
      if (!panel) {
        return void 0;
      }
      const abortController = new AbortController();
      let endingStyleFrame = -1;
      function handleComplete() {
        if (latestOpenRef.current) {
          return;
        }
        setMounted(false);
        setDimensions(EMPTY_DIMENSIONS, false);
      }
      endingStyleFrame = AnimationFrame.request(() => {
        runOnceCloseAnimationsFinish(handleComplete, abortController.signal);
      });
      return () => {
        AnimationFrame.cancel(endingStyleFrame);
        abortController.abort();
      };
    }, [latestOpenRef, mounted, open3, panelTransitionStatus, runOnceCloseAnimationsFinish, setDimensions, setMounted]);
    useIsoLayoutEffect(() => {
      const panel = panelRef.current;
      if (!panel || !hiddenUntilFound || !hidden) {
        return;
      }
      panel.setAttribute("hidden", "until-found");
    }, [hidden, hiddenUntilFound]);
    React17.useEffect(function registerBeforeMatchListener() {
      const panel = panelRef.current;
      if (!panel) {
        return void 0;
      }
      function handleBeforeMatch(event) {
        const eventDetails = createChangeEventDetails(reason_parts_exports.none, event);
        onOpenChange(true, eventDetails);
        if (eventDetails.isCanceled) {
          return;
        }
        shouldSkipNextOpenRef.current = true;
        setOpen(true);
      }
      return addEventListener(panel, "beforematch", handleBeforeMatch);
    }, [onOpenChange, setOpen]);
    const shouldRender = keepMounted || hiddenUntilFound || mounted || open3;
    return {
      height: renderedDimensions.height,
      props: {
        ...shouldPersistHiddenTransitionStyles ? {
          [startingStyle2]: ""
        } : void 0,
        hidden,
        id: idParam
      },
      ref: mergedPanelRef,
      shouldPreventOpenAnimation,
      shouldRender,
      transitionStatus: panelTransitionStatus,
      width: renderedDimensions.width
    };
  }
  function getDimensions(element) {
    return {
      height: element.scrollHeight,
      width: element.scrollWidth
    };
  }
  function getAnimationType(element, hasSuppressedMountAnimation) {
    const panelStyles = getWindow(element).getComputedStyle(element);
    const hasAnimation = (panelStyles.animationName.split(",").map((name) => name.trim()).some((name) => name !== "" && name !== "none") || hasSuppressedMountAnimation) && hasNonZeroDuration(panelStyles.animationDuration);
    const hasTransition = hasNonZeroDuration(panelStyles.transitionDuration);
    if (hasAnimation && hasTransition) {
      if (true) {
        warn("CSS transitions and CSS animations both detected on Collapsible or Accordion panel.", "Only one of either animation type should be used.");
      }
      return "css-transition";
    }
    if (hasTransition) {
      return "css-transition";
    }
    if (hasAnimation) {
      return "css-animation";
    }
    return "none";
  }
  function hasNonZeroDuration(value) {
    return value.split(",").map((part) => part.trim()).some((part) => part !== "" && Number.parseFloat(part) > 0);
  }
  function setTemporaryStyle(element, property, value) {
    const previousValue = element.style.getPropertyValue(property);
    const previousPriority = element.style.getPropertyPriority(property);
    element.style.setProperty(property, value);
    return () => {
      if (previousValue === "") {
        element.style.removeProperty(property);
        return;
      }
      element.style.setProperty(property, previousValue, previousPriority);
    };
  }
  function resetLayoutStyles(element) {
    const originalLayoutStyles = {
      "justify-content": element.style.justifyContent,
      "align-items": element.style.alignItems,
      "align-content": element.style.alignContent,
      "justify-items": element.style.justifyItems
    };
    Object.keys(originalLayoutStyles).forEach((key) => {
      element.style.setProperty(key, "initial", "important");
    });
    function restoreLayoutStyles() {
      Object.entries(originalLayoutStyles).forEach(([key, value]) => {
        if (value === "") {
          element.style.removeProperty(key);
          return;
        }
        element.style.setProperty(key, value);
      });
    }
    const frame = AnimationFrame.request(restoreLayoutStyles);
    return () => {
      AnimationFrame.cancel(frame);
      restoreLayoutStyles();
    };
  }

  // node_modules/@base-ui/utils/platform/parts.mjs
  var parts_exports = {};
  __export(parts_exports, {
    engine: () => engine_exports,
    env: () => env_exports,
    mediaQuery: () => media_query_exports,
    os: () => os_exports,
    screenReader: () => screen_reader_exports
  });

  // node_modules/@base-ui/utils/platform/os.mjs
  var os_exports = {};
  __export(os_exports, {
    android: () => android,
    apple: () => apple,
    ios: () => ios,
    linux: () => linux,
    mac: () => mac,
    windows: () => windows
  });

  // node_modules/@base-ui/utils/platform/shared.mjs
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

  // node_modules/@base-ui/utils/platform/os.mjs
  var ios = /^i(os$|p)/.test(lowerPlatform) || lowerPlatform === "macintel" && maxTouchPoints > 1;
  var ANDROID_STRING = "android";
  var android = lowerPlatform === ANDROID_STRING || lowerUserAgent.includes(ANDROID_STRING);
  var mac = !ios && lowerPlatform.startsWith("mac");
  var windows = lowerPlatform.startsWith("win");
  var linux = !android && /^(linux|chrome os)/.test(lowerPlatform);
  var apple = mac || ios;

  // node_modules/@base-ui/utils/platform/engine.mjs
  var engine_exports = {};
  __export(engine_exports, {
    blink: () => blink,
    gecko: () => gecko,
    webkit: () => webkit
  });
  var webkit = typeof CSS !== "undefined" && !!CSS.supports?.("-webkit-backdrop-filter:none");
  var gecko = !webkit && lowerUserAgent.includes("firefox");
  var blink = !webkit && lowerUserAgent.includes("chrom");

  // node_modules/@base-ui/utils/platform/screen-reader.mjs
  var screen_reader_exports = {};
  __export(screen_reader_exports, {
    voiceOver: () => voiceOver
  });
  var voiceOver = apple;

  // node_modules/@base-ui/utils/platform/env.mjs
  var env_exports = {};
  __export(env_exports, {
    jsdom: () => jsdom
  });
  var jsdom = /jsdom|happydom/.test(lowerUserAgent);

  // node_modules/@base-ui/utils/platform/media-query.mjs
  var media_query_exports = {};
  __export(media_query_exports, {
    iOS: () => iOS
  });
  var iOS = "@supports (-webkit-touch-callout: none)";

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

  // node_modules/@base-ui/react/floating-ui-react/components/FloatingDelayGroup.mjs
  var React18 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/floating-ui-react/utils/event.mjs
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

  // node_modules/@base-ui/react/floating-ui-react/utils/constants.mjs
  var FOCUSABLE_ATTRIBUTE = "data-base-ui-focusable";
  var TYPEABLE_SELECTOR = "input:not([type='hidden']):not([disabled]),[contenteditable]:not([contenteditable='false']),textarea:not([disabled])";

  // node_modules/@base-ui/react/utils/CommonPopupDataAttributes.mjs
  var open2 = "data-open";
  var closed2 = "data-closed";
  var anchorHidden = "data-anchor-hidden";

  // node_modules/@base-ui/react/utils/CommonTriggerDataAttributes.mjs
  var CommonTriggerDataAttributes_exports = {};
  __export(CommonTriggerDataAttributes_exports, {
    popupOpen: () => popupOpen,
    pressed: () => pressed
  });
  var popupOpen = "data-popup-open";
  var pressed = "data-pressed";

  // node_modules/@base-ui/react/utils/popupStateMapping.mjs
  var TRIGGER_HOOK = {
    [popupOpen]: ""
  };
  var PRESSABLE_TRIGGER_HOOK = {
    [popupOpen]: "",
    [pressed]: ""
  };
  var POPUP_OPEN_HOOK = {
    [open2]: ""
  };
  var POPUP_CLOSED_HOOK = {
    [closed2]: ""
  };
  var ANCHOR_HIDDEN_HOOK = {
    [anchorHidden]: ""
  };
  var triggerOpenStateMapping2 = {
    open(value) {
      if (value) {
        return TRIGGER_HOOK;
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

  // node_modules/@base-ui/react/tooltip/trigger/TooltipTriggerDataAttributes.mjs
  var popupOpen2 = CommonTriggerDataAttributes_exports.popupOpen;
  var triggerDisabled = "data-trigger-disabled";

  // node_modules/@base-ui/react/floating-ui-react/utils/element.mjs
  function isTargetInsideEnabledTrigger(target, triggerElements) {
    if (!isElement(target)) {
      return false;
    }
    const targetElement = target;
    if (triggerElements.hasElement(targetElement)) {
      return !targetElement.hasAttribute(triggerDisabled);
    }
    for (const [, trigger] of triggerElements.entries()) {
      if (contains(trigger, targetElement)) {
        return !trigger.hasAttribute(triggerDisabled);
      }
    }
    return false;
  }
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
  function isInteractiveElement(element) {
    return element?.closest(`button,a[href],[role="button"],select,[tabindex]:not([tabindex="-1"]),${TYPEABLE_SELECTOR}`) != null;
  }
  function matchesFocusVisible(element) {
    if (!element || parts_exports.env.jsdom) {
      return true;
    }
    try {
      return element.matches(":focus-visible");
    } catch (_e) {
      return true;
    }
  }

  // node_modules/@base-ui/react/floating-ui-react/hooks/useHoverShared.mjs
  function resolveValue(value, pointerType) {
    if (pointerType != null && !isMouseLikePointerType(pointerType)) {
      return 0;
    }
    if (typeof value === "function") {
      return value();
    }
    return value;
  }
  function getDelay(value, prop, pointerType) {
    const result = resolveValue(value, pointerType);
    if (typeof result === "number") {
      return result;
    }
    return result?.[prop];
  }
  function getRestMs(value) {
    if (typeof value === "function") {
      return value();
    }
    return value;
  }
  function isClickLikeOpenEvent(openEventType, interactedInside) {
    return interactedInside || openEventType === "click" || openEventType === "mousedown";
  }
  function isHoverOpenEvent(openEventType) {
    return openEventType?.includes("mouse") && openEventType !== "mousedown";
  }

  // node_modules/@base-ui/react/floating-ui-react/components/FloatingDelayGroup.mjs
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
  var FloatingDelayGroupContext = /* @__PURE__ */ React18.createContext({
    hasProvider: false,
    timeoutMs: 0,
    delayRef: {
      current: 0
    },
    initialDelayRef: {
      current: 0
    },
    timeout: new Timeout(),
    currentIdRef: {
      current: null
    },
    currentContextRef: {
      current: null
    }
  });
  if (true) FloatingDelayGroupContext.displayName = "FloatingDelayGroupContext";
  function resetDelayRef(delayRef, initialDelayRef) {
    delayRef.current = initialDelayRef.current;
  }
  function FloatingDelayGroup(props) {
    const {
      children,
      delay,
      timeoutMs = 0
    } = props;
    const delayRef = React18.useRef(delay);
    const initialDelayRef = React18.useRef(delay);
    const currentIdRef = React18.useRef(null);
    const currentContextRef = React18.useRef(null);
    const timeout = useTimeout();
    useIsoLayoutEffect(() => {
      initialDelayRef.current = delay;
      if (!currentIdRef.current) {
        delayRef.current = delay;
        return;
      }
      delayRef.current = {
        open: getDelay(delayRef.current, "open"),
        close: getDelay(delay, "close")
      };
    }, [delay, currentIdRef, delayRef, initialDelayRef]);
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(FloatingDelayGroupContext.Provider, {
      value: React18.useMemo(() => ({
        hasProvider: true,
        delayRef,
        initialDelayRef,
        currentIdRef,
        timeoutMs,
        currentContextRef,
        timeout
      }), [timeoutMs, timeout]),
      children
    });
  }
  function useDelayGroup(context, options = {
    open: false
  }) {
    const {
      open: open3
    } = options;
    const store2 = "rootStore" in context ? context.rootStore : context;
    const floatingId = store2.useState("floatingId");
    const groupContext = React18.useContext(FloatingDelayGroupContext);
    const {
      currentIdRef,
      delayRef,
      timeoutMs,
      initialDelayRef,
      currentContextRef,
      hasProvider,
      timeout
    } = groupContext;
    const [isInstantPhase, setIsInstantPhase] = React18.useState(false);
    const openRef = React18.useRef(open3);
    useIsoLayoutEffect(() => {
      openRef.current = open3;
    }, [open3]);
    useIsoLayoutEffect(() => {
      function unset() {
        currentContextRef.current?.setIsInstantPhase(false);
        currentIdRef.current = null;
        currentContextRef.current = null;
        delayRef.current = initialDelayRef.current;
        timeout.clear();
      }
      if (!currentIdRef.current) {
        return void 0;
      }
      if (!open3 && currentIdRef.current === floatingId) {
        setIsInstantPhase(false);
        if (timeoutMs) {
          const closingId = floatingId;
          timeout.start(timeoutMs, () => {
            if (store2.select("open") || currentIdRef.current && currentIdRef.current !== closingId) {
              return;
            }
            unset();
          });
          return () => {
            if (openRef.current || currentIdRef.current !== closingId) {
              timeout.clear();
            }
          };
        }
        unset();
      }
      return void 0;
    }, [open3, floatingId, currentIdRef, delayRef, timeoutMs, initialDelayRef, currentContextRef, timeout, store2]);
    useIsoLayoutEffect(() => {
      if (!open3) {
        return;
      }
      const prevContext = currentContextRef.current;
      const prevId = currentIdRef.current;
      timeout.clear();
      currentContextRef.current = {
        onOpenChange: store2.setOpen,
        setIsInstantPhase
      };
      currentIdRef.current = floatingId;
      delayRef.current = {
        open: 0,
        close: getDelay(initialDelayRef.current, "close")
      };
      if (prevId !== null && prevId !== floatingId) {
        setIsInstantPhase(true);
        prevContext?.setIsInstantPhase(true);
        prevContext?.onOpenChange(false, createChangeEventDetails(reason_parts_exports.none));
      } else {
        setIsInstantPhase(false);
        prevContext?.setIsInstantPhase(false);
      }
    }, [open3, floatingId, store2, currentIdRef, delayRef, initialDelayRef, currentContextRef, timeout]);
    useIsoLayoutEffect(() => {
      return () => {
        if (currentIdRef.current === floatingId) {
          currentContextRef.current = null;
          if (!openRef.current) {
            return;
          }
          currentIdRef.current = null;
          resetDelayRef(delayRef, initialDelayRef);
          timeout.clear();
        }
      };
    }, [currentContextRef, currentIdRef, delayRef, floatingId, initialDelayRef, timeout]);
    return React18.useMemo(() => ({
      activeIdRef: currentIdRef,
      hasProvider,
      delayRef,
      isInstantPhase
    }), [currentIdRef, hasProvider, delayRef, isInstantPhase]);
  }

  // node_modules/@base-ui/utils/mergeCleanups.mjs
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

  // node_modules/@base-ui/react/utils/FocusGuard.mjs
  var React19 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/visuallyHidden.mjs
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
    top: 0,
    left: 0
  };
  var visuallyHiddenInput = {
    ...visuallyHiddenBase,
    position: "absolute"
  };

  // node_modules/@base-ui/react/utils/FocusGuard.mjs
  var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
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
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)("span", {
      ...props,
      ref,
      style: visuallyHidden,
      "aria-hidden": role ? void 0 : true,
      ...restProps,
      "data-base-ui-focus-guard": ""
    });
  });
  if (true) FocusGuard.displayName = "FocusGuard";

  // node_modules/@floating-ui/utils/dist/floating-ui.utils.mjs
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

  // node_modules/@base-ui/react/floating-ui-react/utils/composite.mjs
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

  // node_modules/@base-ui/react/floating-ui-react/utils/tabbable.mjs
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
    const index2 = list.indexOf(active);
    const nextIndex = index2 === -1 ? dir === 1 ? 0 : len - 1 : index2 + dir;
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

  // node_modules/@base-ui/react/floating-ui-react/utils/nodes.mjs
  function getNodeChildren(nodes, id, onlyOpenChildren = true) {
    const directChildren = nodes.filter((node) => node.parentId === id);
    return directChildren.flatMap((child) => [...!onlyOpenChildren || child.context?.open ? [child] : [], ...getNodeChildren(nodes, child.id, onlyOpenChildren)]);
  }

  // node_modules/@base-ui/react/floating-ui-react/utils/createAttribute.mjs
  function createAttribute(name) {
    return `data-base-ui-${name}`;
  }

  // node_modules/@base-ui/react/floating-ui-react/components/FloatingPortal.mjs
  var React20 = __toESM(require_react(), 1);
  var ReactDOM2 = __toESM(require_react_dom(), 1);

  // node_modules/@base-ui/react/internals/constants.mjs
  var DISABLED_TRANSITIONS_STYLE = {
    style: {
      transition: "none"
    }
  };
  var BASE_UI_SWIPE_IGNORE_ATTRIBUTE = "data-base-ui-swipe-ignore";
  var LEGACY_SWIPE_IGNORE_ATTRIBUTE = "data-swipe-ignore";
  var BASE_UI_SWIPE_IGNORE_SELECTOR = `[${BASE_UI_SWIPE_IGNORE_ATTRIBUTE}]`;
  var LEGACY_SWIPE_IGNORE_SELECTOR = `[${LEGACY_SWIPE_IGNORE_ATTRIBUTE}]`;
  var POPUP_COLLISION_AVOIDANCE = {
    fallbackAxisSide: "end"
  };
  var ownerVisuallyHidden = {
    clipPath: "inset(50%)",
    position: "fixed",
    top: 0,
    left: 0
  };

  // node_modules/@base-ui/react/floating-ui-react/components/FloatingPortal.mjs
  var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
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
    const open3 = focusManagerState?.open;
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
      if (!portalNode || open3 !== true || !focusInsideDisabledRef.current) {
        return;
      }
      enableFocusInside(portalNode);
      focusInsideDisabledRef.current = false;
    }, [open3, portalNode]);
    const portalContextValue = React20.useMemo(() => ({
      beforeOutsideRef,
      afterOutsideRef,
      beforeInsideRef,
      afterInsideRef,
      portalNode,
      setFocusManagerState
    }), [portalNode]);
    return /* @__PURE__ */ (0, import_jsx_runtime3.jsxs)(React20.Fragment, {
      children: [portalSubtree, /* @__PURE__ */ (0, import_jsx_runtime3.jsxs)(PortalContext.Provider, {
        value: portalContextValue,
        children: [shouldRenderGuards && portalNode && /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(FocusGuard, {
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
        }), shouldRenderGuards && portalNode && /* @__PURE__ */ (0, import_jsx_runtime3.jsx)("span", {
          role: portalOwnerRole,
          "aria-owns": portalNodeId,
          style: ownerVisuallyHidden
        }), portalNode && /* @__PURE__ */ ReactDOM2.createPortal(children, portalNode), shouldRenderGuards && portalNode && /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(FocusGuard, {
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

  // node_modules/@base-ui/react/floating-ui-react/components/FloatingTree.mjs
  var React21 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/floating-ui-react/utils/createEventEmitter.mjs
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

  // node_modules/@base-ui/react/floating-ui-react/components/FloatingTree.mjs
  var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
  var FloatingNodeContext = /* @__PURE__ */ React21.createContext(null);
  if (true) FloatingNodeContext.displayName = "FloatingNodeContext";
  var FloatingTreeContext = /* @__PURE__ */ React21.createContext(null);
  if (true) FloatingTreeContext.displayName = "FloatingTreeContext";
  var useFloatingParentNodeId = () => React21.useContext(FloatingNodeContext)?.id || null;
  var useFloatingTree = (externalTree) => {
    const contextTree = React21.useContext(FloatingTreeContext);
    return externalTree ?? contextTree;
  };

  // node_modules/@base-ui/react/floating-ui-react/hooks/useClientPoint.mjs
  var React22 = __toESM(require_react(), 1);
  function createVirtualElement(domElement, data) {
    let offsetX = null;
    let offsetY = null;
    let isAutoUpdateEvent = false;
    return {
      contextElement: domElement || void 0,
      getBoundingClientRect() {
        const domRect = domElement?.getBoundingClientRect() || {
          width: 0,
          height: 0,
          x: 0,
          y: 0
        };
        const isXAxis = data.axis === "x" || data.axis === "both";
        const isYAxis = data.axis === "y" || data.axis === "both";
        const canTrackCursorOnAutoUpdate = ["mouseenter", "mousemove"].includes(data.dataRef.current.openEvent?.type || "") && data.pointerType !== "touch";
        let width = domRect.width;
        let height = domRect.height;
        let x = domRect.x;
        let y = domRect.y;
        if (offsetX == null && data.x && isXAxis) {
          offsetX = domRect.x - data.x;
        }
        if (offsetY == null && data.y && isYAxis) {
          offsetY = domRect.y - data.y;
        }
        x -= offsetX || 0;
        y -= offsetY || 0;
        width = 0;
        height = 0;
        if (!isAutoUpdateEvent || canTrackCursorOnAutoUpdate) {
          width = data.axis === "y" ? domRect.width : 0;
          height = data.axis === "x" ? domRect.height : 0;
          x = isXAxis && data.x != null ? data.x : x;
          y = isYAxis && data.y != null ? data.y : y;
        } else if (isAutoUpdateEvent && !canTrackCursorOnAutoUpdate) {
          height = data.axis === "x" ? domRect.height : height;
          width = data.axis === "y" ? domRect.width : width;
        }
        isAutoUpdateEvent = true;
        return {
          width,
          height,
          x,
          y,
          top: y,
          right: x + width,
          bottom: y + height,
          left: x
        };
      }
    };
  }
  function isMouseBasedEvent(event) {
    return event != null && event.clientX != null;
  }
  function useClientPoint(context, props = {}) {
    const {
      enabled = true,
      axis = "both"
    } = props;
    const store2 = "rootStore" in context ? context.rootStore : context;
    const open3 = store2.useState("open");
    const floating = store2.useState("floatingElement");
    const domReference = store2.useState("domReferenceElement");
    const dataRef = store2.context.dataRef;
    const initialRef = React22.useRef(false);
    const cleanupListenerRef = React22.useRef(null);
    const [pointerType, setPointerType] = React22.useState();
    const [reactive, setReactive] = React22.useState([]);
    const resetReference = useStableCallback((reference2) => {
      store2.set("positionReference", reference2);
    });
    const setReference = useStableCallback((newX, newY, referenceElement) => {
      if (initialRef.current) {
        return;
      }
      if (dataRef.current.openEvent && !isMouseBasedEvent(dataRef.current.openEvent)) {
        return;
      }
      store2.set("positionReference", createVirtualElement(referenceElement ?? domReference, {
        x: newX,
        y: newY,
        axis,
        dataRef,
        pointerType
      }));
    });
    const handleReferenceEnterOrMove = useStableCallback((event) => {
      if (!open3) {
        setReference(event.clientX, event.clientY, event.currentTarget);
      } else if (!cleanupListenerRef.current) {
        setReference(event.clientX, event.clientY, event.currentTarget);
        setReactive([]);
      }
    });
    const openCheck = isMouseLikePointerType(pointerType) ? floating : open3;
    React22.useEffect(() => {
      if (!enabled) {
        resetReference(domReference);
        return void 0;
      }
      if (!openCheck) {
        return void 0;
      }
      function cleanupListener() {
        cleanupListenerRef.current?.();
        cleanupListenerRef.current = null;
      }
      const win = getWindow(floating);
      function handleMouseMove(event) {
        const target = getTarget(event);
        if (!contains(floating, target)) {
          setReference(event.clientX, event.clientY);
        } else {
          cleanupListener();
        }
      }
      if (!dataRef.current.openEvent || isMouseBasedEvent(dataRef.current.openEvent)) {
        cleanupListenerRef.current = addEventListener(win, "mousemove", handleMouseMove);
      } else {
        resetReference(domReference);
      }
      return cleanupListener;
    }, [openCheck, enabled, floating, dataRef, domReference, store2, setReference, resetReference, reactive]);
    React22.useEffect(() => () => {
      store2.set("positionReference", null);
    }, [store2]);
    React22.useEffect(() => {
      if (enabled && !floating) {
        initialRef.current = false;
      }
    }, [enabled, floating]);
    React22.useEffect(() => {
      if (!enabled && open3) {
        initialRef.current = true;
      }
    }, [enabled, open3]);
    const reference = React22.useMemo(() => {
      function setPointerTypeRef(event) {
        setPointerType(event.pointerType);
      }
      return {
        onPointerDown: setPointerTypeRef,
        onPointerEnter: setPointerTypeRef,
        onMouseMove: handleReferenceEnterOrMove,
        onMouseEnter: handleReferenceEnterOrMove
      };
    }, [handleReferenceEnterOrMove]);
    return React22.useMemo(() => enabled ? {
      reference,
      trigger: reference
    } : {}, [enabled, reference]);
  }

  // node_modules/@base-ui/react/floating-ui-react/hooks/useDismiss.mjs
  var React23 = __toESM(require_react(), 1);
  function alwaysFalse() {
    return false;
  }
  function normalizeProp(normalizable) {
    return {
      escapeKey: typeof normalizable === "boolean" ? normalizable : normalizable?.escapeKey ?? false,
      outsidePress: typeof normalizable === "boolean" ? normalizable : normalizable?.outsidePress ?? true
    };
  }
  function useDismiss(context, props = {}) {
    const {
      enabled = true,
      escapeKey: escapeKey2 = true,
      outsidePress: outsidePressProp = true,
      outsidePressEvent = "sloppy",
      referencePress = alwaysFalse,
      bubbles,
      externalTree
    } = props;
    const store2 = "rootStore" in context ? context.rootStore : context;
    const open3 = store2.useState("open");
    const floatingElement = store2.useState("floatingElement");
    const {
      dataRef,
      events
    } = store2.context;
    const tree = useFloatingTree(externalTree);
    const outsidePressFn = useStableCallback(typeof outsidePressProp === "function" ? outsidePressProp : () => false);
    const outsidePress2 = typeof outsidePressProp === "function" ? outsidePressFn : outsidePressProp;
    const outsidePressEnabled = outsidePress2 !== false;
    const getOutsidePressEventProp = useStableCallback(() => outsidePressEvent);
    const {
      escapeKey: escapeKeyBubbles,
      outsidePress: outsidePressBubbles
    } = normalizeProp(bubbles);
    const pressStartedInsideRef = React23.useRef(false);
    const pressStartPreventedRef = React23.useRef(false);
    const suppressNextOutsideClickRef = React23.useRef(false);
    const sawPressWhileOpenRef = React23.useRef(false);
    const isComposingRef = React23.useRef(false);
    const currentPointerTypeRef = React23.useRef("");
    const touchStateRef = React23.useRef(null);
    const cancelDismissOnEndTimeout = useTimeout();
    const clearInsideReactTreeTimeout = useTimeout();
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
      return isEventTargetWithin(event, store2.select("floatingElement")) || isEventTargetWithin(event, store2.select("domReferenceElement"));
    });
    const closeOnReferencePress = useStableCallback((event) => {
      if (!referencePress()) {
        return;
      }
      store2.setOpen(false, createChangeEventDetails(reason_parts_exports.triggerPress, event.nativeEvent));
    });
    const closeOnEscapeKeyDown = useStableCallback((event) => {
      if (!open3 || !enabled || !escapeKey2 || event.key !== "Escape") {
        return;
      }
      if (isComposingRef.current) {
        return;
      }
      if (!escapeKeyBubbles && hasBlockingChild("__escapeKeyBubbles")) {
        return;
      }
      const native = isReactEvent(event) ? event.nativeEvent : event;
      const eventDetails = createChangeEventDetails(reason_parts_exports.escapeKey, native);
      store2.setOpen(false, eventDetails);
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
      if (!open3 || !enabled || event.button !== 0) {
        return;
      }
      const target = getTarget(event.nativeEvent);
      if (!contains(store2.select("floatingElement"), target)) {
        return;
      }
      if (!pressStartedInsideRef.current) {
        pressStartedInsideRef.current = true;
        pressStartPreventedRef.current = false;
      }
    });
    const markInsidePressStartPrevented = useStableCallback((event) => {
      if (!open3 || !enabled) {
        return;
      }
      if (!(event.defaultPrevented || event.nativeEvent.defaultPrevented)) {
        return;
      }
      if (pressStartedInsideRef.current) {
        pressStartPreventedRef.current = true;
      }
    });
    React23.useEffect(() => {
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
    React23.useEffect(() => {
      if (!open3 || !enabled) {
        if (!open3) {
          sawPressWhileOpenRef.current = false;
        }
        return clearInsideReactTree;
      }
      dataRef.current.__escapeKeyBubbles = escapeKeyBubbles;
      dataRef.current.__outsidePressBubbles = outsidePressBubbles;
      const compositionTimeout = new Timeout();
      const preventedPressSuppressionTimeout = new Timeout();
      const doc = ownerDocument(floatingElement);
      function handleCompositionStart() {
        compositionTimeout.clear();
        isComposingRef.current = true;
      }
      function handleCompositionEnd() {
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
        const markers = Array.from((isShadowRoot(targetRoot) ? targetRoot : ownerDocument(store2.select("floatingElement"))).querySelectorAll(inertSelector));
        const triggers = store2.context.triggerElements;
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
        !contains(target, store2.select("floatingElement")) && // If the target root element contains none of the markers, then the
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
          const isRTL4 = style.direction === "rtl";
          const pressedVerticalScrollbar = canScrollY && (isRTL4 ? event.offsetX <= target.offsetWidth - target.clientWidth : event.offsetX > target.clientWidth);
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
        store2.setOpen(false, createChangeEventDetails(reason_parts_exports.outsidePress, event));
        clearInsideReactTree();
      }
      function handlePointerDown(event) {
        if (getOutsidePressEvent() !== "sloppy" || event.pointerType === "touch" || !store2.select("open") || !enabled || isEventWithinOwnElements(event)) {
          return;
        }
        closeOnPressOutside(event);
      }
      function handleTouchStart(event) {
        if (getOutsidePressEvent() !== "sloppy" || !store2.select("open") || !enabled || isEventWithinOwnElements(event)) {
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
        compositionTimeout.clear();
        preventedPressSuppressionTimeout.clear();
        resetPressStartState();
        suppressNextOutsideClickRef.current = false;
        clearInsideReactTree();
      };
    }, [dataRef, floatingElement, escapeKey2, outsidePressEnabled, outsidePress2, open3, enabled, escapeKeyBubbles, outsidePressBubbles, closeOnEscapeKeyDown, clearInsideReactTree, getOutsidePressEventProp, hasBlockingChild, isEventWithinOwnElements, tree, store2, cancelDismissOnEndTimeout]);
    const reference = React23.useMemo(() => ({
      onKeyDown: closeOnEscapeKeyDown,
      onPointerDown: closeOnReferencePress,
      onClick: closeOnReferencePress
    }), [closeOnEscapeKeyDown, closeOnReferencePress]);
    const floating = React23.useMemo(() => ({
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
    return React23.useMemo(() => enabled ? {
      reference,
      floating,
      trigger: reference
    } : {}, [enabled, reference, floating]);
  }

  // node_modules/@base-ui/react/floating-ui-react/hooks/useFloating.mjs
  var React31 = __toESM(require_react(), 1);

  // node_modules/@floating-ui/core/dist/floating-ui.core.mjs
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
        name,
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
      middlewareData[name] = {
        ...middlewareData[name],
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

  // node_modules/@floating-ui/dom/dist/floating-ui.dom.mjs
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
  function getDimensions2(element) {
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
    getDimensions: getDimensions2,
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

  // node_modules/@floating-ui/react-dom/dist/floating-ui.react-dom.mjs
  var React24 = __toESM(require_react(), 1);
  var import_react2 = __toESM(require_react(), 1);
  var ReactDOM3 = __toESM(require_react_dom(), 1);
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
    const ref = React24.useRef(value);
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
      open: open3
    } = options;
    const [data, setData] = React24.useState({
      x: 0,
      y: 0,
      strategy,
      placement,
      middlewareData: {},
      isPositioned: false
    });
    const [latestMiddleware, setLatestMiddleware] = React24.useState(middleware);
    if (!deepEqual(latestMiddleware, middleware)) {
      setLatestMiddleware(middleware);
    }
    const [_reference, _setReference] = React24.useState(null);
    const [_floating, _setFloating] = React24.useState(null);
    const setReference = React24.useCallback((node) => {
      if (node !== referenceRef.current) {
        referenceRef.current = node;
        _setReference(node);
      }
    }, []);
    const setFloating = React24.useCallback((node) => {
      if (node !== floatingRef.current) {
        floatingRef.current = node;
        _setFloating(node);
      }
    }, []);
    const referenceEl = externalReference || _reference;
    const floatingEl = externalFloating || _floating;
    const referenceRef = React24.useRef(null);
    const floatingRef = React24.useRef(null);
    const dataRef = React24.useRef(data);
    const hasWhileElementsMounted = whileElementsMounted != null;
    const whileElementsMountedRef = useLatestRef(whileElementsMounted);
    const platformRef = useLatestRef(platform3);
    const openRef = useLatestRef(open3);
    const update2 = React24.useCallback(() => {
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
          ReactDOM3.flushSync(() => {
            setData(fullData);
          });
        }
      });
    }, [latestMiddleware, placement, strategy, platformRef, openRef]);
    index(() => {
      if (open3 === false && dataRef.current.isPositioned) {
        dataRef.current.isPositioned = false;
        setData((data2) => ({
          ...data2,
          isPositioned: false
        }));
      }
    }, [open3]);
    const isMountedRef = React24.useRef(false);
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
    const refs = React24.useMemo(() => ({
      reference: referenceRef,
      floating: floatingRef,
      setReference,
      setFloating
    }), [setReference, setFloating]);
    const elements = React24.useMemo(() => ({
      reference: referenceEl,
      floating: floatingEl
    }), [referenceEl, floatingEl]);
    const floatingStyles = React24.useMemo(() => {
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
    return React24.useMemo(() => ({
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

  // node_modules/@base-ui/react/utils/popups/popupHandle.mjs
  var BasePopupHandle = class {
    /**
     * Stores of every root currently using this handle, in attach order. A handle is meant to be used
     * by a single mounted root, but roots can transiently overlap (e.g. during an animated route
     * transition), so this stack lets `attachStore`'s cleanup restore the previous root instead of
     * leaving a still-mounted root uncontrollable when a newer overlapping root detaches first.
     */
    attachedStores = [];
    /**
     * Store of the root that currently controls the handle: the most recently attached one still
     * mounted, or `null` when no root is attached. Imperative methods are no-ops while this is `null`.
     */
    attachedStoreValue = null;
    /**
     * Listeners notified when `attachedStore` changes, so detached triggers can follow the store pointer.
     */
    storeListeners = /* @__PURE__ */ new Set();
    /**
     * Creates a handle backed by the store used while no root is attached.
     *
     * @param fallbackStore Inert, closed store handed to detached triggers while no root is attached,
     * so they can render and register without a mounted root. Triggers register into whichever store
     * `store` currently resolves to, so while detached they live in this store's trigger map and
     * migrate themselves to the root's store (and back) as it attaches/detaches.
     * @param componentName Component name used to prefix dev warnings, e.g. `'Menu'` produces
     * `MenuHandle.open()` in warning text.
     * @param throwOnMissingTrigger Whether `open(triggerId)` throws when no trigger with that id is
     * registered. Anchored popups (Menu, Popover, Tooltip, PreviewCard) need a trigger to anchor to,
     * so they throw; Dialog is not anchored and instead opens unassociated with a dev warning.
     */
    constructor(fallbackStore, componentName, throwOnMissingTrigger = true) {
      this.fallbackStore = fallbackStore;
      this.componentName = componentName;
      this.throwOnMissingTrigger = throwOnMissingTrigger;
    }
    get attachedStore() {
      return this.attachedStoreValue;
    }
    /**
     * Store that detached triggers read from: the attached root's store, or an inert fallback store
     * used while no root is attached.
     * @internal
     */
    get store() {
      return this.attachedStoreValue ?? this.fallbackStore;
    }
    /**
     * Stable fallback store used for server rendering and hydration. Root stores cannot be recorded on
     * the handle during render because a handle can be shared by concurrent server-rendered requests.
     * @internal
     */
    get serverStore() {
      return this.fallbackStore;
    }
    /**
     * Subscribes to changes of the attached store pointer so detached triggers re-render and re-bind
     * when a root attaches or detaches. Returns a function that removes the listener.
     * @internal
     */
    subscribeStore(listener) {
      this.storeListeners.add(listener);
      return () => {
        this.storeListeners.delete(listener);
      };
    }
    /**
     * Points the handle at a root's store and notifies subscribers so detached triggers re-render and
     * re-register into it (their registration effect migrates them when the store pointer changes).
     * Returns a cleanup function that detaches the store again.
     * @internal
     */
    attachStore(newStore) {
      this.attachedStores.push(newStore);
      this.setActiveStore(newStore);
      if (true) {
        if (this.attachedStores.length > 1) {
          const dev = this;
          (dev.overlapWarningFrame ??= AnimationFrame.create()).request(() => {
            if (this.attachedStores.length > 1) {
              console.warn("Base UI: A handle is attached to more than one mounted root at the same time. The most recently mounted root takes over and the previous one stops being controlled by the handle. A handle should be used by a single root that stays mounted for the lifetime of the handle.");
            }
          });
        }
      }
      return () => {
        const index2 = this.attachedStores.lastIndexOf(newStore);
        if (index2 !== -1) {
          this.attachedStores.splice(index2, 1);
        }
        this.setActiveStore(this.attachedStores[this.attachedStores.length - 1] ?? null);
      };
    }
    /**
     * Sets the store that currently controls the handle and notifies subscribers when it changes, so
     * detached triggers re-render and migrate their registration to the new store.
     */
    setActiveStore(store2) {
      if (this.attachedStoreValue !== store2) {
        this.attachedStoreValue = store2;
        this.storeListeners.forEach((listener) => {
          listener();
        });
      }
    }
    /**
     * Opens the attached root's store and associates it with the trigger with the given id, or a
     * no-op (with a dev warning) while no root is attached. Shared by every concrete handle's public
     * `open()` method, which only narrows the parameter type.
     *
     * When a trigger id is given but no matching trigger is registered, anchored popups throw (see
     * `throwOnMissingTrigger`); Dialog opens unassociated with a dev warning instead.
     *
     * This method should only be called in an event handler or an effect (not during rendering).
     *
     * @param triggerId ID of the trigger to associate with the popup, or `null`/`undefined` to open
     * without associating any trigger.
     */
    openByTrigger(triggerId) {
      const attachedStore = this.attachedStore;
      if (attachedStore === null) {
        if (true) {
          console.warn(`Base UI: ${this.componentName}Handle.open() was called while no root using this handle is mounted. The call was ignored; mount a root with this handle before opening it imperatively.`);
        }
        return;
      }
      let triggerElement;
      if (triggerId) {
        for (let i = this.attachedStores.length - 1; i >= 0 && !triggerElement; i -= 1) {
          triggerElement = this.attachedStores[i].context.triggerElements.getById(triggerId);
        }
        triggerElement ??= this.fallbackStore.context.triggerElements.getById(triggerId);
      }
      if (triggerId && !triggerElement) {
        if (this.throwOnMissingTrigger) {
          throw new Error(true ? `Base UI: ${this.componentName}Handle.open() was called with the trigger id "${triggerId}", but no matching trigger is registered with this handle. An anchored popup cannot open without a trigger to anchor to. Pass the id of a mounted ${this.componentName}.Trigger that has this handle set on its "handle" prop.` : formatErrorMessage_default(99, this.componentName, triggerId, this.componentName));
        }
        if (true) {
          console.warn(`Base UI: ${this.componentName}Handle.open: No trigger found with id "${triggerId}". The popup will open, but the trigger will not be associated with it.`);
        }
      }
      attachedStore.setOpen(true, createChangeEventDetails(reason_parts_exports.imperativeAction, void 0, triggerElement));
    }
    /**
     * Closes the popup by setting the attached root's store to closed, or a no-op (with a dev warning)
     * while no root is attached. Shared by every concrete handle's public `close()` method.
     *
     * This method should only be called in an event handler or an effect (not during rendering).
     */
    closePopup() {
      const attachedStore = this.attachedStore;
      if (attachedStore === null) {
        if (true) {
          console.warn(`Base UI: ${this.componentName}Handle.close() was called while no root using this handle is mounted. The call was ignored.`);
        }
        return;
      }
      attachedStore.setOpen(false, createChangeEventDetails(reason_parts_exports.imperativeAction));
    }
  };

  // node_modules/@base-ui/react/utils/popups/popupStoreUtils.mjs
  var React29 = __toESM(require_react(), 1);
  var ReactDOM4 = __toESM(require_react_dom(), 1);

  // node_modules/@base-ui/react/floating-ui-react/hooks/useSyncedFloatingRootContext.mjs
  var React28 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/store/useStore.mjs
  var React26 = __toESM(require_react(), 1);
  var import_shim = __toESM(require_shim(), 1);
  var import_with_selector = __toESM(require_with_selector(), 1);

  // node_modules/@base-ui/utils/fastHooks.mjs
  var React25 = __toESM(require_react(), 1);
  var hooks = [];
  var currentInstance = void 0;
  function getInstance() {
    return currentInstance;
  }
  function register(hook) {
    hooks.push(hook);
  }
  function fastComponent(fn) {
    const FastComponent = (props, forwardedRef) => {
      const instance = useRefWithInit(createInstance).current;
      let result;
      try {
        currentInstance = instance;
        for (const hook of hooks) {
          hook.before(instance);
        }
        result = fn(props, forwardedRef);
        for (const hook of hooks) {
          hook.after(instance);
        }
        instance.didInitialize = true;
      } finally {
        currentInstance = void 0;
      }
      return result;
    };
    FastComponent.displayName = fn.displayName || fn.name;
    return FastComponent;
  }
  function fastComponentRef(fn) {
    return /* @__PURE__ */ React25.forwardRef(fastComponent(fn));
  }
  function createInstance() {
    return {
      didInitialize: false
    };
  }

  // node_modules/@base-ui/utils/store/useStore.mjs
  var canUseRawUseSyncExternalStore = isReactVersionAtLeast(19);
  var useStoreImplementation = canUseRawUseSyncExternalStore ? useStoreFast : useStoreLegacy;
  function useStore(store2, selector, a1, a2, a3) {
    return useStoreImplementation(store2, selector, a1, a2, a3);
  }
  function useStoreR19(store2, selector, a1, a2, a3) {
    const getSelection = React26.useCallback(() => selector(store2.getSnapshot(), a1, a2, a3), [store2, selector, a1, a2, a3]);
    return (0, import_shim.useSyncExternalStore)(store2.subscribe, getSelection, getSelection);
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
            for (const store2 of stores) {
              unsubscribes.push(store2.subscribe(onStoreChange));
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
  function useStoreFast(store2, selector, a1, a2, a3) {
    const instance = getInstance();
    if (!instance) {
      return useStoreR19(store2, selector, a1, a2, a3);
    }
    const index2 = instance.syncIndex;
    instance.syncIndex += 1;
    let hook;
    if (!instance.didInitialize) {
      hook = {
        store: store2,
        selector,
        a1,
        a2,
        a3,
        value: selector(store2.getSnapshot(), a1, a2, a3)
      };
      instance.syncHooks.push(hook);
    } else {
      hook = instance.syncHooks[index2];
      if (hook.store !== store2 || hook.selector !== selector || !Object.is(hook.a1, a1) || !Object.is(hook.a2, a2) || !Object.is(hook.a3, a3)) {
        if (hook.store !== store2) {
          instance.didChangeStore = true;
        }
        hook.store = store2;
        hook.selector = selector;
        hook.a1 = a1;
        hook.a2 = a2;
        hook.a3 = a3;
        hook.value = selector(store2.getSnapshot(), a1, a2, a3);
      }
    }
    return hook.value;
  }
  function useStoreLegacy(store2, selector, a1, a2, a3) {
    return (0, import_with_selector.useSyncExternalStoreWithSelector)(store2.subscribe, store2.getSnapshot, store2.getSnapshot, (state) => selector(state, a1, a2, a3));
  }

  // node_modules/@base-ui/utils/store/Store.mjs
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

  // node_modules/@base-ui/utils/store/ReactStore.mjs
  var React27 = __toESM(require_react(), 1);
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
      React27.useDebugValue(key);
      const store2 = this;
      useIsoLayoutEffect(() => {
        if (store2.state[key] !== value) {
          store2.set(key, value);
        }
      }, [store2, key, value]);
    }
    /**
     * Synchronizes a single external value into the store and
     * cleans it up (sets to `undefined`) on unmount.
     *
     * Note that the while the value in `state` is updated immediately, the value returned
     * by `useState` is updated before the next render (similarly to React's `useState`).
     */
    useSyncedValueWithCleanup(key, value) {
      const store2 = this;
      useIsoLayoutEffect(() => {
        if (store2.state[key] !== value) {
          store2.set(key, value);
        }
        return () => {
          store2.set(key, void 0);
        };
      }, [store2, key, value]);
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
      const store2 = this;
      if (true) {
        React27.useDebugValue(statePart, (p) => Object.keys(p));
        const keys = React27.useRef(Object.keys(statePart)).current;
        const nextKeys = Object.keys(statePart);
        if (keys.length !== nextKeys.length || keys.some((key, index2) => key !== nextKeys[index2])) {
          console.error("ReactStore.useSyncedValues expects the same prop keys on every render. Keys should be stable.");
        }
      }
      const dependencies = Object.values(statePart);
      useIsoLayoutEffect(() => {
        store2.update(statePart);
      }, [store2, ...dependencies]);
    }
    /**
     * Registers a controllable prop pair (`controlled`, `defaultValue`) for a specific key. If `controlled`
     * is non-undefined, the store's state at `key` is updated to match `controlled`.
     */
    useControlledProp(key, controlled) {
      React27.useDebugValue(key);
      const store2 = this;
      const isControlled = controlled !== void 0;
      useIsoLayoutEffect(() => {
        if (isControlled && !Object.is(store2.state[key], controlled)) {
          store2.setState({
            ...store2.state,
            [key]: controlled
          });
        }
      }, [store2, key, controlled, isControlled]);
      if (true) {
        const cache = this.controlledValues ??= /* @__PURE__ */ new Map();
        if (!cache.has(key)) {
          cache.set(key, isControlled);
        }
        const previouslyControlled = cache.get(key);
        if (previouslyControlled !== void 0 && previouslyControlled !== isControlled) {
          console.error(`A component is changing the ${isControlled ? "" : "un"}controlled state of ${key.toString()} to be ${isControlled ? "un" : ""}controlled. Elements should not switch from uncontrolled to controlled (or vice versa).`);
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
      React27.useDebugValue(key);
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
      React27.useDebugValue(key);
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
      const ref = React27.useRef(void 0);
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

  // node_modules/@base-ui/react/floating-ui-react/components/FloatingRootStore.mjs
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

  // node_modules/@base-ui/react/floating-ui-react/hooks/useSyncedFloatingRootContext.mjs
  function useSyncedFloatingRootContext(options) {
    const {
      popupStore,
      treatPopupAsFloatingElement = false,
      floatingRootContext: floatingRootContextProp,
      floatingId,
      nested,
      onOpenChange
    } = options;
    const open3 = popupStore.useState("open");
    const referenceElement = popupStore.useState("activeTriggerElement");
    const floatingElement = popupStore.useState(treatPopupAsFloatingElement ? "popupElement" : "positionerElement");
    const triggerElements = popupStore.context.triggerElements;
    const handleOpenChange = onOpenChange;
    const internalStoreRef = React28.useRef(null);
    if (floatingRootContextProp === void 0 && internalStoreRef.current === null) {
      internalStoreRef.current = new FloatingRootStore({
        open: open3,
        transitionStatus: void 0,
        referenceElement,
        floatingElement,
        triggerElements,
        onOpenChange: handleOpenChange,
        floatingId,
        syncOnly: true,
        nested
      });
    }
    const store2 = floatingRootContextProp ?? internalStoreRef.current;
    popupStore.useSyncedValue("floatingId", floatingId);
    useIsoLayoutEffect(() => {
      const valuesToSync = {
        open: open3,
        floatingId,
        referenceElement,
        floatingElement
      };
      if (isElement(referenceElement)) {
        valuesToSync.domReferenceElement = referenceElement;
      }
      if (store2.state.positionReference === store2.state.referenceElement) {
        valuesToSync.positionReference = referenceElement;
      }
      store2.update(valuesToSync);
    }, [open3, floatingId, referenceElement, floatingElement, store2]);
    store2.context.onOpenChange = handleOpenChange;
    store2.context.nested = nested;
    return store2;
  }

  // node_modules/@base-ui/react/utils/popups/popupStoreUtils.mjs
  var FOCUSABLE_POPUP_PROPS = {
    tabIndex: -1,
    [FOCUSABLE_ATTRIBUTE]: ""
  };
  function usePopupRootStore(createStore, treatPopupAsFloatingElement = false) {
    const floatingId = useId();
    const nested = useFloatingParentNodeId() != null;
    const store2 = useRefWithInit(() => createStore(floatingId, nested)).current;
    useSyncedFloatingRootContext({
      popupStore: store2,
      treatPopupAsFloatingElement,
      floatingRootContext: store2.state.floatingRootContext,
      floatingId,
      nested,
      onOpenChange: store2.setOpen
    });
    return store2;
  }
  function PopupHandleAttachment({
    handle,
    store: store2
  }) {
    useIsoLayoutEffect(() => {
      return handle.attachStore(store2);
    }, [handle, store2]);
    return null;
  }
  function syncTriggerCount(store2) {
    const triggerCount = store2.context.triggerElements.size;
    if (store2.select("open") && store2.state.triggerCount !== triggerCount) {
      store2.set("triggerCount", triggerCount);
    }
  }
  function useTriggerRegistration(id, store2) {
    const registrationRef = React29.useRef(null);
    return useStableCallback((element) => {
      const registration = registrationRef.current;
      if (registration !== null) {
        if (registration.element === element && registration.store === store2 && registration.id === id) {
          return;
        }
        registrationRef.current = null;
        const registeredStore = registration.store;
        if (registeredStore.context.triggerElements.getById(registration.id) === registration.element) {
          registeredStore.context.triggerElements.delete(registration.id);
          syncTriggerCount(registeredStore);
        }
      }
      if (element !== null && id !== void 0) {
        registrationRef.current = {
          store: store2,
          id,
          element
        };
        store2.context.triggerElements.add(id, element);
        syncTriggerCount(store2);
      }
    });
  }
  function createPopupOpenState(state, open3, trigger, preventUnmountOnClose = false) {
    let preventUnmountingOnClose = state.preventUnmountingOnClose;
    if (open3) {
      preventUnmountingOnClose = false;
    } else if (preventUnmountOnClose) {
      preventUnmountingOnClose = true;
    }
    const triggerId = trigger?.id ?? null;
    let activeTriggerId = state.activeTriggerId;
    let activeTriggerElement = state.activeTriggerElement;
    if (triggerId || open3) {
      activeTriggerId = triggerId;
      activeTriggerElement = trigger ?? null;
    }
    return {
      open: open3,
      preventUnmountingOnClose,
      activeTriggerId,
      activeTriggerElement
    };
  }
  function attachPreventUnmountOnClose(eventDetails) {
    let preventUnmountOnClose = false;
    eventDetails.preventUnmountOnClose = () => {
      preventUnmountOnClose = true;
    };
    return () => preventUnmountOnClose;
  }
  function applyPopupOpenChange(store2, nextOpen, eventDetails, options = {}) {
    const reason = eventDetails.reason;
    const isHover = reason === reason_parts_exports.triggerHover;
    const isFocusOpen = nextOpen && reason === reason_parts_exports.triggerFocus;
    const isDismissClose = !nextOpen && (reason === reason_parts_exports.triggerPress || reason === reason_parts_exports.escapeKey);
    const shouldPreventUnmountOnClose = attachPreventUnmountOnClose(eventDetails);
    store2.context.onOpenChange?.(nextOpen, eventDetails);
    if (eventDetails.isCanceled) {
      return;
    }
    options.onBeforeDispatch?.();
    store2.state.floatingRootContext.dispatchOpenChange(nextOpen, eventDetails);
    const changeState = () => {
      const popupOpenState = createPopupOpenState(store2.state, nextOpen, eventDetails.trigger, shouldPreventUnmountOnClose());
      const updatedState = {
        ...options.extraState,
        ...popupOpenState
      };
      if (isFocusOpen) {
        updatedState.instantType = "focus";
      } else if (isDismissClose) {
        updatedState.instantType = "dismiss";
      } else if (isHover) {
        updatedState.instantType = void 0;
      }
      store2.update(updatedState);
    };
    if (isHover) {
      ReactDOM4.flushSync(changeState);
    } else {
      changeState();
    }
  }
  function useTriggerDataForwarding(triggerId, triggerElementRef, store2, stateUpdates) {
    const isMountedByThisTrigger = store2.useState("isMountedByTrigger", triggerId);
    const baseRegisterTrigger = useTriggerRegistration(triggerId, store2);
    const applyTriggerData = useStableCallback((element) => {
      const open3 = store2.select("open");
      const activeTriggerId = store2.select("activeTriggerId");
      if (activeTriggerId === triggerId) {
        const changes = {
          activeTriggerElement: element,
          ...open3 ? stateUpdates : null
        };
        store2.update(changes);
        return;
      }
      if (activeTriggerId == null && open3) {
        const changes = {
          activeTriggerId: triggerId ?? null,
          activeTriggerElement: element,
          ...stateUpdates
        };
        store2.update(changes);
      }
    });
    const registerTrigger = useStableCallback((element) => {
      baseRegisterTrigger(element);
      if (element) {
        applyTriggerData(element);
      }
    });
    useIsoLayoutEffect(() => {
      registerTrigger(triggerElementRef.current);
      return () => registerTrigger(null);
    }, [registerTrigger, triggerElementRef, store2, triggerId]);
    useIsoLayoutEffect(() => {
      if (isMountedByThisTrigger) {
        const changes = {
          activeTriggerElement: triggerElementRef.current,
          ...stateUpdates
        };
        store2.update(changes);
      }
    }, [isMountedByThisTrigger, store2, triggerElementRef, ...Object.values(stateUpdates)]);
    return {
      registerTrigger,
      isMountedByThisTrigger
    };
  }
  function useImplicitActiveTrigger(store2, options = {}) {
    const {
      closeOnActiveTriggerUnmount = false
    } = options;
    const resolvedActiveTriggerIdRef = React29.useRef(null);
    const open3 = store2.useState("open");
    const reactiveTriggerCount = store2.useState("triggerCount");
    const activeTriggerId = store2.useState("activeTriggerId");
    const reactiveActiveTriggerElement = store2.useState("activeTriggerElement");
    useIsoLayoutEffect(() => {
      if (!open3) {
        resolvedActiveTriggerIdRef.current = null;
        if (store2.state.triggerCount !== 0) {
          store2.set("triggerCount", 0);
        }
        return;
      }
      const triggerCount = store2.context.triggerElements.size;
      const stateUpdates = {};
      if (store2.state.triggerCount !== triggerCount) {
        stateUpdates.triggerCount = triggerCount;
      }
      const currentActiveTriggerId = store2.select("activeTriggerId");
      let lostActiveTriggerId = null;
      if (currentActiveTriggerId) {
        const activeTriggerElement = store2.context.triggerElements.getById(currentActiveTriggerId);
        if (!activeTriggerElement) {
          for (const [triggerId, triggerElement] of store2.context.triggerElements.entries()) {
            if (triggerElement === store2.state.activeTriggerElement) {
              stateUpdates.activeTriggerId = triggerId;
              stateUpdates.activeTriggerElement = triggerElement;
              resolvedActiveTriggerIdRef.current = triggerId;
              break;
            }
          }
          if (stateUpdates.activeTriggerId === void 0) {
            if (resolvedActiveTriggerIdRef.current === currentActiveTriggerId) {
              lostActiveTriggerId = currentActiveTriggerId;
            } else {
              resolvedActiveTriggerIdRef.current = null;
            }
          }
        } else {
          resolvedActiveTriggerIdRef.current = currentActiveTriggerId;
          if (activeTriggerElement !== store2.state.activeTriggerElement) {
            stateUpdates.activeTriggerElement = activeTriggerElement;
          }
        }
      } else {
        resolvedActiveTriggerIdRef.current = null;
      }
      if (!lostActiveTriggerId && !currentActiveTriggerId && triggerCount === 1) {
        const iteratorResult = store2.context.triggerElements.entries().next();
        if (!iteratorResult.done) {
          const [implicitTriggerId, implicitTriggerElement] = iteratorResult.value;
          stateUpdates.activeTriggerId = implicitTriggerId;
          stateUpdates.activeTriggerElement = implicitTriggerElement;
          resolvedActiveTriggerIdRef.current = implicitTriggerId;
        }
      }
      if (stateUpdates.triggerCount !== void 0 || stateUpdates.activeTriggerId !== void 0 || stateUpdates.activeTriggerElement !== void 0) {
        store2.update(stateUpdates);
      }
      if (lostActiveTriggerId) {
        if (closeOnActiveTriggerUnmount) {
          queueMicrotask(() => {
            if (store2.select("open") && store2.select("activeTriggerId") === lostActiveTriggerId && !store2.context.triggerElements.getById(lostActiveTriggerId)) {
              const eventDetails = createChangeEventDetails(reason_parts_exports.none);
              store2.setOpen(false, eventDetails);
              if (!eventDetails.isCanceled) {
                store2.update({
                  activeTriggerId: null,
                  activeTriggerElement: null
                });
              }
            }
          });
        }
      }
    }, [open3, store2, reactiveTriggerCount, activeTriggerId, reactiveActiveTriggerElement, closeOnActiveTriggerUnmount]);
  }
  function useOpenStateTransitions(open3, store2, onUnmount, animateInitialOpen) {
    const {
      mounted,
      setMounted,
      transitionStatus
    } = useTransitionStatus(open3, false, false, animateInitialOpen);
    const preventUnmountingOnClose = store2.useState("preventUnmountingOnClose");
    const syncedPreventUnmountingOnClose = open3 ? false : preventUnmountingOnClose;
    store2.useSyncedValues({
      mounted,
      transitionStatus,
      preventUnmountingOnClose: syncedPreventUnmountingOnClose
    });
    const forceUnmount = useStableCallback(() => {
      setMounted(false);
      store2.update({
        activeTriggerId: null,
        activeTriggerElement: null,
        mounted: false,
        preventUnmountingOnClose: false
      });
      onUnmount?.();
      store2.context.onOpenChangeComplete?.(false);
    });
    useOpenChangeComplete({
      enabled: mounted && !open3 && !syncedPreventUnmountingOnClose,
      open: open3,
      ref: store2.context.popupRef,
      onComplete() {
        if (!open3) {
          forceUnmount();
        }
      }
    });
    return {
      forceUnmount,
      transitionStatus
    };
  }
  function usePopupInteractionProps(store2, statePart) {
    store2.useSyncedValues(statePart);
    useIsoLayoutEffect(() => () => {
      store2.update({
        activeTriggerProps: EMPTY_OBJECT,
        inactiveTriggerProps: EMPTY_OBJECT,
        popupProps: EMPTY_OBJECT
      });
    }, [store2]);
  }

  // node_modules/@base-ui/react/utils/popups/popupTriggerMap.mjs
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
    constructor() {
      this.idMap = /* @__PURE__ */ new Map();
    }
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

  // node_modules/@base-ui/react/utils/popups/store.mjs
  function createInitialPopupStoreState(triggerElements, floatingId, nested = false) {
    return {
      open: false,
      openProp: void 0,
      mounted: false,
      transitionStatus: void 0,
      floatingRootContext: new FloatingRootStore({
        open: false,
        transitionStatus: void 0,
        floatingElement: null,
        referenceElement: null,
        triggerElements,
        floatingId,
        syncOnly: true,
        nested,
        onOpenChange: void 0
      }),
      floatingId,
      triggerCount: 0,
      preventUnmountingOnClose: false,
      payload: void 0,
      activeTriggerId: null,
      activeTriggerElement: null,
      triggerIdProp: void 0,
      popupElement: null,
      positionerElement: null,
      activeTriggerProps: EMPTY_OBJECT,
      inactiveTriggerProps: EMPTY_OBJECT,
      popupProps: EMPTY_OBJECT
    };
  }
  var activeTriggerIdSelector = (state) => state.triggerIdProp ?? state.activeTriggerId;
  var openSelector = (state) => state.openProp ?? state.open;
  var popupIdSelector = (state) => {
    const popupId = state.popupElement?.id ?? state.floatingId;
    return popupId || void 0;
  };
  function triggerOwnsOpenPopup(state, triggerId) {
    return triggerId !== void 0 && openSelector(state) && activeTriggerIdSelector(state) === triggerId;
  }
  function triggerOwnsOpenPopupOrIsOnlyTrigger(state, triggerId) {
    if (triggerOwnsOpenPopup(state, triggerId)) {
      return true;
    }
    return triggerId !== void 0 && openSelector(state) && activeTriggerIdSelector(state) == null && state.triggerCount === 1;
  }
  var popupStoreSelectors = {
    open: openSelector,
    mounted: (state) => state.mounted,
    transitionStatus: (state) => state.transitionStatus,
    floatingRootContext: (state) => state.floatingRootContext,
    triggerCount: (state) => state.triggerCount,
    preventUnmountingOnClose: (state) => state.preventUnmountingOnClose,
    payload: (state) => state.payload,
    activeTriggerId: activeTriggerIdSelector,
    activeTriggerElement: (state) => state.mounted ? state.activeTriggerElement : null,
    popupId: popupIdSelector,
    /**
     * Whether the trigger with the given ID was used to open the popup.
     */
    isTriggerActive: (state, triggerId) => triggerId !== void 0 && activeTriggerIdSelector(state) === triggerId,
    /**
     * Whether the popup is open and was activated by a trigger with the given ID.
     */
    isOpenedByTrigger: (state, triggerId) => triggerOwnsOpenPopup(state, triggerId),
    /**
     * Whether the popup is mounted and was activated by a trigger with the given ID.
     */
    isMountedByTrigger: (state, triggerId) => triggerId !== void 0 && activeTriggerIdSelector(state) === triggerId && state.mounted,
    triggerProps: (state, isActive) => isActive ? state.activeTriggerProps : state.inactiveTriggerProps,
    /**
     * Popup id for the trigger that currently owns the open popup.
     */
    triggerPopupId: (state, triggerId) => triggerOwnsOpenPopupOrIsOnlyTrigger(state, triggerId) ? popupIdSelector(state) : void 0,
    popupProps: (state) => state.popupProps,
    popupElement: (state) => state.popupElement,
    positionerElement: (state) => state.positionerElement
  };

  // node_modules/@base-ui/react/utils/popups/usePopupHandleStore.mjs
  var React30 = __toESM(require_react(), 1);
  var import_shim2 = __toESM(require_shim(), 1);
  function usePopupHandleStore(handle) {
    const subscribe = React30.useCallback((listener) => {
      if (handle === void 0) {
        return NOOP;
      }
      return handle.subscribeStore(listener);
    }, [handle]);
    const getSnapshot = React30.useCallback(() => {
      return handle === void 0 ? void 0 : handle.store;
    }, [handle]);
    return (0, import_shim2.useSyncExternalStore)(subscribe, getSnapshot, () => handle?.serverStore);
  }

  // node_modules/@base-ui/react/floating-ui-react/hooks/useFloating.mjs
  function useBaseUIFloating(options) {
    return useFloatingWithStore(options, options.rootContext);
  }
  function useFloatingWithStore(options, store2) {
    const {
      nodeId,
      externalTree
    } = options;
    const referenceElement = store2.useState("referenceElement");
    const floatingElement = store2.useState("floatingElement");
    const domReferenceElement = store2.useState("domReferenceElement");
    const open3 = store2.useState("open");
    const floatingId = store2.useState("floatingId");
    const [positionReference, setPositionReferenceRaw] = React31.useState(null);
    const [localDomReference, setLocalDomReference] = React31.useState(void 0);
    const [localFloatingElement, setLocalFloatingElement] = React31.useState(void 0);
    const domReferenceRef = React31.useRef(null);
    const tree = useFloatingTree(externalTree);
    const storeElements = React31.useMemo(() => ({
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
    const localDomReferenceElement = isElement(localDomReference) ? localDomReference : null;
    const syncedFloatingElement = localFloatingElement === void 0 ? store2.state.floatingElement : localFloatingElement;
    store2.useSyncedValue("referenceElement", localDomReference ?? null);
    store2.useSyncedValue("domReferenceElement", localDomReference === void 0 ? domReferenceElement : localDomReferenceElement);
    store2.useSyncedValue("floatingElement", syncedFloatingElement);
    const setPositionReference = React31.useCallback((node) => {
      const computedPositionReference = isElement(node) ? {
        getBoundingClientRect: () => node.getBoundingClientRect(),
        getClientRects: () => node.getClientRects(),
        contextElement: node
      } : node;
      setPositionReferenceRaw(computedPositionReference);
      position.refs.setReference(computedPositionReference);
    }, [position.refs]);
    const setReference = React31.useCallback((node) => {
      if (isElement(node) || node === null) {
        domReferenceRef.current = node;
        setLocalDomReference(node);
      }
      if (isElement(position.refs.reference.current) || position.refs.reference.current === null || // Don't allow setting virtual elements using the old technique back to
      // `null` to support `positionReference` + an unstable `reference`
      // callback ref.
      node !== null && !isElement(node)) {
        position.refs.setReference(node);
      }
    }, [position.refs, setLocalDomReference]);
    const setFloating = React31.useCallback((node) => {
      setLocalFloatingElement(node);
      position.refs.setFloating(node);
    }, [position.refs]);
    const refs = React31.useMemo(() => ({
      ...position.refs,
      setReference,
      setFloating,
      setPositionReference,
      domReference: domReferenceRef
    }), [position.refs, setReference, setFloating, setPositionReference]);
    const elements = React31.useMemo(() => ({
      ...position.elements,
      domReference: domReferenceElement
    }), [position.elements, domReferenceElement]);
    const context = React31.useMemo(() => ({
      ...position,
      dataRef: store2.context.dataRef,
      open: open3,
      onOpenChange: store2.setOpen,
      events: store2.context.events,
      floatingId,
      refs,
      elements,
      nodeId,
      rootStore: store2
    }), [position, refs, elements, nodeId, store2, open3, floatingId]);
    useIsoLayoutEffect(() => {
      if (domReferenceElement) {
        domReferenceRef.current = domReferenceElement;
      }
    }, [domReferenceElement]);
    useIsoLayoutEffect(() => {
      store2.context.dataRef.current.floatingContext = context;
      const node = tree?.nodesRef.current.find((n) => n.id === nodeId);
      if (node) {
        node.context = context;
      }
    });
    return React31.useMemo(() => ({
      ...position,
      context,
      refs,
      elements,
      rootStore: store2
    }), [position, refs, elements, context, store2]);
  }

  // node_modules/@base-ui/react/floating-ui-react/hooks/useFocus.mjs
  var React32 = __toESM(require_react(), 1);
  var isMacSafari = parts_exports.os.mac && parts_exports.engine.webkit;
  function useFocus(context, props = {}) {
    const {
      enabled = true,
      delay
    } = props;
    const store2 = "rootStore" in context ? context.rootStore : context;
    const {
      events,
      dataRef
    } = store2.context;
    const blockFocusRef = React32.useRef(false);
    const blockedReferenceRef = React32.useRef(null);
    const keyboardModalityRef = React32.useRef(true);
    const timeout = useTimeout();
    React32.useEffect(() => {
      const domReference = store2.select("domReferenceElement");
      if (!enabled) {
        return void 0;
      }
      const win = getWindow(domReference);
      function onBlur() {
        const currentDomReference = store2.select("domReferenceElement");
        if (!store2.select("open") && isHTMLElement(currentDomReference) && currentDomReference === activeElement(ownerDocument(currentDomReference))) {
          blockFocusRef.current = true;
          blockedReferenceRef.current = currentDomReference;
        }
      }
      function onKeyDown() {
        keyboardModalityRef.current = true;
      }
      function onPointerDown() {
        keyboardModalityRef.current = false;
      }
      return mergeCleanups(addEventListener(win, "blur", onBlur), isMacSafari && addEventListener(win, "keydown", onKeyDown, true), isMacSafari && addEventListener(win, "pointerdown", onPointerDown, true));
    }, [store2, enabled]);
    React32.useEffect(() => {
      if (!enabled) {
        return void 0;
      }
      function onOpenChangeLocal(details) {
        if (details.reason === reason_parts_exports.triggerPress || details.reason === reason_parts_exports.escapeKey) {
          const referenceElement = store2.select("domReferenceElement");
          if (isElement(referenceElement)) {
            blockedReferenceRef.current = referenceElement;
            blockFocusRef.current = true;
          }
        }
      }
      events.on("openchange", onOpenChangeLocal);
      return () => {
        events.off("openchange", onOpenChangeLocal);
      };
    }, [events, enabled, store2]);
    const reference = React32.useMemo(() => {
      function resetBlockedFocus() {
        blockFocusRef.current = false;
        blockedReferenceRef.current = null;
      }
      return {
        onMouseLeave() {
          resetBlockedFocus();
        },
        onFocus(event) {
          const focusTarget = event.currentTarget;
          if (blockFocusRef.current) {
            if (blockedReferenceRef.current === focusTarget) {
              return;
            }
            resetBlockedFocus();
          }
          const target = getTarget(event.nativeEvent);
          if (isElement(target)) {
            if (isMacSafari && !event.relatedTarget) {
              if (!keyboardModalityRef.current && !isTypeableElement(target)) {
                return;
              }
            } else if (!matchesFocusVisible(target)) {
              return;
            }
          }
          const movedFromOtherEnabledTrigger = isTargetInsideEnabledTrigger(event.relatedTarget, store2.context.triggerElements);
          const {
            nativeEvent,
            currentTarget
          } = event;
          const delayValue = typeof delay === "function" ? delay() : delay;
          if (store2.select("open") && movedFromOtherEnabledTrigger || delayValue === 0 || delayValue === void 0) {
            store2.setOpen(true, createChangeEventDetails(reason_parts_exports.triggerFocus, nativeEvent, currentTarget));
            return;
          }
          timeout.start(delayValue, () => {
            if (blockFocusRef.current) {
              return;
            }
            store2.setOpen(true, createChangeEventDetails(reason_parts_exports.triggerFocus, nativeEvent, currentTarget));
          });
        },
        onBlur(event) {
          resetBlockedFocus();
          const relatedTarget = event.relatedTarget;
          const nativeEvent = event.nativeEvent;
          const movedToFocusGuard = isElement(relatedTarget) && relatedTarget.hasAttribute(createAttribute("focus-guard")) && relatedTarget.getAttribute("data-type") === "outside";
          timeout.start(0, () => {
            const domReference = store2.select("domReferenceElement");
            const activeEl = activeElement(ownerDocument(domReference));
            if (!relatedTarget && activeEl === domReference) {
              return;
            }
            if (contains(dataRef.current.floatingContext?.refs.floating.current, activeEl) || contains(domReference, activeEl) || movedToFocusGuard) {
              return;
            }
            const nextFocusedElement = relatedTarget ?? activeEl;
            if (isTargetInsideEnabledTrigger(nextFocusedElement, store2.context.triggerElements)) {
              return;
            }
            store2.setOpen(false, createChangeEventDetails(reason_parts_exports.triggerFocus, nativeEvent));
          });
        }
      };
    }, [dataRef, delay, store2, timeout]);
    return React32.useMemo(() => enabled ? {
      reference,
      trigger: reference
    } : {}, [enabled, reference]);
  }

  // node_modules/@base-ui/react/floating-ui-react/hooks/useHoverFloatingInteraction.mjs
  var React33 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/floating-ui-react/hooks/useHoverInteractionSharedState.mjs
  var HoverInteraction = class _HoverInteraction {
    constructor() {
      this.pointerType = void 0;
      this.interactedInside = false;
      this.handler = void 0;
      this.blockMouseMove = true;
      this.performedPointerEventsMutation = false;
      this.pointerEventsScopeElement = null;
      this.pointerEventsReferenceElement = null;
      this.pointerEventsFloatingElement = null;
      this.restTimeoutPending = false;
      this.openChangeTimeout = new Timeout();
      this.restTimeout = new Timeout();
      this.handleCloseOptions = void 0;
    }
    static create() {
      return new _HoverInteraction();
    }
    dispose = () => {
      this.openChangeTimeout.clear();
      this.restTimeout.clear();
    };
    disposeEffect = () => {
      return this.dispose;
    };
  };
  var pointerEventsMutationOwnerByScopeElement = /* @__PURE__ */ new WeakMap();
  function clearSafePolygonPointerEventsMutation(instance) {
    if (!instance.performedPointerEventsMutation) {
      return;
    }
    const scopeElement = instance.pointerEventsScopeElement;
    if (scopeElement && pointerEventsMutationOwnerByScopeElement.get(scopeElement) === instance) {
      instance.pointerEventsScopeElement?.style.removeProperty("pointer-events");
      instance.pointerEventsReferenceElement?.style.removeProperty("pointer-events");
      instance.pointerEventsFloatingElement?.style.removeProperty("pointer-events");
      pointerEventsMutationOwnerByScopeElement.delete(scopeElement);
    }
    instance.performedPointerEventsMutation = false;
    instance.pointerEventsScopeElement = null;
    instance.pointerEventsReferenceElement = null;
    instance.pointerEventsFloatingElement = null;
  }
  function applySafePolygonPointerEventsMutation(instance, options) {
    const {
      scopeElement,
      referenceElement,
      floatingElement
    } = options;
    const existingOwner = pointerEventsMutationOwnerByScopeElement.get(scopeElement);
    if (existingOwner && existingOwner !== instance) {
      clearSafePolygonPointerEventsMutation(existingOwner);
    }
    clearSafePolygonPointerEventsMutation(instance);
    instance.performedPointerEventsMutation = true;
    instance.pointerEventsScopeElement = scopeElement;
    instance.pointerEventsReferenceElement = referenceElement;
    instance.pointerEventsFloatingElement = floatingElement;
    pointerEventsMutationOwnerByScopeElement.set(scopeElement, instance);
    scopeElement.style.pointerEvents = "none";
    referenceElement.style.pointerEvents = "auto";
    floatingElement.style.pointerEvents = "auto";
  }
  function useHoverInteractionSharedState(store2) {
    const data = store2.context.dataRef.current;
    const instance = useRefWithInit(() => data.hoverInteractionState ?? HoverInteraction.create()).current;
    if (!data.hoverInteractionState) {
      data.hoverInteractionState = instance;
    }
    useOnMount(data.hoverInteractionState.disposeEffect);
    return data.hoverInteractionState;
  }

  // node_modules/@base-ui/react/floating-ui-react/hooks/useHoverFloatingInteraction.mjs
  function useHoverFloatingInteraction(context, parameters = {}) {
    const {
      enabled = true,
      closeDelay: closeDelayProp = 0,
      nodeId: nodeIdProp
    } = parameters;
    const store2 = "rootStore" in context ? context.rootStore : context;
    const open3 = store2.useState("open");
    const floatingElement = store2.useState("floatingElement");
    const domReferenceElement = store2.useState("domReferenceElement");
    const {
      dataRef
    } = store2.context;
    const tree = useFloatingTree();
    const parentId = useFloatingParentNodeId();
    const instance = useHoverInteractionSharedState(store2);
    const childClosedTimeout = useTimeout();
    const isClickLikeOpenEvent2 = useStableCallback(() => {
      return isClickLikeOpenEvent(dataRef.current.openEvent?.type, instance.interactedInside);
    });
    const isHoverOpen = useStableCallback(() => {
      return isHoverOpenEvent(dataRef.current.openEvent?.type);
    });
    const clearPointerEvents = useStableCallback(() => {
      clearSafePolygonPointerEventsMutation(instance);
    });
    useIsoLayoutEffect(() => {
      if (!open3) {
        instance.pointerType = void 0;
        instance.restTimeoutPending = false;
        instance.interactedInside = false;
        clearPointerEvents();
      }
    }, [open3, instance, clearPointerEvents]);
    React33.useEffect(() => {
      return clearPointerEvents;
    }, [clearPointerEvents]);
    useIsoLayoutEffect(() => {
      if (!enabled) {
        return void 0;
      }
      if (open3 && instance.handleCloseOptions?.blockPointerEvents && isHoverOpen() && isElement(domReferenceElement) && floatingElement) {
        const ref = domReferenceElement;
        const floatingEl = floatingElement;
        const doc = ownerDocument(floatingElement);
        const parentFloating = tree?.nodesRef.current.find((node) => node.id === parentId)?.context?.elements.floating;
        if (parentFloating) {
          parentFloating.style.pointerEvents = "";
        }
        const cachedScopeElement = instance.pointerEventsScopeElement !== floatingEl ? instance.pointerEventsScopeElement : null;
        const parentScopeElement = parentFloating !== floatingEl ? parentFloating : null;
        const scopeElement = instance.handleCloseOptions?.getScope?.() ?? cachedScopeElement ?? parentScopeElement ?? ref.closest("[data-rootownerid]") ?? doc.body;
        applySafePolygonPointerEventsMutation(instance, {
          scopeElement,
          referenceElement: ref,
          floatingElement: floatingEl
        });
        return () => {
          clearPointerEvents();
        };
      }
      return void 0;
    }, [enabled, open3, domReferenceElement, floatingElement, instance, isHoverOpen, tree, parentId, clearPointerEvents]);
    React33.useEffect(() => {
      if (!enabled) {
        return void 0;
      }
      function hasParentChildren() {
        return !!(tree && parentId && getNodeChildren(tree.nodesRef.current, parentId).length > 0);
      }
      function closeWithDelay(event) {
        const closeDelay = getDelay(closeDelayProp, "close", instance.pointerType);
        const close = () => {
          store2.setOpen(false, createChangeEventDetails(reason_parts_exports.triggerHover, event));
          tree?.events.emit("floating.closed", event);
        };
        if (closeDelay) {
          instance.openChangeTimeout.start(closeDelay, close);
        } else {
          instance.openChangeTimeout.clear();
          close();
        }
      }
      function handleInteractInside(event) {
        const target = getTarget(event);
        if (!isInteractiveElement(target)) {
          instance.interactedInside = false;
          return;
        }
        instance.interactedInside = target?.closest("[aria-haspopup]") != null;
      }
      function onFloatingMouseEnter() {
        instance.openChangeTimeout.clear();
        childClosedTimeout.clear();
        tree?.events.off("floating.closed", onNodeClosed);
        clearPointerEvents();
      }
      function onFloatingMouseLeave(event) {
        if (hasParentChildren() && tree) {
          tree.events.on("floating.closed", onNodeClosed);
          return;
        }
        if (isTargetInsideEnabledTrigger(event.relatedTarget, store2.context.triggerElements)) {
          return;
        }
        const currentNodeId = dataRef.current.floatingContext?.nodeId ?? nodeIdProp;
        const relatedTarget = event.relatedTarget;
        const isMovingIntoDescendantFloating = tree && currentNodeId && isElement(relatedTarget) && getNodeChildren(tree.nodesRef.current, currentNodeId, false).some((node) => contains(node.context?.elements.floating, relatedTarget));
        if (isMovingIntoDescendantFloating) {
          return;
        }
        if (instance.handler) {
          instance.handler(event);
          return;
        }
        clearPointerEvents();
        if (isHoverOpen() && !isClickLikeOpenEvent2()) {
          closeWithDelay(event);
        }
      }
      function onNodeClosed(event) {
        if (!tree || !parentId || hasParentChildren()) {
          return;
        }
        childClosedTimeout.start(0, () => {
          tree.events.off("floating.closed", onNodeClosed);
          store2.setOpen(false, createChangeEventDetails(reason_parts_exports.triggerHover, event));
          tree.events.emit("floating.closed", event);
        });
      }
      const floating = floatingElement;
      return mergeCleanups(floating && addEventListener(floating, "mouseenter", onFloatingMouseEnter), floating && addEventListener(floating, "mouseleave", onFloatingMouseLeave), floating && addEventListener(floating, "pointerdown", handleInteractInside, true), () => {
        tree?.events.off("floating.closed", onNodeClosed);
      });
    }, [enabled, floatingElement, store2, dataRef, closeDelayProp, nodeIdProp, isHoverOpen, isClickLikeOpenEvent2, clearPointerEvents, instance, tree, parentId, childClosedTimeout]);
  }

  // node_modules/@base-ui/react/floating-ui-react/hooks/useHoverReferenceInteraction.mjs
  var React34 = __toESM(require_react(), 1);
  var ReactDOM5 = __toESM(require_react_dom(), 1);
  var EMPTY_REF = {
    current: null
  };
  function useHoverReferenceInteraction(context, props = {}) {
    const {
      enabled = true,
      delay = 0,
      handleClose = null,
      mouseOnly = false,
      restMs = 0,
      move = true,
      triggerElementRef = EMPTY_REF,
      externalTree,
      isActiveTrigger = true,
      getHandleCloseContext,
      isClosing,
      shouldOpen: shouldOpenProp,
      guardStaleOpen = false
    } = props;
    const store2 = "rootStore" in context ? context.rootStore : context;
    const {
      dataRef,
      events
    } = store2.context;
    const tree = useFloatingTree(externalTree);
    const instance = useHoverInteractionSharedState(store2);
    const isHoverCloseActiveRef = React34.useRef(false);
    const handleCloseRef = useValueAsRef(handleClose);
    const delayRef = useValueAsRef(delay);
    const restMsRef = useValueAsRef(restMs);
    const enabledRef = useValueAsRef(enabled);
    const shouldOpenRef = useValueAsRef(shouldOpenProp);
    const isClosingRef = useValueAsRef(isClosing);
    const isClickLikeOpenEvent2 = useStableCallback(() => {
      return isClickLikeOpenEvent(dataRef.current.openEvent?.type, instance.interactedInside);
    });
    const checkShouldOpen = useStableCallback(() => {
      return shouldOpenRef.current?.() !== false;
    });
    const isOverInactiveTrigger = useStableCallback((currentDomReference, currentTarget, target) => {
      const allTriggers = store2.context.triggerElements;
      if (allTriggers.hasElement(currentTarget)) {
        return !currentDomReference || !contains(currentDomReference, currentTarget);
      }
      if (!isElement(target)) {
        return false;
      }
      const targetElement = target;
      return allTriggers.hasMatchingElement((trigger) => contains(trigger, targetElement)) && (!currentDomReference || !contains(currentDomReference, targetElement));
    });
    const cleanupMouseMoveHandler = useStableCallback(() => {
      if (!instance.handler) {
        return;
      }
      const doc = ownerDocument(store2.select("domReferenceElement"));
      doc.removeEventListener("mousemove", instance.handler);
      instance.handler = void 0;
    });
    const clearPointerEvents = useStableCallback(() => {
      clearSafePolygonPointerEventsMutation(instance);
    });
    if (isActiveTrigger) {
      instance.handleCloseOptions = handleClose?.__options;
    }
    React34.useEffect(() => cleanupMouseMoveHandler, [cleanupMouseMoveHandler]);
    React34.useEffect(() => {
      if (!enabled) {
        return void 0;
      }
      function onOpenChangeLocal(details) {
        if (!details.open) {
          isHoverCloseActiveRef.current = details.reason === reason_parts_exports.triggerHover;
          cleanupMouseMoveHandler();
          instance.openChangeTimeout.clear();
          instance.restTimeout.clear();
          instance.blockMouseMove = true;
          instance.restTimeoutPending = false;
        } else {
          isHoverCloseActiveRef.current = false;
        }
      }
      events.on("openchange", onOpenChangeLocal);
      return () => {
        events.off("openchange", onOpenChangeLocal);
      };
    }, [enabled, events, instance, cleanupMouseMoveHandler]);
    React34.useEffect(() => {
      if (!enabled) {
        return void 0;
      }
      function closeWithDelay(event, runElseBranch = true) {
        const closeDelay = getDelay(delayRef.current, "close", instance.pointerType);
        if (closeDelay) {
          instance.openChangeTimeout.start(closeDelay, () => {
            store2.setOpen(false, createChangeEventDetails(reason_parts_exports.triggerHover, event));
            tree?.events.emit("floating.closed", event);
          });
        } else if (runElseBranch) {
          instance.openChangeTimeout.clear();
          store2.setOpen(false, createChangeEventDetails(reason_parts_exports.triggerHover, event));
          tree?.events.emit("floating.closed", event);
        }
      }
      const trigger = triggerElementRef.current ?? (isActiveTrigger ? store2.select("domReferenceElement") : null);
      if (!isElement(trigger)) {
        return void 0;
      }
      function onMouseEnter(event) {
        instance.openChangeTimeout.clear();
        instance.blockMouseMove = false;
        if (mouseOnly && !isMouseLikePointerType(instance.pointerType)) {
          return;
        }
        const restMsValue = getRestMs(restMsRef.current);
        const openDelay = getDelay(delayRef.current, "open", instance.pointerType);
        const eventTarget = getTarget(event);
        const currentTarget = event.currentTarget ?? null;
        const currentDomReference = store2.select("domReferenceElement");
        let triggerNode = currentTarget;
        if (isElement(eventTarget) && !store2.context.triggerElements.hasElement(eventTarget)) {
          for (const triggerElement of store2.context.triggerElements.elements()) {
            if (contains(triggerElement, eventTarget)) {
              triggerNode = triggerElement;
              break;
            }
          }
        }
        if (isElement(currentTarget) && isElement(currentDomReference) && !store2.context.triggerElements.hasElement(currentTarget) && contains(currentTarget, currentDomReference)) {
          triggerNode = currentDomReference;
        }
        const isOverInactive = triggerNode == null ? false : isOverInactiveTrigger(currentDomReference, triggerNode, eventTarget);
        const isOpen = store2.select("open");
        const isInClosingTransition = isClosingRef.current?.() ?? store2.select("transitionStatus") === "ending";
        const isHoverCloseTransition = !isOpen && isInClosingTransition && isHoverCloseActiveRef.current;
        const isReenteringSameTriggerDuringCloseTransition = !isOverInactive && isElement(triggerNode) && isElement(currentDomReference) && contains(currentDomReference, triggerNode) && isHoverCloseTransition;
        const isRestOnlyDelay = restMsValue > 0 && !openDelay;
        const shouldOpenImmediately = isOverInactive && (isOpen || isHoverCloseTransition) || isReenteringSameTriggerDuringCloseTransition;
        const shouldOpen = !isOpen || isOverInactive;
        if (shouldOpenImmediately) {
          if (checkShouldOpen()) {
            store2.setOpen(true, createChangeEventDetails(reason_parts_exports.triggerHover, event, triggerNode));
          }
          return;
        }
        if (isRestOnlyDelay) {
          return;
        }
        if (openDelay) {
          instance.openChangeTimeout.start(openDelay, () => {
            if (shouldOpen && checkShouldOpen()) {
              store2.setOpen(true, createChangeEventDetails(reason_parts_exports.triggerHover, event, triggerNode));
            }
          });
        } else if (shouldOpen) {
          if (checkShouldOpen()) {
            store2.setOpen(true, createChangeEventDetails(reason_parts_exports.triggerHover, event, triggerNode));
          }
        }
      }
      function onMouseLeave(event) {
        if (isClickLikeOpenEvent2()) {
          clearPointerEvents();
          return;
        }
        cleanupMouseMoveHandler();
        const domReferenceElement = store2.select("domReferenceElement");
        const doc = ownerDocument(domReferenceElement);
        instance.restTimeout.clear();
        instance.restTimeoutPending = false;
        const handleCloseContextBase = dataRef.current.floatingContext ?? getHandleCloseContext?.();
        if (isTargetInsideEnabledTrigger(event.relatedTarget, store2.context.triggerElements)) {
          return;
        }
        if (handleCloseRef.current && handleCloseContextBase) {
          if (!store2.select("open")) {
            instance.openChangeTimeout.clear();
          }
          const currentTrigger = triggerElementRef.current;
          instance.handler = handleCloseRef.current({
            ...handleCloseContextBase,
            tree,
            x: event.clientX,
            y: event.clientY,
            onClose() {
              clearPointerEvents();
              cleanupMouseMoveHandler();
              if (enabledRef.current && !isClickLikeOpenEvent2() && currentTrigger === store2.select("domReferenceElement")) {
                closeWithDelay(event, true);
              }
            }
          });
          doc.addEventListener("mousemove", instance.handler);
          instance.handler(event);
          return;
        }
        const shouldClose = instance.pointerType === "touch" ? !contains(store2.select("floatingElement"), event.relatedTarget) : true;
        if (shouldClose) {
          closeWithDelay(event);
        }
      }
      function onMouseOut(event) {
        if (contains(trigger, event.relatedTarget)) {
          return;
        }
        instance.openChangeTimeout.clear();
        instance.restTimeout.clear();
        instance.restTimeoutPending = false;
      }
      const staleOpenGuard = guardStaleOpen ? addEventListener(trigger, "mouseout", onMouseOut) : void 0;
      if (move) {
        return mergeCleanups(addEventListener(trigger, "mousemove", onMouseEnter, {
          once: true
        }), addEventListener(trigger, "mouseenter", onMouseEnter), addEventListener(trigger, "mouseleave", onMouseLeave), staleOpenGuard);
      }
      return mergeCleanups(addEventListener(trigger, "mouseenter", onMouseEnter), addEventListener(trigger, "mouseleave", onMouseLeave), staleOpenGuard);
    }, [cleanupMouseMoveHandler, clearPointerEvents, dataRef, delayRef, store2, enabled, handleCloseRef, instance, isActiveTrigger, isOverInactiveTrigger, isClickLikeOpenEvent2, mouseOnly, move, restMsRef, triggerElementRef, tree, enabledRef, getHandleCloseContext, isClosingRef, checkShouldOpen, guardStaleOpen]);
    return React34.useMemo(() => {
      if (!enabled) {
        return void 0;
      }
      function setPointerRef(event) {
        instance.pointerType = event.pointerType;
      }
      return {
        onPointerDown: setPointerRef,
        onPointerEnter: setPointerRef,
        onMouseMove(event) {
          const {
            nativeEvent
          } = event;
          const trigger = event.currentTarget;
          const currentDomReference = store2.select("domReferenceElement");
          const currentOpen = store2.select("open");
          const isOverInactive = isOverInactiveTrigger(currentDomReference, trigger, event.target);
          if (mouseOnly && !isMouseLikePointerType(instance.pointerType)) {
            return;
          }
          if (currentOpen && isOverInactive && instance.handleCloseOptions?.blockPointerEvents) {
            const floatingElement = store2.select("floatingElement");
            if (floatingElement) {
              const scopeElement = instance.handleCloseOptions?.getScope?.() ?? trigger.ownerDocument.body;
              applySafePolygonPointerEventsMutation(instance, {
                scopeElement,
                referenceElement: trigger,
                floatingElement
              });
            }
          }
          const restMsValue = getRestMs(restMsRef.current);
          if (currentOpen && !isOverInactive || restMsValue === 0) {
            return;
          }
          if (!isOverInactive && instance.restTimeoutPending && event.movementX ** 2 + event.movementY ** 2 < 2) {
            return;
          }
          instance.restTimeout.clear();
          function handleMouseMove() {
            instance.restTimeoutPending = false;
            if (isClickLikeOpenEvent2()) {
              return;
            }
            const latestOpen = store2.select("open");
            if (!instance.blockMouseMove && (!latestOpen || isOverInactive) && checkShouldOpen()) {
              store2.setOpen(true, createChangeEventDetails(reason_parts_exports.triggerHover, nativeEvent, trigger));
            }
          }
          if (instance.pointerType === "touch") {
            ReactDOM5.flushSync(() => {
              handleMouseMove();
            });
          } else if (isOverInactive && currentOpen) {
            handleMouseMove();
          } else {
            instance.restTimeoutPending = true;
            instance.restTimeout.start(restMsValue, handleMouseMove);
          }
        }
      };
    }, [enabled, instance, isClickLikeOpenEvent2, isOverInactiveTrigger, mouseOnly, store2, restMsRef, checkShouldOpen]);
  }

  // node_modules/@base-ui/react/floating-ui-react/safePolygon.mjs
  var CURSOR_SPEED_THRESHOLD = 0.1;
  var CURSOR_SPEED_THRESHOLD_SQUARED = CURSOR_SPEED_THRESHOLD * CURSOR_SPEED_THRESHOLD;
  var POLYGON_BUFFER = 0.5;
  function hasIntersectingEdge(pointX, pointY, xi, yi, xj, yj) {
    return yi >= pointY !== yj >= pointY && pointX <= (xj - xi) * (pointY - yi) / (yj - yi) + xi;
  }
  function isPointInQuadrilateral(pointX, pointY, x1, y1, x2, y2, x3, y3, x4, y4) {
    let isInsideValue = false;
    if (hasIntersectingEdge(pointX, pointY, x1, y1, x2, y2)) {
      isInsideValue = !isInsideValue;
    }
    if (hasIntersectingEdge(pointX, pointY, x2, y2, x3, y3)) {
      isInsideValue = !isInsideValue;
    }
    if (hasIntersectingEdge(pointX, pointY, x3, y3, x4, y4)) {
      isInsideValue = !isInsideValue;
    }
    if (hasIntersectingEdge(pointX, pointY, x4, y4, x1, y1)) {
      isInsideValue = !isInsideValue;
    }
    return isInsideValue;
  }
  function isInsideRect(pointX, pointY, rect) {
    return pointX >= rect.x && pointX <= rect.x + rect.width && pointY >= rect.y && pointY <= rect.y + rect.height;
  }
  function isInsideAxisAlignedRect(pointX, pointY, x1, y1, x2, y2) {
    const minX = Math.min(x1, x2);
    const maxX = Math.max(x1, x2);
    const minY = Math.min(y1, y2);
    const maxY = Math.max(y1, y2);
    return pointX >= minX && pointX <= maxX && pointY >= minY && pointY <= maxY;
  }
  function safePolygon(options = {}) {
    const {
      blockPointerEvents = false
    } = options;
    const timeout = new Timeout();
    const fn = ({
      x,
      y,
      placement,
      elements,
      onClose,
      nodeId,
      tree
    }) => {
      const side = placement?.split("-")[0];
      let hasLanded = false;
      let lastX = null;
      let lastY = null;
      let lastCursorTime = typeof performance !== "undefined" ? performance.now() : 0;
      function isCursorMovingSlowly(nextX, nextY) {
        const currentTime = performance.now();
        const elapsedTime = currentTime - lastCursorTime;
        if (lastX === null || lastY === null || elapsedTime === 0) {
          lastX = nextX;
          lastY = nextY;
          lastCursorTime = currentTime;
          return false;
        }
        const deltaX = nextX - lastX;
        const deltaY = nextY - lastY;
        const distanceSquared = deltaX * deltaX + deltaY * deltaY;
        const thresholdSquared = elapsedTime * elapsedTime * CURSOR_SPEED_THRESHOLD_SQUARED;
        lastX = nextX;
        lastY = nextY;
        lastCursorTime = currentTime;
        return distanceSquared < thresholdSquared;
      }
      function close() {
        timeout.clear();
        onClose();
      }
      return function onMouseMove(event) {
        timeout.clear();
        const domReference = elements.domReference;
        const floating = elements.floating;
        if (!domReference || !floating || side == null || x == null || y == null) {
          return void 0;
        }
        const {
          clientX,
          clientY
        } = event;
        const target = getTarget(event);
        const isLeave = event.type === "mouseleave";
        const isOverFloatingEl = contains(floating, target);
        const isOverReferenceEl = contains(domReference, target);
        if (isOverFloatingEl) {
          hasLanded = true;
          if (!isLeave) {
            return void 0;
          }
        }
        if (isOverReferenceEl) {
          hasLanded = false;
          if (!isLeave) {
            hasLanded = true;
            return void 0;
          }
        }
        if (isLeave && isElement(event.relatedTarget) && contains(floating, event.relatedTarget)) {
          return void 0;
        }
        function hasOpenChildNode() {
          return Boolean(tree && getNodeChildren(tree.nodesRef.current, nodeId).length > 0);
        }
        function closeIfNoOpenChild() {
          if (!hasOpenChildNode()) {
            close();
          }
        }
        if (hasOpenChildNode()) {
          return void 0;
        }
        const refRect = domReference.getBoundingClientRect();
        const rect = floating.getBoundingClientRect();
        const cursorLeaveFromRight = x > rect.right - rect.width / 2;
        const cursorLeaveFromBottom = y > rect.bottom - rect.height / 2;
        const isFloatingWider = rect.width > refRect.width;
        const isFloatingTaller = rect.height > refRect.height;
        const left = (isFloatingWider ? refRect : rect).left;
        const right = (isFloatingWider ? refRect : rect).right;
        const top = (isFloatingTaller ? refRect : rect).top;
        const bottom = (isFloatingTaller ? refRect : rect).bottom;
        if (side === "top" && y >= refRect.bottom - 1 || side === "bottom" && y <= refRect.top + 1 || side === "left" && x >= refRect.right - 1 || side === "right" && x <= refRect.left + 1) {
          closeIfNoOpenChild();
          return void 0;
        }
        let isInsideTroughRect = false;
        switch (side) {
          case "top":
            isInsideTroughRect = isInsideAxisAlignedRect(clientX, clientY, left, refRect.top + 1, right, rect.bottom - 1);
            break;
          case "bottom":
            isInsideTroughRect = isInsideAxisAlignedRect(clientX, clientY, left, rect.top + 1, right, refRect.bottom - 1);
            break;
          case "left":
            isInsideTroughRect = isInsideAxisAlignedRect(clientX, clientY, rect.right - 1, bottom, refRect.left + 1, top);
            break;
          case "right":
            isInsideTroughRect = isInsideAxisAlignedRect(clientX, clientY, refRect.right - 1, bottom, rect.left + 1, top);
            break;
          default:
        }
        if (isInsideTroughRect) {
          return void 0;
        }
        if (hasLanded && !isInsideRect(clientX, clientY, refRect)) {
          closeIfNoOpenChild();
          return void 0;
        }
        if (!isLeave && isCursorMovingSlowly(clientX, clientY)) {
          closeIfNoOpenChild();
          return void 0;
        }
        let isInsidePolygon = false;
        switch (side) {
          case "top": {
            const cursorXOffset = isFloatingWider ? POLYGON_BUFFER / 2 : POLYGON_BUFFER * 4;
            const cursorPointOneX = isFloatingWider ? x + cursorXOffset : cursorLeaveFromRight ? x + cursorXOffset : x - cursorXOffset;
            const cursorPointTwoX = isFloatingWider ? x - cursorXOffset : cursorLeaveFromRight ? x + cursorXOffset : x - cursorXOffset;
            const cursorPointY = y + POLYGON_BUFFER + 1;
            const commonYLeft = cursorLeaveFromRight ? rect.bottom - POLYGON_BUFFER : isFloatingWider ? rect.bottom - POLYGON_BUFFER : rect.top;
            const commonYRight = cursorLeaveFromRight ? isFloatingWider ? rect.bottom - POLYGON_BUFFER : rect.top : rect.bottom - POLYGON_BUFFER;
            isInsidePolygon = isPointInQuadrilateral(clientX, clientY, cursorPointOneX, cursorPointY, cursorPointTwoX, cursorPointY, rect.left, commonYLeft, rect.right, commonYRight);
            break;
          }
          case "bottom": {
            const cursorXOffset = isFloatingWider ? POLYGON_BUFFER / 2 : POLYGON_BUFFER * 4;
            const cursorPointOneX = isFloatingWider ? x + cursorXOffset : cursorLeaveFromRight ? x + cursorXOffset : x - cursorXOffset;
            const cursorPointTwoX = isFloatingWider ? x - cursorXOffset : cursorLeaveFromRight ? x + cursorXOffset : x - cursorXOffset;
            const cursorPointY = y - POLYGON_BUFFER;
            const commonYLeft = cursorLeaveFromRight ? rect.top + POLYGON_BUFFER : isFloatingWider ? rect.top + POLYGON_BUFFER : rect.bottom;
            const commonYRight = cursorLeaveFromRight ? isFloatingWider ? rect.top + POLYGON_BUFFER : rect.bottom : rect.top + POLYGON_BUFFER;
            isInsidePolygon = isPointInQuadrilateral(clientX, clientY, cursorPointOneX, cursorPointY, cursorPointTwoX, cursorPointY, rect.left, commonYLeft, rect.right, commonYRight);
            break;
          }
          case "left": {
            const cursorYOffset = isFloatingTaller ? POLYGON_BUFFER / 2 : POLYGON_BUFFER * 4;
            const cursorPointOneY = isFloatingTaller ? y + cursorYOffset : cursorLeaveFromBottom ? y + cursorYOffset : y - cursorYOffset;
            const cursorPointTwoY = isFloatingTaller ? y - cursorYOffset : cursorLeaveFromBottom ? y + cursorYOffset : y - cursorYOffset;
            const cursorPointX = x + POLYGON_BUFFER + 1;
            const commonXTop = cursorLeaveFromBottom ? rect.right - POLYGON_BUFFER : isFloatingTaller ? rect.right - POLYGON_BUFFER : rect.left;
            const commonXBottom = cursorLeaveFromBottom ? isFloatingTaller ? rect.right - POLYGON_BUFFER : rect.left : rect.right - POLYGON_BUFFER;
            isInsidePolygon = isPointInQuadrilateral(clientX, clientY, commonXTop, rect.top, commonXBottom, rect.bottom, cursorPointX, cursorPointOneY, cursorPointX, cursorPointTwoY);
            break;
          }
          case "right": {
            const cursorYOffset = isFloatingTaller ? POLYGON_BUFFER / 2 : POLYGON_BUFFER * 4;
            const cursorPointOneY = isFloatingTaller ? y + cursorYOffset : cursorLeaveFromBottom ? y + cursorYOffset : y - cursorYOffset;
            const cursorPointTwoY = isFloatingTaller ? y - cursorYOffset : cursorLeaveFromBottom ? y + cursorYOffset : y - cursorYOffset;
            const cursorPointX = x - POLYGON_BUFFER;
            const commonXTop = cursorLeaveFromBottom ? rect.left + POLYGON_BUFFER : isFloatingTaller ? rect.left + POLYGON_BUFFER : rect.right;
            const commonXBottom = cursorLeaveFromBottom ? isFloatingTaller ? rect.left + POLYGON_BUFFER : rect.right : rect.left + POLYGON_BUFFER;
            isInsidePolygon = isPointInQuadrilateral(clientX, clientY, cursorPointX, cursorPointOneY, cursorPointX, cursorPointTwoY, commonXTop, rect.top, commonXBottom, rect.bottom);
            break;
          }
          default:
        }
        if (!isInsidePolygon) {
          closeIfNoOpenChild();
        } else if (!hasLanded) {
          timeout.start(40, closeIfNoOpenChild);
        }
        return void 0;
      };
    };
    fn.__options = {
      ...options,
      blockPointerEvents
    };
    return fn;
  }

  // node_modules/@base-ui/react/utils/NullStore.mjs
  var NullStore = class extends ReactStore {
    // `update`/`set`/`notifyAll` funnel through `setState` in the base `Store`, so overriding
    // `setState` alone would neutralize them today. They are overridden explicitly so the store stays
    // inert even if a future base-class change stops routing a mutator through `setState`.
    setState(_newState) {
    }
    update(_changes) {
    }
    set(_key, _value) {
    }
    notifyAll() {
    }
  };

  // node_modules/@base-ui/utils/inertValue.mjs
  function inertValue(value) {
    if (isReactVersionAtLeast(19)) {
      return value;
    }
    return value ? "true" : void 0;
  }

  // node_modules/@base-ui/react/internals/direction-context/DirectionContext.mjs
  var React35 = __toESM(require_react(), 1);
  var DirectionContext = /* @__PURE__ */ React35.createContext(void 0);
  if (true) DirectionContext.displayName = "DirectionContext";
  function useDirection() {
    const context = React35.useContext(DirectionContext);
    return context?.direction ?? "ltr";
  }

  // node_modules/@base-ui/react/internals/useAnchorPositioning.mjs
  var React36 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/floating-ui-react/middleware/arrow.mjs
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
        padding = 0,
        offsetParent = "real"
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
      const arrowOffsetParent = offsetParent === "real" ? await platform3.getOffsetParent?.(element) : elements.floating;
      let clientSize = elements.floating[clientProp] || rects.floating[length];
      if (!clientSize || !await platform3.isElement?.(arrowOffsetParent)) {
        clientSize = elements.floating[clientProp] || rects.floating[length];
      }
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
      name,
      fn
    } = baseArrow(options);
    return {
      name,
      fn,
      options: [options, deps]
    };
  };

  // node_modules/@base-ui/react/utils/hideMiddleware.mjs
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

  // node_modules/@base-ui/react/utils/adaptiveOriginConstants.mjs
  var DEFAULT_SIDES = {
    sideX: "left",
    sideY: "top"
  };

  // node_modules/@base-ui/react/utils/CommonPositionerCssVars.mjs
  var availableWidth = "--available-width";
  var availableHeight = "--available-height";
  var anchorWidth = "--anchor-width";
  var anchorHeight = "--anchor-height";
  var transformOrigin = "--transform-origin";
  var positionerWidth = "--positioner-width";
  var positionerHeight = "--positioner-height";

  // node_modules/@base-ui/react/internals/useAnchorPositioning.mjs
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
      adaptiveOrigin: adaptiveOrigin2,
      lazyFlip = false,
      externalTree
    } = params;
    const [mountSide, setMountSide] = React36.useState(null);
    if (!mounted && mountSide !== null) {
      setMountSide(null);
    }
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
    const side = mountSide || {
      top: "top",
      right: "right",
      bottom: "bottom",
      left: "left",
      "inline-end": isRtl ? "left" : "right",
      "inline-start": isRtl ? "right" : "left"
    }[sideParam];
    const placement = align === "center" ? side : `${side}-${align}`;
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
    const arrowRef = React36.useRef(null);
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
    if (collisionAvoidanceSide === "shift" || collisionAvoidanceAlign === "shift" || align === "center") {
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
      padding: arrowRef.current ? arrowPadding : 0,
      offsetParent: "floating"
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
    }, hide4, adaptiveOrigin2);
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
    const autoUpdateOptions = React36.useMemo(() => ({
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
    const floatingStyles = React36.useMemo(() => {
      let base;
      if (!isPositioned) {
        base = {
          position: resolvedPosition,
          top: 0,
          left: 0
        };
      } else if (adaptiveOrigin2) {
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
    }, [adaptiveOrigin2, resolvedPosition, sideX, x, sideY, y, originalFloatingStyles, isPositioned]);
    const registeredPositionReferenceRef = React36.useRef(null);
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
    React36.useEffect(() => {
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
    React36.useEffect(() => {
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
      if (lazyFlip && mounted && isPositioned && renderedSide !== side) {
        setMountSide(renderedSide);
      }
    }, [lazyFlip, mounted, isPositioned, renderedSide, side]);
    const arrowStyles = React36.useMemo(() => ({
      position: "absolute",
      top: middlewareData.arrow?.y,
      left: middlewareData.arrow?.x
    }), [middlewareData.arrow]);
    const arrowUncentered = middlewareData.arrow?.centerOffset !== 0;
    return React36.useMemo(() => ({
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

  // node_modules/@base-ui/react/internals/getDisabledMountTransitionStyles.mjs
  function getDisabledMountTransitionStyles(transitionStatus) {
    return transitionStatus === "starting" ? DISABLED_TRANSITIONS_STYLE : EMPTY_OBJECT;
  }

  // node_modules/@base-ui/react/utils/usePositioner.mjs
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

  // node_modules/@base-ui/react/button/Button.mjs
  var React37 = __toESM(require_react(), 1);
  var Button = /* @__PURE__ */ React37.forwardRef(function Button2(componentProps, forwardedRef) {
    const {
      render,
      className,
      disabled: disabled2 = false,
      focusableWhenDisabled = false,
      nativeButton = true,
      style,
      ...elementProps
    } = componentProps;
    const {
      getButtonProps,
      buttonRef
    } = useButton({
      disabled: disabled2,
      focusableWhenDisabled,
      native: nativeButton
    });
    const state = {
      disabled: disabled2
    };
    return useRenderElement("button", componentProps, {
      state,
      ref: [forwardedRef, buttonRef],
      props: [elementProps, getButtonProps]
    });
  });
  if (true) Button.displayName = "Button";

  // node_modules/@base-ui/react/collapsible/index.parts.mjs
  var index_parts_exports = {};
  __export(index_parts_exports, {
    Panel: () => CollapsiblePanel,
    Root: () => CollapsibleRoot,
    Trigger: () => CollapsibleTrigger
  });

  // node_modules/@base-ui/react/collapsible/root/CollapsibleRoot.mjs
  var React38 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/collapsible/root/stateAttributesMapping.mjs
  var collapsibleStateAttributesMapping = {
    ...collapsibleOpenStateMapping,
    ...transitionStatusMapping
  };

  // node_modules/@base-ui/react/collapsible/root/CollapsibleRoot.mjs
  var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
  var CollapsibleRoot = /* @__PURE__ */ React38.forwardRef(function CollapsibleRoot2(componentProps, forwardedRef) {
    const {
      render,
      className,
      defaultOpen = false,
      disabled: disabled2 = false,
      onOpenChange: onOpenChangeProp,
      open: open3,
      style,
      ...elementProps
    } = componentProps;
    const onOpenChange = useStableCallback(onOpenChangeProp);
    const collapsible = useCollapsibleRoot({
      open: open3,
      defaultOpen,
      onOpenChange,
      disabled: disabled2
    });
    const state = React38.useMemo(() => ({
      open: collapsible.open,
      disabled: collapsible.disabled,
      transitionStatus: collapsible.transitionStatus
    }), [collapsible.open, collapsible.disabled, collapsible.transitionStatus]);
    const contextValue = React38.useMemo(() => ({
      ...collapsible,
      onOpenChange,
      state
    }), [collapsible, onOpenChange, state]);
    const element = useRenderElement("div", componentProps, {
      state,
      ref: forwardedRef,
      props: elementProps,
      stateAttributesMapping: collapsibleStateAttributesMapping
    });
    return /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(CollapsibleRootContext.Provider, {
      value: contextValue,
      children: element
    });
  });
  if (true) CollapsibleRoot.displayName = "CollapsibleRoot";

  // node_modules/@base-ui/react/collapsible/trigger/CollapsibleTrigger.mjs
  var React39 = __toESM(require_react(), 1);
  var stateAttributesMapping = {
    ...triggerOpenStateMapping,
    ...transitionStatusMapping
  };
  var CollapsibleTrigger = /* @__PURE__ */ React39.forwardRef(function CollapsibleTrigger2(componentProps, forwardedRef) {
    const {
      panelId,
      open: open3,
      handleTrigger,
      state,
      disabled: contextDisabled
    } = useCollapsibleRootContext();
    const {
      className,
      disabled: disabled2 = contextDisabled,
      render,
      nativeButton = true,
      style,
      ...elementProps
    } = componentProps;
    const {
      getButtonProps,
      buttonRef
    } = useButton({
      disabled: disabled2,
      focusableWhenDisabled: true,
      native: nativeButton
    });
    const element = useRenderElement("button", componentProps, {
      state,
      ref: [forwardedRef, buttonRef],
      props: [{
        "aria-controls": open3 ? panelId : void 0,
        "aria-expanded": open3,
        onClick: handleTrigger
      }, elementProps, getButtonProps],
      stateAttributesMapping
    });
    return element;
  });
  if (true) CollapsibleTrigger.displayName = "CollapsibleTrigger";

  // node_modules/@base-ui/react/collapsible/panel/CollapsiblePanel.mjs
  var React40 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/collapsible/panel/CollapsiblePanelCssVars.mjs
  var collapsiblePanelHeight = "--collapsible-panel-height";
  var collapsiblePanelWidth = "--collapsible-panel-width";

  // node_modules/@base-ui/react/collapsible/panel/CollapsiblePanel.mjs
  var CollapsiblePanel = /* @__PURE__ */ React40.forwardRef(function CollapsiblePanel2(componentProps, forwardedRef) {
    const {
      className,
      hiddenUntilFound: hiddenUntilFoundProp,
      keepMounted: keepMountedProp,
      render,
      id: idProp,
      style,
      ...elementProps
    } = componentProps;
    if (true) {
      React40.useEffect(() => {
        if (hiddenUntilFoundProp && keepMountedProp === false) {
          warn("The `keepMounted={false}` prop on `Collapsible.Panel` is ignored when `hiddenUntilFound` is enabled, since the panel must remain mounted while closed.");
        }
      }, [hiddenUntilFoundProp, keepMountedProp]);
    }
    const {
      defaultPanelId,
      mounted,
      onOpenChange,
      open: open3,
      setMounted,
      setPanelIdState,
      setOpen,
      state,
      transitionStatus
    } = useCollapsibleRootContext();
    const hiddenUntilFound = hiddenUntilFoundProp ?? false;
    const keepMounted = keepMountedProp ?? false;
    const registeredId = idProp || void 0;
    const id = registeredId ?? defaultPanelId;
    useIsoLayoutEffect(() => {
      setPanelIdState((currentId) => registeredId ?? (currentId === null ? void 0 : currentId));
      return () => {
        setPanelIdState((currentId) => currentId === registeredId ? null : currentId);
      };
    }, [registeredId, setPanelIdState]);
    const {
      height,
      props,
      ref,
      shouldPreventOpenAnimation,
      shouldRender,
      transitionStatus: panelTransitionStatus,
      width
    } = useCollapsiblePanel({
      externalRef: forwardedRef,
      hiddenUntilFound,
      id,
      keepMounted,
      mounted,
      onOpenChange,
      open: open3,
      setMounted,
      setOpen,
      transitionStatus
    });
    const panelState = {
      ...state,
      transitionStatus: panelTransitionStatus
    };
    const resolvedStyle = resolveStyle(style, panelState);
    const element = useRenderElement("div", {
      ...componentProps,
      style: void 0
    }, {
      state: panelState,
      ref,
      props: [
        props,
        {
          style: {
            [collapsiblePanelHeight]: height === void 0 ? "auto" : `${height}px`,
            [collapsiblePanelWidth]: width === void 0 ? "auto" : `${width}px`
          }
        },
        elementProps,
        resolvedStyle ? {
          style: resolvedStyle
        } : void 0,
        // Resolve the public `style` prop so temporary `animationName: 'none'`
        // can still win after user's inline styles have been merged.
        shouldPreventOpenAnimation ? {
          style: {
            animationName: "none"
          }
        } : void 0
      ],
      stateAttributesMapping: collapsibleStateAttributesMapping
    });
    if (!shouldRender) {
      return null;
    }
    return element;
  });
  if (true) CollapsiblePanel.displayName = "CollapsiblePanel";

  // node_modules/@base-ui/react/utils/usePopupViewport.mjs
  var React44 = __toESM(require_react(), 1);
  var ReactDOM6 = __toESM(require_react_dom(), 1);

  // node_modules/@base-ui/utils/usePreviousValue.mjs
  var React41 = __toESM(require_react(), 1);
  function usePreviousValue(value) {
    const [state, setState] = React41.useState({
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

  // node_modules/@base-ui/react/utils/usePopupAutoResize.mjs
  var React42 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/utils/getCssDimensions.mjs
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

  // node_modules/@base-ui/react/utils/CommonPopupCssVars.mjs
  var popupWidth = "--popup-width";
  var popupHeight = "--popup-height";

  // node_modules/@base-ui/react/utils/usePopupAutoResize.mjs
  function usePopupAutoResize(parameters) {
    const {
      popupElement,
      positionerElement,
      content,
      mounted,
      onMeasureLayout: onMeasureLayoutParam,
      onMeasureLayoutComplete: onMeasureLayoutCompleteParam,
      side,
      direction
    } = parameters;
    const runOnceAnimationsFinish = useAnimationsFinished(popupElement, true);
    const animationFrame = useAnimationFrame();
    const committedDimensionsRef = React42.useRef(null);
    const isInitialRenderRef = React42.useRef(true);
    const restoreAnchoringStylesRef = React42.useRef(NOOP);
    const onMeasureLayout = useStableCallback(onMeasureLayoutParam);
    const onMeasureLayoutComplete = useStableCallback(onMeasureLayoutCompleteParam);
    const anchoringStyles = React42.useMemo(() => getPopupAnchoringStyles(side, direction), [side, direction]);
    useIsoLayoutEffect(() => {
      if (!mounted) {
        restoreAnchoringStylesRef.current = NOOP;
        isInitialRenderRef.current = true;
        committedDimensionsRef.current = null;
        return void 0;
      }
      if (!popupElement || !positionerElement) {
        return void 0;
      }
      restoreAnchoringStylesRef.current = applyElementStyles(popupElement, anchoringStyles);
      setPopupCssSize(popupElement, "auto");
      const restorePopupPosition = overrideElementStyle(popupElement, "position", "static");
      const restorePopupTransform = overrideElementStyle(popupElement, "transform", "none");
      const restorePopupScale = overrideElementStyle(popupElement, "scale", "1");
      const restorePositionerAvailableSize = applyElementStyles(positionerElement, {
        [availableWidth]: "max-content",
        [availableHeight]: "max-content"
      });
      function restoreMeasurementOverrides() {
        restorePopupPosition();
        restorePopupTransform();
        restorePositionerAvailableSize();
      }
      function restoreMeasurementOverridesIncludingScale() {
        restoreMeasurementOverrides();
        restorePopupScale();
      }
      onMeasureLayout?.();
      if (isInitialRenderRef.current || committedDimensionsRef.current === null) {
        setPositionerCssSize(positionerElement, "max-content");
        const dimensions = getCssDimensions2(popupElement);
        committedDimensionsRef.current = dimensions;
        setPositionerCssSize(positionerElement, dimensions);
        restoreMeasurementOverridesIncludingScale();
        onMeasureLayoutComplete?.(null, dimensions);
        isInitialRenderRef.current = false;
        return () => {
          restoreAnchoringStylesRef.current();
          restoreAnchoringStylesRef.current = NOOP;
        };
      }
      setPositionerCssSize(positionerElement, "max-content");
      const previousDimensions = committedDimensionsRef.current;
      const newDimensions = getCssDimensions2(popupElement);
      committedDimensionsRef.current = newDimensions;
      setPopupCssSize(popupElement, previousDimensions);
      restoreMeasurementOverridesIncludingScale();
      onMeasureLayoutComplete?.(previousDimensions, newDimensions);
      setPositionerCssSize(positionerElement, newDimensions);
      const abortController = new AbortController();
      animationFrame.request(() => {
        setPopupCssSize(popupElement, newDimensions);
        runOnceAnimationsFinish(() => {
          popupElement.style.setProperty(popupWidth, "auto");
          popupElement.style.setProperty(popupHeight, "auto");
        }, abortController.signal);
      });
      return () => {
        abortController.abort();
        animationFrame.cancel();
        restoreAnchoringStylesRef.current();
        restoreAnchoringStylesRef.current = NOOP;
      };
    }, [content, popupElement, positionerElement, runOnceAnimationsFinish, animationFrame, mounted, onMeasureLayout, onMeasureLayoutComplete, anchoringStyles]);
  }
  function getPopupAnchoringStyles(side, direction) {
    const isPhysicalTop = side === "top";
    const isPhysicalLeft = side === "left" || side === (direction === "rtl" ? "inline-end" : "inline-start");
    if (!isPhysicalTop && !isPhysicalLeft) {
      return EMPTY_OBJECT;
    }
    return {
      position: "absolute",
      [isPhysicalTop ? "bottom" : "top"]: "0",
      [isPhysicalLeft ? "right" : "left"]: "0"
    };
  }
  function overrideElementStyle(element, property, value) {
    const originalValue = element.style.getPropertyValue(property);
    element.style.setProperty(property, value);
    return () => {
      element.style.setProperty(property, originalValue);
    };
  }
  function applyElementStyles(element, styles) {
    const restorers = [];
    for (const [key, value] of Object.entries(styles)) {
      restorers.push(overrideElementStyle(element, key, value));
    }
    return restorers.length ? () => {
      restorers.forEach((restore) => restore());
    } : NOOP;
  }
  function setPopupCssSize(popupElement, size4) {
    const width = size4 === "auto" ? "auto" : `${size4.width}px`;
    const height = size4 === "auto" ? "auto" : `${size4.height}px`;
    popupElement.style.setProperty(popupWidth, width);
    popupElement.style.setProperty(popupHeight, height);
  }
  function setPositionerCssSize(positionerElement, size4) {
    const width = size4 === "max-content" ? "max-content" : `${size4.width}px`;
    const height = size4 === "max-content" ? "max-content" : `${size4.height}px`;
    positionerElement.style.setProperty(positionerWidth, width);
    positionerElement.style.setProperty(positionerHeight, height);
  }

  // node_modules/@base-ui/react/direction-provider/DirectionProvider.mjs
  var React43 = __toESM(require_react(), 1);
  var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
  var DirectionProvider = function DirectionProvider2(props) {
    const {
      direction = "ltr"
    } = props;
    const contextValue = React43.useMemo(() => ({
      direction
    }), [direction]);
    return /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(DirectionContext.Provider, {
      value: contextValue,
      children: props.children
    });
  };
  if (true) DirectionProvider.displayName = "DirectionProvider";

  // node_modules/@base-ui/react/utils/adaptiveOriginMiddleware.mjs
  var adaptiveOrigin = {
    name: "adaptiveOrigin",
    async fn(state) {
      const {
        x: rawX,
        y: rawY,
        rects: {
          floating: floatRect
        },
        elements: {
          floating
        },
        platform: platform3,
        strategy,
        placement
      } = state;
      const win = getWindow(floating);
      const styles = win.getComputedStyle(floating);
      const hasTransition = styles.transitionDuration !== "0s" && styles.transitionDuration !== "";
      if (!hasTransition) {
        return {
          x: rawX,
          y: rawY,
          data: DEFAULT_SIDES
        };
      }
      const offsetParent = await platform3.getOffsetParent?.(floating);
      let offsetDimensions = {
        width: 0,
        height: 0
      };
      if (strategy === "fixed" && win?.visualViewport) {
        offsetDimensions = {
          width: win.visualViewport.width,
          height: win.visualViewport.height
        };
      } else if (offsetParent === win) {
        const doc = ownerDocument(floating);
        offsetDimensions = {
          width: doc.documentElement.clientWidth,
          height: doc.documentElement.clientHeight
        };
      } else if (await platform3.isElement?.(offsetParent)) {
        offsetDimensions = await platform3.getDimensions(offsetParent);
      }
      const currentSide = getSide(placement);
      let x = rawX;
      let y = rawY;
      if (currentSide === "left") {
        x = offsetDimensions.width - (rawX + floatRect.width);
      }
      if (currentSide === "top") {
        y = offsetDimensions.height - (rawY + floatRect.height);
      }
      const sideX = currentSide === "left" ? "right" : DEFAULT_SIDES.sideX;
      const sideY = currentSide === "top" ? "bottom" : DEFAULT_SIDES.sideY;
      return {
        x,
        y,
        data: {
          sideX,
          sideY
        }
      };
    }
  };

  // node_modules/@base-ui/react/utils/CommonViewportDataAttributes.mjs
  var activationDirection = "data-activation-direction";

  // node_modules/@base-ui/react/utils/usePopupViewport.mjs
  var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
  var popupViewportStateMapping = {
    activationDirection: (value) => value ? {
      [activationDirection]: value
    } : null
  };
  function usePopupViewport(parameters) {
    const {
      store: store2,
      side,
      children
    } = parameters;
    const direction = useDirection();
    const activeTrigger = store2.useState("activeTriggerElement");
    const activeTriggerId = store2.useState("activeTriggerId");
    const open3 = store2.useState("open");
    const payload = store2.useState("payload");
    const mounted = store2.useState("mounted");
    const popupElement = store2.useState("popupElement");
    const positionerElement = store2.useState("positionerElement");
    const previousActiveTrigger = usePreviousValue(open3 ? activeTrigger : null);
    const currentContentKey = usePopupContentKey(activeTriggerId, payload);
    const capturedNodeRef = React44.useRef(null);
    const [previousContentNode, setPreviousContentNode] = React44.useState(null);
    const [newTriggerOffset, setNewTriggerOffset] = React44.useState(null);
    const currentContainerRef = React44.useRef(null);
    const previousContainerRef = React44.useRef(null);
    const onAnimationsFinished = useAnimationsFinished(currentContainerRef, true);
    const cleanupFrame = useAnimationFrame();
    const cleanupControllerRef = React44.useRef(null);
    const [previousContentDimensions, setPreviousContentDimensions] = React44.useState(null);
    const [showStartingStyleAttribute, setShowStartingStyleAttribute] = React44.useState(false);
    useIsoLayoutEffect(() => {
      store2.set("adaptiveOrigin", adaptiveOrigin);
      return () => {
        store2.set("adaptiveOrigin", void 0);
      };
    }, [store2]);
    const handleMeasureLayout = useStableCallback(() => {
      currentContainerRef.current?.style.setProperty("animation", "none");
      currentContainerRef.current?.style.setProperty("transition", "none");
      previousContainerRef.current?.style.setProperty("display", "none");
    });
    const handleMeasureLayoutComplete = useStableCallback((previousDimensions) => {
      currentContainerRef.current?.style.removeProperty("animation");
      currentContainerRef.current?.style.removeProperty("transition");
      previousContainerRef.current?.style.removeProperty("display");
      if (previousDimensions) {
        setPreviousContentDimensions(previousDimensions);
      }
    });
    const armViewportCleanup = useStableCallback(() => {
      cleanupControllerRef.current?.abort();
      const controller = new AbortController();
      cleanupControllerRef.current = controller;
      onAnimationsFinished(() => {
        setPreviousContentNode(null);
        setPreviousContentDimensions(null);
        capturedNodeRef.current = null;
      }, controller.signal);
    });
    const lastHandledTriggerRef = React44.useRef(null);
    useIsoLayoutEffect(() => {
      if (!open3 || !mounted) {
        lastHandledTriggerRef.current = null;
      }
    }, [open3, mounted]);
    useIsoLayoutEffect(() => {
      if (activeTrigger && previousActiveTrigger && activeTrigger !== previousActiveTrigger && lastHandledTriggerRef.current !== activeTrigger && capturedNodeRef.current) {
        setPreviousContentNode(capturedNodeRef.current);
        setShowStartingStyleAttribute(true);
        const offset4 = calculateRelativePosition(previousActiveTrigger, activeTrigger);
        setNewTriggerOffset(offset4);
        lastHandledTriggerRef.current = activeTrigger;
      }
    }, [activeTrigger, previousActiveTrigger]);
    useIsoLayoutEffect(() => {
      if (previousContentNode == null) {
        return;
      }
      cleanupControllerRef.current?.abort();
      setShowStartingStyleAttribute(true);
      cleanupFrame.request(() => {
        ReactDOM6.flushSync(() => {
          setShowStartingStyleAttribute(false);
        });
        armViewportCleanup();
      });
    }, [currentContentKey, previousContentNode, armViewportCleanup, cleanupFrame]);
    useIsoLayoutEffect(() => {
      const source = currentContainerRef.current;
      if (!source) {
        return;
      }
      const wrapper = ownerDocument(source).createElement("div");
      for (const child of Array.from(source.childNodes)) {
        wrapper.appendChild(child.cloneNode(true));
      }
      capturedNodeRef.current = wrapper;
    });
    const isTransitioning = previousContentNode != null;
    let childrenToRender;
    if (!isTransitioning) {
      childrenToRender = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)("div", {
        "data-current": true,
        ref: currentContainerRef,
        children
      }, currentContentKey);
    } else {
      childrenToRender = /* @__PURE__ */ (0, import_jsx_runtime7.jsxs)(React44.Fragment, {
        children: [/* @__PURE__ */ (0, import_jsx_runtime7.jsx)("div", {
          "data-previous": true,
          inert: inertValue(true),
          ref: previousContainerRef,
          style: {
            ...previousContentDimensions ? {
              [popupWidth]: `${previousContentDimensions.width}px`,
              [popupHeight]: `${previousContentDimensions.height}px`
            } : null,
            position: "absolute"
          },
          "data-ending-style": showStartingStyleAttribute ? void 0 : ""
        }, "previous"), /* @__PURE__ */ (0, import_jsx_runtime7.jsx)("div", {
          "data-current": true,
          ref: currentContainerRef,
          "data-starting-style": showStartingStyleAttribute ? "" : void 0,
          children
        }, currentContentKey)]
      });
    }
    useIsoLayoutEffect(() => {
      const container = previousContainerRef.current;
      if (!container || !previousContentNode) {
        return;
      }
      container.replaceChildren(...Array.from(previousContentNode.childNodes));
    }, [previousContentNode]);
    usePopupAutoResize({
      popupElement,
      positionerElement,
      mounted,
      content: payload,
      onMeasureLayout: handleMeasureLayout,
      onMeasureLayoutComplete: handleMeasureLayoutComplete,
      side,
      direction
    });
    const state = {
      activationDirection: getActivationDirection(newTriggerOffset),
      transitioning: isTransitioning
    };
    return {
      children: childrenToRender,
      state
    };
  }
  function getActivationDirection(offset4) {
    if (!offset4) {
      return void 0;
    }
    return `${getValueWithTolerance(offset4.horizontal, 5, "right", "left")} ${getValueWithTolerance(offset4.vertical, 5, "down", "up")}`;
  }
  function getValueWithTolerance(value, tolerance, positiveLabel, negativeLabel) {
    if (value > tolerance) {
      return positiveLabel;
    }
    if (value < -tolerance) {
      return negativeLabel;
    }
    return "";
  }
  function calculateRelativePosition(from, to) {
    const fromRect = from.getBoundingClientRect();
    const toRect = to.getBoundingClientRect();
    const fromCenter = {
      x: fromRect.left + fromRect.width / 2,
      y: fromRect.top + fromRect.height / 2
    };
    const toCenter = {
      x: toRect.left + toRect.width / 2,
      y: toRect.top + toRect.height / 2
    };
    return {
      horizontal: toCenter.x - fromCenter.x,
      vertical: toCenter.y - fromCenter.y
    };
  }
  function usePopupContentKey(activeTriggerId, payload) {
    const [contentKey, setContentKey] = React44.useState(0);
    const previousActiveTriggerIdRef = React44.useRef(activeTriggerId);
    const previousPayloadRef = React44.useRef(payload);
    const pendingPayloadUpdateRef = React44.useRef(false);
    useIsoLayoutEffect(() => {
      const previousActiveTriggerId = previousActiveTriggerIdRef.current;
      const previousPayload = previousPayloadRef.current;
      const triggerIdChanged = activeTriggerId !== previousActiveTriggerId;
      const payloadChanged = payload !== previousPayload;
      if (triggerIdChanged) {
        setContentKey((value) => value + 1);
        pendingPayloadUpdateRef.current = !payloadChanged;
      } else if (pendingPayloadUpdateRef.current && payloadChanged) {
        setContentKey((value) => value + 1);
        pendingPayloadUpdateRef.current = false;
      }
      previousActiveTriggerIdRef.current = activeTriggerId;
      previousPayloadRef.current = payload;
    }, [activeTriggerId, payload]);
    return `${activeTriggerId ?? "current"}-${contentKey}`;
  }

  // node_modules/@base-ui/react/utils/FloatingPortalLite.mjs
  var React45 = __toESM(require_react(), 1);
  var ReactDOM7 = __toESM(require_react_dom(), 1);
  var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
  var FloatingPortalLite = /* @__PURE__ */ React45.forwardRef(function FloatingPortalLite2(componentProps, forwardedRef) {
    const {
      children,
      container,
      className,
      render,
      style,
      ...elementProps
    } = componentProps;
    const {
      node: portalNode,
      subtree: portalSubtree
    } = useFloatingPortalNode({
      container,
      ref: forwardedRef,
      componentProps,
      elementProps
    });
    if (!portalSubtree && !portalNode) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)(React45.Fragment, {
      children: [portalSubtree, portalNode && /* @__PURE__ */ ReactDOM7.createPortal(children, portalNode)]
    });
  });
  if (true) FloatingPortalLite.displayName = "FloatingPortalLite";

  // node_modules/@base-ui/react/tooltip/index.parts.mjs
  var index_parts_exports2 = {};
  __export(index_parts_exports2, {
    Arrow: () => TooltipArrow,
    Handle: () => TooltipHandle,
    Popup: () => TooltipPopup,
    Portal: () => TooltipPortal,
    Positioner: () => TooltipPositioner,
    Provider: () => TooltipProvider,
    Root: () => TooltipRoot,
    Trigger: () => TooltipTrigger,
    Viewport: () => TooltipViewport,
    createHandle: () => createTooltipHandle
  });

  // node_modules/@base-ui/react/tooltip/root/TooltipRoot.mjs
  var React48 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/tooltip/root/TooltipRootContext.mjs
  var React46 = __toESM(require_react(), 1);
  var TooltipRootContext = /* @__PURE__ */ React46.createContext(void 0);
  if (true) TooltipRootContext.displayName = "TooltipRootContext";
  function useTooltipRootContext(optional) {
    const context = React46.useContext(TooltipRootContext);
    if (context === void 0 && !optional) {
      throw new Error(true ? "Base UI: TooltipRootContext is missing. Tooltip parts must be placed within <Tooltip.Root>." : formatErrorMessage_default(72));
    }
    return context;
  }

  // node_modules/@base-ui/react/tooltip/store/TooltipStore.mjs
  var React47 = __toESM(require_react(), 1);
  var selectors2 = {
    ...popupStoreSelectors,
    disabled: (state) => state.disabled,
    instantType: (state) => state.instantType,
    isInstantPhase: (state) => state.isInstantPhase,
    trackCursorAxis: (state) => state.trackCursorAxis,
    disableHoverablePopup: (state) => state.disableHoverablePopup,
    lastOpenChangeReason: (state) => state.openChangeReason,
    closeOnClick: (state) => state.closeOnClick,
    closeDelay: (state) => state.closeDelay,
    adaptiveOrigin: (state) => state.adaptiveOrigin
  };
  var TooltipStore = class extends ReactStore {
    constructor(initialState, floatingId, nested) {
      const triggerElements = new PopupTriggerMap();
      super(createInitialState(initialState, triggerElements, floatingId, nested), createInitialContext(triggerElements), selectors2);
    }
    setOpen = (nextOpen, eventDetails) => {
      applyPopupOpenChange(this, nextOpen, eventDetails, {
        extraState: {
          openChangeReason: eventDetails.reason
        }
      });
    };
    // Used by trigger clicks to clear a delayed hover open without reporting a public open-state change.
    cancelPendingOpen(event) {
      this.state.floatingRootContext.dispatchOpenChange(false, createChangeEventDetails(reason_parts_exports.triggerPress, event));
    }
  };
  function createNullTooltipStore() {
    const triggerElements = new PopupTriggerMap();
    const store2 = new NullStore(Object.freeze(createInitialState(void 0, triggerElements)), Object.freeze(createInitialContext(triggerElements)), selectors2);
    return Object.assign(store2, {
      setOpen: NOOP,
      cancelPendingOpen: NOOP
    });
  }
  function createInitialState(initialState, triggerElements, floatingId, nested = false) {
    const state = {
      ...createInitialPopupStoreState(triggerElements, floatingId, nested),
      disabled: false,
      instantType: void 0,
      isInstantPhase: false,
      trackCursorAxis: "none",
      disableHoverablePopup: false,
      openChangeReason: null,
      closeOnClick: true,
      closeDelay: 0,
      adaptiveOrigin: void 0,
      ...initialState
    };
    return state;
  }
  function createInitialContext(triggerElements) {
    return {
      popupRef: /* @__PURE__ */ React47.createRef(),
      onOpenChange: void 0,
      onOpenChangeComplete: void 0,
      triggerElements
    };
  }

  // node_modules/@base-ui/react/tooltip/root/TooltipRoot.mjs
  var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
  var TooltipRoot = fastComponent(function TooltipRoot2(props) {
    const {
      disabled: disabled2 = false,
      defaultOpen = false,
      open: openProp,
      disableHoverablePopup = false,
      trackCursorAxis = "none",
      actionsRef,
      onOpenChange,
      onOpenChangeComplete,
      handle,
      triggerId: triggerIdProp,
      defaultTriggerId: defaultTriggerIdProp = null,
      children
    } = props;
    const store2 = usePopupRootStore((floatingId, nested) => new TooltipStore({
      open: defaultOpen,
      openProp,
      activeTriggerId: defaultTriggerIdProp,
      triggerIdProp
    }, floatingId, nested));
    store2.useControlledProp("openProp", openProp);
    store2.useControlledProp("triggerIdProp", triggerIdProp);
    store2.useContextCallback("onOpenChange", onOpenChange);
    store2.useContextCallback("onOpenChangeComplete", onOpenChangeComplete);
    const openState = store2.useState("open");
    const open3 = !disabled2 && openState;
    const activeTriggerId = store2.useState("activeTriggerId");
    const mounted = store2.useState("mounted");
    const payload = store2.useState("payload");
    store2.useSyncedValues({
      trackCursorAxis,
      disableHoverablePopup,
      disabled: disabled2
    });
    useImplicitActiveTrigger(store2, {
      closeOnActiveTriggerUnmount: true
    });
    const {
      forceUnmount,
      transitionStatus
    } = useOpenStateTransitions(open3, store2);
    const isInstantPhase = store2.useState("isInstantPhase");
    const instantType = store2.useState("instantType");
    const lastOpenChangeReason = store2.useState("lastOpenChangeReason");
    const previousInstantTypeRef = React48.useRef(null);
    useIsoLayoutEffect(() => {
      if (openState && disabled2) {
        store2.setOpen(false, createChangeEventDetails(reason_parts_exports.disabled));
      }
    }, [openState, disabled2, store2]);
    useIsoLayoutEffect(() => {
      if (transitionStatus === "ending" && lastOpenChangeReason === reason_parts_exports.none || transitionStatus !== "ending" && isInstantPhase) {
        if (instantType !== "delay") {
          previousInstantTypeRef.current = instantType;
        }
        store2.set("instantType", "delay");
      } else if (previousInstantTypeRef.current !== null) {
        store2.set("instantType", previousInstantTypeRef.current);
        previousInstantTypeRef.current = null;
      }
    }, [transitionStatus, isInstantPhase, lastOpenChangeReason, instantType, store2]);
    useIsoLayoutEffect(() => {
      if (open3) {
        if (activeTriggerId == null) {
          store2.set("payload", void 0);
        }
      }
    }, [store2, activeTriggerId, open3]);
    React48.useImperativeHandle(actionsRef, () => ({
      unmount: forceUnmount,
      close: () => store2.setOpen(false, createChangeEventDetails(reason_parts_exports.imperativeAction))
    }), [forceUnmount, store2]);
    const shouldRenderInteractions = open3 || mounted || !disabled2 && trackCursorAxis !== "none";
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(TooltipRootContext.Provider, {
      value: store2,
      children: [handle && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(PopupHandleAttachment, {
        handle,
        store: store2
      }), shouldRenderInteractions && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(TooltipInteractions, {
        store: store2,
        disabled: disabled2,
        trackCursorAxis
      }), typeof children === "function" ? children({
        payload
      }) : children]
    });
  });
  if (true) TooltipRoot.displayName = "TooltipRoot";
  function TooltipInteractions({
    store: store2,
    disabled: disabled2,
    trackCursorAxis
  }) {
    const floatingRootContext = store2.useState("floatingRootContext");
    const dismiss = useDismiss(floatingRootContext, {
      enabled: !disabled2,
      referencePress: () => store2.select("closeOnClick")
    });
    const clientPoint = useClientPoint(floatingRootContext, {
      enabled: !disabled2 && trackCursorAxis !== "none",
      axis: trackCursorAxis === "none" ? void 0 : trackCursorAxis
    });
    const triggerProps = React48.useMemo(() => mergeProps(clientPoint.reference, dismiss.reference), [clientPoint.reference, dismiss.reference]);
    usePopupInteractionProps(store2, {
      activeTriggerProps: triggerProps,
      inactiveTriggerProps: triggerProps,
      popupProps: dismiss.floating ?? EMPTY_OBJECT
    });
    return null;
  }

  // node_modules/@base-ui/react/tooltip/trigger/TooltipTrigger.mjs
  var React50 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/tooltip/provider/TooltipProviderContext.mjs
  var React49 = __toESM(require_react(), 1);
  var TooltipProviderContext = /* @__PURE__ */ React49.createContext(void 0);
  if (true) TooltipProviderContext.displayName = "TooltipProviderContext";
  function useTooltipProviderContext() {
    return React49.useContext(TooltipProviderContext);
  }

  // node_modules/@base-ui/react/tooltip/utils/constants.mjs
  var OPEN_DELAY = 600;

  // node_modules/@base-ui/react/tooltip/trigger/TooltipTrigger.mjs
  var TOOLTIP_TRIGGER_IDENTIFIER = "data-base-ui-tooltip-trigger";
  function getTargetElement(event) {
    if ("composedPath" in event) {
      const path = event.composedPath();
      for (let i = 0; i < path.length; i += 1) {
        const element = path[i];
        if (isElement(element)) {
          return element;
        }
      }
    }
    const target = event.target;
    if (isElement(target)) {
      return target;
    }
    return null;
  }
  function closestEnabledTooltipTrigger(element) {
    let current = element;
    while (current) {
      const trigger = current.closest(`[${TOOLTIP_TRIGGER_IDENTIFIER}]`);
      if (trigger) {
        return trigger;
      }
      const root = current.getRootNode();
      current = "host" in root && isElement(root.host) ? root.host : null;
    }
    return null;
  }
  var TooltipTrigger = fastComponentRef(function TooltipTrigger2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      handle,
      payload,
      disabled: disabledProp,
      delay,
      closeOnClick = true,
      closeDelay,
      id: idProp,
      ...elementProps
    } = componentProps;
    const rootContext = useTooltipRootContext(true);
    const handleStore = usePopupHandleStore(handle);
    const store2 = handleStore ?? rootContext;
    if (!store2) {
      throw new Error(true ? "Base UI: <Tooltip.Trigger> must be either used within a <Tooltip.Root> component or provided with a handle." : formatErrorMessage_default(82));
    }
    const thisTriggerId = useBaseUiId(idProp);
    const isTriggerActive = store2.useState("isTriggerActive", thisTriggerId);
    const isOpenedByThisTrigger = store2.useState("isOpenedByTrigger", thisTriggerId);
    const floatingRootContext = store2.useState("floatingRootContext");
    const triggerElementRef = React50.useRef(null);
    const closeDelayWithDefault = closeDelay ?? 0;
    const {
      registerTrigger,
      isMountedByThisTrigger
    } = useTriggerDataForwarding(thisTriggerId, triggerElementRef, store2, {
      payload,
      closeOnClick,
      closeDelay: closeDelayWithDefault
    });
    const providerDelay = useTooltipProviderContext();
    const {
      activeIdRef,
      delayRef,
      isInstantPhase,
      hasProvider
    } = useDelayGroup(floatingRootContext, {
      open: isOpenedByThisTrigger
    });
    const hoverInteraction = useHoverInteractionSharedState(floatingRootContext);
    store2.useSyncedValue("isInstantPhase", isInstantPhase);
    const rootDisabled = store2.useState("disabled");
    const disabled2 = disabledProp ?? rootDisabled;
    const disabledRef = useValueAsRef(disabled2);
    const trackCursorAxis = store2.useState("trackCursorAxis");
    const disableHoverablePopup = store2.useState("disableHoverablePopup");
    const isNestedTriggerHoveredRef = React50.useRef(false);
    const nestedTriggerOpenTimeout = useTimeout();
    const pointerTypeRef = React50.useRef(void 0);
    function getOpenDelay() {
      if (hasProvider && activeIdRef.current != null) {
        return 0;
      }
      return delay ?? providerDelay ?? OPEN_DELAY;
    }
    function isEnabledNestedTriggerTarget(target) {
      const triggerEl = triggerElementRef.current;
      if (!triggerEl || !target) {
        return false;
      }
      const nearestTrigger = closestEnabledTooltipTrigger(target);
      return nearestTrigger !== null && nearestTrigger !== triggerEl && contains(triggerEl, nearestTrigger);
    }
    function detectNestedTriggerHover(target) {
      const nestedTriggerHovered = isEnabledNestedTriggerTarget(target);
      isNestedTriggerHoveredRef.current = nestedTriggerHovered;
      if (nestedTriggerHovered) {
        hoverInteraction.openChangeTimeout.clear();
        hoverInteraction.restTimeout.clear();
        hoverInteraction.restTimeoutPending = false;
        nestedTriggerOpenTimeout.clear();
      }
      return nestedTriggerHovered;
    }
    const hoverProps = useHoverReferenceInteraction(floatingRootContext, {
      enabled: !disabled2,
      mouseOnly: true,
      move: false,
      handleClose: !disableHoverablePopup && trackCursorAxis !== "both" ? safePolygon() : null,
      restMs: getOpenDelay,
      delay() {
        if (closeDelay == null && hasProvider) {
          return {
            close: getDelay(delayRef.current, "close")
          };
        }
        return {
          close: closeDelayWithDefault
        };
      },
      triggerElementRef,
      isActiveTrigger: isTriggerActive,
      isClosing: () => store2.select("transitionStatus") === "ending",
      shouldOpen() {
        return !isNestedTriggerHoveredRef.current;
      }
    });
    const focusProps = useFocus(floatingRootContext, {
      enabled: !disabled2
    }).reference;
    const handleNestedTriggerHover = (event) => {
      const wasNestedTriggerHovered = isNestedTriggerHoveredRef.current;
      const target = getTargetElement(event);
      const nestedTriggerHovered = detectNestedTriggerHover(target);
      const triggerEl = triggerElementRef.current;
      const targetInsideTrigger = triggerEl && target && contains(triggerEl, target);
      if (nestedTriggerHovered && store2.select("open") && store2.select("lastOpenChangeReason") === reason_parts_exports.triggerHover) {
        store2.setOpen(false, createChangeEventDetails(reason_parts_exports.triggerHover, event));
        return;
      }
      if (wasNestedTriggerHovered && !nestedTriggerHovered && targetInsideTrigger && !disabledRef.current && !store2.select("open") && triggerEl && // Match the hover hook's non-strict mouse fallback for mouse-only event sequences.
      isMouseLikePointerType(pointerTypeRef.current)) {
        const open3 = () => {
          if (!isNestedTriggerHoveredRef.current && !disabledRef.current && !store2.select("open")) {
            store2.setOpen(true, createChangeEventDetails(reason_parts_exports.triggerHover, event, triggerEl));
          }
        };
        const openDelay = getOpenDelay();
        if (openDelay === 0) {
          nestedTriggerOpenTimeout.clear();
          open3();
        } else {
          nestedTriggerOpenTimeout.start(openDelay, open3);
        }
      }
    };
    const rootTriggerProps = store2.useState("triggerProps", isMountedByThisTrigger);
    const shouldApplyRootTriggerProps = isMountedByThisTrigger || trackCursorAxis !== "none";
    const state = {
      open: isOpenedByThisTrigger
    };
    const element = useRenderElement("button", componentProps, {
      state,
      ref: [forwardedRef, registerTrigger, triggerElementRef],
      props: [hoverProps, focusProps, shouldApplyRootTriggerProps ? rootTriggerProps : void 0, {
        onMouseOver(event) {
          handleNestedTriggerHover(event.nativeEvent);
        },
        onFocus(event) {
          if (isEnabledNestedTriggerTarget(getTargetElement(event.nativeEvent))) {
            event.preventBaseUIHandler();
          }
        },
        onMouseLeave() {
          isNestedTriggerHoveredRef.current = false;
          nestedTriggerOpenTimeout.clear();
          pointerTypeRef.current = void 0;
        },
        onPointerEnter(event) {
          pointerTypeRef.current = event.pointerType;
        },
        onPointerDown(event) {
          pointerTypeRef.current = event.pointerType;
          store2.set("closeOnClick", closeOnClick);
          if (closeOnClick && !store2.select("open")) {
            store2.cancelPendingOpen(event.nativeEvent);
          }
        },
        onClick(event) {
          if (closeOnClick && !store2.select("open")) {
            store2.cancelPendingOpen(event.nativeEvent);
          }
        },
        id: thisTriggerId,
        [triggerDisabled]: disabled2 ? "" : void 0,
        [TOOLTIP_TRIGGER_IDENTIFIER]: disabled2 ? void 0 : ""
      }, elementProps],
      stateAttributesMapping: triggerOpenStateMapping2
    });
    return element;
  });
  if (true) TooltipTrigger.displayName = "TooltipTrigger";

  // node_modules/@base-ui/react/tooltip/portal/TooltipPortal.mjs
  var React52 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/tooltip/portal/TooltipPortalContext.mjs
  var React51 = __toESM(require_react(), 1);
  var TooltipPortalContext = /* @__PURE__ */ React51.createContext(void 0);
  if (true) TooltipPortalContext.displayName = "TooltipPortalContext";
  function useTooltipPortalContext() {
    const value = React51.useContext(TooltipPortalContext);
    if (value === void 0) {
      throw new Error(true ? "Base UI: <Tooltip.Portal> is missing." : formatErrorMessage_default(70));
    }
    return value;
  }

  // node_modules/@base-ui/react/tooltip/portal/TooltipPortal.mjs
  var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
  var TooltipPortal = /* @__PURE__ */ React52.forwardRef(function TooltipPortal2(props, forwardedRef) {
    const {
      keepMounted = false,
      ...portalProps
    } = props;
    const store2 = useTooltipRootContext();
    const mounted = store2.useState("mounted");
    const shouldRender = mounted || keepMounted;
    if (!shouldRender) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(TooltipPortalContext.Provider, {
      value: keepMounted,
      children: /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(FloatingPortalLite, {
        ref: forwardedRef,
        ...portalProps
      })
    });
  });
  if (true) TooltipPortal.displayName = "TooltipPortal";

  // node_modules/@base-ui/react/tooltip/positioner/TooltipPositioner.mjs
  var React54 = __toESM(require_react(), 1);

  // node_modules/@base-ui/react/tooltip/positioner/TooltipPositionerContext.mjs
  var React53 = __toESM(require_react(), 1);
  var TooltipPositionerContext = /* @__PURE__ */ React53.createContext(void 0);
  if (true) TooltipPositionerContext.displayName = "TooltipPositionerContext";
  function useTooltipPositionerContext() {
    const context = React53.useContext(TooltipPositionerContext);
    if (context === void 0) {
      throw new Error(true ? "Base UI: TooltipPositionerContext is missing. TooltipPositioner parts must be placed within <Tooltip.Positioner>." : formatErrorMessage_default(71));
    }
    return context;
  }

  // node_modules/@base-ui/react/tooltip/positioner/TooltipPositioner.mjs
  var import_jsx_runtime11 = __toESM(require_jsx_runtime(), 1);
  var TooltipPositioner = /* @__PURE__ */ React54.forwardRef(function TooltipPositioner2(componentProps, forwardedRef) {
    const {
      render,
      className,
      anchor,
      positionMethod = "absolute",
      side = "top",
      align = "center",
      sideOffset = 0,
      alignOffset = 0,
      collisionBoundary = "clipping-ancestors",
      collisionPadding = 5,
      arrowPadding = 5,
      sticky = false,
      disableAnchorTracking = false,
      collisionAvoidance = POPUP_COLLISION_AVOIDANCE,
      style,
      ...elementProps
    } = componentProps;
    const store2 = useTooltipRootContext();
    const keepMounted = useTooltipPortalContext();
    const open3 = store2.useState("open");
    const mounted = store2.useState("mounted");
    const trackCursorAxis = store2.useState("trackCursorAxis");
    const disableHoverablePopup = store2.useState("disableHoverablePopup");
    const floatingRootContext = store2.useState("floatingRootContext");
    const instantType = store2.useState("instantType");
    const transitionStatus = store2.useState("transitionStatus");
    const adaptiveOrigin2 = store2.useState("adaptiveOrigin");
    const positioning = useAnchorPositioning({
      anchor,
      positionMethod,
      floatingRootContext,
      mounted,
      side,
      sideOffset,
      align,
      alignOffset,
      collisionBoundary,
      collisionPadding,
      sticky,
      arrowPadding,
      disableAnchorTracking,
      keepMounted,
      collisionAvoidance,
      adaptiveOrigin: adaptiveOrigin2
    });
    const state = React54.useMemo(() => ({
      open: open3,
      side: positioning.side,
      align: positioning.align,
      anchorHidden: positioning.anchorHidden,
      instant: trackCursorAxis !== "none" ? "tracking-cursor" : instantType
    }), [open3, positioning.side, positioning.align, positioning.anchorHidden, trackCursorAxis, instantType]);
    const element = usePositioner(componentProps, state, {
      styles: positioning.positionerStyles,
      transitionStatus,
      props: elementProps,
      refs: [forwardedRef, store2.useStateSetter("positionerElement")],
      hidden: !mounted,
      inert: !open3 || trackCursorAxis === "both" || disableHoverablePopup
    });
    return /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(TooltipPositionerContext.Provider, {
      value: positioning,
      children: element
    });
  });
  if (true) TooltipPositioner.displayName = "TooltipPositioner";

  // node_modules/@base-ui/react/tooltip/popup/TooltipPopup.mjs
  var React55 = __toESM(require_react(), 1);
  var TooltipPopup = /* @__PURE__ */ React55.forwardRef(function TooltipPopup2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      ...elementProps
    } = componentProps;
    const store2 = useTooltipRootContext();
    const {
      side,
      align
    } = useTooltipPositionerContext();
    const open3 = store2.useState("open");
    const instantType = store2.useState("instantType");
    const transitionStatus = store2.useState("transitionStatus");
    const popupProps = store2.useState("popupProps");
    const floatingContext = store2.useState("floatingRootContext");
    const disabled2 = store2.useState("disabled");
    const closeDelay = store2.useState("closeDelay");
    useOpenChangeComplete({
      open: open3,
      ref: store2.context.popupRef,
      onComplete() {
        if (open3) {
          store2.context.onOpenChangeComplete?.(true);
        }
      }
    });
    useHoverFloatingInteraction(floatingContext, {
      enabled: !disabled2,
      closeDelay
    });
    const setPopupElement = store2.useStateSetter("popupElement");
    const state = {
      open: open3,
      side,
      align,
      instant: instantType,
      transitionStatus
    };
    const element = useRenderElement("div", componentProps, {
      state,
      ref: [forwardedRef, store2.context.popupRef, setPopupElement],
      props: [FOCUSABLE_POPUP_PROPS, popupProps, getDisabledMountTransitionStyles(transitionStatus), elementProps],
      stateAttributesMapping: popupTransitionStateMapping
    });
    return element;
  });
  if (true) TooltipPopup.displayName = "TooltipPopup";

  // node_modules/@base-ui/react/tooltip/arrow/TooltipArrow.mjs
  var React56 = __toESM(require_react(), 1);
  var TooltipArrow = /* @__PURE__ */ React56.forwardRef(function TooltipArrow2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      ...elementProps
    } = componentProps;
    const store2 = useTooltipRootContext();
    const {
      arrowRef,
      side,
      align,
      arrowUncentered,
      arrowStyles
    } = useTooltipPositionerContext();
    const open3 = store2.useState("open");
    const instantType = store2.useState("instantType");
    const state = {
      open: open3,
      side,
      align,
      uncentered: arrowUncentered,
      instant: instantType
    };
    const element = useRenderElement("div", componentProps, {
      state,
      ref: [forwardedRef, arrowRef],
      props: [{
        style: arrowStyles,
        "aria-hidden": true
      }, elementProps],
      stateAttributesMapping: popupStateMapping
    });
    return element;
  });
  if (true) TooltipArrow.displayName = "TooltipArrow";

  // node_modules/@base-ui/react/tooltip/provider/TooltipProvider.mjs
  var React57 = __toESM(require_react(), 1);
  var import_jsx_runtime12 = __toESM(require_jsx_runtime(), 1);
  var TooltipProvider = function TooltipProvider2(props) {
    const {
      delay,
      closeDelay,
      timeout = 400
    } = props;
    const delayValue = React57.useMemo(() => ({
      open: delay,
      close: closeDelay
    }), [delay, closeDelay]);
    return /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(TooltipProviderContext.Provider, {
      value: delay,
      children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(FloatingDelayGroup, {
        delay: delayValue,
        timeoutMs: timeout,
        children: props.children
      })
    });
  };
  if (true) TooltipProvider.displayName = "TooltipProvider";

  // node_modules/@base-ui/react/tooltip/viewport/TooltipViewport.mjs
  var React58 = __toESM(require_react(), 1);
  var TooltipViewport = /* @__PURE__ */ React58.forwardRef(function TooltipViewport2(componentProps, forwardedRef) {
    const {
      render,
      className,
      style,
      children,
      ...elementProps
    } = componentProps;
    const store2 = useTooltipRootContext();
    const positioner = useTooltipPositionerContext();
    const instantType = store2.useState("instantType");
    const {
      children: childrenToRender,
      state: viewportState
    } = usePopupViewport({
      store: store2,
      side: positioner.side,
      children
    });
    const state = {
      activationDirection: viewportState.activationDirection,
      transitioning: viewportState.transitioning,
      instant: instantType
    };
    return useRenderElement("div", componentProps, {
      state,
      ref: forwardedRef,
      props: [elementProps, {
        children: childrenToRender
      }],
      stateAttributesMapping: popupViewportStateMapping
    });
  });
  if (true) TooltipViewport.displayName = "TooltipViewport";

  // node_modules/@base-ui/react/tooltip/store/TooltipHandle.mjs
  var TooltipHandle = class extends BasePopupHandle {
    constructor() {
      super(createNullTooltipStore(), "Tooltip");
    }
    /**
     * Opens the tooltip and associates it with the trigger with the given id.
     *
     * This method should only be called in an event handler or an effect (not during rendering).
     *
     * @param triggerId ID of the trigger to associate with the tooltip. The trigger must be a matching
     * `Tooltip.Trigger` with this handle passed as a prop.
     */
    open(triggerId) {
      this.openByTrigger(triggerId);
    }
    /**
     * Closes the tooltip.
     *
     * This method should only be called in an event handler or an effect (not during rendering).
     */
    close() {
      this.closePopup();
    }
    /**
     * Whether the tooltip is currently open. Returns `false` while no root is attached to the handle.
     */
    get isOpen() {
      return this.attachedStore?.select("open") ?? false;
    }
  };
  function createTooltipHandle() {
    return new TooltipHandle();
  }

  // node_modules/@base-ui/react/use-render/useRender.mjs
  function useRender(params) {
    return useRenderElement(params.defaultTagName ?? "div", params, params);
  }

  // packages/ui/build-module/text/text.mjs
  var import_element10 = __toESM(require_element(), 1);
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
    registerStyle("e8e31009f5", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}");
  }
  var global_css_defense_default = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li" };
  var Text = (0, import_element10.forwardRef)(function Text2({ variant = "body-md", render, className, ...props }, ref) {
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
  });

  // packages/ui/build-module/tooltip/popup.mjs
  var import_element14 = __toESM(require_element(), 1);

  // packages/ui/build-module/tooltip/portal.mjs
  var import_element11 = __toESM(require_element(), 1);

  // packages/ui/build-module/utils/wp-compat-overlay-slot.mjs
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
    registerStyle2("be37f31c1e", "._11fc52b637ff8a7e__slot{inset:0;isolation:isolate;pointer-events:none;position:fixed;z-index:1000000003}@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._11fc52b637ff8a7e__slot>*{pointer-events:auto}}}");
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
    const wp4 = topWp ?? window.wp;
    return typeof wp4?.components === "object" && wp4.components !== null;
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

  // packages/ui/build-module/tooltip/portal.mjs
  var import_jsx_runtime13 = __toESM(require_jsx_runtime(), 1);
  var Portal = (0, import_element11.forwardRef)(
    function TooltipPortal3({ container, ...restProps }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
        index_parts_exports2.Portal,
        {
          container: container ?? getWpCompatOverlaySlot(),
          ...restProps,
          ref
        }
      );
    }
  );

  // packages/ui/build-module/tooltip/positioner.mjs
  var import_element12 = __toESM(require_element(), 1);
  var import_jsx_runtime14 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle3("19fcc06039", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._480b748dd3510e64__positioner{z-index:var(--wp-ui-tooltip-z-index,initial)}._50096b232db7709d__popup{--_wp-ui-elevation-sm:0 1px 2px rgba(0,0,0,.05),0 2px 3px rgba(0,0,0,.04),0 6px 6px rgba(0,0,0,.03),0 8px 8px rgba(0,0,0,.02);background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border-radius:var(--wpds-border-radius-md,4px);box-shadow:var(--_wp-ui-elevation-sm);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);line-height:1.4;padding:var(--wpds-dimension-padding-xs,4px) var(--wpds-dimension-padding-sm,8px);@media (forced-colors:active){border-bottom-color:CanvasText;border-bottom-style:solid;border-bottom-width:1px;border-left-color:CanvasText;border-left-style:solid;border-left-width:1px;border-right-color:CanvasText;border-right-style:solid;border-right-width:1px;border-top-color:CanvasText;border-top-style:solid;border-top-width:1px}}}}');
  }
  var style_default2 = { "positioner": "_480b748dd3510e64__positioner", "popup": "_50096b232db7709d__popup" };
  var Positioner = (0, import_element12.forwardRef)(
    function TooltipPositioner3({ align = "center", className, side = "top", sideOffset = 4, ...props }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
        index_parts_exports2.Positioner,
        {
          ref,
          align,
          side,
          sideOffset,
          ...props,
          className: clsx_default(
            resets_default["box-sizing"],
            style_default2.positioner,
            className
          )
        }
      );
    }
  );

  // packages/ui/build-module/utils/render-slot-with-children.mjs
  var import_element13 = __toESM(require_element(), 1);
  function renderSlotWithChildren(slot, defaultSlot, children) {
    return (0, import_element13.cloneElement)(slot ?? defaultSlot, { children });
  }

  // packages/ui/build-module/utils/theme-provider.mjs
  var theme = __toESM(require_theme(), 1);

  // packages/ui/build-module/lock-unlock.mjs
  var import_private_apis = __toESM(require_private_apis(), 1);
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/ui"
  );

  // packages/ui/build-module/utils/theme-provider.mjs
  function getThemeProvider() {
    const themePackage = theme;
    if (themePackage.ThemeProvider) {
      return themePackage.ThemeProvider;
    }
    if (!themePackage.privateApis) {
      throw new Error(
        "@wordpress/ui: @wordpress/theme must expose `ThemeProvider` or `privateApis.ThemeProvider`."
      );
    }
    return unlock(
      themePackage.privateApis
    ).ThemeProvider;
  }
  var ThemeProvider = getThemeProvider();

  // packages/ui/build-module/tooltip/popup.mjs
  var import_jsx_runtime15 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle4("19fcc06039", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._480b748dd3510e64__positioner{z-index:var(--wp-ui-tooltip-z-index,initial)}._50096b232db7709d__popup{--_wp-ui-elevation-sm:0 1px 2px rgba(0,0,0,.05),0 2px 3px rgba(0,0,0,.04),0 6px 6px rgba(0,0,0,.03),0 8px 8px rgba(0,0,0,.02);background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border-radius:var(--wpds-border-radius-md,4px);box-shadow:var(--_wp-ui-elevation-sm);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);line-height:1.4;padding:var(--wpds-dimension-padding-xs,4px) var(--wpds-dimension-padding-sm,8px);@media (forced-colors:active){border-bottom-color:CanvasText;border-bottom-style:solid;border-bottom-width:1px;border-left-color:CanvasText;border-left-style:solid;border-left-width:1px;border-right-color:CanvasText;border-right-style:solid;border-right-width:1px;border-top-color:CanvasText;border-top-style:solid;border-top-width:1px}}}}');
  }
  var style_default3 = { "positioner": "_480b748dd3510e64__positioner", "popup": "_50096b232db7709d__popup" };
  var POPUP_COLOR = { background: "#1e1e1e" };
  var Popup = (0, import_element14.forwardRef)(function TooltipPopup3({ portal, positioner, children, className, ...props }, ref) {
    const popupContent = /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(ThemeProvider, { color: POPUP_COLOR, children: /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(
      index_parts_exports2.Popup,
      {
        ref,
        className: clsx_default(style_default3.popup, className),
        ...props,
        children
      }
    ) });
    const positionedPopup = renderSlotWithChildren(
      positioner,
      /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(Positioner, {}),
      popupContent
    );
    return renderSlotWithChildren(portal, /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(Portal, {}), positionedPopup);
  });

  // packages/ui/build-module/tooltip/trigger.mjs
  var import_element15 = __toESM(require_element(), 1);
  var import_jsx_runtime16 = __toESM(require_jsx_runtime(), 1);
  var Trigger = (0, import_element15.forwardRef)(
    function TooltipTrigger3(props, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(index_parts_exports2.Trigger, { ref, ...props });
    }
  );

  // packages/ui/build-module/utils/direction-provider.mjs
  var import_i18n = __toESM(require_i18n(), 1);
  var import_jsx_runtime17 = __toESM(require_jsx_runtime(), 1);
  function DirectionProvider3({ children }) {
    return /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(DirectionProvider, { direction: (0, import_i18n.isRTL)() ? "rtl" : "ltr", children });
  }

  // packages/ui/build-module/tooltip/root.mjs
  var import_jsx_runtime18 = __toESM(require_jsx_runtime(), 1);
  function Root(props) {
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(DirectionProvider3, { children: /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(index_parts_exports2.Root, { ...props }) });
  }

  // packages/ui/build-module/link/link.mjs
  var import_element16 = __toESM(require_element(), 1);
  var import_i18n2 = __toESM(require_i18n(), 1);
  var import_jsx_runtime19 = __toESM(require_jsx_runtime(), 1);
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
  var resets_default2 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
  if (typeof process === "undefined" || true) {
    registerStyle5("08122b3d53", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{.af79fb116edb0dd7__outset-ring--focus:focus,.dfcfdc28396e5d98__outset-ring--focus-visible:focus-visible,.e5cd9ee879f6403a__outset-ring--focus-within:focus-within,:focus-visible ._81935a08e952f267__outset-ring--focus-parent-visible{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within,.abc777e9713fa711__outset-ring--focus-except-active:focus{outline:none}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.abc777e9713fa711__outset-ring--focus-except-active:focus:not(:active){--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}}");
  }
  var focus_module_default = { "outset-ring--focus": "af79fb116edb0dd7__outset-ring--focus", "outset-ring--focus-visible": "dfcfdc28396e5d98__outset-ring--focus-visible", "outset-ring--focus-within": "e5cd9ee879f6403a__outset-ring--focus-within", "outset-ring--focus-parent-visible": "_81935a08e952f267__outset-ring--focus-parent-visible", "outset-ring--focus-except-active": "abc777e9713fa711__outset-ring--focus-except-active", "outset-ring--focus-within-except-active": "_3c9f5ee9fc9c136d__outset-ring--focus-within-except-active" };
  if (typeof process === "undefined" || true) {
    registerStyle5("e8e6a9be37", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.d4250949359b05ce__link{text-decoration-thickness:from-font;text-underline-offset:.2em}.c6055659b8e2cd2c__is-brand,.c6055659b8e2cd2c__is-brand:visited{--_gcd-a-color:var(--wpds-color-foreground-interactive-brand,var(--wp-admin-theme-color,#3858e9));color:var(--wpds-color-foreground-interactive-brand,var(--wp-admin-theme-color,#3858e9))}.c6055659b8e2cd2c__is-brand:active,.c6055659b8e2cd2c__is-brand:hover{--_gcd-a-color:var(--wpds-color-foreground-interactive-brand-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 52%,#000));color:var(--wpds-color-foreground-interactive-brand-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 52%,#000))}._92e0dfcaeee15b88__is-neutral,._92e0dfcaeee15b88__is-neutral:visited{--_gcd-a-color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);text-decoration-color:var(--wpds-color-stroke-interactive-neutral,#8d8d8d)}._92e0dfcaeee15b88__is-neutral:active,._92e0dfcaeee15b88__is-neutral:hover{--_gcd-a-color:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e);color:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e)}.cf122a9bf1035d42__is-unstyled{--_gcd-a-color:inherit;color:inherit;text-decoration:none}._0cb411afac4c86c7__link-icon{display:inline-block;font-weight:var(--wpds-typography-font-weight-default,400);line-height:1;margin-inline-start:var(--wpds-dimension-padding-xs,4px);text-decoration:none}._0cb411afac4c86c7__link-icon:after{content:"\\2197"}._0cb411afac4c86c7__link-icon:dir(rtl):after{content:"\\2196"}}}');
  }
  var style_default4 = { "link": "d4250949359b05ce__link", "is-brand": "c6055659b8e2cd2c__is-brand", "is-neutral": "_92e0dfcaeee15b88__is-neutral", "is-unstyled": "cf122a9bf1035d42__is-unstyled", "link-icon": "_0cb411afac4c86c7__link-icon" };
  if (typeof process === "undefined" || true) {
    registerStyle5("e8e31009f5", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}");
  }
  var global_css_defense_default2 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li" };
  var Link = (0, import_element16.forwardRef)(function Link2({
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
          resets_default2["box-sizing"],
          focus_module_default["outset-ring--focus-except-active"],
          variant !== "unstyled" && style_default4.link,
          variant !== "unstyled" && style_default4[`is-${tone}`],
          variant === "unstyled" && style_default4["is-unstyled"],
          className
        ),
        target: target ?? (openInNewTab ? "_blank" : void 0),
        children: /* @__PURE__ */ (0, import_jsx_runtime19.jsxs)(import_jsx_runtime19.Fragment, { children: [
          children,
          shouldShowNewTabIndicator && /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
            "span",
            {
              className: style_default4["link-icon"],
              role: "img",
              "aria-label": (
                /* translators: accessibility text appended to link text */
                (0, import_i18n2.__)("(opens in a new tab)")
              )
            }
          )
        ] })
      })
    });
    return element;
  });

  // packages/icons/build-module/library/caution.mjs
  var import_primitives = __toESM(require_primitives(), 1);
  var import_jsx_runtime20 = __toESM(require_jsx_runtime(), 1);
  var caution_default = /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(import_primitives.Path, { d: "M12 8L12 13M12 14.5L12 16M4.75 12C4.75 7.99593 7.99594 4.75 12 4.75C16.0041 4.75 19.25 7.99594 19.25 12C19.25 16.0041 16.0041 19.25 12 19.25C7.99594 19.25 4.75 16.0041 4.75 12Z", strokeLinejoin: "round", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/chevron-down.mjs
  var import_primitives2 = __toESM(require_primitives(), 1);
  var import_jsx_runtime21 = __toESM(require_jsx_runtime(), 1);
  var chevron_down_default = /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(import_primitives2.Path, { d: "M7 11L12 15L17 11", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/close-small.mjs
  var import_primitives3 = __toESM(require_primitives(), 1);
  var import_jsx_runtime22 = __toESM(require_jsx_runtime(), 1);
  var close_small_default = /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(import_primitives3.Path, { d: "M7.75 16.25L16.25 7.75M16.25 16.25L7.75 7.75", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/error.mjs
  var import_primitives4 = __toESM(require_primitives(), 1);
  var import_jsx_runtime23 = __toESM(require_jsx_runtime(), 1);
  var error_default = /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_primitives4.Path, { d: "M12 9L12 14M12 15.5V17M11.1284 5.04947L3.83827 18.0097C3.46331 18.6763 3.94502 19.5 4.70985 19.5H19.2902C20.055 19.5 20.5367 18.6763 20.1617 18.0097L12.8716 5.04947C12.4893 4.36982 11.5107 4.36982 11.1284 5.04947Z", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/external.mjs
  var import_primitives5 = __toESM(require_primitives(), 1);
  var import_jsx_runtime24 = __toESM(require_jsx_runtime(), 1);
  var external_default = /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(import_primitives5.Path, { d: "M9.5 6.25H6.5C5.80964 6.25 5.25 6.80964 5.25 7.5V17.5C5.25 18.1904 5.80964 18.75 6.5 18.75H16.5C17.1904 18.75 17.75 18.1904 17.75 17.5V14.5M11.5 12.5L18.75 5.25M12.5 5.25H18.75V11.5", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/info.mjs
  var import_primitives6 = __toESM(require_primitives(), 1);
  var import_jsx_runtime25 = __toESM(require_jsx_runtime(), 1);
  var info_default = /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(import_primitives6.Path, { d: "M12 16V11M12 9.5V8M19.25 12C19.25 16.0041 16.0041 19.25 12 19.25C7.99594 19.25 4.75 16.0041 4.75 12C4.75 7.99594 7.99594 4.75 12 4.75C16.0041 4.75 19.25 7.99594 19.25 12Z", strokeLinejoin: "round", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/more-vertical.mjs
  var import_primitives7 = __toESM(require_primitives(), 1);
  var import_jsx_runtime26 = __toESM(require_jsx_runtime(), 1);
  var more_vertical_default = /* @__PURE__ */ (0, import_jsx_runtime26.jsxs)(import_primitives7.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(import_primitives7.Path, { d: "M11.75 18.25L12.25 18.25L12.25 17.75L11.75 17.75L11.75 18.25Z", fill: "currentColor", stroke: "none" }),
    /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(import_primitives7.Path, { d: "M11.75 12.25L12.25 12.25L12.25 11.75L11.75 11.75L11.75 12.25Z", fill: "currentColor", stroke: "none" }),
    /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(import_primitives7.Path, { d: "M11.75 6.25L12.25 6.25L12.25 5.75L11.75 5.75L11.75 6.25Z", fill: "currentColor", stroke: "none" }),
    /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(import_primitives7.Path, { d: "M11.75 18.25L12.25 18.25L12.25 17.75L11.75 17.75L11.75 18.25Z", vectorEffect: "non-scaling-stroke" }),
    /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(import_primitives7.Path, { d: "M11.75 12.25L12.25 12.25L12.25 11.75L11.75 11.75L11.75 12.25Z", vectorEffect: "non-scaling-stroke" }),
    /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(import_primitives7.Path, { d: "M11.75 6.25L12.25 6.25L12.25 5.75L11.75 5.75L11.75 6.25Z", vectorEffect: "non-scaling-stroke" })
  ] });

  // packages/icons/build-module/library/plus.mjs
  var import_primitives8 = __toESM(require_primitives(), 1);
  var import_jsx_runtime27 = __toESM(require_jsx_runtime(), 1);
  var plus_default = /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(import_primitives8.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(import_primitives8.Path, { d: "M12 18V6M18 12H6", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/published.mjs
  var import_primitives9 = __toESM(require_primitives(), 1);
  var import_jsx_runtime28 = __toESM(require_jsx_runtime(), 1);
  var published_default = /* @__PURE__ */ (0, import_jsx_runtime28.jsxs)(import_primitives9.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(import_primitives9.Path, { d: "M12 19.25C16.0041 19.25 19.25 16.0041 19.25 12C19.25 7.99594 16.0041 4.75 12 4.75C7.99594 4.75 4.75 7.99594 4.75 12C4.75 16.0041 7.99594 19.25 12 19.25Z", strokeLinejoin: "round", vectorEffect: "non-scaling-stroke" }),
    /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(import_primitives9.Path, { d: "M9 12L11 14L15 10", vectorEffect: "non-scaling-stroke" })
  ] });

  // packages/icons/build-module/library/redo.mjs
  var import_primitives10 = __toESM(require_primitives(), 1);
  var import_jsx_runtime29 = __toESM(require_jsx_runtime(), 1);
  var redo_default = /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(import_primitives10.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(import_primitives10.Path, { d: "M15 15L18.5 11L15 7M18.5 11H8C5 11 4.75 13.8 4.75 17", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/undo.mjs
  var import_primitives11 = __toESM(require_primitives(), 1);
  var import_jsx_runtime30 = __toESM(require_jsx_runtime(), 1);
  var undo_default = /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(import_primitives11.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(import_primitives11.Path, { d: "M8.25 15L4.75 11L8.25 7M4.75 11H15.25C18.25 11 18.5 13.8 18.5 17", vectorEffect: "non-scaling-stroke" }) });

  // packages/ui/build-module/icon/icon.mjs
  var import_element17 = __toESM(require_element(), 1);
  var import_primitives12 = __toESM(require_primitives(), 1);
  var import_jsx_runtime31 = __toESM(require_jsx_runtime(), 1);
  var Icon = (0, import_element17.forwardRef)(function Icon2({ icon, size: size4 = 24, style, ...restProps }, ref) {
    const mergedStyle = icon.props.style || style ? { ...icon.props.style, ...style } : void 0;
    return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
      import_primitives12.SVG,
      {
        ref,
        ...icon.props,
        ...restProps,
        ...mergedStyle ? { style: mergedStyle } : {},
        width: size4,
        height: size4
      }
    );
  });

  // packages/ui/build-module/utils/keyboard-shortcut.mjs
  var import_element19 = __toESM(require_element(), 1);
  var import_i18n3 = __toESM(require_i18n(), 1);

  // packages/ui/build-module/visually-hidden/visually-hidden.mjs
  var import_element18 = __toESM(require_element(), 1);
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
    registerStyle6("f4df7461e9", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{.f37b9e2e191ebd66__visually-hidden{border:0;clip-path:inset(50%);height:1px;margin:-1px;overflow:hidden;overflow-wrap:normal;padding:0;position:absolute;width:1px;word-break:normal}}}");
  }
  var style_default5 = { "visually-hidden": "f37b9e2e191ebd66__visually-hidden" };
  var VisuallyHidden = (0, import_element18.forwardRef)(
    function VisuallyHidden2({ render, ...restProps }, ref) {
      const element = useRender({
        render,
        ref,
        props: mergeProps(
          { className: style_default5["visually-hidden"] },
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

  // packages/ui/build-module/utils/keyboard-shortcut.mjs
  var import_jsx_runtime32 = __toESM(require_jsx_runtime(), 1);
  function useKeyboardShortcutProps({
    "aria-describedby": ariaDescribedBy,
    "aria-keyshortcuts": ariaKeyShortcuts,
    shortcut
  }) {
    const generatedDescriptionId = (0, import_element19.useId)();
    const descriptionId = shortcut ? generatedDescriptionId : void 0;
    const describedBy = [ariaDescribedBy, descriptionId].filter(Boolean).join(" ");
    return {
      descriptionId,
      targetProps: {
        "aria-describedby": describedBy || void 0,
        "aria-keyshortcuts": shortcut?.ariaKeyShortcut ?? ariaKeyShortcuts
      }
    };
  }
  var KeyboardShortcutDescription = (0, import_element19.forwardRef)(function KeyboardShortcutDescription2({ descriptionId, shortcut }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(
      VisuallyHidden,
      {
        id: descriptionId,
        "aria-hidden": "true",
        render: /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("span", { ref }),
        children: (0, import_i18n3.sprintf)(
          /* translators: %s: keyboard shortcut. */
          (0, import_i18n3.__)("Keyboard shortcut: %s"),
          shortcut.label ?? shortcut.ariaKeyShortcut
        )
      }
    );
  });
  var KeyboardShortcutDisplay = (0, import_element19.forwardRef)(function KeyboardShortcutDisplay2({ className, shortcut }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("span", { ref, "aria-hidden": "true", className, dir: "ltr", children: shortcut.displayShortcut });
  });

  // packages/ui/build-module/button/button.mjs
  var import_a11y = __toESM(require_a11y(), 1);
  var import_element20 = __toESM(require_element(), 1);
  var import_i18n4 = __toESM(require_i18n(), 1);
  var import_jsx_runtime33 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle7("effc4e3032", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._97b0fc33c028be1a__button,.abbb272e2ce49bd6__is-unstyled{appearance:none;padding:0}._97b0fc33c028be1a__button{--wp-ui-button-font-weight:var(--wpds-typography-font-weight-emphasis,600);--wp-ui-button-background-color:var(--wpds-color-background-interactive-brand-strong,var(--wp-admin-theme-color,#3858e9));--wp-ui-button-background-color-active:var(--wpds-color-background-interactive-brand-strong-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 93%,#000));--wp-ui-button-background-color-disabled:var(--wpds-color-background-interactive-brand-strong-disabled,#e6e6e6);--wp-ui-button-foreground-color:var(--wpds-color-foreground-interactive-brand-strong,#fff);--wp-ui-button-foreground-color-active:var(--wpds-color-foreground-interactive-brand-strong-active,#fff);--wp-ui-button-foreground-color-disabled:var(--wpds-color-foreground-interactive-brand-strong-disabled,#8d8d8d);--wp-ui-button-padding-block:var(--wpds-dimension-padding-xs,4px);--wp-ui-button-padding-inline:var(--wpds-dimension-padding-md,12px);--wp-ui-button-height:var(--wpds-dimension-size-lg,40px);--wp-ui-button-aspect-ratio:auto;--wp-ui-button-font-size:var(--wpds-typography-font-size-md,13px);--wp-ui-button-min-width:calc(4ch + var(--wp-ui-button-padding-inline)*2);--wp-ui-button-icon-margin:calc((var(--wpds-dimension-size-2xs, 16px) - var(--wpds-dimension-size-sm, 24px))/2);--wp-ui-button-border-color:var(--wp-ui-button-background-color);--wp-ui-button-border-color-active:var(--wp-ui-button-background-color-active);--wp-ui-button-border-color-disabled:var(--wp-ui-button-background-color-disabled);--_gcd-button-font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);--_gcd-button-font-size:var(--wp-ui-button-font-size);--_gcd-button-font-weight:var(--wp-ui-button-font-weight);align-items:center;aspect-ratio:var(--wp-ui-button-aspect-ratio);background-clip:border-box;background-color:var(--wp-ui-button-background-color);border-color:var(--wp-ui-button-border-color);border-radius:var(--wpds-border-radius-sm,2px);border-style:solid;border-width:1px;color:var(--wp-ui-button-foreground-color);display:inline-flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wp-ui-button-font-size);font-weight:var(--wp-ui-button-font-weight);gap:var(--wpds-dimension-gap-sm,8px);justify-content:center;line-height:var(--wpds-typography-line-height-sm,20px);max-width:100%;min-height:var(--wp-ui-button-height);min-width:var(--wp-ui-button-min-width);overflow-wrap:anywhere;padding-block:var(--wp-ui-button-padding-block);padding-inline:var(--wp-ui-button-padding-inline);position:relative;text-align:center;text-decoration:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}@media not (prefers-reduced-motion){transition:color .1s ease-out;*{transition:opacity .1s ease-out}}&[href]{cursor:pointer}[href]{color:inherit;text-decoration:inherit}&:not([data-disabled]):is(:hover,:active,:focus){background-color:var(--wp-ui-button-background-color-active);border-color:var(--wp-ui-button-border-color-active);color:var(--wp-ui-button-foreground-color-active)}&[data-disabled]:not(._914b42f315c0e580__is-loading){background-color:var(--wp-ui-button-background-color-disabled);border-color:var(--wp-ui-button-border-color-disabled);color:var(--wp-ui-button-foreground-color-disabled);@media (forced-colors:active){border-bottom-color:GrayText;border-left-color:GrayText;border-right-color:GrayText;border-top-color:GrayText;color:GrayText}}&:before{aspect-ratio:1;border:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid;border-block-end-color:transparent;border-block-start-color:var(--wp-ui-button-foreground-color);border-inline-end-color:var(--wp-ui-button-foreground-color);border-inline-start-color:transparent;border-radius:50%;box-sizing:border-box;content:"";display:block;height:var(--wp-ui-button-font-size);left:50%;opacity:0;pointer-events:none;position:absolute;top:50%;transform:translate(-50%,-50%);@media not (prefers-reduced-motion){transition:opacity .1s ease-out}@media (forced-colors:active){border-block-end-style:none;border-bottom-color:ButtonText;border-inline-start-style:none;border-left-color:ButtonText;border-right-color:ButtonText;border-top-color:ButtonText}}}._908205475f9f2a92__is-small{--wp-ui-button-padding-block:0px;--wp-ui-button-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-button-height:var(--wpds-dimension-size-sm,24px)}._9f6fc6553aeb36fe__icon{margin:var(--wp-ui-button-icon-margin)}.dd460c965226cc77__is-brand{&._62d5a778b7b258ee__is-outline,&.ad0619a3217c6a5b__is-minimal{--wp-ui-button-foreground-color:var(--wpds-color-foreground-interactive-brand,var(--wp-admin-theme-color,#3858e9));--wp-ui-button-foreground-color-active:var(--wpds-color-foreground-interactive-brand-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 52%,#000));--wp-ui-button-foreground-color-disabled:var(--wpds-color-foreground-interactive-brand-disabled,#8d8d8d)}&._62d5a778b7b258ee__is-outline{--wp-ui-button-background-color:var(--wpds-color-background-interactive-brand-weak,#0000);--wp-ui-button-background-color-active:var(--wpds-color-background-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));--wp-ui-button-background-color-disabled:var(--wpds-color-background-interactive-brand-weak-disabled,#0000);--wp-ui-button-border-color:var(--wpds-color-stroke-interactive-brand,var(--wp-admin-theme-color,#3858e9));--wp-ui-button-border-color-active:var(--wpds-color-stroke-interactive-brand-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 85%,#000));--wp-ui-button-border-color-disabled:var(--wpds-color-stroke-interactive-brand-disabled,#dbdbdb)}&.ad0619a3217c6a5b__is-minimal{--wp-ui-button-background-color:var(--wpds-color-background-interactive-brand-weak,#0000);--wp-ui-button-background-color-active:var(--wpds-color-background-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));--wp-ui-button-background-color-disabled:var(--wpds-color-background-interactive-brand-weak-disabled,#0000)}}.e722a8f96726aa99__is-neutral{&.ad0619a3217c6a5b__is-minimal[aria-pressed=true],&.b50b3358c5fb4d0b__is-solid{--wp-ui-button-background-color:var(--wpds-color-background-interactive-neutral-strong,#2d2d2d);--wp-ui-button-background-color-active:var(--wpds-color-background-interactive-neutral-strong-active,#1e1e1e);--wp-ui-button-background-color-disabled:var(--wpds-color-background-interactive-neutral-strong-disabled,#e6e6e6);--wp-ui-button-foreground-color:var(--wpds-color-foreground-interactive-neutral-strong,#f0f0f0);--wp-ui-button-foreground-color-active:var(--wpds-color-foreground-interactive-neutral-strong-active,#f0f0f0);--wp-ui-button-foreground-color-disabled:var(--wpds-color-foreground-interactive-neutral-strong-disabled,#8d8d8d)}&._62d5a778b7b258ee__is-outline,&.ad0619a3217c6a5b__is-minimal:not([aria-pressed=true]){--wp-ui-button-foreground-color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);--wp-ui-button-foreground-color-active:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e);--wp-ui-button-foreground-color-disabled:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d)}&._62d5a778b7b258ee__is-outline{--wp-ui-button-background-color:var(--wpds-color-background-interactive-neutral-weak,#0000);--wp-ui-button-background-color-active:var(--wpds-color-background-interactive-neutral-weak-active,#ededed);--wp-ui-button-background-color-disabled:var(--wpds-color-background-interactive-neutral-weak-disabled,#0000);--wp-ui-button-border-color:var(--wpds-color-stroke-interactive-neutral,#8d8d8d);--wp-ui-button-border-color-active:var(--wpds-color-stroke-interactive-neutral-active,#6e6e6e);--wp-ui-button-border-color-disabled:var(--wpds-color-stroke-interactive-neutral-disabled,#dbdbdb)}&.ad0619a3217c6a5b__is-minimal:not([aria-pressed=true]){--wp-ui-button-background-color:var(--wpds-color-background-interactive-neutral-weak,#0000);--wp-ui-button-background-color-active:var(--wpds-color-background-interactive-neutral-weak-active,#ededed);--wp-ui-button-background-color-disabled:var(--wpds-color-background-interactive-neutral-weak-disabled,#0000)}}.abbb272e2ce49bd6__is-unstyled{background:none;border:none;min-width:unset}.cf59cf1b69629838__is-compact{--wp-ui-button-height:var(--wpds-dimension-size-md,32px)}._914b42f315c0e580__is-loading:not(.abbb272e2ce49bd6__is-unstyled){color:transparent;&:not([data-disabled]):is(:hover,:active,:focus){color:transparent}@media (forced-colors:active){color:ButtonFace}*{opacity:0}&:before{opacity:1;transition-delay:.05s;@media not (prefers-reduced-motion){animation:_5a1d53da6f830c8d__loading-animation 1s linear infinite}}}}@keyframes _5a1d53da6f830c8d__loading-animation{0%{transform:translate(-50%,-50%) rotate(0deg)}to{transform:translate(-50%,-50%) rotate(1turn)}}}');
  }
  var style_default6 = { "button": "_97b0fc33c028be1a__button", "is-unstyled": "abbb272e2ce49bd6__is-unstyled", "is-loading": "_914b42f315c0e580__is-loading", "is-small": "_908205475f9f2a92__is-small", "icon": "_9f6fc6553aeb36fe__icon", "is-brand": "dd460c965226cc77__is-brand", "is-outline": "_62d5a778b7b258ee__is-outline", "is-minimal": "ad0619a3217c6a5b__is-minimal", "is-neutral": "e722a8f96726aa99__is-neutral", "is-solid": "b50b3358c5fb4d0b__is-solid", "is-compact": "cf59cf1b69629838__is-compact", "loading-animation": "_5a1d53da6f830c8d__loading-animation" };
  if (typeof process === "undefined" || true) {
    registerStyle7("10f3806643", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
  }
  var resets_default3 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
  if (typeof process === "undefined" || true) {
    registerStyle7("08122b3d53", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{.af79fb116edb0dd7__outset-ring--focus:focus,.dfcfdc28396e5d98__outset-ring--focus-visible:focus-visible,.e5cd9ee879f6403a__outset-ring--focus-within:focus-within,:focus-visible ._81935a08e952f267__outset-ring--focus-parent-visible{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within,.abc777e9713fa711__outset-ring--focus-except-active:focus{outline:none}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.abc777e9713fa711__outset-ring--focus-except-active:focus:not(:active){--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}}");
  }
  var focus_module_default2 = { "outset-ring--focus": "af79fb116edb0dd7__outset-ring--focus", "outset-ring--focus-visible": "dfcfdc28396e5d98__outset-ring--focus-visible", "outset-ring--focus-within": "e5cd9ee879f6403a__outset-ring--focus-within", "outset-ring--focus-parent-visible": "_81935a08e952f267__outset-ring--focus-parent-visible", "outset-ring--focus-except-active": "abc777e9713fa711__outset-ring--focus-except-active", "outset-ring--focus-within-except-active": "_3c9f5ee9fc9c136d__outset-ring--focus-within-except-active" };
  if (typeof process === "undefined" || true) {
    registerStyle7("e8e31009f5", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}");
  }
  var global_css_defense_default3 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li" };
  var Button3 = (0, import_element20.forwardRef)(
    function Button22({
      tone = "brand",
      variant = "solid",
      size: size4 = "default",
      className,
      focusableWhenDisabled = true,
      disabled: disabled2,
      loading,
      loadingAnnouncement = (0, import_i18n4.__)("Loading"),
      children,
      ...props
    }, ref) {
      const mergedClassName = clsx_default(
        global_css_defense_default3.button,
        resets_default3["box-sizing"],
        focus_module_default2["outset-ring--focus-except-active"],
        variant !== "unstyled" && style_default6.button,
        style_default6[`is-${tone}`],
        style_default6[`is-${variant}`],
        style_default6[`is-${size4}`],
        loading && style_default6["is-loading"],
        className
      );
      (0, import_element20.useEffect)(() => {
        if (loading && loadingAnnouncement) {
          (0, import_a11y.speak)(loadingAnnouncement);
        }
      }, [loading, loadingAnnouncement]);
      return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
        Button,
        {
          ref,
          className: mergedClassName,
          focusableWhenDisabled,
          disabled: disabled2 ?? loading,
          ...props,
          children
        }
      );
    }
  );

  // packages/ui/build-module/button/icon.mjs
  var import_element21 = __toESM(require_element(), 1);
  var import_jsx_runtime34 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle8("effc4e3032", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._97b0fc33c028be1a__button,.abbb272e2ce49bd6__is-unstyled{appearance:none;padding:0}._97b0fc33c028be1a__button{--wp-ui-button-font-weight:var(--wpds-typography-font-weight-emphasis,600);--wp-ui-button-background-color:var(--wpds-color-background-interactive-brand-strong,var(--wp-admin-theme-color,#3858e9));--wp-ui-button-background-color-active:var(--wpds-color-background-interactive-brand-strong-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 93%,#000));--wp-ui-button-background-color-disabled:var(--wpds-color-background-interactive-brand-strong-disabled,#e6e6e6);--wp-ui-button-foreground-color:var(--wpds-color-foreground-interactive-brand-strong,#fff);--wp-ui-button-foreground-color-active:var(--wpds-color-foreground-interactive-brand-strong-active,#fff);--wp-ui-button-foreground-color-disabled:var(--wpds-color-foreground-interactive-brand-strong-disabled,#8d8d8d);--wp-ui-button-padding-block:var(--wpds-dimension-padding-xs,4px);--wp-ui-button-padding-inline:var(--wpds-dimension-padding-md,12px);--wp-ui-button-height:var(--wpds-dimension-size-lg,40px);--wp-ui-button-aspect-ratio:auto;--wp-ui-button-font-size:var(--wpds-typography-font-size-md,13px);--wp-ui-button-min-width:calc(4ch + var(--wp-ui-button-padding-inline)*2);--wp-ui-button-icon-margin:calc((var(--wpds-dimension-size-2xs, 16px) - var(--wpds-dimension-size-sm, 24px))/2);--wp-ui-button-border-color:var(--wp-ui-button-background-color);--wp-ui-button-border-color-active:var(--wp-ui-button-background-color-active);--wp-ui-button-border-color-disabled:var(--wp-ui-button-background-color-disabled);--_gcd-button-font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);--_gcd-button-font-size:var(--wp-ui-button-font-size);--_gcd-button-font-weight:var(--wp-ui-button-font-weight);align-items:center;aspect-ratio:var(--wp-ui-button-aspect-ratio);background-clip:border-box;background-color:var(--wp-ui-button-background-color);border-color:var(--wp-ui-button-border-color);border-radius:var(--wpds-border-radius-sm,2px);border-style:solid;border-width:1px;color:var(--wp-ui-button-foreground-color);display:inline-flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wp-ui-button-font-size);font-weight:var(--wp-ui-button-font-weight);gap:var(--wpds-dimension-gap-sm,8px);justify-content:center;line-height:var(--wpds-typography-line-height-sm,20px);max-width:100%;min-height:var(--wp-ui-button-height);min-width:var(--wp-ui-button-min-width);overflow-wrap:anywhere;padding-block:var(--wp-ui-button-padding-block);padding-inline:var(--wp-ui-button-padding-inline);position:relative;text-align:center;text-decoration:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}@media not (prefers-reduced-motion){transition:color .1s ease-out;*{transition:opacity .1s ease-out}}&[href]{cursor:pointer}[href]{color:inherit;text-decoration:inherit}&:not([data-disabled]):is(:hover,:active,:focus){background-color:var(--wp-ui-button-background-color-active);border-color:var(--wp-ui-button-border-color-active);color:var(--wp-ui-button-foreground-color-active)}&[data-disabled]:not(._914b42f315c0e580__is-loading){background-color:var(--wp-ui-button-background-color-disabled);border-color:var(--wp-ui-button-border-color-disabled);color:var(--wp-ui-button-foreground-color-disabled);@media (forced-colors:active){border-bottom-color:GrayText;border-left-color:GrayText;border-right-color:GrayText;border-top-color:GrayText;color:GrayText}}&:before{aspect-ratio:1;border:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid;border-block-end-color:transparent;border-block-start-color:var(--wp-ui-button-foreground-color);border-inline-end-color:var(--wp-ui-button-foreground-color);border-inline-start-color:transparent;border-radius:50%;box-sizing:border-box;content:"";display:block;height:var(--wp-ui-button-font-size);left:50%;opacity:0;pointer-events:none;position:absolute;top:50%;transform:translate(-50%,-50%);@media not (prefers-reduced-motion){transition:opacity .1s ease-out}@media (forced-colors:active){border-block-end-style:none;border-bottom-color:ButtonText;border-inline-start-style:none;border-left-color:ButtonText;border-right-color:ButtonText;border-top-color:ButtonText}}}._908205475f9f2a92__is-small{--wp-ui-button-padding-block:0px;--wp-ui-button-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-button-height:var(--wpds-dimension-size-sm,24px)}._9f6fc6553aeb36fe__icon{margin:var(--wp-ui-button-icon-margin)}.dd460c965226cc77__is-brand{&._62d5a778b7b258ee__is-outline,&.ad0619a3217c6a5b__is-minimal{--wp-ui-button-foreground-color:var(--wpds-color-foreground-interactive-brand,var(--wp-admin-theme-color,#3858e9));--wp-ui-button-foreground-color-active:var(--wpds-color-foreground-interactive-brand-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 52%,#000));--wp-ui-button-foreground-color-disabled:var(--wpds-color-foreground-interactive-brand-disabled,#8d8d8d)}&._62d5a778b7b258ee__is-outline{--wp-ui-button-background-color:var(--wpds-color-background-interactive-brand-weak,#0000);--wp-ui-button-background-color-active:var(--wpds-color-background-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));--wp-ui-button-background-color-disabled:var(--wpds-color-background-interactive-brand-weak-disabled,#0000);--wp-ui-button-border-color:var(--wpds-color-stroke-interactive-brand,var(--wp-admin-theme-color,#3858e9));--wp-ui-button-border-color-active:var(--wpds-color-stroke-interactive-brand-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 85%,#000));--wp-ui-button-border-color-disabled:var(--wpds-color-stroke-interactive-brand-disabled,#dbdbdb)}&.ad0619a3217c6a5b__is-minimal{--wp-ui-button-background-color:var(--wpds-color-background-interactive-brand-weak,#0000);--wp-ui-button-background-color-active:var(--wpds-color-background-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));--wp-ui-button-background-color-disabled:var(--wpds-color-background-interactive-brand-weak-disabled,#0000)}}.e722a8f96726aa99__is-neutral{&.ad0619a3217c6a5b__is-minimal[aria-pressed=true],&.b50b3358c5fb4d0b__is-solid{--wp-ui-button-background-color:var(--wpds-color-background-interactive-neutral-strong,#2d2d2d);--wp-ui-button-background-color-active:var(--wpds-color-background-interactive-neutral-strong-active,#1e1e1e);--wp-ui-button-background-color-disabled:var(--wpds-color-background-interactive-neutral-strong-disabled,#e6e6e6);--wp-ui-button-foreground-color:var(--wpds-color-foreground-interactive-neutral-strong,#f0f0f0);--wp-ui-button-foreground-color-active:var(--wpds-color-foreground-interactive-neutral-strong-active,#f0f0f0);--wp-ui-button-foreground-color-disabled:var(--wpds-color-foreground-interactive-neutral-strong-disabled,#8d8d8d)}&._62d5a778b7b258ee__is-outline,&.ad0619a3217c6a5b__is-minimal:not([aria-pressed=true]){--wp-ui-button-foreground-color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);--wp-ui-button-foreground-color-active:var(--wpds-color-foreground-interactive-neutral-active,#1e1e1e);--wp-ui-button-foreground-color-disabled:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d)}&._62d5a778b7b258ee__is-outline{--wp-ui-button-background-color:var(--wpds-color-background-interactive-neutral-weak,#0000);--wp-ui-button-background-color-active:var(--wpds-color-background-interactive-neutral-weak-active,#ededed);--wp-ui-button-background-color-disabled:var(--wpds-color-background-interactive-neutral-weak-disabled,#0000);--wp-ui-button-border-color:var(--wpds-color-stroke-interactive-neutral,#8d8d8d);--wp-ui-button-border-color-active:var(--wpds-color-stroke-interactive-neutral-active,#6e6e6e);--wp-ui-button-border-color-disabled:var(--wpds-color-stroke-interactive-neutral-disabled,#dbdbdb)}&.ad0619a3217c6a5b__is-minimal:not([aria-pressed=true]){--wp-ui-button-background-color:var(--wpds-color-background-interactive-neutral-weak,#0000);--wp-ui-button-background-color-active:var(--wpds-color-background-interactive-neutral-weak-active,#ededed);--wp-ui-button-background-color-disabled:var(--wpds-color-background-interactive-neutral-weak-disabled,#0000)}}.abbb272e2ce49bd6__is-unstyled{background:none;border:none;min-width:unset}.cf59cf1b69629838__is-compact{--wp-ui-button-height:var(--wpds-dimension-size-md,32px)}._914b42f315c0e580__is-loading:not(.abbb272e2ce49bd6__is-unstyled){color:transparent;&:not([data-disabled]):is(:hover,:active,:focus){color:transparent}@media (forced-colors:active){color:ButtonFace}*{opacity:0}&:before{opacity:1;transition-delay:.05s;@media not (prefers-reduced-motion){animation:_5a1d53da6f830c8d__loading-animation 1s linear infinite}}}}@keyframes _5a1d53da6f830c8d__loading-animation{0%{transform:translate(-50%,-50%) rotate(0deg)}to{transform:translate(-50%,-50%) rotate(1turn)}}}');
  }
  var style_default7 = { "button": "_97b0fc33c028be1a__button", "is-unstyled": "abbb272e2ce49bd6__is-unstyled", "is-loading": "_914b42f315c0e580__is-loading", "is-small": "_908205475f9f2a92__is-small", "icon": "_9f6fc6553aeb36fe__icon", "is-brand": "dd460c965226cc77__is-brand", "is-outline": "_62d5a778b7b258ee__is-outline", "is-minimal": "ad0619a3217c6a5b__is-minimal", "is-neutral": "e722a8f96726aa99__is-neutral", "is-solid": "b50b3358c5fb4d0b__is-solid", "is-compact": "cf59cf1b69629838__is-compact", "loading-animation": "_5a1d53da6f830c8d__loading-animation" };
  var ButtonIcon = (0, import_element21.forwardRef)(
    function ButtonIcon2({ className, icon, ...props }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(
        Icon,
        {
          ref,
          icon,
          className: clsx_default(style_default7.icon, className),
          size: 24,
          ...props
        }
      );
    }
  );

  // packages/ui/build-module/button/index.mjs
  ButtonIcon.displayName = "Button.Icon";
  var Button4 = Object.assign(Button3, {
    /**
     * An icon component specifically designed to work well when rendered inside
     * a `Button` component.
     */
    Icon: ButtonIcon
  });

  // packages/ui/build-module/icon-button/icon-button.mjs
  var import_element22 = __toESM(require_element(), 1);
  var import_jsx_runtime35 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle9("c5cdafb1bc", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer compositions{._28cfdc260e755391__icon-button{--wp-ui-button-aspect-ratio:1;--wp-ui-button-padding-inline:0px;--wp-ui-button-min-width:unset}.f1c70d719989a85a__icon{margin:-1px}}}");
  }
  var style_default8 = { "icon-button": "_28cfdc260e755391__icon-button", "icon": "f1c70d719989a85a__icon" };
  var IconButton = (0, import_element22.forwardRef)(
    function IconButton2({
      label,
      className,
      // Prevent accidental forwarding of `children`
      children: _children,
      disabled: disabled2,
      focusableWhenDisabled = true,
      icon,
      size: size4,
      shortcut,
      positioner,
      "aria-describedby": ariaDescribedBy,
      "aria-keyshortcuts": ariaKeyShortcuts,
      ...restProps
    }, ref) {
      const classes = clsx_default(style_default8["icon-button"], className);
      const { descriptionId, targetProps } = useKeyboardShortcutProps({
        "aria-describedby": ariaDescribedBy,
        "aria-keyshortcuts": ariaKeyShortcuts,
        shortcut
      });
      return /* @__PURE__ */ (0, import_jsx_runtime35.jsxs)(Root, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime35.jsxs)(
          Trigger,
          {
            ref,
            ...targetProps,
            disabled: disabled2 && !focusableWhenDisabled,
            render: /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
              Button4,
              {
                ...restProps,
                size: size4,
                "aria-label": label,
                disabled: disabled2,
                focusableWhenDisabled
              }
            ),
            className: classes,
            children: [
              /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(Icon, { icon, size: 24, className: style_default8.icon }),
              shortcut && descriptionId && /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
                KeyboardShortcutDescription,
                {
                  descriptionId,
                  shortcut
                }
              )
            ]
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime35.jsxs)(Popup, { positioner, children: [
          label,
          shortcut && /* @__PURE__ */ (0, import_jsx_runtime35.jsxs)(import_jsx_runtime35.Fragment, { children: [
            " ",
            /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(KeyboardShortcutDisplay, { shortcut })
          ] })
        ] })
      ] });
    }
  );

  // packages/ui/build-module/card/index.mjs
  var card_exports = {};
  __export(card_exports, {
    Content: () => Content,
    FullBleed: () => FullBleed,
    Header: () => Header,
    Root: () => Root2,
    Title: () => Title
  });

  // packages/ui/build-module/card/root.mjs
  var import_element23 = __toESM(require_element(), 1);
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
    registerStyle10("10f3806643", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
  }
  var resets_default4 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
  if (typeof process === "undefined" || true) {
    registerStyle10("cb866dcef6", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._02872bf298eadc43__root{--wp-ui-card-padding:var(--wpds-dimension-padding-2xl,24px);--wp-ui-card-header-content-gap:var(--wpds-dimension-gap-xl,24px);--wp-ui-card-header-content-margin:calc(var(--wp-ui-card-header-content-gap) - var(--wp-ui-card-padding));background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border:1px solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:var(--wpds-border-radius-lg,8px);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);display:flex;flex-direction:column;overflow:clip}._5dffdaf2a6e669ac__content,.bbccc92e6ba5662d__header{padding:var(--wp-ui-card-padding);&:not(:first-child):not(:last-child){padding-block-end:0}}.bbccc92e6ba5662d__header+._5dffdaf2a6e669ac__content{margin-block-start:var(--wp-ui-card-header-content-margin);padding-block-start:0}.c1fa192587e1b4a6__fullbleed{margin-inline:calc(var(--wp-ui-card-padding)*-1);width:calc(100% + var(--wp-ui-card-padding)*2)}._02872bf298eadc43__root>:is(.bbccc92e6ba5662d__header,._5dffdaf2a6e669ac__content):first-child>.c1fa192587e1b4a6__fullbleed:first-child{margin-block-start:calc(var(--wp-ui-card-padding)*-1)}:is(.bbccc92e6ba5662d__header,._5dffdaf2a6e669ac__content):last-child>.c1fa192587e1b4a6__fullbleed:last-child{margin-block-end:calc(var(--wp-ui-card-padding)*-1)}}}");
  }
  var style_default9 = { "root": "_02872bf298eadc43__root", "header": "bbccc92e6ba5662d__header", "content": "_5dffdaf2a6e669ac__content", "fullbleed": "c1fa192587e1b4a6__fullbleed" };
  var Root2 = (0, import_element23.forwardRef)(function Card({ render, ...restProps }, ref) {
    const mergedClassName = clsx_default(style_default9.root, resets_default4["box-sizing"]);
    const element = useRender({
      defaultTagName: "div",
      render,
      ref,
      props: mergeProps({ className: mergedClassName }, restProps)
    });
    return element;
  });

  // packages/ui/build-module/card/header.mjs
  var import_element24 = __toESM(require_element(), 1);
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
    registerStyle11("cb866dcef6", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._02872bf298eadc43__root{--wp-ui-card-padding:var(--wpds-dimension-padding-2xl,24px);--wp-ui-card-header-content-gap:var(--wpds-dimension-gap-xl,24px);--wp-ui-card-header-content-margin:calc(var(--wp-ui-card-header-content-gap) - var(--wp-ui-card-padding));background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border:1px solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:var(--wpds-border-radius-lg,8px);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);display:flex;flex-direction:column;overflow:clip}._5dffdaf2a6e669ac__content,.bbccc92e6ba5662d__header{padding:var(--wp-ui-card-padding);&:not(:first-child):not(:last-child){padding-block-end:0}}.bbccc92e6ba5662d__header+._5dffdaf2a6e669ac__content{margin-block-start:var(--wp-ui-card-header-content-margin);padding-block-start:0}.c1fa192587e1b4a6__fullbleed{margin-inline:calc(var(--wp-ui-card-padding)*-1);width:calc(100% + var(--wp-ui-card-padding)*2)}._02872bf298eadc43__root>:is(.bbccc92e6ba5662d__header,._5dffdaf2a6e669ac__content):first-child>.c1fa192587e1b4a6__fullbleed:first-child{margin-block-start:calc(var(--wp-ui-card-padding)*-1)}:is(.bbccc92e6ba5662d__header,._5dffdaf2a6e669ac__content):last-child>.c1fa192587e1b4a6__fullbleed:last-child{margin-block-end:calc(var(--wp-ui-card-padding)*-1)}}}");
  }
  var style_default10 = { "root": "_02872bf298eadc43__root", "header": "bbccc92e6ba5662d__header", "content": "_5dffdaf2a6e669ac__content", "fullbleed": "c1fa192587e1b4a6__fullbleed" };
  var Header = (0, import_element24.forwardRef)(
    function CardHeader({ render, ...props }, ref) {
      const element = useRender({
        defaultTagName: "div",
        render,
        ref,
        props: mergeProps({ className: style_default10.header }, props)
      });
      return element;
    }
  );

  // packages/ui/build-module/card/content.mjs
  var import_element25 = __toESM(require_element(), 1);
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
    registerStyle12("cb866dcef6", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._02872bf298eadc43__root{--wp-ui-card-padding:var(--wpds-dimension-padding-2xl,24px);--wp-ui-card-header-content-gap:var(--wpds-dimension-gap-xl,24px);--wp-ui-card-header-content-margin:calc(var(--wp-ui-card-header-content-gap) - var(--wp-ui-card-padding));background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border:1px solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:var(--wpds-border-radius-lg,8px);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);display:flex;flex-direction:column;overflow:clip}._5dffdaf2a6e669ac__content,.bbccc92e6ba5662d__header{padding:var(--wp-ui-card-padding);&:not(:first-child):not(:last-child){padding-block-end:0}}.bbccc92e6ba5662d__header+._5dffdaf2a6e669ac__content{margin-block-start:var(--wp-ui-card-header-content-margin);padding-block-start:0}.c1fa192587e1b4a6__fullbleed{margin-inline:calc(var(--wp-ui-card-padding)*-1);width:calc(100% + var(--wp-ui-card-padding)*2)}._02872bf298eadc43__root>:is(.bbccc92e6ba5662d__header,._5dffdaf2a6e669ac__content):first-child>.c1fa192587e1b4a6__fullbleed:first-child{margin-block-start:calc(var(--wp-ui-card-padding)*-1)}:is(.bbccc92e6ba5662d__header,._5dffdaf2a6e669ac__content):last-child>.c1fa192587e1b4a6__fullbleed:last-child{margin-block-end:calc(var(--wp-ui-card-padding)*-1)}}}");
  }
  var style_default11 = { "root": "_02872bf298eadc43__root", "header": "bbccc92e6ba5662d__header", "content": "_5dffdaf2a6e669ac__content", "fullbleed": "c1fa192587e1b4a6__fullbleed" };
  var Content = (0, import_element25.forwardRef)(
    function CardContent({ render, ...props }, ref) {
      const element = useRender({
        defaultTagName: "div",
        render,
        ref,
        props: mergeProps({ className: style_default11.content }, props)
      });
      return element;
    }
  );

  // packages/ui/build-module/card/full-bleed.mjs
  var import_element26 = __toESM(require_element(), 1);
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
    registerStyle13("cb866dcef6", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._02872bf298eadc43__root{--wp-ui-card-padding:var(--wpds-dimension-padding-2xl,24px);--wp-ui-card-header-content-gap:var(--wpds-dimension-gap-xl,24px);--wp-ui-card-header-content-margin:calc(var(--wp-ui-card-header-content-gap) - var(--wp-ui-card-padding));background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border:1px solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);border-radius:var(--wpds-border-radius-lg,8px);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);display:flex;flex-direction:column;overflow:clip}._5dffdaf2a6e669ac__content,.bbccc92e6ba5662d__header{padding:var(--wp-ui-card-padding);&:not(:first-child):not(:last-child){padding-block-end:0}}.bbccc92e6ba5662d__header+._5dffdaf2a6e669ac__content{margin-block-start:var(--wp-ui-card-header-content-margin);padding-block-start:0}.c1fa192587e1b4a6__fullbleed{margin-inline:calc(var(--wp-ui-card-padding)*-1);width:calc(100% + var(--wp-ui-card-padding)*2)}._02872bf298eadc43__root>:is(.bbccc92e6ba5662d__header,._5dffdaf2a6e669ac__content):first-child>.c1fa192587e1b4a6__fullbleed:first-child{margin-block-start:calc(var(--wp-ui-card-padding)*-1)}:is(.bbccc92e6ba5662d__header,._5dffdaf2a6e669ac__content):last-child>.c1fa192587e1b4a6__fullbleed:last-child{margin-block-end:calc(var(--wp-ui-card-padding)*-1)}}}");
  }
  var style_default12 = { "root": "_02872bf298eadc43__root", "header": "bbccc92e6ba5662d__header", "content": "_5dffdaf2a6e669ac__content", "fullbleed": "c1fa192587e1b4a6__fullbleed" };
  var FullBleed = (0, import_element26.forwardRef)(
    function CardFullBleed({ render, ...props }, ref) {
      const element = useRender({
        defaultTagName: "div",
        render,
        ref,
        props: mergeProps(
          { className: style_default12.fullbleed },
          props
        )
      });
      return element;
    }
  );

  // packages/ui/build-module/card/title.mjs
  var import_element27 = __toESM(require_element(), 1);
  var import_jsx_runtime36 = __toESM(require_jsx_runtime(), 1);
  var DEFAULT_TAG = /* @__PURE__ */ (0, import_jsx_runtime36.jsx)("div", {});
  var Title = (0, import_element27.forwardRef)(
    function CardTitle({ render = DEFAULT_TAG, children, ...props }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
        Text,
        {
          ref,
          variant: "heading-lg",
          render,
          ...props,
          children
        }
      );
    }
  );

  // packages/ui/build-module/collapsible/panel.mjs
  var import_element28 = __toESM(require_element(), 1);
  var import_jsx_runtime37 = __toESM(require_jsx_runtime(), 1);
  var Panel = (0, import_element28.forwardRef)(
    function CollapsiblePanel3(props, forwardedRef) {
      return /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(index_parts_exports.Panel, { ref: forwardedRef, ...props });
    }
  );

  // packages/ui/build-module/collapsible/root.mjs
  var import_element29 = __toESM(require_element(), 1);
  var import_jsx_runtime38 = __toESM(require_jsx_runtime(), 1);
  var Root3 = (0, import_element29.forwardRef)(
    function CollapsibleRoot3(props, forwardedRef) {
      return /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(index_parts_exports.Root, { ref: forwardedRef, ...props });
    }
  );

  // packages/ui/build-module/collapsible/trigger.mjs
  var import_element30 = __toESM(require_element(), 1);
  var import_jsx_runtime39 = __toESM(require_jsx_runtime(), 1);
  var Trigger2 = (0, import_element30.forwardRef)(
    function CollapsibleTrigger3(props, forwardedRef) {
      return /* @__PURE__ */ (0, import_jsx_runtime39.jsx)(index_parts_exports.Trigger, { ref: forwardedRef, ...props });
    }
  );

  // packages/ui/build-module/collapsible-card/index.mjs
  var collapsible_card_exports = {};
  __export(collapsible_card_exports, {
    Content: () => Content2,
    Header: () => Header2,
    HeaderDescription: () => HeaderDescription,
    Root: () => Root32
  });

  // packages/ui/build-module/collapsible-card/root.mjs
  var import_element31 = __toESM(require_element(), 1);
  var import_jsx_runtime40 = __toESM(require_jsx_runtime(), 1);
  var Root32 = (0, import_element31.forwardRef)(
    function CollapsibleCardRoot({ render, ...restProps }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(
        Root3,
        {
          ref,
          render: /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(Root2, { render }),
          ...restProps
        }
      );
    }
  );

  // packages/ui/build-module/collapsible-card/header.mjs
  var import_compose = __toESM(require_compose(), 1);
  var import_element33 = __toESM(require_element(), 1);

  // packages/ui/build-module/collapsible-card/context.mjs
  var import_element32 = __toESM(require_element(), 1);
  var HeaderDescriptionIdContext = (0, import_element32.createContext)(null);

  // packages/ui/build-module/collapsible-card/header.mjs
  var import_jsx_runtime41 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle14("64bdb4d46e", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._626190151275d6d3__heading-wrapper{--_gcd-heading-color:inherit;--_gcd-heading-font-size:inherit;--_gcd-heading-font-weight:inherit;--_gcd-heading-margin:0;font-family:inherit;line-height:inherit}.cab17c7a373cb60d__header-content{flex:1;min-width:0}._92ebbaa30c8a1d05__header-description{color:var(--wpds-color-foreground-content-neutral-weak,#707070)}.dd89d27c4f15912d__header-trigger-positioner{align-self:flex-start;flex-shrink:0;margin-block-start:calc(var(--wpds-typography-line-height-sm, 20px)/2);max-height:0;overflow:visible}.bcfab5f2448bafef__header-trigger-wrapper{border-radius:var(--wpds-border-radius-sm,2px);display:flex;translate:0 -50%}._3106f8d2b0330faa__header-trigger{@media not (prefers-reduced-motion){transition:rotate .15s ease-out}}._5d2dfcb4085c6d0f__header[data-panel-open] ._3106f8d2b0330faa__header-trigger{rotate:180deg}._5d2dfcb4085c6d0f__header[data-disabled] ._3106f8d2b0330faa__header-trigger{color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d)}.e34cf37ccd0d81e0__content{height:var(--collapsible-panel-height);margin-block-start:var(--wp-ui-card-header-content-margin);overflow:hidden;&._03cfdbcd710393c9__overflow-visible{overflow:visible}&[hidden]:not([hidden=until-found]){display:none}&[data-ending-style],&[data-starting-style]{height:0}@media not (prefers-reduced-motion){transition:all .15s ease-out}}}@layer compositions{._41bfdbf7b6c087c2__content-inner{padding-block-start:0}._5d2dfcb4085c6d0f__header{align-items:stretch;display:flex;flex-direction:row;gap:var(--wpds-dimension-gap-sm,8px);outline:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}}}}");
  }
  var style_default13 = { "heading-wrapper": "_626190151275d6d3__heading-wrapper", "header-content": "cab17c7a373cb60d__header-content", "header-description": "_92ebbaa30c8a1d05__header-description", "header-trigger-positioner": "dd89d27c4f15912d__header-trigger-positioner", "header-trigger-wrapper": "bcfab5f2448bafef__header-trigger-wrapper", "header-trigger": "_3106f8d2b0330faa__header-trigger", "header": "_5d2dfcb4085c6d0f__header", "content": "e34cf37ccd0d81e0__content", "overflow-visible": "_03cfdbcd710393c9__overflow-visible", "content-inner": "_41bfdbf7b6c087c2__content-inner" };
  if (typeof process === "undefined" || true) {
    registerStyle14("e8e31009f5", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-weak,#707070))}&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}.c59a0ebebd71fa4a__ol{list-style:var(--_gcd-ol-list-style,none);margin:var(--_gcd-ol-margin,0);padding-block:var(--_gcd-ol-padding-block,0);padding-inline:var(--_gcd-ol-padding-inline,0)}._46b5cb0c8e24e8c9__li{margin:var(--_gcd-li-margin,0)}");
  }
  var global_css_defense_default4 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a", "ol": "c59a0ebebd71fa4a__ol", "li": "_46b5cb0c8e24e8c9__li" };
  if (typeof process === "undefined" || true) {
    registerStyle14("08122b3d53", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{.af79fb116edb0dd7__outset-ring--focus:focus,.dfcfdc28396e5d98__outset-ring--focus-visible:focus-visible,.e5cd9ee879f6403a__outset-ring--focus-within:focus-within,:focus-visible ._81935a08e952f267__outset-ring--focus-parent-visible{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within,.abc777e9713fa711__outset-ring--focus-except-active:focus{outline:none}._3c9f5ee9fc9c136d__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.abc777e9713fa711__outset-ring--focus-except-active:focus:not(:active){--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--focus-color,var(--wpds-color-stroke-focus,var(--wp-admin-theme-color,#3858e9)));outline-offset:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px))}}}");
  }
  var focus_module_default3 = { "outset-ring--focus": "af79fb116edb0dd7__outset-ring--focus", "outset-ring--focus-visible": "dfcfdc28396e5d98__outset-ring--focus-visible", "outset-ring--focus-within": "e5cd9ee879f6403a__outset-ring--focus-within", "outset-ring--focus-parent-visible": "_81935a08e952f267__outset-ring--focus-parent-visible", "outset-ring--focus-except-active": "abc777e9713fa711__outset-ring--focus-except-active", "outset-ring--focus-within-except-active": "_3c9f5ee9fc9c136d__outset-ring--focus-within-except-active" };
  var Header2 = (0, import_element33.forwardRef)(
    function CollapsibleCardHeader({
      children,
      className,
      render,
      "aria-describedby": ariaDescribedByProp,
      ...restProps
    }, ref) {
      const [descriptionIds, setDescriptionIds] = (0, import_element33.useState)(
        []
      );
      const [orderedDescriptionIds, setOrderedDescriptionIds] = (0, import_element33.useState)([]);
      const headerContentRef = (0, import_element33.useRef)(null);
      const registerDescriptionId = (0, import_element33.useCallback)((id) => {
        setDescriptionIds((currentDescriptionIds) => {
          if (currentDescriptionIds.includes(id)) {
            return currentDescriptionIds;
          }
          return [...currentDescriptionIds, id];
        });
        return () => {
          setDescriptionIds(
            (currentDescriptionIds) => currentDescriptionIds.filter(
              (descriptionId) => descriptionId !== id
            )
          );
        };
      }, []);
      const contextValue = (0, import_element33.useMemo)(
        () => ({ registerDescriptionId }),
        [registerDescriptionId]
      );
      (0, import_compose.useIsomorphicLayoutEffect)(() => {
        const registeredDescriptionIds = new Set(descriptionIds);
        const nextDescriptionIds = Array.from(
          headerContentRef.current?.querySelectorAll("[id]") ?? []
        ).map((element) => element.id).filter((id) => registeredDescriptionIds.has(id));
        setOrderedDescriptionIds((currentDescriptionIds) => {
          if (currentDescriptionIds.length === nextDescriptionIds.length && currentDescriptionIds.every(
            (id, index2) => id === nextDescriptionIds[index2]
          )) {
            return currentDescriptionIds;
          }
          return nextDescriptionIds;
        });
      });
      const ariaDescribedBy = Array.from(
        /* @__PURE__ */ new Set([
          ...ariaDescribedByProp?.split(/\s+/).filter(Boolean) ?? [],
          ...orderedDescriptionIds
        ])
      ).join(" ") || void 0;
      return useRender({
        defaultTagName: "div",
        render,
        ref,
        props: mergeProps(restProps, {
          className: clsx_default(
            global_css_defense_default4.heading,
            style_default13["heading-wrapper"],
            className
          ),
          children: /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(HeaderDescriptionIdContext.Provider, { value: contextValue, children: /* @__PURE__ */ (0, import_jsx_runtime41.jsxs)(
            Trigger2,
            {
              className: style_default13.header,
              render: /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(Header, {}),
              nativeButton: false,
              "aria-describedby": ariaDescribedBy,
              children: [
                /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(
                  "div",
                  {
                    ref: headerContentRef,
                    className: style_default13["header-content"],
                    children
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(
                  "div",
                  {
                    className: clsx_default(
                      style_default13["header-trigger-positioner"]
                    ),
                    children: /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(
                      "div",
                      {
                        className: clsx_default(
                          style_default13["header-trigger-wrapper"],
                          global_css_defense_default4.div,
                          // While the interactive trigger element is the whole header,
                          // the focus ring will be displayed only on the icon to visually
                          // emulate it being the button.
                          focus_module_default3["outset-ring--focus-parent-visible"]
                        ),
                        children: /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(
                          Icon,
                          {
                            icon: chevron_down_default,
                            className: style_default13["header-trigger"]
                          }
                        )
                      }
                    )
                  }
                )
              ]
            }
          ) })
        })
      });
    }
  );

  // packages/ui/build-module/collapsible-card/header-description.mjs
  var import_element34 = __toESM(require_element(), 1);
  var import_jsx_runtime42 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle15("64bdb4d46e", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._626190151275d6d3__heading-wrapper{--_gcd-heading-color:inherit;--_gcd-heading-font-size:inherit;--_gcd-heading-font-weight:inherit;--_gcd-heading-margin:0;font-family:inherit;line-height:inherit}.cab17c7a373cb60d__header-content{flex:1;min-width:0}._92ebbaa30c8a1d05__header-description{color:var(--wpds-color-foreground-content-neutral-weak,#707070)}.dd89d27c4f15912d__header-trigger-positioner{align-self:flex-start;flex-shrink:0;margin-block-start:calc(var(--wpds-typography-line-height-sm, 20px)/2);max-height:0;overflow:visible}.bcfab5f2448bafef__header-trigger-wrapper{border-radius:var(--wpds-border-radius-sm,2px);display:flex;translate:0 -50%}._3106f8d2b0330faa__header-trigger{@media not (prefers-reduced-motion){transition:rotate .15s ease-out}}._5d2dfcb4085c6d0f__header[data-panel-open] ._3106f8d2b0330faa__header-trigger{rotate:180deg}._5d2dfcb4085c6d0f__header[data-disabled] ._3106f8d2b0330faa__header-trigger{color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d)}.e34cf37ccd0d81e0__content{height:var(--collapsible-panel-height);margin-block-start:var(--wp-ui-card-header-content-margin);overflow:hidden;&._03cfdbcd710393c9__overflow-visible{overflow:visible}&[hidden]:not([hidden=until-found]){display:none}&[data-ending-style],&[data-starting-style]{height:0}@media not (prefers-reduced-motion){transition:all .15s ease-out}}}@layer compositions{._41bfdbf7b6c087c2__content-inner{padding-block-start:0}._5d2dfcb4085c6d0f__header{align-items:stretch;display:flex;flex-direction:row;gap:var(--wpds-dimension-gap-sm,8px);outline:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}}}}");
  }
  var style_default14 = { "heading-wrapper": "_626190151275d6d3__heading-wrapper", "header-content": "cab17c7a373cb60d__header-content", "header-description": "_92ebbaa30c8a1d05__header-description", "header-trigger-positioner": "dd89d27c4f15912d__header-trigger-positioner", "header-trigger-wrapper": "bcfab5f2448bafef__header-trigger-wrapper", "header-trigger": "_3106f8d2b0330faa__header-trigger", "header": "_5d2dfcb4085c6d0f__header", "content": "e34cf37ccd0d81e0__content", "overflow-visible": "_03cfdbcd710393c9__overflow-visible", "content-inner": "_41bfdbf7b6c087c2__content-inner" };
  var DEFAULT_TAG2 = /* @__PURE__ */ (0, import_jsx_runtime42.jsx)("div", {});
  var HeaderDescription = (0, import_element34.forwardRef)(function CollapsibleCardHeaderDescription({ children, className, id: idProp, render = DEFAULT_TAG2, ...restProps }, ref) {
    const generatedId = (0, import_element34.useId)();
    const descriptionId = idProp ?? generatedId;
    const context = (0, import_element34.useContext)(HeaderDescriptionIdContext);
    if (!context) {
      throw new Error(
        "CollapsibleCard.HeaderDescription: Missing parent <CollapsibleCard.Header>. Render <CollapsibleCard.HeaderDescription> inside <CollapsibleCard.Header>."
      );
    }
    (0, import_element34.useEffect)(() => {
      return context?.registerDescriptionId(descriptionId);
    }, [context, descriptionId]);
    return /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(
      Text,
      {
        ref,
        variant: "body-md",
        render,
        id: descriptionId,
        "aria-hidden": "true",
        className: clsx_default(style_default14["header-description"], className),
        ...restProps,
        children
      }
    );
  });

  // packages/ui/build-module/collapsible-card/content.mjs
  var import_element35 = __toESM(require_element(), 1);
  var import_jsx_runtime43 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle16("64bdb4d46e", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._626190151275d6d3__heading-wrapper{--_gcd-heading-color:inherit;--_gcd-heading-font-size:inherit;--_gcd-heading-font-weight:inherit;--_gcd-heading-margin:0;font-family:inherit;line-height:inherit}.cab17c7a373cb60d__header-content{flex:1;min-width:0}._92ebbaa30c8a1d05__header-description{color:var(--wpds-color-foreground-content-neutral-weak,#707070)}.dd89d27c4f15912d__header-trigger-positioner{align-self:flex-start;flex-shrink:0;margin-block-start:calc(var(--wpds-typography-line-height-sm, 20px)/2);max-height:0;overflow:visible}.bcfab5f2448bafef__header-trigger-wrapper{border-radius:var(--wpds-border-radius-sm,2px);display:flex;translate:0 -50%}._3106f8d2b0330faa__header-trigger{@media not (prefers-reduced-motion){transition:rotate .15s ease-out}}._5d2dfcb4085c6d0f__header[data-panel-open] ._3106f8d2b0330faa__header-trigger{rotate:180deg}._5d2dfcb4085c6d0f__header[data-disabled] ._3106f8d2b0330faa__header-trigger{color:var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d)}.e34cf37ccd0d81e0__content{height:var(--collapsible-panel-height);margin-block-start:var(--wp-ui-card-header-content-margin);overflow:hidden;&._03cfdbcd710393c9__overflow-visible{overflow:visible}&[hidden]:not([hidden=until-found]){display:none}&[data-ending-style],&[data-starting-style]{height:0}@media not (prefers-reduced-motion){transition:all .15s ease-out}}}@layer compositions{._41bfdbf7b6c087c2__content-inner{padding-block-start:0}._5d2dfcb4085c6d0f__header{align-items:stretch;display:flex;flex-direction:row;gap:var(--wpds-dimension-gap-sm,8px);outline:none;&:not([data-disabled]){cursor:var(--wpds-cursor-control,pointer)}}}}");
  }
  var style_default15 = { "heading-wrapper": "_626190151275d6d3__heading-wrapper", "header-content": "cab17c7a373cb60d__header-content", "header-description": "_92ebbaa30c8a1d05__header-description", "header-trigger-positioner": "dd89d27c4f15912d__header-trigger-positioner", "header-trigger-wrapper": "bcfab5f2448bafef__header-trigger-wrapper", "header-trigger": "_3106f8d2b0330faa__header-trigger", "header": "_5d2dfcb4085c6d0f__header", "content": "e34cf37ccd0d81e0__content", "overflow-visible": "_03cfdbcd710393c9__overflow-visible", "content-inner": "_41bfdbf7b6c087c2__content-inner" };
  var Content2 = (0, import_element35.forwardRef)(
    function CollapsibleCardContent({ className, render, children, hiddenUntilFound = true, ...restProps }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(
        Panel,
        {
          ref,
          className: (state) => clsx_default(
            style_default15.content,
            state.open && state.transitionStatus === "idle" && style_default15["overflow-visible"],
            className
          ),
          hiddenUntilFound,
          ...restProps,
          children: /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(
            Content,
            {
              className: style_default15["content-inner"],
              render,
              children
            }
          )
        }
      );
    }
  );

  // packages/ui/build-module/stack/stack.mjs
  var import_element36 = __toESM(require_element(), 1);
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
    registerStyle17("32aba35fe1", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._19ce0419607e1896__stack{display:flex}}}");
  }
  var style_default16 = { "stack": "_19ce0419607e1896__stack" };
  var gapTokens = {
    xs: "var(--wpds-dimension-gap-xs, 4px)",
    sm: "var(--wpds-dimension-gap-sm, 8px)",
    md: "var(--wpds-dimension-gap-md, 12px)",
    lg: "var(--wpds-dimension-gap-lg, 16px)",
    xl: "var(--wpds-dimension-gap-xl, 24px)",
    "2xl": "var(--wpds-dimension-gap-2xl, 32px)",
    "3xl": "var(--wpds-dimension-gap-3xl, 40px)"
  };
  var Stack = (0, import_element36.forwardRef)(function Stack2({ direction, gap, align, justify, wrap, render, ...props }, ref) {
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
      props: mergeProps(props, { style, className: style_default16.stack })
    });
    return element;
  });

  // packages/ui/build-module/notice/index.mjs
  var notice_exports = {};
  __export(notice_exports, {
    ActionButton: () => ActionButton,
    ActionLink: () => ActionLink,
    Actions: () => Actions,
    CloseIcon: () => CloseIcon,
    Description: () => Description,
    Root: () => Root4,
    Title: () => Title2
  });

  // packages/ui/build-module/notice/root.mjs
  var import_a11y2 = __toESM(require_a11y(), 1);
  var import_element37 = __toESM(require_element(), 1);
  var import_jsx_runtime44 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle18("10f3806643", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
  }
  var resets_default5 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
  if (typeof process === "undefined" || true) {
    registerStyle18("2e53930a41", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._4145abab73d17514__notice{--icon-height:var(--wpds-dimension-size-sm,24px);--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-background-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#adc6e2);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-background-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#e1bd7c);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-warning-weak,#936300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-background-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#92d39d);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-background-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#e0afa7);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,transparent 50%,var(--wpds-color-background-interactive-neutral-weak-active,#ededed))}}}");
  }
  var style_default17 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
  var icons = {
    neutral: null,
    info: info_default,
    warning: caution_default,
    success: published_default,
    error: error_default
  };
  function getDefaultPoliteness(intent) {
    return intent === "error" ? "assertive" : "polite";
  }
  function safeRenderToString(message) {
    if (!message) {
      return void 0;
    }
    if (typeof message === "string") {
      return message;
    }
    try {
      return (0, import_element37.renderToString)(message);
    } catch {
      return void 0;
    }
  }
  function useSpokenMessage(message, politeness) {
    const spokenMessage = safeRenderToString(message);
    (0, import_element37.useEffect)(() => {
      if (spokenMessage) {
        (0, import_a11y2.speak)(spokenMessage, politeness);
      }
    }, [spokenMessage, politeness]);
  }
  var Root4 = (0, import_element37.forwardRef)(function Notice({
    intent = "neutral",
    children,
    icon,
    spokenMessage = children,
    politeness = getDefaultPoliteness(intent),
    render,
    ...restProps
  }, ref) {
    useSpokenMessage(spokenMessage, politeness);
    const iconElement = icon === null ? null : icon ?? icons[intent];
    const mergedClassName = clsx_default(
      style_default17.notice,
      style_default17[`is-${intent}`],
      resets_default5["box-sizing"]
    );
    const element = useRender({
      defaultTagName: "div",
      render,
      ref,
      props: mergeProps(
        {
          className: mergedClassName,
          children: /* @__PURE__ */ (0, import_jsx_runtime44.jsxs)(import_jsx_runtime44.Fragment, { children: [
            children,
            iconElement && /* @__PURE__ */ (0, import_jsx_runtime44.jsx)(
              Icon,
              {
                className: style_default17.icon,
                icon: iconElement
              }
            )
          ] })
        },
        restProps
      )
    });
    return element;
  });

  // packages/ui/build-module/notice/title.mjs
  var import_element38 = __toESM(require_element(), 1);
  var import_jsx_runtime45 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle19("2e53930a41", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._4145abab73d17514__notice{--icon-height:var(--wpds-dimension-size-sm,24px);--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-background-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#adc6e2);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-background-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#e1bd7c);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-warning-weak,#936300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-background-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#92d39d);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-background-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#e0afa7);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,transparent 50%,var(--wpds-color-background-interactive-neutral-weak-active,#ededed))}}}");
  }
  var style_default18 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
  var Title2 = (0, import_element38.forwardRef)(
    function NoticeTitle({ className, ...props }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(
        Text,
        {
          ref,
          variant: "heading-md",
          className: clsx_default(style_default18.title, className),
          ...props
        }
      );
    }
  );

  // packages/ui/build-module/notice/description.mjs
  var import_element39 = __toESM(require_element(), 1);
  var import_jsx_runtime46 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle20("2e53930a41", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._4145abab73d17514__notice{--icon-height:var(--wpds-dimension-size-sm,24px);--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-background-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#adc6e2);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-background-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#e1bd7c);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-warning-weak,#936300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-background-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#92d39d);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-background-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#e0afa7);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,transparent 50%,var(--wpds-color-background-interactive-neutral-weak-active,#ededed))}}}");
  }
  var style_default19 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
  var Description = (0, import_element39.forwardRef)(
    function NoticeDescription({ className, ...props }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(
        Text,
        {
          ref,
          variant: "body-md",
          className: clsx_default(style_default19.description, className),
          ...props
        }
      );
    }
  );

  // packages/ui/build-module/notice/actions.mjs
  var import_element40 = __toESM(require_element(), 1);
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
    registerStyle21("2e53930a41", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._4145abab73d17514__notice{--icon-height:var(--wpds-dimension-size-sm,24px);--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-background-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#adc6e2);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-background-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#e1bd7c);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-warning-weak,#936300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-background-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#92d39d);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-background-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#e0afa7);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,transparent 50%,var(--wpds-color-background-interactive-neutral-weak-active,#ededed))}}}");
  }
  var style_default20 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
  var Actions = (0, import_element40.forwardRef)(
    function NoticeActions({ render, ...props }, ref) {
      const element = useRender({
        defaultTagName: "div",
        render,
        ref,
        props: mergeProps(
          {
            className: style_default20.actions
          },
          props
        )
      });
      return element;
    }
  );

  // packages/ui/build-module/notice/close-icon.mjs
  var import_element41 = __toESM(require_element(), 1);
  var import_i18n5 = __toESM(require_i18n(), 1);
  var import_jsx_runtime47 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle22("2e53930a41", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._4145abab73d17514__notice{--icon-height:var(--wpds-dimension-size-sm,24px);--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-background-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#adc6e2);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-background-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#e1bd7c);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-warning-weak,#936300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-background-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#92d39d);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-background-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#e0afa7);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,transparent 50%,var(--wpds-color-background-interactive-neutral-weak-active,#ededed))}}}");
  }
  var style_default21 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
  var CloseIcon = (0, import_element41.forwardRef)(
    function NoticeCloseIcon({ className, icon = close_small_default, label = (0, import_i18n5.__)("Dismiss"), ...props }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime47.jsx)(
        IconButton,
        {
          ...props,
          ref,
          className: clsx_default(style_default21["close-icon"], className),
          variant: "minimal",
          size: "small",
          tone: "neutral",
          icon,
          label
        }
      );
    }
  );

  // packages/ui/build-module/notice/action-button.mjs
  var import_element42 = __toESM(require_element(), 1);
  var import_jsx_runtime48 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle23("2e53930a41", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._4145abab73d17514__notice{--icon-height:var(--wpds-dimension-size-sm,24px);--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-background-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#adc6e2);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-background-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#e1bd7c);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-warning-weak,#936300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-background-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#92d39d);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-background-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#e0afa7);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,transparent 50%,var(--wpds-color-background-interactive-neutral-weak-active,#ededed))}}}");
  }
  var style_default22 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
  var ActionButton = (0, import_element42.forwardRef)(
    function NoticeActionButton({ className, loading, loadingAnnouncement, variant, ...props }, ref) {
      const loadingProps = loading !== void 0 ? { loading, loadingAnnouncement: loadingAnnouncement ?? "" } : {};
      return /* @__PURE__ */ (0, import_jsx_runtime48.jsx)(
        Button4,
        {
          ...props,
          ...loadingProps,
          ref,
          size: "compact",
          tone: "neutral",
          variant,
          className: clsx_default(
            style_default22["action-button"],
            style_default22[`is-action-button-${variant}`],
            className
          )
        }
      );
    }
  );

  // packages/ui/build-module/notice/action-link.mjs
  var import_element43 = __toESM(require_element(), 1);
  var import_jsx_runtime49 = __toESM(require_jsx_runtime(), 1);
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
    registerStyle24("2e53930a41", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._4145abab73d17514__notice{--icon-height:var(--wpds-dimension-size-sm,24px);--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-background-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#adc6e2);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-background-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#e1bd7c);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-warning-weak,#936300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-background-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#92d39d);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-background-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#e0afa7);--wp-ui-notice-text-color:var(--wpds-color-foreground-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-foreground-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,transparent 50%,var(--wpds-color-background-interactive-neutral-weak-active,#ededed))}}}");
  }
  var style_default23 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
  var ActionLink = (0, import_element43.forwardRef)(
    function NoticeActionLink({ className, render, ...props }, ref) {
      return /* @__PURE__ */ (0, import_jsx_runtime49.jsx)(
        Text,
        {
          ref,
          className: clsx_default(style_default23["action-link"], className),
          ...props,
          variant: "body-md",
          render: /* @__PURE__ */ (0, import_jsx_runtime49.jsx)(Link, { tone: "neutral", variant: "default", render })
        }
      );
    }
  );

  // packages/admin-ui/build-module/admin-theme-colors/index.mjs
  var DEFAULT_THEME_COLORS = {
    primary: "#3858e9",
    background: "#222524"
  };
  var ADMIN_THEME_COLORS = /* @__PURE__ */ new Map([
    ["modern", DEFAULT_THEME_COLORS],
    ["fresh", { primary: "#007cba", background: "#25292b" }],
    ["midnight", { primary: "#cf4339", background: "#3d4042" }],
    ["coffee", { primary: "#916745", background: "#5b534d" }],
    ["ocean", { primary: "#567958", background: "#5f787f" }],
    ["blue", { primary: "#437aa8", background: "#3876a8" }],
    ["ectoplasm", { primary: "#646c3e", background: "#4f386e" }],
    ["sunrise", { primary: "#ad631e", background: "#cc4541" }],
    ["light", { primary: "#007cba", background: "#eaeeed" }]
  ]);
  function getAdminThemeColors() {
    const scheme = document.body.className.match(/admin-color-([\w-]+)/)?.[1] ?? "modern";
    return ADMIN_THEME_COLORS.get(scheme) ?? DEFAULT_THEME_COLORS;
  }

  // packages/customize-widgets/build-module/components/customize-widgets/index.mjs
  var import_element59 = __toESM(require_element(), 1);
  var import_components7 = __toESM(require_components(), 1);
  var import_theme = __toESM(require_theme(), 1);

  // packages/customize-widgets/build-module/components/error-boundary/index.mjs
  var import_element44 = __toESM(require_element(), 1);
  var import_i18n6 = __toESM(require_i18n(), 1);
  var import_compose2 = __toESM(require_compose(), 1);
  var import_hooks = __toESM(require_hooks(), 1);
  var import_jsx_runtime50 = __toESM(require_jsx_runtime(), 1);
  function getErrorName(error2) {
    return error2 instanceof Error && error2.name || "Error";
  }
  function getErrorMessage(error2) {
    if (typeof error2 === "string" && error2) {
      return error2;
    }
    if (typeof error2?.message === "string" && error2.message) {
      return error2.message;
    }
    return "An unknown error occurred.";
  }
  function getErrorSections(error2, componentStack) {
    const sections = [
      { label: getErrorName(error2), content: getErrorMessage(error2) }
    ];
    if (error2?.stack) {
      sections.push({
        label: "Stack",
        content: error2.stack.trim(),
        preformatted: true
      });
    }
    if (componentStack) {
      sections.push({
        label: "Component stack",
        content: componentStack.trim(),
        preformatted: true
      });
    }
    sections.push({
      label: "Environment",
      content: `User agent: ${window.navigator.userAgent}`,
      preformatted: true
    });
    return sections;
  }
  function getErrorReport(error2, componentStack) {
    const sections = getErrorSections(error2, componentStack).map(
      ({ label, content, preformatted }) => `**${label}**

${preformatted ? `\`\`\`
${content}
\`\`\`` : content}`
    );
    return ["### Error report", ...sections].join("\n\n");
  }
  function CopyButton({ text, children, variant = "outline" }) {
    const ref = (0, import_compose2.useCopyToClipboard)(text);
    return /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(notice_exports.ActionButton, { variant, ref, children });
  }
  function ErrorReport({ error: error2, componentStack }) {
    return /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(
      Stack,
      {
        className: "customize-widgets-error-boundary__report",
        direction: "column",
        gap: "md",
        children: getErrorSections(error2, componentStack).map(
          ({ label, content }) => /* @__PURE__ */ (0, import_jsx_runtime50.jsxs)(Stack, { direction: "column", gap: "xs", children: [
            /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(Text, { variant: "heading-md", children: label }),
            /* @__PURE__ */ (0, import_jsx_runtime50.jsx)("pre", { className: "customize-widgets-error-boundary__report-section", children: content })
          ] }, label)
        )
      }
    );
  }
  function ErrorDetails({ error: error2, componentStack }) {
    return /* @__PURE__ */ (0, import_jsx_runtime50.jsxs)(collapsible_card_exports.Root, { className: "customize-widgets-error-boundary__details", children: [
      /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(collapsible_card_exports.Header, { children: /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(card_exports.Title, { children: (0, import_i18n6.__)("Error details") }) }),
      /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(collapsible_card_exports.Content, { children: /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(
        ErrorReport,
        {
          error: error2,
          componentStack
        }
      ) })
    ] });
  }
  var ErrorBoundary = class extends import_element44.Component {
    constructor() {
      super(...arguments);
      this.state = {
        error: null,
        componentStack: null
      };
    }
    componentDidCatch(error2, errorInfo) {
      this.setState({ componentStack: errorInfo?.componentStack });
      (0, import_hooks.doAction)("editor.ErrorBoundary.errorLogged", error2, errorInfo);
    }
    static getDerivedStateFromError(error2) {
      return { error: error2 };
    }
    render() {
      const { error: error2, componentStack } = this.state;
      if (!error2) {
        return this.props.children;
      }
      return /* @__PURE__ */ (0, import_jsx_runtime50.jsxs)(
        Stack,
        {
          className: "customize-widgets-error-boundary",
          direction: "column",
          gap: "lg",
          children: [
            /* @__PURE__ */ (0, import_jsx_runtime50.jsxs)(notice_exports.Root, { intent: "error", children: [
              /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(notice_exports.Title, { children: (0, import_i18n6.__)("The editor has crashed") }),
              /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(notice_exports.Description, { children: (0, import_i18n6.__)(
                "An unknown error occurred. Reload your browser to try again, or copy the error to report the problem or search."
              ) }),
              /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(notice_exports.Actions, { children: /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(
                CopyButton,
                {
                  variant: "solid",
                  text: () => getErrorReport(error2, componentStack),
                  children: (0, import_i18n6.__)("Copy error")
                }
              ) })
            ] }),
            true ? /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(
              ErrorDetails,
              {
                error: error2,
                componentStack
              }
            ) : null
          ]
        }
      );
    }
  };

  // packages/customize-widgets/build-module/components/sidebar-block-editor/index.mjs
  var import_compose4 = __toESM(require_compose(), 1);
  var import_core_data = __toESM(require_core_data(), 1);
  var import_data13 = __toESM(require_data(), 1);
  var import_element56 = __toESM(require_element(), 1);
  var import_block_editor8 = __toESM(require_block_editor(), 1);
  var import_media_utils = __toESM(require_media_utils(), 1);
  var import_preferences3 = __toESM(require_preferences(), 1);

  // packages/customize-widgets/build-module/components/block-inspector-button/index.mjs
  var import_element45 = __toESM(require_element(), 1);
  var import_i18n7 = __toESM(require_i18n(), 1);
  var import_components = __toESM(require_components(), 1);
  var import_data = __toESM(require_data(), 1);
  var import_block_editor = __toESM(require_block_editor(), 1);
  var import_jsx_runtime51 = __toESM(require_jsx_runtime(), 1);
  function BlockInspectorButton({ inspector, closeMenu, ...props }) {
    const selectedBlockClientId = (0, import_data.useSelect)(
      (select) => select(import_block_editor.store).getSelectedBlockClientId(),
      []
    );
    const selectedBlock = (0, import_element45.useMemo)(
      () => document.getElementById(`block-${selectedBlockClientId}`),
      [selectedBlockClientId]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime51.jsx)(
      import_components.MenuItem,
      {
        onClick: () => {
          inspector.open({
            returnFocusWhenClose: selectedBlock
          });
          closeMenu();
        },
        ...props,
        children: (0, import_i18n7.__)("Show more settings")
      }
    );
  }
  var block_inspector_button_default = BlockInspectorButton;

  // packages/customize-widgets/build-module/components/header/index.mjs
  var import_components5 = __toESM(require_components(), 1);
  var import_block_editor3 = __toESM(require_block_editor(), 1);
  var import_element49 = __toESM(require_element(), 1);
  var import_keycodes3 = __toESM(require_keycodes(), 1);
  var import_i18n12 = __toESM(require_i18n(), 1);

  // packages/customize-widgets/build-module/components/inserter/index.mjs
  var import_i18n8 = __toESM(require_i18n(), 1);
  var import_block_editor2 = __toESM(require_block_editor(), 1);
  var import_components2 = __toESM(require_components(), 1);
  var import_compose3 = __toESM(require_compose(), 1);
  var import_data4 = __toESM(require_data(), 1);

  // packages/customize-widgets/build-module/store/index.mjs
  var import_data3 = __toESM(require_data(), 1);

  // packages/customize-widgets/build-module/store/reducer.mjs
  var import_data2 = __toESM(require_data(), 1);
  function blockInserterPanel(state = false, action) {
    switch (action.type) {
      case "SET_IS_INSERTER_OPENED":
        return action.value;
    }
    return state;
  }
  var reducer_default = (0, import_data2.combineReducers)({
    blockInserterPanel
  });

  // packages/customize-widgets/build-module/store/selectors.mjs
  var selectors_exports = {};
  __export(selectors_exports, {
    __experimentalGetInsertionPoint: () => __experimentalGetInsertionPoint,
    isInserterOpened: () => isInserterOpened
  });
  var EMPTY_INSERTION_POINT = {
    rootClientId: void 0,
    insertionIndex: void 0
  };
  function isInserterOpened(state) {
    return !!state.blockInserterPanel;
  }
  function __experimentalGetInsertionPoint(state) {
    if (typeof state.blockInserterPanel === "boolean") {
      return EMPTY_INSERTION_POINT;
    }
    return state.blockInserterPanel;
  }

  // packages/customize-widgets/build-module/store/actions.mjs
  var actions_exports = {};
  __export(actions_exports, {
    setIsInserterOpened: () => setIsInserterOpened
  });
  function setIsInserterOpened(value) {
    return {
      type: "SET_IS_INSERTER_OPENED",
      value
    };
  }

  // packages/customize-widgets/build-module/store/constants.mjs
  var STORE_NAME = "core/customize-widgets";

  // packages/customize-widgets/build-module/store/index.mjs
  var storeConfig = {
    reducer: reducer_default,
    selectors: selectors_exports,
    actions: actions_exports
  };
  var store = (0, import_data3.createReduxStore)(STORE_NAME, storeConfig);
  (0, import_data3.register)(store);

  // packages/customize-widgets/build-module/components/inserter/index.mjs
  var import_jsx_runtime52 = __toESM(require_jsx_runtime(), 1);
  function Inserter({ setIsOpened }) {
    const inserterTitleId = (0, import_compose3.useInstanceId)(
      Inserter,
      "customize-widget-layout__inserter-panel-title"
    );
    const insertionPoint = (0, import_data4.useSelect)(
      (select) => select(store).__experimentalGetInsertionPoint(),
      []
    );
    return /* @__PURE__ */ (0, import_jsx_runtime52.jsxs)(
      "div",
      {
        className: "customize-widgets-layout__inserter-panel",
        "aria-labelledby": inserterTitleId,
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime52.jsxs)("div", { className: "customize-widgets-layout__inserter-panel-header", children: [
            /* @__PURE__ */ (0, import_jsx_runtime52.jsx)(
              "h2",
              {
                id: inserterTitleId,
                className: "customize-widgets-layout__inserter-panel-header-title",
                children: (0, import_i18n8.__)("Add a block")
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime52.jsx)(
              import_components2.Button,
              {
                size: "small",
                icon: close_small_default,
                onClick: () => setIsOpened(false),
                "aria-label": (0, import_i18n8.__)("Close inserter")
              }
            )
          ] }),
          /* @__PURE__ */ (0, import_jsx_runtime52.jsx)("div", { className: "customize-widgets-layout__inserter-panel-content", children: /* @__PURE__ */ (0, import_jsx_runtime52.jsx)(
            import_block_editor2.__experimentalLibrary,
            {
              rootClientId: insertionPoint.rootClientId,
              __experimentalInsertionIndex: insertionPoint.insertionIndex,
              showInserterHelpPanel: true,
              onSelect: () => setIsOpened(false)
            }
          ) })
        ]
      }
    );
  }
  var inserter_default = Inserter;

  // packages/customize-widgets/build-module/components/more-menu/index.mjs
  var import_components4 = __toESM(require_components(), 1);
  var import_data7 = __toESM(require_data(), 1);
  var import_element48 = __toESM(require_element(), 1);
  var import_i18n11 = __toESM(require_i18n(), 1);
  var import_keycodes2 = __toESM(require_keycodes(), 1);
  var import_keyboard_shortcuts3 = __toESM(require_keyboard_shortcuts(), 1);
  var import_preferences = __toESM(require_preferences(), 1);

  // packages/customize-widgets/build-module/components/keyboard-shortcut-help-modal/index.mjs
  var import_components3 = __toESM(require_components(), 1);
  var import_i18n10 = __toESM(require_i18n(), 1);
  var import_keyboard_shortcuts2 = __toESM(require_keyboard_shortcuts(), 1);
  var import_data6 = __toESM(require_data(), 1);
  var import_element47 = __toESM(require_element(), 1);

  // packages/customize-widgets/build-module/components/keyboard-shortcut-help-modal/config.mjs
  var import_i18n9 = __toESM(require_i18n(), 1);
  var textFormattingShortcuts = [
    {
      keyCombination: { modifier: "primary", character: "b" },
      description: (0, import_i18n9.__)("Make the selected text bold.")
    },
    {
      keyCombination: { modifier: "primary", character: "i" },
      description: (0, import_i18n9.__)("Make the selected text italic.")
    },
    {
      keyCombination: { modifier: "primary", character: "k" },
      description: (0, import_i18n9.__)("Convert the selected text into a link.")
    },
    {
      keyCombination: { modifier: "primaryShift", character: "k" },
      description: (0, import_i18n9.__)("Remove a link.")
    },
    {
      keyCombination: { character: "[[" },
      description: (0, import_i18n9.__)("Insert a link to a post or page.")
    },
    {
      keyCombination: { modifier: "primary", character: "u" },
      description: (0, import_i18n9.__)("Underline the selected text.")
    },
    {
      keyCombination: { modifier: "access", character: "d" },
      description: (0, import_i18n9.__)("Strikethrough the selected text.")
    },
    {
      keyCombination: { modifier: "access", character: "x" },
      description: (0, import_i18n9.__)("Make the selected text inline code.")
    },
    {
      keyCombination: {
        modifier: "access",
        character: "0"
      },
      aliases: [
        {
          modifier: "access",
          character: "7"
        }
      ],
      description: (0, import_i18n9.__)("Convert the current heading to a paragraph.")
    },
    {
      keyCombination: { modifier: "access", character: "1-6" },
      description: (0, import_i18n9.__)(
        "Convert the current paragraph or heading to a heading of level 1 to 6."
      )
    },
    {
      keyCombination: { modifier: "primaryShift", character: "SPACE" },
      description: (0, import_i18n9.__)("Add non breaking space.")
    }
  ];

  // packages/customize-widgets/build-module/components/keyboard-shortcut-help-modal/shortcut.mjs
  var import_element46 = __toESM(require_element(), 1);
  var import_keycodes = __toESM(require_keycodes(), 1);
  var import_jsx_runtime53 = __toESM(require_jsx_runtime(), 1);
  function KeyCombination({ keyCombination, forceAriaLabel }) {
    const shortcut = keyCombination.modifier ? import_keycodes.displayShortcutList[keyCombination.modifier](
      keyCombination.character
    ) : keyCombination.character;
    const ariaLabel = keyCombination.modifier ? import_keycodes.shortcutAriaLabel[keyCombination.modifier](
      keyCombination.character
    ) : keyCombination.character;
    return /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(
      "kbd",
      {
        className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-key-combination",
        "aria-label": forceAriaLabel || ariaLabel,
        children: (Array.isArray(shortcut) ? shortcut : [shortcut]).map(
          (character, index2) => {
            if (character === "+") {
              return /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(import_element46.Fragment, { children: character }, index2);
            }
            return /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(
              "kbd",
              {
                className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-key",
                children: character
              },
              index2
            );
          }
        )
      }
    );
  }
  function Shortcut({ description, keyCombination, aliases = [], ariaLabel }) {
    return /* @__PURE__ */ (0, import_jsx_runtime53.jsxs)(import_jsx_runtime53.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime53.jsx)("div", { className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-description", children: description }),
      /* @__PURE__ */ (0, import_jsx_runtime53.jsxs)("div", { className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-term", children: [
        /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(
          KeyCombination,
          {
            keyCombination,
            forceAriaLabel: ariaLabel
          }
        ),
        aliases.map((alias, index2) => /* @__PURE__ */ (0, import_jsx_runtime53.jsx)(
          KeyCombination,
          {
            keyCombination: alias,
            forceAriaLabel: ariaLabel
          },
          index2
        ))
      ] })
    ] });
  }
  var shortcut_default = Shortcut;

  // packages/customize-widgets/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.mjs
  var import_data5 = __toESM(require_data(), 1);
  var import_keyboard_shortcuts = __toESM(require_keyboard_shortcuts(), 1);
  var import_jsx_runtime54 = __toESM(require_jsx_runtime(), 1);
  function DynamicShortcut({ name }) {
    const { keyCombination, description, aliases } = (0, import_data5.useSelect)(
      (select) => {
        const {
          getShortcutKeyCombination,
          getShortcutDescription,
          getShortcutAliases
        } = select(import_keyboard_shortcuts.store);
        return {
          keyCombination: getShortcutKeyCombination(name),
          aliases: getShortcutAliases(name),
          description: getShortcutDescription(name)
        };
      },
      [name]
    );
    if (!keyCombination) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime54.jsx)(
      shortcut_default,
      {
        keyCombination,
        description,
        aliases
      }
    );
  }
  var dynamic_shortcut_default = DynamicShortcut;

  // packages/customize-widgets/build-module/components/keyboard-shortcut-help-modal/index.mjs
  var import_jsx_runtime55 = __toESM(require_jsx_runtime(), 1);
  var ShortcutList = ({ shortcuts }) => (
    /*
     * Disable reason: The `list` ARIA role is redundant but
     * Safari+VoiceOver won't announce the list otherwise.
     */
    /* eslint-disable jsx-a11y/no-redundant-roles */
    /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
      "ul",
      {
        className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-list",
        role: "list",
        children: shortcuts.map((shortcut, index2) => /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
          "li",
          {
            className: "customize-widgets-keyboard-shortcut-help-modal__shortcut",
            children: typeof shortcut === "string" ? /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(dynamic_shortcut_default, { name: shortcut }) : /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(shortcut_default, { ...shortcut })
          },
          index2
        ))
      }
    )
  );
  var ShortcutSection = ({ title, shortcuts, className }) => /* @__PURE__ */ (0, import_jsx_runtime55.jsxs)(
    "section",
    {
      className: clsx_default(
        "customize-widgets-keyboard-shortcut-help-modal__section",
        className
      ),
      children: [
        !!title && /* @__PURE__ */ (0, import_jsx_runtime55.jsx)("h2", { className: "customize-widgets-keyboard-shortcut-help-modal__section-title", children: title }),
        /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(ShortcutList, { shortcuts })
      ]
    }
  );
  var ShortcutCategorySection = ({
    title,
    categoryName,
    additionalShortcuts = []
  }) => {
    const categoryShortcuts = (0, import_data6.useSelect)(
      (select) => {
        return select(import_keyboard_shortcuts2.store).getCategoryShortcuts(
          categoryName
        );
      },
      [categoryName]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
      ShortcutSection,
      {
        title,
        shortcuts: categoryShortcuts.concat(additionalShortcuts)
      }
    );
  };
  function KeyboardShortcutHelpModal({
    isModalActive,
    toggleModal
  }) {
    const { registerShortcut } = (0, import_data6.useDispatch)(import_keyboard_shortcuts2.store);
    (0, import_element47.useEffect)(() => {
      registerShortcut({
        name: "core/customize-widgets/keyboard-shortcuts",
        category: "main",
        description: (0, import_i18n10.__)("Display these keyboard shortcuts."),
        keyCombination: {
          modifier: "access",
          character: "h"
        }
      });
    }, [registerShortcut]);
    (0, import_keyboard_shortcuts2.useShortcut)("core/customize-widgets/keyboard-shortcuts", toggleModal);
    if (!isModalActive) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime55.jsxs)(
      import_components3.Modal,
      {
        className: "customize-widgets-keyboard-shortcut-help-modal",
        title: (0, import_i18n10.__)("Keyboard shortcuts"),
        onRequestClose: toggleModal,
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
            ShortcutSection,
            {
              className: "customize-widgets-keyboard-shortcut-help-modal__main-shortcuts",
              shortcuts: ["core/customize-widgets/keyboard-shortcuts"]
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
            ShortcutCategorySection,
            {
              title: (0, import_i18n10.__)("Global shortcuts"),
              categoryName: "global"
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
            ShortcutCategorySection,
            {
              title: (0, import_i18n10.__)("Selection shortcuts"),
              categoryName: "selection"
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
            ShortcutCategorySection,
            {
              title: (0, import_i18n10.__)("Block shortcuts"),
              categoryName: "block",
              additionalShortcuts: [
                {
                  keyCombination: { character: "/" },
                  description: (0, import_i18n10.__)(
                    "Change the block type after adding a new paragraph."
                  ),
                  /* translators: The forward-slash character. e.g. '/'. */
                  ariaLabel: (0, import_i18n10.__)("Forward-slash")
                }
              ]
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime55.jsx)(
            ShortcutSection,
            {
              title: (0, import_i18n10.__)("Text formatting"),
              shortcuts: textFormattingShortcuts
            }
          )
        ]
      }
    );
  }

  // packages/customize-widgets/build-module/components/more-menu/index.mjs
  var import_jsx_runtime56 = __toESM(require_jsx_runtime(), 1);
  function MoreMenu() {
    const [
      isKeyboardShortcutsModalActive,
      setIsKeyboardShortcutsModalVisible
    ] = (0, import_element48.useState)(false);
    const toggleKeyboardShortcutsModal = () => setIsKeyboardShortcutsModalVisible(!isKeyboardShortcutsModalActive);
    (0, import_keyboard_shortcuts3.useShortcut)(
      "core/customize-widgets/keyboard-shortcuts",
      toggleKeyboardShortcutsModal
    );
    const { toggle } = (0, import_data7.useDispatch)(import_preferences.store);
    return /* @__PURE__ */ (0, import_jsx_runtime56.jsxs)(import_jsx_runtime56.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
        import_components4.ToolbarDropdownMenu,
        {
          icon: more_vertical_default,
          label: (0, import_i18n11.__)("Options"),
          popoverProps: {
            placement: "bottom-end",
            className: "more-menu-dropdown__content"
          },
          toggleProps: {
            tooltipPosition: "bottom",
            size: "compact"
          },
          children: () => /* @__PURE__ */ (0, import_jsx_runtime56.jsxs)(import_jsx_runtime56.Fragment, { children: [
            /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(import_components4.MenuGroup, { label: (0, import_i18n11._x)("View", "noun"), children: /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
              import_preferences.PreferenceToggleMenuItem,
              {
                scope: "core/customize-widgets",
                name: "fixedToolbar",
                label: (0, import_i18n11.__)("Top toolbar"),
                info: (0, import_i18n11.__)(
                  "Access all block and document tools in a single place"
                ),
                messageActivated: (0, import_i18n11.__)(
                  "Top toolbar activated"
                ),
                messageDeactivated: (0, import_i18n11.__)(
                  "Top toolbar deactivated"
                )
              }
            ) }),
            /* @__PURE__ */ (0, import_jsx_runtime56.jsxs)(import_components4.MenuGroup, { label: (0, import_i18n11.__)("Tools"), children: [
              /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
                import_components4.MenuItem,
                {
                  onClick: () => {
                    setIsKeyboardShortcutsModalVisible(true);
                  },
                  shortcut: import_keycodes2.displayShortcut.access("h"),
                  children: (0, import_i18n11.__)("Keyboard shortcuts")
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
                import_components4.MenuItem,
                {
                  onClick: () => toggle(
                    "core/customize-widgets",
                    "welcomeGuide"
                  ),
                  children: (0, import_i18n11.__)("Welcome Guide")
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime56.jsxs)(
                import_components4.MenuItem,
                {
                  role: "menuitem",
                  icon: external_default,
                  href: (0, import_i18n11.__)(
                    "https://wordpress.org/documentation/article/block-based-widgets-editor/"
                  ),
                  target: "_blank",
                  rel: "noopener",
                  children: [
                    (0, import_i18n11.__)("Help"),
                    /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(VisuallyHidden, {
                      render: /* @__PURE__ */ (0, import_jsx_runtime56.jsx)("span", {}),
                      /* translators: accessibility text */
                      children: (0, import_i18n11.__)("(opens in a new tab)")
                    })
                  ]
                }
              )
            ] }),
            /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(import_components4.MenuGroup, { label: (0, import_i18n11.__)("Preferences"), children: /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
              import_preferences.PreferenceToggleMenuItem,
              {
                scope: "core/customize-widgets",
                name: "keepCaretInsideBlock",
                label: (0, import_i18n11.__)(
                  "Contain text cursor inside block"
                ),
                info: (0, import_i18n11.__)(
                  "Aids screen readers by stopping text caret from leaving blocks."
                ),
                messageActivated: (0, import_i18n11.__)(
                  "Contain text cursor inside block activated"
                ),
                messageDeactivated: (0, import_i18n11.__)(
                  "Contain text cursor inside block deactivated"
                )
              }
            ) })
          ] })
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime56.jsx)(
        KeyboardShortcutHelpModal,
        {
          isModalActive: isKeyboardShortcutsModalActive,
          toggleModal: toggleKeyboardShortcutsModal
        }
      )
    ] });
  }

  // packages/customize-widgets/build-module/components/header/index.mjs
  var import_jsx_runtime57 = __toESM(require_jsx_runtime(), 1);
  function Header3({
    sidebar,
    inserter,
    isInserterOpened: isInserterOpened2,
    setIsInserterOpened: setIsInserterOpened2,
    isFixedToolbarActive
  }) {
    const [[hasUndo, hasRedo], setUndoRedo] = (0, import_element49.useState)([
      sidebar.hasUndo(),
      sidebar.hasRedo()
    ]);
    const shortcut = (0, import_keycodes3.isAppleOS)() ? import_keycodes3.displayShortcut.primaryShift("z") : import_keycodes3.displayShortcut.primary("y");
    (0, import_element49.useEffect)(() => {
      return sidebar.subscribeHistory(() => {
        setUndoRedo([sidebar.hasUndo(), sidebar.hasRedo()]);
      });
    }, [sidebar]);
    return /* @__PURE__ */ (0, import_jsx_runtime57.jsxs)(import_jsx_runtime57.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
        "div",
        {
          className: clsx_default("customize-widgets-header", {
            "is-fixed-toolbar-active": isFixedToolbarActive
          }),
          children: /* @__PURE__ */ (0, import_jsx_runtime57.jsxs)(
            import_block_editor3.NavigableToolbar,
            {
              className: "customize-widgets-header-toolbar",
              "aria-label": (0, import_i18n12.__)("Document tools"),
              children: [
                /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
                  import_components5.ToolbarItem,
                  {
                    as: import_components5.Button,
                    icon: !(0, import_i18n12.isRTL)() ? undo_default : redo_default,
                    label: (0, import_i18n12.__)("Undo"),
                    shortcut: import_keycodes3.displayShortcut.primary("z"),
                    "aria-disabled": !hasUndo,
                    onClick: hasUndo ? sidebar.undo : void 0,
                    size: "compact",
                    className: "customize-widgets-editor-history-button undo-button"
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
                  import_components5.ToolbarItem,
                  {
                    as: import_components5.Button,
                    icon: !(0, import_i18n12.isRTL)() ? redo_default : undo_default,
                    label: (0, import_i18n12.__)("Redo"),
                    shortcut,
                    "aria-disabled": !hasRedo,
                    onClick: hasRedo ? sidebar.redo : void 0,
                    size: "compact",
                    className: "customize-widgets-editor-history-button redo-button"
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(
                  import_components5.ToolbarItem,
                  {
                    as: import_components5.Button,
                    className: "customize-widgets-header-toolbar__inserter-toggle",
                    isPressed: isInserterOpened2,
                    variant: "primary",
                    icon: plus_default,
                    label: (0, import_i18n12._x)(
                      "Add block",
                      "Generic label for block inserter button"
                    ),
                    onClick: () => {
                      setIsInserterOpened2((isOpen) => !isOpen);
                    },
                    size: "compact"
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(MoreMenu, {})
              ]
            }
          )
        }
      ),
      (0, import_element49.createPortal)(
        /* @__PURE__ */ (0, import_jsx_runtime57.jsx)(inserter_default, { setIsOpened: setIsInserterOpened2 }),
        inserter.contentContainer[0]
      )
    ] });
  }
  var header_default = Header3;

  // packages/customize-widgets/build-module/components/inserter/use-inserter.mjs
  var import_element50 = __toESM(require_element(), 1);
  var import_data8 = __toESM(require_data(), 1);
  function useInserter(inserter) {
    const isInserterOpened2 = (0, import_data8.useSelect)(
      (select) => select(store).isInserterOpened(),
      []
    );
    const { setIsInserterOpened: setIsInserterOpened2 } = (0, import_data8.useDispatch)(store);
    (0, import_element50.useEffect)(() => {
      if (isInserterOpened2) {
        inserter.open();
      } else {
        inserter.close();
      }
    }, [inserter, isInserterOpened2]);
    return [
      isInserterOpened2,
      (0, import_element50.useCallback)(
        (updater) => {
          let isOpen = updater;
          if (typeof updater === "function") {
            isOpen = updater(
              (0, import_data8.select)(store).isInserterOpened()
            );
          }
          setIsInserterOpened2(isOpen);
        },
        [setIsInserterOpened2]
      )
    ];
  }

  // packages/customize-widgets/build-module/components/sidebar-block-editor/sidebar-editor-provider.mjs
  var import_block_editor5 = __toESM(require_block_editor(), 1);

  // packages/customize-widgets/build-module/components/sidebar-block-editor/use-sidebar-block-editor.mjs
  var import_es6 = __toESM(require_es6(), 1);
  var import_element51 = __toESM(require_element(), 1);
  var import_is_shallow_equal = __toESM(require_is_shallow_equal(), 1);
  var import_widgets2 = __toESM(require_widgets(), 1);

  // packages/customize-widgets/build-module/utils.mjs
  var import_blocks = __toESM(require_blocks(), 1);
  var import_widgets = __toESM(require_widgets(), 1);
  function settingIdToWidgetId(settingId) {
    const matches = settingId.match(/^widget_(.+)(?:\[(\d+)\])$/);
    if (matches) {
      const idBase = matches[1];
      const number = parseInt(matches[2], 10);
      return `${idBase}-${number}`;
    }
    return settingId;
  }
  function blockToWidget(block, existingWidget = null) {
    let widget;
    const isValidLegacyWidgetBlock = block.name === "core/legacy-widget" && (block.attributes.id || block.attributes.instance);
    if (isValidLegacyWidgetBlock) {
      if (block.attributes.id) {
        widget = {
          id: block.attributes.id
        };
      } else {
        const { encoded, hash, raw, ...rest } = block.attributes.instance;
        widget = {
          idBase: block.attributes.idBase,
          instance: {
            ...existingWidget?.instance,
            // Required only for the customizer.
            is_widget_customizer_js_value: true,
            encoded_serialized_instance: encoded,
            instance_hash_key: hash,
            raw_instance: raw,
            ...rest
          }
        };
      }
    } else {
      const instance = {
        content: (0, import_blocks.serialize)(block)
      };
      widget = {
        idBase: "block",
        widgetClass: "WP_Widget_Block",
        instance: {
          raw_instance: instance
        }
      };
    }
    const { form, rendered, ...restExistingWidget } = existingWidget || {};
    return {
      ...restExistingWidget,
      ...widget
    };
  }
  function widgetToBlock({ id, idBase, number, instance }) {
    let block;
    const {
      encoded_serialized_instance: encoded,
      instance_hash_key: hash,
      raw_instance: raw,
      ...rest
    } = instance;
    if (idBase === "block") {
      const parsedBlocks = (0, import_blocks.parse)(raw.content ?? "", {
        __unstableSkipAutop: true
      });
      block = parsedBlocks.length ? parsedBlocks[0] : (0, import_blocks.createBlock)("core/paragraph", {});
    } else if (number) {
      block = (0, import_blocks.createBlock)("core/legacy-widget", {
        idBase,
        instance: {
          encoded,
          hash,
          raw,
          ...rest
        }
      });
    } else {
      block = (0, import_blocks.createBlock)("core/legacy-widget", {
        id
      });
    }
    return (0, import_widgets.addWidgetIdToBlock)(block, id);
  }

  // packages/customize-widgets/build-module/components/sidebar-block-editor/use-sidebar-block-editor.mjs
  function widgetsToBlocks(widgets) {
    return widgets.map((widget) => widgetToBlock(widget));
  }
  function useSidebarBlockEditor(sidebar) {
    const [blocks, setBlocks] = (0, import_element51.useState)(
      () => widgetsToBlocks(sidebar.getWidgets())
    );
    (0, import_element51.useEffect)(() => {
      return sidebar.subscribe((prevWidgets, nextWidgets) => {
        setBlocks((prevBlocks) => {
          const prevWidgetsMap = new Map(
            prevWidgets.map((widget) => [widget.id, widget])
          );
          const prevBlocksMap = new Map(
            prevBlocks.map((block) => [
              (0, import_widgets2.getWidgetIdFromBlock)(block),
              block
            ])
          );
          const nextBlocks = nextWidgets.map((nextWidget) => {
            const prevWidget = prevWidgetsMap.get(nextWidget.id);
            if (prevWidget && prevWidget === nextWidget) {
              return prevBlocksMap.get(nextWidget.id);
            }
            return widgetToBlock(nextWidget);
          });
          if ((0, import_is_shallow_equal.isShallowEqual)(prevBlocks, nextBlocks)) {
            return prevBlocks;
          }
          return nextBlocks;
        });
      });
    }, [sidebar]);
    const onChangeBlocks = (0, import_element51.useCallback)(
      (nextBlocks) => {
        setBlocks((prevBlocks) => {
          if ((0, import_is_shallow_equal.isShallowEqual)(prevBlocks, nextBlocks)) {
            return prevBlocks;
          }
          const prevBlocksMap = new Map(
            prevBlocks.map((block) => [
              (0, import_widgets2.getWidgetIdFromBlock)(block),
              block
            ])
          );
          const nextWidgets = nextBlocks.map((nextBlock) => {
            const widgetId = (0, import_widgets2.getWidgetIdFromBlock)(nextBlock);
            if (widgetId && prevBlocksMap.has(widgetId)) {
              const prevBlock = prevBlocksMap.get(widgetId);
              const prevWidget = sidebar.getWidget(widgetId);
              if ((0, import_es6.default)(nextBlock, prevBlock) && prevWidget) {
                return prevWidget;
              }
              return blockToWidget(nextBlock, prevWidget);
            }
            return blockToWidget(nextBlock);
          });
          if ((0, import_is_shallow_equal.isShallowEqual)(sidebar.getWidgets(), nextWidgets)) {
            return prevBlocks;
          }
          const addedWidgetIds = sidebar.setWidgets(nextWidgets);
          return nextBlocks.reduce(
            (updatedNextBlocks, nextBlock, index2) => {
              const addedWidgetId = addedWidgetIds[index2];
              if (addedWidgetId !== null) {
                if (updatedNextBlocks === nextBlocks) {
                  updatedNextBlocks = nextBlocks.slice();
                }
                updatedNextBlocks[index2] = (0, import_widgets2.addWidgetIdToBlock)(
                  nextBlock,
                  addedWidgetId
                );
              }
              return updatedNextBlocks;
            },
            nextBlocks
          );
        });
      },
      [sidebar]
    );
    return [blocks, onChangeBlocks, onChangeBlocks];
  }

  // packages/customize-widgets/build-module/components/focus-control/use-blocks-focus-control.mjs
  var import_element53 = __toESM(require_element(), 1);
  var import_data9 = __toESM(require_data(), 1);
  var import_block_editor4 = __toESM(require_block_editor(), 1);
  var import_widgets3 = __toESM(require_widgets(), 1);

  // packages/customize-widgets/build-module/components/focus-control/index.mjs
  var import_element52 = __toESM(require_element(), 1);
  var import_jsx_runtime58 = __toESM(require_jsx_runtime(), 1);
  var FocusControlContext = (0, import_element52.createContext)();
  FocusControlContext.displayName = "FocusControlContext";
  function FocusControl({ api, sidebarControls, children }) {
    const [focusedWidgetIdRef, setFocusedWidgetIdRef] = (0, import_element52.useState)({
      current: null
    });
    const focusWidget = (0, import_element52.useCallback)(
      (widgetId) => {
        for (const sidebarControl of sidebarControls) {
          const widgets = sidebarControl.setting.get();
          if (widgets.includes(widgetId)) {
            sidebarControl.sectionInstance.expand({
              // Schedule it after the complete callback so that
              // it won't be overridden by the "Back" button focus.
              completeCallback() {
                setFocusedWidgetIdRef({ current: widgetId });
              }
            });
            break;
          }
        }
      },
      [sidebarControls]
    );
    (0, import_element52.useEffect)(() => {
      function handleFocus(settingId) {
        const widgetId = settingIdToWidgetId(settingId);
        focusWidget(widgetId);
      }
      let previewBound = false;
      function handleReady() {
        api.previewer.preview.bind(
          "focus-control-for-setting",
          handleFocus
        );
        previewBound = true;
      }
      api.previewer.bind("ready", handleReady);
      return () => {
        api.previewer.unbind("ready", handleReady);
        if (previewBound) {
          api.previewer.preview.unbind(
            "focus-control-for-setting",
            handleFocus
          );
        }
      };
    }, [api, focusWidget]);
    const context = (0, import_element52.useMemo)(
      () => [focusedWidgetIdRef, focusWidget],
      [focusedWidgetIdRef, focusWidget]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime58.jsx)(FocusControlContext.Provider, { value: context, children });
  }
  var useFocusControl = () => (0, import_element52.useContext)(FocusControlContext);

  // packages/customize-widgets/build-module/components/focus-control/use-blocks-focus-control.mjs
  function useBlocksFocusControl(blocks) {
    const { selectBlock } = (0, import_data9.useDispatch)(import_block_editor4.store);
    const [focusedWidgetIdRef] = useFocusControl();
    const blocksRef = (0, import_element53.useRef)(blocks);
    (0, import_element53.useEffect)(() => {
      blocksRef.current = blocks;
    }, [blocks]);
    (0, import_element53.useEffect)(() => {
      if (focusedWidgetIdRef.current) {
        const focusedBlock = blocksRef.current.find(
          (block) => (0, import_widgets3.getWidgetIdFromBlock)(block) === focusedWidgetIdRef.current
        );
        if (focusedBlock) {
          selectBlock(focusedBlock.clientId);
          const blockNode = document.querySelector(
            `[data-block="${focusedBlock.clientId}"]`
          );
          blockNode?.focus();
        }
      }
    }, [focusedWidgetIdRef, selectBlock]);
  }

  // packages/customize-widgets/build-module/lock-unlock.mjs
  var import_private_apis2 = __toESM(require_private_apis(), 1);
  var { lock: lock2, unlock: unlock2 } = (0, import_private_apis2.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/customize-widgets"
  );

  // packages/customize-widgets/build-module/components/sidebar-block-editor/sidebar-editor-provider.mjs
  var import_jsx_runtime59 = __toESM(require_jsx_runtime(), 1);
  var { ExperimentalBlockEditorProvider } = unlock2(import_block_editor5.privateApis);
  function SidebarEditorProvider({
    sidebar,
    settings,
    children
  }) {
    const [blocks, onInput, onChange] = useSidebarBlockEditor(sidebar);
    useBlocksFocusControl(blocks);
    return /* @__PURE__ */ (0, import_jsx_runtime59.jsx)(
      ExperimentalBlockEditorProvider,
      {
        value: blocks,
        onInput,
        onChange,
        settings,
        useSubRegistry: false,
        children
      }
    );
  }

  // packages/customize-widgets/build-module/components/welcome-guide/index.mjs
  var import_i18n13 = __toESM(require_i18n(), 1);
  var import_components6 = __toESM(require_components(), 1);
  var import_data10 = __toESM(require_data(), 1);
  var import_preferences2 = __toESM(require_preferences(), 1);
  var import_jsx_runtime60 = __toESM(require_jsx_runtime(), 1);
  function WelcomeGuide({ sidebar }) {
    const { toggle } = (0, import_data10.useDispatch)(import_preferences2.store);
    const isEntirelyBlockWidgets = sidebar.getWidgets().every((widget) => widget.id.startsWith("block-"));
    return /* @__PURE__ */ (0, import_jsx_runtime60.jsxs)("div", { className: "customize-widgets-welcome-guide", children: [
      /* @__PURE__ */ (0, import_jsx_runtime60.jsx)("div", { className: "customize-widgets-welcome-guide__image__wrapper", children: /* @__PURE__ */ (0, import_jsx_runtime60.jsxs)("picture", { children: [
        /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(
          "source",
          {
            srcSet: "https://s.w.org/images/block-editor/welcome-editor.svg",
            media: "(prefers-reduced-motion: reduce)"
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(
          "img",
          {
            className: "customize-widgets-welcome-guide__image",
            src: "https://s.w.org/images/block-editor/welcome-editor.gif",
            width: "312",
            height: "240",
            alt: ""
          }
        )
      ] }) }),
      /* @__PURE__ */ (0, import_jsx_runtime60.jsx)("h1", { className: "customize-widgets-welcome-guide__heading", children: (0, import_i18n13.__)("Welcome to block Widgets") }),
      /* @__PURE__ */ (0, import_jsx_runtime60.jsx)("p", { className: "customize-widgets-welcome-guide__text", children: isEntirelyBlockWidgets ? (0, import_i18n13.__)(
        "Your theme provides different \u201Cblock\u201D areas for you to add and edit content.\xA0Try adding a search bar, social icons, or other types of blocks here and see how they\u2019ll look on your site."
      ) : (0, import_i18n13.__)(
        "You can now add any block to your site\u2019s widget areas. Don\u2019t worry, all of your favorite widgets still work flawlessly."
      ) }),
      /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(
        import_components6.Button,
        {
          size: "compact",
          variant: "primary",
          onClick: () => toggle("core/customize-widgets", "welcomeGuide"),
          children: (0, import_i18n13.__)("Got it")
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime60.jsx)("hr", { className: "customize-widgets-welcome-guide__separator" }),
      !isEntirelyBlockWidgets && /* @__PURE__ */ (0, import_jsx_runtime60.jsxs)("p", { className: "customize-widgets-welcome-guide__more-info", children: [
        (0, import_i18n13.__)("Want to stick with the old widgets?"),
        /* @__PURE__ */ (0, import_jsx_runtime60.jsx)("br", {}),
        /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(
          import_components6.ExternalLink,
          {
            href: (0, import_i18n13.__)(
              "https://wordpress.org/plugins/classic-widgets/"
            ),
            children: (0, import_i18n13.__)("Get the Classic Widgets plugin.")
          }
        )
      ] }),
      /* @__PURE__ */ (0, import_jsx_runtime60.jsxs)("p", { className: "customize-widgets-welcome-guide__more-info", children: [
        (0, import_i18n13.__)("New to the block editor?"),
        /* @__PURE__ */ (0, import_jsx_runtime60.jsx)("br", {}),
        /* @__PURE__ */ (0, import_jsx_runtime60.jsx)(
          import_components6.ExternalLink,
          {
            href: (0, import_i18n13.__)(
              "https://wordpress.org/documentation/article/wordpress-block-editor/"
            ),
            children: (0, import_i18n13.__)("Here's a detailed guide.")
          }
        )
      ] })
    ] });
  }

  // packages/customize-widgets/build-module/components/keyboard-shortcuts/index.mjs
  var import_element54 = __toESM(require_element(), 1);
  var import_keyboard_shortcuts4 = __toESM(require_keyboard_shortcuts(), 1);
  var import_keycodes4 = __toESM(require_keycodes(), 1);
  var import_data11 = __toESM(require_data(), 1);
  var import_i18n14 = __toESM(require_i18n(), 1);
  var import_block_editor6 = __toESM(require_block_editor(), 1);
  var { usesNativeUndo } = unlock2(import_block_editor6.privateApis);
  function KeyboardShortcuts({ undo, redo, save }) {
    (0, import_keyboard_shortcuts4.useShortcut)("core/customize-widgets/undo", (event) => {
      if (usesNativeUndo(event)) {
        return;
      }
      undo();
      event.preventDefault();
    });
    (0, import_keyboard_shortcuts4.useShortcut)("core/customize-widgets/redo", (event) => {
      if (usesNativeUndo(event)) {
        return;
      }
      redo();
      event.preventDefault();
    });
    (0, import_keyboard_shortcuts4.useShortcut)("core/customize-widgets/save", (event) => {
      event.preventDefault();
      save();
    });
    return null;
  }
  function KeyboardShortcutsRegister() {
    const { registerShortcut, unregisterShortcut } = (0, import_data11.useDispatch)(
      import_keyboard_shortcuts4.store
    );
    (0, import_element54.useEffect)(() => {
      registerShortcut({
        name: "core/customize-widgets/undo",
        category: "global",
        description: (0, import_i18n14.__)("Undo your last changes."),
        keyCombination: {
          modifier: "primary",
          character: "z"
        }
      });
      registerShortcut({
        name: "core/customize-widgets/redo",
        category: "global",
        description: (0, import_i18n14.__)("Redo your last undo."),
        keyCombination: {
          modifier: "primaryShift",
          character: "z"
        },
        // Disable on Apple OS because it conflicts with the browser's
        // history shortcut. It's a fine alias for both Windows and Linux.
        // Since there's no conflict for Ctrl+Shift+Z on both Windows and
        // Linux, we keep it as the default for consistency.
        aliases: (0, import_keycodes4.isAppleOS)() ? [] : [
          {
            modifier: "primary",
            character: "y"
          }
        ]
      });
      registerShortcut({
        name: "core/customize-widgets/save",
        category: "global",
        description: (0, import_i18n14.__)("Save your changes."),
        keyCombination: {
          modifier: "primary",
          character: "s"
        }
      });
      return () => {
        unregisterShortcut("core/customize-widgets/undo");
        unregisterShortcut("core/customize-widgets/redo");
        unregisterShortcut("core/customize-widgets/save");
      };
    }, [registerShortcut]);
    return null;
  }
  KeyboardShortcuts.Register = KeyboardShortcutsRegister;
  var keyboard_shortcuts_default = KeyboardShortcuts;

  // packages/customize-widgets/build-module/components/block-appender/index.mjs
  var import_element55 = __toESM(require_element(), 1);
  var import_block_editor7 = __toESM(require_block_editor(), 1);
  var import_data12 = __toESM(require_data(), 1);
  var import_jsx_runtime61 = __toESM(require_jsx_runtime(), 1);
  function BlockAppender(props) {
    const ref = (0, import_element55.useRef)();
    const isBlocksListEmpty = (0, import_data12.useSelect)(
      (select) => select(import_block_editor7.store).getBlockCount() === 0
    );
    (0, import_element55.useEffect)(() => {
      if (isBlocksListEmpty && ref.current) {
        const { ownerDocument: ownerDocument2 } = ref.current;
        if (!ownerDocument2.activeElement || ownerDocument2.activeElement === ownerDocument2.body) {
          ref.current.focus();
        }
      }
    }, [isBlocksListEmpty]);
    return /* @__PURE__ */ (0, import_jsx_runtime61.jsx)(import_block_editor7.ButtonBlockAppender, { ...props, ref });
  }

  // packages/customize-widgets/build-module/components/sidebar-block-editor/index.mjs
  var import_jsx_runtime62 = __toESM(require_jsx_runtime(), 1);
  var { ExperimentalBlockCanvas: BlockCanvas } = unlock2(
    import_block_editor8.privateApis
  );
  function SidebarBlockEditor({
    blockEditorSettings,
    sidebar,
    inserter,
    inspector
  }) {
    const [isInserterOpened2, setIsInserterOpened2] = useInserter(inserter);
    const isMediumViewport = (0, import_compose4.useViewportMatch)("small");
    const {
      hasUploadPermissions,
      isFixedToolbarActive,
      keepCaretInsideBlock,
      isWelcomeGuideActive
    } = (0, import_data13.useSelect)((select) => {
      const { get } = select(import_preferences3.store);
      return {
        hasUploadPermissions: select(import_core_data.store).canUser("create", {
          kind: "postType",
          name: "attachment"
        }) ?? true,
        isFixedToolbarActive: !!get(
          "core/customize-widgets",
          "fixedToolbar"
        ),
        keepCaretInsideBlock: !!get(
          "core/customize-widgets",
          "keepCaretInsideBlock"
        ),
        isWelcomeGuideActive: !!get(
          "core/customize-widgets",
          "welcomeGuide"
        )
      };
    }, []);
    const settings = (0, import_element56.useMemo)(() => {
      let mediaUploadBlockEditor;
      if (hasUploadPermissions) {
        mediaUploadBlockEditor = ({ onError, ...argumentsObject }) => {
          (0, import_media_utils.uploadMedia)({
            wpAllowedMimeTypes: blockEditorSettings.allowedMimeTypes,
            onError: ({ message }) => onError(message),
            ...argumentsObject
          });
        };
      }
      return {
        ...blockEditorSettings,
        __experimentalSetIsInserterOpened: setIsInserterOpened2,
        mediaUpload: mediaUploadBlockEditor,
        hasFixedToolbar: isFixedToolbarActive || !isMediumViewport,
        keepCaretInsideBlock,
        __unstableHasCustomAppender: true
      };
    }, [
      hasUploadPermissions,
      blockEditorSettings,
      isFixedToolbarActive,
      isMediumViewport,
      keepCaretInsideBlock,
      setIsInserterOpened2
    ]);
    if (isWelcomeGuideActive) {
      return /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(WelcomeGuide, { sidebar });
    }
    return /* @__PURE__ */ (0, import_jsx_runtime62.jsxs)(import_jsx_runtime62.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(keyboard_shortcuts_default.Register, {}),
      /* @__PURE__ */ (0, import_jsx_runtime62.jsxs)(SidebarEditorProvider, { sidebar, settings, children: [
        /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(
          keyboard_shortcuts_default,
          {
            undo: sidebar.undo,
            redo: sidebar.redo,
            save: sidebar.save
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(
          header_default,
          {
            sidebar,
            inserter,
            isInserterOpened: isInserterOpened2,
            setIsInserterOpened: setIsInserterOpened2,
            isFixedToolbarActive: isFixedToolbarActive || !isMediumViewport
          }
        ),
        (isFixedToolbarActive || !isMediumViewport) && /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(import_block_editor8.BlockToolbar, { hideDragHandle: true }),
        /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(
          BlockCanvas,
          {
            shouldIframe: false,
            styles: settings.defaultEditorStyles,
            height: "100%",
            children: /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(import_block_editor8.BlockList, { renderAppender: BlockAppender })
          }
        ),
        (0, import_element56.createPortal)(
          // This is a temporary hack to prevent button component inside <BlockInspector>
          // from submitting form when type="button" is not specified.
          /* @__PURE__ */ (0, import_jsx_runtime62.jsx)("form", { onSubmit: (event) => event.preventDefault(), children: /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(import_block_editor8.BlockInspector, {}) }),
          inspector.contentContainer[0]
        )
      ] }),
      /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(import_block_editor8.__unstableBlockSettingsMenuFirstItem, { children: ({ onClose }) => /* @__PURE__ */ (0, import_jsx_runtime62.jsx)(
        block_inspector_button_default,
        {
          inspector,
          closeMenu: onClose
        }
      ) })
    ] });
  }

  // packages/customize-widgets/build-module/components/sidebar-controls/index.mjs
  var import_element57 = __toESM(require_element(), 1);
  var import_jsx_runtime63 = __toESM(require_jsx_runtime(), 1);
  var SidebarControlsContext = (0, import_element57.createContext)();
  SidebarControlsContext.displayName = "SidebarControlsContext";
  function SidebarControls({
    sidebarControls,
    activeSidebarControl,
    children
  }) {
    const context = (0, import_element57.useMemo)(
      () => ({
        sidebarControls,
        activeSidebarControl
      }),
      [sidebarControls, activeSidebarControl]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(SidebarControlsContext.Provider, { value: context, children });
  }
  function useSidebarControls() {
    const { sidebarControls } = (0, import_element57.useContext)(SidebarControlsContext);
    return sidebarControls;
  }
  function useActiveSidebarControl() {
    const { activeSidebarControl } = (0, import_element57.useContext)(SidebarControlsContext);
    return activeSidebarControl;
  }

  // packages/customize-widgets/build-module/components/customize-widgets/use-clear-selected-block.mjs
  var import_element58 = __toESM(require_element(), 1);
  var import_data14 = __toESM(require_data(), 1);
  var import_block_editor9 = __toESM(require_block_editor(), 1);
  function useClearSelectedBlock(sidebarControl, popoverRef) {
    const { hasSelectedBlock, hasMultiSelection } = (0, import_data14.useSelect)(import_block_editor9.store);
    const { clearSelectedBlock } = (0, import_data14.useDispatch)(import_block_editor9.store);
    (0, import_element58.useEffect)(() => {
      if (popoverRef.current && sidebarControl) {
        let handleClearSelectedBlock = function(element) {
          if (
            // 1. Make sure there are blocks being selected.
            (hasSelectedBlock() || hasMultiSelection()) && // 2. The element should exist in the DOM (not deleted).
            element && ownerDocument2.contains(element) && // 3. It should also not exist in the container, the popover, nor the dialog.
            !container.contains(element) && !popoverRef.current.contains(element) && !element.closest('[role="dialog"]') && // 4. The inspector should not be opened.
            !inspector.expanded()
          ) {
            clearSelectedBlock();
          }
        }, handleMouseDown = function(event) {
          handleClearSelectedBlock(event.target);
        }, handleBlur = function() {
          handleClearSelectedBlock(ownerDocument2.activeElement);
        };
        const inspector = sidebarControl.inspector;
        const container = sidebarControl.container[0];
        const ownerDocument2 = container.ownerDocument;
        const ownerWindow = ownerDocument2.defaultView;
        ownerDocument2.addEventListener("mousedown", handleMouseDown);
        ownerWindow.addEventListener("blur", handleBlur);
        return () => {
          ownerDocument2.removeEventListener(
            "mousedown",
            handleMouseDown
          );
          ownerWindow.removeEventListener("blur", handleBlur);
        };
      }
    }, [
      popoverRef,
      sidebarControl,
      hasSelectedBlock,
      hasMultiSelection,
      clearSelectedBlock
    ]);
  }

  // packages/customize-widgets/build-module/components/customize-widgets/index.mjs
  var import_jsx_runtime64 = __toESM(require_jsx_runtime(), 1);
  function CustomizeWidgets({
    api,
    sidebarControls,
    blockEditorSettings
  }) {
    const [activeSidebarControl, setActiveSidebarControl] = (0, import_element59.useState)(null);
    const parentContainer = document.getElementById(
      "customize-theme-controls"
    );
    const popoverRef = (0, import_element59.useRef)();
    const adminPrimary = (0, import_element59.useMemo)(() => getAdminThemeColors().primary, []);
    useClearSelectedBlock(activeSidebarControl, popoverRef);
    (0, import_element59.useEffect)(() => {
      const unsubscribers = sidebarControls.map(
        (sidebarControl) => sidebarControl.subscribe((expanded) => {
          if (expanded) {
            setActiveSidebarControl(sidebarControl);
          }
        })
      );
      return () => {
        unsubscribers.forEach((unsubscriber) => unsubscriber());
      };
    }, [sidebarControls]);
    const activeSidebar = activeSidebarControl && (0, import_element59.createPortal)(
      /* @__PURE__ */ (0, import_jsx_runtime64.jsx)(ErrorBoundary, { children: /* @__PURE__ */ (0, import_jsx_runtime64.jsx)(
        SidebarBlockEditor,
        {
          blockEditorSettings,
          sidebar: activeSidebarControl.sidebarAdapter,
          inserter: activeSidebarControl.inserter,
          inspector: activeSidebarControl.inspector
        },
        activeSidebarControl.id
      ) }),
      activeSidebarControl.container[0]
    );
    const popover = parentContainer && (0, import_element59.createPortal)(
      /* @__PURE__ */ (0, import_jsx_runtime64.jsx)("div", { className: "customize-widgets-popover", ref: popoverRef, children: /* @__PURE__ */ (0, import_jsx_runtime64.jsx)(import_components7.Popover.Slot, {}) }),
      parentContainer
    );
    return /* @__PURE__ */ (0, import_jsx_runtime64.jsx)(import_components7.SlotFillProvider, { children: /* @__PURE__ */ (0, import_jsx_runtime64.jsx)(import_theme.ThemeProvider, { isRoot: true, color: { primary: adminPrimary }, children: /* @__PURE__ */ (0, import_jsx_runtime64.jsx)(
      SidebarControls,
      {
        sidebarControls,
        activeSidebarControl,
        children: /* @__PURE__ */ (0, import_jsx_runtime64.jsxs)(
          FocusControl,
          {
            api,
            sidebarControls,
            children: [
              activeSidebar,
              popover
            ]
          }
        )
      }
    ) }) });
  }

  // packages/customize-widgets/build-module/controls/sidebar-section.mjs
  var import_i18n15 = __toESM(require_i18n(), 1);

  // packages/customize-widgets/build-module/controls/inspector-section.mjs
  function getInspectorSection() {
    const {
      wp: { customize }
    } = window;
    return class InspectorSection extends customize.Section {
      constructor(id, options) {
        super(id, options);
        this.parentSection = options.parentSection;
        this.returnFocusWhenClose = null;
        this._isOpen = false;
      }
      get isOpen() {
        return this._isOpen;
      }
      set isOpen(value) {
        this._isOpen = value;
        this.triggerActiveCallbacks();
      }
      ready() {
        this.contentContainer[0].classList.add(
          "customize-widgets-layout__inspector"
        );
      }
      isContextuallyActive() {
        return this.isOpen;
      }
      onChangeExpanded(expanded, args) {
        super.onChangeExpanded(expanded, args);
        if (this.parentSection && !args.unchanged) {
          if (expanded) {
            this.parentSection.collapse({
              manualTransition: true
            });
          } else {
            this.parentSection.expand({
              manualTransition: true,
              completeCallback: () => {
                if (this.returnFocusWhenClose && !this.contentContainer[0].contains(
                  this.returnFocusWhenClose
                )) {
                  this.returnFocusWhenClose.focus();
                }
              }
            });
          }
        }
      }
      open({ returnFocusWhenClose } = {}) {
        this.isOpen = true;
        this.returnFocusWhenClose = returnFocusWhenClose;
        this.expand({
          allowMultiple: true
        });
      }
      close() {
        this.collapse({
          allowMultiple: true
        });
      }
      collapse(options) {
        this.isOpen = false;
        super.collapse(options);
      }
      triggerActiveCallbacks() {
        this.active.callbacks.fireWith(this.active, [false, true]);
      }
    };
  }

  // packages/customize-widgets/build-module/controls/sidebar-section.mjs
  var getInspectorSectionId = (sidebarId) => `widgets-inspector-${sidebarId}`;
  function getSidebarSection() {
    const {
      wp: { customize }
    } = window;
    const reduceMotionMediaQuery = window.matchMedia(
      "(prefers-reduced-motion: reduce)"
    );
    let isReducedMotion = reduceMotionMediaQuery.matches;
    reduceMotionMediaQuery.addEventListener("change", (event) => {
      isReducedMotion = event.matches;
    });
    return class SidebarSection extends customize.Section {
      ready() {
        const InspectorSection = getInspectorSection();
        this.inspector = new InspectorSection(
          getInspectorSectionId(this.id),
          {
            title: (0, import_i18n15.__)("Block Settings"),
            parentSection: this,
            customizeAction: [
              (0, import_i18n15.__)("Customizing"),
              (0, import_i18n15.__)("Widgets"),
              this.params.title
            ].join(" \u25B8 ")
          }
        );
        customize.section.add(this.inspector);
        this.contentContainer[0].classList.add(
          "customize-widgets__sidebar-section"
        );
      }
      hasSubSectionOpened() {
        return this.inspector.expanded();
      }
      onChangeExpanded(expanded, _args) {
        const controls = this.controls();
        const args = {
          ..._args,
          completeCallback() {
            controls.forEach((control) => {
              control.onChangeSectionExpanded?.(expanded, args);
            });
            _args.completeCallback?.();
          }
        };
        if (args.manualTransition) {
          if (expanded) {
            this.contentContainer.addClass(["busy", "open"]);
            this.contentContainer.removeClass("is-sub-section-open");
            this.contentContainer.closest(".wp-full-overlay").addClass("section-open");
          } else {
            this.contentContainer.addClass([
              "busy",
              "is-sub-section-open"
            ]);
            this.contentContainer.closest(".wp-full-overlay").addClass("section-open");
            this.contentContainer.removeClass("open");
          }
          const handleTransitionEnd = () => {
            this.contentContainer.removeClass("busy");
            args.completeCallback();
          };
          if (isReducedMotion) {
            handleTransitionEnd();
          } else {
            this.contentContainer.one(
              "transitionend",
              handleTransitionEnd
            );
          }
        } else {
          super.onChangeExpanded(expanded, args);
        }
      }
    };
  }

  // packages/customize-widgets/build-module/controls/sidebar-control.mjs
  var import_data16 = __toESM(require_data(), 1);

  // packages/customize-widgets/build-module/components/sidebar-block-editor/sidebar-adapter.mjs
  var { wp } = window;
  function parseWidgetId(widgetId) {
    const matches = widgetId.match(/^(.+)-(\d+)$/);
    if (matches) {
      return {
        idBase: matches[1],
        number: parseInt(matches[2], 10)
      };
    }
    return { idBase: widgetId };
  }
  function widgetIdToSettingId(widgetId) {
    const { idBase, number } = parseWidgetId(widgetId);
    if (number) {
      return `widget_${idBase}[${number}]`;
    }
    return `widget_${idBase}`;
  }
  function debounce(leading, callback, timeout) {
    let isLeading = false;
    let timerID;
    function debounced(...args) {
      const result = (isLeading ? callback : leading).apply(this, args);
      isLeading = true;
      clearTimeout(timerID);
      timerID = setTimeout(() => {
        isLeading = false;
      }, timeout);
      return result;
    }
    debounced.cancel = () => {
      isLeading = false;
      clearTimeout(timerID);
    };
    return debounced;
  }
  var SidebarAdapter = class {
    constructor(setting, api) {
      this.setting = setting;
      this.api = api;
      this.locked = false;
      this.widgetsCache = /* @__PURE__ */ new WeakMap();
      this.subscribers = /* @__PURE__ */ new Set();
      this.history = [
        this._getWidgetIds().map(
          (widgetId) => this.getWidget(widgetId)
        )
      ];
      this.historyIndex = 0;
      this.historySubscribers = /* @__PURE__ */ new Set();
      this._debounceSetHistory = debounce(
        this._pushHistory,
        this._replaceHistory,
        1e3
      );
      this.setting.bind(this._handleSettingChange.bind(this));
      this.api.bind("change", this._handleAllSettingsChange.bind(this));
      this.undo = this.undo.bind(this);
      this.redo = this.redo.bind(this);
      this.save = this.save.bind(this);
    }
    subscribe(callback) {
      this.subscribers.add(callback);
      return () => {
        this.subscribers.delete(callback);
      };
    }
    getWidgets() {
      return this.history[this.historyIndex];
    }
    _emit(...args) {
      for (const callback of this.subscribers) {
        callback(...args);
      }
    }
    _getWidgetIds() {
      return this.setting.get();
    }
    _pushHistory() {
      this.history = [
        ...this.history.slice(0, this.historyIndex + 1),
        this._getWidgetIds().map(
          (widgetId) => this.getWidget(widgetId)
        )
      ];
      this.historyIndex += 1;
      this.historySubscribers.forEach((listener) => listener());
    }
    _replaceHistory() {
      this.history[this.historyIndex] = this._getWidgetIds().map(
        (widgetId) => this.getWidget(widgetId)
      );
    }
    _handleSettingChange() {
      if (this.locked) {
        return;
      }
      const prevWidgets = this.getWidgets();
      this._pushHistory();
      this._emit(prevWidgets, this.getWidgets());
    }
    _handleAllSettingsChange(setting) {
      if (this.locked) {
        return;
      }
      if (!setting.id.startsWith("widget_")) {
        return;
      }
      const widgetId = settingIdToWidgetId(setting.id);
      if (!this.setting.get().includes(widgetId)) {
        return;
      }
      const prevWidgets = this.getWidgets();
      this._pushHistory();
      this._emit(prevWidgets, this.getWidgets());
    }
    _createWidget(widget) {
      const widgetModel = wp.customize.Widgets.availableWidgets.findWhere({
        id_base: widget.idBase
      });
      let number = widget.number;
      if (widgetModel.get("is_multi") && !number) {
        widgetModel.set(
          "multi_number",
          widgetModel.get("multi_number") + 1
        );
        number = widgetModel.get("multi_number");
      }
      const settingId = number ? `widget_${widget.idBase}[${number}]` : `widget_${widget.idBase}`;
      const settingArgs = {
        transport: wp.customize.Widgets.data.selectiveRefreshableWidgets[widgetModel.get("id_base")] ? "postMessage" : "refresh",
        previewer: this.setting.previewer
      };
      const setting = this.api.create(
        settingId,
        settingId,
        "",
        settingArgs
      );
      setting.set(widget.instance);
      const widgetId = settingIdToWidgetId(settingId);
      return widgetId;
    }
    _removeWidget(widget) {
      const settingId = widgetIdToSettingId(widget.id);
      const setting = this.api(settingId);
      if (setting) {
        const instance = setting.get();
        this.widgetsCache.delete(instance);
      }
      this.api.remove(settingId);
    }
    _updateWidget(widget) {
      const prevWidget = this.getWidget(widget.id);
      if (prevWidget === widget) {
        return widget.id;
      }
      if (prevWidget.idBase && widget.idBase && prevWidget.idBase === widget.idBase) {
        const settingId = widgetIdToSettingId(widget.id);
        this.api(settingId).set(widget.instance);
        return widget.id;
      }
      this._removeWidget(widget);
      return this._createWidget(widget);
    }
    getWidget(widgetId) {
      if (!widgetId) {
        return null;
      }
      const { idBase, number } = parseWidgetId(widgetId);
      const settingId = widgetIdToSettingId(widgetId);
      const setting = this.api(settingId);
      if (!setting) {
        return null;
      }
      const instance = setting.get();
      if (this.widgetsCache.has(instance)) {
        return this.widgetsCache.get(instance);
      }
      const widget = {
        id: widgetId,
        idBase,
        number,
        instance
      };
      this.widgetsCache.set(instance, widget);
      return widget;
    }
    _updateWidgets(nextWidgets) {
      this.locked = true;
      const addedWidgetIds = [];
      const nextWidgetIds = nextWidgets.map((nextWidget) => {
        if (nextWidget.id && this.getWidget(nextWidget.id)) {
          addedWidgetIds.push(null);
          return this._updateWidget(nextWidget);
        }
        const widgetId = this._createWidget(nextWidget);
        addedWidgetIds.push(widgetId);
        return widgetId;
      });
      const deletedWidgets = this.getWidgets().filter(
        (widget) => !nextWidgetIds.includes(widget.id)
      );
      deletedWidgets.forEach((widget) => this._removeWidget(widget));
      this.setting.set(nextWidgetIds);
      this.locked = false;
      return addedWidgetIds;
    }
    setWidgets(nextWidgets) {
      const addedWidgetIds = this._updateWidgets(nextWidgets);
      this._debounceSetHistory();
      return addedWidgetIds;
    }
    /**
     * Undo/Redo related features
     */
    hasUndo() {
      return this.historyIndex > 0;
    }
    hasRedo() {
      return this.historyIndex < this.history.length - 1;
    }
    _seek(historyIndex) {
      const currentWidgets = this.getWidgets();
      this.historyIndex = historyIndex;
      const widgets = this.history[this.historyIndex];
      this._updateWidgets(widgets);
      this._emit(currentWidgets, this.getWidgets());
      this.historySubscribers.forEach((listener) => listener());
      this._debounceSetHistory.cancel();
    }
    undo() {
      if (!this.hasUndo()) {
        return;
      }
      this._seek(this.historyIndex - 1);
    }
    redo() {
      if (!this.hasRedo()) {
        return;
      }
      this._seek(this.historyIndex + 1);
    }
    subscribeHistory(listener) {
      this.historySubscribers.add(listener);
      return () => {
        this.historySubscribers.delete(listener);
      };
    }
    save() {
      this.api.previewer.save();
    }
  };

  // packages/customize-widgets/build-module/controls/inserter-outer-section.mjs
  var import_keycodes5 = __toESM(require_keycodes(), 1);
  var import_dom21 = __toESM(require_dom(), 1);
  var import_data15 = __toESM(require_data(), 1);
  function getInserterOuterSection() {
    const {
      wp: { customize }
    } = window;
    const OuterSection = customize.OuterSection;
    customize.OuterSection = class extends OuterSection {
      onChangeExpanded(expanded, args) {
        if (expanded) {
          customize.section.each((section) => {
            if (section.params.type === "outer" && section.id !== this.id) {
              if (section.expanded()) {
                section.collapse();
              }
            }
          });
        }
        return super.onChangeExpanded(expanded, args);
      }
    };
    customize.sectionConstructor.outer = customize.OuterSection;
    return class InserterOuterSection extends customize.OuterSection {
      constructor(...args) {
        super(...args);
        this.params.type = "outer";
        this.activeElementBeforeExpanded = null;
        const ownerWindow = this.contentContainer[0].ownerDocument.defaultView;
        ownerWindow.addEventListener(
          "keydown",
          (event) => {
            if (this.expanded() && (event.keyCode === import_keycodes5.ESCAPE || event.code === "Escape") && !event.defaultPrevented) {
              event.preventDefault();
              event.stopPropagation();
              (0, import_data15.dispatch)(store).setIsInserterOpened(
                false
              );
            }
          },
          // Use capture mode to make this run before other event listeners.
          true
        );
        this.contentContainer.addClass("widgets-inserter");
        this.isFromInternalAction = false;
        this.expanded.bind(() => {
          if (!this.isFromInternalAction) {
            (0, import_data15.dispatch)(store).setIsInserterOpened(
              this.expanded()
            );
          }
          this.isFromInternalAction = false;
        });
      }
      open() {
        if (!this.expanded()) {
          const contentContainer = this.contentContainer[0];
          this.activeElementBeforeExpanded = contentContainer.ownerDocument.activeElement;
          this.isFromInternalAction = true;
          this.expand({
            completeCallback() {
              const searchBox = import_dom21.focus.tabbable.find(contentContainer)[1];
              if (searchBox) {
                searchBox.focus();
              }
            }
          });
        }
      }
      close() {
        if (this.expanded()) {
          const contentContainer = this.contentContainer[0];
          const activeElement2 = contentContainer.ownerDocument.activeElement;
          this.isFromInternalAction = true;
          this.collapse({
            completeCallback() {
              if (contentContainer.contains(activeElement2)) {
                if (this.activeElementBeforeExpanded) {
                  this.activeElementBeforeExpanded.focus();
                }
              }
            }
          });
        }
      }
    };
  }

  // packages/customize-widgets/build-module/controls/sidebar-control.mjs
  var getInserterId = (controlId) => `widgets-inserter-${controlId}`;
  function getSidebarControl() {
    const {
      wp: { customize }
    } = window;
    return class SidebarControl extends customize.Control {
      constructor(...args) {
        super(...args);
        this.subscribers = /* @__PURE__ */ new Set();
      }
      ready() {
        const InserterOuterSection = getInserterOuterSection();
        this.inserter = new InserterOuterSection(
          getInserterId(this.id),
          {}
        );
        customize.section.add(this.inserter);
        this.sectionInstance = customize.section(this.section());
        this.inspector = this.sectionInstance.inspector;
        this.sidebarAdapter = new SidebarAdapter(this.setting, customize);
      }
      subscribe(callback) {
        this.subscribers.add(callback);
        return () => {
          this.subscribers.delete(callback);
        };
      }
      onChangeSectionExpanded(expanded, args) {
        if (!args.unchanged) {
          if (!expanded) {
            (0, import_data16.dispatch)(store).setIsInserterOpened(
              false
            );
          }
          this.subscribers.forEach(
            (subscriber) => subscriber(expanded, args)
          );
        }
      }
    };
  }

  // packages/customize-widgets/build-module/filters/move-to-sidebar.mjs
  var import_block_editor10 = __toESM(require_block_editor(), 1);
  var import_compose5 = __toESM(require_compose(), 1);
  var import_data17 = __toESM(require_data(), 1);
  var import_hooks2 = __toESM(require_hooks(), 1);
  var import_widgets4 = __toESM(require_widgets(), 1);
  var import_jsx_runtime65 = __toESM(require_jsx_runtime(), 1);
  var withMoveToSidebarToolbarItem = (0, import_compose5.createHigherOrderComponent)(
    (BlockEdit) => (props) => {
      let widgetId = (0, import_widgets4.getWidgetIdFromBlock)(props);
      const sidebarControls = useSidebarControls();
      const activeSidebarControl = useActiveSidebarControl();
      const hasMultipleSidebars = sidebarControls?.length > 1;
      const blockName = props.name;
      const clientId = props.clientId;
      const canInsertBlockInSidebar = (0, import_data17.useSelect)(
        (select) => {
          return select(import_block_editor10.store).canInsertBlockType(
            blockName,
            ""
          );
        },
        [blockName]
      );
      const block = (0, import_data17.useSelect)(
        (select) => select(import_block_editor10.store).getBlock(clientId),
        [clientId]
      );
      const { removeBlock } = (0, import_data17.useDispatch)(import_block_editor10.store);
      const [, focusWidget] = useFocusControl();
      function moveToSidebar(sidebarControlId) {
        const newSidebarControl = sidebarControls.find(
          (sidebarControl) => sidebarControl.id === sidebarControlId
        );
        if (widgetId) {
          const oldSetting = activeSidebarControl.setting;
          const newSetting = newSidebarControl.setting;
          oldSetting(oldSetting().filter((id) => id !== widgetId));
          newSetting([...newSetting(), widgetId]);
        } else {
          const sidebarAdapter = newSidebarControl.sidebarAdapter;
          removeBlock(clientId);
          const addedWidgetIds = sidebarAdapter.setWidgets([
            ...sidebarAdapter.getWidgets(),
            blockToWidget(block)
          ]);
          widgetId = addedWidgetIds.reverse().find((id) => !!id);
        }
        focusWidget(widgetId);
      }
      return /* @__PURE__ */ (0, import_jsx_runtime65.jsxs)(import_jsx_runtime65.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime65.jsx)(BlockEdit, { ...props }, "edit"),
        hasMultipleSidebars && canInsertBlockInSidebar && /* @__PURE__ */ (0, import_jsx_runtime65.jsx)(import_block_editor10.BlockControls, { children: /* @__PURE__ */ (0, import_jsx_runtime65.jsx)(
          import_widgets4.MoveToWidgetArea,
          {
            widgetAreas: sidebarControls.map(
              (sidebarControl) => ({
                id: sidebarControl.id,
                name: sidebarControl.params.label,
                description: sidebarControl.params.description
              })
            ),
            currentWidgetAreaId: activeSidebarControl?.id,
            onSelect: moveToSidebar
          }
        ) })
      ] });
    },
    "withMoveToSidebarToolbarItem"
  );
  (0, import_hooks2.addFilter)(
    "editor.BlockEdit",
    "core/customize-widgets/block-edit",
    withMoveToSidebarToolbarItem
  );

  // packages/customize-widgets/build-module/filters/replace-media-upload.mjs
  var import_hooks3 = __toESM(require_hooks(), 1);
  var import_media_utils2 = __toESM(require_media_utils(), 1);
  var replaceMediaUpload = () => import_media_utils2.MediaUpload;
  (0, import_hooks3.addFilter)(
    "editor.MediaUpload",
    "core/edit-widgets/replace-media-upload",
    replaceMediaUpload
  );

  // packages/customize-widgets/build-module/filters/wide-widget-display.mjs
  var import_compose6 = __toESM(require_compose(), 1);
  var import_hooks4 = __toESM(require_hooks(), 1);
  var import_jsx_runtime66 = __toESM(require_jsx_runtime(), 1);
  var { wp: wp2 } = window;
  var withWideWidgetDisplay = (0, import_compose6.createHigherOrderComponent)(
    (BlockEdit) => (props) => {
      const { idBase } = props.attributes;
      const isWide = wp2.customize.Widgets.data.availableWidgets.find(
        (widget) => widget.id_base === idBase
      )?.is_wide ?? false;
      return /* @__PURE__ */ (0, import_jsx_runtime66.jsx)(BlockEdit, { ...props, isWide }, "edit");
    },
    "withWideWidgetDisplay"
  );
  (0, import_hooks4.addFilter)(
    "editor.BlockEdit",
    "core/customize-widgets/wide-widget-display",
    withWideWidgetDisplay
  );

  // packages/customize-widgets/build-module/index.mjs
  var import_jsx_runtime67 = __toESM(require_jsx_runtime(), 1);
  var { wp: wp3 } = window;
  var DISABLED_BLOCKS = [
    "core/more",
    "core/block",
    "core/freeform",
    "core/template-part"
  ];
  function initialize(editorName, blockEditorSettings) {
    (0, import_data18.dispatch)(import_preferences4.store).setDefaults("core/customize-widgets", {
      fixedToolbar: false,
      welcomeGuide: true
    });
    (0, import_data18.dispatch)(import_blocks2.store).reapplyBlockTypeFilters();
    const coreBlocks = (0, import_block_library.__experimentalGetCoreBlocks)().filter((block) => {
      return !(DISABLED_BLOCKS.includes(block.name) || block.name.startsWith("core/post") || block.name.startsWith("core/query") || block.name.startsWith("core/site") || block.name.startsWith("core/navigation") || block.name.startsWith("core/term"));
    });
    (0, import_block_library.registerCoreBlocks)(coreBlocks);
    (0, import_widgets5.registerLegacyWidgetBlock)();
    if (false) {
      (0, import_block_library.__experimentalRegisterExperimentalCoreBlocks)({
        enableFSEBlocks: ENABLE_EXPERIMENTAL_FSE_BLOCKS
      });
    }
    (0, import_widgets5.registerLegacyWidgetVariations)(blockEditorSettings);
    (0, import_widgets5.registerWidgetGroupBlock)();
    (0, import_blocks2.setFreeformContentHandlerName)("core/html");
    const SidebarControl = getSidebarControl(blockEditorSettings);
    wp3.customize.sectionConstructor.sidebar = getSidebarSection();
    wp3.customize.controlConstructor.sidebar_block_editor = SidebarControl;
    const container = document.createElement("div");
    document.body.appendChild(container);
    wp3.customize.bind("ready", () => {
      const sidebarControls = [];
      wp3.customize.control.each((control) => {
        if (control instanceof SidebarControl) {
          sidebarControls.push(control);
        }
      });
      (0, import_element60.createRoot)(container).render(
        /* @__PURE__ */ (0, import_jsx_runtime67.jsx)(import_element60.StrictMode, { children: /* @__PURE__ */ (0, import_jsx_runtime67.jsx)(
          CustomizeWidgets,
          {
            api: wp3.customize,
            sidebarControls,
            blockEditorSettings
          }
        ) })
      );
    });
  }
  return __toCommonJS(index_exports);
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
(window.wp ||= {}).customizeWidgets = wp.customizeWidgets;
})();
