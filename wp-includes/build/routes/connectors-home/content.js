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

// package-external:@wordpress/i18n
var require_i18n = __commonJS({
  "package-external:@wordpress/i18n"(exports, module) {
    module.exports = window.wp.i18n;
  }
});

// package-external:@wordpress/element
var require_element = __commonJS({
  "package-external:@wordpress/element"(exports, module) {
    module.exports = window.wp.element;
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
        didWarnOld18Alpha || void 0 === React53.startTransition || (didWarnOld18Alpha = true, console.error(
          "You are using an outdated, pre-release alpha of React 18 that does not support useSyncExternalStore. The use-sync-external-store shim will not work correctly. Upgrade to a newer pre-release."
        ));
        var value = getSnapshot();
        if (!didWarnUncachedGetSnapshot) {
          var cachedValue = getSnapshot();
          objectIs(value, cachedValue) || (console.error(
            "The result of getSnapshot should be cached to avoid an infinite loop"
          ), didWarnUncachedGetSnapshot = true);
        }
        cachedValue = useState14({
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
        useEffect15(
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
      var React53 = require_react(), objectIs = "function" === typeof Object.is ? Object.is : is, useState14 = React53.useState, useEffect15 = React53.useEffect, useLayoutEffect4 = React53.useLayoutEffect, useDebugValue2 = React53.useDebugValue, didWarnOld18Alpha = false, didWarnUncachedGetSnapshot = false, shim = "undefined" === typeof window || "undefined" === typeof window.document || "undefined" === typeof window.document.createElement ? useSyncExternalStore$1 : useSyncExternalStore$2;
      exports.useSyncExternalStore = void 0 !== React53.useSyncExternalStore ? React53.useSyncExternalStore : shim;
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
      var React53 = require_react(), shim = require_shim(), objectIs = "function" === typeof Object.is ? Object.is : is, useSyncExternalStore2 = shim.useSyncExternalStore, useRef21 = React53.useRef, useEffect15 = React53.useEffect, useMemo17 = React53.useMemo, useDebugValue2 = React53.useDebugValue;
      exports.useSyncExternalStoreWithSelector = function(subscribe, getSnapshot, getServerSnapshot, selector, isEqual) {
        var instRef = useRef21(null);
        if (null === instRef.current) {
          var inst = { hasValue: false, value: null };
          instRef.current = inst;
        } else inst = instRef.current;
        instRef = useMemo17(
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
        var value = useSyncExternalStore2(subscribe, instRef[0], instRef[1]);
        useEffect15(
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

// package-external:@wordpress/primitives
var require_primitives = __commonJS({
  "package-external:@wordpress/primitives"(exports, module) {
    module.exports = window.wp.primitives;
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

// package-external:@wordpress/components
var require_components = __commonJS({
  "package-external:@wordpress/components"(exports, module) {
    module.exports = window.wp.components;
  }
});

// package-external:@wordpress/data
var require_data = __commonJS({
  "package-external:@wordpress/data"(exports, module) {
    module.exports = window.wp.data;
  }
});

// package-external:@wordpress/core-data
var require_core_data = __commonJS({
  "package-external:@wordpress/core-data"(exports, module) {
    module.exports = window.wp.coreData;
  }
});

// package-external:@wordpress/notices
var require_notices = __commonJS({
  "package-external:@wordpress/notices"(exports, module) {
    module.exports = window.wp.notices;
  }
});

// package-external:@wordpress/url
var require_url = __commonJS({
  "package-external:@wordpress/url"(exports, module) {
    module.exports = window.wp.url;
  }
});

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

// packages/ui/build-module/badge/badge.mjs
var import_element9 = __toESM(require_element(), 1);

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

// node_modules/@base-ui/utils/esm/useStableCallback.js
var React3 = __toESM(require_react(), 1);

// node_modules/@base-ui/utils/esm/useRefWithInit.js
var React2 = __toESM(require_react(), 1);
var UNINITIALIZED = {};
function useRefWithInit(init, initArg) {
  const ref = React2.useRef(UNINITIALIZED);
  if (ref.current === UNINITIALIZED) {
    ref.current = init(initArg);
  }
  return ref;
}

// node_modules/@base-ui/utils/esm/useStableCallback.js
var useInsertionEffect = React3[`useInsertionEffect${Math.random().toFixed(1)}`.slice(0, -3)];
var useSafeInsertionEffect = (
  // React 17 doesn't have useInsertionEffect.
  useInsertionEffect && // Preact replaces useInsertionEffect with useLayoutEffect and fires too late.
  useInsertionEffect !== React3.useLayoutEffect ? useInsertionEffect : (fn) => fn()
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

// node_modules/@base-ui/react/esm/internals/direction-context/DirectionContext.js
var React5 = __toESM(require_react(), 1);
var DirectionContext = /* @__PURE__ */ React5.createContext(void 0);
if (true) DirectionContext.displayName = "DirectionContext";
function useDirection() {
  const context = React5.useContext(DirectionContext);
  return context?.direction ?? "ltr";
}

// node_modules/@base-ui/react/esm/internals/useRenderElement.js
var React8 = __toESM(require_react(), 1);

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
var React7 = __toESM(require_react(), 1);

// node_modules/@base-ui/utils/esm/reactVersion.js
var React6 = __toESM(require_react(), 1);
var majorVersion = parseInt(React6.version, 10);
function isReactVersionAtLeast(reactVersionToCheck) {
  return majorVersion >= reactVersionToCheck;
}

// node_modules/@base-ui/utils/esm/getReactElementRef.js
function getReactElementRef(element) {
  if (!/* @__PURE__ */ React7.isValidElement(element)) {
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
      const children = React8.Children.toArray(render);
      newElement = children[0];
    }
    if (true) {
      if (!/* @__PURE__ */ React8.isValidElement(newElement)) {
        throw new Error(["Base UI: The `render` prop was provided an invalid React element as `React.isValidElement(render)` is `false`.", "A valid React element must be provided to the `render` prop because it is cloned with props to replace the default element.", "https://base-ui.com/r/invalid-render-prop"].join("\n"));
      }
    }
    return /* @__PURE__ */ React8.cloneElement(newElement, mergedProps);
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
  return /* @__PURE__ */ React8.createElement(Tag, props);
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
  inputBlur: () => inputBlur,
  inputChange: () => inputChange,
  inputClear: () => inputClear,
  inputPaste: () => inputPaste,
  inputPress: () => inputPress,
  itemPress: () => itemPress,
  keyboard: () => keyboard,
  linkPress: () => linkPress,
  listNavigation: () => listNavigation,
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
var React10 = __toESM(require_react(), 1);

// node_modules/@base-ui/utils/esm/safeReact.js
var React9 = __toESM(require_react(), 1);
var SafeReact = {
  ...React9
};

// node_modules/@base-ui/utils/esm/useId.js
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

// node_modules/@base-ui/react/esm/internals/useBaseUiId.js
function useBaseUiId(idOverride) {
  return useId(idOverride, "base-ui");
}

// node_modules/@base-ui/react/esm/internals/useAnimationsFinished.js
var ReactDOM = __toESM(require_react_dom(), 1);

// node_modules/@base-ui/utils/esm/useOnMount.js
var React11 = __toESM(require_react(), 1);
var EMPTY = [];
function useOnMount(fn) {
  React11.useEffect(fn, EMPTY);
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
    const index2 = id - this.startId;
    if (index2 < 0 || index2 >= this.callbacks.length) {
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

// node_modules/@base-ui/react/esm/utils/resolveRef.js
function resolveRef(maybeRef) {
  if (maybeRef == null) {
    return maybeRef;
  }
  return "current" in maybeRef ? maybeRef.current : maybeRef;
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

// node_modules/@base-ui/react/esm/internals/useTransitionStatus.js
var React12 = __toESM(require_react(), 1);
function useTransitionStatus(open, enableIdleState = false, deferEndingState = false) {
  const [transitionStatus, setTransitionStatus] = React12.useState(open && enableIdleState ? "idle" : void 0);
  const [mounted, setMounted] = React12.useState(open);
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

// node_modules/@base-ui/react/esm/internals/use-button/useButton.js
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
    return node.ownerDocument ? node.ownerDocument.body : node.body;
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

// node_modules/@base-ui/react/esm/internals/composite/root/CompositeRootContext.js
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

// node_modules/@base-ui/react/esm/utils/useFocusableWhenDisabled.js
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

// node_modules/@base-ui/react/esm/internals/use-button/useButton.js
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
    const type = isNativeButton ? "button" : void 0;
    return mergeProps({
      type,
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
    }, !isNativeButton ? {
      role: "button"
    } : void 0, focusableWhenDisabledProps, otherExternalProps);
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

// node_modules/@base-ui/utils/esm/detectBrowser.js
var hasNavigator = typeof navigator !== "undefined";
var nav = getNavigatorData();
var platform = getPlatform();
var userAgent = getUserAgent();
var isWebKit2 = typeof CSS === "undefined" || !CSS.supports ? false : CSS.supports("-webkit-backdrop-filter:none");
var isIOS = (
  // iPads can claim to be MacIntel
  nav.platform === "MacIntel" && nav.maxTouchPoints > 1 ? true : /iP(hone|ad|od)|iOS/.test(nav.platform)
);
var isFirefox = hasNavigator && /firefox/i.test(userAgent);
var isSafari = hasNavigator && /apple/i.test(navigator.vendor);
var isEdge = hasNavigator && /Edg/i.test(userAgent);
var isAndroid = hasNavigator && /android/i.test(platform) || /android/i.test(userAgent);
var isMac = hasNavigator && platform.toLowerCase().startsWith("mac") && !navigator.maxTouchPoints;
var isJSDOM = userAgent.includes("jsdom/");
function getNavigatorData() {
  if (!hasNavigator) {
    return {
      platform: "",
      maxTouchPoints: -1
    };
  }
  const uaData = navigator.userAgentData;
  if (uaData?.platform) {
    return {
      platform: uaData.platform,
      maxTouchPoints: navigator.maxTouchPoints
    };
  }
  return {
    platform: navigator.platform ?? "",
    maxTouchPoints: navigator.maxTouchPoints ?? -1
  };
}
function getUserAgent() {
  if (!hasNavigator) {
    return "";
  }
  const uaData = navigator.userAgentData;
  if (uaData && Array.isArray(uaData.brands)) {
    return uaData.brands.map(({
      brand,
      version: version2
    }) => `${brand}/${version2}`).join(" ");
  }
  return navigator.userAgent;
}
function getPlatform() {
  if (!hasNavigator) {
    return "";
  }
  const uaData = navigator.userAgentData;
  if (uaData?.platform) {
    return uaData.platform;
  }
  return navigator.platform ?? "";
}

// node_modules/@base-ui/react/esm/floating-ui-react/utils/constants.js
var FOCUSABLE_ATTRIBUTE = "data-base-ui-focusable";
var ACTIVE_KEY = "active";
var SELECTED_KEY = "selected";
var TYPEABLE_SELECTOR = "input:not([type='hidden']):not([disabled]),[contenteditable]:not([contenteditable='false']),textarea:not([disabled])";

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

// node_modules/@base-ui/react/esm/floating-ui-react/utils/element.js
function isTargetInsideEnabledTrigger(target, triggerElements) {
  if (!isElement(target)) {
    return false;
  }
  const targetElement = target;
  if (triggerElements.hasElement(targetElement)) {
    return !targetElement.hasAttribute("data-trigger-disabled");
  }
  for (const [, trigger] of triggerElements.entries()) {
    if (contains(trigger, targetElement)) {
      return !trigger.hasAttribute("data-trigger-disabled");
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
  if (!element || isJSDOM) {
    return true;
  }
  try {
    return element.matches(":focus-visible");
  } catch (_e) {
    return true;
  }
}

// node_modules/@base-ui/react/esm/floating-ui-react/utils/nodes.js
function getNodeChildren(nodes, id, onlyOpenChildren = true) {
  const directChildren = nodes.filter((node) => node.parentId === id);
  return directChildren.flatMap((child) => [...!onlyOpenChildren || child.context?.open ? [child] : [], ...getNodeChildren(nodes, child.id, onlyOpenChildren)]);
}

// node_modules/@base-ui/react/esm/floating-ui-react/utils/event.js
function isReactEvent(event) {
  return "nativeEvent" in event;
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

// node_modules/@floating-ui/utils/dist/floating-ui.utils.mjs
var sides = ["top", "right", "bottom", "left"];
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
  return {
    top: 0,
    right: 0,
    bottom: 0,
    left: 0,
    ...padding
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

// node_modules/@base-ui/react/esm/floating-ui-react/utils/composite.js
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

// node_modules/@base-ui/react/esm/floating-ui-react/utils/tabbable.js
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

// node_modules/@base-ui/utils/esm/addEventListener.js
function addEventListener(target, type, listener, options) {
  target.addEventListener(type, listener, options);
  return () => {
    target.removeEventListener(type, listener, options);
  };
}

// node_modules/@base-ui/react/esm/internals/useOpenChangeComplete.js
var React16 = __toESM(require_react(), 1);
function useOpenChangeComplete(parameters) {
  const {
    enabled = true,
    open,
    ref,
    onComplete: onCompleteParam
  } = parameters;
  const onComplete = useStableCallback(onCompleteParam);
  const runOnceAnimationsFinish = useAnimationsFinished(ref, open, false);
  React16.useEffect(() => {
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

// node_modules/@base-ui/utils/esm/useOnFirstRender.js
var React17 = __toESM(require_react(), 1);
function useOnFirstRender(fn) {
  const ref = React17.useRef(true);
  if (ref.current) {
    ref.current = false;
    fn();
  }
}

// node_modules/@base-ui/utils/esm/useTimeout.js
var EMPTY3 = 0;
var Timeout = class _Timeout {
  static create() {
    return new _Timeout();
  }
  currentId = EMPTY3;
  /**
   * Executes `fn` after `delay`, clearing any previously scheduled call.
   */
  start(delay, fn) {
    this.clear();
    this.currentId = setTimeout(() => {
      this.currentId = EMPTY3;
      fn();
    }, delay);
  }
  isStarted() {
    return this.currentId !== EMPTY3;
  }
  clear = () => {
    if (this.currentId !== EMPTY3) {
      clearTimeout(this.currentId);
      this.currentId = EMPTY3;
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

// node_modules/@base-ui/react/esm/floating-ui-react/components/FloatingDelayGroup.js
var React18 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/esm/floating-ui-react/hooks/useHoverShared.js
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

// node_modules/@base-ui/react/esm/floating-ui-react/components/FloatingDelayGroup.js
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
  const store2 = "rootStore" in context ? context.rootStore : context;
  const floatingId = store2.useState("floatingId");
  const {
    open
  } = options;
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
  useIsoLayoutEffect(() => {
    function unset() {
      setIsInstantPhase(false);
      currentContextRef.current?.setIsInstantPhase(false);
      currentIdRef.current = null;
      currentContextRef.current = null;
      delayRef.current = initialDelayRef.current;
    }
    if (!currentIdRef.current) {
      return void 0;
    }
    if (!open && currentIdRef.current === floatingId) {
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
          timeout.clear();
        };
      }
      unset();
    }
    return void 0;
  }, [open, floatingId, currentIdRef, delayRef, timeoutMs, initialDelayRef, currentContextRef, timeout, store2]);
  useIsoLayoutEffect(() => {
    if (!open) {
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
  }, [open, floatingId, store2, currentIdRef, delayRef, timeoutMs, initialDelayRef, currentContextRef, timeout]);
  useIsoLayoutEffect(() => {
    return () => {
      currentContextRef.current = null;
    };
  }, [currentContextRef]);
  return React18.useMemo(() => ({
    hasProvider,
    delayRef,
    isInstantPhase
  }), [hasProvider, delayRef, isInstantPhase]);
}

// node_modules/@base-ui/utils/esm/mergeCleanups.js
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

// node_modules/@base-ui/utils/esm/useValueAsRef.js
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

// node_modules/@base-ui/react/esm/utils/FocusGuard.js
var React19 = __toESM(require_react(), 1);

// node_modules/@base-ui/utils/esm/visuallyHidden.js
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

// node_modules/@base-ui/react/esm/utils/FocusGuard.js
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
var FocusGuard = /* @__PURE__ */ React19.forwardRef(function FocusGuard2(props, ref) {
  const [role, setRole] = React19.useState();
  useIsoLayoutEffect(() => {
    if (isSafari) {
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

// node_modules/@base-ui/react/esm/floating-ui-react/utils/createAttribute.js
function createAttribute(name) {
  return `data-base-ui-${name}`;
}

// node_modules/@base-ui/react/esm/floating-ui-react/components/FloatingPortal.js
var React20 = __toESM(require_react(), 1);
var ReactDOM2 = __toESM(require_react_dom(), 1);

// node_modules/@base-ui/react/esm/internals/constants.js
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

// node_modules/@base-ui/react/esm/floating-ui-react/components/FloatingPortal.js
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
    if (uniqueId == null) {
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
  }, [containerProp, parentPortalNode, uniqueId]);
  const portalElement = useRenderElement("div", componentProps, {
    ref: [ref, setPortalNodeRef],
    props: [{
      id: uniqueId,
      [attr]: ""
    }, elementProps]
  });
  const portalSubtree = containerElement && portalElement ? /* @__PURE__ */ ReactDOM2.createPortal(portalElement, containerElement) : null;
  return {
    portalNode,
    portalSubtree
  };
}
var FloatingPortal = /* @__PURE__ */ React20.forwardRef(function FloatingPortal2(componentProps, forwardedRef) {
  const {
    children,
    container,
    className,
    render,
    renderGuards,
    style,
    ...elementProps
  } = componentProps;
  const {
    portalNode,
    portalSubtree
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
  const open = focusManagerState?.open;
  const shouldRenderGuards = typeof renderGuards === "boolean" ? renderGuards : !!focusManagerState && !focusManagerState.modal && focusManagerState.open && !!portalNode;
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
  React20.useEffect(() => {
    if (!portalNode || open !== false) {
      return;
    }
    enableFocusInside(portalNode);
    focusInsideDisabledRef.current = false;
  }, [open, portalNode]);
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
        "aria-owns": portalNode.id,
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

// node_modules/@base-ui/react/esm/floating-ui-react/components/FloatingTree.js
var React21 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/esm/floating-ui-react/utils/createEventEmitter.js
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

// node_modules/@base-ui/react/esm/floating-ui-react/components/FloatingTree.js
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

// node_modules/@base-ui/react/esm/floating-ui-react/hooks/useClientPoint.js
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
  const store2 = "rootStore" in context ? context.rootStore : context;
  const open = store2.useState("open");
  const floating = store2.useState("floatingElement");
  const domReference = store2.useState("domReferenceElement");
  const dataRef = store2.context.dataRef;
  const {
    enabled = true,
    axis = "both"
  } = props;
  const initialRef = React22.useRef(false);
  const cleanupListenerRef = React22.useRef(null);
  const [pointerType, setPointerType] = React22.useState();
  const [reactive, setReactive] = React22.useState([]);
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
    if (!open) {
      setReference(event.clientX, event.clientY, event.currentTarget);
    } else if (!cleanupListenerRef.current) {
      setReactive([]);
    }
  });
  const openCheck = isMouseLikePointerType(pointerType) ? floating : open;
  const addListener = React22.useCallback(() => {
    if (!openCheck || !enabled) {
      return void 0;
    }
    const win = getWindow(floating);
    function handleMouseMove(event) {
      const target = getTarget(event);
      if (!contains(floating, target)) {
        setReference(event.clientX, event.clientY);
      } else {
        cleanupListenerRef.current?.();
        cleanupListenerRef.current = null;
      }
    }
    if (!dataRef.current.openEvent || isMouseBasedEvent(dataRef.current.openEvent)) {
      const cleanup = () => {
        cleanupListenerRef.current?.();
        cleanupListenerRef.current = null;
      };
      cleanupListenerRef.current = addEventListener(win, "mousemove", handleMouseMove);
      return cleanup;
    }
    store2.set("positionReference", domReference);
    return void 0;
  }, [openCheck, enabled, floating, dataRef, domReference, store2, setReference]);
  React22.useEffect(() => {
    return addListener();
  }, [addListener, reactive]);
  React22.useEffect(() => {
    if (enabled && !floating) {
      initialRef.current = false;
    }
  }, [enabled, floating]);
  React22.useEffect(() => {
    if (!enabled && open) {
      initialRef.current = true;
    }
  }, [enabled, open]);
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

// node_modules/@base-ui/react/esm/floating-ui-react/hooks/useDismiss.js
var React23 = __toESM(require_react(), 1);
var bubbleHandlerKeys = {
  intentional: "onClick",
  sloppy: "onPointerDown"
};
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
  const store2 = "rootStore" in context ? context.rootStore : context;
  const open = store2.useState("open");
  const floatingElement = store2.useState("floatingElement");
  const {
    dataRef
  } = store2.context;
  const {
    enabled = true,
    escapeKey: escapeKey2 = true,
    outsidePress: outsidePressProp = true,
    outsidePressEvent = "sloppy",
    referencePress = alwaysFalse,
    referencePressEvent = "sloppy",
    bubbles,
    externalTree
  } = props;
  const tree = useFloatingTree(externalTree);
  const outsidePressFn = useStableCallback(typeof outsidePressProp === "function" ? outsidePressProp : () => false);
  const outsidePress2 = typeof outsidePressProp === "function" ? outsidePressFn : outsidePressProp;
  const outsidePressEnabled = outsidePress2 !== false;
  const getOutsidePressEventProp = useStableCallback(() => outsidePressEvent);
  const pressStartedInsideRef = React23.useRef(false);
  const pressStartPreventedRef = React23.useRef(false);
  const suppressNextOutsideClickRef = React23.useRef(false);
  const {
    escapeKey: escapeKeyBubbles,
    outsidePress: outsidePressBubbles
  } = normalizeProp(bubbles);
  const touchStateRef = React23.useRef(null);
  const cancelDismissOnEndTimeout = useTimeout();
  const clearInsideReactTreeTimeout = useTimeout();
  const clearInsideReactTree = useStableCallback(() => {
    clearInsideReactTreeTimeout.clear();
    dataRef.current.insideReactTree = false;
  });
  const isComposingRef = React23.useRef(false);
  const currentPointerTypeRef = React23.useRef("");
  const isReferencePressEnabled = useStableCallback(referencePress);
  const closeOnEscapeKeyDown = useStableCallback((event) => {
    if (!open || !enabled || !escapeKey2 || event.key !== "Escape") {
      return;
    }
    if (isComposingRef.current) {
      return;
    }
    const nodeId = dataRef.current.floatingContext?.nodeId;
    const children = tree ? getNodeChildren(tree.nodesRef.current, nodeId) : [];
    if (!escapeKeyBubbles) {
      if (children.length > 0) {
        let shouldDismiss = true;
        children.forEach((child) => {
          if (child.context?.open && !child.context.dataRef.current.__escapeKeyBubbles) {
            shouldDismiss = false;
          }
        });
        if (!shouldDismiss) {
          return;
        }
      }
    }
    const native = isReactEvent(event) ? event.nativeEvent : event;
    const eventDetails = createChangeEventDetails(reason_parts_exports.escapeKey, native);
    store2.setOpen(false, eventDetails);
    if (!escapeKeyBubbles && !eventDetails.isPropagationAllowed) {
      event.stopPropagation();
    }
  });
  const markInsideReactTree = useStableCallback(() => {
    dataRef.current.insideReactTree = true;
    clearInsideReactTreeTimeout.start(0, clearInsideReactTree);
  });
  React23.useEffect(() => {
    if (!open || !enabled) {
      return void 0;
    }
    dataRef.current.__escapeKeyBubbles = escapeKeyBubbles;
    dataRef.current.__outsidePressBubbles = outsidePressBubbles;
    const compositionTimeout = new Timeout();
    const preventedPressSuppressionTimeout = new Timeout();
    function handleCompositionStart() {
      compositionTimeout.clear();
      isComposingRef.current = true;
    }
    function handleCompositionEnd() {
      compositionTimeout.start(
        // 0ms or 1ms don't work in Safari. 5ms appears to consistently work.
        // Only apply to WebKit for the test to remain 0ms.
        isWebKit() ? 5 : 0,
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
      return isEventTargetWithin(event, store2.select("floatingElement")) || isEventTargetWithin(event, store2.select("domReferenceElement")) || targetIsInsideChildren;
    }
    function closeOnPressOutside(event) {
      if (shouldIgnoreEvent(event)) {
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
        const isRTL2 = style.direction === "rtl";
        const pressedVerticalScrollbar = canScrollY && (isRTL2 ? event.offsetX <= target.offsetWidth - target.clientWidth : event.offsetX > target.clientWidth);
        const pressedHorizontalScrollbar = canScrollX && event.offsetY > target.clientHeight;
        if (pressedVerticalScrollbar || pressedHorizontalScrollbar) {
          return;
        }
      }
      if (isEventWithinFloatingTree(event)) {
        return;
      }
      if (getOutsidePressEvent() === "intentional" && suppressNextOutsideClickRef.current) {
        preventedPressSuppressionTimeout.clear();
        suppressNextOutsideClickRef.current = false;
        return;
      }
      if (typeof outsidePress2 === "function" && !outsidePress2(event)) {
        return;
      }
      const nodeId = dataRef.current.floatingContext?.nodeId;
      const children = tree ? getNodeChildren(tree.nodesRef.current, nodeId) : [];
      if (children.length > 0) {
        let shouldDismiss = true;
        children.forEach((child) => {
          if (child.context?.open && !child.context.dataRef.current.__outsidePressBubbles) {
            shouldDismiss = false;
          }
        });
        if (!shouldDismiss) {
          return;
        }
      }
      store2.setOpen(false, createChangeEventDetails(reason_parts_exports.outsidePress, event));
      clearInsideReactTree();
    }
    function handlePointerDown(event) {
      if (getOutsidePressEvent() !== "sloppy" || event.pointerType === "touch" || !store2.select("open") || !enabled || isEventTargetWithin(event, store2.select("floatingElement")) || isEventTargetWithin(event, store2.select("domReferenceElement"))) {
        return;
      }
      closeOnPressOutside(event);
    }
    function handleTouchStart(event) {
      if (getOutsidePressEvent() !== "sloppy" || !store2.select("open") || !enabled || isEventTargetWithin(event, store2.select("floatingElement")) || isEventTargetWithin(event, store2.select("domReferenceElement"))) {
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
      if (getOutsidePressEvent() !== "sloppy" || !touchStateRef.current || isEventTargetWithin(event, store2.select("floatingElement")) || isEventTargetWithin(event, store2.select("domReferenceElement"))) {
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
      if (getOutsidePressEvent() !== "sloppy" || !touchStateRef.current || isEventTargetWithin(event, store2.select("floatingElement")) || isEventTargetWithin(event, store2.select("domReferenceElement"))) {
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
    const doc = ownerDocument(floatingElement);
    const unsubscribe = mergeCleanups(escapeKey2 && mergeCleanups(addEventListener(doc, "keydown", closeOnEscapeKeyDown), addEventListener(doc, "compositionstart", handleCompositionStart), addEventListener(doc, "compositionend", handleCompositionEnd)), outsidePressEnabled && mergeCleanups(addEventListener(doc, "click", closeOnPressOutsideCapture, true), addEventListener(doc, "pointerdown", closeOnPressOutsideCapture, true), addEventListener(doc, "pointerup", handlePressEndCapture, true), addEventListener(doc, "pointercancel", handlePressEndCapture, true), addEventListener(doc, "mousedown", closeOnPressOutsideCapture, true), addEventListener(doc, "mouseup", handlePressEndCapture, true), addEventListener(doc, "touchstart", handleTouchStartCapture, true), addEventListener(doc, "touchmove", handleTouchMoveCapture, true), addEventListener(doc, "touchend", handleTouchEndCapture, true)));
    return () => {
      unsubscribe();
      compositionTimeout.clear();
      preventedPressSuppressionTimeout.clear();
      resetPressStartState();
      suppressNextOutsideClickRef.current = false;
    };
  }, [dataRef, floatingElement, escapeKey2, outsidePressEnabled, outsidePress2, open, enabled, escapeKeyBubbles, outsidePressBubbles, closeOnEscapeKeyDown, clearInsideReactTree, getOutsidePressEventProp, tree, store2, cancelDismissOnEndTimeout]);
  React23.useEffect(clearInsideReactTree, [outsidePress2, clearInsideReactTree]);
  const reference = React23.useMemo(() => ({
    onKeyDown: closeOnEscapeKeyDown,
    [bubbleHandlerKeys[referencePressEvent]]: (event) => {
      if (!isReferencePressEnabled()) {
        return;
      }
      store2.setOpen(false, createChangeEventDetails(reason_parts_exports.triggerPress, event.nativeEvent));
    },
    ...referencePressEvent !== "intentional" && {
      onClick(event) {
        if (!isReferencePressEnabled()) {
          return;
        }
        store2.setOpen(false, createChangeEventDetails(reason_parts_exports.triggerPress, event.nativeEvent));
      }
    }
  }), [closeOnEscapeKeyDown, store2, referencePressEvent, isReferencePressEnabled]);
  const markPressStartedInsideReactTree = useStableCallback((event) => {
    if (!open || !enabled || event.button !== 0) {
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
    if (!open || !enabled) {
      return;
    }
    if (!(event.defaultPrevented || event.nativeEvent.defaultPrevented)) {
      return;
    }
    if (pressStartedInsideRef.current) {
      pressStartPreventedRef.current = true;
    }
  });
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

// node_modules/@base-ui/react/esm/floating-ui-react/hooks/useFloating.js
var React29 = __toESM(require_react(), 1);

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
  switch (getAlignment(placement)) {
    case "start":
      coords[alignmentAxis] -= commonAlign * (rtl && isVertical ? -1 : 1);
      break;
    case "end":
      coords[alignmentAxis] += commonAlign * (rtl && isVertical ? -1 : 1);
      break;
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
  const offsetScale = await (platform3.isElement == null ? void 0 : platform3.isElement(offsetParent)) ? await (platform3.getScale == null ? void 0 : platform3.getScale(offsetParent)) || {
    x: 1,
    y: 1
  } : {
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
function getSideOffsets(overflow, rect) {
  return {
    top: overflow.top - rect.height,
    right: overflow.right - rect.width,
    bottom: overflow.bottom - rect.height,
    left: overflow.left - rect.width
  };
}
function isAnySideFullyClipped(overflow) {
  return sides.some((side) => overflow[side] >= 0);
}
var hide = function(options) {
  if (options === void 0) {
    options = {};
  }
  return {
    name: "hide",
    options,
    async fn(state) {
      const {
        rects,
        platform: platform3
      } = state;
      const {
        strategy = "referenceHidden",
        ...detectOverflowOptions
      } = evaluate(options, state);
      switch (strategy) {
        case "referenceHidden": {
          const overflow = await platform3.detectOverflow(state, {
            ...detectOverflowOptions,
            elementContext: "reference"
          });
          const offsets = getSideOffsets(overflow, rects.reference);
          return {
            data: {
              referenceHiddenOffsets: offsets,
              referenceHidden: isAnySideFullyClipped(offsets)
            }
          };
        }
        case "escaped": {
          const overflow = await platform3.detectOverflow(state, {
            ...detectOverflowOptions,
            altBoundary: true
          });
          const offsets = getSideOffsets(overflow, rects.floating);
          return {
            data: {
              escapedOffsets: offsets,
              escaped: isAnySideFullyClipped(offsets)
            }
          };
        }
        default: {
          return {};
        }
      }
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
      const crossAxis = getSideAxis(getSide(placement));
      const mainAxis = getOppositeAxis(crossAxis);
      let mainAxisCoord = coords[mainAxis];
      let crossAxisCoord = coords[crossAxis];
      if (checkMainAxis) {
        const minSide = mainAxis === "y" ? "top" : "left";
        const maxSide = mainAxis === "y" ? "bottom" : "right";
        const min2 = mainAxisCoord + overflow[minSide];
        const max2 = mainAxisCoord - overflow[maxSide];
        mainAxisCoord = clamp(min2, mainAxisCoord, max2);
      }
      if (checkCrossAxis) {
        const minSide = crossAxis === "y" ? "top" : "left";
        const maxSide = crossAxis === "y" ? "bottom" : "right";
        const min2 = crossAxisCoord + overflow[minSide];
        const max2 = crossAxisCoord - overflow[maxSide];
        crossAxisCoord = clamp(min2, crossAxisCoord, max2);
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
        mainAxis: 0,
        crossAxis: 0,
        ...rawOffset
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
      var _state$middlewareData, _state$middlewareData2;
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
      const noShift = !state.middlewareData.shift;
      let availableHeight = overflowAvailableHeight;
      let availableWidth = overflowAvailableWidth;
      if ((_state$middlewareData = state.middlewareData.shift) != null && _state$middlewareData.enabled.x) {
        availableWidth = maximumClippingWidth;
      }
      if ((_state$middlewareData2 = state.middlewareData.shift) != null && _state$middlewareData2.enabled.y) {
        availableHeight = maximumClippingHeight;
      }
      if (noShift && !alignment) {
        const xMin = max(overflow.left, 0);
        const xMax = max(overflow.right, 0);
        const yMin = max(overflow.top, 0);
        const yMax = max(overflow.bottom, 0);
        if (isYAxis) {
          availableWidth = width - 2 * (xMin !== 0 || xMax !== 0 ? xMin + xMax : max(overflow.left, overflow.right));
        } else {
          availableHeight = height - 2 * (yMin !== 0 || yMax !== 0 ? yMin + yMax : max(overflow.top, overflow.bottom));
        }
      }
      await apply({
        ...state,
        availableWidth,
        availableHeight
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
  if (!floatingOffsetParent || isFixed && floatingOffsetParent !== getWindow(element)) {
    return false;
  }
  return isFixed;
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
  if (domElement) {
    const win = getWindow(domElement);
    const offsetWin = offsetParent && isElement(offsetParent) ? getWindow(offsetParent) : offsetParent;
    let currentWin = win;
    let currentIFrame = getFrameElement(currentWin);
    while (currentIFrame && offsetParent && offsetWin !== currentWin) {
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
  if (isOffsetParentAnElement || !isOffsetParentAnElement && !isFixed) {
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
  return Array.from(element.getClientRects());
}
function getDocumentRect(element) {
  const html = getDocumentElement(element);
  const scroll = getNodeScroll(element);
  const body = element.ownerDocument.body;
  const width = max(html.scrollWidth, html.clientWidth, body.scrollWidth, body.clientWidth);
  const height = max(html.scrollHeight, html.clientHeight, body.scrollHeight, body.clientHeight);
  let x = -scroll.scrollLeft + getWindowScrollBarX(element);
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
function getViewportRect(element, strategy) {
  const win = getWindow(element);
  const html = getDocumentElement(element);
  const visualViewport = win.visualViewport;
  let width = html.clientWidth;
  let height = html.clientHeight;
  let x = 0;
  let y = 0;
  if (visualViewport) {
    width = visualViewport.width;
    height = visualViewport.height;
    const visualViewportBased = isWebKit();
    if (!visualViewportBased || visualViewportBased && strategy === "fixed") {
      x = visualViewport.offsetLeft;
      y = visualViewport.offsetTop;
    }
  }
  const windowScrollbarX = getWindowScrollBarX(html);
  if (windowScrollbarX <= 0) {
    const doc = html.ownerDocument;
    const body = doc.body;
    const bodyStyles = getComputedStyle(body);
    const bodyMarginInline = doc.compatMode === "CSS1Compat" ? parseFloat(bodyStyles.marginLeft) + parseFloat(bodyStyles.marginRight) || 0 : 0;
    const clippingStableScrollbarWidth = Math.abs(html.clientWidth - body.clientWidth - bodyMarginInline);
    if (clippingStableScrollbarWidth <= SCROLLBAR_MAX) {
      width -= clippingStableScrollbarWidth;
    }
  } else if (windowScrollbarX <= SCROLLBAR_MAX) {
    width += windowScrollbarX;
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
  const scale = isHTMLElement(element) ? getScale(element) : createCoords(1);
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
  if (clippingAncestor === "viewport") {
    rect = getViewportRect(element, strategy);
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
function hasFixedPositionAncestor(element, stopNode) {
  const parentNode = getParentNode(element);
  if (parentNode === stopNode || !isElement(parentNode) || isLastTraversableNode(parentNode)) {
    return false;
  }
  return getComputedStyle2(parentNode).position === "fixed" || hasFixedPositionAncestor(parentNode, stopNode);
}
function getClippingElementAncestors(element, cache) {
  const cachedResult = cache.get(element);
  if (cachedResult) {
    return cachedResult;
  }
  let result = getOverflowAncestors(element, [], false).filter((el) => isElement(el) && getNodeName(el) !== "body");
  let currentContainingBlockComputedStyle = null;
  const elementIsFixed = getComputedStyle2(element).position === "fixed";
  let currentNode = elementIsFixed ? getParentNode(element) : element;
  while (isElement(currentNode) && !isLastTraversableNode(currentNode)) {
    const computedStyle = getComputedStyle2(currentNode);
    const currentNodeIsContaining = isContainingBlock(currentNode);
    if (!currentNodeIsContaining && computedStyle.position === "fixed") {
      currentContainingBlockComputedStyle = null;
    }
    const shouldDropCurrentNode = elementIsFixed ? !currentNodeIsContaining && !currentContainingBlockComputedStyle : !currentNodeIsContaining && computedStyle.position === "static" && !!currentContainingBlockComputedStyle && (currentContainingBlockComputedStyle.position === "absolute" || currentContainingBlockComputedStyle.position === "fixed") || isOverflowElement(currentNode) && !currentNodeIsContaining && hasFixedPositionAncestor(element, currentNode);
    if (shouldDropCurrentNode) {
      result = result.filter((ancestor) => ancestor !== currentNode);
    } else {
      currentContainingBlockComputedStyle = computedStyle;
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
  function setLeftRTLScrollbarOffset() {
    offsets.x = getWindowScrollBarX(documentElement);
  }
  if (isOffsetParentAnElement || !isOffsetParentAnElement && !isFixed) {
    if (getNodeName(offsetParent) !== "body" || isOverflowElement(documentElement)) {
      scroll = getNodeScroll(offsetParent);
    }
    if (isOffsetParentAnElement) {
      const offsetRect = getBoundingClientRect(offsetParent, true, isFixed, offsetParent);
      offsets.x = offsetRect.x + offsetParent.clientLeft;
      offsets.y = offsetRect.y + offsetParent.clientTop;
    } else if (documentElement) {
      setLeftRTLScrollbarOffset();
    }
  }
  if (isFixed && !isOffsetParentAnElement && documentElement) {
    setLeftRTLScrollbarOffset();
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
function observeMove(element, onMove) {
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
      if (ratio === 1 && !rectsAreEqual(elementRectForRootMargin, element.getBoundingClientRect())) {
        refresh();
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
  refresh(true);
  return cleanup;
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
    ancestorScroll && ancestor.addEventListener("scroll", update2, {
      passive: true
    });
    ancestorResize && ancestor.addEventListener("resize", update2);
  });
  const cleanupIo = referenceEl && layoutShift ? observeMove(referenceEl, update2) : null;
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
var hide2 = hide;
var limitShift2 = limitShift;
var computePosition2 = (reference, floating, options) => {
  const cache = /* @__PURE__ */ new Map();
  const mergedOptions = {
    platform: platform2,
    ...options
  };
  const platformWithCache = {
    ...mergedOptions.platform,
    _c: cache
  };
  return computePosition(reference, floating, {
    ...mergedOptions,
    platform: platformWithCache
  });
};

// node_modules/@base-ui/react/node_modules/@floating-ui/react-dom/dist/floating-ui.react-dom.mjs
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
    open
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
  const openRef = useLatestRef(open);
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
    if (open === false && dataRef.current.isPositioned) {
      dataRef.current.isPositioned = false;
      setData((data2) => ({
        ...data2,
        isPositioned: false
      }));
    }
  }, [open]);
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
var hide3 = (options, deps) => {
  const result = hide2(options);
  return {
    name: result.name,
    fn: result.fn,
    options: [options, deps]
  };
};

// node_modules/@base-ui/utils/esm/store/createSelector.js
var createSelector = (a, b, c, d, e, f, ...other) => {
  if (other.length > 0) {
    throw new Error(true ? "Unsupported number of selectors" : formatErrorMessage_default(1));
  }
  let selector;
  if (a && b && c && d && e && f) {
    selector = (state, a1, a2, a3) => {
      const va = a(state, a1, a2, a3);
      const vb = b(state, a1, a2, a3);
      const vc = c(state, a1, a2, a3);
      const vd = d(state, a1, a2, a3);
      const ve = e(state, a1, a2, a3);
      return f(va, vb, vc, vd, ve, a1, a2, a3);
    };
  } else if (a && b && c && d && e) {
    selector = (state, a1, a2, a3) => {
      const va = a(state, a1, a2, a3);
      const vb = b(state, a1, a2, a3);
      const vc = c(state, a1, a2, a3);
      const vd = d(state, a1, a2, a3);
      return e(va, vb, vc, vd, a1, a2, a3);
    };
  } else if (a && b && c && d) {
    selector = (state, a1, a2, a3) => {
      const va = a(state, a1, a2, a3);
      const vb = b(state, a1, a2, a3);
      const vc = c(state, a1, a2, a3);
      return d(va, vb, vc, a1, a2, a3);
    };
  } else if (a && b && c) {
    selector = (state, a1, a2, a3) => {
      const va = a(state, a1, a2, a3);
      const vb = b(state, a1, a2, a3);
      return c(va, vb, a1, a2, a3);
    };
  } else if (a && b) {
    selector = (state, a1, a2, a3) => {
      const va = a(state, a1, a2, a3);
      return b(va, a1, a2, a3);
    };
  } else if (a) {
    selector = a;
  } else {
    throw (
      /* minify-error-disabled */
      new Error("Missing arguments")
    );
  }
  return selector;
};

// node_modules/@base-ui/utils/esm/store/useStore.js
var React26 = __toESM(require_react(), 1);
var import_shim = __toESM(require_shim(), 1);
var import_with_selector = __toESM(require_with_selector(), 1);

// node_modules/@base-ui/utils/esm/fastHooks.js
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

// node_modules/@base-ui/utils/esm/store/useStore.js
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
          if (hook.didChange || !Object.is(hook.value, value)) {
            didChange2 = true;
            hook.value = value;
            hook.didChange = false;
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
      value: selector(store2.getSnapshot(), a1, a2, a3),
      didChange: false
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
      hook.didChange = true;
    }
  }
  return hook.value;
}
function useStoreLegacy(store2, selector, a1, a2, a3) {
  return (0, import_with_selector.useSyncExternalStoreWithSelector)(store2.subscribe, store2.getSnapshot, store2.getSnapshot, (state) => selector(state, a1, a2, a3));
}

// node_modules/@base-ui/utils/esm/store/Store.js
var Store = class {
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

// node_modules/@base-ui/utils/esm/store/ReactStore.js
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
    useIsoLayoutEffect(() => {
      if (this.state[key] !== value) {
        this.set(key, value);
      }
    }, [key, value]);
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
   *
   * Note that the while the values in `state` are updated immediately, the values returned
   * by `useState` are updated before the next render (similarly to React's `useState`).
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
    const isControlled = controlled !== void 0;
    useIsoLayoutEffect(() => {
      if (isControlled && !Object.is(this.state[key], controlled)) {
        super.setState({
          ...this.state,
          [key]: controlled
        });
      }
    }, [key, controlled, isControlled]);
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

// node_modules/@base-ui/react/esm/floating-ui-react/components/FloatingRootStore.js
var selectors = {
  open: createSelector((state) => state.open),
  transitionStatus: createSelector((state) => state.transitionStatus),
  domReferenceElement: createSelector((state) => state.domReferenceElement),
  referenceElement: createSelector((state) => state.positionReference ?? state.referenceElement),
  floatingElement: createSelector((state) => state.floatingElement),
  floatingId: createSelector((state) => state.floatingId)
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

// node_modules/@base-ui/react/esm/utils/popups/popupStoreUtils.js
var React28 = __toESM(require_react(), 1);
function useTriggerRegistration(id, store2) {
  const registeredElementIdRef = React28.useRef(null);
  const registeredElementRef = React28.useRef(null);
  return React28.useCallback((element) => {
    if (id === void 0) {
      return;
    }
    if (registeredElementIdRef.current !== null) {
      const registeredId = registeredElementIdRef.current;
      const registeredElement = registeredElementRef.current;
      const currentElement = store2.context.triggerElements.getById(registeredId);
      if (registeredElement && currentElement === registeredElement) {
        store2.context.triggerElements.delete(registeredId);
      }
      registeredElementIdRef.current = null;
      registeredElementRef.current = null;
    }
    if (element !== null) {
      registeredElementIdRef.current = id;
      registeredElementRef.current = element;
      store2.context.triggerElements.add(id, element);
    }
  }, [store2, id]);
}
function useTriggerDataForwarding(triggerId, triggerElementRef, store2, stateUpdates) {
  const isMountedByThisTrigger = store2.useState("isMountedByTrigger", triggerId);
  const baseRegisterTrigger = useTriggerRegistration(triggerId, store2);
  const registerTrigger = useStableCallback((element) => {
    baseRegisterTrigger(element);
    if (!element || !store2.select("open")) {
      return;
    }
    const activeTriggerId = store2.select("activeTriggerId");
    if (activeTriggerId === triggerId) {
      store2.update({
        activeTriggerElement: element,
        ...stateUpdates
      });
      return;
    }
    if (activeTriggerId == null) {
      store2.update({
        activeTriggerId: triggerId,
        activeTriggerElement: element,
        ...stateUpdates
      });
    }
  });
  useIsoLayoutEffect(() => {
    if (isMountedByThisTrigger) {
      store2.update({
        activeTriggerElement: triggerElementRef.current,
        ...stateUpdates
      });
    }
  }, [isMountedByThisTrigger, store2, triggerElementRef, ...Object.values(stateUpdates)]);
  return {
    registerTrigger,
    isMountedByThisTrigger
  };
}
function useImplicitActiveTrigger(store2) {
  const open = store2.useState("open");
  useIsoLayoutEffect(() => {
    if (open && !store2.select("activeTriggerId") && store2.context.triggerElements.size === 1) {
      const iteratorResult = store2.context.triggerElements.entries().next();
      if (!iteratorResult.done) {
        const [implicitTriggerId, implicitTriggerElement] = iteratorResult.value;
        store2.update({
          activeTriggerId: implicitTriggerId,
          activeTriggerElement: implicitTriggerElement
        });
      }
    }
  }, [open, store2]);
}
function useOpenStateTransitions(open, store2, onUnmount) {
  const {
    mounted,
    setMounted,
    transitionStatus
  } = useTransitionStatus(open);
  store2.useSyncedValues({
    mounted,
    transitionStatus
  });
  const forceUnmount = useStableCallback(() => {
    setMounted(false);
    store2.update({
      activeTriggerId: null,
      activeTriggerElement: null,
      mounted: false
    });
    onUnmount?.();
    store2.context.onOpenChangeComplete?.(false);
  });
  const preventUnmountingOnClose = store2.useState("preventUnmountingOnClose");
  useOpenChangeComplete({
    enabled: !preventUnmountingOnClose,
    open,
    ref: store2.context.popupRef,
    onComplete() {
      if (!open) {
        forceUnmount();
      }
    }
  });
  return {
    forceUnmount,
    transitionStatus
  };
}

// node_modules/@base-ui/react/esm/utils/popups/popupTriggerMap.js
var PopupTriggerMap = class {
  constructor() {
    this.elementsSet = /* @__PURE__ */ new Set();
    this.idMap = /* @__PURE__ */ new Map();
  }
  /**
   * Adds a trigger element with the given ID.
   *
   * Note: The provided element is assumed to not be registered under multiple IDs.
   */
  add(id, element) {
    const existingElement = this.idMap.get(id);
    if (existingElement === element) {
      return;
    }
    if (existingElement !== void 0) {
      this.elementsSet.delete(existingElement);
    }
    this.elementsSet.add(element);
    this.idMap.set(id, element);
    if (true) {
      if (this.elementsSet.size !== this.idMap.size) {
        throw new Error("Base UI: A trigger element cannot be registered under multiple IDs in PopupTriggerMap.");
      }
    }
  }
  /**
   * Removes the trigger element with the given ID.
   */
  delete(id) {
    const element = this.idMap.get(id);
    if (element) {
      this.elementsSet.delete(element);
      this.idMap.delete(id);
    }
  }
  /**
   * Whether the given element is registered as a trigger.
   */
  hasElement(element) {
    return this.elementsSet.has(element);
  }
  /**
   * Whether there is a registered trigger element matching the given predicate.
   */
  hasMatchingElement(predicate) {
    for (const element of this.elementsSet) {
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
    return this.elementsSet.values();
  }
  /**
   * Returns the number of registered trigger elements.
   */
  get size() {
    return this.idMap.size;
  }
};

// node_modules/@base-ui/react/esm/floating-ui-react/utils/getEmptyRootContext.js
function getEmptyRootContext() {
  return new FloatingRootStore({
    open: false,
    transitionStatus: void 0,
    floatingElement: null,
    referenceElement: null,
    triggerElements: new PopupTriggerMap(),
    floatingId: "",
    syncOnly: false,
    nested: false,
    onOpenChange: void 0
  });
}

// node_modules/@base-ui/react/esm/utils/popups/store.js
function createInitialPopupStoreState() {
  return {
    open: false,
    openProp: void 0,
    mounted: false,
    transitionStatus: void 0,
    floatingRootContext: getEmptyRootContext(),
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
var activeTriggerIdSelector = createSelector((state) => state.triggerIdProp ?? state.activeTriggerId);
var popupStoreSelectors = {
  open: createSelector((state) => state.openProp ?? state.open),
  mounted: createSelector((state) => state.mounted),
  transitionStatus: createSelector((state) => state.transitionStatus),
  floatingRootContext: createSelector((state) => state.floatingRootContext),
  preventUnmountingOnClose: createSelector((state) => state.preventUnmountingOnClose),
  payload: createSelector((state) => state.payload),
  activeTriggerId: activeTriggerIdSelector,
  activeTriggerElement: createSelector((state) => state.mounted ? state.activeTriggerElement : null),
  /**
   * Whether the trigger with the given ID was used to open the popup.
   */
  isTriggerActive: createSelector((state, triggerId) => triggerId !== void 0 && activeTriggerIdSelector(state) === triggerId),
  /**
   * Whether the popup is open and was activated by a trigger with the given ID.
   */
  isOpenedByTrigger: createSelector((state, triggerId) => triggerId !== void 0 && activeTriggerIdSelector(state) === triggerId && state.open),
  /**
   * Whether the popup is mounted and was activated by a trigger with the given ID.
   */
  isMountedByTrigger: createSelector((state, triggerId) => triggerId !== void 0 && activeTriggerIdSelector(state) === triggerId && state.mounted),
  triggerProps: createSelector((state, isActive) => isActive ? state.activeTriggerProps : state.inactiveTriggerProps),
  popupProps: createSelector((state) => state.popupProps),
  popupElement: createSelector((state) => state.popupElement),
  positionerElement: createSelector((state) => state.positionerElement)
};

// node_modules/@base-ui/react/esm/floating-ui-react/hooks/useFloatingRootContext.js
function useFloatingRootContext(options) {
  const {
    open = false,
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
  const store2 = useRefWithInit(() => new FloatingRootStore({
    open,
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
      open,
      floatingId
    };
    if (elements.reference !== void 0) {
      valuesToSync.referenceElement = elements.reference;
      valuesToSync.domReferenceElement = isElement(elements.reference) ? elements.reference : null;
    }
    if (elements.floating !== void 0) {
      valuesToSync.floatingElement = elements.floating;
    }
    store2.update(valuesToSync);
  }, [open, floatingId, elements.reference, elements.floating, store2]);
  store2.context.onOpenChange = onOpenChange;
  store2.context.nested = nested;
  return store2;
}

// node_modules/@base-ui/react/esm/floating-ui-react/hooks/useFloating.js
function useFloating2(options = {}) {
  const {
    nodeId,
    externalTree
  } = options;
  const internalRootStore = useFloatingRootContext(options);
  const rootContext = options.rootContext || internalRootStore;
  const rootContextElements = {
    reference: rootContext.useState("referenceElement"),
    floating: rootContext.useState("floatingElement"),
    domReference: rootContext.useState("domReferenceElement")
  };
  const [positionReference, setPositionReferenceRaw] = React29.useState(null);
  const domReferenceRef = React29.useRef(null);
  const tree = useFloatingTree(externalTree);
  useIsoLayoutEffect(() => {
    if (rootContextElements.domReference) {
      domReferenceRef.current = rootContextElements.domReference;
    }
  }, [rootContextElements.domReference]);
  const position = useFloating({
    ...options,
    elements: {
      ...rootContextElements,
      ...positionReference && {
        reference: positionReference
      }
    }
  });
  const setPositionReference = React29.useCallback((node) => {
    const computedPositionReference = isElement(node) ? {
      getBoundingClientRect: () => node.getBoundingClientRect(),
      getClientRects: () => node.getClientRects(),
      contextElement: node
    } : node;
    setPositionReferenceRaw(computedPositionReference);
    position.refs.setReference(computedPositionReference);
  }, [position.refs]);
  const [localDomReference, setLocalDomReference] = React29.useState(void 0);
  const [localFloatingElement, setLocalFloatingElement] = React29.useState(null);
  rootContext.useSyncedValue("referenceElement", localDomReference ?? null);
  const localDomReferenceElement = isElement(localDomReference) ? localDomReference : null;
  rootContext.useSyncedValue("domReferenceElement", localDomReference === void 0 ? rootContextElements.domReference : localDomReferenceElement);
  rootContext.useSyncedValue("floatingElement", localFloatingElement);
  const setReference = React29.useCallback((node) => {
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
  const setFloating = React29.useCallback((node) => {
    setLocalFloatingElement(node);
    position.refs.setFloating(node);
  }, [position.refs]);
  const refs = React29.useMemo(() => ({
    ...position.refs,
    setReference,
    setFloating,
    setPositionReference,
    domReference: domReferenceRef
  }), [position.refs, setReference, setFloating, setPositionReference]);
  const elements = React29.useMemo(() => ({
    ...position.elements,
    domReference: rootContextElements.domReference
  }), [position.elements, rootContextElements.domReference]);
  const open = rootContext.useState("open");
  const floatingId = rootContext.useState("floatingId");
  const context = React29.useMemo(() => ({
    ...position,
    dataRef: rootContext.context.dataRef,
    open,
    onOpenChange: rootContext.setOpen,
    events: rootContext.context.events,
    floatingId,
    refs,
    elements,
    nodeId,
    rootStore: rootContext
  }), [position, refs, elements, nodeId, rootContext, open, floatingId]);
  useIsoLayoutEffect(() => {
    rootContext.context.dataRef.current.floatingContext = context;
    const node = tree?.nodesRef.current.find((n) => n.id === nodeId);
    if (node) {
      node.context = context;
    }
  });
  return React29.useMemo(() => ({
    ...position,
    context,
    refs,
    elements,
    rootStore: rootContext
  }), [position, refs, elements, context, rootContext]);
}

// node_modules/@base-ui/react/esm/floating-ui-react/hooks/useSyncedFloatingRootContext.js
function useSyncedFloatingRootContext(options) {
  const {
    popupStore,
    treatPopupAsFloatingElement = false,
    onOpenChange
  } = options;
  const floatingId = useId();
  const nested = useFloatingParentNodeId() != null;
  const open = popupStore.useState("open");
  const referenceElement = popupStore.useState("activeTriggerElement");
  const floatingElement = popupStore.useState(treatPopupAsFloatingElement ? "popupElement" : "positionerElement");
  const triggerElements = popupStore.context.triggerElements;
  const store2 = useRefWithInit(() => new FloatingRootStore({
    open,
    transitionStatus: void 0,
    referenceElement,
    floatingElement,
    triggerElements,
    onOpenChange,
    floatingId,
    syncOnly: true,
    nested
  })).current;
  useIsoLayoutEffect(() => {
    const valuesToSync = {
      open,
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
  }, [open, floatingId, referenceElement, floatingElement, store2]);
  store2.context.onOpenChange = onOpenChange;
  store2.context.nested = nested;
  return store2;
}

// node_modules/@base-ui/react/esm/floating-ui-react/hooks/useFocus.js
var React30 = __toESM(require_react(), 1);
var isMacSafari = isMac && isSafari;
function useFocus(context, props = {}) {
  const store2 = "rootStore" in context ? context.rootStore : context;
  const {
    events,
    dataRef
  } = store2.context;
  const {
    enabled = true,
    delay
  } = props;
  const blockFocusRef = React30.useRef(false);
  const blockedReferenceRef = React30.useRef(null);
  const timeout = useTimeout();
  const keyboardModalityRef = React30.useRef(true);
  React30.useEffect(() => {
    const domReference = store2.select("domReferenceElement");
    if (!enabled) {
      return void 0;
    }
    const win = getWindow(domReference);
    function onBlur() {
      const currentDomReference = store2.select("domReferenceElement");
      if (!store2.select("open") && isHTMLElement(currentDomReference) && currentDomReference === activeElement(ownerDocument(currentDomReference))) {
        blockFocusRef.current = true;
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
  React30.useEffect(() => {
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
  const reference = React30.useMemo(() => ({
    onMouseLeave() {
      blockFocusRef.current = false;
      blockedReferenceRef.current = null;
    },
    onFocus(event) {
      const focusTarget = event.currentTarget;
      if (blockFocusRef.current) {
        if (blockedReferenceRef.current === focusTarget) {
          return;
        }
        blockFocusRef.current = false;
        blockedReferenceRef.current = null;
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
      blockFocusRef.current = false;
      blockedReferenceRef.current = null;
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
  }), [dataRef, store2, timeout, delay]);
  return React30.useMemo(() => enabled ? {
    reference,
    trigger: reference
  } : {}, [enabled, reference]);
}

// node_modules/@base-ui/react/esm/floating-ui-react/hooks/useHoverFloatingInteraction.js
var React31 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/esm/floating-ui-react/hooks/useHoverInteractionSharedState.js
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
  const instance = useRefWithInit(HoverInteraction.create).current;
  const data = store2.context.dataRef.current;
  if (!data.hoverInteractionState) {
    data.hoverInteractionState = instance;
  }
  useOnMount(data.hoverInteractionState.disposeEffect);
  return data.hoverInteractionState;
}

// node_modules/@base-ui/react/esm/floating-ui-react/hooks/useHoverFloatingInteraction.js
function useHoverFloatingInteraction(context, parameters = {}) {
  const store2 = "rootStore" in context ? context.rootStore : context;
  const open = store2.useState("open");
  const floatingElement = store2.useState("floatingElement");
  const domReferenceElement = store2.useState("domReferenceElement");
  const {
    dataRef
  } = store2.context;
  const {
    enabled = true,
    closeDelay: closeDelayProp = 0,
    nodeId: nodeIdProp
  } = parameters;
  const instance = useHoverInteractionSharedState(store2);
  const tree = useFloatingTree();
  const parentId = useFloatingParentNodeId();
  const isClickLikeOpenEvent2 = useStableCallback(() => {
    return isClickLikeOpenEvent(dataRef.current.openEvent?.type, instance.interactedInside);
  });
  const isHoverOpen = useStableCallback(() => {
    const type = dataRef.current.openEvent?.type;
    return type?.includes("mouse") && type !== "mousedown";
  });
  const isRelatedTargetInsideEnabledTrigger = useStableCallback((target) => {
    return isTargetInsideEnabledTrigger(target, store2.context.triggerElements);
  });
  const closeWithDelay = React31.useCallback((event) => {
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
  }, [closeDelayProp, store2, instance, tree]);
  const clearPointerEvents = useStableCallback(() => {
    clearSafePolygonPointerEventsMutation(instance);
  });
  const handleInteractInside = useStableCallback((event) => {
    const target = getTarget(event);
    if (!isInteractiveElement(target)) {
      instance.interactedInside = false;
      return;
    }
    instance.interactedInside = target?.closest("[aria-haspopup]") != null;
  });
  useIsoLayoutEffect(() => {
    if (!open) {
      instance.pointerType = void 0;
      instance.restTimeoutPending = false;
      instance.interactedInside = false;
      clearPointerEvents();
    }
  }, [open, instance, clearPointerEvents]);
  React31.useEffect(() => {
    return clearPointerEvents;
  }, [clearPointerEvents]);
  useIsoLayoutEffect(() => {
    if (!enabled) {
      return void 0;
    }
    if (open && instance.handleCloseOptions?.blockPointerEvents && isHoverOpen() && isElement(domReferenceElement) && floatingElement) {
      const ref = domReferenceElement;
      const floatingEl = floatingElement;
      const doc = ownerDocument(floatingElement);
      const parentFloating = tree?.nodesRef.current.find((node) => node.id === parentId)?.context?.elements.floating;
      if (parentFloating) {
        parentFloating.style.pointerEvents = "";
      }
      const scopeElement = instance.handleCloseOptions?.getScope?.() ?? instance.pointerEventsScopeElement ?? parentFloating ?? ref.closest("[data-rootownerid]") ?? doc.body;
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
  }, [enabled, open, domReferenceElement, floatingElement, instance, isHoverOpen, tree, parentId, clearPointerEvents]);
  const childClosedTimeout = useTimeout();
  React31.useEffect(() => {
    if (!enabled) {
      return void 0;
    }
    function onFloatingMouseEnter() {
      instance.openChangeTimeout.clear();
      childClosedTimeout.clear();
      tree?.events.off("floating.closed", onNodeClosed);
      clearPointerEvents();
    }
    function onFloatingMouseLeave(event) {
      if (tree && parentId && getNodeChildren(tree.nodesRef.current, parentId).length > 0) {
        tree.events.on("floating.closed", onNodeClosed);
        return;
      }
      if (isRelatedTargetInsideEnabledTrigger(event.relatedTarget)) {
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
      if (!isClickLikeOpenEvent2()) {
        closeWithDelay(event);
      }
    }
    function onNodeClosed(event) {
      if (!tree || !parentId || getNodeChildren(tree.nodesRef.current, parentId).length > 0) {
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
  }, [enabled, floatingElement, store2, dataRef, nodeIdProp, isClickLikeOpenEvent2, isRelatedTargetInsideEnabledTrigger, closeWithDelay, clearPointerEvents, handleInteractInside, instance, tree, parentId, childClosedTimeout]);
}

// node_modules/@base-ui/react/esm/floating-ui-react/hooks/useHoverReferenceInteraction.js
var React32 = __toESM(require_react(), 1);
var ReactDOM4 = __toESM(require_react_dom(), 1);
var EMPTY_REF = {
  current: null
};
function useHoverReferenceInteraction(context, props = {}) {
  const store2 = "rootStore" in context ? context.rootStore : context;
  const {
    dataRef,
    events
  } = store2.context;
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
    isClosing
  } = props;
  const tree = useFloatingTree(externalTree);
  const instance = useHoverInteractionSharedState(store2);
  const isHoverCloseActiveRef = React32.useRef(false);
  const handleCloseRef = useValueAsRef(handleClose);
  const delayRef = useValueAsRef(delay);
  const restMsRef = useValueAsRef(restMs);
  const enabledRef = useValueAsRef(enabled);
  const isClosingRef = useValueAsRef(isClosing);
  if (isActiveTrigger) {
    instance.handleCloseOptions = handleCloseRef.current?.__options;
  }
  const isClickLikeOpenEvent2 = useStableCallback(() => {
    return isClickLikeOpenEvent(dataRef.current.openEvent?.type, instance.interactedInside);
  });
  const isRelatedTargetInsideEnabledTrigger = useStableCallback((target) => {
    return isTargetInsideEnabledTrigger(target, store2.context.triggerElements);
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
  const closeWithDelay = useStableCallback((event, runElseBranch = true) => {
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
  React32.useEffect(() => cleanupMouseMoveHandler, [cleanupMouseMoveHandler]);
  React32.useEffect(() => {
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
  React32.useEffect(() => {
    if (!enabled) {
      return void 0;
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
        store2.setOpen(true, createChangeEventDetails(reason_parts_exports.triggerHover, event, triggerNode));
        return;
      }
      if (isRestOnlyDelay) {
        return;
      }
      if (openDelay) {
        instance.openChangeTimeout.start(openDelay, () => {
          if (shouldOpen) {
            store2.setOpen(true, createChangeEventDetails(reason_parts_exports.triggerHover, event, triggerNode));
          }
        });
      } else if (shouldOpen) {
        store2.setOpen(true, createChangeEventDetails(reason_parts_exports.triggerHover, event, triggerNode));
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
      const ignoreRelatedTargetTrigger = isRelatedTargetInsideEnabledTrigger(event.relatedTarget);
      if (ignoreRelatedTargetTrigger) {
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
    if (move) {
      return mergeCleanups(addEventListener(trigger, "mousemove", onMouseEnter, {
        once: true
      }), addEventListener(trigger, "mouseenter", onMouseEnter), addEventListener(trigger, "mouseleave", onMouseLeave));
    }
    return mergeCleanups(addEventListener(trigger, "mouseenter", onMouseEnter), addEventListener(trigger, "mouseleave", onMouseLeave));
  }, [cleanupMouseMoveHandler, clearPointerEvents, dataRef, delayRef, closeWithDelay, store2, enabled, handleCloseRef, instance, isActiveTrigger, isOverInactiveTrigger, isClickLikeOpenEvent2, isRelatedTargetInsideEnabledTrigger, mouseOnly, move, restMsRef, triggerElementRef, tree, enabledRef, getHandleCloseContext, isClosingRef]);
  return React32.useMemo(() => {
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
          if (!instance.blockMouseMove && (!latestOpen || isOverInactive)) {
            store2.setOpen(true, createChangeEventDetails(reason_parts_exports.triggerHover, nativeEvent, trigger));
          }
        }
        if (instance.pointerType === "touch") {
          ReactDOM4.flushSync(() => {
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
  }, [enabled, instance, isClickLikeOpenEvent2, isOverInactiveTrigger, mouseOnly, store2, restMsRef]);
}

// node_modules/@base-ui/react/esm/floating-ui-react/hooks/useInteractions.js
var React33 = __toESM(require_react(), 1);
function useInteractions(propsList = []) {
  const referenceDeps = propsList.map((key) => key?.reference);
  const floatingDeps = propsList.map((key) => key?.floating);
  const itemDeps = propsList.map((key) => key?.item);
  const triggerDeps = propsList.map((key) => key?.trigger);
  const getReferenceProps = React33.useCallback(
    (userProps) => mergeProps2(userProps, propsList, "reference"),
    // eslint-disable-next-line react-hooks/exhaustive-deps
    referenceDeps
  );
  const getFloatingProps = React33.useCallback(
    (userProps) => mergeProps2(userProps, propsList, "floating"),
    // eslint-disable-next-line react-hooks/exhaustive-deps
    floatingDeps
  );
  const getItemProps = React33.useCallback(
    (userProps) => mergeProps2(userProps, propsList, "item"),
    // eslint-disable-next-line react-hooks/exhaustive-deps
    itemDeps
  );
  const getTriggerProps = React33.useCallback(
    (userProps) => mergeProps2(userProps, propsList, "trigger"),
    // eslint-disable-next-line react-hooks/exhaustive-deps
    triggerDeps
  );
  return React33.useMemo(() => ({
    getReferenceProps,
    getFloatingProps,
    getItemProps,
    getTriggerProps
  }), [getReferenceProps, getFloatingProps, getItemProps, getTriggerProps]);
}
function mergeProps2(userProps, propsList, elementKey) {
  const eventHandlers = /* @__PURE__ */ new Map();
  const isItem = elementKey === "item";
  const outputProps = {};
  if (elementKey === "floating") {
    outputProps.tabIndex = -1;
    outputProps[FOCUSABLE_ATTRIBUTE] = "";
  }
  for (const key in userProps) {
    if (isItem && userProps) {
      if (key === ACTIVE_KEY || key === SELECTED_KEY) {
        continue;
      }
    }
    outputProps[key] = userProps[key];
  }
  for (let i = 0; i < propsList.length; i += 1) {
    let props;
    const propsOrGetProps = propsList[i]?.[elementKey];
    if (typeof propsOrGetProps === "function") {
      props = userProps ? propsOrGetProps(userProps) : null;
    } else {
      props = propsOrGetProps;
    }
    if (!props) {
      continue;
    }
    mutablyMergeProps(outputProps, props, isItem, eventHandlers);
  }
  mutablyMergeProps(outputProps, userProps, isItem, eventHandlers);
  return outputProps;
}
function mutablyMergeProps(outputProps, props, isItem, eventHandlers) {
  for (const key in props) {
    const value = props[key];
    if (isItem && (key === ACTIVE_KEY || key === SELECTED_KEY)) {
      continue;
    }
    if (!key.startsWith("on")) {
      outputProps[key] = value;
    } else {
      if (!eventHandlers.has(key)) {
        eventHandlers.set(key, []);
      }
      if (typeof value === "function") {
        eventHandlers.get(key)?.push(value);
        outputProps[key] = (...args) => {
          return eventHandlers.get(key)?.map((fn) => fn(...args)).find((val) => val !== void 0);
        };
      }
    }
  }
}

// node_modules/@base-ui/react/esm/floating-ui-react/safePolygon.js
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

// node_modules/@base-ui/react/esm/utils/popupStateMapping.js
var CommonPopupDataAttributes = (function(CommonPopupDataAttributes2) {
  CommonPopupDataAttributes2["open"] = "data-open";
  CommonPopupDataAttributes2["closed"] = "data-closed";
  CommonPopupDataAttributes2[CommonPopupDataAttributes2["startingStyle"] = TransitionStatusDataAttributes.startingStyle] = "startingStyle";
  CommonPopupDataAttributes2[CommonPopupDataAttributes2["endingStyle"] = TransitionStatusDataAttributes.endingStyle] = "endingStyle";
  CommonPopupDataAttributes2["anchorHidden"] = "data-anchor-hidden";
  CommonPopupDataAttributes2["side"] = "data-side";
  CommonPopupDataAttributes2["align"] = "data-align";
  return CommonPopupDataAttributes2;
})({});
var CommonTriggerDataAttributes = /* @__PURE__ */ (function(CommonTriggerDataAttributes2) {
  CommonTriggerDataAttributes2["popupOpen"] = "data-popup-open";
  CommonTriggerDataAttributes2["pressed"] = "data-pressed";
  return CommonTriggerDataAttributes2;
})({});
var TRIGGER_HOOK = {
  [CommonTriggerDataAttributes.popupOpen]: ""
};
var PRESSABLE_TRIGGER_HOOK = {
  [CommonTriggerDataAttributes.popupOpen]: "",
  [CommonTriggerDataAttributes.pressed]: ""
};
var POPUP_OPEN_HOOK = {
  [CommonPopupDataAttributes.open]: ""
};
var POPUP_CLOSED_HOOK = {
  [CommonPopupDataAttributes.closed]: ""
};
var ANCHOR_HIDDEN_HOOK = {
  [CommonPopupDataAttributes.anchorHidden]: ""
};
var triggerOpenStateMapping = {
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

// node_modules/@base-ui/utils/esm/inertValue.js
function inertValue(value) {
  if (isReactVersionAtLeast(19)) {
    return value;
  }
  return value ? "true" : void 0;
}

// node_modules/@base-ui/react/esm/utils/useAnchorPositioning.js
var React34 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/esm/floating-ui-react/middleware/arrow.js
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
var arrow4 = (options, deps) => ({
  ...baseArrow(options),
  options: [options, deps]
});

// node_modules/@base-ui/react/esm/utils/hideMiddleware.js
var hide4 = {
  name: "hide",
  async fn(state) {
    const {
      width,
      height,
      x,
      y
    } = state.rects.reference;
    const anchorHidden = width === 0 && height === 0 && x === 0 && y === 0;
    const nativeHideResult = await hide3().fn(state);
    return {
      data: {
        referenceHidden: nativeHideResult.data?.referenceHidden || anchorHidden
      }
    };
  }
};

// node_modules/@base-ui/react/esm/utils/adaptiveOriginMiddleware.js
var DEFAULT_SIDES = {
  sideX: "left",
  sideY: "top"
};
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

// node_modules/@base-ui/react/esm/utils/useAnchorPositioning.js
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
    // Private parameters
    keepMounted = false,
    floatingRootContext,
    mounted,
    collisionAvoidance,
    shiftCrossAxis = false,
    nodeId,
    adaptiveOrigin: adaptiveOrigin2,
    lazyFlip = false,
    externalTree
  } = params;
  const [mountSide, setMountSide] = React34.useState(null);
  if (!mounted && mountSide !== null) {
    setMountSide(null);
  }
  const collisionAvoidanceSide = collisionAvoidance.side || "flip";
  const collisionAvoidanceAlign = collisionAvoidance.align || "flip";
  const collisionAvoidanceFallbackAxisSide = collisionAvoidance.fallbackAxisSide || "end";
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
  const bias = 1;
  const biasTop = sideParam === "bottom" ? bias : 0;
  const biasBottom = sideParam === "top" ? bias : 0;
  const biasLeft = sideParam === "right" ? bias : 0;
  const biasRight = sideParam === "left" ? bias : 0;
  if (typeof collisionPadding === "number") {
    collisionPadding = {
      top: collisionPadding + biasTop,
      right: collisionPadding + biasRight,
      bottom: collisionPadding + biasBottom,
      left: collisionPadding + biasLeft
    };
  } else if (collisionPadding) {
    collisionPadding = {
      top: (collisionPadding.top || 0) + biasTop,
      right: (collisionPadding.right || 0) + biasRight,
      bottom: (collisionPadding.bottom || 0) + biasBottom,
      left: (collisionPadding.left || 0) + biasLeft
    };
  }
  const commonCollisionProps = {
    boundary: collisionBoundary === "clipping-ancestors" ? "clippingAncestors" : collisionBoundary,
    padding: collisionPadding
  };
  const arrowRef = React34.useRef(null);
  const sideOffsetRef = useValueAsRef(sideOffset);
  const alignOffsetRef = useValueAsRef(alignOffset);
  const sideOffsetDep = typeof sideOffset !== "function" ? sideOffset : 0;
  const alignOffsetDep = typeof alignOffset !== "function" ? alignOffset : 0;
  const middleware = [offset3((state) => {
    const data = getOffsetData(state, sideParam, isRtl);
    const sideAxis = typeof sideOffsetRef.current === "function" ? sideOffsetRef.current(data) : sideOffsetRef.current;
    const alignAxis = typeof alignOffsetRef.current === "function" ? alignOffsetRef.current(data) : alignOffsetRef.current;
    return {
      mainAxis: sideAxis,
      crossAxis: alignAxis,
      alignmentAxis: alignAxis
    };
  }, [sideOffsetDep, alignOffsetDep, isRtl, sideParam])];
  const shiftDisabled = collisionAvoidanceAlign === "none" && collisionAvoidanceSide !== "shift";
  const crossAxisShiftEnabled = !shiftDisabled && (sticky || shiftCrossAxis || collisionAvoidanceSide === "shift");
  const flipMiddleware = collisionAvoidanceSide === "none" ? null : flip3({
    ...commonCollisionProps,
    // Ensure the popup flips if it's been limited by its --available-height and it resizes.
    // Since the size() padding is smaller than the flip() padding, flip() will take precedence.
    padding: {
      top: collisionPadding.top + bias,
      right: collisionPadding.right + bias,
      bottom: collisionPadding.bottom + bias,
      left: collisionPadding.left + bias
    },
    mainAxis: !shiftCrossAxis && collisionAvoidanceSide === "flip",
    crossAxis: collisionAvoidanceAlign === "flip" ? "alignment" : false,
    fallbackAxisSideDirection: collisionAvoidanceFallbackAxisSide
  });
  const shiftMiddleware = shiftDisabled ? null : shift3((data) => {
    const html = ownerDocument(data.elements.floating).documentElement;
    return {
      ...commonCollisionProps,
      // Use the Layout Viewport to avoid shifting around when pinch-zooming
      // for context menus.
      rootBoundary: shiftCrossAxis ? {
        x: 0,
        y: 0,
        width: html.clientWidth,
        height: html.clientHeight
      } : void 0,
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
    };
  }, [commonCollisionProps, sticky, shiftCrossAxis, collisionPadding, collisionAvoidanceAlign]);
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
      availableWidth,
      availableHeight,
      rects
    }) {
      if (!mountedRef.current) {
        return;
      }
      const floatingStyle = floating.style;
      floatingStyle.setProperty("--available-width", `${availableWidth}px`);
      floatingStyle.setProperty("--available-height", `${availableHeight}px`);
      const dpr = getWindow(floating).devicePixelRatio || 1;
      const {
        x: x2,
        y: y2,
        width,
        height
      } = rects.reference;
      const anchorWidth = (Math.round((x2 + width) * dpr) - Math.round(x2 * dpr)) / dpr;
      const anchorHeight = (Math.round((y2 + height) * dpr) - Math.round(y2 * dpr)) / dpr;
      floatingStyle.setProperty("--anchor-width", `${anchorWidth}px`);
      floatingStyle.setProperty("--anchor-height", `${anchorHeight}px`);
    }
  }), arrow4(() => ({
    // `transform-origin` calculations rely on an element existing. If the arrow hasn't been set,
    // we'll create a fake element.
    element: arrowRef.current || ownerDocument(arrowRef.current).createElement("div"),
    padding: arrowPadding,
    offsetParent: "floating"
  }), [arrowPadding]), {
    name: "transformOrigin",
    fn(state) {
      const {
        elements: elements2,
        middlewareData: middlewareData2,
        placement: renderedPlacement2,
        rects,
        y: y2
      } = state;
      const currentRenderedSide = getSide(renderedPlacement2);
      const currentRenderedAxis = getSideAxis(currentRenderedSide);
      const arrowEl = arrowRef.current;
      const arrowX = middlewareData2.arrow?.x || 0;
      const arrowY = middlewareData2.arrow?.y || 0;
      const arrowWidth = arrowEl?.clientWidth || 0;
      const arrowHeight = arrowEl?.clientHeight || 0;
      const transformX = arrowX + arrowWidth / 2;
      const transformY = arrowY + arrowHeight / 2;
      const shiftY = Math.abs(middlewareData2.shift?.y || 0);
      const halfAnchorHeight = rects.reference.height / 2;
      const sideOffsetValue = typeof sideOffset === "function" ? sideOffset(getOffsetData(state, sideParam, isRtl)) : sideOffset;
      const isOverlappingAnchor = shiftY > sideOffsetValue;
      const adjacentTransformOrigin = {
        top: `${transformX}px calc(100% + ${sideOffsetValue}px)`,
        bottom: `${transformX}px ${-sideOffsetValue}px`,
        left: `calc(100% + ${sideOffsetValue}px) ${transformY}px`,
        right: `${-sideOffsetValue}px ${transformY}px`
      }[currentRenderedSide];
      const overlapTransformOrigin = `${transformX}px ${rects.reference.y + halfAnchorHeight - y2}px`;
      elements2.floating.style.setProperty("--transform-origin", crossAxisShiftEnabled && currentRenderedAxis === "y" && isOverlappingAnchor ? overlapTransformOrigin : adjacentTransformOrigin);
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
  const autoUpdateOptions = React34.useMemo(() => ({
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
  } = useFloating2({
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
  const floatingStyles = React34.useMemo(() => {
    const base = adaptiveOrigin2 ? {
      position: resolvedPosition,
      [sideX]: x,
      [sideY]: y
    } : {
      position: resolvedPosition,
      ...originalFloatingStyles
    };
    if (!isPositioned) {
      base.opacity = 0;
    }
    return base;
  }, [adaptiveOrigin2, resolvedPosition, sideX, x, sideY, y, originalFloatingStyles, isPositioned]);
  const registeredPositionReferenceRef = React34.useRef(null);
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
  React34.useEffect(() => {
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
  React34.useEffect(() => {
    if (keepMounted && mounted && elements.domReference && elements.floating) {
      return autoUpdate(elements.domReference, elements.floating, update2, autoUpdateOptions);
    }
    return void 0;
  }, [keepMounted, mounted, elements, update2, autoUpdateOptions]);
  const renderedSide = getSide(renderedPlacement);
  const logicalRenderedSide = getLogicalSide(sideParam, renderedSide, isRtl);
  const renderedAlign = getAlignment(renderedPlacement) || "center";
  const anchorHidden = Boolean(middlewareData.hide?.referenceHidden);
  useIsoLayoutEffect(() => {
    if (lazyFlip && mounted && isPositioned) {
      setMountSide(renderedSide);
    }
  }, [lazyFlip, mounted, isPositioned, renderedSide]);
  const arrowStyles = React34.useMemo(() => ({
    position: "absolute",
    top: middlewareData.arrow?.y,
    left: middlewareData.arrow?.x
  }), [middlewareData.arrow]);
  const arrowUncentered = middlewareData.arrow?.centerOffset !== 0;
  return React34.useMemo(() => ({
    positionerStyles: floatingStyles,
    arrowStyles,
    arrowRef,
    arrowUncentered,
    side: logicalRenderedSide,
    align: renderedAlign,
    physicalSide: renderedSide,
    anchorHidden,
    refs,
    context,
    isPositioned,
    update: update2
  }), [floatingStyles, arrowStyles, arrowRef, arrowUncentered, logicalRenderedSide, renderedAlign, renderedSide, anchorHidden, refs, context, isPositioned, update2]);
}
function isRef(param) {
  return param != null && "current" in param;
}

// node_modules/@base-ui/react/esm/utils/getDisabledMountTransitionStyles.js
function getDisabledMountTransitionStyles(transitionStatus) {
  return transitionStatus === "starting" ? DISABLED_TRANSITIONS_STYLE : EMPTY_OBJECT;
}

// node_modules/@base-ui/react/esm/utils/usePositioner.js
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

// node_modules/@base-ui/react/esm/button/Button.js
var React35 = __toESM(require_react(), 1);
var Button = /* @__PURE__ */ React35.forwardRef(function Button2(componentProps, forwardedRef) {
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

// node_modules/@base-ui/react/esm/utils/usePopupViewport.js
var React38 = __toESM(require_react(), 1);
var ReactDOM5 = __toESM(require_react_dom(), 1);

// node_modules/@base-ui/utils/esm/usePreviousValue.js
var React36 = __toESM(require_react(), 1);
function usePreviousValue(value) {
  const [state, setState] = React36.useState({
    current: value,
    previous: null
  });
  if (value !== state.current) {
    setState({
      current: value,
      previous: state.current
    });
  }
  return state.previous;
}

// node_modules/@base-ui/react/esm/utils/usePopupAutoResize.js
var React37 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/esm/utils/getCssDimensions.js
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

// node_modules/@base-ui/react/esm/utils/usePopupAutoResize.js
var DEFAULT_ENABLED = () => true;
function usePopupAutoResize(parameters) {
  const {
    popupElement,
    positionerElement,
    content,
    mounted,
    enabled = DEFAULT_ENABLED,
    onMeasureLayout: onMeasureLayoutParam,
    onMeasureLayoutComplete: onMeasureLayoutCompleteParam,
    side,
    direction
  } = parameters;
  const runOnceAnimationsFinish = useAnimationsFinished(popupElement, true, false);
  const animationFrame = useAnimationFrame();
  const committedDimensionsRef = React37.useRef(null);
  const liveDimensionsRef = React37.useRef(null);
  const isInitialRenderRef = React37.useRef(true);
  const restoreAnchoringStylesRef = React37.useRef(NOOP);
  const onMeasureLayout = useStableCallback(onMeasureLayoutParam);
  const onMeasureLayoutComplete = useStableCallback(onMeasureLayoutCompleteParam);
  const anchoringStyles = React37.useMemo(() => {
    let isOriginSide = side === "top";
    let isPhysicalLeft = side === "left";
    if (direction === "rtl") {
      isOriginSide = isOriginSide || side === "inline-end";
      isPhysicalLeft = isPhysicalLeft || side === "inline-end";
    } else {
      isOriginSide = isOriginSide || side === "inline-start";
      isPhysicalLeft = isPhysicalLeft || side === "inline-start";
    }
    return isOriginSide ? {
      position: "absolute",
      [side === "top" ? "bottom" : "top"]: "0",
      [isPhysicalLeft ? "right" : "left"]: "0"
    } : EMPTY_OBJECT;
  }, [side, direction]);
  useIsoLayoutEffect(() => {
    if (!mounted || !enabled() || typeof ResizeObserver !== "function") {
      restoreAnchoringStylesRef.current = NOOP;
      isInitialRenderRef.current = true;
      committedDimensionsRef.current = null;
      liveDimensionsRef.current = null;
      return void 0;
    }
    if (!popupElement || !positionerElement) {
      return void 0;
    }
    restoreAnchoringStylesRef.current = applyElementStyles(popupElement, anchoringStyles);
    const observer = new ResizeObserver((entries) => {
      const entry = entries[0];
      if (entry) {
        liveDimensionsRef.current = {
          width: Math.ceil(entry.borderBoxSize[0].inlineSize),
          height: Math.ceil(entry.borderBoxSize[0].blockSize)
        };
      }
    });
    observer.observe(popupElement);
    setPopupCssSize(popupElement, "auto");
    const restorePopupPosition = overrideElementStyle(popupElement, "position", "static");
    const restorePopupTransform = overrideElementStyle(popupElement, "transform", "none");
    const restorePopupScale = overrideElementStyle(popupElement, "scale", "1");
    const restorePositionerAvailableSize = applyElementStyles(positionerElement, {
      "--available-width": "max-content",
      "--available-height": "max-content"
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
        observer.disconnect();
        restoreAnchoringStylesRef.current();
        restoreAnchoringStylesRef.current = NOOP;
      };
    }
    setPopupCssSize(popupElement, "auto");
    setPositionerCssSize(positionerElement, "max-content");
    const previousDimensions = committedDimensionsRef.current ?? liveDimensionsRef.current;
    const newDimensions = getCssDimensions2(popupElement);
    committedDimensionsRef.current = newDimensions;
    if (!previousDimensions) {
      setPositionerCssSize(positionerElement, newDimensions);
      restoreMeasurementOverridesIncludingScale();
      onMeasureLayoutComplete?.(null, newDimensions);
      return () => {
        observer.disconnect();
        animationFrame.cancel();
        restoreAnchoringStylesRef.current();
        restoreAnchoringStylesRef.current = NOOP;
      };
    }
    setPopupCssSize(popupElement, previousDimensions);
    restoreMeasurementOverridesIncludingScale();
    onMeasureLayoutComplete?.(previousDimensions, newDimensions);
    setPositionerCssSize(positionerElement, newDimensions);
    const abortController = new AbortController();
    animationFrame.request(() => {
      setPopupCssSize(popupElement, newDimensions);
      runOnceAnimationsFinish(() => {
        popupElement.style.setProperty("--popup-width", "auto");
        popupElement.style.setProperty("--popup-height", "auto");
      }, abortController.signal);
    });
    return () => {
      observer.disconnect();
      abortController.abort();
      animationFrame.cancel();
      restoreAnchoringStylesRef.current();
      restoreAnchoringStylesRef.current = NOOP;
    };
  }, [content, popupElement, positionerElement, runOnceAnimationsFinish, animationFrame, enabled, mounted, onMeasureLayout, onMeasureLayoutComplete, anchoringStyles]);
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
  popupElement.style.setProperty("--popup-width", width);
  popupElement.style.setProperty("--popup-height", height);
}
function setPositionerCssSize(positionerElement, size4) {
  const width = size4 === "max-content" ? "max-content" : `${size4.width}px`;
  const height = size4 === "max-content" ? "max-content" : `${size4.height}px`;
  positionerElement.style.setProperty("--positioner-width", width);
  positionerElement.style.setProperty("--positioner-height", height);
}

// node_modules/@base-ui/react/esm/utils/usePopupViewport.js
var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
function usePopupViewport(parameters) {
  const {
    store: store2,
    side,
    cssVars,
    children
  } = parameters;
  const direction = useDirection();
  const activeTrigger = store2.useState("activeTriggerElement");
  const activeTriggerId = store2.useState("activeTriggerId");
  const open = store2.useState("open");
  const payload = store2.useState("payload");
  const mounted = store2.useState("mounted");
  const popupElement = store2.useState("popupElement");
  const positionerElement = store2.useState("positionerElement");
  const previousActiveTrigger = usePreviousValue(open ? activeTrigger : null);
  const currentContentKey = usePopupContentKey(activeTriggerId, payload);
  const capturedNodeRef = React38.useRef(null);
  const [previousContentNode, setPreviousContentNode] = React38.useState(null);
  const [newTriggerOffset, setNewTriggerOffset] = React38.useState(null);
  const currentContainerRef = React38.useRef(null);
  const previousContainerRef = React38.useRef(null);
  const onAnimationsFinished = useAnimationsFinished(currentContainerRef, true, false);
  const cleanupFrame = useAnimationFrame();
  const [previousContentDimensions, setPreviousContentDimensions] = React38.useState(null);
  const [showStartingStyleAttribute, setShowStartingStyleAttribute] = React38.useState(false);
  useIsoLayoutEffect(() => {
    store2.set("hasViewport", true);
    return () => {
      store2.set("hasViewport", false);
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
  const lastHandledTriggerRef = React38.useRef(null);
  useIsoLayoutEffect(() => {
    if (activeTrigger && previousActiveTrigger && activeTrigger !== previousActiveTrigger && lastHandledTriggerRef.current !== activeTrigger && capturedNodeRef.current) {
      setPreviousContentNode(capturedNodeRef.current);
      setShowStartingStyleAttribute(true);
      const offset4 = calculateRelativePosition(previousActiveTrigger, activeTrigger);
      setNewTriggerOffset(offset4);
      cleanupFrame.request(() => {
        ReactDOM5.flushSync(() => {
          setShowStartingStyleAttribute(false);
        });
        onAnimationsFinished(() => {
          setPreviousContentNode(null);
          setPreviousContentDimensions(null);
          capturedNodeRef.current = null;
        });
      });
      lastHandledTriggerRef.current = activeTrigger;
    }
  }, [activeTrigger, previousActiveTrigger, previousContentNode, onAnimationsFinished, cleanupFrame]);
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
    childrenToRender = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)("div", {
      "data-current": true,
      ref: currentContainerRef,
      children
    }, currentContentKey);
  } else {
    childrenToRender = /* @__PURE__ */ (0, import_jsx_runtime5.jsxs)(React38.Fragment, {
      children: [/* @__PURE__ */ (0, import_jsx_runtime5.jsx)("div", {
        "data-previous": true,
        inert: inertValue(true),
        ref: previousContainerRef,
        style: {
          ...previousContentDimensions ? {
            [cssVars.popupWidth]: `${previousContentDimensions.width}px`,
            [cssVars.popupHeight]: `${previousContentDimensions.height}px`
          } : null,
          position: "absolute"
        },
        "data-ending-style": showStartingStyleAttribute ? void 0 : ""
      }, "previous"), /* @__PURE__ */ (0, import_jsx_runtime5.jsx)("div", {
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
  const [contentKey, setContentKey] = React38.useState(0);
  const previousActiveTriggerIdRef = React38.useRef(activeTriggerId);
  const previousPayloadRef = React38.useRef(payload);
  const pendingPayloadUpdateRef = React38.useRef(false);
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

// node_modules/@base-ui/react/esm/utils/FloatingPortalLite.js
var React39 = __toESM(require_react(), 1);
var ReactDOM6 = __toESM(require_react_dom(), 1);
var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
var FloatingPortalLite = /* @__PURE__ */ React39.forwardRef(function FloatingPortalLite2(componentProps, forwardedRef) {
  const {
    children,
    container,
    className,
    render,
    style,
    ...elementProps
  } = componentProps;
  const {
    portalNode,
    portalSubtree
  } = useFloatingPortalNode({
    container,
    ref: forwardedRef,
    componentProps,
    elementProps
  });
  if (!portalSubtree && !portalNode) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime6.jsxs)(React39.Fragment, {
    children: [portalSubtree, portalNode && /* @__PURE__ */ ReactDOM6.createPortal(children, portalNode)]
  });
});
if (true) FloatingPortalLite.displayName = "FloatingPortalLite";

// node_modules/@base-ui/react/esm/tooltip/index.parts.js
var index_parts_exports = {};
__export(index_parts_exports, {
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

// node_modules/@base-ui/react/esm/tooltip/root/TooltipRoot.js
var React42 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/esm/tooltip/root/TooltipRootContext.js
var React40 = __toESM(require_react(), 1);
var TooltipRootContext = /* @__PURE__ */ React40.createContext(void 0);
if (true) TooltipRootContext.displayName = "TooltipRootContext";
function useTooltipRootContext(optional) {
  const context = React40.useContext(TooltipRootContext);
  if (context === void 0 && !optional) {
    throw new Error(true ? "Base UI: TooltipRootContext is missing. Tooltip parts must be placed within <Tooltip.Root>." : formatErrorMessage_default(72));
  }
  return context;
}

// node_modules/@base-ui/react/esm/tooltip/store/TooltipStore.js
var React41 = __toESM(require_react(), 1);
var ReactDOM7 = __toESM(require_react_dom(), 1);
var selectors2 = {
  ...popupStoreSelectors,
  disabled: createSelector((state) => state.disabled),
  instantType: createSelector((state) => state.instantType),
  isInstantPhase: createSelector((state) => state.isInstantPhase),
  trackCursorAxis: createSelector((state) => state.trackCursorAxis),
  disableHoverablePopup: createSelector((state) => state.disableHoverablePopup),
  lastOpenChangeReason: createSelector((state) => state.openChangeReason),
  closeOnClick: createSelector((state) => state.closeOnClick),
  closeDelay: createSelector((state) => state.closeDelay),
  hasViewport: createSelector((state) => state.hasViewport)
};
var TooltipStore = class _TooltipStore extends ReactStore {
  constructor(initialState) {
    super({
      ...createInitialState(),
      ...initialState
    }, {
      popupRef: /* @__PURE__ */ React41.createRef(),
      onOpenChange: void 0,
      onOpenChangeComplete: void 0,
      triggerElements: new PopupTriggerMap()
    }, selectors2);
  }
  setOpen = (nextOpen, eventDetails) => {
    const reason = eventDetails.reason;
    const isHover = reason === reason_parts_exports.triggerHover;
    const isFocusOpen = nextOpen && reason === reason_parts_exports.triggerFocus;
    const isDismissClose = !nextOpen && (reason === reason_parts_exports.triggerPress || reason === reason_parts_exports.escapeKey);
    eventDetails.preventUnmountOnClose = () => {
      this.set("preventUnmountingOnClose", true);
    };
    this.context.onOpenChange?.(nextOpen, eventDetails);
    if (eventDetails.isCanceled) {
      return;
    }
    this.state.floatingRootContext.dispatchOpenChange(nextOpen, eventDetails);
    const changeState = () => {
      const updatedState = {
        open: nextOpen,
        openChangeReason: reason
      };
      if (isFocusOpen) {
        updatedState.instantType = "focus";
      } else if (isDismissClose) {
        updatedState.instantType = "dismiss";
      } else if (reason === reason_parts_exports.triggerHover) {
        updatedState.instantType = void 0;
      }
      const newTriggerId = eventDetails.trigger?.id ?? null;
      if (newTriggerId || nextOpen) {
        updatedState.activeTriggerId = newTriggerId;
        updatedState.activeTriggerElement = eventDetails.trigger ?? null;
      }
      this.update(updatedState);
    };
    if (isHover) {
      ReactDOM7.flushSync(changeState);
    } else {
      changeState();
    }
  };
  static useStore(externalStore, initialState) {
    const internalStore = useRefWithInit(() => {
      return new _TooltipStore(initialState);
    }).current;
    const store2 = externalStore ?? internalStore;
    const floatingRootContext = useSyncedFloatingRootContext({
      popupStore: store2,
      onOpenChange: store2.setOpen
    });
    store2.state.floatingRootContext = floatingRootContext;
    return store2;
  }
};
function createInitialState() {
  return {
    ...createInitialPopupStoreState(),
    disabled: false,
    instantType: void 0,
    isInstantPhase: false,
    trackCursorAxis: "none",
    disableHoverablePopup: false,
    openChangeReason: null,
    closeOnClick: true,
    closeDelay: 0,
    hasViewport: false
  };
}

// node_modules/@base-ui/react/esm/tooltip/root/TooltipRoot.js
var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
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
  const store2 = TooltipStore.useStore(handle?.store, {
    open: defaultOpen,
    openProp,
    activeTriggerId: defaultTriggerIdProp,
    triggerIdProp
  });
  useOnFirstRender(() => {
    if (openProp === void 0 && store2.state.open === false && defaultOpen === true) {
      store2.update({
        open: true,
        activeTriggerId: defaultTriggerIdProp
      });
    }
  });
  store2.useControlledProp("openProp", openProp);
  store2.useControlledProp("triggerIdProp", triggerIdProp);
  store2.useContextCallback("onOpenChange", onOpenChange);
  store2.useContextCallback("onOpenChangeComplete", onOpenChangeComplete);
  const openState = store2.useState("open");
  const open = !disabled2 && openState;
  const activeTriggerId = store2.useState("activeTriggerId");
  const payload = store2.useState("payload");
  store2.useSyncedValues({
    trackCursorAxis,
    disableHoverablePopup
  });
  useIsoLayoutEffect(() => {
    if (openState && disabled2) {
      store2.setOpen(false, createChangeEventDetails(reason_parts_exports.disabled));
    }
  }, [openState, disabled2, store2]);
  store2.useSyncedValue("disabled", disabled2);
  useImplicitActiveTrigger(store2);
  const {
    forceUnmount,
    transitionStatus
  } = useOpenStateTransitions(open, store2);
  const floatingRootContext = store2.select("floatingRootContext");
  const isInstantPhase = store2.useState("isInstantPhase");
  const instantType = store2.useState("instantType");
  const lastOpenChangeReason = store2.useState("lastOpenChangeReason");
  const previousInstantTypeRef = React42.useRef(null);
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
    if (open) {
      if (activeTriggerId == null) {
        store2.set("payload", void 0);
      }
    }
  }, [store2, activeTriggerId, open]);
  const handleImperativeClose = React42.useCallback(() => {
    store2.setOpen(false, createChangeEventDetails(reason_parts_exports.imperativeAction));
  }, [store2]);
  React42.useImperativeHandle(actionsRef, () => ({
    unmount: forceUnmount,
    close: handleImperativeClose
  }), [forceUnmount, handleImperativeClose]);
  const dismiss = useDismiss(floatingRootContext, {
    enabled: !disabled2,
    referencePress: () => store2.select("closeOnClick")
  });
  const clientPoint = useClientPoint(floatingRootContext, {
    enabled: !disabled2 && trackCursorAxis !== "none",
    axis: trackCursorAxis === "none" ? void 0 : trackCursorAxis
  });
  const {
    getReferenceProps,
    getFloatingProps,
    getTriggerProps
  } = useInteractions([dismiss, clientPoint]);
  const activeTriggerProps = React42.useMemo(() => getReferenceProps(), [getReferenceProps]);
  const inactiveTriggerProps = React42.useMemo(() => getTriggerProps(), [getTriggerProps]);
  const popupProps = React42.useMemo(() => getFloatingProps(), [getFloatingProps]);
  store2.useSyncedValues({
    activeTriggerProps,
    inactiveTriggerProps,
    popupProps
  });
  return /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(TooltipRootContext.Provider, {
    value: store2,
    children: typeof children === "function" ? children({
      payload
    }) : children
  });
});
if (true) TooltipRoot.displayName = "TooltipRoot";

// node_modules/@base-ui/react/esm/tooltip/trigger/TooltipTrigger.js
var React44 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/esm/tooltip/provider/TooltipProviderContext.js
var React43 = __toESM(require_react(), 1);
var TooltipProviderContext = /* @__PURE__ */ React43.createContext(void 0);
if (true) TooltipProviderContext.displayName = "TooltipProviderContext";
function useTooltipProviderContext() {
  return React43.useContext(TooltipProviderContext);
}

// node_modules/@base-ui/react/esm/tooltip/trigger/TooltipTriggerDataAttributes.js
var TooltipTriggerDataAttributes = (function(TooltipTriggerDataAttributes2) {
  TooltipTriggerDataAttributes2[TooltipTriggerDataAttributes2["popupOpen"] = CommonTriggerDataAttributes.popupOpen] = "popupOpen";
  TooltipTriggerDataAttributes2["triggerDisabled"] = "data-trigger-disabled";
  return TooltipTriggerDataAttributes2;
})({});

// node_modules/@base-ui/react/esm/tooltip/utils/constants.js
var OPEN_DELAY = 600;

// node_modules/@base-ui/react/esm/tooltip/trigger/TooltipTrigger.js
var TooltipTrigger = fastComponentRef(function TooltipTrigger2(componentProps, forwardedRef) {
  const {
    className,
    render,
    handle,
    payload,
    disabled: disabledProp,
    delay,
    closeOnClick = true,
    closeDelay,
    id: idProp,
    style,
    ...elementProps
  } = componentProps;
  const rootContext = useTooltipRootContext(true);
  const store2 = handle?.store ?? rootContext;
  if (!store2) {
    throw new Error(true ? "Base UI: <Tooltip.Trigger> must be either used within a <Tooltip.Root> component or provided with a handle." : formatErrorMessage_default(82));
  }
  const thisTriggerId = useBaseUiId(idProp);
  const isTriggerActive = store2.useState("isTriggerActive", thisTriggerId);
  const isOpenedByThisTrigger = store2.useState("isOpenedByTrigger", thisTriggerId);
  const floatingRootContext = store2.useState("floatingRootContext");
  const triggerElementRef = React44.useRef(null);
  const delayWithDefault = delay ?? OPEN_DELAY;
  const closeDelayWithDefault = closeDelay ?? 0;
  const {
    registerTrigger,
    isMountedByThisTrigger
  } = useTriggerDataForwarding(thisTriggerId, triggerElementRef, store2, {
    payload,
    closeOnClick,
    closeDelay: closeDelayWithDefault
  });
  const providerContext = useTooltipProviderContext();
  const {
    delayRef,
    isInstantPhase,
    hasProvider
  } = useDelayGroup(floatingRootContext, {
    open: isOpenedByThisTrigger
  });
  store2.useSyncedValue("isInstantPhase", isInstantPhase);
  const rootDisabled = store2.useState("disabled");
  const disabled2 = disabledProp ?? rootDisabled;
  const trackCursorAxis = store2.useState("trackCursorAxis");
  const disableHoverablePopup = store2.useState("disableHoverablePopup");
  const hoverProps = useHoverReferenceInteraction(floatingRootContext, {
    enabled: !disabled2,
    mouseOnly: true,
    move: false,
    handleClose: !disableHoverablePopup && trackCursorAxis !== "both" ? safePolygon() : null,
    restMs() {
      const providerDelay = providerContext?.delay;
      const groupOpenValue = typeof delayRef.current === "object" ? delayRef.current.open : void 0;
      let computedRestMs = delayWithDefault;
      if (hasProvider) {
        if (groupOpenValue !== 0) {
          computedRestMs = delay ?? providerDelay ?? delayWithDefault;
        } else {
          computedRestMs = 0;
        }
      }
      return computedRestMs;
    },
    delay() {
      const closeValue = typeof delayRef.current === "object" ? delayRef.current.close : void 0;
      let computedCloseDelay = closeDelayWithDefault;
      if (closeDelay == null && hasProvider) {
        computedCloseDelay = closeValue;
      }
      return {
        close: computedCloseDelay
      };
    },
    triggerElementRef,
    isActiveTrigger: isTriggerActive,
    isClosing: () => store2.select("transitionStatus") === "ending"
  });
  const focusProps = useFocus(floatingRootContext, {
    enabled: !disabled2
  }).reference;
  const state = {
    open: isOpenedByThisTrigger
  };
  const rootTriggerProps = store2.useState("triggerProps", isMountedByThisTrigger);
  const element = useRenderElement("button", componentProps, {
    state,
    ref: [forwardedRef, registerTrigger, triggerElementRef],
    props: [hoverProps, focusProps, rootTriggerProps, {
      onPointerDown() {
        store2.set("closeOnClick", closeOnClick);
      },
      id: thisTriggerId,
      [TooltipTriggerDataAttributes.triggerDisabled]: disabled2 ? "" : void 0
    }, elementProps],
    stateAttributesMapping: triggerOpenStateMapping
  });
  return element;
});
if (true) TooltipTrigger.displayName = "TooltipTrigger";

// node_modules/@base-ui/react/esm/tooltip/portal/TooltipPortal.js
var React46 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/esm/tooltip/portal/TooltipPortalContext.js
var React45 = __toESM(require_react(), 1);
var TooltipPortalContext = /* @__PURE__ */ React45.createContext(void 0);
if (true) TooltipPortalContext.displayName = "TooltipPortalContext";
function useTooltipPortalContext() {
  const value = React45.useContext(TooltipPortalContext);
  if (value === void 0) {
    throw new Error(true ? "Base UI: <Tooltip.Portal> is missing." : formatErrorMessage_default(70));
  }
  return value;
}

// node_modules/@base-ui/react/esm/tooltip/portal/TooltipPortal.js
var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
var TooltipPortal = /* @__PURE__ */ React46.forwardRef(function TooltipPortal2(props, forwardedRef) {
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
  return /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(TooltipPortalContext.Provider, {
    value: keepMounted,
    children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(FloatingPortalLite, {
      ref: forwardedRef,
      ...portalProps
    })
  });
});
if (true) TooltipPortal.displayName = "TooltipPortal";

// node_modules/@base-ui/react/esm/tooltip/positioner/TooltipPositioner.js
var React48 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/esm/tooltip/positioner/TooltipPositionerContext.js
var React47 = __toESM(require_react(), 1);
var TooltipPositionerContext = /* @__PURE__ */ React47.createContext(void 0);
if (true) TooltipPositionerContext.displayName = "TooltipPositionerContext";
function useTooltipPositionerContext() {
  const context = React47.useContext(TooltipPositionerContext);
  if (context === void 0) {
    throw new Error(true ? "Base UI: TooltipPositionerContext is missing. TooltipPositioner parts must be placed within <Tooltip.Positioner>." : formatErrorMessage_default(71));
  }
  return context;
}

// node_modules/@base-ui/react/esm/tooltip/positioner/TooltipPositioner.js
var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
var TooltipPositioner = /* @__PURE__ */ React48.forwardRef(function TooltipPositioner2(componentProps, forwardedRef) {
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
  const open = store2.useState("open");
  const mounted = store2.useState("mounted");
  const trackCursorAxis = store2.useState("trackCursorAxis");
  const disableHoverablePopup = store2.useState("disableHoverablePopup");
  const floatingRootContext = store2.useState("floatingRootContext");
  const instantType = store2.useState("instantType");
  const transitionStatus = store2.useState("transitionStatus");
  const hasViewport = store2.useState("hasViewport");
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
    adaptiveOrigin: hasViewport ? adaptiveOrigin : void 0
  });
  const state = React48.useMemo(() => ({
    open,
    side: positioning.side,
    align: positioning.align,
    anchorHidden: positioning.anchorHidden,
    instant: trackCursorAxis !== "none" ? "tracking-cursor" : instantType
  }), [open, positioning.side, positioning.align, positioning.anchorHidden, trackCursorAxis, instantType]);
  const element = usePositioner(componentProps, state, {
    styles: positioning.positionerStyles,
    transitionStatus,
    props: elementProps,
    refs: [forwardedRef, store2.useStateSetter("positionerElement")],
    hidden: !mounted,
    inert: !open || trackCursorAxis === "both" || disableHoverablePopup
  });
  return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(TooltipPositionerContext.Provider, {
    value: positioning,
    children: element
  });
});
if (true) TooltipPositioner.displayName = "TooltipPositioner";

// node_modules/@base-ui/react/esm/tooltip/popup/TooltipPopup.js
var React49 = __toESM(require_react(), 1);
var stateAttributesMapping = {
  ...popupStateMapping,
  ...transitionStatusMapping
};
var TooltipPopup = /* @__PURE__ */ React49.forwardRef(function TooltipPopup2(componentProps, forwardedRef) {
  const {
    className,
    render,
    style,
    ...elementProps
  } = componentProps;
  const store2 = useTooltipRootContext();
  const {
    side,
    align
  } = useTooltipPositionerContext();
  const open = store2.useState("open");
  const instantType = store2.useState("instantType");
  const transitionStatus = store2.useState("transitionStatus");
  const popupProps = store2.useState("popupProps");
  const floatingContext = store2.useState("floatingRootContext");
  useOpenChangeComplete({
    open,
    ref: store2.context.popupRef,
    onComplete() {
      if (open) {
        store2.context.onOpenChangeComplete?.(true);
      }
    }
  });
  const disabled2 = store2.useState("disabled");
  const closeDelay = store2.useState("closeDelay");
  useHoverFloatingInteraction(floatingContext, {
    enabled: !disabled2,
    closeDelay
  });
  const state = {
    open,
    side,
    align,
    instant: instantType,
    transitionStatus
  };
  const element = useRenderElement("div", componentProps, {
    state,
    ref: [forwardedRef, store2.context.popupRef, store2.useStateSetter("popupElement")],
    props: [popupProps, getDisabledMountTransitionStyles(transitionStatus), elementProps],
    stateAttributesMapping
  });
  return element;
});
if (true) TooltipPopup.displayName = "TooltipPopup";

// node_modules/@base-ui/react/esm/tooltip/arrow/TooltipArrow.js
var React50 = __toESM(require_react(), 1);
var TooltipArrow = /* @__PURE__ */ React50.forwardRef(function TooltipArrow2(componentProps, forwardedRef) {
  const {
    className,
    render,
    style,
    ...elementProps
  } = componentProps;
  const store2 = useTooltipRootContext();
  const open = store2.useState("open");
  const instantType = store2.useState("instantType");
  const {
    arrowRef,
    side,
    align,
    arrowUncentered,
    arrowStyles
  } = useTooltipPositionerContext();
  const state = {
    open,
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

// node_modules/@base-ui/react/esm/tooltip/provider/TooltipProvider.js
var React51 = __toESM(require_react(), 1);
var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
var TooltipProvider = function TooltipProvider2(props) {
  const {
    delay,
    closeDelay,
    timeout = 400
  } = props;
  const contextValue = React51.useMemo(() => ({
    delay,
    closeDelay
  }), [delay, closeDelay]);
  const delayValue = React51.useMemo(() => ({
    open: delay,
    close: closeDelay
  }), [delay, closeDelay]);
  return /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(TooltipProviderContext.Provider, {
    value: contextValue,
    children: /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(FloatingDelayGroup, {
      delay: delayValue,
      timeoutMs: timeout,
      children: props.children
    })
  });
};
if (true) TooltipProvider.displayName = "TooltipProvider";

// node_modules/@base-ui/react/esm/tooltip/viewport/TooltipViewport.js
var React52 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/esm/tooltip/viewport/TooltipViewportCssVars.js
var TooltipViewportCssVars = /* @__PURE__ */ (function(TooltipViewportCssVars2) {
  TooltipViewportCssVars2["popupWidth"] = "--popup-width";
  TooltipViewportCssVars2["popupHeight"] = "--popup-height";
  return TooltipViewportCssVars2;
})({});

// node_modules/@base-ui/react/esm/tooltip/viewport/TooltipViewport.js
var stateAttributesMapping2 = {
  activationDirection: (value) => value ? {
    "data-activation-direction": value
  } : null
};
var TooltipViewport = /* @__PURE__ */ React52.forwardRef(function TooltipViewport2(componentProps, forwardedRef) {
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
    cssVars: TooltipViewportCssVars,
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
    stateAttributesMapping: stateAttributesMapping2
  });
});
if (true) TooltipViewport.displayName = "TooltipViewport";

// node_modules/@base-ui/react/esm/tooltip/store/TooltipHandle.js
var TooltipHandle = class {
  /**
   * Internal store holding the tooltip state.
   * @internal
   */
  constructor() {
    this.store = new TooltipStore();
  }
  /**
   * Opens the tooltip and associates it with the trigger with the given ID.
   * The trigger must be a Tooltip.Trigger component with this handle passed as a prop.
   *
   * This method should only be called in an event handler or an effect (not during rendering).
   *
   * @param triggerId ID of the trigger to associate with the tooltip.
   */
  open(triggerId) {
    const triggerElement = triggerId ? this.store.context.triggerElements.getById(triggerId) : void 0;
    if (triggerId && !triggerElement) {
      throw new Error(true ? `Base UI: TooltipHandle.open: No trigger found with id "${triggerId}".` : formatErrorMessage_default(81, triggerId));
    }
    this.store.setOpen(true, createChangeEventDetails(reason_parts_exports.imperativeAction, void 0, triggerElement));
  }
  /**
   * Closes the tooltip.
   */
  close() {
    this.store.setOpen(false, createChangeEventDetails(reason_parts_exports.imperativeAction, void 0, void 0));
  }
  /**
   * Indicates whether the tooltip is currently open.
   */
  get isOpen() {
    return this.store.state.open;
  }
};
function createTooltipHandle() {
  return new TooltipHandle();
}

// node_modules/@base-ui/react/esm/use-render/useRender.js
function useRender(params) {
  return useRenderElement(params.defaultTagName ?? "div", params, params);
}

// packages/ui/build-module/text/text.mjs
var import_element8 = __toESM(require_element(), 1);
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
  registerStyle("0c8601dd83", '@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._83ed8a8da5dd50ea__text{margin:0}._14437cfb77831647__heading-2xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-2xl,32px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-medium,499);--_gcd-p-font-size:var(--wpds-typography-font-size-2xl,32px);--_gcd-p-line-height:var(--wpds-typography-line-height-2xl,40px);font-size:var(--wpds-typography-font-size-2xl,32px);line-height:var(--wpds-typography-line-height-2xl,40px)}._14437cfb77831647__heading-2xl,._3c78b7fa9b4072dd__heading-xl{font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-medium,499)}._3c78b7fa9b4072dd__heading-xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-medium,499);--_gcd-p-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-p-line-height:var(--wpds-typography-line-height-md,24px);font-size:var(--wpds-typography-font-size-xl,20px);line-height:var(--wpds-typography-line-height-md,24px)}.aa58f227716bcde2__heading-lg{--_gcd-heading-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-medium,499);--_gcd-p-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-p-line-height:var(--wpds-typography-line-height-sm,20px);font-size:var(--wpds-typography-font-size-lg,15px)}.aa58f227716bcde2__heading-lg,.fc4da56d8dfe52c4__heading-md{font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-medium,499);line-height:var(--wpds-typography-line-height-sm,20px)}.fc4da56d8dfe52c4__heading-md{--_gcd-heading-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-medium,499);--_gcd-p-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-p-line-height:var(--wpds-typography-line-height-sm,20px);font-size:var(--wpds-typography-font-size-md,13px)}.a9b78c7c82e8dff7__heading-sm{--_gcd-heading-font-size:var(--wpds-typography-font-size-xs,11px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-medium,499);--_gcd-p-font-size:var(--wpds-typography-font-size-xs,11px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-xs,11px);font-weight:var(--wpds-typography-font-weight-medium,499);line-height:var(--wpds-typography-line-height-xs,16px);text-transform:uppercase}._305ff559e52180d5__body-xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-regular,400);--_gcd-p-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-p-line-height:var(--wpds-typography-line-height-xl,32px);font-size:var(--wpds-typography-font-size-xl,20px);line-height:var(--wpds-typography-line-height-xl,32px)}._305ff559e52180d5__body-xl,.ca1aa3fc2029e958__body-lg{font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-regular,400)}.ca1aa3fc2029e958__body-lg{--_gcd-heading-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-regular,400);--_gcd-p-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-p-line-height:var(--wpds-typography-line-height-md,24px);font-size:var(--wpds-typography-font-size-lg,15px);line-height:var(--wpds-typography-line-height-md,24px)}._131101940be12424__body-md{--_gcd-heading-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-regular,400);--_gcd-p-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-p-line-height:var(--wpds-typography-line-height-sm,20px);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px)}._0e8d87a42c1f75fa__body-sm,._131101940be12424__body-md{font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-regular,400)}._0e8d87a42c1f75fa__body-sm{--_gcd-heading-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-regular,400);--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);font-size:var(--wpds-typography-font-size-sm,12px);line-height:var(--wpds-typography-line-height-xs,16px)}}');
}
var style_default = { "text": "_83ed8a8da5dd50ea__text", "heading-2xl": "_14437cfb77831647__heading-2xl", "heading-xl": "_3c78b7fa9b4072dd__heading-xl", "heading-lg": "aa58f227716bcde2__heading-lg", "heading-md": "fc4da56d8dfe52c4__heading-md", "heading-sm": "a9b78c7c82e8dff7__heading-sm", "body-xl": "_305ff559e52180d5__body-xl", "body-lg": "ca1aa3fc2029e958__body-lg", "body-md": "_131101940be12424__body-md", "body-sm": "_0e8d87a42c1f75fa__body-sm" };
if (typeof process === "undefined" || true) {
  registerStyle("1fb29d3a3c", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,#0000);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 #0000);color:var(--_gcd-input-color,var(--wpds-color-fg-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,#0000);border-color:var(--_gcd-input-border-color-disabled,#0000);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid #0000)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-fg-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-medium,499));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid #0000);transition:var(--_gcd-a-transition,none)}");
}
var global_css_defense_default = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a" };
var Text = (0, import_element8.forwardRef)(function Text2({ variant = "body-md", render, className, ...props }, ref) {
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

// packages/ui/build-module/badge/badge.mjs
var import_jsx_runtime11 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle2("d6a685e1aa", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._96e6251aad1a6136__badge{border-radius:var(--wpds-border-radius-lg,8px);padding-block:var(--wpds-dimension-padding-xs,4px);padding-inline:var(--wpds-dimension-padding-sm,8px)}._99f7158cb520f750__is-high-intent{background-color:var(--wpds-color-bg-surface-error,#f6e6e3);color:var(--wpds-color-fg-content-error,#470000)}.c20ebef2365bc8b7__is-medium-intent{background-color:var(--wpds-color-bg-surface-warning,#fde6be);color:var(--wpds-color-fg-content-warning,#2e1900)}._365e1626c6202e52__is-low-intent{background-color:var(--wpds-color-bg-surface-caution,#fee995);color:var(--wpds-color-fg-content-caution,#281d00)}._33f8198127ddf4ef__is-stable-intent{background-color:var(--wpds-color-bg-surface-success,#c6f7cd);color:var(--wpds-color-fg-content-success,#002900)}._04c1aca8fc449412__is-informational-intent{background-color:var(--wpds-color-bg-surface-info,#deebfa);color:var(--wpds-color-fg-content-info,#001b4f)}._90726e69d495ec19__is-draft-intent{background-color:var(--wpds-color-bg-surface-neutral-weak,#f4f4f4);color:var(--wpds-color-fg-content-neutral,#1e1e1e)}._898f4a544993bd39__is-none-intent{background-color:var(--wpds-color-bg-surface-neutral-strong,#fff);border:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral,#dbdbdb);color:var(--wpds-color-fg-content-neutral,#1e1e1e);padding-block:calc(var(--wpds-dimension-padding-xs, 4px) - var(--wpds-border-width-xs, 1px));padding-inline:calc(var(--wpds-dimension-padding-sm, 8px) - var(--wpds-border-width-xs, 1px))}}");
}
var style_default2 = { "badge": "_96e6251aad1a6136__badge", "is-high-intent": "_99f7158cb520f750__is-high-intent", "is-medium-intent": "c20ebef2365bc8b7__is-medium-intent", "is-low-intent": "_365e1626c6202e52__is-low-intent", "is-stable-intent": "_33f8198127ddf4ef__is-stable-intent", "is-informational-intent": "_04c1aca8fc449412__is-informational-intent", "is-draft-intent": "_90726e69d495ec19__is-draft-intent", "is-none-intent": "_898f4a544993bd39__is-none-intent" };
var Badge = (0, import_element9.forwardRef)(function Badge2({ intent = "none", className, ...props }, ref) {
  return /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
    Text,
    {
      ref,
      className: clsx_default(
        style_default2.badge,
        style_default2[`is-${intent}-intent`],
        className
      ),
      ...props,
      variant: "body-sm"
    }
  );
});

// packages/ui/build-module/button/button.mjs
var import_element10 = __toESM(require_element(), 1);
var import_i18n = __toESM(require_i18n(), 1);
var import_jsx_runtime12 = __toESM(require_jsx_runtime(), 1);
import { speak } from "@wordpress/a11y";
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
  registerStyle3("26d90ece4e", '@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._97b0fc33c028be1a__button,.abbb272e2ce49bd6__is-unstyled{appearance:none;padding:0}._97b0fc33c028be1a__button{--wp-ui-button-font-weight:499;--wp-ui-button-background-color:var(--wpds-color-bg-interactive-brand-strong,var(--wp-admin-theme-color,#3858e9));--wp-ui-button-background-color-active:var(--wpds-color-bg-interactive-brand-strong-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 93%,#000));--wp-ui-button-background-color-disabled:var(--wpds-color-bg-interactive-neutral-strong-disabled,#e6e6e6);--wp-ui-button-foreground-color:var(--wpds-color-fg-interactive-brand-strong,#fff);--wp-ui-button-foreground-color-active:var(--wpds-color-fg-interactive-brand-strong-active,#fff);--wp-ui-button-foreground-color-disabled:var(--wpds-color-fg-interactive-neutral-strong-disabled,#8d8d8d);--wp-ui-button-padding-inline:var(--wpds-dimension-padding-md,12px);--wp-ui-button-height:40px;--wp-ui-button-aspect-ratio:auto;--wp-ui-button-font-size:var(--wpds-typography-font-size-md,13px);--wp-ui-button-min-width:calc(4ch + var(--wp-ui-button-padding-inline)*2);--wp-ui-button-border-color:var(--wp-ui-button-background-color);--wp-ui-button-border-color-active:var(--wp-ui-button-background-color-active);--wp-ui-button-border-color-disabled:var(--wp-ui-button-background-color-disabled);--_gcd-button-font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);--_gcd-button-font-size:var(--wp-ui-button-font-size);--_gcd-button-font-weight:var(--wp-ui-button-font-weight);align-items:center;aspect-ratio:var(--wp-ui-button-aspect-ratio);background-clip:padding-box;background-color:var(--wp-ui-button-background-color);border-color:var(--wp-ui-button-border-color);border-radius:var(--wpds-border-radius-sm,2px);border-style:solid;border-width:1px;color:var(--wp-ui-button-foreground-color);cursor:var(--wpds-cursor-control,pointer);display:inline-flex;font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wp-ui-button-font-size);font-weight:var(--wp-ui-button-font-weight);gap:var(--wpds-dimension-gap-sm,8px);height:var(--wp-ui-button-height);justify-content:center;line-height:var(--wpds-typography-line-height-sm,20px);min-width:var(--wp-ui-button-min-width);padding-inline:var(--wp-ui-button-padding-inline);position:relative;text-decoration:none;@media not (prefers-reduced-motion){transition:color .1s ease-out;*{transition:opacity .1s ease-out}}&[href]{cursor:pointer}[href]{color:inherit;text-decoration:inherit}&:not([data-disabled]):is(:hover,:active,:focus){background-color:var(--wp-ui-button-background-color-active);border-color:var(--wp-ui-button-border-color-active);color:var(--wp-ui-button-foreground-color-active)}&[data-disabled]:not(._914b42f315c0e580__is-loading){background-color:var(--wp-ui-button-background-color-disabled);border-color:var(--wp-ui-button-border-color-disabled);color:var(--wp-ui-button-foreground-color-disabled);@media (forced-colors:active){border-bottom-color:GrayText;border-left-color:GrayText;border-right-color:GrayText;border-top-color:GrayText;color:GrayText}}&:before{aspect-ratio:1;border:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid;border-block-end-color:#0000;border-block-start-color:var(--wp-ui-button-foreground-color);border-inline-end-color:var(--wp-ui-button-foreground-color);border-inline-start-color:#0000;border-radius:50%;box-sizing:border-box;content:"";display:block;height:var(--wp-ui-button-font-size);inset-inline-start:50%;opacity:0;pointer-events:none;position:absolute;top:50%;transform:translate(-50%,-50%);@media not (prefers-reduced-motion){transition:opacity .1s ease-out}}}._908205475f9f2a92__is-small{--wp-ui-button-padding-inline:var(--wpds-dimension-padding-sm,8px);--wp-ui-button-height:24px}.dd460c965226cc77__is-brand{&._62d5a778b7b258ee__is-outline,&.ad0619a3217c6a5b__is-minimal{--wp-ui-button-foreground-color:var(--wpds-color-fg-interactive-brand,var(--wp-admin-theme-color,#3858e9));--wp-ui-button-foreground-color-active:var(--wpds-color-fg-interactive-brand-active,var(--wp-admin-theme-color,#3858e9));--wp-ui-button-foreground-color-disabled:var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d)}&._62d5a778b7b258ee__is-outline{--wp-ui-button-background-color:var(--wpds-color-bg-interactive-brand-weak,#0000);--wp-ui-button-background-color-active:var(--wpds-color-bg-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));--wp-ui-button-background-color-disabled:var(--wpds-color-bg-interactive-neutral-weak-disabled,#0000);--wp-ui-button-border-color:var(--wpds-color-stroke-interactive-brand,var(--wp-admin-theme-color,#3858e9));--wp-ui-button-border-color-active:var(--wpds-color-stroke-interactive-brand-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 85%,#000));--wp-ui-button-border-color-disabled:var(--wpds-color-stroke-interactive-neutral-disabled,#dbdbdb)}&.ad0619a3217c6a5b__is-minimal{--wp-ui-button-background-color:var(--wpds-color-bg-interactive-brand-weak,#0000);--wp-ui-button-background-color-active:var(--wpds-color-bg-interactive-brand-weak-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 12%,#fff));--wp-ui-button-background-color-disabled:var(--wpds-color-bg-interactive-neutral-weak-disabled,#0000)}}.e722a8f96726aa99__is-neutral{&.b50b3358c5fb4d0b__is-solid{--wp-ui-button-background-color:var(--wpds-color-bg-interactive-neutral-strong,#2d2d2d);--wp-ui-button-background-color-active:var(--wpds-color-bg-interactive-neutral-strong-active,#1e1e1e);--wp-ui-button-background-color-disabled:var(--wpds-color-bg-interactive-neutral-strong-disabled,#e6e6e6);--wp-ui-button-foreground-color:var(--wpds-color-fg-interactive-neutral-strong,#f0f0f0);--wp-ui-button-foreground-color-active:var(--wpds-color-fg-interactive-neutral-strong-active,#f0f0f0);--wp-ui-button-foreground-color-disabled:var(--wpds-color-fg-interactive-neutral-strong-disabled,#8d8d8d)}&._62d5a778b7b258ee__is-outline,&.ad0619a3217c6a5b__is-minimal{--wp-ui-button-foreground-color:var(--wpds-color-fg-interactive-neutral,#1e1e1e);--wp-ui-button-foreground-color-active:var(--wpds-color-fg-interactive-neutral-active,#1e1e1e);--wp-ui-button-foreground-color-disabled:var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d)}&._62d5a778b7b258ee__is-outline{--wp-ui-button-background-color:var(--wpds-color-bg-interactive-neutral-weak,#0000);--wp-ui-button-background-color-active:var(--wpds-color-bg-interactive-neutral-weak-active,#ededed);--wp-ui-button-background-color-disabled:var(--wpds-color-bg-interactive-neutral-weak-disabled,#0000);--wp-ui-button-border-color:var(--wpds-color-stroke-interactive-neutral,#8d8d8d);--wp-ui-button-border-color-active:var(--wpds-color-stroke-interactive-neutral-active,#6e6e6e);--wp-ui-button-border-color-disabled:var(--wpds-color-stroke-interactive-neutral-disabled,#dbdbdb)}&.ad0619a3217c6a5b__is-minimal{--wp-ui-button-background-color:var(--wpds-color-bg-interactive-neutral-weak,#0000);--wp-ui-button-background-color-active:var(--wpds-color-bg-interactive-neutral-weak-active,#ededed);--wp-ui-button-background-color-disabled:var(--wpds-color-bg-interactive-neutral-weak-disabled,#0000)}}.abbb272e2ce49bd6__is-unstyled{background:none;border:none;min-width:unset}.cf59cf1b69629838__is-compact{--wp-ui-button-height:32px}._914b42f315c0e580__is-loading{color:#0000;&:not([data-disabled]):is(:hover,:active,:focus){color:#0000}*{opacity:0}&:before{opacity:1;transition-delay:.05s;@media not (prefers-reduced-motion){animation:_5a1d53da6f830c8d__loading-animation 1s linear infinite}}}[aria-pressed=true].ad0619a3217c6a5b__is-minimal.e722a8f96726aa99__is-neutral{--wp-ui-button-background-color:var(--wpds-color-bg-interactive-neutral-strong,#2d2d2d);--wp-ui-button-background-color-active:var(--wpds-color-bg-interactive-neutral-strong,#2d2d2d);--wp-ui-button-foreground-color:var(--wpds-color-fg-interactive-neutral-strong,#f0f0f0);--wp-ui-button-foreground-color-active:var(--wpds-color-fg-interactive-neutral-strong,#f0f0f0)}}@keyframes _5a1d53da6f830c8d__loading-animation{0%{transform:translate(-50%,-50%) rotate(0deg)}to{transform:translate(-50%,-50%) rotate(1turn)}}');
}
var style_default3 = { "button": "_97b0fc33c028be1a__button", "is-unstyled": "abbb272e2ce49bd6__is-unstyled", "is-loading": "_914b42f315c0e580__is-loading", "is-small": "_908205475f9f2a92__is-small", "is-brand": "dd460c965226cc77__is-brand", "is-outline": "_62d5a778b7b258ee__is-outline", "is-minimal": "ad0619a3217c6a5b__is-minimal", "is-neutral": "e722a8f96726aa99__is-neutral", "is-solid": "b50b3358c5fb4d0b__is-solid", "is-compact": "cf59cf1b69629838__is-compact", "loading-animation": "_5a1d53da6f830c8d__loading-animation" };
if (typeof process === "undefined" || true) {
  registerStyle3("e3ae230cea", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}");
}
var resets_default = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
if (typeof process === "undefined" || true) {
  registerStyle3("2a5ab8f3a7", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-utilities{._08e8a2e44959f892__outset-ring--focus,._970d04df7376df67__outset-ring--focus-within-except-active,.c5cb3ee4bddaa8e4__outset-ring--focus-within-visible,.cd83dfc2126a0846__outset-ring--focus-within,.d0541bc9dd9dc7b6__outset-ring--focus-visible,.e25b2bdd7aa21721__outset-ring--focus-except-active,.ecadb9e080e2dfa5__outset-ring--focus-parent-visible{@media not (prefers-reduced-motion){--_gcd-a-transition:outline 0.1s ease-out;transition:outline .1s ease-out}outline:0 solid #0000;outline-offset:1px}._08e8a2e44959f892__outset-ring--focus:focus,._970d04df7376df67__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.c5cb3ee4bddaa8e4__outset-ring--focus-within-visible:focus-within:has(:focus-visible),.cd83dfc2126a0846__outset-ring--focus-within:focus-within,.d0541bc9dd9dc7b6__outset-ring--focus-visible:focus-visible,.e25b2bdd7aa21721__outset-ring--focus-except-active:focus:not(:active),:focus-visible .ecadb9e080e2dfa5__outset-ring--focus-parent-visible{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9))}}");
}
var focus_default = { "outset-ring--focus": "_08e8a2e44959f892__outset-ring--focus", "outset-ring--focus-except-active": "e25b2bdd7aa21721__outset-ring--focus-except-active", "outset-ring--focus-visible": "d0541bc9dd9dc7b6__outset-ring--focus-visible", "outset-ring--focus-within": "cd83dfc2126a0846__outset-ring--focus-within", "outset-ring--focus-within-except-active": "_970d04df7376df67__outset-ring--focus-within-except-active", "outset-ring--focus-within-visible": "c5cb3ee4bddaa8e4__outset-ring--focus-within-visible", "outset-ring--focus-parent-visible": "ecadb9e080e2dfa5__outset-ring--focus-parent-visible" };
if (typeof process === "undefined" || true) {
  registerStyle3("1fb29d3a3c", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,#0000);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 #0000);color:var(--_gcd-input-color,var(--wpds-color-fg-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,#0000);border-color:var(--_gcd-input-border-color-disabled,#0000);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid #0000)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-fg-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-medium,499));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid #0000);transition:var(--_gcd-a-transition,none)}");
}
var global_css_defense_default2 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a" };
var Button3 = (0, import_element10.forwardRef)(
  function Button22({
    tone = "brand",
    variant = "solid",
    size: size4 = "default",
    className,
    focusableWhenDisabled = true,
    disabled: disabled2,
    loading,
    loadingAnnouncement = (0, import_i18n.__)("Loading"),
    children,
    ...props
  }, ref) {
    const mergedClassName = clsx_default(
      global_css_defense_default2.button,
      resets_default["box-sizing"],
      focus_default["outset-ring--focus-except-active"],
      variant !== "unstyled" && style_default3.button,
      style_default3[`is-${tone}`],
      style_default3[`is-${variant}`],
      style_default3[`is-${size4}`],
      loading && style_default3["is-loading"],
      className
    );
    (0, import_element10.useEffect)(() => {
      if (loading && loadingAnnouncement) {
        speak(loadingAnnouncement);
      }
    }, [loading, loadingAnnouncement]);
    return /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
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
var import_element12 = __toESM(require_element(), 1);

// packages/ui/build-module/icon/icon.mjs
var import_element11 = __toESM(require_element(), 1);
var import_primitives = __toESM(require_primitives(), 1);
var import_jsx_runtime13 = __toESM(require_jsx_runtime(), 1);
var Icon = (0, import_element11.forwardRef)(function Icon2({ icon, size: size4 = 24, ...restProps }, ref) {
  return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
    import_primitives.SVG,
    {
      ref,
      fill: "currentColor",
      ...icon.props,
      ...restProps,
      width: size4,
      height: size4
    }
  );
});

// packages/ui/build-module/button/icon.mjs
var import_jsx_runtime14 = __toESM(require_jsx_runtime(), 1);
var ButtonIcon = (0, import_element12.forwardRef)(
  function ButtonIcon2({ icon, ...props }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
      Icon,
      {
        ref,
        icon,
        viewBox: "4 4 16 16",
        size: 16,
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

// packages/icons/build-module/library/caution.mjs
var import_primitives2 = __toESM(require_primitives(), 1);
var import_jsx_runtime15 = __toESM(require_jsx_runtime(), 1);
var caution_default = /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_primitives2.Path, { fillRule: "evenodd", clipRule: "evenodd", d: "M5.5 12a6.5 6.5 0 1 0 13 0 6.5 6.5 0 0 0-13 0ZM12 4a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm-.75 12v-1.5h1.5V16h-1.5Zm0-8v5h1.5V8h-1.5Z" }) });

// packages/icons/build-module/library/close-small.mjs
var import_primitives3 = __toESM(require_primitives(), 1);
var import_jsx_runtime16 = __toESM(require_jsx_runtime(), 1);
var close_small_default = /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_primitives3.Path, { d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z" }) });

// packages/icons/build-module/library/error.mjs
var import_primitives4 = __toESM(require_primitives(), 1);
var import_jsx_runtime17 = __toESM(require_jsx_runtime(), 1);
var error_default = /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_primitives4.Path, { fillRule: "evenodd", clipRule: "evenodd", d: "M12.218 5.377a.25.25 0 0 0-.436 0l-7.29 12.96a.25.25 0 0 0 .218.373h14.58a.25.25 0 0 0 .218-.372l-7.29-12.96Zm-1.743-.735c.669-1.19 2.381-1.19 3.05 0l7.29 12.96a1.75 1.75 0 0 1-1.525 2.608H4.71a1.75 1.75 0 0 1-1.525-2.608l7.29-12.96ZM12.75 17.46h-1.5v-1.5h1.5v1.5Zm-1.5-3h1.5v-5h-1.5v5Z" }) });

// packages/icons/build-module/library/info.mjs
var import_primitives5 = __toESM(require_primitives(), 1);
var import_jsx_runtime18 = __toESM(require_jsx_runtime(), 1);
var info_default = /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(import_primitives5.Path, { fillRule: "evenodd", clipRule: "evenodd", d: "M5.5 12a6.5 6.5 0 1 0 13 0 6.5 6.5 0 0 0-13 0ZM12 4a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm.75 4v1.5h-1.5V8h1.5Zm0 8v-5h-1.5v5h1.5Z" }) });

// packages/icons/build-module/library/published.mjs
var import_primitives6 = __toESM(require_primitives(), 1);
var import_jsx_runtime19 = __toESM(require_jsx_runtime(), 1);
var published_default = /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(import_primitives6.Path, { fillRule: "evenodd", clipRule: "evenodd", d: "M12 18.5a6.5 6.5 0 1 1 0-13 6.5 6.5 0 0 1 0 13ZM4 12a8 8 0 1 1 16 0 8 8 0 0 1-16 0Zm11.53-1.47-1.06-1.06L11 12.94l-1.47-1.47-1.06 1.06L11 15.06l4.53-4.53Z" }) });

// packages/ui/build-module/utils/render-slot-with-children.mjs
var import_element13 = __toESM(require_element(), 1);
function renderSlotWithChildren(slot, defaultSlot, children) {
  return (0, import_element13.cloneElement)(slot ?? defaultSlot, { children });
}

// packages/ui/build-module/lock-unlock.mjs
var import_private_apis = __toESM(require_private_apis(), 1);
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/ui"
);

// packages/ui/build-module/stack/stack.mjs
var import_element14 = __toESM(require_element(), 1);
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
  registerStyle4("b51ff41489", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._19ce0419607e1896__stack{display:flex}}");
}
var style_default4 = { "stack": "_19ce0419607e1896__stack" };
var gapTokens = {
  xs: "var(--wpds-dimension-gap-xs, 4px)",
  sm: "var(--wpds-dimension-gap-sm, 8px)",
  md: "var(--wpds-dimension-gap-md, 12px)",
  lg: "var(--wpds-dimension-gap-lg, 16px)",
  xl: "var(--wpds-dimension-gap-xl, 24px)",
  "2xl": "var(--wpds-dimension-gap-2xl, 32px)",
  "3xl": "var(--wpds-dimension-gap-3xl, 40px)"
};
var Stack = (0, import_element14.forwardRef)(function Stack2({ direction, gap, align, justify, wrap, render, ...props }, ref) {
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
    props: mergeProps(props, { style, className: style_default4.stack })
  });
  return element;
});

// packages/ui/build-module/icon-button/icon-button.mjs
var import_element19 = __toESM(require_element(), 1);

// packages/ui/build-module/tooltip/popup.mjs
var import_element17 = __toESM(require_element(), 1);
var import_theme = __toESM(require_theme(), 1);

// packages/ui/build-module/tooltip/portal.mjs
var import_element15 = __toESM(require_element(), 1);
var import_jsx_runtime20 = __toESM(require_jsx_runtime(), 1);
var Portal = (0, import_element15.forwardRef)(
  function TooltipPortal3(props, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(index_parts_exports.Portal, { ref, ...props });
  }
);

// packages/ui/build-module/tooltip/positioner.mjs
var import_element16 = __toESM(require_element(), 1);
var import_jsx_runtime21 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle5("e3ae230cea", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}");
}
var resets_default2 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
if (typeof process === "undefined" || true) {
  registerStyle5("8293efbb49", '@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._480b748dd3510e64__positioner{z-index:var(--wp-ui-tooltip-z-index,initial)}._50096b232db7709d__popup{background-color:var(--wpds-color-bg-surface-neutral-strong,#fff);border-radius:var(--wpds-border-radius-sm,2px);box-shadow:var(--wpds-elevation-sm,0 1px 2px 0 #0000000d,0 2px 3px 0 #0000000a,0 6px 6px 0 #00000008,0 8px 8px 0 #00000005);color:var(--wpds-color-fg-content-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);line-height:1.4;padding:var(--wpds-dimension-padding-xs,4px) var(--wpds-dimension-padding-sm,8px);@media (forced-colors:active){border-bottom-color:CanvasText;border-bottom-style:solid;border-bottom-width:1px;border-left-color:CanvasText;border-left-style:solid;border-left-width:1px;border-right-color:CanvasText;border-right-style:solid;border-right-width:1px;border-top-color:CanvasText;border-top-style:solid;border-top-width:1px}}}');
}
var style_default5 = { "positioner": "_480b748dd3510e64__positioner", "popup": "_50096b232db7709d__popup" };
var Positioner = (0, import_element16.forwardRef)(
  function TooltipPositioner3({ align = "center", className, side = "top", sideOffset = 4, ...props }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
      index_parts_exports.Positioner,
      {
        ref,
        align,
        side,
        sideOffset,
        ...props,
        className: clsx_default(
          resets_default2["box-sizing"],
          style_default5.positioner,
          className
        )
      }
    );
  }
);

// packages/ui/build-module/tooltip/popup.mjs
var import_jsx_runtime22 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle6("8293efbb49", '@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._480b748dd3510e64__positioner{z-index:var(--wp-ui-tooltip-z-index,initial)}._50096b232db7709d__popup{background-color:var(--wpds-color-bg-surface-neutral-strong,#fff);border-radius:var(--wpds-border-radius-sm,2px);box-shadow:var(--wpds-elevation-sm,0 1px 2px 0 #0000000d,0 2px 3px 0 #0000000a,0 6px 6px 0 #00000008,0 8px 8px 0 #00000005);color:var(--wpds-color-fg-content-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);line-height:1.4;padding:var(--wpds-dimension-padding-xs,4px) var(--wpds-dimension-padding-sm,8px);@media (forced-colors:active){border-bottom-color:CanvasText;border-bottom-style:solid;border-bottom-width:1px;border-left-color:CanvasText;border-left-style:solid;border-left-width:1px;border-right-color:CanvasText;border-right-style:solid;border-right-width:1px;border-top-color:CanvasText;border-top-style:solid;border-top-width:1px}}}');
}
var style_default6 = { "positioner": "_480b748dd3510e64__positioner", "popup": "_50096b232db7709d__popup" };
var ThemeProvider = unlock(import_theme.privateApis).ThemeProvider;
var Popup = (0, import_element17.forwardRef)(function TooltipPopup3({ portal, positioner, children, className, ...props }, ref) {
  const popupContent = (
    /* This should ideally use whatever dark color makes sense,
     * and not be hardcoded to #1e1e1e. The solutions would be to:
     *   - review the design of the tooltip, in case we want to stop
     *     hardcoding it to a dark background
     *   - create new semantic tokens as needed (aliasing either the
     *     "inverted bg" or "perma-dark bg" private tokens) and have
     *     Tooltip.Popup use them;
     *   - remove the hardcoded `bg` setting from the `ThemeProvider`
     *     below
     */
    /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(ThemeProvider, { color: { bg: "#1e1e1e" }, children: /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
      index_parts_exports.Popup,
      {
        ref,
        className: clsx_default(style_default6.popup, className),
        ...props,
        children
      }
    ) })
  );
  const positionedPopup = renderSlotWithChildren(
    positioner,
    /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(Positioner, {}),
    popupContent
  );
  return renderSlotWithChildren(portal, /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(Portal, {}), positionedPopup);
});

// packages/ui/build-module/tooltip/trigger.mjs
var import_element18 = __toESM(require_element(), 1);
var import_jsx_runtime23 = __toESM(require_jsx_runtime(), 1);
var Trigger = (0, import_element18.forwardRef)(
  function TooltipTrigger3(props, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(index_parts_exports.Trigger, { ref, ...props });
  }
);

// packages/ui/build-module/tooltip/root.mjs
var import_jsx_runtime24 = __toESM(require_jsx_runtime(), 1);
function Root(props) {
  return /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(index_parts_exports.Root, { ...props });
}

// packages/ui/build-module/tooltip/provider.mjs
var import_jsx_runtime25 = __toESM(require_jsx_runtime(), 1);
function Provider({ ...props }) {
  return /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(index_parts_exports.Provider, { ...props });
}

// packages/ui/build-module/icon-button/icon-button.mjs
var import_jsx_runtime26 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle7("358a2a646a", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-compositions{._28cfdc260e755391__icon-button{--wp-ui-button-aspect-ratio:1;--wp-ui-button-padding-inline:0;--wp-ui-button-min-width:unset}.f1c70d719989a85a__icon{margin:-1px}}");
}
var style_default7 = { "icon-button": "_28cfdc260e755391__icon-button", "icon": "f1c70d719989a85a__icon" };
var IconButton = (0, import_element19.forwardRef)(
  function IconButton2({
    label,
    className,
    // Prevent accidental forwarding of `children`
    children: _children,
    disabled: disabled2,
    focusableWhenDisabled,
    icon,
    size: size4,
    shortcut,
    positioner,
    ...restProps
  }, ref) {
    const classes = clsx_default(style_default7["icon-button"], className);
    return /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(Provider, { delay: 0, children: /* @__PURE__ */ (0, import_jsx_runtime26.jsxs)(Root, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
        Trigger,
        {
          ref,
          disabled: disabled2 && !focusableWhenDisabled,
          render: /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
            Button4,
            {
              ...restProps,
              size: size4,
              "aria-label": label,
              "aria-keyshortcuts": shortcut?.ariaKeyShortcut,
              disabled: disabled2,
              focusableWhenDisabled
            }
          ),
          className: classes,
          children: /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
            Icon,
            {
              icon,
              size: 24,
              className: style_default7.icon
            }
          )
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime26.jsxs)(Popup, { positioner, children: [
        label,
        shortcut && /* @__PURE__ */ (0, import_jsx_runtime26.jsxs)(import_jsx_runtime26.Fragment, { children: [
          " ",
          /* @__PURE__ */ (0, import_jsx_runtime26.jsx)("span", { "aria-hidden": "true", children: shortcut.displayShortcut })
        ] })
      ] })
    ] }) });
  }
);

// packages/ui/build-module/link/link.mjs
var import_element20 = __toESM(require_element(), 1);
var import_i18n2 = __toESM(require_i18n(), 1);
var import_jsx_runtime27 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle8("e3ae230cea", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}");
}
var resets_default3 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
if (typeof process === "undefined" || true) {
  registerStyle8("2a5ab8f3a7", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-utilities{._08e8a2e44959f892__outset-ring--focus,._970d04df7376df67__outset-ring--focus-within-except-active,.c5cb3ee4bddaa8e4__outset-ring--focus-within-visible,.cd83dfc2126a0846__outset-ring--focus-within,.d0541bc9dd9dc7b6__outset-ring--focus-visible,.e25b2bdd7aa21721__outset-ring--focus-except-active,.ecadb9e080e2dfa5__outset-ring--focus-parent-visible{@media not (prefers-reduced-motion){--_gcd-a-transition:outline 0.1s ease-out;transition:outline .1s ease-out}outline:0 solid #0000;outline-offset:1px}._08e8a2e44959f892__outset-ring--focus:focus,._970d04df7376df67__outset-ring--focus-within-except-active:focus-within:not(:has(:active)),.c5cb3ee4bddaa8e4__outset-ring--focus-within-visible:focus-within:has(:focus-visible),.cd83dfc2126a0846__outset-ring--focus-within:focus-within,.d0541bc9dd9dc7b6__outset-ring--focus-visible:focus-visible,.e25b2bdd7aa21721__outset-ring--focus-except-active:focus:not(:active),:focus-visible .ecadb9e080e2dfa5__outset-ring--focus-parent-visible{--_gcd-a-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));--_gcd-div-outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9));outline:var(--wpds-border-width-focus,var(--wp-admin-border-width-focus,2px)) solid var(--wpds-color-stroke-focus-brand,var(--wp-admin-theme-color,#3858e9))}}");
}
var focus_default2 = { "outset-ring--focus": "_08e8a2e44959f892__outset-ring--focus", "outset-ring--focus-except-active": "e25b2bdd7aa21721__outset-ring--focus-except-active", "outset-ring--focus-visible": "d0541bc9dd9dc7b6__outset-ring--focus-visible", "outset-ring--focus-within": "cd83dfc2126a0846__outset-ring--focus-within", "outset-ring--focus-within-except-active": "_970d04df7376df67__outset-ring--focus-within-except-active", "outset-ring--focus-within-visible": "c5cb3ee4bddaa8e4__outset-ring--focus-within-visible", "outset-ring--focus-parent-visible": "ecadb9e080e2dfa5__outset-ring--focus-parent-visible" };
if (typeof process === "undefined" || true) {
  registerStyle8("90a23568f8", '@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{.d4250949359b05ce__link{text-decoration-thickness:from-font;text-underline-offset:.2em}.c6055659b8e2cd2c__is-brand,.c6055659b8e2cd2c__is-brand:visited{--_gcd-a-color:var(--wpds-color-fg-interactive-brand,var(--wp-admin-theme-color,#3858e9));color:var(--wpds-color-fg-interactive-brand,var(--wp-admin-theme-color,#3858e9))}.c6055659b8e2cd2c__is-brand:active,.c6055659b8e2cd2c__is-brand:hover{--_gcd-a-color:var(--wpds-color-fg-interactive-brand-active,var(--wp-admin-theme-color,#3858e9));color:var(--wpds-color-fg-interactive-brand-active,var(--wp-admin-theme-color,#3858e9))}._92e0dfcaeee15b88__is-neutral,._92e0dfcaeee15b88__is-neutral:visited{--_gcd-a-color:var(--wpds-color-fg-interactive-neutral,#1e1e1e);color:var(--wpds-color-fg-interactive-neutral,#1e1e1e);text-decoration-color:var(--wpds-color-stroke-interactive-neutral,#8d8d8d)}._92e0dfcaeee15b88__is-neutral:active,._92e0dfcaeee15b88__is-neutral:hover{--_gcd-a-color:var(--wpds-color-fg-interactive-neutral-active,#1e1e1e);color:var(--wpds-color-fg-interactive-neutral-active,#1e1e1e)}.cf122a9bf1035d42__is-unstyled{--_gcd-a-color:inherit;color:inherit;text-decoration:none}._0cb411afac4c86c7__link-icon{display:inline-block;font-weight:var(--wpds-typography-font-weight-regular,400);line-height:1;margin-inline-start:var(--wpds-dimension-padding-xs,4px);text-decoration:none}._0cb411afac4c86c7__link-icon:after{content:"\\2197"}._0cb411afac4c86c7__link-icon:dir(rtl):after{content:"\\2196"}}');
}
var style_default8 = { "link": "d4250949359b05ce__link", "is-brand": "c6055659b8e2cd2c__is-brand", "is-neutral": "_92e0dfcaeee15b88__is-neutral", "is-unstyled": "cf122a9bf1035d42__is-unstyled", "link-icon": "_0cb411afac4c86c7__link-icon" };
if (typeof process === "undefined" || true) {
  registerStyle8("1fb29d3a3c", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,#0000);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 #0000);color:var(--_gcd-input-color,var(--wpds-color-fg-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,#0000);border-color:var(--_gcd-input-border-color-disabled,#0000);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-fg-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid #0000)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-fg-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-medium,499));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid #0000);transition:var(--_gcd-a-transition,none)}");
}
var global_css_defense_default3 = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a" };
var Link = (0, import_element20.forwardRef)(function Link2({
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
        global_css_defense_default3.a,
        resets_default3["box-sizing"],
        focus_default2["outset-ring--focus"],
        variant !== "unstyled" && style_default8.link,
        variant !== "unstyled" && style_default8[`is-${tone}`],
        variant === "unstyled" && style_default8["is-unstyled"],
        className
      ),
      target: openInNewTab ? "_blank" : void 0,
      children: /* @__PURE__ */ (0, import_jsx_runtime27.jsxs)(import_jsx_runtime27.Fragment, { children: [
        children,
        openInNewTab && /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(
          "span",
          {
            className: style_default8["link-icon"],
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

// packages/ui/build-module/notice/index.mjs
var notice_exports = {};
__export(notice_exports, {
  ActionButton: () => ActionButton,
  ActionLink: () => ActionLink,
  Actions: () => Actions,
  CloseIcon: () => CloseIcon,
  Description: () => Description,
  Root: () => Root2,
  Title: () => Title
});

// packages/ui/build-module/notice/root.mjs
var import_element21 = __toESM(require_element(), 1);
import { speak as speak2 } from "@wordpress/a11y";
var import_jsx_runtime28 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle9("e3ae230cea", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}");
}
var resets_default4 = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
if (typeof process === "undefined" || true) {
  registerStyle9("60dd1d4d42", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._4145abab73d17514__notice{--icon-height:24px;--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-bg-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-fg-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._1904b570a89bb815__description{text-wrap:pretty}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#9fbcdc);--wp-ui-notice-text-color:var(--wpds-color-fg-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#d0b481);--wp-ui-notice-text-color:var(--wpds-color-fg-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-warning-weak,#926300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#8ac894);--wp-ui-notice-text-color:var(--wpds-color-fg-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#daa39b);--wp-ui-notice-text-color:var(--wpds-color-fg-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer wp-ui-compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,#0000 50%,var(--wpds-color-bg-interactive-neutral-weak-active,#ededed))}}");
}
var style_default9 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
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
    return (0, import_element21.renderToString)(message);
  } catch {
    return void 0;
  }
}
function useSpokenMessage(message, politeness) {
  const spokenMessage = safeRenderToString(message);
  (0, import_element21.useEffect)(() => {
    if (spokenMessage) {
      speak2(spokenMessage, politeness);
    }
  }, [spokenMessage, politeness]);
}
var Root2 = (0, import_element21.forwardRef)(function Notice({
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
    style_default9.notice,
    style_default9[`is-${intent}`],
    resets_default4["box-sizing"]
  );
  const element = useRender({
    defaultTagName: "div",
    render,
    ref,
    props: mergeProps(
      {
        className: mergedClassName,
        children: /* @__PURE__ */ (0, import_jsx_runtime28.jsxs)(import_jsx_runtime28.Fragment, { children: [
          children,
          iconElement && /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
            Icon,
            {
              className: style_default9.icon,
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
var import_element22 = __toESM(require_element(), 1);
var import_jsx_runtime29 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle10("60dd1d4d42", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._4145abab73d17514__notice{--icon-height:24px;--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-bg-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-fg-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._1904b570a89bb815__description{text-wrap:pretty}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#9fbcdc);--wp-ui-notice-text-color:var(--wpds-color-fg-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#d0b481);--wp-ui-notice-text-color:var(--wpds-color-fg-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-warning-weak,#926300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#8ac894);--wp-ui-notice-text-color:var(--wpds-color-fg-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#daa39b);--wp-ui-notice-text-color:var(--wpds-color-fg-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer wp-ui-compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,#0000 50%,var(--wpds-color-bg-interactive-neutral-weak-active,#ededed))}}");
}
var style_default10 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
var Title = (0, import_element22.forwardRef)(
  function NoticeTitle({ className, ...props }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
      Text,
      {
        ref,
        variant: "heading-md",
        className: clsx_default(style_default10.title, className),
        ...props
      }
    );
  }
);

// packages/ui/build-module/notice/description.mjs
var import_element23 = __toESM(require_element(), 1);
var import_jsx_runtime30 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle11("60dd1d4d42", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._4145abab73d17514__notice{--icon-height:24px;--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-bg-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-fg-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._1904b570a89bb815__description{text-wrap:pretty}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#9fbcdc);--wp-ui-notice-text-color:var(--wpds-color-fg-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#d0b481);--wp-ui-notice-text-color:var(--wpds-color-fg-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-warning-weak,#926300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#8ac894);--wp-ui-notice-text-color:var(--wpds-color-fg-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#daa39b);--wp-ui-notice-text-color:var(--wpds-color-fg-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer wp-ui-compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,#0000 50%,var(--wpds-color-bg-interactive-neutral-weak-active,#ededed))}}");
}
var style_default11 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
var Description = (0, import_element23.forwardRef)(
  function NoticeDescription({ className, ...props }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
      Text,
      {
        ref,
        variant: "body-md",
        className: clsx_default(style_default11.description, className),
        ...props
      }
    );
  }
);

// packages/ui/build-module/notice/actions.mjs
var import_element24 = __toESM(require_element(), 1);
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
  registerStyle12("60dd1d4d42", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._4145abab73d17514__notice{--icon-height:24px;--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-bg-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-fg-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._1904b570a89bb815__description{text-wrap:pretty}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#9fbcdc);--wp-ui-notice-text-color:var(--wpds-color-fg-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#d0b481);--wp-ui-notice-text-color:var(--wpds-color-fg-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-warning-weak,#926300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#8ac894);--wp-ui-notice-text-color:var(--wpds-color-fg-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#daa39b);--wp-ui-notice-text-color:var(--wpds-color-fg-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer wp-ui-compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,#0000 50%,var(--wpds-color-bg-interactive-neutral-weak-active,#ededed))}}");
}
var style_default12 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
var Actions = (0, import_element24.forwardRef)(
  function NoticeActions({ render, ...props }, ref) {
    const element = useRender({
      defaultTagName: "div",
      render,
      ref,
      props: mergeProps(
        {
          className: style_default12.actions
        },
        props
      )
    });
    return element;
  }
);

// packages/ui/build-module/notice/close-icon.mjs
var import_element25 = __toESM(require_element(), 1);
var import_i18n3 = __toESM(require_i18n(), 1);
var import_jsx_runtime31 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle13("60dd1d4d42", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._4145abab73d17514__notice{--icon-height:24px;--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-bg-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-fg-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._1904b570a89bb815__description{text-wrap:pretty}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#9fbcdc);--wp-ui-notice-text-color:var(--wpds-color-fg-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#d0b481);--wp-ui-notice-text-color:var(--wpds-color-fg-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-warning-weak,#926300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#8ac894);--wp-ui-notice-text-color:var(--wpds-color-fg-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#daa39b);--wp-ui-notice-text-color:var(--wpds-color-fg-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer wp-ui-compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,#0000 50%,var(--wpds-color-bg-interactive-neutral-weak-active,#ededed))}}");
}
var style_default13 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
var CloseIcon = (0, import_element25.forwardRef)(
  function NoticeCloseIcon({ className, icon = close_small_default, label = (0, import_i18n3.__)("Dismiss"), ...props }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
      IconButton,
      {
        ...props,
        ref,
        className: clsx_default(style_default13["close-icon"], className),
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
var import_element26 = __toESM(require_element(), 1);
var import_jsx_runtime32 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle14("60dd1d4d42", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._4145abab73d17514__notice{--icon-height:24px;--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-bg-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-fg-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._1904b570a89bb815__description{text-wrap:pretty}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#9fbcdc);--wp-ui-notice-text-color:var(--wpds-color-fg-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#d0b481);--wp-ui-notice-text-color:var(--wpds-color-fg-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-warning-weak,#926300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#8ac894);--wp-ui-notice-text-color:var(--wpds-color-fg-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#daa39b);--wp-ui-notice-text-color:var(--wpds-color-fg-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer wp-ui-compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,#0000 50%,var(--wpds-color-bg-interactive-neutral-weak-active,#ededed))}}");
}
var style_default14 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
var ActionButton = (0, import_element26.forwardRef)(
  function NoticeActionButton({ className, loading, loadingAnnouncement, variant, ...props }, ref) {
    const loadingProps = loading !== void 0 ? { loading, loadingAnnouncement: loadingAnnouncement ?? "" } : {};
    return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(
      Button4,
      {
        ...props,
        ...loadingProps,
        ref,
        size: "compact",
        tone: "neutral",
        variant,
        className: clsx_default(
          style_default14["action-button"],
          style_default14[`is-action-button-${variant}`],
          className
        )
      }
    );
  }
);

// packages/ui/build-module/notice/action-link.mjs
var import_element27 = __toESM(require_element(), 1);
var import_jsx_runtime33 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle15("60dd1d4d42", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{._4145abab73d17514__notice{--icon-height:24px;--text-vertical-padding:calc((var(--icon-height) - var(--wpds-typography-line-height-sm, 20px))/2);--wp-ui-notice-background-color:var(--wpds-color-bg-surface-neutral-weak,#f4f4f4);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-neutral,#dbdbdb);--wp-ui-notice-text-color:var(--wpds-color-fg-content-neutral,#1e1e1e);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-neutral,#1e1e1e);align-items:start;background-color:var(--wp-ui-notice-background-color);border:1px solid var(--wp-ui-notice-border-color);border-radius:var(--wpds-border-radius-lg,8px);container-type:inline-size;display:grid;grid-template-columns:auto 1fr auto;padding:var(--wpds-dimension-padding-md,12px)}.d0a25570cb528528__icon{color:var(--wp-ui-notice-decorative-icon-color);grid-column:1;grid-row:1;margin-inline-end:var(--wpds-dimension-gap-xs,4px)}._1904b570a89bb815__description,.b5397fb9d05389e3__title{color:var(--wp-ui-notice-text-color);grid-column:2;padding-block:var(--text-vertical-padding)}._1904b570a89bb815__description{text-wrap:pretty}._0a1270dcdd79c031__actions{display:flex;flex-wrap:wrap;gap:var(--wpds-dimension-gap-md,12px);grid-column:2}._4145abab73d17514__notice:has(._1904b570a89bb815__description) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions{margin-block-start:var(--wpds-dimension-gap-sm,8px)}._983740ab855c4e09__action-button{flex-shrink:0}.d329e7416d368d31__action-link{flex-shrink:0;&:not(:first-child){margin-inline-start:var(--wpds-dimension-gap-xs,4px)}&:not(:last-child){margin-inline-end:var(--wpds-dimension-gap-xs,4px)}}._487e6a5c1375f7dc__close-icon{grid-column:3;grid-row:1;margin-inline-start:var(--wpds-dimension-gap-xs,4px)}._531c140826094795__is-info{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-info-weak,#f3f9ff);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-info,#9fbcdc);--wp-ui-notice-text-color:var(--wpds-color-fg-content-info,#001b4f);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-info-weak,#006bd7)}.ae2e1004697cce95__is-warning{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-warning-weak,#fff7e1);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-warning,#d0b481);--wp-ui-notice-text-color:var(--wpds-color-fg-content-warning,#2e1900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-warning-weak,#926300)}._2e614a76af494837__is-success{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-success-weak,#ebffed);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-success,#8ac894);--wp-ui-notice-text-color:var(--wpds-color-fg-content-success,#002900);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-success-weak,#008030)}.af00331ae17a0065__is-error{--wp-ui-notice-background-color:var(--wpds-color-bg-surface-error-weak,#fff6f5);--wp-ui-notice-border-color:var(--wpds-color-stroke-surface-error,#daa39b);--wp-ui-notice-text-color:var(--wpds-color-fg-content-error,#470000);--wp-ui-notice-decorative-icon-color:var(--wpds-color-fg-content-error-weak,#cc1818)}@container (max-width: 320px){._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._0a1270dcdd79c031__actions,._4145abab73d17514__notice:has(.b5397fb9d05389e3__title) ._1904b570a89bb815__description{grid-column:1/3}}}@layer wp-ui-compositions{.d329e7416d368d31__action-link{margin-block:auto}._487e6a5c1375f7dc__close-icon,._983740ab855c4e09__action-button:is(._8ddb8fb33fbf3d38__is-action-button-outline,._77bbde495a8a0af3__is-action-button-minimal){--wp-ui-button-background-color-active:color-mix(in srgb,#0000 50%,var(--wpds-color-bg-interactive-neutral-weak-active,#ededed))}}");
}
var style_default15 = { "notice": "_4145abab73d17514__notice", "icon": "d0a25570cb528528__icon", "title": "b5397fb9d05389e3__title", "description": "_1904b570a89bb815__description", "actions": "_0a1270dcdd79c031__actions", "action-button": "_983740ab855c4e09__action-button", "action-link": "d329e7416d368d31__action-link", "close-icon": "_487e6a5c1375f7dc__close-icon", "is-info": "_531c140826094795__is-info", "is-warning": "ae2e1004697cce95__is-warning", "is-success": "_2e614a76af494837__is-success", "is-error": "af00331ae17a0065__is-error", "is-action-button-outline": "_8ddb8fb33fbf3d38__is-action-button-outline", "is-action-button-minimal": "_77bbde495a8a0af3__is-action-button-minimal" };
var ActionLink = (0, import_element27.forwardRef)(
  function NoticeActionLink({ className, render, ...props }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
      Text,
      {
        ref,
        className: clsx_default(style_default15["action-link"], className),
        ...props,
        variant: "body-md",
        render: /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(Link, { tone: "neutral", variant: "default", render })
      }
    );
  }
);

// packages/admin-ui/build-module/navigable-region/index.mjs
var import_element28 = __toESM(require_element(), 1);
var import_jsx_runtime34 = __toESM(require_jsx_runtime(), 1);
var NavigableRegion = (0, import_element28.forwardRef)(
  ({ children, className, ariaLabel, as: Tag = "div", ...props }, ref) => {
    return /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(
      Tag,
      {
        ref,
        className: clsx_default("admin-ui-navigable-region", className),
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

// packages/admin-ui/build-module/page/sidebar-toggle-slot.mjs
var import_components = __toESM(require_components(), 1);
var { Fill: SidebarToggleFill, Slot: SidebarToggleSlot } = (0, import_components.createSlotFill)("SidebarToggle");

// packages/admin-ui/build-module/page/header.mjs
var import_jsx_runtime35 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle16("aa9c241ccc", "._956b6df0898efed0__page{text-wrap:pretty;background-color:var(--wpds-color-bg-surface-neutral,#fcfcfc);color:var(--wpds-color-fg-content-neutral,#1e1e1e);display:flex;flex-flow:column;height:100%;position:relative;z-index:1}._0625b55e82a0d93d__header{background:var(--wpds-color-bg-surface-neutral-strong,#fff);border-block-end:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral-weak,#e4e4e4);inset-block-start:0;padding:var(--wpds-dimension-padding-lg,16px) var(--wpds-dimension-padding-2xl,24px);position:sticky;z-index:1}.a43c44d5ae28b2e8__header-content{min-height:calc(var(--wpds-dimension-base, 4px)*8)}.b7cb5b9daf3a3b25__header-actions{flex-shrink:0}._8113be94e7caf73c__header-title{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}._9a776c7f70996f61__header-visual{display:grid;flex-shrink:0;grid-template-columns:1fr;grid-template-rows:1fr;height:calc(var(--wpds-dimension-base, 4px)*6);width:calc(var(--wpds-dimension-base, 4px)*6);>*{grid-column:1/-1;grid-row:1/-1;max-height:100%;max-width:100%}}.d5e0920cd15d35bc__sidebar-toggle-slot:empty{display:none}._60fea2f6bf5319cd__header-subtitle{color:var(--wpds-color-fg-content-neutral-weak,#707070);padding-block-end:var(--wpds-dimension-padding-xs,4px)}.be5e57d029ec4036__content{display:flex;flex-direction:column;flex-grow:1;overflow:auto;&._128806d0b26e3a50__has-padding{padding:var(--wpds-dimension-padding-lg,16px) var(--wpds-dimension-padding-2xl,24px)}}");
}
var style_default16 = { "page": "_956b6df0898efed0__page", "header": "_0625b55e82a0d93d__header", "header-content": "a43c44d5ae28b2e8__header-content", "header-actions": "b7cb5b9daf3a3b25__header-actions", "header-title": "_8113be94e7caf73c__header-title", "header-visual": "_9a776c7f70996f61__header-visual", "sidebar-toggle-slot": "d5e0920cd15d35bc__sidebar-toggle-slot", "header-subtitle": "_60fea2f6bf5319cd__header-subtitle", "content": "be5e57d029ec4036__content", "has-padding": "_128806d0b26e3a50__has-padding" };
function Header({
  headingLevel = 1,
  breadcrumbs,
  badges,
  visual,
  title,
  subTitle,
  actions,
  showSidebarToggle = true
}) {
  const HeadingTag = `h${headingLevel}`;
  return /* @__PURE__ */ (0, import_jsx_runtime35.jsxs)(Stack, { direction: "column", className: style_default16.header, children: [
    /* @__PURE__ */ (0, import_jsx_runtime35.jsxs)(
      Stack,
      {
        className: style_default16["header-content"],
        direction: "row",
        gap: "sm",
        justify: "space-between",
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime35.jsxs)(Stack, { direction: "row", gap: "sm", align: "center", justify: "start", children: [
            showSidebarToggle && /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
              SidebarToggleSlot,
              {
                bubblesVirtually: true,
                className: style_default16["sidebar-toggle-slot"]
              }
            ),
            visual && /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
              "div",
              {
                className: style_default16["header-visual"],
                "aria-hidden": "true",
                children: visual
              }
            ),
            title && /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
              Text,
              {
                className: style_default16["header-title"],
                render: /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(HeadingTag, {}),
                variant: "heading-lg",
                children: title
              }
            ),
            breadcrumbs,
            badges
          ] }),
          actions && /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
            Stack,
            {
              align: "center",
              className: style_default16["header-actions"],
              direction: "row",
              gap: "sm",
              children: actions
            }
          )
        ]
      }
    ),
    subTitle && /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
      Text,
      {
        render: /* @__PURE__ */ (0, import_jsx_runtime35.jsx)("p", {}),
        variant: "body-md",
        className: style_default16["header-subtitle"],
        children: subTitle
      }
    )
  ] });
}

// packages/admin-ui/build-module/page/index.mjs
var import_jsx_runtime36 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle17("aa9c241ccc", "._956b6df0898efed0__page{text-wrap:pretty;background-color:var(--wpds-color-bg-surface-neutral,#fcfcfc);color:var(--wpds-color-fg-content-neutral,#1e1e1e);display:flex;flex-flow:column;height:100%;position:relative;z-index:1}._0625b55e82a0d93d__header{background:var(--wpds-color-bg-surface-neutral-strong,#fff);border-block-end:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral-weak,#e4e4e4);inset-block-start:0;padding:var(--wpds-dimension-padding-lg,16px) var(--wpds-dimension-padding-2xl,24px);position:sticky;z-index:1}.a43c44d5ae28b2e8__header-content{min-height:calc(var(--wpds-dimension-base, 4px)*8)}.b7cb5b9daf3a3b25__header-actions{flex-shrink:0}._8113be94e7caf73c__header-title{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}._9a776c7f70996f61__header-visual{display:grid;flex-shrink:0;grid-template-columns:1fr;grid-template-rows:1fr;height:calc(var(--wpds-dimension-base, 4px)*6);width:calc(var(--wpds-dimension-base, 4px)*6);>*{grid-column:1/-1;grid-row:1/-1;max-height:100%;max-width:100%}}.d5e0920cd15d35bc__sidebar-toggle-slot:empty{display:none}._60fea2f6bf5319cd__header-subtitle{color:var(--wpds-color-fg-content-neutral-weak,#707070);padding-block-end:var(--wpds-dimension-padding-xs,4px)}.be5e57d029ec4036__content{display:flex;flex-direction:column;flex-grow:1;overflow:auto;&._128806d0b26e3a50__has-padding{padding:var(--wpds-dimension-padding-lg,16px) var(--wpds-dimension-padding-2xl,24px)}}");
}
var style_default17 = { "page": "_956b6df0898efed0__page", "header": "_0625b55e82a0d93d__header", "header-content": "a43c44d5ae28b2e8__header-content", "header-actions": "b7cb5b9daf3a3b25__header-actions", "header-title": "_8113be94e7caf73c__header-title", "header-visual": "_9a776c7f70996f61__header-visual", "sidebar-toggle-slot": "d5e0920cd15d35bc__sidebar-toggle-slot", "header-subtitle": "_60fea2f6bf5319cd__header-subtitle", "content": "be5e57d029ec4036__content", "has-padding": "_128806d0b26e3a50__has-padding" };
function Page({
  headingLevel,
  breadcrumbs,
  badges,
  visual,
  title,
  subTitle,
  children,
  className,
  actions,
  ariaLabel,
  hasPadding = false,
  showSidebarToggle = true
}) {
  const classes = clsx_default(style_default17.page, className);
  const effectiveAriaLabel = ariaLabel ?? (typeof title === "string" ? title : "");
  return /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(navigable_region_default, { className: classes, ariaLabel: effectiveAriaLabel, children: [
    (title || breadcrumbs || badges || actions || visual) && /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
      Header,
      {
        headingLevel,
        breadcrumbs,
        badges,
        visual,
        title,
        subTitle,
        actions,
        showSidebarToggle
      }
    ),
    hasPadding ? /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
      "div",
      {
        className: clsx_default(
          style_default17.content,
          style_default17["has-padding"]
        ),
        children
      }
    ) : children
  ] });
}
Page.SidebarToggleFill = SidebarToggleFill;
var page_default = Page;

// routes/connectors-home/stage.tsx
var import_components4 = __toESM(require_components());
var import_data4 = __toESM(require_data());
var import_element32 = __toESM(require_element());
var import_i18n7 = __toESM(require_i18n());
var import_core_data3 = __toESM(require_core_data());
import {
  privateApis as connectorsPrivateApis2
} from "@wordpress/connectors";

// routes/connectors-home/style.scss
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='f2df357a8c']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "f2df357a8c");
  style.appendChild(document.createTextNode(".connectors-page{box-sizing:border-box;margin:0 auto;max-width:680px;padding:24px;width:100%}.connectors-page .components-item{background:#fff;border:1px solid #ddd;border-radius:8px;overflow:hidden;padding:20px;scroll-margin-top:120px}.connectors-page .connector-settings__error{color:#cc1818}.connectors-page .connector-settings .components-text-control__input{font-family:monospace;scroll-margin-top:120px}.connectors-page__file-mods-notice{margin-bottom:16px}.connectors-page--empty{align-items:center;display:flex;flex-direction:column;flex-grow:1;gap:32px;justify-content:center;text-align:center}.connectors-page .ai-plugin-callout{background:linear-gradient(90deg,#fff9,#fff9),linear-gradient(90deg,#89dcdc,#c7eb5c 46.15%,#a920c1);border-radius:8px;overflow:hidden;padding:24px;padding-inline-end:220px;position:relative}[dir=rtl] .connectors-page .ai-plugin-callout{background:linear-gradient(270deg,#fff9,#fff9),linear-gradient(270deg,#89dcdc,#c7eb5c 46.15%,#a920c1)}.connectors-page .ai-plugin-callout__content{align-items:flex-start;display:flex;flex-direction:column;gap:12px;padding-top:2px}.connectors-page .ai-plugin-callout__content p{font-size:13px;line-height:20px;margin:0}.connectors-page .ai-plugin-callout__decoration{height:248px;inset-inline-end:8px;position:absolute;top:-15px;width:248px}.connectors-page>p{color:#949494}@media (max-width:680px){.connectors-page .ai-plugin-callout{padding:12px;padding-inline-end:145px}.connectors-page .ai-plugin-callout__decoration{height:134px;inset-inline-end:4px;top:-8px;width:134px}}@media (max-width:480px){.connectors-page{padding:8px}.connectors-page .ai-plugin-callout{padding-inline-end:130px}.connectors-page .components-item{padding:12px}.connectors-page .components-item>.components-v-stack>.components-h-stack:first-child svg{height:32px;width:32px}.connectors-page .components-item>.components-v-stack>.components-h-stack:first-child>.components-h-stack:last-child{align-items:flex-end;flex-direction:column}}"));
  document.head.appendChild(style);
}

// routes/connectors-home/ai-plugin-callout.tsx
var import_components3 = __toESM(require_components());
var import_core_data2 = __toESM(require_core_data());
var import_data3 = __toESM(require_data());
var import_element31 = __toESM(require_element());
var import_i18n6 = __toESM(require_i18n());
var import_notices2 = __toESM(require_notices());
var import_url = __toESM(require_url());

// routes/connectors-home/default-connectors.tsx
var import_components2 = __toESM(require_components());
var import_element30 = __toESM(require_element());
var import_data2 = __toESM(require_data());
var import_i18n5 = __toESM(require_i18n());
import {
  __experimentalRegisterConnector as registerConnector,
  __experimentalConnectorItem as ConnectorItem,
  __experimentalDefaultConnectorSettings as DefaultConnectorSettings,
  privateApis as connectorsPrivateApis
} from "@wordpress/connectors";

// routes/lock-unlock.ts
var import_private_apis2 = __toESM(require_private_apis());
var { lock: lock2, unlock: unlock2 } = (0, import_private_apis2.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/routes"
);

// routes/connectors-home/use-connector-plugin.ts
var import_core_data = __toESM(require_core_data());
var import_data = __toESM(require_data());
var import_element29 = __toESM(require_element());
var import_i18n4 = __toESM(require_i18n());
var import_notices = __toESM(require_notices());
function useConnectorPlugin({
  file: pluginFileFromServer,
  settingName,
  connectorName,
  isInstalled,
  isActivated,
  keySource = "none",
  initialIsConnected = false
}) {
  const [isExpanded, setIsExpanded] = (0, import_element29.useState)(false);
  const [isBusy, setIsBusy] = (0, import_element29.useState)(false);
  const [connectedState, setConnectedState] = (0, import_element29.useState)(initialIsConnected);
  const [pluginStatusOverride, setPluginStatusOverride] = (0, import_element29.useState)(null);
  const pluginBasename = pluginFileFromServer?.replace(/\.php$/, "");
  const pluginSlug = pluginBasename?.includes("/") ? pluginBasename.split("/")[0] : pluginBasename;
  const {
    derivedPluginStatus,
    canManagePlugins,
    currentApiKey,
    canInstallPlugins
  } = (0, import_data.useSelect)(
    (select2) => {
      const store2 = select2(import_core_data.store);
      const siteSettings = store2.getEntityRecord("root", "site");
      const apiKey = siteSettings?.[settingName] ?? "";
      const canCreate = !!store2.canUser("create", {
        kind: "root",
        name: "plugin"
      });
      if (!pluginFileFromServer) {
        const hasLoaded = store2.hasFinishedResolution(
          "getEntityRecord",
          ["root", "site"]
        );
        return {
          derivedPluginStatus: hasLoaded ? "active" : "checking",
          canManagePlugins: void 0,
          currentApiKey: apiKey,
          canInstallPlugins: canCreate
        };
      }
      const plugin = store2.getEntityRecord(
        "root",
        "plugin",
        pluginBasename
      );
      const hasFinished = store2.hasFinishedResolution(
        "getEntityRecord",
        ["root", "plugin", pluginBasename]
      );
      if (!hasFinished) {
        return {
          derivedPluginStatus: "checking",
          canManagePlugins: void 0,
          currentApiKey: apiKey,
          canInstallPlugins: canCreate
        };
      }
      if (plugin) {
        const isPluginActive = plugin.status === "active" || plugin.status === "network-active";
        return {
          derivedPluginStatus: isPluginActive ? "active" : "inactive",
          canManagePlugins: true,
          currentApiKey: apiKey,
          canInstallPlugins: canCreate
        };
      }
      let status = "not-installed";
      if (isActivated) {
        status = "active";
      } else if (isInstalled) {
        status = "inactive";
      }
      return {
        derivedPluginStatus: status,
        canManagePlugins: false,
        currentApiKey: apiKey,
        canInstallPlugins: canCreate
      };
    },
    [pluginBasename, settingName, isInstalled, isActivated]
  );
  const pluginStatus = pluginStatusOverride ?? derivedPluginStatus;
  const canActivatePlugins = canManagePlugins;
  const isConnected = pluginStatus === "active" && connectedState || // After install/activate, if settings re-fetch reveals an existing key,
  // update connected state (mirrors what the server would report on page load).
  pluginStatusOverride === "active" && !!currentApiKey;
  const { saveEntityRecord, invalidateResolution } = (0, import_data.useDispatch)(import_core_data.store);
  const { createSuccessNotice, createErrorNotice } = (0, import_data.useDispatch)(import_notices.store);
  const installPlugin = async () => {
    if (!pluginSlug) {
      return;
    }
    setIsBusy(true);
    try {
      await saveEntityRecord(
        "root",
        "plugin",
        { slug: pluginSlug, status: "active" },
        { throwOnError: true }
      );
      setPluginStatusOverride("active");
      invalidateResolution("getEntityRecord", ["root", "site"]);
      setIsExpanded(true);
      createSuccessNotice(
        (0, import_i18n4.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n4.__)("Plugin for %s installed and activated successfully."),
          connectorName
        ),
        {
          id: "connector-plugin-install-success",
          type: "snackbar"
        }
      );
    } catch {
      createErrorNotice(
        (0, import_i18n4.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n4.__)("Failed to install plugin for %s."),
          connectorName
        ),
        {
          id: "connector-plugin-install-error",
          type: "snackbar"
        }
      );
    } finally {
      setIsBusy(false);
    }
  };
  const activatePlugin = async () => {
    if (!pluginFileFromServer) {
      return;
    }
    setIsBusy(true);
    try {
      await saveEntityRecord(
        "root",
        "plugin",
        {
          plugin: pluginBasename,
          status: "active"
        },
        { throwOnError: true }
      );
      setPluginStatusOverride("active");
      invalidateResolution("getEntityRecord", ["root", "site"]);
      setIsExpanded(true);
      createSuccessNotice(
        (0, import_i18n4.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n4.__)("Plugin for %s activated successfully."),
          connectorName
        ),
        {
          id: "connector-plugin-activate-success",
          type: "snackbar"
        }
      );
    } catch {
      createErrorNotice(
        (0, import_i18n4.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n4.__)("Failed to activate plugin for %s."),
          connectorName
        ),
        {
          id: "connector-plugin-activate-error",
          type: "snackbar"
        }
      );
    } finally {
      setIsBusy(false);
    }
  };
  const handleButtonClick = () => {
    if (pluginStatus === "not-installed") {
      if (canInstallPlugins === false) {
        return;
      }
      installPlugin();
    } else if (pluginStatus === "inactive") {
      if (canActivatePlugins === false) {
        return;
      }
      activatePlugin();
    } else {
      setIsExpanded(!isExpanded);
    }
  };
  const getButtonLabel = () => {
    if (isBusy) {
      return pluginStatus === "not-installed" ? (0, import_i18n4.__)("Installing\u2026") : (0, import_i18n4.__)("Activating\u2026");
    }
    if (isExpanded) {
      return (0, import_i18n4.__)("Cancel");
    }
    if (isConnected) {
      return (0, import_i18n4.__)("Edit");
    }
    switch (pluginStatus) {
      case "checking":
        return (0, import_i18n4.__)("Checking\u2026");
      case "not-installed":
        return (0, import_i18n4.__)("Install");
      case "inactive":
        return (0, import_i18n4.__)("Activate");
      case "active":
        return (0, import_i18n4.__)("Set up");
    }
  };
  const saveApiKey = async (apiKey) => {
    const previousApiKey = currentApiKey;
    try {
      const updatedRecord = await saveEntityRecord(
        "root",
        "site",
        { [settingName]: apiKey },
        { throwOnError: true }
      );
      const record = updatedRecord;
      const returnedKey = record?.[settingName];
      if (apiKey && (returnedKey === previousApiKey || !returnedKey)) {
        throw new Error(
          "It was not possible to connect to the provider using this key."
        );
      }
      setConnectedState(true);
      createSuccessNotice(
        (0, import_i18n4.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n4.__)("%s connected successfully."),
          connectorName
        ),
        {
          id: "connector-connect-success",
          type: "snackbar"
        }
      );
    } catch (error2) {
      console.error("Failed to save API key:", error2);
      throw error2;
    }
  };
  const removeApiKey = async () => {
    try {
      await saveEntityRecord(
        "root",
        "site",
        { [settingName]: "" },
        { throwOnError: true }
      );
      setConnectedState(false);
      createSuccessNotice(
        (0, import_i18n4.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n4.__)("%s disconnected."),
          connectorName
        ),
        {
          id: "connector-disconnect-success",
          type: "snackbar"
        }
      );
    } catch (error2) {
      console.error("Failed to remove API key:", error2);
      createErrorNotice(
        (0, import_i18n4.sprintf)(
          /* translators: %s: Name of the connector (e.g. "OpenAI"). */
          (0, import_i18n4.__)("Failed to disconnect %s."),
          connectorName
        ),
        {
          id: "connector-disconnect-error",
          type: "snackbar"
        }
      );
      throw error2;
    }
  };
  return {
    pluginStatus,
    canInstallPlugins,
    canActivatePlugins,
    isExpanded,
    setIsExpanded,
    isBusy,
    isConnected,
    currentApiKey,
    keySource,
    handleButtonClick,
    getButtonLabel,
    saveApiKey,
    removeApiKey
  };
}

// routes/connectors-home/logos.tsx
var OpenAILogo = () => /* @__PURE__ */ React.createElement(
  "svg",
  {
    width: "40",
    height: "40",
    viewBox: "0 0 24 24",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg",
    "aria-hidden": "true"
  },
  /* @__PURE__ */ React.createElement(
    "path",
    {
      d: "M22.2819 9.8211a5.9847 5.9847 0 0 0-.5157-4.9108 6.0462 6.0462 0 0 0-6.5098-2.9A6.0651 6.0651 0 0 0 4.9807 4.1818a5.9847 5.9847 0 0 0-3.9977 2.9 6.0462 6.0462 0 0 0 .7427 7.0966 5.98 5.98 0 0 0 .511 4.9107 6.051 6.051 0 0 0 6.5146 2.9001A5.9847 5.9847 0 0 0 13.2599 24a6.0557 6.0557 0 0 0 5.7718-4.2058 5.9894 5.9894 0 0 0 3.9977-2.9001 6.0557 6.0557 0 0 0-.7475-7.0729zm-9.022 12.6081a4.4755 4.4755 0 0 1-2.8764-1.0408l.1419-.0804 4.7783-2.7582a.7948.7948 0 0 0 .3927-.6813v-6.7369l2.02 1.1686a.071.071 0 0 1 .038.052v5.5826a4.504 4.504 0 0 1-4.4945 4.4944zm-9.6607-4.1254a4.4708 4.4708 0 0 1-.5346-3.0137l.142.0852 4.783 2.7582a.7712.7712 0 0 0 .7806 0l5.8428-3.3685v2.3324a.0804.0804 0 0 1-.0332.0615L9.74 19.9502a4.4992 4.4992 0 0 1-6.1408-1.6464zM2.3408 7.8956a4.485 4.485 0 0 1 2.3655-1.9728V11.6a.7664.7664 0 0 0 .3879.6765l5.8144 3.3543-2.0201 1.1685a.0757.0757 0 0 1-.071 0l-4.8303-2.7865A4.504 4.504 0 0 1 2.3408 7.872zm16.5963 3.8558L13.1038 8.364l2.0201-1.1685a.0757.0757 0 0 1 .071 0l4.8303 2.7913a4.4944 4.4944 0 0 1-.6765 8.1042v-5.6772a.79.79 0 0 0-.4043-.6813zm2.0107-3.0231l-.142-.0852-4.7735-2.7818a.7759.7759 0 0 0-.7854 0L9.409 9.2297V6.8974a.0662.0662 0 0 1 .0284-.0615l4.8303-2.7866a4.4992 4.4992 0 0 1 6.6802 4.66zM8.3065 12.863l-2.02-1.1638a.0804.0804 0 0 1-.038-.0567V6.0742a4.4992 4.4992 0 0 1 7.3757-3.4537l-.142.0805L8.704 5.459a.7948.7948 0 0 0-.3927.6813zm1.0976-2.3654l2.602-1.4998 2.6069 1.4998v2.9994l-2.5974 1.4997-2.6067-1.4997Z",
      fill: "currentColor"
    }
  )
);
var ClaudeLogo = () => /* @__PURE__ */ React.createElement(
  "svg",
  {
    width: "40",
    height: "40",
    viewBox: "0 0 32 32",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg",
    "aria-hidden": "true"
  },
  /* @__PURE__ */ React.createElement(
    "path",
    {
      d: "M6.2 21.024L12.416 17.536L12.52 17.232L12.416 17.064H12.112L11.072 17L7.52 16.904L4.44 16.776L1.456 16.616L0.704 16.456L0 15.528L0.072 15.064L0.704 14.64L1.608 14.72L3.608 14.856L6.608 15.064L8.784 15.192L12.008 15.528H12.52L12.592 15.32L12.416 15.192L12.28 15.064L9.176 12.96L5.816 10.736L4.056 9.456L3.104 8.808L2.624 8.2L2.416 6.872L3.28 5.92L4.44 6L4.736 6.08L5.912 6.984L8.424 8.928L11.704 11.344L12.184 11.744L12.376 11.608L12.4 11.512L12.184 11.152L10.4 7.928L8.496 4.648L7.648 3.288L7.424 2.472C7.344 2.136 7.288 1.856 7.288 1.512L8.272 0.176L8.816 0L10.128 0.176L10.68 0.656L11.496 2.52L12.816 5.456L14.864 9.448L15.464 10.632L15.784 11.728L15.904 12.064H16.112V11.872L16.28 9.624L16.592 6.864L16.896 3.312L17 2.312L17.496 1.112L18.48 0.464L19.248 0.832L19.88 1.736L19.792 2.32L19.416 4.76L18.68 8.584L18.2 11.144H18.48L18.8 10.824L20.096 9.104L22.272 6.384L23.232 5.304L24.352 4.112L25.072 3.544H26.432L27.432 5.032L26.984 6.568L25.584 8.344L24.424 9.848L22.76 12.088L21.72 13.88L21.816 14.024L22.064 14L25.824 13.2L27.856 12.832L30.28 12.416L31.376 12.928L31.496 13.448L31.064 14.512L28.472 15.152L25.432 15.76L20.904 16.832L20.848 16.872L20.912 16.952L22.952 17.144L23.824 17.192H25.96L29.936 17.488L30.976 18.176L31.6 19.016L31.496 19.656L29.896 20.472L27.736 19.96L22.696 18.76L20.968 18.328H20.728V18.472L22.168 19.88L24.808 22.264L28.112 25.336L28.28 26.096L27.856 26.696L27.408 26.632L24.504 24.448L23.384 23.464L20.848 21.328H20.68V21.552L21.264 22.408L24.352 27.048L24.512 28.472L24.288 28.936L23.488 29.216L22.608 29.056L20.8 26.52L18.936 23.664L17.432 21.104L17.248 21.208L16.36 30.768L15.944 31.256L14.984 31.624L14.184 31.016L13.76 30.032L14.184 28.088L14.696 25.552L15.112 23.536L15.488 21.032L15.712 20.2L15.696 20.144L15.512 20.168L13.624 22.76L10.752 26.64L8.48 29.072L7.936 29.288L6.992 28.8L7.08 27.928L7.608 27.152L10.752 23.152L12.648 20.672L13.872 19.24L13.864 19.032H13.792L5.44 24.456L3.952 24.648L3.312 24.048L3.392 23.064L3.696 22.744L6.208 21.016L6.2 21.024Z",
      fill: "#D97757"
    }
  )
);
var DefaultConnectorLogo = () => /* @__PURE__ */ React.createElement(
  "svg",
  {
    width: "40",
    height: "40",
    viewBox: "0 0 32 32",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg",
    "aria-hidden": "true"
  },
  /* @__PURE__ */ React.createElement(
    "path",
    {
      d: "M0 4C0 1.79086 1.79086 0 4 0H28C30.2091 0 32 1.79086 32 4V28C32 30.2091 30.2091 32 28 32H4C1.79086 32 0 30.2091 0 28V4Z",
      fill: "#F0F0F0"
    }
  ),
  /* @__PURE__ */ React.createElement(
    "path",
    {
      d: "M14.5 8V12H17.5V8H19V12H20.5C20.7652 12 21.0196 12.1054 21.2071 12.2929C21.3946 12.4804 21.5 12.7348 21.5 13V17L18.5 21V23C18.5 23.2652 18.3946 23.5196 18.2071 23.7071C18.0196 23.8946 17.7652 24 17.5 24H14.5C14.2348 24 13.9804 23.8946 13.7929 23.7071C13.6054 23.5196 13.5 23.2652 13.5 23V21L10.5 17V13C10.5 12.7348 10.6054 12.4804 10.7929 12.2929C10.9804 12.1054 11.2348 12 11.5 12H13V8H14.5ZM15 20.5V22.5H17V20.5L20 16.5V13.5H12V16.5L15 20.5Z",
      fill: "#949494"
    }
  )
);
var AkismetLogo = () => /* @__PURE__ */ React.createElement(
  "svg",
  {
    width: "40",
    height: "40",
    viewBox: "0 0 44 44",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg",
    "aria-hidden": "true"
  },
  /* @__PURE__ */ React.createElement("rect", { width: "44", height: "44", fill: "#357B49", rx: "6" }),
  /* @__PURE__ */ React.createElement(
    "path",
    {
      fill: "#fff",
      fillRule: "evenodd",
      d: "m29.746 28.31-6.392-16.797c-.152-.397-.305-.672-.789-.675-.673 0-1.408.611-1.746 1.316l-7.378 16.154c-.072.16-.143.311-.214.454-.5.995-1.045 1.546-2.357 1.626a.399.399 0 0 0-.16.033l-.01.004a.399.399 0 0 0-.23.392v.01c0 .054.01.106.03.155l.004.01a.416.416 0 0 0 .394.252h6.212a.417.417 0 0 0 .307-.12.416.416 0 0 0 .124-.305.398.398 0 0 0-.105-.302.399.399 0 0 0-.294-.127c-.757 0-2.197-.062-2.197-1.164.02-.318.103-.63.245-.916l1.399-3.152c.52-1.163 1.654-1.163 2.572-1.163h5.843c.023 0 .044 0 .062.003.13.014.16.081.214.242l1.534 4.07a2.857 2.857 0 0 1 .216 1.04c0 .054-.003.104-.01.153-.09.726-.831.887-1.49.887a.4.4 0 0 0-.294.127l-.007.008-.007.008a.401.401 0 0 0-.092.286v.01c0 .054.01.106.03.155l.005.01a.42.42 0 0 0 .395.252h7.011a.413.413 0 0 0 .279-.13.412.412 0 0 0 .11-.297.387.387 0 0 0-.09-.294.388.388 0 0 0-.277-.135c-1.448-.122-2.295-.643-2.847-2.08Zm-11.985-5.844 2.847-6.304c.361-.728.659-1.486.889-2.265 0-.06.03-.092.06-.092s.061.032.061.091c.02.122.045.247.073.374.197.888.584 1.878.914 2.723l.176.453 1.684 4.529a.927.927 0 0 1 .092.4.473.473 0 0 1-.009.094c-.041.202-.228.272-.602.272h-6.063c-.122 0-.184-.03-.184-.092a.36.36 0 0 1 .062-.183Zm17.107-.721c0 .786-.446 1.231-1.25 1.231-.806 0-1.125-.409-1.125-1.034 0-.786.465-1.231 1.25-1.231.785 0 1.125.427 1.125 1.034ZM9.629 23.002c.803 0 1.25-.447 1.25-1.231 0-.607-.343-1.036-1.128-1.036-.785 0-1.25.447-1.25 1.231 0 .625.325 1.036 1.128 1.036Z",
      clipRule: "evenodd"
    }
  )
);
var GeminiLogo = () => /* @__PURE__ */ React.createElement(
  "svg",
  {
    width: "40",
    height: "40",
    style: { flex: "none", lineHeight: 1 },
    viewBox: "0 0 24 24",
    xmlns: "http://www.w3.org/2000/svg",
    "aria-hidden": "true"
  },
  /* @__PURE__ */ React.createElement(
    "path",
    {
      d: "M20.616 10.835a14.147 14.147 0 01-4.45-3.001 14.111 14.111 0 01-3.678-6.452.503.503 0 00-.975 0 14.134 14.134 0 01-3.679 6.452 14.155 14.155 0 01-4.45 3.001c-.65.28-1.318.505-2.002.678a.502.502 0 000 .975c.684.172 1.35.397 2.002.677a14.147 14.147 0 014.45 3.001 14.112 14.112 0 013.679 6.453.502.502 0 00.975 0c.172-.685.397-1.351.677-2.003a14.145 14.145 0 013.001-4.45 14.113 14.113 0 016.453-3.678.503.503 0 000-.975 13.245 13.245 0 01-2.003-.678z",
      fill: "#3186FF"
    }
  ),
  /* @__PURE__ */ React.createElement(
    "path",
    {
      d: "M20.616 10.835a14.147 14.147 0 01-4.45-3.001 14.111 14.111 0 01-3.678-6.452.503.503 0 00-.975 0 14.134 14.134 0 01-3.679 6.452 14.155 14.155 0 01-4.45 3.001c-.65.28-1.318.505-2.002.678a.502.502 0 000 .975c.684.172 1.35.397 2.002.677a14.147 14.147 0 014.45 3.001 14.112 14.112 0 013.679 6.453.502.502 0 00.975 0c.172-.685.397-1.351.677-2.003a14.145 14.145 0 013.001-4.45 14.113 14.113 0 016.453-3.678.503.503 0 000-.975 13.245 13.245 0 01-2.003-.678z",
      fill: "url(#lobe-icons-gemini-fill-0)"
    }
  ),
  /* @__PURE__ */ React.createElement(
    "path",
    {
      d: "M20.616 10.835a14.147 14.147 0 01-4.45-3.001 14.111 14.111 0 01-3.678-6.452.503.503 0 00-.975 0 14.134 14.134 0 01-3.679 6.452 14.155 14.155 0 01-4.45 3.001c-.65.28-1.318.505-2.002.678a.502.502 0 000 .975c.684.172 1.35.397 2.002.677a14.147 14.147 0 014.45 3.001 14.112 14.112 0 013.679 6.453.502.502 0 00.975 0c.172-.685.397-1.351.677-2.003a14.145 14.145 0 013.001-4.45 14.113 14.113 0 016.453-3.678.503.503 0 000-.975 13.245 13.245 0 01-2.003-.678z",
      fill: "url(#lobe-icons-gemini-fill-1)"
    }
  ),
  /* @__PURE__ */ React.createElement(
    "path",
    {
      d: "M20.616 10.835a14.147 14.147 0 01-4.45-3.001 14.111 14.111 0 01-3.678-6.452.503.503 0 00-.975 0 14.134 14.134 0 01-3.679 6.452 14.155 14.155 0 01-4.45 3.001c-.65.28-1.318.505-2.002.678a.502.502 0 000 .975c.684.172 1.35.397 2.002.677a14.147 14.147 0 014.45 3.001 14.112 14.112 0 013.679 6.453.502.502 0 00.975 0c.172-.685.397-1.351.677-2.003a14.145 14.145 0 013.001-4.45 14.113 14.113 0 016.453-3.678.503.503 0 000-.975 13.245 13.245 0 01-2.003-.678z",
      fill: "url(#lobe-icons-gemini-fill-2)"
    }
  ),
  /* @__PURE__ */ React.createElement("defs", null, /* @__PURE__ */ React.createElement(
    "linearGradient",
    {
      gradientUnits: "userSpaceOnUse",
      id: "lobe-icons-gemini-fill-0",
      x1: "7",
      x2: "11",
      y1: "15.5",
      y2: "12"
    },
    /* @__PURE__ */ React.createElement("stop", { stopColor: "#08B962" }),
    /* @__PURE__ */ React.createElement("stop", { offset: "1", stopColor: "#08B962", stopOpacity: "0" })
  ), /* @__PURE__ */ React.createElement(
    "linearGradient",
    {
      gradientUnits: "userSpaceOnUse",
      id: "lobe-icons-gemini-fill-1",
      x1: "8",
      x2: "11.5",
      y1: "5.5",
      y2: "11"
    },
    /* @__PURE__ */ React.createElement("stop", { stopColor: "#F94543" }),
    /* @__PURE__ */ React.createElement("stop", { offset: "1", stopColor: "#F94543", stopOpacity: "0" })
  ), /* @__PURE__ */ React.createElement(
    "linearGradient",
    {
      gradientUnits: "userSpaceOnUse",
      id: "lobe-icons-gemini-fill-2",
      x1: "3.5",
      x2: "17.5",
      y1: "13.5",
      y2: "12"
    },
    /* @__PURE__ */ React.createElement("stop", { stopColor: "#FABC12" }),
    /* @__PURE__ */ React.createElement("stop", { offset: ".46", stopColor: "#FABC12", stopOpacity: "0" })
  ))
);

// routes/connectors-home/default-connectors.tsx
var { store: connectorsStore } = unlock2(connectorsPrivateApis);
function getConnectorScriptModuleData() {
  try {
    return JSON.parse(
      document.getElementById(
        "wp-script-module-data-options-connectors-wp-admin"
      )?.textContent ?? "{}"
    );
  } catch {
    return {};
  }
}
function getConnectorData() {
  return getConnectorScriptModuleData().connectors ?? {};
}
function getIsFileModDisabled() {
  return !!getConnectorScriptModuleData().isFileModDisabled;
}
var CONNECTOR_LOGOS = {
  google: GeminiLogo,
  openai: OpenAILogo,
  anthropic: ClaudeLogo,
  akismet: AkismetLogo
};
function getConnectorLogo(connectorId, logoUrl) {
  if (logoUrl) {
    return /* @__PURE__ */ React.createElement("img", { src: logoUrl, alt: "", width: 40, height: 40 });
  }
  const Logo = CONNECTOR_LOGOS[connectorId];
  if (Logo) {
    return /* @__PURE__ */ React.createElement(Logo, null);
  }
  return /* @__PURE__ */ React.createElement(DefaultConnectorLogo, null);
}
var ConnectedBadge = () => /* @__PURE__ */ React.createElement(
  "span",
  {
    style: {
      color: "#345b37",
      backgroundColor: "#eff8f0",
      padding: "4px 12px",
      borderRadius: "2px",
      fontSize: "13px",
      fontWeight: 500,
      whiteSpace: "nowrap"
    }
  },
  (0, import_i18n5.__)("Connected")
);
var PluginDirectoryLink = ({ slug }) => /* @__PURE__ */ React.createElement(
  Link,
  {
    href: (0, import_i18n5.sprintf)(
      /* translators: %s: plugin slug. */
      (0, import_i18n5.__)("https://wordpress.org/plugins/%s/"),
      slug
    ),
    openInNewTab: true
  },
  (0, import_i18n5.__)("Learn more")
);
var UnavailableActionBadge = () => /* @__PURE__ */ React.createElement(Badge, null, (0, import_i18n5.__)("Not available"));
function ApiKeyConnector({
  name,
  description,
  logo,
  authentication,
  plugin
}) {
  const auth = authentication?.method === "api_key" ? authentication : void 0;
  const settingName = auth?.settingName ?? "";
  const helpUrl = auth?.credentialsUrl ?? void 0;
  const pluginFile = plugin?.file?.replace(/\.php$/, "");
  const pluginSlug = pluginFile?.includes("/") ? pluginFile.split("/")[0] : pluginFile;
  let helpLabel;
  try {
    if (helpUrl) {
      helpLabel = new URL(helpUrl).hostname;
    }
  } catch {
  }
  const {
    pluginStatus,
    canInstallPlugins,
    canActivatePlugins,
    isExpanded,
    setIsExpanded,
    isBusy,
    isConnected,
    currentApiKey,
    keySource,
    handleButtonClick,
    getButtonLabel,
    saveApiKey,
    removeApiKey
  } = useConnectorPlugin({
    file: plugin?.file,
    settingName,
    connectorName: name,
    isInstalled: plugin?.isInstalled,
    isActivated: plugin?.isActivated,
    keySource: auth?.keySource,
    initialIsConnected: auth?.isConnected
  });
  const isExternallyConfigured = keySource === "env" || keySource === "constant";
  const showUnavailableBadge = pluginStatus === "not-installed" && canInstallPlugins === false || pluginStatus === "inactive" && canActivatePlugins === false;
  const showActionButton = !showUnavailableBadge;
  const actionButtonRef = (0, import_element30.useRef)(null);
  return /* @__PURE__ */ React.createElement(
    ConnectorItem,
    {
      className: pluginSlug ? `connector-item--${pluginSlug}` : void 0,
      logo,
      name,
      description,
      actionArea: /* @__PURE__ */ React.createElement(import_components2.__experimentalHStack, { spacing: 3, expanded: false }, isConnected && /* @__PURE__ */ React.createElement(ConnectedBadge, null), showUnavailableBadge && (pluginSlug ? /* @__PURE__ */ React.createElement(PluginDirectoryLink, { slug: pluginSlug }) : /* @__PURE__ */ React.createElement(UnavailableActionBadge, null)), showActionButton && /* @__PURE__ */ React.createElement(
        import_components2.Button,
        {
          ref: actionButtonRef,
          variant: isExpanded || isConnected ? "tertiary" : "secondary",
          size: "compact",
          onClick: handleButtonClick,
          disabled: pluginStatus === "checking" || isBusy,
          isBusy,
          accessibleWhenDisabled: true
        },
        getButtonLabel()
      ))
    },
    isExpanded && pluginStatus === "active" && /* @__PURE__ */ React.createElement(
      DefaultConnectorSettings,
      {
        key: isConnected ? "connected" : "setup",
        initialValue: isExternallyConfigured ? "\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022" : currentApiKey,
        helpUrl,
        helpLabel,
        readOnly: isConnected || isExternallyConfigured,
        keySource,
        onRemove: isExternallyConfigured ? void 0 : async () => {
          await removeApiKey();
          actionButtonRef.current?.focus();
        },
        onSave: async (apiKey) => {
          await saveApiKey(apiKey);
          setIsExpanded(false);
          actionButtonRef.current?.focus();
        }
      }
    )
  );
}
function registerDefaultConnectors() {
  const connectors = getConnectorData();
  const sanitize = (s) => s.replace(/[^a-z0-9-_]/gi, "-");
  for (const [connectorId, data] of Object.entries(connectors)) {
    if (connectorId === "akismet" && !data.plugin?.isInstalled) {
      continue;
    }
    const { authentication } = data;
    const connectorName = sanitize(connectorId);
    const args = {
      name: data.name,
      description: data.description,
      type: data.type,
      logo: getConnectorLogo(connectorId, data.logoUrl),
      authentication,
      plugin: data.plugin
    };
    const existing = unlock2((0, import_data2.select)(connectorsStore)).getConnector(
      connectorName
    );
    if (authentication.method === "api_key" && !existing?.render) {
      args.render = ApiKeyConnector;
    }
    registerConnector(connectorName, args);
  }
}

// routes/connectors-home/wp-logo-decoration.tsx
function WpLogoDecoration() {
  return /* @__PURE__ */ React.createElement("div", { className: "ai-plugin-callout__decoration", "aria-hidden": "true" }, /* @__PURE__ */ React.createElement(
    "svg",
    {
      viewBox: "0 0 248 248",
      xmlns: "http://www.w3.org/2000/svg",
      xmlnsXlink: "http://www.w3.org/1999/xlink",
      focusable: "false",
      style: { width: "100%", height: "100%" }
    },
    /* @__PURE__ */ React.createElement(
      "image",
      {
        href: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPgAAAD4CAYAAADB0SsLAACAAElEQVR4XuzdB7hlRZEH8D73zRBniJLDzBAEVFQMKCaCWXENa1oTYM45hwXEtOa0ZgVzWnPOBHPWVcxgzjnrGvb/O91n5s5lZnjAe4Bw6vvqO3XPPed0rO6q6urqUkYYYYQRRhhhhBFGGGGEEUYYYYQRRhhhhBFGGGGEEUYYYYQRRhhhhBFGGGGEEUYYYYQRRhhhhBFGGGGEEUYYYYQRRhhhhBFGGGGEEUYYYYQRRhhhhBFGGGGEEUYYYYQRRhhhhBFGGGGEEUYYYYQRRhhhhBFGGGGEEUYYYYQRRhhhhBFGGGGEEUYYYYQRRhhhhBFGGGGEEUaYD3RdtxY9/XuEEUb4F4aBoWdxhBFG+BeHxsxg0+BmwSXBrYPbBTfOI3Dz4NKZV0cYYYTFhMlkskGcm5tbC6f/m5mtlwavGLxacKt8+k7BY4N7B/cLHhbceTrtaRi+A2bTX7JkSY9Lly4tG220UY8jjDDCmcAsM8/iLHOfCYObtS8bPCi4ZfCIJPHw4J6lMvlVgzuslYEpmP7WbPoDc08z+MYbEwpGGGGEdcIUo24V3CG4WXB5o103R4fBtgpuFkRvm3ubBrcPXiTMuElw+66K4uhdgrt3VUx33Se4PMltF1wRREOMTmTfrNHL2/s7BLfMtzdq+dgmaW4UBt8+zH2RYHh7o+2C24fBl45MPsKFGsx8U4w8O+MO918U/FnwLsGH5f5Pcz0ueJT7YbAXB68f/HnwncGr5pmfBD/Z1Rn79OA3ggcEP5N3fpzrFYLvDqKvkaycEPxx8BbBxwd/Erxv8F6NflKeu0lL+1W5HtzS/lDKcLlcfxLm/lLw0mHq0zbZZJOf5rqPmVwZlWWEES5UQJwddNdp0RozTGPuvTb4z+B9MXbu/SPXpwbv6n7efV3wxo3+SPDQPPPP4Le6ysi/C/46ePk8813Phb5K8FPtuesnO28K/jNIbH9Wox8RfEijn5fnbtWef2fwmi1Pn096Vwz+I+X4Ucp0uTD1bzbddNN/ht5vGMC6kcFHuDCBma3prBuHCTYJg4QPJht1VQyeBKfp/YJmZeL1KnSe3TO4W967Wt6/WK475nrl4KVCbx28XJ7bv6u6tpkbbtHumdXd913PbVOqHn75UkX13RvN4LYjOs+sCBL56e/7JO1tglcJXiq4ZdK7StK+fHDLlOmg4JVCL8/9jfO/ciwp1VK/SamWela4gR5hhH9tMEPDaWNUcJsw+qeDX8l/FwsjvC2McGoQc704+OXQ1w4eG/x88NbBu6Pz7IOCN897X8w3n5ZvHZDvvCLX/8zvTlrDzDkfPDNoz+2bb94r12sEr9TyQbowaHw2ab4vaNY+JfhJ94Nme2U6KJ95WfDLwesGn9ToO86kMe88jTDC+QKGDovh4MDowR2iqxJ/idaXy3+no/Psobme0uj/yPWVjX5g8AnoPPtszIbOd94ZvFrE4q9E7315mHzO9+fL5GcGU8/K49OCBprrlZqnrwWv1OhfJs0Dg3/xO1ez/XfRQXr+xxp9y+DrG82KP8II/5owy0wzDM76fI3gdXOPZZoYfr2uOqBcIa/TjYnIl87vfwvuEbxYoy+e5/fMezcMQxONt8v1KpnFLxWcLOQ69FT+ifX7BnfuqjX9Bl1VF1juD09erhFkVb9O8Lru5//Duqrjs8QbCAwMuwUvU2r5LjqV1AgjnH9hihFW/27XywQ/GDw+nX6fMOP7g28JE+yR6+uC7wi9V/573qSKtEReDLRvXqc3n2GggNODxSD2L5aTyWzasygv68Lh/wZPDb6j1PX2Bzf634J3Dn6wXW/caA44PQzvD+mMMML5Da5Vqgj7veAV0WFCIuwBYc5eRE/HZeH+YXuOjnux0Ga/nWaZaejomHuawReLuQeYzcNsfoY8zTJ4A5z5pVLF8lsF39zoBwSf0eint9/o99TXRhjhfA6YNJfb5nqjdPrtgrcOI9wiuG3wdsE7BTmi3Cx4Z88Ht+qqKMx3fPaT/4qAwc3OdwnuFbx68K7BSwYvGzyyVMs9l1m0/0cY4XwFNwy+vNROzGqMfmhwn+DzgkeHWVeEwZ87qcaqLaZnu2HGm8WzA9Oz5/R31kcPvwfw/rruLxIwuj0/eJMgAyP69qUyvDq0Hq8O0U8pdbltLVhMyWWEEQZ4bKni5f+U6jCC/mTw2o3+QddEdBgm2nl9DA7OBcY6v8BLSq2TZwYf1GgiPFEe/flSZ3T0r4LLvDSoJwNSU0YYYTHhysFHBm8QvHipS0A6KSeSxwTvE6bdNddjgo/oquPJWoys016QYT2D1+Glusdep1TJB81llpFRHd62VOebhwXvHrRpZrXOf27ZIEa48IClK3qkjRjEcmvTDGkHBh9VqmWYkQx9ZH1l3TDfWXp9IvN63t++VAeSm5Wa12NKzQtbgBmSpIFhMMvjSl2qunWjDyiV4dCHBA8uleGIzxcpVRrxzFowSCDrgvXdnwdcotR8367U/KI5+rBNPC7fPSYMbpPL7cLYd2uOQz3DjzDCOQEd3Hout84TShUd6YZ0xVkR/Yv1lTUwzNrrYc6FgP2Dny1VzLXe3KsEwcsFf9doS1XfCP6j1AHJ0pT79ogTmf8ePK7UwcH915VqACNGW85aLyxg2ejhsyL6T7vq6deXKcy8f5j6pE033fSrYfCLmskHS/4II5xdGLZW8qUmSjIKEcuJmK8olUno2oOBqIeh029otlsgMIPfo1T1gD/5k4P/Fdyl1PVlTLqy1CWp/y6Vce8QfG6pUsi/l2oUNFsLAoE2w29bahkNGgsK6xkQBkMlMZ2R7YTgf3V1m+szU4dPCYPvFLxjGPu+uVqVWMueMcII8wE9Rae3rLOqVLdLsxjGMCuarV0XC8xe0iOyWlIygPB00+mJ4oeWyryYlHcYRqS33jCdfMtcbx68aegt3CuV8bcO8nO/TakDAi+62wQNXNJwn5pBD3bfrOm/I0stP3FfntSLAU+euLDauOKZO4R23wBoVcHz5xiadGD14XbB/5irnnP/Eea+Q64XsfyY+3fLM7u1Z2c/McIIZxAzWb6+XqpYiFne1ej7lDq7oc1+iwU8vqTB+8tMTHx+TqmztPtvK1XMJm5/IXi1dv/npTJrL86mPGbe37bf3Ea/1u7Tud/X7hOLbXRBm+3/s9FmUrow+pRSZ3fpnV6qtOL+H/MtA92QnkHoR+05g9Q5htYuBp4+jTD0AWHovzVaOKre9z3X6w9tODL5CGvBdMeY6hyDm6XObI80mrhqVqe/mqV6GN5xXSBRnGHs7aXq/WbEt5bKbGZutIGG3v2WUq31DID2dxt0VgRfXarqwAf8Bf5L3jxjR5fBgbOJMtnNZnAgCbjPsGbGRyufVQK0lQLSBPppeWfP4JuDx3d1nf9Vwf9xv9SBSB4x5VmGaVF7qk12Db43/705TL1H8JXBtwf3zr1nd3W/uu2x62rHES7MMHSorsYvu2ZXN3YQec1GGMymiUuXKgqvnHp1IQDD0uUFRJSeWVl6VAGDCesxhmWxX1mqVZuOjJHAwaUyHkeQQ4IHdjViKoOaAA8bdzXo4qHBTXOPjzy3WKK7TSzKy6Nuj/y+VlfFXGkS5cVss3nEzM1X3juet1dc+KerT+r+cOGiRHs5LLh5V7eV+haVgE6tTMR1hsobBQ0O82bA4VntBMPUS+fqJhcbXOxLt/nlepO60UVZb9xVnX3eaYxwAQVLLHPNvzsdxM6pX5Qq7mE8Vmli4U1LnbnQROaFAmrAD0r9rln0w6WKtjdP+sN20fsHe2eaXPlv03s9/4ZSBwD3P93Vvdfu/zC0wQj9f6EvGSS29+JzcPANx8CD2kEyMPN7hnhu7RnNsv4f7ZkPlDVWbdtFe7E8dfbb1N2l+dn7PVe3wH6/vW8g+kR7h5pjpQH98K4xrHqfdlxZH3h+aKfgdhtvvHGfXt65Qn6fjs73rpPnTkbnehfvDOmMcCEEwQEHRwmdKx3FTETU+2yudNeXlbrkpaNaEvvfUg1ZPeg8CwBvDH66VJH52UEhlQ4OPjJ5+EyuDGW37yoT37OreqZnHt3VKC1ooZVEakG/MbgyeFLwg/nGykkNKuG/iwZPaGl4l3X60/nfrHz/dp//PMOZ9B7c1YHuU8Gnhr6U+6FfkXeIxp9Knb07uGrp0qUnpQ4/HnqvoDzIuy2vVht865Cg0FPu3xbTDQw+MPlArw+mvNe2Tpt9IvjV/L543nlTvvXloNmbuP7ZIGPj6ll/ZPILGWDuhkvSUQ5Ip7l0OstmwUunMxwYXJYOQty0AYQ/pB6i982bq3WwBt4jRhO7fcsVQ6PttSY2C9dEdL3spEZS3SV4mUn1Xb9IkGHJdVlQHncKinluhqYDA9FULhrsgqLFQExz0eAlQ4sRtYf352pYpV2DB4QWqdWGGOltHbRP/TK5L4KqjS/S3j3oHXqu0FG+JVSUaC6CTeyfOrxUrsRn4aSUw7sGgsvP1b3vK3O9Yq7b57rLXA0UsWve2d5MHNx72AK7Phj+D05auiLZTFp+klx38eCVJrWuVoW+wmTKPXiECwFMMTfcWbDAXP8RRr9sOk5vkU2nob/qLFfv6tLPmcLQiQbs1uiAK0oVTX2bB9mPct9vrq1f9p+OGHw/Ou/eMOkfj04HvmfwGHTuPSn/sXh7nlGr35Ka6ylBa9nob+W5SwT/lPd+h9ly7UXmSWVgszH66vmvD7o4qUtP/fbNXB8SNJujbZCxJCa9t4em16OFbbpUe/dHZtHU3e9Fqgl9qdTht9r7dOOTGn2T4Ksafc/ctzKA/q+800epyXtvxbwkqpk2KtMwMPmSNqPPrb0OztovX7cJUjHQx3rGs6Nb6wUc0glncdugkL/fTuPrqJ9KRxAa2F5tgQb76CVTzLpOGDrb0OFcW4dzNUDQrz8QevdcPx78elcNW+/r6p5xMyzR93t59xrpwE9Mxxax9DbB+yRfGOlhwZtOapTUZ4QW9PAHwf9JmvsHvx8UG22vvPP14FeCewQ/EfTcfvn/rZ4LfcXg8/PdH+a/64d+VO5/L/Sdg3d0P/eOC17LfcyS62W9G/pdue4T/G5Qfe2Z508Nnp539wmenHveF1r5da1M1879ZylHrrcLPlTeXYNHpKw/zn8vnmXu6baaBv8tmWHw1kbE9Z/k3k1yNSCqk/svnRk4RrgAwtDIraH7WVnHCb1f8OIbV3F9WToNy2wv902q6LdB2c7frZP1hwFMqhjtN9GXWKsjEkUxt2fpyHu1DrnbpIrV7u8U3DfPTZbWAwX2TX42ynULdHB5/tskKELMRZZUERW9k7LlundwBTqI6aDyuuc/z+y8tDJhF9wGnf/ncl2WK3rTliYXUOl2eeeiwe2UsTH2zgOd+6vad/du70jboEJ0ByuCF8/vTYPySZQXhVX5hJraMbhVkKFuRXAT17TFTsFNguhdg5MNMab6a4OpqK4izCrDnsHLJf0dllafdfVxEd9RLyNcgGBgQh0wjXuJzTff/J9m7jT2PhHRf77ZZpv9tnWm1eJfY9rZT60Fw8zeGPrf8x0i4at0/txnpPO/JTdWbzMf77PPBL9vBs+zH096f8x9NoA3h/5T8ned5OM5y5Ytk8c7Bh8W/L/cPy7/39ozwRcHD8tzf87994W+TPAPob8Y3DfP/yz44/x/0eBXQ/vuJfPMibmir5r7r5RGrjfJ/0+QRvDewbu3tJ+a566f//+a914XPDB5/EOuH871Yrn/x/z/jSV1ADBL/8z93Pt8/pPHKwTfpXy5d3juvaR9987BY6WXe653bvePz2/qyZ/znfekXa6inVJHn8h12RZbbNFtvfXW6xWjWhtMt/NLg38J3j/4qKaKvSB0/7/nRrgAAUbUqDphGvivaejTghj81+lg/5frqnSyszW6N0bnIPL7XF+dNPbG7KEFecDg7w5y1LDTi+eZ00VW5ven2qDAQEUH/XPwOsnDfyc/f0+HxOAP1dFz7zHBW+e5v+SZ4zG4d3PvA43BlYmovE/K83sDV+i98v63vJ//L5HfH27PXTnffk1L40Z55om+FRqD38P9XJ+W567nft55w5IaA13+PpZ63BdThj5trsaWI8b/Iv/vl3tf8E7oA0O/W37znesHj2/fvVPwuDbIPjpp3jmI2V+c3zfwbr75pm233dZ6Ouv4u8PYy5YvXz4JkjzWqvtpwOD+14bBl+d76udBwUdKI9cX+n8YvOEIFxBIZ7EMVk2uc3MXx4StI+yajrAinW8uOPvafIA12/c5yoiCyvKuE7EWO99LpyO6C6roOYcZENPRLLx763Bz9ZwxIrrnic/EcqLm8iDG2WJJtfQTf1meged3XVqtymhLVsq0dzr0UL6VS9eIz7vkmYHe1v383wU3a7SDxojsewc395x6WrJmU4e8DiK6mXsQ0VnqIXrPuWZdD+yeq7omolMPpkV0EsX2GzURXRtsWeEyO+6448X33nvvXXbdddfL7rDDDpcIbp4ZfKuUact8S/vNtsFqaGWGxH9l3Trv7L60ShS84Bw6YbVgUKVmPzHCvwJgoCngcnla8I1pUC6bX83/p6ShN9IZzNzzZe6Z73Lx/Gap2yyl4bvWpVeG/mKuzvTipfaRUoMK8u5iWf7frsZh4275rUk16L1kUq3gTi55XDrjd4O3SP7uGSQCPyj//XueOT30U5dUI9u3g68JMrJ9K9cPYrA8/6XgF/P+qlw/vFE1gBkA3pDnTltSZ+Nn5t53gtcKPiTPSO+I3Bc/ThoPDx7imfx+TtK9RPL7zeAbQhvIHNIgWuyqXD8e/EJotgXl/HresZzHcCi9w4JPaWW6VfABrUwPDDoe6bQ894S0wWG5vi//PWqPPfa4Yhj6HsH7hcF53X0k+Ml81+DWq1Hrg4HJl9aBzGD08HxXOVjUbx36By1v40x+AYHBC+ujpe7I+nPwO2lY67Z9Jzib4LA+37UFUxo2hfDDlsZfSnUeMXN7hpcZ495PvdPVo4GGZTLM8F50rg4VfBE617sG+YGjn5j/jmjPvDp4zUZ/ODh4lp0+V2dPevMfl1T9+IftfZb6YZmMiym7wLCExZEHfb9gv2yV/5/Z1fjmaM4ydpcpx6fy23KfTSynRqTm0vqtMNQ/w6DW+DkH/WVSl+747Hv/8CDfeGncJSi4hPuPDfLtVwf0496RJ3k/ZtWqVZblMOq9d9llF8clSfunKQ9j5Jnq0NrUIOC5vMsHX3qW/npvwK76tq9mcDjCvy5QrK3d2nwBdMqViG4Dy1/zALHCbK8cwNKXGbunG3PrPOhBLEdzcEG7x88bzQ+cUwk1giOLdWuWeGeCcRzhjMLxhTsoxxMqh3PGOJMs9cxcXQNnXefMgil6m8Nc3YHV52NSHV04w+wYxPREXtZnNKu/b3Ga6QNE5vtm7kG96B1r0PnuJTF0ROdNw+QXD+6PjjRk7Z1PgeOGe0eXSRWHV0yqE5FDE5SPEwqVwYrAQXnvotG7t4u+fdUVK1Zc/sADD7xYmPzKwcuvXLlyh4juB3kuuJH8tzJMVf0Zwf8NV03qkicnHSsXh6YM6kddL2l4pt8b4TwGHW8KbBSx/nx0qQz9mlJFaWAP87xh5rtmbSK3/dP2SfPTFgrJrM1Z5dhS90vbjvncvOsc7/el81APeHdx5Xx/V0X3F3XVrdQSGtfRU7p6OCAf9A8Hr593uHYST4VXvm67/6iubvrw/LPyP1fVk7vqALMy+J7gu4J2YTlL7ORJ9ez67/YckfdRk+okc1Dwjo3m4umklZO66s9tgwiV4phSB68PBZ9tFu3qmvMTdtppJ3r4HYP32X777TGQ3WZcWA18T/N+VzeBiEkn7zbz3LGrIvedgjbbeOZhGTCunPffmm89EpNnkHgk9SHfNSC+Jfi2fJc9g5eeQcsgVzYE/p/GfOMWwY+F/q/gYcHPTep5awakM5UMRjj/AP2YWPfaUiOSfC5oEwfgXXZ2AxI8s9TvCo98VKkbRGyRPLjdJ2oTYYmBHFrsDvtns6qbrf7a/jNjswt4xwaRE9t9Hb73ZAt9j+Ax6EllnKFMNm70m02CzgS3E83zorhyi3X/r6ENOj9r//ElJz6jr1LqVlO0M8AdQIA2sNiKiqZ2GCR9y8YU+8ypIP+bvBgwPfMmBrHQGOOe2223HWmEDt5b0UvdR/73rpbJAOsdwRl6T7bg40MbHOX1FfmGlQhlfUukAd6EnIPevPnmm5O+nIVOxCcJOAPt0EmVcCS/QZhibni/UvNhABS3XXrfwOCYm2i/8QbW3Ec4/4CoJWZYMw/QSUQ4PaewMohBlpfqT07/ZTwDBhJr3QBD2OChUxFT+ZzraDZ6XBldKmMbGID901xjHb3LWGXLpeN7DQR2f60Kbh/E2NxoSQC2ctKNqQqeF6WFlMAecGhXfdylZYsoK78Z2ekp6D1DH9KesWXUu3zwWRsx1+CiSxde2Wi73pyRRkQ/eKuttrrirrvuuv0222xzYKN3yEB2UBjl6mESPvXqw2YWZ6/Jsx1fVhGkTRqxxEbKYHe4VN7hQ3696N1XiEi+XdK4dvCq+eZWuX+Dhr5LbfEs0bplbcPQ1XaADJo8Aq/SBot/z/VaSWcJ5macw+AMryOc/wCT2OLI2GNWMxPesVSR/DFTz50p6AxTIAADSWDYI/7CUju+2UtABfuczdTuC2uESRjKHtlVeGHwGa2DMV4dH9SDqAwvL1Wk5wNOwpD3I7tqgcf8RFoBHGwjxYRokUZtWEH/Z1cZhq83kdggJkKLvOyY/54efEVX93nbDopemSsRXNrEezPny7o6GCjjS4M36qo7LVrwB3vR0UR74Z9Z/Y+L+CxG2n3CJA/IDG4ftjK/IPekQcp5ZX4bXH1DHg8tdQuuvDvZ5TDPBO/aBgT5eGTe78JokzAazz4edWutXw84X+jq4Opqw8vjg06aoZ5YWXhivu845ncnvdfmuiUmPwfG1xEWCXQWFlOd6IhSLb3TkUZfPzyowc8CiK7iffuaHx0kZhNlndDhPnHWMhkRlh6OMdwngpMkiIH2Z1tPdh+amb/XaDM5ewFagASMh753njEIoA0QgwWYPjqIz1SPIYQSC/0gokN68K8bjclObbTB6R2NFiCh32xSqn3hno2mdgxqALXD7I+mdhhQerUjaBecaK2nRpQ2ONlS+tOUl2QxlImILkor+m7BJzaaqC4MlRUHzE9k/k2QjrxaHx6WuzD5wNRnhbl9Z4rBDZREcqsS/WaaXG091Xf+2ZyCth2W2UY4fwFmYg3eps0g9m/r/ERYoY/6pRdwFhkc8+oMgDRgABmsMVfrqp4NMMQQqggDCsQAHLtLPJWupSczGKBCkDj0VnkT7JDoTbwXiGFFVw1iZnQMujLXo4KeZUM4MnjdUoMuouVxy1IjovquLa83KfWctM1yvWapARjdl4Y6Qa9o70qb6uEdhj+VhCYtAPk+WDnaDHh46jnkhJRxo2XLlhGfGeluGeYQQIOqwBipfujNR5Qa0UVZlMmecX4Jt/fd4KpSDZb/Lg2I0RYCWp4hEd1aOBHdPoC7B29OXE9Z7h48Knnn+LPBtfYRziXQcFOgsxNTWYOJz2Y/nX1eMPOtB5b6LQYlIuwjShWlGdDMQnaZYZojg/ZGG+69Iw8APaT9kOC9G83I86hGE+dJBAYgDh+P7apF/fBSDyIws9N7Hx+kR5tFn5ArxpQvMyDR1/LfE0pVJTDTMaWuHmB8+fBd+SUBPKarujy1wnMs4gxi8oTBfEtZ3ROmyTq8QQmn+SbGnAseE8a4X5iAzeBBwYeEKTyPQY9OfcjHkaWmh6kNFMpEmjJb9mUq1YahHAaCRYXG4NNoKfLpwQckv/sGn57yHJfrMrP+IEGMcP4BDEn0E0nE7E3cIzKfHRjEWYYsVmtiOaagV7oPzOSnlLo01u/JLtVjbWWpZ2qJAmNQcJ8YiuGI8X5jpO+jG+N+tNEGk94ppFQ1gO0AzXpvQEC/tauRUdF820kK6J93dXZEQ4OcOkAbLL7aaBLAOxttMBxEdOKyAQMtwowZGE1E37/RvNkGFeSPYY6VpZVpUh1gqB2s4kR0W2P/VuoAQT3qreilMrTVB6L6oBJQGRYdZhjcQClPXw0z9yJ66D9N6lr97IA/wvkAdEgzIlGZWPnYUkXdHs5igzHOmckcJIDJ6afE85WlDh6ilJi1b1aqYQxgjkMafUSp+QB3KFUMBZjUt4DvPLjUI5EsVwn4L71rlTWRTDEvQ9ohpZaJocxAQ7Q9OnhE/jNYuM8LjZpCYvBdAwqmVQ4zuPSk4b68eYbxTDoGE0BMx3QGB7O2+4e0/xgC6bCcQh4UvEuQRf5ewfuFVg5lFe5JXbFRGBDlj2QjbQOFEM6McaQTA5K8q8cezmI7nWUYGLyrjkXDDE5cf0zwYZMaPHLR8zHCemCm4hlwdBy6Nr0S82D0swo6M2bAHJgME5ltzMCYDHNoeLqvNC1HyQidk1gN6LFmStAvYbWOQgc3Mw/P+BYwC2N8yp48c2bBfNK7a6lMjKmJvWZEM6Q80qUxDUYk+hKH0b7rfYMIcZw+TvS19oxmwDMIodkVjipV515Zms5eaoRWxsRhSRGNCUHv3KNMYYKjgjdvjMJ2QHQ34MnPHUJTO9QBpxZ1KD15Mvv7Hiu+pUNlUr5BtZlt30WBgcmnmF0Iqwd01flG3VJ39AX1OcK5CTMdgPhMxHtUqbP3/5VqkT2rQH8cxFnr3KKqEj110t5vu5zRio4p3Gdx9s6fgu9O/uiy7ltP3SUXQQn91lmIpmidiDiLNih8pNEGDyK65+jt7AjuP6tMieilDg5oIjrmQf+srLF2QzOzMvmWwe8r7f7BZY2IznL99EYbJAYR3T2iPNqz+6BTtm91TUS3xzr0yvYMml3gh42WHhFd2qQFji5oZaB/e8fVIGpvwFtKg3ODwcEMg/d1mOtvuqkjn0u1dYxwbsJMByASnlBqJzILHF+qcaeHs9BZrEvbdMF9dM9SR+8XdHWGOSL4nK46a9DVdH4xyHX6pwZtkiAae/+2G220kW8RZ2+y2Wabmel9ywAE6NR0XrMd8dUS29aldnS2A7O2gcP6OlGWNd6aOkY0iLy41FnRLIj2be94BsOQJujP1tvNwu69sN0fysQf3uz9vFIZ1yBmADHQWAVQPmlt2t4nfbA4yff98i5ruR1mNovwY39qrpbxOLQcXeqApz4MGGgDgjI9t1SjGhVE2sqGmZTbwNLDWWizcwzSaihIhzX7Z3V1oJJX9UZiG+HchJkOoMPQ3zCljo52bz5AjLRsBOmMOpylIDT9kJhufze90TIX0Zb4fmhQZ6Zzes4sDdDyAGymsMRlhhC8QEcGmNYgAYjdOjvAsAYoTGX92gytc2EODEbPN+sTgZWPWE7cP6RUyQBtkKNDe97SFakE8xoclNWM7D6a6E+94L0m/yza7lNVzGbyMYD6AMpnw4k24F1niRBNbOdJ5xnlmy4TT7Y+amypaVg+k961Uy+WAQ0EpBZ10UP7zrkCbfZ2pX5x+uFFuE1QP2BvUIfnap5GWBuIfkQps6HZA/3K4c8zaZhBlCY6YpoflSqWY4RPt/8wGjWAGGkmZwF2n95P3PxdqbMR5hrEWaIxUfUTpTKl+57DlKzpfptdv91ozHZSo6V3QqnWZ0azYxttVh3E5zeXqk/L62fKmrPJ5J9o7D5VxYz88/bfFUq16ruP0d7e7rMk+/YfSl3CM+u6Txrx3Z+W6qSiTO6fWuoAJI3fpn4FhHCfeMs1d1A7MPegdhjQXl5qPd+u1OU5ao6ykcA888bS4EzabEFhSky3Q4/ziyW/YcWgVzvkZ8ARzn0gCr+v1NlWZz2lVFG0B423ATAbv7ehGfK1pW76wHBEZ0zH4EW8lobZkFHM0huR04yPoTGiAeLdperoBg6DwtNKnV3tp35tV4/84SpqmcvsT+TFaBj/P0vd1CFthsIPlbq8hCFOLJW5zXTK51ki9MmliuMYWV7ZHpTDdyw7Eb+HMmFQ4rLvmi2PafQhpRq/5F0dKhP6iPaO5a0HlFqm95Sa3opS6+M1XZ2Nlcc7JA6DnbTV7TGllo/IK/9vK1UkNzjRuUkdBlP3V7fZuclIUwxul5pddQyHewTfFnxXV89FW83g52beLsyACQ4uVXQ0Ew7i7/kWdIzWkfadNNG9q9Z3TAQwBEZlUf+XgNmO3zq/2d0MCHYsdZABBjRi+gCCUWwa/V15Ob9QRc4TmGLyAW3xPSRoxxoVjYplI1Afjmtk8kUAFT8FJ5YqQtGdzRjoo6cfOD+BDjF4RrE85xanEBtBvoYulSnMjsTnYTntfA+zzN06/ndKLRNj3QcbTQoa2kn5qAK/L21dPfiLUo2j5xkMzK2NchX62dbUv01qUI1hZaDfBTgy+CLATKWyMH+5VHHSYXZiod11eOasNIBGnX5vQ/R80TftRtpss836WG/Q7xYr7KPBL3KHzLOvC34haKajB3+uqwcJzhtm056+tz76nKLyDT7itlUOO68wR/5/e1fLZDMNcf7zpRoeqR10clKKdvtoWSOif7hMDdDSOC9gGISDu0W6EGPui5Ma9ea9ydNXurpuf57l78IEq4I2KnDSoL9a++Vffb6qfIyNAYLLgg5WwBiCBoqgilEweZ/vUh1MhiUZS1KcQ87X4vqwbzrX3cLgO7fdXkJBDXor67nVhtlXZ4EeP1jqz1NoDA4uAic1jJWVA3v6+yi58yjPCOcQGGX+mIo2AzyTFberYYxWzzDnNgxpD2hGa8y91bJly07OldENM/wyKOKJ0L1OwvxbV9erxR5XDgYwFnqzmqWa8wXMlg8zY/Dg1inbH9o2S2GbvxzG+EfKJpjD21uZ2EweF/x2K5/3iesMlgyJPy7VqNnDedF+AzQGd4KM3WYOMjRgCdD596C95KvrYITFA8tg9CLW2Cemsi3XPHSo+POig0x3funrKJg8M/ZyzJ3ri8IMjub5etApIAIRfiDP/zR4kVIdVZxTRh/njspbTec/38B0GYfy2UGW8n03+KOUy0z+0fwnvJI1bo4jPwuy7Bu0hItmUKOHf6vU3XbXDH6j1COKpw1daxI+F2Fg8MbcfBkwOFH9l5MaNHKtehhhAaBVJM+vqvhVcY5ex0tsYwzS1SWo86zSk64DEIij/UaFdAR7ih0kgMmdibWs6eEOL9i8ibPDe7Of8x+/8NU/p2j3h3rgAUdNWRToasaoCa7O5+UAQq3o1YuUwYClTLsGd0KnyLZakk4GJrCddObLZwBqSv/d4MZ5v4+Pdh4yuait8iAQow3v+wVFYTUoa2NRZvW786y/XSBgpoE/UKooZ1mJRfbbpXp1nWcj6kz+eND9Knl4Q1fPpf51GOCjwZXp+L8Ic38jV4f8OfnTDE63s54uXjoPMiLqV0p18aR6KCOD2y1LjQxjLdo69Q9KXYs+tFQnFmv4PSwCQ5AseKZZwuL08stSI6CKy/bLMMCpKcuKzN6nB50QuuPSenaZGZwebs1fm5FKrN0rh+/cttTjlInovPe+k+eenG8K+ywC7RMGBjcQnhcg7YZ2DX4p+ItJndGdCPvrrm4gOtf73AUKZjrsJyc1+D14RanLLBxbejgfVLSADfRN0TqJd/TsLwb3oHOn8/88nd8xPc4MswRD4uAtZ2mMOyRxVvkYDQGnFc4smMH9h5XK+H8q1Qfduqz7LNM9LEIdGGwwpPXrPj15Doqb/reUwekkznRzvpvy7tJ0cMuAq/IcBx95ZDnns8+LjwWdY5D7LOvsDPaPvzDvXHKzzTb7bN5/xsBgGP28gCkGF/DRUcnKLhjlR1s92N67GHV+4YEZBmdhZowCNnPodNP+0uc18FNn2bc8ZL+0s7oEJCS27hymdhYYevPc78/Fap1jPtNu7w/dgHOMGR+YGVcMfyxCZ5M31n1Xde4QBIEVtc2eKccuGDBlNHM7iw1D2ojSW85bfqbzPk2TDgbwzUFER+8wVT+LCUO5BpVnzR9rGNzhCMptiUwebUqxDXi6jCOcHZipPP7QXyy1Uz+50fMyQg0NsdCNMTMAyQs/7edP6trpV4Jv1Pkzg38p6GRPZ4ifmPufbe++LPip9j4jFPdPHl82h/A3HzagmOm5rFoz/kKpPtx8zr9Uqo93Dwtdvhk4MGgt2Nr9ykk1PL0/DL1tyqRsnzZw5b/Xd3UdnH2Aq652Mhg/sNQAkcrAzVc5SCeMbF/M84JaOCCBTwNpZlHabAakTW1g0GTjWQ1T6Ysg+6GWL3X+6uDXQnt3sfN3wYaZysM8xNmDy5qIoEcNf84w27kCM/nDgP8I6gx2YxHDvxmkv/6TCBt6qyARm4hnZtTJlYMkgtmtDKws1Yfbfb7ndy11YwYdlojuPvH9sFLrA/OfG3D1Ust3alfj3SnPr1LvnEL6DTShxVf/JrrUTTZsDGiDwwsbTa0ymPnWI0qtN/dfWupg5v4w6C02DPXJ9rHWLD7F4GZqnnbajLpikPLO+WYJ818WZhjIbMbohBn4N9NPOYOcX2AwSBHhWMadJ2YtmNi6D2Nbo1lgual6x6i0PjVj+v6glwMbN8yOgIPPRaf+W2wgmgptZEAlpfTlm9Rzx3rRvatbageHHRaygQaYfgCrIIMFzT57bUnhvlxZEwJrsUGbHVKqNHSGGaKVR0gqvugOjKBiqHOTzLSKMcLZgSkGV/ncU80IGuNMYWZwMBh8vNRZcrFkKjPqB0s9jocawbrtbDJLRoxNg9h5QqnqBniQ5xvN+PSkUvN3eKm70TCvvL+91DV/xio7xe5d1vivP7FU24TtnWbEM+iTCwQGWPXvLDS2hfdmsHr5pJ40QmwlmnuO+vSG9o7yEX9tpLl9qTvvMDDJZNi9pnzqiuFNGtSUJ5TK5CeVKhIvVpmk/aFS+1Y/2Mz0m9Uz+RRY3fAOKU25rOtvO/3ACPMABhuzwzCKljWbGOald88Anda73y7rGKnPLsjfFFAXpMGqTc/s0+vaIQG5/qWrMc+J1f5j3MGg1I1hJnOfSI/Rib1ESMzs/nGlLsURYQ0Qh7b7ny2V2T9W6qCwPongnMK1Si2HkE29iM57LXWw86SubviP8Y9O+7dSZ7gT3S91LzoRHH2bskYFObZUxkdTOwxyaIMxaUhdUVsWrEwzbaY+padMvT+CfscYOg1TDO5lTjnagIhOCqG6cLUdYb7QrLFrMXmpBhGVOi2uzheIiRpTJ10sWFHq7raDS3U+MQsL9i//hwSv0MphFjbqA5tMVjYamBEGmBZTzXpDJ6cHSktnE0WF2Ey0pef6dg9TnXKhwCzlQAJlSbNMRGQZyid//UaMsrZITpUY8sQ5ybZeDxF11ZNvMipeo1TVQxlFmVEW93m8abMFL0wD56SJ2qIsfX9rqx4bOnABQ+uH4sl7yKC93odHmIGhonPdMtcXB1ltHR97bNC5VcS4+XRgnYaoSEw045hBHj38OY/3zwmYhRmVGMbMagI7PK79d0ypUWGA2cv6NtBxHtpo4p9ZHLPw4/a+GQ0TiA3HUEWffX5oMdJ2L9XV9Uk6nbItdPm6qovq1Dz1xA2XtkMN3H9sWaNq3L2sOQeOtZzqQC8nlj+rVGnDIEV9odZoz+eX6rZqRlRvnHqUT5l8d5BwFgSGusnVQZD61H+mPM4151L84lyXYfJ1ONl48ehS+5KBi3RFhVjMieOCBVMMbm11EP0cunc6urT90uvqwDP3BtH2Q6WKfsSq73rMn+t6fwHB7CRt6WFKFme/zWLyQYTlcsof232zgAgnPMUES8DQ7mMQe6bRBgiMjSaKYw60LaZmRrSIoMN6clkoaN8jldgPbbC111u7/KmrEV36diqVkb9TqmhtcDq51NBOBqdXtGdYzakbaNc7Nvo1papfaCsDDKpo3npWHBYE1iWipwxfy/1D0fbrp60cfbUuJxuVOrSZfsgeoqz3W/3AAtb7BRKmGJzxRmD9+3b1ZMxbB9Gr5tmBzdqYHHMTfRlxMEwP83j/nAAPsLuU6korIZ2aPg3MZAOtExPlgdnrOo1mUfacdzGNUzgxEiYTg5xIbIC4fWgi7Rapp7vnesSkOmbMduRzDF3VUfmM2zJpFhc//KatLZRzKNPBpdo8AIPocN+gwNvPILey1DoR78xAQJKxBu6/O5XaZspNGqCzLxawjzjg8Rapr53S7+4bxr7zXN1Nti4GB1yHvUNqunap/cqgPMJ8YOicweWTesrEcZPq9qixH9vV2WM+DErPNUOY9TTGsaWuKZ8bwKr9yFI7K2OaWXhIW2dWFsAybiAAmN1sBujfdytVvF8RvE8rN2Z4YFfDBxlEHpbrrVI/26UzOg/s/sElg+1ioaExM7Q2/IiuBdlwLTW/wKw4lA8DGGTp09QOore2IJY/pNQBAJMJ2mEN3ECs3jC1Qe5RpQaBZHchqhP9DXILBeqZ2mawstR3bPAhkxqyaTWDz/Q17XVcV0MrG7z0MTaDEeYDUwxurZgoOzgYsM4SjSytzFZ6DzP37lGq+GRph4HOu0TmcwPMYn8Onl7qUpe0B8vyIM4Sy4mzRHbGtg+2+wam5zaa0Y4HGFoHN3ug2RZ6ET1l5lHWi+icaUJzhV3NjAsJwze7JqJvsskm0jeYDWUyw7N6ozHuRxutPl7Z6KPKmvPW6O53bvRry5rDHIjoyqRuflzqgOC+39NGvHMK+pJ24byjj/VqR+pw62kGnxksv9nekVciunyNIvp8YYrBzeBmb/uEdRYd/Wmlit7zqcjLl2rMYrwh3j6xTDXEIsPKUhnynmaDUo1tZi9gg8VgWDNTmckAQ83wDMMamhWa6M7zi5Wa4enoru5k4jBDohEJVISYJ0esfNSkwmIzuBn8cUHShN/aZigHBn5Eo81wZmTitkFWPSiPZbNjuxqeyiyK4ZXJ7K7eSDlmcOvh6oG1He05Us1CwT6lHYCYtHdLvT0l9XhMroJBnoHBW1nl5+ldPRCDdCi/4ww+X1CZTcTcfFLPv7pb0AYOot89usrs8+m8OtIdSnVmoK8eWapuR88jNupEay94LhwQKY8MHr506VJWZstLh7cOQmwlmgOz1LCur8P4b4CBxuQ37upMib5F6AO66jF2m9TNNUPb8XT7oCUfsyhxWV2REuZTV/OCln9IB7/dVJnMZkM5zITsB4AITj9Xz2Z9ARYx7qpS7SHKbEY20GESbXNEqYMBUfyorkbtwdRHNqTyLBRoJ2lcP3UnPNOd0veOCG48q4NPld2pMLa1Wq1Q1iPLeLzR/GGKwW3S6EU/FRnsrZddO8RPZc/CzD16ofd5SbHievfrpXY69/9Q6syyGHBoqWmcmrJYFvqlFYHQ9GaiOxFPR/92e87MNYjoJI9BRNfxLfOhzWCYAv0/Kcs1Gs2KbkZUPtFTGOD6eguuap2yLARMfUuZfP93pUpHvSpVqnGR2sFyrtN/uN1XH4OIbmClt6JJIOwU6Gkr+ifKmuOYifyMWEOZqDM9LEC5BjXny12VJnrnnbTTVtMM3vrjgPdIv3QiqXK/vtT9+UcOH/TMCBuAQTRSyXNtHXxS44hbOnpLVx075tO4vMl0qsH4Y5nmmFINOa8q9RCABVt+mQFqhEMBjt5ss82sHT84ZXhIaNFNnhx8Ycs/8dW6r56ko1sPNuiYEblPEl8tTb2ktCORu+rqiklIMi/vqrFrZfBVQaLjitw/IdeXBm3+mE9dzQumvmNwUofPKnWN+smlLu0BZZJ3UhNJ6cWlDmAGq+ODwhCz/MufM91IMS8rVbQnMr+8VH8ADKT9iNBmWvd9ay0j2wKUTdrUnj31tTD2C3IVdWetGXyKwRk4SUrKJ8/PLvN0nx6hrMXgLJmHB28yqadOWJ4RMqh35JgHGOmJuYP4ZLRmwOJ1RFS/eqneYAsKA0PpIBnpl2y77bYGqkuFuS+lXJtssgld2pKQx3VoMzYw8AwdBZObiYGOxGNM2enzB3dVTBQwwplgwz5lZ6cdPKmB+q/bVbFzwdfEwfC94dtBA/DgeUfsHjz19ih1+csL1Cz+DLZfsh8c0tXlT2eVYRrtpEzEcyqIdnb+mQGOiI/muacODBACLpytKKfD8111rVWHIqaK3KLOrpN2sq13LQYHA5MPaQZXtb+00QjzgSkGt+mfGM0yLFSORqdvir82+1oPM/dZZ1ld31GaiF7q2Vw8kNz/dWmzwfq+d3YhnaMLQ0+22GKLTXLtO0Hy/+Ctttpqd5bnUsVCuieLrN869/sbTa8bRPQjgw9uNGMOqzqaxdkA5Tsi3Qwi+g9TbxhencGVQ6dcSGj1ZVcZBu2aQxL1h41AHfuN0U9q9KF5zAzclyn0o9G5EtXv0GjhnSyVeeYjXVM1cv1OVwcP9/nzo6kG/ttfXlp+5gXD8+0dhk3qhP35vYie+7+eayL6rE86GBi8Aeck+TrCD/cXuq4vcDDF4NsE3xn8SCpNDDAdykjbHx8zDzBLnxJ8tE4RPDnIvRLDnRx8U9cCAi5Go4S5J8uWLVtKRM/3bx285zbbbLNtV10jxfUyuDjKl+5t+Uyn/0CpjEGtcN9sbaY6satGrYNzD80/QPSYk3K1u4t32Sm5vjb15UigD6RzfiDXHdc1E50TUF8Nt5vUIITD/WcG/SC2s3vQxw1KIseKiPKAVg6zNaelk9rVoK1tWOQN4gaFp3RVSnFffRlIPpi03pkr//G3hjYIrOzOAoMPz04h9eCUXJ+fOhKG6iOpr7cGxc5bHfd9Gmb6yrGlthnpYjWDL0Z/usCAztg6pdC1YlMfGrQuaYdDj7PvTAFdz95de5OHM6V0FOIeURGjs2oTeXtxeFKdGkRA3dB3zzKEubt0ELO4b18sHWWfVrZ9kqb9zzqEfBJve4Zp+UMTY9HKu2nwEkHeagI1XnpSd3ChL5dv+q5lnSvk25cJLk/nvFLSvEpw0/X4VJ8jGDrxFJK2iN3yzn9BnQ+0iKSeMSBgIuUhDotSus2keiweMKll4oVHAhnet6f+Yt5P+S6Xcvgt4unlcr1ycBP/wTOD9j3PGiy0/96h+dT7zqXn6qx91dTXQblulbY7aNNNN71K2m3TWSaf+h5PPCoE9Ul7KEM/6M0nTxdKGESjXLfL9Ve5JZjfZdc3E6lIld2ApZmY9exgL87mf4Y5M5/7RDEiOvrHrYGvkO9eKUyx8QZ2EJ1lcKpJKws9lBj31XQaA9APuhq0j+fUqf4LrRO/u9FEbwZAtGVC68vK8bigNX30K4O9FT15N+v0Inqu3056l046f2MNTnlWmIXgQkPr4JAc+ykrHl31rvtSqXk3QL0PnbxePXg8Ovk9MvTR7f7RwSPb/eNDE5nd/1De7dspND/xvfK/gxV+nzLumSuffe/sp0+sq19Mg/8nrZ8Eecb1/SJIylNvnw7ytPtnyvGj4OVb/XHm2Ws4fgpo06Hspe7T/3tX3XYPzn9PzTevOZ88XSgBY0/hNqnozwe/m4ref7g/C0PDNeBwYW8vhw9r0N9JpT8v9OVT4afl+qagGdQzAvRvlWcuke9fMrhkIRnBYNHytqLUuGTEaYa0jwRPDb1j0n9H8ndarnvk9wtz/WauJA0dX7inm+T/OyWP3871fsEbyHuuT85/B+X+6cHX5jcD3mmhT0y6pIWvpUN+LeXZxewz4ELC0Mm7GnqKmPqJSZ2l39HKIdoLpv1G8mdWfELydnquNw7ewzO53jPXGyuH/0NfKfitvPPSSW0ndfDu/LdL3hV2+vNLawDLTwYNZntiOLghwGyTNdLGEUF1/ry8d1i+89185y25Xj74ndTZx1JXjKJfDZ6Welw5zeD64NS3XhQ8Pd+6ZZA09aD8Pkh6cIQp0EiNibeAqWxB9S+TCr9Crn6vd4/uwOBd1dXo6TunggXgPyC4a3BpEBPsMVcBvV/SWaKTJI29gwvK4FMMINM80+jJfu8V1Hl1AlFJ924dgqMFsVHH2aLdnyypR+zuvaQelsABY9+g882S9aUXD64Mkj4MUvumDETKSwcPCG4eXBlUvk0XsnwDtLpXplUt78TUla1MPOz2SF67oB1aewVZqB2awFbgCtCCN3qnj0bb3ldup6XoF3ulfHu2fnDR/L5UrnPtv9lsnQGmmHKX4IHSaWleTj3mW0JaX67V25Zh6MsGD9y0wmoGB/LW8kqyIAGyF4kqe8lW5pHBp0FlwUY/JBV9P42ZSv1+dNl/pMIx+joZXKMNzFTqOvfvU7lPyndu3sSs1y+p50z9KXhKkBfSH3PPEUL75du/zHN/zvdXSmMRmIBH3U+DXyt12ei0oGCFjqj9TAvGeInk5y1BftDE2ae3PB6RZx7YynFs6FsG/xx8SX4f5n7y+0EdM/iX0KemPPvnm79L3f3cTJTrt4Oeu/z66vAcAss5C7pjlwygn25lInW9I/in4NWT7vPb+WW3CT5cWXN9WO7ftpWvn1GDyk0KMHj9Kfc/l+suy5cv/2vK8ZP83jHX70nDwKw8Z1YmzDbF4HcNSuO1+e5123c+HLxy8K/Bb2DuzN6/Sr7+EHqPaQaXlr5qUAn9llam2+XeE5POn4OPHxh86NMXelARDY3oD0iF3TuVt8cm9cSMP2Fwz62rIWcYnK/zb1K5T8x3/j2NhRleHfrKuffb4Id0nPz+XfArG9UZ78dpvF8vIoNbAvtJqQyO2b+evDolgy75yaT3R8yQ/Lyx5fEawacF5bFn8JRfOY4O/kfu/T7XFwYPUzd5/70Y3HeC/4vBMTdmCH3JIFEdo1x2PswwX2j1DawAYPDv5p7Z91NJ4w/J56WCb1PXSyuDP7eV47YYu5XjYcHbtPvPyb1DPZ9vvCUoAMPv8t6n8t+Oeea3wR/k93atX/wxtFn9TGdwzDbD4Or2VcHrSjv1c3K+ww7zh9Bfyz2z90/D5L8+EwZ/kz6W37cNYmyTy2OlBz03QlmzNJYKyWVul1x3TgWyPhPD6EbzFdFZbS8zqfot0RbjONbWd1mid5+r505h8r3y/Um+2evgaailmHt9aZwD0Mr8yKkPk+C+ycfFWplXzK0xFDnkbt+WP8Y/1nHnYy2fq5byLZZUcX0Q0Ym61Izdk+eNcr1Y8n/R4EbpoBcPXiL0ZsqX/y4b7KOUnBkznE1wTJHlQAy0x6Qe+qAc2pIoDIjD6E2DVBC0MimH57dt9UB3H8TcVcEdllSG6l1UG0MT2/fVfsrj/zODgcHlaVJtMlSg1SJ6kD3GIMhIuWXq74Aw9eWCm2HuTaZsGC0/rr2InqujhqmCxHUbgkYRfRpaY/adPGhkfclcDTX82aDjcTDqOjunRpsCftvf6aqR6nDv5jtcD+nljoB961zV8b6b/z6R7+2RBmXdPj1Xhpx1prEQ0KQMy1+2dzL47Zl8vF8eJ3UZ7GUtj4dOqsUc/R/Buzf6wXPVq+87uT5jSVU7HKnz+uT5UsHvJ/8fCe6TzvgtM3dos+An8ox6oB8ueMczaOXCYg55op3Y8msZ7FUtv4cE7dRC3zx430bfJ3izRhNxlUn7vWJS/R6+HXxf7l0k5WCU+8JcXTJ1yIKyGwDmVaZW//DI4Hfz3RflvWvmm98Pvi31d0V0rh9PvV0q9ffNMPa3c3X22mrJTv+Q3qQOGIyB3w/eIvf0OfQj23/zyteFAoYKCdqy994gEU2n/1Eagz53WZWFyWdBo03BY0tdouGTbSuf5RAOLXaUuf+p/L4EujXsvmnMP6QB/d7d9+VjoUEeW/ms+/5qrp7dZRb/Sql55F//HnRXd2jxT0eLaMO3G/34SVtSmtRlsmFJ6eTku9+YsbRa1englnfomZj6e0Ma82WGswJdDfhIheJXYPfaN0pNz2pAv4Em1+vl90sbfYfQgyfbMaFv3+iXBPsy5co5pt8Dnv+tOOyWfDsL7deTuo5tj7jnhhjtNTNnAl1lcBFY+n4RtOtN//pkkAehfsCqzl7x54jo9PO9MPfA4EMfgflWvx8819vn99Pbd58y/L/Qdf0vC0OFTNY4dej8xDjnMRvVBWCcT4XZoMA7SSfwLUH1iI+Al9Rg6WVFHxxP6IqXCT3xX3t29rvnCIa8tzQ4a7DiOpbW+jfpAr1n0rURw7G08izvZnwOMO7bBspBxPvEQQ461JG9llTr+mWaqOnI4gNy4fRCpOcY4zBEjjLzZob5groKkl9xgIq7VMuvo505gsij7aV80dG2tiqT9nDlGILmocYJyfsr2ne5oqoXtLoaHIE4Cqmf+QaYNDPQvXyfc5Hdibwj7XG4qvTnqopwFYNlkEX9oOBVgpvPSnZDmvIUPLirquGqRrvOJ08XHhg63qT6Tn882BvDgu+eq+LYeh0aZiryLqXGB7eLSWV/vKuBCexFdv9FXe1I0nhzvseV84NBbp4a5m155vNlgWNsDeWbq3r0iUGSBG8tO84+KX9ddfX8RFc9o4RismXyRrkeWWqccGGQ+Gork5nPJhXums9J59PxnXr5+sw0LNknJQ11uEee49b5qW6Nl9xUzhYcMNEbS82jOvzvVg57AYRfUg6z5h3bM3fIlcSCFrbpQHTuqQv1g7ZLjpfch0O/B13q/gL/8TFYJ6jvKRDogy/Cw0uN4vKpUiPQYvRPT6qobfDR196u76VOnbf2sdTjrpjbzA1mvmvWVrf29wvL5bvCa/V/zjx74YWBAYJ9KKBUkCUts9xv/M51veLlUJkNeLLZm2wLn+ACvvX2rp0VlqtD7vq9zEnr55OqB9ts4D+i+w/QpcZHWzCYKt9FMhv4vvTNVKej5S94cqMFOHgZutSBqhdngyLbYAy0UNK2j/4peHJ0xWGzCecMjPVX6aRsjhf6WfsP88zW10KDnV7q0GYe23U/Wmp+MdVrGn235KH3OCw1Uuxd0V3d7tqHbMrVe8O+dnVkJQJtL72VCP1Cu+kv64SZcooE5H3bcfv0gvza5WvoF4eg1VeQ4axvp7k1fgr9h2aYdtjDf+dSz3dHP3P4c2TwBipPZeRKjLxerteeq6FrrxFkLOtjZA2j6AZgVanx0HtLZqk7s9zT4M52ZsGWFo+pfvvmpAbvPyjXuVzNNN5fyMB+0wy+tKubLWxOYJDiK48mwtJj0daV7ZiyNdLMtbLUCCcrSo15do18hwWZ2nJYmPvS22+//dZbbbXVlYOX22abbZYHrpq64jpJTDZ4+K5tmbMdf6EBFxxcat6lRy+nV/PVxow2lhBnlcU2TaK4Zbarl3qmm+2th5W6711j+1a/nbar7sZsKdIweKiTNd4nGwaDt1nWVX9As3tQ5UgQ+gZ1ga2AHwJbybVSv/qi0GGrmbVbu/7kzbe4IGszwUhcR5iGYYQM9tFB0zEfnqvln5enI789v/dcH4PPVDjDmigbfLb5Mr+21BC3lqmIw49OQ1lrf32+99+hbWg5IVeBE4QLErTA+3us/uICgDw2tH/5lcE3dlUXNNoTac1Q1vDdN3OZZQRWxBBm9P8pNeSwTi5/95rUM6sZ2x6w00477Zcy3S14ux133JGdgS/+CSmbYBPPa2ksaJnWA/RwW12lR1IifSgHRmfcUg7MTLpCCwd9aKnnmtlBR/pQPuI8pkGbfQWY0JYnlLpL7UWlfpfNZZ2gvqfgiFKPYzbTGhhs8xQXj3HSfeK6IB1vzpUax3agv8iXOlwfg2sz75sUqBu+e9Tw58yzF16YYnBul58M0lP5A/diUmjri7Ov9TBTiU8qVUzSyXorev63iaOPOppG+lpXZ8QhAinf8D4NjVpqBM//KzUo4IKBPDakP/bpBRmJBpWApMFGgNb5X43uaoA/Yqz7z8pvNgbi75snbaOETpiZm0Ryp/wumcEPaM//1SCQa79hp6wJILGYIIDCz0tND+PSSdE3LZXp5Zfa8ZR2X3v1mz9KPW/NDIj2Xt9mpYZs6lW3hgYOqgkRfb22kpl+YSD1rogwbBloW1pv3GibfuQX/atuzUES8nvRof3AzHdPac8ZnIj/aBuFeph59sILUwzOqeM6RPO5KqJbXzwyVzP7OnXwGdChzd5GY5E/b1ZqyF01/W9dtbwaja8zqZZljWBE7/fzlipOYrA+WOFCwdBBumptNsLfJfS2pRqc7PNGE92PKPXMKzaA23YtwB86yKK8Mld7qA/MIGhjxy0zCB6y5557rsjva2XQOni33XbbKWL7DfPfjVNfRFhBD32X6L/YwNR8y1KNaAYzM5u8E4Utm/VlKpVhlYM1fEWpgSyIu+pB3LmDuxoSySBtICMZaEvBK+n5yqSdtyjzAzO1PGFcA4TZliog7duXGiJLAA71RKogMciT/PZx+7p1M7jy+e5FS80/eojQM8IAUwwuprftdocFeQc9bq6K0tbH18ngMxXO8PTkUkP6qPQnlhqFVCNZI7+954OCQNyr0US1o0tdSrEbzayisc8NIIqaXeiiZhYqAobWsVlo2QQMQNb1r1+qqEtkvVXqwgYPPvd32nbbbYnlDwzeMTM4Zjom+Jj8tuRG18c4a4GyD2LnAgMRWh7U531LLROGxjDKRDq6dqnlMKCSLJ5a6sDKgIY2APJcQz+41MFJu2pDqtRxpX7XILJOUL4pIBkwvBok1CmaxKOu0dp9z0ZTK6gH+oH02QhWw8x3zdzeMSBTpdCkghGmYYrBrUta1jo6tN1Xf83fRGnr4evskDMVrlFYWolLZoJBFOsdQYJEdKMz2jleZgYir98alc/4X8oijcIzebWk1IuzXdVDe3E29H8Eiatonnn/hS5V9BvES0tfjDtEVb7fOq373+OFVWodeJ8KQppR/gV3sl8HaCCd3exICvpSqfnCvHRV9H1KZRw0UZ3Ijn5D8juIzJ8pa0R0baIMaGiwoEahL1nmB6LMeP74Ug/EQNviShJA2ycgz+jfljVpQxPF+uAjpT6jXXrnpFLPD+9hpr0v3NBmaLHQDwteLSi8kbPIHhF68HOefW0WLIeZkem0HCuMzMQonVvDsu6qeAargdb5btfeF/nTmqlZYrGBOGIG4KnGWGSGe2RXbQHEUjQHEFZwM70ymXFEUb1+6sqs/9DgLcLULMBCIh0VGmMxaEEiM1CX50Zvk4ayrCi1zonnymdmJoE8olSm1E7WpK0iYF5tZla3+vHQUgcEbWD2Jk4TxdkjDA5D+Ty31uy6AdAHjilVwrNagTabY16GMmK6PMurbxsYDa4PKRvuC9SRY0v9pvyTLEglI6wLBiaGXfWEIkY/pKteXPMZEc1kxEKziI6GqQ8ulZk04mHtO8Sp/ntdXQvFXEDHYvQhEg6dc5/230KD0YqYqBPLqw6nU/Hc0lkMTpiBSCtemYiklmB0dFFTrQwYjG6a+tLR0UeEpo4oN+yXHdZTb0eUxT9XS70q06quBlSUdzOwGfL+XV2bVyZ5Z4NQD9pPm2Bk9w2+jHeYW5kwu4HRd9dSPWbKybCnfJbW9AsDAnWHhIM2ext40NIYBhffph5IW343ZI/RZt65dKnfVafoEdYHjbmhHUW9mNRtIBb6zL1B9CMu6VzE72kR3VZNzNsfE9veHUQxnesXjcZUnytV1MX0iwFmOOnJI/GwF9FLzffr0cmfGYQdwX0rA4N4+Y6uOWkE/7ervvasyr/o6nq6+75rJloffKLU5+4/3JiHhHRW4dRS88WmMIjopDK6OFp79SJ67ln2Gso0LaL/tKwtopNiqG6+i6lWw0xfeGupzx9d1qg5J5Q1VnsiuoEefXZFdA45nlEGA8J3y+LW578+DIzcVecP65PP66of82wDrguIgf9dqvOBmYHuxdpqFKbv3bF957hujZGN2MiwAjDUM7q6nKXR6L3z1fPOKphdzTAvSHpmEjPUC0qdAUgP7rMmKxObgs6v46GP6urMR9+7X1fP9FJundnsg3GU12y3PpCe9eRDZu6fI5hpI9KJ/HJcOaLRZlMHHsg7kdlgqp7ZTKgnaAOZcjBaaR8zNaPak9p97frSMrMOPpM20V75iMys5Ohbljqjo/kaaFu0WVja8mdZckWp6UmHzWZ9QJz3/lVLXd9nBKQOjLA+aEyHZITSqTXKfEMlE/8872qmZrQx4gOdyUzg+8RdrqJo4rwGAhrfIXiWs9BmnrU60QKC4d0y2W1K7URmEGd/22ii06Mx/rCkpDOuzNWARSLZvtSyHtLVM8ictkEMXl5quYmgyrE+kB6m29AMdZZhpp2IxCQSkoQ8o9kODE7yTv2xN4CUpD2Uw6xqILMCIGQ0hmFLUCb9oVc75gH0fOVTf/s3msVenaLZNKg5BlMqkfqUP2kYUIjf2l99rg8OKfVblt5GOIug07MSE4GIneuEmQ5FFPtDqeKsxvLuu8sakYvIqFHRvym1IQdRzKhNHETrjER09GKK6EN6mO3jjZbv16JTNjPgait6fg8i+tu6NRbnL5RqZ0D7nk48XabVMFNXg3hJtOxh5v+zBTPfGKzoGOVNjbYZY1ClzMirHV26umlDuakrfTm6ehyxAcEz1A6MuE6YSXtQCRgoH9/oF5e1HV0GK7p+cWijf1XmL6IPji7sASOcRdis1POo3lbqEtY6YaZRjyh1p9GdS20knYqhxPt0PCLUpqW6SdrpxPj2slJ3LDHkcPEUZhljENWkbTZdDDATKd8HSxUV5U3ezTz3bbQZHsOjGQlZ/tEP7qpL69tLHQDMIGi2B+u59M+3lBlmmKmrxwTfWdacCrogMJUGgohL1yUN0U3lkVMRKUTdWhIk2srrPbq6PRR9TKlr6eheTA6NYV/bbeC4opl7mE75SATqEM2AZrZGP7LUgRz9tFKlO/QJpTK1vJocNmTHoNp5x8A0wtkA4jNRClNqDI0z36URyxs6FmYFGGKg6YQcRXQKPt3DEThoHleYj2hvVDfLnxswlE9n1tnQxNc9SmVs1nWdnthre6mlL+Jrf5RwVzdpWHaSd4Pj5sOH1wOkIiL9UCeLDeoTM7NvsKmoW+WhSvXt1NUY6wZmYjRgZdcmvSrV1SVDYa9mmXldYNBUvpWlfg9tIGQtp66xdRgA1bNBXH9RtweX9Rwr3dK1SqEPesY3zs06vEAB3YeVmbVURQ7ingqdD5jx/lyqqGag8O7nu3q++LBd1F7fXhSb1DjeP0DnGcz2iVJFwpsOHWqRraKfLjWPjE2vRHf1OJ9evMyVL/ogXpJMBoszcVanRP+kVCMUKYCdgRqwGmYY48RS32FMnP1vMcCMLT0i+aB2KBtjl/KtPpssV6JvbzkPfXpXBzl7ErST8NLrbIuZ/L++1DQeXqq0gmZE69MrVSoa6vDzperTaH3uDIN6qx+gbzDcYXRivndGy/nZACMk8QejGXWJ0pZP1isyz1Qu0ZNeS9Q2cvvOi7u6BfPTwbfNzc1tnU4joubH52o8a5s4Phdk/GEd/WyeO7Q17oIzwcz3Tih1ADKLHNtoDjn2T6OtGzM6of+rq8EK0Md31SEG/a6uHs5oQDwg2Ec8mU5rwFLtFDo249zsfwsCM9+ib3+2VJGZqIy+c1f17s/kyrHHsUUGOgEfzK6CKbwm7SHayqeCH56rkX7O4La8jjJod+U7otRAIGiOQWwXaPvrSYdouwlJNGhMi3nXgvZdYFurfJKSnlnqO1SNmTdGWCfMNNSq4V6p1mzrnhuyavbQ3qVT78/91W9M29W9yP5zwICwR31kzKVrTsjYc65GkhFbiUgsPJKwSSs16qSe3DE7kJxt8J2pshJFiaH2TlMf0PJOnL3ipPrjG5zQ8rb1pJ66KlILvZQ7L0a3r32os9XpBHOrj+piw42NOHZJEel9UxpCR1EJFqx8M0BMvnype8OVy+GJ1AwOTTad9KfGdlUVWb1qos4bQ4s4K358v214msHbe1AbCxclDVs+lY8aYGsuelVX+wBa+T2nnqlo0rxCV+vHvv216rD9BuqQX4Y6HNLot5ROPz/CemBgoK42/A+JZSo1f32yVJHZDLfOytxss8364HjtfcswxKeXdlWs+l3wpNYoInWI5CkW+h9sHQ2TXzQzuWin/hNN5sOeCx6ee724F/q+Q/50snMKOulUeT+cW0RQM+rx6Fzvnd/Wyvtgfl3dLuq+We2G8pfrh4MHy3d+i2Yqbtvq+nFtaei0X2zvKxPVBX1kaGvo6CcM+VmI8k3no1TDpvZQhr5M+e/YroZtUg7SFW829wXeNMgRy7+aeto97TScTSbC6ur8zdThye27JB8GR/SDgkc32kmsd2zpvTFoSdR9IaNIRP8Ifr+rA8/qvA/fn9SgIP3qSq7e7cuU+2IO9M9MDzwjrAPaaK0BnTr5+dBC0RqZiesqX0Osk8G32GKL1Yf+5R3WWiLrcekYDC5fDv5PVy3k35urkTT7M6822WST7+SZlUT14E+W1LO+3hT80Vzd3eZ4WfSdpvI3k/pZh+nOGdoGkh+1Tve0rp71fcf8flCuDko8Jmht/Ie5Pm+uRr358Vw9JMCsLn+fmdRjj/pvDmk0dHyTI3g91x8G2NK72aSGaUY/3HtwIcrnO1Nt9cJcHbxoC+aD81u7PmBSQw4r65NDO6DiB7m+KteVyYMBXmyA3dM+P871tCXtiKN1MfikMq1y3LirTlLq6m7B+7f7j8nzQlGjhea+ZqPfEezrsKsHVPYSxNDHpr5PsjP4eEdknRc2WqTY1XU+wgZgaDw4VyOfiqYp6uiqVDjrN5F5nQwOnMsdhp3bdtttt86MvtfOO++8Q0b/OSdU5JuOufXunrnu2tJxhNBw5hXawQJOSli1pMZjt0d9ZVDk1T4Iv061EDB8q6GyOhZX1NGVk3o0MPHU+WVoV+dfocUDdx6WU1f7QxHmav6ESZb31Xmcqk/nnIlW630hsPacq7HupCEsMbpXW4Z3FwK0U6tze9s5s1AtGMpENd0qaW08V+OeKY9nbX0dDkHQBn046+Bm2sI3/R5gyG/Dvg7navmGOhRrf6hD5VReYbj39NykRqkl/pN8qGHiuS+VZziThsMcPNun0fJKten71fD8CBsAESwbCl37zeCv52p44BPTOX4dFMtrnQy+1VZbdWHsuS233HLjvO/MZ0f93G7rrbc2g5+ed96Z7+xdqs+2IPo6kNNLf9xE9C9kMHDUjtMu3hP8bfC6wZdtVI/jufvAMBr8nMIU88H3LqnH9two+Jzgb1t6j5R28vD40Hdo908IHp77jv/x3lVz/ze5fnluJsT01PedVvixVo5rB82SvnXr4JOXVvH3WM961/Wcgu8MHT/4cunlqgwOBxDj/GGhb6McSe/ZoQ8Oam8Rby/W6v/zU2U4Q76GvDZ8l3LM1cMUiOOOsbp37jsDTR0+OfTtWrlfGdS2jjH6YO45J9yZcV+Z1Jhsqxl26vsOOxRtVX6vm3de1tIQv75/Xn5G2AAsXcPgjg/+sbBNqUyzCz3pT92abZ6zr2LwyTbbbDMXht4k73NP1Ci3yu9Lt80lJ06qhfwPuX59rp58+bM8+5vgPhvXc6noemJjfyj4f6GvH3y193PVWVaP6OcUlHPoPKFPSSdU1psFnbrxl1zvlfvHtLR1zju2+05/uYH7ecfBB1cL/j3oGF6z4hkYPO8vybOf805+Xye/39C+5VytZ7TDHx7jWe+6nlPwjaHTB1+vfGmDO+feMdoy10fm/hEtbWrQocG/hn5Xrpdo5aaDmznPkKfh20MZg+9v5btF8LnSyPV++cYj2reent9HtjQcSnm9lvZHcu8q+d5fgqevj8Hz/lzK8On2joCMbCHecfrMOIPPF3R8lRkkchGJ6OOcOzg+sF6uk8FBmHlu2bJlG+24447bZybfd7fddtt9+fLlm6SBBSbsI8P41qSGTNZBnO8FibCOS7pkcBN0/nN4AIZhZWd4cyzxJLjRpKoNs8mfZRg6T3A4p4xK4MyrK87VpTtipZh0RFXH6qKJtIxNjtthWV6+pB4r7LhdsK7vO8KXyuG7RNghDSKrM9uca+26IIPXAMP3JvVYYasXLP4s2ftjpPy3yaSeBT6I6J4Z9v+rj/70kvbf6u+2/913fpuDH5xTZta/QgbqbcOEjoVG7xSm3C14hfy3Ms8R2dXhcB6a1QeHbRC50UR27XsGBm91qL7VlfzynaC7O1duZPD5whSDq0w6Ef1IfHPbPQ9dH4MT0cPMvQ6e5z3HCPLg/Kb7fa6rsbct1xDDPhTU4J9NOl/Mde9cPxh0BpaTLozO35irp30+K/e+taSKd2K5PXRSLdfrzMdZhaED6cStI1sH/mauZpv7J0+n5/qI4K1z75u5PjN47dw/LejYYYPT6u9Mgzz65pIK71COuTpTPrd969/naiTb03Jl0OvfWYhyDTB8c/hu0O6tr+Z6z9y7Yf7/Wq7HTeoRTtrmJZPK6J5h1OqG98FA5z3tfJugWdrgLSKqE1b/LeV5TGgnq94pzM3KTWJ7VPAWee4bQaHArq4+cn1D0BKZ/uUwibUmkVZ/cC74VnUVZKjkfKSdbjedvxHOBDB4KpDV1xoljyzLFl8pdZnlerPPDxDmLptvvnn/fiqbvzOR8BWh7Rb7ffAToenj7rN+mvX+THScq2us3/PfXGXij6FzFcCQH7v7lq0Om9TzpwRuXDBG0IGGTtK15ZdcWX/5qMvHM4McRNA6ZL/ZJO99UUcfOuE0DJ3UN+eq8a2vw9wTs673LMt/R4V+TrvPgWbByjQNQ121bw+bTY7u6sktaMx5SKM/0NXdc5ZFT+uae6pvDN9qaNmKD/txTnTpqp/+LyLB3Sp10S/9RXJ7QP6z4USUWXp+v0zW1T0HfP3RHG0Obmn/pGvLZFPprB5QAr1HZa7/Nlmz1NifZtKe6fM4wplAGoje5SAEaOsgD7bDSvUf1ku09hmGTBXcOhPHCaMyi3l/yMCkWm6tB6MZ7nR6YipkzWUddVYVMZIjhlmaUwZx0qmfK1rjW6PlM77gzNC+yfGCMXH3SV0PtiRDjJW+mcOJJUReNHEzl7U715CvroL/PUQEFdCSw44yWXcmLqsjNCcTr6nXhS3Y2rCy1IMHOJrYFszRpZ81G23LLPqypW4j7V8arkB5g/qIo4T3zMC+LAP8ZSPFHbbffvvtue+++15uxYoV17xEYPfdd7/YFltscViecWAENeSwSbWWUwWUWz/RXw7uquNK7wEojVz7ypvU+sPgVDXtoQ6pjfpFn9/p/I0wD2gVDLmtvjR4Uql+5WYArqdmsTPAUNlTlW6jiV1NdpDpxMTz1wZXBN+RBnvPpJ5r/Wr/dZXBnp3nT+yqBHFc8OSuujkarYlxIpP035fPcwoz3zg2KD1OL3cKntLVABXSd//Y4CHuT6pY6lC91d+YGuSs51JtPjapXm7PD548qae5PKZUxx/+Asp0cqnBMexP5+L7n0NmFqF8dwq+q9RtpDaa2LXFR3xVqT7iHGHs4uI2qt17UEbQyuZqsObDflLE8stnUjgmZX9VGPqaYeZbZva+3W677Xbj7bff/lZ59v1h7vvkGa6xyv3YIMlOnzq+a3U4ja0eLcvankv6OyBIteFchLGn++iQzRHmC1OVZpQ/vVRxSHQOzEqcIrL2MNsJZyrdO57/cle3WRJNLbldYm5NWCgz9o8areH5vfsPU/Xhf7q6H9v2RTT9a8EYfKaDDOGGOIQMGzOInMPe6bd2Na7cUKYzMHjrnKQNIYiIlMpEvPxH7qsPDPb3/H/7UiPJ9CJzqdso/1bqWWI9zOTtbMFMHT251H37RGeba6RNTTBj/zX4obImTNP3S5MmhnxM1bt+8W3PRQy/Vn7/T/CzO+2000223HLLm3s2s/otwuh9vaUOnhcG50lnA5OzyW7Y0qB797P2kIb8tnokWfy6vX9I17wB8z9dvn9uIdr/wg6USzuNNJqNJzqr0Z/BbD5ArLe5nxfcFqWe2mm5TRTXG8Cubrm0dZAXFHHt0FID7fPTtjvrZqWen6UTyofrgsHQsRpcqdTyWdKji6JtHvEbLT8rS82HGbhXvodvNIOQzulophsEb5rfnGSI5zfNTLZzfjuP7ab5TYrhQCTgv33Q6lRZF7R8M2DzD5fjYeuvPQbDzjebPvZstPY4uNFnYPCuAo++m0bv3jni+aHbbLPNzQ466KADg9ddtWrVTa5xjWtce//997/q1ltvfYswvXO/rY7cPGV2HhmVZ4iEc4Y6bLYgqps6vPlcXdWgFt1y0mL1j8y9MGCkthvp1aV2ADMb+urTD20AdCBbMB14sDJXYp3Ya3QnLpT8oDU20f8VpTLSMfn9qlI3SNw3iLZN8IhGi8TZw9ApzgnMfEO8NOVjUMTQaOlJHy0/9k2jH1/W3zmX5WrWso5vuZGl+hURX3m9UTFelv90dGL5K7vqXy1NdXXn/mNT3z0nMPMNAwhf8cNKjYVGXQC4RfAF22KBwRiuBb7VkJHticFXRETfP2V9UMrzor322uuwHXfc8dZh+nvsscceN95hhx04vrx4o402OjLPsG1YTXlAUNtqyyeW2sdWw2wdBl+Xb+w3qdZ+Kyw2+owMvkBgdO8t3KVGOTmp0UTm+QAxm1j2ja6dO5Xr77u6rXIQ0c1YP2+0WUMoJDRRmF6IvlepouQgMi8YzDBAn16pwQANbOjnlTWHBPj/po0WEXQ1g+twc2uWdbYZzndLZ2dL+F90xFaMzLf/j5NqRSeiE8uPKVVs9g5L/oLBDCM8o9Q06PmkkG+VWk5BIdzXvsDAbLA9AyhrV20z+sXfwoDXye++X2yxxRa3DTNzDHp3GP8+ocWSt2rynOCwH/w9pe4HZ6mX/voYfGubkbyTeqV39ysRufJpny3XCGcTNOQdSo16SoxkKDHLEV/nA3uUGsLHZg2np1h/dSYYjyVGLPuStyv1LCqzh22TZhm6mxnG3nK0AQHzYzQi/GKBjoe5zTDEdWW1d5mYjjbLXqzU+jiyzDA4bB10k3TuO2T2vsfOO++8avvtt79hOutt995770tGZL1e/rt7GIDDB6Pb3bvqK47J2Dakt1jAsIbRMDD1SXuuaP8Rl5UbbNpwndBVEJDyHikrZ5YbBu+57bbbXiyi+jVD3yn6+EH57Tjle6Ssh+Y5thbr4uwQVAXSoL6lj62GKQbfNHiH4H1ST8RyBjubZHoHnJHBFwcYSohVB5YaPVVDnSEKxzTo/I0BbDx4YvBhk7pJ4JiunlXGamsQeHzoVaV2cuKvDk+EZezCCDojWtifa5RqdNNBepDGAoP0lNUVs6MZxs4ArYzCTdsBx4nDzquHp6M+OmLrxdPBbxZmv+VlLnOZq2y33XY3z3+PJtrmWbPfY1MXGI+EY+0dAwC6MkZcLGBMu1/Qcp+B6ohSRff1gWf8Tz8XmEHYaNtc9wgeFXxCpJYDUi4OPMdmQDs0ZWaAe2zq4UYpszZ8QqkHBa4XSEENbXLhUvukSd1cYoCghy9obIAR1oZPlypmmQWM/Eb9DRrcGnND6+n/JLqGps/34ldXAwX8sv0+JNhbS0sVhYmznjFzi4TiPpHyYY0mMvewCAxONJeGcENmb/SJ0w+AxtxQbDED1qPSMUkuvRU9M9nBZnPP7rrrrofnv170T6cnydCHWdQNdqzo0qCbrix1SfJI7y0SULOkR/XRHoJQPn2tJ9YGMzrRHpMyuH6/1LbB8KcE/x5mPCK/2RGI1Ta2GLDsY2Bv0Wf+L3hy+946Yaq/sKL/sdRvHTKpfuecjri3jgy+UDDDONZRzaKYVegcxrIzGGOmYWiMSY3k8l9tBucscmxX15V9Q7RSszbGILI+IWjTyuoZvFQbAI8vgwox1v3FNLhZysGM0jXDmsGPmn4AeKehQBmCE14xzLttyvfglPWYPfbY42KZyW5qBj/ggAOunBncppZj2wzOEm9dmJMHf//HlaqWbFJqmvutndqCAumAqmGFQ3q3nfl/FnjzkDIYBzkkDTM4/4bbo1MeOwnF0kPbiKN8nuGVqA0NDmcqdbnf8vSwrnr5SUNQTisvfRSXkcEXCGYagdGMwYtoTkx+YKlLR+uFKQang98reGRXl8Po33fpmg5e6oxCXGdco3djdsx8z64yuyU6aRP1zDho//ewvs5yVmDmGwYRkoMrZkAT19cHW5Wa77vO1dNaGZzusssuu+wepr7B5ptvfuvo4PtFB7+W+xtvvLG95bzK1AEvMsxMBcJEpCPfMsj0sBDlmwE2FQ42Qlb5ONVAva4PGFzvmketJFhmE7mHu6rQTIyH7Cv82A9r7cxjkaca+pDWhtpMmfrCrKNM1D5qA9QXqA0cjaQxPZiu9dII5wBmKvMTpVrF6aIvK1XEM7P1sK6KHxi8a1b0ICs6y/kgojPs9Fb0Uk8LmRbRB6u2zj5Y0aXHEMYKy6+7h3WlfVZh5huDSjBtRf/Q8Ofw7NQ7GMYzRFWMokx/CmMbkPqVgeXLlzu8UJn+mGeI6MR/77Cgi0KKPr6scUJR3z0sQvkw6m9KDTp5SKnpfWDq/1ng3tq3Wakeit9Gd2tEdPQRpS71oR/VVYnE8y8u1bai76wW0ddRJuvwQxrsPL9qtMF9ZO7FgJkKpYNZxjmkVHHVerDllvXC0Chdjcn2xuALgjYoiJ4KzdTWYF/e1cB6x5S6NozxMRadlLRAXEezspu50XTjHhai4We+obPyKiNd6MTKSmLpYXh26p0dSu3Iz8+AtjL3nxx8ie2T+f2o4AkR03nwUUFOyJVIru5eXur3+RagzaoGCAMo42MPC1G+GZAeGwA1x4qFtmDZXh9QyQx6L+vqmWyPy1U7WVXwnjVuYrh1fPThXV0R0U7qUhnfWOpg1sM6yjTUIZQn/cL7JMYRzgXYvNTzyihAZmGzLKYkvtkcQjdb6wXg3gzqLIIXQsYU1mTea0S/Q0r1fiOWmfWJ7KuC/NTRNhpofDRbwGKBpTkdlHg+eJnRi3sYyjlVJlb0fws64G9ZGJpjxw2W1NBTopagRX2x2eLwXIVPsqHF8+wQRFK09WcrE9QB5V8rvQWElaWmZ7UCiJ67oRURVnTLhJYStfNhpapsZnY2BO1nmdPgpG1ET8X8+ogZfz6wrNTvQ3kxkHt/8LwbYZFBZ9ew9E0zGpGLN5Q1VbuDuJSuszMO9xti2F4U6+pM/ZP2++CuRc8slcmJ4OjVvuilLqGZ2dBvKosH/11qGlQDuiP6/cOf1A4wVaZevJzUKKvq6Rel+p8r3+CLjumHlYFblaZ2hCaemwWlYSY1mLA4f1gaCwUz7cLAJr3XlXrE1KPLhg1t2py/uvyKT/6tRl+za44uuXqfFOK7jyi1b6CJ7fOBWRH9Z41ePbCOsLhAnNa4y0vtIHSvo/zu6k6w3hiyPvBfQ9sFBRR4T+h9g68JnthVxsBYJ3X17O1jStXZWLLpcOijSh3Vdaqj65cXBgambXD/Uk8voatKD/3k4c+hLIONISjqi91xdpEZwHRq+6vN/ganD7XyWQ5TVh5unH1OLHUwM2OjOfwQdenDVgoWDGbKx0j20VLrmGhMVLf2vj7YtNTTaj6YPOsHxGjlsD5NHbHzziD/kEb7vrYySK0W/dXZBoAU866GZv83lGqHUG8jnJvQ1QD2ZlziJdRhN+hlNMUMwgWJxUV0Jc7aL321Sd0iyKvLt4h+mB/tkICVQe/Yq22ppE/Pc6Va8i85m945BD1x6I3TdL1RmdtOqF0n1XFHuCHlEKGVr7YBj5Vc0AT2B4H90b0YG7TX3q4z20kNmPbOo+13591FBVpZqiun2YwV+twGDSltebFMJt+Wq5RbO1kWXBakNtnPb3lQm1h6M9hrs4PLhk8KtcxqpYK6hdaWjGrUOHCGuh/h3IHBWvrw4LHoNK4oLj0Tz60jwsZwf66GH/7bknrmlVhcvdNErgYMI7bv2nlmBEdbPuL4gT62q0s1f8n1JV01VBHjzBqrZ9XFhCGNrg5sjw59j6CgBpwy/jypAQl+VGp+zT6fabSObxaUX3oqIxL6/rnPYIh+dqkSC/rtpc7mROMvlwaLXb4p2LXUfPyuVPGZ2qEcBhv5QR+W/FBderUj9PHoXB1X1PeLXF86fHBoI9hgOL/uh6W6yv6pVPVkgw5UIywCtE49/HxK8LuT6mF093Tsb89VV9Rpd8Opt9dyQ9wtzP0VmHc5fAifK16X2Y+4bnOKjmPn2Te7uqWS3m0PtvRYmtGPznuss5ahjh86zmIzgO83NNveNVfbIFcmL0JCf3pSY8m/P3hqV2dvu6DsfSbOPqWrMciIs9QANL9u2yKV1YkgZrGvljoAEFVPLVP2hoUon3oacPr3DJCO1O1H89+OuZ48qfHaGAeFXvr6pEotL07exZa7fvBxadfTc8/hEQat73RTIamGuptKnzHTxh2edKQw6Rk8VrY8jHBeQBpm4zTU8kkNVk//FNReWB6HAdj7u85g/n6nA4jBs1lw07kas8y5ZKJ2iOLKWQIjEN2J40472WrjjTfecaONNrrk8uXLd7jIRS6y3bJlyy651VZb7bbNNtts6f6kelStq5MuOAydNKgO+pBO7be8WxaTD7O75UA0x56VjSbSOrMLLWCE54bOTqzt46CVelopUR3sFuw9uIb0z2Xoz07r6tZeIjiafzi3Ufu2SWS82LS9gy1si+Xso29YXSDSTzO4OmCQVQ8s5yzvpALqCNHcas25XsgRGgyNNTRYGvTxAijm+qw0rAio/wi+DzO3zr7W++5P4f+3dy/AmhTVHcB77iLC3c2yBhZQF3YvPsDoSpnSKJa6gEpekkppLOOrQFETX4SKEo1GBY2SxKI0vlIqiQjxDRrK4Ks0IBQqiRqVSolGixXEoAZ8RTSJ0ZzfnOm9w8e9y33iZe1/1ak533zz6Jnp033O6dOnpR7eOai3bOyPB/0kyDpe77A/zj8xiJf363HMs4OeMBz/grivoRs937vdq153NTFUUiSPu3LITirvnAT+355KIVcmCQfNB5e0n9rKJjcn/n+7HB4z0eR/YitC7GneYZfzrI9xbsnx5Ls7t6Sav+v+txKMjlDPv9Glr+Ga4Tmkv7Zc00/W5aIJ/2h/vIvHxDO8GR/04r2GhJbdqL4MJKOrZ6UZcKZ61p0lI9ka1gJ8KB+PMImtDrox6Mx1uQaVVS4u8H/9wJOoghi0Mc67MsiiCJIy8qx/L7aivtjY3w8y3ZT3WTrlp8SxvM7u+9Tp6enj4r8vBJ07uubE3VYW9dm7XAXVSiHW1dKTXxO0cyp79M/F/zdM5frnH44yeQ5ONIE+eMNLJmTgxXI/MY4R5XdakHhvUWYCUeCGMjtXe9UF3POhkt51vgSNlSmbX4oyet4jpjI/mhVTHhT7zvf9gh4VAv4GE4pC43rhMO1zV3nrdYN+e3huZoyZep6PWt4EfC2hCtNUJqCnYpsSKpE9/rCpXDpXgr4+d9kkfPy9Mrc4L7ShM55oqjaVm4qu17BowH7r168/ONTzIw899NCZ7du3HzEzM3PUEUcc8Stbt269Y6jr26NibVORamVaTYwqqmejskov5bdtjZvmIa/LJvOQGwnAa330jPUadfleNM4LzrNeoeJLbdX/qNvVwqg8yPfYNvAaLg2W94yntTBTmGZMqf3iW2mwLS65Ye9cd26u69bREtemklPPxeILpmlYCxgEu5K44/+MrVlgHE7XB1GZ2Vo8x3oumLzMTXrc4RgZP77dZYyzWG35sh8XJHCCI+dpUYF+z71i+8yNGzceF/u/FP9b82vVBdwzjIiH/Lqgy7q0tzkHOaEI+eVBVs0URMIh9a0uI7skjRTYY2iIQ83z9U62Yb+gF0NGris01Pl60Y/0BSi3qoB7jmu7fCZCfkXQdV0OX1ox1XeidcmK6/tbk65P2ECwoxef77rm9DvXeHd91s+V1oOvHVRhGj6YgAy22RuCFzDBvvpIlwEwx3Y55xsmL9NjdB3SWeecC/wQCy5a7qQukwW4x3Pj3ifG7w/FlmlQh5QEzqx65Z+A8WH35gnnLf9B0A1BhpcsHOA9zARdNhynQfj7gdcwvWzgTcN98sB7TsKPF2WmZ8OL8LtVMHwLtDXesXf+nS57W4LomcxA6yfQdOkv+NDAP9k3UDcI+W7g23qmT5aMUjNxyFApr33DWsGoIvRrXnXZ4lNNOZNUeKq537vUy/lQr1VyvFV8NO/qtiAedTHcB4Yw3ztU9QP3339/XvQjN23atCV68A2xnzd3ZjifOrC6Rvgs3NBEiOoJt+293V0G6rBhQRTYOJ56HPM9rtRj3vH1OcSJ12vdGujq9wiS8JB/Ac/z7V0L5GGa+Da85Lzhpryap7C7Rtbz+JP5cZ+SATBUdN9bDP6t9d0aFopRRagfVuCJcVuqqGgzvZt84irFYlXo1wRRCznVBLd8Pnjjq0JHBbY8PwR/R8neTYir4Rb3Fu99U/1w5fDQoC+WXBhA7/rZkmPUbGxluqjLJBj2/XNJoWVq8IIb7jIjT3mN/+q1nS/JgzBRvKAe6ZQcI5bbOc51v60lx4kvKhn5VlYByuX5Lozrs60/E3TZVPpDqOW+gXFwQUhXBIlsm6wDPSZ++2YcaQKWjPN7Pqp9E+q1jokPae52rzJ3mQqY6mahOUvQ7Eqqt0Cwx12rRrKJcDqtyyyr9p8VvJlH9guUqKrtzrKCAj6utCVnULnHp0qq6D+N/6+JLSefZ/3vMut9dhx1XRw7Xk/1roE3BfYVA+/5TnKtkvHuRw/7LxjO+UnJVFZ6PPu/U3JG12TZVgK82p5jZ5ez9njE/bayqiEzvJ77y/iS00An68Bc6E23klNGzRL7cclnWnBlaFgb0OP46Bxr1HMzqCz1WofUdjlhFgC9CUG4Y0l1XYwyj72ZTbKFCMBwITzHD6F2jJDH1YJ7E4IaX03V7MMqu1zA4LBhvzJX3jnbBp6nWE9cwYlWMd7PVGGmAE2hBsfwZ4gJ7wVqkRrRQkBl9nxUbvcQQ9/fL+5lDjunKZODc8y3XZBjLL654/T23pX3cUzJOPfdtgoNaxRD5ZDN5KNRMV4XdM/4yJcFWQZWUvte2BcBs6w4cx5T0kHFA6vnE954YdDpXc6lxhs/Xq2eQaUUI0591juLLWdKqPTnlszIAq8u6SSjghrDp9JS3fXUeMJMw5FAwzU1ZvbTSNjceNNkqfh6vZfHO9Ngnh909lRGia3GmL9GyjRdJo+IMkk6qNLm+5t/wPzx3HPCdx+hzgNn1jCfzgl6zuzfDbdJDMKN2M68sFT0XvXbd999/d6kYk5UhluC6YlUPIJVVXR2KfsVf0lJjyx1lsq8Wio6DzA1nH2tcXFv3mVqOR4R5OtLqtzGvNmbeA0QgXbM0WVWRX9KyamV+DNLPgez4/1Do2X/Z6enp2diewOVOUiYb79s8wrDdzJywasN7q3sBP/G4XdNEnFL0AA7/k9K5mJj2y90bnjDWsVIwKmVhrhk1dw/KuTjgx4ZfB9vvUgBpxI+oaS6SmiMpVaw6fSCajtnjt+rBR5wQ3Psb6i9EzAN6n7lrD0Ys6Xy+5WbJjek4lfYX9Uaz1MDZR4YDeM9Q6jXhZAfFZrP/aI372hAGzZULX7FoHGiRTB1wHtG+5RMRMGWdsxCQEvxfrbFMzBZCLkGpOG2jpGQV/KRzwrZNrPIpAVq9FllaWOfR5dUITm8VKI3lFRn8ZIknl5ysoae44/LrC27EjAsJoHDH5Yc7nllySygQBX/04HnbHzpwGuYXj7wvOV4Kr2e8GUlEx1sHngCz7xwzG/ts88+JmqcEu/t+EMOOeTgEOrfDOF+2KZNm9YLJNm4caGytmBsK/k+PYsG81UltQoCPicmGmqmFM2KYB9b8nzfqWFPw4SA64Gpaz8MXiWm9vk9djItFKLanCtopHq1P1FyeSM89VIPenlJ7/pSGpH50I8MlBy6Iuye45slG5GqzqrxNwy/NQKGtqi9HGfvKWlGGCoi0I6R6kgGFPv/sqQmYP/b9t57bwEm7w0BP3XLli3UdXb3joMOOugAfgwq+oSALRd1JMIogHdYvxOtaSGoJojG9UUls8saJmzYEzEScCrai0tmQiVwTx9oKW5gSQKkTqY66rX/qMsEEXptvekJJVVhPgA284rZ4yV7WpFnvPvwuBGvPHpoIMBUeaC2116MZ12ZQC9ejwfOw2pUU5MFmfBeHx8q+v03b958h+jBH0hFp65T0asvYwWFvL5DjY53+LySjjFlXQiYRxJYmI8gaeRzulwyevK4hj0YejuVhrBT/U4uqVbzHi8W4rv16NRg3l2qJfVZg4LXEKyEiv7ckgJIwJWbcw84yB4x8AS6+gaOKenxB3Yn2xX0+hoFMFxUGwHQCNgHBMxQI+G1iMKxoa7fLgTaVMvjqO4hQGbZ8XH06vMyhIhG9VclnWL8Bd4bk2deTNzL+3a+kQDP8+IuM63yIUjrNLPCjVDDWsPExyWIVZ2Fbw2/tfiLxYklz/1USbvPNanonFT2I8K+KOg5Rzix5HXeVdIMMH+Z+q83tv/a4bjvDb/hGyWDOQgfL7r9MyWz0QoYoYbzD3yuzDoFHcNu52xzrghA59j/+RBynvofhHALNqEFyYTymW4Yi16MAM3zfJ8t2TAxFb5d5rG75xDW+nw0J15y5sjphDu0jL/pMqXy5DkNezCofpwvotPYqKeWjODSeywW1HUOO8LCRsUTEtd6VZcTYZYb9WU4jL2sd9KDu/7jh//M/tLbQt3Co8rswgzUbaou6M3rWPDWksNj1XPOZCHQwIZ96FBmPare2jNYc/vpA/+0kmu59Scs4/k0rIYhfQeBKG8qOYR3s0CF+h4n7uU5OR6ZIoRchKGEFvwHZv6JfJs8p+EXCFRTKrYeimBQD4VJLhldzq2mvloiqI/bXkYFI3SGiEyo0auxoetQF0/x9oHXwFQBJbw1ok5PX4edNBCVV6A6rAaG0mohjy6zDZ7IQPOnPQN7VgARvtd46nNN9MorDtcf7muYU4NjvW9lpIHwS/ROQKjvu9Jql61h7YIDjBda8IiKT02k7vEqLxldZv4UVEOdrUkYJg+bFxMVUuX9bkn1k/NM+ajohBhPHQf8DwfePr/Z/zzuTAe9Iy+6/Q8qmZPc8Rq3x5ZU/f+iZI/qGBFzAkyovIJEmBr2f7fLVMt94AleeXnVFxHnf4uo74wjr9Lg9JPoon++Lld7vRRf0ha/2fmLee8Nex72LjkefkHJWVQi1Qy1LDggovYsMKpUxto/EHRel1M3l1PRDB29taQ6bVjvnJJON57lvytpb7p+bxIM/EtKjskDFdYzelYONuqwnnxHySEkw1EEmWrMqy6Y5i0lZ5uJDT+7pCORynx2l5lZ3cOMvbNC8G7Ho14zqEwmWlgIxu+n8oMw7xLu6rWfSrw+6B/iOOaLMfsPlWHiyRirEErbcBsHYeKh5lHnKKMOU3sFhdivEdgtagWdpGXA3GzeYM5BoErX8XvJD2R/VfGlMer5LueC67GBwPKeg2EwAl0x5o0IVImggdTxe+bAzPAc8sBJeuh+spc+gOAFHR7Cfd+g6clUSYvF6J0Jsjkq7vFrQesF2cT2d4P2i3sfHfQ7QWLUl/t+G35BoJbwyFJV2eXvK6n6cSYZhvlO0J+rTCr47tTRUSVdicp3Ysly6MVpFuLELynDWlpRlquDNgX9LATLbyu3XDecI0BE3DqeAPPEU9cNuXFk2W946qSSqjhn4zEDT6vRsDnGvPit+BAw2Vu38Kb7He/h7sG/a/369VfE/e9FuHf3bm4Jo/cmwu7SdZkh9/ANGzb09zMOH7+/io9jJMdciXfcsKdiVDkw55acP62S81xfVtJ7zb77WMm10Ppzhp6ynnszVNVyBUCVNmvslC4nf1xcMnzTOO/Hg86NsuwbFf/DQRfGPTmhpEeW/IGKbcTg4yVDUnnTL+4y4b+xcfuptob4XJdaTkuxn5rPicXGfWOXyxpdEtd/X9DmEOKPhDBfGttDYvvCUM3fGPzMcm3x+n67HIpjdpwR99vifkGfDl4q6PPiWa+I5z5q0CYmL9PQMCfYmYjkqujmEG+OCtWv6RXbg6KnkgKKjd2ngkKrAKo0gawgODUIhYe/TgS5S5TpThqSqPCHxDF3HmzVzVHpa7ZVHv2ZgTdkx94mFFTgnh+Ar2PPVPqqonsPdT64lWAOH9Tye8f97nO7BP6ooP2WIuCj9+j+Gh9RdMp3v9jeN+5JK6GqS5NsgYq9ggTeTDXhblgqONt4ik8o6Xyr6qx48NOi8j1c5VLZOZZQqKo9LQUTFfWUkvcDfgE96tjDbeVQKjeV+StRhjtHw/OzuLf84AfGvq/F/9R1DREthDrLln77wNNKziiprhsWpKG4Ls3gN0oGm5xf0t7H/0ucIx+5+32DkEev/X3Tb0OYjwy+V5nj3g9ZiIBrkMbk2QcBF9WmHJ+O3/wD1wddG//jr/XfVK5osssJ19CwVAha+feSIZ8CPwwVGdeWqfVFDlA5VeaxcKOo+KPLLAwTlZWq/E8lc8wJpmE7KwPHnwgySRDuEXRVCNXFBDzu+6Wgf4vyUJ8vjbJdFaQXf09c+6rY3jvIemtXTeVa4a5n/wldpk7eGb8FsjBPvlIybbJG4aogyxLLWnN1nEtNPjzucWUI9s649/bgPxHb/1g363Qr88H7qsdUqkIexDHoud8W+4zfG+KTAprW8umgq4P6deCbcDcsF8aQRb31avtUOq+6DRs2bJzKZPsbh97qgBDwO4Rw7RW0NWhLCHi3FCGfD50aPQvjwN0gEIdEOQ5UjhC2rVGOQzU28dvUTmmHHXPAVC4EgeeBtoYZO11GUsKyb5ceawLMWedeHHiG0oAdX9f3Wh/XnB4EU2acXwqyeIQloCTSsNbbbEkn4L9BoJ1roQrneRYLFljEwP/MESMC9v/yVC4LrLzmpjI36vppDQ0rh9prBJ0YvOWCXhKV8TiqcQjzB0OwHjw9Pf3ToCtDyPcZBG2XKokGIanq6JIxXIP6fUGU4fVxn21RhhuCriPkcfsrY3tj7L9rCNHHgn4Q939AbM+JfZZpMvuN2SFo5VlBT4/r/FfsPzNIdNuHS86+u8k963NUQR0auDl74/GzVhqde4r3Fr//OniLFwjA6RuskuP0r43y8xfIzbZ9KlMlL/u9NTTMi1HlfeaQ+um1se8R0XP+OCrjB0OoHmwoJ/77YvzeR8Wvlb3SZIWfD5PHTQrJQGKtec3fFPcyjPTDuPf1ce+7RVm+Yo2uKJ+VUy2o+KPY7ojf7xjKbgUYQ383xjVMrzw5zrH/NVM5zdKIwUsny+O+VbgnBXzy+ebC6Bonxzk/Cv71se3fW/CnB22Lw/42jjszrmuJoSrg/fJT8123oWHZqJV7KpctpqKbH03F5GS6a9D6oF8NYpOaL02VR2ArMb+eSM+En3PNtIVAWaYyFfQBcZ9Ncb8uBPvgENKDBj9An2plaJQsq2uJZeozNVrZmRtGAbZ1uVQydX1bbKnAyiXPevXaLwhVALtU+V2PaWPrPlT82w/3YmJI4MhZdtBeuT7cYVEma8T1Jk/wUmxJC8VT3id4bGhYdRDwCZLvjTPr3KiYDwjhtsTtR6NiWrTw6tj/hfifM0ygyNe6XIHF+PTXu5yrvOReSaVXBj2o4JLq6GP742vAySDku7ZoaCAmNYKb7J+rXHX/XDT8D0JH318ynbT0S9eUDNaRTuqa2Pe8Lp16X+1yjNscgH8N/o2T5ajP2NBwq2FCEJ5Ycqjq4iDBItRiywwbv+3X1Qo6Moit67c837zghqqOnbj0olEFFxFmgl0FnYCPhXpMk0JEQOt2TItFl+B9Fz0nh/m7Sz7rs6fSJMCLlzc0Zzjs7JIJK6SYslhFXw7lbmj4uWEkHAJKOInutS7VXymNBMRswAfdHx//3z/oQVO54ikhV/l56OmeuijSNBe/1nRT5amSX8tZ99f/DGmZXkrdp708ZCqXHpoJevCwZQIIIuJHYAqY2urYJTUsDVWRoScAAAP6SURBVA0rjtrbjGnUS1qw8ElBjw26Uwj9R2P7yTjGsNQ7u1zeV1CHGV/i383iOn3gzfh6xsCbxbXrfnVb+Xrfyrs31F568ph67lKo5Piz6bRmbgmZvahkskmRbu8tOdXWyieeyfNJGPHSoMvj/o+JMh0fWsY5sX208oyu29CwNjGPcCOrkZ4R9GfBc8D1EyXiOCufCtbwm4pOQPCE+p0DLwBFJpiqwv7cMQij4TNlYm4IgDGH3m/BN+xs/MPKMLkljjHHnOAzYU6Nd/GM6enpr4WQn1oboDkakYaGtY1BwG8fdI+gu69Ldf34oEdNpZdab22BBIEkZodJqSTunADhDys58wtvvvPPFSMB5A2XCumYIKbGw4N+3f6SGWDY0qa27oh9jw4SRcfRZkjOexDA8tAQ9LvxF3hPDQ23SdSefD7P9Qg8zjXBxLMG3rxzGWXwJ5SMff9AyZlgxqgvLDl91VRO+wWGmCgidlzACBv47OE4DcWrh+PYuUJs8XpaOdjwQnE1OI5Xhh0Dz2SQz0ziitd1ObHlvKA3x3PcKejsoLfH/pk4Tgis8vbzyz1jpardjJ2BqKHhNo0q2Lcg4FeUVG2lTSKc+OeXnJeNl3FFRhY8YSSIeOf1XvuSyf3rJBRkkYfvD/zRQV8eeBqBABa8Oe5vGXgRbacN/Lklh7RMQrm0ZL4z6vbOKLuY+Lq+25HD8f7TS39z+K1MNxPwsQnTPOQNezQmBNxkEon+9bqSMMjOarqkHh1vjFh2FceY5bUtSGipRRUOLjkppar75ndrKPB6Zf85Rlaap5bMAqPnNiNOj69RIOicfe6BJ8TK8uySmoPQUQs7/H4I6YFBzwxyH88hqeQfBLmf4cKTy9Jyyzc0NAzYu6S9zu7dVjL18wtKCqLppK8YeHHkEj1QnzUUeOr6SfgQSur6I2NrP6HX87oWxxjjWHooqZ56TPbG4x56osFqaGhYBgidnp7zbUdJtVgqqV59HoiKLltqVdH7udolVfSLBl4v/taB5wdgb/9fmV1HnNDr2XuMhXkuAW+C3tAwByZt8vn4CeiVqcVbS9rqbHbqN0F9Wcke3Pj5K4dj2fB6ag6wJw28HlzeObze+5iSyR6o9tKi3qWket9DOeu2er9rbz7+v6GhoaGhoaGhoeEXHGMVf2wHL4SHsWq9G5OgoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaGhoaFhDeL/AbL/6dpoj+OHAAAAAElFTkSuQmCC",
        width: "248",
        height: "248",
        style: { mixBlendMode: "multiply" }
      }
    ),
    /* @__PURE__ */ React.createElement("rect", { x: "184.055", y: "54.995", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "170.059", y: "44.06", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "200.238", y: "77.302", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "212.048", y: "87.8", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "206.799", y: "83.425", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "204.175", y: "85.612", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "219.046", y: "103.108", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "154.751", y: "30.064", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "188.866", y: "63.742", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "148.189", y: "34", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "134.051", y: "31.707", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "126.124", y: "24.771", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "115.385", y: "29.19", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "95.702", y: "31.376", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "91.766", y: "27.002", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "90.454", y: "32.688", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "184.389", y: "45.58", width: "2.187", height: "2.187" }),
    /* @__PURE__ */ React.createElement("rect", { x: "162.185", y: "41.873", width: "2.187", height: "2.187" })
  ));
}

// routes/connectors-home/ai-plugin-callout.tsx
var AI_PLUGIN_SLUG = "ai";
var AI_PLUGIN_PAGE_SLUG = "ai-wp-admin";
var AI_PLUGIN_ID = "ai/ai";
var AI_PLUGIN_URL = "https://wordpress.org/plugins/ai/";
var connectorDataValues = Object.values(getConnectorData());
var hasAiProviders = connectorDataValues.some(
  (c) => c.type === "ai_provider"
);
var aiProviderSettingNames = [];
for (const c of connectorDataValues) {
  if (c.type === "ai_provider" && c.authentication.method === "api_key") {
    aiProviderSettingNames.push(c.authentication.settingName);
  }
}
function AiPluginCallout() {
  const [isBusy, setIsBusy] = (0, import_element31.useState)(false);
  const [justActivated, setJustActivated] = (0, import_element31.useState)(false);
  const actionButtonRef = (0, import_element31.useRef)(null);
  (0, import_element31.useEffect)(() => {
    if (justActivated) {
      actionButtonRef.current?.focus();
    }
  }, [justActivated]);
  const initialHasConnectedProvider = (0, import_element31.useRef)(
    connectorDataValues.some(
      (c) => c.type === "ai_provider" && c.authentication.method === "api_key" && c.authentication.isConnected
    )
  ).current;
  const {
    pluginStatus,
    canInstallPlugins,
    canManagePlugins,
    hasConnectedProvider
  } = (0, import_data3.useSelect)((select2) => {
    const store2 = select2(import_core_data2.store);
    const canCreate = !!store2.canUser("create", {
      kind: "root",
      name: "plugin"
    });
    const siteSettings = store2.getEntityRecord("root", "site");
    const hasConnected = initialHasConnectedProvider || aiProviderSettingNames.some(
      (name) => !!siteSettings?.[name]
    );
    const plugin = store2.getEntityRecord(
      "root",
      "plugin",
      AI_PLUGIN_ID
    );
    const hasFinished = store2.hasFinishedResolution("getEntityRecord", [
      "root",
      "plugin",
      AI_PLUGIN_ID
    ]);
    if (!hasFinished) {
      return {
        pluginStatus: "checking",
        canInstallPlugins: canCreate,
        canManagePlugins: void 0,
        hasConnectedProvider: hasConnected
      };
    }
    if (!plugin) {
      return {
        pluginStatus: "not-installed",
        canInstallPlugins: canCreate,
        canManagePlugins: canCreate,
        hasConnectedProvider: hasConnected
      };
    }
    return {
      pluginStatus: plugin.status === "active" ? "active" : "inactive",
      canInstallPlugins: canCreate,
      canManagePlugins: true,
      hasConnectedProvider: hasConnected
    };
  }, []);
  const { saveEntityRecord } = (0, import_data3.useDispatch)(import_core_data2.store);
  const { createSuccessNotice, createErrorNotice } = (0, import_data3.useDispatch)(import_notices2.store);
  const installPlugin = async () => {
    setIsBusy(true);
    try {
      await saveEntityRecord(
        "root",
        "plugin",
        { slug: AI_PLUGIN_SLUG, status: "active" },
        { throwOnError: true }
      );
      setJustActivated(true);
      createSuccessNotice(
        (0, import_i18n6.__)("AI plugin installed and activated successfully."),
        {
          id: "ai-plugin-install-success",
          type: "snackbar"
        }
      );
    } catch {
      createErrorNotice((0, import_i18n6.__)("Failed to install the AI plugin."), {
        id: "ai-plugin-install-error",
        type: "snackbar"
      });
    } finally {
      setIsBusy(false);
    }
  };
  const activatePlugin = async () => {
    setIsBusy(true);
    try {
      await saveEntityRecord(
        "root",
        "plugin",
        { plugin: AI_PLUGIN_ID, status: "active" },
        { throwOnError: true }
      );
      setJustActivated(true);
      createSuccessNotice((0, import_i18n6.__)("AI plugin activated successfully."), {
        id: "ai-plugin-activate-success",
        type: "snackbar"
      });
    } catch {
      createErrorNotice((0, import_i18n6.__)("Failed to activate the AI plugin."), {
        id: "ai-plugin-activate-error",
        type: "snackbar"
      });
    } finally {
      setIsBusy(false);
    }
  };
  if (!hasAiProviders) {
    return null;
  }
  if (pluginStatus === "checking") {
    return null;
  }
  if (pluginStatus === "active" && initialHasConnectedProvider && !justActivated) {
    return null;
  }
  if (pluginStatus === "inactive" && canManagePlugins === false) {
    return null;
  }
  const isActiveNoProvider = pluginStatus === "active" && !hasConnectedProvider;
  const isJustConnected = pluginStatus === "active" && hasConnectedProvider && (!initialHasConnectedProvider || justActivated);
  const showInstallActivate = pluginStatus === "not-installed" || pluginStatus === "inactive";
  const hideButtons = pluginStatus === "not-installed" && canInstallPlugins === false;
  const getMessage = () => {
    if (isJustConnected) {
      return (0, import_i18n6.__)(
        "The <strong>AI plugin</strong> is ready to use. You can use it to generate featured images, alt text, titles, excerpts and more. <a>Learn more</a>"
      );
    }
    if (isActiveNoProvider) {
      return (0, import_i18n6.__)(
        "The <strong>AI plugin</strong> is installed. Connect an AI provider below to generate featured images, alt text, titles, excerpts, and more. <a>Learn more</a>"
      );
    }
    return (0, import_i18n6.__)(
      "The <strong>AI plugin</strong> can use your AI connectors to generate featured images, alt text, titles, excerpts and more. <a>Learn more</a>"
    );
  };
  const getPrimaryButtonProps = () => {
    if (pluginStatus === "not-installed") {
      return {
        label: isBusy ? (0, import_i18n6.__)("Installing\u2026") : (0, import_i18n6.__)("Install the AI plugin"),
        disabled: isBusy,
        onClick: isBusy ? void 0 : installPlugin
      };
    }
    return {
      label: isBusy ? (0, import_i18n6.__)("Activating\u2026") : (0, import_i18n6.__)("Activate the AI plugin"),
      disabled: isBusy,
      onClick: isBusy ? void 0 : activatePlugin
    };
  };
  return /* @__PURE__ */ React.createElement("div", { className: "ai-plugin-callout" }, /* @__PURE__ */ React.createElement("div", { className: "ai-plugin-callout__content" }, /* @__PURE__ */ React.createElement("p", null, (0, import_element31.createInterpolateElement)(getMessage(), {
    strong: /* @__PURE__ */ React.createElement("strong", null),
    // @ts-ignore children are injected by createInterpolateElement at runtime.
    a: /* @__PURE__ */ React.createElement(import_components3.ExternalLink, { href: AI_PLUGIN_URL })
  })), !hideButtons && (showInstallActivate ? /* @__PURE__ */ React.createElement(
    import_components3.Button,
    {
      variant: "primary",
      size: "compact",
      isBusy,
      disabled: getPrimaryButtonProps().disabled,
      accessibleWhenDisabled: true,
      onClick: getPrimaryButtonProps().onClick
    },
    getPrimaryButtonProps().label
  ) : /* @__PURE__ */ React.createElement(
    import_components3.Button,
    {
      ref: actionButtonRef,
      variant: "secondary",
      size: "compact",
      href: (0, import_url.addQueryArgs)("options-general.php", {
        page: AI_PLUGIN_PAGE_SLUG
      })
    },
    (0, import_i18n6.__)("Control features in the AI plugin")
  ))), /* @__PURE__ */ React.createElement(WpLogoDecoration, null));
}

// routes/connectors-home/stage.tsx
var { store } = unlock2(connectorsPrivateApis2);
registerDefaultConnectors();
function ConnectorsPage() {
  const isFileModDisabled = getIsFileModDisabled();
  const { connectors, canInstallPlugins, isAiPluginInstalled } = (0, import_data4.useSelect)(
    (select2) => {
      const coreSelect = select2(import_core_data3.store);
      const aiPlugin = coreSelect.getEntityRecord(
        "root",
        "plugin",
        "ai/ai"
      );
      return {
        connectors: unlock2(select2(store)).getConnectors(),
        canInstallPlugins: coreSelect.canUser("create", {
          kind: "root",
          name: "plugin"
        }),
        isAiPluginInstalled: !!aiPlugin
      };
    },
    []
  );
  const renderableConnectors = connectors.filter(
    (connector) => connector.render
  );
  const aiProviderPluginSlugs = Array.from(
    new Set(
      connectors.filter(
        (connector) => connector.type === "ai_provider"
      ).map(
        (connector) => connector.plugin?.file?.split("/")[0]
      ).filter((slug) => !!slug)
    )
  ).sort();
  const installedPluginSlugs = new Set(
    connectors.filter(
      (connector) => connector.plugin?.isInstalled
    ).map(
      (connector) => connector.plugin?.file?.split("/")[0]
    ).filter((slug) => !!slug)
  );
  if (isAiPluginInstalled) {
    installedPluginSlugs.add("ai");
  }
  const manualInstallPluginSlugs = ["ai", ...aiProviderPluginSlugs].filter(
    (slug) => !installedPluginSlugs.has(slug)
  );
  const isEmpty = renderableConnectors.length === 0;
  return /* @__PURE__ */ React.createElement(
    page_default,
    {
      title: (0, import_i18n7.__)("Connectors"),
      subTitle: (0, import_i18n7.__)(
        "All of your API keys and credentials are stored here and shared across plugins. Configure once and use everywhere."
      )
    },
    /* @__PURE__ */ React.createElement(
      "div",
      {
        className: `connectors-page${isEmpty ? " connectors-page--empty" : ""}`
      },
      manualInstallPluginSlugs.length > 0 && (isFileModDisabled || !canInstallPlugins) && /* @__PURE__ */ React.createElement(
        notice_exports.Root,
        {
          intent: "info",
          className: "connectors-page__file-mods-notice"
        },
        /* @__PURE__ */ React.createElement(notice_exports.Description, null, isFileModDisabled ? (0, import_i18n7.__)(
          "Plugins cannot be installed here due to your site configuration. Install them manually using your normal deployment workflow."
        ) : (0, import_i18n7.__)(
          "You do not have permission to install plugins. Please ask a site administrator to install them for you."
        ))
      ),
      isEmpty ? /* @__PURE__ */ React.createElement(
        import_components4.__experimentalVStack,
        {
          alignment: "center",
          spacing: 3,
          style: { maxWidth: 480 }
        },
        /* @__PURE__ */ React.createElement(import_components4.__experimentalVStack, { alignment: "center", spacing: 2 }, /* @__PURE__ */ React.createElement(import_components4.__experimentalHeading, { level: 2, size: 15, weight: 600 }, (0, import_i18n7.__)("No connectors yet")), /* @__PURE__ */ React.createElement(import_components4.__experimentalText, { size: 12 }, (0, import_i18n7.__)(
          "Connectors appear here when you install plugins that use external services. Each plugin registers the API keys it needs, and you manage them all in one place."
        ))),
        /* @__PURE__ */ React.createElement(import_components4.Button, { variant: "secondary", href: "plugin-install.php" }, (0, import_i18n7.__)("Learn more"))
      ) : /* @__PURE__ */ React.createElement(import_components4.__experimentalVStack, { spacing: 3 }, /* @__PURE__ */ React.createElement(AiPluginCallout, null), /* @__PURE__ */ React.createElement(import_components4.__experimentalVStack, { spacing: 3, role: "list" }, connectors.map(
        (connector) => {
          if (connector.render) {
            return /* @__PURE__ */ React.createElement(
              connector.render,
              {
                key: connector.slug,
                slug: connector.slug,
                name: connector.name,
                description: connector.description,
                type: connector.type,
                logo: connector.logo,
                authentication: connector.authentication,
                plugin: connector.plugin
              }
            );
          }
          return null;
        }
      ))),
      canInstallPlugins && !isFileModDisabled && /* @__PURE__ */ React.createElement("p", null, (0, import_element32.createInterpolateElement)(
        (0, import_i18n7.__)(
          "If the connector you need is not listed, <a>search the plugin directory</a> to see if a connector is available."
        ),
        {
          a: (
            // eslint-disable-next-line jsx-a11y/anchor-has-content
            /* @__PURE__ */ React.createElement("a", { href: "plugin-install.php?s=connector&tab=search&type=tag" })
          )
        }
      ))
    )
  );
}
function Stage() {
  return /* @__PURE__ */ React.createElement(ConnectorsPage, null);
}
var stage = Stage;
export {
  stage
};
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
