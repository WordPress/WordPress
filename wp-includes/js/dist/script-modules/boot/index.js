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

// package-external:@wordpress/element
var require_element = __commonJS({
  "package-external:@wordpress/element"(exports, module) {
    module.exports = window.wp.element;
  }
});

// package-external:@wordpress/data
var require_data = __commonJS({
  "package-external:@wordpress/data"(exports, module) {
    module.exports = window.wp.data;
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
        didWarnOld18Alpha || void 0 === React47.startTransition || (didWarnOld18Alpha = true, console.error(
          "You are using an outdated, pre-release alpha of React 18 that does not support useSyncExternalStore. The use-sync-external-store shim will not work correctly. Upgrade to a newer pre-release."
        ));
        var value = getSnapshot();
        if (!didWarnUncachedGetSnapshot) {
          var cachedValue = getSnapshot();
          objectIs(value, cachedValue) || (console.error(
            "The result of getSnapshot should be cached to avoid an infinite loop"
          ), didWarnUncachedGetSnapshot = true);
        }
        cachedValue = useState19({
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
        useEffect18(
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
        } catch (error) {
          return true;
        }
      }
      function useSyncExternalStore$1(subscribe, getSnapshot) {
        return getSnapshot();
      }
      "undefined" !== typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ && "function" === typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());
      var React47 = require_react(), objectIs = "function" === typeof Object.is ? Object.is : is, useState19 = React47.useState, useEffect18 = React47.useEffect, useLayoutEffect3 = React47.useLayoutEffect, useDebugValue2 = React47.useDebugValue, didWarnOld18Alpha = false, didWarnUncachedGetSnapshot = false, shim = "undefined" === typeof window || "undefined" === typeof window.document || "undefined" === typeof window.document.createElement ? useSyncExternalStore$1 : useSyncExternalStore$2;
      exports.useSyncExternalStore = void 0 !== React47.useSyncExternalStore ? React47.useSyncExternalStore : shim;
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
      var React47 = require_react(), shim = require_shim(), objectIs = "function" === typeof Object.is ? Object.is : is, useSyncExternalStore2 = shim.useSyncExternalStore, useRef22 = React47.useRef, useEffect18 = React47.useEffect, useMemo19 = React47.useMemo, useDebugValue2 = React47.useDebugValue;
      exports.useSyncExternalStoreWithSelector = function(subscribe, getSnapshot, getServerSnapshot, selector, isEqual) {
        var instRef = useRef22(null);
        if (null === instRef.current) {
          var inst = { hasValue: false, value: null };
          instRef.current = inst;
        } else inst = instRef.current;
        instRef = useMemo19(
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
        useEffect18(
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

// package-external:@wordpress/components
var require_components = __commonJS({
  "package-external:@wordpress/components"(exports, module) {
    module.exports = window.wp.components;
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

// package-external:@wordpress/keycodes
var require_keycodes = __commonJS({
  "package-external:@wordpress/keycodes"(exports, module) {
    module.exports = window.wp.keycodes;
  }
});

// package-external:@wordpress/editor
var require_editor = __commonJS({
  "package-external:@wordpress/editor"(exports, module) {
    module.exports = window.wp.editor;
  }
});

// package-external:@wordpress/keyboard-shortcuts
var require_keyboard_shortcuts = __commonJS({
  "package-external:@wordpress/keyboard-shortcuts"(exports, module) {
    module.exports = window.wp.keyboardShortcuts;
  }
});

// package-external:@wordpress/html-entities
var require_html_entities = __commonJS({
  "package-external:@wordpress/html-entities"(exports, module) {
    module.exports = window.wp.htmlEntities;
  }
});

// packages/boot/build-module/components/app/index.mjs
var import_element32 = __toESM(require_element(), 1);
var import_data10 = __toESM(require_data(), 1);

// packages/boot/build-module/components/app/router.mjs
var import_i18n10 = __toESM(require_i18n(), 1);
var import_element30 = __toESM(require_element(), 1);

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

// node_modules/@base-ui/utils/safeReact.mjs
var React = __toESM(require_react(), 1);
var SafeReact = {
  ...React
};

// node_modules/@base-ui/utils/useRefWithInit.mjs
var React2 = __toESM(require_react(), 1);
var UNINITIALIZED = {};
function useRefWithInit(init2, initArg) {
  const ref = React2.useRef(UNINITIALIZED);
  if (ref.current === UNINITIALIZED) {
    ref.current = init2(initArg);
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

// node_modules/@base-ui/utils/useIsoLayoutEffect.mjs
var React3 = __toESM(require_react(), 1);
var noop = () => {
};
var useIsoLayoutEffect = typeof document !== "undefined" ? React3.useLayoutEffect : noop;

// node_modules/@base-ui/utils/warn.mjs
var set;
if (true) {
  set = /* @__PURE__ */ new Set();
}
function warn(...messages) {
  if (true) {
    const messageKey = messages.join(" ");
    if (!set.has(messageKey)) {
      set.add(messageKey);
      console.warn(`Base UI: ${messageKey}`);
    }
  }
}

// node_modules/@base-ui/react/internals/direction-context/DirectionContext.mjs
var React4 = __toESM(require_react(), 1);
var DirectionContext = /* @__PURE__ */ React4.createContext(void 0);
if (true) DirectionContext.displayName = "DirectionContext";
function useDirection() {
  const context = React4.useContext(DirectionContext);
  return context?.direction ?? "ltr";
}

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

// node_modules/@base-ui/utils/empty.mjs
function NOOP() {
}
var EMPTY_ARRAY = Object.freeze([]);
var EMPTY_OBJECT = Object.freeze({});

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
      const children = React7.Children.toArray(render);
      newElement = children[0];
    }
    if (true) {
      if (!/* @__PURE__ */ React7.isValidElement(newElement)) {
        throw new Error(["Base UI: The `render` prop was provided an invalid React element as `React.isValidElement(render)` is `false`.", "A valid React element must be provided to the `render` prop because it is cloned with props to replace the default element.", "https://base-ui.com/r/invalid-render-prop"].join("\n"));
      }
    }
    return /* @__PURE__ */ React7.cloneElement(newElement, mergedProps);
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
var EMPTY = [];
function useOnMount(fn) {
  React9.useEffect(fn, EMPTY);
}

// node_modules/@base-ui/utils/useAnimationFrame.mjs
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

// node_modules/@base-ui/react/internals/useTransitionStatus.mjs
function useTransitionStatus(open, enableIdleState = false, deferEndingState = false) {
  const [transitionStatus, setTransitionStatus] = React10.useState(open && enableIdleState ? "idle" : void 0);
  const [mounted, setMounted] = React10.useState(open);
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

// node_modules/@base-ui/react/internals/stateAttributesMapping.mjs
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

// node_modules/@base-ui/utils/owner.mjs
function ownerDocument(node) {
  return node?.ownerDocument || document;
}

// node_modules/@base-ui/react/internals/useOpenChangeComplete.mjs
var React11 = __toESM(require_react(), 1);

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

// node_modules/@base-ui/react/internals/useOpenChangeComplete.mjs
function useOpenChangeComplete(parameters) {
  const {
    enabled = true,
    open,
    ref,
    onComplete: onCompleteParam
  } = parameters;
  const onComplete = useStableCallback(onCompleteParam);
  const runOnceAnimationsFinish = useAnimationsFinished(ref, open, false);
  React11.useEffect(() => {
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

// node_modules/@base-ui/utils/useOnFirstRender.mjs
var React12 = __toESM(require_react(), 1);
function useOnFirstRender(fn) {
  const ref = React12.useRef(true);
  if (ref.current) {
    ref.current = false;
    fn();
  }
}

// node_modules/@base-ui/utils/platform/parts.mjs
var parts_exports = {};
__export(parts_exports, {
  engine: () => engine_exports,
  env: () => env_exports,
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

// node_modules/@base-ui/utils/useTimeout.mjs
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

// node_modules/@base-ui/react/floating-ui-react/components/FloatingDelayGroup.mjs
var React13 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/floating-ui-react/utils/event.mjs
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

// node_modules/@base-ui/react/floating-ui-react/utils/constants.mjs
var FOCUSABLE_ATTRIBUTE = "data-base-ui-focusable";
var TYPEABLE_SELECTOR = "input:not([type='hidden']):not([disabled]),[contenteditable]:not([contenteditable='false']),textarea:not([disabled])";

// node_modules/@base-ui/react/internals/shadowDom.mjs
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

// node_modules/@base-ui/react/floating-ui-react/utils/element.mjs
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
var FloatingDelayGroupContext = /* @__PURE__ */ React13.createContext({
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
  const delayRef = React13.useRef(delay);
  const initialDelayRef = React13.useRef(delay);
  const currentIdRef = React13.useRef(null);
  const currentContextRef = React13.useRef(null);
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
    value: React13.useMemo(() => ({
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
    open
  } = options;
  const store2 = "rootStore" in context ? context.rootStore : context;
  const floatingId = store2.useState("floatingId");
  const groupContext = React13.useContext(FloatingDelayGroupContext);
  const {
    currentIdRef,
    delayRef,
    timeoutMs,
    initialDelayRef,
    currentContextRef,
    hasProvider,
    timeout
  } = groupContext;
  const [isInstantPhase, setIsInstantPhase] = React13.useState(false);
  const openRef = React13.useRef(open);
  const isUnmountedRef = React13.useRef(false);
  useIsoLayoutEffect(() => {
    openRef.current = open;
  }, [open]);
  useIsoLayoutEffect(() => {
    return () => {
      isUnmountedRef.current = true;
    };
  }, []);
  useIsoLayoutEffect(() => {
    function unset() {
      if (!isUnmountedRef.current) {
        setIsInstantPhase(false);
      }
      currentContextRef.current?.setIsInstantPhase(false);
      currentIdRef.current = null;
      currentContextRef.current = null;
      delayRef.current = initialDelayRef.current;
      timeout.clear();
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
          if (openRef.current || currentIdRef.current !== closingId) {
            timeout.clear();
          }
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
  }, [open, floatingId, store2, currentIdRef, delayRef, initialDelayRef, currentContextRef, timeout]);
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
  return React13.useMemo(() => ({
    hasProvider,
    delayRef,
    isInstantPhase
  }), [hasProvider, delayRef, isInstantPhase]);
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
var React14 = __toESM(require_react(), 1);

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
var FocusGuard = /* @__PURE__ */ React14.forwardRef(function FocusGuard2(props, ref) {
  const [role, setRole] = React14.useState();
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
var React15 = __toESM(require_react(), 1);
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
var PortalContext = /* @__PURE__ */ React15.createContext(null);
if (true) PortalContext.displayName = "PortalContext";
var usePortalContext = () => React15.useContext(PortalContext);
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
  const [containerElement, setContainerElement] = React15.useState(null);
  const [portalNode, setPortalNode] = React15.useState(null);
  const setPortalNodeRef = useStableCallback((node) => {
    if (node !== null) {
      setPortalNode(node);
    }
  });
  const containerRef = React15.useRef(null);
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
var FloatingPortal = /* @__PURE__ */ React15.forwardRef(function FloatingPortal2(componentProps, forwardedRef) {
  const {
    render,
    className,
    style,
    children,
    container,
    renderGuards,
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
  const beforeOutsideRef = React15.useRef(null);
  const afterOutsideRef = React15.useRef(null);
  const beforeInsideRef = React15.useRef(null);
  const afterInsideRef = React15.useRef(null);
  const [focusManagerState, setFocusManagerState] = React15.useState(null);
  const focusInsideDisabledRef = React15.useRef(false);
  const modal = focusManagerState?.modal;
  const open = focusManagerState?.open;
  const shouldRenderGuards = typeof renderGuards === "boolean" ? renderGuards : !!focusManagerState && !focusManagerState.modal && focusManagerState.open && !!portalNode;
  React15.useEffect(() => {
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
    if (!portalNode || open !== true || !focusInsideDisabledRef.current) {
      return;
    }
    enableFocusInside(portalNode);
    focusInsideDisabledRef.current = false;
  }, [open, portalNode]);
  const portalContextValue = React15.useMemo(() => ({
    beforeOutsideRef,
    afterOutsideRef,
    beforeInsideRef,
    afterInsideRef,
    portalNode,
    setFocusManagerState
  }), [portalNode]);
  return /* @__PURE__ */ (0, import_jsx_runtime3.jsxs)(React15.Fragment, {
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

// node_modules/@base-ui/react/floating-ui-react/components/FloatingTree.mjs
var React16 = __toESM(require_react(), 1);

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
var FloatingNodeContext = /* @__PURE__ */ React16.createContext(null);
if (true) FloatingNodeContext.displayName = "FloatingNodeContext";
var FloatingTreeContext = /* @__PURE__ */ React16.createContext(null);
if (true) FloatingTreeContext.displayName = "FloatingTreeContext";
var useFloatingParentNodeId = () => React16.useContext(FloatingNodeContext)?.id || null;
var useFloatingTree = (externalTree) => {
  const contextTree = React16.useContext(FloatingTreeContext);
  return externalTree ?? contextTree;
};

// node_modules/@base-ui/react/floating-ui-react/hooks/useClientPoint.mjs
var React17 = __toESM(require_react(), 1);
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
  const open = store2.useState("open");
  const floating = store2.useState("floatingElement");
  const domReference = store2.useState("domReferenceElement");
  const dataRef = store2.context.dataRef;
  const initialRef = React17.useRef(false);
  const cleanupListenerRef = React17.useRef(null);
  const [pointerType, setPointerType] = React17.useState();
  const [reactive, setReactive] = React17.useState([]);
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
    if (!open) {
      setReference(event.clientX, event.clientY, event.currentTarget);
    } else if (!cleanupListenerRef.current) {
      setReference(event.clientX, event.clientY, event.currentTarget);
      setReactive([]);
    }
  });
  const openCheck = isMouseLikePointerType(pointerType) ? floating : open;
  React17.useEffect(() => {
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
  React17.useEffect(() => () => {
    store2.set("positionReference", null);
  }, [store2]);
  React17.useEffect(() => {
    if (enabled && !floating) {
      initialRef.current = false;
    }
  }, [enabled, floating]);
  React17.useEffect(() => {
    if (!enabled && open) {
      initialRef.current = true;
    }
  }, [enabled, open]);
  const reference = React17.useMemo(() => {
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
  return React17.useMemo(() => enabled ? {
    reference,
    trigger: reference
  } : {}, [enabled, reference]);
}

// node_modules/@base-ui/react/floating-ui-react/hooks/useDismiss.mjs
var React18 = __toESM(require_react(), 1);
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
  const open = store2.useState("open");
  const floatingElement = store2.useState("floatingElement");
  const {
    dataRef
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
  const pressStartedInsideRef = React18.useRef(false);
  const pressStartPreventedRef = React18.useRef(false);
  const suppressNextOutsideClickRef = React18.useRef(false);
  const isComposingRef = React18.useRef(false);
  const currentPointerTypeRef = React18.useRef("");
  const touchStateRef = React18.useRef(null);
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
    if (!open || !enabled || !escapeKey2 || event.key !== "Escape") {
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
  React18.useEffect(() => {
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
        const isRTL6 = style.direction === "rtl";
        const pressedVerticalScrollbar = canScrollY && (isRTL6 ? event.offsetX <= target.offsetWidth - target.clientWidth : event.offsetX > target.clientWidth);
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
    const doc = ownerDocument(floatingElement);
    const unsubscribe = mergeCleanups(escapeKey2 && mergeCleanups(addEventListener(doc, "keydown", closeOnEscapeKeyDown), addEventListener(doc, "compositionstart", handleCompositionStart), addEventListener(doc, "compositionend", handleCompositionEnd)), outsidePressEnabled && mergeCleanups(addEventListener(doc, "click", closeOnPressOutsideCapture, true), addEventListener(doc, "pointerdown", closeOnPressOutsideCapture, true), addEventListener(doc, "pointerup", handlePressEndCapture, true), addEventListener(doc, "pointercancel", handlePressEndCapture, true), addEventListener(doc, "mousedown", closeOnPressOutsideCapture, true), addEventListener(doc, "mouseup", handlePressEndCapture, true), addEventListener(doc, "touchstart", handleTouchStartCapture, true), addEventListener(doc, "touchmove", handleTouchMoveCapture, true), addEventListener(doc, "touchend", handleTouchEndCapture, true)));
    return () => {
      unsubscribe();
      compositionTimeout.clear();
      preventedPressSuppressionTimeout.clear();
      resetPressStartState();
      suppressNextOutsideClickRef.current = false;
    };
  }, [dataRef, floatingElement, escapeKey2, outsidePressEnabled, outsidePress2, open, enabled, escapeKeyBubbles, outsidePressBubbles, closeOnEscapeKeyDown, clearInsideReactTree, getOutsidePressEventProp, hasBlockingChild, isEventWithinOwnElements, tree, store2, cancelDismissOnEndTimeout]);
  React18.useEffect(clearInsideReactTree, [outsidePress2, clearInsideReactTree]);
  const reference = React18.useMemo(() => ({
    onKeyDown: closeOnEscapeKeyDown,
    onPointerDown: closeOnReferencePress,
    onClick: closeOnReferencePress
  }), [closeOnEscapeKeyDown, closeOnReferencePress]);
  const floating = React18.useMemo(() => ({
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
  return React18.useMemo(() => enabled ? {
    reference,
    floating,
    trigger: reference
  } : {}, [enabled, reference, floating]);
}

// node_modules/@base-ui/react/floating-ui-react/hooks/useFloating.mjs
var React25 = __toESM(require_react(), 1);

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

// node_modules/@floating-ui/react-dom/dist/floating-ui.react-dom.mjs
var React19 = __toESM(require_react(), 1);
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
  const ref = React19.useRef(value);
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
  const [data, setData] = React19.useState({
    x: 0,
    y: 0,
    strategy,
    placement,
    middlewareData: {},
    isPositioned: false
  });
  const [latestMiddleware, setLatestMiddleware] = React19.useState(middleware);
  if (!deepEqual(latestMiddleware, middleware)) {
    setLatestMiddleware(middleware);
  }
  const [_reference, _setReference] = React19.useState(null);
  const [_floating, _setFloating] = React19.useState(null);
  const setReference = React19.useCallback((node) => {
    if (node !== referenceRef.current) {
      referenceRef.current = node;
      _setReference(node);
    }
  }, []);
  const setFloating = React19.useCallback((node) => {
    if (node !== floatingRef.current) {
      floatingRef.current = node;
      _setFloating(node);
    }
  }, []);
  const referenceEl = externalReference || _reference;
  const floatingEl = externalFloating || _floating;
  const referenceRef = React19.useRef(null);
  const floatingRef = React19.useRef(null);
  const dataRef = React19.useRef(data);
  const hasWhileElementsMounted = whileElementsMounted != null;
  const whileElementsMountedRef = useLatestRef(whileElementsMounted);
  const platformRef = useLatestRef(platform3);
  const openRef = useLatestRef(open);
  const update2 = React19.useCallback(() => {
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
  const isMountedRef = React19.useRef(false);
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
  const refs = React19.useMemo(() => ({
    reference: referenceRef,
    floating: floatingRef,
    setReference,
    setFloating
  }), [setReference, setFloating]);
  const elements = React19.useMemo(() => ({
    reference: referenceEl,
    floating: floatingEl
  }), [referenceEl, floatingEl]);
  const floatingStyles = React19.useMemo(() => {
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
  return React19.useMemo(() => ({
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

// node_modules/@base-ui/react/utils/popups/popupStoreUtils.mjs
var React24 = __toESM(require_react(), 1);
var ReactDOM4 = __toESM(require_react_dom(), 1);

// node_modules/@base-ui/react/floating-ui-react/hooks/useSyncedFloatingRootContext.mjs
var React23 = __toESM(require_react(), 1);

// node_modules/@base-ui/utils/store/createSelector.mjs
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

// node_modules/@base-ui/utils/store/useStore.mjs
var React21 = __toESM(require_react(), 1);
var import_shim = __toESM(require_shim(), 1);
var import_with_selector = __toESM(require_with_selector(), 1);

// node_modules/@base-ui/utils/fastHooks.mjs
var React20 = __toESM(require_react(), 1);
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
  return /* @__PURE__ */ React20.forwardRef(fastComponent(fn));
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
  const getSelection = React21.useCallback(() => selector(store2.getSnapshot(), a1, a2, a3), [store2, selector, a1, a2, a3]);
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

// node_modules/@base-ui/utils/store/ReactStore.mjs
var React22 = __toESM(require_react(), 1);
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
    React22.useDebugValue(key);
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
   *
   * Note that the while the values in `state` are updated immediately, the values returned
   * by `useState` are updated before the next render (similarly to React's `useState`).
   */
  useSyncedValues(statePart) {
    const store2 = this;
    if (true) {
      React22.useDebugValue(statePart, (p) => Object.keys(p));
      const keys = React22.useRef(Object.keys(statePart)).current;
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
    React22.useDebugValue(key);
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
    React22.useDebugValue(key);
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
    React22.useDebugValue(key);
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
    const ref = React22.useRef(void 0);
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
      ...initialState2
    } = options;
    super({
      ...initialState2,
      positionReference: initialState2.referenceElement,
      domReferenceElement: initialState2.referenceElement
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
  const open = popupStore.useState("open");
  const referenceElement = popupStore.useState("activeTriggerElement");
  const floatingElement = popupStore.useState(treatPopupAsFloatingElement ? "popupElement" : "positionerElement");
  const triggerElements = popupStore.context.triggerElements;
  const handleOpenChange = onOpenChange;
  const internalStoreRef = React23.useRef(null);
  if (floatingRootContextProp === void 0 && internalStoreRef.current === null) {
    internalStoreRef.current = new FloatingRootStore({
      open,
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
  store2.context.onOpenChange = handleOpenChange;
  store2.context.nested = nested;
  return store2;
}

// node_modules/@base-ui/react/utils/popups/popupStoreUtils.mjs
var FOCUSABLE_POPUP_PROPS = {
  tabIndex: -1,
  [FOCUSABLE_ATTRIBUTE]: ""
};
function usePopupStore(externalStore, createStore, treatPopupAsFloatingElement = false) {
  const floatingId = useId();
  const nested = useFloatingParentNodeId() != null;
  const internalStoreRef = React24.useRef(null);
  if (externalStore === void 0 && internalStoreRef.current === null) {
    internalStoreRef.current = createStore(floatingId, nested);
  }
  const store2 = externalStore ?? internalStoreRef.current;
  useSyncedFloatingRootContext({
    popupStore: store2,
    treatPopupAsFloatingElement,
    floatingRootContext: store2.state.floatingRootContext,
    floatingId,
    nested,
    onOpenChange: store2.setOpen
  });
  return {
    store: store2,
    internalStore: internalStoreRef.current
  };
}
function useTriggerRegistration(id, store2) {
  const registeredElementIdRef = React24.useRef(null);
  const registeredElementRef = React24.useRef(null);
  return React24.useCallback((element) => {
    if (id === void 0) {
      return;
    }
    let shouldSyncTriggerCount = false;
    if (registeredElementIdRef.current !== null) {
      const registeredId = registeredElementIdRef.current;
      const registeredElement = registeredElementRef.current;
      const currentElement = store2.context.triggerElements.getById(registeredId);
      if (registeredElement && currentElement === registeredElement) {
        store2.context.triggerElements.delete(registeredId);
        shouldSyncTriggerCount = true;
      }
      registeredElementIdRef.current = null;
      registeredElementRef.current = null;
    }
    if (element !== null) {
      registeredElementIdRef.current = id;
      registeredElementRef.current = element;
      store2.context.triggerElements.add(id, element);
      shouldSyncTriggerCount = true;
    }
    if (shouldSyncTriggerCount) {
      const triggerCount = store2.context.triggerElements.size;
      if (store2.select("open") && store2.state.triggerCount !== triggerCount) {
        store2.set("triggerCount", triggerCount);
      }
    }
  }, [store2, id]);
}
function setPopupOpenState(state, open, trigger, preventUnmountOnClose = false) {
  if (open) {
    state.preventUnmountingOnClose = false;
  } else if (preventUnmountOnClose) {
    state.preventUnmountingOnClose = true;
  }
  const triggerId = trigger?.id ?? null;
  if (triggerId || open) {
    state.activeTriggerId = triggerId;
    state.activeTriggerElement = trigger ?? null;
  }
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
    const updatedState = {
      ...options.extraState,
      open: nextOpen
    };
    if (isFocusOpen) {
      updatedState.instantType = "focus";
    } else if (isDismissClose) {
      updatedState.instantType = "dismiss";
    } else if (isHover) {
      updatedState.instantType = void 0;
    }
    setPopupOpenState(updatedState, nextOpen, eventDetails.trigger, shouldPreventUnmountOnClose());
    store2.update(updatedState);
  };
  if (isHover) {
    ReactDOM4.flushSync(changeState);
  } else {
    changeState();
  }
}
function useInitialOpenSync(store2, openProp, defaultOpen, defaultTriggerId) {
  useOnFirstRender(() => {
    if (openProp === void 0 && store2.state.open === false && defaultOpen) {
      store2.state = {
        ...store2.state,
        open: true,
        activeTriggerId: defaultTriggerId,
        preventUnmountingOnClose: false
      };
    }
  });
}
function useTriggerDataForwarding(triggerId, triggerElementRef, store2, stateUpdates) {
  const isMountedByThisTrigger = store2.useState("isMountedByTrigger", triggerId);
  const baseRegisterTrigger = useTriggerRegistration(triggerId, store2);
  const registerTrigger = useStableCallback((element) => {
    baseRegisterTrigger(element);
    if (!element) {
      return;
    }
    const open = store2.select("open");
    const activeTriggerId = store2.select("activeTriggerId");
    if (activeTriggerId === triggerId) {
      store2.update({
        activeTriggerElement: element,
        ...open ? stateUpdates : null
      });
      return;
    }
    if (activeTriggerId == null && open) {
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
function useImplicitActiveTrigger(store2, options = {}) {
  const {
    closeOnActiveTriggerUnmount = false
  } = options;
  const open = store2.useState("open");
  const reactiveTriggerCount = store2.useState("triggerCount");
  useIsoLayoutEffect(() => {
    if (!open) {
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
    const activeTriggerId = store2.select("activeTriggerId");
    let lostActiveTriggerId = null;
    if (activeTriggerId) {
      const activeTriggerElement = store2.context.triggerElements.getById(activeTriggerId);
      if (!activeTriggerElement) {
        lostActiveTriggerId = activeTriggerId;
      } else if (activeTriggerElement !== store2.state.activeTriggerElement) {
        stateUpdates.activeTriggerElement = activeTriggerElement;
      }
    }
    if (!lostActiveTriggerId && !activeTriggerId && triggerCount === 1) {
      const iteratorResult = store2.context.triggerElements.entries().next();
      if (!iteratorResult.done) {
        const [implicitTriggerId, implicitTriggerElement] = iteratorResult.value;
        stateUpdates.activeTriggerId = implicitTriggerId;
        stateUpdates.activeTriggerElement = implicitTriggerElement;
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
  }, [open, store2, reactiveTriggerCount, closeOnActiveTriggerUnmount]);
}
function useOpenStateTransitions(open, store2, onUnmount) {
  const {
    mounted,
    setMounted,
    transitionStatus
  } = useTransitionStatus(open);
  const preventUnmountingOnClose = store2.useState("preventUnmountingOnClose");
  const syncedPreventUnmountingOnClose = open ? false : preventUnmountingOnClose;
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
    enabled: mounted && !open && !syncedPreventUnmountingOnClose,
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

// node_modules/@base-ui/react/floating-ui-react/utils/getEmptyRootContext.mjs
function getEmptyRootContext() {
  return new FloatingRootStore({
    open: false,
    transitionStatus: void 0,
    floatingElement: null,
    referenceElement: null,
    triggerElements: new PopupTriggerMap(),
    floatingId: void 0,
    syncOnly: false,
    nested: false,
    onOpenChange: void 0
  });
}

// node_modules/@base-ui/react/utils/popups/store.mjs
function createInitialPopupStoreState() {
  return {
    open: false,
    openProp: void 0,
    mounted: false,
    transitionStatus: void 0,
    floatingRootContext: getEmptyRootContext(),
    floatingId: void 0,
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
function createPopupFloatingRootContext(triggerElements, floatingId, nested = false) {
  return new FloatingRootStore({
    open: false,
    transitionStatus: void 0,
    floatingElement: null,
    referenceElement: null,
    triggerElements,
    floatingId,
    syncOnly: true,
    nested,
    onOpenChange: void 0
  });
}
var activeTriggerIdSelector = createSelector((state) => state.triggerIdProp ?? state.activeTriggerId);
var openSelector = createSelector((state) => state.openProp ?? state.open);
var popupIdSelector = createSelector((state) => {
  const popupId = state.popupElement?.id ?? state.floatingId;
  return popupId || void 0;
});
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
  mounted: createSelector((state) => state.mounted),
  transitionStatus: createSelector((state) => state.transitionStatus),
  floatingRootContext: createSelector((state) => state.floatingRootContext),
  triggerCount: createSelector((state) => state.triggerCount),
  preventUnmountingOnClose: createSelector((state) => state.preventUnmountingOnClose),
  payload: createSelector((state) => state.payload),
  activeTriggerId: activeTriggerIdSelector,
  activeTriggerElement: createSelector((state) => state.mounted ? state.activeTriggerElement : null),
  popupId: popupIdSelector,
  /**
   * Whether the trigger with the given ID was used to open the popup.
   */
  isTriggerActive: createSelector((state, triggerId) => triggerId !== void 0 && activeTriggerIdSelector(state) === triggerId),
  /**
   * Whether the popup is open and was activated by a trigger with the given ID.
   */
  isOpenedByTrigger: createSelector((state, triggerId) => triggerOwnsOpenPopup(state, triggerId)),
  /**
   * Whether the popup is mounted and was activated by a trigger with the given ID.
   */
  isMountedByTrigger: createSelector((state, triggerId) => triggerId !== void 0 && activeTriggerIdSelector(state) === triggerId && state.mounted),
  triggerProps: createSelector((state, isActive) => isActive ? state.activeTriggerProps : state.inactiveTriggerProps),
  /**
   * Popup id for the trigger that currently owns the open popup.
   */
  triggerPopupId: createSelector((state, triggerId) => triggerOwnsOpenPopupOrIsOnlyTrigger(state, triggerId) ? popupIdSelector(state) : void 0),
  popupProps: createSelector((state) => state.popupProps),
  popupElement: createSelector((state) => state.popupElement),
  positionerElement: createSelector((state) => state.positionerElement)
};

// node_modules/@base-ui/react/floating-ui-react/hooks/useFloatingRootContext.mjs
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

// node_modules/@base-ui/react/floating-ui-react/hooks/useFloating.mjs
function useFloating2(options = {}) {
  const {
    nodeId,
    externalTree
  } = options;
  const internalStore = useFloatingRootContext(options);
  const store2 = options.rootContext || internalStore;
  const referenceElement = store2.useState("referenceElement");
  const floatingElement = store2.useState("floatingElement");
  const domReferenceElement = store2.useState("domReferenceElement");
  const open = store2.useState("open");
  const floatingId = store2.useState("floatingId");
  const [positionReference, setPositionReferenceRaw] = React25.useState(null);
  const [localDomReference, setLocalDomReference] = React25.useState(void 0);
  const [localFloatingElement, setLocalFloatingElement] = React25.useState(void 0);
  const domReferenceRef = React25.useRef(null);
  const tree = useFloatingTree(externalTree);
  const storeElements = React25.useMemo(() => ({
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
  const setPositionReference = React25.useCallback((node) => {
    const computedPositionReference = isElement(node) ? {
      getBoundingClientRect: () => node.getBoundingClientRect(),
      getClientRects: () => node.getClientRects(),
      contextElement: node
    } : node;
    setPositionReferenceRaw(computedPositionReference);
    position.refs.setReference(computedPositionReference);
  }, [position.refs]);
  const setReference = React25.useCallback((node) => {
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
  const setFloating = React25.useCallback((node) => {
    setLocalFloatingElement(node);
    position.refs.setFloating(node);
  }, [position.refs]);
  const refs = React25.useMemo(() => ({
    ...position.refs,
    setReference,
    setFloating,
    setPositionReference,
    domReference: domReferenceRef
  }), [position.refs, setReference, setFloating, setPositionReference]);
  const elements = React25.useMemo(() => ({
    ...position.elements,
    domReference: domReferenceElement
  }), [position.elements, domReferenceElement]);
  const context = React25.useMemo(() => ({
    ...position,
    dataRef: store2.context.dataRef,
    open,
    onOpenChange: store2.setOpen,
    events: store2.context.events,
    floatingId,
    refs,
    elements,
    nodeId,
    rootStore: store2
  }), [position, refs, elements, nodeId, store2, open, floatingId]);
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
  return React25.useMemo(() => ({
    ...position,
    context,
    refs,
    elements,
    rootStore: store2
  }), [position, refs, elements, context, store2]);
}

// node_modules/@base-ui/react/floating-ui-react/hooks/useFocus.mjs
var React26 = __toESM(require_react(), 1);
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
  const blockFocusRef = React26.useRef(false);
  const blockedReferenceRef = React26.useRef(null);
  const keyboardModalityRef = React26.useRef(true);
  const timeout = useTimeout();
  React26.useEffect(() => {
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
  React26.useEffect(() => {
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
  const reference = React26.useMemo(() => {
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
  return React26.useMemo(() => enabled ? {
    reference,
    trigger: reference
  } : {}, [enabled, reference]);
}

// node_modules/@base-ui/react/floating-ui-react/hooks/useHoverFloatingInteraction.mjs
var React27 = __toESM(require_react(), 1);

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
  const open = store2.useState("open");
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
    if (!open) {
      instance.pointerType = void 0;
      instance.restTimeoutPending = false;
      instance.interactedInside = false;
      clearPointerEvents();
    }
  }, [open, instance, clearPointerEvents]);
  React27.useEffect(() => {
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
  }, [enabled, open, domReferenceElement, floatingElement, instance, isHoverOpen, tree, parentId, clearPointerEvents]);
  React27.useEffect(() => {
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
var React28 = __toESM(require_react(), 1);
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
    shouldOpen: shouldOpenProp
  } = props;
  const store2 = "rootStore" in context ? context.rootStore : context;
  const {
    dataRef,
    events
  } = store2.context;
  const tree = useFloatingTree(externalTree);
  const instance = useHoverInteractionSharedState(store2);
  const isHoverCloseActiveRef = React28.useRef(false);
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
    instance.handleCloseOptions = handleCloseRef.current?.__options;
  }
  React28.useEffect(() => cleanupMouseMoveHandler, [cleanupMouseMoveHandler]);
  React28.useEffect(() => {
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
  React28.useEffect(() => {
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
    if (move) {
      return mergeCleanups(addEventListener(trigger, "mousemove", onMouseEnter, {
        once: true
      }), addEventListener(trigger, "mouseenter", onMouseEnter), addEventListener(trigger, "mouseleave", onMouseLeave));
    }
    return mergeCleanups(addEventListener(trigger, "mouseenter", onMouseEnter), addEventListener(trigger, "mouseleave", onMouseLeave));
  }, [cleanupMouseMoveHandler, clearPointerEvents, dataRef, delayRef, store2, enabled, handleCloseRef, instance, isActiveTrigger, isOverInactiveTrigger, isClickLikeOpenEvent2, mouseOnly, move, restMsRef, triggerElementRef, tree, enabledRef, getHandleCloseContext, isClosingRef, checkShouldOpen]);
  return React28.useMemo(() => {
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

// node_modules/@base-ui/react/utils/popupStateMapping.mjs
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

// node_modules/@base-ui/utils/inertValue.mjs
function inertValue(value) {
  if (isReactVersionAtLeast(19)) {
    return value;
  }
  return value ? "true" : void 0;
}

// node_modules/@base-ui/react/utils/useAnchorPositioning.mjs
var React29 = __toESM(require_react(), 1);

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
var arrow4 = (options, deps) => ({
  ...baseArrow(options),
  options: [options, deps]
});

// node_modules/@base-ui/react/utils/hideMiddleware.mjs
var nativeHideFn = hide3().fn;
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
    const nativeHideResult = await nativeHideFn(state);
    return {
      data: {
        referenceHidden: nativeHideResult.data?.referenceHidden || anchorHidden
      }
    };
  }
};

// node_modules/@base-ui/react/utils/adaptiveOriginMiddleware.mjs
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

// node_modules/@base-ui/react/utils/useAnchorPositioning.mjs
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
    inline: inlineMiddleware,
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
  const [mountSide, setMountSide] = React29.useState(null);
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
  const arrowRef = React29.useRef(null);
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
  }), arrow4((state) => ({
    // `transform-origin` calculations rely on an element existing. If the arrow hasn't been set,
    // we'll create a fake element.
    element: arrowRef.current || ownerDocument(state.elements.floating).createElement("div"),
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
  const autoUpdateOptions = React29.useMemo(() => ({
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
  const floatingStyles = React29.useMemo(() => {
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
  const registeredPositionReferenceRef = React29.useRef(null);
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
  React29.useEffect(() => {
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
  React29.useEffect(() => {
    if (keepMounted && mounted && elements.reference && elements.floating) {
      return autoUpdate(elements.reference, elements.floating, update2, autoUpdateOptions);
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
  const arrowStyles = React29.useMemo(() => ({
    position: "absolute",
    top: middlewareData.arrow?.y,
    left: middlewareData.arrow?.x
  }), [middlewareData.arrow]);
  const arrowUncentered = middlewareData.arrow?.centerOffset !== 0;
  return React29.useMemo(() => ({
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

// node_modules/@base-ui/react/utils/getDisabledMountTransitionStyles.mjs
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

// node_modules/@base-ui/react/utils/usePopupViewport.mjs
var React32 = __toESM(require_react(), 1);
var ReactDOM6 = __toESM(require_react_dom(), 1);

// node_modules/@base-ui/utils/usePreviousValue.mjs
var React30 = __toESM(require_react(), 1);
function usePreviousValue(value) {
  const [state, setState] = React30.useState({
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

// node_modules/@base-ui/react/utils/usePopupAutoResize.mjs
var React31 = __toESM(require_react(), 1);

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
  const runOnceAnimationsFinish = useAnimationsFinished(popupElement, true, false);
  const animationFrame = useAnimationFrame();
  const committedDimensionsRef = React31.useRef(null);
  const isInitialRenderRef = React31.useRef(true);
  const restoreAnchoringStylesRef = React31.useRef(NOOP);
  const onMeasureLayout = useStableCallback(onMeasureLayoutParam);
  const onMeasureLayoutComplete = useStableCallback(onMeasureLayoutCompleteParam);
  const anchoringStyles = React31.useMemo(() => {
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
        popupElement.style.setProperty("--popup-width", "auto");
        popupElement.style.setProperty("--popup-height", "auto");
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

// node_modules/@base-ui/react/utils/usePopupViewport.mjs
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
  const capturedNodeRef = React32.useRef(null);
  const [previousContentNode, setPreviousContentNode] = React32.useState(null);
  const [newTriggerOffset, setNewTriggerOffset] = React32.useState(null);
  const currentContainerRef = React32.useRef(null);
  const previousContainerRef = React32.useRef(null);
  const onAnimationsFinished = useAnimationsFinished(currentContainerRef, true, false);
  const cleanupFrame = useAnimationFrame();
  const [previousContentDimensions, setPreviousContentDimensions] = React32.useState(null);
  const [showStartingStyleAttribute, setShowStartingStyleAttribute] = React32.useState(false);
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
  const lastHandledTriggerRef = React32.useRef(null);
  useIsoLayoutEffect(() => {
    if (!open || !mounted) {
      lastHandledTriggerRef.current = null;
    }
  }, [open, mounted]);
  useIsoLayoutEffect(() => {
    if (activeTrigger && previousActiveTrigger && activeTrigger !== previousActiveTrigger && lastHandledTriggerRef.current !== activeTrigger && capturedNodeRef.current) {
      setPreviousContentNode(capturedNodeRef.current);
      setShowStartingStyleAttribute(true);
      const offset4 = calculateRelativePosition(previousActiveTrigger, activeTrigger);
      setNewTriggerOffset(offset4);
      cleanupFrame.request(() => {
        ReactDOM6.flushSync(() => {
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
    childrenToRender = /* @__PURE__ */ (0, import_jsx_runtime5.jsxs)(React32.Fragment, {
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
  const [contentKey, setContentKey] = React32.useState(0);
  const previousActiveTriggerIdRef = React32.useRef(activeTriggerId);
  const previousPayloadRef = React32.useRef(payload);
  const pendingPayloadUpdateRef = React32.useRef(false);
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
var React33 = __toESM(require_react(), 1);
var ReactDOM7 = __toESM(require_react_dom(), 1);
var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
var FloatingPortalLite = /* @__PURE__ */ React33.forwardRef(function FloatingPortalLite2(componentProps, forwardedRef) {
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
  return /* @__PURE__ */ (0, import_jsx_runtime6.jsxs)(React33.Fragment, {
    children: [portalSubtree, portalNode && /* @__PURE__ */ ReactDOM7.createPortal(children, portalNode)]
  });
});
if (true) FloatingPortalLite.displayName = "FloatingPortalLite";

// node_modules/@base-ui/react/tooltip/index.parts.mjs
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

// node_modules/@base-ui/react/tooltip/root/TooltipRoot.mjs
var React36 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/tooltip/root/TooltipRootContext.mjs
var React34 = __toESM(require_react(), 1);
var TooltipRootContext = /* @__PURE__ */ React34.createContext(void 0);
if (true) TooltipRootContext.displayName = "TooltipRootContext";
function useTooltipRootContext(optional) {
  const context = React34.useContext(TooltipRootContext);
  if (context === void 0 && !optional) {
    throw new Error(true ? "Base UI: TooltipRootContext is missing. Tooltip parts must be placed within <Tooltip.Root>." : formatErrorMessage_default(72));
  }
  return context;
}

// node_modules/@base-ui/react/tooltip/store/TooltipStore.mjs
var React35 = __toESM(require_react(), 1);
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
  constructor(initialState2, floatingId, nested = false) {
    const triggerElements = new PopupTriggerMap();
    const state = {
      ...createInitialState(),
      ...initialState2
    };
    state.floatingRootContext = createPopupFloatingRootContext(triggerElements, floatingId, nested);
    super(state, {
      popupRef: /* @__PURE__ */ React35.createRef(),
      onOpenChange: void 0,
      onOpenChangeComplete: void 0,
      triggerElements
    }, selectors2);
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
  static useStore(externalStore, initialState2) {
    const store2 = usePopupStore(externalStore, (floatingId, nested) => new _TooltipStore(initialState2, floatingId, nested)).store;
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

// node_modules/@base-ui/react/tooltip/root/TooltipRoot.mjs
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
  useInitialOpenSync(store2, openProp, defaultOpen, defaultTriggerIdProp);
  store2.useControlledProp("openProp", openProp);
  store2.useControlledProp("triggerIdProp", triggerIdProp);
  store2.useContextCallback("onOpenChange", onOpenChange);
  store2.useContextCallback("onOpenChangeComplete", onOpenChangeComplete);
  const openState = store2.useState("open");
  const open = !disabled2 && openState;
  const activeTriggerId = store2.useState("activeTriggerId");
  const mounted = store2.useState("mounted");
  const payload = store2.useState("payload");
  store2.useSyncedValues({
    trackCursorAxis,
    disableHoverablePopup
  });
  store2.useSyncedValue("disabled", disabled2);
  useImplicitActiveTrigger(store2, {
    closeOnActiveTriggerUnmount: true
  });
  const {
    forceUnmount,
    transitionStatus
  } = useOpenStateTransitions(open, store2);
  const isInstantPhase = store2.useState("isInstantPhase");
  const instantType = store2.useState("instantType");
  const lastOpenChangeReason = store2.useState("lastOpenChangeReason");
  const previousInstantTypeRef = React36.useRef(null);
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
    if (open) {
      if (activeTriggerId == null) {
        store2.set("payload", void 0);
      }
    }
  }, [store2, activeTriggerId, open]);
  const handleImperativeClose = React36.useCallback(() => {
    store2.setOpen(false, createChangeEventDetails(reason_parts_exports.imperativeAction));
  }, [store2]);
  React36.useImperativeHandle(actionsRef, () => ({
    unmount: forceUnmount,
    close: handleImperativeClose
  }), [forceUnmount, handleImperativeClose]);
  const shouldRenderInteractions = open || mounted || !disabled2 && trackCursorAxis !== "none";
  return /* @__PURE__ */ (0, import_jsx_runtime7.jsxs)(TooltipRootContext.Provider, {
    value: store2,
    children: [shouldRenderInteractions && /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(TooltipInteractions, {
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
  const activeTriggerProps = React36.useMemo(() => mergeProps(clientPoint.reference, dismiss.reference), [clientPoint.reference, dismiss.reference]);
  const inactiveTriggerProps = React36.useMemo(() => mergeProps(clientPoint.trigger, dismiss.trigger), [clientPoint.trigger, dismiss.trigger]);
  const popupProps = React36.useMemo(() => mergeProps(FOCUSABLE_POPUP_PROPS, clientPoint.floating, dismiss.floating), [clientPoint.floating, dismiss.floating]);
  usePopupInteractionProps(store2, {
    activeTriggerProps,
    inactiveTriggerProps,
    popupProps
  });
  return null;
}

// node_modules/@base-ui/react/tooltip/trigger/TooltipTrigger.mjs
var React38 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/tooltip/provider/TooltipProviderContext.mjs
var React37 = __toESM(require_react(), 1);
var TooltipProviderContext = /* @__PURE__ */ React37.createContext(void 0);
if (true) TooltipProviderContext.displayName = "TooltipProviderContext";
function useTooltipProviderContext() {
  return React37.useContext(TooltipProviderContext);
}

// node_modules/@base-ui/react/tooltip/trigger/TooltipTriggerDataAttributes.mjs
var TooltipTriggerDataAttributes = (function(TooltipTriggerDataAttributes2) {
  TooltipTriggerDataAttributes2[TooltipTriggerDataAttributes2["popupOpen"] = CommonTriggerDataAttributes.popupOpen] = "popupOpen";
  TooltipTriggerDataAttributes2["triggerDisabled"] = "data-trigger-disabled";
  return TooltipTriggerDataAttributes2;
})({});

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
    if (current.hasAttribute(TOOLTIP_TRIGGER_IDENTIFIER)) {
      return current;
    }
    const parentElement = current.parentElement;
    if (parentElement) {
      current = parentElement;
      continue;
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
  const store2 = handle?.store ?? rootContext;
  if (!store2) {
    throw new Error(true ? "Base UI: <Tooltip.Trigger> must be either used within a <Tooltip.Root> component or provided with a handle." : formatErrorMessage_default(82));
  }
  const thisTriggerId = useBaseUiId(idProp);
  const isTriggerActive = store2.useState("isTriggerActive", thisTriggerId);
  const isOpenedByThisTrigger = store2.useState("isOpenedByTrigger", thisTriggerId);
  const floatingRootContext = store2.useState("floatingRootContext");
  const triggerElementRef = React38.useRef(null);
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
  const hoverInteraction = useHoverInteractionSharedState(floatingRootContext);
  store2.useSyncedValue("isInstantPhase", isInstantPhase);
  const rootDisabled = store2.useState("disabled");
  const disabled2 = disabledProp ?? rootDisabled;
  const disabledRef = useValueAsRef(disabled2);
  const trackCursorAxis = store2.useState("trackCursorAxis");
  const disableHoverablePopup = store2.useState("disableHoverablePopup");
  const isNestedTriggerHoveredRef = React38.useRef(false);
  const nestedTriggerOpenTimeout = useTimeout();
  const pointerTypeRef = React38.useRef(void 0);
  function getOpenDelay() {
    const providerDelay = providerContext?.delay;
    const groupOpenValue = typeof delayRef.current === "object" ? delayRef.current.open : void 0;
    let computedOpenDelay = delayWithDefault;
    if (hasProvider) {
      if (groupOpenValue !== 0) {
        computedOpenDelay = delay ?? providerDelay ?? delayWithDefault;
      } else {
        computedOpenDelay = 0;
      }
    }
    return computedOpenDelay;
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
      const open = () => {
        if (!isNestedTriggerHoveredRef.current && !disabledRef.current && !store2.select("open")) {
          store2.setOpen(true, createChangeEventDetails(reason_parts_exports.triggerHover, event, triggerEl));
        }
      };
      const openDelay = getOpenDelay();
      if (openDelay === 0) {
        nestedTriggerOpenTimeout.clear();
        open();
      } else {
        nestedTriggerOpenTimeout.start(openDelay, open);
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
      [TooltipTriggerDataAttributes.triggerDisabled]: disabled2 ? "" : void 0,
      [TOOLTIP_TRIGGER_IDENTIFIER]: disabled2 ? void 0 : ""
    }, elementProps],
    stateAttributesMapping: triggerOpenStateMapping
  });
  return element;
});
if (true) TooltipTrigger.displayName = "TooltipTrigger";

// node_modules/@base-ui/react/tooltip/portal/TooltipPortal.mjs
var React40 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/tooltip/portal/TooltipPortalContext.mjs
var React39 = __toESM(require_react(), 1);
var TooltipPortalContext = /* @__PURE__ */ React39.createContext(void 0);
if (true) TooltipPortalContext.displayName = "TooltipPortalContext";
function useTooltipPortalContext() {
  const value = React39.useContext(TooltipPortalContext);
  if (value === void 0) {
    throw new Error(true ? "Base UI: <Tooltip.Portal> is missing." : formatErrorMessage_default(70));
  }
  return value;
}

// node_modules/@base-ui/react/tooltip/portal/TooltipPortal.mjs
var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
var TooltipPortal = /* @__PURE__ */ React40.forwardRef(function TooltipPortal2(props, forwardedRef) {
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

// node_modules/@base-ui/react/tooltip/positioner/TooltipPositioner.mjs
var React42 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/tooltip/positioner/TooltipPositionerContext.mjs
var React41 = __toESM(require_react(), 1);
var TooltipPositionerContext = /* @__PURE__ */ React41.createContext(void 0);
if (true) TooltipPositionerContext.displayName = "TooltipPositionerContext";
function useTooltipPositionerContext() {
  const context = React41.useContext(TooltipPositionerContext);
  if (context === void 0) {
    throw new Error(true ? "Base UI: TooltipPositionerContext is missing. TooltipPositioner parts must be placed within <Tooltip.Positioner>." : formatErrorMessage_default(71));
  }
  return context;
}

// node_modules/@base-ui/react/tooltip/positioner/TooltipPositioner.mjs
var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
var TooltipPositioner = /* @__PURE__ */ React42.forwardRef(function TooltipPositioner2(componentProps, forwardedRef) {
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
  const state = React42.useMemo(() => ({
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

// node_modules/@base-ui/react/tooltip/popup/TooltipPopup.mjs
var React43 = __toESM(require_react(), 1);
var stateAttributesMapping = {
  ...popupStateMapping,
  ...transitionStatusMapping
};
var TooltipPopup = /* @__PURE__ */ React43.forwardRef(function TooltipPopup2(componentProps, forwardedRef) {
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
  const open = store2.useState("open");
  const instantType = store2.useState("instantType");
  const transitionStatus = store2.useState("transitionStatus");
  const popupProps = store2.useState("popupProps");
  const floatingContext = store2.useState("floatingRootContext");
  const disabled2 = store2.useState("disabled");
  const closeDelay = store2.useState("closeDelay");
  useOpenChangeComplete({
    open,
    ref: store2.context.popupRef,
    onComplete() {
      if (open) {
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
    open,
    side,
    align,
    instant: instantType,
    transitionStatus
  };
  const element = useRenderElement("div", componentProps, {
    state,
    ref: [forwardedRef, store2.context.popupRef, setPopupElement],
    props: [popupProps, getDisabledMountTransitionStyles(transitionStatus), elementProps],
    stateAttributesMapping
  });
  return element;
});
if (true) TooltipPopup.displayName = "TooltipPopup";

// node_modules/@base-ui/react/tooltip/arrow/TooltipArrow.mjs
var React44 = __toESM(require_react(), 1);
var TooltipArrow = /* @__PURE__ */ React44.forwardRef(function TooltipArrow2(componentProps, forwardedRef) {
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
  const open = store2.useState("open");
  const instantType = store2.useState("instantType");
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

// node_modules/@base-ui/react/tooltip/provider/TooltipProvider.mjs
var React45 = __toESM(require_react(), 1);
var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
var TooltipProvider = function TooltipProvider2(props) {
  const {
    delay,
    closeDelay,
    timeout = 400
  } = props;
  const contextValue = React45.useMemo(() => ({
    delay,
    closeDelay
  }), [delay, closeDelay]);
  const delayValue = React45.useMemo(() => ({
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

// node_modules/@base-ui/react/tooltip/viewport/TooltipViewport.mjs
var React46 = __toESM(require_react(), 1);

// node_modules/@base-ui/react/tooltip/viewport/TooltipViewportCssVars.mjs
var TooltipViewportCssVars = /* @__PURE__ */ (function(TooltipViewportCssVars2) {
  TooltipViewportCssVars2["popupWidth"] = "--popup-width";
  TooltipViewportCssVars2["popupHeight"] = "--popup-height";
  return TooltipViewportCssVars2;
})({});

// node_modules/@base-ui/react/tooltip/viewport/TooltipViewport.mjs
var stateAttributesMapping2 = {
  activationDirection: (value) => value ? {
    "data-activation-direction": value
  } : null
};
var TooltipViewport = /* @__PURE__ */ React46.forwardRef(function TooltipViewport2(componentProps, forwardedRef) {
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

// node_modules/@base-ui/react/tooltip/store/TooltipHandle.mjs
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
    return this.store.select("open");
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
  registerStyle("a495f9d138", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._83ed8a8da5dd50ea__text{margin:0}._14437cfb77831647__heading-2xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-2xl,32px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-2xl,32px);--_gcd-p-line-height:var(--wpds-typography-line-height-2xl,40px);font-size:var(--wpds-typography-font-size-2xl,32px);line-height:var(--wpds-typography-line-height-2xl,40px)}._14437cfb77831647__heading-2xl,._3c78b7fa9b4072dd__heading-xl{font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-emphasis,600)}._3c78b7fa9b4072dd__heading-xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-p-line-height:var(--wpds-typography-line-height-md,24px);font-size:var(--wpds-typography-font-size-xl,20px);line-height:var(--wpds-typography-line-height-md,24px)}.aa58f227716bcde2__heading-lg{--_gcd-heading-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-p-line-height:var(--wpds-typography-line-height-sm,20px);font-size:var(--wpds-typography-font-size-lg,15px)}.aa58f227716bcde2__heading-lg,.fc4da56d8dfe52c4__heading-md{font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-emphasis,600);line-height:var(--wpds-typography-line-height-sm,20px)}.fc4da56d8dfe52c4__heading-md{--_gcd-heading-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-p-line-height:var(--wpds-typography-line-height-sm,20px);font-size:var(--wpds-typography-font-size-md,13px)}.a9b78c7c82e8dff7__heading-sm{--_gcd-heading-font-size:var(--wpds-typography-font-size-xs,11px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-xs,11px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-xs,11px);font-weight:var(--wpds-typography-font-weight-emphasis,600);line-height:var(--wpds-typography-line-height-xs,16px);text-transform:uppercase}._305ff559e52180d5__body-xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-default,400);--_gcd-p-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-p-line-height:var(--wpds-typography-line-height-xl,32px);font-size:var(--wpds-typography-font-size-xl,20px);line-height:var(--wpds-typography-line-height-xl,32px)}._305ff559e52180d5__body-xl,.ca1aa3fc2029e958__body-lg{font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-default,400)}.ca1aa3fc2029e958__body-lg{--_gcd-heading-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-default,400);--_gcd-p-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-p-line-height:var(--wpds-typography-line-height-md,24px);font-size:var(--wpds-typography-font-size-lg,15px);line-height:var(--wpds-typography-line-height-md,24px)}._131101940be12424__body-md{--_gcd-heading-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-default,400);--_gcd-p-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-p-line-height:var(--wpds-typography-line-height-sm,20px);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px)}._0e8d87a42c1f75fa__body-sm,._131101940be12424__body-md{font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-default,400)}._0e8d87a42c1f75fa__body-sm{--_gcd-heading-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-default,400);--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);font-size:var(--wpds-typography-font-size-sm,12px);line-height:var(--wpds-typography-line-height-xs,16px)}}}');
}
var style_default = { "text": "_83ed8a8da5dd50ea__text", "heading-2xl": "_14437cfb77831647__heading-2xl", "heading-xl": "_3c78b7fa9b4072dd__heading-xl", "heading-lg": "aa58f227716bcde2__heading-lg", "heading-md": "fc4da56d8dfe52c4__heading-md", "heading-sm": "a9b78c7c82e8dff7__heading-sm", "body-xl": "_305ff559e52180d5__body-xl", "body-lg": "ca1aa3fc2029e958__body-lg", "body-md": "_131101940be12424__body-md", "body-sm": "_0e8d87a42c1f75fa__body-sm" };
if (typeof process === "undefined" || true) {
  registerStyle("af6d9984a6", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}");
}
var global_css_defense_default = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a" };
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

// packages/icons/build-module/library/check.mjs
var import_primitives = __toESM(require_primitives(), 1);
var import_jsx_runtime11 = __toESM(require_jsx_runtime(), 1);
var check_default = /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives.Path, { d: "M16.5 7.5 10 13.9l-2.5-2.4-1 1 3.5 3.6 7.5-7.6z" }) });

// packages/icons/build-module/library/chevron-down-small.mjs
var import_primitives2 = __toESM(require_primitives(), 1);
var import_jsx_runtime12 = __toESM(require_jsx_runtime(), 1);
var chevron_down_small_default = /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives2.Path, { d: "m15.99 10.889-3.988 3.418-3.988-3.418.976-1.14 3.012 2.582 3.012-2.581.976 1.139Z" }) });

// packages/icons/build-module/library/chevron-left-small.mjs
var import_primitives3 = __toESM(require_primitives(), 1);
var import_jsx_runtime13 = __toESM(require_jsx_runtime(), 1);
var chevron_left_small_default = /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives3.Path, { d: "m13.1 16-3.4-4 3.4-4 1.1 1-2.6 3 2.6 3-1.1 1z" }) });

// packages/icons/build-module/library/chevron-left.mjs
var import_primitives4 = __toESM(require_primitives(), 1);
var import_jsx_runtime14 = __toESM(require_jsx_runtime(), 1);
var chevron_left_default = /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(import_primitives4.Path, { d: "M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z" }) });

// packages/icons/build-module/library/chevron-right-small.mjs
var import_primitives5 = __toESM(require_primitives(), 1);
var import_jsx_runtime15 = __toESM(require_jsx_runtime(), 1);
var chevron_right_small_default = /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_primitives5.Path, { d: "M10.8622 8.04053L14.2805 12.0286L10.8622 16.0167L9.72327 15.0405L12.3049 12.0286L9.72327 9.01672L10.8622 8.04053Z" }) });

// packages/icons/build-module/library/chevron-right.mjs
var import_primitives6 = __toESM(require_primitives(), 1);
var import_jsx_runtime16 = __toESM(require_jsx_runtime(), 1);
var chevron_right_default = /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_primitives6.Path, { d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z" }) });

// packages/icons/build-module/library/menu.mjs
var import_primitives7 = __toESM(require_primitives(), 1);
var import_jsx_runtime17 = __toESM(require_jsx_runtime(), 1);
var menu_default = /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_primitives7.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_primitives7.Path, { d: "M5 5v1.5h14V5H5zm0 7.8h14v-1.5H5v1.5zM5 19h14v-1.5H5V19z" }) });

// packages/ui/build-module/utils/render-slot-with-children.mjs
var import_element11 = __toESM(require_element(), 1);
function renderSlotWithChildren(slot, defaultSlot, children) {
  return (0, import_element11.cloneElement)(slot ?? defaultSlot, { children });
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

// packages/ui/build-module/stack/stack.mjs
var import_element12 = __toESM(require_element(), 1);
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
  registerStyle2("32aba35fe1", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._19ce0419607e1896__stack{display:flex}}}");
}
var style_default2 = { "stack": "_19ce0419607e1896__stack" };
var gapTokens = {
  xs: "var(--wpds-dimension-gap-xs, 4px)",
  sm: "var(--wpds-dimension-gap-sm, 8px)",
  md: "var(--wpds-dimension-gap-md, 12px)",
  lg: "var(--wpds-dimension-gap-lg, 16px)",
  xl: "var(--wpds-dimension-gap-xl, 24px)",
  "2xl": "var(--wpds-dimension-gap-2xl, 32px)",
  "3xl": "var(--wpds-dimension-gap-3xl, 40px)"
};
var Stack = (0, import_element12.forwardRef)(function Stack2({ direction, gap, align, justify, wrap, render, ...props }, ref) {
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
    props: mergeProps(props, { style, className: style_default2.stack })
  });
  return element;
});

// packages/ui/build-module/tooltip/index.mjs
var tooltip_exports = {};
__export(tooltip_exports, {
  Popup: () => Popup,
  Portal: () => Portal,
  Positioner: () => Positioner,
  Provider: () => Provider,
  Root: () => Root,
  Trigger: () => Trigger
});

// packages/ui/build-module/tooltip/popup.mjs
var import_element15 = __toESM(require_element(), 1);

// packages/ui/build-module/tooltip/portal.mjs
var import_element13 = __toESM(require_element(), 1);

// packages/ui/build-module/utils/wp-compat-overlay-slot.mjs
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
  registerStyle3("be37f31c1e", "._11fc52b637ff8a7e__slot{inset:0;isolation:isolate;pointer-events:none;position:fixed;z-index:1000000003}@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._11fc52b637ff8a7e__slot>*{pointer-events:auto}}}");
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
    return cachedSlot;
  }
  const existing = ownerDocument2.querySelector(
    `[${WP_COMPAT_OVERLAY_SLOT_ATTRIBUTE}]`
  );
  if (existing instanceof HTMLDivElement) {
    cachedSlot = existing;
    return existing;
  }
  if (cachedSlot?.isConnected) {
    cachedSlot.remove();
  }
  cachedSlot = createSlot(ownerDocument2);
  return cachedSlot;
}

// packages/ui/build-module/tooltip/portal.mjs
var import_jsx_runtime18 = __toESM(require_jsx_runtime(), 1);
var Portal = (0, import_element13.forwardRef)(
  function TooltipPortal3({ container, ...restProps }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
      index_parts_exports.Portal,
      {
        container: container ?? getWpCompatOverlaySlot(),
        ...restProps,
        ref
      }
    );
  }
);

// packages/ui/build-module/tooltip/positioner.mjs
var import_element14 = __toESM(require_element(), 1);
var import_jsx_runtime19 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle4("10f3806643", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer utilities{._336cd3e4e743482f__box-sizing{box-sizing:border-box;*,:after,:before{box-sizing:inherit}}}}");
}
var resets_default = { "box-sizing": "_336cd3e4e743482f__box-sizing" };
if (typeof process === "undefined" || true) {
  registerStyle4("19fcc06039", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._480b748dd3510e64__positioner{z-index:var(--wp-ui-tooltip-z-index,initial)}._50096b232db7709d__popup{--_wp-ui-elevation-sm:0 1px 2px rgba(0,0,0,.05),0 2px 3px rgba(0,0,0,.04),0 6px 6px rgba(0,0,0,.03),0 8px 8px rgba(0,0,0,.02);background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border-radius:var(--wpds-border-radius-md,4px);box-shadow:var(--_wp-ui-elevation-sm);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);line-height:1.4;padding:var(--wpds-dimension-padding-xs,4px) var(--wpds-dimension-padding-sm,8px);@media (forced-colors:active){border-bottom-color:CanvasText;border-bottom-style:solid;border-bottom-width:1px;border-left-color:CanvasText;border-left-style:solid;border-left-width:1px;border-right-color:CanvasText;border-right-style:solid;border-right-width:1px;border-top-color:CanvasText;border-top-style:solid;border-top-width:1px}}}}');
}
var style_default3 = { "positioner": "_480b748dd3510e64__positioner", "popup": "_50096b232db7709d__popup" };
var Positioner = (0, import_element14.forwardRef)(
  function TooltipPositioner3({ align = "center", className, side = "top", sideOffset = 4, ...props }, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
      index_parts_exports.Positioner,
      {
        ref,
        align,
        side,
        sideOffset,
        ...props,
        className: clsx_default(
          resets_default["box-sizing"],
          style_default3.positioner,
          className
        )
      }
    );
  }
);

// packages/ui/build-module/tooltip/popup.mjs
var import_jsx_runtime20 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle5("19fcc06039", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._480b748dd3510e64__positioner{z-index:var(--wp-ui-tooltip-z-index,initial)}._50096b232db7709d__popup{--_wp-ui-elevation-sm:0 1px 2px rgba(0,0,0,.05),0 2px 3px rgba(0,0,0,.04),0 6px 6px rgba(0,0,0,.03),0 8px 8px rgba(0,0,0,.02);background-color:var(--wpds-color-background-surface-neutral-strong,#fff);border-radius:var(--wpds-border-radius-md,4px);box-shadow:var(--_wp-ui-elevation-sm);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-sm,12px);line-height:1.4;padding:var(--wpds-dimension-padding-xs,4px) var(--wpds-dimension-padding-sm,8px);@media (forced-colors:active){border-bottom-color:CanvasText;border-bottom-style:solid;border-bottom-width:1px;border-left-color:CanvasText;border-left-style:solid;border-left-width:1px;border-right-color:CanvasText;border-right-style:solid;border-right-width:1px;border-top-color:CanvasText;border-top-style:solid;border-top-width:1px}}}}');
}
var style_default4 = { "positioner": "_480b748dd3510e64__positioner", "popup": "_50096b232db7709d__popup" };
var POPUP_COLOR = { background: "#1e1e1e" };
var Popup = (0, import_element15.forwardRef)(function TooltipPopup3({ portal, positioner, children, className, ...props }, ref) {
  const popupContent = /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(ThemeProvider, { color: POPUP_COLOR, children: /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
    index_parts_exports.Popup,
    {
      ref,
      className: clsx_default(style_default4.popup, className),
      ...props,
      children
    }
  ) });
  const positionedPopup = renderSlotWithChildren(
    positioner,
    /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(Positioner, {}),
    popupContent
  );
  return renderSlotWithChildren(portal, /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(Portal, {}), positionedPopup);
});

// packages/ui/build-module/tooltip/trigger.mjs
var import_element16 = __toESM(require_element(), 1);
var import_jsx_runtime21 = __toESM(require_jsx_runtime(), 1);
var Trigger = (0, import_element16.forwardRef)(
  function TooltipTrigger3(props, ref) {
    return /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(index_parts_exports.Trigger, { ref, ...props });
  }
);

// packages/ui/build-module/tooltip/root.mjs
var import_jsx_runtime22 = __toESM(require_jsx_runtime(), 1);
function Root(props) {
  return /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(index_parts_exports.Root, { ...props });
}

// packages/ui/build-module/tooltip/provider.mjs
var import_jsx_runtime23 = __toESM(require_jsx_runtime(), 1);
function Provider({ ...props }) {
  return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(index_parts_exports.Provider, { ...props });
}

// packages/admin-ui/build-module/navigable-region/index.mjs
var import_element17 = __toESM(require_element(), 1);
var import_jsx_runtime24 = __toESM(require_jsx_runtime(), 1);
var NavigableRegion = (0, import_element17.forwardRef)(
  ({ children, className, ariaLabel, as: Tag = "div", ...props }, ref) => {
    return /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
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
var import_jsx_runtime25 = __toESM(require_jsx_runtime(), 1);
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
  registerStyle6("ddd9aab364", "._956b6df0898efed0__page{text-wrap:pretty;background-color:var(--wpds-color-background-surface-neutral,#fcfcfc);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);display:flex;flex-flow:column;height:100%;position:relative;z-index:1}._0625b55e82a0d93d__header{background:var(--wpds-color-background-surface-neutral-strong,#fff);border-block-end:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);inset-block-start:0;padding:var(--wpds-dimension-padding-lg,16px) var(--wpds-dimension-padding-2xl,24px);position:sticky;z-index:1}.a43c44d5ae28b2e8__header-content{min-height:var(--wpds-dimension-size-md,32px)}.b7cb5b9daf3a3b25__header-actions{flex-shrink:0}._8113be94e7caf73c__header-title{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}._9a776c7f70996f61__header-visual{display:grid;flex-shrink:0;grid-template-columns:1fr;grid-template-rows:1fr;height:var(--wpds-dimension-size-sm,24px);width:var(--wpds-dimension-size-sm,24px);>*{grid-column:1/-1;grid-row:1/-1;max-height:100%;max-width:100%}}.d5e0920cd15d35bc__sidebar-toggle-slot:empty{display:none}._60fea2f6bf5319cd__header-subtitle{color:var(--wpds-color-foreground-content-neutral-weak,#707070);padding-block-end:var(--wpds-dimension-padding-xs,4px)}.be5e57d029ec4036__content{display:flex;flex-direction:column;flex-grow:1;overflow:auto;&._128806d0b26e3a50__has-padding{padding:var(--wpds-dimension-padding-lg,16px) var(--wpds-dimension-padding-2xl,24px)}}");
}
var style_default5 = { "page": "_956b6df0898efed0__page", "header": "_0625b55e82a0d93d__header", "header-content": "a43c44d5ae28b2e8__header-content", "header-actions": "b7cb5b9daf3a3b25__header-actions", "header-title": "_8113be94e7caf73c__header-title", "header-visual": "_9a776c7f70996f61__header-visual", "sidebar-toggle-slot": "d5e0920cd15d35bc__sidebar-toggle-slot", "header-subtitle": "_60fea2f6bf5319cd__header-subtitle", "content": "be5e57d029ec4036__content", "has-padding": "_128806d0b26e3a50__has-padding" };
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
  return /* @__PURE__ */ (0, import_jsx_runtime25.jsxs)(Stack, { direction: "column", className: style_default5.header, children: [
    /* @__PURE__ */ (0, import_jsx_runtime25.jsxs)(
      Stack,
      {
        className: style_default5["header-content"],
        direction: "row",
        gap: "sm",
        justify: "space-between",
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime25.jsxs)(Stack, { direction: "row", gap: "sm", align: "center", justify: "start", children: [
            showSidebarToggle && /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(
              SidebarToggleSlot,
              {
                bubblesVirtually: true,
                className: style_default5["sidebar-toggle-slot"]
              }
            ),
            visual && /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(
              "div",
              {
                className: style_default5["header-visual"],
                "aria-hidden": "true",
                children: visual
              }
            ),
            title && /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(
              Text,
              {
                className: style_default5["header-title"],
                render: /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(HeadingTag, {}),
                variant: "heading-lg",
                children: title
              }
            ),
            breadcrumbs,
            badges
          ] }),
          actions && /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(
            Stack,
            {
              align: "center",
              className: style_default5["header-actions"],
              direction: "row",
              gap: "sm",
              children: actions
            }
          )
        ]
      }
    ),
    subTitle && /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(
      Text,
      {
        render: /* @__PURE__ */ (0, import_jsx_runtime25.jsx)("p", {}),
        variant: "body-md",
        className: style_default5["header-subtitle"],
        children: subTitle
      }
    )
  ] });
}

// packages/admin-ui/build-module/page/index.mjs
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
  registerStyle7("ddd9aab364", "._956b6df0898efed0__page{text-wrap:pretty;background-color:var(--wpds-color-background-surface-neutral,#fcfcfc);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);display:flex;flex-flow:column;height:100%;position:relative;z-index:1}._0625b55e82a0d93d__header{background:var(--wpds-color-background-surface-neutral-strong,#fff);border-block-end:var(--wpds-border-width-xs,1px) solid var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);inset-block-start:0;padding:var(--wpds-dimension-padding-lg,16px) var(--wpds-dimension-padding-2xl,24px);position:sticky;z-index:1}.a43c44d5ae28b2e8__header-content{min-height:var(--wpds-dimension-size-md,32px)}.b7cb5b9daf3a3b25__header-actions{flex-shrink:0}._8113be94e7caf73c__header-title{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}._9a776c7f70996f61__header-visual{display:grid;flex-shrink:0;grid-template-columns:1fr;grid-template-rows:1fr;height:var(--wpds-dimension-size-sm,24px);width:var(--wpds-dimension-size-sm,24px);>*{grid-column:1/-1;grid-row:1/-1;max-height:100%;max-width:100%}}.d5e0920cd15d35bc__sidebar-toggle-slot:empty{display:none}._60fea2f6bf5319cd__header-subtitle{color:var(--wpds-color-foreground-content-neutral-weak,#707070);padding-block-end:var(--wpds-dimension-padding-xs,4px)}.be5e57d029ec4036__content{display:flex;flex-direction:column;flex-grow:1;overflow:auto;&._128806d0b26e3a50__has-padding{padding:var(--wpds-dimension-padding-lg,16px) var(--wpds-dimension-padding-2xl,24px)}}");
}
var style_default6 = { "page": "_956b6df0898efed0__page", "header": "_0625b55e82a0d93d__header", "header-content": "a43c44d5ae28b2e8__header-content", "header-actions": "b7cb5b9daf3a3b25__header-actions", "header-title": "_8113be94e7caf73c__header-title", "header-visual": "_9a776c7f70996f61__header-visual", "sidebar-toggle-slot": "d5e0920cd15d35bc__sidebar-toggle-slot", "header-subtitle": "_60fea2f6bf5319cd__header-subtitle", "content": "be5e57d029ec4036__content", "has-padding": "_128806d0b26e3a50__has-padding" };
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
  const classes = clsx_default(style_default6.page, className);
  const effectiveAriaLabel = ariaLabel ?? (typeof title === "string" ? title : "");
  return /* @__PURE__ */ (0, import_jsx_runtime26.jsxs)(navigable_region_default, { className: classes, ariaLabel: effectiveAriaLabel, children: [
    (title || breadcrumbs || badges || actions || visual) && /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
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
    hasPadding ? /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
      "div",
      {
        className: clsx_default(
          style_default6.content,
          style_default6["has-padding"]
        ),
        children
      }
    ) : children
  ] });
}
Page.SidebarToggleFill = SidebarToggleFill;
var page_default = Page;

// packages/admin-ui/build-module/admin-theme-colors/index.mjs
var DEFAULT_THEME_COLORS = {
  primary: "#3858e9",
  background: "#222524"
};
var ADMIN_THEME_COLORS = /* @__PURE__ */ new Map([
  ["modern", DEFAULT_THEME_COLORS],
  ["fresh", { primary: "#3858e9", background: "#25292b" }],
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

// packages/boot/build-module/components/app/router.mjs
var import_data9 = __toESM(require_data(), 1);
var import_core_data4 = __toESM(require_core_data(), 1);
import {
  privateApis as routePrivateApis5
} from "@wordpress/route";

// packages/boot/build-module/components/root/index.mjs
var import_notices = __toESM(require_notices(), 1);
var import_compose4 = __toESM(require_compose(), 1);
var import_components13 = __toESM(require_components(), 1);
import { privateApis as routePrivateApis4 } from "@wordpress/route";
var import_element29 = __toESM(require_element(), 1);
var import_i18n9 = __toESM(require_i18n(), 1);
var import_theme = __toESM(require_theme(), 1);

// packages/boot/build-module/components/sidebar/index.mjs
var import_components9 = __toESM(require_components(), 1);
var import_data7 = __toESM(require_data(), 1);
var import_i18n5 = __toESM(require_i18n(), 1);

// packages/boot/build-module/components/navigation/index.mjs
var import_element21 = __toESM(require_element(), 1);
var import_data4 = __toESM(require_data(), 1);

// packages/boot/build-module/store/index.mjs
var import_data = __toESM(require_data(), 1);

// packages/boot/build-module/store/reducer.mjs
var initialState = {
  menuItems: {},
  routes: [],
  dashboardLink: void 0
};
function reducer(state = initialState, action) {
  switch (action.type) {
    case "REGISTER_MENU_ITEM":
      return {
        ...state,
        menuItems: {
          ...state.menuItems,
          [action.id]: action.menuItem
        }
      };
    case "UPDATE_MENU_ITEM":
      return {
        ...state,
        menuItems: {
          ...state.menuItems,
          [action.id]: {
            ...state.menuItems[action.id],
            ...action.updates
          }
        }
      };
    case "REGISTER_ROUTE":
      return {
        ...state,
        routes: [...state.routes, action.route]
      };
    case "SET_DASHBOARD_LINK":
      return {
        ...state,
        dashboardLink: action.dashboardLink
      };
  }
  return state;
}

// packages/boot/build-module/store/actions.mjs
var actions_exports = {};
__export(actions_exports, {
  registerMenuItem: () => registerMenuItem,
  registerRoute: () => registerRoute,
  setDashboardLink: () => setDashboardLink,
  updateMenuItem: () => updateMenuItem
});
function registerMenuItem(id, menuItem) {
  return {
    type: "REGISTER_MENU_ITEM",
    id,
    menuItem
  };
}
function updateMenuItem(id, updates) {
  return {
    type: "UPDATE_MENU_ITEM",
    id,
    updates
  };
}
function registerRoute(route) {
  return {
    type: "REGISTER_ROUTE",
    route
  };
}
function setDashboardLink(dashboardLink) {
  return {
    type: "SET_DASHBOARD_LINK",
    dashboardLink
  };
}

// packages/boot/build-module/store/selectors.mjs
var selectors_exports = {};
__export(selectors_exports, {
  getDashboardLink: () => getDashboardLink,
  getMenuItems: () => getMenuItems,
  getRoutes: () => getRoutes
});
function getMenuItems(state) {
  return Object.values(state.menuItems);
}
function getRoutes(state) {
  return state.routes;
}
function getDashboardLink(state) {
  return state.dashboardLink;
}

// packages/boot/build-module/store/index.mjs
var STORE_NAME = "wordpress/boot";
var store = (0, import_data.createReduxStore)(STORE_NAME, {
  reducer,
  actions: actions_exports,
  selectors: selectors_exports
});
(0, import_data.register)(store);

// packages/boot/build-module/components/navigation/navigation-item/index.mjs
var import_components4 = __toESM(require_components(), 1);

// packages/boot/build-module/components/navigation/router-link-item.mjs
var import_element18 = __toESM(require_element(), 1);
var import_components2 = __toESM(require_components(), 1);
import { privateApis as routePrivateApis } from "@wordpress/route";

// packages/boot/build-module/lock-unlock.mjs
var import_private_apis2 = __toESM(require_private_apis(), 1);
var { lock: lock2, unlock: unlock2 } = (0, import_private_apis2.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/boot"
);

// packages/boot/build-module/components/navigation/router-link-item.mjs
var import_jsx_runtime27 = __toESM(require_jsx_runtime(), 1);
var { createLink } = unlock2(routePrivateApis);
function AnchorOnlyItem(props, forwardedRef) {
  return /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(import_components2.__experimentalItem, { as: "a", ref: forwardedRef, ...props });
}
var RouterLinkItem = createLink((0, import_element18.forwardRef)(AnchorOnlyItem));
var router_link_item_default = RouterLinkItem;

// packages/boot/build-module/components/navigation/items.mjs
var import_element19 = __toESM(require_element(), 1);
var import_components3 = __toESM(require_components(), 1);
var import_primitives8 = __toESM(require_primitives(), 1);
var import_jsx_runtime28 = __toESM(require_jsx_runtime(), 1);
function isSvg(element) {
  return (0, import_element19.isValidElement)(element) && (element.type === import_primitives8.SVG || element.type === "svg");
}
function wrapIcon(icon, shouldShowPlaceholder = true) {
  if (isSvg(icon)) {
    return /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(import_components3.Icon, { icon });
  }
  if (typeof icon === "string" && icon.startsWith("dashicons-")) {
    const iconKey = icon.replace(
      /^dashicons-/,
      ""
    );
    return /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
      import_components3.Dashicon,
      {
        style: { padding: "2px" },
        icon: iconKey,
        "aria-hidden": "true"
      }
    );
  }
  if (typeof icon === "string" && icon.startsWith("data:")) {
    return /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
      "img",
      {
        src: icon,
        alt: "",
        "aria-hidden": "true",
        style: {
          width: "20px",
          height: "20px",
          display: "block",
          padding: "2px"
        }
      }
    );
  }
  if (icon) {
    return icon;
  }
  if (shouldShowPlaceholder) {
    return /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
      "div",
      {
        style: { width: "24px", height: "24px" },
        "aria-hidden": "true"
      }
    );
  }
  return null;
}

// packages/boot/build-module/components/navigation/navigation-item/index.mjs
var import_jsx_runtime29 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='4e60007e70']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "4e60007e70");
  style.appendChild(document.createTextNode(".boot-navigation-item.components-item{align-items:center;border:none;color:var(--wpds-color-foreground-interactive-neutral,#1e1e1e);display:flex;font-family:-apple-system,system-ui,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif;font-size:13px;font-weight:var(--wpds-typography-font-weight-default,400);line-height:20px;margin-block-end:4px;margin-inline:12px;min-height:32px;padding-block:0;padding-inline:4px;width:calc(100% - 24px)}.boot-dropdown-item__children .boot-navigation-item.components-item{min-height:24px}.boot-navigation-item.components-item{border-radius:var(--wpds-border-radius-sm,2px)}.boot-navigation-item.components-item.active,.boot-navigation-item.components-item:focus,.boot-navigation-item.components-item:hover,.boot-navigation-item.components-item[aria-current=true]{color:var(--wpds-color-foreground-interactive-brand-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 52%,#000))}.boot-navigation-item.components-item.active{font-weight:var(--wpds-typography-font-weight-emphasis,600)}.boot-navigation-item.components-item svg:last-child{padding:4px}.boot-navigation-item.components-item[aria-current=true]{color:var(--wpds-color-foreground-interactive-brand-active,color-mix(in oklch,var(--wp-admin-theme-color,#3858e9) 52%,#000));font-weight:var(--wpds-typography-font-weight-emphasis,600)}.boot-navigation-item.components-item:focus-visible{transform:translateZ(0)}.boot-navigation-item.components-item.with-suffix{padding-right:16px}"));
  document.head.appendChild(style);
}
function NavigationItem({
  className,
  icon,
  shouldShowPlaceholder = true,
  children,
  to
}) {
  const isExternal = !String(
    new URL(to, window.location.origin)
  ).startsWith(window.location.origin);
  const content = /* @__PURE__ */ (0, import_jsx_runtime29.jsxs)(import_components4.__experimentalHStack, { justify: "flex-start", spacing: 2, style: { flexGrow: "1" }, children: [
    wrapIcon(icon, shouldShowPlaceholder),
    /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(import_components4.FlexBlock, { children })
  ] });
  if (isExternal) {
    return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
      import_components4.__experimentalItem,
      {
        as: "a",
        href: to,
        className: clsx_default("boot-navigation-item", className),
        children: content
      }
    );
  }
  return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
    router_link_item_default,
    {
      to,
      className: clsx_default("boot-navigation-item", className),
      children: content
    }
  );
}

// packages/boot/build-module/components/navigation/drilldown-item/index.mjs
var import_components5 = __toESM(require_components(), 1);
var import_i18n = __toESM(require_i18n(), 1);
var import_jsx_runtime30 = __toESM(require_jsx_runtime(), 1);
function DrilldownItem({
  className,
  id,
  icon,
  shouldShowPlaceholder = true,
  children,
  onNavigate
}) {
  const handleClick = (e) => {
    e.preventDefault();
    onNavigate({ id, direction: "forward" });
  };
  return /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
    import_components5.__experimentalItem,
    {
      className: clsx_default("boot-navigation-item", className),
      onClick: handleClick,
      children: /* @__PURE__ */ (0, import_jsx_runtime30.jsxs)(
        import_components5.__experimentalHStack,
        {
          justify: "flex-start",
          spacing: 2,
          style: { flexGrow: "1" },
          children: [
            wrapIcon(icon, shouldShowPlaceholder),
            /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(import_components5.FlexBlock, { children }),
            /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
              import_components5.Icon,
              {
                icon: (0, import_i18n.isRTL)() ? chevron_left_small_default : chevron_right_small_default
              }
            )
          ]
        }
      )
    }
  );
}

// packages/boot/build-module/components/navigation/dropdown-item/index.mjs
var import_components6 = __toESM(require_components(), 1);
var import_compose = __toESM(require_compose(), 1);
var import_data2 = __toESM(require_data(), 1);
var import_jsx_runtime31 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='608eff82f3']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "608eff82f3");
  style.appendChild(document.createTextNode(".boot-dropdown-item__children{display:flex;flex-direction:column;margin-block-end:2px;margin-block-start:-2px;margin-inline-start:30px;overflow:hidden;padding:2px}.boot-dropdown-item__chevron.is-up{transform:rotate(180deg)}"));
  document.head.appendChild(style);
}
var ANIMATION_DURATION = 0.2;
function DropdownItem({
  className,
  id,
  icon,
  children,
  isExpanded,
  onToggle
}) {
  const menuItems = (0, import_data2.useSelect)(
    (select) => (
      // @ts-ignore
      select(STORE_NAME).getMenuItems()
    ),
    []
  );
  const items = menuItems.filter((item) => item.parent === id);
  const disableMotion = (0, import_compose.useReducedMotion)();
  return /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)("div", { className: "boot-dropdown-item", children: [
    /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
      import_components6.__experimentalItem,
      {
        className: clsx_default("boot-navigation-item", className),
        onClick: (e) => {
          e.preventDefault();
          e.stopPropagation();
          onToggle();
        },
        onMouseDown: (e) => e.preventDefault(),
        children: /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(
          import_components6.__experimentalHStack,
          {
            justify: "flex-start",
            spacing: 2,
            style: { flexGrow: "1" },
            children: [
              wrapIcon(icon, false),
              /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_components6.FlexBlock, { children }),
              /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
                import_components6.Icon,
                {
                  icon: chevron_down_small_default,
                  className: clsx_default("boot-dropdown-item__chevron", {
                    "is-up": isExpanded
                  })
                }
              )
            ]
          }
        )
      }
    ),
    /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_components6.__unstableAnimatePresence, { initial: false, children: isExpanded && /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
      import_components6.__unstableMotion.div,
      {
        initial: { height: 0 },
        animate: { height: "auto" },
        exit: { height: 0 },
        transition: {
          type: "tween",
          duration: disableMotion ? 0 : ANIMATION_DURATION,
          ease: "easeOut"
        },
        className: "boot-dropdown-item__children",
        children: items.map((item, index2) => /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
          NavigationItem,
          {
            to: item.to,
            shouldShowPlaceholder: false,
            children: item.label
          },
          index2
        ))
      }
    ) })
  ] });
}

// packages/boot/build-module/components/navigation/navigation-screen/index.mjs
var import_components7 = __toESM(require_components(), 1);
var import_i18n2 = __toESM(require_i18n(), 1);
var import_compose2 = __toESM(require_compose(), 1);
var import_jsx_runtime32 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='db54ac7e93']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "db54ac7e93");
  style.appendChild(document.createTextNode(".boot-navigation-screen{padding-block-end:4px}.boot-navigation-screen .components-text{color:var(--wpds-color-foreground-content-neutral,#1e1e1e)}.boot-navigation-screen__title-icon{padding:12px 16px 8px;position:sticky;top:0}.boot-navigation-screen__title{flex-grow:1;overflow-wrap:break-word}.boot-navigation-screen__title.boot-navigation-screen__title,.boot-navigation-screen__title.boot-navigation-screen__title .boot-navigation-screen__title{color:var(--wpds-color-foreground-content-neutral,#1e1e1e);line-height:32px}.boot-navigation-screen__actions{display:flex;flex-shrink:0}"));
  document.head.appendChild(style);
}
var ANIMATION_DURATION2 = 0.3;
var slideVariants = {
  initial: (direction) => ({
    x: direction === "forward" ? 100 : -100,
    opacity: 0
  }),
  animate: {
    x: 0,
    opacity: 1
  },
  exit: (direction) => ({
    x: direction === "forward" ? 100 : -100,
    opacity: 0
  })
};
function NavigationScreen({
  isRoot,
  title,
  actions,
  content,
  description,
  animationDirection,
  backMenuItem,
  backButtonRef,
  navigationKey,
  onNavigate
}) {
  const icon = (0, import_i18n2.isRTL)() ? chevron_right_default : chevron_left_default;
  const disableMotion = (0, import_compose2.useReducedMotion)();
  const handleBackClick = (e) => {
    e.preventDefault();
    onNavigate({ id: backMenuItem, direction: "backward" });
  };
  return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(
    "div",
    {
      className: "boot-navigation-screen",
      style: {
        overflow: "hidden",
        position: "relative",
        display: "grid",
        gridTemplateColumns: "1fr",
        gridTemplateRows: "1fr"
      },
      children: /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(import_components7.__unstableAnimatePresence, { initial: false, children: /* @__PURE__ */ (0, import_jsx_runtime32.jsxs)(
        import_components7.__unstableMotion.div,
        {
          custom: animationDirection,
          variants: slideVariants,
          initial: "initial",
          animate: "animate",
          exit: "exit",
          transition: {
            type: "tween",
            duration: disableMotion ? 0 : ANIMATION_DURATION2,
            ease: [0.33, 0, 0, 1]
          },
          style: {
            width: "100%",
            gridColumn: "1",
            gridRow: "1"
          },
          children: [
            /* @__PURE__ */ (0, import_jsx_runtime32.jsxs)(
              import_components7.__experimentalHStack,
              {
                spacing: 2,
                className: "boot-navigation-screen__title-icon",
                children: [
                  !isRoot && /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(
                    import_components7.Button,
                    {
                      ref: backButtonRef,
                      icon,
                      onClick: handleBackClick,
                      label: (0, import_i18n2.__)("Back"),
                      size: "small",
                      variant: "tertiary"
                    }
                  ),
                  /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(
                    import_components7.__experimentalHeading,
                    {
                      className: "boot-navigation-screen__title",
                      level: 1,
                      size: "15px",
                      children: title
                    }
                  ),
                  actions && /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("div", { className: "boot-navigation-screen__actions", children: actions })
                ]
              }
            ),
            description && /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("div", { className: "boot-navigation-screen__description", children: description }),
            content
          ]
        },
        navigationKey
      ) })
    }
  );
}

// packages/boot/build-module/components/navigation/use-sidebar-parent.mjs
var import_element20 = __toESM(require_element(), 1);
var import_data3 = __toESM(require_data(), 1);
import { privateApis as routePrivateApis2 } from "@wordpress/route";

// packages/boot/build-module/components/navigation/path-matching.mjs
var isValidParentPath = (currentPath, menuPath) => {
  if (!menuPath || menuPath === currentPath) {
    return false;
  }
  const normalizePath = (path) => {
    const normalized = path.startsWith("/") ? path : "/" + path;
    return normalized.endsWith("/") && normalized.length > 1 ? normalized.slice(0, -1) : normalized;
  };
  const normalizedCurrent = normalizePath(currentPath);
  const normalizedMenu = normalizePath(menuPath);
  return normalizedCurrent.startsWith(normalizedMenu) && (normalizedCurrent[normalizedMenu.length] === "/" || normalizedMenu === "/");
};
var findClosestMenuItem = (currentPath, menuItems) => {
  const exactMatch = menuItems.find((item) => item.to === currentPath);
  if (exactMatch) {
    return exactMatch;
  }
  let bestMatch = null;
  let bestPathLength = 0;
  for (const item of menuItems) {
    if (!item.to) {
      continue;
    }
    if (isValidParentPath(currentPath, item.to)) {
      if (item.to.length > bestPathLength) {
        bestMatch = item;
        bestPathLength = item.to.length;
      }
    }
  }
  return bestMatch;
};
var findDrilldownParent = (id, menuItems) => {
  if (!id) {
    return void 0;
  }
  const currentItem = menuItems.find((item) => item.id === id);
  if (!currentItem) {
    return void 0;
  }
  if (currentItem.parent) {
    const parentItem = menuItems.find(
      (item) => item.id === currentItem.parent
    );
    if (parentItem?.parent_type === "drilldown") {
      return parentItem.id;
    }
    if (parentItem) {
      return findDrilldownParent(parentItem.id, menuItems);
    }
  }
  return void 0;
};
var findDropdownParent = (id, menuItems) => {
  if (!id) {
    return void 0;
  }
  const currentItem = menuItems.find((item) => item.id === id);
  if (!currentItem) {
    return void 0;
  }
  if (currentItem.parent) {
    const parentItem = menuItems.find(
      (item) => item.id === currentItem.parent
    );
    if (parentItem?.parent_type === "dropdown") {
      return parentItem.id;
    }
  }
  return void 0;
};

// packages/boot/build-module/components/navigation/use-sidebar-parent.mjs
var { useRouter, useMatches } = unlock2(routePrivateApis2);
function useSidebarParent() {
  const matches = useMatches();
  const router = useRouter();
  const menuItems = (0, import_data3.useSelect)(
    (select) => (
      // @ts-ignore
      select(STORE_NAME).getMenuItems()
    ),
    []
  );
  const currentPath = matches[matches.length - 1].pathname.slice(
    router.options.basepath?.length ?? 0
  );
  const currentMenuItem = findClosestMenuItem(currentPath, menuItems);
  const [parentId, setParentId] = (0, import_element20.useState)(
    findDrilldownParent(currentMenuItem?.id, menuItems)
  );
  const [parentDropdownId, setParentDropdownId] = (0, import_element20.useState)(findDropdownParent(currentMenuItem?.id, menuItems));
  (0, import_element20.useEffect)(() => {
    const matchedMenuItem = findClosestMenuItem(currentPath, menuItems);
    const updatedParentId = findDrilldownParent(
      matchedMenuItem?.id,
      menuItems
    );
    const updatedDropdownParent = findDropdownParent(
      matchedMenuItem?.id,
      menuItems
    );
    setParentId(updatedParentId);
    setParentDropdownId(updatedDropdownParent);
  }, [currentPath, menuItems]);
  return [
    parentId,
    setParentId,
    parentDropdownId,
    setParentDropdownId
  ];
}

// packages/boot/build-module/components/navigation/index.mjs
var import_jsx_runtime33 = __toESM(require_jsx_runtime(), 1);
function Navigation() {
  const backButtonRef = (0, import_element21.useRef)(null);
  const [animationDirection, setAnimationDirection] = (0, import_element21.useState)(null);
  const [parentId, setParentId, parentDropdownId, setParentDropdownId] = useSidebarParent();
  const menuItems = (0, import_data4.useSelect)(
    (select) => (
      // @ts-ignore
      select(STORE_NAME).getMenuItems()
    ),
    []
  );
  const parent = (0, import_element21.useMemo)(
    () => menuItems.find((item) => item.id === parentId),
    [menuItems, parentId]
  );
  const navigationKey = parent ? `drilldown-${parent.id}` : "root";
  const handleNavigate = ({
    id,
    direction
  }) => {
    setAnimationDirection(direction);
    setParentId(id);
  };
  const handleDropdownToggle = (dropdownId) => {
    setParentDropdownId(
      parentDropdownId === dropdownId ? void 0 : dropdownId
    );
  };
  const items = (0, import_element21.useMemo)(
    () => menuItems.filter((item) => item.parent === parentId),
    [menuItems, parentId]
  );
  const hasRealIcons = items.some((item) => !!item.icon);
  return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
    NavigationScreen,
    {
      isRoot: !parent,
      title: parent ? parent.label : "",
      backMenuItem: parent?.parent,
      backButtonRef,
      animationDirection: animationDirection || void 0,
      navigationKey,
      onNavigate: handleNavigate,
      content: /* @__PURE__ */ (0, import_jsx_runtime33.jsx)("div", { role: "list", children: items.map((item) => {
        if (item.parent_type === "dropdown") {
          return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
            DropdownItem,
            {
              id: item.id,
              className: "boot-navigation-item",
              icon: item.icon,
              shouldShowPlaceholder: hasRealIcons,
              isExpanded: parentDropdownId === item.id,
              onToggle: () => handleDropdownToggle(item.id),
              children: item.label
            },
            item.id
          );
        }
        if (item.parent_type === "drilldown") {
          return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
            DrilldownItem,
            {
              id: item.id,
              icon: item.icon,
              shouldShowPlaceholder: hasRealIcons,
              onNavigate: handleNavigate,
              children: item.label
            },
            item.id
          );
        }
        return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
          NavigationItem,
          {
            to: item.to,
            icon: item.icon,
            shouldShowPlaceholder: hasRealIcons,
            children: item.label
          },
          item.id
        );
      }) })
    }
  );
}
var navigation_default = Navigation;

// packages/boot/build-module/components/save-button/index.mjs
var import_element23 = __toESM(require_element(), 1);
var import_data6 = __toESM(require_data(), 1);
var import_i18n4 = __toESM(require_i18n(), 1);
var import_core_data2 = __toESM(require_core_data(), 1);
var import_keycodes = __toESM(require_keycodes(), 1);
var import_editor2 = __toESM(require_editor(), 1);
var import_components8 = __toESM(require_components(), 1);

// packages/boot/build-module/components/save-panel/use-save-shortcut.mjs
var import_element22 = __toESM(require_element(), 1);
var import_keyboard_shortcuts = __toESM(require_keyboard_shortcuts(), 1);
var import_i18n3 = __toESM(require_i18n(), 1);
var import_data5 = __toESM(require_data(), 1);
var import_core_data = __toESM(require_core_data(), 1);
var import_editor = __toESM(require_editor(), 1);
var shortcutName = "core/boot/save";
function useSaveShortcut({
  openSavePanel
}) {
  const { __experimentalGetDirtyEntityRecords, isSavingEntityRecord } = (0, import_data5.useSelect)(import_core_data.store);
  const { hasNonPostEntityChanges, isPostSavingLocked } = (0, import_data5.useSelect)(import_editor.store);
  const { savePost } = (0, import_data5.useDispatch)(import_editor.store);
  const { registerShortcut, unregisterShortcut } = (0, import_data5.useDispatch)(
    import_keyboard_shortcuts.store
  );
  (0, import_element22.useEffect)(() => {
    registerShortcut({
      name: shortcutName,
      category: "global",
      description: (0, import_i18n3.__)("Save your changes."),
      keyCombination: {
        modifier: "primary",
        character: "s"
      }
    });
    return () => {
      unregisterShortcut(shortcutName);
    };
  }, [registerShortcut, unregisterShortcut]);
  (0, import_keyboard_shortcuts.useShortcut)(shortcutName, (event) => {
    event.preventDefault();
    const dirtyEntityRecords = __experimentalGetDirtyEntityRecords();
    const hasDirtyEntities = !!dirtyEntityRecords.length;
    const isSaving = dirtyEntityRecords.some(
      (record) => isSavingEntityRecord(record.kind, record.name, record.key)
    );
    if (!hasDirtyEntities || isSaving) {
      return;
    }
    if (hasNonPostEntityChanges()) {
      openSavePanel();
    } else if (!isPostSavingLocked()) {
      savePost();
    }
  });
}

// packages/boot/build-module/components/save-button/index.mjs
var import_jsx_runtime34 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='020f606c18']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "020f606c18");
  style.appendChild(document.createTextNode(".boot-save-button{width:100%}.boot-save-button__shortcut:not(:first-child){margin-left:8px}"));
  document.head.appendChild(style);
}
function SaveButton() {
  const [isSaveViewOpen, setIsSaveViewOpened] = (0, import_element23.useState)(false);
  const { isSaving, dirtyEntityRecordsCount } = (0, import_data6.useSelect)((select) => {
    const { isSavingEntityRecord, __experimentalGetDirtyEntityRecords } = select(import_core_data2.store);
    const dirtyEntityRecords = __experimentalGetDirtyEntityRecords();
    return {
      isSaving: dirtyEntityRecords.some(
        (record) => isSavingEntityRecord(record.kind, record.name, record.key)
      ),
      dirtyEntityRecordsCount: dirtyEntityRecords.length
    };
  }, []);
  const [showSavedState, setShowSavedState] = (0, import_element23.useState)(false);
  (0, import_element23.useEffect)(() => {
    if (isSaving) {
      setShowSavedState(true);
    }
  }, [isSaving]);
  const hasChanges = dirtyEntityRecordsCount > 0;
  (0, import_element23.useEffect)(() => {
    if (!isSaving && hasChanges) {
      setShowSavedState(false);
    }
  }, [isSaving, hasChanges]);
  function hideSavedState() {
    if (showSavedState) {
      setShowSavedState(false);
    }
  }
  const shouldShowButton = hasChanges || showSavedState;
  useSaveShortcut({ openSavePanel: () => setIsSaveViewOpened(true) });
  if (!shouldShowButton) {
    return null;
  }
  const isInSavedState = showSavedState && !hasChanges;
  const disabled2 = isSaving || isInSavedState;
  const getLabel = () => {
    if (isInSavedState) {
      return (0, import_i18n4.__)("Saved");
    }
    return (0, import_i18n4.sprintf)(
      // translators: %d: number of unsaved changes (number).
      (0, import_i18n4._n)(
        "Review %d change…",
        "Review %d changes…",
        dirtyEntityRecordsCount
      ),
      dirtyEntityRecordsCount
    );
  };
  const label = getLabel();
  const shortcut = import_keycodes.displayShortcut.primary("s");
  return /* @__PURE__ */ (0, import_jsx_runtime34.jsxs)(import_jsx_runtime34.Fragment, { children: [
    /* @__PURE__ */ (0, import_jsx_runtime34.jsxs)(tooltip_exports.Root, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(
        tooltip_exports.Trigger,
        {
          render: /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(
            import_components8.Button,
            {
              variant: "primary",
              size: "compact",
              onClick: () => setIsSaveViewOpened(true),
              onBlur: hideSavedState,
              disabled: disabled2,
              accessibleWhenDisabled: true,
              isBusy: isSaving,
              "aria-keyshortcuts": import_keycodes.rawShortcut.primary("s"),
              className: "boot-save-button",
              icon: isInSavedState ? check_default : void 0,
              children: label
            }
          )
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime34.jsxs)(tooltip_exports.Popup, { children: [
        hasChanges && /* @__PURE__ */ (0, import_jsx_runtime34.jsx)("span", { children: label }),
        /* @__PURE__ */ (0, import_jsx_runtime34.jsx)("span", { className: "boot-save-button__shortcut", children: shortcut })
      ] })
    ] }),
    isSaveViewOpen && /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(
      import_components8.Modal,
      {
        title: (0, import_i18n4.__)("Review changes"),
        onRequestClose: () => setIsSaveViewOpened(false),
        size: "small",
        children: /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(
          import_editor2.EntitiesSavedStates,
          {
            close: () => setIsSaveViewOpened(false),
            variant: "inline"
          }
        )
      }
    )
  ] });
}

// packages/boot/build-module/components/sidebar/index.mjs
var import_jsx_runtime35 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='17b769ae66']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "17b769ae66");
  style.appendChild(document.createTextNode(".boot-sidebar__scrollable{display:flex;flex-direction:column;height:100%;overflow:auto;position:relative}.boot-sidebar__content{contain:content;flex-grow:1;position:relative}.boot-sidebar__footer{padding:16px 8px 8px 16px}.boot-sidebar__back-button{margin:16px 12px 8px}"));
  document.head.appendChild(style);
}
function DashboardBackButton() {
  const dashboardLink = (0, import_data7.useSelect)(
    (select) => select(store).getDashboardLink(),
    []
  );
  return /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
    import_components9.Button,
    {
      __next40pxDefaultSize: true,
      className: "boot-sidebar__back-button",
      href: dashboardLink || "/",
      icon: (0, import_i18n5.isRTL)() ? chevron_right_default : chevron_left_default,
      label: (0, import_i18n5.__)("Go to the Dashboard"),
      children: (0, import_i18n5.__)("Dashboard")
    }
  );
}
function Sidebar() {
  return /* @__PURE__ */ (0, import_jsx_runtime35.jsxs)("div", { className: "boot-sidebar__scrollable", children: [
    /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(DashboardBackButton, {}),
    /* @__PURE__ */ (0, import_jsx_runtime35.jsx)("div", { className: "boot-sidebar__content", children: /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(navigation_default, {}) }),
    /* @__PURE__ */ (0, import_jsx_runtime35.jsx)("div", { className: "boot-sidebar__footer", children: /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(SaveButton, {}) })
  ] });
}

// packages/boot/build-module/components/save-panel/index.mjs
var import_element24 = __toESM(require_element(), 1);
var import_components10 = __toESM(require_components(), 1);
var import_editor3 = __toESM(require_editor(), 1);
var import_i18n6 = __toESM(require_i18n(), 1);
var import_jsx_runtime36 = __toESM(require_jsx_runtime(), 1);
function SavePanel() {
  const [isOpen, setIsOpen] = (0, import_element24.useState)(false);
  useSaveShortcut({
    openSavePanel: () => setIsOpen(true)
  });
  if (!isOpen) {
    return false;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
    import_components10.Modal,
    {
      className: "edit-site-save-panel__modal",
      onRequestClose: () => setIsOpen(false),
      title: (0, import_i18n6.__)("Review changes"),
      size: "small",
      children: /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
        import_editor3.EntitiesSavedStates,
        {
          close: () => setIsOpen(false),
          variant: "inline"
        }
      )
    }
  );
}

// packages/boot/build-module/components/canvas-renderer/index.mjs
var import_element26 = __toESM(require_element(), 1);

// packages/boot/build-module/components/canvas/index.mjs
var import_element25 = __toESM(require_element(), 1);
var import_components12 = __toESM(require_components(), 1);
import { useNavigate } from "@wordpress/route";

// packages/boot/build-module/components/canvas/back-button.mjs
var import_components11 = __toESM(require_components(), 1);
var import_i18n7 = __toESM(require_i18n(), 1);
var import_jsx_runtime37 = __toESM(require_jsx_runtime(), 1);
function BootBackButton({ length }) {
  const handleBack = () => {
    window.history.back();
  };
  if (length > 1) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
    import_components11.Button,
    {
      size: "compact",
      onClick: handleBack,
      label: (0, import_i18n7.__)("Go back"),
      icon: (0, import_i18n7.isRTL)() ? chevron_right_default : chevron_left_default
    }
  );
}

// packages/boot/build-module/components/canvas/index.mjs
var import_jsx_runtime38 = __toESM(require_jsx_runtime(), 1);
function Canvas({ canvas }) {
  const [Editor, setEditor] = (0, import_element25.useState)(null);
  const navigate = useNavigate();
  (0, import_element25.useEffect)(() => {
    import("@wordpress/lazy-editor").then((module) => {
      setEditor(() => module.Editor);
    }).catch((error) => {
      console.error("Failed to load lazy editor:", error);
    });
  }, []);
  if (!Editor) {
    return /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
      "div",
      {
        style: {
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
          height: "100%",
          padding: "2rem"
        },
        children: /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(import_components12.Spinner, {})
      }
    );
  }
  const backButton = !canvas.isPreview ? ({ length }) => /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(BootBackButton, { length }) : void 0;
  return /* @__PURE__ */ (0, import_jsx_runtime38.jsxs)("div", { style: { height: "100%", position: "relative" }, children: [
    /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
      "div",
      {
        style: { height: "100%" },
        inert: canvas.isPreview ? "true" : void 0,
        children: /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
          Editor,
          {
            postType: canvas.postType,
            postId: canvas.postId,
            settings: {
              isPreviewMode: canvas.isPreview,
              styles: canvas.isPreview ? [{ css: "body{min-height:100vh;}" }] : []
            },
            backButton
          }
        )
      }
    ),
    canvas.isPreview && canvas.editLink && /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
      "div",
      {
        onClick: () => navigate({ to: canvas.editLink }),
        onKeyDown: (e) => {
          if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            navigate({ to: canvas.editLink });
          }
        },
        style: {
          position: "absolute",
          inset: 0,
          cursor: "pointer",
          zIndex: 1
        },
        role: "button",
        tabIndex: 0,
        "aria-label": "Click to edit"
      }
    )
  ] });
}

// packages/boot/build-module/components/canvas-renderer/index.mjs
var import_jsx_runtime39 = __toESM(require_jsx_runtime(), 1);
function CanvasRenderer({
  canvas,
  routeContentModule
}) {
  const [CustomCanvas, setCustomCanvas] = (0, import_element26.useState)(null);
  (0, import_element26.useEffect)(() => {
    if (canvas === null && routeContentModule) {
      import(routeContentModule).then((module) => {
        setCustomCanvas(() => module.canvas);
      }).catch((error) => {
        console.error("Failed to load custom canvas:", error);
      });
    } else {
      setCustomCanvas(null);
    }
  }, [canvas, routeContentModule]);
  if (canvas === void 0) {
    return null;
  }
  if (canvas === null) {
    if (!CustomCanvas) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime39.jsx)(CustomCanvas, {});
  }
  return /* @__PURE__ */ (0, import_jsx_runtime39.jsx)(Canvas, { canvas });
}

// packages/boot/build-module/components/app/use-route-title.mjs
var import_element27 = __toESM(require_element(), 1);
var import_data8 = __toESM(require_data(), 1);
var import_core_data3 = __toESM(require_core_data(), 1);
var import_i18n8 = __toESM(require_i18n(), 1);
var import_html_entities = __toESM(require_html_entities(), 1);
import { speak } from "@wordpress/a11y";
import { privateApis as routePrivateApis3 } from "@wordpress/route";
var { useLocation, useMatches: useMatches2 } = unlock2(routePrivateApis3);
function useRouteTitle() {
  const location = useLocation();
  const matches = useMatches2();
  const currentMatch = matches[matches.length - 1];
  const routeTitle = currentMatch?.loaderData?.title;
  const siteTitle = (0, import_data8.useSelect)(
    (select) => select(import_core_data3.store).getEntityRecord(
      "root",
      "__unstableBase"
    )?.name,
    []
  );
  const isInitialLocationRef = (0, import_element27.useRef)(true);
  (0, import_element27.useEffect)(() => {
    isInitialLocationRef.current = false;
  }, [location]);
  (0, import_element27.useEffect)(() => {
    if (isInitialLocationRef.current) {
      return;
    }
    if (routeTitle && typeof routeTitle === "string" && siteTitle && typeof siteTitle === "string") {
      const decodedRouteTitle = (0, import_html_entities.decodeEntities)(routeTitle);
      const decodedSiteTitle = (0, import_html_entities.decodeEntities)(siteTitle);
      const formattedTitle = (0, import_i18n8.sprintf)(
        /* translators: Admin document title. 1: Admin screen name, 2: Site name. */
        (0, import_i18n8.__)("%1$s ‹ %2$s — WordPress"),
        decodedRouteTitle,
        decodedSiteTitle
      );
      document.title = formattedTitle;
      if (decodedRouteTitle) {
        speak(decodedRouteTitle, "assertive");
      }
    }
  }, [routeTitle, siteTitle, location]);
}

// packages/boot/build-module/components/root/use-sync-body-background.mjs
var import_element28 = __toESM(require_element(), 1);
var import_compose3 = __toESM(require_compose(), 1);
function useSyncBodyBackground() {
  const layoutRef = (0, import_element28.useRef)(null);
  (0, import_compose3.useIsomorphicLayoutEffect)(() => {
    if (!layoutRef.current) {
      return;
    }
    const body = layoutRef.current.ownerDocument.body;
    const bg = getComputedStyle(layoutRef.current).backgroundColor;
    if (!bg) {
      return;
    }
    const previousBackground = body.style.background;
    body.style.background = bg;
    return () => {
      body.style.background = previousBackground;
    };
  }, []);
  return layoutRef;
}

// packages/boot/build-module/components/root/index.mjs
var import_jsx_runtime40 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='d38c56df8c']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "d38c56df8c");
  style.appendChild(document.createTextNode(".boot-layout{background:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);display:flex;flex-direction:row;height:100%;isolation:isolate;width:100%}.boot-layout__sidebar-backdrop{background-color:rgba(0,0,0,.5);bottom:0;cursor:var(--wpds-cursor-control,pointer);left:0;position:fixed;right:0;top:0;z-index:100002}.boot-layout__sidebar{flex-shrink:0;height:100%;overflow:hidden;position:relative;width:240px}.boot-layout__sidebar.is-mobile{background:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);bottom:0;box-shadow:2px 0 8px rgba(0,0,0,.2);inset-inline-start:0;max-width:85vw;position:fixed;top:0;width:300px;z-index:100003}.boot-layout__mobile-sidebar-drawer{left:0;position:fixed;right:0;top:0}.boot-layout--single-page .boot-layout__mobile-sidebar-drawer{top:46px}.boot-layout__mobile-sidebar-drawer{align-items:center;background:var(--wpds-color-background-surface-neutral,#fcfcfc);border-bottom:1px solid var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);display:flex;justify-content:flex-start;padding:16px;z-index:3}.boot-layout__canvas.has-mobile-drawer{padding-top:65px;position:relative}.boot-layout__surfaces{display:flex;flex-grow:1;gap:8px;margin:0}@media (min-width:782px){.boot-layout__surfaces{margin:8px}.boot-layout--single-page .boot-layout__surfaces{margin-top:0;margin-inline-start:0}}.boot-layout__inspector,.boot-layout__stage{background:var(--wpds-color-background-surface-neutral,#fcfcfc);border-radius:0;bottom:0;color:var(--wpds-color-foreground-content-neutral,#1e1e1e);flex:1;height:100vh;left:0;margin:0;overflow-y:auto;position:relative;position:fixed;right:0;top:0;width:100vw}.boot-layout--single-page .boot-layout__inspector,.boot-layout--single-page .boot-layout__stage{height:calc(100vh - 46px);top:46px}@media (min-width:782px){.boot-layout__inspector,.boot-layout__stage{border-radius:var(--wpds-border-radius-xl,12px);height:auto;margin:0;position:static;width:auto}.boot-layout--single-page .boot-layout__inspector,.boot-layout--single-page .boot-layout__stage{height:auto}}.boot-layout__stage{z-index:2}@media (min-width:782px){.boot-layout__stage{z-index:auto}}.boot-layout__inspector{z-index:3}@media (min-width:782px){.boot-layout__inspector{z-index:auto}}.boot-layout__canvas{background:var(--wpds-color-background-surface-neutral,#fcfcfc);border:1px solid var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);border-radius:0;bottom:0;box-shadow:0 1px 3px rgba(0,0,0,.1);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);flex:1;height:100vh;left:0;margin:0;overflow-y:auto;position:relative;position:fixed;right:0;top:0;width:100vw;z-index:1}.boot-layout--single-page .boot-layout__canvas{height:calc(100vh - 46px);top:46px}@media (min-width:782px){.boot-layout__canvas{border-radius:var(--wpds-border-radius-xl,12px);height:auto;position:static;width:auto;z-index:auto}.boot-layout--single-page .boot-layout__canvas{height:auto}.boot-layout.has-canvas .boot-layout__stage,.boot-layout__inspector{max-width:400px}}.boot-layout__canvas .interface-interface-skeleton{height:100%;left:0!important;position:relative;top:0!important}.boot-layout.has-full-canvas .boot-layout__surfaces{gap:0;margin:0}.boot-layout.has-full-canvas .boot-layout__inspector,.boot-layout.has-full-canvas .boot-layout__stage{display:none}.boot-layout.has-full-canvas .boot-layout__canvas{border:none;border-radius:0;bottom:0;box-shadow:none;left:0;margin:0;max-width:none;overflow:hidden;position:fixed;right:0;top:var(--wp-admin--admin-bar--height,0)}.boot-layout--single-page .boot-layout.has-full-canvas .boot-layout__canvas{top:46px}@media (min-width:782px){.boot-layout--single-page .boot-layout.has-full-canvas .boot-layout__canvas{top:32px}}@media (max-width:782px){.boot-layout__canvas:not(.has-mobile-drawer),.boot-layout__inspector,.boot-layout__stage{height:calc(100vh - var(--wp-admin--admin-bar--height, 0px));top:var(--wp-admin--admin-bar--height,0)}.boot-layout__mobile-sidebar-drawer{top:var(--wp-admin--admin-bar--height,0)}}"));
  document.head.appendChild(style);
}
var { useLocation: useLocation2, useMatches: useMatches3, Outlet } = unlock2(routePrivateApis4);
function Root2() {
  const matches = useMatches3();
  const location = useLocation2();
  const currentMatch = matches[matches.length - 1];
  const canvas = currentMatch?.loaderData?.canvas;
  const routeContentModule = currentMatch?.loaderData?.routeContentModule;
  const isFullScreen = canvas && !canvas.isPreview;
  useRouteTitle();
  const isMobileViewport = (0, import_compose4.useViewportMatch)("medium", "<");
  const [isMobileSidebarOpen, setIsMobileSidebarOpen] = (0, import_element29.useState)(false);
  const disableMotion = (0, import_compose4.useReducedMotion)();
  (0, import_element29.useEffect)(() => {
    setIsMobileSidebarOpen(false);
  }, [location.pathname, isMobileViewport]);
  const themeColors = (0, import_element29.useMemo)(getAdminThemeColors, []);
  const layoutRef = useSyncBodyBackground();
  return /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(import_components13.SlotFillProvider, { children: /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(tooltip_exports.Provider, { children: /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(
    import_theme.ThemeProvider,
    {
      isRoot: true,
      color: { ...themeColors, background: "#f8f8f8" },
      children: /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(import_theme.ThemeProvider, { color: themeColors, children: /* @__PURE__ */ (0, import_jsx_runtime40.jsxs)(
        "div",
        {
          ref: layoutRef,
          className: clsx_default("boot-layout", {
            "has-canvas": !!canvas || canvas === null,
            "has-full-canvas": isFullScreen
          }),
          children: [
            /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(SavePanel, {}),
            /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(import_notices.SnackbarNotices, { className: "boot-notices__snackbar" }),
            isMobileViewport && /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(page_default.SidebarToggleFill, { children: /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(
              import_components13.Button,
              {
                icon: menu_default,
                onClick: () => setIsMobileSidebarOpen(true),
                label: (0, import_i18n9.__)("Open navigation panel"),
                size: "compact"
              }
            ) }),
            /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(import_components13.__unstableAnimatePresence, { children: isMobileViewport && isMobileSidebarOpen && !isFullScreen && /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(
              import_components13.__unstableMotion.div,
              {
                initial: { opacity: 0 },
                animate: { opacity: 1 },
                exit: { opacity: 0 },
                transition: {
                  type: "tween",
                  duration: disableMotion ? 0 : 0.2,
                  ease: "easeOut"
                },
                className: "boot-layout__sidebar-backdrop",
                onClick: () => setIsMobileSidebarOpen(false),
                onKeyDown: (event) => {
                  if (event.key === "Escape") {
                    setIsMobileSidebarOpen(
                      false
                    );
                  }
                },
                role: "button",
                tabIndex: -1,
                "aria-label": (0, import_i18n9.__)(
                  "Close navigation panel"
                )
              }
            ) }),
            /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(import_components13.__unstableAnimatePresence, { children: isMobileViewport && isMobileSidebarOpen && !isFullScreen && /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(
              import_components13.__unstableMotion.div,
              {
                initial: { x: "-100%" },
                animate: { x: 0 },
                exit: { x: "-100%" },
                transition: {
                  type: "tween",
                  duration: disableMotion ? 0 : 0.2,
                  ease: "easeOut"
                },
                className: "boot-layout__sidebar is-mobile",
                children: /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(Sidebar, {})
              }
            ) }),
            !isMobileViewport && !isFullScreen && /* @__PURE__ */ (0, import_jsx_runtime40.jsx)("div", { className: "boot-layout__sidebar", children: /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(Sidebar, {}) }),
            /* @__PURE__ */ (0, import_jsx_runtime40.jsx)("div", { className: "boot-layout__surfaces", children: /* @__PURE__ */ (0, import_jsx_runtime40.jsxs)(
              import_theme.ThemeProvider,
              {
                color: {
                  ...themeColors,
                  background: "#ffffff"
                },
                children: [
                  /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(Outlet, {}),
                  (canvas || canvas === null) && /* @__PURE__ */ (0, import_jsx_runtime40.jsxs)(
                    "div",
                    {
                      className: clsx_default(
                        "boot-layout__canvas",
                        {
                          "has-mobile-drawer": canvas?.isPreview && isMobileViewport
                        }
                      ),
                      children: [
                        canvas?.isPreview && isMobileViewport && /* @__PURE__ */ (0, import_jsx_runtime40.jsx)("div", { className: "boot-layout__mobile-sidebar-drawer", children: /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(
                          import_components13.Button,
                          {
                            icon: menu_default,
                            onClick: () => setIsMobileSidebarOpen(
                              true
                            ),
                            label: (0, import_i18n9.__)(
                              "Open navigation panel"
                            ),
                            size: "compact"
                          }
                        ) }),
                        /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(
                          CanvasRenderer,
                          {
                            canvas,
                            routeContentModule
                          }
                        )
                      ]
                    }
                  )
                ]
              }
            ) })
          ]
        }
      ) })
    }
  ) }) });
}

// packages/boot/build-module/components/app/router.mjs
var import_jsx_runtime41 = __toESM(require_jsx_runtime(), 1);
var {
  createLazyRoute,
  createRouter,
  createRootRoute,
  createRoute,
  RouterProvider,
  createBrowserHistory,
  parseHref,
  useLoaderData
} = unlock2(routePrivateApis5);
function NotFoundComponent() {
  return /* @__PURE__ */ (0, import_jsx_runtime41.jsx)("div", { className: "boot-layout__stage", children: /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(page_default, { title: (0, import_i18n10.__)("Route not found"), hasPadding: true, children: (0, import_i18n10.__)("The page you're looking for does not exist") }) });
}
function createRouteFromDefinition(route, parentRoute) {
  let tanstackRoute = createRoute({
    getParentRoute: () => parentRoute,
    path: route.path,
    beforeLoad: async (opts) => {
      if (route.route_module) {
        const module = await import(route.route_module);
        const routeConfig = module.route || {};
        if (routeConfig.beforeLoad) {
          return routeConfig.beforeLoad({
            params: opts.params || {},
            search: opts.search || {}
          });
        }
      }
    },
    loader: async (opts) => {
      let routeConfig = {};
      if (route.route_module) {
        const module = await import(route.route_module);
        routeConfig = module.route || {};
      }
      const context = {
        params: opts.params || {},
        search: opts.deps || {}
      };
      const [, loaderData, canvasData, titleData] = await Promise.all([
        (0, import_data9.resolveSelect)(import_core_data4.store).getEntityRecord(
          "root",
          "__unstableBase"
        ),
        routeConfig.loader ? routeConfig.loader(context) : Promise.resolve(void 0),
        routeConfig.canvas ? routeConfig.canvas(context) : Promise.resolve(void 0),
        routeConfig.title ? routeConfig.title(context) : Promise.resolve(void 0)
      ]);
      let inspector = true;
      if (routeConfig.inspector) {
        inspector = await routeConfig.inspector(context);
      }
      return {
        ...loaderData,
        canvas: canvasData,
        inspector,
        title: titleData,
        routeContentModule: route.content_module
      };
    },
    loaderDeps: (opts) => opts.search
  });
  tanstackRoute = tanstackRoute.lazy(async () => {
    const module = route.content_module ? await import(route.content_module) : {};
    const Stage = module.stage;
    const Inspector = module.inspector;
    return createLazyRoute(route.path)({
      component: function RouteComponent() {
        const { inspector: showInspector } = useLoaderData({ from: route.path }) ?? {};
        return /* @__PURE__ */ (0, import_jsx_runtime41.jsxs)(import_jsx_runtime41.Fragment, { children: [
          Stage && /* @__PURE__ */ (0, import_jsx_runtime41.jsx)("div", { className: "boot-layout__stage", children: /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(Stage, {}) }),
          Inspector && showInspector && /* @__PURE__ */ (0, import_jsx_runtime41.jsx)("div", { className: "boot-layout__inspector", children: /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(Inspector, {}) })
        ] });
      }
    });
  });
  return tanstackRoute;
}
function createRouteTree(routes, rootComponent = Root2) {
  const rootRoute = createRootRoute({
    component: rootComponent,
    context: () => ({})
  });
  const dynamicRoutes = routes.map(
    (route) => createRouteFromDefinition(route, rootRoute)
  );
  return rootRoute.addChildren(dynamicRoutes);
}
function createPathHistory() {
  return createBrowserHistory({
    parseLocation: () => {
      const url = new URL(window.location.href);
      const path = url.searchParams.get("p") || "/";
      const pathHref = `${path}${url.hash}`;
      return parseHref(pathHref, window.history.state);
    },
    createHref: (href) => {
      const searchParams = new URLSearchParams(window.location.search);
      searchParams.set("p", href);
      return `${window.location.pathname}?${searchParams}`;
    }
  });
}
function Router({
  routes,
  rootComponent = Root2
}) {
  const router = (0, import_element30.useMemo)(() => {
    const history = createPathHistory();
    const routeTree = createRouteTree(routes, rootComponent);
    return createRouter({
      history,
      routeTree,
      defaultPreload: "intent",
      defaultNotFoundComponent: NotFoundComponent,
      defaultViewTransition: {
        types: ({
          fromLocation
        }) => {
          if (!fromLocation) {
            return false;
          }
          return ["navigate"];
        }
      }
    });
  }, [routes, rootComponent]);
  return /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(RouterProvider, { router });
}

// packages/boot/build-module/components/root/single-page.mjs
var import_notices2 = __toESM(require_notices(), 1);
var import_components14 = __toESM(require_components(), 1);
var import_element31 = __toESM(require_element(), 1);
import { privateApis as routePrivateApis6 } from "@wordpress/route";
var import_theme2 = __toESM(require_theme(), 1);
var import_jsx_runtime42 = __toESM(require_jsx_runtime(), 1);
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='d38c56df8c']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "d38c56df8c");
  style.appendChild(document.createTextNode(".boot-layout{background:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);display:flex;flex-direction:row;height:100%;isolation:isolate;width:100%}.boot-layout__sidebar-backdrop{background-color:rgba(0,0,0,.5);bottom:0;cursor:var(--wpds-cursor-control,pointer);left:0;position:fixed;right:0;top:0;z-index:100002}.boot-layout__sidebar{flex-shrink:0;height:100%;overflow:hidden;position:relative;width:240px}.boot-layout__sidebar.is-mobile{background:var(--wpds-color-background-surface-neutral-weak,#f4f4f4);bottom:0;box-shadow:2px 0 8px rgba(0,0,0,.2);inset-inline-start:0;max-width:85vw;position:fixed;top:0;width:300px;z-index:100003}.boot-layout__mobile-sidebar-drawer{left:0;position:fixed;right:0;top:0}.boot-layout--single-page .boot-layout__mobile-sidebar-drawer{top:46px}.boot-layout__mobile-sidebar-drawer{align-items:center;background:var(--wpds-color-background-surface-neutral,#fcfcfc);border-bottom:1px solid var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);display:flex;justify-content:flex-start;padding:16px;z-index:3}.boot-layout__canvas.has-mobile-drawer{padding-top:65px;position:relative}.boot-layout__surfaces{display:flex;flex-grow:1;gap:8px;margin:0}@media (min-width:782px){.boot-layout__surfaces{margin:8px}.boot-layout--single-page .boot-layout__surfaces{margin-top:0;margin-inline-start:0}}.boot-layout__inspector,.boot-layout__stage{background:var(--wpds-color-background-surface-neutral,#fcfcfc);border-radius:0;bottom:0;color:var(--wpds-color-foreground-content-neutral,#1e1e1e);flex:1;height:100vh;left:0;margin:0;overflow-y:auto;position:relative;position:fixed;right:0;top:0;width:100vw}.boot-layout--single-page .boot-layout__inspector,.boot-layout--single-page .boot-layout__stage{height:calc(100vh - 46px);top:46px}@media (min-width:782px){.boot-layout__inspector,.boot-layout__stage{border-radius:var(--wpds-border-radius-xl,12px);height:auto;margin:0;position:static;width:auto}.boot-layout--single-page .boot-layout__inspector,.boot-layout--single-page .boot-layout__stage{height:auto}}.boot-layout__stage{z-index:2}@media (min-width:782px){.boot-layout__stage{z-index:auto}}.boot-layout__inspector{z-index:3}@media (min-width:782px){.boot-layout__inspector{z-index:auto}}.boot-layout__canvas{background:var(--wpds-color-background-surface-neutral,#fcfcfc);border:1px solid var(--wpds-color-stroke-surface-neutral-weak,#f0f0f0);border-radius:0;bottom:0;box-shadow:0 1px 3px rgba(0,0,0,.1);color:var(--wpds-color-foreground-content-neutral,#1e1e1e);flex:1;height:100vh;left:0;margin:0;overflow-y:auto;position:relative;position:fixed;right:0;top:0;width:100vw;z-index:1}.boot-layout--single-page .boot-layout__canvas{height:calc(100vh - 46px);top:46px}@media (min-width:782px){.boot-layout__canvas{border-radius:var(--wpds-border-radius-xl,12px);height:auto;position:static;width:auto;z-index:auto}.boot-layout--single-page .boot-layout__canvas{height:auto}.boot-layout.has-canvas .boot-layout__stage,.boot-layout__inspector{max-width:400px}}.boot-layout__canvas .interface-interface-skeleton{height:100%;left:0!important;position:relative;top:0!important}.boot-layout.has-full-canvas .boot-layout__surfaces{gap:0;margin:0}.boot-layout.has-full-canvas .boot-layout__inspector,.boot-layout.has-full-canvas .boot-layout__stage{display:none}.boot-layout.has-full-canvas .boot-layout__canvas{border:none;border-radius:0;bottom:0;box-shadow:none;left:0;margin:0;max-width:none;overflow:hidden;position:fixed;right:0;top:var(--wp-admin--admin-bar--height,0)}.boot-layout--single-page .boot-layout.has-full-canvas .boot-layout__canvas{top:46px}@media (min-width:782px){.boot-layout--single-page .boot-layout.has-full-canvas .boot-layout__canvas{top:32px}}@media (max-width:782px){.boot-layout__canvas:not(.has-mobile-drawer),.boot-layout__inspector,.boot-layout__stage{height:calc(100vh - var(--wp-admin--admin-bar--height, 0px));top:var(--wp-admin--admin-bar--height,0)}.boot-layout__mobile-sidebar-drawer{top:var(--wp-admin--admin-bar--height,0)}}"));
  document.head.appendChild(style);
}
var { useMatches: useMatches4, Outlet: Outlet2 } = unlock2(routePrivateApis6);
function RootSinglePage() {
  const matches = useMatches4();
  const currentMatch = matches[matches.length - 1];
  const canvas = currentMatch?.loaderData?.canvas;
  const routeContentModule = currentMatch?.loaderData?.routeContentModule;
  const isFullScreen = canvas && !canvas.isPreview;
  useRouteTitle();
  const themeColors = (0, import_element31.useMemo)(getAdminThemeColors, []);
  const layoutRef = useSyncBodyBackground();
  return /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(import_components14.SlotFillProvider, { children: /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(
    import_theme2.ThemeProvider,
    {
      isRoot: true,
      color: { ...themeColors, background: "#f8f8f8" },
      children: /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(import_theme2.ThemeProvider, { color: themeColors, children: /* @__PURE__ */ (0, import_jsx_runtime42.jsxs)(
        "div",
        {
          ref: layoutRef,
          className: clsx_default(
            "boot-layout boot-layout--single-page",
            {
              "has-canvas": !!canvas || canvas === null,
              "has-full-canvas": isFullScreen
            }
          ),
          children: [
            /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(SavePanel, {}),
            /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(import_notices2.SnackbarNotices, { className: "boot-notices__snackbar" }),
            /* @__PURE__ */ (0, import_jsx_runtime42.jsx)("div", { className: "boot-layout__surfaces", children: /* @__PURE__ */ (0, import_jsx_runtime42.jsxs)(
              import_theme2.ThemeProvider,
              {
                color: {
                  ...themeColors,
                  background: "#ffffff"
                },
                children: [
                  /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(Outlet2, {}),
                  (canvas || canvas === null) && /* @__PURE__ */ (0, import_jsx_runtime42.jsx)("div", { className: "boot-layout__canvas", children: /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(
                    CanvasRenderer,
                    {
                      canvas,
                      routeContentModule
                    }
                  ) })
                ]
              }
            ) })
          ]
        }
      ) })
    }
  ) });
}

// packages/boot/build-module/components/app/index.mjs
var import_jsx_runtime43 = __toESM(require_jsx_runtime(), 1);
function App({ rootComponent }) {
  const routes = (0, import_data10.useSelect)((select) => select(store).getRoutes(), []);
  return /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(Router, { routes, rootComponent });
}
async function runInitModules(initModules) {
  for (const moduleId of initModules ?? []) {
    const module = await import(moduleId);
    await module.init();
  }
}
async function init({
  mountId,
  menuItems,
  routes,
  initModules,
  dashboardLink
}) {
  (menuItems ?? []).forEach((menuItem) => {
    (0, import_data10.dispatch)(store).registerMenuItem(menuItem.id, menuItem);
  });
  (routes ?? []).forEach((route) => {
    (0, import_data10.dispatch)(store).registerRoute(route);
  });
  if (dashboardLink) {
    (0, import_data10.dispatch)(store).setDashboardLink(dashboardLink);
  }
  await runInitModules(initModules);
  const rootElement = document.getElementById(mountId);
  if (rootElement) {
    const root = (0, import_element32.createRoot)(rootElement);
    root.render(
      /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(import_element32.StrictMode, { children: /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(App, {}) })
    );
  }
}
async function initSinglePage({
  mountId,
  routes,
  initModules
}) {
  (routes ?? []).forEach((route) => {
    (0, import_data10.dispatch)(store).registerRoute(route);
  });
  await runInitModules(initModules);
  const rootElement = document.getElementById(mountId);
  if (rootElement) {
    const root = (0, import_element32.createRoot)(rootElement);
    root.render(
      /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(import_element32.StrictMode, { children: /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(App, { rootComponent: RootSinglePage }) })
    );
  }
}

// packages/boot/build-module/index.mjs
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='54fd778906']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "54fd778906");
  style.appendChild(document.createTextNode("@media (min-width:600px){.boot-layout-container .boot-layout{bottom:0;left:0;min-height:calc(100vh - 46px);position:absolute;right:0;top:0}}@media (min-width:782px){.boot-layout-container .boot-layout{min-height:calc(100vh - 32px)}body:has(.boot-layout.has-full-canvas) .boot-layout-container .boot-layout{min-height:100vh}}.boot-layout-container .boot-layout img{height:auto;max-width:100%}.boot-layout .boot-notices__snackbar{bottom:24px;box-sizing:border-box;display:flex;flex-direction:column;left:0;padding-inline:16px;pointer-events:none;position:fixed;right:0}.boot-layout .boot-notices__snackbar .components-snackbar{margin-inline:auto}"));
  document.head.appendChild(style);
}
if (typeof document !== "undefined" && true && !document.head.querySelector("style[data-wp-hash='28f077581a']")) {
  const style = document.createElement("style");
  style.setAttribute("data-wp-hash", "28f077581a");
  style.appendChild(document.createTextNode("@media (max-width:782px){*{view-transition-name:none!important}}::view-transition-new(root),::view-transition-old(root){animation-duration:.25s}@media not (prefers-reduced-motion:reduce){.boot-layout__canvas .interface-interface-skeleton__header{view-transition-name:boot--canvas-header}.boot-layout__canvas .interface-interface-skeleton__sidebar{view-transition-name:boot--canvas-sidebar}.boot-layout__stage{view-transition-name:boot--stage}.boot-layout__inspector{view-transition-name:boot--inspector}.boot-layout__canvas.is-full-canvas .interface-interface-skeleton__content,.boot-layout__canvas:not(.is-full-canvas){view-transition-name:boot--canvas}@supports (-webkit-hyphens:none) and (not (-moz-appearance:none)){.boot-layout__stage{view-transition-name:boot-safari--stage}.boot-layout__inspector{view-transition-name:boot-safari--inspector}.boot-layout__canvas.is-full-canvas .interface-interface-skeleton__content,.boot-layout__canvas:not(.is-full-canvas){view-transition-name:boot-safari--canvas}}.components-popover:first-of-type{view-transition-name:boot--components-popover}}::view-transition-group(boot--canvas),::view-transition-group(boot--canvas-header),::view-transition-group(boot--canvas-sidebar),::view-transition-group(boot-safari--canvas){z-index:1}::view-transition-new(boot-safari--canvas),::view-transition-new(boot-safari--inspector),::view-transition-new(boot-safari--stage),::view-transition-old(boot-safari--canvas),::view-transition-old(boot-safari--inspector),::view-transition-old(boot-safari--stage){width:auto}::view-transition-new(boot--canvas),::view-transition-new(boot--inspector),::view-transition-new(boot--stage),::view-transition-old(boot--canvas),::view-transition-old(boot--inspector),::view-transition-old(boot--stage){background:#fff;border-radius:var(--wpds-border-radius-xl,12px);height:100%;object-fit:none;object-position:left top;overflow:hidden;width:100%}::view-transition-new(boot--canvas),::view-transition-old(boot--canvas){object-position:center top}::view-transition-old(boot--inspector):only-child,::view-transition-old(boot--stage):only-child,::view-transition-old(boot-safari--inspector):only-child,::view-transition-old(boot-safari--stage):only-child{animation-name:zoomOut;will-change:transform,opacity}::view-transition-new(boot--inspector):only-child,::view-transition-new(boot--stage):only-child,::view-transition-new(boot-safari--inspector):only-child,::view-transition-new(boot-safari--stage):only-child{animation-name:zoomIn;will-change:transform,opacity}@keyframes zoomOut{0%{opacity:1;transform:scale(1)}to{opacity:0;transform:scale(.9)}}@keyframes zoomIn{0%{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}::view-transition-new(boot--canvas):only-child,::view-transition-new(boot-safari--canvas):only-child{animation-name:slideFromRight;will-change:transform}::view-transition-old(boot--canvas):only-child,::view-transition-old(boot-safari--canvas):only-child{animation-name:slideToRight;will-change:transform}@keyframes slideFromRight{0%{transform:translateX(100vw)}to{transform:translateX(0)}}@keyframes slideToRight{0%{transform:translateX(0)}to{transform:translateX(100vw)}}::view-transition-new(boot--canvas-header):only-child{animation-name:slideHeaderFromTop;will-change:transform}::view-transition-old(boot--canvas-header):only-child{animation-name:slideHeaderToTop;will-change:transform}@keyframes slideHeaderFromTop{0%{transform:translateY(-100%)}}@keyframes slideHeaderToTop{to{transform:translateY(-100%)}}::view-transition-new(boot--canvas-sidebar):only-child{animation-name:slideSidebarFromRight;will-change:transform}::view-transition-old(boot--canvas-sidebar):only-child{animation-name:slideSidebarToRight;will-change:transform}@keyframes slideSidebarFromRight{0%{transform:translateX(100%)}}@keyframes slideSidebarToRight{to{transform:translateX(100%)}}"));
  document.head.appendChild(style);
}
export {
  init,
  initSinglePage,
  store
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
